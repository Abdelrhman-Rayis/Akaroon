<?php
$db_host='mysql';
$db_user='root';
$db_pwd='root';
$database='form-wizard';

$db_conn = mysqli_connect($db_host,$db_user,$db_pwd,$database);
if(!$db_conn)
    die("can't Connect to Database");

mysqli_set_charset($db_conn, 'utf8');

?>