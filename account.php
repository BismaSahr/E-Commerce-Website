<?php
ob_start();
session_start();
require_once 'header.php';
require_once 'connection.php';


if (!isset($_SESSION['email'])) {
    header("Location: login.php"); 
    exit();
}


else{
$email = $_SESSION['email'];
$fullname=$_SESSION['user_name'];
if (isset($_SESSION['total_items'])) {
    $items=$_SESSION['total_items'] ;
}else {
    $items=0;
}
ob_flush();
?>
  <style>
.button {
    background-color: #320B56 !important; 
    border: white solid;
    padding: 10px 15px; 
    border-radius: 5px; 
    transition: background-color 0.3s ease; 
    cursor: pointer;
  }
  
.button:hover {
    background-color:#885b96 !important; 
    color: white; 
  }
    </style>
</head>
<body>
    <div class="d-flex justify-content-center m-5">
    <div class="account-container">
        <h1>Welcome, <?php echo htmlspecialchars($fullname); ?>!</h1>
        <p>Your email: <strong><?php echo htmlspecialchars($email); ?></strong></p>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success_message']; ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
     
        <h5>You have <?php echo htmlspecialchars($items); ?> items in Cart</h5>
        <a href="cart.php" style="border:none;">
         <button class="button text-white mt-2 mb-4" type="button">Check it out</button> 
        </a>

        <form method="POST" action="logout.php" class="d-flex justify-content-center">
            <button type="submit" name="logout" class="button btn text-white">Logout</button>
        </form>
    </div>
    </div>
<?php
}
require_once 'footer.php';
include 'javascriptlink.html';
?>
