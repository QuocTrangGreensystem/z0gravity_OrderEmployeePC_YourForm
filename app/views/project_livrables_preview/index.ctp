<?php
echo $this->Html->css(array(
    'slick_grid/slick.grid.activity',
    'jquery.multiSelect',
    'projects',
    'slick_grid/slick.grid_v2',
    'slick_grid/slick.pager',
    'slick_grid/slick.common_v2',
    'multipleUpload/jquery.plupload.queue',
    'slick_grid/slick.edit',
    'preview/project_livrables',
    'dropzone.min',
    'jquery.fancybox',
    'layout_2019'
));
echo $this->Html->script(array(
	'livrable_icons',
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
    'jquery.ui.touch-punch.min',
    'multipleUpload/plupload.full.min',
    'multipleUpload/jquery.plupload.queue',
    'd3/d3.min',
    'd3/pdc',
    'dropzone.min',
    'jquery.fancybox.pack',
	'jquery-ui.min',
));
echo $this->element('dialog_projects');
App::import("vendor", "str_utility");
$str_utility = new str_utility();
$canModified = (!empty($canModified) && !$_isProfile ) || ($_isProfile && $_canWrite);
?>
<style>
    .wd-layout > .wd-main-content > .wd-tab > .wd-panel{
		max-width: 1920px;
	}
</style>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';

</script>
<!-- export excel  -->
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'project_livrables', 'action' => 'export', $projectName['Project']['id'])));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<!-- /export excel  -->

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
    'no.' => array(
        'id' => 'no.',
        'field' => 'no.',
        'name' => __(' ', true),
        'width' => 80,
        'minWidth' => 40,
        'maxWidth' => 80,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
        'behavior' => 'selectAndMove',
        'cssClass' => 'slick-cell-move-handler',
        'formatter' => 'Slick.Formatters.livrableIcon'
    ),
    'name' => array(
        'id' => 'name',
        'field' => 'name',
        'name' => __('Name', true),
        'width' => 350,
        'minWidth' => 150,
        'maxWidth' => 300,
        'sortable' => true,
        'resizable' => true,
        'cssClass' => 'wd-document-name wd-grey-background',
        'editor' => 'Slick.Editors.textBox',
        'formatter' => 'Slick.Formatters.attachmentName'
    ),
	'livrable_time_modify' => array(
        'id' => 'livrable_time_modify',
        'field' => 'livrable_time_modify',
        'name' => __('Doc Last Modified', true),
        'width' => 150,
        'minWidth' => 88,
        'maxWidth' => 180,
        // 'datatype' => 'datetime',
        'sortable' => true,
        'resizable' => true,
		'formatter' => 'Slick.Formatters.livrableModify'
    ),
    'project_livrable_category_id' => array(
        'id' => 'project_livrable_category_id',
        'field' => 'project_livrable_category_id',
        'name' => __('Doc Deliverable', true),
        'width' => 250,
        'minWidth' => 100,
        'maxWidth' => 450,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox',	
    ),
	'livrable_progression' => array(
        'id' => 'livrable_progression',
        'field' => 'livrable_progression',
        'name' => __('Doc Progress', true),
        'width' => 260,
        'sortable' => false,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.percentValues',
        'headerCssClass' => 'slick-header-merged',
    ),
	'livrable_progression_text' => array(
        'id' => 'livrable_progression_text',
        'field' => 'livrable_progression',
        'name' => __(' ', true),
        'width' => 75,
        'sortable' => false,
        'resizable' => true,
        'cssClass' => 'slick-cell-merged no-border',
		'editor' => 'Slick.Editors.percentValue',
		'formatter' => 'Slick.Formatters.percentValuesText',
		// 'validator' => 'percentFieldValidator'
        'headerCssClass' => 'slick-header-merged',
    ),
	'livrable_comment' => array(
        'id' => 'livrable_comment',
        'field' => 'livrable_comment',
        'name' => '',
        'width' => 50,
        'sortable' => false,
        'cssClass' => 'slick-cell-merged no-border',
        'resizable' => true,
        'formatter' => 'Slick.Formatters.livrableComment',
    ),
	'actor_list' => array(
        'id' => 'actor_list',
        'field' => 'actor_list',
        'name' => __('Doc Responsible', true),
        'width' => 240,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.mselectBox',
		'formatter' => 'Slick.Formatters.livrableActor'
    ),
);
$columnsOption = array(
    'project_code' => array(
        'id' => 'project_code',
        'field' => 'project_code',
        'name' => __('Project Code', true),
        'width' => 150,
        'minWidth' => 100,
        'maxWidth' => 150,
        'sortable' => false,
        'resizable' => true,
        'headerCssClass' => 'slick-header-merged',
    ),
	
    'project_livrable_status_id' => array(
        'id' => 'project_livrable_status_id',
        'field' => 'project_livrable_status_id',
        'name' => __('Status', true),
        'width' => 150,
        'minWidth' => 100,
        'maxWidth' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    'livrable_progression' => array(
        'id' => 'livrable_progression',
        'field' => 'livrable_progression',
        'name' => 'Progression',
        'width' => 150,
        'minWidth' => 150,
        'maxWidth' => 180,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.percentValue',
        'formatter' => 'Slick.Formatters.percentValues'
    ),
    'version' => array(
        'id' => 'version',
        'field' => 'version',
        'name' => __('Version', true),
        'width' => 150,
        'minWidth' => 100,
        'maxWidth' => 180,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    'actor_list' => array(
        'id' => 'actor_list',
        'field' => 'actor_list',
        'name' => __('Actor', true),
        'width' => 150,
        'minWidth' => 100,
        'maxWidth' => 180,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.mselectBox'
    ),
    'livrable_date_modify' => array(
        'id' => 'livrable_date_modify',
        'field' => 'livrable_date_modify',
        'name' => __('Date', true),
        'width' => 150,
        'minWidth' => 88,
        'maxWidth' => 180,
        'datatype' => 'datetime',
        'sortable' => false,
        'resizable' => true
    ),
    'livrable_time_modify' => array(
        'id' => 'livrable_time_modify',
        'field' => 'livrable_time_modify',
        'name' => __('Time', true),
        'width' => 150,
        'minWidth' => 88,
        'maxWidth' => 180,
        'datatype' => 'datetime',
        'sortable' => false,
        'resizable' => true
    ),
    'id' => array(
        'id' => 'id',
        'field' => 'id',
        'name' => __('Unique Id', true),
        'width' => 150,
        'minWidth' => 100,
        'maxWidth' => 180,
        'sortable' => false,
        'resizable' => true,
    ),
    'employee_modify' => array(
        'id' => 'employee_modify',
        'field' => 'employee_modify',
        'name' => __('Name', true),
        'width' => 250,
        'minWidth' => 180,
        'maxWidth' => 250,
        'sortable' => false,
        'resizable' => true
    ),
    'team' => array(
        'id' => 'team',
        'field' => 'team',
        'name' => __('Team', true),
        'width' => 150,
        'minWidth' => 150,
        'maxWidth' => 250,
        'sortable' => false,
        'resizable' => true
    ),
);
$columnsAction = array(
    'action.' => array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => '&nbsp;',
        'width' => 60,
        'minWidth' => 60,
        'maxWidth' => 60,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
    )
);
$settings = array();

foreach($settings as $setting){
    list($key, $show) = explode('|', $setting);
    if( $show == 0 )continue;
    if( $key == 'progress' && !$manuallyAchievement )continue;
    if( $key == 'profile_id' && !$activateProfile )continue;
    if( isset($columnsOption[$key]) ) $columns[] = $columnsOption[$key];
}

$columns = array_merge($columns, $columnsAction); 

foreach($columns as $key => $column){
	if(!empty($loadFilter) && !empty($loadFilter[$column['field']. '.Resize'])){
		$columns[$key]['width'] = intval($loadFilter[$column['field']. '.Resize']);
	}
}
$dataView = array();
$selectMaps = array(
    'project_livrable_category_id' => $livrableCategories,
    'project_livrable_status_id' => $projectStatuses,
    'livrable_responsible' => $employees,
    'actor_list' => $employees
);
$i = 1;
$project_livrable_category  = array();
foreach ($projectLivrables as $projectLivrable) {
    $data = array(
        'id' => $projectLivrable['ProjectLivrable']['id'],
        'project_id' => $projectName['Project']['id'],
        'no.' => $i++
    );
    $data['name'] = $projectLivrable['ProjectLivrable']['name'];
    $data['project_livrable_category_id'] = ($projectLivrable['ProjectLivrable']['project_livrable_category_id'] != 0) ? $projectLivrable['ProjectLivrable']['project_livrable_category_id'] : '';
    $data['project_livrable_status_id'] = ($projectLivrable['ProjectLivrable']['project_livrable_status_id'] != 0) ? $projectLivrable['ProjectLivrable']['project_livrable_status_id'] : '';
    $data['livrable_responsible'] = ($projectLivrable['ProjectLivrable']['livrable_responsible'] != 0) ? $projectLivrable['ProjectLivrable']['livrable_responsible'] : '';
    $data['project_code'] = $projectCode['Project']['project_code_1'];

    $data['livrable_progression'] = $projectLivrable['ProjectLivrable']['livrable_progression'];
    $data['weight'] = $projectLivrable['ProjectLivrable']['weight'];
    $data['version'] = $projectLivrable['ProjectLivrable']['version'];
    $data['employee_modify'] = !empty($projectLivrable['ProjectLivrable']['employee_id_upload']) && !empty($listEm[$projectLivrable['ProjectLivrable']['employee_id_upload']]) ? $listEm[$projectLivrable['ProjectLivrable']['employee_id_upload']] : '';
    $data['team'] = !empty($projectLivrable['ProjectLivrable']['employee_id_upload']) && !empty($listEmIdOfPc[$projectLivrable['ProjectLivrable']['employee_id_upload']]) && !empty($listPc[$listEmIdOfPc[$projectLivrable['ProjectLivrable']['employee_id_upload']]]) ? $listPc[$listEmIdOfPc[$projectLivrable['ProjectLivrable']['employee_id_upload']]] : '';

    $data['actor_list'] = Set::classicExtract($projectLivrable['ProjectLivrableActor'], '{n}.Employee.id');

    $data['livrable_date_modify'] = date('d-m-Y', $projectLivrable['ProjectLivrable']['updated']);
    $data['livrable_time_modify'] = $projectLivrable['ProjectLivrable']['updated'];

    $data['livrable_file_attachment'] = $projectLivrable['ProjectLivrable']['livrable_file_attachment'];
    $data['format'] = (string) $projectLivrable['ProjectLivrable']['format'];
    $data['upload_date'] = (string) !empty($projectLivrable['ProjectLivrable']['upload_date']) ? date('d-m-Y H:i:s', $projectLivrable['ProjectLivrable']['upload_date']) : '';

    $data['action.'] = '';
	if($projectLivrable['ProjectLivrable']['project_livrable_category_id']){
		$project_livrable_category[$projectLivrable['ProjectLivrable']['project_livrable_category_id']] = $livrableCategories[$projectLivrable['ProjectLivrable']['project_livrable_category_id']];
	}
    $dataView[] = $data;
}
$projectName['Project']['start_date'] = $str_utility->convertToVNDate($projectName['Project']['start_date']);
$projectName['Project']['end_date'] = $str_utility->convertToVNDate($projectName['Project']['end_date']);
if ($projectName['Project']['end_date'] == "" || $projectName['Project']['end_date'] == '0000-00-00') {
    $projectName['Project']['end_date'] = $str_utility->convertToVNDate($projectName['Project']['planed_end_date']);
}
$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true),
    'Date closing must between %1$s and %2$s' => __('Date closing must between %1$s and %2$s', true),
    'Cannot leave because upload file in processing!' => __('Cannot leave because upload file in processing!', true),
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
);

