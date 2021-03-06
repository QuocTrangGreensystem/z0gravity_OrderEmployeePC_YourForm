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
    'draw-progress',
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
        margin-top: -20px !important;
    }
    #table-control form{
        width: 550px;
        float: left;
        margin-top: 15px;
    }
    #table-control a.btn-plus-green{
        margin-top: 10px;
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
    /*.slick-viewport-right{
        overflow-x: auto !important;
        overflow-y: auto;
    }*/
    .wd-table .slick-viewport{
        overflow-x: scroll !important;
        overflow-y: auto;
    }
    .slick-viewport-left{
        overflow: hidden !important;
    }
    .slick-row.active {
        background: #d8edf9 !important;
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
            <?php 
			if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>

            <div class="wd-tab"> <div class="wd-panel"  style="overflow-y: auto">
            <h2 class="wd-t1" style="margin-bottom: 10px; color: orange"><?php echo $projectName ?></h2>
            <div id = "budget-chard" style="width: 100%; clear:both; display: inline-block;">
                <div id="inve-chard" style="float: left; width: 50%">
                    <h3 class="wd-t1" style="color: #ffb250; margin-bottom: 10px;"><?php echo __d(sprintf($_domain, 'Finance'), 'Budget Investment', true); ?></h3>
                    <div class="chard-content">
                        <div class="budget-chard" style="display: none">
                            <p style="min-width: 200px;"><?php echo __d(sprintf($_domain, 'Finance'), 'Budget', true) .': '. number_format((!empty($totals['inv']['budget']) ? $totals['inv']['budget'] : 0), 2, '.', ' ') . ' '.$bg_currency ?></p>
                            <p style="min-width: 200px;"><?php echo __d(sprintf($_domain, 'Finance'), 'Engaged', true) .': '. number_format((!empty($totals['inv']['avancement']) ? $totals['inv']['avancement'] : 0), 2, '.', ' ')  . ' '.$bg_currency ?></p>
                            <?php
                                if(empty($totals['inv']['budget'])){
                                    $totals['inv']['budget'] = 0;
                                }
                                if(empty($totals['inv']['avancement'])){
                                    $totals['inv']['avancement'] = 0;
                                }
                                if($totals['inv']['budget'] == 0) {
                                    $per = 100;
                                } else {
                                    $per = round($totals['inv']['avancement']/$totals['inv']['budget'] * 100,2);
                                }
                                $color_min = '#13FF02';
                                $color_max = '#15830D';
                                if( $totals['inv']['budget'] == 0 && $totals['inv']['avancement'] == 0 ){
                                    $width_bud = '0%';
                                    $width_avan = '0';
                                    $bg_color = 'green';
                                    $per = 0;
                                } else if( $totals['inv']['budget'] == 0 ){
                                    $width_bud = '0%';
                                    $width_avan = '80';
                                    $bg_color = 'green';
                                } else if( (($totals['inv']['avancement'] > $totals['inv']['budget']) && $totals['inv']['avancement'] > 0) || (($totals['inv']['avancement'] > 0) && ($totals['inv']['budget'] <= 0)) ){
                                    $color_min = '#F98E8E';
                                    $color_max = '#FF0606';
                                    $bg_color = 'red';
                                    $width_bud = '80%';
                                    $width_avan = (abs($totals['inv']['avancement'])/abs($totals['inv']['budget'])*80);
                                } else {
                                    $width_bud = '80%';
                                    $width_avan = (abs($totals['inv']['avancement'])/abs($totals['inv']['budget'])*80);
                                    $bg_color = 'green';
                                }
                                $width_avan = $width_avan <= 100 ? $width_avan : 100;
                                $width_avan = $width_avan . '%';
                                // ob_clean();
                                // debug($per); exit;
                            ?>
                        </div>
                         <aside class="budget-progress-circle" style="overflow:visible;">
                            <div class="progress-circle progress-circle-yellow">
                                <div class="progress-circle-inner">
                                    <i class="icon-question" aria-hidden="true"></i>
                                    <canvas data-value = "<?php echo $per; ?>" id="myCanvas-2" width="165" height="160" style="" class="canvas-circle"></canvas>
                                    <div class ="progress-value progress-validated"><p><?php echo __d(sprintf($_domain, 'Finance'), 'Budget', true);?></p><span><?php echo number_format((!empty($totals['inv']['budget']) ? $totals['inv']['budget'] : 0), 2, '.', ' '); ?> <?php echo ' '.$bg_currency; ?> </span></div>
                                    <div class ="progress-value progress-engaged"><p><?php echo __d(sprintf($_domain, 'Finance'), 'Engaged', true);?></p><span><?php echo number_format((!empty($totals['inv']['avancement']) ? $totals['inv']['avancement'] : 0), 2, '.', ' '); ?> <?php echo ' '.$bg_currency; ?> </span></div>
                                </div>
                            </div>
                        </aside>
                        <div class="percent-chard" style="display: none">
                            <div style="width: 50%">
                                <?php if($totals['inv']['budget'] < 0){ ?>
                                <div style="height: 10px; width: <?php echo $width_bud ?>; background-color: #ccc; float: right"></div>
                                <?php } else { ?>
                                <div style="height: 10px; width: 0px; background-color: #ccc; float: right"></div>
                                <?php }
                                if($totals['inv']['avancement'] < 0){
                                ?>
                                <div style="margin-top: 25px; height: 10px; float: right; width: <?php echo $width_avan ?>; background-color: <?php echo $bg_color ?>"></div>
                                <?php } ?>
                            </div>
                            <div style="width: 50%; margin-left: 50%;">
                                <?php if($totals['inv']['budget'] >= 0){ ?>
                                <div style="height: 10px; width: <?php echo $width_bud ?>; background-color: #ccc"></div>
                                <?php } else { ?>
                                <div style="height: 10px; width: 0px; background-color: #ccc"></div>
                                <?php }
                                if($totals['inv']['avancement'] >= 0){
                                ?>
                                <div style="margin-top: 25px; height: 10px; width: <?php echo $width_avan ?>; background-color: <?php echo $bg_color ?>"></div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="circle-chard" style="display: none">
                            <aside>
                                <svg class="progress-pie" width="90%" height="30%" role="image" style="margin: 10px">
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
                                    <text class="progress-pie__text" x="50" y="57" text-anchor="middle" font-size="18">
                                        <?php echo $per ?><tspan font-size="15" dy="-5">%</tspan></text>
                                </svg>
                            </aside>
                        </div>
                    </div>
                </div>
                <div id="fon-chard" style="float: left; width: 50%">
                    <h3 class="wd-t1" style="color: #ffb250; margin-bottom: 10px;"><?php echo __d(sprintf($_domain, 'Finance'), 'Budget Operation', true); ?></h3>
                    <div class="chard-content">
                        <div class="budget-chard" style="display: none">
                            <p style="min-width: 200px;"><?php echo __d(sprintf($_domain, 'Finance'), 'Budget', true) .': '. number_format((!empty($totals['fon']['budget']) ? $totals['fon']['budget'] : 0), 2, '.', ' ') . ' '.$bg_currency ?></p>
                            <p style="min-width: 200px;"><?php echo __d(sprintf($_domain, 'Finance'), 'Engaged', true) .': '. number_format((!empty($totals['fon']['avancement']) ? $totals['fon']['avancement'] : 0), 2, '.', ' ') . ' '.$bg_currency ?></p>
                            <?php
                            if(empty($totals['fon']['budget'])){
                                $totals['fon']['budget'] = 0;
                            }
                            if(empty($totals['fon']['avancement'])){
                                $totals['fon']['avancement'] = 0;
                            }
                            if($totals['fon']['budget'] == 0) {
                                $per = 100;
                            } else {
                                $per = round($totals['fon']['avancement']/$totals['fon']['budget'] * 100,2);
                            }
                            $_color_min = '#13FF02';
                            $_color_max = '#15830D';
                            if( $totals['fon']['budget'] == 0 && $totals['fon']['avancement'] == 0 ){
                                $width_bud = '0%';
                                $width_avan = '0';
                                $bg_color = 'green';
                                $per = 0;
                            } else if( $totals['fon']['budget'] == 0 ){
                                $width_bud = '0%';
                                $width_avan = '80';
                                $bg_color = 'green';
                            } else if( (($totals['fon']['avancement'] > $totals['fon']['budget']) && $totals['fon']['avancement'] > 0) || (($totals['fon']['avancement'] > 0) && ($totals['fon']['budget'] <= 0)) ){
                                $_color_min = '#F98E8E';
                                $_color_max = '#FF0606';
                                $bg_color = 'red';
                                $width_bud = '80%';
                                $width_avan = (abs($totals['fon']['avancement'])/abs($totals['fon']['budget'])*80);
                            } else {
                                $width_bud = '80%';
                                $width_avan = (abs($totals['fon']['avancement'])/abs($totals['fon']['budget'])*80);
                                $bg_color = 'green';
                            }
                            $width_avan = $width_avan <= 100 ? $width_avan : 100;
                            $width_avan = $width_avan . '%';
                            ?>
                        </div>
                        <div class="percent-chard" style="display: none">
                            <div style="width: 50%">
                                <?php if($totals['fon']['budget'] < 0){ ?>
                                <div style="height: 10px; width: <?php echo $width_bud ?>; background-color: #ccc; float: right"></div>
                                <?php } else { ?>
                                <div style="height: 10px; width: 0px; background-color: #ccc; float: right"></div>
                                <?php }
                                if($totals['fon']['avancement'] < 0){
                                ?>
                                <div style="margin-top: 25px; height: 10px; float: right; width: <?php echo $width_avan ?>; background-color: <?php echo $bg_color ?>"></div>
                                <?php } ?>
                            </div>
                            <div style="width: 50%; margin-left: 50%;">
                                <?php if($totals['fon']['budget'] >= 0){ ?>
                                <div style="height: 10px; width: <?php echo $width_bud ?>; background-color: #ccc"></div>
                                <?php } else { ?>
                                <div style="height: 10px; width: 0px; background-color: #ccc"></div>
                                <?php }
                                if($totals['fon']['avancement'] >= 0){
                                ?>
                                <div style="margin-top: 25px; height: 10px; width: <?php echo $width_avan ?>; background-color: <?php echo $bg_color ?>"></div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="circle-chard" style="display: none">
                            <div id='gaugeProfit'>
                                <aside>
                                    <svg class="progress-pie" width="90%" height="30%" role="image" style="margin: 10px">
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
                                            <linearGradient id="pprg2" class="progress-pie__gradient">
                                                <stop offset="0%" stop-color="<?php echo $_color_min ?>" />
                                                <stop offset="100%" stop-color="<?php echo $_color_max ?>" />
                                            </linearGradient>
                                        </defs>
                                        <circle class="progress-pie__bg" filter="url(#drop-shadow)" r="50" cx="50" cy="50" fill="grey" opacity="0.3" />
                                        <circle stroke="grey" stroke-width="9" fill="none" r="39.5" cx="50" cy="50" opacity="0.3" filter="url(#inset-shadow)" />
                                        <circle class="progress-pie__ring" stroke="url(#pprg2)" stroke-width="9" stroke-dasharray="<?php echo abs($per/100*248.1858154) ?> 248.1858154" fill="none" r="39.5" cx="50" cy="50" transform="rotate(90, 50, 50)" />
                                        <circle class="progress-pie__inner-disc" fill="grey" opacity="0.2" r="35" cx="50" cy="50" filter="url(#drop-shadow-flat)" />
                                        <text class="progress-pie__text" x="50" y="57" text-anchor="middle" font-size="18"><?php echo $per ?><tspan font-size="15" dy="-5">%</tspan></text>
                                    </svg>
                                </aside>
                            </div>
                        </div>
                        <aside class="budget-progress-circle" style="overflow:visible;">
                            <div class="progress-circle progress-circle-yellow">
                                <div class="progress-circle-inner">
                                    <i class="icon-question" aria-hidden="true"></i>
                                    <canvas data-value = "<?php echo $per; ?>" id="myCanvas-3" width="165" height="160" style="" class="canvas-circle"></canvas>
                                    <div class ="progress-value progress-validated"><p><?php echo __d(sprintf($_domain, 'Finance'), 'Budget', true);?></p><span><?php echo number_format((!empty($totals['fon']['budget']) ? $totals['fon']['budget'] : 0), 2, '.', ' '); ?> <?php echo ' '.$bg_currency; ?></span></div>
                                    <div class ="progress-value progress-engaged"><p><?php echo __d(sprintf($_domain, 'Finance'), 'Engaged', true); ?></p><span><?php echo number_format((!empty($totals['fon']['avancement']) ? $totals['fon']['avancement'] : 0), 2, '.', ' '); ?> <?php echo ' '.$bg_currency; ?></span></div>
                                </div>
                            </div>
                        </aside>

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
                <div class="wd-title">
                    <h3 class="wd-t1"><?php echo __d(sprintf($_domain, 'Finance'), 'Budget Investment', true); ?></h3>
                </div>
                <div id="table-control">
                    <?php
                    echo $this->Form->create('Inv', array(
                        'type' => 'get',
                        'url' => '/' . Router::normalize($this->here)));
                    echo $this->Form->hidden('fon_start');
                    echo $this->Form->hidden('fon_end');
                    ?>
                    <fieldset>
                        <label><?php __('From') ?></label>
                        <div class="input" >
                            <?php
                                echo $this->Form->input('inv_start', array('label' => false, 'div' => false,  "class" => "placeholder", "placeholder" => __("(dd-mm-yyyy)", true), 'onchange' => "validated('inv');", 'value' => isset($invStart) ? date('d-m-Y', $invStart) : '', 'disabled' => !$canModified));
                            ?>
                        </div>
                        <label> <?php __('To') ?> </label>
                        <div id="wd-group-inv">
                            <div class="input" id="wd-end-date-inv">
                                <?php
                                    echo $this->Form->input('inv_end', array('label' => false, 'div' => false,  "class" => "placeholder", "placeholder" => __("(dd-mm-yyyy)", true), 'onchange' => "validated('inv');", 'value' => isset($invEnd) ? date('d-m-Y', $invEnd) : '', 'disabled' => !$canModified));
                                ?>
                            </div>
                            <p style="display: none; clear: both; padding-top: 2px; color: red;" class="wd-error wd-error-inv"><?php echo __('The end date must be greater than start date', true);?></p>
                        </div>
						<?php if($canModified){?>
                        <div class="button" id="wd-submit-inv">
                            <input type="submit" value="OK" id="sutInv" />
                        </div>
						<?php } ?> 
                        <div style="clear:both;"></div>
                    </fieldset>
                    <?php
                    echo $this->Form->end();
                    ?>
                    <?php if( $canModified) {?>
                        <a href="javascript:void(0);" class="btn btn-plus-green" onclick="addNewRow('inv');" title="<?php __('Add an order') ?>"></a>
                    <?php } ?>
                </div>
                <div class="wd-table" id="project_container_1" style="width:100%;height:300px;">
                </div>
                <div id="table-2" style="margin-top: 25px;">
                    <hr />
                    <div class="wd-title">
                        <h3 class="wd-t1"><?php echo __d(sprintf($_domain, 'Finance'), 'Budget Operation', true); ?></h3>
                    </div>
                    <div id="table-control">
                        <?php
                        echo $this->Form->create('Fon', array(
                            'type' => 'get',
                            'url' => '/' . Router::normalize($this->here)));
                        echo $this->Form->hidden('inv_start');
                        echo $this->Form->hidden('inv_end');
                        ?>
                        <fieldset>
                            <label><?php __('From') ?></label>
                            <div class="input" >
                                <?php
									/* #1098 user readonly khong the select date */
                                    echo $this->Form->input('fon_start', array('label' => false, 'div' => false,  "class" => "placeholder", "placeholder" => __("(dd-mm-yyyy)", true), 'onchange' => "validated('fon');", 'value' => isset($fonStart) ? date('d-m-Y', $fonStart) : '', 'disabled' => !$canModified));
                                ?>
                            </div>
                            <label> <?php __('To') ?> </label>
                            <div id="wd-group-fon">
                                <div class="input" id="wd-end-date-fon">
                                    <?php
                                        echo $this->Form->input('fon_end', array('label' => false, 'div' => false,  "class" => "placeholder", "placeholder" => __("(dd-mm-yyyy)", true), 'onchange' => "validated('fon');", 'value' => isset($fonEnd) ? date('d-m-Y', $fonEnd) : '', 'disabled' => !$canModified));
                                    ?>
                                </div>
                                <p style="display: none; clear: both; padding-top: 2px; color: red;" class="wd-error wd-error-fon"><?php echo __('The end date must be greater than start date', true);?></p>
                            </div>
							<?php if($canModified){?>
                            <div class="button" id="wd-submit-fon">
                                <input type="submit" value="OK" id="sutFon" />
                            </div>
							<?php } ?> 
                            <div style="clear:both;"></div>
                        </fieldset>
                        <?php
                        echo $this->Form->end();
                        ?>

                        <?php if( $canModified) {?>
                            <a href="javascript:void(0);" class="btn btn-plus-green" onclick="addNewRow('fon');" title="<?php __('Add an order') ?>"></a>
                        <?php } ?>
                    </div>
                    <div class="wd-table" id="project_container_2" style="width:100%;height:300px;">
                    </div>
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
$columns_1 = array(
    array(
        'id' => 'inv_no.',
        'field' => 'inv_no.',
        'name' => '#',
        'width' => 40,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1
    ),
    array(
        'id' => 'inv_name',
        'field' => 'inv_name',
        'name' => "",
        'width' => isset($history['columnWidth1']['inv_name']) ? (int) $history['columnWidth1']['inv_name'] : 120,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'inv_budget',
        'field' => 'inv_budget',
        'name' => __d(sprintf($_domain, 'Finance'), 'Budget', true),
        'width' => isset($history['columnWidth1']['inv_budget']) ? (int) $history['columnWidth1']['inv_budget'] : 120,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.manDayValue'
    ),
    array(
        'id' => 'inv_avancement',
        'field' => 'inv_avancement',
        'name' => __d(sprintf($_domain, 'Finance'), 'Avancement', true),
        'width' => isset($history['columnWidth1']['inv_avancement']) ? (int) $history['columnWidth1']['inv_avancement'] : 120,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.manDayValue'
    ),
    array(
        'id' => 'inv_percent',
        'field' => 'inv_percent',
        'name' => __('%', true),
        'width' => isset($history['columnWidth1']['inv_percent']) ? (int) $history['columnWidth1']['inv_percent'] : 80,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.percentValue'
    ));
$columnInvYears = array();
$invStYear = date('Y', $invStart);
$invEnYear = date('Y', $invEnd);
while($invStYear <= $invEnYear){
    $columnInvYears[] = array(
        'id' => 'inv_budget_' . $invStYear,
        'field' => 'inv_budget_' . $invStYear,
        'name' => __d(sprintf($_domain, 'Finance'), 'Budget', true),
        'width' => isset($history['columnWidth1']['inv_budget_' . $invStYear]) ? (int) $history['columnWidth1']['inv_budget_' . $invStYear] : 120,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericValue',
        'formatter' => 'Slick.Formatters.manDayValue'
    );
    $columnInvYears[] = array(
        'id' => 'inv_avancement_' . $invStYear,
        'field' => 'inv_avancement_' . $invStYear,
        'name' => __d(sprintf($_domain, 'Finance'), 'Avancement', true),
        'width' => isset($history['columnWidth1']['inv_avancement_' . $invStYear]) ? (int) $history['columnWidth1']['inv_avancement_' . $invStYear] : 120,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericValue',
        'formatter' => 'Slick.Formatters.manDayValue'
    );
    $columnInvYears[] = array(
        'id' => 'inv_percent_' . $invStYear,
        'field' => 'inv_percent_' . $invStYear,
        'name' => __('%', true),
        'width' => isset($history['columnWidth1']['inv_percent_' . $invStYear]) ? (int) $history['columnWidth1']['inv_percent_' . $invStYear] : 80,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.percentValue'
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
$columns_1 = array_merge($columns_1, $columnInvYears, $columnInvAction);
$columns_2 = array(
    array(
        'id' => 'fon_no.',
        'field' => 'fon_no.',
        'name' => '#',
        'width' => 40,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1
    ),
    array(
        'id' => 'fon_name',
        'field' => 'fon_name',
        'name' => "",
        'width' => isset($history['columnWidth2']['fon_name']) ? (int) $history['columnWidth2']['fon_name'] : 120,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'fon_budget',
        'field' => 'fon_budget',
        'name' => __d(sprintf($_domain, 'Finance'), 'Budget', true),
        'width' => isset($history['columnWidth2']['fon_budget']) ? (int) $history['columnWidth2']['fon_budget'] : 120,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.manDayValue'
    ),
    array(
        'id' => 'fon_avancement',
        'field' => 'fon_avancement',
        'name' => __d(sprintf($_domain, 'Finance'), 'Avancement', true),
        'width' => isset($history['columnWidth2']['fon_avancement']) ? (int) $history['columnWidth2']['fon_avancement'] : 120,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.manDayValue'
    ),
    array(
        'id' => 'fon_percent',
        'field' => 'fon_percent',
        'name' => __('%', true),
        'width' => isset($history['columnWidth2']['fon_percent']) ? (int) $history['columnWidth2']['fon_percent'] : 80,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.percentValue'
    ));
$columnFonYears = array();
$fonStYear = date('Y', $fonStart);
$fonEnYear = date('Y', $fonEnd);
while($fonStYear <= $fonEnYear){
    $columnFonYears[] = array(
        'id' => 'fon_budget_' . $fonStYear,
        'field' => 'fon_budget_' . $fonStYear,
        'name' => __d(sprintf($_domain, 'Finance'), 'Budget', true),
        'width' => isset($history['columnWidth2']['fon_budget_' . $fonStYear]) ? (int) $history['columnWidth2']['fon_budget_' . $fonStYear] : 120,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericValue',
        'formatter' => 'Slick.Formatters.manDayValue'
    );
    $columnFonYears[] = array(
        'id' => 'fon_avancement_' . $fonStYear,
        'field' => 'fon_avancement_' . $fonStYear,
        'name' => __d(sprintf($_domain, 'Finance'), 'Avancement', true),
        'width' => isset($history['columnWidth2']['fon_avancement_' . $fonStYear]) ? (int) $history['columnWidth2']['fon_avancement_' . $fonStYear] : 120,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericValue',
        'formatter' => 'Slick.Formatters.manDayValue'
    );
    $columnFonYears[] = array(
        'id' => 'fon_percent_' . $fonStYear,
        'field' => 'fon_percent_' . $fonStYear,
        'name' => __('%', true),
        'width' => isset($history['columnWidth2']['fon_percent_' . $fonStYear]) ? (int) $history['columnWidth2']['fon_percent_' . $fonStYear] : 80,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.percentValue'
    );
    $fonStYear++;
}
$columnFonAction[] = array(
    'id' => 'fon_action.',
    'field' => 'fon_action.',
    'name' => __('Action', true),
    'width' => 70,
    'sortable' => false,
    'resizable' => true,
    'noFilter' => 1,
    'formatter' => 'Slick.Formatters.Action'
);
$columns_2 = array_merge($columns_2, $columnFonYears, $columnFonAction);
$i = 1;
$dataView_1 = $dataView_2 = $totalHeader_1 = $totalHeader_2 = $calPercent_1 = $calPercent_2 = array();
if(!empty($finances['inv'])){
    foreach($finances['inv'] as $id => $name){
        $data = array(
            'id' => $id,
            'inv_no.' => $i++,
            'MetaData' => array()
        );
        $data['project_id'] = $projects['Project']['id'];
        $data['activity_id'] = $projects['Project']['activity_id'];
        $data['company_id'] = $projects['Project']['company_id'];
        $data['inv_name'] = (string) $name;
        $totalBudget = $totalAvancement = $totalPercent = 0;
        $percentYears = array();
        if(!empty($financeDetails[$id])){
            foreach($financeDetails[$id] as $model => $invs){
                if(!isset($totalHeader_1['inv_' . $model])){
                    $totalHeader_1['inv_' . $model] = 0;
                }
                $totalHeader_1['inv_' . $model] += $invs['value'];
                $data['inv_' . $model] = $invs['value'];
                if(!isset($percentYears[$invs['year']][$invs['model']])){
                    $percentYears[$invs['year']][$invs['model']] = 0;
                }
                $percentYears[$invs['year']][$invs['model']] += $invs['value'];
                if(!isset($calPercent_1[$invs['year']][$invs['model']])){
                    $calPercent_1[$invs['year']][$invs['model']] = 0;
                }
                $calPercent_1[$invs['year']][$invs['model']] += $invs['value'];
                if($invs['model'] == 'budget'){
                    $totalBudget += $invs['value'];
                } else {
                    $totalAvancement += $invs['value'];
                }
            }
        }
        if(!empty($percentYears)){
            foreach($percentYears as $year => $percentYear){
                $bud = !empty($percentYear['budget']) ? $percentYear['budget'] : 0;
                $ava = !empty($percentYear['avancement']) ? $percentYear['avancement'] : 0;
                $per = ($bud ==0 ) ? 0 : round($ava/$bud*100, 2);
                $data['inv_percent_' . $year] = $per;
            }
        }
        $totalPercent = ($totalBudget == 0) ? 0 : $totalAvancement/$totalBudget*100;
        if(!isset($totalHeader_1['inv_budget'])){
            $totalHeader_1['inv_budget'] = 0;
        }
        $totalHeader_1['inv_budget'] += $totalBudget;

        if(!isset($totalHeader_1['inv_avancement'])){
            $totalHeader_1['inv_avancement'] = 0;
        }
        $totalHeader_1['inv_avancement'] += $totalAvancement;
        if(!isset($calPercent_1['total']['budget'])){
            $calPercent_1['total']['budget'] = 0;
        }
        $calPercent_1['total']['budget'] += $totalBudget;
        if(!isset($calPercent_1['total']['avancement'])){
            $calPercent_1['total']['avancement'] = 0;
        }
        $calPercent_1['total']['avancement'] += $totalAvancement;

        $data['inv_percent'] = round($totalPercent, 2);
        $data['inv_budget'] = round($totalBudget, 2);
        $data['inv_avancement'] = round($totalAvancement, 2);
        $data['inv_action.'] = '';

        $dataView_1[] = $data;
    }
}
if(!empty($calPercent_1)){
    foreach($calPercent_1 as $key => $val){
        $bud = $val['budget'];
        $ava = $val['avancement'];
        $per = ($bud ==0 ) ? 0 : round($ava/$bud*100, 2);
        if($key == 'total'){
            $totalHeader_1['inv_percent'] = $per;
        } else {
            $totalHeader_1['inv_percent_' . $key] = $per;
        }
    }
}

$j = 1;
if(!empty($finances['fon'])){
    foreach($finances['fon'] as $id => $name){
        $data = array(
            'id' => $id,
            'fon_no.' => $j++,
            'MetaData' => array()
        );
        $data['fon_name'] = (string) $name;
        $data['project_id'] = $projects['Project']['id'];
        $data['activity_id'] = $projects['Project']['activity_id'];
        $data['company_id'] = $projects['Project']['company_id'];
        $totalBudget = $totalAvancement = $totalPercent = 0;
        $percentYears = array();
        if(!empty($financeDetails[$id])){
            foreach($financeDetails[$id] as $model => $fons){
                if(!isset($totalHeader_2['fon_' . $model])){
                    $totalHeader_2['fon_' . $model] = 0;
                }
                $totalHeader_2['fon_' . $model] += $fons['value'];
                $data['fon_' . $model] = $fons['value'];
                if(!isset($percentYears[$fons['year']][$fons['model']])){
                    $percentYears[$fons['year']][$fons['model']] = 0;
                }
                $percentYears[$fons['year']][$fons['model']] += $fons['value'];
                if(!isset($calPercent_2[$fons['year']][$fons['model']])){
                    $calPercent_2[$fons['year']][$fons['model']] = 0;
                }
                $calPercent_2[$fons['year']][$fons['model']] += $fons['value'];
                if($fons['model'] == 'budget'){
                    $totalBudget += $fons['value'];
                } else {
                    $totalAvancement += $fons['value'];
                }
            }
        }
        if( !empty($percentYears) ){
            foreach($percentYears as $year => $percentYear){
                $bud = !empty($percentYear['budget']) ? $percentYear['budget'] : 0;
                $ava = !empty($percentYear['avancement']) ? $percentYear['avancement'] : 0;
                $per = ($bud ==0 ) ? 0 : round($ava/$bud*100, 2);
                $data['fon_percent_' . $year] = $per;
            }
        }
        $totalPercent = ($totalBudget == 0) ? 0 : $totalAvancement/$totalBudget*100;
        if( !isset($totalHeader_2['fon_budget']) ){
            $totalHeader_2['fon_budget'] = 0;
        }
        $totalHeader_2['fon_budget'] += $totalBudget;

        if( !isset($totalHeader_2['fon_avancement']) ){
            $totalHeader_2['fon_avancement'] = 0;
        }
        $totalHeader_2['fon_avancement'] += $totalAvancement;
        if( !isset($calPercent_2['total']['budget']) ){
            $calPercent_2['total']['budget'] = 0;
        }
        $calPercent_2['total']['budget'] += $totalBudget;
        if( !isset($calPercent_2['total']['avancement']) ){
            $calPercent_2['total']['avancement'] = 0;
        }
        $calPercent_2['total']['avancement'] += $totalAvancement;
        $data['fon_percent'] = round($totalPercent, 2);
        $data['fon_budget'] = round($totalBudget, 2);
        $data['fon_avancement'] = round($totalAvancement, 2);
        $data['fon_action.'] = '';
        $dataView_2[] = $data;
    }
}
if( !empty($calPercent_2) ){
    foreach($calPercent_2 as $key => $val){
        $bud = $val['budget'];
        $ava = $val['avancement'];
        $per = ($bud ==0 ) ? 0 : round($ava/$bud*100, 2);
        if($key == 'total'){
            $totalHeader_2['fon_percent'] = $per;
        } else {
            $totalHeader_2['fon_percent_' . $key] = $per;
        }
    }
}
$selectMaps = array();
$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true),
    'Date closing must between %1$s and %2$s' => __('Date closing must between %1$s and %2$s', true)
);
$canModified =  (!empty($canModified) || ($_isProfile && $_canWrite));
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete_finance', '%1$s', '%2$s', '?' => array('fon_start' => date('d-m-Y', $fonStart), 'fon_end' => date('d-m-Y', $fonEnd), 'inv_start' => date('d-m-Y', $invStart), 'inv_end' => date('d-m-Y', $invEnd)))); ?>">Delete</a>
        </div>
    </div>
</div>
<script type="text/javascript">
var wdTable = $('.wd-panel');
var heightTable = $(window).height() - wdTable.offset().top - 40;
wdTable.css({
    height: heightTable,
});
$(window).resize(function(){
    heightTable = $(window).height() - wdTable.offset().top - 40;
    wdTable.css({
        height: heightTable,
    });
});
    var DateValidate = {},dataGrid, ControlGridOne, ControlGridTwo, IuploadComplete = function(json){
        var data = dataGrid.eval('currentEditor');
        data.onComplete(json);
    };
    (function($){
        $(function(){
            var $this = SlickGridCustom,
            headerConsumedRightInv = '<div class="slick-header-columns" style="margin-left: -1px; border-top: none; height: 36px;">',
            headerConsumedRightFon = '<div class="slick-header-columns" style="margin-left: -1px; border-top: none; height: 36px;">',
            highLightInv = '.l2, .l3, .l4',
            highLightFon = '.l2, .l3, .l4',
            invStart = <?php echo json_encode(date('Y', $invStart));?>,
            invEnd = <?php echo json_encode(date('Y', $invEnd));?>,
            fonStart = <?php echo json_encode(date('Y', $fonStart));?>,
            fonEnd = <?php echo json_encode(date('Y', $fonEnd));?>,
            projects = <?php echo !empty($projects['Project']) ? json_encode($projects['Project']) : json_encode(array());?>,
            viewEuro = <?php echo json_encode($viewEuro);?>,
            totalHeader_1 = <?php echo json_encode($totalHeader_1);?>,
            totalHeader_2 = <?php echo json_encode($totalHeader_2);?>,
            totalName = <?php echo json_encode(__d(sprintf($_domain, 'Finance'), 'Total', true));?>;

            projects['activity_id'] = projects['activity_id'] ? projects['activity_id'] : 0;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  <?php echo json_encode($canModified); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;

            var _history = <?php echo empty($history) ? '{}' : json_encode($history) ?>;
            var historyData = new $.z0.data(_history);
            var historyPath = <?php echo json_encode($this->params['url']['url']) ?>;

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
            function resizeHandler2(){
                var _cols = ControlGridTwo.getColumns();
                var _numCols = _cols.length;
                var _gridW = 0;
                var columnWidth = {};
                for (var i=0; i<_numCols; i++) {
                    _gridW += _cols[i].width;
                    columnWidth[_cols[i].id] = _cols[i].width;
                }
                $('.slick-header-columns').css('width', _gridW);
                historyData.set('columnWidth2', columnWidth);
                // call save here
                // **
                saveFilter();
            }
            var saveTimer;
            function saveFilter(){
                clearTimeout(saveTimer);
                saveTimer = setTimeout(function(){
                    $.z0.History.save(historyPath, historyData);
                }, 750);
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
            var  data_1 = <?php echo json_encode($dataView_1); ?>;
            var  data_2 = <?php echo json_encode($dataView_2); ?>;
            var  columns_1 = <?php echo jsonParseOptions($columns_1, array('editor', 'formatter', 'validator')); ?>;
            var  columns_2 = <?php echo jsonParseOptions($columns_2, array('editor', 'formatter', 'validator')); ?>;
            var fieldNewInv = {
                project_id: projects['id'],
                activity_id : projects['activity_id'],
                company_id : projects['company_id'],
                inv_name : '',
                inv_percent: '',
                inv_budget: '',
                inv_avancement: ''
            };
            var fieldNewFon = {
                project_id: projects['id'],
                activity_id : projects['activity_id'],
                company_id : projects['company_id'],
                fon_name : '',
                fon_percent: '',
                fon_budget: '',
                fon_avancement: ''
            };

            var fields_1 = {
                id : {defaulValue : 0},
                project_id : {defaulValue : projects['id'], allowEmpty : false},
                activity_id : {defaulValue : projects['activity_id']},
                company_id : {defaulValue : projects['company_id'], allowEmpty : false},
                inv_name : {defaulValue : '', allowEmpty : false}
            };
            var fields_2 = {
                id : {defaulValue : 0},
                project_id : {defaulValue : projects['id'], allowEmpty : false},
                activity_id : {defaulValue : projects['activity_id']},
                company_id : {defaulValue : projects['company_id'], allowEmpty : false},
                fon_name : {defaulValue : '', allowEmpty : false}
            };
            var leftInv = 5, rightInv = 5, countInv = 1;
            while(invStart <= invEnd){
                fields_1['inv_budget_' + invStart] = {defaulValue : ''};
                fields_1['inv_avancement_' + invStart] = {defaulValue : ''};
                fieldNewInv['inv_budget_' + invStart] = '';
                fieldNewInv['inv_avancement_' + invStart] = '';
                fieldNewInv['inv_percent_' + invStart] = '';
                headerConsumedRightInv += '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'+(leftInv++)+' r'+(rightInv++)+'"></div>';
                headerConsumedRightInv += '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'+(leftInv++)+' r'+(rightInv++)+'"><span>' +invStart+ '</span></div>';
                headerConsumedRightInv += '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'+(leftInv++)+' r'+(rightInv++)+'"></div>';
                if(countInv%2 == 0){
                    var _l = leftInv;
                    highLightInv += ', .l' + (_l-3);
                    highLightInv += ', .l' + (_l-2);
                    highLightInv += ', .l' + (_l-1);
                }
                countInv++;
                invStart++;
            }
            headerConsumedRightInv += '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'+(leftInv++)+' r'+(rightInv++)+'"></div></div>';
            var leftFon = 5, rightFon = 5, countFon = 1;
            while(fonStart <= fonEnd){
                fields_2['fon_budget_' + fonStart] = {defaulValue : ''};
                fields_2['fon_avancement_' + fonStart] = {defaulValue : ''};
                fieldNewFon['fon_budget_' + fonStart] = '';
                fieldNewFon['fon_avancement_' + fonStart] = '';
                fieldNewFon['fon_percent_' + fonStart] = '';
                headerConsumedRightFon += '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'+(leftFon++)+' r'+(rightFon++)+'"></div>';
                headerConsumedRightFon += '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'+(leftFon++)+' r'+(rightFon++)+'"><span>' +fonStart+ '</span></div>';
                headerConsumedRightFon += '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'+(leftFon++)+' r'+(rightFon++)+'"></div>';
                if(countFon%2 == 0){
                    var _l = leftFon;
                    highLightFon += ', .l' + (_l-3);
                    highLightFon += ', .l' + (_l-2);
                    highLightFon += ', .l' + (_l-1);
                }
                countFon++;
                fonStart++;
            }
            headerConsumedRightFon += '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'+(leftFon++)+' r'+(rightFon++)+'"></div></div>';
            $this.onBeforeEdit = function(args){
                var columnId = args.column.id;
                if(columnId){
                    columnId = columnId.substring(0, 3);
                    if(columnId == 'inv'){
                        $this.url =  '<?php echo $html->url(array('action' => 'update_finance', 'inv')); ?>';
                        $this.fields = fields_1;
                    } else {
                        $this.url =  '<?php echo $html->url(array('action' => 'update_finance', 'fon')); ?>';
                        $this.fields = fields_2;
                    }
                } else {
                    return false;
                }
                return true;
            }
            $this.onCellChange = function(args){
                if(args && args.column.id && args.item){
                    var columnId = args.column.id;
                    columnId = columnId.substring(0, 3);
                    var totalBudget = 0, totalAvan = 0, totalPercent = 0;
                    var budgetYears = {}, avanYears = {};
                    $.each(args.item, function(ind, val){
                        val = val ? val : 0;
                        ind = ind.split('_');
                        if(ind.length == 3){
                            if(ind[1] == 'budget'){
                                budgetYears[ind[2]] = parseFloat(val);
                                totalBudget += parseFloat(val);
                            } else if(ind[1] == 'avancement') {
                                avanYears[ind[2]] = parseFloat(val)
                                totalAvan += parseFloat(val);
                            }
                        }
                    });
                    totalPercent = (totalBudget == 0) ? 0 : totalAvan/totalBudget*100;
                    if(budgetYears){
                        $.each(budgetYears, function(y, budVal){
                            var avanVal = avanYears[y] ? avanYears[y] : 0;
                            var perVal = (budVal == 0) ? 0 : avanVal/budVal*100;
                            args.item[columnId + '_percent_' + y] = parseFloat(perVal);
                        });
                    }
                    args.item[columnId + '_percent'] = parseFloat(totalPercent);
                    args.item[columnId + '_budget'] = parseFloat(totalBudget);
                    args.item[columnId + '_avancement'] = parseFloat(totalAvan);
                    /**
                     * Tinh header
                     */
                    var _datas = (columnId == 'inv') ? $.extend(true, {}, data_1) : $.extend(true, {}, data_2);
                    var _totalHeader = {}, _budgetHeader = {}, _avanHeader = {};
                    $.each(_datas, function(key, _data){
                        $.each(_data, function(ind, val){
                            val = val ? val : 0;
                            var _ind = ind.split('_');
                            if(_ind[1] && (_ind[1] == 'budget' || _ind[1] == 'avancement')){
                                if(!_totalHeader[ind]){
                                    _totalHeader[ind] = 0;
                                }
                                _totalHeader[ind] += parseFloat(val);
                                var _key = _ind[2] ? _ind[2] : 'total';
                                if(_ind[1] == 'budget'){
                                    if(!_budgetHeader[_key]){
                                        _budgetHeader[_key] = 0;
                                    }
                                    _budgetHeader[_key] += parseFloat(val);
                                } else if(_ind[1] == 'avancement') {
                                    if(!_avanHeader[_key]){
                                        _avanHeader[_key] = 0;
                                    }
                                    _avanHeader[_key] += parseFloat(val);
                                }
                            }
                        });
                    });
                    if(_budgetHeader){
                        $.each(_budgetHeader, function(key, budVal){
                            var avanVal = _avanHeader[key] ? _avanHeader[key] : 0;
                            var perVal = (budVal == 0) ? 0 : avanVal/budVal*100;
                            if(key == 'total'){
                                _totalHeader[columnId + '_percent'] = perVal;
                            } else {
                                _totalHeader[columnId + '_percent_' + key] = perVal;
                            }
                        });
                    }
                    var ControlGrid = (columnId == 'inv') ? ControlGridOne : ControlGridTwo;
                    if(_totalHeader){
                        $.each(_totalHeader , function(id){
                            var _views = id.split('_');
                            var _symbol = (_views[1] && _views[1] == 'percent') ? '%' : viewEuro;
                            var val = Number(this) ? number_format(Number(this), 2, ',', ' ') + ' ' + _symbol : '';
                            if($(ControlGrid.getHeaderRowColumn(id)).hasClass('row-number')){
                                $(ControlGrid.getHeaderRowColumn(id)).find('.row-number b').html(val);
                            } else {
                                $(ControlGrid.getHeaderRowColumn(id)).html('<span class="row-number"><b>' + val + '</b></span>');
                            }
                        });
                    }
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
            ControlGridOne = $this.init($('#project_container_1'),data_1,columns_1,{
                showHeaderRow: true,
                enableAddRow : $this.canModified,
                frozenColumn: 4
            });
            ControlGridOne.onColumnsResized.subscribe(function (e, args) {
                resizeHandler1();
            });
            ControlGridTwo = $this.init($('#project_container_2'),data_2,columns_2,{
                showHeaderRow: true,
                enableAddRow : $this.canModified,
                frozenColumn: 4
            });
            ControlGridTwo.onColumnsResized.subscribe(function (e, args) {
                resizeHandler2();
            });
            var _ids = 999999999999;
            addNewRow = function(type){
                // var newRow = (type == 'inv') ? $.extend(true, {}, fieldNewInv) : $.extend(true, {}, fieldNewFon);
                var ControlGrid = (type == 'inv') ? ControlGridOne : ControlGridTwo;
                var rowData = ControlGrid.getData().getItems();
                var _length = rowData.length;
                // newRow['id'] = _ids++;
                // ControlGrid.invalidateRow(_length);
                // rowData.splice(_length, 0, newRow);
                // ControlGrid.getData().setItems(rowData);
                // ControlGrid.render();
                ControlGrid.scrollRowIntoView(_length, false);
                $('.row-number').parent().addClass('row-number-custom');
                ControlGrid.gotoCell(_length, 1, true);
            }
            /**
             * Add header phia duoi
             */
            if(totalHeader_1){
                $.each(totalHeader_1 , function(id){
                    var _views = id.split('_');
                    var _symbol = (_views[1] && _views[1] == 'percent') ? '%' : viewEuro;
                    var val = Number(this) ? number_format(Number(this), 2, ',', ' ') + ' ' + _symbol : '';
                    $(ControlGridOne.getHeaderRowColumn(id)).html('<span class="row-number"><b>' + val + '</b></span>');
                });
            }
            if(totalHeader_2){
                $.each(totalHeader_2 , function(id){
                    var _views = id.split('_');
                    var _symbol = (_views[1] && _views[1] == 'percent') ? '%' : viewEuro;
                    var val = Number(this) ? number_format(Number(this), 2, ',', ' ') + ' ' + _symbol : '';
                    $(ControlGridTwo.getHeaderRowColumn(id)).html('<span class="row-number"><b>' + val + '</b></span>');
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
                    + '<div class="slick-headerrow-column l3 r3 gs-custom-cell-euro-header border-euro-custom"><span>' +totalName+ '</span></div>'
                    + '<div class="slick-headerrow-column l4 r4 gs-custom-cell-euro-header border-euro-custom"></div>'
              + '</div>';
            $('#project_container_1').find('.slick-header-columns-left').before(headerConsumedLeft);
            $('#project_container_1').find('.slick-header-columns-right').before(headerConsumedRightInv);
            $('#project_container_1 .slick-header-columns').find(highLightInv).addClass('headerHighLight');
            $('#project_container_1').height($('#project_container_1').height() + 36);

            $('#project_container_2').find('.slick-header-columns-left').before(headerConsumedLeft);
            $('#project_container_2').find('.slick-header-columns-right').before(headerConsumedRightFon);
            $('#project_container_2 .slick-header-columns').find(highLightFon).addClass('headerHighLight');
            $('#project_container_2').height($('#project_container_2').height() + 36);

            /**
             * Handle date time
             */
            $("#InvInvStart, #InvInvEnd, #FonFonStart, #FonFonEnd").datepicker({
                dateFormat      : 'dd-mm-yy'
            });
        });

    })(jQuery);
    function validated(checkType){
        var _start = (checkType == 'inv') ? $('#InvInvStart').val().toString() : $('#FonFonStart').val().toString() ;
        _start = _start.split('-');
        var myStartDate = new Date(_start[2],_start[1],_start[0]);
        _start = Number(myStartDate);

        var _end = (checkType == 'inv') ? $('#InvInvEnd').val().toString() : $('#FonFonEnd').val().toString() ;
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
    if(document.getElementById('myCanvas')){
        var prog = draw_progress('myCanvas');
    } 
    if(document.getElementById('myCanvas-2')){
        var prog = draw_progress('myCanvas-2');
    } 
    if(document.getElementById('myCanvas-3')){
        var prog = draw_progress('myCanvas-3');
    }
</script>
