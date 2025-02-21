

<?php
    include 'connessione.php';
    include 'funzioni.php';
    session_start();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Contatti e Telefoni</title>
    <style>
        table{
            margin: 0 auto;
            border-collapse: collapse;
        }
        td{
            border: 1px solid black;
            padding: 10px;
        }
        .error {
            border: 2px solid red;
            background-color: #ffe5e5;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<?php
    $dangerNome = $dangerCognome = $dangerFiscale = $dangerMatricola = $dangerData = $dangerOra = $dangerGruppo = '';
    $dangerNumero = $dangerOperatore = $dangerTipoNumero = '';
    $nome = $cognome = $codice_fiscale = $matricola = $data_nascita = $ora_nascita = '';
    $numeriInseriti = 0;
    $message = '';
    $ultimo_contatto = '';

    try {
        $sql_operatori = "SELECT id_operatori, nome FROM toperatori";
        $result_operatori = $db_conn->query($sql_operatori);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }

    if (isset($_POST['btnInserisci'])) {
        $nome           = @mysqli_real_escape_string($db_conn, ucwords(strtolower(filtro_testo($_POST['txtNome']))));
        $cognome        = @mysqli_real_escape_string($db_conn, ucwords(strtolower(filtro_testo($_POST['txtCognome']))));
        $codice_fiscale = @mysqli_real_escape_string($db_conn, strtoupper(filtro_testo($_POST['txtCodiceFiscale'])));
        $matricola      = @mysqli_real_escape_string($db_conn, strtoupper(filtro_testo($_POST['txtMatricola'])));
        $data_nascita   = @mysqli_real_escape_string($db_conn, filtro_testo($_POST['txtDataNascita']));
        $ora_nascita    = @mysqli_real_escape_string($db_conn, filtro_testo($_POST['txtOraNascita']));
        $valido = true;
        
        if (empty($nome)) {
            $dangerNome = "error";
            $valido = false;
        }
        if (empty($cognome)) {
            $dangerCognome = "error";
            $valido = false;
        }
        if (empty($codice_fiscale)) {
            $dangerFiscale = "error";
            $valido = false;
        }
        if (empty($matricola)) {
            $dangerMatricola = "error";
            $valido = false;
        }
        if (empty($data_nascita)) {
            $dangerData = "error";
            $valido = false;
        }
        if (empty($ora_nascita)) {
            $dangerOra = "error";
            $valido = false;
        }

        if ($valido) {
            $_SESSION['insert'] = [
                "nome" => isset($nome) ? $nome : '',
                "cognome" => isset($cognome) ? $cognome : '',
                "codice_fiscale" => isset($codice_fiscale) ? $codice_fiscale : '',
                "matricola" => isset($matricola) ? $matricola : '',
                "data_nascita" => isset($data_nascita) ? $data_nascita : '',
                "ora_nascita" => isset($ora_nascita) ? $ora_nascita : ''
            ];

            try {
                $query_insert = "INSERT INTO tcontatti (nome, cognome, codice_fiscale, matricola, data_nascita, ora_nascita) "
                . "VALUES ('$nome', '$cognome', '$codice_fiscale', '$matricola', '$data_nascita', '$ora_nascita')";
                $risultato = mysqli_query($db_conn, $query_insert);
                if ($risultato) {
                    $message = "Inserimento contatto avvenuto con successo";
                    $dangerNome = $dangerCognome = $dangerFiscale = $dangerMatricola = $dangerData = $dangerOra = '';
                    $nome = $cognome = $codice_fiscale = $matricola = $data_nascita = $ora_nascita = '';
                } else {
                    
                }
            } catch (Exception $e) {
                $message = $e->getMessage();
                if (@mysqli_errno($db_conn) == 1062) { 
                    $message = @mysqli_error($db_conn);
                    if (strpos($message, 'matricola') !== false) {
                        $message = "Contatto non inserito! La matricola è già registrata";
                        $dangerMatricola = 'error';
                    } elseif (strpos($message, 'codice_fiscale') !== false) {
                        $message = "Contatto non inserito! Il codice fiscale è già inserito";
                        $dangerFiscale = 'error';
                    }
                }

                if (@mysqli_errno($db_conn) == 1644) {
                    $message = @mysqli_error($db_conn);
                    if (strpos($message, 'entrambi') !== false) {
                        $message = "Contatto non inserito! Nome e cognome non validi";
                        $dangerCognome = 'error';
                        $dangerNome = 'error';
                    }else{
                        if (strpos($message, 'cognome') !== false) {
                            $message = "Contatto non inserito! Il cognome non è valido";
                            $dangerCognome = 'error';
                        }elseif (strpos($message, 'nome') !== false) {
                            $message = "Contatto non inserito! Il nome non è valido";
                            $dangerNome = 'error';
                        }
                    }
                    if (strpos($message, 'data') !== false) {
                        $message = "Contatto non inserito! Data di nascita non valida";
                        $dangerData = 'error';
                    }
                    if (strpos($message, 'codice_fiscale') !== false) {
                        $message = "Contatto non inserito! Formato codice fiscale sbagliato";
                        $dangerFiscale = 'error';
                    }
                    if (strpos($message, 'matricola') !== false) {
                        $message = "Contatto non inserito! Formato matricola sbagliato";
                        $dangerMatricola = 'error';
                    }
                }
                echo $message;
                header("refresh:3; telefono_completo.php");
                exit();
                
            }
            //Proviamo a recuperare l'ultimo contatto inserito
            try {
                $ultimo_contatto = mysqli_insert_id($db_conn);
                if ($ultimo_contatto) {
                     $ultimo_contatto = strval($ultimo_contatto); // Converte l'ID in stringa
                }
            } catch (Exception $e) {
                $error_message = $e->getMessage();
            }                   

            //se siamo qui, il contatto e' stato inserito con successo

            if (empty($_POST['gruppi']) || !is_array($_POST['gruppi'])) {
                $dangerGruppo = "error";
                $valido = false;
            }

            if ($valido) {
                try {
                    foreach ($_POST['gruppi'] as $gruppo_id) {
                        $gruppo_id = mysqli_real_escape_string($db_conn, $gruppo_id);
                        $query_gruppi = "INSERT INTO tcontatti_gruppi (contatto_id, gruppo_id) 
                                         VALUES ('$ultimo_contatto', '$gruppo_id')";
                        $risultato = mysqli_query($db_conn, $query_gruppi);

                        if (!$risultato) {
                            throw new Exception("Errore nell'inserimento: " . mysqli_error($db_conn));
                        }
                    }
                } catch (Exception $e) {
                    $message = $e->getMessage();
                    echo $message;
                    header("refresh:3; telefono_completo.php");
                    exit();
                }   
            }
    
            $numeri = $_POST['numeri'];
            $operatori = $_POST['operatori'];
            $tipi = $_POST['tipi'];

            if (empty($numeri)) {
                $dangerNumero = "error";
                $valido = false;
            }
            if (empty($operatori)) {
                $dangerOperatore = "error";
                $valido = false;
            }
            if (empty($tipi)) {
                $dangerTipoNumero = "error";
                $valido = false;
            }

            if(!$valido){
                $message= "Tutti i campi sono obbligatori";
                try {
                    $query = "DELETE FROM tcontatti WHERE id_contatti = '$ultimo_contatto'";
                    $risultato = mysqli_query($db_conn, $query);
                    echo $message;
                    header("refresh:3; telefono_completo.php");
                    exit();
                } catch (Exception $e) {
                    $error_message = $e->getMessage();
                }
            } else {

                $_SESSION['insert'] = [
                    "numeri" => isset($numeri) ? $numeri : '',
                    "operatori" => isset($operatori) ? $operatori : '',
                    "tipi" => isset($tipi) ? $tipi : ''
                ];


                try {            
                    for ($i = 0; $i < count($numeri); $i++) {
                        if (!empty($numeri[$i]) && !empty($tipi[$i])) {  
                            $numero = mysqli_real_escape_string($db_conn, $numeri[$i]);
                            $operatore = mysqli_real_escape_string($db_conn, $operatori[$i]);
                            $tipo = mysqli_real_escape_string($db_conn, $tipi[$i]);
                    
                            $query = "INSERT INTO ttelefoni (contatto_id, numero, operatore_id, tipo) 
                                      VALUES ('$ultimo_contatto', '$numero', '$operatore', '$tipo')";
                            $risultato = mysqli_query($db_conn, $query);
                
                        }
                    }
                    $dangerNumero = $dangerOperatore = $dangerTipoNumero = '';
                    $contatto = $numeri = $operatori = $tipi = '';
                    header("refresh:3; telefono_completo.php");
                } catch (Exception $e) {
                    $message = $e->getMessage();
                    
                    if (@mysqli_errno($db_conn) == 1644) {
                        $message = @mysqli_error($db_conn);
                        if (strpos($message, 'numero') !== false) {
                            $message = "Contatto non inserito! Il numero non è valido";
                            $dangerNome = 'error';
                            try {
                                $query = "DELETE FROM tcontatti WHERE id_contatti = '$ultimo_contatto'";
                                $risultato = mysqli_query($db_conn, $query);
                                echo $message;
                                header("refresh:3; telefono_completo.php");
                                exit();
                            } catch (Exception $e) {
                                $error_message = $e->getMessage();
                            }
                        }
                    }
                    if (@mysqli_errno($db_conn) == 1644) {
                        $message = @mysqli_error($db_conn);
                        if (strpos($message, 'operatore_inesistente') !== false) {
                            $message = "Contatto non inserito! Operatore non valido";
                            $dangerNome = 'error';
                            try {
                                $query = "DELETE FROM tcontatti WHERE id_contatti = '$ultimo_contatto'";
                                $risultato = mysqli_query($db_conn, $query);
                                echo $message;
                                header("refresh:3; telefono_completo.php");
                                exit();
                            } catch (Exception $e) {
                                $error_message = $e->getMessage();
                            }
                        }
                    }

                    if (@mysqli_errno($db_conn) == 1265) {
                        $message = "Contatto non inserito! Tipo di numero non valido";
                        $dangerNome = 'error';
                        try {
                            $query = "DELETE FROM tcontatti WHERE id_contatti = '$ultimo_contatto'";
                            $risultato = mysqli_query($db_conn, $query);
                            echo $message;
                            header("refresh:3; telefono_completo.php");
                            exit();
                        } catch (Exception $e) {
                            $error_message = $e->getMessage();
                        }    
                        
                    }


                }
            }
        } else {
            $message = "Tutti i campi sono obbligatori";
        }
        echo $message;
    }
    if (isset($_POST['btnReset'])) {
        session_unset();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
?>
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" >
        <table>
            <tr>
                <td>Nome</td>
                <td class="<?= $dangerNome ?>">
                    <input type="text" name="txtNome" value="<?= $_SESSION['insert']['nome'] ??''?>">
                </td>
            </tr>
            <tr>
                <td>Cognome</td>
                <td class="<?= $dangerCognome ?>">
                    <input type="text" name="txtCognome" value=" <?= $_SESSION['insert']['cognome'] ?? ''?>">
                </td>
            </tr>
            <tr>
                <td>Codice Fiscale</td>
                <td class="<?= $dangerFiscale ?>">
                    <input type="text" name="txtCodiceFiscale" value="<?= $codice_fiscale ?? '' ?>">
                </td>
            </tr>
            <tr>
                <td>Codice Matricola</td>
                <td class="<?= $dangerMatricola ?>">
                    <input type="text" name="txtMatricola" value="<?= $matricola ?? ''?>">
                </td>
            </tr>
            <tr>
                <td>Data di nascita</td>
                <td class="<?= $dangerData ?>">
                    <input type="date" name="txtDataNascita" value="<?= $_SESSION['insert']['data_nascita'] ?? ''?>">
                </td>
            </tr>
            <tr>
                <td>Ora di nascita</td>
                <td class="<?= $dangerOra ?>">
                    <input type="time" name="txtOraNascita" value="<?= $_SESSION['insert']['ora_nascita'] ?? ''?>">
                </td>
            </tr>

            <tr>
                <td>Gruppi</td>
                <td class="<?= $dangerGruppo ?>">
                    <?php
                    try {
                        $sql_gruppi = "SELECT id_gruppo, nome FROM tgruppi";
                        $result_gruppi = $db_conn->query($sql_gruppi);

                        while ($row = $result_gruppi->fetch_assoc()) {
                            echo "<input type='checkbox' name='gruppi[]' value='{$row['id_gruppo']}'> " . htmlspecialchars($row['nome']) . "<br>";
                        }
                    } catch (Exception $e) {
                        echo "Errore nel recupero dei gruppi: " . $e->getMessage();
                    }
                    ?>
                </td>

            </tr>
            <tr>
                <td>Numeri di telefono</td>
                <td id="phoneContainer">
                    <div class="phoneEntry">
                        <input type="text" name="numeri[]" required minlength="10" maxlength="10" placeholder="Numero">
                        <select name="operatori[]" required>
                            <?php while ($row = $result_operatori->fetch_assoc()) { ?>
                                <option value="<?= $row['id_operatori'] ?>"><?= htmlspecialchars($row['nome']) ?></option>
                            <?php } ?>
                        </select>
                        <input type="radio" name="tipi[0]" value="Personale" required> Personale
                        <input type="radio" name="tipi[0]" value="Casa"> Casa
                        <input type="radio" name="tipi[0]" value="Lavoro"> Lavoro
                        <span class="removeButton" onclick="removePhone(this)">❌</span>
                    </div>
                </td>
            </tr>

            <tr>
                <td colspan="2" style="text-align: center;">
                    <button type="button" onclick="addPhone()">Aggiungi Numero</button>
                </td>
            </tr>

            <tr>
                <td colspan="2" style="text-align: center;">
                    <input type="submit" name="btnInserisci" value="Inserisci">
                    <input type="submit" name="btnReset" value="Cancella">
                </td>
            </tr>
        </table>
    </form>
    <script>
        let phoneIndex = 1;

        function addPhone() {
            let container = document.getElementById("phoneContainer");
            let newEntry = document.createElement("div");
            newEntry.classList.add("phoneEntry");
            newEntry.innerHTML = `
                <input type="text" name="numeri[]" required minlength="10" maxlength="10" placeholder="Numero">
                <select name="operatori[]" required>
                    <?php
                    $result_operatori->data_seek(0);
                    while ($row = $result_operatori->fetch_assoc()) {
                        echo "<option value='{$row['id_operatori']}'>" . htmlspecialchars($row['nome']) . "</option>";
                    }
                    ?>
                </select>
                <input type="radio" name="tipi[${phoneIndex}]" value="Personale" required> Personale
                <input type="radio" name="tipi[${phoneIndex}]" value="Casa"> Casa
                <input type="radio" name="tipi[${phoneIndex}]" value="Lavoro"> Lavoro
                <span class="removeButton" onclick="removePhone(this)">❌</span>
            `;
            container.appendChild(newEntry);
            phoneIndex++;
            <?php $numeriInseriti++; ?>
        }

        function removePhone(element) {
            let parent = element.parentNode;
            parent.remove();
        }
    </script>
<a href="<?= $_SERVER['PHP_SELF'] ?>">Torna indietro</a>
<br>

</body>
</html>


