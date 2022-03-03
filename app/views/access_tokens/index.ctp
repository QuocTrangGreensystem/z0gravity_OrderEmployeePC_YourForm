<?php
echo $html->script(array(
	'history_filter',
	'slick_grid/lib/jquery.event.drag-2.0.min',
	'slick_grid/slick.core',
	'slick_grid/slick.dataview',
	'slick_grid/slick.formatters',
	'slick_grid/plugins/slick.cellrangedecorator',
	'slick_grid/plugins/slick.cellrangeselector',
	'slick_grid/plugins/slick.cellselectionmodel',
	'slick_grid/slick.editors',
	'slick_grid/slick.grid.origin',
	'slick_grid_custom',	
));

echo $html->css(array(
	'jquery.ui.custom',
	'jquery.multiSelect',
	'slick_grid/slick.grid',
	'slick_grid/slick.edit',
	'slick_grid/slick.common',
	'preview/slickgrid',
	'layout',
	'layout_2019',
	'preview/layout',
	'preview/tab-admin',
	'layout_admin_2019',	
));
?>
<style type="text/css">
	.wd-panel .wd-section{
		display: block;
		width: 100%;
	}
	.wd-tab .wd-content{
		width: calc( 100% - 340px);
		float: left;
		overflow: visible;
	}
	.slick-cell a.circle-name{
		height: 30px;
		line-height: 30px;
		width: 30px;
		display: inline-block;
		text-align: center;
		top: 0;
		color: #fff;
		border-radius: 50%;
		top: inherit;
	}
	.slick-header .slick-header-column{
		text-align: left;
	}
	.wd-content {
		margin-top: 30px;
	}
	.actions {
		margin-bottom: 20px;
	}
	.actions a{
		display: inline-block;
		padding-left: 20px;
		padding-right: 20px;
		height: 30px;
		line-height: 30px;
		border: 1px solid #E1E6E8;
	}
	.actions a:hover{
		text-decoration: none;
		color: #fff;
		background-color: #217FC2;
		border-color: #217FC2;
	}
	.duplicate{
		display: inline-block;
		cursor: pointer;
	}
	.duplicate i{
		font-size: 16px;
	}
	.content-duplicate a{
		margin-left: 20px;
	}
	.content-duplicate input{
		width: 85%;
		border: none;
		
	}
	.grid-canvas .slick-cell.active{
		padding: 0;
		height: 39px;
		border-bottom: 1px solid #F2F5F7;
	}
	svg .grid-icon-1 {
		fill: #666;
		fill-rule: evenodd;
	}
	.grid-canvas .slick-row.loading{
		padding-left: 0;
	}
	.wd-table-container #project_container{
		width: 100%;
		float: none;
	}
	.wd-content .wd-table-container{
		position: relative;
		border: none;
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
	.content-duplicate .icon-docs{
		opacity: 0;
		transition: 0.2s ease;
	}
	.content-duplicate:hover .icon-docs{
		opacity: 1;
	}
</style>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                </div>
                <?php
                App::import("vendor", "str_utility");
                $str_utility = new str_utility();
                ?>
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section clearfix" id="wd-fragment-1">
							<?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
								<div id="message-place">
									<?php
									echo $this->Session->flash();
									?>
								</div>
								<div id="access_token_display">
								</div>
								<div class="wd-table-container">
									<a href="javascript:void(0);" class="btn add-field" id="add_accesstoken" style="margin-right:5px;" title="<?php echo __('Add an item', true);?>" onclick="addNewItem();" ></a>
									<div class="wd-table wd-table-2019" id="project_container">
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
<div id="action-template" style="display: none;">
    <div class="action-menu">
        <a onclick="return deleteToken(%1$s);" class="action-menu-item" href="<?php echo $this->Html->url(array('action' => 'deleteToken', '%2$s')); ?>">
		<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 20.03 20">
		  <path class="grid-icon-1" d="M6644.04,275a0.933,0.933,0,0,1-.67-0.279l-8.38-8.374-8.38,8.374a0.954,0.954,0,1,1-1.35-1.347l8.38-8.374-8.38-8.374a0.954,0.954,0,0,1,1.35-1.347l8.38,8.374,8.38-8.374a0.933,0.933,0,0,1,.67-0.279,0.953,0.953,0,0,1,.67,1.626L6636.33,265l8.38,8.374A0.953,0.953,0,0,1,6644.04,275Z" transform="translate(-6624.97 -255)"/>
		</svg>
		</a>
        
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
        'id' => 'no.',
        'field' => 'no.',
        'name' => '',
        'width' => 40,
        'sortable' => false,
        'resizable' => false,
        'cssClass' => 'text-center',
    ),
    array(
        'id' => 'company_id',
        'field' => 'company_id',
        'name' => __('Company', true),
        'width' => !empty($filter_render['company_id.Resize']) ? $filter_render['company_id.Resize'] : 220,
		'minWidth' => 120,
        'sortable' => true,
        'resizable' => true,
		'editor' => 'Slick.Editors.singleSelectBox',
    ),
    array(
        'id' => 'token',
        'field' => 'token',
        'name' => __('Token', true),
        'width' => !empty($filter_render['token.Resize']) ? $filter_render['token.Resize'] : 350,
		'minWidth' => 300,
        'sortable' => true,
        'resizable' => true,
		'formatter' => 'Slick.Formatters.codeCopy'
    ),
    array(
        'id' => 'updated',
        'field' => 'updated',
        'name' => __('Updated', true),
        'width' => !empty($filter_render['updated.Resize']) ? $filter_render['updated.Resize'] : 200,
		'minWidth' => 120,
        'sortable' => true,
        'resizable' => true,
        'datatype' => 'datetime',
        'formatter' => 'Slick.Formatters.DateTime',
    ),
    array(
        'id' => 'expires',
        'field' => 'expires',
        'name' => __('Expires', true),
        'width' => !empty($filter_render['expires.Resize']) ? $filter_render['expires.Resize'] : 208,
		'minWidth' => 120,
        'sortable' => true,
        'resizable' => true,
        'datatype' => 'datetime',
        'editor' => 'Slick.Editors.datePicker',
        'formatter' => 'Slick.Formatters.DateTime',
    ),
	'action.' => array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => '&nbsp;',
        'width' => 40,
        'minWidth' => 50,
        'maxWidth' => 80,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
    )
);
$i = 1;
$dataView = array();
$listCompanies[''] = __('All company', true);
$selectMaps = array(
	'company_id' => $listCompanies
);
foreach ($tokens as $key => $token) {
	$data = $token['AccessToken'];
    $data['id'] = $key;
    $data['no.'] = $key+1;
	$data['MetaData'] = array(
		'cssClasses' => '',
	);
	$dateFields = array('created', 'updated', 'expires');
	$date = '';
	foreach( $dateFields as $fields){
		if( !empty($data[$fields]) && ($data[$fields] != '0000-00-00 00:00:00')){
			$date = new DateTime($data[$fields]);
			$data[$fields] = $date->format('d-m-Y');
		}else{
			$data[$fields] = '';
		}
			
	}
    $dataView[] = $data;
}
$i18n = array(
	'Delete?' => __('Delete?', true),
	'Delete' => __('Delete', true),
	'Cancel' => __('Cancel', true),
);

