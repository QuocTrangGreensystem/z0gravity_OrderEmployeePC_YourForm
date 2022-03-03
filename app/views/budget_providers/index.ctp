<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->css('slick_grid/slick.grid'); ?>
<?php echo $html->css('slick_grid/slick.pager'); ?>
<?php echo $html->css('slick_grid/slick.common'); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
<?php echo $html->script('history_filter'); ?>
<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
	.wd-panel .wd-section{
		display: block;
		width: 100%;
	}
	.wd-tab .wd-content{
		width: calc( 100% - 340px);
		float: left;
		overflow: visible;
		position: relative;
		padding-top: 20px;
	}
	.wd-list-project .wd-tab .wd-panel{
		padding: 20px;
	}
	.slick-viewport .slick-row .slick-cell.grid-action{
		padding: 0;
		border-top:0;
	}
	.grid-action .wd-actions{
		margin: 0;;
	}
	.grid-action .wd-actions .wd-btn{
		width: 40px;
		height: 40px;
		float:left;
	}
	/* width */
	body ::-webkit-scrollbar {
		width: 4px;
		height: 4px;
	}

	/* Track */
	body ::-webkit-scrollbar-track {
		box-shadow: inset 0 0 5px #F2F5F7; 
		border-radius: 5px;
		background-color: #fff;
	}

	/* Handle */
	body ::-webkit-scrollbar-thumb {
		background: #C6CCCF;; 
		border-radius: 5px;
	}
	.btn.add-field{
		position: absolute;
		width: 50px;
		height: 50px;
		line-height: 50px;
		text-align: center;
		border-radius: 50%;
		z-index: 5;
		top: 0;
		margin: 0;
		right: 15px;
		box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2);
		background-color: #247FC3;
	}
	.btn.add-field:before{
		content: '';
		background-color: #fff;
		position: absolute;
		width: 2px;
		height: 20px;
		top: calc( 50% - 10px);
		left: calc( 50% - 1px);
	}
	.btn.add-field:after{
		content: '';
		position: absolute;
		background-color: #fff;
		width: 20px;
		height: 2px;
		left: calc( 50% - 10px);
		top: calc( 50% - 1px);
	}
</style>
<!-- dialog_attachement_or_url -->
<div id="dialog_attachement_or_url" class="buttons" style="display: none;">
    <fieldset>
        <?php 
        echo $this->Form->create('Upload', array(
                'type' => 'file', 'id' => 'form_dialog_attachement_or_url', 
                'url' => array('controller' => 'budget_providers', 'action' => 'upload')
            )); ?>
        <div style="height:auto;" class="wd-scroll-form">
            <div class="wd-input">
                <label for="attachement"><?php __("Attachement") ?></label>
                <p id="gs-attach"></p>
                <?php
                echo $this->Form->hidden('id', array('id' => false, 'rel' => 'no-history', 'value' => ''));
                ?>
                <?php
                echo $this->Form->input('attachment', array('type' => 'file', 'value' => '',
                    'name' => 'FileField[attachment]',
                    'label' => false,
                    'class' => 'update_attach_class',
                    'rel' => 'no-history'));
                ?>   							
            </div>
            <div class="wd-input">
                <label for="url"><?php __("Url") ?></label>
                <p id="gs-url"></p>
                <?php
                echo $this->Form->input('url', array('type' => 'text',
                    'label' => false,
                    'class' => 'update_url',
                    'disabled' => 'disabled',
                    'rel' => 'no-history'));
                ?> 
            </div>
            <p style="color: black;margin-left: 146px; font-size: 12px; font-style: italic;">
                <strong>Ex:</strong> 
                www.example.com
            </p>
        </div>
        <?php
        echo $this->Form->end();
        ?>  
    </fieldset>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_attach"><?php __('OK') ?></a></li>
    </ul>
</div>
<!-- dialog_attachement_or_url.end -->
<!-- export excel  -->
<fieldset style="display: none;">
    <?php
   // echo $this->Form->create('Export', array(
