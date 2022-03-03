<?php
$menu_list = array('cities', 'countries', 'companies', 'currencies', 'profit_centers','absences');
if ($this->params['controller'] == 'pages')
    $active = 1;
if ($this->params['controller'] == 'projects')
    $active = 2;
if ($this->params['controller'] == 'employees')
    $active = 3;
if ($this->params['controller'] == 'user_views')
    $active = 5;
if ($this->params['controller'] == 'projects' && $this->params['action'] == 'edit')
    $active = 6;

if (in_array($this->params['controller'], $menu_list) || (strpos($this->params['controller'], 'project_') === 0))
    $active = 4;
$proj_list = array('projects', 'project_teams', 'project_parts', 'project_phase_plans', 'project_phase_plans', 'project_milestones', 'project_tasks', 'project_risks', 'project_issues', 'project_decisions', 'project_livrables', 'project_evolutions', 'project_amrs', 'project_staffings', 'project_created_vals', 'project_images');
if (in_array($this->params['controller'], $proj_list))
    $active = 2;
$employee_info = $this->Session->read('Auth.employee_info');
$is_sas = $employee_info['Employee']['is_sas'];
if ($is_sas != 1) {
    $role = $employee_info['Role']['name'];
}
?>
<div id="wd-top-nav">
    <ul>
        <li>
            <a href="<?php echo $html->url("/projects/"); ?>">
                <span class="tab-left <?php if ($active == 2) { ?>wd-current-left<?php } ?>"></span>
                <span class="tab-center <?php if ($active == 2) { ?>wd-current-center<?php } ?>"><?php __('Projects') ?></span>
                <span class="tab-right <?php if ($active == 2) { ?>wd-current-right<?php } ?>"></span>
            </a>
            <?php
            if (!($this->params['controller'] == 'projects' && in_array($this->params['action'], array('index'))) && (!empty($this->params['pass']) && $this->params['controller'] != 'user_views'
                    && $this->params['controller'] != 'project_created_values' && $this->params['controller'] != 'employees' && $this->params['controller'] != 'absences')) :
                ?>
                <ul>
                    <li class="<?php echo ($this->params['controller'] == 'projects') ? "wd-current" : "tooltip-pm-details" ?>"><a href="<?php echo $html->url("/projects/edit/" . @$project_id) ?>"><?php __("Details") ?></a></li>
                    <li class="<?php echo ($this->params['controller'] == 'project_global_views') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/project_global_views/index/" . @$project_id) ?>"><?php __("Global View") ?></a></li>
                    <li class="<?php echo ($this->params['controller'] == 'project_images') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/project_images/index/" . @$project_id) ?>"><?php __("Pictures") ?></a></li>
                    <li class="<?php echo ($this->params['controller'] == 'project_created_vals') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/project_created_vals/index/" . @$project_id) ?>"><?php __("Created value") ?></a></li>
                    <li class="<?php echo ($this->params['controller'] == 'project_teams') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/project_teams/index/" . @$project_id) ?>"><?php __("Teams") ?></a></li>
                    <li class="<?php echo ($this->params['controller'] == 'project_parts') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/project_parts/index/" . @$project_id) ?>"><?php __("Part") ?></a></li>
                    <li class="<?php echo ($this->params['controller'] == 'project_phase_plans') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/project_phase_plans/index/" . @$project_id) ?>"><?php __("Phase") ?></a></li>
                    <li class="<?php echo ($this->params['controller'] == 'project_staffings') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/project_staffings/vision/" . @$project_id) ?>"><?php __("Staffing") ?></a></li>
                    <li class="<?php echo ($this->params['controller'] == 'project_milestones') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/project_milestones/index/" . @$project_id) ?>"><?php __("Milestones") ?></a></li>
                    <li class="<?php echo ($this->params['controller'] == 'project_tasks') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/project_tasks/index/" . @$project_id) ?>"><?php __("Tasks") ?></a></li>
                    <li class="<?php echo ($this->params['controller'] == 'project_risks') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/project_risks/index/" . @$project_id) ?>"><?php __("Risks") ?></a></li>
                    <li class="<?php echo ($this->params['controller'] == 'project_issues') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/project_issues/index/" . @$project_id) ?>"><?php __("Issues") ?></a></li>
                    <li class="<?php echo ($this->params['controller'] == 'project_decisions') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/project_decisions/index/" . @$project_id) ?>"><?php __("Decisions") ?></a></li>
                    <li class="<?php echo ($this->params['controller'] == 'project_livrables') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/project_livrables/index/" . @$project_id) ?>"><?php __("Deliverables") ?></a></li>
                    <li class="<?php echo ($this->params['controller'] == 'project_evolutions') ? "wd-current" : "" ?>"><a href="<?php echo $html->url("/project_evolutions/index/" . @$project_id) ?>"><?php __("Evolution") ?></a></li>
                    <li class="<?php echo ($this->params['controller'] == 'project_amrs') ? "wd-current" : "tooltip-pm-amrs" ?>"><a href="<?php echo $html->url("/project_amrs/index/" . @$project_id) ?>"><?php __("KPI") ?></a></li>
                </ul>
                <?php if ($this->params['controller'] != 'projects') : ?>
                    <?php
                    $output = '';
                    $titles = array(
                        'primary_objectives' => __('Primary Objectives', true),
                        'constraint' => __('Constraint', true),
                        'remark' => __('Remark', true)
                    );
                    $datax = ClassRegistry::getObject('Project')->find('first', array(
                        'recursive' => -1,
                        'fields' => array('primary_objectives', 'constraint', 'remark'),
                        'conditions' => array('id' => $project_id)
                            ));
                    if ($datax && ($datax = array_filter($datax['Project']))) {
                        foreach ($datax as $k => $v) {
                            $output .= "<dt><b>{$titles[$k]}</b> : </dt><dd>$v</dd>";
                        }
                        $output = "<dl class='tooltip-pm-details'>$output</dl>";
                    }
                    ?>
                    <?php if (!empty($output)) : ?>
                        <script type="text/javascript">
                            (function($){
                                $(function(){
                                    $('.tooltip-pm-details').tooltip({
                                        maxHeight : 500,
                                        maxWidth : 400,
                                        type : ['bottom','left'],
                                        content:  <?php echo json_encode($output); ?>});
                                });
                            })(jQuery);
                        </script>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($this->params['controller'] != 'project_amrs') : ?>
                    <?php
                    $output = '';
                    $titles = array(
                        'project_amr_solution' => __('General Comment', true)
                    );
                    $datax = ClassRegistry::getObject('Project')->ProjectAmr->find('first', array(
                        'recursive' => -1,
                        'fields' => array('project_amr_solution'),
                        'conditions' => array('project_id' => $project_id)
                            ));
                    if ($datax && ($datax = array_filter($datax['ProjectAmr']))) {
                        foreach ($datax as $k => $v) {
                            $output .= "<dt><b>{$titles[$k]}</b> : </dt><dd>$v</dd>";
                        }
                        $output = "<dl class='tooltip-pm-amrs'>$output</dl>";
                    }
                    ?>
                    <?php if (!empty($output)) : ?>
                        <script type="text/javascript">
                            (function($){
                                $(function(){
                                    $('.tooltip-pm-amrs').tooltip({
                                        maxHeight : 500,
                                        maxWidth : 400,
                                        type : ['bottom','right'],
                                        content:  <?php echo json_encode($output); ?>});
                                });
                            })(jQuery);
                        </script>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </li>
        <li>
            <a href="<?php echo $html->url("/employees/"); ?>">
                <span class="tab-left <?php if ($active == 3) { ?>wd-current-left<?php } ?>"></span>
                <span class="tab-center <?php if ($active == 3) { ?>wd-current-center<?php } ?>"><?php __('Employees') ?></span>
                <span class="tab-right <?php if ($active == 3) { ?>wd-current-right<?php } ?>"></span>
            </a>
        </li>
        <li >
            <a href="<?php echo $html->url("/user_views/"); ?>">
                <span class="tab-left <?php if ($active == 5) { ?> wd-current-left<?php } ?>"></span>
                <span class="tab-center <?php if ($active == 5) { ?> wd-current-center<?php } ?>"><?php __('Personalized Views') ?></span>
                <span class="tab-right <?php if ($active == 5) { ?> wd-current-right<?php } ?> "></span>
            </a>
        </li>
        <?php if ($is_sas || $role == "admin") : ?>
            <li >
                <a href="<?php echo $html->url("/administrators/"); ?>">
                    <span class="tab-left <?php if ($active == 4) { ?>wd-current-left<?php } ?>"></span>
                    <span class="tab-center <?php if ($active == 4) { ?>wd-current-center<?php } ?>"><?php __('Administration') ?></span>
                    <span class="tab-right <?php if ($active == 4) { ?>wd-current-right<?php } ?>"></span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</div>
