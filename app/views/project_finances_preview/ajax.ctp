<?php
echo $this->Html->css(array(
    'slick_grid/slick.grid.activity',
    'jquery.multiSelect',
    'projects',
    'slick_grid/slick.grid_v2',
    'slick_grid/slick.pager',
    'slick_grid/slick.common_v2',
    'slick_grid/slick.edit',
    '/js/qtip/jquery.qtip',
    'dashboard/jqx.base',
    'dashboard/jqx.web',
    'preview/project_finance_index_plus'
));
echo $this->Html->script(array(
    'jquery.multiSelect',
    'slick_grid/lib/jquery-ui-1.8.16.custom.min',
    'slick_grid/lib/jquery.event.drop-2.0.min',
    'slick_grid/lib/jquery.event.drag-2.2',
    'slick_grid/slick.core',
    'slick_grid/slick.dataview',
    'slick_grid/controls/slick.pager',
    'slick_grid/slick.formatters',
    'history_filter',
    'slick_grid/plugins/slick.cellrangedecorator',
    'slick_grid/plugins/slick.cellrangeselector',
    'slick_grid/plugins/slick.cellselectionmodel',
    'slick_grid/plugins/slick.rowselectionmodel',
    'slick_grid/plugins/slick.rowmovemanager',
    'slick_grid/slick.editors',
    'slick_grid/slick.grid',
    'slick_grid_custom',
    'slick_grid/slick.grid.activity',
    'jquery.ui.touch-punch.min',
    'qtip/jquery.qtip',
	'progresspie/jquery-progresspiesvg-min',
));
$viewEuro = $bg_currency;
$user_canModified =  (!empty($canModified) || ($_isProfile && $_canWrite));
$cat = isset($this->params['url']['cat']) ? $this->params['url']['cat'] : 'investissement';//fonctionnement investissement
?>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<div class="wd-popup-content">   
    <div class="budget-row clearfix">
        <div id = "budget-chard-container" class="budget-chard-container clearfix">
            <div class="budget-chard-container-column">
                <div class="budget-chard-container-column-inner">
                    <div id="inve-chard" class="chart" style="display: <?php echo $cat == 'investissement' ? 'block' : 'none'; ?>">
                        <div class="chart-inner">
                            <div class="chard-content clearfix">
                                <?php 
								if(empty($totals['inv']['budget'])){
                                    $totals['inv']['budget'] = 0;
                                }
                                if(empty($totals['inv']['avancement'])){
                                    $totals['inv']['avancement'] = 0;
                                }
                                if($totals['inv']['budget'] == 0) {
                                    $per = 100;
                                } else {
                                    $per = round($totals['inv']['avancement']/$totals['inv']['budget'] * 100,2);
                                }
                                $color_min = '#13FF02';
                                $color_max = '#15830D';
                                if( $totals['inv']['budget'] == 0 && $totals['inv']['avancement'] == 0 ){
                                    $width_bud = '0%';
                                    $width_avan = '0';
                                    $bg_color = 'green';
                                    $per = 0;
                                } else if( $totals['inv']['budget'] == 0 ){
                                    $width_bud = '0%';
                                    $width_avan = '80';
                                    $bg_color = 'green';
                                } else if( (($totals['inv']['avancement'] > $totals['inv']['budget']) && $totals['inv']['avancement'] > 0) || (($totals['inv']['avancement'] > 0) && ($totals['inv']['budget'] <= 0)) ){
                                    $color_min = '#F98E8E';
                                    $color_max = '#FF0606';
                                    $bg_color = 'red';
                                    $width_bud = '80%';
                                    $width_avan = (abs($totals['inv']['avancement'])/abs($totals['inv']['budget'])*80);
                                } else {
                                    $width_bud = '80%';
                                    $width_avan = (abs($totals['inv']['avancement'])/abs($totals['inv']['budget'])*80);
                                    $bg_color = 'green';
                                }
                                $width_avan = $width_avan <= 100 ? $width_avan : 100;
                                $width_avan = $width_avan . '%';
                                ?>
                                <aside class="budget-progress-circle" style="overflow:visible;">
                                    <div class="progress-circle progress-circle-yellow">
                                        <div class="progress-circle-inner">
											<?php $color = ($per > 100) ? '#DB414F' : '#75AF7E';?>
											<div data-val = "<?php echo $per; ?>" id="myCanvas-inv" data-color="<?php echo $color;?>" style="width: 140px;" class="canvas-circle"></div>
                                        </div>
                                    </div>
                                </aside>
                                <div class="progress-values">
                                    <h3 class="wd-t1"><?php echo __d(sprintf($_domain, 'Finance'), 'Budget Investment', true); ?></h3>
                                    <div class ="progress-value progress-validated"><p><?php echo  __('Budget', true);?></p><span><?php echo number_format((!empty($totals['inv']['budget']) ? $totals['inv']['budget'] : 0), 2, '.', ' '); ?> <?php echo $bg_currency; ?></span></div>
                                    <div class ="progress-value progress-engaged"><p><?php echo  __('Engaged', true);?></p><span><?php echo number_format((!empty($totals['inv']['avancement']) ? $totals['inv']['avancement'] : 0), 2, '.', ' '); ?> <?php echo $bg_currency; ?></span></div>
                                </div>
                                <div id="table-control" class="dialog_no_drag">
                                    <?php
                                    echo $this->Form->create('Inv', array(
                                        'type' => 'get',
                                        'url' => array('controller' => 'project_finances_preview', 'action' => 'index_plus', $projects['Project']['id']),
                                        'data-cat' =>'investissement'
                                    ));
                                    echo $this->Form->hidden('fon_start');
                                    echo $this->Form->hidden('fon_end');
                                    ?>
                                    <fieldset>
                                        <label><?php __('From') ?></label>
                                        <div class="input" >
                                            <?php
                                                echo $this->Form->input('inv_start', array('label' => false, 'div' => false,  "class" => "placeholder", "placeholder" => __("(dd-mm-yyyy)", true), 'onchange' => "validated('inv');", 'value' => isset($invStart) ? date('d-m-Y', $invStart) : ''));
                                            ?>
                                        </div>
                                        <label> <?php __('To') ?> </label>
                                        <div id="wd-group-inv">
                                            <div class="input" id="wd-end-date-inv">
                                                <?php
                                                    echo $this->Form->input('inv_end', array('label' => false, 'div' => false,  "class" => "placeholder", "placeholder" => __("(dd-mm-yyyy)", true), 'onchange' => "validated('inv');", 'value' => isset($invEnd) ? date('d-m-Y', $invEnd) : ''));
                                                ?>
                                            </div>
                                            <p style="display: none; clear: both; padding-top: 2px; color: red;" class="wd-error wd-error-inv"><?php echo __('The end date must be greater than start date', true);?></p>
                                        </div>
                                        <div class="btn button chart-form-submit" id="wd-submit-inv">
                                            <input type="submit" value="OK" id="sutInv" />
											<svg fill="#6EAF79" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22px" height="36px"><path d="M 19.980469 5.9902344 A 1.0001 1.0001 0 0 0 19.292969 6.2929688 L 9 16.585938 L 5.7070312 13.292969 A 1.0001 1.0001 0 1 0 4.2929688 14.707031 L 8.2929688 18.707031 A 1.0001 1.0001 0 0 0 9.7070312 18.707031 L 20.707031 7.7070312 A 1.0001 1.0001 0 0 0 19.980469 5.9902344 z"></path></svg>
                                        </div>
                                        <div style="clear:both;">
                                        </div>
                                    </fieldset>
                                    <?php
                                    echo $this->Form->end();
                                    ?>
                                </div>
                            </div>
                            <div class="wd-tab-content" id="wd-tab-content">    
                            
                                <div class="wd-list-project" style="width: 100%; overflow: auto; height: 300px; overflow-x: hidden;">
                                     <?php if($user_canModified){ ?>
                                        <a href="javascript:void(0);" class="btn btn-plus-green" onclick="addNewRow('inv');" title="<?php __('Add an order') ?>">
                                            <span class='ver_line'></span>
                                            <span class='hoz_line'></span>
                                        </a>
                                    <?php } ?>
                                    
                                    <div class="wd-popup-table" id="popup_project_container_1" style="width: 1363px; max-width: calc( 100vw - 85px); height: 360px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			<?php if($cat == 'fonctionnement'){?>
				<div class="budget-chard-container-column">
					<div class="budget-chard-container-column-inner">
						<div id="fon-chard" class="chart clearfix" style="display: <?php echo $cat == 'fonctionnement' ? 'block' : 'none'; ?>">
							<div class="chart-inner">
								<div class="chard-content clearfix">
									<?php
									if(empty($totals['fon']['budget'])){
										$totals['fon']['budget'] = 0;
									}
									if(empty($totals['fon']['avancement'])){
										$totals['fon']['avancement'] = 0;
									}
									if($totals['fon']['budget'] == 0) {
										$per = 100;
									} else {
										$per = round($totals['fon']['avancement']/$totals['fon']['budget'] * 100,2);
									}
									$_color_min = '#13FF02';
									$_color_max = '#15830D';
									if( $totals['fon']['budget'] == 0 && $totals['fon']['avancement'] == 0 ){
										$width_bud = '0%';
										$width_avan = '0';
										$bg_color = 'green';
										$per = 0;
									} else if( $totals['fon']['budget'] == 0 ){
										$width_bud = '0%';
										$width_avan = '80';
										$bg_color = 'green';
									} else if( (($totals['fon']['avancement'] > $totals['fon']['budget']) && $totals['fon']['avancement'] > 0) || (($totals['fon']['avancement'] > 0) && ($totals['fon']['budget'] <= 0)) ){
										$_color_min = '#F98E8E';
										$_color_max = '#FF0606';
										$bg_color = 'red';
										$width_bud = '80%';
										$width_avan = (abs($totals['fon']['avancement'])/abs($totals['fon']['budget'])*80);
									} else {
										$width_bud = '80%';
										$width_avan = (abs($totals['fon']['avancement'])/abs($totals['fon']['budget'])*80);
										$bg_color = 'green';
									}
									$width_avan = $width_avan <= 100 ? $width_avan : 100;
									$width_avan = $width_avan . '%';
									?>
									<aside class="budget-progress-circle" style="overflow:visible;">
										<div class="progress-circle progress-circle-yellow">
											<div class="progress-circle-inner">
												<?php $color = ($per > 100) ? '#DB414F' : '#75AF7E';?>
												<div data-val = "<?php echo $per; ?>" id="myCanvas-fon" data-color="<?php echo $color;?>" style="width: 140px;" class="canvas-circle"></div>
											</div>
										</div>
									</aside>
									<div class="progress-values">
										<h3 class="wd-t1"><?php echo __d(sprintf($_domain, 'Finance'), 'Budget Operation', true); ?></h3>
										<div class ="progress-value progress-validated"><p><?php echo  __('Budget', true);?></p><span><?php echo number_format((!empty($totals['fon']['budget']) ? $totals['fon']['budget'] : 0), 2, '.', ' '); ?> <?php echo $bg_currency; ?></span></div>
										<div class ="progress-value progress-engaged"><p><?php echo  __('Engaged', true);?></p><span><?php echo number_format((!empty($totals['fon']['avancement']) ? $totals['fon']['avancement'] : 0), 2, '.', ' '); ?> <?php echo $bg_currency; ?></span></div>
									</div>
								</div>
								<div id="table-control" class="dialog_no_drag">
									<?php
									echo $this->Form->create('Fon', array(
											'type' => 'POST',
											'url' => array('controller' => 'project_finances_preview', 'action' => 'index_plus', $projects['Project']['id']),
											'data-cat' =>'fonctionnement'
										));
									echo $this->Form->hidden('inv_start');
									echo $this->Form->hidden('inv_end');
									?>
									<fieldset>
										<label><?php __('From') ?></label>
										<div class="input" >
											<?php
												echo $this->Form->input('fon_start', array('label' => false, 'div' => false,  "class" => "placeholder", "placeholder" => __("(dd-mm-yyyy)", true), 'onchange' => "f_popup_validated('fon');", 'value' => isset($fonStart) ? date('d-m-Y', $fonStart) : ''));
											?>
										</div>
										<label> <?php __('To') ?> </label>
										<div id="wd-group-fon">
											<div class="input" id="wd-end-date-fon">
												<?php
													echo $this->Form->input('fon_end', array('label' => false, 'div' => false,  "class" => "placeholder", "placeholder" => __("(dd-mm-yyyy)", true), 'onchange' => "f_popup_validated('fon');", 'value' => isset($fonEnd) ? date('d-m-Y', $fonEnd) : ''));
												?>
											</div>
											<p style="display: none; clear: both; padding-top: 2px; color: red;" class="wd-error wd-error-fon"><?php echo __('The end date must be greater than start date', true);?></p>
										</div>
										<div class="btn button chart-form-submit" id="wd-submit-fon">
											<input type="submit" value="OK" id="sutFon" />
											<svg fill="#6EAF79" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22px" height="36px"><path d="M 19.980469 5.9902344 A 1.0001 1.0001 0 0 0 19.292969 6.2929688 L 9 16.585938 L 5.7070312 13.292969 A 1.0001 1.0001 0 1 0 4.2929688 14.707031 L 8.2929688 18.707031 A 1.0001 1.0001 0 0 0 9.7070312 18.707031 L 20.707031 7.7070312 A 1.0001 1.0001 0 0 0 19.980469 5.9902344 z"></path></svg>
										</div>
										<div style="clear:both;">
											
										</div>
									</fieldset>
									<?php
									echo $this->Form->end();
									?>
									
								</div>
								<div class="wd-tab-content" id="wd-tab-content"> 
									<div class="wd-list-project" style="width: 100%;  overflow: auto; height: 300px; overflow-x: hidden;">
										<?php if($user_canModified){ ?>
											<a href="javascript:void(0);" class="btn btn-plus-green" onclick="addNewRow('fon');" title="<?php __('Add an order') ?>">    <span class='ver_line'></span>
												<span class='hoz_line'></span>
											</a>      
										<?php } ?>                   
										<div class="wd-popup-table" id="popup_project_container_2" style="width: 1363px; max-width: calc( 100vw - 85px); height: 360px;">   
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>  
				</div>
			<?php }elseif($cat == 'finan_investissement'){?>
				<div class="budget-chard-container-column">
					<div class="budget-chard-container-column-inner">
						<div id="fon-chard" class="chart clearfix" style="display: <?php echo $cat == 'finan_investissement' ? 'block' : 'none'; ?>">
							<div class="chart-inner">
								<div class="chard-content clearfix">
									<?php
									if(empty($totals['finaninv']['budget'])){
										$totals['finaninv']['budget'] = 0;
									}
									if(empty($totals['finaninv']['avancement'])){
										$totals['finaninv']['avancement'] = 0;
									}
									if($totals['finaninv']['budget'] == 0) {
										$per = 100;
									} else {
										$per = round($totals['finaninv']['avancement']/$totals['finaninv']['budget'] * 100,2);
									}
									$_color_min = '#13FF02';
									$_color_max = '#15830D';
									if( $totals['finaninv']['budget'] == 0 && $totals['finaninv']['avancement'] == 0 ){
										$width_bud = '0%';
										$width_avan = '0';
										$bg_color = 'green';
										$per = 0;
									} else if( $totals['finaninv']['budget'] == 0 ){
										$width_bud = '0%';
										$width_avan = '80';
										$bg_color = 'green';
									} else if( (($totals['finaninv']['avancement'] > $totals['finaninv']['budget']) && $totals['finaninv']['avancement'] > 0) || (($totals['finaninv']['avancement'] > 0) && ($totals['finaninv']['budget'] <= 0)) ){
										$_color_min = '#F98E8E';
										$_color_max = '#FF0606';
										$bg_color = 'red';
										$width_bud = '80%';
										$width_avan = (abs($totals['finaninv']['avancement'])/abs($totals['finaninv']['budget'])*80);
									} else {
										$width_bud = '80%';
										$width_avan = (abs($totals['finaninv']['avancement'])/abs($totals['finaninv']['budget'])*80);
										$bg_color = 'green';
									}
									$width_avan = $width_avan <= 100 ? $width_avan : 100;
									$width_avan = $width_avan . '%';
									?>
									<aside class="budget-progress-circle" style="overflow:visible;">
										<div class="progress-circle progress-circle-yellow">
											<div class="progress-circle-inner">
												<?php $color = ($per > 100) ? '#DB414F' : '#75AF7E';?>
												<div data-val = "<?php echo $per; ?>" id="myCanvas-finaninv" data-color="<?php echo $color;?>" style="width: 140px;" class="canvas-circle"></div>
											</div>
										</div>
									</aside>
									<div class="progress-values">
										<h3 class="wd-t1"><?php echo __d(sprintf($_domain, 'Finance_Investment'), 'Finance Investment', true); ?></h3>
										<div class ="progress-value progress-validated"><p><?php echo  __('Budget', true);?></p><span><?php echo number_format((!empty($totals['finaninv']['budget']) ? $totals['finaninv']['budget'] : 0), 2, '.', ' '); ?> <?php echo $bg_currency; ?></span></div>
										<div class ="progress-value progress-engaged"><p><?php echo  __('Engaged', true);?></p><span><?php echo number_format((!empty($totals['finaninv']['avancement']) ? $totals['finaninv']['avancement'] : 0), 2, '.', ' '); ?> <?php echo $bg_currency; ?></span></div>
									</div>
								</div>
								<div id="table-control" class="dialog_no_drag">
									<?php
									echo $this->Form->create('Fon', array(
											'type' => 'POST',
											'url' => array('controller' => 'project_finances_preview', 'action' => 'index_plus', $projects['Project']['id']),
											'data-cat' =>'fonctionnement'
										));
									echo $this->Form->hidden('inv_start');
									echo $this->Form->hidden('inv_end');
									?>
									<fieldset>
										<label><?php __('From') ?></label>
										<div class="input" >
											<?php
												echo $this->Form->input('fon_start', array('label' => false, 'div' => false,  "class" => "placeholder", "placeholder" => __("(dd-mm-yyyy)", true), 'onchange' => "f_popup_validated('fon');", 'value' => isset($finanInvStart) ? date('d-m-Y', $finanInvStart) : ''));
											?>
										</div>
										<label> <?php __('To') ?> </label>
										<div id="wd-group-fon">
											<div class="input" id="wd-end-date-fon">
												<?php
													echo $this->Form->input('fon_end', array('label' => false, 'div' => false,  "class" => "placeholder", "placeholder" => __("(dd-mm-yyyy)", true), 'onchange' => "f_popup_validated('fon');", 'value' => isset($finanInvEnd) ? date('d-m-Y', $finanInvEnd) : ''));
												?>
											</div>
											<p style="display: none; clear: both; padding-top: 2px; color: red;" class="wd-error wd-error-fon"><?php echo __('The end date must be greater than start date', true);?></p>
										</div>
										<div class="btn button chart-form-submit" id="wd-submit-fon">
											<input type="submit" value="OK" id="sutFon" />
											<svg fill="#6EAF79" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22px" height="36px"><path d="M 19.980469 5.9902344 A 1.0001 1.0001 0 0 0 19.292969 6.2929688 L 9 16.585938 L 5.7070312 13.292969 A 1.0001 1.0001 0 1 0 4.2929688 14.707031 L 8.2929688 18.707031 A 1.0001 1.0001 0 0 0 9.7070312 18.707031 L 20.707031 7.7070312 A 1.0001 1.0001 0 0 0 19.980469 5.9902344 z"></path></svg>
										</div>
										<div style="clear:both;">
											
										</div>
									</fieldset>
									<?php
									echo $this->Form->end();
									?>
									
								</div>
								<div class="wd-tab-content" id="wd-tab-content"> 
									<div class="wd-list-project" style="width: 100%;  overflow: auto; height: 300px; overflow-x: hidden;">
										<?php if($user_canModified){ ?>
											<a href="javascript:void(0);" class="btn btn-plus-green" onclick="addNewRow('fon');" title="<?php __('Add an order') ?>">    <span class='ver_line'></span>
												<span class='hoz_line'></span>
											</a>      
										<?php } ?>                   
										<div class="wd-popup-table" id="popup_project_container_2" style="width: 1363px; max-width: calc( 100vw - 85px); height: 360px;">   
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>  
				</div>
			<?php }else{?>
				<div class="budget-chard-container-column">
					<div class="budget-chard-container-column-inner">
						<div id="fon-chard" class="chart clearfix" style="display: <?php echo $cat == 'finan_fonctionnement' ? 'block' : 'none'; ?>">
							<div class="chart-inner">
								<div class="chard-content clearfix">
									<?php
									if(empty($totals['finanfon']['budget'])){
										$totals['finanfon']['budget'] = 0;
									}
									if(empty($totals['finanfon']['avancement'])){
										$totals['finanfon']['avancement'] = 0;
									}
									if($totals['finanfon']['budget'] == 0) {
										$per = 100;
									} else {
										$per = round($totals['finanfon']['avancement']/$totals['finanfon']['budget'] * 100,2);
									}
									$_color_min = '#13FF02';
									$_color_max = '#15830D';
									if( $totals['finanfon']['budget'] == 0 && $totals['finanfon']['avancement'] == 0 ){
										$width_bud = '0%';
										$width_avan = '0';
										$bg_color = 'green';
										$per = 0;
									} else if( $totals['finanfon']['budget'] == 0 ){
										$width_bud = '0%';
										$width_avan = '80';
										$bg_color = 'green';
									} else if( (($totals['finanfon']['avancement'] > $totals['finanfon']['budget']) && $totals['finanfon']['avancement'] > 0) || (($totals['finanfon']['avancement'] > 0) && ($totals['finanfon']['budget'] <= 0)) ){
										$_color_min = '#F98E8E';
										$_color_max = '#FF0606';
										$bg_color = 'red';
										$width_bud = '80%';
										$width_avan = (abs($totals['finanfon']['avancement'])/abs($totals['finanfon']['budget'])*80);
									} else {
										$width_bud = '80%';
										$width_avan = (abs($totals['finanfon']['avancement'])/abs($totals['finanfon']['budget'])*80);
										$bg_color = 'green';
									}
									$width_avan = $width_avan <= 100 ? $width_avan : 100;
									$width_avan = $width_avan . '%';
									?>
									<aside class="budget-progress-circle" style="overflow:visible;">
										<div class="progress-circle progress-circle-yellow">
											<div class="progress-circle-inner">
												<?php $color = ($per > 100) ? '#DB414F' : '#75AF7E';?>
												<div data-val = "<?php echo $per; ?>" id="myCanvas-finanfon" data-color="<?php echo $color;?>" style="width: 140px;" class="canvas-circle"></div>
											</div>
										</div>
									</aside>
									<div class="progress-values">
										<h3 class="wd-t1"><?php echo __d(sprintf($_domain, 'Finance_Operation'), 'Finance Operation', true); ?></h3>
										<div class ="progress-value progress-validated"><p><?php echo  __('Budget', true);?></p><span><?php echo number_format((!empty($totals['finanfon']['budget']) ? $totals['finanfon']['budget'] : 0), 2, '.', ' '); ?> <?php echo $bg_currency; ?></span></div>
										<div class ="progress-value progress-engaged"><p><?php echo  __('Engaged', true);?></p><span><?php echo number_format((!empty($totals['finanfon']['avancement']) ? $totals['finanfon']['avancement'] : 0), 2, '.', ' '); ?> <?php echo $bg_currency; ?></span></div>
									</div>
								</div>
								<div id="table-control" class="dialog_no_drag">
									<?php
									echo $this->Form->create('Finanfon', array(
											'type' => 'POST',
											'url' => array('controller' => 'project_finances_preview', 'action' => 'index_plus', $projects['Project']['id']),
											'data-cat' =>'finan_investissement'
										));
									echo $this->Form->hidden('finanfon_start');
									echo $this->Form->hidden('finanfon_end');
									?>
									<fieldset>
										<label><?php __('From') ?></label>
										<div class="input" >
											<?php
												echo $this->Form->input('finanfon_start', array('label' => false, 'div' => false,  "class" => "placeholder", "placeholder" => __("(dd-mm-yyyy)", true), 'onchange' => "f_popup_validated('fon');", 'value' => isset($finanFonStart) ? date('d-m-Y', $finanFonStart) : ''));
											?>
										</div>
										<label> <?php __('To') ?> </label>
										<div id="wd-group-fon">
											<div class="input" id="wd-end-date-fon">
												<?php
													echo $this->Form->input('finanfon_end', array('label' => false, 'div' => false,  "class" => "placeholder", "placeholder" => __("(dd-mm-yyyy)", true), 'onchange' => "f_popup_validated('fon');", 'value' => isset($finanFonEnd) ? date('d-m-Y', $finanFonEnd) : ''));
												?>
											</div>
											<p style="display: none; clear: both; padding-top: 2px; color: red;" class="wd-error wd-error-fon"><?php echo __('The end date must be greater than start date', true);?></p>
										</div>
										<div class="btn button chart-form-submit" id="wd-submit-fon">
											<input type="submit" value="OK" id="sutFon" />
											<svg fill="#6EAF79" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22px" height="36px"><path d="M 19.980469 5.9902344 A 1.0001 1.0001 0 0 0 19.292969 6.2929688 L 9 16.585938 L 5.7070312 13.292969 A 1.0001 1.0001 0 1 0 4.2929688 14.707031 L 8.2929688 18.707031 A 1.0001 1.0001 0 0 0 9.7070312 18.707031 L 20.707031 7.7070312 A 1.0001 1.0001 0 0 0 19.980469 5.9902344 z"></path></svg>
										</div>
										<div style="clear:both;">
											
										</div>
									</fieldset>
									<?php
									echo $this->Form->end();
									?>
									
								</div>
								<div class="wd-tab-content" id="wd-tab-content"> 
									<div class="wd-list-project" style="width: 100%;  overflow: auto; height: 300px; overflow-x: hidden;">
										<?php if($user_canModified){ ?>
											<a href="javascript:void(0);" class="btn btn-plus-green" onclick="addNewRow('fon');" title="<?php __('Add an order') ?>">    <span class='ver_line'></span>
												<span class='hoz_line'></span>
											</a>      
										<?php } ?>                   
										<div class="wd-popup-table" id="popup_project_container_2" style="width: 1363px; max-width: calc( 100vw - 85px); height: 360px;">   
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>  
				</div>
			<?php } ?>
        </div>
    </div>
