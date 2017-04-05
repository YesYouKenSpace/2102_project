<?php

	$dbconn = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=postgres")
    or die('Could not connect: ' . pg_last_error());

	if ($_REQUEST['delete']) {

		$email = $_REQUEST['email'];
    $amount = $_REQUEST['amount'];
    $pid = $_REQUEST['pid'];
    $query = "INSERT INTO Trans (amount, date, email, projectid)
        VALUES (".$amount.", current_date, '".$email."',".$pid.")";

    $result = pg_query($query) or die('Query failed: ' . pg_last_error());

    $query = "SELECT p.title, p.description, p.startdate, p.enddate, p.amountfundingsought, c.name, c.id AS cId, p.email, m.firstname, m.lastname, b.sum, b.donations, b.donors
          FROM Project p INNER JOIN Member m ON p.email = m.email
                 LEFT OUTER JOIN (SELECT t.projectId, COUNT(t.email) AS Donations, COUNT(DISTINCT t.email) AS Donors, SUM(t.amount) AS SUM
                          FROM Trans t
                          GROUP BY t.projectId) b ON b.projectId = p.id
                 INNER JOIN category c ON c.id = p.categoryId
          WHERE p.id =".$_GET['id'];
    $result = pg_query($query) or die('Query failed: ' . pg_last_error());
    $project = pg_fetch_assoc($result);
    
		if ($result) {
			echo "Your transaction has gone through. Thank you!;
		}

	}
?>
