<?php
$username = 'root';
$host = 'localhost';
$password = '';
$database = 'task_manager';

$conn = mysqli_connect($host, $username, $password, $database);
if(!$conn) {
    die("Connection Failed:". mysqli_connect());
}
//echo "Connected successfully";


