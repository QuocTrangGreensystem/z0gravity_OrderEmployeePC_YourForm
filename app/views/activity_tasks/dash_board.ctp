<?php echo $html->css('slick_grid/slick.edit'); ?>
<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<?php 
    echo $this->Html->script(array(
        //'dashboard/jquery-1.10.1.min',
        'dashboard/jqx-all',
        'dashboard/jqxchart',
        'dashboard/jqxcore',
        'dashboard/jqxdata',
        'dashboard/jqxcheckbox',
        'dashboard/jqxradiobutton',
        'dashboard/gettheme'
    )); 
?>
<?php 
    echo $this->Html->css(array(
        'dashboard/jqx.base',
        'dashboard/jqx.web'
    )); 
?>
<?php
    $arg = $this->passedArgs;
    $arg["?"] = $this->params['url'];
    unset($arg['?']['url'], $arg['?']['ext']);
?>
<!-- export excel  -->
<fieldset style="display: none;">
    <?php
    //echo $this->Form->create('Export', array(
//        'type' => 'POST',
//        'url' => array('controller' => 'project_tasks', 'action' => 'export', $projectName['Project']['id'])));
//    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
//    echo $this->Form->end();
    ?>
</fieldset>
<!-- /export excel  -->
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout" style="margin-left: 0px;">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"><div class="wd-panel">
            <div class="wd-list-project">
                <div class="wd-title">
                    <h2 class="wd-t1"><?php echo sprintf(__("Dash Board of %s", true), $activityName['Activity']['name']); ?></h2>	
                    <?php /*
                    <a href="<?php //echo $html->url("/project_phase_plans/phase_vision/" . $projectName['Project']['id']) ?>" class="wd-add-project" style="margin-right:5px; "><span><?php __('Gantt+') ?></span></a>
                    <a href="<?php //echo $html->url("/project_tasks/exportExcel/" . $projectName['Project']['id']) ?>" class="wd-add-project" id="export-submit" style="margin-right:5px; "><span><?php __('Export Excel') ?></span></a>	
                    */ ?>
                </div>
                <?php /*
                <div id="table-control">
                    <?php
                    echo $this->Form->create('Control', array(
                        'type' => 'get',
                        'url' => '/' . Router::normalize($this->here)));
                    ?>
                    <fieldset>
                        <h3 class="input"><?php __('You are view in :'); ?></h3>
                        <div class="input border-input">
                            <?php
                            echo $this->Form->year('year', date('Y', $_start) - 5, date('Y', $_start) + 2, date('Y', $_start), array('empty' => false));
                            ?>
                        </div>
                        <div class="input">
                            <?php
                            echo $this->Form->month('month', date('m', $_start), array('empty' => false));
                            ?>
                        </div>
                        <div class="input border-input">
                            <?php
                            if(!empty($activityName['Activity']['project']) || $activityName['Activity']['project'] != 0 || $activityName['Activity']['project'] != ''){
                                $paths = array(
                                    '1' => __("Employees", true),
                                    '2' => __("Profit Centers", true),
                                    '3' => __("Skills", true),
                                );
                            } else {
                                $paths = array(
                                    '1' => __("Employees", true),
                                    '2' => __("Profit Centers", true),
                                    //'3' => __("Skills", true),
                                );
                            }
                            echo $this->Form->select('filter', $paths, $filterId, array('empty' => false, 'escape' => false));
                            ?>
                        </div>
                        <div class="input" style="float: left;">
                             <h3 class="input"><?php __('Display :'); ?></h3>
                            <?php
                            $tmpDisplay = array(
                                '1' => __("Estimation", true),
                                '2' => __("Consumed", true),
                                '3' => __("Validated", true),
                            );
                            echo $this->Form->input('display', array(
                                'type' => 'select',
                                'name' => 'display',
                                'div' => false,
                                'multiple' => false,
                                'hiddenField' => false,
                                'label' => false,
                                'style' => 'width: 190px; !important',
                                "empty" => __("-- Any --", true),
                                "options" => $tmpDisplay));
                            ?>        							
                        </div>
                        <div class="button">
                            <input type="submit" value="OK" />
                        </div>
                        <div style="clear:both;"></div>
                    </fieldset>
                    <?php
                    echo $this->Form->end();
                    ?>
                </div>
                */?>
                <div class="wd-group-radio">
                    <div id="wd-display"><?php echo __("Display Numbers", true);?></div>
                    <div id="wd-different"><?php echo __("Display Drift", true);?></div>
                </div>
                <div id="message-place">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    ?>
                </div>
                <div class="wd-table" id="project_container" style="width:100%;height:500px;">
                
                </div>
                <?php
                /*
                    if(!empty($activityName['Activity']['project']) || $activityName['Activity']['project'] != 0 || $activityName['Activity']['project'] != ''){
                    <div class="wd-table" id="project_container_risks" style="width:100%;height:500px;">
                
                    </div>
                    }
                */
                ?>
                <div id="pager" style="width:100%;height:36px; overflow: hidden;" class="slick-pager">
                    
                </div>
            </div>
            <?php echo $this->element('grid_status'); ?>
             </div></div>
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
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
 <script type="text/javascript">
        $(document).ready(function () {
            
            var dataSets    = <?php echo json_encode($dataSets); ?>,
            years    = <?php echo json_encode($setYear); ?>,
            //paths    = <?php //echo json_encode($paths); ?>,
            filterId    = <?php echo json_encode($filterId); ?>,
            manDays    = <?php echo json_encode($manDays); ?>,
            display    = <?php echo json_encode($display); ?>,
            showEstimation = <?php echo json_encode($showEstimation); ?>,
            showConsumed = <?php echo json_encode($showConsumed); ?>,
            showValidated = <?php echo json_encode($showValidated); ?>
            ;
            // console.log(dataSets);
            $("#ControlDisplay").multiSelect({
                noneSelected: '<?php __("-- Any --"); ?>', 
                loadingClass : 'wd-disable',
                loadingText : 'Loading...',
                oneOrMoreSelected: '*', selectAll: false });
                
            /*
            if(showEstimation == true && showConsumed == true && showValidated == true){
                $('.multiSelectOptions label').find('input').attr('checked', 'checked');
                $('a.multiSelect span').html('Estimation, Consumed, Validated');
            }
            
            if(showEstimation == true && showConsumed == false && showValidated == false){
                $('a.multiSelect span').html('Estimation');
            }
            if(showEstimation == false && showConsumed == true && showValidated == false){
                $('a.multiSelect span').html('Consumed');
            }
            if(showEstimation == false && showConsumed == false && showValidated == true){
                $('a.multiSelect span').html('Validated');
            }
            
            if(showEstimation == true && showConsumed == true && showValidated == false){
                $('a.multiSelect span').html('Estimation, Consumed');
            }
            if(showEstimation == true && showConsumed == false && showValidated == true){
                $('a.multiSelect span').html('Estimation, Validated');
            }
            if(showEstimation == false && showConsumed == true && showValidated == true){
                $('a.multiSelect span').html('Consumed, Validated');
            }
            
            
            if(showEstimation == true){
                $('.multiSelectOptions label').find('input').eq(0).attr('checked', 'checked');
            }
            if(showConsumed == true){
                $('.multiSelectOptions label').find('input').eq(1).attr('checked', 'checked');
            }
            if(showValidated == true){
                $('.multiSelectOptions label').find('input').eq(2).attr('checked', 'checked');
            }
            */
            // prepare jqxChart settings
            var settings = {
                title: "<?php echo __('M.D Planed Follow Up', true);?>",
                description: years,
                padding: { left: 5, top: 5, right: 5, bottom: 5 },
                titlePadding: { left: 90, top: 0, right: 0, bottom: 10 },
                source: dataSets,
                categoryAxis:
                    {
                        dataField: 'date',
                        description: 'Time',
                        showGridLines: false
                    },
                colorScheme: 'scheme01',
                seriesGroups:
                    [
                        {
                            type: 'line',
                            showLabels: true,//default
                            valueAxis:
                            {
                                axisSize: 'auto',
                                minValue: 0,
                                maxValue: manDays,
                                unitInterval: manDays/10,
                                description: 'Man-Day',
                                displayValueAxis: true
                            },
                            series: [
                                    //{ dataField: 'estimation', displayText: 'Estimation', labelOffset: {x: 0, y: 10}},
                                    { dataField: 'consumed', displayText: '<?php echo __('Consumed', true);?>', labelOffset: {x: 0, y: 10}, color: '#AA4643'},
                                    { dataField: 'validated', displayText: '<?php echo __('Planed', true);?>', labelOffset: {x: 0, y: -10}, color: '#829A50'}
                                  
                                ]
                        },
                        {
                            type: 'rangecolumn',
                            showLabels: true,
                            showLegend: false,
                            columnsGapPercent: 100,
                            valueAxis: {
                                minValue: 0,
                                maxValue: manDays,
                                unitInterval: manDays/10,
                                description: 'Man-Day',
                                displayValueAxis: false
                                
                            },
                            series: [
                                        { 
                                            dataFieldFrom: 'validated', 
                                            dataFieldTo: 'consumed',
                                            //displayText: 'Different between Consumed and Validated',
                                            formatFunction: caculate,
                                            color: '#FF0000',
                                            labelOffset: {x: 20, y: 0}
                                        }
                                    ]
                            
                        }
                    ]
            };
            function caculate(value){
               value = value.from - value.to;
               return  Math.round(value * 100) / 100 ;
                 
            }
            // select the chartContainer DIV element and render the chart.
            var theme = getDemoTheme();
            $('#project_container').jqxChart(settings);
            $("#wd-display").jqxCheckBox({ width: 120, checked: true, theme: theme  });
            $("#wd-different").jqxCheckBox({ width: 120, checked: true , theme: theme  });
            $('#wd-display div div span').addClass('jqx-checkbox-check-checked-web');
            $('#wd-different div div span').addClass('jqx-checkbox-check-checked-web');
            var groups = $('#project_container').jqxChart('seriesGroups');
            // console.log(groups);
            var refreshChart = function () {
                $('#project_container').jqxChart({ enableAnimations: false });
                $('#project_container').jqxChart('refresh');
            }
            // update greyScale values.
            $("#wd-display").on('change', function (event) {
                var checked = event.args.checked;
                //var $class = $('#wd-display div div span').hasClass('jqx-checkbox-check-checked');
                if(checked){
                    $('#wd-display div div span').addClass('jqx-checkbox-check-checked-web');
                    $.each(groups, function(index, group) {
                        group.showLabels = true;
                    });
                } else {
                    $('#wd-display div div span').removeClass('jqx-checkbox-check-checked-web');
                    $.each(groups, function(index, group) {
                        group.showLabels = false;
                    });
                }
                refreshChart();
            });
            $("#wd-different").on('change', function (event) {
                var checked = event.args.checked;
                if(checked){
                    $('#wd-different div div span').addClass('jqx-checkbox-check-checked-web');
                    $.each(groups, function(index, group) {
                        if(group.type == "rangecolumn"){
                            group.valueAxis.maxValue = manDays;
                        };
                    });
                } else {
                    $('#wd-different div div span').removeClass('jqx-checkbox-check-checked-web');
                    $.each(groups, function(index, group) {
                        if(group.type == "rangecolumn"){
                            group.valueAxis.maxValue = 0;
                        };
                    });
                }
                refreshChart();
            });
        });
    </script>