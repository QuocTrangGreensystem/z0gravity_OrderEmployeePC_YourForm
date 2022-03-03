<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title"><?php echo __d(sprintf($_domain, 'KPI'), 'Progress', true);?></h3>
				</div>
				<div class="box-body">
					<?php 
							$pros = !empty($progression) ? $progression : 0;
							//echo __($pros . '% Progression', true);
						?>
					<div class="progress">
						<div class="progress-bar progress-bar-striped active <?php progressClass($pros) ?>" role="progressbar" aria-valuenow="<?php echo $pros?>" aria-valuemin="0" aria-valuemax="100" style="min-width: 2em;width:<?php echo $pros?>%">
							<?php echo __($pros . '% Progression', true);?>
						</div>
					</div>
					<div class="wd-table box-md-12" id="budget_db" style="width:100%;height:280px;">
					
					</div>
					<br clear="all"  />
					<?php
					foreach($dataExternals as $_external=> $_dataExternal)
					{ 
						 $pros = !empty($_dataExternal['progressExternal']) ? $_dataExternal['progressExternal'] : 0;
							//echo __($pros . '% Progression', true);
						?>
						<div class="progress">
						  <div class="progress-bar progress-bar-striped active <?php progressClass($pros) ?>" role="progressbar"
						  aria-valuenow="<?php echo $pros?>" aria-valuemin="0" aria-valuemax="100" style="min-width: 2em; width:<?php echo $pros?>%">
							<?php echo __($pros . '% Progression', true);?>
						  </div>
						</div>
						<div class="wd-table box-md-12" id="budget_external_<?php echo $_external; ?>" style="width:100%;height:280px;  ">
						
						</div>
						<br clear="all"  />
					<?php } ?>
				</div>
			</div>