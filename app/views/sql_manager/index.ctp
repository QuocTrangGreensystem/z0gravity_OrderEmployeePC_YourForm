<?php
	echo $this->Html->css(array(
		'slick_grid/slick.grid_v2',
		'slick_grid/slick.pager',
		'slick_grid/slick.common_v2',
		'slick_grid/slick.edit',
		'preview/grid-project',
		'preview/slickgrid',
		'codemirror',
		'preview/tab-admin',
		'layout_admin_2019'
	));
	echo $this->Html->script(array(
		'history_filter',
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
		'slick_grid_custom',
		'slick_grid/slick.grid.activity',
		'jquery.ui.touch-punch.min',
		'codemirror',
		'sql',
		'autorefresh',
	));
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
	/* Column for Slick grid */
	$canModified = !empty( $is_sas);
	$_columns = array();
	
	$_columns[] = array(
        'id' => 'request_name',
        'field' => 'request_name',
        'name' => __('Name', true),
        'width' => 250,
		'cssClass' => 'wd-grey-background',
        'sortable' => true,
        'resizable' => true,
    );
	$_columns[] = array(
        'id' => 'desc',
        'field' => 'desc',
        'name' => __('Description', true),
        'width' => 500,
        'sortable' => true,
        'resizable' => true,
    );
	if ($employee_info['Employee']['is_sas'] == 1) {
		$_columns[] = array(
			'id' => 'company',
			'field' => 'company',
			'name' => __('Company', true),
			'width' => 250,
			'sortable' => true,
			'resizable' => true,
		);
		$_columns[] = array(
			'id' => 'resource',
			'field' => 'resource',
			'name' => __('Resource', true),
			'width' => 300,
			'sortable' => true,
			'resizable' => true,
			'formatter' => 'Slick.Formatters.avaResource',
		);
	}
	$_columns[] = array(
        'id' => 'action',
        'field' => 'action',
        'name' => '',
        // 'name' => __('Action', true),
        'width' => $canModified ? 130 : 50,
        'resizable' => false,
		'noFilter' => 1,
		'formatter' => 'Slick.Formatters.requestAction',
		'cssClass' => 'grid-action',
    );
	$_columns[] = array(
        'id' => 'holder',
        'field' => 'holder',
        'name' => '',
        // 'name' => __('Action', true),
        'width' => 0,
        'minWidth' => 0,
        'maxWidth' => 0,
        'resizable' => false,
		'noFilter' => 1,
		'cssClass' => 'hidden-column',
		'headerCssClass' => 'hidden-column',
    );
	// debug( $dataView); exit;
	$i = 1;
		
	$i18n = array(
		'-- Any --' => __('-- Any --', true),
		'Excute' => __('Excute', true),
	);
	$selects = array('resource' => array());
	foreach( $dataView as $key => $val){
		foreach($val['resource'] as $eID => $eName){
			$selects['resource'][$eID] = $eName;
		}
		foreach($val['company'] as $cID => $cName){
			$selects['company'][$cID] = $cName;
		}
		$dataView[$key]['resource'] = array_keys($dataView[$key]['resource']);
		$dataView[$key]['company'] = array_keys($dataView[$key]['company']);
	}
