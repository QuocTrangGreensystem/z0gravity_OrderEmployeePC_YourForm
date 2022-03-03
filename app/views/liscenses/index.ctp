<?php echo $html->script('jquery.validation.min'); ?>
<?php echo $html->script('jquery.dataTables'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<?php
$employee_info = $this->Session->read("Auth.employee_info");
?>
<style>
    #wd-form{
        width: 100%;
        float: left;
        margin-top: 5px;
        padding-left: 2px;
    }
    .wd-form-key{
        margin-top: 20px;
    }
    .wd-form-key form label{
        color: black;
        display: block;
        font-size: 18px;
        font-weight: bold;
        margin: 5px;
    }
    .wd-form-key form input[type="file"]{
        border: 1px solid silver;
        width: 100%;
        height: 30px;
    }
    .wd-form-key form input[type="file"]:hover{
        cursor: pointer;
    }
    .wd-form-key form input[type="submit"]{
        border: 1px solid silver;
        display: block;
        padding: 6px;
        margin-top: 5px;
        margin-bottom: 5px;
        cursor: pointer;
        background: url('/img/bg-button.png') repeat-x;
        color: white;
    }
    h1.wd-p{
        background: url('/img/bg_dot.png') repeat-x center;
    }
    h1.wd-p span{
        background-color: white;
        display: inline-block;
        padding-right: 10px;
        font-size: 14px;
    }
    .wd-mes{
        margin-top: 20px;
        margin-left: 10px;
        margin-bottom: 20px;
    }
    .wd-mes p{
        font-size: 15px;
    }
    .wd-mes p span{
        font-weight: bold;
        color: red;
    }
</style>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                <!--<h2 class="wd-t1"><?php echo __("Countries Listing", true); ?></h2>-->
                </div>
                <?php
                App::import("vendor", "str_utility");
                $str_utility = new str_utility();
                //debug()
                ?>
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
                                <!-- <h2 class="wd-t3"></h2> -->
                                <?php echo $this->Session->flash(); ?>
                                <div id="wd-form">
                                    <!-- <h1 class="wd-p"><span><?php __("License Infomation  ") ?></span></h1> -->
                                    <div class="wd-mes">
                                        <p><?php __('Domain Name') ?>: <span><?php echo isset($domain) ? implode(', ', $domain) : '';?></span></p>
                                        <p><?php __('Expire Date') ?>: <span><?php echo isset($licensesDate) ? $licensesDate : 00-00-0000;?></span></p>
                                        <p><?php printf(__('Your license will expired in <span>%s</span> days', true), isset($dateRange) ? $dateRange : 0) ?></p>
                                    </div>
                                    <h1 class="wd-p"><span style="color: red;"><?php __("Upload license") ?></span></h1>
                                    <?php
                                        if (($is_sas == 1) || ($employee_info["Role"]["name"] == "admin")) {
                                    ?>
                                    <div class="wd-form-key">
                                    <?php
                                        echo $this->Form->create('Project', array(
                                            'enctype' => 'multipart/form-data', 
                                            'url' => array('controller' => 'liscenses', 'action' => 'index')
                                        ));
                                    ?>
                                    <?php echo $this->Form->input('key', array('type' => 'file', 'label' => false, 'div' => false));?>
                                    <?php
                                        echo $this->Form->end(array('label' => __('Save', true), 'disabled' => 'disabled', 'id' => 'projectSB'));
                                    ?>
                                    <?php
                                        }
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
    $('#ProjectKey').change(function(){
        var data = $('#ProjectKey').val();
        if(data){
            $('#projectSB').removeAttr('disabled');
        }
    });
</script>