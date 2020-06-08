<?php
include 'misc.php';

$sessionId = $_COOKIE["sessionId"];
$thumb = $_GET["i"];

$conn = connectDatabase($db_config);
$userId = getUserId($conn, $sessionId);

$stmt = $conn->prepare("SELECT i.url, f.name, f.size, f.type
						FROM image i
						NATURAL JOIN file_info f
						WHERE hash = :hash"
); 
$stmt->bindParam(':hash', $thumb);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC); 
$file = $result['url'];
$type = $result['type'];
$size = $result['size'];

header('Content-Type:'.$type);
header('Content-Length: ' . $size);

readfile($file);


?>
