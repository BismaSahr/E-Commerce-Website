<?php
session_start();
require_once 'header.php';
require_once 'connection.php';

$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;
$total_items = 0;

// Migrate session cart items to database when a user logs in
if ($is_logged_in && isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $quantity = $item['quantity'];
        $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE quantity = quantity + ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiii", $user_id, $product_id, $quantity, $quantity);
        $stmt->execute();
    }
    unset($_SESSION['cart']); // Clear session cart after migration
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'], $_POST['quantity'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    if ($is_logged_in) {
        // Logged-in user: store directly in the database
        $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE quantity = quantity + ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiii", $user_id, $product_id, $quantity, $quantity);
        $stmt->execute();
    } else {
        // Guest user: store in session
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            // Get product details and store in session
            $sql = "SELECT p.id, p.name, p.price, p.image_path, b.name AS brand_name
                    FROM product p
                    INNER JOIN brand b ON b.id = p.brand_id
                    WHERE p.id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $product = $stmt->get_result()->fetch_assoc();
            if ($product) {
                $_SESSION['cart'][$product_id] = [
                    'product_name' => $product['name'],
                    'price' => $product['price'],
                    'image_path' => $product['image_path'],
                    'brand_name' => $product['brand_name'],
                    'quantity' => $quantity,
                ];
            }
        }
    }
}

// Retrieve cart items
$cart_items = [];

if ($is_logged_in) {
    // Retrieve cart items from database
    $sql = "SELECT COUNT(*) AS total_items FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $total_items = $row['total_items'] ?? 0;

    // Fetch detailed cart items
    $sql = "SELECT p.id, p.name AS product_name, p.price,p.quantity As productQuantity, c.quantity, p.image_path, b.name AS brand_name
            FROM cart c
            INNER JOIN product p ON c.product_id = p.id
            INNER JOIN brand b ON b.id = p.brand_id
            WHERE c.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
      
    }
    
} else {
    // Retrieve cart items from session
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $product_id => $item) {
            
            $cart_items[] = $item;
            $total_items += $item['quantity'];
        }
    }
}



$_SESSION['total_items'] = $total_items;
//Recomendation

$related_products_sql = "SELECT 
    p.id AS product_id,
    p.name AS product_name,
    p.image_path,
    p.price,
    b.name AS brand_name,
    c.name AS category_name
FROM 
    product p
INNER JOIN 
    brand b ON p.brand_id = b.id
INNER JOIN 
    categories c ON c.id = p.category_id

ORDER BY RAND()
LIMIT 6"; 

$related_stmt = $conn->prepare($related_products_sql);
$related_stmt->execute();
$related_result = $related_stmt->get_result();

$related_products = [];
if ($related_result) {
    while ($row = $related_result->fetch_assoc()) {
        $related_products[] = $row;
    }
}
?>

<style>
     body, html {
        margin: 0;
        padding: 0;
        overflow-x: hidden; 
    }
    .cart-items {
        display: flex;
        flex-direction: column;
    }

    .cart-item {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    .cart-item-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        margin-right: 20px;
    }

    .cart-item p {
        margin: 0 10px;
    }

    button {
    background-color: #320B56 ; 
    border: white solid;
    padding: 10px 15px; 
    border-radius: 5px; 
    transition: background-color 0.3s ease; 
    cursor: pointer;
    margin-top: auto; 
    color: white;
  }
  
  button:hover {
    background-color:#885b96; 
    color: white; 
  }
  .center-content {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
}



.cardd-img-top {
    transition: transform 0.3s ease-in-out;
    cursor: pointer; 
    object-fit: cover;
    height: 300px; 
    width: 100%;
}


.cardd {
    height: 100%; 
    width: 100%; 
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}


.cardd-body {
    flex-grow: 1; 
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 10px; 
    height: 200px; 
    overflow: hidden; 
}

.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis; 
}

 .card-img-top:hover {
 transform:  scale(1.1) !important; 
 }


 @media (max-width: 768px) {
    .col-md-2 {
        flex: 0 0 50%; 
        max-width: 50%;
    }

    .cardd-img-top {
        height: 300px; 
        width: 100%;
        object-fit: contain;
    }

    .cardd-body {
        height: 200px; 
    }
}
</style>

<div class="mt-2 d-flex justify-content-center">
    <h3>Your Cart Items</h3>
</div>
<div class="mt-5 mb-5 d-flex justify-content-center">
    <?php if (!empty($cart_items)): ?>
        <div class="cart-items">
            <?php foreach ($cart_items as $cart_item): ?>
                <div class="row align-items-center mb-3">
                    <div class="col-3">
                        <a href="<?= htmlentities($cart_item['image_path']) ?>">
                            <img src="<?= htmlentities($cart_item['image_path']) ?>" 
                                 alt="<?= htmlentities($cart_item['product_name']) ?>" 
                                 class="img-fluid rounded border">
                        </a>
                    </div>
                    <div class="col-9">
                        <h3 class="mb-1 fw-bold"><?= htmlentities($cart_item['product_name']) ?> 
                            by <?= htmlentities($cart_item['brand_name']) ?></h3>
                        <p class="mb-1">Rs. <?= htmlentities($cart_item['price']) ?></p>
                        <p class="mb-0">Quantity: <?= htmlentities($cart_item['quantity']) ?></p>
                        
                    </div>

                </div>
            <?php endforeach; ?>

        </div>
    <?php else: ?>
     <div class="row center-content">
      <div class="row ">
        <div class="col-12 mb-3">
          <h5>Your cart is empty.</h5>
        </div>
     </div> 
     </div>   
    <?php endif; ?>
</div>
<div class="d-flex justify-content-center">
        <a href="index.php"> <button type="button">Continue Shopping</button></a>
        </div>
<hr>

<h2 class="mt-4 ml-3">You may also like!</h2>
<div class="row no-gutters"> 
    <?php foreach ($related_products as $related_product): ?>
        <div class="col-6 col-md-2 mb-5 d-flex "> 
              <div class="cardd card d-flex flex-column" style="border:none;">
                <a href="productdetail.php?detail_id=<?php echo htmlspecialchars($related_product['product_id']); ?>">
                    <img class="cardd-img-top card-img-top custom-img" src="<?php echo htmlspecialchars($related_product['image_path']); ?>" alt="<?php echo htmlspecialchars($related_product['product_name']); ?>">
                </a>
                <div class="cardd-body card-body"> 
                 <p class="card-text mb-0 mt-0"><b><?php echo htmlspecialchars($related_product['product_name'] . " by " . $related_product['brand_name']); ?></b><br> Rs. <?php echo htmlspecialchars($related_product['price']); ?></p>
                       
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php
require_once 'footer.php';
include 'javascriptlink.html';
?>
