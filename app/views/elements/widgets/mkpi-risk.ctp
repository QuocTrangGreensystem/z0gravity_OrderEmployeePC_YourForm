<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title"><?php echo __d(sprintf($_domain, 'KPI'), 'Risk', true);?></h3>
				</div>
				<div class="box-body">
					<!--label for="risk-control"><?php //__("Risk Control") ?></label-->
					<?php
					echo $this->Form->input('project_amr_risk_control_id', array('div' => false, 'label' => false,
						'class' => 'selection-plus',
						'name' => 'data[ProjectAmr][project_amr_risk_control_id]',
						'value' => (!empty($this->data['ProjectAmr']['project_amr_risk_control_id'])) ? $this->data['ProjectAmr']['project_amr_risk_control_id'] : "",
						"empty" => __("-- Select --", true),
					));
					?>
					<ul class="list-inline"> 
						<li><input checked="true" style="width: 25px; margin-top: 8px;" <?php echo @$this->data["ProjectAmr"]["risk_control_weather"] == 'sun' ? 'checked' : 'checked'; ?> value="sun" name="data[ProjectAmr][risk_control_weather]" type="radio" /> <img title="Sun"  src="<?php echo $html->url('/img/sun.png') ?>"  /></li>
						<li><input type="radio" <?php echo @$this->data["ProjectAmr"]["risk_control_weather"] == 'cloud' ? 'checked' : ''; ?> value="cloud" name="data[ProjectAmr][risk_control_weather]" style="width: 25px;margin-top: 8px;" /> <img title="Cloud" src="<?php echo $html->url('/img/cloud.png') ?>"  /></li>
						<li><input type="radio" <?php echo @$this->data["ProjectAmr"]["risk_control_weather"] == 'rain' ? 'checked' : ''; ?> value="rain" name="data[ProjectAmr][risk_control_weather]" style="width: 25px;margin-top: 8px;"   /> <img title="Rain"  src="<?php echo $html->url('/img/rain.png') ?>"  /></li>
					</ul>
					<?php //echo $this->Form->radio('weather', array('div'=>false, 'label'=>false)); ?>

					<div id="pch_log_1">
						<div id="pch_log_system_content_1">
							<?php
								if(!empty($commentRisks)){
									$LogHtml = '';
									$_relsLog = 1;
									foreach($commentRisks as $idLog => $commentRisk){
										$_onchange = 'onchange=\'updateLogSystem("' . $_relsLog . '", "' . $commentRisk['id'] . '");\'';
										$linkAvatar = '/img/business/avatar.gif';
										$_avatar = !empty($avatarEmploys[$commentRisk['employee_id']]) ? $avatarEmploys[$commentRisk['employee_id']] : '';
										if(!empty($_avatar)){
											$linkAvatar = '/files/avatar_employ/'.$companyName.'/'.$commentRisk['employee_id'].'/'.$_avatar;
										}
										$LogHtml .= '<div class="pch_log_system" rels="' . $_relsLog . '">' . 
											'<div class="input-group">' .
												'<span class="input-group-addon no-padding" id="sizing-addon2"><img class="full-width" src="' . $linkAvatar . '" /></span>' .
												'<input readonly="readonly" class="form-control" aria-describedby="sizing-addon2" value="' . $commentRisk['name'] . '" />' .
											'</div>' .
											'<div class="pch_log_description">' .
												'<span class="des">'.$commentRisk['description'].'</span>' .
											'</div>' .
										'</div>';
										$_relsLog++;
									}
									echo $LogHtml;
								}
							?>
						</div>
					</div>
				</div>
			</div>