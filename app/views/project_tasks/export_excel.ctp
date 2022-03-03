<?php
/**
* PHPExcel
* Copyright (C) 2006 - 2011 PHPExcel
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 2.1 of the License, or (at your option) any later version.
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
* You should have received a copy of the GNU Lesser General Public
* License along with this library; if not, write to the Free Software
* Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
* @category   PHPExcel
* @package    PHPExcel
* @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
* @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
* @version    1.7.6, 2011-02-27
**/
error_reporting(E_ALL);
date_default_timezone_set('Europe/Paris');
$orders = $this->requestAction('/admin_task/getTaskSettings');
$isManual = isset($companyConfigs['manual_consumed']) ? $companyConfigs['manual_consumed'] : 0;
function toData($orders, $taskName, $prio, $status, $milestone, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain, $eac, $amount, $progress_order, $progress_order_amount, $id_activity, $wait, $slider, $text, $unit_price, $consumed_euro, $remain_euro, $workload_euro, $estimated_euro, $bg_currency = '€', $workloadInitial = null,$startInitial = null,$endInitial = null){
    global $projectName;
    $data = array();
    foreach($orders as $key){
        list($word, $show) = explode('|', $key);
        if( !intval($show) )continue;
        switch($word){
            case 'Task':
                $data[] = $taskName;
            break;
            case 'ID':
				break;
			case 'Order':
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
            case 'Milestone':
                $data[] = $milestone;
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
            case '+/-':
                $data[] = $slider;
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
				$isManual = isset($companyConfigs['manual_consumed']) ? $companyConfigs['manual_consumed'] : 0;
				if(!$isManual) break;
                $data[] = isset($companyConfigs['manual_consumed']) && $companyConfigs['manual_consumed'] ? $manual : '';
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
            case 'EAC':
                $data[] = $eac;
            break;
            case 'Initialworkload':
				if($projectName['Project']['off_freeze'] == 0) break;
                $data[] = $projectName['Project']['off_freeze'] != 0 ? $workloadInitial : '';
            break;
            case '%progressorder':
                    $data[] = $progress_order . ' %';
            break;
            case 'Text':
                    $data[] = $text;
            break;
            case 'Attachment':
                    $data[] = '';
            break;
            case 'UnitPrice':
                    $data[] = $unit_price . ' '. $bg_currency;
            break;
            case 'Consumed€':
                    $data[] = $consumed_euro . ' '. $bg_currency;
            break;
            case 'Remain€':
                    $data[] = $remain_euro . ' '. $bg_currency;
            break;
            case 'Workload€':
                    $data[] = $workload_euro . ' '. $bg_currency;
            break;
            case 'Estimated€':
                    $data[] = $workload_euro . ' '. $bg_currency;
            break;
            case 'Initialstartdate':
				if($projectName['Project']['off_freeze'] == 0) break;
                $data[] = $projectName['Project']['off_freeze'] != 0 ? $startInitial : '';
            break;
            case 'Initialenddate':
				if($projectName['Project']['off_freeze'] == 0) break;
                $data[] = $projectName['Project']['off_freeze'] != 0 ? $endInitial : '';
            break;
            default:
                $word = substr($word, 0, 4);
                if($word == 'Amou'){
                    $data[] = $amount . ' '. $bg_currency;
                } else if ($word == '%pro'){
                    $data[] = $progress_order_amount . ' '. $bg_currency;
                }
            break;
        }
    }
    return $data;
}
/** PHPExcel */
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
$objPHPExcel = new PHPExcel();
$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . "user_view.xls");
$i18n = $this->requestAction('/translations/getByLang/Project_Task');
    App::import("vendor", "str_utility");
    $str_utility = new str_utility();
    if($typeExport){
        $_filename = 'project_tasks_of_';
    }else{
        $_filename = 'activity_tasks_of_';
    }
    $title = $projectName['Project']['project_name'];
    $head = array();
    foreach($orders as $key){
        list($word, $show) = explode('|', $key);
        if( $word == 'Order' || $word == 'ID' || (!$isManual && $word == 'ManualConsumed') || !intval($show) )continue;
        if( in_array($word, array('Initialstartdate', 'Initialworkload', 'Initialenddate')) && $projectName['Project']['off_freeze'] == 0 )
            continue;
        $head[] = $i18n[$word];
    }
    $k=0;
    $name_column = $columns;
    foreach($head as $hea) {
        $j = 2;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $title)->setCellValue($name_column[$k].$j, $hea);
        $objPHPExcel->getActiveSheet()->getColumnDimension($name_column[$k])->setAutoSize(true);
        //$objPHPExcel->getActiveSheet()->mergeCells($name_column[$k].1);
        $objPHPExcel->getActiveSheet()->mergeCells('A1:M1');
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $k++;
    }
    $objPHPExcel->getActiveSheet()->getStyle($name_column['0'].$j.":".$name_column[$k-1].$j)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('CCCCCCCC');
    // $id = 'root';
    $taskName = 'All Tasks';
    $prio = '';
    $status = '';
    $milestone = '';
    $assign = '';
    $start = $projectTasks['task_start_date'];
    $end = $projectTasks['task_end_date'];
    $duration = $projectTasks['duration'];
    $predecessor = isset($projectTasks['predecessor']) ? $projectTasks['predecessor'] : '';
    $workload = $projectTasks['estimated'] ? $projectTasks['estimated'] : 0;
    $eac = $projectTasks['eac'] ? $projectTasks['eac'] : 0;
    $cons = $projectTasks['consumed'];
    $wait = !empty($projectTasks['wait']) ? $projectTasks['wait'] : 0;
    $comp = $projectTasks['completed'];
    $remain = $projectTasks['remain'];
    $over = isset($projectTasks['overload']) ? $projectTasks['overload'] : 0;
    $overload = $isManual ? $projectTasks['manual_overload'] : $over;
    $profile = isset($projectTasks['profile_text']) ? $projectTasks['profile_text'] : '';
    $manual = isset($projectTasks['manual_consumed']) ? $projectTasks['manual_consumed'] : 0;
    $amount = isset($projectTasks['amount']) ? $projectTasks['amount'] : 0;
    $progress_order = isset($projectTasks['progress_order']) ? $projectTasks['progress_order'] : 0;
    $progress_order_amount = isset($projectTasks['progress_order_amount']) ? $projectTasks['progress_order_amount'] : 0;
    $id_activity = isset($projectTasks['id_activity']) ? $projectTasks['id_activity'] : '';
    $wait = isset($projectTasks['wait']) ? $projectTasks['wait'] : 0;
    $slider = isset($projectTasks['slider']) ? $projectTasks['slider'] : 0;
    $text = isset($projectTasks['text_1']) ? $projectTasks['text_1'] : '';
    $unit_price = isset($projectTasks['unit_price']) ? $projectTasks['unit_price'] : 0;
    $consumed_euro = isset($projectTasks['consumed_euro']) ? $projectTasks['consumed_euro'] : 0;
    $remain_euro = isset($projectTasks['remain_euro']) ? $projectTasks['remain_euro'] : 0;
    $workload_euro = isset($projectTasks['workload_euro']) ? $projectTasks['workload_euro'] : 0;
    $estimated_euro = isset($projectTasks['estimated_euro']) ? $projectTasks['estimated_euro'] : 0;
    if($settingP['ProjectSetting']['show_freeze'] == 1){
        if($checkP['Project']['is_freeze']==1){
            $workloadInitial = isset($projectTasks['initial_estimated'])?$projectTasks['initial_estimated']:0;
            $startInitial = isset($projectTasks['initial_task_start_date'])?$projectTasks['initial_task_start_date']:'';
            $endInitial = isset($projectTasks['initial_task_end_date'])?$projectTasks['initial_task_end_date']:'';
        }else{
            $workloadInitial = 0;
            $startInitial = '';
            $endInitial = '';
        }
        $datas = toData($orders, $taskName, $prio, $status, $milestone, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain, $eac, $amount, $progress_order, $progress_order_amount, $id_activity, $wait, $slider, $text, $unit_price, $consumed_euro, $remain_euro, $workload_euro, $estimated_euro, $bg_currency, $workloadInitial,$startInitial,$endInitial);
    }else{
        $datas = toData($orders, $taskName, $prio, $status, $milestone, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain, $eac, $amount, $progress_order, $progress_order_amount, $id_activity, $wait, $slider, $text, $unit_price, $consumed_euro, $remain_euro, $workload_euro,$estimated_euro, $bg_currency);
    }
    $k = 0;
	
    foreach($datas as $data) {
		
        $j = 3;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($name_column[$k].$j, $data);
        $k++;
    }
    $j = 4;
    foreach($projectTasks['children'] as $parts) {
        // $id = '';
        $taskName = ' ---- ' . strip_tags($parts['task_title']);
        $prio = !empty($parts['task_priority_text']) ? $parts['task_priority_text'] : '';
        $status = '';
        $milestone = '';
        $assign = '';
        $start = $parts['task_start_date'];
        $end = $parts['task_end_date'];
        $duration = $parts['duration'];
        $predecessor = isset($parts['predecessor']) ? $parts['predecessor'] : '';
        $workload = $parts['estimated'] ? $parts['estimated'] : 0;
        $eac = $parts['eac'] ? $parts['eac'] : 0;
        $cons = $parts['consumed'];
        $wait = !empty($parts['wait']) ? $parts['wait'] : 0;
        $comp = $parts['completed'];
        $remain = $parts['remain'];
        $profile = isset($parts['profile_text']) ? $parts['profile_text'] : '';
        $manual = isset($parts['manual_consumed']) ? $parts['manual_consumed'] : 0;
        $over = isset($parts['overload']) ? $parts['overload'] : 0;
        $overload = $isManual ? $parts['manual_overload'] : $over;
        $amount = isset($parts['amount']) ? $parts['amount'] : 0;
        $progress_order = isset($parts['progress_order']) ? $parts['progress_order'] : 0;
        $progress_order_amount = isset($parts['progress_order_amount']) ? $parts['progress_order_amount'] : 0;
        $id_activity = isset($parts['id_activity']) ? $parts['id_activity'] : '';
        $wait = isset($parts['wait']) ? $parts['wait'] : 0;
        $slider = isset($parts['slider']) ? $parts['slider'] : 0;
        $text = isset($parts['text_1']) ? $parts['text_1'] : '';
        $unit_price = isset($parts['unit_price']) ? $parts['unit_price'] : 0;
        $consumed_euro = isset($parts['consumed_euro']) ? $parts['consumed_euro'] : 0;
        $remain_euro = isset($parts['remain_euro']) ? $parts['remain_euro'] : 0;
        $workload_euro = isset($parts['workload_euro']) ? $parts['workload_euro'] : 0;
        $estimated_euro = isset($parts['estimated_euro']) ? $parts['estimated_euro'] : 0;
        if($settingP['ProjectSetting']['show_freeze'] == 1) {
            if($checkP['Project']['is_freeze'] == 1) {
                $workloadInitial = isset($parts['initial_estimated'])?$parts['initial_estimated']:0;
                $startInitial = isset($parts['initial_task_start_date'])?$parts['initial_task_start_date']:'';
                $endInitial = isset($parts['initial_task_end_date'])?$parts['initial_task_end_date']:'';
            } else {
                $workloadInitial = 0;
                $startInitial = '';
                $endInitial = '';
            }
            $datas = toData($orders, $taskName, $prio, $status, $milestone, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain, $eac, $amount, $progress_order, $progress_order_amount, $id_activity, $wait, $slider, $text, $unit_price, $consumed_euro, $remain_euro, $workload_euro,$estimated_euro, $bg_currency,$workloadInitial,$startInitial,$endInitial);
        } else {
            $datas = toData($orders, $taskName, $prio, $status, $milestone, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain, $eac, $amount, $progress_order, $progress_order_amount, $id_activity, $wait, $slider, $text, $unit_price, $consumed_euro, $remain_euro, $workload_euro, $estimated_euro, $bg_currency);
        }
        $ks=0;
        foreach($datas as $data) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($name_column[$ks].$j, $data);
            $ks++;
        }
        $j++;
        if(!empty($parts['children'])){
            foreach($parts['children'] as $phases){
                // $id = !empty($phases['id']) && $phases['id'] < 999999999 ? $phases['id'] : '';
                $taskName = ' ------ ' . strip_tags($phases['task_title']);
                $prio = !empty($phases['task_priority_text']) ? $phases['task_priority_text'] : '';
                $status = !empty($phases['task_status_text']) ? $phases['task_status_text'] : '';
                $milestone = !empty($phases['milestone_text']) ? $phases['milestone_text'] : '';
                if(!empty($phases['task_status_text']) && !empty($phases['task_status_id']) && !empty($phases['children'])){
                    $listStatusChildren = Set::combine($phases['children'], '{n}.task_status_id', '{n}.task_status_id');
                    if(count($listStatusChildren) == 1){ // co 1 trang thai
                        $statusId = array_shift($listStatusChildren);
                        $status = !empty($projectStatus) && !empty($projectStatus[$statusId]) ? $projectStatus[$statusId] : '';
                    } else {
                        $status = !empty($statusOfCompanies['ProjectStatus']['name']) ? $statusOfCompanies['ProjectStatus']['name'] : '';
                    }
                }
                $ass = !empty($phases['task_assign_to_text']) ? implode(', ', $phases['task_assign_to_text']) : '';
                $assign = $ass;
                $start = $phases['task_start_date'];
                $end = $phases['task_end_date'];
                $duration = $phases['duration'];
                $predecessor = isset($phases['predecessor']) ? $phases['predecessor'] : '';
                $workload = $phases['estimated'] ? $phases['estimated'] : 0;
                $eac = $phases['eac'] ? $phases['eac'] : 0;
                $cons = $phases['consumed'];
                $wait = !empty($phases['wait']) ? $phases['wait'] : 0;
                $comp = $phases['completed'];
                $remain = $phases['remain'];
                $profile = isset($phases['profile_text']) ? $phases['profile_text'] : '';
                $manual = isset($phases['manual_consumed']) ? $phases['manual_consumed'] : 0;
                $over = isset($phases['overload']) ? $phases['overload'] : 0;
                $overload = $isManual ? $phases['manual_overload'] : $over;
                $amount = isset($phases['amount']) ? $phases['amount'] : 0;
                $progress_order = isset($phases['progress_order']) ? $phases['progress_order'] : 0;
                $progress_order_amount = isset($phases['progress_order_amount']) ? $phases['progress_order_amount'] : 0;
                $id_activity = isset($phases['id_activity']) ? $phases['id_activity'] : '';
                $wait = isset($phases['wait']) ? $phases['wait'] : 0;
                $slider = isset($phases['slider']) ? $phases['slider'] : 0;
                $text = isset($phases['text_1']) ? $phases['text_1'] : '';
                $unit_price = isset($phases['unit_price']) ? $phases['unit_price'] : 0;
                $consumed_euro = isset($phases['consumed_euro']) ? $phases['consumed_euro'] : 0;
                $remain_euro = isset($phases['remain_euro']) ? $phases['remain_euro'] : 0;
                $workload_euro = isset($phases['workload_euro']) ? $phases['workload_euro'] : 0;
                $estimated_euro = isset($phases['estimated_euro']) ? $phases['estimated_euro'] : 0;
                if($settingP['ProjectSetting']['show_freeze'] == 1){
                    if($checkP['Project']['is_freeze'] == 1){
                        $workloadInitial = isset($phases['initial_estimated'])?$phases['initial_estimated']:0;
                        $startInitial = isset($phases['initial_task_start_date'])?$phases['initial_task_start_date']:'';
                        $endInitial = isset($phases['initial_task_end_date'])?$phases['initial_task_end_date']:'';
                    } else {
                        $workloadInitial = 0;
                        $startInitial = '';
                        $endInitial = '';
                    }
                    $datas = toData($orders, $taskName, $prio, $status, $milestone, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain, $eac, $amount, $progress_order, $progress_order_amount, $id_activity, $wait, $slider, $text, $unit_price, $consumed_euro, $remain_euro, $workload_euro, $estimated_euro,$bg_currency, $workloadInitial,$startInitial,$endInitial);
                } else {
                    $datas = toData($orders, $taskName, $prio, $status, $milestone, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain, $eac, $amount, $progress_order, $progress_order_amount, $id_activity, $wait, $slider, $text, $unit_price, $consumed_euro, $remain_euro, $workload_euro, $estimated_euro, $bg_currency);
                }
                $_k=0;
                foreach($datas as $data) {
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($name_column[$_k].$j, $data);
                    $_k++;
                }
                $j++;
                if(!empty($phases['children'])){
                    foreach($phases['children'] as $tasks){
                        // $id = $tasks['id'];
                        $taskName = ' -------- ' . strip_tags($tasks['task_title']);
                        $prio = !empty($tasks['task_priority_text']) ? $tasks['task_priority_text'] : '';
                        $status = !empty($tasks['task_status_text']) ? $tasks['task_status_text'] : '';
                        $milestone = !empty($tasks['milestone_text']) ? $tasks['milestone_text'] : '';
                        if(!empty($tasks['task_status_text']) && !empty($tasks['task_status_id']) && !empty($tasks['children'])){
                            $listStatusChildren = Set::combine($tasks['children'], '{n}.task_status_id', '{n}.task_status_id');
                            if(count($listStatusChildren) == 1){ // co 1 trang thai
                                $statusId = array_shift($listStatusChildren);
                                $status = !empty($projectStatus) && !empty($projectStatus[$statusId]) ? $projectStatus[$statusId] : '';
                            } else {
                                $status = !empty($statusOfCompanies['ProjectStatus']['name']) ? $statusOfCompanies['ProjectStatus']['name'] : '';
                            }
                        }
                        $ass = !empty($tasks['task_assign_to_text']) ? implode(', ', $tasks['task_assign_to_text']) : '';
                        $assign = $ass;
                        $start = $tasks['task_start_date'];
                        $end = $tasks['task_end_date'];
                        $duration = $tasks['duration'];
                        $predecessor = isset($tasks['predecessor']) ? $tasks['predecessor'] : '';
                        $workload = $tasks['estimated'] ? $tasks['estimated'] : 0;
                        $eac = $tasks['eac'] ? $tasks['eac'] : 0;
                        $cons = $tasks['consumed'];
                        $wait = !empty($tasks['wait']) ? $tasks['wait'] : 0;
                        $comp = $tasks['completed'];
                        $remain = $tasks['remain'];
                        $profile = isset($tasks['profile_text']) ? $tasks['profile_text'] : '';
                        $manual = isset($tasks['manual_consumed']) ? $tasks['manual_consumed'] : 0;
                        $over = isset($tasks['overload']) ? $tasks['overload'] : 0;
                        $overload = $isManual ? $tasks['manual_overload'] : $over;
                        $amount = isset($tasks['amount']) ? $tasks['amount'] : 0;
                        $progress_order = isset($tasks['progress_order']) ? $tasks['progress_order'] : 0;
                        $progress_order_amount = isset($tasks['progress_order_amount']) ? $tasks['progress_order_amount'] : 0;
                        $id_activity = isset($tasks['id_activity']) ? $tasks['id_activity'] : '';
                        $wait = isset($tasks['wait']) ? $tasks['wait'] : 0;
                        $slider = isset($tasks['slider']) ? $tasks['slider'] : 0;
                        $text = isset($tasks['text_1']) ? $tasks['text_1'] : '';
                        $unit_price = isset($tasks['unit_price']) ? $tasks['unit_price'] : 0;
                        $consumed_euro = isset($tasks['consumed_euro']) ? $tasks['consumed_euro'] : 0;
                        $remain_euro = isset($tasks['remain_euro']) ? $tasks['remain_euro'] : 0;
                        $workload_euro = isset($tasks['workload_euro']) ? $tasks['workload_euro'] : 0;
                        $estimated_euro = isset($tasks['estimated_euro']) ? $tasks['estimated_euro'] : 0;
                        if($settingP['ProjectSetting']['show_freeze'] == 1){
                            if($checkP['Project']['is_freeze'] == 1){
                                $workloadInitial = isset($tasks['initial_estimated'])?$tasks['initial_estimated']:0;
                                $startInitial = isset($tasks['initial_task_start_date'])?$tasks['initial_task_start_date']:'';
                                $endInitial = isset($tasks['initial_task_end_date'])?$tasks['initial_task_end_date']:'';
                            } else {
                                $workloadInitial = 0;
                                $startInitial = '';
                                $endInitial = '';
                            }
                            $datas = toData($orders, $taskName, $prio, $status, $milestone, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain, $eac, $amount, $progress_order, $progress_order_amount, $id_activity, $wait, $slider, $text, $unit_price, $consumed_euro, $remain_euro, $workload_euro, $estimated_euro, $bg_currency,$workloadInitial,$startInitial,$endInitial);
                        } else {
                            $datas = toData($orders, $taskName, $prio, $status, $milestone, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain, $eac, $amount, $progress_order, $progress_order_amount, $id_activity, $wait, $slider, $text, $unit_price, $consumed_euro, $remain_euro, $workload_euro, $estimated_euro, $bg_currency);
                        }
                        $_k=0;
                        foreach($datas as $data) {
                            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($name_column[$_k].$j, $data);
                            $_k++;
                        }
                        $j++;
                        if(!empty($tasks['children'])){
                            foreach($tasks['children'] as $_tasks){
                                // $id = $_tasks['id'];
                                $taskName = ' ---------- ' . strip_tags($_tasks['task_title']);
                                $prio = !empty($_tasks['task_priority_text']) ? $_tasks['task_priority_text'] : '';
                                $status = !empty($_tasks['task_status_text']) ? $_tasks['task_status_text'] : '';
                                $milestone = !empty($_tasks['milestone_text']) ? $_tasks['milestone_text'] : '';
                                $ass = !empty($_tasks['task_assign_to_text']) ? implode(', ', $_tasks['task_assign_to_text']) : '';
                                $assign = $ass;
                                $start = $_tasks['task_start_date'];
                                $end = $_tasks['task_end_date'];
                                $duration = $_tasks['duration'];
                                $predecessor = isset($_tasks['predecessor']) ? $_tasks['predecessor'] : '';
                                $workload = $_tasks['estimated'] ? $_tasks['estimated'] : 0;
                                $eac = $_tasks['eac'] ? $_tasks['eac'] : 0;
                                $cons = $_tasks['consumed'];
                                $wait = !empty($_tasks['wait']) ? $_tasks['wait'] : 0;
                                $comp = $_tasks['completed'];
                                $remain = $_tasks['remain'];
                                $profile = isset($_tasks['profile_text']) ? $_tasks['profile_text'] : '';
                                $manual = isset($_tasks['manual_consumed']) ? $_tasks['manual_consumed'] : 0;
                                $over = isset($_tasks['overload']) ? $_tasks['overload'] : 0;
                                $overload = $isManual ? $_tasks['manual_overload'] : $over;
                                $amount = isset($_tasks['amount']) ? $_tasks['amount'] : 0;
                                $progress_order = isset($_tasks['progress_order']) ? $_tasks['progress_order'] : 0;
                                $progress_order_amount = isset($_tasks['progress_order_amount']) ? $_tasks['progress_order_amount'] : 0;
                                $id_activity = isset($_tasks['id_activity']) ? $_tasks['id_activity'] : '';
                                $wait = isset($_tasks['wait']) ? $_tasks['wait'] : 0;
                                $slider = isset($_tasks['slider']) ? $_tasks['slider'] : 0;
                                $text = isset($_tasks['text_1']) ? $_tasks['text_1'] : '';
                                $unit_price = isset($_tasks['unit_price']) ? $_tasks['unit_price'] : 0;
                                $consumed_euro = isset($_tasks['consumed_euro']) ? $_tasks['consumed_euro'] : 0;
                                $remain_euro = isset($_tasks['remain_euro']) ? $_tasks['remain_euro'] : 0;
                                $workload_euro = isset($_tasks['workload_euro']) ? $_tasks['workload_euro'] : 0;
                                if($settingP['ProjectSetting']['show_freeze'] == 1){
                                    if($checkP['Project']['is_freeze'] == 1){
                                        $workloadInitial = isset($_tasks['initial_estimated'])?$_tasks['initial_estimated']:0;
                                        $startInitial = isset($_tasks['initial_task_start_date'])?$_tasks['initial_task_start_date']:'';
                                        $endInitial = isset($_tasks['initial_task_end_date'])?$_tasks['initial_task_end_date']:'';
                                    } else {
                                        $workloadInitial = 0;
                                        $startInitial = '';
                                        $endInitial = '';
                                    }
                                    $datas = toData($orders, $taskName, $prio, $status, $milestone, $profile, $assign, $start, $end, $duration, $predecessor, $workload,  $eac, $overload, $cons, $manual, $wait, $comp, $remain, $eac, $amount, $progress_order, $progress_order_amount, $id_activity, $wait, $slider, $text, $unit_price, $consumed_euro, $remain_euro, $workload_euro, $bg_currency, $workloadInitial,$startInitial,$endInitial);
                                } else {
                                    $datas = toData($orders, $taskName, $prio, $status, $milestone, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain, $eac, $amount, $progress_order, $progress_order_amount, $id_activity, $wait, $slider, $text, $unit_price, $consumed_euro, $remain_euro, $workload_euro, $bg_currency);
                                }
                                $_k=0;
                                foreach($datas as $data) {
                                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($name_column[$_k].$j, $data);
                                    $_k++;
                                }
                                $j++;
                            }
                        }
                    }
                }
            }
        }
    }
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client�s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$_filename.str_replace(" ",'_',$projectName['Project']['project_name']).'_'.date('H_i_s_d_m_Y').'.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>
