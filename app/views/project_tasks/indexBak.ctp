<!--[if lt IE 8]>
    <style type='text/css'>
        #wd-table{
            height: 300px;
            overflow: auto   
        }
    </style>
<![endif]-->

<?php
$arg = $this->passedArgs;
$arg["?"] = $this->params['url'];
unset($arg['?']['url'], $arg['?']['ext']);
$type = 'monthyear';
?>

<div id="loading-mask"></div>
<div id="loading">
  <div class="loading-indicator">
  </div>
</div>
<?php echo $html->css(array('projects','slick_grid/slick.edit','gantt','project_task')); ?>
<?php echo $html->script('slick_grid/lib/jquery-ui-1.8.16.custom.min'); ?>
<!--[if lt IE 9]>
    <style type='text/css'>
       .gantt-line-s:hover,.gantt-line-n:hover{ 
            filter: progid:DXImageTransform.Microsoft.Shadow(color=#333333, direction='180', strength='3');
            border-collapse: separate !important;
        }
    </style>
<![endif]-->
<script type="text/javascript">
    //HistoryFilter.here =  '<?php //echo $this->params['url']['url'] ?>';
    //HistoryFilter.url =  '<?php ///echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
    .slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
    .ui-dialog{font-size:11px;}.ui-widget{font-family:Arial,sans-serif;}#dialog_import_CSV label{color:#000;}.buttons ul.type_buttons{padding-right:10px!important; height: 50px !important;}.type_buttons .error-message{background-color:#FFF;clear:both;color:#D52424;display:block;width:212px;padding:5px 0 0;}.form-error{border:1px solid #D52424;color:#D52424;}
	/*MODIFY BY VINGYEN 29/05/2014*/
	.phase-order-handler{ width:50px;}
</style>
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'project_tasks', 'action' => 'export', $projectName['Project']['id'])));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<!-- /export excel  -->
<!-- dialog_import -->
<div id="dialog_import_CSV" title="Import CSV file" class="buttons">
    <?php
    echo $this->Form->create('Import', array('id' => 'uploadForm', 'type' => 'file', 'target' => '_blank',
        'url' => array('controller' => 'project_tasks', 'action' => 'import_csv', $projectName['Project']['id'])));
    ?>
    <div class="wd-input">
        <center>
            <label><?php echo __('File:') ?></label>
            <input type="file" name="FileField[csv_file_attachment]" />
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
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout" style="margin-left: 30px;">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                    <h2 class="wd-t1"><?php echo sprintf(__("%s", true), $projectName['Project']['project_name']); ?></h2>	
                    <a href="<?php echo $html->url("/project_tasks/exportExcel/" . $projectName['Project']['id']) ?>" class="export-excel-icon-all" id="export-submit" style="margin-right:5px; " title="<?php __('Export Excel')?>"><span><?php __('Export Excel') ?></span></a>	
                    <a href="javascript:void(0)" class="import-excel-icon-all" id="import_CSV" style="margin-right:5px; " title="<?php __('Import CSV')?>"><span><?php __('Import CSV') ?></span></a>
                    <a href="javascript:void(0);" class="wd-add-project" style="margin-right:5px;" id="skip_value"><span><?php __('Skip') ?></span></a>
                    <a href="<?php echo $html->url("/project_phase_plans/phase_vision/" . $projectName['Project']['id']) ?>" class="wd-add-project" style="margin-right:5px; "><span><?php __('Gantt+') ?></span></a>
                        <?php
