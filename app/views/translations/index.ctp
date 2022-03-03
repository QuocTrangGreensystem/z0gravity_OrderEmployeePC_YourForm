<?php echo $html->script(array(
	'slick_grid/lib/jquery-ui-1.8.16.custom.min',
	'slick_grid/lib/jquery.event.drag-2.0.min',
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
	'slick_grid/slick.grid.origin',
	'slick_grid_custom',
	'slick_grid/slick.grid.activity',
));

echo $html->css(array(
	'jquery.multiSelect',
	'slick_grid/slick.grid',
	'slick_grid/slick.edit',
	'slick_grid/slick.pager',
	'slick_grid/slick.common',
	'preview/slickgrid',
	
));
?>
<?php echo $html->css('jquery.dataTables'); ?>
<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->script('jquery.validation.min'); ?>
<?php echo $html->script('jquery.dataTables'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); 

$employee_info = $this->Session->read('Auth.employee_info');
$is_sas = $employee_info['Employee']['is_sas'] && empty($employee_info['Company']['id']);
if((!empty($is_sas) && $is_sas == 1) || ($is_sas == 0 && empty($employee_info['Employee']['company_id']))){
	$isAdminSas = 1;
}else{
	$isAdminSas = 0;
}
?>
<style>
	::-webkit-scrollbar {
		width: 4px;
		height: 4px;
	}
	::-webkit-scrollbar-track {
		box-shadow: inset 0 0 5px #F2F5F7; 
		border-radius: 5px;
		background-color: #fff;
	}

	::-webkit-scrollbar-thumb {
		background: #C6CCCF;; 
		border-radius: 5px;
	}

	html body {
		scrollbar-face-color: #C6CCCF	; 
		scrollbar-highlight-color: #F2F5F7;
		scrollbar-shadow-color: #ffffff; 
		scrollbar-3dlight-color: #ffffff;
		scrollbar-arrow-color: #C6CCCF; 
		scrollbar-track-color: #F2F5F7;
		scrollbar-darkshadow-color: #F2F5F7;
	}
	.select-display, .select-nextblock, .select-nextline {
		width: 100%;
		padding: 3px 0;
	}
	.display td,
	.display th {
		vertical-align: middle;
	}
	.wd-overlay {
		position: relative;
	}
	.wd-overlay span {
		display: block;
		position: absolute;
		left: 45%;
		top: 10px;
		width: 16px;
		height: 16px;
		background: url(<?php echo $html->url('/img/ajax-loader.gif') ?>) no-repeat;
		display: none;
	}
	.index {
		cursor: move;
	}
	.display td {
		background: #fff;
	}
	.msg {
		position: fixed;
		top: 40%;
		left: 40%;
		width: 20%;
		background: #fff;
		padding: 10px;
		border: 10px solid #eee;
		border-radius: 6px;
		display: none;
		color: #000;
		text-align: center;
	}
	.unsortable .index,
	.locked .index {
		cursor: default;
	}
	.row-hidden td {
		background: #ffc;
	}
	.wd-table-container{
		 position: relative;
	 }
	 .slick-viewport .slick-row .slick-cell.wd-moveline{
		 padding: 0;
	 }
	 .wd-moveline.slick-cell-move-handler svg{
		 padding: 0;
	 }
	 .slick-cell-move-handler {
		cursor: move;
	}
	.wd-table-container{
		margin-top: 15px;
	}
	.btn.add-field{
		position: absolute;
		width: 50px;
		height: 50px;
		line-height: 50px;
		text-align: center;
		border-radius: 50%;
		z-index: 5;
		top: 20px;
		margin: 0;
		right: 15px;
		box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2);
		background-color: #247FC3;
		transition: all 0.3s ease;
	}
	.btn.add-field:hover{
		transform: rotate(45deg);
	}
	.btn.add-field:before{
		content: '';
		background-color: #fff;
		position: absolute;
		width: 2px;
		height: 20px;
		top: calc( 50% - 10px);
		left: calc( 50% - 1px);
	}
	.btn.add-field:after{
		content: '';
		position: absolute;
		background-color: #fff;
		width: 20px;
		height: 2px;
		left: calc( 50% - 10px);
		top: calc( 50% - 1px);
	}

	.listTrans{
		position: absolute;
		top: 51px;
		right: 20px;
		width: 40px;
		height: 40px;
		overflow: hidden;
		transition: all 0.3s ease-in-out;
		z-index: 3;
		display: none;
		box-shadow: 0px 4px 12px rgba(0,0,0, 0.15);
		background-color: #fff;
		padding: 10px;
		padding-bottom: 0;
		box-sizing: border-box;
		border-radius: 5px;
	}
	.listTrans.active{
		display: block;
		width: 300px;
		height: 332px;
	}
	.listTrans li:not(:last-child){
		border-bottom: 1px solid #F3F3F3;
	}
	.listTrans li{
		transition: all 0.3s ease;
	}
	.listTrans li a{
		font-size: 14px;
		color: #424242;
		font-weight: 400;
		line-height: 40px;
		padding: 0 7px;
		display: block;
		width: calc(100% - 35px);
		white-space: nowrap;
		text-overflow: ellipsis;
		overflow: hidden;
		transition: all 0.3s ease-in;
	}
	.listTrans .col-search{
		display: block;
		border: 1px solid #E1E6E8;
		background-color: #FFFFFF;
		box-shadow: 0 0 10px 1px rgba(29,29,27,0.06);
		border-radius: 3px;
		box-sizing: border-box;
	}
	.listTrans li:hover{
		background-color: #eff6f0a8;
	}
	.listTrans li:hover a{
		color: #6EAF79;
	}
	.listTrans ul.listTranslate{
		max-height: 300px;
		overflow: auto;
		scrollbar-color: #C6CCCF #fff;
		scrollbar-width: thin;
	}
	.col-search input{
		display: block;
		height: 40px;
		line-height: 40px;
		background: #FBFBFC url(/img/new-icon/search.png) left 10px center no-repeat;
		padding-left: 30px;
		background-size: 15px 15px;
		font-style: italic;
		border: 0;
		box-sizing: border-box;
		width: 100%;
	}
	.search_notmatch{
		display: none;
	}
	.listTrans li.added {
		background-color: #eff6f0a8;
		cursor: auto;
		
	}
	.listTrans li.added a{
		color: #6EAF79;
	}
	svg .svg-a{
		fill:none;
	}
	svg .svg-b{
		fill:#bcbcbc;
		transition: 0.1s ease;
	}
	svg .svg-fill-green {
		fill: #6EAF79;
	}
	.listTrans li{
		position: relative;
	}
	.listTrans li svg{
		position: absolute;
		right: 0px;
		top: 13px;
		opacity: 0;
		transition: all 0.3s ease;
	}
	.listTrans li.added svg{
		opacity: 1;
		right: 10px;
	}
	.wd-table .wd-disable-row{
		background-color: #f2f5f7 !important;
	}
	.wd-table .wd-disable-row .slick-cell-move-handler{
		cursor: auto;
	}
	.circle-name img{
		width: 35px;
	}
