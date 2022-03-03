<?php

App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));

$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . 'gantt.xls');

// Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
$styleArray = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => 'ffb250'),
        'size'  => 15,
        'name'  => 'Verdana'
    )
);
$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
$objDrawing->setName(__('Vision Project', true));
$objDrawing->setDescription(__('Vision Project gantt chart', true));
$objDrawing->setImageResource($image);
$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
$objDrawing->setHeight($height);
$objDrawing->setCoordinates('A2');
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', sprintf(__('Vision Project of %s', true), $project));
$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);

// insert logo
//$gdImage = imagecreatefromjpeg('img' . DS . 'front' . DS . 'global-logo.jpg');

/*$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
$objDrawing->setName('Global logo');
$objDrawing->setImageResource($gdImage);
$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
$rows += 2;
$objDrawing->setCoordinates('D' . $rows);
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());*/

// Redirect output to a client�s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="projects_vision_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
unlink($tmpFile);
exit;
?>
