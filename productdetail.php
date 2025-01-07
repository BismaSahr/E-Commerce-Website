<?php
ob_start();
session_start();
require_once 'header.php';
require_once 'connection.php'; 
require_once 'site.php';

$product_id = $_GET['detail_id'];



$sql = "SELECT 
    p.id AS product_id,
    p.name AS product_name,
    p.description AS product_description,
    p.image_path,
    p.price,
    p.quantity,
    c.name AS category_name,
    b.name AS brand_name,
    GROUP_CONCAT(pi.image_path) AS additional_images
FROM 
    product p
INNER JOIN 
    categories c ON p.category_id = c.id
INNER JOIN 
    brand b ON p.brand_id = b.id
LEFT JOIN 
    product_images pi ON p.id = pi.product_id
WHERE 
    p.id = ? 
GROUP BY p.id";  

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    $product = $result->fetch_assoc();
}

$stock = $product['quantity'] == 0;

//Recommendation

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
WHERE 
    p.id != ? 
ORDER BY RAND()
LIMIT 6"; 

$related_stmt = $conn->prepare($related_products_sql);
$related_stmt->bind_param("i", $product_id);
$related_stmt->execute();
$related_result = $related_stmt->get_result();

$related_products = [];
if ($related_result) {
    while ($row = $related_result->fetch_assoc()) {
        $related_products[] = $row;
    }
}
$stmt->close();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL; // Set user_id from session or NULL if not logged in

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $product_id = $_POST['product_id'];
    $review_text = $_POST['review_text'];
    $rating = $_POST['rating'];
    $created_at = date('Y-m-d H:i:s'); // Current timestamp
    $query = "INSERT INTO reviews (user_id, order_id, product_id, review_text, rating, created_at) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiisis", $user_id, $order_id, $product_id, $review_text, $rating, $created_at);

    if ($stmt->execute()) {
        header("Location: productdetail.php?detail_id=" . $product_id);
        exit;  
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

ob_end_flush();  
?>


<style>

input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    -moz-appearance: textfield; 
}

header {
    border-radius: 0 !important;
}


    body, html {
        margin: 0;
        padding: 0;
        overflow-x: hidden; 
    }

    .carousel-inner img {
        max-height: 70vh; 
        width: 100%;
        object-fit: contain;
    }
    .carousel-inner img:hover {
        cursor: pointer;
    }

    .carousel-thumbnails img {
        width: 60px;
        height: auto;
        margin: 0 5px;
        cursor: pointer;
        border: 2px solid transparent;
    }

    .carousel-thumbnails img.active {
        border-color:  #320B56; 
    }

    .carousel-inner img {
        transition: transform 0.3s ease;
    }

    .carousel-inner img:hover {
        transform: scale(1.5); 
    }

    .carousel-control-prev-icon, .carousel-control-next-icon {
        filter: invert(1); 
    }


    .button {
    background-color: #320B56 ; 
    border: white solid;
    padding: 10px 15px; 
    border-radius: 5px; 
    transition: background-color 0.3s ease; 
    cursor: pointer;
    margin-top: auto; 
  }
  
  .button:hover {
    background-color:#885b96; 
    color: white; 
  }

  .quantity-box {
  display: flex;
  align-items: center;
  gap:0;
}

.quantity-box #btn-decrement,
.quantity-box #btn-increment {
    border-radius: 0;
    width: 30px;
    height: 30px;
    font-size: 18px;
    font-weight: bold;
    background-color: #320B56; 
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
}

.quantity-box #btn-decrement:hover,
.quantity-box #btn-increment:hover {
    background-color: #885b96; 
}



