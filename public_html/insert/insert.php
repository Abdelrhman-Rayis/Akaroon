<?php
/* Attempt MySQL server connection. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
$link = mysqli_connect("localhost", "cl50-akaroondb", "abdo@1995", "cl50-akaroondb");
$link->set_charset("utf8");

 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
 
// Escape user inputs for security
$tabel = mysqli_real_escape_string($link, $_REQUEST['tabel']);
$id = mysqli_real_escape_string($link, $_REQUEST['id']);
$image = mysqli_real_escape_string($link, $_REQUEST['image']);
$Category = mysqli_real_escape_string($link, $_REQUEST['Category']);
$The_Title_of_Paper_Book = mysqli_real_escape_string($link, $_REQUEST['The_Title_of_Paper_Book']);
$The_number_of_the_Author = mysqli_real_escape_string($link, $_REQUEST['The_number_of_the_Author']);
$Year_of_issue = mysqli_real_escape_string($link, $_REQUEST['Year_of_issue']);
$Place_of_issue = mysqli_real_escape_string($link, $_REQUEST['Place_of_issue']);
$Field_of_research = mysqli_real_escape_string($link, $_REQUEST['Field_of_research']);
$Key_words = mysqli_real_escape_string($link, $_REQUEST['Key_words']);

// attempt insert query execution
$sql = "INSERT into $tabel(`id`, `image`, `Category`, `The_Title_of_Paper_Book`, `The_number_of_the_Author`, `Year_of_issue`, `Place_of_issue`, `Field_of_research`, `Key_words`)VALUES('$id','$image','$Category','$The_Title_of_Paper_Book','$The_number_of_the_Author','$Year_of_issue','$Place_of_issue','$Field_of_research','$Key_words')";
if(mysqli_query($link, $sql)){

  echo "New record created successfully. Last inserted ID is: " . $id;
    echo "Records added successfully.";
} else{
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}
 
// close connection
mysqli_close($link);
?>