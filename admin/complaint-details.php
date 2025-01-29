
<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else{
//Code for forward complaint
if(isset($_POST['fwdsubmit']))
{
$cno=intval($_GET['cid']);	
$fwdto=$_POST['forwardto'];	
$fwdfrom=$_SESSION['id'];
$sql=mysqli_query($con,"insert into tblforwardhistory(ComplaintNumber,ForwardFrom,ForwardTo) values('$cno','$fwdfrom','$fwdto')");
if($sql)
{
echo "<script>alert('Complaint forwaded successfully.');</script>";
echo "<script type='text/javascript'> document.location = 'notprocess-complaint.php'; </script>";
} else 
{
echo "<script>alert('Complaint forwaded successfully.');</script>";
}
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>PTA Admin | Repair Details</title>
	<link rel="icon" href="logopta.png" type="image/png">
	<link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
	<link type="text/css" href="css/theme.css" rel="stylesheet">
	<link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
	<link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
	<script language="javascript" type="text/javascript">
var popUpWin=0;
function popUpWindow(URLStr, left, top, width, height)
{
 if(popUpWin)
{
if(!popUpWin.closed) popUpWin.close();
}
popUpWin = open(URLStr,'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,copyhistory=yes,width='+600+',height='+600+',left='+left+', top='+top+',screenX='+left+',screenY='+top+'');
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
								<h3>Repair Details</h3>
							</div>
							<div class="module-body table">
								<table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped	 display" width="100%">
									
									<tbody>

<?php $st='closed';
$query=mysqli_query($con,"select tblcomplaints.*,users.fullName as name,category.categoryName as catname from tblcomplaints join users on users.id=tblcomplaints.userId join category on category.id=tblcomplaints.category where tblcomplaints.complaintNumber='".$_GET['cid']."'");
while($row=mysqli_fetch_array($query))
{

?>									
										<tr>
											<td><b>Repair Number</b></td>
											<td><?php echo htmlentities($row['complaintNumber']);?></td>
											<td><b>Customer Name</b></td>
											<td> <?php echo htmlentities($row['name']);?></td>
											<td><b>Reg Date</b></td>
											<td><?php echo htmlentities($row['regDate']);?>
											</td>
										</tr>

<tr>
											<td><b>Category </b></td>
											<td><?php echo htmlentities($row['catname']);?></td>
											<td><b>SubCategory</b></td>
											<td> <?php echo htmlentities($row['subcategory']);?></td>
											<td><b>Report Type</b></td>
											<td><?php echo htmlentities($row['complaintType']);?>
											</td>
										</tr>
<tr>
											<td><b>State </b></td>
											<td><?php echo htmlentities($row['state']);?></td>
											<td ><b>Problem</b></td>
											<td colspan="3"> <?php echo htmlentities($row['noc']);?></td>
											
										</tr>
<tr>
											<td><b>Description </b></td>
											
											<td colspan="5"> <?php echo htmlentities($row['complaintDetails']);?></td>
											
										</tr>

											</tr>
<tr>
											<td><b>File(if any) </b></td>
											
											<td colspan="5"> <?php $cfile=$row['complaintFile'];
if($cfile=="" || $cfile=="NULL")
{
  echo "File NA";
}
else{?>
<a href="../users/complaintdocs/<?php echo htmlentities($row['complaintFile']);?>" target="_blank"/> View File</a>
<?php } ?></td>
</tr>

<tr>
<td><b>Final Status</b></td>											
<td colspan="5" style="color:red"><?php if($row['status']==""){ echo "Not Process Yet";
} else {
echo htmlentities($row['status']);
}?></td>
</tr>

<!-- Complaint forward history -->
<?php
$cmpno=intval($_GET['cid']);
$qry=mysqli_query($con,"select  tblsubadmin.SubAdminName,tblsubadmin.Department,tblforwardhistory.ForwadDate from tblforwardhistory join tblsubadmin on tblsubadmin.id=tblforwardhistory.ForwardTo where tblforwardhistory.ComplaintNumber='$cmpno'");
while($result=mysqli_fetch_array($qry))
{
?>
<tr>
<td><b>Forward to </b></td>
<td colspan="3"> <?php echo htmlentities($result['SubAdminName']);?>- (<?php echo htmlentities($result['Department']);?>)</td>
<td><b>Forward Date </b></td>
<td> <?php echo htmlentities($result['ForwadDate']);?></td>
</tr>
<?php } ?>

<!-- admin Remarks -->
<?php $ret=mysqli_query($con,"select complaintremark.remark as remark,complaintremark.status as sstatus,complaintremark.remarkDate as rdate from complaintremark join tblcomplaints on tblcomplaints.complaintNumber=complaintremark.complaintNumber where complaintremark.complaintNumber='".$_GET['cid']."'");
while($rw=mysqli_fetch_array($ret))
{
?>
<tr>
<td><b>Remark</b></td>
<td colspan="3"><?php echo  htmlentities($rw['remark']); ?> 
<th>Remark By :</th>
<td>Admin</td>
</tr>

<tr>
<td><b>Status</b></td>
<td colspan="3"><?php echo  htmlentities($rw['sstatus']); ?></td>
<th>Remark Date :</th>
<td ><?php echo  htmlentities($rw['rdate']); ?></td>
</tr>
<?php }?>



<!-- Sub admin Remarks -->
<?php $ret1=mysqli_query($con,"select tblsubadminremark.ComplainRemark,tblsubadminremark.ComplainStatus,tblsubadminremark.PostingDate,
	tblsubadmin.SubAdminName,tblsubadmin.Department from tblsubadminremark 
	join tblcomplaints on tblcomplaints.complaintNumber=tblsubadminremark.ComplainNumber 
	join tblsubadmin on tblsubadmin.id=tblsubadminremark.RemarkBy 
	where tblsubadminremark.ComplainNumber='".$_GET['cid']."'");
while($rww=mysqli_fetch_array($ret1))
{
?>
<tr>
<td><b>Remark</b></td>
<td colspan="3"><?php echo  htmlentities($rww['ComplainRemark']); ?> 
<th>Remark By :</th>
<td><?php echo  htmlentities($rww['SubAdminName']); ?>-(<?php echo  htmlentities($rww['Department']); ?>)</td>
</tr>

<tr>
<td><b>Status</b></td>
<td colspan="3"><?php echo  htmlentities($rww['ComplainStatus']); ?></td>
<th>Remark Date :</th>
<td ><?php echo  htmlentities($rww['PostingDate']); ?></td>
</tr>
<?php }?>

<tr>
											<td><b>Action</b></td>
											
											<td colspan="5"> 
<?php 
if($row['status']!='closed'){
$cno=$_GET['cid'];
$sql=mysqli_query($con,"select  id from tblforwardhistory 
join tblcomplaints on tblcomplaints.complaintNumber=tblforwardhistory.ComplaintNumber
	where tblforwardhistory.ComplaintNumber='$cno' and (tblcomplaints.status='in process' || tblcomplaints.status='' || tblcomplaints.status is null) ");
$result=mysqli_num_rows($sql);
if($result==0){
?>
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Forward To</button>
<?php }} ?>


											<?php if($row['status']=="closed"){

												} else {?>
<a href="javascript:void(0);" onClick="popUpWindow('updatecomplaint.php?cid=<?php echo htmlentities($row['complaintNumber']);?>');" title="Update order">
											 <button type="button" class="btn btn-primary">Take Action</button>
											</a><?php } ?>

											<a href="javascript:void(0);" onClick="popUpWindow('userprofile.php?uid=<?php echo htmlentities($row['userId']);?>');" title="Update order">
											 <button type="button" class="btn btn-primary">View User Detials</button></a></td>
											
										</tr>
										<?php  } ?>
										
								</table>
							</div>
						</div>						

						
						
					</div><!--/.content-->
				</div><!--/.span9-->
			</div>
		</div><!--/.container-->
	</div><!--/.wrapper-->
<!-- Complaint Forward To --->
 <form name="forwardto" method="post">
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Complaint Number# <?php echo $_GET['cid'];?></h4>
      </div>
     
      <div class="modal-body">
      	<label class="control-label" for="forwardto"><strong>Forward To</strong></label>
        <p><select class="span4 tip" name="forwardto" required="true">
        	<option value=""> Select SubAdmin/ Subordinate</option>
        	<?php $ret=mysqli_query($con,"select id,SubAdminName,Department from  tblsubadmin");
        	while($row=mysqli_fetch_array($ret))
        	{
        	?>
        	<option value="<?php echo $row['id']?>"><?php echo $row['SubAdminName']?> (<?php echo $row['Department']?>)</option>
        <?php } ?>
        </select></p>
      </div>
 
      <div class="modal-footer">
      <button type="submit" class="btn btn-primary" name="fwdsubmit">Submit</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
     </form>
<!-- Complaint Forward To End --->

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