<?php
session_start();
error_reporting(0);
include('include/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else{
date_default_timezone_set('Asia/Kolkata');// change according timezone
$currentTime = date( 'd-m-Y h:i:s A', time () );


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin| Subadmin Report</title>
	<link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
	<link type="text/css" href="css/theme.css" rel="stylesheet">
	<link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
	<link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>

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
				<?php
$fdate=$_POST['fromdate'];
$tdate=$_POST['todate'];
$_SESSION['fdate']=$fdate;
$_SESSION['tdate']=$tdate;
		?>
							<div class="module-head">
		

	<h2 align="center" style="color:blue;">Subadmin  Report from <?php echo $new_Date = date("d-m-Y", strtotime($fdate));?> to <?php echo $new_Date = date("d-m-Y", strtotime($tdate));?></h2>
</div>
<div class="module-body table">
<table border="2" align="center" width="80%" style="">
	<tr>
		<th style="color:#000;text-align:center; font-size:16px;">Subadmin Name /Dept</th>
		<th style="color:#000;text-align:center; font-size:16px;">Total</th>
		<th style="color:red;text-align:center; font-size:16px;">Not Processed Yet</th>
		<th style="color:orange;text-align:center;font-size:16px">In Process</th>
		<th style="color:green;text-align:center;font-size:16px">Closed</th>
	</tr>

												<?php
//$subid=$_SESSION['suid'];
$sql = mysqli_query($con,"SELECT tblsubadmin.SubAdminName,tblsubadmin.Department,tblsubadmin.id as sid,COUNT(tblcomplaints.complaintNumber) as totalcount,COUNT(IF((status is null || status=''),0, NULL))  as notprocessedyet,
   COUNT(IF((status='in process'),0, NULL))  as inprocess,
   COUNT(IF((status='closed'),0, NULL))  as closed FROM tblcomplaints
join tblforwardhistory on tblforwardhistory.ComplaintNumber=tblcomplaints.complaintNumber
join tblsubadmin on tblsubadmin.id=tblforwardhistory.ForwardTo
where date(tblforwardhistory.ForwadDate) Between '$fdate' and '$tdate'
 GROUP by tblsubadmin.SubAdminName");
while($result=mysqli_fetch_array($sql))
{?>

	<tr>
		<th style="color:#000;text-align:center; font-size:14px;"><?php echo $result['SubAdminName']."-".$result['Department'];?></th>
		<th style="color:#000;text-align:center; font-size:14px;"><?php echo $total=$result['totalcount'];?></th>
		<th style="color:#000;text-align:center; font-size:14px;"><a href="subadmin-report-details.php?sid=<?php echo $result['sid'];?>&&type=npy" target="_blank"><?php echo $npy=$result['notprocessedyet'];;?></a></th>
		<th style="color:#000;text-align:center;font-size:14px"><a href="subadmin-report-details.php?sid=<?php echo $result['sid'];?>&&type=in" target="_blank"><?php echo $in=$result['inprocess'];?></a></th>
		<th style="color:#000;text-align:center;font-size:14px"><a href="subadmin-report-details.php?sid=<?php echo $result['sid'];?>&&type=clsd" target="_blank"><?php echo $tc=$result['closed'];?></a></th>
	</tr>
<?php 
$gtotal+=$total;
$gnpy+=$npy;
$gin+=$in;
$gtc+=$tc;
} ?>
<tr>
	<th style="color:blue;text-align:center; font-size:16px;">Grand Total</th>
		<th style="color:#000;text-align:center;font-size:14px"><?php echo $gtotal;?></th>
		<th style="color:#000;text-align:center;font-size:14px"><?php echo $gnpy;?></th>
		<th style="color:#000;text-align:center;font-size:14px"><?php echo $gin;?></th>
		<th style="color:#000;text-align:center;font-size:14px"><?php echo $gtc;?></th>
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

	<script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
	<script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
	<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="scripts/flot/jquery.flot.js" type="text/javascript"></script>
	<script src="scripts/datatables/jquery.dataTables.js"></script>
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