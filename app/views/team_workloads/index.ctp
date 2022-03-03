<?php echo $html->css('context/jquery.contextmenu'); ?>
<?php echo $html->script('context/jquery.contextmenu'); ?>
<?php echo $html->script('qtip/jquery.qtip'); ?>
<?php echo $html->script('jquery.form'); ?>
<?php echo $html->script('jquery.mousewheel.3'); ?>
<?php echo $html->css('/js/qtip/jquery.qtip'); ?>
<?php
$columnWidth = array(
    'phase' => 120,
    'task' => 200,
    'number' => 80
);
$periodText = __('Workload of the period', true);
$periodLength = strlen($periodText);
?>
<!-- export excel  -->
<fieldset style="display: none;">
    <?php
    echo $this->Form->create(false, array(
        'type' => 'POST',
        'url' => array('action' => 'export')));
    echo $this->Form->input('header', array('type' => 'hidden', 'id' => 'json-header'));
    echo $this->Form->input('data', array('type' => 'hidden', 'id' => 'json-data'));
    echo $this->Form->end();
    ?>
</fieldset>
<style type="text/css">
#absence-fixed th span{font-weight: normal;font-style: italic;}
#absence-container {
    background: #fff;
    position: relative;
    height: 100%;
}
#absence-scroll {
    position: absolute;
    top: 25px;
    left: 714px;
    bottom: 0;
    right: 20px;
    /*float: left;*/
    /*width: auto;*/
}
#scrollLeftAbsence {
    position: absolute;
    top: 0;
    right: 0;
    /*float: left;*/
}
#right-header{
    width: auto;
}
#left-scroll,
#right-scroll {
    max-height: 650px;
    overflow-y: hidden;
    overflow-x: scroll;
}
/*#left-scroll {
    overflow-x: hidden;
    max-height: 480px;
}*/
#left-container {
    width: auto;
    float: left;
}
/*#right-scroll {
    max-height: 500px;
    overflow-y: hidden;
    overflow-x: auto;
}*/
#absence-scroll #absence-fixed{
    width: auto !important;
}
#absence-fixed tbody tr td {
    vertical-align: middle;
    padding: 5px;
    min-width: 70px;
	line-height: 22px;
}
#absence-table tr td {
    vertical-align: middle;
    padding: 5px !important;
    height: auto !important;
}
.row-tittle{
    font-size: 15px;
}
.colV, .colW, .colR, .val{ width: 68px; overflow: hidden; min-width: 68px; max-width: 68px;}
.colThV, .colThW, .colThR { width: 60px; overflow: hidden; min-width: 60px; max-width: 60px; }

