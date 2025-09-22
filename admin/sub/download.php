<?php
session_start();
include('../include/config.php');

// Ensure user is logged in as sub-admin
if (empty($_SESSION['subalogin'])) {
    header('location:../index.php');
    exit();
}

// Get Complaint ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Complaint ID.");
}
$id = intval($_GET['id']);

// Autoload TCPDF
require_once('../vendor/autoload.php'); 
require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');

// Custom PDF class
class MYPDF extends TCPDF {
    private $showHeader = true;
    private $isSignaturePage = false;

    public function Header() {
        // Header only on first page and signature page
        if ($this->showHeader && ($this->page == 1 || $this->isSignaturePage)) {
            $this->SetFont('helvetica', 'B', 14);
            $this->SetY(12);
            $this->Cell(0, 0, 'PTA SALES AND SERVICE - Page ' . $this->getPage(), 0, 1, 'C');
            $image_file = __DIR__ . '/../img/logo.jpg';
            if (file_exists($image_file)) {
                if ($this->page == 1) {
                    $this->Image($image_file, 15, 20, 25); // Logo top-left on Page 1
                } elseif ($this->isSignaturePage) {
                    $this->Ln(10);
                    $pageWidth = $this->getPageWidth();
                    $imageWidth = 25;
                    $x = ($pageWidth - $imageWidth) / 2;
                    $this->Image($image_file, $x, $this->GetY(), $imageWidth); // Centered logo on Page 5
                }
            }
        }
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages() . ' | Generated: ' . date('h:i A T, l, F d, Y'), 0, false, 'C');
    }

    public function setShowHeader($show) {
        $this->showHeader = $show;
    }

    public function setIsSignaturePage($isSignature) {
        $this->isSignaturePage = $isSignature;
    }
}

// Fetch complaint and user data using prepared statement
$stmt = $con->prepare("SELECT tblcomplaints.*, 
                              users.fullName AS fullname, 
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
                              category.categoryName AS category 
                       FROM tblcomplaints 
                       JOIN users ON users.id = tblcomplaints.userId 
                       JOIN category ON category.id = tblcomplaints.category 
                       WHERE tblcomplaints.complaintNumber = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("No complaint record found.");
}
$row = $result->fetch_array();
$stmt->close();

// Fetch forward history
$sql = "SELECT s.SubAdminName, s.Department, f.ForwadDate 
        FROM tblforwardhistory f 
        JOIN tblsubadmin s ON s.id = f.ForwardTo 
        WHERE f.ComplaintNumber = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$forward_result = $stmt->get_result();
$forwardHistory = $forward_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Initialize PDF
$pdf = new MYPDF();
$pdf->setAutoPageBreak(true, 20); // Enable auto page breaks with 20mm bottom margin
$pdf->setShowHeader(true);

// Customer Information (Page 1)
$pdf->AddPage();
$pdf->Ln(20);
$html_page1 = '<h3 style="text-align:center; margin-top:45px;">CUSTOMER INFORMATION</h3>
<div style="width:80%; margin:0 auto; font-family:Arial, sans-serif; font-size:14px; line-height:1.6;">
    <p><strong>Full Name:</strong> ' . htmlentities($row['fullname']) . '</p>
    <p><strong>IC Number:</strong> ' . htmlentities($row['icnumber']) . '</p>
    <p><strong>User Email:</strong> ' . htmlentities($row['userEmail']) . '</p>
    <p><strong>Primary Phone Number:</strong> ' . htmlentities($row['contactno']) . '</p>
    <p><strong>Secondary Phone Number:</strong> ' . (!empty($row['contactno2']) ? htmlentities($row['contactno2']) : 'NA') . '</p>
    <p><strong>Address:</strong> ' . htmlentities($row['address']) . '</p>
    <p><strong>State:</strong> ' . htmlentities($row['state_user']) . '</p>
    <p><strong>Country:</strong> ' . htmlentities($row['country']) . '</p>
    <p><strong>Postal Code:</strong> ' . htmlentities($row['pincode']) . '</p>
    <p><strong>Registration Date:</strong> ' . htmlentities($row['userRegDate']) . '</p>
    <p><strong>Last Update:</strong> ' . (!empty($row['userUpdationDate']) ? htmlentities($row['userUpdationDate']) : 'NA') . '</p>
    <p><strong>Status:</strong> ' . ($row['userStatus'] == 1 ? 'Active' : 'Blocked') . '</p>
