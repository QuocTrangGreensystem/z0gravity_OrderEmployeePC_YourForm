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
#absence th span{font-weight: normal;font-style: italic;}
#absence-wrapper{overflow-x:scroll !important; }
.end-absence{border-right: solid 1px red  !important;}
#absence-table td.val{text-align: right;}
.wd-tab .wd-panel{
	padding: 0;
	border: none;
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
                <div class="wd-title" style="margin:0 22px;">
                     <!-- <h2 class="wd-t1"><?php echo sprintf(__("Absence Review %s", true), date('Y', $_start)); ?></h2> -->
                    <div id="table-control">
                        <?php
                        echo $this->Form->create('Control', array(
                            'type' => 'get',
                            'url' => '/' . Router::normalize($this->here)));
                        ?>
                        <fieldset>
                            <!--<?php
                                echo $this->Form->year('year', date('Y', $_start) - 5, date('Y', $_start) + 2, date('Y', $_start), array('empty' => false, 'style' => 'padding: 6px'));
                            ?>
                            <button class="btn btn-go"></button>-->
                            <a id="absence-prev" href="<?php echo $this->Html->here . '?year=' . (date('Y', $_start) - 1); ?>">
                            <span>Prev</span>
                            </a>
                            <span class="currentWeek"><?php echo __(date('Y', $_start));?></span>
                            <a id="absence-next" href="<?php echo $this->Html->here . '?year=' . (date('Y', $_start) + 1); ?>">
                                    <span>Next</span>
                            </a>
                            <div style="clear:both;"></div>
                        </fieldset>
                        <?php
                        echo $this->Form->end();
                        ?>
                    </div>
                </div>
                <div id="message-place">
                    <?php
                    echo $this->Session->flash();
                    ?>
                </div>
                <div class="wd-table" id="project_container" style="width:100%;">
                    <div id="absence-container" style="min-height:400px;">
                        <div id="absence-wrapper">
                            <table id="absence">
                                <thead>
                                    <tr>
                                        <?php 
                                        foreach ($absences as $absence) : ?>
                                            <th colspan="3"><?php echo $absence['type']; 
                                                if($absence['begin']!=''){
                                                    $beginD = explode('-',$absence['begin']);
                                                    $startD = strtotime($beginD[1].'-'.$beginD[0].'-'.date('Y',$_start));
                                                    if($startD > strtotime(date('m/d').'/'.date('Y',$_start)) && $yearSystem <= date('Y',$_start)){
                                                        $currentY = date('Y',$_start)-1;
                                                        $startD = strtotime($beginD[1].'-'.$beginD[0].'-'.$currentY);
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
                                    <tr>
                                        <?php foreach ($absences as $absence) : ?>
                                            <th><?php echo __('Validated');?></th>
                                            <th><?php echo __('Waiting');?></th>
                                            <th><?php echo __('Remain');?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody id="absence-table">
                                    <tr>
                                        <?php foreach ($absences as $absence) : ?>
                                        <td class="val">
                                                <?php
                                                
                                                if (isset($requests[$absence['id']])) {
                                                    if ($absence['total']) {                                                        
                                                        $_total = $absence['total'];
                                                        echo sprintf('%1$s', $requests[$absence['id']]);
                                                    } else {
                                                        echo sprintf('%1$s', $requests[$absence['id']]);
                                                    }
                                                } else {
                                                    if ($absence['total']) {                                                        
                                                        $_total = $absence['total'];
                                                        echo sprintf('%1$s', '0');
                                                    } else {
                                                        echo sprintf('%1$s', '0');
                                                    }
                                                }
                                            ?>
                                        </td>
                                        <td class="val">
                                            <?php 
                                                if (isset($waitings[$absence['id']])) {
                                                    if ($absence['total']) {                                                        
                                                        $_total = $absence['total'];
                                                        echo sprintf('%1$s', $waitings[$absence['id']]);
                                                    } else {
                                                        echo sprintf('%1$s', $waitings[$absence['id']]);
                                                    }
                                                } else {
                                                    if ($absence['total']) {                                                        
                                                        $_total = $absence['total'];
                                                        echo sprintf('%1$s', '0');
                                                    } else {
                                                        echo sprintf('%1$s', '0');
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td class="val end-absence">
                                            <?php 
                                                if ($absence['total']) {  
                                                    $_total = $absence['total']; 
                                                    if ( isset($requests[$absence['id']]) ) {
                                                        $_total -= $requests[$absence['id']];
                                                    }
                                                    if ( isset($waitings[$absence['id']]) ) {
                                                        $_total -= $waitings[$absence['id']];
                                                    }
                                                    echo sprintf('%s', $_total);
                                                } else {
                                                    echo sprintf('%s', '0');
                                                }

                                                ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div></div></div>
        </div>
    </div>
</div>