?>
<style>
.wd-list-project {
    margin-top: 0;
}
.wd-title{
	min-height: 40px;
}
.btn.btn-fullscreen:before {
    content: '' !important;
    width: 100%;
    height: 100%;
    left: 0;
    top: 0;
    position: absolute;
    background: url(/img/new-icon/expand.png) center center no-repeat;
}
.btn.btn-table-collapse{
	top: 0;
	right: 0;
}
.wd-title{
	position: relative;
}
.slick-headerrow-columns .multiselect-filter{
	padding-top: 0;
	margin-top: 4px;
	height: 30px;
} 
.wd-table-2019 .slick-headerrow .slick-headerrow-column input.input-filter, .wd-table-2019 .slick-headerrow .slick-headerrow-column select, .wd-table-2019 .slick-headerrow .slick-headerrow-column a.multiSelect{
	background: transparent;
}
.slick-header-sortable .slick-sort-indicator{
	float: right;
	background: transparent url('/img/new-icon/sort_able.png') center no-repeat;
	height: 100%;
	margin: 0 3px;
}
.slick-header-sortable .slick-sort-indicator.slick-sort-indicator-desc{
	background-image: url('/img/new-icon/sort_able_desc.png');
}
.slick-header-sortable .slick-sort-indicator.slick-sort-indicator-asc{
	background-image: url('/img/new-icon/sort_able_asc.png');
}
/* #wd-container-main {
    background-color: inherit;
} */
.hidden-column{
	display: none !important;
}
.wd-table-container{
	position: relative;
}
.btn.add-field{
    position: absolute;
    width: 50px;
    height: 50px;
    line-height: 50px;
    text-align: center;
    border-radius: 50%;
    z-index: 5;
    top: -25px;
    margin: 0;
    right: 15px;
    /*left: 15px;*/
    box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2);
    background-color: #247FC3;
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
 table tbody tr td.wd-action a.wd-edit{
        margin-top: 0px;
    }
	.btn-blue.btn-old{
		background-color: #0B4578;
	}
	.wd-tab .wd-panel.pm-logged{
		border: none;
	}
	div.wd-input{
		display: block;
		margin-bottom: 15px;
	}
	div.wd-input >label,
	div.wd-input >input,
	.wd-input >select{
		height: 40px;
		line-height: 40px;
		padding: 0 15px;
		width: calc( 100% - 220px);
		display: inline-block;
		min-width: 300px;
	}
	div.wd-input >label{
		width: 140px;
		min-width: 140px;
	}
	.type_buttons .excute{
		background: transparent;
	}
	.wd-submit .btn-form-action.loading{
		padding-right: 38px;
		background: #C6CCCF url(/img/business/wait-1.gif) center right 13px no-repeat;
		background-size: 17px;
	}
	.btn-form-action{
		font-size: 14px;
		line-height: 22px;
		font-weight: 600;
		text-transform: uppercase;	
		color: #fff;
		border: none;
		padding: 14px 27px;
		background-color: #C6CCCF;
		transition: all 0.3s ease;
		border-radius: 3px;
		text-decoration: none;
		background-size: 250%;
		background-image: linear-gradient(to right,#C6CCCF 0%,#C6CCCF 39%,#217FC2 64%,#217FC2 100%);
		display: inline-block;
		margin-left: 20px;
	}	
	.btn-form-action:first-child{
		margin-left: 0
	}
	.btn-form-action:hover{
		background-color: #217FC2;
		background-position: right center;
	}
	.btn-form-action.btn-ok{
		background: #217FC2;
		padding-left: 34px;
		padding-right: 34px;
	}
	.btn-form-action.btn-ok:hover{
		opacity: 0.95;
	}
	.slick-cell .wd-bt-big{
		margin: 0;
	}		
	.slick-cell.grid-action{
		padding: 0;
	}
	.slick-cell.grid-action .wd-actions a{
		width: 39px;
		height: 39px;
		border-right: 1px solid #F2F5F7;
		margin: 0;
		vertical-align: top;
		display: inline-block;
	}
	.slick-cell.grid-action .wd-actions a:last-child{
		border-right-width: 0;
	}
	.wd-table .slick-viewport .slick-cell .circle-name {
		width: 30px;
		height: 30px;
		line-height: 30px;
		font-size: 14px;
		display: inline-block;
	}
	.btn-right {
		float: right;
	}
	#dialog_add_sql,
	#dialog_edit_sql{
		padding: 20px 40px 40px;
	}
	
</style>
<div id="wd-container-main" class="wd-project-admin">
	<div class="wd-layout">
		<div class="wd-main-content">
		<?php echo $this->element("project_top_menu") ?>
			<div class="wd-tab">
			<?php if($is_sas || $is_admin) echo $this->element("admin_sub_top_menu");?>
			<div class="wd-panel">
				<div class="wd-list-project">
					<div class="wd-title">
						<a href="javascript:void(0);" class="btn btn-reset-filter hidden" id="reset-filter" onclick="resetFilter();" style="margin-right:5px;" title="<?php __('Reset filter') ?>"></a>
						<a href="javascript:void(0);" class="btn btn-fullscreen hide-on-mobile" id="table-expand" onclick="expandTable();" title="<?php __("Expand"); ?>"></a>
						<a href="javascript:void(0);" class="btn btn-table-collapse" id="table-collapse" onclick="collapse_table();" title="<?php __('Collapse table') ?>" style="display: none;"></a>
					</div>
					<div id="message-place">
						<?php
						App::import("vendor", "str_utility");
						$str_utility = new str_utility();
						echo $this->Session->flash();
						?>
					</div>
					<div class="wd-table-container" style="width:100%;">
						<?php if ($canModified){ ?>
							<a href="javascript:void(0);" class="btn add-field" id="add_request" style="margin-right:5px;" title="Add an item"></a>
						<?php } ?>
						<div class="wd-table wd-table-2019" id="project_container" style="width:100%; height: 400px;">
						</div>
					</div>				
				</div>
			</div></div>
		</div>
	</div>
