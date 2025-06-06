<?php 

$server="localhost";
$username="root";
$password="abcd@123";
$dbname="hotel";
$port=3307;

$con = mysqli_connect($server,$username,$password,$dbname,$port);

if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}

?>