</style>
<?php
	$current_nonedit = array();
	$svg_icons = array(
		'checked' => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14"><g transform="translate(-317 -66)"><rect class="svg-a" width="14" height="14" transform="translate(317 66)"></rect><path class="svg-fill-green" d="M9.791,1.412h0L4.314,7.757h0a.648.648,0,0,1-1.01,0h0L.209,4.171h0A.9.9,0,0,1,0,3.585a.777.777,0,0,1,.714-.827A.668.668,0,0,1,1.219,3h0l2.59,3L8.781.242h0A.668.668,0,0,1,9.285,0,.778.778,0,0,1,10,.827.9.9,0,0,1,9.791,1.412Z" transform="translate(319 69)"></path></g></svg>'
	);
	$dataView_hide = array();
	$dataView_show = array();

	$i = 1;
	
	foreach ($data as $field) {
		if(!empty($field['Translation']['id'])){
			$dataAll = array(
				'id' => $field['Translation']['id'],
				'no.' => $i++,
				'MetaData' => array()
			);
			$show_field = !empty($field['TranslationSetting']['show'])? $field['TranslationSetting']['show'] : 0;
			$dataAll['show'] = !empty($field['TranslationSetting']['show'])? $field['TranslationSetting']['show'] : 0;
			$dataAll['original_text'] = $field['Translation']['original_text'];
			if( in_array($dataAll['original_text'], $non_edit_row)){
				$current_nonedit[] = $dataAll['original_text'];
				$dataAll['MetaData']['cssClasses'] = 'wd-disable-row';
			}
			if( in_array($currentPage, $dragDropPages) ){
				foreach($langs as $code => $name):
					$entry_text = '';
					if( isset($entries[$field['Translation']['original_text']][$code]) ) $entry_text = $entries[$field['Translation']['original_text']][$code];
					$dataAll[$code] = $entry_text;
				endforeach;
			}else{
				$entries = Set::combine($field['TranslationEntry'], '{n}.code', '{n}', '{n}.company_id');
				foreach($langs as $code => $name):
					if( isset($entries[$company_id][$code]) )
						$entry = $entries[$company_id][$code];
					else $entry['text'] = '';
					
					$dataAll[$code] = $entry['text'];
				endforeach;
			}
			$dataAll['block_name'] = isset($field['TranslationSetting']['block_name']) ? $field['TranslationSetting']['block_name'] : '';
			$dataAll['next_block'] = !empty($field['TranslationSetting']['next_block']) ? $field['TranslationSetting']['next_block'] : 0;
			$dataAll['next_line'] = !empty($field['TranslationSetting']['next_line']) ? $field['TranslationSetting']['next_line'] : 0;
			$dataAll['page'] = !empty($field['Translation']['page'])? $field['Translation']['page'] : 0;
			$dataAll['field'] = !empty($field['Translation']['field'])? $field['Translation']['field'] : '';
			$dataAll['company_id'] = !empty($field['TranslationSetting']['company_id'])? $field['TranslationSetting']['company_id'] : 0;
			$dataAll['can_update'] = !empty($field['Translation']['field']) && !empty($pm_update_field[$field['Translation']['field']]) ? $pm_update_field[$field['Translation']['field']] : array();
			
			$dataAll['moveline'] = '';
			if($currentPage == 'Details'){
				if($show_field == 1){
					$dataView_show[] = $dataAll;
				}else{
					$dataView_hide[$field['Translation']['id']] = $dataAll;
				}
			}else{
				$dataView_show[] = $dataAll;
			}
		}
	}
	if(!empty($dataView_hide)){
		$datas = Set::sort($dataView_hide, '{n}.original_text', 'asc');
		$dataView_hide = array();
		foreach($datas as $k => $v){
			$dataView_hide[$v['id']] = $v;
		}
	}

 ?>
