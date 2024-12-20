<?php
require 'db/connection.php';
require 'db/show.php';

session_start();

// Define a threshold for low stock
$lowStockThreshold = 20;

// Fetch all products
$show = new Show($conn, 'product');
$productz = $show->showRecords(null);
$notificationCount = 0;

foreach ($productz as $product) {
    $stock = $product[3]; // Assuming the stock count is in the 4th column
    if ($stock <= 0 || $stock <= $lowStockThreshold) {
        $notificationCount++;
    }
}

// Return JSON response
echo json_encode(['notificationCount' => $notificationCount]);
?>
