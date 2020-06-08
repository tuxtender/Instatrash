<?php

include 'misc.php';
include 'image.php';

$sessionId = $_COOKIE["sessionId"];
$thumb = $_GET["i"];

$conn = connectDatabase($db_config);

$stmt = $conn->prepare("SELECT url FROM image WHERE hash = :hash" ); 
$stmt->bindParam(':hash', $thumb);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC); 
$file = $result['url'];

rescaleImage($file);

?>
