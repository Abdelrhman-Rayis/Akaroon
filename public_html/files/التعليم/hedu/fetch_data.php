<?php

//fetch_data.php

include('database_connection.php');

if(isset($_POST["action"]))
{
	$query = "
		SELECT * FROM edu WHERE status = '0'
	";
	if(isset($_POST["minimum_price"], $_POST["maximum_price"]) && !empty($_POST["minimum_price"]) && !empty($_POST["maximum_price"]))
	{
		$query .= "
		 AND product_price BETWEEN ? AND ?
		";
	}
	if(isset($_POST["brand"]))
	{
		$brand_filter = implode(",", array_map(function($v) use ($connect) { return $connect->quote(strip_tags(substr($v,0,100))); }, $_POST["brand"]));
		$query .= "
		 AND The_number_of_the_Author IN(".$brand_filter.")
		";
	}
	if(isset($_POST["search_btn"]))
	{
		$ram_filter = implode("','", $_POST["search_btn"]);
		$query .= "
		 AND CONCAT_WS('',Category, The_Title_of_Paper_Book, The_number_of_the_Author, Year_of_issue, Place_of_issue, Field_of_research, Key_words) LIKE '%".$search_var."%'

		";
	}
	if(isset($_POST["ram"]))
	{
		$ram_filter = implode(",", array_map(function($v) use ($connect) { return $connect->quote(strip_tags(substr($v,0,100))); }, $_POST["ram"]));
		$query .= "
		 AND Field_of_research IN(".$ram_filter.")
		";
	}
	if(isset($_POST["storage"]))
	{
		$storage_filter = implode(",", array_map(function($v) use ($connect) { return $connect->quote(strip_tags(substr($v,0,100))); }, $_POST["storage"]));
		$query .= "
		 AND Place_of_issue IN(".$storage_filter.")
		";
	}

	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$total_row = $statement->rowCount();
	$output = '';
	if($total_row > 0)
	{
		foreach($result as $row)
		{
			$output .= '
			<div class="col-sm-4 col-lg-3 col-md-3">
				<div style="border:1px solid #ccc; border-radius:5px; padding:16px; margin-bottom:16px; height:450px;">
					<img src="image/'. htmlspecialchars($row['image'], ENT_QUOTES, 'UTF-8') .'" alt="" class="img-responsive" >
					<p align="center"><strong><a href="../library/community/files/'. $row['id'] .'.pdf">'.$row['The_Title_of_Paper_Book'] .'</a></strong></p>
					<h4 style="text-align:center;" class="text-danger" >'. htmlspecialchars($row['Year_of_issue'], ENT_QUOTES, 'UTF-8') .'</h4>
					<p>Field_of_research : '. htmlspecialchars($row['Field_of_research'], ENT_QUOTES, 'UTF-8') .' <br />
					Category : '. htmlspecialchars($row['Category'], ENT_QUOTES, 'UTF-8') .' <br />
					Author : '. htmlspecialchars($row['The_number_of_the_Author'], ENT_QUOTES, 'UTF-8') .' <br />
					Year of issue : '. htmlspecialchars($row['Year_of_issue'], ENT_QUOTES, 'UTF-8') .'  </p>
				</div>

			</div>
			';
		}
	}
	else
	{
		$output = '<h3>No Data Found</h3>';
	}
	echo $output;
}

?>