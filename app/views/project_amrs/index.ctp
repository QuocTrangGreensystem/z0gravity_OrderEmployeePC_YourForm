<?php echo $html->script('jshashtable-2.1'); ?>
<?php echo $html->script('jquery.numberformatter-1.2.3'); ?>
<?php echo $html->script('jquery.formatCurrency-1.4.0'); ?>
<?php echo $html->script('jquery.validation.min'); ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->script('jquery.dataTables'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<?php echo $html->script('validateDate'); ?>
<?php echo $html->css('dd'); ?>
<?php echo $html->script('jquery.dd'); ?>
<?php echo $html->css('gantt'); ?>
<style>
fieldset div textarea{width:85%;}.error-message{color:red;margin-left:35px;}.wd-weather-list ul li{padding-right:5px;}#GanttChartDIV{width:100%;}.gantt-chart-wrapper{overflow-x:auto;}.gantt-chart{margin-left:0!important;}#gantt-display div label{padding:0 6px;}#gantt-display{width:620px;}.inputcheckbox{float:left;width:50px;margin-left:-20px;margin-top:1px;}.checkbox{float:left;}  .export-excel-icon-all{
             background: url("<?php echo $this->Html->webroot('img/export.jpg'); ?>") no-repeat;
            display: block;
            width: 32px;
            float: right;
            margin-left: 8px;
            padding-bottom: 16px;
    }
    .export-excel-icon-all:hover{
         background: url("<?php echo $this->Html->webroot('img/export_hover.jpg'); ?>") no-repeat;
        display: block;
        width: 32px;
        float: right;
        margin-left: 8px;
        padding-bottom: 16px;
    }
    .export-excel-icon-all span{
        text-indent: -9999px;
        display: block;
    }
    #wd-container-footer{
        display: none;
    }
