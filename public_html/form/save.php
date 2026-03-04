

<?php


$db = new PDO("mysql:host=mysql;dbname=form-wizard",
    "root","root",
      array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")

);
if(isset($_POST['save'])){
    $id = uniqid();
    $number = $_POST['number'];
    $Category = $_POST['Category'];
    $The_Title_of_Paper_Book = $_POST['The_Title_of_Paper_Book'];
    $The_number_of_the_Author = $_POST['The_number_of_the_Author'];
    $Year_of_issue = $_POST['Year_of_issue'];
    $Place_of_issue = md5($_POST['Place_of_issue']);
    $Field_of_research = $_POST['Field_of_research'];
    $Key_words = $_POST['Key_words'];

    $stat1 = $db->prepare("insert into meta2 values(?,?,?,?,?)");
    $stat1->bindParam(1, $id);
    $stat1->bindParam(2, $number);
    $stat1->bindParam(3, $Category);
    $stat1->bindParam(4, $The_Title_of_Paper_Book);
    $stat1->bindParam(5, $The_number_of_the_Author);
    $stat1->execute();
    $stat2 = $db->prepare("insert into account values(?,?,?)");
    $stat2->bindParam(1, $id);
    $stat2->bindParam(2, $username);
    $stat2->bindParam(3, $password);
    $stat2->execute();
    $stat3 = $db->prepare("insert into website values(?,?,?,?,?)");
    $stat3->bindParam(1, $id);
    $stat3->bindParam(2, $title);
    $stat3->bindParam(3, $description);
    $stat3->bindParam(4, $sites);
    $stat3->bindParam(5, $category);
    $stat3->execute();
    header('Location: save.php');
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Form Wizard with jQuery and PHP</title>
    <link href="css/bootstrap.min.css" rel="stylesheet"/>
    <link href="css/font-awesome.min.css" rel="stylesheet"/>
    <link href="style.css" rel="stylesheet"/>
</head>
<body>
    
    <div class="container-fluid">
        <p><br/></p>
        <h3>MetaData Manager</h3>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Username</th>
                    <th>Title</th>
                    <th>Description</th>
                   
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $db->prepare("select `id`, `number`, `Category`, `The_Title_of_Paper_Book`, `The_number_of_the_Author`, `Year_of_issue`, `Place_of_issue`, `Field_of_research`, `Key_words` from `meta2`");
                $stmt->execute();
                while($row = $stmt->fetch()){
                ?>
                <tr>
                    <td><?php echo $row['id'] ?></td>
                    <td><?php echo $row['number'] ?></td>
                    <td><?php echo $row['Category'] ?></td>
                    <td><?php echo $row['The_Title_of_Paper_Book'] ?></td>
                    <td><?php echo $row['The_number_of_the_Author'] ?></td>
                    <td><?php echo $row['Year_of_issue'] ?></td>
                    <td><?php echo $row['Field_of_research'] ?></td>
                    <td><?php echo $row['Key_words'] ?></td>
        
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
        <p class="text-center">
            <br/>
            <a href="index.html" class="btn btn-primary">Back to homepage</a>
        </p>
    </div>
    
    <script src="js/jquery.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="script.js"></script>
</body>
</html>