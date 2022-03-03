<?php 
echo $html->script(array(
	'jquery.multiSelect',
	'draw-progress',
	'preview/define_limit_date',
	'easytabs/jquery.easytabs.min',
	'history_filter',
	'dropzone.min',
	// 'common',
	'html2canvas',
	'jquery.mCustomScrollbar',
	'jquery.html2canvas_v2_preview',
	'jquery.easing.1.3',
));

echo $html->css(array(
    'layout_2019',
    'add_popup',
    'jquery.multiSelect',
	'dropzone.min',
    'preview/project_task',
    // 'jquery.dataTables',
    'gantt_v2_1',
    'jquery.mCustomScrollbar',
    'preview/project_task_gantt',
    'preview/component',
    'preview/availability-popup',
));
$arg = $this->passedArgs;
$arg["?"] = $this->params['url'];
unset($arg['?']['url'], $arg['?']['ext']);
$showGannt = isset( $showGannt) ? $showGannt : 1;
$show_workload = !(empty($adminTaskSetting['Workload'])) ? $adminTaskSetting['Workload'] : 0;
$show_duration = !(empty($adminTaskSetting['Duration'])) ? $adminTaskSetting['Duration'] : 0;
$display_milestone = !(empty($adminTaskSetting['Milestone'])) ? $adminTaskSetting['Milestone'] : 0;
$display_manualConsumed = ( !empty($companyConfigs['manual_consumed']) && ($companyConfigs['manual_consumed'] == '1') && !empty($adminTaskSetting['Manual Consumed'])) ? 1 : 0;
$canModified = (!empty($canModified) && !$_isProfile ) || ($_isProfile && $_canWrite);
$display_activity_forecast = isset($companyConfigs['display_activity_forecast']) ? $companyConfigs['display_activity_forecast'] : 0;
$display_disponibility = isset($companyConfigs['display_disponibility']) ? $companyConfigs['display_disponibility'] : 0;
$showDisponibility = !empty($display_activity_forecast) && !empty($display_disponibility ) ?  1 : 0;
$priority = json_decode($listPrioritiesJson, true);
?>

<div id="loading-mask"></div>
<div id="loading">
  <div class="loading-indicator">
  </div>
</div>
<?php echo $html->css(array('projects','slick_grid/slick.edit','project_task')); ?>
<script type="text/javascript">
    var showProfile = <?php echo isset($companyConfigs['activate_profile']) ? (string) $companyConfigs['activate_profile'] : '0' ?>;
    var showDisponibility = <?php echo $showDisponibility ?>;
    var is_manual_consumed = <?php echo isset($companyConfigs['manual_consumed']) ? (string) $companyConfigs['manual_consumed'] : '0' ?>;
    var gap_linked_task = <?php echo isset($companyConfigs['gap_linked_task']) ? (string) $companyConfigs['gap_linked_task'] : '0' ?>;
    var task_no_phase = <?php echo isset($companyConfigs['task_no_phase']) ? (string) $companyConfigs['task_no_phase'] : '0' ?>;
    var create_ntc_task = <?php echo isset($companyConfigs['create_ntc_task']) ? (string) $companyConfigs['create_ntc_task'] : '0' ?>;
    var webroot = <?php echo json_encode($this->Html->url('/')) ?>;
    var hightlightTask = <?php echo json_encode(isset($_GET['id']) ? $_GET['id'] : '') ?>;
    var canModify = <?php echo json_encode((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite)); ?>;
	var readOnly = <?php echo json_encode(!empty($_isProfile) && !$_canWrite); ?>;
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
	var _keyPressed ={16: false, 17: false}, _selectIconClicked = {clicked: false, index: 0, isSelected: false,	old_status: false};
	var show_workload = <?php echo json_encode($show_workload); ?>; 
</script>
<style>
	.x-box-target .x-box-item:not(:last-child) {
		border-right-color: white !important;
    }
	.new-design .x-grid-item .x-grid-cell-inner {
		display: inline-block;
		vertical-align: middle;
	}
	.new-design .x-grid-item .x-grid-cell {
		border-right: 1px solid #F2F5F7;
	}
	#addProjectTemplate.open.loading{
		position: fixed;
		width: 100vw;
		height: 100vh;
		z-index:99;
		top: 0px;
		left:0px;
	}
	.form-style-2019 .wd-multiselect.multiselect a.wd-combobox{
		line-height: 65px;
	}
	.wd-multiselect .wd-combobox-content a.circle-name, .wd-multiselect .wd-combobox a.circle-name{
		vertical-align: middle;
	}
	#special-task-info-2 #assign-table{
		margin: 0;
	}
	.wd-multiselect.multiselect .circle-name {
		height: 28px;
		width: 28px;
	}
	body .x-grid-dirty-cell{
		background: none;
	}
	.wd-title >* {
		line-height: 38px;
	}
	#displayFreeze li{
		line-height: 20px;
	}
	#displayFreeze .project_off_freeze:first-child{
		line-height: inherit;
	}
	.cancel_skip{
		background-color: #C6CCCF !important;
		font-size: 15px;
		color: #fff;
		text-transform: uppercase;
		width:115px;
		border:none;
		height: 44px;
		border-radius: 3px;
		cursor: pointer;
		margin-left: 27px;
	}
	.yes_skip{
		background-color: #C6CCCF !important;
		font-size: 15px;
		color: #fff;
		text-transform: uppercase;
		width:115px;
		border:none;
		height: 44px;
		border-radius: 3px;
		cursor: pointer;
	}
	.yes_importcsv{
		background-color: #C6CCCF !important;
		font-size: 15px;
		color: #fff;
		text-transform: uppercase;
		width:115px;
		border:none;
		height: 44px;
		border-radius: 3px;
		cursor: pointer;
		margin-left: -40px;
	}
	.buttons ul.type_buttons{
		float: none;
		margin-bottom: 10px;
	}
	.int-sub{
		font-size: 15px;
	}
	#dialog_import_CSV label{
		font-size: 15px;
	}
	body .wd-layout .wd-main-content .wd-tab{
		padding-bottom: 0!important;
	}
	body #layout{
		background: #f2f5f7;
	}
	.x-icon.x-icon-filter.x-icon-filtered .x-column-header-text-wrapper{
		background-image: unset !important;
	}
	.gantt-content #GanttChartDIV.mini-gantt .gantt-primary > tbody{
		position: inherit;
	}
	@media print{
	  .hidden-print {
		display: none !important;
	  }
	}
	.wd-form-data-row{
		margin-bottom: 20px;
	}
	.edit_task-popup .wd-row-inline{
		z-index: 2;
		position: relative;
		overflow: visible;
		min-height: 350px;
	}
	.edit_task-popup .wd-submit-row{
		z-index: 1;
		position: relative;
	}
	.wd-list-assign-to{
		position: relative;
	}
	.total-workload input{
		font-weight: 600;
	}
	/* Chrome, Safari, Edge, Opera */
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
	  -webkit-appearance: none;
	  margin: 0;
	}

	/* Firefox */
	input[type=number] {
	  -moz-appearance: textfield;
	}
	span.c_manday{
		position: relative;
	}
	span.c_manday.c_employee{
		cursor: pointer;
	}
	span.c_manday.loading:after {
		position: absolute;
		left: 50%;
		top: 50%;
		content: '';
		transform: translate(-50%, -50%);
		width: 20px;
		height: 20px;
		background: rgba(255,255,255,0.75) url(/img/business/wait-1.gif) center no-repeat;
		background-size: contain;
		z-index: 2;
		display: inline-block;
	}
	.gs-header-content-over span.inRed{
		color: red;
	}
	.col_manday .c_manday.c_overload{
		color: red;
		font-weight: bold;
	}
	#layout .wd-layout > .wd-main-content > .wd-tab > .wd-panel{
		margin-top: 0;
	}
	#mcs1_container table.gantt .gantt-node-head .gantt-head td div{
		height: 40px;
		line-height: 40px;
		font-size: 26px;
	}
	.display_duration {
		width: 8% !important;
		display: inline-block;
		margin-bottom: unset !important;
	}
	.display_duration label{
		display: none !important;
	}
	.display_duration input{
		padding: 2px !important;
		text-align: center;
	}
	.wd-col.wd-col-sm-6.wd-startdate {
		width: 45%;
		padding-right: 0px;
		margin-right: 1px;
		display: inline-block;
		float: left;
		text-align: left;
	}
	.wd-col.wd-col-sm-6.wd-enddate {
		width: 45%;
		padding: unset;
		float: right;
		margin-left: 1px;
		display: inline-block;
		text-align: left;
	}
	.wd-row1 {
		height: 50px;
		margin-bottom: 20px;
		text-align: center;
	}
</style>

<?php 
function xmultiSelect($_this, $fieldName, $fielData, $textHolder, $pc = array()){
    $cotentField = '';
    $cotentField = '<div class="wd-multiselect multiselect multiselect-pm">
    <a href="" class="wd-combobox wd-project-manager"><p style="position: absolute; color: #c6cccf">'. $textHolder .'</p></a>
    <div class="wd-combobox-content '. $fieldName .'" style="display: none;">
    <div class="context-menu-filter"><span><input type="text" class="wd-input-search" placeholder="Rechercher..." rel="no-history"></span></div><div class="option-content">';
    foreach($fielData as $idPm => $namePm):
		$avatar = '<img src="' . $_this->UserFile->avatar($idPm) . '" />';
		$employee_name = explode(' ', $namePm);
        $cotentField .= '<div class="projectManager wd-data-manager wd-group-' . $idPm . '">
            <p class="projectManager wd-data">
                <a class="circle-name" title="' . $fielData[$idPm] . '"><span data-id = "'. $idPm . '">'. $avatar .'</span></a>' .
                $_this->Form->input($fieldName, array(
                    'label' => false,
                    'div' => false,
                    'type' => 'checkbox',
                    'name' => 'data['. $fieldName .'][]',
                    'value' => $idPm)) .'
                <span class="option-name" style="padding-left: 5px;">' . $namePm . '</span>
            </p>
        </div>';
    endforeach;
    if(!empty($pc)): 
        foreach($pc as $idPm => $namePm):
            $cotentField .= '<div class="projectManager wd-data-manager wd-group-' . $idPm . '">
                <p class="projectManager wd-data">
                    <a class="circle-name" title="' . $pc[$idPm] . '"><span data-id = "'. $idPm . '"><i class="icon-people"></i></span></a> '.
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
?> 
<?php 
    $rows = 0;
    $start = $end = 0;
    $stones = array();
    $_milestoneColor = array();
	$listMilestoneTask = array();
    if (!empty($projectMilestones)) {
        foreach ($projectMilestones as $key => $p) {
            $_start = strtotime($p['milestone_date']);
            if (!$start || $_start < $start) {
                $start = $_start;
            } elseif (!$end || $_start > $end) {
                $end = $_start;
            }
            $stones[] = array($_start, $p['project_milestone'], $p['validated']);
            // tinh mau cho milestone.
            if(!empty($p['validated'])){
                $_milestoneColor[$p['id']] = 'milestone-green';
            } else {
                $currentDate = strtotime(date('d-m-Y', time()));
                $k = strtotime($p['milestone_date']);
                if ($currentDate > $k) {
                    $_milestoneColor[$p['id']] = 'milestone-mi';
                } elseif ($currentDate < $k) {
                    $_milestoneColor[$p['id']] = 'milestone-blue';
                } else {
                    $_milestoneColor[$p['id']] = 'milestone-orange';
                }
            }
			$listMilestoneTask[$key] = $p['project_milestone'];
        }
    }
echo $this->element('dialog_detail_value');

?>
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'project_tasks', 'action' => 'export', $projectName['Project']['id'])));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<!-- /export excel  -->
<!-- dialog_import -->

<div id="dialog_import_CSV" style="display:none" title="<?php __('Import CSV file') ?>" class="buttons">
    <?php
    echo $this->Form->create('Import', array('id' => 'uploadForm', 'type' => 'file',
        'url' => array('controller' => 'project_tasks', 'action' => 'import_csv', $projectName['Project']['id'])));
    ?>
    <div class="wd-input">
        <center>
            <label><?php echo __('File:') ?></label>
            <input class="int-sub" type="file" name="FileField[csv_file_attachment]" />
        </center>
        <div style="clear:both; margin-left:100px; width: 220px; color: #008000; font-style:italic;">(<?php __('Allowed file type') ?>: *.csv)</div>
    </div>
    <ul class="type_buttons">
        <li>
			<input id="no-importcsv" class="cancel_skip" type="button" value='<?php echo __('Close', true)?>'/>
		</li>
		<li>
			<input id="ok-importcsv" type="button" class="yes_importcsv" value='<?php echo __('OK',true)?>'/>
		</li>
        <li id="error"></li>
    </ul>
    <?php echo $this->Form->end(); ?>
</div>
<!-- dialog_import -->
<div id="dialog_import_MICRO" style="display:none" title="<?php __('Import MICRO file') ?>" class="buttons">
    <?php
    echo $this->Form->create('Import', array('id' => 'uploadFormMicro', 'type' => 'file',
        'url' => array('controller' => 'project_tasks', 'action' => 'import_task_micro_project', $projectName['Project']['id'])));
    ?>
    <div class="wd-input">
        <center>
            <label><?php echo __('File:') ?></label>
            <input type="file" name="FileField[micro_file_attachment]" />
        </center>
        <div style="clear:both; margin-left:100px; width: 220px; color: #008000; font-style:italic;">(<?php __('Allowed file type') ?>: *.xml)</div>
    </div>
    <ul class="type_buttons">
        <li><a class="cancel" href="javascript:void(0)"><?php echo __('Close') ?></a></li>
        <li><a id="import-micro-submit" class="new" onclick="return false;" href="#"><?php echo __('Submit') ?></a></li>
        <li id="error-micro"></li>
    </ul>
    <?php echo $this->Form->end(); ?>
</div>
<!-- End dialog_import --> 
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content new-design">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>

            <div class="wd-tab"><div class="wd-panel">
            <div class="wd-list-project">
                <div class="wd-title">
                    <div id="title-1">
                        <a href="<?php echo $html->url('/kanban/index/'.$projectName['Project']['id']) ?>" class="btn redirect-kanban hidden-print" style="margin-right:5px;" title="<?php __('Kanban') ?>"><i class="icon-grid"></i></a>
                        <a href="<?php echo $html->url("/project_phase_plans_preview/phase_vision/" . $projectName['Project']['id']) ?>" class="btn btn-gantt hidden-print" title="<?php __('Gantt+') ?>"></a>
                        <a href="<?php echo $html->url("/project_tasks/exportExcel/" . $projectName['Project']['id']) ?>" class="btn export-excel-icon-all hide-on-mobile hidden-print" id="export-submit" title="<?php __('Export Excel')?>"><span><?php __('Export Excel') ?></span></a>
						<?php 
						$is_show_import_csv = isset($companyConfigs['import_task_csv']) ? $companyConfigs['import_task_csv'] : 1;
						$is_show_import_xml = isset($companyConfigs['import_task_xml']) ? $companyConfigs['import_task_xml'] : 1;
						$is_show_import_excel = isset($companyConfigs['import_task_excel']) ? $companyConfigs['import_task_excel'] : 0;
						if( $is_show_import_csv == 1) { ?>
                        <a href="javascript:void(0)" class="btn import-excel-icon-all hide-on-mobile hidden-print" id="import_CSV" title="<?php __('Import CSV file')?>"><span><?php __('Import CSV') ?></span></a>
						<?php } 
						if( $is_show_import_xml == 1) { ?>
                        <a href="javascript:void(0)" class="btn import-micro-pro-icon-all hide-on-mobile hidden-print" id="import_MICRO" title="<?php __('Import Micro project')?>"><span><?php __('Import Micro project') ?></span></a>
                        <?php }
						if( $is_show_import_excel == 1 && $canModified) { ?>
						<a href="<?php echo $html->url('/project_tasks_preview/import_excel/'.$projectName['Project']['id']) ?>" class="btn import-excel-icon-all hide-on-mobile hidden-print" id="import_excel" title="<?php __('Import Excel file')?>"><span><?php __('Import Excel') ?></span></a>
						<?php } if($canModified){?>
							<a href="javascript:void(0);" class="btn btn-skip hide-on-mobile hidden-print" id="skip_value" title="<?php __('Skip') ?>"><span><?php __('Skip') ?></span></a>
						<?php }?>
						<a href="javascript:void(0);" class="btn btn-touch-move " style="display:<?php echo ($isTouch ? 'inline-block' : 'none') ?>" title="<?php __('Move a task (computer with a touch screen)') ?>"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><defs><style>.a-mile{fill:none;}.b-mile{fill:#d3d3d3;}</style></defs><g transform="translate(4 4)"><rect class="a-mile" width="40" height="40" transform="translate(-4 -4)"/><path class="b-mile" d="M-2915-656v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Zm10-5v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Zm10-5v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Z" transform="translate(2935 678)"/></g></svg></a>
						<a href="javascript:void(0)" class="btn filter-type type-red" data-type="red" title="<?php __('Show or hide task(s) with a warning') ?>"></a>
                        <a href="javascript:void(0)" class="btn filter-type type-green" data-type="green" title="<?php __('Show or hide task(s) without a warning') ?>"></a>
                        <a href="javascript:void(0);" class="btn btn-reset-filter hidden" id="clean-filters" style="margin-right:5px;" title="<?php __('Reset filter') ?>"></a>                       
                        
                        <?php if( $projectName['Project']['category'] == 2 ): ?>
                            <a id="reset-date" href="<?php echo $html->url('/project_tasks/reset_date/' . $project_id) ?>" title="<?php __('Reset the start and end date of all tasks to match with their phases') ?>" class="btn btn-reset" style= "display: none"></a>
                        <?php endif ?>
                    </div>					
					<a href="javascript:void(0);" onclick="expandTaskScreen();" class="btn btn-fullscreen hide-on-mobile wd-right hidden-print" id="expand"></a>
					<a href="javascript:void(0);" class="btn btn-collapse wd-right hidden-print" id="table-collapse" onclick="collapseTaskScreen();" title="Collapse Tasks Screen" style="display: none;"></a>
                    <?php if( $settingP['ProjectSetting']['show_freeze'] ):?>
                    <div class="buttons hide-on-mobile hidden-print" style="display: inline-block; vertical-align: top">
                        <?php if((($myRole=='pm')||($myRole=='admin'))&&($projectName['Project']['is_freeze']==0)){?>
                            <a href="<?php echo $this->Html->url('/project_tasks/freeze/'.$project_id);?>" id="submit-freeze-all-top" class="btn validate-for-validate validate-for-validate-top" title="<?php __('Freeze')?>"><span><?php __('Freeze'); ?></span></a>
                        <?php }?>
                        <?php if(($myRole=='admin')&&($projectName['Project']['is_freeze']==1)){?>
                            <a href="<?php echo $this->Html->url('/project_tasks/unfreeze/'.$project_id);?>" id="submit-unfreeze-all-top" class="validate-for-reject validate-for-reject-top" title="<?php __('Unfreeze')?>"><span><?php __('Unfreeze'); ?></span></a>
                         <?php }?>
                        <ul id="displayFreeze">
                            <?php
                            if(!empty($modifyF['Employee']['fullname']) && !empty($projectName['Project']['freeze_time']) ){
                                if( $projectName['Project']['is_freeze'] ==1 ){ ?>
                                    <li class="unfreeze-freeze"><?php echo sprintf(__('Freezed by %s at %s', true), $modifyF['Employee']['fullname'], date('d/m/Y h:i:s',$projectName['Project']['freeze_time']));?>  </li>
                                <?php } else { ?>
                                    <li class="unfreeze-freeze"><?php echo sprintf(__('Unfreezed by %s at %s', true), $modifyF['Employee']['fullname'], date('d/m/Y h:i:s',$projectName['Project']['freeze_time']));?>  </li>
                                <?php }
                            }
                            ?>
                            <li class="project_off_freeze">
                                <?php
                                echo $this->Form->input('Project.off_freeze', array(
                                    'rel' => 'no-history',
									'type' => 'checkbox',
									'class' => 'hidden',
                                    'label' => '<span class="wd-btn-switch"><span></span></span>' . __('Display initial information',true),
                                    'checked'=>$projectName['Project']['off_freeze'] ? true:false,
									'div' => array(
										'class' => 'wd-input wd-checkbox-switch',
										),
                                    'type' => 'checkbox', 
									// 'legend' => false, 'fieldset' => false
                                ));
                                ?>
                                
                            </li>
                        </ul>
                    </div>
                    <?php endif;?>
					<?php $gantt_views = array(
						'show' => __('Show GANTT', true),
						'hide' => __('Hide GANTT', true),
						'date' => __('View by Date', true),
						'week' => __('View by Week', true),
						'month' => __('View by Month', true),
						'year' => __('View by Year', true),
						'2years' => sprintf( __('View by %s Years', true), 2),
						'3years' => sprintf( __('View by %s Years', true), 3),
						'4years' => sprintf(__('View by %s Years', true), 4),
						'5years' => sprintf(__('View by %s Years', true), 5),
						'10years' => sprintf(__('View by %s Years', true), 10),
					);
					
						$displayGanttInitial  = 0; // default hide the intital line gantt
						$option_gantt = array(
							
						);
					?>
					<div class="wd-check-box">
						<div class="wd-input wd-checkbox-switch has-val" id="gantt-initial-switch" style="display: none" title="<?php echo  __('Initial schedule', true); ?>"><input type="hidden" name="data[ProjectTask][show_initial_gantt]" id="gantt-initial_" ><input type="checkbox" style="display: none" name="data[ProjectTask][show_initial_gantt]" onchange="removeLine(this,'n')" id="gantt-initial" ><label for="gantt-initial"><span class="wd-btn-switch"><span></span></span><?php echo  __('Initial schedule', true); ?></label></div>						
					</div>
					
					<div class="gantt-switch-display">
						<a href="javascript:void(0)" onclick="toggleGantt(this)" class="wd-switch <?php echo $showGannt ? 'active' : ''; ?> " id="gantt-switch"> <span class="wd-switch-icon"></span><span class="bt-switch-text"> <?php echo $showGannt ? $gantt_views['hide'] : $gantt_views['show']; ?> </span></a>
					</div>
			
					<div class="wd-check-box">
						<div class="wd-input wd-checkbox-switch has-val" id="gantt-initial-switch" style="display: none" title="<?php echo  __('Initial schedule', true); ?>"><input type="hidden" name="data[ProjectTask][show_initial_gantt]" id="gantt-initial_" ><input type="checkbox" style="display: none" name="data[ProjectTask][show_initial_gantt]" onchange="removeLine(this,'n')" id="gantt-initial" ><label for="gantt-initial"><span class="wd-btn-switch"><span></span></span><p class="ck-title"><?php echo  __('Initial schedule', true); ?></p></label></div>						
					</div>
					<div class="gantt-switch-view" <?php if( !$showGannt) echo 'style="display:none;"' ?>>
						<div class="wd-dropdown">
							<span class="selected">
								<?php echo $gantt_views[$type];?>
							</span>
							<span class="wd-caret"></span>
							<ul class="popup-dropdown">
								<li>
									<a href="javascript:void(0);" data-type="week" data-text="<?php echo $gantt_views['week'] ?>" class="<?php echo $type == 'week' ? 'gantt-switch-current active' : ''?>"><?php echo __('Week', true) ?></a>
								</li>
								<li>
									<a href="javascript:void(0);" data-type="month" data-text="<?php echo $gantt_views['month'] ?>" class="<?php echo $type == 'month' ? 'gantt-switch-current active' : ''?>"><?php echo __('Month', true) ?></a>
								</li>
								<li>
									<a href="javascript:void(0);" data-type="year" data-text="<?php echo $gantt_views['year'] ?>" class="<?php echo $type == 'year' ? 'gantt-switch-current active' : ''?>"><?php echo __('Year', true) ?></a>
								</li>
								<li>
									<a href="javascript:void(0);" data-type="2years" data-text="<?php echo $gantt_views['2years'] ?>" class="<?php echo ($type == '2years' ? 'gantt-switch-current active' : '') . ' x-year wd-hidden'?>"><?php echo __('2 Years', true) ?></a>
								</li>
								<li>
									<a href="javascript:void(0);" data-type="3years" data-text="<?php echo $gantt_views['3years'] ?>" class="<?php echo ($type == '3years' ? 'gantt-switch-current active' : '') . ' x-year wd-hidden'?>"><?php echo __('3 Years', true) ?></a>
								</li>
								<li>
									<a href="javascript:void(0);" data-type="4years" data-text="<?php echo $gantt_views['4years'] ?>" class="<?php echo ($type == '4years' ? 'gantt-switch-current active' : '') . ' x-year wd-hidden'?>"><?php echo __('4 Years', true) ?></a>
								</li>
								<li>
									<a href="javascript:void(0);" data-type="5years" data-text="<?php echo $gantt_views['5years'] ?>" class="<?php echo ($type == '5years' ? 'gantt-switch-current active' : '') . ' x-year wd-hidden'?>"><?php echo __('5 Years', true) ?></a>
								</li>
								<li>
									<a href="javascript:void(0);" data-type="10years" data-text="<?php echo $gantt_views['10years'] ?>" class="<?php echo ($type == '10years' ? 'gantt-switch-current active' : '') . ' x-year wd-hidden'?>"><?php echo __('10 Years', true) ?></a>
								</li>
							</ul>
						</div>
					</div>
					<div id="predecessor-switch" class="hidden wd-right hidden-print">
						<?php
						echo $this->Form->input('Project.show_predecessor', array(
							// 'rel' => 'no-history',
							'type' => 'checkbox',
							// 'label' => __('Hide the columns Predecessor and ID',true),
							'label' => false,
							'checked'=> false,
							'id' => 'ProjectShowPredecessor',
							'div' => array(
								'class' => 'wd-input wd-checkbox-switch',
								'title' => __('Hide the columns Predecessor and ID',true),
								),
							'type' => 'checkbox', 
							// 'legend' => false, 'fieldset' => false
						));
						?>
					</div>
					
                    <div id="resource-name-switch" class="wd-right hidden-print">
						<?php
						echo $this->Form->input('Project.show_resouce_name', array(
							'type' => 'checkbox',
							'label' => false,
							'checked'=> !empty($display_rescource_name) ? true : false,
							'rel' => 'no-history',
							'id' => 'ShowResouceName',
							'div' => array(
								'class' => 'wd-input wd-checkbox-switch',
								'title' => __('Display or hidde  the name of resource and team',true),
								),
							'type' => 'checkbox', 
						));
						?>
					</div>
					
                    <div id="auto-refresh-gantt-switch" class="wd-right hidden-print">
						<?php
						// debug( $autoRefreshGantt); exit; 
						echo $this->Form->input('Project.autoRefreshGantt', array(
							'type' => 'checkbox',
							'label' => false,
							'rel' => 'no-history',
							'checked'=> !empty($autoRefreshGantt) ? true : false,
							'id' => 'autoRefreshGantt',
							'div' => array(
								'class' => 'wd-input wd-checkbox-switch',
								'title' => __('Auto refresh GANTT',true),
								'style' => 'display: ' . ($showGannt ? 'block;' : 'none;'),
								),
							'type' => 'checkbox', 
						));
						?>
					</div>
					
                    <div id="gantt-display" class="table-container">
                    <table id="table-in-ex" class="hide-on-mobile hidden-print" <?php echo isset($companyConfigs['display_synthesis']) && $companyConfigs['display_synthesis'] == '0' ?'style="display:none"':''?>>
                        <thead>
                            <tr>
                                <th> &nbsp; </th>
                                <th><?php echo __('Budget');?></th>
                                <th><?php echo __('Workload');?></th>
                                <th><?php echo __('VAR%');?></th>
                                <th><?php echo __('Consumed');?></th>
                                <th><?php echo __('Remain');?></th>
                            </tr>
                        </thead>
                        <tbody id="displayWorkload">
                            <tr class="internal-line">
                                <td class="left-column"><?php echo __('Internal');?></td>
                                <td class="display-budget">
                                        <span class="display-value">
                                            <?php if($budgetInters['ProjectBudgetInternalDetail']['total']!=0){
                                                    echo $this->Number->format(round($budgetInters['ProjectBudgetInternalDetail']['total'],2), array(
                                                            'places' => 2,
                                                            'before' => ' ',
                                                            'escape' => false,
                                                            'decimals' => ',',
                                                            'thousands' => ''
                                                        ));
                                                   }else{
                                                    echo '0,00';
                                                   }
                                            ?>
                                        </span>
                                        <?php echo __('M.D', true);?>
                                </td>
                                <td class="display-workload int-wl">
                                    <span class="display-value">
                                    </span>
                                    <?php echo __('M.D', true);?>
                                </td>
                                <td class="display-var">
                                    <span class="display-value">
                                    </span>
                                </td>
                                <td class="display-internal-consumed">
                                    <span class="display-value">
                                        <?php echo $this->Number->format(round($consumedInter, 2), array(
                                            'places' => 2,
                                            'before' => ' ',
                                            'escape' => false,
                                            'decimals' => ',',
                                            'thousands' => ' '
                                        ));
                                        ?>
                                    </span>
                                    <?php echo __('M.D', true);?>
                                </td>
                                <td class="display-internal-remain">
                                    <span class="display-value">
                                        <?php echo $this->Number->format(round($remainInter, 2), array(
                                            'places' => 2,
                                            'before' => ' ',
                                            'escape' => false,
                                            'decimals' => ',',
                                            'thousands' => ' '
                                        ));
                                        ?>
                                    </span>
                                    <?php echo __('M.D', true);?>
                                </td>
                            </tr>
                            <tr class="external-line">
                                <td class="left-column"><?php echo __('External');?></td>
                                <td class="display-budget">
                                    <span class="display-value">
                                        <?php 
										if($budgetExters['ProjectBudgetExternal']['total']!=0){
											echo $this->Number->format(round($budgetExters['ProjectBudgetExternal']['total'],2), array(
												'places' => 2,
												'before' => ' ',
												'escape' => false,
												'decimals' => ',',
												'thousands' => ''
											));
										}else{
											echo '0,00';
										}
                                        ?>
                                    </span>
                                    <?php echo __('M.D', true);?>
                                </td>
                                <td class="display-workload ext-wl">
                                    <span class="display-value">
                                        <?php
                                            echo $this->Number->format(round($workloadExter,2), array(
                                                'places' => 2,
                                                'before' => ' ',
                                                'escape' => false,
                                                'decimals' => ',',
                                                'thousands' => ''
                                            ));
                                        ?>
                                    </span>
                                    <?php echo __('M.D', true);?>
                                </td>
                                <td class="display-var">
                                    <span class="display-value">
                                        <?php if($budgetExters['ProjectBudgetExternal']['total']!=0){
                                                echo $this->Number->format(round(($workloadExter/$budgetExters['ProjectBudgetExternal']['total']-1)*100,2), array(
                                                    'places' => 2,
                                                    'before' => ' ',
                                                    'escape' => false,
                                                    'decimals' => ',',
                                                    'thousands' => ' '
                                                )).' %';
                                            }else{
                                                echo '0,00 %';
                                        }?>
                                    </span>
                                </td>
                                <td class="display-external-consumed">
                                    <span class="display-value">
                                        <?php
                                            echo $this->Number->format(round($consumedExter['ProjectTask']['consumedex'],2), array(
                                                'places' => 2,
                                                'before' => ' ',
                                                'escape' => false,
                                                'decimals' => ',',
                                                'thousands' => ' '
                                            ));
                                        ?>
                                    </span>
                                    <?php echo __('M.D', true);?>
                                </td>
                                <td class="display-external-remain">
                                    <span class="display-value">
                                        <?php
                                            echo $this->Number->format(round($remainExter, 2), array(
                                                'places' => 2,
                                                'before' => ' ',
                                                'escape' => false,
                                                'decimals' => ',',
                                                'thousands' => ' '
                                            ));
                                        ?>
                                    </span>
                                    <?php echo __('M.D', true);?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div><br clear="all"  />
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
                <div id="message-place" style="clear: both;">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    ?>
                </div>
				<div class="gantt-content" id="gantt-content">
					<div id="GanttChartDIV" class="mini-gantt">
					</div>
				</div>
                <!-- Gantt.end -->
                <div class="wd-project-task-table">

                    <div class="wd-table" id="project_container" style=" height: 600px; width: 100%">
                    </div>
                    <div class="add-new-task">
                        <?php if(((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite))){ ?>
							<a href="javascript:void(0);"  id="open-modal" class="btn-add" title="<?php __('Add new task');?>"></a>
                        <?php } ?>
                    </div>
                    <div id="pager" style="width:100%;height:0; overflow: hidden; " class="slick-pager">
                    </div>
                </div>
            </div>
            <div id= "area-append-ntc">
                <div class="title-ntc">
                    <h4><?php echo __('Add a non-continuous task', true); ?></h4>
                    <a href="javascript:void(0);" class="close-ntc"><img src="/img/new-icon/close-light.png" /><img src="/img/new-icon/close-blue.png" /></a>
                </div>
            </div>
            <?php echo $this->element('grid_status'); ?>
            </div></div>
            </div>
        </div>
    </div>
</div>
<?php echo $this->element('dialog_projects') ?>
<?php
$i18n = array(
    //for extjs
    'Read only' => __('Read only', true),
    'Not assigned' => __('Not assigned', true),
    'New Task' => __('New Task', true),
    'Update' => __('Update', true),
    'Resources' => __('Resources', true),
    'Filter' => __('Filter', true),
    'Save' => __('Save', true),
    'Saved' => __('Saved', true),
    'Not saved' => __('Not saved', true),
    'Cancel' => __('Cancel', true),
    'Create not-continuous task' => __('Create not-continuous task', true),
    'Warning' => __('Warning', true),
    'Workload' => __('Workload', true),
    'Delete task and its sub-tasks?' => __('Delete task and its sub-tasks?', true),
    'Delete tasks and its sub-tasks?' => __('Delete tasks and its sub-tasks?', true),
    'Workload Detail For Employee And Profit Center' => __('Workload Detail For Employee And Profit Center', true),
    'Workload Detail' => __('Workload Detail', true),
    'Delete' => __('Delete', true),
    'Total workload' => __('Workload', true),
    'ok' => __('OK', true),
    'yes' => __('Yes', true),
    'no' => __('No', true),
    'cancel' => __('Cancel', true),
    'Move Here' => __('Move Here', true),
    'Move part and phase in the screens part and phase' => __('Move part and phase in the screens part and phase', true),
    'Workload {1} M.D , Workload filled {0} M.D.' => __('Workload {1} M.D , Workload filled {0} M.D.', true),
    'Please wait' => __('Please wait', true),
    'The task name already exists' => __('The task name already exists', true),
    'No consumed and the current date > start date' => __('No consumed and the current date > start date', true),
    'Consumed > Workload' =>  __('Consumed > Workload', true),
    'Task not closed and the current date > end date' => __('Task not closed and the current date > end date', true),
    '{0} is linked to other(s) task(s). If you modified the end date the tasks(s) linked will be modified. However the duration of the tasks linked will not be modified?' => __('{0} is linked to other(s) task(s). If you modified the end date the tasks(s) linked will be modified. However the duration of the tasks linked will not be modified?', true),
    'This task is in used/ has consumed' => __('This task is in used/ has consumed', true),
    'Cannot be deleted task linked' => __('Cannot be deleted task linked', true),
    'Reset' => __('Reset', true),
    'Task name already existed' => __('Task name already existed', true),
    'Edit' => __('Edit', true),
    'Keep the duration of the task ' => __('Keep the duration of the task ', true),
    'Check all' => __('Check all', true),
    'Uncheck all' => __('Uncheck all', true),
    'Error' => __('Error', true),
	'show_gant' => __('Show GANTT', true),
	'hide_gant' => __('Hide GANTT', true),
	'Batch update tasks' => __('Batch update tasks', true),
	'Batch delete tasks' => __('Batch delete tasks', true),
	'Dates cannot be updated, a task has a predecessor' => __('Dates cannot be updated, a task has a predecessor', true),
	'Dates cannot be updated, there is an NCT task' => __('Dates cannot be updated, there is an NCT task', true),
	'New resources' => __('New resources', true),
	'Adjusted the workload with consumed' => __('Adjusted the workload with consumed', true),
	'All these users will have this workload' => __('All these users will have this workload', true),
	'All current workloads will be overwritten' => __('All current workloads will be overwritten', true),
);
$i18n = array_merge($i18n, $this->requestAction('/translations/getByLang/Project_Task'));
$i18n = json_encode($i18n);
?>
<script>
    var i18n_text = <?php echo $i18n; ?>;
    var displayGanttInitial = <?php echo $displayGanttInitial; ?>;
    //var columns_order = <?php echo json_encode(array_merge(array('id|1'), $this->requestAction('/admin_task/getTaskSettings'))) ?>;
    var _milestoneColor = <?php echo json_encode($_milestoneColor) ?>;
    var columns_order = <?php echo json_encode($this->requestAction('/admin_task/getTaskSettings')) ?>;
    var show_unit = show_priority = show_profile = show_milestone = false;
	var key = 0;
    $.each(columns_order, function(index, value){
        var _v = value.split('|');
        if(_v[0] == 'UnitPrice'){
            if(_v[1] == 1) show_unit = true;
        }
        if(_v[0] == 'Milestone'){
            if(_v[1] == 1) show_milestone = true;
        }
        if(_v[0] == 'Priority'){
            if(_v[1] == 1) show_priority = true;
        }
        if(_v[0] == 'Profile'){
            if(_v[1] == 1) show_profile = true;
        }
		key = index;
    });
	
	// Add action delete
	if( canModify ){
		columns_order[key+1] = 'Delete|1';
	}
    var manual;
    function i18n(text){
        if( typeof i18n_text[text] != 'undefined' )return i18n_text[text];
        return text;
    }
   

</script>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<div id="showdetail">
	<div class="gs-header-title">
		<ul>
			<li><a href="javascript:;" id="filter_date"><?php echo __("Day", true)?></a></li>
			<!--li><a href="" id="filter_week"><?php echo __("Week", true)?></a></li-->
			<li><a href="javascript:;" id="filter_month"><?php echo __("Month", true)?></a></li>
			<li><a href="javascript:;" id="filter_year"><?php echo __("Year", true)?></a></li>
		</ul>
	</div>
    <div id="gs-popup-content">
        <div class="table-left">
            <table id="tb-popup-content">
                <tr class="popup-header">
                    <td style="width: 450px;">&nbsp;</td>
                    <td style="width: 90px;"><div class="text-center"><?php __('Priority');?></div></td>
                    <td style="width: 60px;"><div class="text-center"><?php __('Total');?></div></td>
                </tr>
                <tr>
                    <td class="popup-header-group"><div><?php __('Working day');?></div></td>
                    <td>&nbsp;</td>
                    <td id="total-working" class="text-right">&nbsp;</td>
                </tr>
                <tr>
                    <td class="popup-header-group"><div><?php __('Absence');?></div></td>
                    <td>&nbsp;</td>
                    <td id="total-vacation" class="text-right">&nbsp;</td>
                </tr>
                <tr>
                    <td class="popup-header-group"><div><?php __('Capacity');?></div></td>
                    <td>&nbsp;</td>
                    <td id="total-capacity" class="text-right">&nbsp;</td>
                </tr>
                <tr>
                    <td class="popup-header-group"><div><?php __('Workload');?></div></td>
                    <td>&nbsp;</td>
                    <td id="total-workload" class="text-right">&nbsp;</td>
                </tr>
                <tr>
                    <td class="popup-header-group"><div><?php __('Availability');?> * </div></td>
                    <td>&nbsp;</td>
                    <td id="total-availability" class="text-right">&nbsp;</td>
                </tr>
                <tr>
                    <td class="popup-header-group"><div><?php __('Overload');?> </div></td>
                    <td>&nbsp;</td>
                    <td id="total-overload" class="text-right">&nbsp;</td>
                </tr>
                <tbody class="popup-task-detail">

                </tbody>
            </table>
        </div>
        <div class="table-right">
            <table id="tb-popup-content-2">
                <tbody class="popup-header-2">
                </tbody>
                <tbody class="popup-working-2">
                </tbody>
                <tbody class="popup-vaication-2">
                </tbody>
                <tbody class="popup-capacity-2">
                </tbody>
                <tbody class="popup-workload-2">
                </tbody>
                <tbody class="popup-availa-2">
                </tbody>
                <tbody class="popup-over-2">
                </tbody>
                <tbody class="popup-task-detail-2">
                </tbody>
            </table>
        </div>
    </div>
</div>
<!--END-->
<!-- dialog_skip_value -->
<div id="dialog_skip_value" class="buttons" style="display: none;">
    <fieldset>
        <?php
        echo $this->Form->create('Skip'); ?>
        <div style="height:auto; overflow: hidden" class="wd-scroll-form">
            <div class="wd-input div-skip" id="wd-input-day-skip">
                <label style="width: 162px; display: none" for="url"><?php echo __("Value by day", true) ?></label>
                <?php
                echo $this->Form->input('value_day', array('type' => 'text',
                    'label' => false,
                    'maxlength' => 3,
                    'rel' => 'no-history',
                    'div' => false,
                    'value' => 0,
                    'style' => 'display: none'
                ));
                ?>
            </div>
            <div class="wd-input" id="wd-input-date-skip">
				<div class="title_skipdate" style="font-size: 13px; text-align: center;font-weight: 600;padding-bottom: 15px;">
					<?php echo __("Be carefull, all the tasks will modified !", true) ?>
				</div>
                <div style="clear: both"  style="margin-bottom: 10px;">
                    <label style="width: 152px; color: red; text-align: left; margin-left: 10px" for="url"><?php echo __("Actual start date", true) ?></label>
                    <p id ='skip-start-date' style="width: 120px; margin-left: 140px; padding-top: 6px"></p>
                </div>
                <div style="clear: both">
                    <label style="width: 152px; color: red; text-align: left; margin-left: 10px" for="url"><?php echo __("New start date", true) ?></label>
                    <input type="text" id="new-start-date-skip" class="text" style="width: 30%; height: 20px;" />
                </div>
            </div>
            <div class="wd-input div-skip" id="wd-input-week-skip">
                <label style="width: 152px;" for="url"><?php echo __("Value by week", true) ?></label>
                <?php
                echo $this->Form->input('value_week', array('type' => 'text',
                    'label' => false,
                    'maxlength' => 3,
                    'rel' => 'no-history',
                    'div' => false,
                    'value' => 0,
                    'style' => 'width: 30%; height:20px;'
                ));
                ?>
            </div>
            <div class="wd-input div-skip" id="wd-input-month-skip" style="margin-bottom: 20px;">
                <label style="width: 152px;" for="url"><?php echo __("Value by month", true) ?></label>
                <?php
                echo $this->Form->input('value_month', array('type' => 'text',
                    'label' => false,
                    'maxlength' => 3,
                    'rel' => 'no-history',
                    'div' => false,
                    'value' => 0,
                    'style' => 'width: 30%; height:20px;'
                ));
                ?>
            </div>
            <ul class="type_buttons" style="">
                <li><img src="/img/time-reset-1.png"></li>
                <li><input id="input_time_reset" style="margin-top: 12px;" type="checkbox" name="reset_time" <?php if($projectName['Project']['category'] == 2){ echo 'checked'; }?>></li>
                <li>
					<input id="no-skip" class="cancel_skip" type="button" value='<?php echo __('Cancel', true)?>'/>
				</li>
                <li>
				<input id="ok_skip" type="button" class="yes_skip" value='<?php echo __('OK',true)?>'/>
				</li>
            </ul>
        </div>
        <p id="iz-error" style="display: none; font-size: 12px; color: red; text-align: center"></p>
        <?php
        echo $this->Form->end();
        ?>
    </fieldset>
</div>
<input type="hidden" value="<?php echo Configure::read('Config.language'); ?>" id="language" />
<input type="hidden" value="<?php echo $this->Session->read('Auth.employee_info.Role.name'); ?>" id="pm_acl" />
<input type="hidden" value="<?php echo $projectName['Project']['is_freeze']; ?>" id="show_freeze" />
<input type="hidden" value="<?php echo $settingP['ProjectSetting']['show_freeze']; ?>" id="is_show_freeze" />
<input type="hidden" value="<?php echo $projectName['Project']['off_freeze']; ?>" id="off_freeze" />
<textarea style="display:none" id="priorityJson"><?php echo $listPrioritiesJson; ?></textarea>
<!-- dialog_vision_portfolio -->
<div id="add-comment-dialog" class="buttons" style="display: none;" title="">
    <div class="dialog-request-message">
    </div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"></a></li>
        <li><a href="javascript:void(0)" class="ok"></a></li>
    </ul>
</div>
<div id="gs_loader" style="display: none;">
    <div class="gs_loader">
        <p>Please wait, Skip value...</p>
    </div>
</div>
<!-- 
<div id='modal_dialog_confirm' style="display: none">
    <div class='title'>
		<?php __('Remove');?>
    </div>
    <input class="ok-new-project" type='button' value='<?php echo __('OK', true) ?>' id='btnYes' />
    <input class="cancel-new-project" type='button' value='<?php echo __('Cancel', true) ?>' id='btnNo' />
</div>
-->
<div id='modal_dialog_alert' style="display: none">
    <div class='title'  style="color: orange;font-size: 14px;text-align: center; margin-bottom: 10px"><?php echo __('Select at least a resource or a team', true) ?></div>
    <input class="ok-new-project" type='button' value='<?php echo __('OK', true) ?>' id='btnNoAL' />
</div>
<div id='modal_dialog_alert1' style="display: none">
    <div class='title'  style="color: orange;font-size: 14px;text-align: center; margin-bottom: 10px"><?php echo __('Please enter task name', true) ?></div>
    <input class="ok-new-project" type='button' value='<?php echo __('OK', true) ?>' id='btnNoAL1' />
</div>
<div id='modal_dialog_alert2' style="display: none">
    <div class='title'  style="color: orange;font-size: 14px;text-align: center; margin-bottom: 10px"><?php echo __('Select a start date, end date', true) ?></div>
    <input class="ok-new-project" type='button' value='<?php echo __('OK', true) ?>' id='btnNoAL2' />
</div>
<div id="template_upload" class="template_upload" style="height: auto; width: 320px;">
    <div class="heading">
        <h4><?php echo __('File upload(s)', true)?></h4>
        <span class="close close-popup"><img title="close"  src="<?php echo $html->url('/img/new-icon/close.png'); ?>"/></span>
    </div>
    <div id="content_comment">
        <div class="append-comment"></div>
    </div> 
    <div class="wd-popup hidden">
        <?php 
        echo $this->Form->create('Upload', array(
            'type' => 'POST',
            'url' => array('controller' => 'kanban','action' => 'update_document', $projectName['Project']['id'])));
            ?>
            <div class="trigger-upload"><div id="upload-popup" method="post" action="/kanban/update_document/<?php echo $projectName['Project']['id']; ?>" class="dropzone" value="" >

            </div></div>
            <?php echo $this->Form->input('url', array(
                'class' => 'not_save_history',
                'label' => array(
                    'class' => 'label-has-sub',
                    'text' =>__('URL Link',true),
                    'data-text' => __('(optionnel)', true),
                    ),
                'type' => 'text',
                'id' => 'newDocURL',  
                'placeholder' => __('https://', true)));    
            ?>                    
            <input type="hidden" name="data[Upload][id]" rel="no-history" value="" id="UploadId">
            <input type="hidden" name="data[Upload][controller]" rel="no-history" value="project_tasks_preview">
        <?php echo $this->Form->end(); ?>
    </div>
    <ul class="actions hidden" style="">
        <li><a href="javascript:void(0)" class="cancel"><?php __("Upload Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_attach"><?php __('Upload Validate') ?></a></li>
    </ul>
</div>
<div id="wd-task-comment-dialog" class="wd-comment-dialog" style="height: 440px; width: 320px;display: none;">
	<?php if((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite)){?>
		<div class="add-comment"></div>
    <?php } ?>
    <div class="content_comment" style="min-height: 50px">
		<div class="append-comment"></div>
	</div>
</div>
<div class="light-popup"></div>
<?php echo $this->element('dialog_detail_value') ?>
<?php
$listAvartar = array();
$listIdEm = array_keys($listEmployee);
foreach ($listIdEm as $_id) {
    $link = $this->UserFile->avatar($_id, "small");
    $listAvartar[$_id] = $link;
}

?>

<script type="text/javascript">
    $(document).ready(function(){
        var customScrollBox = $('.customScrollBox').width();
        var container = $('.customScrollBox .gantt-chart-wrapper').width();
        if(container <= customScrollBox){
            $('.scroll-progress ').hide();
        }
		if( !$('#gantt-switch').hasClass('active') ){
			$('#GanttChartDIV').hide();
		}
		gantt_height = 255;
		
		//Remove hidden neu user login co quyen update. QuanNV 08/07/2019
		if( canModify ){
			$('#template_upload').find('.wd-popup').removeClass('hidden');
			$('#template_upload').find('.actions').removeClass('hidden');
			$('.icon-delete').removeClass('hidden');
		}
    });
    
    function onNext() {
        var customScrollBox_container = $(".customScrollBox .container");
        var thePos = customScrollBox_container.position().left;
        var container = $('.customScrollBox .container').width();
        var customScrollBox = $('.customScrollBox').width();
        var left = (customScrollBox - container);
        if( (left - thePos) > -200 ){
            customScrollBox_container.css("left", left);
        }else{
            customScrollBox_container.stop().animate({left: "-=" + 200});
        }
    }
    function onPrevous() {
        var customScrollBox_container = $(".customScrollBox .container");
        var thePos = customScrollBox_container.position().left;
        if(thePos < 0 && thePos < -200) customScrollBox_container.stop().animate({left: "+=" + 200});
        if(thePos > -200) customScrollBox_container.css("left", "0");
    }
    function SubmitDataExport(){
        $('#wd-container-main .wd-layout, #mcs1_container .customScrollBox').css('overflow', 'visible');
        var cleft = parseFloat($('#mcs1_container .container').css('left'));
        $('#mcs1_container .container').css('left', '0px');
        $('#GanttChartDIV').html2canvas({
            afterCanvas: function(){
                $('#wd-container-main .wd-layout, #mcs1_container .customScrollBox').css('overflow', 'hidden');
                $('#mcs1_container .container').css('left', (isNaN(cleft) ? cleft : 0) + 'px');
            }
        });
    }

    var $onClickPhaseIds = <?php echo json_encode($onClickPhaseIds); ?>;
    $.each($onClickPhaseIds, function(index, values){
        $.each(values, function(key, val){
            $('.wd-'+val).css('display', 'none');

        });
    });
    $('#display_all').click(function(){
        $(this).css('display', 'none');
        $('#hide_all').css('display', 'inline-block');
        $.each($onClickPhaseIds, function(index, values){
            $.each(values, function(key, val){
                $('.wd-'+val).css('display', 'inline-block');
            });
        });
        $('.gantt_clears').remove();
        Gantt.rdraw();
    });
    $('#hide_all').click(function(){
        $(this).css('display', 'none');
        $('#display_all').css('display', 'inline-block');
        $.each($onClickPhaseIds, function(index, values){
            $.each(values, function(key, val){
                $('.wd-'+val).css('display', 'none');
            });
        });
        $('.gantt_clears').remove();
        Gantt.rdraw();
    });
    $('.gantt-primary tr').toggle(function(){
        var classPhase = $(this).attr("class") ? $(this).attr("class").split(' ')[1] : '';
        if(classPhase != ''){
            if($onClickPhaseIds[classPhase]){
                $.each($onClickPhaseIds[classPhase], function(index, value){
                    $('.wd-'+value).fadeToggle(1000);
                    if($onClickPhaseIds['wd-'+value]){
                        $.each($onClickPhaseIds['wd-'+value], function(_int, $vl){
                            $('.wd-'+$vl).slideUp();
                            $('#hide_all').css('display', 'none');
                            $('#display_all').css('display', 'block');
                        });
                    }
                });
            }
        }
        $('.gantt_clears').remove();
        Gantt.rdraw();
    }, function(){
        var classPhase = $(this).attr("class") ? $(this).attr("class").split(' ')[1] : '';
        if(classPhase != ''){
            if($onClickPhaseIds[classPhase]){
                $.each($onClickPhaseIds[classPhase], function(index, value){
                    $('.wd-'+value).fadeToggle(200);
                    $('.wd-'+value).hide();
                    if($onClickPhaseIds['wd-'+value]){
                        $.each($onClickPhaseIds['wd-'+value], function(_int, $vl){
                            $('.wd-'+$vl).slideUp();
                            $('#hide_all').css('display', 'none');
                            $('#display_all').css('display', 'block');
                        });
                    }
                });
            }
        }
        $('.gantt_clears').remove();
        Gantt.rdraw();
    });

    var tooltipTemplate = $('#tooltip-template').html();
    // build the tool-tip on mouse over
    $(document).on('mouseenter','div.hover-tooltip' , function(e){
        // on moust enter
        var $el         = $(this);
        var idHove = $el.attr("id") ? $el.attr("id").split(' ')[0] : '';

        var Datas = $('#'+idHove).find('div#hover-data');
        var initDate = Datas.find('.hover-data-comp').html();
        var class_pro = '';
        if(initDate > 50) class_pro = 'late-progress';
        var _html_progress ="<div class='task-progress'><div class='project-progress "+ class_pro +"'><p class='progress-full'>" + draw_line_progress(initDate) + "</p></div></div>";
        var content = (
            tooltipTemplate,
            '<p class="title">'+ Datas.find('.hover-data-name').html()+'</p>'
            +'<p class="start-date"><?php __('Start Date'); ?>&nbsp;: <span style="padding-left: 52px">'+ Datas.find('.hover-data-start').html()+'</span></p>'
            +'<p class="end-date"><?php __('End Date'); ?>&nbsp;&nbsp;: <span style="padding-left: 52px">'+ Datas.find('.hover-data-end').html()+'</span></p>'
            + _html_progress
        );
        $el.tooltip({
            maxWidth : 400,
            maxHeight : 300,
            openEvent : 'xtip-show',
            closeEvent : 'xtip-hide',
            content: content
        }).trigger('xtip-show',e);
    }).on('click mouseleave','div.hover-tooltip' , function(){
        //orthewise destroy the tooltip when mouse leaved
        $(this).tooltip('destroy');
    });
    $('.gantt-line-n').siblings('.gantt-line-desc.gantt-line-s').hide();
    function removeLine(checkboxObject,type){
		var _this = $(checkboxObject);
		var checked = _this.is(':checked');
        if(checked){
            if(type=="n"){
                $('.gantt-line-desc').removeClass('padding-line');
				$('.gantt-line-n').show();
                $('.caseline-n').show();
				// if($('#displayreal').val() == 1)
					$('.gantt-line-n').siblings('.gantt-line-desc.gantt-line-s').hide();
            };
            if(type=="s"){
				$('.gantt-line-s').show();
                $('.caseline-s').show();
				$('.gantt-line-s').siblings('.gantt-line-desc.gantt-line-n').hide();
            };
			
        }else{
            if(type=="n"){
                // if(!$('#displayreal').attr("checked")){
					$('.gantt-line-n').hide();
                    $('.caseline-n').hide();
					$('.gantt-line-n').siblings('.gantt-line-desc.gantt-line-s').show();
                // }
            }
            if(type=="s"){
                // if(!$('#displayplan').attr("checked")){
					$('.gantt-line-s').hide();
                    $('.caseline-s').hide();
					// if($('#displayplan').val() == 1) 
						$('.gantt-line-desc.gantt-line-n').show();
                // }
            }
        }
	}
	if(displayGanttInitial == 0){
		$('.gantt-line-n').hide();
        $('.caseline-n').hide();
	}
    jQuery(document).ready(function($) {
		// initresizable();
        if($("#mcs1_container").length > 0) moveToCurrentDate();
        $(window).trigger('resize');
    });
	function moveToCurrentDate(){
		var today = new Date('<?php echo date('Y-m-d') ?>');
        var startYear = <?php echo date('Y', $start) ?>, endYear = <?php echo date('Y', $end) ?>;
        var type = '<?php echo $type ?>';
        if( endYear - startYear < 2 ){
             $('.x-year').hide();
        }
        switch(type){
            case 'year':
            case 'month':
                var $col = $('#month_' + (today.getMonth() + 1) + '_' + today.getFullYear());
            break;
            case 'week':
                var $col = $('#week_<?php echo date('W') ?>_' + (today.getMonth() + 1) + '_' + today.getFullYear());
            break;
            default:
                var $col = $('#date_' + today.getDate() + '_' + (today.getMonth() + 1) + '_' + today.getFullYear());
            break;
        }
		if( $("#mcs1_container").length){
			$("#mcs1_container").mCustomScrollbar("horizontal",500,"easeOutCirc",0,"fixed","no");
		}
        if( $col.length ){
            var container = $("#mcs1_container .container");
            var dragger_container = $('.dragger_container:visible');
            var max = container.width() - dragger_container.width();
            var ratio = $col.position().left / container.width();
            if( ratio > 1 )ratio = 1;
            var left = 0 - Math.round(ratio * max);
            var scroll = Math.round(ratio * (dragger_container.width() - dragger_container.children(".dragger.ui-draggable").width()));
            $("#mcs1_container .container").css('left', left + 'px');
            dragger_container.children(".dragger.ui-draggable").css('left', scroll + 'px');
        }
	}
	function refreshGanttChart(typeGantt = null){
		if( !showGannt) return;
		if(!typeGantt) typeGantt = <?php echo json_encode($type)?>
		// if( !autoRefreshGantt) return;
		if( $('#GanttChartDIV').is(':empty')) $('#GanttChartDIV').height(255);
		$('#GanttChartDIV').show().addClass('loading-mark loading');
		$.ajax({
			type: "POST",
			// url: '/project_tasks_preview/index/'+ project_id,
			url: '/project_amrs_preview/wd_project_gantt/'+ project_id +'/'+typeGantt,
			data: {},
			success: function(_respon){
				dump = _respon;
				if(_respon){
					$('#GanttChartDIV').empty().html(_respon).removeClass('loading').addClass('loaded');
					$('.gantt-line-s').siblings('.gantt-line-desc.gantt-line-n').hide();
					var gant_line = $('#mcs1_container').find('.gantt-line');
					$.each(gant_line, function(i, e){
						var line_s = $(e).find('.gantt-line-s').length;
						var line_n = $(e).find('.gantt-line-n').length;
						if((line_n == 0 && line_s > 0) || (line_n > 0 && line_s == 0) ){
							$(e).addClass('gantt-one-line');
						}else if(line_n == 0 && line_s == 0){
							$(e).addClass('gantt-no-line');
						}
					});
					if( $('#mcs1_container').length){ 
						$('#mcs1_container').css('height', '');
					}
					$(window).trigger('resize');
					initresizable();
					$('#gantt-initial').trigger('change');
					moveToCurrentDate();
				}
			},
			complete: function(){
				$('#GanttChartDIV').removeClass('loading');
			}
		});
	}
	$('.gantt-switch-view li').on('click', 'a', function(){
		var _this = $(this);
		var date_type = $(this).data('type');
		if(date_type){
			$.ajax({
				url : '/project_tasks_preview/handleFilterGantt',
				type: 'POST',
				data: {
					data: {
						path: 'project_tasks_preview',
						params: date_type,
					}
				},
				success: function(){
					$('.gantt-switch-view .wd-dropdown').find('.selected').empty().html(_this.data('text'));
				}
			});
			refreshGanttChart(date_type);
		 }
		 
	});
	function  z_save_task_callback( newTask, oldTask){
		if( !autoRefreshGantt) return;
		var refresh = 0;
		if( oldTask == undefined || newTask == undefined){
			refresh = 1;
		}
		if( !refresh){
			var keys = ['initial_task_end_date','initial_task_start_date','task_end_date','task_start_date']
			$.each( keys, function( i, k){
				if( newTask.get(k) != oldTask[k]) refresh = 1;
			});
		}
		if( refresh){
			refreshGanttChart();
		}
	}
	function z_delete_task_callback(){
		if( !autoRefreshGantt) return;
		refreshGanttChart();		
	}
</script>
<script type="text/javascript">
    var isFull = false;
    var stack =  [],height = 16,icon = 16;
	var flag_new_task_id = '';
    var listEmployee = <?php echo json_encode($listEmployee) ?>;
    var listAvartar = <?php echo json_encode($listAvartar) ?>;
    var listAssign = <?php echo json_encode($listAssign)?>;
	var listAssign_updated = <?php echo json_encode(!empty( $priority['Employees']) ? $priority['Employees'] : $listAssign)?>;
    var projectMilestones = <?php echo json_encode($projectMilestones)?>;
    var budget_settings = <?php echo json_encode(isset($budget_settings) ? $budget_settings : '&euro;') ?>;
    var employee_id = <?php echo json_encode($employee_id) ?>;
    var myRole = <?php echo json_encode($myRole) ?>;
    var $isEmployeeManager = <?php echo json_encode($isEmployeeManager) ?>;
	var listTaskName = <?php echo json_encode($listTaskName) ?>;
	var showGantt = <?php echo json_encode($showGannt ? 1 : 0) ?>;
	var treepanel = {};
    // Milestones
    //END
    $('#update-status').hide();
    $("#btnClose").click(function(){
        $("#showdetail").hide();
    });
    $('.close, .cancel').on( 'click', function (e) {
        // e.preventDefault();
        $("#template_upload").removeClass('show');
        $("#comment_popup").removeClass('show');
        $(".light-popup").removeClass('show');
    });
    $('.close-ntc').on( 'click', function (e) {
        $("#area-append-ntc").removeClass('open');
    });
    var wdTable = $('.wd-table');
    var heightTable = $(window).height() - wdTable.offset().top - 40;
    heightTable = (heightTable < 300) ? 300 : heightTable;
    wdTable.css({
        height: heightTable,
    });
	
    var wdTable = $('.wd-table');
	var xdelay;
	function update_table_height(){
		clearTimeout(xdelay);
		
		treepanel = Ext.getCmp('pmstreepanel',{
			columns : [{flex: 1}] 
		});
		xdelay = setTimeout(function(){
			if( treepanel ){
				var mql = window.matchMedia('printer');
				var z_printing = treepanel.isZPrinting||false;
				if( mql.matches || z_printing){
					if( treepanel) { treepanel.setFullHeight();}
				}else{
					//Email RE: [Z0G] Update Prod6 - Đau đầu quá!
					var heightTable = $(window).height() - wdTable.offset().top - 80;
					heightTable = (heightTable < 300) ? 300 : heightTable;
					heightTable_max = heightTable + 40;
					if( isFull ){ 
						heightTable = heightTable_max;
					}else{
						// Z0G 28/4/2020: Issue - Thay đổi cách lấy height
						var data_length = treepanel.getStore().getData().length;
						var max_height_panel = data_length*40 + 40; // Add header 40px;
						if( max_height_panel > heightTable ) {
							heightTable = heightTable_max;
						}
					}
					wdTable.height(heightTable);
					treepanel.setWidth(wdTable.width());
					treepanel.setHeight(heightTable);
					treepanel.updateLayout();
				}
			}else{
				var heightTable = $(window).height() - wdTable.offset().top - 80;
				heightTable = (heightTable < 300) ? 300 : heightTable;
				wdTable.height( heightTable);
			}
		}, 200);
	}
	function setScrollBar(){
		var container_tree_view = $('#pmstreepanel-body').find('.x-grid-item-container');
		// if(container_tree_view.length > 0){
			var tree_w = container_tree_view.width();
			var tree_h = container_tree_view.height();
			$('.x-tree-view').css({"width": tree_w, "height": tree_h, "overflow": "hidden"});
			$('#pmstreepanel-body').css({"overflow": "auto"});
		// }
	}
    $(window).resize(function(){
        update_table_height();
		// setTimeout(function(){
			// setScrollBar();
		// }, 250);
    });
    function onViewRefresh(view) {
        Ext.each(view.panel.columns, function(column) {
            if (column.autoSizeColumn === true) {
                column.autoSize();
            } else if (column.fixedWidth) {
                column.setMinWidth(column.fixedWidth);
                column.setMaxWidth(column.fixedWidth);
            }
        });
    }
    //clearInterval(flag);
    var flag = setInterval(function(){
        if($('#pmstreepanel-body').find('div table').height()>0){
            var check = parseFloat($('.internal-line .display-var span.display-value').text());
            if(check>0){
                $('.internal-line .display-var span.display-value').removeClass('var-green');
                $('.internal-line .display-var span.display-value').addClass('var-red');
            }else{
                $('.internal-line .display-var span.display-value').removeClass('var-red');
                $('.internal-line .display-var span.display-value').addClass('var-green');
            }
            var checkEx = parseFloat($('.external-line .display-var span.display-value').text());
            if(checkEx>0){
                $('.external-line .display-var span.display-value').removeClass('var-green');
                $('.external-line .display-var span.display-value').addClass('var-red');
            }else{
                $('.external-line .display-var span.display-value').removeClass('var-red');
                $('.external-line .display-var span.display-value').addClass('var-green');
            }
            var treepanel = Ext.getCmp('pmstreepanel');
            treepanel.setHeight(Ext.getBody().getViewSize().height);
            // treepanel.onAfterLoad(function(){
            //     $('#loading').remove();
            // });
            var fr = treepanel.getSetting('show_type');
            $('.type-' + fr).addClass('type-focus');
            $('.filter-type').unbind('click');
            $('.filter-type').on('click', function(){
                var which = $(this).data('type');
                if( $(this).hasClass('type-focus') ){
                    $('.filter-type').removeClass('type-focus');
                    which = '';
                } else {
                    $('.filter-type').removeClass('type-focus');
                    $(this).addClass('type-focus');
                }
                treepanel.saveSetting('show_type', which);
                treepanel.addTypeFilter(which, true);
                cleanFilter();
            });
            clearInterval(flag);
        }
    }, 1000);
    $('#dialog_import_CSV').dialog({
        position    :'center',
        autoOpen    : false,
        autoHeight  : true,
        modal       : true,
        width       : 360,
        height      : 150
    });
    $('#dialog_import_MICRO').dialog({
        position    :'center',
        autoOpen    : false,
        autoHeight  : true,
        modal       : true,
        width       : 360,
        height      : 150
    });
    $('#dialog_data_CSV').dialog({
        position    :'top',
        autoOpen    : false,
        autoHeight  : true,
        modal       : true,
        minHeight   : 102,
        width       : 760
        //auto  : true
        // height      : 230
    });
    $("#import_CSV").click(function(){
        $('.wd-input').show();
        $('#loading').hide();
        $("input[name='FileField[csv_file_attachment]']").val("");
        $(".error-message").remove();
        $("input[name='FileField[csv_file_attachment]']").removeClass("form-error");
        $(".type_buttons").show();
        $('#dialog_import_CSV').dialog("open");
    });
    $("#import_MICRO").click(function(){
        $('.wd-input').show();
        $('#loading').hide();
        $("input[name='FileField[micro_file_attachment]']").val("");
        $(".error-message").remove();
        $("input[name='FileField[micro_file_attachment]']").removeClass("form-error");
        $(".type_buttons").show();
        $('#dialog_import_MICRO').dialog("open");
    });
	var ext_tasks = [];
	var ext_index = 0;
	function secusiveTask(node){
		if(node.length > 0){
			$.each(node, function(i, it){
				ext_tasks[ext_index++] = it.data;
				if(it.childNodes.length > 0){
					secusiveTask(it.childNodes);
				}
			});
		}else{
			ext_tasks[ext_index++] = node.data;
		}
	}
	
    $(".btn-touch-move").click(function(){
		var html = <?php echo json_encode( $this->element('dialog_task_touch')) ?>;
		jQuery('#dialogDetailValue').css({
			'padding-top': 0,
			'padding-bottom': 0,
		});
		var wh = jQuery(window).height();
		var wW = jQuery(window).width();
		var ppW = wW <= 768 ? wW - 30 : 960;
		jQuery('#contentDialog').css({'height': 600, 'width': ppW});
		showMe();
		$(".light-popup").addClass('show');
		jQuery('#contentDialog').empty().html(html);
		
    });
    $("#ok-importcsv").click(function(){
        $(".error-message").remove();
        $("input[name='FileField[csv_file_attachment]']").removeClass("form-error");
        if($("input[name='FileField[csv_file_attachment]']").val()) {
            var filename = $("input[name='FileField[csv_file_attachment]']").val();
            var valid_extensions = /(\.csv)$/i;
            if(valid_extensions.test(filename)) {
                $('#uploadForm').submit();
            } else {
                $("input[name='FileField[csv_file_attachment]']").addClass("form-error");
                jQuery('<div>', {
                    'class': 'error-message',
                    text: 'Incorrect type file'
                }).appendTo('#error');
            }
            $("#dialog_import_CSV").dialog("close");
        } else {
            jQuery('<div>', {
                'class': 'error-message',
                text: 'Please choose a file!'
            }).appendTo('#error');
        }
    });
    $("#import-micro-submit").click(function(){
        $(".error-message").remove();
        $("input[name='FileField[micro_file_attachment]']").removeClass("form-error");
        if($("input[name='FileField[micro_file_attachment]']").val()) {
            var filename = $("input[name='FileField[micro_file_attachment]']").val();
            var valid_extensions = /(\.xml)$/i;
            if(valid_extensions.test(filename)) {
                $('#uploadFormMicro').submit();
            } else {
                $("input[name='FileField[micro_file_attachment]']").addClass("form-error");
                jQuery('<div>', {
                    'class': 'error-message',
                    text: 'Incorrect type file'
                }).appendTo('#error-micro');
            }
            $("#dialog_import_MICRO").dialog("close");
        } else {
            jQuery('<div>', {
                'class': 'error-message',
                text: 'Please choose a file!'
            }).appendTo('#error-micro');
        }
    });
    /* table .end */
    var createDialog = function(){
        $('#dialog_skip_value').dialog({
            position    :'center',
            autoOpen    : false,
            autoHeight  : true,
            modal       : true,
            width       : 400,
            open : function(e){
                var $dialog = $(e.target);
                $dialog.dialog({open: $.noop});
            }
        });
        createDialog = $.noop;
    }
    $("#skip_value").live('click',function(){
        var _id = $('.x-grid-item-selected').find('span:first').attr('id');
        if(_id !== undefined){
            _id = _id.replace('x-task-', '');
            $.ajax({
                url: '/project_tasks/getClassifyTask/' + _id,
                dataType: 'json',
                data: {
                    type : 'task'
                },
                type: 'POST',
                success: function(result) {
                     value_day = (result.start_date).split("-");
                    $('#skip-start-date').empty().append(value_day[2] +'-'+ value_day[1] +'-'+ value_day[0]);

                    if(result.day <= 0){
                        $('#wd-input-day-skip').css('display', 'none');
                        $('#wd-input-date-skip').css('display', 'none');
                    } else {
                        $('#wd-input-day-skip').css('display', '');
                        $('#wd-input-date-skip').css('display', '');
                    }
                    if(result.week <= 0){
                        $('#wd-input-week-skip').css('display', 'none');
                    } else {
                        $('#wd-input-week-skip').css('display', '');
                    }
                    if(result.month <= 0){
                        $('#wd-input-month-skip').css('display', 'none');
                    } else {
                        $('#wd-input-month-skip').css('display', '');
                    }
                    createDialog();
                    $("#dialog_skip_value").dialog('option',{title:''}).dialog('open');
                }
           });
        } else {
            $.ajax({
                url: '/project_tasks/getClassifyTask/' + <?php echo json_encode($project_id) ?>,
                dataType: 'json',
                data: {
                    type : 'project'
                },
                type: 'POST',
                success: function(result) {
                    value_day = (result.start_date).split("-");
                    $('#skip-start-date').empty().append(value_day[2] +'-'+ value_day[1] +'-'+ value_day[0]);
                    if(result.day <= 0){
                        $('#wd-input-day-skip').css('display', 'none');
                        $('#wd-input-date-skip').css('display', 'none');
                    } else {
                        $('#wd-input-day-skip').css('display', '');
                        $('#wd-input-date-skip').css('display', '');
                    }
                    if(result.week <= 0){
                        $('#wd-input-week-skip').css('display', 'none');
                    } else {
                        $('#wd-input-week-skip').css('display', '');
                    }
                    if(result.month <= 0){
                        $('#wd-input-month-skip').css('display', 'none');
                    } else {
                        $('#wd-input-month-skip').css('display', '');
                    }
                    createDialog();
                    $("#dialog_skip_value").dialog('option',{title:''}).dialog('open');
                }
           });
        }
    });
    $(".cancel").live('click',function(){
        $("#dialog_data_CSV").dialog("close");
        $("#dialog_import_MICRO").dialog("close");
    });
    // Chi cho phep nhap so
    $("#SkipValueDay, #SkipValueWeek, #SkipValueMonth").keypress(function (e) {
        if (e.which != 45 && e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            $("#iz-error").html("Only input number.").show().fadeOut("slow");
            return false;
        }
    });
    $('#new-start-date-skip').datepicker({
        dateFormat: 'dd-mm-yy',
        beforeShowDay: function(d){
            var date = d.getDay();
            return [Workdays[date] == 1, '', ''];
        },
        onSelect: function(dateText, inst){
            var _d = $('#new-start-date-skip').val();
            var _start_date = <?php echo json_encode($projectName['Project']['start_date']) ?>;
            _d = _d.split('-');
            var d1 = new Date(_d[2] + '-' + _d[1] + '-' + _d[0]);
            var d2 = new Date(_start_date);
            if(d1 <= d2){
                var calculateDate = 0;
                d1 = d1.getTime();
                d2 = d2.getTime();
                var _d1 = d2;
                while(_d1 > d1){
                    var d1Date = new Date(_d1);
                    d1Date = d1Date.getDay();
                    if(Workdays[d1Date] == 1) calculateDate--;
                    _d1 = _d1 - (1000*60*60*24);
                }
                $('#SkipValueDay').val(calculateDate);
            } else {
                var calculateDate = 0;
                d1 = d1.getTime();
                d2 = d2.getTime();
                var _d1 = d1;
                while(_d1 > d2){
                    var d1Date = new Date(_d1);
                    d1Date = d1Date.getDay();
                    if(Workdays[d1Date] == 1) calculateDate++;
                    _d1 = _d1 - (1000*60*60*24);
                }
                $('#SkipValueDay').val(calculateDate);
            }
        }
    });
    $("#ok_skip").click(function(){
        // tinh valDay
        var _d = $('#new-start-date-skip').val();
        var get_date = $('#skip-start-date').html();
        get_date = get_date.split('-');
        var get_date = new Date(get_date[2] + '-' + get_date[1] + '-' + get_date[0]);
       // var _start_date = <?php echo json_encode($projectName['Project']['start_date']) ?>;
        var _start_date = get_date;
        _d = _d.split('-');
        var d1 = new Date(_d[2] + '-' + _d[1] + '-' + _d[0]);
        var d2 = new Date(_start_date);
        var calculateDate = 0;
        if(d1 <= d2){
            d1 = d1.getTime();
            d2 = d2.getTime();
            var _d1 = d2;
            while(_d1 > d1){
                var d1Date = new Date(_d1);
                d1Date = d1Date.getDay();
                if(Workdays[d1Date] == 1) calculateDate--;
                _d1 = _d1 - (1000*60*60*24);
            }
            $('#SkipValueDay').val(calculateDate);
        } else {
            d1 = d1.getTime();
            d2 = d2.getTime();
            var _d1 = d1;
            while(_d1 > d2){
                var d1Date = new Date(_d1);
                d1Date = d1Date.getDay();
                if(Workdays[d1Date] == 1) calculateDate++;
                _d1 = _d1 - (1000*60*60*24);
            }
            $('#SkipValueDay').val(calculateDate);
        }
        var valDay = calculateDate;
        var valWeek = $('#SkipValueWeek').val(),
            valMonth = $('#SkipValueMonth').val();
        var checked = $('#input_time_reset').attr('checked');
        if(checked !== undefined && checked == 'checked'){
            checked = 1;
        } else {
            checked = 0;
        }
        if(valDay == '' && !valDay && valWeek == 0 && valMonth == 0){
            $('#iz-error').html('The value day is not blank!');
            $('#iz-error').show();
        } else {
            var modelId = <?php echo json_encode($projectName['Project']['id']);?>;
            $.ajax({
                url: '/project_tasks/skipValue/project/' + modelId + '/' + valDay + '/' + valWeek + '/' + valMonth,
                //async: false,
                type: 'POST',
                data: {
                    checked: checked
                },
                beforeSend: function(){
                    $("#dialog_skip_value").dialog('close');
                    $('#gs_loader').css('display', 'block');
                },
                success: function() {
                    _runStaffing(function(){
                        window.location.reload(0);
                    });
                }
           });
        }
    });
	$("#no-skip").click(function(){
		$("#dialog_skip_value").dialog('close');
	});
	$("#no-importcsv").click(function(){
		$("#dialog_import_CSV").dialog('close');
	});
    function _runStaffing(success){
        $.ajax({
            url: '/project_tasks/staffingWhenUpdateTask/<?php echo $projectName['Project']['id'] ?>',
            success: success
        });
    }
</script>
<?php echo $this->Html->script(array(
    'extjs/ext5-include',
    'extjs/app/app',
    'jquery-ui.multidatespicker',
    // 'preview/jquery.multiSelect_preview',
    'jquery.scrollTo'
)); ?>
<?php echo $this->Html->css(array(
    'extjs/resources/css/ext-custom',
    'jquery.multiSelect',
	'preview/datepicker-new',
	// 'layout_2019',
)); ?>
<!-- export excel  -->
<script type="text/javascript">
	// init_multiselect('#addProjectTemplate .wd-multiselect, #special-task-info-2 .wd-multiselect');
	var budgetCurrency = <?php echo json_encode($bg_currency)?>;
	var autoRefreshGantt = <?php echo json_encode($autoRefreshGantt)?>;
	var showGannt = <?php echo json_encode($showGannt)?>;
    $('#ProjectOffFreeze').click (function(){
      var thisCheck = $(this);
	  if( thisCheck.is(':disabled')) return;
	  thisCheck.prop('disabled', true);
	  thisCheck.addClass('loading');
      if(thisCheck.is(':checked')) {
        $.post("<?php echo $this->Html->url('/project_tasks/update_initial/'.$project_id.'/1');?>", function(data){
            location.reload();
        });
      }else{
        $.post("<?php echo $this->Html->url('/project_tasks/update_initial/'.$project_id.'/0');?>", function(data){
            location.reload();
        });
      }
    });
    $('#absence-table-fixed').html('Freeze');
    var openDialog = function(title,callback){
        var $dialog = $('#add-comment-dialog').attr('title' , title);
        $dialog.dialog({
            zIndex : 10000,
            modal : true,
            minHeight : 50,
            close : function(){
                $dialog.dialog('destroy');
            }
        });
        $dialog.find('a.ok').unbind().click(function(){
            if(!$.isFunction(callback)) {
                $dialog.dialog('close');
            } else {
                callback.call(this);
            }
            return false;
        });
        $dialog.find('a.cancel').unbind().click(function(){
            $dialog.dialog('close');
            return false;
        }).toggle($.isFunction(callback));
    };
    var freeze =  $('ul#displayFreeze li.unfreeze-freeze').width();
    var widthF = parseFloat(freeze)+35;
    if ($('ul#displayFreeze li').length > 1) {
        widthF = parseFloat(freeze) + 75;
    }
    $('ul#displayFreeze').css('width',widthF);

    //EXPAND TREE
    $(document).keyup(function(e) {
        if (window.event) {
            var value = window.event.keyCode;
        } else
            var value=e.which;
        if (value == 27) { collapseTaskScreen(); }
    });
    function collapseTaskScreen() {
        $('#table-collapse').hide();
        $('#expand').show();
        $('.wd-panel').removeClass('treeExpand');
        $('#layout').removeClass('wd-ontop');
        $('.body').removeClass('is_treeExpand');
        if(gantt_height) $('#mcs1_container').height(gantt_height);
        isFull = false;
        initresizable();
        $(window).trigger('resize');
    }
    function expandTaskScreen() {
        $('.wd-panel').addClass('treeExpand');
        $('#layout').addClass('wd-ontop');
        $('.body').addClass('is_treeExpand');
        $('#table-collapse').show();
        $('#expand').hide();
        isFull = true;
		$('#mcs1_container').css('height', '');
        destroyresizable();
        $(window).trigger('resize');
    }
	if($('#gantt-switch').hasClass('active')){
		$('#gantt-initial-switch').show();
		$('#autoRefreshGantt').parent().show();
	}
	function toggleGantt(elem){
		var _this = $(elem);
		 
		_this.toggleClass('active');
		reheight = 255;
		if( _this.hasClass('active')){
			// bật
			showGannt = 1;
			$('#gantt-initial-switch').show();
			$('#autoRefreshGantt').parent().show();
			showGantt = 1;
			refreshGanttChart();
			$('.gantt-switch-view').fadeIn();
			_this.find('.bt-switch-text').empty().html(i18n_text['hide_gant']);
		}else{
			//tắt
			showGannt = 0;
			$('#gantt-initial-switch').hide();
			$('#autoRefreshGantt').parent().hide();
			showGantt = 0;
			$('#GanttChartDIV').slideToggle(300, function(){$(window).trigger('resize')});
			$('.gantt-switch-view').fadeToggle();
			_this.find('.bt-switch-text').empty().html(i18n_text['show_gant']);
		}
		$('#mcs1_container').height(reheight);
		$('.gantt-line-s').siblings('.gantt-line-desc.gantt-line-n').hide();
		HistoryFilter.stask = {'showGantt': ( _this.hasClass('active') ? 'true' : 'false')};
		HistoryFilter.send();
		// isFull = false;
		moveToCurrentDate();
        $(window).trigger('resize');
	}
    function filterEmployee(text,e){
        $('li[rel="li-employee"]').each(function(index, element) {
            str=$(this).html();
            str=str.toLowerCase();
            text=text.toLowerCase();
            elm=$(this).attr('id');
            if(str.indexOf(text)==-1) {
                $(this).hide();
                $('div[rel='+elm+']').hide();
            } else {
                $(this).show();
                $('div[rel='+elm+']').show();
            }
        });
    }
    $('#reset-date').click(function(){
        if( !confirm('<?php __("Reset the start and end date of all tasks to match with their phases?") ?>') )
            return false;
    });
    var startDate, endDate;
    var listDeletion = [];
    var Task;
    var Holidays = {};  //get by ajax
    var Workdays = <?php echo json_encode($workdays) ?>;
    var monthName = <?php
        echo json_encode(array(
            __('January', true),
            __('February', true),
            __('March', true),
            __('April', true),
            __('May', true),
            __('June', true),
            __('July', true),
            __('August', true),
            __('September', true),
            __('October', true),
            __('November', true),
            __('December', true)
        )); ?>;
    Date.prototype.format = function(format){
        if( !format )format = 'dd-mm-yy';
        return $.datepicker.formatDate(format, this);
    }
    function SpecialTask(options){
        this.defaults = {
            data: {},
            columns: [],
            employeeActive: [],
            id: 0,
            task: {
                id: 0,
                task_title: '',
                task_priority_id: '',
                task_status_id: '',
                task_start_date: '',
                task_end_date: '',
                project_planed_phase_id: 0,
                unit_price: 0,
                profile_id : '',
                manual_consumed: 0
            },
            consume: {}
        };
        this.options = $.extend({}, this.defaults, options);
        this.init();
        return this;
    }
    SpecialTask.prototype.init = function(){
        listDeletion = [];
        var me = this;
        //init data
        $('#nct-id').val(me.options.id);
        $('#nct-phase-id').val(me.options.task.project_planed_phase_id);
        $('#nct-priority').val( me.options.task.task_priority_id ).trigger('change');
        $('#nct-milestone').val( me.options.task.milestone_id ).trigger('change');
        $('#nct-status').val( me.options.task.task_status_id ).trigger('change');
        $('#nct-start-date').val(me.options.task.task_start_date).datepicker( "setDate", me.options.task.task_start_date).trigger('change');
        $('#nct-end-date').val(me.options.task.task_end_date).datepicker( "setDate", me.options.task.task_end_date ).trigger('change');
        $('.hasDatepicker').define_limit_date('#nct-start-date', '#nct-end-date');
        $('#nct-manual').val(me.options.task.manual_consumed).trigger('change');
        $('#nct-unit-price').val(me.options.task.unit_price).trigger('change');
        $('#nct-profile').val(me.options.task.profile_id).trigger('change');
        $('#nct-name').val(me.options.task.task_title).trigger('change');
        this.columns = this.options.columns;
        if( typeof this.options.data == 'function' ){
            this.data = this.options.data();
        } else {
            this.data = this.options.data;
        }
        var columns = this.columns;
        var data = this.data;
        var request = this.options.request;

        //build html
        var estimate = {}, consume, inUsed;
        try{
            consume = parseFloat(this.options.request.all[0]);
            inUsed = parseFloat(this.options.request.all[1]);
        } catch(ex){
            consume = 0;
            inUsed = 0;
        } finally {
            if( isNaN(consume) )consume = 0;
            if( isNaN(inUsed) )inUsed = 0;
        }
		me.loadFullEmployee();
		// set assign 
		me.setAssign();
		// update Active Employee
		me.updateActiveEmployee();
        //build data
        var type = 2;
        $.each(data, function(row, items){
            //build row
            var date = row.substr(2);
            var html = '<tr><td id="date-' + date + '" class="nct-date">' + toRowName(row) + '</td>';
            var c = parseFloat(request[row][0]), iu = parseFloat(request[row][1]);
            if( isNaN(c) )c = 0;
            if( isNaN(iu) )iu = 0;
            $.each(items, function(i, item){
                var id = item.reference_id + '-' + item.is_profit_center;
                html += '<td class="value-cell ntc-employee-col cell-'+id+'"><input type="text" id="val-' + date + '-' + id + '" data-old="' + item.estimated + '" class="workload workload-'+id+'" data-id="' + id + '" data-ref value="' + item.estimated + '" style="" onchange="changeTotal(this)"></td>';
                //calculate estimated
				if( ! estimate[id])  estimate[id] = 0;
                estimate[id] += item.estimated;
                type = item.type;
            });
            //last col
            html += '<td style="" class="ciu-cell">' + c.toFixed(2) + ' (' + iu.toFixed(2) + ')</td>';
            html += '<td class="remove-cell">';
			 if(!( c > 0 || iu > 0 )){
				html += '<span class="cancel" onclick="removeRow(this)" href="javascript:;"><img src="/img/new-icon/close.png" /><img src="/img/new-icon/close-blue.png" /></span>';
			 }
			html += '</td></tr>';
            $('#assign-table tbody').append(html);
           
                // $('#date-' + date).find('.cancel').remove();
            // }
            //consume += c;
            //inUsed += iu;
        });
        //fill data for footer
        $.each(columns, function(i, col){
            $('#foot-' + col.id).text(estimate[col.id].toFixed(2));
        });
        $('#total-consumed').text(consume.toFixed(2) + ' (' + inUsed.toFixed(2) + ')');
        //disable name if task have consumed/in used
        if( consume > 0 || inUsed > 0 ){
            $('#nct-name').prop('disabled', false);
            //disable neu co consume/in used
            $('#nct-range-type').prop('disabled', true);
        } else {
            $('#nct-range-type').prop('disabled', false);
        }
        if( consume > 0 || inUsed > 0 || $('#nct-range-type').val() == '0' ){
            $('#create-range').hide();
        } else {
            $('#create-range').show();
        }
        if(show_unit){
            $('#wd-unit-price').css('display', 'block');
        }
        if(show_milestone){
            $('#wd-milestone').css('display', 'block');
        }
        if(show_priority){
            $('#wd-priority').css('display', 'block');
        }
        if(show_profile){
            $('#wd-profile').css('display', 'block');
        }
        //chon range tai day:
        $('#nct-range-type option[value="' + type +'"]').prop('selected', true);
        selectRange();
        //calculate
        calculateTotal();
        //refresh picker
        refreshPicker();
        // bind keys
        bindNctKeys();
		// update popup width
		set_width_nct();
        //done!
    }
    SpecialTask.prototype.loadFullEmployee = function(){
		ajaxGetResources(project_id);
	}
    SpecialTask.prototype.setAssign = function(){
		init_multiselect('#special-task-info-2 .wd-multiselect');
		multiselect_setval($('#special-task-info-2 .wd-multiselect'), {});
		var me = this;
		var $elm = $('#nct_list_assigned');
		var datas = me.options.columns;
        $.each(datas, function (ind, data) {
			$elm.find('input[value="' + data.id + '"]').closest('.wd-data').trigger('click');
        });
	}
    SpecialTask.prototype.updateActiveAssignedEmployee = function(){
		init_multiselect('#special-task-info-2 .wd-multiselect');
		var me = this;
		var $elm = $('#nct_list_assigned');
		var employees = me.options.employeeActive;
        $.each(employees, function (ind, employee) {
			employee = employee.Employee;
			var id = 'check-' + employee.id + '-' + employee.is_profit_center;
			var is_active = employee.actif;
			var _input = $elm.find('#' + id);
			if( employee.is_selected == 1) _input.closest('.wd-data').trigger('click');
			_input.closest('.wd-data-manager').addClass('actif-' + is_active);
        });
	}
    SpecialTask.prototype.updateActiveEmployee = function(){
		var me = this;
		var $elm = $('#nct_list_assigned');
		var employees = me.options.employeeActive;
        $.each(employees, function (ind, employee) {
			employee = employee.Employee;
			var id = 'check-' + employee.id + '-' + employee.is_profit_center;
			var is_active = employee.actif;
			var _input = $elm.find('#' + id);
			_input.closest('.wd-data-manager').addClass('actif-' + is_active);
        });
	}
    SpecialTask.prototype.destroy = function(){
        listDeletion = [];
        this.options = {
            data: {},
            columns: [],
            id: 0,
            task: {
                id: 0,
                task_title: '',
                task_priority_id: '',
                task_status_id: '',
                task_start_date: '',
                task_end_date: '',
                project_planed_phase_id: 0,
                unit_price: 0,
                profile_id : '',
                manual_consumed: 0
            },
            consume: {}
        };
        this.data = {};
        this.columns = [];
        //reset input
        $('#special-task-info-2 .text').val('').prop('disabled', false);
        //reset selection
        $('#special-task-info-2 select option').show().filter('[value=""]').prop('selected', true);
        //reset table
        $('#assign-table tbody').html('');
        $('#assign-table td.value-cell').remove();
        $('#total-consumed').html('0');
		
		// 2
		var _mulsel = $('#nct_list_assigned');
		_mulsel.removeClass('has-val');
        var area_append = $(this).find('.wd-combobox');
		area_append.find('a.circle-name').remove();
		_mulsel.find('.wd-combobox-content :checkbox').prop('checked', false);
		var _function = _mulsel.prop('id') + 'onChange';
		if( typeof window[_function] == 'function'){
			window[_function](_mulsel.prop('id'));
		}	
		
        $('#nct-range-type option[value="1"]').prop('selected', true);
		$('#save-special,#cancel-special').removeClass('disabled');
        //$('#add-date').addClass('disabled');
    }
    SpecialTask.prototype.commit = function(){
        var _form = $('#special-task-info-2');
		//check data
        var name = $.trim($('#nct-name').val()),
            sd = $('#nct-start-date').datepicker('getDate'),
            ed = $('#nct-end-date').datepicker('getDate');
			
        $.each( $('#special-task-info-2').find(':required'), function(ind, _input){
			if( $(_input).val() == ''){
				_form.find(':submit').click();
				return false;
			}
		});
		/*
        if( !name ){
            var dialog = $('#modal_dialog_alert1').dialog();
            $('#btnNoAL1').click(function() {
                dialog.dialog('close');
            });
            // alert('<?php __('Error: Please enter task name') ?>');
            return false;
        }
        if( !sd || !ed ){
            var dialog = $('#modal_dialog_alert2').dialog();
            $('#btnNoAL2').click(function() {
                dialog.dialog('close');
            });
            // alert('<?php __('Error: Please pick start date / end date') ?>');
            return false;
        }
		*/
        if( sd > ed ){
            alert('<?php __('Error: start date can not be greater than end date') ?>');
            $('#nct-start-date').focus();
            return false;
        }//check date range
        if( !isValidList() ){
            alert('<?php __('Error: There are dates not between start date and end date') ?>');
            return false;
        }
        var result = {
            data: {
                workloads: {},
                id: <?php echo $projectName['Project']['id'] ?>,
                d: listDeletion,
                type: $('#nct-range-type').val(),
                task: {
                    id: $('#nct-id').val(),
                    task_title: name, //$('#nct-name').prop('disabled') ? '' : name,
                    task_priority_id: $('#nct-priority').val(),
                    task_status_id: $('#nct-status').val(),
                    task_start_date: $('#nct-start-date').val(),
                    task_end_date: $('#nct-end-date').val(),
                    project_planed_phase_id: $('#nct-phase-id').val(),
                    profile_id : $('#nct-profile').val(),
                    manual_consumed: $('#nct-manual').val(),
                    unit_price: $('#nct-unit-price').val(),
                    milestone_id: $('#nct-milestone').val()
                }
            }
        };

        $count_value_cell_hidden = $('#assign-table tbody tr .value-cell').filter(function() {
            return $(this).css('display') == 'none';
        }).length;
        if( !$('#assign-table tbody tr .value-cell input').length ){
            var dialog = $('#modal_dialog_alert').dialog();
            $('#btnNoAL').click(function() {
                dialog.dialog('close');
            });
            // alert('<?php __('Select at least a resource or a team') ?>');
            return false;
        } else if( $count_value_cell_hidden == $('#assign-table tbody tr .value-cell input').length ) {
            var dialog = $('#modal_dialog_alert').dialog();
            $('#btnNoAL').click(function() {
                dialog.dialog('close');
            });
            // alert('<?php __('Select at least a resource or a team') ?>');
            return false;
        }

        $('#assign-table tbody tr').each(function(){
            var tr = $(this),
                date = tr.find('.nct-date').prop('id').replace('date-', '');
            result.data.workloads[date] = {};
            var inputs = tr.find('.value-cell input:visible');
            inputs.each(function(){
                var me = $(this),
                    id = me.data('id'), t = id.split('-');
                result.data.workloads[date][id] = {
                    reference_id: t[0],
                    estimated: parseFloat(me.val()).toFixed(2),
                    is_profit_center: t[1]
                };
            });
        });
        //console.log(result.data.workloads);return;
        //save
        //disable button first
        var btns = $('#save-special,#cancel-special').addClass('disabled');
        var text = $('#nct-progress').show();
        var tree = $('#special-task-info-2').data('tree');
        $.ajax({
            url: '<?php echo $this->Html->url('/') ?>project_tasks/saveNcTask',
            type: 'POST',
            dataType: 'json',
			data:  { data: JSON.stringify(result)},
			beforeSend: function(){
				$('#special-task-info-2').find('.loading-mark').addClass('loading');
			},
            success: function(response){
                cancel_popup('#special-task-info-2', false);
                tree.setLoading(i18n('Please wait'));
                /*
                * add task
                */
                if( response.result ){
                    if( result.data.task.id == 0 ){
                        var
                            cellEditingPlugin   = tree.cellEditingPlugin, // for double click
                            selectionModel      = tree.getSelectionModel(),
                            selectedTask        = selectionModel.getSelection()[0];
                        var task = $.extend({}, {
                            task_title  : '',
                            loaded      : true,
                            leaf        : false,
                            expanded    : true,
                            children    : [],
                            parent_id   : 0,
                            parent_name : '',
                            project_id  : tree.project_id,
                            is_nct      : 1
                        }, response.data);
						var phases = list_phase(tree);
						var _phase_id = task['project_planed_phase_id'];
						if( _phase_id in phases){
							parentTask = phases[_phase_id];
						}else{
							tree.refreshSummary(function(callback){
							   tree.setLoading(false);
							});
							tree.refreshStaffing(function(callback){
								//do nothing
							});
							return;
						}
                        var newTask = Ext.create('PMS.model.ProjectTaskPreview', task);
                        parentTask.set('leaf', false);
                        parentTask.appendChild(newTask);
                        if(!parentTask.data.children||parentTask.data.children=='null')
                            parentTask.data.children=[];
                        parentTask.data.children.push(newTask.data);
                        var eAe = function() {
                            //temporary clear filter
                            // tree.clearFilter();
                            if(parentTask.isExpanded()) {
                                selectionModel.select(newTask);
                                //cellEditingPlugin.startEdit(newTask, 0);
                            } else {
                                tree.on('afteritemexpand', function startEdit(task) {
                                    if(task === parentTask) {
                                        selectionModel.select(newTask);
                                        //cellEditingPlugin.startEdit(newTask, 0);
                                        tree.un('afteritemexpand', startEdit);
                                    }
                                });
                                parentTask.expand();
                            }
                        };
                        if(tree.getView().isVisible(true)) {
                            eAe();
                        } else {
							tree.on('expand', function onExpand() {
                                expandAndEdit();
                                tree.un('expand', onExpand);
                            });
                            tree.expand();
                        }
                    }
                    tree.refreshSummary(function(callback){
                       tree.setLoading(false);
                    });
                    tree.refreshStaffing(function(callback){
                        //do nothing
                    });
                    tree.refreshView();
					if( autoRefreshGantt) refreshGanttChart();
                } else {
                    alert('<?php __('Error saving task. Please reload the page') ?>');
                }
            },
            complete: function(){
                btns.removeClass('disabled');
                text.hide();
                //close dialog
                cancel_popup('#special-task-info-2', false);
				$('#special-task-info-2').find('.loading-mark').removeClass('loading');
            },
        })
    }
	function list_phase(tree){
		var _phases = {};
		var _store = tree.getStore();
		$.each( _store.byIdMap, function( _id, _it){
			if( _it.get('is_phase') == 'true'){
				_phases[_it.get('phase_id')] = _it;
			}
		});
		return _phases;
	}
    function bindNctKeys(){
        var length = $('[data-ref]').length;
        $('[data-ref]').off('focus').on('focus', function(){
            var f = $(this).data('focused');
            if( f )return;
            $(this).data('focused', 1);
            $(this).select();
        }).on('blur', function(){
            $(this).data('focused', 0);
            // changeTotal(this);
        })
        .off('keydown').on('keydown', function(e){
            //tab key
            var index = $('[data-ref]').index(this);
            if( e.keyCode == 13 ){
                if( $(this).closest('td').next().hasClass('ciu-cell') ){
                    $(this).closest('tr').next().find('input:first').focus();
                } else {
                    $(this).closest('td').next().find('input').focus();
                }
                e.preventDefault();
            }
        });
    }
    function resetPicker(){
        if( $('#nct-range-type').val() == 0 ){
            $('#date-list').multiDatesPicker('resetDates');
            $('.nct-date').each(function(){
                var value = $(this).text();
                $('#date-list').multiDatesPicker('addDates', [$.datepicker.parseDate('dd-mm-yy', value)]);
            });
        }
    }
    function removeCol(e){
        //check consume
        var td = $(e).parent();
        var id = td.prop('id').replace('col-', ''), t = id.split('-');
        if( typeof Task.options.consume[id] != 'undefined' && Task.options.consume[id] ){
            alert('<?php __('This resource/PC already has consumed/in used data') ?>');
            return;
        }
        if( confirm('<?php __('Are you sure?') ?>') ){
            $('#col-' + id + ', .cell-' + id + ', #foot-' + id).each(function(){
                $(this).remove();
            });
            $('#res-' + id).show();
            listDeletion.push('res-' + id);
        }
    }

    function dialog_confirm(e, message) {
        $('.title').html(message);
        var dialog = $('#modal_dialog_confirm').dialog();
        $('#btnYes').click(function() {
            dialog.dialog('close');
            removeRowCall(e);
        });
        $('#btnNo').click(function() {
            dialog.dialog('close');
        });
    }
    function removeRowCall(e){
        var cols = $(e).parent().parent().find('input');
        cols.each(function(){
            var me = $(this);
            var id = me.data('id');
            var original = parseFloat($('#foot-' + id).text());
            $('#foot-' + id).text((original-parseFloat(me.val())).toFixed(2));
        });
        $(e).parent().parent().remove();
        //add to deletion list
        listDeletion.push('date:' + $(e).parent().parent().find('.nct-date').text());
        refreshPicker();
        calculateTotal();
    }
    function removeRow(e){
        //check if has in used / consumed
        if( $(e).parent().parent().find('.ciu-cell').text() != '0.00 (0.00)' ){
            alert('<?php __('Can not delete this because it has consumed/in used data') ?>');
            return;
        }
		removeRowCall(e);
		setLimitedDate('#nct-start-date', '#nct-end-date');
        // var check = dialog_confirm(e, '');
    }
    function changeTotal(e){
        //check here
        var me = $(e);
        if( !me.length )return;
        var old = parseFloat(me.data('old'));
        var newVal = parseFloat(me.val());
        var type = parseInt($('#nct-range-type').val());
        if( (isNaN(newVal) || newVal < 0 ) || (type == 0 && newVal > 1) ){
            if( type == 0 )
                alert('<?php __('Enter value between 0 and 1') ?>');
            else alert('<?php __('Please enter value >= 0') ?>');
            me.val(old);
            me.focus();
            return;
        }
        var id = me.data('id');
        var total = 0;
        $('.' + 'workload-' + id).each(function(){
            total += parseFloat($(this).val());
        });
        $('#foot-' + id).text(total.toFixed(2));
        me.data('old', newVal);
        calculateTotal();
    }

    function calculateTotal(){
        var total = 0;
        $('#assign-list tfoot .value-cell').each(function(){
            total += parseFloat($(this).text());
        });
        $('#nct-total-workload').val(total.toFixed(2));
    }

    function minMaxDate(){
        var min, max;
        var type = $('#nct-range-type').val();
        if( type != 0 ){
            return [$('#nct-start-date').datepicker('getDate'), $('#nct-end-date').datepicker('getDate')];
        }
        $('.nct-date').each(function(){
            var text = $(this).text();
            var value = $.datepicker.parseDate('dd-mm-yy', text);
            if( !min || min > value)
                min = value;
            if( !max || max < value)
                max = value;
        });
        return [min, max];
    }
    function isValidList(){
        var range = minMaxDate(),
            start = $('#nct-start-date').datepicker('getDate'),
            end = $('#nct-end-date').datepicker('getDate');
        if( range[0] < start || range[1] > end )return false;
        return true;
    }

    function selectCurrentRange(){
        //$('#nct-range-picker').find('.ui-datepicker-current-day').addClass('ui-state-highlight');
    }
    function unhightlightRange(){
        $('#nct-range-picker').find('.ui-state-highlight').removeClass('ui-state-highlight');
        $('#nct-range-picker').find('.ui-datepicker-current-day').removeClass('ui-datepicker-current-day');
    
        $('#date-list').find('.ui-state-highlight').removeClass('ui-state-highlight');
        $('#date-list').find('.ui-datepicker-current-day').removeClass('ui-datepicker-current-day');
    }

    function resetRange(){
        var start = $('#nct-start-date').datepicker('getDate');
        if( start ){
            $('#nct-range-picker').datepicker('setDate', start);
        }
        unhightlightRange();
        startDate = null;
        endDate = null;
    }
	/*
	* #390 13-08-2019  RE: Ticket #390 «INCIDENT/ANOMALIE» «task screen» «EN COURS DE DEV» «Développeur» «z0 Gravity»
	* hide all calendar picker except date
	*/
    function selectRange(){
        var val = parseInt($('#nct-range-type').val());
        switch(val){
            case 0:
                $('.start-end').show();
                $('.range-picker').hide();
                $('.period-input').hide();
				break;
            default:
                $('.start-end').hide();
                $('.range-picker').hide();
                $('.period-input').hide();
				break;
        }
        if( val ==3){
            $('#create-range').hide();
        } else {
            $('#create-range').show();
        }
        $('#date-list').define_limit_date('#nct-start-date', '#nct-end-date');
        resetRange();
		refreshPicker();
        if( !$('#assign-table tbody tr').length ){
            $('#assign-table tfoot .value-cell').text('0.00');
            $('#nct-total-workload').val('0.00');
        }
    }

    function dateDiff(date1, date2) {
        date1.setHours(0);
        date1.setMinutes(0, 0, 0);
        date2.setHours(0);
        date2.setMinutes(0, 0, 0);
        var datediff = Math.abs(date1.getTime() - date2.getTime()); // difference
        return parseInt(datediff / (24 * 60 * 60 * 1000), 10); //Convert values days and return value
    }

    function dateString(date, format){
        if( !format )format = 'dd-mm-yy';
        return $.datepicker.formatDate(format, date);
    }

    function toRowName(date){
        //parse date from task
        var part = date.split('_');
        switch(part[0]){
            case '1':
            case '3':
                var d = part[1].split('-');
                var start = new Date(d[2], d[1]-1, d[0]);
                d = part[2].split('-');
                var end = new Date(d[2], d[1]-1, d[0]);
                return dateString(start, 'dd/mm') + ' - ' + dateString(end, 'dd/mm/yy');
            case '2':
                var d = part[1].split('-');
                var start = new Date(d[2], d[1]-1, d[0]);
                return start.getMonth() + '/' + d[2];
            default:
                return dateString(new Date(part[1]));
        }
    }
	
	//assign: TODO: add column to the right before consume
	// 2 change assign
	function nct_get_list_assign(_this_id){
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
	function nct_list_assignedonChange(_this_id){
		nct_list_assigned = nct_get_list_assign( _this_id);
		nct_draw_employee_columns();
	}
	function nct_draw_employee_columns(){
		$('.ntc-employee-col').hide();
		var me = $('#nct_list_assigned');
		if(nct_list_assigned.length == 0){
			return;
		}
		
		// console.log( nct_list_assigned);
		$.each(nct_list_assigned, function(ind, emp){
			var id = emp.id;
			var patt = /-1$/;
			var is_profit_center =  patt.test(id);
			// console.log( is_profit_center);
			if(emp){
				var e_id = id.split('-');
				e_id = e_id[0];
			}
			var name = emp.name;
			var cell = $('.cell-' + id);
			if( cell.length ){
				// console.log( cell);
				cell.show();
			}else{
				var _avt = '<span style="margin-top: 10px;" class="circle-name" title="' + name + '" data-id="' + id + '">';
				if( is_profit_center == 1 ){
					_avt += '<i class="icon-people"></i>';
				}else{
					_avt += '<img width = 35 height = 35 src="'+  js_avatar( e_id ) +'" title = "'+ name +'" />';	
				}
				_avt += '</span>';
				_avt += '<span class="header-name" title="' + name + '" >'+ name.replace(/^PC \/ /, '') +'</span>';
				var html = '<td class="value-cell header-cell cell-' + id + ' ntc-employee-col" id="col-' + id + '" data-id="' + id + '">' + _avt + '</td>';
				$(html).insertBefore('#abcxyz');
				//add content
				$('.nct-date').each(function(){
					var ciu = $(this).parent().find('.ciu-cell'),
						date = $(this).prop('id').replace('date-', '');
					var _id = id.split('-');
					var e_id=_id[0],
						e_ispc= _id[1]==1 ? 1 : 0,
						ip_name = 'data[workloads][' + date + '][' + id + ']';
					$('<td class="value-cell cell-' + id + ' ntc-employee-col"><input type="hidden" name="' + ip_name + '[reference_id]" value="' + e_id + '"/><input type="hidden" name="' + ip_name + '[is_profit_center]" value="' + e_ispc + '"/><input type="text" id="val-' + date + '-' + id + '" data-old="0" class="workload workload-' + id + '" data-id="' + id + '" value="0" name="' + ip_name + '[estimated]" onchange="changeTotal(this)" data-ref/></td>').insertBefore(ciu);
				});
				bindNctKeys();
				
				// add footer
				$('<td class="value-cell cell-' + id + ' ntc-employee-col" id="foot-' + id + '" data-id="' + id + '">0.00</td></tr>').insertBefore('#total-consumed');
			}
			
		});
		refreshPicker();
		calculateTotal();
		set_width_nct();
	}
    $(document).ready(function(){
		function is_touch_device() {  
		  if(is_touch_enabled()){
			$('.btn-touch-move').show();
		  }  
		}
		function is_touch_enabled() {
			return ( 'ontouchstart' in window ) || 
				   ( navigator.maxTouchPoints > 0 ) || 
				   ( navigator.msMaxTouchPoints > 0 );
		}
		// Check device is Desktop touch
		is_touch_device();
        $('#clean-filters').click(function(){
            try {
                var panel = Ext.getCmp('pmstreepanel');
                panel.cleanFilters();
            } catch(ex){
            }

            var which = $('.type-focus').data('type');
            if( $('.type-focus').hasClass('type-focus') ){
                $('.filter-type').removeClass('type-focus');
                which = '';
            } else {
                $('.filter-type').removeClass('type-focus');
                $('.type-focus').addClass('type-focus');
            }
            $('#clean-filters').addClass('hidden');
        });
       
        $('#nct-start-date').datepicker({
            dateFormat: 'dd-mm-yy',
            beforeShowDay: function(d){
                var date = d.getDay();
                return [Workdays[date] == 1, '', ''];
            },
            onSelect: function(){
                refreshPicker();
				$(this).trigger('change');
            }
        });
        $('#nct-end-date').datepicker({
            dateFormat: 'dd-mm-yy',
            beforeShowDay: function(d){
                var date = d.getDay();
                var start = $('#nct-start-date').datepicker('getDate'),
                    check = Workdays[date] == 1;
                if( start != null ){
                    check = check && start <= d;
                }
                return [ check, '', ''];
            },
            beforeShow: function(e, i){
                var start = $('#nct-start-date').datepicker('getDate'),
                    end = $(e).datepicker('getDate');
                if( !end && start ){
                    return {defaultDate: start};
                }
            },
            onSelect: function(){
                refreshPicker();
				$(this).trigger('change');
            }
        });

        $('#date-list').on('click', 'td', function(e){
            e.preventDefault();
            if( $(this).hasClass('disabled') )return;
            var dates = getValidDate();
            //check neu co assign thi moi them 
			// 2
            if( nct_list_assigned.length == 0 ){
                return;
            }
            for(var i in dates){
                var date = dates[i];
                if( date ){
                    var invalid = false;
                    $('.nct-date').each(function(){
                        var value = $(this).text();
                        if( value == date )invalid = true;
                    });
                    if( !invalid ){
                        //add new row
                        var html = '<tr><td id="date-' + date + '" class="nct-date" >' + date + '</td>';
                        $('.header-cell').each(function(){
                            var col = $(this);
                            var id = col.prop('id').replace('col-', '');
                            var hide = '';
                            if( !col.is(':visible') )hide = 'style="display: none"';
                            html += '<td class="value-cell ntc-employee-col cell-' + id + '" ' + hide + '><input type="text" id="val-' + date + '-' + id + '" data-old="0" class="workload workload-' + id + '" data-id="' + id + '" value="0" style="" onchange="changeTotal(this)" data-ref></td>';
                        });
                        html += '<td style="" class="ciu-cell">0.00 (0.00)</td><td class="remove-cell"><a class="cancel" onclick="removeRow(this)" href="javascript:;"><img src="/img/new-icon/close.png" /><img src="/img/new-icon/close-blue.png" /></a></td></tr>';
                        $('#assign-table tbody').append(html);
                    }
                }
            }
            bindNctKeys();
            //$('#date-list').multiDatesPicker('resetDates');
        });

        $('#nct-reset-date').click(function(){
            refreshPicker();
        });

        $('#cancel-special').click(function(){
            if( $(this).hasClass('disabled') )return false;
            // $('#special-task-info-2').dialog('close');
			cancel_popup('#special-task-info-2', false);
        });
        $('#save-special').click(function(){
            if( $(this).hasClass('disabled') )return false;
            Task.commit();
        });
        $('#nct-range-picker .ui-datepicker-calendar tr').live('mousemove', function() { $(this).find('td a').addClass('ui-state-hover'); }).live('mouseleave', function() { $(this).find('td a').removeClass('ui-state-hover'); });
        $('#nct-range-picker').datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            dateFormat: 'dd-mm-yy',
            onSelect: function(dateText, inst) {
                var type = parseInt($('#nct-range-type').val());
                var date = $(this).datepicker('getDate');
                var curStart = $('#nct-start-date').datepicker('getDate'),
                    curEnd = $('#nct-end-date').datepicker('getDate');
                if( type == 1 ){
                    startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 1);  //select monday
                    endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 5);    //select friday
                } else if(type == 2 || type == 0){
                    startDate = new Date(date.getFullYear(), date.getMonth(), 1);  //select first day
                    endDate = new Date(date.getFullYear(), date.getMonth() + 1, 0);  //select last day
                }else {
                    startDate = curStart;
                    endDate = curEnd ;
                }
                if( curStart > startDate ){
                }
                if( curEnd < endDate ){
                }
                var start = dateString(startDate);
                var end = dateString(endDate);
                $('#date-list').multiDatesPicker('resetDates');
                selectCurrentRange();
            },
            beforeShowDay: function(date) {
                var cssClass = '';
                var canSelect = true;
                var start = $('#nct-start-date').datepicker('getDate'),
                    end = $('#nct-end-date').datepicker('getDate');
                if( !start || !end )canSelect = false;
                else canSelect = date >= start && date <= end;
                if(date >= startDate && date <= endDate)
                    cssClass = 'ui-state-highlight';
                return [canSelect, cssClass];
            },
            onChangeMonthYear: function(year, month, inst) {
                selectCurrentRange();
            }
        });
        $('#per-workload').keypress(function(e){
            var key = e.keyCode ? e.keyCode : e.which;
            if(!key || key == 8 || key == 13 || e.ctrlKey){return;}
            var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
            var _val = parseFloat(val, 10);
            if(!/^[\-+]?([0-9]{1}|[1-9][0-9]{1,9})(\.[0-9]{0,2})?$/.test(val)){
                e.preventDefault();
                return false;
            }
        });
        $(document).ready(function(){
            $('#per-workload').bind("cut copy paste",function(e) {
                e.preventDefault();
            });
        });
        $('#nct-unit-price').keypress(function(e){
            var key = e.keyCode ? e.keyCode : e.which;
            if(!key || key == 8 || key == 13 || e.ctrlKey){return;}
            var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
            var _val = parseFloat(val, 10);
            if(!/^[\-+]?([0-9]{1}|[1-9][0-9]{1,9})(\.[0-9]{0,2})?$/.test(val)){
                e.preventDefault();
                return false;
            }
        });
        //init datepick period
        $('#nct-period-start-date').datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            dateFormat: 'dd-mm-yy',
            beforeShowDay: function(d){
                var date = d.getDay();
                var start = $('#nct-start-date').datepicker('getDate'),
                    end = $('#nct-end-date').datepicker('getDate');
                var canSelect = d >= start && Workdays[date] == 1 && d <= end;
                return [canSelect, '', ''];
            },
            onSelect: function(dateText, inst) {
                var type = parseInt($('#nct-range-type').val());
                var date = $(this).datepicker('getDate');
                startDate = date;
            },
        });
        function changePeriodEnd(){
            //check neu co assign thi moi them date
            if(!$('#nct-period-end-date').val()) return;
			// 2
            if( nct_list_assigned.length == 0 ){
                return;
            }
            if( startDate && endDate ){
                _addRange(startDate, endDate);
            }
            $('.period-input-calendar').datepicker('setDate', null);
        }
        $('#nct-period-end-date').datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            dateFormat: 'dd-mm-yy',
            beforeShowDay: function(d){
                var date = d.getDay();
                var start = $('#nct-period-start-date').datepicker('getDate'),
                    end = $('#nct-end-date').datepicker('getDate');
                if( start && end){
                    var canSelect = d >= start && Workdays[date] == 1 && d <= end;
                    return [canSelect, '', ''];
                }
                return [false];
            },
            onSelect: function(dateText, inst) {
                var type = parseInt($('#nct-range-type').val());
                var date = $(this).datepicker('getDate');
                endDate = date;
                changePeriodEnd();
            },
        });
        $('#nct-range-type').change(function(e){
            //xoa het tat ca workload
            $('#assign-table tbody').html('');
            selectRange();
			set_width_nct();
        });
        $('#reset-range').click(function(){
            resetRange();
        });
        $('#nct-range-picker').on('click', 'td', function(e){
            e.preventDefault();
            //check neu co assign thi moi them date
			// 2
            if( nct_list_assigned.length == 0 ){
                return;
            }
            if( startDate && endDate ){
                _addRange(startDate, endDate);
            }
            $('.period-input-calendar').datepicker('setDate', null);
        });
        
        $('#create-range').click(function(){
			// 2
            if( nct_list_assigned.length == 0 ){
				$(this).closest('form').find(':submit').click();
				return false;
                // var dialog = $('#modal_dialog_alert').dialog();
                // $('#btnNoAL').click(function() {
                    // dialog.dialog('close');
                // });
                // return;
            }
            var dateType = parseInt($('#nct-range-type').val());
            //available for week and month and day.
            var start = $('#nct-start-date').datepicker('getDate'),
                end = $('#nct-end-date').datepicker('getDate');
            if(!start || !end){
                var dialog = $('#modal_dialog_alert2').dialog();
                $('#btnNoAL2').click(function() {
                    dialog.dialog('close');
                });
                return false;
            }
			setLimitedDate('#nct-start-date', '#nct-end-date');
            addRange(start, end);
        });
        $('#fill-workload').click(function(){
            var val = parseFloat($('#per-workload').val());
            if( isNaN(val) )return;
            if( $('#nct-range-type').val() == '0' && val > 1 )return;
            $('#assign-table tbody .value-cell input').each(function(){
                $(this).val(val).data('old', val).trigger('change');
            });
            //calculateTotal();
        });
		
    });
/*
    @date: js date object
    @day:
        0 = sunday
        1 = monday..
        6 = saturday
    @return: new date object
*/
    function getDayOfWeek(date, day) {
        var d = new Date(date),
            cday = d.getDay(),
            diff = d.getDate() - cday + day;
        return new Date(d.setDate(diff));
    }
    function getListDay(start, end){
        var list = {};
        var current = new Date(start);
        while( current <= end ){
            var sm = new Date(current);
            var currentDate = sm.format('yymmdd');
            if( typeof list[currentDate] == 'undefined' ){
                list[currentDate] = {
                    // start: monday,
                    date: new Date(sm.getFullYear(), sm.getMonth(), sm.getDate())
                };
            }
            current = new Date(sm.getFullYear(), sm.getMonth(), sm.getDate()+1);
        }
        return list;
    }
    function getListWeek(start, end){
        var list = {};
        var current = new Date(start);
        while( current <= end ){
            var monday = getDayOfWeek(current, 1),
                currentDate = monday.format('yymmdd');
            if( typeof list[currentDate] == 'undefined' ){
                list[currentDate] = {
                    start: monday,
                    end: getDayOfWeek(current, 5)
                };
            }
            current = new Date(monday.getFullYear(), monday.getMonth(), monday.getDate()+7);
        }
        return list;
    }
    function getListMonth(start, end){
        var list = {};
        var current = new Date(start);
        end = new Date(end.getFullYear(), end.getMonth() + 1, 0);
        while( current <= end ){
            current.setDate(1);
            var sm = new Date(current);
            var currentDate = sm.format('yymmdd');
            if( typeof list[currentDate] == 'undefined' ){
                list[currentDate] = {
                    start: sm,
                    end: new Date(sm.getFullYear(), sm.getMonth() + 1, 0)
                };
            }
            current.setMonth(current.getMonth() + 1);
        }
        return list;
    }
    function addRange(start, end, reset){
        if( reset ){
            $('#assign-table tbody').html('');
            selectRange();
        }
        var type = parseInt($('#nct-range-type').val());
        var list;
        if( type == 0 ) {
            list = getListDay(start, end)
        } else if (type == 1) {
            list = getListWeek(start, end)
        } else {
            list = getListMonth(start, end)
        }
        var minDate, maxDate;
        $.each(list, function(key, date){
            if(type == 0 ){
                if( !minDate || minDate >= date.date ){
                    minDate = new Date(date.date);
                }
                if( !maxDate || maxDate <= date.date ){
                    maxDate = new Date(date.date);
                }
                var _curYear = date.date.format('yy'),
                    _curDate = date.date.format('dd-mm-yy');
                if(Holidays[_curYear].length == 0){
                    loadHolidays(_curYear);
                }
                if( ($.inArray(_curDate, Holidays[_curYear]) == -1) && isValidDateForNctTaskDate(date.date) ){
                    _addRange(date.date, date.date);
                }
            } else {
                if( !minDate || minDate >= date.start ){
                    minDate = new Date(date.start);
                }
                if( !maxDate || maxDate <= date.end ){
                    maxDate = new Date(date.end);
                }
                _addRange(date.start, date.end);
            }
        });
        $('#nct-start-date').datepicker('setDate', minDate);
        $('#nct-end-date').datepicker('setDate', maxDate);
    }
    function isValidDateForNctTaskDate(d){
        var date = d.getDay();
        return Workdays[date] == 1;
    }
    function _addRange(start, end){
        var date = dateString(start) + '_' + dateString(end);
        var type = parseInt($('#nct-range-type').val());
        var rowName;
        if(type == 0){
            date = dateString(start);
            rowName = start.format('dd-mm-yy');
        } else if (type == 1) {
            rowName = start.format('dd/mm') + ' - ' + end.format('dd/mm/yy');
        }else{
            rowName = monthName[start.getMonth()] + ' ' + start.getFullYear();
        }
        // var rowName = type == 2 ? monthName[start.getMonth()] + ' ' + start.getFullYear() : start.format('dd/mm') + ' - ' + end.format('dd/mm/yy');
        var invalid = $('#date-' + date).length ? true : false;
        if( !invalid ){
            //add new row
            var html = '<tr><td id="date-' + date + '" class="nct-date">' + rowName + '</td>';
            $('.header-cell').each(function(){
                var col = $(this);
                var id = col.prop('id').replace('col-', '');
                var hide = '';
                if( !col.is(':visible') )hide = 'style="display: none"';
                html += '<td class="value-cell ntc-employee-col cell-' + id + '" ' + hide + '><input type="text" id="val-' + date + '-' + id + '" data-old="0" class="workload workload-' + id + '" data-id="' + id + '" value="0" style="" onchange="changeTotal(this)" data-ref></td>';
            });
            html += '<td style="" class="ciu-cell">0.00 (0.00)</td><td class="remove-cell"><a class="cancel" onclick="removeRow(this)" href="javascript:;"><img src="/img/new-icon/close.png" /><img src="/img/new-icon/close-blue.png" /></a></td></tr>';
            $('#assign-table tbody').append(html);
            bindNctKeys();
        }
		set_width_nct();
    }
    function addNewComment (){
      var text = $('.text-textarea').val(),
        _id = $('.submit-btn-msg').data('id');
        if(text != ''){
            var _html = '';
            $.ajax({
                url: '/project_tasks/update_text',
                type: 'POST',
                data: {
                    data:{
                        id: _id,
                        text_1: text
                    }
                },
                dataType: 'json',
                success: function(data){
                    if(data){
                        var idEm =  data['_idEm'],
                        avartarImage = listAvartar[idEm],
                        nameEmloyee = data['text_updater'],
                        comment = data['comment'],
                        created = data['text_time'];
                        _html += '<div class="content"><div class= "avartar-image" style="width: 60px"><img class="avartar-image-img" src="'+ avartarImage +'"></div><div class="content-comment"><div class="name"><h5>'+ nameEmloyee +'</h5><em>'+ created +'</em></div><div class="comment">'+ comment +'</div></div></div>';
                        $('#content-comment-id').append(_html);
                        $('.text-textarea').val("");
                    }
                }
            });
        }
    }
    function updateStatusMilestone(_this, miles_id){
		var miles_item = $('.milestone-'+miles_id);
		miles_item.addClass('loading');
		$.ajax({
			url: '/project_tasks_preview/updateStatusMilestone',
			type: 'POST',
			data: {
				data:{
					id: miles_id,
					project_id: project_id,
				}
			},
			dataType: 'json',
			success: function(res){
				if(res){
					if(res == 2) {
						miles_item.addClass('milestone-green');
					}else{
						miles_item.removeClass('milestone-green');
						var mile_color = (_milestoneColor !== 'undefined' && _milestoneColor[miles_id] !== 'undefined' && _milestoneColor[miles_id] != 'milestone-green') ? _milestoneColor[miles_id] : 'milestone-mi';
						miles_item.addClass(mile_color);
					}
				}
				miles_item.removeClass('loading');
			}
		});
    }
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
                        avatarSrc = js_avatar(idEm),
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
</script>

<div id="special-task-info-2" style="display:none;" class="buttons wd-full-popup autosize-popup">
	<div class="wd-popup-inner">
		<div class="add_nct_task-popup loading-mark wd-popup-container">
			<div class="wd-popup-head clearfix">
				<h4>&nbsp;</h4>
				<!-- <ul class="tabPopup" style="margin: 0; padding: 0;">
					<?php if( !empty($canModified)) { ?>
						<li class="liPopup active" ><a href="#NewTask"> <?php __('Non-continuous task info');?>  </a></li>
					<?php } ?> 
					
				</ul> -->
				
				<a style="right: 70px;height: 70px;position: absolute;padding: 25px;" target="_blank" href="<?php echo $this->Html->url('/guides/tache_non_continue') ?>">
					<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
					<defs>
					<style>
						.cls-1 {
							fill: #666;
							fill-rule: evenodd;
						}
					</style>
					</defs>
					<path id="help" class="cls-1" d="M1960,40a10,10,0,1,1,10-10A10,10,0,0,1,1960,40Zm0-18.667A8.667,8.667,0,1,0,1968.66,30,8.667,8.667,0,0,0,1960,21.333Zm2.04,8.192q-0.15.146-.39,0.374c-0.16.152-.3,0.284-0.41,0.4a2.539,2.539,0,0,0-.27.286,1.379,1.379,0,0,0-.27.909v0.66h-1.66V31.255a2.13,2.13,0,0,1,.14-0.873,3.544,3.544,0,0,1,.61-0.755l1.07-1.07a1.272,1.272,0,0,0,.34-0.909,1.255,1.255,0,0,0-.35-0.9,1.231,1.231,0,0,0-.91-0.359,1.325,1.325,0,0,0-.93.344,1.347,1.347,0,0,0-.43.917h-1.78a3.024,3.024,0,0,1,1.02-2.046,3.251,3.251,0,0,1,2.18-.741,3.1,3.1,0,0,1,2.12.711,2.488,2.488,0,0,1,.83,1.988,2.246,2.246,0,0,1-.49,1.467C1962.28,29.26,1962.13,29.427,1962.04,29.525Zm-2.15,3.71a1.14,1.14,0,0,1,.8.315,1.027,1.027,0,0,1,.34.763,1.048,1.048,0,0,1-.34.77,1.084,1.084,0,0,1-.79.323,1.136,1.136,0,0,1-.8-0.316,1.015,1.015,0,0,1-.33-0.762,1.04,1.04,0,0,1,.33-0.77A1.07,1.07,0,0,1,1959.89,33.235Z" transform="translate(-1950 -20)"/>
					</svg>
				</a>
				<a href="javascript:void(0);" class="close_template_add_nct_task wd-close-popup" onclick="cancel_popup(this, false)"><img title="<?php __('Close');?>" src="<?php echo $this->Html->url('/img/new-icon/close.png');?>"></a>
			</div>
			<div class="template-popup-content wd-popup-content">
	<?php
	echo $this->Form->create('NCTTask', array(
		'type' => 'POST',
		'url' => array('controller' => 'project_tasks', 'action' => 'saveNcTask'),
		'class' => 'form-style-2019',
		'id' => 'NCTForm',
		'data-reload' => 0,
	));
	?>
    <div class="w2">
        <input type="hidden" id="nct-id" class="text" />
        <input type="hidden" id="nct-phase-id" class="text" />
        <div class="wd-row wd-row-inline">
			<div class="wd-inline-col wd-col-530">				
				<?php 
				echo $this->Form->input('task.task_title', array(
					'type'=> 'text',
					'id' => 'nct-name',
					'label' => __d(sprintf($_domain, 'Project_Task'), 'Task', true),
					'required' => true,
					'rel' => 'no-history',
					'div' => array(
						'class' => 'wd-input label-inline required'
					)
				));
				?>
				
				<div class="wd-input wd-area wd-none required">
					<div class="wd-multiselect multiselect nct_list_assigned" id="nct_list_assigned">
						<a href="javascript:void(0);" class="wd-combobox wd-project-manager">
							<p>
								<?php  __d(sprintf($_domain, 'Project_Task'), 'Assigned To');?>
							</p>
						</a>
						<div class="wd-combobox-content task_assign_to_id" style="display:none;">
							<div class="context-menu-filter">
								<span>
									<input type="text" class="wd-input-search" placeholder="<?php __('Search...');?>" rel="no-history">
								</span>
							</div>
							<div class="option-content">
							</div>
						</div>
					</div>
				</div>
				<div class="wd-row">
					<div class="wd-col wd-col-sm-6">
						<?php 
						$list_status = !(empty($priority['Statuses'])) ? Set::combine($priority['Statuses'], '{n}.ProjectStatus.id', '{n}.ProjectStatus.name') : array();
						echo $this->Form->input('task.task_status_id', array(
							'type'=> 'select',
							'id' => 'nct-status',
							'label' => __d(sprintf($_domain, 'Details'), 'Status', true),
							'options' => $list_status,
							'required' => true,
							'rel' => 'no-history',
							'div' => array(
								'class' => 'wd-input label-inline required has-val'
							)
						));
						
						?>
					</div>
					<div class="wd-col wd-col-sm-6">
						<?php 
						$periods = array( __('Day',true), __('Week',true), __('Month',true)/* , __('Period',true )*/);
						echo $this->Form->input('type', array(
							'type'=> 'select',
							'id' => 'nct-range-type',
							'label' => __('Period', true),
							'options' => $periods,
							'required' => true,
							'rel' => 'no-history',
							'selected' => 2,
							'div' => array(
								'class' => 'wd-input label-inline has-val required'
							)
						));
						?>
					</div>
				</div>
				<div class="wd-row">
					<div class="wd-col wd-col-sm-6">
						<div class="wd-input wd-area wd-none">
						<?php 				
							echo $this->Form->input('task.task_start_date', array(
								'type'=> 'text',
								'id' => 'nct-start-date',
								'label' => __d(sprintf($_domain, 'Project_Task'), 'Start date', true),
								'required' => true,
								'class' => 'wd-date',
								'onchange'=> 'nct_validated(this);',
								'autocomplete' => 'off',
								'rel' => 'no-history',
								'div' => array(
									'class' => 'wd-input label-inline required'
								)
							));
							?>
						</div>
					</div>
					<div class="wd-col wd-col-sm-6">
						<div class="input-icon-container">
							<?php 				
							echo $this->Form->input('task.task_end_date', array(
								'type'=> 'text',
								'id' => 'nct-end-date',
								'label' => __d(sprintf($_domain, 'Project_Task'), 'End date', true),
								'required' => true,
								'class' => 'wd-date',
								'autocomplete' => 'off',
								'onchange'=> 'nct_validated(this);',
								'rel' => 'no-history',
								'div' => array(
									'class' => 'wd-input label-inline required'
								)
							));
							?>
							<div class="wd-icon">
								<a href="javascript:void(0);" id="create-range" class="btn"><i class="icon-reload"></i></a>
							</div>
						</div>
					</div>
				</div>
				<div class="wd-row">
					<div class="wd-col wd-col-sm-6">
						<?php 
						$list_profiles = !(empty($priority['Profiles'])) ? Set::combine($priority['Profiles'], '{n}.Profile.id', '{n}.Profile.name') : array();
						echo $this->Form->input('task.profile_id', array(
							'type'=> 'select',
							'id' => 'nct-profile',
							'label' => __d(sprintf($_domain, 'Project_Task'), 'Profile', true),
							'options' => $list_profiles,
							'required' => false,
							'rel' => 'no-history',
							'div' => array(
								'class' => 'wd-input label-inline has-val',
								'id' => 'wd-profile',
								'style' => 'display: none'
							)
						));
						$list_priorities = !(empty($priority['Priorities'])) ? Set::combine($priority['Priorities'], '{n}.ProjectPriority.id', '{n}.ProjectPriority.priority') : array();
						echo $this->Form->input('task.task_priority_id', array(
							'type'=> 'select',
							'id' => 'nct-priority',
							'label' => __d(sprintf($_domain, 'Project_Task'), 'Priority', true),
							'options' => $list_priorities,
							'required' => false,
							'rel' => 'no-history',
							'empty' => '',
							'div' => array(
								'class' => 'wd-input label-inline',
								'id' => 'wd-priority',
								'style' => 'display: none'								
							)
						));
						$list_milestones = !(empty($priority['Milestone'])) ? Set::combine($priority['Milestone'], '{n}.ProjectMilestone.id', '{n}.ProjectMilestone.project_milestone') : array();
						echo $this->Form->input('task.milestone_id', array(
							'type'=> 'select',
							'id' => 'nct-milestone',
							'label' => __('Milestone', true),
							'options' => $list_milestones,
							'required' => false,
							'rel' => 'no-history',
							'empty' => '',
							'div' => array(
								'class' => 'wd-input label-inline',
								'id' => 'wd-milestone',
								'style' => 'display: none'
							)
						));
						echo $this->Form->input('task.unit_price', array(
							'type'=> 'text',
							'id' => 'nct-unit-price',
							'label' => __('Unit Price', true),
							'required' => false,
							'rel' => 'no-history',
							'div' => array(
								'class' => 'wd-input label-inline',
								'id' => 'wd-unit-price',
								'style' => 'display: none'
							)
						));
						echo $this->Form->input('task.total_workload', array(
							'type'=> 'text',
							'id' => 'nct-total-workload',
							'label' => __d(sprintf($_domain, 'Project_Task'), 'Workload', true),
							'required' => false,
							'disabled' => true,
							'rel' => 'no-history',
							'div' => array(
								'class' => 'wd-input label-inline has-val',
							),
							
						));
						if( !empty($ManualConsumed) && $ManualConsumed ){
							echo $this->Form->input('task.manual_consumed', array(
								'type'=> 'text',
								'id' => 'nct-manual',
								'label' => __d(sprintf($_domain, 'Project_Task'), 'Manual Consumed', true),
								'required' => false,
								'rel' => 'no-history',
								'div' => array(
									'class' => 'wd-input label-inline',
								),
								
							));
						}
						?>
						<div class="input-icon-container">
							<?php
							echo $this->Form->input('per-workload', array(
								'type'=> 'text',
								'id' => 'per-workload',
								'label' => __d(sprintf($_domain, 'Project_Task'), 'Workload', true),
								'required' => false,
								'autocomplete' => 'off',
								'rel' => 'no-history',
								'div' => array(
									'class' => 'wd-input label-inline'
								)
							));
							?>
							<div class="wd-icon">
								<a href="javascript:void(0);" id="fill-workload" class="btn">
									<i class="icon-reload"></i>
								</a>
							</div>
						</div>
					</div>
					<div class="wd-col wd-col-sm-6">
						<div class="wd-input wd-calendar">
							<div class="period-input">
							<label><?php __d(sprintf($_domain, 'Project_Task'), 'Start date') ?></label>
							<input type="text" id="nct-period-start-date" class="text period-input-calendar nct-date-period" readonly="readonly" />
							<br>
							<label><?php __d(sprintf($_domain, 'Project_Task'), 'End date') ?></label>
							<input type="text" id="nct-period-end-date" class="text period-input-calendar nct-date-period" readonly="readonly" />
							<br>
							</div>
							<div id="nct-range-picker" class="range-picker" style="display: none; margin-bottom: 5px"></div>
							<a id="add-range" href="javascript:;" class="btn-text btn-green range-picker" style="display: none;">
								<img src="<?php echo $this->Html->url('/img/ui/blank-plus.png') ?>" />
								<span><?php __('Create') ?></span>
							</a>
							<a id="reset-range" class="btn-text range-picker" href="javascript:;" style="display: none;">
								<img src="<?php echo $this->Html->url('/img/ui/blank-reset.png') ?>" />
								<span><?php __('Reset') ?></span>
							</a>
							<div class="input-icon-container">
								<div class="wd-input-group">
									<div id="date-list" class="start-end" style="margin-bottom: 5px"></div>
								</div>
								<div class="wd-icon">
									<a id="add-date" href="javascript:;" class="btn start-end disabled">
										<i class="icon-reload"></i>
									</a>
								</div>
							</div>
							<a id="nct-reset-date" class="btn-text start-end" href="javascript:;">
								<img src="<?php echo $this->Html->url('/img/ui/blank-reset.png') ?>" />
								<span><?php __('Reset') ?></span>
							</a>
						</div>
					</div>
				</div>
				<!-- 
				<div class="wd-row">
					<div class="wd-col">
						<div class="wd-input wd-buttons">
							<ul class="type_buttons">
								<li><a id="save-special" class="new" onclick="return false;" href="#"><?php echo __('Submit') ?></a></li>
								<li><a class="cancel" id="cancel-special" href="javascript:void(0)"><?php echo __('Close') ?></a></li>
								<li id="nct-progress" style="display: none; margin-right: 10px"><?php __('Saving...') ?></li>
							</ul>
						</div>	
					</div>
				</div>
				-->
			</div>
			<div class="wd-inline-col wd-col-autosize">	
				<div id="assign-list">
					<table id="assign-table" class="nct-assign-table">
						<thead>
							<tr>
								<td class="bold base-cell null-cell">&nbsp;</td>
								<td class="base-cell" id="abcxyz"><?php __d(sprintf($_domain, 'Project_Task'), 'Consumed') ?> (<?php __d(sprintf($_domain, 'Project_Task'), 'In Used') ?>)</td>
								<td class="remove" id="remove-row"></td>
							</tr>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<tr>
								<td class="base-cell"></td>
								<td class="base-cell" id="total-consumed">0</td>
								<td class="base-cell" id="remove-row"></td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
        </div>
		<div class="wd-row wd-submit-row">
			<div class="wd-col-xs-12">
				<div class="wd-submit">
					<button type="submit" class="btn-form-action btn-ok btn-right hidden" id="btnSavespecial">
						<span><?php __('Submit') ?></span>
					</button>
					<a href="javascript:void(0);" class="btn-form-action btn-ok btn-right" id="save-special">
						<span><?php __('Submit') ?></span>
					</button>
					<a class="btn-form-action btn-cancel" id="cancel-special" href="javascript:void(0);" onclick="cancel_popup(this, false);">
						<?php echo __("Cancel", true); ?></span>
					</a>
				</div>
			</div>
		</div>
      
    </div>
	<?php echo $this->Form->end();?>
	
			</div>
		</div> <!-- close wd-popup-container -->
	</div>
</div>
<script>
var project_id = <?php echo json_decode($project_id)?>;
var api_key = <?php echo json_encode($api_key) ?>;
var isTablet = <?php echo json_encode($isTablet) ?>;
var isMobile = <?php echo json_encode($isMobile) ?>;
var display_rescource_name = <?php echo json_encode($display_rescource_name) ?>;
var nct_list_assigned = {};
var history_ready = 0;
var canModified = <?php echo json_encode($canModified) ?>;
HistoryFilter.afterLoad = function(){
	//console.log( 'HistoryFilter.afterLoad');
	history_ready = 1;
	set_view_predecessor();
}
function set_view_predecessor(){
	if( !history_ready) return;
	var panel = Ext.getCmp('pmstreepanel');
	if( !panel) return;
	var predecessor_col = panel.columnManager.getHeaderByDataIndex('predecessor');
	if( predecessor_col){
		// if( !predecessor_col.hidden){
			var predecessor_sw = $('#predecessor-switch');
			if( predecessor_sw.length){
				//console.log( 'show');
				// predecessor_sw.show();
				var _checked = predecessor_sw.find(':checkbox').is(':checked');
				panel.columnManager.getHeaderByDataIndex('predecessor').setVisible(_checked);
				var id_col = panel.columnManager.getHeaderByDataIndex('id');
				if( id_col) id_col.setVisible(_checked);
			}
		// }
	}
}
function treepanelAfterLoad(panel){
	setTimeout(function(){ refreshGanttChart() }, 500);
	if( !panel) return;
	// if( typeof ajaxGetResources == 'function') ajaxGetResources(project_id);
	// if( typeof nct_ajaxGetResources_Milestone == 'function') nct_ajaxGetResources_Milestone(project_id);
	if( typeof popupTask_phaseOnChange == 'function') popupTask_phaseOnChange();
	if( typeof nct_phaseOnChange == 'function') nct_phaseOnChange();
	if( typeof popupnct_selectRange == 'function') popupnct_selectRange();
	// if( typeof re_init_afterload == 'function' ) re_init_afterload();
}

function set_name_resource(){
	
}

function loadHolidays(year){
    if( typeof Holidays[year] == 'undefined' ){
		Holidays[year] = [];
        $.ajax({
            url: '<?php echo $this->Html->url('/') ?>holidays/getYear/' + year,
            dataType: 'json',
            success: function(data){
                Holidays[year] = data;
                if( typeof applyDisableDays == 'function')  applyDisableDays();
				if( typeof popupnct_applyDisableDays == 'function') popupnct_applyDisableDays();
            }
        });
    }
}
function cancel_popup_special_task_info_2(elm){
	if( Task ){
		Task.destroy();	
	}
}

function set_width_nct(){
	// return;
	$.each( $('.wd-row-inline'), function (i, _row){
		var _row = $(_row);
		var _width = 0;
		$.each( _row.children(), function( j, _col){
			_width += $(_col).width()+45;
		});
		_row.width(_width);
	});
}
set_width_nct();
var nct_newtask_date_validated = 1
function nct_validated(_this){
	var st_date = $('#nct-start-date').val();
	var en_date = $('#nct-end-date').val();
	if( st_date == '' || en_date == '' ) return;
	st_date = st_date.split('-');
	en_date = en_date.split('-');

	st_date = new Date(st_date[2],st_date[1],st_date[0]);
	en_date = new Date(en_date[2],en_date[1],en_date[0]);
	if( st_date > en_date){
		$(_this).addClass('invalid');
		nct_newtask_date_validated = 0;
	}else{
		$('#nct-start-date').removeClass('invalid');
		$('#nct-end-date').removeClass('invalid');
		nct_newtask_date_validated = 1;
	}
};
function applyDisableDays(){
    hlds = [];
    $.each(Holidays, function(year, list){
        hlds = hlds.concat(list);
    });
    if( hlds.length ){
        $('#date-list').multiDatesPicker('addDates', hlds, 'disabled');
    }
}
function refreshPicker(){
    var start = $('#nct-start-date').datepicker('getDate'),
        end = $('#nct-end-date').datepicker('getDate');
    if( start && end && start <= end ){
        // $('#date-list').datepicker('setDate', start);
		$('#date-list').multiDatesPicker('resetDates');
		$('#date-list').gotoDate(start);
        if( nct_list_assigned.length > 0 ){
            $('#add-date').removeClass('disabled');
            $('#add-range').removeClass('disabled');
        }
        resetRange();
        $('#nct-period-start-date, #nct-period-end-date').prop('disabled', false);
    }
    else {
        $('#add-date').addClass('disabled');
        $('#add-range').addClass('disabled');
        $('#nct-period-start-date, #nct-period-end-date').prop('disabled', true);
    }
    if( !start ) year = new Date().getFullYear();
    else year = start.getFullYear();
    loadHolidays(year);
    resetPicker();
    $('#date-list').datepicker('refresh');
    $('#nct-range-picker').datepicker('refresh');
	// unhightlightRange();
}
//if date in disabled fields, reject
function isValidDate(d, start, end){
    var date = d.getDay();
    return d >= start && d <= end && Workdays[date] == 1;
}
function getValidDate(){
    var result = [];
    var dates = $('#date-list').multiDatesPicker('getDates', 'object');
    var start = $('#nct-start-date').datepicker('getDate'),
        end = $('#nct-end-date').datepicker('getDate');
    for(var i = 0; i < dates.length; i++){
        if( isValidDate(dates[i], start, end) ){
            var date = dates[i].getDate(),
                month = dates[i].getMonth() + 1;
            result.push( (date < 10 ? '0' + date : date) + '-' + (month < 10 ? '0' + month : month) + '-' + dates[i].getFullYear() );
        }
    }
    return result;
}
function deleteTaskAction(){
	var panel = Ext.getCmp('pmstreepanel');
	panel.deleteTaskHandle();
}
function openAttachmentDialog(){
    // if( !canModify && !readOnly )return false;
    var me = $(this), id = me.data('id');
    var _html = task_title = '';
    var latest_update = '';
    var form = $("#UploadIndexForm");
    form.find('input[name="data[Upload][id]"]').val(id);
    var popup = $('#template_attach');
    $.ajax({
        url: '/kanban/getTaskAttachment/'+ id,
        type: 'POST',
        data: {
            id: id,
        },
        dataType: 'json',
        success: function(data) {
            $("#template_upload").addClass('show');
            $('.light-popup').addClass('show');
            $("#template_upload").find('.project-name').empty().append(data['task_title']);
            _html += '<ul>';
            if (data['attachments']) {
                $.each(data['attachments'], function(ind, _data) {
					var attach_id = _data['ProjectTaskAttachment']['id'] ? _data['ProjectTaskAttachment']['id'] : 0;
					var task_id = _data['ProjectTaskAttachment']['task_id'] ? _data['ProjectTaskAttachment']['task_id'] : 0;
                    if(_data['ProjectTaskAttachment']['is_file'] == 1){
                        if((/\.(gif|jpg|jpeg|tiff|png)$/i).test(_data['ProjectTaskAttachment']['attachment'])){ 
                            _html += '<li><i class="icon-paper-clip"></i><span class="file-name" onclick="openFileAttach.call(this);" data-id = "'+ attach_id +'">'+ _data['ProjectTaskAttachment']['attachment'] +'</span><a  data-id = "'+ attach_id +'"><img src="/img/new-icon/delete-attachment.png" alt="'+ attach_id +'" data-id = "'+ task_id +'" onclick="deleteAttachmentFile.call(this)"></a></li>';
                        }else{
                            _link = '/kanban/attachment/'+ attach_id +'/?sid='+ api_key;
                            _html += '<li><i class="icon-paper-clip"></i><a class="file-name"  href="'+ _link +'">'+ _data['ProjectTaskAttachment']['attachment'] +'</a><a  data-id = "'+ attach_id +'"><img src="/img/new-icon/delete-attachment.png" alt="'+ attach_id +'" data-id = "'+ task_id +'"  onclick="deleteAttachmentFile.call(this)"></a></li>';
                        }
                    }else{
                        _html += '<li><i class="icon-link"></i><a class="file-name" target="_blank" href="'+ _data['ProjectTaskAttachment']['attachment'] +'">'+ _data['ProjectTaskAttachment']['attachment'] +'</a><a  data-id = "'+ attach_id +'"><img src="/img/new-icon/delete-attachment.png" alt="'+ attach_id +'" data-id = "'+ task_id +'" onclick="deleteAttachmentFile.call(this)"></a></li>';
                    }
                    
                });
            }
            _html += '</ul>';
            $('#content_comment .append-comment').html(_html);

            // $("#dialog_attachement_or_url").dialog('option',{title:data.task_title, width: '320px'}).dialog('open');

        }
    });
}
function openCommentDialog(){
    // if( !canModify && !readOnly )return false;
    var me = $(this), pTaskId = me.prop('alt');
	var has_comment = 0;
    // var pTaskId = record.id;
	 var popup = $('#wd-task-comment-dialog');
    $.ajax({
        url: '/project_tasks/getCommentTxt',
        data: {
            pTaskId: pTaskId
        },
        type:'POST',
        async: false,
        dataType: 'json',
        success: function(data){
           
            if(data['result']){
                $.each(data['result'], function(ind, val){
                    var comment = val['ProjectTaskTxt']['comment'].replace(/\n/g, "<br>")
					if(comment) has_comment = 1;
                });
				
            }
			// update read status
			if( has_comment ){
				var panel = Ext.getCmp('pmstreepanel');
				panel.setLoading(i18n('Please wait'));
				panel.getStore().getById(pTaskId).set('read_status', 1);
				panel.getView().refresh();
				panel.setLoading(false);
			}
			
			draw_comment(popup, data);
            
		}			
    });
}
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
					ava_src += '<img width = 35 height = 35 src="' + js_avatar( _cm['employee_id']) + '" title = "' + name + '" />';
					comment_tag += '<div class="content content-' + index++ + '"><div class="avatar"><span class="circle-name">' + ava_src + '</span></div><div class="item-content"><p>' + name + ' ' + date + '</p><div class="comment">' + comment + '</div></div>';
					if(myRole == 'admin' || _cm['employee_id'] == employee_id) comment_tag += '<a class="cm-delete" href="javascript:void(0);" onclick="deleteCommentTask(this, '+ _cm['id']+');"><img src="/img/new-icon/delete-attachment.png"></a>';
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
function openAttachment(){
    if( !canModify )return false;
    var me = $(this), taskId = me.prop('alt');
    window.open('<?php echo $this->Html->url('/project_tasks/view_attachment/') ?>' + taskId, '_blank');
}
function deleteAttachment(){
    if( !canModify )return false;
    var me = $(this), taskId = me.prop('alt');
    if( confirm('<?php __('Delete?') ?>') ){
        var panel = Ext.getCmp('pmstreepanel');
        panel.setLoading(i18n('Please wait'));
        //call ajax
        $.ajax({
            url: '<?php echo $this->Html->url('/project_tasks/delete_attachment/') ?>' + taskId,
            complete: function(){
                panel.getStore().getById(taskId).set('attachment', null);
                panel.getView().refresh();
                panel.setLoading(false);
            }
        })
    }
}
function openFileAttach(){
    _id = $(this).data("id");
    $.ajax({
        url : "/kanban/ajax/"+ _id,
        type: "GET",
        cache: false,
        success: function (html) {
            var dump = $('<div />').append(html);
            if( dump.children('.error').length == 1 ){
                //do nothing
            } else if ( dump.children('#attachment-type').val() ) {
                $('#contentDialog').html(html);
                $('#dialogDetailValue').addClass('popup-upload');
                showMe();
            }
        }
    });

}
function setLimitedDate(ele_start, ele_end){
	var limit_sdate = $('#assign-table tbody tr:first').find('.nct-date').attr('id');
	if(limit_sdate){
		 limit_start_date = limit_sdate.split("-");
		 limit_s_date = new Date(limit_start_date[2] +'-'+limit_start_date[1] +'-'+limit_start_date[3]);
		 $(ele_start).datepicker('option','maxDate',limit_s_date);
	}
	limit_edate = $('#assign-table tbody tr:last').find('.nct-date').attr('id');
	if(limit_edate){
		 limit_end_date = limit_edate.split("-");
		 limit_e_date = new Date(limit_end_date[2] +'-'+limit_end_date[1] +'-'+limit_end_date[3]);
		  $(ele_end).datepicker('option','minDate',limit_e_date);
	}
}
function deleteAttachmentFile(){
	if( !canModify )return false;
    var attachId = $(this).prop('alt');
	var taskId = $(this).data('id');
    var itemPic = $(this).closest('li');
    $.ajax({
        url: '<?php echo $this->Html->url('/kanban/delete_attachment/') ?>',
		type: "POST",
		data: {
			attachId: attachId,
			taskId: taskId
		},
		dataType: 'json',
		cache: false,
        success: function (data) {
            itemPic.empty();
        }
    })
}
function predecessor_unhightlightTask(){
	$('.x-grid-item').removeClass('item-task-predecessor');
}
function predecessor_hightlightTask(taskID){
	$('.x-grid-item').removeClass('item-task-predecessor');
	pre_task = $('.task-' + taskID);
	pre_task.closest('.x-grid-item').addClass('item-task-predecessor');
}
function syncX(file){
    $.ajax({
        url: '<?php echo $this->Html->url('/project_tasks/syncX') ?>',
        type: 'POST',
        data: {data: {file: file}}
    });
}
    var today = new Date('<?php echo date('Y-m-d') ?>');
    $(document).ready(function(){
		$('#ProjectShowPredecessor').on('change', set_view_predecessor);
		$('#ShowResouceName').on('change', function(){
			var _checked = $(this).is(':checked');
			display_rescource_name = _checked;
			var panel = Ext.getCmp('pmstreepanel');
			if( !panel) return;
			panel.getView().refresh();
			$.ajax({
				url: '<?php echo $this->Html->url('/project_tasks/switchEmployeeHistory') ?>',
				type: 'POST',
				data: { data: {
					value: _checked,
					key: 'setting_display_name_resource',
				}},
				success: function (res) {
					
				}
			});
		});
		$('#autoRefreshGantt').on('change', function(){
			var _checked = $(this).is(':checked');
			autoRefreshGantt = _checked;
			refreshGanttChart();
			$.ajax({
				url: '<?php echo $this->Html->url('/project_tasks/switchEmployeeHistory') ?>',
				type: 'POST',
				data: { data: {
					value: _checked,
					key: 'auto_refresh_gantt',
				}},
				success: function (res) {
					
				}
			});
		});
        $('#date-list').multiDatesPicker({
            dateFormat: 'dd-mm-yy',
            separator: ',',
            //numberOfMonths: [1,3],
            beforeShowDay: function(d){
                var start = $('#nct-start-date').datepicker('getDate'),
                    end = $('#nct-end-date').datepicker('getDate');
                if( !start || !end || start > end ){
                    return [false, '', ''];
                }
                //var date = d.getDay();
                return [ isValidDate(d, start, end) ,'', ''];
            },
            onChangeMonthYear: function(year){
                loadHolidays(year);
            }
        });

        function check(ele_start, ele_end){
            var dialog_calendar = $('.ui-datepicker-calendar');
            var date = dialog_calendar.find('td');
			
            start_date = $(ele_start).val();
            end_date = $(ele_end).val();
            if(start_date && end_date){
                sdate = start_date.split("-");
                edate = end_date.split("-");
                start_date = new Date(sdate[1] +'-'+sdate[0] +'-'+sdate[2]);
                end_date = new Date(edate[1] +'-'+edate[0] +'-'+edate[2]);
                $(date).each(function() {
                    data_time = new Date($(this).data('time'));
                    if(data_time > start_date && data_time < end_date){
                        $(this).css({"background-color": "", "border-color": ""});
                        $(this).find('a').css("color", "");
                        $(this).find('span').css("color", "");
                    }else if(($(ele_start).val() == $(this).data('time') || $(ele_end).val() == $(this).data('time')) && (!$(this).hasClass('ui-state-disabled') || ($(this).hasClass('ui-datepicker-week-end')))) {
                        $(this).css({"background-color": "#247FC3", "border-color": "#fff"});
                        $(this).find('a').css("color", "#fff");
                        $(this).find('span').css("color", "#fff");
                    }
                });
            }
        }
	
        $('#nct-range-type').change(function(){
            check('#nct-start-date', '#nct-end-date');
        });
        $('body').on('click', '.ui-datepicker-header', function(e){
            check('#nct-start-date', '#nct-end-date');
        });
        $('.hasDatepicker').on('click', function(e){
            check('#nct-start-date', '#nct-end-date');
        });
        $('#nct-period-end-date').on('click', function(e){
            check('#nct-period-start-date', '#nct-end-date');
        });
        $('#nct-start-date').change(function(){
            var ele_start = $(this);
            min_date = $(ele_start).val();
            $('#nct-end-date').datepicker('option','minDate',min_date);

        });
        $('#nct-end-date').change(function(){
            var ele_end = $(this);
            max_date = $(ele_end).val();
            $('#nct-start-date').datepicker('option','maxDate',max_date);

        });
        
        var target = jQuery('.gantt-chart-wrapper').find('#month_' + (today.getMonth() + 1) + '_' + today.getFullYear());
        if( target.length ){
            jQuery('.gantt-chart-wrapper').scrollTo( target, true, null );
        }
        $('#dialog_attachement_or_url').dialog({
            position    :'center',
            autoOpen    : false,
            autoHeight  : true,
            modal       : true,
            width       : 500,
            close: function(){
                $('.update_url').val('');
                $('.update_attach_class').val('');
            }
        });
        var isSaving = false;
        $("#ok_attach").click(function(){
            if( isSaving )return false;
            isSaving = true;
            $('.cancel, .new, .yes_importcsv').addClass('grayscale');
            $('#action-attach-url').css('display', 'none');
            $('.browse').css('display', 'block');
            var form = $("#form_dialog_attachement_or_url");

            var panel = Ext.getCmp('pmstreepanel');
            
                taskId = $('#UploadId').val(),
                params = {
                    url: form.prop('action'),
                    form: form[0],
                    method: 'POST',
                    success: function(response){
                        var data = Ext.JSON.decode(response.responseText);
                        if( data.status ){
                            //update panel
                            try {
                                panel.getStore().getById(taskId).set('attachment', data.attachment);
                                panel.getView().refresh();
                            }catch(ex){
                            }
                            //sync
                            if( data.file && data.sync ){
                                syncX(data.file);
                            }
                        }
                        $("#dialog_attachement_or_url").dialog('close');
                    },
                    failure: function(response){
                        // console.log(response.responseText);
                    },
                    callback: function(){
                        isSaving = false;
                        $('.cancel, .new, .yes_importcsv').removeClass('grayscale');
                    }
                };
            //if use url
            if( $('#gs-url').hasClass('gs-url-add') ){
                var url = $.trim($('.update_url').val());
                if( !url )return false;
            }
            //else upload document
            else {
                var file = $('.update_attach_class').val();
                if( !file )return false;
                params.isUpload = true;
            }
            //make ajax call
            Ext.Ajax.request(params);
        });
        $("#gs-url").click(function(){
            $(this).addClass('gs-url-add');
            $('#gs-attach').addClass('gs-attach-remove');
            $('.update_url').removeAttr('disabled').css('border', '1px solid #3B57EE');
            $('.update_attach_class').attr('disabled', 'disabled').css('border', '1px solid #d4d4d4');
        });
        $("#gs-attach").click(function(){
            $(this).removeClass('gs-attach-remove');
            $('#gs-url').removeClass('gs-url-add');
            $('.update_attach_class').removeAttr('disabled').css('border', '1px solid #3B57EE');;
            $('.update_url').attr('disabled', 'disabled').css('border', '1px solid #d4d4d4');
        });
    });

    Dropzone.autoDiscover = false;
    $(function() {
        // var myDropzone = new Dropzone("#upload-popup");
		var myDropzone = new Dropzone("#upload-popup", {
			// acceptedFiles: ".jpg,.jpeg,.bmp,.gif,.png,.txt,.doc,.xls,.pdf,.docx,.xlsx,.ppt,.pps,.pptx,.csv,.xlsm,.msg",
			acceptedFiles: "",
		});
        myDropzone.on("queuecomplete", function(file) {
            id = $('#UploadId').val();
            $.ajax({
                url: '/kanban/getTaskAttachment/'+ id,
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    _html = '<ul>';
                    if (data['attachments']) {
                        $.each(data['attachments'], function(ind, _data) {
							var attach_id = _data['ProjectTaskAttachment']['id'] ? _data['ProjectTaskAttachment']['id'] : 0;
							var task_id = _data['ProjectTaskAttachment']['task_id'] ? _data['ProjectTaskAttachment']['task_id'] : 0;
                            if(_data['ProjectTaskAttachment']['is_file'] == 1){
                                if((/\.(gif|jpg|jpeg|tiff|png)$/i).test(_data['ProjectTaskAttachment']['attachment'])){ 
                                    _html += '<li><i class="icon-paper-clip"></i><span class="file-name" onclick="openFileAttach.call(this);" data-id = "'+ attach_id +'">'+ _data['ProjectTaskAttachment']['attachment'] +'</span><a  data-id = "'+ attach_id +'"><img src="/img/new-icon/delete-attachment.png" alt="'+ attach_id +'" data-id = "'+ task_id +'" onclick="deleteAttachmentFile.call(this)"></a></li>';
                                }else{
                                    _link = '/kanban/attachment/'+ attach_id +'/?sid='+ api_key;
                                    _html += '<li><i class="icon-paper-clip"></i><a class="file-name"  href="'+ _link +'">'+ _data['ProjectTaskAttachment']['attachment'] +'</a><a  data-id = "'+ attach_id +'"><img src="/img/new-icon/delete-attachment.png" alt="'+ attach_id +'" data-id = "'+ task_id +'" onclick="deleteAttachmentFile.call(this)"></a></li>';
                                }
                            }else{
                                _html += '<li><i class="icon-link"></i><a class="file-name" target="_blank" href="'+ _data['ProjectTaskAttachment']['attachment'] +'">'+ _data['ProjectTaskAttachment']['attachment'] +'</a><a  data-id = "'+ attach_id +'"><img src="/img/new-icon/delete-attachment.png" alt="'+ attach_id +'" data-id = "'+ task_id +'" onclick="deleteAttachmentFile.call(this)"></a></li>';
                            }
                        });
                    }
                    _html += '</ul>';
                    $('#content_comment .append-comment').find('ul').empty();  
                    $('#content_comment .append-comment').append(_html);

                }
            });
        });
        myDropzone.on("success", function(file) {
            myDropzone.removeFile(file);
        });
        $('#UploadIndexForm').on('submit', function(e){
            $('#UploadIndexForm').parent('.wd-popup').addClass('loading');
            // return;
            if(myDropzone.files.length){
                e.preventDefault();
                popupDropzone.processQueue();
            }
        });
        myDropzone.on('sending', function(file, xhr, formData) {
            // Append all form inputs to the formData Dropzone will POST
            var data = $('#UploadIndexForm').serializeArray();
            $.each(data, function(key, el) {
                formData.append(el.name, el.value);
            });
        });
    });
 
    setTimeout(function(){
        cleanFilter();
    }, 3000);
    function cleanFilter(){
        var check = false;
        if($('.type-focus').length > 0 || $('.has-filter').length > 0 || $('.x-icon-filtered').length > 0){
            check = true;
        }
        if(check === true){
            $('#clean-filters').removeClass('hidden');
        } else {
            $('#clean-filters').addClass('hidden');
        }
    }
    $('.wd-check-box').on('click', function(){
        var _sp = $(this).find('span');
        _sp.toggleClass('checked');
        $(this).find('input.checkbox').val( parseInt(_sp.hasClass('checked') ? '1' : '0'));

        $(this).find('input.checkbox').trigger('onchange');
//        console.log($(this).find('input.checkbox').val(), 'onchange');
    });
    $(window).on(' ready resize', function(){
        $('.x-tree-icon-special').each(function(){
            var _this = $(this);
            _this.prev('.x-tree-elbow').addClass('x-tree-is_nct');
            // _this.hide();
        });
    });
    $("#ok_attach").on('click',function(){
        id = $('input[name="data[Upload][id]"]').data('id');
        url = $.trim($('input[name="data[Upload][url]"]').val());
        var form = $("#UploadIndexForm");
        if(url){
            form.submit();
        }
        
    });
    function initresizable(){
        var _max_height = 0;
        $('#GanttChartDIV .gantt-chart-wrapper >.gantt-primary >tbody >tr').each(function(){
            _max_height += $(this).is(":visible") ? $(this).height() : 0;
        });
        _min_height = Math.min(100,_max_height);
        if( _max_height < 235) $('#mcs1_container').css('height', _max_height);
        $('#mcs1_container').resizable({
            handles: "s",
            maxHeight: _max_height,
            minHeight: _min_height ,
            resize: function(e, ui){
                var _max_height = 0;
                $('#GanttChartDIV .gantt-chart-wrapper >.gantt-primary >tbody >tr').each(function(){
                    _max_height += $(this).is(":visible") ? $(this).height() : 0;
                });
                _min_height = Math.min(235,_max_height);
                $('#mcs1_container').resizable("option", 'maxHeight', _max_height);
				gantt_height = $('#GanttChartDIV').height();

            }
        });
        $(window).trigger('resize');

    }
    function destroyresizable(){
        $('#mcs1_container').resizable("destroy");
        $('#mcs1_container').css({
            width: '',
            height:''
        });
    }
	initresizable();
   
	//QuanNV
	
	$('#open-modal').on('click', function(e){
		c_task_data = {};
		$('#addProjectTemplate').toggleClass('open');
		$(window).trigger('resize');
		if( $('#addProjectTemplate').hasClass('open') && !$('#addProjectTemplate').hasClass('loaded') && !$('#addProjectTemplate').hasClass('loading')){
			$('#addProjectTemplate').addClass('loading');
			$.ajax({
				url : "/project_tasks_preview/add_task_popup/" + project_id,
				type: "GET",
				cache: false,
				success: function (html) {
					$('#addProjectTemplate').empty().append($(html));
					$(window).trigger('resize');
					$('#addProjectTemplate').addClass('loaded');
					$('#addProjectTemplate').removeClass('loading');
					$('input[data-return="form-return"]').val('<?php echo $this->here;?>');
					var popup_width = show_workload ? 1080 : 580;
					show_full_popup( '#template_add_task', {width: popup_width}, false);
					// $('#template_add_task').find('input, select').trigger('change');
					$('#toPhase').trigger('change');
					popupnct_nct_list_assignedonChange('popupnct_nct_list_assigned');
				},
				complete: function(){
					$('#special-task-info-2').find('.loading-mark:first').removeClass('loading');
					ajaxGetResources(project_id);
				}
			});
		}
		if( $('#addProjectTemplate').hasClass('loaded') ){
			var popup_width = show_workload ? 1080 : 580;
			show_full_popup( '#template_add_task', {width: popup_width},false);
			// $('#template_add_task').find('input, select').trigger('change');
			ajaxGetResources(project_id);
		}
	});
	
</script>

<div id="collapse" style="padding:4px; cursor:pointer; background-color:#FFF; display:none; position: fixed; top:0; right:0; z-index:9999999999" onclick="collapseTaskScreen();" >
    <button class="btn btn-esc"></button>
</div>
<?php if($canModified){ ?> 
<div id="addProjectTemplate" class="loading-mark loaded">
	<div class="add-popup-container">
		<div id="template_add_task" class="wd-full-popup" style="display: none;">
			<div class="wd-popup-inner">
				<div class="template-popup loading-mark wd-popup-container"  id="tab-popup-container">
					<div class="wd-popup-head clearfix">
						<ul class="tabPopup" style="margin: 0; padding: 0;">
							<?php if( !empty($canModified)) { ?>
								<li class="liPopup" ><a href="#NewTask"> <?php __('Add new task');?>  </a></li>
							<?php } ?> 
						</ul>
						<a href="javascript:void(0);" class="close_template_add_task wd-close-popup" onclick="cancel_popup(this, false)"><img title="<?php __('Close');?>" src="<?php echo $this->Html->url('/img/new-icon/close.png');?>"></a>
					</div>
					<div class="template-popup-content wd-popup-content">
						
						<!--
						-- Create New Task	
						-->
						
						<?php if( !empty($canModified)) { ?>
						<div id="NewTask" class="popup-tab">
							<?php 
							echo $this->Form->create('ProjectTask', array(
								'type' => 'POST',
								'url' => array('controller' => 'project_tasks_preview', 'action' => 'add_new_task_popup'),
								'class' => 'form-style-2019',
								'data-reload' => 0,
								'id' => 'ProjectTaskAddPopupForm'
							));
							?>
							<div class="form-head clearfix">
								<h4 class="desc"><?php __('Task Description'); ?></h4>
								<?php if( empty($companyConfigs['create_ntc_task']) ) {?>
									<a href="javascript:void(0);" class="right-link" onclick="cancel_popup(this, false); show_full_popup('#template_add_nct_task', {width: 'inherit'}, false);">
										<?php __('Add a non-continuous task');?>
									</a>
								<?php }?>
							</div>
							<p style="color: #217FC2" class="form-message"></p>
							<p style="color: red" class="alert-message"></p>
							<?php  
							if( $show_workload) { ?>
								<div class="wd-row">
									<div class="wd-col wd-col-sm-8">
							<?php } 
							echo $this->Form->input('return', array(
								'data-return' => 'form-return',
								'type'=> 'hidden',
								'value' => $this->Html->url(),
								
							));
							echo $this->Form->input('run_staffing', array(
								'data-return' => 'form-return',
								'type'=> 'hidden',
								'value' => 1,
								
							));
							echo $this->Form->input('project_id', array(
								'type'=> 'hidden',
								'rel' => 'no-history',
								'value' => $project_id
							));
							echo $this->Form->input('project_planed_phase_id', array(
								'type'=> 'select',
								'id' => 'toPhase',
								'label' => __('Phase', true),
								'options' => $phases,
								'required' => 'required',
								'rel' => 'no-history',
								'onchange' => 'popupTask_phaseOnChange(this);',
								'div' => array(
									'class' => 'wd-input label-inline required has-val'
								)
							));
							echo $this->Form->input('task_title', array(
								'type'=> 'text',
								'id' => 'newTaskName',
								'label' => __('Task name', true),
								'required' => 'required',
								'rel' => 'no-history',
								'div' => array(
									'class' => 'wd-input label-inline'
								)
							));
							?>
							<div class="wd-row">
								<div class="wd-col wd-col-sm-6">
									<div class="wd-input wd-area wd-none">
										<!-- <label><?php __('Assign to');?></label> -->
										<div class="wd-multiselect multiselect popupnct_nct_list_assigned loading" id="popupnct_nct_list_assigned">
											<a href="javascript:void(0);" class="wd-combobox wd-project-manager disable">
												<p>
													<?php  __d(sprintf($_domain, 'Project_Task'), 'Assigned To');?>
												</p>
											</a>
											<div class="wd-combobox-content task_assign_to_id" style="display:none;">
												<div class="context-menu-filter">
													<span>
														<input type="text" class="wd-input-search" placeholder="<?php __('Search...');?>" rel="no-history">
													</span>
												</div>
												<div class="option-content"></div>
											</div>
										</div>
									</div>
								</div>
								<div class="wd-col wd-col-sm-6">
								<?php 
									echo $this->Form->input('task_status_id', array(
										'type'=> 'select',
										'id' => 'newTaskStatus',
										'label' => __d(sprintf($_domain, 'Project_Task'), 'Status', true),
										'options' => $listAllStatus,
										'required' => 'required',
										'rel' => 'no-history',
										'div' => array(
											'class' => 'wd-input label-inline has-val'
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
											'id' => 'newTaskStartDay',
											'label' => __d(sprintf($_domain, 'Project_Task'), 'Start date', true),
											// 'required' => 'required',
											'class' => 'wd-date',
											'onchange'=> 'newTask_validated(this);',
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
										'id' => 'newTaskEndDay',
										'label' => __d(sprintf($_domain, 'Project_Task'), 'End date', true),
										// 'required' => 'required',
										'class' => 'wd-date',
										'autocomplete' => 'off',
										'onchange'=> 'newTask_validated(this);',
										'rel' => 'no-history',
										'div' => array(
											'class' => 'wd-input label-inline'
										)
									));
									?>
								</div>
							</div>
							<div id="popup_task_template_attach" >
								<div class="heading">
								</div> 
								<div class="trigger-upload">
									<div id="wd-task-upload-popup" method="post" action="<?php echo $this->Html->url(array('controller' => 'project_tasks_preview', 'action' => 'add_new_task_popup')); ?>" class="dropzone" value="" >
									</div>
								</div>
							</div>
							<?php if( $show_workload) { ?>
								</div>
								<div class="wd-col wd-col-sm-4">
									<div class="task-workload">
										<table id="task_assign_table" class="nct-assign-table">
											<thead>
												<tr>
													<td width = 120 class="bold base-cell null-cell"><?php __('Resources');?> </td>
													<td width = 120 class="bold base-cell null-cell"><?php  __d(sprintf($_domain, 'Project_Task'), 'Workload')?></td>
													<?php if( !empty($showDisponibility)){ ?>
													<td width = 120 class="bold base-cell null-cell"><?php __('M.D availability');?> </td>
												<?php } ?>
												</tr>
											</thead>
											<tbody>
												
											</tbody>
											<tfoot>
												<tr>
													<td class="base-cell"><?php __('Total') ?></td>
													<td class="base-cell total-consumed" id="total-consumed">0</td>
													<?php if( !empty($showDisponibility)){ ?>
														<td class="base-cell total-manday"></td>
													<?php } ?>
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
											<span><?php __('create your task') ?></span>
										</button>
										<a class="btn-form-action btn-cancel" id="reset_button" href="javascript:void(0);" onclick="cancel_popup(this, false);">
											<?php echo __("Cancel", true); ?>
										</a>
									</div>
								</div>
							</div>
							<?php echo $this->Form->end(); ?>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<!--
		-- Create New NCT Task	
		-->
		<div id="template_add_nct_task" class="wd-full-popup autosize-popup" style="display: none;" >
			<div class="wd-popup-inner">
				<div class="add_nct_task-popup loading-mark wd-popup-container">
					<div class="wd-popup-head clearfix">
						<div class="popup-back"><a href="javascript:void(0);" onclick="back_to_last_popup(this);"><i class="icon-arrow-left"></i><span> <?php __('Back');?> </span></a></div>
						
						<a style="right: 70px;height: 70px;position: absolute;padding: 25px;" target="_blank" href="<?php echo $this->Html->url('/guides/tache_non_continue') ?>">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
							<defs>
							<style>
								.cls-1 {
									fill: #666;
									fill-rule: evenodd;
								}
							</style>
							</defs>
							<path id="help" class="cls-1" d="M1960,40a10,10,0,1,1,10-10A10,10,0,0,1,1960,40Zm0-18.667A8.667,8.667,0,1,0,1968.66,30,8.667,8.667,0,0,0,1960,21.333Zm2.04,8.192q-0.15.146-.39,0.374c-0.16.152-.3,0.284-0.41,0.4a2.539,2.539,0,0,0-.27.286,1.379,1.379,0,0,0-.27.909v0.66h-1.66V31.255a2.13,2.13,0,0,1,.14-0.873,3.544,3.544,0,0,1,.61-0.755l1.07-1.07a1.272,1.272,0,0,0,.34-0.909,1.255,1.255,0,0,0-.35-0.9,1.231,1.231,0,0,0-.91-0.359,1.325,1.325,0,0,0-.93.344,1.347,1.347,0,0,0-.43.917h-1.78a3.024,3.024,0,0,1,1.02-2.046,3.251,3.251,0,0,1,2.18-.741,3.1,3.1,0,0,1,2.12.711,2.488,2.488,0,0,1,.83,1.988,2.246,2.246,0,0,1-.49,1.467C1962.28,29.26,1962.13,29.427,1962.04,29.525Zm-2.15,3.71a1.14,1.14,0,0,1,.8.315,1.027,1.027,0,0,1,.34.763,1.048,1.048,0,0,1-.34.77,1.084,1.084,0,0,1-.79.323,1.136,1.136,0,0,1-.8-0.316,1.015,1.015,0,0,1-.33-0.762,1.04,1.04,0,0,1,.33-0.77A1.07,1.07,0,0,1,1959.89,33.235Z" transform="translate(-1950 -20)"/>
							</svg>
						</a>
						
						<a href="javascript:void(0);" class="close_template_add_nct_task wd-close-popup" onclick="cancel_popup(this, false)"><img title="<?php __('Close');?>" src="<?php echo $this->Html->url('/img/new-icon/close.png');?>"></a>
					</div>
					<div class="add_nct_task-popup-content wd-popup-content">
						<?php
						echo $this->Form->create('NCTTask', array(
								'type' => 'POST',
								'url' => array('controller' => 'project_tasks', 'action' => 'saveNcTask'),
								'class' => 'form-style-2019',
								'id' => 'NCTTaskAddPopupForm',
								'data-reload' => 0,
							));
						
						?>
						<p style="color: #217FC2" class="form-message"></p>
						<p style="color: red" class="alert-message"></p>
						<div class="wd-row wd-row-inline">
							<div class="wd-inline-col wd-col-530">
								<?php
								echo $this->Form->input('return', array(
									'data-return' => 'form-return',
									'type'=> 'hidden',
									'value' => $this->Html->url(),
									
								));
								echo $this->Form->input('run_staffing', array(
									'data-return' => 'form-return',
									'type'=> 'hidden',
									'value' => 1,
									
								));
								echo $this->Form->input('id', array(
									'type'=> 'hidden',
									'rel' => 'no-history',
									'value' => $project_id
								));
								// $phases = !empty($listPhases) ? Set::combine($listPhases, '{n}.id', '{n}.name') : array();
								echo $this->Form->input('task.id', array(
									'type'=> 'hidden',
									'id' => 'popupnct-id',
									'rel' => 'no-history',
									'value' => 0,
								));
								echo $this->Form->input('task.project_planed_phase_id', array(
									'type'=> 'select',
									'id' => 'popupnct-phase',
									'label' => __('Phase', true),
									'options' => $phases,
									'required' => 'required',
									'onchange'=> 'nct_phaseOnChange(this);',
									'rel' => 'no-history',
									'div' => array(
										'class' => 'wd-input label-inline required has-val'
									)
								));
								echo $this->Form->input('task.task_title', array(
									'type'=> 'text',
									'id' => 'popupnct-name',
									'label' => __('Task name', true),
									'required' => 'required',
									'rel' => 'no-history',
									'div' => array(
										'class' => 'wd-input label-inline required'
									)
								));
								?>
								<div class="wd-input wd-area wd-none required">
									<div class="wd-multiselect multiselect popupnct_nct_list_assigned loading" id="multiselect-popupnct-pm">
										<a href="javascript:void(0);" class="wd-combobox wd-project-manager disable">
											<p>
												<?php  __d(sprintf($_domain, 'Project_Task'), 'Assigned To');?>
											</p>
										</a>
										<div class="wd-combobox-content task_assign_to_id" style="display:none;">
											<div class="context-menu-filter">
												<span>
													<input type="text" class="wd-input-search" placeholder="<?php __('Search...');?>" rel="no-history">
												</span>
											</div>
											<div class="option-content"></div>
										</div>
									</div>
								</div>
								<div class="wd-row">
									<div class="wd-col wd-col-sm-6">
										<?php 
										echo $this->Form->input('task.task_status_id', array(
											'type'=> 'select',
											'id' => 'popupnct-status',
											'label' => __d(sprintf($_domain, 'Project_Task'), 'Status', true),
											'options' => $listAllStatus,
											'required' => 'required',
											'rel' => 'no-history',
											'div' => array(
												'class' => 'wd-input label-inline required has-val'
											)
										));
										
										?>
									</div>
									<div class="wd-col wd-col-sm-6">
									<?php 
										$periods = array( __('Day',true), __('Week',true), __('Month',true)/* , __('Period',true )*/);
										echo $this->Form->input('type', array(
											'type'=> 'select',
											'id' => 'popupnct-range-type',
											'label' => __('Period', true),
											'options' => $periods,
											'required' => 'required',
											'rel' => 'no-history',
											'selected' => 2,
											'div' => array(
												'class' => 'wd-input label-inline has-val required'
											)
										));
									?>
									</div>
								</div>
								<div class="wd-row">
									<div class="wd-col wd-col-sm-6">
										<div class="wd-input wd-area wd-none">
										<?php 				
											echo $this->Form->input('task.task_start_date', array(
												'type'=> 'text',
												'id' => 'popupnct-start-date',
												'label' => __d(sprintf($_domain, 'Project_Task'), 'Start date', true),
												// 'required' => 'required',
												'class' => 'wd-date',
												'onchange'=> 'popupnct_validated(this);',
												'autocomplete' => 'off',
												'rel' => 'no-history',
												'div' => array(
													'class' => 'wd-input label-inline required'
												)
											));
											?>
										</div>
									</div>
									<div class="wd-col wd-col-sm-6">
										<div class="input-icon-container">
											<?php 				
											echo $this->Form->input('task.task_end_date', array(
												'type'=> 'text',
												'id' => 'popupnct-end-date',
												'label' => __d(sprintf($_domain, 'Project_Task'), 'End date', true),
												// 'required' => 'required',
												'class' => 'wd-date',
												'autocomplete' => 'off',
												'onchange'=> 'popupnct_validated(this);',
												'rel' => 'no-history',
												'div' => array(
													'class' => 'wd-input label-inline required'
												)
											));
											?>
											<div class="wd-icon">
												<a href="javascript:void(0);" id="popupnct_create-range" class="btn"><i class="icon-reload"></i></a>
											</div>
										</div>
									</div>
								</div>
								<div class="wd-row">
									<div class="wd-col wd-col-lg-6">
										<?php 
										if( !empty($adminTaskSetting['Profile'])){
											echo $this->Form->input('task.profile_id', array(
												'type'=> 'select',
												'id' => 'popupnct-profile',
												'label' => __d(sprintf($_domain, 'Project_Task'), 'Profile', true),
												'options' => $projectProfiles,
												'required' => false,
												'rel' => 'no-history',
												'empty' => '',
												'div' => array(
													'class' => 'wd-input label-inline'
												)
											));
										}
										if( !empty($adminTaskSetting['Priority'])){
											echo $this->Form->input('task.task_priority_id', array(
												'type'=> 'select',
												'id' => 'popupnct-priority',
												'label' => __d(sprintf($_domain, 'Project_Task'), 'Priority', true),
												'options' => $projectPriorities,
												'required' => false,
												'rel' => 'no-history',
												'empty' => '',
												'div' => array(
													'class' => 'wd-input label-inline'
												)
											));
										}
										if( !empty($adminTaskSetting['Milestone'])){
											echo $this->Form->input('task.milestone_id', array(
												'type'=> 'select',
												'id' => 'popupnct-milestone',
												'label' => __('Milestone', true),
												'options' => '',
												'required' => false,
												'rel' => 'no-history',
												'empty' => '',
												'div' => array(
													'class' => 'wd-input label-inline'
												)
											));
										}
										if( !empty($adminTaskSetting['Unit Price'])){ 
											echo $this->Form->input('task.unit_price', array(
												'type'=> 'text',
												'id' => 'popupnct-unit-price',
												'label' => __d(sprintf($_domain, 'Project_Task'), 'Unit Price', true),
												'required' => false,
												'rel' => 'no-history',
												'div' => array(
													'class' => 'wd-input label-inline'
												)
											));
										} ?>
										<div class="input-icon-container">
											<?php
											echo $this->Form->input('popupnct_per-workload', array(
												'type'=> 'text',
												'id' => 'popupnct_per-workload',
												'label' => __d(sprintf($_domain, 'Project_Task'), 'Workload', true),
												'required' => false,
												'autocomplete' => 'off',
												'rel' => 'no-history',
												'div' => array(
													'class' => 'wd-input label-inline'
												)
											));
											?>
											<div class="wd-icon">
												<a href="javascript:void(0);" id="popupnct_fill-workload" class="btn">
													<i class="icon-reload"></i>
												</a>
											</div>
										</div>
									</div>
									<div class="wd-col wd-col-lg-6 selectDate-container">
										<div class="input-icon-container">
											<div class="wd-input-group">
												<div class="period-input" style="display: none;">
													<?php
													echo $this->Form->input('period_start_date', array(
														'type'=> 'text',
														'id' => 'popupnct-period-start-date',
														'label' => __d(sprintf($_domain, 'Project_Task'), 'Start date', true),
														'required' => false,
														'class' => 'wd-date text popupnct-period-input-calendar popupnct-date-period',
														'autocomplete' => 'off',
														'rel' => 'no-history',
														'div' => array(
															'class' => 'wd-input label-inline'
														)
													));
													echo $this->Form->input('period_end_date', array(
														'type'=> 'text',
														'id' => 'popupnct-period-end-date',
														'label' => __d(sprintf($_domain, 'Project_Task'), 'End date', true),
														'required' => false,
														'class' => 'wd-date text popupnct-period-input-calendar popupnct-date-period',
														'autocomplete' => 'off',
														'rel' => 'no-history',
														'div' => array(
															'class' => 'wd-input label-inline'
														)
													));
													?>
												</div>
											</div>
										</div>
										<div class="input-icon-container" id="popupnct-range-picker-container">
											<div id="popupnct-range-picker" class="popupnct-range-picker"></div>
											<div class="wd-input-group">
											</div>
											<div class="wd-icon">
												<a id="popupnct_add-range" href="javascript:;" class="btn-text popupnct-range-picker">
													<i class="icon-reload"></i>
												</a>
											</div>
										</div>
										<div class="input-icon-container" id="popupnct_date-list-container">
											<div class="wd-input-group">
												<div id="popupnct_date-list" class="nct-date-start-end"></div>
											</div>
											<div class="wd-icon">
												<a id="popupnct_add-date" href="javascript:;" class="btn-text date-picker">
													<i class="icon-reload"></i>
												</a>
											</div>
										</div>
									</div>
								</div>
								<div id="popup_nct_template_attach" class="wd-hide">
									<div class="heading">
									</div> 
									<div class="trigger-upload">
										<div id="wd-popupnct-upload-popup" method="post" action="<?php echo $this->Html->url(array('controller' => 'project_tasks', 'action' => 'saveNcTask')); ?>" class="dropzone" value="" >
										</div>
									</div>
								</div>
							</div>
							
							<div class="wd-inline-col wd-col-autosize">
								<div id="popupnct_assign-list">
									<table id="popupnct-assign-table" class="nct-assign-table">
										<thead>
											<tr>
												<td class="bold base-cell null-cell">&nbsp;</td>
												<td class="base-cell" id="popupnct_consumed-column" style="vertical-align: middle;"><?php __('Consumed'); ?> (<?php __('In Used'); ?>)</td>
												<td class="base-cell row-action"></td>
											</tr>
										</thead>
										<tbody>
										</tbody>
										<tfoot>
											<tr>
												<td class="base-cell"><?php __('Total') ?></td>
												<td class="base-cell" id="popupnct_total-consumed">0</td>
												<td class="base-cell row-action"></td>
											</tr>
										</tfoot>
									</table>
								</div>
							</div>
						</div>
						<div class="wd-row wd-submit-row">
							<div class="wd-col-xs-12">
								<div class="wd-submit">
									<button type="submit" class="btn-form-action btn-ok btn-right" id="btnNCTSave">
										<span><?php __('create your task') ?></span>
									</button>
									<a class="btn-form-action btn-cancel" id="nct_reset_button" href="javascript:void(0);" onclick="cancel_popup(this,false);">
										<?php echo __("Cancel", true); ?></span>
									</a>
								</div>
							</div>
						</div>
						<?php echo $this->Form->end(); ?>
					</div>
				</div>
			</div>
		</div>
	
		<!-- Edit task Popup -->
		<div id="template_edit_task" class="wd-full-popup autosize-popup" style="display: none;">
			<div class="wd-popup-inner">
				<div class="edit_task-popup loading-mark wd-popup-container">
					<div class="wd-popup-head clearfix"> 
						<h4 class="active">
							<?php __('Edit task');?>
						</h4> 
						<a href="javascript:void(0);" class="close_template_add_task wd-close-popup" onclick="cancel_popup(this, false)"><img title="<?php __('Close');?>" src="<?php echo $this->Html->url(array(
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
							'id' => 'ProjectTaskEditForm',
							)							
						);
						?>
						<p style="color: red" class="alert-message"></p>
						<?php 
						if( $show_workload) { ?>
						<div class="wd-row wd-form-data-row wd-row-inline">
							<div class="wd-inline-col wd-col-530">
						<?php } 
						// echo $this->Form->input('return', array(
							// 'type'=> 'hidden',
							// 'value' => $this->Html->url(),
							
						// ));
						echo $this->Form->input('id', array(
							'type'=> 'hidden',
							'value' => '',
							'id' =>'editTaskID'
						));
						echo $this->Form->input('task_title', array(
							'type'=> 'text',
							'id' => 'task_title',
							'label' => __d(sprintf($_domain, 'Project_Task'), 'Task', true),
							'required' => true,
							'rel' => 'no-history',
							'div' => array(
								'class' => 'wd-input label-inline'
							)
						));
						echo $this->Form->input('project_planed_phase_id', array(
							'type'=> 'select',
							'id' => 'edit_normal-Phase',
							'label' => __('Phase', true),
							'options' => $phases,
							'required' => true,
							'rel' => 'no-history',
							'div' => array(
								'class' => 'wd-input'
							)					
						));
						?>
						<div class="wd-row1">
							<div class="wd-col wd-col-sm-6" style="width:45%;padding: unset;text-align: left;">
								<div class="wd-multiselect multiselect list_task_assign_to_edit" id="list_task_assign_to_edit">
									<a href="javascript:void(0);" class="wd-combobox wd-project-manager">
										<p>
											<?php  __d(sprintf($_domain, 'Project_Task'), 'Assigned To');?>
										</p>
									</a>
									<div class="wd-combobox-content task_assign_to_id" style="display:none;">
										<div class="context-menu-filter">
											<span>
												<input type="text" class="wd-input-search" placeholder="<?php __('Search...');?>" rel="no-history">
											</span>
										</div>
										<div class="option-content">
										</div>
									</div>
								</div>
							</div>
							<div class="wd-col wd-col-sm-6" style="width:45%;padding:unset;float:right;text-align: left;">
							<?php 
								$list_status = !(empty($priority['Statuses'])) ? Set::combine($priority['Statuses'], '{n}.ProjectStatus.id', '{n}.ProjectStatus.name') : array();
								echo $this->Form->input('task_status_id', array(
									'type'=> 'select',
									'id' => 'edit-normal-status',
									'label' => __d(sprintf($_domain, 'Project_Task'), 'Status', true),
									'options' => $list_status,
									'required' => true,
									'rel' => 'no-history',
									'div' => array(
										'class' => 'wd-input label-inline required has-val'
									)
								));
								?>
							</div>
						</div>
						<div class="wd-row1">
							<div class="wd-col wd-col-sm-6 wd-startdate" style="width:45%;padding: unset;text-align: left;">
								<div class="wd-input wd-area wd-none">
								<?php 				
									echo $this->Form->input('task_start_date', array(
										'type'=> 'text',
										'id' => 'editTaskStartDay',
										'label' => __d(sprintf($_domain, 'Project_Task'), 'Start date', true),
										// 'required' => true,
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
							
							<?php 	
								if( $show_duration ){
									echo $this->Form->input('duration_of_task', array(
										'id' => 'durationOfTask',
										'label' => __d(sprintf($_domain, 'Project_Task', true), 'Duration', true),
										'value' => 0,
										'rel' => 'no-history',
										'onchange'=> 'editDuration(this);',
										'div' => array(
											'class' => 'wd-input label-inline display_duration',
											'title' =>  __d(sprintf($_domain, 'Project_Task', true), 'Duration', true),
										)
									));
								}
							?>
							<div class="wd-col wd-col-sm-6 wd-enddate">
							<?php 				
								echo $this->Form->input('task_end_date', array(
									'type'=> 'text',
									'id' => 'editTaskEndDay',
									'label' =>  __d(sprintf($_domain, 'Project_Task'), 'End date', true),
									// 'required' => true,
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
						<div class="wd-row1">
							<div class="wd-col wd-col-sm-6" style="width:45%;padding: unset;text-align: left;">
								<?php 				
									if( $display_manualConsumed){
										echo $this->Form->input('manual_consumed', array(
											'id' => 'edit_normal-manual_consumed',
											'label' => __d(sprintf($_domain, 'Project_Task'), 'Manual Consumed', true),
											'value' => 0,
											// 'disabled' => true,
											'required' => false,
											'rel' => 'no-history',
											'div' => array(
												'class' => 'wd-input label-inline'
											)					
										));
									}
									$style_milestone = $display_manualConsumed ? 'float:right;' : '';
								?>
							</div>
							<div class="wd-col wd-col-sm-6" style="text-align: left;width:45%;padding: unset;<?php echo $style_milestone;?>">
								<?php 	
									if( $display_milestone ){
										echo $this->Form->input('milestone_of_task', array(
											'type'=> 'select',
											'id' => 'milestoneOfTask',
											'label' => __d(sprintf($_domain, 'Project_Task', true), 'Milestone', true),
											'rel' => 'no-history',
											'empty' => '',
											'options' => $listMilestoneTask,
											'div' => array(
												'class' => 'wd-input label-inline required'
											)
										));
									}
								?>
							</div>
						</div>
						<?php 	
							// if( $show_duration ){
								// echo $this->Form->input('duration_of_task', array(
									// 'id' => 'durationOfTask',
									// 'label' => __d(sprintf($_domain, 'Project_Task', true), 'Duration', true),
									// 'value' => 0,
									// 'rel' => 'no-history',
									// 'onchange'=> 'editDuration(this);',
									// 'div' => array(
										// 'class' => 'wd-input label-inline display_duration',
										// 'title' =>  __d(sprintf($_domain, 'Project_Task', true), 'Duration', true),
									// )
								// ));
							// }
						?>
						<?php if( $show_workload) { ?>
							</div>
							<div class="wd-inline-col wd-col-autosize">
								<div class="task-workload">
									<table id="edit_task_assign_table" class="nct-assign-table">
										<thead>
											<tr>
												<td width = 120 class="bold base-cell null-cell"><?php __('Resources');?> </td>
												<td width = 120 class="bold base-cell null-cell"><?php  __d(sprintf($_domain, 'Project_Task'), 'Workload')?></td>
												<?php if( empty($companyConfigs['manual_consumed']) && !empty($adminTaskSetting['Consumed']) ){ ?>
													<td width = 120 class="bold base-cell null-cell "><?php  __d(sprintf($_domain, 'Project_Task'), 'Consumed')?></td>
												<?php } ?> 
												<?php if( !empty($showDisponibility)){ ?>
													<td width = 120 class="bold base-cell null-cell"><?php __('M.D availability');?> </td>
												<?php } ?>
											</tr>
										</thead>
										<tbody>
											
										</tbody>
										<tfoot>
											<tr>
												<td class="base-cell"><?php __('Total') ?></td>
												<td class="base-cell total-workload" id="edit_task_total_workload">0</td>
												<?php if( empty($companyConfigs['manual_consumed']) && !empty($adminTaskSetting['Consumed'])){ ?>
													<td class="base-cell total-consumed" id="edit_task_total_consumed">0</td>
												<?php } ?>
												<?php if( !empty($showDisponibility)){ ?>
													<td class="base-cell total-manday" id="edit_task_total_manday"></td>
												<?php } ?>
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
									<button type="submit" class="btn-form-action btn-ok btn-right" id="btnEditNormalSave">
										<span><?php __('Save') ?></span>
									</button>
									<a class="btn-form-action btn-cancel" id="btnEditNormalCancel" href="javascript:void(0);" onclick="cancel_popup(this, false);">
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
		
	
		<!-- Batch Edit task Popup -->
		<div id="template_batch_edit_task" class="wd-full-popup fullwidth-popup" style="display: none;">
			<div class="wd-popup-inner">
				<div class="batch-edit-task-popup wd-edge-popup-container wd-bottom-popup">
					<div class="edit_task-popup-content wd-popup-content">
						<!-- form -->
						<?php 
						echo $this->Form->create('ProjectTask', array(
							'type' => 'POST',
							'url' => array('controller' => 'project_tasks', 'action' => 'batchUpdateTasksByProject', $project_id),
							'class' => 'form-style-2019',
							'id' => 'ProjectTaskBatchEditForm',
							)							
						);
						$_col = $show_workload ? 'wd-col wd-col-md-6 wd-col-lg-4' : 'wd-col wd-col-md-6 wd-col-lg-3';
						// $_col = 'wd-col wd-col-lg-2';
						$_colAsssigned = $show_workload ? 'wd-col wd-col-md-6 wd-col-lg-4' : 'wd-col wd-col-lg-6';
						?>
						<p style="color: red" class="alert-message"></p>
						<div class="hidden hidden-data" style="display: none">
							<div class="batch-edit-task-list-id"></div>
						</div>
						<div class="wd-row wd-form-data-row">
							<div class="<?php echo $_col;?>">
								<?php 
								$_list_status = !(empty($priority['Statuses'])) ? Set::combine($priority['Statuses'], '{n}.ProjectStatus.id', '{n}.ProjectStatus.name') : array();
								$_list_status['0'] = __('Keep current status', true);
								echo $this->Form->input('task_status_id', array(
									'type'=> 'select',
									'id' => 'batchEditTaskStatus',
									'label' => __d(sprintf($_domain, 'Project_Task'), 'Status', true),
									'options' => $_list_status,
									'default' => 0,
									// 'required' => true,
									'rel' => 'no-history',
									'div' => array(
										'class' => 'wd-input label-inline has-val'
									),
								));
								?>
							</div>
							<div class="<?php echo $show_workload ? 'wd-col wd-col-md-6 wd-col-lg-8' : 'wd-col wd-col-md-6 wd-col-lg-6';?>">
								<div class="wd-row">
									<div class="wd-col wd-col-md-6">
										<div class="wd-input wd-area wd-none">
										<?php 				
											echo $this->Form->input('task_start_date', array(
												'type'=> 'text',
												'id' => 'batchEditTaskStartDay',
												'label' => __d(sprintf($_domain, 'Project_Task'), 'Start date', true),
												// 'required' => true,
												'class' => 'wd-date',
												// 'onchange'=> 'editTaskValidated(this);',
												'autocomplete' => 'off',
												'rel' => 'no-history',
												'div' => array(
													'class' => 'wd-input label-inline has-val'
												)
												
											));
											?>
										</div>
									</div>
									<div class="wd-col wd-col-md-6">
										<div class="wd-input wd-area wd-none">
										<?php 				
										echo $this->Form->input('task_end_date', array(
											'type'=> 'text',
											'id' => 'batchEditTaskEndDay',
											'label' =>  __d(sprintf($_domain, 'Project_Task'), 'End date', true),
											// 'required' => true,
											'class' => 'wd-date',
											'autocomplete' => 'off',
											// 'onchange'=> 'editTaskValidated(this);',
											'rel' => 'no-history',
											'div' => array(
												'class' => 'wd-input label-inline has-val'
											)
											
										));
										?>
										</div>
									</div>
								</div>
								<div class="form-field-message for-wd-date">
									<p><?php echo __('Dates cannot be updated, a task has a predecessor', true);?></p>
								</div>
							</div>
							
							<?php if( !$show_workload) { ?>
								<div class="wd-col wd-col-lg-3">
									<div class="wd-multiselect multiselect batchEditAssignedto" id="batchEditAssignedto" data-name="data[ProjectTask][edit_assigned_to_id][]">
										<a href="javascript:void(0);" class="wd-combobox wd-project-manager">
											<p>
												<?php  __d(sprintf($_domain, 'Project_Task'), 'Assigned To');?>
											</p>
										</a>
										<div class="wd-combobox-content task_assign_to_id" style="display:none;">
											<div class="context-menu-filter">
												<span>
													<input type="text" class="wd-input-search" placeholder="<?php __('Search...');?>" rel="no-history">
												</span>
											</div>
											<div class="option-content">
											</div>
										</div>
									</div>
								</div>
							<?php } ?> 
						</div>
						<?php if( $show_workload) { ?>
						<div class="workload-group">
							<div class="radio-group inline-div">
								<div class="input wd-input wd-radio-button has-val">
									<input type="radio" name="data[ProjectTask][update_existing_employee]" id="batchUpdateAssigned" rel="no-history" value="1" style="display: none;" checked="checked">
									<label for="batchUpdateAssigned"><span class="wd-btn-switch"><span></span></span><?php echo __('Keep the existing resources and add new resources', true);?></label>
								</div>
								<div class="input wd-input wd-radio-button has-val">
									<input type="radio" name="data[ProjectTask][update_existing_employee]" id="batchReplaceAssigned" rel="no-history" value="0" style="display: none;">
									
									<label for="batchReplaceAssigned"><span class="wd-btn-switch"><span></span></span><?php echo __('Remove the resources assigned and replace by new resources', true);?></label>
								</div>
							</div>
							<div class="forBatchUpdate" id="forBatchUpdate">
								<div class="wd-row wd-form-data-row">
									<div class="wd-col wd-col-md-6 wd-col-lg-4">
									<?php echo $this->Form->input('update_estimated', array(
										'id' => 'batchUpdateWorkload',
										'label' => sprintf( __('%s (existing resources)', true),__d(sprintf($_domain, 'Project_Task'), 'Workload', true)),
										'value' => '',
										// 'disabled' => true,
										'required' => false,
										'rel' => 'no-history',
										'div' => array(
											'class' => 'wd-input label-inline'
										)					
									)); ?>
									</div>
									<div class="form-field-message for-batchUpdateWorkload">
										<p><?php echo __('All these users will have this workload', true);?></p>
									</div>
								</div>
								<div class="wd-input">
									<label for="batchAddAssigned"><span class="wd-btn-switch"><span></span></span><?php echo __('New resources', true);?></label>
								</div>
								<div class="wd-row wd-form-data-row">
									<div class="wd-col wd-col-md-6 wd-col-lg-4 col-right">
									<?php echo $this->Form->input('add_estimated', array(
										'id' => 'batchAddWorkload',
										'label' => sprintf( __('%s (new resources)', true), __d(sprintf($_domain, 'Project_Task'), 'Workload', true)),
										'value' => '',
										// 'disabled' => true,
										'required' => false,
										'rel' => 'no-history',
										'div' => array(
											'class' => 'wd-input label-inline'
										)					
									)); ?>
									</div>
									<div class="wd-col wd-col-md-6 wd-col-lg-8">
										<div class="wd-multiselect multiselect batchAddAssignedto" id="batchAddAssignedto" data-name="data[ProjectTask][add_assigned_to_id][]">
											<a href="javascript:void(0);" class="wd-combobox wd-project-manager">
												<p>
													<?php  __d(sprintf($_domain, 'Project_Task'), 'Assigned To');?>
												</p>
											</a>
											<div class="wd-combobox-content task_assign_to_id" style="display:none;">
												<div class="context-menu-filter">
													<span>
														<input type="text" class="wd-input-search" placeholder="<?php __('Search...');?>" rel="no-history">
													</span>
												</div>
												<div class="option-content">
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="forBatchReplace" id="forBatchReplace" style="display:none">
								<div class="wd-row wd-form-data-row">
									<div class="wd-col wd-col-md-6 wd-col-lg-4 col-right">
									<?php echo $this->Form->input('replace_estimated', array(
										'id' => 'batchReplaceWorkload',
										'label' => __d(sprintf($_domain, 'Project_Task'), 'Workload', true),
										'value' => '',
										'required' => false,
										'rel' => 'no-history',
										'div' => array(
											'class' => 'wd-input label-inline'
										)					
									)); ?>
									</div>
									<div class="wd-col wd-col-md-6 wd-col-lg-8">
										<div class="wd-multiselect multiselect batchReplaceAssignedto" id="batchReplaceAssignedto" data-name="data[ProjectTask][replace_assigned_to_id][]">
											<a href="javascript:void(0);" class="wd-combobox wd-project-manager">
												<p>
													<?php  __d(sprintf($_domain, 'Project_Task'), 'Assigned To');?>
												</p>
											</a>
											<div class="wd-combobox-content task_assign_to_id" style="display:none;">
												<div class="context-menu-filter">
													<span>
														<input type="text" class="wd-input-search" placeholder="<?php __('Search...');?>" rel="no-history">
													</span>
												</div>
												<div class="option-content">
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php } ?> 
						<div class="wd-row wd-submit-row">
							<div class="wd-col-xs-12">
								<div class="wd-submit">
									<button type="submit" class="btn-form-action btn-ok btn-right" id="btnEditNormalSave">
										<span><?php __('Save') ?></span>
									</button>
									<a class="btn-form-action btn-cancel" id="btnEditNormalCancel" href="javascript:void(0);" onclick="cancel_popup(this, false);">
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
		<!-- END Batch Edit task Popup -->
	</div>
</div>
<?php
	$listPrioritiesJson = json_decode($listPrioritiesJson, true);
	$listProjectStatus = !empty($listPrioritiesJson) ? Set::classicExtract($listPrioritiesJson['Statuses'], '{n}.ProjectStatus') : array();	
 ?>
<script>
$('#tab-popup-container').easytabs({
	'tabs' : '.tabPopup .liPopup',
});
var text_by = <?php echo json_encode(  __('by', true) ) ?>;
var text_modified = <?php echo json_encode( __('Modified', true) ) ?>;
var isTablet = <?php echo json_encode($isTablet) ?>;
var isMobile = <?php echo json_encode($isMobile) ?>;
var curent_time = <?php echo json_encode(time()) ?>;
var popup_listPhases = <?php echo json_encode($listPhases) ?>;
var project_id = <?php echo json_encode($project_id) ?>;
var canModified = <?php echo json_encode($canModified) ?>;
var listAllStatus = <?php echo json_encode($listProjectStatus)?>;
var global_task_data = {};
var popup_resource_loaded = 0;
var popup_edit_resource_loaded = 0;

Dropzone.autoDiscover = false;
$("#editTaskStartDay, #editTaskEndDay, #newTaskEndDay, #newTaskStartDay, #newEmpStartDay, #newEmpEndDay").datepicker({
	dateFormat      : 'dd-mm-yy',
});

/* for Add new Task */

	var adminTaskSetting = <?php echo json_encode($adminTaskSetting);?>;
	var newtask_date_validated = 1;
    function newTask_validated(_this){
		var cur_st_date = st_date = $('#newTaskStartDay').val();
        var cur_en_date = en_date = $('#newTaskEndDay').val();
		
		if( st_date == '' || en_date == '' ) return;
        st_date = st_date.split('-');
        en_date = en_date.split('-');
		
		var arr_st_date = st_date;
		var arr_en_date = en_date;
		
        st_date = new Date(st_date[2],st_date[1],st_date[0]);
        en_date = new Date(en_date[2],en_date[1],en_date[0]);
		
		
		//Ticket #1115.Auto edit startdate or endDate. No border red.
		if(c_task_data['task_start_date']) c_task_data['task_start_date'] = arr_st_date[2] +'-'+ arr_st_date[1] +'-'+  arr_st_date[0];
        if (st_date > en_date) {
			$('#newTaskEndDay').val(cur_st_date);
			if(c_task_data['task_end_date']) c_task_data['task_end_date'] = arr_st_date[2] +'-'+ arr_st_date[1] +'-'+  arr_st_date[0];
        }else{
			if(c_task_data['task_end_date']) c_task_data['task_end_date'] = arr_en_date[2] +'-'+ arr_en_date[1] +'-'+  arr_en_date[0];
		}
		$('#newTaskStartDay').css('border-color', '');
        $('#newTaskEndDay').css('border-color', '');
        newtask_date_validated = 1;
		var _form = $(_this).closest('form');
		if(list_assigned.length > 0 && showDisponibility == 1 && newtask_date_validated == 1){
			getManDayForResource(_form, project_id, list_assigned);
		}
    };
	$('#newTaskStartDay').on('change', function(){
		newTask_validated($(this));
	});
	$('#newTaskEndDay').on('change', function(){
		newTask_validated($(this));
	});
	var popupnct_newtask_date_validated = 1;
    function popupnct_validated(_this){
        var st_date = $('#popupnct-start-date').val();
        var en_date = $('#popupnct-end-date').val();
		if( st_date == '' || en_date == '' ) return;
        st_date = st_date.split('-');
        en_date = en_date.split('-');

        st_date = new Date(st_date[2],st_date[1],st_date[0]);
        en_date = new Date(en_date[2],en_date[1],en_date[0]);
        if( st_date > en_date){
            $(_this).addClass('invalid');
            popupnct_newtask_date_validated = 0;
        }else{
            $('#popupnct-start-date').removeClass('invalid');
            $('#popupnct-end-date').removeClass('invalid');
            popupnct_newtask_date_validated = 1;
        }
		
    };
	
	// function disable_all_option(){
		// $('#toPhase').val('').prop( "disabled", true );
		// $('#popupnct_nct_list_assigned .option-content').empty();
		// init_multiselect('#addProjectTemplate .wd-multiselect, #special-task-info-2 .wd-multiselect');
		
	// }
	function popupTask_phaseOnChange(){
		var _project_id = project_id;
		var _phase_id = $('#toPhase').val();
		if( _phase_id == '') {
			$('#newTaskStartDay').datepicker('setDate', '');
			$('#newTaskEndDay').datepicker('setDate', '');
		}	
		if(_project_id && _phase_id){
			var phase = popup_listPhases[_phase_id];
			var start_date = phase.phase_real_start_date ? new Date(phase.phase_real_start_date) : '';
			var end_date = phase.phase_real_end_date ? new Date(phase.phase_real_end_date) : '';
			$('#newTaskStartDay').datepicker('setDate', start_date);
			$('#newTaskEndDay').datepicker('setDate', end_date);
			c_task_data['project_id'] = _project_id;
			c_task_data['task_start_date'] = phase.phase_real_start_date ? phase.phase_real_start_date : '';
			c_task_data['task_end_date'] = phase.phase_real_end_date ? phase.phase_real_end_date : '';
		}
		
		$('#newTaskStartDay').trigger('change');
		$('#newTaskEndDay').trigger('change');
	}
	function update_employees_to_miltiselect(emps, elm){
		if( !elm.hasClass('wd-multiselect') ) elm = elm.find('.wd-multiselect:first');
		if( !elm.length) return false;
		elm.addClass('loading');
		elm.find('.wd-combobox a.circle-name').remove();
		elm.find('.wd-combobox p').show();
		var _cont = elm.find('.option-content');
		var _input_name = elm.data('name');
		_input_name = _input_name ? _input_name : 'data[task_assign_to_id][]';
		_cont.empty();
		var _html = '';
		$.each(emps, function(ind, emp){
			emp = emp.Employee;
			var e_actif = (typeof emp.actif !== "undefined") ? emp.actif : 1;
			_html += '<div class="projectManager wd-data-manager wd-group-' + emp.id + ' actif-'+ e_actif +'">';
			_html += '<p class="projectManager wd-data">';
			/* #1851 - Remove Avatar image */
			// _html += '<a class="circle-name" title="' + emp.name + '"><span data-id="' + emp.id + '-' + emp.is_profit_center + '">';
			// if( emp.is_profit_center){
				// _html += '<i class="icon-people"></i>';
			// }else{
				// _html += '<img width = 35 height = 35 src="'+  js_avatar( emp.id ) +'" title = "'+ emp.name +'" />';
				
			// }
			// _html += '</span></a>';
			_html += '<input type="checkbox" name="' + _input_name + '" value="' + emp.id + '-' + emp.is_profit_center + '" id="newtaskTaskAssignToId-'+ emp.id +'">';
			_html += '<span class="option-name" style="padding-left: 5px;">' + emp.name + '</span>';
			_html += '</p> </div>';
		});
		_cont.html(_html);
		elm.removeClass('loading');
	}
	function ajaxGetResources(_project_id){
		if( popup_resource_loaded ) return;
		listAssign = listAssign_updated;
		update_employees_to_miltiselect(listAssign, $('#popupnct_nct_list_assigned'));
		update_employees_to_miltiselect(listAssign, $('#batchEditAssignedto'));
		update_employees_to_miltiselect(listAssign, $('#batchAddAssignedto'));
		update_employees_to_miltiselect(listAssign, $('#batchReplaceAssignedto'));
		if( typeof nct_ajaxGetResources_Milestone == 'function') nct_ajaxGetResources_Milestone(project_id);
		if( typeof ext_nct_ajaxGetResources == 'function') ext_nct_ajaxGetResources(project_id);
		popup_resource_loaded = 1;
	}
	// ajaxGetResources(project_id);
	/* NCT task */
	var startDate, endDate;
    var listDeletion = [];
    var Holidays = {};  //get by ajax
    var Workdays = <?php echo json_encode($workdays) ?>;
	var monthName = <?php echo json_encode(array(
		__('January', true),
		__('February', true),
		__('March', true),
		__('April', true),
		__('May', true),
		__('June', true),
		__('July', true),
		__('August', true),
		__('September', true),
		__('October', true),
		__('November', true),
		__('December', true)
	)); ?>;
	var popupnct_list_assigned = {};
	var popupnct_per_workload = 0;
	function popupnct_applyDisableDays(){
		hlds = [];
		$.each(Holidays, function(year, list){
			hlds = hlds.concat(list);
		});
		if( hlds.length ){
			$('#popupnct_date-list').multiDatesPicker('addDates', hlds, 'disabled');
		}
	}
	function popupnct_refreshPicker(){
		var start = $('#popupnct-start-date').datepicker('getDate'),
			end = $('#popupnct-end-date').datepicker('getDate');
		if( start && end && start <= end ){
			// $('#popupnct_date-list').datepicker('setDate', start);
			$('#popupnct_date-list').multiDatesPicker('resetDates');
			$('#popupnct_date-list').gotoDate(start);
			if( popupnct_list_assigned.length > 0 ){
				$('#popupnct_add-date').removeClass('disabled');
				$('#popupnct_add-range').removeClass('disabled');
			}
			popupnct_resetRange();
			$('#popupnct-period-start-date, #popupnct-period-end-date').prop('disabled', false);
		}
		else {
			$('#popupnct_add-date').addClass('disabled');
			$('#popupnct_add-range').addClass('disabled');
			$('#popupnct-period-start-date, #popupnct-period-end-date').prop('disabled', true);
		}
		if( !start ) year = new Date().getFullYear();
		else year = start.getFullYear();
		loadHolidays(year);
		popupnct_resetPicker();
		// $('#popupnct_date-list').multiDatesPicker('resetDates');
		$('#popupnct-range-picker').datepicker('refresh');
		popupnct_unhightlightRange();
	}
	//if date in disabled fields, reject
	function isValidDate(d, start, end){
		var date = d.getDay();
		return d >= start && d <= end && Workdays[date] == 1;
	}
	function popupnct_getValidDate(){
		var result = [];
		var dates = $('#popupnct_date-list').multiDatesPicker('getDates', 'object');
		var start = $('#popupnct-start-date').datepicker('getDate'),
			end = $('#popupnct-end-date').datepicker('getDate');
		for(var i = 0; i < dates.length; i++){
			if( isValidDate(dates[i], start, end) ){
				var date = dates[i].getDate(),
					month = dates[i].getMonth() + 1;
				result.push( (date < 10 ? '0' + date : date) + '-' + (month < 10 ? '0' + month : month) + '-' + dates[i].getFullYear() );
			}
		}
		return result;
	}
	function popupnct_draw_employee_columns(){		
		$('.popupntc-employee-col').remove();
		if(popupnct_list_assigned.length == 0){
			return;
		}
		var datas = [];
		var total_consumed = 0, total_in_used = 0;
		if(global_task_data && global_task_data.data){
			$.each(global_task_data.data, function(key, data){
				var key = key.substr(2);
				datas[key] = [];
				$.each(data, function(index, value){
					datas[key][value.reference_id] = value;
				});
			});
			$.each(global_task_data.request, function(index, val){
				if(index != 'all'){
					_consumed = val[0] ? val[0] : 0;
					_in_used = val[1] ? val[1] : 0;
					total_consumed += parseFloat(_consumed);
					total_in_used += parseFloat(_in_used);
				}
			});
		}		
		$.each(popupnct_list_assigned, function(ind, emp){
			//QuanNV update by 08/06/2019
			var id = '';
			var is_profit_center = '';
			if(emp){
				var employee = (emp.id).split('-');
				id = employee[0];
				is_profit_center = employee[1]==1 ? 1 : 0;
				// console.log(employee, is_profit_center)
			}
			var name = emp.name;
			var cell = $('.cell-' + id);
			//add header
			var _avt = '<span style="margin-bottom: 0px;margin-top: 10px;" class="circle-name" title="' + name + '" data-id="' + id + '-' + is_profit_center + '">';
			if( is_profit_center == 1 ){
				_avt += '<i class="icon-people"></i>';
			}else{
				_avt += '<img width = 35 height = 35 src="'+  js_avatar( id ) +'" title = "'+ name +'" />';	
			}
			_avt += '</span>';
			_avt += '<span class="header-name" title="' + name + '" >'+ name.replace(/^PC \/ /, '') +'</span>';
			id = is_profit_center ? (id + '-1') : (id + '-0');
			var html = '<td class="value-cell header-cell cell-' + id + ' popupntc-employee-col" id="popupnct_col-' + id + '" data-id="' + id + '">' + _avt + '</td>';
						
			//End update
			$(html).insertBefore('#popupnct_consumed-column');
			//add content
			var  total_workload = 0;
			var popupnct_date = $('#popupnct-assign-table').find('.popupnct-date');
			$.each(popupnct_date, function(){
				var ciu = $(this).parent().find('.ciu-cell'),
					date = $(this).prop('id').replace('date-', '');
				var _id = id.split('-');
				var e_id=_id[0],
					e_ispc= _id[1]==1 ? 1 : 0,
					ip_name = 'data[workloads][' + date + '][' + id + ']';
				var _val_workload = (datas[date] && datas[date][e_id] && datas[date][e_id]['estimated']) ? datas[date][e_id]['estimated'] : 0;
				total_workload += _val_workload;
				$('<td class="value-cell cell-' + id + ' popupntc-employee-col"><input type="hidden" name="' + ip_name + '[reference_id]" value="' + e_id + '"/><input type="hidden" name="' + ip_name + '[is_profit_center]" value="' + e_ispc + '"/><input type="text" id="val-' + date + '-' + id + '" data-old="0" class="p_workload p_workload-' + id + '" data-id="' + id + '" value="'+ _val_workload +'" name="' + ip_name + '[estimated]" onchange="popupnct_changeTotal(this)" data-ref/></td>').insertBefore(ciu);
			});
			bindNctKeys();
			//add footer
			$('<td class="value-cell cell-' + id + ' popupntc-employee-col" id="popupnct_foot-' + id + '" data-id="' + id + '">'+ total_workload.toFixed(2) +'</td></tr>').insertBefore('#popupnct_total-consumed');
			
		});
		$('#popupnct_total-consumed').empty().html(total_consumed.toFixed(2) +' ('+ total_in_used.toFixed(2) + ')');
		// popupnct_refreshPicker();
		popupnct_isValidList();
		set_width_popupnct();
		
	}
	Date.prototype.format = function(format){
        if( !format )format = 'dd-mm-yy';
        return $.datepicker.formatDate(format, this);
    }
	function cancel_popup_template_add_nct_task(){
		global_task_data = {};
		multiselect_setval( $('#addProjectTemplate .wd-multiselect'),{});
		$('#popupnct-assign-table').find('tbody').empty();
		// init_multiselect('#addProjectTemplate .wd-multiselect, #special-task-info-2 .wd-multiselect');
	}
	// function nct_disable_all_option(){
		// $('#popupnct-phase').val('').prop( "disabled", true );
		// init_multiselect('#addProjectTemplate .wd-multiselect, #special-task-info-2 .wd-multiselect');
		
	// }
	function ext_nct_ajaxGetResources(_project_id){
		if( $.isEmptyObject(listAssign)){
			$.ajax({
				url: '/projects/getTeamEmployees/'+_project_id,
				type: 'GET',
				dataType: 'json',
				success: function(data) {
					if( data.success == true){
						listAssign = data.data;
						update_employees_to_miltiselect(listAssign, $('#nct_list_assigned'));
					}
				},
				complete: function(){
					$('#nct_list_assigned').removeClass('loading');
				}
			});
		}else{
			update_employees_to_miltiselect(listAssign, $('#nct_list_assigned'));
			$('#nct_list_assigned').removeClass('loading');
		}
	}
	function nct_ajaxGetResources_Milestone(_project_id){
		if( $.isEmptyObject(listAssign)){
			$.ajax({
				url: '/projects/getTeamEmployees/'+_project_id,
				type: 'GET',
				dataType: 'json',
				success: function(data) {
					if( data.success == true){
						listAssign = data.data;
						update_employees_to_miltiselect(listAssign, $('#multiselect-popupnct-pm'));
					}
				},
				complete: function(){
					$('#multiselect-popupnct-pm').removeClass('loading');
				}			
			});
		}else{
			update_employees_to_miltiselect(listAssign, $('#multiselect-popupnct-pm'));
			$('#multiselect-popupnct-pm').removeClass('loading');
		}
		if( adminTaskSetting.Milestone == "1"){
			$('#popupnct-milestone').addClass('loading');
			$('#popupnct-milestone').html('');
			if( $.isEmptyObject(projectMilestones)){
				$.ajax({
					url: '/project_milestones/get_list_milestone/'+_project_id,
					type: 'GET',
					dataType: 'json',
					success: function(data) {
						if( data.length){
							var sel = '<option value=""></option>';
							$.each(data, function(ind, mil){
								sel += '<option value="'+ mil.ProjectMilestone.id +'">' + mil.ProjectMilestone.project_milestone+ '</option>';
							});
							$('#popupnct-milestone').html(sel);
						}
					},
					complete: function(){
						$('#popupnct-milestone').removeClass('loading');
					}
				});
			}else{
				var sel = '<option value=""></option>';
				$.each(projectMilestones, function(id, mil){
					sel += '<option value="'+ mil.id +'">' + mil.project_milestone+ '</option>';
				});
				$('#popupnct-milestone').html(sel);
				$('#popupnct-milestone').removeClass('loading');
			}
		}
	}
	// nct_ajaxGetResources_Milestone(project_id);
	function nct_phaseOnChange(){
		var _project_id = project_id;
		var _phase_id = $('#popupnct-phase').val();
		if( _phase_id == '') {
			$('#popupnct-start-date').datepicker('setDate', '');
			$('#popupnct-end-date').datepicker('setDate', '');
		}	
		if(_project_id && _phase_id){
			var phase = popup_listPhases[_phase_id];
			var start_date = (phase && phase.phase_real_start_date) ? new Date(phase.phase_real_start_date) : '';
			var end_date = (phase && phase.phase_real_end_date) ? new Date(phase.phase_real_end_date) : '';
			$('#popupnct-start-date').datepicker('setDate', start_date);
			$('#popupnct-end-date').datepicker('setDate', end_date);
		}
		$('#popupnct-start-date').trigger('change');
		$('#popupnct-end-date').trigger('change');
		if(typeof popupnct_refreshPicker == 'function') popupnct_refreshPicker();
	}
	/*
	* #390 13-08-2019  RE: Ticket #390 «INCIDENT/ANOMALIE» «task screen» «EN COURS DE DEV» «Développeur» «z0 Gravity»
	* hide all calendar picker except date
	*/
	function popupnct_selectRange(){
        var val = parseInt($('#popupnct-range-type').val());
        switch(val){
            case 0:
                $('#popupnct_date-list-container').show();
                $('#popupnct-range-picker-container').hide();
                $('.period-input').hide();
				break;
            default:
                $('#popupnct_date-list-container').hide();
                $('#popupnct-range-picker-container').hide();
                $('.period-input').hide();
				break;
        }
        if( val ==3){
            $('#popupnct_create-range').hide();
        } else {
            $('#popupnct_create-range').show();
        }
        popupnct_resetRange();
		popupnct_refreshPicker();
        if( !$('#popupnct-assign-table tbody tr').length ){
            $('#popupnct-assign-table tfoot .value-cell').text('0.00');
            $('#popupnct-total-workload').val('0.00');
        }
    }
	// popupnct_selectRange();
    function popupnct_removeRowCall(e){
        var cols = $(e).parent().parent().find('input');
        cols.each(function(){
            var me = $(this);
            var id = me.data('id');
            var original = parseFloat($('#popupnct_foot-' + id).text());
            $('#popupnct_foot-' + id).text((original-parseFloat(me.val())).toFixed(2));
        });
        $(e).parent().parent().remove();
        //add to deletion list
        listDeletion.push('date:' + $(e).parent().parent().find('.popupnct-date').text());
        popupnct_refreshPicker();
        popupnct_isValidList();
    }
    function popupnct_removeRow(e){
        //check if has in used / consumed
        popupnct_removeRowCall(e);
    }
    function popupnct_changeTotal(e){
        //check here
        var me = $(e);
        if( !me.length )return;
        var old = parseFloat(me.data('old'));
        var newVal = parseFloat(me.val());
        var type = parseInt($('#popupnct-range-type').val());
        if( (isNaN(newVal) || newVal < 0 ) || (type == 0 && newVal > 1) ){
            if( type == 0 )
                alert('<?php __('Enter value between 0 and 1') ?>');
            else alert('<?php __('Please enter value >= 0') ?>');
            me.val(old);
            me.focus();
            return;
        }
        var id = me.data('id');
        var total = 0;
        $('.' + 'p_workload-' + id).each(function(){
            total += parseFloat($(this).val());
        });
        $('#popupnct_foot-' + id).text(total.toFixed(2));
        me.data('old', newVal);
        popupnct_isValidList();
    }
    function popupnct_isValidList(){
        var total = 0;
        $('#popupnct_assign-list tfoot .value-cell').each(function(){
            total += parseFloat($(this).text());
        });
        $('#popupnct-total-workload').val(total.toFixed(2));
    }
    function popupnct_minMaxDate(){
        var min, max;
        var type = $('#popupnct-range-type').val();
        if( type != 0 ){
            return [$('#popupnct-start-date').datepicker('getDate'), $('#popupnct-end-date').datepicker('getDate')];
        }
        $('.popupnct-date').each(function(){
            var text = $(this).text();
            var value = $.datepicker.parseDate('dd-mm-yy', text);
            if( !min || min > value)
                min = value;
            if( !max || max < value)
                max = value;
        });
        return [min, max];
    }
    function popupnct_isValidList(){
        var range = popupnct_minMaxDate(),
            start = $('#popupnct-start-date').datepicker('getDate'),
            end = $('#popupnct-end-date').datepicker('getDate');
        if( range[0] < start || range[1] > end )return false;
        return true;
    }
    function popupnct_selectCurrentRange(){
        $('#popupnct-range-picker').find('.ui-datepicker-current-day').addClass('ui-state-highlight');
    }
    function popupnct_unhightlightRange(){
        $('#popupnct-range-picker').find('.ui-state-highlight').removeClass('ui-state-highlight');
        $('#popupnct-range-picker').find('.ui-datepicker-current-day').removeClass('ui-datepicker-current-day');
		
        $('#popupnct_date-list').find('.ui-state-highlight').removeClass('ui-state-highlight');
        $('#popupnct_date-list').find('.ui-datepicker-current-day').removeClass('ui-datepicker-current-day');
    }
	function popupnct_resetRange(){
        var start = $('#popupnct-start-date').datepicker('getDate');
        if( start ){
            //reset range picker
            $('#popupnct-range-picker').datepicker('setDate', start);
        }
        popupnct_unhightlightRange();
        startDate = null;
        endDate = null;
    }
	function dateDiff(date1, date2) {
        date1.setHours(0);
        date1.setMinutes(0, 0, 0);
        date2.setHours(0);
        date2.setMinutes(0, 0, 0);
        var datediff = Math.abs(date1.getTime() - date2.getTime()); // difference
        return parseInt(datediff / (24 * 60 * 60 * 1000), 10); //Convert values days and return value
    }

    function dateString(date, format){
        if( !format )format = 'dd-mm-yy';
        return $.datepicker.formatDate(format, date);
    }

    function toRowName(date){
        //parse date from task
        var part = date.split('_');
        switch(part[0]){
            case '1':
            case '3':
                var d = part[1].split('-');
                var start = new Date(d[2], d[1]-1, d[0]);
                d = part[2].split('-');
                var end = new Date(d[2], d[1]-1, d[0]);
                return dateString(start, 'dd/mm') + ' - ' + dateString(end, 'dd/mm/yy');
            case '2':
                var d = part[1].split('-');
                var start = new Date(d[2], d[1]-1, d[0]);
                return monthName[start.getMonth()] + ' ' + d[2];
            default:
                return dateString(new Date(part[1]));
        }
    }
	$('#popupnct-period-start-date').datepicker({
		showOtherMonths: true,
		selectOtherMonths: true,
		dateFormat: 'dd-mm-yy',
		beforeShowDay: function(d){
			var date = d.getDay();
			var start = $('#popupnct-start-date').datepicker('getDate'),
				end = $('#popupnct-end-date').datepicker('getDate');
			var canSelect = d >= start && Workdays[date] == 1 && d <= end;
			return [canSelect, '', ''];
		},
		onSelect: function(dateText, inst) {
			var type = parseInt($('#popupnct-range-type').val());
			var date = $(this).datepicker('getDate');
			startDate = date;
		},
	});
	$('#popupnct-period-end-date').datepicker({
		showOtherMonths: true,
		selectOtherMonths: true,
		dateFormat: 'dd-mm-yy',
		beforeShowDay: function(d){
			var date = d.getDay();
			var start = $('#popupnct-period-start-date').datepicker('getDate'),
				end = $('#popupnct-end-date').datepicker('getDate');
			if( start && end){
				var canSelect = d >= start && Workdays[date] == 1 && d <= end;
				return [canSelect, '', ''];
			}
			return [false];
		},
		onSelect: function(dateText, inst) {
			var type = parseInt($('#popupnct-range-type').val());
			var date = $(this).datepicker('getDate');
			endDate = date;
		},
	});
	$('#popupnct-range-type').change(function(){
		//xoa het tat ca workload
		$('#popupnct-assign-table tbody').html('');
		popupnct_selectRange();
		set_width_popupnct();
	});
	$('#popupnct_reset-range').click(function(){
		popupnct_resetRange();
	});
	$('#popupnct_add-range').click(function(){
		//check neu co assign thi moi them date
		if( popupnct_list_assigned.length == 0 ){
			return;
		}
		if( startDate && endDate ){
			_popupnct_addRange(startDate, endDate);
		}
		$('.popupnct-period-input-calendar').datepicker('setDate', null);
	});
	$('#popupnct_add-date').click(function(){
		if( $(this).hasClass('disabled') )return;
		var dates = popupnct_getValidDate();
		//check neu co assign thi moi them date
		if( popupnct_list_assigned.length == 0 ){
			return;
		}
		for(var i in dates){
			var date = dates[i];
			if( date ){
				var invalid = false;
				$('.popupnct-date').each(function(){
					var value = $(this).text();
					if( value == date )invalid = true;
				});
				if( !invalid ){
					//add new row
					var html = '<tr>';
					html += '<td id="date-' + date + '" class="popupnct-date" style="text-align: left">' + date + '</td>';
					$('.header-cell').each(function(){
						var col = $(this);
						var id = col.prop('id').replace('popupnct_col-', '');
						var hide = '';
						if( !col.is(':visible') )hide = 'style="display: none"';
						var _id = id.split('-');
						var e_id=_id[0],
							e_ispc= _id[1]==1 ? 1 : 0,
							ip_name = 'data[workloads][' + date + '][' + id + ']';
						html += '<td class="popupntc-employee-col value-cell cell-' + id + '" ' + hide + '><input type="hidden" name="' + ip_name + '[reference_id]" value="' + e_id + '"/><input type="hidden" name="' + ip_name + '[is_profit_center]" value="' + e_ispc + '"/><input type="text" id="val-' + date + '-' + id + '" data-old="0" class="p_workload p_workload-' + id + '" data-id="' + id + '" name="' + ip_name + '[estimated]" value="0" onchange="popupnct_changeTotal(this)" data-ref/></td>';
					});
					html += '<td style="background: #f0f0f0" class="ciu-cell">0.00 (0.00)</td>';
					html += '<td class="row-action"><a class="cancel" onclick="popupnct_removeRow(this)" href="javascript:;"></a></td>';
					html += '</tr>';
					$('#popupnct-assign-table tbody').append(html);
				}
			}
		}
		bindNctKeys();
		set_width_popupnct();
		//$('#popupnct_date-list').multiDatesPicker('resetDates');
	});
	$('#popupnct_create-range').click(function(){
		/*
		if( popupnct_list_assigned.length == 0 ){
			var dialog = $('#popupnct_modal_dialog_alert').dialog();
			$('#popupnct_btnNoAL').click(function() {
				dialog.dialog('close');
			});
			return;
		}
		var dateType = parseInt($('#popupnct-range-type').val());
		//available for week and month and day.
		var start = $('#popupnct-start-date').datepicker('getDate'),
			end = $('#popupnct-end-date').datepicker('getDate');
		if(!start || !end){
			var dialog = $('#popupnct_modal_dialog_alert2').dialog();
			$('#popupnct_btnNoAL2').click(function() {
				dialog.dialog('close');
			});
			return false;
		}
		*/
		var start = $('#popupnct-start-date').datepicker('getDate'),
			end = $('#popupnct-end-date').datepicker('getDate');
		if( popupnct_list_assigned.length == 0 || !start || !end ){
			// $(this).closest('form')[0].reportValidity(); only support by Chrome and Firefox
			$(this).closest('form').find(':submit').click();
			return false;
		}else popupnct_addRange(start, end);
	});
	$('#popupnct_per-workload').bind("cut copy paste",function(e) {
		e.preventDefault();
	});
	$('#popupnct_per-workload, #popupnct-end-date').on('keypress', function(e){
		if( e.key == "Enter"){
			e.preventDefault();
			$(this).closest('.input-icon-container').find('.wd-icon .btn:first').click();
		}
	});
	$('#popupnct_fill-workload').on('click', function(){
		var val = parseFloat($('#popupnct_per-workload').val());
		if( isNaN(val) )return;
		if( $('#popupnct-range-type').val() == '0' && val > 1 )return;
		$('#popupnct-assign-table tbody .value-cell input.p_workload').each(function(){
			// console.log( val);
			$(this).val(val).data('old', val).trigger('change');
		});
		popupnct_per_workload = val;
		//popupnct_isValidList();
	});
	$('#popupnct-unit-price').keypress(function(e){
		var key = e.keyCode ? e.keyCode : e.which;
		if(!key || key == 8 || key == 13 || e.ctrlKey){return;}
		var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
		var _val = parseFloat(val, 10);
		if(!/^[\-+]?([0-9]{1}|[1-9][0-9]{1,9})(\.[0-9]{0,2})?$/.test(val)){
			e.preventDefault();
			return false;
		}
	});
	$('#popupnct_date-list').multiDatesPicker({
		dateFormat: 'dd-mm-yy',
		separator: ',',
		//numberOfMonths: [1,3],
		beforeShowDay: function(d){
			var start = $('#popupnct-start-date').datepicker('getDate'),
				end = $('#popupnct-end-date').datepicker('getDate');
			if( !start || !end || start > end ){
				return [false, '', ''];
			}
			//var date = d.getDay();
			return [ isValidDate(d, start, end) ,'', ''];
		},
		onChangeMonthYear: function(year){
			loadHolidays(year);
		}
	});
	$('#popupnct-range-picker').datepicker({
		showOtherMonths: true,
		selectOtherMonths: true,
		dateFormat: 'dd-mm-yy',
		onSelect: function(dateText, inst) {
			var type = parseInt($('#popupnct-range-type').val());
			var date = $(this).datepicker('getDate');
			var curStart = $('#popupnct-start-date').datepicker('getDate'),
				curEnd = $('#popupnct-end-date').datepicker('getDate');
			if( type == 1 ){
				startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 1);  //select monday
				endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 5);    //select friday
			} else if(type == 2 || type == 0){
				startDate = new Date(date.getFullYear(), date.getMonth(), 1);  //select first day
				endDate = new Date(date.getFullYear(), date.getMonth() + 1, 0);  //select last day
			}else {
				startDate = curStart;
				endDate = curEnd ;
			}
			//điều chỉnh lại start date / end date của 2 cái input
			if( curStart > startDate ){
				//$('#popupnct-start-date').datepicker('setDate', startDate);
			}
			if( curEnd < endDate ){
				//$('#popupnct-end-date').datepicker('setDate', endDate);
			}
			var start = dateString(startDate);
			var end = dateString(endDate);
			$('#popupnct_date-list').multiDatesPicker('resetDates');
			popupnct_selectCurrentRange();
		},
		beforeShowDay: function(date) {
			var cssClass = '';
			var canSelect = true;
			var start = $('#popupnct-start-date').datepicker('getDate'),
				end = $('#popupnct-end-date').datepicker('getDate');
			if( !start || !end )canSelect = false;
			else canSelect = date >= start && date <= end;
			if(date >= startDate && date <= endDate)
				cssClass = 'ui-state-highlight';
			return [canSelect, cssClass];
		},
		onChangeMonthYear: function(year, month, inst) {
			popupnct_selectCurrentRange();
		}
	});
	$('#popupnct_per-workload').keypress(function(e){
		var key = e.keyCode ? e.keyCode : e.which;
		if(!key || key == 8 || key == 13 || e.ctrlKey){return;}
		var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
		var _val = parseFloat(val, 10);
		if(!/^[\-+]?([0-9]{1}|[1-9][0-9]{1,9})(\.[0-9]{0,2})?$/.test(val)){
			e.preventDefault();
			return false;
		}
	});
	
	function popupnct_get_list_assign(_this_id){
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
	function multiselect_popupnct_pmonChange(_this_id){
		popupnct_list_assigned = popupnct_get_list_assign( _this_id);
		popupnct_draw_employee_columns();
		if( popupnct_list_assigned.length > 0 ){
			$('#popupnct_add-date').removeClass('disabled');
			$('#popupnct_add-range').removeClass('disabled');
		}else{
			$('#popupnct_add-date').addClass('disabled');
			$('#popupnct_add-range').addClass('disabled');
		}
	}
	function popupnct_resetPicker(){
        if( $('#popupnct-range-type').val() == 0 ){
            $('#popupnct_date-list').multiDatesPicker('resetDates');
            $('.popupnct-date').each(function(){
                var value = $(this).text();
                $('#popupnct_date-list').multiDatesPicker('addDates', [$.datepicker.parseDate('dd-mm-yy', value)]);
            });
        }
    }
	function bindNctKeys(){
        var length = $('[data-ref]').length;
        $('[data-ref]').off('focus').on('focus', function(){
            var f = $(this).data('focused');
            if( f )return;
            $(this).data('focused', 1);
            $(this).select();
        }).on('blur', function(){
            $(this).data('focused', 0);
            // popupnct_changeTotal(this);
        })
        .off('keydown').on('keydown', function(e){
            //tab key
            var index = $('[data-ref]').index(this);
            if( e.keyCode == 13 ){
                if( $(this).closest('td').next().hasClass('ciu-cell') ){
                    $(this).closest('tr').next().find('input:first').focus();
                } else {
                    $(this).closest('td').next().find('input').focus();
                }
                e.preventDefault();
            }
        });
    }
	
	/*
		@date: js date object
		@day:
			0 = sunday
			1 = monday..
			6 = saturday
		@return: new date object
	*/
    function getDayOfWeek(date, day) {
        var d = new Date(date),
            cday = d.getDay(),
            diff = d.getDate() - cday + day;
        return new Date(d.setDate(diff));
    }
    function getListDay(start, end){
        var list = {};
        var current = new Date(start);
        while( current <= end ){
            var sm = new Date(current);
            var currentDate = sm.format('yymmdd');
            if( typeof list[currentDate] == 'undefined' ){
                list[currentDate] = {
                    // start: monday,
                    date: new Date(sm.getFullYear(), sm.getMonth(), sm.getDate())
                };
            }
            current = new Date(sm.getFullYear(), sm.getMonth(), sm.getDate()+1);
        }
        return list;
    }
    function getListWeek(start, end){
        var list = {};
        var current = new Date(start);
        while( current <= end ){
            var monday = getDayOfWeek(current, 1),
                currentDate = monday.format('yymmdd');
            if( typeof list[currentDate] == 'undefined' ){
                list[currentDate] = {
                    start: monday,
                    end: getDayOfWeek(current, 5)
                };
            }
            current = new Date(monday.getFullYear(), monday.getMonth(), monday.getDate()+7);
        }
        return list;
    }
    function getListMonth(start, end){
        var list = {};
        var current = new Date(start);
        end = new Date(end.getFullYear(), end.getMonth() + 1, 0);
        while( current <= end ){
            current.setDate(1);
            var sm = new Date(current);
            var currentDate = sm.format('yymmdd');
            if( typeof list[currentDate] == 'undefined' ){
                list[currentDate] = {
                    start: sm,
                    end: new Date(sm.getFullYear(), sm.getMonth() + 1, 0)
                };
            }
            current.setMonth(current.getMonth() + 1);
        }
        return list;
    }
	function popupnct_addRange(start, end, reset){
        if( reset ){
            //xoa het tat ca workload
            $('#popupnct-assign-table tbody').html('');
            popupnct_selectRange();
        }
        var type = parseInt($('#popupnct-range-type').val());
        var list;
        if( type == 0 ) {
            list = getListDay(start, end)
        } else if (type == 1) {
            list = getListWeek(start, end)
        } else {
            list = getListMonth(start, end)
        }
        // var list = type == 1 ? getListWeek(start, end) : getListMonth(start, end),
        var minDate, maxDate;
        $.each(list, function(key, date){
            if(type == 0 ){
                if( !minDate || minDate >= date.date ){
                    minDate = new Date(date.date);
                }
                if( !maxDate || maxDate <= date.date ){
                    maxDate = new Date(date.date);
                }
                var _curYear = date.date.format('yy'),
                    _curDate = date.date.format('dd-mm-yy')
                if(typeof Holidays[_curYear] == 'undefined'){
                    loadHolidays(_curYear);
                }else if(Holidays[_curYear].length == 0){
					loadHolidays(_curYear);
				}				
                if( ($.inArray(_curDate, Holidays[_curYear]) == -1) && popupnct_isValidDateForNctTaskDate(date.date) ){
                    _popupnct_addRange(date.date, date.date);
                }
            } else {
                if( !minDate || minDate >= date.start ){
                    minDate = new Date(date.start);
                }
                if( !maxDate || maxDate <= date.end ){
                    maxDate = new Date(date.end);
                }
                _popupnct_addRange(date.start, date.end);
            }
        });
        $('#popupnct-start-date').datepicker('setDate', minDate);
        $('#popupnct-end-date').datepicker('setDate', maxDate);
    }
    function popupnct_isValidDateForNctTaskDate(d){
        var date = d.getDay();
        return Workdays[date] == 1;
    }
    function _popupnct_addRange(start, end){
        var date = dateString(start) + '_' + dateString(end);
        var type = parseInt($('#popupnct-range-type').val());
        var rowName;
        if(type == 0){
            date = dateString(start);
            rowName = start.format('dd-mm-yy');
        } else if (type == 1) {
            rowName = start.format('dd/mm') + ' - ' + end.format('dd/mm/yy');
        }else{
            rowName = monthName[start.getMonth()] + ' ' + start.getFullYear();
        }
        // var rowName = type == 2 ? monthName[start.getMonth()] + ' ' + start.getFullYear() : start.format('dd/mm') + ' - ' + end.format('dd/mm/yy');
        var invalid = $('#date-' + date).length ? true : false;
        if( !invalid ){
            //add new row
            var html = '<tr><td id="date-' + date + '" class="popupnct-date" style="text-align: left">' + rowName + '</td>';
            $('.header-cell').each(function(){
                var col = $(this);
                var id = col.prop('id').replace('popupnct_col-', '');
                var hide = '';
                if( !col.is(':visible') )hide = 'style="display: none"';
				var _id = id.split('-');
				var e_id=_id[0],
					e_ispc= _id[1]==1 ? 1 : 0,
					ip_name = 'data[workloads][' + date + '][' + id + ']';
                html += '<td class="popupntc-employee-col value-cell cell-' + id + '" ' + hide + '><input type="hidden" name="' + ip_name + '[reference_id]" value="' + e_id + '"/><input type="hidden" name="' + ip_name + '[is_profit_center]" value="' + e_ispc + '"/><input type="text" id="val-' + date + '-' + id + '" data-old="0" class="p_workload p_workload-' + id + '" data-id="' + id + '"  name="' + ip_name + '[estimated]" value="0" onchange="popupnct_changeTotal(this)" data-ref/></td>';
            });
            html += '<td style="background: #f0f0f0" class="ciu-cell">0.00 (0.00)</td>';
			html += '<td class="row-action"><a class="cancel" onclick="popupnct_removeRow(this)" href="javascript:;"></a></td>';
			html += '</tr>';
            $('#popupnct-assign-table tbody').append(html);
            bindNctKeys();
        }
		set_width_popupnct();
    }
	$('#popupnct-start-date').datepicker({
		dateFormat: 'dd-mm-yy',
		beforeShowDay: function(d){
			var date = d.getDay();
			return [Workdays[date] == 1, '', ''];
		},
		onSelect: function(){
			//showPicker();
			popupnct_refreshPicker();
		}
	});
	$('#popupnct-end-date').datepicker({
		dateFormat: 'dd-mm-yy',
		beforeShowDay: function(d){
			var date = d.getDay();
			var start = $('#popupnct-start-date').datepicker('getDate'),
				check = Workdays[date] == 1;
			if( start != null ){
				check = check && start <= d;
			}
			return [ check, '', ''];
		},
		beforeShow: function(e, i){
			var start = $('#popupnct-start-date').datepicker('getDate'),
				end = $(e).datepicker('getDate');
			if( !end && start ){
				return {defaultDate: start};
			}
		},
		onSelect: function(){
			//showPicker();
			popupnct_refreshPicker();
		}
	});
	function reset_form(form_id){
		var _form = $('#' + form_id);
		if( !_form.length) return;
		_form[0].reset();
		_form.find('input, select').trigger('change');
		if( _form.find('.wd-multiselect').length ){
			init_multiselect('#' + form_id + ' .wd-multiselect');
		}
		if(_form.find('.nct-assign-table').length ){
			_form.find('.nct-assign-table tbody').empty();
		}

	}
	function update_to_ext(data){
		var taskID = data.data.id;
		var tree = Ext.getCmp('pmstreepanel');
		var _task_exist = tree.getStore().getById(taskID);
		if(!_task_exist){
			// Phần này chỉ dùng cho add new task không sử dụng được cho edit
			var task_data = data;
			if( 'message'in data && 'ProjectTask' in data.message) task_data.data = data.message.ProjectTask;			
			tree.setLoading(i18n('Please wait'));
			var cellEditingPlugin	= tree.cellEditingPlugin, // for double click
				selectionModel		= tree.getSelectionModel();
			var task = $.extend({}, {
				task_title  : '',
				loaded      : true,
				leaf        : false,
				expanded    : true,
				children    : [],
				parent_id   : 0,
				parent_name : '',
				project_id  : tree.project_id,
				is_nct      : 1
			}, task_data.data);
			var phases = list_phase(tree);
			// console.log( task);
			var _phase_id = task['project_planed_phase_id'];
			// console.log( _phase_id, phases);
			if( _phase_id in phases){
				parentTask = phases[_phase_id];
			}else{
				tree.refreshStaffing(function(callback){
				});
				tree.setLoading(false);
				location.reload();
				return;
			}
			var newTask = Ext.create('PMS.model.ProjectTaskPreview', task);
			parentTask.set('leaf', false);
			parentTask.appendChild(newTask);
			if(!parentTask.data.children||parentTask.data.children=='null')
				parentTask.data.children=[];
			parentTask.data.children.push(newTask.data);
			var eAe = function() {
				//temporary clear filter
				// tree.clearFilter();
				if(parentTask.isExpanded()) {
					selectionModel.select(newTask);
					//cellEditingPlugin.startEdit(newTask, 0);
				} else {
					tree.on('afteritemexpand', function startEdit(task) {
						if(task === parentTask) {
							selectionModel.select(newTask);
							//cellEditingPlugin.startEdit(newTask, 0);
							tree.un('afteritemexpand', startEdit);
						}
					});
					parentTask.expand();
				}
			};
			if(tree.getView().isVisible(true)) {
				eAe();
			} else {
				tree.on('expand', function onExpand() {
					expandAndEdit();
					tree.un('expand', onExpand);
				});
				tree.expand();
			}
		}
		tree.refreshSummary(function(callback){
		   tree.setLoading(false);
		});
		tree.refreshStaffing(function(callback){
			//do nothing
		});
		tree.refreshView();
		if( autoRefreshGantt) refreshGanttChart();
	}
	function update_to_jqx(data){
		var task_item = data.data;
		var _task = [] ;
		_task['status'] = parseInt( task_item['task_status_id'] );
		_task['id'] = parseInt(task_item['id']);
		_task['text'] = task_item['task_title'];
		data_tasks[_task['id']] = task_item;
		$('.jqx-kanban').jqxKanban('removeItem', task_item['id']);
		$('#widget-task').find('li.task-' + _task['id']).remove();
		$('.jqx-kanban').jqxKanban('addItem', _task);
		$('#kanban_' + _task['id'] ).addClass('jqx-kanban-item').data('kanbanItemId', _task['id'] );
	}
	function ProjectTaskAddPopupForm_afterSubmit(form_id, data){
		var form = $('#' + form_id);
		var msg = form.find('.form-message');
		var err_msg = form.find('.alert-message');
		if( data.success == true){
			if( typeof Ext != 'object'){
				location.reload();
			}else{
				update_to_ext(data);
				reset_form(form_id);
			}
		}else{
			msg.empty();
			err_msg.text(data.message);
		}
		
	}
	function NCTTaskAddPopupForm_afterSubmit(form_id, data){
		var form = $('#' + form_id);
		var msg = form.find('.form-message');
		var err_msg = form.find('.alert-message');
		if( data.result == true){
			if( typeof Ext != 'object'){
				location.reload();
			}else{
				update_to_ext(data);
				reset_form(form_id);
			}
		}else{
			msg.empty();
			err_msg.text(data.message);
		}
		
	}
	/* END NCT task */
	
/* END for Add new Task */
	
	jQuery.removeFromArray = function(value, arr) {
        return jQuery.grep(arr, function(elem, index) {
            return elem !== value;
        });
    };
	
	function set_width_popupnct(){
		// return;
		$.each( $('.wd-row-inline'), function (i, _row){
			var _row = $(_row);
			var _width = 0;
			$.each( _row.children(), function( j, _col){
				_width += $(_col).width()+41;
			});
			_row.width(_width);
		});
	}
	function template_add_nct_task_showed(){
		init_multiselect('#template_add_nct_task .wd-multiselect');
		set_width_popupnct();
	}
	function popup_dropzone(){
		var popup_task_Dropzones = $('#template_add_task, #template_add_nct_task').find('.dropzone');
		if( popup_task_Dropzones.length) $.each( popup_task_Dropzones, function(ind, _tag){
			$(function() {
				var _form = $(_tag).closest('.form-style-2019');
				var _form_id = _form.prop('id');
				var _reload = _form.data('reload');
				var _redirect = _form.data('open-new-link');
				if( typeof _reload == 'undefined') _reload = 0;
				if( typeof _redirect == 'undefined') _redirect = 0;
				var _Dropzone = new Dropzone(_tag, {
					// acceptedFiles: ".jpg,.jpeg,.bmp,.gif,.png,.txt,.doc,.xls,.pdf,.docx,.xlsx,.ppt,.pps,.pptx,.csv,.xlsm,.msg",
					acceptedFiles: "",
					imageSrc: "/img/new-icon/draganddrop.png",
					dictDefaultMessage: "<?php __('Drag & Drop your document or browse your folders');?>",
					autoProcessQueue: false,
					addRemoveLinks: true,  
					maxFiles: 1,
					dictRemoveFile: '<?php __('Remove file');?>',
				});
				_Dropzone.on("queuecomplete", function(file) {
					if(_reload && !_redirect) location.reload();
					else _form[0].reset();
					_form.closest('.loading-mark').removeClass('loading');
				});
				_Dropzone.on("success", function(file) {
					_Dropzone.removeFile(file);
					var _function = _form_id.replace(/-/g, '_') + '_afterSubmit';
					// console.log( _function);
					_form.closest('.loading-mark').removeClass('loading');
					if( typeof window[_function] == 'function'){
						var _xhr = '';
						var respon = '';
						if ('xhr' in file)
							_xhr = file.xhr;
						if ('responseText' in _xhr)
							respon = _xhr.responseText;
						data = JSON.parse(respon);
						window[_function](_form_id, data);
					}
				});
				_form.on('submit', function(e){
					_form.closest('.loading-mark').addClass('loading');
					var msg = $(this).closest('form').find('.alert-message');
					var validate = true;
					msg.empty();
					var validate_function = 'validate_' + _form.prop('id');
					// console.log( validate_function); 
					if( typeof window[validate_function] == 'function'){
						validate = window[validate_function]();
					}
					if( !validate){
						e.preventDefault();
						_form.closest('.loading-mark').removeClass('loading');
						return;
					}
					// return;
					if(_Dropzone.files.length){
						e.preventDefault();
						// console.log( 'upload file');
						_Dropzone.processQueue();
					}else{
						if( !_reload){
							e.preventDefault();
							
							// console.log( 'ajax');
							$.ajax({
								url: _form.prop('action'),
								type: 'POST',
								dataType: 'json',
								data: _form.serialize(),
								success: function(data) {
									var _function = _form_id.replace(/-/g, '_') + '_afterSubmit';
									// console.log( _function);
									if( typeof window[_function] == 'function'){
										window[_function](_form_id, data);
									}
								},
								complete: function(){
									_form.closest('.loading-mark').removeClass('loading');
								}
							
							})
						}
						else{
							// console.log( 'submit');
						}
					}
					
				});
				_Dropzone.on('sending', function(file, xhr, formData) {
					// Append all form inputs to the formData Dropzone will POST
					var data = _form.serializeArray();
					$.each(data, function(key, el) {
						formData.append(el.name, el.value);
					});
				});
			});
		});
	}
	popup_dropzone();
	function back_to_last_popup(elm){
		// var popup = $(elm);
		cancel_popup(elm, false);
		var popup_width = show_workload ? 1080 : 580;
		show_full_popup( '#template_add_task', {width: popup_width}, false);
	}
	// wd_radio_button();
	$(document).ready(function(){
		$('.gantt-parent').find('.gantt-line-desc.gantt-line-n').hide();
		
	});
	/*
	* Workload table
	* init: list_assigned, c_task_data, show_workload
	*/
	var c_task_data = {}; // Continous task data 
	var list_assigned = {}; // Continous task data 
	var md_resource = {}; // Continous task data 
	var workload_resource = {};
	function template_add_task_showed(){
		init_multiselect('#template_add_task .wd-multiselect');
	}
	function c_calcTotal(ele){
		if($(ele).length > 0 && showDisponibility == 1){
			e_id = $(ele).data('id');
			wl_old = workload_resource[e_id] ? workload_resource[e_id] : 0;
			wl_new = $(ele).val();
			col_id = e_id.split('-')
			avai_refer =  $('#task_assign_table').find('.c_manday[data-id="'+ col_id[0] +'"]');
			if(avai_refer.length > 0){
				avai_value = $(avai_refer).text() ? parseFloat($(avai_refer).text()) : 0;
				wl_change = wl_new - wl_old;
				if($(avai_refer).hasClass('c_overload')){
					avai_value = (avai_value * -1) - wl_change;
				}else{
					avai_value = avai_value - wl_change;
				}
				avai_value = avai_value.toFixed(2);
				if(avai_value < 0){
					avai_refer.addClass('c_overload');
					avai_value = '+'+ avai_value * -1;
				}else{
					avai_refer.removeClass('c_overload');
				}
				avai_refer.empty().text(avai_value);
			}
			workload_resource[e_id] = wl_new;
			c_task_data['workload_resource'] = [];
			c_task_data['workload_resource'] = workload_resource; 
		}
		 var _total = 0;
		$.each( $('#task_assign_table').find('.c_workload'), function(ind, inp){
			_total += $(inp).val() ? parseFloat($(inp).val()) : 0;
		});
		c_task_data['estimated'] = _total;
		$('#task_assign_table').find('.total-consumed').text( _total.toFixed(2) );
			
		
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
		workload_resource = {};
		$.each(list_assigned, function(ind, emp){
			var id = emp.id,
				name = emp.name;
			var employee = (emp.id).split('-');
			var e_id = employee[0];
			var is_profit_center = employee[1]==1 ? 1 : 0;
			var e_manday = (e_id in md_resource) ? md_resource[e_id] : 0;
			var tag = '.c_workload-' + id;
			if( $(tag).length == 0){
				var _avt = '<span style="margin-bottom: 0px;margin-top: 10px;" class="circle-name" title="' + name + '" data-id="' + id + '">';
				if( is_profit_center == 1 ){
					_avt += '<i class="icon-people"></i>';
				}else{
					_avt += '<img width = 35 height = 35 src="'+  js_avatar( e_id ) +'" title = "'+ name +'" alt="avatar"/>';	
				}
				_avt += '</span>';
				_avt += '<span class="header-name" title="' + name + '" >'+ name.replace(/^PC \/ /, '') +'</span>';
				var res_col = '<td class="col_employee" >' + _avt + '</td>';
				
				var e_workload = (id in _data_assigned) ? _data_assigned[id]['estimated'] : 0;
				e_workload = parseFloat(e_workload).toFixed(2);
				workload_resource[id] = parseFloat(e_workload);
				var ip_name = 'data[workloads][' + id + ']';
				var val_col = '<td style="vertical-align: middle;" class="col_workload"><input type="text" id="val-' + id + '" class="c_workload c_workload-' + id + '"  id="c_workload-' + id + '" data-id="' + id + '" value="'+ e_workload +'" name="' + ip_name + '[estimated]" onkeyup="c_calcTotal(this)"/></td>';
				var ex_class = e_manday < 0 ? 'c_overload' : '';
				var manday_col ='';
				if(showDisponibility == 1){ 
					ex_action = '';
					if( is_profit_center == 0 ){
						ex_action = 'onClick="getDataAvailability(this, '+ e_id +')';
						ex_class += ' c_employee';
					}
					manday_col ='<td style="vertical-align: middle;" class="col_manday"><span class="c_manday '+ ex_class +'" data-id="'+ e_id +'"'+ ex_action +'">'+ (e_manday < 0 ? "+"+ e_manday*-1 : e_manday) +'</span></td>';
				}
				
				var _html = '<tr class="workload-row workload-row-' + id + ' ">' + res_col + val_col + manday_col +'</tr>';
				elm.find('.nct-assign-table tbody').append( _html);
			}
		});
		c_task_data.workload_resource = workload_resource;
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
				employee_selected.push({id: key, name: val, estimated: 0});
			});
		}
		return employee_selected;
	}
	function popupnct_nct_list_assignedonChange(_this_id){
		list_assigned = get_list_assign(_this_id);
		var _form = $('#' + _this_id).closest('form');
		draw_c_workload_table(_form);
		if(list_assigned.length > 0 && showDisponibility == 1){
			var _vals = [];
			i = 0;
			$.each(list_assigned, function(idx, value){
				key = value.id.split('-');
				if(typeof md_resource[key[0]] === 'undefined'){
					_vals[i++] = value;
				}
			});
			
			if(_vals.length > 0){
				getManDayForResource(_form, project_id, _vals);
			}
		}
	}
	function cancel_popup_template_edit_task(){
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
		$('#task_assign_table').find('tbody').empty();
		multiselect_setval( $('#addProjectTemplate .wd-multiselect'),{});
	}
	/*
	* END Workload table
	*/
	function submitProjectTaskEditForm(_form){
		var _form_id = _form.prop('id');
		_form.closest('.loading-mark').addClass('loading');
		// console.log(_form_id); //ProjectTaskEditForm
		$.ajax({
			url: _form.prop('action'),
			type: 'POST',
			dataType: 'json',
			data: _form.serialize(),
			success: function(data) {
				var _function = _form_id.replace(/-/g, '_') + '_afterSubmit';
				if( typeof window[_function] == 'function'){
					window[_function](_form_id, data);
				}
			},
			complete: function(){
				_form.closest('.loading-mark').removeClass('loading');
				var tree = Ext.getCmp('pmstreepanel');
				tree.setLoading(i18n("Please wait"));
				tree.refreshSummary(function(callback){
					// tree.setLoading(false);
				});
				Ext.Ajax.on('requestcomplete', function(){
					tree.setLoading(false);
				});
			}
			
		});
	}
	function ProjectTaskEditForm_afterSubmit(form_id, data){
		var form = $('#' + form_id);
		var msg = form.find('.form-message');
		var err_msg = form.find('.alert-message');
		if( data.result == 'success'){
			if( typeof Ext != 'object'){
				location.reload();
			}else{
				// console.log(data);
				data = data.message;
				var taskID = data.id;
				var taskIdPrede = data.prede_task_id;
				var tree = Ext.getCmp('pmstreepanel');
				var _task_exist = tree.getStore().getById(taskID);
				var _task_exist_prede = 0;
				if(taskIdPrede){
					_task_exist_prede = tree.getStore().getById(taskIdPrede);
				}
				if(_task_exist){
					// Chi update cac field da edit
					var _fields = [
						'task_title',
						'task_status_id',
						'project_planed_phase_id',
						'assigned',
						'task_start_date',
						'task_end_date',
						'workload',
						'duration',
						'late',
						'manual_consumed',
					];
					var _fields_prede = [
						'task_start_date',
					];
					var old_phase = _task_exist.get('project_planed_phase_id');
					$.each(_fields, function(i, k){
						// console.log(k);
						switch(k){
							case 'task_status_id':
								stt_id = data[k];
								_task_exist.set('task_status_id', stt_id);
								$.each(listAllStatus, function(j, _st){
									if(stt_id == _st['id']){
										_task_exist.set('task_status_st',_st['status']);
										_task_exist.set('task_status_text',_st['name']);
									}
								});
								break;
							case 'project_planed_phase_id':
								_task_exist.set('project_planed_phase_id', data[k]);
								_task_exist.set('project_planed_phase_text', popup_listPhases[data[k]]['name']);
								break;
							case 'task_start_date':
							case 'task_end_date':
								_task_exist.set(k, new Date(data[k]));
								break;
							case 'workload':
								_task_exist.set('estimated', data[k]);
								break;
							case 'assigned':
								var task_assign_to_id=[], task_assign_to_text=[], is_profit_center=[], estimated_detail=[]; 
								$.each( data[k], function(j, v){
									var _e = tree.datasEmployees.filter(function(e){
										return e.task_assign_to_id == v.reference_id;
									});
									task_assign_to_id.push(v.reference_id);
									task_assign_to_text.push(_e[0]['task_assign_to_text']);
									is_profit_center.push(v.is_profit_center);
									estimated_detail.push(v.estimated);
								});
								_task_exist.set('task_assign_to_text', task_assign_to_text.join(', '));
								_task_exist.set('is_profit_center', is_profit_center);
								_task_exist.set('estimated_detail', estimated_detail);
								_task_exist.set('task_assign_to_id', task_assign_to_id);
								break;
							default:
								//task_title
								_task_exist.set(k, data[k]);
						}
					});
					if(taskIdPrede){
						$.each(_fields_prede, function(a, b){
							switch(b){
								case 'task_start_date':
									_task_exist_prede.set(b, new Date(data['prede_task_startdate']));
									break;
							}
						});
					}
					if( (old_phase != data['project_planed_phase_id']) && (_task_exist.parentNode.get('is_phase'))){
						var phase_id = data['project_planed_phase_id'];
						$.each( tree.getStore().getData().items, function(i, it){
							if(it.get('phase_id') == phase_id){
								it.appendChild(_task_exist);
							}
						});
					}
					tree.setLoading(false);
					tree.refreshView();
					if( autoRefreshGantt) refreshGanttChart();
				}
				// }else{
					// Khac cau truc nen khong dung duoc
					// update_to_ext(data)
				// }
				cancel_popup('#' + form_id, false);
			}
		}else{
			msg.empty();
			err_msg.text(data.message);
		}
		
	}
	$('#ProjectTaskEditForm').on('submit', function(){
		submitProjectTaskEditForm($(this));
		return false;
	});
	function openEditTaskNormal(taskid) {
        var popup = $('#template_edit_task');
        popup.find('.loading-mark:first').addClass('loading');
		md_resource = {};
        show_full_popup(popup, {width: 'inherit'}, false);
		if( !popup_edit_resource_loaded ){
			var _multiselect_assign_to = $('#list_task_assign_to_edit');
			listAssign = listAssign_updated;
			update_employees_to_miltiselect(listAssign, _multiselect_assign_to);
			popup_edit_resource_loaded = 1;
		}
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
						// var startTime = performance.now();
						c_task_data = data.data;
						md_resource = data.data.resource_manday;
                        $('#editTaskID').val(data.data.id).trigger('change');
                        $('#task_title').val(data.data.task_title).trigger('change');
                        $('#edit_normal-Phase').val(data.data.project_planed_phase_id).trigger('change');
						$('#edit_normal-Phase').prop('disabled', data.data.parent_id != '0');
                        $('#edit-normal-status').val(data.data.task_status_id).trigger('change');
                        var start_date = data.data.task_start_date,
							end_date = data.data.task_end_date;
						if((start_date) && (end_date)){
							var st_date = start_date.split('-');
							var en_date = end_date.split('-');
							st_date = st_date[2] + '-' + st_date[1] + '-' + st_date[0];
							en_date = en_date[2] + '-' + en_date[1] + '-' + en_date[0];
						}else{
							var st_date = '';
							var en_date = '';
						}
                        $('#editTaskStartDay').val(st_date).trigger('change');
                        $('#editTaskEndDay').val(en_date).trigger('change');
                        $('#edit_normal-manual_consumed').val(data.data.manual_consumed).trigger('change');
						$('#milestoneOfTask').val(data.data.milestone_id).trigger('change');
						$('#durationOfTask').val(data.data.duration).trigger('change');
						var consume = data.data.consume;
						popup.find('.total-consumed').text(parseFloat(consume).toFixed(2));
						popup.find('.total-workload').empty().append( '<input name="data[ProjectTask][estimated]" type="number" min="0"  step="0.01" value="'+ parseFloat(data.data.estimated).toFixed(2) +'" rel="no-history">' );
						init_multiselect('#template_edit_task .wd-multiselect');
						$('#ProjectTaskEditForm').find('.nct-assign-table tbody').empty();
                        set_assigned(popup.find('.list_task_assign_to_edit'), data.data.assigned);
						list_task_assign_to_editonChange('list_task_assign_to_edit');
						resetOptionAssigned(data.employees_actif, '#list_task_assign_to_edit');
						// var endTime = performance.now();console.log(`Call to doSomething took ${(endTime - startTime)} milliseconds`);
                    } else {
						c_task_data = {};
                        show_form_alert('#ProjectTask', data.message);
                    }
                } else {
					c_task_data = {};
                    show_form_alert('#ProjectTask', "<?php __('Get task failed');?>");
                }
                popup.find('.loading-mark:first').removeClass('loading');
            },
            complete: function () {
                set_width_popup(popup);
            },
            error: function () {
                show_form_alert('#ProjectTask', "<?php __('Get task failed');?>");
                popup.find('.loading-mark:first').removeClass('loading');
            }
        });
    }
	function set_width_popup(elm){
		$.each( elm.find('.wd-row-inline'), function (i, _row){
			var _row = $(_row);
			var _width = 0;
			$.each( _row.children(), function( j, _col){
				_width += $(_col).width()+45;
			});
			_row.width(_width);
		});
	}
	function editDuration(_this){
		if( !_this) return;
		var dura = $('#durationOfTask').val();
        var st_date = $('#editTaskStartDay').val();
        var en_date = $('#editTaskEndDay').val();
		$.ajax({
			url: '/project_tasks/calculate_enddate/',
			type: 'POST',
			data:{
				data:{
					duration: dura,
					start_date: st_date,
					end_date: en_date,
				}
			},
			dataType: 'json',
			success: function(data){
				if(data){
					$('#editTaskStartDay').val(data.start_date).trigger('change');
                    $('#editTaskEndDay').val(data.end_date).trigger('change');
				}
			}
		});
	}
	function editTaskValidated(_this) {
        var cur_st_date = st_date = $('#editTaskStartDay').val();
        var cur_en_date = en_date = $('#editTaskEndDay').val();
		
        st_date = st_date.split('-');
        en_date = en_date.split('-');
		var arr_st_date = st_date;
		var arr_en_date = en_date;
		
		
        st_date = new Date(st_date[2], st_date[1], st_date[0]);
        en_date = new Date(en_date[2], en_date[1], en_date[0]);
		
		//Ticket #1115.Auto edit startdate or endDate. No border red.
		if(c_task_data['task_start_date']) c_task_data['task_start_date'] = arr_st_date[2] +'-'+ arr_st_date[1] +'-'+  arr_st_date[0];
        if (st_date > en_date) {
			$('#editTaskEndDay').val(cur_st_date);
			if(c_task_data['task_end_date']) c_task_data['task_end_date'] = arr_st_date[2] +'-'+ arr_st_date[1] +'-'+  arr_st_date[0];
        }else{
			if(c_task_data['task_end_date']) c_task_data['task_end_date'] = arr_en_date[2] +'-'+ arr_en_date[1] +'-'+  arr_en_date[0];
		}
		$('#editTaskStartDay').css('border-color', '');
        $('#editTaskEndDay').css('border-color', '');
        editTask_date_validated = 1;
		
		var _form = $(_this).closest('form');
		if(list_assigned.length > 0 && showDisponibility == 1 && editTask_date_validated == 1){
			getManDayForResource(_form, project_id, list_assigned);
		}
    }
	function set_assigned(elm, datas) {		
		var $list_ids = [];$.each(datas, function (ind, data) {
			if(data.is_profit_center == 1){
				$list_ids.push(data.reference_id + '-1')
			}else{
				$list_ids.push(data.reference_id + '-0')
			}          
        });
		var wddata = elm.find('.wd-data');
		var area_append = elm.find('.wd-combobox');
		area_append.find('.circle-name').remove();
		$.each(wddata, function(i, _t){
			var _this = $(_t);
			var check_box = _this.find(':checkbox');
			var val = check_box.val();
			if( $.inArray(val, $list_ids) != -1){
				_this.addClass('checked');
				check_box.prop('checked', true);
				var circle_name = '';
				var title = '';
				if( _this.find('.circle-name').length){
					title = _this.find('.circle-name').attr('title');
					circle_name = _this.find('.circle-name').html();
					
				}else{
					title = _this.find('.option-name').text();
					var _val = val.split('-');
					var is_pc = _val[1]==1 ? _val[1] : 0;
					var _e_id = _val[0];
					var _avt = is_pc ? '<i class="icon-people"></i>' : ('<img width = 35 height = 35 src="'+  js_avatar( _e_id ) +'" title = "'+ title +'" />');
					circle_name = '<span data-id="' + val + '">' + _avt + '</span></a>';
				}
				var multiselect_required = elm.find('.multiselect_required:first');
				elm.addClass('has-val');
				multiselect_required.val('1');
				area_append.append('<a class="circle-name" title="' + title + '">' + circle_name + '</a>');
			}else{
				_this.removeClass('checked');
				check_box.prop('checked', false);
			}
		});
    }
	function getManDayForResource(_form, project_id, employees) {
		
		$.each(employees, function (idx, _valu){
			_key = _valu.id.split('-');
			$(_form).find('.c_manday[data-id="'+ _key[0] +'"]').addClass('loading');
		});
		var task_data = c_task_data;
		task_data['resource_manday'] = [];
        $.ajax({
            url: '/project_tasks/getManDayForResource/',
            type: 'POST',
            data: {
                data: {
					project_id: project_id,
                    employees: employees,
					task_data: task_data
                }
            },
            dataType: 'json',
            success: function (data) {
                if (data) {
				   $.each(data, function(i, _val){
					   md_resource[i] = _val;
					   if(_val < 0){
						    $(_form).find('.c_manday[data-id="'+ i +'"]').addClass('c_overload');
							_val = '+'+ _val * -1;
					   }else{
						  $(_form).find('.c_manday[data-id="'+ i +'"]').removeClass('c_overload');
					   }
					  $(_form).find('.c_manday[data-id="'+ i +'"]').empty().text(_val);
				   });
				   edit_calcTotal();
                }
					
            },
            complete: function () {
               $('span.c_manday').removeClass('loading');
            },
            error: function () {
               
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
	function list_task_assign_to_editonChange(_this_id){
		list_assigned = get_list_assign(_this_id);

		var _form = $('#' + _this_id).closest('form');
		draw_c_workload_table_with_consumed(_form);
		
		if(list_assigned.length > 0 && showDisponibility == 1){
			var _vals = [];
			i = 0;
			$.each(list_assigned, function(idx, value){
				key = value.id.split('-');
				if(typeof md_resource[key[0]] === 'undefined'){
					_vals[i++] = value;
				}
			});
			
			if(_vals.length > 0){
				getManDayForResource(_form, project_id, _vals);
			}
		}	
	}
	function draw_c_workload_table_with_consumed(elm){
		if( !elm) return;
		workload_resource = {};
		if(list_assigned.length == 0){
			elm.find('.nct-assign-table tbody').empty();
			edit_calcTotal();
			elm.find('.total-workload').empty().append( '<input name="data[ProjectTask][estimated]" min="0"  step="0.01" type="number" value="'+ parseFloat(c_task_data.estimated).toFixed(2) +'" rel="no-history">');
			return;
		}
		var _data_assigned = {};
		if(c_task_data && c_task_data.assigned){
			$.each(c_task_data.assigned, function(ind, emp){
				var key = emp.reference_id + '-' + emp.is_profit_center;
				_data_assigned[key] = emp;
			});
		}
		
		var curr_edit = elm.find('.nct-assign-table tbody').find('.c_workload');
		if( curr_edit.length){
			$.each( curr_edit, function(i,t){
				var _t = $(t);
				_data_assigned[_t.data('id')] = {
					estimated: _t.val(),
					is_profit_center: "0",
					reference_id: _t.data('id')
				};
			});
		}
		if(c_task_data.assigned.length === 0 && list_assigned.length > 0){
			list_assigned[0]['estimated'] = c_task_data.estimated;
			// console.log(list_assigned);
		}
		elm.find('.nct-assign-table tbody').empty();
		var consumeds = c_task_data.consumeds;
		var resource_manday = c_task_data.resource_manday;
		$.each(consumeds, function(e_id, e_cs){
			if( listEmployee[e_id] != undefined){
				list_assigned.push( { id: e_id + '-0', name: listEmployee[e_id]});
			}
		});
		show_consumed = ( (!is_manual_consumed) && (adminTaskSetting['Consumed'] != 0));
		var total_workload = 0;
		$.each(list_assigned, function(ind, emp){
			var id = emp.id,
				name = emp.name;
			var employee = (emp.id).split('-');
			var e_id = employee[0];
			var is_profit_center = employee[1]==1 ? 1 : 0;
			var e_workload = (id in _data_assigned) ? _data_assigned[id]['estimated'] : 0;
			workload_resource[id] = parseFloat(e_workload);
			var e_consumed = 0;
			var e_manday = (e_id in md_resource) ? md_resource[e_id] : 0;
			var tag = '.c_workload-' + id;
			if( $(tag).length == 0){
				var _avt = '<span style="margin-bottom: 0px;margin-top: 10px;" class="circle-name" title="' + name + '" data-id="' + id + '">';
				if( is_profit_center == 1 ){
					_avt += '<i class="icon-people"></i>';
				}else{
					_avt += '<img width = 35 height = 35 src="'+  js_avatar( e_id ) +'" title = "'+ name +'" alt="avatar"/>';	
					if(e_id in consumeds) e_consumed = consumeds[e_id];
				}
				_avt += '</span>';
				_avt += '<span class="header-name" title="' + name + '" >'+ name.replace(/^PC \/ /, '') +'</span>';
				var res_col = '<td class="col_employee" >' + _avt + '</td>';
				e_workload = parseFloat(e_workload).toFixed(2);
				total_workload = parseFloat(total_workload) + parseFloat(e_workload);
				e_consumed = parseFloat(e_consumed).toFixed(2);
				var ip_name = 'data[workloads][' + id + ']';
				var workload_col = '<td style="vertical-align: middle;" class="col_workload"><input type="number" min="0" step="0.01" id="val-' + id + '" class="c_workload c_workload-' + id + '"  id="c_workload-' + id + '" data-id="' + id + '" value="'+ e_workload +'" name="' + ip_name + '[estimated]" onkeyup="edit_calcTotal(this)"/></td>';
				var consumed_col = show_consumed ? '<td style="vertical-align: middle;" class="col_consumed"><span class="c_consumed" data-id="' + id + '" value="'+ e_consumed +'" ></span>' + e_consumed + '</td>' : '';
				var ex_class = e_manday < 0 ? 'c_overload' : '';
				var manday_col ='';
				if(showDisponibility == 1){ 
					ex_action = '';
					if( is_profit_center == 0 ){
						ex_action = 'onClick="getDataAvailability(this, '+ e_id +')';
						ex_class += ' c_employee';
					}
					manday_col ='<td style="vertical-align: middle;" class="col_manday"><span class="c_manday '+ ex_class +'" data-id="'+ e_id +'"'+ ex_action +'">'+ (e_manday < 0 ? "+"+ e_manday*-1 : e_manday) +'</span></td>';
				}
				var _html = '<tr class="workload-row workload-row-' + id + ' ">' + res_col + workload_col +  consumed_col + manday_col +'</tr>';
				elm.find('.nct-assign-table tbody').append( _html);
			}
		});
		
		c_task_data.workload_resource = workload_resource;
		if(c_task_data.assigned.length === 0 && total_workload < c_task_data.estimated){
			$(elm).find('input#val-'+ list_assigned[0]['id']).val(list_assigned[0]['estimated']);
			c_task_data['workload_resource'][list_assigned[0]['id']] = list_assigned[0]['estimated']; 
		}
		c_task_data.estimated = total_workload;
		
		edit_calcTotal();
	}
	function edit_calcTotal(ele){
		if($(ele).length > 0 && showDisponibility == 1){
			e_id = $(ele).data('id');
			wl_old = workload_resource[e_id] ? workload_resource[e_id] : 0;
			wl_new = $(ele).val();
			col_id = e_id.split('-')
			avai_refer =  $('#edit_task_assign_table').find('.c_manday[data-id="'+ col_id[0] +'"]');
			if(avai_refer.length > 0){
				avai_value = $(avai_refer).text() ? parseFloat($(avai_refer).text()) : 0;
				wl_change = wl_new - wl_old;
				if($(avai_refer).hasClass('c_overload')){
					avai_value = (avai_value * -1) - wl_change;
				}else{
					avai_value = avai_value - wl_change;
				}
				avai_value = avai_value.toFixed(2);
				if(avai_value < 0){
					avai_refer.addClass('c_overload');
					avai_value = '+'+ avai_value * -1;
				}else{
					avai_refer.removeClass('c_overload');
				}
				avai_refer.empty().text(avai_value);
			}
			workload_resource[e_id] = wl_new;
			
			c_task_data['workload_resource'] = [];
			c_task_data['workload_resource'] = workload_resource; 
		}
        $.each( $('.task-workload .nct-assign-table'), function( ind, elm){
			if($('.c_workload').length > 0){
				var _total = 0;
				$.each( $(elm).find('.c_workload'), function(ind, inp){
					_total += $(inp).val() ? parseFloat($(inp).val()) : 0;
				});
				$(elm).find('.total-workload').text( _total.toFixed(2) );
				c_task_data['estimated'] = _total.toFixed(2);
			}
        });
    }
	function getDataAvailability(ele, employee_id){
		$(ele).addClass('loading');
		var _startDate = (c_task_data.task_start_date).replace('/', '-');
		var _endDate = (c_task_data.task_end_date).replace('/', '-');
		 $.ajax({
			url : '/project_tasks/getVocationDetail/' + employee_id + '/' + _startDate + '/' + _endDate,
			dataTye: 'json',
			type: 'POST',
			data: {
				employee_id, 
				task_data: c_task_data,
				current_project_id: project_id
			}, 
			success: function(data){
				data = JSON.parse(data);
				drawPopupAvailability(data);
				$(ele).removeClass('loading');
			},
			error: function(message){
			}
		});
	}
	function drawPopupAvailability(datas){
		var widthDivRight = 0;
        var countRow = 0;
        function init(){
            $('#filter_year').removeClass('ch-current');
            $('#filter_month').removeClass('ch-current');
            $('#filter_date').addClass('ch-current');
            var headers = avais = vocs = work = over = working = capacity = '';
            var totalCount = totalVacation = 0;
			var totalWorking = parseFloat($('#total-working').text());
            if(datas.vocation){
                headers += '<tr>';
                $.each(datas.vocation, function(index, values){
                    var count = 0;
                    $.each(values, function(ind, val){
                        count++;
                        totalCount++;
                        totalVacation += parseFloat(val);
                    });
                    headers += '<td colspan="' + count + '" class="text-center">' + index + '</td>';
                });
                headers += '</tr><tr>';
                avais += '<tr id="total-avai-popup">';
                vocs += '<tr id="total-vocs-popup">';
                work += '<tr id="total-workload-popup">';
                over += '<tr id="total-over-popup">';
                capacity += '<tr id="total-capacity-popup">';
                working += '<tr id="total-working-popup">';
                $.each(datas.vocation, function(index, values){
                    $.each(values, function(ind, val){
                        ind = ind.split('-');
                        var keyWl = index+'-'+ind[1]+'-'+ind[0];
                        ind = ind[0]+'-'+datas.dayMaps[ind[1]];
                        widthDivRight += 50;
                        headers += '<td><div class="text-center">' + ind + '</div></td>';
                        var _vais = '';
                        if(val == 1 ){
                            _vais = 0;
                        }
                        var _wokingday=1;
                        _capacity=parseFloat(_wokingday)-parseFloat(val);
                        avais += '<td><div id="avai-' + keyWl + '">' + _vais + '</div></td>';
                        vocs += '<td><div id="vocs-' + keyWl + '">' + val + '</div></td>';
                        work += '<td><div id="' + keyWl + '">' + 0 + '</div></td>';
                        over += '<td><div id="over-' + keyWl + '">' + 0 + '</div></td>';
                        capacity += '<td><div id="capacity-' + keyWl + '">' + _capacity + '</div></td>';
                        working += '<td><div id="working-' + keyWl + '">' + _wokingday + '</div></td>';
                    });
                });
                headers += '</tr>';
                avais += '</tr>';
                vocs += '</tr>';
                work += '</tr>';
                over += '</tr>';
                capacity += '</tr>';
                working += '</tr>';
            }
			if(!(totalWorking > 0 && totalWorking == totalVacation)){
				$(".popup-header-2").html(headers);
				$(".popup-availa-2").html(avais);
				$(".popup-over-2").html(over);
				$(".popup-vaication-2").html(vocs);
				$(".popup-workload-2").html(work);
				$(".popup-capacity-2").html(capacity);
				$(".popup-working-2").html(working);
			}
            // phan detail cua task
            var listTaskDisplay = '';
            var valTaskDisplay = '';
            var totalWorkload = [];
            var listSumFamily = [];
            var totalFamily = [];
			countRow = 0;
            if(datas.listDateDatas || datas.listTotalDatas){
				var listData = datas.listDateDatas;
				if(totalWorking > 0 && totalWorking == totalVacation){
					listData = datas.listTotalDatas;
				}
                $.each(listData, function(idFamily, values){
					countRow++;
                    var familyName = datas.families[idFamily] ? datas.families[idFamily] : '';
                    listTaskDisplay += '<tr class="family-group"><td><div style="font-weight: bold;">&nbsp;' + familyName + '</div></td><td><div>&nbsp;</div></td><td class="ch-fam"><div id="total-fam-'+idFamily+'">&nbsp;</div></td></tr>';
                    valTaskDisplay += '<tr class="family-group">';
                    $.each(datas.vocation, function(index, values){
                        $.each(values, function(ind, val){
                            ind = ind.split('-');
                            ind = index+'-'+ind[1]+'-'+ind[0];
                            valTaskDisplay += '<td><div id="fam-'+idFamily+'-'+ind+'">&nbsp;</div></td>';
                        });
                    });
                    valTaskDisplay += '</tr>';
                    var sttActivity = 0;
                    $.each(values, function(idGlobal, value){
                        sttActivity++;
                        idGlobal = idGlobal.split('-');
                        if(idGlobal[0] === 'ac'){
                            var activityName = datas.groupNames.activity[idGlobal[1]] ? datas.groupNames.activity[idGlobal[1]] : '';
                            listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">&nbsp;'+ sttActivity +'. ' + activityName + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
                            valTaskDisplay += '<tr class="project-activity-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
                            var sttTask = 0;
							countRow++;
                            $.each(value, function(idTask, valTask){
                                sttTask++;
                                valTaskDisplay += '<tr>';
                                //var idPriority = datas.priority.activity[idTask] ? datas.priority.activity[idTask] : 0;
                                var priorities = datas.priority.activity[idTask] ? datas.priority.activity[idTask] : '';
                                var activityTaskName = datas.groupNameTasks.activity[idTask] ? datas.groupNameTasks.activity[idTask] : '';
                                listTaskDisplay += '<tr><td class="list-task"><div>&nbsp;'+ sttActivity +'.'+ sttTask +'. ' + activityTaskName + '</div></td><td><div>' + priorities + '</div></td><td><div>&nbsp;</div></td></tr>';
								countRow++;
                                $.each(datas.vocation, function(index, values){
                                    $.each(values, function(ind, val){
                                        ind = ind.split('-');
                                        ind = index+'-'+ind[1]+'-'+ind[0];
                                        var _value = valTask[ind] ? valTask[ind] : 0;
                                        if(val == 1){
                                            _value = 0;
                                        }
                                        valTaskDisplay += '<td><div>' + _value + '</div></td>';
                                        if(!totalWorkload[ind]){
                                            totalWorkload[ind] = 0;
                                        }
                                        totalWorkload[ind] += parseFloat(_value);;
                                        if(!listSumFamily[idFamily+'-'+ind]){
                                            listSumFamily[idFamily+'-'+ind] = 0;
                                        }
                                        listSumFamily[idFamily+'-'+ind] += parseFloat(_value);;
                                        if(!totalFamily[idFamily]){
                                            totalFamily[idFamily] = 0;
                                        }
                                        totalFamily[idFamily] += parseFloat(_value);;
                                    });
                                });
                                valTaskDisplay += '</tr>';
                            });
                        } else if(idGlobal[0] === 'pr'){
                            var projectName = datas.groupNames.project[idGlobal[1]] ? datas.groupNames.project[idGlobal[1]] : '';
                            listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">&nbsp;'+ sttActivity +'. ' + projectName + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
                            valTaskDisplay += '<tr class="project-activity-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
                            var sttTask = 0;
							countRow++;
                            $.each(value, function(idTask, valTask){
                                sttTask++;
                                valTaskDisplay += '<tr>';
                                //var idPriority = datas.priority.project[idTask] ? datas.priority.project[idTask] : 0;
                                var priorities =  datas.priority.project[idTask] ?  datas.priority.project[idTask] : '';
                                var projectTaskName = datas.groupNameTasks.project[idTask] ? datas.groupNameTasks.project[idTask] : '';
                                listTaskDisplay += '<tr><td class="list-task"><div>&nbsp;'+ sttActivity +'.'+ sttTask +'. ' + projectTaskName + '</div></td><td><div>' + priorities + '</div></td><td><div>&nbsp;</div></td></tr>';
								countRow++;
								if(typeof(valTask) === 'object'){
									$.each(datas.vocation, function(index, values){
										$.each(values, function(ind, val){
											ind = ind.split('-');
											ind = index+'-'+ind[1]+'-'+ind[0];
											var _value = valTask[ind] ? valTask[ind] : 0;
											if(val == 1){
												_value = 0;
											}
											valTaskDisplay += '<td><div>' + _value + '</div></td>';
											if(!totalWorkload[ind]){
												totalWorkload[ind] = 0;
											}
											totalWorkload[ind] += parseFloat(_value);
											if(!listSumFamily[idFamily+'-'+ind]){
												listSumFamily[idFamily+'-'+ind] = 0;
											}
											listSumFamily[idFamily+'-'+ind] += parseFloat(_value);;
											if(!totalFamily[idFamily]){
												totalFamily[idFamily] = 0;
											}
											totalFamily[idFamily] += parseFloat(_value);;
										});
									});
								}else{
									if(!totalFamily[idFamily]){
										totalFamily[idFamily] = 0;
									}
									totalFamily[idFamily] += parseFloat(valTask);
								}
                                valTaskDisplay += '</tr>';
                            });
                        } else {
                            //do nothing
                        }
                    });
                });
            }
            var totalWorkloads = totalAvais = 0;
            $('#total-workload-popup').find('td div').each(function(){
                var getId = $(this).attr('id');
                var getTotalWl = totalWorkload[getId] ? totalWorkload[getId].toFixed(2) : 0;
                totalWorkloads += parseFloat(getTotalWl);
                var getAvais=0;
                var vocs = $('#total-vocs-popup').find('#vocs-'+getId).html();
                if(vocs == 1){
                    getAvais = 0;
                }else if(vocs == 0.5){
                    getAvais = 0.5 - getTotalWl;
                }else{
                    getAvais = 1 - getTotalWl;
                }
                if (!isNaN(getAvais) && getAvais.toString().indexOf('.') != -1){
                    getAvais = getAvais.toFixed(2);
                }
                totalAvais += parseFloat(getAvais);
                if(getAvais < 0 ) {
                    getOvers = -1*parseFloat(getAvais);
                    getAvais=0;
                } else {
                    getOvers=0;
                }
                $('#total-avai-popup').find('#avai-'+getId).html(getAvais);
                $('#total-over-popup').find('#over-'+getId).html(getOvers);
                $('#'+getId).html(getTotalWl);
            });
            totalWorkloads = totalWorkloads.toFixed(2);
            totalAvais = totalAvais.toFixed(2);
            if(totalAvais < 0){
                totalOver=parseFloat(totalAvais) * (-1);
                totalAvais = 0;
            } else {
                totalOver=0;
            }
		
            $('#total-overload').html(totalOver);
            $('#total-vacation').html(totalVacation);
            $('#total-workload').html(totalWorkloads);

            $(".popup-task-detail").html(listTaskDisplay);
			if(!(totalWorking > 0 && totalWorking == totalVacation)){
				$(".popup-task-detail-2").html(valTaskDisplay);
			}
            $('.popup-task-detail-2').find('.family-group td div').each(function(){
                var idDivOfFamily = $(this).attr('id');
                var idCheck = idDivOfFamily.replace('fam-', '');
                var valSumFam = listSumFamily[idCheck] ? listSumFamily[idCheck].toFixed(2) : 0;
                $('#'+idDivOfFamily).html(valSumFam);
            });
			var sum_overload = 0;
            $('.popup-task-detail').find('td.ch-fam div').each(function(){
                var idDivOfFamily = $(this).attr('id');
                var idCheck = idDivOfFamily.replace('total-fam-', '');
                var valSumFam = totalFamily[idCheck] ? totalFamily[idCheck].toFixed(2) : 0;
				sum_overload += parseFloat(valSumFam);
                $('#'+idDivOfFamily).css('text-align', 'right');
                $('#'+idDivOfFamily).html(valSumFam);
            });
			if(totalWorking > 0 && totalWorking == totalVacation){
				// Truong hop dayoff, absence, holiday ca duration task
				$('#total-overload').empty().html(sum_overload.toFixed(2));
				$('#total-workload').empty().html(sum_overload.toFixed(2));
				widthDivRight = 0;
			}
        }
        function initMonth(){
            $('#filter_month').addClass('ch-current');
            var headers = avais = vocs = work = over = working = capacity = '';
            var totalCount = totalVacation = totalCapacity = totalWorking = 0;
            widthDivRight = 0;
			
            if(datas.vocationMonth){
                headers += '<tr>';
                $.each(datas.vocationMonth, function(index, values){
                    var count = 0;
                    $.each(values, function(ind, val){
                        count++;
                        totalCount++;
                        totalVacation += parseFloat(val);
                        totalWorking += parseFloat(datas.working[index][ind]);
                    });
                    headers += '<td colspan="' + count + '" class="text-center">' + index + '</td>';
                });
                headers += '</tr><tr>';
                avais += '<tr id="total-avai-popup">';
                vocs += '<tr id="total-vocs-popup">';
                work += '<tr id="total-workload-popup">';
                over += '<tr id="total-over-popup">';
                capacity += '<tr id="total-capacity-popup">';
                working += '<tr id="total-working-popup">';
                $.each(datas.vocationMonth, function(index, values){
                    if(values){
                        $.each(values, function(ind, val){
                            var keyWl = index+'-'+ind;
                            widthDivRight += 50;
                            headers += '<td><div class="text-center">' + ind + '</div></td>';
                            var _vais = '';
                            if(val == 1){
                                _vais = 0;
                            }
                            var _wokingday=datas.working[index][ind];
                            var _capacity=parseFloat(_wokingday)-parseFloat(val);
                            avais += '<td><div id="avai-' + keyWl + '">' + _vais + '</div></td>';
                            vocs += '<td><div id="vocs-' + keyWl + '">' + val + '</div></td>';
                            work += '<td><div id="' + keyWl + '">' + 0 + '</div></td>';
                            over += '<td><div id="over-' + keyWl + '">' + 0 + '</div></td>';
                            capacity += '<td><div id="capacity-' + keyWl + '">' + _capacity + '</div></td>';
                            working += '<td><div id="working-' + keyWl + '">' + _wokingday + '</div></td>';
                        });
                    }
                });
                headers += '</tr>';
                avais += '</tr>';
                vocs += '</tr>';
                work += '</tr>';
                over += '</tr>';
                capacity += '</tr>';
                working += '</tr>';
            }
			if(!(totalWorking > 0 && totalWorking == totalVacation)){
				$(".popup-header-2").html(headers);
				$(".popup-availa-2").html(avais);
				$(".popup-over-2").html(over);
				$(".popup-vaication-2").html(vocs);
				$(".popup-workload-2").html(work);
				$(".popup-capacity-2").html(capacity);
				$(".popup-working-2").html(working);
			}

            // phan detail cua task
            var listTaskDisplay = '';
            var valTaskDisplay = '';
            var totalWorkload = [];
            var listSumFamily = [];
            var totalFamily = [];
			countRow = 0;
			
            if(datas.listMonthDatas || datas.listTotalDatas){
				var listData = $.isEmptyObject(datas.listMonthDatas) ? datas.listTotalDatas : datas.listMonthDatas;
				if(listData){
					$.each(listData, function(idFamily, values){
						
						var familyName = datas.families[idFamily] ? datas.families[idFamily] : '';
						listTaskDisplay += '<tr class="family-group"><td><div style="font-weight: bold;">&nbsp;' + familyName + '</div></td><td><div>&nbsp;</div></td><td class="ch-fam"><div id="total-fam-'+idFamily+'">&nbsp;</div></td></tr>';
						countRow++;
						valTaskDisplay += '<tr class="family-group">';
						$.each(datas.vocationMonth, function(index, values){
							$.each(values, function(ind, val){
								ind = index+'-'+ind;
								valTaskDisplay += '<td><div id="fam-'+idFamily+'-'+ind+'">&nbsp;</div></td>';
							});
						});
						valTaskDisplay += '</tr>';
						var sttActivity = 0;
						$.each(values, function(idGlobal, value){
							sttActivity++;
							idGlobal = idGlobal.split('-');
							if(idGlobal[0] === 'ac'){
								var activityName = datas.groupNames.activity[idGlobal[1]] ? datas.groupNames.activity[idGlobal[1]] : '';
								listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">&nbsp;'+ sttActivity +'. ' + activityName + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
								countRow++;
								valTaskDisplay += '<tr class="project-activity-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
								var sttTask = 0;
								$.each(value, function(idTask, valTask){
									sttTask++;
									valTaskDisplay += '<tr>';
									//var idPriority = datas.priority.activity[idTask] ? datas.PriorityActivityTasks[idTask] : 0;
									var priorities = datas.priority.activity[idTask] ? datas.priority.activity[idTask] : '';
									var activityTaskName =  datas.groupNameTasks.activity[idTask] ?  datas.groupNameTasks.activity[idTask] : '';
									listTaskDisplay += '<tr><td class="list-task"><div>&nbsp;'+ sttActivity +'.'+ sttTask +'. ' + activityTaskName + '</div></td><td><div>' + priorities + '</div></td><td><div>&nbsp;</div></td></tr>';
									countRow++;
									$.each(datas.vocationMonth, function(index, values){
										$.each(values, function(ind, val){
											ind = index+'-'+ind;
											var _value = valTask[ind] ? valTask[ind] : 0;
											valTaskDisplay += '<td><div>' + _value + '</div></td>';
											if(!totalWorkload[ind]){
												totalWorkload[ind] = 0;
											}
											totalWorkload[ind] += parseFloat(_value);;
											if(!listSumFamily[idFamily+'-'+ind]){
												listSumFamily[idFamily+'-'+ind] = 0;
											}
											listSumFamily[idFamily+'-'+ind] += parseFloat(_value);;
											if(!totalFamily[idFamily]){
												totalFamily[idFamily] = 0;
											}
											totalFamily[idFamily] += parseFloat(_value);;
										});
									});
									valTaskDisplay += '</tr>';
								});
							} else if(idGlobal[0] === 'pr'){
								var projectName = datas.groupNames.project[idGlobal[1]] ? datas.groupNames.project[idGlobal[1]] : '';
								// console.log(projectName);
								listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">&nbsp;'+ sttActivity +'. ' + projectName + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
								countRow++;
								valTaskDisplay += '<tr class="project-activity-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
								var sttTask = 0;
								
								$.each(value, function(idTask, valTask){
									sttTask++;
									valTaskDisplay += '<tr>';
									//var idPriority = datas.priority.project[idTask] ? datas.priority.project[idTask] : 0;
									var priorities = datas.priority.project[idTask] ? datas.priority.project[idTask] : '';
									var projectTaskName = datas.groupNameTasks.project[idTask] ? datas.groupNameTasks.project[idTask] : '';
									listTaskDisplay += '<tr><td class="list-task"><div>&nbsp;'+ sttActivity +'.'+ sttTask +'. ' + projectTaskName + '</div></td><td><div>' + priorities + '</div></td><td><div>&nbsp;</div></td></tr>';
									countRow++;
									if(typeof(valTask) === 'object'){
										$.each(datas.vocationMonth, function(index, values){
											$.each(values, function(ind, val){
												ind = index+'-'+ind;
												var _value = valTask[ind] ? valTask[ind] : 0;
												valTaskDisplay += '<td><div>' + _value + '</div></td>';
												if(!totalWorkload[ind]){
													totalWorkload[ind] = 0;
												}
												totalWorkload[ind] += parseFloat(_value);
												if(!listSumFamily[idFamily+'-'+ind]){
													listSumFamily[idFamily+'-'+ind] = 0;
												}
												listSumFamily[idFamily+'-'+ind] += parseFloat(_value);
												if(!totalFamily[idFamily]){
													totalFamily[idFamily] = 0;
												}
												totalFamily[idFamily] += parseFloat(_value);
											});
										});
									}else{
										if(!totalFamily[idFamily]){
											totalFamily[idFamily] = 0;
										}
										totalFamily[idFamily] += parseFloat(valTask);
									}
									valTaskDisplay += '</tr>';
								});
							} else {
								// do no thing
							}
						});
					});
				}
            }
			
            var totalWorkloads = totalAvais = 0;
            $('#total-workload-popup').find('td div').each(function(){
                var getId = $(this).attr('id');
                var getTotalWl = totalWorkload[getId] ? totalWorkload[getId].toFixed(2) : 0;
                totalWorkloads += parseFloat(getTotalWl);
                var getAvais = datas.avaiTotalMonth[getId] ? datas.avaiTotalMonth[getId] : 0;
                totalAvais += parseFloat(getAvais);
                if(parseFloat(getAvais) < 0 ) {
                    getOvers = parseFloat(getAvais) * (-1);
                    getAvais=0;
                } else {
                    getOvers=0;
                }

                $('#total-avai-popup').find('#avai-'+getId).html(getAvais);
                $('#total-over-popup').find('#over-'+getId).html(getOvers);
                $('#'+getId).html(getTotalWl);
            });
            totalWorkloads = totalWorkloads.toFixed(2);
            totalAvais = totalAvais.toFixed(2);
            if(totalAvais < 0){
                totalOver = parseFloat(totalAvais) * (-1);
                totalAvais = 0;
            } else {
                totalOver = 0;
            }
            totalCapacity=totalWorking-totalVacation;
		
            $('#total-availability').html(totalAvais);
            $('#total-overload').html(totalOver);
            $('#total-vacation').html(totalVacation);
            $('#total-workload').html(totalWorkloads);
            $('#total-capacity').html(totalCapacity);
            $('#total-working').html(totalWorking);

            $(".popup-task-detail").html(listTaskDisplay);
			if(!(totalWorking > 0 && totalWorking == totalVacation)){
				$(".popup-task-detail-2").html(valTaskDisplay);
			}
            $('.popup-task-detail-2').find('.family-group td div').each(function(){
                var idDivOfFamily = $(this).attr('id');
                var idCheck = idDivOfFamily.replace('fam-', '');
                var valSumFam = listSumFamily[idCheck] ? listSumFamily[idCheck].toFixed(2) : 0;
                $('#'+idDivOfFamily).html(valSumFam);
            });
			var sum_overload = 0;
            $('.popup-task-detail').find('td.ch-fam div').each(function(){
                var idDivOfFamily = $(this).attr('id');
                var idCheck = idDivOfFamily.replace('total-fam-', '');
                var valSumFam = totalFamily[idCheck] ? totalFamily[idCheck].toFixed(2) : 0;
				sum_overload += parseFloat(valSumFam);
                $('#'+idDivOfFamily).css('text-align', 'right');
                $('#'+idDivOfFamily).html(valSumFam);
            });
			
			if(totalWorking > 0 && totalWorking == totalVacation){
				// Truong hop dayoff, absence, holiday ca duration task
				$('#total-overload').empty().html(sum_overload.toFixed(2));
				$('#total-workload').empty().html(sum_overload.toFixed(2));
				widthDivRight = 0;
			}
        }
        //init();
        initMonth();
        //filter
        $("#filter_year").click(function(e){
            $('#filter_date').removeClass('ch-current');
            $('#filter_month').removeClass('ch-current');
            $(this).addClass('ch-current');
            var headers = avais = vocs = work = over = working = capacity = '';
            var totalCount = totalVacation = totalWorking = 0;
            widthDivRight = 0;
			var totalWorking = parseFloat($('#total-working').text());
            if(datas.vocationYear){
				
                headers += '<tr class="popup-header">';
                avais += '<tr id="total-avai-popup">';
                vocs += '<tr id="total-vocs-popup">';
                work += '<tr id="total-workload-popup">';
                over += '<tr id="total-over-popup">';
                capacity += '<tr id="total-capacity-popup">';
                working += '<tr id="total-working-popup">';
                $.each(datas.vocationYear, function(index, values){
                    totalCount++;
                    totalVacation += parseFloat(values);
                    var _wokingday = 0 ;
                    $.each(datas.working[index], function(ind, values){
                        _wokingday+=datas.working[index][ind];
                    });
                    var _capacity=parseFloat(_wokingday)-parseFloat(values);
                    headers += '<td class="text-center tb-year">' + index + '</td>';
                    avais += '<td><div id="avai-' + index + '">' + 0 + '</div></td>';
                    vocs += '<td><div id="vocs-' + index + '">' + values + '</div></td>';
                    work += '<td><div id="' + index + '">' + 0 + '</div></td>';
                    over += '<td><div id="over-' + index + '">' + 0 + '</div></td>';
                    capacity += '<td><div id="capacity-' + index + '">' + 0 + '</div></td>';
                    working += '<td><div id="working-' + index + '">' + 0 + '</div></td>';
                    widthDivRight += 50;
                });
            }
            headers += '</tr>';
            avais += '</tr>';
            vocs += '</tr>';
            work += '</tr>';
            over += '</tr>';
            capacity += '</tr>';
            working += '</tr>';
			if(!(totalWorking > 0 && totalWorking == totalVacation)){
				$(".popup-header-2").html(headers);
				$(".popup-availa-2").html(avais);
				$(".popup-over-2").html(over);
				$(".popup-vaication-2").html(vocs);
				$(".popup-workload-2").html(work);
				$(".popup-capacity-2").html(capacity);
				$(".popup-working-2").html(working);
			}
            // phan detail cua task
            var listTaskDisplay = '';
            var valTaskDisplay = '';
            var totalWorkload = [];
            var listSumFamily = [];
            var totalFamily = [];
			countRow = 0;
            if(datas.listYearDatas || datas.listTotalDatas){
				var listData = $.isEmptyObject(datas.listYearDatas) ? datas.listTotalDatas : datas.listYearDatas;
                $.each(listData, function(idFamily, values){
                    var familyName = datas.families[idFamily] ? datas.families[idFamily] : '';
                    listTaskDisplay += '<tr class="family-group"><td><div style="font-weight: bold;">&nbsp;' + familyName + '</div></td><td><div>&nbsp;</div></td><td class="ch-fam"><div id="total-fam-'+idFamily+'">&nbsp;</div></td></tr>';
					countRow++;
                    valTaskDisplay += '<tr class="family-group">';
                    $.each(datas.vocationYear, function(index, values){
                        valTaskDisplay += '<td><div id="fam-'+idFamily+'-'+index+'">&nbsp;</div></td>';
                    });
                    valTaskDisplay += '</tr>';
                    var sttActivity = 0;
                    $.each(values, function(idGlobal, value){
                        sttActivity++;
                        idGlobal = idGlobal.split('-');
                        if(idGlobal[0] === 'ac'){
                            var activityName = datas.groupNames.activity[idGlobal[1]] ? datas.groupNames.activity[idGlobal[1]] : '';
                            listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">&nbsp;'+ sttActivity +'. ' + activityName + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
							countRow++;
                            valTaskDisplay += '<tr class="project-activity-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
                            var sttTask = 0;
                            $.each(value, function(idTask, valTask){
                                sttTask++;
                                valTaskDisplay += '<tr>';
                                //var idPriority = datas.priority.activity[idTask] ? datas.priority.activity[idTask] : 0;
                                var priorities = datas.priority.activity[idTask] ? datas.priority.activity[idTask] : '';
                                var activityTaskName = datas.groupNameTasks.activity[idTask] ? datas.groupNameTasks.activity[idTask] : '';
                                listTaskDisplay += '<tr><td class="list-task"><div>&nbsp;'+ sttActivity +'.'+ sttTask +'. ' + activityTaskName + '</div></td><td><div>' + priorities + '</div></td><td><div>&nbsp;</div></td></tr>';
								countRow++;
                                $.each(datas.vocationYear, function(index, values){
                                    var _value = valTask[index] ? valTask[index] : 0;
                                    valTaskDisplay += '<td><div>' + _value + '</div></td>';
                                    if(!totalWorkload[index]){
                                        totalWorkload[index] = 0;
                                    }
                                    totalWorkload[index] += parseFloat(_value);
                                    if(!listSumFamily[idFamily+'-'+index]){
                                        listSumFamily[idFamily+'-'+index] = 0;
                                    }
                                    listSumFamily[idFamily+'-'+index] += parseFloat(_value);

                                    if(!totalFamily[idFamily]){
                                        totalFamily[idFamily] = 0;
                                    }
                                    totalFamily[idFamily] += parseFloat(_value);
                                });
                                valTaskDisplay += '</tr>';
                            });
                        } else if(idGlobal[0] === 'pr'){
                            var projectName = datas.groupNames.project[idGlobal[1]] ? datas.groupNames.project[idGlobal[1]] : '';
                            listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">&nbsp;'+ sttActivity +'. ' + projectName + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
							countRow++;
                            valTaskDisplay += '<tr class="project-activity-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
                            var sttTask = 0;
							
                            $.each(value, function(idTask, valTask){
                                sttTask++;
                                valTaskDisplay += '<tr>';
                                //var idPriority = datas.priority.project[idTask] ? datas.priority.project[idTask] : 0;
                                var priorities = datas.priority.project[idTask] ? datas.priority.project[idTask] : '';
                                var projectTaskName = datas.groupNameTasks.project[idTask] ? datas.groupNameTasks.project[idTask] : '';
                                listTaskDisplay += '<tr><td class="list-task"><div>&nbsp;'+ sttActivity +'.'+ sttTask +'. ' + projectTaskName + '</div></td><td><div>' + priorities + '</div></td><td><div>&nbsp;</div></td></tr>';
								countRow++;
								if(typeof(valTask) === 'object'){
									$.each(datas.vocationYear, function(index, values){
										var _value = valTask[index] ? valTask[index] : 0;
										valTaskDisplay += '<td><div>' + _value + '</div></td>';
										if(!totalWorkload[index]){
											totalWorkload[index] = 0;
										}
										totalWorkload[index] += parseFloat(_value);
										if(!listSumFamily[idFamily+'-'+index]){
											listSumFamily[idFamily+'-'+index] = 0;
										}
										listSumFamily[idFamily+'-'+index] += parseFloat(_value);

										if(!totalFamily[idFamily]){
											totalFamily[idFamily] = 0;
										}
										totalFamily[idFamily] += parseFloat(_value);
									});
								}else{
									if(!totalFamily[idFamily]){
										totalFamily[idFamily] = 0;
									}
									totalFamily[idFamily] += parseFloat(valTask);
								}
                                valTaskDisplay += '</tr>';
                            });
                        } else {
                            // do nothing
                        }
                    });
                });
            }
            var totalWorkloads = totalAvais = 0;
            $('#total-workload-popup').find('td div').each(function(){
                var getId = $(this).attr('id');
                var getTotalWl = totalWorkload[getId] ? totalWorkload[getId].toFixed(2) : 0;
                totalWorkloads += parseFloat(getTotalWl);
                var getAvais = datas.avaiTotalYear[getId] ? datas.avaiTotalYear[getId] : 0;
                totalAvais += parseFloat(getAvais);
                if(getAvais < 0 ) {
                    getOvers = parseFloat(getAvais) * (-1);
                    getAvais=0;
                } else {
                    getOvers=0;
                }
                $('#total-avai-popup').find('#avai-'+getId).html(getAvais);
                $('#total-over-popup').find('#over-'+getId).html(getOvers);
                $('#'+getId).html(getTotalWl);
            });
            totalWorkloads = totalWorkloads.toFixed(2);
            totalAvais = totalAvais.toFixed(2);
            if(totalAvais < 0){
                totalOver = totalAvais*(-1);
                totalAvais = 0;
            } else {
                totalOver = 0;
            }
			
            $('#total-availability').html(totalAvais);
            $('#total-overload').html(totalOver);
            $('#total-vacation').html(totalVacation);
            $('#total-workload').html(totalWorkloads);


            $(".popup-task-detail").html(listTaskDisplay);
			if(!(totalWorking > 0 && totalWorking == totalVacation)){
				$(".popup-task-detail-2").html(valTaskDisplay);
			}
            $('.popup-task-detail-2').find('.family-group td div').each(function(){
                var idDivOfFamily = $(this).attr('id');
                var idCheck = idDivOfFamily.replace('fam-', '');
                var valSumFam = listSumFamily[idCheck] ? listSumFamily[idCheck].toFixed(2) : 0;
                $('#'+idDivOfFamily).html(valSumFam);
            });
			var sum_overload = 0;
            $('.popup-task-detail').find('td.ch-fam div').each(function(){
                var idDivOfFamily = $(this).attr('id');
                var idCheck = idDivOfFamily.replace('total-fam-', '');
                var valSumFam = totalFamily[idCheck] ? totalFamily[idCheck].toFixed(2) : 0;
				sum_overload += parseFloat(valSumFam);
                $('#'+idDivOfFamily).css('text-align', 'right');
                $('#'+idDivOfFamily).html(valSumFam);
            });
			if(totalWorking > 0 && totalWorking == totalVacation){
				// Truong hop dayoff, absence, holiday ca duration task
				$('#total-overload').empty().html(sum_overload.toFixed(2));
				$('#total-workload').empty().html(sum_overload.toFixed(2));
				widthDivRight = 0;
			}
            configPopup(widthDivRight);
            return false;
        });
        $("#filter_month").click(function(e){
            $('#filter_date').removeClass('ch-current');
            $('#filter_year').removeClass('ch-current');
            //filter year
            initMonth();
            configPopup(widthDivRight);
            return false;
        });
        $("#filter_week").click(function(e){
            //filter year
            return false;
        });
        var flag=0;
        $("#filter_date").click(function(e){
            $('#filter_month').removeClass('ch-current');
            $('#filter_year').removeClass('ch-current');
            //filter year
            init();
			configPopup(widthDivRight);
            return false;
        });

        // config cho phan hien thi popup
        function configPopup(withRight){
            var lWidth = $(window).width();
            var DialogFull = Math.round((95*lWidth)/100);
            var tableLeft = 450 + 90 + 60;
            var tableRight = withRight + 1;
			var popupWidth = tableLeft + tableRight + 50;
			if(popupWidth > DialogFull){
				popupWidth = DialogFull;
				tableRight = popupWidth - tableLeft - 39;
			}
            $('#gs-popup-content').width(popupWidth - 35);
            $('.table-left').width(tableLeft);
            $('.table-right').width(tableRight);
            $('#tb-popup-content-2').width(tableRight - 10);
            var lHeight =  $(window).height();
            var DialogFullHeight = Math.round((80*lHeight)/100);
			var contentTable = countRow * 32 + 245;
			var popupHeight = DialogFullHeight;
			if(contentTable < DialogFullHeight - 100){
				 $('#gs-popup-content').height(contentTable);
				 popupHeight = contentTable + 100;
			}else{
				$('#gs-popup-content').height(DialogFullHeight - 100);
			}
            $( "#showdetail" ).dialog({
                modal: true,
				position    :'center',
                autoHeight  : true,
                width: popupWidth,
                zIndex: 9999999,
				position: [(lWidth - popupWidth) / 2, (lHeight - popupHeight)/ 2],
            }, {title: ''}
			);
        }
        configPopup(widthDivRight);
	}
	$('#newTaskName').on('change', function(){
		c_task_data.task_title = $(this).val();
	});
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
	
	function before_batch_edit_tasks(tasks){
		ajaxGetResources(project_id);
	}
	$("#batchEditTaskStartDay, #batchEditTaskEndDay").datepicker({
		dateFormat      : 'dd-mm-yy',
		onClose : function(date, elm){
			if( date != ''){
				dependentDateValidate(elm.input, $('#batchEditTaskStartDay'), $('#batchEditTaskEndDay'));
			}
			return true;
		},
	});
	function dependentDateValidate(cur, prevElm, nextElm){
        var st_date = prevElm.datepicker( "getDate" );
        var en_date = nextElm.datepicker( "getDate" );
        if( cur[0] == prevElm[0]){
			if( !en_date ){
				var _newdate = new Date(st_date);
				_newdate.setDate( _newdate.getDate());
				nextElm.datepicker( "setDate",  _newdate).trigger('change');
			}
			var _min = new Date(st_date);
			nextElm.datepicker( "option", "minDate", st_date ? _min : '');
		}
		if( cur[0] == nextElm[0]){
			if( !st_date ){
				var _newdate = new Date(en_date);
				_newdate.setDate( _newdate.getDate());
				prevElm.datepicker( "setDate",  _newdate).trigger('change');
			}
			var _max = new Date(en_date);
			prevElm.datepicker( "option", "maxDate", en_date ? _max : '');	
		}
		return true;
    }
	
	function template_batch_edit_task_showed(){
		init_multiselect('#template_batch_edit_task .wd-multiselect');
	}
	function batchReplaceAssignedtoonChange(){}
	function batchAddAssignedtoonChange(){}
	function batchEditAssignedtoonChange(){
		// var multiSelect = $('#batchAddAssignedto');
		// _list_selected = multiSelect.find(':checkbox:checked');
		// var _show_warning = (_list_selected.length) && ($('#batchEditWorkload').val() !=='');
		// var _field_mesage = multiSelect.closest('form').find('.form-field-message.for-batchUpdateWorkload');
		// if( _field_mesage.length) _field_mesage.toggleClass('show', _show_warning);
		// return true;
	}
	// $('#batchUpdateWorkload').on('change', function(){
		// var multiSelect = $('#batchAddAssignedto');
		// var _this = $(this);
		// _list_selected = multiSelect.find(':checkbox:checked');
		// var _show_warning = (!_list_selected.length) && (_this.val() !=='');
		// var _field_mesage = multiSelect.closest('form').find('.form-field-message.for-batchEditWorkload');
		// if( _field_mesage.length) _field_mesage.toggleClass('show', _show_warning);
		
	// });
	$('#batchUpdateWorkload, #batchAddWorkload, #batchReplaceWorkload').attr('maxlength' , 18).keypress(function(e){
		var key = e.keyCode ? e.keyCode : e.which;
		var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
		if(val == '0' || !/^[\-]?([0-9]{0,16})(\.[0-9]{0,2})?$/.test(val)){
			e.preventDefault();
			return false;
		}		
	});
	
	$('#project_container').on('mousedown', function(e){
		if( $.isEmptyObject( treepanel)) return true;
		var _this =  $(e.target);
		_selectIconClicked = {
			clicked: false,
			index:  0,
			isSelected: false,
			old_status: _selectIconClicked.clicked
		};
		if( _this.hasClass( 'x-tree-elbow') || _this.hasClass( 'x-tree-elbow-end')){
			var _index = _this.closest('.x-grid-item').data('recordindex');
			var _task = treepanel.getStore().getAt( _index );
			var _canSelect = (!( _task.hasChildNodes() || _task.get('is_phase') || _task.get('is_part') || _task.isRoot() ));
			if( _canSelect ){
				_selectIconClicked = {
					clicked: 	true,
					index: 		_index,
					isSelected: treepanel.getSelectionModel().isSelected(_task),
					task: 		_task,
					old_status	: _selectIconClicked.old_status
				};
			}
		} 
	});
	$(document).on('keydown', function(e) {
		var key = e.which ? e.which : e.keyCode;
		_keyPressed[key] = true;
		$(this).on('keyup', function(e) {
			var key = e.which ? e.which : e.keyCode;
			_keyPressed[key] = false;
		});
	});
	$('#batchUpdateAssigned, #batchReplaceAssigned').on('change', function(){
		$('#forBatchUpdate').toggle($('#batchUpdateAssigned').is(':checked'));
		$('#forBatchReplace').toggle($('#batchReplaceAssigned').is(':checked'));
	});
	/*
	$('#ProjectTaskBatchEditForm').on('submit', function(e){
		e.preventDefault();
		var _form = $(this);
		var _form = $('#ProjectTaskBatchEditForm');
		var formData = new FormData(_form[0]);
		var formURL = _form.attr("action");
		$.ajax({
			url: formURL,
			type: 'POST',
			data:  formData,
			mimeType:"multipart/form-data",
			async: false,
			cache: false,
			contentType: false,
			processData: false,
			beforeSend: function(){
				_form.closest('.loading-mark').addClass('loading');
			},
			success: function(response){
				response = JSON.parse(response);
				console.log( response);
                cancel_popup(_form[0], false);
                treepanel.setLoading(i18n('Please wait'));
				treepanel.refreshSummary(function(callback){
				   treepanel.setLoading(false);
				});
				treepanel.refreshStaffing(function(callback){
					//do nothing
				});
				treepanel.refreshView();
				if( autoRefreshGantt) refreshGanttChart();
			},
			complete: function(){
				_form.closest('.loading-mark').removeClass('loading');
			}
		});
		return false;
	});
	*/
</script>
<?php }