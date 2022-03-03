<?php

echo $html->css('jqwidgets/jqx.base'); ?>
<?php echo $html->css('dropzone.min'); ?>
<?php echo $html->css(array('jquery.fancybox')); 
echo $html->script(array('jquery.fancybox.pack'));  ?>
<?php echo $html->script('jqwidgets/jqxcore'); ?>
<?php echo $html->script('jqwidgets/jqxsortable'); ?>
<?php echo $html->script('jqwidgets/jqxkanban'); ?>
<?php echo $html->script('jqwidgets/jqxdata'); ?>
<?php echo $html->script('jqwidgets/demos'); ?>
<?php echo $html->script('dropzone.min'); ?>
<?php echo $html->script('history_filter'); ?>
<?php echo $html->script('jquery-ui.multidatespicker');  ?>
<?php echo $html->css('add_popup'); ?>

<?php 
echo $this->element('dialog_detail_value');
echo $html->css('slick'); 
echo $html->css('slick-theme');  
echo $html->css('preview/kaban-task');  
echo $html->script('slick.min'); 
$read_only = !(($canModified && !$_isProfile) || $_canWrite) ? 1 : 0;
$company_id = !empty($employee_info['Company']['id']) ? $employee_info['Company']['id'] : 0;
$titleMenu = ClassRegistry::init('Menu')->find('first', array(
	'recursive' => -1,
	'conditions' => array(
		'company_id' => $company_id,
		'model' => 'project',
		'controllers' => 'project_tasks',
		'functions' => 'index'
	),
	'fields' => array('name_eng','name_fre')
));
$langCode = Configure::read('Config.langCode');
$language = ($langCode == 'fr') ? 'name_fre' : 'name_eng';
$widget_title = !empty( $widget_title) ? $widget_title : $titleMenu['Menu'][$language];
$widget_title = !empty( $widget_title) ? $widget_title : __('Tasks', true); 
$listEmployeeAssign = $listPCAssign = array();
$canModified = isset( $canModified ) ? $canModified : '0';
$check_consumed = (!empty($adminTaskSetting) && (($adminTaskSetting['Consumed'] == 1)||($adminTaskSetting['Manual Consumed'] == 1))) ? 1 : 0;
$show_workload = !(empty($adminTaskSetting['Workload'])) ? $adminTaskSetting['Workload'] : 0;
// debug($check_manual_consumed);exit;
$define_task_colors = array(
	'blue' => '#217FC2', // Blue
	'red' => '#E94754', // Red
	'green' => '#6EAF79', // Green
);
$listPhases = array();
$list_phases = array();
foreach ($phasePlans as $Phase) {
	$part_id = $Phase['ProjectPhasePlan']['project_part_id'];
	if( !empty($part_id) && !empty($parts[$part_id ]) ){
		$part_name = $parts[$Phase['ProjectPhasePlan']['project_part_id']];
		$Phase['ProjectPhase']['name'] = $Phase['ProjectPhase']['name'] . ' (' . $part_name . ')';
	}
	$listPhases[$Phase['ProjectPhasePlan']['id']] = array_merge($Phase['ProjectPhasePlan'], $Phase['ProjectPhase']);
}
$list_phases = !empty($listPhases) ? Set::combine($listPhases, '{n}.id', '{n}.name') : array();
$list_phase_colors = !empty($listPhases) ? Set::combine($listPhases, '{n}.id', '{n}.color') : array();

$list_phase_date = array();
foreach( $phasePlans as $phase){
	$phase = $phase['ProjectPhasePlan'];
	$list_phase_date[$phase['id']] = array(
		'phase_planed_start_date' => ( ( $phase['phase_planed_start_date'] != '0000-00-00') ? date( 'd-m-Y', strtotime( $phase['phase_planed_start_date'])) : '' ),
		'phase_real_start_date' => ( ( $phase['phase_real_start_date'] != '0000-00-00') ? date( 'd-m-Y', strtotime( $phase['phase_real_start_date'])) : '' ),
		'phase_planed_end_date' => ( ( $phase['phase_planed_end_date'] != '0000-00-00') ? date( 'd-m-Y', strtotime( $phase['phase_planed_end_date'])) : '' ),
		'phase_real_end_date' => ( ( $phase['phase_real_end_date'] != '0000-00-00') ? date( 'd-m-Y', strtotime( $phase['phase_real_end_date'])) : '' ),
	);
}

foreach ($listAssignTask as $key => $value) {
	$value = $value['Employee'];
    if($value['is_profit_center']){
        $listPCAssign[$value['id']] = $value['name'];
    }else{
        $listEmployeeAssign[$value['id']] = !empty($value['name']) ? $value['name'] : '';
    }
}

