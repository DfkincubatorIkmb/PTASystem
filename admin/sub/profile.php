
<?php
session_start();
include('../include/config.php');
if(strlen($_SESSION['subalogin'])==0)
	{	
header('location:../index.php');
}
else{

if(isset($_POST['update']))
{
$fname=$_POST['fname'];
$emailid=$_POST['emailid'];
$contactno=$_POST['contactno'];
$subid=$_SESSION['suid'];
$query=mysqli_query($con,"update tblsubadmin set SubAdminName='$fname',EmailId='$emailid',ContactNumber='$contactno' where id='$subid'");
$_SESSION['msg']="Profile details Successfully !!";

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>PTA Technician | Change Password</title>
	<link rel="icon" href="logopta.png" type="image/png">
	<link type="text/css" href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link type="text/css" href="../bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
	<link type="text/css" href="../css/theme.css" rel="stylesheet">
	<link type="text/css" href="../images/icons/css/font-awesome.css" rel="stylesheet">
	<link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
	<script type="text/javascript">
function valid()
{
if(document.chngpwd.password.value=="")
{
alert("Current Password Filed is Empty !!");
document.chngpwd.password.focus();
return false;
}
else if(document.chngpwd.newpassword.value=="")
{
alert("New Password Filed is Empty !!");
document.chngpwd.newpassword.focus();
return false;
}
else if(document.chngpwd.confirmpassword.value=="")
{
alert("Confirm Password Filed is Empty !!");
document.chngpwd.confirmpassword.focus();
return false;
}
else if(document.chngpwd.newpassword.value!= document.chngpwd.confirmpassword.value)
{
alert("Password and Confirm Password Field do not match  !!");
document.chngpwd.confirmpassword.focus();
return false;
}
return true;
}
</script>
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
								<h3><?php echo $_SESSION['subalogin']?>'s Profile</h3>
							</div>
							<div class="module-body">

									<?php if(isset($_POST['update']))
{?>
									<div class="alert alert-success">
										<button type="button" class="close" data-dismiss="alert">×</button>
										<?php echo htmlentities($_SESSION['msg']);?><?php echo htmlentities($_SESSION['msg']="");?>
									</div>
<?php } ?>
									<br />

<form class="form-horizontal row-fluid" name="subadminprofile" method="post">

<?php
$subid=$_SESSION['suid'];
$query=mysqli_query($con,"select * from tblsubadmin where id='$subid'");
while($row=mysqli_fetch_array($query))
{
?>	

<div class="control-group">
<label class="control-label" for="basicinput"><strong>Regd. Date:</strong></label>
<div class="controls" style="padding-top:0.6%">
<?php echo $row['RegDate'];?>
</div>
</div>

<?php if($row['LastUpdationDate']){?>
<div class="control-group">
<label class="control-label" for="basicinput"><strong>Last Updation Date:</strong></label>
<div class="controls" style="padding-top:0.6%">
<?php echo $row['LastUpdationDate'];?>
</div>
</div>
<?php } ?>

<div class="control-group">
<label class="control-label" for="basicinput"><strong>Sub-Admin Name</strong></label>
<div class="controls">
<input type="text"   name="fname" value="<?php echo $row['SubAdminName'];?>" class="span8 tip" required>
</div>
</div>


<div class="control-group">
<label class="control-label" for="basicinput"><strong>Department</strong></label>
<div class="controls">
<input type="text"   name="dept" value="<?php echo $row['Department'];?>" class="span8 tip" readonly title="Deparment can be change by admin only">
</div>
</div>

<div class="control-group">
<label class="control-label" for="basicinput"><strong>Email-Id</strong></label>
<div class="controls">
<input type="text"   name="emailid" value="<?php echo $row['EmailId'];?>" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<label class="control-label" for="basicinput"><strong>Contact Number</strong></label>
<div class="controls">
<input type="text"   name="contactno" value="<?php echo $row['ContactNumber'];?>" class="span8 tip" required>
</div>
</div>


<div class="control-group">
<label class="control-label" for="basicinput"><strong>UserName</strong></label>
<div class="controls">
<input type="text"   name="username" value="<?php echo $row['UserName'];?>" class="span8 tip" title="Username can't be change" readonly>
</div>
</div>





<?php } ?>



										

										<div class="control-group">
											<div class="controls">
												<button type="submit" name="update" class="btn btn-primary">Update</button>
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

	<script src="../scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
	<script src="../scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
	<script src="../bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="../scripts/flot/jquery.flot.js" type="text/javascript"></script>
</body>
<?php } ?>