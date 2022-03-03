<?php echo $html->css('context/jquery.contextmenu'); ?>
<?php echo $html->script('context/jquery.contextmenu');  ?>
<style>
.capacity, .workload{ font-size:11px; letter-spacing:1px}
.ct{ vertical-align:middle}
i{ display:block;}
</style>

<!-- export excel  -->
<fieldset style="display: none;">
    <?php

    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'absence_requests', 'action' => 'export')));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<!-- /export excel  -->
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div  style="padding-left:20px;">
                    <select style="padding:2px;" name="typeRequest" id="typeRequest">
                        <option value="week" <?php echo $typeSelect=='week'?'selected':'';?>><?php echo __('Week',true);?></option>
                        <option value="month" <?php echo $typeSelect=='month'?'selected':'';?>><?php echo __('Month',true);?></option>
                        <option value="year" <?php echo $typeSelect=='year'?'selected':'';?>><?php echo __('Year',true);?></option>
                    </select>
                </div>
                <div id="message-place">
                    <?php
                    echo $this->Session->flash();
                    $am = __('AM', true);
                    $pm = __('PM', true);

                    $dayMaps = array(
                        'monday' => $_start,
                        'tuesday' => $_start + DAY,
                        'wednesday' => $_start + (DAY * 2),
                        'thursday' => $_start + (DAY * 3),
                        'friday' => $_start + (DAY * 4),
                        'saturday' => $_start + (DAY * 5),
                        'sunday' => $_start + (DAY * 6)
                    );
                    ?>
                </div>
                <div class="wd-table" id="project_container" style="width:100%;">
                    <div id="absence-container" style="min-height:400px;">
                        <div id="table-control" >
                            <?php
                            echo $this->Form->create('Control', array(
                                'type' => 'get',
                                'url' => '/' . Router::normalize($this->here)));
                            ?>
                             <fieldset style="margin-left: 22px;">
                              <?php
                                 echo $this->element('week_activity');
                              ?>
                                <div class="input">
                                    <?php
                                    echo $this->Form->select('profit', $paths, $profit['id'], array('empty' => false, 'escape' => false));
                                    ?>
                                </div>
                                
                                <div class="button">
									<!-- ***Quan update hover on expand icon 14/01/2019*** -->
                                    <input type="submit" value="OK" title="<?php __('Apply')?>"/><span><?php __('Apply'); ?></span>
									<!-- End update  -->
                                </div>
                            </fieldset>
                            <?php
                            echo $this->Form->end();
                            ?>
                            <?php $urlExport =str_replace('my_diary','export_my_diary',$_SERVER['REQUEST_URI']);?>
                            <a href="<?php echo $this->Html->url($urlExport);?>" id="submit-request-all-top" class="export-outlook-my-diary export-outlook-my-diary-top" title="<?php __('Export OutLook')?>"><span><?php __('Export OutLook'); ?></span></a>
                        </div>
                        <?php if($typeSelect=='week'){ ?>

                        <script>
						var intv=setInterval(function(){
							var wAW=$('#absence-wrapper').width();
							var wAF=$('#absence-fixed').width();
							var wAS=wAW-wAF-10;
							$('#absence').css({'width':wAS});
							<?php
							$j=0;
							foreach ($employees as $id => $employee) :
									$j++  ;?>
									var i = $('tr.tdLeftRow-<?php echo $id;?>').height();
									if($.browser.mozilla == true)
									$('tr.tdRightRow-<?php echo $id;?>').css("height",i);
									else
									$('tr.tdRightRow-<?php echo $id;?>').css("height",i+1);
							<?php endforeach;?>
							clearInterval(intv);
						},10);
						$(window).resize(function() {
							var wAW=$('#absence-wrapper').width();
							var wAF=$('#absence-fixed').width();
							var wAS=wAW-wAF-10;
							$('#absence').css({'width':wAS});
							<?php
							$j=0;
							foreach ($employees as $id => $employee) :
									$j++  ;?>
									var i = $('tr.tdLeftRow-<?php echo $id;?>').height();
									if($.browser.mozilla == true)
									$('tr.tdRightRow-<?php echo $id;?>').css("height",i);
									else
									$('tr.tdRightRow-<?php echo $id;?>').css("height",i+1);
							<?php endforeach;?>
						});
						</script>
                        <?php }?>
                        <div id="absence-wrapper"  >
                                <table id="absence-fixed">
                                    <thead>
                                        <tr>
                                            <th ><?php __('Employee'); ?></th>
                                            <th style="width:140px;"><?php __('Workload') ?>*/<?php __('Capacity'); ?>*</th>
                                        </tr>
                                    </thead>
                                    <tbody id="absence-table-fixed">
                                    <tr id="affterLeft"><td colspan="2" ></td></tr>
                                    <tr><td class='ct summary'><?php echo __('Summary',true) ?></td><td id="summary" class='ct summary'></td></tr>
                                    </tbody>
                                </table>

                            <div id="absence-scroll">
                                <table id="absence" class="absence-margin">
                                    <thead>
                                        <tr class="height-header-fixed">
                                            <?php
												if($typeSelect=='week'){
											   		$countWorkdays=0;
                                                    if(!empty($workdays)):
                                                        foreach($workdays as $key => $val):
                                                            if(!empty($val) && $val != 0):
															$countWorkdays++;
                                                ?>

                                                <th width="70" id="<?php echo 'fore'.ucfirst($key);?>"><?php echo __(ucfirst($key)) . ' ' . __(date('d', $dayMaps[$key])) . ' ' . __(date('M', $dayMaps[$key])); ?></th>
                                                <?php
                                                            endif;
                                                        endforeach;
                                                    endif;
                                                ?>
                                            <?php }else{?>
                                            <?php
                                                if(!empty($dayWorks)):
                                                        $dayMaps = array();
                                                        $workdaysTmp = array();
                                                        $i=0;
                                                        foreach($dayWorks as $key => $val):
                                                            if($workdays[$val[1]]!=0):
                                                                 $keyTmp = $val[1];
                                                                 if(!in_array($val[1], $workdays)){ $keyTmp = $val[1].$i;}
                                                                 $workdaysTmp = array_merge($workdaysTmp,array($keyTmp=>1));
                                                                  $dayMaps =  array_merge($dayMaps,array($keyTmp => strtotime($val[0].' '.date('Y',$_start))));
                                                ?>
                                                 <th class="fixedWidth" id="<?php echo 'fore'.ucfirst($key);?>">
                                                    <?php echo __(date('l',strtotime($val[0].' '.date('Y', $_start)))). ' ' . __(date('d',strtotime($val[0].' '.date('Y', $_start)))) . ' ' . __(date('M',strtotime($val[0].' '.date('Y', $_start)))); ?>
                                                 </th>
                                                <?php
                                                            endif;
                                                            $i++;
                                                        endforeach;
                                                    endif;
                                                ?>
                                            <?php $workdays = $workdaysTmp; $countWorkdays=count($workdays); }

											$countEmployees=count($employees); ?>
                                        </tr>
                                    </thead>
                                    <tbody id="absence-table">
									<?php
                                    $dataView = array();
                            
                                    foreach ($employees as $id => $employee) {
                                        foreach ($dayMaps as $day => $time) {
                                            $_holiday=false;
                                            foreach($holidays as $key=>$value123)
                                            {
                                                if($time==$key) $_holiday=true;
                                            }
                                            $default = array(
                                                'holiday'=>$_holiday
                                            );
                                            foreach (array('am', 'pm') as $type) {
                                                if (!empty($requests[$id][$time]['absence_' . $type])
                                                        && ($requests[$id][$time]['response_' . $type] === 'validated'
                                                        || empty($forecasts[$id][$time]['activity_' . $type]))) {
                                                    $default['absence_' . $type] = $requests[$id][$time]['absence_' . $type];
                                                    $default['response_' . $type] = $requests[$id][$time]['response_' . $type];
                                                }
                                                if (!empty($forecasts[$id][$time]['activity_' . $type])) {
                                                    $default['activity_' . $type] = $forecasts[$id][$time]['activity_' . $type];
                                                    $default[$type . '_model'] = strtolower($forecasts[$id][$time][$type . '_model']);
                                                }
                                            }
                                            if(!empty($workdays[$day]) && $workdays[$day] != 0){
                                                $dataView[$id][$day] = $default;
                                            }
                                        }
                                    }
                                    if(!empty($workloads)){
                                        foreach($dataView as $id1 => $_dataViews){
                                            foreach($_dataViews as $time1 => $_dataView){
                                                foreach($workloads as $id2 => $_workloads){
                                                    foreach($_workloads as $time2 => $_workload){
                                                        if($id1 == $id2 && $time1 == $time2){
                                                            $dataView[$id1][$time1]['data'] = $_workload;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
									$tdsSummary='';
									$summary=0;
									$totalCapacity=0;
									$countRow1=0;
									$left='';
									$a='';
                                    $listPhase = $listPart = $listProject = $acOfTask = array();
                                    foreach($dataView as $emp=>$workload)
                                    {
										$totalCountDownCapacity=0;
										$totalCountDownWorkload=0;
										foreach($employees as $id=>$employee)
										{

											if($emp==$id)
											{
												$employeeCustom[$id]['capacity']=0;
												$employeeCustom[$id]['workload']=0;
												$countRow1++;
                                                echo "<tr class='fixedHeight tdLeftRow-".$id."'>";
												$countCell=0;
												$countDownCapacity1=0;
                                                foreach($workload as $time=> $value)
                                                {
													$fw='';
													if(substr($time,0,6)=='monday')
													{
														$fw='fw';
													}
													$countCell++;
                                                    $flag=0;
                                                    $class=$fw.' employee-'.$countRow1.'-'.$countCell;
                                                    $text='';
													$text1='';
													$summaryEmployeeOnDay=0;
                                                    $workloadPr='';
                                                    $namePr='';
													$nameTaskPr='';
                                                    $workloadAc='';
                                                    $nameAc='';
													$nameTaskAc='';
													$countDownCapacity=0;
                                                    if(isset($value['holiday'])&&$value['holiday']===true)
                                                    {
                                                        $class.=' rp-holiday';
														$text = __("Holiday", true);
														$flag=1;
														$countDownCapacity+=1;
                                                    }
                                                    echo "<td class='wd-work ".$class."'>";

                                                    if(isset($value['data']))
                                                    {
														$countRow=0;
                                                        foreach($value['data'] as $key=> $val)
                                                        {
															$countRow=count($value['data']);
                                                            if(isset($value['data'][$key]))
                                                            {
																$vacation=$value['data'][$key]['vacation'];
																/*-----DESCRIPTION-----
																vacation = -1 : ngay binh thuong
																vacation = 1 : nghi ca ngay
																vacation = 3 : nghi buoi sang da validate
																vacation = 5 : nghi buoi sang da validate, buoi chieu dang waiting
																vacation = 7 : nghi buoi chieu da validate
																vacation = 9 : nghi buoi chieu da validate, buoi sang dang waiting
																vacation = 11 : buoi sang dang waiting, nghi buoi chieu da validate
																vacation = 13 : buoi chieu dang waiting, nghi buoi sang da validate
																vacation = 2 : ca ngay dang waiting
																vacation = 4 : nghi buoi sang dang waiting
																vacation = 6 : nghi buoi chieu dang waiting
																---------------------*/
                                                                if($vacation==1)
																{
                                                                    if($value['absence_am'] == $value['absence_pm']){
    																	$class.=' rp-validated';
    																	$text="<span class='rp-validated'>".$absences[$value['absence_am']]['print']."(1)</span>";
    																	$flag=1;
    																	$countDownCapacity+=(1/$countRow);
                                                                    }else {
                                                                        $text="<span class='rp-validated'>".$absences[$value['absence_am']]['print']."(0.5 AM)</span>";
                                                                        $text1="<span class='rp-validated'>".$absences[$value['absence_pm']]['print']."(0.5 PM)</span>";
    																	$flag=3;
    																	$countDownCapacity+=(0.5/$countRow);
                                                                    }
																}
																else if($vacation==3)
																{
																	$text="<span class='rp-validated'>".$absences[$value['absence_am']]['print']."(0.5 AM)</span>";
																	$flag=2;
																	$countDownCapacity+=(0.5/$countRow);
																}
																else if($vacation==5||$vacation==13)
																{
																	$text="<span class='rp-validated'>".$absences[$value['absence_am']]['print']."(0.5 AM)</span>";
																	$text1="<span class='rp-waiting'>".$absences[$value['absence_pm']]['print']."(0.5 PM)</span>";
																	$flag=3;
																	$countDownCapacity+=(0.5/$countRow);
																}
																else if($vacation==7)
																{
																	$text="<span class='rp-validated'>".$absences[$value['absence_pm']]['print']."(0.5 PM)</span>";
																	$flag=2;
																	$countDownCapacity+=(0.5/$countRow);
																}
																else if($vacation==9||$vacation==11)
																{
																	$text1="<span class='rp-validated'>".$absences[$value['absence_am']]['print']."(0.5 AM)</span>";
																	$text="<span class='rp-waiting'>".$absences[$value['absence_pm']]['print']."(0.5 PM)</span>";
																	$flag=4;
																	$countDownCapacity+=(0.5/$countRow);
																}
																else if($vacation==2)
																{
                                                                    if($value['absence_am'] == $value['absence_pm']){
    																	$text="<span class='rp-waiting'>".$absences[$value['absence_am']]['print']."(1)</span>";
    																	$flag=2;
                                                                    } else {
                                                                        $text="<span class='rp-waiting'>".$absences[$value['absence_am']]['print']."(0.5 AM)</span>";
                                                                        $text1="<span class='rp-waiting'>".$absences[$value['absence_pm']]['print']."(0.5 PM)</span>";
    																	$flag=3;
                                                                    }
																}
																else if($vacation==4)
																{
																	$text="<span class='rp-waiting'>".$absences[$value['absence_am']]['print']."(0.5 AM)</span>";
																	$flag=2;
																}
																else if($vacation==6)
																{
																	$text="<span class='rp-waiting'>".$absences[$value['absence_pm']]['print']."(0.5 PM)</span>";
																	$flag=2;
																}

                                                                if(isset($value['data'][$key]['idPr']))
                                                                {
                                                                    $keyPr=$time.'-'.$value['data'][$key]['idPr'];
                                                                    $namePr[$keyPr] = $value['data'][$key]['namePr'];
																	if(!isset($nameTaskPr[$keyPr]))
																	{
																		$nameTaskPr[$keyPr]='';
																		$workloadPrForItem[$keyPr]= 0;

																	}
																	$nameTaskPr[$keyPr] .= '<span>'.$value['data'][$key]['nameTask'].' ('.$value['data'][$key]['workload'].')</span>';
																	$workloadPrForItem[$keyPr] += $value['data'][$key]['workload'];

                                                                    $workloadPr += $value['data'][$key]['workload'];
																	$summary += $value['data'][$key]['workload'];
                                                                }
                                                                if(isset($value['data'][$key]['idAc']))
                                                                {
                                                                    $keyAc=$time.'-'.$value['data'][$key]['idAc'];
                                                                    $nameAc[$keyAc] = $value['data'][$key]['nameAc'];
																	if(!isset($nameTaskAc[$keyAc]))
																	{

																		$nameTaskAc[$keyAc]='';
																		$workloadAcForItem[$keyAc]=0;

																	}
																	$nameTaskAc[$keyAc] .= '<span>'.$value['data'][$key]['nameTask'].' ('.$value['data'][$key]['workload'].')</span>';
																	$workloadAcForItem[$keyAc] += $value['data'][$key]['workload'];

																	$workloadAc += $value['data'][$key]['workload'];
																	$summary += $value['data'][$key]['workload'];
                                                                }
																$summaryEmployeeOnDay=$workloadPr+$workloadAc;
                                                            }
                                                        }
                                                    }
													$namePr=!empty($namePr)?$namePr:array();
													$nameAc=!empty($nameAc)?$nameAc:array();
                                                    if(!$flag)
                                                    {
														//if(!empty())
														foreach($namePr as $_key=>$_name)
														{
															echo "<b>".$_name."</b>";echo "<br />";echo "<i class='workload-data'>".$workloadPrForItem[$_key]."</i>";
														}

														if($nameAc!='')
														{
															echo "<br />";
															foreach($nameAc as $_key=>$_name)
															{
																echo "<b>".$_name."</b>";echo "<br />";echo "<i class='workload-data'>".$workloadAcForItem[$_key]."</i>";
															}
															//echo "<b>".$nameAc."</b>";echo "<br />";echo "<i class='workload-data'>".$nameTaskAc."</i>";
														}
                                                    }
                                                    elseif($flag==1)
                                                    {
                                                        echo $text;
                                                    }
													elseif($flag==2)
                                                    {
                                                        echo $text;
														foreach($namePr as $_key=>$_name)
														{
															//echo "<b>".$_name."</b>";echo "<br />";echo "<i class='workload-data'>".$nameTaskPr[$_key]."</i>";
															echo "<b>".$_name."</b>";echo "<br />";echo "<i class='workload-data'>".$workloadPrForItem[$_key]."</i>";
														}

														if($nameAc!='')
														{
															echo "<br />";
															foreach($nameAc as $_key=>$_name)
															{
																echo "<b>".$_name."</b>";echo "<br />";echo "<i class='workload-data'>".$workloadAcForItem[$_key]."</i>";
															}
															//echo "<b>".$nameAc."</b>";echo "<br />";echo "<i class='workload-data'>".$nameTaskAc."</i>";
														}
                                                    }
													elseif($flag==3)
													{
														echo $text;
														echo $text1;
														foreach($namePr as $_key=>$_name)
														{
															echo "<b>".$_name."</b>";echo "<br />";echo "<i class='workload-data'>".$workloadPrForItem[$_key]."</i>";
														}

														if($nameAc!='')
														{
															echo "<br />";
															foreach($nameAc as $_key=>$_name)
															{
																echo "<b>".$_name."</b>";echo "<br />";echo "<i class='workload-data'>".$workloadAcForItem[$_key]."</i>";
															}
															//echo "<b>".$nameAc."</b>";echo "<br />";echo "<i class='workload-data'>".$nameTaskAc."</i>";
														}
													}
													elseif($flag==4)
													{
														echo $text;
														foreach($namePr as $_key=>$_name)
														{
															echo "<b>".$_name."</b>";echo "<br />";echo "<i class='workload-data'>".$workloadPrForItem[$_key]."</i>";
														}

														if($nameAc!='')
														{
															echo "<br />";
															foreach($nameAc as $_key=>$_name)
															{
																echo "<b>".$_name."</b>";echo "<br />";echo "<i class='workload-data'>".$workloadAcForItem[$_key]."</i>";
															}
															//echo "<b>".$nameAc."</b>";echo "<br />";echo "<i class='workload-data'>".$nameTaskAc."</i>";
														}
														echo $text1;
													}
													$totalCountDownCapacity+=$countDownCapacity;
													$totalCountDownWorkload+=$summaryEmployeeOnDay;
													if(isset($capacity[$countCell]['value']))
													{
														$capacity[$countCell]['value']+=$countDownCapacity;
													}
													else
													{
														$capacity[$countCell]['value']=$countDownCapacity;
													}
													if($countCell==$countWorkdays)
													{
														$employeeCustom[$id]['capacity']=caculateTwoNumber($countWorkdays,$totalCountDownCapacity);
														$employeeCustom[$id]['workload']=$totalCountDownWorkload;
													}
													echo "</td>";

													if($countRow1==$countEmployees)
													{
														$ttt = caculateTwoNumber($capacity[$countCell]['value'],0);
														$totalCapacity+=$ttt;
														$tdsSummary.="<td class='ct ".$fw." summary-".$countRow1.'-'.$countCell."'>".(caculateTwoNumber($countEmployees,$capacity[$countCell]['value']))."</td>";
													}
                                                }
                                                echo "</tr>";
												if($employeeCustom[$id]['workload']>$employeeCustom[$id]['capacity'])
												{
													$clsTemp='check-workload';
												}
												else
												{
													$clsTemp='';
												}
												$a.="<tr class='fixedHeight tdRightRow-".$id."'><td class='st ab-name'>".$employees[$id]."</td><td id='totalWorkload-".$id."' class='ct ".$clsTemp."'><b class='workload'>".$employeeCustom[$id]['workload']."</b>/<b class='capacity'>".$employeeCustom[$id]['capacity']."</b></td></tr>";
                                     		}
                                    	}
                                     }
                                     ?>
                                     <tr ><td colspan="<?php echo $countWorkdays;?>"></td></tr>
									<tr><?php echo $tdsSummary;?></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
function caculateTwoNumber($x,$y)
{
	$x=number_format($x,2, '.', '');
	$y=number_format($y,2, '.', '');
	return $x-$y;
}
/*$i18ns = array(
    'Add a comment' => __('Add a comment', true),
    'Summary' => __('Summary', true),
    'Holiday' => __('Holiday', true),
    'Unknown' => __('Unknown', true),
    'Requested' => __('Requested', true),
    'Validated' => __('Validated', true),
    'No name' => __('No name', true),
    'Detail of %s' => __('Detail of %s', true),
    'Remove forecast' => __('Remove forecast', true),
);*/
$css = '';
foreach ($constraint as $key => $data) {
    $css .= ".rp-$key {background-color : {$data['color']};}";
}
echo '<style type="text/css">' . $css . '</style>';
?>
<div style="display: none;" id="message-template">
    <div class="message error"><?php echo __('Cannot connect to server ...', true); ?><a href="#" class="close">x</a></div>
</div>
<!-- dialog_vision_portfolio -->
<div id="add-comment-dialog" class="buttons" style="display: none;" title="<?php echo __('Add new comments', true) ?>">
    <fieldset>
        <textarea rel="no-history" name="comment"></textarea>
    </fieldset>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="ok"><?php __('OK') ?></a></li>
    </ul>
</div>
<!-- dialog_vision_portfolio.end -->
<div id="tooltip-template" class="buttons" style="display: none;">
    <dl id="tooltip-template-dl">
        <dt><?php __('Short name'); ?> :</dt>
        <dd>%1$s</dd>
        <dt><?php __('Long name'); ?> :</dt>
        <dd>%2$s</dd>
        <dt><?php __('Family'); ?> :</dt>
        <dd>%3$s</dd>
        <dt><?php __('Subfamily'); ?> :</dt>
        <dd>%4$s</dd>
    </dl>
</div>
<?php
$totalCapacity=($countEmployees*$countWorkdays)-$totalCapacity;
$textSummary=round($summary,2)."/".round($totalCapacity,2);

 ?>
<script type="text/javascript">
	$(<?php echo json_encode($a)?>).insertBefore( "#affterLeft" );
    <?php
    $month = date('m', $_start);
    $week = date('W', $_start);
    $year = date('Y', $_start);
    $profit = !empty($this->params['url']['profit']) ? $this->params['url']['profit'] : '';
    ?>
    var $month = <?php echo json_encode($month);?>,
        $week = <?php echo json_encode($week);?>,
        $year = <?php echo json_encode($year);?>,
        $profit = <?php echo json_encode($profit);?>;
    $('#typeRequest').change(function () {
        var linkRequest = '<?php echo $this->Html->url('/') ?>activity_forecasts/my_diary/';
        if($(this).val() == 'week'){ // change month to week
            linkRequest += 'week';
        } else if($(this).val() == 'month'){ // change week to month
            linkRequest += 'month';
        } else { // change to year
            $month = 1;
            linkRequest += 'year';
        }
        var refreshLink = '';
        refreshLink = linkRequest + '?year=' + $year + '&month=' + $month + '&profit=' + $profit;
        window.location.href = refreshLink;
    });
	$('#summary').html(<?php echo json_encode($textSummary) ?>);
    <?php
	$j=0;
	foreach ($employees as $id => $employee) :
			$j++  ;?>
            var i = $('tr.tdLeftRow-<?php echo $id;?>').height();
			if($.browser.mozilla == true)
            $('tr.tdRightRow-<?php echo $id;?>').css("height",i);
			else
			$('tr.tdRightRow-<?php echo $id;?>').css("height",i+1);
    <?php endforeach;?>
</script>
