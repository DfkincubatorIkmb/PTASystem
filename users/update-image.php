<?php
session_start();

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Include configuration file with fallback
$con = null;
if (file_exists('includes/config.php')) {
    include('includes/config.php');
    if (!$con) {
        echo "<p>Sambungan pangkalan data tidak dapat diwujudkan. Sila semak config.php.</p>";
        exit;
    }
} else {
    echo "<p>Fail konfigurasi tidak ditemui. Sila semak includes/config.php.</p>";
    exit;
}

// Redirect if not logged in
if (empty($_SESSION['login'])) {
    header('location:index.php');
    exit;
}

date_default_timezone_set('Asia/Kuala_Lumpur');
$currentTime = date('d-m-Y h:i:s A', time());

$successmsg = '';
$errormsg = '';
$userData = null;

if (isset($con)) {
    // Fetch user data once for both form and sidebar
    $user_query = mysqli_prepare($con, "SELECT fullName, userImage FROM users WHERE userEmail = ? OR icnumber = ?");
    mysqli_stmt_bind_param($user_query, 'ss', $_SESSION['login'], $_SESSION['login']);
    mysqli_stmt_execute($user_query);
    $result = mysqli_stmt_get_result($user_query);
    $userData = $result->fetch_assoc();
    mysqli_stmt_close($user_query);
}

