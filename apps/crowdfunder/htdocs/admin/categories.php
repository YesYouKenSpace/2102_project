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

        <!-- Logo -->
        <a href="index.php" class="logo">
          <span class="logo-lg"><b>CrowdFunder</b>Admin</span>
        </a>

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

	     	<!--Main Content-->
	     	<div class="content-wrapper" style="min-height:916px;">
	    		<section class="content-header">
	      			<h1>Category Management</h1>
	    		</section>
			    <section class="content">
			      	<div class="row">
			        	<div class="col-xs-12">
			          		<div class="box category-box">
			            		<div class="box-header">
			              			<h3 class="box-title">All Categories</h3>
						  			<button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#categoryForm" show="false">
						  				<span><i class="fa fa-plus"></i></span> New Category
					  				</button><br/>
					  				<!-- Modal -->
									<div id="categoryForm" class="modal fade" role="dialog">
									  	<div class="modal-dialog">
											<!-- Modal content-->
											<div class="modal-content">
										  		<form id="add-category-form" role="form" method="post">
										  			<div class="modal-header">
														<button type="button" class="close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title">New Category</h4>
											  		</div>
												  	<div class="modal-body">
														<div class="input-group">
															<span class="input-group-addon">Category Name</span>
															<input name="catName" type="text" class="form-control" placeholder="Category Name">
														</div><br/>
												  	</div>
												  	<div class="modal-footer">
														<button type="submit" name="categoryForm" class="btn btn-primary">Add</button>
														<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
												  	</div>
											  	</form>
												<?php
													$error = false;
													if(isset($_POST['categoryForm'])){
														$catName = $_POST['catName'];
														if (!preg_match("/^[a-zA-Z0-9 .,\- \/ _]+$/", $catName)) {
												        	$error = true;
												        	$title_error = "Project title must contain only alphanumerics, dashes, underscores, forward slashes and spaces";
												    	}
												    	if(!$error) {
															$query = "INSERT INTO Category (name) VALUES ('".$_POST['catName']."')";
															$result = pg_query($query) or die('Query failed: ' . pg_last_error());
															echo "<meta http-equiv='refresh' content='0'>";
														} else {
														echo "<script type='text/javascript'>alert('Invalid characters detected in category name.');</script>";												    
													}
													}
												?>
											</div>
									  	</div>
									</div>
			            		</div><br/>

			            		<div class="box-body">
									<table id="categoryTable" class="table table-bordered table-hover" >
						                <thead>
											<tr>
												<th>Name</th>
												<th>Associated Projects</th>
												<th>No. of Donors</th>
												<th>Funding Achieved</th>
												<th>Status</th>
												<th></th>
												<th></th>
											</tr>
						                </thead>
						                <tbody id="table_data">
							                <?php
												$query = 'SELECT *
															FROM Category c LEFT OUTER JOIN (SELECT p.categoryid, donors, total, COUNT(p.categoryid) AS pcount
																					FROM Project p LEFT OUTER JOIN (SELECT p2.categoryid, COUNT(DISTINCT t.email) AS donors, SUM(t.amount) AS total
													                            FROM Project p2 INNER JOIN Trans t ON p2.id = t.projectId
													                            GROUP BY p2.categoryid) pTrans
													                            ON p.categoryid = pTrans.categoryId
																			GROUP BY p.categoryid, total, donors) fundedCategories
				                											ON c.id = fundedCategories.categoryid
				                							ORDER BY c.name';
												$result = pg_query($query) or die('Query failed: ' . pg_last_error());

												while($category=pg_fetch_assoc($result)) {
								                    $categoryId = $category['id'];
													echo "<tr><td>".$category['name']."</td>";

													if ($category['pcount'] != 0) {
											  			echo "<td>".$category['pcount']."</td>";
											  		} else {
											  			echo "<td>0</td>";
											  		}
											  		if ($category['donors'] != 0) {
											  			echo "<td>".$category['donors']."</td>";
											  		} else {
											  			echo "<td>0</td>";
											  		}
											  		if ($category['total'] != 0) {
											  			echo "<td>$".$category['total']."</td>";
											  		} else {
											  			echo "<td>$0</td>";
											  		}
											  		if ($category['softdelete'] === 't') {
											  			echo "<td><span class='label label-danger'>Inactive</span></td>";
											  		} else {
											  			echo "<td><span class='label label-success'>Active</span></td>";
											  		}
											  		echo "<td>
											  				<button class=\"btn btn-primary btn-xs\" onClick=\"location.href='category_details.php?id=$categoryId'\">
											  					<span class=\"glyphicon glyphicon-info-sign\"></span>
										  					</button>
										  				  </td>";
													if ($category['softdelete'] === 't') {
											  			echo "<td></td></tr>";
											  		} else {
											  			echo "<td>
														  		<button class=\"btn btn-danger btn-xs delete_category\" category-id=\"$categoryId\" href=\"javascript:void(0)\">
														  			<span class=\"glyphicon glyphicon-trash\"></span>
													  			</button>
													  	  	</td></tr>";
													}

													echo "</td>";
												}
												pg_free_result($result);
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

		<!-- jQuery 2.2.3 -->
		<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
		<!-- Bootstrap -->
		<script src="../bootstrap/js/bootstrap.min.js"></script>
		<!--Bootbox-->
		<script src="../plugins/bootbox.min.js"></script>

		<script>
			$(document).ready(function(){

				$('.delete_category').click(function listener(e){
					e.preventDefault();

					var categoryId = $(this).attr('category-id');
					var parent = $(this).parent("td").parent("tr");

					bootbox.dialog({
						message: "Confirm delete? (Associated projects will still keep the category.)",
						title: "<i class='glyphicon glyphicon-trash'></i> Delete Category",
						buttons: {
							danger: {
								label: "Delete",
								className: "btn-danger",
								callback: function() {

									$.post('../commons/deletion/delete_category.php', { 'categoryId':categoryId })
									.done(function(response){
										var values = response.split("/~/");
										bootbox.alert(values[0]);
										$("#table_data").html(values[1]);
										$('.delete_category').click(listener);
									})
									.fail(function(){
										bootbox.alert('Something Went Wrong ....');
									})
								}
							},
							success: {
								label: "No",
								className: "btn",
								callback: function() {
									$('.bootbox').modal('hide');
								}
							}
						}
					});
				});
			});
		</script>
	</body>
</html>
