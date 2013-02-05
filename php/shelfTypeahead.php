<?php
require_once('connect_to_mysql.php');
header('Content-type: application/json; charset=utf-8');

$upc = $_POST['query'];
$upcs = array();

try {
	$stmt = $db->query("SELECT upc FROM shelf_talkers WHERE upc LIKE '%$upc%'");
	$stmt->setFetchMode(PDO::FETCH_NUM);
	while($row = $stmt->fetch()) {
		array_push($upcs, $row[0]);
	}
}
catch (PDOException $e) {
	echo $e->getMessage();
}

echo json_encode($upcs);