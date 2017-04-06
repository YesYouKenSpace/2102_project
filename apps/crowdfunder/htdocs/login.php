<?php
session_start();

if(isset($_SESSION['usr_id'])!="") {
    if ($_SESSION['usr_role'] === 1) {
      header("Location: admin/");
    } else {
      header("Location: user/");
    }
}

$dbconn = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=postgres")
or die('Could not connect: ' . pg_last_error());

//check if form is submitted
if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];
    $query = "SELECT * FROM member WHERE email = '".$_POST['email']."' AND password = crypt('".$_POST['password']."', password)";
    //$result = pg_query($query) or die('Query failed: ' . pg_last_error()); 
    $result = pg_prepare($dbconn, "query", "SELECT * FROM member WHERE email = $1 AND password = crypt($2, password)");
    $result = pg_execute($dbconn, "query", array($_POST['email'], $_POST['password']));
    if ($row = pg_fetch_array($result)) {
        $_SESSION['usr_id'] = $row['email'];
        $_SESSION['usr_role'] = $row['roleid'];
        if ($_SESSION['usr_role'] == 1) {
          header("Location: admin/");
        } else {
          header("Location: user/");
        }
    } else {
        $errormsg = "Incorrect Email or Password!!!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">

    <title>Login</title>

    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="main.css" rel="stylesheet">
    
    
  </head>

<body>

<div class="wrapper" style="height: auto;">
    

    <div class="content-wrapper content-wrapper-user" style="min-height:916px;">

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

    <script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>