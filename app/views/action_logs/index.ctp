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
		'slick_grid/slick.edit',
		'preview/tab-admin',
		'layout_admin_2019'
	));
	echo $this->element('dialog_projects');
?>
<style>
	.slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
	.wd-tab .wd-aside-left{width: 350px !important;}
	.wd-tab .wd-content {
		padding-right: 0;
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
		padding-left: 0;
	}
	.wd-input input,
	.wd-input button {
		padding: 5px;
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
	.open-setting, .export-excel-icon-all, .archive-log, .download-log{
		height: 40px;
		width: 40px;
		line-height: 38px;
		border: 1px solid #E1E6E8;
		background-color: #FFFFFF;
		border-radius: 3px;
		padding: 0;
		box-sizing: border-box;
		display: inline-block;
		text-align: center;
		transition: all 0.3s ease;
		color: #666;
		display: inline-block;
		vertical-align: top
		font-weight: 400;
	}
	.open-setting:before {
		content: "\e09a";
		font-family: 'simple-line-icons';
		font-size: 20px;
	}
	.archive-log:before {
		content: "\e089";
		font-family: 'simple-line-icons';
		font-size: 20px;
	}
	.wd-title, #navigator{
		display: inline-block;
		vertical-align: top
	}
	.wd-title{
		width: 200px;
	}
	#navigator{
		width: calc( 100% - 230px);
	}
	.wd-title{
		margin-top: 5px;
	}
	.wd-main-content .wd-title a{
		font-weight: 400;
	}
	.wd-project-admin.loading:after{
		content: '';
		position: absolute;
		left: 0;
		top: 0;
		width: 100%;
		height: 100%;
		background: rgba(255,255,255, 0.6) url(/img/business/wait-1.gif) no-repeat center center;
		background-size: 30px;
		z-index: 3;
		display: block;
	}
	.open-setting span{
		display: none;
	}
	.wd-tab .wd-content{
		padding-top: 15px;
	}
		   
	.download-log:before {
		content: "\e083";
		font-family: 'simple-line-icons';
		font-size: 20px;
	}
	#log-content{
		    overflow: auto;
		height: 370px;
		font-size: 16px;
		    padding: 0 15px;
	}
	.log-company{
		margin-bottom: 15px;
	}
	.log-company ul{
		padding-left: 15px;
	}
	.log-company-name > span{
		text-transform: uppercase;
		font-weight: 600;
		font-size: 15px;
		margin-bottom: 10px;
	}
	.log-by-year{
		margin-bottom: 15px;
	}
	.log-by-year .log-year > span{
		margin-bottom: 15px;
		text-decoration: underline;
	}
</style>
<?php
echo $this->Form->create(false, array(
	'type' => 'POST',
	'style' => 'display: none',
	'url' => array('action' => 'export')));
echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
echo $this->Form->end();
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
								<div class="wd-title">
									<a href="javascript:;" id="export-log" class="export-excel-icon-all open-dialog" style="margin-right:5px; " title="<?php __('Export Excel')?>"><span><?php __('Export') ?></span></a>
									<a href="javascript:;" id="archive-log" class="archive-log btn" style="margin-right:5px; " title="<?php __('Archive logs')?>"><span><?php __('Archive') ?></span></a>
									<a href="javascript:;" onclick="popupActionLog.call(this);" id="download-log" class="download-log btn" style="margin-right:5px; " title="<?php __('Download logs')?>"><span><?php __('Download') ?></span></a>
									<a href="javascript:;" id="setting-log" class="open-setting" style="margin-right:5px; " title="<?php __('Settings')?>"><span><?php __('Settings') ?></span></a>
								</div>
								<?php echo $this->Form->create(false, array('url' => '/action_logs/', 'type' => 'get', 'class' => 'wd-input', 'id' => 'navigator')) ?>
									<?php __('From') ?> <input id="start-date" name="start" value="<?php echo $start ?>"> <?php __('To') ?> <input id="end-date" name="end" value="<?php echo $end ?>">
									<button><?php __('OK') ?></button>
								<?php echo $this->Form->end() ?>
								<div id="message-place">
									<?php
									App::import("vendor", "str_utility");
									$str_utility = new str_utility();
									echo $this->Session->flash();
									?>
								</div>
								<div class="wd-table" id="project_container" style="width:100%;height:450px;">

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="template_logs" style="height: 420px; width: 320px;display: none;">
    <div id="log-content"></div>
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
	// array(
		// 'id' => 'company',
		// 'field' => 'company',
		// 'name' => __('Company', true),
		// 'width' => 120,
		// 'sortable' => true,
		// 'resizable' => true,
	// ),
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
$i18ns = array(
	'archive_logs' => __('Please archive system logs', true),
);
?>

