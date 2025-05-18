<?php
session_start();


if (!isset($_SESSION['user'])) {
    header("Location:" . $path2root . "index.php");
    exit();
}

$username = $_SESSION['user'];
$path2root = "../";
include $path2root.'/components/navbar.php';
require $path2root.'/pages/config.php';

?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <title>Il tuo carrello</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center mb-4">🛒 Il tuo carrello</h2>

        <?php if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])): ?>
            <div class="alert alert-warning text-center" role="alert">
                Il tuo carrello è vuoto.
            </div>
        <?php else: ?>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <ul class="list-group shadow-sm">
                        <?php foreach ($_SESSION['cart'] as $item): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= htmlspecialchars($item['name']) ?>
                                <span class="badge bg-primary rounded-pill">x<?= (int) $item['quantity'] ?></span>
                            </li>

                        <?php endforeach; ?>
                    </ul>
                    <div class="mt-4 d-flex justify-content-center gap-3">
                        <a href="checkout.php" class="btn btn-success">Procedi al checkout</a>
                        <a href="<?= $path2root .'index.php'?>" class="btn btn-secondary">Torna allo shop</a>
                    </div>

                </div>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>
