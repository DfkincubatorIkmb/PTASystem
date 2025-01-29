
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
	$adminid=$_SESSION['alogin'];
$aname=$_POST['aname'];
$emailid=$_POST['emailid'];
$contactno=$_POST['contactno'];

$query=mysqli_query($con, "update admin set AdminName ='$aname', ContactNumber='$contactno',EmailId='$emailid' where username='$adminid'");
if ($query) {
    
    echo '<script>alert("Admin profile has been updated.")</script>';
  }
  else
    {
      echo '<script>alert("Something Went Wrong. Please try again")</script>';
    }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title> admin| profile</title>
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
								<h3><?php echo $_SESSION['alogin']?>'s Profile</h3>
							</div>
							<div class="module-body">

									
								

<form class="form-horizontal row-fluid" method="post">

<?php
$adminid=$_SESSION['alogin'];
$query=mysqli_query($con,"select * from admin where username='$adminid'");
while($row=mysqli_fetch_array($query))
{
?>	


<div class="control-group">
<label class="control-label" for="basicinput"><strong>Admin Name</strong></label>
<div class="controls">
<input type="text"   name="aname" value="<?php echo $row['AdminName'];?>" class="span8 tip" required>
</div>
</div>


<div class="control-group">
<label class="control-label" for="basicinput"><strong>User Name</strong></label>
<div class="controls">
<input type="text"   name="" value="<?php echo $row['username'];?>" class="span8 tip" readonly title="Username can't be change">
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
<input type="text"   name="contactno" value="<?php echo $row['ContactNumber'];?>" class="span8 tip" required maxlength="10">
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

	<script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
	<script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
	<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="scripts/flot/jquery.flot.js" type="text/javascript"></script>
</body>
<?php } ?>