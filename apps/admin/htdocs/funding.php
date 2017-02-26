<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	
    <meta name="description" content="">
    <meta name="author" content="">

    <title>CrowdFunder</title>

    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
	
	<!-- Ionicons -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  
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
		<li class="treeview">
          <a href="projects.php">
            <i class="fa fa-lightbulb-o"></i> <span>Projects</span>
          </a>
        </li>
		<li class="active treeview">
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
        Funding Management
      </h1>
    </section>

      <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box project-box">
            <div class="box-header">
              <h3 class="box-title">All Fundings</h3>
              <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#fundingForm" show="false"><span><i class="fa fa-plus"></i></span> New Funding</button><br/>
      
            </div>
            <!-- /.box-header -->
            <div class="box-body">
            <!-- Modal -->
            <div id="fundingForm" class="modal fade" role="dialog">
              <div class="modal-dialog">
              
              
              <!-- Modal content-->
              <div class="modal-content">
              <form id="add-project-form" role="form" method="post">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">New Funding</h4>
                </div>
                <div class="modal-body">
                  <div class="input-group">
                    <span class="input-group-addon">Amount</span>
                    <input name="amount" type="text" class="form-control" placeholder="Enter Funding Amount">
                  </div><br/>
                  <div class="input-group">
                    <span class="input-group-addon">Project ID</span>
                    <input name="project_id" type="text" class="form-control" placeholder="Enter Project ID">
                  </div><br/>            
                  <div class="input-group">
                    <span class="input-group-addon">User Email</span>
                    <input name="email" type="number" class="form-control" placeholder="Enter User Email">
                  </div><br/>
                  
                </div>
                <div class="modal-footer">
                <button type="submit" name="fundingForm" class="btn btn-primary">Add Funding</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </form>     
                <?php
                  date_default_timezone_set('Singapore');
                  if(isset($_POST['fundingForm'])){      
                    $date = date('Y-m-d', time());              
                    $query = "INSERT INTO Trans (amount, date, email, projectId)
                        VALUES ('".$_POST['amount']."','".$date."','".$_POST['email']."','".$_POST['project_id'];
                    
                    $result = pg_query($query) or die('Query failed: ' . pg_last_error());
                    //echo "<script type='text/javascript'>alert('".pg_affected_rows($result)."');</script>";
                  }
                ?>  
              </div>
            </div>
          </div>
          <br/>
          <table id="usersTable" class="table table-bordered table-hover" >
                    <thead>
              <tr>
                <th>Amount</th>
                <th>Date</th>
                <th>Project Name</th>
                <th>Donor Email</th>
                <th></th>
                <th></th>
              </tr>
                    </thead>
                    <tbody id="table_data">
                    <?php
              // Category has ID as its pri key here
              // $query = 'SELECT p.title, p.startDate, p.endDate, c.name, p.amountFundingSought, p.email, b.sum
              //     FROM Category c, 
              //       Project p LEFT OUTER JOIN (SELECT t.project_id, SUM(t.amount) AS SUM FROM Trans t GROUP BY t.project_id) b ON b.project_id = p.id 
              //     WHERE c.id = p.category ORDER BY p.end_date DESC, p.start_date DESC';

                $query = 'SELECT t.amount, t.date, p.title, t.email
                          FROM Trans t, Project p
                          WHERE t.projectId = p.id
                          ORDER BY t.date DESC';
              $result = pg_query($query) or die('Query failed: ' . pg_last_error());
             
              while($row=pg_fetch_assoc($result)) {

                  
                  echo "<tr><td>$".$row['amount'].
                  "</td><td>".$row['date'].
                  "</td><td>".$row['title'].
                  "</td><td>".$row['email']."</td>";

                  echo "<td><button class=\"btn btn-primary btn-xs\"><span class=\"glyphicon glyphicon-info-sign\"></span></button></td><td><button class=\"btn btn-danger btn-xs\"><span class=\"glyphicon glyphicon-trash\"></span></button></td></tr>"; 
                  
                }
              
              pg_free_result($result);
            ?>
                    </tbody>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
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
  <script src="plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
  </body>
</html>