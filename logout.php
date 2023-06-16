<?php 



include('config.php');
session_destroy(); //destroy all session
header('location:' .SITEURL.'index.php'); // redirect to login page






?>