<?php
App::import("vendor", "str_utility");
$str_utility = new str_utility();

?>

<?php echo $html->script('jquery.validation.min'); ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->script('jquery.dataTables'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<?php echo $html->script('validateDate'); ?>

<style>
    fieldset div textarea{
        width: 60%;
    }
    .error-message {
        color: #FF0000;
        margin-left: 35px;
    }
    .wd-input label {
        width: 100%;
        float: none;
        text-align: left;
        line-height: normal;
        color: #a9a9a9;
        display: block;
    }
    .multiselect {
        border: solid 1px #c0c0c0;
        overflow: hidden;
        width: 60.6%;
    }
    .wd-backup{

    }
    .wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7 {
        border: 1px solid #d4d4d4;
        background: url("/img/bg-combobox.png") no-repeat right 5px;
        padding: 3px 0px;
        width: 100%;
        display: block;
        height: 20px;
        overflow: hidden;
        line-height: 22px;
        text-decoration: none;
        color: black !important;
        font-weight: inherit !important;
    }
    .wd-combobox:hover, .wd-combobox-2:hover, .wd-combobox-3:hover, .wd-combobox-4:hover, .wd-combobox-5:hover, .wd-combobox-6:hover, .wd-combobox-7:hover {
        text-decoration: none;
        color: black;
        font-weight: inherit;
    }
    .wd-data-manager input{
        /*width:auto !important;
        margin-top: 8px !important;
        margin-left: 5px !important;*/
        width:25px !important;
    }

    .wd-data-manager label{
        /*width:150px !important;
        line-height:15px !important;
        text-align:left !important;
        padding-left: 5px !important;
        padding-top: 8px !important;
        font-weight: inherit;*/
    }
    #wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7 {
        height: 150px !important;
        overflow-y: auto;
    }

    .context-menu-filter, .context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7{
        clear: both;
        overflow: hidden;
        background-color: #004787;
        padding: 3px;
    }
    .context-menu .notmatch{
        display: none;
    }
    .context-menu-filter input, .context-menu-filter-2 input, .context-menu-filter-3 input, .context-menu-filter-4 input, .context-menu-filter-5 input, .context-menu-filter-6 input, .context-menu-filter-7 input{
        padding: 0 !important;
        width: 100% !important;
        border: 0 !important;
        float: none !important;
        line-height: normal !important;
        margin: 0 !important;
        background: 0 !important;
    }
    .context-menu-filter span, .context-menu-filter-2 span, .context-menu-filter-3 span, .context-menu-filter-4 span, .context-menu-filter-5 span, .context-menu-filter-6 span, .context-menu-filter-7 span{
        display: block;
        background: url("<?php echo $this->webroot ?>css/images/search_label.gif") no-repeat 2px center;
        padding-left: 17px;
        background-color: #fff;
        border: 1px solid #D4D4D4;
    }
    .context-menu-shadow{
        background-color: white !important;
    }
    .wd-input img{
        width: 30px;
        height: 30px;
    }
    #wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7 {
        display: none;
        position: absolute;
        width: 27.9%;
        border: solid 1px #c0c0c0;
        background-color: #fff;
        height: 150px !important;
        overflow-y: auto;
        z-index: 1;
    }
    #pseudo-category {
        padding: 6px;
        margin-right: 5px;
    }
</style>

<div id="wd-container-main" class="wd-project-detail">

    <div class="wd-layout">
        <div class="wd-main-content">
            <!-- start: title -->
            <div class="wd-title">
                <h2 class="wd-t1"><?php echo __("New Project", true); ?></h2>
            </div>
            <!-- end: title -->
            <div class="wd-tab">
                <div class="wd-panel">
                    <div class="wd-section" id="wd-fragment-1">
                        <?php echo $this->Session->flash(); ?>
                        <?php
                        echo $this->Form->create('Project', array('enctype' => 'multipart/form-data'));
                        ?>
<?php
/***
*
*   Maps
*
***/
/*
<div class="wd-input">
    <label for="project-name">{label}</label>
    {html}
</div>
*/

// pm template

$pmTemplate = '';
// if ($changeProjectManager) {
    $pmTemplate = '<div class="multiselect" style="float: left;margin-right: 3px;">
    <a href="" class="wd-combobox" style="border-color: red"></a>
    <div id="wd-data-project" style="display: none;" class="projectManager">';
    foreach($employees['pm'] as $idPm => $namePm):
        $pmTemplate .= '<div class="projectManager wd-data-manager wd-group-' . $idPm . '">
            <p class="projectManager wd-data" style="width: 200px; margin: 10px 5px;">
                    ' . $this->Form->input('project_employee_manager', array(
                        'label' => false,
                        'div' => false,
                        'type' => 'checkbox',
                        'class' => 'projectManager',
                        'name' => 'data[project_employee_manager][]',
                        'value' => $idPm)) . '
                    <span class="projectManager" style="padding-left: 5px;">' . $namePm . '</span>
                </p>
                <p class="wd-backup" style="display: none; float: right; margin: -27px 0; padding-right: 5px;">
                    ' . $this->Form->input('is_backup', array(
                            'label' => false,
                            'div' => false,
                            'type' => 'checkbox',
                            'disabled' => 'disabled',
                            'name' => 'data[is_backup][]',
                            'value' => $idPm)) . '
                    <span class="projectManager" style="padding-left: 5px;">backup</span>
            </p>
        </div>';
    endforeach;
    $pmTemplate .= '</div></div>';

//end pm template
//chief template

$chiefTemplate = '<div class="multiselect" style="float: left;margin-right: 3px;">
    <a href="" class="wd-combobox-2"></a>
    <div id="wd-data-project-2" style="display: none;" class="chiefBusiness">';
foreach($employees['pm'] as $idPm => $namePm):
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
        <p class="chiefBusiness wd-backup" style="display: none; float: right; margin: -27px 0; padding-right: 5px;">' .
        $this->Form->input('is_backup_chief', array(
                'label' => false,
                'div' => false,
                'type' => 'checkbox',
                'disabled' => 'disabled',
                'class' => 'chiefBusiness',
                'name' => 'data[is_backup_chief][]',
                'value' => $idPm)) .
            '<span class="chiefBusiness" style="padding-left: 5px;">backup</span>
        </p>
    </div>';
endforeach;

$chiefTemplate .= '</div></div>';

//end chief

$techTemplate = '<div class="multiselect" style="float: left;margin-right: 3px;">
    <a href="" class="wd-combobox-3"></a>
    <div id="wd-data-project-3" style="display: none;" class="technicalManager">';
