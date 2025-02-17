<?php
    const DB_HOST = 'localhost';
    const DB_USER = 'root';
    const DB_PASS = '';
    const DB_NAME = 'my_franchini';    

    try {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $db_conn = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);


    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
?>

