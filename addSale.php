<?php
require 'db/connection.php';
require 'db/show.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_sale'])) {
    $productId = key($_POST['add_to_sale']);
    $quantity = intval($_POST['quantity'][$productId]);

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("SELECT quantity FROM product WHERE product_id = ? FOR UPDATE");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if ($product) {
            $currentStock = $product['quantity'];

            $existingQuantity = isset($_SESSION['sale'][$productId]) ? $_SESSION['sale'][$productId]['quantity'] : 0;
            $newQuantity = $existingQuantity + $quantity;

            if ($newQuantity <= $currentStock) {
                $newStock = $currentStock - $quantity;
                $updateStmt = $conn->prepare("UPDATE product SET quantity = ? WHERE product_id = ?");
                $updateStmt->bind_param("ii", $newStock, $productId);
                $updateStmt->execute();

                $_SESSION['sale'][$productId] = [
                    'quantity' => $newQuantity,
                ];

                $_SESSION['message'] = "Product quantity updated successfully in the sale!";
            } else {
                $_SESSION['message'] = "Insufficient stock for this product!";
            }
        } else {
            $_SESSION['message'] = "Product not found!";
        }

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['message'] = "An error occurred while processing the sale!";
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$show = new Show($conn, 'product');
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
$recordsPerPage = 10;
$paginationData = $show->showRecordsWithPagination($currentPage, null, $recordsPerPage, null);
$products = $paginationData['records'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <script src="bootstrap/js/bootstrap.bundle.js"></script>
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                        <h4>Sale Management</h4>
                    </div>

                    <div class="card border-0">
                        <div class="card-header">
                            <h5 class="card-title">Products for Sale</h5>
                        </div>
                        <div class="card-body table-responsive container">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Quantity</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (count($products) > 0) {
                                        foreach ($products as $index => $product) {
                                            echo "<tr>";
                                            echo "<td>" . (($currentPage - 1) * $recordsPerPage + $index + 1) . "</td>";
                                            echo "<td>" . htmlspecialchars($product[1]) . "</td>";
                                            echo "<td>" . number_format($product[2], 2) . "</td>";
                                            echo "<td>" . (int)$product[3] . "</td>";
                                            echo "<td>
                                                <form method='POST'>
                                                    <input type='number' name='quantity[{$product[0]}]' value='1' min='1' max='{$product[3]}' class='form-control' required>
                                            </td>";
                                            echo "<td>
                                                    <button type='submit' name='add_to_sale[{$product[0]}]' class='btn btn-success'>
                                                        Add to Sale
                                                    </button>
                                                </form>
                                            </td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>No products found</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>

                            <nav class="d-flex justify-content-center" aria-label="Page navigation example">
                                <ul class="pagination">
                                    <?php if ($currentPage > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= ($currentPage - 1) ?>"><</a>
                                        </li>
                                    <?php endif; ?>
                                    <?php for ($page = 1; $page <= $paginationData['totalPages']; $page++): ?>
                                        <li class="page-item <?= ($page == $currentPage) ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $page ?>"><?= $page ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <?php if ($currentPage < $paginationData['totalPages']): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= ($currentPage + 1) ?>">></a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="js/script.js"></script>
    <script>
    <?php if (isset($_SESSION['message'])) { ?>
        const message = "<?= addslashes($_SESSION['message']) ?>";
        const isSuccess = message.includes("successfully");
        
        Swal.fire({
            title: isSuccess ? 'Success!' : 'Error!',
            text: message,
            icon: isSuccess ? 'success' : 'error',
            confirmButtonText: 'OK'
        });
        <?php unset($_SESSION['message']); ?>
    <?php } ?>
    </script>
</body>
</html>
