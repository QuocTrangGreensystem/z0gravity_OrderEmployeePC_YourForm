<?php
echo $html->script('jquery.multiSelect');
echo $html->css('jquery.multiSelect');
echo $html->css('slick_grid/slick.edit');
echo $this->element('dialog_projects');
echo $this->Html->script('multiple-select');
echo $this->Html->css('multiple-select');
echo $html->css('projects');
echo $this->Html->script('progress/nanobar1');
echo $this->Html->script('html2canvas-0.5/html2canvas05');
echo $this->Html->script('html2canvas-0.5/html2canvas05.svg');
echo $this->Html->script('clipboard.min');
?>
<script type="text/javascript" src="/js/jquery-1.12.4/jquery.min.js"></script>
<script type="text/javascript">
    var j$ = $.noConflict(true);
</script>
<?php 
	echo $html->css('preview/z0g_header.css');
	
	 $employee_info = $this->Session->read('Auth.employee_info');
	 $enableZogMsgs = $this->Session->read('enableZogMsgs');
	 /* 
	 ** Update them 3 bien de kiem tra co cho hien thi man hinh Business, Audit, Report 06/05/2019 by QuanNV
	 */
	 $enableBusines = $this->Session->read('enableBusines');
	 $enableAudit   = $this->Session->read('enableAudit');
	 $enableReport  = $this->Session->read('enableReport');
	 /*
	 *	End update 06/05/2019
	 */
	 $is_sas = $employee_info['Employee']['is_sas'] && empty($employee_info['Company']['id']);
	 $role = ($is_sas != 1) ? $employee_info['Role']['name'] : '';
	 $this->is_pm = isset($employee_info['Role']['id']) && $employee_info['Role']['name'] =='pm';
	 $pm_control_resource = isset($employee_info['Role']['id']) && $employee_info['Role']['name'] =='pm' && $employee_info['CompanyEmployeeReference']['control_resource'] == '1';
	 $companyConfigs = isset( $companyConfigs ) ? $companyConfigs : array();
	 $employee_id = empty($employee_id) ? $employee_info['Employee']['id'] : $employee_id;
	 
     //------------------------
	 
	 $companyName = !empty($employee_info['Company']['company_name']) ? $employee_info['Company']['company_name'] : ''; 
     $first_name = !empty($employee_info['Employee']['first_name']) ? $employee_info['Employee']['first_name'] : '';
     $last_name = !empty($employee_info['Employee']['last_name']) ? $employee_info['Employee']['last_name'] : '';
     $full_name = !empty($employee_info['Employee']['fullname']) ? $employee_info['Employee']['fullname'] : '';
	 $employeeIdLogin = !empty($employee_info['Employee']['id']) ? $employee_info['Employee']['id'] : '';
     $urlAvatar = $this->UserFile->avatar($employeeIdLogin);
	 
	
	$sub_num = 0;
	if($is_sas || (!$is_sas && $role =='admin')){
		$sub_num += 1;
	}
	
	
	// Permission access rerources screen
	$checkSeeResource = isset($employee_info['Role']['id']) && ($employee_info['Role']['name'] != "admin") && ($employee_info['Role']['name'] != "hr") &&  !$pm_control_resource;
	if(!$checkSeeResource) $sub_num += 1;
	
	// Permission access persionalize view screen
	$checkSeePersonalizedViews = isset($employee_info['Role']['id']) && ($employee_info['Role']['name'] != "admin") && ($employee_info['Role']['name'] != "hr") && !$this->is_pm;
	if (!$checkSeePersonalizedViews && $role !='conslt') $sub_num += 1;
	
	// Activity
	$enableRMS = $this->Session->read('enableRMS');
	
	// ABSENCE
	$display_absence_tab = isset($companyConfigs['display_absence_tab']) ? $companyConfigs['display_absence_tab'] : 1;
	
    // Assistant
	$display_my_assistant = Configure::read('Config.displayAssistant');
	
	// MY DIARY
	$hasManagerMyDiary = (!empty($employee_id) && !empty($employee_info['Company']['id']) && ($profitMyDiary = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer')->find('first', array(
            'recursive' => -1,
            'conditions' => array('employee_id' => $employee_id)))));
	if($hasManagerMyDiary){
		$profitMyDiary = array_shift($profitMyDiary);
	}
	$profitMyDiary = !empty($profitMyDiary['profit_center_id']) ? $profitMyDiary['profit_center_id'] : '';
	
	// get controller
	$_this_controller =  trim( str_replace('_preview', '', $this->params['controller'] ), ' \t\n\r\0\x0B_');
	$_this_action = $this->params['action'];
	//get controller chua xoa preview
	$this_controller_preview =  $this->params['controller'];
?>

<!--
Header ZOG
Created by VN
Date: 06/12/2018


 --- Start z0g header -->
