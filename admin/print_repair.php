<?php
ob_start(); // Start output buffering
session_start();
include('include/config.php');

// Check if user is logged in
if (strlen($_SESSION['alogin']) == 0) {    
    header('location:index.php');
    exit();
}

// Check database connection
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

require(__DIR__.'/../vendor/tecnickcom/tcpdf/tcpdf.php');

$id = intval($_GET['id']); 

class MYPDF extends TCPDF {
    private $showHeader = true;
    private $isSignaturePage = false;

    public function Header() {
        // Header only on first page and signature page
        if ($this->showHeader && ($this->page == 1 || $this->isSignaturePage)) {
            $this->SetFont('helvetica', 'B', 14);
            $this->SetY(12);
            $this->Cell(0, 0, 'PTA SALES AND SERVICE - Page ' . $this->getPage(), 0, 1, 'C');
            $image_file = __DIR__.'/../img/logo.jpg';
            if (file_exists($image_file)) {
                if ($this->page == 1) {
                    $this->Image($image_file, 15, 20, 25); // Logo on first page
                } elseif ($this->isSignaturePage) {
                    $this->Ln(10); // Add spacing
                    $pageWidth = $this->getPageWidth();
                    $imageWidth = 25; // Width of the logo
                    $x = ($pageWidth - $imageWidth) / 2; // Center the logo
                    $this->Image($image_file, $x, $this->GetY(), $imageWidth); // Centered on signature page
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

// Initialize PDF
$pdf = new MYPDF();
$pdf->setAutoPageBreak(true, 20); // Enable auto page breaks with 20mm bottom margin
$pdf->SetFont('helvetica', '', 12);

// Fetch complaint and user data
$sql = "SELECT tblcomplaints.*, 
               users.fullName AS fullname,
               users.icnumber AS icnumber, 
               users.userEmail AS userEmail, 
               users.contactno AS contactno, 
               users.contactno2 AS contactno2, 
               users.address AS address, 
               users.State AS state_user, 
               users.country AS country, 
               users.pincode AS pincode, 
               users.regDate AS userRegDate, 
               users.updationDate AS userUpdationDate, 
               users.status AS userStatus, 
               category.categoryName AS category 
        FROM tblcomplaints 
        JOIN users ON users.id = tblcomplaints.userId 
        JOIN category ON category.id = tblcomplaints.category 
        WHERE tblcomplaints.complaintNumber = ?";
$stmt = $con->prepare($sql);
if (!$stmt) {
    die("Query preparation error: " . $con->error);
}
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_array();
$stmt->close();

if (!$row) {
    die("No complaint record found for complaint number: $id");
}

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

// Fetch admin remarks
$sql = "SELECT remark, status AS sstatus, remarkDate, notetransport, checking 
        FROM complaintremark 
        WHERE complaintNumber = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$admin_result = $stmt->get_result();
$adminRemarks = $admin_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch sub admin remarks
$sql = "SELECT r.ComplainRemark, r.ComplainStatus, r.PostingDate, r.notetransport, r.checking,
               s.SubAdminName, s.Department 
        FROM tblsubadminremark r 
        JOIN tblsubadmin s ON s.id = r.RemarkBy 
        WHERE r.ComplainNumber = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$subadmin_result = $stmt->get_result();
$subAdminRemarks = $subadmin_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Page 1: Customer Information
$pdf->AddPage();
$pdf->Ln(15);
$html_page1 = '<h3 style="text-align:center; margin-top:45px;">CUSTOMER INFORMATION</h3>
<div style="width:80%; margin:0 auto; font-family:Arial, sans-serif; font-size:14px; line-height:1.6;">
    <p><strong>Full Name:</strong> ' . htmlentities($row['fullname']) . '</p>
    <p><strong>IC Number:</strong> ' . htmlentities($row['icnumber']) . '</p>
    <p><strong>User Email:</strong> ' . htmlentities($row['userEmail']) . '</p>
    <p><strong>Primary Phone Number:</strong> ' . htmlentities($row['contactno']) . '</p>
    <p><strong>Secondary Phone Number:</strong> ' . (!empty($row['contactno2']) ? htmlentities($row['contactno2']) : 'NA') . '</p>
    <p><strong>Address:</strong> ' . htmlentities($row['address']) . '</p>
    <p><strong>Purchase:</strong> ' . htmlentities($row['state']) . '</p>
    <p><strong>Country:</strong> ' . htmlentities($row['country']) . '</p>
    <p><strong>Postal Code:</strong> ' . htmlentities($row['pincode']) . '</p>
    <p><strong>Registration Date:</strong> ' . htmlentities($row['userRegDate']) . '</p>
    <p><strong>Last Update:</strong> ' . (!empty($row['userUpdationDate']) ? htmlentities($row['userUpdationDate']) : 'NA') . '</p>
    <p><strong>Status:</strong> ' . ($row['userStatus'] == 1 ? 'Active' : 'Blocked') . '</p>
</div>';
$pdf->writeHTML($html_page1, true, false, true, false, '');

// Page 2: Repair Report (including Forward History)
$pdf->AddPage();
$pdf->Ln(10);
$html_page2 = '<h4 style="text-align:center; margin-top:20px;">REPAIR REPORT</h4>
<table border="1" cellpadding="5" cellspacing="0" width="100%">
    <tr><td><b>Report Number</b></td><td>' . htmlentities($row['reportNumber']) . '</td><td><b>Report Date</b></td><td>' . htmlentities($row['regDate']) . '</td></tr>
    <tr><td><b>Repair Number</b></td><td>' . htmlentities($row['complaintNumber']) . '</td><td><b>Customer Name</b></td><td>' . htmlentities($row['fullname']) . '</td></tr>
    <tr><td><b>IC Number</b></td><td>' . htmlentities($row['icnumber']) . '</td><td><b>Category</b></td><td>' . htmlentities($row['category']) . '</td></tr>
    <tr><td><b>Subcategory</b></td><td>' . htmlentities($row['subcategory']) . '</td><td><b>Warranty Type</b></td><td>' . htmlentities($row['complaintType']) . '</td></tr>
    <tr><td><b>Purchase</b></td><td>' . htmlentities($row['state']) . '</td><td><b>Brand Name</b></td><td>' . htmlentities($row['brandname']) . '</td></tr>
    <tr><td><b>Model Number</b></td><td>' . htmlentities($row['modelNo']) . '</td><td><b>Damage</b></td><td>' . htmlentities($row['complaintDetails']) . '</td></tr>
    <tr><td><b>Warranty File</b></td><td colspan="3">' . ($row['warrantyFile'] ? htmlentities($row['warrantyFile']) : 'NA') . '</td></tr>
    <tr><td><b>Receipt File</b></td><td colspan="3">' . ($row['receiptFile'] ? htmlentities($row['receiptFile']) : 'NA') . '</td></tr>
    <tr><td><b>Final Status</b></td><td colspan="3">' . ($row['status'] ? htmlentities($row['status']) : 'Not Processed') . '</td></tr>
</table>';
$pdf->writeHTML($html_page2, true, false, true, false, '');

// Forward History (part of Repair Report)
if (!empty($forwardHistory)) {
    $pdf->Ln(10);
    $html_forward = '<h4>Forward History</h4>
    <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <tr><th>Forward To</th><th>Department</th><th>Date</th></tr>';
    foreach ($forwardHistory as $res) {
        $html_forward .= '<tr>
            <td>' . htmlentities($res['SubAdminName']) . '</td>
            <td>' . htmlentities($res['Department']) . '</td>
            <td>' . htmlentities($res['ForwadDate']) . '</td>
        </tr>';
    }
    $html_forward .= '</table><br>';
    $pdf->writeHTML($html_forward, true, false, true, false, '');
}

// Page 3: Admin Remarks
if (!empty($adminRemarks)) {
    $pdf->setShowHeader(false); // Hide header for Admin remarks
    $pdf->AddPage();
    $pdf->Ln(10);
    $html_admin = '<h4 style="text-align:center;">ADMIN REMARKS</h4>';
    foreach ($adminRemarks as $index => $remark) {
        $html_admin .= '
        <p><b>Transport Note:</b> ' . htmlentities($remark['notetransport']) . '</p>
        <p><b>Checking:</b> ' . htmlentities($remark['checking']) . '</p>
        <p><b>Remark:</b> ' . htmlentities($remark['remark']) . '</p>
        <p><b>Status:</b> ' . htmlentities($remark['sstatus']) . ' | <b>Date:</b> ' . htmlentities($remark['remarkDate']) . '</p><hr>';
    }
    $pdf->writeHTML($html_admin, true, false, true, false, '');
    $pdf->setShowHeader(true); // Restore header for subsequent sections
}

// Page 4: Technician Remarks
if (!empty($subAdminRemarks)) {
    $pdf->setShowHeader(false); // Hide header for Technician remarks
    $pdf->AddPage();
    $pdf->Ln(10);
    $html_subadmin = '<h4 style="text-align:center;">TECHNICIAN REMARKS</h4>';
    foreach ($subAdminRemarks as $index => $remark) {
        $html_subadmin .= '
        <p><b>Transport Note:</b> ' . htmlentities($remark['notetransport']) . '</p>
        <p><b>Checking:</b> ' . htmlentities($remark['checking']) . '</p>
        <p><b>Remark:</b> ' . htmlentities($remark['ComplainRemark']) . '</p>
        <p><b>Status:</b> ' . htmlentities($remark['ComplainStatus']) . ' | <b>Date:</b> ' . htmlentities($remark['PostingDate']) . '</p>
        <p><i>By: ' . htmlentities($remark['SubAdminName']) . ' (' . htmlentities($remark['Department']) . ')</i></p><hr>';
    }
    $pdf->writeHTML($html_subadmin, true, false, true, false, '');
    $pdf->setShowHeader(true); // Restore header for subsequent sections
}

// Page 5: Signature Section (keep same design)
$pdf->setIsSignaturePage(true); // Set before adding page to ensure header renders
$pdf->AddPage();
$pdf->Ln(10);
$html_signature = '<h4 style="text-align:center; margin-top:20px;">Signatures</h4>
<table border="0" width="100%">
    <tr>
        <td style="text-align:center;"><br><br><br><br><br><br><br><br><br><br><br><br>
            ___________________________<br>
            Customer Signature<br><b>Name :</b> ____________________<br><b>Phone :</b> ____________________<br><b>Date :</b> ____________________
        </td>
        <td style="text-align:center;"><br><br><br><br><br><br><br><br><br><br><br><br>
            ___________________________<br>
            Technician Signature<br><b>Name :</b> ____________________<br><b>Phone :</b> ____________________<br><b>Date :</b> ____________________
        </td>
    </tr>
</table>';
$pdf->writeHTML($html_signature, true, false, true, false, '');

// Output PDF
ob_end_clean(); // Clean output buffer
$pdf->Output('Repair_Report_' . $id . '.pdf', 'I');

// Close database connection
$con->close();
?>