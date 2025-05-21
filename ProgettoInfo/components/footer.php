<!DOCTYPE html>
<html lang="it">
<head>
    <title>Pagina di esempio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .footer {
            margin-top: auto;
            background: #222;
            color: #fff;
            padding: 2rem 0 1rem 0;
        }
        .footer a {
            color: #FFB22C;
            text-decoration: none;
            margin: 0 8px;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
    </div>
    <footer class="footer text-center">
        <div>
            <a href="<?= $path2root ?? './' ?>index.php" class="fw-bold">ProgettoInfo</a>
        </div>
        <div class="mb-2">
            <small>
                &copy; <?= date('Y') ?> ProgettoInfo &middot; Tutti i diritti riservati
            </small>
        </div>
        <div>
            <a href="<?= $path2root ?? './' ?>pages/account.php">Account</a>
            <a href="<?= $path2root ?? './' ?>pages/cart.php">Carrello</a>
            <a href="<?= $path2root ?? './' ?>pages/about.php">Chi siamo</a>
        </div>
    </footer>
</body>
</html>