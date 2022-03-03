<?php
echo $this->Html->css(array(
    'slick_grid/slick.grid.activity',
    'jquery.multiSelect',
    'projects',
    'slick_grid/slick.grid_v2',
    'slick_grid/slick.pager',
    'slick_grid/slick.common_v2',
    'slick_grid/slick.edit',
    '/js/qtip/jquery.qtip',
    'dashboard/jqx.base',
    'dashboard/jqx.web'
));
echo $this->Html->script(array(
    'jquery.multiSelect',
    'slick_grid/lib/jquery-ui-1.8.16.custom.min',
    'slick_grid/lib/jquery.event.drop-2.0.min',
    'slick_grid/lib/jquery.event.drag-2.2',
    'slick_grid/slick.core',
    'slick_grid/slick.dataview',
    'slick_grid/controls/slick.pager',
    'slick_grid/slick.formatters',
    'history_filter',
    'slick_grid/plugins/slick.cellrangedecorator',
    'slick_grid/plugins/slick.cellrangeselector',
    'slick_grid/plugins/slick.cellselectionmodel',
    'slick_grid/plugins/slick.rowselectionmodel',
    'slick_grid/plugins/slick.rowmovemanager',
    'slick_grid/slick.editors',
    'slick_grid/slick.grid',
    'slick_grid_custom',
    'slick_grid/slick.grid.activity',
    'jquery.ui.touch-punch.min',
    'qtip/jquery.qtip',
));
echo $this->element('dialog_projects');
$viewEuro = $bg_currency;
?>
<style>
    .slick-cell-move-handler {
        cursor: move;
    }
    .slick-cell-move-handler:empty {
        cursor: default;
    }
    p {
        margin-bottom: 10px;
    }
    .grid-canvas .editor-text{
        width: 90% !important;
    }
    #table-control {
        height: 56px;
        /*margin-top: -20px !important;*/
    }
    #table-control form{
        width: 550px;
        float: left;
        margin-top: 25px;
    }
    #table-control a.btn-plus-green{
        margin-top: 20px;
    }
    .slick-headerrow-column.ui-state-default{
        padding: 6px 4px 0 0 !important;
    }
    .error input{
        border: 1px solid red !important;
    }
    #wd-group{
    }
    .wd-end-st{
        overflow: hidden;
        width: 253px;
        float: left;
    }
    #wd-end-date-inv, #wd-end-date-fon{
        margin-left: -10px;
    }
    .slick-pane-top {
        top: 72px !important;
    }
    .gs-custom-cell-euro-header {
        background: url("../../img/front/bg-head-table.png") repeat-x #06427A;
    }
    .border-euro-custom span {
        color: #fff;
        float: left;
        font-size: 14px;
        font-weight: bold;
        padding-top: 7px;
        padding-left: 25px;
    }
    .slick-resizable-handle{
        background: transparent !important;
    }
    .headerHighLight{
        background: #95B3D7;
    }
    h3 {
        font-size: 1.5em;
    }
    h2 {
        font-size: 1.8em;
    }
    .progress-pie__bg {
        fill: rgba(255, 255, 255, 0.5);
    }

    .progress-pie__text {
        fill: #00426b;
        font-family: "Iceland", sans-serif;
        letter-spacing: -2;
    }
    .progress-pie__inner-disc {
        fill: white;
    }
    .sg-section--progress-pie .progress-pie {
        width: 18em;
        height: 18em;
    }
    .budget-chard{
        width: 25%;
        float: left;
    }
    .percent-chard{
        width: 50%;
        margin-left: 25%;
    }
    .circle-chard{
        float: right;
        margin-top: -100px;
        width: 25%;
    }
    #wd-container-footer{
        display: none;
    }
    body{
        overflow: hidden;
    }
    .slick-viewport-right{
        overflow-x: hidden !important;
        overflow-y: auto;
    }
    .slick-viewport-left{
        overflow: hidden !important;
    }
	.wd-tab{
		max-width: 1920px;
	}
