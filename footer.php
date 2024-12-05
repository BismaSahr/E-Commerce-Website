<?php
// Include database connection
include 'connection.php';

$query = "SELECT * FROM footer_items";
$result = $conn->query($query);

// Check if the query returned any rows
if ($result->num_rows > 0) {
    // Store the fetched data in an associative array
    while ($row = $result->fetch_assoc()) {
        $footer_items[] = $row;  // Store each row in the footer_items array
    }
} else {
    echo "No footer items found.";

}


?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="headerfooter.css">
</head>
<body>
 
<footer class="custom-bg text-white py-5">
    <div class="container">
        <div class="row">
            <!-- Logo Section -->
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0 text-center text-lg-start">
                <a href="#">
                    <img src="MyImages/LogoImages/Brandlogo.jpg" alt="Logo" class="logo img-fluid mb-3">
                </a>
            </div>

            <!-- Contact Us Section -->
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <h5 class="text-uppercase">Contact Us</h5>
                <ul class="list-unstyled">
                    <?php
                    // Display contact information
                    foreach ($footer_items as $item) {
                        if ($item['description'] == 'Contact_US') {
                            echo "<li>" . $item['link'] . "</li>";
                        }
                    }
                    ?>
                    <li><a href="contactus" class="text-white text-decoration-none">Email Us</a></li>
                </ul>
            </div>

            <!-- Customers Section -->
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <h5 class="text-uppercase">Customers</h5>
                <ul class="list-unstyled">
                    <?php
                    // Display customer-related links
                    foreach ($footer_items as $item) {
                        if ($item['description'] == 'Customers') {
                            echo "<li><a href='" . $item['link'] . "' class='text-white text-decoration-none'>" . $item['name'] . "</a></li>";
                        }
                    }
                    ?>
                </ul>
            </div>

            <!-- Company Section -->
            <div class="col-lg-3 col-md-6">
             <h5 class="text-uppercase">Company</h5>
             <ul class="list-unstyled">
                 <?php
        
                   foreach ($footer_items as $item) {
           
                   if (strtolower(trim($item['description'])) == 'company') {
                   echo "<li><a href='" . $item['link'] . "' class='text-white text-decoration-none'>" . $item['name'] . "</a></li>";
                  }
                  }
                ?>
             </ul>
         </div>

        </div>

        <!-- Divider -->
        <hr class="my-4 bg-light">

        <!-- Bottom Section -->
        <div class="row">
            <div class="col text-center">
                <p class="mb-0"> <?php 
                   foreach ($footer_items as $item) {
                    if ($item['name'] == 'Copyright') {
                     echo $item['description'];
                     break; 
                      }
                   }
    ?>
                <div class="social-icons mt-3">
                    <?php
                    // Display social media links
                    foreach ($footer_items as $item) {
                        if ($item['description'] == 'Social' ) {
                            echo "<a href='" . $item['link'] . "' class='text-white mx-2'><i class='fab fa-" . strtolower(substr($item['name'], 1)) . "'></i></a>";
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</footer>


    
    
    
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</body>
</html>