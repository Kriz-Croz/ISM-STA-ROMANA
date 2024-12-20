<?php
require 'db/connection.php';
require 'db/show.php';

session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// Capture sorting parameters
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'product_name';  // Default sorting by product_name
$sortDir = isset($_GET['dir']) ? $_GET['dir'] : 'ASC';  // Default to ascending order

// Ensure the direction is either ASC or DESC
if ($sortDir !== 'ASC' && $sortDir !== 'DESC') {
    $sortDir = 'ASC';
}

// Map the user-friendly column names to the actual column names in the database
$sortMap = [
    'name' => 'product_name', // Assuming 'product_name' is the column for product name
    'brand' => 'brand_id',
    'category' => 'category_id',
    'price' => 'price',
    'stock' => 'quantity',
    'status' => 'status',
    'supplier' => 'supplier_id',
];

// If the provided sort column exists in the map, use the mapped value
if (array_key_exists($sortColumn, $sortMap)) {
    $order = $sortMap[$sortColumn] . ' ' . $sortDir;
} else {
    $order = 'product_name ' . $sortDir; // Default to sorting by product_name
}

// Fetch the data with pagination and sorting
$show = new Show($conn, 'product');
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
$paginationData = $show->showRecordsWithPagination($currentPage, null, 10, $order);
$products = $paginationData['records'];
$showCategory = new Show($conn, 'category');
$showBrand = new Show($conn, 'brand');
$showSupplier = new Show($conn, 'supplier');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <script src="bootstrap/js/bootstrap.bundle.js"></script>
    <link rel="stylesheet" href="bootstrap-icons-1.11.3/font/bootstrap-icons.css">
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/style.css">
    <!-- SweetAlert -->
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
                        <h4>Product Management</h4>
                    </div>

                    <div class="card border-0">
                        <div class="card-header d-flex justify-content-between">
                            <h5 class="card-title">
                                Products
                            </h5>
                            <a href="forms/productAdd.php" class="btn btn-success">Add New Product</a>
                        </div>

                        <div class="card-body table-responsive container">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">
                                            <a href="?sort=name&dir=<?= $sortColumn == 'name' && $sortDir == 'ASC' ? 'DESC' : 'ASC' ?>">
                                                Name
                                                <?php if ($sortColumn == 'name'): ?>
                                                    <i class="bi bi-caret-<?= $sortDir == 'ASC' ? 'up' : 'down' ?>"></i>
                                                <?php endif; ?>
                                            </a>
                                        </th>
                                        <th scope="col">
                                            <a href="?sort=brand&dir=<?= $sortColumn == 'brand' && $sortDir == 'ASC' ? 'DESC' : 'ASC' ?>">
                                                Brand
                                                <?php if ($sortColumn == 'brand'): ?>
                                                    <i class="bi bi-caret-<?= $sortDir == 'ASC' ? 'up' : 'down' ?>"></i>
                                                <?php endif; ?>
                                            </a>
                                        </th>
                                        <th scope="col">
                                            <a href="?sort=category&dir=<?= $sortColumn == 'category' && $sortDir == 'ASC' ? 'DESC' : 'ASC' ?>">
                                                Category
                                                <?php if ($sortColumn == 'category'): ?>
                                                    <i class="bi bi-caret-<?= $sortDir == 'ASC' ? 'up' : 'down' ?>"></i>
                                                <?php endif; ?>
                                            </a>
                                        </th>
                                        <th scope="col">
                                            <a href="?sort=price&dir=<?= $sortColumn == 'price' && $sortDir == 'ASC' ? 'DESC' : 'ASC' ?>">
                                                Price
                                                <?php if ($sortColumn == 'price'): ?>
                                                    <i class="bi bi-caret-<?= $sortDir == 'ASC' ? 'up' : 'down' ?>"></i>
                                                <?php endif; ?>
                                            </a>
                                        </th>
                                        <th scope="col">
                                            <a href="?sort=stock&dir=<?= $sortColumn == 'stock' && $sortDir == 'ASC' ? 'DESC' : 'ASC' ?>">
                                                Stock
                                                <?php if ($sortColumn == 'stock'): ?>
                                                    <i class="bi bi-caret-<?= $sortDir == 'ASC' ? 'up' : 'down' ?>"></i>
                                                <?php endif; ?>
                                            </a>
                                        </th>
                                        <th scope="col">
                                            <a href="?sort=status&dir=<?= $sortColumn == 'status' && $sortDir == 'ASC' ? 'DESC' : 'ASC' ?>">
                                                Status
                                                <?php if ($sortColumn == 'status'): ?>
                                                    <i class="bi bi-caret-<?= $sortDir == 'ASC' ? 'up' : 'down' ?>"></i>
                                                <?php endif; ?>
                                            </a>
                                        </th>
                                        <th scope="col">
                                            <a href="?sort=supplier&dir=<?= $sortColumn == 'supplier' && $sortDir == 'ASC' ? 'DESC' : 'ASC' ?>">
                                                Supplier
                                                <?php if ($sortColumn == 'supplier'): ?>
                                                    <i class="bi bi-caret-<?= $sortDir == 'ASC' ? 'up' : 'down' ?>"></i>
                                                <?php endif; ?>
                                            </a>
                                        </th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $startIndex = ($currentPage - 1) * 10 + 1;
                                    $product_count = $startIndex - 1;

                                    if (count($products) > 0) {
                                        foreach ($products as $product) {
                                            $productId = $product[0];
                                            $productName = $product[1];
                                            $brandId = $product[5]; 
                                            $categoryId = $product[6];
                                            $supplierId = $product[7];
                                            $price = $product[2];
                                            $stock = $product[3];
                                            $status = $product[4];

                                            $brands = $showBrand->showRecords("brand_id = $brandId");
                                            $categories = $showCategory->showRecords("category_id = $categoryId");
                                            $suppliers = $showSupplier->showRecords("supplier_id = $supplierId");

                                            echo "<tr>";
                                            echo "<th scope='row'>" . ++$product_count . "</th>";
                                            echo "<td>" . htmlspecialchars($productName) . "</td>";

                                            echo "<td>";
                                            if (!empty($brands)) {
                                                foreach ($brands as $brand) {
                                                    echo htmlspecialchars($brand[1]);
                                                }
                                            } else {
                                                echo "No Brand Found";
                                            }
                                            echo "</td>";

                                            echo "<td>";
                                            if (!empty($categories)) {
                                                foreach ($categories as $category) {
                                                    echo htmlspecialchars($category[1]);
                                                }
                                            } else {
                                                echo "No Category Found";
                                            }
                                            echo "</td>";


                                            echo "<td>" . number_format($price, 2) . "</td>";
                                            echo "<td>" . (int)$stock . "</td>";
                                            
                                            echo "<td>";
                                            switch (htmlspecialchars($status)) {
                                                case 'Out of Stock':
                                                    echo "<span class='badge bg-danger text-white'>Out of Stock</span>";
                                                    break;
                                                case 'Low Stock':
                                                    echo "<span class='badge bg-warning text-dark'>Low Stock</span>";
                                                    break;
                                                default:
                                                    echo "<span class='badge bg-success text-white'>Available</span>";
                                                    break;
                                            }
                                            echo "</td>";

                                            echo "<td>";
                                            if (!empty($suppliers)) {
                                                foreach ($suppliers as $supplier) {
                                                    echo htmlspecialchars($supplier[1]);
                                                }
                                            } else {
                                                echo "No Supplier Found";
                                            }
                                            echo "</td>";

                                            echo "<td>
                                                    <a class='btn btn-warning' href='forms/productEdit.php?id=$productId'><i class='bi bi-pencil-square'></i></a>
                                                    <a class='btn btn-danger' href='forms/productDelete.php?id=$productId'><i class='bi bi-trash'></i></a>
                                                </td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='9'><div class='alert alert-dark' role='alert'>No record Found</div></td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            <nav class="d-flex justify-content-center" aria-label="Page navigation example">
                                <ul class="pagination">
                                    <?php if ($currentPage > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= ($currentPage - 1) ?>">Previous</a>
                                        </li>
                                    <?php endif; ?>
                                    <?php for ($page = 1; $page <= $paginationData['totalPages']; $page++): ?>
                                        <li class="page-item <?= ($page == $currentPage) ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $page ?>"><?= $page ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <?php if ($currentPage < $paginationData['totalPages']): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= ($currentPage + 1) ?>">Next</a>
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

    <!-- SweetAlert Trigger -->
    <script src="js/script.js"></script>
    <script>
        <?php
        if (isset($_SESSION['message'])) {
            echo "Swal.fire({
                title: 'Notification',
                text: '" . addslashes($_SESSION['message']) . "',
                icon: 'success',
                confirmButtonText: 'OK'
            });";
            unset($_SESSION['message']);
        }
        ?>
    </script>
</body>
</html>
