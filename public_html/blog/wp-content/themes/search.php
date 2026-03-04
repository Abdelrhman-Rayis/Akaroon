<?php 
        $link = new mysqli('mysql','root','root','akaroon_akaroondb');
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
html, body{
    height:100%;
    width:100%;
    padding:0;
    margin:0;
}
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
table {
  border-collapse: collapse;
  border-spacing: 0;
  width: 100%;
font-size:18px;
 
}

th, td {
  text-align: left;
  padding: 16px;
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
  <!--  <li>    <a  href="index.html" class="navbar-brand" style="position: relative;top: -10px;"><img src="images/logo.png" alt="Logo" style="width:60%;height: auto;  padding: 0px 0px;-->

"></a></li>
   <li><a href="index.html"> < Back to Home page</a></li>
  

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


             

$sql ="
(SELECT *  FROM `cl50-akaroondb`.`edu` WHERE (CONVERT(`id` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`image` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Category` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`The_Title_of_Paper_Book` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`The_number_of_the_Author` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Year_of_issue` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Place_of_issue` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Field_of_research` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`status` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Key_words` USING utf8) LIKE '%".$search_var."%'))
UNION
(SELECT *  FROM `cl50-akaroondb`.`soc` WHERE (CONVERT(`id` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`image` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Category` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`The_Title_of_Paper_Book` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`The_number_of_the_Author` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Year_of_issue` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Place_of_issue` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Field_of_research` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`status` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Key_words` USING utf8) LIKE '%".$search_var."%'))
UNION
(SELECT *  FROM `cl50-akaroondb`.`tas` WHERE (CONVERT(`id` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`image` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Category` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`The_Title_of_Paper_Book` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`The_number_of_the_Author` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Year_of_issue` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Place_of_issue` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Field_of_research` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`status` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Key_words` USING utf8) LIKE '%".$search_var."%'))
UNION
(SELECT *  FROM `cl50-akaroondb`.`pol` WHERE (CONVERT(`id` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`image` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Category` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`The_Title_of_Paper_Book` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`The_number_of_the_Author` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Year_of_issue` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Place_of_issue` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Field_of_research` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`status` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Key_words` USING utf8) LIKE '%".$search_var."%'))
UNION
(SELECT *  FROM `cl50-akaroondb`.`org` WHERE (CONVERT(`id` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`image` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Category` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`The_Title_of_Paper_Book` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`The_number_of_the_Author` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Year_of_issue` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Place_of_issue` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Field_of_research` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`status` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Key_words` USING utf8) LIKE '%".$search_var."%'))

UNION
(SELECT *  FROM `cl50-akaroondb`.`state` WHERE (CONVERT(`id` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`image` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Category` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`The_Title_of_Paper_Book` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`The_number_of_the_Author` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Year_of_issue` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Place_of_issue` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Field_of_research` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`status` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Key_words` USING utf8) LIKE '%".$search_var."%'))
UNION
(SELECT *  FROM `cl50-akaroondb`.`philo` WHERE (CONVERT(`id` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`image` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Category` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`The_Title_of_Paper_Book` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`The_number_of_the_Author` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Year_of_issue` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Place_of_issue` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Field_of_research` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`status` USING utf8) LIKE '%".$search_var."%' OR CONVERT(`Key_words` USING utf8) LIKE '%".$search_var."%'))


";

            if($res = $link->query($sql)){

        ?>
            <table  class="table table-striped" style="border-top-color:black; border-width: 10px;">
                <thead>
                  <tr>
                    <th>التصنيف</th>
                     <th>الرقم التسلسلي</th>

                    <th>عنوان الورقة/الكتاب</th>
                    <th>إسم الكاتب</th>
                    <th>سنة الإصدار</th>
                    <th>مجال البحث</th>
                    <th>زيارة المحتوى</th>
                   <!-- <th>Cover</th>-->

 

                  </tr>
                </thead>
                <tbody>
        <?php
            if($res->num_rows > 0){

                while($row = $res->fetch_assoc()){
        ?>
                    <tr>
                        
                        
                    <td style="white-space: nowrap;"><?php echo $row['Category'] ?></td>
                                                            <td ><?php echo $row['id'] ?></td>

                    <td ><?php echo $row['The_Title_of_Paper_Book'] ?></td>

                    <td><?php echo $row['The_number_of_the_Author'] ?></td>
                    <td><?php echo $row['Year_of_issue'] ?></td>
                    <td><?php echo $row['Field_of_research'] ?></td>
  <td><?php echo '<a href="files/'.$row['Category'].'/files/'. $row['id'] .'.pdf"><img src="files/'.$row['Category'].'/image/'. $row['id'] .'.jpg" border=3 height=150 width=100></a>' ?></td>           
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