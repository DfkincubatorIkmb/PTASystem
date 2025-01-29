<?php session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['login'])==0)
  { 
header('location:index.php');
}
else{ ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Dashboard">
    <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">

    <title>PTA | User Repair Details</title>

    <link rel="icon" href="logopta.png" type="image/png">

    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/style-responsive.css" rel="stylesheet">
  </head>

  <body>

  <section id="container" >
<?php include('includes/header.php');?>
<?php include('includes/sidebar.php');?>
      <section id="main-content" style="padding-left:5%; color:#000">
          <section class="wrapper site-min-height">
          	<h3><i class="fa fa-angle-right"></i>Repair Progress Report</h3>
            <hr />

 <?php $query=mysqli_query($con,"select tblcomplaints.*,category.categoryName as catname from tblcomplaints join category on category.id=tblcomplaints.category where userId='".$_SESSION['id']."' and complaintNumber='".$_GET['cid']."'");
while($row=mysqli_fetch_array($query))
{?>
          	<div class="row mt">
              <table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped  display" width="100%">
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
                      <td><b>Branche</b></td>
                      <td><?php echo htmlentities($row['state']);?></td>
                      <td ><b>Problem</b></td>
                      <td colspan="3"> <?php echo htmlentities($row['noc']);?></td>
                      
                    </tr>
<tr>
                      <td><b>Description</b></td>
                      
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


                    <?php  } ?>
                    
                </table>

              </div>
        
            

  
                  

		</section>
    
<?php include('includes/footer.php');?>


    <!-- js placed at the end of the document so the pages load faster -->
    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery-ui-1.9.2.custom.min.js"></script>
    <script src="assets/js/jquery.ui.touch-punch.min.js"></script>
    <script class="include" type="text/javascript" src="assets/js/jquery.dcjqaccordion.2.7.js"></script>
    <script src="assets/js/jquery.scrollTo.min.js"></script>
    <script src="assets/js/jquery.nicescroll.js" type="text/javascript"></script>


    <!--common script for all pages-->
    <script src="assets/js/common-scripts.js"></script>

    <!--script for this page-->
    
  <script>
      //custom select box

      $(function(){
          $('select.styled').customSelect();
      });

  </script>

  </body>
</html>
<?php } ?>
