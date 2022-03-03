<?php
$arg = $this->passedArgs;
$arg["?"] = $this->params['url'];
unset($arg['?']['url'], $arg['?']['ext']);
$type = 'monthyear';
$show_workload = !(empty($adminTaskSetting['Workload'])) ? $adminTaskSetting['Workload'] : 0;
$check_consumed = (!empty($adminTaskSetting) && (($adminTaskSetting['Consumed'] == 1)||($adminTaskSetting['Manual Consumed'] == 1))) ? 1 : 0;
echo $html->css('jqwidgets/jqx.base'); 
echo $html->css('dropzone.min'); 
echo $html->css('layout_2019'); 
echo $html->css('add_popup'); 
echo $html->css('preview/datepicker-new'); 
echo $html->css(array('jquery.fancybox')); 
echo $html->script('dropzone.min'); 
echo $html->script('jqwidgets/jqxcore'); 
echo $html->script('jqwidgets/jqxsortable'); 
echo $html->script('jqwidgets/jqxkanban'); 
echo $html->script('jqwidgets/jqxdata'); 
echo $html->script('jqwidgets/demos'); 
echo $html->script('draw-progress'); 
echo $html->script('preview/define_limit_date'); 
echo $html->script('jquery-ui.multidatespicker'); 
echo $html->script(array('jquery.fancybox.pack')); 
echo $html->css(array('projects','slick_grid/slick.edit','gantt','project_task','business', 'preview/grid-project', 'preview/kanban','preview/component'));
echo $html->css('slick'); 
echo $html->css('slick-theme');  
echo $html->css('preview/availability-popup');  
echo $html->script('slick.min'); 
echo $html->script('history_filter'); 

$canModified = !empty($canModified) ? $canModified : 0;
$_isProfile = !empty($_isProfile) ? $_isProfile : 0;
$_canWrite = !empty($_canWrite) ? $_canWrite : 0;
$read_only = !(($canModified && !$_isProfile) || $_canWrite) ? 1 : 0;
$display_activity_forecast = isset($companyConfigs['display_activity_forecast']) ? $companyConfigs['display_activity_forecast'] : 0;
$display_disponibility = isset($companyConfigs['display_disponibility']) ? $companyConfigs['display_disponibility'] : 0;
$showDisponibility = !empty($display_activity_forecast) && !empty($display_disponibility ) ?  1 : 0;

$svg_icons = array(
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
			</svg>',
	'delete' => '<svg xmlns="http://www.w3.org/2000/svg" width="20.03" height="20" viewBox="0 0 20.03 20">
			  <defs>
				<style>
				  .cls-1 {
					fill: #666;
					fill-rule: evenodd;
				  }
				</style>
			  </defs>
			  <path id="suppr" class="cls-1" d="M6644.04,275a0.933,0.933,0,0,1-.67-0.279l-8.38-8.374-8.38,8.374a0.954,0.954,0,1,1-1.35-1.347l8.38-8.374-8.38-8.374a0.954,0.954,0,0,1,1.35-1.347l8.38,8.374,8.38-8.374a0.933,0.933,0,0,1,.67-0.279,0.953,0.953,0,0,1,.67,1.626L6636.33,265l8.38,8.374A0.953,0.953,0,0,1,6644.04,275Z" transform="translate(-6624.97 -255)"/>
			</svg>',
	'message' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
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
	'time' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
	  <defs>
		<style>
		  .cls-1 {
			fill: #666;
			fill-rule: evenodd;
		  }
		</style>
	  </defs>
	  <path id="TIME" class="cls-1" d="M633,395a10,10,0,1,1,10-10A10,10,0,0,1,633,395Zm0-18.065A8.065,8.065,0,1,0,641.064,385,8.074,8.074,0,0,0,633,376.935Zm4.516,9.033h-5.484v-6.129a0.968,0.968,0,0,1,1.936,0v4.193h3.548A0.968,0.968,0,0,1,637.516,385.968Z" transform="translate(-623 -375)"/>
	</svg>',
	'list' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
		<defs>
		<style>
			.cls-1 {
				fill: #666;
				fill-rule: evenodd;
			}
		</style>
		</defs>
		<path id="Mode_List" data-name="Mode List" class="cls-1" d="M434,38V36h16v2H434Zm0-9h16v2H434V29Zm0-7h16v2H434V22Zm-4,14h2v2h-2V36Zm0-7h2v2h-2V29Zm0-7h2v2h-2V22Z" transform="translate(-430 -20)"></path>
		</svg>');
$listPCAssign = $listEmployeeAssign = array();
//Update by QuanNV. Thay vi check dieu kien trong JS. Gan icon = '' khi khong co quyen.
if($canModified == 0){
	$svg_icons['edit'] = '';
	$svg_icons['delete'] = '';
}
foreach ($listEmployee as $key => $value) {
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
	foreach($fielData as $index => $value):
		$employee = $value['Employee'];
		if($employee['is_profit_center'] != 1):
			$avatar = '<img src="' . $_this->UserFile->avatar($employee['id']) . '" />';
			$cotentField .= '<div class="projectManager wd-data-manager wd-group-' . $employee['id'] .' actif-'.$employee['actif'].'">
				<p class="projectManager wd-data">
					' .
					$_this->Form->input($fieldName, array(
						'label' => false,
						'div' => false,
						'type' => 'checkbox',
						'id' => $id.'-'.$employee['id'].'-0',
						'name' => 'data['. $fieldName .'][]',
						'value' => $employee['id'] . '-0')) .'
					<span class="option-name" style="padding-left: 5px;">' . $employee['name'] . '</span>
				</p>
			</div>';
		endif;
	endforeach;
	if(!empty($pc)): 
		foreach($pc as $idPm => $namePm):
			$cotentField .= '<div class="projectManager wd-data-manager wd-group-' . $idPm . '">
				<p class="projectManager wd-data">
					'.
					 $_this->Form->input($fieldName, array(
						'label' => false,
						'div' => false,
						'type' => 'checkbox',
						'id' => $id.'-'.$idPm . '-1',
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
<style> 
.wd-kanban-wrapper .wd-kanban-container{
	width:<?php echo 350*count($listProjectStatus);?>px !important;
	min-width: 100%;
}
.list-all-assigned .loading-icon:after{
	background-size: 20px;
}
#template_logs #content_comment .content{
	margin-bottom: 0;
}
#template_logs #content_comment #content-comment-detail{
	max-height: 280px;
}
#template_logs #content_comment{
	height: initial;
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
.edit_task-popup.loading-mark.wd-popup-container {
    min-width: 1100px;
}
form#ProjectTask {
    min-height: 510px;
}
.edit_task-popup .wd-row-inline{
	z-index: 2;
	position: relative;
	overflow: visible;
	min-height: 350px;
}
.edit_task-popup .wd-submit-row {
    z-index: 1;
    position: relative;
}

#template_logs #content_comment .content {
    display: inline-block;
	width: calc(100% - 22px);
}
#template_logs .content-tast-text{
    padding-right: 0;
}
#template_logs .content-tast-text .content-employee{
	width: calc(100% - 73px);
    display: inline-block;
    max-width: calc(100% - 50px);
}
#template_logs .content_comment .cm-delete{
	display: inline-block;
    width: 20px;
    vertical-align: middle;
    font-size: 16px;
    color: #888;
    top: 10px;
    position: relative;
}
#template_logs .content_comment .cm-delete:hover{
	color: #F05352;
}
#template_logs .content_comment .cm-delete.loading:after{
	content: '';
    width: 15px;
    height: 15px;
    border: 2px solid #E1E6E8;
    border-top-color: #F05352;
    border-radius: 50%;
    animation: wd-rotate 2s infinite;
    position: absolute;
    left: -1px;
    top: -1px;
}
</style> 


