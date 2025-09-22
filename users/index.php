<?php
ob_start();
session_start();
include("includes/config.php");

// Check DB
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Vars
$errormsg = "";
$msg = "";

// Login process
if (isset($_POST['submit'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $errormsg = "Ralat penghantaran borang. Sila cuba lagi.";
    } else {
        $username = mysqli_real_escape_string($con, $_POST['username']);
        $password = $_POST['password'];

        $stmt = $con->prepare("SELECT * FROM users WHERE userEmail = ? OR icnumber = ?");
        if ($stmt) {
            $stmt->bind_param("ss", $username, $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row && password_verify($password, $row['password'])) {
                $_SESSION['login'] = $row['userEmail'] ?: $row['icnumber'];
                $_SESSION['id'] = $row['id'];
                $uip = $_SERVER['REMOTE_ADDR'];
                mysqli_query($con, "INSERT INTO userlog(uid, username, userip, status) VALUES ('".$_SESSION['id']."', '".$_SESSION['login']."', '$uip', 1)");
                header("Location: dashboard.php");
                exit();
            } else {
                $errormsg = "Emel atau nombor IC atau kata laluan salah.";
            }
            $stmt->close();
        } else {
            $errormsg = "Ralat sistem. Sila cuba lagi.";
        }
    }
}

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

ob_end_flush();
?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-CARE | User Login</title>
  <link rel="icon" href="logopta.png" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Poppins', sans-serif;
      color: #fff;
      margin: 0;
      text-align: center;
    }
    .hero-card {
      background: #fff;
      color: #333;
      border-radius: 1.2rem;
      padding: 2rem;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      max-width: 420px;
      width: 100%;
      animation: fadeIn 0.9s ease;
    }
    .btn-gradient {
      background: linear-gradient(90deg, #ff6a00, #ee0979);
      color: #fff;
      border: none;
    }
    .btn-gradient:hover { opacity: 0.9; }
    .welcome-text {
      font-family: 'Pacifico', cursive;
      font-size: 2rem;
      color: #ffdd57;
      text-shadow: 2px 2px 6px rgba(0,0,0,0.3);
    }
    .system-text {
      font-family: 'Pacifico', cursive;
      font-size: 2.5rem;
      color: #ffffff;
      text-shadow: 2px 2px 8px rgba(0,0,0,0.4);
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .no-underline {
      text-decoration: none;
      color: inherit;
    }
    .logo-container {
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0cm 0; /* 3 cm spacing above and below */
    }
    .logo {
      width: 100px;
      margin: 0 10px;
    }
  </style>
</head>
<body>
  <section class="text-center px-3 w-100">
    <div class="logo-container">
      <!-- Welcome Text -->
      <img src="logopta.png" alt="PTA Services Logo" class="logo">
      <img src="logoe-care2.png" alt="e-care Logo" class="logo">
    </div>
    <div class="welcome-text mb-1">WELCOME TO</div>
    <a href="../index.html" class="system-text no-underline">E-CARE</a>

    <div class="hero-card text-start mx-auto">
      <h2 class="fw-bold mb-3 text-primary">Quick Login</h2>

      <!-- Alerts -->
      <?php if ($errormsg): ?>
        <div class="alert alert-danger py-2"><?= htmlentities($errormsg) ?></div>
      <?php endif; ?>
      <?php if ($msg): ?>
        <div class="alert alert-success py-2"><?= htmlentities($msg) ?></div>
      <?php endif; ?>

      <!-- PHP Login Form -->
      <form method="post" name="login">
        <div class="mb-3">
          <label class="form-label">Email / IC Number</label>
          <input type="text" name="username" class="form-control" placeholder="masukkan email atau ic number" required autofocus>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <button type="submit" name="submit" class="btn btn-gradient w-100">Sign In</button>
      </form>

      <!-- Forgot Password -->
      <div class="mt-2 text-center">
        <a href="forgot-password.php" class="text-decoration-none text-primary fw-semibold">
          Forgot Password?
        </a>
      </div>

      <div class="mt-3 text-center">
        <a href="registration.php" class="btn btn-outline-primary w-100">Create Account</a>
      </div>
    </div>

    <footer class="text-center text-white py-3 mt-4">
      © 2025 DFKIKMBesut. All rights reserved.
    </footer>
  </section>
</body>
</html>