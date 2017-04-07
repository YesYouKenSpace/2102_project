
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
	 $dbconn = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=postgres")
    or die('Could not connect: ' . pg_last_error());

    $query = "SELECT m.firstname, m.lastname
              FROM Member m 
              WHERE m.email = '".$_SESSION['usr_id']."'";
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
                <li clas="treeview">
                  <a href="history.php">
                    <i class="fa fa-history"></i><span>History</span>
                  </a>
                </li>
          </ul>
    </section>
  </aside>
  <div class="content-wrapper" style="min-height:916px;">
    <section class="content-header">
      <h1>
        Transaction Details
      </h1>
    </section>

	<section class="content">
    <div class="row">
      <div class="col-md-12">
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
                        <input name="amount" type="number" min="100" max="1000000" class="form-control" placeholder="Transaction Amount" value=<?php echo "'".$transaction['amount']."'";?>>
                        <span class="input-group-addon">.00</span>
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
                        $query = "UPDATE Trans SET amount = '".$_POST['amount']."', projectid = '".$_POST['projectid']."'  
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

    <script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>
