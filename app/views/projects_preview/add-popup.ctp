<?php 
App::import("vendor", "str_utility");
$str_utility = new str_utility();
// echo $html->script(array(
//     'jquery.multiSelect',
//     'jshashtable-2.1',
//     'tinymce/tinymce.min',
// ));
// echo $html->css(array(
//     'jquery.multiSelect',
//     'multipleUpload/jquery.plupload.queue',
// ));
function multiSelect($_this, $fieldName, $fielData, $textHolder, $pc = array()){
    $checkAvatar = $_this->viewVars['checkAvatar'];
    $cotentField = '';
    $cotentField = '<div class="wd-multiselect multiselect multiselect-pm">
    <a href="" class="wd-combobox wd-project-manager"><p style="position: absolute; color: #c6cccf">'. $textHolder .'</p></a>
    <div class="wd-combobox-content '. $fieldName .'" style="display: none;">
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
                <a class="circle-name"  title="' . $fielData[$idPm] . '"><span data-id = "'. $idPm . '">'. $avatar .'</span></a>' .
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

    $cuPhaseTemplate = '<div class="multiselect">
        <a href="javascript:void();" class="wd-combobox-7"><p style="position: absolute; color: #c6cccf">'. __d(sprintf($_domain, 'Details'), 'Current Phase', true) .'</p></a>
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

    $fieldProjecManager = multiSelect($this, 'project_employee_manager', $employees['pm'], __d(sprintf($_domain, 'Details'), 'Project Manager', true));
    $fieldReadAccess   = multiSelect($this, 'read_access', $employees['pm'] , __d(sprintf($_domain, 'Details'), 'Read Access', true), $profitCenters);
    $fieldTechnicalManager   = multiSelect($this, 'technical_manager_list', $employees['pm'] , __d(sprintf($_domain, 'Details'), 'Technical manager', true));
    $fieldUATManager   = multiSelect($this, 'uat_manager_list', $employees['pm'] , __d(sprintf($_domain, 'Details'), 'UAT manager', true));
    $fieldFunctionLeader   = multiSelect($this, 'functional_leader_list', $employees['pm'] , __d(sprintf($_domain, 'Details'), 'Functional leader', true));
    $fieldChiefBusiness   = multiSelect($this, 'chief_business_id', $employees['pm'] , __d(sprintf($_domain, 'Details'), 'Chief Business', true));
    // exit;
    // projectPhases
    $maps = array(
        'project_amr_program_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Program', true),
            'html' => $this->Form->input('project_amr_program_id', array('div' => false, 'label' => false, "name" => 'data[Project][project_amr_program_id]',
                "empty" => __d(sprintf($_domain, 'Details'), 'Program', true),
                "options" => $project_arm_programs)),
        ),
        'project_name' => array(
            'label' => __d(sprintf($_domain, 'Details'), "Project Name", true),
            'html' => $this->Form->input('project_name', array('div' => false, 'label' => false, 'style' => '', 'class' => 'project-name not_save_history',"placeholder" => __d(sprintf($_domain, 'Details'), "Project Name", true), "name" => 'data[Project][project_name]'))
        ),
        'project_status_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Status', true),
            'html' => $this->Form->input('project_status_id', array('div' => false, 'label' => false, "name" => 'data[Project][project_status_id]',"options" => $project_statuses, 'empty' => __d(sprintf($_domain, 'Details'), 'Status', true))),
        ),
        'project_manager_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Project Manager', true),
            'html' => $fieldProjecManager,
            'class' => 'multiselect'
        ),
        'read_access' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Read Access', true),
            'html' => $fieldReadAccess,
            'class' => 'multiselect'
        ),

    );
    $maps2 = array(
        'company_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Company', true),
            'html' => $this->Form->input('company_id', array('name' => 'data[Project][company_id]',
                    'type' => 'select','div' => false,'label' => false,'default' => $employee_info['Company']['id'],
                    "empty" => __d(sprintf($_domain, 'Details'), 'Company', true), 'disabled' => 'disabled',
                    "options" => $company_names)),
        ),
        'long_project_name' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Project long name', true),
            'html' => $this->Form->input('long_project_name', array('div' => false, 'label' => false,"name" => 'data[Project][long_project_name]',"placeholder" => __d(sprintf($_domain, 'Details'), 'Project long name', true)))
        ),
        'project_code_2' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Project Code 2', true),
            'html' => $this->Form->input('project_code_2', array('div' => false, 'label' => false,"name" => 'data[Project][project_code_2]',"placeholder" => __d(sprintf($_domain, 'Details'), 'Project Code 2', true)))
        ),
        
        'project_sub_type_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Sub type', true),
            'html' => $this->Form->input('project_sub_type_id', array('div' => false, 'label' => false,"name" => 'data[Project][project_sub_type_id]',
                'empty' => __d(sprintf($_domain, 'Details'), 'Sub type', true),
                "options" => $project_types,
            ))
        ),
        'project_amr_sub_program_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Sub program', true),
            'html' => $this->Form->input('project_amr_sub_program_id', array('div' => false, 'label' => false,"name" => 'data[Project][project_amr_sub_program_id]',
                'style' => 'display: none',
                'empty' => __d(sprintf($_domain, 'Details'), 'Sub program', true),
                "options" => array(),
            ))
        ),
        'project_priority_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Priority', true),
            'html' => $this->Form->input('project_priority_id', array('div' => false, 'label' => false,"name" => 'data[Project][project_priority_id]', "options" => $project_priorities, 'empty' => __d(sprintf($_domain, 'Details'), 'Priority', true)))
        ),
        'complexity_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Implementation Complexity', true),
            'html' => $this->Form->input('complexity_id', array('div' => false, 'label' => false, "name" => 'data[Project][complexity_id]',"options" => $Complexities, 'empty' => __d(sprintf($_domain, 'Details'), 'Implementation Complexity', true)))
        ), 
        'created_value' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Created value', true),
            'html' => $this->Form->input('created_value', array('div' => false, 'label' => false, "name" => 'data[Project][created_value]', 'placeholder' => __d(sprintf($_domain, 'Details'), 'Created value', true)))
        ),
        'project_phase_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Current Phase', true),
            'html' => $cuPhaseTemplate,
            'class' => 'multiselect'
        ),
        'issues' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Issues', true),
            'html' => $this->Form->input('issues', array('type' => 'textarea', 'div' => false, 'label' => false, "name" => 'data[Project][issues]',"placeholder" => __d(sprintf($_domain, 'Details'), 'Issues', true))),
            'class' => 'wd-input-text'
        ),
        'primary_objectives' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Primary Objectives', true),
            'html' => $this->Form->input('primary_objectives', array('class' => 'not_save_history', 'type' => 'textarea', 'div' => false, "name" => 'data[Project][primary_objectives]','label' => false,"placeholder" => __d(sprintf($_domain, 'Details'), 'Primary Objectives', true))),
            'class' => 'wd-input-text'
        ),
        'project_objectives' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Project Objectives', true),
            'html' => $this->Form->input('project_objectives', array('type' => 'textarea', 'div' => false, "name" => 'data[Project][project_objectives]','label' => false,"placeholder" => __d(sprintf($_domain, 'Details'), 'Project Objectives', true))),
            'class' => 'wd-input-text'
        ),
        'constraint' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Constraint', true),
            'html' => $this->Form->input('constraint', array('type' => 'textarea', 'div' => false, 'label' => false,"name" => 'data[Project][constraint]',"placeholder" => __d(sprintf($_domain, 'Details'), 'Constraint', true))),
            'class' => 'wd-input-text'
        ),
        'remark' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Remark', true),
            'html' => $this->Form->input('remark', array('type' => 'textarea', 'div' => false, 'label' => false,"name" => 'data[Project][remark]',"placeholder" => __d(sprintf($_domain, 'Details'), 'Remark', true))),
            'class' => 'wd-input-text'
        ),
        'free_1' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Free 1', true),
            'html' => $this->Form->input('free_1', array('type' => 'textarea', 'class' => 'resizeOnFocus', 'div' => false, "name" => 'data[Project][free_1]', 'label' => false,"placeholder" => __d(sprintf($_domain, 'Details'), 'Free 1', true))),
            'class' => 'wd-input-text'
        ),
        'free_2' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Free 2', true),
            'html' => $this->Form->input('free_2', array('type' => 'textarea', 'class' => 'resizeOnFocus', 'div' => false, "name" => 'data[Project][free_2]','label' => false,"placeholder" => __d(sprintf($_domain, 'Details'), 'Free 2', true))),
            'class' => 'wd-input-text'
        ),
        'free_3' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Free 3', true),
            'html' => $this->Form->input('free_3', array('type' => 'textarea', 'class' => 'resizeOnFocus', "name" => 'data[Project][free_3]','div' => false, 'label' => false,"placeholder" => __d(sprintf($_domain, 'Details'), 'Free 3', true))),
            'class' => 'wd-input-text'
        ),
        'free_4' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Free 4', true),
            'html' => $this->Form->input('free_4', array('type' => 'textarea',  'class' => 'resizeOnFocus', 'div' => false, "name" => 'data[Project][free_4]','label' => false,"placeholder" => __d(sprintf($_domain, 'Details'), 'Free 4', true))),
            'class' => 'wd-input-text'
        ),
        'free_5' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Free 5', true),
            'html' => $this->Form->input('free_5', array('type' => 'textarea', 'class' => 'resizeOnFocus', 'div' => false,"name" => 'data[Project][free_5]', 'label' => false,"placeholder" => __d(sprintf($_domain, 'Details'), 'Free 5', true))),
            'class' => 'wd-input-text'
        ),
        'chief_business_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Chief Business', true),
            'html' => $fieldChiefBusiness,
            'class' => 'multiselect'
        ),
        'technical_manager_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Technical manager', true),
            'html' => $fieldTechnicalManager,
            'class' => 'multiselect'
        ),
        'uat_manager_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'UAT manager', true),
            'html' => $fieldUATManager,
            'class' => 'multiselect'
        ),
        'functional_leader_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Functional leader', true),
            'html' => $fieldFunctionLeader,
            'class' => 'multiselect'
        ),
        'start_date' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Start Date', true),
            'html' => $this->Form->input('start_date', array('class' => 'not_save_history', 'div' => false,'label' => false, "name" => 'data[Project][start_date]',"placeholder" => __d(sprintf($_domain, 'Details'), 'Start Date', true),'type' => 'text'))
        ),
        'end_date' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'End Date', true),
            'html' => $this->Form->input( 'end_date', array('class' => 'not_save_history', 'div' => false,'label' => false, "name" => 'data[Project][end_date]',"placeholder" => __d(sprintf($_domain, 'Details'), 'End Date', true),'type' => 'text'))
        ),
        'budget_customer_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Customer', true),
            'html' => $this->Form->input('budget_customer_id', array('name' => 'data[Project][budget_customer_id]',
                'type' => 'select',
                'div' => false,
                'label' => false,
                "empty" => __d(sprintf($_domain, 'Details'), 'Customer', true),
                "options" => (array) @$budgetCustomers,
            ))
        )
    );
    $range = range(1, 20);
    foreach($range as $num){
        if( $num <= 4 ){
            //bool 0/1
            // $name = 'data["Project"]["bool_"' . $numn .'"]"';
            $maps2['bool_' . $num] = array(
                'label' => __d(sprintf($_domain, 'Details'), '0/1 ' . $num, true),
                'html' => $this->Form->input('bool_' . $num, array('type' => 'select', 'div' => false, 'label' => false, 'options' => array(0, 1), 'class'=>'bool_' . $num, 'name' => 'data[Project][bool_' . $num .']')),
                'class' => 'multiselect'
            );
        }
        if( $num <= 5 ){
            //text editor
            $maps2['editor_' . $num] = array(
                'label' => __d(sprintf($_domain, 'Details'), 'Editor ' . $num, true),
                'html' => $this->Form->input('editor_' . $num, array('type' => 'textarea', 'placeholder' => __d(sprintf($_domain, 'Details'), 'Editor ' . $num, true),'class' => 'tinymce-editor', 'div' => array('class' => 'tinymce-container'), 'label' => false, 'name' => 'data[Project][editor_' . $num .']')),
                'class' => 'tiny-mce-field'
            );
            //date MM/YY
            $maps2['date_mm_yy_' . $num] = array(
                'label' => __d(sprintf($_domain, 'Details'), 'Date(MM/YY) ' . $num, true),
                'html' => $this->Form->input('date_mm_yy_' . $num, array('type' => 'text', 'class' => 'wd-date-mm-yy', 'placeholder' => __d(sprintf($_domain, 'Details'), 'Date(MM/YY) ' . $num, true),'div' => false, 'label' => false, 'value' => $this->data['Project']['date_mm_yy_' . $num], 'name' => 'data[Project][date_mm_yy_' . $num .']'))
            );
            //date YY
            $maps2['date_yy_' . $num] = array(
                'label' => __d(sprintf($_domain, 'Details'), 'Date(YY) ' . $num, true),
                'html' => $this->Form->input('date_yy_' . $num, array('type' => 'text', 'placeholder' => __d(sprintf($_domain, 'Details'), 'Date(YY) ' . $num, true) ,'class' => 'wd-date-yy', 'div' => false, 'label' => false, 'value' => $this->data['Project']['date_yy_' . $num], 'name' => 'data[Project][date_yy_' . $num .']'))
            );
            // upload documents
            // $maps2['upload_documents_' . $num] = array(
            //     'label' => __d(sprintf($_domain, 'Details'), 'Upload documents ' . $num, true),
            //     'html' => '<div id="uploaderDocument'.$num.'" class="wd-input wd-calendar" style=""><p>Your browser do not have Flash, Silverlight or HTML5 support.</p></div>'
            // );
        }
        if($num <= 9){
            //yes/no
            $maps2['yn_' . $num] = array(
                'label' => __d(sprintf($_domain, 'Details'), 'Yes/No ' . $num, true),
                'html' => $this->Form->input('yn_' . $num, array('type' => 'select', 'div' => false, 'label' => false, 'name' => 'data[Project][yn_' . $num .']', 'options' => array(__('No', true), __('Yes', true)))),
                'class' => 'multiselect'
            );
        }
        // list mutiple select.
      
        if($num <= 10){
            $num_class = 7 + $num;
            if(!empty($datasets['list_muti_' . $num])){
                $htmlListMultiple = '<div class="multiselect">
                    <a href="javascript:void();" class="wd-combobox-'.$num_class.'"><p style="position: absolute; color: #c6cccf">'. __d(sprintf($_domain, 'Details'), 'List(multiselect) ' . $num, true) .'</p></a>
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
                $maps2['list_muti_' . $num] = array(
                    'class' => 'multiselect',
                    'label' => __d(sprintf($_domain, 'Details'), 'List(multiselect) ' . $num, true),
                    'html' => $htmlListMultiple
                );
            }
        }
     
        if( $num <= 14 ){
            //list
            if(!empty( $datasets['list_' . $num])){
                $maps2['list_' . $num] = array(
                    'label' => __d(sprintf($_domain, 'Details'), 'List ' . $num, true),
                    'html' => $this->Form->input('list_' . $num, array('type' => 'select', 'div' => false, 'label' => false,'placeholder' => __d(sprintf($_domain, 'Details'), 'List ' . $num, true), 'options' => $datasets['list_' . $num], 'empty' => __d(sprintf($_domain, 'Details'), 'List ' . $num, true), 'name' => 'data[Project][list_' . $num .']'))
                );
            }
            //date
            $maps2['date_' . $num] = array(
                'label' => __d(sprintf($_domain, 'Details'), 'Date ' . $num, true),
                'html' => $this->Form->input('date_' . $num, array('type' => 'text', 'placeholder' => __d(sprintf($_domain, 'Details'), 'Date ' . $num, true),'class' => 'wd-date', 'div' => false, 'name' => 'data[Project][date_' . $num .']', 'label' => false, 'value' => $str_utility->convertToVNDate($this->data['Project']['date_' . $num])))
            );
        }
        if( $num <= 16 ){
            //price
            $_class = 'numeric-value';
            if( $num > 6 ) {
                $_class .= ' not-decimal';
            }
            $maps2['price_' . $num] = array(
                'label' => __d(sprintf($_domain, 'Details'), 'Price ' . $num, true),
                'html' => $this->Form->input('price_' . $num, array('div' => false, 'class' => $_class,'placeholder' => __d(sprintf($_domain, 'Details'), 'Price ' . $num, true),'label' => false, 'name' => 'data[Project][price_' . $num .']', 'value' => number_format($this->data['Project']['price_' . $num], 2, '.', ' ') ))
            );
        }
        if( $num <= 18 ){
            //number
            $_class = 'numeric-value';
            if( $num > 6 ) {
                $_class .= ' not-decimal';
            }
            $maps2['number_' . $num] = array(
                'label' => __d(sprintf($_domain, 'Details'), 'Number ' . $num, true),
                'html' => $this->Form->input('number_' . $num, array('div' => false, 'class' => $_class, 'label' => false, 'placeholder' => __d(sprintf($_domain, 'Details'), 'Number ' . $num, true),'name' => 'data[Project][number_' . $num .']'))
            );
        }
        //text one line
        $maps2['text_one_line_' . $num] = array(
            'label' => __d(sprintf($_domain, 'Details'), 'Text one line ' . $num, true),
            'html' => $this->Form->input('text_one_line_' . $num, array('div' => false, 'label' => false, 'type' => 'text', 'placeholder' => __d(sprintf($_domain, 'Details'), 'Text one line ' . $num, true), 'name' => 'data[Project][text_one_line_' . $num .']'))
        );
        //text two line
        $maps2['text_two_line_' . $num] = array(
            'label' => __d(sprintf($_domain, 'Details'), 'Text two line ' . $num, true),
            'html' => $this->Form->input('text_two_line_' . $num, array('class' => 'textarea-limit','placeholder' => __d(sprintf($_domain, 'Details'), 'Text two line ' . $num, true), 'div' => false, 'name' => 'data[Project][text_two_line_' . $num .']', 'label' => false, 'rows' => '2', 'style' => 'height:35px;'))
        );
    }