</div>        
            
<?php
function jsonParseOptions($options, $safeKeys = array()) {
    $output = array();
    $safeKeys = array_flip($safeKeys);
    foreach ($options as $option) {
        $out = array();
        foreach ($option as $key => $value) {
            if (!is_int($value) && !isset($safeKeys[$key])) {
                $value = json_encode($value);
            }
            $out[] = $key . ':' . $value;
        }
        $output[] = implode(', ', $out);
    }
    return '[{' . implode('},{ ', $output) . '}]';
}
$columns_1 = array(
    array(
        'id' => 'inv_no.',
        'field' => 'inv_no.',
        'name' => '',
        'width' => 40,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1
    ),
    array(
        'id' => 'inv_name',
        'field' => 'inv_name',
        'name' => "",
        'width' => isset($history['columnWidth1']['inv_name']) ? (int) $history['columnWidth1']['inv_name'] : 120,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'inv_date',
        'field' => 'inv_date',
        'name' => __('Date', true),
        'width' => isset($history['columnWidth2']['inv_date']) ? (int) $history['columnWidth2']['inv_date'] : 100,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.datePicker',
        'datatype' => 'datetime',
        'formatter' => 'Slick.Formatters.DateTime',
    ),
    array(
        'id' => 'inv_budget',
        'field' => 'inv_budget',
        'name' => __d(sprintf($_domain, 'Finance'), 'Budget', true),
        'width' => isset($history['columnWidth1']['inv_budget']) ? (int) $history['columnWidth1']['inv_budget'] : 120,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.manDayValue'
    ),
    array(
        'id' => 'inv_avancement',
        'field' => 'inv_avancement',
        'name' => __d(sprintf($_domain, 'Finance'), 'Avancement', true),
        'width' => isset($history['columnWidth1']['inv_avancement']) ? (int) $history['columnWidth1']['inv_avancement'] : 120,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.manDayValue'
    ),
    array(
        'id' => 'inv_percent',
        'field' => 'inv_percent',
        'name' => __('%', true),
        'width' => isset($history['columnWidth1']['inv_percent']) ? (int) $history['columnWidth1']['inv_percent'] : 80,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.percentValue'
    ));
