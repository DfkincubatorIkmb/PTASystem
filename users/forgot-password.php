<?php
require __DIR__ . '/../vendor/autoload.php'; // ikut lokasi projek
use Twilio\Rest\Client;

session_start();
include("includes/config.php");

$msg = "";
$error = "";

// Bila user submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim(mysqli_real_escape_string($con, $_POST['phone'] ?? ''));

    if ($phone === '') {
        $error = "Please enter your phone number.";
    } else {
        // NOTE: table users column ialah `contactno`
        $sql = "SELECT id FROM users WHERE contactno='" . mysqli_real_escape_string($con, $phone) . "' LIMIT 1";
        $res = mysqli_query($con, $sql);

        if ($res === false) {
            $error = "Database error: " . mysqli_error($con);
        } elseif (mysqli_num_rows($res) === 0) {
            $error = "Phone number not found.";
        } else {
            $row = mysqli_fetch_assoc($res);
            $user_id = $row['id'];

            // Generate 6-digit OTP
            $otp = strval(rand(100000, 999999));
            $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

            // Buang OTP lama dan simpan yang baru
            mysqli_query($con, "DELETE FROM password_resets WHERE user_id='" . intval($user_id) . "'");
            $ins = mysqli_query(
                $con,
                "INSERT INTO password_resets (user_id, otp, expires_at) 
                 VALUES ('" . intval($user_id) . "', '" . mysqli_real_escape_string($con, $otp) . "', '" . $expires . "')"
            );

            if ($ins) {
                // === HANTAR OTP VIA TWILIO ===
                $sid    = "AC223a7e26b7e4281eccf9bfde0aee364d";
                $token  = "a704d9aa88089b6590c95569c6f3dab7";
                $twilio = new Client($sid, $token);

                try {
                    $message = $twilio->messages->create(
                        "+6" . $phone, // guna country code Malaysia +60
                        [
                            "from" => "YOUR_TWILIO_PHONE_NUMBER", // contoh: +1234567890
                            "body" => "Your OTP code is: $otp. It will expire in 10 minutes."
                        ]
                    );
                } catch (Exception $e) {
                    $error = "Failed to send SMS: " . $e->getMessage();
                }

                // Simpan phone dlm session → verify_code.php
                $_SESSION['reset_phone'] = $phone;
                header("Location: verify_code.php?phone=" . urlencode($phone));
                exit;
            } else {
                $error = "Failed to create OTP: " . mysqli_error($con);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Forgot Password (Phone)</title>
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
    <h3>Reset Password (Phone)</h3>

    <?php if ($msg): ?><p class="success"><?php echo htmlspecialchars($msg); ?></p><?php endif; ?>
    <?php if ($error): ?><p class="error"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>

    <form method="post" autocomplete="off">
        <label>Enter your phone number</label><br>
        <input type="text" name="phone" placeholder="e.g. 0123456789" required autofocus><br>
        <button type="submit">Send Verification Code</button>
    </form>

    <p style="margin-top:12px;"><a href="index.php">Back to Login</a></p>
</div>
</body>
</html>
