<?php

session_start();
require_once '../utils/fpdf.php'; // Assicurati che il percorso sia corretto

$path2root = "../";
if (!isset($_SESSION['user']) || empty($_SESSION['cart'])) {
    header("Location: " . $path2root . "index.php");
    exit();
}

require $path2root . '/pages/config.php';

// Recupera i dettagli dei prodotti nel carrello
$cartProducts = [];
$total = 0.0;

$ids = array_column($_SESSION['cart'], 'id');
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$types = str_repeat('i', count($ids));

$stmt = $conn->prepare("SEECT id, nome, prezzo FROM products WHERE id IN ($placeholders)");
$stmt->bind_param($types, ...$ids);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $cartProducts[$row['id']] = $row;
}
$stmt->close();

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Scontrino Acquisto', 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Data: ' . date('d/m/Y H:i'), 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(80, 8, 'Prodotto', 1);
$pdf->Cell(30, 8, 'Prezzo', 1, 0, 'R');
$pdf->Cell(30, 8, 'Quantita', 1, 0, 'R');
$pdf->Cell(40, 8, 'Totale', 1, 1, 'R');
$pdf->SetFont('Arial', '', 12);

foreach ($_SESSION['cart'] as $item) {
    $prodotto = isset($cartProducts[$item['id']]) ? $cartProducts[$item['id']] : null;
    if (!$prodotto) continue;
    $subtotal = $prodotto['prezzo'] * $item['quantity'];
    $total += $subtotal;
    $pdf->Cell(80, 8, utf8_decode($prodotto['nome']), 1);
    $pdf->Cell(30, 8, 'EUR ' . number_format($prodotto['prezzo'], 2, ',', '.'), 1, 0, 'R');
    $pdf->Cell(30, 8, $item['quantity'], 1, 0, 'R');
    $pdf->Cell(40, 8, 'EUR ' . number_format($subtotal, 2, ',', '.'), 1, 1, 'R');
}

$pdf->SetFont('Arial', 'B', 13);
$pdf->Cell(140, 10, 'Totale', 1);
$pdf->Cell(40, 10, 'EUR ' . number_format($total, 2, ',', '.'), 1, 1, 'R');

$pdf->Output('I', 'scontrino.pdf');
exit();
?>