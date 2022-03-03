<ul class="wd-item">
	<li class="<?php echo ($this->params['controller'] == 'projects')?"wd-current":"" ?>"><a href="<?php echo $html->url("/projects/edit/".$project_id )?>"><?php __("Project details")?></a></li>
	<li class="<?php echo ($this->params['controller'] == 'project_teams')?"wd-current":"" ?>"><a href="<?php echo $html->url("/project_teams/index/".$project_id )?>"><?php __("Project teams")?></a></li>
	<li class="<?php echo ($this->params['controller'] == 'project_phase_plans')?"wd-current":"" ?>"><a href="<?php echo $html->url("/project_phase_plans/index/".$project_id)?>"><?php __("Phase planning")?></a></li>
	<li class="<?php echo ($this->params['controller'] == 'project_milestones')?"wd-current":"" ?>"><a href="<?php echo $html->url("/project_milestones/index/".$project_id)?>"><?php __("Milestones")?></a></li>
	<li class="<?php echo ($this->params['controller'] == 'project_tasks')?"wd-current":"" ?>"><a href="<?php echo $html->url("/project_tasks/index/".$project_id)?>"><?php __("Tasks")?></a></li>
	<li class="<?php echo ($this->params['controller'] == 'project_risks')?"wd-current":"" ?>"><a href="<?php echo $html->url("/project_risks/index/".$project_id)?>"><?php __("Risks")?></a></li>
	<li class="<?php echo ($this->params['controller'] == 'project_issues')?"wd-current":"" ?>"><a href="<?php echo $html->url("/project_issues/index/".$project_id)?>"><?php __("Issues")?></a></li>
	<li class="<?php echo ($this->params['controller'] == 'project_decisions')?"wd-current":"" ?>"><a href="<?php echo $html->url("/project_decisions/index/".$project_id)?>"><?php __("Decisions")?></a></li>
	<li class="<?php echo ($this->params['controller'] == 'project_livrables')?"wd-current":"" ?>"><a href="<?php echo $html->url("/project_livrables/index/".$project_id)?>"><?php __("Deliverables")?></a></li>
	<li class="<?php echo ($this->params['controller'] == 'project_evolutions')?"wd-current":"" ?>"><a href="<?php echo $html->url("/project_evolutions/index/".$project_id)?>"><?php __("Evolution")?></a></li>
	<li class="<?php echo ($this->params['controller'] == 'project_amrs')?"wd-current":"" ?>"><a href="<?php echo $html->url("/project_amrs/index/".$project_id)?>"><?php __("KPI")?></a></li>
        <li class="<?php echo ($this->params['controller'] == 'project_staffings')?"wd-current":"" ?>"><a href="<?php echo $html->url("/project_staffings/vision/".$project_id)?>"><?php __("Staffing")?></a></li>			
</ul>