<div id="wd-container-main" class="wd-project-admin">
	<?php echo $this->element("project_top_menu") ?>
	<div class="wd-layout">
		<div class="wd-main-content">
			<div class="wd-list-project">
				<div class="wd-title">
				</div>
				<div class="wd-tab">
					<?php echo $this->element("admin_sub_top_menu");?>
					<div class="wd-panel">
						<div class="wd-section" id="wd-fragment-1">
							<?php echo $this->element('administrator_left_menu', array(
								'translationPages' => $pages,
								'currentPage' => $currentPage
							)) ?>
							<div class="wd-content"style="position: relative;">
								<div id="message-place">
									<?php echo $this->Session->flash();	?>
								</div>
								<?php if($currentPage == 'Details' && !empty($dataView_hide)){ ?>
									<a href="javascript:void(0);" class="btn add-field" id="add_item" style="margin-right:5px;right: 50px;" title="Add item"></a>
									<div class="listTrans hidden">
										<div class="col-search"><input name="listSearch" value="" onkeyup="searchTextTranslate(this);" placeholder="<?php __('Search');?>"/></div>
										<ul class="listTranslate">
											<?php foreach ($dataView_hide as $k => $val){ ?>
												<li><a href="javascript:void(0)" class="transId field-<?php echo $val['id'];?>" data-id="<?php echo $val['id'];?>"><?php echo $val['original_text'];?></a><?php echo $svg_icons['checked']?></li>
											<?php } ?>
										</ul>
									</div>
								<?php } ?>
								<div class="wd-table-container clearfix">
									<div class="wd-table wd-table-2019" id="project_container" style="width: 100%">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php 

	$options = array(
		1 => __('Yes', true),
		0 => __('No', true),
	);
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
		'no.' => array(
			'id' => 'no.',
			'field' => 'no.',
			'name' => '#',
			'width' => 40,
			'noFilter' => 1,
			'sortable' => false,
			'resizable' => false,
			'cssClass' => 'cell-text-center',
		),
		'moveline' => array(
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
		'original_text' => array(
			'id' => 'original_text',
			'field' => 'original_text',
			'name' => __('Original text', true),
			'width' => 250,
			'sortable' => true,
			'resizable' => true,
		),
	);
	$number = count($langs);
	foreach ($langs as $code => $name) {
		$columns[$code] = array(
			'id' => $code,
			'field' => $code,
			'name' => __($name, true),
			'width' => 250,
			'sortable' => true,
			'resizable' => true,
			'editor' => 'Slick.Editors.textBox',
		);
	}
	$columns += array(
		'block_name' => array(
			'id' => 'block_name',
			'field' => 'block_name',
			'name' => __('Block name', true),
			'width' => 250,
			'sortable' => true,
			'resizable' => true,
			'editor' => 'Slick.Editors.textBox',
		),
		'next_block' =>array(
			'id' => 'next_block',
			'field' => 'next_block',
			'name' => __('Next block', true),
			'width' => 130,
			'sortable' => true,
			'resizable' => true,
			'editor' => 'Slick.Editors.selectBox',
		),
		'next_line' =>array(
			'id' => 'next_line',
			'field' => 'next_line',
			'name' => __('Next line', true),
			'width' => 140,
			'sortable' => true,
			'resizable' => true,
			'editor' => 'Slick.Editors.selectBox',
		),
		'show' =>array(
			'id' => 'show',
			'field' => 'show',
			'name' => __('Display', true),
			'width' => 120,
			'sortable' => true,
			'resizable' => true,
			'editor' => 'Slick.Editors.selectBox',
		),
	);
	if( in_array($currentPage, $dragDropPages) ){
		unset( $columns['no.']);
		foreach( $columns as $k => $c){
			$columns[$k]['sortable'] = false;
		}
	}else{
		unset( $columns['moveline']);
	}
	
	if( !in_array($currentPage, $dragDropPages) ){
		unset( $columns['show']);
	}
	$show_can_update = 1;
	if(strpos($currentPage, 'Details') === false || (strpos($currentPage, 'Details') !== false && !in_array($currentPage, array("Details","Details_1", "Details_2", "Details_3", "Details_4", "Details_5")))){
		unset( $columns['block_name']);
		unset( $columns['next_block']);
		unset( $columns['next_line']);
		$show_can_update = 0;
	}
	if(!empty($companyConfigs['can_manage_your_form_field']) && $show_can_update == 1){
		$columns['can_update'] = array(
			'id' => 'can_update',
			'field' => 'can_update',
			'name' => __('Manage update', true),
			'width' => 276,
			'sortable' => true,
			'resizable' => true,
			'editor' => 'Slick.Editors.mselectBox',
			'formatter' => 'Slick.Formatters.pmCanUpdate',
		);
	}
	$i = 1;
	$selectMaps = array(
		'next_block' => $options,
		'next_line' => $options,
		'show' => $options,
		'can_update' => $pm_employee_active,
	);
	$i18n = array(
		'Any' => __('Any', true),
		'-- Any --' => __('-- Any --', true),
		'Delete' => __('Delete', true),
	);
