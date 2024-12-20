<?php
require 'connection.php';
require 'show.php';
require 'update.php';

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_SESSION['sale_id'])) {
    $saleQuery = "INSERT INTO sale (sale_id) VALUES (NULL)";  
    $conn->query($saleQuery);
    
    $_SESSION['sale_id'] = $conn->insert_id;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_to_sale'])) {
        $showProduct = new Show($conn, 'product');
        $productIds = array_keys($_POST['add_to_sale']);
        $quantities = $_POST['quantity'];

        foreach ($productIds as $productId) {
            $quantity = (int)$quantities[$productId];
            if ($quantity <= 0) continue;

            $productData = $showProduct->showRecords("product_id = $productId");
            if (count($productData) > 0) {
                $product = $productData[0];
                $price = $product[2];
                $stock = $product[3];

                if ($quantity > $stock) {
                    $_SESSION['message'] = "Not enough stock for product: " . $product[1];
                    header("Location: ../sales.php");
                    exit();
                }

                $amount = $quantity * $price;

                $saleId = $_SESSION['sale_id'];  
                $query = "INSERT INTO sale (sale_id, product_id, quantity, sub_price) 
                          VALUES ('$saleId', '$productId', '$quantity', '$amount')";
                $conn->query($query);

                $newStock = $stock - $quantity;
                $updateStockQuery = "UPDATE product SET stock = $newStock WHERE product_id = $productId";
                $conn->query($updateStockQuery);
            }
        }

        $_SESSION['message'] = "Products added to sale successfully!";
        header("Location: ../sales.php"); 
        exit();
    }
}
