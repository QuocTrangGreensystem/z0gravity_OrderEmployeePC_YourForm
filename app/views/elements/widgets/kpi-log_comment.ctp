<div class="group-content">
	<h3><span><?php echo __d(sprintf($_domain, 'KPI'), 'Log Comment', true);?></span></h3>
	<div class="wd-input" style="margin: 5px;">
		<?php
		echo $this->Form->input('project_amr_solution_description', array('type' => 'textarea', 'div' => false,
			'name' => 'data[ProjectAmr][project_amr_solution_description]',
			'label' => false,
			'style' => 'width: 99%',
			'value' => (!empty($this->data['ProjectAmr']['project_amr_solution_description'])) ? $this->data['ProjectAmr']['project_amr_solution_description'] : ""
		));
		?>
	</div>
</div>