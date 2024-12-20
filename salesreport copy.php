<?php
require 'db/connection.php';

$timestamp = date('Y-m-d H:i:s');

$searchDate = isset($_GET['search_date']) ? $_GET['search_date'] : null;
$searchMonth = isset($_GET['search_month']) ? $_GET['search_month'] : null;
$searchYear = isset($_GET['search_year']) ? $_GET['search_year'] : null;

$sql = "SELECT * FROM sales_records WHERE sale_date >= ? ";
$params = [$timestamp];

if ($searchDate) {
    $sql .= "AND DATE(sale_date) = ? ";
    $params[] = $searchDate;
} elseif ($searchMonth && $searchYear) {
    $sql .= "AND YEAR(sale_date) = ? AND MONTH(sale_date) = ? ";
    $params[] = $searchYear;
    $params[] = $searchMonth;
} elseif ($searchYear) {
    $sql .= "AND YEAR(sale_date) = ? ";
    $params[] = $searchYear;
} elseif ($searchMonth) {
    $sql .= "AND MONTH(sale_date) = ? ";
    $params[] = $searchMonth;
}

$sql .= "ORDER BY sale_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat('s', count($params)), ...$params); 

$stmt->execute();
$result = $stmt->get_result();
$salesData = $result->fetch_all(MYSQLI_ASSOC);

$noRecordsFound = empty($salesData);

$groupedSalesData = [];
foreach ($salesData as $sale) {
    $saleDateTime = date('Y-m-d H:i:s', strtotime($sale['sale_date']));
    $groupedSalesData[$saleDateTime][] = $sale;
}

function calculateTotals($salesGroup) {
    $uniqueProducts = [];
    $totals = [
        'totalProducts' => 0,
        'totalAmount' => 0,
    ];

    foreach ($salesGroup as $sale) {
        $totals['totalAmount'] += $sale['amount'];
        if (!in_array($sale['product_id'], $uniqueProducts)) {
            $uniqueProducts[] = $sale['product_id'];
        }
    }

    $totals['totalProducts'] = count($uniqueProducts);

    return $totals;
}

$totalGroups = count($groupedSalesData);
$groupsPerPage = 1; 
$totalPages = ceil($totalGroups / $groupsPerPage);

$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $groupsPerPage;

$pagedSalesData = array_slice($groupedSalesData, $offset, $groupsPerPage);
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Order</title>
    <!-- Bootstrap link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <script src="bootstrap/js/bootstrap.bundle.js"></script>
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body data-bs-theme="light">
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
                        <h4>Sales Report</h4>
                        <!-- Search Form -->
                        <form method="GET" class="mb-4">
                            <div class="input-group">
                                <input type="date" class="form-control" name="search_date" value="<?php echo $searchDate; ?>" placeholder="Search by Date">
                                <select class="form-control" name="search_month">
                                    <option value="">Select Month</option>
                                    <option value="01" <?php echo $searchMonth == '01' ? 'selected' : ''; ?>>January</option>
                                    <option value="02" <?php echo $searchMonth == '02' ? 'selected' : ''; ?>>February</option>
                                    <option value="03" <?php echo $searchMonth == '03' ? 'selected' : ''; ?>>March</option>
                                    <option value="04" <?php echo $searchMonth == '04' ? 'selected' : ''; ?>>April</option>
                                    <option value="05" <?php echo $searchMonth == '05' ? 'selected' : ''; ?>>May</option>
                                    <option value="06" <?php echo $searchMonth == '06' ? 'selected' : ''; ?>>June</option>
                                    <option value="07" <?php echo $searchMonth == '07' ? 'selected' : ''; ?>>July</option>
                                    <option value="08" <?php echo $searchMonth == '08' ? 'selected' : ''; ?>>August</option>
                                    <option value="09" <?php echo $searchMonth == '09' ? 'selected' : ''; ?>>September</option>
                                    <option value="10" <?php echo $searchMonth == '10' ? 'selected' : ''; ?>>October</option>
                                    <option value="11" <?php echo $searchMonth == '11' ? 'selected' : ''; ?>>November</option>
                                    <option value="12" <?php echo $searchMonth == '12' ? 'selected' : ''; ?>>December</option>
                                </select>
                                <select class="form-control" name="search_year">
                                    <option value="">Select Year</option>
                                    <option value="2024" <?php echo $searchYear == '2024' ? 'selected' : ''; ?>>2024</option>
                                    <option value="2023" <?php echo $searchYear == '2023' ? 'selected' : ''; ?>>2023</option>
                                    <option value="2022" <?php echo $searchYear == '2022' ? 'selected' : ''; ?>>2022</option>
                                </select>
                                <button class="btn btn-primary" type="submit">Search</button>
                            </div>
                        </form>
                    </div>

                    <?php if ($noRecordsFound): ?>
                        <div class="alert alert-warning">
                            No records found for the selected search criteria.
                        </div>
                    <?php endif; ?>

                    <?php foreach ($pagedSalesData as $saleDateTime => $salesGroup): ?>
                        <?php $totals = calculateTotals($salesGroup); ?>
                        <div class="row mb-4">
                            <div class="card card-1">
                                <div class="card-body">
                                    <h5>Sales on <?php echo $saleDateTime; ?></h5>
                                    <div class="card-body table-responsive container">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Product</th>
                                                    <th scope="col">Price per Item</th>
                                                    <th scope="col">Quantity</th>
                                                    <th scope="col">Total Price</th>
                                                    <th scope="col">Brand</th>
                                                    <th scope="col">Category</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($salesGroup as $index => $sale): ?>
                                                    <?php
                                                    $product = $conn->query("
                                                        SELECT 
                                                            product.product_name, 
                                                            product.price, 
                                                            brand.brand_name, 
                                                            category.category_name 
                                                        FROM product 
                                                        LEFT JOIN brand ON product.brand_id = brand.brand_id 
                                                        LEFT JOIN category ON product.category_id = category.category_id 
                                                        WHERE product.product_id = {$sale['product_id']}"
                                                    )->fetch_assoc();
                                                    ?>
                                                    <tr>
                                                        <th scope='row'><?php echo $index + 1; ?></th>
                                                        <td><?php echo $product['product_name']; ?></td>
                                                        <td>₱<?php echo number_format($product['price'], 2); ?></td>
                                                        <td><?php echo $sale['quantity']; ?></td>
                                                        <td>₱<?php echo number_format($sale['amount'], 2); ?></td>
                                                        <td><?php echo $product['brand_name']; ?></td>
                                                        <td><?php echo $product['category_name']; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-6">
                                            <h5>Total Unique Products: <?php echo $totals['totalProducts']; ?></h5>
                                        </div>
                                        <div class="col-6 text-end">
                                            <h5>Total Amount: ₱<?php echo number_format($totals['totalAmount'], 2); ?></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="pagination-container text-center">
                        <nav aria-label="Page navigation">
                            <ul class="pagination">
                                <?php if ($currentPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>&search_date=<?php echo $searchDate; ?>&search_month=<?php echo $searchMonth; ?>&search_year=<?php echo $searchYear; ?>" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                                    <li class="page-item <?php echo ($page == $currentPage) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page; ?>&search_date=<?php echo $searchDate; ?>&search_month=<?php echo $searchMonth; ?>&search_year=<?php echo $searchYear; ?>"><?php echo $page; ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($currentPage < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>&search_date=<?php echo $searchDate; ?>&search_month=<?php echo $searchMonth; ?>&search_year=<?php echo $searchYear; ?>" aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
