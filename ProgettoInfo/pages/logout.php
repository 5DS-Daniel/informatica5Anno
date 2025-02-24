<?php


session_start();

$path2root = "../";
session_unset();
session_destroy();
header( "Location:" . $path2root . "index.php");
?>