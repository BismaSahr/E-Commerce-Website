<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $review_text = $_POST['review_text'];
    $rating = $_POST['rating'];
    $user_id = $_SESSION['user_id'];
    $created_at = date('Y-m-d H:i:s'); // Current timestamp

    // Insert review into database
    $query = "INSERT INTO reviews (user_id, order_id, product_id, review_text, rating, created_at) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiisis", $user_id, $order_id, $product_id, $review_text, $rating, $created_at);

    if ($stmt->execute()) {
        echo "Review submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>