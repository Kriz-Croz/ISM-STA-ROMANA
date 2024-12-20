<?php
require 'connection.php';

header('Content-Type: application/json');

$productId = intval($_GET['productId'] ?? 0);

$query = $conn->prepare("SELECT quantity FROM product WHERE product_id = ?");
$query->bind_param("i", $productId);
$query->execute();
$result = $query->get_result();

if ($product = $result->fetch_assoc()) {
    echo json_encode(['success' => true, 'quantity' => $product['quantity']]);
} else {
    echo json_encode(['success' => false, 'message' => 'Product not found.']);
}
?>
