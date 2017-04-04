
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
		<li class="active treeview">
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
			$query = "SELECT m.email, m.firstname, m.lastname, m.registrationdate, c.name, b.total_num_donations, b.total_amt_dontaions
					  FROM Member m INNER JOIN Country c ON m.countryId = c.id
					  	LEFT OUTER JOIN (SELECT t.email, COUNT(t.email) AS total_num_donations, SUM(t.amount) AS total_amt_dontaions
					  				FROM Trans t
					  				GROUP BY t.email) b ON b.email = m.email
					  WHERE m.email ='".$_GET['email']."'";

			$result = pg_query($query) or die('Query failed: ' . pg_last_error());
			$user = pg_fetch_assoc($result);

		?>
		<div class="row">
			<div class="col-md-8">
			  <div class="box project-box">
				<div class="box-body">
					<button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#projectForm" show="false"><span><i class="fa fa-pencil"></i></span></button>
					<h3 class="text-center"><?php echo $user['email'];?></h3>
					<div class="row">
						<div class="col-md-6">
							<ul class="list-group list-group-unbordered">
								<li class="list-group-item">
								  <b>Name</b> <a class="pull-right"><?php echo $user['firstname']." ".$user['lastname'];?></a>
								</li>
								<li class="list-group-item">
								  <b>Registration Date</b> <a class="pull-right"><?php echo $user['registrationdate'];?></a>
								</li>
								<li class="list-group-item">
								  <b>Country</b> <a class="pull-right"><?php echo $user['name'];?></a>
								</li>
							</ul>
						</div>
						<div class="col-md-6">
							<ul class="list-group list-group-unbordered">
								<li class="list-group-item">
								  <b>Total Number of Donations</b> <a class="pull-right">$<?php if (!is_null($user['total_num_donations'])){echo $user['total_num_donations'];} else {echo "0";}?></a>
								</li>
								<li class="list-group-item">
								  <b>Total Donation Amount</b> <a class="pull-right">$<?php if (!is_null($user['total_amt_dontaions'])){echo $user['total_amt_dontaions'];} else {echo "0";}?></a>
<!-- 								</li>
								<li class="list-group-item">
								  <b>Donors</b> <a class="pull-right"><?php if (!is_null($project['donors'])){echo $project['donors'];} else {echo "0";}?></a>
								</li>
								<li class="list-group-item">
								  <b>Donations</b> <a class="pull-right"><?php if (!is_null($project['donations'])){echo $project['donations'];} else {echo "0";}?></a>
								</li> -->
							</ul>
						</div>
					</div>
				</div>
				<!-- /.box-body -->
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
