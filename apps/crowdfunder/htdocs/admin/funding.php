<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
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
            <div class="row">
                <form id="search-funding-form" role="form" method="post">
                <div class="col-md-4">
                  <div class="input-group">
                    <input name="search-project-title" type="text" class="form-control" placeholder="Project title"/>
                    <span class="input-group-addon">
                      <i class="fa fa-info-circle"></i>
                    </span>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="input-group">
                    <input name="search-donor-name" type="text" class="form-control" placeholder="Donor name"/>
                    <span class="input-group-addon">
                      <i class="fa fa-user"></i>
                    </span>
                  </div>
                </div>
                <div class="col-md-3">
                  <select name="search-amount-donated" class="form-control" method="post">
                    <option disabled selected>Total Amount Donated</option>
                    <option value="0 1">$0 to $1k Donated</option>
                    <option value="1 10">$1k to $10k Donated</option>
                    <option value="10 100">$10k to $100k Donated</option>
                    <option value="100 1000">$100k to $1M Donated</option>
                    <option value="1000 2147483647">>$1M Donated</option>
                  </select>
                </div>
                <div class="col-md-1">
                  <button name="search-submit" type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                </div>
                </form>
              </div>

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
                      <span class="input-group-addon"><i class="fa fa-user"></i></span>
                      <select name="email" class="form-control">
                        <option value="" disabled selected>Select a Donor</option>
                        <?php
                            $query = 'SELECT m.firstName, m.lastName, m.email
                                  FROM Member m
                                  ORDER BY m.firstName';
                            $result = pg_query($query) or die('Query failed: ' . pg_last_error());

                            while($row=pg_fetch_assoc($result)) {
                                echo "<option value='".$row['email']."'>".$row['firstname']." ".$row['lastname']." (".$row['email'].")</option>";
                              }

                            pg_free_result($result);
                          ?>
                      </select>
                    </div><br/>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user"></i></span>
                      <select name="organiser_id" class="form-control">
                        <option value="" disabled selected>Select a Project</option>
                        <?php
                            $query = 'SELECT p.title, p.email, p.id
                                  FROM Project p
                                  ORDER BY p.title';
                            $result = pg_query($query) or die('Query failed: ' . pg_last_error());

                            while($row=pg_fetch_assoc($result)) {
                                echo "<option value='".$row['id']."'>".$row['title']." (".$row['email'].")</option>";
                              }

                            pg_free_result($result);
                          ?>
                      </select>
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
                          VALUES ('".$_POST['amount']."','".$date."','".$_POST['email']."','".$_POST['organiser_id']."')";

                      $result = pg_query($query) or die('Query failed: ' . pg_last_error());
                      echo "<meta http-equiv='refresh' content='0'>";                    
                    }
                  ?>
                </div>
              </div>
            </div>
            <br/>
            <table id="fundingTable" class="table table-bordered table-hover" >
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
                    ob_start();
                    $query = 'SELECT t.amount, t.date, p.title, t.email, t.transactionNo
                                FROM Trans t, Project p
                                WHERE t.projectId = p.id AND t.softDelete = FALSE
                                ORDER BY t.date DESC';
                    $result = pg_query($query) or die('Query failed: ' . pg_last_error());

                    while($row=pg_fetch_assoc($result)) {
                        $trans_no = $row['transactionno'];
                        echo "<tr><td>$".$row['amount'].
                        "</td><td>".$row['date'].
                        "</td><td>".$row['title'].
                        "</td><td>".$row['email']."</td>";

                        echo "<td><button class=\"btn btn-primary btn-xs\" onClick=\"location.href='funding_details.php?trans-no=$trans_no'\"><span class=\"glyphicon glyphicon-info-sign\"></span></button></td>
                        <td><button class=\"btn btn-danger btn-xs delete_funding\" funding-id=\"$trans_no\" href=\"javascript:void(0)\"><span class=\"glyphicon glyphicon-trash\"></span></button></td></tr>";

                    }

                    pg_free_result($result);

                    if (isset(($_POST['search-submit']))) {
                        ob_end_clean();
                        ob_start();
                        $searchProjectTitle = $_POST['search-project-title'];
                        $searchDonorName = $_POST['search-donor-name'];

                        $searchAmountDonated= $_POST['search-amount-donated'];
                        $amountDonatedArray = explode(" ", $searchAmountDonated);
                        $amountDonatedMin = $amountDonatedArray[0] * 1000;
                        $amountDonatedMax = $amountDonatedArray[1] * 1000;

                        $baseQuery = "SELECT m.firstName, m.lastName, t.amount, t.date,
                                             p.title, t.email, t.transactionNo
                                FROM Trans t
                                INNER JOIN Project p ON t.projectId = p.id
                                INNER JOIN Member m ON t.email = m.email
                                WHERE t.softDelete = FALSE";

                        $query = "SELECT * FROM ({$baseQuery}) AS base
                                WHERE title LIKE '%{$searchProjectTitle}%'
                                AND (firstName LIKE '%{$searchDonorName}%' OR lastName LIKE '%{$searchDonorName}%') ";

                        if (!empty($amountDonatedMin) || !empty($amountDonatedMax)) {
                            $query .= "AND amount <= {$amountDonatedMax} AND amount >= {$amountDonatedMin} ";
                        }

                        $query .= "ORDER BY date DESC";

                        $result = pg_query($query) or die('Query failed: ' . pg_last_error());

                        while($row=pg_fetch_assoc($result)) {
                            $trans_no = $row['transactionno'];
                            echo "<tr><td>$".$row['amount'].
                            "</td><td>".$row['date'].
                            "</td><td>".$row['title'].
                            "</td><td>".$row['email']."</td>";

                            echo "<td><button class=\"btn btn-primary btn-xs\" onClick=\"location.href='funding_details.php?trans-no=$trans_no'\"><span class=\"glyphicon glyphicon-info-sign\"></span></button></td>
                            <td><button class=\"btn btn-danger btn-xs delete_funding\" funding-id=\"$trans_no\" href=\"javascript:void(0)\"><span class=\"glyphicon glyphicon-trash\"></span></button></td></tr>";
                        }

                        pg_free_result($result);
                    }
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

  <script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
  <script src="../bootstrap/js/bootstrap.min.js"></script>
  <script src="../plugins/bootbox.min.js"></script>

  <script>
    $(document).ready(function(){

        $('.delete_funding').click(function(e){

          e.preventDefault();

          var pid = $(this).attr('funding-id');
          var parent = $(this).parent("td").parent("tr");
          bootbox.dialog({
            message: "Are you sure you want to delete this funding?",
            title: "<i class='glyphicon glyphicon-trash'></i> Delete !",
            buttons: {
            danger: {
              label: "Delete!",
              className: "btn-danger",
              callback: function() {

                $.post('../commons/deletion/delete_funding.php', { 'delete':pid })
                .done(function(response){
                  bootbox.alert(response);
                  parent.fadeOut('slow');
                })
                .fail(function(){
                  bootbox.alert('Something Went Wrong ....');
                  })
                }
              },
            success: {
              label: "No",
              className: "btn-success",
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
