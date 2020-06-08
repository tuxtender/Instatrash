<?php
include 'misc.php';

$conn = connectDatabase($db_config);

if($argc != 3) {
	echo "Use: [username] [password] ";
	exit;
}

$user = $argv[1];
$psw = $argv[2];

$hashed = password_hash($psw, PASSWORD_DEFAULT);


$sql = "INSERT INTO user_info (nickname) VALUES	('$user')";
$conn->exec($sql);


$sql1 = "INSERT INTO access (login, password) VALUES ( '$user', '$hashed')";
$conn->exec($sql1);


?>
