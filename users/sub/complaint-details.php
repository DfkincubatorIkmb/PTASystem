```php
<?php
session_start();
include('../include/config.php');

// Check if sub-admin is logged in
if (strlen($_SESSION['subalogin']) == 0) {
    header('location:../index.php');
    exit();
}

// Check database connection
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get complaint ID
$id = isset($_GET['cid']) ? intval($_GET['cid']) : 0;

// Fetch complaint and user data using prepared statement
$stmt = $con->prepare("SELECT tblcomplaints.*, 
                              users.fullName AS fullName, 
                              users.icnumber AS icnumber,
                              users.userEmail AS userEmail,
                              users.contactNo AS contactno,
                              users.contactNo2 AS contactno2,
                              users.address AS address,
                              users.state AS state_user,
                              users.country AS country,
                              users.pincode AS pincode,
                              users.regDate AS userRegDate,
                              users.updationDate AS userUpdationDate,
                              users.status AS userStatus,
                              category.categoryName AS catname 
                       FROM tblcomplaints 
                       JOIN users ON users.id = tblcomplaints.userId 
                       JOIN category ON category.id = tblcomplaints.category 
                       WHERE tblcomplaints.complaintNumber = ?");
if (!$stmt) {
    die("Prepare failed: " . $con->error);
}
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("No complaint record found for complaint number: $id");
}
$row = $result->fetch_array();
// Debug: Check if fullName is present
if (empty($row['fullName'])) {
    echo "Debug: fullName is empty or not found for userId: " . (isset($row['userId']) ? $row['userId'] : 'N/A');
    exit;
}
$stmt->close();

// Fetch forward history
$stmt = $con->prepare("SELECT s.SubAdminName, s.Department, f.ForwadDate 
                       FROM tblforwardhistory f 
                       JOIN tblsubadmin s ON s.id = f.ForwardTo 
                       WHERE f.ComplaintNumber = ?");
if (!$stmt) {
    die("Prepare failed: " . $con->error);
}
$stmt->bind_param("i", $id);
$stmt->execute();
$forward_result = $stmt->get_result();
$forwardHistory = $forward_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch admin remarks
$stmt = $con->prepare("SELECT remark, status AS sstatus, remarkDate AS rdate, notetransport, checking 
                       FROM complaintremark 
                       WHERE complaintNumber = ?");
if (!$stmt) {
    die("Prepare failed: " . $con->error);
}
$stmt->bind_param("i", $id);
$stmt->execute();
$admin_result = $stmt->get_result();
$adminRemarks = $admin_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch sub-admin remarks
$stmt = $con->prepare("SELECT r.ComplainRemark, r.ComplainStatus, r.PostingDate, 
                              s.SubAdminName, s.Department, r.notetransport, r.checking 
                       FROM tblsubadminremark r 
                       JOIN tblsubadmin s ON s.id = r.RemarkBy 
                       WHERE r.ComplainNumber = ?");
if (!$stmt) {
    die("Prepare failed: " . $con->error);
}
$stmt->bind_param("i", $id);
$stmt->execute();
$subadmin_result = $stmt->get_result();
$subAdminRemarks = $subadmin_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sub-Admin | Complaint Details</title>
    <link type="text/css" href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="../bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="../css/theme.css" rel="stylesheet">
    <link type="text/css" href="../images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
    <script language="javascript" type="text/javascript">
        var popUpWin = 0;
        function popUpWindow(URLStr, left, top, width, height) {
            if (popUpWin) {
                if (!popUpWin.closed) popUpWin.close();
            }
            popUpWin = open(URLStr, 'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,copyhistory=yes,width=' + 600 + ',height=' + 600 + ',left=' + left + ', top=' + top + ',screenX=' + left + ',screenY=' + top);
        }
    </script>
</head>
<body>
    <?php include('include/header.php');?>

    <div class="wrapper">
        <div class="container">
            <div class="row">
                <?php include('include/sidebar.php');?>                
                <div class="span9">
                    <div class="content">
                        <div class="module">
                            <div class="module-head">
                                <h3>Repair Details</h3>
                            </div>
                            <div class="module-body table">
                                <table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped display" width="100%">
                                    <tbody>
                                        <tr>
                                            <td><b>Repair Number</b></td>
                                            <td><?php echo htmlentities($row['complaintNumber']);?></td>
                                            <td><b>Customer Name</b></td>
                                            <td><?php echo htmlentities($row['fullName']);?></td>
                                            <td><b>Reg Date</b></td>
                                            <td><?php echo htmlentities($row['regDate']);?></td>
                                        </tr>
                                        <tr>
                                            <td><b>Category</b></td>
                                            <td><?php echo htmlentities($row['catname']);?></td>
                                            <td><b>SubCategory</b></td>
                                            <td><?php echo htmlentities($row['subcategory']);?></td>
                                            <td><b>Report Type</b></td>
                                            <td><?php echo htmlentities($row['complaintType']);?></td>
                                        </tr>
                                        <tr>
                                            <td><b>Branch</b></td>
                                            <td><?php echo htmlentities($row['state']);?></td>
                                            <td><b>Problem</b></td>
                                            <td colspan="3"><?php echo htmlentities($row['complaintDetails']);?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td><b>Warranty</b></td>
                                            <td colspan="5">
                                                <?php 
                                                $cfile = $row['warrantyFile'];
                                                if ($cfile == "" || $cfile == "NULL") {
                                                    echo "File NA";
                                                } else { ?>
                                                    <a href="../../users/warrantydocs/<?php echo htmlentities($row['warrantyFile']);?>" target="_blank">View File</a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Receipt</b></td>
                                            <td colspan="5">
                                                <?php 
                                                $cfile = $row['receiptFile'];
                                                if ($cfile == "" || $cfile == "NULL") {
                                                    echo "File NA";
                                                } else { ?>
                                                    <a href="../../users/receiptdocs/<?php echo htmlentities($row['receiptFile']);?>" target="_blank">View File</a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Final Status</b></td>
                                            <td colspan="5" style="color:red">
                                                <?php 
                                                if ($row['status'] == "") {
                                                    echo "Not Processed Yet";
                                                } else {
                                                    echo htmlentities($row['status']);
                                                } ?>
                                            </td>
                                        </tr>

                                        <!-- Complaint Forward History -->
                                        <?php if (!empty($forwardHistory)) { ?>
                                            <?php foreach ($forwardHistory as $fwd) { ?>
                                                <tr>
                                                    <td><b>Forward to</b></td>
                                                    <td colspan="3"><?php echo htmlentities($fwd['SubAdminName']);?> - (<?php echo htmlentities($fwd['Department']);?>)</td>
                                                    <td><b>Forward Date</b></td>
                                                    <td><?php echo htmlentities($fwd['ForwadDate']);?></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>

                                        <!-- Admin Remarks -->
                                        <?php if (!empty($adminRemarks)) { ?>
                                            <?php foreach ($adminRemarks as $ar) { ?>
                                                <tr>
                                                    <td><b>Note Transport</b></td>
                                                    <td colspan="3"><?php echo htmlentities($ar['notetransport']); ?></td>
                                                    <th>Remark By:</th>
                                                    <td>Admin</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Checking</b></td>
                                                    <td colspan="3"><?php echo htmlentities($ar['checking']); ?></td>
                                                    <th>Remark By:</th>
                                                    <td>Admin</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Remark</b></td>
                                                    <td colspan="3"><?php echo htmlentities($ar['remark']); ?></td>
                                                    <th>Remark By:</th>
                                                    <td>Admin</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Status</b></td>
                                                    <td colspan="3"><?php echo htmlentities($ar['sstatus']); ?></td>
                                                    <th>Remark Date:</th>
                                                    <td><?php echo htmlentities($ar['rdate']); ?></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>

                                        <!-- Sub-Admin Remarks -->
                                        <?php if (!empty($subAdminRemarks)) { ?>
                                            <?php foreach ($subAdminRemarks as $sar) { ?>
                                                <tr>
                                                    <td><b>Note Transport</b></td>
                                                    <td colspan="3"><?php echo htmlentities($sar['notetransport']); ?></td>
                                                    <th>Remark By:</th>
                                                    <td><?php echo htmlentities($sar['SubAdminName']); ?> - (<?php echo htmlentities($sar['Department']); ?>)</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Checking</b></td>
                                                    <td colspan="3"><?php echo htmlentities($sar['checking']); ?></td>
                                                    <th>Remark By:</th>
                                                    <td><?php echo htmlentities($sar['SubAdminName']); ?> - (<?php echo htmlentities($sar['Department']); ?>)</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Remark</b></td>
                                                    <td colspan="3"><?php echo htmlentities($sar['ComplainRemark']); ?></td>
                                                    <th>Remark By:</th>
                                                    <td><?php echo htmlentities($sar['SubAdminName']); ?> - (<?php echo htmlentities($sar['Department']); ?>)</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Status</b></td>
                                                    <td colspan="3"><?php echo htmlentities($sar['ComplainStatus']); ?></td>
                                                    <th>Remark Date:</th>
                                                    <td><?php echo htmlentities($sar['PostingDate']); ?></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>

                                        <!-- Action Buttons -->
                                        <tr>
                                            <td><b>Action</b></td>
                                            <td colspan="5">
                                                <?php 
                                                if ($row['status'] != 'closed') {
                                                    $cno = $id;
                                                    $stmt = $con->prepare("SELECT id 
                                                                           FROM tblforwardhistory 
                                                                           JOIN tblcomplaints ON tblcomplaints.complaintNumber = tblforwardhistory.ComplaintNumber 
                                                                           WHERE tblforwardhistory.ComplaintNumber = ? 
                                                                           AND (tblcomplaints.status = 'in process' OR tblcomplaints.status = '' OR tblcomplaints.status IS NULL)");
                                                    if ($stmt) {
                                                        $stmt->bind_param("i", $cno);
                                                        $stmt->execute();
                                                        $result = $stmt->get_result();
                                                        if ($result->num_rows == 0) { ?>
                                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Forward To</button>
                                                        <?php }
                                                        $stmt->close();
                                                    }
                                                } ?>

                                                <?php if ($row['status'] != "closed") { ?>
                                                    <a href="javascript:void(0);" onClick="popUpWindow('updatecomplaint.php?cid=<?php echo htmlentities($row['complaintNumber']);?>');" title="Update order">
                                                        <button type="button" class="btn btn-primary">Take Action</button>
                                                    </a>
                                                <?php } ?>

                                                <a href="javascript:void(0);" onClick="popUpWindow('userprofile.php?uid=<?php echo htmlentities($row['userId']);?>');">
                                                    <button type="button" class="btn btn-primary">View User Details</button>
                                                </a>

                                                <a href="print_repair.php?id=<?php echo htmlentities($row['complaintNumber']);?>" target="_blank">
                                                    <button type="button" class="btn btn-danger">Download To PDF</button>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div><!--/.content-->
                </div><!--/.span9-->
            </div>
        </div><!--/.container-->
    </div><!--/.wrapper-->

    <!-- Complaint Forward To Modal -->
    <form name="forwardto" method="post">
        <div id="myModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Complaint Number# <?php echo htmlentities($id);?></h4>
                    </div>
                    <div class="modal-body">
                        <label class="control-label" for="forwardto"><strong>Forward To</strong></label>
                        <p>
                            <select class="span4 tip" name="forwardto" required="true">
                                <option value="">Select SubAdmin/Subordinate</option>
                                <?php 
                                $ret = mysqli_query($con, "SELECT id, SubAdminName, Department FROM tblsubadmin");
                                while ($row_subadmin = mysqli_fetch_array($ret)) { ?>
                                    <option value="<?php echo $row_subadmin['id'];?>"><?php echo htmlentities($row_subadmin['SubAdminName']);?> (<?php echo htmlentities($row_subadmin['Department']);?>)</option>
                                <?php } ?>
                            </select>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" name="fwdsubmit">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- Complaint Forward To End -->

    <?php include('include/footer.php');?>

    <script src="../scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="../scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
    <script src="../bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="../scripts/flot/jquery.flot.js" type="text/javascript"></script>
    <script src="../scripts/datatables/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function() {
            $('.datatable-1').dataTable();
            $('.dataTables_paginate').addClass("btn-group datatable-pagination");
            $('.dataTables_paginate > a').wrapInner('<span />');
            $('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
            $('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');
        });
    </script>
</body>
</html>
```