<div class="z0g-header">
	<div id="header_padding_assitant"></div>
    <ul class="small-header">
        <li class="logo">
            <a href="<?php echo '/projects/'?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 80 80">
				<defs>
					<style>
					.a{
						fill:none;
					}
					.b{
						fill:#242424;
					}
					.c{
						fill:#0070b9;
					}
					</style>
				</defs>
				<g transform="translate(-56 -32)"><rect class="a" width="80" height="80" transform="translate(56 32)"/><path class="b" d="M147.612,20.638h-7.529a33.272,33.272,0,0,1,.17,3.335A32.5,32.5,0,0,1,86.5,48.558l-5.308,5.317a39.993,39.993,0,0,0,66.559-29.9C147.748,22.85,147.7,21.735,147.612,20.638Z" transform="translate(-11.748 48.027)"/><path class="c" d="M119.6,81.33a25,25,0,0,0,25-25,25.338,25.338,0,0,0-.221-3.335H119.6v6.67h17.184A17.5,17.5,0,1,1,130.2,42.4l5.334-5.342A25,25,0,0,0,94.829,52.993H87.275a32.5,32.5,0,0,1,53.586-21.251l5.308-5.317a39.993,39.993,0,0,0-66.559,29.9c0,1.123.043,2.237.136,3.335H94.829A25,25,0,0,0,119.6,81.33Z" transform="translate(-23.61 15.672)"/></g>
				</svg>
            </a>
        </li>
        <li class="user-info">
            <a href="<?php echo $this->Html->url('/employees_preview/my_profile') ?>" >
                <div class="employee-name">
                    <p class="name"><?php echo $first_name ?><span><?php echo $last_name ?></span></p>
                    <p><?php echo $companyName; ?></p>
                </div>
                <div class="img-inner"><img src="<?php echo $urlAvatar ?>" alt="<?php __('Login');?>"></div>
            </a>
            <p class="burger">
                <span class="menu-icon">
                    <span class="line"></span>
                    <span class="line"></span>
                    <span class="line"></span>
                </span>
            </p>
        </li>
    </ul>
    <div class="z0g-header-inner">
        <div class="header-action header-action-left">
            <ul>
                <!-- ***Quan update When Consultant click logo then redirect Activity/Request screen*** - 14/01/2019 -->
                <li class="logo">
					<?php if($role =='conslt'){?>
                    <a href="<?php echo '/activity_forecasts/request/'?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 80 80">
						<defs>
							<style>
							.a{
								fill:none;
							}
							.b{
								fill:#242424;
							}
							.c{
								fill:#0070b9;
							}
							</style>
						</defs>
						<g transform="translate(-56 -32)"><rect class="a" width="80" height="80" transform="translate(56 32)"/><path class="b" d="M147.612,20.638h-7.529a33.272,33.272,0,0,1,.17,3.335A32.5,32.5,0,0,1,86.5,48.558l-5.308,5.317a39.993,39.993,0,0,0,66.559-29.9C147.748,22.85,147.7,21.735,147.612,20.638Z" transform="translate(-11.748 48.027)"/><path class="c" d="M119.6,81.33a25,25,0,0,0,25-25,25.338,25.338,0,0,0-.221-3.335H119.6v6.67h17.184A17.5,17.5,0,1,1,130.2,42.4l5.334-5.342A25,25,0,0,0,94.829,52.993H87.275a32.5,32.5,0,0,1,53.586-21.251l5.308-5.317a39.993,39.993,0,0,0-66.559,29.9c0,1.123.043,2.237.136,3.335H94.829A25,25,0,0,0,119.6,81.33Z" transform="translate(-23.61 15.672)"/></g>
						</svg>
                    </a>
                    <?php } else {?>
                    <a href="<?php echo '/projects/'?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 80 80">
						<defs>
							<style>
							.a{
								fill:none;
							}
							.b{
								fill:#242424;
							}
							.c{
								fill:#0070b9;
							}
							</style>
						</defs>
						<g transform="translate(-56 -32)"><rect class="a" width="80" height="80" transform="translate(56 32)"/><path class="b" d="M147.612,20.638h-7.529a33.272,33.272,0,0,1,.17,3.335A32.5,32.5,0,0,1,86.5,48.558l-5.308,5.317a39.993,39.993,0,0,0,66.559-29.9C147.748,22.85,147.7,21.735,147.612,20.638Z" transform="translate(-11.748 48.027)"/><path class="c" d="M119.6,81.33a25,25,0,0,0,25-25,25.338,25.338,0,0,0-.221-3.335H119.6v6.67h17.184A17.5,17.5,0,1,1,130.2,42.4l5.334-5.342A25,25,0,0,0,94.829,52.993H87.275a32.5,32.5,0,0,1,53.586-21.251l5.308-5.317a39.993,39.993,0,0,0-66.559,29.9c0,1.123.043,2.237.136,3.335H94.829A25,25,0,0,0,119.6,81.33Z" transform="translate(-23.61 15.672)"/></g>
						</svg>
                    </a>							
					<?php } ?>					
                </li>
                <!-- ***Quan update redirect and authorized display menu Grid, List, Staffing*** - 12/01/2019 -->
				<?php if($role !='conslt' && !$is_sas){?>
					<?php if(!isset($companyConfigs['display_project_grid']) || $companyConfigs['display_project_grid'] == 1) { ?>

                <li class="<?php echo ($_this_controller == 'projects' && $_this_action == 'index_plus') ? "wd-current" : "" ?>">
                    <a href="<?php echo '/projects/index_plus/'?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
							<defs>
							<style>
								.cls-1{
									fill:#666;
									fill-rule: evenodd;
								}
							</style>
							</defs>
							<g transform="translate(-31 -80)"><path class="cls-1" d="M15.611,8.778A4.389,4.389,0,1,1,20,4.389,4.394,4.394,0,0,1,15.611,8.778Zm0-7.064a2.675,2.675,0,1,0,2.675,2.675A2.678,2.678,0,0,0,15.611,1.714ZM10.587,9.961h-3a.857.857,0,1,1,0-1.714h3a.857.857,0,1,1,0,1.714Zm-3,3.619a.857.857,0,1,1,0-1.714h3a.857.857,0,0,1,0,1.714Zm1.5-10.041H4.746A3.035,3.035,0,0,0,1.714,6.57v8.684a3.035,3.035,0,0,0,3.031,3.031H13.43a3.028,3.028,0,0,0,3.031-3.031V10.913a.857.857,0,1,1,1.714,0v4.342A4.743,4.743,0,0,1,13.43,20H4.746A4.751,4.751,0,0,1,0,15.254V6.57A4.751,4.751,0,0,1,4.746,1.825H9.087a.857.857,0,0,1,0,1.714Z" transform="translate(33 82)"/></g>
						</svg>
                        <span><?php echo __("Grid"); ?></span>
                    </a>
                </li>

					<?php } ?>
                <li class="<?php echo ($_this_controller == 'projects' && $_this_action == 'index') ? "wd-current" : "" ?>">
                    <a href="<?php echo '/projects/'?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
							<defs>
								<style>
									.cls-1 {
										fill: #666;
										fill-rule: evenodd;
									}
								</style>
							</defs>
							<g transform="translate(-40 -80)"><path class="cls-1" d="M18,20a.857.857,0,0,1-.857-.857V8.309a.857.857,0,1,1,1.714,0V19.143A.857.857,0,0,1,18,20Zm-2.206,0a.857.857,0,0,1-.857-.857V5.637a.857.857,0,1,1,1.713,0V19.143A.857.857,0,0,1,15.794,20Zm-3.432,0H1.9A1.9,1.9,0,0,1,0,18.1V1.9A1.9,1.9,0,0,1,1.9,0H12.362a1.9,1.9,0,0,1,1.9,1.9V18.1A1.9,1.9,0,0,1,12.362,20ZM12.55,1.9a.188.188,0,0,0-.187-.187H1.9a.188.188,0,0,0-.188.187V18.1a.188.188,0,0,0,.188.187H12.362a.188.188,0,0,0,.187-.187ZM9.669,10.857H4.426a.857.857,0,0,1,0-1.714H9.669a.857.857,0,1,1,0,1.714Zm0-3.343H4.426a.857.857,0,0,1,0-1.714H9.669a.857.857,0,1,1,0,1.714ZM4.514,12.486h3.51a.857.857,0,0,1,0,1.714H4.514a.857.857,0,0,1,0-1.714Z" transform="translate(42 82)"/></g>
						</svg>
                        <span><?php echo __("List"); ?></span>
                    </a>
                </li>
					<?php if(!isset($companyConfigs['display_project_global']) || $companyConfigs['display_project_global'] == 1) { ?>
                <li class="<?php echo ($_this_controller == 'projects' && $_this_action == 'map') ? "wd-current" : "" ?>">
                    <a href="<?php echo '/projects_preview/map/'?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                        <defs>
                        <style>
                            .cls-1 {
                                fill: #666;
                                fill-rule: evenodd;
                            }
                        </style>
                        </defs>
                        <path id="GEOLOC" class="cls-1" d="M520,40c-1.331.006-8-9.364-8-12.5a8.017,8.017,0,0,1,16,0C528,30.593,521.307,40.006,520,40Zm0-18.75a6.469,6.469,0,0,0-6.668,6.25c0,2.613,5.558,10.63,6.668,10.625,1.09,0.005,6.666-8.047,6.666-10.625A6.469,6.469,0,0,0,520,21.249Zm0,9.375a3.132,3.132,0,1,1,3.332-3.125A3.233,3.233,0,0,1,520,30.624Zm0-5a1.879,1.879,0,1,0,2,1.875A1.941,1.941,0,0,0,520,25.624Z" transform="translate(-510 -20)"/>
                        </svg>
                        <span><?php echo __("Plan"); ?></span>
                    </a>
                </li>
					<?php }
					if( $enableRMS){
					if(!isset($companyConfigs['display_vision_staffing']) || $companyConfigs['display_vision_staffing'] == 1) { ?>
                <li>
                    <a href="#" id="add_vision_staffing_news_menu" class="add_vision_staffing_news_menu">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
						<defs>
							<style>
								.cls-1 {
									fill: #666;
									fill-rule: evenodd;
								}
							</style>
						</defs>
						<g transform="translate(-104 -67)"><path class="cls-1" d="M122.491,191.893a.994.994,0,0,0,1.1-.864c.475-4.1,1.869-6.116,4.66-6.754a.977.977,0,0,0,.064-1.894,4.338,4.338,0,0,1-3.14-4.166,4.428,4.428,0,0,1,8.854,0,4.338,4.338,0,0,1-3.14,4.166.977.977,0,0,0,.064,1.894c2.792.638,4.186,2.658,4.661,6.754a.992.992,0,0,0,.99.87,1.04,1.04,0,0,0,.114-.006.985.985,0,0,0,.878-1.086c-.3-2.564-1.056-5.914-4.027-7.616a6.244,6.244,0,0,0,2.456-4.977,6.423,6.423,0,0,0-12.845,0,6.243,6.243,0,0,0,2.456,4.976c-2.971,1.7-3.73,5.051-4.027,7.616A.985.985,0,0,0,122.491,191.893Z" transform="translate(-13.606 -102.899)"/></g>
						</svg>
                        <span><?php echo __("Staffing"); ?></span>
                    </a>
                </li>
					<?php } ?>
					
					<?php if(!empty($companyConfigs['display_activity_vision_staffing_new'])) { ?>
                <li>
                    <a href="#" id="activity_vision_staffing_news" class="activity_vision_staffing_news">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
						<defs>
							<style>
								.cls-1 {
									fill: #666;
									fill-rule: evenodd;
								}
							</style>
						</defs>
						<g transform="translate(-104 -67)"><g transform="translate(106 69)"><path class="cls-1" d="M1.623,44.536c.388-3.464,1.526-5.171,3.806-5.711a.834.834,0,0,0,.052-1.6,3.69,3.69,0,0,1,1.052-7.2,3.69,3.69,0,0,1,1.052,7.2.834.834,0,0,0,.052,1.6c2.279.539,3.419,2.248,3.806,5.711a.818.818,0,0,0,.9.73.826.826,0,0,0,.717-.918c-.243-2.169-.862-5-3.289-6.439A5.366,5.366,0,0,0,6.533,28.36a5.366,5.366,0,0,0-3.239,9.548C.867,39.347.248,42.18,0,44.347a.826.826,0,0,0,.717.918.888.888,0,0,0,.093.005A.819.819,0,0,0,1.623,44.536Z" transform="translate(0 -25.271)"/><path class="cls-1" d="M94.982,9.549A5.348,5.348,0,0,0,96.988,5.34,5.3,5.3,0,0,0,91.743,0a5.215,5.215,0,0,0-3.877,1.744.841.841,0,0,0,.053,1.172.8.8,0,0,0,1.151-.054,3.593,3.593,0,0,1,2.673-1.2A3.653,3.653,0,0,1,95.36,5.34,3.651,3.651,0,0,1,92.8,8.863a.834.834,0,0,0,.052,1.6c2.28.539,3.418,2.248,3.806,5.711a.82.82,0,0,0,.809.735.888.888,0,0,0,.093-.005.826.826,0,0,0,.717-.918C98.029,13.82,97.409,10.987,94.982,9.549Z" transform="translate(-78.278)"/></g></g>
						</svg>
                        <span><?php echo __("Staffing++"); ?></span>
                    </a>
                </li>
					<?php } ?>
					<?php } // Enable RMS ?> 
				<?php } ?>
                <!--End Update - 12/01/2019 -->
				<?php if(!$is_sas && $role !='conslt' && $enableZogMsgs == true){ ?>
                <li>
                    <a id="menu_zog_msg" href="javascript:void(0)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                        <defs>
                        <style>
                            .cls-1 {
                                fill: #666;
                                fill-rule: evenodd;
                            }
                        </style>
                        </defs>
                        <path id="Z0gMSG" class="cls-1" d="M683.124,30h-6.249a0.625,0.625,0,1,0,0,1.25h6.249A0.625,0.625,0,1,0,683.124,30ZM680,20c-5.523,0-10,3.918-10,8.75a8.375,8.375,0,0,0,3.75,6.824V40l5.12-2.56c0.371,0.036.747,0.059,1.13,0.059,5.523,0,10-3.917,10-8.749S685.523,20,680,20Zm0,16.25c-1.435,0-1.25,0-1.25,0L675,38.125V34.864a7.213,7.213,0,0,1-3.751-6.114c0-4.142,3.918-7.5,8.751-7.5s8.749,3.358,8.749,7.5S684.832,36.25,680,36.25Zm4.374-10h-8.749a0.625,0.625,0,1,0,0,1.25h8.749A0.625,0.625,0,1,0,684.374,26.25Z" transform="translate(-670 -20)"/>
                        </svg>

                        <span><?php echo __("Chat"); ?></span>
                    </a>
                    <div class="z0g-chat-popup" style="display: none;"></div>
                </li>
				<?php } if($sub_num > 1){ ?>
                <li class="has-child">
                    <a href="#">				
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                        <defs>
                        <style>
                            .cls-1 {
                                fill: #666;
                                fill-rule: evenodd;
                            }
                        </style>
                        </defs>
                        <path id="SETTINGS" class="cls-1" d="M769.337,27.642l-1.549.9a7.482,7.482,0,0,1,0,2.912l1.549,0.9a1.339,1.339,0,0,1,.483,1.821L768.5,36.487a1.316,1.316,0,0,1-1.806.488l-1.565-.911a7.905,7.905,0,0,1-2.484,1.47v1.132A1.328,1.328,0,0,1,761.32,40h-2.644a1.328,1.328,0,0,1-1.323-1.333V37.534a7.894,7.894,0,0,1-2.484-1.47l-1.565.911a1.316,1.316,0,0,1-1.806-.488l-1.323-2.309a1.339,1.339,0,0,1,.485-1.821l1.548-.9a7.431,7.431,0,0,1,0-2.912l-1.548-.9a1.339,1.339,0,0,1-.485-1.821l1.323-2.309a1.316,1.316,0,0,1,1.806-.488l1.565,0.911a7.879,7.879,0,0,1,2.484-1.47V21.333A1.328,1.328,0,0,1,758.676,20h2.644a1.328,1.328,0,0,1,1.323,1.333v1.132a7.89,7.89,0,0,1,2.484,1.47l1.565-.911a1.317,1.317,0,0,1,1.806.488l1.322,2.309A1.339,1.339,0,0,1,769.337,27.642Zm-0.992-1.732-0.662-1.155a0.658,0.658,0,0,0-.9-0.244l-1.837,1.07a6.58,6.58,0,0,0-3.623-2.114V22a0.664,0.664,0,0,0-.661-0.666h-1.322a0.663,0.663,0,0,0-.661.666v1.468a6.586,6.586,0,0,0-3.624,2.114l-1.836-1.07a0.66,0.66,0,0,0-.9.244l-0.661,1.155a0.67,0.67,0,0,0,.243.911l1.844,1.074a6.47,6.47,0,0,0,0,4.21l-1.844,1.074a0.669,0.669,0,0,0-.243.91l0.661,1.155a0.659,0.659,0,0,0,.9.244l1.836-1.069a6.59,6.59,0,0,0,3.624,2.114V38a0.663,0.663,0,0,0,.661.667h1.322A0.664,0.664,0,0,0,761.32,38V36.532a6.581,6.581,0,0,0,3.623-2.114l1.837,1.069a0.658,0.658,0,0,0,.9-0.244l0.662-1.155a0.669,0.669,0,0,0-.243-0.91L766.258,32.1a6.469,6.469,0,0,0,0-4.21L768.1,26.82A0.67,0.67,0,0,0,768.345,25.91ZM760,33.333A3.334,3.334,0,1,1,763.3,30,3.32,3.32,0,0,1,760,33.333ZM760,28a2,2,0,1,0,1.983,2A1.992,1.992,0,0,0,760,28Z" transform="translate(-750 -20)"/>
                        </svg>
                        <span><?php echo __("Admin"); ?></span>
                    </a>
                    <ul class="sub-menu">
				<?php } if(!$checkSeeResource){ ?>
                        <li class="<?php echo ($_this_controller == 'employees' && $_this_action == 'index') ? "wd-current" : "" ?>">
                            <a href="<?php echo '/employees/'?>">							
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                <defs>
                                <style>
                                    .cls-1 {
                                        fill: #666;
                                        fill-rule: evenodd;
                                    }
                                </style>
                                </defs>
                                <path id="RESSOURCES" class="cls-1" d="M2788.94,116.871h-2.07a2.074,2.074,0,0,0-.64-1.249h2.52v-1.11a1.79,1.79,0,0,0-.29-0.162,0.627,0.627,0,0,1-.11-0.055l-4-2.177a1.242,1.242,0,0,1-.17-2.078,5.159,5.159,0,0,0,1.44-3.694V103.71c0-.919-1.66-2.461-3.46-2.461a3.889,3.889,0,0,0-2.4.817,6.665,6.665,0,0,0-1.57-.189,5.5,5.5,0,0,1,3.97-1.877c2.33,0,4.71,1.964,4.71,3.71v2.636a6.339,6.339,0,0,1-1.92,4.675l3.99,2.178a1.7,1.7,0,0,1,1.06,1.054v1.563A1.054,1.054,0,0,1,2788.94,116.871Zm-6.45-10.052v2.636c0,1.267-.39,3.526-1.86,4.675l3.94,2.178a1.7,1.7,0,0,1,1.06,1.055v1.582a1.061,1.061,0,0,1-1.06,1.055h-13.52a1.053,1.053,0,0,1-1.05-1.055v-1.582a1.793,1.793,0,0,1,1.05-1.055l4.04-2.188a6.518,6.518,0,0,1-1.96-4.665v-2.636c0-1.746,2.38-3.691,4.71-3.691S2782.49,105.073,2782.49,106.819Zm-1.25,0c0-.919-1.59-2.442-3.4-2.442-1.77,0-3.46,1.545-3.46,2.442v2.636a5.307,5.307,0,0,0,1.49,3.691,1.243,1.243,0,0,1-.18,2.072l-4.04,2.187c-0.03.016-.06,0.03-0.09,0.044a1.886,1.886,0,0,0-.31.186v1.116h13.13v-1.13a2.343,2.343,0,0,0-.29-0.162c-0.04-.015-0.08-0.036-0.12-0.055l-3.93-2.177a1.249,1.249,0,0,1-.18-2.078c1.08-.848,1.38-2.716,1.38-3.694v-2.636Z" transform="translate(-2770 -100)"/>
                                </svg>
                                <span><?php echo __("Resources"); ?></span>
                            </a>

                        </li>
						<?php } ?>
						<?php if (!$checkSeePersonalizedViews && $role !='conslt') { ?>
                        <li class="<?php echo ($_this_controller == 'user_views' && $_this_action == 'index') ? "wd-current" : "" ?>">
                            <a href="<?php echo $html->url("/user_views/"); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                <defs>
                                <style>
                                    .cls-1 {
                                        fill: #666;
                                        fill-rule: evenodd;
                                    }
                                </style>
                                </defs>
                                <path id="VUES" class="cls-1" d="M2788.75,180h-12.5a1.25,1.25,0,0,0-1.25,1.249v12.5a1.25,1.25,0,0,0,1.25,1.249h12.5a1.25,1.25,0,0,0,1.25-1.249v-12.5A1.25,1.25,0,0,0,2788.75,180Zm0,13.125a0.626,0.626,0,0,1-.63.625h-11.25a0.624,0.624,0,0,1-.62-0.625v-11.25a0.624,0.624,0,0,1,.62-0.626h11.25a0.627,0.627,0,0,1,.63.626v11.25Zm-5,5a0.627,0.627,0,0,1-.63.626h-11.25a0.624,0.624,0,0,1-.62-0.626V186.876a0.624,0.624,0,0,1,.62-0.626h1.88V185h-2.5a1.25,1.25,0,0,0-1.25,1.25v12.5a1.25,1.25,0,0,0,1.25,1.25h12.5a1.25,1.25,0,0,0,1.25-1.25v-2.5h-1.25v1.874Z" transform="translate(-2770 -180)"/>
                                </svg>
                                <span><?php echo __("View"); ?></span>
                            </a>

                        </li>
						<?php } ?>
						<?php if($is_sas || (!$is_sas && $role =='admin')){ ?>
                        <li>
                            <a href ="<?php echo '/administrators/'?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                <defs>
                                <style>
                                    .cls-1 {
                                        fill: #666;
                                        fill-rule: evenodd;
                                    }
                                </style>
                                </defs>
                                <path id="SETTINGS2" class="cls-1" d="M2787.33,278.574v0.747a0.665,0.665,0,1,1-1.33,0v-0.747a3.4,3.4,0,0,1,0-6.649V261.679a0.665,0.665,0,1,1,1.33,0v10.246A3.4,3.4,0,0,1,2787.33,278.574Zm-0.66-5.36a2.036,2.036,0,1,0,2,2.036A2.015,2.015,0,0,0,2786.67,273.214Zm-6-4.14v10.247a0.67,0.67,0,1,1-1.34,0V269.074a3.407,3.407,0,0,1,0-6.648v-0.747a0.67,0.67,0,1,1,1.34,0v0.747A3.407,3.407,0,0,1,2780.67,269.074Zm-0.67-5.36a2.036,2.036,0,1,0,2,2.036A2.021,2.021,0,0,0,2780,263.714Zm-6,12.146v3.461a0.675,0.675,0,0,1-.67.679,0.666,0.666,0,0,1-.66-0.679V275.86a3.4,3.4,0,0,1,0-6.649v-7.532a0.666,0.666,0,0,1,.66-0.679,0.675,0.675,0,0,1,.67.679v7.532A3.4,3.4,0,0,1,2774,275.86Zm-0.67-5.36a2.036,2.036,0,1,0,2,2.036A2.021,2.021,0,0,0,2773.33,270.5Z" transform="translate(-2770 -260.5)"/>
                                </svg>
                                <span><?php echo __("Settings"); ?></span>
                            </a>
                        </li>
						<?php } 
				if($sub_num > 1){ ?> 
                    </ul>
                </li>
				<?php } ?> 
				<!-- *** Quan update them 3 icon Audit, Business, Report 06/05/2019 *** -->
				<?php if($role !='conslt'){ ?>
					<?php if($role !='pm' && $enableAudit == true  || $is_sas){ ?>
						<li>
							<a href="<?php echo '/audit_missions/'?>">
								<div style="height:20px;width:20px;display:block;margin:auto;position:relative;"></div>
								<span><?php echo __("Audit"); ?></span>
							</a>
						</li>
					<?php }?>
					<?php if($enableBusines == true  || $is_sas){ ?>
					<li>
						<a href="<?php echo '/sale_leads/'?>">
							<div style="height:20px;width:20px;display:block;margin:auto;position:relative;"></div>
							<span><?php echo __("Business"); ?></span>
						</a>
					</li>
					<?php }?>
					<?php if($enableReport == true || $is_sas){ ?>
					<li class="<?php echo ($_this_controller == 'eports' && $_this_action == 'sql_report') ? "wd-current" : "" ?>">
						<?php 
							$report_url = $html->url('/reports/');
							$report_tab = '';
							if(!$is_sas) {
								$list_report = ClassRegistry::init('SqlManagerEmployee')->find('list', array(
									'recursive' => -1,
									'conditions' => array(
										'company_id' =>$employee_info['Company']['id'],
										'employee_id' => $employee_info['Employee']['id'], 
									),
									'fields' => array('id', 'sql_manager_id'),
								));
								$count_report = count($list_report);
								if($count_report == 1){
									$sql_manager_id = array_values($list_report);
									$report_url = $html->url(array('controller' => 'reports', 'action' => 'viewReport', $sql_manager_id[0]));
									$report_tab = 'target="_blank"';
								}
							}
						?>
						<a class="rotate-svg-180" href="<?php echo $report_url; ?>" <?php echo $report_tab; ?>>
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                <defs>
                                <style>
                                    .cls-1 {
                                        fill: #666;
                                        fill-rule: evenodd;
                                    }
                                </style>
                                </defs>
                                <path id="SETTINGS2" class="cls-1" d="M2787.33,278.574v0.747a0.665,0.665,0,1,1-1.33,0v-0.747a3.4,3.4,0,0,1,0-6.649V261.679a0.665,0.665,0,1,1,1.33,0v10.246A3.4,3.4,0,0,1,2787.33,278.574Zm-0.66-5.36a2.036,2.036,0,1,0,2,2.036A2.015,2.015,0,0,0,2786.67,273.214Zm-6-4.14v10.247a0.67,0.67,0,1,1-1.34,0V269.074a3.407,3.407,0,0,1,0-6.648v-0.747a0.67,0.67,0,1,1,1.34,0v0.747A3.407,3.407,0,0,1,2780.67,269.074Zm-0.67-5.36a2.036,2.036,0,1,0,2,2.036A2.021,2.021,0,0,0,2780,263.714Zm-6,12.146v3.461a0.675,0.675,0,0,1-.67.679,0.666,0.666,0,0,1-.66-0.679V275.86a3.4,3.4,0,0,1,0-6.649v-7.532a0.666,0.666,0,0,1,.66-0.679,0.675,0.675,0,0,1,.67.679v7.532A3.4,3.4,0,0,1,2774,275.86Zm-0.67-5.36a2.036,2.036,0,1,0,2,2.036A2.021,2.021,0,0,0,2773.33,270.5Z" transform="translate(-2770 -260.5)"/>
							</svg>
							<span><?php echo __("Report"); ?></span>
						</a>
					</li>
					<?php }?>
				<?php }?>
            </ul>
        </div>
        <div class="header-action header-action-right">
            <ul>
                <li class="user-info">
                    <a href="<?php echo $this->Html->url('/employees_preview/my_profile') ?>" >
                        <div class="employee-name">
                            <p class="name"><?php echo $first_name ?><span><?php echo $last_name ?></span></p>
                            <p><?php echo $companyName; ?></p>
                        </div>
                        <div class="img-inner"><img src="<?php echo $urlAvatar ?>" alt="<?php __('Login');?>"></div>
                    </a>
                </li>
				<?php
				if( !$is_sas){
					if( $enableTicket ): ?>
					<li class="<?php echo ($_this_controller == 'tickets' && $_this_action == 'index') ? "wd-current" : "" ?>">
						<a href="<?php echo $html->url("/tickets/"); ?>">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
							<defs>
							<style>
								.cls-1 {
									fill: #666;
									fill-rule: evenodd;
								}
							</style>
							</defs>
							<path id="ticket" class="cls-1" d="M5775.58,39.75l-9.88,9.882a1.259,1.259,0,0,1-1.78,0l-7.55-7.553a1.253,1.253,0,0,1,0-1.778l9.88-9.882a2.027,2.027,0,0,1,1.48-.419h6.75a1.521,1.521,0,0,1,1.52,1.522V38.13A2.165,2.165,0,0,1,5775.58,39.75Zm-0.83-1.614V31.522a0.27,0.27,0,0,0-.27-0.272h-6.75a1.9,1.9,0,0,0-.61.062l-9.87,9.873,7.56,7.563,9.89-9.882A2.948,2.948,0,0,0,5774.75,38.135ZM5771,37.5a2.5,2.5,0,1,1,2.5-2.5A2.5,2.5,0,0,1,5771,37.5Zm0-3.75a1.25,1.25,0,1,0,1.25,1.25A1.25,1.25,0,0,0,5771,33.75Z" transform="translate(-5756 -30)"/>
							</svg>
							<span><?php echo __("Tickets"); ?></span>
						</a>
					</li>
					<?php endif; ?>
					<?php if(!$is_sas && $enableRMS == true) { ?>
					<li class="<?php echo ($_this_controller == 'activity_forecasts' && $_this_action != 'manages' && $_this_action != 'my_diary') ? "wd-current" : "" ?>">
						<a href="<?php echo $html->url("/activity_forecasts/request?id=". $employee_id ."&profit=" .  $profitMyDiary) ?>">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
							<defs>
							<style>
								.cls-1 {
									fill: #666;
									fill-rule: evenodd;
								}
							</style>
							</defs>
							<path id="ACTIVITY" class="cls-1" d="M1634.87,33.252a0.521,0.521,0,0,1-.43-0.329c-0.47-1.193-.87-2.262-1.27-3.329-0.41-1.124-.81-2.2-1.29-3.4a0.536,0.536,0,0,1,.06-0.491c1.71-2.381,4.4-2.672,6.99-2.953,2.48-.268,4.82-0.521,6.25-2.519a0.532,0.532,0,0,1,.43-0.22,0.543,0.543,0,0,1,.49.332l2.86,7.247a0.536,0.536,0,0,1-.06.491c-1.72,2.4-4.49,2.549-7.17,2.7-2.55.141-4.95,0.275-6.37,2.26a0.533,0.533,0,0,1-.43.22Zm-1.9-7.172c0.43,1.1.81,2.105,1.18,3.107,0.3,0.826.61,1.651,0.95,2.534,1.73-1.718,4.19-1.855,6.57-1.988,2.45-.136,4.76-0.265,6.2-2.039l-2.43-6.151c-1.7,1.733-4.08,1.99-6.39,2.24C1636.67,24.04,1634.41,24.284,1632.97,26.08Zm4.52,13.91a0.536,0.536,0,0,1-.5-0.333l-5.96-15.152a0.518,0.518,0,0,1,.3-0.676,0.553,0.553,0,0,1,.19-0.035,0.512,0.512,0,0,1,.49.332l5.97,15.152a0.526,0.526,0,0,1-.3.676A0.59,0.59,0,0,1,1637.49,39.99Z" transform="translate(-1630 -20)"/>
							</svg>
							<span><?php echo __("Activity"); ?></span>
						</a>
					</li>
					 <?php } ?>
					<?php if(isset($companyConfigs['display_activity_forecast']) && $companyConfigs['display_activity_forecast'] && $role !='conslt') { ?>
					<li class="<?php echo ($_this_controller == 'activity_forecasts' && $_this_action == 'manages') ? "wd-current" : "" ?>">
						<a href="<?php echo $html->url("/activity_forecasts_preview/manages"); ?>" class="forecast">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
							<defs>
								<style>
								.cls-1{
									fill:#666;
									fill-rule: evenodd;
								}
								</style>
							</defs>
							<g transform="translate(-192 -32)"><path class="cls-1" d="M112.838,1344.817a.857.857,0,0,1-.293-1.175l1.705-2.842a.859.859,0,0,1,.734-.417h3.459a.859.859,0,0,1,.734.417l1.706,2.842a.857.857,0,0,1-.293,1.175.845.845,0,0,1-.44.122.857.857,0,0,1-.736-.416l-1.455-2.426h-2.489l-1.455,2.426a.856.856,0,0,1-1.176.293Zm-1.382-5.8a2.566,2.566,0,0,1-2.563-2.562V1329.7c0-.019,0-.038,0-.056s0-.037,0-.056v-2.93h-1.037a.857.857,0,0,1,0-1.714h17.716a.857.857,0,1,1,0,1.714h-1.037v2.93c0,.019,0,.038,0,.056s0,.037,0,.056v6.759a2.566,2.566,0,0,1-2.563,2.562Zm-.849-9.321v6.759a.85.85,0,0,0,.849.849h10.517a.85.85,0,0,0,.849-.849V1329.7a.866.866,0,0,1,.006-.1s0-.01,0-.015v-2.93H110.607v2.93q0,.032,0,.063C110.606,1329.664,110.606,1329.68,110.606,1329.7Zm8.187,4.887v-3.079a.857.857,0,1,1,1.714,0v3.079a.857.857,0,1,1-1.714,0Zm-2.938,0v-4.643a.857.857,0,0,1,1.715,0v4.643a.857.857,0,0,1-1.715,0Zm-2.937,0v-1.515a.857.857,0,1,1,1.714,0v1.515a.857.857,0,1,1-1.714,0Z" transform="translate(87.002 -1290.94)"/></g>
							</svg>
							<span><?php echo __("Forecasts"); ?></span>
						</a>
					</li>
					 <?php } ?>
					<?php if(!isset($companyConfigs['display_diary_menu']) || $companyConfigs['display_diary_menu'] == 1) { ?>
					<li class="<?php echo ($_this_controller == 'activity_forecasts' && $_this_action == 'my_diary') ? "wd-current" : "" ?>">
						<a href="<?php echo $html->url("/activity_forecasts_preview/my_diary"); ?>">
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
					</li>
					 <?php } ?>
					<?php if($display_absence_tab){ ?>
					<li class="<?php echo ($_this_controller == 'absence_requests' || $_this_controller == 'employee_absences' ) ? "wd-current" : "" ?>">
						<a href="<?php echo $html->url("/absence_requests/index/month") ?>">				
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
							<defs>
							<style>
								.cls-1 {
									fill: #666;
									fill-rule: evenodd;
								}
							</style>
							</defs>
							<path id="ABSENCE" class="cls-1" d="M1808.75,24.375v-1.25a1.876,1.876,0,0,0-1.88-1.875h-1.25V20h1.25a3.127,3.127,0,0,1,3.13,3.125v1.25h-1.25Zm0,5.625a8.74,8.74,0,0,1-4.56,7.682L1806.25,40h-1.88l-1.67-1.678a8.73,8.73,0,0,1-5.4,0L1795.62,40h-1.87l2.06-2.318A8.75,8.75,0,1,1,1808.75,30ZM1800,22.5a7.5,7.5,0,1,0,7.5,7.5A7.5,7.5,0,0,0,1800,22.5Zm4.37,8.125H1800a0.626,0.626,0,0,1-.63-0.625V25a0.625,0.625,0,0,1,1.25,0v4.375h3.75A0.625,0.625,0,1,1,1804.37,30.625Zm-11.25-9.375a1.874,1.874,0,0,0-1.87,1.875v1.25H1790v-1.25A3.124,3.124,0,0,1,1793.12,20h1.25v1.25h-1.25Z" transform="translate(-1790 -20)"/>
							</svg>
							<span><?php echo __("Absence"); ?></span>
						</a>
					</li>
					<?php } ?>
					<?php if($display_my_assistant == 1) { ?>
					<li>
						<a id="nav-assitant" href ="javascript:void(0);">

							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
							<defs>
							<style>
								.cls-1 {
									fill: #666;
									fill-rule: evenodd;
								}
							</style>
							</defs>
							<path id="assistant" class="cls-1" d="M1871.39,37.4a1.386,1.386,0,0,1-1.4-1.366V23.979a1.386,1.386,0,0,1,1.4-1.366h1.88V21.568a0.575,0.575,0,0,1,1.15,0v1.045h8.69V21.568a0.57,0.57,0,0,1,1.14,0v1.045h1.89a1.378,1.378,0,0,1,1.39,1.366V28.2a0.575,0.575,0,0,1-1.15,0V27.756h-15.24v8.277a0.245,0.245,0,0,0,.25.241h7.86a0.563,0.563,0,1,1,0,1.126h-7.86Zm14.99-10.769V23.979a0.242,0.242,0,0,0-.24-0.241h-1.89v0.241a0.57,0.57,0,0,1-1.14,0V23.738h-8.69v0.241a0.575,0.575,0,0,1-1.15,0V23.738h-1.88a0.245,0.245,0,0,0-.25.241V26.63h15.24Zm-1.06,12.376a4.582,4.582,0,1,1,4.67-4.581A4.628,4.628,0,0,1,1885.32,39.007Zm0-8.036a3.456,3.456,0,1,0,3.52,3.455A3.5,3.5,0,0,0,1885.32,30.97Zm1.64,5.625a0.6,0.6,0,0,1-.41-0.165l-1.64-1.607a0.591,0.591,0,0,1-.12-0.182,0.555,0.555,0,0,1-.04-0.216V32.015a0.57,0.57,0,0,1,1.14,0v2.178l1.47,1.443a0.545,0.545,0,0,1,0,.8A0.555,0.555,0,0,1,1886.96,36.6Z" transform="translate(-1870 -20)"/>
							</svg>

							<span><?php echo __("Assistant"); ?></span>
						</a>
					</li>
					<?php } ?>
				<?php } ?>
                <li>
					<?php
						if($display_absence_tab && $_this_controller == 'absence_requests'){
							$linkDoc = '/guides/absence/manager_les_absences.htm';
						}elseif($_this_controller == 'activity_forecasts' && $_this_action == 'manages'){
							$linkDoc = '/guides/plandecharge/plandecharge.htm';
						}elseif($_this_controller == 'activity_forecasts' && $_this_action != 'my_diary'){
							$linkDoc = '/guides/activity/_manager_une_feuille_de_temps.htm';
						}elseif($_this_controller == 'activity_forecasts' && $_this_action == 'my_diary'){
							$linkDoc = '/guides/agenda/mon_agenda.htm';
						}elseif($_this_controller == 'activity_tasks' && $_this_action == 'visions_staffing' ){
							$linkDoc = '/guides/staffing/le_staffing_et_plan_de_charge_global.htm';
						}
						// elseif(!$is_sas && $role !='conslt' && $enableZogMsgs == true){
							// $linkDoc = '/guides/chat';
						// }
						elseif($this->params['url']['url'] == 'administrators/'){
							$linkDoc = '/admin_guides/index.htm';
						}
						else{
							$linkDoc = '/guides/prise_en_main_z0gravity/chapitre_1___prendre_en_main_la_solution.htm';
						}
					?>
                    <a target="_blank" href="<?php echo $this->Html->url($linkDoc) ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                        <defs>
                        <style>
                            .cls-1 {
                                fill: #666;
                                fill-rule: evenodd;
                            }
                        </style>
                        </defs>
                        <path id="help" class="cls-1" d="M1960,40a10,10,0,1,1,10-10A10,10,0,0,1,1960,40Zm0-18.667A8.667,8.667,0,1,0,1968.66,30,8.667,8.667,0,0,0,1960,21.333Zm2.04,8.192q-0.15.146-.39,0.374c-0.16.152-.3,0.284-0.41,0.4a2.539,2.539,0,0,0-.27.286,1.379,1.379,0,0,0-.27.909v0.66h-1.66V31.255a2.13,2.13,0,0,1,.14-0.873,3.544,3.544,0,0,1,.61-0.755l1.07-1.07a1.272,1.272,0,0,0,.34-0.909,1.255,1.255,0,0,0-.35-0.9,1.231,1.231,0,0,0-.91-0.359,1.325,1.325,0,0,0-.93.344,1.347,1.347,0,0,0-.43.917h-1.78a3.024,3.024,0,0,1,1.02-2.046,3.251,3.251,0,0,1,2.18-.741,3.1,3.1,0,0,1,2.12.711,2.488,2.488,0,0,1,.83,1.988,2.246,2.246,0,0,1-.49,1.467C1962.28,29.26,1962.13,29.427,1962.04,29.525Zm-2.15,3.71a1.14,1.14,0,0,1,.8.315,1.027,1.027,0,0,1,.34.763,1.048,1.048,0,0,1-.34.77,1.084,1.084,0,0,1-.79.323,1.136,1.136,0,0,1-.8-0.316,1.015,1.015,0,0,1-.33-0.762,1.04,1.04,0,0,1,.33-0.77A1.07,1.07,0,0,1,1959.89,33.235Z" transform="translate(-1950 -20)"/>
                        </svg>
                        <span><?php echo __("Help"); ?></span>
                    </a>
                </li>
                <li>
					<?php $lang_class = $langCode;
					$langCode = ($langCode == 'fr') ? 'en' : 'fr'; ?>
                    <a href="<?php echo $this->here . Router::queryString(array('hl' => $langCode) + array_diff_key($this->params['url'], array('url' => '', 'ext' => '')), array()); ?>" onclick="toggleLang(this)" class="lang-switch <?php echo $lang_class ?>" id="lang-switch"><p class="wd-switch-icon"></p>
                        <span>fr</span><span>en</span>
                    </a>
                </li>
                <li class="last">
                    <a href="<?php echo $this->Html->url('/logout') ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                        <defs>
                        <style>
                            .cls-1 {
                                fill: #666;
                                fill-rule: evenodd;
                            }
                        </style>
                        </defs>
                        <path id="LEAVE" class="cls-1" d="M2120.5,28.75a0.626,0.626,0,0,0,.63-0.625v-7.5a0.63,0.63,0,0,0-1.26,0v7.5A0.626,0.626,0,0,0,2120.5,28.75Zm3.17-6.955v1.332a8.231,8.231,0,1,1-6.34,0V21.795A9.5,9.5,0,1,0,2123.67,21.795Z" transform="translate(-2110.5 -20)"/>
                        </svg>
                        <span><?php echo __("Logout"); ?></span>
                    </a>
                </li>
            </ul>
        </div>

    </div>
       <?php echo $this->element("z0g_chat") ?>
