<?php
$_db_socket = getenv('DB_SOCKET');
$db_host    = getenv('DB_HOST')     ?: 'mysql';
$db_user    = getenv('DB_USER')     ?: 'root';
$db_pwd     = getenv('DB_PASSWORD') ?: 'root';
$database   = getenv('DB_NAME')     ?: 'form-wizard';

if ($_db_socket) {
    $db_conn = mysqli_init();
    mysqli_real_connect($db_conn, null, $db_user, $db_pwd, $database, null, $_db_socket);
} else {
    $db_conn = mysqli_connect($db_host, $db_user, $db_pwd, $database);
}
unset($_db_socket);
if (!$db_conn)
    die("can't Connect to Database");

mysqli_set_charset($db_conn, 'utf8mb4');
?>