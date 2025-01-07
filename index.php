<?php 
session_start();
require_once 'header.php';
require_once 'connection.php'; 

$vid_query="SELECT * from video LIMIT 1";
$vid_stmt = $conn->prepare($vid_query);
$vid_stmt->execute();
$vid_result = $vid_stmt->get_result();
if ($vid_result) {
    $vid = $vid_result->fetch_assoc();


}
//ads

$ads_query = "SELECT id, title, description, image_path, link, category_id FROM ads";
$ads_stmt = $conn->prepare($ads_query);
$ads_stmt->execute();
$ads_result = $ads_stmt->get_result();

$ads = [];
if ($ads_result) {
    while ($ad = $ads_result->fetch_assoc()) {
        $ads[] = $ad;
    }
}


//men
$men_sql=$product_query = "SELECT p.id, p.name AS product_name, p.price, p.image_path, b.name AS brand_name, c.name AS category_name
FROM product p 
INNER JOIN brand b ON p.brand_id = b.id 
INNER JOIN categories c ON c.id = p.category_id
WHERE p.category_id = 1
ORDER BY RAND()
LIMIT 6"
;

$men_stmt=$conn->prepare($men_sql);
$men_stmt->execute();
$men_result=$men_stmt->get_result();

$men=[];
if ($men_result) {
    while ($man =$men_result->fetch_assoc()) {
        $men[]=$man;
    }
}
//women
$women_sql=$product_query = "SELECT p.id, p.name AS product_name, p.price, p.image_path, b.name AS brand_name, c.name AS category_name
FROM product p 
INNER JOIN brand b ON p.brand_id = b.id 
INNER JOIN categories c ON c.id = p.category_id
WHERE p.category_id = 2
ORDER BY RAND()
LIMIT 6"
;

$women_stmt=$conn->prepare($women_sql);
$women_stmt->execute();
$women_result=$women_stmt->get_result();

$women=[];
if ($women_result) {
    while ($woman =$women_result->fetch_assoc()) {
        $women[]=$woman;
    }
}
//kids
$kid_sql=$product_query = "SELECT p.id, p.name AS product_name, p.price, p.image_path, b.name AS brand_name, c.name AS category_name
FROM product p 
INNER JOIN brand b ON p.brand_id = b.id 
INNER JOIN categories c ON c.id = p.category_id
WHERE p.category_id = 3
ORDER BY RAND()
LIMIT 6"
;

$kid_stmt=$conn->prepare($kid_sql);
$kid_stmt->execute();
$kid_result=$kid_stmt->get_result();

$kid=[];
if ($kid_result) {
    while ($kids =$kid_result->fetch_assoc()) {
        $kid[]=$kids;
    }
}

$random_sql = "SELECT 
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
LIMIT 8"; 


$random_stmt= $conn->prepare($random_sql);
$random_stmt->execute();
$random_result = $random_stmt->get_result();

$random_products = [];
if ($random_result) {
    while ($row = $random_result->fetch_assoc()) {
        $random_products[] = $row;
    }
}


// Fetch FAQs from the database
$query = "SELECT id, question, answer FROM faqs ORDER BY created_at DESC LIMIT 5";
$stmt=$conn->prepare($query);
$stmt->execute();
$result =$stmt->get_result();

$faqs = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $faqs[] = $row;
    }
}
?>

<style>


    body, html {
        margin: 0;
        padding: 0;
        overflow-x: hidden; 
    }
    .cat {
        border: none;
        width: 200px; 
        height: 200px; 
        margin: 20px; 
        border-radius: 50%; 
        overflow: hidden; 
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
        display: flex; 
        flex-direction: column; 
        align-items: center; 
        justify-content: center; 
    }

    .img {
        width: 100%;
        height: 100%;
        object-fit: cover; 
       }
    .img:hover {
       transform:  scale(1.1) !important; 
       transition: transform 0.3s ease-in-out;
    }
    .title {
        font-size: 16px;
        font-weight: bold;
        margin-top: 10px;
        text-align: center; 
    }

    .d-flex {
        justify-content: center; 
        flex-wrap: wrap;
    }

    h1 {
        margin-bottom: 30px;
        font-weight: bold;
    }


    .cardd {
    height: 100%;
    width: 100%; 
   }

.cardd-body {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
     width: 100%;
}

.cardd-img-top {
    height:300px;
    cursor: pointer; 
    object-fit: cover;
    max-height: 300px;
    width: 100%; 
}
.img, .cardd-img-top {
    transition: transform 0.3s ease;
}
.img:hover, .cardd-img-top:hover {
    transform: scale(1.1);
}

