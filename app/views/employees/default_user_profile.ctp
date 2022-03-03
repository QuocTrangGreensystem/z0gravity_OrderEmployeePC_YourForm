<?php 
echo $html->css(array(
	'layout_2019',
	'preview/layout',
	'preview/tab-admin'
));
echo $html->script(array(
	// 'responsive_table',
));
 ?>
<style>
	body {
		font-family: "Open Sans";
	}
	.wd-layout .wd-main-content .wd-tab {
		margin-left: auto;
		margin-right: auto;
		max-width: 1920px;
	}
	.wd-tab .wd-panel {
		background-color: #fff;
		border: none !important;
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
                                    ?>
                                </div>
								<div id="formMessage" style="display: none;" class="message success"><?php __('Saved');?><a href="#" class="close">x</a></div>
                                <div class="wd-table" id="user-default-table" style="width:100%; overflow: auto">
<?php
echo $this->Form->create('EmployeeDefault', array(
	'type' => 'POST',
	'url' => array('controller' => 'employees', 'action' => 'save_company_default_user'),
	'class' => 'form-style-2019',
	'id' => 'EmployeeDefault'
));
	?>
<h3><?php //__('Default value for new users');?></h3>
<div class="generate-email">
	<h4><?php __('Generate email automaticaly');?></h4>
	<?php 
	
	echo $this->Form->input('first_name', array('div' => 'wd-input wd-custom-checkbox', 'label' => __("First name", true), 'type' => 'checkbox', 'checked' => $default_user_profile['first_name']));
	echo $this->Form->input('first_letter_first_name', array('div' => 'wd-input wd-custom-checkbox', 'label' => __("First letter of the first name", true), 'type' => 'checkbox', 'checked' => $default_user_profile['first_letter_first_name']));
	echo $this->Form->input('sperator', array('div' => 'wd-input label-inline', 'label' => __("Sperator", true), 'type' => 'text', 'value' => $default_user_profile['sperator']));
	echo $this->Form->input('last_name', array('div' => 'wd-input wd-custom-checkbox', 'label' => __("Last name", true), 'type' => 'checkbox', 'checked' => $default_user_profile['last_name']));
	echo $this->Form->input('first_letter_last_name', array('div' => 'wd-input wd-custom-checkbox', 'label' => __("First letter of the last name", true), 'type' => 'checkbox', 'checked' => $default_user_profile['first_letter_last_name']));
	echo $this->Form->input('domain_name', array('div' => 'wd-input label-inline', 'label' => __("Domain name", true).' ('. __('include @', true).' )', 'type' => 'text', 'value' => $default_user_profile['domain_name']));
	?>
	<div class="email-preview">
		<?php 
		echo $this->Form->input('email_preview', array('div' => 'wd-input label-inline', 'label' => __("Preview for", true).' \"Giselle Alston\"', 'type' => 'text', 'disabled' => true));
		?>
	</div>
</div>

<div class="default-password">
	<h4><?php //__('Default password');?></h4>
	<?php 
	
	echo $this->Form->input('password', array('div' => 'wd-input', 'label' => __("Default password", true), 'type' => 'password', 'value' => $default_user_profile['password']));
	?>
</div>
<div class="default-value">
	<h4><?php __('Default value');?></h4>
	<?php 
	
	echo $this->Form->input('tjm', array('div' => 'wd-input label-inline', 'label' => __("Average Daily Rate", true), 'type' => 'text', 'data-type' =>'wd-number', 'value' => $default_user_profile['tjm']));
	echo $this->Form->input('capacity_by_year', array('div' => 'wd-input label-inline', 'label' => __('Capacity', true) .'/'. __('Year', true), 'type' => 'text',  'data-type' =>'wd-number', 'value' => $default_user_profile['capacity_by_year']));	
	echo $this->Form->input('email_receive', array('div' => 'wd-input wd-custom-checkbox', 'label' => __("Authorize z0 Gravity email", true), 'type' => 'checkbox', 'checked' => $default_user_profile['email_receive']));	
	echo $this->Form->input('activate_copy', array('div' => 'wd-input wd-custom-checkbox', 'label' => __("Activate COPY in timesheet ", true), 'type' => 'checkbox', 'checked' => $default_user_profile['activate_copy']));	
	echo $this->Form->input('is_enable_popup', array('div' => 'wd-input wd-custom-checkbox', 'label' => __("Enable popup", true), 'type' => 'checkbox', 'checked' => $default_user_profile['is_enable_popup']));	
	echo $this->Form->input('auto_timesheet', array('div' => 'wd-input wd-custom-checkbox', 'label' => __("Auto validate timesheet", true), 'type' => 'checkbox', 'checked' => $default_user_profile['auto_timesheet']));	
	echo $this->Form->input('auto_absence', array('div' => 'wd-input wd-custom-checkbox', 'label' => __("Auto validate absence", true), 'type' => 'checkbox', 'checked' => $default_user_profile['auto_absence']));	
	echo $this->Form->input('auto_by_himself', array('div' => 'wd-input wd-custom-checkbox', 'label' => __("Auto validate by himself", true), 'type' => 'checkbox', 'checked' => $default_user_profile['auto_by_himself']));
	?>
</div>

<div class="default-pm">
	<h4><?php __('Project manager');?></h4>
	<?php 
	$sl_budget = array(
		0 => __('Not Display Budget', true),
		1 => __('Readonly Budget', true),
		2 => __('Update Budget', true),
	);
	echo $this->Form->input('control_resource', array('div' => 'wd-input wd-custom-checkbox', 'label' => __("Allow managing resources?", true), 'type' => 'checkbox', 'checked' => $default_user_profile['control_resource']));	
	echo $this->Form->input('update_your_form', array('div' => 'wd-input wd-custom-checkbox', 'label' => __("Update Your form", true), 'type' => 'checkbox', 'checked' => $default_user_profile['update_your_form']));
	echo $this->Form->input('create_a_project', array('div' => 'wd-input wd-custom-checkbox', 'label' => __("Create a project", true), 'type' => 'checkbox', 'checked' => $default_user_profile['create_a_project']));
	echo $this->Form->input('delete_a_project', array('div' => 'wd-input wd-custom-checkbox', 'label' => __("Delete a project", true), 'type' => 'checkbox', 'checked' => $default_user_profile['delete_a_project']));
	echo $this->Form->input('change_status_project', array('div' => 'wd-input wd-custom-checkbox', 'label' => __("Can change the status opportunity/in progress", true), 'type' => 'checkbox', 'checked' => $default_user_profile['change_status_project']));
	if( !empty($enabled_menus) && !empty($enabled_menus['communications'])){
		echo $this->Form->input('can_communication', array('div' => 'wd-input wd-custom-checkbox', 'label' => __("Can communicate communication", true), 'type' => 'checkbox', 'checked' => (!empty($default_user_profile['can_communication']) ? true : false )));
	}
	if(isset($companyConfigs['display_activity_forecast']) && $companyConfigs['display_activity_forecast']){
		echo $this->Form->input('can_see_forecast', array('div' => 'wd-input wd-custom-checkbox', 'label' => __("Can see the forecast", true), 'type' => 'checkbox', 'checked' => (!empty($default_user_profile['can_see_forecast']) ? true : false )));
	}
	echo $this->Form->input('sl_budget', array(
		'type' => 'select',
		'name' => 'data[EmployeeDefault][sl_budget]',
		'id' => 'sl_budget',
		'div' => false,
		'label' => false,
		'rel' => 'no-history',
		'empty' => false,
		'options' => $sl_budget,
		'selected' => !empty($default_user_profile['sl_budget']) ? $default_user_profile['sl_budget'] : 0,
	));
	
	?>

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
<style>
	.form-style-2019 >div{
		margin-bottom: 40px;
	}
	.form-style-2019 div h4{
		margin-bottom: 10px;
	}
	.form-style-2019{
		padding: 0 20px;
	}
	.form-style-2019 .wd-input.wd-custom-checkbox label{
		font-weight: 400;
	}
	.form-style-2019 input[type="checkbox"] {
		display: inline-block;
		height: auto;
		width: auto;
	}
	.wd-tab .wd-aside-left {
		width: 245px !important;
	}
	#loadingElm{
		width: 15px;
		height: 15px;
		margin-left: 10px;
	}
	.wd-table.saving:before{
		content: '<?php __('Saving');?>';
		position: absolute;
		top: 0;
		right: 0;
		height: 20px;
		line-height: 20px;
		padding: 0 30px 0 20px;
		
	}
	.wd-content,
	.wd-table{
		position: relative;
	}
	.wd-content #formMessage{
		position: absolute;
		top: 0;
		right: 40px;
		max-width: 200px;
		z-index: 99;
	}
	#formMessage.success{
		background-color: #d5ffce;
		background-position: 15px -1505px;
		border-color: #82dc68;
	}
	#formMessage{
		background: #dbe3ff url(../img/common/message.gif) no-repeat 15px -6px;
	}
	.wd-tab .wd-panel{
		border: #d8d8d8 solid 1px;		
	}
	#accordion h3.head-title a{
		font-weight: 600 !important;
	}
