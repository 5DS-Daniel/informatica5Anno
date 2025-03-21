<?php

session_start();
$path2root = "../";
require $path2root . "pages/config.php";

if (!isset($_SESSION['user'])) {
    header("Location:" . $path2root . "index.php");
    exit();
}

$username = $_SESSION['user'];

$query = $conn->prepare("SELECT id FROM users WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    die("Errore: Utente non trovato.");
}

$row = $result->fetch_assoc();
$user_id = $row["id"];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["immagine"])) {
    $prodname = $_POST["nome"];
    $proddesc = $_POST["descrizione"];
    $prodprice = floatval($_POST["prezzo"]);
    $prodphoto = $_FILES["immagine"]["name"];

    $errors = [];

    if (empty($prodname)) {
        $errors[] = "Inserisci un nome valido";
    }
    if ($prodprice < 0) {
        $errors[] = "Il prezzo deve essere maggiore di 0";
    }

    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($_FILES["immagine"]["type"], $allowed_types)) {
        $errors[] = "Formato immagine non valido.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO products (user_id, nome, descrizione, prezzo, immagine) VALUES (?, ?, ?, ?, '')");
        $stmt->bind_param("issd", $user_id, $prodname, $proddesc, $prodprice);
        $stmt->execute();
        $product_id = $stmt->insert_id;

        $user_folder = "uploads/" . $username;
        $products_folder = $user_folder . "/products";

        if (!file_exists($path2root . $user_folder)) {
            mkdir($path2root . $user_folder, 0777, true);
        }
        if (!file_exists($path2root . $products_folder)) {
            mkdir($path2root . $products_folder, 0777, true);
        }

        $image_extension = pathinfo($_FILES["immagine"]["name"], PATHINFO_EXTENSION);
        $image_name = $product_id . "." . $image_extension;
        $image_path = $products_folder . "/" . $image_name;

        if (move_uploaded_file($_FILES["immagine"]["tmp_name"], $path2root . $image_path)) {
            $update_stmt = $conn->prepare("UPDATE products SET immagine = ? WHERE id = ?");
            $update_stmt->bind_param("si", $image_path, $product_id);
            $update_stmt->execute();

            $success = "Prodotto caricato con successo!";
        } else {
            $errors[] = "Errore nel caricamento dell'immagine.";
        }
    }
}

include $path2root . '/components/navbar.php';
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiungi Prodotto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg p-4 text-center">
                    <form action="uploaditem.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Password attuale</label>
                            <input type="text" name="nome" placeholder="Nome del prodotto" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="descrizione" class="form-label">Password attuale</label>
                            <textarea name="descrizione" placeholder="Descrizione" required class="form-control"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="prezzo" class="form-label">Password attuale</label>
                            <input type="number" name="prezzo" step="0.01" placeholder="Prezzo" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="immagine" class="form-label">Password attuale</label>
                            <input type="file" name="immagine" class="form-control" required>
                        </div>
                        <button type="submit" name="add-item" class="btn w-100" style="background-color: #FFB22C;">Aggiungi prodotto</button>
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
