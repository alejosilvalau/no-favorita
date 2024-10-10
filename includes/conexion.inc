<?php 
include_once("../config.php");

$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS) or die("Error");
mysqli_select_db($link, DB_NAME);
?>
