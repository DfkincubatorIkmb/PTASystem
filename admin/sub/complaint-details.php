<?php
session_start();
include('../include/config.php');

// Ensure user is logged in as sub-admin
if (empty($_SESSION['subalogin'])) {
    header('location:../index.php');
    exit;
}

// Get Complaint ID from URL
if (!isset($_GET['cid']) || empty($_GET['cid'])) {
    die("Invalid Complaint ID.");
}
$cid = intval($_GET['cid']);

// SQL to fetch complaint details with joins
$sql = "SELECT 
            tblcomplaints.*,
            users.fullName AS name,
            users.icnumber AS icnumber,
            category.categoryName AS catname 
        FROM tblcomplaints
        JOIN users ON users.id = tblcomplaints.userId
        JOIN category ON category.id = tblcomplaints.category
        WHERE tblcomplaints.complaintNumber = ?";

$stmt = $con->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $con->error);
}
$stmt->bind_param("s", $cid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No complaint record found.");
}
$row = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sub-Admin | Complaint Details</title>
    <link rel="icon" href="../logopta.png" type="image/png">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap-responsive.min.css">
    <link rel="stylesheet" href="../css/theme.css">
    <link rel="stylesheet" href="../images/icons/css/font-awesome.css">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
    <style>
        .remark-history th, .remark-history td {
            padding: 8px;
            text-align: left;
        }
        .remark-history th {
            background-color: #f2f2f2;
        }
        .remark-history tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
    <script>
        var popUpWin = 0;
        function popUpWindow(URLStr, left, top, width, height) {
            if (popUpWin && !popUpWin.closed) popUpWin.close();
            popUpWin = open(URLStr, 'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=' + width + ',height=' + height + ',left=' + left + ',top=' + top);
        }
    </script>
</head>
<body>
<?php include('include/header.php'); ?>

<div class="wrapper">
    <div class="container">
        <div class="row">
            <?php include('include/sidebar.php'); ?>
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
<td><b>Repair Number</b></td><td><?php echo htmlentities($row['complaintNumber']); ?></td>
<td><b>Reg Date</b></td><td><?php echo htmlentities($row['regDate']); ?></td>
</tr>
                                    <tr>