</div>';
$pdf->writeHTML($html_page1, true, false, true, false, '');

// Repair Report (Page 2)
$pdf->AddPage();
$pdf->Ln(10);
$html_page2 = '<h4 style="text-align:center; margin-top:20px;">REPAIR REPORT</h4>
<table border="1" cellpadding="5" cellspacing="0" width="100%">
    <tr><td><b>Repair Number</b></td><td>' . htmlentities($row['complaintNumber']) . '</td><td><b>Report Number</b></td><td>' . (!empty($row['reportNumber']) ? htmlentities($row['reportNumber']) : 'NA') . '</td></tr>
    <tr><td><b>Report Date</b></td><td>' . htmlentities($row['regDate']) . '</td><td><b>Customer Name</b></td><td>' . htmlentities($row['fullname']) . '</td></tr>
    <tr><td><b>IC Number</b></td><td>' . htmlentities($row['icnumber']) . '</td><td><b>Category</b></td><td>' . htmlentities($row['category']) . '</td></tr>
    <tr><td><b>Subcategory</b></td><td>' . htmlentities($row['subcategory']) . '</td><td><b>Warranty Type</b></td><td>' . htmlentities($row['complaintType']) . '</td></tr>
    <tr><td><b>Purchase</b></td><td>' . htmlentities($row['state']) . '</td><td><b>Brand Name</b></td><td>' . htmlentities($row['brandname']) . '</td></tr>
    <tr><td><b>Model Number</b></td><td>' . htmlentities($row['modelNo']) . '</td><td><b>Damage</b></td><td>' . htmlentities($row['complaintDetails']) . '</td></tr>
    <tr><td><b>Warranty File</b></td><td colspan="3">' . ($row['warrantyFile'] ? htmlentities($row['warrantyFile']) : 'NA') . '</td></tr>
    <tr><td><b>Receipt File</b></td><td colspan="3">' . ($row['receiptFile'] ? htmlentities($row['receiptFile']) : 'NA') . '</td></tr>
    <tr><td><b>Final Status</b></td><td colspan="3">' . ($row['status'] ? htmlentities($row['status']) : 'Not Processed') . '</td></tr>
</table>';
$pdf->writeHTML($html_page2, true, false, true, false, '');

// Forward History
if (!empty($forwardHistory)) {
    $pdf->Ln(10);
    $html_forward = '<h4>Forward History</h4><table style="border: 1px solid black; border-collapse: collapse; width:100%;" cellpadding="5" cellspacing="0">
        <tr style="border: 1px solid black;">
            <th style="border: 1px solid black;">Forwarded To</th>
            <th style="border: 1px solid black;">Department</th>
            <th style="border: 1px solid black;">Date</th>
        </tr>';
    foreach ($forwardHistory as $fh) {
        $html_forward .= '<tr style="border: 1px solid black;">
            <td style="border: 1px solid black;">' . htmlentities($fh['SubAdminName']) . '</td>
            <td style="border: 1px solid black;">' . htmlentities($fh['Department']) . '</td>
            <td style="border: 1px solid black;">' . htmlentities($fh['ForwadDate']) . '</td>
        </tr>';
    }
    $html_forward .= '</table>';
    $pdf->writeHTML($html_forward, true, false, true, false, '');
}

// Admin Remarks (Page 3)
$pdf->setShowHeader(false);
$pdf->AddPage();
$pdf->Ln(0);
$html_admin = '<h3 style="text-align:center; margin-top:20px; font-family:Arial, sans-serif; font-size:14px;">ADMIN REMARKS</h3>
<div style="width:85%; margin:0 auto; font-family:Arial, sans-serif; font-size:11px; line-height:1.5; padding:10px;">
    <p><strong>Status:</strong> ' . htmlentities($row['status']) . '</p>';
