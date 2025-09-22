<?php
ob_start();
session_start();
include('includes/config.php');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Default: no display errors

if (strlen($_SESSION['login']) == 0) {
    header('location:index.php');
    exit();
}

date_default_timezone_set('Asia/Kuala_Lumpur');
$currentTime = date('Y-m-d H:i:s'); // Format: 2025-09-17 02:40:00

$msg = "";
$error = "";
$fullname = $email = $contactno = $contactno2 = $icnumber = $address = $state = $country = $pincode = "";
$user_id = 0;

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch current user data
$query = mysqli_query($con, "SELECT * FROM users WHERE userEmail = '" . mysqli_real_escape_string($con, $_SESSION['login']) . "' OR icnumber = '" . mysqli_real_escape_string($con, $_SESSION['login']) . "'");
if ($row = mysqli_fetch_array($query)) {
    $user_id = $row['id'];
    $fullname = $row['fullName'];
    $email = $row['userEmail'] ?? '';
    $contactno = $row['contactno'];
    $contactno2 = $row['contactno2'] ?? '';
    $icnumber = $row['icnumber'];
    $address = $row['address'];
    $state = $row['State'] ?? '';
    $country = $row['country'] ?? '';
    $pincode = $row['pincode'] ?? '';
    $userphoto = $row['userImage'] ?? '';
    $_SESSION['id'] = $user_id; // Set for consistency if not already
} else {
    $error = "Data pengguna tidak ditemui.";
}