?>

<div class ='wd-add-project'>
    <div class="wd-popup-inner">
        <h3>Description du projet</h3>
        <p class="alert" style="color: red; display: none">S'il vous plaît ajouter des informations pour les champs nécessaires dans ce formulaire</p>
        <div class=" project-field">
            <?php echo $this->Form->create('Project', array('type' => 'POST', 'id' => 'add-form', 'url' => array('controller' => 'projects_preview', 'action' => 'add_popup'))); 
            ?>
            <?php
                echo $this->Form->create('Project', array('enctype' => 'multipart/form-data'));
                App::import("vendor", "str_utility");
                $str_utility = new str_utility();
            ?>
            <!-- <fieldset> -->
                <div class="project-field">
                    <?php
                        if(empty($translation_data_default)){
                            // TH nay khi tao cong ty moi
                            // chua co setting 
                            // Co thoi gian se update lai 
                           $translation_data_default = array('project_name', 'project_manager_id', 'project_amr_program_id', 'project_status_id', 'read_access');
                           foreach($translation_data_default as $data){
                                $class = isset($maps[$data]['class']) ? $maps[$data]['class'] : '';
                            ?>
                               <div class="wd-input wd-area wd-none <?php echo $class ?>">
                                    <?php echo !empty($maps[$data]['html']) ? $maps[$data]['html'] : ''; ?>
                                </div>
                            <?php }
                        }else{
                            foreach($translation_data_default as $data){
                                $fieldName = $data['Translation']['field'];
                                if( $data['Translation']['field'] == 'project_details')continue;
                                $class = isset($maps[$fieldName]['class']) ? $maps[$fieldName]['class'] : '';
                                if($data['TranslationSetting']['show'] == 1 && !empty($maps[$fieldName]['html'])){ ?>
                                    <div class="wd-input wd-area wd-none <?php echo $class ?>">
                                        <label><?php echo !empty($maps[$fieldName]['label']) ? $maps[$fieldName]['label'] : ''; ?></label>
                                        <?php echo !empty($maps[$fieldName]['html']) ? $maps[$fieldName]['html'] : ''; ?>
                                    </div>
                                <?php }
                            }
                        }
                        foreach($translation_data as $data){
                            if( $data['Translation']['field'] == 'project_details')continue;
                            $fieldName = $data['Translation']['field'];
                            $class = isset($maps2[$fieldName]['class']) ? $maps2[$fieldName]['class'] : '';
                            if($data['TranslationSetting']['show'] == 1 && !empty($maps2[$fieldName]['html']) && empty($maps[$fieldName])){ ?>
                                <div class="wd-input wd-area wd-none <?php echo $class ?>">
                                    <label><?php echo !empty($maps2[$fieldName]['label']) ? $maps2[$fieldName]['label'] : ''; ?></label>
                                    <?php echo !empty($maps2[$fieldName]['html']) ? $maps2[$fieldName]['html'] : ''; ?>
                                </div>
                            <?php }
                        }
                    ?>
                </div>
            <!-- </fieldset> -->
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
    <div class="wd-popup-action">
        <div class="buton-action">
            <a class="btn-submit" href="#">Créer votre projet</a>
        </div>
    </div>
