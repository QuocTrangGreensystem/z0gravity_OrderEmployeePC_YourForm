<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->script('history_filter'); ?>
<?php echo $html->css('slick_grid/slick.grid'); ?>
<?php echo $html->css('slick_grid/slick.pager'); ?>
<?php echo $html->css('slick_grid/slick.common'); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
 .slick-cell .multiSelect{width:auto;display:block;overflow:hidden;text-overflow:ellipsis;}.wd-select-box{border:#d8d8d8 solid 1px;padding:6px;}.wd-input-select{padding-left:40px;padding-top:10px;font-weight:700;}.wd-input-select label{display:inline-block;min-width:375px;}.wd-save{float:left;background:url(../img/front/bg-submit-save.png) no-repeat left top;cursor:pointer;height:33px;width:82px;border:none;font-size:0;}
 .wd-list-project .wd-tab .wd-content label{
	 width: 400px;
	 margin-top: 7px;
 }
</style>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">

        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
                                <h2 class="wd-t3"></h2>
                                <div id="message-place">
                                    <?php
                                    App::import("vendor", "str_utility");
                                    $str_utility = new str_utility();
                                    echo $this->Session->flash();
                                    ?>
                                </div>
                                <div class="wd-table" id="project_container">
                                    <div id="wd-select">
                                        <?php
                                            $datas = array(
                                                    0 => __("No", true),
                                                    1 => __("Yes", true),
                                                );
                                            echo $this->Form->create('Project', array('url' => array('controller' => 'project_settings', 'action' => 'index')));
                                        ?>
                                        <div class="wd-input-select">
                                            <label><?php echo __("Activate Freeze (Initial workload, Initial start, Initial end)", true)?></label>
                                            <?php
                                                echo $this->Form->input('show_freeze', array(
                                                    'div' => false, 
                                                    'label' => false,
                                                    "class" => "wd-select-box",
                                                    "default" => $setRemain['ProjectSetting']['show_freeze'],
                                                    "options" => $datas
                                                    ));
                                            ?>
                                        </div>
                                      
                                        <div class="wd-submit" style="margin-left: 38px; margin-top: 10px;">
                                            <button type="submit" class="wd-button-f wd-save-project" id="btnSave" />
                                                <span><?php __('Save') ?></span>
                                            </button>
                                        </div>
                                        <?php
                                            echo $this->Form->end();
                                        ?> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>