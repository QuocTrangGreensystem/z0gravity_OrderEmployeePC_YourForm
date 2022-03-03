
        <?php 
			ob_clean();
            $i = 0; 
            $next_ms = '';
            $min='99999999999';
            $active_ms ='';
            $currentDate = strtotime(date('d-m-Y', time()));
			$compare_year = '';
			// ob_clean(); debug($listprojectMilestones); exit;
            foreach ($listprojectMilestones as $p) { 
                $milestone_date = strtotime($p['milestone_date']);
                $flag = abs($currentDate - $milestone_date);
                $milestone_year = new DateTime();
				$milestone_year->setTimestamp($milestone_date);
				$milestone_year = $milestone_year->format('Y');
				if( $compare_year && $milestone_year != $compare_year || $i == 0){
					$listprojectMilestones[$i]['year'] = $milestone_year;
				}
				$compare_year = $milestone_year;
                if($min > $flag && $milestone_date <= $currentDate){
                    $min = $flag;
                    $active_ms = $p['id'];
                }
				$i++;
            }
            $current_item = 0;
			$i = 0;
            foreach ($listprojectMilestones as $p) { 
                $milestone_date = strtotime($p['milestone_date']);
				
				// ob_clean(); debug($milestone_year); exit;
                $nearDate = $currentDate - $milestone_date;
                $item_class = '';
				if( !empty( $p['year']) ){
					$item_class .= ' has-year';
				}
                if( $current_item ){
                     $item_class .= ' next-item';
                     $current_item = 0;
                }
                if ($active_ms == $p['id']) {
                    $item_class .= ' active-item';
                    $current_item = 1;
                }else{
                    if($milestone_date > $currentDate){
                        $item_class .= ' last-item flag-item';
                    }
                }
                if($p['validated']){
                    $item_class .= ' milestone-validated';
                }else{
                    if ($milestone_date < $currentDate) {
                        $item_class .= ' milestone-mi milestone-red';
                    } else if($milestone_date > $currentDate) {
                        $item_class .= ' milestone-blue';
                    } else {
                        $item_class .= ' milestone-orange';
                    }
                }
                if($milestone_date < $currentDate) { $item_class .= ' out_of_date'; }
                ?>
                    <div data-num=<?php echo $i; ?> class="wd-slider-item" data-time="<?php echo $milestone_date;?>">
                        <div class="milestones-item <?php echo $item_class; ?>" data-id="<?php echo $p['id']; ?>">
                            <div class="date-milestones">
                                <span><b><?php echo date("d", strtotime($p['milestone_date'])); ?></b></span>
                                <span><?php echo __(date("M", strtotime($p['milestone_date'])),true); ?></span>
                            </div>
                            <p><?php echo $p['project_milestone']; ?></p>
							<?php if( !empty( $p['year']) ){ ?>
								<div class="milestone-year">
									<?php echo $p['year'];?> 
								</div>
							<?php } ?> 
                        </div>
                    </div>
                <?php 
				$i++;
            }
        ?>