<style type="text/css">
    #table-control{
        margin: 0 0 0 -10px !important;
    }
    .absence-fixed th,.absence-fixed td.st{
        border-right : 1px solid #fff;
        color: #fff;
        text-align: left;
    }
    .absence-fixed .st a{
        color: #fff;
    }
    .absence-fixed .st strong{
        font-size: 0.8em;
        color : #FE4040;
    }
    #absence-scroll {
        overflow-x: scroll;
    }
    .ch-absen-validation{
        background-color: #F0F0F0;
    }
    .monday_am{
        border-left: 2px solid red;
    }
    #absence-wrapper {
        margin: 0 !important;
    }
    #absence-table tr td.ch-absen-validation{background-color: #c3dd8c;}
    #absence-wrapper .absence-fixed{ width: 99% !important;}
    #thColID{ width:8%; } #thColEmployee{ width:70%;}
    #absence th.colThDay{min-width:172px;max-width:172px;width:172px;overflow:hidden;}
    .am, .pm{
        overflow:hidden;
        padding-left: 0;
        padding-right: 0;
    }
    .am span, .pm span{
        width:100%;
        word-break:break-all;
    }
    .absence-fixed tbody tr {
      border: 1px solid #ccc;
    }
    .absence-fixed th {
      background: url(../img/front/bg-head-table.png) repeat-x #64a3c7;
      border-right: 1px solid #fff;
    }
    .absence-fixed tr th {
        padding: 5px;
      text-align: center;
      vertical-align: middle;
      border: 1px solid #fff;
    }
    .absence-fixed tbody td {
        border-right: 1px solid #ccc;
        text-align: right;
        vertical-align: middle;
        padding: 5px;
    }
    .absence-fixed td.st {
      background: url(../img/front/bg-head-table.png) repeat-x #5588B6;
      border: 1px solid #CACACA;
      color: #fff;
      vertical-align: middle;
      padding-left: 6px;
    }
    .absence-fixed .no{
        text-align: center;
    }
    .rp-waiting span{
        background-color: #E47E0A;
    }
    .absence-fixed tbody td span {
      padding: 3px;
      display: block;
    }
    .absence-fixed tbody td.ct {
      text-align: center;
      background-color: #E8F0FA;
      font-weight: bold;
    }
    .rp-holiday span {
      background-color: #ffff00;
    }
    .absence-fixed td.ui-selected {
      background: none repeat scroll 0 0 #F39814;
      color: white;
    }
    .fixed {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 9999;
        background: #f0f0f0;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.6);
        margin: 0 !important;
    }
    #menu {
        margin-bottom: 20px !important;
    }
    #table-control td {
        padding-top: 5px;
        vertical-align: middle;
    }
    #auto-cell {
        padding: 2px;
        text-align: center;
    }
    #profit{
        padding: 6px !important;
        border: solid 1px #c0c0c0 !important;
    }
    #table-freezer {
        width: 520px;
        float: left;
        table-layout:fixed;
    }
    #table-scroller {
        overflow-x: hidden;
        overflow-y: auto;
    }
    #table-scroller table {
        float: left;
        width: auto;
    }
    .absence-fixed tbody td{
        min-width: 120px;
        max-width: 130px;
    }
    .absence-fixed tr th{
        min-width: 120px;
        max-width: 130px;
    }
    .table-content tbody td {
        min-width: 120px;
        max-width: 130px;
    }
    #wd-container-footer{
        display: none;
    }
    body{
        overflow: hidden;
    }
    .task_blue {
        background-image: url(/img/extjs/icon-square.png);
        background-repeat: no-repeat;
        min-height: 16px;
    }
    .task_red {
        background-image: url(/img/extjs/icon-triangle.png);
        background-repeat: no-repeat;
        min-height: 16px;
    }
    .order p{
        min-height: 16px;
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
    #sales-purchases{
        font-weight: bold;
        /*font-size: 16px;
        line-height: 13px;*/
        background: #ccc;
    }
    #data-sales-purchases{
        background: #ccc;
        font-weight: bold;
    }
	.wd-tab{
		max-width: 1920px;
	}
