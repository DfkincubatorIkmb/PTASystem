<aside>
    <div id="sidebar" class="nav-collapse">
        <!-- sidebar menu start-->
        <ul class="sidebar-menu" id="nav-accordion">
            <?php
            // Check session and database connection
            $login = $_SESSION['login'] ?? '';
            $userData = null;

            if (empty($login)) {
                error_log("Tiada sesi log masuk ditemui.");
                header('location:index.php');
                exit;
            }

            if (isset($con) && $con) {
                $query = $con->prepare("SELECT fullName, userImage FROM users WHERE userEmail = ? OR icnumber = ?");
                $query->bind_param("ss", $login, $login);
                if ($query->execute()) {
                    $result = $query->get_result();
                    $userData = $result->fetch_assoc();
                } else {
                    error_log("Kueri sidebar gagal: " . $con->error);
                }
                $query->close();
            } else {
                error_log("Sambungan pangkalan data tidak tersedia dalam sidebar.");
            }

            if ($userData) {
                $userphoto = $userData['userImage'] ?? '';
                $fullname = $userData['fullName'] ?? 'Pengguna Tidak Dikenali';
            ?>
                <p class="centered">
                    <a href="profile.php">
                        <?php
                        $imageUrl = empty($userphoto) || !file_exists("userimages/" . $userphoto)
                            ? 'userimages/man.png?t=' . time()
                            : 'userimages/' . htmlentities($userphoto) . '?t=' . time();
                        ?>
                        <img src="<?php echo $imageUrl; ?>" class="img-circle" width="70" height="70" alt="Foto Profil">
                    </a>
                </p>
                <h5 class="centered"><?php echo htmlentities($fullname); ?></h5>
            <?php } else { ?>
                <p class="centered">
                    <a href="profile.php">
                        <img src="userimages/noimage.png?t=<?php echo time(); ?>" class="img-circle" width="70" height="70" alt="Profil Lalai">
                    </a>
                </p>
                <h5 class="centered">Pengguna Tidak Ditemui</h5>
                <?php error_log("Tiada pengguna ditemui untuk log masuk: $login"); ?>
            <?php } ?>

            <li class="mt">
                <a href="dashboard.php">
                    <i class="fa fa-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="sub-menu">
                <a href="javascript:;">
                    <i class="fa fa-cogs"></i>
                    <span>Account Setting</span>
                </a>
                <ul class="sub">
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="change-password.php">Change Password</a></li>
                </ul>
            </li>
            <li class="sub-menu">
                <a href="register-complaint.php">
                    <i class="fa fa-book"></i>
                    <span>Customer Report Form</span>
                </a>
            </li>
            <li class="sub-menu">
                <a href="complaint-history.php">
                    <i class="fa fa-tasks"></i>
                    <span>Repair progress Report</span>
                </a>
            </li>
        </ul>
        <!-- sidebar menu end-->
    </div>
</aside>