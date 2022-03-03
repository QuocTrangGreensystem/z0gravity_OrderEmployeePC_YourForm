<?php
$gapi = GMapAPISetting::getGAPI();

App::import("vendor", "str_utility");
$str_utility = new str_utility();
?>
<?php echo $html->css('dropzone.min'); ?>
<?php echo $html->script('dropzone.min'); 
echo $html->script('preview/define_limit_date'); 
echo $html->script(array(
    'jquery.dataTables',
    'jquery.validation.min',
    'validateDate',
    'jquery.multiSelect',
    'jshashtable-2.1',
    'jquery.numberformatter-1.2.3',
    'jquery.maxlength-min',
    'multipleUpload/plupload.full.min',
    'multipleUpload/jquery.plupload.queue',
    'slick_grid/lib/jquery-ui-1.8.16.custom.min',
    'slick_grid/lib/jquery.event.drag-2.0.min',
    'jquery.flexslider-min',
    'jquery.fancybox.pack',
    'tinymce/tinymce.min',
    'html2canvas',
    'jquery.html2canvas.organization',
    'jquery.form'
));
echo $html->css(array(
    'jquery.multiSelect',
    'jquery.dataTables',
    'multipleUpload/jquery.plupload.queue',
    'business',
    'flexslider',
    'jquery.fancybox',
    'preview/your-form',
));
?>
<style>
    .content-right-inner{
        background-color: transparent;
    }
</style>
<?php 
    function multiSelect($_this, $fieldName, $fielData, $_data, $textHolder, $pc = array()){
        $checkAvatar = $_this->viewVars['checkAvatar'];
        $cotentField = '';
        $avatar = '';
        $cotentField = '<div class="wd-multiselect multiselect multiselect-pm" onchange="saveFieldMutiSelectYourForm(this)">
        <div class="wd-combobox wd-project-manager">';
        if(!empty($_data)){
            foreach($_data as $idPm => $is_pc):
                if($is_pc == 0){
                    if(isset($checkAvatar[$idPm])){
                        $avatar = '<img src="' . $_this->UserFile->avatar($idPm) . '" />';
                    }else{
                        if(isset($fielData[$idPm])){
                            $employee_name = explode(' ', $fielData[$idPm]);
                            $avatar = substr( trim($employee_name[0]),  0, 1) .''.substr( trim($employee_name[1]),  0, 1);
                        }
                    }
                    if($avatar) $cotentField .= '<a class="circle-name" title="' . $fielData[$idPm] . '"><span data-id="'. $idPm .'">'. $avatar .'</a>';
                }
            endforeach;
            foreach($_data as $idPC => $is_pc):
                if($is_pc && isset($pc[$idPC])){
                    $cotentField .= '<a class="circle-name" title="PC / ' . $pc[$idPC] . '"><span data-id="'. $idPC .'-1"><i class="icon-people"></i></span></a>';
                }
            endforeach;      
            $cotentField .= '<p style="position: absolute; color: #c6cccf; display: none">'. $textHolder .'</p>';
        }else{
            $cotentField .= '<p style="position: absolute; color: #c6cccf">'. $textHolder .'</p>';
        }
        
        $cotentField .= '</div><div class="wd-combobox-content '. $fieldName .'" style="display: none;">
        <div class="context-menu-filter"><span><input type="text" class="wd-input-search" placeholder="Rechercher..." rel="no-history"></span></div><div class="option-content">';
        foreach($fielData as $idPm => $namePm):
            if($checkAvatar[$idPm]){
                $avatar = '<img src="' . $_this->UserFile->avatar($idPm) . '" />';
            }else{
                $employee_name = explode(' ', $namePm);
                $avatar = substr( trim($employee_name[0]),  0, 1) .''.substr( trim($employee_name[1]),  0, 1);
            }
            $cotentField .= '<div class="projectManager wd-data-manager wd-group-' . $idPm . '">
                <p class="projectManager wd-data">
                    <a class="circle-name" title="' . $fielData[$idPm] . '"><span data-id = "'. $idPm . '">'. $avatar .'</span></a>' .
                    $_this->Form->input($fieldName, array(
                        'label' => false,
                        'div' => false,
                        'type' => 'checkbox',
                        'name' => 'data['. $fieldName .'][]',
                        'value' => $idPm,
                        'checked' => array_key_exists($idPm, $_data) ? 'checked' : '' 

                        )) .'
                    <span class="option-name" style="padding-left: 5px;">' . $namePm . '</span>
                </p>
            </div>';
        endforeach;
        if(!empty($pc)): 
            foreach($pc as $idPm => $namePm):
                $cotentField .= '<div class="projectManager wd-data-manager wd-group-' . $idPm . '">
                    <p class="projectManager wd-data">
                        <a class="circle-name" title="PC / ' . $pc[$idPm] . '"><span data-id = "'. $idPm . '"><i class="icon-people"></i></span></a> '.
                         $_this->Form->input($fieldName, array(
                            'label' => false,
                            'div' => false,
                            'type' => 'checkbox',
                            'name' => 'data['. $fieldName .'][]',
                            'checked' => array_key_exists($idPm, $_data) ? 'checked' : '',
                            'value' => $idPm . '-1')) .'
                        <span class="option-name" style="padding-left: 5px;">PC / ' . $namePm . '</span>
                    </p>
                </div>';
            endforeach; 
        endif;
        $cotentField .= '</div></div></div>';
        return $cotentField;
    }
    if( !isset($listEmployeeManagers['PM'])) $listEmployeeManagers['PM'] = '';
    if( !isset($listEmployeeManagers['RA'])) $listEmployeeManagers['RA'] = '';
    $fieldProjecManager = multiSelect($this, 'project_employee_manager', $_employees['pm'], $listEmployeeManagers['PM'], __d(sprintf($_domain, 'Details'), 'Project Manager', true));
    $fieldReadAccess = multiSelect($this, 'read_access', $_employees['pm'], $listEmployeeManagers['RA'], __d(sprintf($_domain, 'Details'), 'Read Access', true), $profitCenters);
?>

<div id="wd-container-main" class="wd-project-detail">

    <div class="wd-layout">
        <div class="wd-main-content wd-content-yourform">
            <div class="openMenu" onclick="openMenuLeft()";><img title="burger"  src="<?php echo $html->url('/img/new-icon/list-black.png'); ?>"/></div>
 
                    <?php echo $this->element("secondary_menu_preview"); ?>

            <!-- end: title -->
            
            <div class="wd-content-right">
                <div class="content-right-inner">
                    <div class="wd-section" id="wd-fragment-1">
                        <?php  //echo '<h3 class="description">'. __d(sprintf($_domain, 'Details'), 'Description du projet', true) .'</h3>';
                            echo $this->Session->flash(); ?>
                        <?php
                        echo $this->Form->create('Project', array('enctype' => 'multipart/form-data', 'id' => 'ProjectEditForm'));
                        echo $this->Form->input('id');
                        echo $this->Form->input('tmp_activity', array('div' => false, 'label' => false, 'type' => 'hidden'));
                        echo $this->Form->hidden('category');
                        echo $this->Form->hidden('is_staffing', array('id' => 'project-is-staffing'));
                        if($this->data['Project']['category'] != 1){
                            echo $this->Form->hidden('activated');
                        }
                        ?>
<?php

//chief template
$chiefTemplate = '<div class="multiselect" onchange="saveFieldMutiSelectYourForm(this)" style="margin-right: 3px;">
    <a href="" class="wd-combobox-2"></a>
    <div id="wd-data-project-2" style="display: none;" class="chiefBusiness">';
foreach($_employees['pm'] as $idPm => $namePm):
    $chiefTemplate .= '
    <div class="chiefBusiness wd-data-manager wd-group-' . $idPm . '">
        <p class="chiefBusiness wd-data" style="width: 200px; margin: 10px 5px;">' .
         $this->Form->input('chief_business_list', array(
                'label' => false,
                'div' => false,
                'type' => 'checkbox',
                'class' => 'chiefBusiness',
                'name' => 'data[chief_business_list][]',
                'value' => $idPm))
            . '<span class="chiefBusiness" style="padding-left: 5px;">' . $namePm . '</span>
        </p>
    </div>';
endforeach;
$chiefTemplate .= '</div></div>';
$urlCB= $this->UserFile->avatar($this->data['Project']['chief_business_id']);
// $chiefTemplate .= '<img src="' . $urlCB . '" />';
//end chief
$techTemplate = '<div class="multiselect" onchange="saveFieldMutiSelectYourForm(this)" style="margin-right: 3px;">
    <a href="" class="wd-combobox-3"></a>
    <div id="wd-data-project-3" style="display: none;" class="technicalManager">';
foreach($_employees['pm'] as $idPm => $namePm):
    $techTemplate .= '<div class="technicalManager wd-data-manager wd-group-' . $idPm . '">
        <p class="technicalManager wd-data" style="width: 200px; margin: 10px 5px;">' .
                $this->Form->input('technical_manager_list', array(
                    'label' => false,
                    'div' => false,
                    'type' => 'checkbox',
                    'class' => 'technicalManager',
                    'name' => 'data[technical_manager_list][]',
                    'value' => $idPm)) .
            '<span class="technicalManager" style="padding-left: 5px;">' . $namePm . '</span>
        </p>
    </div>';
endforeach;
$techTemplate .= '</div></div>';
$urlTM= $this->UserFile->avatar($this->data['Project']['technical_manager_id']);
// $techTemplate .= '<img src="' . $urlTM . '" />';
// functional leader & uat manager
$leaderTemplate = '<div class="multiselect" onchange="saveFieldMutiSelectYourForm(this)" style="margin-right: 3px;">
    <a href="javascript:void();" class="wd-combobox-4"></a>
    <div id="wd-data-project-4" style="display: none;" class="functionalLeader">';
foreach($_employees['pm'] as $idPm => $namePm):
    $leaderTemplate .= '<div class="functionalLeader wd-data-manager wd-group-' . $idPm . '">
        <p class="functionalLeader wd-data" style="width: 200px; margin: 10px 5px;">' .
            $this->Form->input('functional_leader_list', array(
                'label' => false,
                'div' => false,
                'type' => 'checkbox',
                'class' => 'functionalLeader',
                'name' => 'data[functional_leader_list][]',
                'value' => $idPm)) .
            '<span class="functionalLeader" style="padding-left: 5px;">' . $namePm . '</span>
        </p>
    </div>';
endforeach;
$leaderTemplate .= '</div></div>';
$urlTM= $this->UserFile->avatar($this->data['Project']['functional_leader_id']);
// $leaderTemplate .= '</div></div><img src="' . $urlTM . '" />';
// read access.
$readAccessTemplate = '';
if(!$adminSeeAllProjects):
    $readAccessTemplate = '<div class="multiselect" onchange="saveFieldMutiSelectYourForm(this)" style="margin-right: 3px;">
        <a href="javascript:void();" class="wd-combobox-6"></a>
        <div id="wd-data-project-6" style="display: none; height: 150px; overflow: auto; border: 1px solid #E1E6E8;" class="readAccess">';
    foreach($_employees['pm'] as $idPm => $namePm):
        $readAccessTemplate .= '<div class="readAccess wd-data-manager wd-group-' . $idPm .'-0' . '">
            <p class="readAccess wd-data" style="width: 200px; margin: 10px 5px;">' .
                $this->Form->input('read_access', array(
                    'label' => false,
                    'div' => false,
                    'type' => 'checkbox',
                    'class' => 'readAccess',
                    'name' => 'data[read_access][]',
                    'value' => $idPm . '-0')) .
                '<span class="readAccess" style="padding-left: 5px;">' . $namePm . '</span>
            </p>
        </div>';
    endforeach;
    foreach($profitCenters as $idPm => $namePm):
        $readAccessTemplate .= '<div class="readAccess wd-data-manager wd-group-' . $idPm .'-1' . '">
            <p class="readAccess wd-data" style="width: 200px; margin: 10px 5px;">' .
                $this->Form->input('read_access', array(
                    'label' => false,
                    'div' => false,
                    'type' => 'checkbox',
                    'class' => 'readAccess',
                    'name' => 'data[read_access][]',
                    'value' => $idPm . '-1')) .
                '<span class="readAccess" style="padding-left: 5px;">' . 'PC / ' . $namePm . '</span>
            </p>
        </div>';
    endforeach;
    $urlTM= $this->UserFile->avatar($this->data['Project']['functional_leader_id']);
    // $readAccessTemplate .= '</div></div><img src="' . $urlTM . '" />';
    $readAccessTemplate .= '</div></div>';
endif;

$uatTemplate = '<div class="multiselect" onchange="saveFieldMutiSelectYourForm(this)" style="margin-right: 3px;">
    <a href="javascript:void();" class="wd-combobox-5"></a>
    <div id="wd-data-project-5" style="display: none;" class="uatManager">';
foreach($_employees['pm'] as $idPm => $namePm):
    $uatTemplate .= '<div class="uatManager wd-data-manager wd-group-' . $idPm . '">
        <p class="uatManager wd-data" style="width: 200px; margin: 10px 5px;">' .
            $this->Form->input('uat_manager_list', array(
                'label' => false,
                'div' => false,
                'type' => 'checkbox',
                'class' => 'uatManager',
                'name' => 'data[uat_manager_list][]',
                'value' => $idPm)) .
            '<span class="uatManager" style="padding-left: 5px;">' . $namePm . '</span>
        </p>
    </div>';
endforeach;
$urlTM= $this->UserFile->avatar($this->data['Project']['uat_manager_id']);
$uatTemplate .= '</div></div>';

$cuPhaseTemplate = '<div class="multiselect" onchange="saveFieldMutiSelectYourForm(this)" style="margin-right: 3px;">
    <a href="javascript:void();" class="wd-combobox-7"></a>
    <div id="wd-data-project-7" style="display: none;" class="currentPhase">';
foreach($ProjectPhases as $idPm => $namePm):
    $cuPhaseTemplate .= '<div class="currentPhase wd-data-manager wd-group-' . $idPm . '">
        <p class="currentPhase wd-data" style="width: 200px; margin: 10px 5px;">' .
            $this->Form->input('project_phase_id', array(
                'label' => false,
                'div' => false,
                'type' => 'checkbox',
                'class' => 'currentPhase',
                'name' => 'data[project_phase_id][]',
                'value' => $idPm)) .
            '<span class="currentPhase" style="padding-left: 5px;">' . $namePm . '</span>
        </p>
    </div>';