<td><b>Customer Name</b></td><td><?php echo htmlentities($row['name']); ?></td>
<td><b>IC Number</b></td><td colspan="5"><?php echo htmlentities($row['icnumber']); ?></td>
</tr>
                                    <tr>
                                        <td><b>Category</b></td>
                                        <td><?php echo htmlentities($row['catname']); ?></td>
                                        <td><b>SubCategory</b></td>
                                        <td><?php echo htmlentities($row['subcategory']); ?></td>
                                        <td><b>Warranty Type</b></td>
                                        <td><?php echo htmlentities($row['complaintType']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Purchase</b></td>
                                        <td><?php echo htmlentities($row['state']); ?></td>
                                        <td><b>Brand Name</b></td>
                                        <td><?php echo htmlentities($row['brandname']); ?></td>
                                        <td><b>Model No</b></td>
                                        <td><?php echo htmlentities($row['modelNo']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Damage</b></td>
                                        <td colspan="5"><?php echo htmlentities($row['complaintDetails']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Warranty</b></td>
                                        <td colspan="5">
                                            <?php 
                                            $cfile = $row['warrantyFile'];
                                            echo (empty($cfile) || $cfile == "NULL") ? "File NA" : '<a href="../users/warrantydocs/' . htmlentities($cfile) . '" target="_blank">View File</a>';
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Receipt</b></td>
                                        <td colspan="5">
                                            <?php 
                                            $cfile = $row['receiptFile'];
                                            echo (empty($cfile) || $cfile == "NULL") ? "File NA" : '<a href="../users/receiptdocs/' . htmlentities($cfile) . '" target="_blank">View File</a>';
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Final Status</b></td>
                                        <td colspan="5" style="color:red;">
                                            <?php echo empty($row['status']) ? "Not Processed Yet" : htmlentities($row['status']); ?>
                                        </td>
                                    </tr>

                                    <!-- Forward History -->
                                    <?php
                                    $qry = mysqli_query($con, "SELECT tblsubadmin.SubAdminName, tblsubadmin.Department, tblforwardhistory.ForwadDate 
                                        FROM tblforwardhistory 
                                        JOIN tblsubadmin ON tblsubadmin.id = tblforwardhistory.ForwardTo 
                                        WHERE tblforwardhistory.ComplaintNumber = '$cid'");
                                    while ($result = mysqli_fetch_assoc($qry)) {
                                    ?>
                                        <tr>
                                            <td><b>Forward to</b></td>
                                            <td colspan="3"><?php echo htmlentities($result['SubAdminName']); ?> (<?php echo htmlentities($result['Department']); ?>)</td>
                                            <td><b>Forward Date</b></td>
                                            <td><?php echo htmlentities($result['ForwadDate']); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>

                            <!-- Admin Remarks History -->
                            <?php
                            $table_check = mysqli_query($con, "SHOW TABLES LIKE 'complaintremark'");
                            if (mysqli_num_rows($table_check) > 0) {
                                $ret = mysqli_query($con, "SELECT id, remark, status AS sstatus, remarkDate AS rdate, notetransport, checking 
                                    FROM complaintremark 
                                    WHERE complaintNumber = '$cid'
                                    ORDER BY rdate DESC");
                                if (mysqli_num_rows($ret) > 0) {
                            ?>
                                    <h3>Admin Remark History</h3>
                                    <table class="remark-history table table-bordered" width="100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Status</th>
                                                <th>Note Transport</th>
                                                <th>Checking</th>
                                                <th>Remark</th>
                                                <th>Remark Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $cnt = 1;
                                            while ($rw = mysqli_fetch_assoc($ret)) {
                                            ?>
                                                <tr>
                                                    <td><?php echo $cnt; ?></td>
                                                    <td><?php echo htmlentities($rw['sstatus']); ?></td>
                                                    <td><?php echo htmlentities($rw['notetransport']); ?></td>
                                                    <td><?php echo htmlentities($rw['checking']); ?></td>
                                                    <td><?php echo htmlentities($rw['remark']); ?></td>
                                                    <td><?php echo htmlentities($rw['rdate']); ?></td>
                                                </tr>
                                            <?php
                                                $cnt++;
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                            <?php
                                } else {
                                    echo "<p>No admin remarks found.</p>";
                                }
                            }
                            ?>

                            <!-- Sub-Admin Remarks History -->
                            <?php
                            $table_check = mysqli_query($con, "SHOW TABLES LIKE 'tblsubadminremark'");
                            if (mysqli_num_rows($table_check) > 0) {
                                $ret1 = mysqli_query($con, "SELECT tblsubadminremark.id, tblsubadminremark.ComplainRemark, tblsubadminremark.ComplainStatus, 
                                    tblsubadminremark.PostingDate, tblsubadminremark.notetransport, tblsubadminremark.checking,
                                    tblsubadmin.SubAdminName, tblsubadmin.Department
                                    FROM tblsubadminremark 
                                    JOIN tblsubadmin ON tblsubadmin.id = tblsubadminremark.RemarkBy 
                                    WHERE tblsubadminremark.ComplainNumber = '$cid'
                                    ORDER BY tblsubadminremark.PostingDate DESC");
                                if (mysqli_num_rows($ret1) > 0) {
                            ?>
                                    <h3>Sub-Admin Remark History</h3>
                                    <table class="remark-history table table-bordered" width="100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Status</th>
                                                <th>Note Transport</th>
                                                <th>Checking</th>
                                                <th>Remark</th>
                                                <th>Remark By</th>
                                                <th>Remark Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $cnt = 1;
                                            while ($rww = mysqli_fetch_assoc($ret1)) {
                                            ?>
                                                <tr>
                                                    <td><?php echo $cnt; ?></td>
                                                    <td><?php echo htmlentities($rww['ComplainStatus']); ?></td>
                                                    <td><?php echo htmlentities($rww['notetransport']); ?></td>
                                                    <td><?php echo htmlentities($rww['checking']); ?></td>
                                                    <td><?php echo htmlentities($rww['ComplainRemark']); ?></td>
                                                    <td><?php echo htmlentities($rww['SubAdminName']); ?> (<?php echo htmlentities($rww['Department']); ?>)</td>
                                                    <td><?php echo htmlentities($rww['PostingDate']); ?></td>
                                                </tr>
                                            <?php
                                                $cnt++;
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                            <?php
                                } else {
                                    echo "<p>No sub-admin remarks found.</p>";
                                }
                            }
                            ?>

                            <table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped display" width="100%">
                                <tbody>
                                    <tr>
                                        <td><b>Action</b></td>
                                        <td colspan="5">
                                            <?php if ($row['status'] != 'closed') { ?>
                                                <?php
                                                $sql = mysqli_query($con, "SELECT id FROM tblforwardhistory 
                                                    JOIN tblcomplaints ON tblcomplaints.complaintNumber = tblforwardhistory.ComplaintNumber
                                                    WHERE tblforwardhistory.ComplaintNumber = '$cid' 
                                                    AND (tblcomplaints.status = 'in process' OR tblcomplaints.status = '' OR tblcomplaints.status IS NULL)");
                                                if (mysqli_num_rows($sql) == 0) {
                                                ?>
                                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Forward To</button>
                                                <?php } ?>
                                                <a href="javascript:void(0);" onClick="popUpWindow('updatecomplaint.php?cid=<?php echo htmlentities($row['complaintNumber']); ?>',0,0,600,600)">
                                                    <button type="button" class="btn btn-primary">Take Action</button>
                                                </a>
                                            <?php } ?>
                                            <a href="javascript:void(0);" onClick="popUpWindow('userprofile.php?uid=<?php echo htmlentities($row['userId']); ?>',0,0,600,600)">
                                                <button type="button" class="btn btn-primary">View User Details</button>
                                            </a>
                                            <a href="download.php?id=<?php echo htmlentities($row['complaintNumber']); ?>" target="_blank">
                                                <button type="button" class="btn btn-danger">Download PDF</button>
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

<!-- Forward Modal -->
<form name="forwardto" method="post">
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Complaint Number# <?php echo htmlentities($_GET['cid']); ?></h4>
                </div>
                <div class="modal-body">
                    <label class="control-label" for="forwardto"><strong>Forward To</strong></label>
                    <p>
                        <select class="span4 tip" name="forwardto" required="true">
                            <option value="">Select SubAdmin/Subordinate</option>
                            <?php
                            $ret = mysqli_query($con, "SELECT id, SubAdminName, Department FROM tblsubadmin");
                            while ($row = mysqli_fetch_assoc($ret)) {
                                echo '<option value="' . $row['id'] . '">' . $row['SubAdminName'] . ' (' . $row['Department'] . ')</option>';
                            }
                            ?>
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
<!-- Forward Modal End -->

<?php include('include/footer.php'); ?>

<script src="../scripts/jquery-1.9.1.min.js"></script>
<script src="../scripts/jquery-ui-1.10.1.custom.min.js"></script>
<script src="../bootstrap/js/bootstrap.min.js"></script>
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