$columnInvYears = array();
$invStYear = date('Y', $invStart);
$invEnYear = date('Y', $invEnd);
while($invStYear <= $invEnYear){
    $columnInvYears[] = array(
        'id' => 'inv_budget_' . $invStYear,
        'field' => 'inv_budget_' . $invStYear,
        'name' => __d(sprintf($_domain, 'Finance'), 'Budget', true),
        'width' => isset($history['columnWidth1']['inv_budget_' . $invStYear]) ? (int) $history['columnWidth1']['inv_budget_' . $invStYear] : 120,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericValue',
        'formatter' => 'Slick.Formatters.manDayValue'
    );
    $columnInvYears[] = array(
        'id' => 'inv_avancement_' . $invStYear,
        'field' => 'inv_avancement_' . $invStYear,
        'name' => __d(sprintf($_domain, 'Finance'), 'Avancement', true),
        'width' => isset($history['columnWidth1']['inv_avancement_' . $invStYear]) ? (int) $history['columnWidth1']['inv_avancement_' . $invStYear] : 120,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericValue',
        'formatter' => 'Slick.Formatters.manDayValue'
    );
    $columnInvYears[] = array(
        'id' => 'inv_percent_' . $invStYear,
        'field' => 'inv_percent_' . $invStYear,
        'name' => __('%', true),
        'width' => isset($history['columnWidth1']['inv_percent_' . $invStYear]) ? (int) $history['columnWidth1']['inv_percent_' . $invStYear] : 80,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.percentValue'
    );
    $invStYear++;
}
$columnInvAction[] = array(
    'id' => 'inv_action.',
    'field' => 'inv_action.',
    'name' => __('Action', true),
    'width' => 0,
    'minWidth' => 0,
    'sortable' => false,
    'resizable' => true,
    'noFilter' => 1,
    'cssClass' => 'row_action_del',
    'formatter' => 'Slick.Formatters.Action'
);
$columns_1 = array_merge($columns_1, $columnInvYears, $columnInvAction);
if($cat == 'fonctionnement'){
	$columns_2 = array(
		array(
			'id' => 'fon_no.',
			'field' => 'fon_no.',
			'name' => '',
			'width' => 40,
			'sortable' => false,
			'resizable' => false,
			'noFilter' => 1
		),
		array(
			'id' => 'fon_name',
			'field' => 'fon_name',
			'name' => "",
			'width' => isset($history['columnWidth2']['fon_name']) ? (int) $history['columnWidth2']['fon_name'] : 120,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'editor' => 'Slick.Editors.textBox'
		),
		array(
			'id' => 'fon_date',
			'field' => 'fon_date',
			'name' => __('Date', true),
			'width' => isset($history['columnWidth2']['fon_date']) ? (int) $history['columnWidth2']['fon_date'] : 100,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'editor' => 'Slick.Editors.datePicker',
			'datatype' => 'datetime',
			'formatter' => 'Slick.Formatters.DateTime',
		),
		array(
			'id' => 'fon_budget',
			'field' => 'fon_budget',
			'name' => __d(sprintf($_domain, 'Finance'), 'Budget', true),
			'width' => isset($history['columnWidth2']['fon_budget']) ? (int) $history['columnWidth2']['fon_budget'] : 120,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'formatter' => 'Slick.Formatters.manDayValue'
		),
		array(
			'id' => 'fon_avancement',
			'field' => 'fon_avancement',
			'name' => __d(sprintf($_domain, 'Finance'), 'Avancement', true),
			'width' => isset($history['columnWidth2']['fon_avancement']) ? (int) $history['columnWidth2']['fon_avancement'] : 120,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'formatter' => 'Slick.Formatters.manDayValue'
		),
		array(
			'id' => 'fon_percent',
			'field' => 'fon_percent',
			'name' => __('%', true),
			'width' => isset($history['columnWidth2']['fon_percent']) ? (int) $history['columnWidth2']['fon_percent'] : 80,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'formatter' => 'Slick.Formatters.percentValue'
		));
	$columnFonYears = array();
	$fonStYear = date('Y', $fonStart);
	$fonEnYear = date('Y', $fonEnd);
	while($fonStYear <= $fonEnYear){
		$columnFonYears[] = array(
			'id' => 'fon_budget_' . $fonStYear,
			'field' => 'fon_budget_' . $fonStYear,
			'name' => __d(sprintf($_domain, 'Finance'), 'Budget', true),
			'width' => isset($history['columnWidth2']['fon_budget_' . $fonStYear]) ? (int) $history['columnWidth2']['fon_budget_' . $fonStYear] : 120,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'editor' => 'Slick.Editors.numericValue',
			'formatter' => 'Slick.Formatters.manDayValue'
		);
		$columnFonYears[] = array(
			'id' => 'fon_avancement_' . $fonStYear,
			'field' => 'fon_avancement_' . $fonStYear,
			'name' => __d(sprintf($_domain, 'Finance'), 'Avancement', true),
			'width' => isset($history['columnWidth2']['fon_avancement_' . $fonStYear]) ? (int) $history['columnWidth2']['fon_avancement_' . $fonStYear] : 120,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'editor' => 'Slick.Editors.numericValue',
			'formatter' => 'Slick.Formatters.manDayValue'
		);
		$columnFonYears[] = array(
			'id' => 'fon_percent_' . $fonStYear,
			'field' => 'fon_percent_' . $fonStYear,
			'name' => __('%', true),
			'width' => isset($history['columnWidth2']['fon_percent_' . $fonStYear]) ? (int) $history['columnWidth2']['fon_percent_' . $fonStYear] : 80,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'formatter' => 'Slick.Formatters.percentValue'
		);
		$fonStYear++;
	}
	$columnFonAction[] = array(
		'id' => 'fon_action.',
		'field' => 'fon_action.',
		'name' => __('Action', true),
		'width' => 70,
		'sortable' => false,
		'resizable' => true,
		'noFilter' => 1,
		'cssClass' => 'row_action_del',
		'formatter' => 'Slick.Formatters.Action'
	);
	$columns_2 = array_merge($columns_2, $columnFonYears, $columnFonAction);
}elseif($cat == 'finan_investissement'){
	$columns_2 = array(
		array(
			'id' => 'fon_no.',
			'field' => 'fon_no.',
			'name' => '',
			'width' => 40,
			'sortable' => false,
			'resizable' => false,
			'noFilter' => 1
		),
		array(
			'id' => 'fon_name',
			'field' => 'fon_name',
			'name' => "",
			'width' => isset($history['columnWidth2']['fon_name']) ? (int) $history['columnWidth2']['fon_name'] : 120,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'editor' => 'Slick.Editors.textBox'
		),
		array(
			'id' => 'fon_date',
			'field' => 'fon_date',
			'name' => __('Date', true),
			'width' => isset($history['columnWidth2']['fon_date']) ? (int) $history['columnWidth2']['fon_date'] : 100,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'editor' => 'Slick.Editors.datePicker',
			'datatype' => 'datetime',
			'formatter' => 'Slick.Formatters.DateTime',
		),
		array(
			'id' => 'fon_budget',
			'field' => 'fon_budget',
			'name' => __d(sprintf($_domain, 'Finance'), 'Budget', true),
			'width' => isset($history['columnWidth2']['fon_budget']) ? (int) $history['columnWidth2']['fon_budget'] : 120,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'formatter' => 'Slick.Formatters.manDayValue'
		),
		array(
			'id' => 'fon_avancement',
			'field' => 'fon_avancement',
			'name' => __d(sprintf($_domain, 'Finance'), 'Avancement', true),
			'width' => isset($history['columnWidth2']['fon_avancement']) ? (int) $history['columnWidth2']['fon_avancement'] : 120,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'formatter' => 'Slick.Formatters.manDayValue'
		),
		array(
			'id' => 'fon_percent',
			'field' => 'fon_percent',
			'name' => __('%', true),
			'width' => isset($history['columnWidth2']['fon_percent']) ? (int) $history['columnWidth2']['fon_percent'] : 80,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'formatter' => 'Slick.Formatters.percentValue'
		));
	$columnFonYears = array();
	$finanInvStYear = date('Y', $finanInvStart);
	$finanInvEnYear = date('Y', $finanInvEnd);
	while($finanInvStYear <= $finanInvEnYear){
		$columnFonYears[] = array(
			'id' => 'fon_budget_' . $finanInvStYear,
			'field' => 'fon_budget_' . $finanInvStYear,
			'name' => __d(sprintf($_domain, 'Finance'), 'Budget', true),
			'width' => isset($history['columnWidth2']['fon_budget_' . $finanInvStYear]) ? (int) $history['columnWidth2']['fon_budget_' . $finanInvStYear] : 120,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'editor' => 'Slick.Editors.numericValue',
			'formatter' => 'Slick.Formatters.manDayValue'
		);
		$columnFonYears[] = array(
			'id' => 'fon_avancement_' . $finanInvStYear,
			'field' => 'fon_avancement_' . $finanInvStYear,
			'name' => __d(sprintf($_domain, 'Finance'), 'Avancement', true),
			'width' => isset($history['columnWidth2']['fon_avancement_' . $finanInvStYear]) ? (int) $history['columnWidth2']['fon_avancement_' . $finanInvStYear] : 120,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'editor' => 'Slick.Editors.numericValue',
			'formatter' => 'Slick.Formatters.manDayValue'
		);
		$columnFonYears[] = array(
			'id' => 'fon_percent_' . $finanInvStYear,
			'field' => 'fon_percent_' . $finanInvStYear,
			'name' => __('%', true),
			'width' => isset($history['columnWidth2']['fon_percent_' . $finanInvStYear]) ? (int) $history['columnWidth2']['fon_percent_' . $finanInvStYear] : 80,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'formatter' => 'Slick.Formatters.percentValue'
		);
		$finanInvStYear++;
	}
	$columnFonAction[] = array(
		'id' => 'fon_action.',
		'field' => 'fon_action.',
		'name' => __('Action', true),
		'width' => 70,
		'sortable' => false,
		'resizable' => true,
		'noFilter' => 1,
		'cssClass' => 'row_action_del',
		'formatter' => 'Slick.Formatters.Action'
	);
	$columns_2 = array_merge($columns_2, $columnFonYears, $columnFonAction);
}else{
	$columns_2 = array(
		array(
			'id' => 'fon_no.',
			'field' => 'fon_no.',
			'name' => '',
			'width' => 40,
			'sortable' => false,
			'resizable' => false,
			'noFilter' => 1
		),
		array(
			'id' => 'fon_name',
			'field' => 'fon_name',
			'name' => "",
			'width' => isset($history['columnWidth2']['fon_name']) ? (int) $history['columnWidth2']['fon_name'] : 120,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'editor' => 'Slick.Editors.textBox'
		),
		array(
			'id' => 'fon_date',
			'field' => 'fon_date',
			'name' => __('Date', true),
			'width' => isset($history['columnWidth2']['fon_date']) ? (int) $history['columnWidth2']['fon_date'] : 100,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'editor' => 'Slick.Editors.datePicker',
			'datatype' => 'datetime',
			'formatter' => 'Slick.Formatters.DateTime',
		),
		array(
			'id' => 'fon_budget',
			'field' => 'fon_budget',
			'name' => __d(sprintf($_domain, 'Finance'), 'Budget', true),
			'width' => isset($history['columnWidth2']['fon_budget']) ? (int) $history['columnWidth2']['fon_budget'] : 120,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'formatter' => 'Slick.Formatters.manDayValue'
		),
		array(
			'id' => 'fon_avancement',
			'field' => 'fon_avancement',
			'name' => __d(sprintf($_domain, 'Finance'), 'Avancement', true),
			'width' => isset($history['columnWidth2']['fon_avancement']) ? (int) $history['columnWidth2']['fon_avancement'] : 120,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'formatter' => 'Slick.Formatters.manDayValue'
		),
		array(
			'id' => 'fon_percent',
			'field' => 'fon_percent',
			'name' => __('%', true),
			'width' => isset($history['columnWidth2']['fon_percent']) ? (int) $history['columnWidth2']['fon_percent'] : 80,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'formatter' => 'Slick.Formatters.percentValue'
		));
	$columnFonYears = array();
	$finanFonStYear = date('Y', $finanFonStart);
	$finanFonYear = date('Y', $finanFonEnd);
	while($finanFonStYear <= $finanFonYear){
		$columnFonYears[] = array(
			'id' => 'fon_budget_' . $finanFonStYear,
			'field' => 'fon_budget_' . $finanFonStYear,
			'name' => __d(sprintf($_domain, 'Finance'), 'Budget', true),
			'width' => isset($history['columnWidth2']['fon_budget_' . $finanFonStYear]) ? (int) $history['columnWidth2']['fon_budget_' . $finanFonStYear] : 120,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'editor' => 'Slick.Editors.numericValue',
			'formatter' => 'Slick.Formatters.manDayValue'
		);
		$columnFonYears[] = array(
			'id' => 'fon_avancement_' . $finanFonStYear,
			'field' => 'fon_avancement_' . $finanFonStYear,
			'name' => __d(sprintf($_domain, 'Finance'), 'Avancement', true),
			'width' => isset($history['columnWidth2']['fon_avancement_' . $finanFonStYear]) ? (int) $history['columnWidth2']['fon_avancement_' . $finanFonStYear] : 120,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'editor' => 'Slick.Editors.numericValue',
			'formatter' => 'Slick.Formatters.manDayValue'
		);
		$columnFonYears[] = array(
			'id' => 'fon_percent_' . $finanFonStYear,
			'field' => 'fon_percent_' . $finanFonStYear,
			'name' => __('%', true),
			'width' => isset($history['columnWidth2']['fon_percent_' . $finanFonStYear]) ? (int) $history['columnWidth2']['fon_percent_' . $finanFonStYear] : 80,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'formatter' => 'Slick.Formatters.percentValue'
		);
		$finanFonStYear++;
	}
	$columnFonAction[] = array(
		'id' => 'fon_action.',
		'field' => 'fon_action.',
		'name' => __('Action', true),
		'width' => 70,
		'sortable' => false,
		'resizable' => true,
		'noFilter' => 1,
		'cssClass' => 'row_action_del',
		'formatter' => 'Slick.Formatters.Action'
	);
	$columns_2 = array_merge($columns_2, $columnFonYears, $columnFonAction);
}
$i = 1;
$dataView_1 = $dataView_2 = $totalHeader_1 = $totalHeader_2 = $calPercent_1 = $calPercent_2 = array();
if(!empty($finances['inv'])){
	foreach($finances['inv'] as $id => $finance){
        $data = array(
            'id' => $id,
            'inv_no.' => $i++,
            'MetaData' => array()
        );
        $data['project_id'] = $projects['Project']['id'];
        $data['activity_id'] = $projects['Project']['activity_id'];
        $data['company_id'] = $projects['Project']['company_id'];
        $data['inv_name'] = (string) $finance['name'];
        $data['inv_date'] = (string) $finance['finance_date'];
        $totalBudget = $totalAvancement = $totalPercent = 0;
        $percentYears = array();
        if(!empty($financeDetails[$id])){
            foreach($financeDetails[$id] as $model => $invs){
                if(!isset($totalHeader_1['inv_' . $model])){
                    $totalHeader_1['inv_' . $model] = 0;
                }
                $totalHeader_1['inv_' . $model] += $invs['value'];
                $data['inv_' . $model] = $invs['value'];
                if(!isset($percentYears[$invs['year']][$invs['model']])){
                    $percentYears[$invs['year']][$invs['model']] = 0;
                }
                $percentYears[$invs['year']][$invs['model']] += $invs['value'];
                if(!isset($calPercent_1[$invs['year']][$invs['model']])){
                    $calPercent_1[$invs['year']][$invs['model']] = 0;
                }
                $calPercent_1[$invs['year']][$invs['model']] += $invs['value'];
                if($invs['model'] == 'budget'){
                    $totalBudget += $invs['value'];
                } else {
                    $totalAvancement += $invs['value'];
                }
            }
        }
        if(!empty($percentYears)){
            foreach($percentYears as $year => $percentYear){
                $bud = !empty($percentYear['budget']) ? $percentYear['budget'] : 0;
                $ava = !empty($percentYear['avancement']) ? $percentYear['avancement'] : 0;
                $per = ($bud ==0 ) ? 0 : round($ava/$bud*100, 2);
                $data['inv_percent_' . $year] = $per;
            }
        }
        $totalPercent = ($totalBudget == 0) ? 0 : $totalAvancement/$totalBudget*100;
        if(!isset($totalHeader_1['inv_budget'])){
            $totalHeader_1['inv_budget'] = 0;
        }
        $totalHeader_1['inv_budget'] += $totalBudget;

        if(!isset($totalHeader_1['inv_avancement'])){
            $totalHeader_1['inv_avancement'] = 0;
        }
        $totalHeader_1['inv_avancement'] += $totalAvancement;
        if(!isset($calPercent_1['total']['budget'])){
            $calPercent_1['total']['budget'] = 0;
        }
        $calPercent_1['total']['budget'] += $totalBudget;
        if(!isset($calPercent_1['total']['avancement'])){
            $calPercent_1['total']['avancement'] = 0;
        }
        $calPercent_1['total']['avancement'] += $totalAvancement;

        $data['inv_percent'] = round($totalPercent, 2);
        $data['inv_budget'] = round($totalBudget, 2);
        $data['inv_avancement'] = round($totalAvancement, 2);
        $data['inv_action.'] = '';

        $dataView_1[] = $data;
    }
}
if(!empty($calPercent_1)){
    foreach($calPercent_1 as $key => $val){
        $bud = isset($val['budget']) ? $val['budget'] : 0 ;
        $ava = isset($val['avancement']) ? $val['avancement'] : 0;
        $per = ($bud ==0 ) ? 0 : round($ava/$bud*100, 2);
        if($key == 'total'){
            $totalHeader_1['inv_percent'] = $per;
        } else {
            $totalHeader_1['inv_percent_' . $key] = $per;
        }
    }
}

