
<?php
session_start();
include('../include/config.php');
if(strlen($_SESSION['subalogin'])==0)
	{	
header('location:../index.php');
}
else{
date_default_timezone_set('Asia/Kuala_Lumpur');// change according timezone
$currentTime = date( 'd-m-Y h:i:s A', time () );


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>PTA Technician | Dashboard </title>
	<link rel="icon" href="logopta.png" type="image/png">
	<link type="text/css" href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link type="text/css" href="../bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
	<link type="text/css" href="../css/theme.css" rel="stylesheet">
	<link type="text/css" href="../images/icons/css/font-awesome.css" rel="stylesheet">
	<link type="text/css" href='../http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>

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
								<h3>Dashboard</h3>
							</div>
							<div class="module-body table">
<table border="2" align="center" width="80%" style="">
	<tr>
		<th style="color:#000;text-align:center; font-size:22px;">Total</th>
		<th style="color:red;text-align:center; font-size:22px;">Not Processed Yet</th>
		<th style="color:orange;text-align:center;font-size:22px">In Progress</th>
		<th style="color:green;text-align:center;font-size:22px">Closed</th>
	</tr>

												<?php
$subid=$_SESSION['suid'];
$rt = mysqli_query($con,"SELECT * FROM tblcomplaints 
join tblforwardhistory on tblforwardhistory.ComplaintNumber=tblcomplaints.complaintNumber	
	where tblcomplaints.status is null and tblforwardhistory.ForwardTo='$subid'");
$notprocessedyet = mysqli_num_rows($rt); 
//inprocess
  $status="in Process";                   
$rt1 = mysqli_query($con,"SELECT * FROM tblcomplaints
join tblforwardhistory on tblforwardhistory.ComplaintNumber=tblcomplaints.complaintNumber	
 where status='$status' and tblforwardhistory.ForwardTo='$subid'");
$inprocess = mysqli_num_rows($rt1);
//closed
  $status="closed";                   
$rt2 = mysqli_query($con,"SELECT * FROM tblcomplaints
join tblforwardhistory on tblforwardhistory.ComplaintNumber=tblcomplaints.complaintNumber	
 where status='$status' and tblforwardhistory.ForwardTo='$subid' ");
$closed = mysqli_num_rows($rt2);

?>

	<tr>
		<th style="color:#000;text-align:center; font-size:18px;"><?php echo $notprocessedyet+$inprocess+$closed;?></th>
		<th style="color:#000;text-align:center; font-size:18px;"><a href="notprocess-complaint.php"><?php echo $notprocessedyet;?></a></th>
		<th style="color:#000;text-align:center;font-size:18px"><a href="inprocess-complaint.php"><?php echo $inprocess;?></a></th>
		<th style="color:#000;text-align:center;font-size:18px"><a href="closed-complaint.php"><?php echo $closed;?></a></th>
	</tr>

</table>
                    </div>
							
							
							</div>
									

						
						
					</div><!--/.content-->
				</div><!--/.span9-->
			</div>
		</div><!--/.container-->
	</div><!--/.wrapper-->

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
		} );
	</script>
</body>
<?php } ?>