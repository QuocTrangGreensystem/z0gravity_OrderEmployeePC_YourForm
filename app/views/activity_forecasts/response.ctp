<?php  echo $this->Html->css(array(
        'projects',
        'context/jquery.contextmenu'
    ));
    $avg = intval(($_start + $_end)/2);
?>
<style>#absence-scroll {
    overflow-x:hidden !important;
    overflow-y: auto;
}
tbody#absence-table tr td{width: 142px !important;}td.friday{border-right: 2px solid red !important;}
.week-color{background-color: #7CB5D2;}
#time-selected{display: none;}
.week-rejected{background-color: #eba1a1;}
.week-validated, .week-absenced{background-color: #c3dd8c;}
.week-waiting{background-color: #f6d5b9;}
.popup-rejected{color: #fff;font-weight: bold;}
.popup-validated{color: #fff;font-weight: bold;}
.foreDayCols{min-width:216px;max-width:216px;width:216px;overflow:hidden;}
#thColStatus{ width:140px !important;}
/*#absence-scroll, #absence{
    height:600px !important;
    overflow-y:hidden;
}*/
/*#absence-fixed thead{
    display: table;
}
tbody#absence-table-fixed{
    display:block;
    position:relative;
    overflow: hidden;
}
#absence thead{
    display: table;
}
tbody#absence-table {
    display:block;
    position:relative;
    overflow: hidden;
}
#absence-table tr tdv{

}*/
#absence-wrapper{
    height:auto !important;
}
#scrollTopAbsence{
    margin-right:20px;
}
#absence-table tr td div{
    min-width:222px !important;
    width:222px;
    max-width:222px !important;
    overflow:hidden;
    word-break:break-all;

}
#absence-table tr td{
    vertical-align:top;
}
#absence-table tr td.separator-week-response-act div{
    min-width:221px !important;
    width:221px;
    max-width:221px !important;
}
.dialog-request-message {
    float: left;
    color: red;
    font-size: 12px;
    padding: 10px 0 10px 10px;
}
#wd-container-footer{
    display: none;
}
body{
    overflow: hidden;
}
.wd-tab {
	max-width: 1920px;
}
.wd-tab .wd-panel{
	padding: 0;
	border: none;
}
#table-control{
	margin-bottom: 0;
}
</style>
<?php 

$svg_icons = array(
		'message' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(-4 -4)"><rect class="a" width="16" height="16" transform="translate(4 4)"/><path class="b" d="M10.5,8h-5a.5.5,0,0,0,0,1h5a.5.5,0,1,0,0-1ZM8,0C3.581,0,0,3.134,0,7a6.7,6.7,0,0,0,3,5.459V16l4.1-2.048c.3.029.6.047.9.047,4.418,0,8-3.134,8-7S12.417,0,8,0ZM8,13H7L4,14.5V11.891A5.772,5.772,0,0,1,1,7C1,3.686,4.133,1,8,1s7,2.686,7,6S11.865,13,8,13Zm3.5-8h-7a.5.5,0,0,0,0,1h7a.5.5,0,1,0,0-1Z" transform="translate(4.001 4)"/></g></svg>',
		'expand' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(-216 -168)"><rect class="a" width="16" height="16" transform="translate(216 168)"/><path class="b" d="M902-2125h-4v-1h3v-3h1v4Zm-8,0h-4v-4h1v3h3v1Zm8-8h-1v-3h-3v-1h4v4Zm-11,0h-1v-4h4v1h-3v3Z" transform="translate(-672 2307)"/></g></svg>',
		'reload' => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16"><g transform="translate(-1323 -240)"><path class="b" d="M199.5,191.5a7.98,7.98,0,0,0-5.44,2.15v-1.51a.64.64,0,0,0-1.28,0v3.2a.622.622,0,0,0,.113.341l.006.009a.609.609,0,0,0,.156.161c.007.005.01.013.017.018s.021.009.031.015a.652.652,0,0,0,.115.055.662.662,0,0,0,.166.034c.012,0,.023.007.036.007h3.2a.64.64,0,1,0,0-1.28h-1.8a6.706,6.706,0,1,1-2.038,4.8.64.64,0,1,0-1.28,0,8,8,0,1,0,8-8Z" transform="translate(1131.5 48.5)"/><rect class="a" width="16" height="16" transform="translate(1323 240)"/></g></svg>',
		'duplicate' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(-1621.625 -334.663)"><rect class="a" width="16" height="16" transform="translate(1621.625 334.663)"/><g transform="translate(36.824 46.863)"><path class="b" d="M1586.915,301.177a1.116,1.116,0,0,1-1.115-1.115V288.915a1.116,1.116,0,0,1,1.115-1.115h8.525a1.116,1.116,0,0,1,1.115,1.115v11.147a1.115,1.115,0,0,1-1.115,1.115Zm0-12.459a.2.2,0,0,0-.2.2v11.147a.2.2,0,0,0,.2.2h8.525a.2.2,0,0,0,.2-.2V288.915a.2.2,0,0,0-.2-.2Z"/><path class="b" d="M1590.915,305.177a1.116,1.116,0,0,1-1.115-1.115v-.656a.459.459,0,1,1,.918,0v.656a.2.2,0,0,0,.2.2h8.525a.2.2,0,0,0,.2-.2V292.915a.2.2,0,0,0-.2-.2h-.656a.459.459,0,0,1,0-.918h.656a1.116,1.116,0,0,1,1.115,1.115v11.147a1.115,1.115,0,0,1-1.115,1.115Z" transform="translate(-0.754 -1.377)"/></g></g></svg>',
		'agenda' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(0)"><rect class="a" width="16" height="16" transform="translate(0)"/><path class="b" d="M15,16H1a1,1,0,0,1-1-1V2.5a1,1,0,0,1,1-1h4V.5a.5.5,0,0,1,1,0v1h4V.5a.5.5,0,1,1,1,0v1h4a1,1,0,0,1,1,1V15A1,1,0,0,1,15,16ZM15,2.5H11V3a.5.5,0,1,1-1,0V2.5H6V3a.5.5,0,0,1-1,0V2.5H1V15H15ZM3.5,6h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1A.5.5,0,0,1,3,7.5v-1A.5.5,0,0,1,3.5,6Zm0,4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,3.5,10Zm4-4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1A.5.5,0,0,1,7,7.5v-1A.5.5,0,0,1,7.5,6Zm0,4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,7.5,10Zm4-4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,11.5,6Zm0,4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,11.5,10Z" transform="translate(0.001 -0.001)"/></g></svg>',
		'add' => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16.002 16.002"><g transform="translate(-120 -231.999)"><rect class="a" width="16" height="16" transform="translate(120 231.999)"/><path class="b" d="M21284,8418v-6h-6a1,1,0,0,1,0-2h6v-6a1,1,0,1,1,2,0v6h6a1,1,0,0,1,0,2h-6v6a1,1,0,1,1-2,0Z" transform="translate(-21157 -8171)"/></g></svg>',
		'users' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(-192 -132)"><rect class="a" width="16" height="16" transform="translate(192 132)"/><g transform="translate(192 134)"><path class="b" d="M205.507,144.938a4.925,4.925,0,0,0-9.707,0h-1.1a6.093,6.093,0,0,1,3.557-4.665l.211-.093-.183-.14a3.941,3.941,0,1,1,4.75,0l-.183.14.21.093a6.1,6.1,0,0,1,3.552,4.664Zm-4.851-10.909a2.864,2.864,0,1,0,2.854,2.864A2.863,2.863,0,0,0,200.657,134.029Z" transform="translate(-194.697 -132.938)"/><path class="b" d="M214.564,143.9a2.876,2.876,0,0,0-2.271-2.665.572.572,0,0,1-.449-.555.623.623,0,0,1,.239-.507,2.869,2.869,0,0,0-1.344-5.114,4.885,4.885,0,0,0-.272-.553,5.52,5.52,0,0,0-.351-.556c.082-.005.164-.008.245-.008a3.946,3.946,0,0,1,3.929,3.955,3.844,3.844,0,0,1-.827,2.406l-.1.13.147.076a3.959,3.959,0,0,1,2.132,3.392Z" transform="translate(-199.639 -133.26)"/></g></g></svg>',
		'validated' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 40 40"><g transform="translate(-317 -66)"><rect class="a" width="32" height="32" transform="translate(317 66)"/><path class="b" d="M9.791,1.412h0L4.314,7.757h0a.648.648,0,0,1-1.01,0h0L.209,4.171h0A.9.9,0,0,1,0,3.585a.777.777,0,0,1,.714-.827A.668.668,0,0,1,1.219,3h0l2.59,3L8.781.242h0A.668.668,0,0,1,9.285,0,.778.778,0,0,1,10,.827.9.9,0,0,1,9.791,1.412Z" transform="translate(332 82.001)"/></g></svg>',
		'reject' => '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><g transform="translate(-323 -71)"><rect class="a" width="32" height="32" transform="translate(-323 -71)"/><path class="b" d="M-287.4-1709.767a.62.62,0,0,1,0-.876l3.942-3.942-3.942-3.943a.619.619,0,0,1,0-.876.619.619,0,0,1,.876,0l3.943,3.943,3.943-3.943a.619.619,0,0,1,.876,0,.619.619,0,0,1,0,.876l-3.942,3.943,3.942,3.942a.62.62,0,0,1,0,.876.62.62,0,0,1-.876,0l-3.943-3.943-3.943,3.943a.618.618,0,0,1-.438.182A.618.618,0,0,1-287.4-1709.767Z" transform="translate(620.586 1800.587)"/></g></svg>',);
