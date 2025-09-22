<?php
session_start();
include('include/config.php');

if(strlen($_SESSION['alogin'])==0){	
    header('location:index.php');
} else {
   require __DIR__ . '/../vendor/autoload.php';

    $id = intval($_GET['id']); 

    class MYPDF extends TCPDF {
        public function Header() {
            $image_file = __DIR__.'/../img/logo.png';
            if(file_exists($image_file)){
                $this->Image($image_file, 15, 10, 25);
            }
            $this->SetFont('helvetica', 'B', 14);
            $this->SetY(12);
            $this->Cell(0, 0, 'PTA SALES AND SERVICE', 0, 1, 'C');
        }
        public function Footer() {
            $this->SetY(-15);
            $this->SetFont('helvetica', 'I', 8);
            $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C');
        }
    }

    // Fetch complaint details
    $query = mysqli_query($con, "SELECT tblcomplaints.*, 
                                       users.fullName as fullname, 
                                       category.categoryName as category 
                                FROM tblcomplaints 
                                JOIN users ON users.id=tblcomplaints.userId 
                                JOIN category ON category.id=tblcomplaints.category 
                                WHERE tblcomplaints.complaintNumber='$id'");
    $row = mysqli_fetch_array($query);

    $html = '<h3 style="text-align:center; margin-top:45px;">Repair Report</h3>
    <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <tr>
            <td><b>Repair Number</b></td>
            <td>'.htmlentities($row['complaintNumber']).'</td>
            <td><b>Customer Name</b></td>
            <td>'.htmlentities($row['fullname']).'</td>
        </tr>
        <tr>
            <td><b>Category</b></td>
            <td>'.htmlentities($row['category']).'</td>
            <td><b>SubCategory</b></td>
            <td>'.htmlentities($row['subcategory']).'</td>
        </tr>
        <tr>
            <td><b>Warranty Type</b></td>
            <td>'.htmlentities($row['complaintType']).'</td>
            <td><b>Purchase</b></td>
            <td>'.htmlentities($row['state']).'</td>
        </tr>
        <tr>
            <td><b>Brand</b></td>
            <td>'.htmlentities($row['noc']).'</td>
            <td><b>Damage</b></td>
            <td>'.htmlentities($row['complaintDetails']).'</td>
        </tr>
        <tr>
            <td><b>Warranty File</b></td>
            <td colspan="3">'.($row['warrantyFile'] ? htmlentities($row['warrantyFile']) : 'NA').'</td>
        </tr>
        <tr>
            <td><b>Receipt File</b></td>
            <td colspan="3">'.($row['receiptFile'] ? htmlentities($row['receiptFile']) : 'NA').'</td>
        </tr>
        <tr>
            <td><b>Final Status</b></td>
            <td colspan="3">'.($row['status'] ? htmlentities($row['status']) : 'Not Processed').'</td>
        </tr>
    </table><br>';

    // Fetch forward history
    $qry = mysqli_query($con, "SELECT s.SubAdminName, s.Department, f.ForwadDate 
                             FROM tblforwardhistory f 
                             JOIN tblsubadmin s ON s.id=f.ForwardTo 
                             WHERE f.ComplaintNumber='$id'");
    if(mysqli_num_rows($qry) > 0){
        $html .= '<h4>Forward History</h4>
        <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <tr><th>Forward To</th><th>Department</th><th>Date</th></tr>';
        while($res = mysqli_fetch_array($qry)){
            $html .= '<tr>
                <td>'.htmlentities($res['SubAdminName']).'</td>
                <td>'.htmlentities($res['Department']).'</td>
                <td>'.htmlentities($res['ForwadDate']).'</td>
            </tr>';
        }
        $html .= '</table><br>';
    }

    // Admin remarks
    $ret = mysqli_query($con, "SELECT remark, status as sstatus, remarkDate, notetransport, checking 
                              FROM complaintremark 
                              WHERE complaintNumber='$id'");
    if(mysqli_num_rows($ret) > 0){
        $html .= '<h4>Admin Remarks</h4>';
        while($rw = mysqli_fetch_array($ret)){
            $html .= '<p><b>Note Transport:</b> '.htmlentities($rw['notetransport']).'</p>
                      <p><b>Checking:</b> '.htmlentities($rw['checking']).'</p>
                      <p><b>Remark:</b> '.htmlentities($rw['remark']).'</p>
                      <p><b>Status:</b> '.htmlentities($rw['sstatus']).' | <b>Date:</b> '.htmlentities($rw['remarkDate']).'</p><hr>';
        }
    }

    // Sub admin remarks
    $ret1 = mysqli_query($con, "SELECT r.ComplainRemark, r.ComplainStatus, r.PostingDate, r.notetransport, r.checking,
                                     s.SubAdminName, s.Department 
                              FROM tblsubadminremark r 
                              JOIN tblsubadmin s ON s.id=r.RemarkBy 
                              WHERE r.ComplainNumber='$id'");
    if(mysqli_num_rows($ret1) > 0){
        $html .= '<h4>Sub Admin Remarks</h4>';
        while($rww = mysqli_fetch_array($ret1)){
            $html .= '<p><b>Note Transport:</b> '.htmlentities($rww['notetransport']).'</p>
                      <p><b>Checking:</b> '.htmlentities($rww['checking']).'</p>
                      <p><b>Remark:</b> '.htmlentities($rww['ComplainRemark']).'</p>
                      <p><b>Status:</b> '.htmlentities($rww['ComplainStatus']).' | <b>Date:</b> '.htmlentities($rww['PostingDate']).'</p>
                      <p><i>By: '.htmlentities($rww['SubAdminName']).' ('.htmlentities($rww['Department']).')</i></p><hr>';
        }
    }

    // Signature section
    $html .= '<br><br><br>
    <table border="0" width="100%">
        <tr>
            <td style="text-align:center;"><br><br><br><br><br><br><br><br>
                ___________________________<br>
                Customer Signature<br><b>Name:</b> _______<br><b>Phone:</b> _______
            </td>
            <td style="text-align:center;"><br><br><br><br><br><br><br><br>
                ___________________________<br>
                Technician Signature<br><b>Name:</b> _______<br><b>Phone:</b> _______
            </td>
        </tr>
    </table>';

    // Generate PDF output
    $pdf = new MYPDF();
    $pdf->AddPage();
    $pdf->Ln(15);
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('Repair_Report_'.$id.'.pdf', 'I');
}
?>