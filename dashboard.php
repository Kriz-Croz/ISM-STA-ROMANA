<?php
require 'db/connection.php';

session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en" >
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <script src="bootstrap/js/bootstrap.bundle.js"></script>
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
                        <h4>Admin Dashboard</h4>
                    </div>
                    <div class="row">
                        <div class="col col-md d-flex">
                            <div class="card flex-fill border-0 illustration">
                                <div class="card-body p-0 d-flex flex-fill">
                                    <div class="row g-0 w-100">
                                        <div class="col-6">
                                            <div class="p-3 m-1">
                                                <h4>Welcome Back, Admin</h4>
                                            </div>
                                        </div>
                                        <div class="col-6 align-self-end text-end">
                                            <img src="../image/customer-support.jpg" class="img-fluid illustration-img"
                                                alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6 d-flex">
                            <div class="card flex-fill border-0">
                                <div class="card-body py-4">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-grow-1">
                                            <h4 class="mb-2">
                                                <?php
                                                    $query = "SELECT COUNT(*) AS total FROM product";
                                                    $result = $conn->query($query);
                                                    $row = $result->fetch_assoc();
                                                    echo $row['total'];
                                                ?>
                                            </h4>
                                            <p class="mb-2">
                                                Products
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 d-flex">
                            <div class="card flex-fill border-0">
                                <div class="card-body py-4">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-grow-1">
                                            <h4 class="mb-2">
                                                <?php
                                                    $query = "SELECT COUNT(*) AS total FROM brand";
                                                    $result = mysqli_query($conn, $query);
                                                    $row = $result->fetch_assoc();
                                                    echo $row['total'];
                                                ?>
                                            </h4>
                                            <p class="mb-2">
                                               Brands
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6 d-flex">
                            <div class="card flex-fill border-0">
                                <div class="card-body py-4">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-grow-1">
                                            <h4 class="mb-2">
                                                <?php
                                                    $query = "SELECT COUNT(*) AS total FROM category";
                                                    $result = $conn->query($query);
                                                    $row = $result->fetch_assoc();
                                                    echo $row['total'];
                                                ?>
                                            </h4>
                                            <p class="mb-2">
                                                Categories
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 d-flex">
                            <div class="card flex-fill border-0">
                                <div class="card-body py-4">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-grow-1">
                                            <h4 class="mb-2">
                                                <?php
                                                    $query = "SELECT COUNT(*) AS total FROM supplier";
                                                    $result = mysqli_query($conn, $query);
                                                    $row = $result->fetch_assoc();
                                                    echo $row['total'];
                                                ?>
                                            </h4>
                                            <p class="mb-2">
                                               Suppliers
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
