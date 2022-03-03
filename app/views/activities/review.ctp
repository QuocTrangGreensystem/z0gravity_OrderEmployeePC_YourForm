<!--[if lt IE 8]>
    <style type='text/css'>
        #wd-table{
            height: 300px;
        }
    </style>
<![endif]-->
<!-- export excel  -->
<?php
    echo $html->css(array('jquery.multiSelect', 'slick_grid/slick.grid_v2', 'slick_grid/slick.pager', 'slick_grid/slick.common_v2', 'slick_grid/slick.edit'));
    echo $html->script(array(
        'jquery.multiSelect',
        'slick_grid/lib/jquery-ui-1.8.16.custom.min',
        'slick_grid/lib/jquery.event.drag-2.0.min',
        'slick_grid/slick.core',
        'slick_grid/slick.dataview',
        'slick_grid/controls/slick.pager',
        'slick_grid/slick.formatters',
        'slick_grid/plugins/slick.cellrangedecorator',
        'slick_grid/plugins/slick.cellrangeselector',
        'slick_grid/plugins/slick.cellselectionmodel',
        'slick_grid/slick.editors',
        'slick_grid/slick.grid',
        'slick_grid_custom',
        'history_filter',
        'slick_grid/plugins/slick.dataexporter',
    ));
    echo $this->element('dialog_projects');
    App::import("vendor", "str_utility");
    $str_utility = new str_utility();
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
    $columnAlignRight = array(
        'price', 'md', 'raf', 'forecast', 'avancement', 'real_price', 'c23', 'c24', 'c25', 'c26', 'c27',
        'c28', 'c29', 'c31', 'c32', 'c33', 'c34', 'c35', 'c36', 'c37', 'c38', 'c39',
        'completed', 'remain', 'total_costs_var', 'internal_costs_var', 'external_costs_var',
        'external_costs_progress', 'assign_to_profit_center', 'assign_to_employee',
    );
    $columnAlignRightAndEuro = array(
        'sales_sold', 'sales_to_bill', 'sales_billed', 'sales_paid', 'total_costs_budget', 'total_costs_forecast',
        'total_costs_engaged', 'total_costs_remain', 'internal_costs_budget', 'internal_costs_forecast', 'internal_costs_engaged',
        'internal_costs_remain', 'external_costs_budget', 'external_costs_forecast', 'external_costs_ordered',
        'external_costs_remain', 'external_costs_progress_euro', 'internal_costs_average'
    );
	/**
	 * Update by QUANNGUYEN 12/02/2019
	 */
    $columnAlignRightAndManDay = array(
        'internal_costs_budget_man_day', 'sales_man_day', 'total_costs_man_day', 'internal_costs_forecasted_man_day', 'external_costs_man_day',
        'workload_y', 'workload_last_one_y',
        'workload_last_two_y', 'workload_last_thr_y', 'workload_next_one_y', 'workload_next_two_y', 'workload_next_thr_y',
        'consumed_y', 'consumed_last_one_y', 'consumed_last_two_y', 'consumed_last_thr_y', 'consumed_next_one_y', 'consumed_next_two_y',
        'consumed_next_thr_y', 'workload', 'overload', 'consumed_current_year', 'consumed'
    );
	/**
	 * End Update 12/02/2019
	 */
    $columnNotCalculationConsumed = array(
        'id', 'no.', 'MetaData', 'name', 'long_name', 'short_name', 'code2', 'code1', 'family_id', 'subfamily_id', 'actif',
        'code4','code5', 'code6', 'code7', 'code8', 'code9', 'code10',
        'pms', 'accessible_profit', 'linked_profit', 'code3', 'start_date', 'end_date', 'c42', 'c40',
        'raf', 'c30', 'c44', 'completed', 'total_costs_var', 'internal_costs_var', 'external_costs_var', 'assign_to_profit_center',
        'assign_to_employee', 'external_costs_progress', 'activated', 'action.', 'budget_customer_id', 'project_manager_id', 'import_code'
    );
    $listDatas = array(
        'workload_y' => __('Workload', true) . ' ' . date('Y', time()),
        'workload_last_one_y' => __('Workload', true) . ' ' . (date('Y', time()) - 1),
        'workload_last_two_y' => __('Workload', true) . ' ' . (date('Y', time()) - 2),
        'workload_last_thr_y' => __('Workload', true) . ' ' . (date('Y', time()) - 3),
        'workload_next_one_y' => __('Workload', true) . ' ' . (date('Y', time()) + 1),
        'workload_next_two_y' => __('Workload', true) . ' ' . (date('Y', time()) + 2),
        'workload_next_thr_y' => __('Workload', true) . ' ' . (date('Y', time()) + 3),
        'consumed_y' => __('Consumed', true) . ' ' . date('Y', time()),
        'consumed_last_one_y' => __('Consumed', true) . ' ' . (date('Y', time()) - 1),
        'consumed_last_two_y' => __('Consumed', true) . ' ' . (date('Y', time()) - 2),
        'consumed_last_thr_y' => __('Consumed', true) . ' ' . (date('Y', time()) - 3),
        'consumed_next_one_y' => __('Consumed', true) . ' ' . (date('Y', time()) + 1),
        'consumed_next_two_y' => __('Consumed', true) . ' ' . (date('Y', time()) + 2),
        'consumed_next_thr_y' => __('Consumed', true) . ' ' . (date('Y', time()) + 3)
    );
?>
<script type="text/javascript">
    HistoryFilter.auto = false;
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>_';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
    // HistoryFilter.afterLoad = function(){
    //     resizeHandler();
    // }
