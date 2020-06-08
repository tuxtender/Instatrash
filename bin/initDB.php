<?php

include 'misc.php';

$conn = connectDatabase($db_config);
$sql = file_get_contents('../docs/FileStore_db.sql', true);
$conn->exec($sql);

?>
