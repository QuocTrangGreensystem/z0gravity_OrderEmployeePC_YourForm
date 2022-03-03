<?php echo $html->css(array('context/jquery.contextmenu')); ?>
<?php echo $html->script('context/jquery.contextmenu'); ?> 
<!-- export excel  -->
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'absence_requests', 'action' => 'export', $employeeName['id'], $employeeName['company_id'])));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<style type="text/css">
#absence-fixed th span{font-weight: normal;font-style: italic;}
#absence-scroll{overflow-x:scroll !important; }
#absence{width: 25% !important;float: left;}
#absence-scroll #absence-fixed{float: left;width: 100% !important;}
#absence-fixed tbody tr td{height: 22px;}
.end-absence{border-right: solid 1px red !important;}
#absence-fixed td.val{text-align: right;}
#thColID{ width:32px; } #thColEmployee{ }
.colV, .colW, .colR, .val{ width: 68px; overflow: hidden; min-width: 68px; max-width: 68px;}
.colThV, .colThW, .colThR { width: 60px; overflow: hidden; min-width: 60px; max-width: 60px; } 
.wd-tab .wd-panel{
	padding: 0;
	border: none;
}
.wd-table #table-control{
	margin-bottom: 0;
}
</style>
<!-- /export excel  -->
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
             <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"><div class="wd-panel">
            <div class="wd-list-project">
                <div id="message-place">
                    <?php
                    echo $this->Session->flash();
                    ?>
                </div>
                <div class="wd-table" id="project_container" style="width:100%;">
                    <div id="absence-container" class="wd-activity-actions" style="min-height:400px;">
                        <div id="table-control" style="padding: 10px 0 0">
                            <!-- Export excel - By QN 2014/12/23 -->
                            <?php
                            echo $this->Form->create(false, array('type' => 'GET', 'action' => 'reviews', 'id' => 'export-synthesis-form', 'style' => 'display: none'));
                            echo $this->Form->hidden('profit', array('value' => $profit['id']));
                            echo $this->Form->hidden('year', array('value' => date('Y', $_start)));
                            echo $this->Form->hidden('get_path', array('value' => @$_GET['get_path']));
                            echo $this->Form->hidden('export', array('value' => 1));
                            echo $this->Form->end();
                            ?>
                            <?php
                            echo $this->Form->create('Control', array(
                                'type' => 'get',
                                'url' => '/' . Router::normalize($this->here)));
                            ?>
                            <fieldset style="margin-left: 22px;">
                                <?php
                                echo $this->Form->year('year', date('Y', $_start) - 5, date('Y', $_start) + 2, date('Y', $_start), array('empty' => false, 'style' => 'padding: 6px'));
                                ?>
                                <?php
                                echo $this->Form->select('profit', $paths, $profit['id'], array('empty' => false, 'escape' => false, 'style' => 'padding: 6px'));
                                ?>
                                <button class="btn btn-go"></button>
                                <?php
                                    echo $this->element('btn_expand_pc');
                                ?>
                                <a id="absence-prev" href="<?php echo $this->Html->here . '?year=' . (date('Y', $_start) - 1) . '&profit=' . $profit['id']; ?>">
                                    <span>Prev</span>
                                </a>
                                 <span class="currentWeek"><?php echo __(date('Y', $_start));?></span>
                                <a id="absence-next" href="<?php echo $this->Html->here . '?year=' . (date('Y', $_start) + 1) . '&profit=' . $profit['id']; ?>">
                                    <span>Next</span>
                                </a>
                                <a style="margin-left: 5px" class="export-excel-icon-all" href="javascript:;" onclick="$('#export-synthesis-form').submit()" title="<?php __('Export excel') ?>"><span><?php __('Export excel') ?></span></a>
                                <div style="clear:both;"></div>
                            </fieldset>
                            <?php
                            echo $this->Form->end();
                            ?>
                        </div>
                        <div id="absence-wrapper">
                        	<div id="scrollTopAbsence" class="useLeftScroll"><div id="scrollTopAbsenceContent"></div></div>
                        	<br clear="all"  />
                            <div id="scrollLeftAbsence">
                            	<div id="scrollLeftAbsenceContent"></div>
                            </div>
                            <table id="absence">
                            <tr class="elmTemp">
                            <td class="elmTemp">
                            <table>
                                <thead>
                                    <tr class="height-absence header-height-fixed">
                                        <th id="thColID"><?php echo __('No.', true); ?></th>
                                        <th id="thColEmployee"><?php __('Employee'); ?></th>
                                    </tr> 
                                </thead>
                             </table>
                             </td>
                             </tr>
                             <tr class="elmTemp">
                                <td class="elmTemp">
                                    <div class="tbl-tbody" >
                                    <table>
                                    	<tbody id="absence-table">
                                        	 <?php $i = 1; ?>
                                        <?php 
                                        asort($employees);
                                        foreach ($employees as $id => $employee) : ?>
                                            <tr class="heightColLeft" id="heightColLeft-<?php echo $i; ?>">
                                                <td class="st1" >
                                                    <?php echo $i++; ?>
                                                </td>
                                                <td class="st1 ab-name">
                                                    <?php echo $employee; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach;?>
                                        </tbody>
                                 	</table>
                                 </div>
                                 </td>
                             </tr>       
                            </table>    
                            <div id="absence-scroll">
                                <table id="absence-fixed">
                                    <tr class="elmTemp">
                                    <td class="elmTemp">
                                    <table>
                                        <thead>
                                            <tr class="header-height height-absence-1">
                                            <?php 
                                            foreach ($absences as $absence) :  ?>
                                                <th colspan="3"><?php echo $absence['type']; 
                                                    if($absence['begin']!='0000-00-00'){
                                                        $beginD = explode('-',$absence['begin']);
                                                        $startD = strtotime($beginD[2].'-'.$beginD[1].'-'.date('Y',$_start));
                                                        if($startD > strtotime(date('d-m').'-'.date('Y',$_start)) && $yearSystem <= date('Y',$_start)){
                                                            $currentY = date('Y',$_start)-1;
                                                            $startD = strtotime($beginD[2].'-'.$beginD[1].'-'.$currentY);
                                                        }
                                                        $startE = strtotime("+1 year", $startD);
                                                        $startE = strtotime("-1 day", $startE);
                                                        echo '<span>';
                                                        echo sprintf(__(' (from %s to %s)', true), date('d/m/Y',$startD), date('d/m/Y',$startE));
                                                        echo '</span>';
                                                    }
                                                ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                        <tr class="height-absence-2">
                                            <?php $j=0; foreach ($absences as $absence) : $j++; ?>
                                                <th id="colThV<?php echo $j;?>" class="colThV"><?php echo __('Validated');?></th>
                                                <th id="colThW<?php echo $j;?>" class="colThW"><?php echo __('Waiting');?></th>
                                                <th id="colThR<?php echo $j;?>" class="colThR"><?php echo __('Remain');?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </table>
                                    </td></tr>
                                    <tr class="elmTemp"><td class="elmTemp">
                                    <div class="tbl-tbody" >
                                    <table >
                                    <tbody>
                                        <?php $i = 1; ?>
                                        <?php 
                                        asort($employees);
                                        foreach ($employees as $id => $employee) : ?>
                                            <tr class="heightColRight" id="heightColRight-<?php echo $i; ?>">
                                                <?php $j=0; foreach ($absences as $absence) : $j++; ?>
                                                    <td class="val colV<?php echo $j;?>">
                                                        <?php
                                                        if (isset($_absences[$id][$absence['id']])) {
                                                            $absence = $_absences[$id][$absence['id']];
                                                        }
                                                        if (isset($requests[$id][$absence['id']])) {
                                                            if ($absence['total']) {
                                                                echo sprintf('%s', $requests[$id][$absence['id']]);
                                                            } else {
                                                                echo sprintf('%s', $requests[$id][$absence['id']]);
                                                            }
                                                        } else {
                                                           if ($absence['total']) {
                                                                echo sprintf('%s', '0');
                                                            } else {
                                                                echo sprintf('%s', '0');
                                                            }
                                                        }
                                                        ?>

                                                    </td>
                                                    <td class="val colW<?php echo $j;?>">
                                                        <?php
                                                        if (isset($_absences[$id][$absence['id']])) {
                                                            $absence = $_absences[$id][$absence['id']];
                                                        }
                                                        if (isset($waitings[$id][$absence['id']])) {
                                                            if ($absence['total']) {
                                                                echo sprintf('%s', $waitings[$id][$absence['id']]);
                                                            } else {
                                                                echo sprintf('%s', $waitings[$id][$absence['id']]);
                                                            }
                                                        } else {
                                                           if ($absence['total']) {
                                                                echo sprintf('%s', '0');
                                                            } else {
                                                                echo sprintf('%s', '0');
                                                            }
                                                        }
                                                        ?>

                                                    </td>
                                                    <td class="val colR<?php echo $j;?> end-absence">
                                                        <?php
                                                        if (isset($_absences[$id][$absence['id']])) {
                                                            $absence = $_absences[$id][$absence['id']];
                                                        }
                                                        if ($absence['total']) {  
                                                            $_total = $absence['total']; 
                                                            if (isset($requests[$id][$absence['id']])) {
                                                                $_total -= $requests[$id][$absence['id']];
                                                            }
                                                            if (isset($waitings[$id][$absence['id']])) {
                                                                $_total -= $waitings[$id][$absence['id']];
                                                            }
                                                            echo sprintf('%s', $_total);
                                                        } else {
                                                            echo sprintf('%s', '0');
                                                        }
                                                        ?>

                                                    </td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php $i++; endforeach; ?>
                                    </tbody>
                                </table>
                                    </div>
                                    </td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div></div></div>
        </div>
    </div>
