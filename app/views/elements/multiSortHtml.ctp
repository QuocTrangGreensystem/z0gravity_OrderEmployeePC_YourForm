					<!--ADD CODE BY VINGUYEN 07/05/2014-->
                    <a href="javascript:;" id="btnCont" onclick="showHideIt();" class="btn-text">
                    	<img src="<?php echo $this->Html->url('/img/ui/blank-sort-black.png') ?>" alt="" />
                    	<span><?php __('Multiple Sort') ?></span>
                    </a>
                    <div id="boxMultiSort">
                   <select id="optionField" >
                   <option value="-1"><?php echo  __("Default", true)?></option>
                    <?php
                    $order=1;
					if($this->params['controller'] == 'activities')
					{
						foreach($activityColumn as $key=>$fc)
						{
							 if (!empty($fc['display']))
							 {
								echo '<option title="optionChoise'.$order.'" rel="'.$fc["name"].'" value="'.$key.'">'.$fc["name"].'</option>';
								$order++;
							 }

						}
					}
					else
					{
                    	$words = $this->requestAction('/translations/getByPage', array('pass' => array('KPI')));
						foreach($fieldset as $fc)
						{
							if( strpos($fc['key'], 'Project.') !== false ){
								$name = substr($fc["name"], 0, 1) == '*' ? __(substr($fc["name"], 1), true) : __d(sprintf($_domain, 'Details'), $fc["name"], true);
							} else if( strpos($fc['key'], 'ProjectAmr.') !== false && in_array($fc['name'], $words) ){
						        $name = __d(sprintf($_domain, 'KPI'), $fc['name'], true);
						    } else if( $fc['key'] == 'ProjectAmr.manual_consumed' ){
								$name = __d(sprintf($_domain, 'Project_Task'), $fc['name'], true);
							} else {
        						$name = __($fc['name'], true);
								$ff = explode('.', $fc['key']);
						        if( substr($ff[1], 0, 5) == 'sales' ){
						            $name = __d(sprintf($_domain, 'Sales'), $fc['name'], true);
						        } else if ( substr($ff[1], 0, 8) == 'internal' ) {
						            $name = __d(sprintf($_domain, 'Internal_Cost'), $fc['name'], true);
						        } else if ( substr($ff[1], 0, 8) == 'external' ) {
						            $name = __d(sprintf($_domain, 'External_Cost'), $fc['name'], true);
						        }
							}
							echo '<option title="optionChoise'.$order.'" rel="'.$name.'" value="'.$fc["key"].'">'.$name.'</option>';
							$order++;
						}
					}
                    ?>
                    </select>
                    <div id="typeSort" style="display:none">

                          <option value="1"><?php echo  __("Asc", true)?></option>
                          <option value="0"><?php echo  __("Desc", true)?></option>

                    </div>
                    <ul id="fieldsChoise"></ul><br clear="all"  />
                    <a href="javascript:;" id="onSort" class="btn btn-ok" style="margin-right:5px; "><span><?php __('Ok') ?></span></a>
                    <textarea style="display:none"  id="strMultiSort"></textarea>
                    </div>
                    <?php echo $html->script('multiSort');
					if($this->params['controller'] == 'activities'&&($this->params['action'] == 'review'||$this->params['action'] == 'manage'))
					{ echo "<style>#boxMultiSort{margin-left:-233px !important;}</style>"; } ?>
					<!--END-->
