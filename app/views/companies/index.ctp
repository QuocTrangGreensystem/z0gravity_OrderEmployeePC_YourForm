<?php echo $html->script('jquery.validation.min'); ?>
<?php echo $html->script('jquery.dataTables'); ?>
<?php echo $html->script('tagify.min'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<?php echo $html->css('tagify'); 
$langCode = Configure::read('Config.langCode');?>
<style type="text/css">
    .company-module .wd-input div label,.company-module .wd-input div input{float:none;width:auto;}.company-module .wd-input div input{vertical-align:middle;}#title_form_update_city{text-transform:uppercase;font-size:1.1em;}
    #group-checkbox{
        float: left;
    }
    #group-checkbox div{
        float: left;
    }
	table.display thead th{
		line-height:41px !important;
	}
	.wd-list-project .wd-tab .wd-content label {
		width: fit-content;
	}
	fieldset div.wd-input input[type=radio], fieldset div.wd-input input[type=checkbox] {
		float: left;
		margin-top: 8px;
	}
	.wd-input .datepicker{
		margin-bottom: 0;
	}
	
	.tagify__tag>div>*{
		font-size: 13px;
		line-height: 15px;

	}
	<?php if($langCode == 'fr'){?>
		.wd-list-project .wd-tab .wd-content .wd-right-content .wd-input > label{
			min-width: 200px;
		}
	<?php } else { ?>
		.wd-list-project .wd-tab .wd-content .wd-right-content .wd-input > label{
			min-width: 120px;
		}
	<?php } ?>
