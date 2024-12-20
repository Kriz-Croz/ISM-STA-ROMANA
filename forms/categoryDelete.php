<?php
require '../db/connection.php';
require '../db/show.php';
require '../db/delete.php';

session_start(); // Ensure session is started

// Get category ID from the URL
$id = $_GET['id'] ?? NULL;
if (!isset($_GET['id'])) {
    header("Location: ../category.php");
    exit;
}

// Initialize Delete and Show classes
$delete = new Delete($conn, 'category', ["category_id" => $id]);
$show = new Show($conn, 'category');

// Fetch category details
$data = $show->showRecords("category_id = $id");

if (isset($_POST['delete'])) {
    // Delete the category from the database
    try {
        $action = $delete->deleteQuery();
        $_SESSION['message'] = "Category deleted successfully.";  // Set session message before redirect
        header("Location: ../category.php");
        exit;
    } catch (Exception $e) {
        echo "<div class='alert alert-danger' role='alert'>Error: $e</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Category</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #ced4da;">

    <div class="d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-sm" style="width: 100%; max-width: 500px;">
            <div class="card-body text-center">
                <h3 class="card-title mb-4">Delete Category</h3>

                <form action="" method="post">
                    <div class="mb-4">
                        <p class="lead">Are you sure you want to delete the category "<strong><?php echo htmlspecialchars($data[0][1]); ?></strong>"?</p>
                    </div>

                    <div class="d-flex justify-content-center gap-3">
                        <input type="submit" name="delete" value="Yes, delete" class="btn btn-danger">
                        <a href="../category.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
