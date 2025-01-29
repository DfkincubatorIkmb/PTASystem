<?php
session_start();
//error_reporting(0);
include('include/config.php');
if (strlen($_SESSION['said']==0)) {
  header('location:forgot-password.php');
  } else{
    if(isset($_POST['submit']))
  {
  	$adminid=$_SESSION['said'];
  	$newpass=md5($_POST['newpassword']);
  	 $query=mysqli_query($con,"update tblsubadmin set Password='$newpass' where id='$adminid'");
  	 unset($_SESSION['said']);
  	   echo '<script>alert("Password reset successfully.")</script>';
echo "<script>window.location.href ='index.php'</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>CMS | Admin passwword reset</title>
	<link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
	<link type="text/css" href="css/theme.css" rel="stylesheet">
	<link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
	<link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
		<script type="text/javascript">
function valid()
{

 if(document.chngpwd.newpassword.value=="")
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
		<form name="chngpwd" method="post" onSubmit="return valid();">
						<div class="module-head">
							<h3>Reset your Password</h3>
						</div>
						<div class="module-body">
							<div class="control-group">
								<div class="controls row-fluid">
								
	<input class="span12" type="password" id="newpassword" name="newpassword" placeholder="New Password" required="true">

								</div>
							</div>

							<div class="control-group">
								<div class="controls row-fluid">
	
					<input class="span12" type="password" id="confirmpassword" name="confirmpassword" placeholder="Confirm Password" required="true">
								</div>
							</div>
						</div>
						<div class="module-foot">
							<div class="control-group">
								<div class="controls clearfix">
						<button type="submit" name="submit" class="btn btn-primary">Change</button>
									
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
<?php }?>