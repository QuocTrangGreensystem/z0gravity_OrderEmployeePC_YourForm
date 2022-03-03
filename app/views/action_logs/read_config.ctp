<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<?php
App::import('vendor', 'agent');
	echo $html->script(array(
		//'history_filter',
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
		'slick_grid_custom'
	)); 
	echo $html->css(array(
		'jquery.multiSelect',
		'slick_grid/slick.grid',
		'slick_grid/slick.pager',
		'slick_grid/slick.common',
		'slick_grid/slick.edit'
	));
	echo $this->element('dialog_projects');
?>
<style>
	.slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
	.wd-tab .wd-aside-left{width: 350px !important;}
	.wd-tab .wd-content {
		padding-right: 0;
        overflow: auto;
        max-height: 495px;
	}
	.open-dialog {
		float: right;
	}
	.dialog {
		display: none;
	}
	.buttons {
		clear: both;
		text-align: left;
	}
	.dialog {
		color: #000;
		font-size: 12px;
	}
	.wd-input {
		padding: 10px;
	}
	.wd-input input,
	.wd-input button {
		padding: 5px;
	}
	.open-setting {
		display: block;
		width: 32px;
		float: right;
		margin-left: 8px;
		padding-bottom: 16px;
		background-image: url(/img/icon-setting.png);
	}
	.open-setting span {
		text-indent: -9999px;
		display: block;
	}
	#setting-dialog {
		color: #000;
		font-size: 12px;
	}
	.ui-dialog-buttonpane {
		padding: 5px;
		text-align: right;
		border-top: 1px solid #ddd;
	}
	.ui-button {
		background: rgb(0,71,137);
		background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iIzAwNDc4OSIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiMwMTQwNzkiIHN0b3Atb3BhY2l0eT0iMSIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+);
		background: -moz-linear-gradient(top,  rgba(0,71,137,1) 0%, rgba(1,64,121,1) 100%);
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(0,71,137,1)), color-stop(100%,rgba(1,64,121,1)));
		background: -webkit-linear-gradient(top,  rgba(0,71,137,1) 0%,rgba(1,64,121,1) 100%);
		background: -o-linear-gradient(top,  rgba(0,71,137,1) 0%,rgba(1,64,121,1) 100%);
		background: -ms-linear-gradient(top,  rgba(0,71,137,1) 0%,rgba(1,64,121,1) 100%);
		background: linear-gradient(to bottom,  rgba(0,71,137,1) 0%,rgba(1,64,121,1) 100%);
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#004789', endColorstr='#014079',GradientType=0 );
		padding: 10px;
		color: #fff;
		border: 0;
		border-radius: 4px;
		cursor: pointer;
	}
    #absence-fixed tbody td {
        font-size: 14px;
    } 
    .wd-content #absence-fixed {   
        width: 60% !important;
    }
</style>
<?php

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
								<table cellspacing="0" cellpadding="0" class="display" id="absence-fixed">
                                        <thead>
                                            <tr class="wd-header">
                                                <th class="wd-order" width="30%"><?php echo __('Key', true); ?></th>
                                                <th width="10%"><?php echo __('Value', true); ?></th>
                                                


                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            

                                            foreach ($allConfig as $key => $value ):
                                                
                                                ?>
                                                <tr>
                                                    <td><?php echo $key; ?></td>
                                                    <td ><?php echo $value; ?></td>
                                                    

                                                </tr>
                                                <?php
                                            endforeach;
                                        
                                        ?>
                                        </tbody>
                                    </table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="setting-dialog" title="<?php __('Setting') ?>">
	<form>
		<div class="wd-input">
			<label for="dont-store-login" style="float: none; width: auto"><input type="checkbox" id="dont-store-login" <?php echo isset($companyConfigs['action_dont_store_login']) && $companyConfigs['action_dont_store_login'] ? 'checked' : '' ?> /> <?php __('Don\'t store login/logout') ?></label>
		</div>
		<?php
		/*
		<div class="wd-input">
			<?php __('Ignore and don\'t store log for those resource IDs:') ?><br/>
			<textarea name="" id="ignore-list" cols="60" rows="10"><?php echo isset($companyConfigs['action_ignore_list']) && $companyConfigs['action_ignore_list'] ? $companyConfigs['action_ignore_list'] : '' ?></textarea><br/>
			<small><?php __('Separate each ID by line') ?></small>
		</div>
		*/
		?>
	</form>
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
		'id' => 'created',
		'field' => 'created',
		'name' => __('Time', true),
		'width' => 130,
		'sortable' => true,
		'filter' => true,
		'resizable' => true
	),
	array(
		'id' => 'employee_id',
		'field' => 'employee_id',
		'url' => $this->Html->url('/employees/edit/{1}/{2}'),
		'name' => __('ID', true),
		'width' => 60,
		'sortable' => true,
		'resizable' => false,
		'formatter' => 'linkFormatter'
	),
	array(
		'id' => 'first_name',
		'field' => 'first_name',
		'name' => __('First Name', true),
		'width' => 100,
		'sortable' => true,
		'resizable' => true,
		'editor' => 'Slick.Editors.textBox'
	),
	array(
		'id' => 'last_name',
		'field' => 'last_name',
		'name' => __('Last Name', true),
		'width' => 100,
		'sortable' => true,
		'resizable' => true,
		'editor' => 'Slick.Editors.textBox'
	),
	array(
		'id' => 'what',
		'field' => 'what',
		'name' => __('Message', true),
		'width' => 150,
		'sortable' => true,
		'resizable' => true,
		'editor' => 'Slick.Editors.textArea'
	),
	array(
		'id' => 'url',
		'field' => 'url',
		'name' => __('URL', true),
		'width' => 150,
		'sortable' => true,
		'resizable' => true,
		'editor' => 'Slick.Editors.textBox'
	),
	array(
		'id' => 'ip',
		'field' => 'ip',
		'name' => __('IP', true),
		'width' => 100,
		'sortable' => true,
		'resizable' => true,
		//'editor' => 'Slick.Editors.textBox'
	),
	array(
		'id' => 'company',
		'field' => 'company',
		'name' => __('Company', true),
		'width' => 120,
		'sortable' => true,
		'resizable' => true,
		//'editor' => 'Slick.Editors.textBox'
	),
	array(
		'id' => 'browser',
		'field' => 'browser',
		'name' => __('Browser', true),
		'width' => 100,
		'sortable' => true,
		'resizable' => true,
		'editor' => 'Slick.Editors.textBox'
	),
	array(
		'id' => 'os',
		'field' => 'os',
		'name' => __('OS', true),
		'width' => 100,
		'sortable' => true,
		'resizable' => true
	),
	array(
		'id' => 'data',
		'field' => 'data',
		'name' => __('Action', true),
		'width' => 150,
		'sortable' => true,
		'resizable' => true,
		'editor' => 'Slick.Editors.textArea'
	)
);
$i = 1;
$dataView = array();
$selectMaps = array();

