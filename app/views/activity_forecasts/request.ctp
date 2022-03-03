<?php 
/* From 2020.01.13 Remove all ccode with typeRequest != week */
echo $html->script('qtip/jquery.qtip'); 
echo $html->css('/js/qtip/jquery.qtip'); 
    $getDataByPath = $getDataByPath ? 1 : 0;
    echo $this->Html->css(array(
        'projects',
        'activity_request',
        'slick_grid/slick.pager',
        'slick_grid/slick.common',
        'slick_grid/slick.edit',
        '/js/slick_grid/plugins/slick-overlays-gdocs',
		'preview/message-popup',
		'preview/datepicker-new',
    ));
    echo $this->Html->script(array(
        'slick_grid/lib/jquery-ui-1.8.16.custom.min',
        'slick_grid/slick.core',
        'slick_grid/slick.dataview',
        'slick_grid/controls/slick.pager',
        'slick_grid/slick.formatters',
        'slick_grid/plugins/slick.cellrangedecorator',
        'slick_grid/plugins/slick.cellrangeselector',
        'slick_grid/plugins/slick.cellselectionmodel',
        'slick_grid/plugins/slick.rowselectionmodel',
        'slick_grid/plugins/slick.overlays',
        'slick_grid/slick.editors',
        'slick_grid_custom',
        'jquery.inputmask.bundle'
    ));
    if($typeSelect == 'week'){
        echo $this->Html->css(array(
            'slick_grid/slick.grid',
        ));
        echo $this->Html->script(array(
            'slick_grid/lib/jquery.event.drag-2.0.min',
            'slick_grid/slick.grid.origin'
        ));
    } else {
        echo $this->Html->css(array(
            'slick_grid/slick.grid.activity'
        ));
        echo $this->Html->script(array(
            'slick_grid/lib/jquery.event.drag-2.2',
            'slick_grid/slick.grid.activity'
        ));
    }
	
	$svg_icons = array(
		'message' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(-4 -4)"><rect class="a" width="16" height="16" transform="translate(4 4)"/><path class="b" d="M10.5,8h-5a.5.5,0,0,0,0,1h5a.5.5,0,1,0,0-1ZM8,0C3.581,0,0,3.134,0,7a6.7,6.7,0,0,0,3,5.459V16l4.1-2.048c.3.029.6.047.9.047,4.418,0,8-3.134,8-7S12.417,0,8,0ZM8,13H7L4,14.5V11.891A5.772,5.772,0,0,1,1,7C1,3.686,4.133,1,8,1s7,2.686,7,6S11.865,13,8,13Zm3.5-8h-7a.5.5,0,0,0,0,1h7a.5.5,0,1,0,0-1Z" transform="translate(4.001 4)"/></g></svg>',
		'expand' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(-216 -168)"><rect class="a" width="16" height="16" transform="translate(216 168)"/><path class="b" d="M902-2125h-4v-1h3v-3h1v4Zm-8,0h-4v-4h1v3h3v1Zm8-8h-1v-3h-3v-1h4v4Zm-11,0h-1v-4h4v1h-3v3Z" transform="translate(-672 2307)"/></g></svg>',
		'reload' => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16"><g transform="translate(-1323 -240)"><path class="b" d="M199.5,191.5a7.98,7.98,0,0,0-5.44,2.15v-1.51a.64.64,0,0,0-1.28,0v3.2a.622.622,0,0,0,.113.341l.006.009a.609.609,0,0,0,.156.161c.007.005.01.013.017.018s.021.009.031.015a.652.652,0,0,0,.115.055.662.662,0,0,0,.166.034c.012,0,.023.007.036.007h3.2a.64.64,0,1,0,0-1.28h-1.8a6.706,6.706,0,1,1-2.038,4.8.64.64,0,1,0-1.28,0,8,8,0,1,0,8-8Z" transform="translate(1131.5 48.5)"/><rect class="a" width="16" height="16" transform="translate(1323 240)"/></g></svg>',
		'duplicate' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(-1621.625 -334.663)"><rect class="a" width="16" height="16" transform="translate(1621.625 334.663)"/><g transform="translate(36.824 46.863)"><path class="b" d="M1586.915,301.177a1.116,1.116,0,0,1-1.115-1.115V288.915a1.116,1.116,0,0,1,1.115-1.115h8.525a1.116,1.116,0,0,1,1.115,1.115v11.147a1.115,1.115,0,0,1-1.115,1.115Zm0-12.459a.2.2,0,0,0-.2.2v11.147a.2.2,0,0,0,.2.2h8.525a.2.2,0,0,0,.2-.2V288.915a.2.2,0,0,0-.2-.2Z"/><path class="b" d="M1590.915,305.177a1.116,1.116,0,0,1-1.115-1.115v-.656a.459.459,0,1,1,.918,0v.656a.2.2,0,0,0,.2.2h8.525a.2.2,0,0,0,.2-.2V292.915a.2.2,0,0,0-.2-.2h-.656a.459.459,0,0,1,0-.918h.656a1.116,1.116,0,0,1,1.115,1.115v11.147a1.115,1.115,0,0,1-1.115,1.115Z" transform="translate(-0.754 -1.377)"/></g></g></svg>',
		'agenda' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(0)"><rect class="a" width="16" height="16" transform="translate(0)"/><path class="b" d="M15,16H1a1,1,0,0,1-1-1V2.5a1,1,0,0,1,1-1h4V.5a.5.5,0,0,1,1,0v1h4V.5a.5.5,0,1,1,1,0v1h4a1,1,0,0,1,1,1V15A1,1,0,0,1,15,16ZM15,2.5H11V3a.5.5,0,1,1-1,0V2.5H6V3a.5.5,0,0,1-1,0V2.5H1V15H15ZM3.5,6h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1A.5.5,0,0,1,3,7.5v-1A.5.5,0,0,1,3.5,6Zm0,4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,3.5,10Zm4-4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1A.5.5,0,0,1,7,7.5v-1A.5.5,0,0,1,7.5,6Zm0,4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,7.5,10Zm4-4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,11.5,6Zm0,4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,11.5,10Z" transform="translate(0.001 -0.001)"/></g></svg>',
		'add' => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16.002 16.002"><g transform="translate(-120 -231.999)"><rect class="a" width="16" height="16" transform="translate(120 231.999)"/><path class="b" d="M21284,8418v-6h-6a1,1,0,0,1,0-2h6v-6a1,1,0,1,1,2,0v6h6a1,1,0,0,1,0,2h-6v6a1,1,0,1,1-2,0Z" transform="translate(-21157 -8171)"/></g></svg>',
		'users' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(-192 -132)"><rect class="a" width="16" height="16" transform="translate(192 132)"/><g transform="translate(192 134)"><path class="b" d="M205.507,144.938a4.925,4.925,0,0,0-9.707,0h-1.1a6.093,6.093,0,0,1,3.557-4.665l.211-.093-.183-.14a3.941,3.941,0,1,1,4.75,0l-.183.14.21.093a6.1,6.1,0,0,1,3.552,4.664Zm-4.851-10.909a2.864,2.864,0,1,0,2.854,2.864A2.863,2.863,0,0,0,200.657,134.029Z" transform="translate(-194.697 -132.938)"/><path class="b" d="M214.564,143.9a2.876,2.876,0,0,0-2.271-2.665.572.572,0,0,1-.449-.555.623.623,0,0,1,.239-.507,2.869,2.869,0,0,0-1.344-5.114,4.885,4.885,0,0,0-.272-.553,5.52,5.52,0,0,0-.351-.556c.082-.005.164-.008.245-.008a3.946,3.946,0,0,1,3.929,3.955,3.844,3.844,0,0,1-.827,2.406l-.1.13.147.076a3.959,3.959,0,0,1,2.132,3.392Z" transform="translate(-199.639 -133.26)"/></g></g></svg>',
		'validated' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 40 40"><g transform="translate(-317 -66)"><rect class="a" width="32" height="32" transform="translate(317 66)"/><path class="b" d="M9.791,1.412h0L4.314,7.757h0a.648.648,0,0,1-1.01,0h0L.209,4.171h0A.9.9,0,0,1,0,3.585a.777.777,0,0,1,.714-.827A.668.668,0,0,1,1.219,3h0l2.59,3L8.781.242h0A.668.668,0,0,1,9.285,0,.778.778,0,0,1,10,.827.9.9,0,0,1,9.791,1.412Z" transform="translate(332 82.001)"/></g></svg>',
		'reject' => '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><g transform="translate(-323 -71)"><rect class="a" width="32" height="32" transform="translate(-323 -71)"/><path class="b" d="M-287.4-1709.767a.62.62,0,0,1,0-.876l3.942-3.942-3.942-3.943a.619.619,0,0,1,0-.876.619.619,0,0,1,.876,0l3.943,3.943,3.943-3.943a.619.619,0,0,1,.876,0,.619.619,0,0,1,0,.876l-3.942,3.943,3.942,3.942a.62.62,0,0,1,0,.876.62.62,0,0,1-.876,0l-3.943-3.943-3.943,3.943a.618.618,0,0,1-.438.182A.618.618,0,0,1-287.4-1709.767Z" transform="translate(620.586 1800.587)"/></g></svg>');
    $avg = intval(($_start + $_end)/2);
    $totalColums = 3;
    /**
     * get external of employee
     */
    $getExternal = $employeeName['external'];
    if($manage_multiple_resource && $getExternal == 2){
        //$holidays = $requests = array();
        // pr($requests);
    }
	$trans_month = array();
	$trans_month[date('M', $_start)] = __(date('M', $_start), true);
	$trans_month[date('M', $_end)] = __(date('M', $_end), true);
	$trans_month['wee'] = __('wee', true);
	$limited_to_the_capacity_of_week = @$companyConfigs['limited_to_the_capacity_of_week'] && $fillMoreThanCapacity;
?>
<style type="text/css">
.separator-week-div{border-left: 2px solid red; margin-left: -2px;}.disable-edit-day-div{background-color: #FAFAFA;}.foreDayCols{min-width: 100px;max-width:160px;overflow:hidden;}#foreAction{min-width:50px;max-width:50px;overflow:hidden;}#scrollTopAbsence{ height:20px; width:500px; margin-bottom:5px; float:right; overflow-y:hidden;overflow-x:auto;}#scrollTopAbsenceContent{ height:20px;}.slick-cell.l1{ line-height:13px; vertical-align:middle; }
#wd-container-main .wd-layout {
    overflow: visible;
   
}
html{
	background-color: #fff !important;
}
#layout{
	min-width: 100%;
}
.menu-level-1,
.menu-level-2,
.menu-level-3,
.menu-level-3-4 {
    border-color: #bbb;
    box-shadow: 3px 3px 8px rgba(0, 0, 0, 0.3);
}
#menuContext .back-highlighted
{
    background-color: #6aa8ca;
    color: #fff !important;
    border: 1px solid #DFDFDF;
    border-radius: 6px;
}
.hidden-task, .hidden-project{
    display: none !important;
}
.borderEmploy:hover{
    cursor: col-resize;

}
.borderEmploy{
    width:6px;
    float: right;
    /*background: red ;*/
    height:100%;
    margin-right:-9px;
}
.qtip {
    max-width: 320px;
}
.qtip-content {
    font-size: 12px;
    line-height: 20px;
}
.qtip-content dl {
    overflow: hidden;
}
.qtip-content dt {
    float: left;
    width: 100px;
    display: block;
    padding: 0;
    margin: 0;
}
.qtip-content dd {
    width: 200px;
    min-height: 20px;
    display: block;
    padding: 0;
    margin: 0;
    float: left;
    clear: right;
}
body{
 /*overflow: hidden;*/
}
#wd-container-footer{
    display: none;
}
#layout{
	min-height: 100px !important;
}
.slick-viewport{
	overflow: hidden auto !important;
}
.slick-viewport-left{
    overflow: hidden !important;
}
.slick-viewport-right{
    overflow-x: hidden !important;
    overflow-y: auto;
}

#not-select-warning{
	transition: all 0.3s ease;
}
.wd-table #table-control{
	overflow: inherit;
}
.open-day-message{
	margin-left: 6px;
    vertical-align: middle;
    line-height: 20px;
    display: inline-block;
    position: relative;
    top: 1px;
}
.open-day-message svg .b{
	fill: #fff;
}
.open-day-message #Z0gMSG{
	fill: #fff;
}
#sub-nav{
    max-width: 1920px;
    margin: 0 auto;
}
.copyForecastTable .input.checkbox{
	text-align: center;
}
#reForecasts{
    padding: 0 20px;
    box-sizing: border-box;
}
.wd-forecasts-table th{
    height: 30px;
    line-height: 28px;
    padding: 0 15px;
    min-width: 90px;
    text-align: center;
    border: 1px solid #fff;
}
.wd-forecasts-table .wd-action-header{
    min-width: 0px;
    width: 45px;
}
.wd-forecasts-table td{
    border: 1px solid #ccc;
    height: 24px;
    padding: 0;
    vertical-align: middle;
    padding: 0 4px;
}
tbody#absence-table tr td{
	height: 35px;
	vertical-align: middle;
    line-height: 35px;
}
#table-control .btn{
	font-size: 20px;
	line-height: 32px;
}
.wd-tab{
	max-width: 1920px;
}
.btn-action{
	width: 32px;
    height: 32px;
    background-color: #fff;
    display: inline-block;
    text-align: center;
    font-size: 16px;
    line-height: 30px;
    position: relative;
    border: 1px solid #E8E8E8;
    border-radius: 3px;
    -webkit-transition: all 0.4s ease 0s;
    -moz-transition: all 0.4s ease 0s;
    -ms-transition: all 0.4s ease 0s;
    -o-transition: all 0.4s ease 0s;
    transition: all 0.4s ease 0s;
	box-sizing: border-box;
}
.wd-main-content a.btn-action:hover,
a.btn-action:hover{
	border-color: #217FC2;
	background-color: #fff;
}
a.btn-action i,
a.btn-action:before{
	font-size: 16px;
}
#table-control .btn.btn-preview{
	line-height: 30px;
}
.wd-main-content .btn-action.can-send{
	background-color: #6EAF79;
	color: #fff;
}
#submit-request-all-top{
	line-height: 32px;
}
#copy_forecast, #refresh_menu{
	line-height: 34px;
}
.wd-main-content a{
	vertical-align: top;
}
.wd-main-content a.btn-action{
	color: #242424;
}
.wd-main-content a.btn-action:hover:before,
.wd-main-content a.btn-action:hover i:before{
	color: #217FC2;
}
.btn-action:hover svg .b{
	fill: #217FC2;
}
#table-control #open-menu-activity{
	line-height: 30px;
}
#table-control #date-range-picker{
	height: 30px; padding: 0px; width: 0px !important; border: none;
	display: inline;
	vertical-align: top;
	position: absolute;
}
#open-menu-activity:before{
	font-size: 15px;
}
</style>

