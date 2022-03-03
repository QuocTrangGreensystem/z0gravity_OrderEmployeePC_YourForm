<?php echo $html->css(array('projects')); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<style>
.wd-input-select label{
	display:inline-block;
	min-width:175px;
}
.wd-input-select{
	padding-top:10px;
	font-weight:700;
	height: 30px;
}
.wd-input-select select{
	float: left;
}
#error{ 
	color:#F00;
	padding-top:10px;
}
.ui-dialog .ui-dialog-titlebar-close { 
	display:none;
}
.document_mandatory{
	width:252px;
	padding:4px ;
}
.hr_email, .doc_message{
	width:240px;
	padding:4px ;
}
.doc_message{
	width:480px;
}
#loadingElm img{
	margin:4px 0 0 10px;
}
.wd-list-project .wd-tab .wd-content label{
	margin-top: 10px;
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
									$ID = $data['id'];
                                    ?>
                                </div>
                                <div class="wd-table" id="project_container">
                                	<div id="wd-select">
									 	<div class="wd-input-select">
                                            <label><?php echo __("HR email", true);?></label>
                                            <?php
                                                echo $this->Form->input('hr_email', array(
                                                    'div' => false, 
                                                    'label' => false,
													 "default" => $data['hr_email'],
													'onchange' => "editMe('hr_email',this.value);",
                                                    "class" => "hr_email"
                                                    ));
                                            ?>
                                        </div>
                                         <div class="wd-input-select">
                                            <label><?php echo __("Document mandatory", true)?></label>
                                            <?php
												$option = array('No','Yes');
                                                echo $this->Form->input('document_mandatory', array(
                                                    'div' => false, 
                                                    'label' => false,
													'onchange' => "editMe('document_mandatory',this.value);",
                                                    "class" => "document_mandatory",
                                                    "default" => $data['document_mandatory'],
                                                    "options" => $option
                                                    ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select">
                                            <label><?php echo __("Message", true)?></label>
                                            
                                            <?php
												echo $this->Form->input('message', array(
                                                    'div' => false, 
                                                    'label' => false,
													 "default" => $data['message'],
													'onchange' => "editMe('message',this.value);",
                                                    "class" => "doc_message"
                                                    ));
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
</div>

<!-- document_popup -->
<div id="document_popup" style="display:none;" class="buttons">
    <?php
    echo $this->Form->create('Upload', array('id' => 'uploadForm', 'type' => 'file',
        'url' => array('controller' => 'absences', 'action' => 'update_document', $ID, $company_id)
    ));
    ?>
    <div class="wd-input" style="padding-left: 40px;">
        <ul id="ch_group_infor_popup_1">
            <li><input type="file" id="textDocument" name="FileField[attachment]" style="margin-left: 0px;font-size: 13px;"/></li>
            <li id="error"></li>
        </ul>
    </div>
    <ul class="type_buttons" style="padding-right: 25px !important;">
        <li><a class="cancel" href="javascript:void(0)"><?php echo __('Document') ?></a></li>
        <li><a id="document_popup_submit" class="new" onclick="return false;" href="#"><?php echo __('Submit') ?></a></li>
    </ul>
    <?php echo $this->Form->end(); ?>
</div>
<!-- End document_popup -->
<input id="attachmentTmp" value="<?php echo $data['attachment']; ?>" type="hidden" />
<script type="text/javascript" >
var createDialogTwo = function(){
	$('#document_popup').dialog({
		position    :'center',
		autoOpen    : false,
		closeOnEscape: false,
		autoHeight  : true,
		modal       : true,
		width       : 460,
		height      : 150,
		open : function(e){
			var $dialog = $(e.target);
			$dialog.dialog({open: $.noop});
		}
	});
	createDialogTwo = $.noop;
}
createDialogTwo();
<?php if( $data['attachment'] == '' && $data['document_mandatory'] )
{ ?>
<?php } ?>
var loading = "<span id='loadingElm'><img src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt='Loading' /></span>";
$('#upload_document').click(function(){
	
	$("#document_popup").dialog('option',{title:'Document'}).dialog('open');
});
function editMe(field,value)
{
	if(field == 'hr_email' && !checkEmailValid(value))
	{
		$("#hr_email").focus();
		return false;
	}
	$(loading).insertAfter('#'+field);
	var data = field+'/'+value;
	setTimeout(function(){
	$.ajax({
		url: '/absences/edit_attachment/<?php echo $ID; echo'/'; echo $company_id; ?>/'+data,
		data: '',
		async: false, 
		type:'POST',
		success:function(datas) {
			$('#loadingElm').remove();
		}
	});
	},1000);
}
$('#uploadForm').submit(function(e){
	if(window.FormData !== undefined){
		var formData = new FormData($(this)[0]);
		var formURL = $(this).attr("action");
		$('#error').html(loading);
		$.ajax({
			url: formURL,
			type: 'POST',
			data:  formData,
			mimeType:"multipart/form-data",
			async: false,
			cache: false,
			contentType: false,
			cache: false,
			processData: false,
			success: function (data) {
				$data = JSON.parse(data);
				setTimeout(function(){
					$('#error').html($data.message);
					$('#textDocument').val('');
				},1000);
				clearTimeout();
				if($data.success == 1)
				{
					$('#attachmentTmp').val($data.file);
					setTimeout(function(){
						$("#document_popup").dialog("close");
					},1000);
				}
				else
				{

				}
			}
		});
		e.preventDefault(); //Prevent Default action. 
	} else {
		var formObj = $(this);
		//generate a random id
		var iframeId = 'unique' + (new Date().getTime());
		//create an empty iframe
		var iframe = $('<iframe src="javascript:false;" name="'+iframeId+'" />');
		//hide it
		iframe.hide();
		//set form target to iframe
		formObj.attr('target',iframeId);
		//Add iframe to body
		iframe.appendTo('body');
		iframe.load(function(e){
			var doc = getDoc(iframe[0]);
			var docRoot = doc.body ? doc.body : doc.documentElement;
			var data = docRoot.innerHTML;
			//data is returned from server.
			window.location = ('/absences/attached_documents/');
		});
	}
});
function checkEmailValid(email)
{
	if (!email.match(/^([-\d\w][-.\d\w]*)?[-\d\w]@([-\w\d]+\.)+[a-zA-Z]{2,6}$/))
	return false;
	else
	return true;
}
$("#document_popup_submit").live('click', function(){   
		var $file = $('#textDocument').val();
		if($file == '')
		{
			$('#error').html('<?php echo __('Please select attachment!',true);?>');
			return false;
		}  
        $("#uploadForm").submit(); //Submit the form
    });
$(".cancel").live('click',function(){
	var $check = $('#document_mandatory').val();
	if($check == 1)
	{
		var $file = $('#textDocument').val();
		var $fileTmp = $('#attachmentTmp').val();
		if($file == '' && $fileTmp == '')
		{
			$('#error').html('<?php echo __('The document is mandatory!',true);?>');
			return false;
		}
		else
		{
			$("#document_popup").dialog("close");
		}
		return false;
	}
	$("#document_popup").dialog("close");
});
</script>