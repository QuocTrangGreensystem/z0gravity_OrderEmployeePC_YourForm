<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->script('jquery.form'); ?>
<?php echo $html->script('history_filter'); ?>
<?php echo $html->css('slick_grid/slick.grid_v2'); ?>
<?php echo $html->css('slick_grid/slick.pager'); ?>
<?php echo $html->css('slick_grid/slick.common_v2'); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
<?php echo $html->css('preview/layout'); ?>
<?php echo $html->css('preview/slickgrid'); ?>
<?php echo $html->css('preview/employee'); ?>
<style>
	#total-container {
		max-width: 1890px;
		margin-left: auto;
		margin-right: auto;
	}
	.total_item {
		width: 196px;
		display: inline-block;
		padding-bottom: 10px;
		padding-top: 10px;
		position: relative;
		box-sizing: border-box;
		vertical-align: top;
	}
	.slides {
		width: 100%;
		margin-bottom: 30px;
		background: #fff;
		text-align: center;
	}
	.total_value > span {
		line-height: 60px;
		font-size: 20px;
		z-index: 100;
		color: #6EAF79;
	}
	.total_item .total_value {
		width: 60px;
		height: 60px;
		border: 1px solid #6EAF79;
		color: #6EAF79;
		background-color: #fff;
		cursor: unset;
		border-radius: 50%;
		margin: auto;
		z-index: 2;
		display: inline-block;
		position: relative;
	}
	.line_left {
		height: 2px;
		background-color: #6EAF79;
		width: 102px;
		display: inline-block;
		float: left;
		position: absolute;
		left: 0;
		top: 40px;
		z-index: 1;
	}
	.line_right {
		float: right;
		height: 2px;
		background-color: #6EAF79;
		width: 102px;
		display: inline-block;
		position: absolute;
		top: 40px;
		margin-right: 0px;
	}
	.total_item .total_item_small > p{
		margin-top: 7px;
	}
	.total_item:first-child .line_left{
		display: none;
	}
	.total_item:last-child .line_right{
		display: none;
	}
</style>

<script type="text/javascript">
    HistoryFilter.here = '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url = '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>

<?php echo $this->element('dialog_projects');
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
	'slick_grid/plugins/slick.dataexporter',
	'slick_grid_custom',
	'slick_grid/slick.grid.origin',
	'slick_grid/slick.grid.activity',
)); ?>
<?php 
// ob_clean();

