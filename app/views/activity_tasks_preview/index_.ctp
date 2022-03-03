<?php echo $html->css(array('projects','project_task')); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
<!--[if lt IE 8]>
    <style type='text/css'>
        #wd-table{
            height: 300px;
            overflow: auto   
        }
    </style>
<![endif]-->
<div id="loading-mask"></div>
<div id="loading">
  <div class="loading-indicator">
  </div>
</div>
<!-- export excel  -->
<fieldset style="display: none;">
    <?php
    if(!$activityName['Activity']['pms']){
        echo $this->Form->create('Export', array(
            'id' => 'export-item-list',
            'type' => 'POST',
            'url' => array('controller' => 'activity_tasks', 'action' => 'export')));
        echo $this->Form->input('list', array('type' => 'text', 'value' =>$activity_id));
        echo $this->Form->end();
    }else{
        echo $this->Form->create('Export', array(
        'type' => 'POST',
        'id' => 'export-item-list',
        'url' => array('controller' => 'project_tasks', 'action' => 'exportExcel',$project_id)));
        echo $this->Form->input('list', array('type' => 'text', 'value' => 'Activity', ));
        echo $this->Form->end();
    }
    ?>
</fieldset>
<!-- /export excel  -->
<!-- dialog_import -->
<div id="dialog_import_CSV" title="Import CSV file" class="buttons">
    <?php
    echo $this->Form->create('Import', array('id' => 'uploadForm', 'type' => 'file', 'target' => '_blank',
        'url' => array('controller' => 'activity_tasks', 'action' => 'import_csv', $activityName['Activity']['id'])));
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
                    <h2 class="wd-t1"><?php echo sprintf(__("Activity Tasks for %s", true), $activityName['Activity']['name']); ?></h2>	
                    <a href="javascript:void(0)" class="import-excel-icon-all" id="import_CSV" style="margin-right:5px; " title="<?php __('Import CSV')?>"><span><?php __('Import CSV') ?></span></a>
                    <a href="javascript:void(0);" class="export-excel-icon-all" id="export-submit" style="margin-right:5px; "><span><?php __('Export Excel') ?></span></a>	
                    <a href="<?php echo $this->Html->url(array('action' => 'visions', $activityName['Activity']['id']));?>" class="wd-add-project" style="margin-right:5px; "><span><?php __('Staffing+') ?></span></a>
                </div>
                <div id="message-place">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    ?>
                </div>
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
</script>

<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Are you sure you want to delete "%s"?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<input type="hidden" value="<?php echo Configure::read('Config.language'); ?>" id="language"  />
<textarea style="display:none" id="priorityJson"><?php echo $listPrioritiesJson; ?></textarea>
<script type="text/javascript">
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
    $(".cancel").live('click',function(){
        $("#dialog_data_CSV").dialog("close");
        $("#dialog_import_CSV").dialog("close");
    });

</script>
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
        </div>
        <div class="gs-header-content">
            <p class="gs-header-content-name"><?php __('Task: ');?><span>ten task</span></p>
            <p class="gs-header-content-start"><?php __('Start Date: ');?><span>ten start</span></p>
            <p class="gs-header-content-end"><?php __('End Date: ');?><span>ten end</span></p>
            <p class="gs-header-content-work"><?php __('Workload: ');?><span>ten workload</span></p>
            <p class="gs-header-content-avai"><?php __('Availability: ');?><span>ten avai</span></p>
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
<script type="text/javascript">
    $('#export-submit').click(function(){
        $('#export-item-list').closest('form').submit();
    });
</script>


