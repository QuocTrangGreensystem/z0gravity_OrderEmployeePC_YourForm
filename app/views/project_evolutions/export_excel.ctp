<?php

/**
 * PHPExcel
 *
 * Copyright (C) 2006 - 2011 PHPExcel
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
 * @version    1.7.6, 2011-02-27
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
$objPHPExcel = $objReader->load(TEMPLATES . "user_view.xls");

App::import("vendor", "str_utility");
$str_utility = new str_utility();
$title = __("Project Evolution of " . $projectName['Project']['project_name'], true);
$head = array('No', 'Evolution', 'Type Evolution', 'Applicant', 'Validate Date', 'Validator', 'Impact', 'Supplementary Budget');
$k = 0; //$g =1;
$name_column = $columns;
foreach ($head as $hea) {
    $j = 2;
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', $title)
            ->setCellValue($name_column[$k] . $j, $hea);
    //$objPHPExcel->getActiveSheet()->getColumnDimension($name_column[$g])->setAutoSize(true);                     
    $k++; //$g++;          
}

$objPHPExcel->getActiveSheet()->getStyle($name_column['0'] . $j . ":" . $name_column[$k - 1] . $j)->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()->setARGB('CCCCCCCC');

$i = 0;
$j = 3;
foreach ($projectEvolutions as $projectEvolution) {
    $i++;
    $project_evolution_id = $projectEvolution["ProjectEvolution"]["id"];
    $project_evolution = $projectEvolution["ProjectEvolution"]["project_evolution"];

    $project_type_evolution = $projectEvolution["ProjectEvolutionType"]["project_type_evolution"];

    $evolution_applicant = "";
    $evolution_applicant_id = $projectEvolution["ProjectEvolution"]["evolution_applicant"];
    foreach ($projectManagers as $id => $name):
        if ($evolution_applicant_id == $id) {
            $evolution_applicant = $name;
            $evolution_applicant_id = $id;
            break;
        }
    endforeach;

    $validated = $str_utility->convertToVNDate($projectEvolution["ProjectEvolution"]["evolution_date_validated"]);

    $evolution_validator = "";
    $evolution_validator_id = $projectEvolution["ProjectEvolution"]["evolution_validator"];
    foreach ($projectManagers as $id => $name):
        if ($evolution_validator_id == $id) {
            $evolution_validator = $name;
            $evolution_validator_id = $id;
            break;
        }
    endforeach;

    $evolution_impact_ids = "";
    $evolution_impact_list = "";
    foreach ($projectEvolutionImpactRefers as $impact) {
        if ($impact['ProjectEvolutionImpactRefer']['project_evolution_id'] == $project_evolution_id) {
            $evolution_impact_ids .= $impact['ProjectEvolutionImpactRefer']['project_evolution_impact_id'] . "-";

            $evolution_impact_list .= $projectEvolutionImpacts[$impact['ProjectEvolutionImpactRefer']['project_evolution_impact_id']] . ",";
        }
    }
   // debug($evolution_impact_list); exit;
    $budget = $projectEvolution["ProjectEvolution"]["supplementary_budget"];

    $data = array($i, $project_evolution, $project_type_evolution, $evolution_applicant, $validated, $evolution_validator, $evolution_impact_list, $budget);

    $k = 0;
    foreach ($data as $dat) {
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue($name_column[$k] . $j, $dat);
        $k++;
    }
    $j++;
}


$f = $j + 2;
$objDrawing = new PHPExcel_Worksheet_Drawing();
$objDrawing->setName('global');
$objDrawing->setDescription('Global');
$objDrawing->setPath('./img/front/global-logo.jpg');
$objDrawing->setCoordinates($name_column['1'] . $f);
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
$objPHPExcel->getActiveSheet()->getRowDimension($f)->setRowHeight(13);
$objPHPExcel->getActiveSheet()->getStyle($name_column['1'] . $f)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle($name_column['1'] . $f)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client�s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="project_evolution_of_' . str_replace(" ", '_', $projectName['Project']['project_name']) . '_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>