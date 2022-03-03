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
        padding:5px;
        margin-bottom: 20px;
        border: 1px solid #Ddd;
    }
    .import-legend{
        font-size: 14px;
        padding: 10px 4px;
        font-weight: bold;
    }

    .table-info{
        min-width: 200px;
    }
    .table-col{
        min-width: 200px;
      
    }
    .display{
        min-width: 1165px;
    }
    tr td,
    tr th {
        vertical-align: middle
    }
    .dataImport{
        overflow-x: auto;
        overflow-y:  auto;
        width: 99%;
        max-height: 300px
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
<!-- /export excel  -->
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                    <h2 class="wd-t1"><?php echo __("Import CSV File Review", true); ?></h2>
                    <?php /* <a href="<?php echo $this->Html->url(array('action' => 'review', $companyName['Company']['id'])); ?>" class="wd-add-project" style="margin-right:5px; "><span><?php __('Export') ?></span></a>
                    <a href="javascript:void(0)" class="wd-add-project" id="import_CSV" style="margin-right:5px; "><span><?php __('Import CSV') ?></span></a> */ ?>
                </div>
                <div id="message-place">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    ?>
                </div>
                <?php
                echo $this->Form->create('Import', array('id' => 'import-form', 'url' => array('controller' => 'activities', 'action' => 'save_import')));
                ?>
                <?php foreach ($records as $type => $record) : ?>
                    <?php $no = 1; ?>
                    <div class="import-fieldset">
                        <legend class="import-legend">
                            <?php
                            switch ($type) {
                                case 'Create':
                                    echo __('New Activites', true);
                                    break;
                                case 'Update':
                                    echo __('Update Activites', true);
                                    break;
                                default:
                                case 'Error':
                                    echo __('Error Activites', true);
                                    break;
                            }
                            ?>
                        </legend>
                        <div class="dataImport">
                        <table border="0" style="width: auto;" cellspacing="1" cellpadding="3" class="display">
                            <thead>

                                <tr class="wd-header">
                                    <th class="table-no"><?php __('No.'); ?></th>  
                                    <?php foreach($titleColumns as $titleColumn){?>
                                    <th class="table-col" ><?php echo __($titleColumn, true); ?></th>
                                    <?php }?>
                                    <th class="table-info"><?php __('Info'); ?></th>
                                </tr>

                            </thead>

                            <tbody>
                                <?php foreach ($record as $data) : ?>
                                    <tr>
                                        <td class="table-no"><?php echo $no; ?></td>
                                        <?php 
                                            foreach($default as $key => $value){
                                                if($key!='No.'){
                                                    $heighLigh= false;
                                                    if(!empty($data['columnHighLight'])){
                                                        if(isset($data['columnHighLight'][$key])){
                                                            $heighLigh = true;
                                                        }
                                                    }          
                                        ?>
                                        <td class="table-col" <?php if($heighLigh) echo 'style="border-color: red !important ;"'; ?>><?php echo $data[$key]; ?></td>
                                        <?php } }?>
                                        <td class="table-info">
                                            <?php
                                            if(empty($data['error'])){
                                                if(!empty($data['columnHighLight'])){
                                                    $desc = array();
                                                    $_desc = array();
                                                    $desc = array_keys($data['columnHighLight']);
                                                    foreach($desc as $key => $val){
                                                        if($val=='Code 1'){
                                                            $_desc[] = 'N&deg; DT';
                                                            continue;    
                                                        } 
                                                        if($val=='Code 2'){
                                                            $_desc[] =  'N&deg; UAG';
                                                            continue;
                                                        } 
                                                        if($val=='Code 3'){
                                                            $_desc[] = 'Code Analytique';
                                                            continue;
                                                        }
                                                        $_desc[] = $val;
                                                        
                                                    }
                                                  
                                                  echo join(', ', $_desc).' is(are) blank.';
                                                }
                                                //print custom error - QN 12/9
                                                if(isset($data['messages']))
                                                    echo '<br/>' . implode('<br/>', $data['messages']);
                                            }
                                            
                                            echo $this->Html->nestedList($data['error'], array('class' => 'error-info'));

                                            $import = array();
                                            if (!empty($data['data'])) {
                                                $import['do'] = $data['data'];
                                            }
                                            unset($data['error'], $data['data']);
                                            $import['export'] = $data;

                                            foreach ($import as $action => $data) {
                                                foreach ($data as $key => $value) {
                                                    if($key != 'columnHighLight'){
                                                        if ($key === 'accessible_profit') {
                                                            $accessible_profit_id =array();
                                                            foreach ($value as $_key => $_value) {
                                                                $accessible_profit_id[] = $_value;
                                                            }
                                                            echo $this->Form->hidden($type . '.' . $action . '.' . $no . '.' . $key, array('value' => join(';',$accessible_profit_id)));
                                                            continue;
                                                        }
                                                        echo $this->Form->hidden($type . '.' . $action . '.' . $no . '.' . $key, array('value' => $value));
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
                        <div class="buttons" style="margin-bottom: 4px;">
                            <ul class="type_buttons">
                                <?php if ($type === 'Create') : ?>
                                    <li><a id="submit-create-export"  class="export-excel-icon-all" href="javascript:void(0)"><?php echo __('Export') ?></a></li>
                                    <li><a id="submit-create-do" class="new" href="javascript:void(0)"><?php echo __('Add new') ?></a></li>
                                <?php endif; ?>

                                <?php if ($type === 'Update') : ?>
                                    <li><a id="submit-update-export"  class="export-excel-icon-all" href="javascript:void(0)"><?php echo __('Export') ?></a></li>
                                    <li><a id="submit-update-do" class="new" href="javascript:void(0)"><?php echo __('Update') ?></a></li>
                                <?php endif; ?>

                                <?php if ($type === 'Error') : ?>
                                    <li><a id="submit-error-export"  class="export-excel-icon-all" href="javascript:void(0)"><?php echo __('Export') ?></a></li>
                                <?php endif; ?>
                            </ul>
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