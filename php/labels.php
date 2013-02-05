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
$labels = '';
if(isset($_POST['labels'])) {
	$labels = json_decode($_POST['labels'], true);
	foreach ($labels as $label) {
		$label['description'] = ereg_replace("[^A-Za-z0-9\ ]", "", $label['description']);
	}
}
else {
	$labels = $upload;
}

/**************************************************************************************************
 * Validation
 **************************************************************************************************/
$errors = array();
foreach ($labels as $index => $label) {
	$index++;
	if(!is_numeric($label['upc'])) {
		$errors[] = "<strong>Error in line $index:</strong>The UPC contains charactes that are
		 	not a number.";
		$errors[] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;({$label['manufacturer']}, {$label['description']}, 
			{$label['units']}, {$label['size']}, {$label['upc']}, {$label['price']})";
	}
	if(strlen($label['upc']) > 11) {
		$label['upc'] = substr($label['upc'], 0, 11);
	} else if(strlen($label['upc']) != 11) {
		$errors[] = "<strong>Error in line $index:</strong>The UPC is not the correct length. It
			should be 11 digits."; 
		$errors[] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;({$label['manufacturer']}, {$label['description']}, 
			{$label['units']}, {$label['size']}, {$label['upc']}, {$label['price']})";
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
$colorFront = new BCGColor(0,0,0);
$colorBack = new BCGColor(255,255,255);
$font = new BCGFontFile('barcodephp/fonts/Arial.ttf', 11);

/**************************************************************************************************
 * Generate barcodes
 *************************************************************************************************/
foreach($labels as $label) {
	// Barcode settings
	$code = new BCGupca();
	$code->setScale(2);
	$code->setThickness(9);
	$code->setForegroundColor($colorFront);
	$code->setBackgroundColor($colorBack);
	$code->setFont($font);
	$code->parse($label['upc']);
	
	
	// Drawing settings
	$drawing = new BCGDrawing("barcodes/{$label['upc']}.png", $colorBack);
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
$pdf->SetMargins(0, 0, 0);
$pdf->SetTextColor(35,31,32);
$pdf->SetAutoPageBreak(true, 0);
$pdf->setSourceFile('labels.pdf');
$pdf->AddPage();
$tplIdx = $pdf->importPage(1); 
$pdf->useTemplate($tplIdx, 0, 0);
$pdf->SetFont('Arial', 'B', 16);

$count = 0;
foreach($labels as $label) {
	if($count == 30) {
		$pdf->AddPage();
		$tplIdx = $pdf->importPage(1); 
		$pdf->useTemplate($tplIdx, 0, 0);
		$count = 0;
	}
	
	// Manufacturer
	$pdf->SetFont('ArialNarrow', 'B', 10);
	$pdf->setXY(7.8 + ($count % 3) * 69.9, 15.05 + (int)($count / 3) * 25.4);
	$pdf->Cell(17,5,$label['manufacturer']);
	
	// Description
	$pdf->SetFont('ArialNarrow', 'B', 10);
	$pdf->setXY(7.8 + ($count % 3) * 69.9, 18.7 + (int)($count / 3) * 25.4);
	$pdf->Cell(17,5,$label['description']);
	
	// Units
	$pdf->SetFont('ArialNarrow', '', 8);
	$pdf->setXY(7.8 + ($count % 3) * 69.9, 21.6 + (int)($count / 3) * 25.4);
	$pdf->Cell(17,5,$label['units']);
	
	// Size
	$pdf->SetFont('ArialNarrow', '', 8);
	$pdf->setXY(11.7 + ($count % 3) * 69.9, 21.6 + (int)($count / 3) * 25.4);
	$pdf->Cell(17,5,$label['size']);
	
	// Price
	if (strlen($label['price']) == 4)
		$pdf->SetFont('ArialNarrow', 'B', 36);
	if (strlen($label['price']) == 5)
		$pdf->SetFont('ArialNarrow', 'B', 26);
	if (strlen($label['price']) == 6)
		$pdf->SetFont('ArialNarrow', 'B', 21);
	$pdf->setXY(50.7 + ($count % 3) * 69.9, 27.7 + (int)($count / 3) * 25.4);
	$pdf->Cell(17,10,$label['price']);
	
	// Barcode
	$x = 6.4 + ($count % 3) * 69.9;
	$y = 26.7 + (int)($count / 3) * 25.4;	
	$pdf->Image("barcodes/{$label['upc']}.png", $x, $y, 43.6, 10.2);
	$count++;
}
$pdf->Output();

/**************************************************************************************************
 * Remove barcodes
 * 
 * The barcodes are no longer needed. Delete them to clear up memory.
 *************************************************************************************************/
foreach($labels as $label) {
	unlink("barcodes/{$label['upc']}.png");
}

/**********************************************************************************************
 * Update database
 *********************************************************************************************/
 
 foreach ($labels as $label) {
	$stmt = $db->query("SELECT upc FROM labels where upc='" . $label['upc'] . "'");
	
	try {
		if($stmt->rowCount()) {
			$stmt2 = $db->prepare("
				UPDATE labels SET
				manufacturer=:man, 
				description=:desc, 
				units=:units, 
				size=:size, 
				price=:price
				WHERE upc=:upc
			");
			$stmt2->bindParam(':upc'  , $label['upc']);
			$stmt2->bindParam(':man'  , $label['manufacturer']);
			$stmt2->bindParam(':desc' , $label['description']);
			$stmt2->bindParam(':units', $label['units']);
			$stmt2->bindParam(':size' , $label['size']);
			$stmt2->bindParam(':price', $label['price']);
			$stmt2->execute();
		}
		else {
			$stmt2 = $db->prepare("INSERT INTO labels (upc,manufacturer, description, units, size, price) 
				VALUES (:upc, :man, :desc, :units, :size, :price)");
			$stmt2->bindParam(':upc'  , $label['upc']);
			$stmt2->bindParam(':man'  , $label['manufacturer']);
			$stmt2->bindParam(':desc' , $label['description']);
			$stmt2->bindParam(':units', $label['units']);
			$stmt2->bindParam(':size' , $label['size']);
			$stmt2->bindParam(':price', $label['price']);
			$stmt2->execute();
		}	
	}
	catch (PDOException $e) {
		echo $e->getMessage() . "<br>";
	}
 } 	

