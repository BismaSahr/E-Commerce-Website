<?php
session_start();
include 'header.php';
include 'connection.php'; 

// Fetch FAQs from the database
$query = "SELECT id, question, answer FROM faqs ORDER BY created_at DESC";
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
#faqAccordion{
      border:1px solid;
      border-color: #885b96;
      padding:50px;
      
}

</style>
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
