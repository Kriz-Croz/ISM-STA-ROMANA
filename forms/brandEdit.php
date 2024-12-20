<?php
require '../db/connection.php';
require '../db/show.php';
require '../db/update.php';

// Start the session to store messages
session_start();

$id = $_GET['id'] ?? NULL;
if (!isset($id)) {
    header("Location: brand.php"); // Redirect if no ID is provided
    exit();
}

$showBrand = new Show($conn, 'brand');
$update = new Update($conn, 'brand', ['brand_id' => $id]);

// Fetch brand data
$data = $showBrand->showRecords("brand_id = $id");

if (isset($_POST['Update'])) {
    $data = [];
    foreach ($_POST as $name => $value) {
        if ($name != "Update") {
            $data[$name] = $value;
        }
    }

    try {
        // Update brand in the database
        $update->updateQuery($data);
        $_SESSION['message'] = "Brand updated successfully!";
        $_SESSION['message_type'] = 'success'; // Message type for SweetAlert
        header('Location: ../brand.php'); // Redirect to brand.php
        exit();
    } catch (Exception $e) {
        $_SESSION['message'] = "Error: $e";
        $_SESSION['message_type'] = 'error'; // Error message type for SweetAlert
        header('Location: ../brand.php'); // Redirect to brand.php
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
    <title>Edit Brand</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <script src="../bootstrap/js/bootstrap.bundle.js"></script>
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php
    // Check for a session message and display it using SweetAlert
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $messageType = $_SESSION['message_type'] ?? 'info';
        echo "<script>
            Swal.fire({
                icon: '$messageType',
                title: '$message',
                showConfirmButton: false,
                timer: 3000
            });
        </script>";
        unset($_SESSION['message'], $_SESSION['message_type']); // Clear message
    }
    ?>
    <div class="container mt-5">
        <h2 class="text-center">Edit Brand</h2>
        <div class="card">
            <div class="card-body">
                <form action="" method="post">
                    <div class="mb-3">
                        <label for="brand_name" class="form-label">Brand Name</label>
                        <input type="text" name="brand_name" value="<?= $data[0][1] ?>" class="form-control" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="../brand.php" class="btn btn-secondary">Back</a>
                        <input type="submit" value="Update Brand" name="Update" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
