
<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else{


if(isset($_POST['update']))
{
$sid=intval($_GET['sid']);
//Getting post values
$saname=$_POST['subadmin'];
$sadept=$_POST['subadmindept'];
$saemail=$_POST['emailid'];
$sacontactno=$_POST['contactno'];
$isactive=$_POST['contactno'];
$query=mysqli_query($con,"update  tblsubadmin set SubAdminName='$saname',Department='$sadept',EmailId='$saemail',ContactNumber='$sacontactno',IsActive='$isactive' where id='$sid'");
if($query){
echo "<script>alert('Sub admin details updated  successfully.');</script>";
echo "<script type='text/javascript'> document.location = 'manage-subadmins.php'; </script>";
} else {
echo "<script>alert('Something went wron. Please try again.');</script>";
}

}


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin| Update/Edit Subordinates/Sub-admin</title>
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
								<h3>Update/Edit Subordinates/Sub-admin</h3>
							</div>
							<div class="module-body">


<?php
$sid=intval($_GET['sid']);
$query=mysqli_query($con,"select * from tblsubadmin where id='$sid'");
while($row=mysqli_fetch_array($query))
{?>
<form class="form-horizontal row-fluid" name="su-admin" method="post" >


<div class="control-group">
<label class="control-label" for="basicinput">Reg. Date</label>
<div class="controls">
<input type="text" name="sadminusername" id="sadminusername" class="span8 tip"   value="<?php echo htmlentities($row['RegDate']);?>" readonly>
</div>
</div>	

<?php if($row['LastUpdationDate']!=''){?>
<div class="control-group">
<label class="control-label" for="basicinput">Last Updation Date</label>
<div class="controls">
<input type="text" name="sadminusername" id="sadminusername" class="span8 tip"   value="<?php echo htmlentities($row['LastUpdationDate']);?>" readonly>
</div>
</div>	
<?php } ?>
		
<div class="control-group">
<label class="control-label" for="basicinput">Username (used for login)</label>
<div class="controls">
<input type="text" placeholder="Enter Sub-admin Username"  name="sadminusername" id="sadminusername" class="span8 tip"   value="<?php echo htmlentities($row['UserName']);?>" readonly>

</div>
</div>

<div class="control-group">
<label class="control-label" for="basicinput">Sub-admin Name</label>
<div class="controls">
<input type="text" placeholder="Enter Sub-admin Name"  name="subadmin" class="span8 tip" value="<?php echo htmlentities($row['SubAdminName']);?>" required>
</div>
</div>

<div class="control-group">
<label class="control-label" for="basicinput">Sub-admin Department</label>
<div class="controls">
<input type="text" placeholder="Enter Sub-admin Department" value="<?php echo htmlentities($row['Department']);?>"  name="subadmindept" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<label class="control-label" for="basicinput">Email id</label>
<div class="controls">
<input type="email" placeholder="Enter Sub-admin Email id" value="<?php echo htmlentities($row['EmailId']);?>"  name="emailid" class="span8 tip" required>
</div>
</div>


<div class="control-group">
<label class="control-label" for="basicinput">Contact Number</label>
<div class="controls">
<input type="text" placeholder="Enter Sub-admin Contact No." pattern="[0-9]{10}" title="10 numeric characters only"  name="contactno" class="span8 tip" required value="<?php echo htmlentities($row['ContactNumber']);?>">
</div>
</div>

<div class="control-group">
<label class="control-label" for="basicinput">Account Status</label>
<div class="controls">
<?php if($row['IsActive']==1){?>
<input type="radio" name="accntstatus" value="1" required="true" checked="true">Active
<input type="radio" name="accntstatus" value="0" required="true">Blocked
<?php }  else {?>
	<input type="radio" name="accntstatus" value="0" required="true" checked="true">Blocked
	<input type="radio" name="accntstatus" value="1" required="true" >Active
<?php } ?>
</div>
</div>


<?php } ?>


	<div class="control-group">
											<div class="controls">
												<button type="submit" name="update"  id="update" class="btn btn-primary">Update</button>
											</div>
										</div>
									</form>
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