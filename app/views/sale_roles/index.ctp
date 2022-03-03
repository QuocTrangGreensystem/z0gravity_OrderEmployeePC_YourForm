<?php
    echo $html->script(array(
        'jquery.multiSelect',
        'history_filter',
        'slick_grid/lib/jquery-ui-1.8.16.custom.min',
        'slick_grid/lib/jquery.event.drag-2.0.min',
        'slick_grid/slick.core',
        'slick_grid/slick.dataview',
        'slick_grid/controls/slick.pager',
        'slick_grid/slick.formatters',
        'slick_grid/slick.grid',
        'responsive_table.js'
    ));
    echo $html->css(array(
        'jquery.multiSelect',
        'projects',
        'slick_grid/slick.grid_v2',
        'slick_grid/slick.pager',
        'slick_grid/slick.common_v2',
        'slick_grid/slick.edit'
    ));
    echo $this->element('dialog_projects');
?>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
    .multiSelect {width: 323px !important}
    .multiSelect span{width: 317px !important}
  	.wd-title a.wd-add-project{background: url('<?php echo $this->Html->url('/img/front/bg-add-project-new.png'); ?>') no-repeat left top !important;padding-left: 70px !important;}
  	.wd-title a.wd-add-project:hover{background-position:left -33px !important}
</style>
<!--[if lt IE 8]>
    <style type='text/css'>
        #wd-table{
            height: 300px;
        }
    </style>
