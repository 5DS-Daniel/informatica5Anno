<?php
    include 'connessione.php';
    include 'funzioni.php';
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <title>Contatti inserimento</title>
    <meta charset="UTF-8">
    <style>
        table {
            border-collapse: collapse;
        }
        td, th {
            border: 2px solid;
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

    if (isset($_POST['btnInserisci'])) {

        $nome = trim($_POST['txtNome']);
        $cognome = trim($_POST['txtCognome']);
        $codice_fiscale = trim($_POST['txtCodiceFiscale']);
        $matricola = trim($_POST['txtMatricola']);
        $data_nascita = trim($_POST['txtDataNascita']);
        $ora_nascita = trim($_POST['txtOraNascita']);
        $valid = true;
        
        if (empty($nome)) {
            $dangerNome = 'error';
            $valid = false;
        }
        if (empty($cognome)) {
            $dangerCognome = 'error';
            $valid = false;
        }
        if (empty($codice_fiscale)) {
            $dangerFiscale = 'error';
            $valid = false;
        }
        if (empty($matricola)) {
            $dangerMatricola = 'error';
            $valid = false;
        }
        if (empty($data_nascita)) {
            $dangerData = 'error';
            $valid = false;
        }
        if (empty($ora_nascita)) {
            $dangerOra = 'error';
            $valid = false;
        }

        if(!$valid){
            $message= "Tutti i campi sono obbligatori";
        }else{

            $nome           = @mysqli_real_escape_string($db_conn, ucwords(strtolower(filtro_testo($_POST['txtNome']))));
            $cognome        = @mysqli_real_escape_string($db_conn, ucwords(strtolower(filtro_testo($_POST['txtCognome']))));
            $codice_fiscale = @mysqli_real_escape_string($db_conn, strtoupper(filtro_testo($_POST['txtCodiceFiscale'])));
            $matricola      = @mysqli_real_escape_string($db_conn, strtoupper(filtro_testo($_POST['txtMatricola'])));
            $data_nascita   = @mysqli_real_escape_string($db_conn, filtro_testo($_POST['txtDataNascita']));
            $ora_nascita    = @mysqli_real_escape_string($db_conn, filtro_testo($_POST['txtOraNascita']));

            $query_insert = "INSERT INTO tcontatti (nome, cognome, codice_fiscale, matricola,data_nascita, ora_nascita) "
                            . "VALUES('$nome', '$cognome', '$codice_fiscale', '$matricola', '$data_nascita', '$ora_nascita')";

            try {
                $insert = mysqli_query($db_conn, $query_insert);

                if ($insert){
                    $message = "Contatto inserito con successo!";
                    $dangerNome = $dangerCognome = $dangerFiscale = $dangerMatricola = $dangerData = $dangerOra = '';
                    $nome = $cognome = $codice_fiscale = $matricola = $data_nascita = $ora_nascita = '';
                    header("refresh:1; contatti_inserimento.php");
                }                                
            } catch (Exception $ex) {                        
                $message = $ex->getMessage();

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
            }

            if (isset($_POST['btnReset'])) {
                $dangerNome = $dangerCognome = $dangerFiscale = $dangerMatricola = $dangerData = $dangerOra = '';
                $nome = $cognome = $codice_fiscale = $matricola = $data_nascita = $ora_nascita = '';
                $message = '';
            
            }

        }  
        echo $message;
    
}
?>

<form name="frmContattiInserimento" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
    <table>
        <tr>
            <td>Nome</td>
            <td class="<?= $dangerNome ?>">
                <input type="text" name="txtNome" value="<?= htmlspecialchars($nome) ?>">
            </td>
        </tr>
        <tr>
            <td>Cognome</td>
            <td class="<?= $dangerCognome ?>">
                <input type="text" name="txtCognome" value="<?= htmlspecialchars($cognome) ?>">
            </td>
        </tr>
        <tr>
            <td>Codice Fiscale</td>
            <td class="<?= $dangerFiscale ?>">
                <input type="text" name="txtCodiceFiscale" value="<?= htmlspecialchars($codice_fiscale) ?>">
            </td>
        </tr>
        <tr>
            <td>Codice Matricola</td>
            <td class="<?= $dangerMatricola ?>">
                <input type="text" name="txtMatricola" value="<?= htmlspecialchars($matricola) ?>">
            </td>
        </tr>
        <tr>
            <td>Data di nascita</td>
            <td class="<?= $dangerData ?>">
                <input type="date" name="txtDataNascita" value="<?= htmlspecialchars($data_nascita) ?>">
            </td>
        </tr>
        <tr>
            <td>Ora di nascita</td>
            <td class="<?= $dangerOra ?>">
                <input type="time" name="txtOraNascita" value="<?= htmlspecialchars($ora_nascita) ?>">
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center">
                <input type="submit" name="btnInserisci" value="Inserisci">
                <input type="submit" name="btnReset" value="Cancella">
            </td>
        </tr>
    </table>
</form>
<br>
<a href="./telefoni_inserimento.php">
    <button>Telefoni</button>
</a>
</body>
</html>
