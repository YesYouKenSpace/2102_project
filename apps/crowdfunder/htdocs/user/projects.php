<!DOCTYPE html>
<html lang="en">
	<head>
    	<meta charset="utf-8">
    	<title>CrowdFunder</title>

	    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    	<link rel="stylesheet" href="../plugins/font-awesome.min.css">
	  	<link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">
	    <link href="../main.css" rel="stylesheet">
  </head>
  <body>
	<?php
		session_start();
	    if (!isset($_SESSION['usr_id'])) {
	      header("Location: ../login.php");
	    }

		$dbconn = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=postgres")
	    or die('Could not connect: ' . pg_last_error());

	    $query = "SELECT m.firstname, m.lastname, m.email, m.registrationdate, COUNT(p.id) AS pCount, COUNT(DISTINCT t.projectid) AS tCount, SUM(t.amount) AS tSum
            FROM member m LEFT OUTER JOIN project p ON m.email = p.email
                          LEFT OUTER JOIN trans t ON t.email = m.email
            WHERE m.email = '".$_SESSION['usr_id']."'
            GROUP BY m.firstname, m.lastname, m.email, m.registrationdate";
    	$result = pg_query($query) or die('Query failed: ' . pg_last_error());
    	$user=pg_fetch_assoc($result);
	?>
	<div class="wrapper" style="height: auto;">
    	<header class="main-header">
		    <a href="index.php" class="logo logouser">
		      <span class="logo-lg"><b>CrowdFunder</b></span>
		    </a>
    		<nav class="navbar navbaruser navbar-static-top">
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
			                	<?php
				                  	if (isset($_SESSION['usr_id']) && $_SESSION['usr_role'] == 1) {
				                  		echo "<li><a href=\"../admin/index.php\">Switch to admin</a></li>";
	      							}
			                  	?>
			                  <li><a href="../logout.php">Sign Out</a></li>
			                </ul>
			            </li>
            		</ul>
          		</div>
        	</nav>
  		</header>
     	<div class="content-wrapper content-wrapper-user" style="min-height:916px;">
    		<!-- Main content -->
    		<section class="content">
      			<div class="row">
      				<div class="col-lg-2">
			            <div class="box project-box user-projects-nav">
		                	<div class="box-body">
		                		<h4>Navigation</h4>
			                    <ul class="list-group list-group-unbordered">
			                    	<?php
										$query = "SELECT COUNT(p.id) AS projcount FROM Project p
												  WHERE p.softdelete = false AND p.email = '".$_SESSION['usr_id']."'";
										$result = pg_query($query) or die('Query failed: ' . pg_last_error());
										$projectCount = pg_fetch_assoc($result);
									?>
		                     		<li class="list-group-item list-group-item-user active">
			                      		<a href="#myprojects" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-cube"></i> My Projects 
			                      		<?php 
			                      			if (!is_null($projectCount['projcount'])) { 
			                      				echo "<span class='badge badge-success'>".$projectCount['projcount']."</span>"; 
		                      				} else { 
		                      					echo "<span class='badge'>0</span>";
	                      					}?>
                      					</a>
			                    	</li>
			                    	<?php
										$query = "SELECT COUNT(p.id) AS projcount FROM Project p
												  WHERE p.softdelete = false";
										$result = pg_query($query) or die('Query failed: ' . pg_last_error());
										$projectCount = pg_fetch_assoc($result);
									?>
			                    	<li class="list-group-item list-group-item-user">
				                      	<a href="#allprojects" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-cubes"></i> All Projects
				                      	<?php 
			                      			if (!is_null($projectCount['projcount'])) { 
			                      				echo "<span class='badge badge-primary'>".$projectCount['projcount']."</span>"; 
		                      				} else { 
		                      					echo "<span class='badge'>0</span>";
	                      					}?>
                      					</a>
			                    	</li>
			                  	</ul>
		                	</div>
		              	</div>
		        	</div>
    				<div class="col-lg-10">
    					<div class="tab-content">
    						<div role="tabpanel" class="tab-pane active" id="myprojects">
    							<div class="box project-box">
		            				<div class="box-header">
		              					<h3 class="box-title">My Projects</h3>
		              					<button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#projectForm" show="false"><span><i class="fa fa-plus"></i></span> New Project</button>
			        				</div>
	              					<!-- Modal -->
									<div id="projectForm" class="modal fade" role="dialog">
				  						<div class="modal-dialog">
					
											<div class="modal-content">
												<form id="add-project-form" role="form" method="post">
												  	<div class="modal-header">
														<button type="button" class="close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title">New Project</h4>
												  	</div>
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
															<input name="amount" type="number" min="100" max="1000000" class="form-control" placeholder="Goal Amount">
															<span class="input-group-addon">.00</span>
														</div><br/>
														<div class="input-group">
															<span class="input-group-addon"><i class="fa fa-bookmark"></i></span>
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
					  								</div>
												  	<div class="modal-footer">
														<button type="submit" name="projectForm" class="btn btn-primary">Add Project</button>
														<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
											  		</div>
												</form>			
												<?php
												//set validation error flag as false
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
		            				<div class="box-body">
		            					<table id="myProjectsTable" class="table table-bordered table-hover table-striped" >
			                          		<thead>
				                            	<tr>
				                              		<th>Title</th>
				                              		<th>Start Date</th>
				                              		<th>End Date</th>
				                              		<th>Goal Amount</th>
				                              		<th>Category</th>
				                              		<th>Funding Received</th>
				                              		<th>No. of Donors</th>
				                              		<th>Status</th>
				                              		<th></th>
				                              		<th></th>
				                            	</tr>
				                          	</thead>
				                          	<tbody id="table_data">
					                          	<?php
					                            $query = "SELECT p.id, p.title, p.startdate, p.enddate, p.amountfundingsought, p.softdelete, c.name, b.sum, b.donors
					                                      FROM Project p INNER JOIN Member m ON p.email = m.email
					                                                     LEFT OUTER JOIN (SELECT t.projectId, COUNT(DISTINCT t.email) AS Donors, SUM(t.amount) AS SUM
					                                                                      FROM Trans t
					                                                                      GROUP BY t.projectId) b ON b.projectId = p.id
					                                                    INNER JOIN category c ON c.id = p.categoryId
					                                      WHERE p.email = '".$_SESSION['usr_id']."' AND p.softDelete = false
					                                      ORDER BY p.enddate DESC, p.startdate DESC";
					                            $result = pg_query($query) or die('Query failed: ' . pg_last_error());
					                         
					                            if (pg_num_rows($result) > 0) {
					                              	while($row=pg_fetch_assoc($result)) {
					                              		if ((!is_null($row['sum'])) && ($row['sum'] >= $row['amountfundingsought'])) { 
															echo "<tr style=\"background-color:#c9ffc9;\">";
														} else {
															echo "<tr>";
														}
						                                echo "<td>".$row['title']."</td>
						                                      <td>".date('d/m/Y', strtotime(str_replace('-', '/', $row['startdate'])))."</td>
						                                      <td>".date('d/m/Y', strtotime(str_replace('-', '/', $row['enddate'])))."</td>
						                                      <td>$".$row['amountfundingsought']."</td>
						                                      <td>".$row['name']."</td>";
						                                
						                                if ((!is_null($row['sum'])) && ($row['sum'] >= $row['amountfundingsought'])) {
															echo "<td><strong style=\"color:#5cb85c;\">$".$row['sum']."</strong></td>";
						                                } else if ((!is_null($row['sum']))) {
						                                	echo "<td>$".$row['sum']."</td>";
														} else {
					                                  		echo "<td>$0</td>";
						                                }

						                                if (!is_null($row['donors'])) {
						                                  echo "<td>".$row['donors']."</td>";
						                                } else {
						                                  echo "<td>0</td>";
						                                }
						                                
						                                if (new DateTime() > new DateTime($row['enddate'])) {
						                                  echo "<td><span class='label label-danger'>Past</span></td>";
						                                } else if ((!is_null($row['sum'])) && ($row['sum'] >= $row['amountfundingsought'])) {
						                                  echo "<td><span class='label label-success'>Funded</span></td>";
						                                } else {
						                                  echo "<td><span class='label label-success'>Ongoing</span></td>";
						                                }
						                                $proj_id = $row['id'];

														echo "</td>
															<td><button class=\"btn btn-primary btn-xs\" onClick=\"location.href='project_details.php?id=$proj_id'\"><span class=\"glyphicon glyphicon-info-sign\"></span></button></td>
															<td><button class=\"btn btn-danger btn-xs delete_project\" project-id=\"$proj_id\" href=\"javascript:void(0)\"><span class=\"glyphicon glyphicon-trash\"></span></button></td>
															</tr>";
						                              }
						                            } else {
						                              echo "<td colspan=10 class\"text-center\">You have not created any project.</td>";
						                            }
					                          	?>
				                          	</tbody>
				                        </table>
		            				</div>
	            				</div>
		          			</div>
	          				<div role="tabpanel" class="tab-pane" id="allprojects">
	          					<div class="box project-box">
		            				<div class="box-header">
		              					<h3 class="box-title">All Projects</h3>
					  					<br/>
		            				</div>
		            				<!-- Search filter form -->
						            <div class="box-body">
						            <form id="search-project-form" method="post">
						            <div class="row extra-bottom-padding">
										<div class="col-md-5">
											<div class="input-group">
												<input name="search-project-title" type="text" class="form-control" placeholder="Project title"/>
												<span class="input-group-addon">
													<i class="fa fa-info-circle"></i>
												</span>
											</div>
										</div>
										<div class="col-md-2">
											<select name="search-category" class="form-control">
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
											<select name="search-amount-raised" class="form-control">
												<option disabled selected>Total Amount Raised</option>	
												<option value="0 1">$0 to $1k Raised</option>
												<option value="1 10">$1k to $10k Raised</option>
												<option value="10 100">$10k to $100k Raised</option>
												<option value="100 1000">$100k to $1M Raised</option>
												<option value="1000 2147483647">>$1M Raised</option>
											</select>	
										</div>
										<div class="col-md-2">
											<select name="search-amount-goal" class="form-control">
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
		            				<div class="box-body">
										<table id="projectsTable" class="table table-bordered table-hover" >
							                <thead>
												<tr>
													<th>Title</th>
													<th>Start Date</th>
													<th>End Date</th>
													<th>Category</th>
													<th>Amount Raised</th>
													<th></th>
												</tr>
							                </thead>
							                <tbody id="table_data_all">
								                <?php
													$query = 'SELECT p.id, p.title, p.startDate, p.endDate, c.name, p.amountFundingSought, p.email, b.sum
															FROM Project p LEFT OUTER JOIN (SELECT t.projectId, SUM(t.amount) AS SUM 
																						FROM Trans t
																						GROUP BY t.projectId) b ON b.projectId = p.id 
																						, Category c
															WHERE c.id = p.categoryId AND p.softDelete = FALSE AND P.endDate >= current_date
															ORDER BY p.endDate DESC, p.startDate DESC';
													$result = pg_query($query) or die('Query failed: ' . pg_last_error());
								         
													while($row=pg_fetch_assoc($result)) {
														if ((!is_null($row['sum'])) && ($row['sum'] >= $row['amountfundingsought'])) { 
															echo "<tr style=\"background-color:#c9ffc9;\">";
														} else {
															echo "<tr>";
														}
														
														echo "<td>".$row['title']
														."</td><td>".date('d/m/Y', strtotime(str_replace('-', '/', $row['startdate'])))
														."</td><td>".date('d/m/Y', strtotime(str_replace('-', '/', $row['enddate'])))
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

														echo "</td>
															<td><button class=\"btn btn-primary btn-xs\" onClick=\"location.href='project_details.php?id=$proj_id'\"><span class=\"glyphicon glyphicon-info-sign\"></span></button></td></tr>";
													}
													
													pg_free_result($result);
													
												?>
		                					</tbody>
		              					</table>
		            				</div>
		          				</div>
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

			$('#search-project-form').submit(function(e){
				e.preventDefault();

				var original = $("#table_data_all").html();

				var queryString = $(this).serialize();
				$.post( "filter/filter_projects.php", queryString)
				.done(function(response){
					console.log("response");
					console.log(response);
					if (response != "FAIL") {
						$("#table_data_all").html(response);
					} else {
						alert("Project title must contain only alphanumerics, dashes, underscores, forward slashes and spaces");
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