<![endif]-->
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-tab">
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
                                <div id="message-place">
                                    <?php
                                    App::import("vendor", "str_utility");
                                    $str_utility = new str_utility();
                                    echo $this->Session->flash();
                                    ?>
                                </div>
                                <div class="wd-table" id="project_container" style="width:100%;height:400px;">

                                </div>
                                <div id="pager" style="width:100%;height:36px; overflow: hidden;">

                                </div>
                                <?php echo $this->element('grid_status'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$employeeInfo = $this->Session->read('Auth.employee_info');
if ($employeeInfo["Employee"]['is_sas'] == 0 && $employeeInfo["Role"]["name"] == "conslt") {
    echo '<style type="text/css">.wd-bt-big,.wd-add-project{display:none !important;}</style>';
}
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
		'sortable' => true,
		'resizable' => false
	),
	array(
		'id' => 'employee_id',
		'field' => 'employee_id',
		'name' => __('Employee', true),
		'width' => 300,
		'sortable' => true,
		'resizable' => true
	),
	array(
		'id' => 'sales_director',
		'field' => 'sales_director',
		'name' => __('Sales Director', true),
		'width' => 110,
		'sortable' => false,
		'resizable' => false,
		'formatter' => 'Slick.Formatters.HTMLData'
	),
    array(
		'id' => 'sales_manager',
		'field' => 'sales_manager',
		'name' => __('Sales Manager', true),
		'width' => 110,
		'sortable' => false,
		'resizable' => false,
		'formatter' => 'Slick.Formatters.HTMLData'
	),
	array(
		'id' => 'salesman',
		'field' => 'salesman',
		'name' => __('Salesman/Affaire responsible', true),
		'width' => 200,
		'sortable' => false,
		'resizable' => false,
		'formatter' => 'Slick.Formatters.HTMLData'
	),
	array(
		'id' => 'financial',
		'field' => 'financial',
		'name' => __('Financial', true),
		'width' => 100,
		'sortable' => false,
		'resizable' => false,
		'formatter' => 'Slick.Formatters.HTMLData'
	),
	array(
		'id' => 'auditor',
		'field' => 'auditor',
		'name' => __('Auditor', true),
		'width' => 100,
		'sortable' => false,
		'resizable' => false,
		'formatter' => 'Slick.Formatters.HTMLData'
	),
    array(
		'id' => 'easyrap',
		'field' => 'easyrap',
		'name' => __('Easyrap', true),
		'width' => 100,
		'sortable' => false,
		'resizable' => false,
		'formatter' => 'Slick.Formatters.HTMLData'
	)
);
$i = 1;
$dataView = array();
if(!empty($employees)){
    foreach ($employees as $id => $name) {
        $data = array(
            'id' => $id,
            'no.' => $i++,
        );
    	$data['employee_id'] = $name;
        /**
		 * Sales Director
		 */
		$class = '';
		$message = __('Do you want to choose "%s" as Sales Director?', true);
		$checkRole = (!empty($saleRoles) && !empty($saleRoles[$id]) && $saleRoles[$id] == 1) ? true : false;
		if ($checkRole == true) {
			$message = __('Are you sure destroy permission Sales Director of "%s"?', true);
			$class = ' wd-update-default';
		}
		$data['sales_director'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Sales Director', true), array(
					'controller' => 'sale_roles',
					'action' => 'update', 'sales_director', $company_id, $id, empty($class)), array(
					'class' => 'wd-update' . $class), sprintf($message, $name)) . '</div></div>';
        /**
		 * Sales Manager
		 */
		$class = '';
		$message = __('Do you want to choose "%s" as Sales Manager?', true);
		$checkRole = (!empty($saleRoles) && !empty($saleRoles[$id]) && $saleRoles[$id] == 2) ? true : false;
		if ($checkRole == true) {
			$message = __('Are you sure destroy permission Sales Manager of "%s"?', true);
			$class = ' wd-update-default';
		}
		$data['sales_manager'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Sales Manager', true), array(
					'controller' => 'sale_roles',
					'action' => 'update', 'sales_manager', $company_id, $id, empty($class)), array(
					'class' => 'wd-update' . $class), sprintf($message, $name)) . '</div></div>';
        /**
		 *  Salesman/Affaire responsible
		 */
		$class = '';
		$message = __('Do you want to choose "%s" as  Salesman/Affaire responsible?', true);
		$checkRole = (!empty($saleRoles) && !empty($saleRoles[$id]) && $saleRoles[$id] == 3) ? true : false;
		if ($checkRole == true) {
			$message = __('Are you sure destroy permission  Salesman/Affaire responsible of "%s"?', true);
			$class = ' wd-update-default';
		}
		$data['salesman'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__(' Salesman/Affaire responsible', true), array(
					'controller' => 'sale_roles',
					'action' => 'update', 'salesman', $company_id, $id, empty($class)), array(
					'class' => 'wd-update' . $class), sprintf($message, $name)) . '</div></div>';
        /**
		 * Financial
		 */
		$class = '';
		$message = __('Do you want to choose "%s" as Financial?', true);
		$checkRole = (!empty($saleRoles) && !empty($saleRoles[$id]) && $saleRoles[$id] == 4) ? true : false;
		if ($checkRole == true) {
			$message = __('Are you sure destroy permission Financial of "%s"?', true);
			$class = ' wd-update-default';
		}
		$data['financial'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Financial', true), array(
					'controller' => 'sale_roles',
					'action' => 'update', 'financial', $company_id, $id, empty($class)), array(
					'class' => 'wd-update' . $class), sprintf($message, $name)) . '</div></div>';
        /**
		 * Auditor
		 */
		$class = '';
		$message = __('Do you want to choose "%s" as Auditor?', true);
		$checkRole = (!empty($saleRoles) && !empty($saleRoles[$id]) && $saleRoles[$id] == 5) ? true : false;
		if ($checkRole == true) {
			$message = __('Are you sure destroy permission Auditor of "%s"?', true);
			$class = ' wd-update-default';
		}
		$data['auditor'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Auditor', true), array(
					'controller' => 'sale_roles',
					'action' => 'update', 'auditor', $company_id, $id, empty($class)), array(
					'class' => 'wd-update' . $class), sprintf($message, $name)) . '</div></div>';
        /**
		 * Easyrap
		 */
		$class = '';
		$message = __('Do you want to choose "%s" as Easyrap?', true);
		$checkRole = (!empty($saleRoles) && !empty($saleRoles[$id]) && $saleRoles[$id] == 6) ? true : false;
		if ($checkRole == true) {
			$message = __('Are you sure destroy permission Easyrap of "%s"?', true);
			$class = ' wd-update-default';
		}
		$data['easyrap'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Easyrap', true), array(
					'controller' => 'sale_roles',
					'action' => 'update', 'easyrap', $company_id, $id, empty($class)), array(
					'class' => 'wd-update' . $class), sprintf($message, $name)) . '</div></div>';
    	$dataView[] = $data;
    }
}
?>
<script type="text/javascript">
    (function($){
        $(function () {
            /* begin render table*/
            var dataView,sortcol,triggger = false,grid,$sortColumn,$sortOrder;
            var data = <?php echo json_encode($dataView); ?>;
            var columns = <?php echo jsonParseOptions($columns, array('formatter')); ?>;
            var options = {
                enableCellNavigation: false,
                enableColumnReorder: false,
                showHeaderRow: true,
                editable: false,
                enableAddRow: false,
                headerRowHeight: 30,
                rowHeight: 33
            };
            var columnFilters = {};
            var $parent = $('#project_container');
            function updateHeaderRow() {
                $sortOrder = $("<input type=\"text\" style=\"display:none\" name=\""+ $parent.attr('id') +".SortOrder\" />")
                .appendTo($parent);

                $sortColumn = $("<input type=\"text\" style=\"display:none\" name=\""+ $parent.attr('id') +".SortColumn\" />")
                .appendTo($parent).change(function(){
                    triggger = true;
                    var index = grid.getColumnIndex($sortColumn.val());
                    grid.setSortColumns([{
                            sortAsc : $sortOrder.val() != 'asc',
                            columnId : $sortColumn.val()
                        }]);
                    $parent.find('.slick-header-columns').children().eq(index)
                    .find('.slick-sort-indicator').click();
                });
                for (var i = 0; i < columns.length; i++) {
                    var noFilterInput = false, column = columns[i];
                    if (column.id === "no." || column.id === "action." || column.id === "sales_director" || column.id === "sales_manager" || column.id === "salesman" || column.id === "financial" || column.id === "auditor") {
                        noFilterInput = true;
                    }
                    if(!noFilterInput){
                        var header = grid.getHeaderRowColumn(column.id);
                        $(header).empty();
                        $('<div class="multiselect-filter"></div>').append($("<input type=\"text\" style=\"border: 1px solid #cccccc; width:95%\" name=\""+ column.field +"\" />")
                        .data("columnId", column.id)
                        .val(columnFilters[column.id])
                        ).appendTo(header);
                    }
                    $("<input type=\"text\" style=\"display:none\" name=\""+ column.field +".Resize\" />").data('columnIndex',i).appendTo($parent).change(function(){
                        var $element = $(this);
                        columns[$element.data('columnIndex')].width = Number($element.val());
                        grid.eval('applyColumnHeaderWidths();updateCanvasWidth(true);');
                    });
                }
            }
            function comparer(a,b) {
                var x = a[sortcol], y = b[sortcol];
                return (x == y ? 0 : (x > y ? 1 : -1));
            }
            function comparer_date(a,b) {
                var arr;
                if (typeof(a[sortcol]) === "undefined" || a[sortcol]==""){
                    c = "1/1/1970";
                }
                else{
                    arr = a[sortcol].split("-");
                    c = arr[1]+"/"+arr[0]+"/"+arr[2];
                }
                if (typeof(b[sortcol]) === "undefined" || b[sortcol]==""){
                    d  = "1/1/1970";
                }else{
                    arr = b[sortcol].split("-");
                    d = arr[1]+"/"+arr[0]+"/"+arr[2];
                }
                var c = new Date(c),
                d = new Date(d);
                return (c.getTime() - d.getTime());
            }
            function filter(item) {
                for (var columnId in columnFilters) {
                    if (columnId !== undefined && columnFilters[columnId] !== "") {
                        var c = grid.getColumns()[grid.getColumnIndex(columnId)];
                        if (item[c.field].toLowerCase().indexOf(columnFilters[columnId].toLowerCase()) == -1) {
                            return false;
                        }
                    }
                }
                return true;
            }
            dataView = new Slick.Data.DataView();
            grid = new Slick.Grid($parent, dataView, columns, options);
            dataView.onRowCountChanged.subscribe(function (e, args) {
                grid.updateRowCount();
                grid.render();
            });
            dataView.onRowsChanged.subscribe(function (e, args) {
                grid.invalidateRows(args.rows);
                grid.render();
            });
            $(grid.getHeaderRow()).delegate(":input", "change keyup", function (e) {
                columnFilters[$(this).data("columnId")] = $.trim($(this).val());
                dataView.refresh();
            });
            grid.onSort.subscribe(function(e, args) {
                sortcol = args.sortCol.field;
                if (args.sortCol.datatype=="datetime"){
                    dataView.sort(comparer_date, args.sortAsc);
                }
                else{
                    dataView.sort(comparer, args.sortAsc);
                }
                if(triggger){
                    triggger = false;
                    return;
                }
                $sortOrder.val(args.sortAsc ? 'asc' : 'desc').change();
                $sortColumn.val(args.sortCol.id).change();
            });

            grid.onColumnsResized.subscribe(function (e, args) {
                for (var i = 0; i < columns.length; i++) {
                    if(columns[i].previousWidth != columns[i].width){
                        $('input[name="' + columns[i].field + '.Resize"]').val(columns[i].width).change();
                        break;
                    }
                }
            });
            dataView.beginUpdate();
            dataView.setItems(data);
            dataView.setFilter(filter);
            dataView.endUpdate();
            updateHeaderRow();
        });
    })(jQuery);
</script>
