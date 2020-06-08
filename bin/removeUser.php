<?php
include 'misc.php';

$conn = connectDatabase($db_config);

if($argc != 2) {
	echo "Use: [user id] ";
	exit;
}

$userId = $argv[1];


$sql = "DELETE FROM user_info WHERE user_id = '$userId'";
$conn->exec($sql);


?>
