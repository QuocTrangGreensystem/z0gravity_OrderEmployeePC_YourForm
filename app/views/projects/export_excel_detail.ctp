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
$title = __d(sprintf($_domain, 'Details'), "Project details", true) . ' ' . $project_name['Project']['project_name'];
$head = array('No', 'Title', 'Content');
$k = 0;
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(150);

$objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle('A2')->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()->setARGB('CCCCCCCC');

$objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A2', $title);
foreach ($projects as $i => $project) {
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
                        ->setCellValue('A' . ($j+1), $project['Project']['project_name']);
                break;
            case "long_project_name":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $project['Project']['long_project_name']);
                break;
            case "project_code_1":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $project['Project']['project_code_1']);
                break;
            case "project_code_2":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $project['Project']['project_code_2']);
                break;
            case "company_id":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $project['Company']['company_name']);
                break;
            case "project_manager_id":
                $manager[] = $project['Employee']['fullname'];
                if(!empty($project['ProjectEmployeeManager'])){
                    foreach($project['ProjectEmployeeManager'] as $key => $val){
                        if($val['type'] === 'PM'){
                            $mBackup = !empty($employees[$val['project_manager_id']]) ? $employees[$val['project_manager_id']] : '';
                            if(!empty($mBackup)){
                                $manager[] = $mBackup;
                            }
                            unset($project['ProjectEmployeeManager'][$key]);
                        }

                    }
                }
                $manager = !empty($manager) ? implode(', ', $manager) : '';
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $manager);
                break;
            case "project_priority_id":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $project['ProjectPriority']['priority']);
                break;
            case "project_status_id":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $project['ProjectStatus']['name']);
                break;
            case 'start_date':
            case 'date_1':
            case 'date_2':
            case 'date_3':
            case 'date_4':
            case 'date_5':
            case 'date_6':
            case 'date_7':
            case 'date_8':
            case 'date_9':
            case 'date_10':
            case 'date_11':
            case 'date_12':
            case 'date_13':
            case 'date_14':
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $str_utility->convertToVNDate($project['Project'][$field_name]));
                break;
            case "end_date":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $str_utility->convertToVNDate($project['Project']['end_date']));
                break;
            case "planed_end_date":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $str_utility->convertToVNDate($project['Project']['planed_end_date']));
                break;
            case "weather":
                $weather = "";
                if (!empty($project['ProjectAmr']))
                    $weather = $project['ProjectAmr'][0]['weather'];
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $weather);
                break;
            case "budget":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $project['Project']['budget']);
                break;
            case "project_phase_id":
                $phase = array();
                if(!empty($project['ProjectPhaseCurrent'])){
                    foreach($project['ProjectPhaseCurrent'] as $key => $val){
                        $phase[$val['project_phase_id']] = !empty($ProjectPhases[$val['project_phase_id']]) ? $ProjectPhases[$val['project_phase_id']] : '';
                    }
                }
                $phase = !empty($phase) ? implode(', ', $phase) : '';
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $phase);
                break;
            case "primary_objectives":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $project['Project']['primary_objectives']);
                break;
            case "project_objectives":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $project['Project']['project_objectives']);
                break;
            case "issues":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $project['Project']['issues']);
                break;
            case "constraint":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $project['Project']['constraint']);
                break;
            case "remark":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $project['Project']['remark']);
                break;
            case "complexity_id":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $project['ProjectComplexity']['name']);
                break;
            case "created_value":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $project['Project']['created_value']);
                break;
            case "primary_objectives":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $project['Project']['primary_objectives']);
                break;
            case "chief_business_id":
                $chief[] = !empty($employees[$project['Project']['chief_business_id']]) ? $employees[$project['Project']['chief_business_id']] : '';
                if(!empty($project['ProjectEmployeeManager'])){
                    foreach($project['ProjectEmployeeManager'] as $key => $val){
                        if($val['type'] === 'CB'){
                            $mBackup = !empty($employees[$val['project_manager_id']]) ? $employees[$val['project_manager_id']] : '';
                            if(!empty($mBackup)){
                                $chief[] = $mBackup . '(B)';
                            }
                            unset($project['ProjectEmployeeManager'][$key]);
                        }

                    }
                }
                $chief = !empty($chief) ? implode(', ', $chief) : '';
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $chief);
                break;
            case "technical_manager_id":
                $tech[] = !empty($employees[$project['Project']['technical_manager_id']]) ? $employees[$project['Project']['technical_manager_id']] : '';
                if(!empty($project['ProjectEmployeeManager'])){
                    foreach($project['ProjectEmployeeManager'] as $key => $val){
                        if($val['type'] === 'TM'){
                            $mBackup = !empty($employees[$val['project_manager_id']]) ? $employees[$val['project_manager_id']] : '';
                            if(!empty($mBackup)){
                                $tech[] = $mBackup . '(B)';
                            }
                            unset($project['ProjectEmployeeManager'][$key]);
                        }

                    }
                }
                $tech = !empty($tech) ? implode(', ', $tech) : '';
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $tech);
                break;
            case "functional_leader_id":
                $lead[] = !empty($employees[$project['Project']['functional_leader_id']]) ? $employees[$project['Project']['functional_leader_id']] : '';
                if(!empty($project['ProjectEmployeeManager'])){
                    foreach($project['ProjectEmployeeManager'] as $key => $val){
                        if($val['type'] === 'FL'){
                            $mBackup = !empty($employees[$val['project_manager_id']]) ? $employees[$val['project_manager_id']] : '';
                            if(!empty($mBackup)){
                                $lead[] = $mBackup . '(B)';
                            }
                            unset($project['ProjectEmployeeManager'][$key]);
                        }

                    }
                }
                $lead = !empty($lead) ? implode(', ', $lead) : '';
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $lead);
                break;
            case "uat_manager_id":
                $uat[] = !empty($employees[$project['Project']['uat_manager_id']]) ? $employees[$project['Project']['uat_manager_id']] : '';
                if(!empty($project['ProjectEmployeeManager'])){
                    foreach($project['ProjectEmployeeManager'] as $key => $val){
                        if($val['type'] === 'UM'){
                            $mBackup = !empty($employees[$val['project_manager_id']]) ? $employees[$val['project_manager_id']] : '';
                            if(!empty($mBackup)){
                                $uat[] = $mBackup . '(B)';
                            }
                            unset($project['ProjectEmployeeManager'][$key]);
                        }

                    }
                }
                $uat = !empty($uat) ? implode(', ', $uat) : '';
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $uat);
                break;
            case "project_type_id":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $project['ProjectType']['project_type']);
                break;
            case "project_sub_type_id":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $project['ProjectSubType']['project_sub_type']);
                break;
            case "project_amr_program_id":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $project['ProjectAmrProgram']['amr_program']);
                break;
            case "project_amr_sub_program_id":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $project['ProjectAmrSubProgram']['amr_sub_program']);
                break;
            case "activity_id":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $project['Activities']['name']);
                break;
            case "budget_customer_id":
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $project['BudgetCustomer']['name']);
                break;
            case 'list_1':
            case 'list_2':
            case 'list_3':
            case 'list_4':
            case 'list_5':
            case 'list_6':
            case 'list_7':
            case 'list_8':
            case 'list_9':
            case 'list_10':
            case 'list_11':
            case 'list_12':
            case 'list_13':
            case 'list_14':
                $v = $project['Project'][$field_name];
                $value = isset($datasets[$field_name][$v]) ? $datasets[$field_name][$v] : '';
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $value);
                break;
            case 'yn_1':
            case 'yn_2':
            case 'yn_3':
            case 'yn_4':
            case 'yn_5':
            case 'yn_6':
            case 'yn_7':
            case 'yn_8':
            case 'yn_9':
                $value = $project['Project'][$field_name] ? __('Yes', true) : __('No', true);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $value);
                break;
            case 'price_1':
            case 'price_2':
            case 'price_3':
            case 'price_4':
            case 'price_5':
            case 'price_6':
            case 'price_7':
            case 'price_8':
            case 'price_9':
            case 'price_10':
            case 'price_11':
            case 'price_12':
            case 'price_13':
            case 'price_14':
            case 'price_15':
            case 'price_16':
                $value = $project['Project'][$field_name] ? $project['Project'][$field_name] . ' €' : '';
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $value);
                break;
            case 'number_1':
            case 'number_2':
            case 'number_3':
            case 'number_4':
            case 'number_5':
            case 'number_6':
            case 'number_7':
            case 'number_8':
            case 'number_9':
            case 'number_10':
            case 'number_11':
            case 'number_12':
            case 'number_13':
            case 'number_14':
            case 'number_15':
            case 'number_16':
            case 'number_17':
            case 'number_18':
                $value = $project['Project'][$field_name] ? $project['Project'][$field_name] : '';
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $value);
                break;
            case 'editor_1':
            case 'editor_2':
            case 'editor_3':
            case 'editor_4':
            case 'editor_5':
                $value = $project['Project'][$field_name] ? $project['Project'][$field_name] : '';
                $value = strip_tags($value);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $value);
                
                break;
            case 'date_mm_yy_1':
            case 'date_mm_yy_2':
            case 'date_mm_yy_3':
            case 'date_mm_yy_4':
            case 'date_mm_yy_5':
                $value = $project['Project'][$field_name] ? $project['Project'][$field_name] : '';
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $value);
                break;
            case 'date_yy_1':
            case 'date_yy_2':
            case 'date_yy_3':
            case 'date_yy_4':
            case 'date_yy_5':
                $value = $project['Project'][$field_name] ? $project['Project'][$field_name] : '';
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $value);
                break;
            case 'team':
                $value = $project['Project'][$field_name] ? $listTeam[$project['Project'][$field_name]] : '';
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $value);
                break;
            case 'list_muti_1':
                $_v = '';
                if(!empty($_listMuti['project_list_multi_1'])){
                    foreach ($_listMuti['project_list_multi_1'] as $value) {
                        if(empty($_v)){
                            $_v .= isset($datasets[$field_name][$value]) ? $datasets[$field_name][$value] : '';
                        } else {
                            $_v .= ', ' . (isset($datasets[$field_name][$value]) ? $datasets[$field_name][$value] : '');
                        }
                    }
                }
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $_v);
                break;
            case 'list_muti_2':
                $_v = '';
                if(!empty($_listMuti['project_list_multi_2'])){
                    foreach ($_listMuti['project_list_multi_2'] as $value) {
                        if(empty($_v)){
                            $_v .= isset($datasets[$field_name][$value]) ? $datasets[$field_name][$value] : '';
                        } else {
                            $_v .= ', ' . (isset($datasets[$field_name][$value]) ? $datasets[$field_name][$value] : '');
                        }
                    }
                }
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $_v);
                break;
            case 'list_muti_3':
                $_v = '';
                if(!empty($_listMuti['project_list_multi_3'])){
                    foreach ($_listMuti['project_list_multi_3'] as $value) {
                        if(empty($_v)){
                            $_v .= isset($datasets[$field_name][$value]) ? $datasets[$field_name][$value] : '';
                        } else {
                            $_v .= ', ' . (isset($datasets[$field_name][$value]) ? $datasets[$field_name][$value] : '');
                        }
                    }
                }
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $_v);
                break;
            case 'list_muti_4':
                $_v = '';
                if(!empty($_listMuti['project_list_multi_4'])){
                    foreach ($_listMuti['project_list_multi_4'] as $value) {
                        if(empty($_v)){
                            $_v .= isset($datasets[$field_name][$value]) ? $datasets[$field_name][$value] : '';
                        } else {
                            $_v .= ', ' . (isset($datasets[$field_name][$value]) ? $datasets[$field_name][$value] : '');
                        }
                    }
                }
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $_v);
                break;
            case 'list_muti_5':
                $_v = '';
                if(!empty($_listMuti['project_list_multi_5'])){
                    foreach ($_listMuti['project_list_multi_5'] as $value) {
                        if(empty($_v)){
                            $_v .= isset($datasets[$field_name][$value]) ? $datasets[$field_name][$value] : '';
                        } else {
                            $_v .= ', ' . (isset($datasets[$field_name][$value]) ? $datasets[$field_name][$value] : '');
                        }
                    }
                }
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $_v);
                break;
            case 'list_muti_6':
                $_v = '';
                if(!empty($_listMuti['project_list_multi_6'])){
                    foreach ($_listMuti['project_list_multi_6'] as $value) {
                        if(empty($_v)){
                            $_v .= isset($datasets[$field_name][$value]) ? $datasets[$field_name][$value] : '';
                        } else {
                            $_v .= ', ' . (isset($datasets[$field_name][$value]) ? $datasets[$field_name][$value] : '');
                        }
                    }
                }
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $_v);
                break;
            case 'list_muti_7':
                $_v = '';
                if(!empty($_listMuti['project_list_multi_7'])){
                    foreach ($_listMuti['project_list_multi_7'] as $value) {
                        if(empty($_v)){
                            $_v .= isset($datasets[$field_name][$value]) ? $datasets[$field_name][$value] : '';
                        } else {
                            $_v .= ', ' . (isset($datasets[$field_name][$value]) ? $datasets[$field_name][$value] : '');
                        }
                    }
                }
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $_v);
                break;
            case 'list_muti_8':
                $_v = '';
                if(!empty($_listMuti['project_list_multi_8'])){
                    foreach ($_listMuti['project_list_multi_8'] as $value) {
                        if(empty($_v)){
                            $_v .= isset($datasets[$field_name][$value]) ? $datasets[$field_name][$value] : '';
                        } else {
                            $_v .= ', ' . (isset($datasets[$field_name][$value]) ? $datasets[$field_name][$value] : '');
                        }
                    }
                }
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $_v);
                break;
            case 'list_muti_9':
                $_v = '';
                if(!empty($_listMuti['project_list_multi_9'])){
                    foreach ($_listMuti['project_list_multi_9'] as $value) {
                        if(empty($_v)){
                            $_v .= isset($datasets[$field_name][$value]) ? $datasets[$field_name][$value] : '';
                        } else {
                            $_v .= ', ' . (isset($datasets[$field_name][$value]) ? $datasets[$field_name][$value] : '');
                        }
                    }
                }
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $_v);
                break;
            case 'list_muti_10':
                $_v = '';
                if(!empty($_listMuti['project_list_multi_10'])){
                    foreach ($_listMuti['project_list_multi_10'] as $value) {
                        if(empty($_v)){
                            $_v .= isset($datasets[$field_name][$value]) ? $datasets[$field_name][$value] : '';
                        } else {
                            $_v .= ', ' . (isset($datasets[$field_name][$value]) ? $datasets[$field_name][$value] : '');
                        }
                    }
                }
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $_v);
                break;
            case 'text_one_line_1':
            case 'text_one_line_2':
            case 'text_one_line_3':
            case 'text_one_line_4':
            case 'text_one_line_5':
            case 'text_one_line_6':
            case 'text_one_line_7':
            case 'text_one_line_8':
            case 'text_one_line_9':
            case 'text_one_line_10':
            case 'text_one_line_11':
            case 'text_one_line_12':
            case 'text_one_line_13':
            case 'text_one_line_14':
            case 'text_one_line_15':
            case 'text_one_line_16':
            case 'text_one_line_17':
            case 'text_one_line_18':
            case 'text_one_line_19':
            case 'text_one_line_20':
            case 'text_two_line_1':
            case 'text_two_line_2':
            case 'text_two_line_3':
            case 'text_two_line_4':
            case 'text_two_line_5':
            case 'text_two_line_6':
            case 'text_two_line_7':
            case 'text_two_line_8':
            case 'text_two_line_9':
            case 'text_two_line_10':
            case 'text_two_line_11':
            case 'text_two_line_12':
            case 'text_two_line_13':
            case 'text_two_line_14':
            case 'text_two_line_15':
            case 'text_two_line_16':
            case 'text_two_line_17':
            case 'text_two_line_18':
            case 'text_two_line_19':
            case 'text_two_line_20':
            case 'free_1':
            case 'free_2':
            case 'free_3':
            case 'free_4':
            case 'free_5':
                $value = $project['Project'][$field_name] ? $project['Project'][$field_name] : '';
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, $alias)
                        ->setCellValue('A' . ($j+1), $value);
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
header('Content-Disposition: attachment;filename="projects_detail_' . str_replace(" ", '_', $project_name['Project']['project_name']) . '_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>
