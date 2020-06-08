<?php

include 'misc.php';

$sessionId = $_COOKIE["sessionId"];
$login = $_POST['login'];
$psw = $_POST['password'];

$conn = connectDatabase($db_config);

//	First login via form
if (!isset($sessionId)) {

	$stmt = $conn->prepare("SELECT user_id, password
							FROM access 
							WHERE login = :login"); 
							
	$stmt->bindParam(':login', $login);
	$stmt->execute();
	// set the resulting array to associative
	$result = $stmt->fetch(PDO::FETCH_ASSOC);  


	if ($result && password_verify($psw, $result['password']) ) {
		$userId = $result['user_id'];
		$today = date("Y-m-d", time() );
		$sessionId = md5(strval(time()) );
		$expire = date("Y-m-d", time() + (7 * 24 * 60 * 60) ); //7 days
		
		//Remove expired record session
		$sql = "DELETE FROM sessions 
			WHERE user_id = $userId AND expire_date < '$today'";
		$conn->exec($sql);
			
		$sql = "INSERT INTO sessions (session_id, user_id, expire_date)
		VALUES ('$sessionId', $userId, '$expire')";
		$conn->exec($sql);
		
		setcookie("sessionId", $sessionId, time() + (7 * 24 * 60 * 60), '/'); 
		
		
		
	} else {
		//	Access denied
		echo http_response_code(401);
		$msg = "Access refused. Attemp to login $login";
		error_log('['.date(DATE_RFC2822).']'.$msg."\n", 3,  $log_file); 
	}
	
} 

//Get info about user
$userId = getUserId($conn, $sessionId);


$stmt = $conn->prepare("SELECT user_id, nickname,
	email, storage_limit, used
	FROM user_info
	WHERE user_id = :user_id"
); 
	
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);  


$userData['user'] = [ 
	'nickname' => $result['nickname'],
	'email' => $result['email'],
	'storage' => $result['storage_limit'],
	'used' => $result['used']
];

echo json_encode($userData);

$conn = null;

?>