//        'type' => 'POST',
//        'url' => array('controller' => 'project_parts', 'action' => 'export', $projectName['Project']['id'])));
//    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
//    echo $this->Form->end();
    ?>
</fieldset>
<!-- /export excel  -->
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
                                <h2 class="wd-t3">
                                    <?php 
                                        //$_title =  __("Provider", true);
                                        //echo sprintf(__("Budget %s management of %s", true), $_title, $companyName['Company']['company_name']); 
                                    ?>
                                </h2>
                                <div id="message-place">
                                    <?php
                                    App::import("vendor", "str_utility");
                                    $str_utility = new str_utility();
                                    echo $this->Session->flash();
                                    ?>
                                </div>
								<a href="javascript:void(0);" class="btn add-field" id="add_item" style="margin-right:5px;" title="Add an item" onclick="addNewItem();"></a>
                                <div class="wd-table" id="project_container" style="width:100%;height:400px;">

                                <!--</div>
                                <div id="pager" style="width:100%;height:36px; overflow: hidden;">

                                </div>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $this->element('dialog_projects') ?>
<?php echo $html->script('slick_grid/lib/jquery-ui-1.8.16.custom.min'); ?>
<?php echo $html->script('slick_grid/lib/jquery.event.drag-2.0.min'); ?>
<?php echo $html->script('slick_grid/slick.core'); ?>
<?php echo $html->script('slick_grid/slick.dataview'); ?>
<?php echo $html->script('slick_grid/controls/slick.pager'); ?>
<?php echo $html->script('slick_grid/slick.formatters'); ?>
<?php echo $html->script('slick_grid/plugins/slick.cellrangedecorator'); ?>
<?php echo $html->script('slick_grid/plugins/slick.cellrangeselector'); ?>
<?php echo $html->script('slick_grid/plugins/slick.cellselectionmodel'); ?>
<?php echo $html->script('slick_grid/slick.editors'); ?>
<?php echo $html->script('slick_grid/slick.grid'); ?>
<?php echo $html->script(array('slick_grid_custom')); ?>
<?php

function jsonParseOptions($options, $safeKeys = array()) {
    $output = array();
    $safeKeys = array_flip($safeKeys);
    foreach ($options as $option) {
        $out = array();
        foreach ($option as $key => $value) {
            if (!is_int($value) && !isset($safeKeys[$key])) {
                $value = json_encode($value);
            }
            $out[] = $key . ':' . $value;
        }
        $output[] = implode(', ', $out);
    }
    return '[{' . implode('},{ ', $output) . '}]';
}

$columns = array(
    array(
        'id' => 'no.',
        'field' => 'no.',
        'name' => '#',
        'width' => 40,
        'sortable' => true,
        'resizable' => false,
        'noFilter' => 1,
    ),
    array(
        'id' => 'name',
        'field' => 'name',
        'name' => __('Name', true),
        'width' => 250,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox',
        //'validator' => 'DataValidator.isUnique'
    ),
    array(
        'id' => 'payment',
        'field' => 'payment',
        'name' => __('Payment time', true),
        'width' => 300,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.numericValue'
    ),
    array(
        'id' => 'contact',
        'field' => 'contact',
        'name' => __('Contact', true),
        'width' => 300,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'email',
        'field' => 'email',
        'name' => __('Email', true),
        'width' => 300,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox',
        //'validator' => 'DataValidator.isEmail'
    ),
    array(
        'id' => 'address',
        'field' => 'address',
        'name' => __('Address', true),
        'width' => 300,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox',
        //'validator' => 'DataValidator.isEmail'
    ),
    array(
        'id' => 'city',
        'field' => 'city',
        'name' => __('City', true),
        'width' => 300,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'country',
        'field' => 'country',
        'name' => __('Country', true),
        'width' => 300,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'file_attachement',
        'field' => 'file_attachement',
        'name' => __('Attachement or URL', true),
        'width' => 150,
        'noFilter' => 1,
        'sortable' => true,
        'resizable' => false,
        'editor' => 'Slick.Editors.Attachement',
        'formatter' => 'Slick.Formatters.Attachement'
    ),
    array(
        'id' => 'analytical_code',
        'field' => 'analytical_code',
        'name' => __('Analytical code', true),
        'width' => 300,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'analytical_code1',
        'field' => 'analytical_code1',
        'name' => __('Analytical code 1', true),
        'width' => 300,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __('Action', true),
        'width' => 70,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
        ));
