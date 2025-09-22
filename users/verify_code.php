<?php
session_start();
include("includes/config.php");

$msg = "";
$error = "";

// Pre-fill phone if passed
$prefill_phone = isset($_GET['phone']) ? $_GET['phone'] : ($_SESSION['reset_phone'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim(mysqli_real_escape_string($con, $_POST['phone'] ?? ''));
    $otp = trim(mysqli_real_escape_string($con, $_POST['otp'] ?? ''));

    if ($phone === '' || $otp === '') {
        $error = "Please provide phone and code.";
    } else {
        // find user
        $r = mysqli_query($con, "SELECT id FROM users WHERE contactno='" . mysqli_real_escape_string($con, $phone) . "' LIMIT 1");
        if ($r === false) {
            $error = "DB error: " . mysqli_error($con);
        } elseif (mysqli_num_rows($r) === 0) {
            $error = "Phone not found.";
        } else {
            $u = mysqli_fetch_assoc($r);
            $user_id = intval($u['id']);

            // check OTP (latest and not expired)
            $q = "SELECT * FROM password_resets WHERE user_id='$user_id' AND otp='" . mysqli_real_escape_string($con, $otp) . "' AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1";
            $check = mysqli_query($con, $q);
            if ($check && mysqli_num_rows($check) > 0) {
                // OK -> mark session and redirect to reset page
                $_SESSION['reset_user_id'] = $user_id;
                $_SESSION['otp_verified'] = true;

                // optionally delete the OTP row now or after password reset
                // mysqli_query($con, "DELETE FROM password_resets WHERE user_id='$user_id'");

                header("Location: reset-password.php");
                exit;
            } else {
                $error = "Invalid or expired code.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verify Code</title>
    <style>
        body{font-family:Arial;display:flex;align-items:center;justify-content:center;height:100vh;background:#f3f6fb;}
        .box{background:#fff;padding:22px;border-radius:8px;width:360px;box-shadow:0 6px 18px rgba(0,0,0,0.08);text-align:center;}
        input{width:92%;padding:10px;margin:8px 0;border:1px solid #ddd;border-radius:6px;}
        button{padding:10px 18px;background:#2b6ef6;color:#fff;border:0;border-radius:6px;cursor:pointer;}
        .error{color:#c0392b;} .success{color:#27ae60;}
    </style>
</head>
<body>
<div class="box">
    <h3>Enter Verification Code</h3>

    <?php if ($msg): ?><p class="success"><?php echo htmlspecialchars($msg); ?></p><?php endif; ?>
    <?php if ($error): ?><p class="error"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>

    <form method="post" autocomplete="off">
        <input type="text" name="phone" value="<?php echo htmlspecialchars($prefill_phone); ?>" placeholder="Phone" required><br>
        <input type="text" name="otp" placeholder="6-digit code" required><br>
        <button type="submit">Verify & Continue</button>
    </form>
    <p style="margin-top:12px;"><a href="forgot-password.php">Resend Code</a></p>
</div>
</body>
</html>