endforeach;
$cuPhaseTemplate .= '</div></div>';
if(!empty($projectPhasePlans)){
    $_start_date = $projectPhasePlans[0][0]['MinStartDate'];
    $_end_date = $projectPhasePlans[0][0]['MaxEndDate'];
}
$_start_date = isset($_start_date) ? $_start_date : null;
$startTemplate = $this->Form->input('start_date', array('div' => false,
    'label' => false,
    'style' => '',
    'value' => $str_utility->convertToVNDate($_start_date),
    'type' => 'text',
    'class' => 'wd-date',
    'onchange' => 'saveFieldYourForm(this)',
));
$maps = array(
    'project_amr_program_id' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Program', true),
        'html' => $this->Form->input('project_amr_program_id', array('div' => false, 'label' => false,
            "empty" => __("--Select--", true),
            "options" => $ProjectArmPrograms,
            'onchange' => 'saveFieldYourForm(this)'
        )),
        'position' => 'top',
    ),
    'project_type_id' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Project type', true),
        'html' => $this->Form->input('project_type_id', array('div' => false, 'label' => false,
            "empty" => __("--Select--", true),
            "options" => $ProjectTypes,
            'onchange' => 'saveFieldYourForm(this)'
        ))
    ),
    'project_name' => array(
        'label' => __d(sprintf($_domain, 'Details'), "Project Name", true),
        'html' => $this->Form->input('project_name', array('div' => false, 'onfocusout' => 'saveFieldYourForm(this)', 'label' => false, 'maxlength' => 124, 'style' => 'border-color: red')),
        'position' => 'top',
    ),
    'company_id' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Company', true),
        'html' => '<p style="padding-top: 6px">' . $name_company . '</p>'
    ),
    'long_project_name' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Project long name', true),
        'html' => $this->Form->input('long_project_name', array('div' => false, 'onfocusout' => 'saveFieldYourForm(this)', 'label' => false))
    ),
    'project_code_2' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Project Code 2', true),
        'html' => $this->Form->input('project_code_2', array('div' => false, 'onfocusout' => 'saveFieldYourForm(this)', 'label' => false))
    ),
    'project_manager_id' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Project Manager', true),
        'html' => $fieldProjecManager,
        'position' => 'top',
    ),
    
    'project_sub_type_id' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Sub type', true),
        'html' => $this->Form->input('project_sub_type_id', array('div' => false, 'label' => false,
            'empty' => __("--Select--", true),
            "options" => $ProjectSubTypes,
            'onchange' => 'saveFieldYourForm(this)'
        ))
    ),

    'project_amr_sub_program_id' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Sub program', true),
        'html' => $this->Form->input('project_amr_sub_program_id', array('div' => false, 'label' => false,
            'style' => 'display: none',
            'empty' => __("--Select--", true),
            "options" => $ProjectArmSubPrograms,
            'onchange' => 'saveFieldYourForm(this)'
        ))
    ),
    'project_priority_id' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Priority', true),
        'html' => $this->Form->input('project_priority_id', array('div' => false, 'onchange' => 'saveFieldYourForm(this)', 'label' => false, "options" => $Priorities, 'empty' => __("--Select--", true)))
    ),
    'complexity_id' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Implementation Complexity', true),
        'html' => $this->Form->input('complexity_id', array('div' => false, 'label' => false, 'onchange' => 'saveFieldYourForm(this)', "options" => $Complexities, 'empty' => __("--Select--", true)))
    ),
    // 'created_value' => array(
    //     'label' => __d(sprintf($_domain, 'Details'), 'Created value', true),
    //     'html' => $this->Form->input('created_value', array('div' => false, 'label' => false,
    //         "class" => "placeholder", "placeholder" => __("Created value", true), "readonly" => 'readonly',
    //         'style' => 'background-color: rgb(223, 223, 223)',
    //         'onfocusout' => 'saveFieldYourForm(this)'
    //     ))
    // ),
    'project_status_id' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Status', true),
        'html' => $this->Form->input('project_status_id', array('div' => false, 'onchange' => 'saveFieldYourForm(this)', 'label' => false, "options" => $Statuses, 'empty' => __("--Select--", true))),
        'position' => 'top',
    ),
    'project_phase_id' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Current Phase', true),
        'html' => $cuPhaseTemplate
    ),
    'activity_id' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Link To RMS Activity', true),
        'html' => $this->Form->input('activity_id', array(
            'type'      => 'select',
            'div'       => false,
            'label'     => false,
            "options"   => $ProjectActivities,
            'empty'     => __("--Select--", true),
            'onchange' => 'saveFieldYourForm(this)'
        )) . $this->Form->input('tmp_activity_id', array(
            'type' => 'hidden',
            'value' => $this->data['Project']['activity_id']
        ))
    ),
    'issues' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Issues', true),
        'html' => $this->Form->input('issues', array('type' => 'textarea', 'onfocusout' => 'saveFieldYourForm(this)', 'div' => false, 'label' => false)),
        'class' => 'wd-input-text'
    ),
    'primary_objectives' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Primary Objectives', true),
        'html' => $this->Form->input('primary_objectives', array('type' => 'textarea', 'onfocusout' => 'saveFieldYourForm(this)', 'div' => false, 'label' => false)),
        'class' => 'wd-input-text'
    ),
    'project_objectives' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Project Objectives', true),
        'html' => $this->Form->input('project_objectives', array('type' => 'textarea', 'onfocusout' => 'saveFieldYourForm(this)', 'div' => false, 'label' => false)),
        'class' => 'wd-input-text'
    ),
    'constraint' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Constraint', true),
        'html' => $this->Form->input('constraint', array('type' => 'textarea', 'onfocusout' => 'saveFieldYourForm(this)', 'div' => false, 'label' => false)),
        'class' => 'wd-input-text'
    ),
    'remark' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Remark', true),
        'html' => $this->Form->input('remark', array('type' => 'textarea', 'onfocusout' => 'saveFieldYourForm(this)', 'div' => false, 'label' => false)),
        'class' => 'wd-input-text'
    ),
    'free_1' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Free 1', true),
        'html' => $this->Form->input('free_1', array('type' => 'textarea', 'onfocusout' => 'saveFieldYourForm(this)', 'class' => 'resizeOnFocus', 'div' => false, 'label' => false)),
        'class' => 'wd-input-text'
    ),
    'free_2' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Free 2', true),
        'html' => $this->Form->input('free_2', array('type' => 'textarea', 'onfocusout' => 'saveFieldYourForm(this)', 'class' => 'resizeOnFocus', 'div' => false, 'label' => false)),
        'class' => 'wd-input-text'
    ),
    'free_3' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Free 3', true),
        'html' => $this->Form->input('free_3', array('type' => 'textarea', 'onfocusout' => 'saveFieldYourForm(this)', 'class' => 'resizeOnFocus', 'div' => false, 'label' => false)),
        'class' => 'wd-input-text'
    ),
    'free_4' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Free 4', true),
        'html' => $this->Form->input('free_4', array('type' => 'textarea', 'onfocusout' => 'saveFieldYourForm(this)', 'class' => 'resizeOnFocus', 'div' => false, 'label' => false)),
        'class' => 'wd-input-text'
    ),
    'free_5' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Free 5', true),
        'html' => $this->Form->input('free_5', array('type' => 'textarea', 'onfocusout' => 'saveFieldYourForm(this)', 'class' => 'resizeOnFocus', 'div' => false, 'label' => false)),
        'class' => 'wd-input-text'
    ),
    'chief_business_id' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Chief Business', true),
        'html' => $chiefTemplate
    ),
    'technical_manager_id' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Technical manager', true),
        'html' => $techTemplate
    ),
    'uat_manager_id' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'UAT manager', true),
        'html' => $uatTemplate
    ),
    'functional_leader_id' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Functional leader', true),
        'html' => $leaderTemplate
    ),
    'read_access' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Read Access', true),
        'html' => $fieldReadAccess,
        'position' => 'top',
    ),
    'start_date' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Start Date', true),
        'html' => $startTemplate
    ),
    'end_date' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'End Date', true),
        'html' => $this->Form->input('end_date', array('div' => false,
            'label' => false,
            'style' => '',
            'value' =>  $str_utility->convertToVNDate(isset($_end_date) ? $_end_date : null),
            'type' => 'text',
            'class' => 'wd-date',
            'onfocusout' => 'saveFieldYourForm(this)',
        ))
    ),
    'budget_customer_id' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Customer', true),
        'html' => $this->Form->input('budget_customer_id', array('name' => 'data[Project][budget_customer_id]',
            'type' => 'select',
            'div' => false,
            'label' => false,
            "empty" => __("-- Select -- ", true),
            "options" => (array) @$budgetCustomers,
            'onchange' => 'saveFieldYourForm(this)',
        ))
    )
);

$bgc = 'background-color: #ff290a';
if($nextMilestoneByWeek > 0 && $nextMilestoneByWeek <= 3){
    $bgc = 'background-color: #F3960B';
} else if($nextMilestoneByWeek > 3){
    $bgc = 'background-color: #5DBF56';
}
$maps['next_milestone_in_day'] = array(
    'label' => __d(sprintf($_domain, 'Details'), 'Next milestone in day', true),
    'html' => $this->Form->input('next_milestone_in_day', array('div' => false, 'label' => false,
        "class" => "placeholder", "readonly" => 'readonly', "value" => $nextMilestoneByDay,
        'style' => $bgc
    ))
);
$maps['next_milestone_in_week'] = array(
    'label' => __d(sprintf($_domain, 'Details'), 'Next milestone in week', true),
    'html' => $this->Form->input('next_milestone_in_week', array('div' => false, 'label' => false,
        "class" => "placeholder", "readonly" => 'readonly', "value" => $nextMilestoneByWeek,
        'style' => $bgc
    ))
);
if($this->data['Project']['category'] == 1):
    $disabled = 'disabled';
    $style = 'background-color: rgb(218, 221, 226);';
    if(isset($employeeInfo['Role']['name']) && $employeeInfo['Role']['name'] === 'admin'){
        $disabled = '';
    }
    $option = array(__('No', true), __('Yes', true));
    $maps['activated'] = array(
        'label' => __d(sprintf($_domain, 'Details'), 'Timesheet Filling Activated', true),
        'html' => $this->Form->input('activated', array(
            'div' => false,
            'label' => false,
            'disabled' => $disabled,
            "options" => $option,
            'style' => $style,
            'onfocusout' => 'saveFieldYourForm(this)',
        ))
    );
endif;
$range = range(1, 20);
foreach($range as $num){
    if( $num <= 4 ){
        //bool 0/1
        $maps['bool_' . $num] = array(
            'label' => __d(sprintf($_domain, 'Details'), '0/1 ' . $num, true),
            'html' => $this->Form->input('bool_' . $num, array('type' => 'select', 'onchange' => 'saveFieldYourForm(this)', 'div' => false, 'label' => false, 'options' => array(0, 1)))
        );
    }
    if( $num <= 5 ){
        //text editor
        $maps['editor_' . $num] = array(
            'label' => __d(sprintf($_domain, 'Details'), 'Editor ' . $num, true),
            'html' => $this->Form->input('editor_' . $num, array('type' => 'textarea', 'class' => 'tinymce-editor', 'div' => array('class' => 'tinymce-container'), 'label' => false)),
            'class' => 'tiny-mce-field'
        );
        //date MM/YY
        $maps['date_mm_yy_' . $num] = array(
            'label' => __d(sprintf($_domain, 'Details'), 'Date(MM/YY) ' . $num, true),
            'html' => $this->Form->input('date_mm_yy_' . $num, array('type' => 'text', 'class' => 'wd-date-mm-yy', 'onchange' => 'saveFieldYourForm(this)', 'div' => false, 'label' => false, 'value' => $this->data['Project']['date_mm_yy_' . $num]))
        );
        //date YY
        $maps['date_yy_' . $num] = array(
            'label' => __d(sprintf($_domain, 'Details'), 'Date(YY) ' . $num, true),
            'html' => $this->Form->input('date_yy_' . $num, array('type' => 'text', 'class' => 'wd-date-yy', 'onchange' => 'saveFieldYourForm(this)', 'div' => false, 'label' => false, 'value' => $this->data['Project']['date_yy_' . $num]))
        );
        // upload documents
        $maps['upload_documents_' . $num] = array(
            'label' => __d(sprintf($_domain, 'Details'), 'Upload documents ' . $num, true),
            'html' => '<div id="uploaderDocument'.$num.'" class="wd-input wd-calendar" style=""><p>Your browser do not have Flash, Silverlight or HTML5 support.</p></div>'
        );
    }
    if($num <= 9){
        //yes/no
        $maps['yn_' . $num] = array(
            'label' => __d(sprintf($_domain, 'Details'), 'Yes/No ' . $num, true),
            'html' => $this->Form->input('yn_' . $num, array('type' => 'select', 'onchange' => 'saveFieldYourForm(this)', 'div' => false, 'label' => false, 'options' => array(__('No', true), __('Yes', true))))
        );
    }
    // list mutiple select.
    if($num <= 10){
        $num_class = 7 + $num;
        if(!empty($datasets['list_muti_' . $num])){
            $htmlListMultiple = '<div class="multiselect" onchange="saveFieldMutiSelectYourForm(this)" style="margin-right: 3px;">
                <a href="javascript:void();" class="wd-combobox-'.$num_class.'"></a>
                <div id="wd-data-project-'.$num_class.'" style="display: none;" class="list_multiselect listMulti_'.$num.'">';
            foreach($datasets['list_muti_' . $num] as $idPm => $namePm):
                $htmlListMultiple .= '<div class="listMulti wd-data-manager wd-group-' . $idPm . '">
                    <p class="listMulti wd-data" style="width: 200px; margin: 10px 5px;">' .
                        $this->Form->input('project_list_multi_'.$num, array(
                            'label' => false,
                            'div' => false,
                            'type' => 'checkbox',
                            'class' => 'listMulti',
                            'name' => 'data[project_list_multi_'.$num.'][]',
                            'value' => $idPm)) .
                        '<span class="listMulti" style="padding-left: 5px;">' . $namePm . '</span>
                    </p>
                </div>';
            endforeach;
            $htmlListMultiple .= '</div></div>';
            $maps['list_muti_' . $num] = array(
                'label' => __d(sprintf($_domain, 'Details'), 'List(multiselect) ' . $num, true),
                'html' => $htmlListMultiple
            );
        }
    }
    if( $num <= 14 ){
        //list
        if(!empty( $datasets['list_' . $num])){
            $maps['list_' . $num] = array(
                'label' => __d(sprintf($_domain, 'Details'), 'List ' . $num, true),
                'html' => $this->Form->input('list_' . $num, array('type' => 'select', 'onchange' => 'saveFieldYourForm(this)', 'div' => false, 'label' => false, 'options' => $datasets['list_' . $num], 'empty' => __("-- Select -- ", true)))
            );
        }
        //date
        $maps['date_' . $num] = array(
            'label' => __d(sprintf($_domain, 'Details'), 'Date ' . $num, true),
            'html' => $this->Form->input('date_' . $num, array('type' => 'text', 'class' => 'wd-date', 'div' => false, 'onchange' => 'saveFieldYourForm(this)', 'label' => false, 'value' => $str_utility->convertToVNDate($this->data['Project']['date_' . $num])))
        );
    }
    if( $num <= 16 ){
        //price
        $_class = 'numeric-value';
        if( $num > 6 ) {
            $_class .= ' not-decimal';
        }
        $maps['price_' . $num] = array(
            'label' => __d(sprintf($_domain, 'Details'), 'Price ' . $num, true),
            'html' => $this->Form->input('price_' . $num, array('div' => false, 'class' => $_class, 'onchange' => 'saveFieldYourForm(this)', 'label' => false, 'value' => number_format($this->data['Project']['price_' . $num], 2, '.', ' ') )) . ' <span style="position: absolute; right: 30px; font-size: 20px; color: #C6CCCF; bottom: 13px">'.$budget_settings.'</span>'
        );
    }
    if( $num <= 18 ){
        //number
        $_class = 'numeric-value';
        if( $num > 6 ) {
            $_class .= ' not-decimal';
        }
        $maps['number_' . $num] = array(
            'label' => __d(sprintf($_domain, 'Details'), 'Number ' . $num, true),
            'html' => $this->Form->input('number_' . $num, array('div' => false, 'class' => $_class, 'onchange' => 'saveFieldYourForm(this)', 'label' => false))
        );
    }
    //text one line
    $maps['text_one_line_' . $num] = array(
        'label' => __d(sprintf($_domain, 'Details'), 'Text one line ' . $num, true),
        'html' => $this->Form->input('text_one_line_' . $num, array('div' => false, 'label' => false, 'onfocusout' => 'saveFieldYourForm(this)', 'type' => 'text'))
    );
    //text two line
    $maps['text_two_line_' . $num] = array(
        'label' => __d(sprintf($_domain, 'Details'), 'Text two line ' . $num, true),
        'html' => $this->Form->input('text_two_line_' . $num, array('class' => 'textarea-limit', 'div' => false, 'onfocusout' => 'saveFieldYourForm(this)', 'label' => false, 'rows' => '2', 'style' => 'height:35px;'))
    );
}
//team
$PCModel = ClassRegistry::init('ProfitCenter');
$listTeam = $PCModel->generateTreeList(array('company_id' => $employee_info['Company']['id']),null,null,' -- ',-1);
$maps['team'] = array(
    'label' => __d(sprintf($_domain, 'Details'), 'Team', true),
    'html' => $this->Form->input('team', array('div' => false, 'label' => false, 'onchange' => 'saveFieldYourForm(this)', "options" => $listTeam, 'empty' => __("--Select--", true)))
);
?>
<div class="loading_w"><p></p></div>
<div id="slider" class="flexslider" style="display: none; border-bottom: 0; margin-left: 0;">
    <ul class="slides">
        <?php
        foreach ($images as $image) {
            if( $image['ProjectImage']['type'] != 'image' )
                continue;
            $url = $html->url('/project_images/show/' . $project_id . '/' . $image['ProjectImage']['id'] . '/a.jpg');
            $link = $html->url('/project_images/show/' . $project_id . '/' . $image['ProjectImage']['id'] . '/a.jpg');
            if( file_exists($uploadFolder . $company_id . '/' . $project_id . '/l_' . $image['ProjectImage']['file']) )
                $url = $html->url('/project_images/show/' . $project_id . '/' . $image['ProjectImage']['id'] . '/l_a.jpg');
        ?>
            <li>
                <a href="<?php echo $link ?>" class="fancy" rel="gallery1"><img src="<?php echo $url ?>" width="100%" alt=""></a>
            </li>
        <?php } ?>
    </ul>
</div>
<?php
$htmlPictures = '';
$htmlPictures .= '<div id="carousel" class="flexslider">';
$htmlPictures .= '<ul class="slides">';
    if(!empty($images)){
        foreach ($images as $image) {
            if( $image['ProjectImage']['type'] != 'image' )
                continue;
                $url = $html->url('/project_images/show/' . $project_id . '/' . $image['ProjectImage']['id'] . '/r_a.jpg');
                $_url = $html->url('/project_images/show/' . $project_id . '/' . $image['ProjectImage']['id'] . '/l_a.jpg');
$htmlPictures .= '<li>';
$htmlPictures .=  '<img style="height: 100px" src="' . $url . '" alt="" data-url=" '. $_url .' ">';
$htmlPictures .=  '</li>';
            }
        } else {
$htmlPictures .= '<p style="text-align: center; height: 100px; margin-top: 45px;">' .__("No image for display", true). '</p>';
        }
