<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Crowdfunder</title>
    	<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    	<link href="main.css" rel="stylesheet">
	</head>
	<body>
		<?php
			$dbconn = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=postgres")
	    				or die('Could not connect: ' . pg_last_error());
		?>
		<div class="wrapper" style="height: auto;">
	    	<header class="main-header">
			    <a href="index.php" class="logo logouser">
			      <span class="logo-lg"><b>CrowdFunder</b></span>
			    </a>
	    		<nav class="navbar navbaruser navbar-static-top">
	          		<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
		            	<span class="sr-only">Toggle navigation</span>
		          	</a>
		          	<div class="navbar-custom-menu">
		            	<ul class="nav navbar-nav">
		              		<li class="user user-menu">
		                		<a href="login.php">
		                  			<span class="hidden-xs">Login</span>
		                		</a>
		              		</li>
	            		</ul>
	          		</div>
	        	</nav>
	  		</header>
	  		<section id="home" class="parallax-section">
    				<div class="container" style="position: relative;">
	      				<div class="row">
      						<div>
              					<h1>Crowdfunder</h1>		              
	          				</div>
						</div>
      				</div>
			</section>
			<section id="projects-summary">
				<div class="row">
					<div class="col-md-2">Children</div>
					<div class="col-md-2">Education</div>
					<div class="col-md-2">Humanitarian Assistance</div>
					<div class="col-md-2">Sports</div>
					<div class="col-md-2">Technology</div>
					<div class="col-md-2">Many More...</div>
				</div>
			</section>
	  		<section id="crowdfunder-summary">
				<div class="container">
					<div id="counters" class="row count-wrapper">
      					<div class="col-lg-4">
	      					<h1>$<span id="count1"></span></h1>
	      					<span>Funded</span>
      					</div>
      					<div class="col-lg-4">
      						<h1 id="count2">0</h1>
      						<span>Donors</span>
      					</div>
      					<div class="col-lg-4">
      						<h1 id="count3">0</h1>
      						<span>Projects</span>
      					</div>
					</div>
				</div>
			</section>
	  	</div>


		<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
		<script src="bootstrap/js/bootstrap.min.js"></script>
		<script src="plugins/countUp.js"></script>
		<script>
	  		$(document).ready(function(){
	  			var hasCounters = $('#counters').hasClass('count-wrapper');
			    if (hasCounters) {
			        var options = {
			                    useEasing : true,
			                    useGrouping : true, 
			                    separator : ','
			                };
	                <?php
	                	$query = "SELECT SUM(t.amount) AS totalAmount FROM Trans t
								  WHERE t.softdelete = false";
						$result = pg_query($query) or die('Query failed: ' . pg_last_error());
						$totalAmount = pg_fetch_assoc($result);

						$query = "SELECT COUNT(m.email) AS memberCount FROM Member m
								  WHERE m.softdelete = false";
						$result = pg_query($query) or die('Query failed: ' . pg_last_error());
						$memberCount = pg_fetch_assoc($result);

						$query = "SELECT COUNT(p.id) AS projcount FROM Project p
								  WHERE p.softdelete = false";
						$result = pg_query($query) or die('Query failed: ' . pg_last_error());
						$projectCount = pg_fetch_assoc($result);
					?>
	                // Counter 1
	                var counter1 = new CountUp('count1', 0, <?php print_r($totalAmount['totalamount']);?>, 0, 3, options);
	                counter1.start();
	                // Counter 2
	                var counter2 = new CountUp('count2', 0, <?php print_r($memberCount['membercount']);?>, 0, 3, options);
	                counter2.start();
	                // Counter 3
	                var counter3 = new CountUp('count3', 0, <?php print_r($projectCount['projcount']);?>, 0, 3, options);
	                counter3.start();
			    }
	  		});
	  	</script>
	</body>
</html>