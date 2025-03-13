<?php
    include 'connessione.php';
    include 'funzioni.php';
    
    $con = 0;
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Contatti ricerca</title>
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
                if (isset($_POST['btnRicerca'])){
                    $ricerca = @mysqli_real_escape_string($db_conn, filtro_testo($_POST['txtRicerca']));

                    $query    = "SELECT nome, cognome, codice_fiscale, data_nascita, ora_nascita FROM tcontatti WHERE nome LIKE '%$ricerca%' OR cognome LIKE '%$ricerca%'";

                    $resulset = @mysqli_query($db_conn, $query);

                    if(@mysqli_num_rows($resulset)!=0) {
        ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nome</th>
                                    <th>Cognome</th>
                                    <th>Codice Fiscale</th>
                                    <th>Data di nascita</th>
                                    <th>Ora di nascita</th>
                                </tr>
                            </thead>
                            <tbody>
        <?php
                                while($row = @mysqli_fetch_array($resulset, MYSQLI_ASSOC)) {
                                    $con += 1;                                    
                                    $nome           = $row["nome"];
                                    $cognome        = $row["cognome"];
                                    $codice_fiscale = $row["codice_fiscale"];
                                    $data_nascita   = $row["data_nascita"];
                                    $ora_nascita    = $row["ora_nascita"];

                                    $timestamp        = strtotime($data_nascita);
                                    $data_nascita_ita = date("d/m/Y", $timestamp);                                   
        ?>
                                    <tr>
                                        <th><?= $con              ?></th>
                                        <td><?= $nome             ?></td>
                                        <td><?= $cognome          ?></td>
                                        <td><?= $codice_fiscale   ?></td>
                                        <td><?= $data_nascita_ita ?></td>
                                        <td><?= $ora_nascita      ?></td>
                                    </tr>
        <?php
                                }
        ?>
                            </tbody>
                        </table>
        <?php
                    } else {
                        $message = "Nessun contatto presente!";

                        echo $message;

                        header("refresh:3; index.php");
                    }
                } else {
        ?>
                    <form name="frmRicerca" action="<?=$_SERVER['PHP_SELF']?>" method="post">
                        <table>
                            <tr>
                                <td>Ricerca</td>
                                <td>
                                    <input type="text" name="txtRicerca">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" align="center">
                                    <input type="submit" name="btnRicerca" value="Ricerca">
                                    <input type="button" name="btnAnnulla" value="Annulla" onClick="javascript:history.back()">
                                </td>
                            </tr>
                        </table>
                    </form>
        <?php
                }
        ?>
                <br>
                <a href="index.php">Torna indietro</a>
        <?php
            } else {
                echo $error_message;

                header("refresh:3; index.php");
            }
        ?>
    </body>
</html>