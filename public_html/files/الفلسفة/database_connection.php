<?php 

//database_connection.php

$connect = new PDO("mysql:host=mysql;dbname=akaroon_akaroondb","root","root", 


	array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
);

?>