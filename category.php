<?php
require 'db/connection.php';
require 'db/show.php';

session_start(); // Ensure session is started
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management</title>
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
                        <h4>Category Management</h4>
                    </div>

                    <!-- Table Element -->
                    <div class="card border-0">
                        <div class="card-header d-flex justify-content-between">
                            <h5 class="card-title">Categories</h5>
                            <a href="forms/categoryAdd.php" class="btn btn-success">Add New Category</a>
                        </div>
                        <?php
                        $showCategory = new Show($conn, 'category');
                        $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
                        $paginationData = $showCategory->showRecordsWithPagination($currentPage, null, 10, null); // Implement pagination in this method
                        $categories = $paginationData['records'];

                        $showProduct = new Show($conn, 'product');
                        ?>
                        <div class="card-body table-responsive container">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Category Name</th>
                                        <th scope="col">Product Count</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (count($categories) > 0) {
                                        $category_count = ($currentPage - 1) * 10 + 1;
                                        foreach ($categories as $category) {
                                            $categoryId = $category[0];
                                            $categoryName = $category[1];

                                            $productCount = $showProduct->showRecords("category_id = $categoryId");
                                            $productCount = count($productCount);

                                            echo "<tr>";
                                            echo "<th scope='row'>" . $category_count++ . "</th>";
                                            echo "<td>" . htmlspecialchars($categoryName) . "</td>";
                                            echo "<td>" . $productCount . "</td>";

                                            echo "<td>
                                                    <a class='btn btn-warning' href='forms/categoryEdit.php?id=$categoryId'><i class='bi bi-pencil-square'></i></a>
                                                    <a class='btn btn-danger' href='forms/categoryDelete.php?id=$categoryId'><i class='bi bi-trash'></i></a>
                                                </td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4'><div class='alert alert-dark' role='alert'>No Categories Found</div></td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>

                            <!-- Pagination -->
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
