<?php

include 'misc.php';

$sessionId = $_COOKIE["sessionId"];
$thumb = $_POST["thumb"];
$commentText = $_POST["comment"];

$conn = connectDatabase($db_config);
$today = date("Y-m-d H:i:s", time() );	


//	Insert new comment in db
if ($commentText != "") {
	
	$id = hash('crc32', $commentText.$today);

	$stmt = $conn->prepare("SELECT u.user_id, u.nickname
		FROM user_info AS u
		NATURAL JOIN sessions AS s
		WHERE s.session_id = :session_id"
	); 
	$stmt->bindParam(':session_id', $sessionId);
	$stmt->execute();
	$result = $stmt->fetch(PDO::FETCH_ASSOC); 
	$userId = $result['user_id']; 
	$userName = $result['nickname']; 
	
		
	$stmt = $conn->prepare("INSERT INTO comment (comment_id, author, date, text)
							VALUES ( :id, :author, :date, :text)"); 
		
	$stmt->bindParam(':id', $id);
	$stmt->bindParam(':author', $userId);
	$stmt->bindParam(':date', $today);
	$stmt->bindParam(':text', $commentText);
	$stmt->execute();
	
		
	$stmt = $conn->prepare("INSERT INTO comments (hash, comment_id)
							VALUES ( :hash, :id)"); 
		
	$stmt->bindParam(':hash', $thumb);
	$stmt->bindParam(':id', $id);
	$stmt->execute();

} 


	

//	Retrive a thumbnail's comment
//TODO: Refactor SQL query
$stmt = $conn->prepare("SELECT comment.author, 
	comment.date, comment.text
	FROM comment
	NATURAL JOIN comments
	WHERE comments.hash = :hash
	ORDER BY comment.date DESC;"
); 
$stmt->bindParam(':hash', $thumb);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC); 


$stmt1 = $conn->prepare("SELECT nickname FROM user_info	
	WHERE user_id = :user_id"
); 

while ($result) {
	/*	Get author nickname	*/
	$stmt1->bindParam(':user_id', $result['author']);
	$stmt1->execute();
	$result1 = $stmt1->fetch(PDO::FETCH_ASSOC);  

	$list[] =[ 
		'date' => $result['date'],
		'text' => $result['text'],
		'author' => $result1['nickname'],
	];
		
	$result = $stmt->fetch(PDO::FETCH_ASSOC);

}

echo json_encode($list);


$conn = null;

?>
