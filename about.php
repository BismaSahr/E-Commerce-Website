<?php 
session_start();
require_once 'header.php';
require_once 'connection.php';

// Fetch the content and image 
$query = "SELECT content, image_path FROM about_us LIMIT 1";  
$result = mysqli_query($conn, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $aboutContent = $row['content'];
    $aboutImage = $row['image_path'];
} else {
    $aboutContent = "Content not available.";
    $aboutImage = "MyImages/about/about.jpg"; 
}

$aboutContent = str_replace("Bachat.pk", "<a href='index.php' style='color:#320B56; font-weight: bold;'>Bachat.pk</a>", $aboutContent);

?>

<style>
    body, html {
        margin: 0;
        padding: 0;
        overflow-x: hidden; 
    }
    .card-img-top {
        width: 100%;
        height: 400px; 
        object-fit: cover; 
    }

    @media (max-width: 768px) {
        .card-img-top {
            height: 200px;
        }
    }
</style>

<div class="container mt-5 mb-5">
  <div class="d-flex justify-content-center">
    <div class="card-deck">
      <div class="card">
        <img class="card-img-top" src="<?php echo $aboutImage; ?>" alt="About Us Image">
      </div>
    </div>
  </div>
</div>


  <div class="d-flex justify-content-center m-5">
         <div><?php echo $aboutContent; ?></div>

</div>

<?php
require_once 'footer.php';
include 'javascriptlink.html';
?>  