.ad{
    border:none;
    cursor: pointer;

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
.hr{
    background-color: #885b96;
}



.card-over {
  position: relative; /* Ensure the card content layers correctly */
}

.card-img-over {
  width: 100%;
  height: auto;
  object-fit: cover; /* Maintain aspect ratio */
}

.card-img-overlay {
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  padding: 15px;
  overflow: hidden; /* Hide overflow */
}

.card-title,
.card-text {
  word-wrap: break-word;
  overflow-wrap: break-word; /* Ensure text wraps and prevents overflow */
  margin-bottom: 15px;
}

@media (max-width: 576px) {
  .card-img-overlay {
    padding: 10px;
    bottom: 0;
    top: auto; /* Let the content expand as needed */
  }

  .card-text {
    font-size: 10px; /* Reduce text size on smaller screens */
  }
}

</style>

<div class="card mb-3">
    <video style="border:none;" class="card-img-top" src="<?php echo $vid['path'] ?>" autoplay loop muted></video>
</div>

<div class="container text-center my-5">
    <h1>Categories</h1>
    <div class="d-flex justify-content-center">
        <a href="men.php" class="text-decoration-none text-dark">
            <div class="cat">
                <img class="img" src="MyImages/men/Air.jpg" alt="Men's Perfumes">
            </div>
            <h5 class="title">Men</h5>
        </a>


        <a href="women.php" class="text-decoration-none text-dark">
            <div class="cat">
                <img class="img" src="MyImages/women/ALLURE.jpg" alt="Women's Perfumes">
            </div>
            <h5 class="title">Women</h5>
        </a>

      
        <a href="kid.php" class="text-decoration-none text-dark">
            <div class="cat">
                <img class="img" src="MyImages/kids/Starry.jpg" alt="Perfume Gift Sets">
            </div>

            <h5 class="title">Kids</h5>
        </a>
    </div>
</div>
<hr>
<!-- random -->


<h1 class="m-5">Perfumes From All Categories</h1>
<div class="container">
    <div class="row">
        <?php foreach ($random_products as $product): ?>
        <div class="col-6 col-md-3 mb-3 d-flex">
            <div class="cardd card d-flex flex-column" style="border:none;">
                <a href="productdetail.php?detail_id=<?php echo htmlspecialchars($product['product_id'])?>" >
                <img class="cardd-img-top card-img-top h-0" src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                </a>
                <div class="cardd-body card-body " >
                    <h5 class="card-title mb-0 h-0"><?php echo htmlspecialchars($product['product_name']." by ".$product['brand_name']); ?></h5>
                    <p class="card-text mb-0 h-0"><?php echo htmlspecialchars($product['category_name']); ?><br>Rs.<?php echo htmlspecialchars($product['price']); ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<hr>
<?php foreach ($ads as $ad1): ?>
<?php if ($ad1['id'] == "1"): ?>
    <a href="<?php echo $ad1['link']?>">
    <div class="card-over ad card bg-dark text-white">
  <img class="card-img-over card-img" src="<?php echo $ad1['image_path']?>" alt="Card image">
  <div class="card-img-overlay d-flex flex-column justify-content-end p-3">
    <h5 class="card-title"><?php echo $ad1['title']?></h5>
    <p class="card-text"><?php echo $ad1['description']?></p>
  </div>
</div>
</a>
<?php endif; ?>
<?php endforeach; ?>
<h1 class="m-3 "><?php echo htmlspecialchars($men[0]['category_name']); ?></h1>
<div class="row"> 
    <?php foreach ($men as $men_product): ?>
        <div class="col-6 col-md-2 mb-5 d-flex "> 
              <div class="cardd card d-flex flex-column" style="border:none;">
                <a href="productdetail.php?detail_id=<?php echo htmlspecialchars($men_product['id']); ?>">
                    <img class="cardd-img-top card-img-top custom-img" src="<?php echo htmlspecialchars($men_product['image_path']); ?>" alt="<?php echo htmlspecialchars($men_product['product_name']); ?>">
                </a>
                <div class="cardd-body card-body"> 
                 <p class="card-text mb-0 mt-0"><b><?php echo htmlspecialchars($men_product['product_name'] . " by " . $men_product['brand_name']); ?></b><br>Category:<?php echo htmlspecialchars($men_product['category_name']); ?> <br> Rs. <?php echo htmlspecialchars($men_product['price']); ?></p>
                       
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<hr>
<!-- female -->
<?php foreach ($ads as $ad1): ?>
<?php if ($ad1['id'] == "2"): ?>
    <a href="<?php echo $ad1['link']?>">
    <div class="card-over ad card bg-dark text-white">
  <img class="card-img-over card-img" src="<?php echo $ad1['image_path']?>" alt="Card image">
  <div class="card-img-overlay d-flex flex-column justify-content-end p-3">
    <h5 class="card-title"><?php echo $ad1['title']?></h5>
    <p class="card-text"><?php echo $ad1['description']?></p>
  </div>
</div>

</a>
<?php endif; ?>
<?php endforeach; ?>
<h1 class="m-3 "><?php echo htmlspecialchars($women[0]['category_name']); ?></h1>
<div class="row"> 
    <?php foreach ($women as $women_product): ?>
        <div class="col-6 col-md-2 mb-5 d-flex "> 
              <div class="cardd card d-flex flex-column" style="border:none;">
                <a href="productdetail.php?detail_id=<?php echo htmlspecialchars($women_product['id']); ?>">
                    <img class="cardd-img-top card-img-top custom-img" src="<?php echo htmlspecialchars($women_product['image_path']); ?>" alt="<?php echo htmlspecialchars($women_product['product_name']); ?>">
                </a>
                <div class="cardd-body card-body"> 
                 <p class="card-text mb-0 mt-0"><b><?php echo htmlspecialchars($women_product['product_name'] . " by " . $women_product['brand_name']); ?></b><br>Category:<?php echo htmlspecialchars($women_product['category_name']); ?> <br> Rs. <?php echo htmlspecialchars($men_product['price']); ?></p>
                       
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<hr>
<!-- kids -->

<?php foreach ($ads as $ad1): ?>
<?php if ($ad1['id'] == "3"): ?>
    <a href="<?php echo $ad1['link']?>">
    <div class="card-over ad card bg-dark text-white">
  <img class="card-img-over card-img" src="<?php echo $ad1['image_path']?>" alt="Card image">
  <div class="card-img-overlay d-flex flex-column justify-content-end p-3">
    <h5 class="card-title"><?php echo $ad1['title']?></h5>
    <p class="card-text"><?php echo $ad1['description']?></p>
  </div>
</div>
</a>
<?php endif; ?>
<?php endforeach; ?>

<h1 class="m-3 "><?php echo htmlspecialchars($kid[0]['category_name']); ?></h1>
<div class="row"> 
    <?php foreach ($kid as $kid_product): ?>
        <div class="col-6 col-md-2 mb-5 d-flex "> 
              <div class="cardd card d-flex flex-column" style="border:none;">
                <a href="productdetail.php?detail_id=<?php echo htmlspecialchars($kid_product['id']); ?>">
                    <img class="cardd-img-top card-img-top custom-img" src="<?php echo htmlspecialchars($kid_product['image_path']); ?>" alt="<?php echo htmlspecialchars($kid_product['product_name']); ?>">
                </a>
                <div class="cardd-body card-body"> 
                 <p class="card-text mb-0 mt-0"><b><?php echo htmlspecialchars($kid_product['product_name'] . " by " . $kid_product['brand_name']); ?></b><br>Category:<?php echo htmlspecialchars($kid_product['category_name']); ?> <br> Rs. <?php echo htmlspecialchars($men_product['price']); ?></p>
                       
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>



<!-- //FAQS -->
<div class="container mt-5">
     <div class="d-flex justify-content-center">
         <b><h1 class="mb-4">Frequently Asked Questions</h1></b>
     </div>
</div>
<div class="container">
<div class="d-flex justify-content-center">
    <div class="mt-5 accordion text-center w-50 mb-5" id="faqAccordion">
        <?php foreach ($faqs as $index => $faq): ?>
            <div class="accordion-item">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $faq['id']; ?>" aria-expanded="true" aria-controls="collapse<?php echo $faq['id']; ?>" style="border: none; background: none;">
                 <p><?php echo htmlspecialchars($faq['question']); ?> <i class="fas fa-chevron-down"></i></p>
              </button>
                   
                <div id="collapse<?php echo $faq['id']; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $faq['id']; ?>" data-bs-parent="#faqAccordion">
                    <div class="accordion-body mb-5">
                      <?php echo nl2br(htmlspecialchars($faq['answer'])); ?>
                    </div>
                </div>
               
               <div class="hr"><hr></div> 
            </div>
        <?php endforeach; ?>
    </div>
</div>
</div>
<?php
require_once 'footer.php';
include 'javascriptlink.html';
?>