</div>

<!--End z0g header -->
<?php echo $this->element("sub_menu"); ?>
<?php
	if( !$is_sas){
		// list cac controler se hien thi vision staffing+ o activity.
		$displayVisions = array(
			'activity_forecasts', 'activities', 'activity_tasks', 'activity_budget_synthesis', 'activity_budget_sales',
			'activity_budget_internals', 'activity_budget_externals', 'activity_budget_provisionals', 'team_workloads'
		);
		/**
		 * Vision staffing+: code cho phan dialog
		 */
		$menuListFamilies = ClassRegistry::init('Family')->find('list', array(
									'recursive' => -1,
									'conditions' => array('company_id' => $employee_info['Company']['id'],'parent_id'=>null)
							));
		$PCModel = ClassRegistry::init('ProfitCenter');
		$menuListProfitCenters = $PCModel->generateTreeList(array('company_id' => $employee_info['Company']['id']),null,null,' -- ',-1);
		//END
		// lay profit center cho team_workloads.
		$backupManagers = ClassRegistry::init('ProfitCenterManagerBackup')->find('list', array(
			'recursive' => -1,
			'conditions' => array('employee_id' => $employee_id),
			'fields' => array('profit_center_id', 'profit_center_id')
		));
		$team_wl_profit = ClassRegistry::init('ProfitCenter')->generateTreeList(array(
			'OR' => array(
			   'manager_id' => $employee_id,
			   'id' => $backupManagers
		   )
		), null,null,'-- ',-1);
		$pcOfTeamWorkload = ($role != 'admin') ? $team_wl_profit : $menuListProfitCenters;
		// end
		$menuListEmployees = ClassRegistry::init('CompanyEmployeeReference')->find('all', array(
			'conditions' => array(
				'CompanyEmployeeReference.company_id' => $employee_info['Company']['id'],
				'NOT' => array('Employee.is_sas' => 1)
			),
			'fields' => array('Employee.id', 'Employee.first_name', 'Employee.last_name')
		));
		$menuListEmployees = !empty($menuListEmployees) ? Set::combine($menuListEmployees, '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name')) : array();
		$menuListBudgetCustomers = ClassRegistry::init('BudgetCustomer')->find('list', array(
			'recursive' => -1,
			'conditions' => array('company_id' => $employee_info['Company']['id'])
		));
	}
