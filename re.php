<?php
session_start();
require_once 'header.php';
require_once 'connection.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $review_text = $_POST['review_text'];
    $rating = $_POST['rating'];
    $product_id = $_POST['product_id'];
    $order_id = $_POST['order_id'];
    $user_id = $_POST['user_id'];

    // Check if required fields are set
    if (empty($review_text) || empty($rating)) {
        echo "Please fill in both the review and rating!";
        exit();
    }

    // Insert review into database
    $sql = "INSERT INTO reviews (user_id, order_id, product_id, review_text, rating)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisii", $user_id, $order_id, $product_id, $review_text, $rating);

    if ($stmt->execute()) {
        echo "Review submitted successfully!";
    } else {
        echo "Error submitting review.";
    }
    $stmt->close();
    $conn->close();
}
                    ?>

                    <form action="" method="POST">
                        <textarea name="review_text" placeholder="Write your review..."></textarea><br><br>

                        <!-- Star rating system -->
                        <label for="rating">Rating:</label>
                        <input type="radio" name="rating" value="1"> 1
                        <input type="radio" name="rating" value="2"> 2
                        <input type="radio" name="rating" value="3"> 3
                        <input type="radio" name="rating" value="4"> 4
                        <input type="radio" name="rating" value="5"> 5 <br><br>

                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">  <!-- Dynamic product ID -->
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">  <!-- Dynamic order ID -->

                        <?php if (isset($_SESSION['user_id'])): ?>
                            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                        <?php else: ?>
                            <input type="hidden" name="user_id" value="NULL">  <!-- No user ID for guest -->
                        <?php endif; ?>

                        <button type="submit">Submit Review</button>
                    </form>

<head>
    <style>
        .star-rating {
            font-size: 20px;
            color: #320B56;
        }

        .star-rating .empty {
            color: gray;
        }

        .star-rating span {
            font-size: 18px;
        }
    </style>
</head>

<?php

$sql = "SELECT * FROM reviews WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    
    echo "<div class='review'>";
    

    echo "<p><strong>" . ($row['user_id'] ? 'User' : 'Guest') . "</strong></p>"; 
    
  
    echo "<p>" . htmlspecialchars($row['review_text']) . "</p>";

    echo "<span class='star-rating'>";
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $row['rating']) {
            echo "<span>★</span>";  
        } else {
            echo "<span class='empty'>☆</span>"; 
        }
    }
    echo "</span>";
    

    $formatted_date = date("F j, Y", strtotime($row['created_at']));
    echo "<p><em>Submitted on: " . $formatted_date . "</em></p>";
    
    echo "</div>";
}

$stmt->close();
?>























<div class="accordion mt-5" id="reviewsAccordion">
    <div class="card">
        <div class="d-flex justify-content-center card-header" id="headingOne">
            <h2 class="mb-0">
                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    Add Review <i class="fas fa-chevron-down"></i>  
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
                if ($review_result) {
                    while ($review = mysqli_fetch_assoc($review_result)) {
                        $reviews[] = $review; 
                
                    }
                } else {
                    echo "Error in query execution: " . mysqli_error($conn);
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
                    }
               
                ?>
            </div>
        </div>
    </div>
</div>











<?php
$product_id = 20;

// Fetch the average rating from the database
$sql_avg = "SELECT AVG(rating) AS average_rating FROM reviews WHERE product_id = ?";
$stmt_avg = $conn->prepare($sql_avg);
$stmt_avg->bind_param("i", $product_id);
$stmt_avg->execute();
$result_avg = $stmt_avg->get_result();
$row_avg = $result_avg->fetch_assoc();

// Display the average rating as stars
$average_rating = round($row_avg['average_rating'], 1);  // Round to 1 decimal place
echo "<p><strong>Average Rating: </strong>";
echo "<span class='star-rating'>";

// Display stars for the average rating
for ($i = 1; $i <= 5; $i++) {
    if ($i <= $average_rating) {
        echo "<span>★</span>";  // Filled star
    } else {
        echo "<span class='empty'>☆</span>";  // Empty star
    }
}

echo "</span> (" . $average_rating . " / 5)</p>";

$stmt_avg->close();

// Fetch individual reviews for the product
$sql = "SELECT * FROM reviews WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "<div class='review'>";
    
    // Display the rating for each review as stars
    echo "<span class='star-rating'>";
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $row['rating']) {
            echo "<span>★</span>";  // Filled star
        } else {
            echo "<span class='empty'>☆</span>";  // Empty star
        }
    }
    echo "</span>";
    
    echo "</div>";
}

$stmt->close();
?>
















