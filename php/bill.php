<?php
/***************************************************************************************************
 * Includes
 **************************************************************************************************/
// FPDF
include_once('fpdf/fpdf.php');
include_once('fpdf/fpdi.php');
// BarcodePHP
require_once('barcodephp/BCGFontFile.php');
require_once('barcodephp/BCGColor.php');
require_once('barcodephp/BCGDrawing.php');
require_once('barcodephp/BCGcode128.barcode.php');

/***************************************************************************************************
 * Includes
 **************************************************************************************************/
$colorFront = new BCGColor(255,0,0);
$colorBack = new BCGColor(0,0,255);
$font = new BCGFontFile('barcodephp/fonts/Arial.ttf', 12);

// Barcodes
$barcodes = array('hello', 'goodbye', '9349234809');

/**************************************************************************************************
 * Generate barcodes
 *************************************************************************************************/
foreach($barcodes as $barcode) {
	// Barcode settings
	$code = new BCGcode128();
	$code->setScale(2);
	$code->setThickness(10);
	$code->setForegroundColor($colorFront);
	$code->setBackgroundColor($colorBack);
	$code->setFont($font);
	$code->parse($barcode);
	
	// Drawing settings
	$drawing = new BCGDrawing("barcodes/$barcode.png", $colorBack);
	$drawing->setBarcode($code);
	$drawing->draw();
	
	// Generate barcode
	$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
}

/**************************************************************************************************
 * Generate PDF
 *************************************************************************************************/
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$count = 1;
foreach($barcodes as $barcode) {
	$pdf->Image("barcodes/$barcode.png", 50, $count * 30);
	$count++;
}
$pdf->Output();

/**************************************************************************************************
 * Remove barcodes
 * 
 * The barcodes are no longer needed. Delete them to clear up memory.
 *************************************************************************************************/
foreach($barcodes as $barcode) {
	unlink("barcodes/$barcode.png");
}

