<?php
echo $this->Html->css(array(
    'slick_grid/slick.grid.activity',
    'jquery.multiSelect',
    'projects',
    'slick_grid/slick.grid_v2',
    'slick_grid/slick.pager',
    'slick_grid/slick.common_v2',
    'slick_grid/slick.edit'
));
echo $this->Html->script(array(
    'history_filter'
));
?>

<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
    .multiSelect {width: 323px !important;}
    .multiSelect span{width: 317px !important;}
    body{overflow: hidden;}
</style>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"><div class="wd-panel">
            <div class="wd-list-project">
                <div class="wd-title">
                    <a href="<?php echo $html->url('/user_views/add?model='.$model) ?>" class="btn btn-plus"><span><?php //__('Add View') ?></span></a>
                    <a href="javascript:void(0);" class="btn btn-reset-filter hidden" id="reset-filter" onclick="resetFilter();" style="margin-right:5px;" title="<?php __('Reset filter') ?>"></a>
                </div>
                <?php
                App::import("vendor", "str_utility");
                $str_utility = new str_utility();
                echo $this->Session->flash();
                ?>
                <div class="wd-table" id="project_container" style="width:100%;">
                </div>
                <div id="pager" style="width:100%;height:36px;"></div>
            </div></div></div>
        </div>
    </div>
