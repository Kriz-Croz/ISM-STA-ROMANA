<?php
session_start(); // Start the session

require '../db/connection.php';
require '../db/show.php';
require '../db/add.php';

$add = new Add($conn, 'supplier');

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

    // Check if the supplier already exists in the database
    $supplierName = $data['supplier_name'];
    $supplierContact = $data['supplier_number'];

    $stmt = $conn->prepare("SELECT COUNT(*) FROM supplier WHERE supplier_name = ? OR supplier_number = ?");
    $stmt->bind_param("ss", $supplierName, $supplierContact);
    $stmt->execute();
    $stmt->store_result();  // Make sure the result is fully retrieved before proceeding
    $stmt->bind_result($supplierCount);
    $stmt->fetch();
    $stmt->close();  // Close the statement after fetching results

    if ($supplierCount > 0) {
        // If the supplier exists, set an error message in session
        $_SESSION['errorMessage'] = "The supplier '$supplierName' or contact '$supplierContact' already exists!";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    try {
        // Add supplier to the database using prepared statement
        $action = $add->addQuery($data);
        
        // Set the success message in session
        $_SESSION['successMessage'] = "New Supplier Added Successfully";
        
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
    <title>Add Supplier</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <script src="../bootstrap/js/bootstrap.bundle.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert Library -->
    <script>
        // Show SweetAlert if the success or error message is set
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
        <h2 class="text-center">Add New Supplier</h2>
        <div class="card">
            <div class="card-body">
                <form action="" method="post">
                    <div class="mb-3">
                        <label for="supplier_name" class="form-label">Supplier Name</label>
                        <input type="text" name="supplier_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="supplier_contact" class="form-label">Supplier Contact</label>
                        <input type="text" name="supplier_number" class="form-control" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="../supplier.php" class="btn btn-secondary">Back</a>
                        <button type="submit" name="add" class="btn btn-primary">Add Supplier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
