<?php
session_start();
include('include/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}

// Get update count for this complaint // DITAMBAH: Added to enforce 3-update limit
$complaintnumber = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
$updateCount = 0;

if ($complaintnumber) {
    $countQuery = mysqli_query($con, "SELECT COUNT(*) as update_count FROM complaintremark WHERE complaintNumber='$complaintnumber'"); // DITAMBAH: Query to count existing remarks
    $countResult = mysqli_fetch_assoc($countQuery);
    $updateCount = $countResult['update_count'];
}

if (isset($_POST['update'])) {
    $status = $_POST['status'];
    $notetransport = mysqli_real_escape_string($con, $_POST['notetransport']); // DIUBAH: Added sanitization
    $checking = mysqli_real_escape_string($con, $_POST['checking']); // DIUBAH: Added sanitization
    $remark = mysqli_real_escape_string($con, $_POST['remark']); // DIUBAH: Added sanitization
    $edit_remark_id = !empty($_POST['edit_remark_id']) ? intval($_POST['edit_remark_id']) : 0; // DITAMBAH: Check if editing existing remark

    if ($complaintnumber) {
        if ($edit_remark_id > 0) { // DITAMBAH: Update existing remark if edit_remark_id is provided
            $query = mysqli_query($con, "UPDATE complaintremark SET 
                status='$status', 
                notetransport='$notetransport', 
                checking='$checking', 
                remark='$remark', 
                remarkDate=NOW() 
                WHERE id='$edit_remark_id' AND complaintNumber='$complaintnumber'");
            
            // Update status in tblcomplaints
            $sql = mysqli_query($con, "UPDATE tblcomplaints SET status='$status' WHERE complaintNumber='$complaintnumber'");

            if ($query && $sql) {
                echo "<script>alert('Remark updated successfully');</script>";
            } else {
                echo "<script>alert('Failed to update remark');</script>";
            }
        } else {
            if ($updateCount >= 3) { // DITAMBAH: Enforce 3-update limit for new inserts
                echo "<script>alert('Update limit reached (3 times only)');</script>";
            } else {
                // Insert new record to preserve history // DIUBAH: Only insert if not editing
                $query = mysqli_query($con, "INSERT INTO complaintremark (complaintNumber, status, notetransport, checking, remark, remarkDate)
                    VALUES ('$complaintnumber', '$status', '$notetransport', '$checking', '$remark', NOW())");

                // Update status in tblcomplaints
                $sql = mysqli_query($con, "UPDATE tblcomplaints SET status='$status' WHERE complaintNumber='$complaintnumber'");

                if ($query && $sql) { // DITAMBAH: Added success check
                    echo "<script>alert('Complaint details updated successfully');</script>";
                } else {
                    echo "<script>alert('Failed to update complaint details');</script>"; // DITAMBAH: Error handling
                }
            }
        }
    }
}

// Handle delete request // DITAMBAH: Added delete functionality
if (isset($_GET['delete']) && isset($_GET['remark_id']) && $complaintnumber) {
    $remark_id = intval($_GET['remark_id']);
    $deleteQuery = mysqli_query($con, "DELETE FROM complaintremark WHERE id='$remark_id' AND complaintNumber='$complaintnumber'");
    if ($deleteQuery) {
        echo "<script>alert('Remark deleted successfully');</script>";
    } else {
        echo "<script>alert('Failed to delete remark');</script>"; // DITAMBAH: Error handling
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>E-CARE Update Complaint - Admin</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<link href="anuj.css" rel="stylesheet" type="text/css">
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
</style> <!-- DITAMBAH: Added inline CSS for history table -->
<script>
function f2() { window.close(); }
function f3() { window.print(); }

function checkUpdateLimit() { // DITAMBAH: JavaScript to enforce 3-update limit
    var updateCount = <?php echo $updateCount; ?>;
    var editRemarkId = document.getElementById('edit_remark_id').value;
    if (editRemarkId === '') { // DITAMBAH: Only check limit for new inserts, not edits
        if (updateCount >= 3) {
            alert('Update limit reached (3 times only)');
            return false;
        }
    }
    return true;
}

function confirmDelete(remarkId) { // DITAMBAH: JavaScript for delete confirmation
    return confirm('Are you sure you want to delete this remark?');
}

function editRemark(remarkId, notetransport, checking, remark, status) { // DITAMBAH: JavaScript for editing remarks
    document.getElementById('notetransport').value = notetransport;
    document.getElementById('checking').value = checking;
    document.getElementById('remark').value = remark;
    document.getElementById('status').value = status;
    document.getElementById('edit_remark_id').value = remarkId;
}
</script>
</head>
<body>
<div style="margin-left:50px;">
<form name="updateticket" id="updatecomplaint" method="post" onsubmit="return checkUpdateLimit();"> <!-- DIUBAH: Added onsubmit to enforce update limit -->
<table>
    <tr>
        <td><b>Repair Number</b></td>
        <td><?php echo htmlentities($_GET['cid']); ?></td>
    </tr>
    <tr>
        <td><b>Status</b></td>
        <td>
            <select name="status" id="status" required> <!-- DIUBAH: Added id for edit functionality -->
                <option value="">Select Status</option>
                <option value="in process">In Process</option>
                <option value="closed">Closed</option>
            </select>
        </td>
    </tr>
    <tr>
        <td><b>Note Transport</b></td>
        <td><textarea name="notetransport" id="notetransport" cols="50" rows="5" required></textarea></td> <!-- DIUBAH: Added id for edit functionality -->
    </tr>
    <tr>
        <td><b>Checking</b></td>
        <td><textarea name="checking" id="checking" cols="50" rows="5" required></textarea></td> <!-- DIUBAH: Added id for edit functionality -->
    </tr>
    <tr>
        <td><b>Remark</b></td>
        <td><textarea name="remark" id="remark" cols="50" rows="5" required></textarea></td> <!-- DIUBAH: Added id for edit functionality -->
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>
            <input type="hidden" name="edit_remark_id" id="edit_remark_id" value=""> <!-- DITAMBAH: Hidden input for edit functionality -->
            <input type="submit" name="update" value="Submit" class="btn btn-primary"> <!-- DIUBAH: Added Bootstrap class -->
            <input type="button" value="Close this window" onclick="f2();" class="btn btn-default"> <!-- DIUBAH: Added Bootstrap class -->
        </td>
    </tr>
</table>
</form>

<!-- Display history of remarks --> <!-- DITAMBAH: Added history table -->
<h3>Admin Remark History</h3>
<table class="remark-history" width="100%" border="1" cellspacing="0" cellpadding="5">
    <tr>
        <th>#</th>
        <th>Status</th>
        <th>Note Transport</th>
        <th>Checking</th>
        <th>Remark</th>
        <th>Date</th>
        <th>Action</th>
    </tr>
<?php
if ($complaintnumber) {
    $query = mysqli_query($con, "SELECT id, status, notetransport, checking, remark, remarkDate 
        FROM complaintremark 
        WHERE complaintNumber='$complaintnumber' 
        ORDER BY remarkDate DESC");
    $cnt = 1;
    while ($row = mysqli_fetch_assoc($query)) {
?>
    <tr>
        <td><?php echo $cnt; ?></td>
        <td><?php echo htmlentities($row['status']); ?></td>
        <td><?php echo htmlentities($row['notetransport']); ?></td>
        <td><?php echo htmlentities($row['checking']); ?></td>
        <td><?php echo htmlentities($row['remark']); ?></td>
        <td><?php echo htmlentities($row['remarkDate']); ?></td>
        <td>
            <a href="javascript:void(0);" onclick="editRemark('<?php echo $row['id']; ?>', '<?php echo addslashes(htmlentities($row['notetransport'])); ?>', '<?php echo addslashes(htmlentities($row['checking'])); ?>', '<?php echo addslashes(htmlentities($row['remark'])); ?>', '<?php echo htmlentities($row['status']); ?>')" class="btn btn-primary">Edit</a>
            <a href="?cid=<?php echo $complaintnumber; ?>&delete=1&remark_id=<?php echo $row['id']; ?>" onclick="return confirmDelete(<?php echo $row['id']; ?>);" class="btn btn-danger">Delete</a>
        </td>
    </tr>
<?php
        $cnt++;
    }
}
?>
</table>
</div>
</body>
</html>