</script>
<style type='text/css'>
.KO{ color:#F00}
.hasLoading{
    /*text-indent: 100%;*/
    background: url('<?php echo $this->Html->webroot('img/front/waiting-dots.gif'); ?>') no-repeat scroll 0% 105% transparent !important;
    color: #fff;
}
.row-number{
    float: right !important;
}
#wd-container-footer{
    display: none;
}
body{
    overflow: hidden;
}
.slick-viewport{
    overflow-x: hidden !important;
    overflow-y: auto;
}
</style>
<!-- /export excel  -->
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title wd-activity-actions">
                    <h2 class="wd-t1"><?php echo __("Activity review", true); ?></h2>
                    <?php
                        echo $this->Form->create('Category', array('style' => 'float: left; margin-right: 5px'));
                        if(!empty($actiReview)){
                            $op = ($actiReview == 1) ? 'selected="selected"' : '';
                            $in = ($actiReview == 2) ? 'selected="selected"' : '';
                            $ar = ($actiReview == 3) ? 'selected="selected"' : '';
                            $arch = ($actiReview == 4) ? 'selected="selected"' : '';
                        }
                    ?>
                        <select style="padding: 6px;" class="wd-customs" id="FilterStatusActivity">
                            <option value="1" <?php echo isset($op) ? $op : '';?>><?php echo  __("Activated", true)?></option>
                            <option value="2" <?php echo isset($in) ? $in : '';?>><?php echo  __("Not Activated", true)?></option>
                            <option value="3" <?php echo isset($ar) ? $ar : '';?>><?php echo  __("Activated & Not Activated", true)?></option>
                            <option value="4" <?php echo isset($arch) ? $arch : '';?>><?php echo  __("Archived", true)?></option>
                        </select>
                    <?php
                        echo $this->Form->end();
                    ?>
                    <!--ADD CODE BY VINGUYEN 07/05/2014-->
                    <?php //echo $this->element('multiSortHtml'); ?> 
                    <!--END-->
                    <!-- <a href="#" id="export-submit" class="export-excel-icon-all" style="margin-right:5px; " title="<?php __('Export Excel')?>"><span><?php __('Export') ?></span></a> -->
                    <a href="<?php echo $this->Html->url(array('action' => 'export_excel_review'));?>" id="export-table" class="btn btn-excel"><i class="icon-layers"></i></a>
                    <a href="javascript:void(0);" class="btn btn-reset-filter hidden" id="reset-filter" onclick="resetFilter();" style="margin-right:5px;" title="<?php __('Reset filter') ?>"><i class="icon-refresh"></i></a>
                </div>
                <div id="message-place">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    ?>
                </div>
                <div id="scrollTopAbsence"><div id="scrollTopAbsenceContent"></div></div>
                <br clear="all"  />
                <div class="wd-table" id="project_container" style="width:100%;">

                </div>
                <div id="pager" style="width:100%;height:0; overflow: hidden; margin-top: 30px;">

                </div>
                <?php echo $this->element('grid_status'); ?>
            </div>
        </div>
    </div>
</div>
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'activities', 'action' => 'export')));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>

<?php echo $html->script('responsive_table.js'); ?>
<?php
$map = $FIELDS = array();
$map = !empty($allActivityColumn) ? Set::combine($allActivityColumn, '{s}.codeTmp', '{s}.key') : array();
$columns[] = array(
    'id' => 'no.',
    'field' => 'no.',
    'name' => '#',
    'width' => 40,
    'sortable' => true,
    'resizable' => false,
    'noFilter' => 1,
);
foreach ($activityColumn as $key => $column) {
    //$map['C' . $column['code']] = $key;
    if (empty($column['display'])) {
        continue;
    }
    $editor = array();
    if (in_array($key, array('raf', 'price', 'md'))) {
        $editor = array(
            'editor' => 'Slick.Editors.numericValue'
        );
    } elseif ($column['code'] >= 32 && $column['code'] <= 39 || $column['code']==41) {
        $editor = array(
            'editor' => 'Slick.Editors.decimalValue'
        );
    }elseif(($column['code'] >= 40 && $column['code'] <= 45) || ($column['code'] >= 87 && $column['code'] <= 93)){
        $editor = array(
            'editor' => 'Slick.Editors.textBox'
        );
    }
    elseif ($key === 'consumed' || $key === 'consumed_current_year' || $key === 'consumed_current_month') {
        $editor = array(
            'formatter' => 'Slick.Formatters.Action'
        );
    } elseif ($key === 'name') {
        $editor = array(
            'formatter' => 'Slick.Formatters.HyperlinkCellFormatter'
        );
    } elseif ($key === 'actif'){
        $editor = array(
            'editor' => 'Slick.Editors.selectBox'
        );
    } elseif ($key === 'budget_customer_id'){
        $editor = array(
            'editor' => 'Slick.Editors.selectBox'
        );
    } elseif ($key === 'project_manager_id'){
        $editor = array(
            'editor' => 'Slick.Editors.GetProjectManager',
            'formatter' => 'Slick.Formatters.GetProjectManager'
        );
    }
    if(in_array($key, $columnAlignRight)){
        $editor['formatter'] = 'Slick.Formatters.numberVal';
    }
    if(in_array($key, $columnAlignRightAndEuro)){
        $editor['formatter'] = 'Slick.Formatters.numberValEuro';
    }
    if(in_array($key, $columnAlignRightAndManDay)){
        $editor['formatter'] = 'Slick.Formatters.numberValManDay';
    }
	if(isset($editor['editor'])){
		$FIELDS[] = $key;
	}
    $columns[] = array_merge(array(
        'id' => $key,
        'field' => $key,
        'name' => !empty($listDatas) && !empty($listDatas[$key]) ? $listDatas[$key] : __($column['name'], true),
        'code' => 'C' . $column['code'],
        'calculate' => $column['calculate'],
        'width' => isset($history['columnWidth'][$key]) ? (int) $history['columnWidth'][$key] : 100,
        'sortable' => true,
        'resizable' => true), $editor);

}
$_activited[] = array(
    'id' => 'activated',
    'field' => 'activated',
    'name' => __('Activated', true),
    'width' => isset($history['columnWidth']['activated']) ? (int) $history['columnWidth']['activated'] : 120,
    'sortable' => true,
    'resizable' => true,
    'editor' => 'Slick.Editors.selectBox'
);
$columns =  array_merge($columns, $_activited);
$i = 1;
$totalHeaders = array();
$dataView = array();
$selectMaps = array(
    'actif' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'pms' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'activated' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'family_id' => $families,
    'subfamily_id' => $subfamilies,
    'budget_customer_id' => $budgetCustomers,
    'project_manager_id' => $projectManagers
);
/**
 * Translation M.D, J.H trên header màn hình Activity/View
 */
