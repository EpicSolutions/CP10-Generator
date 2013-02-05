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
$pallets = '';
if(isset($_POST['pallets'])) {
	$pallets = json_decode($_POST['pallets'], true);
	foreach ($pallets as $pallet) {
		$pallet['description'] = ereg_replace("[^A-Za-z0-9\ ]", "", $pallet['description']);
	}
}
else {
	$pallets = $upload;
}

/**************************************************************************************************
 * Validation
 **************************************************************************************************/
$errors = array();
foreach ($pallets as $index => $pallet) {
	$index++;
	if(!is_numeric($pallet['upc'])) {
		$errors[] = "<strong>Error in line $index:</strong>The UPC contains charactes that are
		 	not a number.";
		$errors[] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;({$pallet['manufacturer']}, {$pallet['description']}, 
			{$pallet['size']}, {$pallet['upc']}, {$pallet['price']})";
	}
	if(strlen($pallet['upc']) > 11) {
		$pallet['upc'] = substr($pallet['upc'], 0, 11);
	} else if(strlen($pallet['upc']) != 11) {
		$errors[] = "<strong>Error in line $index:</strong>The UPC is not the correct length. It
			should be 11 digits."; 
		$errors[] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;({$pallet['manufacturer']}, {$pallet['description']}, 
			{$pallet['size']}, {$pallet['upc']}, {$pallet['price']})";
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
foreach($pallets as $pallet) {
	// Barcode settings
	$code = new BCGupca();
	$code->setScale(2);
	$code->setThickness(9);
	$code->setForegroundColor($colorFront);
	$code->setBackgroundColor($colorBack);
	$code->setFont($font);
	$code->parse($pallet['upc']);
	
	
	// Drawing settings
	$drawing = new BCGDrawing("barcodes/{$pallet['upc']}.png", $colorBack);
	$drawing->setBarcode($code);
	$drawing->draw();
	
	// Generate barcode
	$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
}
 
/**************************************************************************************************
 * Generate PDF
 *************************************************************************************************/
$pdf = new FPDI('P', 'mm', 'Letter');
$pdf->AddFont('ArialNarrow','','ARIALN.php');
$pdf->AddFont('ArialNarrow','B', 'ARIALNB.php');
$pdf->AddFont ('Frutiger','EXBLC','FRUTEXBLC.php');
$pdf->SetMargins(10, 10, 10);
$pdf->SetTextColor(35,31,32);
$pdf->SetAutoPageBreak(true, 0);
$pdf->setSourceFile('palletSigns.pdf');
$pdf->SetFont('Arial', 'B', 16);

foreach($pallets as $pallet) {
	$pdf->AddPage();
	$tplIdx = $pdf->importPage(1); 
	$pdf->useTemplate($tplIdx, 0, 0);
	
	// Manufacturer
	$pdf->SetFont('ArialNarrow', 'B', 58);
	$pdf->setXY(26, 56);
	$pdf->Cell(164,20,$pallet['manufacturer'], 0, 0, 'C');
	
	// Description
	$pdf->SetFont('ArialNarrow', 'B', 43);
	$pdf->setXY(26, 85);
	$pdf->Cell(164, 13, $pallet['description'], 0, 0, 'C');
	
	// Size
	$pdf->SetFont('ArialNarrow', 'B', 43);
	$pdf->setXY(28, 227.5);
	$pdf->Cell(50, 12, $pallet['size']);
	
	// Price
	$price = number_format(floatval($pallet['price']), 2);
	$price = explode('.', $price);
	
	// Check for less than a dollar
	if ($price[0] < 1) {
		// Cents
		$pdf->SetFont('Frutiger', 'EXBLC', 240);
		$pdf->setXY(55, 115);
		$pdf->Cell(45,70,$price[1] . chr(162));
	}
	else {
		// Dollar
		$pdf->SetFont('Frutiger', 'EXBLC', 240);
		if(strlen($price[0]) == 1) {
			$pdf->setXY(45, 115);
			$pdf->Cell(72, 70,chr(36) . $price[0]);
		}
		else {
			$pdf->setXY(26, 115);
			$pdf->Cell(115, 70,chr(36) . $price[0]);
		}
		// Cents
		$pdf->SetFont('Frutiger', 'EXBLC', 120);
		$pdf->Cell(25,35,$price[1]);
	}
	
	// Barcode
	$x = 137.5;
	$y = 227;	
	$pdf->Image("barcodes/{$pallet['upc']}.png", $x, $y, 46, 15);
	
	// Change font back
	$pdf->SetFont('ArialNarrow', 'B', 77);
}
$pdf->Output();

/**************************************************************************************************
 * Remove barcodes
 * 
 * The barcodes are no longer needed. Delete them to clear up memory.
 *************************************************************************************************/
foreach($pallets as $pallet) {
	unlink("barcodes/{$pallet['upc']}.png");
}

/**********************************************************************************************
 * Update database
 *********************************************************************************************/
 
 foreach ($pallets as $pallet) {
	$stmt = $db->query("SELECT upc FROM labels where upc='" . $pallet['upc'] . "'");
	
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
			$stmt2->bindParam(':upc'  , $pallet['upc']);
			$stmt2->bindParam(':man'  , $pallet['manufacturer']);
			$stmt2->bindParam(':desc' , $pallet['description']);
			$stmt2->bindParam(':size' , $pallet['size']);
			$stmt2->bindParam(':price', $pallet['price']);
			$stmt2->execute();
		}
		else {
			$stmt2 = $db->prepare("INSERT INTO labels (upc, manufacturer, description, size, price) 
				VALUES (:upc, :man, :desc, :size, :price)");
			$stmt2->bindParam(':upc'  , $pallet['upc']);
			$stmt2->bindParam(':man'  , $pallet['manufacturer']);
			$stmt2->bindParam(':desc' , $pallet['description']);
			$stmt2->bindParam(':size' , $pallet['size']);
			$stmt2->bindParam(':price', $pallet['price']);
			$stmt2->execute();
		}	
	}
	catch (PDOException $e) {
		echo $e->getMessage() . "<br>";
	}
 } 	

