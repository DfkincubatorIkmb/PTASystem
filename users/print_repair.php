<?php
require_once __DIR__ . '/vendor/autoload.php'; // pastikan mPDF sudah install via composer

include('includes/config.php');

if (!isset($_GET['cid'])) {
    die("Invalid Request");
}

$cid = intval($_GET['cid']);
$query = mysqli_query($con, "SELECT * FROM tblcomplaints WHERE complaintNumber='$cid'");
$row = mysqli_fetch_array($query);

if (!$row) {
    die("No record found");
}

// create PDF
$mpdf = new \Mpdf\Mpdf();

$html = '
<h2 style="text-align:center;">Repair Report</h2>
<table border="1" cellpadding="10" cellspacing="0" width="100%">
    <tr>
        <td><b>Repair Number</b></td>
        <td>' . $row['complaintNumber'] . '</td>
        <td><b>Customer Name</b></td>
        <td>' . htmlentities($row['fullname']) . '</td>
    </tr>
    <tr>
        <td><b>Category</b></td>
        <td>' . htmlentities($row['category']) . '</td>
        <td><b>SubCategory</b></td>
        <td>' . htmlentities($row['subcategory']) . '</td>
    </tr>
    <tr>
        <td><b>Damage</b></td>
        <td colspan="3">' . htmlentities($row['complaintDetails']) . '</td>
    </tr>
    <tr>
        <td><b>Status</b></td>
        <td colspan="3">' . htmlentities($row['status']) . '</td>
    </tr>
</table>

<br><br><br>
<table border="0" width="100%">
    <tr>
        <td style="text-align:center;">
            ___________________________<br>
            Customer Signature
        </td>
        <td style="text-align:center;">
            ___________________________<br>
            Technician Signature
        </td>
    </tr>
</table>
';

$mpdf->WriteHTML($html);
$mpdf->Output('repair_report_' . $row['complaintNumber'] . '.pdf', 'I');
?>
