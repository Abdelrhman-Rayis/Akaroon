
<h1>مرحبا يا صديlijijي</h1>
<?php 
if (isset($_POST['Search'])) {
	$valueToSearch =$_POST['valueToSearch'];
      $query =  "SELECT * FROM `about` WHERE CONCAT(`id`, `name`, `email`, `phone`, `address`) LIKE '%".$valueToSearch."%'";


	$Search_result = filterTable($query);
}
else {
	$query = "SELECT * FROM `about`";
	$Search_result = filterTable($query);

}
function filterTable ($query)
{
	$connect = mysqli_connect("localhost","root","","form-wizard");
	mysql_query("SET NAMES 'utf8';");
 	$filter_Result =mysqli_query($connect, $query);
	return $filter_Result;
	
}
 ?>
  <html>
 <head>
     <meta charset="utf-8"/>

 	<title></title>
 	<style>
 		table,tr,th,td
 		{
 			border: 1px solid black;
 		}
 	</style>
 </head>
 <body>
 <form action="searctest3.php" method="post">
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
 		   	<td><?php echo $row['id'];?></td>
 		   	<td><?php echo $row['name'];?></td>
 		 	<td><?php echo $row['email'];?></td>
        	<td><?php echo $row['address'];?></td>
 		   </tr> 
 	    	<?php endwhile;?>
 	</table>
 </form>
 </body>
 </html>