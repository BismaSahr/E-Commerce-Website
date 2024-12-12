<?php
require_once 'connection.php';
require_once 'header.php';
require_once 'site.php';

if (isset($_GET['q'])) {
    $searchQuery = trim($_GET['q']); 
} else {
    $searchQuery = ''; 
}

if (empty($searchQuery)) {
    echo "Please enter a search term.";
    exit;
}

$searchTerm = '%' . $searchQuery . '%';


$searchSQL = "
    SELECT 
        p.id, 
        p.name AS product_name, 
        p.price, 
        p.image_path, 
        b.name AS brand_name, 
        c.name AS category_name 
    FROM 
        product p
    INNER JOIN 
        brand b ON p.brand_id = b.id
    INNER JOIN 
        categories c ON p.category_id = c.id
    WHERE 
        p.name LIKE ? OR 
        b.name LIKE ? OR 
        c.name LIKE ? OR 
        p.price LIKE ?
";

if ($stmt = mysqli_prepare($conn, $searchSQL)) {
    mysqli_stmt_bind_param($stmt, "ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $products = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($product = mysqli_fetch_assoc($result)) {
            $products[] = $product;
        }
    } else {
        echo "No results found for '$searchQuery'.";
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing the SQL statement.";
}

?>
<link rel="stylesheet" href="cart.css">
<div class="container d-flex justify-content-center mt-2 mb-2">
    <h2>Search Results for "<?php echo htmlspecialchars($searchQuery); ?>"</h2>
</div>    
<div class="container">
    <div class="row">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="col-6 col-md-3 mb-3 d-flex">
                    <div class="card custom-bg text-white d-flex flex-column">
                        <a href="productdetail.php?detail_id=<?php echo htmlspecialchars($product['id']); ?>">
                            <img class="card-img-top" src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['product_name']." by ".$product['brand_name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($site_name); ?></p>
                            <p class="card-text font-weight-bold">Category: <?php echo htmlspecialchars($product['category_name']); ?></p>
                            <p class="card-text font-weight-bold">Rs. <?php echo htmlspecialchars($product['price']); ?></p>
                            <button type="submit" class="button text-white">Add to cart</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products found matching your search.</p>
        <?php endif; ?>
    </div>
</div>

<?php

mysqli_close($conn);
require_once 'footer.php';
include 'javascriptlink.html';
?>
