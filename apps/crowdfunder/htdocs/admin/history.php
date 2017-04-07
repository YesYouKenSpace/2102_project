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

      $query = "SELECT m.firstname, m.lastname, m.email, m.registrationdate, COUNT(p.id) AS pCount, COUNT(DISTINCT t.projectid) AS tCount, SUM(t.amount) AS tSum
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
        <li class="active treeview">
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
        History
      </h1>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box user-box">
            <div class="box-header">
              <h3 class="box-title" id="user-title">History for Users</h3>
              <h3 class="box-title" id="project-title">History for Projects</h3>
              <h3 class="box-title" id="funding-title">History for Fundings</h3>
              <h3 class="box-title" id="category-title">History for Categories</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
      <div class="row">
        <div class="col-md-2">
          <select name="search-item" class="form-control" onChange="getState(this.value);">
            <option value="users">Users History</option>
            <option value="projects">Projects History</option>
            <option value="fundings">Fundings History</option>
            <option value="categories">Categories History</option>
          </select>
        </div>
      </div>

      <br/>
      <table id="usersHistTable" class="table table-bordered table-hover" >
        <thead>
          <tr>
            <th>Date Time</th>
            <th>Operation</th>
            <th>By User</th>
            <th>Email</th>
            <th>Registration Date</th>
            <th>First Name Before</th>
            <th>Last Name Before</th>
            <th>Country Before</th>
            <th>Role Before</th>
            <th>Delete Before</th>
            <th>First Name After</th>
            <th>Last Name After</th>
            <th>Country After</th>
            <th>Role After</th>
            <th>Delete After</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $query = "SELECT m.stamp, m.operation, m.userid, m.email, m.registrationdate, m.firstnamebefore, m.lastnamebefore, 
                      CASE 
                        WHEN m.countryIdBefore IS NULL THEN NULL 
                        ELSE c1.name
                      END AS countrybefore, 
                      CASE 
                        WHEN m.roleidbefore IS NULL THEN NULL
                        ELSE r1.type
                      END AS rolebefore, 
                      CASE 
                        WHEN m.softdeletebefore = FALSE THEN 'No'
                        WHEN m.softdeletebefore = TRUE THEN 'Yes'
                      END as softdeletebefore, m.firstnameafter, m.lastnameafter, c2.name AS countryafter, r2.type AS roleafter, 
                      CASE
                        WHEN m.softdeleteafter = FALSE THEN 'No'
                        WHEN m.softdeleteafter = TRUE THEN 'Yes'
                      END as softdeleteafter
                    FROM Member_log m, Country c1, Country c2, Role r1, Role r2
                    WHERE ((m.countryIdBefore IS NULL AND m.countryIdAfter = c1.id) OR (m.countryIdBefore IS NOT NULL AND m.countryIdBefore = c1.id)) AND m.countryidafter = c2.id AND 
                          ((m.roleidbefore IS NULL AND m.roleIdAfter = r1.id) OR (m.roleidbefore IS NOT NULL AND m.roleidbefore = r1.id)) AND m.roleIdAfter = r2.id
                    ORDER BY m.stamp DESC";
          $result = pg_query($query) or die('Query failed: ' . pg_last_error());

          while($row=pg_fetch_assoc($result)) {
              echo "<tr><td>".$row['stamp']
              ."</td><td>".$row['operation']
              ."</td><td>".$row['userid']
              ."</td><td>".$row['email']
              ."</td><td>".$row['registrationdate']
              ."</td><td>".$row['firstnamebefore']
              ."</td><td>".$row['lastnamebefore']
              ."</td><td>".$row['countrybefore']
              ."</td><td>".$row['rolebefore']
              ."</td><td>".$row['softdeletebefore']
              ."</td><td>".$row['firstnameafter']
              ."</td><td>".$row['lastnameafter']
              ."</td><td>".$row['countryafter']
              ."</td><td>".$row['roleafter']
              ."</td><td>".$row['softdeleteafter']."</td></tr>";
            }

          pg_free_result($result);
          ?>
        </tbody>
      </table>

      <table id="projectsHistTable" class="table table-bordered table-hover" >
        <thead>
          <tr>
            <th>Date Time</th>
            <th>Operation</th>
            <th>By User</th>
            <th>Email</th>
            <th>Title Before</th>
            <th>Description Before</th>
            <th>Amount Before</th>
            <th>Category Before</th>
            <th>Delete Before</th>
            <th>Title After</th>
            <th>Description After</th>
            <th>Amount After</th>
            <th>Category After</th>
            <th>Delete After</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $query = "SELECT p.stamp, p.operation, p.userid, p.email, p.titlebefore, p.descriptionbefore, p.amountfundingsoughtbefore, 
                      CASE 
                        WHEN p.categoryidbefore IS NULL THEN NULL 
                        ELSE c1.name
                      END AS categorybefore,
                      CASE 
                        WHEN p.softdeletebefore = FALSE THEN 'No'
                        WHEN p.softdeletebefore = TRUE THEN 'Yes'
                      END as softdeletebefore, p.titleafter, p.descriptionafter, p.amountfundingsoughtafter, c2.name AS categoryafter, 
                      CASE
                        WHEN p.softdeleteafter = FALSE THEN 'No'
                        WHEN p.softdeleteafter = TRUE THEN 'Yes'
                      END as softdeleteafter
                    FROM Project_log p, Category c1, Category c2
                    WHERE ((p.categoryIdBefore IS NULL AND p.categoryidafter = c1.id) OR (p.categoryidbefore IS NOT NULL AND p.categoryidbefore = c1.id))
                          AND p.categoryIdAfter = c2.id
                    ORDER BY p.stamp DESC";
          $result = pg_query($query) or die('Query failed: ' . pg_last_error());

          while($row=pg_fetch_assoc($result)) {
              echo "<tr><td>".$row['stamp']
              ."</td><td>".$row['operation']
              ."</td><td>".$row['userid']
              ."</td><td>".$row['email']
              ."</td><td>".$row['titlebefore']
              ."</td><td>".$row['descriptionbefore']
              ."</td><td>".$row['amountfundingsoughtbefore']
              ."</td><td>".$row['categorybefore']
              ."</td><td>".$row['softdeletebefore']
              ."</td><td>".$row['titleafter']
              ."</td><td>".$row['descriptionafter']
              ."</td><td>".$row['amountfundingsoughtafter']
              ."</td><td>".$row['categoryafter']
              ."</td><td>".$row['softdeleteafter']."</td></tr>";
            }

          pg_free_result($result);
          ?>
        </tbody>
      </table>
      <table id="fundingsHistTable" class="table table-bordered table-hover" >
        <thead>
          <tr>
            <th>Date Time</th>
            <th>Operation</th>
            <th>By User</th>
            <th>Date</th>
            <th>Email</th>
            <th>Amount Before</th>
            <th>Project Before</th>
            <th>Delete Before</th>
            <th>Amount After</th>
            <th>Project After</th>
            <th>Delete After</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $query = "SELECT t.stamp, t.operation, t.date, t.userid, t.email, t.amountbefore,
                        CASE 
                          WHEN t.projectidBefore IS NULL THEN NULL 
                          ELSE p1.title
                        END AS projecttitlebefore,
                        CASE 
                          WHEN t.softdeletebefore = FALSE THEN 'No'
                          WHEN t.softdeletebefore = TRUE THEN 'Yes'
                        END as softdeletebefore, t.amountafter, p2.title AS projecttitleafter,
                        CASE
                          WHEN t.softdeleteafter = FALSE THEN 'No'
                          WHEN t.softdeleteafter = TRUE THEN 'Yes'
                        END as softdeleteafter
                      FROM Trans_log t, Project p1, Project p2
                      WHERE ((t.projectidBefore IS NULL AND t.projectidAfter = p1.id) OR (t.projectidBefore IS NOT NULL AND t.projectidBefore = p1.id))
                            AND t.projectidAfter = p2.id
                      ORDER BY t.stamp DESC";
            $result = pg_query($query) or die('Query failed: ' . pg_last_error());

            while($row=pg_fetch_assoc($result)) {
                echo "<tr><td>".$row['stamp']
                ."</td><td>".$row['operation']
                ."</td><td>".$row['userid']
                ."</td><td>".$row['date']
                ."</td><td>".$row['email']
                ."</td><td>".$row['amountbefore']
                ."</td><td>".$row['projecttitlebefore']
                ."</td><td>".$row['softdeletebefore']
                ."</td><td>".$row['amountafter']
                ."</td><td>".$row['projecttitleafter']
                ."</td><td>".$row['softdeleteafter']."</td></tr>";
              }

              pg_free_result($result);
            ?>
          </tbody>
      </table>

      <table id="categoryHistTable" class="table table-bordered table-hover" >
        <thead>
          <tr>
            <th>Date Time</th>
            <th>Operation</th>
            <th>By User</th>
            <th>Name Before</th>
            <th>Delete Before</th>
            <th>Name After</th>
            <th>Delete After</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $query = "SELECT c.stamp, c.operation, c.userid, c.namebefore, 
                        CASE 
                          WHEN c.softdeletebefore = FALSE THEN 'No'
                          WHEN c.softdeletebefore = TRUE THEN 'Yes'
                        END as softdeletebefore, c.nameafter, 
                        CASE
                          WHEN c.softdeleteafter = FALSE THEN 'No'
                          WHEN c.softdeleteafter = TRUE THEN 'Yes'
                        END as softdeleteafter
                      FROM Category_log c
                      ORDER BY c.stamp DESC";
            $result = pg_query($query) or die('Query failed: ' . pg_last_error());

            while($row=pg_fetch_assoc($result)) {
                echo "<tr><td>".$row['stamp']
                ."</td><td>".$row['operation']
                ."</td><td>".$row['userid']
                ."</td><td>".$row['namebefore']
                ."</td><td>".$row['softdeletebefore']
                ."</td><td>".$row['nameafter']
                ."</td><td>".$row['softdeleteafter']."</td></tr>";
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
  </div>
  <script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
  <script src="../bootstrap/js/bootstrap.min.js"></script>
  <script src="../plugins/bootbox.min.js"></script>

  <script>
    $(document).ready(function(){
      $("#user-title").show();
      $("#project-title").hide();
      $("#funding-title").hide();
      $("#category-title").hide();

      $("#usersHistTable").show();
      $("#projectsHistTable").hide();
      $("#fundingsHistTable").hide();
      $("#categoryHistTable").hide();
    });
  </script>

  <script>
    function getState(val) {
      if(val == "users") {
        $("#user-title").show();
          $("#project-title").hide();
          $("#funding-title").hide();
          $("#category-title").hide();

        $("#usersHistTable").show();
          $("#projectsHistTable").hide();
          $("#fundingsHistTable").hide();
          $("#categoryHistTable").hide();

      } else if(val == "projects") {
        $("#user-title").hide();
          $("#project-title").show();
          $("#funding-title").hide();
          $("#category-title").hide();

        $("#usersHistTable").hide();
          $("#projectsHistTable").show();
          $("#fundingsHistTable").hide();
          $("#categoryHistTable").hide();

      } else if(val == "fundings") {
        $("#user-title").hide();
          $("#project-title").hide();
          $("#funding-title").show();
          $("#category-title").hide();

        $("#usersHistTable").hide();
          $("#projectsHistTable").hide();
          $("#fundingsHistTable").show();
          $("#categoryHistTable").hide();
      } else if(val == "categories") {
        $("#user-title").hide();
          $("#project-title").hide();
          $("#funding-title").hide();
          $("#category-title").show();

        $("#usersHistTable").hide();
          $("#projectsHistTable").hide();
          $("#fundingsHistTable").hide();
          $("#categoryHistTable").show();
      }
    }
  </script>
  </body>
</html>