<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Reviews</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Product Reviews</h2>

    <!-- Review Form -->
    <form action="#" method="POST" class="mb-4">
        <div class="mb-3">
            <label for="reviewerName" class="form-label">Your Name</label>
            <input type="text" class="form-control" id="reviewerName" name="reviewerName" required>
        </div>
        <div class="mb-3">
            <label for="reviewText" class="form-label">Your Review</label>
            <textarea class="form-control" id="reviewText" name="reviewText" rows="4" required></textarea>
        </div>
        <div class="mb-3">
            <label for="rating" class="form-label">Rating</label>
            <select class="form-select" id="rating" name="rating" required>
                <option value="1">1 - Poor</option>
                <option value="2">2 - Fair</option>
                <option value="3">3 - Good</option>
                <option value="4">4 - Very Good</option>
                <option value="5">5 - Excellent</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Submit Review</button>
    </form>

    <!-- Collapsible Customer Reviews Section -->
    <div>
        <h3>
            <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#reviewsSection" aria-expanded="false" aria-controls="reviewsSection">
                Customer Reviews
            </button>
        </h3>
        <div class="collapse" id="reviewsSection">
            <!-- Display Reviews -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">John Doe</h5>
                    <p class="card-text">This product is amazing! I love it.</p>
                    <p><strong>Rating:</strong> 5/5</p>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Jane Smith</h5>
                    <p class="card-text">Good quality, but it could be improved in terms of durability.</p>
                    <p><strong>Rating:</strong> 4/5</p>
                </div>
            </div>

            <!-- You can add more reviews here dynamically -->
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>









!-- Accordion for Reviews -->
<div class="accordion mt-5" id="reviewsAccordion">
    <div class="card">
        <div class="d-flex justify-content-center card-header" id="headingOne">
            <h2 class="mb-0">
                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                   Add Reviews <i class="fas fa-chevron-down"></i>  
                </button>
            </h2>
        </div>

        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#reviewsAccordion">
            <div class="card-body">
      
            <form action="" method="POST">
                        <textarea name="review_text" placeholder="Write your review..."></textarea><br><br>

                        <!-- Star rating system -->
                        <label for="rating">Rating:</label>
                        <input type="radio" name="rating" value="1"> 1
                        <input type="radio" name="rating" value="2"> 2
                        <input type="radio" name="rating" value="3"> 3
                        <input type="radio" name="rating" value="4"> 4
                        <input type="radio" name="rating" value="5"> 5 <br><br>

                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">  <!-- Dynamic product ID -->
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">  <!-- Dynamic order ID -->

                        <?php if (isset($_SESSION['user_id'])): ?>
                            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                        <?php else: ?>
                            <input type="hidden" name="user_id" value="NULL">  <!-- No user ID for guest -->
                        <?php endif; ?>

                        <button type="submit">Submit Review</button>
                    </form>
            </div>
        </div>
    </div>
</div>













<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart Button</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .cart-button {
            position: relative;
            display: inline-block;
        }

        .badge {
            position: absolute;
            top: -5px;
            right: -10px;
            background-color: #ff0000;
            color: #fff;
            padding: 5px 7px;
            border-radius: 50%;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <button type="button" class="btn text-white cart-button">
        <i class="fas fa-cart-shopping"></i>
        <span class="badge">4</span>
    </button>
</body>
</html>





<div class="accordion" id="accordionExample">
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingOne">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
        Accordion Item #1
      </button>
    </h2>
    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
      <div class="accordion-body">
        <strong>This is the first item's accordion body.</strong> It is shown by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
      </div>
    </div>
  </div>
 </div>  


 <style>
    .star-rating {
  display: flex;
  direction: row-reverse;
}

input[type="radio"] {
  display: none;
}

.star {
  font-size: 40px;
  color: #ccc;
  cursor: pointer;
  transition: color 0.3s;
}

input[type="radio"]:checked ~ .star {
  color: #FFD700; /* Gold color when checked */
}

input[type="radio"]:hover ~ .star,
.star:hover {
  color: #FFD700; /* Highlight color when hovering over stars */
}

 </style>
 <form action="submit_review.php" method="POST">
  <div class="form-group">
    <label for="user_id">User ID</label>
    <input type="number" class="form-control" id="user_id" name="user_id" required>
  </div>
  
  <div class="form-group">
    <label for="order_id">Order ID</label>
    <input type="number" class="form-control" id="order_id" name="order_id" required>
  </div>

  <div class="form-group">
    <label for="product_id">Product ID</label>
    <input type="number" class="form-control" id="product_id" name="product_id" required>
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

  <button type="submit" class="btn btn-primary">Submit Review</button>
</form>











<p>
  <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
    Link with href
  </a>
  <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
    Button with data-target
  </button>
</p>
<div class="collapse" id="collapseExample">
  <div class="card card-body">
    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident.
  </div>
</div>