</style>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
             <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"> <div class="wd-panel">
            <div class="wd-list-project">
                <div class="wd-title">
                    <?php
                    $langCode = Configure::read('Config.langCode');
                    $fields = ($langCode == 'fr') ? 'name_fre' : 'name_eng';
                    $menu = ClassRegistry::init('Menu')->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => $employee_info['Company']['id'],
                            'model' => 'project',
                            'controllers' => 'project_budget_fiscals',
                            'functions' => 'index',
                        ),
                        'fields' => array('id', 'name_eng', 'name_fre'),
                        'order' => array('id' => 'DESC')
                    ));
                    ?>
                    <h2 class="wd-t1"><?php echo (!empty($menu) ? $menu['Menu'][$fields] : __("FY Budget", true)) . ': ' . $projects['Project']['project_name']; ?></h2>
                    <table id="table-control" class="wd-title">
                        <tr>
                            <td>
                                <fieldset>
                                    <div class="input">
                                    <?php
                                        echo $this->Form->select('profit', $paths, $profiltId, array('empty' => $companyName, 'name' => 'profit', 'escape' => false));
                                        $md = !empty($employee_info['Company']['unit']) ? $employee_info['Company']['unit'] : 'M.D';
                                    ?>
                                    </div>
                                    <select style="margin-right:3px; padding: 6px;float: left;margin-left: 6px; margin-top: -1px;" class="wd-customs" id="viewFollow">
                                        <option value="man-day" <?php echo ($display == 'man-day') ? 'selected="selected"' : '';?>><?php echo  __($md, true)?></option>
                                        <option value="euro" <?php echo ($display == 'euro') ? 'selected="selected"' : '';?>><?php echo  __($budget_settings, true)?></option>
                                    </select>
                                </fieldset>
                            </td>
                        </tr>
                    </table>
                </div>
                <div id="message-place">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    $lastTwoYear = $year - 2;
                    $lastOneYear = $year - 1;
                    $nextOneYear = $year + 1;
                    $nextTwoYear = $year + 2;
                    $viewChar = ($display == 'man-day') ? __($md, true) : __($budget_settings, true);
                    // tinh toan hien thi. ( neu nhu khong co gia tri hoac bang 0 thi khong hien thi)
                    $checkDisplayLastTwoYear = false;
                    if( (!empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$lastTwoYear]) && $saleValues['order'][$lastTwoYear] != 0)
                        || (!empty($saleValues) && isset($saleValues['toBill']) && !empty($saleValues['toBill']) && !empty($saleValues['toBill'][$lastTwoYear]) && $saleValues['toBill'][$lastTwoYear] != 0)
                        || (!empty($saleValues) && isset($saleValues['billed']) && !empty($saleValues['billed']) && !empty($saleValues['billed'][$lastTwoYear]) && $saleValues['billed'][$lastTwoYear] != 0)
                        || (!empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$lastTwoYear]) && $purchaseValues['order'][$lastTwoYear] != 0 )
                        || (!empty($purchaseValues) && isset($purchaseValues['toBill']) && !empty($purchaseValues['toBill']) && !empty($purchaseValues['toBill'][$lastTwoYear]) && $purchaseValues['toBill'][$lastTwoYear]!= 0)
                        || (!empty($purchaseValues) && isset($purchaseValues['billed']) && !empty($purchaseValues['billed']) && !empty($purchaseValues['billed'][$lastTwoYear]) && $purchaseValues['billed'][$lastTwoYear] != 0) ){
                        $checkDisplayLastTwoYear = true;
                    }
                    $checkDisplayLastOneYear = false;
                    if( (!empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$lastOneYear]) && $saleValues['order'][$lastOneYear] != 0)
                        || (!empty($saleValues) && isset($saleValues['toBill']) && !empty($saleValues['toBill']) && !empty($saleValues['toBill'][$lastOneYear]) && $saleValues['toBill'][$lastOneYear] != 0)
                        || (!empty($saleValues) && isset($saleValues['billed']) && !empty($saleValues['billed']) && !empty($saleValues['billed'][$lastOneYear]) && $saleValues['billed'][$lastOneYear] != 0)
                        || (!empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$lastOneYear]) && $purchaseValues['order'][$lastOneYear] != 0 )
                        || (!empty($purchaseValues) && isset($purchaseValues['toBill']) && !empty($purchaseValues['toBill']) && !empty($purchaseValues['toBill'][$lastOneYear]) && $purchaseValues['toBill'][$lastOneYear]!= 0)
                        || (!empty($purchaseValues) && isset($purchaseValues['billed']) && !empty($purchaseValues['billed']) && !empty($purchaseValues['billed'][$lastOneYear]) && $purchaseValues['billed'][$lastOneYear] != 0) ){
                        $checkDisplayLastOneYear = true;
                    }
                    $checkDisplayYear = false;
                    if( (!empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$year]) && $saleValues['order'][$year] != 0)
                        || (!empty($saleValues) && isset($saleValues['toBill']) && !empty($saleValues['toBill']) && !empty($saleValues['toBill'][$year]) && $saleValues['toBill'][$year] != 0)
                        || (!empty($saleValues) && isset($saleValues['billed']) && !empty($saleValues['billed']) && !empty($saleValues['billed'][$year]) && $saleValues['billed'][$year] != 0)
                        || (!empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$year]) && $purchaseValues['order'][$year] != 0 )
                        || (!empty($purchaseValues) && isset($purchaseValues['toBill']) && !empty($purchaseValues['toBill']) && !empty($purchaseValues['toBill'][$year]) && $purchaseValues['toBill'][$year]!= 0)
                        || (!empty($purchaseValues) && isset($purchaseValues['billed']) && !empty($purchaseValues['billed']) && !empty($purchaseValues['billed'][$year]) && $purchaseValues['billed'][$year] != 0) ){
                        $checkDisplayYear = true;
                    }
                    $checkDisplayNextOneYear = false;
                    if( (!empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$nextOneYear]) && $saleValues['order'][$nextOneYear] != 0)
                        || (!empty($saleValues) && isset($saleValues['toBill']) && !empty($saleValues['toBill']) && !empty($saleValues['toBill'][$nextOneYear]) && $saleValues['toBill'][$nextOneYear] != 0)
                        || (!empty($saleValues) && isset($saleValues['billed']) && !empty($saleValues['billed']) && !empty($saleValues['billed'][$nextOneYear]) && $saleValues['billed'][$nextOneYear] != 0)
                        || (!empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$nextOneYear]) && $purchaseValues['order'][$nextOneYear] != 0 )
                        || (!empty($purchaseValues) && isset($purchaseValues['toBill']) && !empty($purchaseValues['toBill']) && !empty($purchaseValues['toBill'][$nextOneYear]) && $purchaseValues['toBill'][$nextOneYear]!= 0)
                        || (!empty($purchaseValues) && isset($purchaseValues['billed']) && !empty($purchaseValues['billed']) && !empty($purchaseValues['billed'][$nextOneYear]) && $purchaseValues['billed'][$nextOneYear] != 0) ){
                        $checkDisplayNextOneYear = true;
                    }
                    $checkDisplayNextTwoYear = false;
                    if( (!empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$nextTwoYear]) && $saleValues['order'][$nextTwoYear] != 0)
                        || (!empty($saleValues) && isset($saleValues['toBill']) && !empty($saleValues['toBill']) && !empty($saleValues['toBill'][$nextTwoYear]) && $saleValues['toBill'][$nextTwoYear] != 0)
                        || (!empty($saleValues) && isset($saleValues['billed']) && !empty($saleValues['billed']) && !empty($saleValues['billed'][$nextTwoYear]) && $saleValues['billed'][$nextTwoYear] != 0)
                        || (!empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$nextTwoYear]) && $purchaseValues['order'][$nextTwoYear] != 0 )
                        || (!empty($purchaseValues) && isset($purchaseValues['toBill']) && !empty($purchaseValues['toBill']) && !empty($purchaseValues['toBill'][$nextTwoYear]) && $purchaseValues['toBill'][$nextTwoYear]!= 0)
                        || (!empty($purchaseValues) && isset($purchaseValues['billed']) && !empty($purchaseValues['billed']) && !empty($purchaseValues['billed'][$nextTwoYear]) && $purchaseValues['billed'][$nextTwoYear] != 0) ){
                        $checkDisplayNextTwoYear = true;
                    }
                    // tinh toan circle.
                    $totalSales = $totalPurchase = 0;
                    if(!empty($saleValues) && !empty($saleValues['order'])){
                        foreach ($saleValues['order'] as $value) {
                            $totalSales += $value;
                        }
                    }
                    if(!empty($purchaseValues) && !empty($purchaseValues['order'])){
                        foreach ($purchaseValues['order'] as $value) {
                            $totalPurchase += $value;
                        }
                    }
                    ?>
                </div>
                <div id="scrollTopAbsence"><div id="scrollTopAbsenceContent"></div></div>
                <br clear="all"  />
                <div class="wd-table" id="project_container" style="width:100%; margin-left: 5px">
                    <table class="absence-fixed" id="table-freezer">
                        <thead>
                        <?php if($display == 'euro'): ?>
                        <tr>
                            <td></td>
                            <td colspan="3">
                                <?php
                                $per = 0;
                                $color_min = '#13FF02';
                                $color_max = '#15830D';
                                if($totalSales != 0){
                                    $per = round($totalPurchase/$totalSales * 100, 2);
                                }
                                if($per > 100){
                                    $color_min = '#F98E8E';
                                    $color_max = '#FF0606';
                                }
                                ?>
                                <div id="total-circle">
                                    <aside style="border-left: 1px solid #ccc;">
                                        <svg class="progress-pie" width="35%" height="15%" role="image" style="margin-bottom: -30px">
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
                                                <linearGradient id="pprgtotal" class="progress-pie__gradient">
                                                    <stop offset="0%" stop-color="<?php echo $color_min ?>" />
                                                    <stop offset="100%" stop-color="<?php echo $color_max ?>" />
                                                </linearGradient>
                                            </defs>
                                            <circle class="progress-pie__bg" filter="url(#drop-shadow)" r="50" cx="50" cy="50" fill="grey" opacity="0.3" />
                                            <circle stroke="grey" stroke-width="9" fill="none" r="39.5" cx="50" cy="50" opacity="0.3" filter="url(#inset-shadow)" />
                                            <circle class="progress-pie__ring" stroke="url(#pprgtotal)" stroke-width="9" stroke-dasharray="<?php echo abs($per/100*248.1858154) ?> 248.1858154" fill="none" r="39.5" cx="50" cy="50" transform="rotate(90, 50, 50)" />
                                            <circle class="progress-pie__inner-disc" fill="grey" opacity="0.2" r="35" cx="50" cy="50" filter="url(#drop-shadow-flat)" />
                                            <text class="progress-pie__text" x="50" y="57" text-anchor="middle" font-size="18"><?php echo $per ?><tspan font-size="15" dy="-5">%</tspan></text>
                                        </svg>
                                    </aside>
                                    <label style="position: fixed;margin-top: -20px;margin-left: 10px;font-size: 14px;font-weight: bold; color: #8c8c8c;"><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Purchase', true) . '/' . __d(sprintf($_domain, 'FY_Budget'), 'Sale', true) ?></label>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td style="width: 130px !important"></td>
                            <th style="width: 390px !important" colspan="3"><?php echo __('Total', true);?></th>
                        </tr>
                        <?php if($display == 'euro'):?>
                        <tr>
                            <td></td>
                            <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Order', true);?></th>
                            <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'To bill', true);?></th>
                            <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Paid', true);?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr id="sales" style="width: 480px !important">
                            <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Sale', true);?></th>
                            <td class="saleOrder"><?php echo '0,00';?></td>
                            <td class="saleToBill"><?php echo '0,00';?></td>
                            <td class="saleBilled"><?php echo '0,00';?></td>
                        </tr>
                        <tr id="purchases" style="width: 480px !important">
                            <th style="border: none"><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Purchase', true);?></th>
                            <td class="saleOrder"><?php echo '0,00';?></td>
                            <td class="saleToBill"><?php echo '0,00';?></td>
                            <td class="saleBilled"><?php echo '0,00';?></td>
                        </tr>
                        <tr id="sales-purchases" style="width: 480px !important">
                            <th style="background: #ccc;font-weight: bold;border: 1px solid #ccc;"><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Sale-Purchase', true);?></th>
                            <td class="saleOrder"><?php echo '0,00';?></td>
                            <td class="saleToBill"><?php echo '0,00';?></td>
                            <td class="saleBilled"><?php echo '0,00';?></td>
                        </tr>
                        <tr id="xxxx" style="width: 480px !important">
                            <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Xxxx', true);?></th>
                            <td class="saleOrder"></td>
                            <td class="saleToBill"></td>
                            <td class="saleBilled"></td>
                        </tr>
                        <?php else:?>
                        </thead>
                        <tbody>
                        <?php endif;?>
                        <?php if($display != 'euro'):?>
                        <tr>
                            <td></td>
                            <th><?php echo __('Provisional', true);?></th>
                            <th><?php echo __('Workload', true);?></th>
                            <th><?php echo __('Consumed', true);?></th>
                        </tr>
                        <tr id="internals">
                            <th><?php echo __('Internal Cost', true);?></th>
                            <td class="internalPro"><?php echo '0,00';?></td>
                            <td class="internalWor"><?php echo '0,00';?></td>
                            <td class="internalCon"><?php echo '0,00';?></td>
                        </tr>
                        <tr id="externals">
                            <th><?php echo __('External Cost', true);?></th>
                            <td class="externalPro"><?php echo '0,00';?></td>
                            <td class="externalWor"><?php echo '0,00';?></td>
                            <td class="externalCon"><?php echo '0,00';?></td>
                        </tr>
                        <?php endif;?>
                        </tbody>
                    </table>
                    <div id="table-scroller">
                        <table class="table-content absence-fixed">
                            <thead>
                            <?php if($display == 'euro'): ?>
                            <tr>
                                <?php if(($displayLastTwoYear && $display != 'euro') || ($checkDisplayLastTwoYear && $displayLastTwoYear && $display == 'euro')) :?>
                                <td colspan="3">
                                    <?php
                                    $a = !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$lastTwoYear]) ?  $saleValues['order'][$lastTwoYear] : 0;
                                    $b = !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$lastTwoYear]) ?  $purchaseValues['order'][$lastTwoYear] : 0;
                                    $per = 0;
                                    $color_min = '#13FF02';
                                    $color_max = '#15830D';
                                    if($a != 0){
                                        $per = round($b/$a * 100, 2);
                                    }
                                    if($per > 100){
                                        $color_min = '#F98E8E';
                                        $color_max = '#FF0606';
                                    }
                                    ?>
                                    <div class="circle-chard">
                                        <aside style="border-left: 1px solid #ccc;">
                                            <svg class="progress-pie" width="30%" height="15%" role="image" style="margin-bottom: -30px">
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
                                                    <linearGradient id="pprglasttwoyear" class="progress-pie__gradient">
                                                        <stop offset="0%" stop-color="<?php echo $color_min ?>" />
                                                        <stop offset="100%" stop-color="<?php echo $color_max ?>" />
                                                    </linearGradient>
                                                </defs>
                                                <circle class="progress-pie__bg" filter="url(#drop-shadow)" r="50" cx="50" cy="50" fill="grey" opacity="0.3" />
                                                <circle stroke="grey" stroke-width="9" fill="none" r="39.5" cx="50" cy="50" opacity="0.3" filter="url(#inset-shadow)" />
                                                <circle class="progress-pie__ring" stroke="url(#pprglasttwoyear)" stroke-width="9" stroke-dasharray="<?php echo abs($per/100*248.1858154) ?> 248.1858154" fill="none" r="39.5" cx="50" cy="50" transform="rotate(90, 50, 50)" />
                                                <circle class="progress-pie__inner-disc" fill="grey" opacity="0.2" r="35" cx="50" cy="50" filter="url(#drop-shadow-flat)" />
                                                <text class="progress-pie__text" x="50" y="57" text-anchor="middle" font-size="18"><?php echo $per ?><tspan font-size="15" dy="-5">%</tspan></text>
                                            </svg>
                                        </aside>
                                    </div>
                                </td>
                                <?php endif;?>
                                <?php if(($displayLastOneYear && $display != 'euro') || ($checkDisplayLastOneYear && $displayLastOneYear && $display == 'euro')) :?>
                                <td colspan="3">
                                    <?php
                                    $a = !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$lastOneYear]) ?  $saleValues['order'][$lastOneYear] : 0;
                                    $b = !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$lastOneYear]) ?  $purchaseValues['order'][$lastOneYear] : 0;
                                    $per = 0;
                                    $color_min = '#13FF02';
                                    $color_max = '#15830D';
                                    if($a != 0){
                                        $per = round($b/$a * 100, 2);
                                    }
                                    if($per > 100){
                                        $color_min = '#F98E8E';
                                        $color_max = '#FF0606';
                                    }
                                    ?>
                                    <div class="circle-chard">
                                        <aside style="border-left: 1px solid #ccc;">
                                            <svg class="progress-pie" width="30%" height="15%" role="image" style="margin-bottom: -30px">
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
                                                    <linearGradient id="pprglastoneyear" class="progress-pie__gradient">
                                                        <stop offset="0%" stop-color="<?php echo $color_min ?>" />
                                                        <stop offset="100%" stop-color="<?php echo $color_max ?>" />
                                                    </linearGradient>
                                                </defs>
                                                <circle class="progress-pie__bg" filter="url(#drop-shadow)" r="50" cx="50" cy="50" fill="grey" opacity="0.3" />
                                                <circle stroke="grey" stroke-width="9" fill="none" r="39.5" cx="50" cy="50" opacity="0.3" filter="url(#inset-shadow)" />
                                                <circle class="progress-pie__ring" stroke="url(#pprglastoneyear)" stroke-width="9" stroke-dasharray="<?php echo abs($per/100*248.1858154) ?> 248.1858154" fill="none" r="39.5" cx="50" cy="50" transform="rotate(90, 50, 50)" />
                                                <circle class="progress-pie__inner-disc" fill="grey" opacity="0.2" r="35" cx="50" cy="50" filter="url(#drop-shadow-flat)" />
                                                <text class="progress-pie__text" x="50" y="57" text-anchor="middle" font-size="18"><?php echo $per ?><tspan font-size="15" dy="-5">%</tspan></text>
                                            </svg>
                                        </aside>
                                    </div>
                                </td>
                                <?php endif;?>
                                <?php if(($displayYear && $display != 'euro') || ($checkDisplayYear && $displayYear && $display == 'euro')) :?>
                                <td colspan="3">
                                    <?php
                                    $a = !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$year]) ?  $saleValues['order'][$year] : 0;
                                    $b = !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$year]) ?  $purchaseValues['order'][$year] : 0;
                                    $per = 0;
                                    $color_min = '#13FF02';
                                    $color_max = '#15830D';
                                    if($a != 0){
                                        $per = round($b/$a * 100, 2);
                                    }
                                    if($per > 100){
                                        $color_min = '#F98E8E';
                                        $color_max = '#FF0606';
                                    }
                                    ?>
                                    <div class="circle-chard">
                                        <aside style="border-left: 1px solid #ccc;">
                                            <svg class="progress-pie" width="30%" height="15%" role="image" style="margin-bottom: -30px">
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
                                                    <linearGradient id="pprgyear" class="progress-pie__gradient">
                                                        <stop offset="0%" stop-color="<?php echo $color_min ?>" />
                                                        <stop offset="100%" stop-color="<?php echo $color_max ?>" />
                                                    </linearGradient>
                                                </defs>
                                                <circle class="progress-pie__bg" filter="url(#drop-shadow)" r="50" cx="50" cy="50" fill="grey" opacity="0.3" />
                                                <circle stroke="grey" stroke-width="9" fill="none" r="39.5" cx="50" cy="50" opacity="0.3" filter="url(#inset-shadow)" />
                                                <circle class="progress-pie__ring" stroke="url(#pprgyear)" stroke-width="9" stroke-dasharray="<?php echo abs($per/100*248.1858154) ?> 248.1858154" fill="none" r="39.5" cx="50" cy="50" transform="rotate(90, 50, 50)" />
                                                <circle class="progress-pie__inner-disc" fill="grey" opacity="0.2" r="35" cx="50" cy="50" filter="url(#drop-shadow-flat)" />
                                                <text class="progress-pie__text" x="50" y="57" text-anchor="middle" font-size="18"><?php echo $per ?><tspan font-size="15" dy="-5">%</tspan></text>
                                            </svg>
                                        </aside>
                                    </div>
                                </td>
                                <?php endif;?>
                                <?php if(($displayNextOneYear && $display != 'euro') || ($checkDisplayNextOneYear && $displayNextOneYear && $display == 'euro')) :?>
                                <td colspan="3">
                                    <?php
                                    $a = !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$nextOneYear]) ?  $saleValues['order'][$nextOneYear] : 0;
                                    $b = !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$nextOneYear]) ?  $purchaseValues['order'][$nextOneYear] : 0;
                                    $per = 0;
                                    $color_min = '#13FF02';
                                    $color_max = '#15830D';
                                    if($a != 0){
                                        $per = round($b/$a * 100, 2);
                                    }
                                    if($per > 100){
                                        $color_min = '#F98E8E';
                                        $color_max = '#FF0606';
                                    }
                                    ?>
                                    <div class="circle-chard">
                                        <aside style="border-left: 1px solid #ccc;">
                                            <svg class="progress-pie" width="30%" height="15%" role="image" style="margin-bottom: -30px">
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
                                                    <linearGradient id="pprgnextoneyear" class="progress-pie__gradient">
                                                        <stop offset="0%" stop-color="<?php echo $color_min ?>" />
                                                        <stop offset="100%" stop-color="<?php echo $color_max ?>" />
                                                    </linearGradient>
                                                </defs>
                                                <circle class="progress-pie__bg" filter="url(#drop-shadow)" r="50" cx="50" cy="50" fill="grey" opacity="0.3" />
                                                <circle stroke="grey" stroke-width="9" fill="none" r="39.5" cx="50" cy="50" opacity="0.3" filter="url(#inset-shadow)" />
                                                <circle class="progress-pie__ring" stroke="url(#pprgnextoneyear)" stroke-width="9" stroke-dasharray="<?php echo abs($per/100*248.1858154) ?> 248.1858154" fill="none" r="39.5" cx="50" cy="50" transform="rotate(90, 50, 50)" />
                                                <circle class="progress-pie__inner-disc" fill="grey" opacity="0.2" r="35" cx="50" cy="50" filter="url(#drop-shadow-flat)" />
                                                <text class="progress-pie__text" x="50" y="57" text-anchor="middle" font-size="18"><?php echo $per ?><tspan font-size="15" dy="-5">%</tspan></text>
                                            </svg>
                                        </aside>
                                    </div>
                                </td>
                                <?php endif;?>
                                <?php if(($displayNextTwoYear && $display != 'euro') || ($checkDisplayNextTwoYear && $displayNextTwoYear && $display == 'euro')) :?>
                                <td colspan="3">
                                    <?php
                                    $a = !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$nextTwoYear]) ?  $saleValues['order'][$nextTwoYear] : 0;
                                    $b = !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$nextTwoYear]) ?  $purchaseValues['order'][$nextTwoYear] : 0;
                                    $per = 0;
                                    $color_min = '#13FF02';
                                    $color_max = '#15830D';
                                    if($a != 0){
                                        $per = round($b/$a * 100, 2);
                                    }
                                    if($per > 100){
                                        $color_min = '#F98E8E';
                                        $color_max = '#FF0606';
                                    }
                                    ?>
                                    <div class="circle-chard">
                                        <aside style="border-left: 1px solid #ccc;">
                                            <svg class="progress-pie" width="30%" height="15%" role="image" style="margin-bottom: -30px">
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
                                                    <linearGradient id="pprgnexttwoyear" class="progress-pie__gradient">
                                                        <stop offset="0%" stop-color="<?php echo $color_min ?>" />
                                                        <stop offset="100%" stop-color="<?php echo $color_max ?>" />
                                                    </linearGradient>
                                                </defs>
                                                <circle class="progress-pie__bg" filter="url(#drop-shadow)" r="50" cx="50" cy="50" fill="grey" opacity="0.3" />
                                                <circle stroke="grey" stroke-width="9" fill="none" r="39.5" cx="50" cy="50" opacity="0.3" filter="url(#inset-shadow)" />
                                                <circle class="progress-pie__ring" stroke="url(#pprgnexttwoyear)" stroke-width="9" stroke-dasharray="<?php echo abs($per/100*248.1858154) ?> 248.1858154" fill="none" r="39.5" cx="50" cy="50" transform="rotate(90, 50, 50)" />
                                                <circle class="progress-pie__inner-disc" fill="grey" opacity="0.2" r="35" cx="50" cy="50" filter="url(#drop-shadow-flat)" />
                                                <text class="progress-pie__text" x="50" y="57" text-anchor="middle" font-size="18"><?php echo $per ?><tspan font-size="15" dy="-5">%</tspan></text>
                                            </svg>
                                        </aside>
                                    </div>
                                </td>
                                <?php endif;?>
                            </tr>
                            <?php endif;?>
                            <tr>
                                <?php if(($displayLastTwoYear && $display != 'euro') || ($checkDisplayLastTwoYear && $displayLastTwoYear && $display == 'euro')) :?>
                                <th colspan="3"><?php echo $year-2;?></th>
                                <?php endif;?>
                                <?php if(($displayLastOneYear && $display != 'euro') || ($checkDisplayLastOneYear && $displayLastOneYear && $display == 'euro')) :?>
                                <th colspan="3"><?php echo $year-1;?></th>
                                <?php endif;?>
                                <?php if(($displayYear && $display != 'euro') || ($checkDisplayYear && $displayYear && $display == 'euro')) :?>
                                <th colspan="3"><?php echo $year;?></th>
                                <?php endif;?>
                                <?php if(($displayNextOneYear && $display != 'euro') || ($checkDisplayNextOneYear && $displayNextOneYear && $display == 'euro')) :?>
                                <th colspan="3"><?php echo $year+1;?></th>
                                <?php endif;?>
                                <?php if(($displayNextTwoYear && $display != 'euro') || ($checkDisplayNextTwoYear && $displayNextTwoYear && $display == 'euro')) :?>
                                <th colspan="3"><?php echo $year+2;?></th>
                                <?php endif;?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $totalXxxx = 0; ?>
                            <?php if($display == 'euro'):?>
                            <tr>
                                <?php if($displayLastTwoYear && $checkDisplayLastTwoYear):?>
                                    <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Order', true);?></th>
                                    <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'To bill', true);?></th>
                                    <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Paid', true);?></th>
                                <?php endif;?>
                                <?php if($displayLastOneYear && $checkDisplayLastOneYear):?>
                                    <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Order', true);?></th>
                                    <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'To bill', true);?></th>
                                    <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Paid', true);?></th>
                                <?php endif;?>
                                <?php if($displayYear && $checkDisplayYear):?>
                                    <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Order', true);?></th>
                                    <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'To bill', true);?></th>
                                    <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Paid', true);?></th>
                                <?php endif;?>
                                <?php if($displayNextOneYear && $checkDisplayNextOneYear):?>
                                    <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Order', true);?></th>
                                    <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'To bill', true);?></th>
                                    <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Paid', true);?></th>
                                <?php endif;?>
                                <?php if($displayNextTwoYear && $checkDisplayNextTwoYear):?>
                                    <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Order', true);?></th>
                                    <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'To bill', true);?></th>
                                    <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Paid', true);?></th>
                                <?php endif;?>
                            </tr>
                            <tr id="data-sales">
                                <?php if($displayLastTwoYear && $checkDisplayLastTwoYear):?>
                                <td class="order"><?php echo !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$lastTwoYear]) ?  number_format($saleValues['order'][$lastTwoYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="toBill"><?php echo !empty($saleValues) && isset($saleValues['toBill']) && !empty($saleValues['toBill']) && !empty($saleValues['toBill'][$lastTwoYear]) ? number_format($saleValues['toBill'][$lastTwoYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="billed"><?php echo !empty($saleValues) && isset($saleValues['billed']) && !empty($saleValues['billed']) && !empty($saleValues['billed'][$lastTwoYear]) ? number_format($saleValues['billed'][$lastTwoYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <?php endif;?>
                                <?php if($displayLastOneYear && $checkDisplayLastOneYear):?>
                                <td class="order"><?php echo !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$lastOneYear]) ? number_format($saleValues['order'][$lastOneYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="toBill"><?php echo !empty($saleValues) && isset($saleValues['toBill']) && !empty($saleValues['toBill']) && !empty($saleValues['toBill'][$lastOneYear]) ? number_format($saleValues['toBill'][$lastOneYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="billed"><?php echo !empty($saleValues) && isset($saleValues['billed']) && !empty($saleValues['billed']) && !empty($saleValues['billed'][$lastOneYear]) ? number_format($saleValues['billed'][$lastOneYear] , 2, ',', ' '): '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <?php endif;?>
                                <?php if($displayYear && $checkDisplayYear):?>
                                <td class="order"><?php echo !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$year]) ? number_format($saleValues['order'][$year], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="toBill"><?php echo !empty($saleValues) && isset($saleValues['toBill']) && !empty($saleValues['toBill']) && !empty($saleValues['toBill'][$year]) ? number_format($saleValues['toBill'][$year], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="billed"><?php echo !empty($saleValues) && isset($saleValues['billed']) && !empty($saleValues['billed']) && !empty($saleValues['billed'][$year]) ? number_format($saleValues['billed'][$year], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <?php endif;?>
                                <?php if($displayNextOneYear && $checkDisplayNextOneYear):?>
                                <td class="order"><?php echo !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$nextOneYear]) ? number_format($saleValues['order'][$nextOneYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="toBill"><?php echo !empty($saleValues) && isset($saleValues['toBill']) && !empty($saleValues['toBill']) && !empty($saleValues['toBill'][$nextOneYear]) ? number_format($saleValues['toBill'][$nextOneYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="billed"><?php echo !empty($saleValues) && isset($saleValues['billed']) && !empty($saleValues['billed']) && !empty($saleValues['billed'][$nextOneYear]) ? number_format($saleValues['billed'][$nextOneYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <?php endif;?>
                                <?php if($displayNextTwoYear && $checkDisplayNextTwoYear):?>
                                <td class="order"><?php echo !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$nextTwoYear]) ? number_format($saleValues['order'][$nextTwoYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="toBill"><?php echo !empty($saleValues) && isset($saleValues['toBill']) && !empty($saleValues['toBill']) && !empty($saleValues['toBill'][$nextTwoYear]) ? number_format($saleValues['toBill'][$nextTwoYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="billed"><?php echo !empty($saleValues) && isset($saleValues['billed']) && !empty($saleValues['billed']) && !empty($saleValues['billed'][$nextTwoYear]) ? number_format($saleValues['billed'][$nextTwoYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <?php endif;?>
                            </tr>
                            <tr id="data-purchases">
                                <?php if($displayLastTwoYear && $checkDisplayLastTwoYear):?>
                                <td class="order">
                                    <?php
                                        $class="";
                                        $a = !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$lastTwoYear]) ?  $saleValues['order'][$lastTwoYear] : 0;
                                        $b = !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$lastTwoYear]) ?  $purchaseValues['order'][$lastTwoYear] : 0;
                                        if($a >= $b){
                                            $class = "task_blue";
                                        } else {
                                            $class = "task_red";
                                        }
                                        if( !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$lastTwoYear]) ){

                                        } else {
                                            $class="";
                                        }
                                    ?>
                                    <p class="<?php echo $class; ?>">
                                    <?php echo !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$lastTwoYear]) ?  number_format($purchaseValues['order'][$lastTwoYear], 2, ',', ' ') . ' ' . $viewChar : '';?>
                                    </p>
                                </td>
                                <td class="toBill"><?php echo !empty($purchaseValues) && isset($purchaseValues['toBill']) && !empty($purchaseValues['toBill']) && !empty($purchaseValues['toBill'][$lastTwoYear]) ? number_format($purchaseValues['toBill'][$lastTwoYear], 2, ',', ' ') . ' ' . $viewChar : '';?></td>
                                <td class="billed"><?php echo !empty($purchaseValues) && isset($purchaseValues['billed']) && !empty($purchaseValues['billed']) && !empty($purchaseValues['billed'][$lastTwoYear]) ? number_format($purchaseValues['billed'][$lastTwoYear], 2, ',', ' ') . ' ' . $viewChar : '';?></td>
                                <?php endif;?>
                                <?php if($displayLastOneYear && $checkDisplayLastOneYear):?>
                                <td class="order">
                                    <?php
                                        $class="";
                                        $a = !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$lastOneYear]) ?  $saleValues['order'][$lastOneYear] : 0;
                                        $b = !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$lastOneYear]) ?  $purchaseValues['order'][$lastOneYear] : 0;
                                        if($a >= $b){
                                            $class = "task_blue";
                                        } else {
                                            $class = "task_red";
                                        }
                                        if( !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$lastOneYear]) ){

                                        } else {
                                            $class="";
                                        }
                                    ?>
                                    <p class="<?php echo $class; ?>">
                                    <?php echo !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$lastOneYear]) ? number_format($purchaseValues['order'][$lastOneYear], 2, ',', ' ') . ' ' . $viewChar : '';?>
                                    </p>
                                </td>
                                <td class="toBill"><?php echo !empty($purchaseValues) && isset($purchaseValues['toBill']) && !empty($purchaseValues['toBill']) && !empty($purchaseValues['toBill'][$lastOneYear]) ? number_format($purchaseValues['toBill'][$lastOneYear], 2, ',', ' ') . ' ' . $viewChar : '';?></td>
                                <td class="billed"><?php echo !empty($purchaseValues) && isset($purchaseValues['billed']) && !empty($purchaseValues['billed']) && !empty($purchaseValues['billed'][$lastOneYear]) ? number_format($purchaseValues['billed'][$lastOneYear] , 2, ',', ' ') . ' ' . $viewChar : '';?></td>
                                <?php endif;?>
                                <?php if($displayYear && $checkDisplayYear):?>
                                <td class="order">
                                    <?php
                                        $class="";
                                        $a = !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$year]) ?  $saleValues['order'][$year] : 0;
                                        $b = !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$year]) ?  $purchaseValues['order'][$year] : 0;
                                        if($a >= $b){
                                            $class = "task_blue";
                                        } else {
                                            $class = "task_red";
                                        }
                                        if( !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$year]) ){

                                        } else {
                                            $class="";
                                        }
                                    ?>
                                    <p class="<?php echo $class; ?>">
                                    <?php echo !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$year]) ? number_format($purchaseValues['order'][$year], 2, ',', ' ') . ' ' . $viewChar : '';?>
                                    </p>
                                </td>
                                <td class="toBill"><?php echo !empty($purchaseValues) && isset($purchaseValues['toBill']) && !empty($purchaseValues['toBill']) && !empty($purchaseValues['toBill'][$year]) ? number_format($purchaseValues['toBill'][$year], 2, ',', ' ') . ' ' . $viewChar : '';?></td>
                                <td class="billed"><?php echo !empty($purchaseValues) && isset($purchaseValues['billed']) && !empty($purchaseValues['billed']) && !empty($purchaseValues['billed'][$year]) ? number_format($purchaseValues['billed'][$year], 2, ',', ' ') . ' ' . $viewChar : '';?></td>
                                <?php endif;?>
                                <?php if($displayNextOneYear && $checkDisplayNextOneYear):?>
                                <td class="order">
                                    <?php
                                        $class="";
                                        $a = !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$nextOneYear]) ?  $saleValues['order'][$nextOneYear] : 0;
                                        $b = !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$nextOneYear]) ?  $purchaseValues['order'][$nextOneYear] : 0;
                                        if($a >= $b){
                                            $class = "task_blue";
                                        } else {
                                            $class = "task_red";
                                        }
                                        if( !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$nextOneYear]) ){

                                        } else {
                                            $class="";
                                        }
                                    ?>
                                    <p class="<?php echo $class; ?>">
                                    <?php echo !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$nextOneYear]) ? number_format($purchaseValues['order'][$nextOneYear], 2, ',', ' ') . ' ' . $viewChar : '';?>
                                    </p>
                                </td>
                                <td class="toBill"><?php echo !empty($purchaseValues) && isset($purchaseValues['toBill']) && !empty($purchaseValues['toBill']) && !empty($purchaseValues['toBill'][$nextOneYear]) ? number_format($purchaseValues['toBill'][$nextOneYear], 2, ',', ' ') . ' ' . $viewChar : '';?></td>
                                <td class="billed"><?php echo !empty($purchaseValues) && isset($purchaseValues['billed']) && !empty($purchaseValues['billed']) && !empty($purchaseValues['billed'][$nextOneYear]) ? number_format($purchaseValues['billed'][$nextOneYear], 2, ',', ' ') . ' ' . $viewChar : '';?></td>
                                <?php endif;?>
                                <?php if($displayNextTwoYear && $checkDisplayNextTwoYear):?>
                                <td class="order">
                                    <?php
                                        $class="";
                                        $a = !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$nextTwoYear]) ?  $saleValues['order'][$nextTwoYear] : 0;
                                        $b = !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$nextTwoYear]) ?  $purchaseValues['order'][$nextTwoYear] : 0;
                                        if($a >= $b){
                                            $class = "task_blue";
                                        } else {
                                            $class = "task_red";
                                        }
                                        if( !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$nextTwoYear]) ){

                                        } else {
                                            $class="";
                                        }
                                    ?>
                                    <p class="<?php echo $class; ?>">
                                    <?php echo !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$nextTwoYear]) ? number_format($purchaseValues['order'][$nextTwoYear], 2, ',', ' ') . ' ' . $viewChar : '';?>
                                    </p>
                                </td>
                                <td class="toBill"><?php echo !empty($purchaseValues) && isset($purchaseValues['toBill']) && !empty($purchaseValues['toBill']) && !empty($purchaseValues['toBill'][$nextTwoYear]) ? number_format($purchaseValues['toBill'][$nextTwoYear], 2, ',', ' ') . ' ' . $viewChar : '';?></td>
                                <td class="billed"><?php echo !empty($purchaseValues) && isset($purchaseValues['billed']) && !empty($purchaseValues['billed']) && !empty($purchaseValues['billed'][$nextTwoYear]) ? number_format($purchaseValues['billed'][$nextTwoYear], 2, ',', ' ') . ' ' . $viewChar : '';?></td>
                                <?php endif;?>
                            </tr>

                            <tr id="data-sales-purchases">
                                <?php if($displayLastTwoYear && $checkDisplayLastTwoYear):?>
                                <td class="order">
                                    <?php
                                    $a = !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$lastTwoYear]) ?  $saleValues['order'][$lastTwoYear] : 0;
                                    $b = !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$lastTwoYear]) ?  $purchaseValues['order'][$lastTwoYear] : 0;
                                    $budgetTwoYear = $a - $b;
                                    echo number_format(($a - $b), 2, ',', ' ') . ' ' . $viewChar;
                                    ?>
                                </td>
                                <td class="toBill">
                                    <?php
                                    $a = !empty($saleValues) && isset($saleValues['toBill']) && !empty($saleValues['toBill']) && !empty($saleValues['toBill'][$lastTwoYear]) ?  $saleValues['toBill'][$lastTwoYear] : 0;
                                    $b = !empty($purchaseValues) && isset($purchaseValues['toBill']) && !empty($purchaseValues['toBill']) && !empty($purchaseValues['toBill'][$lastTwoYear]) ?  $purchaseValues['toBill'][$lastTwoYear] : 0;
                                    echo number_format(($a - $b), 2, ',', ' ') . ' ' . $viewChar;
                                    ?>
                                </td>
                                <td class="billed">
                                    <?php
                                    $a = !empty($saleValues) && isset($saleValues['billed']) && !empty($saleValues['billed']) && !empty($saleValues['billed'][$lastTwoYear]) ?  $saleValues['billed'][$lastTwoYear] : 0;
                                    $b = !empty($purchaseValues) && isset($purchaseValues['billed']) && !empty($purchaseValues['billed']) && !empty($purchaseValues['billed'][$lastTwoYear]) ?  $purchaseValues['billed'][$lastTwoYear] : 0;
                                    echo number_format(($a - $b), 2, ',', ' ') . ' ' . $viewChar;
                                    ?>
                                </td>
                                <?php endif;?>
                                <?php if($displayLastOneYear && $checkDisplayLastOneYear):?>
                                <td class="order">
                                    <?php
                                    $a = !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$lastOneYear]) ?  $saleValues['order'][$lastOneYear] : 0;
                                    $b = !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$lastOneYear]) ?  $purchaseValues['order'][$lastOneYear] : 0;
                                    $budgetOneYear = $a - $b;
                                    echo number_format(($a - $b), 2, ',', ' ') . ' ' . $viewChar;
                                    ?>
                                </td>
                                <td class="toBill">
                                    <?php
                                    $a = !empty($saleValues) && isset($saleValues['toBilltoBill']) && !empty($saleValues['toBilltoBill']) && !empty($saleValues['toBilltoBill'][$lastOneYear]) ?  $saleValues['toBill'][$lastOneYear] : 0;
                                    $b = !empty($purchaseValues) && isset($purchaseValues['toBilltoBill']) && !empty($purchaseValues['toBilltoBill']) && !empty($purchaseValues['toBilltoBill'][$lastOneYear]) ?  $purchaseValues['toBill'][$lastOneYear] : 0;
                                    echo number_format(($a - $b), 2, ',', ' ') . ' ' . $viewChar;
                                    ?>
                                </td>
                                <td class="billed">
                                    <?php
                                    $a = !empty($saleValues) && isset($saleValues['billed']) && !empty($saleValues['billed']) && !empty($saleValues['billed'][$lastOneYear]) ?  $saleValues['billed'][$lastOneYear] : 0;
                                    $b = !empty($purchaseValues) && isset($purchaseValues['billed']) && !empty($purchaseValues['billed']) && !empty($purchaseValues['billed'][$lastOneYear]) ?  $purchaseValues['billed'][$lastOneYear] : 0;
                                    echo number_format(($a - $b), 2, ',', ' ') . ' ' . $viewChar;
                                    ?>
                                </td>
                                <?php endif;?>
                                <?php if($displayYear && $checkDisplayYear):?>
                                <td class="order">
                                    <?php
                                    $a = !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$year]) ?  $saleValues['order'][$year] : 0;
                                    $b = !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$year]) ?  $purchaseValues['order'][$year] : 0;
                                    $budgetYear = $a - $b;
                                    echo number_format(($a - $b), 2, ',', ' ') . ' ' . $viewChar;
                                    ?>
                                </td>
                                <td class="toBill">
                                    <?php
                                    $a = !empty($saleValues) && isset($saleValues['toBill']) && !empty($saleValues['toBill']) && !empty($saleValues['toBill'][$year]) ?  $saleValues['toBill'][$year] : 0;
                                    $b = !empty($purchaseValues) && isset($purchaseValues['toBill']) && !empty($purchaseValues['toBill']) && !empty($purchaseValues['toBill'][$year]) ?  $purchaseValues['toBill'][$year] : 0;
                                    echo number_format(($a - $b), 2, ',', ' ') . ' ' . $viewChar;
                                    ?>
                                </td>
                                <td class="billed">
                                    <?php
                                    $a = !empty($saleValues) && isset($saleValues['billed']) && !empty($saleValues['billed']) && !empty($saleValues['billed'][$year]) ?  $saleValues['billed'][$year] : 0;
                                    $b = !empty($purchaseValues) && isset($purchaseValues['billed']) && !empty($purchaseValues['billed']) && !empty($purchaseValues['billed'][$year]) ?  $purchaseValues['billed'][$year] : 0;
                                    echo number_format(($a - $b), 2, ',', ' ') . ' ' . $viewChar;
                                    ?>
                                </td>
                                <?php endif;?>
                                <?php if($displayNextOneYear && $checkDisplayNextOneYear):?>
                                <td class="order">
                                    <?php
                                    $a = !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$nextOneYear]) ?  $saleValues['order'][$nextOneYear] : 0;
                                    $b = !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$nextOneYear]) ?  $purchaseValues['order'][$nextOneYear] : 0;
                                    $budgetNextOneYear = $a - $b;
                                    echo number_format(($a - $b), 2, ',', ' ') . ' ' . $viewChar;
                                    ?>
                                </td>
                                <td class="toBill">
                                    <?php
                                    $a = !empty($saleValues) && isset($saleValues['toBill']) && !empty($saleValues['toBill']) && !empty($saleValues['toBill'][$nextOneYear]) ?  $saleValues['toBill'][$nextOneYear] : 0;
                                    $b = !empty($purchaseValues) && isset($purchaseValues['toBill']) && !empty($purchaseValues['toBill']) && !empty($purchaseValues['toBill'][$nextOneYear]) ?  $purchaseValues['toBill'][$nextOneYear] : 0;
                                    echo number_format(($a - $b), 2, ',', ' ') . ' ' . $viewChar;
                                    ?>
                                </td>
                                <td class="billed">
                                    <?php
                                    $a = !empty($saleValues) && isset($saleValues['billed']) && !empty($saleValues['billed']) && !empty($saleValues['billed'][$nextOneYear]) ?  $saleValues['billed'][$nextOneYear] : 0;
                                    $b = !empty($purchaseValues) && isset($purchaseValues['billed']) && !empty($purchaseValues['billed']) && !empty($purchaseValues['billed'][$nextOneYear]) ?  $purchaseValues['billed'][$nextOneYear] : 0;
                                    echo number_format(($a - $b), 2, ',', ' ') . ' ' . $viewChar;
                                    ?>
                                </td>
                                <?php endif;?>
                                <?php if($displayNextTwoYear && $checkDisplayNextTwoYear):?>
                                <td class="order">
                                    <?php
                                    $a = !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$nextTwoYear]) ?  $saleValues['order'][$nextTwoYear] : 0;
                                    $b = !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$nextTwoYear]) ?  $purchaseValues['order'][$nextTwoYear] : 0;
                                    $budgetNextTwoYear = $a - $b;
                                    echo number_format(($a - $b), 2, ',', ' ') . ' ' . $viewChar;
                                    ?>
                                </td>
                                <td class="toBill">
                                    <?php
                                    $a = !empty($saleValues) && isset($saleValues['toBill']) && !empty($saleValues['toBill']) && !empty($saleValues['toBill'][$nextTwoYear]) ?  $saleValues['toBill'][$nextTwoYear] : 0;
                                    $b = !empty($purchaseValues) && isset($purchaseValues['toBill']) && !empty($purchaseValues['toBill']) && !empty($purchaseValues['toBill'][$nextTwoYear]) ?  $purchaseValues['toBill'][$nextTwoYear] : 0;
                                    echo number_format(($a - $b), 2, ',', ' ') . ' ' . $viewChar;
                                    ?>
                                </td>
                                <td class="billed">
                                    <?php
                                    $a = !empty($saleValues) && isset($saleValues['billed']) && !empty($saleValues['billed']) && !empty($saleValues['billed'][$nextTwoYear]) ?  $saleValues['billed'][$nextTwoYear] : 0;
                                    $b = !empty($purchaseValues) && isset($purchaseValues['billed']) && !empty($purchaseValues['billed']) && !empty($purchaseValues['billed'][$nextTwoYear]) ?  $purchaseValues['billed'][$nextTwoYear] : 0;
                                    echo number_format(($a - $b), 2, ',', ' ') . ' ' . $viewChar;
                                    ?>
                                </td>
                                <?php endif;?>
                            </tr>
                            <tr id="data-xxxx">
                                <?php if($displayLastTwoYear && $checkDisplayLastTwoYear):?>
                                <?php
                                    $class="";
                                    $totalXxxx += $budgetTwoYear;
                                    if($totalXxxx >= 0){
                                        $class = "task_blue";
                                    } else {
                                        $class = "task_red";
                                    }
                                ?>
                                <td class="order"><p class="<?php echo $class; ?>"><?php echo number_format($totalXxxx, 2, ',', ' '); ?>&nbsp;<?php echo $viewChar;?></p></td>
                                <td class="toBill"></td>
                                <td class="billed"></td>
                                <?php endif;?>
                                <?php if($displayLastOneYear && $checkDisplayLastOneYear):?>
                                <?php
                                    $totalXxxx += $budgetOneYear;
                                    if($totalXxxx >= 0){
                                        $class = "task_blue";
                                    } else {
                                        $class = "task_red";
                                    }
                                ?>
                                <td class="order"><p class="<?php echo $class; ?>"><?php echo number_format($totalXxxx, 2, ',', ' '); ?>&nbsp;<?php echo $viewChar;?></p></td>
                                <td class="toBill"></td>
                                <td class="billed"></td>
                                <?php endif;?>
                                <?php if($displayYear && $checkDisplayYear):?>
                                <?php
                                    $totalXxxx += $budgetYear;
                                    if($totalXxxx >= 0){
                                        $class = "task_blue";
                                    } else {
                                        $class = "task_red";
                                    }
                                ?>
                                <td class="order"><p class="<?php echo $class; ?>"><?php echo number_format($totalXxxx, 2, ',', ' '); ?>&nbsp;<?php echo $viewChar;?></p></td>
                                <td class="toBill"></td>
                                <td class="billed"></td>
                                <?php endif;?>
                                <?php if($displayNextOneYear && $checkDisplayNextOneYear):?>
                                <?php
                                    $totalXxxx += $budgetNextOneYear;
                                    if($totalXxxx >= 0){
                                        $class = "task_blue";
                                    } else {
                                        $class = "task_red";
                                    }
                                ?>
                                <td class="order"><p class="<?php echo $class; ?>"><?php echo number_format($totalXxxx, 2, ',', ' '); ?>&nbsp;<?php echo $viewChar;?></p></td>
                                <td class="toBill"></td>
                                <td class="billed"></td>
                                <?php endif;?>
                                <?php if($displayNextTwoYear && $checkDisplayNextTwoYear):?>
                                <?php
                                    $totalXxxx += $budgetNextTwoYear;
                                    if($totalXxxx >= 0){
                                        $class = "task_blue";
                                    } else {
                                        $class = "task_red";
                                    }
                                ?>
                                <td class="order"><p class="<?php echo $class; ?>"><?php echo number_format($totalXxxx, 2, ',', ' '); ?>&nbsp;<?php echo $viewChar;?></p></td>
                                <td class="toBill"></td>
                                <td class="billed"></td>
                                <?php endif;?>
                            </tr>
                            <?php endif;?>
                            <?php if($display != 'euro'): ?>
                            <tr>
                                <?php if($displayLastTwoYear):?>
                                <th><?php echo __('Provisional', true);?></th>
                                <th><?php echo __('Workload', true);?></th>
                                <th><?php echo __('Consumed', true);?></th>
                                <?php endif;?>
                                <?php if($displayLastOneYear):?>
                                <th><?php echo __('Provisional', true);?></th>
                                <th><?php echo __('Workload', true);?></th>
                                <th><?php echo __('Consumed', true);?></th>
                                <?php endif;?>
                                <?php if($displayYear):?>
                                <th><?php echo __('Provisional', true);?></th>
                                <th><?php echo __('Workload', true);?></th>
                                <th><?php echo __('Consumed', true);?></th>
                                <?php endif;?>
                                <?php if($displayNextOneYear):?>
                                <th><?php echo __('Provisional', true);?></th>
                                <th><?php echo __('Workload', true);?></th>
                                <th><?php echo __('Consumed', true);?></th>
                                <?php endif;?>
                                <?php if($displayNextTwoYear):?>
                                <th><?php echo __('Provisional', true);?></th>
                                <th><?php echo __('Workload', true);?></th>
                                <th><?php echo __('Consumed', true);?></th>
                                <?php endif;?>
                            </tr>
                            <tr id="data-internals">
                                <?php if($displayLastTwoYear):?>
                                <td class="provisional"><?php echo isset($internalValues['provisional_' . $lastTwoYear]) && !empty($internalValues['provisional_' . $lastTwoYear]) ? number_format($internalValues['provisional_' . $lastTwoYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="workload"><?php echo isset($dataOfYearInternals['workload_' . $lastTwoYear]) && !empty($dataOfYearInternals['workload_' . $lastTwoYear]) ? number_format($dataOfYearInternals['workload_' . $lastTwoYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="consumed"><?php echo isset($dataOfYearInternals['consumed_' . $lastTwoYear]) && !empty($dataOfYearInternals['consumed_' . $lastTwoYear]) ? number_format($dataOfYearInternals['consumed_' . $lastTwoYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <?php endif;?>
                                <?php if($displayLastOneYear):?>
                                <td class="provisional"><?php echo isset($internalValues['provisional_' . $lastOneYear]) && !empty($internalValues['provisional_' . $lastOneYear]) ? number_format($internalValues['provisional_' . $lastOneYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="workload"><?php echo isset($dataOfYearInternals['workload_' . $lastOneYear]) && !empty($dataOfYearInternals['workload_' . $lastOneYear]) ? number_format($dataOfYearInternals['workload_' . $lastOneYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="consumed"><?php echo isset($dataOfYearInternals['consumed_' . $lastOneYear]) && !empty($dataOfYearInternals['consumed_' . $lastOneYear]) ? number_format($dataOfYearInternals['consumed_' . $lastOneYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <?php endif;?>
                                <?php if($displayYear):?>
                                <td class="provisional"><?php echo isset($internalValues['provisional_' . $year]) && !empty($internalValues['provisional_' . $year]) ? number_format($internalValues['provisional_' . $year], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="workload"><?php echo isset($dataOfYearInternals['workload_' . $year]) && !empty($dataOfYearInternals['workload_' . $year]) ? number_format($dataOfYearInternals['workload_' . $year], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="consumed"><?php echo isset($dataOfYearInternals['consumed_' . $year]) && !empty($dataOfYearInternals['consumed_' . $year]) ? number_format($dataOfYearInternals['consumed_' . $year], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <?php endif;?>
                                <?php if($displayNextOneYear):?>
                                <td class="provisional"><?php echo isset($internalValues['provisional_' . $nextOneYear]) && !empty($internalValues['provisional_' . $nextOneYear]) ? number_format($internalValues['provisional_' . $nextOneYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="workload"><?php echo isset($dataOfYearInternals['workload_' . $nextOneYear]) && !empty($dataOfYearInternals['workload_' . $nextOneYear]) ? number_format($dataOfYearInternals['workload_' . $nextOneYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="consumed"><?php echo isset($dataOfYearInternals['consumed_' . $nextOneYear]) && !empty($dataOfYearInternals['consumed_' . $nextOneYear]) ? number_format($dataOfYearInternals['consumed_' . $nextOneYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <?php endif;?>
                                <?php if($displayNextTwoYear):?>
                                <td class="provisional"><?php echo isset($internalValues['provisional_' . $nextTwoYear]) && !empty($internalValues['provisional_' . $nextTwoYear]) ? number_format($internalValues['provisional_' . $nextTwoYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="workload"><?php echo isset($dataOfYearInternals['workload_' . $nextTwoYear]) && !empty($dataOfYearInternals['workload_' . $nextTwoYear]) ? number_format($dataOfYearInternals['workload_' . $nextTwoYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="consumed"><?php echo isset($dataOfYearInternals['consumed_' . $nextTwoYear]) && !empty($dataOfYearInternals['consumed_' . $nextTwoYear]) ? number_format($dataOfYearInternals['consumed_' . $nextTwoYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <?php endif;?>
                            </tr>
                            <tr id="data-externals">
                                <?php if($displayLastTwoYear):?>
                                <td class="provisional"><?php echo isset($externalValues['provisional_' . $lastTwoYear]) && !empty($externalValues['provisional_' . $lastTwoYear]) ? number_format($externalValues['provisional_' . $lastTwoYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="workload"><?php echo isset($dataOfYearExternals['workload_' . $lastTwoYear]) && !empty($dataOfYearExternals['workload_' . $lastTwoYear]) ? number_format($dataOfYearExternals['workload_' . $lastTwoYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="consumed"><?php echo isset($dataOfYearExternals['consumed_' . $lastTwoYear]) && !empty($dataOfYearExternals['consumed_' . $lastTwoYear]) ? number_format($dataOfYearExternals['consumed_' . $lastTwoYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <?php endif;?>
                                <?php if($displayLastOneYear):?>
                                <td class="provisional"><?php echo isset($externalValues['provisional_' . $lastOneYear]) && !empty($externalValues['provisional_' . $lastOneYear]) ? number_format($externalValues['provisional_' . $lastOneYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="workload"><?php echo isset($dataOfYearExternals['workload_' . $lastOneYear]) && !empty($dataOfYearExternals['workload_' . $lastOneYear]) ? number_format($dataOfYearExternals['workload_' . $lastOneYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="consumed"><?php echo isset($dataOfYearExternals['consumed_' . $lastOneYear]) && !empty($dataOfYearExternals['consumed_' . $lastOneYear]) ? number_format($dataOfYearExternals['consumed_' . $lastOneYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <?php endif;?>
                                <?php if($displayYear):?>
                                <td class="provisional"><?php echo isset($externalValues['provisional_' . $year]) && !empty($externalValues['provisional_' . $year]) ? number_format($externalValues['provisional_' . $year], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="workload"><?php echo isset($dataOfYearExternals['workload_' . $year]) && !empty($dataOfYearExternals['workload_' . $year]) ? number_format($dataOfYearExternals['workload_' . $year], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="consumed"><?php echo isset($dataOfYearExternals['consumed_' . $year]) && !empty($dataOfYearExternals['consumed_' . $year]) ? number_format($dataOfYearExternals['consumed_' . $year], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <?php endif;?>
                                <?php if($displayNextOneYear):?>
                                <td class="provisional"><?php echo isset($externalValues['provisional_' . $nextOneYear]) && !empty($externalValues['provisional_' . $nextOneYear]) ? number_format($externalValues['provisional_' . $nextOneYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="workload"><?php echo isset($dataOfYearExternals['workload_' . $nextOneYear]) && !empty($dataOfYearExternals['workload_' . $nextOneYear]) ? number_format($dataOfYearExternals['workload_' . $nextOneYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="consumed"><?php echo isset($dataOfYearExternals['consumed_' . $nextOneYear]) && !empty($dataOfYearExternals['consumed_' . $nextOneYear]) ? number_format($dataOfYearExternals['consumed_' . $nextOneYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <?php endif;?>
                                <?php if($displayNextTwoYear):?>
                                <td class="provisional"><?php echo isset($externalValues['provisional_' . $nextTwoYear]) && !empty($externalValues['provisional_' . $nextTwoYear]) ? number_format($externalValues['provisional_' . $nextTwoYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="workload"><?php echo isset($dataOfYearExternals['workload_' . $nextTwoYear]) && !empty($dataOfYearExternals['workload_' . $nextTwoYear]) ? number_format($dataOfYearExternals['workload_' . $nextTwoYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <td class="consumed"><?php echo isset($dataOfYearExternals['consumed_' . $nextTwoYear]) && !empty($dataOfYearExternals['consumed_' . $nextTwoYear]) ? number_format($dataOfYearExternals['consumed_' . $nextTwoYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                                <?php endif;?>
                            </tr>
                            <?php endif;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            </div></div>
           

        </div>
    </div>
</div>
<script>
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
    $(document).ready(function(){
        var project_id = <?php echo json_encode($project_id);?>;
        var totalOrder = 0, totalToBill = 0, totalBilled = 0, totalInterPro = 0, totalInterWor = 0, totalInterCon = 0, totalExterPro = 0, totalExterWor = 0, totalExterCon = 0;
        var viewChar = <?php echo json_encode($viewChar);?>;
        var $totalXxxx = <?php echo json_encode($totalXxxx) ?>;
        $('#data-sales').find('td').each(function(ind, val){
            var iClass = $(this).attr('class');
            if(iClass){
                var val = $(this).html().toString().replace('&nbsp;' + viewChar, '');
                val = number_format(val.toString().replace(',', '.'), 2, '.', '');
                if(iClass == 'order'){
                    totalOrder += parseFloat(val);
                } else if(iClass == 'toBill'){
                    totalToBill += parseFloat(val);
                } else if(iClass == 'billed'){
                    totalBilled += parseFloat(val);
                }
            }
        });
        var checkTotalOrder = totalOrder;
        $('#sales').find('td.saleOrder').html(number_format(parseFloat(totalOrder), 2, ',', ' ') + '&nbsp;' + viewChar);
        $('#sales').find('td.saleToBill').html(number_format(parseFloat(totalToBill), 2, ',', ' ') + '&nbsp;' + viewChar);
        $('#sales').find('td.saleBilled').html(number_format(parseFloat(totalBilled), 2, ',', ' ') + '&nbsp;' + viewChar);

        var totalOrder = 0, totalToBill = 0, totalBilled = 0;
        $('#data-purchases').find('td').each(function(ind, val){
            var iClass = $(this).attr('class');
            if(iClass){
                var val;
                if(iClass == 'order'){
                    val = $(this).find('p').text().toString().replace('&nbsp;' + viewChar, '');
                } else{
                    val = $(this).html().toString().replace('&nbsp;' + viewChar, '');
                }
                val = number_format(val.toString().replace(',', '.'), 2, '.', '');
                if(iClass == 'order'){
                    totalOrder += parseFloat(val);
                } else if(iClass == 'toBill'){
                    totalToBill += parseFloat(val);
                } else if(iClass == 'billed'){
                    totalBilled += parseFloat(val);
                }
            }
        });
        if(checkTotalOrder >= totalOrder){
            $('#purchases').find('td.saleOrder').html('<p class="task_blue">' + number_format(parseFloat(totalOrder), 2, ',', ' ') + '&nbsp;' + viewChar + '</p>');
        } else {
            $('#purchases').find('td.saleOrder').html('<p class="task_red">' + number_format(parseFloat(totalOrder), 2, ',', ' ') + '&nbsp;' + viewChar + '</p>');
        }
        $('#purchases').find('td.saleToBill').html(number_format(parseFloat(totalToBill), 2, ',', ' ') + '&nbsp;' + viewChar);
        $('#purchases').find('td.saleBilled').html(number_format(parseFloat(totalBilled), 2, ',', ' ') + '&nbsp;' + viewChar);

        var totalOrder = 0, totalToBill = 0, totalBilled = 0;
        $('#data-sales-purchases').find('td').each(function(ind, val){
            var iClass = $(this).attr('class');
            if(iClass){
                var val = $(this).html().toString().replace('&nbsp;' + viewChar, '');
                val = number_format(val.toString().replace(',', '.'), 2, '.', '');
                if(iClass == 'order'){
                    totalOrder += parseFloat(val);
                } else if(iClass == 'toBill'){
                    totalToBill += parseFloat(val);
                } else if(iClass == 'billed'){
                    totalBilled += parseFloat(val);
                }
            }
        });
        $('#sales-purchases').find('td.saleOrder').html(number_format(parseFloat(totalOrder), 2, ',', ' ') + '&nbsp;' + viewChar);
        $('#sales-purchases').find('td.saleToBill').html(number_format(parseFloat(totalToBill), 2, ',', ' ') + '&nbsp;' + viewChar);
        $('#sales-purchases').find('td.saleBilled').html(number_format(parseFloat(totalBilled), 2, ',', ' ') + '&nbsp;' + viewChar);

        if($totalXxxx >= 0){
            $('#xxxx').find('td.saleOrder').html('<p class="task_blue">' + number_format(parseFloat($totalXxxx), 2, ',', ' ') + '&nbsp;' + viewChar + '</p>');
        } else {
            $('#xxxx').find('td.saleOrder').html('<p class="task_red">' + number_format(parseFloat($totalXxxx), 2, ',', ' ') + '&nbsp;' + viewChar + '</p>');
        }

        $('#data-internals').find('td').each(function(ind, val){
            var iClass = $(this).attr('class');
            if(iClass){
                var val = $(this).html().toString().replace('&nbsp;' + viewChar, '');
                val = number_format(val.toString().replace(',', '.'), 2, '.', '');
                if(iClass == 'provisional'){
                    totalInterPro += parseFloat(val);
                } else if(iClass == 'workload'){
                    totalInterWor += parseFloat(val);
                } else if(iClass == 'consumed'){
                    totalInterCon += parseFloat(val);
                }
            }
        });
        $('#internals').find('td.internalPro').html(number_format(parseFloat(totalInterPro), 2, ',', ' ') + '&nbsp;' + viewChar);
        $('#internals').find('td.internalWor').html(number_format(parseFloat(totalInterWor), 2, ',', ' ') + '&nbsp;' + viewChar);
        $('#internals').find('td.internalCon').html(number_format(parseFloat(totalInterCon), 2, ',', ' ') + '&nbsp;' + viewChar);
        $('#data-externals').find('td').each(function(ind, val){
            var iClass = $(this).attr('class');
            if(iClass){
                var val = $(this).html().toString().replace('&nbsp;' + viewChar, '');
                val = number_format(val.toString().replace(',', '.'), 2, '.', '');
                if(iClass == 'provisional'){
                    totalExterPro += parseFloat(val);
                } else if(iClass == 'workload'){
                    totalExterWor += parseFloat(val);
                } else if(iClass == 'consumed'){
                    totalExterCon += parseFloat(val);
                }
            }
        });
        $('#externals').find('td.externalPro').html(number_format(parseFloat(totalExterPro), 2, ',', ' ') + '&nbsp;' + viewChar);
        $('#externals').find('td.externalWor').html(number_format(parseFloat(totalExterWor), 2, ',', ' ') + '&nbsp;' + viewChar);
        $('#externals').find('td.externalCon').html(number_format(parseFloat(totalExterCon), 2, ',', ' ') + '&nbsp;' + viewChar);
        $("#profit").change(function() {
            //var currentUrl = updateQueryStringParameter(location.href,'profit', $("#profit").val());
            var val = $(this).val();
            if(!val){
                val = -1;
            }
            var view = $("#viewFollow").val();
            location.href = "/project_budget_fiscals/index/" + project_id + '/' + val + '/' + view;
        });
        $("#viewFollow").change(function() {
            //var currentUrl = updateQueryStringParameter(location.href,'profit', $("#profit").val());
            var val = $( "#profit" ).val();
            if(!val){
                val = -1;
            }
            var view = $(this).val();
            $.ajax({
                url: '/project_budget_fiscals/saveDisplay/' + view,
                success: function(data){
                    location.href = "/project_budget_fiscals/index/" + project_id + '/' + val + '/' + view;
                }
            });
        });
        function updateQueryStringParameter(uri, key, value) {
            var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
            var separator = uri.indexOf('?') !== -1 ? "&" : "?";
            if (uri.match(re)) {
                return uri.replace(re, '$1' + key + "=" + value + '$2');
            } else {
                return uri + separator + key + "=" + value;
            }
        }
    });
    function setupScroll(){
        var t = $('#table-scroller').position();
        $("#scrollTopAbsenceContent").width($(".table-content").width());
        $("#scrollTopAbsence").width($("#table-scroller").width());
        var $display = <?php echo json_encode($display) ?>;
        if($display == 'euro'){
            $("#scrollTopAbsence").css({'top' : t.top + 95, 'left' : t.left, 'position' : 'absolute'});
        }
    }
    setupScroll();
    $( window ).resize(function() {
        setupScroll();
    });
    $("#scrollTopAbsence").scroll(function () {
        $("#table-scroller").scrollLeft($("#scrollTopAbsence").scrollLeft());
    });
</script>
