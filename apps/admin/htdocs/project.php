
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">
    <script src="plugins/chart.js/dist/Chart.bundle.min.js"></script>
    <script src="util/charts/projectChart.js"></script>
    <title>Dashboard</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">

  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">

    <!-- daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- bootstrap datepicker -->
  <link rel="stylesheet" href="plugins/datepicker/datepicker3.css">

    <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap.css">

    <!-- Custom styles for this template -->
    <link href="main.css" rel="stylesheet">


  </head>

  <body>
	<?php
	$dbconn = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=postgres")
    or die('Could not connect: ' . pg_last_error());
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
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <span class="hidden-xs">Admin</span>
            </a>
          </li>
        </ul>
      </div>

    </nav>
  </header>
<!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar" style="height:auto;">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
        </div>
        <div class="pull-left info">
          <p>Admin</p>
        </div>
      </div>
      <!-- sidebar menu: : style can be found in sidebar.less -->
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
          <a href="index.php">
            <i class="fa fa-gear"></i> <span>Settings</span>
          </a>
        </li>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
     <div class="content-wrapper" style="min-height:916px;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Project Management
      </h1>
    </section>

    <!-- Main content -->
	<section class="content">
		<?php
			$query = "SELECT p.title, p.description, p.startdate, p.enddate, p.amountfundingsought, c.name, p.email, m.firstname, m.lastname, b.sum, b.donations, b.donors
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
					<button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#projectForm" show="false"><span><i class="fa fa-pencil"></i></span></button>
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

							if (new DateTime() > new DateTime($project['enddate'])) {
								echo "<span class=\"label label-danger\">Inactive</span>";
							} else {
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
								  <b>Start Date</b> <a class="pull-right"><?php echo $project['startdate'];?></a>
								</li>
								<li class="list-group-item">
								  <b>End Date</b> <a class="pull-right"><?php echo $project['enddate'];?></a>
								</li>
								<li class="list-group-item">
								  <b>Category</b> <a class="pull-right"><?php echo $project['name'];?></a>
								</li>
							</ul>
						</div>
						<div class="col-md-6">
							<ul class="list-group list-group-unbordered">
								<li class="list-group-item">
								  <b>Amount of Funding Sought</b> <a class="pull-right">$<?php echo $project['amountfundingsought'];?></a>
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
				</div>
				<!-- /.box-body -->
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
				<!-- /.box-body -->
			  </div>
			</div>
		</div>
    <div class="row">
      <div class="col-md-6">
        <div class="box project-box">
          <div class="box-body">
            <canvas id="dailyChart" width="1328" height="664" style="display: block; height: 332px; width: 664px;"></canvas>
            <script>
            <?php
            $query = "SELECT t1.date, sum(t1.amount) AS sum
                            FROM Trans t1
                            WHERE t1.projectId = ".$_GET['id']."
                            GROUP BY t1.date, t1.projectId
                            ORDER BY t1.date ASC";
            $result = pg_query($query) or die('Query failed: '.pg_last_error());
            $graphData = array();
            $graphLabels = array();
            $count =0;
            while($buffer = pg_fetch_assoc($result)){
                if($count!=0){
                  $graphData[$count] = (int) ($buffer['sum']); // + $graphData[$count - 1]);
                  $graphLabels[$count] = $buffer['date'];
                }else{
                  $graphData[$count] = (int) $buffer['sum'];
                  $graphLabels[$count] = $buffer['date'];
                }

                  $count++;
            }
            pg_free_result($result);
            ?>
              console.log("DRAWING");
              drawLineGraph(<?php echo json_encode($graphData) ?>, "$ Raised daily",<?php echo json_encode($graphLabels) ?>, document.getElementById("dailyChart"));

            </script>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="box project-box">
          <div class="box-body">
            <canvas id="progressChart" width="1328" height="664" style="display: block; height: 332px; width: 664px;"></canvas>
            <script>
            <?php
            $query = "SELECT sum(t.amount) AS sum
                      FROM(SELECT   CASE
                            WHEN current_date - t1.date <7 THEN 1
                            WHEN current_date - t1.date  <15 then 2
                            WHEN current_date - t1.date  <22 then 3
                            WHEN current_date - t1.date  <30 then 4
                            ELSE 5 END as rank, t1.amount
                            FROM Trans t1
                            WHERE t1.projectId = ".$_GET['id'].") AS t
                            GROUP BY t.rank
                            ORDER BY t.rank DESC";
            $result = pg_query($query) or die('Query failed: '.pg_last_error());
            $graphData = array();
            $graphLabels = [">1 month ago",  "A month ago", "Three weeks ago" , "Two weeks ago",  "This week"];
            $count =0;
            while(($buffer = pg_fetch_assoc($result)) || $count <5){
                if($buffer ==null && $count==0){
                  $graphData[$count] = 0;
                }else if($buffer ==null){
                  $graphData[$count] = (int) (0 + $graphData[$count - 1]);
                }
                else if($count!=0){
                  $graphData[$count] = (int) ($buffer['sum'] + $graphData[$count - 1]);
                }else{
                  $graphData[$count] = (int) ($buffer['sum']);
                }

                  $count++;
            }
            pg_free_result($result);
            ?>
              console.log("DRAWING");
              drawLineGraph(<?php echo json_encode($graphData) ?>, "Aggregate $ raised", <?php echo json_encode($graphLabels) ?>, document.getElementById("progressChart"));

            </script>
          </div>
        </div>
      </div>
    </div>
		<!-- /.row -->
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
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
	</div>



    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>

	<!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>

  <!-- date-range-picker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>

  <!-- bootstrap datepicker -->
<script src="plugins/datepicker/bootstrap-datepicker.js"></script>

	<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
	<script>
    /*$(function () {
		$('#usersTable').DataTable({
			"paging": true,
			"lengthChange": true,
			"searching": true,
			"ordering": true,
			"info": true,
			"autoWidth": false
		});
	});

	function myFunction() {
		var table_data = document.getElementById("table_data").innerHTML;
	}*/

    //Date range picker
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
