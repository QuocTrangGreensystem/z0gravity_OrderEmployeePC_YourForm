<?php echo $html->css(array('gantt_v2')); ?>
<?php echo $html->script('history_filter'); ?>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<?php
$arg = $this->passedArgs;
$arg["?"] = $this->params['url'];
unset($arg['?']['url'], $arg['?']['ext']);
$type = 'month';
?>

<!--[if lt IE 9]>
<?php echo $html->script('flash_canvas/flashcanvas'); ?>
<script type="text/javascript">
    var _createElement = document.createElement;
    document.createElement = function(n){
        var element = _createElement.call(this,n);
        if(n=="canvas"){
            document.getElementById("target").appendChild(element);
            FlashCanvas.initElement(element);
        }
        return element;
    };
</script>
<div id="target" style="position: absolute; top: -10000px;left: -999999px;"></div>
<![endif]-->

<?php
echo $html->script(array('html2canvas', 'jquery.html2canvas_v2'));
echo $html->css('jquery.mCustomScrollbar');
echo $html->script(array('jquery-ui.min', 'jquery.easing.1.3', 'jquery.mCustomScrollbar'));
?>
<?php //echo $html->script('jsgantt');                    ?>
<style type="text/css">
 #gantt-display{overflow:hidden;padding-top:10px;}#gantt-display .input{float:left;}#gantt-display .input input{vertical-align:middle;}#gantt-display .input label{padding:0 7px;}#gantt-display .title{float:left;font-weight:700;padding-right:10px;}.wd-customs{border:solid 1px silver;padding:6px;}
 /*ADD CODE BY VINGUYEN 06/08/2014*/
