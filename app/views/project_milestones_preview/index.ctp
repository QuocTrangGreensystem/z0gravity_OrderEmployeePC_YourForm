<?php
echo $this->Html->css(array(
    'slick_grid/slick.grid.activity',
    'jquery.multiSelect',
    'projects',
    'slick_grid/slick.grid_v2',
    'slick_grid/slick.pager',
    'slick_grid/slick.common_v2',
    'slick_grid/slick.edit',
    'preview/project_milestones'
));
echo $this->Html->script(array(
    'history_filter', 
    'jquery.multiSelect',
    // 'responsive_table',
    'slick_grid/lib/jquery-ui-1.8.16.custom.min',
    'slick_grid/lib/jquery.event.drop-2.0.min',
    'slick_grid/lib/jquery.event.drag-2.2',
    'slick_grid/slick.core',
    'slick_grid/slick.dataview',
    'slick_grid/controls/slick.pager',
    'slick_grid/slick.formatters',
    'slick_grid/plugins/slick.cellrangedecorator',
    'slick_grid/plugins/slick.cellrangeselector',
    'slick_grid/plugins/slick.cellselectionmodel',
    'slick_grid/plugins/slick.rowselectionmodel',
    'slick_grid/plugins/slick.rowmovemanager',
    'slick_grid/slick.editors',
    'slick_grid/slick.grid',
    'slick_grid_custom_milestones',
    'slick_grid/slick.grid.activity',
	'slick_grid/plugins/slick.dataexporter',
    'jquery.ui.touch-punch.min'
));
echo $html->css('slick'); 
echo $html->css('slick-theme');  
echo $html->script('slick.min'); 

echo $this->element('dialog_projects');

$canModified = (!empty($canModified) && !$_isProfile ) || ($_isProfile && $_canWrite);
//Ticket 515, translation
$language = Configure::read('Config.language');
if(!empty($nameColumn) && $language == 'eng'){
	$columnName = $nameColumn['Menu']['name_eng'];
}elseif(!empty($nameColumn) && $language == 'fre'){
	$columnName = $nameColumn['Menu']['name_fre'];
}else{
	$columnName = 'Milestone';
}
?>
<style>
	.wd-slick-slider .slides .wd-slider-item{
		width: 170px;
		float: left;
	}
	.slick-viewport .slick-row .slick-cell.slick-no-left-spacing{
		padding-left: 0;
		margin-left: 0;
	}

	.slick-row .slick-cell.selected svg .cls-1 {
		fill: #fff;
		transition: 0.3s ease;
	}
	.multiselect-filter {
		padding-top: 7px;
	}
	.wd-tab > .wd-panel{
		max-width: 1920px !important;
	}
	.wd-tab{
		max-width: none;
	}
	.slick-cell.wd-moveline.slick-cell-move-handler {
		padding: 0;
	}
	.wd-moveline.slick-cell-move-handler svg {
		padding-top: unset;
	}
	.slick-viewport .grid-canvas .slick-row .slick-cell.loading{
		background-image: url(/img/loading_white.gif);
		background-size: 15px;
	}
	.ui-datepicker .ui-datepicker-buttonpane {
		display: none;
	}
	.content-right-inner, .wd-layout > .wd-main-content > .wd-tab{
		margin-bottom: 40px;
	}
