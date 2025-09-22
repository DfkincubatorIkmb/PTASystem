<div class="span3">
					<div class="sidebar">

		<ul class="widget widget-menu unstyled">
							<li><a href="dashboard.php"><i class="menu-icon icon-dashboard"></i>Dashboard</a></li>
							
					
						</ul>




<ul class="widget widget-menu unstyled">
							<li>
								<a class="collapsed" data-toggle="collapse" href="#togglePages">
									<i class="menu-icon icon-cog"></i>
									<i class="icon-chevron-down pull-right"></i><i class="icon-chevron-up pull-right"></i>
									Manage Repair
								</a>
								<ul id="togglePages" class="collapse unstyled">
									<li>
										<a href="notprocess-complaint.php">
											<i class="icon-tasks"></i>
											Repair Not Process Yet 
											<?php
$subid=$_SESSION['suid'];
$rt = mysqli_query($con,"SELECT * FROM tblcomplaints 
join tblforwardhistory on tblforwardhistory.ComplaintNumber=tblcomplaints.complaintNumber	
	where tblcomplaints.status is null and tblforwardhistory.ForwardTo='$subid'");
$num1 = mysqli_num_rows($rt);
{?>
		
											<b class="label orange pull-right"><?php echo htmlentities($num1); ?></b>
											<?php } ?>
										</a>
									</li>
									<li>
										<a href="inprocess-complaint.php">
											<i class="icon-tasks"></i>
											Repair In Progress 
                   <?php 
  $status="in Process";                   
$rt = mysqli_query($con,"SELECT * FROM tblcomplaints
join tblforwardhistory on tblforwardhistory.ComplaintNumber=tblcomplaints.complaintNumber	
 where status='$status' and tblforwardhistory.ForwardTo='$subid'");
$num1 = mysqli_num_rows($rt);
{?><b class="label orange pull-right"><?php echo htmlentities($num1); ?></b>
<?php } ?>
										</a>
									</li>
									<li>
										<a href="closed-complaint.php">
											<i class="icon-inbox"></i>
											Repair Closed 
	     <?php 
  $status="closed";                   
$rt = mysqli_query($con,"SELECT * FROM tblcomplaints
join tblforwardhistory on tblforwardhistory.ComplaintNumber=tblcomplaints.complaintNumber	
 where status='$status' and tblforwardhistory.ForwardTo='$subid' ");
$num1 = mysqli_num_rows($rt);
{?><b class="label green pull-right"><?php echo htmlentities($num1); ?></b>
<?php } ?>

										</a>
									</li>
								</ul>
							</li>
							
					
						</ul>


				<!--/.widget-nav-->

						<ul class="widget widget-menu unstyled">
					
							
							<li>
								<a href="logout.php">
									<i class="menu-icon icon-signout"></i>
									Logout
								</a>
							</li>
						</ul>

					</div><!--/.sidebar-->
				</div><!--/.span3-->
