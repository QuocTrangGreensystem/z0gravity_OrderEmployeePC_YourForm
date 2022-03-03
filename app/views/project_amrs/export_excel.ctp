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

$view_content = $this->Xml->unserialize($view_content);
App::import("vendor", "str_utility");
$str_utility = new str_utility();
$title = __("Project AMR of " . $project_name, true);
$head = array('No', 'Title', 'Content');
$k = 0;
$name_column = $columns;
foreach ($head as $hea) {
    $j = 2;
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', $title)
            ->setCellValue($name_column[$k] . $j, $hea);
    $k++;
}
$objPHPExcel->getActiveSheet()->getStyle($name_column['0'] . $j . ":" . $name_column[$k - 1] . $j)->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()->setARGB('CCCCCCCC');
$i = 0;
foreach ($view_content as $key => $value) {
    if (isset($value["ProjectDetail"])) {
        foreach ($value["ProjectDetail"] as $key1 => $value1) {
            if (!is_array($value1)) {
                unset($view_content["UserView"]["ProjectDetail"]);
                $view_content["UserView"]["ProjectDetail"]['0'] = $value["ProjectDetail"];
            }
        }
    }
    if (isset($value["ProjectAmr"])) {
        foreach ($value["ProjectAmr"] as $key1 => $value1) {
            if (!is_array($value1)) {
                unset($view_content["UserView"]["ProjectAmr"]);
                $view_content["UserView"]["ProjectAmr"]['0'] = $value["ProjectAmr"];
            }
        }
    }
}
$i = 0;
foreach ($view_content as $key => $value) {
    $j = 3;
    foreach ($value["ProjectAmr"] as $key1 => $value1) {
        $i++;
        foreach ($value1 as $field_name => $alias) {

            switch ($field_name) {
                case 'weather': {
                        $objDrawing = new PHPExcel_Worksheet_Drawing();
                        $objDrawing->setName('Global Weather');
                        $objDrawing->setDescription('Global Weather');
                        $objDrawing->setPath('./weathers/' . $weather . '.png');
                        $objDrawing->setCoordinates($name_column['2'] . $j);
                        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                        $weather = '';
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "Weather")
                                ->setCellValue($name_column['2'] . $j, $weather);
                        $objPHPExcel->getActiveSheet()->getRowDimension($j)->setRowHeight(21);
                        break;
                    }
                case 'project_amr_program_id': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "Program")
                                ->setCellValue($name_column['2'] . $j, $amr_program);
                        break;
                    }
                case 'project_amr_sub_program_id': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "Sub Program")
                                ->setCellValue($name_column['2'] . $j, $amr_sub_program);
                        break;
                    }
                case 'project_amr_category_id': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "Category")
                                ->setCellValue($name_column['2'] . $j, $amr_category);
                        break;
                    }
                case 'project_amr_sub_category_id': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "Sub category")
                                ->setCellValue($name_column['2'] . $j, $amr_sub_category);
                        break;
                    }
                case 'project_manager_id': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "Project Manager")
                                ->setCellValue($name_column['2'] . $j, $project_manager);
                        break;
                    }
                case 'budget': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "Budget")
                                ->setCellValue($name_column['2'] . $j, $budget . " " . $currency);
                        break;
                    }
                case 'project_amr_status_id': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "Status")
                                ->setCellValue($name_column['2'] . $j, $amr_status);
                        break;
                    }
                case 'project_phases_id': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "Current Phase")
                                ->setCellValue($name_column['2'] . $j, $amr_phase);
                        break;
                    }
                case 'project_amr_cost_control_id': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "Cost Control")
                                ->setCellValue($name_column['2'] . $j, $amr_cost_control);
                        break;
                    }
                case 'project_amr_organization_id': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "Organization")
                                ->setCellValue($name_column['2'] . $j, $amr_organization);
                        break;
                    }
                case 'project_amr_plan_id': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "Planning")
                                ->setCellValue($name_column['2'] . $j, $amr_plan);
                        break;
                    }
                case 'project_amr_perimeter_id': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "Perimeter")
                                ->setCellValue($name_column['2'] . $j, $amr_perimeter);
                        break;
                    }
                case 'project_amr_risk_control_id': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "Risk Control")
                                ->setCellValue($name_column['2'] . $j, $amr_risk_control);
                        break;
                    }
                case 'project_amr_problem_control_id': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "Problem control")
                                ->setCellValue($name_column['2'] . $j, $amr_problem_control);
                        break;
                    }
                case 'project_amr_risk_information': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "Risk information")
                                ->setCellValue($name_column['2'] . $j, $amr_risk_info);
                        break;
                    }
                case 'project_amr_problem_information': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "Problem")
                                ->setCellValue($name_column['2'] . $j, $amr_problem_info);
                        break;
                    }
                case 'project_amr_solution': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "Solution")
                                ->setCellValue($name_column['2'] . $j, $amr_solution);
                        break;
                    }
                case 'project_amr_solution_description': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "Solution description")
                                ->setCellValue($name_column['2'] . $j, $amr_solution_decs);
                        break;
                    }
                case 'project_amr_mep_date': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "MEP date")
                                ->setCellValue($name_column['2'] . $j, $str_utility->convertToVNDate($mepdate));
                        break;
                    }
                case 'project_amr_progression': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "Progression")
                                ->setCellValue($name_column['2'] . $j, $progress);
                        break;
                    }
                case 'assign_to_pc': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "% Assigned to profit center")
                                ->setCellValue($name_column['2'] . $j, $assginProfitCenter);
                        break;
                    }
                case 'assign_to_employee': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "% Assigned to employee")
                                ->setCellValue($name_column['2'] . $j, $assgnEmployee);
                        break;
                    }
                case 'cost_control_weather': {
                        $objDrawing = new PHPExcel_Worksheet_Drawing();
                        $objDrawing->setName('Cost Control weather');
                        $objDrawing->setDescription('Cost Control weather');
                        $objDrawing->setPath('./weathers/' . $cost_control_weather . '.png');
                        $objDrawing->setCoordinates($name_column['2'] . $j);
                        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                        $cost_control_weather = '';
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "Cost Control weather")
                                ->setCellValue($name_column['2'] . $j, $cost_control_weather);
                        $objPHPExcel->getActiveSheet()->getRowDimension($j)->setRowHeight(21);
                        break;
                    }
                case 'planning_weather': {
                        $objDrawing = new PHPExcel_Worksheet_Drawing();
                        $objDrawing->setName('Planning weather');
                        $objDrawing->setDescription('Planning weather');
                        $objDrawing->setPath('./weathers/' . $planning_weather . '.png');
                        $objDrawing->setCoordinates($name_column['2'] . $j);
                        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                        $planning_weather = '';
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "Planning weather")
                                ->setCellValue($name_column['2'] . $j, $planning_weather);
                        $objPHPExcel->getActiveSheet()->getRowDimension($j)->setRowHeight(21);
                        break;
                    }
                case 'risk_control_weather': {
                        $objDrawing = new PHPExcel_Worksheet_Drawing();
                        $objDrawing->setName('Risk Control weather');
                        $objDrawing->setDescription('Risk Control weather');
                        $objDrawing->setPath('./weathers/' . $risk_control_weather . '.png');
                        $objDrawing->setCoordinates($name_column['2'] . $j);
                        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                        $risk_control_weather = '';
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "Risk Control weather")
                                ->setCellValue($name_column['2'] . $j, $risk_control_weather);
                        $objPHPExcel->getActiveSheet()->getRowDimension($j)->setRowHeight(21);
                        break;
                    }
                case 'organization_weather': {
                        $objDrawing = new PHPExcel_Worksheet_Drawing();
                        $objDrawing->setName('Organization weather');
                        $objDrawing->setDescription('Organization weather');
                        $objDrawing->setPath('./weathers/' . $organization_weather . '.png');
                        $objDrawing->setCoordinates($name_column['2'] . $j);
                        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                        $organization_weather = '';
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "Organization weather")
                                ->setCellValue($name_column['2'] . $j, $organization_weather);
                        $objPHPExcel->getActiveSheet()->getRowDimension($j)->setRowHeight(21);
                        break;
                    }
                case 'perimeter_weather': {
                        $objDrawing = new PHPExcel_Worksheet_Drawing();
                        $objDrawing->setName('Perimeter weather');
                        $objDrawing->setDescription('Perimeter weather');
                        $objDrawing->setPath('./weathers/' . $perimeter_weather . '.png');
                        $objDrawing->setCoordinates($name_column['2'] . $j);
                        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                        $perimeter_weather = '';
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "Perimeter weather")
                                ->setCellValue($name_column['2'] . $j, $perimeter_weather);
                        $objPHPExcel->getActiveSheet()->getRowDimension($j)->setRowHeight(21);
                        break;
                    }
                case 'issue_control_weather': {
                        $objDrawing = new PHPExcel_Worksheet_Drawing();
                        $objDrawing->setName('Issue Control weather');
                        $objDrawing->setDescription('Issue Control weather');
                        $objDrawing->setPath('./weathers/' . $issue_control_weather . '.png');
                        $objDrawing->setCoordinates($name_column['2'] . $j);
                        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                        $issue_control_weather = '';
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, "Issue Control weather")
                                ->setCellValue($name_column['2'] . $j, $issue_control_weather);
                        $objPHPExcel->getActiveSheet()->getRowDimension($j)->setRowHeight(21);
                        break;
                    }
                case 'md_validated': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, __("M.D Budget", true))
                                ->setCellValue($name_column['2'] . $j, $md_validated);
                        break;
                    }
                case 'md_engaged': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, __("M.D Engaged/Consumed", true))
                                ->setCellValue($name_column['2'] . $j, $md_engaged);
                        break;
                    }
                case 'md_forecasted': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, __("M.D Remain", true))
                                ->setCellValue($name_column['2'] . $j, $md_forecasted);
                        break;
                    }
                case 'md_variance': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, __("M.D Variance", true))
                                ->setCellValue($name_column['2'] . $j, $md_variance);
                        break;
                    }
                case 'validated_currency_id': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, __("Budget Currency", true))
                                ->setCellValue($name_column['2'] . $j, $validated_currency);
                        break;
                    }
                case 'engaged_currency_id': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, __("Engaged/Consumed Currency", true))
                                ->setCellValue($name_column['2'] . $j, $engaged_currency);
                        break;
                    }
                case 'forecasted_currency_id': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, __("Remain Currency", true))
                                ->setCellValue($name_column['2'] . $j, $forecasted_currency);
                        break;
                    }
                case 'variance_currency_id': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, __("Variance Currency", true))
                                ->setCellValue($name_column['2'] . $j, $variance_currency);
                        break;
                    }
                case 'validated': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, __("Budget", true))
                                ->setCellValue($name_column['2'] . $j, $validated);
                        break;
                    }
                case 'engaged': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, __("Engaged/Consumed", true))
                                ->setCellValue($name_column['2'] . $j, $engaged);
                        break;
                    }
                case 'forecasted': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, __("Remain", true))
                                ->setCellValue($name_column['2'] . $j, $forecasted);
                        break;
                    }
                case 'variance': {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column['0'] . $j, $i)
                                ->setCellValue($name_column['1'] . $j, __("Variance", true))
                                ->setCellValue($name_column['2'] . $j, $variance);
                        break;
                    }
            }
        }$j++;
    }
}

// Rename sheet    
$objPHPExcel->getActiveSheet()->setTitle('Project detail');
$f = $j + 2;
/*
$objDrawing = new PHPExcel_Worksheet_Drawing();
$objDrawing->setName('global');
$objDrawing->setDescription('Global');
//$objDrawing->setPath('./img/front/global-logo.jpg');
$objDrawing->setCoordinates($name_column['1'] . $f);
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
$objPHPExcel->getActiveSheet()->getRowDimension($f)->setRowHeight(13);
$objPHPExcel->getActiveSheet()->getStyle($name_column['1'] . $f)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle($name_column['1'] . $f)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
*/

// Redirect output to a client�s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="projects_kpi_' . str_replace(" ", '_', $project_name) . '_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>