</div>
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
echo $this->element('dialog_projects');
?>
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
if($model=='project') {
    $columns = array(
        array(
            'id' => 'no.',
            'field' => 'no.',
            'name' => '#',
            'width' => 20,
            'sortable' => true,
            'resizable' => false
        ),
        array(
            'id' => 'name',
            'field' => 'name',
            'name' => __('View name', true),
            'width' => 200,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'action.',
            'field' => 'action.',
            'name' => __('Action', true),
            'width' => 70,
            'sortable' => false,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'description',
            'field' => 'description',
            'name' => __('Description', true),
            'width' => 300,
            'sortable' => false,
            'resizable' => true
        ),
        array(
            'id' => 'created_date',
            'field' => 'created_date',
            'name' => __('Created date', true),
            'width' => 120,
            'datatype' => 'datetime',
            'sortable' => true,
            'resizable' => true
        ),
        array(
            'id' => 'employee_id',
            'field' => 'employee_id',
            'name' => __('Author', true),
            'width' => 120,
            'sortable' => true,
            'resizable' => true
        ),
        array(
            'id' => 'mobile',
            'field' => 'mobile',
            'name' => __('Mobile', true),
            'width' => 80,
            'sortable' => true,
            'resizable' => true,
            //'formatter' => 'yesNoFormatter'
        ),
        array(
            'id' => 'progress_view',
            'field' => 'progress_view',
            'name' => __('In progress view', true),
            'width' => 80,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'oppor_view',
            'field' => 'oppor_view',
            'name' => __('Opportunity view', true),
            'width' => 80,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'archived_view',
            'field' => 'archived_view',
            'name' => __('Archived view', true),
            'width' => 80,
            'sortable' => false,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'model_view',
            'field' => 'model_view',
            'name' => __('Model view', true),
            'width' => 80,
            'sortable' => false,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'default_mobile_view',
            'field' => 'default_mobile_view',
            'name' => __('Default mobile view', true),
            'width' => 80,
            'sortable' => false,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'default_view',
            'field' => 'default_view',
            'name' => __('Default view', true),
            'width' => 80,
            'sortable' => false,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'public',
            'field' => 'public',
            'name' => $isAdmin ? __('Public view', true) : __('Admin Public view', true),
            'width' => 80,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        )
    );
} else if($model=='business') {
    $columns = array(
        array(
            'id' => 'no.',
            'field' => 'no.',
            'name' => '#',
            'width' => 20,
            'sortable' => true,
            'resizable' => false
        ),
        array(
            'id' => 'name',
            'field' => 'name',
            'name' => __('View name', true),
            'width' => 200,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'action.',
            'field' => 'action.',
            'name' => __('Action', true),
            'width' => 70,
            'sortable' => false,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'description',
            'field' => 'description',
            'name' => __('Description', true),
            'width' => 300,
            'sortable' => true,
            'resizable' => true
        ),
        array(
            'id' => 'created_date',
            'field' => 'created_date',
            'name' => __('Created date', true),
            'width' => 120,
            'datatype' => 'datetime',
            'sortable' => true,
            'resizable' => true
        ),
        array(
            'id' => 'employee_id',
            'field' => 'employee_id',
            'name' => __('Author', true),
            'width' => 120,
            'sortable' => true,
            'resizable' => true
        ),
        array(
            'id' => 'open',
            'field' => 'open',
            'name' => __('Open', true),
            'width' => 80,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'closed_won',
            'field' => 'closed_won',
            'name' => __('Closed Won', true),
            'width' => 80,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'closed_lose',
            'field' => 'closed_lose',
            'name' => __('Closed Lose', true),
            'width' => 80,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'default_view',
            'field' => 'default_view',
            'name' => __('Default view', true),
            'width' => 80,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'public',
            'field' => 'public',
            'name' => $isAdmin ? __('Public view', true) : __('Admin Public view', true),
            'width' => 80,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
    );
} else if($model=='deal') {
    $columns = array(
        array(
            'id' => 'no.',
            'field' => 'no.',
            'name' => '#',
            'width' => 20,
            'sortable' => true,
            'resizable' => false
        ),
        array(
            'id' => 'name',
            'field' => 'name',
            'name' => __('View name', true),
            'width' => 200,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'action.',
            'field' => 'action.',
            'name' => __('Action', true),
            'width' => 70,
            'sortable' => false,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'description',
            'field' => 'description',
            'name' => __('Description', true),
            'width' => 300,
            'sortable' => true,
            'resizable' => true
        ),
        array(
            'id' => 'created_date',
            'field' => 'created_date',
            'name' => __('Created date', true),
            'width' => 120,
            'datatype' => 'datetime',
            'sortable' => true,
            'resizable' => true
        ),
        array(
            'id' => 'employee_id',
            'field' => 'employee_id',
            'name' => __('Author', true),
            'width' => 120,
            'sortable' => true,
            'resizable' => true
        ),
        array(
            'id' => 'open',
            'field' => 'open',
            'name' => __('Open', true),
            'width' => 80,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'archived',
            'field' => 'archived',
            'name' => __('Archived', true),
            'width' => 80,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'renewal',
            'field' => 'renewal',
            'name' => __('Renewal', true),
            'width' => 80,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'default_view',
            'field' => 'default_view',
            'name' => __('Default view', true),
            'width' => 80,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'public',
            'field' => 'public',
            'name' => $isAdmin ? __('Public view', true) : __('Admin Public view', true),
            'width' => 80,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        )
    );
} else if($model=='ticket') {
    $columns = array(
        array(
            'id' => 'no.',
            'field' => 'no.',
            'name' => '#',
            'width' => 20,
            'sortable' => true,
            'resizable' => false
        ),
        array(
            'id' => 'name',
            'field' => 'name',
            'name' => __('View name', true),
            'width' => 200,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'action.',
            'field' => 'action.',
            'name' => __('Action', true),
            'width' => 70,
            'sortable' => false,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'description',
            'field' => 'description',
            'name' => __('Description', true),
            'width' => 300,
            'sortable' => true,
            'resizable' => true
        ),
        array(
            'id' => 'created_date',
            'field' => 'created_date',
            'name' => __('Created date', true),
            'width' => 120,
            'datatype' => 'datetime',
            'sortable' => true,
            'resizable' => true
        ),
        array(
            'id' => 'employee_id',
            'field' => 'employee_id',
            'name' => __('Author', true),
            'width' => 120,
            'sortable' => true,
            'resizable' => true
        ),
        array(
            'id' => 'default_view',
            'field' => 'default_view',
            'name' => __('Default view', true),
            'width' => 80,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'public',
            'field' => 'public',
            'name' => $isAdmin ? __('Public view', true) : __('Admin Public view', true),
            'width' => 80,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        )
    );
} else {
    $columns = array(
        array(
            'id' => 'no.',
            'field' => 'no.',
            'name' => '#',
            'width' => 20,
            'sortable' => true,
            'resizable' => false
        ),
        array(
            'id' => 'name',
            'field' => 'name',
            'name' => __('View name', true),
            'width' => 200,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'action.',
            'field' => 'action.',
            'name' => __('Action', true),
            'width' => 70,
            'sortable' => false,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'description',
            'field' => 'description',
            'name' => __('Description', true),
            'width' => 300,
            'sortable' => true,
            'resizable' => true
        ),
        array(
            'id' => 'created_date',
            'field' => 'created_date',
            'name' => __('Created date', true),
            'width' => 120,
            'datatype' => 'datetime',
            'sortable' => true,
            'resizable' => true
        ),
        array(
            'id' => 'employee_id',
            'field' => 'employee_id',
            'name' => __('Author', true),
            'width' => 120,
            'sortable' => true,
            'resizable' => true
        ),
        array(
            'id' => 'activated',
            'field' => 'activated',
            'name' => __('Activated', true),
            'width' => 80,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'not_activated',
            'field' => 'not_activated',
            'name' => __('Not activated', true),
            'width' => 80,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'activated_and_not_activated',
            'field' => 'activated_and_not_activated',
            'name' => __('Activated and not activated', true),
            'width' => 80,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'default_view',
            'field' => 'default_view',
            'name' => __('Default view', true),
            'width' => 80,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        ),
        array(
            'id' => 'public',
            'field' => 'public',
            'name' => $isAdmin ? __('Public view', true) : __('Admin Public view', true),
            'width' => 80,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.HTMLData'
        )
    );
}
$i = 1;
$dataView = array();
foreach ($userViews as $userView) {
    $data = array(
        'id' => $userView['UserView']['id'],
        'no.' => $i++,
    );
    if($model == 'project') {
        $data['mobile'] = $userView['UserView']['mobile'] ? __('Yes', true) : __('No', true);
        $data['name'] = $this->Html->link($userView['UserView']['name'], array('controller' => 'projects', 'action' => 'index', $userView['UserView']['id']), array());
    } elseif ($model == 'business'){
       $data['name'] = $this->Html->link($userView['UserView']['name'], array('controller' => 'sale_leads', 'action' => 'index', $company_id, $userView['UserView']['id']), array());
    } elseif ($model == 'deal'){
       $data['name'] = $this->Html->link($userView['UserView']['name'], array('controller' => 'sale_leads', 'action' => 'deal', $company_id, $userView['UserView']['id']), array());
    } elseif ($model == 'ticket'){
       $data['name'] = $this->Html->link($userView['UserView']['name'], array('controller' => 'tickets', 'action' => 'index', $userView['UserView']['id']), array());
    } else {
       $data['name'] = $this->Html->link($userView['UserView']['name'], array('controller' => 'activities', 'action' => 'manage?view='.$userView['UserView']['id'], null), array());
    }
    $data['description'] = $userView['UserView']['description'];
    $data['created_date'] = $str_utility->convertToVNDate($userView['UserView']['created_date']);
    $data['employee_id'] = sprintf('%1$s %2$s', $userView['Employee']['first_name'], $userView['Employee']['last_name']);
    $class = $default_mobile = '';
	if( !empty( $defaultView['UserDefaultView']['user_view_id'])){
		if( !empty( $defaultView['UserDefaultView']['user_view_id']) &&   $defaultView['UserDefaultView']['user_view_id'] == $userView['UserView']['id']){
			$class = ' wd-update-default';
		}
		$data['default_view'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Default', true), array(
						'controller' => 'user_views',
						'action' => 'toggle',$model, $userView['UserView']['id'], empty($class)), array(
						'class' => 'wd-update' . $class)) . '</div></div>';
	}else{
		$inPro = !empty($conpanyDefaultView[$userView['UserView']['id']]) && ($conpanyDefaultView[$userView['UserView']['id']]['default_view'] == 1) ? true : false;
			if ($inPro == true) {
				$class = ' wd-update-default';
			}
		$data['default_view'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Default', true), array(
						'controller' => 'user_views',
						'action' => 'toggle',$model, $userView['UserView']['id'], empty($class)), array(
						'class' => 'wd-update' . $class)) . '</div></div>';
	}
	
	
    if($model=='project') {
        /**
         * In progress View
         */		
		if(!empty($statusView) && !empty($statusView[$userView['UserView']['id']])){
			$class = '';
			$inPro = !empty($statusView[$userView['UserView']['id']]) && ($statusView[$userView['UserView']['id']]['progress_view'] == 1) ? true : false;
			if ($inPro == true) {
				$class = ' wd-update-default';
			}
			$data['progress_view'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('InProgress', true), array(
						'controller' => 'user_views',
						'action' => 'toggle_status_views', 'progress_view', $userView['UserView']['id'], empty($class)), array(
						'class' => 'wd-update' . $class)) . '</div></div>';
		} else {
			$class = '';
			$inPro = !empty($conpanyDefaultView[$userView['UserView']['id']]) && ($conpanyDefaultView[$userView['UserView']['id']]['progress_view'] == 1) ? true : false;
			if ($inPro == true) {
				$data['progress_view'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('InProgress', true), array(
						'controller' => 'user_views',
						'action' => 'toggle_status_views', 'progress_view', $userView['UserView']['id'], empty($class)), array(
						'class' => 'wd-update wd-update-default')) . '</div></div>';
			} else {
				$data['progress_view'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('InProgress', true), array(
						'controller' => 'user_views',
						'action' => 'toggle_status_views', 'progress_view', $userView['UserView']['id'], empty($class)), array(
						'class' => 'wd-update' . $class)) . '</div></div>';
			}	
		}
        /**
         * Opportunity view
         */
		
		if(!empty($statusView) && !empty($statusView[$userView['UserView']['id']])){
			$class = '';
			$inOppor = !empty($statusView[$userView['UserView']['id']]) && ($statusView[$userView['UserView']['id']]['oppor_view'] == 1) ? true : false;
			if ($inOppor == true) {
				$class = ' wd-update-default';
			}
			$data['oppor_view'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Opportunity', true), array(
						'controller' => 'user_views',
						'action' => 'toggle_status_views', 'oppor_view', $userView['UserView']['id'], empty($class)), array(
						'class' => 'wd-update' . $class)) . '</div></div>';
		} else {
			$class = '';
			$inOppor = !empty($conpanyDefaultView[$userView['UserView']['id']]) && ($conpanyDefaultView[$userView['UserView']['id']]['oppor_view'] == 1) ? true : false;
			if ($inOppor == true) {
				$data['oppor_view'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Opportunity', true), array(
						'controller' => 'user_views',
						'action' => 'toggle_status_views', 'oppor_view', $userView['UserView']['id'], empty($class)), array(
						'class' => 'wd-update wd-update-default')) . '</div></div>';
			} else {
				$data['oppor_view'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Opportunity', true), array(
                    'controller' => 'user_views',
                    'action' => 'toggle_status_views', 'oppor_view', $userView['UserView']['id'], empty($class)), array(
                    'class' => 'wd-update' . $class)) . '</div></div>';
			}
		}
        /**
         * Archived view
         */
					
		if(!empty($statusView) && !empty($statusView[$userView['UserView']['id']])){
			$class = '';
			$inArchived = !empty($statusView[$userView['UserView']['id']]) && ($statusView[$userView['UserView']['id']]['archived_view'] == 1) ? true : false;
			if ($inArchived == true) {
				$class = ' wd-update-default';
			}
			$data['archived_view'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Archived', true), array(
						'controller' => 'user_views',
						'action' => 'toggle_status_views', 'archived_view', $userView['UserView']['id'], empty($class)), array(
						'class' => 'wd-update' . $class)) . '</div></div>';
		} else {
			$class = '';
			$inArchived = !empty($conpanyDefaultView[$userView['UserView']['id']]) && ($conpanyDefaultView[$userView['UserView']['id']]['archived_view'] == 1) ? true : false;
			if ($inArchived == true) {
				$data['archived_view'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Archived', true), array(
						'controller' => 'user_views',
						'action' => 'toggle_status_views', 'archived_view', $userView['UserView']['id'], empty($class)), array(
						'class' => 'wd-update wd-update-default')) . '</div></div>';
			} else {
				$data['archived_view'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Archived', true), array(
                    'controller' => 'user_views',
                    'action' => 'toggle_status_views', 'archived_view', $userView['UserView']['id'], empty($class)), array(
                    'class' => 'wd-update' . $class)) . '</div></div>';
			}
		}
        /**
         * Model view
         */
		if(!empty($statusView) && !empty($statusView[$userView['UserView']['id']])){
			$class = '';
			$inModel = !empty($statusView[$userView['UserView']['id']]) && ($statusView[$userView['UserView']['id']]['model_view'] == 1) ? true : false;
			if ($inModel == true) {
				$class = ' wd-update-default';
			}
			$data['model_view'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Model', true), array(
						'controller' => 'user_views',
						'action' => 'toggle_status_views', 'model_view', $userView['UserView']['id'], empty($class)), array(
						'class' => 'wd-update' . $class)) . '</div></div>';
		} else {
			$class = '';
			$inModel = !empty($conpanyDefaultView[$userView['UserView']['id']]) && ($conpanyDefaultView[$userView['UserView']['id']]['model_view'] == 1) ? true : false;
			if ($inModel == true) {
				$data['model_view'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Model', true), array(
						'controller' => 'user_views',
						'action' => 'toggle_status_views', 'model_view', $userView['UserView']['id'], empty($class)), array(
						'class' => 'wd-update wd-update-default')) . '</div></div>';
			} else {
				$data['model_view'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Model', true), array(
                    'controller' => 'user_views',
                    'action' => 'toggle_status_views', 'model_view', $userView['UserView']['id'], empty($class)), array(
                    'class' => 'wd-update' . $class)) . '</div></div>';
			}
		}
        //mobile
		if( isset($statusView[ $userView['UserView']['id'] ]['mobile']) && $statusView[ $userView['UserView']['id'] ]['mobile'] == 1 ){
            $default_mobile = ' wd-update-default';
        }
        $data['default_mobile_view'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Default', true), array(
            'controller' => 'user_views',
            'action' => 'toggle_mobile', $userView['UserView']['id'], empty($default_mobile) ? 1 : 0), array(
            'class' => 'wd-update' . $default_mobile)) . '</div></div>';
		
		if(!empty($statusView) && !empty($statusView[$userView['UserView']['id']])){
			if( isset($statusView[ $userView['UserView']['id'] ]['mobile']) && $statusView[ $userView['UserView']['id'] ]['mobile'] == 1 ){
				$default_mobile = ' wd-update-default';
			}
			$data['default_mobile_view'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Default', true), array(
				'controller' => 'user_views',
				'action' => 'toggle_mobile', $userView['UserView']['id'], empty($default_mobile) ? 1 : 0), array(
				'class' => 'wd-update' . $default_mobile)) . '</div></div>';
		} else {
			$class = '';
			$inMob = !empty($conpanyDefaultView[$userView['UserView']['id']]) && ($conpanyDefaultView[$userView['UserView']['id']]['default_mobile'] == 1) ? true : false;
			if ($inMob == true) {
				$data['default_mobile_view'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Default', true), array(
				'controller' => 'user_views',
				'action' => 'toggle_mobile', $userView['UserView']['id'], empty($default_mobile) ? 1 : 0), array(
				'class' => 'wd-update wd-update-default')) . '</div></div>';
			} else {
				$data['default_mobile_view'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Default', true), array(
				'controller' => 'user_views',
				'action' => 'toggle_mobile', $userView['UserView']['id'], empty($default_mobile) ? 1 : 0), array(
				'class' => 'wd-update' . $default_mobile)) . '</div></div>';
			}
		}
		
		
    } else if($model=='business') {
        /**
         * Open
         */
        $class = '';
        $inActi = !empty($statusView[$userView['UserView']['id']]) && ($statusView[$userView['UserView']['id']]['open'] == '1') ? true : false;
        if ($inActi == true) {
            $message = __('Do you want to update "%s" is not Open ?', true);
            $class = ' wd-update-default';
        }
        $data['open'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Open', true), array(
                    'controller' => 'user_views',
                    'action' => 'toggle_status_views_sales', 'open', $userView['UserView']['id'], empty($class)), array(
                    'class' => 'wd-update' . $class)) . '</div></div>';
        /**
         * Closed Won
         */
        $class = '';
        $inNotActi = (!empty($statusView[$userView['UserView']['id']]) && ($statusView[$userView['UserView']['id']]['closed_won'] == 1)) ? true : false;
        if ($inNotActi == true) {
            $class = ' wd-update-default';
        }
        $data['closed_won'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Closed Won', true), array(
                    'controller' => 'user_views',
                    'action' => 'toggle_status_views_sales', 'closed_won', $userView['UserView']['id'], empty($class)), array(
                    'class' => 'wd-update' . $class)) . '</div></div>';
        /**
         * Closed Lose
         */
        $class = '';
        $inBoth = !empty($statusView[$userView['UserView']['id']]) && ($statusView[$userView['UserView']['id']]['closed_lose'] == 1) ? true : false;
        if ($inBoth == true) {
            $class = ' wd-update-default';
        }
        $data['closed_lose'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Closed Lose', true), array(
                    'controller' => 'user_views',
                    'action' => 'toggle_status_views_sales', 'closed_lose', $userView['UserView']['id'], empty($class)), array(
                    'class' => 'wd-update' . $class)) . '</div></div>';
    } else if($model=='deal') {
        /**
         * Open
         */
        $class = '';
        $inActi = !empty($statusView[$userView['UserView']['id']]) && ($statusView[$userView['UserView']['id']]['open'] == '1') ? true : false;
        if ($inActi == true) {
            $class = ' wd-update-default';
        }
        $data['open'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Open', true), array(
                    'controller' => 'user_views',
                    'action' => 'toggle_status_views_sale_deals', 'open', $userView['UserView']['id'], empty($class)), array(
                    'class' => 'wd-update' . $class)) . '</div></div>';
        /**
         * Archived
         */
        $class = '';
        $inNotActi = (!empty($statusView[$userView['UserView']['id']]) && ($statusView[$userView['UserView']['id']]['archived'] == 1)) ? true : false;
        if ($inNotActi == true) {
            $class = ' wd-update-default';
        }
        $data['archived'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Archived', true), array(
                    'controller' => 'user_views',
                    'action' => 'toggle_status_views_sale_deals', 'archived', $userView['UserView']['id'], empty($class)), array(
                    'class' => 'wd-update' . $class)) . '</div></div>';
        /**
         * Renewal
         */
        $class = '';
        $inBoth = !empty($statusView[$userView['UserView']['id']]) && ($statusView[$userView['UserView']['id']]['renewal'] == 1) ? true : false;
        if ($inBoth == true) {
            $class = ' wd-update-default';
        }
        $data['renewal'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Renewal', true), array(
                    'controller' => 'user_views',
                    'action' => 'toggle_status_views_sale_deals', 'renewal', $userView['UserView']['id'], empty($class)), array(
                    'class' => 'wd-update' . $class)) . '</div></div>';
    } else {
        /**
         * Activated
         */
        $class = '';
        $inActi = !empty($statusView[$userView['UserView']['id']]) && ($statusView[$userView['UserView']['id']]['activated'] == '1') ? true : false;
        if ($inActi == true) {
            $class = ' wd-update-default';
        }
        $data['activated'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Activated', true), array(
                    'controller' => 'user_views',
                    'action' => 'toggle_status_views_activity', 'activated', $userView['UserView']['id'], empty($class)), array(
                    'class' => 'wd-update' . $class)) . '</div></div>';
        /**
         * Not activated
         */
        $class = '';
        $inNotActi = (!empty($statusView[$userView['UserView']['id']]) && ($statusView[$userView['UserView']['id']]['not_activated'] == 1)) ? true : false;
        if ($inNotActi == true) {
            $class = ' wd-update-default';
        }
        $data['not_activated'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Not activated', true), array(
                    'controller' => 'user_views',
                    'action' => 'toggle_status_views_activity', 'not_activated', $userView['UserView']['id'], empty($class)), array(
                    'class' => 'wd-update' . $class)) . '</div></div>';
        /**
         * Activated and not activated
         */
        $class = '';
        $inBoth = !empty($statusView[$userView['UserView']['id']]) && ($statusView[$userView['UserView']['id']]['activated_and_not_activated'] == 1) ? true : false;
        if ($inBoth == true) {
            $class = ' wd-update-default';
        }
        $data['activated_and_not_activated'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('Activated and not activated', true), array(
                    'controller' => 'user_views',
                    'action' => 'toggle_status_views_activity', 'activated_and_not_activated', $userView['UserView']['id'], empty($class)), array(
                    'class' => 'wd-update' . $class)) . '</div></div>';
    }
    if ($isAdmin) {
        $class = '';
        if (!empty($userView['UserView']['public'])) {
            $class = ' wd-update-default';
        }
        $data['public'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('public', true), array(
                    'controller' => 'user_views',
                    'action' => 'toggle_public',$model, $userView['UserView']['id'], empty($class)), array(
                    'class' => 'wd-update' . $class)) . '</div></div>';
    } else {
        $data['public'] = '<div style="margin: 0 auto !important; width: 54px;"><div class="wd-bt-big">' . $this->Html->link(__('public', true), 'javascript:void(0)', array(
                    'class' => 'wd-update' . ($userView['UserView']['public'] ? ' wd-update-default' : ''))) . '</div></div>';
    }


    $data['action.'] = !$isAdmin && $userView['UserView']['public'] ? '<span></span>' : '<div style="margin: 0 auto !important; width: 54px;">' . $this->Html->link(__('Edit', true), array(
                'action' => 'edit', $userView['UserView']['id']), array('class' => 'wd-edit')) . '<div class="wd-bt-big" style="margin-left: 2px;">' . $this->Html->link(__('Delete', true), array(
                'action' => 'delete',$model, $userView['UserView']['id']), array(
                'class' => 'wd-hover-advance-tooltip'), sprintf(__('Delete?', true), $userView['UserView']['name'])) . '</div></div>';
    $dataView[] = $data;
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
                rowHeight: 33,
                frozenColumn: 1
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
                    if (column.id === "no." || column.id === "action." || column.id === "default_view" || column.id === "public" || column.id === "progress_view" || column.id === "oppor_view" || column.id === "archived_view" || column.id === "model_view" ||  column.id ==='activated'||  column.id ==='not_activated'||  column.id ==='activated_and_not_activated') {
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
			$parent.data('slickgrid',grid);
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
            $(grid.getHeaderRow()).delegate(":input", "change keyup", function (e) {
                var text = $(this).val();
                if( text != '' ){
                    $(this).parent().css('border', 'solid 2px orange');
                } else {
                    $(this).parent().css('border', 'none');
                }
            });
            dataView.beginUpdate();
            dataView.setItems(data);
            dataView.setFilter(filter);
            dataView.endUpdate();
            //    grid.autosizeColumns();
            updateHeaderRow();
        });
        $('#FilterModel').change(function(){
            $('#FilterModel option').each(function(){
                if($(this).is(':selected')){
                    var id = $('#FilterModel').val();
                    window.location = ('/user_views/index?model=' +id);
                }
            });
        });
        history_reset = function(){
            var check = false;
            $('.multiselect-filter').each(function(val, ind){
                var text = '';
                if($(ind).find('input').length != 0){
                    text = $(ind).find('input').val();
                } else {
                    text = $(ind).find('span').html();
                    if( text == "<?php __('-- Any --');?>" || text == '-- Any --'){
                        text = '';
                    }
                }
                if( text != '' ){
                    $(ind).css('border', 'solid 2px orange');
                    check = true;
                } else {
                    $(ind).css('border', 'none');
                }
            });
            if(!check){
                $('#reset-filter').addClass('hidden');
            } else {
                $('#reset-filter').removeClass('hidden');
            }
        }
        resetFilter = function(){
            // HistoryFilter.stask = '{}';
            // HistoryFilter.send();
            // $('.multiselect-filter').each(function(val, ind){
                // if($(ind).find('input').length != 0){
                    // $(ind).find('input').val('');
                // } else {
                    // $(ind).find('span').html("<?php __('-- Any --');?>");
                // }
                // $(ind).css('border', 'none');
                // $('#reset-filter').addClass('hidden');
            // });
            // setTimeout(function(){
                // location.reload();
            // }, 500);
			ControlGrid = $('#project_container').data('slickgrid');
			$('.multiselect-filter input').val('').trigger('change');
			$('.multiSelectOptions input[type="checkbox"]').prop('checked', false).trigger('change');
			ControlGrid.setSortColumn();
			$('input[name="project_container.SortOrder"]').val('').trigger('change');
			$('input[name="project_container.SortColumn"]').val('').trigger('change');
        }
    })(jQuery);
</script>