?>
<?php
    $dateType = isset($arrGetUrl['aDateType']) ? $arrGetUrl['aDateType'] : 3 ;
	if( !$is_sas && ( (!isset($companyConfigs['display_vision_staffing'])) || ($companyConfigs['display_vision_staffing'] == 1) )){
	?>
		<div id="dialog_vision_staffing_news_menu" class="buttons" style="display: none;">
			<fieldset>
				<?php echo $this->Form->create('Activity', array(
					'type' => 'GET', 
					'id' => 'form_vision_staffing_news_menu', 
					'target' => '_blank', 
					'url' => array('controller' => 'activity_tasks', 'action' => 'visions_staffing'),					
				)); 
				?>
				<div style="height:auto;" class="wd-scroll-form">
					<div class="wd-left-content">
						<fieldset class="fieldset">
							<!-- <legend class="legend"><?php __('Visibility settings'); ?></legend> -->
							<div class="wd-input">
								<label for="status"><?php __('Show staffing by'); ?></label>
								<div id="group-showby">
									<?php
									if(isset($arrGetUrl['type'])) $dataType=$arrGetUrl['type'];
									else $dataType=0;
									echo $this->Form->radio('project_staffing_id', array(0 => __("Activity", true)), array(
										'name' => 'type',
										'fieldset' => false,
										'legend' => false,
										'rel' => 'no-history',
										'value' => $dataType)
									);
									?>
									<?php
									echo $this->Form->radio('project_staffing_id', array(1 => __("Profit center", true)), array(
										'name' => 'type',
										'fieldset' => false,
										'legend' => false,
										'rel' => 'no-history',
										'value' => $dataType)
									);
									?>
									<?php
									//Change to Profile, version date : PMS - 17/6/2015 - Enhancement vision staffing +
									if(isset($companyConfigs['activate_profile']) && $companyConfigs['activate_profile']) {
										echo $this->Form->radio('project_staffing_id', array(5 => __("Profile", true)), array(
											'name' => 'type',
											'fieldset' => false,
											'legend' => false,
											'rel' => 'no-history',
											'value' => $dataType)
										);
									}
									?>
								</div>
							</div>
							<div class="wd-input">
								<label for="status"><?php __('Show Summary'); ?></label>
								<div id="show_summary">
									<?php
									if(isset($arrGetUrl['summary'])) $dataType=$arrGetUrl['summary'];
									else $dataType=0;
									echo $this->Form->radio('project_summary_id', array(__("No", true), __("Yes", true)), array(
										'name' => 'summary',
										'fieldset' => false,
										'legend' => false,
										'rel' => 'no-history',
										'value' => $dataType));
									?>
									<?php
									// echo $this->Form->radio('project_summary_id', array(99 => __("Only Summary", true)), array(
									//  'name' => 'summary',
									//  'fieldset' => false,
									//  'legend' => false,
									//  'value' => $dataType));
									?>
								</div>
							</div>
							<div class="wd-input">
								<label for="status"><?php __('Not Affected') ?></label>
								<div class="is-check-file">
									<?php
									echo $this->Form->radio('show_na', array(__("No", true), __("Yes", true)), array(
										'name' => 'show_na',
										'rel' => 'no-history',
										'fieldset' => false,
										'legend' => false,
										'value' => 1)
									);
									?>
								</div>
							</div>
						</fieldset>
					</div>
					<fieldset class="fieldset" style="margin-top: 10px;">
						<div class="wd-input" style="margin-top: 10px;">
							<label for="status"><?php __('Show by'); ?></label>
							<div id="group-date-type">
								<?php
								echo $this->Form->radio('aDateType', array(3 => __("Month", true)), array(
									'name' => 'aDateType',
									'fieldset' => false,
									'legend' => false,
									'rel' => 'no-history',
									'checked' => $dateType == 3 ? 'checked' : '',
									'value' => $dateType)
								);
							?>
							</div>
						</div>
					<?php
					if($dateType == 1 || $dateType == 2) {
					?>
						<style>
							.dateByDay{ display:block; }
							.dateNotByDay{ display:none; }
						</style>
					<?php
					} else {
					?>
						<style>
							.dateByDay{ display:none; }
							.dateNotByDay{ display:block; }
						</style>
					<?php } ?>
						<div class="dateByDay wd-input" style="margin-top: 10px; padding-left:105px;">
					<?php
						echo $this->Form->input(__('From', true), array(
							'empty' => false,
							'rel' => 'no-history',
							'class' => 'activity-datepicker',
							'value' => isset($arrGetUrl['aStartDate']) ? $arrGetUrl['aStartDate'] : '',
							'id' => 'aStartDate',
							'name' => 'aStartDate',
							'style' => 'width: 101px;'
						));
						echo $this->Form->input(__('To', true), array(
							'empty' => false,
							'rel' => 'no-history',
							'class' => 'activity-datepicker',
							'value' => isset($arrGetUrl['aEndDate']) ? $arrGetUrl['aEndDate'] : '',
							'id' => 'aEndDate',
							'name' => 'aEndDate',
							'style' => 'width: 101px;'
						));
					?>
						</div>
						<div class="dateNotByDay wd-input" style="margin-top: 10px;" id="fromDate">
							<label for="status"><?php __('From'); ?></label>
							<?php
								if(isset($arrGetUrl['aStartMonth'])) $smonth=$arrGetUrl['aStartMonth'];
								else $smonth= !empty($_start) ? date('m', $_start) : date('m', time());
								if(isset($arrGetUrl['aStartYear'])) $syear=$arrGetUrl['aStartYear'];
								else $syear= !empty($_start) ? date('Y', $_start) : date('Y', time());
								$_start = !empty($_start) ? $_start : time();
								echo $this->Form->month('smonth', $smonth, array(
									'empty' => false,
									'rel' => 'no-history',
									'id' => 'aStartMonth',
									'name' => 'aStartMonth',
									'style' => 'width: 101px;'
								));
								echo $this->Form->year('syear', date('Y', $_start) - 10, date('Y', $_start) + 10, $syear, array(
									'empty' => false,
									'rel' => 'no-history',
									'id' => 'aStartYear',
									'name' => 'aStartYear',
									'style' => 'width: 77px; margin-left: 5px;'
								));
						?>
						</div>
						<div class="dateNotByDay wd-input" style="margin-top: 10px;">
							<label for="status"><?php __('To'); ?></label>
							<?php
								if(isset($arrGetUrl['aEndMonth'])) $emonth=$arrGetUrl['aEndMonth'];
								else $emonth= !empty($_start) ? date('m', $_start) : date('m', time());
								if(isset($arrGetUrl['aEndYear'])) $eyear=$arrGetUrl['aEndYear'];
								else $eyear=  !empty($_start) ? date('Y', $_start) : date('Y', time());
								$_end = !empty($_end) ? $_end : time();
								echo $this->Form->month('emonth', $emonth, array(
									'empty' => false,
									'rel' => 'no-history',
									'id' => 'aEndMonth',
									'name' => 'aEndMonth',
									'style' => 'width: 101px;'
								));
								echo $this->Form->year('eyear', date('Y', $_end) - 10, date('Y', $_end) + 10, $eyear, array(
									'empty' => false,
									'rel' => 'no-history',
									'id' => 'aEndYear',
									'name' => 'aEndYear',
									'style' => 'width: 77px; margin-left: 5px;'
								));
							?>
						</div>
					</fieldset>
					<div class="wd-input" id="filter_activated" style="overflow: visible">
						<label for=""><?php __("Activated") ?></label>
						<?php
							echo $this->Form->input('activated', array(
							'type' => 'select',
							'name' => 'aActivated',
							'id' => 'aActivated',
							'rel' => 'no-history',
							'div' => false,
							'label' => false,
							'multiple' => true,
							'hiddenField' => false,
							"empty" => false,
							'style' => 'width: 300px !important',
							"options" => array(0 => 'No',1 => 'Yes'),
							'selected'=>isset($arrGetUrl['aActivated']) ? $arrGetUrl['aActivated'] : array()
							));
						?>
					</div>
					<div class="wd-input" id="filter_activityName" style="overflow: visible">
						<label for=""><?php __("Activity Name") ?></label>
						<?php
						echo $this->Form->input('activity_name', array('div' => false, 'label' => false,
							"empty" => false,
							'name' => 'aName',
							'id' => 'aName',
							'rel' => 'no-history',
							'multiple' => true,
							'hiddenField' => false,
							'style' => 'width: 300px !important',
							"options" => isset($activityFilterList) ? $activityFilterList : array(),
							'selected'=> isset($arrGetUrl['aName']) ? $arrGetUrl['aName'] : array()
							));
						?>
					</div>
					<div class="wd-input" id="filter_priority" style="overflow: visible">
						<label for=""><?php __("Priority") ?></label>
						<?php
						$priorities = ClassRegistry::init('ProjectPriority')->find('list', array(
							'recursive' => -1,
							'conditions' => array(
								'company_id' => $employee_info['Company']['id']
							),
							'fields' => array('id', 'priority')
						));
						$selected = isset($this->params['url']['priority']) ? explode(',', $this->params['url']['priority']) : array();
							echo $this->Form->input('priority', array(
							'type' => 'select',
							'name' => 'priority',
							'id' => 'StaffingPriority',
							'div' => false,
							'label' => false,
							'multiple' => true,
							'hiddenField' => false,
							'rel' => 'no-history',
							"empty" => false,
							'style' => 'width: 300px !important',
							"options" => $priorities,
							'selected' => $selected
							));
							echo $this->Form->hidden('asd', array('name' => 'priority', 'id' => 'hiddenPriority'));
						?>
					</div>
					<div class="wd-input" id="filter_family" style="overflow: visible">
						<label for=""><?php __("Family") ?></label>
						<?php
						echo $this->Form->input('family', array(
							'type' => 'select',
							'name' => 'aFamily',
							'id' => 'aFamily',
							'rel' => 'no-history',
							'div' => false,
							'label' => false,
							'multiple' => true,
							'hiddenField' => false,
							"empty" => false,
							'style' => 'width: 300px !important',
							"options" => $menuListFamilies,
							'selected' => isset($arrGetUrl['aFamily']) ? $arrGetUrl['aFamily'] : array()
							));
						?>
					</div>
					<div class="wd-input" id="filter_subFamily" style="overflow: visible">
						<label for=""><?php __("Sub Family") ?></label>
						<?php
						echo $this->Form->input('sous_family', array(
							'type' => 'select',
							'name' => 'aSub',
							'id' => 'aSub',
							'div' => false,
							'multiple' => true,
							'hiddenField' => false,
							'label' => false,
							'rel' => 'no-history',
							'style' => 'width: 300px !important',
							"empty" => false,
							"options" => isset($subFamilyList) ? $subFamilyList : array(),
							'selected' => isset($arrGetUrl['aSub']) ? $arrGetUrl['aSub'] : array()
							));
						?>
					</div>
					<div class="wd-input" id="filter_profitCenter" style="overflow: visible">
						<label for=""><?php __("Profit Center") ?></label>
						<?php
						echo $this->Form->input('profit_center', array(
							'type' => 'select',
							'name' => 'aPC',
							'id' => 'aPC',
							'div' => false,
							'multiple' => true,
							'hiddenField' => false,
							'label' => false,
							'rel' => 'no-history',
							'style' => 'width: 300px !important',
							"empty" => false,
							"options" => !empty($menuListProfitCenters) ? $menuListProfitCenters : array(),
							'selected' => isset($arrGetUrl['aPC']) ? $arrGetUrl['aPC'] : array()
							));
						echo $this->Form->hidden('selectPCAll', array('value' => 'true'));
						?>
					</div>
					<div class="wd-input" id="filter_employee" style="overflow: visible">
						<label for=""><?php __("Employee") ?></label>
						<?php
						if(isset($employeeRorProfitCenterList)) $menuListEmployees=$employeeRorProfitCenterList;
						echo $this->Form->input('employee', array(
							'type' => 'select',
							'name' => 'aEmployee',
							'id' => 'aEmployee',
							'div' => false,
							'multiple' => true,
							'hiddenField' => false,
							'label' => false,
							'rel' => 'no-history',
							'style' => 'width: 300px !important',
							"empty" => false,
							"options" => !empty($menuListEmployees) ? $menuListEmployees : array(),
							'selected' => isset($arrGetUrl['aEmployee']) ? $arrGetUrl['aEmployee'] : array()
							));
						?>
					</div>
					<div class="wd-input" id="filter_budgetCustomer" style="overflow: visible">
						<label for=""><?php __("Customer") ?></label>
						<?php
						echo $this->Form->input('budget_customer', array(
							'type' => 'select',
							'name' => 'aCustomer',
							'id' => 'aCustomer',
							'div' => false,
							'multiple' => true,
							'hiddenField' => false,
							'label' => false,
							'rel' => 'no-history',
							'style' => 'width: 300px !important',
							"empty" => false,
							"options" => !empty($menuListBudgetCustomers) ? $menuListBudgetCustomers : array(),
							'selected' => isset($arrGetUrl['aCustomer']) ? $arrGetUrl['aCustomer'] : array()
							));
						?>
					</div>
				</div>
				<?php echo $this->Form->end(); ?>
			</fieldset>
			<div style="clear: both;"></div>
			<ul class="type_buttons" style="padding-right: 10px !important">
				<li><a href="javascript:void(0)" class="cancel"></a></li>
				<li><a href="javascript:void(0)" class="new" id="ok_sum_staffing"></a></li>
				<li><a href="javascript:void(0)" class="cancel reset" id="reset_sum"></a></li>
				<li><a href="javascript:void(0)" class="new" id="ok_export_file" style="display: none;"></a></li>
			</ul>
		</div>
	<?php } ?> 