if(!empty($_GET['view']) && $_GET['view']==2){

    $columns = array(
		'no.' => array(
			'id' => 'no.',
			'field' => 'no.',
			'name' => '#',
			'width' => 40,
			'sortable' => true,
			'resizable' => false
		),
		'first_name' => array(
			'id' => 'first_name',
			'field' => 'first_name',
			'name' => __('First name', true),
			'width' => 150,
			'sortable' => true,
			'resizable' => true,
			'formatter' => 'linkFormatter'  //16/10/2013 huythang

		),
		'last_name' => array(
			'id' => 'last_name',
			'field' => 'last_name',
			'name' => __('Last name', true),
			'width' => 150,
			'sortable' => true,
			'resizable' => true,
			'formatter' => 'linkFormatter'  //16/10/2013 huythang

		),
		'email' => array(
			'id' => 'email',
			'field' => 'email',
			'name' => __('Email', true),
			'width' => 200,
			'sortable' => true,
			'resizable' => true,

		),
		'profit_center_id' => array(
			'id' => 'profit_center_id',
			'field' => 'profit_center_id',
			'name' => __('Profit Center', true),
			'width' => 150,
			'sortable' => true,
			'resizable' => true
		),
		'role_id' => array(
			'id' => 'role_id',
			'field' => 'role_id',
			'name' => __('Role', true),
			'width' => 150,
			'sortable' => true,
			'resizable' => true
		),
		'identifiant' => array(
			'id' => 'identifiant',
			'field' => 'identifiant',
			'name' => __('Identifiant', true),
			'width' => 150,
			'sortable' => true,
			'resizable' => true
		),
		'actif' => array(
			'id' => 'actif',
			'field' => 'actif',
			'name' => __('Actif', true),
			'width' => 100,
			'sortable' => true,
			'resizable' => true,
			'formatter' => 'Slick.Formatters.switchButton'
		),
		'sl_budget' => array(
			'id' => 'sl_budget',
			'field' => 'sl_budget',
			'name' => __('Budget', true),
			'width' => 150,
			'sortable' => true,
			'resizable' => true,
			'cssClass' => 'sl-budget',
			'editor' => 'Slick.Editors.singleSelectBox',
			'formatter' => 'Slick.Formatters.slBuget'
		),
		'update_your_form' => array(
			'id' => 'update_your_form',
			'field' => 'update_your_form',
			'name' => __('Update Your form', true),
			'width' => 100,
			'sortable' => true,
			'resizable' => true,
			'formatter' => 'Slick.Formatters.switchButton'
		),
		'control_resource' => array(
			'id' => 'control_resource',
			'field' => 'control_resource',
			'name' => __('Allow managing resources?', true),
			'width' => 100,
			'sortable' => true,
			'resizable' => true,
			'formatter' => 'Slick.Formatters.switchButton'
		),
		'create_a_project' => array(
			'id' => 'create_a_project',
			'field' => 'create_a_project',
			'name' => __('Create a project', true),
			'width' => 150,
			'sortable' => true,
			'resizable' => true,
			'formatter' => 'Slick.Formatters.switchButton'
		),
		'delete_a_project' => array(
			'id' => 'delete_a_project',
			'field' => 'delete_a_project',
			'name' => __('Delete a project', true),
			'width' => 100,
			'sortable' => true,
			'resizable' => true,
			'formatter' => 'Slick.Formatters.switchButton'
		),
		'change_status_project' => array(
			'id' => 'change_status_project',
			'field' => 'change_status_project',
			'name' => __('Can change the status opportunity/in progress', true),
			'width' => 150,
			'sortable' => true,
			'resizable' => true,
			'formatter' => 'Slick.Formatters.switchButton'
		),
		'external' => array(
			'id' => 'external',
			'field' => 'external',
			'name' => __('External', true),
			'width' => 100,
			'sortable' => true,
			'resizable' => true,
			'formatter' => 'Slick.Formatters.switchButton'
		),
	);
	if( !empty($enabled_menus) && !empty($enabled_menus['communications'])){
		$columns['can_communication'] =  array(
			'id' => 'can_communication',
			'field' => 'can_communication',
			'name' => __('Communication', true),
			'width' => 100,
			'sortable' => true,
			'resizable' => true,
			'formatter' => 'Slick.Formatters.switchButton'
		);
	}
	if(isset($companyConfigs['display_activity_forecast']) && $companyConfigs['display_activity_forecast']){
		$columns['can_see_forecast'] =  array(
			'id' => 'can_see_forecast',
			'field' => 'can_see_forecast',
			'name' => __('Forecasts', true),
			'width' => 100,
			'sortable' => true,
			'resizable' => true,
			'formatter' => 'Slick.Formatters.switchButton'
		);
	}
	$columns['z0g_id'] =  array(
		'id' => 'z0g_id',
		'field' => 'z0g_id',
		'name' => __('Z0G ID', true),
		'width' => 100,
		'sortable' => true,
		'resizable' => true,
	);
	$columns['action.'] = array(
		'id' => 'action.',
		'field' => 'action.',
		'name' => __('Action', true),
		'width' => 85,
		'sortable' => false,
		'resizable' => false,
		'formatter' => 'Slick.Formatters.HTMLData',
		'ignoreExport' => true
	 );
} elseif (!empty($_GET['view']) && $_GET['view']==3) {
    $columns = array(
    array('id' => 'no.', 'field' => 'no.', 'name' => '#','width' => 40,'sortable' => true, 'resizable' => false),
    array('id' => 'first_name','field' => 'first_name', 'name' => __('First name', true), 'width' => 200, 'sortable' => true,'resizable' => true,'formatter' => 'linkFormatter'),
    array('id' => 'last_name', 'field' => 'last_name','name' => __('Last name', true), 'width' => 200,'sortable' => true, 'resizable' => true,'formatter' => 'linkFormatter'),
    array('id' => 'email','field' => 'email','name' => __('Email', true),'width' => 200,'sortable' => true,'resizable' => true),
    array('id' => 'identifiant','field' => 'identifiant','name' => __('Identifiant', true),'width' => 150,'sortable' => true,'resizable' => true),
    array('id' => 'role_id','field' => 'role_id','name' => __('Role', true),'width' => 150,'sortable' => true,'resizable' => true),
	array('id' => 'email_receive','field' => 'email_receive','name' => __('Authorize z0 Gravity email', true),'width' => 200,'sortable' => true, 'resizable' => true,
		'formatter' => 'Slick.Formatters.switchButton'),
    array('id' => 'activate_copy', 'field' => 'activate_copy','name' => __('Copy Forecast', true),'width' => 200,'sortable' => true,'resizable' => true,
		'formatter' => 'Slick.Formatters.switchButton' ),
    array('id' => 'auto_timesheet','field' => 'auto_timesheet','name' => __('Auto validate timesheet', true),'width' => 200,'sortable' => true,'resizable' => true,
		'formatter' => 'Slick.Formatters.switchButton'),
    array('id' => 'auto_absence','field' => 'auto_absence','name' => __('Auto validate absence', true),'width' => 200,'sortable' => true,'resizable' => true,
		'formatter' => 'Slick.Formatters.switchButton'),
    array('id' => 'auto_by_himself','field' => 'auto_by_himself', 'name' => __('Auto validate by himself', true),'width' => 200,'sortable' => true, 'resizable' => true,
		'formatter' => 'Slick.Formatters.switchButton'),
	array('id' => 'z0g_id','field' => 'z0g_id','name' => __('Z0G ID', true),'width' => 100,'sortable' => true,'resizable' => true),
    array('id' => 'action.','field' => 'action.','name' => __('Action', true),'width' => 85,'sortable' => false,'resizable' => false,'formatter' => 'Slick.Formatters.HTMLData','ignoreExport' => true)
        );
} else {
    
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
			'id' => 'fullname',
			'field' => 'fullname',
			'name' => __('Fullname', true),
			'width' => 200,
			'sortable' => true,
			'resizable' => true,
			'formatter' => 'linkFormatter'  //16/10/2013 huythang

		),
		array(
			'id' => 'profit_center_id',
			'field' => 'profit_center_id',
			'name' => __('Profit Center', true),
			'width' => 150,
			'sortable' => true,
			'resizable' => true
		),
		array(
			'id' => 'code_id',
			'field' => 'code_id',
			'name' => __('ID', true),
			'width' => 150,
			'sortable' => true,
			'resizable' => true
		),
		array(
			'id' => 'role_id',
			'field' => 'role_id',
			'name' => __('Role', true),
			'width' => 150,
			'sortable' => true,
			'resizable' => true,
			'formatter' => 'Slick.Formatters.selectBox',
		),
		array(
			'id' => 'email',
			'field' => 'email',
			'name' => __('Email', true),
			'width' => 150,
			'sortable' => true,
			'resizable' => true
		),

		array(
			'id' => 'city_id',
			'field' => 'city_id',
			'name' => __('City', true),
			'width' => 150,
			'sortable' => true,
			'resizable' => true
		),
		array(
			'id' => 'identifiant',
			'field' => 'identifiant',
			'name' => __d(sprintf($_domain, 'Resource'), 'ID2', true),
			'width' => 150,
			'sortable' => true,
			'resizable' => true
		),
		array(
			'id' => 'id3',
			'field' => 'id3',
			'name' => __d(sprintf($_domain, 'Resource'), 'ID3', true),
			'width' => 150,
			'sortable' => true,
			'resizable' => true
		),
		array(
			'id' => 'id4',
			'field' => 'id4',
			'name' => __d(sprintf($_domain, 'Resource'), 'ID4', true),
			'width' => 150,
			'sortable' => true,
			'resizable' => true
		),
		array(
			'id' => 'id5',
			'field' => 'id5',
			'name' => __d(sprintf($_domain, 'Resource'), 'ID5', true),
			'width' => 150,
			'sortable' => true,
			'resizable' => true
		),
		array(
			'id' => 'id6',
			'field' => 'id6',
			'name' => __d(sprintf($_domain, 'Resource'), 'ID6', true),
			'width' => 150,
			'sortable' => true,
			'resizable' => true
		),
		array(
			'id' => 'action.',
			'field' => 'action.',
			'name' => __('Action', true),
			'width' => 85,
			'sortable' => false,
			'resizable' => false,
			'formatter' => 'Slick.Formatters.HTMLData',
			'ignoreExport' => true,
		)
	);
}

