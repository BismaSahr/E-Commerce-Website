<?php
session_start(); 

if (isset($_SESSION['email'])) {
    // Clear session data
    session_unset();
    session_destroy();


    setcookie('auth_token', '', time() - 3600, "/", "", true, true);

 
    header("Location: login.php");
    exit();
} else {

    header("Location: login.php");
    exit();
}
?>
