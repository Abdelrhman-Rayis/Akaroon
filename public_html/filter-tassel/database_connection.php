<?php
$_db_socket = getenv('DB_SOCKET');
$_db_host   = getenv('DB_HOST')     ?: 'mysql';
$_db_name   = getenv('DB_NAME')     ?: 'akaroon_akaroondb';
$_db_user   = getenv('DB_USER')     ?: 'root';
$_db_pass   = getenv('DB_PASSWORD') ?: 'root';

$_dsn = $_db_socket
    ? "mysql:unix_socket={$_db_socket};dbname={$_db_name}"
    : "mysql:host={$_db_host};dbname={$_db_name}";

$connect = new PDO($_dsn, $_db_user, $_db_pass,
    [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"]
);
unset($_db_socket, $_db_host, $_db_name, $_db_user, $_db_pass, $_dsn);
