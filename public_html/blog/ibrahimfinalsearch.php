<?php 
$link = new mysqli('mysql','root','root','akaroon_akaroondb');
$link->set_charset("utf8");

if($link->connect_error){
    die("Connection Failed: " . $link->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Search</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        
        .search-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .table-responsive {
            margin-top: 20px;
        }
        th, td {
            text-align: left;
        }
        img {
            max-width: 100%;
            height: auto;
        }
        /* New styles for row highlighting */
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f2f2f2; /* Light grey for odd rows */
        }
        .table-striped tbody tr:nth-of-type(even) {
            background-color: #ffffff; /* White for even rows */
        }
        .table-striped tbody tr:hover {
            background-color: #d1e7dd; /* Light green when hovered */
        }

        @media (max-width: 768px) {
            .search-container {
                margin: 20px;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="https://akaroon.com/blog/">Akaroon || By Ibrahim Ahmed Omer</a> <!-- Change this to your site name -->
            </div>
            <div class="navbar-right">
                <a href="https://akaroon.com/blog/" class="btn btn-default navbar-btn">Back to Main Page</a> <!-- Link to your home page -->
            </div>
        </div>
    </nav>

    <div class="search-container">
        <form action="" method="get">
            <div class="form-group">
                <input type="text" name="search" class="form-control" 
                    placeholder="هنا يمكنك البحث عن طريق عنوان الورقة أو الكتاب أو المؤلف أو مجال البحث" 
                    required autocomplete="off" 
                    style="text-align: center; direction: rtl;" />
            </div>
            <div class="form-group">
                <input type="submit" name="search_btn" class="btn btn-primary btn-block" value="بحث" />
            </div>
        </form>
    </div>

    <div class="container">
        <?php
        if (isset($_GET['search_btn'])) {
            $search_var = $_GET['search'];
            $find = arquery($search_var); // Using your function to prepare the regex

            // Updated SQL Query to search across multiple fields
            $sql = "
                (SELECT * FROM `edu` WHERE 
                    `id` REGEXP '$find' OR 
                    `image` REGEXP '$find' OR 
                    `Category` REGEXP '$find' OR 
                    `The_Title_of_Paper_Book` REGEXP '$find' OR 
                    `The_number_of_the_Author` REGEXP '$find' OR 
                    `Year_of_issue` REGEXP '$find' OR 
                    `Place_of_issue` REGEXP '$find' OR 
                    `Field_of_research` REGEXP '$find' OR 
                    `Key_words` REGEXP '$find')
                UNION
                (SELECT * FROM `soc` WHERE 
                    `id` REGEXP '$find' OR 
                    `image` REGEXP '$find' OR 
                    `Category` REGEXP '$find' OR 
                    `The_Title_of_Paper_Book` REGEXP '$find' OR 
                    `The_number_of_the_Author` REGEXP '$find' OR 
                    `Year_of_issue` REGEXP '$find' OR 
                    `Place_of_issue` REGEXP '$find' OR 
                    `Field_of_research` REGEXP '$find' OR 
                    `Key_words` REGEXP '$find')
                UNION
                (SELECT * FROM `tas` WHERE 
                    `id` REGEXP '$find' OR 
                    `image` REGEXP '$find' OR 
                    `Category` REGEXP '$find' OR 
                    `The_Title_of_Paper_Book` REGEXP '$find' OR 
                    `The_number_of_the_Author` REGEXP '$find' OR 
                    `Year_of_issue` REGEXP '$find' OR 
                    `Place_of_issue` REGEXP '$find' OR 
                    `Field_of_research` REGEXP '$find' OR 
                    `Key_words` REGEXP '$find')
                UNION
                (SELECT * FROM `pol` WHERE 
                    `id` REGEXP '$find' OR 
                    `image` REGEXP '$find' OR 
                    `Category` REGEXP '$find' OR 
                    `The_Title_of_Paper_Book` REGEXP '$find' OR 
                    `The_number_of_the_Author` REGEXP '$find' OR 
                    `Year_of_issue` REGEXP '$find' OR 
                    `Place_of_issue` REGEXP '$find' OR 
                    `Field_of_research` REGEXP '$find' OR 
                    `Key_words` REGEXP '$find')
                UNION
                (SELECT * FROM `org` WHERE 
                    `id` REGEXP '$find' OR 
                    `image` REGEXP '$find' OR 
                    `Category` REGEXP '$find' OR 
                    `The_Title_of_Paper_Book` REGEXP '$find' OR 
                    `The_number_of_the_Author` REGEXP '$find' OR 
                    `Year_of_issue` REGEXP '$find' OR 
                    `Place_of_issue` REGEXP '$find' OR 
                    `Field_of_research` REGEXP '$find' OR 
                    `Key_words` REGEXP '$find')
                UNION
                (SELECT * FROM `state` WHERE 
                    `id` REGEXP '$find' OR 
                    `image` REGEXP '$find' OR 
                    `Category` REGEXP '$find' OR 
                    `The_Title_of_Paper_Book` REGEXP '$find' OR 
                    `The_number_of_the_Author` REGEXP '$find' OR 
                    `Year_of_issue` REGEXP '$find' OR 
                    `Place_of_issue` REGEXP '$find' OR 
                    `Field_of_research` REGEXP '$find' OR 
                    `Key_words` REGEXP '$find')
                UNION
                (SELECT * FROM `philo` WHERE 
                    `id` REGEXP '$find' OR 
                    `image` REGEXP '$find' OR 
                    `Category` REGEXP '$find' OR 
                    `The_Title_of_Paper_Book` REGEXP '$find' OR 
                    `The_number_of_the_Author` REGEXP '$find' OR 
                    `Year_of_issue` REGEXP '$find' OR 
                    `Place_of_issue` REGEXP '$find' OR 
                    `Field_of_research` REGEXP '$find' OR 
                    `Key_words` REGEXP '$find')
            ";

            if ($res = $link->query($sql)) {
                echo '<div class="table-responsive">';
                echo '<table class="table table-striped">';
                echo '<thead><tr>';
                echo '<th>زيارة المحتوى</th>';
                echo '<th>عنوان الورقة/الكتاب</th>';
                echo '<th>إسم الكاتب</th>';
                echo '<th>سنة الإصدار</th>';
                echo '<th>التصنيف</th>';
                echo '<th>مجال البحث</th>';
                echo '<th>الرقم التسلسلي</th>';
                
                
                
                
                echo '</tr></thead><tbody>';

                if ($res->num_rows > 0) {
                    while ($row = $res->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td><a href="../files/' . $row['Category'] . '/files/' . $row['id'] . '.pdf"><img src="../files/' . $row['Category'] . '/image/' . $row['id'] . '.jpg" height="150" width="100" class="img-responsive"></a></td>';
                        echo '<td><a href="../files/' . $row['Category'] . '/files/' . $row['id'] . '.pdf">' . $row['The_Title_of_Paper_Book'] . '</a></td>'; // Title as link
                        echo '<td><a href="../files/' . $row['Category'] . '/files/' . $row['id'] . '.pdf">' . $row['The_number_of_the_Author'] . '</a></td>'; // Author name as link
                        echo '<td>' . $row['Year_of_issue'] . '</td>';
                        echo '<td>' . $row['Field_of_research'] . '</td>';
                        echo '<td>' . $row['Category'] . '</td>';
                        echo '<td>' . $row['id'] . '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="7">لا يوجد نتائج</td></tr>'; // Updated message to Arabic
                }
                echo '</tbody></table>';
                echo '</div>';
            } else {
                echo "Failed: " . $link->error;
            }
        }

        function arquery($text) {
            $replace = array("أ", "ا", "إ", "آ", "ي", "ى", "ه", "ة");
            $with = array("(أ|ا|آ|إ)", "(أ|ا|آ|إ)", "(أ|ا|آ|إ)", "(أ|ا|آ|إ)", "(ي|ى)", "(ي|ى)", "(ه|ة)", "(ه|ة)");
            return str_replace($replace, $with, $text);
        }
        ?>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
