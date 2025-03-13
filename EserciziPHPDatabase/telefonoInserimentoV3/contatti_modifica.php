<?php
    include 'connessione.php';
    include 'funzioni.php';
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Contatti modifica</title>
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
                if (isset($_POST['btnModifica'])) {
                    $id             = $_GET['id'];

                    $nome           = @mysqli_real_escape_string($db_conn, ucwords(strtolower(filtro_testo($_POST['txtNome']))));
                    $cognome        = @mysqli_real_escape_string($db_conn, ucwords(strtolower(filtro_testo($_POST['txtCognome']))));
                    $codice_fiscale = @mysqli_real_escape_string($db_conn, strtoupper(filtro_testo($_POST['txtCodiceFiscale'])));
                    $data_nascita   = @mysqli_real_escape_string($db_conn, filtro_testo($_POST['txtDataNascita']));
                    $ora_nascita    = @mysqli_real_escape_string($db_conn, filtro_testo($_POST['txtOraNascita']));

                    $query_update = "UPDATE tcontatti SET "
                                  . "nome              = '$nome', "
                                  . "cognome           = '$cognome', "
                                  . "codice_fiscale    = '$codice_fiscale', "
                                  . "data_nascita      = '$data_nascita', "
                                  . "ora_nascita       = '$ora_nascita' "
                                  . "WHERE id_contatti = $id";

                    try {
                        $update = @mysqli_query($db_conn, $query_update);

                        if ($update != null)
                            $message = "Contatto modificato con successo!";
                        else
                            $message = "Contatto non modificato!";                                              

                        header("refresh:3; index.php");                       
                    } catch (Exception $ex) {                        
                        $message = $ex->getMessage();
                        
                        if (@mysqli_errno($db_conn) == 4025)
                            $message = "nome e/o cognome errati!";

                        if (@mysqli_errno($db_conn) == 1644)
                            $message = @mysqli_error($db_conn);                        

                        header("refresh:3");
                    }
                    
                    echo $message;
                } else {
                    $id = $_GET['id'];

                    $query_select = "SELECT id_contatti, nome, cognome, codice_fiscale, data_nascita, ora_nascita from tcontatti where id_contatti=$id";

                    $resulset = @mysqli_query($db_conn, $query_select);

                    if(@mysqli_num_rows($resulset)!=0) {
                        while($row = mysqli_fetch_assoc($resulset)) {
                            $id_contatti    = $row["id_contatti"];
                            $nome           = $row["nome"];
                            $cognome        = $row["cognome"];
                            $codice_fiscale = $row["codice_fiscale"];
                            $data_nascita   = $row["data_nascita"];
                            $ora_nascita    = $row["ora_nascita"];
                        }
        ?>
                        <form name="frmContattiModifica" action="<?=$_SERVER['PHP_SELF']?>?id=<?=$id_contatti?>" method="post">
                            <table>
                                <tr>
                                    <td>Nome</td>
                                    <td>
                                        <input type="text" name="txtNome" value="<?=$nome?>">
                                    </td>
                                <tr>
                                    <td>Cognome</td>
                                    <td>
                                        <input type="text" name="txtCognome" value="<?=$cognome?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Codice Fiscale</td>
                                    <td>
                                        <input type="text" name="txtCodiceFiscale" value="<?=$codice_fiscale?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Data di nascita</td>
                                    <td>
                                        <input type="date" name="txtDataNascita" value="<?=$data_nascita?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Ora di nascita</td>
                                    <td>
                                        <input type="time" name="txtOraNascita" value="<?=$ora_nascita?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="center">
                                        <input type="submit" name="btnModifica" value="Modifica">
                                        <input type="reset"  name="btnReset" value="Cancella"> 
                                    </td>
                                </tr>
                            </table>
                        </form>
                        <br>
                        <a href="index.php">Torna indietro</a>
        <?php
                    } else {
                        echo "Contatto inesistente!";

                        header("refresh:3");
                    }
                }
            } else {
                echo $error_message;

                header("refresh:3; index.php");
            }
        ?>
    </body>
</html>