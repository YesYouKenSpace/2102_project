<?php

	$dbconn = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=postgres")
    or die('Could not connect: ' . pg_last_error());

	$searchProjectName = $_REQUEST['search-project-title'];
	$searchProjectOwner = $_REQUEST['search-owner-name'];
	$searchCategoryId = $_REQUEST['search-category'];

	$searchAmountRaised = $_REQUEST['search-amount-raised'];
	$amountRaisedArray = explode(" ", $searchAmountRaised);
	$amountRaisedMin = $amountRaisedArray[0] * 1000;
	$amountRaisedMax = $amountRaisedArray[1] * 1000;

	$searchAmountGoal = $_REQUEST['search-amount-goal'];
	$amountGoalArray = explode(" ", $searchAmountGoal);
	$amountGoalMin = $amountGoalArray[0] * 1000;
	$amountGoalMax = $amountGoalArray[1] * 1000;

	$baseQuery = "SELECT p.id, p.title, p.startDate, p.endDate, c.id AS catId, 
						 m.firstName, m.lastName,
						 c.name, p.amountFundingSought, p.email, 
						 COALESCE(b.transactSum, 0) AS amountRaised
		FROM Project p INNER JOIN Category c ON p.categoryId = c.id 
					   INNER JOIN Member m ON p.email = m.email
					   LEFT OUTER JOIN (SELECT t.projectId, SUM(t.amount) AS transactSum 
										FROM Trans t
										GROUP BY t.projectId) b 
					   					ON p.id = b.projectId
		WHERE p.softDelete = FALSE";

	$query = "SELECT * FROM ({$baseQuery}) AS base
		WHERE title LIKE '%{$searchProjectName}%'
		AND (firstName LIKE '%{$searchProjectOwner}%' OR lastName LIKE '%{$searchProjectOwner}%') ";

	if (!empty($searchCategoryId)) {
		$query .= "AND catId = {$searchCategoryId} ";
	}

	if (!empty($amountRaisedMin) || !empty($amountRaisedMax)) {
		$query .= "AND amountRaised <= {$amountRaisedMax} AND amountRaised >= {$amountRaisedMin} ";
	}

	if (!empty($amountGoalMin) || !empty($amountGoalMax)) {
		$query .= "AND amountFundingSought <= {$amountGoalMax} AND amountFundingSought >= {$amountGoalMin} ";
	}

	$query .= "ORDER BY endDate DESC, startDate DESC";

	$result = pg_query($query) or die('Query failed: ' . pg_last_error());

	$error = false;
	$errorText = "";

	if (!empty($searchProjectName) && !preg_match("/^[a-zA-Z0-9 .,\- \/ _]+$/", $searchProjectName)) {
        $error = true;
    }

    if ($error) {
		echo "FAIL";
		return null;
	}

	ob_end_clean();
	ob_start();

	while($row=pg_fetch_assoc($result)) {
		if ((!is_null($row['amountraised'])) && ($row['amountraised'] >= $row['amountfundingsought'])) { 
			echo "<tr style=\"background-color:#c9ffc9;\">";
		} else {
			echo "<tr>";
		}

		echo "<td>".$row['title']
		."</td><td>".date('d/m/Y', strtotime(str_replace('-', '/', $row['startdate'])))
		."</td><td>".date('d/m/Y', strtotime(str_replace('-', '/', $row['enddate'])))
		."</td><td>".$row['name']
		."</td><td><div class=\"progress\" style=\"margin-bottom:2px;\"><div class=\"progress-bar progress-bar-success\" role=\"progressbar\" aria-valuenow=\"70\"
		aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width:"
		.(($row['amountraised'] * 100 / $row['amountfundingsought']))
		."%;\">
		</div></div>"; 
		
		if ($row['amountraised'] >= $row['amountfundingsought']) {
			echo " <strong style=\"color:#5cb85c;\">$".$row['amountraised']."</strong> / $".$row['amountfundingsought'];
		} else {
			echo "$".$row['amountraised']." / $".$row['amountfundingsought'];
		} 

        $proj_id = $row['id'];

        echo "</td><td><button class=\"btn btn-primary btn-xs\" onClick=\"location.href='project_details.php?id=$proj_id'\"><span class=\"glyphicon glyphicon-info-sign\"></span></button></td></tr>";

	}						
	
	pg_free_result($result);
?>