<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title"><?php echo __d(sprintf($_domain, 'KPI'), 'Log Comment', true);?></h3>
				</div>
				<div class="box-body">
					<?php
					echo $this->Form->input('project_amr_solution_description', array('type' => 'textarea', 'div' => false,
						'name' => 'data[ProjectAmr][project_amr_solution_description]',
						'label' => false,
						'class' => 'form-control',
						'value' => (!empty($this->data['ProjectAmr']['project_amr_solution_description'])) ? $this->data['ProjectAmr']['project_amr_solution_description'] : ""
					));
					?>
				</div>
			</div>