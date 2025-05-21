<?php
session_start();

$path2root = "../";
if (!isset($_SESSION['user'])) {
    header("Location:" . $path2root . "index.php");
    exit();
}

include $path2root.'/components/navbar.php';
require $path2root.'/pages/config.php';

$cartProducts = [];
$total = 0.0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $ids = array_column($_SESSION['cart'], 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));

    $stmt = $conn->prepare("SELECT id, nome, prezzo FROM products WHERE id IN ($placeholders)");
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $cartProducts[$row['id']] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <title>Il tuo carrello</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .cart-summary {
            background: #fffbe6;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        .cart-total {
            font-size: 1.3rem;
            font-weight: bold;
            color: #FFB22C;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center mb-4">🛒 Il tuo carrello</h2>

        <?php if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])): ?>
            <div class="text-center mt-5">
                <h4 class="text-muted">Il tuo carrello è vuoto.</h4>
                <a href="<?= $path2root ?>index.php" class="btn btn-secondary mt-3 px-4">Torna allo shop</a>
            </div>
        <?php else: ?>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-lg p-4">
                        <ul class="list-group shadow-sm mb-4">
                            <?php foreach ($_SESSION['cart'] as $item): 
                                $prodotto = isset($cartProducts[$item['id']]) ? $cartProducts[$item['id']] : null;
                                if (!$prodotto) continue;
                                $subtotal = $prodotto['prezzo'] * $item['quantity'];
                                $total += $subtotal;
                            ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="fw-semibold"><?= htmlspecialchars($prodotto['nome']) ?></span>
                                        <span class="text-muted small ms-2">€<?= number_format($prodotto['prezzo'], 2, ',', '.') ?></span>
                                    </div>
                                    <div class="d-flex flex-column align-items-end" style="min-width: 90px;">
                                        <span class="badge bg-primary rounded-pill mb-1" style="width: 48px; text-align: center;">
                                            x<?= (int) $item['quantity'] ?>
                                        </span>
                                        <span class="fw-bold">€<?= number_format($subtotal, 2, ',', '.') ?></span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="cart-total" style="color: #222; font-weight: bold;">Totale:</span>
                            <span class="cart-total" style="color: #222; font-weight: bold;">€<?= number_format($total, 2, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="checkout.php" class="btn btn-success px-4" target="_blank">Procedi al checkout</a>
                            <a href="<?= $path2root .'index.php'?>" class="btn btn-secondary px-4">Torna allo shop</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div><?php include $path2root . 'components/footer.php'; ?>

</body>
</html>
