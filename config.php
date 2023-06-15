<?php 


session_start();

define('LOCALHOST', 'localhost');
define('ROOT', 'root');
define('PASSWORD', '');
define('DATABASE', 'db-log-reg-1');
define('SITEURL', 'http://localhost:8081/log-reg-form-1/log-reg-form-g/');




$conn = mysqli_connect(LOCALHOST, ROOT, PASSWORD, DATABASE) or die(mysqli_error('failed to connect!'));
$db_select = mysqli_select_db($conn, DATABASE) or die(mysqli_error('failed to select database!'));



?>