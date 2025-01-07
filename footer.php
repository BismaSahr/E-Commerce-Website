<?php

include 'connection.php';
require_once 'site.php';

//Fetching Main Items
$MainitemQuery = "SELECT * FROM footer_main_items";
$MainitemResult = $conn->query($MainitemQuery);

if ($MainitemResult->num_rows > 0) {
    $contactus = $customers = $company = null;

    while ($row2 = $MainitemResult->fetch_assoc()) {
        if ($row2['id'] === '1' ) {
            $contactus = $row2['name']; 
        } elseif ($row2['id'] === '2') {
            $customers = $row2['name'];
        } elseif ($row2['id'] === '3') {
            $company = $row2['name'];
        }
    }
} else {
    echo "No data found in footer_main_items.";
}

//Fetching Sub items
$query = "SELECT * FROM footer_items";
$result = $conn->query($query);


if ($result->num_rows > 0) {
  
    while ($row = $result->fetch_assoc()) {
        $footer_items[] = $row; 
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
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0 text-center text-lg-start">
                <a href="index.php">
                    <img src="<?php echo $logoPath ; ?>" alt="Logo" class="logo img-fluid mb-3">
                </a>
            </div>

           
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <h5 class="text-uppercase"><?php echo $contactus ; ?></h5>
                <ul class="list-unstyled">
                    <?php
                 
                    foreach ($footer_items as $item) {
                        if ($item['link'] === 'contact.php') {
                            echo "<li><a href='" . $item['link'] . "' class='text-white text-decoration-none'>" . $item['name'] . "</a></li>";
                        }
                        elseif ($item['main_item_id'] === '1') {
                            echo "<li>" . $item['link'] . "</li>";
                        }
                    }
                    ?>
                    <!-- <li><a href="contactus" class="text-white text-decoration-none">Email Us</a></li> -->
                </ul>
            </div>

            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <h5 class="text-uppercase"><?php echo $customers; ?></h5>
                <ul class="list-unstyled">
                    <?php
                  
                    foreach ($footer_items as $item) {
                        if ($item['main_item_id'] === '2') {
                            echo "<li><a href='" . $item['link'] . "' class='text-white text-decoration-none'>" . $item['name'] . "</a></li>";
                        }
                    }
                    ?>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
             <h5 class="text-uppercase"><?php echo $company ; ?></h5>
             <ul class="list-unstyled">
                 <?php
        
                   foreach ($footer_items as $item) {
           
                   if (strtolower(trim($item['main_item_id'])) === '3') {
                   echo "<li><a href='" . $item['link'] . "' class='text-white text-decoration-none'>" . $item['name'] . "</a></li>";
                  }
                  }
                ?>
             </ul>
         </div>

        </div>

    
        <hr class="my-4 bg-light">

  
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