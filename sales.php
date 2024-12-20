<?php
require 'db/connection.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

$sales = isset($_SESSION['sale']) ? $_SESSION['sale'] : [];
$total = 0;
$totalProductsInList = count($sales); 

foreach ($sales as $productId => $sale) {
    $product = $conn->query("SELECT * FROM product WHERE product_id = $productId")->fetch_assoc();
    if (!$product) {
        unset($sales[$productId]); 
    }
}
$_SESSION['sale'] = $sales;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <script src="bootstrap/js/bootstrap.bundle.js"></script>
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/style.css">

    <!-- SweetAlert2 CDN -->
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
                    <h4>Sales Management</h4>
                </div>
                <div class="card border-0">
                    <div class="card-header">
                        <h5 class="card-title">Sales Records</h5>
                    </div>
                    <div class="card-body table-responsive container">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="salesTable">
                                <?php
                                if (count($sales) > 0) {
                                    $index = 0;
                                    foreach ($sales as $productId => $sale) {
                                        $product = $conn->query("SELECT * FROM product WHERE product_id = $productId")->fetch_assoc();
                                        if (!$product) continue;

                                        $index++;
                                        $quantity = $sale['quantity'];
                                        $price = $product['price'];
                                        $amount = $quantity * $price;
                                        $total += $amount;

                                        echo "<tr id='row-$productId'>";
                                        echo "<td>$index</td>";
                                        echo "<td>{$product['product_name']}</td>";
                                        echo "<td>
                                            <input type='number' id='quantity-$productId' value='$quantity' class='form-control' min='1'>
                                        </td>";
                                        echo "<td>₱" . number_format($price, 2) . "</td>";
                                        echo "<td id='amount-$productId'>₱" . number_format($amount, 2) . "</td>";
                                        echo "<td>
                                                <button class='btn btn-primary update-btn' data-product-id='$productId'>Update</button>
                                                <button class='btn btn-danger delete-btn' data-product-id='$productId'>Delete</button>
                                            </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>No sale records found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <div class="mt-3 d-flex justify-content-between">
                            <h5>Total Products: <span id="totalProductsInList"><?= $totalProductsInList ?></span></h5>
                            <h5>Total: ₱<span id="total"><?= number_format($total, 2) ?></span></h5>
                            <button class="btn btn-success" id="generateSaleBtn">Generate Sale</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- JavaScript for AJAX -->
<script src="js/script.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const totalElement = document.getElementById('total');
    const totalProductsInListElement = document.getElementById('totalProductsInList');

    document.querySelectorAll('.update-btn').forEach(button => {
        button.addEventListener('click', function () {
            const productId = this.dataset.productId;
            const newQuantity = parseInt(document.getElementById(`quantity-${productId}`).value, 10);

            fetch(`db/getProductStock.php?productId=${productId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const availableStock = data.stock;

                        if (newQuantity > availableStock) {
                            Swal.fire('Error!', 'Insufficient stock for this update.', 'error');
                            return;
                        }

                        fetch('db/salesUpdate.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ productId, newQuantity })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    document.getElementById(`amount-${productId}`).textContent = `₱${data.amount}`;
                                    totalElement.textContent = data.total;
                                    totalProductsInListElement.textContent = data.totalProductsInList;
                                    Swal.fire('Success!', 'The quantity has been updated.', 'success');
                                } else {
                                    Swal.fire('Error!', data.message, 'error');
                                }
                            })
                            .catch(error => Swal.fire('Error!', 'Something went wrong.', 'error'));
                    } else {
                        Swal.fire('Error!', 'Unable to retrieve stock information.', 'error');
                    }
                })
                .catch(error => Swal.fire('Error!', 'Something went wrong.', 'error'));
        });
    });

    // Handle Delete Button Click
document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function () {
        const productId = this.dataset.productId;

        Swal.fire({
            title: 'Are you sure?',
            text: 'You want to delete this product from the sale?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('db/salesDelete.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ productId })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const row = document.getElementById(`row-${productId}`);
                            row.remove();

                            document.getElementById('total').textContent = `₱${data.total}`;
                            document.getElementById('totalProductsInList').textContent = data.totalProductsInList;

                            const tableBody = document.getElementById('salesTable');
                            if (tableBody.children.length === 0) {
                                tableBody.innerHTML = `
                                    <tr>
                                        <td colspan="6" class="text-center">No sale records found</td>
                                    </tr>`;
                            }

                            Swal.fire('Deleted!', 'The product has been removed from the sale.', 'success');
                        } else {
                            Swal.fire('Error!', data.message, 'error');
                        }
                    })
                    .catch(error => Swal.fire('Error!', 'Something went wrong.', 'error'));
            }
        });
    });
});

document.getElementById('generateSaleBtn').addEventListener('click', function() {
    const sales = <?php echo json_encode($sales); ?>; 

    if (Object.keys(sales).length === 0) {
        Swal.fire('Error!', 'No products in the sale.', 'error');
        return;
    }

    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to generate the sale with the current items?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, generate sale!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const saleData = {
                sales: sales,
                total: document.getElementById('total').textContent.replace('₱', '').replace(',', ''), 
            };

            fetch('db/generateSale.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(saleData),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Sale generated successfully.',
                        showConfirmButton: true,  
                        timerProgressBar: true 
                    }).then(() => {
                        const tableBody = document.getElementById('salesTable');
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="6" class="text-center">No sale records found</td>
                            </tr>
                        `;
                        
                        document.getElementById('total').textContent = '₱0.00';
                        document.getElementById('totalProductsInList').textContent = '0';
                    });
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Something went wrong.', 'error');
            });
        }
    });
});



});
</script>
</body>
</html>
