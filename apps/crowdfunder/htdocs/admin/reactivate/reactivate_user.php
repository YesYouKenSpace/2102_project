<?php

	$dbconn = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=postgres")
    or die('Could not connect: ' . pg_last_error());
	
	if ($_REQUEST['delete']) {
		
		$email = $_REQUEST['delete'];
		$query = "UPDATE Member
					SET softDelete = FALSE 
					WHERE email= '".$email."'";
		$result = pg_query($query) or die('Query failed: ' . pg_last_error());
		
		if ($result) {
			echo "User Reactivated Successfully ...";
		}
		
	}
?>