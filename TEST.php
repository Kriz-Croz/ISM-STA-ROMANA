<?php
require 'db/connection.php';
require 'db/show.php';

session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <script src="bootstrap/js/bootstrap.bundle.js"></script>
    <link rel="stylesheet" href="bootstrap-icons-1.11.3/font/bootstrap-icons.css">
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="wrapper">
    <?php include 'sidebar.php' ?>

    <div class="main">
        <nav class="navbar navbar-expand px-3 border-bottom">
            <button class="btn" id="sidebar-toggle" type="button">
                <span class="navbar-toggler-icon"></span>
            </button>
        </nav>
        <main class="content px-3 py-2">
            <div class="container-fluid">
                <div class="mb-3">
                    <h4>Product Details</h4>
                </div>

                <!-- Table Element -->
                <div class="card border-0">
                    <div class="card-header d-flex justify-content-around">
                        <h5 class="card-title">Product Information</h5>
                        <!-- Modal Trigger -->
                        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">Add</a>
                    </div>
                    <?php
                    $show = new Show($conn, 'product');
                    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
                    $paginationData = $show->showRecordsWithPagination($currentPage, null, 10, null);
                    $products = $paginationData['records'];

                    $showCategory = new Show($conn, 'category');
                    $showBrand = new Show($conn, 'brand');
                    $showSupplier = new Show($conn, 'supplier');

                    $brands = $showBrand->showRecords();
                    $categories = $showCategory->showRecords();
                    $suppliers = $showSupplier->showRecords();
                    ?>
                    <div class="card-body table-responsive container">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Brand</th>
                                <th scope="col">Category</th>
                                <th scope="col">Price</th>
                                <th scope="col">Stock</th>
                                <th scope="col">Status</th>
                                <th scope="col">Supplier</th>
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

                                    echo "<td>" . (!empty($brands) ? htmlspecialchars($brands[0][1]) : "No Brand Found") . "</td>";
                                    echo "<td>" . (!empty($categories) ? htmlspecialchars($categories[0][1]) : "No Category Found") . "</td>";
                                    echo "<td>" . number_format($price, 2) . "</td>";
                                    echo "<td>" . (int)$stock . "</td>";
                                    echo "<td>" . htmlspecialchars($status) . "</td>";
                                    echo "<td>" . (!empty($suppliers) ? htmlspecialchars($suppliers[0][1]) : "No Supplier Found") . "</td>";
                                    echo "<td>
                                            <a class='btn btn-primary' href='forms/productEdit.php?id=$productId'><i class='bi bi-pencil-square'></i></a>
                                            <a class='btn btn-warning' href='forms/productDelete.php?id=$productId'><i class='bi bi-trash'></i></a>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9'><div class='alert alert-dark' role='alert'>No record Found</div></td></tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                        <nav class="d-flex justify-content-center" aria-label="Page navigation example">
                            <ul class="pagination">
                                <?php if ($currentPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= ($currentPage - 1) ?>">Previous</a>
                                    </li>
                                <?php endif; ?>
                                <?php for ($page = 1; $page <= $paginationData['totalPages']; $page++): ?>
                                    <li class="page-item"><a class="page-link" href="?page=<?= $page ?>"><?= $page ?></a></li>
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

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addProductForm">
                    <div class="mb-3">
                        <label for="productName" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="productName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="productBrand" class="form-label">Brand</label>
                        <select class="form-select" id="productBrand" name="brand_id" required>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?= $brand[0] ?>"><?= htmlspecialchars($brand[1]) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="productCategory" class="form-label">Category</label>
                        <select class="form-select" id="productCategory" name="category_id" required>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category[0] ?>"><?= htmlspecialchars($category[1]) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="productPrice" class="form-label">Price</label>
                        <input type="number" class="form-control" id="productPrice" name="price" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="productStock" class="form-label">Stock</label>
                        <input type="number" class="form-control" id="productStock" name="stock" required>
                    </div>
                    <div class="mb-3">
                        <label for="productStatus" class="form-label">Status</label>
                        <select class="form-select" id="productStatus" name="status" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="productSupplier" class="form-label">Supplier</label>
                        <select class="form-select" id="productSupplier" name="supplier_id" required>
                            <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?= $supplier[0] ?>"><?= htmlspecialchars($supplier[1]) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="js/script.js"></script>
<script>
    document.getElementById('addProductForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        fetch('forms/productAddHandler.php', {
            method: 'POST',
            body: formData,
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product added successfully!');
                    location.reload();
                } else {
                    alert('Failed to add product: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred.');
            });
    });
</script>
</body>
</html>
