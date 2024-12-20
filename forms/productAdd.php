<?php
session_start(); // Start the session

require '../db/connection.php';
require '../db/show.php';
require '../db/add.php';

$add = new Add($conn, 'product');
$showCategory = new Show($conn, 'category');
$showBrand = new Show($conn, 'brand');
$showSupplier = new Show($conn, 'supplier');

// Check if success message is set in session
$successMessage = isset($_SESSION['successMessage']) ? $_SESSION['successMessage'] : "";

if ($successMessage) {
    unset($_SESSION['successMessage']);
}

if (isset($_POST['add'])) {
    $data = [];
    foreach ($_POST as $name => $value) {
        if ($name != "add") {
            $data[$name] = $value;
        }
    }

    // Calculate status based on quantity
    $quantity = (int)$data['quantity'];
    if ($quantity == 0) {
        $data['status'] = 'Out of Stock';
    } elseif ($quantity <= 20) {
        $data['status'] = 'Low Stock';
    } else {
        $data['status'] = 'Available';
    }

    try {
        // Add product to the database
        $action = $add->addQuery($data);
        
        // Set the success message in session
        $_SESSION['successMessage'] = "New Product Added Successfully";
        
        // Redirect to avoid resubmitting the form on page reload
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } catch (Exception $e) {
        echo "Error: $e";
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <script src="../bootstrap/js/bootstrap.bundle.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert Library -->
    <script>
        // Show SweetAlert if the success message is set
        document.addEventListener('DOMContentLoaded', () => {
            const successMessage = <?= json_encode($successMessage); ?>;
            if (successMessage) {
                Swal.fire({
                    title: 'Success!',
                    text: successMessage,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            }
        });
    </script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Add New Product</h2>
        <div class="card">
            <div class="card-body">
                <form action="" method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" name="product_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="brand_id" class="form-label">Brand</label>
                        <select name="brand_id" class="form-select" required>
                            <option value="" disabled selected>Select Brand</option>
                            <?php
                            $brands = $showBrand->showRecords();
                            foreach ($brands as $brand) {
                                echo "<option value='{$brand[0]}'>{$brand[1]}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select name="category_id" class="form-select" required>
                            <option value="" disabled selected>Select Category</option>
                            <?php
                            $categories = $showCategory->showRecords();
                            foreach ($categories as $category) {
                                echo "<option value='{$category[0]}'>{$category[1]}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" name="price" class="form-control" step="any" required>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" name="quantity" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="supplier_id" class="form-label">Supplier</label>
                        <select name="supplier_id" class="form-select" required>
                            <option value="" disabled selected>Select Supplier</option>
                            <?php
                            $suppliers = $showSupplier->showRecords();
                            foreach ($suppliers as $supplier) {
                                echo "<option value='{$supplier[0]}'>{$supplier[1]}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="../product.php" class="btn btn-secondary">Back</a>
                        <button type="submit" name="add" class="btn btn-primary">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