function multiSelect($_this, $args){
	$fieldName = !empty( $args['fieldName']) ? $args['fieldName'] : 'multiselect
	';
	$fielData = !empty( $args['fielData']) ? $args['fielData'] : array();
	$textHolder = !empty( $args['textHolder']) ? $args['textHolder'] : __('Select');
	$pc = !empty( $args['pc']) ? $args['pc'] : array();
	$id = !empty( $args['id']) ? $args['id'] : 'multiselect-'.$fieldName;
	$cotentField = '';
	$cotentField = '<div class="wd-multiselect multiselect multiselect-pm" id="'.$id.'" >
	<a href="javascript:void(0);" class="wd-combobox wd-project-manager"><p style="position: absolute; color: #c6cccf">'. $textHolder .'</p></a>
	<div class="wd-combobox-content '. $fieldName .'" style="display: none;">
	<div class="context-menu-filter"><span><input type="text" class="wd-input-search" placeholder="Rechercher..." rel="no-history"></span></div><div class="option-content">';
	foreach($fielData as $idPm => $namePm):
		$avatar = '<img src="' . $_this->UserFile->avatar($idPm) . '" />';
		$cotentField .= '<div class="projectManager wd-data-manager wd-group-' . $idPm . '">
			<p class="projectManager wd-data">
				<a class="circle-name" title="' . $fielData[$idPm] . '"><span data-id = "'. $idPm . '-0">'. $avatar .'</span></a>' .
				$_this->Form->input($fieldName, array(
					'label' => false,
					'div' => false,
					'type' => 'checkbox',
					'name' => 'data['. $fieldName .'][]',
					'value' => $idPm . '-0')) .'
				<span class="option-name" style="padding-left: 5px;">' . $namePm . '</span>
			</p>
		</div>';
	endforeach;
	if(!empty($pc)): 
		foreach($pc as $idPm => $namePm):
			$cotentField .= '<div class="projectManager wd-data-manager wd-group-' . $idPm . '">
				<p class="projectManager wd-data">
					<a class="circle-name" title="' . $pc[$idPm] . '"><span data-id = "'. $idPm . '-1"><i class="icon-people"></i></span></a> '.
					 $_this->Form->input($fieldName, array(
						'label' => false,
						'div' => false,
						'type' => 'checkbox',
						'name' => 'data['. $fieldName .'][]',
						'value' => $idPm . '-1')) .'
					<span class="option-name" style="padding-left: 5px;">' . $namePm . '</span>
				</p>
			</div>';
		endforeach; 
	endif;
	$cotentField .= '</div></div></div>';
	return $cotentField;
}
function get_list_phase_option($phases){
    ob_start();
    $phase_id='';
    $phase_name = '';
    foreach ($phases as $phase) {
        $phase_id = $phase['ProjectPhasePlan']['id'];
        $phase_name = $phase['ProjectPhase']['name'];
        echo '<option value='. $phase_id .'>'. $phase_name .'</option>';
    }
    return ob_get_clean();
}
function get_list_project_status($list_project_status){
    ob_start();
    $status_id='';
    $statusname = '';
    foreach ($list_project_status as $key => $status) {
        echo '<option value='. $key .'>'. $status .'</option>';
    }
    return ob_get_clean();
}
function task_status_select($task = null, $listStatus = array()){
	if( empty($task)) return;
	ob_start();
	$task_id = $task['id'];
	$currentStatus = $task['task_status_id'];
	$enddate = $task['task_end_date'];
	$today = strtotime(date('d-m-Y'));
	?>
	<div class="task-status">
		<div class="status_texts">
				<?php 
				$index = 0;
				foreach($listStatus as $id => $status){ 
					$color = 'status_blue';
					if( $status['status'] == 'CL') $color = 'status_green';
					elseif( $today > $enddate )  $color = 'status_red';
					?>
			<span class="status_item status_item_<?php echo $status['id']; ?> status_text <?php if( $status['id'] == $currentStatus) echo 'active';?> <?php echo $color;?>" data-value="<?php echo $status['id']; ?>" data-text="<?php echo $status['name']; ?>" data-index="<?php echo $index++;?>"><?php echo $status['name']; ?></span>
				<?php } ?>
		</div>
		<div class="status_dots">
				<?php 
				$index = 0;
				foreach($listStatus as $id => $status){ 
					$color = 'status_blue';
					if( $status['status'] == 'CL') $color = 'status_green';
					elseif( $today > $enddate )  $color = 'status_red';
					?>
			<a href="javascript:void(0);" class="status_item status_item_<?php echo $status['id']; ?> status_dot <?php if( $status['id'] == $currentStatus) echo 'active';?> <?php echo $color;?>" data-value="<?php echo $status['id']; ?>" title="<?php echo $status['name']; ?>" data-index="<?php echo $index++;?>" data-taskid="<?php echo $task_id; ?>"></a>
				<?php } ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
function  list_employee_assigned($task, $UserFileHelper){
	ob_start();
	?>
	<div class="task-list-assigned">
			<?php foreach ($task['assigned'] as $assigned){
				$e_id = $assigned['reference_id'];  // employee id OR PC id
				if( $assigned['is_profit_center'] == 0){
					echo $UserFileHelper->avatar_html($e_id);
				}else{ // is PC
					$e_id .= '-1';
					echo '<div class="circle-name" title="'. $listEmployeeName[$e_id]['name'] .'"><span data-id="'. $assigned['reference_id'] .'-1"><i class="icon-people"></i></span></div>';
				}
			}
			?>
	</div>
	<?php
	return ob_get_clean();
}
function draw_line_progress_byday($task ){
	ob_start();
	$s_date = !empty( $task['task_start_date'] ) ? $task['task_start_date'] : 0;
	$e_date = !empty( $task['task_end_date'] ) ? $task['task_end_date'] : 0;
	$today = time();
	if( $e_date == $s_date )  $value = 100;
	else $value = intval( ($today - $s_date) / ($e_date - $s_date) * 100 );
    $_css_class = ($value < 100) ? 'green-line': 'red-line';
	$display_value = max($value, 0);
	$display_value = min($value, 100);
    ?>
<div class="progress-slider <?php echo $_css_class;?>" data-value="<?php echo $value;?>">
    <div class="progress-holder">
        <div class="progress-line-holder"></div>
    </div>
    <div class="progress-value" style="width:<?php echo $display_value;?>%;">
        <div class="progress-line"></div>
        <div class="progress-number"> <div class="text" style="margin-left: -<?php echo round($display_value);?>%;"><?php echo round($display_value);?>%</div> </div>
    </div>
</div>
	<?php
    return ob_get_clean();
}
function draw_line_progress_by_consumed($value=0){
	ob_start();
    $_css_class = ($value <= 100) ? 'green-line': 'red-line';
	$display_value = min($value, 100);
    ?>
<div class="progress-slider <?php echo $_css_class;?>" data-value="<?php echo $value;?>">
    <div class="progress-holder">
        <div class="progress-line-holder"></div>
    </div>
    <div class="progress-value" style="width:<?php echo $display_value;?>%;" title="<?php echo $display_value;?>%">
        <div class="progress-line"></div>
        <div class="progress-number"> <div class="text" style="margin-left: -<?php echo round($display_value);?>%;" ><?php echo round($display_value);?>%</div> </div>
    </div>
</div>
	<?php
    return ob_get_clean();
}

/** function display_task_footer
* by Dai Huynh
* @param $task: Task info
	* estimated: workload
	* [special] : is special consumed 
	* [special_consumed] : value consumed special 
	* [consumed] : consumed
	* [task_start_date] => 1486335600 : timestampe start day 
    * [task_end_date] => 1486422000
* @output
	*  Workload
	*  Consumed: if consumed > workload: red
		* if special consumed: purple
	* Progress: (curentime - startday) / (endday -  start day) * 100%
	* update 19-03-2018 
		* Comment count
		* Attachment Count
		* Edit Task
* @logic
    * Actived Manual Consumed = YES
	* consumed =  manual_consumed
	* overload = manual_overload
	* progress = (manual_consumed * 100) / (estimated + manual_overload)
*/
function display_task_footer($task, $canModified, $check_consumed, $check_manual_consumed, $show_workload){
	// ob_clean();
	// debug('task');
	// debug($task);
	// exit;
	ob_start();
	$estimated = !empty( $task['estimated']) ? $task['estimated'] : 0;
	if(!$check_manual_consumed){
		$consumed = !empty($task['special']) ? ( isset( $task['special_consumed']) ? $task['special_consumed'] : 0 ) : ( isset($task['consumed']) ? $task['consumed'] : 0 ) ;
	} else{
		$consumed = ($task['manual_consumed']);
	}
	$progress = 100;
	// if( $estimated != 0) $progress = ($consumed / $estimated) * 100;
	// elseif( empty( $consumed) ) $progress = 0;
	
	if($estimated != 0){
		if(!$check_manual_consumed){
			if($task['overload'] != 0){
				$progress = ($consumed / ($estimated + $task['overload'])) * 100;
			}else {
				$progress = ($consumed / $estimated) * 100;
			}
		} else {
			if($task['manual_overload'] != 0){
				$progress = ($consumed / ($estimated + $task['manual_overload'])) * 100;
			}else{
				$progress = ($consumed / $estimated) * 100;
			}
		}
	} elseif (empty( $consumed)) {
		$progress = 0;
	}
	
	$icon = array(
		'z0g_msg' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
			<defs>
				<style>
				  .cls-1 {
					fill: #666;
					fill-rule: evenodd;
				  }
				</style>
			  </defs>
			  <path id="Z0gMSG" class="cls-1" d="M683.124,30h-6.249a0.625,0.625,0,1,0,0,1.25h6.249A0.625,0.625,0,1,0,683.124,30ZM680,20c-5.523,0-10,3.918-10,8.75a8.375,8.375,0,0,0,3.75,6.824V40l5.12-2.56c0.371,0.036.747,0.059,1.13,0.059,5.523,0,10-3.917,10-8.749S685.523,20,680,20Zm0,16.25c-1.435,0-1.25,0-1.25,0L675,38.125V34.864a7.213,7.213,0,0,1-3.751-6.114c0-4.142,3.918-7.5,8.751-7.5s8.749,3.358,8.749,7.5S684.832,36.25,680,36.25Zm4.374-10h-8.749a0.625,0.625,0,1,0,0,1.25h8.749A0.625,0.625,0,1,0,684.374,26.25Z" transform="translate(-670 -20)"/>
		</svg>',
		'document' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
			<defs>
				<style>
				  .cls-1 {
					fill: #666;
					fill-rule: evenodd;
				  }
				</style>
			</defs>
			<path id="document" class="cls-1" d="M2023.69,590.749h-7.38a0.625,0.625,0,0,0,0,1.25h7.38A0.625,0.625,0,0,0,2023.69,590.749Zm0-3.75h-7.38a0.626,0.626,0,0,0,0,1.251h7.38A0.626,0.626,0,0,0,2023.69,587Zm4.31,10h0V582.624a0.623,0.623,0,0,0-.62-0.624h-14.76a0.623,0.623,0,0,0-.62.624v18.75a0.624,0.624,0,0,0,.62.624h10.46v0l4.92-5v0Zm-4.92,3.459V597h3.4Zm3.69-4.71h-4.92v5h-8a0.623,0.623,0,0,1-.62-0.624v-16.25a0.625,0.625,0,0,1,.62-0.625h12.3a0.625,0.625,0,0,1,.62.625v11.874Z" transform="translate(-2010 -582)"/>
		</svg>',
		'edit' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
			<defs>
				<style>
				  .cls-1 {
					fill: #666;
					fill-rule: evenodd;
				  }
				</style>
			</defs>
			<path id="EDIT" class="cls-1" d="M6593.86,260.788c-0.75.767-11.93,11.826-12.4,12.3a0.7,0.7,0,0,1-.27.157c-0.75.224-5.33,1.7-5.37,1.719a0.693,0.693,0,0,1-.2.03,0.625,0.625,0,0,1-.44-0.184,0.636,0.636,0,0,1-.16-0.642c0.01-.044,1.37-4.478,1.64-5.477a0.627,0.627,0,0,1,.16-0.285s11.99-12,12.34-12.343a3.636,3.636,0,0,1,2.42-1.056,3.186,3.186,0,0,1,2.23.981,3.347,3.347,0,0,1,1.18,2.356A3.455,3.455,0,0,1,6593.86,260.788Zm-17.36,12.665c1.21-.39,3.11-1.045,3.97-1.322a3.9,3.9,0,0,0-.94-1.565,4.037,4.037,0,0,0-1.78-1.087C6577.46,270.444,6576.85,272.274,6576.5,273.453Zm3.92-3.789a4.95,4.95,0,0,1,1.07,1.6c2.23-2.2,7.88-7.791,10.33-10.231a3.894,3.894,0,0,0-1.02-1.875,3.944,3.944,0,0,0-1.84-1.1c-2.41,2.409-8.16,8.167-10.37,10.372A5.418,5.418,0,0,1,6580.42,269.664Zm12.51-12.755a1.953,1.953,0,0,0-1.35-.63,2.415,2.415,0,0,0-1.53.69c-0.01.011-.06,0.055-0.09,0.09a5.419,5.419,0,0,1,1.73,1.194,5.035,5.035,0,0,1,1.14,1.763,1.343,1.343,0,0,0,.12-0.119,2.311,2.311,0,0,0,.78-1.534A2.168,2.168,0,0,0,6592.93,256.909Z" transform="translate(-6575 -255)"/>
		</svg>'
	);
	?>
<div class="footer-top clear-fix">
	<?php if($show_workload) { ?>
    <div class="task-workload">
        <p class="label"> <?php __('Workload');?> </p>
        <span class="value"> <?php printf('%05.2f ' .__('M.D', true), $estimated );?></span>
    </div>
	<?php } ?>
	<?php if (!empty($check_consumed) && $check_consumed == 1){ ?>
		<div class="task-consumed <?php if( !empty( $task['special'])) echo 'special';?> <?php if($consumed > $estimated) echo 'has-overload';?>">
			<p class="label"> <?php __('Consumed');?> </p>
			<span class="value"> <?php printf('%05.2f ' .__('M.D', true), $consumed );?></span>
		</div>
		<div class="task-progress" data-progress="<?php echo $progress;?>">
			<?php  echo draw_line_progress_by_consumed($progress);	?>
		</div>
	<?php } ?> 
</div>
<div class="footer-bottom">
    <div class="wd-task-actions">
			<?php 
				$comment_count = $task['comment_count'];
				$comment_read = $task['read_status'];
				$attachment_count = $task['attachment_count'];
				$attach_read = $task['attach_read_status'];
				$is_nct = ''; 
				$edit_action = 'openEditTask.call(this);';
				if($task['is_nct'] == 1){
					$is_nct = 'task-nct';
					$edit_action = 'openEditTaskNCT(this);';
				}
			?>
        <a href="javascript:void(0)" onclick="popupTaskComment.call(this);" class="wd-task-action task-comment <?php echo (!empty($comment_count) ? 'has-value has-comment' : 'no-comment no-value');?> <?php echo (!empty($comment_read) ? 'read' : '');?>" data-taskid="<?php echo $task['id'];?>" data-project-id="<?php echo $task['project_id'];?>"><?php echo $icon['z0g_msg'];?><span><?php echo $comment_count;?></span></a>
        <a href="javascript:void(0)" onclick="openPopupAttachment.call(this);" class="wd-task-action task-attachment <?php echo (!empty($attachment_count) ? 'has-value has-attachment' : 'no-attachment no-value');?> <?php echo (!empty($attach_read) ? 'read' : '');?>" data-taskid="<?php echo $task['id'];?>" data-project-id="<?php echo $task['project_id'];?>"><?php echo $icon['document'];?><span><?php echo $attachment_count;?></span></a>
		<!-- Update by QuanNV. Them dieu kien check hien thi icon edit task-->
		<?php if( !empty($canModified)){?>
			<a href="javascript:void(0)" onclick="<?php echo $edit_action; ?>" class="<?php echo $is_nct; ?> wd-task-action task-edit" data-taskid="<?php echo $task['id'];?>" data-project-id="<?php echo $task['project_id'];?>"><?php echo $icon['edit'];?><span></span></a>
		<?php } ?>
    </div>
</div>
	<?php
	return ob_get_clean();
}
/* END function display_task_footer */
?>
<div class="wd-widget project-task-widget">
    <div class="wd-widget-inner">
        <div class="widget-title">
            <div class="slick-grid-action wd-hide">
                <a class="slick-prev slick-control" href="javascript:void(0);" ><span>
                        <img src="/img/new-icon/arrow-left-gray.png">
                        <img class="img-hover" src="/img/new-icon/arrow-left-blue.png">
                    </span></a>
                <a class="slick-next slick-control" href="javascript:void(0);"><span>
                        <img src="/img/new-icon/arrow-right-gray.png">
                        <img class="img-hover" src="/img/new-icon/arrow-right-blue.png">
                    </span></a>
            </div>
            <h3 class="title"> <?php echo $widget_title; ?> </h3>
            <!-- <a href="javascript:void(0);" onclick="wd_add_task(this)" class="wd-add-task"></a> -->
            <div class="widget-action">
				<?php if(!$read_only && $on_newdesign_projecttask == 1){ ?>
                <a href="javascript:void(0);" class="btn-add" title="<?php echo __('Create a task', true);?>" onclick="wd_task_add_new(this)"></a>
				<?php } ?>
                <a href="javascript:void(0);" onclick="wd_tasks_expand(this)" class="primary-object-expand"><img src="/img/new-icon/expand_white.png"></a>
                <a href="javascript:void(0);" onclick="wd_tasks_collapse(this)" class="primary-object-collapse" style="display: none;"><img src="/img/new-icon/close-light.png"></a>
            </div> 
        </div>
        <div class="kanban-box">
			<?php echo $this->Form->create('KanbanFilter', array(
					'type' => 'GET',
					'url' => $this->Html->url(),
					'id' => 'KanbanFilter',
				));
				$filter_time = array(
					"day" => __("Day", true),
					"week" => __("Week", true),
					"month" => __("Month", true),
					"late" => __("Late", true),
				);
				
			?>
			<div class="wd-flex-title">
			<div class="wd-task-title wd-title">
				<?php echo $this->Form->input('filter_by_time', array(
					'type'=> 'select',
					'id' => 'filter_by_time',
					'label' => false,
					'required' => false,
					'autocomplete' => 'off',
					'onchange'=> 'filterTask(this);',
					'options' => $filter_time,
					'empty' => __("All", true),
				));
				echo $this->Form->input('filter_task_title', array(
					'type'=> 'text',
					'id' => 'filter_task_title',
					'label' => false,
					'required' => false,
					'autocomplete' => 'off',
					'onchange'=> 'filterTask(this);',
					'placeholder' => __('Filter by title', true),
				));
				
				?>
				<div class="btn filter_submit" onclick="filterTask(this);" title="<?php __('Search');?>">
					<img src="<?php echo $this->Html->url('/img/new-icon/search.png');?>" all="search">
				</div>
				<a href="javascript:void(0);" class="btn btn-reset-filter hidden" id="clean-filters" style="display: none;" onclick="resetFilter();" title="<?php __('Reset filter') ?>"></a>
			</div>
			<div class="wd-list-assign">
				<div class="wd-list-assign-inner">
					<div class="box-ellipsis list-all-assigned" id="list-all-assigned">
						<?php if(!empty($listAvatar)){ ?>
						<ul>
							<?php foreach ($listAvatar as $key => $value) {
								foreach( $value as $_id => $_name){
									$avt = '<i class="icon-people"></i>';
									$is_pc = 1;
									if($key == 'listEmployeeAssigned'){
										$avt = '<img src="' . $html->url('/img/avatar/'.$_id.'.png') .'" alt="avatar">';
										$is_pc = 0;
									}
									$emp = $_id . '-' . $is_pc;
									?>
									<li data-emp="<?php echo $emp;?>" class="<?php echo ( $is_pc ? 'assign-team' : '');?>">
										<div class="wd-input-checkbox-custom"  title="<?php echo $_name?>" >
											<input type="checkbox" onchange="filterTask(this);" name="data[Filter][employee][<?php echo $emp;?>]" class="filter-employee wd-hide" id="filter-employee-<?php echo $emp;?>" data-id="<?php echo $emp;?>">
											<label for="filter-employee-<?php echo $emp;?>">
												<span class="circle-name"><?php echo $avt;?></span>
											</label>
										
										</div>
									</li>
									
								<?php
								}									
							   
							} ?>
						</ul>
						<?php } ?>
					</div>
				</div>
			</div>
			</div>
			<?php echo $this->Form->end(); ?>
			<div class="wd-kanban-container">
				<div id="kanban" class="kanban-task"></div>
			</div>
        </div>
        <div class="widget_content color-background">
            <div id="widget-task">
                <div class="slides">
        			<?php if(!empty($list_tasks)){
        				foreach ($list_tasks as $status_id => $project_task) { 
						$in_progress = ($list_org_project_status[$status_id]['status'] == "IP");
						?>
                    <div class="item-slider" data-status-id = "<?php echo $status_id; ?>">
                        <h4 class="log-title"> <?php echo __($task_status[$status_id], true); ?>
                            <a href="javascript:void(0);" class="add-new-task"></a>
                        </h4>
                        <ul class="project-task-list status-<?php echo $status_id; ?><?php echo $in_progress ? ' in_progress_column' : '';?>">
					            	<?php 
									if(!empty($project_task)){
										foreach ($project_task as $key => $task) { 
										$late = $task['late'];
										?>
                            <li class="task-<?php echo $task['id']; ?>">
                                <div class="task-item" data-taskid="<?php echo $task['id'];?>">
                                    <div class="task-head">
										<?php echo list_employee_assigned($task, $this->UserFile);?>
                                        <span class="task-time<?php echo $late ? ' task_late' : '' ;?>"><?php echo date('d ', $task['task_end_date']). __(date('F', $task['task_end_date']), true).date(' Y', $task['task_end_date']) ?></span>
													<?php if(!$read_only) { ?>
                                        <a href="javascript:void(0)" class="log-field-edit wd-hide"><img src="/img/new-icon/edit-task.png" /></a>
													<?php } 
													// Ticket 336 Disable edit
													?>

													<?php echo task_status_select($task, $list_org_project_status);?>
                                    </div>
                                    <div class="task-title"><?php echo $task['task_title'];  ?></div>
									<?php if(!empty($task['project_planed_phase_id']) && !empty($list_phases)){?>
									<div class="task-item-phase">
										<?php $phase_color = $list_phase_colors[$task['project_planed_phase_id']]; ?>
										<span style="background-color:<?php echo $phase_color;?>;"></span><?php echo $list_phases[$task['project_planed_phase_id']]; ?>
									</div>
									<?php } ?>
                                    <div class="task-footer clearfix">
									<?php echo display_task_footer( $task, $canModified, $check_consumed, $check_manual_consumed, $show_workload); ?>
                                    </div>
                                </div>
                            </li>

										<?php } 
									} ?>
                        </ul>
                        <div style="display: none" class="add-new-task" id="widget-task-add-new-task">
                            <div class="add-task-form">
                            </div>
                        </div>
                    </div>
			            <?php } 
			        }?>
                </div>
            </div>
        </div>
        <div id="template_upload" class="template_upload wd-full-popup" style="display: none;">
            <div class="wd-popup-inner">
                <div class="new-task-popup loading-mark wd-popup-container" style="width: 580px;">
                    <div class="wd-popup-head clearfix"> 
                        <h4 class="active"><?php echo __('File upload(s)', true)?></h4> 
                        <a href="javascript:void(0);" class="close_template_add_task wd-close-popup" onclick="cancel_popup(this)"><img title="Close" src="<?php echo $html->url('/img/new-icon/close.png'); ?>"></a>
                    </div>
                    <div class="new-task-popup-content wd-popup-content">
						<?php 
						echo $this->Form->create('Upload', array(
							'type' => 'POST',
							'url' => array('controller' => 'kanban','action' => 'update_document'),
							'class' => 'form-style-2019',
							'id' => 'Upload',
						));
						?>
                        <div id="content_comment">
                            <div class="append-comment"></div>
                        </div> 
						<?php if( !$read_only) { ?>
							<div class="trigger-upload"><div id="upload-popup" method="post" action="/kanban/update_document/" class="dropzone" value="" >
								</div></div>
							<?php echo $this->Form->input('url', array(
								'class' => 'not_save_history',
								'label' => array(
									'class' => 'label-has-sub',
									'text' =>__('URL Link',true),
									'data-text' => __('(optionnel)', true),
									'required' => true,
								),
								'type' => 'text',
								'id' => 'newDocURL',  
								'placeholder' => __('https://', true)));    
							?>                    
							<input type="hidden" name="data[Upload][return_ajax]" rel="no-history" value="true" id="UploadReturnAjax">
							<input type="hidden" name="data[Upload][id]" rel="no-history" value="" id="UploadId">
							<input type="hidden" name="data[Upload][project_id]" rel="no-history" value="" id="UploadProjectId">
							<div class="wd-submit">
								<button type="submit" class="btn-form-action btn-ok btn-right" id="btnSave">
									<span><?php __('Upload');;?></span>
								</button>
								<a class="btn-form-action btn-cancel" id="reset_button" href="javascript:void(0);" onclick="cancel_popup(this);">
									<?php __('Cancel');?>
								</a>
							</div>
						<?php } ?>
						<?php echo $this->Form->end(); ?>
                    </div>
                </div>
            </div>
        </div>
        <div id="wd-task-comment-dialog" style="height: 440px; width: 320px;display: none;" class="wd-comment-dialog">
            <div class="add-comment"></div>
            <div class="content_comment" style="min-height: 50px">
                <div class="append-comment"></div>
            </div>

        </div>

        <!-- New task Popup -->
        <div id="template_wg_add_task" class="wd-full-popup" style="display: none;">
            <div class="wd-popup-inner">
                <div class="edit_task-popup loading-mark wd-popup-container" style="width: 580px;">
                    <div class="wd-popup-head clearfix"> 
                        <h4 class="active">
							<?php __('Add new task');?>
                        </h4> 
                        <a href="javascript:void(0);" class="close_template_add_task wd-close-popup" onclick="cancel_popup(this)"><img title="<?php __('Close');?>" src="<?php echo $this->Html->url(array(
							'controller' => 'img',
							'action' => 'new-icon',
							'close.png'
						));?>"></a>
                    </div>
                    <div class="new-task-popup-content wd-popup-content">
                        <!-- form -->
					<?php 
						echo $this->Form->create('ProjectTask', array(
							'type' => 'POST',
							'url' => array('controller' => 'project_tasks_preview', 'action' => 'add_new_task_popup'),
							'class' => 'form-style-2019',
							'id' => 'NewProjectTask',
							)							
						);
						?>
                        <p style="color: red" class="alert-message"></p>
						<?php 
						// echo $this->Form->input('return', array(
							// 'type'=> 'hidden',
							// 'value' => $this->Html->url(),
							
						// ));
						echo $this->Form->input('project_id', array(
							'type'=> 'hidden',
							'value' => $project_id,
						));
						
						echo $this->Form->input('task_title', array(
							'type'=> 'text',
							'id' => 'newTaskNameIndex',
							'label' => __('Task name', true),
							'required' => true,
							'rel' => 'no-history',
							'div' => array(
								'class' => 'wd-input label-inline'
							)
						));
						echo $this->Form->input('project_planed_phase_id', array(
							'type'=> 'select',
							'id' => 'newTaskPhase',
							'label' => __('Phase', true),
							'empty' => __('--Select--', true),
							'options' => $list_phases,
							'required' => true,
							'rel' => 'no-history',
							'div' => array(
								'class' => 'wd-input'
							),
							'onchange' => 'new_task_phase_changed(this)',
						));
						?>
                        <div class="wd-row">
                            <div class="wd-col wd-col-sm-6">
                                <div class="wd-input wd-area wd-none ">
                                    <label><?php __('Assign to');?></label>
									<?php 
									echo multiSelect($this, array(
										'fieldName' => 'task_assign_to_id',
										'fielData' => $listEmployeeAssign ,
										'textHolder' => __d(sprintf($_domain, 'Project_Task'), 'Assigned To', true),
										'pc' => $listPCAssign,
										'id' => 'list_task_assign_to'
									));
                                    ?>
                                </div>
                            </div>
                            <div class="wd-col wd-col-sm-6">
							<?php 
								echo $this->Form->input('task_status_id', array(
									'type'=> 'select',
									'id' => 'newTaskStatus',
									'label' => __('Status', true),
									'empty' => __('--Select--', true),
									'options' => $list_project_status,
									'required' => true,
									'rel' => 'no-history',
									'div' => array(
										'class' => 'wd-input'
									)
									
								));
								?>
                            </div>
                        </div>
                        <div class="wd-row">
                            <div class="wd-col wd-col-sm-6">
                                <div class="wd-input wd-area wd-none">
								<?php 				
									echo $this->Form->input('task_start_date', array(
										'type'=> 'text',
										'id' => 'newTaskStartDayIndex',
										'label' => __('Start date', true),
										'required' => true,
										'class' => 'wd-date',
										'onchange'=> 'new_task_validated(this);',
										'autocomplete' => 'off',
										'rel' => 'no-history',
										'div' => array(
											'class' => 'wd-input label-inline'
										)
										
									));
									?>
                                </div>
                            </div>
                            <div class="wd-col wd-col-sm-6">
							<?php 				
								echo $this->Form->input('task_end_date', array(
									'type'=> 'text',
									'id' => 'newTaskEndDayIndex',
									'label' => __('End date', true),
									'required' => true,
									'class' => 'wd-date',
									'autocomplete' => 'off',
									'onchange'=> 'new_task_validated(this);',
									'rel' => 'no-history',
									'div' => array(
										'class' => 'wd-input label-inline'
									)
									
								));
								?>
                            </div>
                        </div>
                        <div id="popup_template_attach" >
                            <div class="heading">

                            </div> 
                            <div class="trigger-upload">
                                <div id="wd-upload-popup" method="post" action="<?php echo $this->Html->url(array('controller' => 'project_tasks_preview', 'action' => 'add_new_task_popup')); ?>" class="dropzone" value="" >
                                </div>
                            </div>
                        </div>
                        <div class="wd-row wd-submit-row">
                            <div class="wd-col-xs-12">
                                <div class="wd-submit">
                                    <button type="submit" class="btn-form-action btn-ok btn-right" id="btnSave">
                                        <span><?php __('Save') ?></span>
                                    </button>
                                    <a class="btn-form-action btn-cancel" id="reset_button" href="javascript:void(0);" onclick="cancel_popup(this);">
										<?php echo __("Cancel", true); ?></span>
                                    </a>
                                </div>
                            </div>
                        </div>
						<?php echo $this->Form->end(); ?>
                        <!-- END  form -->
                    </div>				
                </div>				
            </div>
        </div>
        <!-- END New task Popup -->
        <!-- Edit task Popup -->
        <div id="template_edit_task_index" class="wd-full-popup" style="display: none;">
            <div class="wd-popup-inner">
                <div class="edit_task-popup loading-mark wd-popup-container">
                    <div class="wd-popup-head clearfix"> 
                        <h4 class="active">
							<?php __('Edit task');?>
                        </h4> 
                        <a href="javascript:void(0);" class="close_template_add_task wd-close-popup" onclick="cancel_popup(this)"><img title="<?php __('Close');?>" src="<?php echo $this->Html->url(array(
							'controller' => 'img',
							'action' => 'new-icon',
							'close.png'
						));?>"></a>
                    </div>
                    <div class="edit_task-popup-content wd-popup-content">
                        <!-- form -->
					<?php 
						echo $this->Form->create('ProjectTask', array(
							'type' => 'POST',
							'url' => array('controller' => 'project_tasks', 'action' => 'update_task'),
							'class' => 'form-style-2019',
							'id' => 'ProjectTask',
							)							
						);
						?>
                        <p style="color: red" class="alert-message"></p>
						<?php 
						// echo $this->Form->input('return', array(
							// 'type'=> 'hidden',
							// 'value' => $this->Html->url(),
							
						// ));
						if( $show_workload) { ?>
							<div class="wd-row">
								<div class="wd-col wd-col-sm-8">
						<?php } 
						echo $this->Form->input('id', array(
							'type'=> 'hidden',
							'value' => '',
							'id' =>'editTaskID'
						));
						echo $this->Form->input('task_title', array(
							'type'=> 'text',
							'id' => 'task_title',
							'label' => __('Task name', true),
							'required' => true,
							'rel' => 'no-history',
							'div' => array(
								'class' => 'wd-input label-inline'
							)
						));
						echo $this->Form->input('project_planed_phase_id', array(
							'type'=> 'select',
							'id' => 'toPhaseIndex',
							'label' => __('Phase', true),
							'empty' => __('--Select--', true),
							'options' => $list_phases,
							'required' => true,
							'rel' => 'no-history',
							'div' => array(
								'class' => 'wd-input'
							)					
						));
						?>
                        <div class="wd-row">
                            <div class="wd-col wd-col-sm-6">
                                <div class="wd-input wd-area wd-none ">
                                    <label><?php __d(sprintf($_domain, 'Project_Task'), 'Assigned To');?></label>
									<?php 
									echo multiSelect($this, array(
										'fieldName' => 'task_assign_to_id',
										'fielData' => $listEmployeeAssign ,
										'textHolder' => __d(sprintf($_domain, 'Project_Task'), 'Assigned To', true),
										'pc' => $listPCAssign,
										'id' => 'list_task_assign_to_edit'
									));
                                    ?>
                                </div>
                            </div>
                            <div class="wd-col wd-col-sm-6">
							<?php 
								echo $this->Form->input('task_status_id', array(
									'type'=> 'select',
									'id' => 'toStatus',
									'label' => __d(sprintf($_domain, 'Project_Task'), 'Status', true),
									'options' => $list_project_status,
									'required' => true,
									'rel' => 'no-history',
									'div' => array(
										'class' => 'wd-input'
									)
									
								));
								?>
                            </div>
                        </div>
                        <div class="wd-row">
                            <div class="wd-col wd-col-sm-6">
                                <div class="wd-input wd-area wd-none">
								<?php 				
									echo $this->Form->input('task_start_date', array(
										'type'=> 'text',
										'id' => 'editTaskStartDayIndex',
										'label' => __d(sprintf($_domain, 'Project_Task'), 'Start date', true),
										'required' => true,
										'class' => 'wd-date',
										'onchange'=> 'editTaskValidated(this);',
										'autocomplete' => 'off',
										'rel' => 'no-history',
										'div' => array(
											'class' => 'wd-input label-inline'
										)
										
									));
									?>
                                </div>
                            </div>
                            <div class="wd-col wd-col-sm-6">
							<?php 				
								echo $this->Form->input('task_end_date', array(
									'type'=> 'text',
									'id' => 'editTaskEndDayIndex',
									'label' => __d(sprintf($_domain, 'Project_Task'), 'End date', true),
									'required' => true,
									'class' => 'wd-date',
									'autocomplete' => 'off',
									'onchange'=> 'editTaskValidated(this);',
									'rel' => 'no-history',
									'div' => array(
										'class' => 'wd-input label-inline'
									)
									
								));
								?>
                            </div>
                        </div>
						<?php if( $show_workload) { ?>
							</div>
							<div class="wd-col wd-col-sm-4">
								<div class="task-workload">
									<table id="task_assign_table" class="nct-assign-table">
										<thead>
											<tr>
												<td class="bold base-cell null-cell"><?php __('Resources');?> </td>
												<td class="bold base-cell null-cell"><?php  __d(sprintf($_domain, 'Project_Task'), 'Workload')?></td>
											</tr>
										</thead>
										<tbody>
											
										</tbody>
										<tfoot>
											<tr>
												<td class="base-cell"><?php __('Total') ?></td>
												<td class="base-cell total-consumed" id="total-consumed">0</td>
											</tr>
										</tfoot>
									</table>
								</div>
							</div>
						</div>
						<?php } ?>
                        <div class="wd-row wd-submit-row">
                            <div class="wd-col-xs-12">
                                <div class="wd-submit">
                                    <button type="submit" class="btn-form-action btn-ok btn-right" id="btnSave">
                                        <span><?php __('Save') ?></span>
                                    </button>
                                    <a class="btn-form-action btn-cancel" id="reset_button" href="javascript:void(0);" onclick="cancel_popup(this);">
										<?php echo __("Cancel", true); ?></span>
                                    </a>
                                </div>
                            </div>
                        </div>
						<?php echo $this->Form->end(); ?>
                        <!-- END  form -->
                    </div>				
                </div>				
            </div>
        </div>
        <!-- END Edit task Popup -->
    </div>	
