<?php
//header("Content-type: text/html; charset=utf-8");

if($_FILES["palletFile"]["error"] > 0) {
	echo "Error: " . $_FILES["palletFile"]["error"] . "<br>";
}
else if($file = fopen($_FILES["palletFile"]["tmp_name"], 'r')) {
	$count = 0;
	$upload = array();
	while(($buffer = fgetcsv($file, 4096, ",")) !== false) {
		$line = array(
			'manufacturer' => trim($buffer[0]),
			'description'  => trim(ereg_replace("[^A-Za-z0-9\ ,]", "", $buffer[1])),
			'size'         => trim($buffer[2]),
			'upc'		   => trim($buffer[3]),
			'price'        => trim($buffer[4]),
		);	
		array_push($upload, $line);
		
		$count++;
	}
	
	foreach($upload as $index => $test) {
		if(strtolower($test['upc']) == 'upc')
			unset($upload[$index]);
		else
			// Add leading zeros
			while (strlen($upload[$index]['upc']) < 11) {
				$upload[$index]['upc'] = '0' . $upload[$index]['upc']; 
			} 
	}
	
	// Generate labels
	require_once('pallets.php');
}
