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
$produces = '';
if(isset($_POST['produces'])) {
	$produces = json_decode($_POST['produces'], true);
	foreach ($produces as $produce) {
		$produce['description'] = ereg_replace("[^A-Za-z0-9\ ]", "", $produce['description']);
	}
}
else {
	$produces = $upload;
}

/**************************************************************************************************
 * Validation
 **************************************************************************************************/
$errors = array();
foreach ($produces as $index => $produce) {
	$index++;
	if(!is_numeric($produce['upc'])) {
		$errors[] = "<strong>Error in line $index:</strong>The UPC contains charactes that are
		 	not a number.";
		$errors[] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;({$produce['manufacturer']}, {$produce['description']}, 
			{$produce['size']}, {$produce['upc']}, {$produce['price']})";
	}
	if(strlen($produce['upc']) > 11) {
		$produce['upc'] = substr($produce['upc'], 0, 11);
	} else if(strlen($produce['upc']) != 11) {
		$errors[] = "<strong>Error in line $index:</strong>The UPC is not the correct length. It
			should be 11 digits."; 
		$errors[] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;({$produce['manufacturer']}, {$produce['description']}, 
			{$produce['size']}, {$produce['upc']}, {$produce['price']})";
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
foreach($produces as $produce) {
	// Barcode settings
	$code = new BCGupca();
	$code->setScale(1);
	$code->setThickness(9);
	$code->setForegroundColor($colorFront);
	$code->setBackgroundColor($colorBack);
	$code->setFont($font);
	$code->parse($produce['upc']);
	
	
	// Drawing settings
	$drawing = new BCGDrawing("barcodes/{$produce['upc']}.png", $colorBack);
	$drawing->setBarcode($code);
	$drawing->draw();
	
	// Generate barcode
	$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
}
 
/**************************************************************************************************
 * Generate PDF
 *************************************************************************************************/
$pdf = new FPDI('P', 'mm', 'Letter');
$pdf->AddFont ('Frutiger','','FRUTEXBLC.php');
$pdf->SetMargins(10, 10, 10);
$pdf->SetTextColor(0,0,0);
$pdf->SetAutoPageBreak(true, 0);
$pdf->setSourceFile('produce.pdf');
$pdf->AddPage();
$tplIdx = $pdf->importPage(1); 
$pdf->useTemplate($tplIdx);
$pdf->SetFont('Frutiger', '', 30);

$count = 0;
foreach($produces as $produce) {
	if($count == 10) {
		$pdf->AddPage();
		$tplIdx = $pdf->importPage(1); 
		$pdf->useTemplate($tplIdx);
		$count = 0;
	}
	
	// Manufacturer
	$pdf->SetFont('Frutiger', '', 30);
	$pdf->setXY(4.4 + ($count % 2) * 108, 22.1 + (int)($count / 2) * 50.7);
	$pdf->Cell(100,8,$produce['manufacturer'], 0, 0, 'L');
	
	// Description
	$pdf->SetFont('Frutiger', '', 30);
	$pdf->setXY(4.4 + ($count % 2) * 108, 31.6 + (int)($count / 2) * 50.7);
	$pdf->Cell(100,8,$produce['description'], 0, 0, 'L');
	
	// Size
	$pdf->SetFont('Frutiger', '', 14);
	$pdf->setXY(5.2 + ($count % 2) * 108, 39.6 + (int)($count / 2) * 50.7);
	$pdf->Cell(100,8,$produce['size'], 0, 0, 'L');
	
	// Price
	if(strlen($produce['price']) == 5)
		$pdf->SetFont('Frutiger', '', 60);
	else
		$pdf->SetFont('Frutiger', '', 76);
	$pdf->setXY(51.8 + ($count % 2) * 108, 41.6 + (int)($count / 2) * 50.7);
	$pdf->Cell(60,20,$produce['price'], 0, 0, 'L');
	
	// Barcode
	$x = 6.2 + ($count % 2) * 108;
	$y = 48 + (int)($count / 2) * 50.7;	
	$pdf->Image("barcodes/{$produce['upc']}.png", $x, $y, 44.85, 12.7);
	$count++;
}
$pdf->Output();

/**************************************************************************************************
 * Remove barcodes
 * 
 * The barcodes are no longer needed. Delete them to clear up memory.
 *************************************************************************************************/
foreach($produces as $produce) {
	unlink("barcodes/{$produce['upc']}.png");
}

/**********************************************************************************************
 * Update database
 *********************************************************************************************/
 
 foreach ($produces as $produce) {
	$stmt = $db->query("SELECT upc FROM labels where upc='" . $produce['upc'] . "'");
	
	try {
		if($stmt->rowCount()) {
			$stmt2 = $db->prepare("
				UPDATE produces SET
				manufacturer=:man, 
				description=:desc, 
				size=:size, 
				price=:price
				WHERE upc=:upc
			");
			$stmt2->bindParam(':upc'  , $produce['upc']);
			$stmt2->bindParam(':man'  , $produce['manufacturer']);
			$stmt2->bindParam(':desc' , $produce['description']);
			$stmt2->bindParam(':size' , $produce['size']);
			$stmt2->bindParam(':price', $produce['price']);
			$stmt2->execute();
		}
		else {
			$stmt2 = $db->prepare("INSERT INTO labels (upc,manufacturer, description, size, price) 
				VALUES (:upc, :man, :desc, :size, :price)");
			$stmt2->bindParam(':upc'  , $produce['upc']);
			$stmt2->bindParam(':man'  , $produce['manufacturer']);
			$stmt2->bindParam(':desc' , $produce['description']);
			$stmt2->bindParam(':size' , $produce['size']);
			$stmt2->bindParam(':price', $produce['price']);
			$stmt2->execute();
		}	
	}
	catch (PDOException $e) {
		echo $e->getMessage() . "<br>";
	}
 }