</style>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<!-- export excel  -->
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'project_milestones_preview', 'action' => 'export', $projectName['Project']['id'])));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<!-- /export excel  -->
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>

            <div class="wd-tab"><div class="wd-panel">
            <div class="wd-list-project">
                <div class="wd-title">
                    <a href="<?php echo $html->url("/project_phase_plans_preview/phase_vision/" . $projectName['Project']['id']) ?>" class="btn btn-gantt" title="<?php __('Gantt+') ?>"></a>
                    <a href="javascript:void(0);" class="export-excel-icon-all" id="export-table" style="margin-right:5px; " title="<?php __('Export Excel')?>"><span><?php __('Export Excel') ?></span></a>
					<?php if( $canModified){ ?>
						<a href="javascript:void(0);" class="btn btn-plus-green wd-hide" id="add-new-sales" style="margin-right:5px;" onclick="addNewMilestonesButton();" title="<?php __('Add an item') ?>"></a>
					<?php }?>
                    <a href="javascript:void(0);" class="btn btn-reset-filter hidden" id="reset-filter" onclick="resetFilter();" style="margin-right:5px;" title="<?php __('Reset filter') ?>"></a>
                    <a href="javascript:void(0);" class="btn btn-expand" id="table-expand" onclick="expandTable();" title="Expand"></a>
                    <a href="javascript:void(0);" class="btn btn-table-collapse" id="table-collapse" onclick="collapse_table();" title="Collapse table" style="display: none;"></a>
                </div>
                <div id="message-place">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    ?>
                </div>
                <p><?php //__('In the column #, drag and drop to reorder') ?></p>
                <br clear="all"  />
                <div id="milestone-slider-container" class="milestone-slider-container">
					<div id="milestone-slider" class="wd-slick-slider loading-mark">
						<div class="slides">
							<?php 
							
								$i = 0; 
								$next_ms = '';
								$min='99999999999';
								$active_ms ='';
								$currentDate = strtotime(date('d-m-Y', time()));
								$compare_year = '';
								foreach ($listprojectMilestones as $p) { 
									$milestone_date = strtotime($p['milestone_date']);
									$flag = abs($currentDate - $milestone_date);
									$milestone_year = new DateTime();
									$milestone_year->setTimestamp($milestone_date);
									$milestone_year = $milestone_year->format('Y');
									if(( $compare_year && $milestone_year != $compare_year) || $i == 0){
										$listprojectMilestones[$i]['year'] = $milestone_year;
									}
									$compare_year = $milestone_year;
									if($min > $flag && $milestone_date <= $currentDate){
										$min = $flag;
										$active_ms = $p['id'];
									}
									$i++;
								}
								$current_item = 0;
								$i = 0;
								foreach ($listprojectMilestones as $p) { 
									if( $p['milestone_date'] !='0000-00-00'){
										$milestone_date = strtotime($p['milestone_date']);
										$nearDate = $currentDate - $milestone_date;
										$item_class = '';
										if( !empty( $p['year']) ){
											$item_class .= ' has-year';
										}
										if( $current_item ){
											 $item_class .= ' next-item';
											 $current_item = 0;
										}
										if ($active_ms == $p['id']) {
											$item_class .= ' active-item';
											$current_item = 1;
										}else{
											if($milestone_date > $currentDate){
												$item_class .= ' last-item flag-item';
											}
										}
										if($p['validated']){
											$item_class .= ' milestone-validated';
										}else{
											if ($milestone_date < $currentDate) {
												$item_class .= ' milestone-mi milestone-red';
											} else if($milestone_date > $currentDate) {
												$item_class .= ' milestone-blue';
											} else {
												$item_class .= ' milestone-orange';
											}
										}
										if($milestone_date < $currentDate) { $item_class .= ' out_of_date'; }
										?>
											<div data-num=<?php echo $i; ?> class="wd-slider-item" data-time="<?php echo $milestone_date;?>">
												<div class="milestones-item <?php echo $item_class; ?>" data-id="<?php echo $p['id']; ?>">
													<div class="date-milestones">
														<span><b><?php echo date("d", strtotime($p['milestone_date'])); ?></b></span>
														<span><?php echo __(date("M", strtotime($p['milestone_date'])),true); ?></span>
													</div>
													<p><?php echo $p['project_milestone']; ?></p>
													<?php if( !empty( $p['year']) ){ ?>
														<div class="milestone-year">
															<?php echo $p['year'];?> 
														</div>
													<?php } ?> 
												</div>
											</div>
										<?php 
										$i++;
									}
								}
							?>
						</div>
					</div>

                </div>
                <br clear="all"  />
                <div class="wd-table-container loading-mark">
                    <div class="wd-table" id="project_container" style="width:100%;height:400px;">
                    </div>
					<?php if( $canModified){ ?>
						<div class="wd-popup-container" id="wd-add-new-milestone">
							<div class="wd-popup"></div>
							<a class="add-new-item" href="javascript:void(0);" onclick="addNewMilestonesButton();"><img title="Add an item" src="/img/new-icon/add.png"></a>
						</div>
					<?php } ?> 
                </div>
                <div id="pager" style="width:100%;height:0px; overflow: hidden;">
                </div>
            </div>
            <?php echo $this->element('grid_status'); ?>
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
$columns = array(
	array(
        'id' => 'moveline',
        'field' => 'moveline',
        'name' => '',
        'width' => 40,
        'minWidth' => 40,
		'maxWidth' => 40,
        'sortable' => false,
        'resizable' => false,
		'noFilter' => 0,
		'behavior' => 'selectAndMove',
		'cssClass' => 'wd-moveline slick-cell-move-handler',
		'formatter' => 'Slick.Formatters.moveLine',
		'ignoreExport' => true
    ),
    array(
        'id' => 'project_milestone',
        'field' => 'project_milestone',
        'name' => __($columnName, true),
        'width' => 150,
        'minWidth' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox',
		'cssClass' => 'slick-no-left-spacing',
        'formatter' => 'Slick.Formatters.iconColor',
		'headerCssClass' => 'slick-header-merged',
    ),
    array(
        'id' => 'initial_date',
        'field' => 'initial_date',
        'name' => __('Initial date', true),
        'width' => 150,
        'minWidth' => 150,
        'maxWidth' => 150,
        'sortable' => true,
        'resizable' => false,
        'editor' => 'Slick.Editors.datePicker',
		'cssClass' => "wd-slick-date",
        'datatype' => 'datetime',
        'formatter' => 'Slick.Formatters.DateTime',
    ),
    array(
        'id' => 'milestone_date',
        'field' => 'milestone_date',
        'name' => __('Planned date', true),
        'width' => 150,
        'minWidth' => 150,
        'maxWidth' => 150,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.datePicker',
		'cssClass' => "wd-slick-date",
        'datatype' => 'datetime',
        'formatter' => 'Slick.Formatters.DateTime',
    ),
    array(
        'id' => 'effective_date',
        'field' => 'effective_date',
        'name' => __('Effective date', true),
        'width' => 150,
        'minWidth' => 150,
        'maxWidth' => 150,
        'sortable' => true,
        'resizable' => false,
        'editor' => 'Slick.Editors.datePicker',
		'cssClass' => "wd-slick-date",
        'datatype' => 'datetime',
        'formatter' => 'Slick.Formatters.DateTime',
    )
);
// them alert colums.
$listkey = array();
if(!empty($listAlert)){
    foreach ($listAlert as $_id => $_name) {
        $num = $numberAlert[$_id];
        $listkey[$_id] = 'alert_' . $_id;
        $columns[] = array(
            'id' => 'alert_' . $_id,
            'field' => 'alert_' . $_id,
            'name' => $_name,
            'width' => 150,
            'minWidth' => 80,
            'maxWidth' => 200,
            'sortable' => false,
            'resizable' => true,
        );
    }
}
$columns[] = array(
    'id' => 'validated',
    'field' => 'validated',
    'name' => __('Validated', true),
    'width' => 100,
    'minWidth' => 100,
    'maxWidth' => 120,
    'sortable' => false,
    'resizable' => true,
    // 'editor' => 'Slick.Editors.selectBox',
    'cssClass' => 'wd-milestone-validate',
    'formatter' => 'Slick.Formatters.validateSwitch'
);
$columns[] = array(
    'id' => 'action.',
    'field' => 'action.',
    'name' =>'',
    'width' => 40,
    'minWidth' => 40,
    'maxWidth' => 60,
    'sortable' => false,
    'resizable' => false,
    'cssClass' => 'wd-action-column',
    'noFilter' => 1,
    'formatter' => 'Slick.Formatters.Action',
	'ignoreExport' => true
);

