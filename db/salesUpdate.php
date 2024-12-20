<?php
require 'connection.php';
session_start();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$productId = $data['productId'];
$newQuantity = $data['newQuantity'];

$query = $conn->prepare("SELECT quantity, price FROM product WHERE product_id = ?");
$query->bind_param("i", $productId);
$query->execute();
$result = $query->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found.']);
    exit();
}

$currentStock = $product['quantity'];
$currentPrice = $product['price'];

$currentSaleQuantity = isset($_SESSION['sale'][$productId]['quantity']) ? $_SESSION['sale'][$productId]['quantity'] : 0;
$quantityDifference = $newQuantity - $currentSaleQuantity;

if ($quantityDifference > $currentStock) {
    echo json_encode(['success' => false, 'message' => 'Insufficient stock for this update.']);
    exit();
}

$_SESSION['sale'][$productId]['quantity'] = $newQuantity;

$newStock = $currentStock - $quantityDifference;
$updateStockQuery = $conn->prepare("UPDATE product SET quantity = ? WHERE product_id = ?");
$updateStockQuery->bind_param("ii", $newStock, $productId);
$updateStockQuery->execute();

$amount = $currentPrice * $newQuantity;

$total = 0;
foreach ($_SESSION['sale'] as $id => $sale) {
    $productPrice = $conn->query("SELECT price FROM product WHERE product_id = $id")->fetch_assoc()['price'];
    $total += $sale['quantity'] * $productPrice;
}

echo json_encode(['success' => true, 'amount' => number_format($amount, 2), 'total' => number_format($total, 2)]);
?>
