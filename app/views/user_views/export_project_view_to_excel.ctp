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
//$objPHPexcel_2 = PHPExcel_IOFactory::load(TEMPLATES . 'style.xls');
//$objWorksheet = $objPHPexcel_2->getActiveSheet();
// Read from Excel5 (.xls) template
$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . "user_view.xls");
App::import("vendor", "str_utility");
$str_utility = new str_utility();
$view_content = $this->Xml->unserialize($view_content);
$head = array();
$head[] = "No";
//debug($view_content);
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
foreach ($view_content as $key => $value) {
    foreach ($value["ProjectDetail"] as $key1 => $value1) {
        foreach ($value1 as $field_name => $alias) {
            $head[] = $alias;
        }
    }
}

$i = 0;
$k = 0;
$name_column = $columns;
foreach ($head as $hea) {
    $j = 2;

    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('B1', 'Project List')
            ->setCellValue($name_column[$k] . $j, $hea);
    $k++;
}
$k = $k - 1;
$objPHPExcel->getActiveSheet()->getStyle($name_column[$i] . $j . ":" . $name_column[$k] . $j)->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()->setARGB('CCCCCCCC');
$j = 3;

foreach ($projects as $project) {
    foreach ($view_content as $key => $value) {
        $ct = 1;
        foreach ($value["ProjectDetail"] as $key1 => $value1) {
            foreach ($value1 as $key => $value) {
                switch ($key) {
                    case "project_name":
                        $data = $project['Project']['project_name'];
                        break;
                    case "long_project_name":
                        $data = $project['Project']['long_project_name'];
                        break;
                    case "project_code_1":
                        $data = $project['Project']['project_code_1'];
                        break;
                     case "project_code_2":
                        $data = $project['Project']['project_code_2'];
                        break;
                    case "company_id":
                        $data = $project['Company']['company_name'];
                        break;
                    case "project_manager_id":
                        $data = $project["Employee"]["fullname"];
                        break;
                    case "project_priority_id":
                        $data = $project['ProjectPriority']['priority'];
                        break;
                    case "project_status_id":
                        $data = $project['ProjectStatus']['name'];
                        break;
                    case "project_type_id":
                        $data = $project['ProjectType']['project_type'];
                        break;
                    case "project_sub_type_id":
                        $data = $project['ProjectSubType']['project_sub_type'];
                        break;
                    case "complexity_id":
                        $data = $project['ProjectComplexity']['name'];
                        break;
                    case "created_value":
                        $data = $project['Project']['created_value'];
                        break;
                    case "project_amr_program_id":
                        $data = $project['ProjectAmrProgram']['amr_program'];
                        break;
                    case "project_amr_sub_program_id":
                        $data = $project['ProjectAmrSubProgram']['amr_sub_program'];
                        break;
                    case "chief_business_id":
                        $data = '';
                        foreach ($employees as $employee) {
                            if ($employee['Employee']['id'] == $project['Project']['chief_business_id']) {
                                $data = $employee['Employee']['first_name'] . ' ' . $employee['Employee']['last_name'];
                            }
                        }
                        break;
                    case "start_date":
                        $data = $str_utility->convertToVNDate($project['Project']['start_date']);
                        break;
                    case "end_date":
                        $data = $str_utility->convertToVNDate($project['Project']['end_date']);
                        break;
                    case "planed_end_date":
                        $data = $str_utility->convertToVNDate($project['Project']['planed_end_date']);
                        break;
                    case "weather_amr":
                        $weather = "";
                        if (!empty($project['ProjectAmr']))
                            $weather = $project['ProjectAmr'][0]['weather'];
                        $data = $weather;
                        break;
                    case "currency_id_amr":
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['Currency']['sign_currency'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "currency_id":
                        $data = $project['Currency']['sign_currency'];
                        break;
                    case "issue_control_weather_amr":
                        $issue_control_weather = "";
                        if (!empty($project['ProjectAmr']))
                            $issue_control_weather = $project['ProjectAmr'][0]['issue_control_weather'];
                        $data = $issue_control_weather;
                        break;
                    case "perimeter_weather_amr":
                        $perimeter_weather = "";
                        if (!empty($project['ProjectAmr']))
                            $perimeter_weather = $project['ProjectAmr'][0]['perimeter_weather'];
                        $data = $perimeter_weather;
                        break;
                    case "organization_weather_amr":
                        $organization_weather = "";
                        if (!empty($project['ProjectAmr']))
                            $organization_weather = $project['ProjectAmr'][0]['organization_weather'];
                        $data = $organization_weather;
                        break;
                    case "risk_control_weather_amr":
                        $risk_control_weather = "";
                        if (!empty($project['ProjectAmr']))
                            $risk_control_weather = $project['ProjectAmr'][0]['risk_control_weather'];
                        $data = $risk_control_weather;
                        break;
                    case "planning_weather_amr":
                        $planning_weather = "";
                        if (!empty($project['ProjectAmr']))
                            $planning_weather = $project['ProjectAmr'][0]['planning_weather'];
                        $data = $planning_weather;
                        break;
                    case "cost_control_weather_amr":
                        $cost_control_weather = "";
                        if (!empty($project['ProjectAmr']))
                            $cost_control_weather = $project['ProjectAmr'][0]['cost_control_weather'];
                        $data = $cost_control_weather;
                        break;
                    case "budget":
                        $data = $project['Project']['budget'];
                        break;
                    case "project_amr_problem_control_id_amr":
                        $project_amr_problem_control = "";
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectAmrProblemControl']['amr_problem_control'];
                                    break;
                                }
                            }
                        }
                        break;

                    case "project_amr_program_id_amr":
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectAmrProgram']['amr_program'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "project_amr_sub_program_id_amr":
                        $project_amr_problem_control = "";
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectAmrSubProgram']['amr_sub_program'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "project_amr_category_id_amr":
                        $project_amr_problem_control = "";
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectAmrCategory']['amr_category'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "project_amr_sub_category_id_amr":
                        $project_amr_problem_control = "";
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectAmrSubCategory']['amr_sub_category'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "budget_amr":
                        $project_amr_problem_control = "";
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectAmr']['budget'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "project_amr_status_id_amr":
                        $project_amr_problem_control = "";
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectAmrStatus']['amr_status'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "project_amr_mep_date_amr":
                        $project_amr_problem_control = "";
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $str_utility->convertToVNDate($amr['ProjectAmr']['project_amr_mep_date']);
                                    break;
                                }
                            }
                        }
                        break;
                    case "project_amr_progression_amr":
                        $project_amr_problem_control = "";
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectAmr']['project_amr_progression'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "project_phases_id_amr":
                        $project_amr_problem_control = "";
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectPhases']['name'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "project_amr_cost_control_id_amr":
                        $project_amr_problem_control = "";
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectAmrCostControl']['amr_cost_control'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "project_amr_organization_id_amr":
                        $project_amr_problem_control = "";
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectAmrOrganization']['amr_organization'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "project_amr_plan_id_amr":
                        $project_amr_problem_control = "";
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectAmrPlan']['amr_plan'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "project_amr_perimeter_id_amr":
                        $project_amr_problem_control = "";
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectAmrPerimeter']['amr_perimeter'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "project_amr_risk_control_id_amr":
                        $project_amr_problem_control = "";
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectAmrRiskControl']['amr_risk_control'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "project_amr_problem_control_id_amr":
                        $project_amr_problem_control = "";
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectAmrProblemControl']['amr_problem_control'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "project_amr_risk_information_amr":
                        $project_amr_problem_control = "";
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectAmr']['project_amr_risk_information'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "project_amr_problem_information_amr":
                        $project_amr_problem_control = "";
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectAmr']['project_amr_problem_information'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "project_amr_solution_amr":
                        $project_amr_problem_control = "";
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectAmr']['project_amr_solution'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "project_amr_solution_description_amr":
                        $project_amr_problem_control = "";
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectAmr']['project_amr_solution_description'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "project_amr_solution_description_amr":
                        $project_amr_problem_control = "";
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectAmr']['project_amr_solution_description'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "project_manager_id_amr":
                        $project_amr_problem_control = "";
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['Employee']['fullname'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "project_phase_id":
                        $data = $project['ProjectPhase']['name'];
                        break;
                    case "primary_objectives":
                        $data = $project['Project']['primary_objectives'];
                        break;
                    case "project_objectives":
                        $data = $project['Project']['project_objectives'];
                        break;
                    case "issues":
                        $data = $project['Project']['issues'];
                        break;
                    case "constraint":
                        $data = $project['Project']['constraint'];
                        break;
                    case "remark":
                        $data = $project['Project']['remark'];
                        break;
                    case "md_validated_amr":
                        $data = 0;
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectAmr']['md_validated'];

                                    break;
                                }
                            }
                        }
                        break;
                    case "md_engaged_amr":
                        $data = 0;
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectAmr']['md_engaged'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "md_forecasted_amr":
                        $data = 0;
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectAmr']['md_forecasted'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "md_variance_amr":
                        $data = 0;
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectAmr']['md_variance'];
                                    break;
                                }
                            }
                        }else
                            $data = 0;
                        break;
                    case "validated_currency_id_amr":
                        $data = '';
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['Currency']['sign_currency'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "engaged_currency_id_amr":
                        $data = '';
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['Currency']['sign_currency'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "forecasted_currency_id_amr":
                        $data = '';
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['Currency']['sign_currency'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "variance_currency_id_amr":
                        $data = '';
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['Currency']['sign_currency'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "validated_amr":
                        $data = 0;
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectAmr']['validated'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "engaged_amr":
                        $data = 0;
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectAmr']['engaged'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "forecasted_amr":
                        $data = 0;
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectAmr']['forecasted'];
                                    break;
                                }
                            }
                        }
                        break;
                    case "variance_amr":
                        $data = 0;
                        if (!empty($project['ProjectAmr'])) {
                            foreach ($amrs as $amr) {
                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                    $data = $amr['ProjectAmr']['variance'];
                                    break;
                                }
                            }
                        }
                        break;
                }
                switch ($data) {
                    case "sun":
                        $objDrawing = new PHPExcel_Worksheet_Drawing();
                        $objDrawing->setName('Global Weather');
                        $objDrawing->setDescription('Global Weather');
                        $objDrawing->setPath('./weathers/sun.png');
                        $objDrawing->setCoordinates($name_column[$ct] . $j);
                        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                        $objPHPExcel->getActiveSheet()->getRowDimension($j)->setRowHeight(21);
                        $data = '';
                        break;
                    case "cloud":
                        $objDrawing = new PHPExcel_Worksheet_Drawing();
                        $objDrawing->setName('Global Weather');
                        $objDrawing->setDescription('Global Weather');
                        $objDrawing->setPath('./weathers/cloud.png');
                        $objDrawing->setCoordinates($name_column[$ct] . $j);
                        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                        $objPHPExcel->getActiveSheet()->getRowDimension($j)->setRowHeight(21);
                        $data = '';
                        break;
                    case "rain":
                        $objDrawing = new PHPExcel_Worksheet_Drawing();
                        $objDrawing->setName('Global Weather');
                        $objDrawing->setDescription('Global Weather');
                        $objDrawing->setPath('./weathers/rain.png');
                        $objDrawing->setCoordinates($name_column[$ct] . $j);
                        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                        $objPHPExcel->getActiveSheet()->getRowDimension($j)->setRowHeight(21);
                        $data = '';
                        break;
                }
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue($name_column['0'] . $j, $j - 2)
                        ->setCellValue($name_column[$ct] . $j, $data);
                //$objPHPExcel->getActiveSheet()->getColumnDimension($name_column[$ct])->setAutoSize(true);                  

                $ct++;
            }
        }
        $j++;
    }
}
$j = $j - 1;
$ct = $ct - 1;
$objPHPExcel->getActiveSheet()->getStyle($name_column['0'] . "3:" . $name_column[$ct] . "" . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->getStyle($name_column['0'] . "3:" . $name_column[$ct] . "" . $j)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
$objPHPExcel->getActiveSheet()->getStyle($name_column['0'] . "3:" . $name_column[$ct] . "" . $j)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
$objPHPExcel->getActiveSheet()->getStyle($name_column['0'] . "3:" . $name_column[$ct] . "" . $j)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
$objPHPExcel->getActiveSheet()->getStyle($name_column['0'] . "3:" . $name_column[$ct] . "" . $j)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

// $objPHPExcel->getActiveSheet()->getStyle('A3:G'.$j)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle($name_column['0'] . "3:" . $name_column[$ct] . "" . $j)->getFill()->getStartColor()->setARGB('FFFFFFFF');

$f = $j + 2;
$objDrawing = new PHPExcel_Worksheet_Drawing();
$objDrawing->setName('global');
$objDrawing->setDescription('Global');
$objDrawing->setPath('./img/front/global-logo.jpg');
$objDrawing->setCoordinates('D' . $f);
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
$objPHPExcel->getActiveSheet()->getRowDimension($f)->setRowHeight(13);
$objPHPExcel->getActiveSheet()->getStyle('D' . $f)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('D' . $f)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client�s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="project_list_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