?>

<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content new-design">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>

            <div class="wd-tab"> <div class="wd-panel">
            <div class="wd-list-project">
                <div class="wd-title">
                    <?php if($employee_info['Role']['name'] == 'admin'): ?>
                    <a target="_blank" href="<?php echo $html->url("/project_livrable_categories/index/ajax") ?>" id="button-setting" class="button-setting" title="<?php __('Setting')?>"></a>
                    <?php endif; ?>
                    <!-- 
					<a href="javascript:void(0);" class="export-excel-icon-all" id="export-submit" title="<?php __('Export Excel')?>"><span><?php __('Export Excel') ?></span></a>
                    -->
                    <a href="javascript:void(0);" class="btn btn-reset-filter hidden" id="reset-filter" onclick="resetFilter();" style="margin-right:5px;" title="<?php __('Reset filter') ?>"></a>
                   
                    <select  style="display: none" class="filter" name="filterby" id="table-filter-by" onChange="changeFilter(this);">
                        <option value="" hidden> <?php echo __("Filter by");?> </option>
                    </select>
					<?php if(!empty($project_livrable_category)){ ?>
					<div class="filter-select">
						<label><?php echo __('Filter by: Type of deliverable', true); ?></label>
						<ul class="option-content">
							<?php foreach($project_livrable_category as $cat_id => $cat_name){ ?>
								<li><input type="checkbox" name="project_livrable_category_title_filter[]" value="<?php echo $cat_id; ?>" id="check-filter-<?php echo $cat_id; ?>" class="wd-title-filter" data-target='.multiSelectOptions input[type="checkbox"]'><?php echo $cat_name; ?></li>
							<?php	} ?>
						</ul>
					</div>
					<?php } ?>
                    <a href="javascript:void(0);" class="btn btn-fullscreen" id="table-expand" onclick="expandTable();" title="<?php __("Expand"); ?>"></a>
                    <a href="javascript:void(0);" class="btn btn-table-collapse" id="table-collapse" onclick="collapse_table();" title="<?php __('Collapse table') ?>" style="display: none;"></a>

                </div>
                <div id="message-place">
                    <?php echo $this->Session->flash(); ?>
                </div>
                <br clear="all"  />
                <div class="wd-table-container">
                    <div class="wd-table" id="project_container" style="width:100%;">
                    </div>
                    <div class="wd-popup-container"><div class="wd-popup">
                        <?php 
                        echo $this->Form->create('popupUpload', array(
                            'type' => 'POST',
                            'url' => array('controller' => $this->params['controller'], 'action' => 'add_new_document', $projectName['Project']['id'])));
                        echo $this->Form->input(
                            'name', 
                            array(
                                'label' => __('Nom du document',true),
                                 'type' => 'text', 'required' => true, 
                                 'id' => 'newDocName', 
                                 'class' =>'not_save_history',
                                 'placeholder' => __('Nom du document', true), 
                                 'no-history' => true,
                                 )
                            );
                        echo $this->Form->input('url', array(
                            'class' => 'not_save_history',
                            'label' => array(
                                'class' => 'label-has-sub',
                                'text' =>__('URL',true),
                                'data-text' => __('(optionnel)', true),
                                ),
                            'type' => 'text',
                            'id' => 'newDocURL', 
                            'placeholder' => __('https://', true) ,  
                            ));
                            ?>
                            <div id="popup_template_attach" >
                                <div class="heading">
                                    
                                </div> 
                                <div class="trigger-upload"><div id="upload-popup" method="post" action="/project_livrables_preview/add_new_document/<?php echo $projectName['Project']['id']; ?>" class="dropzone" value="" >
                                </div></div>
                            </div>
                            <?php
                        echo $this->Form->end(__('Ajouter votre document', true));
                        ?>
                    </div>
                    <?php if(($canModified && !$_isProfile) || $_canWrite){ ?>
                    <a class="add-new-item" href="javascript:;"><img title="Add an item" src="/img/new-icon/add.png"></a>
                    <?php } ?>
                </div></div>
                <div id="pager" style="width:100%;height:0px; overflow: hidden;">

                </div>
            </div>
            <?php echo $this->element('grid_status'); ?>
            </div></div>
        </div>
    </div>
