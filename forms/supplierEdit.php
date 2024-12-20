<?php
require '../db/connection.php';
require '../db/show.php';
require '../db/update.php';

// Start the session to store messages
session_start();

$id = $_GET['id'] ?? NULL;
if (!isset($id)) {
    header("Location: supplier.php"); // Redirect if no ID is provided
    exit();
}

$show = new Show($conn, 'supplier');

// Initialize the Update class
$update = new Update($conn, 'supplier', ['supplier_id' => $id]);

// Fetch supplier data
$data = $show->showRecords("supplier_id = $id");

if (isset($_POST['Update'])) {
    $data = [];
    foreach ($_POST as $name => $value) {
        if ($name != "Update") {
            $data[$name] = $value;
        }
    }

    try {
        $update->updateQuery($data);
        $_SESSION['message'] = "Supplier updated successfully!";
        $_SESSION['message_type'] = 'success'; // Message type for SweetAlert in supplier.php
        header('Location: ../supplier.php'); // Redirect to supplier.php
        exit();
    } catch (Exception $e) {
        $_SESSION['message'] = "Error: $e";
        $_SESSION['message_type'] = 'error'; // Error message type for SweetAlert in supplier.php
        header('Location: ../supplier.php'); // Redirect to supplier.php
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Supplier</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <script src="../bootstrap/js/bootstrap.bundle.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Edit Supplier</h2>
        <div class="card">
            <div class="card-body">
                <form action="" method="post">
                    <div class="mb-3">
                        <label for="supplier_name" class="form-label">Supplier Name</label>
                        <input type="text" name="supplier_name" value="<?= $data[0][1] ?>" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="supplier_contact" class="form-label">Supplier Contact</label>
                        <input type="text" name="supplier_number" value="<?= $data[0][2] ?>" class="form-control" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="../supplier.php" class="btn btn-secondary">Back</a>
                        <input type="submit" value="Update Supplier" name="Update" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
