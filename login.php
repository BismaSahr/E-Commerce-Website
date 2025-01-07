<?php
session_start();
require_once 'header.php';
require_once 'connection.php'; 
if (isset($_SESSION['email'])) {
    echo "<script>window.location.href = 'account.php';</script>";

}else{
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);
    $remember_me = isset($_POST['remember_me']);

    // Input validation
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
        echo "<script>window.location.href = 'login.php';</script>";
        exit();
    }

    $query = "SELECT id, fname, lname, password FROM accounts WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result(); // Get the result object

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc(); // Fetch the row as an associative array
        $user_id = $row['id'];
        $fname = $row['fname'];
        $lname = $row['lname'];
        $hashedPassword = $row['password'];

        // Verify password
        if (password_verify($password, $hashedPassword)) {
            $fullname=$fname.$lname;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $fullname;
            $_SESSION['email'] = $email;
            $_SESSION['success_message'] = "You have successfully logged in!";

            if ($remember_me) {
                $token = bin2hex(random_bytes(32)); 
                setcookie('auth_token', $token, time() + (86400 * 30), "/", "", true, true); 

                $query = "UPDATE accounts SET auth_token = ? WHERE id = ?";
                $update_stmt = $conn->prepare($query);
                $update_stmt->bind_param("si", $token, $user_id);
                $update_stmt->execute();
                $update_stmt->close();
            }
            
            echo "<script>window.location.href = 'account.php';</script>";
            exit();
        } else {
            echo "<script>alert('Invalid password');</script>";
        }
    } else {
        echo "<script>alert('No account found with this email');</script>";
    }

    $stmt->close();
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

    .styled-checkbox {
    border: 2px solid #320B56;
    width: 18px;
    height: 18px;
    accent-color: #320B56;
    transition: border 0.3s ease, background-color 0.3s ease;
    cursor: pointer;
    border:solid 2px  #885b96;
}

.styled-checkbox:focus-visible {
    border-color: #885b96; 
    box-shadow: 0 0 3px #885b96;
}
</style>

<div class="d-flex justify-content-center mt-2 mb-1">
<h1 class="">Login</h1>
</div>
<div class="d-flex justify-content-center">
   <form action="" method="POST" id="form" >
        <div class="row mt-1">
            <label for="email">Email</label>
            <div class="input-group mb-3 ">
                <span class="font input-group-text text-white" id="basic-addon1"><i class="fas fa-envelope"></i></span>
                <input type="email" class="form-control" placeholder="Email" aria-label="email" name="email" aria-describedby="basic-addon1">
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
            <div class="col-12 d-flex justify-content-center">
                <input type="checkbox" name="remember" id="remember" class="styled-checkbox mb-0 mt-1 ml-5">
                <label for="remember" class="mr-5 mt-0">Remember Me</label>
            </div>
        </div>

        <div class="d-flex justify-content-center" >
            <button type="submit" class="button text-white"><i class="fas fa-paper-plane"></i> Sign In</button>
        </div>

        <div class="row mt-0 mb-5">
            <a href="signup.php" style="border:none; color:Black;"><h6>Create Account</h6></a>
        </div>
    </form>
</div>

<?php
require_once 'footer.php';
include 'javascriptlink.html';
?>