if (isset($_POST['submit'])) {
    // Enable display errors only during submit for debugging
    ini_set('display_errors', 1);

    if (!isset($_POST['csrf_token']) || !hash_equals($_POST['csrf_token'], $_SESSION['csrf_token'])) {
        error_log("Pengesahan token CSRF gagal: POST=" . ($_POST['csrf_token'] ?? 'tidak ditetapkan') . ", SESSION=" . ($_SESSION['csrf_token'] ?? 'tidak ditetapkan'));
        $error = "Ralat penghantaran borang. Sila cuba lagi.";
    } else {
        // Sanitize and validate inputs
        $fullname = trim($_POST['fullname'] ?? '');
        $email = filter_input(INPUT_POST, 'useremail', FILTER_VALIDATE_EMAIL) ?: '';
        $contactno = preg_replace('/[^0-9]/', '', trim($_POST['contactno'] ?? ''));
        $contactno2 = preg_replace('/[^0-9]/', '', trim($_POST['contactno2'] ?? ''));
        $icnumber = preg_replace('/[^0-9]/', '', trim($_POST['icnumber'] ?? ''));
        $address = trim(strip_tags($_POST['address'] ?? ''));
        $state = trim(strip_tags($_POST['state'] ?? ''));
        $country = trim(strip_tags($_POST['country'] ?? ''));
        $pincode = preg_replace('/[^0-9]/', '', trim($_POST['pincode'] ?? ''));

        if (!empty($email) && !$email) {
            $error = "Sila masukkan alamat emel yang sah.";
            error_log("Pengesahan emel gagal: $email");
        } elseif (empty($fullname) || empty($contactno) || empty($icnumber) || empty($address) || empty($state) || empty($country) || empty($pincode)) {
            $error = "Semua medan yang diperlukan mesti diisi.";
            error_log("Medan yang diperlukan hilang");
        } elseif (!preg_match('/^\d{12}$/', $icnumber)) {
            $error = "Nombor IC tidak sah. Mesti 12 digit angka (contoh: 990123065432 atau 990123-06-5432).";
            error_log("Pengesahan nombor IC gagal: $icnumber");
        } elseif (strlen($pincode) != 5 || !ctype_digit($pincode)) {
            $error = "Pincode mesti 5 digit angka.";
            error_log("Pengesahan pincode gagal: $pincode");
        } else {
            // Check uniqueness for email and icnumber if changed
            $email_changed = $email !== ($row['userEmail'] ?? '');
            $ic_changed = $icnumber !== ($row['icnumber'] ?? '');

            $unique_check = true;
            if ($email_changed || $ic_changed) {
                $sql = "SELECT id, userEmail, icnumber FROM users WHERE ";
                $params = [];
                $types = "";

                if ($email_changed && !empty($email)) {
                    $sql .= "(userEmail = ?)";
                    $params[] = $email;
                    $types .= "s";
                }
                if ($ic_changed) {
                    if ($email_changed) $sql .= " OR ";
                    $sql .= "(icnumber = ?)";
                    $params[] = $icnumber;
                    $types .= "s";
                }
                $sql .= " AND id != ? LIMIT 1";
                $params[] = $user_id;
                $types .= "i";

                $stmt = $con->prepare($sql);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $existing = $result->fetch_assoc();
                    if ($email_changed && $existing['userEmail'] === $email) {
                        $error = "Emel telah didaftarkan oleh pengguna lain.";
                        error_log("Emel sudah didaftarkan: $email");
                    } elseif ($ic_changed && $existing['icnumber'] === $icnumber) {
                        $error = "Nombor IC telah didaftarkan oleh pengguna lain.";
                        error_log("Nombor IC sudah didaftarkan: $icnumber");
                    }
                    $unique_check = false;
                }
                $stmt->close();
            }

            if ($unique_check) {
                // Update user data
                $stmt = $con->prepare("UPDATE users SET fullName = ?, userEmail = ?, contactno = ?, contactno2 = ?, icnumber = ?, address = ?, State = ?, country = ?, pincode = ? WHERE id = ?");
                $stmt->bind_param("sssssssssi", $fullname, $email, $contactno, $contactno2, $icnumber, $address, $state, $country, $pincode, $user_id);

                if ($stmt->execute()) {
                    $msg = " berjaya dikemas kini.";
                    error_log("Kemas kini profil berjaya: user_id=$user_id");
                    $_SESSION['user_icnumber'] = $icnumber;
                    $_SESSION['login'] = $email ?: $icnumber;
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    // Update last updated date with prepared statement
                    $stmt_update = $con->prepare("UPDATE users SET updationDate = ? WHERE id = ?");
                    $stmt_update->bind_param("si", $currentTime, $user_id);
                    if ($stmt_update->execute()) {
                        error_log("updationDate dikemas kini kepada: $currentTime untuk user_id=$user_id");
                    } else {
                        error_log("Ralat mengemas kini updationDate: " . $stmt_update->error);
                    }
                    $stmt_update->close();
                    // Refresh user data
                    $query = mysqli_query($con, "SELECT * FROM users WHERE id = $user_id");
                    $row = mysqli_fetch_array($query);
                    $fullname = $row['fullName'];
                    $email = $row['userEmail'] ?? '';
                    $contactno = $row['contactno'];
                    $contactno2 = $row['contactno2'] ?? '';
                    $icnumber = $row['icnumber'];
                    $address = $row['address'];
                    $state = $row['State'] ?? '';
                    $country = $row['country'] ?? '';
                    $pincode = $row['pincode'] ?? '';
                    $userphoto = $row['userImage'] ?? '';
                } else {
                    $error = "Kemas kini profil gagal: " . $stmt->error;
                    error_log("Ralat pangkalan data: " . $stmt->error);
                }
                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Dashboard">
    <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
    <title>PTA | Profil Pengguna</title>
    <link rel="icon" href="logopta.png" type="image/png">
    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <!--external css-->
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="assets/js/bootstrap-datepicker/css/datepicker.css" />
    <link rel="stylesheet" type="text/css" href="assets/js/bootstrap-daterangepicker/daterangepicker.css" />
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/style-responsive.css" rel="stylesheet">
</head>
<body>
<section id="container">
    <?php include("includes/header.php");?>
    <?php include("includes/sidebar.php");?>
    <section id="main-content">
        <section class="wrapper">
            <h3><i class="fa fa-angle-right"></i> Profile Info</h3>
            <!-- BASIC FORM ELEMENTS -->
            <div class="row mt">
                <div class="col-lg-12">
                    <div class="form-panel">
                        <?php if ($msg) { ?>
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <b>Berjaya!</b> <?php echo htmlentities($msg); ?>
                            </div>
                        <?php } ?>
                        <?php if ($error) { ?>
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <b>Ralat!</b> <?php echo htmlentities($error); ?>
                            </div>
                        <?php } ?>
                        <h4 class="mb"><i class="fa fa-user"></i>&nbsp;&nbsp;Profil <?php echo htmlentities($fullname); ?></h4>
                        <h5><b>Terkini Dikemas Kini pada :</b>&nbsp;&nbsp;<?php echo htmlentities($row['updationDate'] ?? 'Belum dikemas kini'); ?></h5>
                        <form class="form-horizontal style-form" method="post" name="profile" id="profile-form">
                            <div class="form-group">
                                <label class="col-sm-2 col-sm-2 control-label">Nama Penuh</label>
                                <div class="col-sm-4">
                                    <input type="text" name="fullname" required="required" value="<?php echo htmlentities($fullname); ?>" class="form-control">
                                </div>
                                <label class="col-sm-2 col-sm-2 control-label">Emel Pengguna</label>
                                <div class="col-sm-4">
                                    <input type="email" name="useremail" id="userEmail" value="<?php echo htmlentities($email); ?>" class="form-control">
                                    <div id="email-validation-message"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 col-sm-2 control-label">No. Telefon Utama</label>
                                <div class="col-sm-4">
                                    <input type="tel" name="contactno" required="required" pattern="[0-9]+" value="<?php echo htmlentities($contactno); ?>" class="form-control">
                                </div>
                                <label class="col-sm-2 col-sm-2 control-label">No. Telefon Sekunder</label>
                                <div class="col-sm-4">
                                    <input type="tel" name="contactno2" pattern="[0-9]+" value="<?php echo htmlentities($contactno2); ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 col-sm-2 control-label">No. IC</label>
                                <div class="col-sm-4">
                                    <input type="text" name="icnumber" id="icnumber" required="required" value="<?php echo htmlentities($icnumber); ?>" class="form-control">
                                    <div id="icnumber-validation-message"></div>
                                </div>
                                <label class="col-sm-2 col-sm-2 control-label">Alamat</label>
                                <div class="col-sm-4">
                                    <textarea name="address" required="required" class="form-control"><?php echo htmlentities($address); ?></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 col-sm-2 control-label">Cawangan</label>
                                <div class="col-sm-4">
                                    <select name="state" required="required" class="form-control">
                                        <option value="<?php echo htmlentities($state); ?>"><?php echo htmlentities($state); ?></option>
                                        <?php
                                        $sql = mysqli_query($con, "SELECT stateName FROM state");
                                        while ($rw = mysqli_fetch_array($sql)) {
                                            if ($rw['stateName'] == $state) continue;
                                        ?>
                                            <option value="<?php echo htmlentities($rw['stateName']); ?>"><?php echo htmlentities($rw['stateName']); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <label class="col-sm-2 col-sm-2 control-label">Negara</label>
                                <div class="col-sm-4">
                                    <input type="text" name="country" required="required" value="<?php echo htmlentities($country); ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 col-sm-2 control-label">Poskod</label>
                                <div class="col-sm-4">
                                    <input type="text" name="pincode" maxlength="5" required="required" pattern="\d{5}" value="<?php echo htmlentities($pincode); ?>" class="form-control">
                                </div>
                                <label class="col-sm-2 col-sm-2 control-label">Tarikh Pendaftaran</label>
                                <div class="col-sm-4">
                                    <input type="text" name="regdate" required="required" value="<?php echo htmlentities($row['regDate']); ?>" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 col-sm-2 control-label">Foto Pengguna</label>
                                <div class="col-sm-4">
                                    <?php
                                    $imageUrl = empty($userphoto) || !file_exists("userimages/" . $userphoto)
                                        ? 'userimages/man.png?t=' . time()
                                        : 'userimages/' . htmlentities($userphoto) . '?t=' . time();
                                    ?>
                                    <img src="<?php echo $imageUrl; ?>" width="256" height="256" alt="Foto Profil">
                                    <a href="update-image.php">Tukar Foto</a>
                                </div>
                            </div>
                            <input type="hidden" name="csrf_token" value="<?php echo htmlentities($_SESSION['csrf_token']); ?>">
                            <div class="form-group">
                                <div class="col-sm-10" style="padding-left:25%">
                                    <button type="submit" name="submit" class="btn btn-primary">Hantar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </section>
    <?php include("includes/footer.php");?>
</section>
<!-- js placed at the end of the document so the pages load faster -->
<script src="assets/js/jquery.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script class="include" type="text/javascript" src="assets/js/jquery.dcjqaccordion.2.7.js"></script>
<script src="assets/js/jquery.scrollTo.min.js"></script>
<script src="assets/js/jquery.nicescroll.js" type="text/javascript"></script>
<script src="assets/js/common-scripts.js"></script>
<script src="assets/js/jquery-ui-1.9.2.custom.min.js"></script>
<script src="assets/js/bootstrap-switch.js"></script>
<script src="assets/js/jquery.tagsinput.js"></script>
<script type="text/javascript" src="assets/js/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="assets/js/bootstrap-daterangepicker/date.js"></script>
<script type="text/javascript" src="assets/js/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="assets/js/bootstrap-inputmask/bootstrap-inputmask.min.js"></script>
<script src="assets/js/form-component.js"></script>
<script>
    //custom select box
    $(function(){
        $('select.styled').customSelect();
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
            } else {
                $("#email-validation-message").html('');
            }
        });
        $('#icnumber').on('input', function() {
            const icnumber = $(this).val();
            const cleanIcnumber = icnumber.replace(/[^0-9]/g, '');
            if (cleanIcnumber.length === 12 && /^\d{12}$/.test(cleanIcnumber)) {
                $('#icnumber-validation-message').html('<span class="text-success">Format Nombor IC sah</span>');
            } else {
                $('#icnumber-validation-message').html('<span class="text-danger">Nombor IC mesti 12 digit angka (contoh: 990123065432 atau 990123-06-5432)</span>');
            }
        });
    });
</script>
</body>
</html>
<?php ob_end_flush(); ?>