#absence tbody td span,
#absence-fixed tbody td span,
.normal-row span {
    display: block;
    padding: 0 !important;
}
span.consume {
    /*margin-left: 5px;*/
    font-weight: bold;
}
span.consume-month{
    color: #FF6600 !important;
}
/*.task-row span.workload {
    min-width: 30px;
}*/
.align-right {
    text-align: right;
}
.hidden {
    display: none;
}
.qtip-content {
    font-size: 12px;
}
.qtip-title {
    font-size: 13px;
    padding-top: 3px;
    padding-bottom: 3px;
}
.tooltip-workload {
    overflow: hidden;
    padding: 5px 0;
    border-bottom: 1px solid #ADD9ED;
}
.tooltip-workload:last-child {
    margin-bottom: 5px;
}
.tooltip-consume,
.tooltip-name {
    display: inline-block;
    float: left;
    width: auto;
    padding: 5px 5px 5px 0;
}
.tooltip-consume {
    color: #333;
}
.tooltip-input {
    width: 60px;
    float: left;
    padding: 3px 5px;
    border: 1px solid #ADD9ED;
    background: #fff;
    margin-right: 5px;
}
.tooltip-form button {
    border-radius: 3px;
    cursor: pointer;
    min-width: 30px;
    padding: 3px 5px;
    border: 1px solid #ADD9ED;
    background: #efe08b;
    margin-right: 5px;
    background: #addef4;
    background: -moz-linear-gradient(top,  #addef4 0%, #52bff2 100%);
    background: -webkit-linear-gradient(top,  #addef4 0%,#52bff2 100%);
    background: linear-gradient(to bottom,  #addef4 0%,#52bff2 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#addef4', endColorstr='#52bff2',GradientType=0 );
}
.tooltip-form button.tooltip-cancel {
    float: right;
    margin-right: 0;
}
/*.task-row td.highlight {
    background: #f0f0f0;
}*/
.task-row td.can-edit {
    background: #B8E8F9 !important;
    cursor: pointer;
}
.highlight span {
    background: transparent !important;
}

tr.sub-header td {
    background-color: #f0f0f0;
}
tr.sub-header td.no-highlight {
    background: transparent !important;
}
span.workload.updated {
    color: #3BBD43;
}
.input-hidden{
    display: none;
}
.input-edit{
    border: none;
    padding: 5px;
    margin: -5px;
    text-align: right;
    width: 70px;
}
/* scroll section */
tr.normal-row,
tr.normal-row {
    border: 1px solid #ccc;
}
.normal-row td {
    vertical-align: middle;
    padding: 5px;
    min-width: 70px;
    border-right: 1px solid #ccc;
	line-height: 22px;
}
td.align-right {
    line-height: 22px;
}

.fixed-header {
    border-bottom: 5px solid #185790;
}
.fixed-header tr {
    border: 1px solid #ccc;
	height: 43px !important;
}
.fixed-header tr:last-child {
    border-bottom-color: #185790;
}
.fixed-header th {
    background: url(/img/front/bg-head-table.png) repeat-x #06427A;
    /* border-right: 1px solid #185790; */
    border: 1px solid #185790;
    text-align: center;
    color: #fff;
    vertical-align: middle;
}
/*.total-workload-td,
.normal-row td.total-workload-td {
    min-width: 50px;
    text-align: center;
}*/
.total-workload-td,
.total-workload,
.total-freeze {
    text-align: center;
    width: 70px;
}
#horizontal-header {
    overflow: hidden;
}

#scrollTopAbsence {
    width: auto;
    float: left !important;
}
.header-month{
    cursor: pointer;
}
tr.project-row {
    background-color: #DAF1FA;
    font-weight: 700;
}
.wd-main-content td a {
    font-weight: normal;
    text-decoration: none;
    position: relative;
    display: inline-block;
}
.wd-main-content td a:before {
    content: '';
    border-bottom: 1px dotted #013d74;
    display: block;
    position: absolute;
    bottom: -3px;
    width: 100%;
}
/***/

.loading_w{
    width: 100%;
    background-color: #000;
    height: 100%;
    position: absolute;
    top: 0px;
    opacity: 0.5;
    display: none;
    opacity: 0.3;
    z-index: 1;
}
.loading_w p{
    background-image: url("/img/business/wait-1.gif");
    background-attachment: scroll;
    background-clip: border-box;
    background-origin: padding-box;
    background-repeat: no-repeat;
    background-size: auto auto;
    display: block;
    width: 128px;
    height: 128px;
    margin: 10% auto;
}
.header-month b {
    position: relative;
}
.header-month b:before {
    content: '';
    display: block;
    background: url(/img/slick_grid/bullet_star.png) no-repeat;
    width: 14px;
    height: 14px;
    position: absolute;
    top: -1px;
    left: -16px;
}
#header-freeze{
    max-width: 140px;
}
#header-total{
    max-width: 140px;
}
#header-workload{
    max-width: 140px;
	min-width: 100px;
}
#left-header{
    max-width: 750px;
}
#absence{
    max-width: 750px;
}
#wd-container-main .wd-layout {
    padding-bottom: 0px;
}
tr.project-row {
    line-height: 22px;
}
</style>
<!-- /export excel  -->
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div id="message-place">
                    <?php
                    echo $this->Session->flash();
                    $header = $tdModel = $totalbyMonths = $capacityTd = '';
                    $count = 0;
                    $currentDate = time();
                    while($start <= $end){
                        $headerbtn = $time = date("m", $start) . '-' . date("Y", $start);
                        $date = __(date("M", $start), true) . '-' . date("y", $start);
                        $fullDate = strtotime('01-' . $time);
                        $fullDate = strtotime('last day of this month', $fullDate);
                        if( $fullDate < $currentDate){
                            $header .= '<th data-month="' . $time . '" class="header-month header-' . $time . '"><b>' . $date . '</b></th>';
                        } else {
                            $header .= '<th data-month="' . $time . '" class="header-' . $time . '"><b>' . $date . '</b></th>';
                        }
                        $capacityTd .= '<td class="align-right month-' . $time . '" data-time="' . $time . '">&nbsp;</td>';
                        $tdModel .= '<td class="align-right month-' . $time . '" data-time="' . $time . '"><input type="text" rel="no-history" class="input-edit input-hidden"><span style="" class="consume-month">&nbsp;</span><span class="workload">&nbsp;</span><span class="consume">&nbsp;</span></td>';
                        $totalbyMonths .= '<td class="align-right" id="total-month-' . date("m", $start) . '-' . date("Y", $start) . '"><span style="" class="consume-month">&nbsp;</span><span class="workload">&nbsp;</span><span class="consume">&nbsp;</span></td>';
                        $start = mktime(0, 0, 0, date("m", $start)+1, date("d", $start), date("Y", $start));
                        $count ++;
                    }
                    ?>
                </div>
                <a href="#" class="export-excel-icon-all" onclick="execute_export(); return false"></a>
                <div id="absence-container" style="overflow: hidden">
                    <div class="loading_w"><p></p></div>
                    <div id="scrollTopAbsence" class="useLeftScroll"><div id="scrollTopAbsenceContent"></div></div>
                    <br clear="all"  />
                    <div id="left-container">
                        <table id="left-header" class="fixed-header">
                            <thead>
                                <tr class="height-absence header-height-fixed">
                                    <th id="header-phase" width="<?php echo $columnWidth['phase'] ?>"><?php __('Phase'); ?></th>
                                    <th id="header-task" width="<?php echo $columnWidth['task'] ?>"><?php __('Task'); ?></th>
                                    <th id="header-freeze" width="<?php echo $columnWidth['number'] ?>"><?php __(' Freeze '); ?></th>
                                    <th id="header-total" width="<?php echo $columnWidth['number'] ?>"><?php __('Total Workload'); ?></th>
                                    <th id="header-workload" width="<?php echo $columnWidth['number'] ?>"><?php echo $periodText ?></th>
                                </tr>

                                <tr class="normal-row">
                                    <td colspan="5" id="row-capacity-tittle"><b><?php echo __('Capacity'); ?></b></td>
                                </tr>
                                <tr class="normal-row"><td colspan="5">&nbsp;</td></tr>
                                <tr class="normal-row">
                                    <td colspan="2" class="row-tittle" id="team-row"><b><?php echo __('Total of '); echo $pcName; ?></b></td>
                                    <td class='total-workload-td' id="freeze"><span style="" class="consume-month">&nbsp;</span><span class="workload"></span><span class="consume"></span></td>
                                    <td class='total-workload-td' id="total-workload-task"><span style="" class="consume-month">&nbsp;</span><span class="workload"></span><span class="consume"></span></td>
                                    <td class='total-workload-td' id='total_wl'><span style="" class="consume-month">&nbsp;</span><span class="workload"></span><span class="consume"></span></td>
                                </tr>
                            </thead>
                        </table>
                        <div id="left-scroll">
                            <table id="absence">
                                <tbody id="absence-table">
                                    <?php
                                    $keyRow = $keyPjRow = array();
                                    $htmlBody ='';
                                    $htmlHeader = '<tr id="capacity-row" class="normal-row">' . $capacityTd . '</tr><tr class="normal-row"><td class="separator-row" colspan="' . $count . '">&nbsp;</td></tr>' .'<tr id="month-row" class="normal-row">' . $totalbyMonths . '</tr>';
                                    if(!empty($datas) && !empty($priorityNamePJ)){
                                        foreach ($priorityNamePJ as $key => $vl) {
                                            if( !empty($datas[ $key ]) ){
                                                $data = $datas[ $key ];
                                            } else {
                                                continue;
                                            }
                                            $keys = explode("-", $key);
                                            $htmlBody .= '<tr class="project-row"><td colspan="' . $count . '">&nbsp;</td></tr>';
                                            $keyPjRow[$key] = $key;
                                            $rowCount = $taskCount[$key] + 1;
                                            if($keys[0] == "p"){
                                                echo "<tr class='separator-row project-row no-highlight row-name'><td class='project-name' colspan='5'>{$projectNames[$keys[1]]}</td></tr>";
                                                // <td class='no-highlight row-name' rowspan='$rowCount'><b>" . $projectNames[$keys[1]] . "</b></td>
                                                $_namePrio = !empty($priotityName[$idOfPriorityProject[$keys[1]]]) ? $priotityName[$idOfPriorityProject[$keys[1]]] : '';
                                                echo "<tr class='sub-header " . $key . "'><td colspan='2' class='priority-cell'><i>{$_namePrio}</i></td>";
                                            } else {
                                                echo "<tr class='separator-row project-row no-highlight row-name'><td class='project-name' colspan='5'><b>{$listActivities[$keys[1]]}</b></td></tr>";
                                                //<td class='no-highlight row-name' rowspan='$rowCount'><b>" . $listActivities[$keys[1]] . "</b></td>
                                                echo "<tr class='sub-header " . $key . "'><td colspan='2'></td>";
                                            }
                                            //them freeze và total workload vào day
                                            echo "
                                            <td class='total-freeze total-freeze-" . $key . "' width='{$columnWidth['number']}'><span style='' class='consume-month'>&nbsp;</span><span class='workload'>&nbsp;</span><span class='consume'>&nbsp;</span></td>
                                                <td id='total-" . $key . "' class='total-workload title-workload-task-" . $key . "' width='{$columnWidth['number']}'><span style='' class='consume-month'>&nbsp;</span><span class='workload'>&nbsp;</span><span class='consume'>&nbsp;</span></td>
                                                <td class='the-workload total-workload-td' id='" . $key . "' width='{$columnWidth['number']}'><span style='' class='consume-month'>&nbsp;</span><span class='workload'>&nbsp;</span><span class='consume'>&nbsp;</span></td></tr>";

                                            $htmlBody .= '<tr class="sub-header ' . $key . '">' . $tdModel . '</tr>';
                                            foreach($data as $idPhase => $phase){
                                                $countX = count($phase);
                                                if($keys[0] == "p"){
                                                    echo "<tr class='the-phase phase-$idPhase' data-parent='$key'><td rowspan='$countX' class='phase-cell' width='{$columnWidth['phase']}'><b>" . $projectPhases[$idPhase] . "</b></td>";
                                                } else {
                                                    echo "<tr><td></td>";
                                                }
                                                $i = 0;
                                                foreach($phase as $idTask => $name){
                                                    $keyRow[$idTask] = $keys[0] . '-' . $idTask;
                                                    if( $keys[0] == 'a' ){
                                                        $extra = '<td></td>';
                                                        $name = "<a href='/activity_tasks/index/{$keys[1]}/?id=$idTask' target='_blank'>$name</a>";
                                                    }
                                                    else {
                                                        $extra = '';
                                                        $name = "<a href='/project_tasks/index/{$keys[1]}/?id=$idTask' target='_blank'>$name</a>";
                                                    }
                                                    if($i == 0) {
                                                        echo "<td data-phase='$idPhase' class='the-task task-right-" . $keys[0] . '-' . $idTask . "' width='{$columnWidth['task']}'>" . $name . "</td>
                                                        <td class='total-freeze freeze-" . $idTask . "'><span style='' class='consume-month'>&nbsp;</span><span class='workload'>&nbsp;</span><span class='consume'>&nbsp;</span></td>
                                                        <td id='total-" . $key[0] . "-" . $idTask ."' class='total-workload total-workload-task-" . $idTask . "' ><span style='' class='consume-month'>&nbsp;</span><span class='workload'>&nbsp;</span><span class='consume'>&nbsp;</span></td>
                                                        <td class='total-workload-td' id='" . $key[0] . "-" . $idTask ."' ><span style='' class='consume-month'>&nbsp;</span><span class='workload'>&nbsp;</span><span class='consume'>&nbsp;</span></td></tr>";
                                                    } else {
                                                        echo "<tr>$extra<td class='the-task task-right-" . $keys[0] . '-' . $idTask . "'>". $name . "</td>
                                                        <td class='total-freeze freeze-" . $idTask . "'><span style='' class='consume-month'>&nbsp;</span><span class='workload'>&nbsp;</span><span class='consume'>&nbsp;</span></td>
                                                        <td id='total-" . $key[0] . "-" . $idTask ."' class='total-workload total-workload-task-" . $idTask . "' ><span style='' class='consume-month'>&nbsp;</span><span class='workload'>&nbsp;</span><span class='consume'>&nbsp;</span></td>
                                                        <td class='total-workload-td' id='" . $key[0] . "-" . $idTask ."' ><span style='' class='consume-month'>&nbsp;</span><span class='workload'>&nbsp;</span><span class='consume'>&nbsp;</span></td></tr>";
                                                    }
                                                    $htmlBody .= '<tr class="task-row task-' . $keys[0] . '-' . $idTask . '" data-id="' . $idTask . '" data-type="' . $keys[0] . '">' . $tdModel . '</tr>';
                                                    $i ++;
                                                }
                                            }
                                        }
                                        $htmlBody .= '<tr class="separator-row"><td colspan="' . $count . '">&nbsp;</td></tr>';
                                        echo "<tr class='separator-row'><td colspan='6'>&nbsp;</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="absence-scroll">
                        <div id="horizontal-header">
                            <table id="right-header" class="fixed-header">
                                <thead>
                                    <tr id="month" class="header-height height-absence-1">
                                        <?php echo $header;?>
                                    </tr>
                                    <?php echo $htmlHeader ?>
                                </thead>
                            </table>
                        </div>
                        <div id="right-scroll">
                            <table id="absence-fixed">
                                <tbody>
                                    <?php echo $htmlBody;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="scrollLeftAbsence">
                        <div id="scrollLeftAbsenceContent"></div>
                    </div>
                    <div id="tooltip-template" class="hidden">
                        <form class="tooltip-form" method="post" action="<?php echo $this->Html->url('/team_workloads/saveWorkload') ?>">
                            <div class="tooltip-assign">
                                <div class="tooltip-workload">
                                    <input class="tooltip-input" type="text">
                                    <b class="tooltip-consume"></b>
                                    <b class="tooltip-name"></b>
                                </div>
                            </div>
                            <input type="hidden" name="data[id]" class="tooltip-id">
                            <input type="hidden" name="data[month]" class="tooltip-month">
                            <input type="hidden" name="data[type]"  class="tooltip-type">
                            <button class="tooltip-cancel" type="button"><?php __('Cancel') ?></button>
                            <button class="tooltip-ok"><?php __('OK') ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<form id="form-update" action="/team_workloads/updateWorkload" method="post">' +
        <textarea style="display:none" type="hidden" name="workload"></textarea>
</form>
<script type="text/javascript">
var exportReady = false;
var workloadOfATask = <?php echo json_encode($workloadOfATask);?>;
var workloadOfPTask = <?php echo json_encode($workloadOfPTask);?>;
var projectOfPTasks = <?php echo json_encode($projectOfPTasks);?>;
var activityOfATasks = <?php echo json_encode($activityOfATasks);?>;
var listIdsPr = <?php echo json_encode($listIdsPr);?>;
var consumeOfTasks = <?php echo json_encode($consumeOfTasks);?>;
var totalConsumedOfMonth = <?php echo json_encode($totalConsumedOfMonth);?>;
var keyRow = <?php echo json_encode($keyRow); ?>;
var keyPjRow = <?php echo json_encode($keyPjRow); ?>;
// workload cho PJ
var totalWorkload = <?php echo json_encode($totalWorkload); ?>;
//workload for ac
var workloadActivity = <?php echo json_encode($workloadActivity); ?>;
// gia tri cho freeze
var activityFreeze = <?php echo json_encode($activityFreeze) ?>;
var projectFreeze = <?php echo json_encode($projectFreeze) ?>;
var assigns = <?php echo json_encode($assigns) ?>,
    listAssign = <?php echo json_encode($listAssign) ?>,
    resources = <?php echo json_encode($resources) ?>,
    resourceConsume = <?php echo json_encode($resourceConsume) ?>,
    taskMap = <?php echo json_encode($taskMap) ?>,
    pcName = <?php echo json_encode($pcName) ?>,
    pc = <?php echo $params['team'] ?>,
    params = <?php echo json_encode($params) ?>,
    confirmCode = <?php echo json_encode(__('Update the workload with the consumed?', true)) ?>,
    mStart = <?php echo json_encode($mStart) ?>,
    yStart= <?php echo json_encode($yStart) ?>,
    mEnd = <?php echo json_encode($mEnd) ?>,
    yEnd = <?php echo json_encode($yEnd) ?>,
    allDataCs = <?php echo json_encode($allDataCs) ?>,
    totalCsOfTasks = <?php echo json_encode($totalCsOfTasks) ?>;
var staffingxx = <?php echo json_encode($staffing) ?>;

$('.header-month').click(function(){
    var r = confirm(confirmCode);
    if (r == true){
        $('.loading_w').show();
        var month = $(this).data('month');
        var list = $("td[data-time='" + month + "']");
        var data = {consume:{},month:month, pc:pc, mStart:mStart, yStart:yStart, mEnd:mEnd, yEnd:yEnd},
            listIds = {};
        $.each(list, function(key, td){
            var td = $(td);
            tr = td.parent();
            if( tr.hasClass('task-row') ){
                tid = tr.data('id');
                type = tr.data('type');
                cs = td.find('span.consume').text();
                curent_wl = td.find('span.workload').text();
                id = type + '-' + tid;
                data.consume[id] = {};
                var activityTaskId = type == 'a' ? tid : taskMap[tid];
                var _cs = cs.replace('(', '').replace(')', ''),
                    _cs = (_cs != '') ? _cs : 0,
                    _wl = (curent_wl != '') ? curent_wl : 0;
                if(_cs != 0 && _wl == 0){
                    // lay khi co consume ma khong co workload.
                    // data.consume[id]['0'] = _cs;
                    data.consume[id]['none'] = activityTaskId;
                } else {
                    if( resourceConsume[activityTaskId] ){
                        $.each(resourceConsume[activityTaskId], function(resourceId, m){
                            //check if resourceId has assign
                            if( assigns[id][month] && typeof assigns[id][month][resourceId] != 'undefined' ){
                                data.consume[id][resourceId] = m[month];
                            }
                        });
                        //get 0 workload
                        if( assigns[id][month] ){
                            $.each(assigns[id][month], function(resourceId, x){
                                if( data.consume[id][resourceId] )return;
                                data.consume[id][resourceId] = 0;
                            });
                        }
                    } else {
                        //get khi not consume
                        if( assigns[id][month] ){
                            $.each(assigns[id][month], function(resourceId, x){
                                data.consume[id][resourceId] = 0;
                            });
                        }
                    }
                }
                // lay id cua PJ hoac AC
                if( type == 'a' ){
                    parent_id = activityOfATasks[tid];
                } else {
                    parent_id = projectOfPTasks[tid];
                }
                parent_id = type + '-' + parent_id;
                if( !listIds[parent_id] ){
                    listIds[parent_id] = parent_id;
                }
            }
        });
        $("#form-update").find('textarea').val(JSON.stringify(data));
        $("#form-update").submit();
    } else {
        $('.loading_w').hide();
        return;
    }
});
// chay staffings cho nhieu PJ hoac AC
function staffings(list){
    $.ajax({
        type: "POST",
        url: '/team_workloads/staffings',
        data: {list: list}
    });
}
//todo: run staffing
function staffing(id, type){
    //call ajax, type = a | p
    var controller = type == 'p' ? 'project_tasks' : 'activity_tasks';
    $.ajax({
        url: '/' + controller + '/staffingWhenUpdateTask/' + id,
        method: 'GET'
    });
}
//todo: run staffings

if( staffingxx.length || !$.isEmptyObject(staffingxx) ){
    staffings(staffingxx);
}
//calculate section
function calculateAll(){
    var sumWorkload = 0;
    // total workload for all task.
    var sumW = 0;
    var sumfreeze = 0;
    var sumWorkloadByMonths = {};
    if(workloadOfPTask){
        var sumHeaders = {};
        var sumOfProject = {};
        // buil total workload theo PJ
        var worloadOfPJ = {};
        var freezeByTask = {};
        $.each(workloadOfPTask, function(index, values){
            var project_id = projectOfPTasks[index] ? projectOfPTasks[index] : 0;
            var sumOfPtask = 0;
            $.each(values, function(ind, val){
                $('.task-p-' + index).find('td.month-' + ind).find('span.workload').html(val.toFixed(2));
                sumOfPtask += val;
                var _key = project_id + '/' + ind;
                var keyOfMonths = ind;
                if(!sumHeaders[_key]){
                    sumHeaders[_key] = 0;
                }
                sumHeaders[_key] += val;
                //total workload by months
                if(!sumWorkloadByMonths[keyOfMonths]){
                    sumWorkloadByMonths[keyOfMonths] = 0;
                }
                sumWorkloadByMonths[keyOfMonths] += val;
            });
            // buil total workload the task
            totalWorkload[index] = ( typeof(totalWorkload[index]) == 'undefined' ) ? 0 : parseFloat(totalWorkload[index]);
            $('.total-workload-task-' + index).find('span.workload').html(totalWorkload[index].toFixed(2));
            $('#p-' + index).find('span.workload').html(sumOfPtask.toFixed(2));
            if(!sumOfProject[project_id]){
                sumOfProject[project_id] = 0;
            }
            sumOfProject[project_id] += sumOfPtask;
            // project total
            if(!worloadOfPJ[project_id]){
                worloadOfPJ[project_id] = 0;
            }
            worloadOfPJ[project_id] += totalWorkload[index];
            sumW += totalWorkload[index];
            sumWorkload += sumOfPtask;
            //freeze
            projectFreeze[index] = ( typeof(projectFreeze[index]) == 'undefined' ) ? 0 : parseFloat(projectFreeze[index]);
            $('.freeze-' + index).find('span.workload').html(projectFreeze[index].toFixed(2));
            if(!freezeByTask[project_id]){
                freezeByTask[project_id] = 0;
            }
            freezeByTask[project_id] += projectFreeze[index];
            sumfreeze += projectFreeze[index];
        });
        if(sumHeaders){
            $.each(sumHeaders, function(index, value){
                index = index.split('/');
                $('.p-' + index[0]).find('td.month-' + index[1]).find('span.workload').html(value.toFixed(2));
            });
        }
        if(sumOfProject){
            $.each(sumOfProject, function(_ind, _val){
                $('#p-' + _ind).find('span.workload').html(_val.toFixed(2));
            });
        }
        if(worloadOfPJ){
            $.each(worloadOfPJ, function(_ind, _val){
                _val = parseFloat(_val);
                $('.title-workload-task-p-' + _ind).find('span.workload').html(_val.toFixed(2));
            });
        }
        //freezeByTask
        if(freezeByTask){
            $.each(freezeByTask, function(_ind, _val){
                _val = parseFloat(_val);
                $('.total-freeze-p-' + _ind).find('span.workload').html(_val.toFixed(2));
            });
        }
    }
    if(workloadOfATask){
        var sumHeaders = {};
        var workloadHeaders = {};
        var sumOfActivity = {};
        var freezeByTaskA = {};
        $.each(workloadOfATask, function(index, values){
            var activity_id = activityOfATasks[index] ? activityOfATasks[index] : 0;
            var sumOfAtask = 0;
            $.each(values, function(ind, val){
                $('.task-a-' + index).find('td.month-' + ind).find('span.workload').html(val.toFixed(2));
                sumOfAtask += val;
                var _key = activity_id + '/' + ind;
                var keyOfMonths = ind;
                if(!sumHeaders[_key]){
                    sumHeaders[_key] = 0;
                }
                sumHeaders[_key] += val;
                //to tal workload by months
                if(!sumWorkloadByMonths[keyOfMonths]){
                    sumWorkloadByMonths[keyOfMonths] = 0;
                }
                sumWorkloadByMonths[keyOfMonths] += val;
            });
            //build total workload for activity task
            workloadActivity[index] = ( typeof(workloadActivity[index]) == 'undefined' ) ? 0 : parseFloat(workloadActivity[index]);
            $('.total-workload-task-' + index).find('span.workload').html(workloadActivity[index].toFixed(2));
            //freeze
            activityFreeze[index] = ( typeof(activityFreeze[index]) == 'undefined' ) ? 0 : parseFloat(activityFreeze[index]);
            $('.freeze-' + index).find('span.workload').html(activityFreeze[index].toFixed(2));
            if(!freezeByTaskA[activity_id]){
                freezeByTaskA[activity_id] = 0;
            }
            freezeByTaskA[activity_id] += activityFreeze[index];
            sumfreeze += activityFreeze[index];
            //
            if(!workloadHeaders[activity_id]){
                workloadHeaders[activity_id] = 0;
            }
            workloadHeaders[activity_id] += workloadActivity[index];
            sumW += workloadActivity[index];
            //
            $('#a-' + index).find('span.workload').html(sumOfAtask.toFixed(2));
            if(!sumOfActivity[activity_id]){
                sumOfActivity[activity_id] = 0;
            }
            sumOfActivity[activity_id] += sumOfAtask;
            sumWorkload += sumOfAtask;
        });
        if(sumHeaders){
            $.each(sumHeaders, function(index, value){
                index = index.split('/');
                $('.a-' + index[0]).find('td.month-' + index[1]).find('span.workload').html(value.toFixed(2));
            });
        }
        if(sumOfActivity){
            $.each(sumOfActivity, function(_ind, _val){
                $('#a-' + _ind).find('span.workload').html(_val.toFixed(2));
            });
        }
        if(workloadHeaders){
            $.each(workloadHeaders, function(_ind, _val){
                _val = parseFloat(_val);
                $('.title-workload-task-a-' + _ind).find('span.workload').html(_val.toFixed(2));
            });
        }
        //freeze
        if(freezeByTaskA){
            $.each(freezeByTaskA, function(_ind, _val){
                _val = parseFloat(_val);
                $('.total-freeze-a-' + _ind).find('span.workload').html(_val.toFixed(2));
            });
        }
    }
    if(sumWorkloadByMonths){
        $.each(sumWorkloadByMonths, function(_ind, _val){
            $('#total-month-' + _ind).find('span.workload').html(_val.toFixed(2));
        });
    }
    $('#total_wl').find('span.workload').html(sumWorkload.toFixed(2));
    $('#total-workload-task').find('span.workload').html(sumW.toFixed(2));
    $('#freeze').find('span.workload').html(sumfreeze.toFixed(2));

    if(consumeOfTasks){
        var sumOfConsume = 0;
        var sumOfConsumeByTasks = {};
        var sumOfConsumeByMonths = {};
        var consumeByProject = {};
        var consumeByTask ={};

        $.each(consumeOfTasks, function(index, value){
            $.each(value, function(_ind, _val){
                _val = parseFloat(_val);
                $('.task-p-' + listIdsPr[index]).find('td.month-' + _ind).find('span.consume').html('(' + _val.toFixed(2) + ')');
                $('.task-a-' + index).find('td.month-' + _ind).find('span.consume').html('(' + _val.toFixed(2) + ')');

                var key = index;
                if(!sumOfConsumeByTasks[key]){
                    sumOfConsumeByTasks[key] = 0;
                }
                sumOfConsumeByTasks[key] += _val;
                sumOfConsume += _val;
                //consume by month of task.
                if(!sumOfConsumeByMonths[_ind]){
                    sumOfConsumeByMonths[_ind] = 0;
                }
                sumOfConsumeByMonths[_ind] += _val;

                //consume by activity/month
                var parentKey = projectOfPTasks[listIdsPr[index]] ? 'p-' + projectOfPTasks[listIdsPr[index]] : 'a-' + activityOfATasks[index];
                if( !consumeByProject[parentKey] ){
                    consumeByProject[parentKey] = {};
                }
                if( !consumeByProject[parentKey][_ind] ){
                    consumeByProject[parentKey][_ind] = 0;
                }
                consumeByProject[parentKey][_ind] += _val;
                //tinh consum tung task
                if(!consumeByTask[parentKey]){
                    consumeByTask[parentKey] = 0;
                }
                consumeByTask[parentKey] += _val;
            });
        });
        $('#total_wl').find('span.consume').html('(' + sumOfConsume.toFixed(2) + ')');
        //consume by activity/month
        $.each(consumeByProject, function(id, data){
            $.each(data, function(month, value){
                $('.' + id).find('.month-' + month).find('span.consume').html('(' + value.toFixed(2) + ')');
            });
        });
        // consume back task.
        if(sumOfConsumeByTasks){
            $.each(sumOfConsumeByTasks, function(index, value){
                $('#p-' + listIdsPr[index]).find('span.consume').html('(' + value.toFixed(2) + ')');
                $('#a-' + index).find('span.consume').html('(' + value.toFixed(2) + ')');
            });
        }
        if(sumOfConsumeByMonths){
            $.each(sumOfConsumeByMonths, function(index, value){
                $('#total-month-' + index).find('span.consume').html('(' + value.toFixed(2) + ')');
            });
        }
        // consume black pj
        if(consumeByTask){
            $.each(consumeByTask, function(id, value){
                $('#' + id).find('span.consume').html('(' + value.toFixed(2) + ')');
            });
        }
    }
    if( totalConsumedOfMonth ){
        var totalCsOfMonth = {};
        $.each(totalConsumedOfMonth, function(_index, _project){
            $.each(_project, function(index, value){
                // gan gia tri cho total screen.
                if(!totalCsOfMonth['total']){
                    totalCsOfMonth['total'] = 0;
                }
                if(index == 'total'){
                    $('#' + _index).find('span.consume-month').html('(' + value.toFixed(2) + ')').show();
                    // show cho no can hang.
                    $('#total-' + _index).find('span.consume-month').show();
                    $('.total-freeze-' + _index).find('span.consume-month').show();
                    totalCsOfMonth['total'] += value;
                } else {
                    _value = $('.' + _index).find('.month-' + index).find('span.consume').text().replace(')', '').replace('(', '');
                    $('.' + _index).find('.month-' + index).find('span.consume-month').html('(' + value.toFixed(2) + ')').show();
                    //lay consume mau cam. roi hien thi.
                    if(!totalCsOfMonth[index]){
                        totalCsOfMonth[index] = 0;
                    }
                    totalCsOfMonth[index] += value;
                }
            });
        });
        if(totalCsOfMonth){
            $.each(totalCsOfMonth, function(index, value){
                if(index == 'total'){
                    $('#total_wl').find('span.consume-month').html('(' + value.toFixed(2) + ')').show();
                    // hien thi header cho no cung hang.
                    $('#total-workload-task').find('span.consume-month').show();
                }else{
                    $('#total-month-' + index).find('span.consume-month').html('(' + value.toFixed(2) + ')').show();
                    $('#month-row').find('.align-right').find('span.consume-month').show();
                }
            });
        }
        // hien thi du lieu cho consume cua total theo PJ.
        var sumAllCs = 0;
        if(allDataCs){
            $.each(allDataCs, function(PjId, cs){
                $('#total-p-' + PjId).find('span.consume').html('(' + cs.toFixed(2) + ')').show();
                sumAllCs += cs;
            });
            $('#total-workload-task').find('span.consume').html('(' + sumAllCs.toFixed(2) + ')').show();
        }
        // hien thi consume cua total NCT task
        if(totalCsOfTasks){
            $.each(totalCsOfTasks, function(task_id, cs){
                $('#total-' + task_id).find('span.consume').html('(' + cs.toFixed(2) + ')').show();
            });
        }
    }
    scaleTable();
}
//end calculate
//first call
calculateAll();


$(function() {
    //capacity by Quyet
    function substr_count(str){
        return (str.match(/\s\-\-\s/g) || []).length;
    }
    function getCapacity(){
        exportReady = false;
        //get all pcs and their children
        pcs = [pc];
        var level = substr_count($('#aTeam option[value=' + pc + ']').text());
        $('#aTeam option[value=' + pc + '] ~ option').each(function(){
            var thisLevel = substr_count($(this).text());
            if( thisLevel > level ){
                pcs.push($(this).val());
            } else {
                return false;
            }
        });
        startDate = '01-' + params['smonth'] + '-' + params['syear'];
        date = new Date(params['eyear'], params['emonth'], 0);
        endDate = $.datepicker.formatDate('dd-mm-yy', date);
        $.ajax({
            url: '/new_staffing/?start_date=' + startDate + '&end_date=' + endDate + '&view_by=month&type=1&summary=1&pc=' + pcs.join(','),
            dataType: 'json',
            success: function(data){
                $.each(data.summary.capacity, function(time, capacity){
                    var month = $.datepicker.formatDate('mm-yy', new Date(time * 1000));
                    $('#capacity-row .month-' + month).text(capacity);
                });
                scaleTable();
                exportReady = true;
            }
        });
    }
    getCapacity();

    //dialog
    $('.task-row td').click(function(ev){
        //neu ko co workload ma van co the chinh sua thi de dong nay, comment cac dong duoi
        // showDialog($(this), ev);
        // return;
        //nguoc lai, phai co workload
        var data = $.trim($(this).find('span.workload').text());
        if( data ){
            showDialog($(this), ev);
        }
    }).hover(function(){
        // highlight($(this));
        // return;
        var data = $.trim($(this).find('span.workload').text());
        if( data ){
            highlight($(this));
        }
    }, function(){
        highlight($(this), true);
    });

    function highlight(td, unhighlight){
        var time = td.data('time'),
            cls = '.month-' + time + ', #total-month-' + time,
            tbody = td.closest('tbody');
        if( !unhighlight ){
            // tbody.find(cls).addClass('highlight');
            // td.closest('tr').find('td').addClass('highlight');
            td.addClass('can-edit');
        } else {
            // tbody.find(cls).removeClass('highlight');
            // td.closest('tr').find('td').removeClass('highlight');
            td.removeClass('can-edit');
        }
    }

    function scaleY(amount){
        //down -> negative
        $('#scrollLeftAbsence')[0].scrollTop -= amount;
    }
    $('#scrollLeftAbsence').on('scroll', function(e){
        var amount = $('#scrollLeftAbsence').scrollTop();
        //update left container
        $('#left-scroll').scrollTop(amount);
        //update right container
        $('#right-scroll').scrollTop(amount);
    });

    var _left_scroll = 0;
    $('#right-scroll').on('scroll', function(e){
        //only update left scroll
        var curLeft = $(this).scrollLeft();
        if( _left_scroll != curLeft ){
            $('#horizontal-header').scrollLeft(curLeft);
            $('#scrollTopAbsence').scrollLeft(curLeft);
            _left_scroll = curLeft;
        }
    });
    $('#scrollTopAbsence').on('scroll', function(event) {
        var curLeft = $(this).scrollLeft();
        $('#right-scroll').scrollLeft(curLeft);
    });
    $('#absence-container').mousewheel(function(event) {
        var amount = event.deltaY * event.deltaFactor;
        scaleY(amount);
    });
    $(document).on('mousewheel', '#absence-container', function(){
        //do check here
        return false;
    });
});

Array.prototype.push_repeat = function(str, number){
    for(var i = 0; i < number; i++){
        this.push(str);
    }
    return this;
}

Array.prototype.merge_2d = function(array2){
    if( this.length != array2.length )return false;
    var result = [];
    for(var i = 0; i < this.length; i++){
        result[i] = this[i].concat(array2[i]);
    }
    return result;
}

function formatWC(wl, cs, tcs){
    return tcs + ',' + wl + ',' + cs + ',' + tcs;
}

function execute_export(){
    if( !exportReady ){
        alert('<?php __('Please wait') ?>');
        return;
    }
    var data = [], header = [];
    //get header
    $('#left-header tr').each(function(i){
        var tr = $(this), rightHeaderRow = $('#right-header tr').eq(i);
        header[i] = [];
        //add an empty column
        header[i].push(' ');
        //left header
        tr.find('td, th').each(function(){
            var td = $(this), colspan = parseInt(td.prop('colspan'));
            if( isNaN(colspan) || !colspan )colspan = 1;
            colspan--;
            //push data
            text = $.trim(td.text());
            if( td.find('span').length ){
                text = '[number]' + formatWC(td.find('span.workload').text(), td.find('span.consume').text(), td.find('span.consume-month').text());
            } else if( text ){
                text = '[label]' + text;
            }
            header[i].push(text);
            //check colspan
            header[i].push_repeat('', colspan);
        });

        //right header
        rightHeaderRow.find('td, th').each(function(){
            var td = $(this), colspan = parseInt(td.prop('colspan'));
            if( isNaN(colspan) || !colspan )colspan = 1;
            colspan--;
            //push data
            text = $.trim(td.text());
            if( td.find('span').length ){
                if( rightHeaderRow.prop('id') == 'capacity-row' ){
                    text = '[number]' + td.find('span.workload').text();
                } else {
                    text = '[number]' + formatWC(td.find('span.workload').text(), td.find('span.consume').text(), td.find('span.consume-month').text());
                }
            }
            header[i].push(text);
            //check colspan
            header[i].push_repeat('', colspan);
        });
    });

    var project = {}, row = 0,
        absence = $('#absence tr');
    //get data
    absence.each(function(i){
        var tr = $(this), right = $('#absence-fixed tr').eq(i);
        if( tr.hasClass('separator-row') ){
            //luu project
            if( !$.isEmptyObject(project) ){
                data.push(project);
            }
            //tao moi project
            project = {
                name: '',
                rows: []
            };
            row = 0;
            if (tr.hasClass('row-name')){
                if( tr.find('td.project-name') ){
                    var text = tr.find('td.project-name').text();
                    project.name = text;
                }
            }
            return;
        }
        project.rows[row] = [];
        //bat dau lay du lieu
        //left table
        tr.find('td').each(function(j){
            var td = $(this), colspan = parseInt(td.prop('colspan'));
            if( isNaN(colspan) || !colspan )colspan = 1;
            colspan--;
            var text = td.text();
            var markup = '';
            if( td.hasClass('phase-cell') ){
                markup = '[phase]';
            } else if( td.hasClass('the-task') ){
                markup = '[task]';
                //neu la phase co nhieu task, task thu 2 cua phase
                if( j == 0 ){
                    project.rows[row].push('[phase]');
                }
            } else if( td.hasClass('priority-cell') ){
                markup = '[priority]';
            } else if( td.find('span').length ){
                markup = '[number]';
                text = formatWC(td.find('span.workload').text(), td.find('span.consume').text(), td.find('span.consume-month').text());
            }
            project.rows[row].push(markup + text);
            // }
            if( td.hasClass('priority-cell') ){
                project.rows[row].push(markup);
                colspan--;
            }
            project.rows[row].push_repeat('', colspan);
        });

        //right table
        right.find('td').each(function(j){
            var td = $(this);
            markup = '[number]';
            text = formatWC(td.find('span.workload').text(), td.find('span.consume').text(), td.find('span.consume-month').text());
            project.rows[row].push(markup + text);
        });


        row++;
    });
    $('#json-header').val(JSON.stringify(header));
    $('#json-data').val(JSON.stringify(data)).closest('form').submit();
}

function scaleTable(){
    $.each(keyRow, function(key, id){
        var i = $('#absence').find('.task-right-' + id).height();
        if($.browser.mozilla == true){
            $('#absence-fixed').find('tr.task-row.task-' + id).css("height", i+10);
        } else {
            $('#absence-fixed').find('tr.task-row.task-' + id).css("height", i+10);
        }
    });
    var i = $('.header-height-fixed').height();
    if( $.zIE() !== false ){
        $('#month').css("height", i+2.4);
    }
    else if($.browser.mozilla == true){
        $('#month').css("height", i+0.4);
    }
    else {
        $('#month').css("height", i+2);
    }
    // set with header.
    // $('#header-phase').width(($('.phase-cell:first').width()));
    // $('#header-task').width(($('.the-task:first').width()));

    var k = $('#header-freeze').width();
    if($.browser.mozilla == true){
        $('.total-freeze:first').css("width", k);
    } else {
        $('.total-freeze:first').css("width", k-5);
    }
    var k = $('#header-total').width();
    if($.browser.mozilla == true){
        $('.total-workload:first').css("width", k);
    } else {
        $('.total-workload:first').css("width", k-5);
    }
    var k = $('#header-workload').width();
    if($.browser.mozilla == true){
        $('.the-workload:first').css("width", k);
    } else {
        $('.the-workload:first').css("width", k-5);
    }
    var k = $('#month').height();
    if($.browser.mozilla == true){
        $('#header-workload').css("height", k-11);
    } else {
        $('#header-workload').css("height", k-9);
    }
    var i = $('#total_wl').height(),
        j = $('#header-workload').width();
    if($.browser.mozilla == true){
        $('#month-row').css("height", i+12);
    } else {
        $('#month-row').css("height", i+10);
    }
    //setup scroll
    setupScroll();
}
var _timer;
$( window ).resize(function() {
    clearTimeout(_timer);
    _timer = setTimeout(function(){
        scaleTable();
        setupScroll();
    }, 750);
});
function bindKey(selector){
    selector.off('keydown').keydown(function(e){
        var key = e.keyCode ? e.keyCode : e.which;
        if(key == 9 || key == 13){
            var nextTd = selector.closest('td').next('td');
            if( e.shiftKey ){
                nextTd = selector.closest('td').prev('td');
            }
            var data = $.trim(nextTd.find('span.workload').text());

            if( nextTd.data('hasqtip') != undefined ){
                // open qtip
                // bind tab keys
            }else if( data ){
                setTimeout(function(){
                    showDialog(nextTd, e);
                    setupScroll();
                }, 50);
            }
        }
    });
    selector.off('keypress').keypress(function(e){
        var key = e.keyCode ? e.keyCode : e.which;
        if(!key || key == 8 || key == 13 || e.ctrlKey || e.shiftKey){
            return;
        }
        var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
        if( (!/^([0-9]+)(\.[0-9]{0,2})?$/.test(val)) ){
            e.preventDefault();
            return false;
        }
    });
}

function showDialog(selector, ev){

    //tao danh sach name/workload
    var tr = selector.closest('tr'),
        key = tr.data('type') + '-' + tr.data('id'),
        month = selector.data('time'),
        aTask = tr.data('type') == 'p' ? taskMap[tr.data('id')] : tr.data('id');
    var input = selector.find('input');
    input.focus(function(){
        input.select();
    });
    //phan nay tao list cho nhung cell chua co du lieu
    if( typeof assigns[key] == 'undefined' ){
        assigns[key] = {};
    }
    if( typeof assigns[key][month] == 'undefined' ){
        assigns[key][month] = {};
        $.each(listAssign[key], function(resource, abc){
            assigns[key][month][resource] = 0;
        });
    }
    //neu > 1 thi goi dialog con == 1 thi dung input
    if(Object.keys(listAssign[key]).length > 1){
        selector.qtip({
            overwrite: false,
            show: {
                solo: true,
                event: ev.type, // Use the same event type as above
                ready: true // Show immediately - important!
            },
            hide: 'unfocus',
            content: {
                text: function(e, api){
                    //lay du lieu tu assigns
                    var data = assigns[key][month];
                    var template = $('#tooltip-template').clone().removeClass('hidden').prop('id', ''),
                        item = template.find('.tooltip-workload').clone(),
                        list = template.find('.tooltip-assign');
                    //fill hidden input value
                    template.find('.tooltip-id').val(tr.data('id'));
                    template.find('.tooltip-type').val(tr.data('type'));
                    template.find('.tooltip-month').val(month);
                    //empty list
                    list.html('');
                    //append list
                    $.each(data, function(assignID, value){
                        resourceId = assignID.split('-');
                        var newItem = item.clone();
                        if( resourceId[0] == '1' ){
                            newItem.find('.tooltip-name').text(pcName);
                        } else {
                            newItem.find('.tooltip-name').text(resources[resourceId[1]]);
                        }
                        //workload
                        theInput = newItem.find('.tooltip-input');
                        theInput.prop({
                            name: 'data[workload][' + assignID + ']'
                        }).val(value);
                        bindKey(theInput);
                        //consume
                        try {
                            xconsume = resourceConsume[aTask][assignID][month];
                        } catch(ex){
                            xconsume = 0;
                        } finally {
                            xconsume = parseFloat(xconsume);
                        }
                        if( isNaN(xconsume) )xconsume = 0.00;
                        xconsume = xconsume.toFixed(2);
                        newItem.find('.tooltip-consume').text(xconsume);
                        //append to list
                        list.append(newItem);
                    });
                    template.find('.tooltip-input').click(function(){
                        $(this).select();
                    })
                    .off('keydown').on('keydown', function(e){
                        if( e.keyCode == 13 || e.keyCode == 9 ){
                            // find next input
                            $(this).closest('div').next().find('input').focus().select();
                            // if last input, save and find the cell in table.
                            if( !$(this).closest('div').next().hasClass('tooltip-workload') ){
                                template.find('.tooltip-form').find('.tooltip-ok').click();
                                selector.closest('td').next().click();
                            }
                            e.preventDefault();
                        }
                    });
                    //click vo cancel thi tat tooltip
                    template.find('.tooltip-cancel').on('click', function(){
                        api.hide();
                    });

                    //ajax save workload here
                    template.find('.tooltip-form')
                        .submit(function(){
                        template.find('button').prop('disabled', true);
                        $(this).ajaxSubmit({
                            dataType:  'json',
                            success: function(xdata){
                                //todo: update lai workload
                                taskId = tr.data('id');
                                if( tr.data('type') == 'a' ){
                                    id = activityOfATasks[taskId];
                                    //update workload cua activity task
                                    if( typeof workloadOfATask[taskId] == 'undefined' ){
                                        workloadOfATask[taskId] = {};
                                    }
                                    workloadOfATask[taskId][month] = xdata.total;
                                } else {
                                    id = projectOfPTasks[taskId];
                                    if( typeof workloadOfPTask[taskId] == 'undefined' ){
                                        workloadOfPTask[taskId] = {};
                                    }
                                    workloadOfPTask[taskId][month] = xdata.total;
                                }
                                //update assign
                                assigns[key][month] = xdata.assign;
                                //update total workload.
                                if( typeof totalWorkload[taskId] == 'undefined' ){
                                    totalWorkload[taskId] = {};
                                }
                                totalWorkload[taskId] = parseFloat(xdata.sum_wl);
                                //funny animation
                                selector.find('span.workload').addClass('updated');
                                selector.animate({
                                    'background-color': '#FFFFA3',
                                }, 400, function(){
                                    $(this).animate({
                                        'background-color': '#fff',
                                    }, 600);
                                });
                                template.find('button').prop('disabled', false);
                                //
                                calculateAll();
                                scaleTable();
                                //run staffing
                                staffing(id, tr.data('type'));
                                //end update
                                api.hide();
                            },
                            error: function(){
                                selector.find('span.workload').css({
                                    color: '#f00'
                                });
                                api.hide();
                            }
                        });
                        return false;
                    });
                    return template;
                },
                title: function(e, api){
                    return $('.header-' + selector.data('time')).text();
                }
            },
            position: {
                my: 'bottom center',
                at: 'top center'
            },
            style: {
                classes: 'qtip-shadow qtip-blue'
                // width: 150,
            },
            events: {
                show: function(event, api) {
                    selector.addClass('highlight');
                },
                hide: function(event, api){
                    selector.removeClass('highlight');
                }
            }
        });
    } else {
        var _val = parseFloat(selector.find('span.workload').text()),
            input = selector.find('input');
        if( isNaN(_val) )_val = 0;
        if( input.data('shown') ){
            return;
        }
        selector.find('span').hide();
        bindKey(input);
        input
            .removeClass('input-hidden')
            .val(_val)
            .data({
                'old-workload': _val,
                'shown': true
            })
            .focus()
            .off('change blur')
            .change(function(){
                //lay du lieu tu assigns
                var data = assigns[key][month];
                input.data('shown', false).prop('disabled', true);
                selector.find('span.workload').addClass('pch_loading loading_input');
                var newWorkload = parseFloat(input.val()),
                    oldWorkload = parseFloat(input.data('old-workload'));
                if( newWorkload != oldWorkload && !isNaN(newWorkload) ){
                    //ajax
                    wl = {};
                    $.each(data, function(resource, val){
                        wl[resource] = newWorkload;
                    });
                    $.ajax({
                        url: $('.tooltip-form:first').prop('action'),
                        type: 'post',
                        dataType: 'json',
                        data: {
                            data: {
                                id: tr.data('id'),
                                type: tr.data('type'),
                                month: month,
                                workload: wl
                            }
                        },
                        success: function(xdata){
                            input.prop('disabled', false).addClass('input-hidden');
                            selector.find('span').show();
                            selector.find('span.consume-month').hide();
                            //todo: update lai workload
                            taskId = tr.data('id');
                            if( tr.data('type') == 'a' ){
                                id = activityOfATasks[taskId];
                                //update workload cua activity task
                                if( typeof workloadOfATask[taskId] == 'undefined' ){
                                    workloadOfATask[taskId] = {};
                                }
                                workloadOfATask[taskId][month] = xdata.total;
                            } else {
                                id = projectOfPTasks[taskId];
                                if( typeof workloadOfPTask[taskId] == 'undefined' ){
                                    workloadOfPTask[taskId] = {};
                                }
                                workloadOfPTask[taskId][month] = xdata.total;
                            }
                            //update assign
                            assigns[key][month] = xdata.assign;
                            //update total workload
                            if( typeof totalWorkload[taskId] == 'undefined' ){
                                totalWorkload[taskId] = {};
                            }
                            totalWorkload[taskId] = parseFloat(xdata.sum_wl);
                            //funny animation
                            setTimeout(function(){
                                selector.find('span.workload').removeClass('pch_loading loading_input');
                            }, 300);
                            selector.find('span.workload').addClass('updated');
                            selector.animate({
                                'background-color': '#FFFFA3',
                            }, 400, function(){
                                $(this).animate({
                                    'background-color': '#fff',
                                }, 600);
                            });
                            //
                            calculateAll();
                            scaleTable();
                            //run staffing
                            staffing(id, tr.data('type'));
                            //end update
                        }
                    });
                } else {
                    selector.find('span.workload').removeClass('pch_loading loading_input');
                    input.addClass('input-hidden').prop('disabled', false);
                    selector.find('span').show();
                    selector.find('span.consume-month').hide();
                }
            })
            .blur(function(){
                if( input.data('shown') ){
                    input.data('shown', false);
                    input.addClass('input-hidden');
                    selector.find('span').show();
                    selector.find('span.consume-month').hide();
                }
           });
        ;
    }

}

function setupScroll(){
    rightContent = $('#absence-fixed');
    rightHeaderHeight = $('#right-header').height();
    rightScroll = $('#scrollLeftAbsenceContent');
    rightScrollContainer = $('#scrollLeftAbsence');
    //fix position for right scroll container
    rightScrollContainer.height($('#right-scroll').height()).css('top', rightHeaderHeight + 20);
    //right scroll content height
    var rightHeight = rightContent.height() + 30;
    rightScroll.height(rightHeight);
    //top scroll
    $('#scrollTopAbsence').width($('#right-scroll').width()).css('margin-left', $('#left-scroll').width());
    $('#scrollTopAbsence div').width($('#right-header').width());
    $('#absence-scroll').css('left', $('#left-scroll').width());
}
</script>
