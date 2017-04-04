<!DOCTYPE html>
<html lang="en">
  	<head>
    	<meta charset="utf-8">
    	<meta name="viewport" content="width=device-width, initial-scale=1">

    	<meta name="description" content="">
    	<meta name="author" content="">
    	<link rel="icon" href="../../favicon.ico">

    	<title>Dashboard</title>

    	<!-- Bootstrap core CSS -->
    	<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<!-- Font Awesome -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  		<!-- Ionicons -->
  		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    	<!-- Custom styles for this template -->
    	<link href="main.css" rel="stylesheet">
	</head>

  	<body>
		<?php $dbconn = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=postgres")
			or die('Could not connect: ' . pg_last_error());?>

		<div class="wrapper" style="height: auto;">
			<!--Header Nav-->
	    	<header class="main-header">
	    		<a href="index.php" class="logo">
	      			<span class="logo-lg"><b>CrowdFunder</b>Admin</span>
	    		</a>
	    		<nav class="navbar navbar-static-top">
			      	<div class="navbar-custom-menu">
			        	<ul class="nav navbar-nav">
			          		<li class="dropdown user user-menu">
			            		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
			              			<span class="hidden-xs">Admin</span>
			            		</a>
			          		</li>
			        	</ul>
			      	</div>
	   			 </nav>
	  		</header>
			<!--Sidebar Nav-->
	  		<aside class="main-sidebar">
				<section class="sidebar" style="height:auto;">
	      			<div class="user-panel">
	        			<div class="pull-left info">
	          				<p>Admin</p>
	        			</div>
	      			</div>
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
													if(isset($_POST['categoryForm'])){
														$query = "INSERT INTO Category (name) VALUES ('".$_POST['catName']."')";
														$result = pg_query($query) or die('Query failed: ' . pg_last_error());
														echo "<script type='text/javascript'>alert('".pg_affected_rows($result)."');</script>";
													}
												?>
											</div>
									  	</div>
									</div>
			            		</div><br/>

			            		<div class="box-body">
									<table id="usersTable" class="table table-bordered table-hover" >
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
													echo "<td>".$category['name']."</td>";
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
											  				<button class=\"btn btn-primary btn-xs\" onClick=\"location.href='category.php?id=$categoryId'\">
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

		<!--JavaScript (Placed at end to load HTML page faster)-->
		<!-- jQuery 2.2.3 -->
		<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
		<!-- Bootstrap -->
		<script src="bootstrap/js/bootstrap.min.js"></script>
		<!--Bootbox-->
		<script src="plugins/bootbox.min.js"></script>

		<script>
			$(document).ready(function(){

				$('.delete_category').click(function(e){
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

									$.post('deletion/delete_category.php', { 'categoryId':categoryId })
										.done(function(response){
										bootbox.alert(response);
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
