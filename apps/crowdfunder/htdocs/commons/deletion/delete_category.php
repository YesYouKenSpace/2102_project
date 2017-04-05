<?php

	$dbconn = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=postgres")
    or die('Could not connect: ' . pg_last_error());
	
	if ($_REQUEST['categoryId']) {
		
		$categoryId = $_REQUEST['categoryId'];
		$query = "UPDATE Category
					SET softdelete = 'true' 
					WHERE id= ".$categoryId;
		$result = pg_query($query) or die('Query failed: ' . pg_last_error());
		
		if ($result) {
			echo "Category has been set to inactive.";
		}
		
	}
?>