<div id="loading_w" class="loading_w"><div class="loading_w_cont"><p></p><span><?php echo __('Staffing in progress', true); ?></span><div id="progressBar1" style="height: 20px;" class = 'progressBar1'></div></div></div>
<?php 
	if(!empty($companyConfigs['display_activity_vision_staffing_new'])) {
		// debug( $employeeRorProfitCenterList);
		echo $this->element('staffing_popup', array(
			'menuListProfitCenters' => $menuListProfitCenters,
			// 'employeeRorProfitCenterList' => $employeeRorProfitCenterList
		));
	}
?>
<script type="text/javascript">
    var activeMenu = 1;
    var companyConfigs = <?php echo json_encode($companyConfigs) ?>;
    var nanobar1 = new Nanobar1();
    var auto;
    var progress = 0;
    var done = false;
	var _controller = <?php echo json_encode($_this_controller) ?>,
		_isActivityScreen = 1;

    //PROGRESS
    function setValueProgress1(progress) {
        if (progress > 100)
            progress = 100;
        nanobar1.go(progress);
        $('#progressBar1 div div').html(progress + '% &nbsp;');
    }
    function autoprogressBar1() {
        $('#btnCheck').hide();
        var i = 1;
        auto = setInterval(function () {
            if (i % 2 == 0)
                $('#progressBar1 div').css('backgroundPosition', '25% 50%');
            else
                $('#progressBar1 div').css('backgroundPosition', '50% 50%');
            i++;
            progress = parseFloat(progress) + 0.01;
            progress = parseFloat(progress).toFixed(2);
            setValueProgress1(progress);
        }, 200);
    }
    function setStatusSystem1() {
        $('#progressBar1').show();
        progress = 0;
        setValueProgress1(progress);
        done = false;
        autoprogressBar1();
    }
    function checkStaffing(_pjId) {
        if (companyConfigs['run_staffing_before_display_staffing'] == 1) {
            $.ajax({
                url: '<?php echo $html->url(array('controller' => 'projects', 'action' => 'checkStaffing')); ?>',
                async: true,
                type: 'POST',
                data: {
                    id: _pjId
                },
                beforeSend: function () {
                    $('#progressBar1').hide();
                    $('#loading_w').show();
                },
                complete: function () {
                    $('#loading_w').hide();
                    window.location.href = '<?php echo $html->url(array('controller' => 'project_staffings', 'action' => 'visions')); ?>' + '/' + _pjId;
                }
            });
        } else {
            window.location.href = '<?php echo $html->url(array('controller' => 'project_staffings', 'action' => 'visions')); ?>' + '/' + _pjId;
        }
    }
    $(function () {
        if (activeMenu == 4) {
            $('#nav-guide').prop('href', '/admin_guides/');
        }
        var _hasSub = false;
        $('#sub-nav ul').append('<li class="end-child"></li>');
        $('#sub-nav li').hover(function () {
            _hasSub = $(this).find('ul').length;
            if (_hasSub) {
                $(this).addClass('hover');
            }
        }, function () {
            $(this).removeClass('hover');
            _hasSub = false;
        });
        //apply vs filter
        <?php if( !$is_sas && ( (!isset($companyConfigs['display_vision_staffing'])) || ($companyConfigs['display_vision_staffing'] == 1) )){ ?>
	        $(window).ready(function(){
				var filter_default = {
					spmonth: "",
					spyear: "",
					epmonth: "",
					epyear: "",
					date_type: "",
					end_month: "",
					end_year: "",
					show_na: "",
					start_month: "",
					start_year: "",
					summary: "",
					view_by: ""
				};
				var vs_filter = JSON.parse(<?php echo json_encode(!empty($vs_filter) ? $vs_filter : "[]");?>);
				if((typeof vs_filter == "undefined")||(vs_filter == null) ||((!vs_filter.length)&&(vs_filter.length == 0))){
					// console.log(3);
					var vsFilter = new $.z0.data(filter_default);
				}else{
					// console.log(4);
					var vsFilter = new $.z0.data(vs_filter);
				}
				// console.log(vs_filter);
				// console.log(vsFilter);
				//view by (0, 1, 5)
				var viewBy = vsFilter.get('view_by', 0);
				$(':input[name="type"][value="' + viewBy + '"]').prop('checked', true);
				//show summary (0, 1)
				var summary = vsFilter.get('summary', 0);
				$(':input[name="summary"][value="' + summary + '"]').prop('checked', true);
				var showNA = vsFilter.get('show_na', 1);
				$(':input[name="show_na"][value="' + showNA + '"]').prop('checked', true);
				//date type (1, 2, 3)
				var dateType = parseInt(vsFilter.get('date_type', 3));
				$(':input[name="aDateType"][value="' + dateType + '"]').prop('checked', true);
				switch (dateType) {
					case 1:
					case 2:
						//date & week
						var start = vsFilter.get('start_date', ''),
								end = vsFilter.get('end_date', '');
						$('#aStartDate').val(start);
						$('#aEndDate').val(end);
						break;
					case 3:
						//month
						var today = new Date(),
							month = today.getMonth() + 1,
							sm = vsFilter.get('start_month', month < 10 ? '0' + month : month),
							sy = vsFilter.get('start_year', today.getFullYear()),
							em = vsFilter.get('end_month', month < 10 ? '0' + month : month),
							ey = vsFilter.get('end_year', today.getFullYear());
						$('#aStartMonth').val(sm);
						$('#aStartYear').val(sy);
						$('#aEndMonth').val(em);
						$('#aEndYear').val(ey);
						break;
				}
				//save for team workload
				var today = new Date(),
					month = today.getMonth() + 1,
					sm = vsFilter.get('smonth', month < 10 ? '0' + month : month),
					sy = vsFilter.get('syear', today.getFullYear()),
					em = vsFilter.get('emonth', month < 10 ? '0' + month : month),
					ey = vsFilter.get('eyear', today.getFullYear());
				$('#teamStartMonth').val(sm);
				$('#teamStartYear').val(sy);
				$('#teamEndMonth').val(em);
				$('#teamEndYear').val(ey);
				var t = vsFilter.get('team', 0);
				$('#aTeam').val(t);
				var priority = vsFilter.get('teampriority', []);
				$('#teamPriority').multipleSelect('setSelects', priority);
				//end
				// save for team workload plus
				var today = new Date(),
					month = today.getMonth() + 1,
					sm = vsFilter.get('spmonth', month < 10 ? '0' + month : month),
					sy = vsFilter.get('spyear', today.getFullYear()),
					em = vsFilter.get('epmonth', month < 10 ? '0' + month : month),
					ey = vsFilter.get('epyear', today.getFullYear());
				$('#teamPSm').val(sm);
				$('#teamPSy').val(sy);
				$('#teamPEm').val(em);
				$('#teamPEy').val(ey);
				var t = vsFilter.get('teamP', []);
				$('#teamP').multipleSelect(
					'setSelects', {
						values: t,
						click: true,
						complete: function () {
						}
					}
				);
				var priority = vsFilter.get('teamPrio', []);
				$('#teamPrio').multipleSelect('setSelects', priority);
				// end team workload plus
				//activated?
				//filter_activated
				var activated = vsFilter.get('activated', []);

				$('#aActivated').multipleSelect(
					'setSelects', {
						values: activated,
						click: true,
						complete: function () {
							var activity = vsFilter.get('activity', []);
							$('#aName').multipleSelect('setSelects', activity);
						}
					}
				);
				var priority = vsFilter.get('priority', []);
				$('#StaffingPriority').multipleSelect('setSelects', priority);
				// //filter_family
				var family = vsFilter.get('family', []);
				$('#aFamily').multipleSelect(
					'setSelects', {
						values: family,
						click: true,
						//call after ajax loading sub-family complete and sub-family select is filled
						complete: function () {
							var sub_family = vsFilter.get('sub_family', []);
							$('#aSub').multipleSelect('setSelects', sub_family);
						}
					}
				);
				var pc = vsFilter.get('pc', []);
				$('#aPC').multipleSelect(
					'setSelects', {
						values: pc,
						click: true,
						complete: function () {
							var resource = vsFilter.get('resource', []);
							$('#aEmployee').multipleSelect('setSelects', resource);
						}
					}
				);
				//filter_budgetCustomer
				var customer = vsFilter.get('customer', []);
				$('#aCustomer').multipleSelect('setSelects', customer);

				var checkShowby = $('#group-showby').find('input:checked');
				if( checkShowby.length){
					if (checkShowby[0].id == 'ActivityProjectStaffingId0') {
						clickPId0();
					} else if (checkShowby[0].id == 'ActivityProjectStaffingId1') {
						clickPId1();
					} else {
						clickPId5();
					}
				}
			});
		<?php } ?> 


        //store vs filter (data changed)
        function clickPId0() {
            $('#aActivated, #aName, #aFamily, #aSub, #aEmployee').removeAttr('disabled');
            $('#aActivated, #aName, #aFamily, #aSub, #aEmployee').parent().removeClass('display-none');
            $('#aPC').removeAttr('disabled');
            $('#aPC').parent().removeClass('display-none');
            $('#aDateType1').show();
            $('#aDateType2').show();
            $('label[for=aDateType1]').show();
            $('label[for=aDateType2]').show();
            //vsFilter.set('view_by', 0);
        }
        function clickPId1() {
            $('#aName, #aEmployee').attr('disabled', 'disabled');
            $('#aName, #aEmployee').parent().addClass('display-none');
            $('#aEmployee').attr('disabled', 'disabled');
            $('#aEmployee').parent().addClass('display-none');
            $('#aActivated').removeAttr('disabled');
            $('#aActivated').parent().removeClass('display-none');
            $('#aPC').removeAttr('disabled');
            $('#aPC').parent().removeClass('display-none');
            $('#aDateType1').show();
            $('#aDateType2').show();
            $('label[for=aDateType1]').show();
            $('label[for=aDateType2]').show();
            //vsFilter.set('view_by', 1);
        }
        function clickPId5() {
            $('#aActivated, #aName, #aFamily, #aSub, #aEmployee').removeAttr('disabled');
            $('#aActivated, #aName, #aFamily, #aSub, #aEmployee').parent().removeClass('display-none');
            $('#aFamily, #aSub').removeAttr('disabled');
            $('#aFamily, #aSub').parent().removeClass('display-none');
            $('#aEmployee, #aPC').attr('disabled', 'disabled');
            $('#aEmployee, #aPC').parent().addClass('display-none');
            $('#aDateType1').hide();
            $('#aDateType2').hide();
            $('label[for=aDateType1]').hide();
            $('label[for=aDateType2]').hide();
            //vsFilter.set('view_by', 5);
        }
        function validateDate(oldValue, elm) {
            var startMonth = $('#aStartMonth').val();
            var startYear = $('#aStartYear').val();
            var endMonth = $('#aEndMonth').val();
            var endYear = $('#aEndYear').val();
            var $id = elm.id;
            var error = 0;
            //var oldValue = elm.options[elm.selectedIndex].value;
            if (endYear - startYear > 5) {
                error = 1;
            } else if (startYear > endYear) {
                error = 1;
            } else if (startMonth == endMonth && startYear > endYear) {
                error = 1;
            } else if (startMonth > endMonth && startYear >= endYear) {
                error = 1;
            }
            if (error == 1) {
                $('#' + $id).val(oldValue);
            }
        }
        var previous;
        $("#aStartMonth").on('focus', function () {
            previous = this.value;
        }).change(function () {
            validateDate(previous, this);
        });
        $("#aStartYear").on('focus', function () {
            previous = this.value;
        }).change(function () {
            validateDate(previous, this);
        });
        $("#aEndMonth").on('focus', function () {
            previous = this.value;
        }).change(function () {
            validateDate(previous, this);
        });
        $("#aEndYear").on('focus', function () {
            previous = this.value;
        }).change(function () {
            validateDate(previous, this);
        });
        $('input[name="aDateType"]').click(function () {
            if ($(this).val() == 1 || $(this).val() == 2) {
                $('.dateByDay').show();
                $('.dateNotByDay').hide();
                var currentDate = new Date();
                currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate() - currentDate.getDay() + 1); // First day is the day of the month - the day of the week
                var last = currentDate + 5; // last day is the first day + 6
                if ($('#aStartDate').val() == '')
                    $('#aStartDate').datepicker('setDate', currentDate);
                if ($('#aEndDate').val() == '')
                    $('#aEndDate').datepicker('setDate', currentDate);
                if ($(this).val() == 2) {
                    if ($('#aStartDate').val() != '') {
                        var currentDateWeek = $('#aStartDate').datepicker('getDate');
                        currentDateWeek = currentDateWeek.getDate() - currentDateWeek.getDay(); // First day is the day of the month - the day of the week
                        $('#aStartDate').datepicker('setDate', currentDate);
                    }
                    if ($('#aEndDate').val() != '') {
                        var currentDateWeek = $('#aStartDate').datepicker('getDate');
                        currentDateWeek = currentDateWeek.getDate() - currentDateWeek.getDay(); // First day is the day of the month - the day of the week
                        var last = currentDateWeek + 4;
                        $('#aEndDate').datepicker('setDate', last);
                    }
                    $('.activity-datepicker').datepicker('option', 'beforeShowDay', function (date) {
                        var result;
                        if (this.id == 'aStartDate')
                            result = date.getDay() == 1;
                        else
                            result = date.getDay() == 0;
                        return [result, ''];
                    });
                } else {
                    $('.activity-datepicker').datepicker('option', 'beforeShowDay', '');
                }
            } else {
                $('.dateByDay').hide();
                $('.dateNotByDay').show();
            }
        });
        $('.activity-datepicker').datepicker({
            dateFormat: 'dd-mm-yy',
            showOn: 'focus',
            onSelect: function () {
                var date1 = $('#aStartDate').datepicker('getDate'),
                        date2 = $('#aEndDate').datepicker('getDate');
                validField = true;
                if (date2 < date1) {
                    validField = false;
                    $('#aEndDate').datepicker('setDate', date1);
                }
            }
        }).prop({readonly: true});
        <?php if(isset($dateType) && $dateType == 2)
        { ?>
        $('.activity-datepicker').datepicker('option', 'beforeShowDay', function (date) {
            var result;
            if (this.id == 'aStartDate')
                result = date.getDay() == 1;
            else
                result = date.getDay() == 0;
            return [result, ''];
        });
        <?php } ?>

        $('#ActivityProjectStaffingId0').click(function () {
            //$('#filter_activated div label').find('input').eq(1).prop('checked' , true);
            clickPId0();
        });
        $('#ActivityProjectStaffingId1').click(function () {
            clickPId1();
            //RESET ACTIVITY
            $('#aName').find('span').text('<?php __("-- Any --"); ?>');
            $('#filter_activityName div label').each(function () {
                if ($(this).find('input').is(':checked')) {
                    $(this).removeAttr('class');
                    $(this).find('input').removeAttr('checked');
                }
            });
            //RESET EMPLOYEE
            $('#aEmployee').find('span').text('<?php __("-- Any --"); ?>');
            $('#filter_employee div label').each(function () {
                if ($(this).find('input').is(':checked')) {
                    $(this).removeAttr('class');
                    $(this).find('input').removeAttr('checked');
                }
            });
        });
        $('#ActivityProjectStaffingId5').click(function () {
            clickPId5();
            //RESET FAMILY
            $('#aName').find('span').text('<?php __("-- Any --"); ?>');
            $('#filter_activityName div label').each(function () {
                if ($(this).find('input').is(':checked')) {
                    $(this).removeAttr('class');
                    $(this).find('input').removeAttr('checked');
                }
            });
        });
        //END
        $("#add_vision_staffing_news_menu").on('click', function () {
            if (companyConfigs['run_staffing_before_display_staffing'] == 1) {
                checkStaffings = function (value) {
                    $.ajax({
                        url: '<?php echo $html->url(array('controller' => 'projects', 'action' => 'checkStaffings')); ?>',
                        async: true,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            first: value
                        },
                        beforeSend: function () {
                            $('#loading_w').show();
                        },
                        success: function (data) {
                            progress = data['progress'];
                            if (data['done'] == false) {
                                setValueProgress1(progress);
                                checkStaffings(2);
                            } else {
                                clearInterval(auto);
                                $('#loading_w').hide();
                                $("#dialog_vision_staffing_news_menu").dialog().dialog('open');
                                return false;
                            }
                        }
                    });
                }
                // run first.
                setStatusSystem1();
                checkStaffings(1);
            } else {
                $("#dialog_vision_staffing_news_menu").dialog().dialog('open');
                return false;
            }
        });
        $(".cancel").on('click', function () {
            $("#dialog_vision_staffing_news_menu").dialog().dialog('close');
        });
        $("#add_team_workload_news_menu").on('click', function () {
            $("#dialog_team_workload_news_menu").dialog().dialog('open');
            return false;
        });
        $(".cancel").on('click', function () {
            $("#dialog_team_workload_news_menu").dialog().dialog('close');
        });
        $("#add_team_workload_plus").on('click', function () {
            $("#dialog_team_workload_plus").dialog().dialog('open');
            return false;
        });
        $(".cancel").on('click', function () {
            $("#dialog_team_workload_plus").dialog().dialog('close');
        });
        $("#ok_sum_staffing").click(function () {
            var list = $('#StaffingPriority'),
                    priorities = list.multipleSelect('getSelects');
            list.multipleSelect('disable');
            if (priorities.length) {
                $('#hiddenPriority').val(priorities.join(','));
            } else {
                $('#hiddenPriority').val('');
            }
            //save vs filter.
            vsFilter.set('priority', priorities);
            vsFilter.set('view_by', $('#group-showby :checked').val());
            vsFilter.set('summary', $('#show_summary :checked').val());
            vsFilter.set('show_na', $('[name="show_na"]:checked').val());
            var dateType = parseInt($('#group-date-type :checked').val());
            vsFilter.set('date_type', dateType);
            switch (dateType) {
                case 1:
                case 2:
                    //date & week
                    vsFilter.set('start_date', $('#aStartDate').val());
                    vsFilter.set('end_date', $('#aEndDate').val());
                    vsFilter.unset('start_month', 'start_year', 'end_month', 'end_year');
                    break;
                case 3:
                    //month
                    vsFilter.set('start_month', $('#aStartMonth').val());
                    vsFilter.set('start_year', $('#aStartYear').val());
                    vsFilter.set('end_month', $('#aEndMonth').val());
                    vsFilter.set('end_year', $('#aEndYear').val());
                    vsFilter.unset('start_date', 'end_date');
                    break;
            }
            var listpc = $('#aPC').multipleSelect('getSelects');
            vsFilter.set('activated', $('#aActivated').multipleSelect('getSelects'));
            vsFilter.set('family', $('#aFamily').multipleSelect('getSelects'));
            vsFilter.set('sub_family', $('#aSub').multipleSelect('getSelects'));
            vsFilter.set('pc', listpc);
            vsFilter.set('resource', $('#aEmployee').multipleSelect('getSelects'));
            vsFilter.set('customer', $('#aCustomer').multipleSelect('getSelects'));
            vsFilter.set('prority', $('#StaffingPriority').multipleSelect('getSelects'));
            vsFilter.set('activity', $('#aName').multipleSelect('getSelects'));
            //save filter
            $.z0.History.save('vs_filter', vsFilter);
            //select 1 pc and show staffing by pc:
            if ($('#ActivityProjectStaffingId1').prop('checked')) {
                if (listpc.length == 1) {
                    if (!$("#form_vision_staffing_news_menu .x-target").length) {
                        $('<input type="hidden" name="ItMe" value="" class="x-target x-pc"><input class="x-target" type="hidden" name="target" value="1">').appendTo("#form_vision_staffing_news_menu");
                    }
                    $('.x-pc').val(listpc[0]);
                }
            } else {
                $('.x-target').remove();
            }
            //submit
            $("#form_vision_staffing_news_menu").submit();
            list.multipleSelect('enable');
        });
        $("#reset_sum").on('click', function () {
            //RESET Activated
            $('#aActivated').multipleSelect('setSelects', []);
            //RESET FAMILY
            $('#aFamily').multipleSelect('setSelects', []);
            //RESET SUB FAMILY
            $('#aSub').multipleSelect('setSelects', []);
            //RESET PC
            $('#aPC').multipleSelect('setSelects', []);
            //RESET resource
            $('#aEmployee').multipleSelect('setSelects', []);
            $('#aCustomer').multipleSelect('setSelects', []);
            $('#StaffingPriority').multipleSelect('setSelects', []);
            $('#aName').multipleSelect('setSelects', []);
            vsFilter.set('activated', []);
            vsFilter.set('family', []);
            vsFilter.set('sub_family', []);
            vsFilter.set('pc', []);
            vsFilter.set('resource', []);
            vsFilter.set('customer', []);
            vsFilter.set('prority', []);
            vsFilter.set('activity', []);
            $("#dialog_vision_staffing_news_menu").dialog().dialog('open');
            return false;
        });
       
        /*dialog export*/
        var validField = true;

        var eMessageDialog = $('#activity-export-message-dialog').dialog({
            position: 'center',
            autoOpen: false,
            autoHeight: true,
            modal: true,
            width: 500,
            open: function (event, ui) {
                $(".ui-dialog-titlebar-close", ui.dialog || ui).hide();
                //$('#export-submit-dialog').focus();
            }
        });
        $('.export-datepicker').datepicker({
            dateFormat: 'dd-mm-yy',
            showOn: 'focus',
            onSelect: function () {
                var date1 = $('#export-start-date').datepicker('getDate'),
                        date2 = $('#export-end-date').datepicker('getDate');
                validField = true;
                if (date2 < date1) {
                    validField = false;
                    alert('<?php __('End date must be greater than start date') ?>');
                }
            }
        });

        /**
         * Get Employee Of Profit Center
         */
        getEmployOfProfit = function (profitCenters) {
            var results = '';
            $.ajax({
                url: '<?php echo $html->url(array('controller' => 'activity_forecasts', 'action' => 'getEmployeeOfProfitCenterUsingModuleExportActivity')); ?>',
                cache: false,
                type: 'POST',
                async: false,
                data: {
                    profit_center_id: profitCenters
                },
                success: function (data) {
                    result = JSON.parse(data);
                }
            });
            return result;
        };
        /**
         * Get Employee Of Profit Center
         */
        countRecordExporting = function () {
            var results = '';
            $.ajax({
                url: '<?php echo $html->url(array('controller' => 'activity_forecasts', 'action' => 'countModuleExportActivity')); ?>',
                cache: false,
                async: false,
                success: function (data) {
                    result = JSON.parse(data);
                }
            });
            return result;
        };
        var mergeFile = 'no';

        var createDialog = function () {

            $('#dialog_vision_staffing_news_menu').dialog({
                position: 'center',
                autoOpen: false,
                autoHeight: true,
                modal: true,
                width: 500,
                show: function (e) {
                },
                open: function (e) {

                }
            });


            createDialog = $.noop;
        };
        createDialog();
        var timeout, timeout2, timeout3;
        var multiActivated = $('#aActivated').multipleSelect({
            minimumCountSelected: 0,
            placeholder: '<?php __("-- Any --") ?>',
            onClick: function (view) {
                clearTimeout(timeout);
                timeout = setTimeout(function () {
                    updateList('<?php echo $html->url('/activities/get_activity_filter/') ?>', multiActivated, $('#aName'), view.complete);
                }, 750);
            }
        });
        var multiActivityName = $('#aName').multipleSelect({
            minimumCountSelected: 0,
            placeholder: '<?php __("-- Any --") ?>'
        });
        var multiPriority = $('#StaffingPriority').multipleSelect({
            minimumCountSelected: 0,
            placeholder: '<?php __("-- Any --") ?>'
        });

        var multiPriorityTeam = $('#teamPriority').multipleSelect({
            minimumCountSelected: 0,
            placeholder: '<?php __("-- Any --") ?>'
        });
        var multiPriorityTeamplus = $('#teamPrio').multipleSelect({
            minimumCountSelected: 0,
            placeholder: '<?php __("-- Any --") ?>'
        });
        var multiPcTeamPlus = $('#teamP').multipleSelect({
            minimumCountSelected: 0,
            position: 'top',
            placeholder: '<?php __("-- Any --") ?>',
            onClick: function (view) {
                clearTimeout(timeout3);
                timeout3 = setTimeout(function () {
                    updatePcAndResource(multiPcTeamPlus, $('#aEmployeteam'), view.complete);
                }, 1000);
            }
        });
        var multiFamily = $('#aFamily').multipleSelect({
            minimumCountSelected: 0,
            placeholder: '<?php __("-- Any --") ?>',
            onClick: function (view) {
                clearTimeout(timeout2);
                timeout2 = setTimeout(function () {
                    updateList('<?php echo $html->url('/activities/get_sub_family/') ?>', multiFamily, $('#aSub'), view.complete);
                }, 750);
            }
        });
        var multiSubfamily = $('#aSub').multipleSelect({
            minimumCountSelected: 0,
            placeholder: '<?php __("-- Any --") ?>'
        });
        var multiPc = $('#aPC').multipleSelect({
            minimumCountSelected: 0,
            position: 'top',
            placeholder: '<?php __("-- Any --") ?>',
            onClick: function (view) {
                clearTimeout(timeout3);
                timeout3 = setTimeout(function () {
                    updatePcAndResource(multiPc, $('#aEmployee'), view.complete);
                }, 1000);
            }
            // userData: function(instance, li) {

            //              return {};
            //          }
        });

        var multiResource = $('#aEmployee').multipleSelect({
            minimumCountSelected: 0,
            position: 'top',
            placeholder: '<?php __("-- Any --") ?>'
        });
        var multiCustomer = $('#aCustomer').multipleSelect({
            minimumCountSelected: 0,
            position: 'top',
            placeholder: '<?php __("-- Any --") ?>'
        });
    });
    var totalPC = <?php echo isset($menuListProfitCenters) ? json_encode(count($menuListProfitCenters)) : 0 ?>;
    function updatePcAndResource(loader, filler, callback) {
        var placeholder = filler.multipleSelect('getPlaceholder'),
                list = loader.multipleSelect('getSelects');
        $.ajax({
            url: '<?php echo $html->url('/activities/get_employee_for_profit_center/') ?>',
            cache: true,
            data: {
                data: list
            },
            dataType: 'json',
            beforeSend: function () {
                placeholder.addClass('loading');
                loader.multipleSelect('disable');
                loader.multipleSelect('disableCheckboxes');
            },
            success: function (data) {
                //update filler
                filler.html(data.html);
                filler.multipleSelect('refresh');
                //update loader
                if ($.isArray(data.pc)) {
                    data.pc = $.merge(list, data.pc);
                } else {
                    var pc = [];
                    $.each(data.pc, function (i, v) {
                        pc.push(v);
                    });
                    data.pc = $.merge(list, pc);
                }
                loader.multipleSelect('setSelects', data.pc);
                if ($.isFunction(callback)) {
                    callback(loader, filler, data);
                }
            },
            complete: function () {
                placeholder.removeClass('loading');
                loader.multipleSelect('enable');
                loader.multipleSelect('enableCheckboxes');
                //set select pc all
                var instance = loader.multipleSelect('getInstance');
                var total = instance.$selectItems.filter(':checked').length;
                if (totalPC == total) {
                    $('#ActivitySelectPCAll').val('true');
                } else {
                    $('#ActivitySelectPCAll').val('false');
                }
            }
        });
    }
    function updateList(url, loader, filler, callback, empty) {
        var placeholder = filler.multipleSelect('getPlaceholder'),
                list = loader.multipleSelect('getSelects');
        if (!list.length && !empty) {
            filler.html('');
            filler.multipleSelect('refresh');
            return;
        }
        $.ajax({
            url: url,
            cache: true,
            data: {
                data: list
            },
            beforeSend: function () {
                placeholder.addClass('loading');
            },
            success: function (data) {
                filler.html(data);
                filler.multipleSelect('refresh');
                if ($.isFunction(callback)) {
                    callback(loader, filler, data);
                }
            },
            complete: function () {
                placeholder.removeClass('loading');
            }
        });
    }
    $('#open-main-menu').click(function () {
        if ($('#wd-top-nav').hasClass('dissable-menu')) {
            $('#wd-top-nav').removeClass('dissable-menu');
            $.ajax({
                url: '<?php echo $html->url('/menus/saveHistoryForMenu') ?>',
                type: 'POST',
                data: {
                    data: 2
                },
            });
        } else {
            $('#wd-top-nav').addClass('dissable-menu');
            $.ajax({
                url: '<?php echo $html->url('/menus/saveHistoryForMenu') ?>',
                type: 'POST',
                data: {
                    data: 1
                },
            });
        }
    });
    $('.burger').click(function () {
        $(this).toggleClass('active');
        $('.z0g-header .z0g-header-inner').toggleClass('active');

    });
    $('body').on('click', function (e) {
        if (!($(e.target).hasClass('z0g-header') || $(('.z0g-header')).find(e.target).length)) {
            $('.z0g-header .z0g-header-inner , .burger').removeClass('active');
        }
    });
