
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
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">

	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">

  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">

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
<!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar" style="height:auto;">
      <!-- Sidebar user panel -->

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
    <!-- /.sidebar -->
  </aside>
    <div class="content-wrapper" style="min-height: 976px;">
		<!-- Main component for a primary marketing message or call to action -->
		<section class="content">

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
      <div class="row">
        <div class="col-md-6">
         <div class="box project-box">
           <div class="box-body">
              <h3 class="text-center">Popular Project of the Month</h3>
              <ul class="list-group list-group-unbordered">
               <?php
               $query = "SELECT SUM(t1.amount) AS sum, p1.title, RANK() OVER (ORDER BY SUM(t1.amount) DESC) as ranking
                          FROM Trans t1 INNER JOIN Project p1 ON t1.projectId = p1.id
                          WHERE current_date - current_date < 30
                          GROUP BY t1.projectId, p1.title
                          ORDER BY sum DESC";

               $result = pg_query($query) or die('Query failed: ' . pg_last_error());

               if (pg_num_rows($result) > 0) {
                  $num_to_display = 10;
                 while($num_to_display > 0 && $row=pg_fetch_assoc($result)) {
                    $num_to_display--;
                   echo "<li class=\"list-group-item\"><strong>#".$row['ranking']." ".$row['title']." </strong><a class=\"pull-right\">$".$row['sum']."</a>";
                 }
               } else {
                 echo "<li class=\"list-group-item text-center\">No funding has been made this month.</li>";
               }

               pg_free_result($result);
               ?>

             </ul>
            </div>
          </div>
        </div>
        <div class="col-md-6">
         <div class="box project-box">
           <div class="box-body">
              <h3 class="text-center">10 Newest Projects</h3>
              <table id="projectsTable" class="table table-bordered table-hover" >
                        <thead>
        					<tr>
        						<th>Title</th>
        						<th>Start Date</th>
        						<th>Amount Raised</th>
                    <th></th>
        					</tr>
                        </thead>
                        <tbody id="table_data">
                          <?php
                          ob_start();
                          $query = "SELECT SUM(t1.amount) AS sum, t1.projectId AS pid, p1.title AS title, RANK() OVER (ORDER BY p1.startdate DESC) as ranking, p1.amountFundingSought, p1.startDate
                                     FROM Trans t1 INNER JOIN Project p1 ON t1.projectId = p1.id
                                     WHERE p1.softDelete = FALSE
                                     GROUP BY t1.projectId, p1.title, p1.amountFundingSought, p1.startDate
                                     ORDER BY p1.startDate DESC";
                          $count = 0;
                          $result = pg_query($query) or die('Query failed: ' . pg_last_error());

                					while($row=pg_fetch_assoc($result) && $count<10) {
                						if ((!is_null($row['sum'])) && ($row['sum'] >= $row['amountfundingsought'])) {
                							echo "<tr style=\"background-color:#c9ffc9;\">";
                						} else {
                							echo "<tr>";
                						}

                						echo "<td>".$row['title']
                						."</td><td>".$row['startdate']
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

                						echo "</td><td><button class=\"btn btn-primary btn-xs\" onClick=\"location.href='project_details.php?id=$proj_id'\"><span class=\"glyphicon glyphicon-info-sign\"></span></button></td></tr>";
                            $count++;
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
    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