$i18n = array(
    'M.D' => __('M.D', true)
);
$viewManDay = __('M.D', true);
$activityListIds = array();
foreach ($activities as $activity) {
    $data = array(
        'id' => $activity['Activity']['id'],
        'no.' => $i++,
        'backupPM' => array(),
        'project_manager_id' => array(),
        'MetaData' => array()
    );
    $data['name'] = (string) $activity['Activity']['name'];
    $data['long_name'] = (string) $activity['Activity']['long_name'];
    $data['short_name'] = (string) $activity['Activity']['short_name'];
    $data['family_id'] = (string) $activity['Activity']['family_id'];
    $data['subfamily_id'] = (string) $activity['Activity']['subfamily_id'];
    $data['pms'] = $activity['Activity']['pms'] ? 'yes' : 'no';
    $data['code1'] = (string) $activity['Activity']['code1'];
    $data['code2'] = (string) $activity['Activity']['code2'];
    $data['code3'] = (string) $activity['Activity']['code3'];
    $data['import_code'] = (string) $activity['Activity']['import_code'];
    $data['start_date'] = !empty($activity['Activity']['start_date']) ? date('d-m-Y', $activity['Activity']['start_date']) : '';
    $data['end_date'] = !empty($activity['Activity']['end_date']) ? date('d-m-Y', $activity['Activity']['end_date']) : '';
    $data['actif'] = $activity['Activity']['actif'] ? 'yes' : 'no';
    $data['c32'] = (string) $activity['Activity']['c32'];
    $data['c33'] = (string) $activity['Activity']['c33'];
    $data['c34'] = (string) $activity['Activity']['c34'];
    $data['c35'] = (string) $activity['Activity']['c35'];
    $data['c36'] = (string) $activity['Activity']['c36'];
    $data['c37'] = (string) $activity['Activity']['c37'];
    $data['c38'] = (string) $activity['Activity']['c38'];
    $data['c39'] = (string) $activity['Activity']['c39'];
    $data['c40'] = (string) $activity['Activity']['c40'];
    $data['c41'] = (string) $activity['Activity']['c41'];
    $data['c42'] = (string) $activity['Activity']['c42'];
    $data['c43'] = (string) $activity['Activity']['c43'];
    $data['c44'] = (string) $activity['Activity']['c44'];
    $data['c45'] = (string) $activity['Activity']['c45'];
    $data['project'] = (string) $activity['Activity']['project'];
    $data['budget_customer_id'] = (string) $activity['Activity']['budget_customer_id'];
    $data['code4'] = (string) $activity['Activity']['code4'];
    $data['code5'] = (string) $activity['Activity']['code5'];
    $data['code6'] = (string) $activity['Activity']['code6'];
    $data['code7'] = (string) $activity['Activity']['code7'];
    $data['code8'] = (string) $activity['Activity']['code8'];
    $data['code9'] = (string) $activity['Activity']['code9'];
    $data['code10'] = (string) $activity['Activity']['code10'];
    $data['activated'] = $activity['Activity']['activated'] ? 'yes' : 'no';
    //$data['accessible_profit'] = (array) isset($activityProfitRefer[$activity['Activity']['id']]) && !empty($activityProfitRefer[$activity['Activity']['id']]) ? array_merge($activityProfitRefer[$activity['Activity']['id']]) : array();
    //$data['linked_profit'] = (string) !empty($linkedProfitRefer[$activity['Activity']['id']]) ? $linkedProfitRefer[$activity['Activity']['id']] : '';
    $data['project_manager_id'] = !empty($activity['Activity']['project_manager_id']) ? array($activity['Activity']['project_manager_id']) : array();
    $data['backupPM'] = !empty($activity['Activity']['project_manager_id']) ? array($activity['Activity']['project_manager_id'] => 0) : array();
    if(isset($listManger[$activity['Activity']['id']]) && !empty($listManger[$activity['Activity']['id']])){
        $backupManager = $listManger[$activity['Activity']['id']];
        $data['backupPM'] = $data['backupPM'] + $backupManager;
        $data['project_manager_id'] = array_merge($data['project_manager_id'], array_keys($backupManager));
    }
    $data['action.'] = '';
    $dataView[] = $data;
    $activityListIds[] = $activity['Activity']['id'];
}
?>
<script type="text/javascript">
// var wdTable = $('.wd-table');
// var heightTable = $(window).height() - wdTable.offset().top - 40;
	////heightTable = (heightTable < 500) ? 500 : heightTable;
// wdTable.css({
    // height: heightTable,
// });
// $(window).resize(function(){
    // heightTable = $(window).height() - wdTable.offset().top - 40;
   ////heightTable = (heightTable < 500) ? 500 : heightTable;
    // wdTable.css({
        // height: heightTable,
    // });
// });

