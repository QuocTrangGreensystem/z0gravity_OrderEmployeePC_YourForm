<style type="text/css">
    .error-info li{
        margin-left: 20px;
        list-style: decimal;
    }
    .buttons ul.type_buttons {
        padding-right: 11px !important;
    }
    .error, .error td{ background: #FF6F6F}
    .error1{ background: #FF6F6F}
    table tbody tr:hover td {
        background: none !important;
    }
    center {color:red; font-weight: bold}
</style>

<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<style>
    .ui-dialog {font-size: 11px}
    #dialog_import_CSV label{color:black}
    .buttons ul.type_buttons {padding-right: 2px !important}
    .type_buttons .error-message {
        background-color: #FFFFFF;
        clear: both;
        color: #D52424;
        display: block;
        padding: 5px 0 0 0px;
        width: 212px;
    }
    .form-error {
        border: 1px solid #D52424;
        color: #D52424;
    }
    .import-fieldset{
        border: 1px solid #D4D0C8;
        padding:5px;
        margin-bottom: 20px
    }
    .import-legend{
        font-size: 14px;
        padding: 0px 4px;
        font-weight: bold;
    }
    .table-info{
        width: 20%;
    }
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
<div id="wd-container-main" class="wd-project-index">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                    <h2 class="wd-t1"><?php echo __("Import CSV File Review", true); ?></h2>
                </div>
                <?php
                echo $this->Session->flash();
                echo $this->Form->create('Import', array('id' => 'import-form', 'url' => array('controller' => 'absences', 'action' => 'save_file_import')));
                ?>
                <?php foreach ($records as $type => $record) : ?>
                    <?php $no = 1; ?>
                    <fieldset class="import-fieldset">
                        <legend class="import-legend">
                            <?php
                            switch ($type) {
                                case 'Create':
                                    echo __('New Absences', true);
                                    break;
                                case 'Update':
                                    echo __('Update Absences', true);
                                    break;
                                default:
                                case 'Error':
                                    echo __('Error Absences', true);
                                    break;
                            }
                            ?>
                        </legend>
                        <table border="0" cellspacing="1" cellpadding="3" class="display">
                            <thead>

                                <tr class="wd-header">
                                <th class="table-no"><?php __('No.'); ?></th>
                                <th class="table-col"><?php __('Employee ID'); ?></th>
                                <th class="table-col"><?php __('Code 1'); ?></th>
                                <th class="table-col"><?php __('Year'); ?></th>
                                <th class="table-col"><?php __('Number By Year'); ?></th>
                                <th class="table-col"><?php __('Description'); ?></th>
                                </tr>

                            </thead>

                            <tbody>
                                <?php foreach ($record as $data) : ?>
                                    <tr>
                                    <td class="table-no"><?php echo $no; ?></td>
                                    <td class="table-col"><?php echo $data['Employee ID']; ?></td>
                                    <td class="table-col"><?php echo $data['Code 1']; ?></td>
                                    <td class="table-col"><?php echo $data['Year']; ?></td>
                                    <td class="table-col"><?php echo $data['Number By Year']; ?></td>
                                    <td class="table-info">
                                        <?php
                                        echo $this->Html->nestedList($data['error'], array('class' => 'error-info'));

                                        $import = array();
                                        if (!empty($data['data'])) {
                                            $import['do'] = $data['data'];
                                        }
                                        unset($data['error'], $data['data']);
                                        $import['export'] = $data;

                                        foreach ($import as $action => $data) {
                                            foreach ($data as $key => $value) {
                         
                                                if ($key === 'function_id') {
                                                    foreach ($value as $_key => $_value) {
                                                        echo $this->Form->hidden($type . '.' . $action . '.' . $no . '.function_id.' . $_key . '.', array('value' => $_value));
                                                    }
                                                    continue;
                                                }
                                                
                                                echo $this->Form->hidden($type . '.' . $action . '.' . $no . '.' . $key, array('value' => $value));
                                            }
                                        }
                                        ?>
                                    </td>
                                    </tr>
                                    <?php $no++; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <div class="buttons" style="margin-bottom: 4px;">
                            <ul class="type_buttons">
                                <?php if ($type === 'Create') : ?>
                                    <li><a id="submit-create-export" class="export-excel-icon-all" href="javascript:void(0)"><?php echo __('Export') ?></a></li>
                                    <li><a id="submit-create-do" class="new" href="javascript:void(0)"><?php echo __('Add new') ?></a></li>
                                <?php endif; ?>

                                <?php if ($type === 'Update') : ?>
                                    <li><a id="submit-update-export" class="export-excel-icon-all" href="javascript:void(0)"><?php echo __('Export') ?></a></li>
                                    <li><a id="submit-update-do" class="new" href="javascript:void(0)"><?php echo __('Update') ?></a></li>
                                <?php endif; ?>

                                <?php if ($type === 'Error') : ?>
                                    <li><a id="submit-error-export" class="export-excel-icon-all" href="javascript:void(0)"><?php echo __('Export') ?></a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </fieldset>
                <?php endforeach; ?>
                <?php
                echo $this->Form->hidden('task', array('value' => '', 'id' => 'import-task'));
                echo $this->Form->hidden('type', array('value' => '', 'id' => 'import-type'));
                echo $this->Form->end();
                ?>
                <div class="wd-title">
                    <a class="wd-add-project" id="submit-export-all" href="javascript:void(0)" style="margin-right:5px;"><span><?php __('Do all action') ?></span></a>
                </div>
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