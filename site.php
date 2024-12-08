<?php
$sql = "SELECT * FROM site_info WHERE id = 1 LIMIT 1";
$result = $conn->query($sql);

$logoPath = ''; 

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $logoPath = $row['logo_path']; 
    $site_name = $row['site_name']; 
}

?>