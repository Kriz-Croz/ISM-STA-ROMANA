<?php
session_start(); // Start the session

require '../db/connection.php';
require '../db/show.php';
require '../db/add.php';

$add = new Add($conn, 'brand'); // Change table to 'brand'
$showBrand = new Show($conn, 'brand');

// Check if success or error message is set in session
$successMessage = isset($_SESSION['successMessage']) ? $_SESSION['successMessage'] : "";
$errorMessage = isset($_SESSION['errorMessage']) ? $_SESSION['errorMessage'] : "";

if ($successMessage) {
    unset($_SESSION['successMessage']);
}

if ($errorMessage) {
    unset($_SESSION['errorMessage']);
}

if (isset($_POST['add'])) {
    $data = [];
    foreach ($_POST as $name => $value) {
        if ($name != "add") {
            $data[$name] = $value;
        }
    }

    // Check if the brand already exists in the database
    $brandName = $data['brand_name'];
    $stmt = $conn->prepare("SELECT COUNT(*) FROM brand WHERE brand_name = ?");
    $stmt->bind_param("s", $brandName);
    $stmt->execute();
    $stmt->store_result();  // Make sure the result is fully retrieved before proceeding
    $stmt->bind_result($brandCount);
    $stmt->fetch();
    $stmt->close();  // Close the statement after fetching results

    if ($brandCount > 0) {
        // If the brand exists, set an error message in session
        $_SESSION['errorMessage'] = "The brand '$brandName' already exists!";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    try {
        // Add brand to the database using prepared statement
        $action = $add->addQuery($data);
        
        // Set the success message in session
        $_SESSION['successMessage'] = "New Brand Added Successfully";
        
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
    <title>Add Brand</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <script src="../bootstrap/js/bootstrap.bundle.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert Library -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const successMessage = <?= json_encode($successMessage); ?>;
        const errorMessage = <?= json_encode($errorMessage); ?>;

        // If success message exists, show success alert
        if (successMessage) {
            Swal.fire({
                title: 'Success!',
                text: successMessage,
                icon: 'success',
                confirmButtonText: 'OK'
            });
        }
        // If error message exists, show error alert
        else if (errorMessage) {
            Swal.fire({
                title: 'Error!',
                text: errorMessage,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    });
    </script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Add New Brand</h2>
        <div class="card">
            <div class="card-body">
                <form action="" method="post">
                    <div class="mb-3">
                        <label for="brand_name" class="form-label">Brand Name</label>
                        <input type="text" name="brand_name" class="form-control" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="../brand.php" class="btn btn-secondary">Back</a>
                        <button type="submit" name="add" class="btn btn-primary">Add Brand</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
