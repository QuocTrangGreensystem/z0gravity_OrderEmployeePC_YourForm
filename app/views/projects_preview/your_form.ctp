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
    'preview/global-views',
    'preview/your-form.css?ver=1.3'
));
?>
<style>
    .content-right-inner {
        background-color: transparent;
    }

    .loading-mark {
        position: relative;
    }

    .loading-mark:after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        background: rgba(255, 255, 255, 0.75) url(/img/business/wait-1.gif) center no-repeat;
        background-size: 40px;
        z-index: 0;
        opacity: 0;
        transition: all 0.2s ease;
        visibility: hidden;
    }

    .loading-mark.loading:after {
        z-index: 20;
        opacity: 1;
        visibility: visible;
    }

    fieldset .next-line div.wd-input .multiselect-pm {
        width: 100%;
    }

    fieldset .next-line div.wd-input {
        width: 100%;
    }

    fieldset .next-line div.wd-input>input {
        width: inherit;
    }

    fieldset .next-line div.wd-input select {
        width: inherit;
    }

    #valueOnChange {
        font-size: 13px;
        margin-top: 10px;
    }

    #chart-wrapper .wd-input .wd-multiselect .wd-combobox .circle-name {
        width: 30px;
        height: 30px;
        vertical-align: middle;
    }

    select#ProjectActivated {
        background: none;
        background-color: #f1f6fa;
    }

    .check-project {
        line-height: 24px;
        display: block;
        color: red;
        display: none;
    }

    .wd-multiselect .actif-0 {
        display: none !important;
    }
</style>
<?php
function multiSelect($_this, $fieldName, $fielData, $_data, $textHolder, $pc = array())
{
    // $checkAvatar = $_this->viewVars['checkAvatar'];
    $_data = !empty($_data) ? $_data : array();
    $cotentField = '';
    $avatar = '';
    $cotentField = '<div class="wd-multiselect multiselect multiselect-pm" >
        <div class="wd-combobox wd-project-manager">';

    if (!empty($_data)) {
        $order_employee = array();
        foreach ($fielData as $id => $_val) :
            if (array_key_exists($id, $_data)) {
                $order_employee[$id] = $_data[$id];
            }
        endforeach;
        foreach ($order_employee as $idPm => $is_pc) :
                if ($is_pc == 0 && !empty($fielData[$idPm])) {
                    $cotentField .= $_this->UserFile->avatar_html($idPm);
                }
        endforeach;
        $order_pc = array();
        if(!empty($pc)){
            foreach ($pc as $id => $_val) :
                if (array_key_exists($id, $_data)) {
                    $order_pc[$id] = $_data[$id];
                }
            endforeach;
            foreach ($order_pc as $idPC => $is_pc) :
                    if ($is_pc == 1 && !empty($pc[$idPC])) {
                        $cotentField .= '<a class="circle-name" title="PC / ' . $pc[$idPC] . '"><span data-id="' . $idPC . '-1"><i class="icon-people"></i></span></a>';
                    }
            endforeach;
            
        }
        // foreach ($_data as $idPC => $is_pc) :
        //     if ($is_pc && isset($pc[$idPC])) {
        //         $cotentField .= '<a class="circle-name" title="PC / ' . $pc[$idPC] . '"><span data-id="' . $idPC . '-1"><i class="icon-people"></i></span></a>';
        //     }
        // endforeach;
        $cotentField .= '<p style="position: absolute; color: #c6cccf; display: none">' . $textHolder . '</p>';
    } else {
        $cotentField .= '<p style="position: absolute; color: #c6cccf">' . $textHolder . '</p>';
    }

    $cotentField .= '</div><div class="wd-combobox-content ' . $fieldName . '" style="display: none;">
        <div class="context-menu-filter"><span><input type="text" class="wd-input-search" placeholder="' . __('Search', true) . '" rel="no-history"></span></div><div class="option-content">';
    foreach ($fielData as $idPm => $value) :
        $cotentField .= '<div class="projectManager wd-data-manager wd-group-' . $idPm . ' actif-' . $value['actif'] . '">
                <p class="projectManager wd-data">' .
            $_this->Form->input($fieldName, array(
                'label' => false,
                'div' => false,
                'type' => 'checkbox',
                'name' => 'data[' . $fieldName . '][]',
                'value' => $idPm,
                'id' => $fieldName . '-' . $idPm,
                'checked' => array_key_exists($idPm, $_data) ? 'checked' : ''

            )) . '
                    <span class="option-name" style="padding-left: 5px;">' . $value['full_name'] . '</span>
                </p>
            </div>';
    endforeach;
    if (!empty($pc)) :
        foreach ($pc as $idPm => $namePm) :
            $cotentField .= '<div class="projectManager wd-data-manager wd-group-' . $idPm . '">
                    <p class="projectManager wd-data">' .
                $_this->Form->input($fieldName, array(
                    'label' => false,
                    'div' => false,
                    'type' => 'checkbox',
                    'name' => 'data[' . $fieldName . '][]',
                    'checked' => array_key_exists($idPm, $_data) ? 'checked' : '',
                    'id' => $fieldName . '-' . $idPm .  '-1',
                    'value' => $idPm . '-1'
                )) . '
                        <span class="option-name" style="padding-left: 5px;">PC / ' . $namePm . '</span>
                    </p>
                </div>';
        endforeach;
    endif;
    $cotentField .= '</div></div></div>';
    return $cotentField;
}
$list_selects = array('budgetCustomers', 'ProjectArmPrograms', 'Priorities', 'Complexities', 'Statuses', 'ProjectTypes', 'ProjectSubTypes', 'projectSubSubTypes', 'ProjectArmSubPrograms', 'ProjectTypes', 'ProjectTypes', 'ProjectTypes', 'families', 'subFamilies', 'ProjectPhases', 'ProjectPhases');
foreach ($list_selects as $k) {
    if (!empty($$k)) natcasesort($$k);
}
$listEmployeeManagers = array_merge(array(
    'PM' => array(),
    'RA' => array(),
    'TM' => array(),
    'CB' => array(),
    'UM' => array(),
    'FL' => array(),
), $listEmployeeManagers);

asort($_employees['pm']);
asort($profitCenters);
// debug($listEmployeeManagers['PM']);
// array_multisort($_employees['pm'], $listEmployeeManagers['PM']);
// debug($listEmployeeManagers['PM']);exit;
$fieldProjecManager = multiSelect($this, 'project_employee_manager', $_employees['pm'], $listEmployeeManagers['PM'], __d(sprintf($_domain, 'Details'), 'Project Manager', true));
$fieldReadAccess = multiSelect($this, 'read_access', $_employees['pm'], $listEmployeeManagers['RA'], __d(sprintf($_domain, 'Details'), 'Read Access', true), $profitCenters);

$techTemplate = multiSelect($this, 'technical_manager_list', $_employees['pm'], $listEmployeeManagers['TM'], __d(sprintf($_domain, 'Details'), 'Technical manager', true));
$chiefTemplate = multiSelect($this, 'chief_business_list', $_employees['pm'], $listEmployeeManagers['CB'], __d(sprintf($_domain, 'Details'), 'Chief Business', true));
$uatTemplate = multiSelect($this, 'uat_manager_list', $_employees['pm'], $listEmployeeManagers['UM'], __d(sprintf($_domain, 'Details'), 'UAT manager', true));
$leaderTemplate = multiSelect($this, 'functional_leader_list', $_employees['pm'], $listEmployeeManagers['FL'], __d(sprintf($_domain, 'Details'), 'Functional leader', true));
?>

