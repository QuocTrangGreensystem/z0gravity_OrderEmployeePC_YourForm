<?php
    //echo $html->css(array('scss/modules/_checkbox', 'scss/utils/_mixins', 'scss/modules/_select.scss'));
    $langCode = Configure::read('Config.langCode');
    echo $this->Form->create('Employee');
?>
<style>
    @media(max-width: 991px){
         .page-login__panel .select{
            margin-bottom: 10px !important;
        }
    }
    .page-login__panel{
        color: #363636;
        text-align: center;
    }
    #company_role{
        color: #7B7B7B;
        border-radius: 3px;
        line-height: px;
        padding: 0;
        padding-left: 10px;
        font-size: 14px;
        font-weight: 400;
		width: 256px;
		height: 56px;
		border: 1px solid #D3D3D3;
		box-sizing: border-box;
    }
    .checkbox__tick{
        border-color: #363636;
    }
    .page-login__login-btn{
        min-width: inherit;
    }
    @media(max-width: 991px){
        .page-login__login-btn{
            margin-top: 0;
        }
    }
    @media (max-width: 48em){
        .page-login__panel {
            padding: 15px;
        }
    }
	.page-login__panel.new .page-login__input.page-login__checkbox{
		width: 240px;
		margin-bottom: 10px;
	}
	.page-login__panel.new .page-login__input label{
		top: 0;
		font-weight: normal;
		left: 25px;
	}
	.page-login__lost-password{
		font-size: 14px;
	}
	.page-login__panel.new .page-login__input.page-login__checkbox input{
		height: 20px;
		width: 20px;
		box-sizing: border-box;
		z-index: 3;
	}
	.page-login__panel.new .page-login__input label{
		cursor: pointer;
	}
	.page-login__main-holder .page-login__panel.new .message{
		margin-bottom: 0;
	}
	.checkbox_admin_is_sas{
		margin-top: 0;
	}
	.page-login__login-btn{
		display: block;
	}