</div>

 <?php            /* -------- Content------------ */ ?>
<div id="action-template" style="display: none;">
    <div class="action-menu">
        <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="action-menu-item" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">
		<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 20.03 20">
		  <defs>
			<style>
			  .cls-1 {
				fill: #666;
				fill-rule: evenodd;
			  }
			</style>
		  </defs>
		  <path id="suppr" class="cls-1" d="M6644.04,275a0.933,0.933,0,0,1-.67-0.279l-8.38-8.374-8.38,8.374a0.954,0.954,0,1,1-1.35-1.347l8.38-8.374-8.38-8.374a0.954,0.954,0,0,1,1.35-1.347l8.38,8.374,8.38-8.374a0.933,0.933,0,0,1,.67-0.279,0.953,0.953,0,0,1,.67,1.626L6636.33,265l8.38,8.374A0.953,0.953,0,0,1,6644.04,275Z" transform="translate(-6624.97 -255)"/>
		</svg>
		</a>
        
    </div>
</div>
<div id="attachment-template" style="display: none;">
    <div style="overflow: hidden;" class="img-to-right">
        <!-- <a class="download-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '?' => array('type' => 'download'))); ?>"><?php echo __('Download', true); ?></a>
        &nbsp; -->
        <a class="delete-attachment" href="<?php echo $this->Html->url(array('action' => 'Attachement', '%1$s', '?' => array('type' => 'delete'))); ?>" rel="%2$s"><?php echo __('Delete', true); ?></a>
    </div>
</div>
<div id="attachment-template-url" style="display: none;">
    <div style="overflow: hidden;" class="img-to-right">
        <a class="url-attachment" target="_blank" href="<?php echo 'http://%2$s'; ?>"></a>
        &nbsp; <a class="delete-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '?' => array('type' => 'delete'))); ?>" rel="%3$s"><?php echo __('Delete', true); ?></a>
    </div>
</div>
<div id="template-local-file" style="display: none;">
    <div id="local_directory_link">

    </div>
</div>
<!-- dialog_attachement_or_url -->
<div id="dialog_attachement_or_url" class="buttons" style="display: none;">
    <fieldset>
        <?php
        echo $this->Form->create('Upload', array(
                'type' => 'file', 'id' => 'form_dialog_attachement_or_url',
                'url' => array('controller' => 'project_livrables', 'action' => 'upload', $projectName['Project']['id'])
            )); ?>
        <div style="height:auto;" class="wd-scroll-form">
            <div class="wd-input">
                <label for="attachement"><?php __("Attachement") ?></label>
                <p id="gs-attach"></p>
                <?php
                echo $this->Form->hidden('id', array('id' => false, 'rel' => 'no-history', 'value' => ''));
                ?>
                <?php
                echo $this->Form->input('attachment', array('type' => 'file', 'value' => '',
                    'name' => 'FileField[attachment]',
                    'label' => false,
                    'class' => 'update_attach_class',
                    'rel' => 'no-history'));
                ?>
            </div>
            <div class="wd-input">
                <label for="url"><?php __("Url") ?></label>
                <p id="gs-url"></p>
                <?php
                echo $this->Form->input('url', array('type' => 'text',
                    'label' => false,
                    'class' => 'update_url',
                    'disabled' => 'disabled',
                    'rel' => 'no-history'));
                ?>
            </div>
            <p style="color: black;margin-left: 146px; font-size: 12px; font-style: italic;">
                <strong>Ex:</strong>
                www.example.com
            </p>
        </div>
        <?php
        echo $this->Form->end();
        ?>
    </fieldset>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"><?php __("Cancel") ?></a></li>
        <li  class="btn-disable item-toggle"><a href="javascript:void(0)" class="new" id="ok_attach"><?php __('OK') ?></a></li>
    </ul>
</div>
<div id="dialog_attachement_drag" class="buttons" style="display: none;">
    <?php
    echo $this->Form->create('Upload', array(
            'type' => 'file', 'class' => 'form_dialog_attachement_drag',
            'url' => array('controller' => 'project_livrables', 'action' => 'upload', $projectName['Project']['id'])
        ));
    echo $this->Form->hidden('id', array('id' => false, 'rel' => 'no-history', 'value' => ''));
    echo $this->Form->input('drag', array('type' => 'file', 'value' => '',
        'name' => 'FileField[attachment]',
        'label' => false,
        'value' => '',
        'class' => 'update_drag_class',
        'draggable' => "true",
        'rel' => 'no-history'));
    echo $this->Form->end();
    ?>
</div>
<!-- dialog_attachement_or_url.end -->

<div id="template_comment" style="height: 420px; width: 320px;display: none;" class="loading-mark">
    <div id="content_comment">
    <div class="append-comment"></div>
    </div>
    <div class="add-comment"></div>
    
</div>
<div id="template_attach" style="height: auto; width: 280px; display: none;">
    <div class="heading">
        <span class="close"><img title="close"  src="<?php echo $html->url('/img/new-icon/close.png'); ?>"/></span>
    </div>
    <div id="content_comment">
        <div class="append-comment"></div>
    </div> 
    <div class="trigger-upload"><form id="upload-widget" onsubmit="completeAndRedirect()" method="post" action="/project_livrables_preview/upload/<?php echo $projectName['Project']['id']; ?>" class="dropzone" value="" >
        <input type="hidden" name="data[Upload][id]" rel="no-history" value="" id="UploadId">
    </form></div>