foreach($employees['pm'] as $idPm => $namePm):
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
        <p class="technicalManager wd-backup" style="display: none; float: right; margin: -27px 0; padding-right: 5px;">' .
        $this->Form->input('is_backup_tech', array(
                    'label' => false,
                    'div' => false,
                    'type' => 'checkbox',
                    'disabled' => 'disabled',
                    'class' => 'technicalManager',
                    'name' => 'data[is_backup_tech][]',
                    'value' => $idPm)) .
            '<span class="technicalManager" style="padding-left: 5px;">backup</span>
        </p>
    </div>';
endforeach;
$techTemplate .= '</div></div>';

// added on 2015-04-28 by QN
// functional leader & uat manager

$leaderTemplate = '<div class="multiselect" style="float: left;margin-right: 3px;">
    <a href="javascript:void();" class="wd-combobox-4"></a>
    <div id="wd-data-project-4" style="display: none;" class="functionalLeader">';
foreach($employees['pm'] as $idPm => $namePm):
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
        <p class="functionalLeader wd-backup" style="display: none; float: right; margin: -27px 0; padding-right: 5px;">' .
            $this->Form->input('is_backup_lead', array(
                'label' => false,
                'div' => false,
                'type' => 'checkbox',
                'disabled' => 'disabled',
                'class' => 'functionalLeader',
                'name' => 'data[is_backup_lead][]',
                'value' => $idPm)) .
            '<span class="functionalLeader" style="padding-left: 5px;">backup</span>
        </p>
    </div>';
endforeach;

$leaderTemplate .= '</div></div>';


$uatTemplate = '<div class="multiselect" style="float: left;margin-right: 3px;">
    <a href="javascript:void();" class="wd-combobox-5"></a>
    <div id="wd-data-project-5" style="display: none;" class="uatManager">';
foreach($employees['pm'] as $idPm => $namePm):
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
        <p class="uatManager wd-backup" style="display: none; float: right; margin: -27px 0; padding-right: 5px;">' .
            $this->Form->input('is_backup_uat', array(
                'label' => false,
                'div' => false,
                'type' => 'checkbox',
                'disabled' => 'disabled',
                'class' => 'uatManager',
                'name' => 'data[is_backup_uat][]',
                'value' => $idPm)) .
            '<span class="uatManager" style="padding-left: 5px;">backup</span>
        </p>
    </div>';
endforeach;
$uatTemplate .= '</div></div>';

$cuPhaseTemplate = '<div class="multiselect" style="float: left;margin-right: 3px;">
    <a href="javascript:void();" class="wd-combobox-7"></a>
    <div id="wd-data-project-7" style="display: none;" class="currentPhase">';
foreach($project_phases as $idPm => $namePm):
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


$startTemplate = $this->Form->input('start_date', array('div' => false,
    'label' => false,
    'disabled' => 'disabled',
    'style' => 'background-color: rgb(223, 223, 223)',
    'value' => '',
    'type' => 'text'
));

