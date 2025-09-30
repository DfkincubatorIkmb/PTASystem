<?php
session_start();
error_reporting(0);
include("include/config.php");

// Kosongkan sesi lama untuk mengelakkan percampuran
session_unset();

if(isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = md5($_POST['password']); // Pastikan ini serasi dengan hashing dalam pangkalan data

    // Pengesahan Admin
    $stmt = $con->prepare("SELECT * FROM admin WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    $num = $result->fetch_assoc();

    if($num) {
        $_SESSION['alogin'] = $username;
        $_SESSION['id'] = $num['id'];
        $extra = "dashboard.php";
        $host = $_SERVER['HTTP_HOST'];
        $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        header("location:http://$host$uri/$extra");
        exit();
    } else {
        // Pengesahan Subadmin
        $stmt1 = $con->prepare("SELECT * FROM tblsubadmin WHERE UserName = ? AND Password = ?");
        $stmt1->bind_param("ss", $username, $password);
        $stmt1->execute();
        $result1 = $stmt1->get_result();
        $num1 = $result1->fetch_assoc();

        if($num1) {
            $acctstatus = $num1['IsActive'];
            if($acctstatus == '1') {
                $_SESSION['subalogin'] = $username;
                $_SESSION['suid'] = $num1['id'];
                $extra = "sub/dashboard.php";
                $host = $_SERVER['HTTP_HOST'];
                $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
                header("location:http://$host$uri/$extra");
                exit();
            } else {
                $_SESSION['errmsg'] = "Your account is blocked. Please contact administrator.";
            }
        } else {
            $_SESSION['errmsg'] = "Invalid username or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-CARE | Admin Login</title>
  <link rel="icon" href="logopta.png" type="image/png">
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      font-family: 'Poppins', sans-serif;
      color: #fff;
      margin: 0;
    }
    .navbar {
      background: rgba(0, 0, 0, 0.3);
      backdrop-filter: blur(6px);
    }
    .logo-container {
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 40px 0 10px 0;
    }
    .logo {
      width: 100px;
      margin: 0 10px;
    }
    .welcome-text {
      font-family: 'Pacifico', cursive;
      font-size: 2rem;
      color: #ffdd57;
      text-shadow: 2px 2px 6px rgba(0,0,0,0.3);
      text-align: center;
    }
    .system-text {
      font-family: 'Pacifico', cursive;
      font-size: 2.5rem;
      color: #ffffff;
      text-shadow: 2px 2px 8px rgba(0,0,0,0.4);
      text-align: center;
      margin-bottom: 20px;
    }
    .module-login {
      background: #fff;
      color: #333;
      border-radius: 1.2rem;
      padding: 2rem;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      max-width: 420px;
      margin: 0 auto;
      animation: fadeIn 0.9s ease;
    }
    .btn-gradient {
      background: linear-gradient(90deg, #ff6a00, #ee0979);
      color: #fff;
      border: none;
    }
    .btn-gradient:hover { opacity: 0.9; }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    footer {
      margin-top: auto;
      text-align: center;
      padding: 15px;
      color: #fff;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
      <a class="navbar-brand fw-bold" href="../index.html">E-CARE | Admin</a>
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="../index.html">Back to Portal</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Logo + Welcome -->
  <div class="logo-container">
    <img src="logopta.png" alt="PTA Services Logo" class="logo">
    <img src="logoe-care2.png" alt="e-care Logo" class="logo">
  </div>
  <div class="welcome-text">WELCOME TO</div>
  <div class="system-text">E-CARE</div>

  <!-- Login Module -->
  <div class="module-login">
    <h3 class="fw-bold text-primary mb-3">Admin Sign In</h3>

    <!-- Alerts -->
    <?php if ($errormsg): ?>
      <div class="alert alert-danger py-2"><?= htmlentities($errormsg) ?></div>
    <?php endif; ?>
    <?php if ($msg): ?>
      <div class="alert alert-success py-2"><?= htmlentities($msg) ?></div>
    <?php endif; ?>

    <!-- Form -->
    <form method="post" name="login">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" placeholder="masukkan username" required autofocus>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        <div class="mt-1">
          <a href="forgot-password.php" class="text-decoration-none text-primary fw-semibold">Forgot Password?</a>
        </div>
      </div>
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
      <button type="submit" name="submit" class="btn btn-gradient w-100">Login</button>
    </form>
  </div>

  <!-- Footer -->
  <footer>
    © 2025 DFKIKMBesut. All rights reserved.
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