<script type="text/javascript">
	//var DataValidator = {};
	var DIRECTORY_SEPARATOR = <?php echo json_encode(DIRECTORY_SEPARATOR);?>;
	var linkFormatter = function ( row, cell, value, columnDef, dataContext ) {
		return '<a target="_blank" href="' + columnDef.url.replace('{1}', dataContext.employee_id).replace('{2}', dataContext.company_id) + '">' + value + '</a>';
		//return value;
	};
	var i18ns = <?php echo json_encode($i18ns); ?>;
	var api_key = <?php echo json_encode($api_key) ?>;
	var wdTable = $('.wd-table');
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
		
		$(window).trigger('resize');
		
	});
	$('#archive-log').on('click', function(e){
	   var _this = $(this).closest('.wd-project-admin');
	   _this.addClass('loading');
	  $.ajax({
			url: "/action_logs/archiveLogs",
			type: "POST",
			cache: false,
			success: function (result) {
				// if(result){
					_this.removeClass('loading');
				// }
			}
		});
	});
	$('#download-log12').on('click', function(e){
	   var _this = $(this).closest('.wd-project-admin');
	   _this.addClass('loading');
	  $.ajax({
			url: "/action_logs/downloadLogs",
			type: "POST",
			cache: false,
			success: function (result) {
				// if(result){
					window.location = result;
					_this.removeClass('loading');
				// }
			}
		});
	});
	$(window).resize(function () {
        var heightTable = $(window).height() - wdTable.offset().top - 40;
        wdTable.css({
            height: heightTable,
        });
        var header_table_height = 0;
        $('.slick-pane-header').each(function () {
            _height = $(this).height();
            header_table_height = Math.max(header_table_height, _height);
        });
        var header_row_height = $('.slick-headerrow:first').height();
        var header_row_columns_height = $('.slick-headerrow-columns:first').height();
        wdTable.find('.slick-viewport').css({
            height: heightTable - header_table_height - header_row_height - header_row_columns_height - 5,
        });
    });
	var _MS_PER_DAY = 1000 * 60 * 60 * 24;

	// a and b are javascript Date objects
	function dateDiff(a, b) {
	  // Discard the time and time-zone information.
	  var utc1 = Date.UTC(a.getFullYear(), a.getMonth(), a.getDate());
	  var utc2 = Date.UTC(b.getFullYear(), b.getMonth(), b.getDate());

	  return Math.abs(Math.floor((utc2 - utc1) / _MS_PER_DAY));
	}
	function renderContentLog(logList){
		var logFormat = {}; 
		i = 0;
		$.each(logList, function (key, value) {
			value = value.split(DIRECTORY_SEPARATOR);
			if(!logFormat[value[0]]) logFormat[value[0]] = {};
			logFormat[value[0]][i++] = value[1];
			// i = 0;
			// companyName = value;
		});
		console.log(logFormat);
		_html = '';
		if(logFormat){
			$.each(logFormat, function (companyName, values) {
				_html += '<ul class="log-company">';
				_html += '<li class="log-company-name"><span>'+ companyName+'</span>';
				if(values){
					_html += '<ul class="log-content">';
						$.each(values, function (key, log) {
							_url = '/action_logs/attachment/'+ companyName + '/' + log + '?sid='+ api_key;
							_html += '<li class="log-name"><a href="'+_url +'">'+ log +'</a></li>';
						});
					_html += '</ul>';
				}
				_html += '</li></ul>';
			});
		}
		return _html;
	}
	
	function popupActionLog() {
        var _html = '';
        var popup = $('#template_logs');
        $.ajax({
            url: '/action_logs/popupActionLogs',
            type: 'POST',
            dataType: 'json',
            success: function (data) {
				if(data){
					_html += renderContentLog(data);
				}else{
					_html += '<span>'+ i18ns.archive_logs +'</span>';
				}
                $('#log-content').empty().html(_html);

                var createDialog = function () {
                    $('#template_logs').dialog({
                        position: 'center',
                        autoOpen: false,
                        height: 420,
                        modal: true,
                        width: 520,
                        minHeight: 50,
                        open: function (e) {
                            var $dialog = $(e.target);
                            $dialog.dialog({open: $.noop});
                        }
                    });
                    createDialog = $.noop;
                }
                createDialog();
                $("#template_logs").dialog('option', {title: ''}).dialog('open');

            }
        });

    }
</script>
