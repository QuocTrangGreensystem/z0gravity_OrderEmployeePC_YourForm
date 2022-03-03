<!-- =============================================== CHEN CSS + JS ================================================================ -->
<?php
	echo $this->Html->css(array(
		'projects',
		//'activity_request',
		'slick_grid/slick.pager',
		'slick_grid/slick.common',
		'slick_grid/slick.edit'
	));
	echo $this->Html->script(array(
        'slick_grid/lib/jquery-ui-1.8.16.custom.min',
		'jquery.mousewheel.min',
		'slick_grid/slick.core',
		'slick_grid/slick.dataview',
		'slick_grid/controls/slick.pager',
		'slick_grid/slick.formatters',
		'slick_grid/plugins/slick.cellrangedecorator',
		'slick_grid/plugins/slick.cellrangeselector',
		'slick_grid/plugins/slick.cellselectionmodel',
		'slick_grid/slick.editors',
		'slick_grid_custom'
	));
	echo $this->Html->css(array(
		'slick_grid/slick.grid.activity'
	));
	echo $this->Html->script(array(
		'slick_grid/lib/jquery.event.drag-2.2',
		'slick_grid/slick.grid.activity'
	));
    App::import("vendor", "str_utility");
    $str_utility = new str_utility();
?>
<style>
    .slick-headerrow-column.ui-state-default{padding: 0 1em;}
    .r3, .l3{
        text-align: center;
    }
    .r4, .l4{
        background-color: yellow;
    }
    .row-number {
        float: right;
    }
</style>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"><div class="wd-panel">
            <div class="wd-list-project">
                <div class="wd-title">
                    <!--a href="<?php //echo $this->Html->url(array('action' => 'update', 'customer', $company_id));?>" id="add-activity" class="wd-add-project" style="margin-right:5px;"><span></span></a-->
                    <table id="table-control" class="wd-title" style="float: left; width: auto; margin-right: 5px">
    					<tr>
    						<td>
    							<fieldset>
                                <?php if($roles == 'admin'){?>
                                <?php echo $this->Form->select('profit', $paths, $profiltId, array('empty' => $companyName, 'name' => 'profit', 'escape' => false, 'style' => 'padding: 6px')); ?>
                                <?php } else {?>
                                <?php echo $this->Form->select('profit', $paths, $profiltId, array('empty' => false, 'name' => 'profit', 'escape' => false, 'style' => 'padding: 6px')); ?>
                                <?php }?>
    							<a href="<?php echo $this->here ?>?profit=<?php echo $profiltId;?>&amp;year=<?php echo $year-1;?>" id="absence-prev">Prev</a>
    							<span class="currentWeek"><?php echo $year ?></span>
    							<a href="<?php echo $this->here ?>?profit=<?php echo $profiltId;?>&amp;year=<?php echo $year+1;?>" id="absence-next">Next</a>
                                <input type="hidden" name="year" value="<?php echo $year;?>" />
    							</fieldset>
    						</td>
    					</tr>
    				</table>
                    <a href="javascript:void(0);" id="export-submit" class="export-excel-icon-all" style="margin-right:5px;" title="<?php __('Export Excel')?>"><span><?php __('Export') ?></span></a>
                </div>
                <div id="message-place">
                    <?php
                    echo $this->Session->flash();
                    ?>
                </div>
                <div class="wd-table" id="project_container" style="width:100%;">

                </div>
                <div id="pager" style="width:100%;height:36px; overflow: hidden;">

                </div>
                <?php //echo $this->element('grid_status'); ?>
            </div></div></div>
        </div>
    </div>
