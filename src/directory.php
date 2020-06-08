<?php

include 'misc.php';

$sessionId = $_COOKIE["sessionId"];
$currentDir = $_POST['work_directory'];
$newFolderName = $_POST['new_directory'];

$conn = connectDatabase($db_config);
$userId = getUserId($conn, $sessionId);

/*	Create directory	*/

$stmt = $conn->prepare("INSERT INTO folders
	(user_id, path, folder_name)
	VALUES
	( :userId, :path, :name)"
);

$stmt->bindParam(':userId', $userId);
$stmt->bindParam(':path', $currentDir);
$stmt->bindParam(':name', $newFolderName);

if($stmt->execute()) {
	$userData['folders'] = [
		array('folder_name' => $newFolderName, 'path' => $currentDir)
		];
		
	echo json_encode($userData);
} else {
	//TODO: fault create folder
}

$conn = null;
?>
