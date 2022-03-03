<style type="text/css">.error-info li{margin-left:20px;list-style:decimal;}.buttons ul.type_buttons{padding-right:2px!important;}table tbody tr:hover td{}center{color:red;font-weight:700;}.ui-dialog{font-size:11px;}#dialog_import_CSV label{color:#000;}.type_buttons .error-message{background-color:#FFF;clear:both;color:#D52424;display:block;width:212px;padding:5px 0 0;}.form-error{border:1px solid #D52424;color:#D52424;}.import-fieldset{border:1px solid #D4D0C8;margin-bottom:20px;padding:5px;}.import-legend{font-size:14px;font-weight:700;padding:0 4px;}.table-info{min-width:200px;}.table-col{min-width:200px;white-space:nowrap;overflow:hidden;}
.display th, .display td { vertical-align: middle; }
.dataImport{overflow-x:scroll;overflow-y:scroll;max-height:300px;}.error,.error td,.error1{background:#FF6F6F;}
#wd-container-footer{margin: 0px !important;}#wd-container-main{padding-bottom: 0px !important;}
.buttons ul.type_buttons {
    height: 33px;
}
.buttons ul.type_buttons li{
    float: right;
    margin-left: 0px;
    margin-top: 3px;
}
.buttons ul.type_buttons li a {
    text-indent: -9999px;
    height: 33px;
}
.buttons ul.type_buttons li a.export-excel-icon-all{
    margin-top: -5px;
}
</style>
<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<div id="wd-container-main">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <!--div class="wd-title">
                    <h2 class="wd-t1"><?php //echo __("Import CSV File Review", true); ?></h2>
                </div-->
                <?php
                echo $this->Session->flash();
                echo $this->Form->create('Import', array('id' => 'import-form', 'url' => array('controller' => 'project_tasks', 'action' => 'save_file_import', $project_id)));
                ?>
                <?php 
                foreach ($records as $type => $record) : ?>
                    <?php $no = 1; ?>
                    <div class="import-fieldset" data-type="<?php echo $type;?>">
                        <!--legend class="import-legend">
                            <?php
                            /*
                            switch ($type) {
                                case 'Create':
                                    echo __('New Employees', true);
                                    break;
                                case 'Update':
                                    echo __('Update Employees', true);
                                    break;
                                default:
                                case 'Error':
                                    echo __('Error Employees', true);
                                    break;
                            }*/
                            ?>
                        </legend-->
                        <div class="dataImport">
                        <table border="0" cellspacing="1" cellpadding="3" class="display">
                            <thead>

                                <tr class="wd-header">
                                <?php foreach($default as $key => $titleColumn){ ?>
                                <th class="table-col colspan<?php echo ($key == 'Parent Task Name' || $key == 'Description') ? 5 : 2;?>"><?php __($key); ?></th>
                                <?php } ?>
                                <th class="table-info"><?php __('Description'); ?></th>
                                </tr>

                            </thead>

                            <tbody>
                                <?php foreach ($record as $data) :  ?>

                                    <tr>
                                     <?php foreach($default as $key => $titleColumn){
                                            $heighLigh= false;
                                            if(!empty($data['columnHighLight'])){
                                                if(isset($data['columnHighLight'][$key])){
                                                    $heighLigh = true;
                                                }
                                            }
                                     ?>
                                    <td class="table-col"  <?php if($heighLigh) echo 'style="color: #F71230; border-color: red"'; ?>  ><?php echo $data[$key]; ?></td>
                                    <?php } ?>
                                    <td class="table-info">
                                        <?php
                                        echo $this->Html->nestedList($data['error'], array('class' => 'error-info'));
                                        $import = array();

                                        if (!empty($data['data'])) {
                                            $import['do'] = $data['data'];
                                        }
                                        unset($data['error'], $data['data']);


                                        $import['export'] = $data;
                                        foreach ($import as $action => $_data) {
                                            foreach ($_data as $_key => $value) {
                                                if($_key!='description'&&$_key!='columnHighLight'){
                                                    if ($_key === 'function_id') {
                                                        foreach ($value as $_key => $_value) {
                                                            echo $this->Form->hidden($type . '.' . $action . '.' . $no . '.function_id.' . $_key . '.', array('value' => $_value));
                                                        }
                                                        continue;
                                                    }

                                                    echo $this->Form->hidden($type . '.' . $action . '.' . $no . '.' . $_key, array('value' => $value));
                                                }
                                            }
                                        }
                                        ?>
                                    </td>
                                    </tr>
                                    <?php $no++; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        </div>
                        <div style="text-align: right; margin-top: 5px">
                            <?php if ($type === 'Create') : ?>
                                <a id="submit-create-do" class="btn btn-ok" href="javascript:void(0)"></a>
                            <?php endif; ?>
                            <a id="submit-error-export" class="btn btn-excel" href="javascript:void(0)"></a>
                        </div>
                    </fieldset>
                </div>
                <?php endforeach; ?>
                <?php
                echo $this->Form->hidden('task', array('value' => '', 'id' => 'import-task'));
                echo $this->Form->hidden('type', array('value' => '', 'id' => 'import-type'));
                echo $this->Form->end();
                ?>
                <div class="wd-title">
                    <a class="btn-text" id="submit-export-all" href="javascript:void(0)" style="margin-right:5px;">
                        <img src="<?php echo $this->Html->url('/img/ui/blank-plus.png') ?>" alt="" />
                        <span><?php __('Do all action') ?></span>
                    </a>
                </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function($){

        $(function(){
            function submitForm(type,task){
                $('#import-task').val(task);
                $('#import-type').val(type);
                $('#import-form').submit();
            }

            $('#submit-create-export').click(function(){
                submitForm('Create','export');
            });
            $('#submit-create-do').click(function(){
                submitForm('Create','do');
            });

            $('#submit-update-export').click(function(){
                submitForm('Update','export');
            });
            $('#submit-update-do').click(function(){
                submitForm('Update','do');
            });

            $('#submit-error-export').click(function(){
                submitForm('Error','export');
            });
            $('#submit-export-all').click(function(){
                submitForm('Create,Update','do');
            });

        });
    })(jQuery);
</script>
