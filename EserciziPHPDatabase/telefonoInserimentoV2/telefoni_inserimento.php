

<!--
Implementare il file telefoni_inserimento.php dove:
la scelta dell'operatore tramite select dovrà essere ottenuta da una ricerca nella tabella toperatori, 
la scelta del contatto sempre tramite select dovrà essere ottenuta da una ricerca nella tabella tcontatti ordinati per cognome e nome in modo da poter associare la chiave primaria del contatto scelto alla chiave esterna del telefono che si sta inserendo.
-->

<?php
    include 'funzioni.php';
    include 'connessione.php';
    session_start();


//DOPO AVER EFFETTUATO LA CONNESSIONE, ANDIAMO A PRENDERE TUTTI GLI OPERATORI CHE SONO PRESENTI NELLA NOSTRA TABELLA 


    try {

        $sql_operatori = "SELECT id_operatori, nome FROM toperatori";
        $result_operatori = $db_conn->query($sql_operatori);

        $sql_contatti = "SELECT id_contatti, CONCAT(cognome, ' ', nome) AS nome_completo FROM tcontatti ORDER BY cognome, nome";
        //concat ci permette di unire due campi in un unico campo mettendo uno spazio tra i due e poi dando loro una specie 
        // di alias piu' facile da ricordare per noi

        //order by mette in ordine alfabetico crescente, se i cognomi sono uguali controlla i nomi

        $result_contatti = $db_conn->query($sql_contatti);



        $dangerContatto = $dangerNumero = $dangerOperatore = $dangerTipoNumero = '';
        $valido = true;
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }


    if (isset($_POST['btnInvio'])) {
        $contatto = trim($_POST['contatto']);
        $numero = trim($_POST['numero']);
        $operatore = trim($_POST['operatore']);
        $tipoNumero = isset($_POST['tipoNumero']) ? trim($_POST['tipoNumero']) : '';
        if (empty($contatto)) {
            $dangerContatto = "error";
            $valido = false;
        }
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
        } else {
            $_SESSION['insert'] = [
                "contatto" => isset($contatto) ? $contatto : '',
                "numero" => isset($numero) ? $numero : '',
                "operatore" => isset($operatore) ? $operatore : '',
                "tipoNumero" => isset($tipoNumero) ? $tipoNumero : ''
            ];
            try {            
                $query = "INSERT INTO ttelefoni (contatto_id, numero, operatore_id, tipo) VALUES ('$contatto', '$numero', '$operatore', '$tipoNumero')";
                $risultato = mysqli_query($db_conn, $query);
                if ($risultato) {
                    $message = "Inserimento avvenuto con successo";
                    $dangerContatto = $dangerNumero = $dangerOperatore = $dangerTipoNumero = '';
                    $contatto = $numero = $operatore = $tipoNumero = '';
                    header("refresh:3; telefoni_inserimento.php");
                } else {
                    
                }
            } catch (Exception $e) {
                $message = $e->getMessage();
                


                ////////////////////INUTILE PERCHE` NON VA UNIQUE////////////////////////
                //errori sui campi unique
                if (@mysqli_errno($db_conn) == 1062) { 
                    $message = @mysqli_error($db_conn);
                    if (strpos($message, 'numero') !== false) {
                        $message = "Contatto non inserito! Il numero è già registrato";
                        $dangerNumero = 'error';
                    }
                }
                //////////////////////////////////////////
                
                if (@mysqli_errno($db_conn) == 1644) {
                    $message = @mysqli_error($db_conn);
                    if (strpos($message, 'numero') !== false) {
                        $message = "Contatto non inserito! Il numero non è valido";
                        $dangerNome = 'error';
                    }

                    if (strpos($message, 'tipo_numero') !== false) {
                        $message = "Contatto non inserito! Il tipo del numero non è valido";
                        $dangerTipoNumero = 'error';
                    }

                    if (strpos($message, 'operatore') !== false) {
                        $message = "Contatto non inserito! L'operatore non è valido";
                        $dangerOperatore = 'error';
                    }

                    if (strpos($message, 'contatto') !== false) {
                        $message = "Contatto non inserito! Il contatto non è valido";
                        $dangerContatto = 'error';
                    }


                }
            }
        }
        echo $message;
        header("refresh:3; telefoni_inserimento.php");
        exit();
    }
    
    if (isset($_POST['btnReset'])) {
        session_unset();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserimento Telefono</title>
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
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" >
        <table>
            <tr>
                <td>
                    Contatto
                </td>
                <td class="<?= $dangerContatto ?>">
                    <select name="contatto" required>
                        <?php while($row = $result_contatti->fetch_assoc()) { 
                            $selected = (isset($_SESSION['insert']['contatto']) && $_SESSION['insert']['contatto'] == $row['id_contatti']) ? 'selected' : '';
                        ?>
                            <option value="<?= $row['id_contatti'] ?>" <?= $selected ?>><?= htmlspecialchars($row['nome_completo']) ?></option>
                        <?php } ?>
                    </select>
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
                    <input type="submit" name="btnInvio" value="Inserisci">
                    <input type="submit" name="btnReset" value="Cancella">
                </td>
            </tr>
        </table>
    </form>
    <a href="<?= $_SERVER['PHP_SELF'] ?>">Torna indietro</a>
    <br>
    <a href="contatti_inserimento.php">
        <button>Contatti</button>
    </a>



</body>
</html>