</script>

<!--chat-->
<script type="text/javascript">
    if ($(window).width() <= 768) {
        $('.z0g_chat_body').addClass('tablet');
    } else {
        $('.z0g_chat_body').removeClass('tablet');
    }
    $('body').on('click', '.back', function () {
        $(".tablet .z0g_chat_left").show();
        $(".tablet .z0g_chat_right").hide();

    });
    $('body').on('click', '#listProject .head-title', function () {
        $('.loading_cmt').show();
        $(".head-title").removeClass('wd-current');
        var idProject = $(this).data('idproject');

        var nameProject = $(this).data('name');

        $(".tablet .z0g_chat_left").hide();
        $(".tablet .z0g_chat_right").show();
        getComment(idProject, nameProject, 'comment-ct');
//        getComment(idProject, nameProject);

//        $('.project-' + idProject).find('.message-new-ct').hide('');
    });
    // get comment for project.
    function getComment(id, name, divID) {
        if (typeof divID == 'undefined')
            divID = 'comment-ct';
        var result = '';
        $('#p-' + id).addClass('wd-current');
        $('.head-title').removeClass('wd-current');
        $('.project-' + id).addClass('wd-current');
        $("#comment-ct").hide();
        $('.textarea-ct').attr('value', '');
        $.ajax({
            url: '/zog_msgs/getComment',
            type: 'POST',
            async: false,
            data: {
                id: id
            },
            dataType: 'json',
            success: function (data) {

                $(".loading_cmt").hide();
                $("#comment-ct").show();
                var htmls = '<div class="lastComment"></div>';
                if (typeof (data['comment']) == 'undefined') {
                    $('.time span').html('');
                    htmls += '<div class="comment-none"><?=__('No data available',true)?></div>';
                } else {
                    $('.time span').html(data['comment'][0].created);
                    $.each(data['comment'], function (ind, _data) {
                        htmls += getDataComment(_data);
                    });
                }
                result = data['maxId'] ? data['maxId'] : 0;
                var subscribe = data['subscribe'] ? data['subscribe'] : 0;
                // subscribe
                if (subscribe == 1) {
                    $('#subscribe').prop('checked', true);
                } else {
                    $('#subscribe').prop('checked', false);
                }
                $('#' + divID).html(htmls);
                if (name != '') {
                    $("#wd-t3").html(name);
                }
                $("#form-zog_msg .project_id").val(id);
//                $('#submit-btn-msg').removeData();
//                $('#submit-btn-msg').attr('data-id', id);
                $('#subscribe').removeData();
                $('#subscribe').attr('data-id', id);

            }
        });
        // gan lai max Id.
        $('#submit-btn-msg').removeData();
//        $('#submit-btn-msg').attr('data-maxid', result);
        $('#max_id').val(result);
        return result;
    }
    $(".textarea-ct").keyup(function (event) {
        if (event.keyCode === 13) {
            var data = $('form#form-zog_msg').serialize();

            saveComment(data);
        }
    });
    // submit comment.
    $('body').on('click', '#submit-btn-msg', function () {
//        var text = $('.textarea-ct').val(),
//                parent_id = $('#parent_id').val(),
//                _id = $('#submit-btn-msg').data('id');
//        var src = $('#accordion .wd-current').data('src');
//        console.log(src);
//        $('.project-title .image-left').empty().html('<img width=50 height=50 src= "' + src + '" alt =""/>');
        var data = $('form#form-zog_msg').serialize();
        saveComment(data);
    });
    function saveComment(data) {
        $('.textarea-ct').attr('value', '');
        var result = '';
        $.ajax({
            url: '/zog_msgs/saveComment',
            type: 'POST',
            data: data,
            success: function (rs) {
                var obj = JSON.parse(rs);
                if (obj.project_id) {
                    $('.textarea-ct').attr('value', '');
                    $("#comment-ct").prepend(getLastComment(obj));
                    $('.comment-none').hide();
                    $(".time span").html(obj.created);
                    $('#max_id').val(obj.id);
//                    $(getLastComment(obj)).insertBefore($(".lastComment :first-child"));
//                    getComment(obj.project_id, '', 'comment-ct');

                }
            }
        });

        return result;
    }
    // search.
    function filter() {
        // Declare variables
        var input, filter, ul, li, a, i, txtValue;
        input = document.getElementById('input-project');
        filter = input.value.toUpperCase();
        ul = document.getElementById("listProject");
        li = ul.getElementsByTagName('li');

        // Loop through all list items, and hide those who don't match the search query
        for (i = 0; i < li.length; i++) {
            a = li[i].getElementsByTagName("a")[0];
            txtValue = a.textContent || a.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = "";
            } else {
                li[i].style.display = "none";
            }
        }
    }
    $('#subscribe').change(function () {
        subscribe = this.checked ? 1 : 0;
        id = $('#subscribe').data('id');
        $.ajax({
            url: '/zog_msgs/saveSub',
            type: 'POST',
            data: {
                id: id,
                subscribe: subscribe
            }
        });
    });
	function setSizeZ0gComment(){
		var z0g_comment_left = 0;
		if($('#menu_zog_msg').length == 0) return;
		if(($(window).width() - $('#z0g_chat').width()) > $('#menu_zog_msg').offset().left){
			$('#z0g_chat').css({
				left: $('#menu_zog_msg').offset().left
			});
		}else{
			var z0g_comment_align = $(window).width() - $('#z0g_chat').width() > 0 ? $(window).width() - $('#z0g_chat').width() : 0;
			$('#z0g_chat').css({
				left: z0g_comment_align
			});
		}
		var listProject = $('#listProject');
		var listProjectComment = $('.wrap-list-comment');
		if(listProject.length > 0){
			var height_ListProject = $(window).height() - listProject.offset().top;
			if( height_ListProject > 700){
				height_ListProject = 700;
			}
			listProject.css({
				height: height_ListProject - 30
			});
		}
		if(listProjectComment.length > 0){
			var height_ListComment = $(window).height() - listProjectComment.offset().top;
			if( height_ListComment > 615){
				height_ListComment = 615;
			}
			listProjectComment.css({
				height: height_ListComment - 30
			});
		}
	};
	$(window).resize(function () {
		setSizeZ0gComment();
	});
    var _timeout = function () {
        var maxId = $('#submit-btn-msg').data('maxid');
        var src = $('#accordion .wd-current').data('src');
        getNewComment();
    };

	// setInterval(_timeout, 20000);


    // ham update nhung comment moi nhat.
    function getNewComment() {
        var _id = $('#project_id').val();
        var maxId = $('#max_id').val();
        $.ajax({
            url: '/zog_msgs/getNewComment',
            type: 'POST',
            dataType: 'json',
            async: false,
            data: {
                maxId: maxId,
                _id: _id
            },
            success: function (result) {
                if (result) {
                    var comment = result['comment'];
                    // hien thi them gia tri o PJ hien tai.
                    var htmls = '';
                    if (comment) {
                        $.each(comment, function (id, data) {
                            htmls += getDataComment(data);

                        });
                    }
                    if (parseInt(result.maxId) > parseInt(maxId)) {
                        $('#comment-ct').prepend(htmls);
                    }
                    $('#max_id').val(result.maxId);
                }
            }
        });
//        $('#submit-btn-msg').removeData();
//        $('#submit-btn-msg').attr('data-maxid', _maxid);
    }
