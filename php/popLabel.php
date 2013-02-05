<?php
require_once('connect_to_mysql.php');
header('Content-type: application/json; charset=utf-8');
$upc = $_POST['upc'];

$label = array(
	"man" => "",
	"desc" => "",
	"units" => "",
	"size" => "",
	"price" => "",
);

try {
	$stmt = $db->query("SELECT * FROM labels where upc='$upc'");
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	while($row = $stmt->fetch()) {
		$label["man"]   = $row["manufacturer"];
		$label["desc"]  = utf8_encode($row["description"]);
		$label["units"] = $row["units"];
		$label["size"]  = $row["size"];
		$label["price"] = $row["price"];
		
		if($label["man"] != "") {
			echo json_encode($label);
		}
	}
	
	if($label["man"] == "")
		echo json_encode(array("man" => "empty"));
}
catch (PDOException $e) {
	echo $e->getMessage();
}
