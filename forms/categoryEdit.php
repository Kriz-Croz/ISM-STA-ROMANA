<?php
require '../db/connection.php';
require '../db/show.php';
require '../db/update.php';

// Start the session to store messages
session_start();

$id = $_GET['id'] ?? NULL;
if (!isset($id)) {
    header("Location: category.php"); // Redirect if no ID is provided
    exit();
}

$show = new Show($conn, 'category');
$update = new Update($conn, 'category', ['category_id' => $id]);

// Fetch category data
$data = $show->showRecords("category_id = $id");

if (isset($_POST['Update'])) {
    $data = [];
    foreach ($_POST as $name => $value) {
        if ($name != "Update") {
            $data[$name] = $value;
        }
    }

    try {
        $update->updateQuery($data);
        $_SESSION['message'] = "Category updated successfully!";
        $_SESSION['message_type'] = 'success'; // Message type for SweetAlert in category.php
        header('Location: ../category.php'); // Redirect to category.php
        exit();
    } catch (Exception $e) {
        $_SESSION['message'] = "Error: $e";
        $_SESSION['message_type'] = 'error'; // Error message type for SweetAlert in category.php
        header('Location: ../category.php'); // Redirect to category.php
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
    <title>Edit Category</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <script src="../bootstrap/js/bootstrap.bundle.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Edit Category</h2>
        <div class="card">
            <div class="card-body">
                <form action="" method="post">
                    <div class="mb-3">
                        <label for="category_name" class="form-label">Category Name</label>
                        <input type="text" name="category_name" value="<?= $data[0][1] ?>" class="form-control" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="../category.php" class="btn btn-secondary">Back</a>
                        <input type="submit" value="Update Category" name="Update" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
