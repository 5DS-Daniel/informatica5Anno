<?php

session_start();
$path2root = "../";
require $path2root . "pages/config.php"; // Connessione al DB

if (!isset($_SESSION['user'])) {
    header("Location:" . $path2root . "index.php");
    exit();
}

$username = $_SESSION['user'];

$stmt = $conn->prepare("SELECT email, password, profile_pic FROM users WHERE username = ?");
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
$errors = [];
$success = "";

// Gestione upload immagine profilo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["upload_profile_pic"])) {
    if (!empty($_FILES["profile_pic"]["name"])) {
        $target_dir = "../uploads/";
        $file_name = basename($_FILES["profile_pic"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Controlla se è un'immagine valida
        $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
        if ($check === false) {
            $errors[] = "Il file non è un'immagine valida.";
        }

        // Controllo dimensioni file (max 2MB)
        if ($_FILES["profile_pic"]["size"] > 2 * 1024 * 1024) {
            $errors[] = "Il file è troppo grande. Massimo 2MB.";
        }

        // Formati consentiti
        $allowed_formats = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $allowed_formats)) {
            $errors[] = "Formato non supportato. Usa JPG, JPEG, PNG o GIF.";
        }

        // Se non ci sono errori, salva l'immagine
        if (empty($errors)) {
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE username = ?");
                $stmt->bind_param("ss", $file_name, $username);
                $stmt->execute();
                $profile_pic = $file_name;
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
        <div class="col-md-6">
            <div class="card shadow-lg p-4 text-center">
                
                <!-- Immagine del profilo -->
                <div class="mb-3">
                    <img src="../uploads/<?php echo htmlspecialchars($profile_pic); ?>" alt="Immagine profilo" class="rounded-circle border" width="150" height="150">
                </div>

                <!-- Form per cambiare immagine -->
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

                <!-- Form per aggiornare i dati -->
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

                    <button type="submit" name="update_account" class="btn btn-primary w-100">Aggiorna</button>
                </form>

                <hr>
                <p class="mt-3 text-center">
                    Torna alla <a href="<?php echo $path2root ?>index.php">home</a> | 
                    <a href="#" id="toggleDeleteForm" class="text-danger">Elimina Account</a>
                </p>

                <div id="deleteAccountForm" style="display: none;">
                    <h3 class="text-center text-danger">Elimina account</h3>
                    <p class="text-center">Attenzione! Questa azione è irreversibile.</p>
                    
                    <form action="account.php" method="POST">
                        <div class="mb-3">
                            <label for="delete_password" class="form-label">Inserisci la password</label>
                            <input type="password" name="delete_password" id="delete_password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="delete_password_confirm" class="form-label">Conferma la password</label>
                            <input type="password" name="delete_password_confirm" id="delete_password_confirm" class="form-control" required>
                        </div>

                        <button type="submit" name="delete_account" class="btn btn-danger w-100">Elimina Account</button>
                    </form>
                </div>

                <script>
                    document.getElementById("toggleDeleteForm").addEventListener("click", function(event) {
                        event.preventDefault();
                        let form = document.getElementById("deleteAccountForm");
                        if (form.style.display === "none" || form.style.display === "") {
                            form.style.display = "block";
                        } else {
                            form.style.display = "none";
                        }
                    });
                </script>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