</style>
<script>
var loading = "<span id='loadingElm'><img src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt='Loading' /></span>";
var timeout = 0;

/* For Custom check box */
var form_checkboxs = $('.wd-custom-checkbox');
form_checkboxs.prepend('<span class="wd-checkbox"></span>');
form_checkboxs.each(function(){
	var _this = $(this);
	var _input = _this.find('input[type="checkbox"]')
	if( _input.is(":checked") ) _this.find('.wd-checkbox').addClass('checked');
	_input.on('change', function(){
		if( _input.is(":checked") ) 
			_this.find('.wd-checkbox').addClass('checked');
		else 
			_this.find('.wd-checkbox').removeClass('checked');
	});
});
$('.wd-custom-checkbox .wd-checkbox').on('click', function(){
	$(this).closest('.wd-custom-checkbox').find('input[type="checkbox"]').click();
});
// /* End For Custom check box */

$(window).ready(function(){
	$('.wd-content').find('input').trigger('change');
	$('#EmployeeDefault').addClass('ready');
});
var wdTable = $('.wd-table');
if( wdTable.length){
	var heightTable = $(window).height() - wdTable.offset().top - 80;
	wdTable.css({
		height: heightTable,
	});
	$('.wd-aside-left').css({
		height: heightTable,
	});	
	$(window).resize(function(){
		heightTable = $(window).height() - wdTable.offset().top - 80;
		wdTable.css({
			height: heightTable,
		});
		$('.wd-aside-left').css({
			height: heightTable,
		});
	});
}
$('.wd-content').find('input').on('change keyup', function(){
	if( $(this).is(':disabled') ) return;
	var example_name = 'Giselle Alston';
	example_name = example_name.split(' ');
	var first_name = $('#EmployeeDefaultFirstName').is(':checked') ? example_name[0] : '';
	var first_letter_first_name = $('#EmployeeDefaultFirstLetterFirstName').is(':checked') ? example_name[0][0] : '';
	var last_name = $('#EmployeeDefaultLastName').is(':checked') ? example_name[1] : '';
	var first_letter_last_name = $('#EmployeeDefaultFirstLetterLastName').is(':checked') ? example_name[1][0] : '';
	var sperator = $('#EmployeeDefaultSperator').val();
	if( first_name == '' && first_letter_first_name =='') sperator = '';
	var domain_name = $('#EmployeeDefaultDomainName').val();
	var example_email = first_name + first_letter_first_name + sperator +last_name + first_letter_last_name + domain_name;
	$('#EmployeeDefaultEmailPreview').val(example_email).trigger('change');
	
});
$('#EmployeeDefault').find('input, select').on('change', function(){
	if( $(this).is(':disabled') ) return;
	if( $(this).data('type') == 'wd-number'){
		var _val = $(this).val();
		_val = parseFloat($(this).val()).toFixed(2);
		console.log( _val);
		if( _val =='NaN') _val = '0.00';
		$(this).val(_val);
	}
	var _input = $(this);
	var _form = _input.closest('form');
	var _table = _form.closest('.wd-table');
	if( !( _form.hasClass('ready'))) return;
	var _form_data = _form.serializeArray();
	var _url = _form.prop('action');
	_input.closest('.wd-input').find('label').append(loading);
	_input.prop('disabled', true);
	$.ajax({
		url: _url,
		type: 'POST',
		data: {
			data: _form_data,
		},
		success: function(respon){
			_input.closest('.wd-input').find('#loadingElm').remove();
			_input.prop('disabled', false);
			// _table.prepend(saved);
			$('#formMessage').slideDown(300)
			clearTimeout(timeout);
			timeout = setTimeout(function(){
				$('#formMessage').slideUp(300);
			}, 3000);
		}
	});

});
</script>