foreach($columns as $key => $column){
	if(!empty($loadFilter) && !empty($loadFilter[$column['field']. '.Resize'])){
		$columns[$key]['width'] = intval($loadFilter[$column['field']. '.Resize']);
	}
}

$i = $j = 1; 
$dataView = array();
$selectMaps = array(
    'validated' => array("no" => __('No', true), "yes"=>__('Yes', true)),
);
foreach ($projectMilestones as $projectMilestone) {
    $data = array(
        'id' => $projectMilestone['ProjectMilestone']['id'],
        'project_id' => $projectName['Project']['id'],
        'no.' => $i++
    );
    $data['project_milestone'] = $projectMilestone['ProjectMilestone']['project_milestone'];
	$data['initial_date'] = !empty($projectMilestone['ProjectMilestone']['initial_date']) ? date('d-m-Y', $projectMilestone['ProjectMilestone']['initial_date']) : '';
    $data['milestone_date'] = $str_utility->convertToVNDate($projectMilestone['ProjectMilestone']['milestone_date']);
    $data['effective_date'] = !empty($projectMilestone['ProjectMilestone']['effective_date']) ? date('d-m-Y', $projectMilestone['ProjectMilestone']['effective_date']) : '';
    $data['validated'] = $projectMilestone['ProjectMilestone']['validated'] ? 'yes' : 'no';
    $data['weight'] = $projectMilestone['ProjectMilestone']['weight'];
    $data['action.'] = '';
    $data['moveline'] = '';
    foreach ($listkey as $key => $value) {
        $date = $str_utility->convertToVNDate($projectMilestone['ProjectMilestone']['milestone_date']);
        $num = $numberAlert[$key];
        $t = '-' . $num . ' day';
        $data[$value] = date('d-m-Y', strtotime($t, strtotime($date)));
    }
    $dataView[] = $data;
}
$projectName['Project']['start_date'] = $str_utility->convertToVNDate($projectName['Project']['start_date']);
$projectName['Project']['end_date'] = $str_utility->convertToVNDate($projectName['Project']['end_date']);
if ($projectName['Project']['end_date'] == "" || $projectName['Project']['end_date'] == '0000-00-00') {
    $projectName['Project']['end_date'] = $str_utility->convertToVNDate($projectName['Project']['planed_end_date']);
}
$i18n = array(
    '-- Any --' => __('Any', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true),
    'Milestone date must between %s and %s' => __('Milestone date must between %s and %s', true),
	'yes' => __('Yes', true),
	'no' => __('No', true),
	
);
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
		<?php if( $canModified){ ?>
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
		<?php } ?> 
    </div>
