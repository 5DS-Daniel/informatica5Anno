<?php

session_start();
$path2root = "../";
include $path2root.'/components/navbar.php';
require $path2root.'/pages/config.php';

if (isset($_SESSION['user'])) {
    header("Location: " . $path2root . "index.php");
    exit();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $errors[] = "Tutti i campi sono obbligatori.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, ruolo FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $username, $hashed_password, $ruolo);
            $stmt->fetch();
            
            if (password_verify($password, $hashed_password)) {
                $_SESSION['user'] = $username;
                $_SESSION['user_id'] = $id;
                $_SESSION['ruolo'] = $ruolo;

                header("Location: " . $path2root . "index.php");
                exit();
            } else {
                $errors[] = "Email o password errata.";
            }
        } else {
            $errors[] = "Email o password errata.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg p-4">
                <h2 class="text-center mb-4">Accedi</h2>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Accedi</button>
                </form>

                <p class="mt-3 text-center">
                    Non hai un account? <a href="register.php">Registrati</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include $path2root . 'components/footer.php'; ?>
