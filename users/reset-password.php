<?php
session_start();
include("includes/config.php");

$msg = "";
$error = "";

// Semak sama ada user sah dari verify_code.php
if (!isset($_SESSION['reset_user'])) {
    header("Location: forgot-password.php");
    exit;
}

$user_id = intval($_SESSION['reset_user']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass1 = trim($_POST['password'] ?? '');
    $pass2 = trim($_POST['confirm_password'] ?? '');

    if ($pass1 === '' || $pass2 === '') {
        $error = "Please enter your new password.";
    } elseif ($pass1 !== $pass2) {
        $error = "Passwords do not match.";
    } elseif (strlen($pass1) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Hash password baru
        $hash = password_hash($pass1, PASSWORD_BCRYPT);

        $sql = "UPDATE users SET password='" . mysqli_real_escape_string($con, $hash) . "' 
                WHERE id='" . $user_id . "' LIMIT 1";
        $res = mysqli_query($con, $sql);

        if ($res) {
            // Hapus session reset
            unset($_SESSION['reset_user']);
            unset($_SESSION['reset_phone']);
            $msg = "Password successfully updated. <a href='index.php'>Login here</a>.";
        } else {
            $error = "Failed to update password: " . mysqli_error($con);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Password</title>
    <style>
        body{font-family:Arial;display:flex;align-items:center;justify-content:center;height:100vh;background:#f3f6fb;}
        .box{background:#fff;padding:22px;border-radius:8px;width:360px;box-shadow:0 6px 18px rgba(0,0,0,0.08);text-align:center;}
        input{width:92%;padding:10px;margin:8px 0;border:1px solid #ddd;border-radius:6px;}
        button{padding:10px 18px;background:#27ae60;color:#fff;border:0;border-radius:6px;cursor:pointer;}
        .error{color:#c0392b;} .success{color:#27ae60;}
    </style>
</head>
<body>
<div class="box">
    <h3>Set New Password</h3>

    <?php if ($msg): ?><p class="success"><?php echo $msg; ?></p><?php endif; ?>
    <?php if ($error): ?><