</style>
<?php 
	$svg_icons = array(
		'logo-zog' => '<svg xmlns="http://www.w3.org/2000/svg" width="96" height="76" viewBox="0 0 96 76"><defs><style>.a{fill:none;}.b,.d{fill:#1c557d;}.c{fill:#217fc2;}.d{fill-rule:evenodd;}</style></defs><g transform="translate(24)"><g transform="translate(0 0)"><circle class="a" cx="24" cy="24" r="24" transform="translate(0 0)"/><path class="b" d="M18.128,26.4a24.173,24.173,0,0,1-4.837-.488,23.876,23.876,0,0,1-4.505-1.4A23.994,23.994,0,0,1,4.71,22.3,24.17,24.17,0,0,1,1.158,19.37c-.4-.4-.787-.816-1.158-1.242l3.411-3.411A19.166,19.166,0,0,0,18.128,21.6,19.21,19.21,0,0,0,37.179,0h4.83c.079.795.119,1.6.119,2.4a24.171,24.171,0,0,1-.488,4.837,23.88,23.88,0,0,1-1.4,4.505,24.007,24.007,0,0,1-2.213,4.077A24.129,24.129,0,0,1,31.547,22.3a24,24,0,0,1-4.077,2.213,23.879,23.879,0,0,1-4.505,1.4A24.173,24.173,0,0,1,18.128,26.4Z" transform="translate(5.872 21.6)"/></g><g transform="translate(0 0)"><circle class="a" cx="24" cy="24" r="24" transform="translate(0 0)"/><path class="c" d="M18.395,37.269A14.4,14.4,0,0,1,9.8,26.4H.119C.04,25.605,0,24.8,0,24a24.189,24.189,0,0,1,.487-4.837A23.872,23.872,0,0,1,4.1,10.581a24.076,24.076,0,0,1,10.56-8.7,23.891,23.891,0,0,1,4.505-1.4,24.238,24.238,0,0,1,9.673,0A23.862,23.862,0,0,1,37.418,4.1a24.152,24.152,0,0,1,2.31,1.773L36.317,9.283A19.191,19.191,0,0,0,4.949,21.6H9.8a14.328,14.328,0,0,1,2.261-5.651,14.445,14.445,0,0,1,6.336-5.217,14.379,14.379,0,0,1,13.656,1.328c.292.2.582.409.861.629l-3.433,3.433A9.6,9.6,0,1,0,33.3,26.4H24V21.6H38.2a14.555,14.555,0,0,1,.2,2.4,14.331,14.331,0,0,1-2.459,8.051,14.448,14.448,0,0,1-6.336,5.217,14.45,14.45,0,0,1-11.21,0Z" transform="translate(0 0)"/></g></g><g transform="translate(0 56)"><path class="d" d="M90.255,20.01H87.418L89.7,14.733,85.478,4.408h2.988l2.723,7.4,2.76-7.4h2.833Zm-10.529-8.3v-5.1H78.47v-2.2h1.256V1.789H82.41V4.408h2.361v2.2H82.41v5.124c0,.707.288,1.013,1.123,1.013h1.238V15H83.1C81.078,15,79.726,14.14,79.726,11.712ZM75.367,3.148A1.57,1.57,0,0,1,75.2.012,1.731,1.731,0,0,1,77,1.529c0,.017,0,.034,0,.051a1.578,1.578,0,0,1-1.588,1.567h-.049ZM65.373,15,61.489,4.408h2.856l2.665,8.128,2.666-8.128h2.836L68.607,15Zm-8.052-1.548a4.153,4.153,0,0,1-3.5,1.721c-2.665,0-4.8-2.2-4.8-5.507s2.133-5.432,4.817-5.432a4.163,4.163,0,0,1,3.484,1.682V4.408H60V15H57.321Zm-2.8-6.864a2.82,2.82,0,0,0-2.78,3.078,2.889,2.889,0,0,0,2.78,3.155,2.846,2.846,0,0,0,2.8-3.117,2.848,2.848,0,0,0-2.8-3.119Zm-9.84,3.136V15H42.015V4.408H44.68V6.053a3.654,3.654,0,0,1,3.239-1.8V7.066h-.706c-1.58,0-2.532.612-2.532,2.658ZM33.546,15.117a6.591,6.591,0,0,1-6.8-6.807,6.606,6.606,0,0,1,6.372-6.828q.2-.006.4,0a6.48,6.48,0,0,1,5,2.07L36.561,5.53a3.35,3.35,0,0,0-3.034-1.581c-2.379,0-4.036,1.7-4.036,4.359,0,2.715,1.676,4.4,4.15,4.4a3.612,3.612,0,0,0,3.765-3.078H32.84V7.582h7.2V9.914a6.5,6.5,0,0,1-6.492,5.2ZM15.4,14.964c-3.922,0-5.14-3.04-5.14-7.112C10.263,3.818,11.481.8,15.4.8s5.141,3.019,5.141,7.054c0,4.072-1.218,7.112-5.14,7.112Zm0-11.682c-2.246,0-2.513,2.18-2.513,4.569,0,2.486.266,4.628,2.513,4.628s2.513-2.142,2.513-4.628c0-2.392-.265-4.571-2.512-4.571ZM.783,12.84,5.391,6.607H.8v-2.2H8.455V6.568L3.81,12.8h4.7V15H.783ZM76.681,15H74.016V4.408h2.666Z" transform="translate(-0.783 -0.01)"/></g></svg>',
		'mail'=> '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><defs><style>.a{fill:none;}.b{fill:#7b7b7b;fill-rule:evenodd;}</style></defs><rect class="a" width="16" height="16"/><path class="b" d="M0,8.857A1.155,1.155,0,0,0,1.167,10H12.833A1.155,1.155,0,0,0,14,8.857V1.143A1.155,1.155,0,0,0,12.833,0H1.167A1.155,1.155,0,0,0,0,1.143ZM4.293,5.009,1.625,2.377a.547.547,0,0,1,0-.786.574.574,0,0,1,.8,0L6.685,5.769a.455.455,0,0,0,.624,0l4.264-4.177a.575.575,0,0,1,.8,0,.547.547,0,0,1,0,.786L9.7,5.009l2.672,2.614a.547.547,0,0,1,0,.786.576.576,0,0,1-.8,0L8.9,5.8s-.834.831-.986.98A1.334,1.334,0,0,1,7,7.143a1.316,1.316,0,0,1-.925-.374C5.921,6.62,5.093,5.8,5.093,5.8L2.426,8.409a.575.575,0,0,1-.8,0,.547.547,0,0,1,0-.786Z" transform="translate(1 3)"/></svg>'
	);