<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"><div class="wd-panel">
            <div class="wd-list-project">
                <div id="message-place">
                    <?php
                    echo $this->Session->flash();
                    ?>
                </div>
                <div id="not-select-warning" class="message warning" style="display: none;"><?php __('Please select a day to open the message');?> <a href="#" class="close">x</a></div>
                <div id="clean-ok" class="message success" style="display: none;">OK <a href="#" class="close">x</a></div>
                <div id="absence-container" class="wd-activity-actions" style="">
                    <div class="wd-table ch-check loading-mark" id="project_container" style="width:100%;height: auto;">
                        <div id="table-control">
                            <?php
                            echo $this->Form->create('Control', array(
                                'type' => 'get',
                                'style' => 'float: left',
                                'url' => '/' . Router::normalize($this->here)));
                            ?>
                            <fieldset style="margin-left: 22px;">
								<?php 
                                $idManageCheck = null;
                                if($isManage){
                                    echo $this->Form->hidden('id', array('value' => $this->params['url']['id']));
                                    $idManageCheck = $this->params['url']['id'];
                                    echo $this->Form->hidden('profit', array('value' => $this->params['url']['profit'], 'style' => 'padding: 6px'));
                                }
                                echo $this->Form->hidden('get_path', array('value' => $getDataByPath ? 1 : 0));
                                ?>
								<input id='date-range-picker' rel="no-history" value="<?php echo date('d-m-Y',$_start); ?>" readonly="readonly">
								<label id="dateRequest">
									<?php 
									$text_format = 'next friday';
									if(!empty($workdays)){
										foreach($workdays as $key => $isWorking){
											if($isWorking == 1) $text_format = 'next '.$key;
										}
									}
								
									$_end_format = strtotime($text_format, $_start);
									$text_date = '';
									$text_start = date('d ', $_start) . $trans_month[date('M', $_start)] . '. ';
									$text_end = date('d ', $_end_format) . $trans_month[date('M', $_end_format)] . '. ' . date('Y', $_end_format);
									$text_date = sprintf(__('Week %1$s - %2$s to %3$s', true), date('W', $_start),$text_start,$text_end);  
									?>
									<input id='date-range-picker-display' rel="no-history" type="text" value="<?php echo $text_date; ?>" readonly="readonly">	
									<?php echo $svg_icons['agenda']; 
									echo $this->Form->hidden('week', array('value' => date('W', $_start), 'rel'=>'no-history'));
									echo $this->Form->hidden('year', array('value' => date('Y', $_start), 'rel'=>'no-history'));
									?>
								</label>
                                <h2 class="activity-request-status">
                                    <?php
                                        $dateValidate = !empty($requestConfirmDate) ? date('d/m/Y', $requestConfirmDate) : '';
                                        $nameValidate = !empty($requestConfirmName) ? $requestConfirmName : '';
                                        if ($requestConfirm == 0 && $typeSelect != 'year'){
                                            //__('Waiting validation');
                                            __('Sent');
                                        } elseif ($requestConfirm == 1 && $typeSelect != 'year'){
                                            printf(__('Rejected (%s)', true), $nameValidate. ', ' .$dateValidate);
                                        } elseif ($requestConfirm == 2 && $typeSelect != 'year'){
                                            printf(__('Validated (%s)', true), $nameValidate. ', ' .$dateValidate);
                                        } elseif($requestConfirm == -1 && $typeSelect != 'year'){
                                            __('In progress');
                                        }
                                    ?>
                                </h2>
                                <div style="clear:both;"></div>
                            </fieldset>
                            <?php
                            echo $this->Form->end();
							$employee_info = $this->Session->read('Auth.employee_info');
							$is_sas = $employee_info['Employee']['is_sas'];
							if ($is_sas != 1) {
								$role = $employee_info['Role']['name'];
							}
							$canManageResource = $employee_info['CompanyEmployeeReference']['role_id'] == 3 && $employee_info['CompanyEmployeeReference']['control_resource'];
                            ?>
							
                            <?php 
							if(empty($this->params['url']['id']) || empty($this->params['url']['profit'])){
								if($role == "admin" || $canManageResource){
									$isManage = 1;
								}
							}
							/* Edit by Huynh 
							* Email: Important email Activity request : priority one
							*/
							if( $typeSelect != 'year'){
								$ip = ($requestConfirm == -1 || $requestConfirm == 1);
								$_sent = ($requestConfirm == 0);
								$_validated = ($requestConfirm == 2);
								// debug( $_validated); exit;
								?>
								<div class="display-inline title-timesheet-action"  id="action-inprogress" <?php if(!$ip) echo 'style="display: none"';?>>
									<a href="javascript:void(0)" class="btn-action" id="refresh_menu" title="<?php __('Refresh Menu')?>"><?php echo $svg_icons['reload'];?></a>
									<a href="javascript:void(0)" class="btn-action" id="copy_forecast" class="copy-timesheet" title="<?php __('Copy Forecast')?>"><?php echo $svg_icons['duplicate'];?></a>
                                    <a href="javascript:void(0)" class="btn-action" id="submit-request-all-top" class="send-for-validate send-for-validate-top" title="<?php __('Send')?>"><i class="icon-rocket"></i></a>
								</div>
								<div class="display-inline title-timesheet-action" id="action-sent-validated"  <?php if(!$_sent&& !$_validated) echo 'style="display: none"';?>>
									<?php if( $role == "admin" || $canManageResource || ( $isPCManager && $role == 'pm' ) ){?>
										<a href="javascript:void(0)" id="submit-request-no-top" class="btn-action validate-for-reject validation-for-reject-top" title="<?php __('Reject Requested')?>"><?php echo $svg_icons['reject'];?></a>
									<?php } ?> 
								</div>
								<div class="display-inline title-timesheet-action" id="action-validated" <?php if(!$_sent) echo 'style="display: none"';?>>
									<?php if( $role == "admin" || $canManageResource || ( $isPCManager && $role == 'pm' ) ) { ?>
										<a href="javascript:void(0)" id="submit-request-ok-top" class="btn-action validate-for-validate validation-for-validate-top" title="<?php __('Validate Requested')?>"><?php echo $svg_icons['validated'];?></a>
									<?php } ?> 
								</div>
							<?php } 
							/* End edit by Huynh */
							?>
							
							<!-- ***Quan update hover on expand icon 14/01/2019*** -->
                            <a href="javascript:;" onclick="expandScreen();" id="expand-btn" class="btn btn-action" title="<?php __('Fullscreen')?>"><?php echo $svg_icons['expand'] ?></a>
							<!-- End update -->
                            <?php
                                $employee_info = $this->Session->read('Auth.employee_info');
                                $role = !empty($employee_info['Role']) && !empty($employee_info['Role']['name']) ? $employee_info['Role']['name'] : '';
                            
                            ?>
							<?php
								$show_activity_forecast_comment = isset($companyConfigs['show_activity_forecast_comment']) ? $companyConfigs['show_activity_forecast_comment'] : 0;
							?>
							<!-- ***Quan update hover on Add an activity icon 14/01/2019*** -->
                            <a href="javascript:;" id="open-menu-activity" class="btn btn-action" title="<?php __('Add an activity')?>"><?php echo $svg_icons['add']; ?></a>
							<!-- End update -->
							<?php if($show_activity_forecast_comment) { ?>
							<!-- ***Quan update hover on Add a message icon 14/01/2019*** -->
                            <a href="javascript:;" id="open-popup-message" class="btn btn-action btn-message" title="<?php __('Add a message')?>">
							<span><?php __('Add a message'); ?></span>
							<!-- End update -->
								<?php echo $svg_icons['message']; ?>
							</a>
							<?php } ?>
							<?php 
							if( empty( $employee_info['Employee']['company_id'])){
								$pass = $_GET;
								unset($pass['url']); ?>
								<a href="<?php echo $this->Html->url(array('controller' => 'activity_forecasts_preview', 'action' => 'request', '?' => $pass));?>" id="show-preview" class="btn btn-action btn-preview" title="<?php __('Open new screen')?>">
									<i class="icon-star"></i>
								</a>
							<?php } ?> 
                        </div>
                        <div id="absence-wrapper" <?php if($typeSelect != 'week'){echo "class='hideScroll'";}?> >

                            <?php if($typeSelect != 'week'){?>
                            <style>#absence-fixed{ width:28% !important; }</style>
                            <div id="scrollTopAbsence"><div id="scrollTopAbsenceContent"></div></div>
                            <br clear="all"  />
                            <table id="absence-fixed">
                                <thead>
                                    <tr>
                                        <th rowspan="2" width="" id="foreEmploy"></th>
                                        <th rowspan="2" width="30%" id="foreCapacity"><?php __(''); ?></th>
                                    </tr>
                                </thead>
                                <tbody id="absence-table-fixed">
                                </tbody>
                            </table>
                            <?php } ?>
                            <div id="absence-scroll" style="visibility: hidden;">
                                <table id="absence" style="visibility: visible;">
                                    <thead>
                                        <tr>
                                            <?php if($typeSelect == 'week'):?>
                                                                                    <th rowspan="2" width="18%" id="foreEmploy">
                                                                                        <span class="slick-sort-indicator slick-sort-indicator-asc actisort"></span>
                                                                                        <div class="borderEmploy" ></div></th>
                                            <th rowspan="2" width="8%" id="foreCapacity"><?php __(''); ?></th>
                                            <?php endif;?>
                                            <?php
                                                if(!empty($listWorkingDays)):
                                                    $count=3;
                                                    foreach($listWorkingDays as $key => $val):
                                                            $totalColums++;
                                            ?>
                                            <th class="foreDayCols <?php echo 'fore'.date('l', $val);?> col-<?php echo $count;?>"> <span><?php echo __(date('l', $val)) . __(date(' d ', $val)) . __(date('M', $val)); ?></span><a href="javascript:void(0);" class="open-day-message" id="msg-day-<?php echo $val; ?>" onclick="getCommentRequest('date',<?php echo $val; ?>);"><?php echo $svg_icons['message']; ?></a></th>
                                            <?php
                                                    $count++;
                                                    endforeach;
                                                endif;
                                            ?>
                                            <?php
                                                if(($typeSelect == 'week' || $typeSelect == 'month') && ($requestConfirm == -1 || $requestConfirm == 1)):
                                            ?>
                                            <th rowspan="2" width="1%" id="foreAction"><?php echo __('Action'); ?></th>
                                            <?php
                                                endif
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody id="absence-table">
                                        <tr><td colspan="27">&nbsp;</td></tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                        <div id="activity-request" style="margin: 0px 22px 20px 22px;"></div>
						<div class="wd-title" style="display: none;">
							<?php
							echo $this->Form->create('Request', array(
								'id' => 'form-request-all',
								'type' => 'get',
								'url' => array('controller' => 'activity_forecasts', 'action' => 'confirm_request', $typeSelect, $profit)));
							if ($isManage) {
								echo $this->Form->hidden('id', array('value' => $this->params['url']['id']));
								echo $this->Form->hidden('profit', array('value' => $this->params['url']['profit']));
							}
							$valWeek = date('W', $avg);
							$valYear = date('Y', $avg);
							if($typeSelect === 'month'){
								$valWeek = date('W', $_start);
								$valYear = date('Y', $_start);
								echo $this->Form->hidden('month', array('value' => @$_GET['month']));
							}
							echo $this->Form->input('week', array('type' => 'hidden', 'value' => $valWeek));
							echo $this->Form->input('year', array('type' => 'hidden', 'value' => $valYear));
							echo $this->Form->hidden('rd', array('value' => mt_rand(9999, 99999)));
							echo $this->Form->hidden('get_path', array(
								'name' => 'get_path',
								'value' => $getDataByPath ? 1 : 0
							));
							echo $this->Form->end();
							?>
							<a href="javascript:void(0)" id="submit-request-all" style="display: none" class="send-for-validate send-for-validate-bot" title="<?php __('Send')?>"><span><?php __('Request validate'); ?></span></a>
							<?php
							$types = 'week';
							$urls = array('profit' => $profit, 'week' => date('W', $_start), 'year' => date('Y', $_start), 'get_path' => $getDataByPath ? 1 : 0);
							echo $this->Form->create('ValidateRequest', array(
								'escape' => false, 'id' => 'request-form', 'type' => 'post',
								'url' => array('controller' => 'activity_forecasts', 'action' => 'response', $types, '?' => $urls)));
							echo $this->Form->hidden('id.' . $employeeName['id'], array('value' => 1));
							echo $this->Form->hidden('validated', array('name' => 'data[validated]', 'value' => 0, 'id' => 'ac-validated'));
							echo $this->Form->end();
							?>
							<?php
							$employee_info = $this->Session->read('Auth.employee_info');
							$is_sas = $employee_info['Employee']['is_sas'];
							if ($is_sas != 1) {
								$role = $employee_info['Role']['name'];
							}?>
                        </div>
                    </div>
                </div>
            </div></div></div>
        </div>
    </div>
</div>
<div id="menuContext" style=" content:close-quote; position:absolute;">

</div>
<?php
/**
 * Cac function ho tro cho phan view
 */
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

function formatForecast($n) {
    return number_format($n, 2, '.', '');
}
/**
 * Xay dung du lieu cho phan forecast va phan timesheet
 */
