<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title"><?php echo __d(sprintf($_domain, 'KPI'), 'Staffing', true);?></h3>
				</div>
				<div class="box-body">
					<!--label for="organization"><?php //__("Organization") ?></label-->
					<?php
					// echo $this->Form->input('project_amr_organization_id', array('div' => false, 'label' => false,
					// 	'class' => 'selection-plus',
					// 	'name' => 'data[ProjectAmr][project_amr_organization_id]',
					// 	'value' => (!empty($this->data['ProjectAmr']['project_amr_organization_id'])) ? $this->data['ProjectAmr']['project_amr_organization_id'] : "",
					// 	"empty" => __("-- Select --", true),
					// ));
					?>
					<ul class="list-inline">
						<li><input checked="true" style="width: 25px; margin-top: 8px;" <?php echo @$this->data["ProjectAmr"]["organization_weather"] == 'sun' ? 'checked' : 'checked'; ?> value="sun" name="data[ProjectAmr][organization_weather]" type="radio" /> <img title="Sun"  src="<?php echo $html->url('/img/sun.svg') ?>"  /></li>
						<li><input type="radio" <?php echo @$this->data["ProjectAmr"]["organization_weather"] == 'cloud' ? 'checked' : ''; ?> value="cloud" name="data[ProjectAmr][organization_weather]" style="width: 25px;margin-top: 8px;" /> <img title="Cloud" src="<?php echo $html->url('/img/cloud.svg') ?>"  /></li>
						<li><input type="radio" <?php echo @$this->data["ProjectAmr"]["organization_weather"] == 'rain' ? 'checked' : ''; ?> value="rain" name="data[ProjectAmr][organization_weather]" style="width: 25px;margin-top: 8px;"   /> <img title="Rain"  src="<?php echo $html->url('/img/rain.svg') ?>"  /></li>
					</ul>
					<?php
						$assPc = !empty($assginProfitCenter) ? $assginProfitCenter : 0;
						//echo $assPc . __('% Assigned to profit center', true);
					?>
					<div class="progress">
					  <div class="progress-bar progress-bar-striped active <?php progressClass($assPc) ?>" role="progressbar"
					  aria-valuenow="<?php echo $assPc?>" aria-valuemin="0" aria-valuemax="100" style="min-width: 2em;width:<?php echo $assPc?>%">
						<?php echo __($assPc . '% Assigned to profit center', true);?>
					  </div>
					</div>

					<?php
						$assEm = !empty($assgnEmployee) ? $assgnEmployee : 0;
						//echo $assEm . __('% Assigned to employee', true);
					?>
					<div class="progress">
					  <div class="progress-bar progress-bar-striped active <?php progressClass($assEm) ?>" role="progressbar"
					  aria-valuenow="<?php echo $assEm?>" aria-valuemin="0" aria-valuemax="100" style="min-width: 2em;width:<?php echo $assEm?>%">
						<?php echo __($assEm . '% Assigned to employee', true);?>
					  </div>
					</div>

				</div>
			</div>
