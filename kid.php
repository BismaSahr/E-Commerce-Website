<?php 
require_once 'header.php';
require_once 'connection.php'; 
require_once 'site.php';

// Fetch category
$category_id = 3; // Assuming '2' corresponds to the desired category
$category_query = "SELECT name FROM categories WHERE id = $category_id";
$result = mysqli_query($conn, $category_query);

if ($result && mysqli_num_rows($result) > 0) {
    $category = mysqli_fetch_assoc($result); 
    $category_name = $category['name'];
} else {
    $category_name = 'Category Not Found';
}

// Fetching Product
$product_query = "SELECT p.id, p.name AS product_name, p.price, p.image_path, b.name AS brand_name 
                  FROM product p 
                  INNER JOIN brand b ON p.brand_id = b.id 
                  WHERE p.category_id = $category_id
                  ORDER BY RAND()"
                  ;
$productResult = mysqli_query($conn, $product_query);

$products = []; 

if ($productResult) {
    while ($product = mysqli_fetch_assoc($productResult)) {
        $products[] = $product; 
        $product_id=$product['id'];

    }
} else {
    echo "Error in query execution: " . mysqli_error($conn);
}

?>

<link rel="stylesheet" href="cart.css">

<div class="container d-flex justify-content-center mt-2 mb-2">
<h1 class="m-3 "><?php echo htmlspecialchars($category_name); ?></h1>
</div>

<div class="container">
    <div class="row">
        <?php foreach ($products as $product): ?>
        <div class="col-6 col-md-3 mb-3 d-flex">
            <div class="card custom-bg text-white d-flex flex-column">
                <a href="productdetail.php?detail_id=<?php echo htmlspecialchars($product['id'])?>" >
                <img class="img card-img-top" src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                </a>
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($product['product_name']." by ".$product['brand_name']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($site_name); ?></p>
                    <p class="card-text font-weight-bold">Rs.<?php echo htmlspecialchars($product['price']); ?></p>
                    <button type="submit" class="button text-white">Add to cart</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
require_once 'footer.php';
include 'javascriptlink.html';
