<?php
session_start();
error_reporting(0);
include("include/config.php");
if(isset($_POST['submit']))
{

	$utype=$_POST['usertype'];
	$username=$_POST['username'];
	$emialcnt=$_POST['emailcontact'];
	if($utype=='admin'){
$ret=mysqli_query($con,"SELECT * FROM admin WHERE username='$username' and (EmailId='$emialcnt' || ContactNumber='$emialcnt')");
$num=mysqli_fetch_array($ret);
if($num>0)
{
$_SESSION['aid']=$num['id'];
echo "<script>window.location.href ='reset-password-admin.php'</script>";
}  else {

    echo '<script>alert("Invalid details. Please try again")</script>';
echo "<script>window.location.href ='forgot-password.php'</script>";

} } else {

$ret=mysqli_query($con,"SELECT * FROM tblsubadmin WHERE UserName='$username' and (EmailId='$emialcnt' || ContactNumber='$emialcnt')");
$num=mysqli_fetch_array($ret);
if($num>0)
{
$_SESSION['said']=$num['id'];
echo "<script>window.location.href ='reset-password-subadmin.php'</script>";
}  else {

    echo '<script>alert("Invalid details. Please try again")</script>';
echo "<script>window.location.href ='forgot-password.php'</script>";

}

}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>CMS | Admin passwword recovery</title>
	<link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
	<link type="text/css" href="css/theme.css" rel="stylesheet">
	<link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
	<link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
</head>
<body>

	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".navbar-inverse-collapse">
					<i class="icon-reorder shaded"></i>
				</a>

			  	<a class="brand" href="../index.html">
			  		Complaint Mnagaement System | Admin
			  	</a>

				<div class="nav-collapse collapse navbar-inverse-collapse">
				
					<ul class="nav pull-right">

						<li><a href="../index.html">
						Back to Portal
						
						</a></li>

						

						
					</ul>
				</div><!-- /.nav-collapse -->
			</div>
		</div><!-- /navbar-inner -->
	</div><!-- /navbar -->



	<div class="wrapper">
		<div class="container">
			<div class="row">
				<div class="module module-login span4 offset4">
					<form class="form-vertical" method="post">
						<div class="module-head">
							<h3>Password Recovery</h3>
						</div>
						<span style="color:red;" ><?php echo htmlentities($_SESSION['errmsg']); ?><?php echo htmlentities($_SESSION['errmsg']="");?></span>
						<div class="module-body">

<div class="control-group">

								<div class="controls row-fluid">
									<select class="span12" name="usertype" required="true">
										<option value=""> Select user type</option>
										<option value="admin">Admin</option>
										<option value="subadmin">Sub-Admin</option>
									</select>
								</div>
							</div>

							<div class="control-group">
								<div class="controls row-fluid">
									<input class="span12" type="text" id="inputEmail" name="username" placeholder="Username" required="true">
								</div>
							</div>
							<div class="control-group">
								<div class="controls row-fluid">
						<input class="span12" type="text" id="inputPassword" name="emailcontact" placeholder="Registered Email or Contact Number" required="true">
								</div>
							</div>
						</div>
						<div class="module-foot">
							<div class="control-group">
								<div class="controls clearfix">
									<button type="submit" class="btn btn-primary pull-right" name="submit">Submit</button>
									
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div><!--/.wrapper-->

	<div class="footer">
		<div class="container">
			 

			<b class="copyright">&copy; 2020 CMS </b> All rights reserved.
		</div>
	</div>
	<script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
	<script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
	<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
</body>