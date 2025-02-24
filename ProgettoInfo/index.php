
<?php

session_start();
$path2root = "";
include 'components/navbar.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>CapitoloDue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">

</head>


<body>
    
<?php if (isset($_SESSION['user'])): ?>
    <h2>Ciao <?= $_SESSION['user'] ?>, benvenuto!</h2>
<?php else: ?>
    <h2>Benvenuto, visita il nostro sito!</h2>
<?php endif; ?>

</body>


</html>