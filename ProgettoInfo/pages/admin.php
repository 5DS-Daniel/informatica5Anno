<?php
session_start();
$path2root = "../";
require $path2root . "pages/config.php";

if (!isset($_SESSION['user']) || $_SESSION['ruolo'] !== 'admin') {
    header("Location: " . $path2root . "index.php");
    exit();
}

$errors = [];
$success = "";


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_user"])) {
    $userId = $_POST["user_id"];
    $newUsername = trim($_POST["username"]);
    $newEmail = trim($_POST["email"]);
    $newRuolo = $_POST["ruolo"];

    if (empty($newUsername) || empty($newEmail)) {
        $errors[] = "Tutti i campi sono obbligatori.";
    } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Inserisci un'email valida.";
    } else {
        try {
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, ruolo = ? WHERE id = ?");
            $stmt->bind_param("sssi", $newUsername, $newEmail, $newRuolo, $userId);
            
            if ($stmt->execute()) {
                $success = "Dati aggiornati con successo!";
            } else {
                throw new Exception("Errore durante l'aggiornamento: " . $stmt->error);
            }
            
            $stmt->close();
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_user"])) {
    $userId = $_POST["user_id"];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();
    $success = "Utente eliminato con successo!";
}

include $path2root . '/components/navbar.php';
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Utenti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function toggleEditForm(userId) {
            let form = document.getElementById("edit-form-" + userId);
            form.style.display = (form.style.display === "none" || form.style.display === "") ? "block" : "none";
        }

        function searchUsers() {
            let input = document.getElementById("search").value.toLowerCase();
            let rows = document.querySelectorAll("tbody tr.user-row");

            rows.forEach(row => {
                let username = row.getAttribute("data-username").toLowerCase();
                let email = row.getAttribute("data-email").toLowerCase();
                let ruolo = row.getAttribute("data-ruolo").toLowerCase();

                if (username.includes(input) || email.includes(input) || ruolo.includes(input)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }
    </script>
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="text-center">Gestione Utenti</h2>

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

    <input type="text" id="search" class="form-control mb-3" placeholder="Cerca utenti..." onkeyup="searchUsers()">

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Ruolo</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $conn->prepare("SELECT id, username, email, ruolo FROM users");
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()):
            ?>
                <tr class="user-row" 

                    data-username="<?php echo htmlspecialchars($row['username']); ?>" 
                    data-email="<?php echo htmlspecialchars($row['email']); ?>" 
                    data-ruolo="<?php echo htmlspecialchars($row['ruolo']); ?>">

                    <!-- Gli attributi personalizzati ci permettono di passare valori, o meglio assegnali ad elementi html -->
                    
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['ruolo']); ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="toggleEditForm(<?php echo $row['id']; ?>)">Modifica</button>
                        <form action="admin.php" method="POST" class="d-inline">
                            <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete_user" class="btn btn-danger btn-sm" onclick="return confirm('Sei sicuro di voler eliminare questo utente?');">Elimina</button>
                        </form>
                    </td>
                </tr>
                <tr id="edit-form-<?php echo $row['id']; ?>" style="display: none;">
                    <td colspan="5">
                        <form action="admin.php" method="POST">
                            <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($row['username']); ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                                </div>
                                <div class="col-md-2">
                                    <select name="ruolo" class="form-control">
                                        <option value="admin" <?php if ($row['ruolo'] == 'admin') echo 'selected'; ?>>Admin</option>
                                        <option value="utente" <?php if ($row['ruolo'] == 'utente') echo 'selected'; ?>>Utente</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" name="update_user" class="btn btn-success">Salva</button>
                                </div>
                            </div>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            <?php $stmt->close(); ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include $path2root . 'components/footer.php'; ?>
