<div class="group-content" style="clear: both;">
    <h3><span><?php echo __d(sprintf($_domain, 'KPI'), 'Planning', true);?></span></h3>
    <div class="wd-input wd-input-90">
        <!--label for="project-plan"><?php //__("Planning") ?></label-->
        <!-- <?php
        // echo $this->Form->input('project_amr_plan_id', array('div' => false, 'label' => false,
        // 	'class' => 'selection-plus',
        // 	'name' => 'data[ProjectAmr][project_amr_plan_id]',
        // 	'value' => (!empty($this->data['ProjectAmr']['project_amr_plan_id'])) ? $this->data['ProjectAmr']['project_amr_plan_id'] : "",
        // 	"empty" => __("-- Select --", true),
        // ));
        ?> -->
        <div style="float: left; line-height: -40px; width:400px">
            <div class="wd-input wd-weather-list-dd">
                <ul style="float: left; display: inline;">
                    <li><input class="input_weather" checked="true" <?php echo !(($canModified && !$_isProfile)|| ($_isProfile && $_canWrite)) ? 'disabled' : '' ?> style="width: 25px; margin-top: 8px;" <?php echo @$this->data["ProjectAmr"]["planning_weather"] == 'sun' ? 'checked' : 'checked'; ?> value="sun" name="data[ProjectAmr][planning_weather][]" type="radio" /> <img title="Sun"  src="<?php echo $html->url('/img/sun.png') ?>"  /></li>
                    <li><input class="input_weather" <?php echo !(($canModified && !$_isProfile)|| ($_isProfile && $_canWrite)) ? 'disabled' : '' ?> type="radio" <?php echo @$this->data["ProjectAmr"]["planning_weather"] == 'cloud' ? 'checked' : ''; ?> value="cloud" name="data[ProjectAmr][planning_weather][]" style="width: 25px;margin-top: 8px;" /> <img title="Cloud" src="<?php echo $html->url('/img/cloud.png') ?>"  /></li>
                    <li><input class="input_weather" <?php echo !(($canModified && !$_isProfile)|| ($_isProfile && $_canWrite)) ? 'disabled' : '' ?> type="radio" <?php echo @$this->data["ProjectAmr"]["planning_weather"] == 'rain' ? 'checked' : ''; ?> value="rain" name="data[ProjectAmr][planning_weather][]" style="width: 25px;margin-top: 8px;"   /> <img title="Rain"  src="<?php echo $html->url('/img/rain.png') ?>"  /></li>
                </ul>
                <?php //echo $this->Form->radio('weather', array('div'=>false, 'label'=>false)); ?>
                <a href="<?php echo $html->url("/project_phase_plans/phase_vision/" . $projectName['Project']['id']) ?>" class="btn btn-gantt" title="Gantt+"><span><?php __('Gantt+') ?></span></a>
                 <div class="delay-plan">
                        <?php echo __('Delay:');?>
                        <?php echo $delay;?>
                        <?php echo __('M.D', true);?>
                </div>
            </div>
        </div>
        <div class="wd-title" style="width: 380px; float: right;">
            <div id="gantt-display" style="display: none">
               <label class="title" style="float: left; padding-right: 10px;color:#000;"><?php __('Display initial time'); ?> </label>
                <?php
                    $displayplan = 1;
                    $display = 1;
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
                    <label class="title" style="float: left; padding-right: 10px;color:#000;"><?php __('Display real time'); ?> </label>
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
            <!--a href="<?php //echo $html->url("/project_amrs/exportExcel/" . $projectName['Project']['id']) ?>" class="wd-add-project" style="margin-right:5px; "><span><?php //__('Export Excel') ?></span></a-->
            <!-- <a href="<?php echo $html->url("/project_phase_plans/phase_vision/" . $projectName['Project']['id']) ?>" class="wd-add-project" style="margin-right:5px; "><span><?php __('Gantt+') ?></span></a> -->
        </div>
    </div>

<!-- Gantt -->
<div id="GanttChartDIV">
    <?php
    $rows = 0;
    $start = $end = 0;
    $data = $projectId = $conditions = array();
    $stones = array();
    if (!empty($projectMilestones)) {
        foreach ($projectMilestones as $p) {
            $_start = strtotime($p['milestone_date']);
            if (!$start || $_start < $start) {
                $start = $_start;
            } elseif (!$end || $_start > $end) {
                $end = $_start;
            }
            $stones[] = array($_start, $p['project_milestone'], $p['validated']);
        }
    }
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
//                            pr($data);
//                            exit();

    unset($projects, $project, $_data, $_phase, $phase);
    $summary = isset($this->params['url']['summary']) ? (bool) $this->params['url']['summary'] : false;
    $showType = isset($this->params['url']['type']) ? (int) $this->params['url']['type'] : 0;


    if (empty($start) || empty($end)) {
        echo $this->Html->tag('h1', __('No data exist to create Gantt chart', true), array('style' => 'color:red'));
    } else {
        $this->Gantt->create($type, $start, $end, $stones, false , false);

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
        $this->Gantt->end(false);
    }
    ?>
</div>
</div>