$container_width = 0;
if(!empty($filter_render)){
	foreach($columns as $key => $vals){
		$field_resize = $vals['field'] . '.Resize';
		if(!empty($filter_render[$field_resize])){
			$columns[$key]['width'] = intval($filter_render[$field_resize]);
		}
	}
}
foreach($columns as $key => $vals){
	$container_width += $vals['width'];
}
$view = !empty($_GET['view'])?$_GET['view']:1;
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

$i = 1;
$actif_user = $pmActif = $csActif = $adActif = $maxActif = $canNotAddMoreMaxActif = 0;
$selects = array();
$selects['sl_budget'] = array(
	0 => __('Not Display Budget', true),
	1 => __('Readonly Budget', true),
	2 => __('Update Budget', true),
);
$dataView = array();
foreach ($employees as $employee) {
    $company_refer =$employee['CompanyEmployeeReference'];
    $refer = $references[$employee['Employee']['id']];
    $control_resource = 0;
    foreach ($company_refer as $value){
        if($refer['employee_id']==$value['employee_id'] && $value['role_id']==3){
            $control_resource = $value['control_resource'];
        }
    }
    
    $data = array(
        'id' => $employee['Employee']['id'],
        'no.' => $i++,
    );
	$data['is_pm'] = 0;
	if(!empty($employeeProfile[$employee['Employee']['id']])){
        $refer['role_id'] = 'profile_' . $employeeProfile[$employee['Employee']['id']];
		$data['is_pm'] = 1;
    }
	if($refer['role_id'] == 3) $data['is_pm'] = 1;
    $data['fullname'] = $employee['Employee']['first_name'] . ' ' . $employee['Employee']['last_name'];
    $data['first_name'] = $employee['Employee']['first_name'];
    $data['last_name'] = $employee['Employee']['last_name'];
    $data['company_id'] = $companies[$refer['company_id']];
    $data['cid'] = $refer['company_id'];
    $data['code_id'] = $employee['Employee']['code_id'];
    $data['identifiant'] = $employee['Employee']['identifiant'];
    $data['id3'] = $employee['Employee']['id3'];
    $data['id4'] = $employee['Employee']['id4'];
    $data['id5'] = $employee['Employee']['id5'];
    $data['id6'] = $employee['Employee']['id6'];
    $data['z0g_id'] = $employee['Employee']['id'] .'/'.$refer['company_id'];
	
    $data['role_id'] = $refer['role_id'];
    $data['email'] = $employee['Employee']['email'];
    $data['email'] = $employee['Employee']['email'];
    $data['work_phone'] = $employee['Employee']['work_phone'];
    $data['mobile_phone'] = $employee['Employee']['mobile_phone'];
    $data['city_id'] = $employee['City']['id'];
    $data['country_id'] = $employee['Country']['id'];
    // $data['update_budget'] = !empty($employee['Employee']['update_budget']) ? $employee['Employee']['update_budget'] : 0;
    $data['profit_center_id'] = !empty($employee_profits[$data['id']]) ? $employee_profits[$data['id']] : null;
	// select budget
	$see_budget = $refer['role_id'] == 3 && !empty($refer['see_budget']) ? $refer['see_budget'] : 0;
	$data['sl_budget'] = 0;
	if(isset($employee['Employee']['update_budget'])){
		if($employee['Employee']['update_budget'] == 1 && $see_budget == 1) {
			$data['sl_budget'] = 2;
		}elseif($employee['Employee']['update_budget'] == 0 && $see_budget == 1) {
			$data['sl_budget'] = 1;
		}
	}
	// admin
	if($refer['role_id'] == 2){
		$data['sl_budget'] = 2;
	}
	//  Select map
	$selects['role_id'][$refer['role_id']] = __($roles[$refer['role_id']], true);
	if(!empty($employee_profits[$data['id']]))$selects['profit_center_id'][$employee_profits[$data['id']]] = $profit_centers[$employee_profits[$data['id']]];
	$selects['city_id'][$employee['City']['id']] = $employee['City']['name'];
	$selects['country_id'][$employee['Country']['id']] = $employee['Country']['name'];
	
	 //kiem tra neu admin thi cac quyen hien thi = YES. Doi voi view Manage right.
	if($value['role_id'] < 3){
		$data['update_your_form'] = 1;
		$data['control_resource'] = 1;
		$data['create_a_project'] = 1;
		$data['delete_a_project'] = 1;
		$data['change_status_project'] = 1;
	}elseif($value['role_id'] == 3){
		$data['update_your_form'] = $employee['Employee']['update_your_form']==1 ? 1: 0;
		$data['control_resource'] = $control_resource==1 ? 1: 0;
		$data['create_a_project'] = $employee['Employee']['create_a_project']==1 ? 1: 0;
		$data['delete_a_project'] = $employee['Employee']['delete_a_project']==1 ? 1: 0;
		$data['change_status_project'] = $employee['Employee']['change_status_project']==1 ? 1: 0;
	}else{
		$data['update_your_form'] = 0;
		$data['control_resource'] = 0;
		$data['create_a_project'] = 0;
		$data['delete_a_project'] = 0;
		$data['change_status_project'] = 0;
	}
    $data['actif'] = $employee['Employee']['actif']==1 ? 1: 0;
    $data['external'] = $employee['Employee']['external']==1 ? 1: 0;
    $data['email_receive'] = $employee['Employee']['email_receive']==1 ? 1: 0;
    $data['activate_copy'] = $employee['Employee']['activate_copy']==1 ? 1: 0;
    $data['auto_timesheet'] = $employee['Employee']['auto_timesheet']==1 ? 1: 0;
    $data['auto_absence']  = $employee['Employee']['auto_absence']==1 ? 1: 0;
    $data['auto_by_himself'] = $employee['Employee']['auto_by_himself']==1 ? 1: 0;
    $data['can_communication'] = $employee['Employee']['can_communication']==1 ? 1: 0;
    $data['can_see_forecast'] = $employee['Employee']['can_see_forecast']==1 ? 1: 0;
    $data['action.'] = '<div class="wd-actions">' . $this->Html->link(__('Edit', true), array(
                'action' => 'edit', $employee['Employee']['id'], $refer['company_id']), array('class' => 'wd-edit')) .
                (!($refer['role_id'] == 2 && $is_pm) ? $this->Html->link('', 'javascript:;', array(
                    'class' => 'wd-hover-advance-tooltip delete-resource',
                    'data-id' => $data['id'],
                    'data-fullname' => $data['fullname'],
                    'data-pc' => !empty($employee_profits[$data['id']]) ? $employee_profits[$data['id']] : 0,
                )) : '')
            . '</div>';
    $dataView[] = $data;
	//draw slider total employee.
	if($employee['Employee']['actif'] == 1){
		$actif_user++;
		if($employee['CompanyEmployeeReference'][0]['role_id'] > 3){
			$csActif++;
		}elseif($employee['CompanyEmployeeReference'][0]['role_id'] > 2){
			$pmActif++;
		}else{
			$adActif++;
		}
	}
}
if((isset($day_alert_billing)) && ($day_alert_billing['Company']['actif_max'] > 0)){
	$maxActif = $day_alert_billing['Company']['actif_max'];
}
?>
<?php if($actif_user > $maxActif){ ?>
	<style>
		.total_item:last-child .total_value{
			border: 1px solid red;
		}
		.total_item:last-child .total_value > span{
			color: red;
		}
		.add-plus {
			background-color: red !important;
		}
	</style>
<?php }?>
<div id="wd-container-main" class="wd-project-index">
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project project-lists">    
                <form method="get" action="" id="form_view_display">
                    <div class="wd-title">
						<?php  
							$sum_employee = array();
							$count_employee = count($employees);
							$view_id = !empty($_GET['view']) ? $_GET['view'] : 1;
							$_sum_employee[$view_id] = ' ('. $count_employee. '/'. $count_employee.')';
							$view_options = array(
								1 => __("Main view", true),
								2 => __("Manage right", true),
								3 => __("View validation, email and copy forecast", true)
							);
							$tmp_view_options = array();
							foreach($view_options as $v_id => $v_name){
								$tmp_view_options[$v_id] = $v_name;
								if(!empty($_sum_employee[$v_id])){
									$tmp_view_options[$v_id] = $v_name . $_sum_employee[$v_id];
								}
							}
							echo $this->Form->input('display_view', array(
								'id' => 'display_view',
								'type' => 'select',
								'div' => false,
								'label' => false,
								'rel' => 'no-history',
								'options' => $tmp_view_options,
								'selected' => $view_id
							));
						?>
                      
                    <?php if( !$is_pm || ($is_pm && (isset($checkControlResource['CompanyEmployeeReference']['control_resource']) && $checkControlResource['CompanyEmployeeReference']['control_resource'] == 1)) || (!empty($profileName['ProfileProjectManager']['create_resource']) && $profileName['ProfileProjectManager']['create_resource'] == 1)): 
						if(!empty($canAddMoreMax)){ ?>
						<a href="<?php echo $html->url('/employees/add') ?>" class="btn-text">
						<?php }else{?>
						<a href="<?php echo $html->url('/employees/add') ?>" class="btn-text add-plus" title="<?php echo __("All your licences are used", true);?>">
						<?php }?>
							<i class="icon-plus"></i>
							<span><?php __('Add Employee') ?></span>
						</a>
						<?php 
						if(!$is_pm || (!empty($profileName['ProfileProjectManager']['create_resource']) && $profileName['ProfileProjectManager']['create_resource'] == 1)): ?>
							<a href="<?php echo $html->url(array('controller' => 'profit_centers', 'action' => 'organization?link=' . $this->params['controller'] . "/" . $this->params['action'] . "|" . __('Employee List', true))) ?>" class="btn-text">
								<i class="icon-organization"></i>
								<span><?php echo __('Organization chart') ?></span>
							</a>
							<a href="javascript:void(0)" class="import-excel-icon-all" title="<?php __('Import Excel')?>" id="import_CSV"><span><?php __('Import CSV') ?></span></a>
						<?php endif ?>
                    <?php endif ?>
                        <a id="export-submitplus" href="javascript:void(0);" class="export-excel-icon-all" title="<?php __('Export Excel')?>"></a>
						<a href="javascript:void(0);" class="btn btn-text reset-filter hidden" id="reset-filter" onclick="resetFilter();" title="<?php __('Delete the filter') ?>">
                                <i class="icon-refresh"></i>
                            </a>
						<?php 
							if(!empty($show_alert_billing)){
								echo '<span class="billing-alert">' . __('Billing in progress, end of licence', true) . ' ' . $licensesDate . ' !</span>';
							}
						?>
                    </div>
                </form>
                <?php
                App::import("vendor", "str_utility");
                $str_utility = new str_utility();
                ?>
                <div id="message-place"><?php echo $this->Session->flash(); ?></div>
				
				<?php if($view_id > 1){?>
					<div id="total-container" class="total-container">
						<div id="total-slider" class="wd-slick-slider loading-mark">
							<div class="slides">
								<?php
								$test_ori = array(
									0 => 'Actif user(s)',
									1 => 'Actif project manager(s)',
									2 => 'Actif consultant(s)',
									3 => 'Actif Administrator(s)',
									4 => 'Maximum of resources'
								);
								$value_total = array(
									0 => $actif_user,
									1 => $pmActif,
									2 => $csActif,
									3 => $adActif,
									4 => $maxActif
								);
								foreach ($value_total as $a=>$p) {
									?>
										<div class="total_item">
											<div class="total_item_small">
												<div class="total_value">
													<span><?php echo $p; ?></span>
												</div>
												<p><?php echo __($test_ori[$a],true); ?></p>
											</div>
											<div class="line_left"></div>
											<div class="line_right"></div>
										</div>
									<?php 
								}
								?>
							</div>
						</div>
					</div>
				<?php }?>
				
				<div class="wd-list-employee" style="width: <?php echo $container_width + 10;?>px;">
					<div class="wd-table" id="project_container"></div>
					<div id="pager" style="width:100%;height:0px; overflow: hidden;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<fieldset style="display: none;">
    <?php
	if($view_id != 1){
		echo $this->Form->create('Export', array(
			'type' => 'POST',
			'url' => array('controller' => 'employees', 'action' => 'exportExcel')));
		echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
		echo $this->Form->end();
	}else{
		echo $this->Form->create('Exportplus', array(
			'id' => '_exportplus',
			'type' => 'POST',
			'url' => array('controller' => 'employees', 'action' => 'exportExcelplus')));
		echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-listplus'));
		echo $this->Form->end();
	}
    ?>
     
