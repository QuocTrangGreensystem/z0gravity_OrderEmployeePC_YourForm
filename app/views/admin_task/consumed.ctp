<?php
echo $html->script('jquery.multiSelect');
echo $html->css('jquery.multiSelect');
echo $html->script('history_filter');
echo $html->css('slick_grid/slick.grid');
echo $html->css('slick_grid/slick.pager');
echo $html->css('slick_grid/slick.common');
echo $html->css('slick_grid/slick.edit');
echo $html->css('preview/tab-admin');
echo $html->css('layout_admin_2019');
?>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
 .slick-cell .multiSelect{width:auto;display:block;overflow:hidden;text-overflow:ellipsis;}.wd-select-box{border:#d8d8d8 solid 1px;padding:6px;}.wd-input-select{padding-left:40px;padding-top:10px;font-weight:700;}.wd-input-select label{display:inline-block;min-width:375px;}.wd-save{float:left;background:url(../img/front/bg-submit-save.png) no-repeat left top;cursor:pointer;height:33px;width:82px;border:none;font-size:0;}
.wd-input-select select {
	float: none;
}
.wd-input-select label {
	min-width: 200px;
}
.wd-list-project .wd-tab .wd-content label {
	width: auto;
	margin: 7px 10px 0 0;
}
</style>
<div id="wd-container-main" class="wd-project-admin">
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
                                <div class="wd-table" id="project_container" style="width:100%;height:400px;">
                                    <div id="wd-select">
                                        <?php
                                            echo $this->Form->create('Project', array('url' => array('controller' => 'admin_task', 'action' => 'consumed')));
                                        ?>
                                        <div class="wd-input-select">
                                            <label><?php echo __("Activate Manual Consumed", true)?></label>
                                            <?php
                                                $option = array(__('No', true), __('Yes', true));
                                                echo $this->Form->input('manual_consumed', array(
                                                    'div' => false,
                                                    'label' => false,
                                                    'onchange' => "editMe('manual_consumed', this.value);",
                                                    "class" => "wd-select-box",
                                                    "default" => &$companyConfigs['manual_consumed'],
                                                    "options" => $option,
                                                    "rel" => "no-history"
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
<script>
var loading = "<span id='loadingElm'><img src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt='Loading' /></span>";
function editMe(field,value) {
    $(loading).insertAfter('#'+field);
    var data = field+'/'+value;
    $.ajax({
        url: '/company_configs/editMe/',
        data: {
            data : { value : value, field : field }
        },
        type:'POST',
        success:function(data) {
            $('#'+field).removeClass('KO');
            $('#loadingElm').remove();
        }
    });
}
</script>
