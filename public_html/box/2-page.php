<?php
/* [SEARCH FOR USERS] */
if (isset($_POST['search'])) {
  require "3-search.php";
}

/* [DISPLAY HTML] */ ?>
<!DOCTYPE html>
<html>
  <body>
    <!-- [SEARCH FORM] -->
    <form method="post">
      <h1>
        SEARCH FOR USERS
      </h1>
      <input type="text" name="search" required/>
      <input type="submit" value="Search"/>
    </form>
    

    <!-- [SEARCH RESULTS] -->
    <?php
    if (isset($_POST['search'])) {
      if (count($results) > 0) {
        foreach ($results as $r) {
               echo "<tr><th>".$row['The Title of Paper/Book']."</th>"."<th>".$row['The name of the Author']."</th>"."</tr>";

        }
      } else {
        echo "No results found";
      }
    }
    ?>
  </body>
</html>