<?php
session_start(); // Start the session

require '../db/connection.php';
require '../db/show.php';
require '../db/add.php';

$add = new Add($conn, 'category');  // Change table to 'category'
$showCategory = new Show($conn, 'category');

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

    // Check if the category already exists in the database
    $categoryName = $data['category_name'];
    $stmt = $conn->prepare("SELECT COUNT(*) FROM category WHERE category_name = ?");
    $stmt->bind_param("s", $categoryName);
    $stmt->execute();
    $stmt->store_result();  // Make sure the result is fully retrieved before proceeding
    $stmt->bind_result($categoryCount);
    $stmt->fetch();
    $stmt->close();  // Close the statement after fetching results

    if ($categoryCount > 0) {
        // If the category exists, set an error message in session
        $_SESSION['errorMessage'] = "The category '$categoryName' already exists!";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    try {
        // Add category to the database using prepared statement
        $action = $add->addQuery($data);
        
        // Set the success message in session
        $_SESSION['successMessage'] = "New Category Added Successfully";
        
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
    <title>Add Category</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <script src="../bootstrap/js/bootstrap.bundle.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert Library -->
    <script>
        // Show SweetAlert if the success message is set
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
        <h2 class="text-center">Add New Category</h2>
        <div class="card">
            <div class="card-body">
                <form action="" method="post">
                    <div class="mb-3">
                        <label for="category_name" class="form-label">Category Name</label>
                        <input type="text" name="category_name" class="form-control" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="../category.php" class="btn btn-secondary">Back</a>
                        <button type="submit" name="add" class="btn btn-primary">Add Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