</style>
<?php
$arg = $this->passedArgs;
$arg["?"] = $this->params['url'];
unset($arg['?']['url'], $arg['?']['ext']);
$type = 'monthyear';
$md = !empty($employee_info['Company']['unit']) ? $employee_info['Company']['unit'] : 'M.D';
?>
<div id="wd-container-main" class="wd-project-detail">
    <div class="wd-layout">

        <div class="wd-main-content">
            <div class="wd-title">
                <fieldset style="float: left">
                    <div class="wd-submit" style="overflow: hidden;margin: 0;">
                        <input value="" id="btnSave"  class="wd-save"/>
                        <a href="" class="wd-reset"><?php __('Reset') ?></a>
                    </div>
                </fieldset>
                <a href="<?php echo $html->url("/project_amrs/exportExcel/" . $projectName['Project']['id']) ?>" class="export-excel-icon-all" style="margin-right:5px; " title="<?php __('Export Excel')?>"><span><?php __('Export Excel') ?></span></a>
                <a href="<?php echo $html->url("/project_phase_plans/phase_vision/" . $projectName['Project']['id']) ?>" class="wd-add-project" style="margin-right:5px; "><span><?php __('Gantt+') ?></span></a>
                <div style="float: left; margin-top: 23px; width: 300px; margin-left: -23px;">

                    <div id="gantt-display">
                       <label class="title" style="float: left; padding-right: 10px;"><?php __('Display initial time'); ?> </label>
                        <?php
                            $displayplan = 1;
                            $chk = ($displayplan == 1) ? true : false;
                            $chkreal = ($display == 1) ? true : false;
                            echo $this->Form->input('displayplan', array(
                                'rel' => 'no-history',
                                'onchange' => 'removeLine(this,"n");',
                                'value' => $displayplan,
                                'label' => '',
                                'class' => 'inputcheckbox',
                                'type' => 'checkbox', 'legend' => false, 'fieldset' => false, 'checked' => $chk
                            ));?>
                            <label class="title" style="float: left; padding-right: 10px;"><?php __('Display real time'); ?> </label>
                            <?php
                            echo $this->Form->input('displayreal', array(
                                'rel' => 'no-history',
                                'onchange' => 'removeLine(this,"s");',
                                'value' => $display,
                                'label' => '',
                                'class' => 'inputcheckbox',
                                'type' => 'checkbox', 'legend' => false, 'fieldset' => false ,'checked' => $chkreal
                            ));
                        ?>
                    </div>
                    <?php echo $this->Form->end(); ?>
                </div>
            </div>
            <div class="wd-tab">
                <?php //echo $this->element('project_tab') ?>
                <div class="wd-panel">
                    <?php echo $this->Session->flash(); ?>
                    <div class="wd-section" id="wd-fragment-1">
                        <h2 class="wd-t2"><?php echo sprintf(__("%s", true), $projectName['Project']['project_name']) ?></h2>
                        <?php
                        echo $this->Form->create('ProjectAmr', array('url' => array(
                                'controller' => 'project_amrs', 'action' => 'index', $projectName['Project']['id']
                                )));
                        echo $this->Form->input('project_id', array('type' => 'hidden', 'name' => 'data[ProjectAmr][project_id]', 'value' => $project_id));
                        echo $this->Form->input('id', array('type' => 'hidden', 'name' => 'data[ProjectAmr][id]', 'value' => (@$this->data['ProjectAmr']['id']) ? $this->data['ProjectAmr']['id'] : ""));
                        App::import("vendor", "str_utility");
                        $str_utility = new str_utility();
                        ?>
                        <!-- Gantt -->
                        <div id="GanttChartDIV">
                            <?php
                            $rows = 0;
                            $start = $end = 0;
                            $data = $projectId = $conditions = array();
                            foreach ($projects as $project) {
                                $_data = array(
                                    'name' => $project['Project']['project_name'],
                                    'phase' => array(),
                                );
                                $projectId[$project['Project']['id']] = $project['Project']['project_name'];
                                if (!empty($project['ProjectPhasePlan'])) {
                                    foreach ($project['ProjectPhasePlan'] as $phace) {
                                        $_phase = array(
                                            'name' => !empty($phace['ProjectPhase']['name']) ? $phace['ProjectPhase']['name'] : '',
                                            'start' => $this->Gantt->toTime($phace['phase_planed_start_date']),
                                            'end' => $this->Gantt->toTime($phace['phase_planed_end_date']),
                                            'rstart' => $this->Gantt->toTime($phace['phase_real_start_date']),
                                            'rend' => $this->Gantt->toTime($phace['phase_real_end_date']),
                                            'color' => !empty($phace['ProjectPhase']['color']) ? $phace['ProjectPhase']['color'] : '#004380'
                                        );
                                        if ($_phase['rstart'] > 0) {
                                            $_start = min($_phase['start'], $_phase['rstart']);
                                        } else {
                                            $_start = $_phase['start'];
                                        }
                                        if (!$start || ($_start > 0 && $_start < $start)) {
                                            $start = $_start;
                                        }
                                        $_end = max($_phase['end'], $_phase['rend']);
                                        if (!$end || $_end > $end) {
                                            $end = $_end;
                                        }
                                        $_data['phase'][] = $_phase;
                                    }
                                }
                                $data[] = $_data;
                            }

                            //pr(date('Y-m-d',$start));
                            //pr(date('Y-m-d',$end));
                            //pr($data);
                            //exit();

                            unset($projects, $project, $_data, $_phase, $phase);
                            $summary = isset($this->params['url']['summary']) ? (bool) $this->params['url']['summary'] : false;
                            $showType = isset($this->params['url']['type']) ? (int) $this->params['url']['type'] : 0;


                            if (empty($start) || empty($end)) {
                                echo $this->Html->tag('h1', __('No data exist to create Gantt chart', true), array('style' => 'color:red'));
                            } else {
                                $this->Gantt->create($type, $start, $end, array(), false , false);

                                foreach ($data as $value) {
                                    $rows++;
                                    if (empty($value['phase'])) {
                                        $this->Gantt->drawLine(__('no data exit', true), 0, 0, 0, 0, '#ffffff', true);
                                    } else {
                                        foreach ($value['phase'] as $node) {
                                            $color = '#004380';
                                            if (!empty($node['color'])) {
                                                $color = $node['color'];
                                            }
                                            $this->Gantt->drawLine($node['name'], $node['start'], $node['end'], $node['rstart'], $node['rend'], $color, true);
                                        }
                                    }
                                    $this->Gantt->drawEnd($value['name'], false);
                                }
                                $this->Gantt->end();
                            }
                            ?>
                        </div>

                        <!-- Gantt.end -->
                        <fieldset>
                            <div class="wd-scroll-form" style="height: auto;">
                                <div class="wd-input wd-weather-list">
                                    <ul style="float: left; display: inline;width:500px; ">
                                        <li style="width: 50px;padding-top: 10px;"><?php __('Weather'); ?></li>
                                        <li><input checked="true" style="width: 25px; margin-top: 8px;" <?php echo @$this->data["ProjectAmr"]["weather"] == 'sun' ? 'checked' : 'checked'; ?> value="sun" name="data[ProjectAmr][weather][]" type="radio" /> <img title="Sun"  src="<?php echo $html->url('/img/sun.png') ?>"  /></li>
                                        <li><input type="radio" <?php echo @$this->data["ProjectAmr"]["weather"] == 'cloud' ? 'checked' : ''; ?> value="cloud" name="data[ProjectAmr][weather][]" style="width: 25px;margin-top: 8px;" /> <img title="Cloud" src="<?php echo $html->url('/img/cloud.png') ?>"  /></li>
                                        <li><input type="radio" <?php echo @$this->data["ProjectAmr"]["weather"] == 'rain' ? 'checked' : ''; ?> value="rain" name="data[ProjectAmr][weather][]" style="width: 25px;margin-top: 8px;"   /> <img title="Rain"  src="<?php echo $html->url('/img/rain.png') ?>"  /></li>
                                    </ul>
                                    <?php //echo $this->Form->radio('weather', array('div'=>false, 'label'=>false));?>
                                </div>
                                <div class="wd-input">
                                    <label><?php __("General Comment") ?></label>
                                    <?php
                                    echo $this->Form->input('project_amr_solution', array('type' => 'textarea', 'div' => false,
                                        'name' => 'data[ProjectAmr][project_amr_solution]',
                                        'label' => false,
                                        'value' => (!empty($this->data['ProjectAmr']['project_amr_solution'])) ? $this->data['ProjectAmr']['project_amr_solution'] : ""
                                    ));
                                    ?>
                                </div>

                                <div class="wd-left-content">
                                    <div class="wd-input">
                                        <label for="program"><?php __("Program") ?></label>
                                        <?php
                                        echo $this->Form->input('project_amr_program_id', array('div' => false, 'label' => false,
                                            'name' => 'data[ProjectAmr][project_amr_program_id]',
                                            'value' => (!empty($this->data['ProjectAmr']['project_amr_program_id'])) ? $this->data['ProjectAmr']['project_amr_program_id'] : "",
                                            "empty" => __("-- Select Program-- ", true),
                                        ));
                                        ?>
                                    </div>

                                    <div class="wd-input">
                                        <label for="category"><?php __("Category") ?></label>
                                        <?php
                                        echo $this->Form->input('project_amr_category_id', array('div' => false, 'label' => false,
                                            'name' => 'data[ProjectAmr][project_amr_category_id]',
                                            'value' => (!empty($this->data['ProjectAmr']['project_amr_category_id'])) ? $this->data['ProjectAmr']['project_amr_category_id'] : "",
                                            "empty" => __("-- Select Category --", true),
                                        ));
                                        ?>
                                    </div>

                                    <div class="wd-input">
                                        <?php /* <label for="project-manager"><?php __("Project Manager") ?></label>
                                          <?php
                                          /*echo $this->Form->input('project_amr_manager_id', array('div' => false, 'label' => false,
                                          'name' => 'data[ProjectAmr][project_manager_id]',
                                          'value' => (!empty($this->data['ProjectAmr']['project_manager_id'])) ? $this->data['ProjectAmr']['project_manager_id'] : "",
                                          "empty" => __("-- Select Project Manager --", true),
                                          "options" => $projectManagers
                                          )); */
                                        ?>
                                    </div>
                                    <!--<div class="wd-input">
                                                                        <label for="status"><?php //__("Status")                 ?></label>
                                    <?php /* echo $this->Form->input('project_amr_status_id', array('div'=>false, 'label'=>false,
                                      'name'=>'data[ProjectAmr][project_amr_status_id]',
                                      'value'=>(!empty($this->data['ProjectAmr']['project_amr_status_id']))?$this->data['ProjectAmr']['project_amr_status_id']:"",
                                      "empty"=>__("-- Select Status --", true),
                                      )); */ ?>
                                                                </div>-->

                                    <div class="wd-input wd-input-80">
                                        <label for="progression"><?php __("Progression") ?></label>
                                        <?php
                                        echo $this->Form->input('project_amr_progression', array('div' => false, 'label' => false,
                                            'name' => 'data[ProjectAmr][project_amr_progression]',
                                            'disabled' => 'disabled',
                                            //'value'=>(!empty($this->data['ProjectAmr']))?(strpos($this->data['ProjectAmr']['project_amr_progression'], "%")?$this->data['ProjectAmr']['project_amr_progression']:($this->data['ProjectAmr']['project_amr_progression'] + 0)."%"):"0%"
                                            'value' => (!empty($progression)) ? $progression : "",
                                        ));
                                        ?>
                                        <label style="text-align:left; padding: 0px !important;"> <?php echo "%" ?></label>
                                    </div>

                                    <div class="wd-input wd-input-80">
                                        <label for="progression"><?php __("% Assigned to profit center") ?></label>
                                        <?php
                                        echo $this->Form->input('assign_to_pc', array('div' => false, 'label' => false,
                                            //'name' => 'data[ProjectAmr][project_amr_progression]',
                                            'disabled' => 'disabled',
                                            //'value'=>(!empty($this->data['ProjectAmr']))?(strpos($this->data['ProjectAmr']['project_amr_progression'], "%")?$this->data['ProjectAmr']['project_amr_progression']:($this->data['ProjectAmr']['project_amr_progression'] + 0)."%"):"0%"
                                            'value' => (!empty($assginProfitCenter)) ? $assginProfitCenter : "",
                                        ));
                                        ?>
                                        <label style="text-align:left; padding: 0px !important;"> <?php echo "%" ?></label>
                                    </div>

                                    <div class="wd-input wd-input-80">
                                        <label for="progression"><?php __("% Assigned to employee") ?></label>
                                        <?php
                                        echo $this->Form->input('assign_to_employee', array('div' => false, 'label' => false,
                                            //'name' => 'data[ProjectAmr][project_amr_progression]',
                                            'disabled' => 'disabled',
                                            //'value'=>(!empty($this->data['ProjectAmr']))?(strpos($this->data['ProjectAmr']['project_amr_progression'], "%")?$this->data['ProjectAmr']['project_amr_progression']:($this->data['ProjectAmr']['project_amr_progression'] + 0)."%"):"0%"
                                            'value' => (!empty($assgnEmployee)) ? $assgnEmployee : "",
                                        ));
                                        ?>
                                        <label style="text-align:left; padding: 0px !important;"> <?php echo "%" ?></label>
                                    </div>

                                    <div class="wd-input wd-input-90">
                                        <label for="cost-control"><?php __("Cost Control") ?></label>
                                        <?php
                                        echo $this->Form->input('project_amr_cost_control_id', array('div' => false, 'label' => false,
                                            'name' => 'data[ProjectAmr][project_amr_cost_control_id]',
                                            'value' => (!empty($this->data['ProjectAmr']['project_amr_cost_control_id'])) ? $this->data['ProjectAmr']['project_amr_cost_control_id'] : "",
                                            "empty" => __("-- Select Cost Control --", true),
                                        ));
                                        ?>
                                        <div style="float: left; line-height: -40px; width:30%">
                                            <div class="wd-input wd-weather-list-dd">
                                                <ul style="float: left; display: inline;">
                                                    <li><input checked="true" style="width: 25px; margin-top: 8px;" <?php echo @$this->data["ProjectAmr"]["cost_control_weather"] == 'sun' ? 'checked' : 'checked'; ?> value="sun" name="data[ProjectAmr][cost_control_weather]" type="radio" /> <img title="Sun"  src="<?php echo $html->url('/img/sun.png') ?>"  /></li>
                                                    <li><input type="radio" <?php echo @$this->data["ProjectAmr"]["cost_control_weather"] == 'cloud' ? 'checked' : ''; ?> value="cloud" name="data[ProjectAmr][cost_control_weather]" style="width: 25px;margin-top: 8px;" /> <img title="Cloud" src="<?php echo $html->url('/img/cloud.png') ?>"  /></li>
                                                    <li><input type="radio" <?php echo @$this->data["ProjectAmr"]["cost_control_weather"] == 'rain' ? 'checked' : ''; ?> value="rain" name="data[ProjectAmr][cost_control_weather]" style="width: 25px;margin-top: 8px;"   /> <img title="Rain"  src="<?php echo $html->url('/img/rain.png') ?>"  /></li>
                                                </ul>
                                                <?php //echo $this->Form->radio('weather', array('div'=>false, 'label'=>false));?>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="wd-input wd-input-90">
                                        <label for="project-plan"><?php __("Planning") ?></label>
                                        <?php
                                        echo $this->Form->input('project_amr_plan_id', array('div' => false, 'label' => false,
                                            'name' => 'data[ProjectAmr][project_amr_plan_id]',
                                            'value' => (!empty($this->data['ProjectAmr']['project_amr_plan_id'])) ? $this->data['ProjectAmr']['project_amr_plan_id'] : "",
                                            "empty" => __("-- Select Plan --", true),
                                        ));
                                        ?>
                                        <div style="float: left; line-height: -40px; width:30%">
                                            <div class="wd-input wd-weather-list-dd">
                                                <ul style="float: left; display: inline;">
                                                    <li><input checked="true" style="width: 25px; margin-top: 8px;" <?php echo @$this->data["ProjectAmr"]["planning_weather"] == 'sun' ? 'checked' : 'checked'; ?> value="sun" name="data[ProjectAmr][planning_weather]" type="radio" /> <img title="Sun"  src="<?php echo $html->url('/img/sun.png') ?>"  /></li>
                                                    <li><input type="radio" <?php echo @$this->data["ProjectAmr"]["planning_weather"] == 'cloud' ? 'checked' : ''; ?> value="cloud" name="data[ProjectAmr][planning_weather]" style="width: 25px;margin-top: 8px;" /> <img title="Cloud" src="<?php echo $html->url('/img/cloud.png') ?>"  /></li>
                                                    <li><input type="radio" <?php echo @$this->data["ProjectAmr"]["planning_weather"] == 'rain' ? 'checked' : ''; ?> value="rain" name="data[ProjectAmr][planning_weather]" style="width: 25px;margin-top: 8px;"   /> <img title="Rain"  src="<?php echo $html->url('/img/rain.png') ?>"  /></li>
                                                </ul>
                                                <?php //echo $this->Form->radio('weather', array('div'=>false, 'label'=>false)); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="wd-input wd-input-90">
                                        <label for="risk-control"><?php __("Risk Control") ?></label>
                                        <?php
                                        echo $this->Form->input('project_amr_risk_control_id', array('div' => false, 'label' => false,
                                            'name' => 'data[ProjectAmr][project_amr_risk_control_id]',
                                            'value' => (!empty($this->data['ProjectAmr']['project_amr_risk_control_id'])) ? $this->data['ProjectAmr']['project_amr_risk_control_id'] : "",
                                            "empty" => __("-- Select Risk Control --", true),
                                        ));
                                        ?>

                                        <div style="float: left; line-height: -40px; width:30%">
                                            <div class="wd-input wd-weather-list-dd">
                                                <ul style="float: left; display: inline;">
                                                    <li><input checked="true" style="width: 25px; margin-top: 8px;" <?php echo @$this->data["ProjectAmr"]["risk_control_weather"] == 'sun' ? 'checked' : 'checked'; ?> value="sun" name="data[ProjectAmr][risk_control_weather]" type="radio" /> <img title="Sun"  src="<?php echo $html->url('/img/sun.png') ?>"  /></li>
                                                    <li><input type="radio" <?php echo @$this->data["ProjectAmr"]["risk_control_weather"] == 'cloud' ? 'checked' : ''; ?> value="cloud" name="data[ProjectAmr][risk_control_weather]" style="width: 25px;margin-top: 8px;" /> <img title="Cloud" src="<?php echo $html->url('/img/cloud.png') ?>"  /></li>
                                                    <li><input type="radio" <?php echo @$this->data["ProjectAmr"]["risk_control_weather"] == 'rain' ? 'checked' : ''; ?> value="rain" name="data[ProjectAmr][risk_control_weather]" style="width: 25px;margin-top: 8px;"   /> <img title="Rain"  src="<?php echo $html->url('/img/rain.png') ?>"  /></li>
                                                </ul>
                                                <?php //echo $this->Form->radio('weather', array('div'=>false, 'label'=>false)); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="wd-input wd-input-90">
                                        <label for="organization"><?php __("Organization") ?></label>
                                        <?php
                                        echo $this->Form->input('project_amr_organization_id', array('div' => false, 'label' => false,
                                            'name' => 'data[ProjectAmr][project_amr_organization_id]',
                                            'value' => (!empty($this->data['ProjectAmr']['project_amr_organization_id'])) ? $this->data['ProjectAmr']['project_amr_organization_id'] : "",
                                            "empty" => __("-- Select Organization --", true),
                                        ));
                                        ?>
                                        <div style="float: left; line-height: -40px; width:30%">
                                            <div class="wd-input wd-weather-list-dd">
                                                <ul style="float: left; display: inline;">
                                                    <li><input checked="true" style="width: 25px; margin-top: 8px;" <?php echo @$this->data["ProjectAmr"]["organization_weather"] == 'sun' ? 'checked' : 'checked'; ?> value="sun" name="data[ProjectAmr][organization_weather]" type="radio" /> <img title="Sun"  src="<?php echo $html->url('/img/sun.png') ?>"  /></li>
                                                    <li><input type="radio" <?php echo @$this->data["ProjectAmr"]["organization_weather"] == 'cloud' ? 'checked' : ''; ?> value="cloud" name="data[ProjectAmr][organization_weather]" style="width: 25px;margin-top: 8px;" /> <img title="Cloud" src="<?php echo $html->url('/img/cloud.png') ?>"  /></li>
                                                    <li><input type="radio" <?php echo @$this->data["ProjectAmr"]["organization_weather"] == 'rain' ? 'checked' : ''; ?> value="rain" name="data[ProjectAmr][organization_weather]" style="width: 25px;margin-top: 8px;"   /> <img title="Rain"  src="<?php echo $html->url('/img/rain.png') ?>"  /></li>
                                                </ul>
                                                <?php //echo $this->Form->radio('weather', array('div'=>false, 'label'=>false)); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="wd-input wd-input-90">
                                        <label for="perimeter"><?php __("Perimeter") ?></label>
                                        <?php
                                        echo $this->Form->input('project_amr_perimeter_id', array('div' => false, 'label' => false,
                                            'name' => 'data[ProjectAmr][project_amr_perimeter_id]',
                                            'value' => (!empty($this->data['ProjectAmr']['project_amr_perimeter_id'])) ? $this->data['ProjectAmr']['project_amr_perimeter_id'] : "",
                                            "empty" => __("-- Select Perimeter --", true),
                                        ));
                                        ?>
                                        <div style="float: left; line-height: -40px; width:30%">
                                            <div class="wd-input wd-weather-list-dd">
                                                <ul style="float: left; display: inline;">
                                                    <li><input checked="true" style="width: 25px; margin-top: 8px;" <?php echo @$this->data["ProjectAmr"]["perimeter_weather"] == 'sun' ? 'checked' : 'checked'; ?> value="sun" name="data[ProjectAmr][perimeter_weather]" type="radio" /> <img title="Sun"  src="<?php echo $html->url('/img/sun.png') ?>"  /></li>
                                                    <li><input type="radio" <?php echo @$this->data["ProjectAmr"]["perimeter_weather"] == 'cloud' ? 'checked' : ''; ?> value="cloud" name="data[ProjectAmr][perimeter_weather]" style="width: 25px;margin-top: 8px;" /> <img title="Cloud" src="<?php echo $html->url('/img/cloud.png') ?>"  /></li>
                                                    <li><input type="radio" <?php echo @$this->data["ProjectAmr"]["perimeter_weather"] == 'rain' ? 'checked' : ''; ?> value="rain" name="data[ProjectAmr][perimeter_weather]" style="width: 25px;margin-top: 8px;"   /> <img title="Rain"  src="<?php echo $html->url('/img/rain.png') ?>"  /></li>
                                                </ul>
                                                <?php //echo $this->Form->radio('weather', array('div'=>false, 'label'=>false)); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="wd-input wd-input-90">
                                        <label for="problem-control"><?php __("Issue Control") ?></label>
                                        <?php
                                        echo $this->Form->input('project_amr_problem_control_id', array('div' => false, 'label' => false,
                                            'name' => 'data[ProjectAmr][project_amr_problem_control_id]',
                                            'value' => (!empty($this->data['ProjectAmr']['project_amr_problem_control_id'])) ? $this->data['ProjectAmr']['project_amr_problem_control_id'] : "",
                                            "empty" => __("-- Select Issue Control --", true),
                                        ));
                                        ?>
                                        <div style="float: left; line-height: -40px; width:30%">
                                            <div class="wd-input wd-weather-list-dd">
                                                <ul style="float: left; display: inline;">
                                                    <li><input checked="true" style="width: 25px; margin-top: 8px;" <?php echo @$this->data["ProjectAmr"]["issue_control_weather"] == 'sun' ? 'checked' : 'checked'; ?> value="sun" name="data[ProjectAmr][issue_control_weather]" type="radio" /> <img title="Sun"  src="<?php echo $html->url('/img/sun.png') ?>"  /></li>
                                                    <li><input type="radio" <?php echo @$this->data["ProjectAmr"]["issue_control_weather"] == 'cloud' ? 'checked' : ''; ?> value="cloud" name="data[ProjectAmr][issue_control_weather]" style="width: 25px;margin-top: 8px;" /> <img title="Cloud" src="<?php echo $html->url('/img/cloud.png') ?>"  /></li>
                                                    <li><input type="radio" <?php echo @$this->data["ProjectAmr"]["issue_control_weather"] == 'rain' ? 'checked' : ''; ?> value="rain" name="data[ProjectAmr][issue_control_weather]" style="width: 25px;margin-top: 8px;"   /> <img title="Rain"  src="<?php echo $html->url('/img/rain.png') ?>"  /></li>
                                                </ul>
                                                <?php //echo $this->Form->radio('weather', array('div'=>false, 'label'=>false)); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="wd-right-content">
                                    <div class="wd-input">
                                        <label for="sub-program"><?php __("Sub Program") ?></label>
                                        <?php
                                        echo $this->Form->input('project_amr_sub_program_id', array('div' => false, 'label' => false,
                                            'name' => 'data[ProjectAmr][project_amr_sub_program_id]',
                                            'value' => (!empty($this->data['ProjectAmr']['project_amr_sub_program_id'])) ? $this->data['ProjectAmr']['project_amr_sub_program_id'] : "",
                                            "empty" => __("-- Select Sub Program --", true),
                                        ));
                                        ?>
                                    </div>

                                    <div class="wd-input">
                                        <label for="sub-category"><?php __("Sub Category") ?></label>
                                        <?php
                                        echo $this->Form->input('project_amr_sub_category_id', array('div' => false, 'label' => false,
                                            'name' => 'data[ProjectAmr][project_amr_sub_category_id]',
                                            'value' => (!empty($this->data['ProjectAmr']['project_amr_sub_category_id'])) ? $this->data['ProjectAmr']['project_amr_sub_category_id'] : "",
                                            "empty" => __("-- Select Sub Category --", true),
                                        ));
                                        ?>
                                    </div>
                                    <div class="wd-input wd-input-80" style="height: 19px !important; margin-bottom: 0px !important;">
                                        <label for="budget" style="padding-left: 14% !important;"><?php __("CAPEX") ?></label>
                                        <label for="budget" style="padding-left: 9% !important;"><?php __($md, true) ?></label>
                                    </div>
                                    <div class="wd-input wd-input-80">
                                        <label for="budget"><?php __("Budget") ?></label>
                                        <?php
                                        echo $this->Form->input('validated', array('div' => false, 'label' => false, 'style' => 'width:20% !important',
                                            'name' => 'data[ProjectAmr][validated]',
                                            'value' => (!empty($this->data['ProjectAmr']['validated'])) ? $this->data['ProjectAmr']['validated'] : "0"
                                        ));
                                        ?>


                                        <?php
                                        echo $this->Form->input('validated_currency_id', array('div' => false, 'label' => false, 'style' => 'width:100px; margin-right:10px',
                                            'name' => 'data[ProjectAmr][validated_currency_id]',
                                            'value' => (!empty($this->data['ProjectAmr']['validated_currency_id'])) ? $this->data['ProjectAmr']['validated_currency_id'] : "",
                                            "empty" => __("-- Currency --", true),
                                            "options" => $currency_name
                                        ));
                                        ?>
                                        <?php
                                        echo $this->Form->input('md_validated', array('div' => false, 'label' => false, 'style' => 'width:13% !important',
                                            'name' => 'data[ProjectAmr][md_validated]',
                                            'disabled' => 'disabled',
                                            'value' => (!empty($validated)) ? $validated : "0"
                                        ));
                                        ?>
                                    </div>

                                    <div class="wd-input wd-input-80">
                                        <label for="budget"><?php __("Engaged/Consumed") ?></label>
                                        <?php
                                        echo $this->Form->input('engaged', array('div' => false, 'label' => false, 'style' => 'width:20% !important',
                                            'name' => 'data[ProjectAmr][engaged]',
                                            'value' => (!empty($this->data['ProjectAmr']['engaged'])) ? $this->data['ProjectAmr']['engaged'] : "0"
                                        ));
                                        ?>


                                        <?php
                                        echo $this->Form->input('engaged_currency_id', array('div' => false, 'label' => false, 'style' => 'width:100px; margin-right:10px',
                                            'name' => 'data[ProjectAmr][engaged_currency_id]',
                                            'value' => (!empty($this->data['ProjectAmr']['engaged_currency_id'])) ? $this->data['ProjectAmr']['engaged_currency_id'] : "",
                                            "empty" => __("-- Currency --", true),
                                            "options" => $currency_name
                                        ));
                                        ?>
                                        <?php
                                        echo $this->Form->input('md_engaged', array('div' => false, 'label' => false, 'style' => 'width:13% !important',
                                            'name' => 'data[ProjectAmr][md_engaged]',
                                            'disabled' => 'disabled',
                                            'value' => (!empty($engaged)) ? $engaged : "0"
                                        ));
                                        ?>
                                    </div>

                                    <div class="wd-input wd-input-80">
                                        <label for="budget"><?php __("Remain") ?></label>
                                        <?php
                                        echo $this->Form->input('forecasted', array('div' => false, 'label' => false, 'style' => 'width:20% !important',
                                            'name' => 'data[ProjectAmr][forecasted]',
                                            'value' => (!empty($this->data['ProjectAmr']['forecasted'])) ? $this->data['ProjectAmr']['forecasted'] : "0"
                                        ));
                                        ?>


                                        <?php
                                        echo $this->Form->input('forecasted_currency_id', array('div' => false, 'label' => false, 'style' => 'width:100px; margin-right:10px',
                                            'name' => 'data[ProjectAmr][forecasted_currency_id]',
                                            'value' => (!empty($this->data['ProjectAmr']['forecasted_currency_id'])) ? $this->data['ProjectAmr']['forecasted_currency_id'] : "",
                                            "empty" => __("-- Currency --", true),
                                            "options" => $currency_name
                                        ));
                                        ?>
                                        <?php
                                        echo $this->Form->input('md_forecasted', array('div' => false, 'label' => false, 'style' => 'width:13% !important',
                                            'name' => 'data[ProjectAmr][md_forecasted]',
                                            'disabled' => 'disabled',
                                            'value' => (!empty($remain)) ? $remain : "0"
                                        ));
                                        ?>
                                    </div>

                                    <div class="wd-input wd-input-80">
                                        <label for="budget"><?php __("Variance") ?></label>
                                        <?php
                                        echo $this->Form->input('variance', array('div' => false, 'label' => false, 'readonly' => true, 'style' => 'width:20% !important',
                                            'name' => 'data[ProjectAmr][variance]',
                                            'value' => (!empty($this->data['ProjectAmr']['variance'])) ? $this->data['ProjectAmr']['variance'] : "0"
                                        ));
                                        ?>


                                        <?php
                                        echo $this->Form->input('variance_currency_id', array('div' => false, 'label' => false, 'style' => 'width:100px; margin-right:10px',
                                            'name' => 'data[ProjectAmr][variance_currency_id]',
                                            'value' => (!empty($this->data['ProjectAmr']['variance_currency_id'])) ? $this->data['ProjectAmr']['variance_currency_id'] : "",
                                            "empty" => __("-- Currency --", true),
                                            "options" => $currency_name
                                        ));
                                        ?>
                                        <?php
                                        echo $this->Form->input('md_variance', array('div' => false, 'label' => false, 'readonly' => true, 'style' => 'width:13% !important',
                                            'name' => 'data[ProjectAmr][md_variance]',
                                            'value' => (!empty($variance)) ? $variance : "0"
                                        ));
                                        ?>

                                    </div>

                                    <div class="wd-input wd-calendar">
                                        <label for="startdate"><?php __("End Date") ?></label>
                                        <?php
                                        echo $this->Form->input('project_amr_mep_date', array('div' => false,
                                            'label' => false,
                                            'maxlength' => '10',
                                            //'value'=> (!empty($this->data["ProjectAmr"]["project_amr_mep_date"]))?$str_utility->convertToVNDate($this->data["ProjectAmr"]["project_amr_mep_date"]):"",
                                            'type' => 'text',
                                            'name' => 'data[ProjectAmr][project_amr_mep_date]',
                                            'value' => (!empty($endDateAllTask)) ? date('d-m-Y', $endDateAllTask) : "",
                                            'disabled' => 'disabled',
                                            "class" => "placeholder", "placeholder" => __("(dd-mm-yyyy)", true)
                                        ));
                                        ?>
                                    </div>

                                    <div class="wd-input">
                                        <label for="current-phase"><?php __("Current Phase") ?></label>
                                        <?php
                                        echo $this->Form->input('project_amr_phase_id', array('div' => false, 'label' => false,
                                            'name' => 'data[ProjectAmr][project_phases_id]',
                                            'value' => (!empty($this->data['ProjectAmr']['project_phases_id'])) ? $this->data['ProjectAmr']['project_phases_id'] : "",
                                            "empty" => __("-- Select Phase --", true),
                                            "options" => $ProjectPhases
                                        ));
                                        ?>
                                    </div>
                                </div>

                                <div class="wd-input">
                                    <label><?php __("Risk Information") ?></label>
                                    <?php
                                    echo $this->Form->input('project_amr_risk_information', array('type' => 'textarea',
                                        'name' => 'data[ProjectAmr][project_amr_risk_information]',
                                        'div' => false, 'label' => false,
                                        'value' => (!empty($this->data['ProjectAmr']['project_amr_risk_information'])) ? $this->data['ProjectAmr']['project_amr_risk_information'] : ""
                                    ));
                                    ?>
                                </div>

                                <div class="wd-input">
                                    <label><?php __("Issue Information") ?></label>
                                    <?php
                                    echo $this->Form->input('project_amr_problem_information', array('type' => 'textarea',
                                        'name' => 'data[ProjectAmr][project_amr_problem_information]',
                                        'div' => false, 'label' => false,
                                        'value' => (!empty($this->data['ProjectAmr']['project_amr_problem_information'])) ? $this->data['ProjectAmr']['project_amr_problem_information'] : ""
                                    ));
                                    ?>
                                </div>

                                <div class="wd-input">
                                    <label><?php __("Solution Description") ?></label>
                                    <?php
                                    echo $this->Form->input('project_amr_solution_description', array('type' => 'textarea', 'div' => false,
                                        'name' => 'data[ProjectAmr][project_amr_solution_description]',
                                        'label' => false,
                                        'value' => (!empty($this->data['ProjectAmr']['project_amr_solution_description'])) ? $this->data['ProjectAmr']['project_amr_solution_description'] : ""
                                    ));
                                    ?>
                                </div>

                            </div>
                        </fieldset>
                        <?php echo $this->Form->end(); ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $this->element('dialog_projects') ?>