</div>
<div style="clear: both"></div>
<?php echo $this->element('dialog_projects') ?>
<div id="addProjectTemplate" class="loading-mark"></div>
<style type="text/css">

	<?php foreach ( $define_task_colors as $name => $color){?>
    .project-task-widget .status_text.status_<?php echo $name;?>{
        color: <?php echo $color;?>;
    }
    .project-task-widget .status_dot.active.status_<?php echo $name;?>{
        border-color: <?php echo $color;?>;
    }
    .project-task-widget .status_dot.status_<?php echo $name;?>:hover{
        border-color: <?php echo $color;?>;
    }
    .project-task-widget .status_dot.loading.status_<?php echo $name;?>{
        border-color: #F2F5F7;
        border-top-color: <?php echo $color;?>;
    }
	<?php } ?>

    .project-task-widget .wd-task-actions .wd-task-action.has-value{
        color: <?php echo $define_task_colors['red'];?>;
    }
    .project-task-widget .wd-task-actions .wd-task-action.has-value.read{
        color: <?php echo $define_task_colors['blue'];?>;
    }
    .project-task-widget .wd-task-actions .has-value svg .cls-1{
        fill: <?php echo $define_task_colors['red'];?>;
    }
    .project-task-widget .wd-task-actions .has-value.read  svg .cls-1{
        fill: <?php echo $define_task_colors['blue'];?>;
    }
	/* #444 remove Status on kanban */
	.kanban-box .jqx-kanban-item .task-status{
		display: none !important;
	}
	.kanban-box .wd-kanban-container{
		width:<?php echo 320*count($projectStatusEX);?>px !important;
		min-width: 100%;
	}
	#filter_task_title{
		width: 220px;
	}
	.wd-list-assign ul li{
		display: inline-block;
		vertical-align: middle;
		line-height: 40px;
		margin-right: 4px;
	}
	.list-all-assigned .circle-name{
		width: 40px;
		height: 40px;
		border: 2px solid transparent;
		background-color: transparent;
		transition: all 0.3s ease;
		font-size: 16px;
		line-height: 36px;
		display: block;
		top: 0;
	}
	.list-all-assigned .circle-name img{
		width: 100%;
		height: 100%;
	}
	.list-all-assigned .assign-team .circle-name{
		border-color: #C5CCCF;
		color: #fff;
	}
	.list-all-assigned .filter-employee:checked ~ label .circle-name{
		background-color: #217FC2 ;
		color: #fff;
	}
	.project-task-widget .in_progress_column .task_late.task-time{
		color: #E94754;
	}
	#widget-task button{
		top: -40px;

	}
</style>

<?php
	$t_i18ns = array(
		'Workload' => __('Workload', true),
		'Consumed' => __('Consumed', true),
		'M.D' => __('M.D', true),
	);
	for ($m=1; $m<=12; $m++) {
		$month = date('F', mktime(0,0,0,$m));
		$t_i18ns[$month] = __($month,true);
	}
	$status_not_order = array_values($list_org_project_status);
	$file_upload = array();
	if(!empty($attachedFile)){
		foreach ($attachedFile as $key => $value) {
			if ( preg_match('/\.(jpg|jpeg|bmp|gif|png|swf)$/i', $value)) {
				$file_upload[$key] = $this->Html->url(array('controller' => 'kanban', 'action' => 'attachment', $key, '?' => array('sid' => $api_key)));
			}else{
				$file_upload[$key] = $this->Html->url(array('controller' => 'kanban', 'action' => 'attachment', $key, '?' => array('download' => true, 'sid' => $api_key)),true);
			}
		}
	}
 ?>
