<?php
echo $html->script('jshashtable-2.1');
echo $html->script('jquery.numberformatter-1.2.3');
echo $html->script('jquery.formatCurrency-1.4.0');
echo $html->script('jquery.validation.min');
echo $html->css('jquery.multiSelect');
echo $html->script('validateDate');
echo $html->css('dd');
echo $html->script('jquery.dd');
echo $html->css('gantt');
echo $html->css('layout_2019');
echo $html->css('preview/project_amr');
echo $this->Html->script(array(
    'dashboard/jqx-all',
    'dashboard/jqxchart_preview',
    'dashboard/jqxcore',
    'dashboard/jqxdata',
    'dashboard/jqxcheckbox',
    'dashboard/jqxradiobutton',
    'dashboard/gettheme',
    'dashboard/jqxgauge',
    'dashboard/jqxbuttons',
    'dashboard/jqxslider',
    'chart/highcharts.js',
    'chart/exporting.js',
    'html2canvas',
    'jquery.scrollTo',
    'autosize.min',
    'gridster/jquery.gridster.min',
    'preview/jquery.html2canvas.indicator',
	'jquery.multiSelect',
	'history_filter'
));
echo $this->Html->css(array(
    'dashboard/jqx.base',
    'dashboard/jqx.web',
    'gridster/jquery.gridster.min',
	'preview/project_task'
));

?>
<style>
    .indidator-layout .btn.btn-table-collapse{
        right: 40px;
        top: 40px;
    }
	#project-favorite svg{
		width: 20px;
		text-align: center;
		padding: 8px;
		vertical-align: top;
		display: inline-block;
	}
	a#project-favorite{
		border-radius: 3px;
		padding: 8px;
		box-sizing: border-box;
		transition: all 0.3s ease 0s;
		width: 40px;
		height: 40px;
		background-color: transparent;
		cursor: pointer;
		opacity: 1;
	}
	.svg-stroke-color {
		stroke: #7b7b7b70;
		fill: none;
		stroke-width: 4px;
	}
	#project-favorite:hover {
		background-color: #fff;
	}
	#project-favorite:hover .svg-stroke-color{
		stroke: #247FC3;
		fill: #fff;
	}
	#project-favorite.has-favor .svg-stroke-color {
		stroke: #247FC3;
		fill: #247FC3;
	}
	.icon-weather svg {
		padding-top: 9px;
	}
	#layout.widget-expand .wd-layout.indidator-layout .wd-main-content .wd-tab{
		padding: 0;
	}
	#layout.widget-expand .wd-main-content > .wd-tab > .wd-panel{
		margin: 0;
	}
	#layout.widget-expand .wd-widget{
		margin-bottom: 0;
	}
</style>
<?php
$employeeAvatarLink = $this->Html->url(array(
	'controller' => 'employees',
	'action' => 'avatar',
	'%ID%',
	'avatar_resize', 
));
$canModified = ($canModified && !$_isProfile) || $_canWrite;
$_isProfile = !empty($_isProfile) ? $_isProfile : 0;
$_canWrite = !empty($_canWrite) ? $_canWrite : 0;
$read_only = !(($canModified && !$_isProfile) || $_canWrite) ? 1 : 0;

