<?php 
        $link = new mysqli('mysql','root','root','form-wizard');
          $link->set_charset("utf8");

        if($link->connect_error){
            die("Connection Failed".$link->connect_error);
        }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <title></title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">

<style type="text/css">
    ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
  background-color: #333;
}

li {
  float: left;
}

li a, .dropbtn {
  display: inline-block;
  color: white;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
}

li a:hover, .dropdown:hover .dropbtn {
 }

li.dropdown {
  display: inline-block;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f9f9f9;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
}

.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
  text-align: left;
}

.dropdown-content a:hover {background-color: #f1f1f1;}

.dropdown:hover .dropdown-content {
  display: block;

}
.flex {
    display: flex;
}

</style>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    </head>
    <body data-spy="scroll" data-target=".mainmenu-area">
    <!-- Preloader-content -->
  
    <!-- MainMenu-Area -->
   
   <!--  <li><a  href="#"><img src="images/logo.png" alt="Logo" style="width:30%;height: auto;  padding: 0px 0px;
"></a>  </li>-->

  <ul>
    <li>    <a  href="index.html" class="navbar-brand" style="position: relative;top: -10px;"><img src="images/logo.png" alt="Logo" style="width:20%;height: auto;  padding: 0px 0px;

"></a></li>
<div style="position: relative;right: 300px;">
  <li><a href="index.html">Home</a></li>
  <li><a href="#news">Categories</a></li>
  <li >  <a class="dropbtn">Bio</a> </li>
    <li><a href="#news">Blog</a></li>
    <li><a href="#news">Contacts</a></li>

</ul>
</div>
    <div >
        <br><br>
        <div class="col-md-5">
      <form action="" method="get">
    <div class="flex">
        <div class="form-group">
            <input type="text" name="search" class="form-control" placeholder="Search Here..."/>

        </div>
        <div class="form-group" >
            <input type="submit" name="search_btn" class="btn btn-default" value="Search"  />
        </div>
      </form>
</div>
      <?php
        if(isset($_GET['search_btn'])){

            $search_var = $_GET['search'];


             $sql = "SELECT * FROM `meta2` WHERE CONCAT_WS('',Category, The_Title_of_Paper_Book, The_number_of_the_Author, Year_of_issue, Place_of_issue, Field_of_research, Key_words) LIKE '%".$search_var."%'";



            if($res = $link->query($sql)){

        ?>
            <table class="table table-striped" dir="rtl" ps>
                <thead>
                  <tr>
                    <th>Category</th>
                    <th>The_Title_of_Paper_Book</th>
                     <th>The_number_of_the_Author</th>
                    <th>Year_of_issue</th>
                         <th>Field_of_research</th>
                        <th>Cover</th>

 

                  </tr>
                </thead>
                <tbody>
        <?php
            if($res->num_rows > 0){

                while($row = $res->fetch_assoc()){
        ?>
                    <tr>
                        
                        
                    <td><?php echo $row['Category'] ?></td>
                    <td><?php echo $row['The_Title_of_Paper_Book'] ?></td>
                    <td><?php echo $row['The_number_of_the_Author'] ?></td>
                    <td><?php echo $row['Year_of_issue'] ?></td>
                    <td><?php echo $row['Field_of_research'] ?></td>
                    <td><?php echo '<a href="library/education/files/'. $row['number'] .'.pdf"><img src="image/'. $row['number'] .'.jpg" style="width:200%;height: auto;  padding: 0px 0px;

"></a>' ?></td>                    
                    </tr>
        <?php   
                }   
            }
            else
            {
        ?>
                <tr>
                    <td colspan="2">Not Found<?php echo $link->error;?></td>
                </tr>   
        <?php       
            }
        ?>
                </tbody>
            </table>
        <?php
            }
            else
            {
                echo "Failed".$sql;
            }
        }
      ?>
      </div>
    </div>

    </body>
    </html>