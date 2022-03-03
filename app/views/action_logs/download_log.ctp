<?php
$employee_info = $this->Session->read("Auth.employee_info");
?>
<style>
.inputConfig{
    width:240px;
    padding:5px ;
    border:1px solid #CCC !important;
}
select.inputConfig{
    width:252px;
    background:none !important;
}
.inputConfig:focus{
    background: url('/img/edit.png') 230px 5px  no-repeat ;
}
.wd-input-select label{display:inline-block;min-width:175px; font-weight:bold;}
.wd-input-select{ margin:5px 0; }
.section_cf{ display:none }
.loadingElm img{
    margin-top:5px;
}
</style>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                </div>
                <?php
                App::import("vendor", "str_utility");
                $str_utility = new str_utility();
                ?>
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                                <?php echo $this->element('administrator_left_menu') ?>
                                <div class="wd-content">
                                    <a id="btnSave" class="wd-button-f wd-save-project" href="<?php echo $html->url('/action_logs/download_log_confirm'); ?>">
                                    <span>Download</span>
                                    </a>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