//                        echo $this->Form->create('Display', array('type' => 'get', 'url' => array_merge(array(
//                                'controller' => 'project_tasks',
//                                'action' => 'index',
//                                $projectName['Project']['id']
//                            ))));
                        ?>
               <div id="gantt-display">
                        <ul id="listdisplay">
                            <li> <label class="title" style="float: left; padding-right: 10px;"><?php __('Display initial time'); ?> </label>
                            <?php
                            //edit by thach 2013-11-21
                            $chk = ($displayplan == 1) ? true : false;
                            $chkreal = ($displayreal == 1) ? true : false;
                            echo $this->Form->input('display', array(
                                'rel' => 'no-history',
                                'onclick' => 'removeLine(this,"n")',
                                'value' => $displayplan,
                                'label' => '',
                                'class' => 'inputcheckbox',
                                'type' => 'checkbox', 'legend' => false, 'fieldset' => false, 'checked' => $chk
                            ));?></li>
                            <li>  <label class="title" style="float: left; padding-right: 10px;"><?php __('Display real time'); ?> </label>
                            
                            <?php
 
                            echo $this->Form->input('display', array(
                                'rel' => 'no-history',
                                'onclick' => 'removeLine(this,"s")',
                                'value' => $displayreal,
                                'label' => '',
                                'class' => 'inputcheckbox',
                                'type' => 'checkbox', 'legend' => false, 'fieldset' => false ,'checked' => $chkreal
                            ));
                           
                            ?></li>
                           
                        </ul>
                  
                        </div>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
                <div id="message-place">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    ?>
                </div>
                <!-- Gantt -->
                <div id="GanttChartDIV">
                    <?php
                    $rows = 0;
                    $start = $end = 0;
                    $data = $projectId = $conditions = array();
                    foreach ($projects as $project) {
                        $_data = array(
                            'name' => $project['Project']['project_name'],
                            'phase' => array(),
                        );
                        $projectId[$project['Project']['id']] = $project['Project']['project_name'];
                        if (!empty($project['ProjectPhasePlan'])) {
                            foreach ($project['ProjectPhasePlan'] as $phace) {
                                $_phase = array(
                                    'name' => !empty($phace['ProjectPhase']['name']) ? $phace['ProjectPhase']['name'] : '',
                                    'start' => $this->Gantt->toTime($phace['phase_planed_start_date']),
                                    'end' => $this->Gantt->toTime($phace['phase_planed_end_date']),
                                    'rstart' => $this->Gantt->toTime($phace['phase_real_start_date']),
                                    'rend' => $this->Gantt->toTime($phace['phase_real_end_date']),
                                    'color' => !empty($phace['ProjectPhase']['color']) ? $phace['ProjectPhase']['color'] : '#004380'
                                );
                                if ($_phase['rstart'] > 0) {
                                    $_start = min($_phase['start'], $_phase['rstart']);
                                } else {
                                    $_start = $_phase['start'];
                                }
                                if (!$start || ($_start > 0 && $_start < $start)) {
                                    $start = $_start;
                                }
                                $_end = max($_phase['end'], $_phase['rend']);
                                if (!$end || $_end > $end) {
                                    $end = $_end;
                                }
                                $_data['phase'][] = $_phase;
                            }
                        }
                        $data[] = $_data;
                    }
                    unset($projects, $project, $_data, $_phase, $phase);
                    $summary = isset($this->params['url']['summary']) ? (bool) $this->params['url']['summary'] : false;
                    $showType = isset($this->params['url']['type']) ? (int) $this->params['url']['type'] : 0;
                    if (empty($start) || empty($end)) {
                        echo $this->Html->tag('h1', __('No data exist to create Gantt chart', true), array('style' => 'color:red'));
                    } else {
                        $this->Gantt->create($type, $start, $end, array(), false , false);
                        
                        foreach ($data as $value) {
                            $rows++;
                            if (empty($value['phase'])) {
                                $this->Gantt->drawLine(__('no data exit', true), 0, 0, 0, 0, '#ffffff', true);
                            } else {
                                foreach ($value['phase'] as $node) {
                                    $color = '#004380';
                                    if (!empty($node['color'])) {
                                        $color = $node['color'];
                                    }
                                   
                                    $this->Gantt->drawLine($node['name'], $node['start'], $node['end'], $node['rstart'], $node['rend'], $color, true);
                                }
                            }
                            $this->Gantt->drawEnd($value['name'], false);
                        }
                        $this->Gantt->end();
                    }
                    ?>
                </div>
                
                <!-- Gantt.end -->   
                <div class="wd-table" id="project_container" style="width:100%;">
                
                </div>
                <div id="pager" style="width:100%;height:36px; overflow: hidden;" class="slick-pager">
                    
                </div>
            </div>
            <?php echo $this->element('grid_status'); ?>
        </div>
    </div>
</div>
<?php echo $this->element('dialog_projects') ?>
<?php //echo $html->script('slick_grid/lib/jquery-ui-1.8.16.custom.min'); ?>
<?php //echo $html->script('slick_grid/lib/jquery.event.drag-2.0.min'); ?>
<?php //echo $html->script('slick_grid/slick.core'); ?>
<?php //echo $html->script('slick_grid/slick.dataview'); ?>
<?php //echo $html->script('slick_grid/controls/slick.pager'); ?>
<?php //echo $html->script('slick_grid/slick.formatters'); ?>
<?php //echo $html->script('slick_grid/plugins/slick.cellrangedecorator'); ?>
<?php //echo $html->script('slick_grid/plugins/slick.cellrangeselector'); ?>
<?php //echo $html->script('slick_grid/plugins/slick.cellselectionmodel'); ?>
<?php //echo $html->script('slick_grid/slick.editors'); ?>

<?php //echo $html->script(array('slick_grid_custom')); ?>

