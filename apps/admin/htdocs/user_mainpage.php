
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	
    <meta name="description" content="">
    <meta name="author" content="">

    <title>CrowdFunder</title>

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
  <?php
	$dbconn = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=postgres")
    or die('Could not connect: ' . pg_last_error());
	?>
	<div class="wrapper" style="height: auto;">
	
	
	
    <header class="main-header">

    <!-- Logo -->
    <a href="index.php" class="logo">
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>CrowdFunder</b></span>
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
              <span class="hidden-xs">User</span>
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
          <p>User</p>
        </div>
      </div>
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
        <li class="header">NAVIGATION</li>
        <li class="active treeview">
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
		<li class="treeview">
          <a href="categories.php">
            <i class="fa fa-gear"></i> <span>Category</span>
          </a>
        </li>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
    <div class="content-wrapper" style="min-height: 976px;">
		<!-- Main component for a primary marketing message or call to action -->
		<section class="content">
		<div class="row">
			<div class="col-lg-3">
				<div class="small-box bg-blue">
					<div class="inner">
						<?php
							$query = 'SELECT COUNT(DISTINCT t.email) FROM trans t';
							$result = pg_query($query) or die('Query failed: ' . pg_last_error());
       
							$data=pg_fetch_assoc($result);
							echo "<h3>".$data['count']."</h3>";
						?>
						<p>Investors</p>
					</div>
					<div class="icon">
						<i class="ion ion-android-contacts"></i>
					</div>
					<a href="users.php" class="small-box-footer">View Details <i class="fa fa-arrow-circle-right"></i></a>
				</div>
			</div>
			<div class="col-lg-3">
				<div class="small-box bg-red">
					<div class="inner">
						<?php
							$query = 'SELECT COUNT(DISTINCT m.email) FROM project p, member m WHERE p.email = m.email';
							$result = pg_query($query) or die('Query failed: ' . pg_last_error());
       
							$data=pg_fetch_assoc($result);
							echo "<h3>".$data['count']."</h3>";
						?>
						<p>Entrepreneurs</p>
					</div>
					<div class="icon">
						<i class="ion ion-briefcase"></i>
					</div>
					<a href="users.php" class="small-box-footer">View Details <i class="fa fa-arrow-circle-right"></i></a>
				</div>
			</div>
			<div class="col-lg-3">
				<div class="small-box bg-yellow">
					<div class="inner">
						<?php
							$query = 'SELECT COUNT(*) FROM project p';
							$result = pg_query($query) or die('Query failed: ' . pg_last_error());
       
							$data=pg_fetch_assoc($result);
							echo "<h3>".$data['count']."</h3>";
						?>
						<p>Projects</p>
					</div>
					<div class="icon">
						<i class="ion ion-bulb"></i>
					</div>
					<a href="projects.php" class="small-box-footer">View Details <i class="fa fa-arrow-circle-right"></i></a>
				</div>
			</div>
			<div class="col-lg-3">
				<div class="small-box bg-green">
					<div class="inner">
						<?php
							$query = 'SELECT SUM(t.amount) FROM trans t';
							$result = pg_query($query) or die('Query failed: ' . pg_last_error());
       
							$data=pg_fetch_assoc($result);
							echo "<h3>$".$data['sum']."</h3>";
						?>
						<p>Funded</p>
					</div>
					<div class="icon">
						<i class="ion ion-cash"></i>
					</div>
					<a href="funding.php" class="small-box-footer">View Details <i class="fa fa-arrow-circle-right"></i></a>
				</div>
			</div>
	   </div>
	   </content>
    </div> <!-- /container -->
	</div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="../../dist/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
