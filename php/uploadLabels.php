<?php
header("Content-type: text/html; charset=utf-8");

if($_FILES["file"]["error"] > 0) {
	echo "Error: " . $_FILES["file"]["error"] . "<br>";
}
else if($file = fopen($_FILES["file"]["tmp_name"], 'r')) {
	$count = 0;
	$upload = array();
	while(($buffer = fgetcsv($file, 4096, ",")) !== false) {
		$line = array(
			'manufacturer' => trim($buffer[0]),
			'description'  => trim(ereg_replace("[^A-Za-z0-9\ ,]", "", $buffer[1])),
			'units'        => trim($buffer[2]),
			'size'         => trim($buffer[3]),
			'upc'		   => trim((strlen($buffer[4]) < 11) ? '0' . $buffer[4] : $buffer[4]),
			'price'        => trim($buffer[5]),
		);	
		array_push($upload, $line);
		
		$count++;
	}
	
	foreach($upload as $index => $test) {
		if(strtolower(substr($test['upc'],1,3)) == 'upc')
			unset($upload[$index]);
	}
	
	// Generate labels
	require_once('labels.php');
}
