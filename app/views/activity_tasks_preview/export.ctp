<?php
//exit();
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
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    1.7.6, 2011-02-27
 */

/** Error reporting */
error_reporting(E_ALL);

date_default_timezone_set('Europe/Paris');
$orders = $this->requestAction('/admin_task/getTaskSettings');
$isManual = isset($companyConfigs['manual_consumed']) ? $companyConfigs['manual_consumed'] : 0;
function toData($orders, $id, $taskName, $prio, $status, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain,$workloadInitial = null,$startInitial = null,$endInitial = null){
    $data = array($id);
    foreach($orders as $key){
        list($word, $show) = explode('|', $key);
        if( !intval($show) )continue;
        switch($word){
            case 'Task':
                $data[] = $taskName;
            break;
            case 'Order':
                continue;
            break;
            case 'AssignedTo':
                $data[] = $assign;
            break;
            case 'Priority':
                $data[] = $prio;
            break;
            case 'Status':
                $data[] = $status;
            break;
            case 'Profile':
                $data[] = $profile;
            break;
            case 'Startdate':
                $data[] = $start;
            break;
            case 'Enddate':
                $data[] = $end;
            break;
            case 'Duration':
                $data[] = $duration;
            break;
            case 'Predecessor':
                $data[] = $predecessor;
            break;
            case 'Workload':
                $data[] = $workload;
            break;
            case 'Overload':
                $data[] = $overload;
            break;
            case 'Consumed':
                $data[] = $cons;
            break;
            case 'ManualConsumed':
                if( isset($companyConfigs['manual_consumed']) && $companyConfigs['manual_consumed'] )
                    $data[] = $manual;
            break;
            case 'InUsed':
                $data[] = $wait;
            break;
            case 'Completed':
                $data[] = $comp;
            break;
            case 'Remain':
                $data[] = $remain;
            break;
            case 'Initialworkload':
                if( $workloadInitial !== null )
                    $data[] = $workloadInitial;
            break;
            case 'Initialstartdate':
                if( $startInitial !== null )
                    $data[] = $startInitial;
            break;
            case 'Initialenddate':
                if( $endInitial !== null )
                    $data[] = $endInitial;
            break;
        }
    }
    return $data;
}

