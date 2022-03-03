<?php
/**
 * PHPExcel
 *
 * Copyright (C) 2006 - 2017 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    7.0, 2017-03-17
 */
/** Error reporting */
error_reporting(E_ALL);
date_default_timezone_set('Europe/Paris');
/** PHPExcel */
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Read from Excel5 (.xls) template
$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . 'project.xls');
App::import("vendor", "str_utility");
$str_utility = new str_utility();
$title = $project_name['Project']['project_name'];
$head = array('No', 'Title', 'Content');
$k = 0;
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(150);

$objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle('A2')->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()->setARGB('CCCCCCCC');

$objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A2', $title);
 // debug($risk_comment); exit;
function getDataComment($data_comment = array()){
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $j, $alias);
    if(!empty($data_comment)){
        foreach ($data_comment as $key => $comment) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($j+1), $comment['LogSystem']['name'])
                ->setCellValue('A' . ($j+2), date('d M Y', $comment['LogSystem']['created']))
                ->setCellValue('A' . ($j+3), $comment['LogSystem']['description']);
            $j += 3;
        }
    }
}
foreach ($project_name as $i => $project) {
    $data = array();
    $data[] = $i;
    $j = 3;
    foreach ($view_content as $field_name => $alias) {
        $objPHPExcel->getActiveSheet()
                    ->getStyle('A'. $j)
                    ->applyFromArray(
                        array(
                            'font'  => array(
                                'color' => array('rgb' => 'a9a9a9')
                            )
                        )
                    );
        $objPHPExcel->getActiveSheet()
                    ->getStyle('A'. ($j+1))
                    ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $alias = __d(sprintf($_domain, 'Details'), $alias, true);
        switch ($field_name) {
            case "project_name":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $flash_data['project_name']);
                break;
            
            case "project_code":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $flash_data['project_code']);
                break;
           
            case "project_manager":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $flash_data['project_manager']);
                break;
            case 'start_date':
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), date('d/m/Y', strtotime($flash_data['start_date'])));
                break;
            case "end_date":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), date('d/m/Y', strtotime($flash_data['end_date'])));
                break;
            case "weather":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $flash_data['weather']);
                break;
            case "primary_objectives":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $flash_data['primary_objectives']);
                break;
            case "technical_manager":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $flash_data['technical_manager']);
                break;
            
            case "overload":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, __('Overload', true))
                        ->setCellValue('A' . ($j+1), $flash_data['overload']);
                break;
            
            case "rank":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, __('Rank', true))
                        ->setCellValue('A' . ($j+1), $flash_data['rank']);
                break;
            
            case "weather":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, __('Weather', true))
                        ->setCellValue('A' . ($j+1), $flash_data['weather']);
                break;
            
            case "engaged":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j,  __('Consumed', true))
                        ->setCellValue('A' . ($j+1), $flash_data['engaged']);
                break;
            
            case "validated":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, __('Planed', true))
                        ->setCellValue('A' . ($j+1), $flash_data['validated']);
                break;
            
            case "risk_comment":
                $n = 0;
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias);
                if(!empty($risk_comment)){
                    foreach ($risk_comment as $key => $comment) {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . ($j+1), $comment['LogSystem']['name'])
                            ->setCellValue('A' . ($j+2), date('d M Y', $comment['LogSystem']['created']))
                            ->setCellValue('A' . ($j+3), $comment['LogSystem']['description']);
                        $j += 3;
                        $n++;
                        if($n == 1 ) break;
                    }
                }
                break;
            case "kpi_comment":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias);
                $n = 0; 
                if(!empty($kpi_comment)){
                    foreach ($kpi_comment as $key => $comment) {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . ($j+1), $comment['LogSystem']['name'])
                            ->setCellValue('A' . ($j+2), date('d M Y', $comment['LogSystem']['created']))
                            ->setCellValue('A' . ($j+3), $comment['LogSystem']['description']);
                        $j += 3;
                        $n++;
                        if($n == 2 ) break;
                    }
                }
                break;
            case "todo":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias);
                $n = 0; 
                if(!empty($todo)){
                    foreach ($todo as $key => $comment) {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . ($j+1), $comment['LogSystem']['name'])
                            ->setCellValue('A' . ($j+2), date('d M Y', $comment['LogSystem']['created']))
                            ->setCellValue('A' . ($j+3), $comment['LogSystem']['description']);
                        $j += 3;
                        $n++;
                        if($n == 2 ) break;
                    }

                }
                break;
            case "done":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias);
                $n = 0; 
                if(!empty($done)){
                    foreach ($done as $key => $comment) {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . ($j+1), $comment['LogSystem']['name'])
                            ->setCellValue('A' . ($j+2), date('d M Y', $comment['LogSystem']['created']))
                            ->setCellValue('A' . ($j+3), $comment['LogSystem']['description']);
                        $j += 3;
                        $n++;
                        if($n == 2 ) break;
                    }
                }
                break;
            case "projectRisks":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias);
                $n = 0; 
                if(!empty($projectRisks)){
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($j+2), date('d M Y', $projectRisks['ProjectRisk']['updated']))
                        ->setCellValue('A' . ($j+3), $projectRisks['ProjectRisk']['project_risk']);
                    $j += 3;
                }
                break;            
            default:
                $j -= 2;
                break;
        }
        $j += 2;
    }
}
$f = $i + 4;
/*
$objDrawing = new PHPExcel_Worksheet_Drawing();
$objDrawing->setName('global');
$objDrawing->setDescription('Global');
$objDrawing->setPath('./img/front/global-logo.jpg');
$objDrawing->setCoordinates($name_column['1'] . $f);
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
$objPHPExcel->getActiveSheet()->getRowDimension($name_column['1'] . $f)->setRowHeight(13);
$objPHPExcel->getActiveSheet()->getStyle($name_column['1'] . $f)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle($name_column['1'] . $f)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
*/
//$file = $title;
//$excel->render($file);
// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle('Project detail');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client�s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Flash_Info_' . str_replace(" ", '_', $project_name['Project']['project_name']) . '_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>
