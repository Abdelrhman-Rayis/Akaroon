<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Akaroon Form</title>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Add icon library -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
body {font-family: Arial, Helvetica, sans-serif;}
* {box-sizing: border-box;}

.input-container {
  display: -ms-flexbox; /* IE10 */
  display: flex;
  width: 100%;
  margin-bottom: 15px;
}

.icon {
  padding: 10px;
  background: dodgerblue;
  color: white;
  min-width: 50px;
  text-align: center;
}

.input-field {
  width: 100%;
  padding: 10px;
  outline: none;
}

.input-field:focus {
  border: 2px solid dodgerblue;
}

/* Set a style for the submit button */
.btn {
  background-color: dodgerblue;
  color: white;
  padding: 15px 20px;
  border: none;
  cursor: pointer;
  width: 100%;
  opacity: 0.9;
}

.btn:hover {
  opacity: 1;
}
</style>
</head>
</head>
<body>
<?php
/* Attempt MySQL server connection. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
$link = mysqli_connect("localhost", "cl50-akaroondb", "abdo@1995", "cl50-akaroondb");
$link->set_charset("utf8");

 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
 

 //echo "New record created successfully. Last inserted ID for edu is: " $edu.id;
   // echo "Records added successfully.";




 
 
// close connection
mysqli_close($link);
?>

  <h3 style="text-align: center;margin-bottom: -90px;">Welcome to Akaroon Metadata online input form</h3>
<form action="insert.php" method="post" style="max-width:500px;margin:auto">
	<br><br><br><br><br><br>
  <select name="tabel" style="font-size: 18px;background-color: red;margin-bottom: 5px; font-style: italic;">
<option value="">Select Table</option>
<option value="edu">edu</option>
<option value="org">org</option>
<option value="philo">philo</option>
<option value="pol">pol</option>
<option value="soc">soc</option>
<option value="state">state</option>
<option value="tas">tas</option>
</select>
	  <div class="input-container">

    <i class="fa fa-user icon"></i>
    <input class="input-field" type="text" placeholder="id" name="id">
  </div>
  <div class="input-container">
    <i class="fa fa-user icon"></i>
    <input class="input-field" type="text" placeholder="image" name="image">
  </div>
  <select name="Category"style="font-size: 18px;background-color: red;margin-bottom: 5px; font-style: italic;">
<option value="">Select Catagory</option>
<option value="التعليم">التعليم</option>
<option value="منظمات">منظمات</option>
<option value="التأصيل">التأصيل</option>
<option value="المجتمع">المجتمع</option>
<option value="الدولة">الدولة</option>
<option value="الفلسفة">الفلسفة</option>
<option value="السياسة">السياسة</option>

</select>
 
  <div class="input-container">
    <i class="fa fa-user icon"></i>
    <input class="input-field" type="text" placeholder="The_Title_of_Paper_Book" name="The_Title_of_Paper_Book">
  </div>
  <div class="input-container">
    <i class="fa fa-user icon"></i>
    <input class="input-field" type="text" placeholder="Place_of_issue" name="Place_of_issue">
  </div>
  <div class="input-container">
    <i class="fa fa-user icon"></i>
    <input class="input-field" type="text" placeholder="The_name_of_the_Author" name="The_number_of_the_Author">
  </div>
 <div class="input-container">
    <i class="fa fa-user icon"></i>
    <input class="input-field" type="text" placeholder="Year_of_issue" name="Year_of_issue">
  </div>

  
    

  </div>
  <div class="input-container">
    <i class="fa fa-user icon"></i>
    <input class="input-field" type="text" placeholder="Field_of_research" name="Field_of_research">
  </div>

  <div class="input-container">
    <i class="fa fa-user icon"></i>
    <input class="input-field" type="text" placeholder="Key_words" name="Key_words">
  </div>
   
    
   
    <input type="submit" value="Add Akaroon Metadata"  class="btn">
</form>

</body>
</html>