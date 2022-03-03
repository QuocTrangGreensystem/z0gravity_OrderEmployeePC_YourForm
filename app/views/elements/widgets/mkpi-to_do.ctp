<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title"><?php echo __d(sprintf($_domain, 'KPI'), 'To Do', true);?></h3>
					<div class="box-tools pull-right">
						<?php if($checkIsChang === 'true'):?>
							<a href="javascript:void(0);" id="add-activity" class="btn btn-primary" onclick="addLog('pch_todo', 'ToDo');"><i class="glyphicon glyphicon-plus"></i></a>
						<?php endif;?>
					</div>
				</div>
				<div class="box-body has-scroll">
					<div id="pch_todo" class="pch_log">
						<div class="pch_log_system_content">
							<?php
								if(!empty($todos)){
									$LogHtml = '';
									$_relsLog = 1;
									if($checkIsChang === 'false'){
										$_disable = 'disabled="disabled"';
									} else {
										$_disable = '';
									}
									//$checkIsChang
									foreach($todos as $idLog => $todo){
										$_onchange = 'onchange=\'updateLog("' . $_relsLog . '", "ToDo", "' . $todo['id'] . '");\'';
										$linkAvatar = '/img/business/avatar.gif';
										$_avatar = !empty($avatarEmploys[$todo['employee_id']]) ? $avatarEmploys[$todo['employee_id']] : '';
										if(!empty($_avatar)){
											$linkAvatar = '/files/avatar_employ/'.$companyName.'/'.$todo['employee_id'].'/'.$_avatar;
										}
										$LogHtml .= '<div class="pch_log_system" rels="' . $_relsLog . '">' . 
											'<div class="input-group">' .
												'<span class="input-group-addon no-padding" id="sizing-addon2"><img class="full-width" alt="" src="' . $linkAvatar . '" /></span>' .
												'<input id="Done_' . $_relsLog . '" readonly="readonly" class="form-control" aria-describedby="sizing-addon2" value="' . $todo['name'] . '" />' .
											'</div>' .
											'<div class="pch_log_description">' .
												'<textarea class="form-control" id="DoneDes_' . $_relsLog . '" ' . $_onchange . ' ' . $_disable . '>' . $todo['description'] . '</textarea>' .
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