</style>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>

            <div class="wd-tab"> <div class="wd-panel">
            <h2 class="wd-t1" style="margin-bottom: 10px; color: orange"><?php echo $projectName ?></h2>
            <div id = "budget-chard" style="width: 80%; clear:both; height: 155px">
                <div id="inve-chard" style="float: left; width: 50%">
                    <div class="chard-content">
                        <div class="budget-chard">
                            <p style="min-width: 200px;"><?php echo __d(sprintf($_domain, 'Finance_2'), 'Budget revised', true) .': '. number_format((!empty($total['budget_revised']) ? $total['budget_revised'] : 0), 2, '.', ' ') . ' '.$bg_currency ?></p>
                            <p style="min-width: 200px;"><?php echo __d(sprintf($_domain, 'Finance_2'), 'Latest estimate', true) .': '. number_format((!empty($total['last_estimated']) ? $total['last_estimated'] : 0), 2, '.', ' ')  . ' '.$bg_currency ?></p>
                            <?php
                                if(empty($total['budget_revised'])){
                                    $total['budget_revised'] = 0;
                                }
                                if(empty($total['last_estimated'])){
                                    $total['last_estimated'] = 0;
                                }
                                $per = 0;
                                if($total['budget_revised'] != 0) {
                                    $per = round($total['last_estimated']/$total['budget_revised'] * 100,2);
                                }
                                $color_min = '#13FF02';
                                $color_max = '#15830D';
                                if( $total['budget_revised'] == 0 && $total['last_estimated'] == 0 ){
                                    $width_bud = '0%';
                                    $width_avan = '0';
                                    $bg_color = 'green';
                                    $per = 0;
                                } else if( $total['budget_revised'] == 0 ){
                                    $width_bud = '0%';
                                    $width_avan = '80';
                                    $bg_color = 'green';
                                } else if( (($total['last_estimated'] > $total['budget_revised']) && $total['last_estimated'] > 0) || (($total['last_estimated'] > 0) && ($total['budget_revised'] <= 0)) ){
                                    $color_min = '#F98E8E';
                                    $color_max = '#FF0606';
                                    $bg_color = 'red';
                                    $width_bud = '80%';
                                    $width_avan = (abs($total['last_estimated'])/abs($total['budget_revised'])*80);
                                } else {
                                    $width_bud = '80%';
                                    $width_avan = (abs($total['last_estimated'])/abs($total['budget_revised'])*80);
                                    $bg_color = 'green';
                                }
                                $width_avan = $width_avan <= 100 ? $width_avan : 100;
                                $width_avan = $width_avan . '%';
                            ?>
                        </div>
                        <div class="percent-chard">
                            <div style="width: 50%">
                                <?php if($total['budget_revised'] < 0){ ?>
                                <div style="height: 10px; width: <?php echo $width_bud ?>; background-color: #ccc; float: right"></div>
                                <?php } else { ?>
                                <div style="height: 10px; width: 0px; background-color: #ccc; float: right"></div>
                                <?php }
                                if($total['last_estimated'] < 0){
                                ?>
                                <div style="margin-top: 25px; height: 10px; float: right; width: <?php echo $width_avan ?>; background-color: <?php echo $bg_color ?>"></div>
                                <?php } ?>
                            </div>
                            <div style="width: 50%; margin-left: 50%;">
                                <?php if($total['budget_revised'] >= 0){ ?>
                                <div style="height: 10px; width: <?php echo $width_bud ?>; background-color: #ccc"></div>
                                <?php } else { ?>
                                <div style="height: 10px; width: 0px; background-color: #ccc"></div>
                                <?php }
                                if($total['last_estimated'] >= 0){
                                ?>
                                <div style="margin-top: 25px; height: 10px; width: <?php echo $width_avan ?>; background-color: <?php echo $bg_color ?>"></div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="circle-chard">
                            <aside>
                                <svg class="progress-pie" width="90%" height="30%" role="image" style="margin: 10px; margin-top: 20px">
                                    <defs>
                                        <filter id="drop-shadow" x="-50%" y="-50%" width="200%" height="200%">
                                            <feOffset result="offOut" in="SourceAlpha" dx="0" dy="0" />
                                            <feGaussianBlur result="blurOut" in="offOut" stdDeviation="1" />
                                            <feBlend in="SourceGraphic" in2="blurOut" mode="normal" />
                                        </filter>
                                        <!-- inner circle shadow filter -->
                                        <filter id="drop-shadow-flat" x="-50%" y="-50%" width="200%" height="200%">
                                            <feGaussianBlur in="SourceAlpha" stdDeviation="1" result="A" />
                                            <feBlend in="SourceGraphic" in2="A" mode="normal" />
                                        </filter>
                                        <!-- shadow under progress ring -->
                                        <filter id="inset-shadow" x="-50%" y="-50%" width="200%" height="200%">
                                            <femorphology in="SourceAlpha" operator="erode" radius="0.5" />
                                            <feComponentTransfer>
                                                <feFuncA type="table" tableValues="1 0" />
                                            </feComponentTransfer>
                                            <feGaussianBlur stdDeviation="1" />
                                            <feOffset dx="0" dy="0" result="offsetblur" />
                                            <feFlood flood-color="rgb(0, 0, 0)" result="color" />
                                            <feComposite in2="offsetblur" operator="in" />
                                            <feComposite in2="SourceAlpha" operator="in" />
                                        </filter>
                                        <linearGradient id="pprg1" class="progress-pie__gradient">
                                            <stop offset="0%" stop-color="<?php echo $color_min ?>" />
                                            <stop offset="100%" stop-color="<?php echo $color_max ?>" />
                                        </linearGradient>
                                    </defs>
                                    <circle class="progress-pie__bg" filter="url(#drop-shadow)" r="50" cx="50" cy="50" fill="grey" opacity="0.3" />
                                    <circle stroke="grey" stroke-width="9" fill="none" r="39.5" cx="50" cy="50" opacity="0.3" filter="url(#inset-shadow)" />
                                    <circle class="progress-pie__ring" stroke="url(#pprg1)" stroke-width="9" stroke-dasharray="<?php echo abs($per/100*248.1858154) ?> 248.1858154" fill="none" r="39.5" cx="50" cy="50" transform="rotate(90, 50, 50)" />
                                    <circle class="progress-pie__inner-disc" fill="grey" opacity="0.2" r="35" cx="50" cy="50" filter="url(#drop-shadow-flat)" />
                                    <text class="progress-pie__text" x="50" y="57" text-anchor="middle" font-size="18"><?php echo $per ?><tspan font-size="15" dy="-5">%</tspan></text>
                                </svg>
                            </aside>
                        </div>
                    </div>
                </div>
                <div id="fon-chard" style="float: right; width: 50%">
                    <div style="clear: both;">
                        <p style="width: 20%; float: left"><?php echo __d(sprintf($_domain, 'Finance_2'), 'Budget revised', true)?></p>
                        <p style="margin-left: 20%; width: 40%;"><?php echo number_format((!empty($total['budget_revised']) ? $total['budget_revised'] : 0), 2, '.', ' ') . ' '.$bg_currency ?></p>
                        <p style="width: 9%; margin-left: 40%; margin-top: -25px"></p>
                        <p style="width: 300px; height:25px; background-color: #ccc; margin-left: 50%; margin-top: -35px">&nbsp</p>
                    </div>
                    <div style="clear: both;">
                        <p style="width: 20%; float: left"><?php echo __d(sprintf($_domain, 'Finance_2'), 'Latest estimate', true) ?></p>
                        <p style="margin-left: 20%; width: 20%;"><?php echo number_format((!empty($total['last_estimated']) ? $total['last_estimated'] : 0), 2, '.', ' ') . ' '.$bg_currency ?></p>
                        <?php
                            $total['last_estimated'] = !empty($total['last_estimated']) ? $total['last_estimated'] : 0;
                            $_per = !empty($total['budget_revised']) ? $total['last_estimated']/$total['budget_revised']*100 : 0;
                        ?>
                        <p style="width: 9%; margin-left: 40%; margin-top: -26px; text-align: right; <?php echo $_per > 100 ? 'color: red' : '' ?>"><?php
                            echo number_format($_per, 2, '.', ' ') . ' %';
                        ?></p>
                        <p style="width: <?php echo $_per*300/100 ?>px; height:25px; background-color: #f70707; margin-left: 50%; margin-top: -35px">&nbsp</p>
                    </div>
                    <div style="clear: both;">
                        <p style="width: 20%; float: left"><?php echo __d(sprintf($_domain, 'Finance_2'), 'Engaged', true)?></p>
                        <p style="margin-left: 20%; width: 20%;"><?php echo number_format((!empty($total['engaged']) ? $total['engaged'] : 0), 2, '.', ' ') . ' '.$bg_currency ?></p>
                        <?php
                            $total['engaged'] = !empty($total['engaged']) ? $total['engaged'] : 0;
                            $_per = !empty($total['budget_revised']) ? $total['engaged']/$total['budget_revised']*100 : 0;
                        ?>
                        <p style="width: 9%; margin-left: 40%; margin-top: -26px; text-align: right; <?php echo $_per > 100 ? 'color: red' : '' ?>"><?php
                            echo number_format($_per, 2, '.', ' ') . ' %';
                        ?></p>
                        <p style="width: <?php echo $_per*300/100 ?>px; height:25px; background-color: #fdea02; margin-left: 50%; margin-top: -35px">&nbsp</p>
                    </div>
                    <div style="clear: both;">
                        <p style="width: 20%; float: left"><?php echo __d(sprintf($_domain, 'Finance_2'), 'Bill', true) ?></p>
                        <p style="margin-left: 20%; width: 20%;"><?php echo number_format((!empty($total['bill']) ? $total['bill'] : 0), 2, '.', ' ') . ' '.$bg_currency ?></p>
                        <?php
                            $total['bill'] = !empty($total['bill']) ? $total['bill'] : 0;
                            $_per = !empty($total['budget_revised']) ? $total['bill']/$total['budget_revised']*100 : 0;
                        ?>
                        <p style="width: 9%; margin-left: 40%; margin-top: -26px; text-align: right; <?php echo $_per > 100 ? 'color: red' : '' ?>"><?php
                            echo number_format($_per, 2, '.', ' ') . ' %';
                        ?></p>
                        <p style="width: <?php echo $_per*300/100 ?>px; height:25px; background-color: rgba(255, 130, 1, 0.81); margin-left: 50%; margin-top: -35px">&nbsp</p>
                    </div>
                    <div style="clear: both;">
                        <p style="width: 20%; float: left"><?php echo __d(sprintf($_domain, 'Finance_2'), 'Disbursed', true)?></p>
                        <p style="margin-left: 20%; width: 20%;"><?php echo number_format((!empty($total['disbursed']) ? $total['disbursed'] : 0), 2, '.', ' ') . ' '.$bg_currency ?></p>
                        <?php
                            $total['disbursed'] = !empty($total['disbursed']) ? $total['disbursed'] : 0;
                            $_per = !empty($total['budget_revised']) ? $total['disbursed']/$total['budget_revised']*100 : 0;
                        ?>
                        <p style="width: 9%; margin-left: 40%; margin-top: -26px; text-align: right; <?php echo $_per > 100 ? 'color: red' : '' ?>"><?php
                            echo number_format($_per, 2, '.', ' ') . ' %';
                        ?></p>
                        <p style="width: <?php echo $_per*300/100 ?>px; height:25px; background-color: #ca5959; margin-left: 50%; margin-top: -35px">&nbsp</p>
                    </div>
                </div>
            </div>
            <div class="wd-list-project">
                <div id="message-place">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    ?>
                </div>
                <div id="table-control">
                    <?php
                    echo $this->Form->create('Engaged', array(
                        'type' => 'get',
                        'url' => '/' . Router::normalize($this->here)));
                    echo $this->Form->hidden('start');
                    echo $this->Form->hidden('end');
                    ?>
                    <fieldset>
                        <label><?php __('From') ?></label>
                        <div class="input" >
                            <?php
                                echo $this->Form->input('inv_start', array('label' => false, 'div' => false,  "class" => "placeholder", "placeholder" => __("(dd-mm-yyyy)", true), 'onchange' => "validated('inv');", 'value' => isset($invStart) ? date('d-m-Y', $invStart) : ''));
                            ?>
                        </div>
                        <label> <?php __('To') ?> </label>
                        <div id="wd-group-inv">
                            <div class="input" id="wd-end-date-inv">
                                <?php
                                    echo $this->Form->input('inv_end', array('label' => false, 'div' => false,  "class" => "placeholder", "placeholder" => __("(dd-mm-yyyy)", true), 'onchange' => "validated('inv');", 'value' => isset($invEnd) ? date('d-m-Y', $invEnd) : ''));
                                ?>
                            </div>
                            <p style="display: none; clear: both; padding-top: 2px; color: red;" class="wd-error wd-error-inv"><?php echo __('The end date must be greater than start date', true);?></p>
                        </div>
                        <div class="button" id="wd-submit-inv">
                            <input type="submit" value="OK" id="sutInv" />
                        </div>
                        <div style="clear:both;"></div>
                    </fieldset>
                    <?php
                    echo $this->Form->end();
                    ?>
                    <a href="javascript:void(0);" class="btn btn-plus-green" onclick="addNewRow('inv');" title="<?php __('Add an order') ?>"></a>
                </div>
                <div id="scrollTopAbsence"><div id="scrollTopAbsenceContent"></div></div>
                <br clear="all"  />
                <div class="wd-table" id="project_container" style="width:100%;height:300px;">
                </div>
            </div>
            </div></div>
        </div>
    </div>
</div>
<?php
function jsonParseOptions($options, $safeKeys = array()) {
    $output = array();
    $safeKeys = array_flip($safeKeys);
    foreach ($options as $option) {
        $out = array();
        foreach ($option as $key => $value) {
            if (!is_int($value) && !isset($safeKeys[$key])) {
                $value = json_encode($value);
            }
            $out[] = $key . ':' . $value;
        }
        $output[] = implode(', ', $out);
    }
    return '[{' . implode('},{ ', $output) . '}]';
}
$dataView = array();
$columns = array(
    array(
        'id' => 'no.',
        'field' => 'no.',
        'name' => '#',
        'width' => 20,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1
    ),
    array(
        'id' => 'name',
        'field' => 'name',
        'name' => "",
        'width' => 100,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'budget_initial',
        'field' => 'budget_initial',
        'name' => __d(sprintf($_domain, 'Finance_2'), 'Budget initial', true),
        'width' => 100,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.manDayValue'
    ),
    array(
        'id' => 'budget_revised',
        'field' => 'budget_revised',
        'name' => __d(sprintf($_domain, 'Finance_2'), 'Budget revised', true),
        'width' => 100,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.manDayValue'
    ),
    array(
        'id' => 'last_estimated',
        'field' => 'last_estimated',
        'name' => __d(sprintf($_domain, 'Finance_2'), 'Latest estimate', true),
        'width' => 100,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.manDayValue'
    ),
    array(
        'id' => 'percent',
        'field' => 'percent',
        'name' => __('%', true),
        'width' => 60,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.percentValue'
    ),
    array(
        'id' => 'dr_de',
        'field' => 'dr_de',
        'name' => __d(sprintf($_domain, 'Finance_2'), 'DR - DE', true),
        'width' => 100,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.manDayValueDeDr'
    ),
    array(
        'id' => 'engaged',
        'field' => 'engaged',
        'name' => __d(sprintf($_domain, 'Finance_2'), 'Engaged', true),
        'width' => 100,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.manDayValue'
    ),
    array(
        'id' => 'bill',
        'field' => 'bill',
        'name' => __d(sprintf($_domain, 'Finance_2'), 'Bill', true),
        'width' => 100,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.manDayValue'
    ),
    array(
        'id' => 'disbursed',
        'field' => 'disbursed',
        'name' => __d(sprintf($_domain, 'Finance_2'), 'Disbursed', true),
        'width' => 100,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.manDayValue'
    ),
);
$columnInvYears = array();
$invStYear = date('Y', $start);
$invEnYear = date('Y', $end);
while($invStYear <= $invEnYear){
    $columnInvYears[] = array(
        'id' => 'budget_initial-' . $invStYear,
        'field' => 'budget_initial-' . $invStYear,
        'name' => __d(sprintf($_domain, 'Finance_2'), 'Budget initial', true),
        'width' => 100,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericValue',
        'formatter' => 'Slick.Formatters.manDayValue'
    );
    $columnInvYears[] = array(
        'id' => 'budget_revised-' . $invStYear,
        'field' => 'budget_revised-' . $invStYear,
        'name' => __d(sprintf($_domain, 'Finance_2'), 'Budget revised', true),
        'width' => 100,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericValue',
        'formatter' => 'Slick.Formatters.manDayValue'
    );
    $columnInvYears[] = array(
        'id' => 'last_estimated-' . $invStYear,
        'field' => 'last_estimated-' . $invStYear,
        'name' => __d(sprintf($_domain, 'Finance_2'), 'Latest estimate', true),
        'width' => 100,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericValue',
        'formatter' => 'Slick.Formatters.manDayValue'
    );
    $columnInvYears[] = array(
        'id' => 'percent-' . $invStYear,
        'field' => 'percent-' . $invStYear,
        'name' => __('%', true),
        'width' => 60,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.percentValue'
    );
    $columnInvYears[] = array(
        'id' => 'dr_de-' . $invStYear,
        'field' => 'dr_de-' . $invStYear,
        'name' => __d(sprintf($_domain, 'Finance_2'), 'DR - DE', true),
        'width' => 100,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.manDayValueDeDr'
    );
    $columnInvYears[] = array(
        'id' => 'engaged-' . $invStYear,
        'field' => 'engaged-' . $invStYear,
        'name' => __d(sprintf($_domain, 'Finance_2'), 'Engaged', true),
        'width' => 100,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericValue',
        'formatter' => 'Slick.Formatters.manDayValue'
    );
    $columnInvYears[] = array(
        'id' => 'bill-' . $invStYear,
        'field' => 'bill-' . $invStYear,
        'name' => __d(sprintf($_domain, 'Finance_2'), 'Bill', true),
        'width' => 100,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericValue',
        'formatter' => 'Slick.Formatters.manDayValue'
    );
    $columnInvYears[] = array(
        'id' => 'disbursed-' . $invStYear,
        'field' => 'disbursed-' . $invStYear,
        'name' => __d(sprintf($_domain, 'Finance_2'), 'Disbursed', true),
        'width' => 100,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericValue',
        'formatter' => 'Slick.Formatters.manDayValue'
    );
    $invStYear++;
}
$columnInvAction[] = array(
    'id' => 'inv_action.',
    'field' => 'inv_action.',
    'name' => __('Action', true),
    'width' => 70,
    'sortable' => false,
    'resizable' => true,
    'noFilter' => 1,
    'formatter' => 'Slick.Formatters.Action'
);
$columns = array_merge($columns, $columnInvYears, $columnInvAction);
$totalHeader = $calPercent = array();
foreach($finances as $id => $name){
    $data = array(
        'id' => $id,
        'MetaData' => array()
    );
    $data['project_id'] = $projects['Project']['id'];
    $data['company_id'] = $projects['Project']['company_id'];
    $data['name'] = (string) $name;
    $totalBudget = array();
    $totalEstimated = $totalBudgetEr = $percentYears = array();
    $totalDrDe = 0;
    if(!empty($financeDetails[$id])){
        foreach($financeDetails[$id] as $year => $financeDetail){
            foreach ($financeDetail as $key => $value) {
                $value = !empty($value) ? $value : 0;
                if(!isset($totalHeader[$key . '-' . $year])){
                    $totalHeader[$key . '-' . $year] = 0;
                }
                $totalHeader[$key . '-' . $year] += $value;

                $data[$key . '-' . $year] = $value;
                if(!isset($totalBudget[$key])){
                    $totalBudget[$key] = 0;
                }
                $totalBudget[$key] += $value;
            }
            //
            $a = !empty($financeDetail['last_estimated']) ? $financeDetail['last_estimated'] : 0;
            $b = !empty($financeDetail['budget_revised']) ? $financeDetail['budget_revised'] : 0;
            $data['dr_de-' . $year] = $a - $b;
            if(!isset($totalHeader['dr_de-' . $year])){
                $totalHeader['dr_de-' . $year] = 0;
            }
            $totalHeader['dr_de-' . $year] += $a - $b;
            $totalDrDe += $a - $b;
            $data['percent-' . $year] = ($b == 0) ? 0 : round($a/$b*100, 2);

            if(!isset($totalEstimated[$year])){
                $totalEstimated[$year] = 0;
            }
            $totalEstimated[$year] += !empty($totalHeader['last_estimated-' . $year]) ? $totalHeader['last_estimated-' . $year] : 0;
            if(!isset($totalBudgetEr[$year])){
                $totalBudgetEr[$year] = 0;
            }
            $totalBudgetEr[$year] += !empty($totalHeader['budget_revised-' . $year]) ? $totalHeader['budget_revised-' . $year] : 0;
        }
    }
    $data['dr_de'] = $totalDrDe;
    if(!isset($totalHeader['dr_de'])){
        $totalHeader['dr_de'] = 0;
    }
    $totalHeader['dr_de'] += $totalDrDe;
    if(!empty($totalBudget)){
        foreach ($totalBudget as $key => $value) {
            $data[$key] = $value;
            if(!isset($totalHeader[$key])){
                $totalHeader[$key] = 0;
            }
            $totalHeader[$key] += $value;
        }
    }
    $a = !empty($totalBudget['last_estimated']) ? $totalBudget['last_estimated'] : 0;
    $b = !empty($totalBudget['budget_revised']) ? $totalBudget['budget_revised'] : 0;
    $data['percent'] = ($b == 0) ? 0 : round($a/$b*100, 2);
    $dataView[] = $data;
}
$a = !empty($totalHeader['last_estimated']) ? $totalHeader['last_estimated'] : 0;
$b = !empty($totalHeader['budget_revised']) ? $totalHeader['budget_revised'] : 0;
$totalHeader['percent'] = ($b == 0) ? 0 : round($a/$b*100, 2);
$_s = date('Y', $start);
while ($_s <= $invEnYear) {
    $a = !empty($totalEstimated[$_s]) ? $totalEstimated[$_s] : 0;
    $b = !empty($totalBudgetEr[$_s]) ? $totalBudgetEr[$_s] : 0;
    $totalHeader['percent-' . $_s] = ($b == 0) ? 0 : round($a/$b*100, 2);
    $_s++;
}
$selectMaps = array();
$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true),
    'Date closing must between %1$s and %2$s' => __('Date closing must between %1$s and %2$s', true)
);
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete_finance_two_plus', '%1$s', '%2$s', '?' => array('start' => date('d-m-Y', $start), 'end' => date('d-m-Y', $end)))); ?>">Delete</a>
        </div>
    </div>
