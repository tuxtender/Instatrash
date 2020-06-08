<?php

include 'misc.php';
include 'image.php';

$sessionId = $_COOKIE["sessionId"];
$wd = $_POST['work_directory'];

$conn = connectDatabase($db_config);


//	Get data from user_info table	
//	Disk quota manage
$stmt = $conn->prepare("SELECT u.user_id, u.nickname,
						u.storage_limit, u.used
						FROM user_info AS u
						NATURAL JOIN sessions AS s
						WHERE s.session_id = :session_id"
); 
$stmt->bindParam(':session_id', $sessionId);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC); 
$userName = $result['nickname']; 
$userId = $result['user_id'];
$limitMemory = $result['storage_limit'];
$usedMemory = $result['used'];


//	Fill a image table	
//	Users haven't access to original files
$stmt1 = $conn->prepare("INSERT INTO image (hash, url)
						VALUES (:hash, :url)"
); 
$stmt1->bindParam(':hash', $hash);
$stmt1->bindParam(':url', $target);


//	Fill a viewers table
$stmt2 = $conn->prepare("INSERT INTO viewers (hash, user_id, path)
						 VALUES	(:hash, :user_id, :path)" ); 
// Default viewer of images is owner (who upoads files)
$stmt2->bindParam(':hash', $hash);
$stmt2->bindParam(':user_id', $userId);		
$stmt2->bindParam(':path', $wd);

//	Fill a file_info table
$stmt3 = $conn->prepare("INSERT INTO file_info
						(hash, owner, name, size,
							type, date_upload, date_create)
						VALUES	
						(:hash, :user_id, :name, :size,
							:type, :date_upload, :date_create)"
); 

$stmt3->bindParam(':hash', $hash);
$stmt3->bindParam(':user_id', $userId);		
$stmt3->bindParam(':name', $name);
$stmt3->bindParam(':size', $size);
$stmt3->bindParam(':type', $mimeType);		
$stmt3->bindParam(':date_upload', $dateUpload);
$stmt3->bindParam(':date_create', $dateCreate);


function isFreeDiskSpace() {
	//Is storage space exhausted
	$fileSize = $_FILES["file"]['size'];
	if ( ($usedMemory + $fileSize) > $limitMemory) {
		//"Out of storage limit
		echo http_response_code(507);
		exit;
	} 
	return true;

}				 

/* Single upload	*/
$error = $_FILES["file"]["error"];

if ($error == UPLOAD_ERR_OK ) {
	
	$name = basename($_FILES["file"]["name"]);	//file_info.name
	$size = $_FILES['file']['size'];
	$mimeType = $_FILES['file']['type'];
	$dateUpload = date("Y-m-d H:i:s");	// the MySQL DATETIME format
	//	TODO: Retrieve a date creation
	$dateCreate = null;
	
	
	$tmp = $_FILES["file"]["tmp_name"];
	$target = $ORIGINAL_PATH.DIRECTORY_SEPARATOR.$name;
	move_uploaded_file($tmp, $target);
		
	//Uniq full path name a thumbnail 
	$hash = hash_file('crc32', $target); 
	$dest = $THUMB_PATH.DIRECTORY_SEPARATOR.$hash;
	
	/*	Image downscale, but a files make recognition pic	*/
	rescaleImage($target, 300, $dest);
	$info = 'n/a'; // Exif info
		
	/*	Output new data at success	*/
	if($stmt1->execute() and $stmt2->execute() and $stmt3->execute()) {
		$data[] = [ 
			'thumb' => $hash,
			'owner' => $userId,
			'name' => $name, 
			'size' => $size,
			'type' => $mimeType,
			'upload' => $dateUpload,
			'create' => $dateCreate
		];
		
		echo json_encode($data);

	}
	
} else {
	echo http_response_code(500);
	exit;
}

$conn = null;

?>