$htmlPictures .=  '</ul>';
$htmlPictures .=  '</div>';
$maps['pictures'] = array(
    'label' => __d(sprintf($_domain, 'Details'), 'Pictures', true),
    'html' => $htmlPictures
);
$start_layout = '<div class="wd-fields"><div class="wd-behavior"><i class="icon-arrow-up"></i></div><div class="wd-content-field">';
$end_layout = '</div></div>';
?>
                        <fieldset>
                            <div id='chart-wrapper' class="wd-scroll-form">
                                <?php

                                // layout top
                                // echo $start_layout; ?>
                                <div class="wd-fields"><div class="wd-behavior">
                                    <i class="icon-arrow-up"></i>
                                </div>
                                <?php
                                    $_h = '';
                                    if ($employeeInfo['Employee']['change_status_project'] = 1 || $pmCanChange){ }else{
                                        $_h = 'disabled';
                                    }
                                ?>
                                <select onchange="if(validateForm()){saveFieldSelectYourForm(this, 'category')}" <?php echo $_h ?> id="pseudo-category">
                                <?php
                                $cates = array(
                                    1 => __("In progress", true),
                                    2 => __("Opportunity", true),
                                    3 => __("Archived", true),
                                    4 => __("Model", true)
                                );
                                foreach($cates as $cat => $name){
                                    $selected = $this->data['Project']['category'] == $cat ? 'selected' : '';
                                    echo '<option value="'.$cat.'" '.$selected.'>'.$name.'</option>';
                                }
                                ?>
                                </select>
                                <div class="wd-content-field">
                                <div class="column-box column-1"><?php 
                                    $item = 0;
                                    foreach($translation_data_box_1 as $data){
                                        //ignore project details
                                        if( $data['Translation']['field'] == 'project_details')continue;
                                        $fieldName = $data['Translation']['field'];
                                        $class = isset($maps[$fieldName]['class']) ? $maps[$fieldName]['class'] : '';
                                        if($data['TranslationSetting']['show'] == 1 && !empty($maps[$fieldName]['html']) && $item < 3 ){ ?>
                                            <div class="wd-input wd-area wd-none <?php echo $class ?>">
                                                <label><?php echo !empty($maps[$fieldName]['label']) ? $maps[$fieldName]['label'] : ''; ?></label>
                                                <?php echo !empty($maps[$fieldName]['html']) ? $maps[$fieldName]['html'] : ''; ?>
                                            </div>
                                            <?php $item++;
                                        }
                                    } ?>
                                </div>
                                <div class="column-box column-2">
                                    <?php if(!empty($item) && !empty($translation_data_box_1[3])){
                                        $fieldName = $translation_data_box_1[3]['Translation']['field'];
                                        $class = isset($maps[$fieldName]['class']) ? $maps[$fieldName]['class'] : '';
                                        if($translation_data_box_1[3]['TranslationSetting']['show'] == 1 && !empty($maps[$fieldName]['html'])){ ?>
                                            <div class="wd-input wd-area wd-none <?php echo $class ?>">
                                                <label><?php echo !empty($maps[$fieldName]['label']) ? $maps[$fieldName]['label'] : ''; ?></label>
                                                <?php echo !empty($maps[$fieldName]['html']) ? $maps[$fieldName]['html'] : ''; ?>
                                            </div>
                                            <?php 
                                        }
                                    } ?>
                                    <div class="wd-dropzone">
                                        <div class="wd-input" style="width: auto; clear: none;">
                                           <!--  <?php
                                            echo $this->Form->input('attachment', array('div' => false, 'label' => false,
                                                'type' => 'file',
                                                'name' => 'FileField[attachment]',
                                                'style' => '',
                                                'onchange' => 'saveFieldUploadYourForm(this)',
                                            ));
                                            ?> -->
                                            <div class="trigger-upload">
                                            <?php
                                                $isDoc = false;
                                                $link = $this->Html->url(array('controller' => 'project_global_views', 'action' => 'attachment', $project_id, '?' => array('sid' => $api_key)), true);
                                                if (!empty($globals[$project_id]['global']) && $globals[$project_id]['file'] == false) {
                                                    if (!preg_match('/\.(jpg|jpeg|bmp|gif|png|swf)$/i', $globals[$project_id]['global']['ProjectGlobalView']['attachment'])) {
                                                        $link = 'https://docs.google.com/gview?url=' . ($link) . '&embedded=true';
                                                        $isDoc = true;
                                                    }
                                                } else {
                                                    $link = '';
                                                }
                                                if(!empty($link)){
                                                if(!$isDoc){ ?>
                                                    <div class="open-popup" data-id="<?php echo $project_id ?>"  style="width: 100%;<?php echo !empty($link) ? '' : 'border: 1px solid #ccc;' ?>">
                                                        <img class="img-responsive" src="<?php echo (!empty($link) ? $link : '') ?>" alt="">
                                                    </div>
                                                    <?php }else{ ?>
                                                    <div class="container">
                                                        <iframe src="<?php echo $link; ?>" class="img-responsive" style="width: 100%;height: 252px;"></iframe>
                                                    </div>
                                                    <?php }
                                                } else { ?>
                                                    <img title="burger"  src="<?php echo $html->url('/img/new-icon/drop-icon.png'); ?>"/>
                                                    <p>Drag & Drop votre image ou parcourir vos dossiers</p>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="column-box column-3">
                                    <?php if(!empty($translation_data_box_1[4])){
                                        $fieldName = $translation_data_box_1[4]['Translation']['field'];
                                        $class = isset($maps[$fieldName]['class']) ? $maps[$fieldName]['class'] : '';
                                        if($translation_data_box_1[4]['TranslationSetting']['show'] == 1 && !empty($maps[$fieldName]['html'])){ ?>
                                            <div class="wd-input wd-area wd-none <?php echo $class ?>">
                                                <label><?php echo !empty($maps[$fieldName]['label']) ? $maps[$fieldName]['label'] : ''; ?></label>
                                                <?php echo !empty($maps[$fieldName]['html']) ? $maps[$fieldName]['html'] : ''; ?>
                                            </div>
                                            <?php 
                                        }
                                    } ?>
                                    <div class='wd-map-area'>
                                       <!--  <div class="fullscreen"><i class="icon-frame"></i></div> -->
                                        <a class="fullscreen" href="javascript:;" onclick="expandScreen();"><i class="icon-frame"></i></a>
                                        <div class="map-heading">
                                            <h3><?php echo sprintf(__("%s", true), $projectName['Project']['project_name']); ?></h3>
                                            <a class="collapse" href="javascript:;" onclick="collapseScreen();"><i class="icon-size-actual"></i></a>
                                        </div>
                                        <iframe src="about:blank" style="height: 230px; display: none;" id="map-frame" ></iframe>
                                        <input type="text" id="coord-input" onfocusout="refreshMapYourForm(this)" name="data[Project][address]" size="40">
                                    </div>
                                </div>
                                '</div></div>'
                                <?php
                                // echo $end_layout;

                                //layout bottom
                                echo $start_layout;
                                    foreach($translation_data as $data){
                                        //ignore project details
                                        if( $data['Translation']['field'] == 'project_details')continue;
                                        $fieldName = $data['Translation']['field'];
                                        $class = isset($maps[$fieldName]['class']) ? $maps[$fieldName]['class'] : '';
                                        if(isset($maps[$fieldName]['position'])){ }else{
                                        if($data['TranslationSetting']['show'] == 1 && !empty($maps[$fieldName]['html'])){ ?>
                                            <div style = "width: <?php if(!empty($data['TranslationSetting']['next_line']) && $data['TranslationSetting']['next_line'] == 1) echo '100%'; ?>" class="wd-input wd-area wd-none <?php echo $class ?>">
                                                <label><?php echo !empty($maps[$fieldName]['label']) ? $maps[$fieldName]['label'] : ''; ?></label>
                                                <?php echo !empty($maps[$fieldName]['html']) ? $maps[$fieldName]['html'] : ''; ?>
                                            </div>
                                            <?php }
                                        }
                                    }
                                echo $end_layout;
                                ?>

                            </div>
                            <div class="wd-status-update">
                                    <?php
                                        if( !($time = $this->data['Project']['last_modified']) ){
                                            $time = $project_name['Project']['updated'];
                                        }
                                        $full_name = explode(' ', $project_name['Project']['update_by_employee']);
                                        $first_name = $full_name[0]; $last_name = $full_name[1];

                                        $first_name  = (!empty($first_name)) ? $first_name = substr( trim($first_name),  0, 1) : '';
                                        $last_name  = (!empty($last_name)) ? $last_name = substr( trim($last_name),  0, 1) : '';

                                        $theTime = $time ? date('H:i:s', $time) : '';
                                        $theDate = $time ? date('d/m/Y', $time) : '../../....';
                                       $byEmployee = !empty($project_name['Project']['update_by_employee']) ? $project_name['Project']['update_by_employee'] : 'N/A';
                                    echo '<div class="content-status">';
                                    echo str_replace(
                                            array(
                                                '{time}', '{date}', '{resource}'
                                            ),
                                            array(
                                                $theTime, $theDate, $byEmployee
                                            ),
                                            __d(sprintf($_domain, 'Details'), 'Last Update: {time} {date} by {resource}', true)
                                        );
                                    echo '</div>';
                                    echo '<span class="circle-name">' . $first_name .''.$last_name.'</span>';
                                    
                                    ?>
                            </div>
                        </fieldset>
                        </form>
                        <div id ='openPopup' class="wd-image-popup" style="display: none">
                            <a class="close-popup" href="javascript:;"><i class="icon-size-actual"></i></a>
                            <?php echo $this->Session->flash(); ?>
                            <div class="wd-section" id="wd-fragment-1">
                                <h2 class="wd-t2"><?php echo $projectName['Project']['project_name'] ?></h2>
                                <?php
                                echo $this->Form->create('ProjectGlobalView', array(
                                    'type' => 'file',
                                    'url' => array('controller' => 'projects_preview', 'action' => 'upload_global',
                                        $projectName['Project']['id'])));
                                ?>
                                <fieldset>
                                    <?php if ($projectGlobalView) : ?>
                                     <!-- If is_file = 1 then File else if is_file = 0 then Url -->
                                        <div id="download-place">
                                            <?php if($projectGlobalView['ProjectGlobalView']['is_file']){
                                            echo $this->Html->link('<i class="icon-cloud-download"></i>', array('controller' => 'project_global_views',
                                                        'action' => 'attachment', $projectName['Project']['id'], '?' => array('download' => true, 'sid' => $api_key)), array(
                                                'escape' => false,
                                                'id' => 'download-attachment'));
                                            }else{
                                                $is_http = $projectGlobalView['ProjectGlobalView']['is_https'] ? 'https://' : 'http://';
                                                $IFRAME = $is_http.$projectGlobalView['ProjectGlobalView']['attachment'] ;
                                                echo "<a href=".$is_http.$projectGlobalView['ProjectGlobalView']['attachment']." target='_blank'>".$this->Html->image('url.png') . __('URL ', true)."</a>";
                                            }
                                            if((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite)) {
                                                echo $this->Html->link('<i class="icon-close"></i>', 'javascript:void(0);', array(
                                                    'escape' => false,
                                                    'id' => 'replace-attachment'));
                                                echo $html->image('ajax-loader.gif', array(
                                                    'id' => 'loader',
                                                    'style' => 'display: none; margin-left: 3px'
                                                ));
                                            }
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                    <div id="upload-place" class="wd-global-submit" style="<?php echo 'display:' . ($projectGlobalView ? 'none' : 'block') ?>">
                                        <?php if((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite)) : ?>
                                             <div class="wd-input wd-normal">
                                                <?php
                                                    $options=array( 1 => __('File',true), 0 => __('URL',true));
                                                    $attributes=array('legend'=>false,'class'=>'r-right','div'=>true,'default'=>1);
                                                    echo $this->Form->radio('is_file',$options,$attributes);
                                                ?>
                                            </div>
                                            <div class="wd-input">
                                                <?php
                                                echo $this->Form->input('attachment', array('div' => false, 'label' => false,
                                                    'type' => 'file',
                                                    'name' => 'FileField[attachment]',
                                                    'style' => 'width: 200px'
                                                ));
                                                ?>
                                            </div>
                                            <div class="wd-input">
                                                <?php
                                                echo $this->Form->input('attachment', array('div' => false, 'label' => false,'id'=>'attachmentUrl',
                                                    'type' => 'text',
                                                    'style' => 'width: 200px; padding: 6px',
                                                    'placeholder' => 'Ex: www.example.com'
                                                ));
                                                ?>
                                            </div>
                                            <button type="submit" class="btn btn-save" id="btnSave">
                                                <span><?php __('Save') ?></span>
                                            </button>    
                                            <div id="file-types">
                                                <?php __('Allowed file types') ?>: <span><?php echo str_replace(',', ', ', $allowedFiles) ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </fieldset>
                                <?php echo $this->Form->end(); ?>
                                <br style="clear: both;" />
                            </div>
                            <br  />
                            <div class="wd-section" id="wd-fragment-2" style="overflow: auto">
                                <?php
                                $isDoc = false;
                                 $link = $this->Html->url(array('controller' => 'project_global_views', 'action' => 'attachment', $project_id, '?' => array('sid' => $api_key)), true);
                                if ($projectGlobalView && empty($noFileExists)) {
                                    if (!preg_match('/\.(jpg|jpeg|bmp|gif|png|swf)$/i', $projectGlobalView['ProjectGlobalView']['attachment'])) {
                                        $link = 'https://docs.google.com/gview?url=' . ($link) . '&embedded=true';
                                        $isDoc = true;
                                    }
                                } else {
                                    $link = '';
                                }
                                if(!isset($IFRAME)) {
                                    $IFRAME = $link;
                                } else {
                                    $is_link = 1;
                                    $IFRAME = $IFRAME;
                                    $IFRAME_NAME = $IFRAME;
                                    echo '<div id="local_link">' . __('Local view : ', true) .
                                    $this->Html->link(
                                        $IFRAME_NAME,$IFRAME,array('target' => '_blank')
                                    ) . '</div>';
                                }
                                $check = 1;
                                if(!empty($link)){
                                    $check = 2;
                                }
                                if(!$isDoc){ ?>
                                <img onclick="openImage(this)" src="<?php echo $link; ?>" style="max-width: 1800px; max-height: 950px; margin-left: auto; margin-right: auto; display: block;"></img>
                                <?php }else{ ?>
                                <iframe src="<?php echo $link; ?>" style="width: 100%;height: 900px;"></iframe>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $this->element('dialog_detail_value') ?>
<form method="post" action="/user_files/upload_editor_image" style="width: 0; height: 0; overflow: hidden" enctype="multipart/form-data" id="temp-upload-form">
    <input name="image" type="file" onchange="doUpload.call(this)">
</form>
<div class="wd-input wd-calendar" style="display: none" rels="<?php echo Configure::read('Config.language'); ?>" id="languageTranslationAudit">
</div>
<?php
echo $this->element('dialog_projects');
echo $validation->bind("Project");
$showAllFieldYourform = 0;
if(!$checkReadAccess){
    if(($canModified && !$_isProfile) || $_canWrite){
        $showAllFieldYourform = 1;
    } else if($pmCanChange){
        $showAllFieldYourform = $employeeInfo['Employee']['update_your_form'];
    }
   
}
$opportunity_to_in_progress_without_validation = !empty($companyConfigs['opportunity_to_in_progress_without_validation']) ? $companyConfigs['opportunity_to_in_progress_without_validation'] : 0;
// ob_clean(); debug($opportunity_to_in_progress_without_validation); exit;
echo $this->Form->create('Export', array('url' => array('controller' => 'projects', 'action' => 'export_pdf'), 'type' => 'file'));
echo $this->Form->hidden('canvas', array('id' => 'canvasData'));
echo $this->Form->hidden('height', array('id' => 'canvasHeight'));
echo $this->Form->hidden('width', array('id' => 'canvasWidth'));
echo $this->Form->end();
?>
<script type="text/javascript">


    var curretnCate = <?php echo json_encode($this->data['Project']['category']);?>;
    var role = <?php echo json_encode($employeeInfo['Role']['name']);?>;
    var activityId = <?php echo json_encode($this->data['Project']['activity_id']);?>;
    var haveActivity = <?php echo json_encode($haveActivity);?>;
    var changeProjectManager =<?php echo json_encode($changeProjectManager); ?>;
    var pmCanChange =<?php echo json_encode($pmCanChange); ?>;
    var showAllFieldYourform = <?php echo json_encode($showAllFieldYourform); ?>;
    var codeExisted = <?php echo json_encode(__('Already used in project', true));?>;
    var activityNameExited = <?php echo json_encode(__('An activity has already this name', true));?>;
    var project_id = <?php echo json_encode($project_name['Project']['id']) ?>;
    var change_status_project = <?php echo json_encode(!empty($employeeInfo['Employee']['change_status_project']) ? $employeeInfo['Employee']['change_status_project'] : 0); ?>;
    var projectFiles1 = <?php echo json_encode(!empty($projectFiles['upload_documents_1']) ? $projectFiles['upload_documents_1'] : array()) ?>;
    var projectFiles2 = <?php echo json_encode(!empty($projectFiles['upload_documents_2']) ? $projectFiles['upload_documents_2'] : array()) ?>;
    var projectFiles3 = <?php echo json_encode(!empty($projectFiles['upload_documents_3']) ? $projectFiles['upload_documents_3'] : array()) ?>;
    var projectFiles4 = <?php echo json_encode(!empty($projectFiles['upload_documents_4']) ? $projectFiles['upload_documents_4'] : array()) ?>;
    var projectFiles5 = <?php echo json_encode(!empty($projectFiles['upload_documents_5']) ? $projectFiles['upload_documents_5'] : array()) ?>;
    var $opportunity_to_in_progress_without_validation = <?php echo json_encode($opportunity_to_in_progress_without_validation) ?>;

    /* table .end */
    if(showAllFieldYourform == 0){
        $(".wd-input,.wd-map-area").find("input").prop('disabled', true);
        $(".wd-input").find("select").prop('disabled', true);
        $(".wd-input").find("textarea").prop('disabled', true);
        $(".mce-edit-area").prop('disabled', true);
        $(".wd-combobox-2").prop('disabled', true);
        $(".wd-combobox-3").prop('disabled', true);
        $(".wd-combobox-4").prop('disabled', true);
        $(".wd-combobox-5").prop('disabled', true);
        $(".wd-combobox-6").prop('disabled', true);
        $(".wd-combobox-7").prop('disabled', true);
        $(".wd-combobox-8").prop('disabled', true);
        $(".wd-combobox-9").prop('disabled', true);
        $(".wd-combobox-10").prop('disabled', true);
        $(".wd-combobox-11").prop('disabled', true);
        $(".wd-combobox-12").prop('disabled', true);
        $(".wd-combobox-13").prop('disabled', true);
        $(".wd-combobox-14").prop('disabled', true);
        $(".wd-combobox-15").prop('disabled', true);
        $(".wd-combobox-16").prop('disabled', true);
        $(".wd-combobox-17").prop('disabled', true);
        $('#pseudo-category').prop('disabled', true);
        
    }
    var createDialog = function(){
        $('#change_category, #save_activity_linked').dialog({
            position    :'center',
            autoOpen    : false,
            autoHeight  : true,
            modal       : true,
            width       : 600,
            open : function(e){
                var $dialog = $(e.target);
                $dialog.dialog({open: $.noop});
            }
        });
        createDialog = $.noop;
    }

    function doUpload(){
        var me = $(this);
        $('#temp-upload-form').ajaxSubmit({
            dataType: 'json',
            success: function(d){
                var url = d.location,
                    win = me.data('win');
                win.document.getElementById(me.data('name')).value = url;
            }
        });
        this.value = '';
    }

    $(".cancel").live('click',function(){
        $("#change_category").dialog('close');
    });
    $(".cancel_save_ac_linked").live('click',function(){
        $("#save_activity_linked").dialog('close');
    });
    // get sub family
    function listSubFamily(){
        //$('#ok_save_ac_linked').hide();
        var familyId = '';
        $('#ActivityLinkedFamily option').each(function(){
            if($(this).is(':selected')){
                familyId = $('#ActivityLinkedFamily').val();
            }
        });
        var subFam = $('#ProjectProjectAmrSubProgramId').val();
        var _url = '/projects/getSubFamily/' + familyId;
        if(subFam){
            _url = '/projects/getSubFamily/' + familyId + '/' + subFam;
        }
        if(familyId != ''){
            setTimeout(function(){
                $.ajax({
                    url: _url,
                    async: false,
                    beforeSend: function(){

                    },
                    success:function(datas) {
                        var datas = JSON.parse(datas);
                        $('#ActivityLinkedSubFamily').html(datas.select);
                        $('#ActivityLinkedSubFamily').val(datas.subFamId);
                        //$('#ActivityLinkedSubFamily').val(sub_familyId);
                        //$('#ok_save_ac_linked').show();
                    }
                });
            }, 100);
        }
    }
    //-------
    $(document).ready(function(){
        
        $('.wd-date').define_limit_date('#ProjectStartDate', '#ProjectEndDate');
        // $('#ProjectEndDate').define_limit_date(0);
        var edit = false;
        $("#onChangeCode").focus(function(){
            edit = false;
            $("#idButtonSave").hide();
        });
        $("#onChangeCode").keypress(function(){
            edit = true;
        });
        $("#onChangeCode").one('paste', function(){
            edit = true;
        });
        $("#onChangeCode").blur(function(){
            if(edit == false){
                $("#idButtonSave").show();
            }
        });
        $("#onChangeCode").on('change', function(){
            // goi len controll de kiem tra xem code 1 da ton tai
            if($('#onChangeCode').val() != ''){
                $('#valueOnChange').html("<img src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt='Loading' />");
                $("#valueOnChange").show();
                $.ajax({
                  url: '<?php echo $html->url(array('action' => 'checkCode1', $this->data['Project']['id'])); ?>',
                  type: 'POST',
                  data: {
                      data: {
                          code: $('#onChangeCode').val()
                      }
                  },
                  success: function(project_name) {
                     if( project_name ){
                        $("#idButtonSave").hide();
                        $('#valueOnChange').css('color', 'red');
                        $('#valueOnChange').html(codeExisted + ' ' + '<b>' + project_name + '</b>');
                     } else {
                        $("#valueOnChange").hide();
                        // $("#idButtonSave").show();
                        saveFieldChangeCodeYourForm($('#onChangeCode').val(), 'project_code_1')
                     }
                  }
               });
           } else {
                $("#idButtonSave").show();
                $("#valueOnChange").hide();
           }
        });
    });
    //---end---
    function validateForm(){
        if(!checkConsultant()) return false;

        var flag = true, flag1 = true;
        $("#flashMessage").hide();
        $('div.error-message').remove();
        $("div.wd-input input, select").removeClass("form-error");
        if (!(isDate('ProjectStartDate'))) {
            var endDate = $("#ProjectStartDate");
            endDate.addClass("form-error");
            var parentElem = endDate.parent();
            parentElem.addClass("error");
            parentElem.append('<div class="error-message">'+"<?php __("Invalid Date (Valid format is dd-mm-yyyy)") ?>"+'</div>');
            flag1 = flag = false;
        }
        if (!(isDate('ProjectEndDate'))) {
            var endDate = $("#ProjectEndDate");
            endDate.addClass("form-error");
            var parentElem = endDate.parent();
            parentElem.addClass("error");
            parentElem.append('<div class="error-message">'+"<?php __("Invalid Date (Valid format is dd-mm-yyyy)") ?>"+'</div>');
            flag1 = flag = false;
        }
        if($('.wd-combobox').html() == ''){
            var projectManage = $(".wd-combobox");
            projectManage.addClass("form-error");
            var parentElem = projectManage.parent();
            projectManage.addClass("error");
            parentElem.append('<div class="error-message" style="padding-left: 0px !important; margin-left: -1px;">'+"<?php __("The field is not blank.") ?>"+'</div>');
            flag1 = flag = false;
        }
        var isNotEmpty = true;
        if (flag) {
            if(isNotEmpty){
                if (compareDate('ProjectStartDate','ProjectEndDate') > 0 ) {
                    var endDate = $("#ProjectEndDate");
                    endDate.addClass("form-error");
                    var parentElem = endDate.parent();
                    parentElem.addClass("error");
                    parentElem.append('<div class="error-message">'+'<?php __("The end date must be greater than start date.") ?>'+'</div>');
                    flag1 = false;
                }
            }
            else return false;
        }
        var newCate = $('#pseudo-category').val();

        if(newCate == curretnCate){
            if(newCate == 1) curretnCate = 2;
            else if(newCate == 2) curretnCate = 1;
        }
        var program = $('#ProjectProjectAmrProgramId').val();
        var activate_family_linked_program = <?php echo json_encode($activate_family_linked_program);?>;
        // all ok -> change status

        if(flag1){
            // 2 - op , 1 - ip
            // ip -> op or op-> ip
            if((curretnCate == 1 && newCate == 2) || (curretnCate == 2 && newCate == 1)){
                if( (role === 'admin') || (role === 'pm' && change_status_project == 1) ){
                    var reFlag = false;
                    var projectName = $('#ProjectProjectName').val();
                    var projectLongName = $('#ProjectLongProjectName').val();
                    $('#ActivityLinkedName').val(projectName);
                    $('#ActivityLinkedNameDetail').val(projectLongName);
                    $('#ActivityLinkedShortName').val(projectName);
                    if(curretnCate == 1 && newCate == 2){ // chuyen In progress to Opportunity
                        if(haveActivity === 'true'){
                            $("#change_category p").html("<?php echo sprintf(__("The following employees have already worked in project", true)); ?>");
                            createDialog();
                            $("#change_category").dialog('option',{title:'Notice'}).dialog('open');
                            return false;
                        } else {
                            var category = $('#pseudo-category').val();
                            $.ajax({
                                type:'POST',
                                url: '<?php echo $html->url(array('action' => 'deleteActivityLinked', $this->data['Project']['id'])); ?>',
                                cache: false,
                                data:{
                                    category: category
                                },
                                success:function(data){
                                    $('#ProjectTmpActivityId').val(-1);
                                    // $('#ProjectEditForm').submit();
                                }
                            });
                        }
                        return false;
                    } else if(curretnCate == 2 && newCate == 1){ // chuyen Opportunity to In progress
                        
                        if(activate_family_linked_program){
                            if(program){ //lay famiy anh sub family
                                setTimeout(function(){
                                    $.ajax({
                                        type:'POST',
                                        url: '<?php echo $html->url(array('action' => 'getFamily')); ?>' + '/' + program,
                                        cache: false,
                                        success:function(data){
                                            var _famId = JSON.parse(data);
                                            $('#ActivityLinkedFamily').val(_famId);
                                            listSubFamily();
                                        }
                                    });

                                }, 200);
                                ///do nothing
                            } else{
                                alert("<?php echo sprintf(__("%s is mandatory to change the status", true), __d(sprintf($_domain, 'Details'), 'Program', true));?>");
                                return false;
                            }
                        }
                        // console.log(activityId);
                        // if(activityId){
                        //     // da co lien ket saved.
                        //     reFlag = true;
                        // } else {
                            if($opportunity_to_in_progress_without_validation != 1){
                                createDialog();
                                $("#save_activity_linked").dialog('option',{title:''}).dialog('open');
                                $('#ok_save_ac_linked').click(function(){
                                    var familyLinked = $('#ActivityLinkedFamily').val();
                                    if(familyLinked){
                                        ///do nothing
                                    } else{
                                        alert("<?php echo sprintf(__("%s is mandatory, Please select %s linked to a family", true), 'Family', __d(sprintf($_domain, 'Details'), 'Program', true));?>");
                                        return false;
                                    }
                                    var datas = {
                                        name: $('#ActivityLinkedName').val(),
                                        name_detail: $('#ActivityLinkedNameDetail').val(),
                                        short_name: $('#ActivityLinkedShortName').val(),
                                        family_id: $('#ActivityLinkedFamily').val(),
                                        sub_family_id: $('#ActivityLinkedSubFamily').val(),
                                        activated: $('#ActivityLinkedActivated').val(),
                                        category: $('#pseudo-category').val()
                                    };
                                    $.ajax({
                                        type:'POST',
                                        url: '<?php echo $html->url(array('action' => 'saveActivityLinked', $project_name['Project']['id'], $this->data['Company']['id'])); ?>',
                                        data:{
                                            data: datas
                                        },
                                        cache: false,
                                        beforeSend:function(){
                                            $("#save_activity_linked").dialog('close');
                                        },
                                        success:function(data){
                                            var id_acti = JSON.parse(data);
                                            $('#ProjectTmpActivityId').val(id_acti);
                                            $('#ProjectActivated').val($('#ActivityLinkedActivated').val());
                                            // $('#ProjectEditForm').submit();
                                        }
                                    });
                                });
                            } else {
                                var datas = {
                                    name: $('#ActivityLinkedName').val(),
                                    name_detail: $('#ActivityLinkedNameDetail').val(),
                                    short_name: $('#ActivityLinkedShortName').val(),
                                    family_id: $('#ActivityLinkedFamily').val(),
                                    sub_family_id: $('#ActivityLinkedSubFamily').val(),
                                    activated: $('#ActivityLinkedActivated').val(),
                                    category: $('#pseudo-category').val()
                                };
                                $.ajax({
                                    type:'POST',
                                    url: '<?php echo $html->url(array('action' => 'saveActivityLinked', $project_name['Project']['id'], $this->data['Company']['id'])); ?>',
                                    data:{
                                        data: datas
                                    },
                                    cache: false,
                                    beforeSend:function(){
                                        $("#save_activity_linked").dialog('close');
                                    },
                                    success:function(data){
                                        var id_acti = JSON.parse(data);
                                        $('#ProjectTmpActivityId').val(id_acti);
                                        $('#ProjectActivated').val($('#ActivityLinkedActivated').val());
                                        // $('#ProjectEditForm').submit();
                                    }
                                });
                            }
                        // }
                    }
                    return reFlag;
                } else {
                    if(curretnCate == 1 && newCate == 2){
                        $("#change_category p").html('<?php echo __('You can not change the status "In progress" to "Opportunity"', true);?>');

                    } else if(curretnCate == 2 && newCate == 1){
                        $("#change_category p").html('<?php echo __('You can not change the status "Opportunity" to "In progress"', true);?>');
                    }
                    createDialog();
                    $("#change_category").dialog('option',{title:'Notice'}).dialog('open');
                    return false;
                }
            }
        }
        return flag1;
    }
    function isNotEmpty1(elementId){
        var date = $("#"+elementId).val();
        if(date==""){
            var endDate = $("#"+elementId);
            var parentElem = endDate.parent();
            parentElem.addClass("error");
            endDate.addClass("form-error");
            parentElem.append('<div class="error-message"><?php __('This field is not blank.') ?></div>');
            return false;
        }
        return true;
    }
    function compareDate_(startDateId, endDateId){
        var defaultMessage = 'The end date must be greater than start date';
        var tmp = compareDate(startDateId,endDateId);
        if(tmp==1){
            var endDate = $("#"+endDateId);
            var parentElem = endDate.parent();
            endDate.addClass("form-error");
            parentElem.addClass("error");
            parentElem.append('<div class="error-message">'+"<?php __("The end date must be greater than start date") ?>"+'</div>');
            return false;
        }
        else {
            return true;
        }
    }
    checkConsultant();
    function checkConsultant(){
    <?php
    $employee_info = $this->Session->read('Auth.employee_info');
    if(isset($employeeInfo['Role']['name']) && $employeeInfo['Role']['name'] === 'admin'){ ?>
        return true;
    <?php }
    else if (($employee_info["Employee"]['is_sas'] == 0 && $employee_info["Role"]["name"] == "conslt") || (isset($profileName['ProfileProjectManager']['can_change_status_project']) && (empty($profileName['ProfileProjectManager']['can_change_status_project']) || $profileName['ProfileProjectManager']['can_change_status_project'] == 0))) {
        ?>
        $(".wd-submit").hide();
        return false;
        <?php
    }else
        echo "return true";
    ?>
    }

    $(document).ready(function(){
        // tinymce editor
        var readonly = 0;
        if(showAllFieldYourform == 0){
            readonly = 1;
        }
        tinymce.init({
            selector: '.tinymce-editor',
            autoresize_min_height: 150,
            autoresize_bottom_margin: 0,
            readonly : readonly,
            // height: 300,
            plugins: [
                'advlist autolink lists link image charmap anchor fullscreen table contextmenu wordcount textcolor colorpicker emoticons imagetools spellchecker fullscreen paste autoresize painter'
            ],
            menubar: false,
            toolbar: 'bold italic blockquote forecolor styleselect | removeformat | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image painter emoticons | fullscreen',
            content_css: [
                // '//fast.fonts.net/cssapi/e6dc9b99-64fe-4292-ad98-6974f93cd2a2.css',
                // '//www.tinymce.com/css/codepen.min.css'
                '/css/editor.css'
            ],
            skin: 'z0',
            image_advtab: true,
            language: Azuree.language,
            // use absolute url for image
            relative_urls : false,
            remove_script_host : true,
            convert_urls : true,

            entity_encoding: 'raw',
            entities : '160,nbsp,162,cent,8364,euro,163,pound',
            // inline: true,
            // readonly: true,
            // setup: function(ed) {
            // 	ed.on('show', function(e){
            // 		var content = $('#ticket-content').html();
            // 		ed.setContent(content);
            // 		ed.save();
            // 	});
            // },
            setup: function(ed) {
                ed.on('focusout', function(e){
                    var field = $(ed.getBody()).attr('data-id');
                    var content = ed.getContent();
                    $.ajax({
                        url: '<?php echo $html->url(array('action' => 'saveFieldYourFormEditer', $this->data['Project']['id'])); ?>',
                        type: 'POST',
                        data: {
                            field : field,
                            value : content
                        },
                    });
                });
            },
            image_caption: true,
            paste_data_images: true,
            automatic_uploads: true,
            images_upload_url: '/user_files/upload_editor_image',

            file_browser_callback: function(field_name, url, type, win) {
                if(type == 'image'){
                    $('#temp-upload-form input').data({
                        name: field_name,
                        win: win
                    }).click();
                }
            }
        });
        $('.wd-date').datepicker({
            dateFormat: 'yy-mm-dd'
        });
        $('.wd-date-mm-yy').datepicker({
            dateFormat: 'mm-yy'
        });
        $('.wd-date-yy').datepicker({
            dateFormat: 'yy'
        });
        $('.numeric-value').keypress(function(e){
            var key = e.keyCode ? e.keyCode : e.which;
            if(!key || key == 8 || key == 13){
                return;
            }
            var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
            if(val == '0' || !/^[\-]?([0-9]{0,8})(\.[0-9]{0,2})?$/.test(val) ){
                e.preventDefault();
                return false;
            }
        });
        $('#pseudo-category').change(function(){
            $('#ProjectCategory').val($(this).val());
        });
        var height;
        $('#ProjectIssues,#ProjectPrimaryObjectives,#ProjectProjectObjectives,#ProjectConstraint,#ProjectRemark, .resizeOnFocus').focus(function(){
            $(this).tooltip('disable');
            height = $(this).height();
            $(this).stop().animate({height : '150'} , 300);
        }).mouseup(function(){
            $(this).tooltip('close');
        }).blur(function(){
            $(this).tooltip('option' , 'content' , $(this).val());
            $(this).tooltip('enable');
            $(this).stop().animate({height : height}, 300 , function(){
                $(this).css({height : ''});
            });
        }).tooltip({maxWidth : 1000, maxHeight : 300,content: function(target){
                return $(target).val();
            }});
        // tooltip checkbox
         $('#ProjectIsStaffing').focus(function(){
            $(this).tooltip('option' , 'content' , '<?php echo __('Take into account this project in total workload',true);?>');
            $(this).tooltip('enable');
        }).mouseup(function(){
            $(this).tooltip('close');
        }).blur(function(){
            $(this).tooltip('option' , 'content' , '<?php echo __('Take into account this project in total workload',true);?>');
            $(this).tooltip('enable');
        }).tooltip({maxWidth : 1000, maxHeight : 300,content: function(target){
                return '<?php echo __('Take into account this project in total workload',true);?>';
        }}).click(function(){
            var val = $(this).prop('checked') ? 1 : 0;
            $('#project-is-staffing').val(val);
        });
        $('#ProjectProjectName').maxlength({
            events: [], // Array of events to be triggerd
            maxCharacters: <?php echo $projectNameLength ?>, // Characters limit
            status: false, // True to show status indicator bewlow the element
            statusClass: "status", // The class on the status div
            statusText: "character left", // The status text
            notificationClass: "notification",	// Will be added when maxlength is reached
            showAlert: false, // True to show a regular alert message
            alertText: "You have typed too many characters.", // Text in alert message
            slider: false // True Use counter slider
        });


        $("#ProjectCreatedValue").live('blur',function(){
            var number = $(this).val();
            var number = $.formatNumber(number, {format:"#", locale:"us"});
            $(this).val(number);
        });
        $(".wd-table table").dataTable();
        $('#ProjectBudget').keypress(function(){
            var rule = /^([0-9]*)$/;
            var x=$('#ProjectBudget').val();
            $('div.error-message').remove();
            if(!rule.test(x)){
                var fomrerror = $("#ProjectBudget");
                fomrerror.addClass("form-error");
                var parentElem = fomrerror.parent();
                parentElem.addClass("error");
                parentElem.append('<div class="error-message">'+"<?php __("The Budget must be a number ") ?>"+'</div>');
            }
            else{
                var fomrerror = $("#ProjectBudget");
                fomrerror.removeClass("form-error");
                $('div.error-message').remove();
            }
        });

        $("#ProjectProjectTypeId").change(function(){
            var project_type_id = $(this).val();
            $.ajax({
                url:  '<?php echo $html->url('/projects/get_project_sub_type/') ?>' + project_type_id,
                beforeSend: function() { $("#ProjectProjectSubTypeId").html("<option value=''>Loading...</option>"); },
                success: function(data) {
                    $("#ProjectProjectSubTypeId").html(data);
                    $("#ProjectProjectSubTypeId").removeClass("wd-disable");
                }
            });
        });

        $("#ProjectProjectAmrProgramId").change(function(){
            var program_id = $(this).val();
            $.ajax({
                url:  '<?php echo $html->url('/project_amrs/get_sub_program/') ?>' + program_id,
                beforeSend: function() { $("#ProjectProjectAmrSubProgramId").html("<option>Loading...</option>"); },
                success: function(data) {
                    $("#ProjectProjectAmrSubProgramId").html(data);
                    $("#ProjectProjectAmrSubProgramId").removeClass("wd-disable");
                }
            });
        });
        // $('#ProjectAttachment').live('change', function(){
        //     console.log(1111);  
        //     var form = $("#form_dialog_attachement_or_url");
        //     var _id = $('.active.selected').find('div').attr('data-id');
        //     form.find('input[name="data[Upload][id]"]').val(_id);
        //     form.submit();
        // });
        // disable combobox ProjectActivityId
        var  activityRequest = <?php echo $activityRequest ?>;
        if(activityRequest != 0 || activityId != null){
            $('#ProjectActivityId').attr('disabled', 'disabled');
        }
        // disabled activity da dc chon
        var  listIdActivity = <?php echo json_encode($listIdActivity); ?>;
        var project_id = <?php echo $project_name['Project']['id'] ?>;
        $('#ProjectActivityId').change(function(){
           $('#ProjectTmpActivityId').val($(this).val());
        });
        $.each(listIdActivity, function(key, val) {
            $('#ProjectActivityId option').each(function(){
                var valueOption = $(this).attr('value');
                if(valueOption != 0){
                    if(val == valueOption){

                        if(project_id == parseInt(key)){
                            //
                        }else{
                            // disable the option
                            $(this).attr('disabled', 'disabled');
                        }
                    }
                }
            });
        });

         /**
         * Function Filter
         */
        var projectEmployeeManager = $('#wd-data-project').find('.wd-data-manager'),
            chiefBusiness = $('#wd-data-project-2').find('.wd-data-manager'),
            technicalManager = $('#wd-data-project-3').find('.wd-data-manager'),
            functionalLeader = $('#wd-data-project-4').find('.wd-data-manager'),
            uatManager = $('#wd-data-project-5').find('.wd-data-manager'),
            readAccess = $('#wd-data-project-6').find('.wd-data-manager'),
            currentPhase = $('#wd-data-project-7').find('.wd-data-manager'),
            
            chiefBusinessDatas = <?php echo !empty($listEmployeeManagers['CB']) ? json_encode($listEmployeeManagers['CB']) : json_encode(array());?>,
            technicalManagerDatas = <?php echo !empty($listEmployeeManagers['TM']) ? json_encode($listEmployeeManagers['TM']) : json_encode(array());?>,
            functionalLeaderDatas = <?php echo !empty($listEmployeeManagers['FL']) ? json_encode($listEmployeeManagers['FL']) : json_encode(array());?>,
            uatManagerDatas = <?php echo !empty($listEmployeeManagers['UM']) ? json_encode($listEmployeeManagers['UM']) : json_encode(array());?>,
            readAccessDatas = <?php echo !empty($listEmployeeManagers['RA']) ? json_encode($listEmployeeManagers['RA']) : json_encode(array());?>,
            currentPhaseDatas = <?php echo !empty($phasePlans) ? json_encode($phasePlans) : json_encode(array());?>,
            ProjectMultiLists = <?php echo !empty($ProjectMultiLists) ? json_encode($ProjectMultiLists) : json_encode(array());?>,
            phaseHaveTasks = <?php echo !empty($phaseHaveTasks) ? json_encode($phaseHaveTasks) : json_encode(array());?>;

        var initMenuFilter = function($menu, $check){
            if($check === 'CB') {
                var $filter = $('<div class="context-menu-filter-2"><span><input type="text" rel="no-history"></span></div>');
            } else if($check === 'TM') {
                var $filter = $('<div class="context-menu-filter-3"><span><input type="text" rel="no-history"></span></div>');
            } else if($check === 'FL') {
                var $filter = $('<div class="context-menu-filter-4"><span><input type="text" rel="no-history"></span></div>');
            } else if($check === 'UM'){
                var $filter = $('<div class="context-menu-filter-5"><span><input type="text" rel="no-history"></span></div>');
            } else if($check === 'RA'){
                var $filter = $('<div class="context-menu-filter-6"><span><input type="text" rel="no-history"></span></div>');
            } else if($check === 'CR') {
                var $filter = $('<div class="context-menu-filter-7"><span><input type="text" rel="no-history"></span></div>');
            } else if($check === 'ML1'){
                var $filter = $('<div class="context-menu-filter-8"><span><input type="text" rel="no-history"></span></div>');
            } else if($check === 'ML2'){
                var $filter = $('<div class="context-menu-filter-9"><span><input type="text" rel="no-history"></span></div>');
            } else if($check === 'ML3'){
                var $filter = $('<div class="context-menu-filter-10"><span><input type="text" rel="no-history"></span></div>');
            } else if($check === 'ML4'){
                var $filter = $('<div class="context-menu-filter-11"><span><input type="text" rel="no-history"></span></div>');
            } else if($check === 'ML5'){
                var $filter = $('<div class="context-menu-filter-12"><span><input type="text" rel="no-history"></span></div>');
            } else if($check === 'ML6'){
                var $filter = $('<div class="context-menu-filter-13"><span><input type="text" rel="no-history"></span></div>');
            } else if($check === 'ML7'){
                var $filter = $('<div class="context-menu-filter-14"><span><input type="text" rel="no-history"></span></div>');
            } else if($check === 'ML8'){
                var $filter = $('<div class="context-menu-filter-15"><span><input type="text" rel="no-history"></span></div>');
            } else if($check === 'ML9'){
                var $filter = $('<div class="context-menu-filter-16"><span><input type="text" rel="no-history"></span></div>');
            } else {
                var $filter = $('<div class="context-menu-filter-17"><span><input type="text" rel="no-history"></span></div>');
            }
            $menu.before($filter);

            var timeoutID = null, searchHandler = function(){
                var val = $(this).val();
                var te = $($menu).find('.wd-data-manager .wd-data span').html();

                $($menu).find('.wd-data-manager .wd-data span').each(function(){
                    var $label = $(this).html();
                    $label = $label.toLowerCase();
                    val = val.toLowerCase();
                    if(!val.length || $label.indexOf(val) != -1 || !val){
                        $(this).parent().css('display', 'block');
                        $(this).parent().next().css('display', 'block');
                    } else{
                        $(this).parent().css('display', 'none');
                        $(this).parent().next().css('display', 'none');
                    }
                });
            };

            $filter.find('input').click(function(e){
                e.stopImmediatePropagation();
            }).keyup(function(){
                var self = this;
                clearTimeout(timeoutID);
                timeoutID = setTimeout(function(){
                    searchHandler.call(self);
                } , 200);
            });

        };
        initMenuFilter($('#wd-data-project-2'), 'CB');
        initMenuFilter($('#wd-data-project-3'), 'TM');
        initMenuFilter($('#wd-data-project-4'), 'FL');
        initMenuFilter($('#wd-data-project-5'), 'UM');
        initMenuFilter($('#wd-data-project-6'), 'RA');
        initMenuFilter($('#wd-data-project-7'), 'CR');
        initMenuFilter($('#wd-data-project-8'), 'ML1');
        initMenuFilter($('#wd-data-project-9'), 'ML2');
        initMenuFilter($('#wd-data-project-10'), 'ML3');
        initMenuFilter($('#wd-data-project-11'), 'ML4');
        initMenuFilter($('#wd-data-project-12'), 'ML5');
        initMenuFilter($('#wd-data-project-13'), 'ML6');
        initMenuFilter($('#wd-data-project-14'), 'ML7');
        initMenuFilter($('#wd-data-project-15'), 'ML8');
        initMenuFilter($('#wd-data-project-16'), 'ML9');
        initMenuFilter($('#wd-data-project-17'), 'ML10');

        // $('.wd-combobox').on('click', function(e){
        //     if(showAllFieldYourform == 0) return;
        //     e.preventDefault();
        //     $(this).closest('.wd-multiselect').find('.wd-combobox-content').toggle();
        // });
        // $('body').on('click', function(e){
        //     if(!( $(e.target).hasClass('wd-combobox-content') || $('.wd-combobox-content').find(e.target).length) && !($(e.target).hasClass('wd-combobox') || $('.wd-combobox').find(e.target).length) ){
        //         $('.wd-combobox-content').hide();
        //     }
        // });

        $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
        //$('.context-menu-filter').css('display', 'none');

        $('.wd-combobox-2').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7, .wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if(showAllFieldYourform != 0){
                if(checked){
                    $('#wd-data-project-2').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-2').css('display', 'none');
                } else {
                    $('#wd-data-project-2').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-2').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '91%',
                        'z-index': 2
                    });
                    $('#wd-data-project-2 div:first-child').css('padding-top', '20px');
                }
            }
            return false;
        });
        $('.wd-combobox-3').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project-2, #wd-data-project, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox-2, .wd-combobox, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7, .wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if(showAllFieldYourform != 0){
                if(checked){
                    $('#wd-data-project-3').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-3').css('display', 'none');
                } else {
                    $('#wd-data-project-3').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-3').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '91%',
                        'z-index': 2
                    });
                    $('#wd-data-project-3 div:first-child').css('padding-top', '20px');
                }
            }
            return false;
        });
        $('.wd-combobox-4').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project-2, #wd-data-project, #wd-data-project-3, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox-2, .wd-combobox, .wd-combobox-3, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7, .wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if(showAllFieldYourform != 0){
                if(checked){
                    $('#wd-data-project-4').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-4').css('display', 'none');
                } else {
                    $('#wd-data-project-4').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-4').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '91%',
                        'z-index': 2
                    });
                    $('#wd-data-project-4 div:first-child').css('padding-top', '20px');
                }
            }
            return false;
        });
        $('.wd-combobox-5').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project-2, #wd-data-project, #wd-data-project-3, #wd-data-project-4,  #wd-data-project-6,  #wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox-2, .wd-combobox, .wd-combobox-3, .wd-combobox-4, .wd-combobox-6, .wd-combobox-7, .wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if(showAllFieldYourform != 0){
                if(checked){
                    $('#wd-data-project-5').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-5').css('display', 'none');
                } else {
                    $('#wd-data-project-5').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-5').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '91%',
                        'z-index': 2
                    });
                    $('#wd-data-project-5 div:first-child').css('padding-top', '20px');
                }
            }
            return false;
        });
        $('.wd-combobox-6').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-7, .wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if(showAllFieldYourform != 0){
                if(checked){
                    $('#wd-data-project-6').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-6').css('display', 'none');
                } else {
                    $('#wd-data-project-6').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-6').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '91%',
                        'z-index': 2
                    });
                    $('#wd-data-project-6 div:first-child').css('padding-top', '20px');
                }
            }
            return false;
        });
        $('.wd-combobox-7').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-8, #wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if(showAllFieldYourform != 0){
                if(checked){
                    $('#wd-data-project-7').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-7').css('display', 'none');
                } else {
                    $('#wd-data-project-7').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-7').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '91%',
                        'z-index': 2
                    });
                    $('#wd-data-project-7 div:first-child').css('padding-top', '20px');
                }
            }
            return false;
        });
        $('.wd-combobox-8').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-9, #wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if(showAllFieldYourform != 0){
                if(checked){
                    $('#wd-data-project-8').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-8').css('display', 'none');
                } else {
                    $('#wd-data-project-8').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-8').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '91%',
                        'z-index': 2
                    });
                    $('#wd-data-project-8 div:first-child').css('padding-top', '20px');
                }
            }
            return false;
        });
        $('.wd-combobox-9').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-8, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7,.wd-combobox-8, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if(showAllFieldYourform != 0){
                if(checked){
                    $('#wd-data-project-9').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-9').css('display', 'none');
                } else {
                    $('#wd-data-project-9').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-9').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '91%',
                        'z-index': 2
                    });
                    $('#wd-data-project-9 div:first-child').css('padding-top', '20px');
                }
            }
            return false;
        });
        $('.wd-combobox-10').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7,.wd-combobox-8, .wd-combobox-9, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if(showAllFieldYourform != 0){
                if(checked){
                    $('#wd-data-project-10').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-10').css('display', 'none');
                } else {
                    $('#wd-data-project-10').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-10').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '91%',
                        'z-index': 2
                    });
                    $('#wd-data-project-10 div:first-child').css('padding-top', '20px');
                }
            }
            return false;
        });
        $('.wd-combobox-11').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7,.wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if(showAllFieldYourform != 0){
                if(checked){
                    $('#wd-data-project-11').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-11').css('display', 'none');
                } else {
                    $('#wd-data-project-11').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-11').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '91%',
                        'z-index': 2
                    });
                    $('#wd-data-project-11 div:first-child').css('padding-top', '20px');
                }
            }
            return false;
        });
        $('.wd-combobox-12').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7,.wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if(showAllFieldYourform != 0){
                if(checked){
                    $('#wd-data-project-12').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-12').css('display', 'none');
                } else {
                    $('#wd-data-project-12').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-12').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '91%',
                        'z-index': 2
                    });
                    $('#wd-data-project-12 div:first-child').css('padding-top', '20px');
                }
            }
            return false;
        });
        $('.wd-combobox-13').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7,.wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if(showAllFieldYourform != 0){
                if(checked){
                    $('#wd-data-project-13').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-13').css('display', 'none');
                } else {
                    $('#wd-data-project-13').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-13').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '91%',
                        'z-index': 2
                    });
                    $('#wd-data-project-13 div:first-child').css('padding-top', '20px');
                }
            }
            return false;
        });
        $('.wd-combobox-14').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7,.wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if(showAllFieldYourform != 0){
                if(checked){
                    $('#wd-data-project-14').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-14').css('display', 'none');
                } else {
                    $('#wd-data-project-14').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-14').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '91%',
                        'z-index': 2
                    });
                    $('#wd-data-project-14 div:first-child').css('padding-top', '20px');
                }
            }
            return false;
        });
        $('.wd-combobox-15').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7,.wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if(showAllFieldYourform != 0){
                if(checked){
                    $('#wd-data-project-15').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-15').css('display', 'none');
                } else {
                    $('#wd-data-project-15').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-15').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '91%',
                        'z-index': 2
                    });
                    $('#wd-data-project-15 div:first-child').css('padding-top', '20px');
                }
            }
            return false;
        });
        $('.wd-combobox-16').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7,.wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-17').removeAttr('checked');
            if(showAllFieldYourform != 0){
                if(checked){
                    $('#wd-data-project-16').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-16').css('display', 'none');
                } else {
                    $('#wd-data-project-16').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-16').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '91%',
                        'z-index': 2
                    });
                    $('#wd-data-project-16 div:first-child').css('padding-top', '20px');
                }
            }
            return false;
        });
        $('.wd-combobox-17').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7,.wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16').removeAttr('checked');
            if(showAllFieldYourform != 0){
                if(checked){
                    $('#wd-data-project-17').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-17').css('display', 'none');
                } else {
                    $('#wd-data-project-17').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-17').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '91%',
                        'z-index': 2
                    });
                    $('#wd-data-project-17 div:first-child').css('padding-top', '20px');
                }
            }
            return false;
        });
        $('html').click(function(e){
            if($(e.target).attr('class') &&
            (
                ( $(e.target).attr('class').split(' ')[0] &&
                    (
                        $(e.target).attr('class').split(' ')[0] == 'projectManager' || $(e.target).attr('class').split(' ')[0] == 'chiefBusiness' || $(e.target).attr('class').split(' ')[0] == 'technicalManager' || $(e.target).attr('class').split(' ')[0] == 'functionalLeader' || $(e.target).attr('class').split(' ')[0] == 'uatManager' || $(e.target).attr('class').split(' ')[0] == 'readAccess' || $(e.target).attr('class').split(' ')[0] == 'currentPhase'
                        || $(e.target).attr('class').split(' ')[0] == 'listMulti' || $(e.target).attr('class').split(' ')[0] == 'listMulti_1' || $(e.target).attr('class').split(' ')[0] == 'listMulti_2' || $(e.target).attr('class').split(' ')[0] == 'listMulti_3' || $(e.target).attr('class').split(' ')[0] == 'listMulti_4' || $(e.target).attr('class').split(' ')[0] == 'listMulti_5'
                        || $(e.target).attr('class').split(' ')[0] == 'listMulti_6' || $(e.target).attr('class').split(' ')[0] == 'listMulti_7' || $(e.target).attr('class').split(' ')[0] == 'listMulti_8' || $(e.target).attr('class').split(' ')[0] == 'listMulti_9' || $(e.target).attr('class').split(' ')[0] == 'listMulti_10'
                    )
                ) ||
            $(e.target).attr('class') == 'context-menu-filter-2' ||
            $(e.target).attr('class') == 'context-menu-filter-3' ||
            $(e.target).attr('class') == 'context-menu-filter-4' ||
            $(e.target).attr('class') == 'context-menu-filter-5' ||
            $(e.target).attr('class') == 'context-menu-filter-6' ||
            $(e.target).attr('class') == 'context-menu-filter-7' ||
            $(e.target).attr('class') == 'context-menu-filter-8' ||
            $(e.target).attr('class') == 'context-menu-filter-9' ||
            $(e.target).attr('class') == 'context-menu-filter-10' ||
            $(e.target).attr('class') == 'context-menu-filter-11' ||
            $(e.target).attr('class') == 'context-menu-filter-12' ||
            $(e.target).attr('class') == 'context-menu-filter-13' ||
            $(e.target).attr('class') == 'context-menu-filter-14' ||
            $(e.target).attr('class') == 'context-menu-filter-15' ||
            $(e.target).attr('class') == 'context-menu-filter-16' ||
            $(e.target).attr('class') == 'context-menu-filter-17'
            )){
                //do nothing
            } else {
                $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
                $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-8, #wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
                $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7, .wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            }
        });
        /**
         * Remove element to array
         */
        jQuery.removeFromArray = function(value, arr) {
            return jQuery.grep(arr, function(elem, index) {
                return elem !== value;
            });
        };

        /**
         * Phan chon cac phan tu trong combobox cua chiefBusiness
         */
        var $ids_2 = [];
        chiefBusiness.each(function(){
            var data = $(this).find('.wd-data');
            var backup = $(this).find('.wd-backup');
            /**
             * When load data
             */
            var valList = $(data).find('#ProjectChiefBusinessList').val();
            var valListBackup = $(backup).find('#ProjectIsBackupChief').val();
            if(chiefBusinessDatas){
                $.each(chiefBusinessDatas, function(employId, isBackup){
                    isBackup = (isBackup == 1) ? employId : 0;
                    if(valList == employId){
                        $(data).find('#ProjectChiefBusinessList').attr('checked', 'checked');
                        $(backup).find('#ProjectIsBackupChief').removeAttr('disabled');
                        $('a.wd-combobox-2').append('<span class="wd-dt-'+valList+'">' + $('.wd-group-' + valList).find('span').html() + '<span class="wd-bk-'+valList+'"></span></span><span class="wd-em-'+valList+'">, </span>');
                    }
                    if(valListBackup == isBackup){
                        $(backup).find('#ProjectIsBackupChief').attr('checked', 'checked');
                        $('a.wd-combobox-2 .wd-bk-'+valListBackup).append('(B)');
                    }
                    $ids_2.push(employId);
                });
            }
            /**
             * When click in checkbox
             */
            $(data).find('#ProjectChiefBusinessList').click(function(){
                var _datas = $(this).val();
                if($(this).is(':checked')){
                    $(backup).find('#ProjectIsBackupChief').removeAttr('disabled');
                    $ids_2.push(_datas);
                    $('a.wd-combobox-2').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                } else {
                    $ids_2 = jQuery.removeFromArray(_datas, $ids_2);
                    $(backup).find('#ProjectIsBackupChief').attr('disabled', 'disabled');
                    $('a.wd-combobox-2').find('.wd-dt-' +_datas).remove();
                    $('a.wd-combobox-2').find('.wd-em-' +_datas).remove();
                    $(backup).find('#ProjectIsBackupChief').removeAttr('checked');
                }
                if($ids_2.length > 1){
                    for(var i = 0; i < $ids_2.length; i++){
                        var _bkup = $(backup).find('#ProjectIsBackupChief').val();
                        if($ids_2[i] != $ids_2[0] && $ids_2[i] == _bkup){
                            $(backup).find('#ProjectIsBackupChief').attr('checked', 'checked');
                            $('a.wd-combobox-2 .wd-bk-'+_bkup).append('(B)');
                        }
                    }
                }
            });
            /**
             * When click in checkbox BACKUP
             */
            $(backup).find('#ProjectIsBackupChief').click(function(){
                var _bkup = $(backup).find('#ProjectIsBackupChief').val();
                if($(this).is(':checked')) {
                    $('a.wd-combobox-2 .wd-bk-'+_bkup).append('(B)');
                } else {
                    $('a.wd-combobox-2').find('.wd-bk-' +_bkup).remove();
                    $('a.wd-combobox-2 .wd-dt-' +_bkup).append('<span class="wd-bk-'+_bkup+'"></span>');
                }
            });
        });

        /**
         * Phan chon cac phan tu trong combobox cua chiefBusiness
         */
        var $ids_3 = [];
        technicalManager.each(function(){
            var data = $(this).find('.wd-data');
            var backup = $(this).find('.wd-backup');
            /**
             * When load data
             */
            var valList = $(data).find('#ProjectTechnicalManagerList').val();
            var valListBackup = $(backup).find('#ProjectIsBackupTech').val();
            if(technicalManagerDatas){
                $.each(technicalManagerDatas, function(employId, isBackup){
                    isBackup = (isBackup == 1) ? employId : 0;
                    if(valList == employId){
                        $(data).find('#ProjectTechnicalManagerList').attr('checked', 'checked');
                        $(backup).find('#ProjectIsBackupTech').removeAttr('disabled');
                        $('a.wd-combobox-3').append('<span class="wd-dt-'+valList+'">' + $('.wd-group-' + valList).find('span').html() + '<span class="wd-bk-'+valList+'"></span></span><span class="wd-em-'+valList+'">, </span>');
                    }
                    if(valListBackup == isBackup){
                        $(backup).find('#ProjectIsBackupTech').attr('checked', 'checked');
                        $('a.wd-combobox-3 .wd-bk-'+valListBackup).append('(B)');
                    }
                    $ids_3.push(employId);
                });
            }
            /**
             * When click in checkbox
             */
            $(data).find('#ProjectTechnicalManagerList').click(function(){
                var _datas = $(this).val();
                if($(this).is(':checked')){
                    $(backup).find('#ProjectIsBackupTech').removeAttr('disabled');
                    $ids_3.push(_datas);
                    $('a.wd-combobox-3').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                } else {
                    $ids_3 = jQuery.removeFromArray(_datas, $ids_3);
                    $(backup).find('#ProjectIsBackupTech').attr('disabled', 'disabled');
                    $('a.wd-combobox-3').find('.wd-dt-' +_datas).remove();
                    $('a.wd-combobox-3').find('.wd-em-' +_datas).remove();
                    $(backup).find('#ProjectIsBackupTech').removeAttr('checked');
                }
                if($ids_3.length > 1){
                    for(var i = 0; i < $ids_3.length; i++){
                        var _bkup = $(backup).find('#ProjectIsBackupTech').val();
                        if($ids_3[i] != $ids_3[0] && $ids_3[i] == _bkup){
                            $(backup).find('#ProjectIsBackupTech').attr('checked', 'checked');
                            $('a.wd-combobox-3 .wd-bk-'+_bkup).append('(B)');
                        }
                    }
                }
            });
            /**
             * When click in checkbox BACKUP
             */
            $(backup).find('#ProjectIsBackupTech').click(function(){
                var _bkup = $(backup).find('#ProjectIsBackupTech').val();
                if($(this).is(':checked')) {
                    $('a.wd-combobox-3 .wd-bk-'+_bkup).append('(B)');
                } else {
                    $('a.wd-combobox-3').find('.wd-bk-' +_bkup).remove();
                    $('a.wd-combobox-3 .wd-dt-' +_bkup).append('<span class="wd-bk-'+_bkup+'"></span>');
                }
            });
        });

        /**
         * Phan chon cac phan tu trong combobox cua lead
         */
        var $ids_4 = [];
        functionalLeader.each(function(){
            var data = $(this).find('.wd-data');
            var backup = $(this).find('.wd-backup');
            /**
             * When load data
             */
            var valList = $(data).find('#ProjectFunctionalLeaderList').val();
            var valListBackup = $(backup).find('#ProjectIsBackupLead').val();
            if(functionalLeaderDatas){
                $.each(functionalLeaderDatas, function(employId, isBackup){
                    isBackup = (isBackup == 1) ? employId : 0;
                    if(valList == employId){
                        $(data).find('#ProjectFunctionalLeaderList').attr('checked', 'checked');
                        $(backup).find('#ProjectIsBackupLead').removeAttr('disabled');
                        $('a.wd-combobox-4').append('<span class="wd-dt-'+valList+'">' + $('.wd-group-' + valList).find('span').html() + '<span class="wd-bk-'+valList+'"></span></span><span class="wd-em-'+valList+'">, </span>');
                    }
                    if(valListBackup == isBackup){
                        $(backup).find('#ProjectIsBackupLead').attr('checked', 'checked');
                        $('a.wd-combobox-4 .wd-bk-'+valListBackup).append('(B)');
                    }
                    $ids_4.push(employId);
                });
            }
            /**
             * When click in checkbox
             */
            $(data).find('#ProjectFunctionalLeaderList').click(function(){
                var _datas = $(this).val();
                if($(this).is(':checked')){
                    $(backup).find('#ProjectIsBackupLead').removeAttr('disabled');
                    $ids_4.push(_datas);
                    $('a.wd-combobox-4').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                } else {
                    $ids_4 = jQuery.removeFromArray(_datas, $ids_4);
                    $(backup).find('#ProjectIsBackupLead').attr('disabled', 'disabled');
                    $('a.wd-combobox-4').find('.wd-dt-' +_datas).remove();
                    $('a.wd-combobox-4').find('.wd-em-' +_datas).remove();
                    $(backup).find('#ProjectIsBackupLead').removeAttr('checked');
                }
                if($ids_4.length > 1){
                    for(var i = 0; i < $ids_4.length; i++){
                        var _bkup = $(backup).find('#ProjectIsBackupLead').val();
                        if($ids_4[i] != $ids_4[0] && $ids_4[i] == _bkup){
                            $(backup).find('#ProjectIsBackupLead').attr('checked', 'checked');
                            $('a.wd-combobox-4 .wd-bk-'+_bkup).append('(B)');
                        }
                    }
                }
            });
            /**
             * When click in checkbox BACKUP
             */
            $(backup).find('#ProjectIsBackupLead').click(function(){
                var _bkup = $(backup).find('#ProjectIsBackupLead').val();
                if($(this).is(':checked')) {
                    $('a.wd-combobox-4 .wd-bk-'+_bkup).append('(B)');
                } else {
                    $('a.wd-combobox-4').find('.wd-bk-' +_bkup).remove();
                    $('a.wd-combobox-4 .wd-dt-' +_bkup).append('<span class="wd-bk-'+_bkup+'"></span>');
                }
            });
        });

        /**
         * Phan chon cac phan tu trong combobox cua uat
         */
        var $ids_5 = [];
        uatManager.each(function(){
            var data = $(this).find('.wd-data');
            var backup = $(this).find('.wd-backup');
            /**
             * When load data
             */
            var valList = $(data).find('#ProjectUatManagerList').val();
            var valListBackup = $(backup).find('#ProjectIsBackupUat').val();
            if(uatManagerDatas){
                $.each(uatManagerDatas, function(employId, isBackup){
                    isBackup = (isBackup == 1) ? employId : 0;
                    if(valList == employId){
                        $(data).find('#ProjectUatManagerList').attr('checked', 'checked');
                        $(backup).find('#ProjectIsBackupUat').removeAttr('disabled');
                        $('a.wd-combobox-5').append('<span class="wd-dt-'+valList+'">' + $('.wd-group-' + valList).find('span').html() + '<span class="wd-bk-'+valList+'"></span></span><span class="wd-em-'+valList+'">, </span>');
                    }
                    if(valListBackup == isBackup){
                        $(backup).find('#ProjectIsBackupUat').attr('checked', 'checked');
                        $('a.wd-combobox-5 .wd-bk-'+valListBackup).append('(B)');
                    }
                    $ids_5.push(employId);
                });
            }
            /**
             * When click in checkbox
             */
            $(data).find('#ProjectUatManagerList').click(function(){
                var _datas = $(this).val();
                if($(this).is(':checked')){
                    $(backup).find('#ProjectIsBackupUat').removeAttr('disabled');
                    $ids_5.push(_datas);
                    $('a.wd-combobox-5').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                } else {
                    $ids_5 = jQuery.removeFromArray(_datas, $ids_5);
                    $(backup).find('#ProjectIsBackupUat').attr('disabled', 'disabled');
                    $('a.wd-combobox-5').find('.wd-dt-' +_datas).remove();
                    $('a.wd-combobox-5').find('.wd-em-' +_datas).remove();
                    $(backup).find('#ProjectIsBackupUat').removeAttr('checked');
                }
                if($ids_5.length > 1){
                    for(var i = 0; i < $ids_5.length; i++){
                        var _bkup = $(backup).find('#ProjectIsBackupUat').val();
                        if($ids_5[i] != $ids_5[0] && $ids_5[i] == _bkup){
                            $(backup).find('#ProjectIsBackupUat').attr('checked', 'checked');
                            $('a.wd-combobox-5 .wd-bk-'+_bkup).append('(B)');
                        }
                    }
                }
            });
            /**
             * When click in checkbox BACKUP
             */
            $(backup).find('#ProjectIsBackupUat').click(function(){
                var _bkup = $(backup).find('#ProjectIsBackupUat').val();
                if($(this).is(':checked')) {
                    $('a.wd-combobox-5 .wd-bk-'+_bkup).append('(B)');
                } else {
                    $('a.wd-combobox-5').find('.wd-bk-' +_bkup).remove();
                    $('a.wd-combobox-5 .wd-dt-' +_bkup).append('<span class="wd-bk-'+_bkup+'"></span>');
                }
            });
        });
        /**
         * Phan chon cac phan tu trong combobox cua readAccess
         */
        var $ids_6 = [];
        readAccess.each(function(){
            var data = $(this).find('.wd-data');
            var backup = $(this).find('.wd-backup');
            /**
             * When load data
             */
            var valList = $(data).find('#ProjectReadAccess').val();
            valList = valList.toString().split('-');
           
            if(readAccessDatas){
                $.each(readAccessDatas, function(employId, isProfitCenter){
                    if(valList[0] == employId && valList[1] == isProfitCenter){
                        
                        $(data).find('#ProjectReadAccess').attr('checked', 'checked');
                        $('a.wd-combobox-6').append('<span class="wd-dt-'+valList[0]+'-'+valList[1]+'">' + $('.wd-group-' + valList[0]+'-'+valList[1]).find('span').html() + '<span class="wd-bk-'+valList[0]+'-'+valList[1]+'"></span></span><span class="wd-em-'+valList[0]+'-'+valList[1]+'">, </span>');
                    }
                    $ids_6.push(employId + '-' + isProfitCenter);
                });
            }
            /**
             * When click in checkbox
             */
            $(data).find('#ProjectReadAccess').click(function(){
                var _datas = $(this).val();
                if($(this).is(':checked')){
                    $(backup).find('#ProjectIsBackup').removeAttr('disabled');
                    $ids_6.push(_datas);
                    $('a.wd-combobox-6').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                } else {
                    $ids_6 = jQuery.removeFromArray(_datas, $ids_6);
                    $(backup).find('#ProjectIsBackup').attr('disabled', 'disabled');
                    $('a.wd-combobox-6').find('.wd-dt-' +_datas).remove();
                    $('a.wd-combobox-6').find('.wd-em-' +_datas).remove();
                }
            });
        });
        /**
         * Phan chon cac phan tu trong combobox cua readAccess
         */
        var $ids_7 = [];
        //var _phaseHaveTasks = $.map(phaseHaveTasks, function(value, index) {
