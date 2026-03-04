<?php

//fetch_data.php

include('database_connection.php');

if(isset($_POST["action"]))
{
	$query = "
		SELECT * FROM tas WHERE 	status = '0'
	";
	if(isset($_POST["minimum_price"], $_POST["maximum_price"]) && !empty($_POST["minimum_price"]) && !empty($_POST["maximum_price"]))
	{
		$query .= "
		 AND product_price BETWEEN '".$_POST["minimum_price"]."' AND '".$_POST["maximum_price"]."'
		";
	}
	if(isset($_POST["brand"]))
	{
		$brand_filter = implode("','", $_POST["brand"]);
		$query .= "
		 AND The_number_of_the_Author IN('".$brand_filter."')
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
		$ram_filter = implode("','", $_POST["ram"]);
		$query .= "
		 AND Field_of_research IN('".$ram_filter."')
		";
	}
	if(isset($_POST["storage"]))
	{
		$storage_filter = implode("','", $_POST["storage"]);
		$query .= "
		 AND Place_of_issue IN('".$storage_filter."')
		";
	}

	$tasment = $connect->prepare($query);
	$tasment->execute();
	$result = $tasment->fetchAll();
	$total_row = $tasment->rowCount();
	$output = '';
	if($total_row > 0)
	{
		foreach($result as $row)
		{
			$output .= '
			<div class="col-sm-4 col-lg-3 col-md-3">
				<div style="border:1px solid #ccc; border-radius:5px; padding:16px; margin-bottom:16px; height:450px;">
					<img src="image/'. $row['image'] .'" alt="" class="img-responsive" >
					<p align="center"><strong><a href="files/'. $row['id'] .'.pdf">'.$row['The_Title_of_Paper_Book'] .'</a></strong></p>
					<h4 style="text-align:center;" class="text-danger" >'. $row['Year_of_issue'] .'</h4>
					<p>Field_of_research : '. $row['Field_of_research'].' <br />
					Category : '. $row['Category'] .' <br />
					Author : '. $row['The_number_of_the_Author'] .' <br />
					Year of issue : '. $row['Year_of_issue'] .'  </p>
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