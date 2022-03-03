<?php 
echo $html->css(array(
	'preview/tab-admin',
	'layout_admin_2019',
	'preview/layout',
));
?>
<style type="text/css">
.error-info li {
    margin-left: 20px;
    list-style: decimal;
}

.buttons ul.type_buttons {
    padding-right: 2px!important;
}

table tbody tr:hover td {
    background: none!important;
}

center {
    color: red;
    font-weight: 700;
}

.ui-dialog {
    font-size: 11px;
}

#dialog_import_CSV label {
    color: #000;
}

.type_buttons .error-message {
    background-color: #FFF;
    clear: both;
    color: #D52424;
    display: block;
    width: 212px;
    padding: 5px 0 0;
}

.form-error {
    border: 1px solid #D52424;
    color: #D52424;
}

.import-fieldset {
    border: 1px solid #D4D0C8;
    margin-bottom: 20px;
    padding: 5px;
}

.import-legend {
    font-size: 14px;
    font-weight: 700;
    padding: 5px;
}

.table-info {
    min-width: 200px;
}

.table-col {
    min-width: 200px;
    white-space: nowrap;
    overflow: hidden;
}

.display {
    min-width: 1165px;
}

.dataImport {
    overflow: auto;
    max-height: 300px;
}

.error,.error td,.error1 {
    background: #FF6F6F;
}
.buttons ul.type_buttons {
    height: 33px;
}
.buttons ul.type_buttons li a {
    text-indent: -9999px;
    height: 33px;
}
.display td,
.display th {
    vertical-align: middle;
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
.wd-main-content .ui-buttons a{
	line-height: 32px;
	font-weight: normal;
}
#submit-export-all{
	background: #247fc3;
    border-radius: 4px;
}
.table-no{
	min-width: 19px;
    text-align: center;
	padding: 0 5px;
}
</style>
<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->css('jquery.dataTables'); ?> 
<div id="wd-container-main" class="wd-project-index">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                    
                </div>
                <div class="wd-tab">
                	<?php echo $this->element("admin_sub_top_menu");?>
                	<div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
                            	<h2 class="wd-t3"><?php echo __("Import CSV File Review", true); ?></h2>
                <?php
     
                echo $this->Session->flash();
                echo $this->Form->create('Import', array('id' => 'import-form', 'url' => array('controller' => 'profit_centers', 'action' => 'save_import')));
                ?>
                <?php 

                foreach ($records as $type => $record) : ?>
                    <?php $no = 1; ?>
                    <div class="import-fieldset">
                        <div class="import-legend">
                            <?php
                            switch ($type) {
                                case 'Create':
                                    echo __('New profit centers', true);
                                    break;
                                case 'Update':
                                    echo __('Update profit centers', true);
                                    break;
                                default:
                                case 'Error':
                                    echo __('Error profit centers', true);
                                    break;
                            }
                            ?>
                        </div>
                        <div class="dataImport">
                        <table border="0" cellspacing="1" cellpadding="3" class="display">
                            <thead>
                                <tr class="wd-header">
                                <?php foreach($default as $key => $titleColumn){ ?>
                                <th class="table-col"><?php __($key); ?></th>
                                <?php } ?>
                                <th class="table-info"><?php __('Info'); ?></th>
                                </tr>

                            </thead>
                            <tbody>
                                <?php foreach ($record as $data) : ?>
                                    <tr>
                                     <?php foreach($default as $key => $titleColumn){ 
										$hightlight= false;
										if(!empty($data['columnHighLight'])){
											if(isset($data['columnHighLight'][$key])){
												$hightlight = true;
											}
										}                                    
                                    ?>
                                    <td class="table-col"  <?php if($hightlight) echo 'style="border-color: red;"'; ?>  ><?php echo $data[$key]; ?></td>
                                    <?php } ?>
                                    <td class="table-info">
                                        <?php
                                        if(!empty($data['error'])){
                                            if(!empty($data['description'])){
                                                echo join(', ',$data['description']).' is(are) blank';
                                            }
                                        }
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
                                                    if( is_array( $value)){
														foreach( $value as $k => $v){
															 echo $this->Form->hidden($type . '.' . $action . '.' . $no . '.' . $_key . '.' . $k, array('value' => $v));
														}
													}else
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
                        <div class="ui-buttons">
                            <?php if ($type === 'Create') : ?>
                                <a id="submit-create-do" class="btn btn-ok" href="javascript:void(0)"></a>
                                <a id="submit-create-export" class="btn btn-excel" href="javascript:void(0)"></a>
                            <?php endif; ?>

                            <?php if ($type === 'Update') : ?>
                                <a id="submit-update-do" class="btn btn-ok" href="javascript:void(0)"></a>
                                <a id="submit-update-export" class="btn btn-excel" href="javascript:void(0)"></a>
                            <?php endif; ?>

                            <?php if ($type === 'Error') : ?>
                                <a id="submit-error-export" class="export-excel-icon-all" href="javascript:void(0)"></a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php
                echo $this->Form->hidden('task', array('value' => '', 'id' => 'import-task'));
                echo $this->Form->hidden('type', array('value' => '', 'id' => 'import-type'));
                echo $this->Form->end();
                ?>
                <a class="btn-text" id="submit-export-all" href="javascript:void(0)" style="margin-right:5px; float: right;">
                    <img src="<?php echo $this->Html->url('/img/ui/blank-ok.png') ?>" alt="" />
                    <span><?php __('Do all') ?></span>
                </a>
                <!-- End table -->
	                		</div>
	                	</div>
	                </div>
                </div><!-- end wd-tab -->
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