.quantity-box input[type="number"] {
    
  width: 50px;
  height: 30px;
  text-align: center;
  border:none;
  background-color:#885b96;
  margin: 0;
  font-size: 16px;
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





        .star-rating span {
            font-size: 18px;
        }



        .fas.fa-chevron-down {
    display: inline-block;
    text-align: center;
    line-height: 20px; 
    width: 20px; 
    height: 20px; 
    border-radius: 50%; 
    border: none;
    color: #320B56; 
    font-size: 16px; 
    margin-left: 10px; 
    transition: all 0.3s ease; 
}

.fas.fa-chevron-down:hover {
    background-color: #885b96; 
    color: white; 
    cursor: pointer; 
}

.hr {
    background-color: #885b96;
}

#reviewsAccordion .card {
    border: 1px solid #885b96;
    margin-bottom: 10px;
}

#reviewsAccordion .card-header {
    background-color: #f8f9fa;
    border: none;
}

#reviewsAccordion .card-body {
    padding: 15px;
    background-color: #f8f9fa;
    color: #333;
}

#reviewsAccordion .btn-link {
    text-decoration: none;
    color: #320B56;
    font-weight: bold;
}

#reviewsAccordion .btn-link:hover {
    color: #885b96;
}

.star-rating {
    font-size: 20px;
    color: #320B56;
}

