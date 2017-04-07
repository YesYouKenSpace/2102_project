
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<link rel="icon" href="../../../favicon.ico">

		<title>CrowdFunder</title>

		<link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="../plugins/font-awesome.min.css">
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

		    $query = "SELECT m.firstname, m.lastname 
		    		  FROM member m
		              WHERE m.email = '".$_SESSION['usr_id']."'";
		    $result = pg_query($query) or die('Query failed: ' . pg_last_error());
		    $user=pg_fetch_assoc($result);
			?>
			<div class="wrapper" style="height: auto;">



		    <header class="main-header">

		    <!-- Logo -->
		    <a href="index.php" class="logo">
		      <!-- logo for regular state and mobile devices -->
		      <span class="logo-lg"><b>CrowdFunder</b>Admin</span>
		    </a>

		    <!-- Header Navbar: style can be found in header.less -->
		    <nav class="navbar navbar-static-top">
		      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
		        <span class="sr-only">Toggle navigation</span>
		      </a>
		      <div class="navbar-custom-menu">
		        <ul class="nav navbar-nav">
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
			<!--Sidebar Nav-->
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
						<li class="treeview">
					  		<a href="projects.php">
					    		<i class="fa fa-lightbulb-o"></i> <span>Projects</span>
					  		</a>
						</li>
						<li class="treeview">
					  		<a href="funding.php">
				    			<i class="fa fa-dollar"></i> <span>Funding</span>
				 	 		</a>
						</li>
						<li class="active treeview">
					  		<a href="categories.php">
					    		<i class="fa fa-gear"></i> <span>Category</span>
					  		</a>
						</li>
						<li class="treeview">
				          <a href="analytics.php">
				            <i class="fa fa-chart"></i> <span>Analytics</span>
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

	  		<!--Main Content-->
			<div class="content-wrapper" style="min-height:916px;">
		    	<section class="content-header">
		      		<h1>Category Management</h1>
		    	</section>
				<section class="content">
					<div class="row">
						<div class="col-md-8">
						  	<div class="row category-row">
						  		<div class="box category-box">
									<div class="box-body">
										<button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#categoryForm" show="false"><span><i class="fa fa-pencil"></i></span></button>

										<?php
											$query = "SELECT * FROM Category c WHERE id = ".$_GET['id'];
											$result = pg_query($query) or die('Query failed: ' . pg_last_error());
											$category = pg_fetch_assoc($result);
										?>
										<!-- Modal -->
										<div id="categoryForm" class="modal fade" role="dialog">
										  <div class="modal-dialog">

											<!-- Modal content-->
											<div class="modal-content">
											  	<form id="edit-category-form" role="form" method="post">
											  		<div class="modal-header">
														<button type="button" class="close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title">Rename Category</h4>
												  	</div>
												  	<div class="modal-body">
														<div class="input-group">
															<span class="input-group-addon">Name</span>
															<input name="catName" type="text" class="form-control" placeholder="Category Name" value=<?php echo "'".$category['name']."'";?>>
														</div><br/>
												  	</div>
												  	<div class="modal-footer">
														<button type="submit" name="categoryForm" class="btn btn-primary">Rename</button>
														<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
												  	</div>
											  	</form>
												<?php
													if(isset($_POST['categoryForm'])){
														$error = false;

														$categoryName = $_POST['catName'];

													  	if (!preg_match("/^[a-zA-Z0-9 .,\- \/ _]+$/", $categoryName)) {
								                                $error = true;
								                                $category_error = "Category Name must contain only alphanumerics, dashes, underscores, forward slashes and spaces";
							                            }

							                            if(!$error) {
							                              	$query = "UPDATE Category SET name = '".$categoryName."'
																		WHERE id = ".$_GET['id'];
															$result = pg_query($query) or die('Query failed: ' . pg_last_error());
															echo "<meta http-equiv='refresh' content='0'>";
							                            } else {
							                              echo "<script type='text/javascript'>alert('Invalid characters detected in title or description.');</script>";                           
							                            }
													}
												?>
											</div>
									  	</div>
									</div>

									<?php
										$query = "SELECT *
													FROM Category c LEFT OUTER JOIN (SELECT p.categoryid, donors, total, COUNT(p.categoryid) AS pCount
																						FROM Project p LEFT OUTER JOIN (SELECT p2.categoryid, COUNT(DISTINCT t.email)						AS donors, SUM(t.amount) AS total
													                            									FROM Project p2 INNER JOIN Trans t ON p2.id = t.projectId
													                            									GROUP BY p2.categoryid) pTrans
													                            						ON p.categoryid = pTrans.categoryId
																						GROUP BY p.categoryid, total, donors) fundedCategories
				                									ON c.id = fundedCategories.categoryid
				                									WHERE c.id = ".$_GET['id'];
										$result = pg_query($query) or die('Query failed: ' . pg_last_error());
										$category = pg_fetch_assoc($result);
									?>
										<h3 class="text-center"><strong><?php echo $category['name'];?></strong></h3>
										<p class="text-center">
											<?php
												if ($category['softdelete'] === 't') {
										  			echo "<span class='label label-danger'>Inactive</span>";
										  		} else {
										  			echo "<span class='label label-success'>Active</span>";
										  		}
									  		?>
										</p>
										<div class="row">
											<div class="col-md-6">
												<ul class="list-group list-group-unbordered">
													<li class="list-group-item">
													  <b>Associated Projects</b>
													  	<a class="pull-right">
													  		<?php
														  		if ($category['pcount'] != 0) {
														  			echo $category['pcount'];
														  		} else {
														  			echo "0";
													  		}?>
												  		</a>
													</li>
													<li class="list-group-item">
													  <b>Funding Achieved</b>
													  	<a class="pull-right">$
														  	<?php
														  		if ($category['total'] != 0) {
														  			echo $category['total'];
														  		} else {
														  			echo "0";
														  		}
													  		?>
														</a>
													</li>
												</ul>
											</div>
											<div class="col-md-6">
												<ul class="list-group list-group-unbordered">
													<li class="list-group-item">
													  	<b>Donors</b>
													  	<a class="pull-right">
														  	<?php
														  		if ($category['donors'] != 0) {
														  			echo $category['donors'];
														  		} else {
														  			echo "0";
														  		}
													  		?>
														</a>
													</li>
													<li class="list-group-item">
													  <b>Amount Raised</b> <a class="pull-right">$<?php if (!is_null($project['sum'])){echo $project['sum'];} else {echo "0";}?></a>
													</li>
												</ul>
											</div>
										</div>
									</div>
					  			</div>
						  	</div>
						  	<div class="row category-row">
						  		<div class="box category-box">
									<div class="box-body">
										<h4 class="text-center">Popular Projects</h4>
										<div class="row category-row-box">
											<table id="usersTable" class="table table-bordered table-hover table-striped" >
												<thead>
													<tr>
														<th></th>
														<th>Title</th>
														<th>Investor</th>
														<th>No. of Donors</th>
														<th>Amount Funded</th>
													</tr>
												</thead>
												<tbody id="table_data">
													<?php
														$query = "SELECT * FROM
																	(SELECT p.title, m.firstname, m.lastname, m.email, COUNT(*) AS donors, SUM(t.amount) AS total, RANK() OVER (ORDER BY SUM(t.amount) DESC) AS ranking
																		FROM Trans t, Project p, Member m
																		WHERE p.id = t.projectid
																		AND p.email = m.email
																		AND p.categoryid = ".$_GET['id']."
																		GROUP BY p.title, m.firstname, m.lastname, m.email) TopProj
																	WHERE TopProj.ranking <= 5
																	ORDER BY TopProj.ranking";
														$result = pg_query($query) or die('Query failed: ' . pg_last_error());

														if (pg_num_rows($result) > 0) {
															while($row=pg_fetch_assoc($result)) {
																echo "<tr>
																	<td><span class='badge badge-".$row['ranking']."'>".$row['ranking']."</span></td>
																	<td>".$row['title']."</td>
																	<td>".$row['firstname']." ".$row['lastname']." (".$row['email'].")</td>
																	<td>".$row['donors']."</td>
																	<td>$".$row['total']."</td>
																	</tr>";
															}
														} else {
															echo "<td colspan=5 class\"text-center\">No donations have been made.</td>";
														}
													?>
												</tbody>
											</table>
										</div>
									</div>
					  			</div>
						  	</div>
						</div>
						<div class="col-md-4">
					  		<div class="box category-box">
								<div class="box-body">
						  			<h4 class="text-center">Top Donors</h4>
									<ul class="list-group list-group-unbordered">
										<?php
											$query = "SELECT *
														FROM (SELECT t.email, m.firstname, m.lastname, SUM(t.amount) AS total, RANK() OVER (ORDER BY SUM(t.amount) DESC) as ranking
														        FROM Trans t INNER JOIN Member m ON t.email = m.email
														                     INNER JOIN Project p
														                        INNER JOIN Category c ON p.categoryId = c.id
														                     ON t.projectid = p.id
														        WHERE c.id = ".$_GET['id']."
														        GROUP BY t.email, m.firstname, m.lastname
														) TopDonors
														WHERE TopDonors.ranking <= 10
														ORDER BY TopDonors.ranking";

											$result = pg_query($query) or die('Query failed: ' . pg_last_error());

											if (pg_num_rows($result) > 0) {
											while($row=pg_fetch_assoc($result)) {
											echo "<li class=\"list-group-item\"><strong>#".$row['ranking']." ".$row['firstname']." ".$row['lastname']."</strong><a class=\"pull-right\">$".$row['total']."</a>";
											}
											} else {
											echo "<li class=\"list-group-item text-center\">No donations have been made.</li>";
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
							<div class="box category-box">
								<div class="box-header">
									<h4 class="box-title">Associated Projects</h4>
								</div>
								<div class="box-body">
									<table id="usersTable" class="table table-bordered table-hover table-striped" >
										<thead>
											<tr>
												<th>Title</th>
												<th>Investor</th>
												<th>Start Date</th>
												<th>End Date</th>
												<th>Goal Amount</th>
												<th>Status</th>
											</tr>
										</thead>
										<tbody id="table_data">
											<?php
												$query = "SELECT p.id, p.title, p.startdate, p.enddate, m.firstname, m.lastname, m.email, p.amountfundingsought, p.softdelete
															FROM Project P, Category c, Member m
															WHERE p.categoryId = c.id
															AND p.email = m.email
															AND c.id = ".$_GET['id']."
															ORDER BY p.title";
												$result = pg_query($query) or die('Query failed: ' . pg_last_error());

												if (pg_num_rows($result) > 0) {
													while($row=pg_fetch_assoc($result)) {
														echo "<tr>
															<td>".$row['title']."</td>
															<td>".$row['firstname']." ".$row['lastname']." (".$row['email'].")</td>
															<td>".$row['startdate']."</td>
															<td>".$row['enddate']."</td>
															<td>$".$row['amountfundingsought']."</td>";
														if ($row['softdelete'] == 'true') {
															echo "<td><span class='label label-default'>Deleted</span></td>";
														} else if (new DateTime() < new DateTime($row['enddate'])) {
															echo "<td><span class='label label-success'>Ongoing</span></td>";
														} else {
															echo "<td><span class='label label-danger'>Past</span></td>";
														}
														echo "</tr>";
													}
												} else {
													echo "<td colspan=3 class\"text-center\">There are no projects in this category.</td>";
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
	    <script src="../bootstrap/js/bootstrap.min.js"></script>
  	</body>
</html>