foreach($logs as $log){
	$data = $log['ActionLog'];
	$data['url'] .= sprintf(' [%s]', $log['ActionLog']['method']);
	$data['company'] = isset($log['Company']['company_name']) ? $log['Company']['company_name'] : '';
	$info = AgentParser::getBrowserInfo($data['agent']);
	$data['browser'] = sprintf('%s %s', $info['name'], $info['version']);
	$data['os'] = $info['platform'];
	if( !empty($log['Employee']) ){
		$data['last_name'] = $log['Employee']['last_name'];
		$data['first_name'] = $log['Employee']['first_name'];
	} else {
		$data['last_name'] = $data['first_name'] = '';
	}
	$dataView[] = $data;
}
?>

<script type="text/javascript">
	//var DataValidator = {};
	var linkFormatter = function ( row, cell, value, columnDef, dataContext ) {
		return '<a target="_blank" href="' + columnDef.url.replace('{1}', dataContext.employee_id).replace('{2}', dataContext.company_id) + '">' + value + '</a>';
		//return value;
	};
	var markDown = function(value){
		return value.replace(/`([^`]+)`/gm, '<b>$1</b>');
	};
	$(document).ready(function(){
		//dialog
		var dialog = $('#setting-dialog').dialog({
			modal: true,
            width       : 'auto',
            height      : 'auto',
			autoOpen: false,
			buttons: [
				{
					text: '<?php __('Save') ?>',
					click: function(){
						var data = {
							data: {
								action_dont_store_login : $('#dont-store-login').prop('checked') ? 1 : 0
								//action_ignore_list : $('#ignore-list').val()
							}
						};
						$.ajax({
							type: 'POST',
							url : '<?php echo $this->Html->url('/action_logs/saveSetting') ?>',
							data : data,
							complete: function(){
								window.location.reload(0);
							}
						});
					}
				},
				{
					text: '<?php __('Cancel') ?>',
					click: function(){
						$(this).dialog('close');
					}
				}
			],
			close: function(){
				$('#dont-store-login').closest('form')[0].reset();
			}
		});
		$('#setting-log').click(function(){
			dialog.dialog('open');
		});
		var data = <?php echo json_encode($dataView); ?>;
		var columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;

		var $this = SlickGridCustom;
		$this.canModified =  true;
		$this.onCellChange = function(args){
			return false;
		}
		$this.selectMaps = <?php echo json_encode($selectMaps); ?>;
		$this.fields = {};
		$this.init($('#project_container'), data, columns, {
			enableAddRow : false
		});
		var grid = $this.getInstance(),
			dataView = grid.getDataView();

		$('#clear-log').click(function(){
			if( !confirm('<?php echo h(__('Are you sure?', true)) ?>') )return false;
		});
		//<?php echo $this->Html->url(array('action' => 'export')) ?>

		$('#export-log').click(function(){
			var length = dataView.getLength();
			var list = [];
			for(var i =0; i<length; i++){
				list.push(dataView.getItem(i).id);
			}
			$('#export-item-list').val(list.join(',')).closest('form').submit();
		});
		$('#start-date').datepicker({
			dateFormat: 'dd-mm-yy',
			onSelect: function(text, obj){
				var start = $(this).datepicker('getDate');
				var end = $('#end-date').datepicker('getDate');
				if(dateDiff(start, end) > 30 || start > end){
					$('#end-date').datepicker('setDate', start);
				}
			}
		}).prop('readonly', true);
		$('#end-date').datepicker({
			dateFormat: 'dd-mm-yy',
			beforeShowDay: function(d){
                var date = d.getDay();
                var start = $('#start-date').datepicker('getDate');
                var check = true;
                if( start ){
	           	    check = check && start <= d && dateDiff(start, d) <= 30;
            	}
                return [ check, '', ''];
            },
		}).prop('readonly', true);
	});
	var _MS_PER_DAY = 1000 * 60 * 60 * 24;

	// a and b are javascript Date objects
	function dateDiff(a, b) {
	  // Discard the time and time-zone information.
	  var utc1 = Date.UTC(a.getFullYear(), a.getMonth(), a.getDate());
	  var utc2 = Date.UTC(b.getFullYear(), b.getMonth(), b.getDate());

	  return Math.abs(Math.floor((utc2 - utc1) / _MS_PER_DAY));
	}
</script>
