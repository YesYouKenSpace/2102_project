<html>
<head> <title>Demo Online Book Catalog</title> </head>

<body>
<table>
<tr> <td colspan="2" style="background-color:#FFA500;">
<h1> Demo Book Catalog</h1>
</td> </tr>

<?php
$dbconn = pg_connect("host=localhost port=5432 dbname=postgres   user=postgres password=postgres")
    or die('Could not connect: ' . pg_last_error());
?>

<tr>
<td style="background-color:#eeeeee;">
<form>
        Title: <input type="text" name="Title" id="Title">

        <select name="Language"> <option value="">Select Language</option>
        <?php
        $query = 'SELECT DISTINCT language FROM book';
        $result = pg_query($query) or die('Query failed: ' . pg_last_error());
         
        while($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
           foreach ($line as $col_value) {
              echo "<option value=\"".$col_value."\">".$col_value."</option><br>";
            }
        }
        pg_free_result($result);
        ?>
        </select>

        <input type="radio" name="Format" id="Format1" value="hardcover">hardcover
        <input type="radio" name="Format" id="Format2" value="paperback">paperback

        <input type="submit" name="formSubmit" value="Search" >
</form>
<?php

if(isset($_GET['formSubmit'])) 
{
    $query = "SELECT Title, Authors FROM Book WHERE Title LIKE '%".$_GET['Title']."%' AND Language LIKE '%".$_GET['Language']."%' AND Format LIKE '%".$_GET['Format']."%'";
    echo "<b>SQL:   </b>".$query."<br><br>";
    $result = pg_query($query) or die('Query failed: ' . pg_last_error());
    echo "<table border=\"1\" >
    <col width=\"75%\">
    <col width=\"25%\">
    <tr>
    <th>Title</th>
    <th>Authors</th>
    </tr>";


    while ($row = pg_fetch_row($result)){
      echo "<tr>";
      echo "<td>" . $row[0] . "</td>";
      echo "<td>" . $row[1] . "</td>";
      echo "</tr>";
    }
    echo "</table>";
    
    pg_free_result($result);
}
?>

</td> </tr>
<?php
pg_close($dbconn);
?>
<tr>
<td colspan="2" style="background-color:#FFA500; text-align:center;"> Copyright &#169; CS2102
</td> </tr>
</table>

</body>
</html>
