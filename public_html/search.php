<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?php 
if (isset($_POST['Search'])) {

	$valueToSearch =$_POST['valueToSearch'];
      $query =  "SELECT * FROM `test_db` WHERE CONCAT(`id`, `FirstName`, `LastName`, `Age`) LIKE '%".$valueToSearch."%'";


	$Search_result = filterTable($query);
}
else {
	$query = "SELECT * FROM `test_db`";
	$Search_result = filterTable($query);

}
function filterTable ($query)
{
	$connect = mysqli_connect("mysql","root","root","test_db");
	 mysqli_set_charset($connect, 'utf8');

	$filter_Result =mysqli_query($connect, $query);

	return $filter_Result;
	
}
 ?>
  <html>
 <head>

 	<title></title>
 	<style>
 		table,tr,th,td
 		{
 			border: 1px solid black;
 		}
 	</style>
 </head>
 <body>
 <form action="search.php" method="post">
 	<table>
 		<input type="text" name="valueToSearch" placeholder="value To Search">
 		<input type="submit" name="Search" value="Filter"><br><br>
 		<tr>
 			<th>id</th>
 			<th>First Name</th>
 			<th>Last Name</th>
 			<th>Age</th>

 		</tr>
 		   <?php while($row = mysqli_fetch_array($Search_result)):?>
 		   <tr>
 		   	<td><?php echo $row['id'];?></td>
 		   	<td><?php echo $row['FirstName'];?></td>
 		 	<td><?php echo $row['LastName'];?></td>
        	<td><?php echo $row['Age'];?></td>
 		   </tr> 
 	    	<?php endwhile;?>
 	</table>
 </form>
 </body>
 </html>