echo $html->script('context/jquery.contextmenu'); ?>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"><div class="wd-panel">
            <div class="wd-list-project">
                <div id="message-place">
                    <?php
                    echo $this->Session->flash();
                    ?>
                </div>
                <div class="wd-table" id="project_container" style="width:100%;">
                    <div id="absence-container" style="min-height:400px;">
                        <div id="table-control" class="wd-activity-actions">
                            <?php
                            echo $this->Form->create('Control', array(
                                'type' => 'get',
                                'id' => 'formControl',
                                'url' => '/' . Router::normalize($this->here)));
                            ?>
                            <fieldset style="margin-left: 22px; float: left; margin-right: 5px">
                                <?php
                                 echo $this->element('week_activity');
                                ?>
                                <?php
                                echo $this->Form->select('profit', $paths, $profit['id'], array('empty' => false, 'escape' => false, 'style' => 'padding: 6px'));
                                ?>
                                <button class="btn btn-go"></button>
                                <div style="clear:both;"></div>
                            </fieldset>
                            <a href="javascript:;" onclick="expandScreen();" id="expand-btn" class="btn btn-fullscreen">
								<?php echo $svg_icons['expand'];?>
							</a>
                            <?php
                                if($typeSelect != 'year'):
                                    echo $this->element('btn_expand_pc');
                            ?>
                            <a href="javascript:void(0)" id="submit-request-ok-top" class="validate-for-validate validate-for-validate-top" title="<?php __('Validate Requested')?>"><?php echo $svg_icons['validated'];?></a>
                            <a href="javascript:void(0)" id="submit-request-no-top" class="validate-for-reject validate-for-reject-top" title="<?php __('Reject Requested')?>"><?php echo $svg_icons['reject']?></a>
                            <?php
                                endif;
                            ?>
                            <?php
                            echo $this->Form->end();
                            ?>
                        </div>
                        <div id="absence-wrapper">
                            <div id="scrollTopAbsence"><div id="scrollTopAbsenceContent"></div></div>
                            <br clear="all"  />
                            <?php
                            $status = array(0 => __('Waiting validation', true), 1 => __('Rejected', true), 2 => __('Validated', true), -1 => __('In progress', true));
                            $types = '';
                            $urls = array();
                            if($typeSelect === 'week'){
                                ?><style>#absence-fixed{ width:34% !important; }</style><?php
                                $types = 'week';
                                $urls = array('week' => date('W', $avg), 'year' => date('Y', $avg),'profit' => $profit['id']);
                            } elseif($typeSelect === 'month'){
                                ?><style>#absence-fixed{ width:34% !important; }</style><?php
                                $types = 'month';
                                $urls = array('month' => date('m', $_start), 'year' => date('Y', $_start),'profit' => $profit['id']);
                            } else {
                                $types = 'year';
                                $urls = array('month' => date('m', $avg), 'year' => date('Y', $avg),'profit' => $profit['id']);
                            }
                            if($getDataByPath)
                            $urls['get_path'] = $getDataByPath;
                            echo $this->Form->create('Request', array(
                                'escape' => false,
                                'id' => 'request-form',
                                'type' => 'post',
                                'url' => array('controller' => 'activity_forecasts', 'action' => 'response', $types, '?' => $urls)));
                            ?>
                            <?php
                                echo $this->Form->input('selected', array('id'=>'time-selected','type' => 'text', 'label' => false));
                            ?>
                            <div id="scrollLeftAbsence">
                                <div id="scrollLeftAbsenceContent"></div>
                            </div>
                            <table id="absence-fixed">
                            <tr class="elmTemp">
                            <td class="elmTemp">
                            <table>
                                <thead>
                                    <tr class="header-height-fixed">
                                        <th id="thColID" style="width:6% !important"><?php __('#'); ?></th>
                                        <th id="thColEmployee" style="width:50% !important"><?php __('Employee'); ?></th>
                                        <th id="thColStatus" style="width:24% !important"><?php __('Status'); ?></th>
                                        <th id="thColCapacity" style="width:20% !important"><?php __('Capacity'); ?></th>
                                     </tr>
                                </thead>
                             </table>
                             </td>
                             </tr>
                             <tr class="elmTemp">
                                <td class="elmTemp">
                                    <div class="tbl-tbody" >
                                    <table>
                                        <tbody id="absence-table-fixed"></tbody>
                                     </table>
                                    </div>
                                </td>
                             </tr>
                            </table>
                            <div id="absence-scroll">
                                <table id="absence">
                                <tr class="elmTemp">
                                <td class="elmTemp">
                                <table>
                                    <thead>
                                        <tr class="header-height">
                                        <?php
                                            if(!empty($listWorkingDays)):
                                                foreach($listWorkingDays as $key => $val):
                                        ?>
                                        <th class="<?php echo ($typeSelect == 'week') ? '' : 'foreDayCols';?>" width="<?php echo ($typeSelect == 'week') ? '11%' : '149px';?>" id="<?php echo 'fore'.date('l', $val);?>"><?php echo __(date('l', $val)) . __(date(' d ', $val)) . __(date('M', $val)); ?></th>
                                        <?php
                                                endforeach;
                                            endif;
                                        ?>
                                         </tr>
                                    </thead>
                                    </table>
                                    </td></tr>
                                    <tr class="elmTemp"><td class="elmTemp">
                                    <div class="tbl-tbody" >
                                    <table id="absence-table-container">
                                    <tbody id="absence-table">
                                        <?php if (!empty($employees)) : ?>
                                            <?php
                                            asort($employees);
                                            $contentAbsenFixeds = '';
                                            foreach ($employees as $id => $employee) :
                                            ?>
                                            <tr class="r_height_<?php echo $id;?>">
                                            <?php
                                                $output = '';
                                                $capacity = 0;
                                                $contentAbsenFixeds .= '<tr class ="l_height_' . $id . '"><td class ="tdColID height_' . $id . '" style="vertical-align: middle;">';
                                                $showCheckbox = false;
                                                if($typeSelect == 'week'){
                                                    if(isset($requestConfirms[$id]) && $requestConfirms[$id] == 0){
                                                        $showCheckbox = true;
                                                    }
                                                } else {
                                                    if(!empty($requestConfirms[$id]) && in_array('0', $requestConfirms[$id])){ // co 1 hoac nhieu tuan sent thi waiting
                                                        $showCheckbox = true;
                                                    }
                                                }
                                                if ($showCheckbox) {
                                                    $checkBox = $this->Form->input('id.' . $id, array('type' => 'checkbox', 'label' => false, 'hiddenField' => false));
                                                    $contentAbsenFixeds .= $checkBox;
                                                }
                                                $contentAbsenFixeds .= '</td>';
                                                $countWeek = 0;//dem so tuan cua thang
                                                $classWeek = 0; //class cua week
                                                foreach ($listWorkingDays as $day => $time) {
                                                    //pr(date('d-m-Y l', $time));
                                                    $text = array();
                                                    $classWeekAbsenced = '';
                                                    if(!empty($requestConfirms[$id]) && $requestConfirms[$id] == -1){
                                                        // dang inprogress
                                                    } else {
                                                        if($typeSelect == 'week'){
                                                            if(!empty($requestConfirms) && isset($requestConfirms[$id])){
                                                                if($requestConfirms[$id] == -1){
                                                                    $classWeekAbsenced = '';
                                                                } elseif($requestConfirms[$id] == 0){
                                                                    $classWeekAbsenced = 'td-waiting';
                                                                } elseif($requestConfirms[$id] == 1){
                                                                    $classWeekAbsenced = 'td-rejected';
                                                                } elseif($requestConfirms[$id] == 2){
                                                                    $classWeekAbsenced = 'td-validate';
                                                                }
                                                            } else {
                                                                $classWeekAbsenced = '';
                                                            }
                                                        } else {
                                                            $mondayOfWeekendCurrents = (strtolower(date('l', $time)) === 'monday') ? $time : strtotime('last monday', $time);
                                                            if(!empty($requestConfirms) && !empty($requestConfirms[$id]) && isset($requestConfirms[$id][$mondayOfWeekendCurrents])){
                                                                if($requestConfirms[$id][$mondayOfWeekendCurrents] == -1){
                                                                    $classWeekAbsenced = '';
                                                                } elseif($requestConfirms[$id][$mondayOfWeekendCurrents] == 0){
                                                                    $classWeekAbsenced = 'td-waiting';
                                                                } elseif($requestConfirms[$id][$mondayOfWeekendCurrents] == 1){
                                                                    $classWeekAbsenced = 'td-rejected';
                                                                } elseif($requestConfirms[$id][$mondayOfWeekendCurrents] == 2){
                                                                    $classWeekAbsenced = 'td-validate';
                                                                }
                                                            } else {
                                                                $classWeekAbsenced = '';
                                                            }
                                                        }
                                                    }
                                                    if($managerHour){
                                                        $gHour = !empty($hourDayOfEmployees[$id]) ? $hourDayOfEmployees[$id] : '00:00';
                                                        $gHour = explode(':', $gHour);
                                                        $totalMinutes = $gHour[0] * 60 + $gHour[1];
                                                        $totalMinutes = round($totalMinutes/2, 0);
                                                    }
                                                    if (isset($holidays[$time])) {
                                                        $hlAm = isset($holidays[$time]['am']) ? 0.5 : 0;
                                                        $hlPm = isset($holidays[$time]['pm']) ? 0.5 : 0;
                                                        $holiValue = $hlAm + $hlPm;
                                                        if($managerHour){
                                                            if($holiValue == 1){
                                                                $m = ($gHour[1] < 10) ? '0'.$gHour[1] : $gHour[1];
                                                                $h = ($gHour[0] < 10) ? '0'.$gHour[0] : $gHour[0];
                                                            } else {
                                                                $m = $totalMinutes%60;
                                                                $h = ($totalMinutes - $m)/60;
                                                                $m = ($m < 10) ? '0'.$m : $m;
                                                                $h = ($h < 10) ? '0'.$h : $h;
                                                            }
                                                            $text[] = '<span class="ab-holiday">' . sprintf('%s (%s)', __('Holiday', true), $h . ':' . $m) . '</span>';
                                                        } else {
                                                            $text[] = '<span class="ab-holiday">' . sprintf('%s (%s)', __('Holiday', true), $holiValue) . '</span>';
                                                        }

                                                    } else {
                                                        $vals = array();
                                                        foreach (array('am', 'pm') as $type) {
                                                            if (!empty($requests[$id][$time]['absence_' . $type])) {
                                                                if (!isset($vals[$requests[$id][$time]['absence_' . $type]][$requests[$id][$time]['response_' . $type]])) {
                                                                    $vals[$requests[$id][$time]['absence_' . $type]][$requests[$id][$time]['response_' . $type]] = 0;
                                                                }
                                                                $vals[$requests[$id][$time]['absence_' . $type]][$requests[$id][$time]['response_' . $type]]+=0.5;
                                                            }
                                                        }
                                                        foreach ($vals as $key => $val) {
                                                            // Fix, hide the Provisional Day Off
                                                            if($key == -1){

                                                            }else{
                                                                foreach ($val as $k => $v) {
                                                                    if($managerHour){
                                                                        if($v == 1){
                                                                            $m = ($gHour[1] < 10) ? '0'.$gHour[1] : $gHour[1];
                                                                            $h = ($gHour[0] < 10) ? '0'.$gHour[0] : $gHour[0];
                                                                        } else {
                                                                            $m = $totalMinutes%60;
                                                                            $h = ($totalMinutes - $m)/60;
                                                                            $m = ($m < 10) ? '0'.$m : $m;
                                                                            $h = ($h < 10) ? '0'.$h : $h;
                                                                        }
                                                                        $text[] = '<span class="ab-' . $k . ' ' . $classWeekAbsenced . '">' . sprintf('%s (%s)', $absences[$key]['print'], $h . ':' . $m) . '</span>';
                                                                    } else {
                                                                        $text[] = '<span class="ab-' . $k . ' ' . $classWeekAbsenced . '">' . sprintf('%s (%s)', $absences[$key]['print'], $v) . '</span>';
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    if(!empty($listExternalOfEmps[$id]) && $listExternalOfEmps[$id] == 2 && $manage_multiple_resource){
                                                        $text = array();
                                                    }
                                                    if (isset($activityRequests[$id])) {
                                                        $totalValues = array();
                                                        $classNoneRequest = 'none-request';
                                                        foreach ($activityRequests[$id] as $key => $request) {
                                                            if ($request['ActivityRequest']['date'] == $time) {
                                                                if($managerHour){
                                                                    $value = $request['ActivityRequest']['value_hour'];
                                                                    $tHour = explode(':', $request['ActivityRequest']['value_hour']);
                                                                    $tHour = $tHour[0] * 60 + $tHour[1];
                                                                    if(!isset($totalValues[$request['Activity']['id']])){
                                                                        $totalValues[$request['Activity']['id']] = 0;
                                                                    }
                                                                    $totalValues[$request['Activity']['id']] += $tHour;
                                                                } else {
                                                                    $value = floatval($request['ActivityRequest']['value']);
                                                                    if(!isset($totalValues[$request['Activity']['id']])){
                                                                        $totalValues[$request['Activity']['id']] = 0;
                                                                    }
                                                                    $totalValues[$request['Activity']['id']] += $request['ActivityRequest']['value'];
                                                                }

                                                                $textName = $request['Activity']['name'];
                                                                $class = 'td-waiting';
                                                                if($request['ActivityRequest']['status'] == 2){
                                                                    $class = 'td-validate';
                                                                }
                                                                if($request['ActivityRequest']['status'] == 1){
                                                                    $class = 'td-rejected';
                                                                }
                                                                if($request['ActivityRequest']['status'] == 0){
                                                                     $classNoneRequest = '';
                                                                }
                                                                if($request['ActivityRequest']['status'] == -1){
                                                                    $text[$request['Activity']['id']] = '';
                                                                } else {
                                                                    if($managerHour){
                                                                        $value = explode(':', $value);
                                                                        $value = $value[0] * 60 + $value[1];
                                                                        $capacity+=$value;
                                                                        $totalHour = $totalValues[$request['Activity']['id']];
                                                                        $mTotal = $totalHour%60;
                                                                        $hTotal = ($totalHour - $mTotal)/60;
                                                                        $hTotal = ($hTotal < 10) ? '0' . $hTotal : $hTotal;
                                                                        $mTotal = ($mTotal < 10) ? '0' . $mTotal : $mTotal;
                                                                        $text[$request['Activity']['id']] = sprintf('%s (%s)', '<span class="' . $class . '">' . $textName . '</span>', $hTotal . ':' . $mTotal);
                                                                    } else {
                                                                        $capacity+=$value;
                                                                        $text[$request['Activity']['id']] = sprintf('%s (%s)', '<span class="' . $class . '">' . $textName . '</span>', $totalValues[$request['Activity']['id']]);
                                                                    }
                                                                }
                                                                unset($activityRequests[$id][$key]);
                                                            }
                                                        }
                                                    }else{
                                                        $classNoneRequest = 'none-request';
                                                    }
                                                    $classTd = '';
                                                    if(strtolower(date('l', $time)) == 'monday'){
                                                        $classTd = 'selectedElementWeek ';
                                                    }
                                                    if($typeSelect == 'month' && strtolower(date('l', $time)) == 'monday'){
                                                        $classTd = 'selectedElementWeek separator-week-response-act';
                                                    }
                                                    if($countWeek%$numDays==0){
                                                        $classWeek++;
                                                    }
                                                    $classTd.=' selected-'.$id.' background-'.$id.'-'.$classWeek;
                                                    $output.= '<td class="selected-'.$id.'-'.$time.' '.$classNoneRequest.' height_'.$id. ' ' . $classTd . '"><div>' . implode('</div><div>', $text) . '</div></td>';
                                                $countWeek++;
                                                }
                                                //exit;
                                                echo $output;
                                                /**
                                                 * Build html using table fixed
                                                 */
                                                if($typeSelect === 'week'){
                                                    $urlEmploy = $this->Html->link($employee, array(
                                                        'action' => 'request', 'week',
                                                        '?' => array(
                                                            'id' => $id,
                                                            'profit' => $profit['id'],
                                                            'week' => date('W', $avg),
                                                            'year' => date('Y', $avg), 'get_path' => $getDataByPath)), array('escape' => false));
                                                } elseif($typeSelect === 'month'){
                                                    $urlEmploy = $this->Html->link($employee, array(
                                                        'action' => 'request', 'month',
                                                        '?' => array(
                                                            'id' => $id,
                                                            'profit' => $profit['id'],
                                                            'month' => date('m', $_start),
                                                            'year' => date('Y', $_start), 'get_path' => $getDataByPath)), array('escape' => false));
                                                } else {
                                                    $urlEmploy = $this->Html->link($employee, array(
                                                        'action' => 'request', 'year',
                                                        '?' => array(
                                                            'id' => $id,
                                                            'profit' => $profit['id'],
                                                            'month' => date('m', $avg),
                                                            'year' => date('Y', $avg), 'get_path' => $getDataByPath)), array('escape' => false));
                                                }
                                                $avatarEmploy = $this->UserFile->avatar($id);
                                                if( $showAllPicture ){
                                                    $contentAbsenFixeds .= '<td class="tdColEmployee" style="vertical-align: middle;"><span><div class="circle-name inlineblock circle-30" title="'. strip_tags($employee) .'"><img style="width: 30px; height: 30px;" src="' . $avatarEmploy . '" alt="'. strip_tags($employee).'"></div>' . $urlEmploy . '</span></td>';
                                                } else {
                                                    $contentAbsenFixeds .= '<td class="tdColEmployee" style="vertical-align: middle;"><span>' . $urlEmploy . '</span></td>';
                                                }
                                                //debug($listStatus);
                                                $info = '';
                                                if($showCheckbox){
                                                    $style = 'style="color: #e46c0a !important;"';
                                                    $info.= '<strong class="status" ' . $style . '> (' . __('Waiting validation', true) . ')</strong>';
                                                } else {
                                                    $style = 'style="color: #e46c0a !important;"';
                                                    $statusTest = 0;
                                                    if($typeSelect == 'week'){
                                                        if(isset($requestConfirms[$id])){
                                                            if($requestConfirms[$id] == 1){ // reject
                                                                $style = 'style="color: #c00000 !important;"';
                                                                $statusTest = 1;
                                                            } elseif($requestConfirms[$id] == 2){ //validation
                                                                $style = 'style="color: #77933c !important;"';
                                                                $statusTest = 2;
                                                            } elseif($requestConfirms[$id] == -1){ //chua send
                                                                $style = '';
                                                                $statusTest = -1;
                                                            }
                                                        } else {
                                                            $style = '';
                                                            $statusTest = -1;
                                                        }
                                                    } else {
                                                        if(isset($requestConfirms[$id])){
                                                            if(in_array('0', $requestConfirms[$id])){
                                                                //do nothing
                                                            } else {
                                                                if(in_array('1', $requestConfirms[$id]) || count($requestConfirms[$id]) != count($listWeekOfMonths)){
                                                                    $style = '';
                                                                    $statusTest = -1;
                                                                } else {
                                                                    $style = 'style="color: #77933c !important;"';
                                                                    $statusTest = 2;
                                                                }
                                                            }
                                                        } else {
                                                            $style = '';
                                                            $statusTest = -1;
                                                        }
                                                    }
                                                    $info.= '<strong class="status" ' . $style . '> (' . $status[$statusTest] . ')</strong>';
                                               }
                                               if($typeSelect === 'week'){
                                                    $urlStatus = $this->Html->link($info, array(
                                                        'action' => 'request', 'week',
                                                        '?' => array(
                                                            'id' => $id,
                                                            'profit' => $profit['id'],
                                                            'week' => intval(date('W', $avg)),
                                                            'year' => date('Y', $avg))), array('escape' => false));
                                                } elseif($typeSelect === 'month'){
                                                    $urlStatus = $this->Html->link($info, array(
                                                        'action' => 'request', 'month',
                                                        '?' => array(
                                                            'id' => $id,
                                                            'profit' => $profit['id'],
                                                            'month' => intval(date('m', $_start)),
                                                            'year' => date('Y', $_start))), array('escape' => false));
                                                } else {
                                                    $urlStatus = $this->Html->link($info, array(
                                                        'action' => 'request', 'year',
                                                        '?' => array(
                                                            'id' => $id,
                                                            'profit' => $profit['id'],
                                                            'month' => intval(date('m', $avg)),
                                                            'year' => date('Y', $avg))), array('escape' => false));
                                                }
                                                $contentAbsenFixeds .= '<td class="tdColStatus height_' . $id . '" style="text-align:center; vertical-align: middle;"><span>' . $urlStatus . '</span></td>';
                                                if($managerHour){
                                                    $mCapacity = $capacity%60;
                                                    $hCapacity = ($capacity - $mCapacity)/60;
                                                    $hCapacity = ($hCapacity < 10) ? '0'.$hCapacity : $hCapacity;
                                                    $mCapacity = ($mCapacity < 10) ? '0'.$mCapacity : $mCapacity;
                                                    $contentAbsenFixeds .= ' <td class="tdColCapacity ct height_' . $id . '" style="vertical-align: middle;"><span>' . $hCapacity . ':' . $mCapacity . '</span></td>';
                                                } else {
                                                    $contentAbsenFixeds .= ' <td class="tdColCapacity ct height_' . $id . '" style="vertical-align: middle;"><span>' . $capacity . '</span></td>';
                                                }
                                            ?>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="10" style="text-align: center;color: red;font-weight: bold;"><?php __('Data not found.'); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                    </table>
                                    </div>
                                    </td></tr>
                                </table>
                            </div>
                            <?php
                            echo $this->Form->hidden('validated', array('name' => 'data[validated]', 'value' => 0, 'id' => 'ac-validated'));
                            echo $this->Form->end();
                            ?>
                        </div>
                        <br clear="all"  />
                        <?php
                            if($typeSelect != 'year'):
                        ?>
                        <div class="wd-title123">
                        <br clear="all"  />
                        <a href="javascript:void(0)" id="submit-request-ok" style="display: none" class="validate-for-validate validate-for-validate-bottom" title="<?php __('Validate Requested')?>"><span><?php __('Validate Requested'); ?></span></a>
                        <a href="javascript:void(0)" id="submit-request-no" style="display: none" class="validate-for-reject validate-for-reject-bottom" title="<?php __('Reject Requested')?>"><span><?php __('Reject Requested'); ?></span></a>
                        <?php
                            endif;
                        ?>
                        </div>
                    </div>
                </div>
            </div></div></div>
        </div>
    </div>
</div>
<!-- dialog_vision_portfolio -->
<div id="add-comment-dialog" class="buttons" style="display: none;" title="">
    <div class="dialog-request-message">

    </div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"></a></li>
        <li><a href="javascript:void(0)" class="ok"></a></li>
    </ul>
</div>
<?php
$css = '';
foreach ($constraint as $key => $data) {
    $css .= ".ab-$key {background-color : {$data['color']};border:1px solid #ccc;margin:1px;}";
}
echo '<style type="text/css">' . $css . '</style>';
?>
<!-- dialog_vision_portfolio.end -->
<script type="text/javascript">
    var locale = <?php echo json_encode(array(
        'Please select the employees.' => __('Please select the employees.', true),
        'Reject?' => __('Reject?', true),
        'Validate?' => __('Validate?', true)
    )) ?>;
    function i18n(text){
        if( typeof locale[text] != 'undefined' )return locale[text];
        return text;
    }
    (function($){

        //$(function(){
            var contentAbsenFixeds = <?php echo json_encode($contentAbsenFixeds);?>;
            $('#absence-table-fixed').html(contentAbsenFixeds);
            var openDialog = function(title,callback){
                var $dialog = $('#add-comment-dialog');
                $dialog.find('.dialog-request-message').html(title);
                $dialog.dialog({
                    zIndex : 10000,
                    modal : true,
                    minHeight : 50,
                    close : function(){
                        $dialog.dialog('destroy');
                    }
                });
                $dialog.find('a.ok').unbind().click(function(){
                    if(!$.isFunction(callback)){
                        $dialog.dialog('close');
                    }else{
                        callback.call(this);
                    }
                    return false;
                });
                $dialog.find('a.cancel').unbind().click(function(){
                    $dialog.dialog('close');
                    return false;
                }).toggle($.isFunction(callback));
            };
            $('#submit-request-no, #submit-request-no-top').click(function(){
                var $form = $('#request-form'),$input = $form.find('.checkbox :checked');
                if(!$input.length){
                    openDialog(i18n('Please select the employees.'));
                }else{
                    openDialog(i18n('Reject?'), function(){
                        $('#ac-validated').val(0);
                        $form.submit();
                    });
                }
            });
            $('#submit-request-ok, #submit-request-ok-top').click(function(){
                var $form = $('#request-form'),$input = $form.find('.checkbox :checked');
                if(!$input.length){
                    openDialog(i18n('Please select the employees.'));
                }else{
                    openDialog(i18n('Validate?'), function(){
                        $('#ac-validated').val(1);
                        $form.submit();
                    });
                }
            });
            /* xoa tuan chon
            $('#absence-table tr td.selectedElementWeek').each(function(){
                if($(this).children().children().hasClass('td-validate')){
                    var classStr = $(this).attr('class'),
                    lastClass = classStr.substr( classStr.lastIndexOf(' ') + 1);
                    $('#absence-table tr td.selectedElementWeek.'+lastClass).append("<span class='popup-validated'>VALIDATED<span>");
                }
                if($(this).children().children().hasClass('td-rejected')){
                    var classStr = $(this).attr('class'),
                    lastClass = classStr.substr( classStr.lastIndexOf(' ') + 1);
                    $('#absence-table tr td.selectedElementWeek.'+lastClass).append("<span class='popup-rejected'>REJECTED<span>");
                }
            });
            */

            //checked checkbox
            $('tbody#absence-table-fixed tr td :checkbox').click(function() {
                var $this = $(this).attr('id');
                // $this will contain a reference to the checkbox
                if ($(this).is(':checked')) {
                    var idChecked = $this.substr(2,$this.length);
                    <?php for ($i = 1; $i <= $numWeek; $i++) { ?>
                        if(((!$('.background-'+idChecked+<?php echo '-'.$i;?>).children().children().hasClass('td-validate'))&&(!$('.background-'+idChecked+<?php echo '-'.$i;?>).children().children().hasClass('td-rejected')))){
                            var classStr = $('.background-'+idChecked+<?php echo '-'.$i;?>).attr('class'),
                            lastClass = classStr.substr( classStr.lastIndexOf(' ') + 1);
                            $("tbody#absence-table tr td.selected-"+idChecked).addClass('week-color');
                            var id = lastClass.split("-");
                                ids = id[1];
                            $("#id"+ids).prop('checked', true);
                        }
                        if($('.background-'+idChecked+<?php echo '-'.$i;?>).hasClass('week-waiting')){
                            $('.background-'+idChecked+<?php echo '-'.$i;?>).removeClass('week-waiting');
                            $('.background-'+idChecked+<?php echo '-'.$i;?>).addClass('waitingForValids');
                        }
                    <?php }?>
                } else {
                     //remove class chon tuan
                    var idChecked = $this.substr(2,$this.length);
                    <?php for ($i = 1; $i <= $numWeek; $i++) { ?>
                        if($("tbody#absence-table tr td").hasClass('background-'+idChecked+<?php echo '-'.$i;?>)){
                            $("tbody#absence-table tr td.background-"+idChecked+<?php echo '-'.$i;?>).removeClass('week-color');
                            if(!$('.background-'+idChecked+<?php echo '-'.$i;?>).hasClass('week-validated')&&!$('.background-'+idChecked+<?php echo '-'.$i;?>).hasClass('week-rejected')){
                                $("tbody#absence-table tr td.background-"+idChecked+<?php echo '-'.$i;?>).addClass('week-waiting');
                                $("tbody#absence-table tr td.background-"+idChecked+<?php echo '-'.$i;?>).removeClass('waitingForValids');
                            }
                        }
                    <?php }?>
                }
                // xoa tuan duoc chon
                var selectedMonth = ''; // reset lai danh sach tuan duoc chon
                $('#absence-table tr td').each(function()
                {
                  if($(this).hasClass("week-color") && $(this).hasClass("selectedElementWeek") && $(this).hasClass("waitingForValids")){
                    var classStr = $(this).attr('class');
                    var classFirst = classStr.split(" ");
                        classFirsts = classFirst[0];
                    var idTime = classFirsts.split("-");
                    selectedMonth+=idTime[1]+'-'+idTime[2]+',';
                  }
                });
                selectedMonth = selectedMonth.substr(0,selectedMonth.length-1);
                $('#time-selected').val(selectedMonth);
            });
            $('#absence-wrapper').css("height",$('#absence').height()+50);
            $('th.header-table-fixed').css("height",$('th.header-table').height()-1);
            <?php
            $month = date('m', $avg);
            $week = date('W', $avg);
            $year = date('Y', $avg);
            $profit = !empty($this->params['url']['profit']) ? $this->params['url']['profit'] : '';
            ?>
            var $month = <?php echo json_encode($month);?>,
                $week = <?php echo json_encode($week);?>,
                $year = <?php echo json_encode($year);?>,
                $profit = <?php echo json_encode($profit);?>;
            $('#typeRequest').change(function () {
                var linkRequest = '<?php echo $this->Html->url('/') ?>activity_forecasts/response/';
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
            $('.header-height-fixed').css('height', $('.header-height').height() + 'px');

        //});
        $("tbody#absence-table tr td").click(function(){
                if($(this).hasClass("week-waiting")&&$(this).children().children().hasClass('td-waiting')){
                  var classStr = $(this).attr('class'),
                    lastClass = classStr.split(" "),
                    classDelete = lastClass[lastClass.length-2];
                    $("tbody#absence-table tr td."+classDelete).removeClass('week-waiting');
                }
                if($(this).hasClass("week-color")){
                    //remove class chon tuan
                    var classStr = $(this).attr('class'),
                    lastClass = classStr.substr( classStr.lastIndexOf(' ')+1);
                    $(this).removeClass('week-color');
                    var classStrE = $(this).attr('class'),
                    lastClassE = classStrE.substr( classStrE.lastIndexOf(' ')+1);
                    $("tbody#absence-table tr td."+lastClassE).removeClass('week-color');
                    $("tbody#absence-table tr td."+lastClassE).addClass('week-waiting');
                    var id = lastClassE.split("-");
                    ids = id[1];
                    if(!$('.selected-'+ids).hasClass('week-color')){
                        $("#id"+ids).prop('checked', false);
                    }
                }else{
                    //add class chon tuan
                    if((!$(this).hasClass('none-request'))&&((!$(this).children().children().hasClass('ab-validated')))&&((!$(this).children().children().hasClass('ab-holiday')))&&(!$(this).children().children().hasClass('td-validate'))&&(!$(this).children().children().hasClass('td-rejected'))){
                        var classStr = $(this).attr('class'),
                        lastClass = classStr.substr( classStr.lastIndexOf(' ') + 1);
                        $("tbody#absence-table tr td."+lastClass).addClass('week-color');
                        var id = lastClass.split("-");
                            ids = id[1];
                        $("#id"+ids).prop('checked', true);
                    }
                }
                // xoa tuan duoc chon
                    var selectedMonth = ''; // reset lai danh sach tuan duoc chon
                    $('#absence-table tr td').each(function()
                    {
                      if($(this).hasClass("week-color") && $(this).hasClass("selectedElementWeek")){
                        var classStr = $(this).attr('class');
                        var classFirst = classStr.split(" ");
                            classFirsts = classFirst[0];
                        var idTime = classFirsts.split("-");
                        selectedMonth+=idTime[1]+'-'+idTime[2]+',';
                      }
                    });
                    selectedMonth = selectedMonth.substr(0,selectedMonth.length-1);
                    //alert(selectedMonth);
                    $('#time-selected').val(selectedMonth);
        });
        // re chuot kiem tra trang thai rejected, validated cua tuan
        $("tbody#absence-table tr td.selectedElementWeek").each(function(){
            var classStr = $(this).attr('class'),
            lastClass = classStr.substr( classStr.lastIndexOf(' ') + 1);
            if($("tbody#absence-table tr td."+lastClass).children().children().hasClass('td-rejected')){
                $("tbody#absence-table tr td."+lastClass).addClass('week-rejected');
                $('#absence-table tr td.selectedElementWeek.'+lastClass).append("<span class='popup-validated'><?php __('Rejected') ?><span>");
            } else if($("tbody#absence-table tr td."+lastClass).children().children().hasClass('td-validate')){
                $("tbody#absence-table tr td."+lastClass).addClass('week-validated');
                $('#absence-table tr td.selectedElementWeek.'+lastClass).append("<span class='popup-validated'><?php __('Validated') ?><span>");
            } else if($("tbody#absence-table tr td."+lastClass).children().children().hasClass('td-waiting')){
                $("tbody#absence-table tr td."+lastClass).addClass('week-waiting');
                $('#absence-table tr td.selectedElementWeek.'+lastClass).append("<span class='popup-validated'><?php __('Waiting') ?><span>");
            }
        });
        //$("tbody#absence-table tr td.selectedElementWeek.week-absenced").each(function(){
//            $(this).append("<span class='popup-validated'>VALIDATED<span>");
//        });
        //xoa background khi di chuot ra
        /*
        $("tbody#absence-table tr td").mouseout(function(){
            var classStr = $(this).attr('class'),
            lastClass = classStr.substr( classStr.lastIndexOf(' ') + 1);
            if($(this).children().children().hasClass('td-rejected')){
                $("tbody#absence-table tr td."+lastClass).removeClass('week-rejected');
                $('.popup-rejected').css('opacity',0);
            }
            if($(this).children().children().hasClass('td-validate')){
                $("tbody#absence-table tr td."+lastClass).removeClass('week-validated');
                $('.popup-validated').css('opacity',0);
            }
        });
        */
    })(jQuery);
    $(window).resize(function(e) {
        configSizeScroll();
    });
    var temp1 = temp2 = -1;
    if($.browser.mozilla == true)
    {
        temp1 = 1;
        temp2 = 0;
    }
    else if($.browser.msie == true)
    {
        temp1 = 0;
        temp2 = -1;
    }

    function configSizeScroll(hd){
        fixedHeightRows();
        //hd:hright default
        $("#scrollTopAbsenceContent").width($("#absence").width());
        $("#scrollTopAbsence").width($("#absence-scroll").width());
        var height = $("#absence-table-container").height();
        // if( BrowserDetect.browser == 'Explorer' ){
        //     var ieFix = 0;
        //     $('tbody#absence-table tr').slice(-4).each(function(){
        //         ieFix += $(this).height();
        //     });
        //     height += ieFix;
        // }
        $("#scrollLeftAbsenceContent").height(height);
        var hHead=$('.header-height').height();//+12;
        $("#scrollLeftAbsence").css({'marginTop':(hHead)+'px'});
        $(".tdColID").width($("#thColID").width()+7);
        $(".tdColEmployee").width($("#thColEmployee").width()+7);
        $(".tdColStatus").width($("#thColStatus").width()+7);
        $(".tdColCapacity").width($("#thColCapacity").width()+7);
        if(hd!=600)
        {
            hd=hd-hHead-25;
        }
        $('.tbl-tbody').height(hd);
        $("#scrollLeftAbsence").height(hd);
    }
    function fixedHeightRows()
    {
        <?php foreach ($employees as $id => $employee) : ?>
        var i = $('tbody#absence-table tr td.<?php echo 'height_'.$id;?>').height()+temp1;
        $('tbody#absence-table-fixed tr td.<?php echo 'height_'.$id;?>').height(i);
        i = $('tbody#absence-table-fixed tr td.<?php echo 'height_'.$id;?>').height()+temp2;
        $('tbody#absence-table tr td.<?php echo 'height_'.$id;?>').height(i);
        <?php endforeach;?>
    }
	$(document).ready(fix_table_height);
	$(window).resize(fix_table_height);
	var temp = 0;
	function fix_table_height(){
		temp=setInterval(function(){
			var height_table = 600;
			if( $('#request-form').length){
				height_table = $(window).height() - $('#request-form').offset().top - 10;
			}
			configSizeScroll(height_table);
			clearInterval(temp);
		},1000);
	 }
    var allowScrollWindow = true;
    var abc = 0;

    var setupMouseWheel = function(){
        // for mouse scrolling in Firefox
        var elem = window;
        if (elem.addEventListener) {    // all browsers except IE before version 9
            // Internet Explorer, Opera, Google Chrome and Safari
            elem.addEventListener ("mousewheel", onMouseWheelSpin, false);
            // Firefox
            elem.addEventListener ("DOMMouseScroll", onMouseWheelSpin, false);
        }else{
            if (elem.attachEvent) { // IE before version 9
                elem.attachEvent ("onmousewheel", onMouseWheelSpin);
            }
        }
    }
    //$(function () {
        $(document).on('onmousewheel wheel onmousewheel mousewheel DOMMouseScroll', function(event, delta) {
            if(event.originalEvent.wheelDelta)
            {
                delta = event.originalEvent.wheelDelta;
            }
            else
            {
                delta = event.originalEvent.deltaY * -1;
            }
            if(allowScrollWindow == false)
            {
                if(delta < 0)
                {
                    abc = abc == $("#absence-table").height() ? $("#absence-table").height() : abc + 120;
                }
                else
                {
                    abc = abc == 0 ? abc : abc - 120;
                }
                //$("#scrollLeftAbsence").scrollTop(abc);
                $('#scrollLeftAbsence').animate({scrollTop:abc},'fast');
                //$(".tbl-tbody").scrollTop(abc);
                //$("#absence-table-fixed").scrollTop(abc);
                return false;
            }
        });
        $("#scrollLeftAbsence").click(function(){
            allowScrollWindow = true;
        });
        $("#scrollTopAbsence").scroll(function () {

            $("#absence-scroll").scrollLeft($("#scrollTopAbsence").scrollLeft());
        });
        $("#absence-scroll").scroll(function () {
            $("#scrollTopAbsence").scrollLeft($("#absence-scroll").scrollLeft());
        });
        $("#scrollLeftAbsence").scroll(function () {
            $(".tbl-tbody").scrollTop($('#scrollLeftAbsence').scrollTop());
            if(allowScrollWindow == true)
            abc = $('#scrollLeftAbsence').scrollTop();
            $("#absence-table-fixed").scrollTop($('#scrollLeftAbsence').scrollTop());
        });
    //});
    $('#absence-table-fixed').mouseover(function(e) {
        allowScrollWindow = false;
       // $('html').css({"overflow":"hidden"});
    });
    $('#absence-table-fixed').mouseout(function(e) {
        allowScrollWindow = true;
       // $('html').css({"overflow":"hidden"});
    });
    $("#absence-scroll").mouseover(function(e) {
        allowScrollWindow = false;
       // $('html').css({"overflow":"hidden"});
    });
    $("#absence-scroll").mouseout(function(e) {
        allowScrollWindow = true;
        //$('html').css({"overflow":"auto"});
    });
    //EXPAND TREE
    $(document).keyup(function(e) {
        if (window.event)
        {
            var value = window.event.keyCode;
        }
        else
            var value=e.which;
        if (value == 27) { collapseScreen(); }
    });
    function collapseScreen()
    {
        configSizeScroll(600);
        $('#table-control').show();
        $('.wd-title123').show();
        $('#collapse').hide();
        $('#project_container').removeClass('fullScreen');
        $(window).resize();
        $("#scrollTopAbsence").width($("#absence-scroll").width());
    }
    function expandScreen()
    {
        var wh=$(window).height();
        //$('#absence-wrapper').height(hehe);
        configSizeScroll(wh);
        $('#table-control').hide();
        $('.wd-title123').hide();
        $('#project_container').addClass('fullScreen');
        $('#collapse').show();
        $(window).resize();
        $("#scrollTopAbsence").width($("#absence-scroll").width());
    }
</script>

<div id="collapse" onclick="collapseScreen();" ><i class="icon-size-actual"></i></div>