</fieldset>
<!-- dialog_import -->
<div id="dialog_import_CSV" title="Import CSV file" class="buttons">
    <?php
    echo $this->Form->create('Import', array('id' => 'uploadForm', 'type' => 'file',
        'url' => array('controller' => 'employees', 'action' => 'import_csv')));
    ?>
    <div class="wd-input">
        <center>
            <label><?php echo __('File:') ?></label>
            <input type="file" name="FileField[csv_file_attachment]" />
        </center>
        <div style="clear:both; margin-left:100px; width: 220px; color: #008000; font-style:italic;">(Allowed file type: *.csv)</div>
    </div>
    <ul class="type_buttons">
        <li><a class="cancel" href="javascript:void(0)"><?php echo __('Close') ?></a></li>
        <li><a id="import-submit" class="new" onclick="return false;" href="#"><?php echo __('Submit') ?></a></li>
        <li id="error"></li>
    </ul>
    <?php echo $this->Form->end(); ?>
</div>
<!-- End dialog_import -->
<!-- dialog_data_csv -->
<div id="dialog_data_CSV" title="Data CSV file" class="buttons">

</div>
<!-- End dialog_data_csv -->

<?php $documents_text = !empty($menu_project_livrable['name_'.$longCode]) ? $menu_project_livrable['name_'.$longCode] : __('Documents', true);?>
<div id="dialog-delete" class="buttons" style="display: none">
    <div class="dialog-wrapper">
        <div class="flashMessage message error">
            <span></span>
        </div>
		<div class="dataLivAct">
            <h4><?php echo $documents_text. ': ' . __('Doc Responsible', true); ?></h4>
            <ul></ul>
        </div>
        <div class="dataLivCmt">
            <h4><?php echo $documents_text. ': ' . __('Comments', true); ?></h4>
            <ul></ul>
        </div>
        <div class="dataLog">
            <h4><?php __('Synthesis')?></h4>
            <ul></ul>
        </div>
        <div class="projects">
            <h4><?php __('Manager of projects:') ?></h4>
            <ul></ul>
        </div>
        <div class="requests">
            <h4><?php __('Activity requests') ?></h4>
            <ul></ul>
        </div>
        <div class="tasks">
            <h4><?php __('Project tasks') ?></h4>
            <ul></ul>
        </div>
        <div class="tasks2">
            <h4><?php __('Activity tasks') ?></h4>
            <ul></ul>
        </div>
    </div>
