<?php

include '../config/db.php';

$log_file = "../errors.log"; 

date_default_timezone_set('Asia/Yekaterinburg');

function connectDatabase($config , $log_file = "../errors.log") {
	$now = date("Y-m-d", time() );

	try {
		$dbtype = $config['DB_TYPE'];
		$servername = $config['DB_HOST'];
		$dbname = $config['DB_DATABASE'];
		$username = $config['DB_USERNAME'];
		$password = $config['DB_PASSWORD'];
		
		$conn = new PDO("$dbtype:host=$servername;dbname=$dbname", $username, $password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


	} catch(PDOException $e) {
		$msg = $e->getMessage();
		error_log('['.date(DATE_RFC2822).']'.$msg."\n", 3,  $log_file); 
	}

	return $conn;
}


function getUserId($conn, $sessionId) {
	$stmt = $conn->prepare("SELECT user_id, expire_date
		FROM sessions
		WHERE session_id = :session_id"
	); 
	$stmt->bindParam(':session_id', $sessionId);
	$stmt->execute();
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($result) {
		return $result['user_id'];

	} else {
		setcookie("sessionId", $sessionId, time() - 3600, '/'); 

		echo http_response_code(401);
		exit;
	}
	
	
}

?>