</style>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">

        <div class="wd-main-content">
            <div class="wd-list-project">
                <?php
                $options = Configure::read('App.modules');
                App::import("vendor", "str_utility");
                $str_utility = new str_utility();
                ?>
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
                                <h3 class="wd-t3">&nbsp;</h3>
                                <form action="" id="table-list-admin-form" style="height: 200px; overflow: auto !important;">
                                    <table cellspacing="0" cellpadding="0" class="display" id="table-list-admin">
                                        <thead>
                                            <tr class="wd-header">
                                                <th class="wd-left"><?php echo __('Company name', true); ?></th>
                                                <th class="wd-left"><?php echo __('Enabled Modules', true); ?></th>
												<th class="wd-left"><?php echo __('Day Established', true); ?></th>
                                                <th><?php echo __("Actions", true); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 0;
                                            if (!empty($tree)) {
                                            foreach ($tree as $key => $v):
                                                $value = $v[$key]["name"];
                                                $parent_id = $v[$key]["parent_id"];
                                                //$is_sas = $v[$key]["is_sas"];
												$day = $v[$key]["day_established"];
                                                $module_pms = $v[$key]["module_pms"];
                                                $module_rms = $v[$key]["module_rms"];
                                                $module_audit = $v[$key]["module_audit"];
                                                $module_report = $v[$key]["module_report"];
                                                $module_busines = $v[$key]["module_busines"];
                                                $module_zogmsgs = $v[$key]["module_zogmsgs"];
                                                $module_ticket = $v[$key]["module_ticket"];
                                                $multi_country = $v[$key]['multi_country'];
                                    			$manage_hours = $v[$key]['manage_hours'];
                                                $module_license = $v[$key]["module_license"];
                                                $unit = $v[$key]['unit'];
                                    			$ratio = $v[$key]['ratio'];
                                    			$day_alert_billing = $v[$key]['day_alert_billing'];
                                    			$day_licence = $v[$key]['day_licence'];
                                    			$actif_max = $v[$key]['actif_max'];
                                    			$no_add_more_max = $v[$key]['no_add_more_max'];
                                    			$customer_email = $v[$key]['customer_email'];
                                                $class = null;
                                                if ($module_pms == 1 && $module_rms == 0){
                                                    $totalModul = 'PMS';
                                                }elseif ($module_pms == 0 && $module_rms == 1){
                                                    $totalModul = 'RMS';
                                                }else {
                                                    $totalModul = 'PMS & RMS';
                                                }
                                                if($module_audit == 1){
                                                    $totalModul = $totalModul.' & AUDIT';
                                                }
                                                if($module_report == 1){
                                                    $totalModul = $totalModul.' & REPORT';
                                                }
                                                if($module_busines == 1){
                                                    $totalModul = $totalModul.' & BUSINES';
                                                }
                                                if($module_zogmsgs == 1){
                                                    $totalModul = $totalModul.' & ZogMsg';
                                                }
                                                if($module_ticket == 1){
                                                    $totalModul = $totalModul.' & Tickets';
                                                }
                                                $i++;
                                                ?>
                                                <tr>
                                                    <?php
                                                    $node_level = substr_count($value, '--')
                                                    ?>
                                                    <td style="padding-left: <?php echo $node_level * 20 + 20 ?>px; background: url(<?php echo $html->url('/img/treenode.png') ?>) no-repeat <?php echo $node_level * 20 + 5 ?>px 12px;"><?php echo substr($value, $node_level * 2); ?></td>
                                                    <td style="padding-left: <?php echo $node_level * 20 + 20 ?>px; background: url(<?php echo $html->url('/img/treenode.png') ?>) no-repeat <?php echo $node_level * 20 + 5 ?>px 12px;"><?php echo $totalModul; ?></td>
													<td style="padding-left: <?php echo $node_level * 20 + 20 ?>px; background: url(<?php echo $html->url('/img/treenode.png') ?>) no-repeat <?php echo $node_level * 20 + 5 ?>px 12px;"><?php echo $v[$key]["day_established"]; ?></td>
                                                    <td class="wd-action" nowrap >
                                                        <a class="wd-edit" title="Edit" href="javascript:void(0)"
                                                            onclick="editCompany('<?php echo $key; ?>', '<?php echo $parent_id; ?>', '<?php echo $value; ?>', '<?php echo $module_pms; ?>', '<?php echo $module_rms; ?>', '<?php echo $module_audit; ?>', '<?php echo $module_report; ?>', '<?php echo $module_busines; ?>', '<?php echo $module_zogmsgs; ?>', '<?php echo $module_ticket ?>', '<?php echo $is_sas; ?>', '<?php echo $day; ?>', '<?php echo $multi_country; ?>', '<?php echo $manage_hours; ?>', '<?php echo $unit; ?>', '<?php echo $ratio; ?>', '<?php echo $module_license; ?>', '<?php echo $day_alert_billing; ?>', '<?php echo $day_licence; ?>', '<?php echo $actif_max; ?>', '<?php echo $no_add_more_max; ?>', '<?php echo $customer_email; ?>');">
                                                            <?php __('Edit') ?></a>
                                                        <?php if ($is_sas == 1) { ?><div class="wd-bt-big"><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $key), array('class' => 'wd-hover-advance-tooltip'), sprintf(__('All data of this company will be deleted. Confirm ?  (Y) (N) "%s"?', true), substr($value, $node_level * 2))); ?>
                                                            </div><?php } ?>

                                                    </td>
                                                </tr>
                                                <?php
                                            endforeach;
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </form>

                                <div class="wd-add-employee">
                                    <?php echo $this->Form->create('Company', array("action" => "edit", 'id' => 'CompanyEditForm')); ?>
                                    <?php echo $validation->bind("Company", array('form' => '#CompanyEditForm')); ?>
                                    <?php echo $this->Session->flash(); ?>
                                    <fieldset>
                                        <div class="wd-scroll-form-min" style="height: auto">
                                            <div class="wd-left-content">
                                                <div class="wd-input ">
                                                    <label style="min-width:200px;" for="last-name"><?php __("Company Name") ?></label>
                                                    <?php
                                                    echo $this->Form->input('company_name', array('name' => 'data[Company][company_name]',
                                                        'type' => 'text',
                                                        'div' => false,
                                                        'label' => false,
                                                        'disabled' => ($isAdminLevel2 == true) ? true : false,
                                                        'style' => ' width:150px ',
                                                        "class" => "placeholder", "placeholder" => __("Company Name (*)", true))
                                                    );
                                                    ?>
                                                </div>
												<div class="wd-input ">
                                                    <label style="min-width:200px;" for="last-name"><?php __("Day Established") ?></label>
                                                    <?php
                                                    echo $this->Form->input('day_established', array('name' => 'data[Company][day_established]',
                                                        'type' => 'text',
                                                        'div' => false,
                                                        'label' => false,
                                                        'disabled' => ($isAdminLevel2 == true) ? true : false,
                                                        'style' => ' width:150px ',
                                                        "class" => "placeholder datepicker", "placeholder" => __("Day Established", true) . ' (*)')
                                                    );
                                                    ?>
                                                </div>
                                                <?php
                                                if ($is_sas == 1) {
                                                    ?>
                                                <div class="wd-input">
                                                    <label style="min-width:200px;" for="last-name"><?php __("Multi countries") ?></label>
                                                    <?php
        												$option = array(__('No', true), __('Yes', true));
        												echo $this->Form->input('multi_country', array(
                                                            'name' => 'data[Company][multi_country]',
        													'div' => false,
        													'label' => false,
        													"class" => "wd-select-box",
                                                            'disabled' => ($isAdminLevel2 == true) ? true : false,
                                                            'style' => 'width: 56px',
        													"options" => $option,
        													"rel" => "no-history"
        												));
        											?>

                                                </div>
                                                <div class="wd-input" style="clear: both">
                                                    <label style="min-width:200px;" for="last-name"><?php __("Manage hours") ?></label>
                                                    <?php
        												$option = array(__('No', true), __('Yes', true));
        												echo $this->Form->input('manage_hours', array(
                                                            'name' => 'data[Company][manage_hours]',
        													'div' => false,
        													'label' => false,
        													"class" => "wd-select-box",
                                                            'disabled' => ($isAdminLevel2 == true) ? true : false,
                                                            'style' => 'width: 56px',
                                                            // 'value' => $tree
        													"options" => $option,
        													"rel" => "no-history"
        												));
        											?>
                                                    <div id='wd-unit-ratio'>
                                                        <label style="width:50px;" for="last-name"><?php __("UNIT") ?></label>
                                                        <?php
                                                        echo $this->Form->input('company_unit', array(
                                                            'name' => 'data[Company][unit]',
                                                            'type' => 'text',
                                                            'div' => false,
                                                            'label' => false,
                                                            'style' => 'width:50px'
                                                        ));
                                                        ?>
                                                        <label style="width:50px;" for="last-name"><?php __("RATIO") ?></label>
                                                        <?php
                                                        echo $this->Form->input('company_ratio', array(
                                                            'name' => 'data[Company][ratio]',
                                                            'type' => 'text',
                                                            'div' => false,
                                                            'label' => false,
                                                            'style' => 'width:50px',
                                                        ));
                                                        ?>
                                                    </div>
                                                </div>
												<div class="wd-input">
                                                    <label style="min-width:200px;" for="last-name"><?php __("Customer email") ?></label>
                                                    <?php
        												echo $this->Form->input('customer_email', array(
                                                            'name' => 'data[Company][customer_email]',
															'type' => 'text',
															'div' => false,
															'label' => false,
															'placeholder' => __('email@example.com', true)
        												));
        											?>

                                                </div>
                                                <?php } ?>
                                            </div>
                                            <div class="wd-right-content">
                                                <div class="wd-input">
                                                    <?php
                                                    if ($is_sas == 1) {
                                                        ?>
														<div class="wd-input" style="clear: both">
															<label for="last-name"><?php __("Company Parent") ?></label>
															<select name="data[Company][parent_id]" id = "CompanyParentId">
																<option value=""><?php __("---Select parent---") ?></option>
																<?php
																foreach ($tree_ as $key => $value) {
																	echo "<option value='" . $key . "'>" . str_replace('->', '--', $value) . "</option>";
																}
																?>
															</select>
														</div>
														<div class="wd-input" style="clear: both">
															<label for="last-name"><?php __("End of licence") ?></label>
															<?php
															echo $this->Form->input('day_licence', array('name' => 'data[Company][day_licence]',
																'type' => 'text',
																'div' => false,
																'label' => false,
																'style' => ' width:150px ',
																"class" => "placeholder datepicker", "placeholder" => __("End of licence", true),
															));
															?>
														</div>
														<div class="wd-input" style="clear: both">
															<label for="last-name"><?php __("Alert billing") ?></label>
															<?php
															echo $this->Form->input('day_alert_billing', array('name' => 'data[Company][day_alert_billing]',
																'type' => 'text',
																'div' => false,
																'label' => false,
																'style' => ' width:150px ',
																"class" => "placeholder datepicker", "placeholder" => __("Alert billing", true)
															));
															?>
														</div>
														<div class="wd-input" style="clear: both">
															<label for="last-name"><?php __("Actif max") ?></label>
															<?php
															echo $this->Form->input('actif_max', array(
																'name' => 'data[Company][actif_max]',
																'type' => 'text',
																'div' => false,
																'label' => false,
																'style' => 'width:50px'
															));
															
															echo $this->Form->input('Company.no_add_more_max', array(
																'type' => 'checkbox',
																'legend' => false,
																'fieldset' => false,
																'checked' => false,
																'label' => __("Cannot create more than actif max", true)
															));
															?>
															
														</div>
                                                        <?php
                                                    } else {
                                                        echo $this->Form->input('parent_id', array('name' => 'data[Company][parent_id]', 'type' => 'hidden', 'value' => $company_id));
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <?php echo $this->Form->input('id', array('name' => 'data[Company][id]', 'type' => 'hidden')); ?>
                                        </div>
                                        <div class="company-module">
                                            <div class="wd-input">
                                                <?php if ($is_sas == 1) { ?>
                                                    <label style="min-width:200px;" for="last-name"><?php __("Enable Modules") ?>:</label>
                                                    <div id="group-checkbox" style="display: inline-block;">
                                                        <?php
                                                        echo $this->Form->input('Company.module', array(
                                                            'options' => $options,
                                                            'type' => 'radio',
                                                            'legend' => false,
                                                            'fieldset' => false,
                                                            'value' => 0,
                                                            'label' => __("Enable Modules", true)
                                                        ));
                                                        echo $this->Form->input('Company.module_audit', array(
                                                            'type' => 'checkbox',
                                                            'legend' => false,
                                                            'fieldset' => false,
                                                            'value' => 'audit',
                                                            'label' => __("AUDIT", true)
                                                        ));
                                                        ?>
                                                    </div>
                                                    <div id="group-checkbox">
                                                        <?php
                                                    echo $this->Form->input('Company.module_report', array(
                                                        'type' => 'checkbox',
                                                        'legend' => false,
                                                        'fieldset' => false,
                                                        'value' => 'report',
                                                        'label' => __("REPORT", true)
                                                    ));
                                                    echo $this->Form->input('Company.module_busines', array(
                                                        'type' => 'checkbox',
                                                        'legend' => false,
                                                        'fieldset' => false,
                                                        'value' => 'busines',
                                                        'label' => __("BUSINES", true)
                                                    ));
                                                    echo $this->Form->input('Company.module_zogmsgs', array(
                                                        'type' => 'checkbox',
                                                        'legend' => false,
                                                        'fieldset' => false,
                                                        'value' => 'zogmsg',
                                                        'label' => __("ZogMsg", true)
                                                    ));
                                                    echo $this->Form->input('Company.module_ticket', array(
                                                        'type' => 'checkbox',
                                                        'legend' => false,
                                                        'fieldset' => false,
                                                        'value' => '1',
                                                        'label' => __("Tickets", true)
                                                    ));
                                                    echo $this->Form->input('Company.module_license', array(
                                                        'type' => 'checkbox',
                                                        'legend' => false,
                                                        'fieldset' => false,
                                                        'value' => '0',
                                                        'label' => __("Licenses", true)
                                                    ));
                                                    ?>
                                                    </div>
                                                <?php } else {echo $this->Form->hidden('Company.module');} ?>
                                            </div>
                                        </div>
                                        <div class="wd-submit">
                                            <button type="submit" class="wd-button-f wd-save-project" id="btnSave" />
                                                <span><?php __('Save') ?></span>
                                            </button>
                                            <a href="javascript:reset_form()" id="reset_button" class="wd-reset"><?php __('Reset') ?></a>
                                        </div>
                                    </fieldset>
                                    <?php echo $this->Form->end(); ?>
                                    </form>
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
	$('.datepicker').datepicker({
		dateFormat : 'dd-mm-yy',
		showOn : 'focus',
	});
    function reset_form(){
        $("#CompanyCompanyName").val('');
        <?php if ($isAdminLevel2) { ?>$("#CompanyCompanyName").attr("disabled","true"); <?php } ?>
        $("#CompanyId").val('');
        $("#title_form_update_city").html("<?php __("Add a new company") ?>");
        $(".company-module .radio input").prop('checked' , false).first().prop('checked' , true);
        $('#CompanyAudit').prop('checked' , false);
        $('#Company').prop('checked' , false);
        $('#CompanyAudit').prop('checked' , false);
        $('#CompanyAudit').prop('checked' , false);
        $("#flashMessage").hide();
        $(".error-message").hide();
        $("div.wd-input input, select").removeClass("form-error");
    }
    function editCompany(company_id, company_parent_id, company_name , module_pms, module_rms, module_audit, module_report, module_busines, module_zogmsgs, ticket, is_sas, day, multi_country, manage_hours, unit, ratio, module_license, day_alert_billing, day_licence, actif_max, no_add_more_max, customer_email){
        $("#CompanyId").val(company_id);
        $("#CompanyParentId").val(company_parent_id);
        $("#CompanyCompanyName").removeAttr("disabled");
        if((module_pms == 1) && (module_rms == 0)){
            $(".company-module .radio input").prop('checked' , false).first().prop('checked' , true);
        } else if((module_pms == 0) && (module_rms == 1)){
            $(".company-module .radio input").prop('checked' , false);
            $("#CompanyModule1").prop('checked' , true);
        } else {
            //$('#CompanyPms').prop('checked' , false);
            $('#CompanyModule2').prop('checked' , true);
        }

        if(module_audit == 1){
             $('#CompanyModuleAudit').prop('checked' , true);
        } else {
             $('#CompanyModuleAudit').prop('checked' , false);
        }
        if(module_report == 1){
             $('#CompanyModuleReport').prop('checked' , true);
        } else {
             $('#CompanyModuleReport').prop('checked' , false);
        }
        if(module_busines == 1){
             $('#CompanyModuleBusines').prop('checked' , true);
        } else {
             $('#CompanyModuleBusines').prop('checked' , false);
        }
        if(module_zogmsgs == 1){
             $('#CompanyModuleZogmsgs').prop('checked' , true);
        } else {
             $('#CompanyModuleZogmsgs').prop('checked' , false);
        }
        if(ticket == 1){
             $('#CompanyModuleTicket').prop('checked' , true);
        } else {
             $('#CompanyModuleTicket').prop('checked' , false);
        }
        if(module_license == 1){
             $('#CompanyModuleLicense').prop('checked' , true);
        } else {
             $('#CompanyModuleLicense').prop('checked' , false);
        }
        if(multi_country == 1){
            $('#CompanyMultiCountry').val(1);
        } else {
            $('#CompanyMultiCountry').val(0);
        }
        if(manage_hours == 1){
            $('#CompanyManageHours').val(1);
        } else {
            $('#CompanyManageHours').val(0);
        }
        if(no_add_more_max == 1){
            $('#CompanyNoAddMoreMax').prop('checked' , true);
        } else {
            $('#CompanyNoAddMoreMax').prop('checked' , false);
        }
        // if(is_sas){
        //     $('#CompanyModule').val(moduleAdmin);
        // }
        $("#CompanyCompanyName").val(company_name);
		$("#CompanyDayEstablished").val(day);
        $("#CompanyDayAlertBilling").val(day_alert_billing);
        $("#CompanyDayLicence").val(day_licence);
		$("#CompanyActifMax").val(actif_max);
        if(!unit){
            unit = 'M.D';
        }
        $("#CompanyCompanyUnit").val(unit);
        $("#CompanyCompanyRatio").val(ratio);
        $("#title_form_update_city").html("<?php __("Edit the company") ?>");
        $("#flashMessage").hide();
        $(".error-message").hide();
        $("div.wd-input input, select").removeClass("form-error");
        if($('#CompanyManageHours').val() != 0){
            $('#wd-unit-ratio').hide();
        }
		tagify.removeAllTags();
		if(customer_email){
			tagify.addTags(customer_email);
		}
    }
    $("#btnSave").click(function(){
        $("#flashMessage").hide();
    });
    if($('#CompanyManageHours').val() != 0){
        $('#wd-unit-ratio').hide();
    }
    $('#CompanyManageHours').click(function(){
        if($('#CompanyManageHours').val() == 0){
            $('#wd-unit-ratio').show();
        } else {
            $('#wd-unit-ratio').hide();
        }
    });
	
</script>

<script type="text/javascript">

	var tagify;
	(function(){
		var inputElm = document.querySelector('input[name="data[Company][customer_email]"]');
		// initialize Tagify on the above input node reference
		 tagify = new Tagify(inputElm, {
			pattern: /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/,
		 });
	})()
</script>

<script type="text/javascript">
var wdTable = $('#wd-fragment-2');

    (function($){
        $(function(){
            $('#replace-attachment').click(function(){
                $('#loader').show();
                $.ajax({
                    type : 'POST',
                    url : '<?php echo $html->url('/companies/delete/' . @$company_id) ?>',
                    success : function(){
                        $('#loader').hide();
                        $('#download-place').remove();
                        $('#upload-place').show();
                        $('iframe').prop('src', 'about:blank');
                        $('#wd-fragment-2').html('');
                    }
                });
            });
        });
    })(jQuery);
    $("#attachmentUrl").parent().hide();
    $('#CompanyIsFile1, #CompanyIsFile0').click(function(){
       if($('#CompanyIsFile1').is(':checked')) {
            $("#CompanyAttachment").parent().show();
            $("#attachmentUrl").parent().hide();
            $('#file-types').show();
        } else {
            $("#CompanyAttachment").parent().hide();
            $("#attachmentUrl").parent().show();
            $('#file-types').hide();
        }
    });
   
</script>