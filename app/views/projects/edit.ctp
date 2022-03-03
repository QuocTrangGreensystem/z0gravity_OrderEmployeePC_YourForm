<?php
App::import('Model', 'Project');
App::import('Model', 'Company');
function _getPath($project_id) {
    $Project = new Project();
    $Company = new Company();
    $company = $Project->find('first', array(
        'recursive' => 0,
        'fields' => array(
            'Company.parent_id',
            'Company.company_name',
            'Company.dir'
        ), 'conditions' => array('Project.id' => $project_id)));
    $pcompany = $Company->find('first', array(
        'recursive' => -1, 'conditions' => array('Company.id' => $company['Company']['parent_id'])));
    $path = FILES . 'projects' . DS . 'globalviews' . DS;
    if ($pcompany) {
        $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['dir'])) . DS;
    }
    $path .= $company['Company']['dir'] . DS;
    return $path;
}
?>

<?php echo $html->script('jquery.validation.min'); ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->script('jquery.dataTables'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<?php echo $html->script('validateDate'); ?>
<?php echo $html->script('tinymce/tinymce.min'); ?>

<style>

    fieldset div textarea{
        width: 85%;
    }
    .error-message {
        color: #FF0000;
        margin-left: 35px;
    }
    .wd-input label {width: 154px !important;}
    .wd-input .ui-combobox {
        width: 60%;
    }
    .multiselect {
        width: 59.8%;
        border-right: solid 1px #c0c0c0;
        overflow: hidden;
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
    .inputcheckbox{
        width: auto !important;
        position: absolute;
        top: 75px;
        left: 530px;
    }
    .inputcheckbox-none{display: none;}
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
    #wd-container-footer{
        display: none;
    }
    body{
        overflow: hidden;
    }
</style>
<div id="wd-container-main" class="wd-project-detail">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>

            <div class="wd-tab">
                <?php //echo $this->element('project_tab') ?>
                <div class="wd-panel" style="overflow: auto">
                    <div class="wd-title">
                        <button type="button" id="idButtonSave" class="btn-text btn-green" onclick="if(validateForm()){jQuery('#wd-fragment-1 form:first').submit();};"/>
                            <img src="<?php echo $html->url('/img/ui/blank-save.png') ?>" alt="" />
                            <span><?php __('Save') ?></span>
                        </button>
                        <a href="<?php echo $html->url(array('action' => 'edit', $project_name['Project']['id']));?>" class="btn-text btn-red">
                            <img src="<?php echo $html->url('/img/ui/blank-reset.png') ?>" alt="" />
                            <span><?php __('Reset') ?></span>
                        </a>
                        <a href="<?php echo $html->url("/project_phase_plans/phase_vision/" . $project_name['Project']['id']) ?>" class="btn btn-gantt" title="<?php __('Gantt+') ?>"><span><?php __('Gantt+') ?></span></a>
                        <a href="<?php echo $html->url("/projects/exportExcelDetail/" . $project_name['Project']['id']) ?>" class="btn btn-excel" title="<?php __('Export Excel')?>"><span><?php __('Export Excel') ?></span></a>
                        <?php
                            $_h = '';
                        if ( !empty($employeeInfo['Employee']['change_status_project']) && $employeeInfo['Employee']['change_status_project'] != 1 && $employeeInfo['Role']['name'] == 'pm' ){
                            $_h = 'disabled"';
                        }
                        ?>
                        <select <?php echo $_h ?> id="pseudo-category">
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

                        <?php if( $this->data['Project']['category'] == 2 ): ?>
                        <input type="checkbox" <?php echo $this->data['Project']['is_staffing'] ? 'checked' : '' ?> id="ProjectIsStaffing" />
                        <?php endif ?>
                    </div>
                    <script>
                    var temp=setInterval(function(){
                        var doc = window.frames['iframe_custom'].document;
                        $(doc).ready(function(){
                            $('body',doc).prepend('<style>body,html{border:none;padding:0 !important}img{margin:0 !important; max-height:300px}</style><!--[if IE]><style>img{height:270px}</style><![endif]-->');
                        });
                        clearInterval(temp);
                    },1000);
                    </script>
                    <p style="clear: both; color: rgb(255, 2, 2); font-size: 11px; font-weight: bold;">
                        <?php
                            if( !($time = $this->data['Project']['last_modified']) ){
                                $time = $project_name['Project']['updated'];
                            }
                            $theTime = $time ? date('H:i:s', $time) : '';
                            $theDate = $time ? date('d/m/Y', $time) : '../../....';
                            $byEmployee = !empty($project_name['Project']['update_by_employee']) ? $project_name['Project']['update_by_employee'] : 'N/A';
                            echo str_replace(
                                array(
                                    '{time}', '{date}', '{resource}'
                                ),
                                array(
                                    $theTime, $theDate, $byEmployee
                                ),
                                __d(sprintf($_domain, 'Details'), 'Last Update: {time} {date} by {resource}', true)
                            );
                            //__('Last Update: ', true) . $updated . __(' by ', true) . $byEmployee;
                        ?>
                    </p>
                    <div class="wd-section" id="wd-fragment-1">
                        <h2 class="wd-t2"><?php __d(sprintf($_domain, 'Details'), "Project details"); ?></h2>
                        <?php echo $this->Session->flash(); ?>
                        <?php
                        echo $this->Form->create('Project', array('enctype' => 'multipart/form-data', 'id' => 'ProjectEditForm'));
                        echo $this->Form->input('id');
                        App::import("vendor", "str_utility");
                        $str_utility = new str_utility();
                        echo $this->Form->input('tmp_activity', array('div' => false, 'label' => false, 'type' => 'hidden'));
                        ?>
                        <fieldset>
                            <div class="wd-scroll-form" style="height:auto;">
                                <div class="wd-section" id="wd-fragment-2">
                                    <?php
                                    $link = $this->Html->url(array('controller' => 'project_global_views', 'action' => 'attachment', $project_name['Project']['id'], '?' => array('sid' => $api_key)), true);
                                    if (!empty($this->data['ProjectGlobalView'][0]['attachment'])) {
                                        $view = $this->data['ProjectGlobalView'][0];
                                        if( $view['is_file'] == 1 && !file_exists(_getPath($project_name['Project']['id']) . $view['attachment']) )
                                            $link = 'about:blank';
                                        else if (!preg_match('/\.(jpg|jpeg|bmp|gif|png|swf)$/i', $view['attachment'])) {
                                            $link = 'https://docs.google.com/gview?url=' . ($link) . '&embedded=true';
                                        }
                                    }
                                    ?>
                                    <iframe name="iframe_custom" id="iframe_custom" src="<?php echo $link; ?>" style=" padding:4px; width: 80%;height: 300px; border: 1px solid #D8D8D8; margin-left: 160px;"></iframe>
                                </div>
                                <div class="wd-left-content">
                                        <?php echo $this->Form->hidden('category') ?>
                                        <?php echo $this->Form->hidden('is_staffing', array('id' => 'project-is-staffing')) ?>
                                    <div class="wd-input">
                                        <label for="project-name"><?php __d(sprintf($_domain, 'Details'), "Project Name") ?></label>
                                        <?php
                                        echo $this->Form->input('project_name', array('div' => false, 'label' => false, 'maxlength' => 124,
                                            "style" => "padding: 6px 2px; width: 62%; border-color: red"));
                                        ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="project_code_1"><?php __d(sprintf($_domain, 'Details'), "Project Code 1") ?></label>
                                        <?php
                                        echo $this->Form->input('project_code_1', array('div' => false, 'label' => false,
                                            "style" => "padding: 6px 2px; width: 62%",
                                            'id' => 'onChangeCode'
                                        )).'<p style="display: none; float:left; color: #000; width: 62%" id= "valueOnChange"></p>';
                                        ?>
                                    </div>

                                    <div class="wd-input">
                                        <label for="Company"><?php __d(sprintf($_domain, 'Details'), "Company") ?></label>
                                        <p style ="padding-top:6px;"><?php echo $name_company ?></p>
                                    </div>
                                    <div class="wd-input wd-input-80">
                                        <label for="budget"><?php __d(sprintf($_domain, 'Details'), "Project type") ?></label>
                                        <?php
                                        echo $this->Form->input('project_type_id', array('div' => false, 'label' => false,
                                            "empty" => __("--Select--", true),
                                            'style' => 'margin-right:11px; width:31% !important',
                                            "options" => $ProjectTypes));
                                        ?>
                                        <?php
                                        echo $this->Form->input('project_sub_type_id', array('div' => false, 'label' => false,
                                            'style' => 'width:30% !important',
                                            'empty' => __d(sprintf($_domain, 'Details'), "Sub type", true),
                                            "options" => $ProjectSubTypes));
                                        ?>
                                    </div>
                                    <div class="wd-input wd-input-80">
                                        <label for="budget"><?php __d(sprintf($_domain, 'Details'), "Program") ?></label>
                                        <?php
                                        echo $this->Form->input('project_amr_program_id', array('div' => false, 'label' => false,
                                            "empty" => __("--Select--", true),
                                            'style' => 'margin-right:11px; width:31% !important',
                                            "options" => $ProjectArmPrograms));
                                        ?>
                                        <?php
                                        echo $this->Form->input('project_amr_sub_program_id', array('div' => false, 'label' => false,
                                            'style' => 'width:30% !important',
                                            'empty' => __d(sprintf($_domain, 'Details'), "Sub program", true),
                                            "options" => $ProjectArmSubPrograms));
                                        ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="priority"><?php __d(sprintf($_domain, 'Details'), "Priority") ?></label>
                                        <?php echo $this->Form->input('project_priority_id', array('div' => false, 'label' => false, "options" => $Priorities, 'empty' => __("--Select--", true))); ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="Implementation_complexity"><?php __d(sprintf($_domain, 'Details'), "Implementation Complexity") ?></label>
                                        <?php echo $this->Form->input('complexity_id', array('div' => false, 'label' => false, "options" => $Complexities, 'empty' => __("--Select--", true))); ?>
                                    </div>
                                    <div class="wd-input">
                                        <label><?php __d(sprintf($_domain, 'Details'), 'Created value') ?></label>
                                        <?php
                                        echo $this->Form->input('created_value', array('div' => false, 'label' => false,
                                            "class" => "placeholder", "placeholder" => __("Created value", true), "readonly" => 'readonly',
                                            'style' => 'width:26% !important;'));
                                        ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="status"><?php __d(sprintf($_domain, 'Details'), "Status") ?></label>
                                        <?php echo $this->Form->input('project_status_id', array('div' => false, 'label' => false, "options" => $Statuses, 'empty' => __("--Select--", true))); ?>
                                    </div>
                                    <!--div class="wd-input">
                                        <label for="current-phase"><?php //__d(sprintf($_domain, 'Details'), "Current Phase") ?></label>
                                        <?php //echo $this->Form->input('project_phase_id', array('div' => false, 'label' => false, "options" => $ProjectPhases, 'empty' => false)); ?>
                                    </div-->
                                    <div class="wd-input">
                                        <label for="currentPhase"><?php __d(sprintf($_domain, 'Details'), "Current Phase") ?></label>
                                        <div class="multiselect" style="float: left;margin-right: 3px; width: 63%;">
                                            <a href="" class="wd-combobox-7"></a>
                                            <div id="wd-data-project-7" style="display: none;" class="currentPhase">
                                                <?php foreach($ProjectPhases as $idPm => $namePm):?>
                                                <div class="currentPhase wd-data-manager wd-group-<?php echo $idPm;?>">
                                                    <p class="currentPhase wd-data" style="width: 300px; margin: 10px 5px;">
                                                        <?php
                                                            echo $this->Form->input('project_phase_id', array(
                                                                'label' => false,
                                                                'div' => false,
                                                                'type' => 'checkbox',
                                                                'class' => 'currentPhase',
                                                                'name' => 'data[project_phase_id][]',
                                                                'value' => $idPm));
                                                        ?>
                                                        <span class="currentPhase" style="padding-left: 5px;"><?php echo $namePm;?></span>
                                                    </p>
                                                </div>
                                                <?php endforeach;?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                        /*foreach($ProjectActivities as $k => $v){
                                            foreach($listIdActivity as $key => $value){
                                                if($k == $value){
                                                    unset($ProjectActivities[$k]);
                                                }
                                            }
                                        }*/
                                    ?>
                                    <div class="wd-input">
                                        <label for="current-phase"><?php __d(sprintf($_domain, 'Details'), "Link To RMS Activity") ?></label>
                                        <?php echo $this->Form->input('activity_id', array(
                                        'type'      => 'select',
                                        'div'       => false,
                                        'label'     => false,
                                        "options"   => $ProjectActivities,
                                        'empty'     => __("--Select--", true)));
                                        echo $this->Form->input('tmp_activity_id', array(
                                            'type' => 'hidden',
                                            'value' => $this->data['Project']['activity_id']
                                        ));
                                        ?>
                                    </div>
                                </div>
                                <div class="wd-right-content">
                                    <div class="wd-input">
                                        <label for="priority"><?php __d(sprintf($_domain, 'Details'), "Project long name") ?></label>
                                        <?php
                                        echo $this->Form->input('long_project_name', array('div' => false, 'label' => false,
                                            "style" => "padding: 6px 2px; width: 59%"));
                                        ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="project_code_2"><?php __d(sprintf($_domain, 'Details'), "Project Code 2") ?></label>
                                        <?php
                                        echo $this->Form->input('project_code_2', array('div' => false, 'label' => false,
                                            "style" => "padding: 6px 2px; width: 59%"));
                                        ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="projectManager"><?php __d(sprintf($_domain, 'Details'), "Project Manager") ?></label>
                                        <?php //if ($changeProjectManager) {?>
                                        <div class="multiselect" style="float: left;margin-right: 3px">
                                            <a href="" class="wd-combobox" style="border-color: red"></a>
                                            <div id="wd-data-project" style="display: none;" class="projectManager">
                                                <?php foreach($employees['pm'] as $idPm => $namePm):?>
                                                <div class="projectManager wd-data-manager wd-group-<?php echo $idPm;?>">
                                                    <p class="projectManager wd-data" style="width: 200px; margin: 10px 5px;">
                                                        <?php
                                                            echo $this->Form->input('project_employee_manager', array(
                                                                'label' => false,
                                                                'div' => false,
                                                                'type' => 'checkbox',
                                                                'class' => 'projectManager',
                                                                'name' => 'data[project_employee_manager][]',
                                                                'value' => $idPm));
                                                        ?>
                                                        <span class="projectManager" style="padding-left: 5px;"><?php echo $namePm;?></span>
                                                    </p>
                                                </div>
                                                <?php endforeach;?>
                                            </div>
                                        </div>
                                        <?php
                                        //} else {
//                                            $_fullname = $this->data['Employee']['first_name'] . ' ' . $this->data['Employee']['last_name'];
//                                            $fullNameBackup = '';
//                                            if(!empty($employBackups)){
//                                                foreach($employBackups as $bk){
//                                                    $fullNameBackup .= ', '.$bk['Employee']['first_name'] .' '. $bk['Employee']['last_name'] .'(B)';
//                                                }
//                                            }
//                                            echo '<b style="display:block;margin-top : 7px;color:#00477B; overflow: hidden; min-width: -19px; width: 415px; float: left; margin-right: -35px;">' . $_fullname . $fullNameBackup . '</b>';
//                                            echo $this->Form->hidden('project_manager_id');
//                                        }
                                        //$avatarPm = !empty($avatarOfEmploys[$this->data['Project']['project_manager_id']]) ? $avatarOfEmploys[$this->data['Project']['project_manager_id']] : '';
                                        $urlPm = $this->UserFile->avatar($this->data['Project']['project_manager_id']);
                                        ?>
                                        <img src="<?php echo $urlPm;?>" />
                                    </div>
                                    <div class="wd-input">
                                        <label for="chiefBusiness"><?php __d(sprintf($_domain, 'Details'), "Chief Business") ?></label>
                                        <div class="multiselect" style="float: left;margin-right: 3px;">
                                            <a href="" class="wd-combobox-2"></a>
                                            <div id="wd-data-project-2" style="display: none;" class="chiefBusiness">
                                                <?php foreach($employees['pm'] as $idPm => $namePm):?>
                                                <div class="chiefBusiness wd-data-manager wd-group-<?php echo $idPm;?>">
                                                    <p class="chiefBusiness wd-data" style="width: 200px; margin: 10px 5px;">
                                                        <?php
                                                            echo $this->Form->input('chief_business_list', array(
                                                                'label' => false,
                                                                'div' => false,
                                                                'type' => 'checkbox',
                                                                'class' => 'chiefBusiness',
                                                                'name' => 'data[chief_business_list][]',
                                                                'value' => $idPm));
                                                        ?>
                                                        <span class="chiefBusiness" style="padding-left: 5px;"><?php echo $namePm;?></span>
                                                    </p>
                                                </div>
                                                <?php endforeach;?>
                                            </div>
                                        </div>
                                        <?php
                                        $urlCB = $this->UserFile->avatar($this->data['Project']['chief_business_id']);
                                        ?>
                                        <img src="<?php echo $urlCB;?>" />
                                    </div>
                                    <div class="wd-input">
                                        <label for="technicalManager"><?php __d(sprintf($_domain, 'Details'), "Technical manager") ?></label>
                                        <div class="multiselect" style="float: left;margin-right: 3px;">
                                            <a href="" class="wd-combobox-3"></a>
                                            <div id="wd-data-project-3" style="display: none;" class="technicalManager">
                                                <?php foreach($employees['pm'] as $idPm => $namePm):?>
                                                <div class="technicalManager wd-data-manager wd-group-<?php echo $idPm;?>">
                                                    <p class="technicalManager wd-data" style="width: 200px; margin: 10px 5px;">
                                                        <?php
                                                            echo $this->Form->input('technical_manager_list', array(
                                                                'label' => false,
                                                                'div' => false,
                                                                'type' => 'checkbox',
                                                                'class' => 'technicalManager',
                                                                'name' => 'data[technical_manager_list][]',
                                                                'value' => $idPm));
                                                        ?>
                                                        <span class="technicalManager" style="padding-left: 5px;"><?php echo $namePm;?></span>
                                                    </p>
                                                </div>
                                                <?php endforeach;?>
                                            </div>
                                        </div>
                                        <?php
                                        $urlTM= $this->UserFile->avatar($this->data['Project']['technical_manager_id']);
                                        ?>
                                        <img src="<?php echo $urlTM;?>" />
                                    </div>
                                    <?php if(!$adminSeeAllProjects):?>
                                    <div class="wd-input">
                                        <label for="readAccess"><?php echo __d(sprintf($_domain, 'Details'), 'Read Access', true) ?></label>
                                        <div class="multiselect" style="float: left;margin-right: 3px;">
                                            <a href="" class="wd-combobox-6"></a>
                                            <div id="wd-data-project-6" style="display: none;" class="readAccess">
                                                <?php foreach($employees['tech'] as $idPm => $namePm):?>
                                                <div class="readAccess wd-data-manager wd-group-<?php echo $idPm . '-0';?>">
                                                    <p class="readAccess wd-data" style="width: 200px; margin: 10px 5px;">
                                                        <?php
                                                            echo $this->Form->input('read_access', array(
                                                                'label' => false,
                                                                'div' => false,
                                                                'type' => 'checkbox',
                                                                'class' => 'readAccess',
                                                                'name' => 'data[read_access][]',
                                                                'value' => $idPm . '-0'));
                                                        ?>
                                                        <span class="readAccess" style="padding-left: 5px;"><?php echo $namePm;?></span>
                                                    </p>
                                                </div>
                                                <?php endforeach;?>
                                                <?php foreach($profitCenters as $idPc => $namePc):?>
                                                <div class="readAccess wd-data-manager wd-group-<?php echo $idPc . '-1';?>">
                                                    <p class="readAccess wd-data" style="width: 350px; margin: 10px 5px;">
                                                        <?php
                                                            echo $this->Form->input('read_access', array(
                                                                'label' => false,
                                                                'div' => false,
                                                                'type' => 'checkbox',
                                                                'class' => 'readAccess',
                                                                'name' => 'data[read_access][]',
                                                                'value' => $idPc . '-1'));
                                                        ?>
                                                        <span class="readAccess" style="padding-left: 5px;"><?php echo 'PC / ' . $namePc;?></span>
                                                    </p>
                                                </div>
                                                <?php endforeach;?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif;?>
                                    <?php
                                        //added on 2015-04-28 by QN
                                        $setting = $this->requestAction('/translations/getSetting', array('pass' => array('Functional leader', 'Details')));
                                        if( $setting['show'] ):
                                    ?>
                                    <div class="wd-input">
                                        <label for="functionalLeader"><?php __d(sprintf($_domain, 'Details'), "Functional leader") ?></label>
                                        <div class="multiselect" style="float: left;margin-right: 3px;">
                                            <a href="javascript:void();" class="wd-combobox-4"></a>
                                            <div id="wd-data-project-4" style="display: none;" class="functionalLeader">
                                                <?php foreach($employees['pm'] as $idPm => $namePm):?>
                                                <div class="functionalLeader wd-data-manager wd-group-<?php echo $idPm;?>">
                                                    <p class="functionalLeader wd-data" style="width: 200px; margin: 10px 5px;">
                                                        <?php
                                                            echo $this->Form->input('functional_leader_list', array(
                                                                'label' => false,
                                                                'div' => false,
                                                                //'id' => 'fl-' . $idPm,
                                                                'type' => 'checkbox',
                                                                'class' => 'functionalLeader',
                                                                'name' => 'data[functional_leader_list][]',
                                                                'value' => $idPm));
                                                        ?>
                                                        <span class="functionalLeader" style="padding-left: 5px;"><?php echo $namePm;?></span>
                                                    </p>
                                                </div>
                                                <?php endforeach;?>
                                            </div>
                                        </div>
                                        <?php
                                        $urlTM= $this->UserFile->avatar($this->data['Project']['functional_leader_id']);
                                        ?>
                                        <img src="<?php echo $urlTM;?>" />
                                    </div>
                                    <?php
                                        endif;
                                    ?>

                                    <?php
                                        //added on 2015-04-28 by QN
                                        $setting = $this->requestAction('/translations/getSetting', array('pass' => array('UAT manager', 'Details')));
                                        if( $setting['show'] ):
                                    ?>
                                    <div class="wd-input">
                                        <label for="uatManager"><?php __d(sprintf($_domain, 'Details'), "UAT manager") ?></label>
                                        <div class="multiselect" style="float: left;margin-right: 3px;">
                                            <a href="javascript:void();" class="wd-combobox-5"></a>
                                            <div id="wd-data-project-5" style="display: none;" class="uatManager">
                                                <?php foreach($employees['pm'] as $idPm => $namePm):?>
                                                <div class="uatManager wd-data-manager wd-group-<?php echo $idPm;?>">
                                                    <p class="uatManager wd-data" style="width: 200px; margin: 10px 5px;">
                                                        <?php
                                                            echo $this->Form->input('uat_manager_list', array(
                                                                'label' => false,
                                                                'div' => false,
                                                                'type' => 'checkbox',
                                                                'class' => 'uatManager',
                                                                'name' => 'data[uat_manager_list][]',
                                                                'value' => $idPm));
                                                        ?>
                                                        <span class="uatManager" style="padding-left: 5px;"><?php echo $namePm;?></span>
                                                    </p>
                                                </div>
                                                <?php endforeach;?>
                                            </div>
                                        </div>
                                        <?php
                                        $urlTM= $this->UserFile->avatar($this->data['Project']['uat_manager_id']);
                                        ?>
                                        <img src="<?php echo $urlTM;?>" />
                                    </div>
                                    <?php
                                        endif;
                                    ?>

                                    <div class="wd-input wd-calendar">
                                        <label for="startdate"><?php __d(sprintf($_domain, 'Details'), "Start Date") ?></label>
                                        <?php
                                        if(!empty($projectPhasePlans)){
                                            $_start_date = $projectPhasePlans[0][0]['MinStartDate'];
                                            $_end_date = $projectPhasePlans[0][0]['MaxEndDate'];
                                        }
                                        $_start_date = isset($_start_date) ? $_start_date : null;
                                        echo $this->Form->input('start_date', array('div' => false,
                                            'label' => false,
                                            'disabled' => 'disabled',
                                            'style' => 'width:59% !important; background-color: rgb(223, 223, 223)',
                                            'value' => $str_utility->convertToVNDate($_start_date),
                                            'type' => 'text'));
                                        ?>
                                    </div>
                                    <div class="wd-input wd-calendar">
                                        <label for="enddate"><?php __d(sprintf($_domain, 'Details'), "End Date") ?></label>
                                        <?php
                                        $_end_date = isset($_end_date) ? $_end_date : null;
                                        echo $this->Form->input('end_date', array('div' => false,
                                            'label' => false,
                                            'disabled' => 'disabled',
                                            'style' => 'width:59% !important; background-color: rgb(223, 223, 223)',
                                            'value' => $str_utility->convertToVNDate($_end_date),
                                            'type' => 'text'));
                                        ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="customer"><?php __d(sprintf($_domain, 'Details'), "Customer") ?></label>
                                        <?php
                                        echo $this->Form->input('budget_customer_id', array('name' => 'data[Project][budget_customer_id]',
                                            'type' => 'select',
                                            'div' => false,
                                            'label' => false,
                                            // 'class' => 'ui-combobox',
                                            'style' => 'width:60.5% !important',
                                            "empty" => __("-- Select -- ", true),
                                            "options" => (array) @$budgetCustomers));
                                        ?>
                                    </div>
                                    <?php
                                        if($this->data['Project']['category'] == 1){
                                        $disabled = 'disabled';
                                        $style = 'width:60.5% !important; background-color: rgb(218, 221, 226);';
                                        if(isset($employeeInfo['Role']['name']) && $employeeInfo['Role']['name'] === 'admin'){
                                            $disabled = '';
                                            $style = 'width:60.5% !important;';
                                        }
                                    ?>
                                    <div class="wd-input">
                                        <label for="current-phase"><?php __d(sprintf($_domain, 'Details'), "Timesheet Filling Activated") ?></label>
                                        <?php
                                            $option = array(__('No', true), __('Yes', true));
                                            echo $this->Form->input('activated', array(
                                                'div' => false,
                                                'label' => false,
                                                //'class' => 'ui-combobox',
                                                'disabled' => $disabled,
                                                "options" => $option,
                                                'style' => $style,
                                                //'empty' => false
                                            ));
                                        ?>
                                    </div>
                                    <?php
                                        } else {
                                            echo $this->Form->hidden('activated');
                                        }
                                    ?>
                                    <!--
                                    <?php $styleShow = ($this->data['Project']['category']!=2)?'display:none;':'';?>
                                    <div class="wd-input" style="<?php echo $styleShow;?>">
                                        <label for="customer"><?php __("Staffing") ?></label>
                                        <?php
                                       // $chk = true;
                                        echo $this->Form->input('is_staffing', array(
                                            'rel' => 'no-history',
                                            //'value' => 1,
                                            'label' => '',
                                            'class' => 'inputcheckbox',
                                            'type' => 'checkbox', 'legend' => false, 'fieldset' => false
                                        ));?>
                                    </div>
                                    -->
                                    <?php /*
                                    <div class="wd-input wd-calendar">
                                        <label for="originaldate"><?php __("Planned End Date") ?></label>
                                        <?php
                                        echo $this->Form->input('planed_end_date', array('div' => false,
                                            'label' => false,
                                            'style' => 'width:57.6% !important',
                                            'value' => $str_utility->convertToVNDate($this->data["Project"]["planed_end_date"]),
                                            'type' => 'text'));
                                        ?>
                                    </div>
                                    */ ?>
                                </div>
                                <div class="wd-input wd-area wd-none">
                                    <label><?php __d(sprintf($_domain, 'Details'), "Issues") ?></label>
                                    <?php echo $this->Form->input('issues', array('type' => 'textarea', 'div' => false, 'label' => false)); ?>
                                </div>
                                <div class="wd-input wd-area wd-none">
                                    <label><?php __d(sprintf($_domain, 'Details'), "Primary Objectives") ?></label>
                                    <?php echo $this->Form->input('primary_objectives', array('type' => 'textarea', 'div' => false, 'label' => false)); ?>
                                </div>
                                <div class="wd-input wd-area wd-none">
                                    <label><?php __d(sprintf($_domain, 'Details'), "Project Objectives") ?></label>
                                    <?php echo $this->Form->input('project_objectives', array('type' => 'textarea', 'div' => false, 'label' => false)); ?>
                                </div>
                                <div class="wd-input wd-area wd-none">
                                    <label for="constraint"><?php __d(sprintf($_domain, 'Details'), "Constraint") ?></label>
                                    <?php echo $this->Form->input('constraint', array('type' => 'textarea', 'div' => false, 'label' => false)); ?>
                                </div>
                                <div class="wd-input wd-area wd-none">
                                    <label for="remark"><?php __d(sprintf($_domain, 'Details'), "Remark") ?></label>
                                    <?php echo $this->Form->input('remark', array('type' => 'textarea', 'div' => false, 'label' => false)); ?>
                                </div>
                                <?php
                                //added on 2015-04-28 by QN
                                $frees = range(1, 5);
                                foreach($frees as $num):
                                    $check = $this->requestAction('/translations/getSetting', array('pass' => array('Free ' . $num, 'Details')));
                                    if( $check['show'] ):
                                ?>

                                <div class="wd-input wd-area wd-none">
                                    <label for="ProjectFree<?php echo $num ?>"><?php __d(sprintf($_domain, 'Details'), 'Free ' . $num) ?></label>
                                    <?php echo $this->Form->input('free_' . $num, array('type' => 'textarea', 'class' => 'resizeOnFocus', 'div' => false, 'label' => false)); ?>
                                </div>

                                <?php
                                    endif;
                                endforeach;
                                //added on 2015-11-04 by QN, requested from Partecis
                                $range = range(1, 4);
                                //date
                                foreach($range as $num):
                                    $check = $this->requestAction('/translations/getSetting', array('pass' => array('Date ' . $num, 'Details')));
                                    if( $check['show'] ):
                                ?>
                                <div class="wd-input wd-area wd-none">
                                    <label for="ProjectFree<?php echo $num ?>"><?php __d(sprintf($_domain, 'Details'), 'Date ' . $num) ?></label>
                                    <?php echo $this->Form->input('date_' . $num, array('type' => 'text', 'class' => 'wd-date', 'div' => false, 'label' => false, 'value' => $str_utility->convertToVNDate($this->data['Project']['date_' . $num]))); ?>
                                </div>
                                <?php
                                    endif;
                                endforeach;
                                foreach($range as $num):
                                    $check = $this->requestAction('/translations/getSetting', array('pass' => array('List ' . $num, 'Details')));
                                    if( $check['show'] ):
                                ?>
                                <div class="wd-input wd-area wd-none">
                                    <label for="ProjectFree<?php echo $num ?>"><?php __d(sprintf($_domain, 'Details'), 'List ' . $num) ?></label>
                                    <?php echo $this->Form->input('list_' . $num, array('type' => 'select', 'style' => 'margin-right:11px; width:60.5% !important', 'div' => false, 'label' => false, 'options' => $datasets['list_' . $num], 'empty' => __("-- Select -- ", true))); ?>
                                </div>
                                <?php
                                    endif;
                                endforeach;
                                foreach($range as $num):
                                    $check = $this->requestAction('/translations/getSetting', array('pass' => array('Yes/No ' . $num, 'Details')));
                                    if( $check['show'] ):
                                ?>
                                <div class="wd-input wd-area wd-none">
                                    <label for="ProjectFree<?php echo $num ?>"><?php __d(sprintf($_domain, 'Details'), 'Yes/No ' . $num) ?></label>
                                    <?php echo $this->Form->input('yn_' . $num, array('type' => 'select', 'style' => 'margin-right:11px; width:60.5% !important', 'div' => false, 'label' => false, 'options' => array(__('Yes', true), __('No', true)))); ?>
                                </div>
                                <?php
                                    endif;
                                endforeach;
                                foreach($range as $num):
                                    $check = $this->requestAction('/translations/getSetting', array('pass' => array('0/1 ' . $num, 'Details')));
                                    if( $check['show'] ):
                                ?>
                                <div class="wd-input wd-area wd-none">
                                    <label for="ProjectFree<?php echo $num ?>"><?php __d(sprintf($_domain, 'Details'), '0/1 ' . $num) ?></label>
                                    <?php echo $this->Form->input('bool_' . $num, array('type' => 'select', 'style' => 'margin-right:11px; width:60.5% !important', 'div' => false, 'label' => false, 'options' => array(0, 1))); ?>
                                </div>
                                <?php
                                    endif;
                                endforeach;
                                $range = range(1, 6);
                                foreach($range as $num):
                                    $check = $this->requestAction('/translations/getSetting', array('pass' => array('Price ' . $num, 'Details')));
                                    if( $check['show'] ):
                                ?>
                                <div class="wd-input wd-area wd-none">
                                    <label for="ProjectFree<?php echo $num ?>"><?php __d(sprintf($_domain, 'Details'), 'Price ' . $num) ?></label>
                                    <?php echo $this->Form->input('price_' . $num, array('div' => false, 'class' => 'numeric-value', 'label' => false)); ?> <span style="margin-left: 5px; line-height: 29px"><?php echo $budget_settings;?></span>
                                </div>
                                <?php
                                    endif;
                                endforeach;
                                $range = range(1, 8);
                                foreach($range as $num):
                                    $check = $this->requestAction('/translations/getSetting', array('pass' => array('Number ' . $num, 'Details')));
                                    if( $check['show'] ):
                                ?>
                                <div class="wd-input wd-area wd-none">
                                    <label for="ProjectFree<?php echo $num ?>"><?php __d(sprintf($_domain, 'Details'), 'Number ' . $num) ?></label>
                                    <?php echo $this->Form->input('number_' . $num, array('div' => false, 'class' => 'numeric-value', 'label' => false)); ?>
                                </div>
                                <?php
                                    endif;
                                endforeach;
                                ?>
                            </div>

                        </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $this->element('dialog_projects') ?>
<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->script('validateDate'); ?>
<?php echo $html->script('jshashtable-2.1'); ?>
<?php echo $html->script('jquery.numberformatter-1.2.3'); ?>
<?php echo $html->script('jquery.maxlength-min'); ?>
<?php
    echo $validation->bind("Project", array('form' => '#ProjectEditForm'));
    $showAllFieldYourform = 0;
    if(empty($employeeInfo['Role']['name']) || $employeeInfo['Role']['name'] == 'admin' || ($_isProfile && $_canWrite)){
        $showAllFieldYourform = 1;
    } else if($_isProfile && !$_canWrite){
        $showAllFieldYourform = 0;
    } else {
        $showAllFieldYourform = $employeeInfo['Employee']['update_your_form'];
    }
?>
<script type="text/javascript">
var wdTable = $('.wd-panel');
var heightTable = $(window).height() - wdTable.offset().top - 40;
//heightTable = (heightTable < 500) ? 500 : heightTable;
wdTable.css({
    height: heightTable,
});
$(window).resize(function(){
    heightTable = $(window).height() - wdTable.offset().top - 40;
    //heightTable = (heightTable < 500) ? 500 : heightTable;
    wdTable.css({
        height: heightTable,
    });
});
    var codeExisted = <?php echo json_encode(__('Already used in project', true));?>;
    var curretnCate = <?php echo json_encode($this->data['Project']['category']);?>;
    var role = <?php echo json_encode($employeeInfo['Role']['name']);?>;
    var activityId = <?php echo json_encode($this->data['Project']['activity_id']);?>;
    var haveActivity = <?php echo json_encode($haveActivity);?>;
    var changeProjectManager =<?php echo json_encode($changeProjectManager);?>;
    var showAllFieldYourform = <?php echo json_encode($showAllFieldYourform); ?>;
    var change_status_project = <?php echo json_encode(!empty($employeeInfo['Employee']['change_status_project']) ? $employeeInfo['Employee']['change_status_project'] : 0); ?>;

    //-------
    if(showAllFieldYourform == 0){
        $(".wd-input").find("input").prop('disabled', true);
        $(".wd-input").find("select").prop('disabled', true);
        $(".wd-input").find("textarea").prop('disabled', true);
        $(".wd-combobox-2").prop('disabled', true);
        $(".wd-combobox-3").prop('disabled', true);
        $(".wd-combobox-4").prop('disabled', true);
        $(".wd-combobox-5").prop('disabled', true);
        $(".wd-combobox-6").prop('disabled', true);
        $(".wd-combobox-7").prop('disabled', true);
        $('#pseudo-category').prop('disabled', true);
    }
    $(document).ready(function(){
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
                        $("#idButtonSave").show();
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
    /* table .end */
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
                }*/
            }
            else return false;
        }
        var newCate = $('#ProjectCategory').val();
        var program = $('#ProjectProjectAmrProgramId').val();
        var activate_family_linked_program = <?php echo json_encode($activate_family_linked_program);?>;
        if(flag1){
            if((curretnCate == 1 && newCate == 2) || (curretnCate == 2 && newCate == 1)){
                if((role === 'admin') || (role === 'pm' && change_status_project == 1) ){
                    var reFlag = false;
                    var projectName = $('#ProjectProjectName').val();
                    var projectLongName = $('#ProjectLongProjectName').val();
                    $('#ActivityLinkedName').val(projectName);
                    $('#ActivityLinkedNameDetail').val(projectLongName);
                    $('#ActivityLinkedShortName').val(projectName);
                    if(curretnCate == 1 && newCate == 2){ // chuyen In progress to Opportunity
                        if(haveActivity === 'true'){
                            $("#change_category p").html("<?php echo sprintf(__("The following employees have already worked in project <b>%s</b>", true), $this->data['Project']['project_name']); ?>");
                            createDialog();
                            $("#change_category").dialog('option',{title:'Notice'}).dialog('open');
                            return false;
                        } else {
                            $.ajax({
                                type:'POST',
                                url: '<?php echo $html->url(array('action' => 'deleteActivityLinked', $this->data['Project']['activity_id'])); ?>',
                                cache: false,
                                success:function(data){
                                    $('#ProjectTmpActivityId').val(-1);
                                    $('#ProjectEditForm').submit();
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
                        if(activityId){
                            // da co lien ket saved.
                            reFlag = true;
                        } else {
                            createDialog();
                            $("#save_activity_linked").dialog('open');
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
                                    activated: $('#ActivityLinkedActivated').val()
                                };
                                $.ajax({
                                    type:'POST',
                                    url: '<?php echo $html->url(array('action' => 'saveActivityLinked', $project_name['Project']['id'], $this->data['Company']['id'])); ?>',
                                    data:{
                                        data: datas
                                    },
                                    cache: false,
                                    success:function(data){
                                        var id_acti = JSON.parse(data);
                                        $('#ProjectTmpActivityId').val(id_acti);
                                        $('#ProjectActivated').val($('#ActivityLinkedActivated').val());
                                        $('#ProjectEditForm').submit();
                                    }
                                });
                            });
                        }
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
if (($employee_info["Employee"]['is_sas'] == 0 && $employee_info["Role"]["name"] == "conslt")) {
    ?>
                $(".wd-submit").hide();
                return false;
    <?php
}else
    echo "return true";
?>
    }

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

        //$("#ProjectStartDate, #ProjectEndDate, #ProjectPlanedEndDate").datepicker({
        /*$("#ProjectPlanedEndDate").datepicker({
            showOn          : 'button',
            buttonImage     : '<?php echo $html->url("/img/front/calendar.gif") ?>',
            buttonImageOnly : true,
            dateFormat      : 'dd-mm-yy'
        }); */

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
            projectEmployeeManagerDatas = <?php echo !empty($listEmployeeManagers['PM']) ? json_encode($listEmployeeManagers['PM']) : json_encode(array());?>,
            chiefBusinessDatas = <?php echo !empty($listEmployeeManagers['CB']) ? json_encode($listEmployeeManagers['CB']) : json_encode(array());?>,
            technicalManagerDatas = <?php echo !empty($listEmployeeManagers['TM']) ? json_encode($listEmployeeManagers['TM']) : json_encode(array());?>,
            functionalLeaderDatas = <?php echo !empty($listEmployeeManagers['FL']) ? json_encode($listEmployeeManagers['FL']) : json_encode(array());?>,
            uatManagerDatas = <?php echo !empty($listEmployeeManagers['UM']) ? json_encode($listEmployeeManagers['UM']) : json_encode(array());?>,
            readAccessDatas = <?php echo !empty($listEmployeeManagers['RA']) ? json_encode($listEmployeeManagers['RA']) : json_encode(array());?>,
            currentPhaseDatas = <?php echo !empty($phasePlans) ? json_encode($phasePlans) : json_encode(array());?>,
            phaseHaveTasks = <?php echo !empty($phaseHaveTasks) ? json_encode($phaseHaveTasks) : json_encode(array());?>;
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
            if(!changeProjectManager){
                // $('.wd-combobox').css('background-color', 'rgb(223, 223, 223)');
                return false;
            }
            var checked = $(this).attr('checked');
            $('#wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7').css('display', 'none');
            $('.wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7').removeAttr('checked');
            if(showAllFieldYourform != 0){
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
            }
            return false;
        });
        $('.wd-combobox-2').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7').css('display', 'none');
            $('.context-menu-filter, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7').css('display', 'none');
            $('.wd-combobox, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7').removeAttr('checked');
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
                        'width': '26.3%',
                        'z-index': 2
                    });
                    $('#wd-data-project-2 div:first-child').css('padding-top', '20px');
                }
            }
            return false;
        });
        $('.wd-combobox-3').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project-2, #wd-data-project, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7').css('display', 'none');
            $('.wd-combobox-2, .wd-combobox, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7').removeAttr('checked');
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
                        'width': '26.3%',
                        'z-index': 2
                    });
                    $('#wd-data-project-3 div:first-child').css('padding-top', '20px');
                }
            }
            return false;
        });
        $('.wd-combobox-4').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project-2, #wd-data-project, #wd-data-project-3, #wd-data-project-5, #wd-data-project-6, #wd-data-project-7').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter, .context-menu-filter-3, .context-menu-filter-5, .context-menu-filter-6, .context-menu-filter-7').css('display', 'none');
            $('.wd-combobox-2, .wd-combobox, .wd-combobox-3, .wd-combobox-5, .wd-combobox-6, .wd-combobox-7').removeAttr('checked');
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
                        'width': '26.3%',
                        'z-index': 2
                    });
                    $('#wd-data-project-4 div:first-child').css('padding-top', '20px');
                }
            }
            return false;
        });
        $('.wd-combobox-5').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project-2, #wd-data-project, #wd-data-project-3, #wd-data-project-4,  #wd-data-project-6,  #wd-data-project-7').css('display', 'none');
            $('.context-menu-filter-2, .context-menu-filter, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-6, .context-menu-filter-7').css('display', 'none');
            $('.wd-combobox-2, .wd-combobox, .wd-combobox-3, .wd-combobox-4, .wd-combobox-6, .wd-combobox-7').removeAttr('checked');
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
                        'width': '26.3%',
                        'z-index': 2
                    });
                    $('#wd-data-project-5 div:first-child').css('padding-top', '20px');
                }
            }
            return false;
        });
        $('.wd-combobox-6').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-7').css('display', 'none');
            $('.context-menu-filter, .context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-7').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-7').removeAttr('checked');
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
                        'width': '26.3%',
                        'z-index': 2
                    });
                    $('#wd-data-project-6 div:first-child').css('padding-top', '20px');
                }
            }
            return false;
        });
        $('.wd-combobox-7').click(function(){
            var checked = $(this).attr('checked');
            $('#wd-data-project, #wd-data-project-2, #wd-data-project-3, #wd-data-project-4, #wd-data-project-5, #wd-data-project-6').css('display', 'none');
            $('.context-menu-filter, .context-menu-filter-2, .context-menu-filter-3, .context-menu-filter-4, .context-menu-filter-5, .context-menu-filter-6').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2, .wd-combobox-3, .wd-combobox-4, .wd-combobox-5, .wd-combobox-6').removeAttr('checked');
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
                        'width': '26.3%',
                        'z-index': 2
                    });
                    $('#wd-data-project-7 div:first-child').css('padding-top', '20px');
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
            if(!changeProjectManager){
                // $('.wd-combobox').css('background-color', 'rgb(223, 223, 223)');
            }
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
                    if(valListBackup == isBackup){
                        $(backup).find('#ProjectIsBackup').attr('checked', 'checked');
                        $('a.wd-combobox .wd-bk-'+valListBackup).append('(B)');
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
                if($ids.length > 1){
                    for(var i = 0; i < $ids.length; i++){
                        var _bkup = $(backup).find('#ProjectIsBackup').val();
                        if($ids[i] != $ids[0] && $ids[i] == _bkup){
                            $(backup).find('#ProjectIsBackup').attr('checked', 'checked');
                            $('a.wd-combobox .wd-bk-'+_bkup).append('(B)');
                        }
                    }
                }
            });
            /**
             * When click in checkbox BACKUP
             */
            $(backup).find('#ProjectIsBackup').click(function(){
                var _bkup = $(backup).find('#ProjectIsBackup').val();
                if($(this).is(':checked')) {
                    $('a.wd-combobox .wd-bk-'+_bkup).append('(B)');
                } else {
                    $('a.wd-combobox').find('.wd-bk-' +_bkup).remove();
                    $('a.wd-combobox .wd-dt-' +_bkup).append('<span class="wd-bk-'+_bkup+'"></span>');
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
                        $('a.wd-combobox-3').append('<span class="wd-dt-'+valList+'">' + $('#wd-data-project-3').find('.wd-group-'+valList).find('span').html() + '<span class="wd-bk-'+valList+'"></span></span><span class="wd-em-'+valList+'">, </span>');
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
            // console.log(readAccessDatas);
            // console.log(employId);
            // console.log(isProfitCenter);
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
                        //if($.inArray(idPlan, _phaseHaveTasks) != -1){ //co trong mang
//                            $(data).find('#ProjectProjectPhaseId').attr('disabled', 'disabled');
//                            $('#ProjectProjectPhaseIdTmp').val(idPhase + ', ' + $('#ProjectProjectPhaseIdTmp').val());
//                        }
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
                <label for="name"><?php __("Name") ?></label>
                <?php
                echo $this->Form->input('name', array(
                    'div' => false,
                    'label' => false));
                ?>
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
                    )
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
<!-- dialog_vision_portfolio.end -->
