<?php
require 'connection.php';
session_start();

$data = json_decode(file_get_contents('php://input'), true);
$productId = $data['productId'];

if (isset($_SESSION['sale'][$productId])) {
    $quantity = $_SESSION['sale'][$productId]['quantity'];

    $stmt = $conn->prepare("UPDATE product SET quantity = quantity + ? WHERE product_id = ?");
    $stmt->bind_param("ii", $quantity, $productId);
    $stmt->execute();

    unset($_SESSION['sale'][$productId]);

    $total = 0;
    $totalProductsInList = count($_SESSION['sale']);
    foreach ($_SESSION['sale'] as $id => $sale) {
        $product = $conn->query("SELECT price FROM product WHERE product_id = $id")->fetch_assoc();
        $total += $sale['quantity'] * $product['price'];
    }

    echo json_encode([
        'success' => true,
        'total' => number_format($total, 2),
        'totalProductsInList' => $totalProductsInList
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Product not found'
    ]);
}
?>