</div>
<fieldset style="display: none;">
    <?php
    $is_ad = $tmpProfit ? 'yes' : 'no';
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'activity_forecasts', 'action' => 'export_budget', $year, $company_id, $profiltId, $manager, $is_ad, $managerBackup)));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<?php echo $html->script('responsive_table.js'); ?>
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
        'id' => 'family_id',
        'field' => 'family_id',
        'name' => __('Family', true),
        'width' => 180,
        'sortable' => true,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.testVal'
    ),
    array(
        'id' => 'subfamily_id',
        'field' => 'subfamily_id',
        'name' => __('Sub Family', true),
        'width' => 180,
        'sortable' => true,
        'resizable' => true
    ),
    array(
        'id' => 'type',
        'field' => 'type',
        'name' => __('Type', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true
    ),
    array(
        'id' => 'year',
        'field' => 'year',
        'name' => __($year, true),
        'width' => 100,
        'sortable' => false,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.numericValue',
		'noFilter' => 1
    )
);
$firstYear = strtotime('01-01-'.$year);
$lastYear = strtotime('31-12-'.$year);
$countMonth = 1;
$monthLists = array();
while($firstYear <= $lastYear){
    $columns[] = array(
        'id' => $firstYear,
        'field' => $firstYear,
        'name' => __(date('M', $firstYear), true) . '-' . __(date('y', $firstYear), true) . ' (P' . $countMonth . ')',
        'width' => 100,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.numericValue',
        'formatter' => 'Slick.Formatters.numericValue',
		'noFilter' => 1
    );
    $monthLists[$firstYear] = $firstYear;
    $firstYear = mktime(0, 0, 0, date("m", $firstYear)+1, date("d", $firstYear), date("Y", $firstYear));
    $countMonth++;
}
$md = !empty($employee_info['Company']['unit']) ? $employee_info['Company']['unit'] : 'M.D';
$groupTypes = array('euro', 'md', 'fte', 'ec_euro');
$formats = array('euro' => $budget_settings, 'md' => __($md, true), 'fte' => __('FTE', true), 'ec_euro' => __('External Cost', true) . $budget_settings);
$i = 1;
$dataView = array();
$families = array('0' => '--') + $families;

$selectMaps = array(
    'family_id' => $families,
    'subfamily_id' => $families,
    'type' => $formats
);
if(!empty($groupFamilyAdmins)){
    foreach($groupFamilyAdmins as $groupFamilyAdmin){
        $dx = $groupFamilyAdmin['ActivityFamily'];
        $id = $dx['id'];
        $dataBudgets = $budgetFamilies;
        if(!empty($dx['parent_id'])){
            $dataBudgets = $budgetSubFamilies;
        }
        $tmpId = $id;
        foreach($groupTypes as $type){
            $data = array(
                'id' => $id,
                'no.' => $i++,
                'MetaData' => array()
            );
            $data['company_id'] = $company_id;
            $data['profit_id'] = ($tmpProfit == true) ? 0 : $profiltId;
            $data['manager_id'] = ($tmpProfit == true) ? 0 : $manager;
            if(!empty($dx['parent_id'])){
                $data['family_id'] = !empty($dx['parent_id']) ? $dx['parent_id'] : '';
                $data['subfamily_id'] = !empty($dx['id']) ? $dx['id'] : 0;
            } else {
                $data['family_id'] = !empty($dx['id']) ? $dx['id'] : '';
                $data['subfamily_id'] = !empty($dx['parent_id']) ? $dx['parent_id'] : 0;
            }
            $data['id'] = $data['family_id'] . '-' . $data['subfamily_id'] . '-' . $type;
            $data['type'] = !empty($type) ? $type : '';
            $data['is_admin'] = ($tmpProfit == true) ? 1 : 0;
            if(isset($dataBudgets[$tmpId][$type]) && !empty($dataBudgets[$tmpId][$type])){
                $sumBudget = 0;
                foreach($dataBudgets[$tmpId][$type] as $date => $value){
                    $data[$date] = $value;
                    $sumBudget += $value;
                }
                $data['year'] = $sumBudget;
            }
            $data['action.'] = '';
            $dataView[] = $data;
        }
    }
}
$i18n = array(
    'The Activity has already been exist.' => __('The Activity has already been exist.', true),
    'The date must be smaller than or equal to %s' => __('The date must be smaller than or equal to %s', true),
    'The date must be greater than or equal to %s' => __('The date must be greater than or equal to %s', true),
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true)
);
?>
<div id="action-template" style="display: none;">
    <a class="wd-edit" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'update', 'customer', '%1$s', '%2$s')); ?>">Edit</a>
    <div class="wd-bt-big">
        <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
    </div>