.star-rating .empty {
    color: gray;
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
    input[type="radio"] {
  display: none;
}

.star {
    font-size: 20px;
  color: #ccc;
  cursor: pointer;
  transition: color 0.3s;
}

input[type="radio"]:checked ~ .star {
  color:#885b96;
}
</style>

<div class="row d-flex mt-4">
    <div class="col-6">
        <div id="productCarousel" class="carousel slide" data-interval="false">
         
            <div class="carousel-inner w-100">
                <div class="carousel-item active">
                    <a href="<?php echo htmlentities($product['image_path']); ?>">
                        <img src="<?php echo htmlentities($product['image_path']); ?>" class="d-block img-fluid" alt="Image">
                    </a>
                </div>


                <?php
                if (!empty($product['additional_images'])) {
                    $additional_images = explode(",", $product['additional_images']);
                    foreach ($additional_images as $index => $image) {
                        echo '<div class="carousel-item">
                                <a href="' . htmlentities($image) . '">
                                    <img src="' . htmlentities($image) . '" class="d-block img-fluid" alt="Image">
                                </a>
                              </div>';
                    }
                }
                ?>
            </div>

            <a class="carousel-control-prev" href="#productCarousel" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#productCarousel" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>

            <div class="carousel-thumbnails mt-3 d-flex justify-content-center">
                 <img src="<?php echo htmlentities($product['image_path']); ?>" data-target="#productCarousel" data-slide-to="0" class="img-thumbnail">
         
                <?php
                if (!empty($product['additional_images'])) {
                    $thumbnails = explode(",", $product['additional_images']);
                    foreach ($thumbnails as $index => $thumbnail) {
                        echo '<img src="' . htmlentities($thumbnail) . '" data-target="#productCarousel" data-slide-to="' . ($index + 1) . '" class="img-thumbnail">';
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <div class="col-6">
        <h2><?php echo $product['product_name']." by " .$product['brand_name'];?></h2>


        <?php

$sql_avg = "SELECT AVG(rating) AS average_rating FROM reviews WHERE product_id = ?";
$stmt_avg = $conn->prepare($sql_avg);
$stmt_avg->bind_param("i", $product_id);
$stmt_avg->execute();
$result_avg = $stmt_avg->get_result();
$row_avg = $result_avg->fetch_assoc();


$average_rating = round($row_avg['average_rating'], 1);  // Round to 1 decimal place

echo "<span class='star-rating'>";

for ($i = 1; $i <= 5; $i++) {
    if ($i <= $average_rating) {
        echo "<span>★</span>";  
    } else {
        echo "<span class='empty'>☆</span>";  
    }
}

// echo "</span> (" . $average_rating . " / 5)</p>";

$stmt_avg->close();



 ?>


        <hr>
        <h5 class="mt-4"><?php echo "Rs." . $product['price']?></h5>
        <h4 class="mt-4">Product Details</h4>
        <p class="mr-5 mb-3"><?php echo htmlentities($product['product_description']); ?></p>

        <form id="buyNowForm" action="" method="POST">
        <div class="quantity-box mt-4">
           <?php if ($stock): ?>
           <button type="button" class="btn text-white" id="btn-decrement" disabled>-</button>
           <input type="number" id="quantity" class="text-white" name="quantity" value="0" min="0" disabled>
           <button type="button" class="btn text-white" id="btn-increment" disabled>+</button>

            <div class="mt-0"><p style="color: red; font-weight: bold;">Out of stock</p><br></div>
          <?php else: ?>
          <button type="button" class="btn text-white" id="btn-decrement" >-</button>
           <input type="number" id="quantity" class="text-white" name="quantity" value="1" min="1">
           <button type="button" class="btn text-white" id="btn-increment">+</button>
          <?php endif; ?>
         </div>

        

        <div class="row d-flex justify-content-center mt-5">
          <?php if ($stock): ?>  
         <div class="col-8"><button type="submit" class="button text-white w-100" disabled>Add to cart</button></div>
         <div class="col-8"><button type="submit" class="button text-white w-100" disabled>Buy Now</button></div>
        
         <?php else: ?>  
        <div class="col-8"><button type="button" class="button text-white w-100" 
                     onclick="addToCart(<?php echo $product['product_id']; ?>, 
                          '<?php echo addslashes($product['product_name']); ?>', 
                          '<?php echo $product['image_path']; ?>')">
                              Add to Cart
           </button></div>
      
          <div class="col-8"><button type="submit" class="button text-white w-100 ">Buy Now</button></div>
         <?php endif; ?>
        </div>
        </form>
    </div>
</div>


 
<!-- Add to Cart Modal -->
<div id="addToCartModal" class="modal" tabindex="-1" role="dialog" style="display:none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content text-white" style="background-color:#885b96; border-radius:20px;">
            <div class="modal-header">
                <h5 class="modal-title">Product Added to Cart</h5>
                <button type="button" class="close" onclick="closeModal()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img id="modalImage" src="<?php echo $product['image_path']; ?>" alt="Product Image" style="width: 100px; height: auto; margin-bottom: 10px;">
                <p id="modalProductName"></p>
                <p>Product successfully added to your cart!</p>
            </div>
            <div class="modal-footer">
                <div class="col-12"> <button type="button" class="button text-white w-100" onclick="closeModal()">Continue Shopping</button></div>
                <div class="col-12"> <a href="cart.php"><button type="button" class="button text-white w-100">View Cart</button></a></div>
            </div>
        </div>
    </div>
</div>


<div class="accordion mt-5" id="reviewsAccordion">
    <div class="card">
        <div class="d-flex justify-content-center card-header" id="headingOne">
            <h2 class="mb-0">
                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                 <h4> Reviews <i class="fas fa-chevron-down"></i> </h4>  
                </button>   
            </h2>
        </div>

        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#reviewsAccordion">
            <div class="card-body">
                <?php

                // Fetch reviews for this product
                $review_sql = "
                SELECT 
                    r.rating, 
                    r.review_text, 
                    a.fname,
                    a.lname
                FROM 
                    reviews r
                LEFT JOIN 
                    accounts a 
                ON 
                    r.user_id = a.id
                WHERE 
                    r.product_id = ?
            ";

            
             
                $review_stmt = $conn->prepare($review_sql);
                $review_stmt->bind_param("i", $product_id);
                $review_stmt->execute();
                $review_result = $review_stmt->get_result();
                $reviews=[];
                if ($review_result->num_rows > 0) {
                    while ($review = mysqli_fetch_assoc($review_result)) {
                        $reviews[] = $review; 
                
                    }
                              
                    foreach ($reviews as $review) {
                  
                        $name = !empty($review['fname']) || !empty($review['lname']) 
                        ? $review['fname'] . ' ' . $review['lname'] 
                        : 'Guest';
                       
                        echo "<h6>" . htmlspecialchars($name) . "</h6>";
                         echo "<span class='star-rating'>";
                        for ($i = 1; $i <= 5; $i++) {
                            
                            echo ($i <= $review['rating']) ? "★" : "☆";
                        }
                        echo "</span>";
                        echo "<p>" . htmlspecialchars($review['review_text']) . "</p>";
                     echo "<hr>";
                    }}
                    else {
                        echo "No Reviews yet" ;
                    }
               
                ?>
            <div class="container">
             <h5 class="d-flex justify-content-center btn-link btn-block">
               Add Review
             </h5>
            </div>
    <form action="" method="POST">
        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_id); ?>">

        <div class="form-group">
            <label for="order_id">Order ID</label>
            <input type="number" class="form-control" id="order_id" name="order_id" required>
        </div>

        <div class="form-group">
            <label for="review_text">Review Text</label>
            <textarea class="form-control" id="review_text" name="review_text" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="rating">Rating</label><br>
            <div class="star-rating">
                <input type="radio" name="rating" id="star-5" value="5">
                <label for="star-5" class="star">&#9733;</label>

                <input type="radio" name="rating" id="star-4" value="4">
                <label for="star-4" class="star">&#9733;</label>

                <input type="radio" name="rating" id="star-3" value="3">
                <label for="star-3" class="star">&#9733;</label>

                <input type="radio" name="rating" id="star-2" value="2">
                <label for="star-2" class="star">&#9733;</label>

                <input type="radio" name="rating" id="star-1" value="1">
                <label for="star-1" class="star">&#9733;</label>
            </div>
        </div>

        <div class="d-flex justify-content-center w-100">
    <div class="col-4">
        <button type="submit" class="button text-white w-100">Submit Review</button>
    </div>
</div>   </form>

            </div>
        </div>
    </div>
</div>


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


</div>



<script>
    const thumbnails = document.querySelectorAll('.carousel-thumbnails img');
    thumbnails.forEach((thumbnail, index) => {
        thumbnail.addEventListener('click', () => {
            thumbnails.forEach(img => img.classList.remove('active'));
            thumbnail.classList.add('active');
        });
    });



    document.addEventListener('DOMContentLoaded', function () {
    const decrementButton = document.querySelector('#btn-decrement');
    const incrementButton = document.querySelector('#btn-increment');
    const quantityInput = document.getElementById('quantity');

    const maxQuantity = <?php echo $product['quantity']; ?>;

    if (!maxQuantity) {
        decrementButton.disabled = true;
        incrementButton.disabled = true;
    }

    decrementButton.addEventListener('click', function () {
        let currentValue = parseInt(quantityInput.value);
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
        }
    });

    incrementButton.addEventListener('click', function () {
        let currentValue = parseInt(quantityInput.value);
        if (currentValue < maxQuantity) {
            quantityInput.value = currentValue + 1;
        } else {
            alert('Maximum quantity available is ' + maxQuantity);
        }
    });

    quantityInput.addEventListener('input', function () {
        let currentValue = parseInt(quantityInput.value);
        if (currentValue > maxQuantity) {
            quantityInput.value = maxQuantity;
            alert('Maximum quantity available is ' + maxQuantity);
        }
    });
});
//Add to Cart

function addToCart(product_id, product_name, image_path) {
    // Make an AJAX request to add the product to the cart
    const quantity = document.getElementById('quantity').value;
    if (quantity <= 0) {
        alert('Please select a valid quantity');
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'cart.php', true); // Modify the URL as needed
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    // Send product details to the server
    xhr.send(`product_id=${product_id}&quantity=${quantity}`);

    xhr.onload = function() {
        if (xhr.status === 200) {
            // If the request was successful, show the modal
            document.getElementById('modalProductName').textContent = product_name;
            document.getElementById('addToCartModal').style.display = 'block';
        } else {
            alert('Failed to add to cart');
        }
    };
}

// Function to close the modal
function closeModal() {
    document.getElementById('addToCartModal').style.display = 'none';

}

</script>





<?php
require_once 'footer.php';
include 'javascriptlink.html';
?>