var wdTable = $('.wd-table');
	$(document).ready(set_slick_table_height);
	$(window).resize(set_slick_table_height);
	function set_slick_table_height(){
		if( wdTable.length){
			var heightTable = $(window).height() - wdTable.offset().top - 45;
			console.log(heightTable);
			wdTable.css({
				height: heightTable,
			});
			heightViewPort = heightTable - 72;
			wdTable.find('.slick-viewport').height(heightViewPort);
			console.log( heightViewPort, "   ");
			clearInterval(wdTable);
		}
	}

    var ControlGrid;
    // manual history filter
    var _history = <?php echo empty($history) ? '{}' : json_encode($history) ?>;
    var historyData = new $.z0.data(_history);
    var historyPath = <?php echo json_encode($this->params['url']['url']) ?>;

    function resizeHandler(){
        var _cols = ControlGrid.getColumns();
        var _numCols = _cols.length;
        var _gridW = 0;
        var columnWidth = {};
        for (var i=0; i<_numCols; i++) {
            _gridW += _cols[i].width;
            columnWidth[_cols[i].id] = _cols[i].width;
        }
        $('#wd-header-custom').css('width', _gridW);

        historyData.set('columnWidth', columnWidth);
        // call save here
        // **
        saveFilter();
    }
    function sortHandler(ev, info){
        // if( info ){
        //     var columnSort = [{
        //         columnId: info.sortCol.id,
        //         sortAsc: info.sortAsc
        //     }];
        //     historyData.set('columnSort', columnSort);
        // } else {
        //     historyData.set('columnSort', ev);
        // }
        // // call save here
        // // **
        // saveFilter();
    }

    function applyFilter(){
        // apply filters
        // sorter
        // var sorter = historyData.get('columnSort', []);
        // if( sorter ){
        //     ControlGrid.sort(sorter);
        // }
        // // header fields
        // var filters = historyData.get('filters', {});
        // HistoryFilter.data = filters;
        // HistoryFilter.parse();
        HistoryFilter.init();
        // var header = ControlGrid.getHeaderRow();
        // $(header).find(':input[name][rel!="no-history"][type!="file"]').on('change', function(){
        //     var e = $(this);
        //     var name = e.attr("name");
        //     var val = (HistoryFilter.getVal(e, "radio") || HistoryFilter.getVal(e, name, "checkbox") || e.val());
        //     filters[name] = val;
        //     historyData.set('filters', filters);

        //     saveFilter();
        // });
    }
    var saveTimer;
    function saveFilter(){
        clearTimeout(saveTimer);
        saveTimer = setTimeout(function(){
            $.z0.History.save(historyPath, historyData);
        }, 750);
    }

    function number_format (number, decimals, dec_point, thousands_sep) {
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
    var DataValidator = {};
    var activityHasLoading = new Array();

    (function($){
            var $this = SlickGridCustom;
            var  activityColumn = <?php echo json_encode($activityColumn); ?>;
            var  viewManDay = <?php echo json_encode($viewManDay); ?>;
            var activityListIds = <?php echo json_encode($activityListIds); ?>;
            var employeeName = <?php echo json_encode($employeeName); ?>;
            var  map = <?php echo json_encode($map); ?>;
            var  url = <?php echo json_encode(urldecode($this->Html->link('%1$s', array('action' => 'detail', '%2$s')))); ?>;
            var  urlActivityTask = <?php echo json_encode(urldecode($this->Html->link('%1$s', array('controller' => 'activity_tasks', 'action' => 'index', '%2$s')))); ?>;
            $this.onApplyValue = function(item){
                $.extend(item, {backupPM : []})
            };
            var backupText = {
                regex :  /<strong>\(B\)<\/strong>/,
                text : '<strong>(B)</strong>'
            };
            $this.moduleAction = 'activity_review_';
			$FIELDS = <?php echo json_encode($FIELDS); ?>;
            $.extend(Slick.Editors,{
                decimalValue  : function(args){
                    $.extend(this, new Slick.Editors.textBox(args));
                    this.input.attr('maxlength' , 10).keypress(function(e){
                        var key = e.keyCode ? e.keyCode : e.which;
                        if(!key || key == 8 || key == 13){
                            return;
                        }
                        var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
                        if(val != '0' && !/^(([\-1-9][0-9]{0,9})|(0))(\.[0-9]{0,2})?$/.test(val)){
                            e.preventDefault();
							$(e.currentTarget).addClass('KO');
                            return false;
                        }
						else
						{
							$(e.currentTarget).removeClass('KO');
						}
                    });
                },
                GetProjectManager : function(args){
                    var $options,hasChange = false,isCreated = false;
                    var defaultValue = [];
                    var scope = this;
                    var preload = function(){
                        // Get Ajax Employee
                        var getEmployee = function(){
                            if(scope.getValue(true) === true){
                                return;
                            }
                            scope.setValue(true);
                            $.ajax({
                                url : '<?php echo $html->url(array('action' => 'get_project_manager')); ?>',
                                cache :  false,
                                type : 'GET',
                                success : function(data){
                                    $options.html(data);
                                    scope.input.html('');
                                    scope.setValue(defaultValue);
                                    scope.input.select();
                                    // filter menu
                                    initMenuFilter($('.select-employees'));

                                    $options.find('.id-hander').each(function(){
                                        var $el =  $(this);
                                        $.each(defaultValue, function(undefined,v){
                                            if($el.val() == v){
                                                $el.prop('checked' , true);
                                                if(args.item.backupPM[v] == '1'){
                                                    $el.closest('tr').find('.bk-hander').prop('checked' , true);
                                                }
                                                return false;
                                            }
                                        });
                                    }).add($options.find('.bk-hander')).click(function(){
                                        var list = [],c = args.column.id ,
                                        $hander = $(this).closest('tr').find('.id-hander');
                                        args.item.backupPM = {};
                                        //args.item.project_manager_id = {};
                                        if(this.checked && !$hander.is(':checked')){
                                            $hander.prop('checked' , true);
                                        }
                                        //var count = 0;
                                        $options.find('.id-hander:checked').each(function(){
                                            var $el =  $(this),id= $el.val();
                                            list.push(id);
                                            //args.item.project_manager_id[count] = id;
                                            args.item.backupPM[id] = $el.closest('tr').find('.bk-hander:checked').length > 0 ? 1 : 0;
                                            //count++;
                                        });
                                        args.item[c] = list;
                                        hasChange = true;
                                        scope.setValue(list);
                                    });
                                    $options.find('.no-employee input').click(function(){
                                        getEmployee();
                                    });
                                }
                            });
                        };
                        getEmployee();
                    };
                    $.extend(this, new BaseSlickEditor(args));

                    $options = $('<div class="multiSelectOptionCustoms" style="position: absolute; z-index: 99999; visibility: hidden;max-height:150px;"></div>').appendTo('body');
                    var hideOption = function(){
                        scope.input.removeClass('active').removeClass('hover');
                        $options.css({
                            visibility:'hidden',
                            display : 'none'
                        });
                    }
                    this.input = $('<a href="javascript:void(0);" class="multiSelect"></a>')
                    .appendTo(args.container)
                    .hover( function() {
                        scope.input.addClass('hover');
                    }, function() {
                        scope.input.removeClass('hover');
                    })
                    .click( function(e) {
                        // Show/hide on click
                        if(scope.input.hasClass('active')) {
                            hideOption();
                        } else {
                            var offset = scope.input.addClass('active').offset();
                            $options.css({
                                top:  offset.top + scope.input.outerHeight() + 'px',
                                left: offset.left + 'px',
                                visibility:'visible',
                                display : 'block'
                            });
                            if(scope.input.width() < 320){
                                $options.width(320);
                            }
                        }
                        if(e.stopPropagation) {
                            e.stopPropagation();
                        } else {
                            e.returnValue = false;
                        }
                        return false;
                    });

                    $(document).click( function(event) {
                        if(!($(event.target).parents().andSelf().is('.multiSelectOptionCustoms'))){
                            hideOption();
                        }
                    });

                    var destroy = this.destroy;
                    this.destroy = function () {
                        $options.remove();
                        destroy.call(this, $.makeArray(arguments));
                    };

                    this.getValue = function (val) {
                        if(this.input.html() == 'Loading ...'){
                            if(val ==true){
                                return true;
                            }
                            return '';
                        }
                        return this.input.html().split(',');
                    };

                    this.setValue = function (val) {
                        if(val === true){
                            val = 'Loading ...';
                        }else{
                            val = Slick.Formatters.GetProjectManager(null,null,val, args.column, args.item);
                        }
                        this.input.html(val);
                    };

                    this.loadValue = function (item) {
                        defaultValue = item[args.column.field] || "";
                    };

                    this.serializeValue = function () {
                        if(!isCreated){
                            this.loadValue(args.item);
                            preload();
                        }
                        return scope.getValue();
                    };

                    var applyValue = this.applyValue;
                    this.applyValue = function (item, state) {
                        if($.isEmptyObject(item)){
                            applyValue.call(this, item , state);
                        }
                        $.extend(item ,args.item , true);
                    };

                    this.isValueChanged = function () {
                        return (hasChange == true);
                    };

                    this.validate = function () {
                        var option = $this.fields[args.column.id] || {};
                        var result = {
            				valid: true,
            				message: typeof option.message != 'undefined' ? option.message : $this.t('This information is not blank!')
            			},val = this.getValue();
            			if(option.allowEmpty === false && !val.length && !this.isCreate){
            				result.valid = false;
            			}
            			if(result.valid && val.length){
            				if(option.maxLength && val.length > option.maxLength){
            					result = {
            						valid: false,
            						message: $this.t('Please enter must be no larger than %s characters long.' , option.maxLength)
            					};
            				}else if($.isFunction(args.column.validator)){
            					result = args.column.validator.call(this, val, args);
            				}
            			}
            			if(!result.valid && result.message){
            				this.tooltip(result.message , result.callback);
            			}
            			return result;
                    };

                    this.focus();
                }
            });
            $this.onCellChange = function(args){
                $.each (activityColumn , function(key){
                    if(!this.calculate){
                        return;
                    }
                    var cal = this.calculate;
                    if (this.match) {
                        $.each(this.match , function(e){
                            var val = args.item[map[this]] ? parseFloat(args.item[map[this]] , 10) : 0;
                            cal = cal.replace(new RegExp(this , 'g'),val);
                        });
                    }
                    cal = eval('(' + cal + ');');

                    if(!$.isNumeric(cal)){
                        cal = 0;
                    }else if( Math.floor(args.item[key]) != args.item[key]){
                        cal = Number(cal).toFixed(2);
                    }
                    args.item[key]= cal;
                });
                return true;
            };
            var startCurrentYear = <?php echo json_encode('01-01-'.date('Y', time()));?>;
            var endCurrentYear = <?php echo json_encode('31-12-'.date('Y', time()));?>;
            var startMonthYear = <?php echo json_encode('01-'.date('m', time()).'-'.date('Y', time()));?>;
            var endMonthYear = <?php echo json_encode('31-'.date('m', time()).'-'.date('Y', time()));?>;
            $.extend(Slick.Formatters,{
                Action : function(row, cell, value, columnDef, dataContext){
                    var ACID = dataContext.id ? dataContext.id : 0;
                    if(columnDef.id == 'consumed_current_year'){
                        var customUrl = dataContext.id + '?start='+startCurrentYear+'&end='+endCurrentYear;
                        if(activityHasLoading.length != 0 && $.inArray(ACID, activityHasLoading) != -1){
                            return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + $this.t(url,value || '',customUrl) + '</span>', columnDef, dataContext);
                        } else {
                            return Slick.Formatters.HTMLData(row, cell, '<span class="row-number hasLoading">' + $this.t(url,value || '',customUrl) + '</span>', columnDef, dataContext);
                        }
                    } else if(columnDef.id == 'consumed_current_month'){
                        var customUrl2 = dataContext.id + '?start='+startMonthYear+'&end='+endMonthYear;
                        if(activityHasLoading.length != 0 && $.inArray(ACID, activityHasLoading) != -1){
                            return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + $this.t(url,value || '',customUrl2) + '</span>', columnDef, dataContext);
                        } else {
                            return Slick.Formatters.HTMLData(row, cell, '<span class="row-number hasLoading">' + $this.t(url,value || '',customUrl2) + '</span>', columnDef, dataContext);
                        }
                    } else {
                        if(activityHasLoading.length != 0 && $.inArray(ACID, activityHasLoading) != -1){
                            return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + $this.t(url,value || '',dataContext.id) + '</span>', columnDef, dataContext);
                        } else {
                            return Slick.Formatters.HTMLData(row, cell, '<span class="row-number hasLoading">' + $this.t(url,value || '',dataContext.id) + '</span>', columnDef, dataContext);
                        }
                    }
                },
                HyperlinkCellFormatter : function(row, cell, value, columnDef, dataContext) {
                    return Slick.Formatters.HTMLData(row, cell,$this.t(urlActivityTask,value || '', dataContext.id), columnDef, dataContext);
                },
                numberVal : function(row, cell, value, columnDef, dataContext){
                    value = value ? value : 0;
                    var icon = '';
                    if(columnDef.id == 'completed' || columnDef.id =='total_costs_var' || columnDef.id == 'internal_costs_var'
                    || columnDef.id == 'external_costs_var' || columnDef.id == 'external_costs_progress'
                    || columnDef.id == 'assign_to_profit_center' || columnDef.id =='assign_to_employee'
                    ){
                        icon = '%';
                    }
                    var ACID = dataContext.id ? dataContext.id : 0;
                    if(activityHasLoading.length != 0 && $.inArray(ACID, activityHasLoading) != -1){
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ') + '' +icon+ '</span>', columnDef, dataContext);
                    } else {
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number hasLoading">' + number_format(value, 2, ',', ' ') + '' +icon+ '</span>', columnDef, dataContext);
                    }
        		},
                numberValEuro : function(row, cell, value, columnDef, dataContext){
                    value = value ? value : 0;
                    var ACID = dataContext.id ? dataContext.id : 0;
                    if(activityHasLoading.length != 0 && $.inArray(ACID, activityHasLoading) != -1){
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ') + ' '+ $this.currency  +'</span>', columnDef, dataContext);
                    } else {
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number hasLoading">' + number_format(value, 2, ',', ' ') + ' '+ $this.currency  + '</span>', columnDef, dataContext);
                    }
        		},
                numberValManDay : function(row, cell, value, columnDef, dataContext){
                    value = value ? value : 0;
                    var ACID = dataContext.id ? dataContext.id : 0;
                    if(activityHasLoading.length != 0 && $.inArray(ACID, activityHasLoading) != -1){
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ') + ' ' +viewManDay+ '</span> ', columnDef, dataContext);
                    } else {
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number hasLoading">' + number_format(value, 2, ',', ' ') + ' ' +viewManDay+ '</span> ', columnDef, dataContext);
                    }
        		},
                GetProjectManager : function(row, cell, value, columnDef, dataContext){
                    var _value = [];
                    $.each(value, function(i,val){
                        _value.push($this.selectMaps['project_manager_id'][val] + (dataContext.backupPM[val] == '1' ? backupText.text : ''));
                    });
                    return Slick.Formatters.HTMLData(row, cell, _value.join(', '), columnDef, dataContext);
                }
            });

            var initMenuFilter = function($menu){
                var $filter = $('<div class="context-menu-filter"><span><input type="text" rel="no-history"></span></div>');
                $menu.before($filter);
                var timeoutID = null, searchHandler = function(){
                    var val = $(this).val();
                    var te = $($menu).find('tbody tr td.wd-employ-data div label').html();
                    $($menu).find('tbody tr td.wd-employ-data div label').each(function(){
                        var $label = $(this).html();
                        $label = $label.toLowerCase();
                        val = val.toLowerCase();
                        if(!val.length || $label.indexOf(val) != -1 || !val){
                            $(this).parent().parent().parent().removeClass('wd-displays');
                        } else{
                            $(this).parent().parent().parent().addClass('wd-displays');
                        }
                    });
                };

                $filter.find('input').click(function(e){
                    e.stopImmediatePropagation();
                }).keyup(function(){
                    var self = this;
                    clearTimeout(timeoutID);
                    timeoutID = setTimeout(function(){
                        searchHandler.call(self);
                    } , 200);
                });
            };
            $this.canModified =  <?php echo json_encode($canModified); ?>;
			$this.currency = <?php echo json_encode($bg_currency); ?>;
            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.columnAlignRightAndEuro = <?php echo json_encode($columnAlignRightAndEuro); ?>;
            $this.columnAlignRightAndManDay = <?php echo json_encode($columnAlignRightAndManDay); ?>;
            $this.columnNotCalculationConsumed = <?php echo json_encode($columnNotCalculationConsumed); ?>;
            var totalHeaders = <?php echo json_encode($totalHeaders);?>;
            $this.url =  '<?php echo $html->url(array('action' => 'update_review')); ?>';
            $activityColumns = {};
			$.each($FIELDS,function(_ind,_field){
				$activityColumns[_field] = {defaulValue : ''};
			});
            $this.fields = {
                id : {defaulValue : 0},
                price : {defaulValue : ''},
                md : {defaulValue : ''},
                actif : {defaulValue : ''},
                raf : {defaulValue : ''},
                c32: {defaulValue : ''},
                c33: {defaulValue : ''},
                c34: {defaulValue : ''},
                c35: {defaulValue : ''},
                c36: {defaulValue : ''},
                c37: {defaulValue : ''},
                c38: {defaulValue : ''},
                c39: {defaulValue : ''},
				c40: {defaulValue : ''},
                c41: {defaulValue : ''},
                c42: {defaulValue : ''},
                c43: {defaulValue : ''},
                c44: {defaulValue : ''},
				c45: {defaulValue : ''},
                activated : {defaulValue : ''},
                budget_customer_id : {defaulValue : ''},
                backupPM: {defaulValue : ''},
                project_manager_id : {defaulValue : ''}
            };
            $.extend($this.fields,$activityColumns);
            ControlGrid = $this.init($('#project_container'),data,columns , {
                enableAddRow : true,
                showHeaderRow : true
            });
            var exporter = new Slick.DataExporter('/activities/export_excel_review');
            ControlGrid.registerPlugin(exporter);
            $('#export-table').click(function(){
                exporter.submit();
                return false;
            });
            $(ControlGrid.getHeaderRow()).delegate(":input", "change keyup", function (e) {
                var text = $(this).val();
                if( text != '' ){
                    $(this).parent().css('border', 'solid 2px orange');
                } else {
                    $(this).parent().css('border', 'none');
                }
            });
            var Columns = ControlGrid.getColumns();
            /*
            HistoryFilter.setVal = function(name, value){
                console.log(name);
                console.log(value);
                var $data = $("[name='"+name+"']").each(function(){
                    var $element = $(this);
                    if($element.is(':checkbox') || $element.is(':radio')){
                        if(!$.isArray(value)){
                            value = [value];
                        }
                        $element.prop('checked', $.inArray($element.val(), value) != -1);
                    }else{
                        $element.val(value);
                        $element.keypress();
                    }
                    $element.data('__auto_trigger' , true);
                    $element.change();
                });
                var _cols = ControlGrid.getColumns();
                var _numCols = cols.length;
                var _gridW = 0;
                for (var i=0; i<_numCols; i++) {
                    _gridW += _cols[i].width;
                }
                $('#wd-header-custom').css('width', _gridW);
                return $data.length > 0;
            }*/
            /**
             * Calculation width of grid.
             */
            var cols = ControlGrid.getColumns();
            var numCols = cols.length;
            var gridW = 0;
            for (var i=0; i<numCols; i++) {
                gridW += cols[i].width;
            }
            ControlGrid.onScroll.subscribe(function(args, e, scope){
                //$('.row-number').parent().addClass('row-number-custom');
            });
            ControlGrid.onSort.subscribe(function(args, e, scope){
                //$('.row-number').parent().addClass('row-number-custom');
                sortHandler(args, e);
            });
            ControlGrid.onColumnsResized.subscribe(function (e, args) {
				resizeHandler();
			});
            if(columns){
                var headerConsumed = '<div id="wd-header-custom" class="slick-headerrow-columns" style="width: '+gridW+'px">';
                $.each(columns, function(index, value){
                    var idOfHeader = 'activity_review_' + value.id;
                    var valOfHeader = (totalHeaders[value.id] || totalHeaders[value.id] == 0) ? totalHeaders[value.id] : '';

                    if(value.id === 'sales_man_day'
                    || value.id === 'total_costs_man_day'
                    || value.id === 'internal_costs_forecasted_man_day'
                    || value.id === 'external_costs_man_day'
                    || value.id === 'internal_costs_budget_man_day'
                    ){
                        valOfHeader = number_format(valOfHeader, 2, ',', ' ') + ' ' +viewManDay;
                    } else if(value.id === 'sales_sold' || value.id === 'sales_to_bill'
                    || value.id === 'sales_billed' || value.id === 'sales_paid'
                    || value.id === 'total_costs_budget' || value.id === 'total_costs_forecast'
                    || value.id === 'total_costs_engaged' || value.id === 'total_costs_remain'
                    || value.id === 'internal_costs_budget' || value.id === 'internal_costs_forecast'
                    || value.id === 'internal_costs_engaged' || value.id === 'internal_costs_remain'
                    || value.id === 'external_costs_budget' || value.id === 'external_costs_forecast'
                    || value.id === 'external_costs_ordered' || value.id === 'external_costs_remain'
                    || value.id === 'external_costs_progress_euro'
                    || value.id === 'internal_costs_average'
                    ){
                        valOfHeader = number_format(valOfHeader, 2, ',', ' ') + ' '+ $this.currency ;
                    } else {
                        if(valOfHeader){
                            valOfHeader = number_format(valOfHeader, 2, ',', ' ');
                        }
                    }
                    idOfHeader = idOfHeader.replace('.', '_');
                    var left = 'l'+index;
                    var right = 'r'+index;
                    headerConsumed += '<div class="ui-state-default slick-headerrow-column wd-row-custom '+left+' '+right+'" id="'+idOfHeader+'"><p>'+valOfHeader+'</p></div>';

                });
                headerConsumed += '</div>';
                $('.slick-header-columns').after(headerConsumed);
            }
            var company_id = <?php echo json_encode($company_id);?>;
            $('#FilterStatusActivity').change(function(){
                $('#FilterStatusActivity option').each(function(){
                    if($(this).is(':selected')){
                        var id = $('#FilterStatusActivity').val();
                        window.location = ('/activities/review/'+company_id+'?actiReview=' +id);
                    }
                });
            });
            /* Slow performace here */
            /* @todo: fix performance @huupc */
            $("#add_vision_staffing_news").live('click',function(){
                $("#dialog_vision_staffing_news").dialog('option',{title:'Vision Staffing+ Filter'}).dialog('open');
            });

            $(".cancel").live('click',function(){
                $("#dialog_vision_staffing_news").dialog('close');
            });

            $("#ok_sum").click(function(){
                $("#form_vision_staffing_news").submit();
            });

            $('#ProjectProjectGanttId0,#ProjectProjectGanttId1').click(function(){
                if($('#ProjectProjectGanttId1').prop('checked')){
                    $('#display-real-time').show().find('input').prop('disabled' , false);
                }else{
                    $('#display-real-time').hide().find('input').prop('disabled' , true);
                }

            }).filter(':checked').click();

            $('#ProjectProjectGanttIdNews0, #ProjectProjectGanttIdNews1').click(function(){
                if($('#ProjectProjectGanttIdNews1').prop('checked')){
                    $('#display-real-time-news').show().find('input').prop('disabled' , false);
                }else{
                    $('#display-real-time-news').hide().find('input').prop('disabled' , true);
                }

            }).filter(':checked').click();

            var dataView = ControlGrid.getDataView();

            function ajaxRequestDataForActivity(dataSend){
                if( !dataSend.length )return;
                setTimeout(function(){
                    $.ajax({
                        url: '<?php echo $html->url(array('action' => 'handleDataOfActivity')); ?>',
                        //async: false,
                        type : 'POST',
                        dataType : 'json',
                        data: {
                            'activityIds' : JSON.stringify(dataSend),
                            'activityColumn' : JSON.stringify(activityColumn),
                            'employeeName' : JSON.stringify(employeeName),
                            'map' : JSON.stringify(map)
                        },
                        success:function(data) {
                            if(data){
                                dataView.beginUpdate();
                                $.each(data, function(acId, acVal){
                                    var row = ControlGrid.getData().getRowById(acId);
                                    var item = ControlGrid.getData().getItem(row);
                                    if( typeof item != 'object' )return;
                                    var extdata = $.extend(true, item, acVal);
                                    dataView.updateItem(acId, extdata);
                                    setTimeout(function(){
                                        // ControlGrid.updateRow(row);
                                        $('#row-' + acId).find('div span').removeClass('hasLoading');
                                        activityHasLoading.push(acId);
                                    }, 50);
                                });
                                dataView.endUpdate();
                                setTimeout(function(){
                                    $('#name').trigger('keyup');
                                }, 50);
                            }
                            setTimeout(function(){
                                if(activityListIds && activityListIds.length !=0){
                                    var _sendActivity = [];
                                    if(activityListIds.length >= 500){
                                        _sendActivity = activityListIds.splice(0, 500);
                                    } else {
                                        _sendActivity = activityListIds;
                                        activityListIds = [];
                                        applyFilter();
                                    }
                                    ajaxRequestDataForActivity(_sendActivity);
                                } else {
                                    applyFilter();
                                }
                            }, 50);
                        }
                    });
                }, 50);
            }
            if(activityListIds && activityListIds.length !=0){
                var sendActivity = [];
                if(activityListIds.length >= 500){
                    sendActivity = activityListIds.splice(0, 500);
                } else {
                    sendActivity = activityListIds;
                    activityListIds = [];
                }
                ajaxRequestDataForActivity(sendActivity);
            }
            function setupScroll(){
                $("#scrollTopAbsenceContent").width($(".grid-canvas").width()+50);
                $("#scrollTopAbsence").width($(".slick-viewport").width());
            }
            setTimeout(function(){
                setupScroll();
            }, 2500);
            $("#scrollTopAbsence").scroll(function () {
                $(".slick-viewport").scrollLeft($("#scrollTopAbsence").scrollLeft());
            });
            $(".slick-viewport").scroll(function () {
                $("#scrollTopAbsence").scrollLeft($(".slick-viewport").scrollLeft());
            });
            history_reset = function(){
                var check = false;
                $('.multiselect-filter').each(function(val, ind){
                    var text = '';
                    if($(ind).find('input').length != 0){
                        text = $(ind).find('input').val();
                    } else {
                        text = $(ind).find('span').html();
                        if( text == "<?php __('-- Any --');?>" || text == '-- Any --'){
                            text = '';
                        }
                    }
                    if( text != '' ){
                        $(ind).css('border', 'solid 2px orange');
                        check = true;
                    } else {
                        $(ind).css('border', 'none');
                    }
                });
                if(!check){
                    $('#reset-filter').addClass('hidden');
                } else {
                    $('#reset-filter').removeClass('hidden');
                }
            }
            resetFilter = function(){
                // HistoryFilter.stask = '{}';
                // HistoryFilter.send();
                // $('.multiselect-filter').each(function(val, ind){
                    // if($(ind).find('input').length != 0){
                        // $(ind).find('input').val('');
                    // } else {
                        // $(ind).find('span').html("<?php __('-- Any --');?>");
                    // }
                    // $(ind).css('border', 'none');
                    // $('#reset-filter').addClass('hidden');
                // });
                // setTimeout(function(){
                    // location.reload();
                // }, 500);
				$.ajax({
					url : '/employees/history_filter',
					type: 'POST',
					data: {
						data: {
							path: HistoryFilter.here,						
						}
					},
					success : function(respons){
						var _data =  $.parseJSON(respons);
						$.each(_data, function( _index, _val){
							if( _index.indexOf('Resize') == -1){
								 _data[_index]='';
							}
							
						});
						HistoryFilter.stask = _data;
						HistoryFilter.send();
						setTimeout(function(){
							location.reload();
						}, 500);
					}
				});
            }
    })(jQuery);
</script>
<?php
echo $this->Form->create('ExportVision', array('url' => array('controller' => 'activity_tasks', 'action' => 'export_system'), 'type' => 'file'));
echo $this->Form->hidden('showType');
echo $this->Form->hidden('summary');
echo $this->Form->hidden('from');
echo $this->Form->hidden('to');
echo $this->Form->hidden('activated');
echo $this->Form->hidden('activityName');
echo $this->Form->hidden('family');
echo $this->Form->hidden('subFamily');
echo $this->Form->hidden('profit_center');
echo $this->Form->hidden('employee');
echo $this->Form->end();
?>
