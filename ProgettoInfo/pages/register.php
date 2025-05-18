<?php

session_start();

$path2root = "../";
include $path2root . '/components/navbar.php';
require $path2root . '/pages/config.php';

$errors = [];
$username = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    
    if (empty($username)) {
        $errors[] = "Il nome utente è obbligatorio.";
    }
    if (empty($email)) {
        $errors[] = "Inserisci un'email valida.";
    }
    if (empty($password)) {
        $errors[] = "La password è obbligatoria.";
    } elseif (strlen($password) < 8) { 
        $errors[] = "La password deve avere almeno 8 caratteri.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $errors[] = "La password deve contenere almeno una lettera maiuscola, una minuscola, un numero e un carattere speciale.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Le password non coincidono.";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            $stmt->execute();
            $user_id = $stmt->insert_id; // Get the newly created user's ID
            $stmt->close();

            $user_folder = $path2root . "uploads/users/" . $user_id;

            if (!file_exists($user_folder)) {
                if (!mkdir($user_folder, 0777, true)) {
                    $errors[] = "Errore nella creazione della cartella per l'utente.";
                }
            }

            $_SESSION['user'] = $username;
            $_SESSION['user_id'] = $user_id; // Store the user ID in the session
            $_SESSION['role'] = 'user';

            echo "Registrazione avvenuta con successo!";
            header("Location:" . $path2root . "index.php");
            exit();
        } catch (Exception $e) {
            $message = $e->getMessage();
            if (@mysqli_errno($conn) == 1644) {
                $message = @mysqli_error($conn);
                $errors[] = $message;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg p-4">
                <h2 class="text-center mb-4">Registrati</h2>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="register.php" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nome utente</label>
                        <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Conferma Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Registrati</button>
                </form>

                <p class="mt-3 text-center">
                    Hai già un account? <a href="<?php echo $path2root ?>pages/login.php">Accedi</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
