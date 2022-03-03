<?php 
    echo $html->script(array(
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
        'jquery.validation.min',
		'progress/nanobar'
    )); 
	
    echo $html->css(array(
        'jquery.multiSelect',
        'slick_grid/slick.grid',
        'slick_grid/slick.pager',
        'slick_grid/slick.common',
        'slick_grid/slick.edit',
        'jquery.ui.custom',
		'preview/tab-admin',
		'layout_admin_2019'
    ));
    $employee_info = $this->Session->read("Auth.employee_info");
?>
<style type="text/css">
.wd-header th{ line-height:41px; }
.valWorkload{ text-align:right; }
.error{ background:#F00 !important; color:#FFF !important; }
input[type='radio']{ margin-top:3px;}
label{ font-weight:bold; margin-right:10px; margin-top:-3px; }
#table-control{ margin-left:-5px !important; padding-bottom:10px; }
select{ padding:3px 5px}
input[type='button']{ padding:3px 5px; cursor:pointer}
.valueDecimal{ text-align:right}
.wd-bt-big a.wd-hover-advance-tooltip{
	background:url(<?php echo $this->Html->webroot('img/rebuild.jpg'); ?>) 0 2px  !important;
}
.wd-bt-big a.wd-hover-advance-tooltip-archived{
	background:url(<?php echo $this->Html->webroot('img/test-pass-icon.png'); ?>) 0 2px  !important;
}
.wd-bt-big a.wd-hover-advance-tooltip-not-archived{
	background:url(<?php echo $this->Html->webroot('img/test-fail-icon.png'); ?>) 0 2px  !important;
}
#table-control {
    margin: -15px 0 !important;
}
/*.invalid{background: #F00 !important;}*/
.wd-list-project .wd-tab .wd-content label {
	width: 100px;
	margin-top: 10px;
}
.wd-bt-big a.wd-hover-advance-tooltip:hover {
	background: url(/img/rebuild.jpg) 0 2px !important;
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
				$arrMonth = array(
					'01'=>'Jan',
					'02'=>'Feb',
					'03'=>'March',
					'04'=>'Apri',
					'05'=>'May',
					'06'=>'Jun',
					'07'=>'July',
					'08'=>'Aug',
					'09'=>'Sep',
					'10'=>'Oct',
					'11'=>'Nov',
					'12'=>'Dec',
				);
				$URL = $this->Html->url(array('action'=>'rebuilds'));
				$currentYear = date('Y',time());
				$startYear = $currentYear - 5 ;
				$endYear = $currentYear + 6 ;
				//$disable = $done == false ? "disabled='disabled'" : '';
				$disable = '';
				$checkActivity = $checkProject = '';
				if($keyword == 'Activity')
				{
					$checkActivity = 'checked="checked"';
				}
				else
				{
					$checkProject = 'checked="checked"';
				}
                ?>
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                        	<?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
                                <h2 class="wd-t3 paddingTop"><?php __('Archived') ?></h2>
                                <div id="table-control">
                                	<div class="wd-title">
                                	<label><input type="radio" value="Project" <?php echo $checkProject; echo $disable; ?> name="typeChecking" /><?php echo __('Project',true); ?></label>
                                    <label><input type="radio" <?php echo $checkActivity; echo $disable; ?> value="Activity" name="typeChecking" /><?php echo __('Activity',true); ?></label>
                                    <?php /*
                                    <select name="checkingMonth" id="checkingMonth">
                                    <option value="-1"> -- None -- </option>
                                    <?php foreach($arrMonth as $key=>$value)
                                    {
                                        ?>
                                        <option value="<?php echo $key;?>"><?php echo $value;?></option>  
                                        <?php
                                    }
                                    ?>
                                    </select>
                                    <select name="checkingYear" id="checkingYear">
                                    <option value="-1"> -- None -- </option>
                                    <?php for( $i = $startYear; $i < $endYear; $i++ )
                                    {
                                        $selected = '';
                                        //if($currentYear == $i)	$selected = "selected";
                                        ?>
                                        <option <?php echo $selected; ?> value="<?php echo $i;?>"><?php echo $i;?></option>  
                                        <?php
                                    }
                                    ?>
                                    </select>
									*/
									?>
                                    
                                    <input type="button" value="Checking" id="btnCheck"  onclick=" resetViewData(); checkingAchirved();" />
                                    <?php
									/*
                                    <a class="wd-add-project" id="setAll" style="float:right" href="javascript:;" onclick="getAllRowNeedArchived();" ><span><?php echo __('Archived all'); ?></span></a>
									*/
									?>
                                    <a class="wd-add-project" id="setAll" style="float:right" href="javascript:;" onclick="getRowChecked();" ><span><?php echo __('Archive'); ?></span></a>
                                    </div>
                                    <?php echo $this->Session->flash(); ?>
                                </div>
                                
                                <div class="wd-table" id="project_container" style="width:100%;height:550px;">

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
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
        	<input type="checkbox" value="<?php echo '%2$s'; ?>" class="archived-me checkAll" id="archived-me-<?php echo '%2$s'; ?>" onchange="setArchivedForMe('<?php echo '%2$s'; ?>')"  />
        </div>
    </div>
</div>

<div id="archived-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
             <a onclick="archivedMe('<?php echo '%1$s'; ?>','<?php echo '%2$s'; ?>');" id="row_<?php echo '%2$s'; ?>"  class="wd-hover-advance-tooltip" href="javascript:;">Rebuild</a>
        </div>
    </div>
</div>

<div id="not-archived-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a id="row_archived_<?php echo '%2$s'; ?>"  class="wd-hover-advance-tooltip-not-archived" href="javascript:;">test-pass-icon</a>
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
        'width' => 60,
        'sortable' => true,
        'resizable' => false,
        'noFilter' => 1,
    ),
    array(
        'id' => 'name',
        'field' => 'name',
        'name' => __('Name', true),
        'width' => 500,
        'sortable' => true,
        'resizable' => true
    ),
    array(
        'id' => 'start_date',
        'field' => 'start_date',
        'name' => __('Start Date', true),
        'width' => 120,
		'datatype' => 'datetime',
        'sortable' => true,
		//'formatter' => 'Slick.Formatters.valueDecimal',
        'resizable' => true
    ),
    array(
        'id' => 'end_date',
        'field' => 'end_date',
        'name' => __('End Date', true),
        'width' => 120,
		'datatype' => 'datetime',
        'sortable' => true,
		//'formatter' => 'Slick.Formatters.valueDecimal',
        'resizable' => true
    ),
    /*array(
        'id' => 'status',
        'field' => 'status',
        'name' => __('Status', true),
        'width' => 270,
        'sortable' => true,
		'formatter' => 'Slick.Formatters.valueDecimal',
        'resizable' => true
    ),*/
    array(
        'id' => 'archivedMe',
        'field' => 'archivedMe',
        'name' => __('Archived', true),
        'width' => 90,
        'sortable' => true,
        //'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.ActionMe'
        ),
	array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => '<input type="checkbox" id="checkAll" />',
        'width' => 40,
        'sortable' => false,
        //'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
        ));
