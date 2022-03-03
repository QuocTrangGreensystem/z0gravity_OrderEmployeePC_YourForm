<?php
App::import('Vendor', 'PHPExcel/Shared/PDF', array('file' => 'tcpdf.php'));
class MYPDF extends TCPDF {
    public function Footer($employee_name = "") {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
// set document information
$pdf->SetCreator(PDF_CREATOR);
// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 9));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// set default header data


// remove header logo
// if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design']){

// }else $pdf->SetHeaderData(PDF_HEADER_LOGO, 275);
// $pdf->SetHeaderData(PDF_HEADER_LOGO, 100, date('d/m/Y',time()) . ' ' . $employee_name, '', array(0,0,0), array(20, 255, 100));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->Rect(0,0,210,297,'F','',$fill_color = array(255, 237, 212));
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
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
foreach ($list as $key => $value) {
    $pdf->AddPage('L', 'A4');
    // $pdf->Image($value);
    if($key == $i){
        $pdf->Image($value, 2, 9, 380, false, '', '', '', false, 300, '', false, false, false, false, false, false);
    } else {
        $pdf->Image($value,2,7,0,0,'PNG');
    }
    unlink($value);
}

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
App::import('Core', 'Folder');
new Folder($path, true, 0777);
$pdf->Output($path . $file_name, 'F');
$pdf->Output($file_name, 'D');
?>
