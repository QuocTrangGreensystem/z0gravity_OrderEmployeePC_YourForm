<?php echo $html->css(array('projects')); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
<?php 
    echo $this->Html->script(array(
        //'dashboard/jquery-1.10.1.min',
        'dashboard/jqx-all',
        'dashboard/jqxchart',
        'dashboard/jqxcore',
        'dashboard/jqxdata'
    )); 
?>
<?php 
    echo $this->Html->css(array(
        'dashboard/jqx.base'
    )); 
?>
<!-- export excel  -->
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
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout" style="margin-left: 0px;">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                    <h2 class="wd-t1"><?php echo sprintf(__("Dash Board of %s", true), $projectName['Project']['project_name']); ?></h2>	
                    <?php /*
                    <a href="<?php //echo $html->url("/project_phase_plans/phase_vision/" . $projectName['Project']['id']) ?>" class="wd-add-project" style="margin-right:5px; "><span><?php __('Gantt+') ?></span></a>
                    <a href="<?php //echo $html->url("/project_tasks/exportExcel/" . $projectName['Project']['id']) ?>" class="wd-add-project" id="export-submit" style="margin-right:5px; "><span><?php __('Export Excel') ?></span></a>	
                    */ ?>
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
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        // prepare chart data as an array
        var dataSets    = <?php echo json_encode($endDatas); ?>,
        _leght    = <?php echo json_encode($lenght); ?>,
        _series    = <?php echo json_encode($series); ?>,
        listSeverities = <?php echo json_encode($listSeverities);?>
        ;      
        var formatData = [
                { 0: '', 50: listSeverities[2], 100: listSeverities[1], 150: listSeverities[0]}
            ];
        var _setRies = [];
        $.each(_series, function(id, value){
            _setRies[id] = {
                dataField: 'SalesQ'+id,
                radiusDataField: 'YoYGrowthQ'+id,
                minRadius: value['minRadius'+id],
                maxRadius: value['maxRadius'+id],
                displayText: value['name'+id],
                color: value['color'+id],
                tooltip: 'Name: ' + value['name'+id] + '<br />Assign: ' + value['assign'+id]
            };
        });
        // prepare jqxChart settings
        var settings = {
            title: "Risks Category",
            //description: "(the size of the circles represents relative YoY growth)",
            enableAnimations: true,
            showLegend: true,
            padding: { left: 5, top: 5, right: 5, bottom: 10 },
            titlePadding: { left: 90, top: 0, right: 0, bottom: 10 },
            // legendLayout: { flow: 'vertical' },
            // legendLayout: { left: 100, top: 10, width: 70, height: 700, flow: 'vertical' },
            // legendLayout: { left: 90, top: 300, width: 1600, height: 5000, flow: 'vertical' },
            // showLegend: true,
            //legendLayout: { left: 450, top: 405, width: 70, height: 700, flow: 'vertical' },
            //legendLayout: { left: 450, top: 405 , height: 90, flow: 'vertical' },
            source: dataSets,
            categoryAxis:
                {
                    dataField: 'occur',
                    description: 'Occurrence',
                    valuesOnTicks: false
                },
            colorScheme: 'scheme02',
            seriesGroups:
                [
                    {
                        type: 'bubble',
                        showLabels: true,
                        toolTipFormatFunction : function(a, b, data, d){
                            return data.tooltip;
                        },
                        formatFunction: function (a, b, data, d) {
                            //return data.displayText;
                        },
                        valueAxis:
                        {
                            unitInterval: 50,
                            minValue: 0,
                            maxValue: 150,
                            description: 'Severity',
                            formatFunction: function (value) {
                                return  formatData[0][value];
                            }
                        },
                        series: _setRies
                    }
                ]
        };
        // setup the chart
        $('#project_container').jqxChart(settings);
    });
</script>