<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout wd-layout-kanban">
        <div class="wd-kanban-heading">
            <div class="wd-kanban-heading-inner">
				<?php echo $this->Form->create('KanbanFilter', array(
						'type' => 'GET',
						'url' => $this->Html->url(),
						// 'class' => 'form-style-2019',
						'id' => 'KanbanFilter',
					));
					$filter_time = array(
						// '' => __("All", true),
						"day" => __("Day", true),
						"week" => __("Week", true),
						"month" => __("Month", true),
						"late" => __("Late", true),
					);
					
				?>
				<div class="wd-flex-title">
                <div class="wd-task-title wd-title">
                    <a href="<?php echo $html->url('/project_tasks/index/'.$projectName['Project']['id']) ?>" title="<?php echo __('Tasks list', true); ?>"><?php echo $svg_icons['list'] ?></a>
					<?php echo $this->Form->input('filter_by_time', array(
						'type'=> 'select',
						'id' => 'filter_by_time',
						// 'label' => __('Filter by time', true),
						// 'div' => false,
						'label' => false,
						'required' => false,
						'autocomplete' => 'off',
						'onchange'=> 'filterTask(this);',
						'options' => $filter_time,
						'empty' => __("All", true),
						// 'rel' => 'no-history',
						// 'div' => array(
							// 'class' => 'wd-input label-inline'
						// )
					));
					echo $this->Form->input('filter_task_title', array(
						'type'=> 'text',
						'id' => 'filter_task_title',
						// 'label' => __('Filter by title', true),
						'label' => false,
						// 'div' => false,
						'required' => false,
						'autocomplete' => 'off',
						'onchange'=> 'filterTask(this);',
						// 'rel' => 'no-history',
						'placeholder' => __('Filter by title', true),
					));
					
					?>
					<div class="btn filter_submit" onclick="filterTask(this);" title="<?php __('Search');?>">
						<img src="<?php echo $this->Html->url('/img/new-icon/search.png');?>" all="search">
					</div>
					<a href="javascript:void(0);" class="btn btn-reset-filter" id="clean-filters" style="display: none;" onclick="resetFilter();" title="<?php __('Reset filter') ?>"></a>
					<?php if(empty($localDataTask)){?>
						<a href="javascript:void(0)" class="icon-plus" id="add_task" onclick="open_add_new_task();"></a>
					<?php }?>
                </div>
                <div class="wd-list-assign">
                    <div class="wd-list-assign-inner">
                        <div class="box-ellipsis list-all-assigned" id="list-all-assigned">
                            <?php if(!empty($listAvata)){ ?>
                            <ul>
								<?php foreach ($listAvata as $key => $value) {
									foreach( $value as $_id => $_name){
										$avt = '<i class="icon-people"></i>';
										$is_pc = 1;
										if($key == 'listEmployeeAssigned'){
											$avt = '<img src="' . $this->UserFile->avatar($_id) .'" alt="avatar">';
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
                <div style="clear : both"></div>
            </div>
        </div>
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>

            <div class="wd-tab">
                <div class="wd-kanban-wrapper loading-mark">
                <div class="wd-kanban-container">
                    <div id="kanban1" class="kanban-task loading" style=""></div>   
                </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="template_logs" style="height: 440px; width: 320px;display: none;">
	<?php if(!$read_only){?>
		<div class="add-comment"></div>
	<?php }?>
    <div id="content_comment" class="content_comment">
		<div class="append-comment"></div>
    </div>  
</div>
<div id="attach-logs">
    <div class="attach-content">
    </div>
</div>
<div id="file-upload" style="display: none;">
    <div class="heading">
        <span class="close"><img title="close"  src="<?php echo $html->url('/img/new-icon/close.png'); ?>"/></span>
    </div>
    <div class="file-content">
    </div>
</div>
<div id="template_upload" class="template_upload" style="height: auto; width: 320px;">
    <div class="heading">
        <h4><?php echo __('File upload(s)', true)?></h4>
        <span class="close close-popup"><img title="close"  src="<?php echo $html->url('/img/new-icon/close.png'); ?>"/></span>
    </div>
    <div id="content_comment">
        <div class="append-comment"></div>
    </div> 
    <div class="wd-popup">
        <?php 
        echo $this->Form->create('Upload', array(
            'type' => 'POST',
            'url' => array('controller' => 'kanban','action' => 'update_document', $projectName['Project']['id'])));
            ?>
			<?php if(!$read_only) {?>
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
				<input type="hidden" name="data[Upload][controller]" rel="no-history" value="kanban">
			<?php }?>
        <?php echo $this->Form->end(); ?>
    </div>
	<?php if( !$read_only ){ ?>
		<ul class="actions" style="">
			<li><a href="javascript:void(0)" class="cancel"><?php __("Upload Cancel") ?></a></li>
			<li><a href="javascript:void(0)" class="new" id="ok_attach"><?php __('Upload Validate') ?></a></li>
		</ul>
	<?php }?>
</div>
<div class="light-popup"></div>
<?php echo $this->element('dialog_detail_value') ?>

<!-- New task Popup -->

<div id="template_add_task_normal" class="wd-full-popup" style="display: none;">
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
				
				<?php if( $show_workload) { ?>
				<div class="wd-row wd-submit-row">
					<div class="wd-col wd-col-sm-8">
				<?php } 
				// echo $this->Form->input('return', array(
					// 'type'=> 'hidden',
					// 'value' => $this->Html->url(),
					
				// ));
				echo $this->Form->input('project_id', array(
					'type'=> 'hidden',
					'value' => $project_id,
					'rel' => 'no-history',
				));
				
				echo $this->Form->input('task_title', array(
					'type'=> 'text',
					'id' => 'newTaskName',
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
					'options' => $list_phases,
					'required' => true,
					'rel' => 'no-history',
					'div' => array(
						'class' => 'wd-input label-inline required has-val'
					),
					'onchange' => 'new_task_phase_changed(this)',
				));
				?>
				<div class="wd-row">
					<div class="wd-col wd-col-sm-6">
						<div class="wd-input wd-area wd-none ">
							<?php 
							echo multiSelect($this, array(
								'fieldName' => 'task_assign_to_id',
								'fielData' => $listEmployee,
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
							'label' => __d(sprintf($_domain, 'Project_Task'), 'Status', true),
							'options' => $projectStatus,
							'required' => true,
							'rel' => 'no-history',
							'div' => array(
								'class' => 'wd-input label-inline required has-val'
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
								// 'required' => true,
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
							'id' => 'newTaskEndDay',
							'label' => __d(sprintf($_domain, 'Project_Task'), 'End date', true),
							// 'required' => true,
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
				<?php 
				if( $show_workload) { ?>
					</div>
					<div class="wd-col wd-col-sm-4">
						<div class="task-workload">
							<table id="new_task_assign_table" class="nct-assign-table">
								<thead>
									<tr>
										<td width = 120  class="bold base-cell null-cell"><?php __('Resources');?> </td>
										<td width = 120  class="bold base-cell null-cell"><?php  __d(sprintf($_domain, 'Project_Task'), 'Workload')?></td>
										<?php if( !empty($showDisponibility)){ ?>
											<td width = 120 class="bold base-cell null-cell"><?php __('M.D availability');?> </td>
										<?php }?>
									</tr>
								</thead>
								<tbody>
									
								</tbody>
								<tfoot>
									<tr>
										<td class="base-cell"><?php __('Total') ?></td>
										<td class="base-cell total-consumed" id="new_task_total-consumed">0</td>
										<?php if( !empty($showDisponibility)){ ?>
											<td class="base-cell total-manday" id="edit_task_total_manday">0</td>
										<?php }?>
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
							<button type="submit" class="btn-form-action btn-ok btn-right" id="btnAddTask">
								<span><?php __('Save') ?></span>
							</button>
							<a class="btn-form-action btn-cancel" id="reset_button" href="javascript:void(0);" onclick="cancel_popup(this);">
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
<!-- END New task Popup -->

<!-- Edit task Popup -->
<div id="template_edit_task" class="wd-full-popup" style="display: none;">
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
					'label' => __('Task name', true),
					'required' => true,
					'rel' => 'no-history',
					'div' => array(
						'class' => 'wd-input label-inline'
					)
				));
				echo $this->Form->input('project_planed_phase_id', array(
					'type'=> 'select',
					'id' => 'toPhase',
					'label' => __('Phase', true),
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
							<label><?php __('Assign to');?></label>
							<?php 
							echo multiSelect($this, array(
								'fieldName' => 'task_assign_to_id',
								'fielData' => $listEmployee,
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
							'label' => __('Status', true),
							'options' => $projectStatus,
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
								'id' => 'editTaskStartDay',
								'label' => __('Start date', true),
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
					<div class="wd-col wd-col-sm-6">
					<?php 				
						echo $this->Form->input('task_end_date', array(
							'type'=> 'text',
							'id' => 'editTaskEndDay',
							'label' => __('End date', true),
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
				<?php 
				if( $show_workload) { ?>
					</div>
					<div class="wd-inline-col wd-col-autosize">
						<div class="task-workload">
							<table id="task_assign_table" class="nct-assign-table">
								<thead>
									<tr>
										<td width = 120  class="bold base-cell null-cell"><?php __('Resources');?> </td>
										<td width = 120  class="bold base-cell null-cell"><?php  __d(sprintf($_domain, 'Project_Task'), 'Workload')?></td>
										<?php if( empty($companyConfigs['manual_consumed']) && !empty($adminTaskSetting['Consumed']) ){ ?>
											<td width = 120 class="bold base-cell null-cell "><?php  __d(sprintf($_domain, 'Project_Task'), 'Consumed')?></td>
										<?php } ?> 
										<?php if( !empty($showDisponibility)){ ?>
											<td width = 120 class="bold base-cell null-cell"><?php __('M.D availability');?> </td>
										<?php }?>
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
											<td class="base-cell total-manday" id="edit_task_total_manday">0</td>
										<?php }?>
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
<!-- Show Avaibility Popup -->
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
<!-- End Avaibility Popup -->
<?php echo $this->element('dialog_projects') ?>
<div id="addProjectTemplate" class="loading-mark"></div>
<?php
$myAvatar = '';
/* $avatarEmploys = array();
foreach ($assignEmployee as $key => $value) {
    // $avatarEmploys[$value['Employee']['id']] = $this->UserFile->avatar($value['Employee']['id']);
    $avatarEmploys[$value['Employee']['id']] = $this->Html->url(array('controller' => 'employees' ,'action' => 'attachment', $value['Employee']['id'], '?' => array('sid' => $api_key)), true);
} */
$file_upload = array();
if(!empty($attachedFile)){
    foreach ($attachedFile as $key => $value) {
        if ( preg_match('/\.(jpg|jpeg|bmp|gif|png|swf)$/i', $value)) {
            $file_upload[$key] = $this->Html->url(array('action' => 'attachment', $key, '?' => array('sid' => $api_key)));
        }else{
            $file_upload[$key] = $this->Html->url(array('action' => 'attachment', $key, '?' => array('download' => true, 'sid' => $api_key)),true);
        }
    }
}
$i18n = array(
	'minute' => __('cmMinute', true),
	'hour' => __('cmHour', true),
	'day' => __('cmDay', true),
	'month' => __('cmMonth', true),
	'year' => __('cmYear', true),
	'minutes' => __('cmMinutes', true),
	'hours' => __('cmHours', true),
	'days' => __('cmDays', true),
	'months' => __('cmMonths', true),
	'years' => __('cmYears', true),
	'startday' => __('Start date', true),
	'enddate' => __('End date', true),
	'progress' => __('Progress', true),
	'resource' => __('Resource', true),
	'date' => __('Date', true),
	'Workload' => __('Workload', true),
	'Consumed' => __('Consumed', true),
	'M.D' => __('M.D', true),
	'Add new task' => __('Add new task', true),
);
for ($m=1; $m<=12; $m++) {
	$month = date('F', mktime(0,0,0,$m, 1, 2019));
	$i18n[$month] = __($month,true);
}
 ?>
<script type="text/javascript">
	HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
    var api_key = <?php echo json_encode($api_key) ?>;
	var current_time = <?php echo time() ?>;
    var projectID = <?php echo json_encode($projectName['Project']['id']) ?>;
	var project_id = <?php echo json_encode($project_id) ?>;
    var linkFile = <?php echo json_encode($file_upload) ?>;
    var attachedFile = <?php echo json_encode($attachedFile) ?>;
    var company_id = <?php echo json_encode($employee_info['Company']['id']) ?>;
    var url = <?php echo json_encode( FILES . 'projects/project_tasks/'); ?>;
    var uatManager = $('#wd-data-project').find('.wd-data-manager');
    var listEmployee = <?php echo !empty($listEmployee) ? json_encode($listEmployee) : json_encode(array());?>;
    var listProjectStatus = <?php echo json_encode($listProjectStatus)?>;
    var projectStatusEX = <?php echo json_encode($projectStatusEX)?>;
    var localDataTask = <?php echo json_encode($localDataTask)?>;
    var taskStatus = <?php echo json_encode($taskStatus)?>;
    var read_only = <?php echo json_encode($read_only)?>;
	var isTablet = <?php echo json_encode($isTablet); ?>;
    var isMobile = <?php echo json_encode($isMobile); ?>;
    var isIE = <?php echo json_encode(preg_match('/MSIE\s(?P<v>\d+)/i', @$_SERVER['HTTP_USER_AGENT'], $B)) ?>;
    var svg_icons = <?php echo json_encode($svg_icons) ?>;
	var i18n = <?php echo json_encode($i18n); ?>;
    isIE = 0;
	var c_task_data = {}; // Continous task data 
	var list_assigned = {}; // Continous task data 
	var md_resource = {}; // Continous task data 
	var workload_resource = {};
	var show_workload = <?php echo json_encode($show_workload); ?>; // Continous task data 
	var list_phase_date = <?php echo json_encode($list_phase_date); ?>;
	var check_consumed = <?php echo json_encode($check_consumed); ?>;
	var list_phases = <?php echo json_encode($list_phases); ?>;
	var list_phase_colors = <?php echo json_encode($list_phase_colors); ?>;
	var kanbanTag = '#kanban1';
	var curent_date = new Date(<?php echo json_encode(date("Y-m-d 0:0:0"));?>);
	var this_monday = new Date(<?php echo json_encode(date("Y-m-d 0:0:0", strtotime('monday this week')));?>);
	var this_sunday = new Date(<?php echo json_encode(date("Y-m-d 0:0:0", strtotime('sunday this week')));?>);
	var showDisponibility = <?php echo $showDisponibility ?>;
    var is_manual_consumed = <?php echo isset($companyConfigs['manual_consumed']) ? (string) $companyConfigs['manual_consumed'] : '0' ?>;
    var gap_linked_task = <?php echo isset($companyConfigs['gap_linked_task']) ? (string) $companyConfigs['gap_linked_task'] : '0' ?>;
    var task_no_phase = <?php echo isset($companyConfigs['task_no_phase']) ? (string) $companyConfigs['task_no_phase'] : '0' ?>;
    var create_ntc_task = <?php echo isset($companyConfigs['create_ntc_task']) ? (string) $companyConfigs['create_ntc_task'] : '0' ?>;
    var canModify = <?php echo json_encode((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite)); ?>;
    var myRole = <?php echo json_encode($myRole) ?>;
    var emp_id_login = <?php echo json_encode($emp_id_login) ?>;
    var adminTaskSetting  = <?php echo json_encode($adminTaskSetting) ?>;
    $("#btnClose").click(function(){
        $("#showdetail").hide();
    });
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
	/* Huynh
	14/09/2019 #444
	để tránh việc function filterTask chạy nhiều lần khi HistoryFilter set value
	sử dụng biến run_filter để chặn function filterTask() 
	Sau khi HistoryFilter load xong thì cho run_filter = 1;
	*/
	var run_filter = 0;	
    $('body').on( 'click', function (e) {
        // e.preventDefault();
        var _popup = $('#popup-assign.active');
        if( _popup.find(e.target).length) return;
        $(this).find('#popup-assign').removeClass('active');
    });
	// $('.wd-combobox').on('click', function(e){
 //        e.preventDefault();
 //        $(this).closest('.wd-multiselect').find('.wd-combobox-content').toggle();
 //    });
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
	// $('body').on('click', function(e){
	// 	var _multisels = $('.wd-full-popup .wd-multiselect');
	// 	$.each( _multisels, function( ind, _multisel){
	// 		_target = $(e.target);
	// 		_multisel = $(_multisel);
	// 		if( _multisel.find( e.target).length  || (_multisel.length == _target.length && _multisel.length == _multisel.filter(_target).length) ) {
	// 			return;
	// 		}
	// 		else{
	// 			_multisel.find('.wd-combobox-content').fadeOut('300');
	// 		}
	// 	});
	// });
	$("#newTaskEndDay, #newTaskStartDay").datepicker({
        dateFormat: 'dd-mm-yy'
    });
	var newtaskIndexForm_date_validated = 1;
    function new_task_validated(_this) {
		var cur_st_date = st_date = $('#newTaskStartDay').val();
        var cur_en_date = en_date = $('#newTaskEndDay').val();
		
        st_date = st_date.split('-');
        en_date = en_date.split('-');
		
		var arr_st_date = st_date;
        var arr_en_date = en_date;
		
        st_date = new Date(st_date[2], st_date[1], st_date[0]);
        en_date = new Date(en_date[2], en_date[1], en_date[0]);
		
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
        newtaskIndexForm_date_validated = 1;
		var _form = $(_this).closest('form');
		if(list_assigned.length > 0 && showDisponibility == 1 && newtaskIndexForm_date_validated == 1){
			getManDayForResource(_form, project_id, list_assigned);
		}
    }
	$('#newTaskStartDay').on('change', function(){
		new_task_validated($(this));
	});
	$('#newTaskEndDay').on('change', function(){
		new_task_validated($(this));
	});
    $("#editTaskStartDay, #editTaskEndDay").datepicker({
        dateFormat: 'dd-mm-yy'
    });
    var editTask_date_validated = 1;
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
    function new_task_phase_changed(_this) {
        var _this = $(_this);
        var _val = _this.val();
        if (_val in list_phase_date) {
            $('#newTaskStartDay').val(list_phase_date[_val]['phase_real_start_date']).trigger('change').css('border-color', '');
            $('#newTaskEndDay').val(list_phase_date[_val]['phase_real_end_date']).trigger('change').css('border-color', '');
            new_task_validated('#newTaskEndDay');
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
	jQuery.removeFromArray = function (value, arr) {
        return jQuery.grep(arr, function (elem, index) {
            return elem !== value;
        });
    };
	init_multiselect('#template_add_task_normal .wd-multiselect');
	function show_form_alert(form, msg) {
        $(form).find('.alert-message').empty().append(msg);
        clearTimeout(alert_timeout);
        alert_timeout = setTimeout(function () {
            $(form).find('.alert-message').empty();
        }, 3000);
    }
    $('#template_upload .close, #template_upload .cancel').on( 'click', function (e) {
        // e.preventDefault();
        $("#template_upload").removeClass('show');
        $(".light-popup").removeClass('show');
    });
    $('#file-upload .close').on( 'click', function (e) {
        // e.preventDefault();
        $("#file-upload").removeClass('show');
        $(".light-popup").removeClass('show');
    });
	$('#KanbanFilter').on('submit', function(e){
		e.preventDefault();
		filterTask();
		return;
	});
	HistoryFilter.afterLoad = function(){
		run_filter = 1;
		var time_filter = $('#filter_by_time').val();
		var title_filter =  $('#filter_task_title').val();
		var employee_filter = [];
		var emp_checkbox =  $('#list-all-assigned').find(':checked');
		if( emp_checkbox.length) $.each(emp_checkbox, function(i, _checkbox){
			employee_filter.push( $(_checkbox).data('id'));
		});
		if(time_filter || title_filter || employee_filter.length > 0){
			filterTask();
		}
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
				var is_IP = listProjectStatus[task.task_status_id]['status'] == "IP";
				result = ( curent_date > task_ed && is_IP);
				break;
			default:
				result = 0; 
				break;
		}
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
		var time_filter = $('#filter_by_time').val();
		var title_filter =  $('#filter_task_title').val();
		var employee_filter = [];
		var emp_checkbox =  $('#list-all-assigned').find(':checked');
		if( emp_checkbox.length) $.each(emp_checkbox, function(i, _checkbox){
			employee_filter.push( $(_checkbox).data('id'));
		});
		$(kanbanTag).closest('.loading-mark').addClass('loading');
		$('#clean-filters').show();
		var _task_display = [];
		var _new_tasks = [];
		if( taskStatus){
			$.each(taskStatus, function(_status, _tasks){
				if( _tasks){
					$.each(_tasks, function(_task_id, _task){
						if( !checkTime(_task, time_filter)){
							_task_display[_task_id] = 0;
							return true; // continue;
						}
						if( !checkTitle(_task, title_filter)){
							_task_display[_task_id] = 0;
							return true;
						}
						if( !checkAssign(_task, employee_filter)){
							_task_display[_task_id] = 0;
							return true;
						}
						_task_display[_task_id] = 1;
						_new_tasks.push(_task);
					});
				}
			});
			re_init_kanban(_new_tasks);
		}
		$('#kanban1').closest('.loading-mark').removeClass('loading');
		return;
		
	}
    function resetFilter($this){
		if($('#clean-filters').hasClass('disabled')) return;
		$('#filter_by_time').val('').trigger('change');
		$('#list-all-assigned').find(':checkbox').prop('checked', false).trigger('change');
		$('#filter_task_title').val('').trigger('change');
		$('#clean-filters').hide();
		return;
	}
    // function filterTask($this){
        // filter = $($this).find('option:selected').val();
        // url = '<?php echo $this->Html->url('/kanban/index/')?>'+ projectID + '/' + filter;
        // window.open(url,"_self");
    // }
    function setBackgrounDate(){
        var popup_date = $('.ui-datepicker-calendar');
        
    }
   

    function getTaksText() {
        id = $(this).data("id");
        var _html = '';
		var index = 0;
        var latest_update = '';
        var popup = $('#template_logs');
        $.ajax({
            url: '/project_tasks/getCommentTxt/',
            type: 'POST',
            data: {
                pTaskId: id,
            },
            dataType: 'json',
            success: function(data) {
				var task_title = data && data.task_title ? data.task_title : '';
                _html += '<div id="content-comment-detail">';
                if (data['result']) {
                    
                    data = data['result'];
                    $.each(data, function(ind, _data) {
                        if(_data){
							employee = _data['Employee'];
							cm_id = _data['ProjectTaskTxt']['id'] ? _data['ProjectTaskTxt']['id'] : '';
                            comment = _data['ProjectTaskTxt']['comment'] ? _data['ProjectTaskTxt']['comment'].replace(/\n/g, "<br>") : '';
                            date = _data['created'];
                          
                            nameEmp = employee['first_name'] +' '+ employee['last_name'];
							ava_src = '<img class="circle-name" width = 35 height = 35 src="' + js_avatar(_data['ProjectTaskTxt']['employee_id']) + '" title = "' + nameEmp + '" />';
                            _html += '<div class="content-tast-text content-' + index++ + '">';
                            _html += '<div class="content"><div class="avatar">'+ ava_src +'</div><div class="content-employee"><div class="employee-info"><p>'+ nameEmp +' '+ _data['ProjectTaskTxt']['created'] +'</p></div><div class="comment">'+ comment +'</div></div></div>';
							if(myRole == 'admin' || (_data['ProjectTaskTxt']['employee_id'] == emp_id_login)) _html += '<a class="cm-delete" href="javascript:void(0);" onclick="deleteCommentTask(this, '+ cm_id+');"><img src="/img/new-icon/delete-attachment.png"></a>';
                            _html += '</div>';
                        }                       
                    });
                } else {
                    _html += '';
                }
                _html += '</div>'
                $('#template_logs #content_comment').html(_html);
                $('#template_logs .add-comment').html('<div class="input-add"><textarea class="text-textarea" id="update-comment" data-id="'+ id +'" cols="30" rows="6" ></textarea></div>');
                var createDialog2 = function(){
                    $('#template_logs').dialog({
                        position    :'center',
                        autoOpen    : false,
                        height      : 460,
                        modal       : true,
                        width: (isTablet || isMobile) ? 320 : 520,
                        minHeight   : 50,
                        open : function(e){
                            var $dialog = $(e.target);
                            $dialog.dialog({open: $.noop});
                        }
                    });
                    createDialog2 = $.noop;
                }
                createDialog2();
                $("#template_logs").dialog('option',{title: task_title}).dialog('open');
                
            }
        });
       
    };
	
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
						$(_this).closest('.content-tast-text').remove();
						$(_this).removeClass('loading');
					}
				}			
			});
		}
	}
	
    function updateAssignTask() {
        employee_id = $(this).data("emp");
        is_profit = $(this).data("profit");
        taskid = $(this).closest('.popup-content').data("id");
        parent_element = $(this).closest('.wd-task-assign');
        if(is_profit == 1){
            _html = '<li class="assign-team"><span class="delete-employee icon-close" onclick="removeAssign.call(this)" data-emp="'+ employee_id +'"></span>'+ $(this).html() +'</li>'
        } else _html = '<li><span class="delete-employee icon-close" onclick="removeAssign.call(this)" data-emp="'+ employee_id +'"></span>'+ $(this).html() +'</li>'
        $.ajax({
            url: '/kanban/updateAssignTask/' + taskid,
            type: 'POST',
            data: {
                employee_id: employee_id,
                is_profit: is_profit,
            },
            dataType: 'json',
            success: function(data) {
                if(data == 1){
                    parent_element.find('.list-assign').append(_html);
					updateListAssigned();
                }
                
            }
        });
       
    };

    function removeAssign() {
        var employee_id = $(this).data("emp");
        var taskid = $(this).closest('.jqx-kanban-item').data("taskid");
        var task_item = $('#kanban1_'+taskid);
        var parent_ele = task_item.find('.delete-employee[data-emp="'+employee_id+'"]');
        var parent_ele_item = parent_ele.closest('li');
        $.ajax({
            url: '/kanban/deleteAssign/' + taskid,
            type: 'POST',
            data: {
                employee_id: employee_id,
            },
            dataType: 'json',
            success: function(data) {
                if(data.result == 'success'){
                    parent_ele_item.remove();
					// employee_id = employee_id.split('-');
					// var e_id = employee_id[0];
					// var is_pc = employee_id[1];
                }
				$.each(taskStatus, function(status, _tasks){
					if( taskid in _tasks ){
						task = _tasks[taskid]['assigned'] = data.data;
					}
				});
				updateListAssigned();
                
            }
        });
       
    };
    function getTaskAttachment() {
        id = $(this).data("id");
		var _this = $(this);
        var _html = '';
        var latest_update = '';
        $('#UploadId').val(id);
        var popup = $('#template_upload');
		
        $.ajax({
            url: '/kanban/getTaskAttachment/'+ id,
            type: 'POST',
            data: {
                id: id,
            },
            dataType: 'json',
            success: function(data) {
                popup.addClass('show');
                _this.addClass('read');
                _this.removeClass('un-read');
                $('.light-popup').addClass('show');
                _html = '<div class="content-attachment">';
                _html += '<ul>';
                if (data['attachments']) {
                    $.each(data['attachments'], function(ind, _data) {
                        if(_data){
                            if(_data['ProjectTaskAttachment']['is_file'] == 1){
                                if((/\.(gif|jpg|jpeg|tiff|png)$/i).test(_data['ProjectTaskAttachment']['attachment'])){ 
                                    if(linkFile[_data['ProjectTaskAttachment']['id']]) _link = linkFile[_data['ProjectTaskAttachment']['id']];
                                    else _link = '/kanban/attachment/'+ _data['ProjectTaskAttachment']['id'] +'/?sid='+ api_key;
                                    _html += '<li><i class="icon-paper-clip"></i><span href="'+ _link +'" class="fancy image" rel="one_pic_expand" data-fancybox="image" data-type="image">'+ _data['ProjectTaskAttachment']['attachment'] +'</span>'+ (!read_only ? ('<a  data-id = "'+ id +'"><img src="/img/new-icon/delete-attachment.png" alt="'+ _data['ProjectTaskAttachment']['id'] +'" onclick="deleteAttachmentFile.call(this)"></a>') : '') +'</li>';
                                }else{
                                    if(linkFile[_data['ProjectTaskAttachment']['id']]) _link = linkFile[_data['ProjectTaskAttachment']['id']];
                                    else _link = '/kanban/attachment/'+ _data['ProjectTaskAttachment']['id'] +'/?download=1&sid='+ api_key;
                                    _html += '<li><i class="icon-paper-clip"></i><a class="file-name" href = "'+ _link +'">'+ _data['ProjectTaskAttachment']['attachment'] +'</a>'+ (!read_only ? ('<a  data-id = "'+ id +'"><img src="/img/new-icon/delete-attachment.png" alt="'+ _data['ProjectTaskAttachment']['id'] +'" onclick="deleteAttachmentFile.call(this)"></a>') : '') +'</li>';
                                }
                            }else{
                                _html += '<li><i class="icon-link"></i><a class="file-name" target="_blank" href="'+ _data['ProjectTaskAttachment']['attachment'] +'">'+ _data['ProjectTaskAttachment']['attachment'] +'</a>'+ ( !read_only ? ('<a  data-id = "'+ _data['ProjectTaskAttachment']['id'] +'"><img src="/img/new-icon/delete-attachment.png" alt="'+ _data['ProjectTaskAttachment']['id'] +'" onclick="deleteAttachment.call(this)"></a>') : '') +'</li>';
                            }
                        }
                    });
                }
                _html += '</ul>';
                $('#template_upload #content_comment').html(_html);
            }
        });
       
    };
	function renderHtmlProgress(value){
		_html = '';
		if((check_consumed) && (check_consumed == 1)){
			_css_class = (value <= 100) ? 'green-line': 'red-line';
			display_value = Math.min(value, 100);
			//Them dieu kien hien thi progress. neu hien thi consumed or manual consumed thi moi hien thi progress. QuanNV 04/07/2019
			_html = '<div class="progress-slider '+_css_class+'" data-value="'+ value +'"><div class="progress-holder"><div class="progress-line-holder"></div></div><div class="progress-value" style="width:'+ display_value +'%" title="'+ display_value +'%"><div class="progress-line"></div><div class="progress-number"> <div class="text" style="margin-left: -'+ display_value +'%;" >'+ display_value +'%</div></div></div></div>';
		}
		return _html;
	}
	function renderTaskStatus(task, listStatus){
		if (listStatus) {
			statu = task['task_status_id'];
			late = task['late'];
            _html_head = '<div class="task-status">';
            _status_title = '<div class="status_texts">';
            _status_button = '<div class="status_dots"><div class="status_dots_cont">';
            $.each(listStatus, function (id, task_status) {
                color = 'status_blue';
                if (task_status.status == 'CL')
                    color = 'status_green';
                else if (late == 1)
                    color = 'status_red';
                active = (statu == task_status.id) ? 'active' : '';

                _status_title += '<span class="status_item status_item_' + task_status.id + ' status_text ' + active + ' ' + color + '" data-value="' + task_status.id + '">' + task_status.name + '</span>';
                _status_button += '<a class="status_item status_item_' + task_status.id + ' status_dot ' + active + ' ' + color + '" data-value="' + task_status.id + '" title="' + task_status.name + '" data-taskid=""></a>';
            });
            _status_title += '</div>';
            _status_button += '</div></div>';
            _html_head += _status_title + _status_button;
            _html_head += '</div>';
        }
		return _html_head;
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

    };
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
    function deleteAttachmentFile(){
        var attachId = $(this).prop('alt');
        var itemPic = $(this).closest('li');
        $.ajax({
            url: '<?php echo $this->Html->url('/kanban/delete_attachment/') ?>' + attachId,
			type: 'POST',
			data: {
				attachId: attachId,
			},
			dataType: 'json',
            success: function (data) {
                itemPic.empty();
            }
        })
    }
	$('body').on("change", "#update-comment", function () {
	   var $_this = $(this);
	   updateCommentTask($_this);
	});
	// function updateTaskText(){
		// var $_this = $(this);
	    // updateCommentTask($_this);
	// }
	function updateCommentTask($_this){
      var text = $('.text-textarea').val(),
        _id = $_this.data("id");
		var popup = $('#template_logs');
		popup.find('.content_comment').addClass('loading');
        if(text != ''){
            var _html = '';
            $.ajax({
                url: '/kanban/update_text',
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
                        nameEmloyee = data['text_updater'],
                        comment = data['comment'],
                        created = data['text_time'];
						comment_count = data['comment_count'];
                        url = js_avatar(idEm);
                        _html += '<div class="content-tast-text">';
                        _html += '<div class="content"><div class="avatar"><img class="circle-name" src ="'+ url +'" /></div><div class="content-employee"><div class="employee-info"><p>'+ nameEmloyee +' '+ created +'</p></div><div class="comment">'+ comment +'</div></div></div>';
						_html += '<a class="cm-delete" href="javascript:void(0);" onclick="deleteCommentTask(this, '+ data['id']+');"><img src="/img/new-icon/delete-attachment.png"></a>';
                        _html += '</div>';
                        $('#content-comment-detail').prepend(_html);
                        $('.text-textarea').val("");
						$('#kanban1_'+_id).find('.count-mess').empty().append(comment_count);
						popup.find('.content_comment').removeClass('loading');
                    }
                }
            });
        }
    };
    function deleteAttachment(){
        var file_id = $(this).prop('alt');
        var itemPic = $(this).closest('li');
		var attachId = $(this).closest('a').data('id');
        $.ajax({
			type: 'POST',
            dataType: 'json',
			data: {
				attachId: file_id,
			},
            url: '<?php echo $this->Html->url('/kanban/delete_attachment/') ?>' + file_id,
            success: function (data) {
                itemPic.empty();
				$('#kanban1_'+taskId).find('.count-file').empty().append(data);
            }
        })
    }
    function getTaskDetail(){
        var id = $(this).data("id");
        $.ajax({
            url : "/kanban/task_detail/"+id,
            type: "POST",
            data: {
                data:{
                    project_id : projectID,
                }
            },
            cache: false,
            success: function (html) {
                showMe();
                $('#contentDialog').html(html);
            }
        });
    }
    function getTaskAdd(){
        var id = $(this).data("id");
        $.ajax({
            url : "/kanban/task_new/"+id,
            type: "POST",
            data: {
                project_id : projectID,
            },
            cache: false,
            success: function (html) {
                showMe();
                $('#contentDialog').html(html);
            }
        });
    }
	// function renderTimeUpdate(date){
		// date = date.split('-');
		// tag_cur = date[0] + ' ' + i18ns[date[1]] + ' ' + date[2];
		// return tag_cur;
	// }
    function deleteTask(){
		var _this = $(this);
		_this.addClass('loading');
		id = _this.data('id');
		$('.task-item-actions').find('i.alert').remove();
		if (id) {
			$.ajax({
				url: '/project_tasks/destroyTaskJson/'+ id,
				type: 'POST',
				dataType: 'json',
				success: function(data) {
					if(data.success){
						var col_id = _this.closest('.wd-task-item').data('column_id');
						$('#kanban1').jqxKanban('removeItem', id);						
						taskStatus[col_id] = $.removeFromObjectbyKey( taskStatus[col_id], id);
						
					}else{
						_this.closest('.task-item-actions').append('<i class="alert">'+ data.message +'</i>');
						setTimeout(function(){
							$('.task-item-actions').find('i.alert').slideUp(300, function(){
								$('.task-item-actions').find('i.alert').remove();
							});
							
						}, 2000)
					}
					_this.removeClass('loading');
					updateListAssigned();
				}
			});
			
		}
	}
    function addNewTask(){
        var task_status_id = $(this).data("status");
        var task_title = $(this).closest('.new-task-content').find('input').val();
        if(task_title.trim() == '') return;
        var parent_ele = $(this).closest('.jqx-kanban-item');
        $(this).text('salvateur...');
        $(this).addClass('saving');
        $.ajax({
            url : "/project_tasks/createTaskJson/" + projectID,
			type: 'POST',
            dataType: 'json',
            data: {
				data: {
					task_status_id : task_status_id,
					task_title : task_title,
					parent_id : 0,
					project_id : projectID,
					task_start_date: '',
					task_end_date: '',
					estimated: 0,
					parentId: 0,
				}
            },
            success: function(result) {
                var _html = _action = _html_assign = '';
                if(result.success){
					var _task = [];
					var task_added = result.message['ProjectTask'];
					
					_task['status'] = task_added['task_status_id'];
					_task['id'] = parseInt(task_added['id']);
					_task['text'] = task_added['task_title'];
					
					// set default
					task_added['progress'] = 0;
					task_added['comment_count'] = 0;
					task_added['attachment_count'] = 0;
					task_added['consumed'] = 0;
					task_added['estimated'] = 0;
					
					taskStatus[task_added['task_status_id']][task_added['id']] = task_added;
					
					// delete template add kanban
					item_temp = $('#kanban1').jqxKanban('getItems').length - 1;
					$('#kanban1').jqxKanban('removeItem', item_temp);
					$('#kanban1').jqxKanban('addItem', _task);
					$('#kanban1_' + _task['id']).addClass('jqx-kanban-item').data('kanbanItemId', _task['id']);
                }else{
					
					parent_ele.find('.save-task').text('Enregistrer');
                    parent_ele.find('.save-task').removeClass('saving');
                    parent_ele.find('.save-task').before('<i>'+ result.message +'</i>');
                }
                $('.jqx-kanban-column-header').removeClass('nomore');
				updateListAssigned();
            }
        });
    }
	function formatDataTask(data){
		$dataFormat = data;
		
	}
    $("#gs-url").click(function(){
        $(this).addClass('gs-url-add');
        $('#gs-attach').addClass('gs-attach-remove');
        $('.update_url').removeAttr('disabled').css('border', '1px solid #3B57EE');
        $('.update_attach_class').attr('disabled', 'disabled').css('border', '1px solid #d4d4d4');
    });
    $("#gs-attach").click(function(){
        $(this).removeClass('gs-attach-remove');
        $('#gs-url').removeClass('gs-url-add');
        $('.update_attach_class').removeAttr('disabled').css('border', '1px solid #3B57EE');
        $('.update_url').attr('disabled', 'disabled').css('border', '1px solid #d4d4d4');
    });
    $("#closePopup").click(function(){
        $("#dialogDetailValue").removeClass('popup-upload');
    });
    // Kanban
	function kanbanAdapter(datakanbanAdapter){
		var fields = [
			{ name: "id", map: "id",type: "number" },
			{ name: "status", map: "task_status_id", type: "string" },
			{ name: "text", map: "task_title", type: "string" },
			{ name: "task_end_date", map: "task_end_date", type: "string" },
			{ name: "color", map: "hex", type: "string" },
			{ name: "resourceId", type: "number" },
			{ name: "content" , map: "task_end_date" , type: "array"}
		];

		var source =
		{
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
				{ name: "id", type: "number" },
				{ name: "task_title", type: "string" },
				{ name: "task_end_date", type: "string" },
				{ name: "common", type: "boolean" }
			]
		};
		var resourcesDataAdapter = new $.jqx.dataAdapter(resourcesSource);
		return resourcesDataAdapter;
	}
	
	function init_kanban(kanbanElm){
		if( kanbanElm == null) kanbanElm = '#kanban1';
		var wdKanban = $(kanbanElm);     
		var heightKanban = $(window).height() - ( wdKanban.offset().top + 50 );
		$(window).resize(function(){
			var heightKanban = $(window).height() - ( wdKanban.offset().top + 50 );
			wdKanban.css({ height: heightKanban});
		}); 			
		wdKanban.jqxKanban({
			width: '100%',
			height: heightKanban,
			theme: theme,
			template: "<div class='jqx-kanban-item wd-task-item' id='' data-taskid='' data-column_id=''>"
					+ "<div class='wd-task-assign'><ul class='list-assign'></ul><span class='task-item-date'></span></div>" + /* // 1 */ "<div class='wd-task-status'></div>"
					+ "<div class='task-item-content'><div class='jqx-kanban-item-text task-item-title'></div><div class='task-item-phase'></div>"
					+ "<div class='task-progress'></div>"
					+ "</div><div class='task-item-actions'></div><div style='clear: both'></div>"
			+ "</div>",
			resources: resourcesAdapterFunc(localDataTask),
			source: kanbanAdapter(localDataTask),
			// render items.
			itemRenderer: function (item, data, resource) {
				item.attr('data-taskid', data.id);
				item.attr('data-column_id', data.status);
				if(data.status in taskStatus){
					if(data.resourceId != 'add_task'){
						var k_task = taskStatus[data.status][data.id];
						var end_date = k_task['task_end_date_format'];
						var comment_count = k_task['comment_count'] ? k_task['comment_count'] : 0;
						var attachment_count = k_task['attachment_count'] ? k_task['attachment_count'] : 0;
						var updated = k_task['updated'];
						var is_nct = ( k_task['is_nct'] == 1) ? 'task-nct' : '';
						var edit_task = ( k_task['is_nct'] == 1) ? 'openEditTaskNCT(this);' : 'openEditTaskNormal(this);';
						var comment_status = (comment_count > 0) ? (k_task['comment_read_status'] ? 'read' : 'un-read') : 'no-file';
						var file_status = (attachment_count > 0) ? (k_task['file_read_status'] ? 'read' : 'un-read') : 'no-file';
						var task_late = k_task['late'];
						$(item).find(".task-item-actions").html("<ul><li><a class="+ comment_status +" href='javascript:;' data-id = "+ data.id +" onclick='getTaksText.call(this);'>"+svg_icons['message']+'<span class="count-mess">'+ comment_count + "</span></a></li><li><a class="+ file_status +" href='javascript:;' data-id = "+ data.id +" onclick='getTaskAttachment.call(this);' >"+svg_icons['document']+'<span class="count-file">'+ attachment_count +"</span></a></li><li><a class="+ is_nct +" href='javascript:;' data-id = "+ data.id +" onclick="+ edit_task +">"+svg_icons['edit']+"</a></li><li data-id = "+ data.id +" class='delete-task' onclick='deleteTask.call(this);'>"+svg_icons['delete']+"</li></ul>");
						
						if( task_late) $(item).find(".task-item-date").addClass('task_late');
						if( end_date && end_date == '0000-00-00'){
							translate_end_date = end_date.split('-');
							$(item).find(".task-item-date").html(translate_end_date[0] + ' ' + i18n[translate_end_date[1]] + ' ' + translate_end_date[2]);
						}
						// progress
						task_consumed = k_task['consumed'] ? k_task['consumed'] : 0;
						task_estimated = k_task['estimated'];
						_value_task = '<ul>' + ( show_workload ? ('<li><p>' + i18n['Workload'] +'</p><p>'+ task_estimated +' '+ i18n['M.D'] +'</p></li>' ) : '' ) + (((check_consumed) && (check_consumed == 1)) ? '<li><p>'+ i18n['Consumed'] +'</p><p>'+ task_consumed +' '+ i18n['M.D']  +'</p></li>' : '') +'</ul>';			
						var task_progress = 100;
						if( task_estimated != 0) task_progress = parseInt((task_consumed / task_estimated) * 100  );
						k_task['progress'] = task_progress;
						 // $(item).find(".wd-task-status").html(renderTaskStatus(k_task, listProjectStatus));
						_html_progress = renderHtmlProgress(k_task['progress']);
						var phase_color = list_phase_colors[k_task['project_planed_phase_id']] ? list_phase_colors[k_task['project_planed_phase_id']] : '';
						if(k_task['project_planed_phase_id']) $(item).find(".task-item-phase").html('<span style="background-color: '+ phase_color +'"></span>'+ list_phases[k_task['project_planed_phase_id']]);
						$(item).find(".task-progress").html(_value_task + _html_progress);
						var _html_assign = '';  
						$.each(k_task.assigned, function(ind, _data) {
							var e_id = _data['reference_id'];
							var is_pc = (_data['is_profit_center'] == 1) ? 1 : 0;
							var nameEmloyee = is_pc ? (listEmployeeName[e_id + '-' + is_pc]['name'] || '') : (listEmployeeName[e_id]['fullname'] || '') ;
							if(nameEmloyee){
								avt = is_pc ? '<i class="icon-people"></i>' : '<img src="' + js_avatar(e_id) + '" alt="avatar"/>';
								var _class = is_pc ? 'assign-team' : 'assign-employee';
								_html_assign += '<li title= "'+ nameEmloyee +'" class="' + _class + '">' + avt + '</li>';
							}
						});
						if(_html_assign){
							$(item).find(".list-assign").html(_html_assign);
						}
					}
				}
			},
			columns: projectStatusEX,
			// render column headers.
			columnRenderer: function (element, collapsedElement, column) {
				var columnItems = $("#kanban1").jqxKanban('getColumnItems', column.dataField).length;
				if( listProjectStatus[column.dataField]['status'] == 'IP') element.closest('.jqx-kanban-column').addClass('in_progress_column');
				element.find(".task_count").remove();
				element.find(".jqx-kanban-column-header-title").append('<span data-id="'+ column['dataField'] +'" class="task_count">(' + columnItems + ')</span>');
				// read only
				if(read_only) return;
				element.find(".jqx-kanban-column-header-status").html("<a href='javascript:void(0);' data-id='"+ column['dataField'] +"'><span class='icon-add'></span></a>");
			},
			ready: function(){
				if( read_only){
					$("#kanban1").find('*').unbind('mousedown');
				}
			}
		});
		wdKanban.on('itemMoved', function (event) {
			if(read_only) return;
			$('#clean-filters').addClass('disabled');
			var args = event.args;
			var itemId = args.itemData.id;
			var old_status = args.oldColumn.dataField;
			var new_status = args.newColumn.dataField;			
			var flag_id = $('#kanban1_'+args.itemId).find('.flag-id').val();
			if(flag_id) itemId = flag_id;
			var newColumn = args.newColumn['dataField'];
			if(itemId && newColumn){
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
							_task_move = $('#kanban1_' + itemId);
							_status_active = $('.status_item_' + newColumn);
							_task_move.find('.status_item').removeClass('active');
							_task_move.find(_status_active).addClass('active');
							if( taskStatus[new_status] == '') taskStatus[new_status] = {};
							taskStatus[new_status][itemId] = taskStatus[old_status][itemId];
							taskStatus[new_status][itemId].task_status_id = new_status;
							taskStatus[old_status] = $.removeFromObjectbyKey( taskStatus[old_status], itemId);
							$('#clean-filters').removeClass('disabled');
						}
					}
				});
			}
		});
		wdKanban.on("itemAttrClicked", function (event) {
			var args = event.args;
			var task_id = args.item.id;
			// Gan cờ cho trường hợp 
			var flag_id = $('#kanban1_'+args.itemId).find('.flag-id').val();
			if(flag_id) task_id = flag_id;

			if (args.attribute == "template") {
				$.ajax({
					url: '/kanban/delete_task/'+ task_id,
					type: 'POST',
					dataType: 'json',
					success: function(data) {
						if(data == 1){
							wdKanban.jqxKanban('removeItem', args.item.id);
							updateListAssigned();
						}
						
					}
				});
				
			}
		});

		wdKanban.on('click', '.wd-input-search', function(e){
			e.stopImmediatePropagation();
			$(this).focus();
		});
		var itemIndex = 0;
		wdKanban.on('columnAttrClicked', function (event) {
			var args = event.args;
			args.cancelToggle = true;
			headerElement = args.column.headerElement;
			if(read_only) return;
			cancel_popup('#template_add_task_normal');
			var cur_status = args.column.dataField;
			$('#newTaskStatus').val(cur_status);
			var popup_width = show_workload ? 1080 : 580;
			show_full_popup( '#template_add_task_normal', {width: popup_width}, false);
		});
	}
	function open_add_new_task(){
		var popup_width = show_workload ? 1080 : 580;
		show_full_popup( '#template_add_task_normal', {width: popup_width}, false);
	}
	function re_init_kanban(list_tasks){
		if( list_tasks != null) localDataTask = list_tasks;		
		$(kanbanTag).jqxKanban('destroy');
		if( !$(kanbanTag).length) $('.wd-kanban-container:first').append( $('<div id="kanban1" class="kanban-task" style=""></div>'));
		init_kanban(kanbanTag);
	}
    $(document).ready( function () {
        init_kanban(kanbanTag);
    });
    $('.fancy.image').fancybox({
       type: 'image'
    });
    $("#ok_attach").on('click',function(){
        id = $('input[name="data[Upload][id]"]').data('id');
        url = $.trim($('input[name="data[Upload][url]"]').val());
        var form = $("#UploadIndexForm");
        if(url){
            form.submit();
        }
        
    });
    Dropzone.autoDiscover = false;
    $(function() {
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
                            if(_data){
                                if(_data['ProjectTaskAttachment']['is_file'] == 1){
                                    if((/\.(gif|jpg|jpeg|tiff|png)$/i).test(_data['ProjectTaskAttachment']['attachment'])){ 
                                        if(linkFile[_data['ProjectTaskAttachment']['id']]) _link = linkFile[_data['ProjectTaskAttachment']['id']];
                                        else _link = '/kanban/attachment/'+ _data['ProjectTaskAttachment']['id'] +'/?sid='+ api_key;
                                        _html += '<li><i class="icon-paper-clip"></i><span href="'+ _link +'" class="fancy image" rel="one_pic_expand" data-fancybox="image" data-type="image">'+ _data['ProjectTaskAttachment']['attachment'] +'</span><a  data-id = "'+ id +'"><img src="/img/new-icon/delete-attachment.png" alt="'+ _data['ProjectTaskAttachment']['id'] +'" onclick="deleteAttachmentFile.call(this)"></a></li>';
                                    }else{
                                        if(linkFile[_data['ProjectTaskAttachment']['id']]) _link = linkFile[_data['ProjectTaskAttachment']['id']];
                                        else _link = '/kanban/attachment/'+ _data['ProjectTaskAttachment']['id'] +'/?download=1&sid='+ api_key;
                                        _html += '<li><i class="icon-paper-clip"></i><a class="file-name" href = "'+ _link +'">'+ _data['ProjectTaskAttachment']['attachment'] +'</a><a  data-id = "'+ id +'"><img src="/img/new-icon/delete-attachment.png" alt="'+ _data['ProjectTaskAttachment']['id'] +'" onclick="deleteAttachmentFile.call(this)"></a></li>';
                                    }
                                }else{
                                    _html += '<li><i class="icon-link"></i><a class="file-name" target="_blank" href="'+ _data['ProjectTaskAttachment']['attachment'] +'">'+ _data['ProjectTaskAttachment']['attachment'] +'</a><a  data-id = "'+ _data['ProjectTaskAttachment']['id'] +'"><img src="/img/new-icon/delete-attachment.png" alt="'+ _data['ProjectTaskAttachment']['id'] +'" onclick="deleteAttachment.call(this)"></a></li>';
                                }
                            }
                        });
                    }
                    _html += '</ul>';
                    $('#content_comment .content-attachment').find('ul').empty();
                    $('#content_comment .content-attachment').append(_html);
					$('#kanban1_'+id).find('.count-file').empty().append(data['attachment_count']);

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
	$('#ProjectTask').on('submit', function (e) {
		e.preventDefault();
        var form_tag = '#ProjectTask';
        var _form = $(form_tag);
        var start_date = $('#editTaskStartDay').val(),
                end_date = $('#editTaskEndDay').val();
        var st_date = start_date.split('-');
        var en_date = end_date.split('-');
        st_date = new Date(st_date[2], st_date[1], st_date[0]);
        en_date = new Date(en_date[2], en_date[1], en_date[0]);
        if (start_date && end_date && (st_date > en_date)) {
            show_form_alert('#ProjectTask', "<?php __('Please enter the end date must be after the start date');?>");
            return;
        }
        _form.closest('.loading-mark').addClass('loading');
        $.ajax({
            type: "POST",
            url: _form.prop('action'),
            data: _form.serialize(),
            dataType: 'json',
            success: function (data) {
                if (data.result == 'success') {
                    var _task = [];
                    _task['status'] = parseInt(data['message']['task_status_id']);
                    _task['id'] = parseInt(data['message']['id']);
                    _task['text'] = data['message']['task_title'];
                    // data_tasks[_task['id']] = data['message'];
					var status = data['message']['task_status_id'];
					if( taskStatus[status] == '') taskStatus[status] = {};
					taskStatus[status][_task['id']] = data['message'];
                    $('#kanban1').jqxKanban('removeItem', data['message']['id']);
                    $('#kanban1').jqxKanban('addItem', _task);
                    $('#kanban_' + _task['id']).addClass('jqx-kanban-item').data('kanbanItemId', _task['id']);
                    
                } else {
                    show_form_alert('#' + _form.prop('id'), data.message);

                }
                _form.closest('.loading-mark').removeClass('loading');
            },
            error: function () {
                _form.closest('.loading-mark').removeClass('loading');
            },
			complete: function(){
				cancel_popup(form_tag);
				filterTask();
				updateListAssigned();
			},
        });

    });
	function set_assigned(elm, datas){		
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
					var _avt = is_pc ? '<i class="icon-people"></i>' : ('<img width = 35 height = 35 src="'+  js_avatar(_e_id ) +'" title = "'+ title +'" />');
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
    function openEditTaskNormal(elm) {
        var _this = $(elm);
        var taskid = _this.data('id');
        var popup = $('#template_edit_task');
		var popup_width = show_workload ? 1080 : 580;
        popup.find('.loading-mark:first').addClass('loading');
		md_resource = {};
        show_full_popup( '#template_edit_task', {width: popup_width}, false);
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
						md_resource = data.data.resource_manday;
                        $('#editTaskID').val(data.data.id).trigger('change');
                        $('#task_title').val(data.data.task_title).trigger('change');
                        $('#toPhase').val(data.data.project_planed_phase_id).trigger('change');
                        $('#toStatus').val(data.data.task_status_id).trigger('change');
                        var start_date = data.data.task_start_date,
                                end_date = data.data.task_end_date;
						if(start_date){
							var st_date = start_date.split('-');
							st_date = st_date[2] + '-' + st_date[1] + '-' + st_date[0];
							$('#editTaskStartDay').val(st_date).trigger('change');
						}
						if(end_date){
							var en_date = end_date.split('-');
							en_date = en_date[2] + '-' + en_date[1] + '-' + en_date[0];
							$('#editTaskEndDay').val(en_date).trigger('change');
						}
						
                        $('#edit_normal-manual_consumed').val(data.data.manual_consumed).trigger('change');
						var consume = data.data.consume;
						popup.find('.total-consumed').text(parseFloat(consume).toFixed(2));
						popup.find('.total-workload').empty().append( '<input name="data[ProjectTask][estimated]" type="number" min="0"  step="0.01" value="'+ parseFloat(data.data.estimated).toFixed(2) +'" rel="no-history">' );
						init_multiselect('#template_edit_task .wd-multiselect');
                        set_assigned(popup.find('.multiselect-pm:first'), data.data.assigned);
						list_task_assign_to_editonChange('list_task_assign_to_edit');
						resetOptionAssigned(data.employees_actif, '#list_task_assign_to_edit');
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
            error: function () {
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
		var status = data['message']['ProjectTask']['task_status_id'];
		if( taskStatus[status] == '') taskStatus[status] = {};
        taskStatus[status][_task['id']] = data['message']['ProjectTask'];
        $('#kanban1').jqxKanban('addItem', _task);
        $('#kanban_' + _task['id']).addClass('jqx-kanban-item').data('kanbanItemId', _task['id']);
		updateListAssigned();
		filterTask();
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
		} else {
			var _form = $(dropzone_elm).closest('form');
			show_form_alert('#' + _form.prop('id'), data.message);
			$(dropzone_elm).closest('.loading-mark').removeClass('loading');
		}
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
							if( !localDataTask.length ) location.reload(); 
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
					url : "/project_tasks_preview/add_task_popup/" + projectID,
					type: "GET",
					cache: false,
					success: function (html) {
						$('#addProjectTemplate').empty().append($(html));
						$('.tabPopup .liPopup a.active').empty().html('<?php __('Edit task');?>');
						$('#template_add_task .btn-ok span').empty().html('<?php __('Save');?>');
						$('.right-link').hide();
						$('#add-form').trigger('reset');
						$(window).trigger('resize');
						$('#addProjectTemplate').addClass('loaded');
						$('#addProjectTemplate').removeClass('loading');
						$('input[data-return="form-return"]').val('<?php echo $this->here;?>');
						if( $('#addProjectTemplate').hasClass('open') ){
							show_full_popup('#template_add_task', {}, false);
							$('#template_add_task').find('input, select').trigger('change');
						}
					},
					complete: function(){
						$('#addProjectTemplate').removeClass('loading');
						$(kanbanTag).removeClass('loading');
					},
					error: function(){
					}
				});
			}
		}, 2000);
	});
	
	function openEditTaskNCT(elm) {
		var id = $(elm).data('id');
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
						if( typeof val[0]['type'] !== 'undefined') type = val[0]['type'];
						
						return false;
					});
				}
				$('#popupnct-range-type').val(type);
				show_full_popup('#template_add_nct_task', {width: 'inherit'}, false);
				$('.popup-back').empty().html('<?php __('Edit NCT task');?>');
				$('#popupnct-id').val(id).trigger('change');
				$('#popupnct-name').val(data['task']['task_title']).trigger('change');
				$('#popupnct-phase').val(data['task']['project_planed_phase_id']).trigger('change');
				$('#popupnct-status').val(data['task']['task_status_id']).trigger('change');
				
				var assigns = [];
				if(data['columns']){
					$.each(data['columns'], function(key, column){
						id = (column.id).split('-');
						assigns.push({reference_id: id[0], is_profit_center: id[1]});
					});
				}
				render_list_date(data);
				setTimeout(function(){
					set_assigned($('#template_add_nct_task').find('.popupnct_nct_list_assigned'), assigns);
					multiselect_popupnct_pmonChange('multiselect-popupnct-pm');
					resetOptionAssigned(data.employees_actif, '#multiselect-popupnct-pm');
				}, 1000);
				
				$('#popupnct-profile').val(data['task']['profile_id']).trigger('change');
				$('#popupnct-priority').val(data['task']['task_priority_id']).trigger('change');
				$('#popupnct_per-workload').val(data['task']['estimated']).trigger('change');
				// var assign_data = 
				// render_html_assign(data);
				$('#template_add_nct_task #btnSave').empty().html('<?php __('Save');?>');
				setLimitedDate('#popupnct-start-date', '#popupnct-end-date');
				set_width_popupnct();
			}
		});
	
	}
	function set_width_popupnct(){
		// return;
		$.each( $('#template_add_nct_task .wd-row-inline'), function (i, _row){
			var _row = $(_row);
			var _width = 0;
			$.each( _row.children(), function( j, _col){
				_width += $(_col).width()+41;
			});
			_row.width(_width);
		});
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
	function render_list_date(data){
		var request = data.request;
		var data = data.data;
		var consumed = 0, in_used = 0;
		var html = '';
		$.each(data, function(row, val){
			var date = row.substr(2);
			var date_name = toRowName(row);
			html += '<tr><td id="date-' + date + '" class="popupnct-date" style="text-align: left">' + toRowName(row) + '<span class="cancel" onclick="removeRow(this)" href="javascript:;"></span></td>';
			
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
			
			consumed += c;
			in_used += iu;
		});
		$('#addProjectTemplate').find('#popupnct-assign-table tbody').html(html);
		$('#popupnct_total-consumed').empty().html(consumed.toFixed(2) +' ('+ in_used.toFixed(2) +')');
	}
	/*
	* Workload table
	* init: list_assigned, c_task_data, show_workload
	*/
	function template_add_task_normal_showed(){
		init_multiselect('#template_add_task_normal .wd-multiselect');
	}
	function c_calcTotal(ele){
		if($(ele).length > 0 && showDisponibility == 1){
			e_id = $(ele).data('id');
			wl_old = workload_resource[e_id] ? workload_resource[e_id] : 0;
			wl_new = $(ele).val();
			col_id = e_id.split('-')
			avai_refer =  $('#task_assign_table').find('.c_manday[data-id="'+ col_id[0] +'"]');
			// console.log(avai_refer);
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
				// console.log(2);
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
					_avt += '<img width = 35 height = 35 src="'+  js_avatar(e_id ) +'" title = "'+ name +'" alt="avatar"/>';	
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
				// console.log(showDisponibility);
				if(showDisponibility == 1){ 
					ex_action = '';
					if( is_profit_center == 0 ){
						ex_action = 'onClick="getDataAvailability(this, '+ e_id +')';
						ex_class += ' c_employee';
					}
					manday_col ='<td style="vertical-align: middle;" class="col_manday"><span class="c_manday '+ ex_class +'" data-id="'+ e_id +'"'+ ex_action +'">'+ (e_manday < 0 ? "+"+ e_manday*-1 : e_manday) +'</span></td>';
				}
				var _html = '<tr class="workload-row workload-row-' + id + ' ">' + res_col + val_col + '</tr>';
				elm.find('.nct-assign-table tbody').append( _html);
			}
		});
		c_task_data.workload_resource = workload_resource;
		c_calcTotal();
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
					_avt += '<img width = 35 height = 35 src="'+  js_avatar(e_id ) +'" title = "'+ name +'" alt="avatar"/>';	
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
            $(".popup-header-2").html(headers);
            $(".popup-availa-2").html(avais);
            $(".popup-over-2").html(over);
            $(".popup-vaication-2").html(vocs);
            $(".popup-workload-2").html(work);
            $(".popup-capacity-2").html(capacity);
            $(".popup-working-2").html(working);

            // phan detail cua task
            var listTaskDisplay = '';
            var valTaskDisplay = '';
            var totalWorkload = [];
            var listSumFamily = [];
            var totalFamily = [];
			countRow = 0;
            if(datas.listDateDatas){
                $.each(datas.listDateDatas, function(idFamily, values){
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
            $(".popup-task-detail-2").html(valTaskDisplay);

            $('.popup-task-detail-2').find('.family-group td div').each(function(){
                var idDivOfFamily = $(this).attr('id');
                var idCheck = idDivOfFamily.replace('fam-', '');
                var valSumFam = listSumFamily[idCheck] ? listSumFamily[idCheck].toFixed(2) : 0;
                $('#'+idDivOfFamily).html(valSumFam);
            });
            $('.popup-task-detail').find('td.ch-fam div').each(function(){
                var idDivOfFamily = $(this).attr('id');
                var idCheck = idDivOfFamily.replace('total-fam-', '');
                var valSumFam = totalFamily[idCheck] ? totalFamily[idCheck].toFixed(2) : 0;
                $('#'+idDivOfFamily).css('text-align', 'right');
                $('#'+idDivOfFamily).html(valSumFam);
            });
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
            $(".popup-header-2").html(headers);
            $(".popup-availa-2").html(avais);
            $(".popup-over-2").html(over);
            $(".popup-vaication-2").html(vocs);
            $(".popup-workload-2").html(work);
            $(".popup-capacity-2").html(capacity);
            $(".popup-working-2").html(working);


            // phan detail cua task
            var listTaskDisplay = '';
            var valTaskDisplay = '';
            var totalWorkload = [];
            var listSumFamily = [];
            var totalFamily = [];
			countRow = 0;
            if(datas.listMonthDatas){
                $.each(datas.listMonthDatas, function(idFamily, values){
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
                                valTaskDisplay += '</tr>';
                            });
                        } else {
                            // do no thing
                        }
                    });
                });
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
            $(".popup-task-detail-2").html(valTaskDisplay);

            $('.popup-task-detail-2').find('.family-group td div').each(function(){
                var idDivOfFamily = $(this).attr('id');
                var idCheck = idDivOfFamily.replace('fam-', '');
                var valSumFam = listSumFamily[idCheck] ? listSumFamily[idCheck].toFixed(2) : 0;
                $('#'+idDivOfFamily).html(valSumFam);
            });
            $('.popup-task-detail').find('td.ch-fam div').each(function(){
                var idDivOfFamily = $(this).attr('id');
                var idCheck = idDivOfFamily.replace('total-fam-', '');
                var valSumFam = totalFamily[idCheck] ? totalFamily[idCheck].toFixed(2) : 0;
                $('#'+idDivOfFamily).css('text-align', 'right');
                $('#'+idDivOfFamily).html(valSumFam);
            });
        }
        //init();
        initMonth();
        //filter
        $("#filter_year").click(function(e){
            $('#filter_date').removeClass('ch-current');
            $('#filter_month').removeClass('ch-current');
            $(this).addClass('ch-current');
            var headers = avais = vocs = work = over = working = capacity = '';
            var totalCount = totalVacation = 0;
            widthDivRight = 0;
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

            $(".popup-header-2").html(headers);
            $(".popup-availa-2").html(avais);
            $(".popup-over-2").html(over);
            $(".popup-vaication-2").html(vocs);
            $(".popup-workload-2").html(work);
            $(".popup-capacity-2").html(capacity);
            $(".popup-working-2").html(working);

            // phan detail cua task
            var listTaskDisplay = '';
            var valTaskDisplay = '';
            var totalWorkload = [];
            var listSumFamily = [];
            var totalFamily = [];
			countRow = 0;
            if(datas.listYearDatas){
                $.each(datas.listYearDatas, function(idFamily, values){
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
            $(".popup-task-detail-2").html(valTaskDisplay);

            $('.popup-task-detail-2').find('.family-group td div').each(function(){
                var idDivOfFamily = $(this).attr('id');
                var idCheck = idDivOfFamily.replace('fam-', '');
                var valSumFam = listSumFamily[idCheck] ? listSumFamily[idCheck].toFixed(2) : 0;
                $('#'+idDivOfFamily).html(valSumFam);
            });
            $('.popup-task-detail').find('td.ch-fam div').each(function(){
                var idDivOfFamily = $(this).attr('id');
                var idCheck = idDivOfFamily.replace('total-fam-', '');
                var valSumFam = totalFamily[idCheck] ? totalFamily[idCheck].toFixed(2) : 0;
                $('#'+idDivOfFamily).css('text-align', 'right');
                $('#'+idDivOfFamily).html(valSumFam);
            });
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
	function cancel_popup_template_add_task_normal(){
		c_task_data = {};
		list_assigned = {};
		$('#new_task_assign_table').find('tbody').empty();
	}
	
</script>
