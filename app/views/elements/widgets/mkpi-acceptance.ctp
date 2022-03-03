<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title"><?php echo __d(sprintf($_domain, 'KPI'), 'Acceptance', true);?></h3>
				</div>
				<div class="box-body">
					<div id="acceptance">
						<?php
						foreach($acceptances as $acc){
							if( !$acc['ProjectAcceptance']['weather'] )$acc['ProjectAcceptance']['weather'] = 'sun';
							$accId = $acc['ProjectAcceptance']['id'];
							$progress = $acc['ProjectAcceptance']['progress'] ? $acc['ProjectAcceptance']['progress'] : '0.00';
						?>
						<div class="row">
							<div class="col-md-3"><?php echo @$types[ $acc['ProjectAcceptance']['project_acceptance_type_id'] ] ?></div>
							<div class="col-md-4">
							<ul style="margin-top:-5px;" class="list-inline"> 
								<li><input checked="true" style="width: 25px; margin-top: 8px;" <?php echo @$acc["ProjectAcceptance"]["weather"] == 'sun' ? 'checked' : 'checked'; ?> value="sun" name="data[ProjectAcceptance][<?php echo $accId ?>]" type="radio" class="weather" data-id="<?php echo $accId ?>" /> <img title="Sun"  src="<?php echo $html->url('/img/sun.png') ?>"  /></li>
								<li><input type="radio" <?php echo @$acc["ProjectAcceptance"]["weather"] == 'cloud' ? 'checked' : ''; ?> value="cloud" name="data[ProjectAcceptance][<?php echo $accId ?>]" style="width: 25px;margin-top: 8px;" class="weather" data-id="<?php echo $accId ?>" /> <img title="Cloud" src="<?php echo $html->url('/img/cloud.png') ?>"  /></li>
								<li><input type="radio" <?php echo @$acc["ProjectAcceptance"]["weather"] == 'rain' ? 'checked' : ''; ?> value="rain" name="data[ProjectAcceptance][<?php echo $accId ?>]" style="width: 25px;margin-top: 8px;" class="weather" data-id="<?php echo $accId ?>" /> <img title="Rain"  src="<?php echo $html->url('/img/rain.png') ?>"  /></li>
							</ul>
							</div>
							<div class="col-md-5">
								<div class="progress">
									<div class="progress-bar progress-bar-striped active <?php progressClass($progress) ?>" role="progressbar" aria-valuenow="<?php echo $progress?>" aria-valuemin="0" aria-valuemax="100" style="min-width: 3em; width:<?php echo $progress?>%">
									<?php echo $progress ?> %
									</div>
								</div>
							</div>
						</div>
						<?php
						}
						?>
					</div>
				</div>
			</div>