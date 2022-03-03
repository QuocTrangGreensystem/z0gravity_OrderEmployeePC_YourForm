<?php 
echo $html->css(array(
	'slick_grid/slick.grid',
	'slick_grid/slick.common',
	'slick_grid/slick.edit',
	'preview/tab-admin',
	'layout_admin_2019'
));                         
echo $html->script(array( 
	'slick_grid/lib/jquery.event.drag-2.0.min',
	'slick_grid/slick.core',
	'slick_grid/slick.dataview',
	'slick_grid/slick.formatters',
	'slick_grid/slick.editors',
	'slick_grid/slick.grid',
	'slick_grid_custom',
    'slick_grid/slick.grid.activity',
	'history_filter'
)); ?> 
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
    .slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
    .wd-input-select{
        margin-bottom: 3px;
    }
    .wd-input-select label{
        font-size: 13px;
        font-weight: bold;
        padding-right: 20px;
        margin-top: 12px;
        display: block;
        float: left;
    }
    .wd-input-select select{
        padding: 5px;
        float: left;
        border: 1px solid rgb(179, 179, 179);
    }
    .gr-settings{
        width: 500px;
    }
    .gr-settings .wd-input-select select{
        float: right;
    }
	#project_container{
		margin-top: 20px;
	}
	.action-menu{
		text-align: center;
	}
	.btn.add-field{
		position: absolute;
		width: 50px;
		height: 50px;
		line-height: 50px;
		text-align: center;
		border-radius: 50%;
		z-index: 5;
		top: 0px;
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
	.wd-tab .wd-content {
		width: calc( 100% - 340px);
		float: left;
		overflow: visible;
		position: relative;
	}
	.action-menu-item:hover svg .cls-1{
		fill: #F05352;
	}
</style>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">

        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
                                <h2 class="wd-t3"><?php //echo sprintf(__("Budget Settings management of %s", true), $companyName['Company']['company_name']); ?></h2>
                                <div id="message-place">
                                    <?php
                                    App::import("vendor", "str_utility");
                                    $str_utility = new str_utility();
                                    echo $this->Session->flash();
                                    ?>
                                </div>
								<a href="javascript:void(0);" class="btn add-field" id="add_country" style="margin-right:5px;" title="Add an item" onclick="addNewItem();" ></a>
                                <div class="wd-table" id="project_container" style="width:100%;height:400px;clear: both;">

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
        'name' => '#',
        'width' => 40,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
    ),
    array(
        'id' => 'name',
        'field' => 'name',
        'name' => __('Name', true),
        'width' => 250,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox',
        'validator' => 'DataValidator.isUnique'
    ),
    array(
        'id' => 'description',
        'field' => 'description',
        'name' => __('Description', true),
        'width' => 300,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textArea'
    ) 
    );

$columnsAction = array(
    'id' => 'action.',
    'field' => 'action.',
    'name' => __('Action', true),
    'width' => 70,
    'sortable' => false,
    'resizable' => true,
    'noFilter' => 1,
    'formatter' => 'Slick.Formatters.Action'
);
if (!!$isAdminSas) {
    $columnsSas = array(
        array(
            'id' => 'limit_period',
            'field' => 'limit_period',
            'name' => __('external_limit_period', true),
            'width' => 100,
            'sortable' => true,
            'resizable' => true,
            'editor' => 'Slick.Editors.datePicker'
        ),
        array(
            'id' => 'limit_support',
            'field' => 'limit_support',
            'name' => __('external_limit_support', true),
            'width' => 200,
            'sortable' => true,
            'resizable' => true,
            'editor' => 'Slick.Editors.textArea',
            'validator' => 'DataValidator.isNumeric'
        ),
        array(
            'id' => 'limit_formation',
            'field' => 'limit_formation',
            'name' => __('external_limit_formation', true),
            'width' => 200,
            'sortable' => true,
            'resizable' => true,
            'editor' => 'Slick.Editors.textArea',
            'validator' => 'DataValidator.isNumeric'
        ),
        array(
            'id' => 'limit_coaching',
            'field' => 'limit_coaching',
            'name' => __('external_limit_coaching', true),
            'width' => 200,
            'sortable' => true,
            'resizable' => true,
            'editor' => 'Slick.Editors.textArea',
            'validator' => 'DataValidator.isNumeric'
        )
    );
    $columns =  array_merge($columns,$columnsSas);
}
array_push($columns, $columnsAction);
$i = 1;
$dataView = array();
$selectMaps = array();

