<?php 

//index.php

include('database_connection.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <style type="text/css">
        .flex {
    display: flex;
}
    </style>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>عكارون</title>

    <script src="js/jquery-1.10.2.min.js"></script>
    <script src="js/jquery-ui.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href = "css/jquery-ui.css" rel = "stylesheet">
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <!-- Page Content -->
    <div class="container">
        <div class="row">
            <br />
            <h2 align="center">الفلسفة</h2>
            <br />
            <div class="col-md-3">                              
                <div class="list-group">

                    <h3>Search</h3>

                     <form action="" method="get">
    <div class="flex">
            <input type="text" name="search" class="form-control" placeholder="Search Here..."/>

       
            <input type="submit" name="search_btn" class="btn btn-default" value="Search"  />
        </div>
      </form>
                </div>              
                <div class="list-group">
                    <h3>Author</h3>
                    <div style="height: 180px; overflow-y: auto; overflow-x: hidden;">
                    <?php

                    $query = "SELECT DISTINCT(The_number_of_the_Author) FROM philo ORDER BY The_number_of_the_Author";
                    $philoment = $connect->prepare($query);
                    $philoment->execute();
                    $result = $philoment->fetchAll();
                    foreach($result as $row)
                    {
                    ?>
                    <div class="list-group-item checkbox">
                        <label><input type="checkbox" class="common_selector brand" value="<?php echo $row['The_number_of_the_Author']; ?>"  > <?php echo $row['The_number_of_the_Author']; ?></label>
                    </div>
                    <?php
                    }

                    ?>
                    </div>
                </div>

                <div class="list-group">
                    <h3>Field of research</h3>
                    <div style="height: 180px; overflow-y: auto; overflow-x: hidden;">

                    <?php

                    $query = "
                    SELECT DISTINCT(Field_of_research) FROM philo  ORDER BY Field_of_research
                    ";
                    $philoment = $connect->prepare($query);
                    $philoment->execute();
                    $result = $philoment->fetchAll();
                    foreach($result as $row)
                    {
                    ?>
                    <div class="list-group-item checkbox">
                        <label><input type="checkbox" class="common_selector ram" value="<?php echo $row['Field_of_research']; ?>" > <?php echo $row['Field_of_research']; ?></label>
                    </div>
                    <?php    
                    }

                    ?>
                </div>
                </div>

                
                <div class="list-group">
                    <h3>Place of issue</h3>
                    <div style="height: 180px; overflow-y: auto; overflow-x: hidden;">

                    <?php
                    $query = "
                    SELECT DISTINCT(Place_of_issue) FROM philo ORDER BY Place_of_issue
                    ";
                    $philoment = $connect->prepare($query);
                    $philoment->execute();
                    $result = $philoment->fetchAll();
                    foreach($result as $row)
                    {
                    ?>
                    <div class="list-group-item checkbox">
                        <label><input type="checkbox" class="common_selector storage" value="<?php echo $row['Place_of_issue']; ?>"  > <?php echo $row['Place_of_issue']; ?> </label>
                    </div>
                    <?php
                    }
                    ?>
                                        </div>
    
                </div>
            </div>

            <div class="col-md-9">
                <br />
                <div class="row filter_data">

                </div>
            </div>
        </div>

    </div>
<style>
#loading
{
    text-align:center; 
    background: url('loader.gif') no-repeat center; 
    height: 150px;
}
</style>

<script>
$(document).ready(function(){

    filter_data();

    function filter_data()
    {
        $('.filter_data').html('<div id="loading" style="" ></div>');
        var action = 'fetch_data';
        var minimum_price = $('#hidden_minimum_price').val();
        var maximum_price = $('#hidden_maximum_price').val();
        var brand = get_filter('brand');
        var ram = get_filter('ram');
        var storage = get_filter('storage');
        $.ajax({
            url:"fetch_data.php",
            method:"POST",
            data:{action:action, minimum_price:minimum_price, maximum_price:maximum_price, brand:brand, ram:ram, storage:storage},
            success:function(data){
                $('.filter_data').html(data);
            }
        });
    }

    function get_filter(class_name)
    {
        var filter = [];
        $('.'+class_name+':checked').each(function(){
            filter.push($(this).val());
        });
        return filter;
    }

    $('.common_selector').click(function(){
        filter_data();
    });

    $('#price_range').slider({
        range:true,
        min:1000,
        max:65000,
        values:[1000, 65000],
        step:500,
        stop:function(event, ui)
        {
            $('#price_show').html(ui.values[0] + ' - ' + ui.values[1]);
            $('#hidden_minimum_price').val(ui.values[0]);
            $('#hidden_maximum_price').val(ui.values[1]);
            filter_data();
        }
    });

});
</script>

</body>

</html>
