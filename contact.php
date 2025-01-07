<?php 
session_start();
require_once 'header.php';
require_once 'connection.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $number = $_POST['number'];
    $message = $_POST['message'];

    // Insert the data into the database
    $sql = "INSERT INTO messages (name, email, number, message) VALUES (?, ?, ?, ?)";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("ssss",$name,$email,$number, $message);
    $result=$stmt->execute();
    
    if ($result) {
        echo "<script>alert('Message sent successfully!'); window.location.href='index.php';</script>";
        header("Location: index.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

}
$sqlLocation = "SELECT * FROM location LIMIT 1";  
$stmtLocation=$conn->prepare($sqlLocation);
$stmtLocation->execute();
$resultLocation = $stmtLocation->get_result();

// Check if data is available
if ($resultLocation ->num_rows > 0) {
   // Fetch data
   $location = $resultLocation->fetch_assoc();
} else {
   echo "No data found.";
}

$conn->close();
?>

<style>
        body, html {
        margin: 0;
        padding: 0;
        overflow-x: hidden; 
    }
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
  .color {
        border: 2px solid #885b96 !important; 
    }

    .form-control {
        border-radius: 5px;
    }
    .link{
        color:#885b96;
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
<h1 class="">Get in touch with us</h1>

</div>
<div class="d-flex justify-content-center m-3">

<p>We will respond to every email within 24 hours, Monday to Friday, excluding holidays.</p>
</div>
<div class="d-flex justify-content-center">

<form action="" method="POST" id="form">
    <div class="row mt-4">
        <div class="col-md-6">
            <label for="name">Name</label>
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Name" aria-label="fname" name="name" aria-describedby="basic-addon1" required>
            </div>
        </div>  
        <div class="col-md-6">
            <label for="email">Email</label>
            <div class="input-group mb-3">
                <input type="email" class="form-control" placeholder="Email" aria-label="email" name="email" aria-describedby="basic-addon1" required>
            </div>
        </div>      
    </div>         
    <div class="row mt-1">
        <div class="col-12">
            <label for="number">Phone number</label>
            <input type="text" class="form-control" placeholder="Phone number" aria-label="number" name="number" aria-describedby="basic-addon1" required>
        </div>
    </div>

    <div class="row mt-1">
        <div class="col-12">
            <label for="message">Message</label>
            <textarea type="text" class="form-control" placeholder="Type your message here..." name="message" required></textarea>
        </div>
    </div>

    <div class="row mt-4 mb-5">
        <div class="col-12 d-flex justify-content-center">
            <button type="submit" class="button text-white"><i class="fas fa-paper-plane"></i> Send</button>
        </div>
    </div>
</form>

</div>
<div class="container mt-5">
    <div class="row justify-content-start">
        <div class="col-md-8 col-lg-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="card-title ">Outlet Information</h3>
                    <p><strong >Address:</strong> <?php echo $location['address']; ?>, <?php echo $location['area']; ?></p>
                    <p><strong>City:</strong> <?php echo $location['city']; ?></p>
                    <p><strong>Country:</strong> <?php echo $location['country']; ?></p>
                    <p><strong>Email:</strong> <a class="link" href="mailto:<?php echo $location['email']; ?>"><?php echo $location['email']; ?></a></p>
                    <p><strong>Phone:</strong> <a class="link" href="tel:<?php echo $location['phone']; ?>"><?php echo $location['phone']; ?></a></p>
                    <p><strong>Working Hours:</strong> <?php echo $location['working_hours']; ?></p>
                    <a href="https://www.google.com/maps/place/Dolmen+Mall+-+Clifton/@24.802281,67.0273625,17.12z/data=!4m6!3m5!1s0x3eb33d099c12586f:0x8f468f64498f32f7!8m2!3d24.8021323!4d67.0300195!16s%2Fm%2F0wr8vjr?entry=ttu&g_ep=EgoyMDI0MTIxMS4wIKXMDSoASAFQAw%3D%3D" target="_blank">
                      <button type="button" class="button text-white">Get directions</button>
                    </a> 
          </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-center mt-5 mb-5">
<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3621.804788734754!2d67.02744457366666!3d24.802137147592102!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3eb33d099c12586f%3A0x8f468f64498f32f7!2sDolmen%20Mall%20-%20Clifton!5e0!3m2!1sen!2s!4v1734809200074!5m2!1sen!2s" width="800" height="600" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

</div>
<?php
require_once 'footer.php';
include 'javascriptlink.html';
?>  
