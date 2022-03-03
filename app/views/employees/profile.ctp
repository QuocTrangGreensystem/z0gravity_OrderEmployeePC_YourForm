<?php 
echo $html->script('jquery.validation.min'); 
echo $html->script(array(
	'history_filter',
	'slick_grid/lib/jquery-ui-1.8.16.custom.min',
	'slick_grid/lib/jquery.event.drag-2.0.min',
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
	'slick_grid/slick.pager',
	'slick_grid/slick.common',
	'preview/slickgrid',
	'jquery.ui.custom',
	'slick_grid/slick.edit',
	'preview/tab-admin',
	'layout_admin_2019'
));
?> 
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
    .slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
    .row-number-custom {text-align: right;}
    .gs-custom-cell-euro-header{
        background: url("../../img/front/bg-head-table.png") repeat-x #fff;
    }
    .fist-element{
        border-left: none !important;
    }
    .border-euro-custom span {
      color: #fff;
      float: left;
      font-size: 12px;
      font-weight: bold;
      padding-top: 3px;
      padding-right: 5px;
    }
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
	.slick-header-sortable .slick-sort-indicator{
		background-size: inherit;
	}
	.slick-row .slick-cell .circle-name{
		width: 35px;
		height: 35px;
		line-height: 35px;
		position: relative;
		top: 1px;
	}   
	.slick-headerrow-columns .slick-headerrow-column.ui-state-default .multiselect-filter .multiSelect{
		background-position: 95%;
	}
</style>
<?php
	$employee_info = $this->Session->read("Auth.employee_info");
	$is_sas = $employee_info['Employee']['is_sas'] && empty($employee_info['Company']['id']);
	if((!empty($is_sas) && $is_sas == 1) || ($is_sas == 0 && empty($employee_info['Employee']['company_id']))){
		$isAdminSas = 1;
	}else{
		$isAdminSas = 0;
	}
?>
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
								<?php if($isAdminSas == 1){ ?>
									<div class="wd-input-select">
										<label><?php echo __("Activate profile", true)?></label>
										<?php
											$option = array(__('No', true), __('Yes', true));
											echo $this->Form->input('activate_profile', array(
												'div' => false, 
												'label' => false,
												'onchange' => "editMe('activate_profile', this.value);",
												"class" => "activate_profile",
												"default" => &$companyConfigs['activate_profile'],
												"options" => $option,
												"rel" => "no-history"
												));
										?>
									</div>
								<?php } ?>
								<a href="javascript:void(0);" class="btn add-field" id="add_country" style="margin-right:5px;top: 50px;" title="Add an item" onclick="addNewItem();" ></a>
                                <div class="wd-table wd-table-2019" id="project_container" style="width:100%;">

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
        'name' => '',
        'width' => 40,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
    ),
    array(
        'id' => 'name',
        'field' => 'name',
        'name' => __('Profile Name', true),
        'width' => 250,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox',
        'validator' => 'DataValidator.isUnique'
    )
);
$columns[] = array(
    'id' => 'tjm',
    'field' => 'tjm',
    'name' => __('Average', true),
    'width' => 140,
    'sortable' => true,
    'resizable' => false,
    'editor' => 'Slick.Editors.decimalValue',
    'formatter' => 'Slick.Formatters.decimalValue'
);
$fieldYear = array();
while($lastYear <= $nextYear){
    $yearID = 'year_' . $lastYear;
    $fieldYear[] = $yearID;
    $columns[] = array(
        'id' => $yearID,
        'field' => $yearID,
        'name' => $lastYear,
        'width' => 140,
        'sortable' => true,
        'resizable' => false,
        'editor' => 'Slick.Editors.decimalValue',
        'formatter' => 'Slick.Formatters.decimalValue'
    );
    $lastYear++;
}
$columns[] = array(
    'id' => 'capacity_by_year',
    'field' => 'capacity_by_year',
    'name' => __('Capacity By Year', true),
    'width' => 150,
    'sortable' => true,
    'resizable' => true,
    'editor' => 'Slick.Editors.decimalValue',
    'formatter' => 'Slick.Formatters.decimalValue'
);
$columns[] = array(
    'id' => 'action.',
    'field' => 'action.',
    'name' => __('Action', true),
    'width' => 70,
    'sortable' => false,
    'resizable' => true,
    'noFilter' => 1,
    'formatter' => 'Slick.Formatters.Action'
);
$i = 1;
$dataView = array();
$selectMaps = array();

App::import("vendor", "str_utility");
$str_utility = new str_utility();

