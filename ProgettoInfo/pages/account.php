<?php
session_start();
$path2root = "../";
require $path2root . "pages/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location:" . $path2root . "index.php");
    exit();
}

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id, username, email, password, profile_pic FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();

if (!$userData) {
    session_destroy();
    header("Location: " . $path2root . "login.php");
    exit();
}

if (!empty($userData['profile_pic'])) {
    $profile_pic = $userData['profile_pic'];
} else {
    $profile_pic = "default.png";
}

$username = $userData['username'];
$email = $userData['email'];
$hashed_password = $userData['password'];


//andiamo a recueprare i prodtotti che lk'utente sta vendendo. stile vinted
$stmt = $conn->prepare("SELECT id, nome, descrizione, prezzo FROM products WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$productsResult = $stmt->get_result();
$products = [];
while ($product = $productsResult->fetch_assoc()) {
    $img_stmt = $conn->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
    $img_stmt->bind_param("i", $product['id']);
    $img_stmt->execute();
    $img_result = $img_stmt->get_result();
    $images = [];
    while ($img = $img_result->fetch_assoc()) {
        $images[] = $img['image_path'];
    }
    $img_stmt->close();
    $product['images'] = $images;
    $products[] = $product;
}
$stmt->close();

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["upload_profile_pic"])) {
    if (isset($_FILES['profile_pic'])) {
        $file = $_FILES['profile_pic'];

        if ($file['error'] === UPLOAD_ERR_OK) {
            $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
            $maxFileSize = 2 * 1024 * 1024; // 2MB

            $fileName = $file['name'];
            $fileTmp = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Controllo estensione
            if (!in_array($fileExt, $allowedExt)) {
                $errors[] = "Formato file non consentito. Usa solo JPG, PNG o GIF.";
            }

            //aggiunta di un controllo per evitare fi far caricare file 
            //di dimensioni troppo grandi
            if ($fileSize > $maxFileSize) {
                $errors[] = "File troppo grande. Max 2MB.";
            }


            if (empty($errors)) {
                $uploadDir = $path2root . "uploads/users/" . $userId;
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Usa sempre nome uguale per sovrascrivere immagine vecchia
                $newFileName = $userId . "." . $fileExt;
                $destination = $uploadDir . "/" . $newFileName;

                if (move_uploaded_file($fileTmp, $destination)) {
                    // Aggiorna nome immagine nel DB
                    $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
                    $stmt->bind_param("si", $newFileName, $userId);
                    $stmt->execute();
                    $stmt->close();

                    $success = "Immagine profilo aggiornata con successo.";
                    $profile_pic = $newFileName;
                } else {
                    $errors[] = "Errore nel caricamento del file.";
                }
            }
        } else {
            $errors[] = "Nessun file caricato o errore nell'upload.";
        }
    } else {
        $errors[] = "Nessun file selezionato.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_account"])) {
    $newUsername = trim($_POST["username"]);
    $newEmail = trim($_POST["email"]);
    $currentPassword = $_POST["current_password"];
    $newPassword = $_POST["new_password"];

    if (!password_verify($currentPassword, $hashed_password)) {
        $errors[] = "La password attuale non è corretta.";
    }

    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email inserita non è valida.";
    }

    if (empty($errors)) {
        //Hash della nuova password se è stata inserita, altrimenti mantieni quella vecchia DUH
        if (!empty($newPassword)) {
            $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        } else {
            $newHashedPassword = $hashed_password;
        }

        //aggiorniamo i dati
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
        $stmt->bind_param("sssi", $newUsername, $newEmail, $newHashedPassword, $userId);

        if ($stmt->execute()) {
            $_SESSION['user'] = $newUsername;
            $username = $newUsername;
            $email = $newEmail;
            $hashed_password = $newHashedPassword;

            header("Location: account.php");
            exit();
        } else {
            $errors[] = "Errore nell'aggiornamento dei dati.";
        }
        $stmt->close();
    }
}

include $path2root . '/components/navbar.php';
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Modifica Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg p-4 text-center">

                <div class="mb-3">
                    <img src="../uploads/users/<?php echo htmlspecialchars($userId) . "/" . htmlspecialchars($profile_pic); ?>" alt="Immagine profilo" class="rounded-circle border" width="150" height="150" />
                </div>

                <!-- FORM UPLOAD IMMAGINE -->
                <form action="account.php" method="POST" enctype="multipart/form-data" class="mb-4">
                    <input type="file" name="profile_pic" class="form-control mb-2" required />
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

                <!-- FORM MODIFICA ACCOUNT -->
                <form action="account.php" method="POST">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Password attuale</label>
                        <input type="password" name="current_password" id="current_password" class="form-control" required />
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Nome utente</label>
                        <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required />
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required />
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nuova Password (lascia vuoto per non cambiarla)</label>
                        <input type="password" name="new_password" id="new_password" class="form-control" />
                    </div>

                    <button type="submit" name="update_account" style="background-color: #FFB22C" class="btn w-100">Aggiorna</button>
                </form>

                <hr class="my-4" />
                <h3 class="mb-4">I tuoi prodotti in vendita</h3>

                <?php if (count($products) > 0): ?>
                    <div class="row">
                        <?php foreach ($products as $product): ?>
                            <div class="col-md-5 mb-3">
                                <div class="card">
                                    <?php if (!empty($product['images'])): ?>
                                        <div id="carousel-<?php echo $product['id']; ?>" class="carousel slide" data-bs-ride="carousel">
                                            <div class="carousel-inner">
                                                <?php foreach ($product['images'] as $idx => $img): ?>
                                                    <div class="carousel-item <?php if ($idx === 0) echo 'active'; ?>">
                                                        <img src="<?php echo htmlspecialchars($path2root . $img); ?>" class="card-img-top" alt="Immagine prodotto" style="height: 150px; object-fit: cover;" />
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <?php if (count($product['images']) > 1): ?>
                                                <button class="carousel-control-prev" type="button" data-bs-target="#carousel-<?php echo $product['id']; ?>" data-bs-slide="prev">
                                                    <span class="carousel-control-prev-icon"></span>
                                                </button>
                                                <button class="carousel-control-next" type="button" data-bs-target="#carousel-<?php echo $product['id']; ?>" data-bs-slide="next">
                                                    <span class="carousel-control-next-icon"></span>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <img src="https://via.placeholder.com/200" class="card-img-top" alt="Nessuna immagine" style="height: 150px; object-fit: cover;" />
                                    <?php endif; ?>
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
<?php include $path2root . 'components/footer.php'; ?>