</div>
<?php  if( $canModified){ ?>
	<!--pop up-->
	<div id="dialog_add_sql" class="buttons" style="display: none;">
		<div>
			<?php echo $this->Form->create('SqlManager', array('type' => 'POST', 'id' => 'form_create_sql', 'url' => array('controller' => 'sql_manager', 'action' => 'add'))); ?>

			<div class="wd-input">
				<label for="request_name"><?php __("Request Name") ?></label>
				<?php echo $this->Form->input('request_name', array('div' => false, 'label' => false, 'class' => '', 'style' => 'color: #000', 'required' => true)); ?>
			</div>
			<div class="wd-input">
				<label for="desc"><?php __("Description") ?></label>
				<?php echo $this->Form->input('desc', array('div' => false, 'label' => false, 'class' => '', 'style' => 'color: #000', 'required' => true)); ?>
			</div>
			<div class="wd-input">
				<label for="request_type_new"><?php __("Type") ?></label>
				<?php
				echo $this->Form->input('type', array(
					'type' => 'select',
					'id' => 'request_type_new',
					'div' => false,
					'label' => false,
					'rel' => 'no-history',
					'required' => true,
					'style' => 'width: 300px !important',
					'options' => $typelist,
				));
				?>
			</div>
			<div class="wd-input"  style="overflow: visible">
				<label for=""><?php __("Company") ?></label>
				<?php
				echo $this->Form->input('company', array(
					'type' => 'select',
	//                'name' => 'company',
					'id' => 'companyId',
					'div' => false,
					'label' => false,
					'multiple' => true,
					'hiddenField' => false,
					'rel' => 'no-history',
					"empty" => false,
					'required' => true,
					'style' => 'width: 300px !important',
					"options" => $companylist,
				));
				?>
			</div>
			


			<div class="wd-input" id="" style="overflow: visible">
				<label for=""><?php __("Resource") ?></label>
				<?php
				echo $this->Form->input('SqlManagerEmployee.resource', array('div' => false, 'label' => false,
					"empty" => false,
	//                'name' => 'resource',
					'id' => 'resourceId',
					'multiple' => true,
					'type' => 'select',
					'hiddenField' => false,
					'required' => true,
					'style' => 'width: 300px !important',
					"options" => '',
				));
				?>
			</div>
			<div class="wd-input" id="" style="overflow: visible">
	<?php echo $this->Form->input('request_sql', array('type' => 'textarea', 'div' => false, 'label' => false, 'id' => 'code', 'rows' => '10', "cols" => '60')); ?>   

			</div>

	<?php echo $this->Form->input('create_by', array('type' => 'hidden', 'default' => $employee_info['Employee']['id'])); ?>

			<?php //  echo $this->Form->input('id', array('type' => 'hidden')); ?>


	<?php echo $this->Form->end(); ?>
		</div>
		<div style="clear: both;"></div>
		<div class="wd-submit">
			<a href="javascript:void(0)" class="btn-form-action btn-cancel cancel"><?php __("Cancel") ?></a>
			<a href="javascript:void(0)" class="btn-form-action btn-right btn-ok" id="ok_save"><?php __('Save') ?></a>
			<a href="javascript:void(0)" class="btn-form-action btn-right" id="excute-new"><?php __('Excute') ?></a>
		</div>
	</div>

	<div id="dialog_edit_sql" class="buttons" style="display: none;">

	</div>
	<div id="result_sql" class="buttons" style="display: none;">

	</div>
<?php } ?> 