if(!empty($profiles)){
    foreach($profiles as $profile){
        $dx = $profile['Profile'];
        $data = array(
            'id' => $dx['id'],
            'company_id' => $company_id,
            'no.' => $i++,
            'MetaData' => array()
        );
        $data['name'] = (string) $dx['name'];
		$data['tjm'] = (string) $dx['tjm'];
        $data['capacity_by_year'] = (string) $dx['capacity_by_year'];
        if(!empty($profileValues[$dx['id']])){
            foreach($profileValues[$dx['id']] as $year => $value){
                $data['year_' . $year] = (string) $value;
            }
        }
        $data['action.'] = '';
        $dataView[] = $data;
    }
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
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete_profile', '%1$s', '%2$s')); ?>">Delete</a>
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
			if ( ('url' in SlickGridCustom ) && SlickGridCustom.url){
				SlickGridCustom.getInstance().resizeCanvas();
			}
			// header custom 
			var _header_custom = $('#wd-header-custom');
			if( _header_custom.length){
				$.each( wdTable.find('.slick-viewport'), function(){
					var _this = $(this);
					_this.height(_this.height - _header_custom.height());
					
				});
			}
			
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
            var fieldYear = <?php echo json_encode($fieldYear);?>;
            //format float number
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
                    message : $this.t('The Profile has already been exist.')
                };
            }
        
            $.extend(Slick.Formatters,{
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,
                    dataContext.company_id,dataContext.name), columnDef, dataContext);
                },
                decimalValue : function(row, cell, value, columnDef, dataContext){
                    if(value && value != 0){
                        value = number_format(value, 2, ',', ' ');
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + '</span> ', columnDef, dataContext);
                    } else {
                        value = (value && value != 0) ? value : '';
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + '</span> ', columnDef, dataContext);
                    }
        		}
            });
            $.extend(Slick.Editors,{
                decimalValue : function(args){
        			$.extend(this, new Slick.Editors.textBox(args));
        			this.input.attr('maxlength' , 10).keypress(function(e){
        				var key = e.keyCode ? e.keyCode : e.which;
        				if(!key || key == 8 || key == 13){
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
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            
            $this.fields = {
                id : {defaulValue : 0},
                company_id : {defaulValue : '<?php echo $company_id; ?>', allowEmpty : false},
                name : {defaulValue : '', allowEmpty : false, maxLength: 150},
                tjm : {defaulValue : ''},
                capacity_by_year: {defaulValue : ''}
            };
            if(fieldYear){
    			$.each(fieldYear, function(ind, val){
    				$this.fields[val] = {defaulValue : ''};
    			});
    		}
            $this.url =  '<?php echo $html->url(array('action' => 'update_profile')); ?>';
            ControlGrid = $this.init($('#project_container'),data,columns);
			addNewItem = function(){
				ControlGrid.gotoCell(data.length, 1, true);
			};
            $('.row-number').parent().addClass('row-number-custom');
            var nameNumber = <?php echo json_encode(__('Number of FTE by year', true));?>;
            header =
                '<div id="wd-header-custom" class="slick-headerrow-columns" style="margin-left: -1px;">'
                    + '<div class="slick-headerrow-column l0 r0 gs-custom-cell-euro-header fist-element border-euro-custom"></div>'
                    + '<div class="slick-headerrow-column l1 r1 gs-custom-cell-euro-header border-euro-custom"></div>'
                    + '<div class="slick-headerrow-column l2 r2 gs-custom-cell-euro-header border-euro-custom"></div>'
                    + '<div class="slick-headerrow-column l3 r3 gs-custom-cell-euro-header border-euro-custom"></div>'
                    + '<div class="slick-headerrow-column l4 r4 gs-custom-cell-euro-header border-euro-custom"></div>'
                    + '<div class="slick-headerrow-column l5 r5 gs-custom-cell-euro-header border-euro-custom"><span>' +nameNumber+ '</span></div>'
                    + '<div class="slick-headerrow-column l6 r6 gs-custom-cell-euro-header border-euro-custom"></div>'
                    + '<div class="slick-headerrow-column l7 r7 gs-custom-cell-euro-header"></div>'
                    + '<div class="slick-headerrow-column l8 r8 gs-custom-cell-euro-header"></div>'
                    + '<div class="slick-headerrow-column l9 r9 gs-custom-cell-euro-header"></div>'
                    + '<div class="slick-headerrow-column l10 r10 gs-custom-cell-euro-header"></div>'
                    + '<div class="slick-headerrow-column l11 r11 gs-custom-cell-euro-header"></div>'
              + '</div>';
            $('.slick-header-columns').before(header);
			set_slick_table_height();
        });
        
    })(jQuery);
</script>