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

// Get update count for this complaint (sub-admin remarks)
$updateCount = 0;
$countQuery = mysqli_query($con, "SELECT COUNT(*) as update_count FROM tblsubadminremark WHERE ComplainNumber='$cid' AND RemarkBy='{$_SESSION['suid']}'");
$countResult = mysqli_fetch_assoc($countQuery);
$updateCount = $countResult['update_count'];

// Handle form submission
if (isset($_POST['update'])) {
    $complaintnumber = $cid;
    $status = $_POST['status'];
    $notetransport = mysqli_real_escape_string($con, $_POST['notetransport']);
    $checking = mysqli_real_escape_string($con, $_POST['checking']);
    $remark = mysqli_real_escape_string($con, $_POST['remark']);
    $sadminid = $_SESSION['suid'];
    $edit_remark_id = !empty($_POST['edit_remark_id']) ? intval($_POST['edit_remark_id']) : 0;

    // Validate input
    if (empty($status) || empty($notetransport) || empty($checking) || empty($remark)) {
        echo "<script>alert('All fields are required.');</script>";
    } else {
        if ($edit_remark_id > 0) {
            // Update existing remark
            $query = mysqli_query($con, "UPDATE tblsubadminremark 
                SET ComplainStatus='$status', notetransport='$notetransport', checking='$checking', ComplainRemark='$remark', PostingDate=NOW()
                WHERE id='$edit_remark_id' AND ComplainNumber='$complaintnumber' AND RemarkBy='$sadminid'");
            
            // Update status in tblcomplaints
            $sql = mysqli_query($con, "UPDATE tblcomplaints SET status='$status' WHERE complaintNumber='$complaintnumber'");

            if ($query && $sql) {
                echo "<script>alert('Remark updated successfully'); window.close();</script>";
            } else {
                echo "<script>alert('Failed to update remark');</script>";
            }
        } else {
            if ($updateCount >= 3) {
                echo "<script>alert('Update limit reached (3 times only)');</script>";
            } else {
                // Insert new remark
                $query = mysqli_query($con, "INSERT INTO tblsubadminremark (ComplainNumber, ComplainStatus, notetransport, checking, ComplainRemark, RemarkBy, PostingDate)
                    VALUES ('$complaintnumber', '$status', '$notetransport', '$checking', '$remark', '$sadminid', NOW())");

                // Update status in tblcomplaints
                $sql = mysqli_query($con, "UPDATE tblcomplaints SET status='$status' WHERE complaintNumber='$complaintnumber'");

                if ($query && $sql) {
                    echo "<script>alert('Complaint details updated successfully'); window.close();</script>";
                } else {
                    echo "<script>alert('Failed to update complaint details');</script>";
                }
            }
        }
    }
}

// Handle delete request
if (isset($_GET['delete']) && isset($_GET['remark_id']) && $complaintnumber) {
    $remark_id = intval($_GET['remark_id']);
    $deleteQuery = mysqli_query($con, "DELETE FROM tblsubadminremark WHERE id='$remark_id' AND ComplainNumber='$complaintnumber' AND RemarkBy='{$_SESSION['suid']}'");
    if ($deleteQuery) {
        echo "<script>alert('Remark deleted successfully');</script>";
    } else {
        echo "<script>alert('Failed to delete remark');</script>";
    }
}

// Fetch complaint details to display
$sql = "SELECT complaintNumber FROM tblcomplaints WHERE complaintNumber = ?";
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
    <title>Sub-Admin | Update Complaint</title>
    <link rel="icon" href="../logopta.png" type="image/png">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap-responsive.min.css">
    <link rel="stylesheet" href="../css/theme.css">
    <link rel="stylesheet" href="../images/icons/css/font-awesome.css">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
    <style>
        .module-body {
            padding: 20px;
        }
        .form-horizontal .control-label {
            font-weight: bold;
        }
        .form-horizontal .controls {
            margin-left: 180px;
        }
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
        function closeWindow() {
            window.close();
        }

        function checkUpdateLimit() {
            var updateCount = <?php echo $updateCount; ?>;
            var editRemarkId = document.getElementById('edit_remark_id').value;
            if (editRemarkId === '') {
                if (updateCount >= 3) {
                    alert('Update limit reached (3 times only)');
                    return false;
                }
            }
            return true;
        }

        function confirmDelete(remarkId) {
            return confirm('Are you sure you want to delete this remark?');
        }

        function editRemark(remarkId, notetransport, checking, remark, status) {
            document.getElementById('notetransport').value = notetransport;
            document.getElementById('checking').value = checking;
            document.getElementById('remark').value = remark;
            document.getElementById('status').value = status;
            document.getElementById('edit_remark_id').value = remarkId;
        }
    </script>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="row">
                <div class="span9">
                    <div class="content">
                        <div class="module">
                            <div class="module-head">
                                <h3>Update Complaint #<?php echo htmlentities($row['complaintNumber']); ?></h3>
                            </div>
                            <div class="module-body">
                                <form name="updateticket" id="updatecomplaint" method="post" class="form-horizontal" onsubmit="return checkUpdateLimit();">
                                    <div class="control-group">
                                        <label class="control-label">Repair Number</label>
                                        <div class="controls">
                                            <input type="text" value="<?php echo htmlentities($row['complaintNumber']); ?>" class="span4" readonly>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Status</label>
                                        <div class="controls">
                                            <select name="status" id="status" class="span4" required>
                                                <option value="">Select Status</option>
                                                <option value="in process">In Process</option>
                                                <option value="closed">Closed</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Note Transport</label>
                                        <div class="controls">
                                            <textarea name="notetransport" id="notetransport" class="span4" rows="5" required></textarea>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Checking</label>
                                        <div class="controls">
                                            <textarea name="checking" id="checking" class="span4" rows="5" required></textarea>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Remark</label>
                                        <div class="controls">
                                            <textarea name="remark" id="remark" class="span4" rows="5" required></textarea>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <div class="controls">
                                            <input type="hidden" name="edit_remark_id" id="edit_remark_id" value="">
                                            <button type="submit" name="update" class="btn btn-primary">Submit</button>
                                            <button type="button" class="btn btn-default" onclick="closeWindow()">Close</button>
                                        </div>
                                    </div>
                                </form>

                                <!-- Sub-Admin Remark History -->
                                <h3>Sub-Admin Remark History</h3>
                                <table class="remark-history table table-bordered" width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Status</th>
                                            <th>Note Transport</th>
                                            <th>Checking</th>
                                            <th>Remark</th>
                                            <th>Remark Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = mysqli_query($con, "SELECT id, ComplainStatus, notetransport, checking, ComplainRemark, PostingDate 
                                            FROM tblsubadminremark 
                                            WHERE ComplainNumber='$cid' AND RemarkBy='{$_SESSION['suid']}'
                                            ORDER BY PostingDate DESC");
                                        $cnt = 1;
                                        while ($row = mysqli_fetch_assoc($query)) {
                                        ?>
                                            <tr>
                                                <td><?php echo $cnt; ?></td>
                                                <td><?php echo htmlentities($row['ComplainStatus']); ?></td>
                                                <td><?php echo htmlentities($row['notetransport']); ?></td>
                                                <td><?php echo htmlentities($row['checking']); ?></td>
                                                <td><?php echo htmlentities($row['ComplainRemark']); ?></td>
                                                <td><?php echo htmlentities($row['PostingDate']); ?></td>
                                                <td>
                                                    <a href="javascript:void(0);" onclick="editRemark('<?php echo $row['id']; ?>', '<?php echo addslashes(htmlentities($row['notetransport'])); ?>', '<?php echo addslashes(htmlentities($row['checking'])); ?>', '<?php echo addslashes(htmlentities($row['ComplainRemark'])); ?>', '<?php echo htmlentities($row['ComplainStatus']); ?>')" class="btn btn-primary">Edit</a>
                                                    <a href="?cid=<?php echo $cid; ?>&delete=1&remark_id=<?php echo $row['id']; ?>" onclick="return confirmDelete(<?php echo $row['id']; ?>);" class="btn btn-danger">Delete</a>
                                                </td>
                                            </tr>
                                        <?php
                                            $cnt++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div><!--/.content-->
                </div><!--/.span9-->
            </div>
        </div><!--/.container-->
    </div><!--/.wrapper-->

    <script src="../scripts/jquery-1.9.1.min.js"></script>
    <script src="../scripts/jquery-ui-1.10.1.custom.min.js"></script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
</body>
</html>