<?php
include 'misc.php';

$sessionId = $_COOKIE["sessionId"];
$input = json_decode(file_get_contents('php://input'), true);

$dest = $input['destination'];
$currentDir = $input['work_directory'];
$thumbs = $input['files'];
$dirs = $input['folders'];

$conn = connectDatabase($db_config);
$userId = getUserId($conn, $sessionId);


//	Updates a path files and folders in work directory

foreach ($thumbs as $thumb) {
	$sql = "UPDATE viewers 
		SET path = :path 
		WHERE user_id = :user_id 
		AND hash = :thumb";	
	$params = array(':user_id' => $userId, ':path' => $dest,
					':thumb' => $thumb);
	$stmt = $conn->prepare($sql);
	$stmt->execute($params);
	
}

foreach ($dirs as $dir) {
	$sql1 =  "UPDATE folders 
	SET path = :destination 
	WHERE user_id = :user_id
	AND path = :actual 
	AND  folder_name = :name";
	$params = array(':user_id' => $userId, ':destination' => $dest,
					':actual' => $currentDir, ':name' => $dir);
	$stmt1 = $conn->prepare($sql1);
	$stmt1->execute($params);

}

//	Updates a path files and folders in subdirectories 

foreach ($dirs as $dir) {
	$path = $currentDir.$dir.'/';
	$params = array(':user_id' => $userId, ':path' => "$path%");

	$sql2 = "SELECT path
		FROM folders 
		WHERE user_id = :user_id AND path LIKE :path"; 

	$stmt2 = $conn->prepare($sql2);
	$stmt2->execute($params);
	$result2 = $stmt2->fetch(PDO::FETCH_ASSOC);  
	
	while($result2) {
		$originalPath = $result2['path'];
		$newPath = replacePath($currentDir, $dest, $originalPath);
		$sql3 = "UPDATE folders 
				SET path = '$newPath' 
				WHERE user_id = $userId 
				AND path = '$originalPath'";
		$conn->exec($sql3);
	
		$result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
	}
	
	$sql4 = "SELECT path
		FROM viewers 
		WHERE user_id = :user_id AND path LIKE :path" ; 

	$stmt3 = $conn->prepare($sql4);
	$stmt3->execute($params);
	$result3 = $stmt3->fetch(PDO::FETCH_ASSOC);  
			
	while($result3) {
		$originalPath = $result3['path'];
		$newPath = replacePath($currentDir, $dest, $originalPath);
		$sql5 = "UPDATE viewers 
			SET path = '$newPath' 
			WHERE user_id = $userId 
			AND path = '$originalPath'";
		$conn->exec($sql5);
	
		$result3 = $stmt3->fetch(PDO::FETCH_ASSOC);
	}

}

function replacePath($current, $destination, $original) {
	$index = strlen($current);
	return $destination.substr($original, $index);
}


$conn = null;
?>
