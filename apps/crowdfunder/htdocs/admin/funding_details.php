
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <link rel="icon" href="../../favicon.ico">
  <script src="../plugins/chart.js/dist/Chart.bundle.min.js"></script>
  <script src="../util/charts/projectChart.js"></script>
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
	$dbconn = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=postgres")
    or die('Could not connect: ' . pg_last_error());
	?> 
	<div class="wrapper" style="height: auto;">
    <header class="main-header">

    <!-- Logo -->
    <a href="dashboard.php" class="logo">
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

      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
            <li class="header">NAVIGATION</li>
            <li class="treeview">
                <a href="dashboard.php">
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
  <div class="content-wrapper" style="min-height:916px;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Transaction Details
      </h1>
    </section>

    <!-- Main content -->
	<section class="content">
    <div class="row">
      <div class="col-md-10">
        <div class="box project-box">
          <div class="box-body">
            <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#editTransactForm" show="false"><span><i class="fa fa-pencil"></i></span></button>
            <?php
              $query = "SELECT t.transactionno, m.email, p.id AS projectid, p.title AS projectname, t.amount, t.date, p.email AS projectemail, m.firstname, m.lastname, m2.firstname AS receipientfirstname, m2.lastname AS receipientlastname
                        FROM Trans t, Member m, Project p, Member m2
                        WHERE t.email = m.email AND t.projectId = p.id AND p.email=m2.email AND t.transactionNo =".$_GET['trans-no'];

              $result = pg_query($query) or die('Query failed: ' . pg_last_error());
              $transaction = pg_fetch_assoc($result);
            ?>
             <!-- Modal -->
             <div id="editTransactForm" class="modal fade" role="dialog">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form id="edit-transact-form" role="form" method="post">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h4 class="modal-title">Edit Transaction</h4>
                    </div>
                    <div class="modal-body">
                      <div class="input-group">
                        <span class="input-group-addon">Amount $</span>
                        <input name="amount" type="text" class="form-control" placeholder="Enter First Name" value=<?php echo "'".$transaction['amount']."'";?>>
                      </div><br/>
                      <div class="input-group">
                          <span class="input-group-addon">Transaction From (Email)</span>
                          <select name="transactFromEmail" class="form-control">
                            <option value="" disabled selected>Select An Email</option>
                            <?php
                              $query = 'SELECT * 
                                        FROM Member
                                        ORDER BY email';
                              $result = pg_query($query) or die('Query failed: ' . pg_last_error());

                              while($row=pg_fetch_assoc($result)) {
                                if ($transaction['email'] === $row['email']) {
                                  echo "<option value='".$row['email']."' selected='selected'>".$row['email']."</option>";
                                } else {
                                  echo "<option value='".$row['email']."'>".$row['email']."</option>";
                                }
                              }

                              pg_free_result($result);
                            ?>            
                          </select>
                        </div><br/>
                        <div class="input-group">
                            <span class="input-group-addon">Project</span>
                            <select name="projectid" class="form-control">
                              <option value="" disabled selected>Select a Project</option>
                              <?php
                                $query = 'SELECT * FROM Project
                                          ORDER BY title';
                                $result = pg_query($query) or die('Query failed: ' . pg_last_error());

                                while($row=pg_fetch_assoc($result)) {
                                  if ($transaction['projectid'] === $row['id']) {
                                    echo "<option value='".$row['id']."' selected='selected'>".$row['title']."</option>";
                                  } else {
                                    echo "<option value='".$row['id']."'>".$row['title']."</option>";
                                  }
                                }

                                pg_free_result($result);
                              ?>            
                            </select>
                          </div><br/>
                        </div>
                      <div class="modal-footer">
                        <button type="submit" name="editTransactForm" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      </div>
                    </form>     
                    <?php
                      if(isset($_POST['editTransactForm'])){
                        $query = "UPDATE Trans SET amount = '".$_POST['amount']."', projectid = '".$_POST['projectid']."', email = '".$_POST['transactFromEmail']."' 
                        WHERE transactionno = ".$_GET['trans-no'];
                        $result = pg_query($query) or die('Query failed: ' . pg_last_error());
                        
                        echo "<meta http-equiv='refresh' content='0'>";
                      }
                    ?>  
                  </div>
                </div>
              </div>

            <h3 class="text-center">Transaction Number: <?php echo $transaction['transactionno'];?></h3>
            <div class="row">
              <div class="col-md-6">
                <ul class="list-group list-group-unbordered">
                  <li class="list-group-item">
                    <b>Amount</b> <a class="pull-right">$<?php echo $transaction['amount'];?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Date</b> <a class="pull-right"><?php echo $transaction['date'];?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Project Funded</b> <a class="pull-right"><?php echo $transaction['projectname'];?></a>
                  </li>
                </ul>
              </div>
              <div class="col-md-6">
                <ul class="list-group list-group-unbordered">
                  <li class="list-group-item">
                    <b>Transaction From (Name)</b> <a class="pull-right"><?php echo $transaction['firstname']." ".$transaction['lastname'];?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Transaction From (Email)</b> <a class="pull-right"><?php echo $transaction['email'];?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Transaction To (Name)</b> <a class="pull-right"><?php echo $transaction['receipientfirstname']." ".$transaction['receipientlastname'];?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Transaction To (Email)</b> <a class="pull-right"><?php echo $transaction['projectemail'];?></a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
  </div>
  </section>
  </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
    <!-- jQuery 2.2.3 -->
    <script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>

    <!-- date-range-picker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>

    <!-- DataTables -->
    <script src="../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../plugins/datatables/dataTables.bootstrap.min.js"></script>
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