</div>
<style>
.heightColLeft, heightColRight{
    padding-top:0 !important;
    padding-bottom:0 !important;
    overflow:hidden !important;
}
</style>
<script type="text/javascript">
var heightHeader = $('#absence-fixed .height-absence-1').height();
heightHeader+= $('#absence-fixed .height-absence-2').height();
$('#absence .height-absence').css("height", heightHeader+4);
fixedHeightScreen();
$(window).resize(function(e) {
    fixedHeightScreen();
	configSizeScroll();
});
function fixedHeightScreen(){
    var temp = 2;
    if($.browser.mozilla == true)
    {
        /*temp = 0;
        $('#absence thead').height($('#absence-fixed thead').height());
        $('#absence-fixed thead').height($('#absence thead').height());*/
    }
    $('.heightColLeft').each(function(index, element) {
        index = index + 1;
        $('#heightColRight-'+index).height($('#heightColLeft-'+index).height()+temp);
        
        $('#heightColLeft-'+index).height($('#heightColRight-'+index).height());
    });
}
function configSizeScroll(hd){
		//hd:hright default
		$("#scrollTopAbsenceContent").width($("#absence-fixed").width());
		$("#scrollTopAbsence").width($("#absence-scroll").width());
		$("#scrollLeftAbsenceContent").height($("#absence-table").height());
		var hHead=$('.header-height').height()*1.5+5;
		$("#scrollLeftAbsence").css({'marginTop':(hHead)+'px'});
		$(".st").width($("#thColID").width()+10);
		$(".st.ab-name").width($("#thColEmployee").width()+7);
		//$(".workday").width($(".colThDay").width()/2);
		/*var count = '<?php echo count($absences)+1;?>';
		for(var i=1;i<count;i++)
		{
			$(".colV"+i).width($("#colThV"+i).width());
			$(".colW"+i).width($("#colThW"+i).width());
			$(".colR"+i).width($("#colThR"+i).width());
		}*/
		if(hd!=600)
		{
			hd=hd-hHead-25;
		}
		$('.tbl-tbody').height(hd);
		$("#scrollLeftAbsence").height(hd);
	}
	var temp=setInterval(function(){
		$(window).resize();
		configSizeScroll(600);
		clearInterval(temp);
	},100);
	$(function () {
        var allowScrollWindow = true;
        var abc = 0;
        $(document).on('onmousewheel wheel onmousewheel mousewheel DOMMouseScroll', function(event, delta) {
			if(event.originalEvent.wheelDelta)
			{
				delta = event.originalEvent.wheelDelta;
			}
			else
			{
				delta = event.originalEvent.deltaY * -1;
			}
			if(allowScrollWindow == false)
			{
				if(delta < 0)
				{
					abc = abc == $("#absence-table").height() ? $("#absence-table").height() : abc + 120;
				}
				else
				{
					abc = abc == 0 ? abc : abc - 120;
				}
				//$("#scrollLeftAbsence").scrollTop(abc);
				$('#scrollLeftAbsence').animate({scrollTop:abc},'fast');
				//$(".tbl-tbody").scrollTop(abc);
				//$("#absence-table-fixed").scrollTop(abc);
				return false;
			}
		});
		$("#scrollTopAbsence").scroll(function () {
			//$('.separator-week').parent().addClass('separator-week-div');
			//$('.disable-edit-day').parent().addClass('disable-edit-day-div');
			//$(".slick-viewport-right").scrollLeft($("#absence-scroll").scrollLeft());
			$("#absence-scroll").scrollLeft($("#scrollTopAbsence").scrollLeft());
		});
		$("#absence-scroll").scroll(function () {
			//$('.separator-week').parent().addClass('separator-week-div');
			//$('.disable-edit-day').parent().addClass('disable-edit-day-div');
			//$(".slick-viewport-right").scrollLeft($("#absence-scroll").scrollLeft());
			$("#scrollTopAbsence").scrollLeft($("#absence-scroll").scrollLeft());
		});
		$("#scrollLeftAbsence").scroll(function () {
			$(".tbl-tbody").scrollTop($('#scrollLeftAbsence').scrollTop());
			$("#absence-table-fixed").scrollTop($('#scrollLeftAbsence').scrollTop());
		});
        
        $("#absence-scroll").mouseover(function(e) {
		  allowScrollWindow = false;
           // $('html').css({"overflow":"hidden"});
        });
    	$("#absence-scroll").mouseout(function(e) {
    		allowScrollWindow = true;
            //$('html').css({"overflow":"auto"});
        });
    	$("#absence-fixed").mouseover(function(e) {
    		allowScrollWindow = false;
           // $('html').css({"overflow":"hidden"});
        });
    	$("#absence-fixed").mouseout(function(e) {
    		allowScrollWindow = true;
            //$('html').css({"overflow":"auto"});
        });
	});
</script>