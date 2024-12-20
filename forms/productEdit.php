<?php
require '../db/connection.php';
require '../db/show.php';
require '../db/update.php';

// Start the session to store messages
session_start();

$id = $_GET['id'] ?? NULL;
if (!isset($id)) {
    header("Location: product.php"); // Redirect if no ID is provided
    exit();
}

$show = new Show($conn, 'product');
$showCategory = new Show($conn, 'category');
$showBrand = new Show($conn, 'brand');
$showSupplier = new Show($conn, 'supplier');

$update = new Update($conn, 'product', ['product_id' => $id]);

// Fetch product data
$data = $show->showRecords("product_id = $id");

if (isset($_POST['Update'])) {
    $data = [];
    foreach ($_POST as $name => $value) {
        if ($name != "Update") {
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
        $update->updateQuery($data);
        $_SESSION['message'] = "Product updated successfully!";
        $_SESSION['message_type'] = 'success'; // Message type for SweetAlert in product.php
        header('Location: ../product.php'); // Redirect to product.php
        exit();
    } catch (Exception $e) {
        $_SESSION['message'] = "Error: $e";
        $_SESSION['message_type'] = 'error'; // Error message type for SweetAlert in product.php
        header('Location: ../product.php'); // Redirect to product.php
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <script src="../bootstrap/js/bootstrap.bundle.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Edit Product</h2>
        <div class="card">
            <div class="card-body">
                <form action="" method="post">
                    <div class="mb-3">
                        <label for="product_name" class="form-label">Product Name</label>
                        <input type="text" name="product_name" value="<?= $data[0][1] ?>" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="brand_id" class="form-label">Brand</label>
                        <select name="brand_id" class="form-select" required>
                            <option value="" disabled>Select Brand</option>
                            <?php
                                $brands = $showBrand->showRecords();
                                foreach ($brands as $brand) {
                                    $selected = ($data[0][5] == $brand[0]) ? "selected" : "";  
                                    echo "<option value='{$brand[0]}' $selected>{$brand[1]}</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select name="category_id" class="form-select" required>
                            <option value="" disabled>Select Category</option>
                            <?php
                                $categories = $showCategory->showRecords();
                                foreach ($categories as $category) {
                                    $selected = ($data[0][6] == $category[0]) ? "selected" : "";  
                                    echo "<option value='{$category[0]}' $selected>{$category[1]}</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" step="any" name="price" value="<?= $data[0][2] ?>" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" name="quantity" value="<?= $data[0][3] ?>" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="supplier_id" class="form-label">Supplier</label>
                        <select name="supplier_id" class="form-select" required>
                            <option value="" disabled>Select Supplier</option>
                            <?php
                                $suppliers = $showSupplier->showRecords();
                                foreach ($suppliers as $supplier) {
                                    $selected = ($data[0][7] == $supplier[0]) ? "selected" : "";
                                    echo "<option value='{$supplier[0]}' $selected>{$supplier[1]}</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="../product.php" class="btn btn-secondary">Back</a>
                        <input type="submit" value="Update Product" name="Update" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
