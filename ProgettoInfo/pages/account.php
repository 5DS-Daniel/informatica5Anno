
<?php

session_start();
$path2root = "../";
require $path2root . "pages/config.php"; // Connessione al DB

if (!isset($_SESSION['user'])) {
    header("Location:" . $path2root . "index.php");
    exit();
}

$username = $_SESSION['user'];

$stmt = $conn->prepare("SELECT id, email, password, profile_pic FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();

if (!$userData) {
    die("Errore: utente non trovato.");
}

$email = $userData['email'];
$hashed_password = $userData['password'];
$profile_pic = !empty($userData['profile_pic']) ? $userData['profile_pic'] : "default.png";


//PRODOTTI VENDUTI DALL'UTENTE
$stmt = $conn->prepare("SELECT id, nome, descrizione, prezzo, immagine FROM products WHERE user_id = ?");
$stmt->bind_param("i", $userData['id']);
$stmt->execute();
$productsResult = $stmt->get_result();
$products = [];
while ($product = $productsResult->fetch_assoc()) {
    $products[] = $product;
}
$stmt->close();

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["upload_profile_pic"])) {
    if (!empty($_FILES["profile_pic"]["name"])) {
        $user_folder = $path2root . "uploads/users/" . $username;
        
        if (!file_exists($user_folder)) {
            if (!mkdir($user_folder, 0777, true)) {
                $errors[] = "Errore nella creazione della cartella per l'utente.";
            }
        }

        $file_name = basename($_FILES["profile_pic"]["name"]);
        $imageFileType = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $new_file_name = $username . "." . $imageFileType; // Usa l'username e l'estensione del file

        $target_file = $user_folder . "/" . $new_file_name;

        $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
        if ($check === false) {
            $errors[] = "Il file non è un'immagine valida.";
        }

        if ($_FILES["profile_pic"]["size"] > 2 * 1024 * 1024) {
            $errors[] = "Il file è troppo grande. Massimo 2MB.";
        }

        $allowed_formats = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $allowed_formats)) {
            $errors[] = "Formato non supportato. Usa JPG, JPEG, PNG o GIF.";
        }

        if (empty($errors)) {
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE username = ?");
                $stmt->bind_param("ss", $new_file_name, $username);
                $stmt->execute();
                $profile_pic = $new_file_name;
                $success = "Immagine del profilo aggiornata con successo!";
            } else {
                $errors[] = "Errore nel caricamento del file.";
            }
        }
    } else {
        $errors[] = "Seleziona un'immagine da caricare.";
    }
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
        <div class="col-md-8">
            <div class="card shadow-lg p-4 text-center">
                
                <div class="mb-3">
                    <img src="../uploads/users/<?php echo htmlspecialchars($username) . "/" . htmlspecialchars($profile_pic); ?>" alt="Immagine profilo" class="rounded-circle border" width="150" height="150">
                </div>

                <form action="account.php" method="POST" enctype="multipart/form-data">
                    <input type="file" name="profile_pic" class="form-control mb-2">
                    <button type="submit" name="upload_profile_pic" class="btn btn-secondary">Cambia immagine</button>
                </form>

                <h2 class="text-center mb-4 mt-4">Modifica il tuo account</h2>

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

                <form action="account.php" method="POST">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Password attuale</label>
                        <input type="password" name="current_password" id="current_password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Nome utente</label>
                        <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nuova Password (lascia vuoto per non cambiarla)</label>
                        <input type="password" name="new_password" id="new_password" class="form-control">
                    </div>

                    <button type="submit" name="update_account" style="background-color: #FFB22C" class="btn  w-100">Aggiorna</button>
                </form>

                <hr class="my-4">
                <h3 class="mb-4">I tuoi prodotti in vendita</h3>

                <?php if (count($products) > 0): ?>
                    <div class="row">
                        <?php foreach ($products as $product): ?>
                            <div class="col-md-5 mb-3">
                                <div class="card">
                                    <img src="<?php echo htmlspecialchars($path2root . $product['immagine']); ?>" class="card-img-top" alt="Immagine prodotto" style="height: 150px; object-fit: cover;">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($product['nome']); ?></h5>
                                        <p class="card-text"><?php echo htmlspecialchars($product['descrizione']); ?></p>
                                        <p class="card-text"><strong>€<?php echo number_format($product['prezzo'], 2, ',', '.'); ?></strong></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>Non hai ancora caricato nessun prodotto.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