$i = 1;
$dataView = array();
$selectMaps = array();

App::import("vendor", "str_utility");
$str_utility = new str_utility();
$dataView = $results;
$i18n = array();
?>
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
var loading = "<span id='loadingElm'><img src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt='Loading' /></span>";
var keyword = '<?php echo $keyword; ?>';
var DataValidator = {},ControlGrid,IuploadComplete = function(json){ 
    var data = ControlGrid.eval('currentEditor');
    data.onComplete(json);
};
function archivedMe(key,id){
	$(loading).insertBefore('#row_'+id);
	$.ajax({
		url  : "/staffing_systems/archivedMe/"+key+"/"+id,
		type : "POST",
		data : {},
		success : function(data){
			$('#loadingElm').remove();
			if(data == 1)
			$('#row_'+id).remove();
		}
	});
}
var archivedChecked = [];
function setArchivedForMe(id){
	//var $this = $('#archived-me-'+id);
}
function getRowChecked()
{
	$('.archived-me:checked').each(function() {
		archivedChecked.push($(this).val());
	});
	setArchivedForItems(archivedChecked);
}
function setArchivedForItems(items)
{
	var r = confirm("Are you sure?");
	if (r == true) {
		
	} else {
		return;
	}
	$('#flashMessage').html(loading + ' Progress...');
	$.ajax({
		url  : "/staffing_systems/archivedRecords/"+keyword,
		type : "POST",
		data : {
			data:{'data' : items}
		},
		success : function(data){
			if(data){
                window.location.href = "/staffing_systems/archived/";
			} else {
				$('#flashMessage').removeClass('success');
				$('#flashMessage').addClass('warning');
			}
		}
	}); 
}
(function($){
    $(function(){
        var $this = SlickGridCustom;
        $this.i18n = <?php echo json_encode($i18n); ?>;
        $this.canModified =  true;
        var actionTemplate =  $('#action-template').html();
		var archivedTemplate =  $('#archived-template').html();
        $.extend(Slick.Formatters,{
            Action : function(row, cell, value, columnDef, dataContext){
				//invalid
				
				if(dataContext.class == 'archived')
				{	
					return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,keyword,dataContext.id), columnDef, dataContext);
				}
            },
			 ActionMe : function(row, cell, value, columnDef, dataContext){
				if(dataContext.class == 'archived')
				{	
					return Slick.Formatters.HTMLData(row, cell,$this.t(archivedTemplate,keyword,dataContext.id), columnDef, dataContext);
				}
            },
			valueDecimal : function(row, cell, value, columnDef, dataContext){
				return '<div class="valueDecimal ">'+value+'</div>'
			}
        });
        var data = <?php echo json_encode($dataView); ?>;
        var columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
        $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
        $this.fields = {
            id : {defaulValue : 0}
        };
        $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
        ControlGrid = $this.init($('#project_container'),data,columns);
        ControlGrid.onSort.subscribe(function(e, args) {
           setTimeout(function(){
                if($('#checkAll').is(':checked')){
                    $('.checkAll').prop('checked' , true);
                } else {
                    $('.checkAll').prop('checked' , false);
                }
            }, 1000);
        });
        ControlGrid.onScroll.subscribe(function(args, e, scope){
            setTimeout(function(){
                if($('#checkAll').is(':checked')){
                    $('.checkAll').prop('checked' , true);
                } else {
                    $('.checkAll').prop('checked' , false);
                }
            }, 1000);
        });
		$('.wd-bt-big').parent().parent().parent().addClass('invalid');
		/*$this.onCellChange = function(args){
			console.log(args);
                $('.row-center').parent().addClass('row-center-custom');
            };*/
		resetViewData = function(){
			ControlGrid.getData().setItems([]);
		};
		getAllRowNeedArchived = function(){
			var $data = ControlGrid.getData().getItems();
			$archived = [];
			jQuery.each($data, function(ind, val){
				if(val.class == 'archived')
				{
					$archived.push(val.id);
				}
			});
			setArchivedForItems($archived);
		};
        checkingAchirved = function(){
			var type = $('input:radio[name = "typeChecking"]:checked').val();
			var month = $('#checkingMonth').val();
			var year = $('#checkingYear').val();
			var data = '';
			$('#flashMessage').html(loading + ' Progress...');
			$.ajax({
				url  : "/staffing_systems/archived/ajax/",
				type : "POST",
				data : {
					data:{'type' : type,'month' : month, 'year' : year}
				},
				success : function(data){
					data = JSON.parse(data);
					results = data.results;
					keywordTmp = keyword;
					keyword = data.keyword;
					if(results)
					{
						ControlGrid.getData().setItems([]);
						var newData = new Array();
						jQuery.each(results, function(ind, val){
							var _newData = {
									'action.': val.class,
									'id': val.id,
									'name': val.name,
									'no.': val.id,
									'class': val.class,
									'start_date': val.start_date,
									'end_date': val.end_date,
								};
								//newData.push(_newData);
								ControlGrid.getData().addItem(_newData);
						});
						$('#flashMessage').html('Done!');
						$('#flashMessage').removeClass('warning');
						$('#flashMessage').addClass('success');
						$('#flashMessage').show();
					}
				}
			}); 
        };
        $('#checkAll').click(function(){
           if($(this).is(':checked')){
                $('.checkAll').prop('checked' , true);
           } else {
                $('.checkAll').prop('checked' , false);
           }
        });
    }); 
})(jQuery);
</script>
