<?php

session_start();
$path2root = "../";
require $path2root . "pages/config.php";

if (!isset($_SESSION['user'])) {
    header("Location:" . $path2root . "index.php");
    exit();
}

$username = $_SESSION['user'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prodphoto = $_FILES["immagine-prodotto"]["name"];
    $prodname = $_POST["nome-prodotto"];
    $proddesc = $_POST["descrizione-prodotto"];
    $prodprice = $_POST["prezzo-prodotto"];
    
    if (empty($prodname)) {
        $errors[] = "Inserisci un nome valido";
    }
    if ($prodprice < 0) { 
        $errors[] = "Il prezzo deve essere maggiore di 0";
    }

    


    $success = "Prodotto caricato  con successo!";
}




include $path2root . '/components/navbar.php';
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg p-4 text-center">


                    <form action="uploaditem.php" method="POST" enctype="multipart/form-data">
                        <input type="file" name="immagine-prodotto" class="form-control mb-2">
                        <div class="mb-3">
                            <label for="nome-prodotto" class="form-label">Nome prodotto</label>
                            <input type="text" name="nome-prodotto" id="nome-prodotto" class="form-control" required>
                        </div>

                        <div class="mb-3">
                                <label for="descrizione-prodotto" class="form-label">Descrizione prodotto</label>
                                <textarea class="form-control" name="descrizione-prodotto" id="descrizione-prodotto" rows="10" required style="resize: none"></textarea>
                        </div>

                        <div class="mb-3">
                                <label for="prezzo-prodotto" class="form-label">Prezzo prodotto</label>
                                <input type="number" name="prezzo-prodotto" id="prezzo-prodotto" class="form-control" required>
                        </div>
                        <button type="submit" name="add-item" style="background-color: #FFB22C;" class="btn w-100">Aggiungi prodotto</button>
                    </form>



                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>            
    </div>
    

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