?>
<div class="msg"><?php echo __('Updating...', true); ?></div>
<script type="text/javascript">
var timeoutID;
var selectMaps = <?php echo json_encode($selectMaps); ?>;
dataView_popups = false;
<?php if(!empty($dataView_hide)){?>
	var dataView_popups = <?php echo json_encode($dataView_hide); ?>;
<?php } ?>
var data = <?php echo json_encode($dataView_show); ?>;
function get_grid_option(){
	var _option ={
		showHeaderRow: true,
		frozenColumn: '',
		enableAddRow: false,   
		rowHeight: 40,
		topPanelHeight: 40,
		headerRowHeight: 40
	};
	if( $(window).width() > 992 ){
		return _option;
	}
	else{
		_option.frozenColumn = '';
		return _option;
	}
}
var _menu_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><defs><style>.a-mile{fill:none;}.b-mile{fill:#d3d3d3;}</style></defs><g transform="translate(4 4)"><rect class="a-mile" width="40" height="40" transform="translate(-4 -4)"/><path class="b-mile" d="M-2915-656v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Zm10-5v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Zm10-5v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Z" transform="translate(2935 678)"/></g></svg>';
(function($){
	$(function(){
		var $this = SlickGridCustom;
		var current_company = <?php echo json_encode( !empty($employee_info['Company']['id']) ? $employee_info['Company']['id'] : ''); ?>;
		var langs = <?php echo json_encode($langs); ?>;
		var currentPage = <?php echo json_encode($currentPage); ?>;
		var pm_update_field = <?php echo json_encode( !empty($pm_update_field) ? $pm_update_field : array()); ?>;
		var pm_employee_active = <?php echo json_encode($pm_employee_active); ?>;
		var company_id = <?php echo json_encode($company_id); ?>;
        $this.i18n = <?php echo json_encode($i18n); ?>;
        $this.canModified =  true;
        $this.non_edit_row = <?php echo json_encode($current_nonedit); ?>;	
		$this.url = <?php echo json_encode($html->url(array('action' => 'save'))); ?>;
		$.extend($this,{
			selectMaps : selectMaps,
			canModified: true,
		});
		$.extend(Slick.Formatters,{
			moveLine: function(row, cell, value, columnDef, dataContext){
				return _menu_svg;
			},
			pmCanUpdate: function(row, cell, value, columnDef, dataContext){
				var avatar = '';
				if(value.length > 0){
					$.each(value, function (key, val) {
						avatar +='<a class="circle-name" title="'+ pm_employee_active[val] +'"><img width="35" height="35" class="circle" src="/img/avatar/'+ val +'.png" alt="avatar"></a>';
					});
				}else{
					avatar += $this.i18n['Any'];
				}
                return avatar;
			},
		});
        var columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
        $this.fields = {
            id : {defaulValue : 0},
            original_text : {defaulValue : ''},
            block_name : {defaulValue : ''},
            next_block : {defaulValue : 0},
            next_line : {defaulValue : 0},
            show : {defaulValue : 0},
            page : {defaulValue : currentPage},
            company_id : {defaulValue : company_id},
            can_update : {defaulValue : ''},
            field : {defaulValue : ''},
        };
		$.each(langs, function (code, name) {
			$this.fields[code] = {
				defaulValue : '',
			}
		});
        ControlGrid = $this.init($('#project_container'),data,columns, get_grid_option());
		//Dissable 3 dong dau.
		ControlGrid.onBeforeEditCell.subscribe(function(e, args) {
			var non_edit_row = $this.non_edit_row;
			var columns = args.grid.getColumns(); 
				if ($.inArray(args.item.original_text, non_edit_row)  != -1 ) {
				var cell = columns[args.cell];
				cell = cell.field;				
				var can_edit_cell = ['eng', 'fre', 'can_update'];
				if( $.inArray(cell, can_edit_cell) == -1) return false;
			}
			return true
		});
		
		ControlGrid.setSelectionModel(new Slick.RowSelectionModel());
		var moveRowsPlugin = new Slick.RowMoveManager({
			cancelEditOnDrag: true
		});
		moveRowsPlugin.onBeforeMoveRows.subscribe(function (e, args) {
			var non_edit_row = $this.non_edit_row;
			var len = non_edit_row.length;
			for (var i = 0; i < args.rows.length; i++) {
					// no point in moving before or after itself
				var item_edit = ControlGrid.getDataView().getItem(args.rows[i]).original_text;	
				var item_insert = ControlGrid.getDataView().getItem(args.insertBefore).original_text;	
				if ($.inArray(item_edit, non_edit_row)  != -1 || $.inArray(item_insert, non_edit_row)  != -1) {	
					e.stopPropagation();
					return false;
				}	
				if (args.rows[i] == args.insertBefore || args.rows[i] == args.insertBefore - 1) {
					e.stopPropagation();
					return false;
				}
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
				url : '<?php echo $html->url('/translations/saveOrder/') ?>',
				type : 'POST',
				data : orders,
				success : function(){
				},
				error: function(){
					location.reload();
				}
			});
			ControlGrid.resetActiveCell();
			var dataView = ControlGrid.getDataView();
			dataView.beginUpdate();
			//if set data via grid.setData(), the DataView will get removed
			//to prevent this, use DataView.setItems()
			dataView.setItems(data);
			dataView.endUpdate();
			ControlGrid.setSelectedRows(selectedRows);
			ControlGrid.render();
		});

		ControlGrid.registerPlugin(moveRowsPlugin);
		ControlGrid.onDragInit.subscribe(function (e, dd) {
			// prevent the grid from cancelling drag'n'drop by default
			e.stopImmediatePropagation();
		});
			
		addNewItem = function(){
			ControlGrid.gotoCell(data.length, 1, true);
		};
		function update_table_height(){
			wdTable = $('.wd-table');
			var heightTable = $(window).height() - wdTable.offset().top - 40;
			wdTable.height(heightTable);
			if( SlickGridCustom.getInstance() ) SlickGridCustom.getInstance().resizeCanvas();
		}
		$(window).resize(function(){
			update_table_height();
		});
		update_table_height();
	});
})(jQuery);
</script>
<script>
	$("#btnSave").click(function(){
		$("#flashMessage").hide();
	});
	var current;
	$('#TranslationSaveForm').submit(function(){
		var id = $('#TranslationId').val();
		if( !id )return false;
		$('#btnSave').addClass('grayscale');
		$(this).ajaxSubmit({
			success: function(flash){
				$('#the-flash').html(flash);
				$('#flashMessage').show();
				var tr = $(current).closest('tr');
				$('.t-entry').each(function(){
					var t = $(this),
						code = t.data('code');
					var inp = tr.find('#' + code + '_' + id);
					inp.val(t.val());
					inp.siblings('span').html(t.val());
				});
				$("#reset-form").trigger('click');
				$('#btnSave').removeClass('grayscale');
			},
			error: function(){
				$("#reset-form").trigger('click');
				$('#btnSave').removeClass('grayscale');
			}
		});
		return false;
	});
	function editfield(e, id){
		current = e;
		var tr = $(e).parent().parent(),
			text = tr.find('strong').text(),
			block_name = tr.find('.block-name').text();
		
		$("#TranslationId").val(id);
		$("#original_text").val(text);
		$("#text_block_name").val(block_name);
		tr.find('[name=aaa]').each(function(){
			var code = $(this).prop('id').replace('_' + id, '');
			$('#text_' + code).val($(this).val());
		});
		$("#flashMessage").hide();
		$(".error-message").hide();
		$("div.wd-input,input,select").removeClass("form-error");
		$('#table-list-admin tbody tr').addClass('locked');
	}
	$("#reset-form").click(function(){
		current = null;
		$("#TranslationSaveForm input[type='text']").val('');
		$("#original_text").val('');
		$('#TranslationId').val('');

		//$("#flashMessage").hide();
		$(".error-message").hide();
		$("div.wd-input input, select").removeClass("form-error");
		$('#table-list-admin tbody tr').removeClass('locked');
	});
	<?php if( in_array($currentPage, $dragDropPages) ): ?>

	$('tbody tr').each(function(){
		var me = $(this);
		if( me.find('select.select-display').val() == 0 ){
			me.addClass('row-hidden');
		}
	});
	$('.select-display').each(function(){
		var me = $(this);
		me.change(function(){
			var tr = me.parent().children('span');
			me.prop('disabled', true);
			tr.show();
			$.ajax({
				url: '<?php echo $html->url('/translations/saveSetting') ?>',
				type: 'POST',
				data: {
					data: {
						id : me.data('setting-id'),
						translate: me.data('translate'),
						show : me.val()
					}
				},
				success: function(data){
					tr.hide();
					me.prop('disabled', false);
					if( me.val() == 1 ){
						me.parent().removeClass('row-hidden');
					} else {
						me.parent().parent().addClass('row-hidden');
					}
				},
				error: function(){
					// tr.hide();
					// me.prop('disabled', false);
					location.reload();
				}
			});
		});
	});
	$('.select-nextline').each(function(){
		var me = $(this);
		me.change(function(){
			var tr = me.parent().children('span');
			me.prop('disabled', true);
			tr.show();
			$.ajax({
				url: '<?php echo $html->url('/translations/saveSettingNextLine') ?>',
				type: 'POST',
				data: {
					data: {
						id : me.data('setting-id'),
						translate: me.data('translate'),
						nextline : me.val()
					}
				},
				success: function(data){
					tr.hide();
					me.prop('disabled', false);
				},
				error: function(){
					location.reload();
				}
			});
		});
	});
	$('.select-nextblock').each(function(){
		var me = $(this);
		me.change(function(){
			var tr = me.parent().children('span');
			me.prop('disabled', true);
			tr.show();
			$.ajax({
				url: '<?php echo $html->url('/translations/saveSettingNextBlock') ?>',
				type: 'POST',
				data: {
					data: {
						id : me.data('setting-id'),
						translate: me.data('translate'),
						nextblock : me.val()
					}
				},
				success: function(data){
					tr.hide();
					me.prop('disabled', false);
				},
				error: function(){
					location.reload();
				}
			});
		});
	});

	var fixHelperModified = function(e, tr) {
		var $originals = tr.children();
		var $helper = tr.clone();
		$helper.children().each(function(index) {
			$(this).width($originals.eq(index).width())
		});
		return $helper;
	};

	var tbody = $("#table-list-admin tbody").sortable({
		helper: fixHelperModified,
		stop: function(e, ui) {
			$('.msg').show('fast');
			var t = $(this).sortable('disable');
			//update current order
			var result = {data : {
				TranslationSetting : []
				}
			};
			$('tr', ui.item.parent()).each(function (i) {
				var tr = $(this);
				var index = tr.children('td.index');
				//update index
				index.html(i);
				tr.data('order', i);
				//get orders
				result.data.TranslationSetting.push({
					id : tr.data('id'),
					company_id: <?php echo $company_id ?>,
					translation_id: tr.data('translate'),
					setting_order : tr.data('order')
				})
			});
			//saving order
			$.ajax({
				url : '<?php echo $html->url('/translations/saveOrder') ?>',
				type: 'POST',
				data: result,
				success: function(data){
					$('.msg').hide('fast');
					t.sortable('enable');
				},
				error: function(){
					location.reload();
				}
			});
		},
		handle: '.index',
		cancel: '.unsortable, .locked',
		opacity: 0.8
	});
	$('.index').disableSelection();
	<?php endif ?>
