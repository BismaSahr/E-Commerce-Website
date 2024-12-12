<?php
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


//Recomendation

$related_products_sql = "SELECT 
    p.id AS product_id,
    p.name AS product_name,
    p.image_path,
    p.price,
    b.name AS brand_name
FROM 
    product p
INNER JOIN 
    brand b ON p.brand_id = b.id
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















.card-img-top {
    transition: transform 0.3s ease-in-out;
    cursor: pointer; 
    object-fit: cover;
    height: 300px; 
    width: 100%;
}

/* Card appearance */
.custom-bg {
    background-color: #320B56; 
    border: none; 
    border-radius: 8px;
}

.card {
    height: 100%; 
    width: 100%; 
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}


.card-body {
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

    .card-img-top {
        height: 150px; 
    }

    .card-body {
        height: 130px;
    }
}

@media (max-width: 576px) {
    .col-md-2 {
        flex: 0 0 100%;
        max-width: 100%;
    }

    .card-img-top {
        height: 120px; 
    }

    .card-body {
        height: auto; 
    }
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
         <div class="col-8"><button type="submit" class="button text-white w-100 ">Add to cart</button></div>
         <div class="col-8"><button type="submit" class="button text-white w-100 ">Buy Now</button></div>
         <?php endif; ?>
        </div>
        </form>
    </div>
</div>
   <h2 class="mt-4 ml-3">You may also like!</h2>
   <div class="row no-gutters"> 
    <?php foreach ($related_products as $related_product): ?>
        <div class="col-12 col-md-2 mb-5 d-flex"> 
            <div class="card custom-bg text-white d-flex flex-column">
                <a href="productdetail.php?detail_id=<?php echo htmlspecialchars($related_product['product_id']); ?>">
                    <img class="card-img-top custom-img" src="<?php echo htmlspecialchars($related_product['image_path']); ?>" alt="<?php echo htmlspecialchars($related_product['product_name']); ?>">
                </a>
                <div class="card-body custom-card-body">
                    <h5 class="card-title text-truncate"><?php echo htmlspecialchars($related_product['product_name'] . " by " . $related_product['brand_name']); ?></h5>
                    <p class="card-text font-weight-bold mb-0">Rs.<?php echo htmlspecialchars($related_product['price']); ?></p>
                    <button type="submit" class="button text-white mt-2">Add to cart</button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
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

</script>

<?php
require_once 'footer.php';
include 'javascriptlink.html';
?>
