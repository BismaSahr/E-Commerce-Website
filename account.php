<?php
session_start();

require_once 'header.php';
require_once 'connection.php';
// Start session to track user

if (!isset($_SESSION['email'])) {
    header("Location: login.php"); 
    exit();
}


else{
$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Account</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .button {
    background-color: #320B56; 
  
    border: white solid;
    padding: 10px 15px; 
    border-radius: 5px; 
    transition: background-color 0.3s ease; 
    cursor: pointer;
  }
  
  .button:hover {
    background-color:#885b96; 
    color: white; 
  }
        .account-container {
            max-width: 500px;
            margin: 50px auto;
            text-align: center;
        }
        .logout-button {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="account-container">
        <!-- <h1>Welcome, <?php echo htmlspecialchars($full_name); ?>!</h1> -->
        <p>Your email: <strong><?php echo htmlspecialchars($email); ?></strong></p>
        
        <!-- Optional success message -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success_message']; ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>


        <form method="POST" action="logout.php">
            <button type="submit" name="logout" class="btn btn-danger logout-button">Logout</button>
        </form>
    </div>
<?php
}
require_once 'footer.php';
include 'javascriptlink.html';
?>