</div>
<script type="text/javascript">
    var DateValidate = {},dataGrid, IuploadComplete = function(json){
        var data = dataGrid.eval('currentEditor');
        data.onComplete(json);
    };
    (function($){
        $(function(){
            var $this = SlickGridCustom,
                projects = <?php echo json_encode($projects) ?>,
                headerConsumedRightInv = '<div class="slick-header-columns" style="margin-left: -1px; border-top: none; height: 36px;">',
                highLightInv = '.l2, .l3, .l4, .l5, .l6, .l7, .l8, .l9',
                invStart = <?php echo json_encode(date('Y', $start));?>,
                invEnd = <?php echo json_encode(date('Y', $end));?>,
                start = <?php echo json_encode(date('Y', $start));?>,
                end = <?php echo json_encode(date('Y', $end));?>,
                totalHeader = <?php echo json_encode($totalHeader);?>,
                projects = <?php echo !empty($projects['Project']) ? json_encode($projects['Project']) : json_encode(array());?>,
                viewEuro = <?php echo json_encode($viewEuro);?>,
                totalName = <?php echo json_encode(__d(sprintf($_domain, 'Finance'), 'Total', true));?>;
                $this.i18n = <?php echo json_encode($i18n); ?>;
                $this.canModified =  <?php echo json_encode((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite)); ?>;
                $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            var actionTemplate =  $('#action-template').html();
            function resizeHandler1(){
                var _cols = ControlGridOne.getColumns();
                var _numCols = _cols.length;
                var _gridW = 0;
                var columnWidth = {};
                for (var i=0; i<_numCols; i++) {
                    _gridW += _cols[i].width;
                    columnWidth[_cols[i].id] = _cols[i].width;
                }
                $('.slick-header-columns').css('width', _gridW);
                historyData.set('columnWidth1', columnWidth);
                // call save here
                // **
                saveFilter();
            }

            $.extend(Slick.Formatters,{
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,
                    dataContext.project_id, dataContext.name), columnDef, dataContext);
                },
                manDayValue : function(row, cell, value, columnDef, dataContext){
                    if(value && value != 0){
                        value = number_format(value, 2, ',', ' ');
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + ' ' + viewEuro + '</span> ', columnDef, dataContext);
                    } else {
                        value = (value && value != 0) ? value : '';
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + '</span> ', columnDef, dataContext);
                    }
                },
                manDayValueDeDr : function(row, cell, value, columnDef, dataContext){
                    if(value && value != 0){
                        if(value > 0) {
                            style = 'color: red';
                        } else {
                            style = 'color: green';
                        }
						value = number_format(Math.abs(value), 2, ',', ' ');
                        return Slick.Formatters.HTMLData(row, cell, '<span style="' + style + '" class="row-number">' + value + ' ' + viewEuro + '</span> ', columnDef, dataContext);
                    } else {
                        value = (value && value != 0) ? value : '';
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + '</span> ', columnDef, dataContext);
                    }
                },
                percentValue : function(row, cell, value, columnDef, dataContext){
                    if(value && value != 0){
                        value = number_format(value, 2, ',', ' ');
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + ' %' + '</span> ', columnDef, dataContext);
                    } else {
                        value = (value && value != 0) ? value : '';
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + '</span> ', columnDef, dataContext);
                    }
                }
            });

            $.extend(Slick.Editors,{
                numericValue : function(args){
                    $.extend(this, new Slick.Editors.textBox(args));
                    this.input.attr('maxlength' , 10).keypress(function(e){
                        var key = e.keyCode ? e.keyCode : e.which;
                        if(!key || key == 8 || key == 13 || e.ctrlKey || e.shiftKey){
                            return;
                        }
                        var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
                        if(val == '0' || !/^[\-]?([0-9]{0,8})(\.[0-9]{0,2})?$/.test(val)){
                            e.preventDefault();
                            return false;
                        }
                    });
                }
            });
            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            var options = {
                showHeaderRow: true,
                enableAddRow : false,
                frozenColumn: 9
            };
            ControlGridOne = $this.init($('#project_container'),data,columns,options);
            ControlGridOne.onColumnsResized.subscribe(function (e, args) {
                resizeHandler1();
            });
            var fields = {
                id : {defaulValue : 0},
                project_id : {defaulValue : projects['id'], allowEmpty : false},
                company_id : {defaulValue : projects['company_id'], allowEmpty : false},
                name : {defaulValue : '', allowEmpty : false}
            };
            var fieldNew = {
                project_id: projects['id'],
                company_id : projects['company_id'],
                name : '',
                budget_initial: '',
                budget_revised: '',
                last_estimated: '',
                engaged: '',
                bill: '',
                disbursed: ''
            };
            var leftInv = 10, rightInv = 10, countInv = 1;
            while(invStart <= invEnd){
                fields['budget_initial-' + invStart] = {defaulValue : ''};
                fields['budget_revised-' + invStart] = {defaulValue : ''};
                fields['last_estimated-' + invStart] = {defaulValue : ''};
                fields['engaged-' + invStart] = {defaulValue : ''};
                fields['bill-' + invStart] = {defaulValue : ''};
                fields['disbursed-' + invStart] = {defaulValue : ''};
                fieldNew['budget_initial-' + invStart] = '';
                fieldNew['budget_revised-' + invStart] = '';
                fieldNew['last_estimated-' + invStart] = '';
                fieldNew['engaged-' + invStart] = '';
                fieldNew['bill-' + invStart] = '';
                fieldNew['disbursed-' + invStart] = '';
                headerConsumedRightInv += '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'+(leftInv++)+' r'+(rightInv++)+'"></div>';
                headerConsumedRightInv += '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'+(leftInv++)+' r'+(rightInv++)+'"></div>';
                headerConsumedRightInv += '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'+(leftInv++)+' r'+(rightInv++)+'"></div>';
                headerConsumedRightInv += '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'+(leftInv++)+' r'+(rightInv++)+'"></div>';
                headerConsumedRightInv += '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'+(leftInv++)+' r'+(rightInv++)+'"><span>' +invStart+ '</span></div>';
                headerConsumedRightInv += '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'+(leftInv++)+' r'+(rightInv++)+'"></div>';
                headerConsumedRightInv += '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'+(leftInv++)+' r'+(rightInv++)+'"></div>';
                headerConsumedRightInv += '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'+(leftInv++)+' r'+(rightInv++)+'"></div>';
                if(countInv%2 == 0){
                    var _l = leftInv;
                    highLightInv += ', .l' + (_l-8);
                    highLightInv += ', .l' + (_l-7);
                    highLightInv += ', .l' + (_l-6);
                    highLightInv += ', .l' + (_l-5);
                    highLightInv += ', .l' + (_l-4);
                    highLightInv += ', .l' + (_l-3);
                    highLightInv += ', .l' + (_l-2);
                    highLightInv += ', .l' + (_l-1);
                }
                countInv++;
                invStart++;
            }
            headerConsumedRightInv += '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'+(leftInv++)+' r'+(rightInv++)+'"></div></div>';

            $this.url =  '<?php echo $html->url(array('action' => 'update_finance_two_plus')); ?>';
            $this.fields = fields;
            $this.onBeforeEdit = function(args){
                return true;
            }
            $this.onCellChange = function(args){
                if(args.column.id == 'name') return true;
                if(args && args.column.id && args.item){
                    var columnName = args.column.id;
                    var _start = start;
                    var _end = end;
                    columnName = columnName.split('-');
                    var totalValue = 0;
                    while(_start <= _end){
                        totalValue += Number(args.item[columnName[0] + '-' + _start]) ? parseFloat(args.item[columnName[0] + '-' + _start]) : 0;
                        _start++;
                    }
                    args.item[columnName[0]] = totalValue;
                    if(columnName[0] == 'budget_revised'){
                        var _percentV = 0;
                        var last_estimated = args.item.last_estimated;
                        if(totalValue != 0){
                            _percentV = last_estimated/totalValue * 100;
                        }
                        args.item['percent'] = _percentV;
                        args.item['dr_de'] = last_estimated - totalValue;
                        args.item['percent-' + columnName[1]] = (args.item['budget_revised-' + columnName[1]] ==0) ? 0 : args.item['last_estimated-' + columnName[1]] / args.item['budget_revised-' + columnName[1]]*100;
                        args.item['dr_de-' + columnName[1]] = args.item['last_estimated-' + columnName[1]] - args.item['budget_revised-' + columnName[1]];
                    } else if(columnName[0] == 'last_estimated'){
                        var _percentV = 0;
                        var budget_revised = args.item.budget_revised;
                        if(totalValue != 0){
                            _percentV = totalValue/budget_revised * 100;
                        }
                        args.item['percent'] = _percentV;
                        args.item['dr_de'] = totalValue - budget_revised;
                        args.item['percent-' + columnName[1]] = (args.item['budget_revised-' + columnName[1]] == 0) ? 0 : args.item['last_estimated-' + columnName[1]] / args.item['budget_revised-' + columnName[1]]*100;
                        args.item['dr_de-' + columnName[1]] = args.item['last_estimated-' + columnName[1]] - args.item['budget_revised-' + columnName[1]];
                    }

                    /**
                     * Tinh header
                     */
                    var _datas = $.extend(true, {}, data);
                    var _totalHeader = {}, _budgetHeader = {}, _avanHeader = {};
                    var totalEsti = totalBud = 0;
                    $.each(_datas, function(key, _data){
                        $.each(_data, function(ind, val){
                            var _ind = ind.split('-');
                            if(_ind[0] == 'id' || _ind[0] == 'MetaData' || _ind[0] == 'project_id' || _ind[0] == 'company_id' || _ind[0] == 'name'){

                            } else {
                                if(_ind[0] &&  (_ind[0] != 'percent')){
                                    if(!_totalHeader[ind]){
                                        _totalHeader[ind] = 0;
                                    }
                                    _totalHeader[ind] += parseFloat(val);
                                }
                                if((_ind[0] == 'budget_revised') && _ind[1]){
                                    if(!_budgetHeader[_ind[1]]){
                                        _budgetHeader[_ind[1]] = 0;
                                    }
                                    _budgetHeader[_ind[1]] += parseFloat(val);
                                }
                                if((_ind[0] == 'last_estimated') && _ind[1]) {
                                    if(!_avanHeader[_ind[1]]){
                                        _avanHeader[_ind[1]] = 0;
                                    }
                                    _avanHeader[_ind[1]] += parseFloat(val);
                                }
                                if((_ind[0] == 'budget_revised') && !_ind[1]){
                                    totalBud += val;
                                }
                                if((_ind[0] == 'last_estimated') && !_ind[1]){
                                    totalEsti += val;
                                }
                            }
                        });
                    });
                    if(_budgetHeader){
                        $.each(_budgetHeader, function(key, budVal){
                            var avanVal = _avanHeader[key] ? _avanHeader[key] : 0;
                            var perVal = (budVal == 0) ? 0 : avanVal/budVal*100;
                            _totalHeader['percent-'+key] = perVal;
                        });
                        _totalHeader['percent'] = (totalBud == 0) ? 0 : totalEsti/totalBud*100;
                    }
                    if(_totalHeader){
                        $.each(_totalHeader , function(id){
                            var _views = id.split('-');
                            var _symbol = (_views[0] && _views[0] == 'percent') ? '%' : viewEuro;
							// var _real_val = Number(this);
                            var val = Number(this) ? number_format(Math.abs(Number(this)), 2, ',', ' ') + ' ' + _symbol : '';
                            if($(ControlGridOne.getHeaderRowColumn(id)).hasClass('row-number')){
                                if(_views[0] && _views[0] == 'dr_de'){
                                    if(Number(this) > 0){
                                        $(ControlGridOne.getHeaderRowColumn(id)).find('.row-number b').html(val).css('color', 'red');
                                    } else {
                                        $(ControlGridOne.getHeaderRowColumn(id)).find('.row-number b').html(val).css('color', 'green');
                                    }
                                } else {
                                    $(ControlGridOne.getHeaderRowColumn(id)).find('.row-number b').html(val);
                                }
                            } else {
                                if(_views[0] && _views[0] == 'dr_de'){
                                    if(Number(this) > 0){
                                        $(ControlGridOne.getHeaderRowColumn(id)).html('<span style = "color: red" class="row-number"><b>' + val + '</b></span>');
                                    } else {
                                        $(ControlGridOne.getHeaderRowColumn(id)).html('<span style = "color: green" class="row-number"><b>' + val + '</b></span>');
                                    }
                                } else {
                                    $(ControlGridOne.getHeaderRowColumn(id)).html('<span class="row-number"><b>' + val + '</b></span>');
                                }
                            }
                        });
                    }
                    //end.
                    var columns = args.grid.getColumns(),
                        col, cell = args.cell;
                    do {
                        cell++;
                        if( columns.length == cell )break;
                        col = columns[cell];
                    } while (typeof col.editor == 'undefined');

                    if( cell < columns.length ){
                        args.grid.gotoCell(args.row, cell, true);
                    }
                }
                $('.row-number').parent().addClass('row-number-custom');
            }
            var _ids = 999999999999;
            addNewRow = function(type){
                var newRow = $.extend(true, {}, fieldNew);
                var ControlGrid = ControlGridOne;
                var rowData = ControlGrid.getData().getItems();
                var _length = rowData.length;
                newRow['id'] = _ids++;
                ControlGrid.invalidateRow(_length);
                rowData.splice(_length, 0, newRow);
                ControlGrid.getData().setItems(rowData);
                ControlGrid.render();
                ControlGrid.scrollRowIntoView(_length-1, false);
                $('.row-number').parent().addClass('row-number-custom');
                ControlGrid.gotoCell(_length, 1, true);
            }
            /**
             * Add header phia duoi
             */
            if(totalHeader){
                $.each(totalHeader , function(id){
                    var _views = id.split('-');
                    var _symbol = (_views[0] && _views[0] == 'percent') ? '%' : viewEuro;
                    var val = Number(this) ? number_format(Math.abs(Number(this)), 2, ',', ' ') + ' ' + _symbol : '';
                    if(_views[0] && _views[0] == 'dr_de'){
                        if(Number(this) > 0){
                            $(ControlGridOne.getHeaderRowColumn(id)).html('<span style = "color: red" class="row-number"><b>' + val + '</b></span>');
                        } else {
                            $(ControlGridOne.getHeaderRowColumn(id)).html('<span style = "color: green" class="row-number"><b>' + val + '</b></span>');
                        }
                    } else {
                        $(ControlGridOne.getHeaderRowColumn(id)).html('<span class="row-number"><b>' + val + '</b></span>');
                    }
                });
            }
            /**
             * Handle Form Control date time
             */
            $('#sutInv').click(function(){
                $('#InvFonStart').val($('#FonFonStart').val());
                $('#InvFonEnd').val($('#FonFonEnd').val());
            });
            $('#sutFon').click(function(){
                $('#FonInvStart').val($('#InvInvStart').val());
                $('#FonInvEnd').val($('#InvInvEnd').val());
            });
            /**
             * add class lt
             */
            $('.row-number').parent().addClass('row-number-custom');
            $('.slick-headerrow-columns div').addClass('gs-custom-cell-erro');
            /**
             * Add header phia tren
             */
            var headerConsumedLeft =
                '<div class="slick-header-columns" style="margin-left: -1px; border-top: none; height: 36px;">'
                    + '<div class="slick-headerrow-column l0 r0 gs-custom-cell-euro-header fist-element border-euro-custom"></div>'
                    + '<div class="slick-headerrow-column l1 r1 gs-custom-cell-euro-header border-euro-custom"></div>'
                    + '<div class="slick-headerrow-column l2 r2 gs-custom-cell-euro-header border-euro-custom"></div>'
                    + '<div class="slick-headerrow-column l3 r3 gs-custom-cell-euro-header border-euro-custom"></div>'
                    + '<div class="slick-headerrow-column l4 r4 gs-custom-cell-euro-header border-euro-custom"></div>'
                    + '<div class="slick-headerrow-column l5 r5 gs-custom-cell-euro-header border-euro-custom"><span>' +totalName+ '</span></div>'
                    + '<div class="slick-headerrow-column l6 r6 gs-custom-cell-euro-header border-euro-custom"></div>'
                    + '<div class="slick-headerrow-column l7 r7 gs-custom-cell-euro-header border-euro-custom"></div>'
                    + '<div class="slick-headerrow-column l8 r8 gs-custom-cell-euro-header border-euro-custom"></div>'
                    + '<div class="slick-headerrow-column l9 r9 gs-custom-cell-euro-header border-euro-custom"></div>'
              + '</div>';
            $('#project_container').find('.slick-header-columns-left').before(headerConsumedLeft);
            $('#project_container').find('.slick-header-columns-right').before(headerConsumedRightInv);
            $('#project_container .slick-header-columns').find(highLightInv).addClass('headerHighLight');
            $('#project_container').height($('#project_container').height() + 36);

            /**
             * Handle date time
             */
            $("#EngagedInvStart, #EngagedInvEnd").datepicker({
                dateFormat      : 'dd-mm-yy'
            });
        });
    })(jQuery);
    function validated(checkType){
        var _start = $('#EngagedInvStart').val().toString();
        _start = _start.split('-');
        var myStartDate = new Date(_start[2],_start[1],_start[0]);
        _start = Number(myStartDate);

        var _end = $('#EngagedInvEnd').val().toString();
        _end = _end.split('-');
        var myEndDate = new Date(_end[2],_end[1],_end[0]);
        _end = Number(myEndDate);
        if(_start <= _end){
            $('#wd-end-date-' + checkType).removeClass('error');
            $('#wd-group-' + checkType).removeClass('wd-end-st');
            $('.wd-error-' + checkType).css('display', 'none');
            $('#wd-submit-' + checkType).show();
        } else {
            $('#wd-end-date-' + checkType).addClass('error');
            $('#wd-group-' + checkType).addClass('wd-end-st');
            $('.wd-error-' + checkType).css('display', 'block');
            $('#wd-submit-' + checkType).hide();
        }
    }
    function number_format(number, decimals, dec_point, thousands_sep) {
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }
    function setupScroll(){
        $("#scrollTopAbsenceContent").width($(".grid-canvas-right:first").width()+100);
        $("#scrollTopAbsence").width($(".slick-viewport-right:first").width());
    }
    setTimeout(function(){
        setupScroll();
    }, 1500);
    $( window ).resize(function() {
        setupScroll();
    });
    $("#scrollTopAbsence").scroll(function () {
        $(".slick-viewport-right:first").scrollLeft($("#scrollTopAbsence").scrollLeft());
    });
</script>