// Handle AJAX request
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Verify CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $errormsg = 'Token CSRF tidak sah.';
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => $errormsg]);
            exit;
        }
    } else {
        try {
            if (!$con) {
                throw new Exception('Tiada sambungan pangkalan data tersedia.');
            }

            mysqli_begin_transaction($con);

            $imgfile = $_FILES['image']['name'] ?? '';
            $tmpfile = $_FILES['image']['tmp_name'] ?? '';

            if (empty($imgfile) || empty($tmpfile)) {
                throw new Exception('Tiada fail dimuat naik.');
            }

            // Validate file
            $allowed_extensions = ['.jpg', '.jpeg', '.png', '.gif'];
            $extension = strtolower(strrchr($imgfile, '.'));
            if (!in_array($extension, $allowed_extensions)) {
                throw new Exception('Format tidak sah. Hanya format jpg, jpeg, png, atau gif dibenarkan.');
            }

            // Enhanced file validation
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $tmpfile);
            finfo_close($finfo);
            $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 2 * 1024 * 1024; // 2MB
            if (!in_array($mime_type, $allowed_mime_types) || $_FILES['image']['size'] > $max_size) {
                throw new Exception('Imej tidak sah atau saiz fail melebihi 2MB.');
            }

            // Check image dimensions and integrity
            $image_info = getimagesize($tmpfile);
            if (!$image_info) {
                throw new Exception('Fail yang dimuat naik bukan imej yang sah.');
            }

            $imgnewfile = md5($imgfile . time()) . $extension;
            $upload_dir = 'userimages/';
            $dest_file = $upload_dir . basename($imgnewfile);

            // Ensure upload directory exists and is writable
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            if (!is_writable($upload_dir)) {
                throw new Exception('Direktori muat naik tidak boleh ditulis.');
            }

            // Delete old image if it exists
            if (!empty($userData['userImage']) && file_exists('userimages/' . $userData['userImage'])) {
                unlink('userimages/' . $userData['userImage']);
            }

            // Move uploaded file with additional security
            if (!move_uploaded_file($tmpfile, $dest_file)) {
                throw new Exception('Gagal memindahkan fail yang dimuat naik.');
            }
            chmod($dest_file, 0644); // Set secure file permissions

            // Update database
            $stmt = mysqli_prepare($con, "UPDATE users SET userImage = ? WHERE userEmail = ? OR icnumber = ?");
            mysqli_stmt_bind_param($stmt, 'sss', $imgnewfile, $_SESSION['login'], $_SESSION['login']);
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception('Kemas kini pangkalan data gagal: ' . mysqli_error($con));
            }

            mysqli_commit($con);
            $successmsg = 'Foto profil berjaya dikemas kini!';
            // Update userData for non-AJAX
            if ($userData) {
                $userData['userImage'] = $imgnewfile;
            }

            // Generate new CSRF token
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            if ($isAjax) {
                // Append timestamp to prevent caching
                $imageUrl = 'userimages/' . $imgnewfile . '?t=' . time();
                echo json_encode([
                    'success' => true,
                    'message' => $successmsg,
                    'newImage' => $imageUrl
                ]);
                exit;
            }
        } catch (Exception $e) {
            if (isset($con) && mysqli_ping($con)) {
                mysqli_rollback($con);
            }
            $errormsg = $e->getMessage();
            if ($isAjax) {
                echo json_encode(['success' => false, 'message' => $errormsg]);
                exit;
            }
        } finally {
            if (isset($stmt)) {
                mysqli_stmt_close($stmt);
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

    <title>E-CARE | Kemas Kini Foto Profil</title>
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
    <?php
    if (file_exists('includes/header.php')) {
        include('includes/header.php');
    } else {
        echo "<p>Fail header tidak ditemui.</p>";
    }
    if (file_exists('includes/sidebar.php')) {
        include('includes/sidebar.php');
    } else {
        echo "<p>Fail sidebar tidak ditemui.</p>";
    }
    ?>
    <section id="main-content">
        <section class="wrapper">
            <h3><i class="fa fa-angle-right"></i> Kemas Kini Foto Profil</h3>
            <div class="row mt">
                <div class="col-lg-12">
                    <div class="form-panel">
                        <?php if ($successmsg): ?>
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <b>Berjaya!</b> <?php echo htmlentities($successmsg); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($errormsg): ?>
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <b>Ralat!</b> <?php echo htmlentities($errormsg); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($userData): ?>
                            <h4 class="mb"><i class="fa fa-user"></i>&nbsp;&nbsp;Profil <?php echo htmlentities($userData['fullName']); ?></h4>
                            <form class="form-horizontal style-form" id="profileForm" enctype="multipart/form-data" method="post" name="profile">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlentities($_SESSION['csrf_token']); ?>">
                                <input type="hidden" name="submit" value="1">
                                <div class="form-group">
                                    <label class="col-sm-2 col-sm-2 control-label">Foto Pengguna</label>
                                    <div class="col-sm-4">
                                        <?php $userphoto = $userData['userImage'] ?? '';
                                        $imageUrl = empty($userphoto) ? 'userimages/man.png' : 'userimages/' . htmlentities($userphoto) . '?t=' . time(); ?>
                                        <img src="<?php echo $imageUrl; ?>" width="256" height="256" alt="Foto Profil" id="previewImage">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 col-sm-2 control-label">Muat Naik Foto Baru</label>
                                    <div class="col-sm-4">
                                        <input type="file" name="image" id="imageUpload" accept="image/jpeg,image/png,image/gif" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-10" style="padding-left:25%">
                                        <button type="submit" class="btn btn-primary" id="submitButton">Hantar</button>
                                        <span id="loading" style="display:none;">Memuat...</span>
                                    </div>
                                </div>
                            </form>
                        <?php else: ?>
                            <p>Tiada data pengguna ditemui.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </section>
    <?php
    if (file_exists('includes/footer.php')) {
        include('includes/footer.php');
    } else {
        echo "<p>Fail footer tidak ditemui.</p>";
    }
    ?>
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
    $(function () {
        $('select.styled').customSelect();

        // Enhanced image preview with error handling
        $('#imageUpload').change(function () {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#previewImage').attr('src', e.target.result).attr('alt', 'Pratonton imej');
                };
                reader.onerror = function () {
                    alert('Ralat membaca fail. Sila cuba lagi.');
                };
                reader.readAsDataURL(file);
            }
        });

        // AJAX form submission
        $('#profileForm').submit(function (e) {
            e.preventDefault();
            $('#loading').show();
            $('#submitButton').prop('disabled', true);

            var formData = new FormData(this);

            $.ajax({
                url: window.location.href,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    try {
                        var data = JSON.parse(response);
                        if (data.success) {
                            // Update main image
                            $('#previewImage').attr('src', data.newImage).attr('alt', 'Imej Profil Dikemas Kini');
                            // Update sidebar image
                            $('.img-circle').attr('src', data.newImage);
                            alert(data.message);
                        } else {
                            alert(data.message);
                        }
                    } catch (err) {
                        alert('Ralat memproses respons.');
                    }
                },
                error: function () {
                    alert('Muat naik gagal. Sila cuba lagi.');
                },
                complete: function () {
                    $('#loading').hide();
                    $('#submitButton').prop('disabled', false);
                }
            });
        });
    });
</script>

</body>
</html>
<?php
if (isset($con)) {
    mysqli_close($con);
}
?>