//            return value;
//        });
        currentPhase.each(function(){
            var data = $(this).find('.wd-data');
            var backup = $(this).find('.wd-backup');
            /**
             * When load data
             */
            var valList = $(data).find('#ProjectProjectPhaseId').val();
            if(currentPhaseDatas){
                $.each(currentPhaseDatas, function(idPlan, idPhase){
                    if(valList == idPhase){
                        $(data).find('#ProjectProjectPhaseId').attr('checked', 'checked');
                        if($.inArray(idPhase, $ids_7) != -1){
                            //do nothing
                        } else {
                            $('a.wd-combobox-7').append('<span class="wd-dt-'+valList+'">' + $('.wd-group-' + valList).find('span').html() + '<span class="wd-bk-'+valList+'"></span></span><span class="wd-em-'+valList+'">, </span>');
                            $ids_7.push(idPhase);
                        }
                    }

                });
            }
            /**
             * When click in checkbox
             */
            $(data).find('#ProjectProjectPhaseId').click(function(){
                var _datas = $(this).val();
                if($(this).is(':checked')){
                    $ids_7.push(_datas);
                    $('a.wd-combobox-7').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                } else {
                    $ids_7 = jQuery.removeFromArray(_datas, $ids_7);
                    $('a.wd-combobox-7').find('.wd-dt-' +_datas).remove();
                    $('a.wd-combobox-7').find('.wd-em-' +_datas).remove();
                }
            });
        });
        //list multi select
        // list 1
        var $ids_8 = [];
        listMultiSelect = $('#wd-data-project-8').find('.listMulti.wd-data-manager');
        listMultiSelect.each(function(){
            var data = $(this).find('.wd-data');
            /**
             * When load data
             */
            var valList = $(data).find('#ProjectProjectListMulti1').val();
            if(ProjectMultiLists['project_list_multi_1']){
                $.each(ProjectMultiLists['project_list_multi_1'], function(idPlan, idPhase){
                    if(valList == idPhase){
                        $(data).find('#ProjectProjectListMulti1').attr('checked', 'checked');
                        if($.inArray(idPhase, $ids_8) != -1){
                            //do nothing
                        } else {
                            $('a.wd-combobox-8').append('<span class="wd-dt-'+valList+'">' + $('.listMulti.wd-data-manager.wd-group-' + valList).find('span').html() + '<span class="wd-bk-'+valList+'"></span></span><span class="wd-em-'+valList+'">, </span>');
                            $ids_8.push(idPhase);
                        }
                    }

                });
            }
            /**
             * When click in checkbox
             */
            $(data).find('#ProjectProjectListMulti1').on('hover', function(){
                var _datas = $(this).val();
                if($(this).is(':checked')){
                    $ids_8.push(_datas);
                    $('a.wd-combobox-8').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                } else {
                    $ids_8 = jQuery.removeFromArray(_datas, $ids_8);
                    $('a.wd-combobox-8').find('.wd-dt-' +_datas).remove();
                    $('a.wd-combobox-8').find('.wd-em-' +_datas).remove();
                }
            });
        });
        // list 2
        var $ids_9 = [];
        listMultiSelect = $('#wd-data-project-9').find('.listMulti.wd-data-manager');
        listMultiSelect.each(function(){
            var data = $(this).find('.wd-data');
            /**
             * When load data
             */
            var valList = $(data).find('#ProjectProjectListMulti2').val();
            if(ProjectMultiLists['project_list_multi_2']){
                $.each(ProjectMultiLists['project_list_multi_2'], function(idPlan, idPhase){
                    if(valList == idPhase){
                        $(data).find('#ProjectProjectListMulti2').attr('checked', 'checked');
                        if($.inArray(idPhase, $ids_9) != -1){
                            //do nothing
                        } else {
                            $('a.wd-combobox-9').append('<span class="wd-dt-'+valList+'">' + $('.listMulti.wd-data-manager.wd-group-' + valList).find('span').html() + '<span class="wd-bk-'+valList+'"></span></span><span class="wd-em-'+valList+'">, </span>');
                            $ids_9.push(idPhase);
                        }
                    }

                });
            }
            /**
             * When click in checkbox
             */
            $(data).find('#ProjectProjectListMulti2').click(function(){
                var _datas = $(this).val();
                if($(this).is(':checked')){
                    $ids_9.push(_datas);
                    $('a.wd-combobox-9').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                } else {
                    $ids_9 = jQuery.removeFromArray(_datas, $ids_9);
                    $('a.wd-combobox-9').find('.wd-dt-' +_datas).remove();
                    $('a.wd-combobox-9').find('.wd-em-' +_datas).remove();
                }
            });
        });
        // lisst 3
        var $ids_10 = [];
        listMultiSelect = $('#wd-data-project-10').find('.listMulti.wd-data-manager');
        listMultiSelect.each(function(){
            var data = $(this).find('.wd-data');
            /**
             * When load data
             */
            var valList = $(data).find('#ProjectProjectListMulti3').val();
            if(ProjectMultiLists['project_list_multi_3']){
                $.each(ProjectMultiLists['project_list_multi_3'], function(idPlan, idPhase){
                    if(valList == idPhase){
                        $(data).find('#ProjectProjectListMulti3').attr('checked', 'checked');
                        if($.inArray(idPhase, $ids_10) != -1){
                            //do nothing
                        } else {
                            $('a.wd-combobox-10').append('<span class="wd-dt-'+valList+'">' + $('.listMulti.wd-data-manager.wd-group-' + valList).find('span').html() + '<span class="wd-bk-'+valList+'"></span></span><span class="wd-em-'+valList+'">, </span>');
                            $ids_10.push(idPhase);
                        }
                    }

                });
            }
            /**
             * When click in checkbox
             */
            $(data).find('#ProjectProjectListMulti3').click(function(){
                var _datas = $(this).val();
                if($(this).is(':checked')){
                    $ids_10.push(_datas);
                    $('a.wd-combobox-10').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                } else {
                    $ids_10 = jQuery.removeFromArray(_datas, $ids_10);
                    $('a.wd-combobox-10').find('.wd-dt-' +_datas).remove();
                    $('a.wd-combobox-10').find('.wd-em-' +_datas).remove();
                }
            });
        });
        // list 4
        var $ids_11 = [];
        listMultiSelect = $('#wd-data-project-11').find('.listMulti.wd-data-manager');
        listMultiSelect.each(function(){
            var data = $(this).find('.wd-data');
            /**
             * When load data
             */
            var valList = $(data).find('#ProjectProjectListMulti4').val();
            if(ProjectMultiLists['project_list_multi_4']){
                $.each(ProjectMultiLists['project_list_multi_4'], function(idPlan, idPhase){
                    if(valList == idPhase){
                        $(data).find('#ProjectProjectListMulti4').attr('checked', 'checked');
                        if($.inArray(idPhase, $ids_11) != -1){
                            //do nothing
                        } else {
                            $('a.wd-combobox-11').append('<span class="wd-dt-'+valList+'">' + $('.listMulti.wd-data-manager.wd-group-' + valList).find('span').html() + '<span class="wd-bk-'+valList+'"></span></span><span class="wd-em-'+valList+'">, </span>');
                            $ids_11.push(idPhase);
                        }
                    }

                });
            }
            /**
             * When click in checkbox
             */
            $(data).find('#ProjectProjectListMulti4').click(function(){
                var _datas = $(this).val();
                if($(this).is(':checked')){
                    $ids_11.push(_datas);
                    $('a.wd-combobox-11').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                } else {
                    $ids_11 = jQuery.removeFromArray(_datas, $ids_11);
                    $('a.wd-combobox-11').find('.wd-dt-' +_datas).remove();
                    $('a.wd-combobox-11').find('.wd-em-' +_datas).remove();
                }
            });
        });
        //list 5
        var $ids_12 = [];
        listMultiSelect = $('#wd-data-project-12').find('.listMulti.wd-data-manager');
        listMultiSelect.each(function(){
            var data = $(this).find('.wd-data');
            /**
             * When load data
             */
            var valList = $(data).find('#ProjectProjectListMulti5').val();
            if(ProjectMultiLists['project_list_multi_5']){
                $.each(ProjectMultiLists['project_list_multi_5'], function(idPlan, idPhase){
                    if(valList == idPhase){
                        $(data).find('#ProjectProjectListMulti5').attr('checked', 'checked');
                        if($.inArray(idPhase, $ids_8) != -1){
                            //do nothing
                        } else {
                            $('a.wd-combobox-12').append('<span class="wd-dt-'+valList+'">' + $('.listMulti.wd-data-manager.wd-group-' + valList).find('span').html() + '<span class="wd-bk-'+valList+'"></span></span><span class="wd-em-'+valList+'">, </span>');
                            $ids_12.push(idPhase);
                        }
                    }

                });
            }
            /**
             * When click in checkbox
             */
            $(data).find('#ProjectProjectListMulti5').click(function(){
                var _datas = $(this).val();
                if($(this).is(':checked')){
                    $ids_12.push(_datas);
                    $('a.wd-combobox-12').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                } else {
                    $ids_12 = jQuery.removeFromArray(_datas, $ids_12);
                    $('a.wd-combobox-12').find('.wd-dt-' +_datas).remove();
                    $('a.wd-combobox-12').find('.wd-em-' +_datas).remove();
                }
            });
        });
        // list 6
        var $ids_13 = [];
        listMultiSelect = $('#wd-data-project-13').find('.listMulti.wd-data-manager');
        listMultiSelect.each(function(){
            var data = $(this).find('.wd-data');
            /**
             * When load data
             */
            var valList = $(data).find('#ProjectProjectListMulti6').val();
            if(ProjectMultiLists['project_list_multi_6']){
                $.each(ProjectMultiLists['project_list_multi_6'], function(idPlan, idPhase){
                    if(valList == idPhase){
                        $(data).find('#ProjectProjectListMulti6').attr('checked', 'checked');
                        if($.inArray(idPhase, $ids_13) != -1){
                            //do nothing
                        } else {
                            $('a.wd-combobox-13').append('<span class="wd-dt-'+valList+'">' + $('.listMulti.wd-data-manager.wd-group-' + valList).find('span').html() + '<span class="wd-bk-'+valList+'"></span></span><span class="wd-em-'+valList+'">, </span>');
                            $ids_13.push(idPhase);
                        }
                    }

                });
            }
            /**
             * When click in checkbox
             */
            $(data).find('#ProjectProjectListMulti6').click(function(){
                var _datas = $(this).val();
                if($(this).is(':checked')){
                    $ids_13.push(_datas);
                    $('a.wd-combobox-13').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                } else {
                    $ids_13 = jQuery.removeFromArray(_datas, $ids_13);
                    $('a.wd-combobox-13').find('.wd-dt-' +_datas).remove();
                    $('a.wd-combobox-13').find('.wd-em-' +_datas).remove();
                }
            });
        });
        // list 7
        var $ids_14 = [];
        listMultiSelect = $('#wd-data-project-14').find('.listMulti.wd-data-manager');
        listMultiSelect.each(function(){
            var data = $(this).find('.wd-data');
            /**
             * When load data
             */
            var valList = $(data).find('#ProjectProjectListMulti7').val();
            if(ProjectMultiLists['project_list_multi_7']){
                $.each(ProjectMultiLists['project_list_multi_7'], function(idPlan, idPhase){
                    if(valList == idPhase){
                        $(data).find('#ProjectProjectListMulti7').attr('checked', 'checked');
                        if($.inArray(idPhase, $ids_14) != -1){
                            //do nothing
                        } else {
                            $('a.wd-combobox-14').append('<span class="wd-dt-'+valList+'">' + $('.listMulti.wd-data-manager.wd-group-' + valList).find('span').html() + '<span class="wd-bk-'+valList+'"></span></span><span class="wd-em-'+valList+'">, </span>');
                            $ids_14.push(idPhase);
                        }
                    }

                });
            }
            /**
             * When click in checkbox
             */
            $(data).find('#ProjectProjectListMulti7').click(function(){
                var _datas = $(this).val();
                if($(this).is(':checked')){
                    $ids_14.push(_datas);
                    $('a.wd-combobox-14').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                } else {
                    $ids_14 = jQuery.removeFromArray(_datas, $ids_14);
                    $('a.wd-combobox-14').find('.wd-dt-' +_datas).remove();
                    $('a.wd-combobox-14').find('.wd-em-' +_datas).remove();
                }
            });
        });
        // list 8
        var $ids_15 = [];
        listMultiSelect = $('#wd-data-project-15').find('.listMulti.wd-data-manager');
        listMultiSelect.each(function(){
            var data = $(this).find('.wd-data');
            /**
             * When load data
             */
            var valList = $(data).find('#ProjectProjectListMulti8').val();
            if(ProjectMultiLists['project_list_multi_8']){
                $.each(ProjectMultiLists['project_list_multi_8'], function(idPlan, idPhase){
                    if(valList == idPhase){
                        $(data).find('#ProjectProjectListMulti8').attr('checked', 'checked');
                        if($.inArray(idPhase, $ids_15) != -1){
                            //do nothing
                        } else {
                            $('a.wd-combobox-15').append('<span class="wd-dt-'+valList+'">' + $('.listMulti.wd-data-manager.wd-group-' + valList).find('span').html() + '<span class="wd-bk-'+valList+'"></span></span><span class="wd-em-'+valList+'">, </span>');
                            $ids_15.push(idPhase);
                        }
                    }

                });
            }
            /**
             * When click in checkbox
             */
            $(data).find('#ProjectProjectListMulti8').click(function(){
                var _datas = $(this).val();
                if($(this).is(':checked')){
                    $ids_15.push(_datas);
                    $('a.wd-combobox-15').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                } else {
                    $ids_15 = jQuery.removeFromArray(_datas, $ids_15);
                    $('a.wd-combobox-15').find('.wd-dt-' +_datas).remove();
                    $('a.wd-combobox-15').find('.wd-em-' +_datas).remove();
                }
            });
        });
        // list 9
        var $ids_16 = [];
        listMultiSelect = $('#wd-data-project-16').find('.listMulti.wd-data-manager');
        listMultiSelect.each(function(){
            var data = $(this).find('.wd-data');
            /**
             * When load data
             */
            var valList = $(data).find('#ProjectProjectListMulti9').val();
            if(ProjectMultiLists['project_list_multi_9']){
                $.each(ProjectMultiLists['project_list_multi_9'], function(idPlan, idPhase){
                    if(valList == idPhase){
                        $(data).find('#ProjectProjectListMulti9').attr('checked', 'checked');
                        if($.inArray(idPhase, $ids_16) != -1){
                            //do nothing
                        } else {
                            $('a.wd-combobox-16').append('<span class="wd-dt-'+valList+'">' + $('.listMulti.wd-data-manager.wd-group-' + valList).find('span').html() + '<span class="wd-bk-'+valList+'"></span></span><span class="wd-em-'+valList+'">, </span>');
                            $ids_16.push(idPhase);
                        }
                    }

                });
            }
            /**
             * When click in checkbox
             */
            $(data).find('#ProjectProjectListMulti9').click(function(){
                var _datas = $(this).val();
                if($(this).is(':checked')){
                    $ids_16.push(_datas);
                    $('a.wd-combobox-16').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                } else {
                    $ids_16 = jQuery.removeFromArray(_datas, $ids_16);
                    $('a.wd-combobox-16').find('.wd-dt-' +_datas).remove();
                    $('a.wd-combobox-16').find('.wd-em-' +_datas).remove();
                }
            });
        });
        // list 10
        var $ids_17 = [];
        listMultiSelect = $('#wd-data-project-17').find('.listMulti.wd-data-manager');
        listMultiSelect.each(function(){
            var data = $(this).find('.wd-data');
            /**
             * When load data
             */
            var valList = $(data).find('#ProjectProjectListMulti10').val();
            if(ProjectMultiLists['project_list_multi_10']){
                $.each(ProjectMultiLists['project_list_multi_10'], function(idPlan, idPhase){
                    if(valList == idPhase){
                        $(data).find('#ProjectProjectListMulti10').attr('checked', 'checked');
                        if($.inArray(idPhase, $ids_17) != -1){
                            //do nothing
                        } else {
                            $('a.wd-combobox-17').append('<span class="wd-dt-'+valList+'">' + $('.listMulti.wd-data-manager.wd-group-' + valList).find('span').html() + '<span class="wd-bk-'+valList+'"></span></span><span class="wd-em-'+valList+'">, </span>');
                            $ids_17.push(idPhase);
                        }
                    }

                });
            }
            /**
             * When click in checkbox
             */
            $(data).find('#ProjectProjectListMulti10').click(function(){
                var _datas = $(this).val();
                if($(this).is(':checked')){
                    $ids_17.push(_datas);
                    $('a.wd-combobox-17').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                } else {
                    $ids_17 = jQuery.removeFromArray(_datas, $ids_17);
                    $('a.wd-combobox-17').find('.wd-dt-' +_datas).remove();
                    $('a.wd-combobox-17').find('.wd-em-' +_datas).remove();
                }
            });
        });
        user_modify = <?php echo json_encode(($canModified && !$_isProfile) || $_canWrite); ?>;
        listMultiSelect = $('.wd-multiselect');
        listMultiSelect.each(function(){
            if(!user_modify) return;
            var data = $(this).find('.wd-data');
            var area_append = $(this).closest('.multiselect').find('.wd-combobox');
            var data_value = [];
            data.click(function(){
                item = $(this).closest('.wd-combobox-content').find('.wd-data-manager');
                $.each(item, function(index, elem) {
                    var checkbox = $(elem).find('input[type="checkbox"]');
                    value = checkbox.val();
                    if(checkbox.is(':checked') && value){
                        data_value.push(value);
                    }
                });
                var checkbox = $(this).find('input[type="checkbox"]');
                var checked = checkbox.is(":checked");
                checkbox.attr("checked",!checked);
                var _datas = checkbox.val();
                if(checkbox.is(':checked')){
                    var circle_name = $(this).find('.circle-name').html();
                    var title = $(this).find('.circle-name').attr('title');
                    data_value.push(_datas)
                    area_append.find('p').css('display', 'none');
                    area_append.append('<a class="circle-name" title="' + title + '">' + circle_name + '</a>');
                }else{
                    data_value = jQuery.removeFromArray(_datas, data_value);
                    area_append.find('span[data-id = "'+ _datas + '"]').closest('.circle-name').remove();
                }
                checkbox.trigger('change');
            });
            $(area_append).on('click', '.circle-name span', function(){
                var eid = $(this).data('id');
                $(this).closest('.circle-name').remove();
                data.find('input[type="checkbox"][value = "'+ eid +'"]').attr("checked", false).trigger('change');
                data_value = jQuery.removeFromArray(eid, data_value);
                if(!(area_append.find('.circle-name').length)) area_append.children('p').show();
            });
            var timeoutID;
            $('.wd-input-search').keyup(function(){
                var _this = $(this);
                clearTimeout(timeoutID);
                timeoutID = setTimeout(function(){
                    var val = $.trim(_this.val()).toLowerCase();
                    _this.closest('.wd-combobox-content').find('.wd-data').each(function(){
                        var label = $(this).find('.option-name').html().toLowerCase();
                        if(!val.length || label.indexOf(val) != -1 || !val){
                            $(this).closest('.wd-data-manager').css('display', 'block');
                        } else{
                            $(this).closest('.wd-data-manager').css('display', 'none');
                        }
                    });
                } , 200);
            });
        });
    });
    /**
     * Multiple Upload
     */
    var uploader = $("#uploaderDocument1").pluploadQueue({
        runtimes : 'html5, html4',
        url : "/projects/uploads/"+project_id+'/upload_documents_1',
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
                up.project_id = project_id;
                up.linkedAction = '/projects/attachment/';
                if(projectFiles1 && Object.keys(projectFiles1).length > 0){
                    up.auditFiles = projectFiles1;
                    var tmpHtml = '';
                    var display_none = '';
                    if(showAllFieldYourform == 0){
                        display_none = 'display: none';
                    }
                    $.each(projectFiles1, function(ind, val){
                        var hrefDownload = '/projects/attachment/upload_documents_1'+'/'+project_id+'/'+val.id+'/download/';
                        var hrefDelete = '/projects/attachment/upload_documents_1'+'/'+project_id+'/'+val.id+'/delete/';
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
    var uploader = $("#uploaderDocument2").pluploadQueue({
        runtimes : 'html5, html4',
        url : "/projects/uploads/"+project_id+'/upload_documents_2',
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
                up.project_id = project_id;
                up.linkedAction = '/projects/attachment/';
                if(projectFiles2 && Object.keys(projectFiles2).length > 0){
                    up.auditFiles = projectFiles2;
                    var tmpHtml = '';
                    var display_none = '';
                    if(showAllFieldYourform == 0){
                        display_none = 'display: none';
                    }
                    $.each(projectFiles2, function(ind, val){
                        var hrefDownload = '/projects/attachment/upload_documents_2'+'/'+project_id+'/'+val.id+'/download/';
                        var hrefDelete = '/projects/attachment/upload_documents_2'+'/'+project_id+'/'+val.id+'/delete/';
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
                    $('#uploaderDocument2_filelist').html(tmpHtml);
                }
            }
        }
    });
    var uploader = $("#uploaderDocument3").pluploadQueue({
        runtimes : 'html5, html4',
        url : "/projects/uploads/"+project_id+'/upload_documents_3',
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
                up.project_id = project_id;
                up.linkedAction = '/projects/attachment/';
                if(projectFiles3 && Object.keys(projectFiles3).length > 0){
                    up.auditFiles = projectFiles3;
                    var tmpHtml = '';
                    var display_none = '';
                    if(showAllFieldYourform == 0){
                        display_none = 'display: none';
                    }
                    $.each(projectFiles3, function(ind, val){
                        var hrefDownload = '/projects/attachment/upload_documents_3'+'/'+project_id+'/'+val.id+'/download/';
                        var hrefDelete = '/projects/attachment/upload_documents_3'+'/'+project_id+'/'+val.id+'/delete/';
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
                    $('#uploaderDocument3_filelist').html(tmpHtml);
                }
            }
        }
    });
    var uploader = $("#uploaderDocument4").pluploadQueue({
        runtimes : 'html5, html4',
        url : "/projects/uploads/"+project_id+'/upload_documents_4',
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
                up.project_id = project_id;
                up.linkedAction = '/projects/attachment/';
                if(projectFiles4 && Object.keys(projectFiles4).length > 0){
                    up.auditFiles = projectFiles4;
                    var tmpHtml = '';
                    var display_none = '';
                    if(showAllFieldYourform == 0){
                        display_none = 'display: none';
                    }
                    $.each(projectFiles4, function(ind, val){
                        var hrefDownload = '/projects/attachment/upload_documents_4'+'/'+project_id+'/'+val.id+'/download/';
                        var hrefDelete = '/projects/attachment/upload_documents_4'+'/'+project_id+'/'+val.id+'/delete/';
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
                    $('#uploaderDocument4_filelist').html(tmpHtml);
                }
            }
        }
    });
    var uploader = $("#uploaderDocument5").pluploadQueue({
        runtimes : 'html5, html4',
        url : "/projects/uploads/"+project_id+'/upload_documents_5',
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
                up.project_id = project_id;
                up.linkedAction = '/projects/attachment/';
                if(projectFiles5 && Object.keys(projectFiles5).length > 0){
                    up.auditFiles = projectFiles5;
                    var tmpHtml = '';
                    var display_none = '';
                    if(showAllFieldYourform == 0){
                        display_none = 'display: none';
                    }
                    $.each(projectFiles5, function(ind, val){
                        var hrefDownload = '/projects/attachment/upload_documents_5'+'/'+project_id+'/'+val.id+'/download/';
                        var hrefDelete = '/projects/attachment/upload_documents_5'+'/'+project_id+'/'+val.id+'/delete/';
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
                    $('#uploaderDocument5_filelist').html(tmpHtml);
                }
            }
        }
    });
    if(showAllFieldYourform == 0){
        $('.plupload_start').css('display', 'none');
    }
    $("a.fancy").fancybox();
    $('#carousel').flexslider({
        animation: "slide",
        controlNav: false,
        animationLoop: false,
        slideshow: false,
        itemWidth: 150,
        itemMargin: 5,
        minItems: 5,
        asNavFor: '#slider',
        pauseOnHover: true,
      });

    $('#slider').flexslider({
        animation: "slide",
        controlNav: false,
        animationLoop: true,
        slideshow: true,
        pauseOnAction: false,
        pauseOnHover: true,
        sync: "#carousel",
    });
    $(document).ready(function () {
      $('.textarea-limit').on('keypress', function (event) {
        var textarea = $(this),
            numberOfLines = (textarea.val().match(/\n/g) || []).length + 1,
            maxRows = 2;
        if (event.which === 13 && numberOfLines === maxRows ) {
            return false;
        }
      });
      $('.not-decimal').on('keypress', function (e) {
        var key = e.keyCode ? e.keyCode : e.which;
        if (key == 46 ) {
            return false;
        }
      });

      $('.wd-behavior').on('click', function(){
            $(this).next('.wd-content-field').toggle();
      });
    });
    // call big image.
    $('.flex-active-slide img').live('click', function(){
        var i = $(this).data('url');
        hideMe();
        var _html = '<img style="width: 100%; height: 100%" src="'+i+'">';
        jQuery('#dialogDetailValue').css({'padding-bottom':0});
        jQuery('#dialogDetailValue').css({'top':'23%', 'left': '25%'});
        $('.loading_w').show();
        setTimeout(function(){
            $('.loading_w').hide();
            jQuery('#contentDialog').html(_html);
            showMe();
        }, 500);
    });
    // check name ActivityLinked
    $(document).ready(function(){
        var edit = false;
        $("#ActivityLinkedName").focus(function(){
            edit = false;
            $("#ok_save_ac_linked").hide();
        });
        $("#ActivityLinkedName").keypress(function(){
            edit = true;
        });
        $("#ActivityLinkedName").one('paste', function(){
            edit = true;
        });
        $("#ActivityLinkedName").blur(function(){
            if(edit == false){
                $("#ok_save_ac_linked").show();
            }
        });
        $("#ActivityLinkedName").on('change',function(){
            // goi len controll de kiem tra xem code 1 da ton tai
            if($('#ActivityLinkedName').val() != ''){
                $('#onChangeNameActivity').html("<img src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt='Loading' />");
                $("#onChangeNameActivity").show();
                $.ajax({
                    url: '<?php echo $html->url(array('action' => 'checkNameAcitivity')); ?>',
                    type: 'POST',
                    data: {
                        data: {
                            code: $('#ActivityLinkedName').val()
                        }
                    },
                    success: function(data) {
                       if( data == 1 ){
                            $("#ok_save_ac_linked").hide();
                            $('#onChangeNameActivity').css('color', 'red');
                            $('#onChangeNameActivity').html(activityNameExited);
                        } else {
                            $("#onChangeNameActivity").hide();
                            $("#ok_save_ac_linked").show();
                        }
                    }
                });
            } else {
                $("#ok_save_ac_linked").show();
                $("#onChangeNameActivity").hide();
            }
        });
        $("#ActivityLinkedName").blur(function(){
            // goi len controll de kiem tra xem code 1 da ton tai
            if($('#ActivityLinkedName').val() != ''){
                $('#onChangeNameActivity').html("<img src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt='Loading' />");
                $("#onChangeNameActivity").show();
                $.ajax({
                    url: '<?php echo $html->url(array('action' => 'checkNameAcitivity')); ?>',
                    type: 'POST',
                    data: {
                        data: {
                            code: $('#ActivityLinkedName').val()
                        }
                    },
                    success: function(data) {
                        if( data == 1 ){
                            $("#ok_save_ac_linked").hide();
                            $('#onChangeNameActivity').css('color', 'red');
                            $('#onChangeNameActivity').html(activityNameExited);
                        } else {
                            $("#onChangeNameActivity").hide();
                            $("#ok_save_ac_linked").show();
                        }
                    }
                });
            } else {
                $("#ok_save_ac_linked").show();
                $("#onChangeNameActivity").hide();
            }
        });
    });
    function saveFieldSelectYourForm($this, field){
        $.ajax({
            url: '<?php echo $html->url(array('action' => 'saveFieldYourForm', $this->data['Project']['id'])); ?>',
            type: 'POST',
            data: {
                field : field,
                value : $($this).find('option:selected').val()
            },
        });
    }
    function saveFieldChangeCodeYourForm(value, field){
        $.ajax({
            url: '<?php echo $html->url(array('action' => 'saveFieldYourForm', $this->data['Project']['id'])); ?>',
            type: 'POST',
            data: {
                field : field,
                value : value
            },
        });
    }
    function saveFieldYourForm($this){
        var field = $($this).attr('name');
        field = field.replace('data[Project][', '').replace(']', '');
        var value = $($this).val();
        $.ajax({
            url: '<?php echo $html->url(array('action' => 'saveFieldYourForm', $this->data['Project']['id'])); ?>',
            type: 'POST',
            data: {
                field : field,
                value : value
            },
        });
    }
    function saveFieldUploadYourForm($this){
        var field = $($this).attr('name');
        field = field.replace('data[Project][', '').replace(']', '');
        var value = $($this).val();

        var file = $(this)[0].files[0];
        var upload = new Upload(file);
        $.ajax({
            url: '<?php echo $html->url(array('action' => 'saveFieldUploadYourForm', $this->data['Project']['id'])); ?>',
            type: 'POST',
            data: {
                field : upload
            },
        });
    }
    
    function expandScreen(){
        $('.wd-map-area').addClass('fullScreen');
        $('.wd-content-right').addClass('view_full');
    }
    function collapseScreen(){
        $('.wd-map-area').removeClass('fullScreen');
        $('.wd-content-right').removeClass('view_full');
        // $(window).resize();
        // $('#map-frame').resize();
    }

        //location view
    var state = 1;
    var coord = /^\s*(\-?[0-9]+\.[0-9]+)\s*,\s*(\-?[0-9]+\.[0-9]+)\s*$/;
    var gapi = <?php echo json_encode($gapi) ?>;
    function refreshMap(show){

        var map_width = $('.wd-map-area').width();
        $('.wd-map-area').find('iframe').css("width", map_width);

        var query = $.trim($('#coord-input').val());
        if( query ){
            //initial google maps
            $('#map-frame').prop('src', 'https://www.google.com/maps/embed/v1/place?q=' + encodeURIComponent(query) + '&key=' + gapi);
            if( show ){
                $('#map-frame').show();
                $('#local-frame').hide();
                state = 0;
            }
        } else {
            $('#map-frame').prop('src', 'about:blank');
        }
    }
    function refreshMapYourForm($this){
        // var newAddress = $.trim($this.val());
        var field = $($this).attr('name');
        field = field.replace('data[Project][', '').replace(']', '');
        var value = $($this).val();
        refreshMap(true);
        $.ajax({
            url: '<?php echo $html->url(array('action' => 'saveFieldYourForm', $this->data['Project']['id'])); ?>',
            type: 'POST',
            data: {
                field : field,
                value : value
            },
        });
    }
    function saveFieldMutiSelectYourForm($this){
        var listPm = [];
        var field = $($this).find('input[type="checkbox"]:first').attr('name');
        $($this).find('input:checked').each(function(val, index){
            listPm[val] = $(index).val();
        });
        field = field.replace('data[', '').replace('][]', '');
        $.ajax({
            url: '<?php echo $html->url(array('action' => 'saveFieldYourFormPM', $this->data['Project']['id'])); ?>',
            type: 'POST',
            data: {
                field : field,
                value : listPm
            },
        });
    }
    $(document).ready(function(){
        var saving = false;
        $('#coord-input').val(<?php echo json_encode($projectName['Project']['address']) ?>);
        <?php if($projectName['Project']['address']): ?> refreshMap(true);  <?php endif ?>
        
    });
    (function($){
        $(function(){
            $('#replace-attachment').click(function(){
                $('#loader').show();
                $.ajax({
                    type : 'POST',
                    url : '<?php echo $html->url('/project_global_views/delete/' . @$projectGlobalView['ProjectGlobalView']['id']) ?>',
                    success : function(){
                        $('#loader').hide();
                        $('#download-place').remove();
                        $('#upload-place').show();
                        $('iframe').prop('src', 'about:blank');
                        $('#wd-fragment-2').html('');
                    }
                });
            });
            $('.trigger-upload').click(function(){
                if(showAllFieldYourform == 0) return;
                $(this).closest('.wd-section').find('#openPopup').toggle();
            });
            $('.close-popup').click(function(){
                $(this).closest('#openPopup').toggle();
            });
        });
    })(jQuery);
    $("#attachmentUrl").parent().hide();
    $('#ProjectGlobalViewIsFile1, #ProjectGlobalViewIsFile0').click(function(){
       if($('#ProjectGlobalViewIsFile1').is(':checked')) {
            $("#ProjectGlobalViewAttachment").parent().show();
            $("#attachmentUrl").parent().hide();
            $('#file-types').show();
        } else {
            $("#ProjectGlobalViewAttachment").parent().hide();
            $("#attachmentUrl").parent().show();
            $('#file-types').hide();
        }
    });
</script>
<!-- change_category -->
<div id="change_category" class="buttons" style="display: none; min-height: 59px !important;">
    <p style="font-size: 13px; color: black; padding-left: 10px;"></p>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"><?php __("OK") ?></a></li>
    </ul>
</div>
<!-- change_category.end -->

<!-- dialog_vision_portfolio -->
<div id="save_activity_linked" class="buttons" style="display: none;">
    <fieldset>
        <?php echo $this->Form->create('ActivityLinked', array('id' => 'form_save_activity_linked', 'url' => array('controller' => 'projects', 'action' => 'saveActivityLinked', $project_name['Project']['id'], $this->data['Company']['id']))); ?>
        <div style="height:auto;" class="wd-scroll-form">
            <div class="ch-input-custom">
                <label id="id_name_activity_linked" for="name"><?php __("Name") ?></label>
                <?php
                echo $this->Form->input('name', array(
                    'div' => false,
                    'label' => false));
                ?>
                <span style="display: none; float:left; color: #000; width: 62%" id= "onChangeNameActivity"></span>
            </div>
            <div class="ch-input-custom">
                <label for="name-detail"><?php __("Long name") ?></label>
                <?php
                echo $this->Form->input('name_detail', array(
                    'div' => false,
                    'label' => false));
                ?>
            </div>
            <div class="ch-input-custom">
                <label for="short-name"><?php __("Short Name") ?></label>
                <?php
                echo $this->Form->input('short_name', array(
                    'div' => false,
                    'label' => false));
                ?>
            </div>
            <div class="ch-input-custom">
                <label for="family"><?php __("Family") ?></label>
                <?php
                $disabled = '';
                $style = 'width:62% !important;';
                if($activate_family_linked_program){
                    $disabled = 'disabled';
                    $style = 'width:62% !important; background-color: rgb(218, 221, 226);';
                }
                echo $this->Form->input('family', array(
                    'type' => 'select',
                    'div' => false,
                    'label' => false,
                    'disabled' => $disabled,
                    'style' => $style,
                    "empty" => __("-- Any --", true),
                    'onchange' => "listSubFamily();",
                    "options" => $families));
                ?>
            </div>
            <div class="ch-input-custom">
                <label for="sub-family"><?php __("Sub Family") ?></label>
                <?php
                echo $this->Form->input('sub_family', array(
                    'type' => 'select',
                    'div' => false,
                    'label' => false,
                    'disabled' => $disabled,
                    'style' => $style,
                    "empty" => __("-- Any --", true),
                    "options" => $subFamilies));
                ?>
            </div>
            <div class="ch-input-custom">
                <label for="activated" style="line-height: 16px;"><?php __("Activate timesheet filling") ?></label>
                <?php
                echo $this->Form->input('activated', array(
                    'type' => 'select',
                    'div' => false,
                    'label' => false,
                    'style' => 'width:62% !important',
                    "options" => array(
                        0 => __('No', true),
                        1 => __('Yes', true)
                    ),
                    'selected' => 1
                    ));
                ?>
            </div>
        </div>
        <?php
        echo $this->Form->end();
        ?>
    </fieldset>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel_save_ac_linked cancel"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_save_ac_linked"><?php __('OK') ?></a></li>
    </ul>
</div>
<!-- <div id="overlay-container">
    <div id="overlay-wrapper"></div>
    <div id="overlay-box">
        Please wait, Preparing export ...
    </div>
</div> -->
<!-- dialog_vision_portfolio.end -->
