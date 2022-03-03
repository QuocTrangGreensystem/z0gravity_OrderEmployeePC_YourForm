<?php
App::import('Vendor', 'PHPExcel/Shared/PDF', array('file' => 'tcpdf.php'));

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
// set document information
$pdf->SetCreator(PDF_CREATOR);

// set default header data
// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->getPageSizeFromFormat('A4');
// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
// set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('dejavusans', '', 14, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
// debug( $list); exit;
foreach ($list as $key => $value) {
    $pdf->AddPage();
    // $pdf->Image($value);
    $pdf->Image($value, '', '', 250, '', '', '', '', false, 300, '', false, false, false, false, false, false);
    unlink($value);
}

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('Indicator_'.time().'.pdf', 'D');
?>