</div>


<?php

foreach (array('create_a_project','delete_a_project','change_status_project','actif','external','email_receive','activate_copy','auto_timesheet','auto_absence','auto_by_himself','update_your_form','control_resource','can_communication','can_see_forecast') as $val){
    $selects[$val] = array( 0 =>__('No', true), 1 =>__('Yes', true));
}

$company_id = $employee_info["Company"]["id"];
$screenDefaults = ClassRegistry::init('Menu')->find('first', array(
    'recursive' => -1,
    'conditions' => array('model' => 'project', 'company_id' => $company_id, 'default_screen' => 1)
));

$ACLController = 'projects';
$ACLAction = 'edit';
if(!empty($screenDefaults)){
    $ACLController = $screenDefaults['Menu']['controllers'];
    $ACLAction = $screenDefaults['Menu']['functions'];
}
$project_url = '/' . $ACLController . '/' . $ACLAction . '/';

$i18n_yn = array( 0 =>__('No', true), 1 =>__('Yes', true));
?>

<script type="text/javascript">
    //edit 16/10/2013 huythang
    var linkFormatter = function (row, cell, value, columnDef, dataContext) {
        return '<a href="<?php echo $this->Html->url('/employees/edit/') ?>' + dataContext.id + '/' + dataContext.cid + '">' + value + '</a>';
        //return value;
    };
    
    var data = <?php echo json_encode($dataView); ?>;
    var view_options = <?php echo json_encode($view_options); ?>;
    var count_employee = <?php echo json_encode($count_employee); ?>;
    var view_id = <?php echo json_encode($view_id); ?>;
    var i18n_yn = <?php echo json_encode($i18n_yn); ?>;
    var checkColumn = $('#checkColumn').val();
    var $parent = $('#project_container');
    var options = {
		showHeaderRow: true,
		enableAddRow: false,   
		rowHeight: 40,
		topPanelHeight: 40,
		headerRowHeight: 40
	};
	var selects = <?php echo json_encode($selects); ?>;
	var $this = SlickGridCustom;
	$this.url = <?php echo json_encode($html->url(array('action' => 'update_budget'))); ?>;
	$this.fields = {
		id : {defaulValue : 0},
		sl_budget : {defaulValue : 0},
	};
    var columnFilters = {};
	var ControlGrid;
	function resetContainerWidth(){
		var _container_grid = $('.wd-list-employee');
		var _width_grid = _container_grid.width();
		var _width_screen = $(window).width();
		if(_width_grid > _width_screen - 60){
			_container_grid.width(_width_screen - 60);
		}else{
			_container_grid.width(_width_grid);
		}
	}
	resetContainerWidth();
	function slickGridFilterCallBack() {
		var dataView = ControlGrid.getDataView();
		var count = dataView.getLength();
		var op_id = $('#display_view option[selected="selected"]').val();
		$('#display_view option[selected="selected"]').text(view_options[op_id] + ' (' + count + '/' + count_employee + ')');
	}
	function history_reset() {
		var check = false;
		$('.multiselect-filter').each(function (val, ind) {
			var text = '';
			if ($(ind).find('input').length != 0) {
				text = $(ind).find('input').val();
			} else {
				text = $(ind).find('span').html();
				if (text == "<?php __('-- Any --');?>" || text == '-- Any --') {
					text = '';

				}
			}
			if (text != '') {
				check = true;
			}
		});
		if (!check) {
			$('#reset-filter').addClass('hidden');
			$('#reset-filter').css('display', 'none');
		} else {
			$('#reset-filter').removeClass('hidden');
            $('#reset-filter').css('display', 'inline-block');
		}
	}
	function resetFilter() {
		$('.input-filter').val('').trigger('change');
		$('.multiSelectOptions input[type="checkbox"]').prop('checked', false).trigger('change');
		ControlGrid.setSortColumn();
		$('input[name="project_container.SortOrder"]').val('').trigger('change');
		$('input[name="project_container.SortColumn"]').val('').trigger('change');
	}
	function switchUpdate(){
		var _this = $(this);
		_this.addClass('sw-loading');
		var employee_id = _this.data('id');
		var field = _this.data('field');
		var value = _this.find('.sw-value').val();
		var sw_value = (value == 1) ? 0 : 1;
		$.ajax({
			url: '/employees/switchUpdate/',
			type: 'post',
			dataType: 'json',
			data: {
				id: employee_id,
				field: field,
				value: sw_value,
			},
			success: function (res) {
				if(res == 1){
					_this.removeClass('sw-loading');
					_this.find('.sw-value').val(sw_value).trigger('change');
					if(sw_value == 1){
						_this.addClass('sw-yes');
					}else{
						_this.removeClass('sw-yes');
					}
				}
			},
		});
	}
    (function ($) {

        //end edit 16/10/2013 huythang

        /* begin render table*/

        var sortcol, triggger = false, grid, $sortColumn, $sortOrder;
       
        var project_url = <?php echo json_encode($project_url) ?>;
    
        function comparer(a, b) {
            var x = a[sortcol], y = b[sortcol];
            return (x == y ? 0 : (x > y ? 1 : -1));
        }

        function comparer_date(a, b) {
            var arr;
            if (typeof (a[sortcol]) === "undefined" || a[sortcol] == "") {
                c = "1/1/1970";
            } else {
                arr = a[sortcol].split("-");
                c = arr[1] + "/" + arr[0] + "/" + arr[2];
            }
            if (typeof (b[sortcol]) === "undefined" || b[sortcol] == "") {
                d = "1/1/1970";
            } else {
                arr = b[sortcol].split("-");
                d = arr[1] + "/" + arr[0] + "/" + arr[2];
            }
            var c = new Date(c),
                    d = new Date(d);
            return (c.getTime() - d.getTime());
        }
		
        function filter(item) {
            for (var columnId in columnFilters) {
                if (columnId !== undefined && columnFilters[columnId] !== "") {
                    var c = grid.getColumns()[grid.getColumnIndex(columnId)];
                    if (typeof (c) == 'undefined' || item[c.field] == null || item[c.field].toLowerCase().indexOf(columnFilters[columnId].toLowerCase()) == -1) {
                        return false;
                    }
                }
            }
            return true;
        }
		$.extend($this,{
			selectMaps : selects,
			canModified: true,
		});
		$.extend(Slick.Formatters, {
			 switchButton: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting) return (value == 1) ? $this.t('Yes') : $this.t('No');
				var _ex_class = (value == 1) ? 'sw-yes' : '';
				if(columnDef.field == 'actif') _ex_class += ' sw-actif';
				var _action = '';
				if(dataContext.role_id > 3){
					if(columnDef.field == 'auto_by_himself'){
						if(dataContext.auto_by_himself == 1) _ex_class += ' sw-yes';
					}else if(columnDef.field == 'activate_copy'){
						if(dataContext.activate_copy == 1) _ex_class += ' sw-yes';
					}else if(columnDef.field == 'email_receive'){
						if(dataContext.email_receive == 1) _ex_class += ' sw-yes';
					}else if(columnDef.field == 'actif'){
						if(dataContext.actif == 1) _ex_class += ' sw-yes';
					}else if(columnDef.field == 'external'){
						if(dataContext.external == 1) _ex_class += ' sw-yes';
					}else{
						return '';
					}
				}else{
					if(dataContext.is_pm != 1 && columnDef.field != 'actif') return '';
				}
				if(view_id != 1){
					_action = ' href="javascript:void(0);"  onclick="switchUpdate.call(this);" data-id ='+ dataContext.id +' data-field ='+ columnDef.field +'';
				}
				var _html = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-switch"><a class="wd-switch '+ _ex_class +'" '+ _action +' title ="'+ i18n_yn[value] +'"><input class="sw-value" rel="no-history" type="hidden" value=' + value +' /></a></div></div>';
                return _html;
            },
			slBuget: function (row, cell, value, columnDef, dataContext) {
				var _html = (view_id == 2 && dataContext.role_id == 3) ? '<p>' +selects['sl_budget'][value] + '</p>' : '';
				return _html;
			}, 
		});
		$this.onBeforeEdit = function(args){
			if(args.item && args.item.role_id != 3){
				return false;
			}
			return true;
		};
		var columns = <?php echo jsonParseOptions($columns, array('formatter', 'editor',  'validator')); ?>;
		ControlGrid = $this.init($parent, data, columns, options);
		function update_table_height(){
			// Width table
			var _container_grid = $('.wd-list-employee');
			var _width_grid = 0;
			var _cols = ControlGrid.getColumns();
			var _numCols = _cols.length;
			for (var i = 0; i < _numCols; i++) {
			   _width_grid += _cols[i].width;
			}
			var _width_screen = $(window).width();
			if(_width_grid > _width_screen - 50){
				_container_grid.width(_width_screen - 60);
			}else{
				_container_grid.width(_width_grid + 10);
			}
			
			// Height table
			var wdTable = $('.wd-table');
			var heightTable = $(window).height() - wdTable.offset().top - 40;
			wdTable.height(heightTable);
			ControlGrid.resizeCanvas();
		}
		$(window).resize(function(){
			update_table_height();
		});
		ControlGrid.onColumnsResized.subscribe(function (e, args) {
            update_table_height();
        });
		update_table_height();
		if(view_id != 1){
			var exporter = new Slick.DataExporter('/employees/export_excel_index?view='+ view_id);
			ControlGrid.registerPlugin(exporter);
			$('#export-submitplus').click(function () {
				$this.isExporting = 1;
				exporter.submit();
				$this.isExporting = 0;
				return false;
			});
		}else{
			$('#export-submitplus').click(function () {
				var dataView = ControlGrid.getDataView();
				var length = dataView.getLength();
				var list = [];
				for (var i = 0; i < length; i++) {
					list.push(dataView.getItem(i).id);
				}
				$('#export-item-listplus').val(list.join(',')).closest('form').submit();
			});
        }
        $('#display_view').change(function () {
            var id = $(this).val();
            if (id == 2) {
                window.location.href = '/employees?view=2';
            } else if (id == 3) {
                window.location.href = '/employees?view=3';
            } else {
                window.location.href = '/employees?view=1';
            }
        });
        $('#dialog_import_CSV').dialog({
            position: 'center',
            autoOpen: false,
            autoHeight: true,
            modal: true,
            width: 360,
            height: 125
        });
        $('#dialog_data_CSV').dialog({
            position: 'top',
            autoOpen: false,
            autoHeight: true,
            modal: true,
            minHeight: 102,
            width: 760
                    //auto  : true
                    // height      : 230
        });
        $('#dialog-delete').dialog({
            position: 'top',
            autoOpen: false,
            autoHeight: true,
            modal: true,
            autoHeight: true,
            width: 600
        });
        $("#import_CSV").click(function () {
            $('.wd-input').show();
            $('#loading').hide();
            $("input[name='FileField[csv_file_attachment]']").val("");
            $(".error-message").remove();
            $("input[name='FileField[csv_file_attachment]']").removeClass("form-error");
            $(".type_buttons").show();
            $('#dialog_import_CSV').dialog("open");
        });
        $("#import-submit").click(function () {
            $(".error-message").remove();
            $("input[name='FileField[csv_file_attachment]']").removeClass("form-error");
            if ($("input[name='FileField[csv_file_attachment]']").val()) {
                var filename = $("input[name='FileField[csv_file_attachment]']").val();
                var valid_extensions = /(\.csv)$/i;
                if (valid_extensions.test(filename)) {
                    $('#uploadForm').submit();
                } else {
                    $("input[name='FileField[csv_file_attachment]']").addClass("form-error");
                    jQuery('<div>', {
                        'class': 'error-message',
                        text: 'Incorrect type file'
                    }).appendTo('#error');
                }
            } else {
                jQuery('<div>', {
                    'class': 'error-message',
                    text: 'Please choose a file!'
                }).appendTo('#error');
            }
        });
        $(".cancel").live('click', function () {
            $("#dialog_data_CSV").dialog("close");
            $("#dialog_import_CSV").dialog("close");
        });
		
        $(document).on('click', '.delete-resource', function () {
            var id = $(this).data('id'),
			    fullname =  $(this).data('fullname'),
                    pc = $(this).data('pc');
            var url = '/activity_forecasts/request/month?id=' + id + '&profit=' + pc + '&month={month}&year={year}';
            // if (confirm(<?php echo json_encode(__('Delete', true)) ?> + ' '+ fullname + '?')) {
			wdConfirmIt({
                // call ajax here
				title: <?php echo json_encode(__('Delete', true)) ?>,
				content: <?php echo json_encode(__('Delete', true)) ?> + ' '+ fullname + '?',
				buttonModel: 'WD_TWO_BUTTON',
				buttonText: [
					'<?php __('Yes');?>',
					'<?php __('No');?>'
				]}, function(){
					ajaxDeleteEmployee(id);
				}
			);
			function ajaxDeleteEmployee(id){
				$.ajax({
                    url: '/employees/delete/' + id,
                    type: 'post',
                    dataType: 'json',
                    success: function (data) {
                        var e = $('#dialog-delete');
                        if (!data.result) {
                            e.find('.message').html(data.message);
                            if (typeof data.dataLivAct != 'undefined') {
                                var p = e.find('.dataLivAct ul').html('').show();
                                e.find('.dataLivAct').show();
                                $.each(data.dataLivAct, function (k, v) {
                                    p.append('<li><a target="_blank" href="/project_livrables_preview/index/' + k + '">' + v + '</a></li>');
                                });
                            } else {
                                e.find('.dataLivAct').hide();
                            }
                            if (typeof data.dataLivCmt != 'undefined') {
                                var p = e.find('.dataLivCmt ul').html('').show();
                                e.find('.dataLivCmt').show();
                                $.each(data.dataLivCmt, function (k, v) {
                                    p.append('<li><a target="_blank" href="project_livrables_preview/index/' + k + '">' + v + '</a></li>');
                                });
                            } else {
                                e.find('.dataLivCmt').hide();
                            }
                            if (typeof data.dataLog != 'undefined') {
                                var p = e.find('.dataLog ul').html('').show();
                                e.find('.dataLog').show();
                                $.each(data.dataLog, function (k, v) {
                                    p.append('<li><a target="_blank" href="/project_amrs_preview/indicator/' + k + '">' + v + '</a></li>');
                                });
                            } else {
                                e.find('.dataLog').hide();
                            }
                            if (typeof data.projects != 'undefined') {
                                var p = e.find('.projects ul').html('').show();
                                e.find('.projects').show();
                                $.each(data.projects, function (k, v) {
                                    p.append('<li><a target="_blank" href="' + project_url + k + '">' + v + '</a></li>');
                                });
                            } else {
                                e.find('.projects').hide();
                            }
                            if (typeof data.requests != 'undefined') {
                                var p = e.find('.requests ul').html('').show();
                                e.find('.requests').show();
                                $.each(data.requests, function (k, v) {
                                    var vv = v.ActivityRequest;
                                    var m = vv.month.split('-');
                                    var my_url = url.replace('{month}', m[0]).replace('{year}', m[1]);
                                    p.append('<li><a target="_blank" href="' + my_url + '">' + vv.month + '</a>: <b>' + vv.sum_consume + '</b> / ' + vv.sum_wait + '</li>');
                                });
                            } else {
                                e.find('.requests').hide();
                            }

                            if (typeof data.tasks != 'undefined' && data.tasks.length) {
                                var p = e.find('.tasks ul').html('');
                                e.find('.tasks').show();
                                $.each(data.tasks, function (k, v) {
                                    var vv = v.ProjectTask;
                                    p.append('<li><a target="_blank" href="/project_tasks/index/' + vv.project_id + '?id=' + vv.id + '">' + vv.task_title + '</a></li>');
                                });
                            } else {
                                e.find('.tasks').hide();
                            }

                            if (typeof data.tasks2 != 'undefined' && data.tasks2.length) {
                                var p = e.find('.tasks2 ul').html('');
                                e.find('.tasks2').show();
                                $.each(data.tasks2, function (k, v) {
                                    var vv = v.ActivityTask;
                                    p.append('<li><a target="_blank" href="/activity_tasks/index/' + vv.activity_id + '?id=' + vv.id + '">' + vv.name + '</a></li>');
                                });
                            } else {
                                e.find('.tasks2').hide();
                            }

                            e.dialog('open');
                        } else {
                            //delete from grid
							var dataView = ControlGrid.getDataView();
                            dataView.deleteItem(id);
                            $('#message-place').html('<div class="flashMessage message success">' + data.message + '</div>');
                        }
                    }
                }); 
			}
        });
    }
	
		
    )(jQuery);
</script>