$i = 1;
$dataView = array();
$selectMaps = array();

App::import("vendor", "str_utility");
$str_utility = new str_utility();


foreach ($budgetProviders as $budgetProvider) {
    $data = array(
        'id' => $budgetProvider['BudgetProvider']['id'],
        'company_id' => $company_id,
        'no.' => $i++,
        'MetaData' => array()
    );
    $data['name'] = (string) $budgetProvider['BudgetProvider']['name'];
    $data['payment'] = (string) $budgetProvider['BudgetProvider']['payment'];
    $data['contact'] = (string) $budgetProvider['BudgetProvider']['contact'];
    $data['email'] = (string) $budgetProvider['BudgetProvider']['email'];
    $data['address'] = (string) $budgetProvider['BudgetProvider']['address'];
    $data['city'] = (string) $budgetProvider['BudgetProvider']['city'];
    $data['country'] = (string) $budgetProvider['BudgetProvider']['country'];
    $data['file_attachement'] = (string) $budgetProvider['BudgetProvider']['file_attachement'];
    $data['analytical_code'] = (string) $budgetProvider['BudgetProvider']['analytical_code'];
    $data['analytical_code1'] = (string) $budgetProvider['BudgetProvider']['analytical_code1'];
    $data['format'] = (string) $budgetProvider['BudgetProvider']['format'];

    $data['action.'] = '';

    $dataView[] = $data;
}

$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'The code is avaiable, please enter another!' => __('The code is avaiable, please enter another!', true),
    'Clear' => __('Clear', true)
);
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<div id="attachment-template" style="display: none;">
    <div style="overflow: hidden;" class="img-to-right">
        <a class="download-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '?' => array('type' => 'download'))); ?>"><?php echo __('Download', true); ?></a>
        &nbsp; <a class="delete-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '?' => array('type' => 'delete'))); ?>" rel="%2$s"><?php echo __('Delete', true); ?></a>
    </div>
</div>
<div id="attachment-template-url" style="display: none;">
    <div style="overflow: hidden;" class="img-to-right">
        <a class="url-attachment" target="_blank" href="<?php echo 'http://%2$s'; ?>"></a>
        &nbsp; <a class="delete-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '?' => array('type' => 'delete'))); ?>" rel="%3$s"><?php echo __('Delete', true); ?></a>
    </div>
</div>
<div style="display: none" id="upload-template-place">
    <div id="upload-template">
        <?php
        echo $this->Form->create('Upload', array('type' => 'file',
            'target' => 'i-upload-template',
            'url' => array('controller' => 'budget_providers', 'action' => 'upload')));
        echo $this->Form->hidden('id', array('id' => false, 'rel' => 'no-history', 'value' => ''));
        echo $this->Form->input('attachment', array('type' => 'file', 'value' => '',
            'name' => 'FileField[attachment]',
            'label' => false,
            'after' => '<div class="browse"></div>',
            'rel' => 'no-history'));
        echo $this->Form->end();
        ?>
    </div>
    <iframe id="i-upload-template" name="i-upload-template" src="" style="height: 0;visibility: hidden;position: absolute; top:  -9999px;"></iframe>