<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
	var wdTable = $('.wd-table');
	var $this = SlickGridCustom;
	var updateLink = '';
	var dataGrid;
	var canModified = <?php echo json_encode($canModified); ?>;
	var selects = <?php echo json_encode($selects); ?>;
	(function($){
		$.extend($this,{
			i18n : <?php echo json_encode($i18n); ?>,
			selectMaps : selects,
			canModified: canModified,
			delete_link: "<?php echo $this->Html->url(array('action' => 'delete', '%ID%')); ?>",
		});
		$.extend(Slick.Formatters,{
			requestAction: function(row, cell, value, columnDef, dataContext){
				var _html = '<div class="wd-actions wd-bt-big">';
				_html += '<a href="<?php echo $this->Html->url(array('controller' => 'reports', 'action' => 'viewReport'));?>/' + dataContext.id + '" target="_blank" class="wd-btn wd-dashboard" title="' + $this.t('Excute') + '" >' + $this.t('Excute') + '</a>';
				if( $this.canModified){
					// edit
					_html += '<a class="wd-btn wd-edit" title="' + $this.t('Edit') + '" href="javascript:void(0)" onclick="editRequire(\'' + dataContext.id + '\');">' + $this.t('Edit') + '</a>';
					
					//delete
					_html += '<a class="wd-btn wd-hover-advance-tooltip" title="' + $this.t('Delete') + '" href="' + $this.delete_link.replace('%ID%' , dataContext.id) +'" onclick="return confirm(\'' + $this.t('Delete?') + '\');">' + $this.t('Delete') + '</a>';
				}				
				_html += '</div>';
				return _html;
			},
			avaResource: function (row, cell, value, columnDef, dataContext) {
                var avatar = '<div class="list-avatars">';
                // avatar = '';
                $.each(value, function (i, val) {
                    avatar += '<span class="circle-name" title="' + $this.selectMaps[columnDef.id][val] + '" data-id="' + val + '"><img alt="avatar" src="' + employeeAvatar_link.replace( '%ID%', val) + '" /></span>'
                });
				avatar += '</div>';
				return avatar;
            },
		});
		var  data = <?php echo json_encode($dataView); ?>;
		// var  data = '';
		var columns = <?php echo jsonParseOptions($_columns, array('editor', 'formatter', 'validator')); ?>;
		
		dataGrid = $this.init($('#project_container'),data,columns, {
			enableCellNavigation: false,
			enableColumnReorder: false,
			showHeaderRow: true,
			editable: false,
			enableAddRow: false,
			headerRowHeight: 40,
			rowHeight: 40,
		});
		var dataView = dataGrid.getDataView();
		$(window).on('resize', function(){
			dataGrid.resizeCanvas();
		});
	})(jQuery);
	
	resetFilter = function () {
		$('.input-filter').val('').trigger('change');
		$('.multiSelectOptions input[type="checkbox"]').prop('checked', false).trigger('change');
		dataGrid.setSortColumn();
		$('input[name="project_container.SortOrder"]').val('').trigger('change');
		$('input[name="project_container.SortColumn"]').val('').trigger('change');

	}
	function reGrid(){
		dataGrid = $this.getInstance();
		dataGrid.resizeCanvas();
	}
	expandTable = function(){
		$('#wd-container-main').addClass('fullScreen');
		$('#table-collapse').show();
		$('#table-expand').hide();
		$('ul.wd-item').hide();
		$(window).trigger('resize');
	}
	collapse_table = function(){
		$('#wd-container-main').removeClass('fullScreen');
		$('#table-collapse').hide();
		$('#table-expand').show();
		$('ul.wd-item').show();
		$(window).trigger('resize');
	}
	function set_table_height(){
		if ( !wdTable.length ) return;
		var heightTable = $(window).height() - wdTable.offset().top - 80;
		heightTable = heightTable > 300 ? heightTable : 300;
		wdTable.css({
			height: heightTable,
		});
	}
	$(document).on('ready', function(){
		set_table_height();
		reGrid();
	});
	$(window).on('resize', function(){
		set_table_height();
		reGrid();
	});
	set_table_height();
	history_reset = function () {
		var check = false;
		$('.multiselect-filter').each(function (val, ind) {
			var text = '';
			if ($(ind).find('input').length != 0) {
				text = $(ind).find('input').val();
			} else {
				text = $(ind).find('span').html();
				if (text == "<?php __('-- Any --');?>" || text == '-- Any --') {
					text = '';

				}
			}
			if (text != '') {
				$(ind).css('border', 'solid 1px #E9E9E9');
				check = true;
			} else {
				$(ind).css('border', 'none');
			}
		});
		if (!check) {
			$('#reset-filter').addClass('hidden');
		} else {
			$('#reset-filter').removeClass('hidden');
		}
	}
	
	
	function postExcute(field, value) {
		//post code excute with field "requireID" for idSql and "requireSql" for sql code
		// add new "viewIframe", "viewIframeText"
		if( field == 'viewIframeText' || field == 'requireSql'){
			value = value.replace(/\n/g, ' ');
			// value = value.replace(/\'/g, '\"');
			console.log( value);
			var data = {};
			data[field] = value;
			$.ajax({
				type: "POST",
				url: '/sql_manager/excutesql',
				data: data,
				dataType: 'html',
				success: function(data){
					// console.log( data);
					$('#result_sql').empty().append( $(data));
					resultDialog.dialog('open');
					$("#excute-new").removeClass('disabled').removeClass('loading');
				},
			});
		}else if(field == 'openLink'  ){
			console.log( value);
			window.open(value);
		}else{
			var form = $('<form id="sql_submit" action="/sql_manager/excutesql" method="post">' +
					'<input type="hidden" name="' + field + '" value=\'' + value + '\' />' +
					'</form>');
			$('body').append(form);
			$(form).submit();
		}
	}
	if( canModified ){
		var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
			lineNumbers: true,
			autoRefresh: true,
			value: 'SELECT',
			mode: "text/x-mysql",
			lineWrapping: true,
		});


		var eMessageDialog = $('#dialog_add_sql').dialog({
			position: 'center',
			autoOpen: false,
			autoHeight: true,
			modal: true,
			width: 1000

		});
		var editDialog = $('#dialog_edit_sql').dialog({
			position: 'center',
			autoOpen: false,
			autoHeight: true,
			modal: true,
			maxHeight: $(window).height() - 40,
			width: 1000

		});
		var resultDialog = $('#result_sql').dialog({
			position: 'center',
			autoOpen: false,
			autoHeight: true,
			modal: true,
			width: 1000,
			maxHeight: $(window).height() - 40,
			title: ''

		});
		function editRequire(require_id) {
			//render popup edit
			$.post("/sql_manager/edit/" + require_id, function (data) {
				$('#dialog_edit_sql').html('');
				$('#dialog_edit_sql').html(data);
				$(".CodeMirror-scroll").trigger('click');
				editDialog.dialog('open');
			});
		}
		$(function () {
			function getResource() {
				//get resource from company select
				var companySelect = $("#companyId").multipleSelect("getSelects");
				$.ajax({
					method: "POST",
					url: "/sql_manager/getresource",
					data: {companySelect: companySelect}
				})
				.done(function (result) {
					$('#resourceId').html(result);
					$('#resourceId').multipleSelect();
				});

			}
			$("#add_request").live('click', function () {
				$('#companyId').multipleSelect({
					onClick: function (view) {
						getResource();
					}

				});
				$('#resourceId').multipleSelect();
				eMessageDialog.dialog('open');
				$(".CodeMirror-scroll").trigger('click');
				return false;
			});

			$(".cancel").live('click', function () {
				eMessageDialog.dialog('close');
				editDialog.dialog('close');
			});

			$("#ok_save").click(function (event) {
				name = $("#SqlManagerRequestName").val();
				desc = $("#SqlManagerDesc").val();
				company = $("#companyId").val();
				employee = $("#resourceId").val();
				$("form#form_create_sql input[type=text]").each(function () {
					$(this).removeClass('form-error');
					if ($(this).val() == "" || $(this).val() == null) {
						$(this).addClass('form-error');
					}
				});
				if (name != "" && desc != "" && company != null && employee != null) {

					$("#form_create_sql").submit();
				} else {
					event.preventDefault();
					return false;
				}

			});
			$("#excute-new").click(function (event) {
				if( $(this).hasClass('disabled') ) return;
				//excute sql in new dialog
				var requireSql = editor.getValue();
				if( !requireSql) return;
				$(this).addClass('disabled loading');
				var request_type = $('#request_type_new').val();
				if( request_type == 'sql' ){
					postExcute('requireSql',requireSql);
				}else if( request_type == 'iframe'){
					postExcute('viewIframeText',requireSql);
				}else if( request_type == 'link'){
					window.open(requireSql);
					$(this).removeClass('disabled loading');
				}
			});
		})
	}
</script>