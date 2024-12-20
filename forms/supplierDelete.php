<?php
require '../db/connection.php';
require '../db/show.php';
require '../db/delete.php';

// Start the session to store messages
session_start();

// Get supplier ID from the URL
$id = $_GET['id'] ?? NULL;
if (!isset($_GET['id'])) {
    $_SESSION['message'] = "No supplier selected for deletion.";
    $_SESSION['message_type'] = 'error'; // Error message type
    header("Location: ../supplier.php");
    exit;
}

// Initialize Delete and Show classes
$delete = new Delete($conn, 'supplier', ["supplier_id" => $id]);
$show = new Show($conn, 'supplier');

// Fetch supplier details
$data = $show->showRecords("supplier_id = $id");

if (isset($_POST['delete'])) {
    try {
        // Attempt to delete the supplier
        $delete->deleteQuery();
        $_SESSION['message'] = "Supplier deleted successfully!";
        $_SESSION['message_type'] = 'success'; // Success message type
        header("Location: ../supplier.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['message'] = "Error deleting supplier: " . $e->getMessage();
        $_SESSION['message_type'] = 'error'; // Error message type
        header("Location: ../supplier.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Supplier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body style="background-color: #ced4da;">

    <div class="d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-sm" style="width: 100%; max-width: 500px;">
            <div class="card-body text-center">
                <h3 class="card-title mb-4">Delete Supplier</h3>
                <form action="" method="post">
                    <div class="mb-4">
                        <p class="lead">
                            Are you sure you want to delete the supplier 
                            "<strong><?= htmlspecialchars($data[0][1]); ?></strong>"?
                        </p>
                    </div>
                    <div class="d-flex justify-content-center gap-3">
                        <input type="submit" name="delete" value="Yes, delete" class="btn btn-danger">
                        <a href="../supplier.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Show success or error notifications from PHP using SweetAlert
        document.addEventListener('DOMContentLoaded', () => {
            const message = <?= json_encode($_SESSION['message'] ?? '') ?>;
            const messageType = <?= json_encode($_SESSION['message_type'] ?? '') ?>;

            if (message) {
                Swal.fire({
                    title: messageType === 'success' ? 'Success' : 'Error',
                    text: message,
                    icon: messageType === 'success' ? 'success' : 'error',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Redirect to supplier page after closing the SweetAlert
                    window.location.href = '../supplier.php';
                });
            }
        });
    </script>

</body>
</html>
