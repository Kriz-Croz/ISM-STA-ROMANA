<?php
require 'db/connection.php';
require 'db/show.php';

session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// Define a threshold for low stock
$lowStockThreshold = 20;

// Fetch all products along with their supplier's number from the database
$show = new Show($conn, 'product');
$query = "
    SELECT p.product_id, p.product_name, p.quantity, s.supplier_number 
    FROM product p
    LEFT JOIN supplier s ON p.supplier_id = s.supplier_id
";
$products = $conn->query($query); // You can modify this as needed if you're using the Show class
$productz = $show->showRecords(null); 
$notificationCount = 0;
foreach ($productz as $product) {
    $stock = $product[3]; // Assuming the stock count is in the 4th column
    if ($stock <= 0 || $stock <= $lowStockThreshold) {
        $notificationCount++;
    }
}

// Store notification count in session
$_SESSION['notificationCount'] = $notificationCount;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <script src="bootstrap/js/bootstrap.bundle.js"></script>
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="main">
        <nav class="navbar navbar-expand px-3 border-bottom">
            <button class="btn" id="sidebar-toggle" type="button">
                <span class="navbar-toggler-icon"></span>
            </button>
        </nav>
        
        <main class="content px-3 py-2">
            <div class="container-fluid">
                <div class="mb-3">
                    <h4>Stock Notifications</h4>
                </div>

                <!-- Table for Notifications -->
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Stock Status</th>
                            <th>Notification</th>
                            <th>Supplier Contact</th> <!-- New Column -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Loop through each product and check stock status
                        while ($product = $products->fetch_assoc()) {
                            $productName = $product['product_name'];
                            $stock = $product['quantity'];
                            $supplierNumber = $product['supplier_number'];

                            // Check if stock is low or out of stock
                            if ($stock <= 0) {
                                $statusClass = 'bg-danger text-white';
                                $statusText = 'Out of Stock';
                                $notificationText = "Product '{$productName}' is out of stock.";
                            } elseif ($stock <= $lowStockThreshold) {
                                $statusClass = 'bg-warning text-dark';
                                $statusText = 'Low Stock';
                                $notificationText = "Product '{$productName}' is low on stock. Only {$stock} left.";
                            } else {
                                continue; // Skip products that are in stock
                            }

                            // Display Product in Table Row
                            echo "
                                <tr>
                                    <td>{$productName}</td>
                                    <td><span class='badge {$statusClass}'>{$statusText}</span></td>
                                    <td>{$notificationText}</td>
                                    <td>{$supplierNumber}</td> <!-- Display Supplier Number -->
                                </tr>
                            ";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<script src="js/script.js"></script>


<script>
    function updateSidebarNotification() {
        fetch('notification_count.php')
            .then(response => response.json())
            .then(data => {
                const badge = document.querySelector('.sidebar-item .badge');
                if (data.notificationCount > 0) {
                    badge.textContent = data.notificationCount;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            })
            .catch(error => console.error('Error fetching notification count:', error));
    }

    // Fetch notifications every 5 seconds
    setInterval(updateSidebarNotification, 5000);

    // Initial fetch on page load
    document.addEventListener('DOMContentLoaded', updateSidebarNotification);
</script>

</body>
</html>
