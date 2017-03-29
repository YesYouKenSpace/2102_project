<?php
session_start();

if(isset($_SESSION['usr_id'])!="") {
    header("Location: index.php");
}

$dbconn = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=postgres")
or die('Could not connect: ' . pg_last_error());

//check if form is submitted
if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];
    $query = "SELECT * FROM member WHERE email = '".$_POST['email']."' AND password = crypt('".$_POST['password']."', password)";
    $result = pg_query($query) or die('Query failed: ' . pg_last_error()); 

    if ($row = pg_fetch_array($result)) {
        $_SESSION['usr_id'] = $row['email'];
        $_SESSION['usr_name'] = $row['name'];
        header("Location: index.php");
    } else {
        $errormsg = "Incorrect Email or Password!!!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Login</title>

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
              <span class="hidden-xs"></span>
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

  </aside>
    <div class="content-wrapper" style="min-height:916px;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-4 col-md-offset-4 well">
                <form role="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="loginform">
                    <fieldset>
                        <legend>Login</legend>
                        <div class="form-group">
                            <label for="name">Email</label>
                            <input type="text" name="email" placeholder="Your Email" required class="form-control" />
                        </div>

                        <div class="form-group">
                            <label for="name">Password</label>
                            <input type="password" name="password" placeholder="Your Password" required class="form-control" />
                        </div>

                        <div class="form-group">
                            <input type="submit" name="login" value="Login" class="btn btn-primary" />
                        </div>
                    </fieldset>
                </form>
                <span class="text-danger"><?php if (isset($errormsg)) { echo $errormsg; } ?></span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-md-offset-4 text-center">    
            New User? <a href="register.php">Sign Up Here</a>
            </div>
    </section>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <!-- DataTables -->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
    <script src="plugins/bootbox.min.js"></script>
</body>
</html>