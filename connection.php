<?php
$server="localhost";
$username="root";
$password="";
$database="e-commerce-website";

$conn= mysqli_connect($server,$username,$password,$database);
// localstorage.setitem('cart',i+1);

if (!$conn) {
    die("Connection Failed" . mysqli_connect_error());
}

?>