<?php
header("charset=utf-8");
/***************************************************************************************************
 * Includes
 **************************************************************************************************/
// Mysql
include_once('connect_to_mysql.php');
// FPDF
include_once('fpdf/fpdf.php');
include_once('fpdf/fpdi.php');
include_once('fpdf/makefont/makefont.php');
// BarcodePHP
require_once('barcodephp/BCGFontFile.php');
require_once('barcodephp/BCGColor.php');
require_once('barcodephp/BCGDrawing.php');
require_once('barcodephp/BCGupca.barcode.php');

/**************************************************************************************************
 * POST
 **************************************************************************************************/
$shelfs = '';
if(isset($_POST['shelfs'])) {
	$shelfs = json_decode($_POST['shelfs'], true);
	foreach ($shelfs as $shelf) {
		$shelf['description'] = ereg_replace("[^A-Za-z0-9\ ]", "", $shelf['description']);
	}
}
else {
	$shelfs = $upload;
}

/**************************************************************************************************
 * Validation
 **************************************************************************************************/
$errors = array();
foreach ($shelfs as $index => $shelf) {
	$index++;
	if(!is_numeric($shelf['upc'])) {
		$errors[] = "<strong>Error in line $index:</strong>The UPC contains charactes that are
		 	not a number.";
		$errors[] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;({$shelf['manufacturer']}, {$shelf['description']}, 
			{$shelf['size']}, {$shelf['upc']}, {$shelf['price']})";
	}
	if(strlen($shelf['upc']) > 11) {
		$shelf['upc'] = substr($shelf['upc'], 0, 11);
	} else if(strlen($shelf['upc']) != 11) {
		$errors[] = "<strong>Error in line $index:</strong>The UPC is not the correct length. It
			should be 11 digits."; 
		$errors[] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;({$shelf['manufacturer']}, {$shelf['description']}, 
			{$shelf['size']}, {$shelf['upc']}, {$shelf['price']})";
	}
}
if(count($errors) > 0) {
	echo "<strong>You have errors in your CSV file.</strong>";
	echo " Make sure that any fields with commas have quotes around them.<br><br>";
	foreach($errors as $error) {
		echo $error . '<br>';
	}
}

if(count($errors) > 0)
	return;
/***************************************************************************************************
 * Barcode Settings
 **************************************************************************************************/
$colorFront = new BCGColor(0, 0,0);
$colorBack = new BCGColor(255,255,255);
$font = new BCGFontFile('barcodephp/fonts/Arial.ttf', 8);

/**************************************************************************************************
 * Generate barcodes
 *************************************************************************************************/
foreach($shelfs as $shelf) {
	// Barcode settings
	$code = new BCGupca();
	$code->setScale(2);
	$code->setThickness(9);
	$code->setForegroundColor($colorFront);
	$code->setBackgroundColor($colorBack);
	$code->setFont($font);
	$code->parse($shelf['upc']);
	
	
	// Drawing settings
	$drawing = new BCGDrawing("barcodes/{$shelf['upc']}.png", $colorBack);
	$drawing->setBarcode($code);
	$drawing->draw();
	
	// Generate barcode
	$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
}
 
/**************************************************************************************************
 * Generate PDF
 *************************************************************************************************/
$pdf = new FPDI('L', 'mm', 'Letter');
$pdf->AddFont('ArialNarrow','','ARIALN.php');
$pdf->AddFont('ArialNarrow','B', 'ARIALNB.php');
$pdf->AddFont ('Frutiger','EXBLC','FRUTEXBLC.php');
$pdf->SetMargins(10, 10, 10);
$pdf->SetTextColor(35,31,32);
$pdf->SetAutoPageBreak(true, 0);
$pdf->setSourceFile('shelfTalkers.pdf');
$pdf->AddPage();
$tplIdx = $pdf->importPage(1); 
$pdf->useTemplate($tplIdx, .5, 4);
$pdf->SetFont('Arial', 'B', 16);

