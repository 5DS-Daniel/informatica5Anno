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
                border: 1px solid;
            }
        </style>
    </head>
    <body>
        <?php
            if (!isset($error_message)) {
                if (isset($_POST['btnInserisci'])) {
                    $nome           = @mysqli_real_escape_string($db_conn, ucwords(strtolower(filtro_testo($_POST['txtNome']))));
                    $cognome        = @mysqli_real_escape_string($db_conn, ucwords(strtolower(filtro_testo($_POST['txtCognome']))));
                    $codice_fiscale = @mysqli_real_escape_string($db_conn, strtoupper(filtro_testo($_POST['txtCodiceFiscale'])));
                    $data_nascita   = @mysqli_real_escape_string($db_conn, filtro_testo($_POST['txtDataNascita'])); //date("Y-n-j") data attuale; date("2024-01-07") determinata data;
                    $ora_nascita    = @mysqli_real_escape_string($db_conn, filtro_testo($_POST['txtOraNascita']));  //date("H:i:s") ora attuale;  date("12:09:48")   determinata ora;

                    //I browser moderni invertono la data automaticamente usando type="date"
                    //$data_nascita   = str_replace("/", "-", $data_nascita);
                    //$timestamp      = strtotime($data_nascita);
                    //$data_nascita   = date("Y-m-d", $timestamp);

                    $query_insert = "INSERT INTO tcontatti (nome, cognome, codice_fiscale, data_nascita, ora_nascita) "
                                  . "VALUES('$nome', '$cognome', '$codice_fiscale', '$data_nascita', '$ora_nascita')";

                    try {
                        $insert = @mysqli_query($db_conn, $query_insert);

                        if ($insert)
                            $message = "Contatto inserito con successo!";
                        else
                            $message = "Contatto non inserito!";                                                

                        header("refresh:3; index.php");
                        //header("Location: index.php");
                    } catch (Exception $ex) {                        
                        $message = $ex->getMessage();
                        
                        //------------------------------------------------------
                        if (@mysqli_errno($db_conn) == 4025) // errore generato dai check
                            $message = "nome e/o cognome errati!";

                        if (@mysqli_errno($db_conn) == 1062) // errore generato dai campi unique
                            $message = "contatto già esistente!";

                        if (@mysqli_errno($db_conn) == 1644) // errore generato dai trigger
                            $message = @mysqli_error($db_conn);                        
                        //------------------------------------------------------
                        
                        header("refresh:3");
                    }
                    
                    echo $message;
                } else {
        ?>
                    <form name="frmContattiInserimento" method="post" action="<?=$_SERVER['PHP_SELF']?>">
                        <table>
                            <tr>
                                <td>Nome</td>
                                <td>
                                    <input type="text" name="txtNome">
                                </td>
                            </tr>
                            <tr>
                                <td>Cognome</td>
                                <td>
                                    <input type="text" name="txtCognome">
                                </td>
                            </tr>
                            <tr>
                                <td>Codice Fiscale</td>
                                <td>
                                    <input type="text" name="txtCodiceFiscale">
                                </td>
                            </tr>
                            <tr>
                                <td>Data di nascita</td>
                                <td>
                                    <input type="date" name="txtDataNascita">
                                </td>
                            </tr>
                            <tr>
                                <td>Ora di nascita</td>
                                <td>
                                    <input type="time" name="txtOraNascita">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" align="center">
                                    <input type="submit" name="btnInserisci" value="Inserisci">
                                    <input type="reset"  name="btnReset" value="Cancella">                                    
                                </td>
                            </tr>
                        </table>
                    </form>
                    <br>
                    <input type="button" value="Torna indietro" onClick="javascript:history.back()">
                    <button onClick="javascript:history.back()">Torna indietro</button>
                    <a href="index.php">Torna indietro</a>
        <?php
                }
            } else {
                echo $error_message;

                header("refresh:3; index.php");
            }
        ?>
    </body>
</html>