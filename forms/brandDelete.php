<?php
require '../db/connection.php';
require '../db/show.php';
require '../db/delete.php';

// Start the session to store messages
session_start();

$id = $_GET['id'] ?? NULL;

if (!isset($id)) {
    $_SESSION['message'] = "No brand selected for deletion.";
    $_SESSION['message_type'] = 'error'; // Error message type
    header("Location: ../brand.php");
    exit();
}

$show = new Show($conn, 'brand');
$delete = new Delete($conn, 'brand', ['brand_id' => $id]);

// Fetch brand details for confirmation
$data = $show->showRecords("brand_id = $id");

if (isset($_POST['delete'])) {
    try {
        $delete->deleteQuery();
        $_SESSION['message'] = "Brand deleted successfully!"; // Success message
        $_SESSION['message_type'] = 'success'; // Success message type
        header("Location: ../brand.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['message'] = "Error deleting brand: " . $e->getMessage();
        $_SESSION['message_type'] = 'error'; // Error message type
        header("Location: ../brand.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Brand</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #ced4da;">
    <div class="d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-sm" style="width: 100%; max-width: 500px;">
            <div class="card-body text-center">
                <h3 class="card-title mb-4">Delete Brand</h3>
                <form action="" method="post">
                    <div class="mb-4">
                        <p class="lead">
                            Are you sure you want to delete the brand 
                            "<strong><?= htmlspecialchars($data[0][1]); ?></strong>"?
                        </p>
                    </div>
                    <div class="d-flex justify-content-center gap-3">
                        <input type="submit" name="delete" value="Yes, delete" class="btn btn-danger">
                        <a href="../brand.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