$count = 0;
foreach($shelfs as $shelf) {
	if($count == 2) {
		$pdf->AddPage();
		$tplIdx = $pdf->importPage(1); 
		$pdf->useTemplate($tplIdx, .5, 4);
		$count = 0;
	}
	
	/**********************************************************************************************
	 * Left
	 *********************************************************************************************/
	// Manufacturer
	$pdf->SetFont('Frutiger', 'EXBLC', 40);
	$pdf->setXY(7.8, 15.8 + $count * 108);
	$pdf->Cell(125,13,$shelf['manufacturer'], 0, 0, 'C');
	
	// Description
	//$pdf->setTextcolor(0,255,0);
	$desc = $shelf['size'] . ' ' . $shelf['description'];
	$pdf->SetFont('ArialNarrow', 'B', 25);
	$pdf->setXY(7.8, 31 + $count * 108);
	$pdf->Cell(125,10,$desc, 0, 0, 'C');
	
	// Price
	$price = number_format(floatval($shelf['price']), 2);
	$price = explode('.', $price);
	
	// Check for less than a dollar
	if ($price[0] < 1) {
		// Cents
		$pdf->SetFont('Frutiger', 'EXBLC', 130);
		$pdf->setXY(45, 42.4 + $count * 108);
		$pdf->Cell(45,40,$price[1] . chr(162));
	}
	else {
		// Dollar
		$pdf->SetFont('Frutiger', 'EXBLC', 130);
		if(strlen($price[0]) == 1) {
			$pdf->setXY(40, 42.4 + $count * 108);
			$pdf->Cell(40,40,chr(36) . $price[0]);
		}
		else {
			$pdf->setXY(25, 42.4 + $count * 108);
			$pdf->Cell(65,40,chr(36) . $price[0]);
		}
		// Cents
		$pdf->SetFont('Frutiger', 'EXBLC', 68);
		$pdf->Cell(25,20.5,$price[1]);
	}
	
	// Barcode
	$x = 96.8;
	$y = 83 + $count * 108;	
	$pdf->Image("barcodes/{$shelf['upc']}.png", $x, $y, 30.5, 10);
	
	/**********************************************************************************************
	 * Right
	 *********************************************************************************************/
	// Manufacturer
	$pdf->SetFont('Frutiger', 'EXBLC', 40);
	$pdf->setXY(7.8 + 139.8, 15.8 + $count * 108);
	$pdf->Cell(125,13,$shelf['manufacturer'], 0, 0, 'C');
	
	// Description
	//$pdf->setTextcolor(0,255,0);
	$desc = $shelf['size'] . ' ' . $shelf['description'];
	$pdf->SetFont('ArialNarrow', 'B', 25);
	$pdf->setXY(7.8 + 139.8, 31 + $count * 108);
	$pdf->Cell(125,10,$desc, 0, 0, 'C');
	
	// Price
	$price = explode('.', $shelf['price']);
	
	// Check for less than a dollar
	if ($price[0] < 1) {
		// Cents
		$pdf->SetFont('Frutiger', 'EXBLC', 130);
		$pdf->setXY(45 + 139.8, 42.4 + $count * 108);
		$pdf->Cell(45,40,$price[1] . chr(162));
	}
	else {
		// Dollar
		$pdf->SetFont('Frutiger', 'EXBLC', 130);
		if(strlen($price[0]) == 1) {
			$pdf->setXY(40 + 139.8, 42.4 + $count * 108);
			$pdf->Cell(40,40,chr(36) . $price[0]);
		}
		else {
			$pdf->setXY(25 + 139.8, 42.4 + $count * 108);
			$pdf->Cell(65,40,chr(36) . $price[0]);
		}
		// Cents
		$pdf->SetFont('Frutiger', 'EXBLC', 68);
		$pdf->Cell(25,20.5,$price[1]);
	}
	
	// Change font back
	$pdf->SetFont('ArialNarrow', 'B', 77);
	
	// Barcode
	$x = 96.8 + 139.8;
	$y = 83 + $count * 108;	
	$pdf->Image("barcodes/{$shelf['upc']}.png", $x, $y, 30.5, 10);
	$count++;
}
$pdf->Output();

/**************************************************************************************************
 * Remove barcodes
 * 
 * The barcodes are no longer needed. Delete them to clear up memory.
 *************************************************************************************************/
foreach($shelfs as $shelf) {
	unlink("barcodes/{$shelf['upc']}.png");
}

/**********************************************************************************************
 * Update database
 *********************************************************************************************/
 
 foreach ($shelfs as $shelf) {
	$stmt = $db->query("SELECT upc FROM labels where upc='" . $shelf['upc'] . "'");
	
	try {
		if($stmt->rowCount()) {
			$stmt2 = $db->prepare("
				UPDATE shelfs SET
				manufacturer=:man, 
				description=:desc, 
				size=:size, 
				price=:price
				WHERE upc=:upc
			");
			$stmt2->bindParam(':upc'  , $shelf['upc']);
			$stmt2->bindParam(':man'  , $shelf['manufacturer']);
			$stmt2->bindParam(':desc' , $shelf['description']);
			$stmt2->bindParam(':size' , $shelf['size']);
			$stmt2->bindParam(':price', $shelf['price']);
			$stmt2->execute();
		}
		else {
			$stmt2 = $db->prepare("INSERT INTO labels (upc,manufacturer, description, size, price) 
				VALUES (:upc, :man, :desc, :size, :price)");
			$stmt2->bindParam(':upc'  , $shelf['upc']);
			$stmt2->bindParam(':man'  , $shelf['manufacturer']);
			$stmt2->bindParam(':desc' , $shelf['description']);
			$stmt2->bindParam(':size' , $shelf['size']);
			$stmt2->bindParam(':price', $shelf['price']);
			$stmt2->execute();
		}	
	}
	catch (PDOException $e) {
		echo $e->getMessage() . "<br>";
	}
 } 	