$dataView = array();
$mapDatas = array('hl' => array(), 'ab' => array(), 'ac' => array(), 'chAb' => array());
$absences['off'] = array('print' => __('Holiday', true));
$can_send = true;
foreach ($employees as $id => $employee) {
    foreach ($listWorkingDays as $day => $time) {
        $default = array(
            'date' => $time,
            'absence_am' => 0,
            'absence_pm' => 0,
            'activity_am' => 0,
            'activity_pm' => 0,
            'employee_id' => $id
        );
        foreach (array('am', 'pm') as $type) {
            if (!empty($holidays[$time][$type])) {
                continue;
            }
            $isValidated = isset($requests[$id][$time]['absence_' . $type]) && ($requests[$id][$time]['response_' . $type] == 'validated' || $requests[$id][$time]['response_' . $type] == 'waiting');
            if (!empty($forecasts[$id][$time]['activity_' . $type]) && $forecasts[$id][$time][$type . '_model']) {
                $default['activity_' . $type] = $forecasts[$id][$time]['activity_' . $type];
                $default['model_' . $type] = strtolower($forecasts[$id][$time][$type . '_model']);
                if ($isValidated) {
                    unset($activityRequests[$default['activity_' . $type]]);
                }
            }
            if (!empty($requests[$id][$time]['absence_' . $type])
                    && ($requests[$id][$time]['response_' . $type] === 'validated'
                    || $requests[$id][$time]['response_' . $type] === 'waiting'
                    || empty($forecasts[$id][$time]['activity_' . $type]))) {
                $default['absence_' . $type] = $requests[$id][$time]['absence_' . $type];
                $default['response_' . $type] = $requests[$id][$time]['response_' . $type];
				if($requests[$id][$time]['response_' . $type] === 'waiting'){
					$can_send = false;
				}
                if ($isValidated) {
                    $mapDatas['chAb'][$default['absence_' . $type]][] = $requests[$id][$time]['response_' . $type];
                    if (!isset($mapDatas['ab'][$default['absence_' . $type]][$day])) {
                        $mapDatas['ab'][$default['absence_' . $type]][$day] = 0;
                    }
                    $mapDatas['ab'][$default['absence_' . $type]][$day] += 0.5;
                }
            }
        }
        $dataView[$id][$day] = $default;
    }
}
echo '<style type="text/css">
    .slick-header-columns{ display:none;},
    .slick-viewport{ padding:0 !important; },
    .slick-viewport .grid-canvas .ab-validated .r1,
    .slick-viewport .grid-canvas .ab-holiday .r1 {background-color: #ffff00; color: #000 !important; }
    .ab-holiday .r1 {background-color: ' . $constraint['holiday']['color'] . '; color: #000 !important; }
    .ab-validation .r1 {background-color: ' . $constraint['validated']['color'] . '; color: #fff !important;}
    .ab-waiting .r1 {background-color: ' . $constraint['waiting']['color'] . '; color: #000 !important;}
    .ab-validated .r1,.rp-validated span {}
    .ab-holiday .r1, .rp-holiday span {}
    .l0{ padding-right:0px !important; }
    .l'.$totalColums++.'{ padding-right:0px !important;}
      </style>';
$css = '';
foreach ($constraint as $key => $data) {
    //$css .= ".rp-$key span {background-color :#fff;}";
}
echo '<style type="text/css">' . $css . '</style>';
$columns = array(
    array(
	'id' => 'no.',
	'field' => 'no.',
	'name' => '#',
	'sortable' => false,
	'resizable' => false,
	'noFilter' => 1,
    ),
    array(
    'rerenderOnResize' => true,
	'id' => 'activity',
	'field' => 'activity',
	'name' => __('Activity', true),
	'width' => 130,
	'sortable' => true,
	'resizable' => true,
	'noFilter' => 1,
	'formatter' => 'Slick.Formatters.Activity',
	'editor' => 'Slick.Editors.activityLabel',
	'sorter' => 'activityNameSorter'
    ),
    array(
    'rerenderOnResize' => true,
	'id' => 'capacity',
	'field' => 'capacity',
	'name' => __('Capacity', true),
	'width' => 130,
	'sortable' => false,
	'resizable' => false,
	'noFilter' => 1,
	'formatter' => 'Slick.Formatters.Capacity'
    )
);
if(!empty($listWorkingDays)){
    foreach($listWorkingDays as $key => $val){
        $columns[] = array(
            'rerenderOnResize' => true,
                'id' => $val,
                'field' => $val,
                'name' => __(date('l', $val), true) . ' / ' . date('d M', $val),
                'width' => 130,
                'sortable' => false,
                'resizable' => false,
				'format_date' => date('d-m-Y', $val),
                'noFilter' => 1,
                'fillable' => true,
                'editor' => 'Slick.Editors.forecastValue',
                'validator' => 'DataValidation.forecastValue',
                'formatter' => 'Slick.Formatters.forecastValue'
        );
    }
}
$columns[] = array(
    'rerenderOnResize' => true,
        'id' => 'action.',
        'field' => 'action.',
        'name' => __('Action', true),
        'width' => 50,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
);
$i = 1;
$gridView = array();
$selectMaps = array();
$capacity = array();
$totalCapacity = 0;
$gHour = !empty($getHour) && !empty($getHour['Employee']['hour']) ? $getHour['Employee']['hour'] : 0;
$gMinute = !empty($getHour) && !empty($getHour['Employee']['minutes']) ? $getHour['Employee']['minutes'] : 0;
foreach ($listWorkingDays as $day => $time) {
    if( $isMulti ){
        $capacity[$day] = floatval(isset($mCapacity[$day]) ? $mCapacity[$day] : 0);
    } else {
        //2016-11-04 ratio
        $capacity[$day] = floatval($workdays[strtolower(date('l', $day))]*$ratio);
    }
    if($managerHour){
        $capacity[$day] = $gHour . ':' . $gMinute;
        $totalCapacity += $gHour*60+$gMinute;
    }
    if (isset($holidays[$time])) {
        if( $isMulti ){
            $capacity[$day] = 0;
            $mapDatas['hl']['off'][$day] = 0;
        } else {
            foreach ($holidays[$time] as $k => $val) {
                if (!isset($mapDatas['hl']['off'][$day])) {
                    $mapDatas['hl']['off'][$day] = 0;
                }
                if($k == 'am' || $k == 'pm'){
                    $mapDatas['hl']['off'][$day] += 0.5*$ratio;
                }
            }
        }
    }
}
if($managerHour){
    $minutes = $totalCapacity%60;
    $hour = ($totalCapacity - ($totalCapacity%60))/60;
    $capacity['capacity'] = $hour . ':' . $minutes;
} else {
    $capacity['capacity'] = array_sum($capacity);
}


$_mapDatas = !empty($mapDatas['chAb']) ? $mapDatas['chAb'] : array();
unset($mapDatas['chAb']);

foreach ($mapDatas as $type => $mapData) {
    foreach ($mapData as $activity => $dx) {
        $data = array(
            'id' => $type . '-' . $activity,
            'no.' => $i++,
            'type' => $type,
            'MetaData' => array(),
            'readonly' => true
        );
        if ($type == 'ab') {
            $data['MetaData']['cssClasses'] = 'disabled ab-validated';
            if(!empty($_mapDatas[$activity]) && in_array('waiting', $_mapDatas[$activity])){
                $data['MetaData']['cssClasses'] = 'disabled ab-validated ab-waiting';
            } else {
                $data['MetaData']['cssClasses'] = 'disabled ab-validated ab-validation';
            }
        } elseif ($type == 'hl') {
            $data['MetaData']['cssClasses'] = 'disabled ab-holiday';
        }
        $data['activity'] = $activity;
        foreach ($dx as $day => $val) {
            if($managerHour){
                if($val == 1){
                    $data[$day] = $gHour . ':' . $gMinute;
                } else {
                    $tHour = $gHour * 60 + $gMinute;
                    $tHour = round($tHour/2, 0);
                    $mHour = $tHour%60;
                    $hHour = ($tHour- $mHour)/60;
                    $hHour = ($hHour < 10) ? '0' . $hHour : $hHour;
                    $mHour = ($mHour < 10) ? '0' . $mHour : $mHour;
                    $data[$day] = $hHour . ':' . $mHour;
                }
            } else {
                $data[$day] = formatForecast($val);
                //2016-11-04 ratio
                if($type == 'ab'){
                    $data[$day] *= $ratio;
                }
            }

        }
        if ($type == 'ac') {
            foreach ($dayMaps as $day => $time) {
                if (isset($activityRequests[$activity][$time])) {
                    if($managerHour){
                        $data[$day] = $activityRequests[$activity][$time]['value_hour'];
                    } else {
                        $data[$day] = formatForecast($activityRequests[$activity][$time]['value']);
                    }
                }
            }
            unset($activityRequests[$activity]);
        }
        $data['action.'] = '';
        $gridView[] = $data;
    }
}

foreach ($activityRequests as $activity => $data) {
    $data = array(
        'id'          => 'ac-' . $activity,
        'no.'         => $i++,
        'type'        => 'ac',
        'MetaData'    => array(),
        'last'        => $activity
    );
	
    $new   = $activity;
    $task  = '';
    if(strpos($activity, '-') !== false){
        list($new , $task) = explode('-', $activity , 2);
    }

    $data['activity'] = $new;
    $data['task_id']  = $task;
    foreach ($listWorkingDays as $day => $time) {
        if (isset($activityRequests[$activity][$time])) {
            if($managerHour){
                $data[$day] = $activityRequests[$activity][$time]['value_hour'];
            } else {
                // $data[$day] = formatForecast($activityRequests[$activity][$time]['value']);
                $data[$day] = floatval($activityRequests[$activity][$time]['value']);
            }

        }
    }
	
    $data['action.'] = '';
    $gridView[] = $data;
}
$i18ns = array(
    'Comment(s)' => __('Comment(s)', true),
    'Add a comment' => __('Add a comment', true),
    'Summary' => __('Summary', true),
    'Holiday' => __('Holiday', true),
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'You have to declare %s days before validate your request' => __('You have to declare %s days before validate your request', true),
	'Absence has to be validated before sending the timesheet' => __('Absence has to be validated before sending the timesheet', true),
    'Your are sure to validate you time sheet? Once validated your could not modify it' => __('Your are sure to validate you time sheet? Once validated your could not modify it', true),
    'The value must between %1$s and %2$s.' => __('The value must between %1$s and %2$s.', true),
    'Clear' => __('Clear', true),
    'No name' => __('No name', true),
    'Unknown' => __('Unknown', true),
    'No more value is allowed' => __('No more value is allowed', true),
    'Send timesheet?' => __('Send timesheet?', true),
    'There is error(s) in your timesheet' => __('There is error(s) in your timesheet', true),
    'Please justify to fill more than capacity by day or close the popup to cancel. Justification %s:' => __('Please justify to fill more than capacity by day or close the popup to cancel. Justification %s:', true),
	'picker_text' => __('Week %1$s - %2$s to %3$s', true),
);


if($typeSelect === 'week'){
    $queryUpdate = '/week?week=' . date('W', $avg) . '&year=' . date('Y', $avg);
}else{
    $queryUpdate = '/month?month=' . date('m', $_start) . '&year=' . date('Y', $_start);
}
if ($isManage) {
    $queryUpdate .= '&id=' . $this->params['url']['id'] . '&profit=' . $this->params['url']['profit'];
}
if ($is_sas != 1) {
    $role = $employee_info['Role']['name'];
}
$queryUpdate = $getDataByPath ? $queryUpdate.'&get_path='.$getDataByPath : $queryUpdate;
?>
<!-- Phan html dung de xu ly mot so tac vu an, khi nao can thiet se goi len -->
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a class="wd-hover-advance-tooltip timesheet-delete-task" href="<?php echo $this->Html->url(array('action' => 'delete_request', '%1$s','%3$s')) . $queryUpdate; ?>">&nbsp;</a>
			
        </div>
    </div>
</div>
<!-- dialog_import -->
<div id="dialog_import_CSV" style="display:none" title="<?php __('Import CSV file') ?>" class="buttons">
    <?php
    echo $this->Form->create('Import', array('id' => 'uploadForm', 'type' => 'file', //'target' => '_blank',
        'url' => array('controller' => 'activity_forecasts', 'action' => 'import_csv')));
    ?>
    <div class="wd-input">
        <center>
            <label><?php echo __('File:') ?></label>
            <input type="file" name="FileField[csv_file_attachment]" />
        </center>
        <div style="clear:both; margin-left:100px; width: 220px; color: #008000; font-style:italic;">(<?php __('Allowed file type') ?>: *.csv)</div>
    </div>
    <ul class="type_buttons">
        <li><a class="cancel" href="javascript:void(0)"><?php echo __('Close') ?></a></li>
        <li><a id="import-submit" class="new" onclick="return false;" href="#"><?php echo __('Submit') ?></a></li>
        <li id="error"></li>
    </ul>
    <?php echo $this->Form->end(); ?>
</div>
<!-- dialog_import -->
<!-- Dialog xu ly phan request all data in timesheet -->
<div id="dialog-request-all" class="buttons" style="display: none;" title="<?php echo __('Send request for validation', true) ?>">
    <div class="dialog-request-message">

    </div>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"></a></li>
        <li><a href="javascript:void(0)" class="ok"></a></li>
    </ul>
</div>
<!-- dialog_vision_portfolio: phan nay xu ly luc reject va validate o admin -->
<div id="add-comment-dialog" class="buttons" style="display: none;" title="<?php __('Confirm'); ?>">
    <div class="dialog-request-message">
        <?php //__('Your are sure to validate you time sheet? Once validated your could not modify it'); ?>
    </div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"></a></li>
        <li><a href="javascript:void(0)" class="ok"></a></li>
    </ul>
</div>

<div id="template_logs" class="template_logs" style="height: 420px; width: 320px;display: none;">
    <div class="add-comment"></div>
    <div id="content_comment" style="min-height: 50px">
    <div class="append-comment"></div>
    </div>
    
</div>
<!-- ToolTip -->
<div id="tooltip-template" class="tooltip-template buttons" style="display: none;">
    <dl class="non-actask">
        <dt><?php __('Name'); ?> :</dt>
        <dd>%1$s</dd>
        <dt><?php __('Short name'); ?> :</dt>
        <dd>%2$s</dd>
        <dt><?php __('Long name'); ?> :</dt>
        <dd>%3$s</dd>
        <dt><?php __('Family'); ?> :</dt>
        <dd>%4$s</dd>
        <dt><?php __('Subfamily'); ?> :</dt>
        <dd>%5$s</dd>
        <dt class="actask"><?php __('Task'); ?> :</dt>
        <dd class="actask">%6$s</dd>
    </dl>
</div>
<!-- Cho nay de tao context menu -->
<div id="jqxMenu"></div>
<?php 
	$date_format = __("'Week' d - d M.  'to' d M. yy'", true);
	$select_project_without_task = isset($companyConfigs['select_project_without_task']) ? $companyConfigs['select_project_without_task'] : 0; 
?>
<!-- =============================================== SCRIPT WRITE INSIDE CODE ================================================================ -->
<script type="text/javascript">
	var date_format = <?php echo json_encode($date_format); ?>;
	$('#date-range-picker-display').on('click', function(){
		// $('#date-range-picker').trigger('click');
		$('#date-range-picker').datepicker('show');
	});
	function selectedDate(start, end){
		if(start && end) {
			_startDate = dateString(start);
			_endDate = dateString(end);
			_day_start = _startDate.split("-");
			_day_end = _endDate.split("-");
			_class = '.date-'+_day_start[0]+'-'+_day_start[1]+'-';
			for(i = _day_start[2]; i<= _day_end[2]; i++){
				$('#date-range-picker').find(( _class.toString() + i)).addClass('ui-state-highlight');
			}
		}
	}
	function initDatePicker(eleDatePicker){
		$(eleDatePicker).datepicker({
			showOtherMonths: true,
			selectOtherMonths: true,
			dateFormat: 'dd-mm-yy',
			onSelect: function(dateText, inst) {
				var type = 'week';
				var date = $(this).datepicker('getDate');
				var input_date = dateString(date, 'dd-mm-yy');
				// $('#date-range-picker').val(input_date);
				$('#date-range-picker').trigger('change');
				var curStart = $('#nct-start-date').datepicker('getDate'),
					curEnd = $('#nct-end-date').datepicker('getDate');
				if( type == 'week' ){
					startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 1);  //select monday
					endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 5);    //select friday
				} else if(type == 'month' ){
					startDate = new Date(date.getFullYear(), date.getMonth(), 1);  //select first day
					endDate = new Date(date.getFullYear(), date.getMonth() + 1, 0);  //select last day
				} else{
					startDate = new Date(date.getFullYear(), date.getMonth(), 1);  //select first day
					endDate = new Date(date.getFullYear() + 1, date.getMonth(), 0);  //select last day
				}
				var start = dateString(startDate, 'dd M.');
				var end = dateString(endDate, 'dd M. yy');
				var week = $.datepicker.iso8601Week(startDate);
				var year = getYearFromWeekNumber(startDate, week);
				selectedDate(startDate, endDate);
				setTextDatePicker(start, end, week);
				$('#RequestWeek').val(week).trigger('change');
				$('#RequestMonth').val(dateString(endDate, 'm')).trigger('change');
				$('#RequestYear').val(year).trigger('change');
				loadDataTimeshet(week, year);
				
			},
			onChangeMonthYear: function(year, month, inst) {
				selectCurrentRange();
			}
		});
	}
	initDatePicker('#date-range-picker');
	function getYearFromWeekNumber(date, week) {
        var year = date.getFullYear();
        if (week == 1 && date.getMonth() == 11) {
			year++;
        }
        return year;
    }
	function setTextDatePicker(startPicker, endPicker, week){
		var text_date = $this.t('picker_text', week, startPicker, endPicker);
		// $('#date-range-picker').datepicker('setDate', dateString((new Date(startdate * 1000)), 'dd-mm-yy'));
		$('#date-range-picker-display').val(text_date);
	}
	function selectCurrentRange(){
        $('#date-range-picker').find('.ui-datepicker-current-day').addClass('ui-state-highlight');
    }
	function dateString(date, format){
        if( !format )format = 'yy-mm-dd';
        return $.datepicker.formatDate(format, date);
    }
	$(document).ready(set_slick_table_height);
	$(window).resize(set_slick_table_height);
	function set_slick_table_height(){
		var wdTable = $('#activity-request');
		// if( !wdTable.length) return;
		if( wdTable.length){
			var heightTable = $(window).height() - wdTable.offset().top - 40;
			wdTable.css({
				height: heightTable,
			});
			if ( ('url' in SlickGridCustom ) && SlickGridCustom.url){
				SlickGridCustom.getInstance().resizeCanvas();
			}
		}
	}
    (function($){
        //$(function(){
            var openDialog = function(title,callback){
                var $dialog = $('#add-comment-dialog').attr('title' , title);
                $dialog.dialog({
                    zIndex : 10000,
                    modal : true,
                    minHeight : 50,
                    close : function(){
                        $dialog.dialog('destroy');
                    }
                });
                $dialog.find('a.ok').unbind().click(function(){
                    callback.call(this);
                });
                $dialog.find('a.cancel').unbind().click(function(){
                    $dialog.dialog('close');
                    return false;
                });
            };
            $('#submit-request-no, #submit-request-no-top').click(function(){
                openDialog('<?php __('Reject Activity request?'); ?>',function(){
                    $('#ac-validated').val(0);
                    $('#request-form').submit();
                });
            });
            $('#submit-request-ok, #submit-request-ok-top').click(function(){
                openDialog('<?php __('Activity validate?'); ?>',function(){
                    $('#ac-validated').val(1);
                    $('#request-form').submit();
                });
            });

        //});

    })(jQuery);
</script>
<script>
    var $this = SlickGridCustom, actionTemplate  =  $('#action-template').html(), currentMousePos = { x: -1, y: -1 }
		listEmployeeName = <?php echo json_encode($listEmployeeName); ?>,
		employeeName = <?php echo json_encode($employeeName); ?>,
        mapeds = <?php echo json_encode($mapeds); ?>,
        capacity = <?php echo json_encode($capacity); ?>,
        absences = <?php echo json_encode($absences); ?>,
        workdays = <?php echo json_encode($workdays); ?>,
        listWorkingDays = <?php echo  json_encode($listWorkingDays);?>,
        daysInWeek = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
        activityNotAccessDeletes = <?php echo json_encode($activityNotAccessDeletes);?>,
        taskNotAccessDeletes = <?php echo json_encode($taskNotAccessDeletes);?>,
        checkFullDayActivities = <?php echo json_encode($checkFullDayActivities);?>,
        checkFullDayTasks = <?php echo json_encode($checkFullDayTasks);?>,
        checkFullDays = <?php echo json_encode($checkFullDays);?>,
        manage_multiple_resource = <?php echo json_encode($manage_multiple_resource);?>,
        resource_see_him = <?php echo json_encode($resource_see_him);?>,
        getExternal = <?php echo json_encode($getExternal);?>,
        isMulti = manage_multiple_resource && getExternal == 2,
        holidays = <?php echo json_encode($holidays) ?>,
        activities = mapeds['activity'], tasks = mapeds['task'],
        managerHour = <?php echo json_encode($managerHour) ?>,
        sendTimesheetPartially = <?php echo json_encode($sendTimesheetPartially);?>,
        fillMoreThanCapacity = <?php echo json_encode($fillMoreThanCapacity);?>,
        limitedToCapacityWeek = <?php echo json_encode($limited_to_the_capacity_of_week);?>,
        showActivityForecastComment = <?php echo $showActivityForecastComment;?>,
		avg_date = <?php echo json_encode($avg); ?>,
        gHour = <?php echo json_encode($gHour);?>,
        gMinute = <?php echo json_encode($gMinute);?>;
        ratio = <?php echo json_encode($ratio);?>;
		canSend = <?php echo json_encode($can_send); ?>;
		select_project_without_task = <?php echo json_encode($select_project_without_task); ?>;
		has_comment = false;
	var screenSettings = <?php echo json_encode($screenSettings) ?>;
	var listTaskDisplay = <?php echo json_encode($listTaskDisplay);?>;
	var listActivityDisplay = <?php echo json_encode($listActivityDisplay);?>;
	var listActi = <?php echo json_encode($lisActivityRequests);?>;
	var listTask = <?php echo json_encode($lisTaskRequests);?>;
	var dataSets = <?php echo json_encode($dataView); ?>,
		holidays = <?php echo json_encode(@$holidays); ?> || {},
		employees = <?php echo json_encode($employees); ?>;
		profit_name = <?php echo json_encode($profit_name); ?>;
    function activityNameSorter(a, b){
        var strA = '', strB = '';
        //holiday
        if( a.type == 'hl' ){
            //asc = true
            if( screenSettings.sort == 'true' || screenSettings.sort == true ){
                return -1;
            } else {
                return 1;
            }
        }
        if( b.type == 'hl' ){
            //asc = true
            if( screenSettings.sort == 'true' || screenSettings.sort == true ){
                return 1;
            } else {
                return -1;
            }
        }
        //absence
        if( a.type == 'ab' ){
            //asc = true
            if( screenSettings.sort == 'true' || screenSettings.sort == true ){
                return -1;
            } else {
                return 1;
            }
        }
        if( b.type == 'ab' ){
            //asc = true
            if( screenSettings.sort == 'true' || screenSettings.sort == true ){
                return 1;
            } else {
                return -1;
            }
        }
        if( a.activity != '0' ){
            try {
                strA = activities[a.activity].short_name;
            } catch(ex){
                strA = '';
            }
        } else {
            try {
                task = tasks[a.task_id];
                var name = task.name.split('/');
                name = name[name.length-1];
                strA = activities[task.activity_id].short_name + ' ' + name;
            } catch(ex){
                strA = '';
            }
        }
        if( b.activity != '0' ){
            try {
                strB = activities[b.activity].short_name;
            } catch(ex){
                strB = '';
            }
        } else {
            try {
                task = tasks[b.task_id];
                var name = task.name.split('/');
                name = name[name.length-1];
                strB = activities[task.activity_id].short_name + ' ' + name;
            } catch(ex){
                strB = '';
            }
        }
        return alphabetSorter(strA, strB);
    }
    function alphabetSorter(x, y){
        var x = x.toLowerCase(), y = y.toLowerCase();
        return (x == y ? 0 : (x > y ? 1 : -1));
    }
    var DataValidation = {}, gridControl, typeSelect = <?php echo json_encode($typeSelect);?>, requestConfirm = <?php echo json_encode($requestConfirm);?>;
	function build_list_fields(){
		$this.fields = {
			id          : {defaulValue : 0},
			last        : {defaulValue : 0},
			activity    : {defaulValue : '', allowEmpty : false},
			task_id     : {defaulValue : ''}
		};
		if(listWorkingDays){
			$.each(listWorkingDays, function(ind, val){
				$this.fields[ind] = {defaulValue : '', required : ['activity']};
			});
		}
	}
    $(document).ready(function () {
        /**
         * Cac function viet them.
         */
        function GetObjectKeyIndex(obj, keyToFind) {
            var i = 0, key;
            for (key in obj) {
                if (key == keyToFind) {
                    return i;
                }
                i++;
            }
            return null;
        };
        function GetObjectValueIndex(obj, keyToFind) {
            var i = 0, key;
            for (key in obj) {
                var val = obj[key] ? obj[key] : 0;
                if (val == keyToFind) {
                    return i;
                }
                i++;
            }
            return null;
        };
        /**
         * Cac doan code
         */
        /**
         * Phan Slick Grid cho phan timesheet
         */
        $(document).mousemove(function(event) {
            currentMousePos.x = event.pageX;
            currentMousePos.y = event.pageY;
        });
        $this.i18n = <?php echo json_encode($i18ns); ?>;
        $this.canModified =  <?php echo json_encode($requestConfirm == -1 || $requestConfirm == 1); ?>;
        if(typeSelect === 'year'){
            $this.canModified = false;
        }
        // Formatters...
        $.extend(Slick.Formatters,{
            Action : function(row, cell, value, columnDef, dataContext){
                if(dataContext.activity != 0){ // is activity
                    if(activityNotAccessDeletes[dataContext.activity]){
                        return '';
                    }
                } else { // is task
                    if(taskNotAccessDeletes[dataContext.task_id]){
                        return '';
                    }
                }
                if(dataContext.type == 'ab' || dataContext.readonly || dataContext.type == 'hl'){
                    return '';
                }
                var nameDelete = '';
                if(dataContext.activity && dataContext.activity != 0){
                    nameDelete = (activities[dataContext.activity] || {}).name || $this.t('Unknown');
                } else {
                    nameDelete = (tasks[dataContext.task_id] || {}).name || $this.t('Unknown');
                }
                return Slick.Formatters.HTMLData(row, cell, $this.t(actionTemplate, dataContext.activity,
                nameDelete, dataContext.task_id || -1), columnDef, dataContext);
            },
            Activity : function(row, cell, value, columnDef, dataContext){
                if(dataContext.type == 'ac'){
                    if(activities[value]){
						name_display = (activities[value].long_name) ? activities[value].long_name : activities[value].short_name;
                        if(dataContext.task_id && activities[value].tasks[dataContext.task_id]){
                            var _taskName = activities[value].tasks[dataContext.task_id] ? activities[value].tasks[dataContext.task_id].name : '';
                            _taskName = _taskName ? '<i>' + _taskName + '</i>' : _taskName;
                            value = '<div class="activity-tootip" rel="' + value + '" task="' + dataContext.task_id + '">' + name_display + _taskName + '</div>';
                        } else {
                            value = '<div class="activity-tootip" rel="' + value + '">' + activities[value].short_name + '</div>';
                        }
                    } else {
                        if(dataContext.task_id) {
                            var task_id = dataContext.task_id;
                            if(tasks[task_id]){
                                var task = tasks[task_id];
                                if(task.activity_id){
                                    var activity_id = task.activity_id;
                                    if(activities[activity_id]){
										name_display = (activities[activity_id].long_name) ? activities[activity_id].long_name : activities[activity_id].short_name;
                                        var _taskName = activities[activity_id].tasks[dataContext.task_id] ? activities[activity_id].tasks[dataContext.task_id].name : '';
                                        _taskName = _taskName ? '<i>' + _taskName + '</i>' : _taskName;
                                        value = '<div class="activity-tootip" rel="' + activity_id + '" task="' + dataContext.task_id + '">' + name_display + _taskName +'</div>';
                                    } else {
                                        value = $this.t('Unknown');
                                    }
                                }
                            }
                        }
                    }
                } else {
                    value = absences[value] ? absences[value].print : $this.t('Unknown');
                }
                return Slick.Formatters.HTMLData(row, cell, value , columnDef, dataContext);
            },
            Capacity : function(row, cell, value, columnDef, dataContext){
                if(managerHour){
                    value = 0;
                    $.each(listWorkingDays , function(ind, val){
                        if(dataContext[ind]){
                            var valueHour = dataContext[ind];
                            valueHour = valueHour.split(':');
                            valueHour = parseInt(valueHour[0]) * 60 + parseInt(valueHour[1]);
                            value += parseFloat(valueHour);
                        }
                    });
                    var m = value%60;
                    var h = (value - m)/60;
                    h = (h < 10) ? '0'+h : h;
                    m = (m < 10) ? '0'+m : m;
                    dataContext.capacity = h + ':' + m;
                } else {
                    value = 0;
                    $.each(listWorkingDays , function(ind, val){
                        if(dataContext[ind]){
                            value += parseFloat(dataContext[ind]);
                        }
                    });
                    dataContext.capacity =  Number(value).toFixed(2);
                }

                return Slick.Formatters.HTMLData(row, cell, dataContext.capacity , columnDef, dataContext);
            },
            forecastValue : function(row, cell, value, columnDef, dataContext){
                var day = columnDef.field * 1000;
                day = new Date(day);
                var _class = 'cell-value ';
                if(daysInWeek[day.getDay()] && daysInWeek[day.getDay()] == 'monday' && typeSelect != 'week'){
                    _class += 'separator-week';
                }
                if(dataContext.activity && dataContext.activity != 0){ // activity
                    if(checkFullDayActivities && checkFullDayActivities[dataContext.activity] && (checkFullDayActivities[dataContext.activity][columnDef.field] == 0 || checkFullDayActivities[dataContext.activity][columnDef.field] == 2)){
                        _class += ' disable-edit-day';
                    }
                } else {
                    if(checkFullDayTasks && checkFullDayTasks[dataContext.task_id] && (checkFullDayTasks[dataContext.task_id][columnDef.field] == 0 || checkFullDayTasks[dataContext.task_id][columnDef.field] == 2)){
                        _class += ' disable-edit-day';
                    }
                }
                if(checkFullDays && checkFullDays[columnDef.field]){
                    _class += ' disable-edit-day';
                }
                value = value ? value : '';
                return Slick.Formatters.HTMLData(row, cell, '<span data-field = "'+ columnDef.field +'" class="' + _class + '">' + value + '</span>', columnDef, dataContext);
            }
        });
        // Editors...
        $.extend(Slick.Editors,{
            forecastValue : function(args){
                if(managerHour){
                    $.extend(this, new BaseSlickEditor(args));
                    this.input = $("<input type='text' placeholder='hh:mm' />")
                    .appendTo(args.container).attr('rel','no-history').addClass('editor-text');
                    this.input.inputmask('hh:mm');
                } else {
                     $.extend(this, new Slick.Editors.textBox(args));
                        this.input.attr('maxlength' , 7).keypress(function(e){
                        var key = e.keyCode ? e.keyCode : e.which;
                        if(!key || key == 8 || key == 13 || e.ctrlKey || e.shiftKey){return;}
                        var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
                        var _role = <?php echo json_encode($role);?>;
                        if(_role == 'conslt'){
                            var _val = parseFloat(val, 10);
                            if(!(val != '0' && val == '+') && (!/^[\-+]?([0-9]{1}|[1-9][0-9]{1,2})(\.[0-9]{0,2})?$/.test(val) || !(_val >= 0 && _val <= 365))){
                                e.preventDefault();
                                return false;
                            }
                        } else {
                            var _val = parseFloat(val, 10);
                            if(!(val != '0' && val == '-' || val == '+') && (!/^[\-+]?([0-9]{1}|[1-9][0-9]{1,2})(\.[0-9]{0,2})?$/.test(val) || !(_val >= -365 && _val <= 365))){
                                e.preventDefault();
                                return false;
                            }
                        }
                    });
                }
                this.focus();
            },
            selectBox2 : function(args){
                $.extend(this, new Slick.Editors.selectBox(args));
                this.div = $('<div class="request-selectbox" />');
                var destroy = this.destroy;
                this.destroy = function () {
                    destroy.apply(this, $.makeArray(arguments));
                    this.div.remove();
                };
                var serializeValue = this.serializeValue;
                this.serializeValue = function(){
                    if(!this.isCreated){
                        this.div.html(this.input.find(':selected').text() || '');
                        // setTimeout(function(){
                        //     $('#activity-request').trigger({
                        //         type: 'contextmenu',
                        //         which: 3,
                        //         pageX : currentMousePos.x,
                        //         pageY : currentMousePos.y
                        //     });
                        // } , 100);
                        this.isCreated = true;
                    }
                    return serializeValue.apply(this,$.makeArray(arguments));
                };
                this.input.hide();
                $(args.container).append(this.div);
            },
            activityLabel: function(args){
                $.extend(this, new Slick.Editors.SlickLabel(args));
                this.div = $('<div class="request-selectbox" />');
                var destroy = this.destroy;
                this.destroy = function () {
                    destroy.apply(this, $.makeArray(arguments));
                    this.div.remove();
                };
                var serializeValue = this.serializeValue;
                this.serializeValue = function(){
                    if(!this.isCreated){
                        this.input.hide();
                        this.div.html('--');
                        // setTimeout(function(){
                        //     $('#activity-request').trigger({
                        //         type: 'contextmenu',
                        //         which: 3,
                        //         pageX: currentMousePos.x,
                        //         pageY: currentMousePos.y
                        //     });
                        // } , 100);
                        this.isCreated = true;
                    }
                    return serializeValue.apply(this,$.makeArray(arguments));
                };
                $(args.container).append(this.div);
            }
        });
        // SlickGrid Validate in timesheet
        DataValidation.forecastValue = function(value, args){
			if(limitedToCapacityWeek){
				var _valid= true, _msg = '';
				var newCapacity = new BigNumber(value);
				var _remain = new BigNumber(capacity['capacity']);
				var _col_field = args.column.field, 
					_task_id = args.item.id; 
				
				$.each( args.grid.getData().getItems(), function(i, _row){
					$.each( _row, function( k, v){
						if( $.isNumeric(k) && (( _row.id != _task_id ) || ( k != _col_field))){
							newCapacity = newCapacity.plus(v);
							_remain = _remain.minus(v);
						}
					});
				});
				if(newCapacity > capacity['capacity']){
					_valid = false;
					_msg = _remain > 0 ? $this.t('The value must between %1$s and %2$s.' , '0', _remain.toFormat(2)) : $this.t('No more value is allowed');
				}
				$('#submit-request-all-top').toggleClass('can-send', ( canSend && (newCapacity == capacity['capacity'] )));
				return {
					valid : _valid || (value == 0),
					message : _msg
				};
			}
            if(managerHour){
                value = value.split(':');
                value = parseInt(value[0]) * 60 + parseInt(value[1]);
                var _value = parseInt(value);
            } else {
                var _value = parseFloat(value);
            }
            var value = 0;
            var _checkAbAndHl = true;
            var nameOffDay = '';
            $.each(args.grid.getData().getItems() , function(){
                if(this[args.column.field] && (!args.item || this.id != args.item.id)){
                    if(managerHour){
                        var valueHour = this[args.column.field];
                        valueHour = valueHour.split(':');
                        valueHour = parseInt(valueHour[0]) * 60 + parseInt(valueHour[1]);
                        value += parseFloat(valueHour);
                    } else {
                        value += parseFloat(this[args.column.field]);
                    }
                }
                if(managerHour){
                    var totalHourOfDay = gHour + ':' + gMinute;
                    if(this && (this.type == 'ab' || this.type == 'chAb' || this.type == 'hl') && this[args.column.field] === totalHourOfDay){
                        nameOffDay = this.type;
                        _checkAbAndHl = false;
                    }
                } else {
                    if(this && (this.type == 'ab' || this.type == 'chAb' || this.type == 'hl') && this[args.column.field] == 1*ratio){
                        nameOffDay = this.type;
                        _checkAbAndHl = false;
                    }
                }

            });
            otherVal = value;
            value += _value;
            if(managerHour){
                var totalVal = capacity[args.column.field];
                totalVal = totalVal.split(':');
                totalVal = parseInt(totalVal[0]) * 60 + parseInt(totalVal[1]);
                var _valid = value <= parseInt(totalVal);
                var rangeHour = totalVal - otherVal;
                var rangeMinute = rangeHour%60;
                rangeHour = (rangeHour - rangeMinute)/60;
                rangeHour = (rangeHour < 10) ? '0'+rangeHour : rangeHour;
                rangeMinute = (rangeMinute < 10) ? '0'+rangeMinute : rangeMinute;
                var _msg = parseFloat(totalVal - otherVal) == 0 ? $this.t('No more value is allowed') : $this.t('The value must between %1$s and %2$s.' , '00:00', rangeHour + ':' + rangeMinute);
            } else {
                value = parseFloat(value.toFixed(2));
                var _valid = value <= capacity[args.column.field];
                var _msg = parseFloat((capacity[args.column.field] - otherVal).toFixed(2)) == 0 ? $this.t('No more value is allowed') : $this.t('The value must between %1$s and %2$s.' , 0 , (capacity[args.column.field] - otherVal).toFixed(2));
            }
            if(manage_multiple_resource && getExternal == 2){
                _valid = true;
            }
            if(fillMoreThanCapacity){
				if(showActivityForecastComment && (value > 1)){
					date_column = args.column.format_date;
					getCommentRequest('date', args.column.field, date_column, args.item.id, args.column.field);
					if(!has_comment){
						has_comment = false;
						return {
							_valid : false,
							message : '',
						};
					}
				}
                _valid = true;
				_checkAbAndHl = true;
            }
			has_comment = false;
			if(_checkAbAndHl == true){
				return {
					valid : _valid,
					message : _msg
				};
			} else {
				var _nameOffDay = '';
				if(nameOffDay == 'ab'){
					_nameOffDay = 'Absence';
				} else if(nameOffDay == 'hl'){
					_nameOffDay = 'Holiday';
				}
				return {
					valid : false,
					message : $this.t(_nameOffDay)
				};
			}
        };
        // du lieu cho forecast
        // var activityFromTasks = < ?php echo json_encode($activityFromTasks); ?>,
            // projectFromTasks = < ?php echo json_encode($projectFromTasks); ?>,
            // workloadOfEmployees = < ?php echo json_encode($workloadOfEmployees);?>;
        // tootip cho activity va task
        var tooltipTemplate = $('#tooltip-template').html();
        $(document).on('mouseenter', 'div.activity-tootip', function(ev){
            var me = this;
            $(this).qtip({
                overwrite: false,
                show: {
                    solo: true,
                    event: ev.type, // Use the same event type as above
                    ready: true // Show immediately - important!
                },
                content: {
                    text: function(e, api){
                        var $el         = $(me),
                            dx          = activities[$el.attr('rel')],
                            task        = $el.attr('task'),
                            taskName    = dx.tasks[task] ? dx.tasks[task].name : '';
                        var content = $this.t(
                            tooltipTemplate,
                            dx.name ,
                            dx.short_name,
                            dx.long_name,
                            (mapeds['family'][dx.family_id] || {name : ''}).name,
                            (mapeds['subfamily'][dx.subfamily_id] || {name : ''}).name,
                            taskName
                        );
                        if(taskName){
                            content = content.replace('non-actask', 'has-actask');
                        }
                        return content;
                    }
                },
                position: {
                    adjust: {
                        y: 0
                    },
                    my: 'bottom center',
                    at: 'top center'
                },
                style: {
                    classes: 'qtip-shadow qtip-xlight'
                }
            });
        });
		$('body').on('click', '.handle-overlay', function(){
			parent = $(this).closest('.grid-canvas');
			input = parent.find('.editable input.editor-text');
			input.select();
			document.execCommand("copy");
			if(input.length > 0){
				input.trigger('change');
				parent.trigger('click');
				rowEdit = gridControl.getSelectedRows();
				cell = gridControl.getActiveCell().cell;
				var el = gridControl.gotoCell(rowEdit, cell + 1, true);
				$('html').trigger('click', [el]);
			}
		});
        // Khai bao cac bien
        var index = 0, notSelectTasks = {}, notSelectActivities = {}, taskSelected = {};
        var onAfterSave = function(){
            if(managerHour){
                var val = 0;
                $.each(gridControl.getData().getItems() , function(ind, dx){
                    if(this.id && !this.notsaved){
                        $.each(dx, function(key, vl){
                            if( $.isNumeric(key) ){
                                vl = vl.split(':');
                                vl = parseInt(vl[0]) * 60 + parseInt(vl[1]);
                                val += Number(vl);
                            }
                        });
                    }
                });
                var _t = capacity.capacity;
                _t = _t.split(':');
                _t = parseInt(_t[0]) * 60 + parseInt(_t[1]);
                $('#form-request-all').data('capacity' , {
                    value : val,
                    total : _t
                });
            } else {
                var val = 0;
                $.each(gridControl.getData().getItems() , function(ind, dx){
                    if(this.id && !this.notsaved){
                        $.each(dx, function(key, vl){
                            if( $.isNumeric(key) ){
                                val += Number(vl);
                            }
                        });
                    }
                });
                val = Number(val.toFixed(2));
                $('#form-request-all').data('capacity' , {
                    value : val,
                    total : capacity.capacity
                });
            }

        };
        var onCellChange = function(args){
            if(args){
                $.each(listWorkingDays, function(ind, val){
                    if(managerHour){
                        if(args.item[ind] == ''){
                            args.item[ind] = '00:00';
                        }
                    } else {
                        if(args.item[ind] == ''){
                            args.item[ind] = '0';
                        }
                    }
                });
                if( !args.stopMove ){
                    if( args.column.field != 'activity' ){
                        var columns = args.grid.getColumns(),
                            col, cell = args.cell;
                        do {
                            cell++;
                            if( columns.length == cell )break;
                            col = columns[cell];
                        } while (typeof col.editor == 'undefined');

                        // if( cell < columns.length ){
                            // args.grid.gotoCell(args.row, cell, true);
                        // }
                    }
                }
            }
            if(managerHour){
                var dt = {capacity: 0};
                $.each(gridControl.getData().getItems() , function(index, dx){
                    $.each(dx, function(key, val){
                        if( key != 'capacity' && typeof capacity[key] !== 'undefined' ){
                            val = val.split(':');
                            val = parseInt(val[0]) * 60 + parseInt(val[1]);
                            dt[key] = dt[key] || 0;
                            dt[key] += Number(val);
                            dt.capacity += Number(val);
                        }
                    });
                });
            } else {
                var dt = {capacity: 0};
                $.each(gridControl.getData().getItems() , function(index, dx){
                    $.each(dx, function(key, val){
                        if( key != 'capacity' && typeof capacity[key] !== 'undefined' ){
                            dt[key] = dt[key] || 0;
                            dt[key] += Number(val);
                            dt.capacity += Number(val);
                        }
                    });
                });
            }
            args && $.extend(args.item, taskSelected);

            if(managerHour){
                $.each(dt , function(id){
                    var vl = Number(this);
                    var m = vl%60;
                    var h = (vl - m)/60;
                    h = (h < 10) ? '0'+h : h;
                    m = (m < 10) ? '0'+m : m;
                    $(gridControl.getHeaderRowColumn(id)).find('.activity-capacity b').html(h + ':' + m);
                });
            } else {
                $.each(dt , function(id){
                    var vl = Number(this).toFixed(2);
                    $(gridControl.getHeaderRowColumn(id)).find('.activity-capacity b').html(vl);
                });
            }
            $('.separator-week').parent().addClass('separator-week-div');
            $('.disable-edit-day').parent().addClass('disable-edit-day-div');
        };

        // Cac phuong thuc cua slick grid
        $this.onApplyValue = function(item){
            item.type = 'ac';
            item.id = 'act-' + (index++);
            item.notsaved = true;
        };
        $this.onCellChange = $this.onAddNewRow = onCellChange;
        $this.onAfterSave =  function(result,args){
            if(result){
                if(args.item && args.item.notsaved){
                    args.item.notsaved = false;
                }
            } else {
                args.item.notsaved = true;
            }
            onAfterSave();
            return true;
        };
        $this.onBeforeSave = function(args){
            var result = false;
            $.each(listWorkingDays , function(ind, val){
                if(args.item[ind]){
                    result = true;
                    return false;
                }
            });
            return result;
        };
        $this.onBeforeEdit = function(args){
            rowEdit = args.row;
            taskSelected = {};
            if(args.item && args.item.type == 'ab' || $.type(capacity[args.column.field]) != 'undefined' && capacity[args.column.field] == 0){
                return false;
            }
            if(args.item){
                if(args.item.activity && args.item.activity != 0){ //activity
                    if(checkFullDayActivities && checkFullDayActivities[args.item.activity] && (checkFullDayActivities[args.item.activity][args.column.field] == 0 || checkFullDayActivities[args.item.activity][args.column.field] == 2)){
                        return false;
                    }
                } else { // task
                    if(checkFullDayTasks && checkFullDayTasks[args.item.task_id] && (checkFullDayTasks[args.item.task_id][args.column.field] == 0 || checkFullDayTasks[args.item.task_id][args.column.field] == 2)){
                        return false;
                    }
                }
                if(checkFullDays && checkFullDays[args.column.field]){
                    return false;
                }
            }
            if(args.column.field == 'activity'){
                if(args.item && args.item.readonly){
                    return false;
                }
                notSelectTasks = {};
                notSelectActivities = {};
                $.each(args.grid.getData().getItems() , function(){
                    if(this.type == 'ac' && this.activity && (!args.item || this.id != args.item.id)){
                        if(!this.task_id){
                            notSelectActivities[this.activity] = this.activity;
                        } else {
                            notSelectTasks[this.task_id] = true;
                        }
                    }
                });
            }
            return true;
        };
        $this.selectMaps.activity = {};
        var  gridView = <?php echo json_encode($gridView); ?>;
        var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator', 'sorter')); ?>;
		build_list_fields();
        $this.url =  '<?php echo $html->url(array('action' => 'update_request')) . $queryUpdate; ?>';
        gridControl = $this.init($('#activity-request'),gridView,columns, {frozenColumn: 2});

        var dataView = gridControl.getDataView();
		/* Change send button to green */
		var newCapacity = new BigNumber(0);
		$.each( gridControl.getData().getItems(), function(i, _row){
			$.each( _row, function( k, v){
				if( $.isNumeric(k)){
					newCapacity = newCapacity.plus(v);
				}
			});
		});
		$('#submit-request-all-top').toggleClass('can-send', ( canSend && (newCapacity == capacity['capacity'] )));
		/* END Change send button to green */
        var rowEdit = 0;

        // fill down plugin

        var overlayPlugin = new Ext.Plugins.Overlays({
            decoratorWidth: 1,
            horizontal: true,
            vertical: false,
            beforeShowHandle: function(activeCell){
                // is holiday or absence row
                var row = $(this.getCellNode(activeCell.row, activeCell.cell)).closest('.slick-row');
                if( row.hasClass('disabled') )return false;
                // has holiday or absence column
                var item = dataView.getItem(activeCell.row);
				var total;
                if(item){
					total = getTotal(item.id, activeCell.cell, activeCell.cell);
					if( total[activeCell.cell] == 'off' ){
						return false;
					}
					// no data to fill
					if( isNaN(parseFloat(item[this.getColumns()[activeCell.cell].field])) ){
						return false;
					}
				}
                return true;
            }
        });

        overlayPlugin.onFillUpDown.subscribe(function (e, args) {
            var columns = gridControl.getColumns();
            var column = columns[args.origin.cell];

            if (!column.fillable) {
                return;
            }
            var origin = dataView.getItem(args.origin.row);
            var value = origin[column.field];
            if( isNaN(value) )value = 0;

            dataView.beginUpdate();

            var count = 0;
            var total = getTotal(origin.id, args.range.fromCell, args.range.toCell);

            for (var i = args.range.fromRow; i <= args.range.toRow; i++) {
                var item = dataView.getItem(i);
                for( var j = args.range.fromCell; j <= args.range.toCell; j++){
                    var col = columns[j],
                        newValue = value;
                    // validate here
                    if( !isMulti ){
                        if( total[j] == 'off' ){
                            continue;
                        } else if( typeof total[j] == 'undefined' ){
                            total[j] = 0;
                        }
                        var remain = 1*ratio - total[j];
                        newValue = fillMoreThanCapacity ? value : parseFloat(Math.min(value, remain)).toFixed(2);
                    }
                    // myself
                    if( col == column )continue;
                    // update
                    item[col.field] = newValue;
                    dataView.updateItem(item.id, item);
                    // end validate
                    count++;
                }
                gridControl.invalidateRow(i);
            }

            dataView.endUpdate();
            gridControl.render();
            // call save

            if( count ){
                gridControl.onCellChange.notify({
                    cell: args.origin.cell,
                    row: args.origin.row,
                    item: origin,
                    column: column,
                    grid: gridControl,
                    stopMove: true
                });
            }
        });

        function getTotal(forId, fromCell, toCell){
            var items = dataView.getItems(),
                result = {},
                columns = gridControl.getColumns();
            for(var i = 0; i < items.length; i++){
                var item = items[i];
                if( item.id == forId )continue;
                for(var j = fromCell; j <= toCell; j++){
                    var field = columns[j].field,
                        value = item[field];
                    if( !result[j] ){
                        result[j] = 0;
                    }
                    // check ab, hl
                    if( value ){
                        value = parseFloat(value);
                        if( ( item.type == 'hl' || item.type == 'ab' ) && value == 1*ratio ){
                            result[j] = 'off';
                        } else if( result[j] != 'off' ) {
                            result[j] += value;
                        }
                    }
                }
            }
            return result;
        }
        gridControl.registerPlugin(overlayPlugin);

        /* btn menu */
        gridControl.setSelectionModel(new Slick.RowSelectionModel());
        var dataView = gridControl.getDataView();
        var rowEdit = 0;

		/* Old: Open menu 
		 * New: add new task
        $('#open-menu-activity').click(function(){
            if( !$this.canModified )return false;

            rowEdit = gridControl.getSelectedRows();
            var item = dataView.getItem(rowEdit);
                cell = 1;

            if( item && (item.type == 'ab' || item.type == 'chAb') )return false;

            var el = gridControl.getCellNode(rowEdit, cell);
            // gridControl.gotoCell(rowEdit, cell, true);

            $('html').trigger('click', [el]);
            return false;
        });
		*/
		$('#open-menu-activity').click(function(){
            if( !$this.canModified )return false;
			lastRow = gridControl.getDataLength();
			rowSelected = gridControl.getSelectedRows(); // edit
			rowSelected = rowSelected[0];
			if( !rowSelected ) rowSelected = gridControl.getDataLength(); //add new
			activityColumn = gridControl.getColumnIndex('activity');
			gridControl.gotoCell(rowSelected, activityColumn, true);
			var el = gridControl.getCellNode(rowSelected, activityColumn);
			setTimeout( function(){
				$('html').trigger('click', [el]);
			}, 50);
			return false;
		});

        var _timer;

        gridSorter(gridControl.getColumns()[1], screenSettings.sort == 'true', true);

        function gridSorter(column, isAsc, noSave) {
            screenSettings.sort = isAsc;
            //gridControl.setSortColumn(column.id, isAsc);
            dataView.sort(column.sorter, isAsc);
            gridControl.invalidate();
            gridControl.render();
            if( !noSave ){
                clearTimeout(_timer);
                _timer = setTimeout(storeSettings, 750);
            }
        }

        function storeSettings(){
            $.ajax({
                url: "/history_filters/saveSettings",
                type: 'post',
                dataType : 'json',
                data:{
                    data:{
                        store: screenSettings,
                        path:'activity_forecasts/request'
                    }

                }
            });
        }

        gridControl.onSort.subscribe(function(e, args){
            if( args.sortCol.sorter ){
                //dataView.sort(args.sortCol.sorter, args.sortAsc);
                gridSorter(args.sortCol, args.sortAsc);
            }
        });
        //

        //hide if sent
        if( !$this.canModified ){
            $('#open-menu-activity').hide();
        }
        /**
         * Tinh toan lai phan width cua table timesheet
         */

        //alert($("#absence").width());

        //var tableWidth = 27;
        function fixCols(load){
            var widthEmploy = $('#foreEmploy').width();
            var widthCapacity = $('#foreCapacity').width();

            //var widthMon= $('.foreDayCols');
            var widthAction= $('#foreAction').width();
            $('.slick-headerrow-columns').width($('#absence').width());
            $('.slick-headerrow').width($('#absence').width('#absence'));
            if(widthEmploy){
                var cols = gridControl.getColumns();
                var _lengthCols = cols.length;
                cols[0].width = 32;
                cols[1].width = widthEmploy-21;
                cols[2].width = widthCapacity+12;
                if($.browser.mozilla == true)
                {
                    var wt1=11;
                    var wt2=8;
                    var wt3=7;
                }
                else
                {
                    var wt1=12;
                    var wt2=9;
                    var wt3=8;
                }
                //weekday
                for(var i = 3, j = 0; i < _lengthCols; i++, j++){
                    if(cols[i]){
                        var tw = $('.col-'+i).width();
                        cols[i].width = tw + wt1;
                    }
                }
                //action col
                if(cols[_lengthCols-1].id === 'action.'){
                    if($('.grid-canvas-right').height()>369)
                    {
                        cols[_lengthCols-1].width = widthAction-8;
                    }
                    else
                    {
                        cols[_lengthCols-1].width = widthAction+wt1;
                    }
                }
                // else
                // {
                //     if($('.grid-canvas-right').height()>369)
                //     {
                //         cols[_lengthCols-1].width = widthMon-7;
                //     }
                // }
                if(load == 1){
                    $('#foreEmploy').height($('.foreDayCols').height() + 5);
                }
                gridControl.setColumns(cols);
                $(".slick-viewport-right").scrollTop(100);
                //$(".slick-viewport-right").scrollTop(0);
                var temp=setInterval(function(){
                    $(window).resize();
                    $("#scrollTopAbsenceContent").width($("#absence").width());
                    $("#scrollTopAbsence").width($("#absence-scroll").width());
                    clearInterval(temp);
                },100);
            }
        }

        fixCols(1);
        // resize col activity
        var pressed = false;
        var start = $("#foreEmploy");
        var startX, startWidth;
        var checkResize = false;
        // set size activity if change before
        function resizeHandler(noSave){
            screenSettings.feWidth = start.width();
            var cols = gridControl.getColumns();
            var l0w = $(".l0").width();
            cols[1].width = screenSettings.feWidth - l0w;
            gridControl.setColumns(cols);
            fixCols(2);
            if( !noSave ){
                clearTimeout(_timer);
                _timer = setTimeout(storeSettings, 750);
            }
        }
        if(screenSettings.feWidth != 0){
            start.width(screenSettings.feWidth);
            resizeHandler(1);
        }
        $("table .borderEmploy").mousedown(function(e) {
            pressed = true;
            startX = e.pageX;
            startWidth = start.width();
            fixCols(2);
        });
        $(document).mousemove(function(e) {
            if(pressed) {
                $(start).width(startWidth+(e.pageX-startX));
            }
        });
        $(document).mouseup(function(e, d) {
            if(pressed) {
                pressed = false;
                resizeHandler(d);
            }
        });
        //sort activity
        $("#foreEmploy").click(function (e){
            if(e.target == this||$(e.target).hasClass('actisort')){
                if($(this).children('span').hasClass('slick-sort-indicator-asc')){
                    $(this).children('span').attr('class', "slick-sort-indicator slick-sort-indicator-desc actisort");
                }else{
                    $(this).children('span').attr('class', "slick-sort-indicator slick-sort-indicator-asc actisort");
                }
               $(".slick-header-columns .slick-header-column:nth-child(2)").trigger('click');
           }
        });

        $(window).resize(function() {
            var _lWidths = $(window).width();
            var _widthPopups = Math.round((95*_lWidths)/100);
            $('#dialog_copy_forecasts').parent().css('width', _widthPopups + 'px');
            //_widthPopups = Math.round((90*_widthPopups)/100);
            $('#reForecasts').css('width', _widthPopups + 'px');

            var temp=setInterval(function(){
                var absenceWidht=$("#absence-scroll").width();
                $(".slick-pane-right").width(absenceWidht);
                $(".slick-viewport-right").width(absenceWidht);
                $(".slick-viewport-right").scrollTop(100);
                $(".slick-headerrow").width(absenceWidht);

                $("#absence-request").width(absenceWidht+$(".slick-viewport-left").width());
                $("#activity-request").width(absenceWidht+$(".slick-viewport-left").width());
                $(".slick-viewport-right").scrollTop(0);
                clearInterval(temp);
            },1000);
            var widthBrowser = $(window).width();
            widthBrowser = ((widthBrowser*85)/100).toFixed(0);
            widthBrowser = (widthBrowser/4).toFixed(1);
            // var leftMenuTwo = parseFloat(widthBrowser)-20;
            var leftMenuTwo = parseFloat(widthBrowser);
            // var leftMenuThree = parseFloat(widthBrowser) + parseFloat(leftMenuTwo) - 20;
            var leftMenuThree = parseFloat(widthBrowser) + parseFloat(leftMenuTwo);
            var leftMenuFour = parseFloat(leftMenuTwo) + parseFloat(leftMenuThree);
            $('.menu-level-1, .menu-level-2, .menu-level-3, .menu-level-3-4').css('width', widthBrowser+'px');
            $('.menu-level-2').css('left', leftMenuTwo+'px');
            $('.menu-level-3').css('left', leftMenuThree+'px');
            $('.menu-level-3-4').css('left', leftMenuFour+'px');
            $('#menuContext, .menu-level-2, .menu-level-3, .menu-level-3-4').hide();
            // gridControl.gotoCell(gridView.length, 2, true);
            // resize timesheet
            widthEmploy = $('#foreEmploy').width();
            widthCapacity = $('#foreCapacity').width();

            widthMon= $('.foreDayCols').width();
            widthAction= $('#foreAction').width();
            if(widthEmploy){
                var cols = gridControl.getColumns();
                var _lengthCols = cols.length;
                if($.browser.mozilla == true)
                {
                    var wt1=11;
                    var wt2=8;
                    var wt3=7;
                }
                else
                {
                    var wt1=12;
                    var wt2=9;
                    var wt3=8;
                }
                cols[0].width = 32;
                cols[1].width = widthEmploy-21;
                cols[2].width = widthCapacity+wt1;
                for(i = 3; i < _lengthCols; i++){
                    if(cols[i]){
                        var tw = $('.col-'+i).width();
                        cols[i].width = tw + wt1;
                    }
                }

                if(cols[_lengthCols-1] && cols[_lengthCols-1].id === 'action.'){
                    if($('.grid-canvas-right').height()>369)
                        cols[_lengthCols-1].width = widthAction-8;
                    else
                        cols[_lengthCols-1].width = widthAction+wt1;
                }
                else
                {
                    if($('.grid-canvas-right').height()>369)
                    {
                        cols[_lengthCols-1].width = widthMon-7;
                    }
                }
                gridControl.setColumns(cols);
            }
            //$('#foreEmploy').height($('.foreDayCols').height() + 5);
            $("#scrollTopAbsenceContent").width($("#absence").width());
            $("#scrollTopAbsence").width($("#absence-scroll").width());

            if(managerHour){
                var dt = {capacity: 0};
                $.each(capacity, function(a, b){
                    dt[a] = 0;
                });
                $.each(gridControl.getData().getItems(), function(index, dx){
                    $.each(dx, function(key, val){
                        if( key != 'capacity' && typeof capacity[key] !== 'undefined' ){
                            val = val.split(':');
                            val = parseInt(val[0]) * 60 + parseInt(val[1]);
                            dt[key] = dt[key] || 0;
                            dt[key] += Number(val);
                            dt.capacity += Number(val);
                        }
                    });
                });
            } else {
                var dt = {capacity: 0};
                $.each(capacity, function(a, b){
                    dt[a] = 0;
                });
                $.each(gridControl.getData().getItems(), function(index, dx){
                    $.each(dx, function(key, val){
                        if( key != 'capacity' && typeof capacity[key] !== 'undefined' ){
                            dt[key] = dt[key] || 0;
                            dt[key] += Number(val);
                            dt.capacity += Number(val);
                        }
                    });
                });
            }

            if( $.isEmptyObject(dt) ){
                if(managerHour){
                    $.each(capacity , function(id){
                        $(gridControl.getHeaderRowColumn(id)).html('<span class="activity-capacity"><b>0</b> / ' + this + '</span>');
                    });
                } else {
                    $.each(capacity , function(id){
                        $(gridControl.getHeaderRowColumn(id)).html('<span class="activity-capacity"><b>0</b> / ' + Number(this).toFixed(2) + '</span>');
                    });
                }
            } else {
                if(managerHour){
                    $.each(dt , function(id, val){
                        var m = val%60;
                        var h = (val - m)/60;
                        h = (h < 10) ? '0'+h : h;
                        m = (m < 10) ? '0'+m : m;
                        $(gridControl.getHeaderRowColumn(id)).html('<span class="activity-capacity"><b>'+ h + ':' + m +'</b> / ' + capacity[id] + '</span>');
                    });
                } else {
                    $.each(dt , function(id, val){
                        $(gridControl.getHeaderRowColumn(id)).html('<span class="activity-capacity"><b>'+val.toFixed(2)+'</b> / ' + capacity[id].toFixed(2) + '</span>');
                    });
                }

            }
            $('.separator-week').parent().addClass('separator-week-div');
            $('.disable-edit-day').parent().addClass('disable-edit-day-div');
        });
        if(managerHour){
            $.each(capacity , function(id){
                header = $(gridControl.getHeaderRowColumn(id));
                header.html('<span class="activity-capacity"><b>0</b> / ' + this + '</span>');
            });
        } else {
            $.each(capacity , function(id){
                header = $(gridControl.getHeaderRowColumn(id));
                header.html('<span class="activity-capacity"><b>0</b> / ' + Number(this).toFixed(2) + '</span>');
            });
        }
        onAfterSave();
        onCellChange();
        // Button submit request all click
        $('#submit-request-all, #submit-request-all-top').click(function(){
            var $dialog = $('#dialog-request-all');
            $dialog.dialog({
                zIndex : 10000,
                modal : true,
                minHeight : 50,
                close : function(){
                    $dialog.dialog('destroy');
                }
            });
            if(managerHour){
                var valid = true;
                if(sendTimesheetPartially){
                    //do nothing
                } else {
                    $('.activity-capacity').each(function(){
                        var current = $(this).find('b').text(),
                            total = $.trim($(this).text().split('/')[1]);
                        current = current.split(':');
                        current = parseInt(current[0]) * 60 + parseInt(current[1]);

                        total = total.split(':');
                        total = parseInt(total[0]) * 60 + parseInt(total[1]);
                        if( current != total )valid = false;
                    });
                }
            } else {
                var valid = true;				
                if(sendTimesheetPartially){
                    //do nothing
                } else {
					if(limitedToCapacityWeek){
						var newCapacity = new BigNumber(0);
						$.each( gridControl.getData().getItems(), function(i, _row){
							$.each( _row, function( k, v){
								if( $.isNumeric(k)){
									newCapacity = newCapacity.plus(v);
								}
							});
							
						});
						valid = ( newCapacity == capacity['capacity']);
					}else{
						$('.activity-capacity').each(function(){
							var current = parseFloat($(this).find('b').text()),
								total = parseFloat($.trim($(this).text().split('/')[1]));
							
							if(fillMoreThanCapacity && current < total){
								valid = false;
							}
							if(!fillMoreThanCapacity && current != total )valid = false;
							
						});
					}
                }
				
            }
			
			if(!canSend){
				valid = false;
			}
			
            var dx = $('#form-request-all').data('capacity');
            if(manage_multiple_resource && getExternal == 2){
                valid = true;
            }
			
            if(valid){
                $dialog.find('a.ok').unbind().click(function(){
                    $('#form-request-all').submit();
                    $dialog.dialog('close');
                    return false;
                });
                $dialog.find('a.cancel').show().unbind().click(function(){
                    $dialog.dialog('close');
                    return false;
                });
                $dialog.find('.dialog-request-message').html($this.t('Send timesheet?'));
            }else if(!canSend){
				$dialog.find('a.ok').unbind().click(function(){
                    $dialog.dialog('close');
                    return false;
                });
                $dialog.find('a.cancel').hide();
                $dialog.find('.dialog-request-message').html($this.t('Absence has to be validated before sending the timesheet'));
			}else if( dx.total > dx.value ) {
                $dialog.find('a.ok').unbind().click(function(){
                    $dialog.dialog('close');
                    return false;
                });
                $dialog.find('a.cancel').hide();
                if(managerHour){
                    var rangeHour = dx.total - dx.value;
                    var rMinutes = rangeHour%60;
                    var rHour = (rangeHour - rMinutes)/60;
                    rHour = (rHour<10) ? '0'+rHour : rHour;
                    rMinutes = (rMinutes<10) ? '0'+rMinutes : rMinutes;
                    $dialog.find('.dialog-request-message').html($this.t('You have to declare %s before validate your request' , rHour + ':' + rMinutes));
                } else {
                    $dialog.find('.dialog-request-message').html($this.t('You have to declare %s days before validate your request' , (dx.total - dx.value).toFixed(2) ));
                }
            } else {
                $dialog.find('a.ok').unbind().click(function(){
                    $dialog.dialog('close');
                    return false;
                });
                $dialog.find('a.cancel').hide();
                $dialog.find('.dialog-request-message').html($this.t('There is error(s) in your timesheet'));
            }

        });
        // create context menu
        var employeeHasProject = <?php echo json_encode($employeeHasProject);?>;
        var profitHasProject = <?php echo json_encode($profitHasProject);?>;
        function contextMenuBuild(datas){
            var widthBrowser = $(window).width();
            widthBrowser = ((widthBrowser*85)/100).toFixed(0);
            widthBrowser = (widthBrowser/4).toFixed(1);
            // var leftMenuTwo = parseFloat(widthBrowser)-20;
            var leftMenuTwo = parseFloat(widthBrowser);
            // var leftMenuThree = parseFloat(widthBrowser) + parseFloat(leftMenuTwo) - 20;
            var leftMenuThree = parseFloat(widthBrowser) + parseFloat(leftMenuTwo);
            var idOfMenu1 = 'ch-menu-family';
            var classMenu1 = 'ch-menu-1';
            var menuLevelOne = '<div class="menu-level-1" style="width:'+widthBrowser+'px;" id="' +idOfMenu1+ '"><ul><div class="context-menu-filter no-hide-menu-1"><span class="no-hide-menu-1"><input type="text" rel="no-history" onclick="initMenuFilter(\''+idOfMenu1+'\', \'' +classMenu1+ '\');" class="ch-menu-input no-hide-menu-1"></span></div>';
            var menu = menuTwo = menuThree = '';
            $.each(datas.family, function(idFamily, valFamily){
                var nameFamily = valFamily.name ? valFamily.name : '';
                var familyChildren = valFamily.sub || valFamily.act ? 'have-children' : '';
                    menuLevelOne += '<li id="family-' +idFamily+ '" class="'+familyChildren+' no-hide-menu-1"><span class="ch-family no-hide-menu-1 ch-menu-1">' +nameFamily+ '</span></li>';
                if(valFamily.sub || valFamily.act){
                    var idOfMenu2 = 'menu-family-' +idFamily;
                    var classMenu2 = 'ch-menu-2';
                    var menuLevelTwo = '<div id="menu-family-' +idFamily+ '" class="menu-level-2" style="width:'+widthBrowser+'px; left:'+leftMenuTwo+'px;"><ul><div class="context-menu-filter no-hide-menu-1"><span class="no-hide-menu-1"><input type="text" rel="no-history" onclick="initMenuFilter(\''+idOfMenu2+'\', \'' +classMenu2+ '\');" class="ch-menu-input no-hide-menu-1"></span></div>';
                    if(valFamily.sub){
                        $.each(valFamily.sub, function(index, idSubFamily){
                            var nameSubFamily = datas.subfamily[idSubFamily].name ? datas.subfamily[idSubFamily].name : '';
                            var subFamilyChildren = datas.subfamily[idSubFamily].act ? 'have-children-sub' : '';
                            menuLevelTwo += '<li id="sub-family-' +idSubFamily+ '" class="'+subFamilyChildren+' no-hide-menu-1"><span class="ch-sub-family no-hide-menu-1 ch-menu-2">' +nameSubFamily+ '</span></li>';
                            if(datas.subfamily[idSubFamily].act){
                                var idOfMenu3 = 'menu-sub-family-' +idSubFamily;
                                var classMenu3 = 'ch-menu-3';
                                var menuLevelThree = '<div id="menu-sub-family-' +idSubFamily+ '" class="menu-level-3" style="width:'+widthBrowser+'px; left:'+leftMenuThree+'px;"><ul><div class="context-menu-filter no-hide-menu-1"><span class="no-hide-menu-1"><input type="text" rel="no-history" onclick="initMenuFilter(\''+idOfMenu3+'\', \'' +classMenu3+ '\');" class="ch-menu-input no-hide-menu-1"></span></div>';
                                $.each(datas.subfamily[idSubFamily].act, function(index, idActivity){
                                    var haveTask = (datas.activity[idActivity].tasks.length != 0 || datas.activity[idActivity].have_task === 'true') ? true : false;
                                    var classLi = (haveTask == true || (haveTask == false && !select_project_without_task)) ? 'color: #000' : 'color: orange';
                                    var classSpan = (haveTask == true) ? 'ch-activities' : '';
                                    var haveChildren = (haveTask == true || (haveTask == false && !select_project_without_task)) ? 'have-children-sub' : '';

                                    var nameActivity = datas.activity[idActivity].short_name ? datas.activity[idActivity].short_name : '';
                                    var _checking = true;
                                    if(resource_see_him){
                                        if(listActivityDisplay && $.inArray(idActivity, listActivityDisplay) != -1){
                                            // khi bat multiple kiem tra thang nao co trong lisst listActivityDisplay thi hien thi ra
                                        } else {
                                            // neu bat multiple, kiem tra khong co trong danh sach tassk display thi ko hien thi
                                            _checking = false;
                                        }
                                    } else {
                                        // khi ko dung multiple resource thi hien thi tat ca cac task ra...okie
                                    }
                                    if(_checking == true ){
                                        menuLevelThree += '<li id="three-activity-' +idActivity+ '" style="' +classLi+ '" class="'+haveChildren+' no-hide-menu-1"><span id="acti-' +idActivity+ '" class="no-hide-menu-1 ch-activity-img ch-menu-3 ' +classSpan+ '">' +nameActivity+ '</span></li>';
                                    }
                                });
                                menuLevelThree += '</ul></div>';
                                menuThree += menuLevelThree;
                            }
                        });
                    }
                    if(valFamily.sub && valFamily.act){
                        menuLevelTwo += '<hr style="margin: -3px 0;" />';
                    }
                    if(valFamily.act){
                        $.each(valFamily.act, function(index, idActivity){
							
                            var haveTask = (datas.activity[idActivity].tasks.length != 0 || datas.activity[idActivity].have_task === 'true') ? true : false;
                            var classLi = (haveTask == true || (haveTask == false && select_project_without_task == 0)) ? 'color: #000' : 'color: orange';
                            var classSpan = (haveTask == true || (haveTask == false && select_project_without_task == 0)) ? 'ch-activities' : '';
                            var haveChildren = (haveTask == true || (haveTask == false && !select_project_without_task == 0)) ? 'have-children-sub' : '';
							haveChildren += (haveTask == false && !select_project_without_task == 0) ? ' no-select' : '';
                            var nameActivity = datas.activity[idActivity].short_name ? datas.activity[idActivity].short_name : '';
                            var _checking = true;
                            if(resource_see_him){
                                if(listActivityDisplay && $.inArray(idActivity, listActivityDisplay) != -1){
                                    // khi bat multiple kiem tra thang nao co trong lisst listActivityDisplay thi hien thi ra
                                } else {
                                    // neu bat multiple, kiem tra khong co trong danh sach tassk display thi ko hien thi
                                    _checking = false;
                                }
                            } else {
                                // khi ko dung multiple resource thi hien thi tat ca cac task ra...okie
                            }
                            if(_checking == true){
                               menuLevelTwo += '<li id="two-activity-' +idActivity+ '" style="' +classLi+ '" class="'+haveChildren+' no-hide-menu-1"><span id="acti-' +idActivity+ '" class="no-hide-menu-1 ch-activity-img ch-menu-2 ' +classSpan+ '">' +nameActivity+ '</span></li>';
                            }
                        });
                    }
                    menuLevelTwo += '</ul></div>';
                    menuTwo += menuLevelTwo;
                }
            });
            menuLevelOne += '</ul></div>';
            menu += menuLevelOne;
            menu += menuTwo;
            menu += menuThree;
            $('#menuContext').html(menu);
            var menuLevelFour = '';
            $.each(datas.alltask, function(idActivity, tasks){
                var idOfMenu4 = 'menu-activity-' +idActivity;
                var classMenu4 = 'ch-menu-4';
                menuLevelFour += '<div id="menu-activity-' +idActivity+ '" class="menu-level-3-4" style="width:'+widthBrowser+'px;"><ul><div class="context-menu-filter no-hide-menu-1"><span class="no-hide-menu-1"><input type="text" rel="no-history" onclick="initMenuFilter(\''+idOfMenu4+'\', \'' +classMenu4+ '\');" class="ch-menu-input no-hide-menu-1"></span></div>';
                var isProject = datas.activity[idActivity].is_project ? datas.activity[idActivity].is_project : 0;
                $.each(tasks, function(idTask, values){
                    if(values.status && (values.status === 'cl' || values.status === 'CL')){
                        // do nothing
                    } else {
                        var _checking = true;
                        if(resource_see_him){
                            if(listTaskDisplay && $.inArray(idTask, listTaskDisplay) != -1){
                                // khi bat multiple kiem tra thang nao co trong lisst listTaskDisplay thi hien thi ra
                            } else {
                                // neu bat multiple, kiem tra khong co trong danh sach tassk display thi ko hien thi
                                _checking = false;
                            }
                        } else {
                            // khi ko dung multiple resource thi hien thi tat ca cac task ra...okie
                        }
                        if(values.previous == null && _checking == true){
                            var nameTask = values.name ? values.name : '';
                            menuLevelFour += '<li style="color: blue;" class="no-hide-menu-1"><span id="task-' +idActivity+ '-' +idTask+'" class="no-hide-menu-1 ch-task-img ch-menu-4">' +nameTask+ '</span>';
                            var consumed = values['consumed'] ? parseFloat(values['consumed']) : 0;
                            var workload = values['estimated'] ? parseFloat(values['estimated']) : 0;
                            var stt = values['status'] ? values['status'] : 'xx';
                            var classValid = 'wd-bt-yes';
                            var classValidSpan = '';
                            if(values['is_parent'] === 'true'){
                                classValid = 'wd-bt-no ch-item-disabled ch-item-disabled-selected parent_task';
                                classValidSpan = 'ch-bt-parent';
                            } else {
                                if(consumed >= workload){
                                    classValid = 'wd-bt-no ch-item-disabled cs_wl';
                                }
                            }
                            if(isProject == 0){
                                // nhung activity khong lien ket voi project
                                // thi employee duoc request tu do
                            } else {
                                // nhung activity co linked voi project
                                // profitHasProject: cac project profit nay co tham gia vao
                                if(GetObjectKeyIndex(profitHasProject, isProject) == null){
                                    // kiem tra cac project khong co trong profitHasProject
                                    // thi ko cho phep request
                                    classValid += ' ch-only-profit-have-request';
                                } else {
                                    // neu kiem tra project co trong list profitHasProject
                                    // thi cho phep request
                                }
                                // employeeHasProject: cac project employee nay co tham gia vao.
                                if(GetObjectKeyIndex(employeeHasProject, isProject) == null){
                                    // kiem tra cac project khong co trong employeeHasProject
                                    // thi ko cho phep request
                                    classValid += ' ch-only-employ-have-request';
                                } else {
                                    // neu kiem tra project co trong list employeeHasProject
                                    // thi cho phep request
                                }
                            }
							classValid += ' ' + stt; 
                            menuLevelFour += '<p style="margin-left: 28px;" class="' + classValid + ' no-hide-menu-1">(' + consumed + '/' + workload + ')<span class="' +classValidSpan+ '"></span></p></li>';
                        }
                    }
                });
                menuLevelFour += '</ul></div>';
            });
            if(menuLevelFour != ''){
                $('#menuContext').html($('#menuContext').html() + menuLevelFour);
            }
            $.ajax({
                url : '<?php echo $html->url(array('action' => 'contextMenuCache', $idManageCheck)); ?>',
                cache : false,
                type : 'POST',
                data: {
                    content: $('#menuContext').html()
                },
                success: function(data){
                }
            });
            return menu;
        }
        function isRightClick(event) {
            var rightclick;
            if (!event) var event = window.event;
            if (event.which) {
                //if(event.which == 3 || event.which == 1){
                if(event.which == 3){
                    rightclick = true;
                }
            }
            else if (event.button) rightclick = (event.button == 2);
            return rightclick;
        }
        function refreshMapeds(id_activity, id_task, list_mapeds){
			console.log( 'refreshMapeds');
            var result = '';
            $.ajax({
                url : '<?php echo $html->url(array('action' => 'refreshDataMenu')); ?>' + '/' + id_activity + '/' + id_task,
                cache : false,
                type : 'POST',
                async: false,
                data: {
                    mapeds: list_mapeds
                },
                success: function(data){
                    result = JSON.parse(data);
                }
            });
            return result;
        }
        // disable the default browser's context menu.
        $(document).on('contextmenu', function (e) {
            return false;
        });
        var cacheMenu = <?php echo json_encode($cacheMenu)?>;
        var allowRequestRemainZero = <?php echo json_encode($allowRequestRemain['ActivitySetting']['allow_remain_zero_consume']);?>;
        var onlyEmployeeInProjectHaveRequest = <?php echo json_encode($allowRequestRemain['ActivitySetting']['allow_employee_in_team_consume']);?>;
        var onlyPorfitInProjectHaveRequest = <?php echo json_encode($allowRequestRemain['ActivitySetting']['allow_team_consume']);?>;
        var employeeName = <?php echo json_encode($employeeName);?>;
        /**
         * An/hien refresh menu
         */
        if(cacheMenu){
            $('#menuContext').html(cacheMenu);
            // $('#refresh_menu').show();
        } else {
            // $('#refresh_menu').hide();
        }
        $.ajax({
            url : '<?php echo $html->url(array('action' => 'contextMenu', $idManageCheck)); ?>',
            cache : false,
            type : 'POST',
            data: {
                listActi: listActi,
                listTask: listTask,
                employeeName: employeeName
            },
            success: function(data){
                var datas = JSON.parse(data);
                mapeds = datas;
                activities = datas.activity;
                tasks = datas.task;
                if(!cacheMenu){
                    contextMenu = contextMenuBuild(datas);
                    if(contextMenu){
                        $('#ch-waiting').hide();
                        // $('#refresh_menu').show();
                    }
                }
            }
        });
        /**
         * Xu ly hover vao cac item cua menu context
         */
        var menuLiOne = [];
        var menuLiTwo = [];
        var hoverLiThree = '';
        var hoverLiThreeFour = '';
        $('.menu-level-1 ul li').live('hover', function(){
            //alert('dadas');return;
            var familyHoverId = $(this).attr('id');
            familyHoverId = '#menu-'+familyHoverId;
            if(familyHoverId){
                $('.menu-level-1 ul li').removeClass('back-highlighted');
                $('.menu-level-2 ul li').removeClass('back-highlighted');
                $('.menu-level-3 ul li').removeClass('back-highlighted');
                $(this).addClass('back-highlighted');
                $('#menuContext').find('.menu-level-2').each(function(){
                    var subAndActHoverId = '#' + $(this).attr('id');
                    if(familyHoverId == subAndActHoverId){
                        $(familyHoverId).show();
                    } else {
                        $(subAndActHoverId).hide();
                        if(hoverLiThreeFour){
                            $(hoverLiThreeFour).hide();
                        }
                        if(hoverLiThree){
                            $(hoverLiThree).hide();
                        }
                    }
                });
            }
        });
        $('.menu-level-2 ul li').live('hover', function(){
            var getId = $(this).attr('id');
            var checkSub = getId.split('-');
            if(checkSub[0] === 'sub'){
                $('.menu-level-2 ul li').removeClass('back-highlighted');
                $('.menu-level-3 ul li').removeClass('back-highlighted');
                $(this).addClass('back-highlighted');
                $('.menu-level-3-4').hide();
                var subFamilyHoverId = '#menu-'+getId;
                if(subFamilyHoverId){
                    $('#menuContext').find('.menu-level-3').each(function(){
                        var activityHoverId = '#' + $(this).attr('id');
                        if(subFamilyHoverId == activityHoverId){
                            hoverLiThree = subFamilyHoverId;
                            $(subFamilyHoverId).show();
                        } else {
                            $(activityHoverId).hide();
                        }
                    });
                }
            } else {
                $('.menu-level-3').hide();
                var widthBrowser = $(window).width();
                widthBrowser = ((widthBrowser*85)/100).toFixed(0);
                widthBrowser = (widthBrowser/4).toFixed(1);
                // var leftMenuTwo = parseFloat(widthBrowser)-20;
				var leftMenuTwo = parseFloat(widthBrowser);
				// var leftMenuThree = parseFloat(widthBrowser) + parseFloat(leftMenuTwo) - 20;
				var leftMenuThree = parseFloat(widthBrowser) + parseFloat(leftMenuTwo);
                var activityHoverId = '#menu-'+checkSub[1]+'-'+checkSub[2];
                if(activityHoverId){
                    $('.menu-level-2 ul li').removeClass('back-highlighted');
                    $(this).addClass('back-highlighted');
                    $('#menuContext').find('.menu-level-3-4').each(function(){
                        var taskHoverId = '#' + $(this).attr('id');
                        if(activityHoverId == taskHoverId){
                            $(activityHoverId).css('left', leftMenuThree+'px');
                            hoverLiThreeFour = activityHoverId;
                            $(activityHoverId).show();
                        } else {
                            $(taskHoverId).hide();
                        }
                    });
                }
            }
        });
        $('.menu-level-3 ul li').live('hover', function(){
            var widthBrowser = $(window).width();
            widthBrowser = ((widthBrowser*85)/100).toFixed(0);
            widthBrowser = (widthBrowser/4).toFixed(1);
            // var leftMenuTwo = parseFloat(widthBrowser)-20;
            var leftMenuTwo = parseFloat(widthBrowser);
            // var leftMenuThree = parseFloat(widthBrowser) + parseFloat(leftMenuTwo) - 20;
            var leftMenuThree = parseFloat(widthBrowser) + parseFloat(leftMenuTwo);
            var leftMenuFour = parseFloat(leftMenuTwo) + parseFloat(leftMenuThree);
            var getId = $(this).attr('id');
            getId = getId.split('-');
            var activityHoverId = '#menu-'+getId[1]+'-'+getId[2];
            if(activityHoverId){
                $('.menu-level-3 ul li').removeClass('back-highlighted');
                    $(this).addClass('back-highlighted');
                $('#menuContext').find('.menu-level-3-4').each(function(){
                    var taskHoverId = '#' + $(this).attr('id');
                    if(activityHoverId == taskHoverId){
                        $(activityHoverId).css('left', leftMenuFour+'px');
                        hoverLiThreeFour = activityHoverId;
                        $(activityHoverId).show();
                    } else {
                        $(taskHoverId).hide();
                    }
                });
            }
        });
       
        /**
         * refresh menu
         */
        $('#refresh_menu').click(function(){
			 _href = window.location.search;
			if(_href){
				_href = '<?php echo $this->Html->url('/') ?>activity_forecasts/request/'+typeSelect+'/'+_href;
			} else {
				_href = '<?php echo $this->Html->url('/') ?>activity_forecasts/request/'+typeSelect+'/';
			}
            $.ajax({
                url : '<?php echo $html->url(array('action' => 'cleanupCacheMenu', $idManageCheck)); ?>',
                cache : false,
                success: function(data){
                    // $('#refresh_menu').hide();
                    $('#clean-ok').show();
                    window.location = (_href);
                }
            });
            cacheMenu = '';
        });
        // click vao cac item trong context menu
        var checkTask = false;
        $('.menu-level-2 ul li, .menu-level-3 ul li, .menu-level-3-4 ul li').live('click', function(){
            var element = this;
            // family
            if($(this).find('p').hasClass('ch-family') || $(this).find('span').hasClass('ch-family')){
                return false;
            }
            // sub-family
            if($(this).find('p').hasClass('ch-sub-family') || $(this).find('span').hasClass('ch-sub-family')){
                return false;
            }
            // activity
            if($(this).find('p').hasClass('ch-activities') || $(this).find('span').hasClass('ch-activities')){
                return false;
            }
            // task con roi or task cha
            if($(this).find('p').hasClass('ch-item-disabled-selected') || $(this).find('span').hasClass('ch-item-disabled-selected')){
                return false;
            }
            // allow/not allow request when task have remain = 0
            if(allowRequestRemainZero == 0){
                if($(this).find('p').hasClass('ch-item-disabled RemainZero') || $(this).find('span').hasClass('ch-item-disabled RemainZero')){
                    return false;
                }
            }
            // only profit in project have request
            if(onlyPorfitInProjectHaveRequest == 1){
                if($(this).find('p').hasClass('ch-only-profit-have-request') || $(this).find('span').hasClass('ch-only-profit-have-request')){
                    return false;
                }
            }
            // only employee in project have request
            if(onlyEmployeeInProjectHaveRequest == 1){
                if($(this).find('p').hasClass('ch-only-employ-have-request') || $(this).find('span').hasClass('ch-only-employ-have-request')){
                    return false;
                }
            }
            var idSelected = $(this).find('span').attr('id');
            var titleSelected = $(this).find('span').html();
            if(idSelected){
                $('#menuContext').find('#'+idSelected).each(function(){
                    $(this).addClass('ch-item-disabled-selected selected');
                    $(this).parent().css('color', '#000');
                });
                idSelected = idSelected.split('-');
                if(idSelected[0] === 'task'){
                    checkTask = true;
                    if(GetObjectValueIndex(listTask, idSelected[2]) == null){
                        // do nothing
                     } else {
                        return false;
                     }
                } else {
                    checkTask = false;
                    // return false;
                    if(GetObjectValueIndex(listActi, idSelected[1]) == null){
                        // do nothing
                     } else {
                        return false;
                     }
                }
                selectActivity.call(element, idSelected[1], titleSelected);
                if(idSelected[0] === 'task'){
                    taskSelected = tasks[idSelected[2]] ? tasks[idSelected[2]] : [];
                    selectTask.call(element, idSelected[1], taskSelected);
                }
                $('#menuContext, .menu-level-1, .menu-level-2, .menu-level-3, .menu-level-3-4').hide();
            }
        });
        $('html').click(function(e, target) {
            //if is absence -> dont open
            var item = dataView.getItem(rowEdit);
			if( typeof item !==  'undefined'){
				if( item && ( item.type == 'ab' || item.type == 'chAb' || item.type == 'hl') ){
					return;
				}
			}
			if( !target )target = e.target;
			var widthBrowser = $(window).width();
			widthBrowser = ((widthBrowser*85)/100).toFixed(0);
			widthBrowser = (widthBrowser/4).toFixed(1);
			// var leftMenuTwo = parseFloat(widthBrowser)-20;
			var leftMenuTwo = parseFloat(widthBrowser);
			// var leftMenuThree = parseFloat(widthBrowser) + parseFloat(leftMenuTwo) - 20;
			var leftMenuThree = parseFloat(widthBrowser) + parseFloat(leftMenuTwo);
			var cellEdit = $(gridControl.getCellNode(rowEdit, 1));
			if(cellEdit.length > 0){
				var cellTop = $(gridControl.getCellNode(rowEdit, 1)).offset().top,
					wh = $(window).height(),
					menuHeight = 252, top = cellTop + 35;

				if( wh - cellTop < menuHeight/3 ){
					top = cellTop - menuHeight;
				}
				//top -= $(gridControl.getCellNode(0, 1)).offset().top;
				if(($(target).is('.l1') && !$(target).is('.slick-headerrow-column')) || $(target).is('.request-selectbox') || $(target).is('.no-hide-menu-1')) {
					if( $this.canModified){
						if($(target).is('.l1')){
							$('.menu-level-2, .menu-level-3, .menu-level-3-4').hide();
						}
						$('#menuContext').css('top', top+'px');
						$('#menuContext').show();
						$('.menu-level-1, .menu-level-2, .menu-level-3, .menu-level-3-4').css('width', widthBrowser+'px');
						$('.menu-level-2').css('left', leftMenuTwo+'px');
						$('.menu-level-3').css('left', leftMenuThree+'px');
						$('.menu-level-1').show();
					}
				} else {
					$('#menuContext').hide();
					$('.menu-level-1').hide();
					$('.menu-level-1 ul li').removeClass('back-highlighted');
					$('.menu-level-2 ul li').removeClass('back-highlighted');
					$('.menu-level-3 ul li').removeClass('back-highlighted');
				}
			}
			
        });
        // chon task or activity
        var selectActivity = function(key,title){
            var editor = gridControl.getCellEditor(),
            item = editor.getArgs().item;
            editor.setValue(key);
            editor.div.html(title);
            editor.applyValue(item, editor.getValue());
            taskSelected = {task_id : ''};
            $(this).closest('.sub-context').hide();
            if(checkTask == false){
                // gridControl.gotoCell(gridView.length, 3, true);
            }
        },
        selectTask = function(key,task){
            var editor = gridControl.getCellEditor();
            taskSelected = {task_id : task.id};
            $(this).closest('.sub-context').hide();
            try{
                editor.div.html(editor.div.html() + ':' + activities[key].tasks[task.id].name);
                // gridControl.gotoCell(gridView.length, 3, true);
            }catch(ex){
            }
        };
        /**************************************************Phan forecast nam ben tren cua man hinh request ***********************/			
        var    $container      = $('#absence-table').html(''),
            dataSum = 0,
            output = _output = '',total = _tForecast = _tRemain = _tWorkload = 0, _workloads = [];
        if(typeSelect != 'week'){
            $containerFixed  = $('#absence-table-fixed').html('');
        }
        var _total = 0;
        $.each(listWorkingDays, function(k, val){
            _total++;
        });
        $.each(dataSets, function(id){
            var output  = '', total = _total;
            $.each(this ,function(day , data){
                var _day = day * 1000;
                _day = new Date(_day);
                _day = daysInWeek[_day.getDay()];
                var val     = parseFloat(workdays[_day]),
                    dt      = holidays[data.date] || {},
                    opt     = {value : ''};
                if(this.workload){
                    var name = '';
                    $.each(this.workload, function(ind, values){
                        ind = ind.split('-');
                        if(ind[0] === 'pr'){
                            name += projectFromTasks[ind[1]] ? '<div class="activity-tootip-forecast" rel="pr-'+ind[1]+'" day="'+day+'"><p>' + projectFromTasks[ind[1]] + '</p></div>' : '';
                        } else {
                            name += activityFromTasks[ind[1]] ? '<div class="activity-tootip-forecast" rel="ac-'+ind[1]+'" day="'+day+'"><p>' + activityFromTasks[ind[1]] + '</p></div>' : '';
                        }
                    });
                    opt.value = name;
                }
                switch(val){
                    case 1:
                        if(dt['am']){
                            if(dt['pm']){
                                opt.value += '<div><p><span style="color: red;">' + $this.t('Holiday') + '</span></p></div>';
                                total -= 1;
                            } else {
                                opt.value += '<div><p><span style="color: red;">' + $this.t('Holiday') + '</span></p></div>';
                                total -= 0.5;
                            }
                        } else {
                            if(dt['pm']){
                                opt.value += '<div><p><span style="color: red;">' + $this.t('Holiday') + '</span></p></div>';
                                total -= 0.5;
                            } else {

                            }
                        }
                        break;
                    case 0.5:
                        if(dt['am']){
                            opt.value += '<div><p><span style="color: red;">' + $this.t('Holiday') + '</span></p></div>';
                            total -= 0.5;
                        } else {

                        }
                        break;
                }
                if(data['absence_am'] && data['response_am'] == 'validated'){
                    if(data['absence_pm'] && data['response_pm'] == 'validated'){
                        if(absences[data['absence_am']].print === absences[data['absence_pm']].print){
                            opt.value += '<div><p><span style="color: red;">' + absences[data['absence_am']].print + '</span></p></div>';
                        } else {
                            opt.value += '<div><p><span style="color: red;">' + absences[data['absence_am']].print + '</span></p></div>'
                            + '<div><p><span style="color: red;">' + absences[data['absence_pm']].print + '</span></p></div>';
                        }
                        total -= 1;
                    } else {
                        opt.value += '<div><p><span style="color: red;">' + absences[data['absence_am']].print + '</span></p></div>';
                        total -= 0.5;
                    }
                } else {
                    if(data['absence_pm'] && data['response_pm'] == 'validated'){
                        opt.value += '<div><p><span style="color: red;">' + absences[data['absence_pm']].print + '</span></p></div>';
                        total -= 0.5;
                    } else {

                    }
                }
                if(listWorkingDays[day]){
                    $.each(opt, function(index, vals){
                        output+= '<td class="foreDayCols" >' + vals + '</td>';
                    });
                }
            });
            if((typeSelect == 'week' || typeSelect == 'month') && (requestConfirm == -1 || requestConfirm == 1)){
                output += '<td  style="border-right: 1px solid #ccc !important;"></td>';
            }
            var $class = '';
            if(dataSum > total){
                $class = 'check-workload';
            }
            _top = dataSum.toFixed(2) + '/' + total*ratio;
            if( isMulti )_top = '';
            if(typeSelect === 'week'){
                $container.append('<tr><td rowspan="2"><span class="employee-top"><img src="' + js_avatar(id) + '" alt="avatar" />' + employees[id]
                     + ' ('+ profit_name +') </span><td class="' + $class + '" rowspan="2"><span>' + '' + '</span></td>' + output + '</tr>'
                     + '<tr>' + _output + '</tr>'
                );
            } else {
                $containerFixed.append('<tr><td rowspan="2"><span class="employee-top"><img src="' + js_avatar(id) + '" alt="avatar" />' + employees[id]
                     + ' ('+ profit_name +') </span><td class="' + $class + '" rowspan="2"><span>' + _top + '</span></td></tr>'

                );
                $container.append('<tr>' + output + '</tr>');
            }
        });
    });
    var initMenuFilter = function(indFilter, indSpan){
        var timeoutID = null, searchHandler = function(){
            var val = $(this).val();
            $('#' + indFilter).find('li span.'+indSpan).each(function(){
                var $label = $(this).html();
                $label = $label.toLowerCase();
                val = val.toLowerCase();
                if(!val.length || $label.indexOf(val) != -1 || !val){
                    $(this).parent().css('display', 'block');
                } else{
                    $(this).parent().css('display', 'none');
                }
            });
        };

        $('#' + indFilter + ' div span').find('input').click(function(e){
            e.stopImmediatePropagation();
        }).keyup(function(){
            var self = this;
            clearTimeout(timeoutID);
            timeoutID = setTimeout(function(){
                searchHandler.call(self);
            } , 200);
        });

    };
    <?php
    $month = date('m', $avg);
    $week = date('W', $avg);
    $year = date('Y', $avg);
    //$profit = !empty($this->params['url']['profit']) ? $this->params['url']['profit'] : '';
    $idManageValidation = !empty($this->params['url']['id']) ? $this->params['url']['id'] : '';
    ?>
    var $month = <?php echo json_encode($month);?>,
        $week = <?php echo json_encode($week);?>,
        $year = <?php echo json_encode($year);?>,
        $profit = <?php echo json_encode($profit);?>,
        $getDataByPath = <?php echo json_encode($getDataByPath);?>,
        $idManageValidation = <?php echo json_encode($idManageValidation);?>;
   
    $('#activity-request').css('width',$('#absence-wrapper').width());
    $(function () {
        $("#absence-scroll").scroll(function () {
            $('.separator-week').parent().addClass('separator-week-div');
            $('.disable-edit-day').parent().addClass('disable-edit-day-div');
            $(".slick-viewport-right").scrollLeft($("#absence-scroll").scrollLeft());
            $("#scrollTopAbsence").scrollLeft($("#absence-scroll").scrollLeft());
        });
        $("#scrollTopAbsence").scroll(function () {
            $('.separator-week').parent().addClass('separator-week-div');
            $('.disable-edit-day').parent().addClass('disable-edit-day-div');
            $(".slick-viewport-right").scrollLeft($("#absence-scroll").scrollLeft());
            $("#absence-scroll").scrollLeft($("#scrollTopAbsence").scrollLeft());
        });

        $(".slick-viewport-right").scroll(function () {
            $('.separator-week').parent().addClass('separator-week-div');
            $('.disable-edit-day').parent().addClass('disable-edit-day-div');
            $("#absence-scroll").scrollLeft($(".slick-viewport-right").scrollLeft());
        });
        $('.separator-week').parent().addClass('separator-week-div');
        $('.disable-edit-day').parent().addClass('disable-edit-day-div');
        $('#absence-table-fixed').children().children().css({
            height: $('#absence-table').children().children().height() + 'px',
            'vertical-align': 'middle'
        });
    });
</script>
<style>
	#copy_forecasts_table tr.selected,
    .tr_copy_exists{
        background-color: rgb(224, 224, 224);
    }
</style>
<!-- dialog_copy_forecasts -->
<div id="dialog_copy_forecasts" class="buttons" style="display: none;">
    <fieldset>
        <?php
        echo $this->Form->create('Copy', array(
			'type' => 'file', 'id' => 'form_dialog_copy_forecasts',
			'url' => array('controller' => 'activity_forecasts', 'action' => 'copy_forecasts', $typeSelect, $_start, $_end, '?' => array(
				'get_path' => $getDataByPath
			))
		));
		// if(!empty($dataForecasts)){
			echo $this->Form->hidden('get_path', array('value' => isset($getDataByPath) ? $getDataByPath : ''));
			echo $this->Form->hidden('profit', array('value' => !empty($this->params['url']['profit']) ? $this->params['url']['profit'] : $profit));
			echo $this->Form->hidden('id', array('value' => !empty($this->params['url']['id']) ? $this->params['url']['id'] : $employee_info['Employee']['id']));
			echo $this->Form->input('week', array('type' => 'hidden', 'value' => date('W', $avg)));
			echo $this->Form->input('month', array('type' => 'hidden', 'value' => date('m', $_start)));
			echo $this->Form->input('year', array('type' => 'hidden', 'value' => date('Y', $avg)));
		// }
		?>
        <div style="height:auto; width: 100%; overflow-x: auto;" class="wd-scroll-form" id="reForecasts">
            <table id="copy_forecasts_table" class="display wd-forecasts-table">
                <?php  
				///$taskTeam = $taskEm = $projectTeam = $projectEm = array();
				// ob_clean();
				// pr($dataForecasts);die;
				$displayed=array();
				foreach( array(0,1) as $is_pc){
					$i = 0;
					// echo  'asdasdasdasdas'.$is_pc;
					echo $is_pc ? '<!-- PC Tasks -->' : '<!-- Employee Tasks -->';
					$_key = 'Employee';
					if( $is_pc) { 
						$_key = 'Team';
						?>
						<td colspan="5" style="color: black; text-align: left; height: 30px; padding: 30px 10px 0; font-size: 200%;"><?php __('Affected to my team'); ?></td>
					<?php } ?> 
					<thead>
						<tr>
							<th rowspan="1" class="wd-header wd-action-header"><?php __('Action'); ?></th>
							<th rowspan="2" class="wd-header"><?php __('Project/Activity'); ?></th>
							<th rowspan="2" class="wd-header"><?php __('Part'); ?></th>
							<th rowspan="2" class="wd-header"><?php __('Phase'); ?></th>
							<th rowspan="2" class="wd-header"><?php __('Task'); ?></th>
						</tr>
					</thead>
					<tbody id="copy-forecast-<?php echo $is_pc ? 'pc' : 'em'; ?>-table" class="copyForecastTable">
						<tr style = "background-color: #DDDDDD;">
							<td colspan="1">
								<?php echo $this->Form->input('select_all_'.( $is_pc ? 'pc' : 'employee'), array('type' => 'checkbox', 'label' => false, 'class' => 'select-all-task', 'hiddenField' => false, 'title' => __('Toggle select all', true),));?>
							</td>
							<td colspan="1"><input class="input-<?php echo $_key; ?>" id="input-Project-<?php echo $_key; ?>" type="text" value="" style="height: 15px; width: 98%;"></td>
							<td colspan="1"></td>
							<td colspan="1"></td>
							<td colspan="1" ><input class="input-<?php echo $_key; ?>" id="input-Task-<?php echo $_key; ?>" type="text" value="" style="height: 15px; width: 98%;"></td>
						</tr>
					<?php
					
					?>
					</tbody>
					<?php echo $is_pc ? '<!-- END PC Tasks -->' : '<!-- END Employee Tasks -->'; 
				}?> 	
            </table>
        </div>
        <?php
        echo $this->Form->end();
        ?>
    </fieldset>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_copy" style="display: none;"><?php __('OK') ?></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_copy_disable" style="background: #CECACA !important;"><?php __('OK') ?></a></li>
    </ul>
</div>
<!-- dialog_copy_forecasts.end -->
<!-- dialog_loader -->
<div id="gs_loader" style="display: none;">
    <div class="gs_loader">
        <p><?php __('Please wait...') ?></p>
    </div>
</div>
<!-- dialog_loader.end -->
<?php
	// ob_clean();
	// debug($projectFromTasks);
	// exit;
 // ?>
<script>
	var fillTimesheet = <?php echo json_encode($fillTimesheet); ?>;
	var copyActivity = <?php echo json_encode(isset($copyActivity) ? $copyActivity : 0);?>;
	var dataForecasts = <?php echo json_encode(isset($dataForecasts) ? $dataForecasts : array());?>;
	var projectPartFromTasks = <?php echo json_encode(isset($projectPartFromTasks) ? $projectPartFromTasks : array());?>;
	var projectPhaseFromTasks = <?php echo json_encode(isset($projectPhaseFromTasks) ? $projectPhaseFromTasks : array());?>;
	var lisTaskRequests = <?php echo json_encode(isset($lisTaskRequests) ? $lisTaskRequests : array());?>;
	var projectFromTasks = <?php echo json_encode(isset($projectFromTasks) ? $projectFromTasks : array());?>;
	var activityFromTasks = <?php echo json_encode(isset($activityFromTasks) ? $activityFromTasks : array());?>;
	var start = <?php echo json_encode($_start);?>;
	var end = <?php echo json_encode($_end);?>;
	
	var filterHandler = function(_this, _prj_act_filter, _task_filter){
        var _list_tr_filter = _this.closest('tr').siblings();
		$.each(_list_tr_filter, function( index, _tr){
			_tr = $(_tr);
			var _project_name = _tr.find('.filter-project-activity').text();
			if( _project_name.toLowerCase().indexOf(_prj_act_filter.toLowerCase()) == -1 ){
				_tr.addClass('hidden-project');
			}else{
				_tr.removeClass('hidden-project');
			}
			var _task_name = _tr.find('.filter-task').text();
			if( _task_name.toLowerCase().indexOf(_task_filter.toLowerCase()) == -1 ){
				_tr.addClass('hidden-task');
			}else{
				_tr.removeClass('hidden-task');
			}
		});
		
    };
	function initPopupEvent(){
		$('.input-Employee').unbind('keyup').on('keyup', function(){
			var _prj_act_filter = $('#input-Project-Employee').val();
			var _task_filter = $('#input-Task-Employee').val();
			var _this = $(this);
			filterHandler(_this, _prj_act_filter, _task_filter);
		});
		$('.input-Team').unbind('keyup').on('keyup', function(){
			var _this = $(this);
			var val1 = $('#input-Project-Team').val();
			var val2 = $('#input-Task-Team').val();
			filterHandler(_this, val1, val2);
		});
		$('.select-all-task').unbind('click').on('click', function(){
			var _this = $(this);
			var is_checked = _this.is(':checked');
			$.each( _this.closest('tr').siblings(), function(){
				var _checkbox = $(this).find('.copy-checkbox-format');
				if( !$(_checkbox).prop('disabled')){
					$(_checkbox).prop('checked', is_checked).trigger('change');
				}
			});
		});
		
		// Disable "select all" button if have no task selectable
		$('#copy_forecasts_table .copyForecastTable').each(function(){
			if( !$(this).find('.copy-checkbox-format:checkbox:not(":disabled")').length)
				$(this).find('.select-all-task').prop(':checked',false).prop('disabled', 'disabled');
		});
		$(".copy-checkbox-format").unbind('change').on('change',function(){
			var _this = $(this);
			if(_this.is(':checked')){
				$('#ok_copy').show();
				$('#ok_copy_disable').hide();
				_this.closest('tr').addClass('selected');
			} else {
				_this.closest('tr').removeClass('selected');
				// Disable submit if no select
				var _selected = $("#copy_forecasts_table tr .copy-checkbox-format:checkbox:checked:not(':disabled')");
				if(  !_selected.length){
					$('#ok_copy').hide();
					$('#ok_copy_disable').show();
				}
			}

		});
	}
	function renderCopyForecastPopup(){
		var _table = $('#copy_forecasts_table');
		var displayed={};
		var _html = '';
		$.each( [0,1], function(index, is_pc){
			_html +=  is_pc ? '<!-- PC Tasks -->' : '<!-- Employee Tasks -->';
			_key = is_pc ? 'Team' : 'Employee';
			if( is_pc){
				// team title
				_html += '<td colspan="5" style="color: black; text-align: left; height: 30px; padding: 30px 10px 0; font-size: 200%;"><?php __('Affected to my team'); ?></td>';
			}
			_html += '<thead><tr>';
			// table head
			_html += '<th class="wd-header wd-action-header"><?php __('Action'); ?></th>';
			_html += '<th class="wd-header"><?php __('Project/Activity'); ?></th>';
			_html += '<th class="wd-header"><?php __('Part'); ?></th>';
			_html += '<th class="wd-header"><?php __('Phase'); ?></th>';
			_html += '<th class="wd-header"><?php __('Task'); ?></th>';
			_html += '</tr></thead>';
			
			// Filter input
			_html += '<tbody id="copy-forecast-' + _key + '-table" class="copyForecastTable">';
			
				// Filter row
				_html += '<tr style = "background-color: #DDDDDD;">';
					// checkbox select all
					_html += '<td colspan="1"><div class="input checkbox"><input type="checkbox" name="data[Copy][select_all_' + _key + ']" class="select-all-task" title="<?php __('Toggle select all');?>" value="1" id="CopySelectAll' + _key + '"></div></td>';
					// Filter Project/Activity
					_html += '<td colspan="1"><input class="input-' + _key + '" id="input-Project-' + _key + '" type="text" value="" style="height: 15px; width: 98%;"></td>';
					// Nofilter for Part and phase 
					_html += '<td colspan="1"></td>';
					_html += '<td colspan="1"></td>';
					// Filter task name
					_html += '<td colspan="1" ><input class="input-' + _key + '" id="input-Task-' + _key + '" type="text" value="" style="height: 15px; width: 98%;"></td>';
				_html += '</tr>';
				
				//Data row
				$.each(dataForecasts, function(index, _activity_task){
					var taskId = _activity_task['task_id'];
					if( (is_pc == _activity_task['is_profit_center']) && ( copyActivity || is_pc) && !(taskId in displayed)){
						var partId = _activity_task['part_id'] ? _activity_task['part_id'] : '';
						var phaseId = _activity_task['phase_id'] ? _activity_task['phase_id'] : '';
						var taskTitle = _activity_task['task_title'] ? _activity_task['task_title'] : '';
						var projectId = _activity_task['project_id'] ? _activity_task['project_id'] : '';
						var activityId = _activity_task['activity_id'] ? _activity_task['activity_id'] : '';
						var projectActivityName = ( projectFromTasks[projectId] ? projectFromTasks[projectId]: (activityFromTasks[activityId] ? activityFromTasks[activityId] : ''));
						var projectPartName = projectPartFromTasks[partId] ? projectPartFromTasks[partId] : '';
						var projectPhaseName = projectPhaseFromTasks[phaseId] ? projectPhaseFromTasks[phaseId] : '';
						displayed[taskId] = 1;
						var exists = (lisTaskRequests.indexOf(taskId.toString()) !== -1);
						_html += '<tr id="filter-' + taskId + '" class="filter-' + _key + '-' + projectId + ( exists ? ' tr_copy_exists' : '') + '">';
							// checkbox 
							_html += '<td>';
							_html += '<div class="input checkbox"><input type="checkbox" name="data[id][' + taskId + ']" class="copy-checkbox-format" ' + (exists ? 'disabled="disabled" ' : '') + 'value="1" id="id' + taskId + '"></div>';
							_html += '</td>';
							// Project/Activity name 
							_html += '<td class="filter-project-activity">' + projectActivityName + '</td>';
							// Part name 
							_html += '<td>' + projectPartName + '</td>';
							// Phase name 
							_html += '<td>' + projectPhaseName + '</td>';
							// Task title
							_html += '<td class="filter-task">' + taskTitle + '</td>';
						_html += '</tr>';
					}
					
				});
				
			_html += '</tbody>';
			_html += is_pc ? '<!-- END PC Tasks -->' : '<!-- END Employee Tasks -->'; 
		});
		_table.html(_html);
		$('#ok_copy').hide();
		$('#ok_copy_disable').show();
		initPopupEvent();
	}
    $("#copy_forecast").live('click',function(){
        var lHeight =  $(window).height();
        var DialogFullHeight = Math.round((80*lHeight)/100);
        var lWidth = $(window).width();
        var DialogFull = Math.round((95*lWidth)/100);
        widthPopup = DialogFull;
		if( !fillTimesheet) {
			$.ajax({
				url: <?php echo json_encode($this->Html->url( array('controller' => 'activity_forecasts', 'action' => 'getDataCopyForecast')) ); ?>,
				type: 'get',
				dataType: 'json',
				data: {
					idEmp: '<?php echo $employeeName['id'];?>',
					idPc: '<?php echo $employeeName['profit_center_id'];?>',
					start: start,
					end: end,
					
				},
				beforeSend: function(){
					$('#gs_loader').show();
				},
				success: function(response){
					if( response.result == true){
						var _data = response.data;
						dataForecasts = _data.results;
						projectPartFromTasks = _data.projectParts;
						projectPhaseFromTasks = _data.projectPhases;
						projectFromTasks = _data.projects;
						activityFromTasks = _data.activities;
						lisTaskRequests = _data.lisTaskRequests;
						renderCopyForecastPopup();
					}else{
						$('#copy_forecasts_table').html('<p class="error no-data">' + (response.message ? response.message : '<?php __('The data was not found, please try again');?>') + '</p>');
					}
				},
				error: function(){
					$('#copy_forecasts_table').html('<p class="error no-data"><?php __('The data was not found, please try again');?></p>');
				},
				complete: function(){
					$('#gs_loader').hide();
				},
			});
		}else{
			renderCopyForecastPopup();
		}
        $('#dialog_copy_forecasts').dialog({
            position    :'center',
            autoOpen    : false,
            autoHeight  : true,
            modal       : true,
            width       : widthPopup,
            open : function(e){
                var $dialog = $(e.target);
                $dialog.dialog({open: $.noop});
            }
        });
        $("#dialog_copy_forecasts").dialog('option',{title:'<?php __('Copy Forecast');?>'}).dialog('open');
    });
	

    $(".cancel").live('click',function(){
        $("#dialog_copy_forecasts").dialog('close');
        $("#dialog_import_CSV").dialog("close");
    });
    $("#ok_copy").live('click',function(e){
        $('#gs_loader').css('display', 'block');
        $("#form_dialog_copy_forecasts").submit();
    });
    
    var temp=setInterval( function(){
        $('.disable-edit-day').each(function(index, element) {
            var text=$(this).text();
            if(text=='')
            {
                //$(this).text('0.00');
            }
        });
        clearInterval(temp);
    },1000);
    //EXPAND TREE
    $(document).keyup(function(e) {
        if (window.event)
        {
            var value = window.event.keyCode;
        }
        else
            var value=e.which;
        if (value == 27) { collapseScreen(); }
    });
    function collapseScreen()
    {
        $('#table-control').show();
        $('.wd-title').show();
        $('#collapse').hide();
        $('#project_container').removeClass('fullScreen');
        $(window).resize();
    }
    function expandScreen()
    {
        $('#table-control').hide();
        $('.wd-title').hide();
        $('#project_container').addClass('fullScreen');
        $('#collapse').show();
        $(window).resize();
    }
    $('#dialog_import_CSV').dialog({
        position    :'center',
        autoOpen    : false,
        autoHeight  : true,
        modal       : true,
        width       : 360,
        height      : 150
    });
    $("#import_CSV").click(function(){
        $('.wd-input').show();
        $('#loading').hide();
        $("input[name='FileField[csv_file_attachment]']").val("");
        $(".error-message").remove();
        $("input[name='FileField[csv_file_attachment]']").removeClass("form-error");
        $(".type_buttons").show();
        $('#dialog_import_CSV').dialog("open");

    });
    $("#import-submit").click(function(){
        $(".error-message").remove();
        $("input[name='FileField[csv_file_attachment]']").removeClass("form-error");
        if($("input[name='FileField[csv_file_attachment]']").val()){
            var filename = $("input[name='FileField[csv_file_attachment]']").val();
            var valid_extensions = /(\.csv)$/i;
            if(valid_extensions.test(filename)){
                $('#uploadForm').submit();
            }
            else{
                $("input[name='FileField[csv_file_attachment]']").addClass("form-error");
                jQuery('<div>', {
                    'class': 'error-message',
                    text: 'Incorrect type file'
                }).appendTo('#error');
            }
            $("#dialog_import_CSV").dialog("close");
        }else{
            jQuery('<div>', {
                'class': 'error-message',
                text: 'Please choose a file!'
            }).appendTo('#error');
        }
    });
    
	var default_mess = cell_focus_id = column_field = '';
	var getCommentRequest = function(type, date, date_column, cell_id, column_id) {
		save_capacity = false;
		var _html = '';
		var _comment_default = date_column ? $this.t('Please justify to fill more than capacity by day or close the popup to cancel. Justification %s:', date_column) : '';
		default_mess = _comment_default;
		cell_focus_id = cell_id;
		column_field = column_id;       
		var popup = $('#template_logs');
		$('.wd-list-project').addClass('loading');
		_html += ('<div class="comment"><textarea onfocusout="addCommentRequest(\'' + type + '\')" data-date = '+ date +' cols="30" rows="6" id="update-comment">'+ _comment_default +'</textarea></div>');
		$.ajax({
			url: '/activity_forecasts/getCommentRequest',
			type: 'POST',
			data: {
				data:{
					date: date,
					type: type,
					employee_id: employeeName['id']
				}
			},
			dataType: 'json',
			success: function(data) {
				_html += '<div class="content-logs">';
				if (data) {
					save_capacity = true;
					$.each(data, function(ind, _data) {
						var idEm =  _data['employee_id'],
						comment = _data['comment'],
						created = new Date(_data['created'] * 1e3).toISOString().slice(0, 10);
						var avartarImage = '<span data-id="' + idEm + '" title="' + listEmployeeName[idEm]['fullname'] + '"><img src="' + js_avatar(idEm) + '" alt="avatar"></span>';
						_html += '<div class="content"><div class="avatar">'+ avartarImage +'</div><div class="item-content"><p>'+ created +'</p><div class="comment">'+ comment +'</div></div></div>';						
					});
				} else {
					_html += '';
				}
				_html += '</div>';
				$('#content_comment').html(_html);
				var createDialog2 = function(){
					$('#template_logs').dialog({
						position    :'center',
						autoOpen    : false,
						autoHeight  : true,
						modal       : true,
						width       : 500,
						minHeight   : 50,
						open : function(e){
							var $dialog = $(e.target);
							$dialog.dialog({open: $.noop});
						}
					});
					createDialog2 = $.noop;
				}
				createDialog2();
				$("#template_logs").dialog('option',{title: $this.t('Comment(s)')}).dialog('open');
				$('.wd-list-project').removeClass('loading');
				
			},
			complete: function(data) {
				$('#update-comment').focus().val("").val(_comment_default);
			}
		});
		
	    return save_capacity;
	};
	var data_view, date_row, data_item;
	var addCommentRequest = function(type){
      var text = $('#update-comment').val(),
        _date = $('#update-comment').data('date');
		var cell_value = $('.editor-text').val();
        if($.trim(text) != default_mess && $.trim(text) != ''){
            var _html = '';
			$('#update-comment').closest('#content_comment').addClass('loading');
            $.ajax({
                url: '/activity_forecasts/addCommentRequest/',
                type: 'POST',
                data: {
                    data:{
                        date: _date,
                        comment: text,
						type: type,
						employee_id: employeeName['id']
                    }
                },
                dataType: 'json',
                success: function(data){
                    if(data.result=='success'){
						data = data.data;
                        var idEm =  data['update_by'],
						comment = data['comment'],
						created = data['created'];
						var avartarImage = '<span data-id="' + idEm + '" title="' + listEmployeeName[idEm]['fullname'] + '"><img src="' + js_avatar(idEm) + '" alt="avatar"></span>';
						_html += '<div class="content"><div class="avatar">'+ avartarImage +'</div><div class="item-content"><p>'+ created +'</p><div class="comment">'+ comment +'</div></div></div>';	
                        $('#content_comment .content-logs').append(_html).scrollTop($('#content_comment .content-logs').height());
                        $('#update-comment').val("");
						has_comment = true;
					}
                },
				complete: function() {
					var dataView = gridControl.getDataView();
					var row = dataView.getRowById(cell_focus_id);
					var this_item = dataView.getItem(row);
					if ( this_item){
						id_att = '#'+this_item.id;
						$('.cell-value[data-field="' + column_field + '"]').click();
						$(id_att).find('.editor-text').val(cell_value);
						$('.slick-cell:first').click();
					}
					$('#update-comment').closest('#content_comment').removeClass('loading');
					has_comment = false;
				}
            });
			
        }
    }
	var timeout = 0;
	function notSelectWarning(){
		clearTimeout(timeout);
		$('#not-select-warning').show();
		$('#not-select-warning').css('opacity', 1);
		timeout = setTimeout(function(){
			$('#not-select-warning').css('opacity', 0);
		},2000);
	}
	function notSelectWarning(){
		clearTimeout(timeout);
		$('#not-select-warning').show();
		$('#not-select-warning').css('opacity', 1);
		timeout = setTimeout(function(){
			$('#not-select-warning').css('opacity', 0);
		},2000);
	}
	$('#open-popup-message').click(function(){
		date_column = 0;
		getCommentRequest('week', avg_date, date_column);
	});
	$('body').on('click','.timesheet-delete-task', function(e){
		e.preventDefault();
		var action = $(this).attr('href');
		var id = $(this).closest('.slick-row').attr("id");
		if(id){
			id = id.split("row-");
			id = id[1];
		}
		$.ajax({
			url: action,
			type: 'get',
			dataType : 'json',
			success: function(data){
				if(data == true && id){
					var dataView = gridControl.getDataView();
					var items = dataView.getItems();
					var _data = [];
					$.each(items, function(key, value){
						if(items[key]['id'] != id){
							_data[key] = items[key];
						}
					});
					dataView.beginUpdate();
					dataView.setItems( Object.values(_data));
					dataView.endUpdate();
					gridControl.render();
				}
            }
		});
	});
	function loadDataTimeshet(week, year){
		var id = $idManageValidation;
		var profit = $profit;
		var _url = '/activity_forecasts/request_ajax/week?week=' + week + '&year=' + year + '&id=' + $idManageValidation + '&profit=' + $profit + '&get_path=' + $getDataByPath;
		fillTimesheet = 0;
		has_comment = false;
		$.ajax({
			url: _url,
			type: 'get',
			dataType : 'json',
			beforeSend: function(){
				$('#project_container').addClass('loading');
			},
			success: function(data){
				listWorkingDays = data.listWorkingDays;
				capacity = data.capacity;
				holidays = data.holidays;
				dataSets = data.dataSets;
				gridView = data.gridView;
				listTask = data.lisTaskRequests;
				listActi = data.lisActivityRequests;
				listTaskDisplay = data.listTaskDisplay;
				listActivityDisplay = data.listActivityDisplay;
				activityNotAccessDeletes = data.activityNotAccessDeletes;
				taskNotAccessDeletes = data.taskNotAccessDeletes;
				checkFullDayActivities = data.checkFullDayActivities;
				checkFullDayTasks = data.checkFullDayTasks;
				canSend = data.canSend;
				if( !$.isEmptyObject(data.mapeds['activity'])){
					$.each(data.mapeds['activity'], function (k,v){
						activities[k] = v;
					});
				}
				if( !$.isEmptyObject(data.mapeds['task'])){
					$.each(data.mapeds['task'], function (k,v){
						tasks[k] = v;
					});
				}
				requestConfirm = data.requestConfirm;
				$this.canModified = (requestConfirm == -1 || requestConfirm == 1);
				requestConfirmInfo = data.requestConfirmInfo;
				avg_date = data.avg_date;
				var queryUpdate = data.queryUpdate;
				$this.url =  '<?php echo $html->url(array('action' => 'update_request')); ?>' + queryUpdate;
				$week = data.timesheet_info.week;
				$month = data.timesheet_info.month;
				$year = data.timesheet_info.year;
				start = data.start;
				end = data.end;
				$('#RequestWeek').val( $week);
				$('#CopyWeek').val( $week);
				$('#CopyMonth').val( $month);
				$('#RequestYear').val( $year);
				$('#CopyYear').val( $year);
				
				$('#form_dialog_copy_forecasts').prop('action', data.copyURL);
				$('#request-form').prop('action', '<?php echo $html->url(array('action' => 'response'));?>' + queryUpdate);
				window.history.pushState({week: $week, year: $year}, document.title, '<?php echo $html->url(array('action' => 'request'));?>' + queryUpdate);

				timesheet_update_form(requestConfirm, data.requestConfirmText);
				update_table_header();
				update_data_to_grid(data.columns, gridView, data.header_text);
				build_list_fields();
				$(window).trigger('resize');
			},
			complete: function(){
				$('#project_container').removeClass('loading');				
			}
		});
	}
	function update_table_header(){
		/**************************************************Phan forecast nam ben tren cua man hinh request ***********************/			
        var    $container      = $('#absence-table').html(''),
            dataSum = 0,
            output = _output = '',total = _tForecast = _tRemain = _tWorkload = 0, _workloads = [];
        if(typeSelect != 'week'){
            $containerFixed  = $('#absence-table-fixed').html('');
        }
        var _total = 0;
        $.each(listWorkingDays, function(k, val){
            _total++;
        });
        $.each(dataSets, function(id){
            var output  = '', total = _total;
            $.each(this ,function(day , data){
                var _day = day * 1000;
                _day = new Date(_day);
                _day = daysInWeek[_day.getDay()];
                var val     = parseFloat(workdays[_day]),
                    dt      = holidays[data.date] || {},
                    opt     = {value : ''};
                if(this.workload){
                    var name = '';
                    $.each(this.workload, function(ind, values){
                        ind = ind.split('-');
                        if(ind[0] === 'pr'){
                            name += projectFromTasks[ind[1]] ? '<div class="activity-tootip-forecast" rel="pr-'+ind[1]+'" day="'+day+'"><p>' + projectFromTasks[ind[1]] + '</p></div>' : '';
                        } else {
                            name += activityFromTasks[ind[1]] ? '<div class="activity-tootip-forecast" rel="ac-'+ind[1]+'" day="'+day+'"><p>' + activityFromTasks[ind[1]] + '</p></div>' : '';
                        }
                    });
                    opt.value = name;
                }
                switch(val){
                    case 1:
                        if(dt['am']){
                            if(dt['pm']){
                                opt.value += '<div><p><span style="color: red;">' + $this.t('Holiday') + '</span></p></div>';
                                total -= 1;
                            } else {
                                opt.value += '<div><p><span style="color: red;">' + $this.t('Holiday') + '</span></p></div>';
                                total -= 0.5;
                            }
                        } else {
                            if(dt['pm']){
                                opt.value += '<div><p><span style="color: red;">' + $this.t('Holiday') + '</span></p></div>';
                                total -= 0.5;
                            } else {

                            }
                        }
                        break;
                    case 0.5:
                        if(dt['am']){
                            opt.value += '<div><p><span style="color: red;">' + $this.t('Holiday') + '</span></p></div>';
                            total -= 0.5;
                        } else {

                        }
                        break;
                }
                if(data['absence_am'] && data['response_am'] == 'validated'){
                    if(data['absence_pm'] && data['response_pm'] == 'validated'){
                        if(absences[data['absence_am']].print === absences[data['absence_pm']].print){
                            opt.value += '<div><p><span style="color: red;">' + absences[data['absence_am']].print + '</span></p></div>';
                        } else {
                            opt.value += '<div><p><span style="color: red;">' + absences[data['absence_am']].print + '</span></p></div>'
                            + '<div><p><span style="color: red;">' + absences[data['absence_pm']].print + '</span></p></div>';
                        }
                        total -= 1;
                    } else {
                        opt.value += '<div><p><span style="color: red;">' + absences[data['absence_am']].print + '</span></p></div>';
                        total -= 0.5;
                    }
                } else {
                    if(data['absence_pm'] && data['response_pm'] == 'validated'){
                        opt.value += '<div><p><span style="color: red;">' + absences[data['absence_pm']].print + '</span></p></div>';
                        total -= 0.5;
                    } else {

                    }
                }
                if(listWorkingDays[day]){
                    $.each(opt, function(index, vals){
                        output+= '<td class="foreDayCols" >' + vals + '</td>';
                    });
                }
            });
            if((typeSelect == 'week' || typeSelect == 'month') && (requestConfirm == -1 || requestConfirm == 1)){
                output += '<td  style="border-right: 1px solid #ccc !important;"></td>';
            }
            var $class = '';
            if(dataSum > total){
                $class = 'check-workload';
            }
            _top = dataSum.toFixed(2) + '/' + total*ratio;
            if( isMulti )_top = '';
            if(typeSelect === 'week'){
                $container.append('<tr><td rowspan="2"><span class="employee-top"><img src="' + js_avatar(id) + '" alt="avatar" />' + employees[id]
                     + ' ('+ profit_name +') </span><td class="' + $class + '" rowspan="2"><span>' + '' + '</span></td>' + output + '</tr>'
                     + '<tr>' + _output + '</tr>'
                );
            } else {
                $containerFixed.append('<tr><td rowspan="2"><span class="employee-top"><img src="' + js_avatar(id) + '" alt="avatar" />' + employees[id]
                     + ' ('+ profit_name +') </span><td class="' + $class + '" rowspan="2"><span>' + _top + '</span></td></tr>'

                );
                $container.append('<tr>' + output + '</tr>');
            }
        });
	}
	function timesheet_update_form(requestConfirm, requestConfirmText){
		$('.activity-request-status').html(requestConfirmText);
		$('.title-timesheet-action').hide();
		switch(requestConfirm){
			case '0': 
				$('#action-validated').show();
			case '2':
				$('#action-sent-validated').show();
				break;
			default:  
				$('.title-timesheet-action').not('#action-inprogress').hide();
				$('#action-inprogress').show();
				break;
		}
		initDatePicker('#date-range-picker');
	}
	function update_data_to_grid(input_cols, data, header_text){
		// header and absence
		var index = 0;
		$.each(listWorkingDays, function(i, date){
			$('#absence').find('thead .foreDayCols').eq(index).find('a').attr('onclick', 'getCommentRequest("date",' + date + ')');
			index++;
		});
		$.each(header_text, function( _class, _text){
			$('#absence').find('thead .foreDayCols.' + _class).children('span').text(_text);
		});
		// header and absence
		var sf_key = ['editor', 'formatter', 'validator', 'sorter'];
		var cols = [];
		$.each(input_cols, function(ind, _col){
			var col = [];
			$.each(_col, function(key, val){
				if( $.inArray(key,sf_key) != -1){
					val = val.split('.');
					var x = 0;
					$.each(val, function(i,k){
						if( !x) x = window[k];
						else x = x[k];
					});
					col[key] = x;
				}else{
					col[key] = val;
				}
			}); 
			cols[ind] = col;
		});
		gridControl.setColumns(cols);
		gridControl.getData().setItems(data);
		gridControl.setOptions({
			enableAddRow : $this.canModified,
			editable: $this.canModified,
		});
		gridControl.invalidate();
		gridControl.render();
	}
</script>
<div id="collapse" onclick="collapseScreen();" ><button class="btn btn-esc"></button></div>
<style>
#absence-fixed th {
    padding-top: 3px !important;
    padding-bottom: 3px !important;
}
.wd-list-project{
	position: relative;
}
.wd-list-project:after{
	content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background: rgba(0,0,0,0.4) url(/img/fancybox_loading.gif) center no-repeat;
    z-index: 0;
    opacity: 0;
    transition: all 0.2s ease;
	visibility: hidden;
}
.wd-list-project.loading:after{
	z-index: 20;
    opacity: 1;
	visibility: visible;
}

</style>