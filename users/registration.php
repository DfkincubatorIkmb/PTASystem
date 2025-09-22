<?php
ob_start();
session_start();
include('includes/config.php');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Hide errors in production

$msg = "";
$error = "";
$fullname = $email = $contactno = $contactno2 = $icnumber = $address = "";

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['submit'])) {
    ini_set('display_errors', 1); // Enable errors during submission

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        error_log("CSRF token validation failed");
        $error = "Ralat penghantaran borang. Sila cuba lagi.";
    } else {
        // Sanitize input
        $fullname   = htmlspecialchars(trim($_POST['fullname'] ?? ''));
        $email      = !empty($_POST['userEmail']) ? filter_var(trim($_POST['userEmail']), FILTER_SANITIZE_EMAIL) : null;
        $contactno  = htmlspecialchars(trim($_POST['contactno'] ?? ''));
        $contactno2 = htmlspecialchars(trim($_POST['contactno2'] ?? ''));
        $icnumber   = preg_replace('/[^0-9]/', '', $_POST['icnumber'] ?? '');
        $address    = htmlspecialchars(trim($_POST['address'] ?? ''));
        $password   = $_POST['password'] ?? '';

        // Validation
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Sila masukkan alamat emel yang sah.";
        } elseif (empty($fullname) || empty($password) || empty($contactno) || empty($icnumber) || empty($address)) {
            $error = "Semua medan yang diperlukan mesti diisi.";
        } elseif (!preg_match('/^\d{12}$/', $icnumber)) {
            $error = "Nombor IC tidak sah. Mesti 12 digit angka.";
        } elseif (strlen($password) < 8) {
            $error = "Kata laluan mesti sekurang-kurangnya 8 aksara.";
        } else {
            // Check duplicates
            if ($email) {
                $stmt = $con->prepare("SELECT userEmail, icnumber FROM users WHERE icnumber = ? OR userEmail = ?");
                $stmt->bind_param("ss", $icnumber, $email);
            } else {
                $stmt = $con->prepare("SELECT userEmail, icnumber FROM users WHERE icnumber = ?");
                $stmt->bind_param("s", $icnumber);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if ($email && $row['userEmail'] === $email) {
                    $error = "Emel telah didaftarkan.";
                } elseif ($row['icnumber'] === $icnumber) {
                    $error = "Nombor IC telah didaftarkan.";
                }
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $status = 1;

                mysqli_query($con, "SET SESSION sql_mode = ''");

                $stmt = $con->prepare("INSERT INTO users (fullName, userEmail, password, contactno, contactno2, icnumber, address, status, State, country, pincode, userImage) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NULL, NULL, NULL, NULL)");
                $stmt->bind_param("sssssssi", $fullname, $email, $hashed_password, $contactno, $contactno2, $icnumber, $address, $status);

                if ($stmt->execute()) {
                    $_SESSION['id'] = $con->insert_id;
                    $_SESSION['user_id'] = $con->insert_id;
                    $_SESSION['user_icnumber'] = $icnumber;
                    $_SESSION['login'] = $email ?: $icnumber;

                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "Pendaftaran gagal: " . $stmt->error;
                }
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="PTA Service Management System - User Registration">
  <meta name="author" content="PTA Admin">
  <title>E-CARE | User Registration</title>
  <link rel="icon" href="logopta.png" type="image/png">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(135deg, #4361ee, #3a0ca3);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }
    .page-title {
      color: #fff;
      font-size: 2rem;
      font-weight: bold;
      margin-bottom: 20px;
      text-align: center;
      text-shadow: 1px 1px 4px rgba(0,0,0,0.4);
    }
    .card {
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      animation: fadeIn 1s ease-in-out;
    }
    .card h2 {
      color: #4361ee;
      margin-bottom: 20px;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

  <h1 class="page-title">
    <a href="../index.html" class="text-decoration-none text-white">
      E-CARE
    </a>
  </h1>

  <div class="card p-4 w-100" style="max-width: 500px;">
    <form id="registration-form" method="post">
      <h2 class="text-center">User Registration</h2>

      <?php if ($msg): ?>
        <div class="alert alert-success"><?php echo htmlentities($msg); ?></div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlentities($error); ?></div>
      <?php endif; ?>

      <div class="mb-3">
        <input type="text" class="form-control" placeholder="Sila Isi Nama Penuh Seperti Dalam Ic" name="fullname" required value="<?php echo htmlentities($fullname); ?>">
      </div>

      <div class="mb-3">
        <input type="email" class="form-control" placeholder="Email ID (Optional)" name="userEmail" id="userEmail" value="<?php echo htmlentities($email); ?>">
        <div id="email-validation-message" class="form-text"></div>
      </div>

      <div class="mb-3">
  <input type="password" class="form-control" placeholder="Password" name="password" id="password" required>
</div>
<div class="mb-3">
  <input type="password" class="form-control" placeholder="Confirm Password" id="confirm-password" required>
  <div id="password-match-message" class="form-text"></div>
</div>

      <div class="mb-3">
        <input type="tel" class="form-control" name="contactno" placeholder="Primary Phone Number" required pattern="[0-9]+" value="<?php echo htmlentities($contactno); ?>">
      </div>

      <div class="mb-3">
        <input type="tel" class="form-control" name="contactno2" placeholder="Secondary Phone Number" pattern="[0-9]+" value="<?php echo htmlentities($contactno2); ?>">
      </div>

      <div class="mb-3">
        <input type="text" class="form-control" name="icnumber" placeholder="IC Number" id="icnumber" required value="<?php echo htmlentities($icnumber); ?>">
        <div id="icnumber-validation-message"></div>
      </div>

      <div class="mb-3">
        <textarea class="form-control" name="address" placeholder="Address" required rows="4"><?php echo htmlentities($address); ?></textarea>
      </div>

      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
      <button class="btn btn-primary w-100" type="submit" name="submit"><i class="fa fa-user"></i> Register</button>

      <div class="text-center mt-3">
        Already Registered? <a href="index.php" class="fw-bold text-decoration-none">Sign in</a>
      </div>
    </form>
  </div>

  <footer class="text-center text-white py-3 mt-4">
    © 2025 DFKIKMBesut. All rights reserved.
  </footer>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <script>
    $('#userEmail').on('blur', function() {
      const email = $(this).val();
      if (email) {
        $.ajax({
          url: "check_availability.php",
          data: {email: email},
          type: "POST",
          success: function(data) {
            $("#email-validation-message").html(data);
          }
        });
      }
    });

    // $('#confirm-password').on('keyup', function() {
    //   const password = $('#password').val();
    //   const confirmPassword = $(this).val();
    //   if (password === confirmPassword) {
    //     $('#password-match-message').html('<span class="text-success">Passwords match</span>');
    //   } else {
    //     $('#password-match-message').html('<span class="text-danger">Passwords do not match</span>');
    //   }
    // });

    // $('#icnumber').on('input', function() {
    //   const icnumber = $(this).val().replace(/[^0-9]/g, '');
    //   if (icnumber.length === 12) {
    //     $('#icnumber-validation-message').html('<span class="text-success">IC Number format valid</span>');
    //   } else {
    //     $('#icnumber-validation-message').html('<span class="text-danger">IC Number must be 12 digits</span>');
    //   }
    // });

    function isIC(password) {
  return /^\d{12}$/.test(password); // IC mesti 12 digit nombor
}

function isStrongPassword(password) {
  return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(password);
}

function validatePassword() {
  const password = $("#password").val();
  const confirm = $("#confirm-password").val();
  const message = $("#password-match-message");
  const submitBtn = $("#submit-btn");

  if (password === "" || confirm === "") {
    message.text("");
    submitBtn.prop("disabled", true);
    return;
  }

  // check IC or strong password
  if (!(isIC(password) || isStrongPassword(password))) {
    message.html('<span class="text-danger">❌ Password mesti IC (12 digit) ATAU password yang kuat</span>');
    submitBtn.prop("disabled", true);
    return;
  }

  // check confirm password
  if (password === confirm) {
    message.html('<span class="text-success">✅ Password sepadan</span>');
    submitBtn.prop("disabled", false);
  } else {
    message.html('<span class="text-danger">❌ Password tak sama</span>');
    submitBtn.prop("disabled", true);
  }
}

// IC validation
$('#icnumber').on('input', function() {
  const icnumber = $(this).val().replace(/[^0-9]/g, '');
  $(this).val(icnumber);

  if (icnumber.length === 12) {
    $('#icnumber-validation-message').html('<span class="text-success">IC Number format valid</span>');
  } else {
    $('#icnumber-validation-message').html('<span class="text-danger">IC Number must be 12 digits</span>');
  }
});

// Password validation
$("#password, #confirm-password").on("input", validatePassword);

    

    $('#registration-form').on('submit', function(e) {
      const password = $('#password').val();
      const confirmPassword = $('#confirm-password').val();
      if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
      }
    });
  </script>
</body>
</html>
<?php ob_end_flush(); ?>