$EPM_see_the_budget = isset($companyConfigs['EPM_see_the_budget']) && !empty($companyConfigs['EPM_see_the_budget']) ?  true : false;
$md = !empty($employee_info['Company']['unit']) ? $employee_info['Company']['unit'] : 'M.D';
$svg_icons = array(
	'expand' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.a{fill:none;}.b{fill:#7b7b7b;}</style></defs><rect class="a" width="24" height="24"/><path class="b" d="M11,16a1,1,0,1,1,0-2h3V11a1,1,0,1,1,2,0v4a1,1,0,0,1-1,1ZM1,16a1,1,0,0,1-1-1V11a1,1,0,1,1,2,0v3H5a1,1,0,1,1,0,2ZM14,5V2H11a1,1,0,1,1,0-2h4a1,1,0,0,1,1,1V5a1,1,0,1,1-2,0ZM0,5V1A1,1,0,0,1,1,0H5A1,1,0,0,1,5,2H2V5A1,1,0,1,1,0,5Z" transform="translate(4 4)"/></svg>',
	'setting' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.a{fill:none;}.b{fill:#7b7b7b;fill-rule:evenodd;}</style></defs><rect class="a" width="24" height="24"/><path class="b" d="M19.874,11.507a.857.857,0,0,1-.56.68l-1.576.56c-.092.264-.2.521-.315.769l.716,1.509a.855.855,0,0,1-.084.874,9.959,9.959,0,0,1-2.146,2.146.853.853,0,0,1-.875.084l-1.508-.717c-.249.117-.506.222-.77.316L12.195,19.3a.854.854,0,0,1-.676.56A9.848,9.848,0,0,1,10,19.99a9.848,9.848,0,0,1-1.519-.127A.854.854,0,0,1,7.8,19.3l-.561-1.575c-.264-.094-.521-.2-.77-.316l-1.508.717a.853.853,0,0,1-.875-.084A9.959,9.959,0,0,1,1.945,15.9a.855.855,0,0,1-.084-.874l.716-1.509a8.2,8.2,0,0,1-.315-.769l-1.576-.56a.857.857,0,0,1-.56-.68A9.876,9.876,0,0,1,0,9.991,9.879,9.879,0,0,1,.126,8.473a.857.857,0,0,1,.56-.678l1.576-.56a8.061,8.061,0,0,1,.315-.769L1.861,4.957a.857.857,0,0,1,.084-.876A9.981,9.981,0,0,1,4.091,1.936a.853.853,0,0,1,.875-.084l1.509.717a7.869,7.869,0,0,1,.769-.316L7.8.677a.858.858,0,0,1,.676-.56,9.115,9.115,0,0,1,3.038,0,.858.858,0,0,1,.676.56l.561,1.576a8.021,8.021,0,0,1,.77.316l1.508-.717a.854.854,0,0,1,.875.084,9.981,9.981,0,0,1,2.146,2.145.857.857,0,0,1,.084.876l-.716,1.509a8.2,8.2,0,0,1,.315.769l1.576.56a.855.855,0,0,1,.56.678A9.879,9.879,0,0,1,20,9.991,9.876,9.876,0,0,1,19.874,11.507ZM18.245,9.234l-1.479-.526a.853.853,0,0,1-.535-.565,6.426,6.426,0,0,0-.517-1.257.86.86,0,0,1-.021-.777l.671-1.41a8.357,8.357,0,0,0-1.073-1.072l-1.41.67a.85.85,0,0,1-.777-.02,6.463,6.463,0,0,0-1.257-.517.857.857,0,0,1-.564-.535l-.526-1.479a6.957,6.957,0,0,0-1.512,0L8.717,3.225a.857.857,0,0,1-.564.535A6.463,6.463,0,0,0,6.9,4.277a.854.854,0,0,1-.778.02l-1.41-.67A8.356,8.356,0,0,0,3.636,4.7l.671,1.41a.86.86,0,0,1-.021.777,6.5,6.5,0,0,0-.517,1.258.854.854,0,0,1-.535.563l-1.479.527a6.955,6.955,0,0,0,0,1.513l1.479.525a.858.858,0,0,1,.535.565,6.421,6.421,0,0,0,.517,1.257.862.862,0,0,1,.021.778l-.671,1.409a8.366,8.366,0,0,0,1.072,1.073l1.41-.671A.854.854,0,0,1,6.9,15.7a6.463,6.463,0,0,0,1.257.517.854.854,0,0,1,.564.535l.526,1.478a6.958,6.958,0,0,0,1.512,0l.526-1.478a.854.854,0,0,1,.564-.535A6.463,6.463,0,0,0,13.1,15.7a.858.858,0,0,1,.777-.021l1.41.67a8.277,8.277,0,0,0,1.073-1.072l-.671-1.409a.862.862,0,0,1,.021-.778,6.453,6.453,0,0,0,.517-1.257.858.858,0,0,1,.535-.565l1.479-.525a6.966,6.966,0,0,0,0-1.514ZM10,13.757A3.968,3.968,0,1,1,13.969,9.79,3.973,3.973,0,0,1,10,13.757Zm0-6.222A2.254,2.254,0,1,0,12.254,9.79,2.257,2.257,0,0,0,10,7.535Z" transform="translate(2 2.01)"/></svg>',
	'star' => '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 47.94 47.94" style="enable-background:new 0 0 47.94 47.94;" xml:space="preserve">
		<path class="svg-stroke-color" d="M26.285,2.486l5.407,10.956c0.376,0.762,1.103,1.29,1.944,1.412l12.091,1.757  c2.118,0.308,2.963,2.91,1.431,4.403l-8.749,8.528c-0.608,0.593-0.886,1.448-0.742,2.285l2.065,12.042  c0.362,2.109-1.852,3.717-3.746,2.722l-10.814-5.685c-0.752-0.395-1.651-0.395-2.403,0l-10.814,5.685  c-1.894,0.996-4.108-0.613-3.746-2.722l2.065-12.042c0.144-0.837-0.134-1.692-0.742-2.285l-8.749-8.528  c-1.532-1.494-0.687-4.096,1.431-4.403l12.091-1.757c0.841-0.122,1.568-0.65,1.944-1.412l5.407-10.956  C22.602,0.567,25.338,0.567,26.285,2.486z"/>

	</svg>',
	'print' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.ap,.cp{fill:none;}.bp{fill:#7b7b7b;}.cp{stroke:#7b7b7b;stroke-width:2px;}.d{stroke:none;}</style></defs><rect class="ap" width="24" height="24"/><path class="bp" d="M17920,19756h0l-2,0a2,2,0,0,1-2-2v-9a2,2,0,0,1,2-2h16.008a2,2,0,0,1,2,2v9a2,2,0,0,1-2,2h-2v-2h1a1,1,0,0,0,1-1v-7a1,1,0,0,0-1-1h-14a1,1,0,0,0-1,1v7a1,1,0,0,0,1,1h1v2Z" transform="translate(-17914.002 -19736.998)"/><g class="cp" transform="translate(6 2)"><rect class="d" width="12" height="6"/><rect class="ap" x="1" y="1" width="10" height="4"/></g><g class="cp" transform="translate(6 15)"><rect class="d" width="12" height="7"/><rect class="ap" x="1" y="1" width="10" height="5"/></g><circle class="bp" cx="1.5" cy="1.5" r="1.5" transform="translate(15 10)"/></svg>',
	'sun' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.sun-a{fill:none;}.sun-b{fill:#ee8845;fill-rule:evenodd;}</style></defs><rect class="sun-a" width="24" height="24"/><path class="sun-b" d="M19.143,10.857H17.429a.857.857,0,0,1,0-1.714h1.714a.857.857,0,0,1,0,1.714ZM16.32,4.891a.857.857,0,0,1-1.212-1.212l1.143-1.143a.857.857,0,1,1,1.212,1.212ZM10,16a6,6,0,1,1,6-6A6.007,6.007,0,0,1,10,16ZM10,5.714A4.286,4.286,0,1,0,14.286,10,4.29,4.29,0,0,0,10,5.714Zm0-2.286a.857.857,0,0,1-.857-.857V.857a.857.857,0,1,1,1.714,0V2.571A.857.857,0,0,1,10,3.429ZM4.286,5.143a.855.855,0,0,1-.606-.251L2.537,3.749A.857.857,0,0,1,3.749,2.537L4.891,3.679a.857.857,0,0,1-.606,1.463ZM3.429,10a.857.857,0,0,1-.857.857H.857a.857.857,0,1,1,0-1.714H2.571A.857.857,0,0,1,3.429,10Zm.251,5.108A.857.857,0,1,1,4.891,16.32L3.749,17.463a.857.857,0,0,1-1.212-1.212ZM10,16.571a.857.857,0,0,1,.857.857v1.714a.857.857,0,0,1-1.714,0V17.429A.857.857,0,0,1,10,16.571Zm5.714-1.714a.854.854,0,0,1,.606.251l1.143,1.143a.857.857,0,1,1-1.212,1.212L15.108,16.32a.857.857,0,0,1,.606-1.463Z" transform="translate(2 2)"/></svg>',
	'cloud' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.cloud-a{fill:none;}.cloud-b{fill:#79b2da;fill-rule:evenodd;}</style></defs><rect class="cloud-a" width="24" height="24"/><path class="cloud-b" d="M19.268,9.257H17.8a.773.773,0,0,1,0-1.543h1.463a.773.773,0,0,1,0,1.543Zm-2.9-4.341a.709.709,0,0,1-.517.226.752.752,0,0,1-.732-.771.793.793,0,0,1,.214-.546L16.312,2.8a.713.713,0,0,1,.518-.226.752.752,0,0,1,.732.771.788.788,0,0,1-.215.545Zm-1.576,6.74a3.353,3.353,0,0,1,1.791,3A3.26,3.26,0,0,1,13.415,18H3.659A3.761,3.761,0,0,1,0,14.143a3.884,3.884,0,0,1,1.967-3.418A5.242,5.242,0,0,1,7.073,5.657,4.852,4.852,0,0,1,8.6,5.912a4.073,4.073,0,0,1,3.347-1.8A4.264,4.264,0,0,1,16.1,8.486,4.46,4.46,0,0,1,14.795,11.657ZM7.073,7.2a3.749,3.749,0,0,0-3.646,3.619l-.05.87-.732.4a2.326,2.326,0,0,0-1.181,2.05,2.26,2.26,0,0,0,2.2,2.314h9.756a1.757,1.757,0,0,0,1.707-1.8A1.8,1.8,0,0,0,14.071,13l-.9-.4V11.571a2.249,2.249,0,0,0-2.122-2.309l-.68-.023-.421-.564A3.575,3.575,0,0,0,7.073,7.2Zm4.878-1.543a2.61,2.61,0,0,0-1.993.949A5.291,5.291,0,0,1,11.1,7.721,3.653,3.653,0,0,1,14.267,9.9a2.9,2.9,0,0,0,.367-1.41A2.762,2.762,0,0,0,11.951,5.657Zm0-2.571a.752.752,0,0,1-.732-.771V.771a.733.733,0,1,1,1.463,0V2.314A.752.752,0,0,1,11.951,3.086Zm-3.9,2.057a.711.711,0,0,1-.518-.226L6.556,3.888a.79.79,0,0,1-.214-.545.753.753,0,0,1,.732-.771A.711.711,0,0,1,7.59,2.8l.976,1.029a.791.791,0,0,1,.215.546A.753.753,0,0,1,8.049,5.143Z" transform="translate(2 3)"/></svg>',
	'rain' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.rain-a{fill:none;}.rain-b{fill:#f05352;fill-rule:evenodd;}</style></defs><rect class="rain-a" width="24" height="24"/><path class="rain-b" d="M16.176,14.4a.9.9,0,0,1,0-1.8,2.107,2.107,0,0,0,.792-4.039L15.882,8.1V6.9a2.661,2.661,0,0,0-2.559-2.693L12.5,4.18l-.508-.658A4.359,4.359,0,0,0,8.529,1.8a4.452,4.452,0,0,0-4.4,4.222L4.072,7.036l-.883.472A2.708,2.708,0,0,0,4.412,12.6a.9.9,0,0,1,0,1.8A4.456,4.456,0,0,1,0,9.9,4.508,4.508,0,0,1,2.372,5.912,6.225,6.225,0,0,1,8.529,0,6.117,6.117,0,0,1,13.38,2.408,4.456,4.456,0,0,1,17.647,6.9a3.913,3.913,0,0,1-1.471,7.5ZM7.353,10.2a.891.891,0,0,1,.882.9v1.2a.883.883,0,1,1-1.765,0V11.1A.891.891,0,0,1,7.353,10.2Zm0,3.6a.891.891,0,0,1,.882.9v1.2a.883.883,0,1,1-1.765,0V14.7A.891.891,0,0,1,7.353,13.8Zm2.941-2.4a.891.891,0,0,1,.882.9v1.2a.883.883,0,1,1-1.765,0V12.3A.891.891,0,0,1,10.294,11.4Zm0,3.6a.891.891,0,0,1,.882.9v1.2a.883.883,0,1,1-1.765,0V15.9A.891.891,0,0,1,10.294,15Zm2.941-4.8a.891.891,0,0,1,.882.9v1.2a.883.883,0,1,1-1.765,0V11.1A.891.891,0,0,1,13.235,10.2Zm0,3.6a.891.891,0,0,1,.882.9v1.2a.883.883,0,1,1-1.765,0V14.7A.891.891,0,0,1,13.235,13.8Z" transform="translate(2 3)"/></svg>',
);
function draw_line_progress($value=0){
	ob_start();
	$_css_class = ($value <= 100) ? 'green-line': 'red-line';
	$display_value = min($value, 100);?>
		<div class="wd-progress-slider <?php echo $_css_class;?>" data-value="<?php echo $value;?>">
			<div class="wd-progress-holder">
				<div class="wd-progress-line-holder"></div>
			</div>
			<div class="wd-progress-value-line" style="width:<?php echo $display_value;?>%;"></div>
			<div class="wd-progress-value-text">
				<div class="wd-progress-value-inner">
					<div class="wd-progress-number" style="left:<?php echo $display_value;?>%;">
						<div class="text"><?php echo round($display_value);?>%</div> 
						<input class="input-progress wd-hide" value="<?php echo $value;?>"  onchange="saveManualProgress(this);" onfocusout="progressHideInput(this)" />
					</div>
				</div>
			</div>
		</div>
	<?php
	return ob_get_clean();
}
$i18ns = array(
	'-- Any --' => __('-- Any --', true),
	'add_favorite' => __('Add favorite', true),
	'remove_favorite' => __('Remove favorite', true),
);
$has_favorite = !empty($favorites[$project_id]);

echo $this->Form->create('Export', array('url' => array('controller' => $this->params['controller'], 'action' => 'export_pdf'), 'type' => 'file'));

echo $this->Form->hidden('canvas', array('id' => 'canvasData'));

echo $this->Form->hidden('height', array('id' => 'canvasHeight'));

echo $this->Form->hidden('width', array('id' => 'canvasWidth'));

echo $this->Form->end();

$arg = $this->passedArgs;
$arg["?"] = $this->params['url'];
unset($arg['?']['url'], $arg['?']['ext']);
$type = 'monthyear';
if( !function_exists('wd_layout_setting')){
    function wd_layout_setting($_this, $list_wid){ 
        $task_status = $_this->viewVars['task_status'];
        $issueStatus = $_this->viewVars['issueStatus'];
        $_domain = $_this->viewVars['_domain'];
        ?>
<div class="wd-layout-setting">
    <a href="javascript:void(0);" class="close" title="<?php __('Close')?>"><img src="/img/new-icon/close.png" /><img src="/img/new-icon/close-blue.png" /></a>
    <div class="wd-layout-title">
        <h4><?php echo __('Display settings', true); ?></h4>
        <p><?php echo __('Drag and drop to re-arrange the display', true); ?></p>
    </div>
    <div id ="layout-setting" class="layout-setting gridster">
        <ul>
			<?php
			foreach ($list_wid as $key => $value) {
				if(!isset($value['show']) || (!empty($value['show']) && $value['show'] == 1)):
				$class_status = ($value['display'] == 1) ? '' : 'disabled';
			?>
			<li  data-widget= "<?php echo $value['widget']; ?>" data-row="<?php echo $value['row']; ?>" data-col="<?php echo $value['col']; ?>" data-sizex="<?php echo $value['sizex']; ?>" data-sizey="<?php echo $value['sizey']; ?>" class="<?php echo $class_status; ?>">
				<p class="layout-name"><?php echo __($value['name'], true); ?></p>
				<a href="javascript:void(0);" onclick="displayWidget(this);" id="acd<?php echo $key;?>" data-display = '<?php echo $value['display']; ?>' title="<?php __('Display')?>"></a>
				<?php
					if($value['widget'] == 'project_task' && !empty($task_status)){
						echo '<div class="select"><label class="title">'. __('Status', true) .'</label><div class="dropdown noOrdered">';
						$options = !empty($value['task_status']) ? Set::combine( $value['task_status'], '{n}.status_id', '{n}') : array();
						foreach ($task_status as $status_id => $name) {
							$is_display = isset($options[$status_id]['status_display']) ? $options[$status_id]['status_display'] : 1;
							$is_default = isset($options[$status_id]['default']) ? $options[$status_id]['default'] : 0;
						?>
							<div class="wd-check-box wd-input wd-custom-checkbox wd-custom-checkbox-2">
								<label>
									<input type="checkbox" data-status-id="<?php echo $status_id ?>" class="checkbox" data-status-display= '<?php echo $is_display; ?>' <?php if($is_display) echo 'checked="checked"';?> />
									<span class="wd-checkbox"></span>
									<span class="status-name"><?php echo $name ?></span>
								</label>
								<label>
									<input type="radio" data-status-id="<?php echo $status_id ?>" class="checkbox-2" data-status-default="<?php echo $is_default; ?>" <?php if($is_default) echo 'checked="checked"';?> name="indicator_task_default_status" style="display:none;"/>
									<span class="wd-checkbox-2"></span>
								</label>
							</div>
						<?php }
						echo '</div></div>';
					}
					if($value['widget'] == 'project_synthesis'){
						echo '<div class="select"><label class="title">'. __('Select options', true) .'</label><div class="dropdown">';
						$options = array(
							'ProjectAmr' => __d(sprintf($_domain, 'KPI'), 'Comment', true),
							'Done' =>  __d(sprintf($_domain, 'KPI'), 'Done', true),
							'ProjectIssue' => __d(sprintf($_domain, 'KPI'), 'Issue', true),
							'ProjectRisk' =>  __d(sprintf($_domain, 'KPI'), 'Risk', true),
						);
					
						foreach ($value['options'] as $key => $opt) {
							$opt_display = isset($opt['model_display']) ? $opt['model_display'] : 1;
							$model_id = $opt['model'];
							$opt_name = $options[$model_id];
						?>
							<div class="wd-check-box wd-input wd-custom-checkbox">
								<label><input type="checkbox" data-model="<?php echo $model_id ?>" class="checkbox" data-model-display= '<?php echo $opt_display; ?>' <?php if($opt_display) echo 'checked="checked"';?> /><span class="wd-checkbox"></span><span class="status-name"><?php echo $opt_name ?></span></label>
							</div>
						<?php }
						echo '</div></div>';
					}
					if($value['widget'] == 'project_status'){
						echo '<div class="select"><label class="title">'. __('Select options', true) .'</label><div class="dropdown">';
						if( !empty ( $value['options'])){
							foreach( $value['options'] as $index => $vals) {
								$model_name = $vals['model'];
								$is_display = $vals['model_display'];
							?>
							<div class="wd-check-box wd-input wd-custom-checkbox">
								<label><input type="checkbox" data-model="<?php echo $model_name ?>" class="checkbox" data-model-display= '<?php echo $is_display; ?>' <?php if($is_display) echo 'checked="checked"';?> /><span class="wd-checkbox"></span><span class="status-name"><?php __d(sprintf($_domain, 'KPI'), $model_name); ?></span></label>
							</div>
						<?php }
						}
						echo '</div></div>';
					}if($value['widget'] == 'project_synthesis_budget'){
						echo '<div class="select"><label class="title">'. __('Select options', true) .'</label><div class="dropdown">';
						$options = array(
							'SynthesisBudget' =>  __('Synthesis Budget', true),
							'BudgetInternal' =>  __d(sprintf($_domain, 'Internal_Cost'), "Internal Cost", null),
							'BudgetExternal' =>  __d(sprintf($_domain, 'External_Cost'), "External Cost", null)
						);
						if( !empty ( $value['options'])){
							foreach( $value['options'] as $index => $vals) {
								$model_name = $vals['model'];
								$is_display = $vals['model_display'];
							?>
							<div class="wd-check-box wd-input wd-custom-checkbox">
								<label><input type="checkbox" data-model="<?php echo $model_name ?>" class="checkbox" data-model-display= '<?php echo $is_display; ?>' <?php if($is_display) echo 'checked="checked"';?> /><span class="wd-checkbox"></span><span class="status-name"><?php echo $options[$model_name] ?></span></label>
							</div>
						<?php }
						}
						echo '</div></div>';
					}
					if($value['widget'] == 'project_budget'){
						echo '<div class="select"><label class="title">'. __('Select options', true) .'</label><div class="dropdown">';
						$options = array(
							'inv' =>  __d(sprintf($_domain, 'Finance'), "Budget Investment", null),
							'fon' =>  __d(sprintf($_domain, 'Finance'), "Budget Operation", null),
							'finaninv' =>  __d(sprintf($_domain, 'Finance'), "Finance Investment", null),
							'finanfon' =>  __d(sprintf($_domain, 'Finance'), "Finance Operation", null)
						);
						if( !empty ( $value['options'])){
							foreach( $value['options'] as $index => $vals) {
								$model_name = $vals['model'];
								$is_display = $vals['model_display'];
							?>
							<div class="wd-check-box wd-input wd-custom-checkbox">
								<label><input type="checkbox" data-model="<?php echo $model_name ?>" class="checkbox" data-model-display= '<?php echo $is_display; ?>' <?php if($is_display) echo 'checked="checked"';?> /><span class="wd-checkbox"></span><span class="status-name"><?php echo $options[$model_name] ?></span></label>
							</div>
						<?php }
						}
						echo '</div></div>';
					}
					if($value['widget'] == 'project_risk' && !empty($issueStatus)){
						echo '<div class="select"><label class="title">'. __('Status', true) .'</label><div class="dropdown noOrdered">';
						$options = !empty($value['options']) ? Set::combine( $value['options'], '{n}.id', '{n}.display') : array();
						foreach ($issueStatus as $status_id => $name) {
							$is_display = isset($options[$status_id]) ? $options[$status_id] : 1;?>
							<div class="wd-check-box wd-input wd-custom-checkbox wd-custom-checkbox">
								<label>
									<input type="checkbox" data-status-id="<?php echo $status_id ?>" class="checkbox" data-status-display="<?php echo $is_display; ?>" <?php if($is_display) echo 'checked="checked"';?> />
									<span class="wd-checkbox"></span>
									<span class="status-name"><?php echo $name ?></span>
								</label>
							</div>
						<?php }
						echo '</div></div>';
					}
				?>
			</li>
			<?php endif; } ?>
        </ul>
    </div>
    <div class="wd-submit">
        <button onclick="submitSetting(this);return false;" class="btn-form-action btn-ok btn-right" id="btnSave">
            <span><?php echo __('Save', true); ?></span>
        </button>
        <a class="btn-form-action btn-cancel close" id="reset_button" href="javascript:void(0);">
                    <?php echo __('Cancel', true); ?>
        </a>
    </div>
</div>
    <?php }
} ?>
<div id="wd-container-main" class="wd-project-detail">
    <div id="chart-wrapper" class="wd-layout indidator-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"> 
                <div class="wd-panel wd-no-panel">
					<div class="wd-indidator-header clear-fix">
						<?php if(!empty($employee_logo_id)){
							$url_logo = $this->Html->url(array('controller' => 'customer_logos', 'action' => 'attachment', $employee_logo_id, '?' => array('sid' => $api_key)), true);
							echo '<div class="wd-customer-logo"><img src="'. $url_logo .'"/></div>';
						}?>
						<div class="wd-select-project-arm wd-select-project-name">
							<select id="select-project-name" class="select-project-arm select-project-name">
								<?php foreach( $list_projects as $_id => $project_name){
									$selected = ($_id == $project['Project']['id']) ? 'selected' : '';
									?>
									<option <?php echo $selected; ?> value="<?php echo $_id;?>"><?php echo $project_name;?></option>
								<?php } ?> 
							</select>
							<img class="gif-loading" src="/img/loading_check.gif" />
						</div>
						<div class="wd-input wd-weather-list">
							<?php
							$_disabled = !(($canModified && !$_isProfile) || $_canWrite ) ? 'disabled' : '';
							 $ex_pm = 0;
							 if(!empty($list_pm_project)) : ?>
							 <div class="wd-project-manager">
									<?php 
										$limit = 0;
										$ex_pm = count($list_pm_project) - 2;
										 foreach($list_pm_project as $id => $employee_name){
											 if($ex_pm > 0 && $limit == 2) echo '<div class="ex-project-manager-wrapper"> <div class="ex-project-manager"><span class="ex-number">+' .$ex_pm.'</span>';
											 ?>
												<p class="circle-name">
													<img width ="40" height="40" title="<?php echo $employee_name; ?>" src="<?php echo $this->Html->url('/img/avatar/' . $id . '.png') ?>" alt="avatar" />
												</p>
											 <?php 
											 $limit++; 
											 if($limit == count($list_pm_project) && $ex_pm > 0) echo '</div></div>';
										 }?>
								
							 </div>
							 <?php endif; ?>
							
							<div class="wd-weather">
								<ul>
									<?php
									$weathers = array( 'sun', 'cloud', 'rain');
									$ranks = array( 'up', 'mid', 'down');
									$weather = !empty( $project['ProjectAmr'][0]['weather'] ) ? $project['ProjectAmr'][0]['weather'] : $weathers[0];
									$rank = !empty( $project['ProjectAmr'][0]['rank'] ) ? $project['ProjectAmr'][0]['rank'] : $ranks[0];
									foreach($weathers as $_weather){?>
										<li class="<?php echo $weather == $_weather ? 'checked' : '';?>">
											<input type="radio" class="input_weather" name="data[ProjectAmr][weather][]" value="<?php echo $_weather;?>" <?php if( $_weather == $weather) echo 'checked="checked"';?> <?php echo $_disabled ;?> />
											<span class="icon-weather"><?php echo $svg_icons[$_weather]; ?></span>
										</li>
									<?php } ?> 
								</ul>
							</div>
							<div class="wd-weather">
								<ul>
								  
									<?php 
									foreach($ranks as $_rank){?>
										<li class="<?php echo $rank == $_rank ? 'checked' : '';?>">
											<input type="radio" class="input_weather" name="data[ProjectAmr][rank][]" value="<?php echo $_rank;?>" <?php if( $_rank == $rank) echo 'checked="checked"';?> <?php echo $_disabled ;?> />
											<img title="<?php __(ucfirst($_rank));?>"  src="<?php echo $html->url('/img/new-icon/project_rank/'.$_rank.'.png') ?>"  />
										</li>
									<?php } ?> 
								</ul>
							</div>
						</div>
						<div class="indidator-actions-right">
							<!-- Them checkPPM, neu user la Profile Project Manager thi hien thi button setting Dashboard. Ticket #395. QuanNV update 14/06/2019 -->
							<?php if($canModified || !empty($checkPPM['Employee']['profile_account']) || $read_only) { ?>
								<a href="javascript:void(0);" onclick="openLayoutSetting(this);"  title="<?php __('Setting')?>" class="btn-setting btn">
									<?php echo $svg_icons['setting']; ?>
								</a>
							<?php } ?>
							<a href="javascript: void(0);" title="<?php $has_favorite ? __('Remove favorite') : __('Add favorite');?>" class="btn project-favor <?php echo $has_favorite ? 'has-favor' : '';?>" id="project-favorite" onclick="toggleFavoriteProject(<?php echo $project_id ?>);">
								<?php echo $svg_icons['star'];?>
							</a>
							<a href="javascript:void(0);" class="btn btn-print btn-img-bgr hide-on-mobile" id="window-print" title="<?php __('Print')?>">
								<?php echo $svg_icons['print']; ?>
							</a> 
							<!-- <a href="javascript:exportPDF(); <?php //echo $html->url("/project_amrs_preview/exportExcel/" . $projectName['Project']['id']) ?>" class="btn export-excel-icon-all hide-on-mobile" id="export-submit" title="<?php __('Export Excel')?>"></a> -->
							<a href="javascript:void(0);" onclick="expandIndicator();" class="btn hide-on-mobile" id="expand">
								<?php echo $svg_icons['expand']; ?>
							</a>
							<a href="javascript:void(0);" class="btn btn-table-collapse" id="table-collapse" onclick="collapseIndicator();" title="Collapse" style="display: none;">
								<img src="/img/new-icon/close.png" class="img center"/>
								<img src="/img/new-icon/close-blue.png" class="img center"/>
								
							</a>
						</div>
						<div class="wd-header-total-value">
							<?php 
							// ob_clean();
							// debug($adminTaskSetting); exit;
							// $showProgress = 1;
							// if( !empty($companyConfigs['project_progress_method']) && ($companyConfigs['project_progress_method'] == 'no_progress') ) $showProgress = 0; 
							// if( 
								// (empty($companyConfigs['project_progress_method']) || ( $companyConfigs['project_progress_method'] == 'consumed')) 
								// && empty($adminTaskSetting['Consumed']) 
								// && empty($adminTaskSetting['Manual Consumed'] )
							// ){
								// $showProgress = 0; 
							// }
							if(!( empty($showProgress) && empty($phase_plan_end_date) && empty($phase_real_end_date) )){?>
							
								<div class="wd-header-value wd-header-progress">
									<div class="wd-value-inner wd-progress-inner loading-mark">
										<div class="wd-value-content wd-phase-planed-end-date">
											<?php if( !empty( $phase_plan_end_date)){?>
												<p><?php __('Plan end date');?></p>
												<span><?php echo date('d-m-Y',$phase_plan_end_date);?></span>
											<?php } ?> 
										</div>
										<?php if( $project_progress !== false ){?>
											<div class="wd-value-content wd-project-progress">
												<?php echo draw_line_progress($project_progress); ?>
											</div>
										<?php } ?> 
										<div class="wd-value-content wd-phase-real-end-date">
											<?php if( !empty( $phase_real_end_date)){?>
												<p><?php __('Real end date');?></p>
												<span><?php echo date('d-m-Y',$phase_real_end_date);?></span>
												<?php $date_diff = floor(($phase_real_end_date - $phase_plan_end_date)/(60*60*24));
												$phase_class = ($date_diff > 0) ? 'budget-red' : 'budget-green';?>
												<span class="wd-value-percent <?php echo $phase_class;?>">
													<?php echo $date_diff; __('d' );?>
												</span>
											<?php } ?> 
										</div>
										
										
									</div>
								</div>
							<?php }
							$widget_settings_display = !empty($list_widgets) ? Set::combine($list_widgets, '{n}.widget', '{n}.display') : array();

							 if(!empty($list_widgets['project_milestones']) && $list_widgets['project_milestones']['display'] && (!empty($miles_first_late) || !empty($miles_next_future))){ ?>
							 <div class="wd-header-value wd-header-milestone">
								<div class="wd-value-inner wd-milestone-inner">
									<?php if(!empty($miles_first_late)){ ?>
										<div class="wd-value-content wd-milestone-date-late">
											<p><?php echo __('Late milestone');?></p>
											<span class="milestone-red"><?php echo $miles_first_late['project_milestone'];?> <?php echo date('d/m/Y', strtotime($miles_first_late['milestone_date']));?></span> 	
										</div>
									<?php } ?>
									<?php if(!empty($miles_next_future)){ ?>
										<div class="wd-value-content wd-milestone-future-date">
											<p><?php echo __('Next milestone');?></p> 
											<span><?php echo $miles_next_future['project_milestone'];?> <?php echo date('d/m/Y', strtotime($miles_next_future['milestone_date']));?></span> 	
										</div>
									<?php } ?>
								</div>
							 </div>
							 <?php } 
							if(!empty($list_widgets['project_synthesis_budget']) && $list_widgets['project_synthesis_budget']['display'] && !empty($projectBudgetSyn)){?>
							 
							 <div class="wd-header-value wd-header-budget">
								<div class="wd-value-inner wd-budget-inner">
									<?php 
								    $budget_syns = !empty($projectBudgetSyn) ? $projectBudgetSyn['ProjectBudgetSyn'] : array();
									$internal_costs_budget = !empty($budget_syns['internal_costs_budget']) ? $budget_syns['internal_costs_budget'] : 0;
									$internal_costs_forecast = !empty($budget_syns['internal_costs_forecast']) ? $budget_syns['internal_costs_forecast'] : 0;
									$external_costs_budget = !empty($budget_syns['external_costs_budget']) ? $budget_syns['external_costs_budget'] : 0;
									$external_costs_forecast = !empty($budget_syns['external_costs_forecast']) ? $budget_syns['external_costs_forecast'] : 0;
									
									$totalBudgetProgress = (float)$internal_costs_budget + (float)$external_costs_budget;
									$totalForecastProgress = (float)$internal_costs_forecast + (float)$external_costs_forecast;
								
									if($totalBudgetProgress == 0) {
										$per_budget_syns = 100;
									} else {
										$per_budget_syns = round($totalForecastProgress/$totalBudgetProgress * 100);
									} 
									$budget_class = '';
									if( $totalForecastProgress > $totalBudgetProgress) $budget_class = 'budget-red';
									?>
									<div class="wd-value-content wd-budget-total">
										<p><?php echo __d(sprintf($_domain, 'Total_Cost'), 'Budget €', true); ?></p>
										<span><?php echo number_format($totalBudgetProgress, 2, '.', ' '); ?> <?php echo ' '.$bg_currency; ?> </span> 	
									</div>
								
							
									<div class="wd-value-content wd-total-Forecast">
										<p><?php  echo __d(sprintf($_domain, 'Total_Cost'), 'Forecast €', true);?></p> 
										<span><?php echo number_format($totalForecastProgress, 2, '.', ' '); ?> <?php echo ' '.$bg_currency; ?> </span><span class="wd-value-percent <?php echo $budget_class;?>"><?php echo round($per_budget_syns) . '%'; ?></span> 	
									</div>
								</div>
							</div>
							<?php } 
							if(!empty($list_widgets['project_budget']) && $list_widgets['project_budget']['display'] && $totals['inv']){
								if(empty($totals['inv']['budget'])){
									$totals['inv']['budget'] = 0;
								}
								if(empty($totals['inv']['avancement'])){
									$totals['inv']['avancement'] = 0;
								}
								if($totals['inv']['budget'] == 0) {
									$per_inv = 100;
								} else {
									$per_inv = round($totals['inv']['avancement']/$totals['inv']['budget'] * 100);
								}
								$inv_class = '';
								if( $totals['inv']['avancement'] > $totals['inv']['budget'] ) $inv_class = 'budget-red';
							  ?>
							<div class="wd-header-value wd-header-finance">
								<div class="wd-value-inner wd-finance-inner">
									<?php if(isset($totals['inv']['budget'])){ ?>
										<div class="wd-value-content wd-finance-budget">
											<p><?php echo __d(sprintf($_domain, 'Budget_Investment'), 'Budget', true); ?></p>
											<span><?php echo number_format((!empty($totals['inv']['budget']) ? $totals['inv']['budget'] : 0), 2, '.', ' '); ?> <?php echo ' '.$bg_currency; ?> </span> 	
										</div>
									<?php } ?>
									<?php if(isset($totals['inv']['avancement'])){ ?>
										<div class="wd-value-content wd-finance-avancement">
											<p><?php echo __d(sprintf($_domain, 'Budget_Investment'), 'Engaged');?></p> 
											<span><?php echo number_format((!empty($totals['inv']['avancement']) ? $totals['inv']['avancement'] : 0), 2, '.', ' '); ?> <?php echo ' '.$bg_currency; ?> </span><span class="wd-value-percent <?php echo $inv_class;?>"><?php echo round($per_inv) . '%'; ?></span> 	
										</div>
									<?php } ?>
								</div>
							</div>
							<?php } ?>
						</div>
					</div>
                    <?php wd_layout_setting($this, $list_widgets); 
					
                    //tat ca widget nam trong views/elements/widgets/
                    //mobile version se co dang: mkpi-ten_widget
                    /*
                    =======Cach them moi widget========
                    1. kpi_settings_controller
                        ::get
                            $default
                                ten_widget|01
                                //0 = hide
                                //1 = show
                    2. tao file widget
                    Tat ca code css, js cua widget nen cho vao file widget luon, ko nen de o day
                    */
                    ?>
                    <div class="indicator-layout">
                        <?php  
                        $i = $flag = 0;

                        // default
                        // init
                        $start = '<div class="wd-row">';
                        $end = '</div>';
						$list_all_widget = array_values($list_widgets);
						$list_all_widget = !empty($list_all_widget) ? Set::sort($list_all_widget, '{n}.row', 'asc') : array();
                        foreach ($list_all_widget as $key => $value) {
                            $row = $value['row'];
                            $display = $value['display'];
                            $col = $value['col'];
                            $sizex = $value['sizex'];
                            $sizey = $value['sizey'];
                            $widget_id = $value['widget'];
                            $file = 'widgets'.DS.$value['widget'];
							$class = 'wd-col';
                            if($display == 1){
                                $class .= ($sizex == 2) ? ' wd-col-md-12 ' : ' wd-col-md-6 ';
                                $class .= ($col == 2) ? ' align-right' : '';
                                if($sizex == 2) echo $start;
                                else if($sizex == 1 && $flag == 0){
                                    echo $start;
                                } $flag++;
                                
                                ?>
                        <div class="<?php echo $class; ?>" data-widget= "<?php echo $widget_id; ?>" data-row="<?php echo $row; ?>" data-col="<?php echo $col; ?>" data-sizex="<?php echo $sizex; ?>" data-sizey="<?php echo $sizey; ?>">
                                        <?php if( file_exists(ELEMENTS . DS . $file . '.ctp') ){
											$options = array(
                                                'type' => $type,
                                                'widget_sizex' => $sizex
                                            );
											if( isset( $value['options'])) $options['options'] = $value['options'];
                                            echo $this->element($file, $options);
                                        } ?>
                        </div>
                                <?php
                                if((!empty($list_all_widget[$key + 1]['sizex']) && $list_all_widget[$key + 1]['sizex'] == 2) || $sizex == 2 || $flag == 2) {
                                    echo $end;
                                    $flag = 0;
                                }
                                if($sizex == 1 && empty($list_all_widget[$key + 1]['sizex'])) { echo $end; $flag = 0;}
                            }
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
echo $this->element('dialog_projects');
echo $html->script('jquery.ba-bbq.min');
echo $html->script('jquery.multiSelect');

?>

<style type="text/css">
    .setvalidation{
        border-color: red !important;
    }
</style>

<script language="javascript">
    var canModified = <?php echo json_encode($canModified) ?>;
	var body_overflow = $('body').css('overflow');
	var html_overflow = $('html').css('overflow');
	var isFull = false;
	HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
	function widget_after_expand(){
		$('body', 'html').css('overflow', 'hidden');
	}
	function widget_after_collapse(){
		$('body').css('overflow', body_overflow);
		$('html').css('overflow', html_overflow);
	}
	$('#select-project-name').multiSelect({
		selectAll: false,
		noneSelected: '<?php __('Project');?>',
		oneOrMoreSelected: '*',
		loadingClass : 'loading',
		loadingText : 'Loading...',
		placeholder : '<?php __('Search');?>',
		oneSelected: true,
		noHistory: true,
		// appendTo: 'body'
	}, function(input_element){
		parent_select = input_element.closest('.wd-select-project-name');
		parent_select.addClass('loading');
		project_id = input_element.val();
		if(project_id){
			window.location.href = "/project_amrs_preview/indicator/"+ project_id;
		}
	});
	<?php if( !empty($project['ProjectAmrProgram']['amr_program'])){ ?>
		$('#select-project-name').addClass('has-program').prepend("<p class='program-name' title='<?php echo ucfirst(strtolower(htmlspecialchars($project['ProjectAmrProgram']['amr_program'], ENT_QUOTES)));?>'><?php echo ucfirst(strtolower(htmlspecialchars($project['ProjectAmrProgram']['amr_program'])));?></p>");
	<?php } ?>	if(canModified){
		$('#select-project-program').multiSelect({
			selectAll: false,
			noneSelected: '<?php __('Program');?>',
			oneOrMoreSelected: '*',
			loadingClass : 'loading',
			loadingText : 'Loading...',
			placeholder : '<?php __('Search');?>',
			oneSelected: true,
		}, function(input_element){
			if(!canModified) return;
			parent_select = input_element.closest('.wd-project-program');
			parent_select.addClass('loading');
			$.ajax({
				url: '/projects/saveFieldYourForm/' + <?php echo $project_id ?>,
				type: 'POST',
				data: {
					'field' : 'project_amr_program_id',
					'value' : input_element.val(),
				},
				success:function() {

					parent_select.removeClass('loading');
				}
			});
		});
	}
    function expandIndicator() {
        // $('.wd-panel').addClass('treeExpand');
		$('#wd-container-header-main').hide();
		// $('#layout').addClass('widget-expand');
        $('#table-collapse').show();
        $('#expand').hide();
        isFull = true;
        $(window).trigger('resize');
    }

    function collapseIndicator() {
        $('#table-collapse').hide();
        $('#expand').show();
        // $('.wd-panel').removeClass('treeExpand');
		$('#wd-container-header-main').show();
		isFull = false;
        $(window).trigger('resize');
    }
    function submitSetting(_this) {
		if($(_this).hasClass('loading')) return;
		$(_this).addClass('loading');
        var _wid_item = $('#layout-setting').find('li');
        var _data = {};
        i = 0;
        _wid_item.each(function () {
            _row = $(this).data('row');
            _col = $(this).data('col');
            _sizex = $(this).data('sizex');
            _sizey = $(this).data('sizey');
            _widget = $(this).data('widget');
            _display = $(this).find('a').data('display');
			_data[i] = {
				widget: _widget,
				row: _row,
				col: _col,
				sizex: _sizex,
				sizey: _sizey,
				display: _display,
			};
            if (_widget == 'project_task') {
                var _checkbox = $(this).find('.wd-check-box');
				var count_default = 0;
				var first_enable = -1;
				var j = 0;
				_data[i]['task_status'] = {};
                _checkbox.each(function(){
                    status_id = $(this).find(':checkbox:first').data('status-id');
                    status_display = ( $(this).find(':checkbox:first').is(':checked') ? 1 : 0);
					if((first_enable == -1) && status_display) first_enable = j;
                    status_default = ( $(this).find('.checkbox-2').is(':checked') ? 1 : 0);
					count_default += status_default;
					_data[i]['task_status'][j] = {
						status_id: status_id,
						status_display: status_display,
						'default': status_default
					};
					j++;
                });
				if( !count_default && (first_enable != -1)) _data[i]['task_status'][first_enable]['default'] = 1;
            }else if (_widget == 'project_risk') {
                var _checkbox = $(this).find(':checkbox');
				if( _checkbox.length){
					var j = 0;
					_data[i]['options'] = {};
					_checkbox.each(function(){
						var _this = $(this);
						var id = _this.data('status-id');
						var is_display = ( _this.is(':checked') ? 1 : 0);
						_data[i]['options'][j++] = {
							id: id,
							display: is_display,
						}
					});
				}
               
            }else {
                var _checkbox = $(this).find('.wd-check-box');
                if (_checkbox.length) {
					var j = 0;
					_data[i]['options'] = {};
                    _checkbox.each(function () {
                        var model = $(this).find('input').data('model');
                        var model_display = ( $(this).find(':checkbox:first').is(':checked') ? 1 : 0);
                       _data[i]['options'][j++] = {
							model: model,
							model_display: model_display,
						}
                    });
                }
            }
            i++;
        });
        $.ajax({
            url: '/project_amrs_preview/save_layout_setting/' + project_id,
            type: 'POST',
            data: {data: _data},
            dataType: 'JSON',
            success: function (response) {
                if (response) {
                    location.reload();
                }
            },
            complete: function () {

            }
        });
    }
    $(".input_weather").on('change', function () {
		if( !$(this).is(':checked')) return;
        var field = $(this).attr('name');
        var value = $(this).val();
        _parent = $(this).closest('ul');
		// _parent.addClass('loading');
        _item = $(this).closest('li');
        $.ajax({
            url: '/project_amrs_preview/updateWeather/',
            type: 'POST',
            data: {
                data: {
                    project_id: <?php echo $project_id ?>,
                    field: field,
                    value: value
                }
            },
            dataType: 'JSON',
            success: function (response) {
                if (response == 1) {
                    _parent.find('li').removeClass('checked');
                    _item.addClass('checked');
					// _parent.removeClass('loading');
                }
            },
        });
    });

    function displayWidget(_this) {
        _display = $(_this).data('display');
        if (_display == 1) {
            _display = 0;
            $(_this).closest('li').addClass('disabled');
        } else {
            _display = 1;
            $(_this).closest('li').removeClass('disabled');
        }
        $(_this).data('display', _display);
        // $(_this).attr('data-display',_display);

    }

    var ly_setting;
    timeout = 0;
    function openLayoutSetting(_this) {
        $('.wd-layout-setting').toggleClass('open');
        if ($('.wd-layout-setting').hasClass('open')) {
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                $('.wd-panel').css('min-height', $('.wd-layout-setting.open').height() + 40);
            }, 600);
        } else {
            clearTimeout(timeout);
            $('.wd-panel').css('min-height', '');
        }
        ;
        ly_setting = $(".layout-setting ul").gridster({
            namespace: '#layout-setting',
            widget_base_dimensions: [290, 140],
            widget_margins: [10, 10],
            cols: 2,
            max_cols: 2,
            resize: {
                enabled: true
            }
        }).data('gridster');
    }
    $(document).ready(function () {
        var gridster;
        _wd_base_width = Math.round(($('#indicator-layout').width() - 50) / 2);
        gridster = $(".indicator-layout > ul").gridster({
            namespace: '#indicator-layout',
            widget_base_dimensions: ['auto', 172],
            widget_margins: [20, 20],
            cols: 2,
            max_cols: 2,
            resize: {enabled: false},
           draggable: true,
        }).data('gridster');
        setTimeout(function () {
            var _widget_item = $('#indicator-layout').find('li.gs-w');
            _widget_item.each(function () {
                _wd_height = $(this).find('.wd-widget');
                _height = 0;
                _wd_height.each(function () {
                    _height += $(this).height();
                });
                _wd_width = $(this).width();
                _wd_row = $(this).data('row');
                if (_height) {
                    gridster.fit_to_content_width_responsive($(this), _wd_width, _height, _wd_row);
                }
            });
        }, 3000);

        $('.wd-weather-list li').click(function () {
            $(this).find('.input_weather').prop('checked', true).trigger('change');
        });


        $('.close').click(function () {
            $('.wd-layout-setting').removeClass('open');
            clearTimeout(timeout);
            $('.wd-panel').css('min-height', '');
        });
        $('body').click(function (e) {
            if (!($(e.target).hasClass('btn-setting') || $('.btn-setting').find(e.target).length || $(e.target).hasClass('wd-layout-setting') || $('.wd-layout-setting').find(e.target).length)) {
                $('.wd-layout-setting').removeClass('open');
                clearTimeout(timeout);
                $('.wd-panel').css('min-height', '');
            }
        });
        $('.dropdown').find(':checkbox').on('change', function () {
            var _this = $(this);
			if (_this.is(':checked')) {
                _this.data('status-display', 1);
                _this.data('status-default', 1);
                _this.data('model-display', 1);
            }else{
                _this.data('status-display', 0);
                _this.data('status-default', 0);
                _this.data('model-display', 0);
            }
			var _default = _this.closest('.wd-check-box').find('.checkbox-2');
			if(_default.length && _this.hasClass('checkbox')){
				if(_this.is(':checked')){
					_default.prop('disabled', false);
				}else{
					_default.data('status-default', 0).prop('checked', false).prop('disabled', true);
				}
			}
        });
		$('#select-project-name').on('click', 'i.selected-item', function(e){
			e.preventDefault();
			return;
		});
		$('#layout-setting .dropdown:not(.noOrdered)').sortable();
		$('#layout-setting .dropdown:not(.noOrdered)').bind('mousedown',function() {
			ly_setting.disable();
		});
		$('#layout-setting .dropdown:not(.noOrdered)').bind('mousemove',function() {
			ly_setting.enable();
		});
    });
	$('#window-print').on('click', print_indicator);
	function print_indicator(){
		wdConfirmIt({
			title: <?php echo json_encode( __('Print this page', true).'?');?>,
			content: <?php echo json_encode( __('Only available with Chrome and Firefox.', true).' <br> '. __('Please select "print background and pictures"', true));?>,
			buttonModel: 'WD_TWO_BUTTON',
			buttonText: [
				'<?php __('Yes');?>',
				'<?php __('No');?>'
			],
		},function(){
			window.print();
		});
	}
    function exportPDF() {
        var _style = '.dragger_container, .wd-map-area iframe, #wd-container-header,.add-message,.log-addnew,.log-field-edit,.menu-zog,.openMenu,.scroll-progress,.slick-slider button,.ui-resizable-handle,.wd-comment-form,.wd-gantt-expand,.wd-layout .wd-content-left,.wd-panel>.wd-title,.wd-panel>.wd-weather-list,.wd-widget .add-new-item,.wd-widget .image-expand-btn,.wg-cv-collapse,.widget-title .widget-action>a,.widget-title>a {display:none !important;} .indicator-layout{padding-top: 300px; max-width: 1320px;} .wd-map-area .imgprinter{display: block;}';
        $('#indicator-inline-css').html(_style);
        $('.indicator-layout').indicator2canvas();
    }
	//QuanNV update ticket 457
	if($(".heading-weather").length != 0){
		$(".heading-weather").addClass('hidden');
	}
	$('.select-project-name .drd-project-name').on('click','span',  function(e){
		project_id = $(this).data('id');
		// console.log(project_id);
		if(project_id){
			window.location.href = "/project_amrs_preview/indicator/"+ project_id;
		}
	});
	$('.ex-project-manager').on('hover',  function(e){
		ex_pm = <?php echo $ex_pm; ?>;
		console.log(ex_pm);
		if(ex_pm > 0) $('.ex-project-manager').width(ex_pm * 38);
	}).on('mouseleave', function(){
		$(this).width(40);
	});
	<?php if( !empty($companyConfigs['project_progress_method']) && $companyConfigs['project_progress_method']=='manual' && $canModified){?>
		$('.wd-header-total-value').on('click', '.wd-progress-number .text', function(){
			var _this = $(this);
			_this.hide();
			_this.siblings('.input-progress').removeClass('wd-hide').focus();			
		});
		function saveManualProgress(elm){
			var _this = $(elm);
			if(!_this.hasClass('input-progress')) {
				_this = _this.find('.input-progress');
			}
			var val = _this.val();
			var project_item = _this.closest('.wd-header-value');
			var project_id = <?php echo json_encode($project_id);?>;
			project_item.find('.loading-mark').addClass('loading');
			$.ajax({
				url: '/projects_preview/saveFieldYourForm/' + project_id,
				type: 'post',
				dataType: 'json',
				data: {
					field: 'manual_progress',
					value: val
				},
				beforeSent: function(){
					project_item.find('.loading-mark').addClass('loading');
				},
				success: function(res){
					if( res.result == 'success'){
						displayProgrressVal(_this.closest('.wd-progress-slider'), parseFloat( res.data.Project.manual_progress ), true);
						_this.blur(); 
					}else{;
						location.reload();
					}
				},
				error: function(){
					location.reload();
				},
				complete: function(){
					project_item.find('.loading-mark').removeClass('loading');
				}
			});
		}
		function progressHideInput(elm){
			var _this = $(elm).parent();
			_this.find('.text').show();
			_this.find('.input-progress').addClass('wd-hide');
		}
		$('.wd-progress-slider').find('.wd-progress-number').draggable({
			axis: 'x',
			opacity: 0.6,
			cursor: 'pointer',
			drag: function( event, ui ) {
				var container = ui.helper.closest('.wd-progress-slider');
				var _w = ui.helper.width() + parseInt(ui.helper.css('border-left-width')) + parseInt(ui.helper.css('border-right-width'));
				var max_w = container.width() - _w;
				ui.position.left = Math.max( 0, Math.min( container.width() - _w, ui.position.left ));
				var val = parseInt(ui.position.left / max_w * 100);
				displayProgrressVal(container, val);
			},
			stop: function( event, ui ) {
				saveManualProgress(ui.helper[0]);
			}
		});
		function displayProgrressVal(container, val, is_animation){
			var v = parseInt( Math.max(0, Math.min(val, 100)));
			var css_class= '';
			if( val > 100) css_class='red-line';
			if( is_animation){
				container.find('.wd-progress-value-line, .wd-progress-number').addClass('ease');
			}
			container.removeClass('red-line').addClass(css_class);
			container.data('value', val);
			container.find('.wd-progress-value-line').width(v + '%');
			container.find('.wd-progress-number').css('left', v + '%');
			container.find('.wd-progress-number .text').text(v + '%');
			container.find('.input-progress').val(val);
			setTimeout( function(){
				container.find('.wd-progress-value-line, .wd-progress-number').removeClass('ease');
			}, 400);
		}
	<?php } ?> 
	
	function toggleFavoriteProject(project_id){
		var project_item = $('#project-favorite');
		$.ajax({
			url: '/projects/toggleFavoriteProject/' + project_id,
			type: 'get',
			dataType: 'json',
			beforeSend: function(){
				project_item.find('.loading-mark').addClass('loading');
			},
			success: function(res){
				if( res.result == 'success'){
					if( res.favorite){
						project_item.addClass('has-favor');
					}else{
						project_item.removeClass('has-favor');
					}
				}else{;
					location.reload();
				}
			},
			error: function(){
				location.reload();
			},
			complete: function(){
				project_item.find('.loading-mark').removeClass('loading');
			}
		});
	}
</script>

<div id="overlay-container">
    <div id="overlay-wrapper"></div>
    <div id="overlay-box">
        <?php echo __('Please wait, Preparing export ...', true); ?>
    </div>
</div>
<style id="indicator-inline-css"></style>