<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('includes/config.php');

if (strlen($_SESSION['login']) == 0) { 
    header('location:index.php');
    exit();
}

// Query utama untuk butiran aduan
$sql = "SELECT tblcomplaints.*, users.fullName AS fullName, users.icnumber AS icnumber, category.categoryName AS catname 
        FROM tblcomplaints 
        JOIN users ON users.id = tblcomplaints.userId 
        JOIN category ON category.id = tblcomplaints.category 
        WHERE tblcomplaints.userId = ? AND tblcomplaints.complaintNumber = ?";
$stmt = $con->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $con->error);
}
$stmt->bind_param("is", $_SESSION['id'], $_GET['cid']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if (!$row) {
    $errormsg = "❌ Invalid or unauthorized complaint number.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="PTA Customer Complaint System">
    <meta name="author" content="Dashboard">
    <meta name="keyword" content="Complaint, Customer, PTA">

    <title>E-CARE | User Repair Details</title>

    <link rel="icon" href="logopta.png" type="image/png">

    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/style-responsive.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        body {
            background-color: #ffffff;
        }
        .table {
            background-color: #fff;
            border-radius: 5px;
        }
        .table th, .table td {
            padding: 12px;
            vertical-align: middle;
        }
        .form-panel {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .alert {
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <section id="container">
        <?php include('includes/header.php'); ?>
        <?php include('includes/sidebar.php'); ?>
        <section id="main-content">
            <section class="wrapper site-min-height">
                <h3><i class="fa fa-angle-right"></i> Repair Progress Report</h3>
                <hr />

                <div class="row mt">
                    <div class="col-lg-12">
                        <div class="form-panel">
                            <?php if (isset($errormsg)) { ?>
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <b>Error!</b> <?php echo htmlentities($errormsg); ?>
                                </div>
                            <?php } ?>

                            <?php if ($row) { ?>
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <td><b>Repair Number</b></td>
                                        <td><?php echo htmlentities($row['complaintNumber']); ?></td>
                                        <td><b>Reg Date</b></td>
                                        <td><?php echo htmlentities($row['regDate']); ?></td>
                                        <td><b>Final Status</b></td>
                                        <td style="color:<?php echo $row['status'] == 'Closed' ? 'green' : 'red'; ?>">
                                            <?php echo $row['status'] == "" ? "Not Process Yet" : htmlentities($row['status']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Report Number</b></td>
                                        <td colspan="5"><?php echo htmlentities($row['reportNumber']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Customer Name</b></td>
                                        <td><?php echo htmlentities($row['fullName']); ?></td>
                                        <td><b>IC Number</b></td>
                                        <td colspan="3"><?php echo htmlentities($row['icnumber']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Category</b></td>
                                        <td><?php echo htmlentities($row['catname']); ?></td>
                                        <td><b>SubCategory</b></td>
                                        <td><?php echo htmlentities($row['subcategory']); ?></td>
                                        <td><b>Report Type</b></td>
                                        <td><?php echo htmlentities($row['complaintType']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Branch</b></td>
                                        <td><?php echo htmlentities($row['state']); ?></td>
                                        <td><b>Problem</b></td>
                                        <td colspan="3"><?php echo htmlentities($row['complaintDetails']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Warranty</b></td>
                                        <td colspan="5">
                                            <?php if ($row['warrantyFile'] == "" || $row['warrantyFile'] == "NULL") {
                                                echo "File NA";
                                            } else { ?>
                                                <a href="warrantydocs/<?php echo htmlentities($row['warrantyFile']); ?>" target="_blank">View File</a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Receipt</b></td>
                                        <td colspan="5">
                                            <?php if ($row['receiptFile'] == "" || $row['receiptFile'] == "NULL") {
                                                echo "File NA";
                                            } else { ?>
                                                <a href="receiptdocs/<?php echo htmlentities($row['receiptFile']); ?>" target="_blank">View File</a>
                                            <?php } ?>
                                        </td>
                                    </tr>

                                    <!-- Complaint forward history -->
                                    <?php
                                    $cmpno = intval($_GET['cid']);
                                    $qry = $con->prepare("SELECT tblsubadmin.SubAdminName, tblsubadmin.Department, tblforwardhistory.ForwadDate 
                                                          FROM tblforwardhistory 
                                                          JOIN tblsubadmin ON tblsubadmin.id = tblforwardhistory.ForwardTo 
                                                          WHERE tblforwardhistory.ComplaintNumber = ?");
                                    $qry->bind_param("i", $cmpno);
                                    $qry->execute();
                                    $forward_result = $qry->get_result();
                                    while ($result = $forward_result->fetch_assoc()) { ?>
                                        <tr>
                                            <td><b>Forward to</b></td>
                                            <td colspan="3"><?php echo htmlentities($result['SubAdminName']); ?> - (<?php echo htmlentities($result['Department']); ?>)</td>
                                            <td><b>Forward Date</b></td>
                                            <td><?php echo htmlentities($result['ForwadDate']); ?></td>
                                        </tr>
                                    <?php }
                                    $qry->close();
                                    ?>

                                    <!-- Admin Remarks -->
                                    <?php
                                    $ret = $con->prepare("SELECT complaintremark.remark AS remark, complaintremark.status AS sstatus, complaintremark.remarkDate AS rdate 
                                                          FROM complaintremark 
                                                          JOIN tblcomplaints ON tblcomplaints.complaintNumber = complaintremark.complaintNumber 
                                                          WHERE complaintremark.complaintNumber = ?");
                                    $ret->bind_param("i", $cmpno);
                                    $ret->execute();
                                    $admin_remark_result = $ret->get_result();
                                    while ($rw = $admin_remark_result->fetch_assoc()) { ?>
                                        <tr>
                                            <td><b>Remark</b></td>
                                            <td colspan="3"><?php echo htmlentities($rw['remark']); ?></td>
                                            <th>Remark By:</th>
                                            <td>Admin</td>
                                        </tr>
                                        <tr>
                                            <td><b>Status</b></td>
                                            <td colspan="3"><?php echo htmlentities($rw['sstatus']); ?></td>
                                            <th>Remark Date:</th>
                                            <td><?php echo htmlentities($rw['rdate']); ?></td>
                                        </tr>
                                    <?php }
                                    $ret->close();
                                    ?>

                                    <!-- Sub-admin Remarks -->
                                    <?php
                                    $ret1 = $con->prepare("SELECT tblsubadminremark.ComplainRemark, tblsubadminremark.ComplainStatus, tblsubadminremark.PostingDate, 
                                                          tblsubadmin.SubAdminName, tblsubadmin.Department 
                                                          FROM tblsubadminremark 
                                                          JOIN tblcomplaints ON tblcomplaints.complaintNumber = tblsubadminremark.ComplainNumber 
                                                          JOIN tblsubadmin ON tblsubadmin.id = tblsubadminremark.RemarkBy 
                                                          WHERE tblsubadminremark.ComplainNumber = ?");
                                    $ret1->bind_param("i", $cmpno);
                                    $ret1->execute();
                                    $subadmin_remark_result = $ret1->get_result();
                                    while ($rww = $subadmin_remark_result->fetch_assoc()) { ?>
                                        <tr>
                                            <td><b>Remark</b></td>
                                            <td colspan="3"><?php echo htmlentities($rww['ComplainRemark']); ?></td>
                                            <th>Remark By:</th>
                                            <td><?php echo htmlentities($rww['SubAdminName']); ?> - (<?php echo htmlentities($rww['Department']); ?>)</td>
                                        </tr>
                                        <tr>
                                            <td><b>Status</b></td>
                                            <td colspan="3"><?php echo htmlentities($rww['ComplainStatus']); ?></td>
                                            <th>Remark Date:</th>
                                            <td><?php echo htmlentities($rww['PostingDate']); ?></td>
                                        </tr>
                                    <?php }
                                    $ret1->close();
                                    ?>
                                </table>
                            <?php } else { ?>
                                <p>No complaint details found.</p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </section>
        </section>
        <?php include('includes/footer.php'); ?>
    </section>

    <!-- js placed at the end of the document so the pages load faster -->
    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery-ui-1.9.2.custom.min.js"></script>
    <script src="assets/js/jquery.ui.touch-punch.min.js"></script>
    <script class="include" type="text/javascript" src="assets/js/jquery.dcjqaccordion.2.7.js"></script>
    <script src="assets/js/jquery.scrollTo.min.js"></script>
    <script src="assets/js/jquery.nicescroll.js" type="text/javascript"></script>

    <!--common script for all pages-->
    <script src="assets/js/common-scripts.js"></script>

    <script>
        $(function(){
            $('select.styled').customSelect();
        });
    </script>
</body>
</html>