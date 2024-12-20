<?php
require 'connection.php';
session_start(); // Start the session

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['sales']) && isset($data['total'])) {
    $sales = $data['sales'];
    $total = $data['total'];

    $conn->begin_transaction();

    try {
        foreach ($sales as $productId => $sale) {
            $quantity = $sale['quantity'];
            $product = $conn->query("SELECT price FROM product WHERE product_id = $productId")->fetch_assoc();

            if ($product) {
                $price = $product['price'];
                $amount = $quantity * $price;

                $stmt = $conn->prepare("INSERT INTO sales_records (product_id, quantity, price, amount) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iidd", $productId, $quantity, $price, $amount);
                $stmt->execute();
            }
        }

        $conn->commit();

        unset($_SESSION['sale']); 

        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
}
?>