</div>
<script type="text/javascript">
  
    $(function(){
        $("#start_date, #end_date").datepicker({
            showOn          : 'button',
            buttonImage     : '<?php echo $html->url("../../img/new-icon/date.png") ?>',
            buttonImageOnly : true,
            dateFormat      : 'yy-mm-dd'
        });
        var heightToTop = $('#addProjectTemplate').offset().top;
        var screenHeight = $(window).height();
        $(window).resize(function(){
            var heightToTop = parseInt($('#addProjectTemplate').offset().top);
            var screenHeight = parseInt($(window).height());

            if( heightToTop && screenHeight ){
                var poupHeight = screenHeight - (heightToTop + 173); //Trên 69, dưới 79 , box shadow 5, spacing 20
                $('#addProjectTemplate').find('#add-form').css('max-height', poupHeight);
            }
        });
    });
    $('.btn-submit').click(function(){

        if($('#project_name').val() == ''){
            $('#project_name').css('border', '1px solid red');
            return;
        }

        if($('.wd-project-manager').text() == ''){
            $('.wd-project-manager').css('border', '1px solid red');
            return;
        }

        $("#add-form").submit();

    });
    $(document).ready(function(){
        /**
         * Function Filter
         */
         var showAllFieldYourform = 1;
        var  currentPhase = $('#wd-data-project-7').find('.wd-data-manager'),
            currentPhaseDatas = <?php echo !empty($phasePlans) ? json_encode($phasePlans) : json_encode(array());?>,
            ProjectMultiLists = <?php echo !empty($ProjectMultiLists) ? json_encode($ProjectMultiLists) : json_encode(array());?>,
            phaseHaveTasks = <?php echo !empty($phaseHaveTasks) ? json_encode($phaseHaveTasks) : json_encode(array());?>;
            var initMenuFilter = function($menu, $check){
                if($check === 'CR') {
                    var $filter = $('<div class="context-menu-filter-7 menu-filter"><span><input type="text" rel="no-history"></span></div>');
                } else if($check === 'ML1'){
                    var $filter = $('<div class="context-menu-filter-8 menu-filter"><span><input type="text" rel="no-history"></span></div>');
                } else if($check === 'ML2'){
                    var $filter = $('<div class="context-menu-filter-9 menu-filter"><span><input type="text" rel="no-history"></span></div>');
                } else if($check === 'ML3'){
                    var $filter = $('<div class="context-menu-filter-10 menu-filter"><span><input type="text" rel="no-history"></span></div>');
                } else if($check === 'ML4'){
                    var $filter = $('<div class="context-menu-filter-11 menu-filter"><span><input type="text" rel="no-history"></span></div>');
                } else if($check === 'ML5'){
                    var $filter = $('<div class="context-menu-filter-12 menu-filter"><span><input type="text" rel="no-history"></span></div>');
                } else if($check === 'ML6'){
                    var $filter = $('<div class="context-menu-filter-13 menu-filter"><span><input type="text" rel="no-history"></span></div>');
                } else if($check === 'ML7'){
                    var $filter = $('<div class="context-menu-filter-14 menu-filter"><span><input type="text" rel="no-history"></span></div>');
                } else if($check === 'ML8'){
                    var $filter = $('<div class="context-menu-filter-15 menu-filter"><span><input type="text" rel="no-history"></span></div>');
                } else if($check === 'ML9'){
                    var $filter = $('<div class="context-menu-filter-16 menu-filter"><span><input type="text" rel="no-history"></span></div>');
                } else {
                    var $filter = $('<div class="context-menu-filter-17 menu-filter"><span><input type="text" rel="no-history"></span></div>');
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
            //     e.preventDefault();
            //     $(this).closest('.wd-multiselect').find('.wd-combobox-content').toggle();
            // });
            // $('body').on('click', function(e){
            //     if(!( $(e.target).hasClass('wd-combobox-content') || $('.wd-combobox-content').find(e.target).length) && !($(e.target).hasClass('wd-combobox') || $('.wd-combobox').find(e.target).length) ){
            //         $('.wd-combobox-content').hide();
            //     }
            // });

            $('.wd-combobox-7').click(function(){
                var checked = $(this).attr('checked');
                $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-8, #wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
                $('.context-menu-filter, .context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
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
                $('#wd-data-project-7, #wd-data-project-9, #wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
                $('.context-menu-filter-7, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
                $('.wd-combobox-7, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
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
                $('#wd-data-project-7, #wd-data-project-8, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
                $('.context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
                $('.wd-combobox-7,.wd-combobox-8, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
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
                $('#wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
                $('.context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
                $('.wd-combobox-7,.wd-combobox-8, .wd-combobox-9, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
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
                $('#wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
                $('.context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
                $('.wd-combobox-7,.wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
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
                $('#wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
                $('.context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
                $('.wd-combobox-7,.wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
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
                $('#wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
                $('.context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
                $('.wd-combobox-7,.wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
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
                $('#wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
                $('.context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
                $('.wd-combobox-7,.wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
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
                $('#wd-data-project-6, #wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
                $('.context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
                $('.wd-combobox-7,.wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
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
                $('#wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-17').css('display', 'none');
                $('.context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-17').css('display', 'none');
                $('.wd-combobox-7,.wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-17').removeAttr('checked');
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
                $('#wd-data-project-7, #wd-data-project-8,#wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16').css('display', 'none');
                $('.context-menu-filter-6, .context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16').css('display', 'none');
                $('.wd-combobox-7,.wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16').removeAttr('checked');
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
                            $(e.target).attr('class').split(' ')[0] == 'currentPhase'
                            || $(e.target).attr('class').split(' ')[0] == 'listMulti' || $(e.target).attr('class').split(' ')[0] == 'listMulti_1' || $(e.target).attr('class').split(' ')[0] == 'listMulti_2' || $(e.target).attr('class').split(' ')[0] == 'listMulti_3' || $(e.target).attr('class').split(' ')[0] == 'listMulti_4' || $(e.target).attr('class').split(' ')[0] == 'listMulti_5'
                            || $(e.target).attr('class').split(' ')[0] == 'listMulti_6' || $(e.target).attr('class').split(' ')[0] == 'listMulti_7' || $(e.target).attr('class').split(' ')[0] == 'listMulti_8' || $(e.target).attr('class').split(' ')[0] == 'listMulti_9' || $(e.target).attr('class').split(' ')[0] == 'listMulti_10'
                        )
                    ) ||
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
                    $('.context-menu-filter-7, .context-menu-filter-8, .context-menu-filter-9, .context-menu-filter-10, .context-menu-filter-11, .context-menu-filter-12, .context-menu-filter-13, .context-menu-filter-14, .context-menu-filter-15, .context-menu-filter-16, .context-menu-filter-17').css('display', 'none');
                    $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7, #wd-data-project-8, #wd-data-project-9, #wd-data-project-10, #wd-data-project-11, #wd-data-project-12, #wd-data-project-13, #wd-data-project-14, #wd-data-project-15, #wd-data-project-16, #wd-data-project-17').css('display', 'none');
                    $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7, .wd-combobox-8, .wd-combobox-9, .wd-combobox-10, .wd-combobox-11, .wd-combobox-12, .wd-combobox-13, .wd-combobox-14, .wd-combobox-15, .wd-combobox-16, .wd-combobox-17').removeAttr('checked');
                }
            });

            jQuery.removeFromArray = function(value, arr) {
                return jQuery.grep(arr, function(elem, index) {
                    return elem !== value;
                });
            };
    
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
            var valList = $(data).find('#project_phase_id').val();
            if(currentPhaseDatas){
                $.each(currentPhaseDatas, function(idPlan, idPhase){
                    if(valList == idPhase){
                        $(data).find('#project_phase_id').attr('checked', 'checked');
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
            $(data).find('#project_phase_id').click(function(){
                var _datas = $(this).val();
                if($(this).is(':checked')){
                    $ids_7.push(_datas);
                    if($ids_7.length > 0){
                        $('a.wd-combobox-7').find('p').css('display', 'none');
                    }
                    $('a.wd-combobox-7').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                } else {
                    $ids_7 = jQuery.removeFromArray(_datas, $ids_7);
                    if($ids_7.length == 0){
                        $('a.wd-combobox-7').find('p').css('display', 'block');
                    }
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
                var valList = $(data).find('#project_list_multi_1').val();
                if(ProjectMultiLists['project_list_multi_1']){
                    $.each(ProjectMultiLists['project_list_multi_1'], function(idPlan, idPhase){
                        if(valList == idPhase){
                            $(data).find('#project_list_multi_1').attr('checked', 'checked');
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
                $(data).find('#project_list_multi_1').on('hover', function(){
                    var _datas = $(this).val();
                    if($(this).is(':checked')){
                        $ids_8.push(_datas);
                        if($ids_8.length > 0){
                            $('a.wd-combobox-8').find('p').css('display', 'none');
                        }
                        $('a.wd-combobox-8').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                    } else {
                        $ids_8 = jQuery.removeFromArray(_datas, $ids_8);
                        if($ids_8.length == 0){
                            $('a.wd-combobox-8').find('p').css('display', 'block');
                        }
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
                var valList = $(data).find('#project_list_multi_2').val();
                if(ProjectMultiLists['project_list_multi_2']){
                    $.each(ProjectMultiLists['project_list_multi_2'], function(idPlan, idPhase){
                        if(valList == idPhase){
                            $(data).find('#project_list_multi_2').attr('checked', 'checked');
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
                $(data).find('#project_list_multi_2').click(function(){
                    var _datas = $(this).val();
                    if($(this).is(':checked')){
                        $ids_9.push(_datas);
                        if($ids_9.length > 0){
                            $('a.wd-combobox-9').find('p').css('display', 'none');
                        }
                        $('a.wd-combobox-9').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                    } else {
                        $ids_9 = jQuery.removeFromArray(_datas, $ids_9);
                        if($ids_9.length == 0){
                            $('a.wd-combobox-9').find('p').css('display', 'block');
                        }
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
                var valList = $(data).find('#project_list_multi_3').val();
                if(ProjectMultiLists['project_list_multi_3']){
                    $.each(ProjectMultiLists['project_list_multi_3'], function(idPlan, idPhase){
                        if(valList == idPhase){
                            $(data).find('#project_list_multi_3').attr('checked', 'checked');
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
                $(data).find('#project_list_multi_3').click(function(){
                    var _datas = $(this).val();
                    if($(this).is(':checked')){
                        $ids_10.push(_datas);
                        if($ids_10.length > 0){
                            $('a.wd-combobox-10').find('p').css('display', 'none');
                        }
                        $('a.wd-combobox-10').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                    } else {
                        $ids_10 = jQuery.removeFromArray(_datas, $ids_10);
                        if($ids_10.length == 0){
                            $('a.wd-combobox-10').find('p').css('display', 'block');
                        }
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
                var valList = $(data).find('#project_list_multi_4').val();
                if(ProjectMultiLists['project_list_multi_4']){
                    $.each(ProjectMultiLists['project_list_multi_4'], function(idPlan, idPhase){
                        if(valList == idPhase){
                            $(data).find('#project_list_multi_4').attr('checked', 'checked');
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
                $(data).find('#project_list_multi_4').click(function(){
                    var _datas = $(this).val();
                    if($(this).is(':checked')){
                        $ids_11.push(_datas);
                        if($ids_11.length > 0){
                            $('a.wd-combobox-11').find('p').css('display', 'none');
                        }
                        $('a.wd-combobox-11').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                    } else {
                        $ids_11 = jQuery.removeFromArray(_datas, $ids_11);
                        if($ids_11.length == 0){
                            $('a.wd-combobox-11').find('p').css('display', 'block');
                        }
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
                var valList = $(data).find('#project_list_multi_5').val();
                if(ProjectMultiLists['project_list_multi_5']){
                    $.each(ProjectMultiLists['project_list_multi_5'], function(idPlan, idPhase){
                        if(valList == idPhase){
                            $(data).find('#project_list_multi_5').attr('checked', 'checked');
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
                $(data).find('#project_list_multi_5').click(function(){
                    var _datas = $(this).val();
                    if($(this).is(':checked')){
                        $ids_12.push(_datas);
                        if($ids_12.length > 0){
                            $('a.wd-combobox-12').find('p').css('display', 'none');
                        }
                        $('a.wd-combobox-12').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                    } else {
                        $ids_12 = jQuery.removeFromArray(_datas, $ids_12);
                            if($ids_12.length == 0){
                            $('a.wd-combobox-12').find('p').css('display', 'block');
                        }
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
                var valList = $(data).find('#project_list_multi_6').val();
                if(ProjectMultiLists['project_list_multi_6']){
                    $.each(ProjectMultiLists['project_list_multi_6'], function(idPlan, idPhase){
                        if(valList == idPhase){
                            $(data).find('#project_list_multi_6').attr('checked', 'checked');
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
                $(data).find('#project_list_multi_6').click(function(){
                    var _datas = $(this).val();
                    if($(this).is(':checked')){
                        $ids_13.push(_datas);
                        if($ids_13.length > 0){
                            $('a.wd-combobox-13').find('p').css('display', 'none');
                        }
                        $('a.wd-combobox-13').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                    } else {
                        $ids_13 = jQuery.removeFromArray(_datas, $ids_13);
                        if($ids_13.length == 0){
                            $('a.wd-combobox-13').find('p').css('display', 'block');
                        }
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
                var valList = $(data).find('#project_list_multi_7').val();
                if(ProjectMultiLists['project_list_multi_7']){
                    $.each(ProjectMultiLists['project_list_multi_7'], function(idPlan, idPhase){
                        if(valList == idPhase){
                            $(data).find('#project_list_multi_7').attr('checked', 'checked');
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
                $(data).find('#project_list_multi_7').click(function(){
                    var _datas = $(this).val();
                    if($(this).is(':checked')){
                        $ids_14.push(_datas);
                        if($ids_14.length > 0){
                            $('a.wd-combobox-14').find('p').css('display', 'none');
                        }
                        $('a.wd-combobox-14').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                    } else {
                        $ids_14 = jQuery.removeFromArray(_datas, $ids_14);
                        if($ids_14.length == 0){
                            $('a.wd-combobox-14').find('p').css('display', 'block');
                        }
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
                var valList = $(data).find('#project_list_multi_8').val();
                if(ProjectMultiLists['project_list_multi_8']){
                    $.each(ProjectMultiLists['project_list_multi_8'], function(idPlan, idPhase){
                        if(valList == idPhase){
                            $(data).find('#project_list_multi_8').attr('checked', 'checked');
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
                $(data).find('#project_list_multi_8').click(function(){
                    var _datas = $(this).val();
                    if($(this).is(':checked')){
                        $ids_15.push(_datas);
                        if($ids_15.length > 0){
                            $('a.wd-combobox-15').find('p').css('display', 'none');
                        }
                        $('a.wd-combobox-15').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                    } else {
                        $ids_15 = jQuery.removeFromArray(_datas, $ids_15);
                        if($ids_15.length == 0){
                            $('a.wd-combobox-15').find('p').css('display', 'block');
                        }
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
                var valList = $(data).find('#project_list_multi_9').val();
                if(ProjectMultiLists['project_list_multi_9']){
                    $.each(ProjectMultiLists['project_list_multi_9'], function(idPlan, idPhase){
                        if(valList == idPhase){
                            $(data).find('#project_list_multi_9').attr('checked', 'checked');
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
                $(data).find('#project_list_multi_9').click(function(){
                    var _datas = $(this).val();
                    if($(this).is(':checked')){
                        $ids_16.push(_datas);
                        if($ids_16.length > 0){
                            $('a.wd-combobox-16').find('p').css('display', 'none');
                        }
                        $('a.wd-combobox-16').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                    } else {
                        $ids_16 = jQuery.removeFromArray(_datas, $ids_16);
                        if($ids_16.length == 0){
                            $('a.wd-combobox-16').find('p').css('display', 'block');
                        }
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
                var valList = $(data).find('#project_list_multi_10').val();
                if(ProjectMultiLists['project_list_multi_10']){
                    $.each(ProjectMultiLists['project_list_multi_10'], function(idPlan, idPhase){
                        if(valList == idPhase){
                            $(data).find('#project_list_multi_10').attr('checked', 'checked');
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
                $(data).find('#project_list_multi_10').click(function(){
                    var _datas = $(this).val();
                    if($(this).is(':checked')){
                        $ids_17.push(_datas);
                        if($ids_17.length > 0){
                            $('a.wd-combobox-17').find('p').css('display', 'none');
                        }
                        $('a.wd-combobox-17').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
                    } else {
                        $ids_17 = jQuery.removeFromArray(_datas, $ids_17);
                        if($ids_17.length == 0){
                            $('a.wd-combobox-17').find('p').css('display', 'block');
                        }
                        $('a.wd-combobox-17').find('.wd-dt-' +_datas).remove();
                        $('a.wd-combobox-17').find('.wd-em-' +_datas).remove();
                    }
                });
            });
    });
</script>