App::import("vendor", "str_utility");
$str_utility = new str_utility();


foreach ($externals as $external) {
    $data = array(
        'id' => $external['External']['id'],
        'company_id' => $company_id,
        'no.' => $i++,
        'MetaData' => array()
    );
    $data['name'] = (string) $external['External']['name'];
    $data['description'] = (string) $external['External']['description'];
    // Check dieu kien SAS
    $data['limit_period'] = $str_utility->convertToVNDate($external['External']['limit_period']);
    $data['limit_support'] = (string) $external['External']['limit_support'];
    $data['limit_formation'] = (string) $external['External']['limit_formation'];
    $data['limit_coaching'] = (string) $external['External']['limit_coaching'];
    $data['action.'] = '';

    $dataView[] = $data;
}

$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'The code is avaiable, please enter another!' => __('The code is avaiable, please enter another!', true),
    'Clear' => __('Clear', true)
);
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<script type="text/javascript">
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
	
    var DataValidator = {};
    var loading = "<span id='loadingElm'><img src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt='Loading' /></span>";
    function editMe(field,value)
    {
    	$(loading).insertAfter('#'+field);
    	var data = field+'/'+value;
    	$.ajax({
    		url: '/company_configs/editMe/',
    		data: {
    			data : { value : value, field : field }
    		},
    		type:'POST',
    		success:function(data) {
    			$('#'+field).removeClass('KO');
    			$('#loadingElm').remove();
    		}
    	});
    }
    (function($){
        
        $(function(){
            var $this = SlickGridCustom;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  true;
            // For validate date
            var actionTemplate =  $('#action-template').html();
        
            DataValidator.isUnique = function(value,args){
                var result = true;
                $.each(args.grid.getData().getItems() , function(undefined,dx){
                    if(args.item.id && args.item.id == dx.id){
                        return true;
                    }
                    return (result = (dx.name.toLowerCase() != value.toLowerCase()));
                });
                return {
                    valid : result,
                    message : $this.t('The External has already been exist.')
                };
            }
            DataValidator.isNumeric = function(value, args){
                var _valid = true;
                var _message = '';
                if(args && args.item){
                    var val = parseInt(value);
                    if(val == value){
                        _valid = true;
                    } else {
                        _valid = false;
                        // _message = $this.t('External has already been exist.');
                    }
                }
                return {
                    valid : $.isNumeric(value),
                    message : $.isNumeric(value) ? '' : $this.t('Must be number')
                };
		    }
            $.extend(Slick.Formatters,{
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,
                    dataContext.company_id,dataContext.name), columnDef, dataContext);
                }
            });
//            $.extend(Slick.Editors, {
//                textPrint:  function(args){
//                    $.extend(this, new Slick.Editors.textBox(args));
//                    this.input.attr('maxlength' , 5);
//                }
//            })
        
            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                company_id : {defaulValue : '<?php echo $company_id; ?>', allowEmpty : false},
                name : {defaulValue : '' , allowEmpty : false, maxLength: 100},
                description : {defaulValue : '', maxLength : 255}
            };
            let isAdminSas = <?php echo json_encode($isAdminSas); ?>;
            if  (!!isAdminSas) {
                $this.fields.limit_period = {defaulValue : '' , allowEmpty : true, maxLength: 10};
                $this.fields.limit_support = {defaulValue : '0' , allowEmpty : true, maxLength: 9};
                $this.fields.limit_formation = {defaulValue : '0' , allowEmpty : true, maxLength: 9};
                $this.fields.limit_coaching = {defaulValue : '0' , allowEmpty : true, maxLength: 9};
            }
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            ControlGrid = $this.init($('#project_container'),data,columns);
			addNewItem = function(){
				ControlGrid.gotoCell(data.length, 1, true);
			};
        });
        
    })(jQuery);
</script>