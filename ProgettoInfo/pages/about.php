<?php
session_start();
$path2root = "../";
require $path2root . "pages/config.php";
include $path2root . "components/navbar.php";
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <title>CapitoloDue - Chi siamo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>

    </style>
</head>

<body class="bg-light">
    <main>
        <div class="container mt-5">
            <h2 class="mb-4 text-center">Chi siamo</h2>
            <div class="card shadow-sm p-4">
                <p>
                    Olibrary nasce da un’idea semplice: dare una seconda vita ai libri e metterli nelle mani di chi li saprà apprezzare.<br>
                    Siamo un piccolo progetto indipendente con una grande passione per la lettura, la condivisione e la sostenibilità. Su Olibrary puoi vendere e acquistare libri usati in modo facile, sicuro e conveniente.
                </p>
                <p>
                    Crediamo che ogni libro abbia una storia da raccontare — non solo quella scritta tra le sue pagine, ma anche quella vissuta da chi lo ha letto. Vogliamo che questi racconti continuino a circolare, abbattendo gli sprechi e favorendo una cultura accessibile a tutti.
                </p>
                <p>
                    Che tu sia uno studente, un lettore appassionato o semplicemente alla ricerca di un buon affare, Olibrary è il posto giusto per te.
                </p>
            </div>
        </div>
    </main>
    <?php include $path2root . 'components/footer.php'; ?>
</body>
</html>