.gantt-title.gantt-head td div{
	width:60px !important;
}
.gantt-title.gantt-head .gantt-name div{
	width:220px !important;
	padding:0 !important;
}
.gantt-node.gantt-child .gantt-name{
	width:200px !important;
	padding:0 !important;
}
.gantt-title.gantt-head .gantt-func div{
	width:156px !important;
	padding:0 !important;
}
</style>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">

        <div class="wd-main-content">
            <div class="wd-title">
            	<?php echo $this->element("checkStaffingBuilding") ?>
                <a href="#" onclick="SubmitDataExport();return false;" class="export-excel-icon-all" style="margin-right:5px; " title="<?php __('Export Excel')?>"><span><?php __('Export Excel') ?></span></a>
                <div style="float: right; margin-right: 5px; margin-top: 2px;">
                    <?php
                    echo $this->Form->create('Display', array('type' => 'get', 'url' => array(
                            'controller' => 'activity_tasks',
                            'action' => 'visions', $activityName['Activity']['id']
                        )));
                    if(!empty($staffingCate)){
                            $employee = ($staffingCate == 'employee') ? 'selected="selected"' : '';
                            $profit = ($staffingCate == 'profit') ? 'selected="selected"' : '';
							$profile = ($staffingCate == 'profile') ? 'selected="selected"' : '';
                        }
                    ?>
                    <div>
                        <select class="wd-customs" id="CategoryCategory">
                            <option value="employee" <?php echo isset($employee) ? $employee : '';?>><?php echo  __("Employees", true)?></option>
                            <option value="profit" <?php echo isset($profit) ? $profit : '';?>><?php echo  __("Profit Centers", true)?></option>
                            <?php if(isset($companyConfigs['activate_profile']) && $companyConfigs['activate_profile'])
							{ ?>
                            <option value="profile" <?php echo isset($profile) ? $profile : '';?>><?php echo  __("Profile", true)?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <?php echo $this->Form->end(); ?>
                </div>
            </div>
             <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <!-- <div class="wd-tab"><div class="wd-panel"> -->
            <div class="wd-tab">
                <?php
                if (isset($view_id))
                    echo $this->element('project_tab_view');
                else
                //echo $this->element('project_tab')
                    
                    ?>
                <div class="wd-panel">
                    <div class="wd-section" id="wd-fragment-1">
                        <?php echo $this->Session->flash(); ?>
                        <div class="wd-content">
                            <h2 class="vision-project"><?php echo sprintf(__('Vision Activity of %s', true), $activityName['Activity']['name']); ?></h2>
                            <div id="GanttChartDIV">

                                <?php
                                $rows = 0;
                                $start = $end = 0;
                                $data = $projectId = $conditions = array();
                                $start = !empty($startDateFilter) ? $startDateFilter : '';
                                $end = !empty($endDateFilter) ? $endDateFilter : '';
                                //$summary = isset($this->params['url']['summary']) ? (bool) $this->params['url']['summary'] : false;
                                //$showType = isset($this->params['url']['type']) ? (int) $this->params['url']['type'] : 0;

                                if (empty($start) || empty($end)) {
                                    echo $this->Html->tag('h1', __('No data exist to create Gantt chart', true));
                                } else {
                                    // change in here
                                    $stones = array();
                                    $this->GanttSt->create($type, $start, $end, $stones, true);
                                    echo $this->Html->scriptBlock('GanttData = ' . $this->GanttSt->drawStaffing($staffings, true, $showType, $profile));
                                    $this->GanttSt->end();
                                }
                                ?>
                                <div style="clear: both"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
echo $this->Form->create('Export', array('url' => array_merge($arg, array('controller' => 'activity_tasks', 'action' => 'export_visions')), 'type' => 'file'));
echo $this->Form->hidden('canvas', array('id' => 'canvasData'));
echo $this->Form->hidden('height', array('id' => 'canvasHeight'));
echo $this->Form->hidden('width', array('id' => 'canvasWidth'));
echo $this->Form->hidden('activities', array('value' => $activityName['Activity']['name']));
echo $this->Form->hidden('rows', array('value' => $rows));
echo $this->Form->hidden('start', array('value' => $start));
echo $this->Form->hidden('end', array('value' => $end));
echo $this->Form->hidden('activity_id', array('value' => $activity_id));
echo $this->Form->hidden('months', array('value' => serialize($this->GanttSt->getMonths())));
echo $this->Form->hidden('displayFields', array('value' => '0'));
echo $this->Form->hidden('category');
echo $this->Form->end();
?>
<?php
echo $this->element('dialog_projects');
$_start = date('Y', $start);
$year = range($_start, $_start + (date('Y', $end) - $_start));
?>
<style type="text/css">
 .ui-dialog,.type_buttons a{color:#000;font-size:100%;}.gantt-list-primary tr td,.gantt-list-primary td div{white-space:nowrap;}.gantt-d31 div{width:62px;}.gantt-d30 div{width:60px;}.gantt-d29 div{width:58px;}.gantt-d28 div{width:56px;}.gantt-image{padding-right:10px;vertical-align:middle;height:16px;width:16px;}.gantt-unzero{background-color:#68A8CA !important;}.gantt-unzero input, .gantt-unzero div{color:#fff !important;}.gantt-summary .gantt-image{display:none;}.number-col{ margin:0 !important; min-width:62px; max-width:62px; width:62px; overflow:hidden}.head-cols td{ min-width:67px; max-width:67px; width:67px; overflow:hidden;}
</style>
<!-- Dialog Export -->
<div id="dialog_vision_export" class="buttons" title="<?php __("Display Fields") ?>" style="display: none;">
    <fieldset>
        <div style="height:auto;" class="wd-scroll-form">
            <div class="input">
                <?php
                $options = array();
                if($showType == 0){
                    $options = array(
                        0 => __("All fields", true),
                        'validated' => __('Workload', true),
                        'consumed' => __('Consumed', true),
                        'capacity' => __('Capacity', true),
                        'absence' => __('Absence', true),
                        'totalWorkload' => __('Total Workload', true),
                        'assignEm' => __('% Assigned to employee', true)
                    );
                } elseif($showType == 1){
                    $options = array(
                        0 => __("All fields", true),
                        'validated' => __('Workload', true),
                        'consumed' => __('Consumed', true),
                        'capacity' => __('Capacity', true),
                        'absence' => __('Absence', true),
                        'totalWorkload' => __('Total Workload', true),
                        'assignPc' => __('% Assigned to profit center', true)
                    );
                } else {
                    $options = array(
                        0 => __("All fields", true),
                        'validated' => __('Workload', true),
                        'consumed' => __('Consumed', true),
                        'remains' => __('Remain', true)
                    );
                }
                echo $this->Form->input('displayFields', array(
                    'div' => false,
                    'label' => false,
                    'name' => 'displayFields',
                    'empty' => false,
                    'id' => 'dialog_vision_export_fields',
                    'multiple' => 'checkbox',
                    'hiddenField' => false,
                    // 'style' => 'margin-right:11px; width:52% !important',
                    'value' => 0,
                    "options" => $options
                ));
                ?>   							
            </div>
        </div>
    </fieldset>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel" id="no_port"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_port"><?php __('OK') ?></a></li>
    </ul>
</div>
<style type="text/css">
  #dialog_vision_export fieldset label{font-size:13px!important;color:#000;float:none;}#dialog_vision_export .input .checkbox{overflow:hidden;padding-left:20px;}#dialog_vision_export .input .checkbox label{font-weight:400;}#dialog_vision_export .input .checkbox input{margin-right:10px;}.error-message{color:#D52424;}
  .list-task div{ word-break:keep-all !important; word-spacing:normal; word-wrap:normal; width:auto !important; overflow:hidden ; }  .list-task{ overflow:hidden ;}
</style>
<!-- Dialog Export -->

<!-- dialog_import -->
<div id="dialog_import_CSV" title="Import CSV file" class="buttons" style="display: none;">
    <?php
    echo $this->Form->create('Import', array('id' => 'uploadForm', 'type' => 'file',
        'url' => array('controller' => 'activity_tasks', 'action' => 'import', $activityName['Activity']['id'])));
    ?>
    <div class="wd-input">
        <center>
            <label><?php echo __('File:') ?></label>
            <input type="file" name="FileField[csv_file_attachment]" rel="no-history" />
        </center>
        <div style="clear:both; margin-left:100px; width: 220px; color: #008000; font-style:italic;">(Allowed file type: *.csv)</div>
    </div>
    <ul class="type_buttons">
        <li><a class="cancel" href="javascript:void(0)"><?php echo __('Close') ?></a></li>
        <li><a id="import-submit" class="new" onclick="return false;" href="#"><?php echo __('Submit') ?></a></li>
        <li id="error"></li>
    </ul>
    <?php echo $this->Form->end(); ?>
</div>
<!-- End dialog_import -->

<script type="text/javascript">
    function SubmitDataExport(){
        var $ = jQuery;
        $('#dialog_vision_export').dialog({
            position    :'center',
            autoOpen    : true,
            autoHeight  : true,
            modal       : true,
            width       : 300,
            open : function(e){
                //var $dialog = $(e.target);
            }
        });
        $('#no_port').click(function(){
            $('#dialog_vision_export').dialog('close');
        });
        $('#ok_port').click(function(){
            var input = $('#dialog_vision_export input[name="displayFields[]"]').filter(':checked');
            if(!input.length){
                alert('<?php echo h(__('Please choose a fields to export' , true)); ?>');
            }else{
                $('#dialog_vision_export').dialog('close');
                //var rs = [];
                //$.each(input);
                $('#ExportDisplayFields').val($.map(input.get() , function(v){return $(v).val();}).join(','));
                $('#ExportCategory').val($('#CategoryCategory').val());
                $('#GanttChartDIV').html2canvas();
            }
        });
        $('#DisplayFields0').click(function(){
            $(this).closest('.input').find('input').prop('checked' , $(this).is(':checked'));
        });
        $('#DisplayFields0').attr('checked', 'checked');
        if($('#DisplayFields0').is(':checked')){
            $('#DisplayFieldsValidated').attr('checked', 'checked');
            $('#DisplayFieldsConsumed').attr('checked', 'checked');
            $('#DisplayFieldsAbsence').attr('checked', 'checked');
            $('#DisplayFieldsCapacity').attr('checked', 'checked');
            $('#DisplayFieldsTotalWorkload').attr('checked', 'checked');
            $('#DisplayFieldsAssignTo').attr('checked', 'checked');
            $('#DisplayFieldsAssignPc').attr('checked', 'checked');
            $('#DisplayFieldsAssignEm').attr('checked', 'checked');
            //$(this).closest('.input').find('input').prop('checked' , $(this).is(':checked'));
        }
    }

    (function($){
        
        $(function(){
            $.ajaxSetup({
                cache: false
            });
            var na = '<?php echo $this->GanttSt->na; ?>';
            var yl = <?php echo json_encode($year); ?>;
            var url = '<?php echo $this->Html->url(array('action' => 'update_staffing_demo', $activity_id)); ?>';
            var employees = '<?php echo $this->Html->url(array('action' => 'employees')); ?>';
            var regNum = /^(([\-1-9][0-9]{0,2})|(0))(\.[0-9]{0,1})?$/;
            /**
             * Auto identify
             * 
             * @param prefix
             * 
             * return identify
             */
            $.fn.identify = function(prefix) {
                var i = 0,id='';
                this.each(function() {
                    id = $(this).attr('id');
                    if(id) return false;
                    do { 
                        i++;
                        id = prefix + '_' + i;
                    } while($('#' + id).length > 0);            
                    $(this).attr('id', id);            
                });
                return id;
            };
            /**
             * Convert to float
             * 
             * @param val
             * @param dp
             * 
             * return mixed na or float number 
             */
            var toFloat = function (val,dp){
                //if(dp && (val.length == 0 || val == na || !regNum.test(val))){
//                    return na;
//                }
                val =  Number(parseFloat(val || '0').toFixed(1));
                if(val < 0){
                    val = 0;
                }
                return !regNum.test(val)  ? 0 : val;
            }
            /**
             * Get input collections
             * 
             * @param $list
             * @param rel
             * @param node
             * 
             * return object list input
             */
            var getInput = function($list,rel,node){
                return {
                    e:$list.find('[rel="e-'+rel+'"] '+node),
                    v:$list.find('[rel="v-'+rel+'"] '+node),     
                    c:$list.find('[rel="c-'+rel+'"] '+node),        
                    r:$list.find('[rel="r-'+rel+'"] '+node),          
                    f:$list.find('[rel="f-'+rel+'"] '+node)
                };
            }
            /**
             * Set forecast highlight
             * 
             * @param $list
             * @param method
             * 
             * return object list input
             */
            var setHighlight = function($input, method){
                var kclass = '';
                if(Number($input.f[method]()) > Number($input.v[method]())){
                    kclass = 'gantt-invalid';
                }
                $input.f.closest('td').removeClass('gantt-invalid').addClass(kclass);
            }
            /**
             * Synchronous vertical data
             * 
             * @param $list
             * @param val
             * @param rel
             * 
             * return object list input
             */
            var syncVertical = function($list,rel){
                var $input = getInput($list,rel,'input');
                var $sum = getInput($('.gantt-chart').find('[rel="summary"]'),rel,'div');
                var val,val2;
                
                var forecast = 0;
                if(toFloat($input.c.val() , true) != na){
                    forecast = Number($input.c.val()) + Number($input.r.val());
                }else{
                    forecast = Number($input.r.val());
                } 
                $input.f.val(forecast);
            
                $.each($input , function(k,$v){
                    val = getIncrease.call($v,rel);
                    val2 = toFloat($sum[k].html() , true);
                    
                    if(val2 == na && val != na){
                        val2 = 0;
                    }else if(val2 != na && val == na){
                        val = 0;
                    }
                
                    $sum[k].html(toFloat(val2 + val));
                    if(k == 'f'){
                        return true;
                    }
                
                    val = toFloat($v.val() , true);
                    if(val !=  na && val != 0){
                        $input[k].parent().addClass('gantt-unzero');
                    }else{
                        $v.val(k == 'c' && val != '0' ? na : 0);
                        $input[k].parent().removeClass('gantt-unzero');
                    }
                });
            
                setHighlight($input,'val');
                setHighlight($sum,'html');
                return $input;
            }
            /**
             * Synchronous horizontal data
             * 
             * @param $list
             * @param $chart
             * @param rel
             * @param node
             * 
             * return void
             */
            var syncHorizontal = function($list,$chart,rel,node){
                var c,v,has,
                method = node == 'input' ? 'val':'html',$input;
           
                $.each(['e','v','c','r','f'], function(undefined,k){
                    c = 0, has = false;    
                    for(var i = 1; i<=12 ;i++){
                        v = $chart.find('[rel="'+k+'-'+rel+ '-' + i + '"] '+ node)[method]();
                        
                        if(v != na){
                            has = true;
                        }
                        c += toFloat(v);
                    }
                    c = parseFloat(c).toFixed(1);
                    $list.find('[rel="'+k+'-'+rel+'"] div').html(!has ? na : c);
                
                    c = 0, has = false;
                    for(i = 0; i< yl.length ;i++){
                        v = $list.find('[rel="'+k+'-' + yl[i] + '"] div').html();
                        if(v !=  na){
                            has = true;
                        }
                        c += toFloat(v);
                    }
                    c = parseFloat(c).toFixed(1);
                    $list.find('[rel="'+k+'-total"] div').html(!has ? na : c);
                });
                setHighlight(getInput($list,rel,'div'),'html');
                setHighlight(getInput($list,'total','div'),'html');
            }
            /**
             * Update grid element event
             * 
             * @param $element
             * 
             * return void
             */
            var getIncrease =function(val){
                var val = this.data('_value'),
                _val = toFloat(this.val() , true);
                this.data('_value',_val);
                switch(true){
                    case (val == na && _val != na) : 
                        val = _val;
                        break
                    case (val != na && _val == na) : 
                        val = - val;
                        break
                    case (val != na && _val != na) : 
                        val = _val - val;
                        break
                    default : 
                        val = na;
                }
                return val;
            }
            /**
             * Update grid element event
             * 
             * @param $element
             * 
             * return void
             */
            var updateElement =function($element){
                var num = $element.closest('table').attr('rel');
                var check = $element.closest('table').attr('check');
                var rel = String($element.attr('rel')).split('-');
                var type = rel.shift();
                var year = rel[0];
                rel = rel.join('-');
                var $input = syncVertical($element.closest('.gantt-staff'),rel);
                syncHorizontal($('.gantt-list').find('[rel="list-'+num+'"]'),$('.gantt-chart').find('[rel="'+num+'"]'),year,'input');
                syncHorizontal($('.gantt-list').find('[rel="list-summary"]'),$('.gantt-chart').find('[rel="summary"]'),year,'div');
            
                var consumed = toFloat($input.c.val() , true);
                if(consumed == na){
                    consumed = '';
                }
                $.ajax({
                    type:'POST',
                    url:url,
                    data:{
                        data:{
                            estimation:toFloat($input.e.val()),
                            //validated:toFloat($input.v.val()),
                            //consumed : consumed,
                            //remains:toFloat($input.r.val()),
                            date:rel+'-1',
                            name:num,
                            is_check: check
                        }
                    },
                    cache: false,
                    success:function(content){
                        //alert(content);
                    }
                });
            };
            /**
             * Update function employees grid element event
             * 
             * @param type
             * @param data
             * @param $target
             * @param $element
             * 
             * return void
             */
            var validateInput = function(type,rel,$target,$element){
                var value = Number(this.val());
                var validated = 100;
                this.removeClass('gantt-invalid');
                if(rel =='manday'){
                    validated = Number($element.find('[rel="'+type.charAt(0)+'-total"] div').html());
                }
                var max = 0;
                $target.find('[rel="'+type+'-'+rel+'"] input').not(this).each(function(){
                    max += Number($(this).val());
                });
                
                if(max+value > validated){
                    value = Math.max(0,validated - max);
                    alert('<?php echo h(__('The value is not to be greater than %1$s, valuable suggestion %2$s', true)); ?>'.replace('%1$s',validated).replace('%2$s',value));
                    this.addClass('gantt-invalid');
                    return false;
                }
                this.val(value);
                return true;
            }
            /**
             * Attach dialog employees grid element event
             * 
             * return void
             */
            var attachDialog = function($list){
				
                var $showType = <?php echo json_encode($showType);?>;
                if($showType == false){
                    $list.find('table').not('.gantt-summary').find('.gantt-name div').click(function(){
                    var $element = $(this), identify = $element.identify('gantt-name');
                    var employee_name = $element.find('span').html();
                    var start = <?php echo json_encode($startEmployees);?>;
                    var end = <?php echo json_encode($endEmployees);?>;
                    var id_employee = $element.attr('rel') ? $element.attr('rel') : 0;
                    // ten employee
                    $(".gs-name-header span").html(employee_name);
                    // chen title cua avai
                    var startDate = start[0] + '-' + start[1] + '-' + start[2];
                    var endDate = end[0] + '-' + end[1] + '-' + end[2];
                    //$(".gs-header-content-name span").html(inforTasks.name);
                    $(".gs-header-content-start span").html(startDate);
                    $(".gs-header-content-end span").html(endDate);
                    //$(".gs-header-content-work span").html(inforTasks.workload);
                    if(id_employee == 999999999){
                        return false;
                    }
                    // phan content
                    function getVocationDetail(){
                        var result = [];
                        var _startDate = start[2] + '-' + start[1] + '-' + start[0];
                        var _endDate = end[2] + '-' + end[1] + '-' + end[0];
                        $.ajax({
                            url : '/project_staffings/getVocationDetailByMonth/' + id_employee + '/' + _startDate + '/' + _endDate,
                            async: false,
                            dataTye: 'json',
                            success: function(data){
                                data = JSON.parse(data);
                                result = data;       
                            },
                            error: function(message){
                            }                   
                        });
                        return result;
                    }
                    var datas = getVocationDetail();
                    var widthDivRight = 0;
                    function init(){
                        var headers = avais = vocs = work = '';
                        var totalCount = totalVacation = 0;
                        if(datas.vocation){
                            headers += '<tr>';
                            $.each(datas.vocation, function(index, values){
                                var count = 0;
                                $.each(values, function(ind, val){
                                    count++;
                                    totalCount++;
                                    totalVacation += parseFloat(val);
                                });
                                headers += '<td colspan="' + count + '" class="text-center">' + index + '</td>';
                            });
                            headers += '</tr><tr>';
                            avais += '<tr id="total-avai-popup">';
                            vocs += '<tr id="total-vocs-popup">';
                            work += '<tr id="total-workload-popup">';
                            $.each(datas.vocation, function(index, values){
                                $.each(values, function(ind, val){
                                    ind = ind.split('-');
                                    var keyWl = index+'-'+ind[1]+'-'+ind[0];
                                    ind = ind[0]+'-'+datas.dayMaps[ind[1]];
                                    widthDivRight += 50;
                                    headers += '<td><div class="text-center">' + ind + '</div></td>';
                                    var _vais = '';
                                    if(val == 1){
                                        _vais = 0;
                                    }
                                    avais += '<td><div id="avai-' + keyWl + '">' + _vais + '</div></td>';
                                    vocs += '<td><div id="vocs-' + keyWl + '">' + val + '</div></td>';
                                    work += '<td><div id="' + keyWl + '">' + 0 + '</div></td>';
                                });
                            });
                            headers += '</tr>';
                            avais += '</tr>';
                            vocs += '</tr>';
                            work += '</tr>';
                        }
                        $(".popup-header-2").html(headers);
                        $(".popup-availa-2").html(avais);
                        $(".popup-vaication-2").html(vocs);
                        $(".popup-workload-2").html(work);
                        
                        // phan detail cua task
                        var listTaskDisplay = '';
                        var valTaskDisplay = '';
                        var totalWorkload = [];
                        if(datas.dataDetail){
                            if(datas.dataDetail.project){
                                $.each(datas.dataDetail.project, function(project_id, values){
                                    var project_name = datas.groupNames.project[project_id] ? datas.groupNames.project[project_id] : '';
                                    listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">' + project_name + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
                                    valTaskDisplay += '<tr class="project-activity-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
                                    var stt = 0;
                                    $.each(values, function(pTask_id, value){
                                        valTaskDisplay += '<tr>';
                                        stt++;
                                        var priorities = datas.priority.project[pTask_id] ? datas.priority.project[pTask_id] : '';
                                        var projectTaskName = datas.groupNameTasks.project[pTask_id] ? datas.groupNameTasks.project[pTask_id] : '';
                                        listTaskDisplay += '<tr><td class="list-task"><div>&nbsp;&nbsp;'+ stt +'. ' + projectTaskName + '</div></td><td><div>' + priorities + '</div></td><td><div>&nbsp;</div></td></tr>';
                                        $.each(datas.vocation, function(index, values){
                                            $.each(values, function(ind, val){
                                                ind = ind.split('-');
                                                ind = index+'-'+ind[1]+'-'+ind[0];
                                                var _value = value[ind] ? value[ind] : 0;
                                                if(val == 1){
                                                    _value = 0;
                                                }
                                                valTaskDisplay += '<td><div>' + _value + '</div></td>';
                                                if(!totalWorkload[ind]){
                                                    totalWorkload[ind] = 0;
                                                }
                                                totalWorkload[ind] += _value;
                                            });
                                        });
                                        valTaskDisplay += '</tr>';
                                    });
                                });
                            }
                            if(datas.dataDetail.activity){
                                $.each(datas.dataDetail.activity, function(activity_id, values){
                                    var activity_name = datas.groupNames.activity[activity_id] ? datas.groupNames.activity[activity_id] : '';
                                    listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">' + activity_name + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
                                    valTaskDisplay += '<tr class="project-activity-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
                                    var stt = 0;
                                    $.each(values, function(aTask_id, value){
                                        valTaskDisplay += '<tr>';
                                        stt++;
                                        var priorities = datas.priority.activity[aTask_id] ? datas.priority.activity[aTask_id] : '';
                                        var activityTaskName = datas.groupNameTasks.activity[aTask_id] ? datas.groupNameTasks.activity[aTask_id] : '';
                                        listTaskDisplay += '<tr><td class="list-task"><div>&nbsp;&nbsp;'+ stt +'. ' + activityTaskName + '</div></td><td><div>' + priorities + '</div></td><td><div>&nbsp;</div></td></tr>';
                                        $.each(datas.vocation, function(index, values){
                                            $.each(values, function(ind, val){
                                                ind = ind.split('-');
                                                ind = index+'-'+ind[1]+'-'+ind[0];
                                                var _value = value[ind] ? value[ind] : 0;
                                                if(val == 1){
                                                    _value = 0;
                                                }
                                                valTaskDisplay += '<td><div>' + _value + '</div></td>';
                                                if(!totalWorkload[ind]){
                                                    totalWorkload[ind] = 0;
                                                }
                                                totalWorkload[ind] += _value;
                                            });
                                        });
                                        valTaskDisplay += '</tr>';
                                    });
                                });
                            }
                        }
                        var totalWorkloads = totalAvais = 0;
                        $('#total-workload-popup').find('td div').each(function(){
                            var getId = $(this).attr('id');
                            var getTotalWl = totalWorkload[getId] ? totalWorkload[getId].toFixed(3) : 0;
                            totalWorkloads += parseFloat(getTotalWl);
                            var getAvais = 1 - getTotalWl;
                            if (!isNaN(getAvais) && getAvais.toString().indexOf('.') != -1){
                                getAvais = getAvais.toFixed(3);
                            }
                            var vocs = $('#total-vocs-popup').find('#vocs-'+getId).html();
                            if(vocs == 1){
                                getAvais = 0;
                            }
                            totalAvais += parseFloat(getAvais);
                            $('#total-avai-popup').find('#avai-'+getId).html(getAvais);
                            $('#'+getId).html(getTotalWl);
                        });
                        totalWorkloads = totalWorkloads.toFixed(3);
                        totalAvais = totalAvais.toFixed(3);
                        if(totalAvais < 0){
                            totalAvais = 0;
                        }
                        $('#total-availability, .gs-header-content-avai span').html(totalAvais);
                        $('#total-vacation').html(totalVacation);
                        $('#total-workload').html(totalWorkloads);
                        
                        $(".popup-task-detail").html(listTaskDisplay);
                        $(".popup-task-detail-2").html(valTaskDisplay);
                        
                    }
                    function initMonth(){
                        $('#filter_month').addClass('ch-current');
                        var headers = working = dayOff = capacity = avais = workload = overload = '';
                        var totalCount = totalWorkingDay = totalDayOff = totalCapacity = totalOverload = 0;
                        widthDivRight = 0;
                        if(datas.MonthVocations){
                            headers += '<tr>';
                            $.each(datas.MonthVocations, function(index, values){
                                var count = 0;
                                $.each(values, function(ind, val){
                                    count++;
                                    totalCount++;
                                    totalDayOff += parseFloat(val);
                                });
                                headers += '<td colspan="' + count + '" class="text-center">' + index + '</td>';
                            });
                            headers += '</tr><tr>';
                            working += '<tr id="total-working-popup">';
                            dayOff += '<tr id="total-dayOff-popup">';
                            capacity += '<tr id="total-capacity-popup">';
                            workload += '<tr id="total-workload-popup">';
                            avais += '<tr id="total-avai-popup">';
							overload += '<tr id="total-over-popup">';
                            
                            $.each(datas.MonthVocations, function(index, values){
                                if(values){
                                    $.each(values, function(ind, val){
                                        var keyWl = index+'-'+ind;
                                        var theWorking = datas.MonthWorkingDays[index][ind] ?  datas.MonthWorkingDays[index][ind] : 0;
                                        totalWorkingDay += parseFloat(theWorking);
                                        widthDivRight += 50;
                                        headers += '<td><div class="text-center">' + ind + '</div></td>';
                                        working += '<td><div id="working-' + keyWl + '">' + theWorking + '</div></td>';
                                        dayOff += '<td><div id="dayOff-' + keyWl + '">' + val + '</div></td>';
                                        var _capa = parseFloat(theWorking) - parseFloat(val);
                                        totalCapacity += parseFloat(_capa);
                                        capacity += '<td><div id="capacity-' + keyWl + '">' + _capa + '</div></td>';
                                        workload += '<td><div id="' + keyWl + '">' + 0 + '</div></td>';
                                        avais += '<td><div id="avai-' + keyWl + '">' + 0 + '</div></td>';
                                        overload += '<td><div id="over-' + keyWl + '">' + 0 + '</div></td>';
                                    });
                                }
                            });
                            headers += '</tr>';
                            working += '</tr>';
                            dayOff += '</tr>';
                            capacity += '</tr>';
                            workload += '</tr>';
                            avais += '</tr>';
							overload += '</tr>'; 
                        }
                        $(".popup-header-2").html(headers);
                        $(".popup-working-2").html(working);
                        $(".popup-dayOff-2").html(dayOff);
                        $(".popup-capacity-2").html(capacity);
                        $(".popup-workload-2").html(workload);
                        $(".popup-availa-2").html(avais);
						$(".popup-over-2").html(overload);
                        
                        // phan detail cua task
                        var listTaskDisplay = '';
                        var valTaskDisplay = '';
                        var totalWorkload = [];
                        var listSumFamily = [];
                        var totalFamily = [];
                        if(datas.listMonthDatas){
                            $.each(datas.listMonthDatas, function(idFamily, values){
                                var familyName = datas.families[idFamily] ? datas.families[idFamily] : '';
                                listTaskDisplay += '<tr class="family-group"><td><div style="font-weight: bold;">&nbsp;' + familyName + '</div></td><td><div>&nbsp;</div></td><td class="ch-fam"><div id="total-fam-'+idFamily+'">&nbsp;</div></td></tr>';
                                //valTaskDisplay += '<tr class="family-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
                                valTaskDisplay += '<tr class="family-group">';
                                $.each(datas.MonthVocations, function(index, values){
                                    $.each(values, function(ind, val){
                                        ind = index+'-'+ind;
                                        valTaskDisplay += '<td><div id="fam-'+idFamily+'-'+ind+'">&nbsp;</div></td>';
                                    });
                                });
                                valTaskDisplay += '</tr>';
                                var sttActivity = 0;
								
								
                                $.each(values, function(idGlobal, value){
                                    sttActivity++;
                                    idGlobal = idGlobal.split('-');
                                    if(idGlobal[0] === 'ac'){
                                        var activityName = datas.ListActivities[idGlobal[1]] ? datas.ListActivities[idGlobal[1]] : '';
                                        listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">&nbsp;'+ sttActivity +'. ' + activityName + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
                                        valTaskDisplay += '<tr class="project-activity-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
                                        var sttTask = 0;
										
                                        $.each(value, function(idTask, valTask){
                                            sttTask++;
                                            valTaskDisplay += '<tr >';
                                            var idPriority = datas.PriorityActivityTasks[idTask] ? datas.PriorityActivityTasks[idTask] : 0;
                                            var priorities = datas.ProjectPriorities[idPriority] ? datas.ProjectPriorities[idPriority] : '';
                                            var activityTaskName = datas.NameActivityTasks[idTask] ? datas.NameActivityTasks[idTask] : '';
                                            listTaskDisplay += '<tr ><td class="list-task"><div>&nbsp;'+ sttActivity +'.'+ sttTask +'. ' + activityTaskName + '</div></td><td><div>' + priorities + '</div></td><td><div>&nbsp;</div></td></tr>';
                                            $.each(datas.MonthVocations, function(index, values){
                                                $.each(values, function(ind, val){
                                                    ind = index+'-'+ind;
                                                    var _value = valTask[ind] ? valTask[ind] : 0;
                                                    valTaskDisplay += '<td><div>' + _value + '</div></td>';
                                                    if(!totalWorkload[ind]){
                                                        totalWorkload[ind] = 0;
                                                    }
                                                    totalWorkload[ind] += _value;
                                                    if(!listSumFamily[idFamily+'-'+ind]){
                                                        listSumFamily[idFamily+'-'+ind] = 0;
                                                    }
                                                    listSumFamily[idFamily+'-'+ind] += _value; 
                                                    if(!totalFamily[idFamily]){
                                                        totalFamily[idFamily] = 0;
                                                    }
                                                    totalFamily[idFamily] += _value;                                               
                                                });
                                            });
                                            valTaskDisplay += '</tr>';
											
                                        });
                                    } else if(idGlobal[0] === 'pr'){
                                        var projectName = datas.ListProjects[idGlobal[1]] ? datas.ListProjects[idGlobal[1]] : '';
                                        listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">&nbsp;'+ sttActivity +'. ' + projectName + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
                                        valTaskDisplay += '<tr class="project-activity-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
                                        var sttTask = 0;
										
                                        $.each(value, function(idTask, valTask){
                                            sttTask++;
                                            valTaskDisplay += '<tr >';
                                            var idPriority = datas.PriorityProjectTasks[idTask] ? datas.PriorityProjectTasks[idTask] : 0;
                                            var priorities = datas.ProjectPriorities[idPriority] ? datas.ProjectPriorities[idPriority] : '';
                                            var projectTaskName = datas.NameProjectTasks[idTask] ? datas.NameProjectTasks[idTask] : '';
                                            listTaskDisplay += '<tr ><td class="list-task"><div>&nbsp;'+ sttActivity +'.'+ sttTask +'. ' + projectTaskName + '</div></td><td><div>' + priorities + '</div></td><td><div>&nbsp;</div></td></tr>';
                                            $.each(datas.MonthVocations, function(index, values){
                                                $.each(values, function(ind, val){
                                                    ind = index+'-'+ind;
                                                    var _value = valTask[ind] ? valTask[ind] : 0;
                                                    valTaskDisplay += '<td><div>' + _value + '</div></td>';
                                                    if(!totalWorkload[ind]){
                                                        totalWorkload[ind] = 0;
                                                    }
                                                    totalWorkload[ind] += _value;
                                                    if(!listSumFamily[idFamily+'-'+ind]){
                                                        listSumFamily[idFamily+'-'+ind] = 0;
                                                    }
                                                    listSumFamily[idFamily+'-'+ind] += _value;
                                                    if(!totalFamily[idFamily]){
                                                        totalFamily[idFamily] = 0;
                                                    }
                                                    totalFamily[idFamily] += _value;
                                                });
                                            });
                                            valTaskDisplay += '</tr>';
											
                                        });
                                    } else {
                                        var activityName = datas.ListActivities[idGlobal[1]] ? datas.ListActivities[idGlobal[1]] : '';
                                        listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">&nbsp;'+ sttActivity +'. ' + activityName + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
                                        valTaskDisplay += '<tr class="project-activity-group">';
                                        $.each(datas.MonthVocations, function(index, values){
                                            $.each(values, function(ind, val){
                                                ind = index+'-'+ind;
                                                var _value = value[ind] ? value[ind] : 0;
                                                valTaskDisplay += '<td><div>' + _value + '</div></td>';
                                                if(!totalWorkload[ind]){
                                                    totalWorkload[ind] = 0;
                                                }
                                                totalWorkload[ind] += _value;
                                                if(!listSumFamily[idFamily+'-'+ind]){
                                                    listSumFamily[idFamily+'-'+ind] = 0;
                                                }
                                                listSumFamily[idFamily+'-'+ind] += _value;
                                                if(!totalFamily[idFamily]){
                                                    totalFamily[idFamily] = 0;
                                                }
                                                totalFamily[idFamily] += _value;
                                            });
                                        });
                                        valTaskDisplay += '</tr>';
										
                                    }
                                });
                            });
                        }
                        var totalWorkloads = totalAvais = 0;
                        $('#total-workload-popup').find('td div').each(function(){
                            var getId = $(this).attr('id');
                            var getTotalWl = totalWorkload[getId] ? totalWorkload[getId].toFixed(2) : 0;
                            totalWorkloads += parseFloat(getTotalWl);
                            $('#'+getId).html(getTotalWl);
                            var getCapacity = $('#capacity-'+getId).html();
                            var getAvais = parseFloat(getCapacity) - parseFloat(getTotalWl);
                            //if(getAvais < 0){getAvais = 0;}
                            totalAvais += parseFloat(getAvais);
                            if (getAvais.toString().indexOf('.') != -1){
                                getAvais = getAvais.toFixed(2);
                            }
							var getOver = 0;
							if(parseFloat(getAvais)<0){
								getOver = parseFloat(getAvais)*(-1);
								getAvais = 0;
							}else
							{
								getOver = 0;
							}
                            $('#total-avai-popup').find('#avai-'+getId).html(getAvais);
                            $('#total-over-popup').find('#over-'+getId).html(getOver);
                        });
                        totalWorkloads = totalWorkloads.toFixed(2);   
                        totalAvais = totalAvais.toFixed(2);
                        if(totalAvais < 0){
							totalOverload = totalAvais*(-1);
                            totalAvais = 0;
                        }else{
                            totalOverload = 0;
                        }                    
                        $('#total-workingDay').html(totalWorkingDay);
                        $('#total-dayOff').html(totalDayOff);
                        $('#total-capacity').html(totalCapacity);
                        $('#total-workload').html(totalWorkloads);
                        $('#total-availability, .gs-header-content-avai span').html(totalAvais);
						$('#total-overload, .gs-header-content-over span').html(totalOverload);
                                              
                        $(".popup-task-detail").html(listTaskDisplay);
                        $(".popup-task-detail-2").html(valTaskDisplay);
                        
                        $('.popup-task-detail-2').find('.family-group td div').each(function(){
                            var idDivOfFamily = $(this).attr('id');
                            var idCheck = idDivOfFamily.replace('fam-', '');
                            var valSumFam = listSumFamily[idCheck] ? listSumFamily[idCheck].toFixed(2) : 0;
                            $('#'+idDivOfFamily).html(valSumFam);
                        });
                        $('.popup-task-detail').find('td.ch-fam div').each(function(){
                            var idDivOfFamily = $(this).attr('id');
                            var idCheck = idDivOfFamily.replace('total-fam-', '');
                            var valSumFam = totalFamily[idCheck] ? totalFamily[idCheck].toFixed(2) : 0;
                            $('#'+idDivOfFamily).css('text-align', 'right');
                            $('#'+idDivOfFamily).html(valSumFam);
                        });
                    }
                    //init();
                    initMonth();
                    
                    //filter
                    $("#filter_year").click(function(e){
                        $('#filter_month').removeClass('ch-current');
                        $(this).addClass('ch-current');
                        var headers = working = dayOff = capacity = avais = workload = overload = '';
                        var totalCount = totalWorkingDay = totalDayOff = totalCapacity = totalOverload = 0;
                        widthDivRight = 0;
                        if(datas.YearVocations){
                            headers += '<tr class="popup-header">';
                            working += '<tr id="total-working-popup">';
                            dayOff += '<tr id="total-dayOff-popup">';
                            capacity += '<tr id="total-capacity-popup">';
                            workload += '<tr id="total-workload-popup">';
                            avais += '<tr id="total-avai-popup">';
							overload += '<tr id="total-over-popup">';
                            $.each(datas.YearVocations, function(index, values){
                                totalCount++;
                                totalDayOff += parseFloat(values);
                                var theWorkingYear = datas.YearWorkingDays[index] ?  datas.YearWorkingDays[index] : 0;
                                totalWorkingDay += parseFloat(theWorkingYear);
                                headers += '<td class="text-center">' + index + '</td>';
                                working += '<td><div id="working-' + index + '">' + theWorkingYear + '</div></td>';
                                dayOff += '<td><div id="dayOff-' + index + '">' + values + '</div></td>';
                                var _capa = parseFloat(theWorkingYear) - parseFloat(values);
                                totalCapacity += parseFloat(_capa);
                                capacity += '<td><div id="capacity-' + index + '">' + _capa + '</div></td>';
                                workload += '<td><div id="' + index + '">' + 0 + '</div></td>';
                                avais += '<td><div id="avai-' + index + '">' + 0 + '</div></td>';
								overload += '<td><div id="over-' + index + '">' + 0 + '</div></td>';
                                widthDivRight += 50;
                            });
                            headers += '</tr>';
                            working += '</tr>';
                            dayOff += '</tr>';
                            capacity += '</tr>';
                            workload += '</tr>';
                            avais += '</tr>';
							overload += '</tr>';
                        }
                        $(".popup-header-2").html(headers);
                        $(".popup-working-2").html(working);
                        $(".popup-dayOff-2").html(dayOff);
                        $(".popup-capacity-2").html(capacity);
                        $(".popup-workload-2").html(workload);
                        $(".popup-availa-2").html(avais);
						$(".popup-over-2").html(overload);
                        
                        // phan detail cua task
                        var listTaskDisplay = '';
                        var valTaskDisplay = '';
                        var totalWorkload = [];
                        var listSumFamily = [];
                        var totalFamily = [];
                        if(datas.listYearDatas){
                            $.each(datas.listYearDatas, function(idFamily, values){
                                var familyName = datas.families[idFamily] ? datas.families[idFamily] : '';
                                listTaskDisplay += '<tr class="family-group"><td><div style="font-weight: bold;">&nbsp;' + familyName + '</div></td><td><div>&nbsp;</div></td><td class="ch-fam"><div id="total-fam-'+idFamily+'">&nbsp;</div></td></tr>';
                                //valTaskDisplay += '<tr class="family-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
                                valTaskDisplay += '<tr class="family-group">';
                                $.each(datas.YearVocations, function(index, values){
                                    valTaskDisplay += '<td><div id="fam-'+idFamily+'-'+index+'">&nbsp;</div></td>';
                                });
                                valTaskDisplay += '</tr>';
								
                                var sttActivity = 0;
                                $.each(values, function(idGlobal, value){
                                    sttActivity++;
                                    idGlobal = idGlobal.split('-');
                                    if(idGlobal[0] === 'ac'){
                                        var activityName = datas.ListActivities[idGlobal[1]] ? datas.ListActivities[idGlobal[1]] : '';
                                        listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">&nbsp;'+ sttActivity +'. ' + activityName + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
                                        valTaskDisplay += '<tr class="project-activity-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
                                        var sttTask = 0;
										
                                        $.each(value, function(idTask, valTask){
                                            sttTask++;
                                            valTaskDisplay += '<tr >';
                                            var idPriority = datas.PriorityActivityTasks[idTask] ? datas.PriorityActivityTasks[idTask] : 0;
                                            var priorities = datas.ProjectPriorities[idPriority] ? datas.ProjectPriorities[idPriority] : '';
                                            var activityTaskName = datas.NameActivityTasks[idTask] ? datas.NameActivityTasks[idTask] : '';
                                            listTaskDisplay += '<tr ><td class="list-task"><div>&nbsp;'+ sttActivity +'.'+ sttTask +'. ' + activityTaskName + '</div></td><td><div>' + priorities + '</div></td><td><div>&nbsp;</div></td></tr>';
                                            $.each(datas.YearVocations, function(index, values){
                                                var _value = valTask[index] ? valTask[index] : 0;
                                                valTaskDisplay += '<td><div>' + _value + '</div></td>';
                                                if(!totalWorkload[index]){
                                                    totalWorkload[index] = 0;
                                                }
                                                totalWorkload[index] += _value;
                                                if(!listSumFamily[idFamily+'-'+index]){
                                                    listSumFamily[idFamily+'-'+index] = 0;
                                                }
                                                listSumFamily[idFamily+'-'+index] += _value; 
                                                
                                                if(!totalFamily[idFamily]){
                                                    totalFamily[idFamily] = 0;
                                                }
                                                totalFamily[idFamily] += _value;
                                            });
                                            valTaskDisplay += '</tr>';
											
                                        });
                                    } else if(idGlobal[0] === 'pr'){
                                        var projectName = datas.ListProjects[idGlobal[1]] ? datas.ListProjects[idGlobal[1]] : '';
                                        listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">&nbsp;'+ sttActivity +'. ' + projectName + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
                                        valTaskDisplay += '<tr class="project-activity-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
                                        var sttTask = 0;
										
                                        $.each(value, function(idTask, valTask){
                                            sttTask++;
                                            valTaskDisplay += '<tr >';
                                            var idPriority = datas.PriorityProjectTasks[idTask] ? datas.PriorityProjectTasks[idTask] : 0;
                                            var priorities = datas.ProjectPriorities[idPriority] ? datas.ProjectPriorities[idPriority] : '';
                                            var projectTaskName = datas.NameProjectTasks[idTask] ? datas.NameProjectTasks[idTask] : '';
                                            listTaskDisplay += '<tr ><td class="list-task"><div>&nbsp;'+ sttActivity +'.'+ sttTask +'. ' + projectTaskName + '</div></td><td><div>' + priorities + '</div></td><td><div>&nbsp;</div></td></tr>';
                                            $.each(datas.YearVocations, function(index, values){
                                                var _value = valTask[index] ? valTask[index] : 0;
                                                valTaskDisplay += '<td><div>' + _value + '</div></td>';
                                                if(!totalWorkload[index]){
                                                    totalWorkload[index] = 0;
                                                }
                                                totalWorkload[index] += _value;
                                                if(!listSumFamily[idFamily+'-'+index]){
                                                    listSumFamily[idFamily+'-'+index] = 0;
                                                }
                                                listSumFamily[idFamily+'-'+index] += _value; 
                                                
                                                if(!totalFamily[idFamily]){
                                                    totalFamily[idFamily] = 0;
                                                }
                                                totalFamily[idFamily] += _value;
                                            });
                                            valTaskDisplay += '</tr>';
											
                                        });
                                    } else {
                                        var activityName = datas.ListActivities[idGlobal[1]] ? datas.ListActivities[idGlobal[1]] : '';
                                        listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">&nbsp;'+ sttActivity +'. ' + activityName + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
                                        valTaskDisplay += '<tr class="project-activity-group">';
                                        $.each(datas.YearVocations, function(index, values){
                                            var _value = value[index] ? value[index] : 0;
                                            valTaskDisplay += '<td><div>' + _value + '</div></td>';
                                            if(!totalWorkload[index]){
                                                totalWorkload[index] = 0;
                                            }
                                            totalWorkload[index] += _value;
                                            if(!listSumFamily[idFamily+'-'+index]){
                                                listSumFamily[idFamily+'-'+index] = 0;
                                            }
                                            listSumFamily[idFamily+'-'+index] += _value; 
                                            
                                            if(!totalFamily[idFamily]){
                                                totalFamily[idFamily] = 0;
                                            }
                                            totalFamily[idFamily] += _value;
                                        });
                                        valTaskDisplay += '</tr>';
										
                                    }
                                });
                            });
                        }
                        var totalWorkloads = totalAvais = 0;
                        $('#total-workload-popup').find('td div').each(function(){
                            var getId = $(this).attr('id');
                            var getTotalWl = totalWorkload[getId] ? totalWorkload[getId].toFixed(2) : 0;
                            totalWorkloads += parseFloat(getTotalWl);
                            $('#'+getId).html(getTotalWl);
                            var getCapacity = $('#capacity-'+getId).html();
                            var getAvais = parseFloat(getCapacity) - parseFloat(getTotalWl);
                            //if(getAvais < 0){getAvais = 0;}
                            totalAvais += parseFloat(getAvais);
                            if (getAvais.toString().indexOf('.') != -1){
                                getAvais = getAvais.toFixed(2);
                            }
							var getOver = 0;
							if(parseFloat(getAvais)<0){
								getOver = parseFloat(getAvais)*(-1);
								getAvais = 0;
							}else
							{
								getOver = 0;
							}
                            $('#total-avai-popup').find('#avai-'+getId).html(getAvais);
                            $('#total-over-popup').find('#over-'+getId).html(getOver);
                        });
                        totalWorkloads = totalWorkloads.toFixed(2);   
                        totalAvais = totalAvais.toFixed(2);
                        if(totalAvais < 0){
							totalOverload = totalAvais*(-1);
                            totalAvais = 0;
                        }
						else{
							totalOverload = 0;
						}
                        $('#total-workingDay').html(totalWorkingDay);
                        $('#total-dayOff').html(totalDayOff);
                        $('#total-capacity').html(totalCapacity);
                        $('#total-workload').html(totalWorkloads);
                        $('#total-availability, .gs-header-content-avai span').html(totalAvais);
						$('#total-overload, .gs-header-content-over span').html(totalOverload);
                        
                        $(".popup-task-detail").html(listTaskDisplay);
                        $(".popup-task-detail-2").html(valTaskDisplay);
                        
                        $('.popup-task-detail-2').find('.family-group td div').each(function(){
                            var idDivOfFamily = $(this).attr('id');
                            var idCheck = idDivOfFamily.replace('fam-', '');
                            var valSumFam = listSumFamily[idCheck] ? listSumFamily[idCheck].toFixed(2) : 0;
                            $('#'+idDivOfFamily).html(valSumFam);
                        });
                        $('.popup-task-detail').find('td.ch-fam div').each(function(){
                            var idDivOfFamily = $(this).attr('id');
                            var idCheck = idDivOfFamily.replace('total-fam-', '');
                            var valSumFam = totalFamily[idCheck] ? totalFamily[idCheck].toFixed(2) : 0;
                            $('#'+idDivOfFamily).css('text-align', 'right');
                            $('#'+idDivOfFamily).html(valSumFam);
                        });
                        configPopup(widthDivRight);
                        return false;
                    });
                    $("#filter_month").click(function(e){
                        //filter year
                        $('#filter_year').removeClass('ch-current');
                        initMonth();
                        configPopup(widthDivRight);
                        return false;
                    });
                    $("#filter_week").click(function(e){
                        //filter year
                        
                        return false;
                    });
                    $("#filter_date").click(function(e){
                        //filter year
                        init();
                        configPopup(widthDivRight);
                        return false;
                    });
                    // config cho phan hien thi popup
                    function configPopup(withRight){
                        var lWidth = $(window).width();
                        var DialogFull = Math.round((95*lWidth)/100);
                        var header = Math.round((93*lWidth)/100);
                        var marginTile = Math.round((22*lWidth)/100);
                        var tableLeft = Math.round((35*lWidth)/100);
                        var tableRight = Math.round((56.7*lWidth)/100);
                        var tableRightContent = Math.round((70*lWidth)/100);
                        if(withRight <= tableRightContent){
                            tableRightContent = withRight;
                        }
                        $('#gs-popup-header, #gs-popup-content').width(header);
                        $('.gs-name-header').css('margin-left', marginTile);
                        $('.table-left').width(tableLeft);
                        $('.table-right').width(tableRight);
                        $('#tb-popup-content-2').width(tableRightContent);
                        //
                        
                        var lHeight =  $(window).height();
                        var DialogFullHeight = Math.round((80*lHeight)/100);
                        var heightDetail = Math.round((48*lHeight)/100);
                        $('#gs-popup-content').height(heightDetail);
                        $( "#showdetail" ).dialog({
                            modal: true,
                            width: DialogFull,
                            height: DialogFullHeight,
                            zIndex: 9999999
                        });
						//HOVER ROW
						$('#tb-popup-content-2 tr').hover(function(){
							var index=this.rowIndex;
							if(this.parentNode.className=='popup-header')
								return false;
							if(index == ''||index == 0||index == 1)
								return false;
							if($('#filter_year').hasClass('ch-current'))
							{
								//do nothing
							}
							else
							{
								index=index-1;
							}
							
							var elm=document.getElementById("tb-popup-content").rows[index];
							elm.className+=" highlight";
							this.className+=" highlight";
						});
						$('#tb-popup-content-2 tr').mouseleave(function(){
							var index=this.rowIndex;
							if(this.parentNode.className=='popup-header')
								return false;
							if(index == ''||index == 0||index == 1)
								return false;
							if($('#filter_year').hasClass('ch-current'))
							{
								//do nothing
							}
							else
							{
								index=index-1;
							}
							var elm=document.getElementById("tb-popup-content").rows[index];
							elm.className=elm.className.split('highlight').join(" ");
							this.className=this.className.split('highlight').join(" ");
						});
						$('#tb-popup-content tr').hover(function(){
							var index=this.rowIndex;
							if(index == ''||index == 0)
								return false;
							if($('#filter_year').hasClass('ch-current'))
							{
								//do nothing
							}
							else
							{
								index=index+1;
							}
							
							var elm=document.getElementById("tb-popup-content-2").rows[index];
							//elm.addClass('highlight');
							elm.className+=" highlight";
							this.className+=" highlight";
						});
						$('#tb-popup-content tr').mouseleave(function(){
							var index=this.rowIndex;
							if(index == ''||index == 0)
								return false;
							if($('#filter_year').hasClass('ch-current'))
							{
								//do nothing
							}
							else
							{
								index=index+1;
							}
							
							var elm=document.getElementById("tb-popup-content-2").rows[index];
							//elm.addClass('highlight');
							elm.className=elm.className.split('highlight').join(" ");
							this.className=this.className.split('highlight').join(" ");
						});
                    }
					//END
                    configPopup(widthDivRight);
					configSizeScroll();
                });
                }
            }
       
            var pressNumber = function(e){
                var key = e.keyCode ? e.keyCode : e.which;
                if(!key || key == 8 || key == 13){
                    return;
                }
                var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
                if(val != '0' && !regNum.test(val)){
                    e.preventDefault();
                    return false;
                }
            }
            /**
             * Attach grid element event
             *
             */
            var canModified = '<?php echo true; ?>';
            if(canModified){
            
                $('#dialog_import_CSV').dialog({
                    position    :'center',
                    autoOpen    : false,
                    autoHeight  : true,
                    modal       : true,
                    width       : 360,
                    height      : 125
                });
            
                $("#import_CSV").show().click(function(){
                    $("input[name='FileField[csv_file_attachment]']").val("");
                    $(".error-message").remove();
                    $("input[name='FileField[csv_file_attachment]']").removeClass("form-error");
                    $('#dialog_import_CSV').dialog("open");
                });
                $("#import-submit").click(function(){
                    $(".error-message").remove();
                    $("input[name='FileField[csv_file_attachment]']").removeClass("form-error");
                    if($("input[name='FileField[csv_file_attachment]']").val()){
                        var filename = $("input[name='FileField[csv_file_attachment]']").val();
                        var valid_extensions = /(\.csv)$/i;   
                        if(valid_extensions.test(filename)){ 
                            $('#uploadForm').submit();
                        }
                        else{
                            $("input[name='FileField[csv_file_attachment]']").addClass("form-error");
                            jQuery('<div>', {
                                'class': 'error-message',
                                text: 'Incorrect type file'
                            }).appendTo('#error');
                        }
                    }else{
                        jQuery('<div>', {
                            'class': 'error-message',
                            text: 'Please choose a file!'
                        }).appendTo('#error');
                    }
                });
            }
            
            $(".cancel").live('click',function(){
                $("#dialog_data_CSV").dialog("close");
                $("#dialog_import_CSV").dialog("close");
            });
        
            GanttCallback = function($list, $gantt){
                $gantt.find('.gantt-input').each(function(){
                    var  $element =  $(this);
                    //var val = toFloat($element.html() , true);
                    var val = $element.html();
                    var $input = $('<input type="text" maxlength="5" value="'+ val +'" />').prop('readonly' , canModified);
                    $element.html($input);
                    $input.data('_value' , val);
                    $input.change(function(){
                        updateElement.call($input,$element.parent());
                    }).keypress(pressNumber).focus(function(){
                        $(this).select();
                    });
                    if(val != na && val > 0){
                        //$element.addClass('gantt-unzero');
                    }
                });
                attachDialog($list);
            };
        });
        $(function () {
			$("#scrollTopAbsence").scroll(function () {
				$(".table-right").scrollLeft($("#scrollTopAbsence").scrollLeft());
			});
			$(".table-right").scroll(function () {
				$("#scrollTopAbsence").scrollLeft($(".table-right").scrollLeft());
			});
		});
		function configSizeScroll(){
			$("#scrollTopAbsenceContent").width($("#tb-popup-content-2").width());
			$("#scrollTopAbsence").width($(".table-right").width());
			$('#tb-popup-content tr').each(function() {
				var index=this.rowIndex;
				if(index == ''||index == 0)
				{
				}
				else
					{
					if($('#filter_year').hasClass('ch-current'))
					{
						//do nothing
					}
					else
					{
						index=index+1;
					}
					var  h = $(this).height();
					var elm=document.getElementById("tb-popup-content-2").rows[index];
					$(elm).height(h);
				}
				
			});
		}
        var _activityId = '<?php echo $activity_id; ?>';
        $('#CategoryCategory').change(function(){
            $('#CategoryCategory option').each(function(){
                if($(this).is(':selected')){
                    var id = $('#CategoryCategory').val();
                    window.location = ('<?php echo $html->url('/') ?>activity_tasks/visions/'+_activityId+'/'+id);
                }
            });
        });  
		setTimeout(function(){
			$(window).resize();
		},1000);
        var mgLeft = $('#mcs_container').find('.container').width();
       $('.gantt-chart-1').css('margin-left', mgLeft-3);
    	
    })(jQuery);
</script>
<div id="overlay-container">
    <div id="overlay-wrapper"></div>
    <div id="overlay-box">
        Please wait, Preparing export ...
    </div>
</div>
<div id="showdetail">
    <div id="gs-popup-header">
        <div class="gs-header-title">
            <ul>
                <!--li><a href="" id="filter_date"><?php echo __("Date", true)?></a></li-->
                <!--li><a href="" id="filter_week"><?php echo __("Week", true)?></a></li-->
                <li><a href="" id="filter_month"><?php echo __("Month", true)?></a></li>
                <li><a href="" id="filter_year"><?php echo __("Year", true)?></a></li>
            </ul>
            <p class="gs-name-header"><?php __('Availability');?> : <span>ten employee</span></p>
        </div>
        <br clear="all"  />
        <div class="gs-header-content">
            <p class="gs-header-content-start"><?php __('Start Date');?> : <span>ten start</span></p>
            <p class="gs-header-content-end"><?php __('End Date');?> : <span>ten end</span></p>
            <p class="gs-header-content-avai"><?php __('Availability');?> : <span>ten avai</span></p>
            <p class="gs-header-content-over"><?php __('Overload');?> : <span>overload</span></p>
        </div>
    </div>
    <div id="scrollTopAbsence" class="useLeftScroll"><div id="scrollTopAbsenceContent"></div></div>
        <br clear="all"  />
    <div id="gs-popup-content">
        <div class="table-left">
            <table id="tb-popup-content">
                <tr class="popup-header">
                    <td style="width: 450px;">&nbsp;</td>
                    <td style="width: 90px;"><div class="text-center"><?php __('Priority');?></div></td>
                    <td style="width: 60px;"><div class="text-center"><?php __('Total');?></div></td>
                </tr>
                <tr >
                    <td class="popup-header-group popup-header-group-working-day"><div><?php __('Working Day');?></div></td>
                    <td>&nbsp;</td>
                    <td id="total-workingDay" class="text-right">&nbsp;</td>
                </tr>
                <tr >
                    <td class="popup-header-group popup-header-group-day-off"><div><?php __('Day Off');?></div></td>
                    <td>&nbsp;</td>
                    <td id="total-dayOff" class="text-right">&nbsp;</td>
                </tr>
                <tr >
                    <td class="popup-header-group popup-header-group-capacity"><div><?php __('Capacity');?></div></td>
                    <td>&nbsp;</td>
                    <td id="total-capacity" class="text-right">&nbsp;</td>
                </tr>
                <tr >
                    <td class="popup-header-group popup-header-group-workload"><div><?php __('Workload');?></div></td>
                    <td>&nbsp;</td>
                    <td id="total-workload" class="text-right">&nbsp;</td>
                </tr>
                
                <tr >
                    <td class="popup-header-group popup-header-group-availability"><div><?php __('Availability');?></div></td>
                    <td>&nbsp;</td>
                    <td id="total-availability" class="text-right">&nbsp;</td>
                </tr>
                
                <tr >
                    <td class="popup-header-group popup-header-group-overload"><div><?php __('Overload');?></div></td>
                    <td>&nbsp;</td>
                    <td id="total-overload" class="text-right">&nbsp;</td>
                </tr>
                
                <tbody class="popup-task-detail">
                    
                </tbody>
            </table>
        </div>
        <div class="table-right">
            <table id="tb-popup-content-2">
                <tbody class="popup-header-2">
                    
                </tbody>
                <tbody class="popup-working-2">
                    
                </tbody>
                <tbody class="popup-dayOff-2">
                    
                </tbody>
                <tbody class="popup-capacity-2">
                    
                </tbody>
                <tbody class="popup-workload-2">
                    
                </tbody>
                
                <tbody class="popup-availa-2">
                    
                </tbody>
                
                <tbody class="popup-over-2">
                    
                </tbody>
                
                <tbody class="popup-task-detail-2">
                    
                </tbody>
            </table>
        </div>
    </div>
</div>