$maps = array(
    'project_name' => array(
        'label' => __d(sprintf($_domain, 'Details'), "Project Name", true),
        'html' => $this->Form->input('project_name', array('div' => false, 'label' => false, 'maxlength' => 124, 'style' => 'border-color: red'))
    ),
    'project_code_1' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Project Code 1', true),
        'html' => $this->Form->input('project_code_1', array('div' => false, 'label' => false, 'id' => 'onChangeCode')).'<span style="display: none; float:left; color: #000; width: 62%" id= "valueOnChange"></span>'
    ),
    'company_id' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Company', true),
        'html' => $this->Form->input('company_id', array(
            'type' => 'select',
            'div' => false,
            'label' => false,
            'default' => $employee_info['Company']['id'],
            "empty" => __("-- Select -- ", true),
            "options" => $company_names,
            'style' => 'margin-right:11px; width:60.6% !important'
        ))
    ),
    'long_project_name' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Project long name', true),
        'html' => $this->Form->input('long_project_name', array('div' => false, 'label' => false))
    ),
    'project_code_2' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Project Code 2', true),
        'html' => $this->Form->input('project_code_2', array('div' => false, 'label' => false))
    ),
    'project_manager_id' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Project Manager', true),
        'html' => $pmTemplate
    ),
    'project_type_id' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Project type', true),
        'html' => $this->Form->input('project_type_id', array('div' => false, 'label' => false,
            "empty" => __("-- Select --", true),
            'style' => 'margin-right:11px; width:60.6% !important',
            "options" => $project_types
        ))
        // . $this->Form->input('project_sub_type_id', array('div' => false, 'label' => false,
        //     'style' => 'width:30% !important',
        //     'empty' => __('Select sub type', true),
        //     "options" => array()
        // ))
    ),
    'project_sub_type_id' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Sub type', true),
        'html' => $this->Form->input('project_sub_type_id', array('div' => false, 'label' => false,
            'style' => 'margin-right:11px; width:60.6% !important',
            'empty' => __("--Select--", true),
            "options" => array()
        ))
    ),
    'project_amr_program_id' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Program', true),
        'html' => $this->Form->input('project_amr_program_id', array('div' => false, 'label' => false,
            "empty" => __("--Select--", true),
            'style' => 'margin-right:11px; width:60.6% !important',
            "options" => array()
        ))
    ),
    'project_amr_sub_program_id' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Sub program', true),
        'html' => $this->Form->input('project_amr_sub_program_id', array('div' => false, 'label' => false,
            'style' => 'display: none',
            'empty' => __("--Select--", true),
            'style' => 'margin-right:11px; width:60.6% !important',
            "options" => array()
        ))
    ),
    'project_priority_id' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Priority', true),
        'html' => $this->Form->input('project_priority_id', array('div' => false, 'label' => false, "options" => $project_priorities, 'empty' => __("--Select--", true), 'style' => 'margin-right:11px; width:60.6% !important'))
    ),
    'complexity_id' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Implementation Complexity', true),
        'html' => $this->Form->input('complexity_id', array('div' => false, 'label' => false, "options" => $Complexities, 'empty' => __("--Select--", true), 'style' => 'margin-right:11px; width:60.6% !important'))
    ),
    'created_value' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Created value', true),
        'html' => $this->Form->input('created_value', array('div' => false, 'label' => false,
            "class" => "placeholder", "placeholder" => __("Created value", true), "readonly" => 'readonly',
            'style' => 'background-color: rgb(223, 223, 223)'
        ))
    ),
    'project_status_id' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Status', true),
        'html' => $this->Form->input('project_status_id', array('div' => false, 'label' => false, "options" => $project_statuses, 'empty' => __("--Select--", true), 'style' => 'margin-right:11px; width:60.6% !important'))
    ),
    'project_phase_id' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Current Phase', true),
        'html' => $cuPhaseTemplate
    ),
    // 'activity_id' => array(
    // 	'label' => __d(sprintf($_domain, 'Details'), 'Link To RMS Activity', true),
    // 	'html' => $this->Form->input('activity_id', array(
    // 		'type'      => 'select',
    // 		'div'       => false,
    // 		'label'     => false,
    // 		"options"   => $ProjectActivities,
    // 		'style' => 'margin-right:11px; width:60.6% !important',
    // 		'empty'     => __("--Select--", true)
    // 	)) . $this->Form->input('tmp_activity_id', array(
    // 		'type' => 'hidden',
    // 		'value' => $this->data['Project']['activity_id']
    // 	))
    // ),
    'issues' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Issues', true),
        'html' => $this->Form->input('issues', array('type' => 'textarea', 'div' => false, 'label' => false))
    ),
    'primary_objectives' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Primary Objectives', true),
        'html' => $this->Form->input('primary_objectives', array('type' => 'textarea', 'div' => false, 'label' => false))
    ),
    'project_objectives' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Project Objectives', true),
        'html' => $this->Form->input('project_objectives', array('type' => 'textarea', 'div' => false, 'label' => false))
    ),
    'constraint' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Constraint', true),
        'html' => $this->Form->input('constraint', array('type' => 'textarea', 'div' => false, 'label' => false))
    ),
    'remark' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Remark', true),
        'html' => $this->Form->input('remark', array('type' => 'textarea', 'div' => false, 'label' => false))
    ),
    'free_1' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Free 1', true),
        'html' => $this->Form->input('free_1', array('type' => 'textarea', 'class' => 'resizeOnFocus', 'div' => false, 'label' => false))
    ),
    'free_2' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Free 2', true),
        'html' => $this->Form->input('free_2', array('type' => 'textarea', 'class' => 'resizeOnFocus', 'div' => false, 'label' => false))
    ),
    'free_3' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Free 3', true),
        'html' => $this->Form->input('free_3', array('type' => 'textarea', 'class' => 'resizeOnFocus', 'div' => false, 'label' => false))
    ),
    'free_4' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Free 4', true),
        'html' => $this->Form->input('free_4', array('type' => 'textarea', 'class' => 'resizeOnFocus', 'div' => false, 'label' => false))
    ),
    'free_5' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Free 5', true),
        'html' => $this->Form->input('free_5', array('type' => 'textarea', 'class' => 'resizeOnFocus', 'div' => false, 'label' => false))
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
    'start_date' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Start Date', true),
        'html' => $startTemplate
    ),
    'end_date' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'End Date', true),
        'html' => $this->Form->input('end_date', array('div' => false,
            'label' => false,
            'disabled' => 'disabled',
            'style' => 'background-color: rgb(223, 223, 223)',
            'value' => '',
            'type' => 'text'
        ))
    ),
    'budget_customer_id' => array(
        'label' => __d(sprintf($_domain, 'Details'), 'Customer', true),
        'html' => $this->Form->input('budget_customer_id', array('name' => 'data[Project][budget_customer_id]',
            'type' => 'select',
            'div' => false,
            'label' => false,
            'class' => 'ui-combobox',
            //'style' => 'width:60% !important',
            "empty" => __("-- Select -- ", true),
            "options" => (array) @$budgetCustomers
        ))
    )
);
//added on 2015-11-04
$range = range(1, 8);
foreach($range as $num){
    if( $num <= 4 ){
        //date
        $maps['date_' . $num] = array(
            'label' => __d(sprintf($_domain, 'Details'), 'Date ' . $num, true),
            'html' => $this->Form->input('date_' . $num, array('type' => 'text', 'class' => 'wd-date', 'div' => false, 'label' => false, 'value' => $str_utility->convertToVNDate($this->data['Project']['date_' . $num])))
        );
        //list
        $maps['list_' . $num] = array(
            'label' => __d(sprintf($_domain, 'Details'), 'List ' . $num, true),
            'html' => $this->Form->input('list_' . $num, array('type' => 'select', 'style' => 'margin-right:11px; width:60.6% !important', 'div' => false, 'label' => false, 'options' => $datasets['list_' . $num], 'empty' => __("-- Select -- ", true)))
        );
        //yes/no
        $maps['yn_' . $num] = array(
            'label' => __d(sprintf($_domain, 'Details'), 'Yes/No ' . $num, true),
            'html' => $this->Form->input('yn_' . $num, array('type' => 'select', 'style' => 'margin-right:11px; width:60.6% !important', 'div' => false, 'label' => false, 'options' => array(__('Yes', true), __('No', true))))
        );
        //bool 0/1
        $maps['bool_' . $num] = array(
            'label' => __d(sprintf($_domain, 'Details'), '0/1 ' . $num, true),
            'html' => $this->Form->input('bool_' . $num, array('type' => 'select', 'style' => 'margin-right:11px; width:60.6% !important', 'div' => false, 'label' => false, 'options' => array(0, 1)))
        );
    }
    if( $num <= 6 ){
        //price
        $maps['price_' . $num] = array(
            'label' => __d(sprintf($_domain, 'Details'), 'Price ' . $num, true),
            'html' => $this->Form->input('price_' . $num, array('div' => false, 'class' => 'numeric-value', 'label' => false)) . ' <span style="margin-left: 5px; line-height: 29px">&euro;</span>'
        );
    }
    //number
    $maps['number_' . $num] = array(
        'label' => __d(sprintf($_domain, 'Details'), 'Number ' . $num, true),
        'html' => $this->Form->input('number_' . $num, array('div' => false, 'class' => 'numeric-value', 'label' => false))
    );
}
?>
                        <fieldset>
                            <div class="wd-scroll-form" style="height:auto;">

                                <!-- <div class="wd-right-content">

                                </div> -->
<?php
foreach($translation_data as $data){
    //ignore project details
    if( $data['Translation']['field'] == 'project_details')continue;
    $fieldName = $data['Translation']['field'];
?>
                                <div class="wd-input wd-area wd-none" <?php echo ($data['TranslationSetting']['show'] == 0  || empty($maps[$fieldName]['html']) || empty($maps[$fieldName]['label']))? 'style="display: none"' : '' ?>>
                                    <label><?php echo !empty($maps[$fieldName]['label']) ? $maps[$fieldName]['label'] : ''; ?></label>
                                    <?php echo !empty($maps[$fieldName]['html']) ? $maps[$fieldName]['html'] : ''; ?>
                                </div>
<?php
}
?>
                            </div>
                            <div class="wd-submit">
                                <button type="submit" class="btn-text btn-green" id="btnSave"/>
                                    <img src="<?php echo $html->url('/img/ui/blank-save.png') ?>" alt="" />
                                    <span><?php __('Save') ?></span>
                                </button>
                                <a href="javascript:void(0)" class="btn-text btn-red" onclick="reset_form()" id="reset">
                                    <img src="<?php echo $html->url('/img/ui/blank-reset.png') ?>" alt="" />
                                    <span><?php __('Reset'); ?></span>
                                </a>
                            </div>
                        </fieldset>
                        <?php echo $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $html->script('jshashtable-2.1'); ?>