$j = 1;
if($cat == 'fonctionnement'){
	if(!empty($finances['fon'])){
		foreach($finances['fon'] as $id => $finance){
			$data = array(
				'id' => $id,
				'fon_no.' => $j++,
				'MetaData' => array()
			);
			$data['fon_name'] = (string) $finance['name'];
			$data['fon_date'] = (string) $finance['finance_date'];
			$data['project_id'] = $projects['Project']['id'];
			$data['activity_id'] = $projects['Project']['activity_id'];
			$data['company_id'] = $projects['Project']['company_id'];
			$totalBudget = $totalAvancement = $totalPercent = 0;
			$percentYears = array();
			if(!empty($financeDetails[$id])){
				foreach($financeDetails[$id] as $model => $fons){
					if(!isset($totalHeader_2['fon_' . $model])){
						$totalHeader_2['fon_' . $model] = 0;
					}
					$totalHeader_2['fon_' . $model] += $fons['value'];
					$data['fon_' . $model] = $fons['value'];
					if(!isset($percentYears[$fons['year']][$fons['model']])){
						$percentYears[$fons['year']][$fons['model']] = 0;
					}
					$percentYears[$fons['year']][$fons['model']] += $fons['value'];
					if(!isset($calPercent_2[$fons['year']][$fons['model']])){
						$calPercent_2[$fons['year']][$fons['model']] = 0;
					}
					$calPercent_2[$fons['year']][$fons['model']] += $fons['value'];
					if($fons['model'] == 'budget'){
						$totalBudget += $fons['value'];
					} else {
						$totalAvancement += $fons['value'];
					}
				}
			}
			if( !empty($percentYears) ){
				foreach($percentYears as $year => $percentYear){
					$bud = !empty($percentYear['budget']) ? $percentYear['budget'] : 0;
					$ava = !empty($percentYear['avancement']) ? $percentYear['avancement'] : 0;
					$per = ($bud ==0 ) ? 0 : round($ava/$bud*100, 2);
					$data['fon_percent_' . $year] = $per;
				}
			}
			$totalPercent = ($totalBudget == 0) ? 0 : $totalAvancement/$totalBudget*100;
			if( !isset($totalHeader_2['fon_budget']) ){
				$totalHeader_2['fon_budget'] = 0;
			}
			$totalHeader_2['fon_budget'] += $totalBudget;

			if( !isset($totalHeader_2['fon_avancement']) ){
				$totalHeader_2['fon_avancement'] = 0;
			}
			$totalHeader_2['fon_avancement'] += $totalAvancement;
			if( !isset($calPercent_2['total']['budget']) ){
				$calPercent_2['total']['budget'] = 0;
			}
			$calPercent_2['total']['budget'] += $totalBudget;
			if( !isset($calPercent_2['total']['avancement']) ){
				$calPercent_2['total']['avancement'] = 0;
			}
			$calPercent_2['total']['avancement'] += $totalAvancement;
			$data['fon_percent'] = round($totalPercent, 2);
			$data['fon_budget'] = round($totalBudget, 2);
			$data['fon_avancement'] = round($totalAvancement, 2);
			$data['fon_action.'] = '';
			$dataView_2[] = $data;
		}
	}
	if( !empty($calPercent_2) ){
		foreach($calPercent_2 as $key => $val){
			$bud = !empty($val['budget']) ? $val['budget'] : '';
			$ava = !empty($val['avancement']) ? $val['avancement'] : '';
			$per = ($bud ==0 ) ? 0 : round($ava/$bud*100, 2);
			if($key == 'total'){
				$totalHeader_2['fon_percent'] = $per;
			} else {
				$totalHeader_2['fon_percent_' . $key] = $per;
			}
		}
	}
}elseif($cat == 'finan_investissement'){
	if(!empty($finances['finaninv'])){
		foreach($finances['finaninv'] as $id => $finance){
			$data = array(
				'id' => $id,
				'fon_no.' => $j++,
				'MetaData' => array()
			);
			$data['fon_name'] = (string) $finance['name'];
			$data['fon_date'] = (string) $finance['finance_date'];
			$data['project_id'] = $projects['Project']['id'];
			$data['activity_id'] = $projects['Project']['activity_id'];
			$data['company_id'] = $projects['Project']['company_id'];
			$totalBudget = $totalAvancement = $totalPercent = 0;
			$percentYears = array();
			if(!empty($financeDetails[$id])){
				foreach($financeDetails[$id] as $model => $fons){
					if(!isset($totalHeader_2['fon_' . $model])){
						$totalHeader_2['fon_' . $model] = 0;
					}
					$totalHeader_2['fon_' . $model] += $fons['value'];
					$data['fon_' . $model] = $fons['value'];
					if(!isset($percentYears[$fons['year']][$fons['model']])){
						$percentYears[$fons['year']][$fons['model']] = 0;
					}
					$percentYears[$fons['year']][$fons['model']] += $fons['value'];
					if(!isset($calPercent_2[$fons['year']][$fons['model']])){
						$calPercent_2[$fons['year']][$fons['model']] = 0;
					}
					$calPercent_2[$fons['year']][$fons['model']] += $fons['value'];
					if($fons['model'] == 'budget'){
						$totalBudget += $fons['value'];
					} else {
						$totalAvancement += $fons['value'];
					}
				}
			}
			if( !empty($percentYears) ){
				foreach($percentYears as $year => $percentYear){
					$bud = !empty($percentYear['budget']) ? $percentYear['budget'] : 0;
					$ava = !empty($percentYear['avancement']) ? $percentYear['avancement'] : 0;
					$per = ($bud ==0 ) ? 0 : round($ava/$bud*100, 2);
					$data['fon_percent_' . $year] = $per;
				}
			}
			$totalPercent = ($totalBudget == 0) ? 0 : $totalAvancement/$totalBudget*100;
			if( !isset($totalHeader_2['fon_budget']) ){
				$totalHeader_2['fon_budget'] = 0;
			}
			$totalHeader_2['fon_budget'] += $totalBudget;

			if( !isset($totalHeader_2['fon_avancement']) ){
				$totalHeader_2['fon_avancement'] = 0;
			}
			$totalHeader_2['fon_avancement'] += $totalAvancement;
			if( !isset($calPercent_2['total']['budget']) ){
				$calPercent_2['total']['budget'] = 0;
			}
			$calPercent_2['total']['budget'] += $totalBudget;
			if( !isset($calPercent_2['total']['avancement']) ){
				$calPercent_2['total']['avancement'] = 0;
			}
			$calPercent_2['total']['avancement'] += $totalAvancement;
			$data['fon_percent'] = round($totalPercent, 2);
			$data['fon_budget'] = round($totalBudget, 2);
			$data['fon_avancement'] = round($totalAvancement, 2);
			$data['fon_action.'] = '';
			$dataView_2[] = $data;
		}
	}
	if( !empty($calPercent_2) ){
		foreach($calPercent_2 as $key => $val){
			$bud = !empty($val['budget']) ? $val['budget'] : '';
			$ava = !empty($val['avancement']) ? $val['avancement'] : '';
			$per = ($bud ==0 ) ? 0 : round($ava/$bud*100, 2);
			if($key == 'total'){
				$totalHeader_2['fon_percent'] = $per;
			} else {
				$totalHeader_2['fon_percent_' . $key] = $per;
			}
		}
	}
}else{
	if(!empty($finances['finanfon'])){
		foreach($finances['finanfon'] as $id => $finance){
			$data = array(
				'id' => $id,
				'fon_no.' => $j++,
				'MetaData' => array()
			);
			$data['fon_name'] = (string) $finance['name'];
			$data['fon_date'] = (string) $finance['finance_date'];
			$data['project_id'] = $projects['Project']['id'];
			$data['activity_id'] = $projects['Project']['activity_id'];
			$data['company_id'] = $projects['Project']['company_id'];
			$totalBudget = $totalAvancement = $totalPercent = 0;
			$percentYears = array();
			if(!empty($financeDetails[$id])){
				foreach($financeDetails[$id] as $model => $fons){
					if(!isset($totalHeader_2['fon_' . $model])){
						$totalHeader_2['fon_' . $model] = 0;
					}
					$totalHeader_2['fon_' . $model] += $fons['value'];
					$data['fon_' . $model] = $fons['value'];
					if(!isset($percentYears[$fons['year']][$fons['model']])){
						$percentYears[$fons['year']][$fons['model']] = 0;
					}
					$percentYears[$fons['year']][$fons['model']] += $fons['value'];
					if(!isset($calPercent_2[$fons['year']][$fons['model']])){
						$calPercent_2[$fons['year']][$fons['model']] = 0;
					}
					$calPercent_2[$fons['year']][$fons['model']] += $fons['value'];
					if($fons['model'] == 'budget'){
						$totalBudget += $fons['value'];
					} else {
						$totalAvancement += $fons['value'];
					}
				}
			}
			if( !empty($percentYears) ){
				foreach($percentYears as $year => $percentYear){
					$bud = !empty($percentYear['budget']) ? $percentYear['budget'] : 0;
					$ava = !empty($percentYear['avancement']) ? $percentYear['avancement'] : 0;
					$per = ($bud ==0 ) ? 0 : round($ava/$bud*100, 2);
					$data['fon_percent_' . $year] = $per;
				}
			}
			$totalPercent = ($totalBudget == 0) ? 0 : $totalAvancement/$totalBudget*100;
			if( !isset($totalHeader_2['fon_budget']) ){
				$totalHeader_2['fon_budget'] = 0;
			}
			$totalHeader_2['fon_budget'] += $totalBudget;

			if( !isset($totalHeader_2['fon_avancement']) ){
				$totalHeader_2['fon_avancement'] = 0;
			}
			$totalHeader_2['fon_avancement'] += $totalAvancement;
			if( !isset($calPercent_2['total']['budget']) ){
				$calPercent_2['total']['budget'] = 0;
			}
			$calPercent_2['total']['budget'] += $totalBudget;
			if( !isset($calPercent_2['total']['avancement']) ){
				$calPercent_2['total']['avancement'] = 0;
			}
			$calPercent_2['total']['avancement'] += $totalAvancement;
			$data['fon_percent'] = round($totalPercent, 2);
			$data['fon_budget'] = round($totalBudget, 2);
			$data['fon_avancement'] = round($totalAvancement, 2);
			$data['fon_action.'] = '';
			$dataView_2[] = $data;
		}
	}
	if( !empty($calPercent_2) ){
		foreach($calPercent_2 as $key => $val){
			$bud = !empty($val['budget']) ? $val['budget'] : '';
			$ava = !empty($val['avancement']) ? $val['avancement'] : '';
			$per = ($bud ==0 ) ? 0 : round($ava/$bud*100, 2);
			if($key == 'total'){
				$totalHeader_2['fon_percent'] = $per;
			} else {
				$totalHeader_2['fon_percent_' . $key] = $per;
			}
		}
	}
}
$selectMaps = array();
$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true),
    'Date closing must between %1$s and %2$s' => __('Date closing must between %1$s and %2$s', true)
);
// $canModified = 'true';
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete_finance', '%1$s', '%2$s', '?' => array('fon_start' => date('d-m-Y', $fonStart), 'fon_end' => date('d-m-Y', $fonEnd), 'inv_start' => date('d-m-Y', $invStart), 'inv_end' => date('d-m-Y', $invEnd)))); ?>">Delete</a>
        </div>
    </div>