</div>
<script type="text/javascript">
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
    (function($){
        $(function(){
            var $this = SlickGridCustom,
            monthLists = <?php echo json_encode($monthLists);?>,
            //company_id = <?php //echo json_encode($company_id);?>,
            urlUpdateCustomer = <?php echo json_encode(urldecode($this->Html->link('%3$s', array('controller' => 'sale_customers', 'action' => 'update', 'customer', '%1$s', '%2$s')))); ?>;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  true;
            // For validate date
            var actionTemplate =  $('#action-template').html();
            var backupText = {
                regex :  /<strong>\(B\)<\/strong>/,
                text : '<strong>(B)</strong>'
            };
            $.extend(Slick.Editors,{
                numericValue : function(args){
        			$.extend(this, new Slick.Editors.textBox(args));
        			this.input.attr('maxlength' , 11).keypress(function(e){
        				var key = e.keyCode ? e.keyCode : e.which;
        				if(!key || key == 8 || key == 13 || e.ctrlKey || e.shiftKey){
        					return;
        				}
        				var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
                        ///^[\-+]??$/
                        //&& (!/^[\-+]?([0-9]{1}|[1-9][0-9]{1,2})(\.[0-9]{0,2})?$/.test(val)
        				if(val == '0' || !/^[\-]?([0-9]{0,8})(\.[0-9]{0,2})?$/.test(val)){
        					e.preventDefault();
        					return false;
        				}
        			});
        		}
            });
            $.extend(Slick.Formatters,{
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate, company_id,
                    dataContext.id, dataContext.name), columnDef, dataContext);
                },
                goToUpdate : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(urlUpdateCustomer, company_id, dataContext.id, value), columnDef, dataContext);
                },
                numericValue : function(row, cell, value, columnDef, dataContext){
                    if(typeof value != 'undefined'){
                        var specialChar = '';
                        if(dataContext.type && (dataContext.type == 'euro' || dataContext.type == 'ec_euro')){
                            specialChar = $budget_settings;
                        } else if(dataContext.type && dataContext.type == 'md'){
                            specialChar = '<?php echo __($md, true);?>';
                        } else {
                            specialChar = '<?php echo __('FTE', true);?>';
                        }
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ') + ' ' + specialChar + '</span>', columnDef, dataContext);
                    } else {
                        return '';
                    }
                },
            });
            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                company_id : {defaulValue : ''},
                profit_id : {defaulValue : ''},
                manager_id : {defaulValue : ''},
                family_id : {defaulValue : 0},
                subfamily_id : {defaulValue : 0},
                type : {defaulValue : 0},
                is_admin : {defaulValue : 0}
            };
            if(monthLists){
    			$.each(monthLists, function(ind, val){
    				$this.fields[ind] = {defaulValue : ''};
    			});
    		}
            $this.url =  '<?php echo $html->url(array('action' => 'update_budget')); ?>';
            ControlGrid = $this.init($('#project_container'),data,columns,{
                enableAddRow : false,
                showHeaderRow :  true,
                frozenColumn: 4
            });
            $this.onCellChange = function(args){
                if(args && args.item){
                    var sumBudget = 0;
                    $.each(args.item, function(key, val){
                        if(isNaN(parseFloat(key)) == false){
                            sumBudget += val ? parseFloat(val) : 0;
                        }
                    });
                    args.item.year = sumBudget;

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
                }
                return true;
            };
            $( "#profit" ).change(function() {
                var val = $("#profit").val() ? $("#profit").val() : -1;
    			var currentUrl = updateQueryStringParameter(location.href,'profit', val);
    			location.href = currentUrl;
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
    })(jQuery);
</script>
