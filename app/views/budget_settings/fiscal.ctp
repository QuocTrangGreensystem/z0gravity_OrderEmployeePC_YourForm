<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<style>
    .slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
    .wd-input-select input{
        padding: 5px;
        float: right;
        border: 1px solid rgb(179, 179, 179);
        width: 100px;
    }
	.wd-list-project .wd-tab .wd-content label {
		margin-top: 10px;
		width: 150px;
		display: inline-block;
		float: none;
	}
	#project_container{
		width: 350px;
		height: 500px;
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
                                <h2 class="wd-t3"><?php //echo sprintf(__("Budget Settings management of %s", true), $companyName['Company']['company_name']); ?></h2>
                                <div id="message-place">
                                    <?php
                                    App::import("vendor", "str_utility");
                                    $str_utility = new str_utility();
                                    echo $this->Session->flash();
                                    $option = array(__('No', true), __('Yes', true));
                                    ?>
                                </div>
                                <div class="wd-table" id="project_container">
                                    <div class="wd-input-select">
                                        <label><?php echo __("Fiscal year", true)?></label>
                                        <?php
                                            echo $this->Form->input('fiscal_year', array(
                                                'div' => false, 
                                                'label' => false,
    											'onchange' => "editMe('fiscal_year', this.value);",
                                                "class" => "fiscal_year",
                                                "default" => &$companyConfigs['fiscal_year'],
                                                //"options" => $option,
                                                "rel" => "no-history"
                                                ));
                                        ?>
                                    </div>
                                    <div class="wd-input-select">
                                        <label><?php echo __("Display", true)?></label>
                                        <?php
                                            echo $this->Form->input('display_fiscal_year', array(
                                                'div' => false, 
                                                'label' => false,
    											'onchange' => "editDisplay('display_fiscal_year', this.value);",
                                                "class" => "display_fiscal_year",
                                                "default" => $display,
                                                "options" => $option,
                                                "rel" => "no-history"
                                                ));
                                        ?>
                                    </div>
                                    <div class="wd-input-select">
                                        <label><?php echo __("Display Y", true)?></label>
                                        <?php
                                            echo $this->Form->input('budget_display_y', array(
                                                'div' => false, 
                                                'label' => false,
    											'onchange' => "editMe('budget_display_y', this.value);",
                                                "class" => "budget_display_y",
                                                "default" => &$companyConfigs['budget_display_y'],
                                                "options" => $option,
                                                "rel" => "no-history"
                                                ));
                                        ?>
                                    </div>
                                    <div class="wd-input-select">
                                        <label><?php echo __("Display Y+1", true)?></label>
                                        <?php
                                            echo $this->Form->input('budget_display_y_next_one', array(
                                                'div' => false, 
                                                'label' => false,
    											'onchange' => "editMe('budget_display_y_next_one', this.value);",
                                                "class" => "budget_display_y_next_one",
                                                "default" => &$companyConfigs['budget_display_y_next_one'],
                                                "options" => $option,
                                                "rel" => "no-history"
                                                ));
                                        ?>
                                    </div>
                                    <div class="wd-input-select">
                                        <label><?php echo __("Display Y+2", true)?></label>
                                        <?php
                                            echo $this->Form->input('budget_display_y_next_two', array(
                                                'div' => false, 
                                                'label' => false,
    											'onchange' => "editMe('budget_display_y_next_two', this.value);",
                                                "class" => "budget_display_y_next_two",
                                                "default" => &$companyConfigs['budget_display_y_next_two'],
                                                "options" => $option,
                                                "rel" => "no-history"
                                                ));
                                        ?>
                                    </div>
                                    <div class="wd-input-select">
                                        <label><?php echo __("Display Y-1", true)?></label>
                                        <?php
                                            echo $this->Form->input('budget_display_y_last_one', array(
                                                'div' => false, 
                                                'label' => false,
    											'onchange' => "editMe('budget_display_y_last_one', this.value);",
                                                "class" => "budget_display_y_last_one",
                                                "default" => &$companyConfigs['budget_display_y_last_one'],
                                                "options" => $option,
                                                "rel" => "no-history"
                                                ));
                                        ?>
                                    </div>
                                    <div class="wd-input-select">
                                        <label><?php echo __("Display Y-2", true)?></label>
                                        <?php
                                            echo $this->Form->input('budget_display_y_last_two', array(
                                                'div' => false, 
                                                'label' => false,
    											'onchange' => "editMe('budget_display_y_last_two', this.value);",
                                                "class" => "budget_display_y_last_two",
                                                "default" => &$companyConfigs['budget_display_y_last_two'],
                                                "options" => $option,
                                                "rel" => "no-history"
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
<script type="text/javascript">
    var loading = "<span id='loadingElm'><img src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt='Loading' /></span>";
    var company_id = <?php echo json_encode($company_id);?>;
    function editMe(field,value)
    {
    	$(loading).insertAfter('#'+field);
    	var data = field+'/'+value;
    	$.ajax({
    		url: '/company_configs/editMe/',
    		data: {
    			data : { value : value, field : field }
    		},
    		type:'POST',
    		success:function(data) {
    			$('#'+field).removeClass('KO');
    			$('#loadingElm').remove();
    		}
    	});
    }
    function editDisplay(field,value)
    {
    	$(loading).insertAfter('#'+field);
    	var data = field+'/'+value;
    	$.ajax({
    		url: '/budget_settings/editDisplayMenuOfProject/',
    		data: {
    			data : { value : value, field : field, company_id: company_id}
    		},
    		type:'POST',
    		success:function(data) {
    			$('#'+field).removeClass('KO');
    			$('#loadingElm').remove();
    		}
    	});
    }
</script>