//    var switchStatus = false;
//    $("#subscribe").on('change', function () {
//        if ($(this).is(':checked')) {
//            switchStatus = $(this).is(':checked');
//            $(this).attr('checked', true)
//            console.log(switchStatus);// To verify
//        } else {
//            switchStatus = $(this).is(':checked');
//            $(this).attr('checked', false)
//            console.log(switchStatus);// To verify
//        }
//    });
    function getLastComment(data) {
        var idEm = data['employee_id'],
                name = data['first_name'] + ' ' + data['last_name'],
                content = data['content'].replace(/\n/g, "<br>"),
                date = data['created'];
        var src = js_avatar(idEm);
        var html = '';
        var count_like = data['count_like'] > 0 ? data['count_like'] : "";
        html += '<div id="comment-' + data['id'] + '" class="comment-content">';
        html += '<div class="my-avatar"><img class="avatar" src="' + src + '" alt="photo"></div>\n\
                        <div class="my-comment">\n\
                            <div class="my-header">\n\
                                <div class="my-header-left">\n\
                                <div class="my-date"><span class="user-name"><b>' + name + '</b></span><p><img width="15" src= "<?php echo $html->url('/img/new-icon/Time.svg') ?>"/><em>' + date + '</em></p></div>\n\
                                </div>\n\
                                <div class="my-header-right"><a class="like" href="javascript:void(0)" data-id="' + data['id'] + '" ><span>' + count_like + '</span> <img width="16" src= "<?php echo $html->url('/img/new-icon/like.svg') ?>"/></a>\n\
                                </div>\n\
                                </div>\n\
                            <div class="my-content"><span>' + content + '</span></div>\n\
                    </div><div class="sub"></div>';
//        html += '<div class="sub">';
//        if (data['sub']['comment']) {
//            $.each(data['sub']['comment'], function (sid, val) {
//                html += getDataSubComment(val);
//            });
//        }
//        html += '</div>';
        html += '</div>';
        return html;
    }
    function getDataComment(data) {
        var idEm = data['employee_id']['id'],
                name = data['employee_id']['first_name'] + ' ' + data['employee_id']['last_name'],
                content = data['content'].replace(/\n/g, "<br>"),
                date = data['created'];

        var src = js_avatar(idEm);
        var html = '';
        var count_like = data['count_like'] > 0 ? data['count_like'] : "";
        html += '<div id="comment-' + data['id'] + '" class="comment-content">';
        html += '<div class="my-avatar"><img class="avatar" src="' + src + '" alt="photo"></div>\n\
                        <div class="my-comment">\n\
                            <div class="my-header">\n\
                                <div class="my-header-left">\n\
                                <div class="my-date"><span class="user-name"><b>' + name + '</b></span><p><img width="15" src= "<?php echo $html->url('/img/new-icon/Time.svg') ?>"/><em>' + date + '</em></p></div>\n\
                                </div>\n\
                                <div class="my-header-right"><a class="like" href="javascript:void(0)" data-id="' + data['id'] + '" ><span>' + count_like + '</span> <img width="16" src= "<?php echo $html->url('/img/new-icon/like.svg') ?>"/></a>\n\
                                </div>\n\
                                </div>\n\
                            <div class="my-content"><span>' + content + '</span></div>\n\
                    </div>';
        html += '<div class="sub">';
        if (data['sub']['comment']) {
            $.each(data['sub']['comment'], function (sid, val) {
                html += getDataSubComment(val);
            });
        }
        html += '</div>';
        html += '</div>';
        return html;
    }
    function getDataSubComment(data) {
        var idEm = data['employee_id']['id'],
                name = data['employee_id']['first_name'] + ' ' + data['employee_id']['last_name'],
                content = data['content'].replace(/\n/g, "<br>"),
                date = data['created'];

        var src = js_avatar(idEm);
        var html = '';
        var count_like = data['count_like'] > 0 ? data['count_like'] : "";
        html += '<div id="comment-' + data['id'] + '" class="comment-content">\n\
                    <div class="my-avatar"><img class="avatar" src="' + src + '" alt="photo"></div>\n\
                        <div class="my-comment">\n\
                            <div class="my-header">\n\
                                <div class="my-header-left">\n\
                                <div class="my-date"><span class="user-name"><b>' + name + '</b></span><p><img width="15" src= "<?php echo $html->url('/img/new-icon/Time.svg') ?>"/><em>' + date + '</em></p></div>\n\
                                </div>\n\
                                <div class="my-header-right"><a class="like" href="javascript:void(0)" data-id="' + data['id'] + '" ><span>' + count_like + '</span> <img width="16" src= "<?php echo $html->url('/img/new-icon/like.svg') ?>"/></a>\n\
                                </div>\n\
                                </div>\n\
                            <div class="my-content"><span>' + content + '</span></div>\n\
                    </div>\n\
                </div>';
        return html;
    }
    // $('body').on('click', '.reply', function () {
        // var id = $(this).data('id');
        // $(".parent_id").val(id);
        // $(".textarea-ct").val($(this).data('name'));
    // });
    $('body').on('click', '.like', function () {
        var id = $(this).data('id');
        $.ajax({
            url: '/zog_msgs/like',
            type: 'POST',
            data: {
                zog_msg_id: id
            },
            success: function (rs) {
                var obj = JSON.parse(rs);
                $("#comment-" + id + " .like span").html(obj.count_like);
            }
        })
    });

    function getListProject() {
        $.ajax({
            url: '/zog_msgs/listProjects',
            type: 'GET',
            success: function (rs) {
                var obj = JSON.parse(rs);
                var html = '';

                var left = $('.header-action-left').width() - 162;
                if (($(window).width() <= 1275) && ($(window).width() >= 1024)) {
                    var w = $(window).width() - 320;
                    left = 0;
                    $("#z0g_chat").css("width", w + "px");
                }
                if (obj.length > 0) {
                    $('#z0g_chat').show();
                    $("#z0g_chat").css("left", left + "px");
                    getComment(obj[0].id, obj[0].name);
                    $.each(obj, function (key, data) {
                        if ($(window).width() > 768) {
                            var active = key == 0 ? "wd-current" : "";
                        }
                        var count_msg = '';

                        html += '<li id="p-' + data.id + '" data-idProject="' + data.id + '" data-name="' + data.name + '" class="head-title project-' + data.id + ' ' + active + '"><a>' + data.name + '<span class="message-new-ct">' + data.count + '</span></a></li>'
                    });
                }
                $(".z0g_chat_body").show();
                $(".loading").hide();
                $("#listProject").html(html);
				
				setSizeZ0gComment();
            }
        })
    }
	var interval;
    $('body').on('click', '#menu_zog_msg', function () {
        getListProject();
        interval = setInterval(_timeout, 20000);
    });
    $('body').on('click', '.popup_cancel', function () {
		clearInterval(interval);
        $('#z0g_chat').hide();
		
    });

</script>