<?php
    $i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true),
    'Phase start date must between %1$s and %2$s' => __('Phase start date must between %1$s and %2$s', true),
    'This date value must between %1$s and %2$s' => __('This date value must between %1$s and %2$s', true),
    'This phase of project is exist.' => __('This phase of project is exist.', true),
    'Remain' => __('Rest à Faire', true),
    'Phase' => __('Phaase', true)
);

    $i18n = json_encode($i18n);
?>

<script>
    var translate = <?php echo $i18n?>;
    $(function(){
        $('.gantt-line-s').hide();
    })
    function removeLine(checkboxObject,type){
        if(checkboxObject.checked){
            if(type=="n"){
                $('.gantt-line-n').show();
            }
            if(type=="s"){
                 $('.gantt-line-s').show();
            }
        }else{
            if(type=="n"){
                $('.gantt-line-n').hide();
            }
            if(type=="s"){
                 $('.gantt-line-s').hide();
            }
        }
    }
      
</script>

<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Are you sure you want to delete "%s"?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<!--MODIFY BY VINGUYEN 18/06/2014-->
<div id="showdetail">
    <div id="gs-popup-header">
        <div class="gs-header-title">
            <ul>
                <li><a href="" id="filter_date"><?php echo __("Date", true)?></a></li>
                <!--li><a href="" id="filter_week"><?php echo __("Week", true)?></a></li-->
                <li><a href="" id="filter_month"><?php echo __("Month", true)?></a></li>
                <li><a href="" id="filter_year"><?php echo __("Year", true)?></a></li>
            </ul>
            <p class="gs-name-header"><?php __('Availability: ');?><span>ten employee</span></p>
        </div><br clear="all"  />
        <div class="gs-header-content">
            <p class="gs-header-content-name"><?php __('Task');?> : <span>ten task</span></p>
            <p class="gs-header-content-start"><?php __('Start Date');?> : <span>ten start</span></p>
            <p class="gs-header-content-end"><?php __('End Date');?> : <span>ten end</span></p>
            <p class="gs-header-content-work"><?php __('Workload');?> : <span>ten workload</span></p>
            <p class="gs-header-content-avai"><?php __('Availability');?> : <span>ten avai</span></p>
            <p class="gs-header-content-over"><?php __('Overload');?> : <span>ten over</span></p>
        </div>
    </div>
    <div id="gs-popup-content">
        <div class="table-left">
            <table id="tb-popup-content">
                <tr class="popup-header">
                    <td style="width: 450px;">&nbsp;</td>
                    <td style="width: 90px;"><div class="text-center"><?php __('Priority');?></div></td>
                    <td style="width: 60px;"><div class="text-center"><?php __('Total');?></div></td>
                </tr>
                <tr>
                    <td class="popup-header-group"><div><?php __('Availability');?></div></td>
                    <td>&nbsp;</td>
                    <td id="total-availability" class="text-right">&nbsp;</td>
                </tr>
                <tr>
                    <td class="popup-header-group"><div><?php __('Overload');?> </div></td>
                    <td>&nbsp;</td>
                    <td id="total-overload" class="text-right">&nbsp;</td>
                </tr>
                <tr>
                    <td class="popup-header-group"><div><?php __('Vacation/Day Off');?></div></td>
                    <td>&nbsp;</td>
                    <td id="total-vacation" class="text-right">&nbsp;</td>
                </tr>
                <tr>
                    <td class="popup-header-group"><div><?php __('Workload');?></div></td>
                    <td>&nbsp;</td>
                    <td id="total-workload" class="text-right">&nbsp;</td>
                </tr>
                
                <tbody class="popup-task-detail">
                    
                </tbody>
            </table>
        </div>
        <div class="table-right">
            <table id="tb-popup-content-2">
                <tbody class="popup-header-2">
                    
                </tbody>
                <tbody class="popup-availa-2">
                    
                </tbody>
                <tbody class="popup-over-2">
                    
                </tbody>
                <tbody class="popup-vaication-2">
                    
                </tbody>
                <tbody class="popup-workload-2">
                    
                </tbody>
                
                <tbody class="popup-task-detail-2">
                    
                </tbody>
            </table>
        </div>
    </div>
</div>
<!--END-->
<!-- dialog_skip_value -->
<div id="dialog_skip_value" class="buttons" style="display: none;">
    <fieldset>
        <?php 
        echo $this->Form->create('Skip'); ?>
        <div style="height:auto;" class="wd-scroll-form">
            <div class="wd-input">
                <label for="url"><?php __("Value") ?></label>
                <?php
                echo $this->Form->input('value', array('type' => 'text',
                    'label' => false,
                    'maxlength' => 3,
                    'rel' => 'no-history'));
                ?> 
                <p id="iz-error" style="width: 150px; margin-left: 136px; color: red; font-size: 15px; font-style: italic; padding-top: 35px;"></p>
            </div>
        </div>
        <?php
        echo $this->Form->end();
        ?>  
    </fieldset>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important; padding-bottom: 30px !important;">
        <li><a href="javascript:void(0)" class="cancel"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_skip"><?php __('OK') ?></a></li>
    </ul>
