<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title"><?php echo __d(sprintf($_domain, 'KPI'), 'Comment', true);?></h3>
					<div class="box-tools pull-right">
						<?php if($checkIsChang === 'true'):?>
							<a href="javascript:void(0);" id="add-activity" class="btn btn-primary" onclick="addLogSaleLead();"><i class="glyphicon glyphicon-plus"></i></a>
						<?php endif;?>
					</div>
				</div>
				<div class="box-body has-scroll">
					<div id="pch_log" class="pch_log" >
						<div id="pch_log_system_content" class="pch_log_system_content">
							<?php
								if(!empty($logSystems)){
									$LogHtml = '';
									$_relsLog = 1;
									if($checkIsChang === 'false'){
										$_disable = 'disabled="disabled"';
									} else {
										$_disable = '';
									}
									//$checkIsChang
									foreach($logSystems as $idLog => $logSystem){
										$_onchange = 'onchange=\'updateLogSystem("' . $_relsLog . '", "' . $logSystem['id'] . '");\'';
										$linkAvatar = '/img/business/avatar.gif';
										$_avatar = !empty($avatarEmploys[$logSystem['employee_id']]) ? $avatarEmploys[$logSystem['employee_id']] : '';
										if(!empty($_avatar)){
											$linkAvatar = '/files/avatar_employ/'.$companyName.'/'.$logSystem['employee_id'].'/'.$_avatar;
										}
										/*$LogHtml .= '<div class="pch_log_system" rels="' . $_relsLog . '">' . 
											'<div class="pch_log_name">' .
												'<input id="logName_' . $_relsLog . '" readonly="readonly" class="input_disabled" value="' . $logSystem['name'] . '" />' .
											'</div>' .
											'<div class="pch_log_description">' .
												'<textarea class="form-control" id="logDes_' . $_relsLog . '" ' . $_onchange . ' ' . $_disable . '>' . $logSystem['description'] . '</textarea>' .
											'</div>' .
											'<div class="pch_log_avatar pch_log_avatar_content">' .
												'<img id="logAvatar_' . $_relsLog . '" src="' . $linkAvatar . '" />' . 
											'</div>' .
										'</div>';*/
										$LogHtml .= '<div class="pch_log_system" rels="' . $_relsLog . '">' . 
											'<div class="input-group">
											  <span class="input-group-addon no-padding" id="sizing-addon2"><img class="full-width" id="logAvatar_' . $_relsLog . '" src="' . $linkAvatar . '" /></span>
											  <input class="form-control" aria-describedby="sizing-addon2" id="logName_' . $_relsLog . '" readonly="readonly" value="' . $logSystem['name'] . '" />
											  
											</div>' .
											'<div class="pch_log_description">' .
												'<textarea class="form-control" id="logDes_' . $_relsLog . '" ' . $_onchange . ' ' . $_disable . '>' . $logSystem['description'] . '</textarea>' .
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