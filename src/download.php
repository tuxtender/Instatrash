<?php

include 'misc.php';

$sessionId = $_COOKIE["sessionId"];
$input = json_decode(file_get_contents('php://input'), true);
$currentDir = $input['work_directory'];
$thumbs = $input['files'];
$dirs = $input['folders'];

$tmp = sys_get_temp_dir();
$zipname = $tmp.DIRECTORY_SEPARATOR.uniqid();

$conn = connectDatabase($db_config);
$userId = getUserId($conn, $sessionId);



$stmt = $conn->prepare("SELECT url FROM image WHERE hash = :hash"); 
$stmt->bindParam(':hash', $thumb);

$zip = new ZipArchive;
$zip->open($zipname, ZipArchive::CREATE);

/*	TODO: Remove contained folders within archive	*/

foreach ($thumbs as $thumb) {
	
	$stmt->execute();
	$result = $stmt->fetch(PDO::FETCH_ASSOC);  
	$file = $result['url'];
	$zip->addFile($file);
}

/*	Gathering all files from folders selected	*/

foreach ($dirs as $dir) {
	$path = $currentDir.$dir.'/';
			
	$sql1 = "SELECT i.url
		FROM viewers v
		NATURAL JOIN image i
		WHERE v.user_id = :user_id AND v.path LIKE :path";
		
	$params = array(':user_id' => $userId, ':path' => "$path%");
	$stmt1 = $conn->prepare($sql1);
	$stmt1->execute($params);
	$result1 = $stmt1->fetch(PDO::FETCH_ASSOC);  
				
	while ($result1) {
		$zip->addFile($result1['url']);
		$result1 = $stmt1->fetch(PDO::FETCH_ASSOC);
	}
	
}

$zip->close();

header('Content-Type: application/zip');
header('Content-disposition: attachment; filename='.$zipname);
header('Content-Length: '.filesize($zipname));

//Remove temp zip file on success or cancel download
ignore_user_abort(true);
readfile($zipname);
unlink($zipname);

?>
