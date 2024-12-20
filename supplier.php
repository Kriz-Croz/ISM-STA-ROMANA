<?php
require 'db/connection.php';
require 'db/show.php';

session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

$show = new Show($conn, 'supplier');
$showProduct = new Show($conn, 'product');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Management</title>
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
                        <h4>Supplier Management</h4>
                    </div>
                    
                    <div class="card border-0">
                        <div class="card-header d-flex justify-content-between">
                            <h5 class="card-title">
                                Suppliers
                            </h5>
                            <a href="forms/supplierAdd.php" class="btn btn-success">Add New Supplier</a>
                        </div>
                        
                        <?php
                        $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
                        $paginationData = $show->showRecordsWithPagination($currentPage, null, 10, null);
                        $suppliers = $paginationData['records'];
                        ?>
                        <div class="card-body table-responsive container">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Phone Number</th>
                                        <th scope="col">Product Count</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $startIndex = ($currentPage - 1) * 10 + 1;
                                    $supplier_count = $startIndex - 1;

                                    if (count($suppliers) > 0) {
                                        foreach ($suppliers as $supplier) {
                                            $supplierId = $supplier[0];
                                            $supplierName = $supplier[1];
                                            $supplierNumber = $supplier[2]; 
                                            
                                            $productCount = $showProduct->showRecords("supplier_id = $supplierId");
                                            $productCount = count($productCount);

                                            echo "<tr>";
                                            echo "<th scope='row'>" . ++$supplier_count . "</th>";
                                            echo "<td>" . htmlspecialchars($supplierName) . "</td>";
                                            echo "<td>" . htmlspecialchars($supplierNumber) . "</td>";
                                            echo "<td>" . $productCount . "</td>";
                                            echo "<td>
                                                    <a class='btn btn-warning' href='forms/supplierEdit.php?id=$supplierId'><i class='bi bi-pencil-square'></i></a>
                                                    <a class='btn btn-danger delete-btn' href='forms/supplierDelete.php?id=$supplierId'><i class='bi bi-trash'></i></a>
                                                </td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5'><div class='alert alert-dark' role='alert'>No record Found</div></td></tr>";
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