?>
<div class="page-login__panel new company-role">
	<ul class="page-login-lang">
		<?php
		$langCode = Configure::read('Config.langCode');
		?>
		<li>
			<a href="<?php echo $this->here . Router::queryString(array('hl' => 'fr') + array_diff_key($this->params['url'], array('url' => '', 'ext' => '')), array()); ?>" class="btn page-login__lang-btn fr <?php echo ($langCode == 'fr' ? 'page-login__lang-btn--active' : ''); ?>"><?php __("FR") ?></a>
		</li>
		<li>
			<a href="<?php echo $this->here . Router::queryString(array('hl' => 'en') + array_diff_key($this->params['url'], array('url' => '', 'ext' => '')), array()); ?>" class="btn page-login__lang-btn en <?php echo ($langCode == 'en' ? 'page-login__lang-btn--active' : ''); ?>"><?php __("EN") ?></a>
		</li>
		<li>
			<a href="<?php echo $this->here . Router::queryString(array('hl' => 'vi') + array_diff_key($this->params['url'], array('url' => '', 'ext' => '')), array()); ?>" class="btn page-login__lang-btn vi <?php echo ($langCode == 'vi' ? 'page-login__lang-btn--active' : ''); ?>"><?php __("VI") ?></a>
		</li>
	</ul>
    <div class="page-login__logo">
		<?php echo $svg_icons['logo-zog']; ?>
		<?php echo $session->flash();?>
    </div>
	
    <?php if ($employee_info["Employee"]["is_sas"] == 1) { ?>
    <p class="checkbox_admin_is_sas">
        <input type="checkbox" id="admin_is_sas" value="1" name="data[Employee][is_sas]" class="checkbox" />
        <label class="checkbox__label" for="admin_is_sas">
            <!--Put the label in a span with the "checkbox__text" class-->
            <span class="checkbox__text"><?php __("Admin SAS") ?></span>
            <span class="checkbox__tick"></span>
        </label>
    </p>
    <?php } ?>
    <div class="select" style="margin-bottom: 30px;">
      <select name="data[Employee][company_role]" id="company_role">
        <option value=""><?php __("Choose your company role"); ?></option>
        <?php
        if($employee_info["Employee"]["is_sas"]==1) {
            if (!empty($companies)) {
                if (count($companies) == 2)
                    $select_c = "";
                else
                    $select_c = "selected=\"selected\"";
                foreach ($companies as $key=>$ref) {
                    echo "<option $select_c value='" . $key . "'>" . $ref . "</option>";
                }
            }
        } else {
            if (!empty($employee_all_info)) {
                if (count($employee_all_info) == 2)
                    $select_c = "";
                else
                    $select_c = "selected=\"selected\"";
                foreach ($employee_all_info as $ref) {
                    echo "<option $select_c value='" . $ref['CompanyEmployeeReference']['id'] . "'>" . $ref['Company']['company_name'] . " - " . $ref['Role']['desc'] . "</option>";
                }
            }
        }
        ?>
      </select>
      <button class="page-login__login-btn btn btn--fancy"><?php __("Continue") ?></button>
    </div>
    <?php
    if($employee_info['Employee']['is_sas'] == 1) { ?>
		<div class="page-login__input page-login__checkbox">
			<input type="checkbox" name = "data[Employee][design]" id = "EmployeeNewDesign">
			<span class="checkmark"></span>
			<label for="EmployeeNewDesign"><?php __("New Design") ?></label>
		</div>
    <?php } ?>
    <a href="<?php echo $this->Html->url('/logout'); ?>" class="page-login__lost-password"><?php __("Sign out") ?></a>
</div>
<?php if( !empty($this->params['url']['continue']) ){ ?>
	<div class="wd-hide continue_login" style="display: none;">
		<?php echo $this->Form->input('continue', array(
			'type' => 'hidden',
			'value' => $this->params['url']['continue'],
		));?>
	</div> 
<?php } ?> 
<?php echo $this->Form->end();?>
<?php echo $html->script('jquery.min'); ?>
<?php echo $html->script('jquery.valid'); ?>
<script>
    $("#admin_is_sas").click(function(){
        if ($(this).attr("checked")){
            $("#company_role").val("");
            $("#company_role").attr("disabled", "disabled");
        }else{
            $("#company_role").removeAttr("disabled");
        }
    })
</script>
