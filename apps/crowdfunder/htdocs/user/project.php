<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <link rel="icon" href="../../favicon.ico">
    <title>Dashboard</title>

    <!-- Bootstrap core CSS -->
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  	<!-- Ionicons -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link href="../main.css" rel="stylesheet">
  	</head>
  	<body>
		<?php    
		session_start();
	    if (isset($_SESSION['usr_id'])) {
	      if ($_SESSION['usr_role'] == 1) {
	        header("Location: ../index.php");
	      }
	    } else {
	      header("Location: ../login.php");
	    }

		$dbconn = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=postgres")
					or die('Could not connect: ' . pg_last_error());
		?>
		<div class="wrapper" style="height: auto;">
    		<header class="main-header">
			    <a href="index.php" class="logo logouser">
			      <span class="logo-lg"><b>CrowdFunder</b></span>
			    </a>
    			<nav class="navbar navbaruser navbar-static-top">
	          		<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
		            	<span class="sr-only">Toggle navigation</span>
		          	</a>
		          	<div class="navbar-custom-menu">
		            	<ul class="nav navbar-nav">
		              		<li class="user user-menu">
		                		<a href="index.php">
		                  			<span class="hidden-xs">Profile</span>
		                		</a>
		              		</li>
		              		<li class="user user-menu">
	                			<a href="projects.php">
		                  			<span class="hidden-xs">View Projects</span>
	                			</a>
		              		</li>
		              		<li class="dropdown user user-menu">
				                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $user['firstname']." ".$user['lastname'];?><span class="caret"></span></a>
				                <ul class="dropdown-menu">
				                  <li><a href="../logout.php">Sign Out</a></li>
				                </ul>
				            </li>
	            		</ul>
	          		</div>
	        	</nav>
  			</header>
     		<div class="content-wrapper content-wrapper-user" style="min-height:916px;">
				<section class="content">
					<?php
						$query = "SELECT p.title, p.description, p.startdate, p.enddate, p.amountfundingsought, c.name, c.id AS cId, p.email, m.firstname, m.lastname, b.sum, b.donations, b.donors
								  FROM Project p INNER JOIN Member m ON p.email = m.email
												 LEFT OUTER JOIN (SELECT t.projectId, COUNT(t.email) AS Donations, COUNT(DISTINCT t.email) AS Donors, SUM(t.amount) AS SUM
																  FROM Trans t
																  GROUP BY t.projectId) b ON b.projectId = p.id
												 INNER JOIN category c ON c.id = p.categoryId
								  WHERE p.id =".$_GET['id'];
						$result = pg_query($query) or die('Query failed: ' . pg_last_error());
						$project = pg_fetch_assoc($result);
					?>
					<div class="row">
						<div class="col-md-8">
			  				<div class="box project-box">
								<div class="box-body">
									<?php
										if ($project['email'] == $_SESSION['usr_id']) {
											echo "<button type=\"button\" class=\"btn btn-primary pull-right\" data-toggle=\"modal\" data-target=\"#editProjectForm\" show=\"false\"><span><i class=\"fa fa-pencil\"></i></span> </button>";
										}
									?>
									<!-- Modal -->
									<div id="editProjectForm" class="modal fade" role="dialog">
				  						<div class="modal-dialog">
					
											<div class="modal-content">
												<form id="edit-project-form" role="form" method="post">
												  	<div class="modal-header">
														<button type="button" class="close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title">Edit Project</h4>
												  	</div>
												  	<div class="modal-body">
														<div class="input-group">
															<span class="input-group-addon">Title</span>
															<input name="title" type="text" class="form-control" placeholder="Enter Project Title" value=<?php echo "'".$project['title']."'";?>>
														</div><br/>
														<div class="input-group">
															<span class="input-group-addon">Description</span>
															<textarea name="description" class="form-control custom-control" rows="3" style="resize:none" placeholder="Enter Project Description"><?php echo 
																$project['description'];?></textarea>
														</div><br/>
														<div class="input-group">
														  <div class="input-group-addon">
															<i class="fa fa-calendar"></i>
														  </div>
														  <input name="duration" type="text" class="form-control pull-right" id="project-duration" value=<?php echo "'".date('d/m/Y', strtotime(str_replace('-', '/', $project['startdate'])))." - ".date('d/m/Y', strtotime(str_replace('-', '/', $project['enddate'])))."'";?> readonly>
														</div><br/>
														<div class="input-group">
															<span class="input-group-addon"><i class="fa fa-dollar"></i></span>
															<input name="amount" type="number" min="100" max="1000000" class="form-control" placeholder="Goal Amount" value=<?php echo "'".$project['amountfundingsought']."'";?>>
															<span class="input-group-addon">.00</span>
														</div><br/>
														<div class="input-group">
															<span class="input-group-addon"><i class="fa fa-hashtag"></i></span>
															<select name="category" class="form-control">
									 							<option value="" disabled selected>Select a category</option>
															 	<?php
																	$query = 'SELECT * FROM Category c';
																	$result = pg_query($query) or die('Query failed: ' . pg_last_error());
														 
																	while($row=pg_fetch_assoc($result)) {
																			if ($project['cid'] === $row['id']) {
																			echo "<option value='".$row['id']."' selected='selected'>".$row['name']."</option>";
																		} else {
																			echo "<option value='".$row['id']."'>".$row['name']."</option>";
																		}
																	}
																	
																	pg_free_result($result);
																?>						
															</select>
														</div><br/>
					  								</div>
												  	<div class="modal-footer">
														<button type="submit" name="editProjectForm" class="btn btn-primary">Save</button>
														<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
											  		</div>
												</form>			
												<?php
												if(isset($_POST['editProjectForm'])){
													$dateStr = $_POST['duration'];
													$dateArr = (explode(" - ",$dateStr));
													$startDate = date('Y-m-d', strtotime(str_replace('/', '-', $dateArr[0])));
													$endDate = date('Y-m-d', strtotime(str_replace('/', '-', $dateArr[1])));
													
													$query = "UPDATE Project SET title = '".$_POST['title']."', description = '".$_POST['description']."', startdate = '".$startDate."', enddate = '".$endDate."', categoryid = '".$_POST['category']."', amountfundingsought = ".$_POST['amount']."
															WHERE id = ".$_GET['id'];
													$result = pg_query($query) or die('Query failed: ' . pg_last_error());
												}
											?>	
											</div>
				  						</div>
									</div>
									<h3 class="text-center"><?php echo $project['title'];?></h3>
									<p class="text-center"><em><?php echo $project['description'];?></em></p>
									<div class="progress" style="margin-bottom:2px;">
										<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo (($project['sum'] / $project['amountfundingsought'])*100);?>%;"></div>
									</div>
									<p style="text-align:center;">
										<?php
										    if (!is_null($project['sum'])){
												echo "<strong style=\"color:#00a65a;\">$".$project['sum']."</strong> raised of $".$project['amountfundingsought']." goal<br/>";
											} else {
												echo "<strong style=\"color:#00a65a;\">$0</strong> raised of $".$project['amountfundingsought']." goal<br/>";
											}

											if ($project['email'] == $_SESSION['usr_id'] && new DateTime() > new DateTime($project['enddate'])) {
												echo "<span class=\"label label-danger\">Inactive</span>";
											} else if ($project['email'] == $_SESSION['usr_id']) {
												echo "<span class=\"label label-success\">Active</span>";
											}
											if ($project['sum'] >= $project['amountfundingsought']) {
												echo " <span class=\"label label-success\">100% Funded</span>";
											} else {
												echo " <span class=\"label label-warning\">".floor(($project['sum'] / $project['amountfundingsought'])*100)."% Funded</span>";
											}
										?>
									</p>
									<div class="row">
										<div class="col-md-6">
											<ul class="list-group list-group-unbordered">
												<li class="list-group-item">
												  <b>Organiser</b> <a class="pull-right"><?php echo $project['firstname']." ".$project['lastname'];?></a>
												</li>
												<li class="list-group-item">
												  <b>Start Date</b> <a class="pull-right"><?php echo date('d/m/Y', strtotime(str_replace('-', '/', $project['startdate'])));?></a>
												</li>
												<li class="list-group-item">
												  <b>End Date</b> <a class="pull-right"><?php echo date('d/m/Y', strtotime(str_replace('-', '/', $project['enddate'])));?></a>
												</li>
												<li class="list-group-item">
												  <b>Category</b> <a class="pull-right"><?php echo $project['name'];?></a>
												</li>
											</ul>
										</div>
										<div class="col-md-6">
											<ul class="list-group list-group-unbordered">
												<li class="list-group-item">
												  <b>Amounf of Funding Sought</b> <a class="pull-right">$<?php echo $project['amountfundingsought'];?></a>
												</li>
												<li class="list-group-item">
												  <b>Amount Raised</b> <a class="pull-right">$<?php if (!is_null($project['sum'])){echo $project['sum'];} else {echo "0";}?></a>
												</li>
												<li class="list-group-item">
												  <b>Donors</b> <a class="pull-right"><?php if (!is_null($project['donors'])){echo $project['donors'];} else {echo "0";}?></a>
												</li>
												<li class="list-group-item">
												  <b>Donations</b> <a class="pull-right"><?php if (!is_null($project['donations'])){echo $project['donations'];} else {echo "0";}?></a>
												</li>
											</ul>
										</div>
									</div>
									<div class="row">
										<?php
											$query = "SELECT SUM(t.amount) AS tSUM
													  FROM Trans t
													  WHERE t.email = '".$_SESSION['usr_id']."' AND t.projectid =".$_GET['id'];
											$result = pg_query($query) or die('Query failed: ' . pg_last_error());
											$donation = pg_fetch_assoc($result);
										?>
										<div class="col-md-12">
											<div><span>You have donated <strong>$<?php if (is_null($donation['tsum'])) { echo "0"; } else { echo $donation['tsum'];}?></strong> to this project.</span></div>
											<button type="button" class="btn btn-success" data-toggle="modal" data-target="#donationForm" show="false" style="width: inherit;"><span><i class="fa fa-dollar"></i> Donate</span></button>
										</div>
										<!-- Modal -->
										<div id="donationForm" class="modal fade" role="dialog">
					  						<div class="modal-dialog">
						
												<div class="modal-content">
													<form id="donate-form" role="form" method="post">
													  	<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title">Make a Donation</h4>
													  	</div>
													  	<div class="modal-body">
															<div class="input-group">
																<span class="input-group-addon"><i class="fa fa-dollar"></i></span>
																<input name="amount" type="number" min="10" class="form-control" placeholder="Amount">
																<span class="input-group-addon">.00</span>
															</div><br/>
						  								</div>
													  	<div class="modal-footer">
															<button type="submit" name="donationForm" class="btn btn-success">Donate</button>
															<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
												  		</div>
													</form>			
													<?php
													if(isset($_POST['donationForm'])){
														$query = "INSERT INTO Trans (amount, date, email, projectid)
																VALUES (".$_POST['amount'].", current_date, '".$_SESSION['usr_id']."',".$_GET['id'].")";
														
														$result = pg_query($query) or die('Query failed: ' . pg_last_error());
													}
												?>	
												</div>
					  						</div>
										</div>
									</div>
								</div>
			  				</div>
						</div>
						<div class="col-md-4">
			  				<div class="box project-box">
								<div class="box-body">
				  					<h4 class="text-center">Top Donors</h4>
				  					<ul class="list-group list-group-unbordered">
									  	<?php
											$query = "SELECT *
													  FROM (SELECT t.amount, t.email, m.firstname, m.lastname, RANK() OVER (ORDER BY amount DESC) as ranking
															FROM Trans t INNER JOIN Member m ON t.email = m.email
															WHERE t.projectid = ".$_GET['id']."
														   ) TopDonors
													  WHERE TopDonors.ranking <= 10
													  ORDER BY TopDonors.ranking";

											$result = pg_query($query) or die('Query failed: ' . pg_last_error());

											if (pg_num_rows($result) > 0) {
												while($row=pg_fetch_assoc($result)) {
													echo "<li class=\"list-group-item\"><strong>#".$row['ranking']." ".$row['firstname']." ".$row['lastname']."</strong><a class=\"pull-right\">$".$row['amount']."</a>";
												}
											} else {
												echo "<li class=\"list-group-item text-center\">No donations has been made.</li>";
											}

											pg_free_result($result);
									  	?>
				  					</ul>
								</div>
			  				</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="box project-box">
								<div class="box-header">
									<h3 class="box-title">All Donations</h3>
								</div>
								<div class="box-body">
									<table id="usersTable" class="table table-bordered table-hover table-striped" >
										<thead>
											<tr>
												<th>Date of Donation</th>
												<th>Donor</th>
												<th>Amount</th>
											</tr>
										</thead>
										<tbody id="table_data">
											<?php
												$query = 'SELECT t.email, t.amount, t.date, m.firstname, m.lastname
														  FROM Trans t INNER JOIN Member m ON t.email = m.email
														  WHERE t.projectid = '.$_GET['id'].'
														  ORDER BY t.date DESC';
												$result = pg_query($query) or die('Query failed: ' . pg_last_error());

												if (pg_num_rows($result) > 0) {
													while($row=pg_fetch_assoc($result)) {
														echo "<tr>
															<td>".$row['date']."</td>
															<td>".$row['firstname']." ".$row['lastname']." (".$row['email'].")</td>
															<td>$".$row['amount']."</td>
														  </tr>";
													}
												} else {
													echo "<td colspan=3 class\"text-center\">No donations has been made.</td>";
												}

											?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
    			</section>
  			</div>
		</div>
    	<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
		<script src="../plugins/daterangepicker/daterangepicker.js"></script>
		<script src="../bootstrap/js/bootstrap.min.js"></script>
		<script src="../plugins/bootbox.min.js"></script>
	</body>
</html>
