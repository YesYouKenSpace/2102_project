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
			echo "Category has been set to inactive./~/";
		}
		
		// Get new table after deleting category
		$query = 'SELECT * FROM Category c LEFT OUTER JOIN (SELECT p.categoryid, donors, total, COUNT(p.categoryid) AS pcount
			      FROM Project p LEFT OUTER JOIN (SELECT p2.categoryid, COUNT(DISTINCT t.email) AS donors, SUM(t.amount) AS total
	 			  FROM Project p2 INNER JOIN Trans t ON p2.id = t.projectId
				  GROUP BY p2.categoryid) pTrans
   				  ON p.categoryid = pTrans.categoryId
				  GROUP BY p.categoryid, total, donors) fundedCategories
				  ON c.id = fundedCategories.categoryid
 				  ORDER BY c.name';
												
		$result = pg_query($query) or die('Query failed: ' . pg_last_error());

		while($category=pg_fetch_assoc($result)) {
            $categoryId = $category['id'];
			echo "<tr><td>".$category['name']."</td>";
			if ($category['pcount'] != 0) {
	  			echo "<td>".$category['pcount']."</td>";
	  		} else {
	  			echo "<td>0</td>";
	  		}
	  		if ($category['donors'] != 0) {
	  			echo "<td>".$category['donors']."</td>";
	  		} else {
	  			echo "<td>0</td>";
	  		}
	  		if ($category['total'] != 0) {
	  			echo "<td>$".$category['total']."</td>";
	  		} else {
	  			echo "<td>$0</td>";
	  		}
	  		if ($category['softdelete'] === 't') {
	  			echo "<td><span class='label label-danger'>Inactive</span></td>";
	  		} else {
	  			echo "<td><span class='label label-success'>Active</span></td>";
	  		}

	  		echo "<td>
	  				<button class=\"btn btn-primary btn-xs\" onClick=\"location.href='category_details.php?id=$categoryId'\">
	  			  		<span class=\"glyphicon glyphicon-info-sign\"></span>
  				  	</button>
  				  </td>";

			if ($category['softdelete'] === 't') {
	  			echo "<td></td></tr>";
	  		} else {
	  			echo "<td>
				  		<button class=\"btn btn-danger btn-xs delete_category\" category-id=\"$categoryId\" href=\"javascript:void(0)\">
				  			<span class=\"glyphicon glyphicon-trash\"></span>
			  			</button>
			  	  	</td></tr>";
			}

			echo "</tr>";
		}

		pg_free_result($result);
	}
?>