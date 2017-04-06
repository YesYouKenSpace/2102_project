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
        <a href="index.php" class="logo logouser">
          <span><b>CrowdFunder</b></span>
        </a>

        <nav class="navbar navbaruser navbar-static-top">
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
              <li class="user user-menu">
                <a href="projects.php">
                  <span class="hidden-xs">View Projects</span>
                </a>
              </li>
              <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $user['firstname']." ".$user['lastname'];?><span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <?php
                      if (isset($_SESSION['usr_id']) && $_SESSION['usr_role'] == 1) {
                          echo "<li><a href=\"../admin/index.php\">Switch to admin</a></li>";
                      }
                  ?>
                  <li><a href="../logout.php">Sign Out</a></li>
                </ul>
            </li>
            </ul>
          </div>
        </nav>
      </header>
      <div class="content-wrapper content-wrapper-user" style="min-height: 976px;">
        <section class="content">
          <div class="row">

            <div class="col-lg-3">
	            <div class="box project-box">
                <div class="box-body box-profile">
                  <img class="profile-user-img img-responsive img-circle" src="profile.png" alt="User profile picture">
                  <h3 class="profile-username text-center"><?php echo $user['firstname']." ".$user['lastname']; ?></h3>
                  <p class="text-muted text-center"><?php echo $user['email'];?></p>
                  <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                      <b>Member since</b> <a class="pull-right"><?php echo date('d/m/Y', strtotime(str_replace('-', '/', $user['registrationdate'])));?></a>
                    </li>
                    <li class="list-group-item">
                      <b>Projects Created</b> <a class="pull-right"><?php echo $user['pcount'];?></a>
                    </li>
                    <li class="list-group-item">
                      <b>Projects Donated</b> <a class="pull-right"><?php echo $user['tcount'];?></a>
                    </li>
                    <li class="list-group-item">
                      <b>Donation</b> <a class="pull-right">$<?php if ($user['tsum'] != 0) { echo $user['tsum']; } else { echo "0"; }?></a>
                    </li> 
                  </ul>
                </div>
              </div>
		        </div>

            <div class="col-lg-9">
              <div class="panel panel-default category-box">
                <div class="panel-body">
                  <div role="tabpanel">
                    <ul class="nav nav-tabs" role="tablist">
                      <li role="presentation" class="active"><a href="#myprojects" aria-controls="profile" role="tab" data-toggle="tab">My Projects</a></li>
                      <li role="presentation"><a href="#mydonations" aria-controls="messages" role="tab" data-toggle="tab">My Donations</a></li>
                      <li role="presentation"><a href="#myAccount" aria-controls="home" role="tab" data-toggle="tab">Account</a></li>
                    </ul>
                    <div class="tab-content">
                      <div role="tabpanel" class="tab-pane active" id="myprojects">
                        <table id="myProjectsTable" class="table table-bordered table-hover table-striped" >
                          <thead>
                            <tr>
                              <th>Title</th>
                              <th>Start Date</th>
                              <th>End Date</th>
                              <th>Goal Amount</th>
                              <th>Category</th>
                              <th>Funding Received</th>
                              <th>No. of Donors</th>
                              <th>Status</th>
                            </tr>
                          </thead>
                          <tbody id="table_data">
                            <?php
                            $query = "SELECT p.title, p.startdate, p.enddate, p.amountfundingsought, p.softdelete, c.name, b.sum, b.donors
                                      FROM Project p INNER JOIN Member m ON p.email = m.email
                                                     LEFT OUTER JOIN (SELECT t.projectId, COUNT(DISTINCT t.email) AS Donors, SUM(t.amount) AS SUM
                                                                      FROM Trans t
                                                                      GROUP BY t.projectId) b ON b.projectId = p.id
                                                    INNER JOIN category c ON c.id = p.categoryId
                                      WHERE p.softdelete = false AND p.email = '".$_SESSION['usr_id']."'
                                      ORDER BY p.enddate DESC, p.startdate DESC";

                            $result = pg_query($query) or die('Query failed: ' . pg_last_error());
                         
                            if (pg_num_rows($result) > 0) {
                              while($row=pg_fetch_assoc($result)) {
                                echo "<tr>
                                      <td>".$row['title']."</td>
                                      <td>".$row['startdate']."</td>
                                      <td>".$row['enddate']."</td>
                                      <td>$".$row['amountfundingsought']."</td>
                                      <td>".$row['name']."</td>";
                                if ($row['sum'] != 0) {
                                  echo "<td>$".$row['sum']."</td>";
                                } else {
                                  echo "<td>$0</td>";
                                }
                                if ($row['donors'] != 0) {
                                  echo "<td>".$row['donors']."</td>";
                                } else {
                                  echo "<td>0</td>";
                                }
                                if ($row['softdelete'] === 't') {
                                  echo "<td><span class='label label-default'>Deleted</span></td>";
                                } else if (new DateTime() > new DateTime($row['enddate'])) {
                                  echo "<td><span class='label label-danger'>Past</span></td>";
                                } else if ((!is_null($row['sum'])) && ($row['sum'] >= $row['amountfundingsought'])) {
                                  echo "<td><span class='label label-success'>Funded</span></td>";
                                } else {
                                  echo "<td><span class='label label-primary'>Ongoing</span></td>";
                                }
                                echo "</tr>";
                              }
                            } else {
                              echo "<td colspan=9 class\"text-center\">You have not created any project.</td>";
                            }
                          ?>
                          </tbody>
                        </table>
                      </div>
                      <div role="tabpanel" class="tab-pane" id="mydonations">
                        <table id="myDonationsTable" class="table table-bordered table-hover table-striped" >
                          <thead>
                            <tr>
                              <th>Transaction Date</th>
                              <th>Project</th>
                              <th>Amount</th>
                            </tr>
                          </thead>
                          <tbody id="table_data">
                            <?php
                            $query = "SELECT t.date, p.title, t.amount
                                      FROM trans t INNER JOIN project p ON t.projectid = p.id
                                      WHERE t.email = '".$_SESSION['usr_id']."'
                                      ORDER BY t.date DESC";

                            $result = pg_query($query) or die('Query failed: ' . pg_last_error());
                         
                            if (pg_num_rows($result) > 0) {
                              while($row=pg_fetch_assoc($result)) {
                                echo "<tr>
                                      <td>".$row['date']."</td>
                                      <td>".$row['title']."</td>
                                      <td>$".$row['amount']."</td></tr>";
                              }
                            } else {
                              echo "<td colspan=3 class\"text-center\">You have not made any donations.</td>";
                            }
                          ?>
                          </tbody>
                        </table>
                      </div>
                      <div role="tabpanel" class="tab-pane" id="myAccount">
                        <div class="row" style="padding-top: 20px;">
                          <div class="col-lg-12">
                            <span><strong>Email: </strong><?php echo $user['email']?></span><br/>
                            <span>(Member since <?php echo date('d/m/Y', strtotime(str_replace('-', '/', $user['registrationdate'])));?>)</span>
                            <hr>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#changePasswordForm" show="false"><span><i class="fa fa-key"></i></span> Change Password</button>
                            <!-- Modal -->
                            <div id="changePasswordForm" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                    
                                <div class="modal-content">
                                  <form id="password-form" role="form" method="post">
                                      <div class="modal-header">
                                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                                      <h4 class="modal-title">Change Password</h4>
                                      </div>
                                      <div class="modal-body">
                                      <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-key"></i></span>
                                        <input name="newpassword" type="password" class="form-control" placeholder="Enter Password">
                                      </div><br/>
                                      </div>
                                      <div class="modal-footer">
                                      <button type="submit" name="changePasswordForm" class="btn btn-success">Save</button>
                                      <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                      </div>
                                  </form>     
                                  <?php
                                  if(isset($_POST['changePasswordForm'])){
                                    $query = "UPDATE member 
                                              SET password = crypt('".$_POST['newpassword']."', gen_salt('bf',8)) 
                                              WHERE email = '".$_SESSION['usr_id']."'";
                                    $result = pg_query($query) or die('Query failed: ' . pg_last_error());
                                  }
                                ?>  
                                </div>
                                </div>
                            </div>
                          </div> 
                        </div>
                      </div>                     
                    </div>
                  </div>
                </div>
              </div>
            </div> 
          </div>
        </section>
      </div>
    </div>
    <script src="../plugins/jquery/jquery-2.2.3.min.js"></script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>