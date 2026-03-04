<?php
include('config.php');
$s1=$_REQUEST["n"];
$select_query="select * from meta4 where The_Title_of_Paper_Book like '%".$s1."%'";
$sql=mysqli_query($db_conn, $select_query) or die (mysqli_error($db_conn));
$s="";
while($row=mysqli_fetch_array($sql))

{
	$s=$s."
	<a dir='rtl' class='link-p-colr' href='view.php?product=".$row['id']."'>
		<div class='live-outer'>
            	<div class='live-im' >
                	<img src='../image/".$row['id'].".jpg'/>
                </div>
                <div class='live-product-det'>
                	<div class='live-product-name'>
                    	<p>".$row['The_Title_of_Paper_Book']."</p>
                    </div>
                     
                </div>
            </div>
	</a>
	"	;
}
echo $s;
?>