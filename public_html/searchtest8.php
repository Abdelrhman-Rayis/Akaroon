<?php
$db = new PDO("mysql:host=localhost;dbname=iaoltest","root","");
                $stmt = $db->prepare("SELECT * FROM `iaoltest`");

if(isset($_POST['Search'])){
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
     mysql_set_charset('utf8');

    $filter_Result =mysqli_query($connect, $query);

    return $filter_Result;
    
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Form Wizard with jQuery and PHP</title>
    
</head>
<body>
    
    <div>
          <p>
            <br/>
         </p>
        <p><br/></p>
        <h3>My Campaigns</h3>
        <table class="table table-bordered table-striped">
            <thead>
               <form action="searchtest8.php" method="post">
    <table>
        <input type="text" name="valueToSearch" placeholder="value To Search">
        <input type="submit" name="Search" value="Filter"><br><br>
        <tr>
            <th>id</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Age</th>

        </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $db->prepare("SELECT * FROM `iaoltest`");
                $stmt->execute();
               while($row = mysqli_fetch_array($Search_result)){
                ?>
                <tr>
                    <td><?php echo $row['ID'] ?></td>
                    <td><?php echo $row['number'] ?></td>
                    <td><?php echo $row['Category'] ?></td>
                    <td><?php echo $row['The Title of Paper/Book'] ?></td>
                    
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
      
    </div>
    
  
</body>
</html>