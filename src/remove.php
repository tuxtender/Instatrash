<?php

include 'misc.php';

$sessionId = $_COOKIE["sessionId"];
$input = json_decode(file_get_contents('php://input'), true);
$thumbs = $input['files'];
$dirs = $input['folders'];

$conn = connectDatabase($db_config);
$userId = getUserId($conn, $sessionId);


foreach ($thumbs as $thumb) {
	//	Delete from the viewers table  directly selected files
	$sql = "DELETE FROM viewers WHERE user_id = :user_id AND hash = :thumb";
	$stmt = $conn->prepare($sql);
	$params = array(':user_id' => $userId, ':thumb' => $thumb);
	$stmt->execute($params);
				
}

//	 Nested path processing

foreach ($dirs as $dir) {
	//	The viewers table
	$sql1 = "DELETE FROM viewers WHERE user_id = :user_id AND path LIKE :dir";
	$params = array(':user_id' => $userId, ':dir' => "$dir%");
	$stmt1 = $conn->prepare($sql1);
	$stmt1->execute($params);

	// The folders table
	$sql2 = "DELETE FROM folders WHERE user_id = :user_id AND path LIKE :dir";
	$params = array(':user_id' => $userId, ':dir' => "$dir%");
	$stmt2 = $conn->prepare($sql2);
	$stmt2->execute($params);

	//	Remove folders directly selected in work directory
	$index = strrpos($dir, '/', -2);
	$directory = substr($dir, 0, $index + 1);
	$folder = substr($dir, $index+1, -1);

	$sql3 = "DELETE FROM folders
		WHERE user_id = :user_id 
		AND path = :directory 
		AND folder_name = :folder";
		
	$stmt3 = $conn->prepare($sql3); 
	$stmt3->bindParam(':user_id', $userId);
	$stmt3->bindParam(':directory', $directory);
	$stmt3->bindParam(':folder', $folder);
	$stmt3->execute();
}



//	Remove the image  records there aren't in the viewers  anymore 
//	then cascade deleted the file_info and the comments records
$sql5 = "SELECT hash, url FROM image
		WHERE NOT EXISTS
		(SELECT * FROM viewers WHERE viewers.hash = image.hash)";

foreach ($conn->query($sql5) as $row) {
    unlink($row['url']);	//Remove original file
    $thumb = $row['hash'];
    unlink($THUMB_PATH.DIRECTORY_SEPARATOR.$thumb);//Remove thumbnail

    $sql6 = "DELETE FROM image WHERE hash = '$thumb'";
    $conn->exec($sql6);
    
}

//	Remove the comment records there aren't in the comments anymore
$sql4 = "DELETE FROM comment
		WHERE NOT EXISTS
		(SELECT * FROM comments 
		WHERE comments.comment_id = comment.comment_id)";
			
$conn->exec($sql4);


$conn = null;
?>
