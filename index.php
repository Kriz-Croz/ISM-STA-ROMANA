<?php
session_start();
require 'db/connection.php';
require 'db/show.php';

if (isset($_SESSION['admin'])) {
    header("Location: dashboard.php");
}

$error = '';

$adminTable = new Show($conn, 'admin');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Username and Password are required.";
    } else {
        $where = "username = '$username'";
        $result = $adminTable->showRecords($where);

        if (!empty($result)) {
            $admin = $result[0];
            $admin_id = $admin[0];
            $hashed_password = $admin[2];

            if (password_verify($password, $hashed_password)) {
                $_SESSION['admin'] = true;
                $_SESSION['id'] = $admin_id;
                $_SESSION['username'] = $username;

                $lowStockThreshold = 10;
                $show = new Show($conn, 'product');
                $products = $show->showRecords(null);

                $notificationCount = 0;
                foreach ($products as $product) {
                    $stock = $product[3]; 
                    if ($stock <= 0 || $stock <= $lowStockThreshold) {
                        $notificationCount++;
                    }
                }


                $_SESSION['notificationCount'] = $notificationCount;

                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid Password.";
            }
        } else {
            $error = "Invalid Username or Password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .card {
      border-radius: 10px;
      box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
      background-color: #ffffff;
    }
    .btn-login {
      background-color: #1d4e2b;
      color: white;
    }
    .btn-login:hover {
      background-color: #155a24;
    }
    .form-label {
      color: #333;
    }
    #togglePassword {
      color: #6c757d;
    }
  </style>
</head>
<body>
  <section class="vh-100 d-flex align-items-center" style="background-color: #ced4da;">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
          <div class="card">
            <div class="row g-0">
              <div class="col-md-5 d-none d-md-block">
                <img src="logo.png" alt="Login" class="img-fluid" style="border-radius: 10px 0 0 10px;">
              </div>
              <div class="col-md-7">
                <div class="card-body p-4">
                  <h4 class="mb-3 text-center" style="color: #1d4e2b;">Admin Login</h4>
                  <form method="POST" action="">
                    <?php if (!empty($error)): ?>
                      <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <div class="form-outline mb-3">
                      <input type="text" name="username" id="username" class="form-control" required>
                      <label class="form-label" for="username">Username</label>
                    </div>
                    <div class="form-outline mb-3 position-relative">
                      <input type="password" name="password" id="password" class="form-control" required>
                      <label class="form-label" for="password">Password</label>
                      <span id="togglePassword" style="position: absolute; top: 25%; right: 10px; transform: translateY(-50%); cursor: pointer;">
                        <i class="fas fa-eye-slash"></i>
                      </span>
                    </div>
                    <div class="d-grid">
                      <button type="submit" class="btn btn-login btn-lg">Login</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script>
    const togglePassword = document.querySelector('#togglePassword');
    const passwordField = document.querySelector('#password');

    togglePassword.addEventListener('click', function () {
      const isPasswordHidden = passwordField.getAttribute('type') === 'password';
      passwordField.setAttribute('type', isPasswordHidden ? 'text' : 'password');
      this.innerHTML = isPasswordHidden
        ? '<i class="fas fa-eye"></i>'  
        : '<i class="fas fa-eye-slash"></i>';
    });
  </script>
</body>
</html>
