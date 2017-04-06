
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <link rel="icon" href="../../favicon.ico">
  <script src="../plugins/chart.js/dist/Chart.bundle.min.js"></script>
  <script src="../util/charts/projectChart.js"></script>
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
    <!-- /.sidebar -->
  </aside>
  <div class="content-wrapper" style="min-height:916px;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        User Details
      </h1>
    </section>

    <!-- Main content -->
	<section class="content">
		<div class="row">
			<div class="col-md-8">
			  <div class="box project-box">
  				<div class="box-body">
  					<button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#editUserForm" show="false"><span><i class="fa fa-pencil"></i></span></button>
            <?php
              $query = "SELECT m.email, m.firstname, m.lastname, m.registrationdate, c.id AS countryid, c.name AS countryname, b.total_num_donations, b.total_amt_dontaions, b1.total_projects_owned, r.type AS roletype, r.id AS roleid
                        FROM Member m INNER JOIN Country c ON m.countryId = c.id
                          LEFT OUTER JOIN (SELECT t.email, COUNT(t.email) AS total_num_donations, SUM(t.amount) AS total_amt_dontaions
                                FROM Trans t
                                GROUP BY t.email) b ON b.email = m.email
                          LEFT OUTER JOIN (SELECT p.email, COUNT(*) AS total_projects_owned
                                            FROM Project p
                                            GROUP BY p.email) b1 ON b1.email = m.email,
                          Role r
                          WHERE m.roleid=r.id AND m.email ='".$_GET['email']."'";

              $result = pg_query($query) or die('Query failed: ' . pg_last_error());
              $user = pg_fetch_assoc($result);
            ?>
             <!-- Modal -->
             <div id="editUserForm" class="modal fade" role="dialog">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form id="edit-user-form" role="form" method="post">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h4 class="modal-title">Edit User</h4>
                    </div>
                    <div class="modal-body">
                      <div class="input-group">
                        <span class="input-group-addon">First Name</span>
                        <input name="firstname" type="text" class="form-control" placeholder="Enter First Name" value=<?php echo "'".$user['firstname']."'";?>>
                      </div><br/>
                      <div class="input-group">
                        <span class="input-group-addon">Last Name</span>
                        <input name="lastname" type="text" class="form-control" placeholder="Enter Last Name" value=<?php echo "'".$user['lastname']."'";?>>
                      </div><br/>

                      <div class="input-group">
                          <span class="input-group-addon">Country</span>
                          <select name="countryid" class="form-control">
                            <option value="" disabled selected>Select a Country</option>
                            <?php
                              $query = 'SELECT * FROM Country
                                        ORDER BY name';
                              $result = pg_query($query) or die('Query failed: ' . pg_last_error());

                              while($row=pg_fetch_assoc($result)) {
                                if ($user['countryid'] === $row['id']) {
                                  echo "<option value='".$row['id']."' selected='selected'>".$row['name']."</option>";
                                } else {
                                  echo "<option value='".$row['id']."'>".$row['name']."</option>";
                                }
                              }

                              pg_free_result($result);
                            ?>            
                          </select>
                        </div><br/>
                        <div class="input-group">
                            <span class="input-group-addon">Role Type</span>
                            <select name="roleid" class="form-control">
                              <option value="" disabled selected>Select a Role Type</option>
                              <?php
                                $query = 'SELECT * FROM Role
                                          ORDER BY id';
                                $result = pg_query($query) or die('Query failed: ' . pg_last_error());

                                while($row=pg_fetch_assoc($result)) {
                                  if ($user['roleid'] === $row['id']) {
                                    echo "<option value='".$row['id']."' selected='selected'>".$row['type']."</option>";
                                  } else {
                                    echo "<option value='".$row['id']."'>".$row['type']."</option>";
                                  }
                                }

                                pg_free_result($result);
                              ?>            
                            </select>
                          </div><br/>
                        </div>
                      <div class="modal-footer">
                        <button type="submit" name="editUserForm" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      </div>
                    </form>     
                    <?php
                      if(isset($_POST['editUserForm'])){
                        $query = "UPDATE Member SET firstname = '".$_POST['firstname']."', lastname = '".$_POST['lastname']."', countryid = '".$_POST['countryid']."', roleid = ".$_POST['roleid']."
                        WHERE email = '".$_GET['email']."'";
                        $result = pg_query($query) or die('Query failed: ' . pg_last_error());

                        echo "<meta http-equiv='refresh' content='0'>";
                      }
                    ?>  
                  </div>
                </div>
              </div>

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
  								  <b>Country</b> <a class="pull-right"><?php echo $user['countryname'];?></a>
  								</li>
                  <li class="list-group-item">
                    <b>Role Type</b><a class="pull-right"><?php echo $user['roletype'];?></a>
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
                  </li>
                  <li class="list-group-item">
                    <b>Total Projects Owned</b><a class="pull-right"><?php if (!is_null($user['total_projects_owned'])){echo $user['total_projects_owned'];} else {echo "0";}?></a>
                  </li>
  							</ul>
  						</div>
  					</div>
  				</div>
			  </div>
			</div>
      <div class="col-md-4">
        <div class="box project-box">
          <div class="box-body">
            <h3 class="text-center">Donation Details</h3>
            <table id="donationTable" class="table table-bordered table-hover" >
              <thead>
                  <tr>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Project Name</th>
                  </tr>
              </thead>
              <tbody>
              <?php
              $query = "SELECT t.amount, t.date, p.title
                        FROM Trans t, Project p
                        WHERE p.id = t.projectId AND t.email ='".$_GET['email']."'
                        ORDER BY t.date";

              $result = pg_query($query) or die('Query failed: ' . pg_last_error());

              while($row=pg_fetch_assoc($result)) {
                echo "<tr><td>".$row['date'].
                "</td><td>".$row['amount'].
                "</td><td>".$row['title']."</td></tr>";
              }
              pg_free_result($result);

              ?>
              </tbody>
            </table>
          </div>
        </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box project-box">
        <div class="box-body">
          <h3 class="text-center">Project(s) Owned By User</h3>
          <table id="projectsTable" class="table table-bordered table-hover" >
            <thead>
                <tr>
                  <th>Title</th>
                  <th>Description</th>
                  <th>Start Date</th>
                  <th>End Date</th>
                  <th>Category</th>
                  <th>Amount Raised</th>
                </tr>
            </thead>
            <tbody>
            <?php
          $query = "SELECT p.title, p.description, p.startdate, p.enddate, c.name, p.amountfundingsought, p.email, b.total_amount
                    FROM Project p LEFT OUTER JOIN (
                                  SELECT t.projectId, SUM(t.amount) AS total_amount
                                  FROM Trans t
                                  GROUP BY t.projectId) b ON b.projectId = p.id,
                                  Category c
                    WHERE c.id = p.categoryId AND p.softDelete = FALSE AND p.email='".$_GET['email']."'
                    ORDER BY p.endDate DESC, p.startDate DESC";

            $result = pg_query($query) or die('Query failed: ' . pg_last_error());

            while($row=pg_fetch_assoc($result)) {
              echo "<tr><td>".$row['title'].
              "</td><td>".$row['description'].
              "</td><td>".$row['startdate'].
              "</td><td>".$row['enddate'].
              "</td><td>".$row['name'].
              "</td><td><div class=\"progress\" style=\"margin-bottom:2px;\"><div class=\"progress-bar progress-bar-success\" role=\"progressbar\" aria-valuenow=\"70\"
              aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width:"
              .(($row['total_amount'] / $row['amountfundingsought'])*100).
              "%;\"></div></div>";
              if (is_null($row['sum'])) {
                echo "$0 / $".$row['amountfundingsought'];
              }else if ($row['sum'] >= $row['amountfundingsought']) {
                echo " <strong style=\"color:#5cb85c;\">$".$row['sum']."</strong> / $".$row['amountfundingsought'];
              } else {
                echo "$".$row['sum']." / $".$row['amountfundingsought'];
              }
              echo "</td></tr>";
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

    <script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>
