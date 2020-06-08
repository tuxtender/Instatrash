<?php

include 'misc.php';

$sessionId = $_COOKIE["sessionId"];
$path = $_POST['path'];

$conn = connectDatabase($db_config);
$userId = getUserId($conn, $sessionId);

//	Get a files and meta data

$stmt = $conn->prepare("SELECT v.user_id, v.hash, v.path, i.url,
	f.owner, f.name, f.size,f.type, f.date_upload, 
	f.date_create 
	FROM viewers AS v 
	NATURAL JOIN image AS i 
	NATURAL JOIN file_info AS f 
	WHERE v.user_id = :user_id AND v.path = :path"
);

$stmt->bindParam(':user_id', $userId);
$stmt->bindParam(':path', $path);

$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);  

//	Get a folders

$stmt2 = $conn->prepare("SELECT path, folder_name
	FROM folders
	WHERE user_id = :user_id AND path = :path"
); 
$stmt2->bindParam(':user_id', $userId);
$stmt2->bindParam(':path', $path);
$stmt2->execute();
$result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);  
	
$list = [];
	
while ($result) {

	$list[] =[ 
		'thumb' => $result['hash'],
		'owner' => $result['owner'],
		'name' => $result['name'], 
		'size' => $result['size'],
		'type' => $result['type'],
		'upload' => $result['date_upload'],
		'create' => $result['date_create'],
	];
		
	$result = $stmt->fetch(PDO::FETCH_ASSOC);

}

$userData['files'] = $list;
$userData['folders'] = $result2;

echo json_encode($userData);

$conn = null;


?>