</div>
<style>
	.chart .slick-pane.slick-pane-header .slick-header .slick-header-column{
		padding-top: 40px !important;
	}
	.chart .slick-pane.slick-pane-top.slick-pane-left{
		box-shadow: none;
	}
	.chart #table-control >*{
		display: inherit;
	}
	#table-control .button.chart-form-submit{
		background: none;
	}
</style>
<script type="text/javascript">   
    (function($){
		var DateValidate = {},dataGrid, ControlGridOne, ControlGridTwo, IuploadComplete = function(json){
			var data = dataGrid.eval('currentEditor');
			data.onComplete(json);
		};
        $(function(){
            var $this = SlickGridCustom,
            headerConsumedRightInv = '<div class="slick-header-columns">',
            headerConsumedRightFon = '<div class="slick-header-columns">',
            highLightInv = '.l2, .l3, .l4',
            highLightFon = '.l2, .l3, .l4',
            invStart = <?php echo json_encode(date('Y', $invStart));?>,
            invEnd = <?php echo json_encode(date('Y', $invEnd));?>,
            fonStart = <?php echo json_encode(date('Y', $fonStart));?>,
            fonEnd = <?php echo json_encode(date('Y', $fonEnd));?>,
            finanInvStart = <?php echo json_encode(date('Y', $finanInvStart));?>,
            finanInvEnd = <?php echo json_encode(date('Y', $finanInvEnd));?>,
            finanFonStart = <?php echo json_encode(date('Y', $finanFonStart));?>,
            finanFonEnd = <?php echo json_encode(date('Y', $finanFonEnd));?>,
            projects = <?php echo !empty($projects['Project']) ? json_encode($projects['Project']) : json_encode(array());?>,
            viewEuro = <?php echo json_encode($viewEuro);?>,
            totalHeader_1 = <?php echo json_encode($totalHeader_1);?>,
            totalHeader_2 = <?php echo json_encode($totalHeader_2);?>,
            totalName = <?php echo json_encode(__d(sprintf($_domain, 'Finance'), 'Total', true));?>;

            projects['activity_id'] = projects['activity_id'] ? projects['activity_id'] : 0;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  <?php echo json_encode(!empty($canModified) || ($_isProfile && $_canWrite)); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;

            var _history = <?php echo empty($history) ? '{}' : json_encode($history) ?>;
            var historyData = new $.z0.data(_history);
            var historyPath = <?php echo json_encode($this->params['url']['url']) ?>;
			$this.isExpand = true;
			$this.isResizing = 0;
            var actionTemplate =  $('#action-template').html();
            function resizeHandler1(){
                var _cols = ControlGridOne.getColumns();
                var _numCols = _cols.length;
                var _gridW = 0;
                var columnWidth = {};
                for (var i=0; i<_numCols; i++) {
                    _gridW += _cols[i].width;
                    columnWidth[_cols[i].id] = _cols[i].width;
                }
                $('.slick-header-columns').css('width', _gridW);
                historyData.set('columnWidth1', columnWidth);
                // call save here
                // **
                saveFilter();
            }
            function resizeHandler2(){
                var _cols = ControlGridTwo.getColumns();
                var _numCols = _cols.length;
                var _gridW = 0;
                var columnWidth = {};
                for (var i=0; i<_numCols; i++) {
                    _gridW += _cols[i].width;
                    columnWidth[_cols[i].id] = _cols[i].width;
                }
                $('.slick-header-columns').css('width', _gridW);
                historyData.set('columnWidth2', columnWidth);
                // call save here
                // **
                saveFilter();
            }
            var saveTimer;
            function saveFilter(){
                clearTimeout(saveTimer);
                saveTimer = setTimeout(function(){
                    $.z0.History.save(historyPath, historyData);
                }, 750);
            }
            $.extend(Slick.Formatters,{
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,
                    dataContext.project_id, dataContext.name), columnDef, dataContext);
                },
                manDayValue : function(row, cell, value, columnDef, dataContext){
                    if(value && value != 0 && typeof value !== 'undefined'){
                        value = number_format(value, 2, ',', ' ');
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + ' ' + viewEuro + '</span> ', columnDef, dataContext);
                    } else {
                        value = (value && value != 0) ? value : '';
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + '</span> ', columnDef, dataContext);
                    }
                },
                percentValue : function(row, cell, value, columnDef, dataContext){
                    if(value && value != 0){
                        var old_value = value <= 100 ? value : 100;
                        value = number_format(value, 2, ',', ' ');
                        var percentValue_html = '<span class="row-number">' + value + ' %' + '</span> <span class="row-percent" data-value="'+ old_value +'" style="width:' + old_value + '%;"></span>';
                        return Slick.Formatters.HTMLData(row, cell, percentValue_html , columnDef, dataContext);
                    } else {
                        value = (value && value != 0) ? value : '';
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + '</span> ', columnDef, dataContext);
                    }
                }
            });

            $.extend(Slick.Editors,{
                numericValue : function(args){
                    $.extend(this, new Slick.Editors.textBox(args));
                    this.input.attr('maxlength' , 10).keypress(function(e){
                        var key = e.keyCode ? e.keyCode : e.which;
                        var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
                        if(val == '0' || !/^[\-]?([0-9]{0,8})(\.[0-9]{0,2})?$/.test(val)){
                            e.preventDefault();
                            return false;
                        }
                    });
                }
            });
            var  data_1 = <?php echo json_encode($dataView_1); ?>;
            var  data_2 = <?php echo json_encode($dataView_2); ?>;
            var  columns_1 = <?php echo jsonParseOptions($columns_1, array('editor', 'formatter', 'validator')); ?>;
            var  columns_2 = <?php echo jsonParseOptions($columns_2, array('editor', 'formatter', 'validator')); ?>;
            var fieldNewInv = {
                project_id: projects['id'],
                activity_id : projects['activity_id'],
                company_id : projects['company_id'],
                inv_name : '',
                inv_date : '',
                inv_percent: '',
                inv_budget: '',
                inv_avancement: ''
            };
            var fieldNewFon = {
                project_id: projects['id'],
                activity_id : projects['activity_id'],
                company_id : projects['company_id'],
                fon_name : '',
                fon_date : '',
                fon_percent: '',
                fon_budget: '',
                fon_avancement: ''
            };

            var fields_1 = {
                id : {defaulValue : 0},
                project_id : {defaulValue : projects['id'], allowEmpty : false},
                activity_id : {defaulValue : projects['activity_id']},
                company_id : {defaulValue : projects['company_id'], allowEmpty : false},
                inv_name : {defaulValue : '', allowEmpty : false},
				inv_date : {defaulValue : '', allowEmpty : true}
            };
            var fields_2 = {
                id : {defaulValue : 0},
                project_id : {defaulValue : projects['id'], allowEmpty : false},
                activity_id : {defaulValue : projects['activity_id']},
                company_id : {defaulValue : projects['company_id'], allowEmpty : false},
                fon_name : {defaulValue : '', allowEmpty : false},
				fon_date : {defaulValue : '', allowEmpty : true}
            };
            var leftInv = 6, rightInv = 6, countInv = 1;
            while(invStart <= invEnd){
                fields_1['inv_budget_' + invStart] = {defaulValue : ''};
                fields_1['inv_avancement_' + invStart] = {defaulValue : ''};
                fieldNewInv['inv_budget_' + invStart] = '';
                fieldNewInv['inv_avancement_' + invStart] = '';
                fieldNewInv['inv_percent_' + invStart] = '';
                headerConsumedRightInv += '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'+(leftInv++)+' r'+(rightInv++)+'"><span>' +invStart+ '</span></div>';
                headerConsumedRightInv += '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'+(leftInv++)+' r'+(rightInv++)+'"></div>';
                headerConsumedRightInv += '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'+(leftInv++)+' r'+(rightInv++)+'"></div>';
                if(countInv%2 == 0){
                    var _l = leftInv;
                    highLightInv += ', .l' + (_l-3);
                    highLightInv += ', .l' + (_l-2);
                    highLightInv += ', .l' + (_l-1);
                }
                countInv++;
                invStart++;
            }
            headerConsumedRightInv += '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'+(leftInv++)+' r'+(rightInv++)+'"></div></div>';
            var leftFon = 6, rightFon = 6, countFon = 1;
            while(fonStart <= fonEnd){
                fields_2['fon_budget_' + fonStart] = {defaulValue : ''};
                fields_2['fon_avancement_' + fonStart] = {defaulValue : ''};
                fieldNewFon['fon_budget_' + fonStart] = '';
                fieldNewFon['fon_avancement_' + fonStart] = '';
                fieldNewFon['fon_percent_' + fonStart] = '';
                headerConsumedRightFon += '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'+(leftFon++)+' r'+(rightFon++)+'"><span>' +fonStart+ '</span></div>';
                headerConsumedRightFon += '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'+(leftFon++)+' r'+(rightFon++)+'"></div>';
                headerConsumedRightFon += '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'+(leftFon++)+' r'+(rightFon++)+'"></div>';
                if(countFon%2 == 0){
                    var _l = leftFon;
                    highLightFon += ', .l' + (_l-3);
                    highLightFon += ', .l' + (_l-2);
                    highLightFon += ', .l' + (_l-1);
                }
                countFon++;
                fonStart++;
            }
            headerConsumedRightFon += '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'+(leftFon++)+' r'+(rightFon++)+'"></div></div>';
            $this.onBeforeEdit = function(args){
                var columnId = args.column.id;                
                if( !$this.canModified) return false;
                if(columnId){
                    columnId = columnId.substring(0, 3);
                    if(columnId == 'inv'){
                        $this.url =  '<?php echo $html->url(array('action' => 'update_finance', 'inv')); ?>';
                        $this.fields = fields_1;
                    } else {
                        $this.url =  '<?php echo $html->url(array('action' => 'update_finance', 'fon')); ?>';
                        $this.fields = fields_2;
                    }
                } else {
                    return false;
                }
                return true;
            }
			$this.onCoxlumnsResized = function(args){
				if( $this.isResizing) return true;
				$this.isResizing = 1;
				var grid = args.grid;
				var options = grid.getOptions();
				var column_change = {};
				// only check when expand
				if( options.forceFitColumns == false ){
					// get column with changed
					var _columns = grid.getColumns();
					$.each( _columns, function(i, column){
						// moi lan resize co the co nhieu column bi anh huong, o day chi check cho 1 column
						if (column.width != column.previousWidth) { column_change = column;}
					});
				}
				var list_column_willchange = {};
				if( column_change.width ){
					var width = column_change.width;
					var _field = column_change.field.split('_');
					if( _field[1]){
						// change column width for the columns with same name
						var price_fields = ['avancement', 'budget'];
						$.each(_columns, function(i, column){
							var field = column.field.split('_');
							if( field[1] == _field[1] || ( ($.inArray(field[1], price_fields) != -1) && ($.inArray(_field[1], price_fields) != -1) ) ){
								_columns[i].previousWidth = _columns[i].width;
								_columns[i].width = width;
							}
						});
						grid.wdSetColumns(_columns); // uppdate for Slick grid custom
						grid.setColumns(_columns);
						type = ( $(grid.getContainerNode()).attr('id') == 'project_container_1' ? 'inv' : 'fon');
						headerCalc(type);
					}
				}
				
				// resizeHandler1();
				// resizeHandler2();
				grid.eval('applyColumnHeaderWidths();updateCanvasWidth(true);');
				$this.isResizing = 0;
				return true;
			}
			function updateCanvas(type){
				var ControlGrid = (type == 'inv') ? ControlGridOne : ControlGridTwo;
				var chard = (type == 'inv') ? '#inve-chard' : '#fon-chard';
				var totalBudget = $( ControlGrid.getHeaderRowColumn( type + '_budget') ).text();
				$(chard).find('.chard-content .progress-validated span').text(totalBudget);
				totalBudget = parseFloat(totalBudget.replace(/ /g, '').replace(',', '.'));
				var totalAvan = $( ControlGrid.getHeaderRowColumn( type + '_avancement') ).text();
				$(chard).find('.chard-content .progress-engaged span').text(totalAvan);
				totalAvan = parseFloat(totalAvan.replace(/ /g, '').replace(',', '.'));
				var percent = Math.round(( totalAvan / totalBudget ) * 100); // L??m tr??n 2 ch??? s??? th???p ph??n
				var canvas = (type == 'inv') ? 'myCanvas-inv' : 'myCanvas-fon';
				color = (percent > 100) ? '#DB414F' : '#75AF7E';
				$('#' + canvas).attr('data-color', color);
				$('#' + canvas).data('val', percent);
				draw_pie_progress($('#' + canvas));
				setColorFromCanvas();
			}
            $this.onCellChange = function(args){
                if(args && args.column.id && args.item){
                    var columnId = args.column.id;
                    columnId = columnId.substring(0, 3);
                    var totalBudget = 0, totalAvan = 0, totalPercent = 0;
                    var budgetYears = {}, avanYears = {};
                    $.each(args.item, function(ind, val){
                        val = val ? val : 0;
                        ind = ind.split('_');
                        if(ind.length == 3){
                            if(ind[1] == 'budget'){
                                budgetYears[ind[2]] = parseFloat(val);
                                totalBudget += parseFloat(val);
                            } else if(ind[1] == 'avancement') {
                                avanYears[ind[2]] = parseFloat(val)
                                totalAvan += parseFloat(val);
                            }
                        }
                    });
                    totalPercent = (totalBudget == 0) ? 0 : totalAvan/totalBudget*100;
                    if(budgetYears){
                        $.each(budgetYears, function(y, budVal){
                            var avanVal = avanYears[y] ? avanYears[y] : 0;
                            var perVal = (budVal == 0) ? 0 : avanVal/budVal*100;
                            args.item[columnId + '_percent_' + y] = parseFloat(perVal);
                        });
                    }
                    args.item[columnId + '_percent'] = parseFloat(totalPercent);
                    args.item[columnId + '_budget'] = parseFloat(totalBudget);
                    args.item[columnId + '_avancement'] = parseFloat(totalAvan);
                    /**
                     * Tinh header
                     */
                    var _datas = (columnId == 'inv') ? $.extend(true, {}, data_1) : $.extend(true, {}, data_2);
                    var _totalHeader = {}, _budgetHeader = {}, _avanHeader = {};
                    $.each(_datas, function(key, _data){
                        $.each(_data, function(ind, val){
                            val = val ? val : 0;
                            var _ind = ind.split('_');
                            if(_ind[1] && (_ind[1] == 'budget' || _ind[1] == 'avancement')){
                                if(!_totalHeader[ind]){
                                    _totalHeader[ind] = 0;
                                }
                                _totalHeader[ind] += parseFloat(val);
                                var _key = _ind[2] ? _ind[2] : 'total';
                                if(_ind[1] == 'budget'){
                                    if(!_budgetHeader[_key]){
                                        _budgetHeader[_key] = 0;
                                    }
                                    _budgetHeader[_key] += parseFloat(val);
                                } else if(_ind[1] == 'avancement') {
                                    if(!_avanHeader[_key]){
                                        _avanHeader[_key] = 0;
                                    }
                                    _avanHeader[_key] += parseFloat(val);
                                }
                            }
                        });
                    });
                    if(_budgetHeader){
                        $.each(_budgetHeader, function(key, budVal){
                            var avanVal = _avanHeader[key] ? _avanHeader[key] : 0;
                            var perVal = (budVal == 0) ? 0 : avanVal/budVal*100;
                            if(key == 'total'){
                                _totalHeader[columnId + '_percent'] = perVal;
                            } else {
                                _totalHeader[columnId + '_percent_' + key] = perVal;
                            }
                        });
                    }
                    var ControlGrid = (columnId == 'inv') ? ControlGridOne : ControlGridTwo;
                    if(_totalHeader){
                        $.each(_totalHeader , function(id){
                            var _views = id.split('_');
                            var _symbol = (_views[1] && _views[1] == 'percent') ? '%' : viewEuro;
                            var val = Number(this) ? number_format(Number(this), 2, ',', ' ') + ' ' + _symbol : '';
                            if($(ControlGrid.getHeaderRowColumn(id)).hasClass('row-number')){
                                $(ControlGrid.getHeaderRowColumn(id)).find('.row-number b').html(val);
                            } else {
                                $(ControlGrid.getHeaderRowColumn(id)).html('<span class="row-number"><b>' + val + '</b></span>');
                            }
                        });
                    }
                    var columns = args.grid.getColumns(),
                        col, cell = args.cell;
                    do {
                        cell++;
                        if( columns.length == cell )break;
                        col = columns[cell];
                    } while (typeof col.editor == 'undefined');

                    if( cell < columns.length ){
                        args.grid.gotoCell(args.row, cell, true);
                    }
					updateCanvas(columnId);
                }
                $('.row-number').parent().addClass('row-number-custom');
            }
            ControlGridOne = $this.init($('#popup_project_container_1'),data_1,columns_1,{
                showHeaderRow: true,
                enableAddRow : false,
                frozenColumn: 5,
                rowHeight: 40,
                enableCellNavigation: true,
                enableAddRow: true,
				forceFitColumns: false													  
            });
            ControlGridOne.onColumnsResized.subscribe(function (e, args) {
                resizeHandler1();
            });
            ControlGridTwo = $this.init($('#popup_project_container_2'),data_2,columns_2,{
                showHeaderRow: true,
                enableAddRow : false,
                frozenColumn: 5,
                rowHeight: 40,
                enableCellNavigation: true,
                enableAddRow: true,
				forceFitColumns: false
            });
            ControlGridTwo.onColumnsResized.subscribe(function (e, args) {
                resizeHandler2();
            });
            
            var _ids = 999999999999;
            addNewRow = function(type){
                if(!$this.canModified) return;
                var newRow = (type == 'inv') ? $.extend(true, {}, fieldNewInv) : $.extend(true, {}, fieldNewFon);
                var ControlGrid = (type == 'inv') ? ControlGridOne : ControlGridTwo;
                var rowData = ControlGrid.getData().getItems();
                var _length = rowData.length;
                newRow['id'] = _ids++;
                ControlGrid.invalidateRow(_length);
                rowData.splice(_length, 0, newRow);
                ControlGrid.getData().setItems(rowData);
                ControlGrid.render();
                ControlGrid.scrollRowIntoView(_length-1, false);
                $('.row-number').parent().addClass('row-number-custom');
                ControlGrid.gotoCell(_length, 1, true);
            }
            /**
             * Add header phia duoi
             */
            if(totalHeader_1){
                $.each(totalHeader_1 , function(id){
                    var _views = id.split('_');
                    var _symbol = (_views[1] && _views[1] == 'percent') ? '%' : viewEuro;
                    var val = Number(this) ? number_format(Number(this), 2, ',', ' ') + ' ' + _symbol : '';
                    $(ControlGridOne.getHeaderRowColumn(id)).html('<span class="row-number"><b>' + val + '</b></span>');
                });
            }
            if(totalHeader_2){
                $.each(totalHeader_2 , function(id){
                    var _views = id.split('_');
                    var _symbol = (_views[1] && _views[1] == 'percent') ? '%' : viewEuro;
                    var val = Number(this) ? number_format(Number(this), 2, ',', ' ') + ' ' + _symbol : '';
                    $(ControlGridTwo.getHeaderRowColumn(id)).html('<span class="row-number"><b>' + val + '</b></span>');
                });
            }
            /**
             * Handle Form Control date time
             */
            $('#sutInv').click(function(){
                $('#InvFonStart').val($('#FonFonStart').val());
                $('#InvFonEnd').val($('#FonFonEnd').val());
            });
            $('#sutFon').click(function(){
                $('#FonInvStart').val($('#InvInvStart').val());
                $('#FonInvEnd').val($('#InvInvEnd').val());
            });
            /**
             * add class lt
             */
            $('.budget-chard-container .row-number').parent().addClass('row-number-custom');
            $('.budget-chard-container .slick-headerrow-columns div').addClass('gs-custom-cell-erro');
            /**
             * Add header phia tren
             */
            var headerConsumedLeft =
                '<div class="slick-header-columns">'
                    + '<div class="slick-headerrow-column l0 r1 gs-custom-cell-euro-header fist-element border-euro-custom"></div>'
                    + '<div class="slick-headerrow-column l3 r5 gs-custom-cell-euro-header border-euro-custom"><span>' +totalName+ '</span></div>'
              + '</div>';
            $('#popup_project_container_1').find('.slick-header-columns-left').before(headerConsumedLeft);
            $('#popup_project_container_1').find('.slick-header-columns-right').before(headerConsumedRightInv);
            $('#popup_project_container_1 .slick-header-columns').find(highLightInv).addClass('headerHighLight');
            //$('#popup_project_container_1').height($('#popup_project_container_1').height() + 36);

            $('#popup_project_container_2').find('.slick-header-columns-left').before(headerConsumedLeft);
            $('#popup_project_container_2').find('.slick-header-columns-right').before(headerConsumedRightFon);
            $('#popup_project_container_2 .slick-header-columns').find(highLightFon).addClass('headerHighLight');
            //$('#popup_project_container_2').height($('#popup_project_container_2').height() + 36);

            /**
             * Handle date time
             */
            $("#InvInvStart, #InvInvEnd, #FonFonStart, #FonFonEnd").datepicker({
                dateFormat      : 'dd-mm-yy'
            });
			function hexToRgb(hex) {
                var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
                return result ? {
                    r: parseInt(result[1], 16),
                    g: parseInt(result[2], 16),
                    b: parseInt(result[3], 16)
                } : null;
            }	/**
				 * Tinh header
				 */
			function headerCalc(columnId){
				var _datas = (columnId == 'inv') ? $.extend(true, {}, data_1) : $.extend(true, {}, data_2);
				var _totalHeader = {}, _budgetHeader = {}, _avanHeader = {};
				$.each(_datas, function(key, _data){
					$.each(_data, function(ind, val){
						val = val ? val : 0;
						var _ind = ind.split('_');
						if(_ind[1] && (_ind[1] == 'budget' || _ind[1] == 'avancement')){
							if(!_totalHeader[ind]){
								_totalHeader[ind] = 0;
							}
							_totalHeader[ind] += parseFloat(val);
							var _key = _ind[2] ? _ind[2] : 'total';
							if(_ind[1] == 'budget'){
								if(!_budgetHeader[_key]){
									_budgetHeader[_key] = 0;
								}
								_budgetHeader[_key] += parseFloat(val);
							} else if(_ind[1] == 'avancement') {
								if(!_avanHeader[_key]){
									_avanHeader[_key] = 0;
								}
								_avanHeader[_key] += parseFloat(val);
							}
						}
					});
				});
				if(_budgetHeader){
					$.each(_budgetHeader, function(key, budVal){
						var avanVal = _avanHeader[key] ? _avanHeader[key] : 0;
						var perVal = (budVal == 0) ? 0 : avanVal/budVal*100;
						if(key == 'total'){
							_totalHeader[columnId + '_percent'] = perVal;
						} else {
							_totalHeader[columnId + '_percent_' + key] = perVal;
						}
					});
				}
				var ControlGrid = (columnId == 'inv') ? ControlGridOne : ControlGridTwo;
				if(_totalHeader){
					$.each(_totalHeader , function(id){
						var _views = id.split('_');
						var _symbol = (_views[1] && _views[1] == 'percent') ? '%' : viewEuro;
						var val = Number(this) ? number_format(Number(this), 2, ',', ' ') + ' ' + _symbol : '';
						if($(ControlGrid.getHeaderRowColumn(id)).hasClass('row-number')){
							$(ControlGrid.getHeaderRowColumn(id)).find('.row-number b').html(val);
						} else {
							$(ControlGrid.getHeaderRowColumn(id)).html('<span class="row-number"><b>' + val + '</b></span>');
						}
					});
				}
				$('.row-number').parent().addClass('row-number-custom');
			}
			function setColorFromCanvas(){
				$('[id*="myCanvas"]').each(function(){
					var _this = $(this);
					var color = _this.attr('data-color');
					var _chart = _this.closest('.chart');
					_chart.find('.slick-pane.slick-pane-header').css('background-color',color);
					_chart.find('.btn-plus-green').css('color',color);
					_chart.find('.btn-plus-green span').css('background-color',color);
					var rgb = hexToRgb(color);
					var header_color = 'rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ', 0.3 )';
					var percent_color = 'rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ', 0.1 )';
					_chart.find('.slick-headerrow').css('background-color',header_color);
					_chart.find('.wd-table').data('chart-color',percent_color);
					_chart.find('.row-percent').css('background-color',percent_color);
					$('.row-number').parent().addClass('row-number-custom');
				});
			}
			setColorFromCanvas();
			$(window).resize(function(){
				ControlGridOne.resizeCanvas();
				ControlGridTwo.resizeCanvas();
			});					  
        });
		if(document.getElementById('myPopupcanvas')){
			var prog = draw_progress('myPopupcanvas');
		} 
		if(document.getElementById('myPopupcanvas-2')){
			var prog = draw_progress('myPopupcanvas-2');
		} 
		if(document.getElementById('myPopupcanvas-3')){
			var prog = draw_progress('myPopupcanvas-3');
		}
		function hexToRgb(hex) {
            var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? {
                r: parseInt(result[1], 16),
                g: parseInt(result[2], 16),
                b: parseInt(result[3], 16)
            } : null;
        }
        $(document).ready(function() {
            $('[id*="myPopupcanvas"]').each(function(){
                var _this = $(this);
                var color = _this.data('color');
                var _chart = _this.closest('.chart');
                _chart.find('.slick-pane.slick-pane-header').css('background-color',color);
                _chart.find('.btn-plus-green').css('color',color);
                _chart.find('.btn-plus-green span').css('background-color',color);
                var rgb = hexToRgb(color);
                var header_color = 'rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ', 0.3 )';
                var percent_color = 'rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ', 0.1 )';
                _chart.find('.slick-headerrow').css('background-color',header_color);
                _chart.find('.wd-popup-table').data('chart-color',header_color);
                _chart.find('.wd-bt-big').closest('.slick-cell').addClass('row_action_del');
                ControlGridOne.resizeCanvas();
                ControlGridTwo.resizeCanvas();
                _chart.find('.row-percent').css('background-color',percent_color);                
                $('.row-number').parent().addClass('row-number-custom');
            });
        });
        $('form').on('submit', function(e){
            if($('#dialogDetailValue').length == 0) return;
            var _this = $(this);
            var _cat = _this.data('cat') ? _this.data('cat') : '';
            _this.addClass('loading');
            var id = <?php echo $project_id; ?>;
            e.preventDefault();
            var form_data = $(this).serialize();
            if(form_data == '') return;
            form_data += '&cat=' + _cat;
            _this.addClass('loading');
            _this.closest('.chart').addClass('loading');
            $.ajax({
                url : "/project_finances_preview/ajax/"+id,
                type: "GET",
                cache: false,
                data: form_data,
                success: function (html) {
                    hideMe();
                     $('#dialogDetailValue').css({'padding-top':0,'padding-bottom':0});
                    var wh= $(window).height();
                    if (wh < 768) {
                         $('#contentDialog').css({'max-height':600,'width':'auto'});
                    } else {
                         $('#contentDialog').css({'max-height':'','width':'auto'});
                    }
                    _this.closest('.chart').removeClass('loading');
                    _this.removeClass('loading');

                    $('#contentDialog').css('opacity', '0');
                    showMe();
                    $('#contentDialog').html(html);

                    $('#contentDialog').css('opacity', '1');
                }
            });
        });
    })(jQuery);
    function f_popup_validated(checkType){
        var _start = (checkType == 'inv') ? $('#InvInvStart').val().toString() : $('#FonFonStart').val().toString() ;
        _start = _start.split('-');
		console.log(_start);
        var myStartDate = new Date(_start[2],_start[1],_start[0]);
        _start = Number(myStartDate);

        var _end = (checkType == 'inv') ? $('#InvInvEnd').val().toString() : $('#FonFonEnd').val().toString() ;
        _end = _end.split('-');
		console.log(_end);
		
        var myEndDate = new Date(_end[2],_end[1],_end[0]);
        _end = Number(myEndDate);
        if(_start <= _end){
            $('#wd-end-date-' + checkType).removeClass('error');
            $('#wd-group-' + checkType).removeClass('wd-end-st');
            $('.wd-error-' + checkType).css('display', 'none');
            $('#wd-submit-' + checkType).show();
        } else {
            $('#wd-end-date-' + checkType).addClass('error');
            $('#wd-group-' + checkType).addClass('wd-end-st');
            $('.wd-error-' + checkType).css('display', 'block');
            $('#wd-submit-' + checkType).hide();
        }
    }
    function number_format(number, decimals, dec_point, thousands_sep) {
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }
	function draw_pie_progress(pie_progress){
		var progress_width = pie_progress.width();
		pie_progress.addClass('wd-progress-pie').setupProgressPie({
			size: 124,
			strokeWidth: 8,
			ringWidth: 8,
			ringEndsRounded: true,
			strokeColor: "#f3f3f3",
			color: function(value){
				var red = 'rgba(233, 71, 84, 1)';
				var green = 'rgba(110, 175, 121, 1)';
				return pie_progress.data('val') > 100 ? red : green;
			},
			valueData: "val",
			contentPlugin: "progressDisplay",
			contentPluginOptions: {
				fontFamily: 'Open Sans',
				multiline: [
					{
						cssClass: "progresspie-progressText",
						fontSize: 11,
						textContent: ('<?php __('Progress');?>').toUpperCase(),
						color: '#ddd',
					},
					{
						cssClass: "progresspie-progressValue",
						fontSize: 28,
						textContent: '%s%' ,
						color: function(value){
							var red = 'rgba(233, 71, 84, 1)';
							var green = 'rgba(110, 175, 121, 1)';
							return pie_progress.data('val') > 100 ? red : green;
						}
					},
				],
				fontSize: 28
			},
			animate: {
				dur: "1.5s"
			}
		}).progressPie();
	}
	draw_pie_progress($('#myCanvas-inv'));
	draw_pie_progress($('#myCanvas-fon'));
	draw_pie_progress($('#myCanvas-finaninv'));
	draw_pie_progress($('#myCanvas-finanfon'));
</script>