<div id="wd-container-main" class="wd-project-detail">

    <div class="wd-layout">
        <div class="wd-main-content wd-content-yourform loading-mark">
            <?php if (!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1)
                echo $this->element("secondary_menu_preview");
            ?>

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
                        if ($this->data['Project']['category'] != 1) {
                            echo $this->Form->hidden('activated');
                        }
                        ?>
                        <?php
                        $cuPhaseTemplate = '<div class="multiselect" onchange="saveFieldMutiSelectYourForm(this)" style="margin-right: 3px;">
    <a href="javascript:void();" class="wd-combobox-7"></a>
    <div id="wd-data-project-7" style="display: none;" class="currentPhase list_multiselect">';
                        // asort($ProjectPhases);
                        foreach ($ProjectPhases as $idPm => $namePm) :
                            $cuPhaseTemplate .= '<div class="currentPhase wd-data-manager wd-group-' . $idPm . '">
        <p class="currentPhase wd-data" style="margin: 10px 5px;">' .
                                $this->Form->input('project_phase_id', array(
                                    'label' => false,
                                    'div' => false,
                                    'type' => 'checkbox',
                                    'class' => 'currentPhase',
                                    'id' => 'currentPhase' . $idPm,
                                    'name' => 'data[project_phase_id][]',
                                    'value' => $idPm
                                )) .
                                '<span class="currentPhase" style="padding-left: 5px;">' . $namePm . '</span>
        </p>
    </div>';
                        endforeach;
                        $cuPhaseTemplate .= '</div></div>';
                        if (!empty($projectPhasePlans)) {
                            $_start_date = $projectPhasePlans[0][0]['MinStartDate'];
                            $_end_date = $projectPhasePlans[0][0]['MaxEndDate'];
                        }
                        $_start_date = isset($_start_date) ? $_start_date : null;
                        $startTemplate = $this->Form->input('start_date', array(
                            'div' => false,
                            'label' => false,
                            'style' => '',
                            'disabled' => 'disabled',
                            'value' => $str_utility->convertToVNDate($_start_date),
                            'type' => 'text',
                            'class' => 'wd-date',
                            // 'onchange' => 'saveFieldYourForm(this)',
                        ));
                        $maps = array(
                            'project_amr_program_id' => array(
                                'label' => __d(sprintf($_domain, 'Details'), 'Program', true),
                                'html' => $this->Form->input('project_amr_program_id', array(
                                    'div' => false, 'label' => false,
                                    "empty" => __("--Select--", true),
                                    "options" => $ProjectArmPrograms,
                                    'onchange' => 'saveFieldYourForm(this)'
                                )),
                                'position' => 'top',
                            ),
                            'project_type_id' => array(
                                'label' => __d(sprintf($_domain, 'Details'), 'Project type', true),
                                'html' => $this->Form->input('project_type_id', array(
                                    'div' => false, 'label' => false,
                                    "empty" => __("--Select--", true),
                                    "options" => $ProjectTypes,
                                    'onchange' => 'saveFieldYourForm(this)'
                                ))
                            ),
                            'project_name' => array(
                                'label' => __d(sprintf($_domain, 'Details'), "Project Name", true),
                                'html' => $this->Form->input('project_name', array('div' => false, 'onchange' => 'saveFieldYourForm(this)', 'label' => false, 'maxlength' => 124, 'style' => 'border-color: red')) . '<p class="check-project">' . __('The project already exists', true) . '</p>',
                                'position' => 'top',
                            ),
                            'company_id' => array(
                                'label' => __d(sprintf($_domain, 'Details'), 'Company', true),
                                'html' => $this->Form->input('company_name', array('div' => false, 'label' => false, 'maxlength' => 124, 'value' => $name_company, 'disabled' => true, 'readonly' => 'readonly'))
                            ),
                            'long_project_name' => array(
                                'label' => __d(sprintf($_domain, 'Details'), 'Project long name', true),
                                'html' => $this->Form->input('long_project_name', array('div' => false, 'onfocusout' => 'saveFieldYourForm(this)', 'label' => false))
                            ),
                            'project_code_1' => array(
                                'label' => __d(sprintf($_domain, 'Details'), 'Project Code 1', true),
                                'html' => $this->Form->input('project_code_1', array('div' => false, 'label' => false, 'id' => 'onChangeCode')) . '<span style="display: none; float:left; color: #000; width: 62%" id= "valueOnChange"></span>'
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
                                'html' => $this->Form->input('project_sub_type_id', array(
                                    'div' => false, 'label' => false,
                                    'empty' => __("--Select--", true),
                                    "options" => $ProjectSubTypes,
                                    'onchange' => 'saveFieldYourForm(this)'
                                ))
                            ),
                            'project_sub_sub_type_id' => array(
                                'label' => __d(sprintf($_domain, 'Details'), 'Sub sub type', true),
                                'html' => $this->Form->input('project_sub_sub_type_id', array(
                                    'div' => false, 'label' => false,
                                    'empty' => __("--Select--", true),
                                    "options" => $projectSubSubTypes,
                                    'onchange' => 'saveFieldYourForm(this)'
                                ))
                            ),
                            'project_amr_sub_program_id' => array(
                                'label' => __d(sprintf($_domain, 'Details'), 'Sub program', true),
                                'html' => $this->Form->input('project_amr_sub_program_id', array(
                                    'div' => false, 'label' => false,
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
                            'project_status_id' => array(
                                'label' => __d(sprintf($_domain, 'Details'), 'Status', true),
                                'html' => $this->Form->input('project_status_id', array('div' => false, 'onchange' => 'saveFieldYourForm(this)', 'label' => false, "options" => $Statuses, 'empty' => __("--Select--", true))),
                                // 'position' => 'top', //Position dung de xac dinh field nao hien thi o block 1.
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
                                'class' => 'wd-input-text display-block'
                            ),
                            'primary_objectives' => array(
                                'label' => __d(sprintf($_domain, 'Details'), 'Primary Objectives', true),
                                'html' => $this->Form->input('primary_objectives', array('type' => 'textarea', 'onfocusout' => 'saveFieldYourForm(this)', 'div' => false, 'label' => false)),
                                'class' => 'wd-input-text display-block'
                            ),
                            'project_objectives' => array(
                                'label' => __d(sprintf($_domain, 'Details'), 'Project Objectives', true),
                                'html' => $this->Form->input('project_objectives', array('type' => 'textarea', 'onfocusout' => 'saveFieldYourForm(this)', 'div' => false, 'label' => false)),
                                'class' => 'wd-input-text display-block'
                            ),
                            'constraint' => array(
                                'label' => __d(sprintf($_domain, 'Details'), 'Constraint', true),
                                'html' => $this->Form->input('constraint', array('type' => 'textarea', 'onfocusout' => 'saveFieldYourForm(this)', 'div' => false, 'label' => false)),
                                'class' => 'wd-input-text display-block'
                            ),
                            'remark' => array(
                                'label' => __d(sprintf($_domain, 'Details'), 'Remark', true),
                                'html' => $this->Form->input('remark', array('type' => 'textarea', 'onfocusout' => 'saveFieldYourForm(this)', 'div' => false, 'label' => false)),
                                'class' => 'wd-input-text display-block'
                            ),
                            'free_1' => array(
                                'label' => __d(sprintf($_domain, 'Details'), 'Free 1', true),
                                'html' => $this->Form->input('free_1', array('type' => 'textarea', 'onfocusout' => 'saveFieldYourForm(this)', 'class' => 'resizeOnFocus', 'div' => false, 'label' => false)),
                                'class' => 'wd-input-text display-block'
                            ),
                            'free_2' => array(
                                'label' => __d(sprintf($_domain, 'Details'), 'Free 2', true),
                                'html' => $this->Form->input('free_2', array('type' => 'textarea', 'onfocusout' => 'saveFieldYourForm(this)', 'class' => 'resizeOnFocus', 'div' => false, 'label' => false)),
                                'class' => 'wd-input-text display-block'
                            ),
                            'free_3' => array(
                                'label' => __d(sprintf($_domain, 'Details'), 'Free 3', true),
                                'html' => $this->Form->input('free_3', array('type' => 'textarea', 'onfocusout' => 'saveFieldYourForm(this)', 'class' => 'resizeOnFocus', 'div' => false, 'label' => false)),
                                'class' => 'wd-input-text display-block'
                            ),
                            'free_4' => array(
                                'label' => __d(sprintf($_domain, 'Details'), 'Free 4', true),
                                'html' => $this->Form->input('free_4', array('type' => 'textarea', 'onfocusout' => 'saveFieldYourForm(this)', 'class' => 'resizeOnFocus', 'div' => false, 'label' => false)),
                                'class' => 'wd-input-text display-block'
                            ),
                            'free_5' => array(
                                'label' => __d(sprintf($_domain, 'Details'), 'Free 5', true),
                                'html' => $this->Form->input('free_5', array('type' => 'textarea', 'onfocusout' => 'saveFieldYourForm(this)', 'class' => 'resizeOnFocus', 'div' => false, 'label' => false)),
                                'class' => 'wd-input-text display-block'
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
                                // 'position' => 'top', //Position dung de xac dinh field nao hien thi o block 1.
                            ),
                            'start_date' => array(
                                'label' => __d(sprintf($_domain, 'Details'), 'Start Date', true),
                                'html' => $startTemplate
                            ),
                            'end_date' => array(
                                'label' => __d(sprintf($_domain, 'Details'), 'End Date', true),
                                'html' => $this->Form->input('end_date', array(
                                    'div' => false,
                                    'label' => false,
                                    'style' => '',
                                    'disabled' => 'disabled',
                                    'value' =>  $str_utility->convertToVNDate(isset($_end_date) ? $_end_date : null),
                                    'type' => 'text',
                                    'class' => 'wd-date',
                                    // 'onfocusout' => 'saveFieldYourForm(this)',
                                ))
                            ),
                            'budget_customer_id' => array(
                                'label' => __d(sprintf($_domain, 'Details'), 'Customer', true),
                                'html' => $this->Form->input('budget_customer_id', array(
                                    'name' => 'data[Project][budget_customer_id]',
                                    'type' => 'select',
                                    'div' => false,
                                    'label' => false,
                                    "empty" => __("-- Select -- ", true),
                                    "options" => (array) @$budgetCustomers,
                                    'onchange' => 'saveFieldYourForm(this)',
                                ))
                            ),
                            'created_value' => array(
                                'label' => __d(sprintf($_domain, 'Details'), 'Created value', true),
                                'html' => $this->Form->input('created_value', array(
                                    'div' => false, 'label' => false,
                                    "class" => "placeholder",
                                    "placeholder" => __("Created value", true),
                                    "readonly" => 'readonly',
                                    'style' => '',
                                    'disabled' => 'disabled',
                                ))
                            ),
                            'id' => array(
                                'label' => __d(sprintf($_domain, 'Details'), 'Project ID', true),
                                'html' => $this->Form->input('id', array(
                                    'div' => false,
                                    'label' => false,
                                    'type' => 'text',
                                    'disabled' => 'disabled',
                                    'id' => 'ProjectProjectId',
                                    // "class" => "placeholder", "placeholder" => __("", true),
                                    "readonly" => 'readonly',
                                ))
                            )
                        );

                        $bgc = 'background-color: #ff290a';
                        if ($nextMilestoneByWeek > 0 && $nextMilestoneByWeek <= 3) {
                            $bgc = 'background-color: #F3960B';
                        } else if ($nextMilestoneByWeek > 3) {
                            $bgc = 'background-color: #5DBF56';
                        }
                        $maps['next_milestone_in_day'] = array(
                            'label' => __d(sprintf($_domain, 'Details'), 'Next milestone in day', true),
                            'html' => $this->Form->input('next_milestone_in_day', array(
                                'div' => false, 'label' => false,
                                "class" => "placeholder", "readonly" => 'readonly', "value" => $nextMilestoneByDay,
                                'style' => $bgc
                            ))
                        );
                        $maps['next_milestone_in_week'] = array(
                            'label' => __d(sprintf($_domain, 'Details'), 'Next milestone in week', true),
                            'html' => $this->Form->input('next_milestone_in_week', array(
                                'div' => false, 'label' => false,
                                "class" => "placeholder", "readonly" => 'readonly', "value" => $nextMilestoneByWeek,
                                'style' => $bgc
                            ))
                        );
                        if ($this->data['Project']['category'] == 1) :
                            $disabled = 'disabled';
                            $style = 'background-color: rgb(218, 221, 226);';
                            if (isset($employeeInfo['Role']['name']) && $employeeInfo['Role']['name'] === 'admin') {
                                $disabled = '';
                            }
                            $option = array(__('No', true), __('Yes', true));
                            $maps['activated'] = array(
                                'label' => __d(sprintf($_domain, 'Details'), 'Timesheet Filling Activated', true),
                                'html' => $this->Form->input('activated', array(
                                    'div' => false,
                                    'label' => false,
                                    'disabled' => 'disabled',
                                    "options" => $option
                                ))
                            );
                        endif;
                        $range = range(1, 20);
                        foreach ($range as $num) {
                            if ($num <= 4) {
                                //bool 0/1
                                $maps['bool_' . $num] = array(
                                    'label' => __d(sprintf($_domain, 'Details'), '0/1 ' . $num, true),
                                    'html' => $this->Form->input('bool_' . $num, array('type' => 'select', 'onchange' => 'saveFieldYourForm(this)', 'div' => false, 'label' => false, 'options' => array(0, 1)))
                                );
                            }
                            if ($num <= 5) {
                                //text editor
                                $maps['editor_' . $num] = array(
                                    'label' => __d(sprintf($_domain, 'Details'), 'Editor ' . $num, true),
                                    'html' => $this->Form->input('editor_' . $num, array('type' => 'textarea', 'class' => 'tinymce-editor', 'div' => array('class' => 'tinymce-container'), 'label' => false)),
                                    'class' => 'tiny-mce-field display-block'
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
                                    'html' => '<div id="uploaderDocument' . $num . '" class="" style=""><p>Your browser do not have Flash, Silverlight or HTML5 support.</p></div>',
                                    'class' => 'wd-upload-document display-block'
                                );
                            }
                            if ($num <= 9) {
                                //yes/no
                                $maps['yn_' . $num] = array(
                                    'label' => __d(sprintf($_domain, 'Details'), 'Yes/No ' . $num, true),
                                    'html' => $this->Form->input('yn_' . $num, array('type' => 'select', 'onchange' => 'saveFieldYourForm(this)', 'div' => false, 'label' => false, 'options' => array(__('No', true), __('Yes', true))))
                                );
                            }
                            // list mutiple select.
                            if ($num <= 10) {
                                $num_class = 7 + $num;
                                if (!empty($datasets['list_muti_' . $num])) {
                                    natcasesort($datasets['list_muti_' . $num]);
                                    $htmlListMultiple = '<div class="multiselect" onchange="saveFieldMutiSelectYourForm(this)" style="margin-right: 3px;">
                <a href="javascript:void();" class="wd-combobox-' . $num_class . '"></a>
                <div id="wd-data-project-' . $num_class . '" style="display: none;" class="list_multiselect list_muti_' . $num . '  listMulti_' . $num . '">';
                                    foreach ($datasets['list_muti_' . $num] as $idPm => $namePm) :
                                        $htmlListMultiple .= '<div class="listMulti wd-data-manager wd-group-' . $idPm . '">
                    <p class="listMulti wd-data" style="margin: 10px 5px;">' .
                                            $this->Form->input('project_list_multi_' . $num, array(
                                                'label' => false,
                                                'div' => false,
                                                'type' => 'checkbox',
                                                'class' => 'listMulti',
                                                'name' => 'data[project_list_multi_' . $num . '][]',
                                                'value' => $idPm,
                                                'id' => 'ProjectProjectListMulti' . $num . '-' . $idPm,
                                            )) .
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
                            if ($num <= 14) {
                                //list
                                if (!empty($datasets['list_' . $num])) {
                                    natcasesort($datasets['list_' . $num]);
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
                            if ($num <= 16) {
                                //price
                                $_class = 'numeric-value';
                                // if( $num > 6 ) {
                                // $_class .= ' not-decimal';
                                // }
                                $maps['price_' . $num] = array(
                                    'label' => __d(sprintf($_domain, 'Details'), 'Price ' . $num, true),
                                    'html' => $this->Form->input('price_' . $num, array('div' => false, 'class' => $_class, 'onchange' => 'saveFieldYourForm(this)', 'label' => false, 'value' => number_format($this->data['Project']['price_' . $num], 2, '.', ' '))) . ' <span style="position: absolute; right: 30px; font-size: 20px; color: #C6CCCF; bottom: 13px">' . $budget_settings . '</span>'
                                );
                            }
                            if ($num <= 18) {
                                //number
                                $_class = 'numeric-value';
                                if ($num > 6) {
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
                                'html' => $this->Form->input('text_two_line_' . $num, array('class' => 'textarea-limit', 'div' => false, 'onfocusout' => 'saveFieldYourForm(this)', 'label' => false, 'rows' => '2',))
                            );
                        }
                        //team
                        $PCModel = ClassRegistry::init('ProfitCenter');
                        $listTeam = $PCModel->generateTreeList(array('company_id' => $employee_info['Company']['id']), null, null, ' -- ', -1);
                        $maps['team'] = array(
                            'label' => __d(sprintf($_domain, 'Details'), 'Team', true),
                            'html' => $this->Form->input('team', array('div' => false, 'label' => false, 'onchange' => 'saveFieldYourForm(this)', "options" => $listTeam, 'empty' => __("--Select--", true)))
                        );
                        ?>
                        <div class="loading_w">
                            <p></p>
                        </div>
                        <?php
                        $htmlPictures = '';
                        $htmlPictures .= '<div id="carousel" class="flexslider">';
                        $htmlPictures .= '<ul class="slides">';
                        if (!empty($images)) {
                            foreach ($images as $image) {
                                if ($image['ProjectImage']['type'] != 'image')
                                    continue;
                                $url = $html->url('/project_images/show/' . $project_id . '/' . $image['ProjectImage']['id'] . '/r_a.jpg');
                                $_url = $html->url('/project_images/show/' . $project_id . '/' . $image['ProjectImage']['id'] . '/l_a.jpg');
                                $htmlPictures .= '<li>';
                                $htmlPictures .=  '<a href="' . $_url . '" class="fancy" rel="project_picture"> <img style="height: 100px" src="' . $url . '" alt="" data-url=" ' . $_url . ' "> </a>';
                                $htmlPictures .=  '</li>';
                            }
                        } else {
                            $htmlPictures .= '<p style="text-align: center; height: 100px; margin-top: 45px;">' . __("No image for display", true) . '</p>';
                        }
                        $htmlPictures .=  '</ul>';
                        $htmlPictures .=  '</div>';
                        $maps['pictures'] = array(
                            'label' => __d(sprintf($_domain, 'Details'), 'Pictures', true),
                            'html' => $htmlPictures
                        );

                        /* 
 * Ticket #1163 - Display / hide field
 * Chuc nang order chua thuc hien
 * Theo yeu cau cua Yann o ticket 1163 thi chua thuc hien chuc nang order
 */
                        $manage_project_setting = (!empty($companyConfigs['manage_project_setting']) && $companyConfigs['manage_project_setting'] == 1);
                        $block_fields = array();
                        $block_num = 0;
                        $displayLastUpdate = 0;
                        $first = 1;
                        foreach ($translation_data as $key => $data) {
                            $fieldName = $data['Translation']['field'];
                            if ($data['TranslationSetting']['show'] == 1 && !empty($maps[$fieldName]['html']) && !isset($maps[$fieldName]['position'])) {
                                $setting_id = $data['TranslationSetting']['id'];
                                if ($first) {
                                    $data['TranslationSetting']['next_block'] = 1;
                                }
                                if (!empty($data['TranslationSetting']['next_block']) && $data['TranslationSetting']['next_block'] == 1) {
                                    $block_num += 1;
                                    $data['block_display'] = 1;
                                    if (isset($employee_setting_details[$setting_id]['block_display']) && ($employee_setting_details[$setting_id]['block_display'] == 0) && ($manage_project_setting)) {
                                        $data['block_display'] = 0;
                                    }
                                }
                                $data['field_display'] = 1;
                                if (isset($employee_setting_details[$setting_id]['field_display']) && ($employee_setting_details[$setting_id]['field_display'] == 0) && $manage_project_setting) {
                                    $data['field_display'] = 0;
                                }
                                $data['label'] = $maps[$fieldName]['label'];
                                $block_fields[$block_num][] = $data;
                                $first = 0;
                            }
                            if (($data['TranslationSetting']['show'] == 1) && ($data['Translation']['original_text'] == 'Last Update: {time} {date} by {resource}')) {
                                $displayLastUpdate = 1;
                            }
                        }
                        // debug( $block_fields); exit;
                        /* Ticket #1163 - Display / hide field */

                        $start_layout = '<div class="wd-fields"><div class="wd-behavior"><i class="icon-arrow-up"></i></div><div class="wd-content-field">';
                        $end_layout = '</div></div>';

                        ?>
                        <fieldset>
                            <div id='chart-wrapper' class="wd-scroll-form">
                                <?php

                                // layout top
                                // echo $start_layout; 
                                ?>

                                <?php
                                /* Update 22/02/2019
								   Change status:
								   1 - Admin
								   2 - PM of project and has right change status of project
								*/
                                $_h = '';
                                if (($employeeInfo['Employee']['change_status_project'] == 1 && $pmCanChange) || $employee_info['Role']['name'] == 'admin') {
                                } else {
                                    $_h = 'disabled';
                                }
                                ?>
                                <div class="project-change-category">
                                    <?php
                                    $cates = array(
                                        1 => __("In progress", true),
                                        2 => __("Opportunity", true),
                                        3 => __("Archived", true),
                                        4 => __("Model", true)
                                    );
                                    $curretnCate = !empty($this->data['Project']['category']) ? $this->data['Project']['category'] : 1;
                                    ?>
                                    <div class="wd-dropdown no-border <?php echo $_h; ?>" onBeforeOpen="checkCategory">
                                        <?php echo $this->Form->input('category', array(
                                            'class' => 'wd-dropdown-seleted pseudo-category',
                                            'div' => false,
                                            'label' => false,
                                            'type' => 'text',
                                            'required' => true,
                                            'disabled' => $_h,
                                            'value' => $curretnCate,
                                            'id' => 'pseudo-category',
                                            'onchange' => 'changeCategory(this)'
                                        )); ?>
                                        <span class="wd-dropdown-selected">
                                            <span class="selected"><?php echo $cates[$curretnCate]; ?></span>
                                            <span class="wd-caret"></span>
                                        </span>
                                        <ul class="popup-dropdown loading-mark">
                                            <?php foreach ($cates as $id => $name) { ?>
                                                <li class="wd-dropdown-option">
                                                    <a href="javascript:void(0);" data-value="<?php echo $id; ?>" class="cate-<?php echo $id; ?> <?php if ($curretnCate == $id) echo 'active'; ?>" data-class="cate-<?php echo $id; ?>" data-text="<?php echo $name; ?>"><?php echo $name; ?></a>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                                <div class="your-form-actions right-action">
                                    <?php if (!empty($companyConfigs['manage_project_setting'])) { ?>
                                        <div class="your-form-action btn-yourform-setting">
                                            <a href="javascript:void(0);" class="yourform-setting-toggle" id="yourform-setting-toggle"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                                    <rect class="svg-a" width="24" height="24"></rect>
                                                    <path class="svg-b" d="M19.874,11.507a.857.857,0,0,1-.56.68l-1.576.56c-.092.264-.2.521-.315.769l.716,1.509a.855.855,0,0,1-.084.874,9.959,9.959,0,0,1-2.146,2.146.853.853,0,0,1-.875.084l-1.508-.717c-.249.117-.506.222-.77.316L12.195,19.3a.854.854,0,0,1-.676.56A9.848,9.848,0,0,1,10,19.99a9.848,9.848,0,0,1-1.519-.127A.854.854,0,0,1,7.8,19.3l-.561-1.575c-.264-.094-.521-.2-.77-.316l-1.508.717a.853.853,0,0,1-.875-.084A9.959,9.959,0,0,1,1.945,15.9a.855.855,0,0,1-.084-.874l.716-1.509a8.2,8.2,0,0,1-.315-.769l-1.576-.56a.857.857,0,0,1-.56-.68A9.876,9.876,0,0,1,0,9.991,9.879,9.879,0,0,1,.126,8.473a.857.857,0,0,1,.56-.678l1.576-.56a8.061,8.061,0,0,1,.315-.769L1.861,4.957a.857.857,0,0,1,.084-.876A9.981,9.981,0,0,1,4.091,1.936a.853.853,0,0,1,.875-.084l1.509.717a7.869,7.869,0,0,1,.769-.316L7.8.677a.858.858,0,0,1,.676-.56,9.115,9.115,0,0,1,3.038,0,.858.858,0,0,1,.676.56l.561,1.576a8.021,8.021,0,0,1,.77.316l1.508-.717a.854.854,0,0,1,.875.084,9.981,9.981,0,0,1,2.146,2.145.857.857,0,0,1,.084.876l-.716,1.509a8.2,8.2,0,0,1,.315.769l1.576.56a.855.855,0,0,1,.56.678A9.879,9.879,0,0,1,20,9.991,9.876,9.876,0,0,1,19.874,11.507ZM18.245,9.234l-1.479-.526a.853.853,0,0,1-.535-.565,6.426,6.426,0,0,0-.517-1.257.86.86,0,0,1-.021-.777l.671-1.41a8.357,8.357,0,0,0-1.073-1.072l-1.41.67a.85.85,0,0,1-.777-.02,6.463,6.463,0,0,0-1.257-.517.857.857,0,0,1-.564-.535l-.526-1.479a6.957,6.957,0,0,0-1.512,0L8.717,3.225a.857.857,0,0,1-.564.535A6.463,6.463,0,0,0,6.9,4.277a.854.854,0,0,1-.778.02l-1.41-.67A8.356,8.356,0,0,0,3.636,4.7l.671,1.41a.86.86,0,0,1-.021.777,6.5,6.5,0,0,0-.517,1.258.854.854,0,0,1-.535.563l-1.479.527a6.955,6.955,0,0,0,0,1.513l1.479.525a.858.858,0,0,1,.535.565,6.421,6.421,0,0,0,.517,1.257.862.862,0,0,1,.021.778l-.671,1.409a8.366,8.366,0,0,0,1.072,1.073l1.41-.671A.854.854,0,0,1,6.9,15.7a6.463,6.463,0,0,0,1.257.517.854.854,0,0,1,.564.535l.526,1.478a6.958,6.958,0,0,0,1.512,0l.526-1.478a.854.854,0,0,1,.564-.535A6.463,6.463,0,0,0,13.1,15.7a.858.858,0,0,1,.777-.021l1.41.67a8.277,8.277,0,0,0,1.073-1.072l-.671-1.409a.862.862,0,0,1,.021-.778,6.453,6.453,0,0,0,.517-1.257.858.858,0,0,1,.535-.565l1.479-.525a6.966,6.966,0,0,0,0-1.514ZM10,13.757A3.968,3.968,0,1,1,13.969,9.79,3.973,3.973,0,0,1,10,13.757Zm0-6.222A2.254,2.254,0,1,0,12.254,9.79,2.257,2.257,0,0,0,10,7.535Z" transform="translate(2 2.01)"></path>
                                                </svg></a>
                                        </div>
                                        <?php }
                                    $enableRMS = $this->Session->read('enableRMS');
                                    if (!empty($this->data['Project']['activity_id']) && !empty($pmCanChange) &&  ($enableRMS == true)) {
                                        $active_class = $activated_of_actvity ? 'wd-update-default' : '';
                                        if ($this->data['Project']['category'] == 1) { ?>
                                            <div class="your-form-action activated-field">
                                                <div id="switch-activated" class="wd-bt-switch">
                                                    <a class="wd-update <?php echo $active_class; ?>" title="<?php echo __('Timesheet activated', true); ?>">
                                                        <input type="hidden" name="activated" data-value="<?php echo $activated_of_actvity; ?>" data-activity-id='<?php echo $this->data['Project']['activity_id']; ?>' data-id='<?php echo $this->data['Project']['id']; ?>'>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php }
                                    }
                                    //Dupliquer ce projet
                                    if (($employee_info['Role']['name'] == 'admin') || ($employee_info['Employee']['create_a_project'] == '1')) { ?>
                                        <div class="your-form-action btn-duplicate">
                                            <a href="/projects/duplicateProject/<?php echo $project_id; ?>" title="<?php echo __('Duplicate this project'); ?>"><img src="/img/new-icon/duplicate.jpg" /></a>
                                        </div>
                                    <?php }  ?>
                                </div>
                                <div class="wd-fields">
                                    <div class="wd-behavior">

                                        <i class="icon-arrow-up"></i>
                                    </div>
                                    <div class="wd-content-field">
                                        <div class="wd-row">
                                            <div class="column-box column-1 wd-col wd-col-md-4">
                                                <div class="wd-row">
                                                    <?php $item = 0;
                                                    // ob_clean();
                                                    // debug($translation_data_box_1);
                                                    // exit;
                                                    // draw block 1 cua man hinh 
                                                    foreach ($translation_data_box_1 as $data) {
                                                        //ignore project details
                                                        if ($data['Translation']['field'] == 'project_details') continue;
                                                        $fieldName = $data['Translation']['field'];
                                                        $class = isset($maps[$fieldName]['class']) ? $maps[$fieldName]['class'] : '';
                                                        if (!empty($maps[$fieldName]['html']) && $item < 3) { ?>
                                                            <div class="wd-input wd-area wd-none <?php echo $class ?>">
                                                                <label><?php echo !empty($maps[$fieldName]['label']) ? $maps[$fieldName]['label'] : ''; ?></label>
                                                                <?php echo !empty($maps[$fieldName]['html']) ? $maps[$fieldName]['html'] : ''; ?>
                                                            </div>
                                                    <?php $item++;
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="column-box column-2 wd-col wd-col-md-4">
                                                <?php if (!empty($item) && !empty($translation_data_box_1[3])) {
                                                    $fieldName = $translation_data_box_1[3]['Translation']['field'];
                                                    $class = isset($maps[$fieldName]['class']) ? $maps[$fieldName]['class'] : '';
                                                    if ($translation_data_box_1[3]['TranslationSetting']['show'] == 1 && !empty($maps[$fieldName]['html'])) { ?>
                                                        <div class="wd-input wd-area wd-none <?php echo $class ?>">
                                                            <label><?php echo !empty($maps[$fieldName]['label']) ? $maps[$fieldName]['label'] : ''; ?></label>
                                                            <?php echo !empty($maps[$fieldName]['html']) ? $maps[$fieldName]['html'] : ''; ?>
                                                        </div>
                                                <?php
                                                    }
                                                } ?>
                                                <div class="wd-dropzone">
                                                    <div class="trigger-upload">
                                                        <div class="open-popup" data-id="<?php echo $project_id; ?>" style="width: 100%;">
                                                            <?php
                                                            $link = $this->Html->url(array('controller' => 'project_global_views', 'action' => 'attachment', $project_id, '?' => array('sid' => $api_key)), true);
                                                            $is_html = false;
                                                            $is_link = false;
                                                            if ($projectGlobalView) {
                                                                $is_http = $projectGlobalView['ProjectGlobalView']['is_https'] ? 'https://' : 'http://';
                                                                if ($noFileExists) {
                                                                    // LINK
                                                                    if ($projectGlobalView['ProjectGlobalView']['is_https'] == 1) {
                                                                        $link = $projectGlobalView['ProjectGlobalView']['attachment'];
                                                                        preg_match('/(<iframe)|(http)/', $link, $matches);
                                                                        if (empty($matches)) $link = $is_http . $link;
                                                                        $is_link = true;
                                                                    } else {
                                                                        $link = $projectGlobalView['ProjectGlobalView']['attachment'];
                                                                        // $is_html = true;
                                                                        $is_link = true;
                                                                    }
                                                                } else {
                                                                    // File upload is not image.
                                                                    if (!preg_match('/\.(jpg|jpeg|bmp|gif|png|swf)$/i', $projectGlobalView['ProjectGlobalView']['attachment'])) {
                                                                        $link = 'https://docs.google.com/gview?url=' . ($link) . '&embedded=true';
                                                                        $is_link = true;
                                                                    }
                                                                }
                                                            } else {
                                                                $link = '';
                                                            }
                                                            if (!empty($link)) {
                                                                if ($is_html) {
                                                                    echo $link;
                                                                } else if ($is_link) {
                                                                    if (preg_match('/<iframe/', $link)) {
                                                                        echo $link;
                                                                    } else { ?>
                                                                        <iframe src="<?php echo $link; ?>" class="img-responsive" style="width: 100%;height: 252px;"></iframe>
                                                                    <?php } ?>
                                                                <?php } else { ?>
                                                                    <img class="img-responsive" src="<?php echo (!empty($link) ? $link : '') ?>" alt="">
                                                                <?php }
                                                                if ((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite)) {
                                                                    echo $this->Html->link($this->Html->image('ajax-loader.gif', array('id' => 'loader', 'style' => 'display: none;')) . $this->Html->image('new-icon/delete.png') . __('', true), 'javascript:void(0);', array('escape' => false, 'class' => 'btn replace-attachment', 'title' => __('Remove', true)));
                                                                }
                                                                // khong co hinh thi hien thi icon upload
                                                            } else { ?>
                                                                <div id="wd-project-upload-popup" method="post" action="/project_global_views_preview/upload/<?php echo $projectName['Project']['id']; ?>/1" class="dropzone" value="1">

                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="column-box column-3 wd-col wd-col-md-4">
                                                <?php if (!empty($translation_data_box_1[4])) {
                                                    $fieldName = $translation_data_box_1[4]['Translation']['field'];
                                                    $class = isset($maps[$fieldName]['class']) ? $maps[$fieldName]['class'] : '';
                                                    if ($translation_data_box_1[4]['TranslationSetting']['show'] == 1 && !empty($maps[$fieldName]['html'])) { ?>
                                                        <div class="wd-input wd-area wd-none <?php echo $class ?>">
                                                            <label><?php echo !empty($maps[$fieldName]['label']) ? $maps[$fieldName]['label'] : ''; ?></label>
                                                            <?php echo !empty($maps[$fieldName]['html']) ? $maps[$fieldName]['html'] : ''; ?>
                                                        </div>
                                                <?php
                                                    }
                                                } ?>
                                                <div class='wd-map-area'>
                                                    <!--  <div class="fullscreen"><i class="icon-frame"></i></div> -->
                                                    <a class="fullscreen-btn" href="javascript:;" onclick="expandScreen();"><i class="icon-frame"></i></a>
                                                    <div class="map-heading">
                                                        <h3><?php echo sprintf(__("%s", true), $projectName['Project']['project_name']); ?></h3>
                                                        <a class="collapse" href="javascript:;" onclick="collapseScreen();"><i class="icon-size-actual"></i></a>
                                                    </div>
                                                    <iframe src="about:blank" style="height: 230px; display: none;" id="map-frame"></iframe>
                                                    <div class='wd-input'>
                                                        <input type="text" id="coord-input" onfocusout="" onchange="refreshMapYourForm(this)" name="data[Project][address]" size="40" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                //layout bottom
                                //draw block 2+ cho man hinh project_preview/your_form

                                if (!empty($block_fields)) {
                                    foreach ($block_fields as $key => $datas) {
                                        if (!empty($datas)) {
                                            $block_title = '';
                                            $block_setting_id = $datas[0]['TranslationSetting']['id'];
                                            if (empty($datas[0]['block_display']) && ($key != 0)) {
                                                continue;
                                            }
                                            $has_field = 0;
                                            foreach ($datas as $key => $data) {
                                                if (!empty($data['field_display'])) {
                                                    $has_field = 1;
                                                    break 1; //break 1 vòng
                                                }
                                            }
                                            if (!$has_field) {
                                                continue;
                                            }
                                            if ((!empty($datas[0]['TranslationSetting']['next_block']) && $datas[0]['TranslationSetting']['next_block'] == 1) || ($key == 0 && !empty($datas[0]['TranslationSetting']['block_name']))) {
                                                $block_title = '<span class="block-title">' . (!empty($datas[0]['TranslationSetting']['block_name']) ? $datas[0]['TranslationSetting']['block_name'] : '') . '</span>';
                                            }
                                            // echo $start_layout;
                                            echo '<div class="wd-fields block-' . $block_setting_id . '"><div class="wd-behavior">' . $block_title . '<i class="icon-arrow-up"></i></div><div class="wd-content-field">';
                                            echo '<div class="wd-row">';
                                            $i = 0;
                                            $last_key = count($datas) - 1;
                                            // ob_clean(); debug( $maps); exit;
                                            foreach ($datas as $key => $data) {
                                                $i = $i % 3; // 0< $i < 2
                                                //ignore project details
                                                $has_field = 0;
                                                if (empty($data['field_display'])) {
                                                    continue;
                                                }
                                                $has_field = 1;
                                                if ($data['Translation']['field'] == 'project_details') continue;
                                                $fieldName = $data['Translation']['field'];
                                                $class = isset($maps[$fieldName]['class']) ? $maps[$fieldName]['class'] : '';
                                                $next_line = 0;
                                                $preg = '/(wd-input-text)|(tiny-mce-field)|(wd-upload-document)|(display-block)/';
                                                preg_match($preg, $class, $matches);
                                                if (!empty($data['TranslationSetting']['next_line'])  || ($key == $last_key)   || ($i == 2) ||  !empty($matches))  $next_line = 1;

                                                if ($next_line && $i == 0) $class .= ' display-block';

                                                if (isset($maps[$fieldName]['position'])) {
                                                    // Cac fields trong Detail_5. Da render o block dau tien
                                                } else {
                                                    if ($data['TranslationSetting']['show'] == 1 && !empty($maps[$fieldName]['html'])) { ?>
                                                        <?php if ($next_line) {
                                                            // echo '<div class="next-line">';
                                                            $class .= ' next-line';
                                                            $i = 2;
                                                        } ?>
                                                        <div class="wd-input wd-area wd-none <?php echo $class ?>">
                                                            <label><?php echo !empty($maps[$fieldName]['label']) ? $maps[$fieldName]['label'] : ''; ?></label>
                                                            <?php echo !empty($maps[$fieldName]['html']) ? $maps[$fieldName]['html'] : ''; ?>
                                                        </div>
                                <?php if ($next_line) {
                                                            // echo '</div>';
                                                            echo '<div class="clearfix"></div>';
                                                        }
                                                    }
                                                }
                                                $i++;
                                            }
                                            echo '</div>'; // Close row
                                            echo $end_layout;
                                        }
                                    }
                                }
                                ?>
                            </div>
                            <?php if ($displayLastUpdate == 1) { ?>
                                <div class="wd-status-update">
                                    <?php
                                    if (!($time = $this->data['Project']['last_modified'])) {
                                        $time = $project_name['Project']['updated'];
                                    }
                                    $full_name = explode(' ', $project_name['Project']['update_by_employee']);
                                    $first_name = $full_name[0];
                                    $last_name = $full_name[1];
                                    $first_name  = (!empty($first_name)) ? $first_name = substr(trim($first_name),  0, 1) : '';
                                    $last_name  = (!empty($last_name)) ? $last_name = substr(trim($last_name),  0, 1) : '';
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
                                    echo '<span class="circle-name">' . $first_name . '' . $last_name . '</span>';
                                    ?>
                                </div>
                            <?php } ?>
                        </fieldset>
                        </form>
                        <div id="white-space"></div>
                        <div id='openPopup' class="wd-image-popup" style="display: none">
                            <a class="close-popup" href="javascript:;"><i class="icon-size-actual"></i></a>
                            <?php echo $this->Session->flash(); ?>
                            <div class="wd-section">
                                <h2 class="wd-t2"><?php echo $projectName['Project']['project_name'] ?></h2>
                                <fieldset>
                                    <?php if ($projectGlobalView) : ?>
                                        <!-- If is_file = 1 then File else if is_file = 0 then Url -->
                                        <div id="download-place">
                                            <a class="download-place-toggle-button" href="javascript:void(0);" title=" <?php echo __d(sprintf($_domain, 'Global_Views'), 'More option', true); ?>" data-closetitle="<?php echo __d(sprintf($_domain, 'Global_Views'), 'Close', true); ?>" data-viewmoretitle="<?php echo __d(sprintf($_domain, 'Global_Views'), 'More option', true); ?>">
                                                <i class='button-dot button-dot-1'></i>
                                                <i class='button-dot button-dot-2'></i>
                                                <i class='button-dot button-dot-3'></i>
                                            </a>
                                            <div class="download-place-inner wd-title">
                                                <?php
                                                if ($projectGlobalView['ProjectGlobalView']['is_file']) {
                                                    echo $this->Html->link($this->Html->image('new-icon/download.png') . __('', true), array(
                                                        'controller' => 'project_global_views_preview',
                                                        'action' => 'attachment', $projectName['Project']['id'], '?' => array('download' => true, 'sid' => $api_key)
                                                    ), array(
                                                        'escape' => false,
                                                        'id' => 'download-attachment',
                                                        'class' => 'btn',
                                                        'title' => __d(sprintf($_domain, 'Global_Views'), 'Download this attachment ', true)
                                                    ));
                                                } else {
                                                    $is_http = $projectGlobalView['ProjectGlobalView']['is_https'] ? 'https://' : 'http://';
                                                    $IFRAME = $is_http . $projectGlobalView['ProjectGlobalView']['attachment'];
                                                    echo "<a href=" . $is_http . $projectGlobalView['ProjectGlobalView']['attachment'] . " target='_blank'>" . $this->Html->image('url.png') . __('URL ', true) . "</a>";
                                                }

                                                if ((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite)) {
                                                    echo $this->Html->link($this->Html->image('new-icon/delete.png') . __('', true), 'javascript:void(0);', array(
                                                        'escape' => false,
                                                        'id' => '',
                                                        'class' => 'replace-attachment',
                                                        'title' => __d(sprintf($_domain, 'Global_Views'), 'Remove this attachment', true)
                                                    ));
                                                    echo $html->image('ajax-loader.gif', array(
                                                        'id' => 'loader',
                                                        'style' => 'display: none; margin-left: 3px'
                                                    ));
                                                }
                                                echo $this->Html->link($this->Html->image('new-icon/expand.png') . __('', true), 'javascript:void(0);', array(
                                                    'escape' => false,
                                                    'id' => 'expand',
                                                    'class' => 'btn',
                                                    'title' => __d(sprintf($_domain, 'Global_Views'), 'Expand', true)
                                                ));
                                                ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div id="upload-place" class="wd-upload" style="<?php echo 'display:' . ($projectGlobalView ? 'none' : 'block'); ?>">
                                        <?php if ($canModified) { ?>
                                            <div class="wd-tab wd-uload-tabs">
                                                <nav class="wd-item tabs-header-container">
                                                    <ul class="tabs-upload-header-inner">
                                                        <li class="tab-header wd-current" data-tab="0"><?php echo __('File', true); ?></li>
                                                        <li class="tab-header" data-tab="1"><?php echo __('URL', true); ?></li>
                                                        <li class="tab-header" data-tab="2"><?php echo __('HTML', true); ?></li>
                                                    </ul>
                                                </nav>
                                                <div class="tabs-content-container">
                                                    <ul class="tabs-content-container-inner">
                                                        <li class="tab-content wd-current" data-tab="0">
                                                            <div class="trigger-upload-popup">
                                                                <form id="upload-widget" onsubmit="completeAndRedirect()" method="post" action="/project_global_views_preview/upload/<?php echo $projectName['Project']['id']; ?>" class="dropzone" value="">
                                                                    <input type="hidden" name="data[ProjectGlobalView][is_file]" rel="no-history" value="1" id="UploadId">
                                                                </form>
                                                            </div>
                                                            <br style="clear: both;" />
                                                        </li>
                                                        <li class="tab-content" data-tab="1">
                                                            <?php
                                                            echo $this->Form->create('ProjectGlobalView', array(
                                                                'type' => 'file',
                                                                'display' => 'block',
                                                                'id' => 'ProjectGlobalView',
                                                                'url' => array(
                                                                    'controller' => 'projects_preview', 'action' => 'upload_global',
                                                                    $projectName['Project']['id']
                                                                )
                                                            ));

                                                            echo $this->Form->input('attachment', array(
                                                                'div' => false, 'label' => false, 'id' => 'attachmentUrl',
                                                                'type' => 'text',
                                                                //'style' => 'width: 200px; padding: 6px',
                                                                'placeholder' => 'Ex: www.example.com/your_image.jpg'
                                                            ));
                                                            ?>
                                                            <fieldset>
                                                                <?php
                                                                echo $this->Form->input('is_file', array(
                                                                    //'div' => false, 
                                                                    'label' => false,
                                                                    'id' => 'IsFile0',
                                                                    'type' => 'hidden',
                                                                    'value' => '0',
                                                                ));
                                                                ?>

                                                                <div class="wd-submit">
                                                                    <button type="submit" class="btn btn-submit wd-button-f" id="btnSave">
                                                                        <?php __('Submit') ?>
                                                                    </button>
                                                                </div>
                                                            </fieldset>

                                                            <?php echo $this->Form->end(); ?>

                                                        </li>
                                                        <li class="tab-content" data-tab="2">
                                                            <?php
                                                            echo $this->Form->create('ProjectGlobalView', array(
                                                                // 'type' => 'file',
                                                                'id' => 'ProjectGlobalView_uploadHtml',
                                                                'url' => array(
                                                                    'controller' => 'projects_preview', 'action' => 'upload_global',
                                                                    $projectName['Project']['id']
                                                                )
                                                            ));

                                                            echo $this->Form->input('attachment', array(
                                                                'div' => false, 'label' => false, 'id' => 'attachmentHtml',
                                                                'type' => 'textarea',
                                                                'name' => 'data[ProjectGlobalView][attachment]',
                                                                'placeholder' => 'Enter embed code here'
                                                            ));
                                                            echo $this->Form->input('is_file', array(
                                                                'div' => false, 'label' => false, 'id' => 'IsFile2',
                                                                'type' => 'hidden',
                                                                'value' => '2',
                                                                'name' => 'data[ProjectGlobalView][is_file]'
                                                            ));
                                                            ?>

                                                            <fieldset>
                                                                <div class="wd-submit">
                                                                    <button type="submit" class="btn btn-submit wd-button-f" id="btnSave2">
                                                                        <?php __('Submit') ?>
                                                                    </button>
                                                                </div>
                                                            </fieldset>

                                                            <?php echo $this->Form->end(); ?>
                                                        </li>
                                                    </ul>
                                                </div>
                                            <?php } else { ?>
                                                <p class="empty-message">
                                                    <?php __('No Global View was upload'); ?>
                                                </p>
                                            <?php } ?>

                                            </div>
                                </fieldset>
                            </div>
                            <br />

                            <div class="wd-section" id="wd-fragment-2" style="overflow: auto">
                                <?php
                                $is_link = false;
                                $is_html = false;
                                $link = $this->Html->url(array('controller' => 'project_global_views', 'action' => 'attachment', $projectName['Project']['id'], '?' => array('sid' => $api_key)), true);

                                if ($projectGlobalView) {
                                    $is_http = $projectGlobalView['ProjectGlobalView']['is_https'] ? 'https://' : 'http://';
                                    if ($noFileExists) {
                                        // LINK
                                        if ($projectGlobalView['ProjectGlobalView']['is_https'] == 1) {
                                            $link = $projectGlobalView['ProjectGlobalView']['attachment'];
                                            preg_match('/(<iframe)|(http)/', $link, $matches);
                                            if (empty($matches)) $link = $is_http . $link;
                                            $is_link = true;
                                        } else {
                                            $link = $projectGlobalView['ProjectGlobalView']['attachment'];
                                            // $is_html = true;
                                            $is_link = true;
                                        }
                                    } else {
                                        // File upload is not image.
                                        if (!preg_match('/\.(jpg|jpeg|bmp|gif|png|swf)$/i', $projectGlobalView['ProjectGlobalView']['attachment'])) {
                                            $link = 'https://docs.google.com/gview?url=' . ($link) . '&embedded=true';
                                            $is_link = true;
                                        }
                                    }
                                } else {
                                    $link = '';
                                }
                                if ($is_html) {
                                    echo $link;
                                } else if ($is_link) {
                                    if (preg_match('/<iframe/', $link)) {
                                        echo $link;
                                    } else { ?>
                                        <iframe src="<?php echo $link; ?>" class="img-responsive" style="width: 100%;height: 900px;"></iframe>
                                    <?php } ?>
                                <?php } else { ?>
                                    <!--
									<img class="img-responsive" src="<?php echo (!empty($link) ? $link : '') ?>" alt="">
									-->
                                    <img onclick="openImage(this)" src="<?php echo $link; ?>" style="max-width: 100%; max-height: 950px; margin-left: auto; margin-right: auto; display: block;"></img>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($companyConfigs['manage_project_setting'])) { ?>
    <!-- POPUP setting -->
    <?php if (!function_exists('draw_setting_block')) {
        function draw_setting_block($Form, $block_data, $index = null)
        {
            // debug( $Form); exit;
            if (empty($block_data[0])) return '';
            $block = $block_data[0];
            $_block_display = !empty($block['block_display']);
            $_block_id = $block['TranslationSetting']['id'];
            // $_block_name = !empty( $block['TranslationSetting']['block_name']) ? $block['TranslationSetting']['block_name'] : sprintf( __('Block %s', true), $index);
            $_block_name = !empty($block['TranslationSetting']['block_name']) ? $block['TranslationSetting']['block_name'] : '&nbsp';
            ob_start();
    ?>
            <li class="block <?php echo $_block_display ? 'displayed' : 'not-displayed'; ?>">
                <div class="block-setting">
                    <p class="layout-name block-name"><?php echo $_block_name; ?></p>
                    <?php
                    echo $Form->input('ProjectDetailEmployeeSetting.' . $_block_id . '.block_display', array(
                        'type' => 'checkbox',
                        'label' => false,
                        'checked' => $_block_display,
                        'value' => $_block_id,
                        'class' => 'checkbox_block_display',
                        'div' => array(
                            'class' => 'wd-input wd-checkbox-switch',
                            // 'title' => __('Actif',true),
                        ),
                        'type' => 'checkbox',
                    )); ?>
                </div>
                <div class="fields-setting">
                    <?php
                    $_opts = array();
                    $_vals = array();
                    foreach ($block_data as $k => $field) {
                        $field_id = $field['TranslationSetting']['id'];
                        $field_name = 'data[ProjectDetailEmployeeSetting][' . $field_id . '][field_display]';
                        $_opts[$field_id] = $field['label'];
                        $checked = !empty($field['field_display']);
                        if ($checked) $_vals[] = $field_id;
                        echo $Form->input('field_display', array(
                            'name' => $field_name,
                            'type' => 'hidden',
                            'value' => 0,
                            'id' => 'ProjectDetailEmployeeSettingFieldDisplay' . $field_id,
                        ));
                        echo $Form->input('translation_setting_id', array(
                            'name' => 'data[ProjectDetailEmployeeSetting][' . $field_id . '][translation_setting_id]',
                            'type' => 'hidden',
                            'value' => $field_id,
                            'id' => 'TranslationSettingId' . $field_id,
                        ));
                    }
                    echo $Form->input('ProjectDetailEmployeeSetting.translation_setting_id', array(
                        'type' => 'select',
                        'multiple' => 'checkbox',
                        'multiple' => 'true',
                        'options' => $_opts,
                        'default' => $_vals,
                        'label' => false,
                        'id' => 'translation_setting_id' . $_block_id,
                        'div' => array(
                            'class' => 'wd-input jq-multiselect-custom'
                        ),
                    ));
                    ?>
                </div>
            </li>

    <?php
            return ob_get_clean();
        }
    }
    ?>
    <div class="wd-yourform-settings wd-hide">
        <div id="dialog-yourform-settings" class="dialog-yourform-settings dialog-popup" style="display: none;">
            <span class="dialog-title wd-hide"><?php echo __('Display settings', true); ?></span>
            <?php echo $this->Form->create('ProjectDetailEmployeeSetting', array(
                'type' => 'POST',
                'id' => 'ProjectDetailEmployeeSettingForm',
                'url' => array('controller' => 'project_detail_employee_settings', 'action' => 'update')
            ));
            echo $this->Form->input('return', array(
                'data-return' => 'form-return',
                'type' => 'hidden',
                'value' => $this->Html->url(),
            ));
            ?>
            <div id="layout-setting" class="popup-layout-setting">
                <ul class="wd-relative">
                    <?php foreach ($block_fields as $key => $block) {
                        $component = $this->Form;
                        echo draw_setting_block($component, $block, $key);
                    } ?>
                </ul>
            </div>
            <div class="wd-submit">
                <button type="submit" class="btn-form-action btn-ok btn-right" id="ysettingsSave">
                    <span><?php echo __('Save', true); ?></span>
                </button>
                <a class="btn-form-action btn-cancel" id="ysettingsCancel" href="javascript:void(0);" onclick="">
                    <?php echo __('Cancel', true); ?>
                </a>
                <!--
				<button type="reset" class="btn-form-action btn-reset" id="ysettingsReset" href="javascript:void(0);" value="Reset">
							<?php echo __('Reset', true); ?>
				</button>
				-->
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
    <!-- END POPUP setting -->
<?php } ?>
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
if ($employee_info['Role']['name'] == 'admin') {
    $showAllFieldYourform = 1;
} else {
    if ($_isProfile && $_canWrite) {
        $showAllFieldYourform = 1;
    } else if ($pmCanChange) {
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
$all_cates = array(
    1 => __("In progress", true),
    2 => __("Opportunity", true),
    3 => __("Archived", true),
    4 => __("Model", true)
);

$limit_cates = array(
    1 => __("In progress", true),
    3 => __("Archived", true)
);
$i18n = array(
    'Search' => __("Search", true),
);
$select_pm_attribute = array(
    'project_manager_id' => 'project_employee_manager',
    'chief_business_id' => 'chief_business_list',
    'functional_leader_id' => 'functional_leader_list',
    'uat_manager_id' => 'uat_manager_list',
    'technical_manager_id' => 'technical_manager_list',
    'read_access' => 'read_access',
);

$can_manage_your_form_field = !empty($companyConfigs['can_manage_your_form_field']) ? $companyConfigs['can_manage_your_form_field'] : 0;
?>
<script type="text/javascript">
    var companyConfigs = <?php echo json_encode($companyConfigs); ?>;
    var curretnCate = <?php echo json_encode($curretnCate); ?>;
    var role = <?php echo json_encode($employeeInfo['Role']['name']); ?>;
    var employee_id = <?php echo json_encode($employeeInfo['Employee']['id']); ?>;
    var activityId = <?php echo json_encode($this->data['Project']['activity_id']); ?>;
    var haveActivity = <?php echo json_encode($haveActivity); ?>;
    var changeProjectManager = <?php echo json_encode($changeProjectManager); ?>;
    var pmCanChange = <?php echo json_encode($pmCanChange); ?>;
    var showAllFieldYourform = <?php echo json_encode($showAllFieldYourform); ?>;
    var pm_update_field = <?php echo json_encode($pm_update_field); ?>;
    var select_pm_attribute = <?php echo json_encode($select_pm_attribute); ?>;
    var can_manage_your_form_field = <?php echo json_encode($can_manage_your_form_field); ?>;
    var codeExisted = <?php echo json_encode(__('Already used in project', true)); ?>;
    var activityNameExited = <?php echo json_encode(__('An activity has already this name', true)); ?>;
    var project_id = <?php echo json_encode($project_name['Project']['id']) ?>;
    var change_status_project = <?php echo json_encode(!empty($employeeInfo['Employee']['change_status_project']) ? $employeeInfo['Employee']['change_status_project'] : 0); ?>;
    var projectFiles1 = <?php echo json_encode(!empty($projectFiles['upload_documents_1']) ? $projectFiles['upload_documents_1'] : array()) ?>;
    var projectFiles2 = <?php echo json_encode(!empty($projectFiles['upload_documents_2']) ? $projectFiles['upload_documents_2'] : array()) ?>;
    var projectFiles3 = <?php echo json_encode(!empty($projectFiles['upload_documents_3']) ? $projectFiles['upload_documents_3'] : array()) ?>;
    var projectFiles4 = <?php echo json_encode(!empty($projectFiles['upload_documents_4']) ? $projectFiles['upload_documents_4'] : array()) ?>;
    var projectFiles5 = <?php echo json_encode(!empty($projectFiles['upload_documents_5']) ? $projectFiles['upload_documents_5'] : array()) ?>;
    var $opportunity_to_in_progress_without_validation = <?php echo json_encode($opportunity_to_in_progress_without_validation) ?>;
    var all_cates = <?php echo json_encode($all_cates) ?>;
    var limit_cates = <?php echo json_encode($limit_cates) ?>;
    var i18n = <?php echo json_encode($i18n) ?>;
    var multiSelect_timeout = 0;
    /* table .end */
    if (showAllFieldYourform == 0) {
        var _your_form_section = $("#wd-fragment-1");
        _your_form_section.find("input").prop('disabled', true);
        _your_form_section.find("select").prop('disabled', true);
        _your_form_section.find("textarea").prop('disabled', true);
        _your_form_section.find(".wd-multiselect").addClass('disabled');
        _your_form_section.find(".multiselect").addClass('disabled');
        _your_form_section.find(".mce-edit-area").prop('disabled', true);
        _your_form_section.find(".wd-combobox-2").prop('disabled', true);
        _your_form_section.find(".wd-combobox-3").prop('disabled', true);
        _your_form_section.find(".wd-combobox-4").prop('disabled', true);
        _your_form_section.find(".wd-combobox-5").prop('disabled', true);
        _your_form_section.find(".wd-combobox-6").prop('disabled', true);
        _your_form_section.find(".wd-combobox-7").prop('disabled', true);
        _your_form_section.find(".wd-combobox-8").prop('disabled', true);
        _your_form_section.find(".wd-combobox-9").prop('disabled', true);
        _your_form_section.find(".wd-combobox-10").prop('disabled', true);
        _your_form_section.find(".wd-combobox-11").prop('disabled', true);
        _your_form_section.find(".wd-combobox-12").prop('disabled', true);
        _your_form_section.find(".wd-combobox-13").prop('disabled', true);
        _your_form_section.find(".wd-combobox-14").prop('disabled', true);
        _your_form_section.find(".wd-combobox-15").prop('disabled', true);
        _your_form_section.find(".wd-combobox-16").prop('disabled', true);
        _your_form_section.find(".wd-combobox-17").prop('disabled', true);

    }
    if (showAllFieldYourform == 1 && role == 'pm' && can_manage_your_form_field == 1) {
        if (pm_update_field) {
            $.each(pm_update_field, function(key, val) {
                if ($.inArray(employee_id, val) === -1) {
                    $('input[name="data[Project][' + key + ']"]').prop('disabled', true);
                    $('select[name="data[Project][' + key + ']"]').prop('disabled', true);
                    $('textarea[name="data[Project][' + key + ']"]').prop('disabled', true);
                    $('.' + key).closest(".multiselect").addClass('disabled');
                    if (select_pm_attribute[key]) {
                        $('.' + select_pm_attribute[key]).closest(".multiselect").addClass('disabled');
                    }
                }
            });
        }
    }
    var createDialog = function() {
        $('#change_category, #save_activity_linked').dialog({
            position: 'center',
            autoOpen: false,
            autoHeight: true,
            modal: true,
            width: 600,
            open: function(e) {
                var $dialog = $(e.target);
                $dialog.dialog({
                    open: $.noop
                });
            }
        });
        createDialog = $.noop;
    }

    function doUpload() {
        var me = $(this);
        $('#temp-upload-form').ajaxSubmit({
            dataType: 'json',
            success: function(d) {
                var url = d.location,
                    win = me.data('win');
                win.document.getElementById(me.data('name')).value = url;
            }
        });
        this.value = '';
    }

    $(".cancel").live('click', function() {
        $("#change_category").dialog('close');
    });
    $(".cancel_save_ac_linked").live('click', function() {
        if (curretnCate) {
            // $('#pseudo-category').val(curretnCate);
            wd_dropdown_setvalue($('#pseudo-category'), curretnCate);
        }
        $('.wd-main-content').removeClass('loading');
        $("#save_activity_linked").dialog('close');
    });
    // get sub family
    function listSubFamily() {
        //$('#ok_save_ac_linked').hide();
        var familyId = '';
        $('#ActivityLinkedFamily option').each(function() {
            if ($(this).is(':selected')) {
                familyId = $('#ActivityLinkedFamily').val();
            }
        });
        var subFam = $('#ProjectProjectAmrSubProgramId').val();
        var _url = '/projects/getSubFamily/' + familyId;
        if (subFam) {
            _url = '/projects/getSubFamily/' + familyId + '/' + subFam;
        }
        if (familyId != '') {
            setTimeout(function() {
                $.ajax({
                    url: _url,
                    async: false,
                    beforeSend: function() {

                    },
                    success: function(datas) {
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
    $(document).ready(function() {
        if ($("#dialog_employee_has_left").length > 0) {
            var createTaskDialog = function() {
                $('#dialog_employee_has_left').dialog({
                    position: 'center',
                    autoOpen: false,
                    autoHeight: true,
                    modal: true,
                    create: function(event, ui) {
                        $('#dialog_employee_has_left').closest('.ui-dialog').addClass('wd-dialog-2019');
                    },
                    width: 1120,
                    maxHeight: parseInt($(window).height() * 0.8),
                    open: function(e) {
                        var $dialog = $(e.target);
                        $dialog.dialog({
                            open: $.noop
                        });
                    },
                    title: ''
                });
                createDialog = $.noop;
            }
            createTaskDialog();
            $("#dialog_employee_has_left").dialog('open');
        }
        $('.wd-date').define_limit_date('#ProjectStartDate', '#ProjectEndDate');
        // $('#ProjectEndDate').define_limit_date(0);
        var edit = false;
        $("#onChangeCode").focus(function() {
            edit = false;
            $("#idButtonSave").hide();
        });
        $("#onChangeCode").keypress(function() {
            edit = true;
        });
        $("#onChangeCode").one('paste', function() {
            edit = true;
        });
        $("#onChangeCode").blur(function() {
            if (edit == false) {
                $("#idButtonSave").show();
            }
        });
        $("#onChangeCode").on('change', function() {
            // goi len controll de kiem tra xem code 1 da ton tai
            var _this = $(this);
            if ($('#onChangeCode').val() != '') {
                _this.addClass('loading');
                $.ajax({
                    url: '<?php echo $html->url(array('action' => 'checkCode1', $this->data['Project']['id'])); ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        data: {
                            code: $('#onChangeCode').val()
                        }
                    },
                    success: function(respon) {
                        if (respon.result == true) {
                            $("#valueOnChange").hide();
                            saveFieldChangeCodeYourForm($('#onChangeCode').val(), 'project_code_1');
                            $("#onChangeCode").addClass('saved');
                            setTimeout(function() {
                                $("#onChangeCode").removeClass('saved');
                            }, 3000);
                        } else { //codeExisted
                            $('#valueOnChange').css('color', 'red');
                            $('#valueOnChange').html(codeExisted + ' ' + '<b>' + respon.data + '</b>');
                            $("#valueOnChange").show();
                            $("#onChangeCode").addClass('error');
                            setTimeout(function() {
                                $("#onChangeCode").removeClass('error');
                            }, 3000);
                        }
                        _this.removeClass('loading');
                    }
                });
            } else {
                $("#valueOnChange").hide();
                saveFieldChangeCodeYourForm($('#onChangeCode').val(), 'project_code_1');
                $("#onChangeCode").addClass('saved');
                setTimeout(function() {
                    $("#onChangeCode").removeClass('saved');
                }, 3000);
            }
        });

        var list_multiselect = $('.multiselect');
        if (list_multiselect.length > 0) {
            var height_of_list = 0;
            $.each(list_multiselect, function(ind, elm) {
                var position = $('#white-space').offset();
                var bottom = position.top - ($(elm).offset().top + $(elm).height()); //500
                var dropdown_height = $(elm).find('.list_multiselect').height(); // 240
                if (dropdown_height > 300) {
                    $(elm).find('.list_multiselect').css('max-height', '300px');
                    dropdown_height = 300;
                }
                if (dropdown_height > bottom) {
                    var inscrease_height = dropdown_height - bottom;
                    if (inscrease_height > height_of_list) height_of_list = inscrease_height;
                }
            });
            $('#white-space').height(height_of_list + 10);
        }

        $('.wd-fields .wd-row').children('.wd-input').each(function() {
            var _this = $(this);
            if (_this.prev().hasClass('clearfix') && _this.next().hasClass('display-block')) {
                _this.addClass('next-line display-block');
            }
        });
        init_multiselect('#ProjectEditForm .wd-multiselect', {
            empty: 0,
            callback: function() {
                var _this = $(this);
                clearTimeout(multiSelect_timeout);
                if (_this.hasClass('disabled') || _this.hasClass('waiting')) {
                    return;
                } else {
                    vs_selectPC_timeout = setTimeout(function() {
                        saveFieldMutiSelectYourForm(_this[0]);
                    }, 1500);
                }
            },
        });
    });
    //---end---
    var prevCategor = '';
    /*
    $('#pseudo-category').on('focus', function(){
    	var _this = $(this);
    	// _this.empty();
    	curretnCate = _this.val();
    	$.ajax({
    		url: <?php echo json_encode($this->Html->url(array('action' => 'hasActivity', $project_id))); ?>,
    		type: 'get',
    		dataType: 'json',
    		beforeSend: function(){
    			_this.closest('.project-change-category').addClass('loading');
    		},
    		success: function(res){
    			if( res == true){ // co activity
    				haveActivity = 'true';
    				set_list_options(_this,limit_cates, curretnCate);
    			}else{ // Neu khong co
    				haveActivity = 'false';
    				set_list_options(_this,all_cates, curretnCate);
    			}
    		},
    		complete: function(){
    			_this.closest('.project-change-category').removeClass('loading');
    		},
    		
    	});
    	
    });
    */
    function checkCategory(elm) {
        var _this = $(elm);
        curretnCate = $('#pseudo-category').val();
        $.ajax({
            url: <?php echo json_encode($this->Html->url(array('action' => 'hasActivity', $project_id))); ?>,
            type: 'get',
            dataType: 'json',
            beforeSend: function() {
                _this.find('.popup-dropdown').addClass('loading');
            },
            success: function(res) {
                if (res == true) { // co activity
                    haveActivity = 'true';
                    set_list_options(_this, limit_cates, curretnCate);
                } else { // Neu khong co
                    haveActivity = 'false';
                    set_list_options(_this, all_cates, curretnCate);
                }
            },
            complete: function() {
                _this.find('.popup-dropdown').removeClass('loading');
            },

        });
    }

    function set_list_options(elm, newOptions, selected) {
        var options = elm.find('.popup-dropdown').find('a');
        if (options.length) {
            $.each(options, function(i, tag) {
                var _this = $(tag);
                var val = _this.data('value');
                if (val == selected || (val in newOptions)) {
                    _this.parent().show();
                } else {
                    _this.parent().hide();
                }
            });
        }
    }

    function changeCategory(elm) {
        // console.log(elm);
        $('.wd-main-content').addClass('loading');
        if (validateForm()) {
            var newCate = $('#pseudo-category').val();
            if ((curretnCate == 1 && newCate == 2) || (curretnCate == 2 && newCate == 1)) {
                var reFlag = false;
                var program = $('#ProjectProjectAmrProgramId').val();
                var activate_family_linked_program = <?php echo json_encode($activate_family_linked_program); ?>;
                var projectName = $('#ProjectProjectName').val();
                var projectLongName = $('#ProjectLongProjectName').val();
                $('#ActivityLinkedName').val(projectName);
                $('#ActivityLinkedNameDetail').val(projectLongName);
                $('#ActivityLinkedShortName').val(projectName);
                if (curretnCate == 1 && newCate == 2) { // chuyen In progress to Opportunity
                    if (haveActivity === 'true') {
                        $("#change_category p").html("<?php echo sprintf(__("The following employees have already worked in project", true)); ?>");
                        createDialog();
                        $("#change_category").dialog('option', {
                            title: 'Notice'
                        }).dialog('open');
                        // $('#pseudo-category').val(curretnCate);
                        wd_dropdown_setvalue($('#pseudo-category'), curretnCate);
                        $('.wd-main-content').removeClass('loading');
                        return false;
                    } else {
                        var category = $('#pseudo-category').val();
                        $.ajax({
                            type: 'POST',
                            url: '<?php echo $html->url(array('action' => 'deleteActivityLinked', $this->data['Project']['id'])); ?>',
                            cache: false,
                            data: {
                                category: category
                            },
                            success: function(data) {
                                $('#ProjectTmpActivityId').val(-1);
                            },
                            complete: function() {
                                $('.wd-main-content').removeClass('loading');
                                curretnCate = category;
                            }
                        });
                    }
                    return false;
                } else if (curretnCate == 2 && newCate == 1) { // chuyen Opportunity to In progress 
                    if (activate_family_linked_program) {
                        if (program) { //lay famiy anh sub family
                            setTimeout(function() {
                                $.ajax({
                                    type: 'POST',
                                    url: '<?php echo $html->url(array('action' => 'getFamily')); ?>' + '/' + program,
                                    cache: false,
                                    success: function(data) {
                                        var _famId = JSON.parse(data);
                                        $('#ActivityLinkedFamily').val(_famId);
                                        listSubFamily();
                                    }
                                });

                            }, 200);
                            ///do nothing
                        } else {
                            alert("<?php echo sprintf(__("%s is mandatory to change the status", true), __d(sprintf($_domain, 'Details'), 'Program', true)); ?>");
                            $('.wd-main-content').removeClass('loading');
                            // $('#pseudo-category').val(curretnCate);
                            wd_dropdown_setvalue($('#pseudo-category'), curretnCate);
                            return false;
                        }
                    }

                    if ($opportunity_to_in_progress_without_validation != 1) {
                        createDialog();
                        $("#save_activity_linked").dialog('option', {
                            title: ''
                        }).dialog('open');
                        $('.wd-main-content').removeClass('loading');
                        $('#ok_save_ac_linked').click(function() {
                            var familyLinked = $('#ActivityLinkedFamily').val();
                            if (familyLinked) {
                                ///do nothing
                            } else {
                                alert("<?php echo sprintf(__("%s is mandatory, Please select %s linked to a family", true), 'Family', __d(sprintf($_domain, 'Details'), 'Program', true)); ?>");
                                // $('#pseudo-category').val(curretnCate);
                                wd_dropdown_setvalue($('#pseudo-category'), curretnCate);
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
                                type: 'POST',
                                url: '<?php echo $html->url(array('action' => 'saveActivityLinked', $project_name['Project']['id'], $this->data['Company']['id'])); ?>',
                                data: {
                                    data: datas
                                },
                                cache: false,
                                beforeSend: function() {
                                    $("#save_activity_linked").dialog('close');
                                },
                                success: function(data) {
                                    var id_acti = JSON.parse(data);
                                    $('#ProjectTmpActivityId').val(id_acti);
                                    $('#ProjectActivated').val($('#ActivityLinkedActivated').val());
                                    // $('#ProjectEditForm').submit();
                                },
                                complete: function() {
                                    // curretnCate = $('#pseudo-category').val();
                                    wd_dropdown_setvalue($('#pseudo-category'), curretnCate);
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
                            type: 'POST',
                            url: '<?php echo $html->url(array('action' => 'saveActivityLinked', $project_name['Project']['id'], $this->data['Company']['id'])); ?>',
                            data: {
                                data: datas
                            },
                            cache: false,
                            beforeSend: function() {
                                $("#save_activity_linked").dialog('close');
                            },
                            success: function(data) {
                                var id_acti = JSON.parse(data);
                                $('#ProjectTmpActivityId').val(id_acti);
                                $('#ProjectActivated').val($('#ActivityLinkedActivated').val());
                                // $('#ProjectEditForm').submit();
                            },
                            complete: function() {
                                $('.wd-main-content').removeClass('loading');
                                curretnCate = $('#pseudo-category').val();
                            }
                        });
                    }
                }
                return reFlag;
            } else saveFieldSelectYourForm(elm, 'category');
        } else {
            if (curretnCate) {
                // $(elm).val(curretnCate);
                wd_dropdown_setvalue($('#pseudo-category'), curretnCate);
            }
            $('.wd-main-content').removeClass('loading');
        }
    }

    function validateForm() {
        if (!checkConsultant()) return false;
        var flag = true,
            flag1 = true;
        $("#flashMessage").hide();
        $('div.error-message').remove();
        $("div.wd-input input, select").removeClass("form-error");
        var startDate = $("#ProjectStartDate");
        if (startDate.length) {
            if (!(isDate('ProjectStartDate'))) {
                startDate.addClass("form-error");
                var parentElem = startDate.parent();
                parentElem.addClass("error");
                parentElem.append('<div class="error-message">' + "<?php __("Invalid Date (Valid format is dd-mm-yyyy)") ?>" + '</div>');
                flag1 = flag = false;
            }
        }
        var endDate = $("#ProjectEndDate");
        if (endDate.length) {
            if (!(isDate('ProjectEndDate'))) {
                endDate.addClass("form-error");
                var parentElem = endDate.parent();
                parentElem.addClass("error");
                parentElem.append('<div class="error-message">' + "<?php __("Invalid Date (Valid format is dd-mm-yyyy)") ?>" + '</div>');
                flag1 = flag = false;
            }
        }
        if ($('.wd-combobox').html() == '') {
            var projectManage = $(".wd-combobox");
            projectManage.addClass("form-error");
            var parentElem = projectManage.parent();
            projectManage.addClass("error");
            parentElem.append('<div class="error-message" style="padding-left: 0px !important; margin-left: -1px;">' + "<?php __("The field is not blank.") ?>" + '</div>');
            flag1 = flag = false;
        }
        var isNotEmpty = startDate.val() && endDate.val();
        if (flag) {
            if (isNotEmpty) {
                if (compareDate('ProjectStartDate', 'ProjectEndDate') > 0) {
                    var endDate = $("#ProjectEndDate");
                    endDate.addClass("form-error");
                    var parentElem = endDate.parent();
                    parentElem.addClass("error");
                    parentElem.append('<div class="error-message">' + '<?php __("The end date must be greater than start date.") ?>' + '</div>');
                    flag1 = false;
                }
            }
        } else return false;
        return flag1;
    }

    function isNotEmpty1(elementId) {
        var date = $("#" + elementId).val();
        if (date == "") {
            var endDate = $("#" + elementId);
            var parentElem = endDate.parent();
            parentElem.addClass("error");
            endDate.addClass("form-error");
            parentElem.append('<div class="error-message"><?php __('This field is not blank.') ?></div>');
            return false;
        }
        return true;
    }

    function compareDate_(startDateId, endDateId) {
        var defaultMessage = 'The end date must be greater than start date';
        var tmp = compareDate(startDateId, endDateId);
        if (tmp == 1) {
            var endDate = $("#" + endDateId);
            var parentElem = endDate.parent();
            endDate.addClass("form-error");
            parentElem.addClass("error");
            parentElem.append('<div class="error-message">' + "<?php __("The end date must be greater than start date") ?>" + '</div>');
            return false;
        } else {
            return true;
        }
    }
    checkConsultant();

    function checkConsultant() {
        <?php
        $employee_info = $this->Session->read('Auth.employee_info');
        if (isset($employeeInfo['Role']['name']) && $employeeInfo['Role']['name'] === 'admin') { ?>
            return true;
        <?php } else if (($employee_info["Employee"]['is_sas'] == 0 && $employee_info["Role"]["name"] == "conslt") || (isset($profileName['ProfileProjectManager']['can_change_status_project']) && (empty($profileName['ProfileProjectManager']['can_change_status_project']) || $profileName['ProfileProjectManager']['can_change_status_project'] == 0))) {
        ?>
            $(".wd-submit").hide();
            return false;
        <?php
        } else
            echo "return true";
        ?>
    }

    $(document).ready(function() {
        // tinymce editor
        var readonly = 0;
        if (showAllFieldYourform == 0) {
            readonly = 1;
        }
        tinymce.init({
            selector: '.tinymce-editor',
            autoresize_min_height: 150,
            autoresize_bottom_margin: 0,
            readonly: readonly,
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
            relative_urls: false,
            remove_script_host: true,
            convert_urls: true,

            entity_encoding: 'raw',
            entities: '160,nbsp,162,cent,8364,euro,163,pound',
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
                ed.on('focusout', function(e) {
                    var field = $(ed.getBody()).attr('data-id');
                    var content = ed.getContent();
                    $.ajax({
                        url: '<?php echo $html->url(array('action' => 'saveFieldYourFormEditer', $this->data['Project']['id'])); ?>',
                        type: 'POST',
                        data: {
                            field: field,
                            value: content
                        },
                    });
                });
            },
            image_caption: true,
            paste_data_images: true,
            automatic_uploads: true,
            images_upload_url: '/user_files/upload_editor_image',

            file_browser_callback: function(field_name, url, type, win) {
                if (type == 'image') {
                    $('#temp-upload-form input').data({
                        name: field_name,
                        win: win
                    }).click();
                }
            }
        });
        $('.wd-date').datepicker({
            dateFormat: 'dd-mm-yy'
        });
        $('.wd-date-mm-yy').datepicker({
            dateFormat: 'mm-yy'
        });
        $('.wd-date-yy').datepicker({
            dateFormat: 'yy'
        });
        $('.numeric-value').keypress(function(e) {
            var key = e.keyCode ? e.keyCode : e.which;
            if (!key || key == 8 || key == 13) {
                return;
            }
            var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
            if (!/^[\-]?([0-9]{0,12})(\.[0-9]{0,2})?$/.test(val)) {
                e.preventDefault();
                return false;
            }
        });
        // $('#pseudo-category').change(function(){
        // $('#ProjectCategory').val($(this).val());
        // });
        var height;
        var _textArea = $('#ProjectIssues,#ProjectPrimaryObjectives,#ProjectProjectObjectives,#ProjectConstraint,#ProjectRemark, .resizeOnFocus');
        $.each(_textArea, function(key, el) {
            if (el) $(el).height($(el).get(0).scrollHeight);

        });
        _textArea.focus(function() {
            $(this).tooltip('disable');
            $(this).height($(this).get(0).scrollHeight);
        }).mouseup(function() {
            $(this).tooltip('close');
        }).blur(function() {
            $(this).tooltip('option', 'content', $(this).val());
            $(this).tooltip('enable');
            $(this).stop().animate({
                height: height
            }, 300, function() {
                $(this).css({
                    height: ''
                });
            });
        }).tooltip({
            maxWidth: 1000,
            maxHeight: 300,
            content: function(target) {
                return $(target).val();
            }
        });
        // tooltip checkbox
        $('#ProjectIsStaffing').focus(function() {
            $(this).tooltip('option', 'content', '<?php echo __('Take into account this project in total workload', true); ?>');
            $(this).tooltip('enable');
        }).mouseup(function() {
            $(this).tooltip('close');
        }).blur(function() {
            $(this).tooltip('option', 'content', '<?php echo __('Take into account this project in total workload', true); ?>');
            $(this).tooltip('enable');
        }).tooltip({
            maxWidth: 1000,
            maxHeight: 300,
            content: function(target) {
                return '<?php echo __('Take into account this project in total workload', true); ?>';
            }
        }).click(function() {
            var val = $(this).prop('checked') ? 1 : 0;
            $('#project-is-staffing').val(val);
        });
        $('#ProjectProjectName').maxlength({
            events: [], // Array of events to be triggerd
            maxCharacters: <?php echo $projectNameLength ?>, // Characters limit
            status: false, // True to show status indicator bewlow the element
            statusClass: "status", // The class on the status div
            statusText: "character left", // The status text
            notificationClass: "notification", // Will be added when maxlength is reached
            showAlert: false, // True to show a regular alert message
            alertText: "You have typed too many characters.", // Text in alert message
            slider: false // True Use counter slider
        });


        $("#ProjectCreatedValue").live('blur', function() {
            var number = $(this).val();
            var number = $.formatNumber(number, {
                format: "#",
                locale: "us"
            });
            $(this).val(number);
        });
        $(".wd-table table").dataTable();
        $('#ProjectBudget').keypress(function() {
            var rule = /^([0-9]*)$/;
            var x = $('#ProjectBudget').val();
            $('div.error-message').remove();
            if (!rule.test(x)) {
                var fomrerror = $("#ProjectBudget");
                fomrerror.addClass("form-error");
                var parentElem = fomrerror.parent();
                parentElem.addClass("error");
                parentElem.append('<div class="error-message">' + "<?php __("The Budget must be a number ") ?>" + '</div>');
            } else {
                var fomrerror = $("#ProjectBudget");
                fomrerror.removeClass("form-error");
                $('div.error-message').remove();
            }
        });

        $("#ProjectProjectTypeId").change(function() {
            var project_type_id = $(this).val();
            $.ajax({
                url: '<?php echo $html->url('/projects/get_project_sub_type/') ?>' + project_type_id,
                beforeSend: function() {
                    $("#ProjectProjectSubTypeId").html("<option value=''>Loading...</option>");
                    $("#ProjectProjectSubSubTypeId").html("<option value=''>--Select--</option>");
                },
                success: function(data) {
                    $("#ProjectProjectSubTypeId").html(data);
                    $("#ProjectProjectSubTypeId").removeClass("wd-disable");
                }
            });
        });

        $("#ProjectProjectSubTypeId").change(function() {
            var project_sub_type_id = $(this).val();
            $.ajax({
                url: '<?php echo $html->url('/projects/get_project_sub_sub_type/') ?>' + project_sub_type_id,
                beforeSend: function() {
                    $("#ProjectProjectSubSubTypeId").html("<option value=''>Loading...</option>");
                },
                success: function(data) {
                    $("#ProjectProjectSubSubTypeId").html(data);
                    $("#ProjectProjectSubSubTypeId").removeClass("wd-disable");
                }
            });
        });

        $("#ProjectProjectAmrProgramId").change(function() {
            var program_id = $(this).val();
            $.ajax({
                url: '<?php echo $html->url('/project_amrs/get_sub_program/') ?>' + program_id,
                beforeSend: function() {
                    $("#ProjectProjectAmrSubProgramId").html("<option>Loading...</option>");
                },
                success: function(data) {
                    $("#ProjectProjectAmrSubProgramId").html(data);
                    $("#ProjectProjectAmrSubProgramId").removeClass("wd-disable");
                }
            });
        });
        // $('#ProjectAttachment').live('change', function(){
        //     var form = $("#form_dialog_attachement_or_url");
        //     var _id = $('.active.selected').find('div').attr('data-id');
        //     form.find('input[name="data[Upload][id]"]').val(_id);
        //     form.submit();
        // });
        // disable combobox ProjectActivityId
        var activityRequest = <?php echo $activityRequest ?>;
        if (activityRequest != 0 || activityId != null) {
            $('#ProjectActivityId').attr('disabled', 'disabled');
        }
        // disabled activity da dc chon
        var listIdActivity = <?php echo json_encode($listIdActivity); ?>;
        var project_id = <?php echo $project_name['Project']['id'] ?>;
        $('#ProjectActivityId').change(function() {
            $('#ProjectTmpActivityId').val($(this).val());
        });
        $.each(listIdActivity, function(key, val) {
            $('#ProjectActivityId option').each(function() {
                var valueOption = $(this).attr('value');
                if (valueOption != 0) {
                    if (val == valueOption) {

                        if (project_id == parseInt(key)) {
                            //
                        } else {
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
            // technicalManager = $('#wd-data-project-3').find('.wd-data-manager'),
            functionalLeader = $('#wd-data-project-4').find('.wd-data-manager'),
            uatManager = $('#wd-data-project-5').find('.wd-data-manager'),
            readAccess = $('#wd-data-project-6').find('.wd-data-manager'),
            currentPhase = $('#wd-data-project-7').find('.wd-data-manager'),

            currentPhaseDatas = <?php echo !empty($phasePlans) ? json_encode($phasePlans) : json_encode(array()); ?>,
            ProjectMultiLists = <?php echo !empty($ProjectMultiLists) ? json_encode($ProjectMultiLists) : json_encode(array()); ?>,
            phaseHaveTasks = <?php echo !empty($phaseHaveTasks) ? json_encode($phaseHaveTasks) : json_encode(array()); ?>;

        var initMenuFilter = function($menu, $check) {
            if ($check === 'CB') {
                var $filter = $('<div class="context-menu-filter-2"><span><input type="text" rel="no-history" placeholder="' + i18n['Search'] + '" ></span></div>');
            } else if ($check === 'TM') {
                var $filter = $('<div class="context-menu-filter-3"><span><input type="text" rel="no-history" placeholder="' + i18n['Search'] + '" ></span></div>');
            } else if ($check === 'FL') {
                var $filter = $('<div class="context-menu-filter-4"><span><input type="text" rel="no-history" placeholder="' + i18n['Search'] + '" ></span></div>');
            } else if ($check === 'UM') {
                var $filter = $('<div class="context-menu-filter-5"><span><input type="text" rel="no-history" placeholder="' + i18n['Search'] + '" ></span></div>');
            } else if ($check === 'RA') {
                var $filter = $('<div class="context-menu-filter-6"><span><input type="text" rel="no-history" placeholder="' + i18n['Search'] + '" ></span></div>');
            } else if ($check === 'CR') {
                var $filter = $('<div class="context-menu-filter-7"><span><input type="text" rel="no-history"  placeholder="' + i18n['Search'] + '" ></span></div>');
            } else if ($check === 'ML1') {
                var $filter = $('<div class="context-menu-filter-8"><span><input type="text" rel="no-history" placeholder="' + i18n['Search'] + '" ></span></div>');
            } else if ($check === 'ML2') {
                var $filter = $('<div class="context-menu-filter-9"><span><input type="text" rel="no-history" placeholder="' + i18n['Search'] + '" ></span></div>');
            } else if ($check === 'ML3') {
                var $filter = $('<div class="context-menu-filter-10"><span><input type="text" rel="no-history" placeholder="' + i18n['Search'] + '" ></span></div>');
            } else if ($check === 'ML4') {
                var $filter = $('<div class="context-menu-filter-11"><span><input type="text" rel="no-history" placeholder="' + i18n['Search'] + '" ></span></div>');
            } else if ($check === 'ML5') {
                var $filter = $('<div class="context-menu-filter-12"><span><input type="text" rel="no-history" placeholder="' + i18n['Search'] + '" ></span></div>');
            } else if ($check === 'ML6') {
                var $filter = $('<div class="context-menu-filter-13"><span><input type="text" rel="no-history" placeholder="' + i18n['Search'] + '" ></span></div>');
            } else if ($check === 'ML7') {
                var $filter = $('<div class="context-menu-filter-14"><span><input type="text" rel="no-history" placeholder="' + i18n['Search'] + '" ></span></div>');
            } else if ($check === 'ML8') {
                var $filter = $('<div class="context-menu-filter-15"><span><input type="text" rel="no-history" placeholder="' + i18n['Search'] + '" ></span></div>');
            } else if ($check === 'ML9') {
                var $filter = $('<div class="context-menu-filter-16"><span><input type="text" rel="no-history" placeholder="' + i18n['Search'] + '" ></span></div>');
            } else {
                var $filter = $('<div class="context-menu-filter-17"><span><input type="text" rel="no-history" placeholder="' + i18n['Search'] + '" ></span></div>');
            }
            $menu.before($filter);

            var timeoutID = null,
                searchHandler = function() {
                    var val = $(this).val();
                    var te = $($menu).find('.wd-data-manager .wd-data span').html();

                    $($menu).find('.wd-data-manager .wd-data span').each(function() {
                        var $label = $(this).html();
                        $label = $label.toLowerCase();
                        val = val.toLowerCase();
                        if (!val.length || $label.indexOf(val) != -1 || !val) {
                            $(this).parent().css('display', 'block');
                            $(this).parent().next().css('display', 'block');
                        } else {
                            $(this).parent().css('display', 'none');
                            $(this).parent().next().css('display', 'none');
                        }
                    });
                };

            $filter.find('input').click(function(e) {
                e.stopImmediatePropagation();
            }).keyup(function() {
                var self = this;
                clearTimeout(timeoutID);
                timeoutID = setTimeout(function() {
                    searchHandler.call(self);
                }, 200);
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

        $('.wd-combobox-2').click(function() {
            if ($(this).closest('.multiselect').hasClass('disabled')) return false;
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7, .wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if (showAllFieldYourform != 0) {
                if (checked) {
                    $('#wd-data-project-2').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-2').css('display', 'none');
                } else {
                    $('#wd-data-project-2').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-2').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '100%',
                        'z-index': 2
                    });
                    // $('#wd-data-project-2 div:first-child').css('padding-top', '33px');
                }
            }
            return false;
        });
        $('.wd-combobox-3').click(function() {
            if ($(this).closest('.multiselect').hasClass('disabled')) return false;
            var checked = $(this).attr('checked');
            $('#wd-data-project-2, #wd-data-project, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox-2, .wd-combobox, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7, .wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if (showAllFieldYourform != 0) {
                if (checked) {
                    $('#wd-data-project-3').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-3').css('display', 'none');
                } else {
                    $('#wd-data-project-3').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-3').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '100%',
                        'z-index': 2
                    });
                    // $('#wd-data-project-3 div:first-child').css('padding-top', '33px');
                }
            }
            return false;
        });
        $('.wd-combobox-4').click(function() {
            if ($(this).closest('.multiselect').hasClass('disabled')) return false;
            var checked = $(this).attr('checked');
            $('#wd-data-project-2, #wd-data-project, #wd-data-project-3, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox-2, .wd-combobox, .wd-combobox-3, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7, .wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if (showAllFieldYourform != 0) {
                if (checked) {
                    $('#wd-data-project-4').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-4').css('display', 'none');
                } else {
                    $('#wd-data-project-4').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-4').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '100%',
                        'z-index': 2
                    });
                    // $('#wd-data-project-4 div:first-child').css('padding-top', '33px');
                }
            }
            return false;
        });
        $('.wd-combobox-5').click(function() {
            if ($(this).closest('.multiselect').hasClass('disabled')) return false;
            var checked = $(this).attr('checked');
            $('#wd-data-project-2, #wd-data-project, #wd-data-project-3, #wd-data-project-4,  #wd-data-project-6,  #wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox-2, .wd-combobox, .wd-combobox-3, .wd-combobox-4, .wd-combobox-6, .wd-combobox-7, .wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if (showAllFieldYourform != 0) {
                if (checked) {
                    $('#wd-data-project-5').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-5').css('display', 'none');
                } else {
                    $('#wd-data-project-5').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-5').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '100%',
                        'z-index': 2
                    });
                    // $('#wd-data-project-5 div:first-child').css('padding-top', '33px');
                }
            }
            return false;
        });
        $('.wd-combobox-6').click(function() {
            if ($(this).closest('.multiselect').hasClass('disabled')) return false;
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-7, .wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if (showAllFieldYourform != 0) {
                if (checked) {
                    $('#wd-data-project-6').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-6').css('display', 'none');
                } else {
                    $('#wd-data-project-6').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-6').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '100%',
                        'z-index': 2
                    });
                    // $('#wd-data-project-6 div:first-child').css('padding-top', '33px');
                }
            }
            return false;
        });
        $('.wd-combobox-7').click(function() {
            if ($(this).closest('.multiselect').hasClass('disabled')) return false;
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-8, #wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if (showAllFieldYourform != 0) {
                if (checked) {
                    $('#wd-data-project-7').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-7').css('display', 'none');
                } else {
                    $('#wd-data-project-7').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-7').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '100%',
                        'z-index': 2
                    });
                    // $('#wd-data-project-7 div:first-child').css('padding-top', '33px');
                }
            }
            return false;
        });
        $('.wd-combobox-8').click(function() {
            if ($(this).closest('.multiselect').hasClass('disabled')) return false;
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-9, #wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if (showAllFieldYourform != 0) {
                if (checked) {
                    $('#wd-data-project-8').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-8').css('display', 'none');
                } else {
                    $('#wd-data-project-8').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-8').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '100%',
                        'top': '100%',
                        'z-index': 2
                    });
                    // $('#wd-data-project-8 div:first-child').css('padding-top', '33px');
                }
            }
            return false;
        });
        $('.wd-combobox-9').click(function() {
            if ($(this).closest('.multiselect').hasClass('disabled')) return false;
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-8, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7,.wd-combobox-8, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if (showAllFieldYourform != 0) {
                if (checked) {
                    $('#wd-data-project-9').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-9').css('display', 'none');
                } else {
                    $('#wd-data-project-9').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-9').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '100%',
                        'top': '100%',
                        'z-index': 2
                    });
                    // $('#wd-data-project-9 div:first-child').css('padding-top', '33px');
                }
            }
            return false;
        });
        $('.wd-combobox-10').click(function() {
            if ($(this).closest('.multiselect').hasClass('disabled')) return false;
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7,.wd-combobox-8, .wd-combobox-9, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if (showAllFieldYourform != 0) {
                if (checked) {
                    $('#wd-data-project-10').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-10').css('display', 'none');
                } else {
                    $('#wd-data-project-10').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-10').css({
                        'display': 'block',
                        'position': 'absolute',
                        'top': '100%',
                        'width': '100%',
                        'z-index': 2
                    });
                    // $('#wd-data-project-10 div:first-child').css('padding-top', '33px');
                }
            }
            return false;
        });
        $('.wd-combobox-11').click(function() {
            if ($(this).closest('.multiselect').hasClass('disabled')) return false;
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7,.wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if (showAllFieldYourform != 0) {
                if (checked) {
                    $('#wd-data-project-11').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-11').css('display', 'none');
                } else {
                    $('#wd-data-project-11').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-11').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '100%',
                        'top': '100%',
                        'z-index': 2
                    });
                    // $('#wd-data-project-11 div:first-child').css('padding-top', '33px');
                }
            }
            return false;
        });
        $('.wd-combobox-12').click(function() {
            if ($(this).closest('.multiselect').hasClass('disabled')) return false;
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7,.wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if (showAllFieldYourform != 0) {
                if (checked) {
                    $('#wd-data-project-12').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-12').css('display', 'none');
                } else {
                    $('#wd-data-project-12').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-12').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '100%',
                        'top': '100%',
                        'z-index': 2
                    });
                    // $('#wd-data-project-12 div:first-child').css('padding-top', '33px');
                }
            }
            return false;
        });
        $('.wd-combobox-13').click(function() {
            if ($(this).closest('.multiselect').hasClass('disabled')) return false;
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7,.wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if (showAllFieldYourform != 0) {
                if (checked) {
                    $('#wd-data-project-13').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-13').css('display', 'none');
                } else {
                    $('#wd-data-project-13').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-13').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '100%',
                        'top': '100%',
                        'z-index': 2
                    });
                    // $('#wd-data-project-13 div:first-child').css('padding-top', '33px');
                }
            }
            return false;
        });
        $('.wd-combobox-14').click(function() {
            if ($(this).closest('.multiselect').hasClass('disabled')) return false;
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7,.wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if (showAllFieldYourform != 0) {
                if (checked) {
                    $('#wd-data-project-14').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-14').css('display', 'none');
                } else {
                    $('#wd-data-project-14').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-14').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '100%',
                        'top': '100%',
                        'z-index': 2
                    });
                    // $('#wd-data-project-14 div:first-child').css('padding-top', '33px');
                }
            }
            return false;
        });
        $('.wd-combobox-15').click(function() {
            if ($(this).closest('.multiselect').hasClass('disabled')) return false;
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7,.wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
            if (showAllFieldYourform != 0) {
                if (checked) {
                    $('#wd-data-project-15').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-15').css('display', 'none');
                } else {
                    $('#wd-data-project-15').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-15').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '100%',
                        'top': '100%',
                        'z-index': 2
                    });
                    // $('#wd-data-project-15 div:first-child').css('padding-top', '33px');
                }
            }
            return false;
        });
        $('.wd-combobox-16').click(function() {
            if ($(this).closest('.multiselect').hasClass('disabled')) return false;
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-17').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-17').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7,.wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-17').removeAttr('checked');
            if (showAllFieldYourform != 0) {
                if (checked) {
                    $('#wd-data-project-16').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-16').css('display', 'none');
                } else {
                    $('#wd-data-project-16').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-16').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '100%',
                        'top': '100%',
                        'z-index': 2
                    });
                    // $('#wd-data-project-16 div:first-child').css('padding-top', '33px');
                }
            }
            return false;
        });
        $('.wd-combobox-17').click(function() {
            if ($(this).closest('.multiselect').hasClass('disabled')) return false;
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7,.wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16').removeAttr('checked');
            if (showAllFieldYourform != 0) {
                if (checked) {
                    $('#wd-data-project-17').css('display', 'none');
                    $(this).removeAttr('checked');
                    $('.context-menu-filter-17').css('display', 'none');
                } else {
                    $('#wd-data-project-17').css('display', 'block');
                    $(this).attr('checked', 'checked');
                    $('.context-menu-filter-17').css({
                        'display': 'block',
                        'position': 'absolute',
                        'width': '100%',
                        'top': '100%',
                        'z-index': 2
                    });
                    // $('#wd-data-project-17 div:first-child').css('padding-top', '33px');
                }
            }
            return false;
        });
        $('html').click(function(e) {
            if ($(e.target).attr('class') &&
                (
                    ($(e.target).attr('class').split(' ')[0] &&
                        (
                            $(e.target).attr('class').split(' ')[0] == 'projectManager' || $(e.target).attr('class').split(' ')[0] == 'chiefBusiness' || $(e.target).attr('class').split(' ')[0] == 'technicalManager' || $(e.target).attr('class').split(' ')[0] == 'functionalLeader' || $(e.target).attr('class').split(' ')[0] == 'uatManager' || $(e.target).attr('class').split(' ')[0] == 'readAccess' || $(e.target).attr('class').split(' ')[0] == 'currentPhase' ||
                            $(e.target).attr('class').split(' ')[0] == 'listMulti' || $(e.target).attr('class').split(' ')[0] == 'listMulti_1' || $(e.target).attr('class').split(' ')[0] == 'listMulti_2' || $(e.target).attr('class').split(' ')[0] == 'listMulti_3' || $(e.target).attr('class').split(' ')[0] == 'listMulti_4' || $(e.target).attr('class').split(' ')[0] == 'listMulti_5' ||
                            $(e.target).attr('class').split(' ')[0] == 'listMulti_6' || $(e.target).attr('class').split(' ')[0] == 'listMulti_7' || $(e.target).attr('class').split(' ')[0] == 'listMulti_8' || $(e.target).attr('class').split(' ')[0] == 'listMulti_9' || $(e.target).attr('class').split(' ')[0] == 'listMulti_10'
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
                )) {
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
         * Phan chon cac phan tu trong combobox cua readAccess
         */
        var $ids_7 = [];
        //var _phaseHaveTasks = $.map(phaseHaveTasks, function(value, index) {
        //            return value;
        //        });
        currentPhase.each(function() {
            var data = $(this).find('.wd-data');
            var backup = $(this).find('.wd-backup');
            /**
             * When load data
             */
            var valList = $(data).find(':checkbox').val();
            if (currentPhaseDatas) {
                $.each(currentPhaseDatas, function(idPlan, idPhase) {
                    if (valList == idPhase) {
                        $(data).find(':checkbox').attr('checked', 'checked');
                        if ($.inArray(idPhase, $ids_7) != -1) {
                            //do nothing
                        } else {
                            $('a.wd-combobox-7').append('<span class="wd-dt-' + valList + '">' + $('.wd-group-' + valList).find('span').html() + '<span class="wd-bk-' + valList + '"></span></span><span class="wd-em-' + valList + '">, </span>');
                            $ids_7.push(idPhase);
                        }
                    }

                });
            }
            /**
             * When click in checkbox
             */
            $(data).find(':checkbox').click(function() {
                var _datas = $(this).val();
                if ($(this).is(':checked')) {
                    $ids_7.push(_datas);
                    $('a.wd-combobox-7').append('<span class="wd-dt-' + _datas + '">' + $(data).find('span').html() + '<span class="wd-bk-' + _datas + '"></span></span><span class="wd-em-' + _datas + '">, </span>');
                } else {
                    $ids_7 = jQuery.removeFromArray(_datas, $ids_7);
                    $('a.wd-combobox-7').find('.wd-dt-' + _datas).remove();
                    $('a.wd-combobox-7').find('.wd-em-' + _datas).remove();
                }
            });
        });
        //list multi select
        // list 1
        var $ids_8 = [];
        listMultiSelect = $('#wd-data-project-8').find('.listMulti.wd-data-manager');
        listMultiSelect.each(function() {
            var data = $(this).find('.wd-data');
            /**
             * When load data
             */
            var valList = $(data).find(':checkbox').val();
            if (ProjectMultiLists['project_list_multi_1']) {
                $.each(ProjectMultiLists['project_list_multi_1'], function(idPlan, idPhase) {
                    if (valList == idPhase) {
                        $(data).find(':checkbox').attr('checked', 'checked');
                        if ($.inArray(idPhase, $ids_8) != -1) {
                            //do nothing
                        } else {
                            $('a.wd-combobox-8').append('<span class="wd-dt-' + valList + '">' + $('.listMulti.wd-data-manager.wd-group-' + valList).find('span').html() + '<span class="wd-bk-' + valList + '"></span></span><span class="wd-em-' + valList + '">, </span>');
                            $ids_8.push(idPhase);
                        }
                    }

                });
            }
            /**
             * When click in checkbox
             */
            $(data).find(':checkbox').on('click', function() {
                var _datas = $(this).val();
                if ($(this).is(':checked')) {
                    $ids_8.push(_datas);
                    $('a.wd-combobox-8').append('<span class="wd-dt-' + _datas + '">' + $(data).find('span').html() + '<span class="wd-bk-' + _datas + '"></span></span><span class="wd-em-' + _datas + '">, </span>');
                } else {
                    $ids_8 = jQuery.removeFromArray(_datas, $ids_8);
                    $('a.wd-combobox-8').find('.wd-dt-' + _datas).remove();
                    $('a.wd-combobox-8').find('.wd-em-' + _datas).remove();
                }
            });
        });
        // list 2
        var $ids_9 = [];
        listMultiSelect = $('#wd-data-project-9').find('.listMulti.wd-data-manager');
        listMultiSelect.each(function() {
            var data = $(this).find('.wd-data');
            /**
             * When load data
             */
            var valList = $(data).find(':checkbox').val();
            if (ProjectMultiLists['project_list_multi_2']) {
                $.each(ProjectMultiLists['project_list_multi_2'], function(idPlan, idPhase) {
                    if (valList == idPhase) {
                        $(data).find(':checkbox').attr('checked', 'checked');
                        if ($.inArray(idPhase, $ids_9) != -1) {
                            //do nothing
                        } else {
                            $('a.wd-combobox-9').append('<span class="wd-dt-' + valList + '">' + $('.listMulti.wd-data-manager.wd-group-' + valList).find('span').html() + '<span class="wd-bk-' + valList + '"></span></span><span class="wd-em-' + valList + '">, </span>');
                            $ids_9.push(idPhase);
                        }
                    }

                });
            }
            /**
             * When click in checkbox
             */
            $(data).find(':checkbox').click(function() {
                var _datas = $(this).val();
                if ($(this).is(':checked')) {
                    $ids_9.push(_datas);
                    $('a.wd-combobox-9').append('<span class="wd-dt-' + _datas + '">' + $(data).find('span').html() + '<span class="wd-bk-' + _datas + '"></span></span><span class="wd-em-' + _datas + '">, </span>');
                } else {
                    $ids_9 = jQuery.removeFromArray(_datas, $ids_9);
                    $('a.wd-combobox-9').find('.wd-dt-' + _datas).remove();
                    $('a.wd-combobox-9').find('.wd-em-' + _datas).remove();
                }
            });
        });
        // lisst 3
        var $ids_10 = [];
        listMultiSelect = $('#wd-data-project-10').find('.listMulti.wd-data-manager');
        listMultiSelect.each(function() {
            var data = $(this).find('.wd-data');
            /**
             * When load data
             */
            var valList = $(data).find(':checkbox').val();
            if (ProjectMultiLists['project_list_multi_3']) {
                $.each(ProjectMultiLists['project_list_multi_3'], function(idPlan, idPhase) {
                    if (valList == idPhase) {
                        $(data).find(':checkbox').attr('checked', 'checked');
                        if ($.inArray(idPhase, $ids_10) != -1) {
                            //do nothing
                        } else {
                            $('a.wd-combobox-10').append('<span class="wd-dt-' + valList + '">' + $('.listMulti.wd-data-manager.wd-group-' + valList).find('span').html() + '<span class="wd-bk-' + valList + '"></span></span><span class="wd-em-' + valList + '">, </span>');
                            $ids_10.push(idPhase);
                        }
                    }

                });
            }
            /**
             * When click in checkbox
             */
            $(data).find(':checkbox').click(function() {
                var _datas = $(this).val();
                if ($(this).is(':checked')) {
                    $ids_10.push(_datas);
                    $('a.wd-combobox-10').append('<span class="wd-dt-' + _datas + '">' + $(data).find('span').html() + '<span class="wd-bk-' + _datas + '"></span></span><span class="wd-em-' + _datas + '">, </span>');
                } else {
                    $ids_10 = jQuery.removeFromArray(_datas, $ids_10);
                    $('a.wd-combobox-10').find('.wd-dt-' + _datas).remove();
                    $('a.wd-combobox-10').find('.wd-em-' + _datas).remove();
                }
            });
        });
        // list 4
        var $ids_11 = [];
        listMultiSelect = $('#wd-data-project-11').find('.listMulti.wd-data-manager');
        listMultiSelect.each(function() {
            var data = $(this).find('.wd-data');
            /**
             * When load data
             */
            var valList = $(data).find(':checkbox').val();
            if (ProjectMultiLists['project_list_multi_4']) {
                $.each(ProjectMultiLists['project_list_multi_4'], function(idPlan, idPhase) {
                    if (valList == idPhase) {
                        $(data).find(':checkbox').attr('checked', 'checked');
                        if ($.inArray(idPhase, $ids_11) != -1) {
                            //do nothing
                        } else {
                            $('a.wd-combobox-11').append('<span class="wd-dt-' + valList + '">' + $('.listMulti.wd-data-manager.wd-group-' + valList).find('span').html() + '<span class="wd-bk-' + valList + '"></span></span><span class="wd-em-' + valList + '">, </span>');
                            $ids_11.push(idPhase);
                        }
                    }

                });
            }
            /**
             * When click in checkbox
             */
            $(data).find(':checkbox').click(function() {
                var _datas = $(this).val();
                if ($(this).is(':checked')) {
                    $ids_11.push(_datas);
                    $('a.wd-combobox-11').append('<span class="wd-dt-' + _datas + '">' + $(data).find('span').html() + '<span class="wd-bk-' + _datas + '"></span></span><span class="wd-em-' + _datas + '">, </span>');
                } else {
                    $ids_11 = jQuery.removeFromArray(_datas, $ids_11);
                    $('a.wd-combobox-11').find('.wd-dt-' + _datas).remove();
                    $('a.wd-combobox-11').find('.wd-em-' + _datas).remove();
                }
            });
        });
        //list 5
        var $ids_12 = [];
        listMultiSelect = $('#wd-data-project-12').find('.listMulti.wd-data-manager');
        listMultiSelect.each(function() {
            var data = $(this).find('.wd-data');
            /**
             * When load data
             */
            var valList = $(data).find(':checkbox').val();
            if (ProjectMultiLists['project_list_multi_5']) {
                $.each(ProjectMultiLists['project_list_multi_5'], function(idPlan, idPhase) {
                    if (valList == idPhase) {
                        $(data).find(':checkbox').attr('checked', 'checked');
                        if ($.inArray(idPhase, $ids_8) != -1) {
                            //do nothing
                        } else {
                            $('a.wd-combobox-12').append('<span class="wd-dt-' + valList + '">' + $('.listMulti.wd-data-manager.wd-group-' + valList).find('span').html() + '<span class="wd-bk-' + valList + '"></span></span><span class="wd-em-' + valList + '">, </span>');
                            $ids_12.push(idPhase);
                        }
                    }

                });
            }
            /**
             * When click in checkbox
             */
            $(data).find(':checkbox').click(function() {
                var _datas = $(this).val();
                if ($(this).is(':checked')) {
                    $ids_12.push(_datas);
                    $('a.wd-combobox-12').append('<span class="wd-dt-' + _datas + '">' + $(data).find('span').html() + '<span class="wd-bk-' + _datas + '"></span></span><span class="wd-em-' + _datas + '">, </span>');
                } else {
                    $ids_12 = jQuery.removeFromArray(_datas, $ids_12);
                    $('a.wd-combobox-12').find('.wd-dt-' + _datas).remove();
                    $('a.wd-combobox-12').find('.wd-em-' + _datas).remove();
                }
            });
        });
        // list 6
        var $ids_13 = [];
        listMultiSelect = $('#wd-data-project-13').find('.listMulti.wd-data-manager');
        listMultiSelect.each(function() {
            var data = $(this).find('.wd-data');
            /**
             * When load data
             */
            var valList = $(data).find(':checkbox').val();
            if (ProjectMultiLists['project_list_multi_6']) {
                $.each(ProjectMultiLists['project_list_multi_6'], function(idPlan, idPhase) {
                    if (valList == idPhase) {
                        $(data).find(':checkbox').attr('checked', 'checked');
                        if ($.inArray(idPhase, $ids_13) != -1) {
                            //do nothing
                        } else {
                            $('a.wd-combobox-13').append('<span class="wd-dt-' + valList + '">' + $('.listMulti.wd-data-manager.wd-group-' + valList).find('span').html() + '<span class="wd-bk-' + valList + '"></span></span><span class="wd-em-' + valList + '">, </span>');
                            $ids_13.push(idPhase);
                        }
                    }

                });
            }
            /**
             * When click in checkbox
             */
            $(data).find(':checkbox').click(function() {
                var _datas = $(this).val();
                if ($(this).is(':checked')) {
                    $ids_13.push(_datas);
                    $('a.wd-combobox-13').append('<span class="wd-dt-' + _datas + '">' + $(data).find('span').html() + '<span class="wd-bk-' + _datas + '"></span></span><span class="wd-em-' + _datas + '">, </span>');
                } else {
                    $ids_13 = jQuery.removeFromArray(_datas, $ids_13);
                    $('a.wd-combobox-13').find('.wd-dt-' + _datas).remove();
                    $('a.wd-combobox-13').find('.wd-em-' + _datas).remove();
                }
            });
        });
        // list 7
        var $ids_14 = [];
        listMultiSelect = $('#wd-data-project-14').find('.listMulti.wd-data-manager');
        listMultiSelect.each(function() {
            var data = $(this).find('.wd-data');
            /**
             * When load data
             */
            var valList = $(data).find(':checkbox').val();
            if (ProjectMultiLists['project_list_multi_7']) {
                $.each(ProjectMultiLists['project_list_multi_7'], function(idPlan, idPhase) {
                    if (valList == idPhase) {
                        $(data).find(':checkbox').attr('checked', 'checked');
                        if ($.inArray(idPhase, $ids_14) != -1) {
                            //do nothing
                        } else {
                            $('a.wd-combobox-14').append('<span class="wd-dt-' + valList + '">' + $('.listMulti.wd-data-manager.wd-group-' + valList).find('span').html() + '<span class="wd-bk-' + valList + '"></span></span><span class="wd-em-' + valList + '">, </span>');
                            $ids_14.push(idPhase);
                        }
                    }

                });
            }
            /**
             * When click in checkbox
             */
            $(data).find(':checkbox').click(function() {
                var _datas = $(this).val();
                if ($(this).is(':checked')) {
                    $ids_14.push(_datas);
                    $('a.wd-combobox-14').append('<span class="wd-dt-' + _datas + '">' + $(data).find('span').html() + '<span class="wd-bk-' + _datas + '"></span></span><span class="wd-em-' + _datas + '">, </span>');
                } else {
                    $ids_14 = jQuery.removeFromArray(_datas, $ids_14);
                    $('a.wd-combobox-14').find('.wd-dt-' + _datas).remove();
                    $('a.wd-combobox-14').find('.wd-em-' + _datas).remove();
                }
            });
        });
        // list 8
        var $ids_15 = [];
        listMultiSelect = $('#wd-data-project-15').find('.listMulti.wd-data-manager');
        listMultiSelect.each(function() {
            var data = $(this).find('.wd-data');
            /**
             * When load data
             */
            var valList = $(data).find(':checkbox').val();
            if (ProjectMultiLists['project_list_multi_8']) {
                $.each(ProjectMultiLists['project_list_multi_8'], function(idPlan, idPhase) {
                    if (valList == idPhase) {
                        $(data).find(':checkbox').attr('checked', 'checked');
                        if ($.inArray(idPhase, $ids_15) != -1) {
                            //do nothing
                        } else {
                            $('a.wd-combobox-15').append('<span class="wd-dt-' + valList + '">' + $('.listMulti.wd-data-manager.wd-group-' + valList).find('span').html() + '<span class="wd-bk-' + valList + '"></span></span><span class="wd-em-' + valList + '">, </span>');
                            $ids_15.push(idPhase);
                        }
                    }

                });
            }
            /**
             * When click in checkbox
             */
            $(data).find(':checkbox').click(function() {
                var _datas = $(this).val();
                if ($(this).is(':checked')) {
                    $ids_15.push(_datas);
                    $('a.wd-combobox-15').append('<span class="wd-dt-' + _datas + '">' + $(data).find('span').html() + '<span class="wd-bk-' + _datas + '"></span></span><span class="wd-em-' + _datas + '">, </span>');
                } else {
                    $ids_15 = jQuery.removeFromArray(_datas, $ids_15);
                    $('a.wd-combobox-15').find('.wd-dt-' + _datas).remove();
                    $('a.wd-combobox-15').find('.wd-em-' + _datas).remove();
                }
            });
        });
        // list 9
        var $ids_16 = [];
        listMultiSelect = $('#wd-data-project-16').find('.listMulti.wd-data-manager');
        listMultiSelect.each(function() {
            var data = $(this).find('.wd-data');
            /**
             * When load data
             */
            var valList = $(data).find(':checkbox').val();
            if (ProjectMultiLists['project_list_multi_9']) {
                $.each(ProjectMultiLists['project_list_multi_9'], function(idPlan, idPhase) {
                    if (valList == idPhase) {
                        $(data).find(':checkbox').attr('checked', 'checked');
                        if ($.inArray(idPhase, $ids_16) != -1) {
                            //do nothing
                        } else {
                            $('a.wd-combobox-16').append('<span class="wd-dt-' + valList + '">' + $('.listMulti.wd-data-manager.wd-group-' + valList).find('span').html() + '<span class="wd-bk-' + valList + '"></span></span><span class="wd-em-' + valList + '">, </span>');
                            $ids_16.push(idPhase);
                        }
                    }

                });
            }
            /**
             * When click in checkbox
             */
            $(data).find(':checkbox').click(function() {
                var _datas = $(this).val();
                if ($(this).is(':checked')) {
                    $ids_16.push(_datas);
                    $('a.wd-combobox-16').append('<span class="wd-dt-' + _datas + '">' + $(data).find('span').html() + '<span class="wd-bk-' + _datas + '"></span></span><span class="wd-em-' + _datas + '">, </span>');
                } else {
                    $ids_16 = jQuery.removeFromArray(_datas, $ids_16);
                    $('a.wd-combobox-16').find('.wd-dt-' + _datas).remove();
                    $('a.wd-combobox-16').find('.wd-em-' + _datas).remove();
                }
            });
        });
        // list 10
        var $ids_17 = [];
        listMultiSelect = $('#wd-data-project-17').find('.listMulti.wd-data-manager');
        listMultiSelect.each(function() {
            var data = $(this).find('.wd-data');
            /**
             * When load data
             */
            var valList = $(data).find(':checkbox').val();
            if (ProjectMultiLists['project_list_multi_10']) {
                $.each(ProjectMultiLists['project_list_multi_10'], function(idPlan, idPhase) {
                    if (valList == idPhase) {
                        $(data).find(':checkbox').attr('checked', 'checked');
                        if ($.inArray(idPhase, $ids_17) != -1) {
                            //do nothing
                        } else {
                            $('a.wd-combobox-17').append('<span class="wd-dt-' + valList + '">' + $('.listMulti.wd-data-manager.wd-group-' + valList).find('span').html() + '<span class="wd-bk-' + valList + '"></span></span><span class="wd-em-' + valList + '">, </span>');
                            $ids_17.push(idPhase);
                        }
                    }

                });
            }
            /**
             * When click in checkbox
             */
            $(data).find(':checkbox').click(function() {
                var _datas = $(this).val();
                if ($(this).is(':checked')) {
                    $ids_17.push(_datas);
                    $('a.wd-combobox-17').append('<span class="wd-dt-' + _datas + '">' + $(data).find('span').html() + '<span class="wd-bk-' + _datas + '"></span></span><span class="wd-em-' + _datas + '">, </span>');
                } else {
                    $ids_17 = jQuery.removeFromArray(_datas, $ids_17);
                    $('a.wd-combobox-17').find('.wd-dt-' + _datas).remove();
                    $('a.wd-combobox-17').find('.wd-em-' + _datas).remove();
                }
            });
        });
        user_modify = <?php echo json_encode(($canModified && !$_isProfile) || $_canWrite); ?>;
    });
    /**
     * Multiple Upload
     */
    var uploader = $("#uploaderDocument1").pluploadQueue({
        runtimes: 'html5, html4',
        url: "/projects/uploads/" + project_id + '/upload_documents_1',
        chunk_size: '10mb',
        rename: true,
        dragdrop: true,
        filters: {
            max_file_size: '10mb',
            mime_types: [{
                title: "Files",
                extensions: "jpg,jpeg,bmp,gif,png,swf,txt,zip,rar,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,csv,eml,msg,xlsm"
            }]
        },
        init: {
            PostInit: function(up) {
                up.project_id = project_id;
                up.linkedAction = '/projects/attachment/';
                if (projectFiles1 && Object.keys(projectFiles1).length > 0) {
                    up.auditFiles = projectFiles1;
                    var tmpHtml = '';
                    var display_none = '';
                    if (showAllFieldYourform == 0) {
                        display_none = 'display: none';
                    }
                    $.each(projectFiles1, function(ind, val) {
                        var hrefDownload = '/projects/attachment/upload_documents_1' + '/' + project_id + '/' + val.id + '/download/';
                        var hrefDelete = '/projects/attachment/upload_documents_1' + '/' + project_id + '/' + val.id + '/delete/';
                        tmpHtml +=
                            '<li id="' + val.id + '" class="plupload_done">' +
                            '<div class="plupload_file_name"><span>' + val.file_attachment + '</span></div>' +
                            '<div class="plupload_file_action_modify">' +
                            '<a class="download-attachment" href="' + hrefDownload + '" rels=' + val.id + '>Download</a>' +
                            '<a class="delete-attachment" style="' + display_none + '" href="' + hrefDelete + '" rels=' + val.id + '>Delete</a></div>' +
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
        runtimes: 'html5, html4',
        url: "/projects/uploads/" + project_id + '/upload_documents_2',
        chunk_size: '10mb',
        rename: true,
        dragdrop: true,
        filters: {
            max_file_size: '10mb',
            mime_types: [{
                title: "Files",
                extensions: "jpg,jpeg,bmp,gif,png,swf,txt,zip,rar,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,csv,eml,msg,xlsm"
            }]
        },
        init: {
            PostInit: function(up) {
                up.project_id = project_id;
                up.linkedAction = '/projects/attachment/';
                if (projectFiles2 && Object.keys(projectFiles2).length > 0) {
                    up.auditFiles = projectFiles2;
                    var tmpHtml = '';
                    var display_none = '';
                    if (showAllFieldYourform == 0) {
                        display_none = 'display: none';
                    }
                    $.each(projectFiles2, function(ind, val) {
                        var hrefDownload = '/projects/attachment/upload_documents_2' + '/' + project_id + '/' + val.id + '/download/';
                        var hrefDelete = '/projects/attachment/upload_documents_2' + '/' + project_id + '/' + val.id + '/delete/';
                        tmpHtml +=
                            '<li id="' + val.id + '" class="plupload_done">' +
                            '<div class="plupload_file_name"><span>' + val.file_attachment + '</span></div>' +
                            '<div class="plupload_file_action_modify">' +
                            '<a class="download-attachment" href="' + hrefDownload + '" rels=' + val.id + '>Download</a>' +
                            '<a class="delete-attachment" style="' + display_none + '" href="' + hrefDelete + '" rels=' + val.id + '>Delete</a></div>' +
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
        runtimes: 'html5, html4',
        url: "/projects/uploads/" + project_id + '/upload_documents_3',
        chunk_size: '10mb',
        rename: true,
        dragdrop: true,
        filters: {
            max_file_size: '10mb',
            mime_types: [{
                title: "Files",
                extensions: "jpg,jpeg,bmp,gif,png,swf,txt,zip,rar,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,csv,eml,msg,xlsm"
            }]
        },
        init: {
            PostInit: function(up) {
                up.project_id = project_id;
                up.linkedAction = '/projects/attachment/';
                if (projectFiles3 && Object.keys(projectFiles3).length > 0) {
                    up.auditFiles = projectFiles3;
                    var tmpHtml = '';
                    var display_none = '';
                    if (showAllFieldYourform == 0) {
                        display_none = 'display: none';
                    }
                    $.each(projectFiles3, function(ind, val) {
                        var hrefDownload = '/projects/attachment/upload_documents_3' + '/' + project_id + '/' + val.id + '/download/';
                        var hrefDelete = '/projects/attachment/upload_documents_3' + '/' + project_id + '/' + val.id + '/delete/';
                        tmpHtml +=
                            '<li id="' + val.id + '" class="plupload_done">' +
                            '<div class="plupload_file_name"><span>' + val.file_attachment + '</span></div>' +
                            '<div class="plupload_file_action_modify">' +
                            '<a class="download-attachment" href="' + hrefDownload + '" rels=' + val.id + '>Download</a>' +
                            '<a class="delete-attachment" style="' + display_none + '" href="' + hrefDelete + '" rels=' + val.id + '>Delete</a></div>' +
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
        runtimes: 'html5, html4',
        url: "/projects/uploads/" + project_id + '/upload_documents_4',
        chunk_size: '10mb',
        rename: true,
        dragdrop: true,
        filters: {
            max_file_size: '10mb',
            mime_types: [{
                title: "Files",
                extensions: "jpg,jpeg,bmp,gif,png,swf,txt,zip,rar,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,csv,eml,msg,xlsm"
            }]
        },
        init: {
            PostInit: function(up) {
                up.project_id = project_id;
                up.linkedAction = '/projects/attachment/';
                if (projectFiles4 && Object.keys(projectFiles4).length > 0) {
                    up.auditFiles = projectFiles4;
                    var tmpHtml = '';
                    var display_none = '';
                    if (showAllFieldYourform == 0) {
                        display_none = 'display: none';
                    }
                    $.each(projectFiles4, function(ind, val) {
                        var hrefDownload = '/projects/attachment/upload_documents_4' + '/' + project_id + '/' + val.id + '/download/';
                        var hrefDelete = '/projects/attachment/upload_documents_4' + '/' + project_id + '/' + val.id + '/delete/';
                        tmpHtml +=
                            '<li id="' + val.id + '" class="plupload_done">' +
                            '<div class="plupload_file_name"><span>' + val.file_attachment + '</span></div>' +
                            '<div class="plupload_file_action_modify">' +
                            '<a class="download-attachment" href="' + hrefDownload + '" rels=' + val.id + '>Download</a>' +
                            '<a class="delete-attachment" style="' + display_none + '" href="' + hrefDelete + '" rels=' + val.id + '>Delete</a></div>' +
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
        runtimes: 'html5, html4',
        url: "/projects/uploads/" + project_id + '/upload_documents_5',
        chunk_size: '10mb',
        rename: true,
        dragdrop: true,
        filters: {
            max_file_size: '10mb',
            mime_types: [{
                title: "Files",
                extensions: "jpg,jpeg,bmp,gif,png,swf,txt,zip,rar,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,csv,eml,msg,xlsm"
            }]
        },
        init: {
            PostInit: function(up) {
                up.project_id = project_id;
                up.linkedAction = '/projects/attachment/';
                if (projectFiles5 && Object.keys(projectFiles5).length > 0) {
                    up.auditFiles = projectFiles5;
                    var tmpHtml = '';
                    var display_none = '';
                    if (showAllFieldYourform == 0) {
                        display_none = 'display: none';
                    }
                    $.each(projectFiles5, function(ind, val) {
                        var hrefDownload = '/projects/attachment/upload_documents_5' + '/' + project_id + '/' + val.id + '/download/';
                        var hrefDelete = '/projects/attachment/upload_documents_5' + '/' + project_id + '/' + val.id + '/delete/';
                        tmpHtml +=
                            '<li id="' + val.id + '" class="plupload_done">' +
                            '<div class="plupload_file_name"><span>' + val.file_attachment + '</span></div>' +
                            '<div class="plupload_file_action_modify">' +
                            '<a class="download-attachment" href="' + hrefDownload + '" rels=' + val.id + '>Download</a>' +
                            '<a class="delete-attachment" style="' + display_none + '" href="' + hrefDelete + '" rels=' + val.id + '>Delete</a></div>' +
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
    if (showAllFieldYourform == 0) {
        $('.plupload_start').css('display', 'none');
    }
    // $("a.fancy").fancybox();
    $("a.fancy").fancybox({
        type: 'image',
    });
    $('#carousel').flexslider({
        animation: "slide",
        controlNav: false,
        animationLoop: false,
        slideshow: false,
        itemWidth: 150,
        itemMargin: 5,
        minItems: 5,
        pauseOnHover: true,
    });
    $(document).ready(function() {
        $('.textarea-limit').on('keypress', function(event) {
            var textarea = $(this),
                numberOfLines = (textarea.val().match(/\n/g) || []).length + 1,
                maxRows = 2;
            if (event.which === 13 && numberOfLines === maxRows) {
                return false;
            }
        });
        $('.not-decimal').on('keypress', function(e) {
            var key = e.keyCode ? e.keyCode : e.which;
            if (key == 46) {
                return false;
            }
        });

        $('.wd-behavior').on('click', function() {
            $(this).next('.wd-content-field').slideToggle();
        });
    });
    // call big image.
    $('.flex-active-slide img').live('click', function() {
        var i = $(this).data('url');
        hideMe();
        var _html = '<img style="width: 100%; height: 100%" src="' + i + '">';
        jQuery('#dialogDetailValue').css({
            'padding-bottom': 0
        });
        jQuery('#dialogDetailValue').css({
            'top': '23%',
            'left': '25%'
        });
        $('.loading_w').show();
        setTimeout(function() {
            $('.loading_w').hide();
            jQuery('#contentDialog').html(_html);
            showMe();
        }, 500);
    });
    // check name ActivityLinked
    $(document).ready(function() {
        var edit = false;
        $("#ActivityLinkedName").focus(function() {
            edit = false;
            $("#ok_save_ac_linked").hide();
        });
        $("#ActivityLinkedName").keypress(function() {
            edit = true;
        });
        $("#ActivityLinkedName").one('paste', function() {
            edit = true;
        });
        $("#ActivityLinkedName").blur(function() {
            if (edit == false) {
                $("#ok_save_ac_linked").show();
            }
        });
        $("#ActivityLinkedName").on('change', function() {
            // goi len controll de kiem tra xem code 1 da ton tai
            if ($('#ActivityLinkedName').val() != '') {
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
                        if (data == 1) {
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
        $("#ActivityLinkedName").blur(function() {
            // goi len controll de kiem tra xem code 1 da ton tai
            if ($('#ActivityLinkedName').val() != '') {
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
                        if (data == 1) {
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

    function saveFieldSelectYourForm(element, field) {
        $.ajax({
            url: '<?php echo $html->url(array('action' => 'saveFieldYourForm', $this->data['Project']['id'])); ?>',
            type: 'POST',
            data: {
                field: field,
                value: $(element).val()
            },
            success: function(respon) {
                if (respon) {
                    $('#pseudo-category').parent().removeClass('error').addClass('saved');
                    setTimeout(function() {
                        $('#pseudo-category').parent().removeClass('saved');
                    }, 3000);
                } else {
                    $('#pseudo-category').parent().addClass('error');
                }
            },
            complete: function(respon) {
                $('.wd-main-content').removeClass('loading');
                curretnCate = $('#pseudo-category').val();
            },
        });
    }

    function saveFieldChangeCodeYourForm(value, field) {
        $.ajax({
            url: '<?php echo $html->url(array('action' => 'saveFieldYourForm', $this->data['Project']['id'])); ?>',
            type: 'POST',
            data: {
                field: field,
                value: value
            },
        });
    }

    function saveFieldYourForm(element) {
        var _this = $(element);
        var field = _this.attr('name');
        var value = _this.val();
        field = field.replace('data[Project][', '').replace(']', '');
        var project_name_exist = false;
        if (field == 'project_name') {
            $.ajax({
                url: '<?php echo $html->url(array('controller' => 'projects', 'action' => 'checkProjectName', $this->data['Project']['id'])); ?>',
                type: 'POST',
                dataType: 'json',
                async: false,
                data: {
                    project_name: value.trim(),
                },
                success: function(res) {
                    if (res == 1) {
                        project_name_exist = true;
                        $('.check-project').show();
                    } else {
                        $('.check-project').hide();
                    }
                },
            });
        }
        if (!project_name_exist) {
            $.ajax({
                url: '<?php echo $html->url(array('action' => 'saveFieldYourForm', $this->data['Project']['id'])); ?>',
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    _this.addClass('loading');
                },
                data: {
                    field: field,
                    value: value,
                    label: _this.siblings('label').text(),
                },
                success: function(respon) {
                    if (respon.result == 'success') {
                        // _this.val(respon.data.Project[field]);
                        _this.removeClass('error').addClass('saved');
                        setTimeout(function() {
                            _this.removeClass('saved');
                        }, 4000);
                    } else {
                        _this.addClass('error');
                    }
                },
                complete: function() {
                    _this.removeClass('loading');
                },
            });
        }
    }

    function saveFieldUploadYourForm($this) {
        var field = $($this).attr('name');
        field = field.replace('data[Project][', '').replace(']', '');
        var value = $($this).val();

        var file = $(this)[0].files[0];
        var upload = new Upload(file);
        $.ajax({
            url: '<?php echo $html->url(array('action' => 'saveFieldUploadYourForm', $this->data['Project']['id'])); ?>',
            type: 'POST',
            data: {
                field: upload
            },
        });
    }

    function expandScreen() {
        $('.wd-map-area').addClass('fullScreen');
        $('.wd-content-right').addClass('view_full');
    }

    function collapseScreen() {
        $('.wd-map-area').removeClass('fullScreen');
        $('.wd-content-right').removeClass('view_full');
        // $(window).resize();
        // $('#map-frame').resize();
    }

    //location view
    var state = 1;
    var coord = /^\s*(\-?[0-9]+\.[0-9]+)\s*,\s*(\-?[0-9]+\.[0-9]+)\s*$/;
    var gapi = <?php echo json_encode($gapi) ?>;

    function refreshMap(show) {
        // var map_width = $('.wd-map-area').width();
        // $('.wd-map-area').find('iframe').css("width", map_width);

        var query = $.trim($('#coord-input').val());
        if (query) {
            //initial google maps
            $('#map-frame').prop('src', 'https://www.google.com/maps/embed/v1/place?q=' + encodeURIComponent(query) + '&key=' + gapi);
            if (show) {
                $('#map-frame').show();
                $('#local-frame').hide();
                state = 0;
            }
        } else {
            $('#map-frame').prop('src', 'about:blank');
        }
    }

    function getCode(callback) {
        var address = $.trim($('#coord-input').val());
        if (!address) return callback('');
        if (matches = address.match(coord)) {
            return callback({
                lat: matches[1],
                lng: matches[2]
            });
        }
        var url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' + encodeURIComponent(address) + '&key=' + gapi;
        $.ajax({
            url: url,
            type: 'GET',
            success: function(result) {
                if (result.status == 'OK') {
                    callback(result.results[0].geometry.location);
                } else {
                    callback(null);
                }
            }
        });
    }
    var saving = false;

    function updateLongLat() {
        if (saving) return false;
        var me = $('#coord-input');
        $('#loader').show();
        //get location
        //result may be null, empty string or an object
        getCode(function(result) {
            if (result == null) {
                me.css('color', 'red');
                $('#loader').hide();
                alert(<?php echo json_encode(__('Cannot find the location of this address', true)) ?>);
            } else {
                $.ajax({
                    url: '<?php echo $this->Html->url('/project_local_views/saveAddress/' . $projectName['Project']['id']) ?>',
                    data: {
                        data: {
                            address: me.val(),
                            latlng: result
                        }
                    },
                    type: 'POST',
                    complete: function() {
                        saving = false;
                        refreshMap(state == 0);
                        me.css('color', 'green');
                        refreshMap(true);
                        $('#loader').hide();
                    }
                });
            }
        });
    }
    $(document).ready(function() {
        $('#coord-input').val(<?php echo json_encode($projectName['Project']['address']) ?>);
        updateLongLat();
        <?php if ($projectName['Project']['address']) : ?>
            refreshMap(true);
        <?php endif ?>
    });

    function refreshMapYourForm($this) {
        var _this = $($this);
        // var newAddress = $.trim($this.val());
        var field = $($this).attr('name');
        field = field.replace('data[Project][', '').replace(']', '');
        var value = $($this).val();
        // refreshMap(true);
        updateLongLat();
        $.ajax({
            url: '<?php echo $html->url(array('action' => 'saveFieldYourForm', $this->data['Project']['id'])); ?>',
            type: 'POST',
            beforeSend: function() {
                _this.addClass('loading');
            },
            data: {
                field: field,
                value: value
            },
            success: function(respon) {
                if (respon.result == 'success') {
                    _this.removeClass('error').addClass('saved');
                    setTimeout(function() {
                        _this.removeClass('saved');
                    }, 2000);
                } else {
                    _this.addClass('error');
                }
            },
            complete: function() {
                _this.removeClass('loading');
            },
        });
    }

    function saveFieldMutiSelectYourForm(elm) {
        var _this = $(elm);
        var listPm = [];
        var field = _this.find(':checkbox:first').attr('name');
        _this.find('input:checked').each(function(index, _ip) {
            listPm.push($(_ip).val());
        });
        field = field.replace('data[', '').replace('][]', '');
        $.ajax({
            url: '<?php echo $html->url(array('action' => 'saveFieldYourFormPM', $this->data['Project']['id'])); ?>',
            type: 'POST',
            data: {
                field: field,
                value: listPm
            },
            beforeSend: function() {
                _this.addClass('loading');
            },
            success: function(respon) {
                if (respon == 'Done') {
                    _this.removeClass('error').addClass('saved');
                    setTimeout(function() {
                        _this.removeClass('saved');
                    }, 2000);
                } else {
                    _this.addClass('error');
                }
            },
            complete: function() {
                _this.removeClass('loading');
            },
        });
    }

    $('.trigger-upload').click(function() {
        // if(showAllFieldYourform == 0) return;
        $(this).closest('.wd-section').find('#openPopup').toggle();
    });
    $('.replace-attachment').click(function(e) {
        e.stopPropagation();
        $('#loader').show();
        $('#loader + img').hide();
        $.ajax({
            type: 'POST',
            url: '<?php echo $html->url('/project_global_views/delete/' . @$projectGlobalView['ProjectGlobalView']['id']) ?>',
            success: function() {
                $('#loader').hide();
                $('#download-place').remove();
                $('#upload-place').show();
                $('iframe').prop('src', 'about:blank');
                $('#wd-fragment-2').html('');
                location.reload();
            }
        });
    });

    $('.close-popup').click(function() {
        $(this).closest('#openPopup').toggle();
    });
    // $("#attachmentUrl").parent().hide();
    $('#ProjectGlobalViewIsFile1, #ProjectGlobalViewIsFile0').click(function() {
        if ($('#ProjectGlobalViewIsFile1').is(':checked')) {
            $("#ProjectGlobalViewAttachment").parent().show();
            $("#attachmentUrl").parent().hide();
            $('#file-types').show();
        } else {
            $("#ProjectGlobalViewAttachment").parent().hide();
            $("#attachmentUrl").parent().show();
            $('#file-types').hide();
        }
    });
    //Update by QuanNV. 24/06/2019
    $('.wd-tab >nav .tab-header').on('click', function() {
        var _this = $(this);
        var _index = _this.data('tab');
        _this.addClass('wd-current').siblings().removeClass('wd-current');
        var _tabs_content = _this.closest('.wd-tab').find('.tabs-content-container-inner:first').children('.tab-content');
        _tabs_content.fadeOut(300);
        _tabs_content.each(function() {
            var _this = $(this);
            if (_this.data('tab') == _index) {
                _this.addClass('wd-current').siblings().removeClass('wd-current');
                _this.fadeIn(300);

            }
        });

    });
    openImage = function(element) {
        var t = $(element);
        var url = t.attr('src');
        window.open(url, '_blank');
    }
    var switch_activated = $('#switch-activated');
    if (switch_activated.length > 0) {
        $('#switch-activated').on('click', function(e) {
            e.preventDefault();
            var _this = $(this);
            var _input = _this.find('input');
            var _activaty_id = _input.data('activity-id');
            var _id = _input.data('id');
            var _activated_val = _input.data('value');
            _this.find('.wd-update').addClass('sw-loading');
            $.ajax({
                url: '/projects_preview/update_activated',
                type: 'POST',
                data: {
                    data: {
                        id: _id,
                        activaty_id: _activaty_id,
                        activated: _activated_val ? 0 : 1,
                    }
                },
                dataType: 'json',
                success: function(datas) {
                    if (datas['result'] == 'success') {
                        if (_activated_val != 0) {
                            _this.find('.wd-update').removeClass('wd-update-default');
                        } else {
                            _this.find('.wd-update').addClass('wd-update-default');
                        }
                        var val_updated = _activated_val ? 0 : 1;
                        _input.data("value", val_updated);
                        _this.find('.wd-update').removeClass('sw-loading');
                    }
                }
            });
        });
    }
    $('.download-place-toggle-button').on('click', function() {
        var _this = $(this);
        if (_this.hasClass('active')) {
            _this.siblings('.download-place-inner').removeClass('open').fadeOut(500);
            _this.removeClass('active');
        } else {
            _this.siblings('.download-place-inner').addClass('open').show();
            _this.addClass('active');
        }
    });
    $('.fancy.image').fancybox({
        type: 'image'
    });

    Dropzone.autoDiscover = false;
    $(function() {
        var dropzone_container = $('.trigger-upload-popup');
        var radio_input = $('#ProjectGlobalViewIndexForm input[type="radio"]');
        var input_change = $('input#ProjectGlobalViewIsFile1');
        input_change.closest('form').find('.wd-button').hide();
        radio_input.on('click', function() {
            if (input_change.is(':checked')) {
                dropzone_container.fadeIn(300);
                input_change.closest('form').find('[type="submit"]').hide();

            } else {
                dropzone_container.hide();
                input_change.closest('form').find('[type="submit"]').show();
                input_change.closest('form').find('.wd-button').show();
            }
        });
        if ($('#upload-widget').length > 0) {
            var myDropzone = new Dropzone("#upload-widget", {
                maxFiles: 1
            });
            myDropzone.on("success", function(file) {
                myDropzone.removeFile(file);
                location.reload();
            });
        }
    });

    function popup_dropzone() {
        var popup_project_Dropzones = $('.trigger-upload').find('.dropzone');
        if (popup_project_Dropzones.length) $.each(popup_project_Dropzones, function(ind, _tag) {
            $(function() {
                var _Dropzone = new Dropzone(_tag, {
                    acceptedFiles: ".jpg,.jpeg,.bmp,.gif,.png,.txt,.doc,.xls,.pdf,.docx,.xlsx,.ppt,.pps,.pptx,.csv,.xlsm,.msg",
                    imageSrc: "/img/new-icon/draganddrop.png",
                    dictDefaultMessage: "<?php __('Drag & Drop your document or browse your folders'); ?>",
                    maxFiles: 1,
                    clickable: false,
                });
                _Dropzone.on("success", function(file) {
                    // _Dropzone.removeFile(file);
                    location.reload();
                });
            });
        });
    }
    popup_dropzone();

    $('#yourform-setting-toggle').on('click', function() {
        var _this = $(this);
        _this = _this.closest('.your-form-action');
        var popup = $('#dialog-yourform-settings');
        var _form = popup.find('form');
        var popup_title = popup.find('.dialog-title').text();
        // var max_height = $(window).height() - _this.offset().top - _this.height();
        // if( max_height < 500) max_height = false;
        // console.log( max_height);
        var _content = $('#layout-setting');
        _content.height('');
        popup.dialog({
            title: popup_title,
            position: {
                my: "right top",
                at: "right bottom",
                of: _this
            },
            autoOpen: true,
            closeOnEscape: true,
            modal: true,
            draggable: false,
            maxWidth: _this.closest('fieldset').width(),
            minWidth: 540,
            // maxHeight: max_height,
            open: function(e, ui) {
                var _blocks = popup.find('.popup-layout-setting').find('.block');
                var _len = _blocks.length;
                for (_i = _len; _i > 0; _i--) {
                    $(_blocks[_i - 1]).css('z-index', _len + 1100 - _i);
                }
                popup.find('.btn-cancel').off('click').on('click', function() {
                    popup.dialog('close');
                });
                var overlay = $('body').children('.ui-widget-overlay').css('opacity', 0.05);
                overlay.on('click', function(e) {
                    popup.dialog('close');
                });
                if (popup.outerHeight(true) > $(window).height()) {
                    var _dialog_height = popup.outerHeight(true) + popup.siblings('.ui-dialog-titlebar').outerHeight(true);
                    var _content_height = _content.height();
                    var _other_height = _dialog_height - _content_height;
                    var max_height = $(window).height() - _this.offset().top - _this.height();
                    if (max_height > 500) {
                        _content_height = max_height - _other_height;
                        _content.height(_content_height);
                        popup.dialog("option", "position", {
                            my: "right top",
                            at: "right bottom",
                            of: _this
                        });
                    }
                }

                function _updateLiClass(el) {
                    var li = $(el).closest('li');
                    var _checked = $(el).is(':checked');
                    $(el).closest('li').toggleClass('displayed', _checked);
                    li.toggleClass('not-displayed', !_checked);
                }
                if (popup.find('.checkbox_block_display').length) {
                    $.each(popup.find('.checkbox_block_display'), function(i, el) {
                        _updateLiClass(el);
                    });
                    popup.find('.checkbox_block_display').off('change').on('change', function(e) {
                        _updateLiClass(this);
                    });
                }
                popup.find('form').off('reset').on('reset', function() {
                    setTimeout(function() {
                        if (popup.find('.multiSelectOptions').length) {
                            $.each(popup.find('.multiSelectOptions').find(':checkbox'), function(i, el) {
                                $(el).trigger('change');
                            });
                        }
                    }, 100);

                });
                if (!_form.find('.multiSelectOptions').length) {
                    setTimeout(function() {
                        _form.find('select[multiple="multiple"]').each(function(i, _t) {
                            $(_t).multiSelect({
                                selectAll: true,
                                noneSelected: '<?php echo __('Select', true); ?>',
                                oneOrMoreSelected: '<?php echo __('Select', true); ?>',
                                appendTo: _form,
                                position: 'auto',
                                name: $(_t).attr('name').replace(/\[\]$/, ''),
                            });
                        });
                    }, 50);

                }
            },
            beforeClose: function(e, ui) {},
            close: function(e, ui) {},

        });
    });
</script>
<!-- Popup project tasks has employee assigned has left -->
<?php if (!empty($list_task_need_update)) { ?>
    <div id="dialog_employee_has_left" class="buttons dialog_skip_value">
        <div class="dialog-content loading-mark">
            <div class="wd-row">
                <h1 class="h1-value popup-note"><?php echo __('Please note that the resource is no longer active, please modify the following tasks', true); ?></h1>
            </div>
            <div class="wd-row">
                <div class="wd-col wd-col-lg-4">
                    <h1 class="h1-value"><?php echo __('Resources', true); ?></h1>
                </div>
                <div class="wd-col wd-col-lg-8">
                    <h1 class="h1-value"><?php echo __('Tasks', true); ?></h1>
                </div>
            </div>
            <div class="wd-row">
                <?php foreach ($list_task_need_update as $employee_id => $tasks) {
                    $employee_name = '';
                    if (!empty($listEmployeeNotActive[$employee_id])) {
                        $employee_name = $listEmployeeNotActive[$employee_id]['first_name'] . ' ' . $listEmployeeNotActive[$employee_id]['last_name'];
                    } ?>
                    <div class="wd-col wd-col-lg-4">
                        <a class="employee-assigned" href="/employees/edit/<?php echo $employee_id; ?>" target="_blank" title="<?php echo $employee_name; ?>"><img width="35" height="35" class="circle" src="<?php echo $this->UserFile->avatar($employee_id); ?>" alt="<?php echo $employee_name; ?>"> <span><?php echo $employee_name; ?></span></a>
                    </div>
                    <div class="wd-col wd-col-lg-8">
                        <ul class="list-task">
                            <?php foreach ($tasks as $task_id => $task_title) { ?>
                                <li> <a href="/project_tasks/index/<?php echo $project_id; ?>/?id=<?php echo $task_id ?>" target="_blank"><?php echo $task_title; ?> </a></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>
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
                    'label' => false
                ));
                ?>
                <span style="display: none; float:left; color: #000; width: 62%" id="onChangeNameActivity"></span>
            </div>
            <div class="ch-input-custom">
                <label for="name-detail"><?php __("Long name") ?></label>
                <?php
                echo $this->Form->input('name_detail', array(
                    'div' => false,
                    'label' => false
                ));
                ?>
            </div>
            <div class="ch-input-custom">
                <label for="short-name"><?php __("Short Name") ?></label>
                <?php
                echo $this->Form->input('short_name', array(
                    'div' => false,
                    'label' => false
                ));
                ?>
            </div>
            <div class="ch-input-custom">
                <label for="family"><?php __("Family") ?></label>
                <?php
                $disabled = '';
                $style = 'width:62% !important;';
                if ($activate_family_linked_program) {
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
                    "options" => $families
                ));
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
                    "options" => $subFamilies
                ));
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