$i18n = $this->requestAction('/translations/getByLang/Project_Task');
/** PHPExcel */
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Read from Excel5 (.xls) template
$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . "user_view.xls");

    App::import("vendor", "str_utility");
    $str_utility = new str_utility();
    
    $title = __("Activity Tasks of ".$activityName['Activity']['name'],true);
    $head = array('ID');
    foreach($orders as $key){
        list($word, $show) = explode('|', $key);
        if( $word == 'Order' || (!$isManual && $word == 'ManualConsumed') || !intval($show) )continue;
        if( in_array($word, array('Initialstartdate', 'Initialworkload', 'Initialenddate')) && $activityName['Activity']['off_freeze'] == 0 )
            continue;
        $head[] = $i18n[$word];
    }
    $k=0;//$g =1;
    $name_column = $columns;
    foreach ($head as $hea) {
        $j = 2;
        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A1', $title)
                            ->setCellValue($name_column[$k].$j, $hea);
        $objPHPExcel->getActiveSheet()->getColumnDimension($name_column[$k])->setAutoSize(true); 
        //$objPHPExcel->getActiveSheet()->mergeCells($name_column[$k].1); 
        $objPHPExcel->getActiveSheet()->mergeCells('A1:M1');
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);                
        $k++;         
    }
    
    $objPHPExcel->getActiveSheet()->getStyle($name_column['0'].$j.":".$name_column[$k-1].$j)->getFill()
                                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('CCCCCCCC');  
    $activityTask['children'];
    $_activityTask = $activityTask['children'][0];
    $id = 'root';
    $taskName = $_activityTask['task_title'];
    $prio = '';
    $status = !empty($_activityTask['task_status_text']) ? $_activityTask['task_status_text'] : '';
    $ass = !empty($_activityTask['task_assign_to_text']) ? implode(', ', $_activityTask['task_assign_to_text']) : '';
    $assign = $ass;
    $start = $_activityTask['task_start_date'];
    $end = $_activityTask['task_end_date'];
    $duration = $_activityTask['duration'];
    $predecessor = isset($_activityTask['predecessor']) ? $_activityTask['predecessor'] : '';
    $workload = $_activityTask['estimated'];
    $cons = $_activityTask['consumed'];
    $wait = !empty($_activityTask['wait']) ? $_activityTask['wait'] : 0;
    $comp = $_activityTask['completed'];
    $remain = $_activityTask['remain'];
    $overload = $isManual ? $_activityTask['manual_overload'] : $_activityTask['overload'];
    $profile = isset($_activityTask['profile_text']) ? $_activityTask['profile_text'] : '';
    $manual = isset($_activityTask['manual_consumed']) ? $_activityTask['manual_consumed'] : 0;
    if($settingP['ProjectSetting']['show_freeze']==1){
        if($checkP['Activity']['is_freeze']==1){
            $workloadInitial = isset($_activityTask['initial_estimated'])?$_activityTask['initial_estimated']:0;
            $startInitial = isset($_activityTask['initial_task_start_date'])?$_activityTask['initial_task_start_date']:'';
            $endInitial = isset($_activityTask['initial_task_end_date'])?$_activityTask['initial_task_end_date']:'';
        }else{
            $workloadInitial = 0;
            $startInitial = '';
            $endInitial = '';
        }
        $datas = toData($orders, $id, $taskName, $prio, $status, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain,$workloadInitial,$startInitial,$endInitial);
    }else{
        $datas = toData($orders, $id, $taskName, $prio, $status, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain );
    }
    $k = 0;
    foreach ($datas as $data) {
        $j = 3;
            $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue($name_column[$k].$j, $data); 
        $k++;
    }
    $j=4;
    
    foreach($_activityTask['children'] as $parts)
    {
        $id = '';
        $taskName = ' ---- '.$parts['task_title'];
        $prio = !empty($parts['task_priority_text']) ? $parts['task_priority_text'] : '';
        $status = !empty($parts['task_status_text']) ? $parts['task_status_text'] : '';
        if(!empty($parts['task_status_text']) && !empty($parts['task_status_id']) && !empty($parts['children'])){
            $listStatusChildren = Set::combine($parts['children'], '{n}.task_status_id', '{n}.task_status_id');
            if(count($listStatusChildren) == 1){ // co 1 trang thai
                $statusId = array_shift($listStatusChildren);
                $status = !empty($projectStatus) && !empty($projectStatus[$statusId]) ? $projectStatus[$statusId] : '';
            } else {
                $status = !empty($statusOfCompanies['ProjectStatus']['name']) ? $statusOfCompanies['ProjectStatus']['name'] : '';
            }
        }
        $ass = !empty($parts['task_assign_to_text']) ? implode(', ', $parts['task_assign_to_text']) : '';
        $assign = $ass;
        $start = $parts['task_start_date'];
        $end = $parts['task_end_date'];
        $duration = $parts['duration'];
        $predecessor = isset($parts['predecessor']) ? $parts['predecessor'] : '';
        $workload = $parts['estimated'];
        $cons = $parts['consumed'];
        $wait = !empty($parts['wait']) ? $parts['wait'] : 0;
        $comp = $parts['completed'];
        $remain = $parts['remain'];
        $overload = $isManual ? $parts['manual_overload'] : $parts['overload'];
        $profile = isset($parts['profile_text']) ? $parts['profile_text'] : '';
        $manual = isset($parts['manual_consumed']) ? $parts['manual_consumed'] : 0;
        if($settingP['ProjectSetting']['show_freeze']==1){
            if($checkP['Activity']['is_freeze']==1){
                $workloadInitial = isset($parts['initial_estimated'])?$parts['initial_estimated']:0;
                $startInitial = isset($parts['initial_task_start_date'])?$parts['initial_task_start_date']:'';
                $endInitial = isset($parts['initial_task_end_date'])?$parts['initial_task_end_date']:'';
            }else{
                $workloadInitial = 0;
                $startInitial = '';
                $endInitial = '';
            }
            $datas = toData($orders, $id, $taskName, $prio, $status, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain,$workloadInitial,$startInitial,$endInitial);
        }else{
            $datas = toData($orders, $id, $taskName, $prio, $status, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain );
        }
        $ks=0;
        foreach ($datas as $data) {
                $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column[$ks].$j, $data);                                
                $ks++; 
        }
        $j++;
        if(!empty($parts['children'])){
            foreach($parts['children'] as $phases){
                $id = !empty($phases['id']) && $phases['id'] < 999999999 ? $phases['id'] : '';
                $taskName = ' ------ '.$phases['task_title'];
                $prio = !empty($phases['task_priority_text']) ? $phases['task_priority_text'] : '';
                $status = !empty($phases['task_status_text']) ? $phases['task_status_text'] : '';
                $ass = !empty($phases['task_assign_to_text']) ? implode(', ', $phases['task_assign_to_text']) : '';
                $assign = $ass;
                $start = $phases['task_start_date'];
                $end = $phases['task_end_date'];
                $duration = $phases['duration'];
                $predecessor = isset($phases['predecessor']) ? $phases['predecessor'] : '';
                $workload = $phases['estimated'];
                $cons = $phases['consumed'];
                $wait = !empty($phases['wait']) ? $phases['wait'] : 0;
                $comp = $phases['completed'];
                $remain = $phases['remain'];
                $overload = $isManual ? $phases['manual_overload'] : $phases['overload'];
                $profile = isset($phases['profile_text']) ? $phases['profile_text'] : '';
                $manual = isset($phases['manual_consumed']) ? $phases['manual_consumed'] : 0;
                if($settingP['ProjectSetting']['show_freeze']==1){
                    if($checkP['Activity']['is_freeze']==1){
                        $workloadInitial = isset($phases['initial_estimated'])?$phases['initial_estimated']:0;
                        $startInitial = isset($phases['initial_task_start_date'])?$phases['initial_task_start_date']:'';
                        $endInitial = isset($phases['initial_task_end_date'])?$phases['initial_task_end_date']:'';
                    }else{
                        $workloadInitial = 0;
                        $startInitial = '';
                        $endInitial = '';
                    }
                    $datas = toData($orders, $id, $taskName, $prio, $status, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain,$workloadInitial,$startInitial,$endInitial);
                }else{
                    $datas = toData($orders, $id, $taskName, $prio, $status, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain );
                }
                $_k=0;
                foreach ($datas as $data) {
                        $objPHPExcel->setActiveSheetIndex(0)
                                        ->setCellValue($name_column[$_k].$j, $data);                                
                        $_k++; 
                }
                $j++;
            }
        }
    }
    
// Set active sheet index to the first sheet, so Excel opens this as the first sheet

$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client?s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="activity_tasks_of_'.str_replace(" ",'_',$activityName['Activity']['name']).'_'.date('H_i_s_d_m_Y').'.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;        
?>