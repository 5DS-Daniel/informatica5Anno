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
    $dangerNome = $dangerCognome = $dangerFiscale = $dangerMatricola = $dangerData = $dangerOra = '';
    $nome = $cognome = $codice_fiscale = $matricola = $data_nascita = $ora_nascita = '';
    $message = '';


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
        $valid = true;
        
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


        if ($valid) {
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


            //se siamo qui, il contatto e' stato inserito con successo


            //Proviamo a recuperare l'ultimo contatto inserito
            try {
                $query = "SELECT id_contatti FROM tcontatti ORDER BY id_contatti DESC LIMIT 1";
                $risultato = mysqli_query($db_conn, $query);
            
                if ($risultato && $row = mysqli_fetch_assoc($risultato)) {
                    $ultimo_contatto = strval($row['id_contatti']); // Converte l'ID in stringa
                }
            } catch (Exception $e) {
                $error_message = $e->getMessage();
            }                


            $dangerNumero = $dangerOperatore = $dangerTipoNumero = '';
            $valido = true;
            $numero = trim($_POST['numero']);
            $operatore = isset($_POST['operatore']) ? trim($_POST['oepratore']) : '';
            $tipoNumero = isset($_POST['tipoNumero']) ? trim($_POST['tipoNumero']) : '';

            if (empty($numero)) {
                $dangerNumero = "error";
                $valido = false;
            }
            if (empty($operatore)) {
                $dangerOperatore = "error";
                $valido = false;
            }
            if (empty($tipoNumero)) {
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
                    "numero" => isset($numero) ? $numero : '',
                    "operatore" => isset($operatore) ? $operatore : '',
                    "tipoNumero" => isset($tipoNumero) ? $tipoNumero : ''
                ];
                try {            
                    $query = "INSERT INTO ttelefoni (contatto_id, numero, operatore_id, tipo) VALUES ('$ultimo_contatto', '$numero', '$operatore', '$tipoNumero')";
                    $risultato = mysqli_query($db_conn, $query);
                    if ($risultato) {
                        $message = $message . " - Inserimento numero avvenuto con successo";
                        $dangerNumero = $dangerOperatore = $dangerTipoNumero = '';
                        $contatto = $numero = $operatore = $tipoNumero = '';
                        header("refresh:3; telefono_completo.php");
                    }
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
                <td>
                    Numero di telefono
                </td>
                <td class="<?= $dangerNumero ?>">
                    <input type="text" name="numero" value="<?= $_SESSION['insert']['numero'] ?? '' ?>" required minlength="10" maxlength="10">
                </td>
            </tr>

            <tr>
                <td>Operatore</td>
                <td class="<?= $dangerOperatore ?>">
                    <select name="operatore" required>
                        <?php while($row = $result_operatori->fetch_assoc()) { 
                            $selected = (isset($_SESSION['insert']['operatore']) && $_SESSION['insert']['operatore'] == $row['id_operatori']) ? 'selected' : '';
                        ?>
                            <option value="<?= $row['id_operatori'] ?>" <?= $selected ?>><?= htmlspecialchars($row['nome']) ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Tipo</td>
                <td class="<?= $dangerTipoNumero ?>">
                    <input type="radio" id="Personale" name="tipoNumero" value="Personale" <?= (isset($_SESSION['insert']['tipoNumero']) && $_SESSION['insert']['tipoNumero'] == 'Personale') ? 'checked' : '' ?>>
                    <label for="personale">Personale</label><br>
                    <input type="radio" id="Casa" name="tipoNumero" value="Casa" <?= (isset($_SESSION['insert']['tipoNumero']) && $_SESSION['insert']['tipoNumero'] == 'Casa') ? 'checked' : '' ?>>
                    <label for="casa">Casa</label><br>
                    <input type="radio" id="Lavoro" name="tipoNumero" value="Lavoro" <?= (isset($_SESSION['insert']['tipoNumero']) && $_SESSION['insert']['tipoNumero'] == 'Lavoro') ? 'checked' : '' ?>>
                    <label for="lavoro">Lavoro</label>
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
    <a href="<?= $_SERVER['PHP_SELF'] ?>">Torna indietro</a>
    <br>

</body>
</html>