?>
<script type="text/javascript">
HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
var timeoutID;
var grid_options ={
	enableAddRow: false,            
	showHeaderRow: true,
	rowHeight: 40,
	topPanelHeight: 40,
	headerRowHeight: 40
};
var ControlGrid;
var newTokenURI = <?php echo json_encode( $this->Html->url(array('action' => 'createNewToken')));?>;
var delTokenURI = <?php echo json_encode( $this->Html->url(array('action' => 'deleteToken')));?>;
var wdTable = $('#project_container');
function update_table_height(){
	var heightTable = $(window).height() - wdTable.offset().top - 40;
	wdTable.height(heightTable);
	if( SlickGridCustom.getInstance() ) SlickGridCustom.getInstance().resizeCanvas();
}
$(window).resize(function(){
	update_table_height();
});
update_table_height();
(function($){
	$(function(){
		var $this = SlickGridCustom;
        $this.i18n = <?php echo json_encode($i18n); ?>;
        $this.canModified =  true;
		$this.url = <?php echo json_encode($html->url(array('action' => 'update'))); ?>;
		var actionTemplate =  $('#action-template').html();
		var DataValidation = {};
		$.extend(Slick.Formatters,{
			codeCopy : function(row, cell, value, columnDef, dataContext){
				duplicate = '<div class="content-duplicate"><span type="text">'+ (value ? value : '' )+ '</span><a href="javascript:void(0);" class="duplicate" onclick="copyCode.call(this);"><i class="icon-docs"></i></a></div>';
				return duplicate;
			},
			Action : function(row, cell, value, columnDef, dataContext){
				return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id, dataContext.token));
			},
			DateTime : function(row, cell, value, columnDef, dataContext){
				return '<div class="cell-data"><span style="text-align: right">' + (value ? value : '') + '</span></div>';
			}
		});
		
        var data = <?php echo json_encode($dataView); ?>;
        var columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
        $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
        $this.fields = {
            id : {defaulValue : ''},
            token : {defaulValue : '', allowEmpty: true},
            company_id : {defaulValue : '', allowEmpty: true},
            expires : {defaulValue : ''},
        };		
        ControlGrid = $this.init($('#project_container'),data,columns, grid_options);
		update_table_height();
	});
})(jQuery);
$(document).ready(function(){
	clearTimeout(timeoutID);
	timeoutID = setTimeout(function(){
		$('#message-place').slideUp(500);
	}, 5000);
});
function getNewKeyforAdd(key){
	var items = ControlGrid.getData().getItems();
	var max_k = 0;
	if( items.length){
		$.each(items, function(i,v){
			max_k = Math.max( max_k, parseInt(v[key]));
		});
	}
	return ++max_k;
}
function addNewItem(){
	var bt = $('#add_accesstoken');
	if( bt.hasClass('disabled')) return;
	bt.addClass('disabled');
	var new_id = getNewKeyforAdd('id');
	var new_item = {
		id: new_id,
		'no.': getNewKeyforAdd('no.'),
		MetaData: {cssClasses: 'loading'}
	};
	var dataView = ControlGrid.getData();
	dataView.addItem(new_item);
	ControlGrid.invalidate();
	var row =  ControlGrid.getData().getRowById(new_id);
	var col = ControlGrid.getColumnIndex('token');
	ControlGrid.gotoCell(row, 0, false);
	$.ajax({
		url: newTokenURI,
		type: 'get',
		dataType: 'json',
		success: function(res){
			if( res.result === true){
				$.extend( new_item, res.data);
				new_item.id = new_id;
				new_item.MetaData.cssClasses = 'success';
				dataView.updateItem(new_id, new_item);
				dataView.refresh();
				$('#message-place').html(res.message).show();
				SlickGridCustom.onAfterSave(true, {
					row: row,
					cell: col,
					column: ControlGrid.getColumns()[col],
					grid: ControlGrid,
					item: new_item
				});
				setTimeout(function(){
					$('#message-place .message').fadeOut('slow');
				} , 5000);
			}else{
				dataView.deleteItem(new_id);
				
			}
		},
		error: function(){
			dataView.deleteItem(new_id);
		},
		complete: function(){
			$('#add_accesstoken').removeClass('disabled');
			ControlGrid.invalidate();
		}
	});
};
function deleteToken(id){
	console.log( arguments);
	var dataView = ControlGrid.getData();
	var it = dataView.getItemById(id);
	var row = dataView.getRowById(id);
	var token = it.token;
	var opt = {
		title: SlickGridCustom.t('Delete?'),
		content: token,
		buttonText: [SlickGridCustom.t('Delete'), SlickGridCustom.t('Cancel')],
	};
	var confirm = wdConfirmIt( opt, function(){
		$.ajax({
			url: delTokenURI + '/' + token,
			type: 'get',
			dataType: 'json',
			beforeSend: function(res){
				it.MetaData.cssClasses = 'loading';
				ControlGrid.invalidate();
				ControlGrid.render();
			},
			success: function(res){
				if( res.result === true){
					dataView.deleteItem(id);
					dataView.refresh();
				}else{
					it.MetaData.cssClasses = 'error';
				}
				ControlGrid.invalidate();
				ControlGrid.render();
				$('#message-place').html(res.message).show();
				setTimeout(function(){
					$('#message-place .message').fadeOut('slow');
				} , 5000);
			},
			error: function(){
				it.MetaData.cssClasses = 'error';
			},
			complete: function(){
				$('#add_accesstoken').removeClass('disabled');
				ControlGrid.invalidate();
				ControlGrid.render();
			}
		});
	});
	return false;
}
function copyCode() {
	var $temp = $("<input>");
	$("body").append($temp);
	$temp.val($(this).closest('.content-duplicate').find('span').text()).select();
	document.execCommand("copy");
	$temp.remove();
	var  $mess = '<div id="flashMessage" class="message success">' + '<?php __('Copied');?>' + '<a href="#" class="close">x</a></div>';
	$('#message-place').html($mess)
	if( $('#message-place').is(':hidden')) $('#message-place').slideDown(500);
	clearTimeout(timeoutID);
	timeoutID = setTimeout(function(){
		$('#message-place').slideUp(500);
	}, 5000);
}
</script>