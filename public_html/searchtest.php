<meta http-equiv="Content-Type"
content="text/html; charset=utf-8" /> 
<h1>مرحبا يا صديقي</h1>
<?php 
if (isset($_POST['Search'])) {
	$valueToSearch =$_POST['valueToSearch'];
      $query =  "SELECT * FROM `iaoltest` WHERE CONCAT(`ID`, `number`, `Category`, `The Title of Paper/Book`, `The name of the Author`, `Year of issue`, `Place of issue`, `Field of research`, `Key words`) LIKE '%".$valueToSearch."%'";


	$Search_result = filterTable($query);
}
else {
	$query = "SELECT * FROM `iaoltest`";
	$Search_result = filterTable($query);

}
function filterTable ($query)
{
	$connect = mysqli_connect("localhost","root","","iaoltest");
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
 <form action="searchtest.php" method="post">
 	<table>
 		<input type="text" name="valueToSearch" placeholder="value To Search">
 		<input type="submit" name="Search" value="Filter"><br><br>
 		<tr>
 			<th>ID</th>
 			<th>Category</th>
 			<th>The Title of Paper/Book</th>
 			<th>The name of the Author</th>

 		</tr>
 		   <?php while($row = mysqli_fetch_array($Search_result)):?>
 		   <tr>
 		   	<td><?php echo $row['ID'];?></td>
 		   	<td><?php echo $row['Category'];?></td>
 		 	<td><?php echo $row['The Title of Paper/Book'];?></td>
        	<td><?php echo $row['The name of the Author'];?></td>
 		   </tr> 
 	    	<?php endwhile;?>
 	</table>
 </form>
 </body>
 </html>