</div>
<div class="light-popup"></div>
<div id="icon-comment" style="display: none"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
  <defs>
    <style>
      .cls-1 {
        fill: #666;
        fill-rule: evenodd;
      }
    </style>
  </defs>
  <path id="Z0gMSG" class="cls-1" d="M683.124,30h-6.249a0.625,0.625,0,1,0,0,1.25h6.249A0.625,0.625,0,1,0,683.124,30ZM680,20c-5.523,0-10,3.918-10,8.75a8.375,8.375,0,0,0,3.75,6.824V40l5.12-2.56c0.371,0.036.747,0.059,1.13,0.059,5.523,0,10-3.917,10-8.749S685.523,20,680,20Zm0,16.25c-1.435,0-1.25,0-1.25,0L675,38.125V34.864a7.213,7.213,0,0,1-3.751-6.114c0-4.142,3.918-7.5,8.751-7.5s8.749,3.358,8.749,7.5S684.832,36.25,680,36.25Zm4.374-10h-8.749a0.625,0.625,0,1,0,0,1.25h8.749A0.625,0.625,0,1,0,684.374,26.25Z" transform="translate(-670 -20)"/>
</svg></div>

<script type="text/javascript">
    var livrableIcon = <?php echo json_encode($livrableIcon) ?>;
    var listAvartar = <?php echo json_encode($list_avatar) ?>;
    var employee_id = <?php echo json_encode($employee_id); ?>;
    var curent_time = <?php echo json_encode(time()); ?>;
    var listEmployee = <?php echo json_encode($listEmployee); ?>;
    var canModified = <?php echo json_encode($canModified); ?>;
    var listId = [];
    var _project_id = <?php echo json_encode($projectName['Project']['id']) ?>;
    var projectFiles1 = {};
	if( typeof set_slick_table_height == 'function') set_slick_table_height();
    else{ 
		var wdTable = $('.wd-table');
		if( wdTable.length){
			var heightTable = $(window).height() - wdTable.offset().top - 80;
			console.log(heightTable);
			wdTable.css({
				height: heightTable,
			});
			wdTable.find('.slick-viewport').css({
				height:  $('.wd-table').height() - $('.slick-pane-header:first').height()
			});
			$(window).resize(function(){
				heightTable = $(window).height() - wdTable.offset().top - 80;
				wdTable.find('.slick-viewport').css({
					height:  $('.wd-table').height() - $('.slick-pane-header:first').height()
				});
			});
		}
	}
    $('#template_attach .close').on( 'click', function (e) {
        $("#template_attach").removeClass('show');
        $(".light-popup").removeClass('show');
    });
		
	
    function get_grid_option(){
        var _option ={
            frozenColumn: '',
            enableAddRow: false,            
            showHeaderRow: true,
            rowHeight: 61,
            topPanelHeight: 40,
			enableAsyncPostRender: true,
            headerRowHeight: 40,
			editable: true,
			enableCellNavigation: true,
			asyncEditorLoading: false,
        };
        if( $(window).width() > 992 ){
            return _option;
        }
        else{
            _option.frozenColumn = '';
            _option.forceFitColumns = false;
            return _option;
        }
    }

    // viet nguyen
    function commentCallback(id) {
        var _html = '';

        var profile = $('.class_'+id+'').data('profile');
        var popup = $('#template_comment');
        popup.find(".add-comment")
            .html('<div class="input-add"><input type="text" class="textarea-ct" name="name" id="document-comment-input"/><button data-id="'+id+'" onclick="addComment(this)" class="submit-btn-msg" type="button" id="document-comment-button"><img src="/img/new-icon/icon-add.png"></button></div>');
		$('#document-comment-input').on('keydown',function(e){						
			if(e.key == 'Enter') addComment('#document-comment-button');			
		});
        $.ajax({
            url: '/project_livrables/getComment',
            type: 'POST',
            data: {
                id: id
            },
            dataType: 'json',
            success: function(data) {
                _html = '<div id="content-comment-id">';
                if (data['comment']) {
                    $.each(data['comment'], function(ind, _data) {
                        var idEm = _data['employee_id']['id'],
                        nameEmloyee = _data['employee_id']['first_name'] + ' ' + _data['employee_id']['last_name'],
                        comment = _data['comment'] ? _data['comment'].replace(/\n/g, "<br>") : '',
                        created = _data['created'];
                        var avartarImage = '<a class="circle-name" title="'+ data.text_updater +'"><img src="' + employeeAvatar_link.replace('%ID%', idEm) + '" alt="'+ data.text_updater +'"></a>';
                        _html += '<div class="content"><div class= "content-employee">'+ avartarImage +'<div class="employee-info"><p>'+ nameEmloyee +'</p><p>'+ created +'</p></div></div><div class="content-comment"><div class="comment">'+ comment +'</div></div></div>';                        
                    });
					
                } else {
                    _html += '';
                }
                _html += '</div>';
                popup.find('#content_comment').empty().append(_html);
				 $('#content_comment').animate({
					  scrollTop: document.body.scrollHeight
				   }, 100);
                var createDialog2 = function(){
                    $('#template_comment').dialog({
                        position    :'center',
                        autoOpen    : false,
                        modal       : true,
                        height      : 453,
                        modal       : true,
                        width       : 320,
                        minHeight   : 50,
                        open : function(e){
                            var $dialog = $(e.target);
                            $dialog.dialog({open: $.noop});
                        }
                    });
                    createDialog2 = $.noop;
                }
                createDialog2();
                $("#template_comment").dialog('option',{title:''}).dialog('open');
                
            }
        });
       
    };
    function addComment(elm){
		var textfield = $(elm).prev('.textarea-ct');
        var text = textfield.val(),
            _id = $(elm).data('id');
        if(text != ''){
			$(elm).closest('.loading-mark').addClass('loading');
            var _html = '';
            $.ajax({
                url: '/project_livrables/update_text',
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
                        avartarImage = '<a class="circle-name" title="'+ data.text_updater +'"><img src="' + employeeAvatar_link.replace('%ID%', idEm) + '" alt="'+ data.text_updater +'"></a>',
                        nameEmloyee = data['text_updater'],
                        comment = data['comment'],
                        created = data['text_time'];
                        _html += '<div class="content"><div class= "content-employee">'+ avartarImage +'<div class="employee-info"><p>'+ nameEmloyee +'</p><p>'+ created +'</p></div></div><div class="content-comment"><div class="comment">'+ comment +'</div></div></div>'; 
                        var comment_cont = $('#template_comment').find('#content_comment #content-comment-id')
						comment_cont.append(_html);
						comment_cont.parent().animate({ scrollTop: comment_cont.height() }, 200);
						textfield.val("");
						$(elm).closest('.loading-mark').removeClass('loading');
                    }
                }
            });
        }
    };
	
    function collapse_table() {
        $('#table-collapse').hide();
        $('#table-expand').show();
        $('.wd-panel').removeClass('treeExpand');
        isFull = false;
        $(window).trigger('resize');
    }
    function expandTable() {
        $('.wd-panel').addClass('treeExpand');
        $('#table-collapse').show();
        $('#table-expand').hide();
        isFull = true;
        $(window).trigger('resize');
    }
    function getUploadFile() {
        id = $(this).data("id");
        name = $(this).data("name");
        var _html = '';
        var latest_update = '';
        $('#UploadId').val(id);
        var popup = $('#template_attach');
        popup.addClass('show');
        $('.light-popup').addClass('show');
        _html = '<p class="project-name">'+ name +'</p>';
        $('#template_attach #content_comment').html(_html);
    };
    Dropzone.autoDiscover = false;
    $(function() {
        var myDropzone = new Dropzone("#upload-widget");
        myDropzone.on("queuecomplete", function(file) {
            location.reload();
        });
        myDropzone.on("success", function(file) {
            myDropzone.removeFile(file);
        });

    });
    // end viet nguyen
    /* Dropzone with form  
     * By Dai Huynh  19-07-2018
     */ 
    $(function() {
        var popupDropzone = new Dropzone("#upload-popup",{
            maxFiles: 1,
            autoProcessQueue: false,
            addRemoveLinks: true,
        });
        popupDropzone.on("success", function(file) {
            popupDropzone.removeFile(file);
        });
        popupDropzone.on("queuecomplete", function(file) {
            location.reload();
        });
        $('#popupUploadIndexForm').on('submit', function(e){
            $('#popupUploadIndexForm').parent('.wd-popup').addClass('loading')
            $('#popupUploadName').val($('#newDocName').val());
            $('#popupUploadUrl').val($('#newDocURL').val());;

            if(popupDropzone.files.length){
                e.preventDefault();
                popupDropzone.processQueue();
            }
        });
        popupDropzone.on('sending', function(file, xhr, formData) {
            // Append all form inputs to the formData Dropzone will POST
            var data = $('#popupUploadIndexForm').serializeArray();
            $.each(data, function(key, el) {
                formData.append(el.name, el.value);
            });
        });
    });
        
    /* End Dropzone with form  */ 

    var DateValidate = {},dataGrid,IuploadComplete = function(json){
        var data = dataGrid.eval('currentEditor');
        data.onComplete(json);

    };
    (function($){
        $(function(){
            function number_format(number, decimals, dec_point, thousands_sep) {
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
            var $this = SlickGridCustom ,
            uploadForm = $('#upload-template');

            $('a.delete-attachment').live('click' , function(){
                var row = $(this).attr('rel'),
                data = dataGrid.getDataItem(row);
                if(data && confirm($this.t(<?php echo json_encode(h(sprintf(__('Delete?', true), '%3$s'))) ?>))){
                    data && $.ajax({
                        cache : false,
                        type : 'GET',
                        url : $(this).attr('href')
                    });
                    data['livrable_file_attachment'] = '';

                    dataGrid.updateRow(row);
                }
                return false;
            });

            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  <?php echo json_encode((!empty($canModified)  && !$_isProfile ) || ($_isProfile && $_canWrite)); ?>;
            // For validate date
            var projectName = <?php echo json_encode($projectName['Project']); ?>;
            var getTime = function(value){
                value = value.split("-");
                return (new Date(parseInt(value[2] ,10), parseInt(value[1], 10), parseInt(value[0], 10))).getTime();
            }

            DateValidate.startDate = function(value){
                value = getTime(value);
                if(projectName['start_date'] == ''){
                    _valid = true;
                    _message = '';
                } else {
                    _valid = value >= getTime(projectName['start_date']);
                    _message = $this.t('Date closing must larger %1$s' ,projectName['start_date']);
                }
                return {
                    valid : _valid,
                    message : _message
                };
            }
            var status = "";
            if(!$this.canModified){
                $('#attachment-template').find('a.delete-attachment').remove();
                status = "disabled";
            }

            var actionTemplate =  $('#action-template').html(),
            attachmentTemplate =  $('#attachment-template').html(),
            attachmentDragTemplate = $('#dialog_attachement_drag').html(),
            attachmentURLTemplate =  $('#attachment-template-url').html();


            $.extend(Slick.AsyncPostRender,{
				renderSlider : function (row, cell, value, columnDef, dataContext){
					console.log(value);
					var cell = $(cellNode);
					cell.find('.slider').slider({
					  value: dataContext.value,
					  slide: function(e, ui) {
						data[row].value = ui.value;
						cell.prev().html(ui.value);
					  }
					});
				}
			});
            $.extend(Slick.Formatters,{
                attachmentName : function(row, cell, value, columnDef, dataContext){
                    return '<div class="document-name" ><p class="att-name" >' + dataContext['name'] + '</p> <p class="file-name">' + dataContext['livrable_file_attachment'] + '</p></div>';
                },
                livrableAttachment : function(row, cell, value, columnDef, dataContext){
                    if(value){
                        if(dataContext.format == 2 || dataContext.format == 1 || dataContext.format == 3){
                            value = $this.t(attachmentTemplate,dataContext.id,row);
                        }
                        return Slick.Formatters.HTMLData(row, cell, value, columnDef, dataContext);
                    }
                    return '<div data-id="'+dataContext.id+'"><a id="upload-file-attachment" class="download-attachment" href="#"</a><a id="upload-file-url" style="margin-left: 0px" class="url-attachment" href="#"></a></div>';
                },
                livrableComment : function(row, cell, value, columnDef, dataContext){
					var icon_comment = $('#icon-comment').html();
                    return '<a class ="livrable-comment class_'+dataContext.id+'"data-profile = "'+ status +'" data-id="'+dataContext.id+'" href="#" class="liv_img_comment" onclick="commentCallback('+dataContext.id+');" >'+icon_comment+'</a>';
                },
                livrableModify : function(row, cell, value, columnDef, dataContext){
					var tag_cur = ''
					_diff = curent_time - value;
					if(_diff < 3600*24*31){ // dưới 1 tháng
						if( _diff < 3600){
							tag_cur = (_diff <= 60) ? '1 ' + $this.i18n.minute : parseInt( _diff /60 ).toString() + ' ' + $this.i18n.minutes;
						}else if(_diff < 3600*24 ){
							tag_cur = (_diff <= 1) ? '1 ' + $this.i18n.hour : parseInt( _diff /3600 ).toString() + ' ' + $this.i18n.hours;	
						}else{
							tag_cur = (_diff == 1) ? '1 ' + $this.i18n.day : parseInt(_diff/(3600*24)).toString() + ' ' + $this.i18n.days;
						}
					}else{ // trên 1 tháng
						var t = 3600*24*31;
						var curr_date = new Date(curent_time*1000);
						var _updated_date = new Date(value*1000);
						if( _diff < 365* t){
							var _jdiff = curr_date.getMonth() - _updated_date.getMonth();
							if( _jdiff <=0 ) _jdiff +=12;
							tag_cur = (_jdiff == 1) ? '1 ' + $this.i18n.month : parseInt(_jdiff).toString() + ' ' + $this.i18n.months;
						}else{
							var _jdiff = curr_date.getFullYear() - _updated_date.getFullYear();
							tag_cur = (_jdiff == 1) ? '1 ' + $this.i18n.year : parseInt(_jdiff).toString() + ' ' + $this.i18n.years;	
						}
					}
					if( tag_cur){
						tag_cur = '<span class="livrable-modify"><i class="icon-clock"></i>' + tag_cur +'</span>';
					}
					return tag_cur;
                },
                livrableResponsible : function(row, cell, value, columnDef, dataContext){
				
					avatar = '';
					if(value && value > 0) {
						avatar = '<p class="livrable-responsible">'+listAvartar[value]['tag'] + listEmployee[value]+'</p>';
					}
					return avatar;
                },
                livrableActor : function(row, cell, value, columnDef, dataContext){
					// console.log(value);
					avatar = '<p class="livrable-responsible">';
					if(value){
						$.each(value, function(val, employee_id){
							avatar += listAvartar[employee_id]['tag'];
						});
					}
					avatar += '</p>';
					return avatar;
                },
                livrableDrag : function(row, cell, value, columnDef, dataContext){
                    if(!dataContext.livrable_file_attachment){
                        if(listId[dataContext.id] && dataContext.id !== undefined){

                        } else {
                            listId[dataContext.id] = dataContext.id;
                        }
                        return '<div data-id="'+dataContext.id+'" id="uploaderDocument'+dataContext.id+'" class="wd-input wd-calendar" style=""></div>';
                    }
                    return Slick.Formatters.HTMLData(row, cell, value, columnDef, dataContext);
                },
                percentValues : function(row, cell, value, columnDef, dataContext){
                    var val = number_format(value, 0);
					var left = 'left: '+val+'%';
					if(val > 9){
						left = 'left: calc('+val+'% - 20px)';
					}
                    var _html = '<div class="livr-progress"><div class="progress-line"><div class="line-active" style="width: '+value+'%;"></div><span style="'+left+'" class="circle"></span></div></div>';
                    return _html;
                },
                percentValuesText : function(row, cell, value, columnDef, dataContext){
                    var val = number_format(value, 0);
                    return '<span class="progress-text">'+val+'%</span>';
                },
				renderSlider : function (row, cell, value, columnDef, dataContext){
					console.log(value);
					var cell = $(cellNode);
					cell.find('.slider').slider({
					  value: dataContext.value,
					  slide: function(e, ui) {
						data[row].value = ui.value;
						cell.prev().html(ui.value);
					  }
					});
				},
                livrableIcon : function(row, cell, value, columnDef, dataContext){
                    var plcid = dataContext.project_livrable_category_id;
                    var format = dataContext.format;
                    var filename = dataContext.livrable_file_attachment; 
                    var _ext = '';
                    if( filename){
                        _ext = filename.split('.');
                        _ext = (_ext.length > 1) ? _ext[_ext.length -1] : '';
                        _ext = _ext.split(" ", 3).join('');
                    }
                    if(dataContext.livrable_file_attachment && dataContext.livrable_file_attachment != ''){
                        if(format == 2){
							var fancy = '';
							if((/\.(gif|jpg|jpeg|tiff|png)$/i).test(filename)){
								fancy =' rel="one_pic_expand" data-fancybox="image" data-type="image" data-fancy="true"';
								console.log(fancy);
							}
							if(plcid && plcid !== undefined && livrableIcon[plcid] && livrableIcon[plcid] !== undefined ){
                                var link = <?php echo json_encode($this->Html->url(array('action' => 'attachment', '{id}', '?' => array('sid' => $api_key)))) ?>.replace('{id}', dataContext.id);
								return '<a href="'+ link +'" class="liv-icon svg"' + fancy + '> <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40">' + liv_icons[livrableIcon[plcid]] + '</svg></a>';
                            } else {
                                var link = <?php echo json_encode($this->Html->url(array('action' => 'attachment', '{id}','?' => array('sid' => $api_key, 'type' => 'download')))) ?>.replace('{id}', dataContext.id);
                                return '<a href="'+ link +'" ' + fancy + ' class="liv-icon"> <img class="icon-gray" src="/img/new-icon/file_icon.png" alt="No extension" > <img class="icon-color" src="/img/new-icon/file_icon_blue.png" alt="' + _ext +' file" ><span class="ext-text">' + _ext + '</span> </a>';
                            }								
                        } else if (format == 1) {
							_htt = filename.substr(0,4);
							_htt = _htt.toString();
							if(_htt == 'http'){
								return '<a target="_blank" href="'+dataContext.livrable_file_attachment+'" class="liv-icon"> <img class="icon-gray" src="/img/new-icon/icon_url.png" alt="No extension" > <img class="icon-color" src="/img/new-icon/icon_url_blue.png" alt="No extension" > </a>';
							}else{
								return '<a target="_blank" href="https://'+dataContext.livrable_file_attachment+'" class="liv-icon"> <img class="icon-gray" src="/img/new-icon/icon_url.png" alt="No extension" > <img class="icon-color" src="/img/new-icon/icon_url_blue.png" alt="No extension" > </a>';
							}
						} else if(format == 3){
                            return '<a href="javascript:void(0);" class="local-file liv-icon" data="'+dataContext.livrable_file_attachment+'"><img class="icon-gray" src="/img/new-icon/file_icon.png" alt="No extension" ><img class="icon-color" src="/img/new-icon/file_icon_blue.png" alt="No extension" ></a>';
                        }
                    }
					return '<a class="upload-file liv-icon" data-name = "'+ dataContext.name +'" data-id = "'+ dataContext.id +'" href="javascript:void(0);" onclick="getUploadFile.call(this);"><img class="icon-gray" src="/img/new-icon/file_upload.png" alt="Upload" ><img class="icon-color" src="/img/new-icon/file_upload_blue.png" alt="Upload" ></a>';
                },
				
                UploadDate : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell, '<div style="text-align: center;">' + value + '</div>', columnDef, dataContext);
                },
                Action : function(row, cell, value, columnDef, dataContext){
                    var filename = dataContext.livrable_file_attachment;
                    var cond = '';
                    if(filename){
                        if((/\.(gif|jpg|jpeg|tiff|png)$/i).test(filename)){
                            cond = 'image-file';
                        }else{
                            cond = 'not-image';
                        }
                    }else {
                        cond = 'no-file';
                    }
                    var link_open = <?php echo json_encode($this->Html->url(array('action' => 'attachment', '{id}', '?' => array('sid' => $api_key)))) ?>.replace('{id}', dataContext.id);
                    var link_download = <?php echo json_encode($this->Html->url(array('action' => 'attachment', '{id}','?' => array('download' => true, 'sid' => $api_key)))) ?>.replace('{id}', dataContext.id);
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,dataContext.project_id,
                    $this.selectMaps.project_livrable_category_id[dataContext.project_livrable_category_id], link_open, link_download, cond),
                    columnDef, dataContext);
                }
            });
            $.extend(Slick.Editors,{
                livrableAttachment : function(args){
                    var self = this;
                    $.extend(this, new BaseSlickEditor(args));
                    this.input = $('<div><a class="download-attachment" href="#"</a><a style="margin-left: 45px" class="url-attachment" href="#"></a></div>')
                    .appendTo(args.container).attr('rel','no-history');
                    $("#ok_attach").click(function(){
                        $('#action-attach-url').css('display', 'none');
                        $('.browse').css('display', 'block');
                        $("#dialog_attachement_or_url").dialog('close');

                        var form = $("#form_dialog_attachement_or_url");
                        form.find('input[name="data[Upload][id]"]').val(args.item.id);
                        form.submit();
                    });
                    this.focus();
                }
            });

            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
		
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                project_id : {defaulValue : projectName['id'], allowEmpty : false},
                project_livrable_category_id : {defaulValue : ''},
                project_livrable_status_id : {defaulValue : ''},
                livrable_progression : {defaulValue : 0},
                livrable_responsible : {defaulValue : ''},
                livrable_date_delivery : {defaulValue : ''},
                livrable_date_delivery_planed : {defaulValue : ''},
                livrable_file_attachment : {defaulValue : '' , required : ['id']},
                format : {defaulValue : ''},
                version : {defaulValue : ''},
                actor_list : {defaulValue : []},
                name : {defaulValue : '', allowEmpty : false},
                weight : {defaulValue : 0 }
            };
            $this.url =  '<?php echo $html->url(array('controller' => 'project_livrables_preview','action' => 'update')); ?>';
            $this.onBeforeEdit = function(args){
				if( !canModified) return;
                if(args.column.field == 'livrable_file_attachment' && (!args.item || args.item['livrable_file_attachment'] || !args.item['id'])){
                    return false;
                }
                return true;
            }

            $this.onCellChange = function(args){
                var columns = args.grid.getColumns(),
                    col, cell = args.cell;
                do {
                    cell++;
                    if( columns.length == cell )break;
                    col = columns[cell];
                } while (typeof col.editor == 'undefined');
            }
            // Grid init 
            dataGrid = $this.init($('#project_container'),data,columns, get_grid_option());
			
			$('.image-file .fancy.image, .slick-cell >.fancy.image, [data-fancy="true"]').fancybox({
			   type: 'image'
			});
            dataGrid.setSortColumns('weight' , true);
            dataGrid.setSelectionModel(new Slick.RowSelectionModel());
            $('.row-number').parent().addClass('row-number-custom');
            var moveRowsPlugin = new Slick.RowMoveManager({
                cancelEditOnDrag: true
            });
            moveRowsPlugin.onBeforeMoveRows.subscribe(function (e, data) {
                for (var i = 0; i < data.rows.length; i++) {
                    if (data.rows[i] == data.insertBefore || data.rows[i] == data.insertBefore - 1) {
                        e.stopPropagation();
                        return false;
                    }
                }
                return true;
            });
            $(dataGrid.getHeaderRow()).delegate(":input", "change keyup", function (e) {
                var text = $(this).val();
                if( text != '' ){
                    $(this).parent().css('border', 'solid 2px orange');
                } else {
                    $(this).parent().css('border', 'none');
                }
            });
            //fire after row move completed
            moveRowsPlugin.onMoveRows.subscribe(function (e, args) {
                var extractedRows = [], left, right;
                var rows = args.rows;
                var insertBefore = args.insertBefore;
                left = data.slice(0, insertBefore);
                right = data.slice(insertBefore, data.length);
                rows.sort(function(a,b) { return a-b; });
                for (var i = 0; i < rows.length; i++) {
                    extractedRows.push(data[rows[i]]);
                }
                rows.reverse();
                for (var i = 0; i < rows.length; i++) {
                    var row = rows[i];
                    if (row < insertBefore) {
                        left.splice(row, 1);
                    } else {
                        right.splice(row - insertBefore, 1);
                    }
                }
                data = left.concat(extractedRows.concat(right));

                var selectedRows = [];
                for (var i = 0; i < rows.length; i++)
                    selectedRows.push(left.length + i);

                //update no.
                var orders = { data : {} };
                for(var i = 0; i < data.length; i++){
                    data[i]['no.'] = (i+1);
                    data[i].weight = (i+1);
                    orders.data[data[i].id] = (i+1);
                }
                //ajax call
                $.ajax({
                    url : '<?php echo $html->url('/project_livrables/order/' . $projectName['Project']['id']) ?>',
                    type : 'POST',
                    data : orders,
                    success : function(){
                    },
                    error: function(){
                        location.reload();
                    }
                });
                dataGrid.resetActiveCell();
                var dataView = dataGrid.getDataView();
                dataView.beginUpdate();
                dataView.setItems(data);
                dataView.endUpdate();
                dataGrid.setSelectedRows(selectedRows);
                dataGrid.render();
            });

            dataGrid.registerPlugin(moveRowsPlugin);
            dataGrid.onDragInit.subscribe(function (e, dd) {
                e.stopImmediatePropagation();
            });
            // add new colum grid
            //ControlGrid = $this.init($('#project_container'),data,columns);
            addNewDeliverablesButton = function(){
                dataGrid.gotoCell(data.length, 1, true);
            }
            /* table .end */
            var createDialog = function(){
                $('#dialog_attachement_or_url').dialog({
                    position    :'center',
                    autoOpen    : false,
                    autoHeight  : true,
                    modal       : true,
                    width       : 500,
                    open : function(e){
                        var $dialog = $(e.target);
                        $dialog.dialog({open: $.noop});
                    }
                });
                createDialog = $.noop;
            }
            $("#upload-file-url").live('click',function(){
                createDialog();
                var titlePopup = <?php echo json_encode(__('Attachement or URL', true))?>;
                $("#dialog_attachement_or_url").dialog('option',{title:titlePopup}).dialog('open');
            });
            $("#upload-file-attachment").live('click', function(){
                createDialog();
                var titlePopup = <?php echo json_encode(__('Attachement or URL', true))?>;
                $("#dialog_attachement_or_url").dialog('option',{title:titlePopup}).dialog('open');
            });
            //
            $('.update_drag_class').live('click', function(){
                $(this).val('');
                return false;
            });
            $('.update_drag_class').live('change', function(){
                var form = $(this).parent().parent();
                var _id = $(form).parent().attr('data-id');
                form.find('input[name="data[Upload][id]"]').val(_id);
                form.submit();
            });
            
            window.addEventListener("dragover",function(e){
                e = e || event;
                if(!$(e.target).hasClass('update_drag_class')){
                    e.preventDefault();
                }
            },false);
            window.addEventListener("drop",function(e){
                if(!$(e.target).hasClass('plupload_droptext') && !$(e.target).hasClass('plupload_filelist')){
                    e.preventDefault();
                } else {
                    if($(e.target).hasClass('plupload_droptext')){
                        var element = $(e.target).parent().next().find('a.plupload_start');
                    } else {
                        var element = $(e.target).next().find('a.plupload_start');
                    }
                    setTimeout(function(){
                        $(element).trigger('click');
                    }, 200);
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                }
            },false);
            /**
             * Multiple Upload
             */
            setTimeout(function(){
                for (var i = 0; i < listId.length; i++) {
                    if(listId[i] !== undefined){
                        var uploader = $("#uploaderDocument" + listId[i]).pluploadQueue({
                            runtimes : 'html5, html4',
                            url : "/project_livrables/uploads/" + <?php echo json_encode($projectName['Project']['id']) ?> + '/' + listId[i],
                            chunk_size : '10mb',
                            rename : true,
                            dragdrop: true,
                            filters : {
                                max_file_size : '10mb',
                                mime_types: [
                                    {title : "Files", extensions : "jpg,jpeg,bmp,gif,png,swf,txt,zip,rar,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,csv,eml,msg,xlsm"}
                                ]
                            },
                            init: {
                                PostInit: function(up) {
                                    up.project_id = _project_id;
                                    up.linkedAction = '/project_livrables/attachment/';
                                    if(projectFiles1 && Object.keys(projectFiles1).length > 0){
                                        up.auditFiles = projectFiles1;
                                        var tmpHtml = '';
                                        var display_none = '';
                                        if(showAllFieldYourform == 0){
                                            display_none = 'display: none';
                                        }
                                        $.each(projectFiles1, function(ind, val){
                                            var hrefDownload = '/projects/attachment/upload_documents_1'+'/'+_project_id+'/'+val.id+'/download/';
                                            var hrefDelete = '/projects/attachment/upload_documents_1'+'/'+_project_id+'/'+val.id+'/delete/';
                                            tmpHtml +=
                                            '<li id="' + val.id + '" class="plupload_done">' +
                                                '<div class="plupload_file_name"><span>' + val.file_attachment + '</span></div>' +
                                                '<div class="plupload_file_action_modify">' +
                                                '<a class="download-attachment" href="' +hrefDownload+ '" rels=' + val.id + '>Download</a>' +
                                                '<a class="delete-attachment" style="'+display_none+'" href="' +hrefDelete+ '" rels=' + val.id + '>Delete</a></div>' +
                                                '<div class="plupload_file_action"><a href="#" style="display: block;"></a></div>' +
                                                '<div class="plupload_file_status">' + 100 + '%</div>' +
                                                '<div class="plupload_file_size">' + plupload.formatSize(val.size) + '</div>' +
                                                '<div class="plupload_clearer">&nbsp;</div>' +
                                            '</li>';
                                        });
                                        $('#uploaderDocument1_filelist').html(tmpHtml);
                                    }
                                }
                            }
                        });
                    }
                }
            }, 2000);

            $('.local-file').live('click', function(){
                var createDialog1 = function(){
                    $('#template-local-file').dialog({
                        position    :'center',
                        autoOpen    : false,
                        autoHeight  : true,
                        modal       : true,
                        width       : 500,
                        minHeight   : 50,
                        open : function(e){
                            var $dialog = $(e.target);
                            $dialog.dialog({open: $.noop});
                        }
                    });
                    createDialog = $.noop;
                }
                createDialog1();
                var titlePopup = <?php echo json_encode(__('Local directory', true))?>;
                $("#template-local-file").dialog('option',{title:titlePopup}).dialog('open');
                var url = $(this).attr('data');
                $("#template-local-file").find('input').val(url);
                $('#local_directory_link').append('<a style="color: black; font-size: 13px;margin-left: 10px;line-height: 29px;" target="_blank" href="file:///'+url+'">'+url+'</a>')
            });
            $('#UploadAttachment').live('change', function(){
                var form = $("#form_dialog_attachement_or_url");
                var _id = $('.active.selected').find('div').attr('data-id');
                form.find('input[name="data[Upload][id]"]').val(_id);
                form.submit();
            });
            $("#ok_attach").click(function(){
                //self.input[0].remove();
                $('#action-attach-url').css('display', 'none');
                $('.browse').css('display', 'block');
                $("#dialog_attachement_or_url").dialog('close');
                var _id = $('.active.selected').find('div').attr('data-id');
                var form = $("#form_dialog_attachement_or_url");
                form.find('input[name="data[Upload][id]"]').val(_id);
                form.submit();
            });
            $("#action-attach-url").live('click',function(){
                createDialog();
                var titlePopup = <?php echo json_encode(__('Attachement or URL', true))?>;
                $("#dialog_attachement_or_url").dialog('option',{title:titlePopup}).dialog('open');
            });

            $(".cancel").live('click',function(){
                $("#dialog_attachement_or_url").dialog('close');
            });
            $("#gs-url").click(function(){
                $(this).addClass('gs-url-add');
                $('#gs-attach').addClass('gs-attach-remove');
                $('.item-toggle').removeClass('btn-disable');
                $('.update_url').removeAttr('disabled').css('border', '1px solid #3B57EE');
                $('.update_attach_class').attr('disabled', 'disabled').css('border', '1px solid #d4d4d4');
            });
            $('#hidden_checkbox').click(function(){
                var checked = $('#hidden_checkbox').attr('checked');
                var url = window.location.pathname;
                checked = checked && checked == "checked" ? true : false;
                $.ajax({
                    url: '/project_livrables/saveHiddenColums/' + <?php echo json_encode($projectName['Project']['id']) ?>,
                    type: 'POST',
                    data: {
                        checked : checked,
                        url : url
                    },
                    dataType: 'json',
                    success: function(data) {
                        location.reload();
                    }
                });
            });
            $("#gs-attach").click(function(){
                $(this).removeClass('gs-attach-remove');
                $('#gs-url').removeClass('gs-url-add');
                $('.item-toggle').addClass('btn-disable');
                $('.update_attach_class').removeAttr('disabled').css('border', '1px solid #3B57EE');;
                $('.update_url').attr('disabled', 'disabled').css('border', '1px solid #d4d4d4');
            });
            $('.row-number').parent().addClass('row-number-custom');

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
			$.ajax({
				url : '/employees/history_filter',
				type: 'POST',
				data: {
					data: {
						path: HistoryFilter.here,						
					}
				},
				success : function(respons){
					var _data =  $.parseJSON(respons);
					$.each(_data, function( _index, _val){
						if( _index.indexOf('Resize') == -1){
							 _data[_index]='';
						}
						
					});
					HistoryFilter.stask = _data;
					HistoryFilter.send();
					setTimeout(function(){
						location.reload();
					}, 500);
				}
			});
        }
        $(window).on('resize', function(){
            dataGrid.resizeCanvas();
        });
        $('.add-new-item').on('click', function(){

            $("#popupUploadIndexForm").trigger('reset');
            $('.wd-popup-container').toggleClass('open');
        });
        // $('body').on('click', function(e){
        //     if($('.wd-popup-container').find(e.target).length){
        //         return;
        //     }
        //     if( !( $(e.target).hasClass('wd-popup-container') || $('.wd-popup-container').find(e.target).length)){
        //         $('.wd-popup-container').removeClass('open');
        //     }

        // });

    })(jQuery);
    function changeFilter(elm){
            return 0;
        }
	var cb_data = '';
	function wd_oncellchange_callback(cb_data){
		if(cb_data){
			var dataView = dataGrid.getDataView();
			var row = dataGrid.getData().getRowById(cb_data['data']['id']);
			var this_item = dataGrid.getData().getItem(row);
			dataView.beginUpdate();
			this_item['livrable_time_modify'] = cb_data['data']['updated'].toString();	
			dataView.updateItem(cb_data['data']['id'], this_item);
			dataView.endUpdate();
			dataGrid.render();
		}
	}
	$('.wd-title-filter').on('change', function(){
		$.each( $('.wd-title-filter'), function(i, _src){
			// var _targets = $( $(_src).data('target'));
			var _target = $('#project_livrable_category_id-check-'+$(_src).val());
			$(_target).prop('checked', $(_src).is(':checked')).trigger('change');
		} ); 
	});
</script>