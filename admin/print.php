<?php
session_start();
include('include/config.php'); // adjust ikut path awak
require('fpdf/fpdf.php');     // pastikan folder fpdf ada

if(!isset($_GET['cid'])){
    die("Complaint ID not provided.");
}
$cid = intval($_GET['cid']);

// Query complaint detail
$sql = mysqli_query($con,"SELECT c.complaintNumber, c.status, c.complaintDetails, c.regDate, 
                                 u.fullName, u.contactNo, u.email, u.address
                          FROM tblcomplaints c
                          JOIN users u ON u.id=c.userId
                          WHERE c.complaintNumber='$cid'");
$row = mysqli_fetch_assoc($sql);

if(!$row){
    die("Complaint not found.");
}

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);

// Title
$pdf->Cell(0,10,'Repair Report',0,1,'C');
$pdf->Ln(5);

// Complaint Info
$pdf->SetFont('Arial','',12);
$pdf->Cell(50,10,'Complaint No:',0,0);
$pdf->Cell(0,10,$row['complaintNumber'],0,1);

$pdf->Cell(50,10,'Customer Name:',0,0);
$pdf->Cell(0,10,$row['fullName'],0,1);

$pdf->Cell(50,10,'Contact No:',0,0);
$pdf->Cell(0,10,$row['contactNo'],0,1);

$pdf->Cell(50,10,'Email:',0,0);
$pdf->Cell(0,10,$row['email'],0,1);

$pdf->Cell(50,10,'Address:',0,0);
$pdf->MultiCell(0,10,$row['address'],0,1);

$pdf->Cell(50,10,'Status:',0,0);
$pdf->Cell(0,10,$row['status'],0,1);

$pdf->Cell(50,10,'Registered On:',0,0);
$pdf->Cell(0,10,$row['regDate'],0,1);

$pdf->Ln(10);

// Complaint Details
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'Complaint Details:',0,1);
$pdf->SetFont('Arial','',12);
$pdf->MultiCell(0,10,$row['complaintDetails']);
$pdf->Ln(20);

// Signatures
$pdf->SetFont('Arial','B',12);
$pdf->Cell(90,10,'Customer Signature:',0,0,'C');
$pdf->Cell(90,10,'Technician Signature:',0,1,'C');

$pdf->Ln(20);

// Signature Lines
$pdf->SetFont('Arial','',12);
$pdf->Cell(90,10,'_______________________',0,0,'C');
$pdf->Cell(90,10,'_______________________',0,1,'C');

$pdf->Ln(10);

// Footer
$pdf->SetFont('Arial','I',8);
$pdf->Cell(0,10,'Generated on '.date("d-m-Y H:i:s"),0,0,'C');

$pdf->Output('I','repair_report_'.$cid.'.pdf');
?>
