<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->script('history_filter'); ?>
<?php echo $html->css('slick_grid/slick.grid'); ?>
<?php echo $html->css('slick_grid/slick.pager'); ?>
<?php echo $html->css('slick_grid/slick.common'); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>

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
	.select-display {
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
	.unsortable .index {
		cursor: default;
	}
	.row-hidden td {
		background: #ffc;
	}
	.wd-list-project .wd-tab .wd-content label {
		width: auto;
	}
	.wd-panel .wd-section{
		display: block;
		width: 100%;
	}
	.wd-tab .wd-content{
		width: calc( 100% - 340px);
		float: left;
		overflow: visible;
		position: relative;
		padding-top: 20px;
	}
	.wd-list-project .wd-tab .wd-panel{
		padding: 20px;
	}
	.slick-viewport .slick-row .slick-cell.grid-action{
		padding: 0;
		border-top:0;
	}
	.grid-action .wd-actions{
		margin: 0;;
	}
	.grid-action .wd-actions .wd-btn{
		width: 40px;
		height: 40px;
		float:left;
	}
	/* width */
	body ::-webkit-scrollbar {
		width: 4px;
		height: 4px;
	}

	/* Track */
	body ::-webkit-scrollbar-track {
		box-shadow: inset 0 0 5px #F2F5F7; 
		border-radius: 5px;
		background-color: #fff;
	}

	/* Handle */
	body ::-webkit-scrollbar-thumb {
		background: #C6CCCF;; 
		border-radius: 5px;
	}
	.btn.add-field{
		position: absolute;
		width: 50px;
		height: 50px;
		line-height: 50px;
		text-align: center;
		border-radius: 50%;
		z-index: 5;
		top: 87px;
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
                                <div class="gr-settings">
                                    <div class="wd-input-select">
                                        <label><?php echo __('Activate budget management by team/profit center', true)?></label>
                                        <?php
    										$option = array(__('No', true), __('Yes', true));
                                            echo $this->Form->input('budget_team', array(
                                                'div' => false, 
                                                'label' => false,
    											'onchange' => "editMe('budget_team', this.value);",
                                                "class" => "budget_team",
                                                "default" => &$companyConfigs['budget_team'],
                                                "options" => $option,
                                                "rel" => "no-history"
                                                ));
                                        ?>
                                    </div><br /><br />
                                    <div class="wd-input-select">
                                        <label><?php echo __('Every project manager see the budget', true)?></label>
                                        <?php
    										$option = array(__('No', true), __('Yes', true));
                                            echo $this->Form->input('EPM_see_the_budget', array(
                                                'div' => false, 
                                                'label' => false,
    											'onchange' => "editMe('EPM_see_the_budget', this.value);",
                                                "class" => "EPM_see_the_budget",
                                                "default" => &$companyConfigs['EPM_see_the_budget'],
                                                "options" => $option,
                                                "rel" => "no-history"
                                                ));
                                        ?>
                                    </div>
                                </div>
								<a href="javascript:void(0);" class="btn add-field" id="add_item" style="margin-right:5px;" title="Add an item" onclick="addNewItem();"></a>
                                <div class="wd-table" id="project_container" style="width:100%;height:400px;clear: both;top:5px;">

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $this->element('dialog_projects') ?>
<?php
echo $this->Html->script(array(
    'history_filter',
    'jquery.multiSelect',
    'responsive_table',
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
    'jquery.ui.touch-punch.min'
));
?>
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
        'name' => __('Currency', true),
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
    ),
    array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __('Action', true),
        'width' => 120,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
        ));
$i = 1;
$dataView = array();
$selectMaps = array();

App::import("vendor", "str_utility");
$str_utility = new str_utility();


foreach ($budgetSettings as $budgetSetting) {
    $data = array(
        'id' => $budgetSetting['BudgetSetting']['id'],
        'company_id' => $company_id,
        'no.' => $i++,
        'MetaData' => array()
    );
    $data['name'] = (string) $budgetSetting['BudgetSetting']['name'];
    $data['description'] = (string) $budgetSetting['BudgetSetting']['description'];

    $data['action.'] = $budgetSetting['BudgetSetting']['currency_budget'];

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
            <a class="wd-update" href="<?php echo $this->Html->url(array('action' => 'update_default', '%1$s')); ?>">Select</a>
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<div id="default-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a class="wd-update wd-update-default">Select</a>
        </div>
    </div>
</div>
<script type="text/javascript">
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
            var defaultTemplate =  $('#default-template').html();
        
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
                    message : $this.t('The Currency has already been exist.')
                };
            }
        
            $.extend(Slick.Formatters,{
                Action : function(row, cell, value, columnDef, dataContext){
                    var _html = '';
                    if(value == 1){
                        // var _url = '<?php echo $this->Html->url('/') ?>budget_settings/update_default/' + dataContext.id;
                        // _html = '<div id="action-template" style="display: none;">';
                        // _html +=     '<div style="margin: 0 auto !important; width: 54px;">';
                        // _html +=        '<div class="wd-bt-big">';
                        // _html +=            '<a class="wd-update wd-update-default" href="'+ _url +'">Select</a>';
                        // _html +=        '</div>';
                        // _html +=    '</div>';
                        // _html += '</div>';
                        _html = $this.t(defaultTemplate,dataContext.id, dataContext.company_id,dataContext.name);
                    } else {
                        _html = $this.t(actionTemplate,dataContext.id, dataContext.company_id,dataContext.name);
                    }
                    return Slick.Formatters.HTMLData(row, cell, _html, columnDef, dataContext);
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
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            ControlGrid = $this.init($('#project_container'),data,columns);
			addNewItem = function(){
				ControlGrid.gotoCell(data.length, 1, true);
			};
        });        
    })(jQuery);
</script>