$stmt = $con->prepare("SELECT remark, status AS sstatus, remarkDate, notetransport, checking 
                       FROM complaintremark 
                       WHERE complaintNumber = ? 
                       ORDER BY remarkDate DESC");
$stmt->bind_param("s", $id);
$stmt->execute();
$ret_result = $stmt->get_result();
if ($ret_result->num_rows > 0) {
    while ($rw = $ret_result->fetch_assoc()) {
        $html_admin .= '<p><strong>Status:</strong> ' . htmlentities($rw['sstatus']) . '</p>
                        <p><strong>Note Transport:</strong> ' . htmlentities($rw['notetransport']) . '</p>
                        <p><strong>Checking:</strong> ' . htmlentities($rw['checking']) . '</p>
                        <p><strong>Remark:</strong> ' . htmlentities($rw['remark']) . '</p>
                        <p><strong>Date:</strong> ' . htmlentities($rw['remarkDate']) . '</p>
                        <hr style="border:0; border-top:1px solid #ccc; margin:10px 0;">';
    }
} else {
    $html_admin .= '<p>No admin remarks found.</p>';
}
$html_admin .= '</div>';
$stmt->close();
$pdf->writeHTML($html_admin, true, false, true, false, '');

// Technician Remarks (Page 4)
$pdf->setShowHeader(false);
$pdf->AddPage();
$pdf->Ln(0);
$html_technician = '<h3 style="text-align:center; margin-top:20px; font-family:Arial, sans-serif; font-size:14px;">TECHNICIAN REMARKS</h3>
<div style="width:85%; margin:0 auto; font-family:Arial, sans-serif; font-size:11px; line-height:1.5; padding:10px;">
    <p><strong>Status:</strong> ' . htmlentities($row['status']) . '</p>';
$stmt = $con->prepare("SELECT r.ComplainRemark, r.ComplainStatus, r.PostingDate, r.notetransport, r.checking,
                              s.SubAdminName, s.Department 
                       FROM tblsubadminremark r 
                       JOIN tblsubadmin s ON s.id = r.RemarkBy 
                       WHERE r.ComplainNumber = ? 
                       ORDER BY r.PostingDate DESC");
$stmt->bind_param("s", $id);
$stmt->execute();
$ret1_result = $stmt->get_result();
if ($ret1_result->num_rows > 0) {
    while ($rww = $ret1_result->fetch_assoc()) {
        $html_technician .= '<p><strong>Status:</strong> ' . htmlentities($rww['ComplainStatus']) . '</p>
                             <p><strong>Note Transport:</strong> ' . htmlentities($rww['notetransport']) . '</p>
                             <p><strong>Checking:</strong> ' . htmlentities($rww['checking']) . '</p>
                             <p><strong>Remark:</strong> ' . htmlentities($rww['ComplainRemark']) . '</p>
                             <p><strong>Remark By:</strong> ' . htmlentities($rww['SubAdminName']) . ' (' . htmlentities($rww['Department']) . ')</p>
                             <p><strong>Date:</strong> ' . htmlentities($rww['PostingDate']) . '</p>
                             <hr style="border:0; border-top:1px solid #ccc; margin:10px 0;">';
    }
} else {
    $html_technician .= '<p>No technician remarks found.</p>';
}
$html_technician .= '</div>';
$stmt->close();
$pdf->writeHTML($html_technician, true, false, true, false, '');

// Signature (Page 5)
$pdf->setShowHeader(true);
$pdf->setIsSignaturePage(true);
$pdf->AddPage();
$pdf->Ln(9);
$html_signature = '<h3 style="text-align:center; margin-top:20px; font-family:Arial, sans-serif; font-size:14px;">Signature</h3>
<table style="width:85%; margin:0 auto; font-family:Arial, sans-serif; font-size:11px; text-align:center;">
    <tr>
        <td style="padding:20px;">
            <br><br><br><br><br><br><br><br><br><br><br><br><br><br>
            ___________________________<br>
            Customer Signature<br><b>Name:</b> ____________________<br><b>Phone:</b> ____________________
        </td>
        <td style="padding:20px;">
            <br><br><br><br><br><br><br><br><br><br><br><br><br><br>
            ___________________________<br>
            Technician Signature<br><b>Name:</b> ____________________<br><b>Phone:</b> ____________________
        </td>
    </tr>
</table>';
$pdf->writeHTML($html_signature, true, false, true, false, '');

// Output PDF
ob_end_clean(); // Clear any output buffer to prevent TCPDF error
$pdf->Output('Repair_Report_' . $id . '.pdf', 'I');

// Close database connection
$con->close();
?>