</div>
<script type="text/javascript">
	var canModified = <?php echo json_encode($canModified); ?>;
	var projectName = <?php echo json_encode($projectName['Project']); ?>;
    var wdTable = $('.wd-table');
    var heightTable = $(window).height() - wdTable.offset().top - 80;
    heightTable = Math.max(400, heightTable);
    wdTable.css({
        height: heightTable,
    });
    function get_grid_option(){
        var _option ={
            //frozenColumn: '',
            //enableAddRow: false,            
            // showHeaderRow: true,
            rowHeight: 40,
            // forceFitColumns: true,
            topPanelHeight: 40,
            headerRowHeight: 40,
        };

        if( $(window).width() > 992 ){
            return _option;
        }
        else{
            //_default.frozenColumn = '';
            _option.forceFitColumns = false;
            return _option;
        }
    }
	var _menu_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><defs><style>.a-mile{fill:none;}.b-mile{fill:#d3d3d3;}</style></defs><g transform="translate(4 4)"><rect class="a-mile" width="40" height="40" transform="translate(-4 -4)"/><path class="b-mile" d="M-2915-656v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Zm10-5v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Zm10-5v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Z" transform="translate(2935 678)"/></g></svg>';
    var DateValidate = {};
    (function($){
        $(function(){
            var $this = SlickGridCustom;
			$this.isExporting = 0;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  canModified;
            // For validate date
            var listkey = <?php echo json_encode($listkey) ?>;
            var numberAlert = <?php echo json_encode($numberAlert) ?>;
            var getTime = function(value){
                value = value.split("-");
                return (new Date(parseInt(value[2] ,10), parseInt(value[1], 10), parseInt(value[0], 10))).getTime();
            }
            DateValidate.startDate = function(value){
                value = getTime(value);
                if(projectName['start_date'] == ''){
                    _valid = true;
                    _message = '';
                    //_message = $this.t('Start-Date or End-Date of Project are missing. Please input these data before full-field this date-time field.');
                } else {
                    //_valid = value >= getTime(projectName['start_date']) && value <= getTime(projectName['end_date']);
                    //_message = $this.t('Date closing must between %1$s and %2$s' ,projectName['start_date'], projectName['end_date']);
                    _valid = value >= getTime(projectName['start_date']);
                    _message = $this.t('Milestone date must larger %1$s' ,projectName['start_date']);
                }
                return {
                    valid : _valid,
                    message : _message
                };
            }
            var actionTemplate =  $('#action-template').html();
            $.extend(Slick.Formatters,{
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,
                    dataContext.project_id,dataContext.project_milestone), columnDef, dataContext);
                },
                ColorMile : function(row, cell, value, columnDef, dataContext){
                    var rightnow = new Date();
                    var dateConvert = dataContext.milestone_date.split("-");
                    var dateCheck = new Date(dateConvert[2]+'-'+dateConvert[1]+'-'+dateConvert[0]);
                    if((dateCheck<rightnow)&&(dataContext.validated=='no')){
                        if(columnDef.id == 'validated'){
                            return '<span style="color: blue;">' + $this.selectMaps.validated[value] + '</span>';
                        }
                        return '<span style="color: blue;">' + value + '</span>';
                    }
                    if((dateCheck>=rightnow)&&(dataContext.validated=='no')){
                        if(columnDef.id == 'validated'){
                            return '<span style="color: red;">' + $this.selectMaps.validated[value] + '</span>';
                        }
                        return '<span style="color: red;">' + value + '</span>';
                    }
                    if(dataContext.validated=='yes'){
                        if(columnDef.id == 'validated'){
                            return '<span style="color: green;">' + $this.selectMaps.validated[value] + '</span>';
                        }
                        return '<span style="color: green;">' + value + '</span>';
                    }
                },
                iconColor: function(row, cell, value, columnDef, dataContext){
                    var rightnow = new Date();
                    var dateConvert = dataContext.milestone_date.split("-");
                    var dateCheck = new Date(dateConvert[2]+'-'+dateConvert[1]+'-'+dateConvert[0]);
					var resl = '';
                    if(dataContext.validated=='yes'){
                        resl = '<i class="milestone-icon milestone-green" data-color="milestone-row-green" data-itemid="' + dataContext.id + '">&nbsp</i><span>' + value + '</span>';
                    } else {
                        if (dateCheck < rightnow) {
                            resl = '<i class="milestone-icon milestone-mi" data-color="milestone-row-mi" data-itemid="' + dataContext.id + '">&nbsp</i><span>' + value + '</span>';
                        } else if(dateCheck > rightnow) {
                            resl = '<i class="milestone-icon milestone-blue" data-color="milestone-row-blue" data-itemid="' + dataContext.id + '">&nbsp</i><span>' + value + '</span>';
                        } else {
                            resl = '<i class="milestone-icon milestone-orange" data-color="milestone-row-orange" data-itemid="' + dataContext.id + '">&nbsp</i><span>' + value + '</span>';
                        }
                    }
					return resl;
                },
				moveLine: function(row, cell, value, columnDef, dataContext){
					return _menu_svg;
				},
				validateSwitch: function(row, cell, value, columnDef, dataContext){
					// return '';
					// console.log(value, dataContext);
					// console.log(value);
					if( $this.isExporting){
						return $this.t(value);
					}
					active_class = (value == 'yes') ? 'validated' : '';
                    return '<a data-itemid="' + dataContext.id + '" onclick="switchValidated.call(this)" class = "wd-switch '+ active_class +'" title = ""><input type="hidden" name="activated" data-value="'+ value +'" data-id = ""></a>';
                },
            });
            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.onCellChange = function(args){
                if(args.item && args.item.validated == ''){
                    args.item.validated = 'no';
                }
				if(projectName['category'] == 3){
					if(args.item && args.item.milestone_date == '' && args.item.initial_date == '' ) return false;
				}
				if(args.item && args.item.milestone_date == '' && args.item.initial_date != '' ) 
					args.item.milestone_date = args.item.initial_date;
				if(args.item && args.item.milestone_date != '' && args.item.initial_date == '' ) 
					args.item.initial_date = args.item.milestone_date;
                if(args.item && args.item.milestone_date != ''){
                    var columnId = args.column.id;
                    columnId = columnId.substring(0, 3);
                    var date = args.item.milestone_date;
                    $.each(listkey, function(_id, _key){
                        var val = numberAlert[_id];
                        var _date;
                        _date = diffDate(date, val);
                        args.item[_key] = _date;
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
                } else {
                    //end of row
                    try {
                        args.grid.gotoCell(args.row + 1, 0);
                    } catch(ex) {}
                }
                return true;
            };
			//Update theo ticket #944. Project Model co the tao milestone ma ko co initial_date va milestone_date
			if(projectName['category'] == 3){
				$this.fields = {
					id : {defaulValue : 0},
					project_id : {defaulValue : projectName['id'], allowEmpty : false},
					project_milestone : {defaulValue : '' , allowEmpty : false},
					initial_date : {defaulValue : '', allowEmpty : false},
					milestone_date : {defaulValue : '', allowEmpty : false},
					effective_date : {defaulValue : '', allowEmpty : true},
					validated : {defaulValue : ''},
					weight: {defaulValue: 0},
					category: {defaulValue: projectName['category']}
				};
			}else{
				$this.fields = {
					id : {defaulValue : 0},
					project_id : {defaulValue : projectName['id'], allowEmpty : false},
					project_milestone : {defaulValue : '' , allowEmpty : false},
					initial_date : {defaulValue : '', allowEmpty : true},
					milestone_date : {defaulValue : '', allowEmpty : true},
					effective_date : {defaulValue : '', allowEmpty : true},
					validated : {defaulValue : ''},
					weight: {defaulValue: 0},
					category: {defaulValue: projectName['category']}
				};
			}
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            dataGrid = $this.init($('#project_container'),data,columns, get_grid_option());
            $('.slick-cell .milestone-icon').each(function(){
                var _this = $(this);
                _this.closest('.slick-row').removeClass('milestone-row-green').removeClass('milestone-row-blue').removeClass('milestone-row-orange').removeClass('milestone-row-mi').addClass(_this.data('color'));
            });
            dataGrid.setSortColumns('weight' , true);
            dataGrid.setSelectionModel(new Slick.RowSelectionModel());
            $('.row-number').parent().addClass('row-number-custom');
            var moveRowsPlugin = new Slick.RowMoveManager({
                cancelEditOnDrag: true
            });
            moveRowsPlugin.onBeforeMoveRows.subscribe(function (e, data) {
                for (var i = 0; i < data.rows.length; i++) {
                        // no point in moving before or after itself
                    if (data.rows[i] == data.insertBefore || data.rows[i] == data.insertBefore - 1) {
                        e.stopPropagation();
                        return false;
                    }
                }
            });
            $(dataGrid.getHeaderRow()).delegate(":input", "change keyup", function (e) {
                var text = $(this).val();
                if( text != '' ){
                    $(this).parent().css('border', 'solid 2px orange');
                } else {
                    $(this).parent().css('border', 'none');
                }
            });
            //fire after row move completed
            moveRowsPlugin.onMoveRows.subscribe(function (e, args) {
                var extractedRows = [], left, right;
                var rows = args.rows;
                var insertBefore = args.insertBefore;
                left = data.slice(0, insertBefore);
                right = data.slice(insertBefore, data.length);
                rows.sort(function(a,b) { return a-b; });
                for (var i = 0; i < rows.length; i++) {
                    extractedRows.push(data[rows[i]]);
                }
                rows.reverse();
                for (var i = 0; i < rows.length; i++) {
                    var row = rows[i];
                    if (row < insertBefore) {
                        left.splice(row, 1);
                    } else {
                        right.splice(row - insertBefore, 1);
                    }
                }
                data = left.concat(extractedRows.concat(right));

                var selectedRows = [];
                for (var i = 0; i < rows.length; i++)
                    selectedRows.push(left.length + i);

                //update no.
                var orders = { data : {} };
                for(var i = 0; i < data.length; i++){
                    data[i]['no.'] = (i+1);
                    data[i].weight = (i+1);
                    orders.data[data[i].id] = (i+1);
                }

                //ajax call
                $.ajax({
                    url : '<?php echo $html->url('/project_milestones/order/' . $projectName['Project']['id']) ?>',
                    type : 'POST',
                    data : orders,
                    success : function(){
                    },
                    error: function(){
                        location.reload();
                    }
                });
                dataGrid.resetActiveCell();
                var dataView = dataGrid.getDataView();
                dataView.beginUpdate();
                //if set data via grid.setData(), the DataView will get removed
                //to prevent this, use DataView.setItems()
                dataView.setItems(data);
                //dataView.setFilter(filter);
                //updateFilter();
                dataView.endUpdate();
                // dataGrid.getDataView.setData(data);
                dataGrid.setSelectedRows(selectedRows);
                dataGrid.render();
            });

            dataGrid.registerPlugin(moveRowsPlugin);
            dataGrid.onDragInit.subscribe(function (e, dd) {
                // prevent the grid from cancelling drag'n'drop by default
                e.stopImmediatePropagation();
            });
            // add new colum grid
            //ControlGrid = $this.init($('#project_container'),data,columns);
            addNewMilestonesButton = function(){
                dataGrid.gotoCell(data.length, 1, true);
            }
            // $(window).resize(function(){
                // dataGrid.resizeCanvas();
            // });
			var exporter = new Slick.DataExporter('<?php echo $this->Html->url( array( 'action' => 'export_excel'));?>');
			dataGrid.registerPlugin(exporter);

			$('#export-table').click(function () {
				$this.isExporting = 1;
				exporter.submit();
				$this.isExporting = 0;
				return false;
			});
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
				if(($('.slick-header-column-sorted').length != 0)||($('.slick-sort-indicator-asc').length != 0)){
					text = '1';
				}
                if( text != '' ){
                    // $(ind).css('border', 'solid 2px orange');
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
			$('.multiselect-filter input').val('').trigger('change');
			$('.multiSelectOptions input[type="checkbox"]').prop('checked', false).trigger('change');
			// dataGrid.setSortColumn();
			$('input[name="project_container.SortOrder"]').val('').trigger('change');
			$('input[name="project_container.SortColumn"]').val('').trigger('change');
			dataGrid.setSortColumn('project_milestone', false);
			$('.slick-header-columns').children().eq(0).trigger('click'); // for first column
			dataGrid.setSortColumn();		
        }
    })(jQuery);
    // minus date - val (val is number).
    function diffDate(date, val){
        var d = date.split('-');
        var newdate = new Date(d[2], d[1]-1, d[0]);
        newdate.setDate(newdate.getDate() - val); // minus the date
        var nd = new Date(newdate);
        var dd = nd.getDate() < 10 ? '0' + nd.getDate() : nd.getDate();
        var mm = nd.getMonth() < 10 ? '0' + (nd.getMonth() + 1) : nd.getMonth() + 1;
        var yyyy = nd.getFullYear();
        var _date = dd + '-' + mm + '-' + yyyy;
        return _date;
    }
    function setupScroll(){
        $("#scrollTopAbsenceContent").width($(".grid-canvas-right:first").width()+50);
        $("#scrollTopAbsence").width($(".slick-viewport-right:first").width());
    }
    setTimeout(function(){
        setupScroll();
    }, 1000);
    $("#scrollTopAbsence").scroll(function () {
        $(".slick-viewport-right:first").scrollLeft($("#scrollTopAbsence").scrollLeft());
    });
    $(".slick-viewport-right:first").scroll(function () {
        $("#scrollTopAbsence").scrollLeft($(".slick-viewport-right:first").scrollLeft());
    });
    reGrid = function(){
        heightTable = $(window).height() - wdTable.offset().top - 80;
        heightTable = Math.max(400, heightTable);
        wdTable.css({
            height: heightTable,
        });
        dataGrid.resizeCanvas();
        $('#milestone-slider .slides').slick('refresh');
    }
    expandTable = function(){
        $('.wd-list-project').addClass('fullScreen');
        reGrid();
        $('#table-collapse').show();
        $('#table-expand').hide();
    }

    collapse_table = function(){
        $('.wd-list-project').removeClass('fullScreen');
        reGrid();
        $('#table-collapse').hide();
        $('#table-expand').show();
    }
    function milestones_slider(){
        //<div id="milestone-slider" class="wd-slick-slider">
        var _slider = $('#milestone-slider .slides');
        var active_index = 0, index=0;

        if( _slider.length == 0) return;
        var item = _slider.children('.wd-slider-item').length;
        if( item){
            _slider.children('.wd-slider-item').each(function(){
                if( $(this).find('.milestones-item').hasClass('active-item')) active_index = index;
                index++;
            });
        }
		index;
        var slider_show = 9;
        if(item <= slider_show){
            active_index = 0;
        }
		_slider.on('init', function(event, slick){
			heightTable = $(window).height() - wdTable.offset().top - 80;
			heightTable = Math.max(400, heightTable);
			wdTable.css({
				height: heightTable,
			});
			
		});
        var slick_slider = _slider.slick({
            infinite: false,
            slidesToShow: slider_show,
            //slidesToScroll: slider_show,
            speed: 600,
            arrows: true,
            dots: false,
            //centerMode: true,
            focusOnSelect: true,
            initialSlide: Math.min( active_index, (index - slider_show) > 0 ? (index - slider_show) : 0 ),
            centerPadding: '0',
            prevArrow: '<button type="button" class="slick-prev"><span><img src="/img/new-icon/arrow-left-gray.png"><img class="img-hover" src="/img/new-icon/arrow-left-brown.png"><span></button>',
            nextArrow: '<button type="button" class="slick-next"><span><img src="/img/new-icon/arrow-right-gray.png"><img class="img-hover" src="/img/new-icon/arrow-right-brown.png"></span></button>',
            responsive:[
                {
                    breakpoint: 1790,
                    settings: {
                        slidesToShow: 7,
						initialSlide: Math.min( active_index, index - Math.max(slider_show-1, 7)),
                    }
                },
                {
                    breakpoint: 1440,
                    settings: {
                        slidesToShow: 7,
						initialSlide: Math.min( active_index, index - Math.max(slider_show-1, 3)),
                    }
                },
                {
                    breakpoint: 1199,
                    settings: {
                        slidesToShow: 5,
						initialSlide: Math.min( active_index, index - Math.max(slider_show-2, 3)),
                    }
                },
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 4,
						initialSlide: Math.min( active_index, index - Math.max(slider_show-3 , 3)),
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 2,
						initialSlide: Math.min( active_index, index - Math.max(slider_show-3 , 7)),
                    }
                },
            ],
        });

		
    }
    milestones_slider();
	function reDrawMilestoneSlider(){
		var _cont = $('#milestone-slider');
		var _slider = $('#milestone-slider .slides');
		_slider.height(_slider.height());
		_cont.addClass('loading');
		_slider.slick('unslick');
		_slider.css('overflow', 'hidden');
		$.ajax({
			url: "<?php echo $html->url(array('action' => 'get_milestone_slider', $projectName['Project']['id']));?>",
			type : 'GET',
			data : '',
			success : function(respons){
				if( respons) {
					_slider.empty().html(respons);
					_slider.height('');
					milestones_slider();
					_slider.css('overflow', '');
					_cont.removeClass('loading');
				}else{
					if(projectName['category'] == 3){
						location.reload();
					}
				}
			},
			error: function(){
				location.reload();
			}
		});
	}
	if( canModified ){
		$('#milestone-slider').on('click', '.milestones-item', function(e){
			//e.preventDefault();
			$('#milestone-slider, .wd-table-container').addClass('loading');
			var _this = $(this);
			color = getColorMilestone(_this);
			$(this).removeClass('last-item');
			$(this).addClass('active-item');
			var _item_id = $(this).data('id');
			updateMilestone(_item_id, color);
		});
	}
	function getColorMilestone(_this){
		var color = '';
		$('#milestone-slider').find('.milestones-item').removeClass('active-item');
		$('#milestone-slider').find('.flag-item').addClass('last-item');
		var out_date = _this.hasClass('out_of_date');
		if( !_this.hasClass('milestone-validated')){
			color = 'green';
			_this.removeClass('milestone-blue milestone-mi milestone-red milestone-orange').addClass('milestone-validated');
		}else{
			if( out_date){
				color = 'mi';
				_this.removeClass('milestone-blue milestone-green milestone-validated milestone-orange').addClass('milestone-mi milestone-red');
			}else{
				color = 'blue';
				_this.removeClass('milestone-green milestone-validated milestone-mi milestone-red milestone-orange').addClass('milestone-blue');
			}
		}
		return color;
	}
	function updateMilestone(_item_id, color){
		$.ajax({
		   url : "<?php echo $html->url('/project_milestones_preview/change_milestone_status/'.$projectName['Project']['id']); ?>" + '/' + _item_id,
			type : 'GET',
			dataType : 'json',
			data : '',
			success : function(respons){
				var success = respons['result'];
				if( success){
					var item = respons.data.ProjectMilestone;
					var item_id = item.id;
					// console.log(item.validated );
					var data = [], selectedRows = 0;
					dataGrid.resetActiveCell();
					var dataView = dataGrid.getDataView();
					var tab_length = dataView.getLength();
					dataView.beginUpdate();
					var i = 0;
					for( i =0; i< dataView.getLength(); i++){
						data[i] = dataView.getItem(i);
						if( data[i]['id'] == item_id){
							data[i].validated = (item.validated == 1) ? 'yes' : 'no';
							data[i].effective_date = (item.effective_date) ? item.effective_date : '';
							selectedRows = i;
						}
					}
					dataView.setItems(data);
					dataView.endUpdate();
					dataGrid.invalidate();
					dataGrid.render();
					var validate_col = dataGrid.getColumnIndex("validated");
					dataGrid.gotoCell(selectedRows, validate_col, false); 
					var _icon = $('.milestone-icon[data-itemid="' + _item_id + '"]');
					_icon.removeClass('milestone-blue milestone-mi milestone-red milestone-orange milestone-green milestone-validated').addClass('milestone-'+color).attr('data-color','milestone-row-'+color);
					if( color=='green') _icon.addClass( 'milestone-validated');
					var _row = _icon.closest('.slick-row');
					_row.removeClass('milestone-row-mi milestone-row-blue milestone-row-green').addClass('milestone-row-'+color);
					// Viet add code last-update here
					$('#milestone-slider, .wd-table-container').removeClass('loading');
				}
				else{
					location.reload();
				}
			},
			error: function(){
				location.reload();
			}
		});
	}
	function switchValidated(){
		if( !canModified) return;
		var _this = $(this);
		_item_id = _this.data('itemid');
		var _item_slider_selected = $('.wd-slider-item [data-id="'+ _item_id +'"]');
		_color = getColorMilestone(_item_slider_selected);
		_this.addClass('loading');
		updateMilestone(_item_id, _color);
	}
    $(window).resize(function(){
        reGrid();
    });
	$('html').on('click', function(e){
	   if( Slick.GlobalEditorLock.isActive() && $('html').find(e.target).length && !($('.wd-table').find('.slick-row').find(e.target).length) && !($('#add-new-sales').find(e.target).length || $(e.target).hasClass('btn-plus-green') ) && !($('#wd-add-new-milestone').find(e.target).length)){
		   Slick.GlobalEditorLock.commitCurrentEdit();
	   }
	});
</script>