<?php echo $html->script('jquery.numberformatter-1.2.3'); ?>
<?php echo $html->script('validateDate'); ?>
<?php echo $html->script('jquery.maxlength-min'); ?>
<script type="text/javascript">
    // remove element to array
    jQuery.removeFromArray = function(value, arr) {
        return jQuery.grep(arr, function(elem, index) {
            return elem !== value;
        });
    };
    $("#ProjectCreatedValue").live('blur',function(){
        var number = $(this).val();
        var number = $.formatNumber(number, {format:"#", locale:"us"});
        $(this).val(number);
    })

    $("#ProjectAddForm")[0].reset();

    $("select").attr("disabled","disabled");
    $("#ProjectCompanyId").removeAttr("disabled");

    //$('#ProjectStartDate,#ProjectEndDate,#ProjectPlanedEndDate').datepicker({
    /*
    $('#ProjectPlanedEndDate').datepicker({
        showOn          : 'button',
        buttonImage     : '<?php echo $html->url("/img/front/calendar.gif") ?>',
        buttonImageOnly : true,
        dateFormat      : 'dd-mm-yy'
    });*/

    function removeMessage(){
        $("#flashMessage").hide();
        $('div.error-message').remove();
        $("div.wd-input input, select").removeClass("form-error");
    }

    function showError(element, message) {
        var itemz = $("#"+element);
        var parentElem = itemz.parent();
        parentElem.addClass("error");
        parentElem.append('<div class="error-message">'+message+'</div>');
    }

    //function validateForm(){
    $('#btnSave').click(function(){
        var flag = true,
        flag1 = true;
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

        if (flag1) {
            if ($("#ProjectProjectName").val() =="") {
                var endDate = $("#ProjectProjectName");
                endDate.addClass("form-error");
                var parentElem = endDate.parent();
                parentElem.addClass("error");
                parentElem.append('<div class="error-message" style="padding-left: 130px;">'+"<?php __("The field is not blank.") ?>"+'</div>');
                flag1 = false;
            }
            if($('.wd-combobox').html() == ''){
                var projectManage = $(".wd-combobox");
                projectManage.addClass("form-error");
                var parentElem = projectManage.parent();
                projectManage.addClass("error");
                parentElem.append('<div class="error-message" style="padding-left: 0px !important; margin-left: -1px;">'+"<?php __("The field is not blank.") ?>"+'</div>');
                flag = false;
            }
            if (($("#ProjectProjectPhaseId").val() == "") || ($("#ProjectProjectPhaseId").val() == '--Select--')) {
                var endDate = $("#ProjectProjectPhaseId");
                endDate.addClass("form-error");
                var parentElem = endDate.parent();
                parentElem.addClass("error");
                parentElem.append('<div class="error-message">'+"<?php __("The field is not blank.") ?>"+'</div>');
                flag1 = false;
            }
            if (($("#ProjectProjectManagerId").val() == "") || ($("#ProjectProjectManagerId").val() == '--Select--')) {
                var endDate = $("#ProjectProjectManagerId");
                endDate.addClass("form-error");
                var parentElem = endDate.parent();
                parentElem.addClass("error");
                parentElem.append('<div class="error-message">'+"<?php __("The field is not blank.") ?>"+'</div>');
                flag1 = false;
            }

            //if ($("#ProjectProjectPhaseId").val() == "" || $("#ProjectProjectManagerId").val() == "") flag1 = false;
        }


        if (!(isDate('ProjectEndDate'))) {
            var endDate = $("#ProjectEndDate");
            endDate.addClass("form-error");
            var parentElem = endDate.parent();
            parentElem.addClass("error");
            parentElem.append('<div class="error-message">'+"<?php __("Invalid Date (Valid format is dd-mm-yyyy)") ?>"+'</div>');
            flag1 = flag = false;
        }
       /*if (!(isDate('ProjectPlanedEndDate'))) {
            var endDate = $("#ProjectPlanedEndDate");
            endDate.addClass("form-error");
            var parentElem = endDate.parent();
            parentElem.addClass("error");
            parentElem.append('<div class="error-message">'+"<?php __("Invalid Date (Valid format is dd-mm-yyyy)") ?>"+'</div>');
            flag1 = false;
        }*/
        var isNotEmpty = true;
        //	if (!(isNotEmpty1('ProjectStartDate')))  isNotEmpty = false;
        //	if (!(isNotEmpty1('ProjectBudget')))  isNotEmpty = false;
        //	if (!(isNotEmpty1('ProjectPlanedEndDate')))  isNotEmpty = false;
        if (flag) {
            if(isNotEmpty){
                if (compareDate('ProjectStartDate','ProjectEndDate') > 0 ) {
                    var endDate = $("#ProjectEndDate");
                    endDate.addClass("form-error");
                    var parentElem = endDate.parent();
                    parentElem.addClass("error");
                    parentElem.append('<div class="error-message">'+"<?php __("The end date must be greater than start date.") ?>"+'</div>');
                    flag1 = false;
                }
                /*
                if (compareDate('ProjectStartDate','ProjectPlanedEndDate') > 0 ) {
                    var endDate = $("#ProjectPlanedEndDate");
                    endDate.addClass("form-error");
                    var parentElem = endDate.parent();
                    parentElem.addClass("error");
                    parentElem.append('<div class="error-message">'+"<?php __("The planed end date must be greater than start date.") ?>"+'</div>');
                    flag1 = false;
                }
                */
            }
            else return false;
        }

        return flag1;
    });

    // reset
    function reset_form(){
        $("#ProjectCompanyId").val("");
        if($("#ProjectCompanyId").val()!=""){
            $("select").removeClass("wd-disable").removeAttr("disabled");
            /*    $("#ProjectProjectManagerId").removeAttr('disabled');
                $("#ProjectProjectStatusId").removeAttr('disabled');
                $("#ProjectProjectPriorityId").removeAttr('disabled');
                $("#ProjectProjectPhaseId").removeAttr('disabled');
            $("#ProjectProjectManagerId").removeClass("wd-disable");
            $("#ProjectProjectPhaseId").removeClass("wd-disable");
            $("#ProjectProjectStatusId").removeClass("wd-disable");
            $("#ProjectProjectPriorityId").removeClass("wd-disable"); */
        }else{
            $("select").addClass("wd-disable").attr("disabled", "disabled");
            $("#ProjectCompanyId").removeClass("wd-disable").removeAttr("disabled");
            /*
            $("#ProjectProjectPhaseId").addClass("wd-disable");
            $("#ProjectProjectStatusId").addClass("wd-disable");
            $("#ProjectProjectPriorityId").addClass("wd-disable");
             */
        }
        var id = $("#ProjectCompanyId").val();
        // $.ajax({
        // 	url: '<?php echo $html->url('/projects/get_project_manager/') ?>' + id,
        // 	beforeSend: function() { $("#ProjectProjectManagerId").html("<option>Loading...</option>"); },
        // 	success: function(data) {

        // 		try{
        // 			data = eval("(" + data + ")");
        // 			$.each({
        // 				pm : 'ProjectProjectManagerId',
        // 				tech : 'ProjectTechnicalManagerId',
        // 				project : 'ProjectChiefBusinessId'
        // 			}, function(i,v){
        // 				$("#"+v).html(data[i]).removeClass("wd-disable");
        // 			});
        // 		}catch(e){

        // 		};

        // 	}
        // });

        // $.ajax({
        // 	url: '<?php echo $html->url('/projects/get_phase/') ?>' + id,
        // 	beforeSend: function() { $("#ProjectProjectPhaseId").html("<option>Loading...</option>"); },
        // 	success: function(data) {
        // 		$("#ProjectProjectPhaseId").html(data);
        // 		$("#ProjectProjectPhaseId").removeClass("wd-disable");
        // 	}
        // });

        $.ajax({
            url: '<?php echo $html->url('/projects/get_status/') ?>' + id,
            beforeSend: function() { $("#ProjectProjectStatusId").html("<option>Loading...</option>"); },
            success: function(data) {
                $("#ProjectProjectStatusId").html(data);
                $("#ProjectProjectStatusId").removeClass("wd-disable");
            }
        });

        // $.ajax({
        // 	url: '<?php echo $html->url('/projects/get_priority/') ?>' + id,
        // 	beforeSend: function() { $("#ProjectProjectPriorityId").html("<option>Loading...</option>"); },
        // 	success: function(data) {
        // 		$("#ProjectProjectPriorityId").html(data);
        // 		$("#ProjectProjectPriorityId").removeClass("wd-disable");
        // 	}
        // });

        // $.ajax({
        // 	url: '<?php echo $html->url('/projects/get_complexity/') ?>' + id,
        // 	beforeSend: function() { $("#ProjectComplexityId").html("<option>Loading...</option>"); },
        // 	success: function(data) {
        // 		$("#ProjectComplexityId").html(data);
        // 		$("#ProjectComplexityId").removeClass("wd-disable");
        // 	}
        // });

        // $.ajax({
        // 	url: '<?php echo $html->url('/projects/get_currency/') ?>' + id,
        // 	beforeSend: function() { $("#ProjectCurrencyId").html("<option>Loading...</option>"); },
        // 	success: function(data) {
        // 		$("#ProjectCurrencyId").html(data);
        // 		$("#ProjectCurrencyId").removeClass("wd-disable");
        // 	}
        // });
        // $.ajax({
        // 	url: '<?php echo $html->url('/projects/get_program/') ?>' + id,
        // 	beforeSend: function() { $("#ProjectProjectAmrProgramId").html("<option>Loading...</option>"); },
        // 	success: function(data) {
        // 		$("#ProjectProjectAmrProgramId").html(data);
        // 		$("#ProjectProjectAmrProgramId").removeClass("wd-disable");
        // 		var program_id = $("#ProjectProjectAmrProgramId").val();
        // 		$.ajax({
        // 			url:  '<?php echo $html->url('/project_amrs/get_sub_program/') ?>' + program_id,
        // 			beforeSend: function() { $("#ProjectProjectAmrSubProgramId").html("<option>Loading...</option>"); },
        // 			success: function(data) {
        // 				$("#ProjectProjectAmrSubProgramId").html(data);
        // 				$("#ProjectProjectAmrSubProgramId").removeClass("wd-disable");
        // 			}
        // 		});
        // 	}
        // });

        // $.ajax({
        // 	url: '<?php echo $html->url('/projects/get_project_type/') ?>' + id,
        // 	beforeSend: function() { $("#ProjectProjectTypeId").html("<option value=''>Loading...</option>"); },
        // 	success: function(data) {
        // 		$("#ProjectProjectTypeId").html(data);
        // 		$("#ProjectProjectTypeId").removeClass("wd-disable");
        // 		var project_type_id = $("#ProjectProjectTypeId").val();
        // 		$.ajax({
        // 			url:  '<?php echo $html->url('/projects/get_project_sub_type/') ?>' + project_type_id,
        // 			beforeSend: function() { $("#ProjectProjectSubTypeId").html("<option value=''>Loading...</option>"); },
        // 			success: function(data) {
        // 				$("#ProjectProjectSubTypeId").html(data);
        // 				$("#ProjectProjectSubTypeId").removeClass("wd-disable");
        // 			}
        // 		});
        // 	}
        // });





        $("#ProjectProjectManagerId").val("");
        $("#ProjectComplexityId").val("");
        $("#ProjectProjectPhaseId").val("");
        $("#ProjectProjectPriorityId").val("");
        $("#ProjectProjectStatusId").val("");
        $("input, textarea").val('');
        $("#flashMessage").hide();
        $(".error-message").hide();
        $("div.wd-input,input,select").removeClass("form-error");
    }


    if($("#ProjectCompanyId").val()!=""){
        $("#ProjectProjectManagerId").removeClass("wd-disable");
        $("#ProjectProjectPhaseId").removeClass("wd-disable");
        $("#ProjectProjectStatusId").removeClass("wd-disable");
        $("#ProjectProjectPriorityId").removeClass("wd-disable");
        $("#ProjectComplexityId").removeClass("wd-disable");
    }else{
        $("#ProjectProjectManagerId").addClass("wd-disable");
        $("#ProjectProjectPhaseId").addClass("wd-disable");
        $("#ProjectProjectStatusId").addClass("wd-disable");
        $("#ProjectProjectPriorityId").addClass("wd-disable");
        $("#ProjectComplexityId").removeClass("wd-disable");
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
    if($("#ProjectCompanyId").val() != ""){
        $("select").removeClass("wd-disable").removeAttr("disabled");
        var id = $("#ProjectCompanyId").val();
        $.ajax({
            url: '<?php echo $html->url('/projects/get_project_manager/') ?>' + id,
            beforeSend: function() { $("#ProjectProjectManagerId").html("<option>Loading...</option>"); },
            success: function(data) {

                try{
                    data = eval("(" + data + ")");
                    $.each({
                        pm : 'ProjectProjectManagerId',
                        tech : 'ProjectTechnicalManagerId',
                        project : 'ProjectChiefBusinessId'
                    }, function(i,v){
                        $("#"+v).html(data[i]).removeClass("wd-disable");
                    });
                }catch(e){

                };


            }
        });

        $.ajax({
            url: '<?php echo $html->url('/projects/get_phase/') ?>' + id,
            beforeSend: function() { $("#ProjectProjectPhaseId").html("<option>Loading...</option>"); },
            success: function(data) {
                $("#ProjectProjectPhaseId").html(data);
                $("#ProjectProjectPhaseId").removeClass("wd-disable");
            }
        });

        $.ajax({
            url: '<?php echo $html->url('/projects/get_status/') ?>' + id,
            beforeSend: function() { $("#ProjectProjectStatusId").html("<option>Loading...</option>"); },
            success: function(data) {
                $("#ProjectProjectStatusId").html(data);
                $("#ProjectProjectStatusId").removeClass("wd-disable");
            }
        });

        $.ajax({
            url: '<?php echo $html->url('/projects/get_priority/') ?>' + id,
            beforeSend: function() { $("#ProjectProjectPriorityId").html("<option>Loading...</option>"); },
            success: function(data) {
                $("#ProjectProjectPriorityId").html(data);
                $("#ProjectProjectPriorityId").removeClass("wd-disable");
            }
        });

        $.ajax({
            url: '<?php echo $html->url('/projects/get_complexity/') ?>' + id,
            beforeSend: function() { $("#ProjectComplexityId").html("<option>Loading...</option>"); },
            success: function(data) {
                $("#ProjectComplexityId").html(data);
                $("#ProjectComplexityId").removeClass("wd-disable");
            }
        });

        $.ajax({
            url: '<?php echo $html->url('/projects/get_currency/') ?>' + id,
            beforeSend: function() { $("#ProjectCurrencyId").html("<option>Loading...</option>"); },
            success: function(data) {
                $("#ProjectCurrencyId").html(data);
                $("#ProjectCurrencyId").removeClass("wd-disable");
            }
        });
        $.ajax({
            url: '<?php echo $html->url('/projects/get_program/') ?>' + id,
            beforeSend: function() { $("#ProjectProjectAmrProgramId").html("<option>Loading...</option>"); },
            success: function(data) {
                $("#ProjectProjectAmrProgramId").html(data);
                $("#ProjectProjectAmrProgramId").removeClass("wd-disable");
                var program_id = $("#ProjectProjectAmrProgramId").val();
                $.ajax({
                    url:  '<?php echo $html->url('/project_amrs/get_sub_program/') ?>' + program_id,
                    beforeSend: function() { $("#ProjectProjectAmrSubProgramId").html("<option>Loading...</option>"); },
                    success: function(data) {
                        $("#ProjectProjectAmrSubProgramId").html(data);
                        $("#ProjectProjectAmrSubProgramId").removeClass("wd-disable");
                    }
                });
            }
        });

        $.ajax({
            url: '<?php echo $html->url('/projects/get_project_type/') ?>' + id,
            beforeSend: function() { $("#ProjectProjectTypeId").html("<option value=''>Loading...</option>"); },
            success: function(data) {
                $("#ProjectProjectTypeId").html(data);
                $("#ProjectProjectTypeId").removeClass("wd-disable");
                var project_type_id = $("#ProjectProjectTypeId").val();
                $.ajax({
                    url:  '<?php echo $html->url('/projects/get_project_sub_type/') ?>' + project_type_id,
                    beforeSend: function() { $("#ProjectProjectSubTypeId").html("<option value=''>Loading...</option>"); },
                    success: function(data) {
                        $("#ProjectProjectSubTypeId").html(data);
                        $("#ProjectProjectSubTypeId").removeClass("wd-disable");
                    }
                });
            }
        });
    }else {
        $("select").addClass("wd-disable").attr("disabled", "disabled");
        $("#ProjectCompanyId").removeClass("wd-disable").removeAttr("disabled");
    }
    $("#ProjectCompanyId").change(function(){
        if($("#ProjectCompanyId").val()!=""){
            $("select").removeClass("wd-disable").removeAttr("disabled");
            /*    $("#ProjectProjectManagerId").removeAttr('disabled');
                $("#ProjectProjectStatusId").removeAttr('disabled');
                $("#ProjectProjectPriorityId").removeAttr('disabled');
                $("#ProjectProjectPhaseId").removeAttr('disabled');
            $("#ProjectProjectManagerId").removeClass("wd-disable");
            $("#ProjectProjectPhaseId").removeClass("wd-disable");
            $("#ProjectProjectStatusId").removeClass("wd-disable");
            $("#ProjectProjectPriorityId").removeClass("wd-disable"); */
        }else{
            $("select").addClass("wd-disable").attr("disabled", "disabled");
            $("#ProjectCompanyId").removeClass("wd-disable").removeAttr("disabled");
            /*
            $("#ProjectProjectPhaseId").addClass("wd-disable");
            $("#ProjectProjectStatusId").addClass("wd-disable");
            $("#ProjectProjectPriorityId").addClass("wd-disable");
             */
        }
        var id = $(this).val();
        $.ajax({
            url: '<?php echo $html->url('/projects/get_project_manager/') ?>' + id,
            beforeSend: function() { $("#ProjectProjectManagerId").html("<option>Loading...</option>"); },
            success: function(data) {

                try{
                    data = eval("(" + data + ")");
                    $.each({
                        pm : 'ProjectProjectManagerId',
                        tech : 'ProjectTechnicalManagerId',
                        project : 'ProjectChiefBusinessId'
                    }, function(i,v){
                        $("#"+v).html(data[i]).removeClass("wd-disable");
                    });
                }catch(e){

                };

            }
        });

        $.ajax({
            url: '<?php echo $html->url('/projects/get_phase/') ?>' + id,
            beforeSend: function() { $("#ProjectProjectPhaseId").html("<option>Loading...</option>"); },
            success: function(data) {
                $("#ProjectProjectPhaseId").html(data);
                $("#ProjectProjectPhaseId").removeClass("wd-disable");
            }
        });

        $.ajax({
            url: '<?php echo $html->url('/projects/get_status/') ?>' + id,
            beforeSend: function() { $("#ProjectProjectStatusId").html("<option>Loading...</option>"); },
            success: function(data) {
                $("#ProjectProjectStatusId").html(data);
                $("#ProjectProjectStatusId").removeClass("wd-disable");
            }
        });

        $.ajax({
            url: '<?php echo $html->url('/projects/get_priority/') ?>' + id,
            beforeSend: function() { $("#ProjectProjectPriorityId").html("<option>Loading...</option>"); },
            success: function(data) {
                $("#ProjectProjectPriorityId").html(data);
                $("#ProjectProjectPriorityId").removeClass("wd-disable");
            }
        });

        $.ajax({
            url: '<?php echo $html->url('/projects/get_complexity/') ?>' + id,
            beforeSend: function() { $("#ProjectComplexityId").html("<option>Loading...</option>"); },
            success: function(data) {
                $("#ProjectComplexityId").html(data);
                $("#ProjectComplexityId").removeClass("wd-disable");
            }
        });

        $.ajax({
            url: '<?php echo $html->url('/projects/get_currency/') ?>' + id,
            beforeSend: function() { $("#ProjectCurrencyId").html("<option>Loading...</option>"); },
            success: function(data) {
                $("#ProjectCurrencyId").html(data);
                $("#ProjectCurrencyId").removeClass("wd-disable");
            }
        });
        $.ajax({
            url: '<?php echo $html->url('/projects/get_program/') ?>' + id,
            beforeSend: function() { $("#ProjectProjectAmrProgramId").html("<option>Loading...</option>"); },
            success: function(data) {
                $("#ProjectProjectAmrProgramId").html(data);
                $("#ProjectProjectAmrProgramId").removeClass("wd-disable");
                var program_id = $("#ProjectProjectAmrProgramId").val();
                $.ajax({
                    url:  '<?php echo $html->url('/project_amrs/get_sub_program/') ?>' + program_id,
                    beforeSend: function() { $("#ProjectProjectAmrSubProgramId").html("<option>Loading...</option>"); },
                    success: function(data) {
                        $("#ProjectProjectAmrSubProgramId").html(data);
                        $("#ProjectProjectAmrSubProgramId").removeClass("wd-disable");
                    }
                });
            }
        });

        $.ajax({
            url: '<?php echo $html->url('/projects/get_project_type/') ?>' + id,
            beforeSend: function() { $("#ProjectProjectTypeId").html("<option value=''>Loading...</option>"); },
            success: function(data) {
                $("#ProjectProjectTypeId").html(data);
                $("#ProjectProjectTypeId").removeClass("wd-disable");
                var project_type_id = $("#ProjectProjectTypeId").val();
                $.ajax({
                    url:  '<?php echo $html->url('/projects/get_project_sub_type/') ?>' + project_type_id,
                    beforeSend: function() { $("#ProjectProjectSubTypeId").html("<option value=''>Loading...</option>"); },
                    success: function(data) {
                        $("#ProjectProjectSubTypeId").html(data);
                        $("#ProjectProjectSubTypeId").removeClass("wd-disable");
                    }
                });
            }
        });
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

    $(document).ready(function(){
        $('.wd-date').datepicker({
            dateFormat: 'dd-mm-yy'
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
            projectEmployeeManagerDatas = <?php echo !empty($listEmployeeManagers['PM']) ? json_encode($listEmployeeManagers['PM']) : json_encode(array());?>,
            chiefBusinessDatas = <?php echo !empty($listEmployeeManagers['CB']) ? json_encode($listEmployeeManagers['CB']) : json_encode(array());?>,
            technicalManagerDatas = <?php echo !empty($listEmployeeManagers['TM']) ? json_encode($listEmployeeManagers['TM']) : json_encode(array());?>,
            functionalLeaderDatas = <?php echo !empty($listEmployeeManagers['FL']) ? json_encode($listEmployeeManagers['FL']) : json_encode(array());?>,
            uatManagerDatas = <?php echo !empty($listEmployeeManagers['UM']) ? json_encode($listEmployeeManagers['UM']) : json_encode(array());?>,
            readAccessDatas = <?php echo !empty($listEmployeeManagers['RA']) ? json_encode($listEmployeeManagers['RA']) : json_encode(array());?>;
        var initMenuFilter = function($menu, $check){
            if($check === 'PM'){
                var $filter = $('<div class="context-menu-filter"><span><input type="text" rel="no-history"></span></div>');
            } else if($check === 'CB') {
                var $filter = $('<div class="context-menu-filter-2"><span><input type="text" rel="no-history"></span></div>');
            } else if($check === 'TM') {
                var $filter = $('<div class="context-menu-filter-3"><span><input type="text" rel="no-history"></span></div>');
            } else if($check === 'FL') {
                var $filter = $('<div class="context-menu-filter-4"><span><input type="text" rel="no-history"></span></div>');
            } else if($check === 'UM'){
                var $filter = $('<div class="context-menu-filter-5"><span><input type="text" rel="no-history"></span></div>');
            } else if($check === 'RA'){
                var $filter = $('<div class="context-menu-filter-6"><span><input type="text" rel="no-history"></span></div>');
            } else {
                var $filter = $('<div class="context-menu-filter-7"><span><input type="text" rel="no-history"></span></div>');
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
        initMenuFilter($('#wd-data-project'), 'PM');
        initMenuFilter($('#wd-data-project-2'), 'CB');
        initMenuFilter($('#wd-data-project-3'), 'TM');
        initMenuFilter($('#wd-data-project-4'), 'FL');
        initMenuFilter($('#wd-data-project-5'), 'UM');
        initMenuFilter($('#wd-data-project-6'), 'RA');
        initMenuFilter($('#wd-data-project-7'), 'CR');
        $('.context-menu-filter, .context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7').css('display', 'none');
        //$('.context-menu-filter').css('display', 'none');
        $('.wd-combobox').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5,#wd-data-project-6,#wd-data-project-7').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7').css('display', 'none');
            $('.wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7').removeAttr('checked');
            if(checked){
                $('#wd-data-project').css('display', 'none');
                $(this).removeAttr('checked');
                $('.context-menu-filter').css('display', 'none');
            } else {
                $('#wd-data-project').css('display', 'block');
                $(this).attr('checked', 'checked');
                $('.context-menu-filter').css({
                    'display': 'block',
                    'position': 'absolute',
                    'width': '26.3%',
                    'z-index': 2
                });
                $('#wd-data-project div:first-child').css('padding-top', '20px');
            }
            return false;
        });
        $('.wd-combobox-2').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7').css('display', 'none');
            $('.context-menu-filter, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7').css('display', 'none');
            $('.wd-combobox, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7').removeAttr('checked');
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
                    'width': '26.3%',
                    'z-index': 2
                });
                $('#wd-data-project-2 div:first-child').css('padding-top', '20px');
            }
            return false;
        });
        $('.wd-combobox-3').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project-2, #wd-data-project, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7').css('display', 'none');
            $('.wd-combobox-2, .wd-combobox, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7').removeAttr('checked');
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
                    'width': '26.3%',
                    'z-index': 2
                });
                $('#wd-data-project-3 div:first-child').css('padding-top', '20px');
            }
            return false;
        });
        $('.wd-combobox-4').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project-2, #wd-data-project, #wd-data-project-3, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter, .context-menu-filter-3, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7').css('display', 'none');
            $('.wd-combobox-2, .wd-combobox, .wd-combobox-3, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7').removeAttr('checked');
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
                    'width': '26.3%',
                    'z-index': 2
                });
                $('#wd-data-project-4 div:first-child').css('padding-top', '20px');
            }
            return false;
        });
        $('.wd-combobox-5').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project-2, #wd-data-project, #wd-data-project-3, #wd-data-project-4, #wd-data-project-6, #wd-data-project-7').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-6, .context-menu-filter-7').css('display', 'none');
            $('.wd-combobox-2, .wd-combobox, .wd-combobox-3, .wd-combobox-4, .wd-combobox-6, .wd-combobox-7').removeAttr('checked');
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
                    'width': '26.3%',
                    'z-index': 2
                });
                $('#wd-data-project-5 div:first-child').css('padding-top', '20px');
            }
            return false;
        });
        $('.wd-combobox-6').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-7').css('display', 'none');
            $('.context-menu-filter, .context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-7').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-7').removeAttr('checked');
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
                    'width': '26.3%',
                    'z-index': 2
                });
                $('#wd-data-project-6 div:first-child').css('padding-top', '20px');
            }
            return false;
        });
        $('.wd-combobox-7').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6').css('display', 'none');
            $('.context-menu-filter, .context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6').removeAttr('checked');
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
                    'width': '26.3%',
                    'z-index': 2
                });
                $('#wd-data-project-7 div:first-child').css('padding-top', '20px');
            }
            return false;
        });
        $('html').click(function(e){
            if($(e.target).attr('class') &&
            (
                ( $(e.target).attr('class').split(' ')[0] &&
                    (
                        $(e.target).attr('class').split(' ')[0] == 'projectManager' || $(e.target).attr('class').split(' ')[0] == 'chiefBusiness' || $(e.target).attr('class').split(' ')[0] == 'technicalManager' || $(e.target).attr('class').split(' ')[0] == 'functionalLeader' || $(e.target).attr('class').split(' ')[0] == 'uatManager' || $(e.target).attr('class').split(' ')[0] == 'readAccess' || $(e.target).attr('class').split(' ')[0] == 'currentPhase'
                    )
                ) ||
            $(e.target).attr('class') == 'context-menu-filter' ||
            $(e.target).attr('class') == 'context-menu-filter-2' ||
            $(e.target).attr('class') == 'context-menu-filter-3' ||
            $(e.target).attr('class') == 'context-menu-filter-4' ||
            $(e.target).attr('class') == 'context-menu-filter-5' ||
            $(e.target).attr('class') == 'context-menu-filter-6' ||
            $(e.target).attr('class') == 'context-menu-filter-7'
            )){
                //do nothing
            } else {
                $('.context-menu-filter, .context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7').css('display', 'none');
                $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7').css('display', 'none');
                $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7').removeAttr('checked');
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
         * Phan chon cac phan tu trong combobox cua projectEmployeeManager
         */
        var $ids = [];
        projectEmployeeManager.each(function(){
            var data = $(this).find('.wd-data');
            var backup = $(this).find('.wd-backup');
            /**
             * When load data
             */
            var valList = $(data).find('#ProjectProjectEmployeeManager').val();
            var valListBackup = $(backup).find('#ProjectIsBackup').val();
            if(projectEmployeeManagerDatas){
                $.each(projectEmployeeManagerDatas, function(employId, isBackup){
                    isBackup = (isBackup == 1) ? employId : 0;
                    if(valList == employId){
                        $(data).find('#ProjectProjectEmployeeManager').attr('checked', 'checked');
                        $(backup).find('#ProjectIsBackup').removeAttr('disabled');
                        $('a.wd-combobox').append('<span class="wd-dt-'+valList+'">' + $('.wd-group-' + valList).find('span').html() + '<span class="wd-bk-'+valList+'"></span></span><span class="wd-em-'+valList+'">, </span>');
                    }
                   
                    $ids.push(employId);
                });
            }
            /**
             * When click in checkbox
             */
            $(data).find('#ProjectProjectEmployeeManager').click(function(){
                var _datas = $(this).val();
                if($(this).is(':checked')){
                    $(backup).find('#ProjectIsBackup').removeAttr('disabled');
                    $ids.push(_datas);
                    $('a.wd-combobox').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                } else {
                    $ids = jQuery.removeFromArray(_datas, $ids);
                    $(backup).find('#ProjectIsBackup').attr('disabled', 'disabled');
                    $('a.wd-combobox').find('.wd-dt-' +_datas).remove();
                    $('a.wd-combobox').find('.wd-em-' +_datas).remove();
                    $(backup).find('#ProjectIsBackup').removeAttr('checked');
                }
            });
        });

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
        currentPhase.each(function(){
            var data = $(this).find('.wd-data');
            var backup = $(this).find('.wd-backup');
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
        $(document).ready(function(){
        var edit = false;
        $("#onChangeCode").focus(function(){
            edit = false;
            $("#btnSave").hide();
        });
        $("#onChangeCode").keypress(function(){
            edit = true;
        });
        $("#onChangeCode").one('paste', function(){
            edit = true;
        });
        $("#onChangeCode").blur(function(){
            if(edit == false){
                $("#btnSave").show();
            }
        });
        var codeExisted = <?php echo json_encode(__('Already used in project', true));?>;
        $("#onChangeCode").on('change', function(){
                // goi len controll de kiem tra xem code 1 da ton tai
                if($('#onChangeCode').val() != ''){
                    $('#valueOnChange').html("<img src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt='Loading' />");
                    $("#valueOnChange").show();
                    $.ajax({
                      url: '<?php echo $html->url(array('action' => 'checkCodeAdd1')); ?>',
                      type: 'POST',
                      data: {
                          data: {
                              code: $('#onChangeCode').val()
                          }
                      },
                      success: function(project_name) {
                         if( project_name ){
                             $("#btnSave").hide();
                             $('#valueOnChange').css('color', 'red');
                             $('#valueOnChange').html(codeExisted + ' ' + '<b>' + project_name + '</b>');
                         } else {
                            $("#valueOnChange").hide();
                            $("#btnSave").show();
                         }
                      }
                   });
               } else {
                    $("#btnSave").show();
                    $("#valueOnChange").hide();
               }
            });
        });
</script>
