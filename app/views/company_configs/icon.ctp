<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<style>
.slick-cell .multiSelect{width:auto;display:block;overflow:hidden;text-overflow:ellipsis;}.wd-select-box{border:#d8d8d8 solid 1px;padding:6px;}.wd-input-select{padding-left:40px;padding-top:10px;font-weight:700;}.wd-input-select label{display:inline-block;min-width:375px;}.wd-save{float:left;background:url(/img/front/bg-submit-save.png) no-repeat left top;cursor:pointer;height:33px;width:82px;border:none;font-size:0;}
.btn-grid{
	background-image: url(/img/grid.png);
}
.wd-input-select .btn.btn-globe {
	background-image: url(/img/ui/icon-globe.png);
}
.wd-input-select .btn.btn-plus {
    background-image: url(/img/ui/icon-plus.png);
}
.wd-input-select .btn.btn-crop {
    background-image: url(/img/ui/icon-crop.png);
}
.btn span {
    display: none;
}

.btn-text {
    background: url(/img/ui/btn-blue-left.png) left top no-repeat;
    vertical-align: top;
    cursor: pointer;
    height: 32px;
    border: 0;
    outline: 0;
    margin: 0;
    padding: 0;
    opacity: 1;
    overflow: hidden;
    position: relative;
    display: inline-block;
}
.btn-text img {
    margin: 0 3px 0 0;
    padding: 0;
    display: block;
    float: left;
}
.btn-text span {
    background: url(/img/ui/btn-blue-right.png) right top no-repeat;
    display: block;
    float: left;
    padding: 0 10px 0 5px;
    margin: 0;
    vertical-align: top;
    color: #fff;
    text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.3);
    line-height: 32px;
    height: 32px;
    font-weight: bold;
    position: relative;
}
.btn-text:hover,
.btn-text:focus {
    opacity: 0.9;
}
.wd-input-select .btn.btn-globe:before{
    content:''
}
a.btn-text {
    width:  inherit;
}
a.menu-icon{
	display: inline-block;
    text-align: center;
    min-width: 60px;
	width: inherit;
    height: 60px;
    position: relative;
    padding-top: 8px;
    border: 1px solid #F2F5F7;
    border-bottom: 4px solid #424242;
    box-sizing: border-box;
}
a.menu-icon span{
	color: #666666;
    text-transform: uppercase;
    font-size: 9px;
    font-weight: 600;
    position: relative;
    top: 5px;
    display: block;
	
}
a.menu-icon:hover{
	text-decoration: none;
}
a.dashboard {
	top: 15px;
    display: inline-block;
    text-align: center;
    min-width: 60px;
    width: inherit;
    height: 60px;
    position: relative;
    padding-top: 8px;
}
</style>
<div id="wd-container-main" class="wd-project-admin">
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
                                <div class="wd-table" id="project_container" style="width:100%;">
                                    <div id="wd-select">
                                        <?php
                                            echo $this->Form->create('Project', array('url' => array('controller' => 'company_configs', 'action' => 'resourceSettings')));
                                        ?>
										<div id="set-width" style="width: 400px;">
											<div class="wd-input-select">
												<a href="#" id="map-icon" class="btn btn-globe"></a>
												<?php
													$option = array(__('No', true), __('Yes', true));
													echo $this->Form->input('display_project_global', array(
														'div' => false,
														'label' => false,
														'id' => 'display_project_global',
														'onchange' => "editMe('display_project_global', this.value);",
														"class" => "wd-select-box",
														"default" => isset($companyConfigs['display_project_global']) ? $companyConfigs['display_project_global'] : 1,
														"options" => $option,
														"rel" => "no-history"
													));
												?>
											</div>
											<div class="wd-input-select">
												<a href="#" id="grid-icon" class="btn btn-grid"></a>
												<?php
													$option = array(__('No', true), __('Yes', true));
													echo $this->Form->input('display_project_grid', array(
														'div' => false,
														'label' => false,
														'id' => 'display_project_grid',
														'onchange' => "editMe('display_project_grid', this.value);",
														"class" => "wd-select-box",
														"default" => isset($companyConfigs['display_project_grid']) ? $companyConfigs['display_project_grid'] : 1,
														"options" => $option,
														"rel" => "no-history"
													));
												?>
											</div>
											<div class="wd-input-select">
												<a href="#" id="btnCont" class="btn-text">
													<img src="<?php echo $this->Html->url('/img/ui/blank-sort.png') ?>" alt="" />
													<span><?php __('Multiple Sort') ?></span>
												</a>
												<?php
													$option = array(__('No', true), __('Yes', true));
													echo $this->Form->input('display_muti_sort', array(
														'div' => false,
														'label' => false,
														'id' => 'display_muti_sort',
														'onchange' => "editMe('display_muti_sort', this.value);",
														"class" => "wd-select-box",
														"default" => isset($companyConfigs['display_muti_sort']) ? $companyConfigs['display_muti_sort'] : 1,
														"options" => $option,
														"rel" => "no-history"
													));
												?>
											</div>
											<div class="wd-input-select">
												<a href="#" class="btn-text">
													<img src="<?php echo $this->Html->url('/img/ui/blank-vision.png') ?>" alt="" />
													<span><?php __('Vision staffing+') ?></span>
												</a>
												<?php
													$option = array(__('No', true), __('Yes', true));
													echo $this->Form->input('dispaly_vision_staffing_new', array(
														'div' => false,
														'label' => false,
														'id' => 'dispaly_vision_staffing_new',
														'onchange' => "editMe('dispaly_vision_staffing_new', this.value);",
														"class" => "wd-select-box",
														"default" => isset($companyConfigs['dispaly_vision_staffing_new']) ? $companyConfigs['dispaly_vision_staffing_new'] : 1,
														"options" => $option,
														"rel" => "no-history"
													));
												?>
											</div>
											<div class="wd-input-select">
												<a href="#" class="btn-text">
													<img src="<?php echo $this->Html->url('/img/ui/blank-vision.png') ?>" alt="" />
													<span><?php __('Vision portfolio') ?></span>
												</a>
												<?php
													$option = array(__('No', true), __('Yes', true));
													echo $this->Form->input('display_vision_portfolio', array(
														'div' => false,
														'label' => false,
														'id' => 'display_vision_portfolio',
														'onchange' => "editMe('display_vision_portfolio', this.value);",
														"class" => "wd-select-box",
														"default" => isset($companyConfigs['display_vision_portfolio']) ? $companyConfigs['display_vision_portfolio'] : 1,
														"options" => $option,
														"rel" => "no-history"
													));
												?>
											</div>
											<div class="wd-input-select">
												<a href="#" class="btn-text">
													<img src="<?php echo $this->Html->url('/img/ui/blank-vision.png') ?>" alt="" />
													<span><?php __('Portfolio') ?></span>
												</a>
												<?php
													$option = array(__('No', true), __('Yes', true));
													echo $this->Form->input('display_portfolio', array(
														'div' => false,
														'label' => false,
														'id' => 'display_portfolio',
														'onchange' => "editMe('display_portfolio', this.value);",
														"class" => "wd-select-box",
														"default" => isset($companyConfigs['display_portfolio']) ? $companyConfigs['display_portfolio'] : 1,
														"options" => $option,
														"rel" => "no-history"
													));
												?>
											</div>
											
											<!-- Add 10-11-2018 -->
											<div class="wd-input-select">
												<a href="#" id="crop-icon" class="btn btn-crop" title="<?php __('Display Crop icon on Header Action');?>"></a>
												<?php
													$option = array(__('No', true), __('Yes', true));
													echo $this->Form->input('display_crop_icon', array(
														'div' => false,
														'label' => false,
														'id' => 'display_crop_icon',
														'onchange' => "editMe('display_crop_icon', this.value);",
														"class" => "wd-select-box",
														"default" => isset($companyConfigs['display_crop_icon']) ? $companyConfigs['display_crop_icon'] : 0,
														"options" => $option,
														"rel" => "no-history"
													));
												?>
											</div>
											<div class="wd-input-select">
												<a class="menu-icon diary-menu">
													<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
													  <defs>
														<style>
														  .cls-1 {
															fill: #666;
															fill-rule: evenodd;
														  }
														</style>
													  </defs>
													  <path id="agenda" class="cls-1" d="M1728.75,40h-17.5a1.25,1.25,0,0,1-1.25-1.25V23.125a1.25,1.25,0,0,1,1.25-1.25h5V20.63a0.618,0.618,0,0,1,.62-0.625,0.626,0.626,0,0,1,.63.625v1.245h5V20.63a0.618,0.618,0,0,1,.62-0.625,0.626,0.626,0,0,1,.63.625v1.245h5a1.25,1.25,0,0,1,1.25,1.25V38.75A1.25,1.25,0,0,1,1728.75,40Zm0-16.875h-5v0.63a0.626,0.626,0,0,1-.63.625,0.618,0.618,0,0,1-.62-0.625v-0.63h-5v0.63a0.626,0.626,0,0,1-.63.625,0.618,0.618,0,0,1-.62-0.625v-0.63h-5V38.75h17.5V23.125ZM1714.37,27.5h1.25a0.626,0.626,0,0,1,.63.625v1.25a0.626,0.626,0,0,1-.63.625h-1.25a0.624,0.624,0,0,1-.62-0.625v-1.25A0.624,0.624,0,0,1,1714.37,27.5Zm0,5h1.25a0.626,0.626,0,0,1,.63.625v1.25a0.626,0.626,0,0,1-.63.625h-1.25a0.624,0.624,0,0,1-.62-0.625v-1.25A0.624,0.624,0,0,1,1714.37,32.5Zm5-5h1.25a0.626,0.626,0,0,1,.63.625v1.25a0.626,0.626,0,0,1-.63.625h-1.25a0.624,0.624,0,0,1-.62-0.625v-1.25A0.624,0.624,0,0,1,1719.37,27.5Zm0,5h1.25a0.626,0.626,0,0,1,.63.625v1.25a0.626,0.626,0,0,1-.63.625h-1.25a0.624,0.624,0,0,1-.62-0.625v-1.25A0.624,0.624,0,0,1,1719.37,32.5Zm5-5h1.25a0.626,0.626,0,0,1,.63.625v1.25a0.626,0.626,0,0,1-.63.625h-1.25a0.624,0.624,0,0,1-.62-0.625v-1.25A0.624,0.624,0,0,1,1724.37,27.5Zm0,5h1.25a0.626,0.626,0,0,1,.63.625v1.25a0.626,0.626,0,0,1-.63.625h-1.25a0.624,0.624,0,0,1-.62-0.625v-1.25A0.624,0.624,0,0,1,1724.37,32.5Z" transform="translate(-1710 -20)"/>
													</svg>
													<span><?php echo __("Diary"); ?></span>
												</a>
												<?php
													$option = array(__('No', true), __('Yes', true));
													echo $this->Form->input('display_diary_menu', array(
														'div' => false,
														'label' => false,
														'id' => 'display_diary_menu',
														'onchange' => "editMe('display_diary_menu', this.value);",
														"class" => "wd-select-box",
														"default" => isset($companyConfigs['display_diary_menu']) ? $companyConfigs['display_diary_menu'] : 1,
														"options" => $option,
														"rel" => "no-history",
														'style' => 'top: 15px; position: relative;'
														
													));
												?>
											</div>
											<div class="wd-input-select">
												<a class="menu-icon vision-staffing-menu">
													<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
													  <defs>
														<style>
														  .cls-1 {
															fill: #424242;
															fill-rule: evenodd;
														  }
														</style>
													  </defs>
													  <path id="STAFFING" class="cls-1" d="M600,20a10,10,0,1,0,10,10A10,10,0,0,0,600,20Zm0,18.75a8.68,8.68,0,0,1-5.141-1.7,9.3,9.3,0,0,1,1.417-1.821,18.53,18.53,0,0,1,1.712-1.475,3.569,3.569,0,0,0,4.025,0,18.4,18.4,0,0,1,1.712,1.475,9.224,9.224,0,0,1,1.417,1.821A8.679,8.679,0,0,1,600,38.75Zm-3.125-9.686c0-2.244,1.4-4.063,3.125-4.063s3.126,1.819,3.126,4.063-1.4,4.062-3.126,4.062S596.874,31.307,596.874,29.064Zm9.195,7.22a8.641,8.641,0,0,0-1.428-1.87,19.043,19.043,0,0,0-1.683-1.458,5.848,5.848,0,0,0,1.417-3.892c0-2.935-1.959-5.313-4.376-5.313s-4.375,2.378-4.375,5.313a5.843,5.843,0,0,0,1.418,3.892,19.043,19.043,0,0,0-1.683,1.458,8.654,8.654,0,0,0-1.429,1.87A8.751,8.751,0,1,1,606.069,36.284Z" transform="translate(-590 -20)"/>
													</svg>
													<span><?php echo __("Staffing"); ?></span>
												</a>
												<?php
													$option = array(__('No', true), __('Yes', true));
													echo $this->Form->input('display_vision_staffing', array(
														'div' => false,
														'label' => false,
														'id' => 'display_vision_staffing',
														'onchange' => "editMe('display_vision_staffing', this.value);",
														"class" => "wd-select-box",
														"default" => isset($companyConfigs['display_vision_staffing']) ? $companyConfigs['display_vision_staffing'] : 1,
														"options" => $option,
														"rel" => "no-history",
														'style' => 'top: 15px; position: relative;'
													));
												?>
											</div>
											
											<?php 
											/* Ticket 487 */
											?>
											<div class="wd-input-select">
												<a class="menu-icon vision-staffing-menu">
													<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
													  <defs>
														<style>
														  .cls-1 {
															fill: #424242;
															fill-rule: evenodd;
														  }
														</style>
													  </defs>
													  <path id="STAFFING" class="cls-1" d="M600,20a10,10,0,1,0,10,10A10,10,0,0,0,600,20Zm0,18.75a8.68,8.68,0,0,1-5.141-1.7,9.3,9.3,0,0,1,1.417-1.821,18.53,18.53,0,0,1,1.712-1.475,3.569,3.569,0,0,0,4.025,0,18.4,18.4,0,0,1,1.712,1.475,9.224,9.224,0,0,1,1.417,1.821A8.679,8.679,0,0,1,600,38.75Zm-3.125-9.686c0-2.244,1.4-4.063,3.125-4.063s3.126,1.819,3.126,4.063-1.4,4.062-3.126,4.062S596.874,31.307,596.874,29.064Zm9.195,7.22a8.641,8.641,0,0,0-1.428-1.87,19.043,19.043,0,0,0-1.683-1.458,5.848,5.848,0,0,0,1.417-3.892c0-2.935-1.959-5.313-4.376-5.313s-4.375,2.378-4.375,5.313a5.843,5.843,0,0,0,1.418,3.892,19.043,19.043,0,0,0-1.683,1.458,8.654,8.654,0,0,0-1.429,1.87A8.751,8.751,0,1,1,606.069,36.284Z" transform="translate(-590 -20)"/>
													</svg>
													<span><?php echo __("Staffing++"); ?></span>
												</a>
												<?php
												
													$option = array(__('No', true), __('Yes', true));
													echo $this->Form->input('display_activity_vision_staffing_new', array(
														'div' => false,
														'label' => false,
														'id' => 'display_activity_vision_staffing_new',
														'onchange' => "editMe('display_activity_vision_staffing_new', this.value);",
														"class" => "wd-select-box",
														"default" => isset($companyConfigs['display_activity_vision_staffing_new']) ? $companyConfigs['display_activity_vision_staffing_new'] : 0,
														"options" => $option,
														"rel" => "no-history",
														'style' => 'top: 15px; position: relative;'
													));
												?>
											</div>
											<div class="wd-input-select">
												<a class="menu-icon activity-forecast-menu">
													<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
														<defs>
														<style>
															.cls-1 {
																fill: #424242;
																fill-rule: evenodd;
															}
														</style>
														</defs>
														<path id="Forecasts" class="cls-1" d="M600,20a10,10,0,1,0,10,10A10,10,0,0,0,600,20Zm0,18.75a8.68,8.68,0,0,1-5.141-1.7,9.3,9.3,0,0,1,1.417-1.821,18.53,18.53,0,0,1,1.712-1.475,3.569,3.569,0,0,0,4.025,0,18.4,18.4,0,0,1,1.712,1.475,9.224,9.224,0,0,1,1.417,1.821A8.679,8.679,0,0,1,600,38.75Zm-3.125-9.686c0-2.244,1.4-4.063,3.125-4.063s3.126,1.819,3.126,4.063-1.4,4.062-3.126,4.062S596.874,31.307,596.874,29.064Zm9.195,7.22a8.641,8.641,0,0,0-1.428-1.87,19.043,19.043,0,0,0-1.683-1.458,5.848,5.848,0,0,0,1.417-3.892c0-2.935-1.959-5.313-4.376-5.313s-4.375,2.378-4.375,5.313a5.843,5.843,0,0,0,1.418,3.892,19.043,19.043,0,0,0-1.683,1.458,8.654,8.654,0,0,0-1.429,1.87A8.751,8.751,0,1,1,606.069,36.284Z" transform="translate(-590 -20)"></path>
													</svg>
													<span><?php echo __("Forecasts"); ?></span>
												</a>
												<?php
													$option = array(__('No', true), __('Yes', true));
													echo $this->Form->input('display_activity_forecast', array(
														'div' => false,
														'label' => false,
														'id' => 'display_activity_forecast',
														'onchange' => "editMe('display_activity_forecast', this.value);",
														"class" => "wd-select-box",
														"default" => isset($companyConfigs['display_activity_forecast']) ? $companyConfigs['display_activity_forecast'] : 0,
														"options" => $option,
														"rel" => "no-history",
														'style' => 'top: 15px; position: relative;'
													));
												?>
											</div>
											<div class="wd-input-select">
												<a class="dashboard">
													<span><?php echo __("Dashboard"); ?></span>
												</a>
												<?php
													$option = array(__('No', true), __('Yes', true));
													echo $this->Form->input('display_project_dashboard', array(
														'div' => false,
														'label' => false,
														'id' => 'display_project_dashboard',
														'onchange' => "editMe('display_project_dashboard', this.value);",
														"class" => "wd-select-box",
														"default" => isset($companyConfigs['display_project_dashboard']) ? $companyConfigs['display_project_dashboard'] : 0,
														"options" => $option,
														"rel" => "no-history",
														'style' => 'top: 15px; position: relative;'
													));
												?>
											</div>
										</div>
										<?php 
										/* END Ticket 487 */
										?>
										<!-- End Add 10-11-2018 -->
										
                                        <?php
                                            echo $this->Form->end();
                                        ?>
                                        <div style="margin-left: 40px; margin-top: 10px;">
                                            <a href="#" class="btn-text btn-blue">
                                                <img src="<?php echo $this->Html->url('/img/ui/blank-plus.png') ?>" alt="" />
                                                <span><?php __('Add Project') ?></span>
                                            </a>
                                            <input class="radio_add_project" style="margin: 10px;" type="radio" name="add_project" value="1" checked>
                                            <a href="#" class="btn-text btn-blue">
                                                <img src="<?php echo $this->Html->url('/img/ui/blank-plus.png') ?>" alt="" />
                                            </a>
                                            <input class="radio_add_project" type="radio" name="add_project" value="0" <?php if(isset($companyConfigs['add_proroject_full_icon']) && $companyConfigs['add_proroject_full_icon'] == 0 ){ ?> checked <?php } ?>>
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
<script>
var loading = "<span id='loadingElm'><img src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt='Loading' /></span>";
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
$('.radio_add_project').on('change', function(){
    var value = $(this).val();
    var field = 'add_proroject_full_icon';
    $.ajax({
        url: '/company_configs/editMe/',
        data: {
            data : { value : value, field : field }
        },
        type:'POST',
    });
});
</script>
