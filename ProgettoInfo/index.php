<?php

session_start();
$path2root = "";
require "pages/config.php";

include 'components/navbar.php';


$successMessage = '';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['product_name'])) {
    $productId = (int) $_POST['product_id'];
    $productName = $_POST['product_name'];

    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] === $productId) {
            $item['quantity'] += 1;
            $successMessage = "🔁 Quantità aggiornata nel carrello.";
            $found = true;
            break;
        }
    }
    

    if (!$found) {
        $_SESSION['cart'][] = [
            'id' => $productId,
            'name' => $productName,
            'quantity' => 1
        ];
        $successMessage = "✅ Prodotto aggiunto al carrello!";
    }
}


//Ho deciso di recuperare gli ultimi 5 prod ma alla fin fine si puo scegleire quanti
// e soprattutto magari in futuro anche filtrali

$stmt = $conn->prepare("SELECT p.id, p.nome, p.descrizione, p.prezzo, u.username 
                        FROM products p 
                        JOIN users u ON p.user_id = u.id
                        ORDER BY p.id DESC
                        LIMIT 5");
$stmt->execute();
$result = $stmt->get_result();
$prodotti = [];
while ($prodotto = $result->fetch_assoc()) {
    $img_stmt = $conn->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
    $img_stmt->bind_param("i", $prodotto['id']);
    $img_stmt->execute();
    $img_result = $img_stmt->get_result();
    $immagini = [];
    while ($img = $img_result->fetch_assoc()) {
        $immagini[] = $img['image_path'];
    }
    $img_stmt->close();
    $prodotto['immagini'] = $immagini;
    $prodotti[] = $prodotto;
}
$stmt->close();

?>

<!DOCTYPE html>
<html lang="it">

<head>
    <title>CapitoloDue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body class="bg-light">
    <!--questo pezzo è quello che botstrap chiama toast, che indica il piccolo pop-up che appare in basso a destra  -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
        <div id="cartToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" <?php if (!empty($successMessage)) echo 'data-bs-autohide="false"'; ?>>
            <div class="d-flex">
                <div class="toast-body">
                    <?= htmlspecialchars($successMessage) ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Chiudi"></button>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <h2 class="text-center">
            <?php if (isset($_SESSION['user'])): ?>
                Ciao <?= htmlspecialchars($_SESSION['user']) ?>, benvenuto!
            <?php else: ?>
                Benvenuto, visita il nostro sito!
            <?php endif; ?>
        </h2>

        <h3 class="mt-4">🔥 Ultimi prodotti caricati</h3>
        <div class="row">
            <?php if (!empty($prodotti)): ?>
                <?php foreach ($prodotti as $prodotto): ?>
                    <div class="col-md-4 mt-3">
                        <div class="card shadow-sm d-flex flex-column h-100">
                            <?php if (!empty($prodotto['immagini'])): ?>
                                <img src="<?= htmlspecialchars($path2root . $prodotto['immagini'][0]) ?>" class="card-img-top" alt="<?= htmlspecialchars($prodotto['nome']) ?>" style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/200" class="card-img-top" alt="Nessuna immagine" style="height: 200px; object-fit: cover;">
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column flex-grow-1">
                                <h5 class="card-title"><?= htmlspecialchars($prodotto['nome']) ?></h5>
                                <p class="card-text flex-grow-1"><?= htmlspecialchars($prodotto['descrizione']) ?></p>
                                <h6 class="text-primary"><?= htmlspecialchars($prodotto['prezzo']) ?>€</h6>
                                <p class="small text-muted">Venduto da: <?= htmlspecialchars($prodotto['username']) ?></p>
                                <form method="POST" action="">
                                    <input type="hidden" name="product_id" value="<?= (int) $prodotto['id'] ?>">
                                    <input type="hidden" name="product_name" value="<?= htmlspecialchars($prodotto['nome']) ?>">
                                    <button type="submit" class="btn w-100 mt-auto" style="background-color: #FFB22C">🛒 Aggiungi al carrello</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">Nessun prodotto disponibile al momento.</p>
            <?php endif; ?>
        </div>
    </div>
<?php include $path2root . 'components/footer.php'; ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            <?php if (!empty($successMessage)): ?>
                var toastEl = document.getElementById('cartToast');
                var toast = new bootstrap.Toast(toastEl, { delay: 2500 });
                toast.show();
            <?php endif; ?>
        });
    </script>
</body>

</html>
