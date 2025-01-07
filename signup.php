<?php
session_start();
require_once 'header.php';
require_once 'connection.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fname = filter_var(trim($_POST['fname']));
    $lname = filter_var(trim($_POST['lname']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    // Validation
    $errors = [];
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<script>alert('$error');</script>";
        }
        echo "<script>window.location.href = 'signup.php';</script>";
        exit();
    }

    // Check for duplicate email
    $queryemail = "SELECT id FROM accounts WHERE email = ?";
    $stmtemail = $conn->prepare($queryemail);
    $stmtemail->bind_param("s", $email);
    $stmtemail->execute();
    $resultemail=$stmtemail->get_result();

    if ($resultemail->num_rows > 0) {
        echo "<script>alert('Email is already registered. Please use a different email.');</script>";
        echo "<script>window.location.href = 'signup.php';</script>";
        exit();
    }

else{
    // Insert new user
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $query = "INSERT INTO accounts (fname ,lname, password,email) VALUES (?, ?, ?,?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $fname,$lname, $hashedPassword, $email);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;
         $fullname=$fname.$lname;
        $_SESSION['email'] = $email;
        $_SESSION['user_name'] =  $fullname;

        $_SESSION['success_message'] = "Your account has been created successfully!";
        echo "<script>window.location.href = 'account.php';</script>";
        // header('Location: account.php');
        exit();
    } else {
        echo "<script>alert('Error creating account. Please try again later.');</script>";
        echo "<script>window.location.href = 'signup.php';</script>";
    }

    $stmt->close();
    $conn->close();
}}
?>


<style>
    .button {
    background-color: #320B56; 
     width: 200px;
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


  .form-control,.font{
    border-color: #320B56 !important; 
    border: solid 4px;
    border-radius:0px !important; 
  }
  .font{
    background-color:#885b96 !important;
  }
  .form-control:focus {
        border-color: #885b96 !important; 
        box-shadow: 0 0 5px #885b96; 
        outline: none; 
        transition: border-color 0.3s ease, box-shadow 0.3s ease; 
    }
</style>
<div class="d-flex justify-content-center mt-2 mb-1">
<h1 class="">Create Account</h1>
</div>

    <div class="d-flex justify-content-center">

        <form action="" method="POST" id="form" >
            <div class="row mt-4">
                  <label for="fname" >First Name</label>
                
                <div class="input-group mb-3  ">
                    <span class="font input-group-text text-white" id="basic-addon1"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" placeholder="Firstname" aria-label="fname" name="fname" aria-describedby="basic-addon1">
                  </div>
            </div>  


            <div class="row mt-1">
                  <label for="lname">Last Name</label>
                <div class="input-group mb-3">
                    <span class="font input-group-text text-white" id="basic-addon1"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" placeholder="Lastname" aria-label="lname" name="lname" aria-describedby="basic-addon1">
                  </div>
            </div>  
            
            <div class="row mt-1">
                  <label for="password">Password</label>
    
                <div class="input-group mb-3">
                    <span class="font input-group-text text-white" id="basic-addon1"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" placeholder="Password" aria-label="password" name="password" aria-describedby="basic-addon1">
                  </div>
            </div>  
            <div class="row mt-1">
                  <label for="email">Email</label>
        
                <div class="input-group mb-3 ">
                    <span class=" font input-group-text text-white" id="basic-addon1"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" placeholder="Email" aria-label="email" name="email" aria-describedby="basic-addon1">
                  </div>
            </div>
            <div class="d-flex justify-content-center mb-4" >
            <button type="submit" class="button text-white"><i class="fas fa-paper-plane"></i>Create</button>
            </div>
  </form>
</div>
<script>
    document.getElementById('form').addEventListener('submit', function (event) {
        const fname = document.querySelector('input[name="fname"]').value;
        const lname = document.querySelector('input[name="lname"]').value;
        const email = document.querySelector('input[name="email"]').value;
        const password = document.querySelector('input[name="password"]').value;

        // Regex for name validation (no numbers or special characters)
        const nameRegex = /^[a-zA-Z\s]+$/;
        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!emailPattern.test(email)) {
            alert("Email Should be Valid");
            event.preventDefault(); 
            return;
        }
        if (!nameRegex.test(fname)) {
            alert("First Name should not contain numbers or special characters.");
            event.preventDefault(); 
            return;
        }

        if (!nameRegex.test(lname)) {
            alert("Last Name should not contain numbers or special characters.");
            event.preventDefault();
            return;
        }

        if (password.length < 8) {
            alert("Password must be at least 8 characters long.");
            event.preventDefault();
            return;
        }
    });
</script>

<?php
require_once 'footer.php';
include 'javascriptlink.html';

?>

