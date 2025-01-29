
<?php
session_start();
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
	<title>Admin| In process Complaints</title>
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
							<div class="module-head">
								<h3>Sub-admin Complaints</h3>
							</div>
							<div class="module-body table">
<?php
$subid=intval($_GET['sid']);
$type=$_GET['type'];
$sql=mysqli_query($con,"select SubAdminName,Department from tblsubadmin where id='$subid'");
$result=mysqli_fetch_array($sql);
?>

<?php if($type=='npy'){?>
<h4 align="center" style="color:blue">Not processed Yet Complaints of <?php echo $result['SubAdminName']."-".$result['Department'];?></h4>
<?php } elseif($type=='in'){?>
<h4 align="center" style="color:blue">In Process Complaints of <?php echo $result['SubAdminName']."-".$result['Department'];?></h4>	
	<?php } else {?>
		<h4 align="center" style="color:blue">Closed Complaints of <?php echo $result['SubAdminName']."-".$result['Department'];?></h4>	
	<?php } ?>
<hr />
							
								<table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped	 display" >
									<thead>
										<tr>
											<th>Complaint No</th>
											<th> complainant Name</th>
											<th>Reg Date</th>
											<th>Status</th>
											
											<th>Action</th>
											
										
										</tr>
									</thead>
								
<tbody>
<?php 
$st='in process';
$st1='closed';
switch ($type) {
	case 'npy':
		$query=mysqli_query($con,"select tblcomplaints.*,
	users.fullName as name from tblcomplaints 
	join users on users.id=tblcomplaints.userId 
	join tblforwardhistory on tblforwardhistory.ComplaintNumber=tblcomplaints.complaintNumber	
	where tblforwardhistory.ForwardTo='$subid' and (tblcomplaints.status is null || tblcomplaints.status='')");
		break;
	
	case 'in':
			$query=mysqli_query($con,"select tblcomplaints.*,
	users.fullName as name from tblcomplaints 
	join users on users.id=tblcomplaints.userId 
	join tblforwardhistory on tblforwardhistory.ComplaintNumber=tblcomplaints.complaintNumber	
	where  tblforwardhistory.ForwardTo='$subid' and (tblcomplaints.status='$st')");
		break;
	case 'clsd':
			$query=mysqli_query($con,"select tblcomplaints.*,
	users.fullName as name from tblcomplaints 
	join users on users.id=tblcomplaints.userId 
	join tblforwardhistory on tblforwardhistory.ComplaintNumber=tblcomplaints.complaintNumber	
	where  tblforwardhistory.ForwardTo='$subid' and (tblcomplaints.status='$st1')");
		break;

}

while($row=mysqli_fetch_array($query))
{
?>										
										<tr>
											<td><?php echo htmlentities($row['complaintNumber']);?></td>
											<td><?php echo htmlentities($row['name']);?></td>
											<td><?php echo htmlentities($row['regDate']);?></td>
									<?php if($type=='npy'){?>
												<td><button type="button" class="btn btn-danger">Not process yet</button></td>
											<?php } elseif($type=='in') { ?>
									<td><button type="button" class="btn btn-warning">In Process</button></td>
											<?php } else {?>
								<td><button type="button" class="btn btn-success">Closed</button></td>
								<?php } ?>				
											
											<td>   <a href="complaint-details.php?cid=<?php echo htmlentities($row['complaintNumber']);?>"> View Details</a> 
											</td>
											</tr>

										<?php  } ?>
										</tbody>
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