<script type="text/javascript">
	HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
	Dropzone.autoDiscover = false;
	/*
	* Workload table
	*/
	var c_task_data = {}; // Continous task data 
	var list_assigned = {}; // Continous task data 
	var show_workload = <?php echo json_encode($show_workload); ?>; 
	/*
	* END Workload table
	*/
	
    var projectStatusEX = <?php echo json_encode($projectStatusEX); ?>;
    var task_kanban = <?php echo json_encode($task_kanban); ?>;
    var data_tasks = <?php echo json_encode($data_tasks); ?>;
    var listStatus = <?php echo json_encode($list_org_project_status); ?>;
    var slider_active = <?php echo json_encode($slider_active); ?>;
    var t_i18ns = <?php echo json_encode($t_i18ns); ?>;
    var canModified = <?php echo json_encode($canModified); ?>;
    var isTablet = <?php echo json_encode($isTablet); ?>;
	var linkFile = <?php echo json_encode($file_upload) ?>;
    var isMobile = <?php echo json_encode($isMobile); ?>;
	var api_key = <?php echo json_encode($employee_info['Employee']['api_key']); ?>;
	var check_consumed = <?php echo json_encode($check_consumed); ?>;
	var list_phase_colors = <?php echo json_encode($list_phase_colors); ?>;
	var list_phases = <?php echo json_encode($list_phases); ?>;
	var kanbanTag = '#kanban';
	var run_filter = 0;	
	var curent_date = new Date(<?php echo json_encode(date("Y-m-d 0:0:0"));?>);
	var this_monday = new Date(<?php echo json_encode(date("Y-m-d 0:0:0", strtotime('monday this week')));?>);
	var this_sunday = new Date(<?php echo json_encode(date("Y-m-d 0:0:0", strtotime('sunday this week')));?>);
	var heightKanban = $(window).height() - 140;
    var canModify = <?php echo json_encode((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite)); ?>;
    var myRole = <?php echo json_encode($myRole); ?>;
    var emp_id_login = <?php echo json_encode($emp_id_login); ?>;
	$(window).resize(function () {
		heightKanban = $(window).height() - 140;
		$(kanbanTag).css({height: heightKanban});
	});
	function re_init_kanban(list_tasks){
		if( list_tasks != null) task_kanban = list_tasks;		
		$(kanbanTag).jqxKanban('destroy');
		if( !$(kanbanTag).length) $('.wd-kanban-container:first').append( $('<div id="kanban" class="kanban-task" style=""></div>'));
		init_kanban(kanbanTag);
	}
	function kanbanAdapter(datakanbanAdapter){
		var fields = [
            {name: "id", type: "number"},
            {name: "status", map: "task_status_id", type: "number"},
            {name: "text", map: "task_title", type: "string"},
            {name: "task_id", type: "number"},
        ];
        var source = {
			localData: datakanbanAdapter,
			dataType: "array",
			dataFields: fields
		};
        var dataAdapter = new $.jqx.dataAdapter(source);
		return dataAdapter;
	}
	function resourcesAdapterFunc(dataresourcesAdapter) {
		var resourcesSource =
				{
					localData: dataresourcesAdapter,
					dataType: "array",
					dataFields: [
						{name: "id", type: "number"},
					]
				};

		var resourcesDataAdapter = new $.jqx.dataAdapter(resourcesSource);
		return resourcesDataAdapter;
	}
	function init_kanban(kanbanElm){
		if( kanbanElm == null) kanbanElm = '#kanban';
		var wdKanban = $(kanbanElm); 
		// console.log( heightKanban, resourcesAdapterFunc(task_kanban), kanbanAdapter(task_kanban));
		wdKanban.jqxKanban({
            template: "<div><div class='task-item'>"
                    + "<div class='task-head'></div>"
                    + "<div class='jqx-kanban-item-text task-title'></div>"
                    + "<div class='task-item-phase'></div>"
                    + "<div class='task-footer clearfix'></div>"
                    + "</div></div>",
            width: '100%',
            height: heightKanban,
            resources: resourcesAdapterFunc(task_kanban),
            source: kanbanAdapter(task_kanban),
			templateContent: {
				className: 'wd-kanban-item jqx-kanban-item',
			},
            itemRenderer: function (element, data, resource) {
                var task_item = data_tasks[data.id];
                if (task_item) {
                    // Assign to
                    var _html_head = '<div class="task-list-assigned">';
                    var list_assign = task_item['assigned'];
                    if (list_assign) {
                        $.each(list_assign, function (ind, _data_assign) {
                            if (_data_assign.is_profit_center == 1) {
								var _eid = _data_assign.reference_id + '-1';
                                _html_head += '<div class="circle-name" title="' + listEmployeeName[_eid]['name'] + '"><span data-id="' + _data_assign.reference_id + '"><i class="icon-people"></i></span></div>';
                            } else {
								var _eid = _data_assign.reference_id;
                                _html_head += '<a class="circle-name" title="' + listEmployeeName[_eid]['fullname'] + '" ><span data-id="' + _eid + '"><img alt="avatar" src="' + js_avatar(_eid) + '"/></span></a>';
                            }
                        });

                    }
                    _html_head += '</div>';
                    // Task end date
					var late = task_item.late == 1;

                    if (task_item['task_end_date']) {
                        var task_end_date = task_item['task_end_date_format'];
                        task_end_date = task_end_date.split('-');
                        _html_head += '<span class="task-time' + (late ? ' task_late' : '') + '">' + task_end_date[0] + ' ' + t_i18ns[task_end_date[1]] + ' ' + task_end_date[2] + '</span>';
                    }
                    // Task status
                    _html_head += '<div class="task-status">';
                    _status_title = '<div class="status_texts">';
                    _status_button = '<div class="status_dots">';
                    $.each(listStatus, function (id, task_status) {
                        color = 'status_blue';
                        if (task_status.status == 'CL')
                            color = 'status_green';
                        else if (task_item['late'] == 1)
                            color = 'status_red';
                        active = (data.status == task_status.id) ? 'active' : '';

                        _status_title += '<span class="status_item status_item_' + task_status.id + ' status_text ' + active + ' ' + color + '" data-value="' + task_status.id + '">' + task_status.name + '</span>';
                        _status_button += '<a href="javascript:void(0);" class="status_item status_item_' + task_status.id + ' status_dot ' + active + ' ' + color + '" data-value="' + task_status.id + '" title="' + task_status.name + '" data-taskid="' + data.id + '"></a>';
                    });
                    _status_title += '</div>';
                    _status_button += '</div>';
                    _html_head += _status_title + _status_button;
                    _html_head += '</div>';

                    $(element).find(".task-head").html(_html_head);
					var phase_color = list_phase_colors[task_item['project_planed_phase_id']] ? list_phase_colors[task_item['project_planed_phase_id']] : '';
					if(task_item['project_planed_phase_id']) $(element).find(".task-item-phase").html('<span style="background-color: '+ phase_color +';"></span>'+ list_phases[task_item['project_planed_phase_id']]);
                    // Task footer
                    _html_footer = '';
                    _html_footer = '<div class="footer-top clear-fix">';
                    estimated = (task_item['estimated']) ? task_item['estimated'] : 0;
                    
					if( show_workload) {
						_html_footer += '<div class="task-workload"><p class="label">' + t_i18ns['Workload'] + '</p><span class="value">' + estimated + t_i18ns['M.D'] + '</span></div>';
					}
					if((check_consumed) && (check_consumed == 1)){
						consumed = (task_item['special'] != 0) ? ((task_item['special_consumed'] != 0) ? task_item['special_consumed'] : 0) : ((task_item['consumed']) ? task_item['consumed'] : 0);
						// consumed = Math.round(consumed, 2);
						progress = 100;
						if (estimated != 0)
							progress = (consumed / estimated) * 100;
						else if (consumed)
							progress = 0;
						
						_css_class = (progress <= 100) ? 'green-line' : 'red-line';
						display_value = (progress < 100) ? Math.round(progress) : 100;
						_html_footer += '<div class="task-consumed"><p class="label">' + t_i18ns['Consumed'] + '</p><span class="value">' + consumed + t_i18ns['M.D'] + '</span></div>';
					
						_html_footer += '<div class="task-progress" data-progress="' + progress + '">';

						_html_footer += '<div class="progress-slider ' + _css_class + '" data-value="' + display_value + '"><div class="progress-holder"><div class="progress-line-holder"></div></div><div class="progress-value" style="width:' + display_value + '%" title="' + display_value + '%"><div class="progress-line"></div><div class="progress-number"> <div class="text" style="margin-left: -' + display_value + '%;" >' + display_value + '%</div></div></div></div></div>';
					}
                    _html_footer += '</div>';

                    // footer bottom
                    _html_footer += '<div class="footer-bottom">';
                    _html_footer += '<div class="wd-task-actions">';
                    _html_footer += '<a href="javascript:void(0)" onclick="popupTaskComment.call(this);" class="wd-task-action task-comment no-comment ' + (task_item['comment_count'] ? 'has-value' : '') + ' ' + (task_item['read_status'] ? 'read' : '') + '" data-taskid="' + task_item['id'] + '" data-project-id="' + task_item['project_id'] + '">';
                    _html_footer += '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><defs><style>  .cls-1 {fill: #666;fill-rule: evenodd;  }</style>  </defs>  <path id="Z0gMSG" class="cls-1" d="M683.124,30h-6.249a0.625,0.625,0,1,0,0,1.25h6.249A0.625,0.625,0,1,0,683.124,30ZM680,20c-5.523,0-10,3.918-10,8.75a8.375,8.375,0,0,0,3.75,6.824V40l5.12-2.56c0.371,0.036.747,0.059,1.13,0.059,5.523,0,10-3.917,10-8.749S685.523,20,680,20Zm0,16.25c-1.435,0-1.25,0-1.25,0L675,38.125V34.864a7.213,7.213,0,0,1-3.751-6.114c0-4.142,3.918-7.5,8.751-7.5s8.749,3.358,8.749,7.5S684.832,36.25,680,36.25Zm4.374-10h-8.749a0.625,0.625,0,1,0,0,1.25h8.749A0.625,0.625,0,1,0,684.374,26.25Z" transform="translate(-670 -20)"></path></svg>';

                    _html_footer += '<span>' + task_item['comment_count'] + '</span></a>';
                    _html_footer += '<a href="javascript:void(0)" onclick="openPopupAttachment.call(this);" class="wd-task-action task-attachment ' + (task_item['attachment_count'] ? 'has-value' : '') + ' ' + (task_item['attach_read_status'] ? 'read' : '') + '" data-taskid="' + task_item['id'] + '" data-project-id="' + task_item['project_id'] + '" tabindex="0">';
                    _html_footer += '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><defs><style>  .cls-1 {fill: #666;fill-rule: evenodd;  }</style></defs><path id="document" class="cls-1" d="M2023.69,590.749h-7.38a0.625,0.625,0,0,0,0,1.25h7.38A0.625,0.625,0,0,0,2023.69,590.749Zm0-3.75h-7.38a0.626,0.626,0,0,0,0,1.251h7.38A0.626,0.626,0,0,0,2023.69,587Zm4.31,10h0V582.624a0.623,0.623,0,0,0-.62-0.624h-14.76a0.623,0.623,0,0,0-.62.624v18.75a0.624,0.624,0,0,0,.62.624h10.46v0l4.92-5v0Zm-4.92,3.459V597h3.4Zm3.69-4.71h-4.92v5h-8a0.623,0.623,0,0,1-.62-0.624v-16.25a0.625,0.625,0,0,1,.62-0.625h12.3a0.625,0.625,0,0,1,.62.625v11.874Z" transform="translate(-2010 -582)"></path></svg>';
                    _html_footer += '<span>' + task_item['attachment_count'] + '</span></a>';
					var is_nct = '', edit_action = 'openEditTask.call(this);';
					if(task_item['is_nct'] == 1){
						is_nct = 'task-nct';
						edit_action = 'openEditTaskNCT(this);';
					}
					if(canModified == 1){
                    _html_footer += '<a href="javascript:void(0)" onclick="'+ edit_action +'" class="wd-task-action task-edit '+ is_nct +'"  data-taskid="' + task_item['id'] + '" data-project-id="' + task_item['project_id'] + '" tabindex="0">';
                    _html_footer += '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><defs><style>  .cls-1 {fill: #666;fill-rule: evenodd;  }</style></defs><path id="EDIT" class="cls-1" d="M6593.86,260.788c-0.75.767-11.93,11.826-12.4,12.3a0.7,0.7,0,0,1-.27.157c-0.75.224-5.33,1.7-5.37,1.719a0.693,0.693,0,0,1-.2.03,0.625,0.625,0,0,1-.44-0.184,0.636,0.636,0,0,1-.16-0.642c0.01-.044,1.37-4.478,1.64-5.477a0.627,0.627,0,0,1,.16-0.285s11.99-12,12.34-12.343a3.636,3.636,0,0,1,2.42-1.056,3.186,3.186,0,0,1,2.23.981,3.347,3.347,0,0,1,1.18,2.356A3.455,3.455,0,0,1,6593.86,260.788Zm-17.36,12.665c1.21-.39,3.11-1.045,3.97-1.322a3.9,3.9,0,0,0-.94-1.565,4.037,4.037,0,0,0-1.78-1.087C6577.46,270.444,6576.85,272.274,6576.5,273.453Zm3.92-3.789a4.95,4.95,0,0,1,1.07,1.6c2.23-2.2,7.88-7.791,10.33-10.231a3.894,3.894,0,0,0-1.02-1.875,3.944,3.944,0,0,0-1.84-1.1c-2.41,2.409-8.16,8.167-10.37,10.372A5.418,5.418,0,0,1,6580.42,269.664Zm12.51-12.755a1.953,1.953,0,0,0-1.35-.63,2.415,2.415,0,0,0-1.53.69c-0.01.011-.06,0.055-0.09,0.09a5.419,5.419,0,0,1,1.73,1.194,5.035,5.035,0,0,1,1.14,1.763,1.343,1.343,0,0,0,.12-0.119,2.311,2.311,0,0,0,.78-1.534A2.168,2.168,0,0,0,6592.93,256.909Z" transform="translate(-6575 -255)"></path></svg>';
                    _html_footer += '<span></span></a>';
					}
                    _html_footer += '</div>';
                    _html_footer += '</div>';
                    // footer bottom
                    $(element).find(".task-footer").html(_html_footer);
                }
            },
            columns: projectStatusEX,
			columnRenderer: function (element, collapsedElement, column) {
                var columnItems = $("#kanban").jqxKanban('getColumnItems', column.dataField).length;
				if( listStatus[column.dataField]['status'] == 'IP') element.closest('.jqx-kanban-column').addClass('in_progress_column');				
                element.find(".task_count").remove();
                element.find(".jqx-kanban-column-header-title").append('<span data-id="'+ column['dataField'] +'" class="task_count">(' + columnItems + ')</span>');
            },
			ready: function(){
				if( read_only){
					// console.log(' ready readonly');
					wdKanban.find('*').unbind('mousedown');
				}
			}
        });
        wdKanban.on('itemMoved', function (event) {
			if (read_only) return;
            var args = event.args;
            var itemId = args.itemData.id;
            var flag_id = $('#kanban_' + args.itemId).find('.flag-id').val();
            if (flag_id)
                itemId = flag_id;
            var newColumn = args.newColumn['dataField'];
            if (itemId && newColumn) {
                $.ajax({
                    url: '/kanban/update_task_status',
                    type: 'POST',
                    data: {
                        id: itemId,
                        status: newColumn,
                    },
                    dataType: 'json',
                    success: function (respon) {
                        if (respon.result == true) {
                            _task_move = $('#kanban_' + itemId);
                            _status_active = $('.status_item_' + newColumn);
                            _task_move.find('.status_item').removeClass('active');
                            _task_move.find(_status_active).addClass('active');

                            //move task in slider
                            _task_move1 = $('.task-' + itemId);
                            _status_active1 = $('.status_item_' + newColumn);
                            _task_move1.find('.status_item').removeClass('active');
                            _task_move1.find(_status_active1).addClass('active');
                            _item_slider = '<li class="task-' + itemId + '">' + $('#widget-task').find('.task-' + itemId).html() + '</li>';
                            $('#widget-task').find('.task-' + itemId).addClass('removed');
                            $('#widget-task').find('.task-' + itemId).removeClass('.task-' + itemId);
                            $('#widget-task').find('.status-' + newColumn).append(_item_slider);

                        }
                    },
                });
            }
        });
	}
    $(document).ready(function () {
        init_kanban(kanbanTag);
    });
	function updateListAssigned(){
		var _element = $('#list-all-assigned');
		if( !_element.length) return;
		var old_filter = _element.find(':checked');
		$.ajax({
			url : <?php echo json_encode($this->Html->url(array('controller' => 'kanban', 'action' => 'listEmployeeAssigned', $projectName['Project']['id'])));?>,
			// url : '/kanban/listEmployeeAssigned/' + projectID,
			type: "GET",
			cache: false,
			dataType: 'json',
			beforeSend: function(){
				_element.find('ul').prepend($('<li class="wd-loading"> <a href="javascript:void(0);" class="loading-icon loading loading-mark"><i></i></a></li>'));
			},
			complete: function(){
				_element.find('.wd-loading').remove();
			},
			success: function (data){
				if(data.result == 'success'){
					var _html = '<ul>';
					$.each(data.data, function(i, _data){
						if( _data){
							$.each(_data, function(_id, _name){
								var avt = '<i class="icon-people"></i>';
								var is_pc = 1;
								if( i == 'listEmployeeAssigned'){
									avt = '<img src="' + js_avatar(_id) + '" alt="avatar">';
									is_pc = 0;
								}
								var emp = _id + '-' + is_pc;
								_html += '<li data-emp="' + emp + '" class="' + ( is_pc ? 'assign-team' : '') + '">';
								_html +=  '<div class="wd-input-checkbox-custom"  title="' + _name + '" >';
								_html +=  '<input type="checkbox" onchange="filterTask(this);" name="data[Filter][employee][' + emp + ']" class="filter-employee wd-hide" id="filter-employee-' + emp + '" data-id="' + emp + '"><label for="filter-employee-' + emp + '"><span class="circle-name">' + avt + '</span></label>';
									
								_html += '</div>';
								_html += '</li>';
							});
						}
					});
					_html += '</ul>';
					_element.empty().html(_html);
					if( old_filter.length){
						$.each(old_filter, function(i, _checkbox){
							$('#' + $(_checkbox).prop('id')).prop('checked', true);
						});
						filterTask();
					}
					
					
				}
			},
		});
	}
	$('#KanbanFilter').on('submit', function(e){
		e.preventDefault();
		filterTask();
		return;
	});
	HistoryFilter.afterLoad = function(){
		run_filter = 1;
		filterTask();
	}
	function checkTime(task, time_filter){
		if( !time_filter) return 1;
		var result = 0;
		var task_ed = new Date( task.task_end_date);task_ed.setHours(0);
		// console.log( task_ed);
		switch(time_filter){
			case 'day':
				result = ( curent_date.valueOf() == task_ed.valueOf());
				break;
			case 'week':
				result = ( this_monday <= task_ed && this_sunday >= task_ed);
				break;
			case 'month':
				result = ( curent_date.getFullYear() == task_ed.getFullYear() && curent_date.getMonth() == task_ed.getMonth() );
				break;
			case 'late':
				var is_IP = listStatus[task.task_status_id]['status'] == "IP";
				result = ( curent_date > task_ed && is_IP);
				break;
			default:
				result = 0; 
				break;
		}
		console.log( result, task_ed);
		return result;
	}
	function checkTitle(task, title_filter){
		if( !title_filter) return 1;
		title_filter = title_filter.toLowerCase();
		regE = title_filter.toLowerCase().replace('?', '.').replace('*', '.+');
		var regE = new RegExp(regE);
		var tasktitle = task.task_title.toLowerCase();
		// console.log( tasktitle, regE, tasktitle.match(regE));
		if( tasktitle.match(regE)) return 1;
		
	}
	function checkAssign(task, employee_filter){
		if( employee_filter == '') return 1;
		var result = 0;
		// console.log( task);
		$.each( task.assigned, function( i, em){
			var emp = em.reference_id + '-' + em.is_profit_center;
			if( ($.inArray( emp, employee_filter) != -1) ){
				result = 1;
				return false;
			}
		});
		return result;
	}
    function filterTask($this){
		if( !run_filter) return;
		$(kanbanTag).closest('.loading-mark').addClass('loading');
		$('#clean-filters').show();
		var time_filter = $('#filter_by_time').val();
		var title_filter =  $('#filter_task_title').val();
		var employee_filter = [];
		var emp_checkbox =  $('#list-all-assigned').find(':checked');
		if( emp_checkbox.length) $.each(emp_checkbox, function(i, _checkbox){
			employee_filter.push( $(_checkbox).data('id'));
		});
		var _task_display = [];
		var _new_tasks = [];
		if( data_tasks){
			$.each(data_tasks, function(_task_id, _task){
				if( !checkTime(_task, time_filter)){
					return true; // continue;
				}
				if( !checkTitle(_task, title_filter)){
					return true;
				}
				if( !checkAssign(_task, employee_filter)){
					return true;
				}
				_new_tasks.push(_task);
			});
			re_init_kanban(_new_tasks);
		}
		$(kanbanTag).closest('.loading-mark').removeClass('loading');
		return;
	}
    function resetFilter($this){
		$('#filter_by_time').val('').trigger('change');
		$('#list-all-assigned').find(':checkbox').prop('checked', false).trigger('change');
		$('#filter_task_title').val('').trigger('change');
		$('#clean-filters').hide();
		return;
	}
