<?php echo $html->script('jquery.dataTables'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->script('jquery.validation.min'); ?>
<?php echo $html->script('jquery.treeTable'); ?>
<!--[if gte IE 9]>
  <style type="text/css">
    .gradient {
       filter: none;
    }
  </style>
<![endif]-->
<style>
    .wd-project-admin fieldset div.wd-input label { width: 29% !important;}
    fieldset div.wd-input div.error-message {padding-left: 31% !important;}
    #employee-place .ui-combobox, #employee-place-2 .ui-combobox{
        width: 63%;
    }
    #employee-place .ui-combobox input, #employee-place-2 .ui-combobox input{
        color: #000;
    }
fieldset div.wd-submit input.wd-save{background: url('<?php echo $this->Html->url('/img/front/bg-submit-save-new.png'); ?>') no-repeat left top !important;}
fieldset div.wd-submit input:hover{background-position:left -33px !important;}
a.wd-reset{background: url('<?php echo $this->Html->url('/img/front/bg-reload-new.png'); ?>') no-repeat left top !important;}
a.wd-reset:hover{background-position:left -33px !important;color:#000;text-decoration:none;}
.gradient {
    background: #ffffff;
    background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2ZmZmZmZiIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiNmMGYwZjAiIHN0b3Atb3BhY2l0eT0iMSIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+);
    background: -moz-linear-gradient(top, #ffffff 0%, #f0f0f0 100%);
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(100%,#f0f0f0));
    background: -webkit-linear-gradient(top, #ffffff 0%,#f0f0f0 100%);
    background: -o-linear-gradient(top, #ffffff 0%,#f0f0f0 100%);
    background: -ms-linear-gradient(top, #ffffff 0%,#f0f0f0 100%);
    background: linear-gradient(to bottom, #ffffff 0%,#f0f0f0 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#f0f0f0',GradientType=0 );
    padding: 5px 15px;
    border: 1px solid #d0d0d0;
    cursor: pointer;
    border-radius: 4px;
}
.gradient:hover,
.gradient:focus {
    border-color: #bbb;
}
.error-message {
    margin: 10px 0 0 0 !important;
}
</style>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
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
                                <h2 class="wd-t3"><?php echo __("Import project tasks", true); ?></h2>
                                <?php
                                echo $this->Session->flash();
                                echo $this->Form->create('Import', array('id' => 'uploadForm', 'type' => 'file',
                                    'url' => array('controller' => 'tasks', 'action' => 'import')));
                                ?>
                                <div class="wd-input">
                                    <label><?php echo __('File:') ?></label>
                                    <input type="file" name="FileField[csv_file_attachment]" />
                                    <button type="submit" id="import-submit" class="gradient" onclick="return false;" href="#"><?php echo __('Submit') ?></button>
                                    <button type="button" id="download-sample" class="gradient" href="#"><?php echo __('Example of CSV file') ?></button>
                                    <div style="clear:both; margin-top: 5px;color: #008000; font-style:italic;"><?php __('Allowed file type') ?>: *.csv</div>
                                    <div id="error"></div>
                                </div>
                                <?php echo $this->Form->end(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#download-sample').click(function(){
        location.href = '<?php echo $this->Html->url('/shared/sample-tasks.csv') ?>'
    });
    $("#import-submit").click(function(){
        $(".error-message").remove();
        $("input[name='FileField[csv_file_attachment]']").removeClass("form-error");
        if($("input[name='FileField[csv_file_attachment]']").val()){
            var filename = $("input[name='FileField[csv_file_attachment]']").val();
            var valid_extensions = /(\.csv)$/i;   
            if(valid_extensions.test(filename)){ 
                $('#uploadForm').submit();
                return true;
            }
            else{
                $("input[name='FileField[csv_file_attachment]']").addClass("form-error");
                jQuery('<div>', {
                    'class': 'error-message',
                    html: '<?php __('Incorrect type file') ?>'
                }).appendTo('#error');
            }
        }else{
            jQuery('<div>', {
                'class': 'error-message',
                html: '<?php __('Please choose a file!') ?>'
            }).appendTo('#error');
        }
        return false;
    });
</script>