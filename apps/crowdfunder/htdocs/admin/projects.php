<!DOCTYPE html>
<html lang="en">
	<head>
    	<meta charset="utf-8">
    	<link rel="icon" href="../../favicon.ico">

    	<title>CrowdFunder</title>

	    <!-- Bootstrap core CSS -->
	    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<!-- Font Awesome -->
		<link rel="stylesheet" href="../plugins/font-awesome.min.css">
		<!-- daterange picker -->
		<link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">
	    <!-- Custom styles for this template -->
	    <link href="../main.css" rel="stylesheet">
  	</head>
  	<body>
	    <?php
		    session_start();
		    if (!isset($_SESSION['usr_id'])) {
		      header("Location: ../login.php");
		    } else if ($_SESSION['usr_role'] == 2) {
		      header("Location: ../user/index.php");
		    }

		  	$dbconn = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=postgres")
		      or die('Could not connect: ' . pg_last_error());

		      $query = "SELECT m.firstname, m.lastname, m.email, m.registrationdate, COUNT(p.id) AS pCount, COUNT(DISTINCT t.projectid) AS     tCount, SUM(t.amount) AS tSum
		              FROM member m LEFT OUTER JOIN project p ON m.email = p.email
		                            LEFT OUTER JOIN trans t ON t.email = m.email
		              WHERE m.email = '".$_SESSION['usr_id']."'
		              GROUP BY m.firstname, m.lastname, m.email, m.registrationdate";
		      $result = pg_query($query) or die('Query failed: ' . pg_last_error());
		      $user=pg_fetch_assoc($result);
	  	?>
		<div class="wrapper" style="height: auto;">
		  	<header class="main-header">
		      	<a href="index.php" class="logo">
		        	<span class="logo-lg"><b>CrowdFunder</b>Admin</span>
		      	</a>
		      	<nav class="navbar navbar-static-top">
			        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
			          <span class="sr-only">Toggle navigation</span>
			        </a>
		        	<div class="navbar-custom-menu">
			          	<ul class="nav navbar-nav">
				            <li class="user user-menu">
				              <a href="#index.php">
				                <span class="hidden-xs">Profile</span>
				              </a>
				            </li>
			            	<li class="dropdown user user-menu">
			              		<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $user['firstname']." ".$user['lastname'];?><span class="caret"></span></a>
				              	<ul class="dropdown-menu">
				              		<li><a href="../user/index.php">Switch to user</a></li>
				                	<li><a href="../logout.php">Sign Out</a></li>
			              		</ul>
			          		</li>
			          	</ul>
		        	</div>
		      	</nav>
		    </header>
			<aside class="main-sidebar">
				<section class="sidebar" style="height:auto;">
				  	<ul class="sidebar-menu">
					    <li class="header">NAVIGATION</li>
					    <li class="treeview">
					      <a href="index.php">
					        <i class="fa fa-dashboard"></i> <span>Dashboard</span>
					      </a>
					    </li>
						<li class="treeview">
					      <a href="users.php">
					        <i class="fa fa-users"></i> <span>Users</span>
					      </a>
					    </li>
						<li class="active treeview">
					      <a href="projects.php">
					        <i class="fa fa-lightbulb-o"></i> <span>Projects</span>
					      </a>
					    </li>
						<li class="treeview">
					      <a href="funding.php">
					        <i class="fa fa-dollar"></i> <span>Funding</span>
					      </a>
					    </li>
						<li class="treeview">
					      <a href="categories.php">
					        <i class="fa fa-gear"></i> <span>Category</span>
					      </a>
					    </li>
						<li class="treeview">
					      <a href="analytics.php">
					        <i class="fa fa-dollar"></i> <span>Analytics</span>
					      </a>
					    </li>
						<li class="treeview">
					      <a href="reactivation.php">
					        <i class="fa fa-recycle"></i> <span>Reactivation</span>
					      </a>
					    </li>
					    <li clas="treeview">
				          <a href="history.php">
				            <i class="fa fa-history"></i><span>History</span>
				          </a>
				        </li>
				  	</ul>
				</section>
			</aside>
	     	<div class="content-wrapper" style="min-height:916px;">
			    <section class="content-header">
		     		<h1>Project Management</h1>
			    </section>
	    		<section class="content">
	      			<div class="row">
	        			<div class="col-xs-12">
	          				<div class="box project-box">
					            <div class="box-header">
				              		<h3 class="box-title">All Projects</h3>
							  		<button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#projectForm" show="false"><span><i class="fa fa-plus"></i></span> New Project</button><br/>
					            </div>
	            				<div class="box-body">
	        						<form id="search-project-form" role="form" method="post">
							            <div class="row extra-bottom-padding">
											<div class="col-md-3">
												<div class="input-group">
													<input name="search-project-title" type="text" class="form-control" placeholder="Project title"/>
													<span class="input-group-addon">
														<i class="fa fa-info-circle"></i>
													</span>
												</div>
											</div>
											<div class="col-md-2">
												<div class="input-group">
													<input name="search-owner-name" type="text" class="form-control" placeholder="Owner name"/>
													<span class="input-group-addon">
														<i class="fa fa-user"></i>
													</span>
												</div>
											</div>
											<div class="col-md-2">
												<select name="search-category" class="form-control" method="post">
												<option disabled selected>Select a category</option>
													<?php
														$query = 'SELECT * FROM Category c';
														$result = pg_query($query) or die('Query failed: ' . pg_last_error());

														while($row=pg_fetch_assoc($result)) {
															echo "<option value=".$row['id'].">".$row['name']."</option>";
														}

														pg_free_result($result);
													?>
												</select>
											</div>
											<div class="col-md-2">
												<select name="search-amount-raised" class="form-control" method="post">
													<option disabled selected>Total Amount Raised</option>
													<option value="0 1">$0 to $1k Raised</option>
													<option value="1 10">$1k to $10k Raised</option>
													<option value="10 100">$10k to $100k Raised</option>
													<option value="100 1000">$100k to $1M Raised</option>
													<option value="1000 2147483647">>$1M Raised</option>
												</select>
											</div>
											<div class="col-md-2">
												<select name="search-amount-goal" class="form-control" method="post">
													<option disabled selected>Total Goal Amount</option>
													<option value="0 1">$0 to $1k goal</option>
													<option value="1 10">$1k to $10k goal</option>
													<option value="10 100">$10k to $100k goal</option>
													<option value="100 1000">$100k to $1M goal</option>
													<option value="1000 2147483647">>$1M goal</option>
												</select>
											</div>
											<div class="col-md-1" >
												<button name="search-submit" type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
											</div>
										</div>
									</form>
								</div>

								<!-- Modal -->
								<div id="projectForm" class="modal fade" role="dialog">
				  					<div class="modal-dialog">
										<div class="modal-content">
											<form id="add-project-form" role="form" method="post">
											  	<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title">New Project</h4>
											  	</div><br/>
											  	<div class="modal-body">
													<div class="input-group">
														<span class="input-group-addon">Title</span>
														<input name="title" type="text" class="form-control" placeholder="Enter Project Title">
													</div><br/>
													<div class="input-group">
														<span class="input-group-addon">Description</span>
														<textarea name="description" class="form-control custom-control" rows="3" style="resize:none" placeholder="Enter Project Description"></textarea>
													</div><br/>
													<div class="input-group">
													  <div class="input-group-addon">
														<i class="fa fa-calendar"></i>
													  </div>
													  <input name="duration" type="text" class="form-control pull-right" id="project-duration">
													</div><br/>
													<div class="input-group">
														<span class="input-group-addon"><i class="fa fa-dollar"></i></span>
														<input name="amount" type="number" class="form-control" placeholder="Goal Amount">
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
																		echo "<option value='".$row['id']."'>".$row['name']."</option>";
																	}

																pg_free_result($result);
															?>
														</select>
													</div><br/>
													<div class="input-group">
														<span class="input-group-addon"><i class="fa fa-user"></i></span>
														<select name="organiser" class="form-control">
															<option value="" disabled selected>Select an Organiser</option>
															<?php
																	$query = 'SELECT m.firstname, m.lastname, m.email
																				FROM Member m
																				ORDER BY m.firstname';
																	$result = pg_query($query) or die('Query failed: ' . pg_last_error());

																	while($row=pg_fetch_assoc($result)) {
																			echo "<option value='".$row['email']."'>".$row['firstname']." ".$row['lastname']." (".$row['email'].")</option>";
																		}

																	pg_free_result($result);
																?>
														</select>
													</div>
											  	</div>
											  	<div class="modal-footer">
													<button type="submit" name="projectForm" class="btn btn-primary">Add Project</button>
													<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
											  	</div>
											</form>
											<?php
												$error = false;

												if(isset($_POST['projectForm'])){
													
													$title = $_POST['title'];
													$description = $_POST['description'];
													$dateStr = $_POST['duration'];
													$dateArr = (explode(" - ",$dateStr));
													$startDate = date('Y-m-d', strtotime(str_replace('/', '-', $dateArr[0])));
													$endDate = date('Y-m-d', strtotime(str_replace('/', '-', $dateArr[1])));
													
												    if (!preg_match("/^[a-zA-Z0-9 .,\- \/ _]+$/", $title)) {
												        $error = true;
												        $title_error = "Project title must contain only alphanumerics, dashes, underscores, forward slashes and spaces";
												    }

												    if (!preg_match("/^[a-zA-Z0-9 .,\- \/ _]+$/", $description)) {
												        $error = true;
												        $description_error = "Description must contain only alphanumerics, dashes, underscores, forward slashes and spaces";
												    }

												    if(!$error) {
												    	$query = "INSERT INTO Project (title, description, startDate, endDate, categoryId, amountFundingSought, email)
															VALUES ('".$title."','".$description."','".$startDate."','".$endDate."','".$_POST['category']."',".$_POST['amount'].",'".$_SESSION['usr_id']."')";
													
														$result = pg_query($query) or die('Query failed: ' . pg_last_error());
												    } else {
														echo "<script type='text/javascript'>alert('Invalid characters detected in title or description.');</script>";												    
													}
												}
											?>
										</div>
									  </div>
									</div>
									<br/>
									<table id="projectsTable" class="table table-bordered table-hover" >
						                <thead>
											<tr>
												<th>Title</th>
												<th>Start Date</th>
												<th>End Date</th>
												<th>Category</th>
												<th>Amount Raised</th>
												<th>Organiser</th>
												<th></th>
												<th></th>
											</tr>
	                					</thead>
	                					<tbody id="table_data">
							                <?php
												ob_start();
												$query = 'SELECT p.id, p.title, p.startDate, p.endDate, c.name, p.amountFundingSought, p.email, b.sum
														FROM Project p LEFT OUTER JOIN (SELECT t.projectId, SUM(t.amount) AS SUM
																					FROM Trans t
																					GROUP BY t.projectId) b ON b.projectId = p.id
																					, Category c
														WHERE c.id = p.categoryId AND p.softDelete = FALSE
														ORDER BY p.endDate DESC, p.startDate DESC';
												$result = pg_query($query) or die('Query failed: ' . pg_last_error());

												while($row=pg_fetch_assoc($result)) {
													if ((!is_null($row['sum'])) && ($row['sum'] >= $row['amountfundingsought'])) {
														echo "<tr style=\"background-color:#c9ffc9;\">";
													} else {
														echo "<tr>";
													}

													echo "<td>".$row['title']
													."</td><td>".$row['startdate']
													."</td><td>".$row['enddate']
													."</td><td>".$row['name']
													."</td><td><div class=\"progress\" style=\"margin-bottom:2px;\"><div class=\"progress-bar progress-bar-success\" role=\"progressbar\" aria-valuenow=\"70\"
													aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width:"
													.(($row['sum'] / $row['amountfundingsought'])*100)
													."%;\">
													</div></div>";

													if (is_null($row['sum'])) {
														echo "$0 / $".$row['amountfundingsought'];
													}else if ($row['sum'] >= $row['amountfundingsought']) {
														echo " <strong style=\"color:#5cb85c;\">$".$row['sum']."</strong> / $".$row['amountfundingsought'];
													} else {
														echo "$".$row['sum']." / $".$row['amountfundingsought'];
													}
								                    $proj_id = $row['id'];

													echo "</td><td>".$row['email'].
													"</td><td><button class=\"btn btn-primary btn-xs\" onClick=\"location.href='project_details.php?id=$proj_id'\"><span class=\"glyphicon glyphicon-info-sign\"></span></button></td>
													<td><button class=\"btn btn-danger btn-xs delete_project\" project-id=\"$proj_id\" href=\"javascript:void(0)\"><span class=\"glyphicon glyphicon-trash\"></span></button></td></tr>";
												}

												pg_free_result($result);

												if (isset(($_POST['search-submit']))) {
													$searchProjectName = $_POST['search-project-title'];
													$searchProjectOwner = $_POST['search-owner-name'];
													$searchCategoryId = $_POST['search-category'];

													$searchAmountRaised = $_POST['search-amount-raised'];
													$amountRaisedArray = explode(" ", $searchAmountRaised);
													$amountRaisedMin = $amountRaisedArray[0] * 1000;
													$amountRaisedMax = $amountRaisedArray[1] * 1000;

													$searchAmountGoal = $_POST['search-amount-goal'];
													$amountGoalArray = explode(" ", $searchAmountGoal);
													$amountGoalMin = $amountGoalArray[0] * 1000;
													$amountGoalMax = $amountGoalArray[1] * 1000;

													$error = false;
													$errorText = "";

													if (!empty($searchProjectName) && !preg_match("/^[a-zA-Z0-9 .,\- \/ _]+$/", $searchProjectName)) {
												        $error = true;
												        $errorText .= "Project title must contain only alphanumerics, dashes, underscores, forward slashes and spaces. ";
												    }

												    if (!empty($searchProjectOwner)  && !preg_match("/^[a-zA-Z ]+$/", $searchProjectOwner)) {
												        $error = true;
												        $errorText .= "Invalid characters detected in searched owner name.";
												    }

												    if ($error) {
														echo "<script type='text/javascript'>alert('{$errorText}');</script>";
														return null;
													}

													$baseQuery = "SELECT p.id, p.title, p.startDate, p.endDate, c.id AS catId,
																		 m.firstName, m.lastName,
																		 c.name, p.amountFundingSought, p.email,
																		 COALESCE(b.transactSum, 0) AS amountRaised
														FROM Project p INNER JOIN Category c ON p.categoryId = c.id
																	   INNER JOIN Member m ON p.email = m.email
																	   LEFT OUTER JOIN (SELECT t.projectId, SUM(t.amount) AS transactSum
																						FROM Trans t
																						GROUP BY t.projectId) b
																	   					ON p.id = b.projectId
														WHERE p.softDelete = FALSE";

													$query = "SELECT * FROM ({$baseQuery}) AS base
														WHERE title LIKE '%{$searchProjectName}%'
														AND (firstName LIKE '%{$searchProjectOwner}%' OR lastName LIKE '%{$searchProjectOwner}%') ";

													if (!empty($searchCategoryId)) {
														$query .= "AND catId = {$searchCategoryId} ";
													}

													if (!empty($amountRaisedMin) || !empty($amountRaisedMax)) {
														$query .= "AND amountRaised <= {$amountRaisedMax} AND amountRaised >= {$amountRaisedMin} ";
													}

													if (!empty($amountGoalMin) || !empty($amountGoalMax)) {
														$query .= "AND amountFundingSought <= {$amountGoalMax} AND amountFundingSought >= {$amountGoalMin} ";
													}

													$query .= "ORDER BY endDate DESC, startDate DESC";

													$result = pg_query($query) or die('Query failed: ' . pg_last_error());

													ob_end_clean();
													ob_start();

													while($row=pg_fetch_assoc($result)) {
														if ((!is_null($row['amountraised'])) && ($row['amountraised'] >= $row['amountfundingsought'])) {
															echo "<tr style=\"background-color:#c9ffc9;\">";
														} else {
															echo "<tr>";
														}

														echo "<td>".$row['title']
														."</td><td>".$row['startdate']
														."</td><td>".$row['enddate']
														."</td><td>".$row['name']
														."</td><td><div class=\"progress\" style=\"margin-bottom:2px;\"><div class=\"progress-bar progress-bar-success\" role=\"progressbar\" aria-valuenow=\"70\"
														aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width:"
														.(($row['amountraised'] * 100 / $row['amountfundingsought']))
														."%;\">
														</div></div>";

														if ($row['amountraised'] >= $row['amountfundingsought']) {
															echo " <strong style=\"color:#5cb85c;\">$".$row['amountraised']."</strong> / $".$row['amountfundingsought'];
														} else {
															echo "$".$row['amountraised']." / $".$row['amountfundingsought'];
														}

									                    $proj_id = $row['id'];

														echo "</td><td>".$row['email'].
														"</td><td><button class=\"btn btn-primary btn-xs\" onClick=\"location.href='project_details.php?id=$proj_id'\"><span class=\"glyphicon glyphicon-info-sign\"></span></button></td>
														<td><button class=\"btn btn-danger btn-xs delete_project\" project-id=\"$proj_id\" href=\"javascript:void(0)\"><span class=\"glyphicon glyphicon-trash\"></span></button></td></tr>";
													}

													pg_free_result($result);
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
		<script src="../plugins/daterangepicker/moment.min.js"></script>
		<script src="../plugins/daterangepicker/daterangepicker.js"></script>
		<script src="../bootstrap/js/bootstrap.min.js"></script>
		<script src="../plugins/bootbox.min.js"></script>
	 	<script>
	 		$(document).ready(function(){
	        	$('.delete_project').click(function(e){

	          		e.preventDefault();

					var pid = $(this).attr('project-id');
					var parent = $(this).parent("td").parent("tr");
					bootbox.dialog({
						message: "Are you sure you want to delete this project?",
						title: "<i class='glyphicon glyphicon-trash'></i> Delete !",
						buttons: {
							danger: {
							  label: "Delete!",
							  className: "btn-danger",
							  callback: function() {

							    $.post('../commons/deletion/delete_project.php', { 'delete':pid })
							    .done(function(response){
							      bootbox.alert(response);
							      parent.fadeOut('slow');
							    })
							    .fail(function(){
							      bootbox.alert('Something Went Wrong ....');
							      })
							    }
							  },
							success: {
							  	label: "No",
							  	className: "btn-success",
						  		callback: function() {
						   			$('.bootbox').modal('hide');
					    		}
						 	}
	            		}
	          		});
	        	});
	  		});
		</script>
		<script>
			$(function() {
				var startDate;
				var endDate;

				$('#project-duration').daterangepicker({
					"minDate": new Date(),
					"locale": {
						"format": "DD/MM/YYYY",
					}
				});
			});
		</script>
  	</body>
</html>