</script>
<script type="text/javascript">
    var project_id = <?php echo json_encode($project_id); ?>;
    $("#newTaskEndDayIndex, #newTaskStartDayIndex").datepicker({
        dateFormat: 'dd-mm-yy',
    });
    var _debug = '';
    var read_only = <?php echo json_encode($read_only); ?>;
	// var canModified = <?php echo json_encode($canModified); ?>;
    var list_phase_date = <?php echo json_encode($list_phase_date); ?>;
    var newtaskIndexForm_date_validated = 1;
	// console.log(canModified);
	$('.fancy.image').fancybox({
       type: 'image'
    });
    function new_task_validated(_this) {
        var st_date = $('#newTaskStartDayIndex').val();
        var en_date = $('#newTaskEndDayIndex').val();
        st_date = st_date.split('-');
        en_date = en_date.split('-');
        st_date = new Date(st_date[2], st_date[1], st_date[0]);
        en_date = new Date(en_date[2], en_date[1], en_date[0]);
        if (st_date > en_date) {
            $(_this).css('border-color', 'red');
            newtaskIndexForm_date_validated = 0;
        } else {
            $('#newTaskStartDayIndex').css('border-color', '');
            $('#newTaskEndDayIndex').css('border-color', '');
            newtaskIndexForm_date_validated = 1;
        }
    }
    ;
    var editTask_date_validated = 1;
    function editTaskValidated(_this) {
        var st_date = $('#editTaskStartDayIndex').val();
        var en_date = $('#editTaskEndDayIndex').val();
        st_date = st_date.split('-');
        en_date = en_date.split('-');
        st_date = new Date(st_date[2], st_date[1], st_date[0]);
        en_date = new Date(en_date[2], en_date[1], en_date[0]);
        if (st_date > en_date) {
            $(_this).css('border-color', 'red');
            editTask_date_validated = 0;
        } else {
            $('#editTaskStartDayIndex').css('border-color', '');
            $('#editTaskEndDayIndex').css('border-color', '');
            editTask_date_validated = 1;
        }
    }
    function new_task_phase_changed(_this) {
        var _this = $(_this);
        var _val = _this.val();
        if (_val in list_phase_date) {
            $('#newTaskStartDayIndex').val(list_phase_date[_val]['phase_real_start_date']).trigger('change').css('border-color', '');
            $('#newTaskEndDayIndex').val(list_phase_date[_val]['phase_real_end_date']).trigger('change').css('border-color', '');
            new_task_validated('#newTaskEndDayIndex');
        }
    }
    var alert_timeout = '';
    function show_add_task_form_alert(msg) {
        $('#newtaskIndicatorForm .alert-message').empty().append(msg);
        clearTimeout(alert_timeout);
        alert_timeout = setTimeout(function () {
            $('#newtaskIndicatorForm .alert-message').empty();
        }, 3000);
    }
    init_multiselect('#list_task_assign_to, #list_task_assign_to_edit');

    $('.project-task-widget .kanban-box').hide();
    function wd_tasks_expand(_element) {
        $('.project-task-widget .kanban-box').show();
        $('.project-task-widget .widget_content').hide();
        var _this = $(_element);
        var _wg_container = _this.closest('.wd-widget');
        _wg_container.addClass('fullScreen');
        $('#wd-container-header-main').slideUp();
		$('html').css('overflow', 'hidden');
        _wg_container.closest('li').addClass('wd_on_top');
        _this.hide();
        _wg_container.find('.primary-object-collapse').show();
        _wg_container.find('#widget-task ul').height($(window).height() - 150);
        $('#widget-task .slides').slick('slickSetOption', 'slidesToShow', Math.floor($('#widget-task .slides').width() / 540), true);
        $('html').scrollTop(0);
		$(window).trigger('resize');
    }
    /////
    function wd_tasks_collapse(_element) {
        $('.project-task-widget .kanban-box').hide();
        $('.project-task-widget .widget_content').show();
        var _this = $(_element);
        var _wg_container = _this.closest('.wd-widget');
        _wg_container.removeClass('fullScreen');
        $('#wd-container-header-main').slideDown();
		$('html').css('overflow', '');
        _wg_container.closest('li').removeClass('wd_on_top');
        _this.hide();
        _wg_container.find('.primary-object-expand').show();
        _wg_container.find('#widget-task ul').height(325);
        $('#widget-task .slides').slick('slickSetOption', 'slidesToShow', Math.floor($('#widget-task .slides').width() / 540), true);
		$(window).trigger('resize');
    }
    $('.log-field-edit').click(function () {
        var text_area = $(this).closest('.task-item').find('textarea');
        val = text_area.val();
        text_area.focus().val("").val(val);
    });

    function updateTaskTitle() {
        if (read_only)
            return;
        var inp = $(this);
        var value = $.trim(inp.val()),
                task_id = inp.data('task-id');
        if (value) {
            inp.prop('disabled', true);
            // save
            $.ajax({
                url: '/project_amrs_preview/update_task_title/' + project_id,
                type: 'POST',
                dataType: 'json',
                data: {
                    id: task_id,
                    task_title: value,
                },
                success: function (response) {
                    if (response) {
                        inp.prop('disabled', false).css('color', '#3BBD43');
                    } else {
                        inp.prop('disabled', false).css('color', 'red');
                    }
                },
                complete: function () {
                    // hide loading
                    // can change

                }
            });
        }
    }
    var _task_resizable = $('.project-task-widget ul.project-task-list');
    function initresizable() {
        var _max_height = 0;
        var _min_height = 248;
        _task_resizable.children().each(function () {
            _max_height += $(this).is(":visible") ? ($(this).height() + parseInt($(this).css('margin-bottom')) + parseInt($(this).css('margin-top')) + parseInt($(this).css('padding-bottom')) + parseInt($(this).css('padding-top')) + parseInt(_task_resizable.css('padding-top')) + parseInt(_task_resizable.css('padding-bottom'))) : 0;
        });
        _max_height = Math.max(_min_height, _max_height);
        _min_height = Math.min(_min_height, _max_height);
        _task_resizable.resizable({
            handles: "s",
            maxHeight: _max_height,
            minHeight: _min_height,
            resize: function (e, ui) {
                _max_height = 0;
                _min_height = 248;
                _task_resizable.children().each(function () {
                    _max_height += $(this).is(":visible") ? ($(this).height() + parseInt($(this).css('margin-bottom')) + parseInt($(this).css('margin-top')) + parseInt($(this).css('padding-bottom')) + parseInt($(this).css('padding-top')) + parseInt(_task_resizable.css('padding-top')) + parseInt(_task_resizable.css('padding-bottom'))) : 0;
                });
                _max_height = Math.max(_min_height, _max_height);
                _min_height = Math.min(_min_height, _max_height);
                _task_resizable.resizable("option", 'maxHeight', _max_height);
                _task_resizable.resizable("option", 'minHeight', _min_height);

            }
        });
        $(window).trigger('resize');

    }
    function destroyresizable() {
        _task_resizable.resizable("destroy");
        _task_resizable.css({
            width: '',
            height: ''
        });
    }
    // initresizable();

    $('#widget-task .slides').slick({
        prevArrow: '<button type="button" class="slick-prev"><span><img src="/img/new-icon/arrow-left-gray.png"><img class="img-hover" src="/img/new-icon/arrow-left-blue.png"><span></button>',
        nextArrow: '<button type="button" class="slick-next"><span><img src="/img/new-icon/arrow-right-gray.png"><img class="img-hover" src="/img/new-icon/arrow-right-blue.png"></span></button>',
        slidesToShow: Math.floor($('#widget-task .slides').width() / 540),
        initialSlide: slider_active,
        infinite: false,

    });

    // $('.add-new-task .btn-add').on('click',function(){
    // $('.new-project-popup').toggleClass('open');
    // $(this).toggleClass('open');
    // });
    // $('body').on('click', function(e){
    // if(!($(e).hasClass('add-new-task') || $('.add-new-task').find(e.target).length || $(e).hasClass('ui-datepicker') || $('.ui-datepicker').find(e.target).length)){
    // $('.new-project-popup').removeClass('open');
    // $('.add-new-task .btn-add').removeClass('open');
    // }
    // });

    $('#new-more-task').on('click', function (e) {
        if (read_only)
            return;
        e.preventDefault();
        var start_date = $('#newTaskStartDayIndex').val(),
                end_date = $('#newTaskEndDayIndex').val();
        var st_date = start_date.split('-');
        var en_date = end_date.split('-');
        st_date = new Date(st_date[2], st_date[1], st_date[0]);
        en_date = new Date(en_date[2], en_date[1], en_date[0]);
        if (start_date && end_date && (st_date > en_date)) {
            show_add_task_form_alert("<?php __('Please enter the end date must be after the start date');?>");
            return;
        }
        if ($.trim($('#newTaskNameIndex').val())) {
        } else {
            $("#newTaskNameIndex").prop('required', true);
            return;
        }
        $('.new-project-popup').addClass('loading');
        $.ajax({
            type: "POST",
            url: '/project_tasks_preview/add_new_task_popup/' + project_id + '/true',
            data: $("#newtaskIndicatorForm").serialize(),
            dataType: 'json',
            success: function (data) {
                if (data.success == false) {
                    $('.new-project-popup').removeClass('loading');
                    show_add_task_form_alert(data.message);
                } else {
                    var _task = [];
                    _task['status'] = parseInt(data['message']['ProjectTask']['task_status_id']);
                    _task['id'] = parseInt(data['message']['ProjectTask']['id']);
                    _task['text'] = data['message']['ProjectTask']['task_title'];
                    data_tasks[_task['id']] = data['message']['ProjectTask'];
                    $('#kanban').jqxKanban('addItem', _task);
                    $('#kanban_' + _task['id']).addClass('jqx-kanban-item').data('kanbanItemId', _task['id']);
                    // add task into slider
                    $('#widget-task').find('.status-' + _task['status']).append('<li class="task-' + _task['id'] + '">' + renderTaskItem(data['message']) + '</li>');
                    $('.new-project-popup').removeClass('loading');
                }

            },
			complete: function(){
				updateListAssigned();
				filterTask();
			},
        });

    });
    // To Viet
    // Nho goi function destroyresizable() khi Expand va goi function initresizable() sau khi collapse
    function renderTaskItem(data) {
        data = ('ProjectTask' in data) ? data['ProjectTask'] : data;
        $('.jqx-kanban-column-container').children().addClass('jqx-kanban-item');
        var _html = '<div class="task-item" data-taskid="'+ data['id'] +'">';
        //start head
        var _html_head = '<div class="task-head"><div class="task-list-assigned">';
        var list_assign = data['assigned'];
		var late = data['late']; 
		// console.log(list_assign);
        if (list_assign) {
            is_pc = data['is_profit_center'];
            $.each(list_assign, function (key, employee) {
				if (employee.is_profit_center == 0) {
					var _eid = employee.reference_id;
					_html_head += '<a class="circle-name" title="' + listEmployeeName[_eid]['fullname'] + '" ><span data-id="' + _eid + '"><img alt="avatar" src="' + js_avatar(_eid) + '"/></span></a>';
				} else {
					var _eid = employee.reference_id + '-1';
					_html_head += '<div class="circle-name" title="' + listEmployeeName[_eid]['name'] + '"><span data-id="' + employee.reference_id + '"><i class="icon-people"></i></span></div>';
				}

            });
        }
        _html_head += '</div>';
        // Task end date
        if (data['task_end_date']) {
            var task_end_date = data['task_end_date_format'];
            task_end_date = task_end_date.split('-');
            _html_head += '<span class="task-time' + (late ? ' task_late' : '') + '">' + task_end_date[0] + ' ' + t_i18ns[task_end_date[1]] + ' ' + task_end_date[2] + '</span>';
            ;
        }

        // Task status
        if (data['task_status_id']) {
            _status_id = data['task_status_id'];
            _html_head += '<div class="task-status">';
            _status_title = '<div class="status_texts">';
            _status_button = '<div class="status_dots">';
            $.each(listStatus, function (id, task_status) {
                color = 'status_blue';
                if (task_status.status == 'CL')
                    color = 'status_green';
                else if (data['late'] == 1)
                    color = 'status_red';
                active = (_status_id == task_status.id) ? 'active' : '';

                _status_title += '<span class="status_item status_item_' + task_status.id + ' status_text ' + active + ' ' + color + '" data-value="' + task_status.id + '">' + task_status.name + '</span>';
                _status_button += '<a href="javascript:void(0);" class="status_item status_item_' + task_status.id + ' status_dot ' + active + ' ' + color + '" data-value="' + task_status.id + '" title="' + task_status.name + '" data-taskid="' + data['id'] + '"></a>';
            });
            _status_title += '</div>';
            _status_button += '</div>';
            _html_head += _status_title + _status_button;
            _html_head += '</div>';
        }
        //end head
        _html_head += '</div>';
        _html_head += '<div class="task-title app-task-title">' + data['task_title'] + '</div>';
        // start footer
        _html_footer = '<div class="task-footer clearfix">';
        _html_footer += '<div class="footer-top clear-fix">';
		
		if( show_workload) {
			_html_footer += '<div class="task-workload"><p class="label">' + t_i18ns['Workload'] + '</p><span class="value">' + estimated + t_i18ns['M.D'] + '</span></div>';
		}
		if((check_consumed) && (check_consumed == 1)){
			progress = 100;
			_css_class = (progress <= 100) ? 'green-line' : 'red-line';
			_html_footer += '<div class="task-consumed"><p class="label">' + t_i18ns['Consumed'] + '</p><span class="value">' + consumed + t_i18ns['M.D'] + '</span></div>';
		
			_html_footer += '<div class="task-progress" data-progress="' + progress + '">';

			_html_footer += '<div class="progress-slider ' + _css_class + '" data-value="' + progress + '"><div class="progress-holder"><div class="progress-line-holder"></div></div><div class="progress-value" style="width:' + progress + '%" title="' + progress + '%"><div class="progress-line"></div><div class="progress-number"> <div class="text" style="margin-left: -' + progress + '%;" >' + progress + '%</div></div></div></div>';
			_html_footer += '</div>';
		}
        _html_footer += '</div>';

        // footer bottom
        _html_footer += '<div class="footer-bottom">';
        _html_footer += '<div class="wd-task-actions">';
        _html_footer += '<a href="javascript:void(0)" onclick="popupTaskComment.call(this);" class="wd-task-action task-comment no-comment ' + (data['comment_count'] ? 'has-value' : '') + ' ' + (data['read_status'] ? 'read' : '') + '" data-taskid="' + data['id'] + '" data-project-id="' + data['project_id'] + '">';
        _html_footer += '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><defs><style>  .cls-1 {fill: #666;fill-rule: evenodd;  }</style>  </defs>  <path id="Z0gMSG" class="cls-1" d="M683.124,30h-6.249a0.625,0.625,0,1,0,0,1.25h6.249A0.625,0.625,0,1,0,683.124,30ZM680,20c-5.523,0-10,3.918-10,8.75a8.375,8.375,0,0,0,3.75,6.824V40l5.12-2.56c0.371,0.036.747,0.059,1.13,0.059,5.523,0,10-3.917,10-8.749S685.523,20,680,20Zm0,16.25c-1.435,0-1.25,0-1.25,0L675,38.125V34.864a7.213,7.213,0,0,1-3.751-6.114c0-4.142,3.918-7.5,8.751-7.5s8.749,3.358,8.749,7.5S684.832,36.25,680,36.25Zm4.374-10h-8.749a0.625,0.625,0,1,0,0,1.25h8.749A0.625,0.625,0,1,0,684.374,26.25Z" transform="translate(-670 -20)"></path></svg>';

        _html_footer += '<span>' + data['comment_count'] + '</span></a>';
        _html_footer += '<a href="javascript:void(0)" onclick="openPopupAttachment.call(this);" class="wd-task-action task-attachment ' + (data['attachment_count'] ? 'has-value' : '') + ' ' + (data['attach_read_status'] ? 'read' : '') + '" data-taskid="' + data['id'] + '" data-project-id="' + data['project_id'] + '" tabindex="0">';
        _html_footer += '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><defs><style>  .cls-1 {fill: #666;fill-rule: evenodd;  }</style></defs><path id="document" class="cls-1" d="M2023.69,590.749h-7.38a0.625,0.625,0,0,0,0,1.25h7.38A0.625,0.625,0,0,0,2023.69,590.749Zm0-3.75h-7.38a0.626,0.626,0,0,0,0,1.251h7.38A0.626,0.626,0,0,0,2023.69,587Zm4.31,10h0V582.624a0.623,0.623,0,0,0-.62-0.624h-14.76a0.623,0.623,0,0,0-.62.624v18.75a0.624,0.624,0,0,0,.62.624h10.46v0l4.92-5v0Zm-4.92,3.459V597h3.4Zm3.69-4.71h-4.92v5h-8a0.623,0.623,0,0,1-.62-0.624v-16.25a0.625,0.625,0,0,1,.62-0.625h12.3a0.625,0.625,0,0,1,.62.625v11.874Z" transform="translate(-2010 -582)"></path></svg>';
        _html_footer += '<span>' + data['attachment_count'] + '</span></a>';
		_html_footer += '<a href="javascript:void(0)" onclick="openEditTask.call(this);" class="wd-task-action task-edit "  data-taskid="' + data['id'] + '" data-project-id="' + data['project_id'] + '" tabindex="0">';
		_html_footer += '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><defs><style>  .cls-1 {fill: #666;fill-rule: evenodd;  }</style></defs><path id="EDIT" class="cls-1" d="M6593.86,260.788c-0.75.767-11.93,11.826-12.4,12.3a0.7,0.7,0,0,1-.27.157c-0.75.224-5.33,1.7-5.37,1.719a0.693,0.693,0,0,1-.2.03,0.625,0.625,0,0,1-.44-0.184,0.636,0.636,0,0,1-.16-0.642c0.01-.044,1.37-4.478,1.64-5.477a0.627,0.627,0,0,1,.16-0.285s11.99-12,12.34-12.343a3.636,3.636,0,0,1,2.42-1.056,3.186,3.186,0,0,1,2.23.981,3.347,3.347,0,0,1,1.18,2.356A3.455,3.455,0,0,1,6593.86,260.788Zm-17.36,12.665c1.21-.39,3.11-1.045,3.97-1.322a3.9,3.9,0,0,0-.94-1.565,4.037,4.037,0,0,0-1.78-1.087C6577.46,270.444,6576.85,272.274,6576.5,273.453Zm3.92-3.789a4.95,4.95,0,0,1,1.07,1.6c2.23-2.2,7.88-7.791,10.33-10.231a3.894,3.894,0,0,0-1.02-1.875,3.944,3.944,0,0,0-1.84-1.1c-2.41,2.409-8.16,8.167-10.37,10.372A5.418,5.418,0,0,1,6580.42,269.664Zm12.51-12.755a1.953,1.953,0,0,0-1.35-.63,2.415,2.415,0,0,0-1.53.69c-0.01.011-.06,0.055-0.09,0.09a5.419,5.419,0,0,1,1.73,1.194,5.035,5.035,0,0,1,1.14,1.763,1.343,1.343,0,0,0,.12-0.119,2.311,2.311,0,0,0,.78-1.534A2.168,2.168,0,0,0,6592.93,256.909Z" transform="translate(-6575 -255)"></path></svg>';
		_html_footer += '<span></span></a>';
        _html_footer += '</div>';
        _html_footer += '</div>';
        // footer bottom


        _html += _html_head + _html_footer;

        _html += '</div">';
        return _html;

    }
    $('.project-task-widget').on('click', '.status_dots .status_dot', function () {
        if (read_only)
            return;
        var _this = $(this);
        if (_this.hasClass('active') || _this.hasClass('loading') || _this.closest('.status_dots').hasClass('loading'))
            return;
        _this.closest('.status_dots').addClass('loading');
        _this.addClass('loading').siblings().removeClass('loading');
        _task_id = _this.data('taskid');
        _status_id = _this.data('value');
        $.ajax({
            type: "POST",
            url: '/kanban/update_task_status',
            data: {
                id: _task_id,
                status: _status_id
            },
            dataType: 'json',
            success: function (respon) {
                _this.closest('.status_dots').removeClass('loading');
                var _texts = _this.closest('.task-status');
                _texts.find('.status_text').removeClass('active');
                _texts.find('.status_text[data-value="' + _status_id + '"]').addClass('active');
                _this.removeClass('loading');

                if (respon.result == true) {
                    _this.addClass('active').siblings().removeClass('active');
                    _this.addClass('puffIn');
                    // move task in kanban
                    _task_move = $('#kanban_' + _task_id);
                    _status_active = $('.status_item_' + _status_id);
                    _task_move.find('.status_item').removeClass('active');
                    _task_move.find(_status_active).addClass('active');
                    _task_move_html = _task_move.html();
                    var task_kanban = $('#kanban').jqxKanban('getItems');
                    $.each(task_kanban, function (key, _task) {
                        if (_task.id == _task_id) {
                            _task.status = _status_id;							
							_task['tags'] = "usser, update";
                            $('#kanban').jqxKanban('removeItem', _task_id);
                            $('#kanban').jqxKanban('addItem', _task);
                        }
                    });
                    $('#kanban').find(_task_move).empty().append(_task_move_html);
                    // move task in slider
                    $('.task-' + _task_id).find('.status_item').removeClass('active');
                    $('.task-' + _task_id).find(_status_active).addClass('active');
                    var _item_slider = '<li class="task-' + _task_id + '">' + $('#widget-task').find('.task-' + _task_id).html() + '</li>';
                    $('#widget-task').find('.task-' + _task_id).addClass('removed');
                    $('#widget-task').find('.task-' + _task_id).removeClass('.task-' + _task_id);
                    $('#widget-task').find('.status-' + _status_id).append(_item_slider);

                    var wait = window.setTimeout(function () {
                        _this.removeClass('puffIn');
                    }, 1500);
                } else {
                    _this.removeClass('active').siblings().removeClass('active');
                }
            },
            error: function () {
                _this.closest('.status_dots').removeClass('loading');
                _this.removeClass('loading');
                _this.removeClass('active').siblings().removeClass('active');
            },
			complete: function(){
				updateListAssigned();
				filterTask();
			}
        });
    });

    $(function () {
        var myDropzone = new Dropzone("#upload-popup", {
            // acceptedFiles: ".jpg,.jpeg,.bmp,.gif,.png,.txt,.doc,.xls,.pdf,.docx,.xlsx,.ppt,.pps,.pptx,.csv,.xlsm,.msg",
            acceptedFiles: "",
        });
        myDropzone.on("queuecomplete", function (file) {
            id = $('#UploadId').val();
            var _has_file = 0;
            $.ajax({
                url: '/kanban/getTaskAttachment/' + id,
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    _html = '<ul>';
                    if (data['attachments']) {
                        $.each(data['attachments'], function (ind, _data) {
                            if (_data)
                                _has_file = 1;
                            if (_data['ProjectTaskAttachment']['is_old_attachment'] == 1) {
                                var att = _data['ProjectTaskAttachment']['attachment'].split(':');
                                _html += '<li class="old-attachment">';
                                if (att[0] == 'file') {
									if(linkFile[_data['ProjectTaskAttachment']['id']]) _link = linkFile[_data['ProjectTaskAttachment']['id']];
                                    else _link = '/kanban/attachment/'+ _data['ProjectTaskAttachment']['id'] +'/?sid='+ api_key;
                                    _html += '<i class="icon-paper-clip"></i><span href = "'+ _link +'" class="file-name fancy image"   href="javascript:void(0);" alt="' + id + '">' + _data['ProjectTaskAttachment']['attachment'].replace('file:', '') + '</span>';
                                } else {
                                    _html += '<i class="icon-link"></i><a class="file-name"  href="' + _data['ProjectTaskAttachment']['attachment'].replace('url:', '') + '" target="_blank" alt="' + id + '">' + _data['ProjectTaskAttachment']['attachment'].replace('url:', '') + '</a>';
                                }
                                _html += '<a href="javascript:void(0);" data-id = "' + id + '" alt="' + id + '"><img src="/img/new-icon/delete-attachment.png" alt="' + id + '" onclick="deleteAttachment.call(this)"></a>';
                                _html += '</li>';
                            } else {
                                if (_data['ProjectTaskAttachment']['is_file'] == 1) {
                                    if ((/\.(gif|jpg|jpeg|tiff|png)$/i).test(_data['ProjectTaskAttachment']['attachment'])) {
										if(linkFile[_data['ProjectTaskAttachment']['id']]) _link = linkFile[_data['ProjectTaskAttachment']['id']];
										else _link = '/kanban/attachment/'+ _data['ProjectTaskAttachment']['id'] +'/?sid='+ api_key;
                                        _html += '<li><i class="icon-paper-clip"></i><span href = "'+ _link +'" class="file-name fancy image"  data-id = "' + _data['ProjectTaskAttachment']['id'] + '">' + _data['ProjectTaskAttachment']['attachment'] + '</span><a  data-id = "' + _data['ProjectTaskAttachment']['id'] + '" data-taskid="' + id + '"><img src="/img/new-icon/delete-attachment.png" alt="' + _data['ProjectTaskAttachment']['id'] + '" onclick="deleteAttachmentFile.call(this)"></a></li>';
                                    } else {
                                        _link = '/kanban/attachment/' + _data['ProjectTaskAttachment']['id'] + '/?download=1&sid=' + api_key;
                                        _html += '<li><i class="icon-paper-clip"></i><a class="file-name"  href="' + _link + '">' + _data['ProjectTaskAttachment']['attachment'] + '</a><a  data-id = "' + _data['ProjectTaskAttachment']['id'] + '" data-taskid="' + id + '"><img src="/img/new-icon/delete-attachment.png" alt="' + _data['ProjectTaskAttachment']['id'] + '" onclick="deleteAttachmentFile.call(this)"></a></li>';
                                    }
                                } else {
                                    _html += '<li><i class="icon-link"></i><a class="file-name" target="_blank" href="' + _data['ProjectTaskAttachment']['attachment'] + '">' + _data['ProjectTaskAttachment']['attachment'] + '</a><a  data-id = "' + _data['ProjectTaskAttachment']['id'] + '" data-taskid="' + id + '"><img src="/img/new-icon/delete-attachment.png" alt="' + _data['ProjectTaskAttachment']['id'] + '" onclick="deleteAttachmentFile.call(this)"></a></li>';
                                }
                            }
                        });
                    }
                    _html += '</ul>';
                    $('#content_comment .append-comment').empty();
                    $('#content_comment .append-comment').html(_html);
                    var _tag = $('.wd-task-actions a.task-attachment[data-taskid="' + id + '"]');
                    _tag.addClass('read');
                    _tag.find('span').html(data.attachment_count);


                }
            });
        });
        myDropzone.on("success", function (file) {
            myDropzone.removeFile(file);
        });
        $('#Upload').on('submit', function (e) {
            $('#Upload').parent('.wd-popup').addClass('loading');
            // return;
            if (myDropzone.files.length) {
                e.preventDefault();
                myDropzone.processQueue();
            } else {
                e.preventDefault();
                var _form = $('#upload-popup').closest('form');
                $('#upload-popup').closest('.loading-mark').addClass('loading');
                var id = _form.find('#UploadId').val();
                $.ajax({
                    type: "POST",
                    url: _form.prop('action'),
                    data: _form.serialize(),
                    dataType: 'json',
                    success: function (data) {
                        if (data.attachment) {
                            var _tag = $('.wd-task-actions a.task-attachment[data-taskid="' + id + '"]');
                            _tag.addClass('read');
                            _tag.find('span').html(parseInt(_tag.find('span').html()) + 1);
                            cancel_popup('#upload-popup');
                        } else {
                            show_form_alert('#' + _form.prop('id'), data.message);

                        }
                        $('#upload-popup').closest('.loading-mark').removeClass('loading');
                    },
                    error: function () {
                        $('#upload-popup').closest('.loading-mark').removeClass('loading');
                    }
                });
            }
        });
        myDropzone.on('sending', function (file, xhr, formData) {
            // Append all form inputs to the formData Dropzone will POST
            var data = $('#Upload').serializeArray();
            $.each(data, function (key, el) {
                formData.append(el.name, el.value);
            });
        });
    });
    function openFileAttach() {
        _id = $(this).data("id");
        $.ajax({
            url: "/kanban/ajax/" + _id,
            type: "GET",
            cache: false,
            success: function (html) {
                var dump = $('<div />').append(html);
                if (dump.children('.error').length == 1) {
                    //do nothing
                } else if (dump.children('#attachment-type').val()) {
                    $('#contentDialog').html(html);
                    $('#dialogDetailValue').addClass('popup-upload');
                    showMe();
                }
            }
        });

    }
	function setLimitedDate(ele_start, ele_end){
		var limit_sdate = $('#popupnct-assign-table tbody tr:first').find('.popupnct-date').attr('id');
		if(limit_sdate){
			 limit_start_date = (limit_sdate.split("_")[1]).split("-");
			 limit_s_date = new Date(limit_start_date[1] +'-'+limit_start_date[0] +'-'+limit_start_date[2]);
			 $(ele_start).datepicker('option','maxDate',limit_s_date);
		}
		var limit_edate = $('#popupnct-assign-table tbody tr:last').find('.popupnct-date').attr('id');
		if(limit_edate){
			 limit_end_date = (limit_edate.split("_")[0]).split("-");
			 limit_e_date = new Date(limit_end_date[2] +'-'+limit_end_date[1] +'-'+limit_end_date[3]);
			  $(ele_end).datepicker('option','minDate',limit_e_date);
		}
	}
    function deleteAttachment() {
        var me = $(this), taskId = me.prop('alt');
        var itemPic = $(this).closest('li');
        var itemList = $('#content_comment .append-comment ul');
        var attachment_cont = itemPic.closest('ul');
        if (confirm('<?php __('Delete?') ?>')) {
            //call ajax
            $.ajax({
                url: '<?php echo $this->Html->url('/project_tasks/delete_attachment/') ?>' + taskId,
                complete: function () {
                    itemPic.remove();
                    var _tag = $('.wd-task-actions a.task-attachment[data-taskid="' + taskId + '"]');
                    _tag.addClass('read');
                    _tag.find('span').html(parseInt(_tag.find('span').html()) - 1);
                }
            })
        }
    }
    function deleteAttachmentFile() {
        var AttachmentId = $(this).closest('a').data('id');
        var taskId = $(this).closest('a').data('taskid');
        var itemPic = $(this).closest('li');
        var attachment_cont = itemPic.closest('ul');
        $.ajax({
            url: '<?php echo $this->Html->url('/kanban/delete_attachment/') ?>' + AttachmentId,
            success: function (data) {
                itemPic.remove();
                var _tag = $('.wd-task-actions a.task-attachment[data-taskid="' + taskId + '"]');
                _tag.addClass('read');
                _tag.find('span').html(parseInt(_tag.find('span').html()) - 1);
            }
        })
    }
    function openAttachment() {
        var me = $(this), taskId = me.attr('alt');
        window.open('<?php echo $this->Html->url('/project_tasks/view_attachment/') ?>' + taskId, '_blank');
    }
    function openPopupAttachment() {
        var me = $(this),
                id = me.data('taskid'),
                project_id = me.data('project-id');
        var _html = task_title = '';
        var latest_update = '';
        $('#UploadId').val(id);
        $('#UploadProjectId').val(project_id);
        var popup = $('#template_upload');
        var file_count = 0;
        $.ajax({
            url: '/kanban/getTaskAttachment/' + id,
            type: 'POST',
            data: {
                id: id,
            },
            dataType: 'json',
            success: function (data) {
                popup.addClass('show').show();
                $('.light-popup').addClass('show');
                popup.find('.wd-popup-head h4').empty().append(data['task_title']);
                _html += '<ul>';
                if (data['attachments']) {
                    $.each(data['attachments'], function (ind, _data) {
                        if (_data)
                            file_count++;
                        if (_data['ProjectTaskAttachment']['is_old_attachment'] == 1) {
                            var att = _data['ProjectTaskAttachment']['attachment'].split(':');
                            _html += '<li class="old-attachment">';
                            if (att[0] == 'file') {
								if(linkFile[_data['ProjectTaskAttachment']['id']]) _link = linkFile[_data['ProjectTaskAttachment']['id']];
                                else _link = '/kanban/attachment/'+ _data['ProjectTaskAttachment']['id'] +'/?sid='+ api_key;
                                _html += '<i class="icon-paper-clip"></i><span href="'+ _link +'" class="file-name fancy image" rel="one_pic_expand" data-fancybox="image" data-type="image" href="javascript:void(0);" alt="' + id + '">' + _data['ProjectTaskAttachment']['attachment'].replace('file:', '') + '</span>';
                            } else {
                                _html += '<i class="icon-link"></i><a class="file-name" href="' + _data['ProjectTaskAttachment']['attachment'].replace('url:', '') + '" target="_blank" alt="' + id + '">' + _data['ProjectTaskAttachment']['attachment'].replace('url:', '') + '</a>';
                            }
                            _html += '<a href="javascript:void(0);" data-id = "' + id + '" alt="' + id + '"><img src="/img/new-icon/delete-attachment.png" alt="' + id + '" onclick="deleteAttachment.call(this)"></a>';
                            _html += '</li>';
                        } else {
                            if (_data['ProjectTaskAttachment']['is_file'] == 1) {
                                if ((/\.(gif|jpg|jpeg|tiff|png)$/i).test(_data['ProjectTaskAttachment']['attachment'])) {
									if(linkFile[_data['ProjectTaskAttachment']['id']]) _link = linkFile[_data['ProjectTaskAttachment']['id']];
									else _link = '/kanban/attachment/'+ _data['ProjectTaskAttachment']['id'] +'/?sid='+ api_key;
                                    _html += '<li><i class="icon-paper-clip"></i><span href="'+ _link +'" class="file-name fancy image" rel="one_pic_expand" data-fancybox="image" data-type="image" data-id = "' + _data['ProjectTaskAttachment']['id'] + '">' + _data['ProjectTaskAttachment']['attachment'] + '</span>'+ (!read_only ? ('<a  data-id = "' + _data['ProjectTaskAttachment']['id'] + '" data-taskid="' + id + '"><img src="/img/new-icon/delete-attachment.png" alt="' + _data['ProjectTaskAttachment']['id'] + '" onclick="deleteAttachmentFile.call(this)"></a>') : '') +'</li>';
                                } else {
                                    _link = '/kanban/attachment/' + _data['ProjectTaskAttachment']['id'] + '/?download=1&?sid=' + api_key;
                                    _html += '<li><i class="icon-paper-clip"></i><a class="file-name" href="' + _link + '">' + _data['ProjectTaskAttachment']['attachment'] + '</a>'+ (!read_only ? ('<a  data-id = "' + _data['ProjectTaskAttachment']['id'] + '" data-taskid="' + id + '"><img src="/img/new-icon/delete-attachment.png" alt="' + _data['ProjectTaskAttachment']['id'] + '" onclick="deleteAttachmentFile.call(this)"></a>') : '') +'</li>';
                                }
                            } else {
                                _html += '<li><i class="icon-link"></i><a class="file-name" target="_blank" href="' + _data['ProjectTaskAttachment']['attachment'] + '">' + _data['ProjectTaskAttachment']['attachment'] + '</a>'+ (!read_only ? ('<a  data-id = "' + _data['ProjectTaskAttachment']['id'] + '" data-taskid="' + id + '"><img src="/img/new-icon/delete-attachment.png" alt="' + _data['ProjectTaskAttachment']['id'] + '" onclick="deleteAttachmentFile.call(this)"></a>') : '') +'</li>';
                            }
                        }
                    });
                }
                _html += '</ul>';
                $('#content_comment .append-comment').html(_html);
                // update read status
                var _tag = $('.wd-task-actions a.task-attachment[data-taskid="' + id + '"]');
                _tag.addClass('read');
                _tag.find('span').html(file_count);
            }
        });
    }
    function popupTaskComment() {
        var taskid = $(this).data("taskid");
        var _html = '';
        var latest_update = '';
        var index = 0;
        var has_comment = 0;
        var popup = $('#wd-task-comment-dialog');
        $.ajax({
            url: '/project_tasks/getCommentTxt',
            type: 'POST',
            data: {
                pTaskId: taskid
            },
            dataType: 'json',
            success: function (data) {
                draw_comment(popup, data);
                var _tag = $('.wd-task-actions a.task-comment[data-taskid="' + taskid + '"]');
                _tag.addClass('read');
            }
        });

    }
    ;
    $('body').on("change", "#update-comment", function () {
        var _this = $(this);
        var taskid = _this.data("taskid");
        var text_1 = _this.val();
        if (text_1 == '')
            return;
        var popup = $('#wd-task-comment-dialog');
        popup.find('.content_comment').addClass('loading');
        var comment_cont = popup.find('.content-logs-inner');
        var _html = '';
        $.ajax({
            url: '/project_tasks/update_text',
            type: 'POST',
            data: {
                data: {
                    id: taskid,
                    text_1: text_1
                }
            },
            dataType: 'json',
            success: function (data) {
                if (data) {
                    var idEm = data['_idEm'],
                            avatarSrc = js_avatar(idEm);
                    nameEmloyee = data['text_updater'],
                            comment = data['comment'],
                            created = data['text_time'];
                    _html = '<div class="content"><div class= "avatar"><span class="circle-name"><img class="avatar-image-img" src="' + avatarSrc + '"></span></div><div class="item-content"><p>' + nameEmloyee + ' ' + created + '</p><div class="comment">' + comment + '</div></div>';
					if(canModify) _html += '<a class="cm-delete" href="javascript:void(0);" onclick="deleteCommentTask(this, '+ data['id']+');"><img src="/img/new-icon/delete-attachment.png"></a>';
					_html += '</div>';
                    comment_cont.prepend(_html);
                    _this.val('');
                    popup.find('.content_comment').removeClass('loading');
                    comment_cont.parent().animate({scrollTop: 0}, 200);

                    var _tag = $('.wd-task-actions a.task-comment[data-taskid="' + taskid + '"]');
                    _tag.addClass('read');
                    _tag.find('span').html(parseInt(_tag.find('span').html()) + 1);
                }
            }
        });

    });
    function draw_comment(popup, data) {
        taskid = data.id;
        var _html = '';
        var latest_update = '';
        var index = 0;
        var has_comment = 0;
        var text_modified = '<?php __('Modified');?>';
        var text_by = '<?php __('by');?>';
        var check = canModified;
        var comment_tags = comment_tag = '';
        if (data) {
            latest_update = '';
            if (check)
                _html += '<div class="comment"><textarea data-taskid = ' + taskid + '  cols="30" rows="6" id="update-comment"></textarea></div>';
            _html += '<div class="content-logs"><div class="content-logs-inner">';
            // if (data.old_comment) {
                // var _cm = data['old_comment']['ProjectTask'];
                // latest_update = _cm.text_time.slice(0, 10);
                // latest_update = text_modified + ' ' + latest_update + ' ' + text_by + ' ' + _cm['text_updater'];
                // comment_tags += '<div class="content content-' + index++ + '">';
                // comment_tags += '<div class = "avatar"><span class="circle-name"><span>' + _cm['avt'] + '</span></span></div>';
                // comment_tags += '<div class="item-content"><p>' + _cm['text_updater'] + ' ' + _cm['text_time'] + '</p><div class="comment">' + _cm['text_1'] + '</div></div>'
                // comment_tags += '</div>';
                // if (_cm && !has_comment)
                    // has_comment = 1;
            // }
            if (data.result) {
                $.each(data.result, function (ind, _data) {
                    _cm = _data.ProjectTaskTxt;
                    if (_cm && ('id' in Object(_cm))) {
                        var name = ava_src = time = '';
                        latest_update = _cm.created.slice(0, 10);
                        comment = _cm['comment'] ? _cm['comment'].replace(/\n/g, "<br>") : '';
                        name = _data.Employee.first_name + ' ' + _data.Employee.last_name;
                        date = _cm.created;
                        latest_update = text_modified + ' ' + latest_update + ' ' + text_by + ' ' + name;
                        ava_src += '<img width = 35 height = 35 src="' + js_avatar(_cm['employee_id']) + '" title = "' + name + '" />';
                        comment_tag += '<div class="content content-' + index++ + '"><div class="avatar"><span class="circle-name">' + ava_src + '</span></div><div class="item-content"><p>' + name + ' ' + date + '</p><div class="comment">' + comment + '</div></div>';
						if(myRole == 'admin' || _cm['employee_id'] == emp_id_login)  comment_tag += '<a class="cm-delete" href="javascript:void(0);" onclick="deleteCommentTask(this, '+ _cm['id']+');"><img src="/img/new-icon/delete-attachment.png"></a>';
						comment_tag += '</div>';
                    }
                });
            }
			comment_tags = comment_tag + comment_tags;
            if (latest_update) {
                // draw progress here
            }
            _html += comment_tags;
            _html += '</div></div>';
        }

        popup.find('.content_comment:first').html(_html);

        var createDialog2 = function () {
            popup.dialog({
                position: 'center',
                autoOpen: false,
                height: 460,
                modal: true,
                width: (isTablet || isMobile) ? 320 : 520,
                minHeight: 50,
                open: function (e) {
                    var $dialog = $(e.target);
                    $dialog.dialog({open: $.noop});
                }
            });
            createDialog2 = $.noop;
        }
        createDialog2();
        popup.dialog('option', {title: data.task_title}).dialog('open');
    }
    function set_assigned($elm, datas) {
        $elm.find('.wd-combobox >.circle-name').remove();
        $elm.find('.wd-combobox >p').show();
        $elm.find('.wd-data input[type="checkbox"]').prop('checked', false).trigger('change');
        $elm.find('.wd-input-search').val('').trigger('change');
        $.each(datas, function (ind, data) {
            if (data.is_profit_center == 1) {
                $elm.find('input[value="' + data.reference_id + '-1"]').closest('.wd-data').click();
            } else {
                $elm.find('input[value="' + data.reference_id + '-0"]').closest('.wd-data').click();
                $elm.find('input[value="' + data.reference_id + '"]').closest('.wd-data').click();
            }
        });
    }
    $("#editTaskStartDayIndex, #editTaskEndDayIndex").datepicker({
        dateFormat: 'dd-mm-yy'
    });
    function show_form_alert(form, msg) {
        $(form).find('.alert-message').empty().append(msg);
        clearTimeout(alert_timeout);
        alert_timeout = setTimeout(function () {
            $(form).find('.alert-message').empty();
        }, 3000);
    }
    $('#ProjectTask').on('submit', function (e) {
		e.preventDefault();
		var form_tag = '#ProjectTask';
		handleSaveTask(form_tag);

    });
   
	function handleSaveTask(form_tag){
        var _form = $(form_tag);
        var start_date = $('#editTaskStartDayIndex').val(),
            end_date = $('#editTaskEndDayIndex').val();
        var st_date = start_date.split('-');
        var en_date = end_date.split('-');
        st_date = new Date(st_date[2], st_date[1], st_date[0]);
        en_date = new Date(en_date[2], en_date[1], en_date[0]);
        if (start_date && end_date && (st_date > en_date)) {
            show_form_alert(_form, "<?php __('Please enter the end date must be after the start date');?>");
            return;
        }
        _form.closest('.loading-mark').addClass('loading');
        $.ajax({
            type: "POST",
            url: _form.prop('action'),
            data: _form.serialize(),
            dataType: 'json',
            success: function (data) {
				// console.log(data);
                if (data.result == 'success') {
                    var _task = [];
                    _task['status'] = parseInt(data['message']['task_status_id']);
                    _task['id'] = parseInt(data['message']['id']);
                    _task['text'] = data['message']['task_title'];
                    data_tasks[_task['id']] = data['message'];
					_task['tags'] = "user, addTask";
                    $('#kanban').jqxKanban('removeItem', data['message']['id']);
                    $('#kanban').jqxKanban('addItem', _task);
                    $('#kanban_' + _task['id']).addClass('jqx-kanban-item').data('kanbanItemId', _task['id']);
					$('#widget-task').find('.task-' + _task['id']).addClass('removed');
                    $('#widget-task').find('.task-' + _task['id']).removeClass('task-' + _task['id']);
                    // add task into slider
					$('#widget-task').find('.status-' + _task['status']).append('<li class="task-' + _task['id'] + '">' + renderTaskItem(data['message']) + '</li>');
					 cancel_popup(form_tag);
                } else {
                    show_form_alert('#' + _form.prop('id'), data.message);

                }
                _form.closest('.loading-mark').removeClass('loading');
            },
            error: function () {
                _form.closest('.loading-mark').removeClass('loading');
            },
			complete: function(){
				filterTask();
				updateListAssigned();
			}
        });
	}
    function openEditTask() {
        var _this = $(this);
        var taskid = _this.data('taskid');
        var popup = $('#template_edit_task_index');
        popup.find('.loading-mark:first').addClass('loading');
        var popup_width = show_workload ? 1080 : 580;
		show_full_popup( '#template_edit_task_index', {width: popup_width});
        $.ajax({
            url: '/project_tasks/get_task_info/',
            type: 'POST',
            data: {
                data: {
                    id: taskid,
                }
            },
            dataType: 'json',
            success: function (data) {
                if (data) {
                    if (data.result == 'success') {
						c_task_data = data.data;
                        $('#editTaskID').val(data.data.id).trigger('change');
                        $('#task_title').val(data.data.task_title).trigger('change');
                        $('#toPhaseIndex').val(data.data.project_planed_phase_id).trigger('change');
                        $('#toStatus').val(data.data.task_status_id).trigger('change');
                        var start_date = data.data.task_start_date,
                                end_date = data.data.task_end_date;
                        var st_date = start_date.split('-');
                        var en_date = end_date.split('-');
                        st_date = st_date[2] + '-' + st_date[1] + '-' + st_date[0];
                        en_date = en_date[2] + '-' + en_date[1] + '-' + en_date[0];
                        $('#editTaskStartDayIndex').val(st_date).trigger('change');
                        $('#editTaskEndDayIndex').val(en_date).trigger('change');
                        set_assigned(popup.find('.multiselect-pm:first'), data.data.assigned);
						resetOptionAssigned(data.employees_actif, '#list_task_assign_to_edit');
                    } else {
                        show_form_alert('#ProjectTask', "data.message");
                    }
                } else {
					c_task_data = {};
                    show_form_alert('#ProjectTask', "<?php __('Get task failed');?>");
                }
                popup.find('.loading-mark:first').removeClass('loading');
            },
            error: function () {
				c_task_data = {};
                show_form_alert('#ProjectTask', "<?php __('Get task failed');?>");
                popup.find('.loading-mark:first').removeClass('loading');
            }
        });


    }
	function resetOptionAssigned(employees_actif, id_element){
		if(employees_actif.length > 0){
			$.each(employees_actif, function(i, employee){
				if(employee['Employee']['actif'] == 0){
					itemEle = '.wd-group-'+employee['Employee']['id'];
					$(id_element).find(itemEle).addClass('wd-actif-0');
				}
			});
		}
	}
    function add_new_task_item(data) {
        var _task = [];
        _task['status'] = parseInt(data['message']['ProjectTask']['task_status_id']);
        _task['id'] = parseInt(data['message']['ProjectTask']['id']);
        _task['text'] = data['message']['ProjectTask']['task_title'];
        data_tasks[_task['id']] = data['message']['ProjectTask'];
        $('#kanban').jqxKanban('addItem', _task);
        $('#kanban_' + _task['id']).addClass('jqx-kanban-item').data('kanbanItemId', _task['id']);
        // add task into slider
        $('#widget-task').find('.status-' + _task['status']).append('<li class="task-' + _task['id'] + '">' + renderTaskItem(data['message']) + '</li>');
		filterTask();
		updateListAssigned();
    }
    function newTaskDropzone_success_calback(file, dropzone_elm) {
        // var popup = $(dropzone_elm).closest('.wd-full-popup').fadeOut(300);
        cancel_popup(dropzone_elm);
        var _xhr = '';
        var respon = '';
        if ('xhr' in file)
            _xhr = file.xhr;
        if ('responseText' in _xhr)
            respon = _xhr.responseText;
        data = JSON.parse(respon);
		if (data.success) {
			add_new_task_item(data);
			cancel_popup(dropzone_elm);
		} else {
			var _form = $(dropzone_elm).closest('form');
			show_form_alert('#' + _form.prop('id'), data.message);
			$(dropzone_elm).closest('.loading-mark').removeClass('loading');
		}
        add_new_task_item(data);
    }
     function new_task_dropzone() {
        // return;
        var newTaskDropzone = '';
        var dropzone_elm = '#wd-upload-popup';
        $(function () {
            newTaskDropzone = new Dropzone(dropzone_elm, {
                // acceptedFiles: ".jpg,.jpeg,.bmp,.gif,.png,.txt,.doc,.xls,.pdf,.docx,.xlsx,.ppt,.pps,.pptx,.csv,.xlsm,.msg",
                acceptedFiles: "",
                imageSrc: "/img/new-icon/drop-icon.png",
                dictDefaultMessage: "<?php __('Drag & Drop your document or browse your folders');?>",
                autoProcessQueue: false,
                addRemoveLinks: true,
                maxFiles: 1,
            });
            newTaskDropzone.on("queuecomplete", function (file) {
                $(dropzone_elm).closest('.loading-mark').removeClass('loading');
            });
            newTaskDropzone.on("success", function (file) {
                newTaskDropzone.removeFile(file);
                if (typeof newTaskDropzone_success_calback == 'function')
                    newTaskDropzone_success_calback(file, dropzone_elm);
                cancel_popup(dropzone_elm);
            });
            $(dropzone_elm).closest('form').on('submit', function (e) {
                $(dropzone_elm).closest('.loading-mark').addClass('loading');
                if (newTaskDropzone.files.length) {
                    e.preventDefault();
                    newTaskDropzone.processQueue();
                } else {
                    e.preventDefault();
                    var _form = $(dropzone_elm).closest('form');
                    $.ajax({
                        type: "POST",
                        url: _form.prop('action'),
                        data: _form.serialize(),
                        dataType: 'json',
                        success: function (data) {
                            if (data.success) {
                                add_new_task_item(data);
                                cancel_popup(dropzone_elm);
                            } else {
                                show_form_alert('#' + _form.prop('id'), data.message);
                                $(dropzone_elm).closest('.loading-mark').removeClass('loading');
                            }

                        }
                    });
                }

            });
            newTaskDropzone.on('sending', function (file, xhr, formData) {
                // Append all form inputs to the formData Dropzone will POST
                var data = $(dropzone_elm).closest('form').serializeArray();
                $.each(data, function (key, el) {
                    formData.append(el.name, el.value);
                });
            });
        });
    } 
	new_task_dropzone();
	// function NewProjectTask_afterSubmit(_form_id, data){
		// var _form = $('#' + _form_id);
		// if (data.success) {
			// add_new_task_item(data);
			// cancel_popup('#' + _form_id);
		// } else {
			// show_form_alert('#' + _form.prop('id'), data.message);
			// _form.closest('.loading-mark').removeClass('loading');
		// }
	// }
    $(document).on('ready', function () {
        $('form').find('select, input').trigger('change');
    });
	// NEW POPUP EDIT
	$(window).ready(function(){
		setTimeout(function(){
			if( !$('#addProjectTemplate').hasClass('loaded') && !$('#addProjectTemplate').hasClass('loading') && !read_only){
				$('#addProjectTemplate').addClass('loading');
				$.ajax({
					url : "/project_tasks_preview/add_task_popup/" + project_id,
					type: "GET",
					cache: false,
					success: function (html) {
						$('#addProjectTemplate').empty().append($(html));
						$('.tabPopup .liPopup a.active').empty().html('<?php __('Create a task');?>');
						$('#template_add_task .btn-ok span').empty().html('<?php __('Save');?>');
						$('.right-link').hide();
						$('#add-form').trigger('reset');
						$(window).trigger('resize');
						$('#addProjectTemplate').addClass('loaded');
						$('#addProjectTemplate').removeClass('loading');
						$('input[data-return="form-return"]').val('<?php echo $this->here;?>');
						if( $('#addProjectTemplate').hasClass('open') ){
							var popup_width = show_workload ? 1080 : 580;
							show_full_popup( '#template_add_task', {width: popup_width});
							$('#template_add_task').find('input, select').trigger('change');
						}
					},
					complete: function(){
						$('#addProjectTemplate').removeClass('loading');
					},
					error: function(){
						alert('aaa');
					}
				});
			}
		}, 2000);
	});
	function wd_task_add_new(elm){
		$('#addProjectTemplate').toggleClass('open');
		if( !$('#addProjectTemplate').hasClass('loaded') && !$('#addProjectTemplate').hasClass('loading') && !read_only){
			$('#addProjectTemplate').addClass('loading');
			$.ajax({
				url : "/project_tasks_preview/add_task_popup/" + project_id,
				type: "GET",
				cache: false,
				success: function (html) {
					$('#addProjectTemplate').empty().append($(html));
					$('.tabPopup .liPopup a.active').empty().html('<?php __('Create a task');?>');
					$('#template_add_task .btn-ok span').empty().html('<?php __('Save');?>');
					$('.right-link').hide();
					$('#add-form').trigger('reset');
					$(window).trigger('resize');
					$('#addProjectTemplate').addClass('loaded');
					$('#addProjectTemplate').removeClass('loading');
					$('input[data-return="form-return"]').val('<?php echo $this->here;?>');
					if( $('#addProjectTemplate').hasClass('open') ){
						var popup_width = show_workload ? 1080 : 580;
						show_full_popup( '#template_add_task', {width: popup_width});
						$('#template_add_task').find('input, select').trigger('change');
					}
				},
				complete: function(){
					$('#addProjectTemplate').removeClass('loading');
				},
				// error: function(){
					// alert('error');
				// }
			});
		}else{
			var popup_width = show_workload ? 1080 : 580;
			show_full_popup( '#template_add_task', {width: popup_width});
			$('#template_add_task').find('input, select').trigger('change');
		}
	}
	function openEditTaskNCT(elm) {
		var id = $(elm).data('taskid');
		$.ajax({
			url : "/project_tasks/getNcTask/",
			type: "POST",
			cache: false,
			data: {data: {id: id}},
			dataType: 'json',
			success: function (data) {
				global_task_data = data;
				var type = 1;
				if( 'data' in global_task_data){
					$.each(global_task_data.data, function (key, val){
						console.log( val );
						if( typeof val[0]['type'] !== 'undefined') type = val[0]['type'];
						
						return false;
					});
				}
				$('#popupnct-range-type').val(type);
				show_full_popup('#template_add_nct_task', {width: 'inherit'});
				$('.popup-back').empty().html('<?php __('Edit NCT task');?>');
				$('#popupnct-id').val(id).trigger('change');
				$('#popupnct-name').val(data['task']['task_title']).trigger('change');
				$('#popupnct-phase').val(data['task']['project_planed_phase_id']).trigger('change');
				
				var assigns = [];
				if(data['columns']){
					$.each(data['columns'], function(key, column){
						id = (column.id).split('-');
						assigns.push({reference_id: id[0], is_profit_center: id[1]});
					});
				}
				render_list_date(data);
				set_assigned($('#template_add_nct_task').find('.popupnct_nct_list_assigned'), assigns);
				resetOptionAssigned(data.employees_actif, '#multiselect-popupnct-pm');
				$('#popupnct-status').val(data['task']['task_status_id']).trigger('change');
				$('#popupnct-profile').val(data['task']['profile_id']).trigger('change');
				$('#popupnct-priority').val(data['task']['task_priority_id']).trigger('change');
				$('#popupnct_per-workload').val(data['task']['estimated']).trigger('change');
				$('#template_add_nct_task #btnSave').empty().html('<?php __('Save');?>');
				setLimitedDate('#popupnct-start-date', '#popupnct-end-date');
				set_width_popupnct();
			}
		});
	
	}
	function render_list_date(data){
		var request = data.request;
		var data = data.data;
		var consumed = 0, in_used = 0;
		$.each(data, function(row, val){
			var date = row.substr(2);
			var date_name = toRowName(row);
			var html = '<tr><td id="date-' + date + '" class="popupnct-date" style="text-align: left">' + toRowName(row) + '<span class="cancel" onclick="removeRow(this)" href="javascript:;"></span></td>';
			
			var c = parseFloat(request[row][0]), iu = parseFloat(request[row][1]);
            if( isNaN(c) )c = 0;
            if( isNaN(iu) )iu = 0;
			//last col
            html += '<td style="background: #f0f0f0" class="ciu-cell">' + c.toFixed(2) + ' (' + iu.toFixed(2) + ')</td>';
			html += '<td class="row-action">';
			if(!( c > 0 || iu > 0 )){
                html += '<a class="cancel" onclick="popupnct_removeRow(this)" href="javascript:;"></a>';
            }
			html += '</td></tr>';
            $('#popupnct-assign-table tbody').append(html);
			consumed += c;
			in_used += iu;
		});
		$('#popupnct_total-consumed').empty().html(consumed.toFixed(2) +' ('+ in_used.toFixed(2) +')');
	}
	function deleteCommentTask(_this, cm_id){
		if(!canModify) return;
		$(_this).addClass('loading');
		if(cm_id){
			 $.ajax({
				url: '/project_tasks/deleteCommentTxt',
				data: {
					cm_id: cm_id
				},
				type:'POST',
				dataType: 'json',
				success: function(res){
					if(res == 'success'){
						$(_this).closest('.content').remove();
						$(_this).removeClass('loading');
					}
				}			
			});
		}
	}
	/*
	* Workload table
	* init: list_assigned, c_task_data, show_workload
	*/
	function template_add_task_showed(){
		init_multiselect('#template_add_task .wd-multiselect');
	}
	function c_calcTotal(){
		$.each( $('.task-workload .nct-assign-table'), function( ind, elm){
			var _total = 0;
			$.each( $(elm).find('.c_workload'), function(ind, inp){
				_total += $(inp).val() ? parseFloat($(inp).val()) : 0;
			});
			$(elm).find('.total-consumed').text( _total.toFixed(2) );
		});
	}
	function draw_c_workload_table(elm){
		if( !elm) return;
		if(list_assigned.length == 0){
			elm.find('.nct-assign-table tbody').empty();
			return;
		}
		var _data_assigned = {};
		if(c_task_data && c_task_data.assigned){
			$.each(c_task_data.assigned, function(ind, emp){
				var key = emp.reference_id + '-' + emp.is_profit_center;
				_data_assigned[key] = emp;
			});
		}
		$.each( elm.find('.nct-assign-table .c_workload'), function(ind, cell){
			var id = $(cell).data('id');
			if( !(id in _data_assigned) ){
				$(cell).closest('tr').remove();
			}
		});
		$.each(list_assigned, function(ind, emp){
			var id = emp.id,
				name = emp.name;
			var employee = (emp.id).split('-');
			var e_id = employee[0];
			var is_profit_center = employee[1]==1 ? 1 : 0;
			
			var tag = '.c_workload-' + id;
			if( $(tag).length == 0){
				var _avt = '<span class="circle-name" title="' + name + '" data-id="' + id + '">';
				if( is_profit_center == 1 ){
					_avt += '<i class="icon-people"></i>';
				}else{
					_avt += '<img width = 35 height = 35 src="'+  js_avatar(e_id ) +'" title = "'+ name +'" alt="avatar"/>';	
				}
				_avt += '</span>';
				
				var res_col = '<td class="col_employee" >' + _avt + '</td>';
				
				var e_workload = (id in _data_assigned) ? _data_assigned[id]['estimated'] : 0;
				e_workload = parseFloat(e_workload).toFixed(2);
				var ip_name = 'data[workloads][' + id + ']';
				var val_col = '<td class="col_workload"><input type="text" id="val-' + id + '" class="c_workload c_workload-' + id + '"  id="c_workload-' + id + '" data-id="' + id + '" value="'+ e_workload +'" name="' + ip_name + '[estimated]" onkeyup="c_calcTotal(this)"/></td>';
				var _html = '<tr class="workload-row workload-row-' + id + ' ">' + res_col + val_col + '</tr>';
				elm.find('.nct-assign-table tbody').append( _html);
			}
		});
		c_calcTotal();
	}
	function get_list_assign(_this_id){
		var multiSelect = $('#' + _this_id);
		var _list_selected = multiSelect.find(':checkbox:checked');
		var employee_selected = []; 
		if( _list_selected.length){
			$.each( _list_selected, function(ind, emp){
				var key = $(emp).val();
				var val = $(emp).next('.option-name').text();
				employee_selected.push({id: key, name: val});
			});
		}
		return employee_selected;
	}
	function list_task_assign_to_editonChange(_this_id){
		list_assigned = get_list_assign(_this_id);
		var _form = $('#' + _this_id).closest('form');
		draw_c_workload_table(_form);
	}
	function popupnct_nct_list_assignedonChange(_this_id){
		list_assigned = get_list_assign(_this_id);
		var _form = $('#' + _this_id).closest('form');
		draw_c_workload_table(_form);
	}
	function cancel_popup_template_edit_task_index(){
		c_task_data = {};
		list_assigned = {};
		$('#task_assign_table').find('tbody').empty();
	}
	function list_task_assign_toonChange(_this_id){
		list_assigned = get_list_assign(_this_id);
		var _form = $('#' + _this_id).closest('form');
		draw_c_workload_table(_form);
	}
	function cancel_popup_template_add_task(){
		c_task_data = {};
		list_assigned = {};
		$('#new_task_assign_table').find('tbody').empty();
	}
	/*
	* END Workload table
	*/
</script>