<?php echo $validation->bind("ProjectAmr"); ?>
<?php echo $html->script('jquery.ba-bbq.min'); ?>
<?php echo $html->script('jquery.multiSelect'); ?>

<style type="text/css">
    .setvalidation{
        border-color: red !important;
    }
</style>

<script language="javascript">
  $(function(){
        $('.gantt-line-s').hide();
    })
    function removeLine(checkboxObject,type){
        if(checkboxObject.checked){
            if(type=="n"){
                $('.gantt-line-n').show();
                $('.gantt-line-desc').show();
            };
            if(type=="s"){
                 $('.gantt-line-s').show();
                 $('.gantt-line-desc').show();
            };
        }else{
            if(type=="n"){
                if(!$('#displayreal').attr("checked"))
                    $('.gantt-line-desc').hide();
                $('.gantt-line-n').hide();
            }
            if(type=="s"){
                if(!$('#displayplan').attr("checked"))
                    $('.gantt-line-desc').hide();
                $('.gantt-line-s').hide();
            }
        }
    }
    $(document).ready(function() {

        //24/10/2013 huy thang
        stylevalidation('#ProjectAmrMdVariance');
        stylevalidation('#ProjectAmrVariance');

        function stylevalidation (id) {
            var calculatior = parseFloat($(id).val());
            if (calculatior > 0) {
                $(id).addClass('setvalidation');
            }else{
                $(id).removeClass('setvalidation');
            }
        }
        //24/10/2013 huy thang

        var height;
        $('#ProjectAmrProjectAmrSolution,#ProjectAmrProjectAmrRiskInformation,#ProjectAmrProjectAmrProblemInformation,#ProjectAmrProjectAmrSolutionDescription').focus(function(){
            $(this).tooltip('disable');
            height = $(this).height();
            $(this).stop().animate({height : '150'} , 1000);
        }).mouseup(function(){
            $(this).tooltip('close');
        }).blur(function(){
            $(this).tooltip('option' , 'content' , $(this).val());
            $(this).tooltip('enable');
            $(this).stop().animate({height : height}, 1000 , function(){
                $(this).css({height : ''});
            });
        }).tooltip({maxWidth : 1000, maxHeight : 300,content: function(target){
                return $(target).text();
            }});

        $("#ProjectAmrMdValidated,#ProjectAmrMdEngaged,#ProjectAmrMdForecasted").blur(function(){
            $(this).toNumber();
            var mdForecasted = $("#ProjectAmrMdForecasted").val().replace(/\$|\,/g,'');
            var mdValidated = $("#ProjectAmrMdValidated").val().replace(/\$|\,/g,'');
            $("#ProjectAmrMdVariance").val(mdForecasted - mdValidated);

        });
        $('#ProjectAmrValidated, #ProjectAmrEngaged, #ProjectAmrForecasted').blur(function()
        {
            $(this).toNumber();
            if($(this).val()=='')
                $(this).val('0.00');
            $(this).formatCurrency({ symbol:"" });
            //var Forecasted = $("#ProjectAmrForecasted").val().replace(/\$|\,/g,'');
            //var Validated = $("#ProjectAmrValidated").val().replace(/\$|\,/g,'');

            var Budget = $("#ProjectAmrValidated").val().replace(/\$|\,/g,'');
            var Engaged = $("#ProjectAmrEngaged").val().replace(/\$|\,/g,'');
            var Remain = $("#ProjectAmrForecasted").val().replace(/\$|\,/g,'');

            Budget = parseFloat(Budget);
            Engaged = parseFloat(Engaged);
            Remain = parseFloat(Remain);
            // console.log(Engaged);

            //24/10/2013 huy thang
            var calculatior = Engaged + Remain - Budget;
            if (calculatior > 0) {
                $("#ProjectAmrVariance").addClass('setvalidation');
            }else{
                $("#ProjectAmrVariance").removeClass('setvalidation');
            }

            $("#ProjectAmrVariance").val(Engaged + Remain - Budget).formatCurrency({ symbol:"",negativeFormat: '%s - %n'  });
            //24/10/2013 huy thang
        });

        var tabs = $(".wd-tab");
        var tab_a_selector = 'ul.ui-tabs-nav a';
        var tab_a_active = 'li.ui-state-active a';
        var cache = {};
        //tabs.tabs({event: 'change'});
        //   tabs.tabs({
        //    cache: true,
        //    event: 'change'
        //    });

        var state = {};
        var idx;

        var current_url =  document.URL ;
        tmp_p = current_url.substr(current_url.indexOf("#"),current_url.length);
        $("#project_tab_index").val(tmp_p);
        var p_tab_index = $("#project_tab_index").val();

        var check_multi_selected_deliverable = false;
        var check_multi_selected_evolution = false;

        tabs.find( tab_a_selector ).click(function(){
            var selected = $( ".wd-tab" ).tabs( "option", "selected" );
            var tab_index = (selected+1);
            $("#project_tab_index").val("#wd-fragment-"+tab_index);
            $("#flashMessage").hide();
            if(tab_index==9){
                if(!check_multi_selected_deliverable){
                    $("#ProjectLivrableActor").multiSelect({noneSelected: 'Select actors', oneOrMoreSelected: '*', selectAll: false });
                    check_multi_selected_deliverable = true;
                }
            }

            if(tab_index==10){
                if(!check_multi_selected_evolution){
                    $("#ProjectProjectEvolutionImpactId").multiSelect({noneSelected: 'Select impacts', oneOrMoreSelected: '*', selectAll: false });
                    check_multi_selected_evolution = true;
                }
            }
        });

        if(p_tab_index=="#wd-fragment-9"){
            if(!check_multi_selected_deliverable){
                $("#ProjectLivrableActor").multiSelect({noneSelected: 'Select actors', oneOrMoreSelected: '*', selectAll: false });
                check_multi_selected_deliverable = true;
            }
        }

        if(p_tab_index=="#wd-fragment-10"){
            if(!check_multi_selected_evolution){
                $("#ProjectProjectEvolutionImpactId").multiSelect({noneSelected: 'Select impacts', oneOrMoreSelected: '*', selectAll: false });
                check_multi_selected_evolution = true;
            }
        }

        $(".wd-table table").dataTable();

        /*$('#ProjectAmrProjectAmrMepDate').datepicker({
            showOn          : 'button',
            buttonImage     : '<?php echo $html->url("/img/front/calendar.gif") ?>',
            buttonImageOnly : true,
            dateFormat      : 'dd-mm-yy'
        });*/

        function isNumber(n) {
            return !isNaN(parseFloat(n)) && isFinite(n);
        }


        $('#btnSave').click(function(){

            $("#flashMessage").hide();
            $('div.error-message').remove();
            $("div.wd-input input, select").removeClass("form-error");
            //var v1 = isOnLimit("ProjectAmrBudget",'vc',0,"<?php __('The budget must be a number and at least 0.') ?>");
            var v2 = isOnLimit("ProjectAmrProjectAmrProgression",100,0,"<?php __('The progression must be a number and between 0 and 100.') ?>");
            var flag1=false, flag2=false;
            if(v2) flag2 = true;
            if (!(isDate('ProjectAmrProjectAmrMepDate'))) {
                var endDate = $("#ProjectAmrProjectAmrMepDate");
                endDate.addClass("form-error");
                var parentElem = endDate.parent();
                parentElem.addClass("error");
                parentElem.append('<div class="error-message">'+"<?php __("Invalid Date (Valid format is dd-mm-yyyy)") ?>"+'</div>');
            }
            else flag2=true;
            if(flag2){
                $('#wd-fragment-1 form:first').submit();
            }
            return false;
        });
        //	$('#ProjectAmrBudget').keypress(function(){
        //	  var rule = /^([0-9]*)$/;
        //	  var x=$('#ProjectAmrBudget').val();
        //	  $('div.error-message').remove();
        //	  if(!rule.test(x)){
        //	  var fomrerror = $("#ProjectAmrBudget");
        //			fomrerror.addClass("form-error");
        //			var parentElem = fomrerror.parent();
        //			parentElem.addClass("error");
        //			parentElem.append('<div class="error-message">'+"<?php __("The Budget must be a number ") ?>"+'</div>');
        //	  }
        //	  else{
        //	    var fomrerror = $("#ProjectAmrBudget");
        //			fomrerror.removeClass("form-error");
        //			$('div.error-message').remove();
        //	  }
        //	});
        $('#ProjectAmrProjectAmrProgression').keypress(function(){
            var rule = /^([0-9]*)$/;
            var x=$('#ProjectAmrProjectAmrProgression').val();
            $('div.error-message').remove();
            if(!rule.test(x)||x<0||x>100){
                var fomrerror = $("#ProjectAmrProjectAmrProgression");
                fomrerror.addClass("form-error");
                var parentElem = fomrerror.parent();
                parentElem.addClass("error");
                parentElem.append('<div class="error-message">'+"<?php __("The Progression must be a number 0-100 ") ?>"+'</div>');
            }
            else{
                var fomrerror = $("#ProjectAmrProjectAmrProgression");
                fomrerror.removeClass("form-error");
                $('div.error-message').remove();
            }
        });

        function isOnLimit(elementId,top,bottom,notify){
            var val = $("#"+elementId).val();
            if(isNumber(val)){
                if(top=='vc'){
                    if(bottom=='vc'){
                        return true;
                    }
                    else{
                        if(val>=bottom) return true;
                    }
                }
                else{
                    if(bottom=='vc'){
                        if(val<=top) return true;
                    }
                    else{
                        if(val>=bottom && val<=top) return true;
                    }
                }
            }
            NotifyError(elementId,notify);
            return false;
        }

        function NotifyError(elementId,notify){
            if(notify=='') notify = "This field must be between the limit.";
            var endDate = $("#"+elementId);
            endDate.addClass("form-error");
            var parentElem = endDate.parent();
            parentElem.addClass("error");
            parentElem.append('<div class="error-message">'+notify+'</div>');
        }

        $("#reset_button").click(function(){
            $("#project_team_id").val("");
            $("#title_form_update").html("<?php __("Add a new employee for this project") ?>");
            $("#project_phase_plan_id").val("");
            $("#title_form_update_phase").html("<?php __("Add a new phase planning for this project") ?>");
            $("#project_milestone_id").val("");
            $("#title_form_update_milestone").html("<?php __("Add a new milestone for this project") ?>");
            $("#project_task_id").val("");
            $("#title_form_update_task").html("<?php __("Add a new task for this project") ?>");
            $("#project_risk_id").val("");
            $("#title_form_update_risk").html("<?php __("Add a new risk for this project") ?>");
            $("#project_issue_id").val("");
            $("#title_form_update_issue").html("<?php __("Add a new issue for this project") ?>");
            $("#project_decision_id").val("");
            $("#title_form_update_decisions").html("<?php __("Add a new decision for this project") ?>");
            $("#ProjectLivrableActor span").html("<?php __("Select actors") ?>");
            $("input[name='ProjectLivrableActor[]']").removeAttr("checked");
            $("input[name='ProjectLivrableActor[]']").parent().removeClass("checked");
            $("#project_livrable_id").val("");
            $("#title_form_update_livrable").html("<?php __("Add new a deliverable for this project") ?>");
            $("#ProjectProjectEvolutionImpactId span").html("<?php __("Select impact") ?>");
            $("input[name='ProjectProjectEvolutionImpactId[]']").removeAttr("checked");
            $("input[name='ProjectProjectEvolutionImpactId[]']").parent().removeClass("checked");
            $("#project_evolution_id").val("");
            $("#title_form_update_evolution").html("<?php __("Add a new evolution for this project") ?>");
            // HuyTD: Quick reset form command
            $("[name*='data[ProjectAmr]']").val("");
        });


        // Script for  subprogram
        $("#ProjectAmrProjectAmrSubProgramId").attr("disabled", "disabled");
        if($.trim($("#ProjectAmrProjectAmrProgramId").val()!="")){
            var id = $("#ProjectAmrProjectAmrProgramId").val();
            var current_id = '<?php echo (!empty($this->data['ProjectAmr']['project_amr_sub_program_id'])) ? $this->data['ProjectAmr']['project_amr_sub_program_id'] : "" ?>';
            $.ajax({
                url: '<?php echo $html->url('/project_amrs/get_sub_program/') ?>' + id +'/'+current_id,
                beforeSend: function() { $("#ProjectAmrProjectAmrSubProgramId").html("<option>Loading...</option>"); },
                success: function(data) {
                    $("#ProjectAmrProjectAmrSubProgramId").html(data);
                    $("#ProjectAmrProjectAmrSubProgramId").removeAttr("disabled");


                }
            });
        }

        $("#ProjectAmrProjectAmrProgramId").change(function(){
            var id = $(this).val();
            var program_current_id = '<?php echo (!empty($this->data['ProjectAmr']['project_amr_program_id'])) ? $this->data['ProjectAmr']['project_amr_program_id'] : ""; ?>';
            $("#ProjectAmrProjectAmrSubProgramId").attr("disabled", "disabled");
            if(id == program_current_id){
                var current_id = '<?php echo (!empty($this->data['ProjectAmr']['project_amr_sub_program_id'])) ? $this->data['ProjectAmr']['project_amr_sub_program_id'] : "" ?>';
                $.ajax({
                    url: '<?php echo $html->url('/project_amrs/get_sub_program/') ?>' + id + "/"+current_id,
                    beforeSend: function() { $("#ProjectAmrProjectAmrSubProgramId").html("<option>Loading...</option>"); },
                    success: function(data) {
                        $("#ProjectAmrProjectAmrSubProgramId").html(data);
                        $("#ProjectAmrProjectAmrSubProgramId").removeAttr("disabled");
                    }
                });
            }else{
                $.ajax({
                    url: '<?php echo $html->url('/project_amrs/get_sub_program/') ?>' + id,
                    beforeSend: function() { $("#ProjectAmrProjectAmrSubProgramId").html("<option>Loading...</option>"); },
                    success: function(data) {
                        $("#ProjectAmrProjectAmrSubProgramId").html(data);
                        $("#ProjectAmrProjectAmrSubProgramId").removeAttr("disabled");
                    }
                });
            }

        });

        $("#ProjectAmrProjectAmrSubCategoryId").attr("disabled", "disabled");
        if($.trim($("#ProjectAmrProjectAmrCategoryId").val()!="")){
            var id = $("#ProjectAmrProjectAmrCategoryId").val();
            var current_id = '<?php echo (!empty($this->data['ProjectAmr']['project_amr_sub_category_id'])) ? $this->data['ProjectAmr']['project_amr_sub_category_id'] : "" ?>';
            $.ajax({
                url: '<?php echo $html->url('/project_amrs/get_sub_categories/') ?>' + id +"/"+current_id ,
                beforeSend: function() { $("#ProjectAmrProjectAmrSubCategoryId").html("<option>Loading...</option>"); },
                success: function(data) {
                    $("#ProjectAmrProjectAmrSubCategoryId").html(data);
                    $("#ProjectAmrProjectAmrSubCategoryId").removeAttr("disabled");
                }
            });
        }

        $("#ProjectAmrProjectAmrCategoryId").change(function(){
            var id = $(this).val();
            var cate_current_id = '<?php echo (!empty($this->data['ProjectAmr']['project_amr_category_id'])) ? $this->data['ProjectAmr']['project_amr_category_id'] : "" ?>';
            $("#ProjectAmrProjectAmrSubCategoryId").attr("disabled", "disabled");
            if(id == cate_current_id){
                var current_id = '<?php echo (!empty($this->data['ProjectAmr']['project_amr_sub_category_id'])) ? $this->data['ProjectAmr']['project_amr_sub_category_id'] : "" ?>';
                $.ajax({
                    url: '<?php echo $html->url('/project_amrs/get_sub_categories/') ?>' + id + "/"+ current_id,
                    beforeSend: function() { $("#ProjectAmrProjectAmrSubCategoryId").html("<option>Loading...</option>"); },
                    success: function(data) {
                        $("#ProjectAmrProjectAmrSubCategoryId").html(data);
                        $("#ProjectAmrProjectAmrSubCategoryId").removeAttr("disabled");
                    }
                });
            }else{
                $.ajax({
                    url: '<?php echo $html->url('/project_amrs/get_sub_categories/') ?>' + id,
                    beforeSend: function() { $("#ProjectAmrProjectAmrSubCategoryId").html("<option>Loading...</option>"); },
                    success: function(data) {
                        $("#ProjectAmrProjectAmrSubCategoryId").html(data);
                        $("#ProjectAmrProjectAmrSubCategoryId").removeAttr("disabled");
                    }
                });
            }

        });

        try {
            oHandler = $(".mydds").msDropDown().data("dd");
            $("#ver").html($.msDropDown.version);
        } catch(e) {
            alert("Error: "+e.message);
        }
    });



</script>