</script>
<script>
	function setSizePopup(){
		var wrapper = $('.listTrans');
		var listContent = wrapper.find('.listTranslate');
		if( wrapper.hasClass('active')){
			listContent.css('max-height', $(window).height() - listContent.offset().top - 42);
			wrapper.css({
				height: listContent.outerHeight() + 52
			});
		}else{
			wrapper.css({
				height: ''
			});
		}
	};
	if(dataView_popups){
		$( function(){
			
			$('#add_item').on('click', function(){
				if( $('.listTrans').hasClass('active') ){
					$(".listTrans").removeClass('active');
				}else{
					$(".listTrans").addClass('active');
				}
				setSizePopup();
			});
			$('.listTranslate').on('click', 'a', function(){
				var field_id = $(this).data('id');
				if(field_id && dataView_popups[field_id]){
					var _ele_parent = $(".field-"+field_id).closest('li');
					var _show = 0;
					if(_ele_parent.hasClass('added')){
						_ele_parent.removeClass('added');
					}else{
						_show = 1;
						_ele_parent.addClass('added');
					}
					var data = ControlGrid.getData().getItems();
					var new_item = dataView_popups[field_id];
					new_item.show = _show;
					if(_show == 1) data.push(dataView_popups[field_id]);
					else{
						var split_data = [];
						var i = 0;
						$.each(data, function(index, value){
							if(value.id != field_id){
								split_data[i++] = value;
							}
						});
						data = split_data;
					}
					ControlGrid.getData().setItems(data);
					ControlGrid.invalidate();
					ControlGrid.render();
					$.ajax({
						url: '/translations/add_field_display/',
						type: 'post',
						dataType: 'json',
						data: {
							data:{
								field_id: field_id,
								show: _show,
							}
						},
						success: function(res){							
							if( res.result == 'failed'){
								location.reload();
							}
						}
					});
				}
			});
		});
		function wd_oncellchange_callback(item){
			item = item.data;
			if( item.show == '0'){
				data = ControlGrid.getData().getItems();
				var key = 0;
				$.each(data, function( k, v){
					if( v.id == item.id) key = k;
				});
				var new_data = $.removeFromObjectbyKey(data, key);
				ControlGrid.getData().setItems(new_data);
				ControlGrid.invalidate();
				ControlGrid.render();			
			}
		}
		function searchTextTranslate(elm){
		var _this = $(elm);
		var val = _this.val().toLowerCase();
		if(val ==''){
			_this.closest('.listTrans').find('li').removeClass('search_notmatch'); //display block
			return;
		}
		var text_elm = _this.closest('.wd-content').find('ul li a.transId');
		$.each( text_elm, function( i, _el){
			var el = $(_el);
			var text = el.text().toLowerCase();
			var is_show = text.match(val);
			if( is_show) el.closest('li').removeClass('search_notmatch');// display block
			else el.closest('li').addClass('search_notmatch'); // display  none
		});
		
	}}
	$(window).resize(function () {
		setSizePopup();
	});
</script>