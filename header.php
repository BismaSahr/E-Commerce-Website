<?php
// session_start();
include 'connection.php'; 
require_once 'site.php';


//Navbar Items fetching from DataBase
$query = "SELECT * FROM navbar_items WHERE status = 'active'"; 
$main_items = $conn->query($query);

//Category List fetching from DataBase
$category_query = "SELECT * FROM categories WHERE status = 'active' ORDER BY position";
$categories = $conn->query($category_query);

$quantity = isset($_SESSION['total_items']) ? $_SESSION['total_items'] : 0;


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
    <style>

      button:focus, input:focus, .btn:focus, .form-control:focus{
       outline: none !important;  
       box-shadow: none  !important;
      }
      .dropdown-menu .dropdown-item:hover {
        background-color:#320B56 !important;
        color: white !important; 
      }

      
    </style>


    
</head>
<body>

    <header>
      <div class="custom-bg d-flex justify-content-center bg-info text-white" style="height:60px;"><h1 class="mt-2"><?php echo $site_name; ?></h1></div>
      <nav class="custom-bg navbar navbar-expand-lg navbar-dark bg-dark pr-4 pl-1 pb-2 pt-4">
        <a class="navbar-brand" href="index.php">
          <img class="logo" src="<?php echo $logoPath; ?>" alt="">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
      
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
           <?php while ($item = $main_items->fetch_assoc()): ?>
           <?php if ($item['name'] === "Category"): ?>
           <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Category
              </a>
              <div class="dropdown-menu custom-bg" aria-labelledby="navbarDropdown">
                <?php while ($category = $categories->fetch_assoc()): ?>
                <a class="text-white dropdown-item" href="<?php echo $category['link']; ?>"><?php echo $category['name']; ?></a>
                <?php endwhile; ?>
              </div>
           </li>
           <?php else: ?>
           <li class="nav-item">
             <a class="nav-link" href="<?php echo $item['link']; ?>"><?php echo $item['name']; ?></a>
           </li>
            <?php endif; ?>
            <?php endwhile; ?>
        </ul>

          <form action="search.php" method="GET" class="form-inline my-2 my-lg-0">
              <button class="btn text-white" type="button" data-bs-toggle="collapse" data-bs-target="#searchField" aria-expanded="false" aria-controls="searchField">
                  <i class="fas fa-search"></i> 
              </button>
      
              <div class="collapse mr-2" id="searchField">
                  <input type="text" name="q" class="form-control" placeholder="Search Product" id="searchInput">
              </div>
          </form>
         
            <a href="cart.php" class="position-relative d-inline-block">
              <button type="button" class="btn text-white">
                  <i class="fas fa-cart-shopping"></i>
              </button>
              <span class="text-white position-absolute top-0 start-100 translate-middle badge rounded-pill" 
                    style="font-size: 10px; transform: translate(-90%, -10%); height: 15px; background-color: #320B56">
                  <?php echo $quantity; ?>
              </span>
          </a>
       

          <a href="login.php">
          <button type="button" class="btn text-white"><i class="fas fa-user"></i><span class="ml-2">Account</span></button>
          </a>
        </div>
      </nav>
    </header>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>    
<?php
include 'javascriptlink.html';
?>