</div>
<!-- dialog_attachement_or_url.end -->
<!-- dialog_loader --->
<div id="gs_loader" style="display: none;">
    <div class="gs_loader">
        <p>Please wait, Skip value...</p>
    </div>
</div>
<!-- dialog_loader.end --->
<script type="text/javascript">
    $("#btnClose").click(function(){
        $("#showdetail").hide();
    });
    var heightTable = $(window).height() - 450;
    heightTable = (heightTable < 400) ? 400 : heightTable;
    $('.wd-table').css({
        height: heightTable,
    }); 
    $(window).resize(function(){
        $('.wd-table').css({
            height: heightTable
        }); 
        var heightTree = $(window).height() - 510;
        heightTree = (heightTree < 400) ? 400 : heightTree;
        $('#treeview-1023').css({
            height: heightTree,
            overflow: "auto",
        });
        var heightBody = $(window).height() - 470;
        heightBody = (heightBody < 400) ? 400 : heightBody;
        $('#main-body').css({
            height: heightBody
        });
        $('#pmstreepanel').css({
            height: heightBody
        });
        $('#pmstreepanel-body').css({
            height: heightBody
        });
    });
    $('#dialog_import_CSV').dialog({
        position    :'center',
        autoOpen    : false,
        autoHeight  : true,
        modal       : true,
        width       : 360,
        height      : 150
    });
    $('#dialog_data_CSV').dialog({
        position    :'top',
        autoOpen    : false,
        autoHeight  : true,
        modal       : true,
        minHeight   : 102,
        width       : 760
        //auto  : true
        // height      : 230
    });
    $("#import_CSV").click(function(){
        $('.wd-input').show();
        $('#loading').hide();
        $("input[name='FileField[csv_file_attachment]']").val("");
        $(".error-message").remove();
        $("input[name='FileField[csv_file_attachment]']").removeClass("form-error");
        $(".type_buttons").show();
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
            $("#dialog_import_CSV").dialog("close");
        }else{
            jQuery('<div>', {
                'class': 'error-message',
                text: 'Please choose a file!'
            }).appendTo('#error');
        }
    });
    /* table .end */
    var createDialog = function(){
        $('#dialog_skip_value').dialog({
            position    :'center',
            autoOpen    : false,
            autoHeight  : true,
            modal       : true,
            width       : 500,
            open : function(e){
                var $dialog = $(e.target);
                $dialog.dialog({open: $.noop});
            }
        });
        createDialog = $.noop;
    }
    $("#skip_value").live('click',function(){
        createDialog();
        $("#dialog_skip_value").dialog('option',{title:'Skip Value'}).dialog('open');
    });
    $(".cancel").live('click',function(){
        $("#dialog_skip_value").dialog('close');
        $("#dialog_data_CSV").dialog("close");
        $("#dialog_import_CSV").dialog("close");
    });
    // Chi cho phep nhap so
    $("#SkipValue").keypress(function (e) {
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            $("#iz-error").html("Only input number.").show().fadeOut("slow");
            return false;
        }
    });
    $("#ok_skip").click(function(){
        var val = $('#SkipValue').val();
        if(val == '' || !val){
            $('#iz-error').css('display', 'block');
            $('#iz-error').html('The value is not blank!');
        } else {
            var modelId = <?php echo json_encode($projectName['Project']['id']);?>;
            $.ajax({
              url: '/project_tasks/skipValue/project/' + modelId + '/' + val,
              async: false,  
              beforeSend: function(){
                $("#dialog_skip_value").dialog('close');
                $('#gs_loader').css('display', 'block');
              },
              success:function(data) {
                 result = data; 
                 $('#gs_loader').css('display', 'none');
                 var url = <?php echo json_encode($_SERVER['REQUEST_URI']);?>;
                 window.location = url;
              }
           });
        }
    });
</script>


<?php echo $this->Html->script(array(
'extjs/extjs/ext-all',
'extjs/extjs/ext-theme-neptune',
'extjs/app/app'
)); ?>

<?php echo $this->Html->css(array(
    // 'extjs/resources/css/ext-all',
    // 'extjs/resources/css/ext-neptune',
    'extjs/resources/css/ext-all-neptune',
    // 'extjs/resources/css/tasks', 
      'extjs/resources/css/ext-custom'
    )); ?>
<!-- export excel  -->