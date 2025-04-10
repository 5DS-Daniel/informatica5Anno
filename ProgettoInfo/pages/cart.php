<?php
session_start();

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<p>Il tuo carrello è vuoto.</p>";
} else {
    echo "<ul>";
    foreach ($_SESSION['cart'] as $item) {
        echo "<li>🛒 " . htmlspecialchars($item['name']) . "</li>";
    }
    echo "</ul>";
}
?>
