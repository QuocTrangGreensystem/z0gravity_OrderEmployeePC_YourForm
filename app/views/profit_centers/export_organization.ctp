<?php

App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));

$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . 'gantt.xls');

// Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
// Insert gantt
$gdImage = imagecreatefrompng($tmpFile);

$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
$objDrawing->setName('Vision portfolio');
$objDrawing->setDescription('Vision portfolio gantt chart');
$objDrawing->setImageResource($gdImage);
$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
$objDrawing->setHeight($height);
$objDrawing->setCoordinates('A3');
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', 'Organization chart ');

// insert logo
//$gdImage = imagecreatefromjpeg('img' . DS . 'front' . DS . 'global-logo.jpg');

//$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
//$objDrawing->setName('Global logo');
//$objDrawing->setImageResource($gdImage);
//$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
//$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
//$rows += 5;
//$objDrawing->setCoordinates('D' .//$rows);
//$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

//$objPHPExcel->setActiveSheetIndex(0)->getRowDimension(2)->setRowHeight($height);
// Redirect output to a client�s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="organization_chart' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
unlink($tmpFile);
exit;
?>