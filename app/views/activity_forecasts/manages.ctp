<?php echo $html->css('projects'); ?>
<?php echo $html->css('context/jquery.contextmenu'); ?>
<?php echo $html->script('context/jquery.contextmenu') ?>
<?php echo $html->script('jquery.touchSwipe.min') ?>
<?php echo $html->script('jquery.form') ?>
<?php
$translate = $this->requestAction('/translations/getByLang/Project_Task');
$taskColumns = $this->requestAction('/admin_task/getTaskSettings');
$settingP = $this->requestAction('/project_settings/get');
$settingP = !empty($settingP) && !empty($settingP['ProjectSetting']['show_freeze']) ? $settingP['ProjectSetting']['show_freeze'] : 0;
?>
<style>
#absence-scroll {
    overflow-x:hidden !important;
}
.capacity, .workload{ font-size:11px; letter-spacing:1px;}
.ct{ vertical-align:middle;}
/*i{ display:block;}*/
#absence-wrapper #absence-fixed{ width: 25% !important;}
#tooltip-template-dl-pr dt, #tooltip-template-dl dt {
    clear: left;
    width: 80px;
}
#tooltip-template-dl-pr dt, dd, #tooltip-template-dl dt, dd  {
    float: left;
    padding: 3px 5px;
}
.actask {
    display: block;
    font-weight: bold;
}
span.activity-tootip-forecast{
    cursor: pointer;
    display: inline !important;
}
.wl-item {
    margin-bottom: 5px;
}
.project-name {
    padding: 0px 0 0 2px;
    display: block;
}
.loading{
    background-image: url("/img/loader.gif");
    background-repeat: no-repeat;
    background-position: center;
    z-index: 9999;
    opacity: 0.5;
    background-color: white;
}
#popup_check_drag_content p{
    color: #000;
    font-size: 14px;
    padding: 10px 0 0 20px;
}
.wl-input {
    padding: 5px;
    width: 90%;
}
.wl-input:disabled {
    background: rgb(235, 235, 228);
}
.hide {
    display: none;
    cursor: pointer;
}
#wd-container-footer{
    display: none;
}
body{
    overflow: hidden;
}
#project_container{
    overflow: auto !important;
}
table *{
	box-sizing: border-box;
}
tbody#absence-table-fixed tr td,
tbody#absence-table tr td{
	height: 35px;
}
.tdColEmployee{
	vertical-align: middle;
}
#wd-container-main {
	max-width: 1920px;
}
</style>
<!-- export excel  -->
<fieldset style="display: none;">
    <?php
    $ACTION = $this->params['action'];
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'absence_requests', 'action' => 'export')));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<!-- /export excel  -->
<!-- ToolTip -->
<div id="tooltip-template" class="buttons" style="display: none;">
    <dl id="tooltip-template-dl">
        <dt><?php __('Name'); ?> :</dt>
        <dd id="tooltip-name"></dd>
        <dt><?php __('Short name'); ?> :</dt>
        <dd id="tooltip-sname"></dd>
        <dt><?php __('Long name'); ?> :</dt>
        <dd id="tooltip-lname"></dd>
        <dt><?php __('Family'); ?> :</dt>
        <dd id="tooltip-fam"></dd>
        <dt><?php __('Subfamily'); ?> :</dt>
        <dd id="tooltip-sfam"></dd>
        <dt class="actask"><?php __('Task'); ?> :</dt>
        <dd class="actask" id="tooltip-ac-task"></dd>
    </dl>
</div>
<div id="tooltip-template-pr" class="buttons" style="display: none;">
    <dl id="tooltip-template-dl-pr">
        <dt><?php __('Project'); ?> :</dt>
        <dd id="tooltip-project"></dd>
        <dt><?php __('Part'); ?> :</dt>
        <dd id="tooltip-part"></dd>
        <dt><?php __('Phase'); ?> :</dt>
        <dd id="tooltip-phase"></dd>
        <dt class="actask"><?php __('Task'); ?> :</dt>
        <dd class="actask" id="tooltip-pr-task"></dd>
    </dl>
</div>
<!-- Popup right click-->
<div id="popup_check_drag" style="display: none;">
    <fieldset>
        <div id="popup_check_drag_content">
            <p></p>
        </div>
        <div style="clear: both;"></div>
        <ul class="type_buttons" style="float: right;margin-top: 15px;margin-right: 10px;">
            <li><a href="javascript:void(0)" class="cancel" style="display: block;"><?php __("Cancel") ?></a></li>
        </ul>
    </fieldset>
