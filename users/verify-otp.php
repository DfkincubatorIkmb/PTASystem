<?php
session_start();
include("includes/config.php");

$error = "";

if (isset($_POST['verify'])) {
    $otp = $_POST['otp'];
    $userId = $_SESSION['reset_user'];

    $check = mysqli_query($con, "SELECT * FROM password_resets 
                                 WHERE user_id='$userId' AND otp='$otp' 
                                 ORDER BY created_at DESC LIMIT 1");

    if (mysqli_num_rows($check) > 0) {
        $_SESSION['otp_verified'] = true;
        header("Location: reset-password.php");
        exit;
    } else {
        $error = "Kod tidak sah!";
    }
}
?>

<form method="post">
    <input type="text" name="otp" placeholder="Masukkan kod OTP">
    <button type="submit" name="verify">Sahkan</button>
</form>
<p style="color:red"><?= $error ?></p>