</div>
<script type="text/javascript">

	var wdTable = $('.wd-table');
	$(document).ready(set_slick_table_height);
	$(window).resize(set_slick_table_height);
	function set_slick_table_height(){
		if( wdTable.length){
			var heightTable = $(window).height() - wdTable.offset().top - 45;
			console.log(heightTable);
			wdTable.css({
				height: heightTable,
			});
			heightViewPort = heightTable - 72;
			wdTable.find('.slick-viewport').height(heightViewPort);
			console.log( heightViewPort, "   ");
			clearInterval(wdTable);
		}
	}

    var DataValidator = {},ControlGrid,IuploadComplete = function(json){ 
        var data = ControlGrid.eval('currentEditor');
        data.onComplete(json);
    };
    (function($){
        
        $(function(){
            var $this = SlickGridCustom,
            uploadForm = $('#upload-template');
        
        
            $('a.delete-attachment').live('click' , function(){
                var row = $(this).attr('rel'),
                data = ControlGrid.getDataItem(row);
                if(data && confirm($this.t('Are you sure you want to delete attachement : %s'
                , data['file_attachement']))){
                    data && $.ajax({
                        cache : false,
                        type : 'GET',
                        url : $(this).attr('href')
                    });
                    data['file_attachement'] = '';
                    ControlGrid.updateRow(row);
                }
                return false;
            });
            
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  true;
            // For validate date
        
            DataValidator.isUnique = function(value,args){
                var result = true;
                $.each(args.grid.getData().getItems() , function(undefined,dx){
                    if(args.item.id && args.item.id == dx.id){
                        return true;
                    }
                    return (result = (dx.name.toLowerCase() != value.toLowerCase()));
                });
                return {
                    valid : result,
                    message : $this.t('The Currency has already been exist.')
                };
            }
            
            if(!$this.canModified){
                $('#attachment-template').find('a.delete-attachment').remove();
            }
            
            var actionTemplate =  $('#action-template').html(),
            attachmentTemplate =  $('#attachment-template').html();
             attachmentURLTemplate =  $('#attachment-template-url').html();
        
            $.extend(Slick.Formatters,{
                Attachement : function(row, cell, value, columnDef, dataContext){
                    if(value){
                        if(dataContext.format == 2){
                            value = $this.t(attachmentTemplate,dataContext.id,row);
                        }
                        if(dataContext.format == 1){
                            value = $this.t(attachmentURLTemplate,dataContext.id,dataContext.file_attachement,row);
                        }
                    }
                    return Slick.Formatters.HTMLData(row, cell, value, columnDef, dataContext);
                },
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,
                    dataContext.company_id,dataContext.name), columnDef, dataContext);
                }
            });
            
            $.extend(Slick.Editors,{
                Attachement : function(args){
                    var self = this;
                    $.extend(this, new BaseSlickEditor(args));
        			this.input = $("<a href='#' id='action-attach-url'></a><div class='browse'></div>")
        			.appendTo(args.container).attr('rel','no-history').addClass('editor-text');
                    $("#ok_attach").click(function(){
                        //self.input[0].remove();
                        $('#action-attach-url').css('display', 'none');
                        $('.browse').css('display', 'block');
                        $("#dialog_attachement_or_url").dialog('close');
                        
                        var form = $("#form_dialog_attachement_or_url");
                        form.find('input[name="data[Upload][id]"]').val(args.item.id);
                        form.submit();
                    });
                    this.focus();
                }
            });
            
            //$.extend(Slick.Editors,{
//                Attachement : function(args){
//                    var self = this;
//                    $.extend(this, new BaseSlickEditor(args));
//                    this.input = uploadForm.find('input[type=file]');
//                    uploadForm.appendTo(args.container).addClass('editor-select');
//                
//                    this.inprocess  = false;
//                    this.value  = '';
//                
//                    this.input.unbind().change(function(){
//                        self.inprocess = true;
//                        var form = self.input.hide().parent().addClass('file-uploading').closest('form');
//                    
//                        form.find('.browse').show();
//                        form.find('input[name="data[Upload][id]"]').val(args.item.id);
//                        form.submit();
//                    });
//                
//                    this.input.parent().click(function(){
//                        if(self.inprocess){
//                            $('#i-upload-template').unbind('load')
//                            .prop('src', 'javascript'.concat(':false;'));
//                            IuploadComplete({
//                                result : false,
//                                message : $this.t('Uploading has been aborted')
//                            });
//                        }
//                    });
//                
//                    this.onComplete = function(data){
//                        this.value = data.filename;
//                        this.inprocess = false;
//                        this.input.show().parent().removeClass('file-uploading').closest('form').find('.browse').hide();
//                        if(!data.result){
//                            this.tooltip(data.message);
//                            this.focus();
//                        }else{
//                            args.grid.eval('commitEditAndSetFocus();');
//                        }
//                    }
//                
//                    var validate = this.validate;
//                    this.validate = function(){
//                        if(this.inprocess){
//                            this.tooltip($this.t('Cannot leave because upload file in processing!'));
//                            return {
//                                valid: false,
//                                message: ''
//                            };
//                        }
//                        return validate.apply(this , $.makeArray(arguments));
//                    }
//                    this.destroy = function(){
//                        this.tooltip();
//                        uploadForm.remove();
//                    }
//                    this.loadValue = this.setValue = function(){}
//                    this.getValue = function(){
//                        return this.value;
//                    }
//                    this.focus();
//                }
//            });
        
            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                company_id : {defaulValue : '<?php echo $company_id; ?>', allowEmpty : false},
                name : {defaulValue : '' , allowEmpty : false, maxLength: 100},
                payment  : {defaulValue : '', maxLength : 255},
                contact  : {defaulValue : '', maxLength : 255},
                email  : {defaulValue : '', maxLength : 255},
                address  : {defaulValue : '', maxLength : 255},
                city  : {defaulValue : '', maxLength : 255},
                country  : {defaulValue : '', maxLength : 255},
                file_attachement : {defaulValue : '' , required : ['id']},
                format : {defaulValue : ''},
                analytical_code  : {defaulValue : '', maxLength : 255},
                analytical_code1  : {defaulValue : '', maxLength : 255},
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            $this.onBeforeEdit = function(args){
                if(args.column.field == 'file_attachement' && (!args.item || args.item['file_attachement'] || !args.item['id'])){
                    return false;
                }
                return true;
            }
            //$this.init($('#project_container'),data,columns);
            ControlGrid = $this.init($('#project_container'),data,columns);
            addNewItem = function(){
				ControlGrid.gotoCell(data.length, 1, true);
			};
            /* table .end */
            var createDialog = function(){
                $('#dialog_attachement_or_url').dialog({
                    position    :'center',
                    autoOpen    : false,
                    autoHeight  : true,
                    modal       : true,
                    width       : 500,
                    open : function(e){
                        var $dialog = $(e.target);
                        $dialog.dialog({open: $.noop});
                    }
                });
                createDialog = $.noop;
            }
            
            $("#action-attach-url").live('click',function(){
                createDialog();
                var titlePopup = <?php echo json_encode(__('Attachement or URL', true))?>;
                $("#dialog_attachement_or_url").dialog('option',{title:titlePopup}).dialog('open');
            });
            
            $(".cancel").live('click',function(){
                $("#dialog_attachement_or_url").dialog('close');
            });
            $("#gs-url").click(function(){
                $(this).addClass('gs-url-add');
                $('#gs-attach').addClass('gs-attach-remove');
                $('.update_url').removeAttr('disabled').css('border', '1px solid #3B57EE');
                $('.update_attach_class').attr('disabled', 'disabled').css('border', '1px solid #d4d4d4');
            });
            $("#gs-attach").click(function(){
                $(this).removeClass('gs-attach-remove');
                $('#gs-url').removeClass('gs-url-add');
                $('.update_attach_class').removeAttr('disabled').css('border', '1px solid #3B57EE');;
                $('.update_url').attr('disabled', 'disabled').css('border', '1px solid #d4d4d4');
            });
        });
        
    })(jQuery);
</script>