</div>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div id="message-place">
                    <?php
                    echo $this->Session->flash();
                    $am = __('AM', true);
                    $pm = __('PM', true);

                    $dayMaps = array(
                        'monday' => $_start,
                        'tuesday' => $_start + DAY,
                        'wednesday' => $_start + (DAY * 2),
                        'thursday' => $_start + (DAY * 3),
                        'friday' => $_start + (DAY * 4),
                        'saturday' => $_start + (DAY * 5),
                        'sunday' => $_start + (DAY * 6)
                    );
                    $listDays = array_values($dayMaps);
                    $keyProfitCenter = !empty($profit['id']) ? $profit['id'] : 0; // khong dc xoa
                    ?>
                </div>
                <div class="wd-table" id="project_container" style="width:100%;">
                    <div id="absence-container" class="wd-activity-actions " style="min-height:400px;">
                        <div id="table-control" >
                            <?php
                            echo $this->Form->create('Control', array(
                                'type' => 'get',
                                'url' => '/' . Router::normalize($this->here)));
                            ?>
                             <fieldset style="margin-left: 22px; float: left; margin-right: 5px">
                                <select style="padding:6px;" name="typeRequest" id="typeRequest">
                                    <option value="week" <?php echo $typeSelect=='week'?'selected':'';?>><?php echo __('Week',true);?></option>
                                    <option value="month" <?php echo $typeSelect=='month'?'selected':'';?>><?php echo __('Month',true);?></option>
                                    <option value="year" <?php echo $typeSelect=='year'?'selected':'';?>><?php echo __('Year',true);?></option>
                                </select>
                                <?php echo $this->element('week_activity'); ?>
                               
                                <?php
                                if(!empty($paths) && $ACTION != 'my_diary'){
                                    $cp['-1'] = $companyName;
                                    $paths = $cp + $paths;
                                }
                                echo $this->Form->select('profit', $paths, $profit['id'], array('empty' => false, 'escape' => false, 'style' => 'padding: 6px'));
                                ?>
                                <button class="btn btn-go"></button>
                                <?php
                                if($ACTION == 'manages')
                                echo $this->element('btn_expand_pc');
                                ?>
                            <?php
                            if($ACTION == 'my_diary'):
                                $urlExport =str_replace('my_diary','export_my_diary',$_SERVER['REQUEST_URI']);
                            ?>
                                <a href="<?php echo $this->Html->url($urlExport);?>" id="submit-request-all-top" class="export-outlook-my-diary export-outlook-my-diary-top" title="<?php __('Export OutLook')?>"><span><?php __('Export OutLook'); ?></span></a>
                            <?php endif ?>
                            </fieldset>
                            <?php echo $this->Form->end(); ?>
                        </div>
                        <div id="absence-wrapper">
                        <div id="scrollTopAbsence" class="useLeftScroll"><div id="scrollTopAbsenceContent"></div></div>
                            <br clear="all"  />
                            <div id="scrollLeftAbsence">
                                <div id="scrollLeftAbsenceContent"></div>
                            </div>
                            <table id="absence-fixed">
                                <tr class="elmTemp">
                                <td class="elmTemp">
                                <table>
                                    <thead>
                                        <tr class="height-header-fixed-left">
                                            <th id="thColEmployee" ><?php __('Employee'); ?></th>
                                            <th id="thColCapacity" style="width:140px;"><?php __('Workload') ?>*/<?php __('Capacity'); ?>*</th>
                                        </tr>
                                    </thead>
                                 </table>
                             </td>
                             </tr>
                             <tr class="elmTemp">
                                <td class="elmTemp">
                                <div class="tbl-tbody" >
                                <table>
                                    <tbody id="absence-table-fixed">
                                    <tr id="affterLeft"><td colspan="2" ></td></tr>
                                    <tr <?php if(isset($isDiary)) { ?>style = "display: none;"
                                        <?php } ?>
                                     ><td class='ct summary'><?php echo __('Summary',true) ?></td><td id="summary" class='ct summary'></td></tr>
                                    </tbody>
                                </table>
                                </div>
                                </td>
                                </tr>
                             </table>

                            <div id="absence-scroll" >
                                <table id="absence" class="absence-margin" style="width:-1px">
                                    <tr class="elmTemp">
                                    <td class="elmTemp">
                                    <table>
                                    <thead>
                                        <tr class="height-header-fixed">
                                            <?php
                                                if($typeSelect=='week'){
                                                       $countWorkdays=0;
                                                    if(!empty($workdays)):
                                                        $workdays = array_combine(array_values($dayMaps),array_values($workdays));
                                                        foreach($workdays as $key => $val):
                                                            if(!empty($val) && $val != 0):
                                                            $countWorkdays++;
                                                            $dayMaps[$key] = $key;
                                                ?>
                                                <th width="149" class="fixedWidth" id="<?php echo 'fore'.$countWorkdays;?>"><?php echo __(date('l', $key)) . __(date(' d ', $key)) .  __(date('M', $key)); ?></th>
                                                <?php
                                                            endif;
                                                        endforeach;
                                                    endif;
                                                ?>
                                            <?php }else{?>
                                            <?php
                                                if(!empty($dayWorks)):
                                                        $dayMaps = array();
                                                        $workdaysTmp = array();
                                                        $i=0;
                                                        foreach($dayWorks as $key => $val):
                                                            if($workdays[$val[1]]!=0):
                                                            $keyTmp = $val[1];
                                                            if(!in_array($val[1], $workdays)){
                                                                $keyTmp = $val[2];
                                                            }
                                                            $workdaysTmp[$keyTmp] = 1;
                                                            $dayMaps[$keyTmp] = $val[2];
                                                ?>
                                                 <th class="fixedWidth" id="<?php echo 'fore'.ucfirst($key);?>">
                                                    <?php echo __(date('l',$val[2])).__(date(' d ',$val[2])).__(date('M',$val[2])); ?>
                                                 </th>
                                                <?php
                                                            endif;
                                                            $i++;
                                                        endforeach;
                                                    endif;
                                                ?>
                                            <?php $workdays = $workdaysTmp; $countWorkdays=count($workdays); }
                                            $countEmployees=count($employees);
                                            if( isset($employees['tNotAffec'])){
                                                $countEmployees-=1;
                                            }
                                             ?>
                                        </tr>
                                    </thead>
                                    </table>
                                    </td></tr>
                                    <tr class="elmTemp"><td class="elmTemp">
                                    <div class="tbl-tbody" >
                                    <table >
                                    <tbody id="absence-table">
                                    <?php
									$count_absence_employee = array();
                                    $dataView = array();
                                    $assignHtml = '<option value="'. 0 . '" data-status="' . 0 . '">-- ' . __('Assign To', true) . ' --</option>';
                                    foreach ($employees as $id => $employee) {
                                        if($id != 'tNotAffec'){
                                            $assignHtml .= '<option value="'. $id . '" data-status="' . $id . '">' . $employee . '</option>';
                                        }
										$count_absence_employee[$id] = 0;
                                        foreach ($dayMaps as $day => $time) {
                                            $_holiday=false;
                                            foreach($holidays as $key=>$value123)
                                            {
                                                if($time==$key) $_holiday=true;
                                            }
                                            $default = array(
                                                'holiday'=>$_holiday
                                            );
                                            foreach (array('am', 'pm') as $type) {
                                                if (!empty($requests[$id][$time]['absence_' . $type])
                                                        && ($requests[$id][$time]['response_' . $type] === 'validated'
                                                        || empty($forecasts[$id][$time]['activity_' . $type]))) {
                                                    $default['absence_' . $type] = $requests[$id][$time]['absence_' . $type];
                                                    $default['response_' . $type] = $requests[$id][$time]['response_' . $type];
													$count_absence_employee[$id] += 0.5;
                                                }
                                                if (!empty($forecasts[$id][$time]['activity_' . $type])) {
                                                    $default['activity_' . $type] = $forecasts[$id][$time]['activity_' . $type];
                                                    $default[$type . '_model'] = strtolower($forecasts[$id][$time][$type . '_model']);
                                                }
                                            }
                                            if(!empty($workdays[$day]) && $workdays[$day] != 0){
                                                $dataView[$id][$day] = $default;
                                            }
                                        }
                                    }
									
                                    if(!empty($workloads)){
                                        foreach($dataView as $id1 => $_dataViews){
                                            foreach($_dataViews as $time1 => $_dataView){
                                                foreach($workloads as $id2 => $_workloads){
                                                    foreach($_workloads as $time2 => $_workload){
                                                        if($id1 == $id2 && $time1 == $time2){
                                                            $dataView[$id1][$time1]['data'] = $_workload;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    $tdsSummary='';
                                    $summary=0;
                                    $totalCapacity=0;
                                    $countRow1=0;
                                    $left='';
                                    $a='';
                                    $listPhase = $listPart = $listProject = $acOfTask = array();
                                    $myCapacity = array();
                                    foreach($dataView as $id => $workload)
                                    {
                                        $totalCountDownCapacity = 0;
                                        $totalCountDownWorkload = 0;
                                        $employee = $employees[$id];
                                        $employeeCustom[$id]['capacity']=0;
                                        $employeeCustom[$id]['workload']=0;
                                        $countRow1++;
                                        echo "<tr class='fixedHeight tdLeftRow-".$id."'>";
                                        $countCell=0;
                                        $countDownCapacity1=0;
                                        foreach($workload as $time=> $value)
                                        {
                                            $fw='';
                                            if(strtolower(date('l',$time))=='monday')
                                            {
                                                $fw='fw';
                                            }
                                            $countCell++;
                                            $flag=0;
                                            $class=$fw.' employee-'.$countRow1.'-'.$countCell.' fore'.$countCell;
                                            $text='';
                                            $text1='';
                                            $summaryEmployeeOnDay=0;
                                            $workloadPr=0;
                                            $namePr=array();
                                            $workloadAc=0;
                                            $nameAc=array();
                                            $countDownCapacity=0;
                                            if(isset($value['holiday'])&&$value['holiday']===true)
                                            {
                                                $class.=' rp-holiday';
                                                $text = __("Holiday", true);
                                                $flag=1;
                                                $countDownCapacity+=1;
                                            }
                                            $dayF = date('d-m-Y', $time);
                                            echo "<td width='149' id='row-" . $id . '-' . $time . "' class='dragTable fixedWidth wd-work ".$class."' data-emp='$id' data-empName='$employee' data-day='$time' data-dayF='$dayF'>";
                                            $getAcId = 0;
                                            $stP = $stA = $isPcA = $isPcP = $idAc = $idPr = $idPTask = $isNCTA = $isNCTP = array();
											
                                            if(isset($value['data']))
                                            {
                                                $countRow=count($value['data']);
                                                foreach($value['data'] as $key=> $val)
                                                {
                                                    if(isset($value['data'][$key]))
                                                    {
                                                        $vacation=$value['data'][$key]['vacation'];
                                                        /*-----DESCRIPTION-----
                                                        vacation = -1 : ngay binh thuong
                                                        vacation = 1 : nghi ca ngay
                                                        vacation = 3 : nghi buoi sang da validate
                                                        vacation = 5 : nghi buoi sang da validate, buoi chieu dang waiting
                                                        vacation = 7 : nghi buoi chieu da validate
                                                        vacation = 9 : nghi buoi chieu da validate, buoi sang dang waiting
                                                        vacation = 11 : buoi sang dang waiting, nghi buoi chieu da validate
                                                        vacation = 13 : buoi chieu dang waiting, nghi buoi sang da validate
                                                        vacation = 2 : ca ngay dang waiting
                                                        vacation = 4 : nghi buoi sang dang waiting
                                                        vacation = 6 : nghi buoi chieu dang waiting
                                                        ---------------------*/
                                                        if($vacation==1)
                                                        {
                                                            if($value['absence_am'] == $value['absence_pm']){
                                                                $class.=' rp-validated';
                                                                $text="<span class='rp-validated'>".$absences[$value['absence_am']]['print']."(1)</span>";
                                                                $flag=1;
                                                                $countDownCapacity+=(1/$countRow);
                                                            }else {
                                                                $text="<span class='rp-validated'>".$absences[$value['absence_am']]['print']."(0.5 AM)</span>";
                                                                $text1="<span class='rp-validated'>".$absences[$value['absence_pm']]['print']."(0.5 PM)</span>";
                                                                $flag=3;
                                                                $countDownCapacity+=(0.5/$countRow);
                                                            }
															if(!empty($count_absence_employee[$id]) && (count($workdays) > $count_absence_employee[$id])) $workloadPr -= $value['data'][$key]['workload'];
                                                        }
                                                        else if($vacation==3)
                                                        {
                                                            $text="<span class='rp-validated'>".$absences[$value['absence_am']]['print']."(0.5 AM)</span>";
                                                            $flag=2;
                                                            $countDownCapacity+=(0.5/$countRow);
                                                        }
                                                        else if($vacation==5||$vacation==13)
                                                        {
                                                            $text="<span class='rp-validated'>".$absences[$value['absence_am']]['print']."(0.5 AM)</span>";
                                                            $text1="<span class='rp-waiting'>".$absences[$value['absence_pm']]['print']."(0.5 PM)</span>";
                                                            $flag=3;
                                                            $countDownCapacity+=(0.5/$countRow);
                                                        }
                                                        else if($vacation==7)
                                                        {
                                                            $text="<span class='rp-validated'>".$absences[$value['absence_pm']]['print']."(0.5 PM)</span>";
                                                            $flag=2;
                                                            $countDownCapacity+=(0.5/$countRow);
                                                        }
                                                        else if($vacation==9||$vacation==11)
                                                        {
                                                            $text1="<span class='rp-validated'>".$absences[$value['absence_am']]['print']."(0.5 AM)</span>";
                                                            $text="<span class='rp-waiting'>".$absences[$value['absence_pm']]['print']."(0.5 PM)</span>";
                                                            $flag=4;
                                                            $countDownCapacity+=(0.5/$countRow);
                                                        }
                                                        else if($vacation==2)
                                                        {
                                                            if($value['absence_am'] == $value['absence_pm']){
                                                                $text="<span class='rp-waiting'>".$absences[$value['absence_am']]['print']."(1)</span>";
                                                                $flag=2;
                                                            } else {
                                                                $text="<span class='rp-waiting'>".$absences[$value['absence_am']]['print']."(0.5 AM)</span>";
                                                                $text1="<span class='rp-waiting'>".$absences[$value['absence_pm']]['print']."(0.5 PM)</span>";
                                                                $flag=3;
                                                            }
                                                        }
                                                        else if($vacation==4)
                                                        {
                                                            $text="<span class='rp-waiting'>".$absences[$value['absence_am']]['print']."(0.5 AM)</span>";
                                                            $flag=2;
                                                        }
                                                        else if($vacation==6)
                                                        {
                                                            $text="<span class='rp-waiting'>".$absences[$value['absence_pm']]['print']."(0.5 PM)</span>";
                                                            $flag=2;
                                                        }
                                                        if(isset($value['data'][$key]['idPr']))
                                                        {
                                                            $keyPr=$time.'-'.$value['data'][$key]['task_id'].'-'.$id;
                                                            $namePr[$keyPr] = $value['data'][$key]['nameTask'];
                                                            if(!isset($nameTaskPr[$keyPr]))
                                                            {
                                                                $nameTaskPr[$keyPr]='';
                                                                $workloadPrForItem[$keyPr]= 0;

                                                            }
                                                            $nameTaskPr[$keyPr] .= '<span>'.$value['data'][$key]['nameTask'].' ('.$value['data'][$key]['workload'].')</span>';
                                                            $workloadPrForItem[$keyPr] += $value['data'][$key]['workload'];
                                                            $stP[$keyPr] = $val['st'];
                                                            $isPcP[$keyPr] = $val['is_pc'];
                                                            $idPr[$keyPr] = $val['idPr'];
                                                            $idPTask[$keyPr] = $val['p_task_id'];
                                                            $isNCTP[$keyPr] = $val['nct'];
															
                                                            $workloadPr += $value['data'][$key]['workload'];
                                                            $summary += $value['data'][$key]['workload'];
                                                            /**
                                                             * Danh sach Part cua task ID
                                                             */
                                                            if(!isset($listPart[$val['task_id']])){
                                                                $listPart[$val['task_id']] = '';
                                                            }
                                                            $listPart[$val['task_id']] = !empty($val['namePart']) ? $val['namePart'] : '';
                                                            /**
                                                             * Danh sach Phase cua task ID
                                                             */
                                                            if(!isset($listPhase[$val['task_id']])){
                                                                $listPhase[$val['task_id']] = '';
                                                            }
                                                            $listPhase[$val['task_id']] = !empty($val['namePhase']) ? $val['namePhase'] : '';
                                                            /**
                                                             * Danh sach Project cua task ID
                                                             */
                                                            if(!isset($listProject[$val['task_id']])){
                                                                $listProject[$val['task_id']] = '';
                                                            }
                                                            $listProject[$val['task_id']] = !empty($val['namePr']) ? $val['namePr'] : '';
                                                        }
                                                        if(isset($value['data'][$key]['idAc']))
                                                        {
                                                            $keyAc=$time.'-'.$value['data'][$key]['task_id'].'-'.$id;
                                                            $nameAc[$keyAc] = $value['data'][$key]['nameTask'];
                                                            if(!isset($nameTaskAc[$keyAc]))
                                                            {

                                                                $nameTaskAc[$keyAc]='';
                                                                $workloadAcForItem[$keyAc]=0;

                                                            }
                                                            $nameTaskAc[$keyAc] .= '<span>'.$value['data'][$key]['nameTask'].' ('.$value['data'][$key]['workload'].')</span>';
                                                            $workloadAcForItem[$keyAc] += $value['data'][$key]['workload'];

                                                            $workloadAc += $value['data'][$key]['workload'];
                                                            $summary += $value['data'][$key]['workload'];
                                                            $stA[$keyAc] = $val['st'];
                                                            $isPcA[$keyAc] = $val['is_pc'];
                                                            $idAc[$keyAc] = $val['idAc'];
                                                            $isNCTA[$keyAc] = $val['nct'];

                                                            $acOfTask[$val['task_id']] = $val['idAc'];
                                                        }
                                                        $summaryEmployeeOnDay=$workloadPr+$workloadAc;
                                                    }
                                                }
                                            }
											
                                            $namePr=!empty($namePr)?$namePr:array();
                                            $nameAc=!empty($nameAc)?$nameAc:array();
                                            if( $flag != 1 ){
                                                if( $flag ){
                                                    echo $text;
                                                }
                                                if( $flag == 3 ){
                                                    echo $text1;
                                                }
                                                foreach($namePr as $_key => $_name)
                                                {
                                                    $formatKey = explode('-', $_key);
                                                    $formatKey = !empty($formatKey[1]) ? $formatKey[1] : 0;
                                                    $project_name = isset($listProject[$formatKey]) ? $listProject[$formatKey] : '';
                                                    $closed = $this->Html->image('slick_grid/tick.png', array('title' => __('Closed', true), 'class' => 'ipr-' . $formatKey, 'alt' => 'pr-' . $formatKey, 'style' => 'display: ' . ($stP[$_key] == 'CL' ? 'inline' : 'none')));
                                                    //if(!empty($workloadPrForItem[$_key]) && $workloadPrForItem[$_key] != 0){
                                                        echo "<div class='pr-" .$formatKey. " wl-item'>
                                                        <b class='project-name'>$project_name</b>
                                                        <span class='activity-tootip-forecast' rel='pr-" .$_key. "' day='" .$time. "' empId='" .$id. "' emp='" .$employee. "' dayF='" .date('d-m-Y', $time). "' st='".$stP[$_key]."' isPc='".$isPcP[$_key]."' relID='pr-".$idPr[$_key]."' idPTask='".$idPTask[$_key]."' isNCT='".$isNCTP[$_key]."' data-task='pr-{$idPTask[$_key]}'>".$_name."</span>";
                                                        echo " (<i class='workload-data' id='pr-" .$_key. "'>".$workloadPrForItem[$_key]."</i>) $closed</div>";
                                                    //}
                                                }

                                                if($nameAc!='')
                                                {
                                                    foreach($nameAc as $_key=>$_name)
                                                    {
                                                        $formatKey = explode('-', $_key);
                                                        $formatKey = !empty($formatKey[1]) ? $formatKey[1] : 0;
                                                        $itemName = $allActivies[ $acOfTask[$formatKey] ]['name'];
                                                        $closed = $stA[$_key] == 'CL' ? $this->Html->image('slick_grid/tick.png', array('title' => __('Closed', true), 'class' => 'iac-' . $formatKey, 'alt' => 'ac-' . $formatKey, 'style' => 'display: ' . ($stA[$_key] == 'CL' ? 'inline' : 'none'))) : '';
                                                        //if(!empty($workloadAcForItem[$_key]) && $workloadAcForItem[$_key] != 0){
                                                            echo "<div class='ac-" .$formatKey . " wl-item'>
                                                            <b class='project-name'>$itemName</b>
                                                            <span class='activity-tootip-forecast' rel='ac-" .$_key. "'  day='" .$time. "' empId='" .$id. "' emp='" .$employee. "' dayF='" .date('d-m-Y', $time). "' st='".$stA[$_key]."' isPc='".$isPcA[$_key]."' relID='ac-".$idAc[$_key]."' idPTask='0' isNCT='".$isNCTA[$_key]."' data-task='ac-$formatKey'>".$_name."</span>
                                                            (<i class='workload-data' id='ac-" .$_key. "'>".$workloadAcForItem[$_key]."</i>) $closed</div>";
                                                        //}
                                                    }
                                                }
                                                if( $flag == 4 ){
                                                    echo $text1;
                                                }
                                            } else {
                                                echo $text;
                                            }
                                            $totalCountDownCapacity+=$countDownCapacity;
                                            $totalCountDownWorkload+=$summaryEmployeeOnDay;
                                            if(isset($capacity[$countCell]['value']))
                                            {
                                                $capacity[$countCell]['value']+=$countDownCapacity;
                                            }
                                            else
                                            {
                                                $capacity[$countCell]['value']=$countDownCapacity;
                                            }
                                            if($countCell==$countWorkdays)
                                            {
                                                $employeeCustom[$id]['capacity']=caculateTwoNumber($countWorkdays,$totalCountDownCapacity);
                                                $employeeCustom[$id]['workload']=$totalCountDownWorkload;
                                            }
                                            echo "</td>";
                                            //calculate bottom capacity
                                            if( !isset($myCapacity[$time]) )$myCapacity[$time] = 0;
                                            if( !isset($holidays[$time]) ){
                                                if( isset($externalEmployee[$id]) && $externalEmployee[$id] == 2 ){
                                                    if( isset($cpOfMulti[$id][$time]) ){
                                                        $myCapacity[$time] += $cpOfMulti[$id][$time];
                                                    }
                                                } else if($id != 'tNotAffec' ) {
                                                    $myCapacity[$time] += 1 - $countDownCapacity;
                                                }
                                            }
                                        }
										
                                        echo "</tr>";
                                        if($employeeCustom[$id]['workload']>$employeeCustom[$id]['capacity'])
                                        {
                                            $clsTemp='check-workload';
                                        }
                                        else
                                        {
                                            $clsTemp='';
                                        }
                                        if($id === 'tNotAffec'){
                                            $employeeCustom[$id]['capacity'] = 0;
                                        }
                                        if( isset($externalEmployee[$id]) && $externalEmployee[$id] == 2 ){
                                            $employeeCustom[$id]['capacity'] = !empty($capacityOfMultiResources[$id]) ? $capacityOfMultiResources[$id] : 0;
                                        }
                                        $a .= "<tr class='fixedHeight tdRightRow-".$id."'><td class='tdColEmployee ab-name'>";
                                        if( $showAllPicture && $id != 'tNotAffec' ){
                                            $avatarEmploy = $this->UserFile->avatar($id);
											$a .= '<div class="circle-name inlineblock circle-30" title="'. strip_tags($employees[$id]).'"><img style="width: 30px; height: 30px;" src="' . $avatarEmploy . '" alt="'. strip_tags($employees[$id]).'"></div>';
                                            // $a .= "<img style='float:right; width: 20px; height:20px' src='" . $avatarEmploy . "'>";
                                        }
                                        $a .= $employees[$id];
                                        $a .= "</td><td id='totalWorkload-".$id."' class='tdColCapacity ct ".$clsTemp."'><b class='workload'>".$employeeCustom[$id]['workload']."</b>/<b class='capacity'>".$employeeCustom[$id]['capacity']."</b></td></tr>";
                                    }
                                    $i = 1;
                                    foreach ($workload as $time => $val) {
                                        $tdsSummary.="<td class='ct ".$fw." summary-".$countRow1.'-'.$i++."'>".(caculateTwoNumber($myCapacity[$time], 0))."</td>";
                                        $totalCapacity += $myCapacity[$time];
                                    }
                                    ?>
                                    <tr><td colspan="<?php echo $countWorkdays;?>"></td></tr>
                                    <tr <?php if(isset($isDiary)) { ?>style = "display: none;"
                                        <?php } ?>
                                    >
                                    <?php
                                        if($ACTION != 'my_diary'){
                                            echo $tdsSummary;
                                        }
                                    ?>
                                    </tr>
                                    </tbody>
                                    </table>
                                    </div>
                                    </td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Popup right click-->
<div id="popup_infor_task" style="display: none;">
    <fieldset>
        <div id="infor_task_content">
            <table id="absence-task" style="font-size: 12px;">
                <thead>
                    <tr>
                        <?php
                            $trHead = $trBody = '';
                            $freeze = array('Initialworkload', 'Initialstartdate', 'Initialenddate', 'Amount€', '%progressorder', '%progressorder€', 'Duration', 'Predecessor');
                            $statusHtml = '<option value="">-- ' . __('Status', true) . ' --</option>';
                            foreach($projectStatuses as $status){
                                $ss = $status['ProjectStatus'];
                                $statusHtml .= '<option value="'. $ss['id'] . '" data-status="' . $ss['status'] . '">' . $ss['name'] . '</option>';
                            }
                            $profileHtml = '<option value="">-- ' . __('Profile', true) . ' --</option>';
                            foreach($profiles as $id => $profile){
                                $profileHtml .= '<option value="'. $id . '" data-status="' . $id . '">' . $profile . '</option>';
                            }
                            foreach($taskColumns as $na){
                                $na = explode('|', $na);
                                if($na[1]){
                                    if(in_array($na[0], $freeze)){
                                    } else {
                                        $naShow = !empty($translate[$na[0]]) ? $translate[$na[0]] : '';
                                        if( $na[0] == 'Task' ){
                                            $trHead .= "<th width='200'>$naShow</th>";
                                            $trBody .= "<td id='" . $na[0] . "'><input type='text' id='task-name' class='wl-input' /><input type='hidden' id='task-id' /><input type='hidden' id='container-id' /></td>";
                                        } else if( $na[0] == 'Status' ){
                                            $trHead .= "<th width='150'>$naShow</th>";
                                            $trBody .= "<td id='" . $na[0] . "'><select id='select-status' class='wl-input'>$statusHtml</select></td>";
                                        } else if( $na[0] == 'Text' ){
                                            $trHead .= "<th>$naShow</th>";
                                            $trBody .= "<td id='" . $na[0] . "'><img src='" . $this->Html->url('/img/extjs/icon-text.png') . "' id='icon-text' class='hide' alt='' /></td>";
                                        } else if( $na[0] == 'Attachment' ){
                                            $trHead .= "<th>$naShow</th>";
                                            $trBody .= "<td id='" . $na[0] . "'>
                                                <img src='" . $this->Html->url('/img/extjs/icon-task-folder.png') . "' id='icon-folder' class='hide' alt='' />
                                                <img src='" . $this->Html->url('/img/extjs/icon-url.png') . "' id='icon-url' class='hide' alt='' />
                                                <img src='" . $this->Html->url('/img/download.png') . "' id='icon-download' class='hide' alt='' />
                                                <img src='" . $this->Html->url('/img/delete.png') . "' id='icon-delete' class='hide' alt='' />
                                            </td>";
                                        } else if ($na[0] == 'AssignedTo'){
                                            $trHead .= "<th width='150'>$naShow</th>";
                                            $trBody .= "<td id='" . $na[0] . "'><select id='select-assign' class='wl-input'>$assignHtml</select></td>";
                                        } else if ($na[0] == 'Profile'){
                                            $trHead .= "<th width='150'>$naShow</th>";
                                            $trBody .= "<td id='" . $na[0] . "'><select id='select-profile' class='wl-input'>$profileHtml</select></td>";
                                        } else {
                                            $trHead .= "<th>$naShow</th>";
                                            $trBody .= "<td id='" . $na[0] . "'></td>";
                                        }
                                    }
                                }
                            }
                            echo $trHead;
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <tr style="color: #000;">
                        <?php
                            echo $trBody;
                        ?>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="clear: both; text-align: right;margin-top: 15px;margin-right: 10px;">
            <img src="<?php echo $this->Html->url('/img/icon-loading.gif') ?>" id="img-loading" style="display: none" />
            <a href="javascript:void(0)" class="btn btn-ok" id="ok_save" style="display: none;"></a>
            <a href="javascript:void(0)" class="cancel btn btn-cancel"></a>
        </div>
    </fieldset>
</div>
<?php
/*$i18ns = array(
    'Add a comment' => __('Add a comment', true),
    'Summary' => __('Summary', true),
    'Holiday' => __('Holiday', true),
    'Unknown' => __('Unknown', true),
    'Requested' => __('Requested', true),
    'Validated' => __('Validated', true),
    'No name' => __('No name', true),
    'Detail of %s' => __('Detail of %s', true),
    'Remove forecast' => __('Remove forecast', true),
);*/

$css = '';
foreach ($constraint as $key => $data) {
    $css .= ".rp-$key {background-color : {$data['color']};}";
}
echo '<style type="text/css">' . $css . '</style>';
function caculateTwoNumber($x,$y)
{
    $x=number_format($x,2, '.', '');
    $y=number_format($y,2, '.', '');
    return $x-$y;
}
?>
<?php if($typeSelect!='week'){ ?>
<style type="text/css">
th.fixedWidth{
    min-width:150px !important;
    max-width:150px !important;
    width:150px !important;
    overflow:hidden !important;
    word-break:break-all;
}
td.fixedWidth{
    min-width:161px !important;
    max-width:161px !important;
    width:161px !important;
    overflow:hidden !important;
    word-break:break-all;
}
td.fixedWidth.fw{
    min-width:160px !important;
    max-width:160px !important;
    width:160px !important;
    overflow:hidden !important;
}
</style>
<?php }
else{ ?>
<style>
.fixedWidth{ min-width:80px !important; }
</style>
<?php } ?>
<div style="display: none;" id="message-template">
    <div class="message error"><?php echo __('Cannot connect to server ...', true); ?><a href="#" class="close">x</a></div>
</div>
<!-- dialog_vision_portfolio -->
<div id="add-comment-dialog" class="buttons" style="display: none;" title="<?php echo __('Add new comments', true) ?>">
    <fieldset>
        <textarea rel="no-history" name="comment"></textarea>
    </fieldset>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="ok"><?php __('OK') ?></a></li>
    </ul>
</div>
<!-- dialog_vision_portfolio.end -->
<div id="tooltip-template" class="buttons" style="display: none;">
    <dl id="tooltip-template-dl">
        <dt><?php __('Short name'); ?> :</dt>
        <dd>%1$s</dd>
        <dt><?php __('Long name'); ?> :</dt>
        <dd>%2$s</dd>
        <dt><?php __('Family'); ?> :</dt>
        <dd>%3$s</dd>
        <dt><?php __('Subfamily'); ?> :</dt>
        <dd>%4$s</dd>
    </dl>
</div>
<?php
// $totalCapacity=($countEmployees*$countWorkdays)-$totalCapacity;
$textSummary=round($summary,2)."/".round($totalCapacity,2);
 ?>
<script type="text/javascript">
    var checkWeek = 0;
    $(<?php echo json_encode($a)?>).insertBefore( "#affterLeft" );
    <?php
    $month = date('m', $_start);
    $week = date('W', $_start);
    $year = date('Y', $_start);
    $profit = !empty($this->params['url']['profit']) ? $this->params['url']['profit'] : '';
    ?>
    var $month = <?php echo json_encode($month);?>,
        $week = <?php echo json_encode($week);?>,
        $year = <?php echo json_encode($year);?>,
        $profit = <?php echo json_encode($profit);?>;
    var base = <?php echo json_encode($this->Html->url('/')) ?>;
    $('#typeRequest').change(function () {
        var linkRequest = '<?php echo $this->Html->url('/') ?>activity_forecasts/<?php echo $ACTION;?>/';
        if($(this).val() == 'week'){ // change month to week
            linkRequest += 'week';
        } else if($(this).val() == 'month'){ // change week to month
            linkRequest += 'month';
        } else { // change to year
            $month = 1;
            linkRequest += 'year';
        }
        var refreshLink = '';
        refreshLink = linkRequest + '?year=' + $year + '&month=' + $month + '&profit=' + $profit;
        window.location.href = refreshLink;
    });
    $('#summary').html(<?php echo json_encode($textSummary) ?>);

    <?php if($typeSelect=='week'){ ?>
    checkWeek = 1;
    function resetWidthLeftAndRight()
    {
        var wAW=$('#absence-wrapper').width();
        var wAF=$('#absence-fixed').width();
        var wAS=wAW-wAF-30;
        $('#absence').css({'width':wAS});
    }
    <?php }?>
    function fixedHeightRows()
    {
        <?php
        $j=0;
        foreach ($employees as $id => $employee) :
                $j++  ;?>
                var i = $('tr.tdLeftRow-<?php echo $id;?>').height() + parseInt( $('tr.tdLeftRow-<?php echo $id;?>').css('border-top-width') ) + parseInt( $('tr.tdLeftRow-<?php echo $id;?>').css('border-bottom-width') ) ;
                // if($.browser.mozilla == true) i++;
                $('tr.tdRightRow-<?php echo $id;?>').css("height",i);
        <?php endforeach;?>
    }

    //$(function () {
        $("#scrollTopAbsence").scroll(function () {
            $("#absence-scroll").scrollLeft($("#scrollTopAbsence").scrollLeft());
        });
        $("#absence-scroll").scroll(function () {
            $("#scrollTopAbsence").scrollLeft($("#absence-scroll").scrollLeft());
        });
        $("#scrollLeftAbsence").scroll(function () {
            $(".tbl-tbody").scrollTop($('#scrollLeftAbsence').scrollTop());
            $("#absence-table-fixed").scrollTop($('#scrollLeftAbsence').scrollTop());
        });
    //});
    function configSizeScroll(hd){
        //hd:hright default
        $("#scrollTopAbsenceContent").width($("#absence").width());
        $("#scrollTopAbsence").width($("#absence-scroll").width());
        $("#scrollLeftAbsenceContent").height($("#absence-table").height());
        var hHead=$('.height-header-fixed').height()+5;
        $("#scrollLeftAbsence").css({'marginTop':(hHead)+'px'});
        $(".tdColEmployee").width($("#thColEmployee").width()+7);
        $(".tdColCapacity").width($("#thColCapacity").width()+7);
        if(hd!=600)
        {
            hd=hd-hHead-25;
        }
        $('.tbl-tbody').height(hd);
        $("#scrollLeftAbsence").height(hd);
        if($.browser.mozilla == true)
        var heightTemp = $(".height-header-fixed").height();
        else
        var heightTemp = $(".height-header-fixed").height()+2;
        $(".height-header-fixed-left").height(heightTemp);
        fixedHeightRows();
    }
    var temp = setInterval(function(){
        $(window).resize();
        if(checkWeek == 1)
        {
            resetWidthLeftAndRight();
        }
        height = $('#absence-table').height() + 80;
        clearInterval(temp);
    },1000);
    var xdelay, saving = false;
    $(window).resize(function() {
        clearTimeout(xdelay);
        xdelay = setTimeout(function(){
            if(checkWeek == 1)
            {
                resetWidthLeftAndRight();
            }
            height = $('#absence-table').height() + 80;
            configSizeScroll(height);
        }, 200);
        /**
         * Chinh sua popup
         */
        var _lWidth = $(window).width();
        var _DialogFull = Math.round((95*_lWidth)/100);
        $('#popup_infor_task').parent().css('width', _DialogFull + 'px');
        $('#layout').removeAttr('style');
    }).ready(function(){
        <?php if( $isMobile || $isTablet ): ?>
        $('#absence-fixed').swipe({
            swipeLeft: function(e){
                location.href = $('#absence-prev').prop('href');
            },
            swipeRight: function(e){
                location.href = $('#absence-next').prop('href');
            },
            triggerOnTouchEnd: true
        });
        <?php endif ?>

        $('#ok_save').click(function(){
            if( saving )return false;
            saving = true;
            $('#img-loading').show();
            var can_modify = $(this).data('can_modify');
            if( can_modify ){
                var taskId = $(this).data('taskid');
                $.ajax({
                    url: '<?php echo $this->Html->url('/activity_forecasts/saveTask') ?>',
                    data: {
                        data: {
                            id: $('#task-id').val(),
                            name: $('#task-name').val(),
                            status: $('#select-status').val(),
                            container: $('#container-id').val(),
                            profile_id: $('#select-profile').val(),
                        }
                    },
                    type: 'POST',
                    dataType: 'json',
                    success: function(data){
                        if( data.status ){
                            //doi ten task
                            $('.' + taskId).find('span').text($('#task-name').val());
                            var img = $('.i' + taskId);
                            var status = $('#select-status option:selected').data('status');
                            if( status == 'CL' )img.show();
                            else img.hide();
                            fixedHeightRows();
                            //close dialog
                            $('#popup_infor_task').dialog('close');
                        } else {
                            alert(data.message);
                        }
                    },
                    complete: function(){
                        saving = false;
                        $('#img-loading').hide();
                    }
                });
            }
        });

        $('#icon-url, #icon-download').click(function(){
            openAttachment.call(this);
        });
        $('#icon-folder').click(function(){
            openAttachmentDialog.call(this);
        });
        $('#icon-delete').click(function(){
            deleteAttachment.call(this);
        });
        $('#icon-text').click(function(){
            openTextDialog.call(this);
        });

        var isSaving = false;
        $('#dialog-text').dialog({
            position    :'center',
            autoOpen    : false,
            modal       : true,
            width       : 500,
            autoHeight  : true,
            close: function(){

            },
            buttons: [
                {
                    text: '<?php __('OK') ?>',
                    'class': 'btn btn-ok',
                    'id': 'save-text',
                    click: function(){
                        if( isSaving )return false;
                        isSaving = true;
                        var me = $(this);
                        var t = $('#text-id').val().split('-');
                        var url = base + 'project_tasks/update_text';
                        if (t[0] == 'ac'){
                            url = base + 'activity_tasks/update_text';
                        }
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                data: {
                                    id: t[1],
                                    text_1: $('#text-value').val()
                                }
                            },
                            complete: function(){
                                me.dialog('close');
                            }
                        });
                    }
                },
                {
                    text: '<?php __('Cancel') ?>',
                    'class': 'btn btn-cancel',
                    'id': 'close-text',
                    click: function(){
                        $(this).dialog('close');
                    }
                }
            ]
        });
        $('#dialog_attachement_or_url').dialog({
            position    :'center',
            autoOpen    : false,
            autoHeight  : true,
            modal       : true,
            width       : 500,
            close: function(){
                $('.update_url').val('');
                $('.update_attach_class').val('');
                $('#save-attachment, #close-attachment').removeClass('grayscale');
            },
            buttons: [
                {
                    text: '<?php __('OK') ?>',
                    'class': 'btn btn-ok',
                    'id': 'save-attachment',
                    click: function(){
                        if( isSaving )return false;
                        isSaving = true;
                        var me = $(this);
                        var t = $('#UploadId').val().split('-');
                        $('#UploadId').val(t[1]);
                        $('#save-attachment, #close-attachment').addClass('grayscale');
                        var form = $('#form_dialog_attachement_or_url');
                        if (t[0] == 'ac'){
                            form.prop('action', base + 'activity_tasks/update_document');
                        } else {
                            form.prop('action', base + 'project_tasks/update_document');
                        }
                        form.ajaxSubmit({
                            dataType: 'json',
                            success: function(data){
                                if( data.status ){
                                    //update document
                                    updateDocument(data.attachment);
                                }
                                me.dialog('close');
                            },
                            error: function(){
                                me.dialog('close');
                            }
                        });
                    }
                },
                {
                    text: '<?php __('Cancel') ?>',
                    'class': 'btn btn-cancel',
                    'id': 'close-attachment',
                    click: function(){
                        $(this).dialog('close');
                    }
                }
            ]
        });
        $("#gs-url").click(function(){
            $(this).addClass('gs-url-add');
            $('#gs-attach').addClass('gs-attach-remove');
            $('.update_url').removeAttr('disabled').css('border', '1px solid #3B57EE');
            $('.update_attach_class').attr('disabled', 'disabled').css('border', '1px solid #d4d4d4');
        });
        $("#gs-attach").click(function(){
            $(this).removeClass('gs-attach-remove');
            $('#gs-url').removeClass('gs-url-add');
            $('.update_attach_class').removeAttr('disabled').css('border', '1px solid #3B57EE');;
            $('.update_url').attr('disabled', 'disabled').css('border', '1px solid #d4d4d4');
        });
    });
    // tootip cho forecasts
    var listProject = <?php echo json_encode($listProject);?>,
        listPart = <?php echo json_encode($listPart);?>,
        listPhase = <?php echo json_encode($listPhase);?>,
        acOfTask = <?php echo json_encode($acOfTask);?>,
        allActivies = <?php echo json_encode($allActivies);?>,
        families = <?php echo json_encode($families);?>,
        company_id = <?php echo json_encode($employeeName['company_id']) ?>,
        consultant = <?php echo json_encode($employee_info['Role']['id'] == 4) ?>;

    $(document).on('mouseenter','.activity-tootip-forecast' , function(e){

        var $el = $(this);
        var tmpRel = rel = $el.attr('rel');
        var day = $el.attr('day');
        var idCurrent = rel;
        rel = rel.split('-');
        var _id = rel[2] ? rel[2] : 0;
        var _val = $('#' + tmpRel).html();
        var _name = $el.html() ? $el.html() : '';
        if(rel[0] === 'pr'){
            $('#tooltip-project').html(listProject[_id] ? listProject[_id] : '');
            $('#tooltip-part').html(listPart[_id] ? listPart[_id] : '');
            $('#tooltip-phase').html(listPhase[_id] ? listPhase[_id] : '');
            $('#tooltip-pr-task').html(_name + '(' + _val + ')');
            var content = $('#tooltip-template-pr').html();
        } else {
            var activityId = acOfTask[_id] ? acOfTask[_id] : 0;
            var _acti = allActivies[activityId] ? allActivies[activityId] : '';
            var _rname = _acti.name ? _acti.name : '',
                _sname = _acti.short_name ? _acti.short_name : '',
                _lname = _acti.long_name ? _acti.long_name : '',
                _fam = _acti.family_id ? _acti.family_id : '',
                _sfam = _acti.subfamily_id ? _acti.subfamily_id : '';
            $('#tooltip-name').html(_rname);
            $('#tooltip-sname').html(_sname);
            $('#tooltip-lname').html(_lname);
            $('#tooltip-fam').html(families[_fam] ? families[_fam] : '');
            $('#tooltip-sfam').html(families[_sfam] ? families[_sfam] : '');
            $('#tooltip-ac-task').html(_name + '(' + _val + ')');
            var content = $('#tooltip-template').html();
        }
        $el.tooltip({
            maxWidth : 600,
            maxHeight : 500,
            openEvent : 'xtip-show',
            closeEvent : 'xtip-hide',
            content: content
        }).trigger('xtip-show',e);
    }).on('mousedown mouseleave mouseup click','.activity-tootip-forecast' , function(){
        $(this).tooltip('destroy');
    });
    $(document).on('contextmenu', '#absence-table', function(e){
        // return false;
    });
    function openAttachmentDialog(){
        var me = $(this),
            taskId = me.prop('alt');
        $('#UploadId').val(taskId);
        $('#dialog_attachement_or_url').dialog('open');
    }
    function openAttachment(){
        var me = $(this), t = me.prop('alt').split('-');
        if( t[0] == 'pr' ){
            window.open(base + 'project_tasks/view_attachment/' + t[1], '_blank');
        } else {
            window.open(base + 'activity_tasks/view_attachment/' + t[1], '_blank');
        }
    }
    function deleteAttachment(){
        var me = $(this), t = me.prop('alt').split('-');
        if( confirm('<?php __('Delete?') ?>') ){
            //call ajax
            $.ajax({
                url: base + (t[0] == 'pr' ? 'project_tasks' : 'activity_tasks') + '/delete_attachment/' + t[1],
                complete: function(){
                    updateDocument();
                }
            })
        }
    }
    function updateDocument(attachment){
        if( !attachment )attachment = '';
        var type = attachment.substr(0, 3);
        if( type == 'url' ){
            $('#icon-folder, #icon-download').hide();
            $('#icon-url, #icon-delete').show();
        } else if( type == 'fil' ){
            $('#icon-folder, #icon-url').hide();
            $('#icon-download, #icon-delete').show();
        } else {
            $('#icon-download, #icon-delete, #icon-url').hide();
            $('#icon-folder').show();
        }
    }

    function openTextDialog(){
        $('#text-id').val($(this).prop('alt'));
        $('#dialog-text').dialog('open');
    }

    var taskColumns = <?php echo json_encode($taskColumns);?>,
        translate = <?php echo json_encode($translate);?>,
        $ACTION = <?php echo json_encode($ACTION);?>;
    $(document).on('contextmenu', '.activity-tootip-forecast', function(e){
        var lWidth = $(window).width();
        var DialogFull = Math.round((95*lWidth)/100);
        $('#popup_infor_task').dialog({
            position    :'center',
            autoOpen    : false,
            autoHeight  : true,
            modal       : true,
            width       : DialogFull,
            open : function(e){
                var $dialog = $(e.target);
                $dialog.dialog({open: $.noop});
            }
        });
        var $el = $(this);
        var tmpRel = rel = $el.attr('rel');
        var emp = $el.attr('emp');
        var empId = $el.attr('empId');
        var dayF = $el.attr('dayF');
        rel = rel.split('-');
        var _id = rel[2] ? rel[2] : 0;
        var _val = $('#' + tmpRel).html();
        var _title = emp + ' >>> ' + dayF;
        $('#popup_infor_task').addClass('loading');
        $('#popup_infor_task').dialog('option',{title: _title}).dialog("open");
        setTimeout(function(){
            $.ajax({
                url: '<?php echo $html->url(array('action' => 'get_infor_task')); ?>',
                async: true,
                type : 'POST',
                dataType : 'json',
                data: {
                    type: rel[0] ? rel[0] : 'pr',
                    task_id: _id,
                    date: rel[1] ? rel[1] : '',
                    company_id: company_id,
                    employee_id: empId
                },
                success:function(data) {
                    if(data){
                        var tasks = data.tasks ? data.tasks : '';
                        var priorities = data.priorities ? data.priorities : '';
                        var status = data.status ? data.status : '';
                        var profiles = data.profiles ? data.profiles : '';
                        var consume = parseFloat(data.consume),
                            taskId = $el.siblings('img').prop('alt');
                        data.role = parseInt(data.role);
                        if( isNaN(consume) )consume = 0;

                        $('#task-name').val(tasks.name ? tasks.name : '');
                        $('#select-status').val(tasks.status);
                        $('#task-id').val(tasks.id);
                        $('#container-id').val($el.attr('relId'));
                        $('#ok_save').data('taskid', taskId);
                        $('#select-assign').val(empId ? empId : '');
                        $('#select-profile').val(tasks.profile_id);
                        var notModifyName = true,
                            notModifyStatus = true,
                            notModifyAssign = true,
                            notModifyText = true,
                            notModifyFile = true,
                            notModifyProfile = true;
                        if($ACTION == 'my_diary'){
                            if(data.diary_modify){
                                if(data.diary_status){
                                    notModifyStatus = false;
                                }
                                if(data.diary_others_fields){
                                    notModifyName = false;
                                    notModifyText = false;
                                    notModifyFile = false;
                                    notModifyProfile = false;
                                }
                            }
                        } else {
                            if(data.forecast_modify){
                                if(data.forecast_status){
                                    notModifyStatus = false;
                                }
                                if(data.forecast_assigned_to){
                                    notModifyAssign = false;
                                }
                                if(data.forecast_others_fields){
                                    notModifyName = false;
                                    notModifyText = false;
                                    notModifyFile = false;
                                    notModifyProfile = false;
                                }
                            }
                        }
                        notModifyAssign = true;
                        $('#select-status').prop('disabled', notModifyStatus);
                        $('#select-assign').prop('disabled', notModifyAssign);
                        $('#select-profile').prop('disabled', notModifyProfile);
                        if(consume == 0 && !notModifyName){
                            $('#task-name').prop('disabled', false);
                        } else {
                            $('#task-name').prop('disabled', true);
                        }
                        if(!notModifyText && !notModifyFile){
                            updateDocument(tasks.attachment);
                            $('#icon-download, #icon-delete, #icon-url, #icon-folder, #icon-text').prop('alt', $el.data('task'));
                            $('#icon-text').show();
                        } else {
                            $('#icon-download, #icon-delete, #icon-url, #icon-folder, #icon-text').hide();
                        }
                        //check permission for PM

                        var can_modify = true;
                        if( (data.role == 3 && data.can_modify) || data.role == 2 ){
                            $('#task-name, #select-status').prop('disabled', false);
                            $('#ok_save').show().data('can_modify', 1);
                            //attachment
                            updateDocument(tasks.attachment);
                            $('#icon-download, #icon-delete, #icon-url, #icon-folder, #icon-text').prop('alt', $el.data('task'));
                            //text
                            $('#text-value').val(tasks.text_1);
                            $('#icon-text').show();
                        } else {
                            $('#task-name, #select-status').prop('disabled', true);
                            $('#ok_save').hide().data('can_modify', 0);
                            $('#icon-download, #icon-delete, #icon-url, #icon-folder, #icon-text').hide();
                            can_modify = false;
                        }


                        $('#Startdate').html(tasks.start ? tasks.start : '');
                        $('#Enddate').html(tasks.end ? tasks.end : '');
                        $('#Workload').html(tasks.workload ? tasks.workload : 0);
                        //$('#Duration').html('chua co');
                        //$('#Predecessor').html('chua co');
                        $('#InUsed').html(tasks.used ? tasks.used : 0);
                        $('#Priority').html(tasks.priority && priorities[tasks.priority] ? priorities[tasks.priority] : '');
                        //$('#Profile').html(tasks.profile && profiles[tasks.profile] ? profiles[tasks.profile] : '');
                        var consumed = tasks.consumed ? tasks.consumed : 0;
                        var workload = tasks.workload ? tasks.workload : 0;
                        var overload = (parseFloat(consumed) - parseFloat(workload)).toFixed(2);
                        overload = (overload < 0) ? 0 : overload;
                        var completed = (consumed == 0) ? 0 : ((parseFloat(consumed)*100)/(parseFloat(workload)+parseFloat(overload))).toFixed(2);
                        var remain = (parseFloat(workload)+parseFloat(overload)) - parseFloat(consumed);
                        $('#Overload').html(overload ? overload : 0);
                        $('#Consumed').html(consumed ? consumed : 0);
                        completed = completed ? completed : 0;
                        $('#Completed').html(completed + '%');
                        $('#Remain').html(remain ? remain : 0);
                        $('#ok_save').show().data('can_modify', 1);
                        $('#popup_infor_task').removeClass('loading');
                    }
                }
            });
        }, 200);
        return false;
    });
    $(".cancel").live('click',function(){
        $("#popup_infor_task").dialog('close');
        $("#popup_check_drag").dialog('close');
    });
    function bindDrag($element){
        $element.draggable({
          cancel: "a.ui-icon",
          revert: "invalid",
          containment: "document",
          helper: "clone",
          cursor: "move"
        });
    }
    if( !consultant ){
    // let the gallery items be draggable
        var $gallery = $(".dragTable");
        bindDrag($( "div", $gallery ));
    // let the trash be droppable, accepting the gallery items
        $gallery.droppable({
            accept: ".dragTable > div",
            activeClass: "ui-state-highlight",
            drop: function(event, ui) {
                moveImage($(ui.draggable), $(this));
            }
        });
    }
    function checkingAbsence(taskMove, fromEmp, toEmp, $isPc, $isNCT){
        var rel = taskMove.split('-');
        var pr = <?php echo json_encode($keyProfitCenter);?>;
        var result = true;
        $.ajax({
            url: '<?php echo $html->url(array('action' => 'checking_absence')); ?>',
            async: false,
            type : 'POST',
            dataType : 'json',
            data: {
                type: rel[0] ? rel[0] : 'pr',
                task_id: rel[1] ? rel[1] : '',
                company_id: company_id,
                from_emp: fromEmp,
                to_emp: toEmp,
                isPc: $isPc,
                profit_id: pr,
                isNCT: $isNCT
            },
            success:function(data) {
                // false la cho drag drop
                result = data;
            }
        });
        return result;
    }
    /**
     * Tao popup
     */
    $('#popup_check_drag').dialog({
        position    :'center',
        autoOpen    : false,
        autoHeight  : true,
        modal       : true,
        width       : 400,
        open : function(e){
            var $dialog = $(e.target);
            $dialog.dialog({open: $.noop});
        }
    });
    // image moving function
    function moveImage($from, $to){
        var fromEmp = $from.closest('td').data('emp'),
            toEmp = $to.data('emp'),
            $class = $from.attr('class').split(' ')[0],
            $st = $from.find('span').attr('st'),
            $isPc = $from.find('span').attr('ispc'),
            $isNCT = $from.find('span').attr('isnct'),
            $typeSelect = <?php echo json_encode($typeSelect);?>;
        //if(fromEmp == toEmp || $to.closest('tr').find('.' + $class).length || $st == 'CL' || $typeSelect == 'year'){
        if( (fromEmp == toEmp) || $st == 'CL' || $typeSelect == 'year' || toEmp == 'tNotAffec' || ($isNCT == 1) ){
            return false;
        }
        /**
         * Call popup
         */
        $('#popup_check_drag_content p').html('');
        $('#popup_check_drag').addClass('loading');
        $('#popup_check_drag').dialog('option',{title: 'Dragging'}).dialog("open");
        setTimeout(function(){
            var datas = checkingAbsence($class, fromEmp, toEmp, $isPc, $isNCT);
            //return false;
            var project_id = datas['project_id'] ? datas['project_id'] : 0,
                activity_id = datas['activity_id'] ? datas['activity_id'] : 0,
                datas = datas['data'] ? datas['data'] : [];
            $('#absence-table tr.tdLeftRow-' +fromEmp+ ' td').each(function(){
                var CID = $(this).attr('id');
                CID = $('#' + CID);
                var toId = 'row-' + toEmp + '-' + $(this).attr('id').split('-')[2];
                if(datas && datas[CID.data('day')] != 'ab-holiday'){
                    $newElement = $from.clone();
                    // thay doi rel
                    var oldRel = $newElement.find('span').attr('rel').split('-');
                    $newElement.find('span').attr('rel', oldRel[0] + '-' + CID.data('day') + '-' + oldRel[2] + '-' + toEmp);
                    // thay doi cac thong so khac
                    $newElement.find('span').attr('day', CID.data('day'));
                    $newElement.find('span').attr('empid', toEmp);
                    $newElement.find('span').attr('emp', $to.data('empname'));
                    $newElement.find('span').attr('dayf', CID.data('dayf'));
                    $newElement.find('span').attr('ispc', 0);
                    $newElement.find('i').attr('id', oldRel[0] + '-' + CID.data('day') + '-' + oldRel[2] + '-' + toEmp);
                    $newElement.find('i').html(datas[CID.data('day')]);
                    // check neu employee to co task nay roi thi xoa di
                    if($('#' + toId).find('.' + $class).length){
                        $('#' + toId).find('.' + $class).remove();
                    }
                    $('#' + toId).append($newElement);
                    bindDrag($newElement);
                }
                var $element = $(this).find('.' + $class).not('.ui-draggable-dragging');
                if($element.length){
                    $element.remove();
                }
            });
            fixedHeightRows();
            $('#popup_check_drag_content p').html('Successfully Dragging!');
            $('#popup_check_drag').removeClass('loading');
            // SAVE STAFFING
            setTimeout(function(){
                var _url = '/activity_tasks/staffingWhenUpdateTask/' + activity_id;
                if(project_id != 0){
                    _url = '/project_tasks/staffingWhenUpdateTask/' + project_id;
                }
                $.ajax({
                    url: _url,
                    async: true,
                    type : 'POST',
                    dataType : 'json',
                    success:function(data) {
                    }
                });
            }, 1000);
        }, 1000);
    }
    // double left click to Not Affected
    $('.activity-tootip-forecast').dblclick(function(){
        if( consultant )return false;
        var key = $(this).attr('empid'),
            _id = $(this).attr('relid').split('-');
            rel = $(this).attr('rel').split('-');
        //if(key == 'tNotAffec'){
            var _url = '/activity_tasks/index/' + _id[1] + '?id=' + rel[2];
            if(_id[0] == 'pr'){
                var _idPTask = $(this).attr('idPTask');
                _url = '/project_tasks/index/' + _id[1] + '?id=' + _idPTask;
            }
            window.open(_url, '_blank');
            window.focus();
        //}
    });
    var wdTable = $('.wd-table');
    var heightTable = $(window).height() - wdTable.offset().top - 40;
    //heightTable = (heightTable < 500) ? 500 : heightTable;
    wdTable.css({
        height: heightTable,
    });
    console.log(heightTable);
    $(window).resize(function(){
        heightTable = $(window).height() - wdTable.offset().top - 70;
        wdTable.css({
            height: heightTable,
        });
    });
</script>
<div id="dialog_attachement_or_url" class="buttons" style="display: none;">
    <fieldset>
        <?php
        echo $this->Form->create('Upload', array(
                'type' => 'file', 'id' => 'form_dialog_attachement_or_url',
                'url' => array('controller' => 'project_tasks', 'action' => 'update_document')
            )); ?>
        <div style="height:auto;" class="wd-scroll-form">
            <div class="wd-input">
                <label for="attachement"><?php __("Attachment") ?></label>
                <p id="gs-attach"></p>
                <?php echo $this->Form->hidden('id', array('rel' => 'no-history', 'value' => '')) ?>
                <?php
                echo $this->Form->input('attachment', array('type' => 'file', 'value' => '',
                    'name' => 'FileField[attachment]',
                    'label' => false,
                    'class' => 'update_attach_class',
                    'rel' => 'no-history'));
                ?>
            </div>
            <div class="wd-input">
                <label for="url"><?php __("Url") ?></label>
                <p id="gs-url"></p>
                <?php
                echo $this->Form->input('url', array('type' => 'text',
                    'label' => false,
                    'class' => 'update_url',
                    'disabled' => 'disabled',
                    'placeholder' => 'Ex: www.example.com',
                    'rel' => 'no-history'
                ));
                ?>
            </div>
            <p style="margin-left: 20px;font-size: 12px; color: black">
                <span style="color: green;"><?php __('Allowed file type') ?>:</span> <?php echo str_replace(',', ', ', $fileTypes) ?>
            </p>
        </div>
        <?php
        echo $this->Form->end();
        ?>
    </fieldset>
    <!-- <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel" onclick="$('#dialog_attachement_or_url').dialog('close')"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_attach"><?php __('OK') ?></a></li>
    </ul> -->
</div>
<div id="dialog-text" class="buttons" style="display: none;">
    <fieldset>
        <?php
        echo $this->Form->create(false, array(
                'type' => 'file', 'id' => 'form-text',
                'url' => array('controller' => 'project_tasks', 'action' => 'update_text')
            )); ?>
        <div class="wd-input">
            <?php echo $this->Form->hidden('id', array('rel' => 'no-history', 'id' => 'text-id')) ?>
            <?php
            echo $this->Form->textarea('text_1', array(
                'label' => false,
                'rel' => 'no-history',
                'style' => 'height: 200px; width: 90%; margin-left: 4%',
                'id' => 'text-value'
            ));
            ?>
        </div>
        <?php
        echo $this->Form->end();
        ?>
    </fieldset>
</div>
