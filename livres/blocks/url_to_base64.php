<?php
if (isset($_GET['url'])) {
    $url = $_GET['url'];
    $imageData = file_get_contents($url);
    $base64 = base64_encode($imageData);
    echo 'data:image/jpeg;base64,' . $base64;
}
?>
