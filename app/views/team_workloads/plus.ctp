<script>
var _show_in_config = {
	show_program: ( typeof companyConfigs.team_workload_show_program == 'undefined') ? 1 : parseInt(companyConfigs.team_workload_show_program),
	show_sub_program: ( typeof companyConfigs.team_workload_show_sub_program == 'undefined') ? 1 : parseInt(companyConfigs.team_workload_show_sub_program),
	show_priority: ( typeof companyConfigs.team_workload_show_priority == 'undefined') ? 1 : parseInt(companyConfigs.team_workload_show_priority),
	show_total: ( typeof companyConfigs.team_workload_show_total == 'undefined') ? 1 : parseInt(companyConfigs.team_workload_show_total),
	show_ref: ( typeof companyConfigs.team_workload_show_ref == 'undefined') ? 1 : parseInt(companyConfigs.team_workload_show_ref),
};
</script>
<?php

echo $html->script('jquery-1.12.4/jquery.min.js');
echo $html->script('bootstrap-3.3.7/bootstrap.min.js');
echo $html->script('d3/d3.min.js');
echo $html->script('d3/pdc.js?ver=1.1');
echo $html->script('less.js-2.7.1/less.min.js');
echo $html->script('z0.history');
echo $html->script('jquery.scrollTo');
echo $html->script('jquery.multiSelect.wdcustom.js');
echo $html->script('multiple-select.js');
echo $html->script('z0.main');
echo $html->script('jquery-ui-1.10.3/jquery-ui.min.js');
echo $html->script('jquery-ui.multidatespicker');
echo $html->script('qtip/jquery.qtip');
echo $html->script('jquery.mousewheel.3');
echo $html->script('jquery.form');
echo $html->script('history_filter');
// echo $html->script('xlsx.min');
// echo $html->script('special_task');
echo $html->css('/js/qtip/jquery.qtip');
echo $html->css('jquery.multiSelect.css');
echo $html->css('bootstrap-3.3.7/bootstrap.min.css');
echo $html->css('pdc.css');
echo $html->css(array(
	'preview/datepicker-new',
    'add_popup')
);
?>
<style type="text/css">
	body #wd-container-main .wd-layout{
		padding-right: 20px;
		overflow: auto;
	}
	body #layout{
		min-height: 0;
	}
    #wd-container-header-main #wd-container-header h1.wd-logo{
        font-size: 12px;
        margin: 0;
    }
    .name-employee b{
        font-weight: normal;
    }
    #scrollLeftAbsence {
        position: absolute;
        top: 0;
        right: 0px;
		width: 20px;
    }
    #right-scroll {
        /* max-height: 750px; */
        overflow-y: hidden;
        overflow-x: auto;
        margin-right: -5px;
    }
    .wd-content-project{
        width: 100%;
        // height: 800px;
        overflow-x: auto;
        overflow-y: hidden;
    }
    .input-hidden{
        display: none;
    }
    .modal-content{
        height: 595px;
        width: 500px;
        box-shadow: none;
        border-radius: 0;
    }
    #sub-nav a {
        font-size: 82.5%;
    }
    #wd-top-nav a span.tab-center{
        font-size: 83%;
    }
    .name-employe span{
        font-size: 83%;
    }
    .ms-drop.top, .ms-drop.bottom{
        padding-left: 10px;
    }
    .third-nav {
        position: absolute;
        top: 3em;
        right: 4.3em;
        display: inline-block;
    }
    .modal-body span{
        font-size: 11px;
    }
    #comment-ct{
        max-height: 500px;
        overflow-x: hidden;
        overflow-y: auto;
    }
    .comment-ct{
        min-height: 50px;
    }
    .comment-btn textarea{
        width: calc(100% - 50px);
        padding: 8px;
        line-height:1.5;
        font:13px Tahoma, cursive;
        max-height: 400px;
        overflow-x: hidden;
        overflow-y: auto;
    }
    .submit-btn-msg{
        width: 50px;
        height: 50px;
        border: none;
        vertical-align: top;
        color: #fff;
        text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.3);
        position: relative;
        font-size: 20px;
        background-color: #56aaff !important;
    }
    
    .input-project{
        height: 20px;
        width: 95%;
        margin-right: 10px;
        margin-bottom: 10px;
        padding-left: 10px;
    }
    .modal-body{
        padding: 0;
        padding-top: 20px;
    }
    .hidden{
        display: none;
    }
    .subscribe{
        margin-top: -15px;
        font-size: 15px;
        font-weight: bold;
        margin-bottom: -29px;
        font: normal normal 100%/1.35 Arial, Helvetica, sans-serif;
    }
    .project-title{
        max-width: 1200px;
        margin-left: 50px;
        width: 80%;
        margin-top: 70px;
    }
    .btn-order{
        margin-bottom: 30px;
        border: none;
        padding: 5px;
        background-color: #fff;
        color: #fff;
        margin-right: 20px;
    }
    .subscribe span{
        font-size: 15px;
    }
    .btn-order:hover{
        cursor: pointer;
    }
    .btn-order img{
        height: 32px;
        margin-top: -15px;
        outline : none;
        border : 0;
        -moz-outline-style: none;
    }
    .btn-order:focus{
        outline:0;
    }
    .right-avatar{
        float: right;
        margin-top: -70px;
        clear: both;
    }
    .right-avatar img{
        height: 40px;
        width: 40px;
        border-radius: 30px;
        margin-right: 10px;
    }
    .show-hide img{
        height: 30px;
    }
    
    .show-hide img:hover{
        cursor: pointer;
    }
    .hidden-div{
        display: none;
    }
    .message-new-ct{
        float: right;
        background-color: red;
        color: #fff;
        border-radius: 50%;
        width: 20px;
        text-align: center;
        display: block;
        height: 20px;
        margin-top: 5px;
        margin-right: 5px;
        line-height: 20px;
    }
    .wd-tab .wd-panel h2.wd-t3{
        font-size: 16px;
        font-weight: bold;
        padding: 6px;
    }
    .wd-table{
        background: transparent !important;
    }
    .qtip-content {
        font-size: 12px;
    }
    .qtip-title {
        font-size: 13px;
        padding-top: 3px;
        padding-bottom: 3px;
    }
    .tooltip-workload {
        overflow: hidden;
        padding: 5px 0;
        border-bottom: 1px solid #ADD9ED;
    }
    .tooltip-workload:last-child {
        margin-bottom: 5px;
    }
    .tooltip-consume,
    .tooltip-name {
        display: inline-block;
        float: left;
        width: auto;
        padding: 5px 5px 5px 0;
    }
    .tooltip-consume {
        color: #333;
    }
    .tooltip-input {
        width: 60px;
        float: left;
        padding: 3px 5px;
        border: 1px solid #ADD9ED;
        background: #fff;
        margin-right: 5px;
    }
    .tooltip-form button {
        border-radius: 3px;
        cursor: pointer;
        min-width: 30px;
        padding: 3px 5px;
        border: 1px solid #ADD9ED;
        background: #efe08b;
        margin-right: 5px;
        background: #addef4;
        background: -moz-linear-gradient(top,  #addef4 0%, #52bff2 100%);
        background: -webkit-linear-gradient(top,  #addef4 0%,#52bff2 100%);
        background: linear-gradient(to bottom,  #addef4 0%,#52bff2 100%);
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#addef4', endColorstr='#52bff2',GradientType=0 );
    }
    /* comment popup style*/

    .comment{
        margin-top: 5px;
        width: 98%;
        height: 16px;
        background: #fff;
        border: 1px solid transparent;
        transition: border-color 0.5s;
    }
    .name h5{
        float: left;
        color: #08c;
        font-size: 14px;
        display: inline-block;
        padding: 0;
        margin: 0;
        font-weight: bold;
    }
    .name em{
        font-size: 12px;
        color: #888;
        display: inline-block;
        margin-left: 10px;
        vertical-align: top;
    }
    .avartar-image{
        display: inline-block;
        vertical-align: top;
        margin-top: 2px;
    }
    .avartar-image-img {
        width: 40px;
        height: 40px;
        margin: 0 10px;
        padding: 5px;
        border: 1px solid #bbb;
        border-radius: 3px;
    }  
    .modal-header{
        padding: 12px;
    }
    .content-comment {
        display: inline-block;
        width: calc( 100% - 65px);
    } 
    #content_comment .content{
        margin-bottom: 20px
    }
    .comment {
        margin-top: 5px;
        width: 98%;
        height: 16px;
        background: #fff;
        border: 1px solid transparent;
        transition: border-color 0.5s;
        font-size: 13px;
        color:#000;
    }
    .modal-body .content{
        margin-bottom: 20px;
    }
    .modal-backdrop{
        display: none;
    }
    .tooltip-form button.tooltip-cancel {
        float: right;
        margin-right: 0;
    }
    .modal-title{
        font-size: 13px;
        font-weight: bold;
    }
    .comment-btn textarea{
        border: none;
        border-top: 1px solid #ccc
    }
    #collapse{
        position: fixed;
        right: 10px;
    }
    <?php if(count($_period) <= 2){ ?>
        #capacity-row td{
            width: 80px;
        }
    <?php }else if(count($_period) <= 3){?>
        #capacity-row td{
            width: 76px;
        }
    <?php }else if(count($_period) <= 4){?>
        #capacity-row td{
            width: 74px;
        }
    <?php }else if(count($_period) <= 5){?>
        #capacity-row td{
            width: 71px;
        }
    <?php }else if(count($_period) <= 6){ ?>
        #capacity-row td{
            width: 68px;
        }
    <?php }else if(count($_period) <= 7){ ?>
        #capacity-row td{
            width: 65px;
        }
    <?php }else if(count($_period) <= 8){ ?>
        #capacity-row td{
            width: 63.5px;
        }
    <?php }else if(count($_period) <= 9){ ?>
        #capacity-row td{
            width: 61.5px;
        }
    <?php } ?>
    @font-face {
    font-family: 'Glyphicons Halflings';
    src: url('../fonts/glyphicons-halflings-regular.eot');
    src: url('../fonts/glyphicons-halflings-regular.eot?#iefix') format('embedded-opentype'),
        url('../fonts/glyphicons-halflings-regular.woff2') format('woff2'),
        url('../fonts/glyphicons-halflings-regular.woff') format('woff'),
        url('../fonts/glyphicons-halflings-regular.ttf') format('truetype'),
        url('../fonts/glyphicons-halflings-regular.svg#glyphicons_halflingsregular') format('svg');
    }
	.ui-dialog{font-size:11px;}.ui-widget{font-family:Arial,sans-serif;}#dialog_import_CSV label{color:#000;}.buttons ul.type_buttons{padding-right:10px!important;}.type_buttons .error-message{background-color:#FFF;clear:both;color:#D52424;display:block;width:212px;padding:5px 0 0;}.form-error{border:1px solid #D52424;color:#D52424;}#dialog_import_MICRO label{color:#000;}
	
   
    .multiSelect, .multiSelectOptions {
        width: 585px !important;
    }
    .multiSelect span {
        width: 285px !important;
        word-wrap: break-word;
    }
    .multiSelect {
        border-color: #bbb !important;
        padding: 1px !important;
        height: auto !important;
        margin-bottom: 5px !important;
    }
    .multiSelectOptions label {
        clear: both;
    }
    .multiSelectOptions label input {
        border: none !important;
        background: transparent  !important;
    }
   
    .ui-datepicker-calendar td,
    .ui-datepicker-calendar th {
        padding: 0;
        background-color: #fff;
    }
    .ui-datepicker-calendar td a,
    .ui-datepicker-calendar td a:hover,
    .ui-datepicker-calendar td span {
        padding: 3px;
        display: block;
        border: 1px solid transparent;
    }
    .btn1 {
        background-attachment: scroll;
        background-clip: border-box;
        background-color: transparent;
        background-image: url("/img/front/bg-add-project.png");
        background-origin: padding-box;
        background-position: left top;
        background-repeat: no-repeat;
        background-size: auto auto;
        color: #FFFFFF;
        display: inline-block;
        height: 33px;
        line-height: 33px;
        padding-left: 27px;
        text-decoration: none;
        border: 0;
        cursor: pointer !important;
    }
    .btn1 span {
        background-attachment: scroll;
        background-clip: border-box;
        background-color: transparent;
        background-image: url("/img/front/bg-add-project-right.png");
        background-origin: padding-box;
        background-position: right top;
        background-repeat: no-repeat;
        background-size: auto auto;
        display: block;
        height: 33px;
        padding-bottom: 0;
        padding-left: 2px;
        padding-right: 15px;
        padding-top: 0;
        font-size: 13px;
        text-decoration: none;
    }
    .btn1:hover {
        background-position: bottom left;
    }
    .btn1:hover span {
        background-position: bottom right;
    }
    .btn2 {
        background-attachment: scroll;
        background-clip: border-box;
        background-color: transparent;
        background-image: url("/img/front/bg-reload.png");
        background-origin: padding-box;
        background-position: left top;
        background-repeat: no-repeat;
        background-size: auto auto;
        display: inline-block;
        height: 33px;
        width: 81px;
        color: #000 !important;
        line-height: 33px;
        padding-left: 30px;
        text-decoration: none;
        border: 0;
        cursor: pointer !important;
    }
    .btn2:hover {
        background-position: bottom left;
    }
    .hide-on-mobile {
        <?php if( $isMobile ): ?>display: none !important;<?php endif ?>
    }
    #nct-range-type:disabled {
        background: #eee;
    }
	
	#wd-container-main {
		max-width: 1920px;
	}
	.wd-full-popup .wd-popup-inner{
		text-align: left;
	}
	#assign-table .remove-cell img{
		vertical-align: middle;
	}
	#assign-table td.remove-cell{
		width: 40px;
		text-align: center !important;
		padding-left: 0 !important;
		cursor: pointer;
	}
	#assign-table td.remove-cell img + img{
		display: none;
	}
	#assign-table td.remove-cell:hover img{
		display: none;
	}
	#assign-table td.remove-cell:hover img + img{
		display: inline-block;
	}
	#save-special{
		background: #217FC2;
		padding-left: 34px;
		padding-right: 34px;
	}
	.wd-submit-row .btn-form-action {
		font-size: 14px;
		line-height: 22px;
		font-weight: 600;
		text-transform: uppercase;
		color: #fff;
		border: none;
		padding: 14px 27px;
		background-color: #C6CCCF;
		transition: all 0.3s ease;
		border-radius: 3px;
		text-decoration: none;
		background-size: 250%;
		background-image: linear-gradient(to right,#C6CCCF 0%,#C6CCCF 39%,#217FC2 64%,#217FC2 100%);
		display: inline-block;
		margin-right: 16px;
	}
	#save-special{
		float: right;
		margin-right: 0;
	}
	.wd-multiselect.multiselect .circle-name span{
		line-height: 25px;
	}
	.wd-multiselect.multiselect .circle-name{
		vertical-align: middle;
		margin-right: 5px;
	}
	.wd-multiselect .wd-combobox a.circle-name{
		vertical-align: bottom;
	}
	.ui-widget-content .ui-datepicker-calendar .ui-state-default{
		padding: 0;
	}
	.form-style-2019{
		padding-left: 0;
		padding-right: 0;
	}
	#cancel-special{
		margin-right: 0;
	}
	.wd-submit-actions{
		padding-bottom: 20px;
	}
	.wd-col-autosize {
		max-width: 830px;
		overflow: auto;
	}
	.nct-assign-table tr td .circle-name{
		display: inline-block;
	}
	.ui-widget-overlay{
		z-index: 99;
	}
	.ui-dialog{
		z-index: 999;
	}
	#special-task-info #assign-list{
		max-height: 700px;
	}
	table.table .pdc-header-table tr th{
		padding: 0;
		line-height: inherit;
		vertical-align: middle;
	}
	table.table .pdc-header-table tr th span{
		line-height: 50px;
		display: block;
	}
	table.table .pdc-header-table tr th.month-td span{
		line-height: 25px;
	}
	
	.option-bar .wd-right {
		width: auto;
		display: inline-block;
		vertical-align: middle;
		margin: 0;
		margin-right: 6px;
		float: right;
	}
	.wd-input.wd-checkbox-switch label .wd-btn-switch {
		position: relative;
		top: 10px;
	}
	.wd-input.wd-checkbox-switch label .wd-btn-switch {
		width: 38px;
		height: 18px;
		border: 1px solid #E1E6E8;
		background: #FFFFFF;
		overflow: visible;
		border-radius: 10px;
		position: relative;
	}
	.wd-input.wd-checkbox-switch label .wd-btn-switch {
		display: inline-block;
		vertical-align: middle;
		margin-right: 4px;
		cursor: pointer;
	}
	label .wd-btn-switch:before {
		content: '';
		position: absolute;
		width: 18px;
		height: 18px;
		border-radius: 50%;
		border: 1px solid #E1E6E8;
		background: #FFFFFF;
		left: -1px;
		top: -1px;
		transition: all 0.2s ease;
	}
	input:checked ~ label .wd-btn-switch:before {
		content: '';
		position: absolute;
		width: 18px;
		height: 18px;
		border: 2px solid #247FC3;
		top: -1px;
		left: calc( 100% - 18px);
	}
</style>
<!--[if !IE]><!-->
    <style type="text/css">
    .close-btn {
        right: 11px;
    }
    </style>
 <!--<![endif]-->
<!-- /export excel  -->
<?php
$columnConfig = array(
	'team_workload_show_program' => array(
		'name' => __d(sprintf($_domain, 'Details'), "Program", null),
		/* https://api.cakephp.org/2.3/source-function-__d.html#599-620
		Thêm args == null để return value */ 
		'target' => '.commitee-td',
	),
	'team_workload_show_sub_program' => array(
		'name' => __d(sprintf($_domain, 'Details'), "Sub program", null),
		'target' => '.version-td',
	),
	'team_workload_show_priority' => array(
		'name' => __("Priority", true),
		'target' => '.priority-td',
	),
	'team_workload_show_total' => array(
		'name' => __("Totals", true),
		'target' => '.totals-td, .team-total-td',
	),
	'team_workload_show_ref' => array(
		'name' => __("Ref.", true),
		'target' => '.task-reference, .conso-td.ref-td',
	),
);
$isShowPopup = !empty($filter_render) && !empty($filter_render['show_popup']) && $filter_render['show_popup'][0] == 1 ? true : false;
?>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="option-bar">
                <div class="search-container">
                    <input type="text" class="form-control input-sm" id="search-text" placeholder="Recherche" />
                    <span class="glyphicon glyphicon-remove-circle close-btn"></span>
                </div>
                <button type="button" class="btn btn-sm btn-default btn-success active" id="timesheet-btn"><?php echo __('Consumed', true) ?></button>
                <div class="dropdown" id="team-select-container">
                    <button class="btn btn-sm btn-default dropdown-toggle" type="button" id="team-select" data-toggle="dropdown">
                        <?php echo __('Teams', true) ?> <span class="caret"></span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="team-select">
                        <div class="dropdown-link"><?php echo __('Clear selection', true) ?></div>
                        <div class="dropdown-options"></div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-default" id="capacity-btn"><?php echo __('Capacity', true) ?></button>
                <div class="dropdown" id="export-container">
                    <button class="btn btn-sm btn-default dropdown-toggle" type="button" id="export-btn" data-toggle="dropdown">
                        <?php echo __('Export', true) ?> <span class="caret"></span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="export-btn">
                        <div class="dropdown-link" data-export="workload"><?php echo __('Workload', true) ?></div>
                        <div class="dropdown-link" data-export="timesheet"><?php echo __('Consumed', true) ?></div>
                        <div class="dropdown-link" data-export="capacity"><?php echo __('Capacity', true) ?></div>
                    </div>
                </div>
                <?php if($is_sas || (!$is_sas && $employee_info['Role']['name'] =='admin')){ ?>
					<div class="dropdown" id="setting-container">
						<a class="dropdown-toggle" type="button" id="setting-btn" data-toggle="dropdown" title="<?php __('Setting');?>">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
							  <defs>
								<style>
								  .cls-1 {
									fill: #666;
									fill-rule: evenodd;
								  }
								</style>
							  </defs>
							  <path id="SETTINGS" class="cls-1" d="M769.337,27.642l-1.549.9a7.482,7.482,0,0,1,0,2.912l1.549,0.9a1.339,1.339,0,0,1,.483,1.821L768.5,36.487a1.316,1.316,0,0,1-1.806.488l-1.565-.911a7.905,7.905,0,0,1-2.484,1.47v1.132A1.328,1.328,0,0,1,761.32,40h-2.644a1.328,1.328,0,0,1-1.323-1.333V37.534a7.894,7.894,0,0,1-2.484-1.47l-1.565.911a1.316,1.316,0,0,1-1.806-.488l-1.323-2.309a1.339,1.339,0,0,1,.485-1.821l1.548-.9a7.431,7.431,0,0,1,0-2.912l-1.548-.9a1.339,1.339,0,0,1-.485-1.821l1.323-2.309a1.316,1.316,0,0,1,1.806-.488l1.565,0.911a7.879,7.879,0,0,1,2.484-1.47V21.333A1.328,1.328,0,0,1,758.676,20h2.644a1.328,1.328,0,0,1,1.323,1.333v1.132a7.89,7.89,0,0,1,2.484,1.47l1.565-.911a1.317,1.317,0,0,1,1.806.488l1.322,2.309A1.339,1.339,0,0,1,769.337,27.642Zm-0.992-1.732-0.662-1.155a0.658,0.658,0,0,0-.9-0.244l-1.837,1.07a6.58,6.58,0,0,0-3.623-2.114V22a0.664,0.664,0,0,0-.661-0.666h-1.322a0.663,0.663,0,0,0-.661.666v1.468a6.586,6.586,0,0,0-3.624,2.114l-1.836-1.07a0.66,0.66,0,0,0-.9.244l-0.661,1.155a0.67,0.67,0,0,0,.243.911l1.844,1.074a6.47,6.47,0,0,0,0,4.21l-1.844,1.074a0.669,0.669,0,0,0-.243.91l0.661,1.155a0.659,0.659,0,0,0,.9.244l1.836-1.069a6.59,6.59,0,0,0,3.624,2.114V38a0.663,0.663,0,0,0,.661.667h1.322A0.664,0.664,0,0,0,761.32,38V36.532a6.581,6.581,0,0,0,3.623-2.114l1.837,1.069a0.658,0.658,0,0,0,.9-0.244l0.662-1.155a0.669,0.669,0,0,0-.243-0.91L766.258,32.1a6.469,6.469,0,0,0,0-4.21L768.1,26.82A0.67,0.67,0,0,0,768.345,25.91ZM760,33.333A3.334,3.334,0,1,1,763.3,30,3.32,3.32,0,0,1,760,33.333ZM760,28a2,2,0,1,0,1.983,2A1.992,1.992,0,0,0,760,28Z" transform="translate(-750 -20)"></path>
							</svg>
						</a>
						<div class="dropdown-menu" aria-labelledby="setting-btn">
							<div class="dropdown-options">
								<?php 
								foreach($columnConfig as $key => $val){ 
									$companyConfigs[$key] = isset( $companyConfigs[$key]) ? $companyConfigs[$key] : 1;
									echo $this->Form->input($key, array(
										'type' => 'checkbox',
										'label' => $val['name'],
										'name' => $key,
										'onchange' => "updateConfig('" . $key ."' , this.value, '" . $val['target'] . "');",
										"class" => $key,
										"checked" => isset( $companyConfigs[$key]) ? $companyConfigs[$key] : 1,
										"rel" => "no-history"
									));
								} ?>
							</div>
						</div>
					</div>
				<?php } ?>
				<a href="javascript:void(0);" onclick="expandScreen();" id="expand-btn" class="btn btn-fullscreen"></a>
				<div class="wd-right">
					<?php echo $this->Form->input('show_popup', array(
						'type' => 'checkbox',
						'class' => 'hidden',
						'label' => '<span class="wd-btn-switch"><span></span></span>',
						'checked'=> !empty($isShowPopup) ? true : false,
						'name' => 'show_popup',
						'div' => array(
							'class' => 'wd-input wd-checkbox-switch wd-show-popup',
							'title' => __('Activate popup even for single resource',true),
						),
						// 'onChange' => 'display_dashboard()',
						'type' => 'checkbox', 
					));
					?>
				</div>
            </div>
			<div class="capacity-container table-container" style="display: none;">
			<?php // ob_clean(); debug( $companyConfigs); exit;?>
				<table class="table capacity-table">
					<thead>
						<tr>
							<th class="team-td"><?php echo __('Teams', true) ?></th>
							<th class="task-td"><?php echo __('Ref.', true) ?></span></th>
							<th class="workload-td total-td" data-toggle="tooltip" data-placement="left">
								<span class="sortable" data-sort="teamWorkload"><?php echo __('Total', true) ?></span></th>
							<th class="task-comment-td last-month" data-toggle="tooltip" data-placement="left"><?php echo __('Msg.', true) ?></th>
						</tr>
					</thead>
					<tbody id="capacity-row">
					</tbody>
				</table>
			</div>
			<div class="wd-content-project">	
				<div class="header-container">
					<table class="table pdc-header-table">
						<thead class="pdc-header-table">
							<tr>
								<th class="commitee-td <?php if( empty($companyConfigs['team_workload_show_program'])) echo ' d3-hidden'; ?>"><span class="sortable" data-sort="commitee"><?php __d(sprintf($_domain, 'Details'), "Program") ?></span></th>
								<th class="version-td <?php if( empty($companyConfigs['team_workload_show_sub_program'])) echo ' d3-hidden'; ?>"><span class="sortable sorted" data-sort="version"><?php __d(sprintf($_domain, 'Details'), "Sub program") ?></span></th>
								<th class="name-td"><span class="sortable" data-sort="name"><?php echo __('Project', true) ?></span></th>
								<th class="priority-td <?php if( empty($companyConfigs['team_workload_show_priority'])) echo ' d3-hidden'; ?>"><span class="sortable" data-sort="priority"><?php echo __('Priority', true) ?></span></th>
								<th class="total-td totals-td total-header<?php if( empty($companyConfigs['team_workload_show_total'])) echo ' d3-hidden'; ?>" data-toggle="tooltip" data-placement="left" colspan="2">
									<span class="sortable" data-sort="totalWorkload"><?php echo __('Totals', true) ?></span></th>
								<th class="team-td"><?php echo __('Teams', true) ?></th>
								<th class="task-td"><?php echo __('Tasks', true) ?></span></th>
								<th class="workload-td task-reference <?php if( empty($companyConfigs['team_workload_show_ref'])) echo ' d3-hidden'; ?>" data-toggle="tooltip" data-placement="left"><?php echo __('Ref.', true) ?></span></th>
								<th class="workload-td total-td" data-toggle="tooltip" data-placement="left">
									<span class="sortable" data-sort="teamWorkload"><?php echo __('Total', true) ?></span></th>
								<th class="task-comment-td last-month" data-toggle="tooltip" data-placement="left"><?php echo __('Msg.', true) ?></th>
							</tr>
						</thead>
						<tbody id="capacity-row">
						</tbody>
					</table>
				</div>
				<div class="pdc-container">
					<div id="right-scroll">
						<table class="table pdc-table"></table>
					</div>
				</div>
				<div id="scrollLeftAbsence">
                    <div id="scrollLeftAbsenceContent"></div>
                </div>
                <div class="modal" id="commentsPopup" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="commentsTitle"></h4>
                            </div>
                            <div class="modal-body">
                            </div>
                            <div class="comment-btn">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal" id="loadingPopup" tabindex="-1" role="dialog">
                  <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div id="loading-icon">
                            <span class="glyphicon glyphicon-refresh spinning"></span>
                            <span>Chargement des données...</span>
                        </div>
                    </div>
                  </div>
                </div>
                <div id="tooltip-template" class="hidden">
                    <form class="tooltip-form" method="post" action="<?php echo $this->Html->url('/team_workloads/saveWorkload') ?>">
                        <div class="tooltip-assign">
                            <div class="tooltip-workload">
                                <input class="tooltip-input" type="text">
                                <b class="tooltip-consume"></b>
                                <b class="tooltip-name"></b>
                            </div>
                        </div>
                        <input type="hidden" name="data[id]" class="tooltip-id">
                        <input type="hidden" name="data[month]" class="tooltip-month">
                        <input type="hidden" name="data[type]"  class="tooltip-type">
                        <button class="tooltip-cancel" type="button"><?php __('Cancel') ?></button>
                        <button class="tooltip-ok"><?php __('OK') ?></button>
                    </form>
                </div>
                <div id="collapse" onclick="collapseScreen();" ><button class="btn btn-esc"></button></div>
                <?php
                // get capacity
                foreach ($listTeamIds as $id) {
                    $args = array(
                        'type' => 1,
                        'view_by' => 'month',
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'summary' => 1,
                        'pc' => $id,
                    );
                    $extra = $this->requestAction(array('controller' => 'new_staffing', 'action' => 'index', '?' => $args));
                    $_capacity[$listTeam[$id]] = !empty($extra['summary']['capacity']) ? $extra['summary']['capacity'] : $zeroCapa;
                }
                $listAvartar = array();
                $listIdEm = array_keys($listEmployee);
                foreach ($listIdEm as $_id) {
                    $link = $this->UserFile->avatar($_id, "small");
                    $listAvartar[$_id] = $link;
                }
                ?>
            </div>
        </div>
    </div>
</div>
<div id="special-task-info" style="display:none" title="Task info" class="buttons wd-full-popup autosize-popup">
    <div class="wd-popup-inner">
		<div class="form-style-2019">
			<div class="wd-inline-col wd-col-530">
				<input type="hidden" id="nct-id" class="text" />
				<input type="hidden" id="nct-phase-id" class="text" />
				<input type="hidden" id="nct-project-id" class="text" />
				<div class="wd-input">
					<?php 
						echo $this->Form->input('task.task_title', array(
							'type'=> 'text',
							'id' => 'nct-name',
							'label' => __d(sprintf($_domain, 'Project_Task'), 'Task', true),
							'required' => true,
							'rel' => 'no-history',
							'div' => array(
								'class' => 'wd-input label-inline has-val required'
							)
						));
					?>
				</div>
				<div class="wd-input wd-area wd-none required">
					<div class="wd-multiselect multiselect popupnct_nct_list_assigned" id="multiselect-popupnct-pm">
						<a href="javascript:void(0);" class="wd-combobox wd-project-manager disable">
							<p>
								<?php  __d(sprintf($_domain, 'Project_Task'), 'Assigned To');?>
							</p>
						</a>
						<div class="wd-combobox-content task_assign_to_id" style="display:none;">
							<div class="context-menu-filter">
								<span>
									<input type="text" class="wd-input-search" placeholder="<?php __('Search...');?>" rel="no-history">
								</span>
							</div>
							<div class="option-content"></div>
						</div>
					</div>
				</div>
				<div class="wd-row">
					<div class="wd-col wd-col-sm-6">
						<?php 
						$list_status = !(empty($projectStatus)) ? Set::combine($projectStatus, '{n}.ProjectStatus.id', '{n}.ProjectStatus.name') : array();
						echo $this->Form->input('task.task_status_id', array(
							'type'=> 'select',
							'id' => 'nct-status',
							'label' => __d(sprintf($_domain, 'Details'), 'Status', true),
							'options' => $list_status,
							'required' => true,
							'rel' => 'no-history',
							'div' => array(
								'class' => 'wd-input label-inline required has-val'
							)
						));
						
						?>
				   </div>
				   <div class="wd-col wd-col-sm-6">
						
						<?php 
						$periods = array( __('Day',true), __('Week',true), __('Month',true)/* , __('Period',true )*/);
						echo $this->Form->input('type', array(
							'type'=> 'select',
							'id' => 'nct-range-type',
							'label' => __('Period', true),
							'options' => $periods,
							'required' => true,
							'rel' => 'no-history',
							'selected' => 2,
							'div' => array(
								'class' => 'wd-input label-inline has-val required'
							)
						));
						?>
					</div>
				</div>
				<div class="wd-row">
					<div class="wd-col wd-col-sm-6">
					<?php 				
						echo $this->Form->input('task.task_start_date', array(
							'type'=> 'text',
							'id' => 'nct-start-date',
							'label' => __d(sprintf($_domain, 'Project_Task'), 'Start date', true),
							'required' => true,
							'class' => 'wd-date',
							'onchange'=> 'nct_validated(this);',
							'autocomplete' => 'off',
							'rel' => 'no-history',
							'div' => array(
								'class' => 'wd-input label-inline has-val required'
							)
						));
						?>
					</div>
					<div class="wd-col wd-col-sm-6">
						<div class="input-icon-container">
							<?php 				
								echo $this->Form->input('task.task_end_date', array(
									'type'=> 'text',
									'id' => 'nct-end-date',
									'label' => __d(sprintf($_domain, 'Project_Task'), 'End date', true),
									'required' => true,
									'class' => 'wd-date',
									'autocomplete' => 'off',
									'onchange'=> 'nct_validated(this);',
									'rel' => 'no-history',
									'div' => array(
										'class' => 'wd-input label-inline has-val required'
									)
								));
								?>
							<div class="wd-icon">
								<a href="javascript:void(0);" id="create-range" class="btn"><i class="icon-reload"></i></a>
							</div>
						</div>
					</div>
				</div>
				<div class="wd-row">
					<div class="wd-col wd-col-sm-6">
						<?php 
						if( !empty($adminTaskSetting['Profile'])){
							$list_profiles = !(empty($profiles)) ? Set::combine($profiles, '{n}.Profile.id', '{n}.Profile.name') : array();
							echo $this->Form->input('task.task_profiles_id', array(
								'type'=> 'select',
								'id' => 'nct-profile',
								'label' => __d(sprintf($_domain, 'Details'), 'Profile', true),
								'options' => $list_profiles,
								'required' => true,
								'rel' => 'no-history',
								'div' => array(
									'class' => 'wd-input label-inline required has-val'
								)
							));
						}
						?>
					</div>
					<div class="wd-col wd-col-sm-6">
						<?php
						if( !empty($adminTaskSetting['Priority'])){						
							$list_priorities = !(empty($priorities)) ? Set::combine($priorities, '{n}.ProjectPriority.id', '{n}.ProjectPriority.priority') : array();
							echo $this->Form->input('task.task_priority_id', array(
								'type'=> 'select',
								'id' => 'nct-priority',
								'label' => __d(sprintf($_domain, 'Details'), 'Priority', true),
								'options' => $list_priorities,
								'required' => true,
								'rel' => 'no-history',
								'div' => array(
									'class' => 'wd-input label-inline required has-val'
								)
							));
						}
						
						?>
					</div>
					
				</div>
				<div class="wd-row">
					<div class="wd-col wd-col-sm-6">
						
					
						<select id="nct-milestone"  style="display: none">
							<option value=""><?php __('Milestone') ?></option>
						</select>
						<?php 	
						echo $this->Form->input('task.unit_price', array(
							'type'=> 'text',
							'id' => 'nct-unit-price',
							'label' => __('Unit Price', true),
							'required' => false,
							'rel' => 'no-history',
							'div' => array(
								'class' => 'wd-input label-inline has-val',
								'id' => 'wd-unit-price',
								'style' => 'display: none'
							)
						));
						 ?>
					
						<?php 
							echo $this->Form->input('task.total_workload', array(
								'type'=> 'text',
								'id' => 'nct-total-workload',
								'label' => __d(sprintf($_domain, 'Project_Task'), 'Workload', true),
								'required' => false,
								'disabled' => true,
								'rel' => 'no-history',
								'div' => array(
									'class' => 'wd-input label-inline has-val',
								),
								
							));
							// if( !empty($companyConfigs['manual_consumed']) && !empty($adminTaskSetting['Manual Consumed']) ): 
							if( !empty($ManualConsumed) && $ManualConsumed ):
									echo $this->Form->input('task.manual_consumed', array(
									'type'=> 'text',
									'id' => 'nct-manual',
									'label' => __d(sprintf($_domain, 'Project_Task'), 'Manual Consumed', true),
									'required' => false,
									'rel' => 'no-history',
									'div' => array(
										'class' => 'wd-input label-inline has-val',
									),
									
								));
							 endif
						?>
						<div class="input-icon-container">
							<?php
							echo $this->Form->input('per-workload', array(
								'type'=> 'text',
								'id' => 'per-workload',
								'label' => __d(sprintf($_domain, 'Project_Task'), 'Workload', true),
								'required' => false,
								'autocomplete' => 'off',
								'rel' => 'no-history',
								'div' => array(
									'class' => 'wd-input label-inline has-val'
								)
							));
							?>
							<div class="wd-icon">
								<a href="javascript:void(0);" id="fill-workload" class="btn">
									<i class="icon-reload"></i>
								</a>
							</div>
						</div>
					</div>
				
					<div class="wd-col wd-col-sm-6">
						<div class="wd-input wd-calendar placeholder">
							
							<a id="add-range" href="javascript:;" class="btn-text btn-green range-picker" style="display: none;">
								<img src="<?php echo $this->Html->url('/img/ui/blank-plus.png') ?>" />
								<span><?php __('Create') ?></span>
							</a>
							<a id="reset-range" class="btn-text range-picker" href="javascript:;" style="display: none;">
								<img src="<?php echo $this->Html->url('/img/ui/blank-reset.png') ?>" />
								<span><?php __('Reset') ?></span>
							</a>

							<div id="date-list" class="start-end" style="margin-bottom: 5px"></div>
							
							<a id="nct-reset-date" class="btn-text start-end" href="javascript:;">
								<img src="<?php echo $this->Html->url('/img/ui/blank-reset.png') ?>" />
								<span><?php __('Reset') ?></span>
							</a>
						</div>
					</div>
				</div>
			</div>
			<div class="wd-inline-col wd-col-autosize">
				<div id="assign-list" style="float: left; overflow: auto;">
					<table id="assign-table" class="nct-assign-table">
						<thead>
							<tr>
								<td class="bold base-cell null-cell">&nbsp;</td>
								<td class="base-cell" id="abcxyz"><?php __d(sprintf($_domain, 'Project_Task'), 'Consumed') ?> (<?php __d(sprintf($_domain, 'Project_Task'), 'In Used') ?>)</td>
								<td class="remove" id="remove-row"></td>
							</tr>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<tr>
								<td class="base-cell"><?php __('Total') ?></td>
								<td class="base-cell" id="total-consumed">0</td>
								<td class="base-cell" id="remove-row"></td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
			<div class="wd-submit-row">
				<div class="wd-submit-actions wd-col-xs-12">
				<a class="cancel btn-form-action btn-cancel" id="cancel-special" href="javascript:void(0)"><?php echo __('Close') ?></a>
				<a id="save-special" class="btn-form-action new" onclick="return false;" href="#"><?php echo __('Submit') ?></a>
				<li id="nct-progress" style="display: none; margin-right: 10px"><?php __('Saving...') ?></li>
			</div>
		</div>
    </div>
</div>
<script type="text/javascript">
    HistoryFilter.auto = false;
    HistoryFilter.here = '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url = '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
	// HistoryFilter.auto = 0;
</script>
<script type="text/javascript">
	var employee_id = <?php echo json_encode($employee_id); ?>;
	var listEmployee = <?php echo json_encode($listEmployee); ?>;
	var listAvartar = <?php echo json_encode($listAvartar) ?>;
	var listTeam = <?php echo json_encode($listTeam) ?>;
	var _totalConsumeOfPj = <?php echo json_encode($_totalConsumeOfPj) ?>;
	var _urlImg = <?php echo json_encode($this->Html->url('/img/ui/blank-plus.png')); ?>;
	var companyConfigs = <?php echo json_encode($companyConfigs); ?>;
	var isShowPopup = <?php echo json_encode($isShowPopup); ?>;
	var i18n = <?php echo json_encode(array(
		'Consume' => __('Consommés', true),
	)) ?>;
	var consumeCapa = <?php echo json_encode($consumeCapa) ?>;
	// doan nay custom function cho nhung method con thieu.
	// method fill
	if ( ![].fill)  {
		Array.prototype.fill = function( value ) {
			var O = Object( this );
			var len = parseInt( O.length, 10 );
			var start = arguments[1];
			var relativeStart = parseInt( start, 10 ) || 0;
			var k = relativeStart < 0 ? Math.max( len + relativeStart, 0) : Math.min( relativeStart, len );
			var end = arguments[2];
			var relativeEnd = end === undefined ? len : ( parseInt( end)  || 0) ;
			var final = relativeEnd < 0 ? Math.max( len + relativeEnd, 0 ) : Math.min( relativeEnd, len );
			for (; k < final; k++) {
				O[k] = value;
			}
			return O;
		};
	}
	
	
	// method assign
	if (typeof Object.assign != 'function') {
		Object.assign = function(target) {
			'use strict';
			if (target == null) {
			  throw new TypeError('Cannot convert undefined or null to object');
			}

			target = Object(target);
			for (var index = 1; index < arguments.length; index++) {
				var source = arguments[index];
				if (source != null) {
					for (var key in source) {
						if (Object.prototype.hasOwnProperty.call(source, key)) {
							target[key] = source[key];
						}
					}
				}
			}
			return target;
		};
	}
	// method startsWith
	if (!String.prototype.startsWith) {
		String.prototype.startsWith = function(searchString, position){
			position = position || 0;
			return this.substr(position, searchString.length) === searchString;
		};
	}
	// teams
	var _teamsObj = <?php echo json_encode($listTeam); ?>;
	var _teams = [];
	var _teams = $.map(_teamsObj, function(value) {
		return [value];
	});
	// period
	var _periods = <?php echo json_encode($_period) ?>;
	var _period = [];
	var _period = $.map(_periods, function(value) {
		return [value];
	});
	var numberOfMonth = _period.length;
	//
	var firstDate = _period[0] + '01';
	var lastDate = _period[numberOfMonth-1] + '01';
	firstDate = firstDate.replace('-', '');
	lastDate = lastDate.replace('-', '');
	// capacity
	var capacity = <?php echo json_encode($_capacity); ?>;
	var _capacity = {};
	if(capacity){
		$.each(capacity, function(t, value){
			// var a = [];
			var a = $.map(value, function(val) {
				return [val];
			});
			_capacity[t] = a;
		});
	}
	var rawPjList = <?php echo json_encode($stringPj); ?>;
	var rawData = <?php echo json_encode($rawData); ?>;
	var assignConsume = <?php echo json_encode($assignConsume) ?>;
	var assignWorkloads = <?php echo json_encode($assignWorkloads) ?>;
	// -----------
	var pjList = d3.map();
	var pjCsvData = d3.dsvFormat(";").parseRows(rawPjList);
	
	for (var i = 0; i < pjCsvData.length; i++) {
		var line = pjCsvData[i];
		var name = line[1];
		pjList.set(name, {
			id: line[0],
			name: name,
			cdp: line[3],
			version: line[7],
			commitee: line[4],
			priority: line[6],
		});
	}

	var readNumber = function(value) {
		value = value.trim();
		if (value.length == 0) {
			return 0.0;
		} else {
			var nr = parseFloat(value);
			return isNaN(nr) ? 0.0 : nr;
		}
	};
	var sum = function(a,b) {
		return a+b;
	};
	var _projects = d3.map();
	var _counter = 0;
	for (var k=0; k<_teams.length; k++) {
		var team = _teams[k];
		var csvData = d3.dsvFormat(";").parseRows(rawData[team]);

		for (var i = 0; i < csvData.length; i++) {
			// Process task
			var line = csvData[i];
			var name = line[1];
			var pj = pjList.get(name);
			if (pj === null || pj === undefined) {
				continue;
			}
			var project = null;
			if (_projects.has(pj.id)) {
				project = _projects.get(pj.id);
			} else {
				_projects.set(pj.id, {
					id: pj.id,
					name: name,
					version: pj.version,
					commitee: pj.commitee,
					// priority: pj.priority.substring(0,1),
					priority: pj.priority,
					// List of tasks
					tasks: [],
				});
				project = _projects.get(pj.id);
			}
			// console.log(line.slice(6, 6+numberOfMonth).map(readNumber));
			var task = {
				id: line[0],
				team: team,
				phase: line[2],
				name: line[3],
				teamId: line[4],
				// Task type : non linear ("NL") or linear ("L")
				type: "NL",
				// Workload reference
				reference: readNumber(line[5]),
				// Task workload by month => can be empty for linear tasks
				workload: line.slice(8, 8+numberOfMonth).map(readNumber),
				// Workload ids (for editing)
				wIds: [],
				// Total task workload
				totalWorkload: 0,
				// Task timesheet by month
				timesheet: line.slice(9+numberOfMonth, 9+numberOfMonth+numberOfMonth).map(readNumber),
				// Task total timesheet
				totalTimesheet: 0,
				startDate: line[6],
				endDate: line[7],
			};
			task.totalWorkload = task.workload.reduce(sum,0);
			task.totalTimesheet = task.timesheet.reduce(sum,0);
			// fake ids
			task.wIds = new Array(numberOfMonth).fill(0).map(function(v, i) { return _counter++; });
			project.tasks.push(task);
		}
	}
	_projects = _projects.values();
	//$('#loadingPopup').modal('show');
	//ticket 975
	show_popup = (isShowPopup) ? 1 : 0;
	$('#show_popup').on('change', function(){ 
		PDCData.show_popup = ($('#show_popup').is(':checked') ? 1 : 0) ;
	});
	PDCData.load(_period, _teams, _projects, _capacity, show_popup);
	$(initTable);
	var x = $('th.last-month').innerWidth();
	var w = $('.pdc-header-table .workload-td:first').width();
	// set right cho capacity table.
	$('#capacity-row .capacity-text-td').width(w);
	$('#capacity-row .workload-td').width(w);
	var saveCommentPj = function(){
		var text = $('.textarea-ct').val(),
			_id = $('.submit-btn-msg').data('id');
		var d = new Date,
		dformat =   [ d.getFullYear(),
					((d.getMonth()+1) < 10 ? '0'+(d.getMonth()+1) : (d.getMonth()+1)),
					(d.getDate()< 10 ? '0'+d.getDate() : d.getDate())].join('-')+' '+
					[(d.getHours() < 10 ? '0'+d.getHours() : d.getHours()),
					(d.getMinutes() < 10 ? '0'+d.getMinutes() : d.getMinutes()),
					(d.getSeconds() < 10 ? '0'+d.getSeconds() : d.getSeconds())].join(':');
		var link = listAvartar[employee_id];
		var content = text.replace(/\n/g, "<br>");
		if(text != ''){
			$.ajax({
				url: '/zog_msgs/saveComment',
				type: 'POST',
				data: {
					id: _id,
					content: text
				},
				success: function(success){
					$('.modal-body').append('<div class="content"><div class= "avartar-image" style="width: 60px"><img class="avartar-image-img" src="'+link+'"></div><div class="content-comment"><div class="name"><h5>'+ listEmployee[employee_id] +'</h5><em>'+ dformat +'</em></div><div class="comment">'+ content +'</div></div></div>');
					$('.textarea-ct').val('');
				}
			});
		}
	};
	var saveCommentTask = function(){
		var text = $('.textarea-ct').val(),
			_id = $('.submit-btn-msg').data('id');
		if(text != ''){
			$.ajax({
				url: '/team_workloads/saveTxtOfTask',
				type: 'POST',
				data: {
					id: _id,
					content: text
				},
				dataType: 'json',
				success: function(data){
					if(data){
						var time = data.time,
							content = data.content,
							name = data['name'],
							link = listAvartar[data.employee_id];
						$('.modal-body').append('<div class="content"><div class= "avartar-image" style="width: 60px"><img class="avartar-image-img" src="'+link+'"></div><div class="content-comment"><div class="name"><h5>'+ name +'</h5><em>'+ time +'</em></div><div class="comment">'+ content +'</div></div></div>');
						$('.textarea-ct').val('');
					}
				}
			});
		}
	};
	$('#scrollLeftAbsence').on('scroll', function(e){
		var amount = $('#scrollLeftAbsence').scrollTop();
		$('#right-scroll').scrollTop(amount);
	});
	function setupScroll(){
		rightContent = $('.pdc-table');
		rightHeaderHeight = $('.pdc-header-table').height();
		rightScroll = $('#scrollLeftAbsenceContent');
		rightScrollContainer = $('#scrollLeftAbsence');
		//fix position for right scroll container
		var scroll_offset_top = $('.option-bar').outerHeight() + rightHeaderHeight;
		if( $('.capacity-container').is(':visible')) scroll_offset_top +=  $('.capacity-container').outerHeight(true);
		rightScrollContainer.css({
			height: $('.pdc-container').height(),
			top: scroll_offset_top
		});
		//right scroll content height
		// var rightHeight = rightContent.height() + 630;
		var rightHeight = rightContent.height();
		rightScroll.height(rightHeight);
		$("#scrollTopAbsence").width($(".wd-content-project").width());
		
	}
	setupScroll();
	$('.wd-content-project').mousewheel(function(event) {
		var amount = event.deltaY * event.deltaFactor;
		scaleY(amount);
	});
	// $(document).on('mousewheel', '.wd-content-project', function(){
		//do check here
		// return false;
		// return true;
	// });
	$("#scrollLeftAbsence").click(function(){
		allowScrollWindow = true;
	});
	$("#scrollTopAbsence").scroll(function () {

		$(".wd-content-project").scrollLeft($("#scrollTopAbsence").scrollLeft());
	});
	$("#absence-scroll").scroll(function () {
		$("#scrollTopAbsence").scrollLeft($(".wd-content-project").scrollLeft());
	});
	function scaleY(amount){
		//down -> negative
		$('#scrollLeftAbsence')[0].scrollTop -= amount;
	}
	function expandScreen(){
		$('.wd-layout').addClass('fullScreen');
		$(window).resize();
		$("#scrollLeftAbsence").trigger('scroll');
		$('#collapse').show();
		$('#expand-btn').hide();
	}
	function collapseScreen(){
		$('#collapse').hide();
		$('.wd-layout').removeClass('fullScreen');
		$('#expand-btn').show();
		$(window).resize();
		$("#scrollLeftAbsence").trigger('scroll');
	}
	var loading = "<span id='loadingElm'><img src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt='Loading' /></span>";
	function updateConfig(field,value, target)
	{
		var _cont = $('#'+field).closest('.input').find('label');
		// $(loading).insertAfter(_cont);
		_cont.append(loading);
		var data = field+'/'+value;
		$.ajax({
			url: '/company_configs/editMe/',
			data: {
				data : { value : ($('#'+field).is(':checked') ? 1 : 0), field : field }
			},
			type:'POST',
			success: function(data) {
				// console.log( data);
				data = JSON.parse(data);
				$('#'+field).removeClass('KO');
				$('#loadingElm').remove();
				if( data.CompanyConfig.cf_value == 1){
					$(target).removeClass('d3-hidden');
				}else{
					$(target).addClass('d3-hidden');
				}
				var width = $('.pdc-header-table').width();
				$('.pdc-header-table').css("width", width + "px");
				$('.pdc-container').css("width", (width) + "px");
				$('.pdc-table').css("width", width + "px");
			}
		});
	}
	var wdTable = $('.wd-content-project');
	$(document).ready(set_slick_table_height);
	$(window).resize(set_slick_table_height);
	function set_slick_table_height(){
		if( wdTable.length){
			if( $('.capacity-container').is(':visible')){
				wdTable.css({
					'max-height': '',
				});
				wdTable.find('#right-scroll').css({
					'max-height': ''
				});
			}else{
				var heightTable = $(window).height() - wdTable.not(':hidden').first().offset().top - parseInt($('.wd-layout:first').css('padding-bottom'));
				heightTable = Math.max( heightTable, 400);
				wdTable.css({
					'max-height': heightTable,
				});
				wdTable.find('#right-scroll').css({
					'max-height': heightTable - 74,
				});
			}
			setupScroll();
		}
	}
	function openNCTask(task_id, project_id){
		if( $('#special-task-info').hasClass('loading') ) return;
		$('#special-task-info').addClass('loading');
		if( task_id == undefined) return;
		// $('#special-task-info').dialog( "option", "classes",{"ui-dialog": "loading"} );
		$('#special-task-info').dialog('open');
		$.ajax({
			url: '/project_milestones/get_list_milestone/'+ project_id,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				$('#nct-milestone').empty();
				$('#nct-milestone').append('<option value="">--<?php __('Milestone');?>--</option>');
			},
			success: function(data){
				if( data){
					$.each(data, function(i, milestone){
						milestone = milestone['ProjectMilestone'];
						$('#nct-milestone').append('<option value="' + milestone['id'] + '">' + milestone['project_milestone'] + '</option>');
					} );
				}
			},
		});
		$.ajax({
			url: '/project_tasks/getNcTask',
			data: {data : {id : task_id}},
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#special-task-info').dialog( "option", "title", data.task.task_title);
				Task = new SpecialTask({
					columns: data.columns,
					data: data.data,
					id: task_id,
					project_id: project_id,
					task: data.task,
					consume: data.consumeResult,
					request: data.request
				});
				$.ajax({
					url: '/project_tasks/get_team_employees/' + project_id,
					type: 'get',
					dataType: 'json',
					success: function(response){
						var  elm = $('#multiselect-popupnct-pm').find('.option-content');
						// multiSelectRestore(elm);
						var _html_options = '';
						$.each(response, function( i, empl){
							empl = empl.Employee;
							var checked = '';
							var val = empl['id'] + '-' + empl['is_profit_center'];
							_html_options +='<div class="projectManager wd-data-manager wd-group-'+empl['id']+'">';
								_html_options +='<p class="projectManager haha wd-data checked '+ checked +'">';
									_html_options +='<a href="javascript:void(0);" class="circle-name" title="' + empl['name'] + '"><span data-id="'+empl['id']+'-'+empl['is_profit_center']+'">';
										if(empl['is_profit_center'] == 1){
											_html_options +='<i class="icon-people"></i>';
										} else {
											_html_options +='<img width ="35" height="35" src="/img/avatar/'+empl['id']+'.png" alt="avatar"/>';
										}
									_html_options +='</a>';
									_html_options +='<input type="hidden" name="nct-resources-list[]" id="check-'+empl['id']+'-'+empl['is_profit_center']+'_" value="0">';
									_html_options +='<input type="checkbox" name="nct-resources-list[]" id="check-'+empl['id']+'-'+empl['is_profit_center']+'" value="'+empl['id']+'-'+empl['is_profit_center']+'">';
									_html_options +='<span class="option-name">' + empl['name'] + '</span>	';
								_html_options +='</p>';
							_html_options +='</div>';
						});
						$(elm).empty().append(_html_options);
						init_multiselect('#multiselect-popupnct-pm', {
							callback: on_assign_change_callback
						});
						resetOptionAssigned(data.employees_actif, '#multiselect-popupnct-pm');
						$.each( data.columns, function( i, column){
							$('#check-' + column.id).closest('.wd-data').click();
						}); 
						setLimitedDate('#nct-start-date', '#nct-end-date');
						$('#special-task-info').removeClass('loading');
					},
					complete: function(){
						
					}
				});
				
			},
			failure: function(){
				alert('Problem loading task data. Please reload this page.');
			}
		});
	}
	function resetOptionAssigned(employees_actif, id_element){
		if(employees_actif.length > 0){
			$.each(employees_actif, function(i, employee){
				if(employee['Employee']['actif'] == 0){
					itemEle = '.wd-group-'+employee['Employee']['id'];
					$(id_element).find(itemEle).addClass('wd-actif-0');
				}
			});
		}
	}
	function on_assign_change_callback(elm){
		
		var me = $(elm);
		var cb =  me.find(':checkbox');
		var name = me.find('.option-name').text(),
			id = cb.val();
		var employee_id = 0;
		var is_profit_center = 0;
		if(id){
			resouce = id.split('-');
			employee_id = resouce[0];
			is_profit_center = resouce[1];
		}
		
		var _avt = name;
		if(employee_id){
			_avt = '<span class="circle-name" title="' + name + '" data-id="' + id + '">';
			if( is_profit_center == 1 ){
				_avt += '<i class="icon-people"></i>';
			}else{
				_avt += '<img width = 30 height = 30 src="'+  employeeAvatar_link.replace('%ID%', employee_id ) +'" title = "'+ name +'" />';	
			}
			_avt += '</span><p class="emp-name">' + name + '</p>';
		}
		var cell = $('.cell-' + id);
		if( cb.is(':checked') ){
			if( cell.length ){
				cell.show();
				return;
			}
			//add header
			var html = '<td class="value-cell header-cell cell-' + id + '" id="col-' + id + '">' + _avt + '<a class="cancel" onclick="removeRow(this)" href="javascript:;"></a></td>';
			$(html).insertBefore('#abcxyz');
			//add content
			$('.nct-date').each(function(){
				var ciu = $(this).parent().find('.ciu-cell'),
					date = $(this).text();
				$('<td class="value-cell cell-' + id + '"><input type="text" id="val-' + date + '-' + id + '" data-old="0" class="workload-' + id + '" value="0" style="text-align: right; width: 98%; border: 0; padding: 0" onchange="changeTotal(this)" data-ref></td>').insertBefore(ciu);
			});
			bindNctKeys();
			//add footer
			$('<td class="value-cell cell-' + id + '" id="foot-' + id + '">0.00</td></tr>').insertBefore('#total-consumed');
		} else {
			//remove (or hide), i choose to hide those cells
			cell.hide();
		}
		refreshPicker();
		calculateTotal();
	}
	function multiselect_popupnct_pmonChange(){
		return;
	}
	//1
	<?php
		$i18n = array(
			//for extjs
			'Read only' => __('Read only', true),
			'New Task' => __('New Task', true),
			'Update' => __('Update', true),
			'Resources' => __('Resources', true),
			'Filter' => __('Filter', true),
			'Save' => __('Save', true),
			'Saved' => __('Saved', true),
			'Not saved' => __('Not saved', true),
			'Cancel' => __('Cancel', true),
			'New Task' => __('New Task', true),
			'Create not-continuous task' => __('Create not-continuous task', true),
			'Warning' => __('Warning', true),
			'Workload' => __('Workload', true),
			'Delete task and its sub-tasks?' => __('Delete task and its sub-tasks?', true),
			'Workload Detail For Employee And Profit Center' => __('Workload Detail For Employee And Profit Center', true),
			'Delete' => __('Delete', true),
			'Total workload' => __('Workload', true),
			'ok' => __('OK', true),
			'yes' => __('Yes', true),
			'no' => __('No', true),
			'cancel' => __('Cancel', true),
			'Move Here' => __('Move Here', true),
			'Move part and phase in the screens part and phase' => __('Move part and phase in the screens part and phase', true),
			'Workload {1} M.D , Workload filled {0} M.D.' => __('Workload {1} M.D , Workload filled {0} M.D.', true),
			'Please wait' => __('Please wait', true),
			'The task name already exists' => __('The task name already exists', true),
			'No consumed and the current date > start date' => __('No consumed and the current date > start date', true),
			'Consumed > Workload' =>  __('Consumed > Workload', true),
			'Task not closed and the current date > end date' => __('Task not closed and the current date > end date', true),
			'{0} is linked to other(s) task(s). If you modified the end date the tasks(s) linked will be modified. However the duration of the tasks linked will not be modified?' => __('{0} is linked to other(s) task(s). If you modified the end date the tasks(s) linked will be modified. However the duration of the tasks linked will not be modified?', true),
			'This task is in used/ has consumed' => __('This task is in used/ has consumed', true),
			'Reset' => __('Reset', true),
			'Task name already existed' => __('Task name already existed', true),
			'Edit' => __('Edit', true),
			'Keep the duration of the task ' => __('Keep the duration of the task ', true),
			'Check all' => __('Check all', true),
			'Uncheck all' => __('Uncheck all', true),
		);
		$i18n = array_merge($i18n, $this->requestAction('/translations/getByLang/Project_Task'));
		$i18n = json_encode($i18n);
	?>
	var i18n_text = <?php echo $i18n?>;
    var showProfile = <?php echo isset($companyConfigs['activate_profile']) ? (string) $companyConfigs['activate_profile'] : '0' ?>;
    var is_manual_consumed = <?php echo isset($companyConfigs['manual_consumed']) ? (string) $companyConfigs['manual_consumed'] : '0' ?>;
    var gap_linked_task = <?php echo isset($companyConfigs['gap_linked_task']) ? (string) $companyConfigs['gap_linked_task'] : '0' ?>;
    var task_no_phase = <?php echo isset($companyConfigs['task_no_phase']) ? (string) $companyConfigs['task_no_phase'] : '0' ?>;
    var create_ntc_task = <?php echo isset($companyConfigs['create_ntc_task']) ? (string) $companyConfigs['create_ntc_task'] : '0' ?>;
    var webroot = <?php echo json_encode($this->Html->url('/')) ?>;
    var hightlightTask = <?php echo json_encode(isset($_GET['id']) ? $_GET['id'] : '') ?>;
    var canModify = <?php echo json_encode(!empty($canModified)); ?>;
	
	var listDeletion = [];
    var Task;
    var Holidays = {};  //get by ajax
    var Workdays = <?php echo json_encode($workdays) ?>;
    var startDate, endDate;

    var monthName = <?php
        echo json_encode(array(
            __('January', true),
            __('February', true),
            __('March', true),
            __('April', true),
            __('May', true),
            __('June', true),
            __('July', true),
            __('August', true),
            __('September', true),
            __('October', true),
            __('November', true),
            __('December', true)
        ));
    ?>;
	var columns_order = <?php echo json_encode($this->requestAction('/admin_task/getTaskSettings')) ?>;
    var hide_unit = hide_milestone = false;
    // var _milestoneColor = < ?php echo json_encode($_milestoneColor) ?>;
    $.each(columns_order, function(index, value){
        var _v = value.split('|');
        if(_v[0] == 'UnitPrice'){
            if(_v[1] == 1) hide_unit = true;
        }
        if(_v[0] == 'Milestone'){
            if(_v[1] == 1) hide_milestone = true;
        }
    });
    var manual
    function i18n(text){
        if( typeof i18n_text[text] != 'undefined' )return i18n_text[text];
        return text;
    }
	function multiSelectRestore(elm){
		var multiSelect = $(elm);
		$(multiSelect.data("multiSelectOptions")).remove();
		$('<select data-class="'+ multiSelect.prop('class') +'" data-id="'+ multiSelect.prop('id') +'" data-multiSelectRestore="true" multiple="true"></select>').insertBefore(multiSelect);
		multiSelect.remove();
		var rest = $('[data-multiSelectRestore="true"]');
		$.each(rest.prop("attributes"), function(i, val){
			if( RegExp(/^data-/).test(this.name) ) {
				rest.attr( (this.name).replace(/^data-/,''), this.value);
				rest.removeAttr(this.name);
			}
		});
		// Chạy 1 lần vẫn còn sót
		// méo hiểu tại sao.
		$.each(rest.prop("attributes"), function(i, val){
			if( RegExp(/^data-/).test(this.name) ) {
				rest.attr( (this.name).replace(/^data-/,''), this.value);
				rest.removeAttr(this.name);
			}
		});
	}
	function initMultiSelect(elm){
		$(elm).multiSelect({
			selectAll: false,
			noneSelected: '&nbsp;',
			oneOrMoreSelected: '*'
		});
	}
	function setLimitedDate(ele_start, ele_end){
		var limit_sdate = $('#assign-table tbody tr:first').find('.nct-date').attr('id');
		if(limit_sdate){
			 limit_start_date = (limit_sdate.split("_")[1]).split("-");
			 limit_s_date = new Date(limit_start_date[1] +'-'+limit_start_date[0] +'-'+limit_start_date[2]);
			 $(ele_start).datepicker('option','maxDate',limit_s_date);
		}
		limit_edate = $('#assign-table tbody tr:last').find('.nct-date').attr('id');
		if(limit_edate){
			 limit_end_date = (limit_edate.split("_")[0]).split("-");
			 limit_e_date = new Date(limit_end_date[2] +'-'+limit_end_date[1] +'-'+limit_end_date[3]);
			  $(ele_end).datepicker('option','minDate',limit_e_date);
		}
	}
	function SpecialTask(options){
        this.defaults = {
            data: {},
            columns: [],
            id: 0,
            project_id: 0,
            task: {
                id: 0,
                task_title: '',
                task_priority_id: '',
                task_status_id: '',
                task_start_date: '',
                task_end_date: '',
                project_planed_phase_id: 0,
                unit_price: 0,
                profile_id : '',
                manual_consumed: 0
            },
            consume: {}
        };
        this.options = $.extend({}, this.defaults, options);
        // this.data = {};
        // this.columns = [];
        // this.deleted = [];
        this.init();
        return this;
    }
    SpecialTask.prototype.init = function(){
		
        listDeletion = [];
        var me = this;
        //init data
        $('#nct-id').val(me.options.id);
        $('#nct-project-id').val(me.options.project_id);
        $('#nct-phase-id').val(me.options.task.project_planed_phase_id);
        $('#nct-priority option[value="' + me.options.task.task_priority_id + '"]').prop('selected', true);
        $('#nct-milestone option[value="' + me.options.task.milestone_id + '"]').prop('selected', true);
        $('#nct-status option[value="' + me.options.task.task_status_id + '"]').prop('selected', true);
        $('#nct-start-date').val(me.options.task.task_start_date).datepicker( "setDate", me.options.task.task_start_date );
        $('#nct-end-date').val(me.options.task.task_end_date).datepicker( "setDate", me.options.task.task_end_date );
        $('#nct-manual').val(me.options.task.manual_consumed);
        $('#nct-unit-price').val(me.options.task.unit_price);
        $('#nct-profile option[value="' + me.options.task.profile_id + '"]').prop('selected', true);
        $('#nct-name').val(me.options.task.task_title);
        this.columns = this.options.columns;
        if( typeof this.options.data == 'function' ){
            this.data = this.options.data();
        } else {
            this.data = this.options.data;
        }
        var columns = this.columns;
        var data = this.data;
        var request = this.options.request;

        //build html
        var estimate = {}, consume, inUsed;
        try{
            consume = parseFloat(this.options.request.all[0]);
            inUsed = parseFloat(this.options.request.all[1]);
        } catch(ex){
            consume = 0;
            inUsed = 0;
        } finally {
            if( isNaN(consume) )consume = 0;
            if( isNaN(inUsed) )inUsed = 0;
        }
        /*
        *{ id : number }
        */
        //build header
        var list = [];
        $.each(columns, function(i, col){
			var employee_id = 0;
			var is_profit_center = 0;
			if(col){
				id = col.id;
				resouce = id.split('-');
				employee_id = resouce[0];
				is_profit_center = resouce[1];
			}
			
			var _avt = col.name;
			if(employee_id){
				_avt = '<span class="circle-name" title="' + col.name + '" data-id="' + col.id + '">';
				if( is_profit_center == 1 ){
					_avt += '<i class="icon-people"></i>';
				}else{
					_avt += '<img width = 30 height = 30 src="'+  employeeAvatar_link.replace('%ID%', employee_id ) +'" title = "'+ col.name +'" />';	
				}
				_avt += '</span><p class="emp-name">' + col.name + '</p>';
			}
            var html = '<td class="value-cell header-cell cell-' + col.id + '" id="col-' + col.id + '">' + _avt + '</td>';
            $(html).insertBefore('#abcxyz');
            //hide from select
            $('#res-' + col.id).hide();
            //build footer
            html = '<td class="value-cell cell-' + col.id + '" id="foot-' + col.id + '"></td>';
            $(html).insertBefore('#total-consumed');
            estimate[col.id] = 0;
            //update assign list
            $('#check-' + col.id).prop('checked', true).parent().addClass('checked').trigger('change');
            // if( typeof me.options.consume[col.id] != 'undefined' && me.options.consume[col.id] ){
            //     $('#check-' + col.id).prop('disabled', true);
            // }
            list.push(col.name);
        });
        $('#nct-resources-list > span').html(list.length ? list.join(', ') : '&nbsp;');
        //build data
        var type = 2;
        $.each(data, function(row, items){
            //build row
            var date = row.substr(2);
            var html = '<tr><td id="date-' + date + '" class="nct-date" style="text-align: left">' + toRowName(row) + '</td>';
            var c = parseFloat(request[row][0]), iu = parseFloat(request[row][1]);
            if( isNaN(c) )c = 0;
            if( isNaN(iu) )iu = 0;
            $.each(items, function(i, item){
                var id = item.reference_id + '-' + item.is_profit_center;
                html += '<td class="value-cell cell-'+id+'"><input type="text" id="val-' + date + '-' + id + '" data-old="' + item.estimated + '" class="workload-'+id+'" data-ref value="' + item.estimated + '" style="text-align: right; width: 98%; border: 0; padding: 0" onchange="changeTotal(this)"></td>';
                //calculate estimated
                estimate[id] += item.estimated;
                type = item.type;
            });
            //last col
            html += '<td style="background: #f0f0f0" class="ciu-cell">' + c.toFixed(2) + ' (' + iu.toFixed(2) + ')</td>';
			html += '<td class="remove-cell"><span class="cancel" onclick="removeRow(this)" href="javascript:;"><img src="/img/new-icon/close.png" /><img src="/img/new-icon/close-blue.png" /></span></td></tr>';
            $('#assign-table tbody').append(html);
            if( c > 0 || iu > 0 ){
                $('#date-' + date).find('.cancel').remove();
            }
            //consume += c;
            //inUsed += iu;
        });
        //fill data for footer
        $.each(columns, function(i, col){
            $('#foot-' + col.id).text(estimate[col.id].toFixed(2));
        });
        $('#total-consumed').text(consume.toFixed(2) + ' (' + inUsed.toFixed(2) + ')');
        //disable name if task have consumed/in used
        if( consume > 0 || inUsed > 0 ){
            $('#nct-name').prop('disabled', false);
            //disable neu co consume/in used
            $('#nct-range-type').prop('disabled', true);
        } else {
            $('#nct-range-type').prop('disabled', false);
        }
        if( consume > 0 || inUsed > 0 || $('#nct-range-type').val() == '0' ){
            $('#create-range').hide();
        } else {
            $('#create-range').show();
        }
        if(hide_unit){
            $('#wd-unit-price').css('display', 'block');
        }
        if(hide_milestone){
            $('#wd-milestone').css('display', 'block');
        }
        //chon range tai day:
        $('#nct-range-type option[value="' + type +'"]').prop('selected', true);
		
        selectRange();
        //calculate
        calculateTotal();
        //refresh picker
        refreshPicker();
        // bind keys
        bindNctKeys();
        //done!
    }
    SpecialTask.prototype.destroy = function(){
        listDeletion = [];
        this.options = {
            data: {},
            columns: [],
            id: 0,
            task: {
                id: 0,
                task_title: '',
                task_priority_id: '',
                task_status_id: '',
                task_start_date: '',
                task_end_date: '',
                project_planed_phase_id: 0,
                unit_price: 0,
                profile_id : '',
                manual_consumed: 0
            },
            consume: {}
        };
        this.data = {};
        this.columns = [];
        //reset input
        $('.w .text').val('').prop('disabled', false);
        //reset selection
        $('.w select option').show().filter('[value=""]').prop('selected', true);
        //reset table
        $('#assign-table tbody').html('');
        $('#assign-table td.value-cell').remove();
        $('#total-consumed').html('0');
		
        $('#multiselect-popupnct-pm .wd-combobox-content input').prop('checked', false).prop('disabled', false).parent().removeClass('checked');
        $('#nct-resources-list > span').html('&nbsp;');
        $('#nct-range-type option[value="1"]').prop('selected', true);
    }
    SpecialTask.prototype.commit = function(){
        //check data
        var name = $.trim($('#nct-name').val()),
            sd = $('#nct-start-date').datepicker('getDate'),
            ed = $('#nct-end-date').datepicker('getDate');
        if( !name ){
            var dialog = $('#modal_dialog_alert1').dialog();
            $('#btnNoAL1').click(function() {
                dialog.dialog('close');
            });
            return false;
        }
        if( !sd || !ed ){
            var dialog = $('#modal_dialog_alert2').dialog();
            $('#btnNoAL2').click(function() {
                dialog.dialog('close');
            });
            return false;
        }
        if( sd > ed ){
            alert('Error: start date can not be greater than end date');
            $('#nct-start-date').focus();
            return false;
        }//check date range
        if( !isValidList() ){
            alert('Error: There are dates not between start date and end date');
            return false;
        }
        var result = {
            data: {
                workloads: {},
                id: $('#nct-project-id').val(),
                d: listDeletion,
                type: $('#nct-range-type').val(),
                task: {
                    id: $('#nct-id').val(),
                    task_title: name, //$('#nct-name').prop('disabled') ? '' : name,
                    task_priority_id: $('#nct-priority').val(),
                    task_status_id: $('#nct-status').val(),
                    task_start_date: $('#nct-start-date').val(),
                    task_end_date: $('#nct-end-date').val(),
                    project_planed_phase_id: $('#nct-phase-id').val(),
                    profile_id : $('#nct-profile').val(),
                    manual_consumed: $('#nct-manual').val(),
                    unit_price: $('#nct-unit-price').val(),
                    milestone_id: $('#nct-milestone').val()
                }
            }
        };

        $count_value_cell_hidden = $('#assign-table tbody tr .value-cell').filter(function() {
            return $(this).css('display') == 'none';
        }).length;
        if( !$('#assign-table tbody tr .value-cell input').length ){
            var dialog = $('#modal_dialog_alert').dialog();
            $('#btnNoAL').click(function() {
                dialog.dialog('close');
            });
            return false;
        } else if( $count_value_cell_hidden == $('#assign-table tbody tr .value-cell input').length ) {
            var dialog = $('#modal_dialog_alert').dialog();
            $('#btnNoAL').click(function() {
                dialog.dialog('close');
            });
            return false;
        }

        $('#assign-table tbody tr').each(function(){
            var tr = $(this),
                date = tr.find('.nct-date').prop('id').replace('date-', '');
            result.data.workloads[date] = {};
            var inputs = tr.find('.value-cell input:visible');
            inputs.each(function(){
                var me = $(this),
                    id = me.prop('class').replace('workload-', ''), t = id.split('-');
                result.data.workloads[date][id] = {
                    reference_id: t[0],
                    estimated: parseFloat(me.val()).toFixed(2),
                    is_profit_center: t[1]
                };
            });
        });
        //console.log(result.data.workloads);return;
        //save
        //disable button first
        var btns = $('#save-special,#cancel-special').addClass('disabled');
        var text = $('#nct-progress').show();
        var tree = $('#special-task-info').data('tree');
        $.ajax({
            url: '/project_tasks/saveNcTask',
            type: 'POST',
            dataType: 'json',
            data: result,
            success: function(response){
                $('#special-task-info').dialog('close');
                /*
                * add task
                */
                if( response.result ){
					location.reload();
                } else {
                    alert('Error saving task. Please reload the page');
                }
            },
            complete: function(){
                btns.removeClass('disabled');
                text.hide();
                //close dialog
                $('#special-task-info').dialog('close');
                //excute
            }
        })
    }
	function bindNctKeys(){
        var length = $('[data-ref]').length;
        $('[data-ref]').off('focus').on('focus', function(){
            var f = $(this).data('focused');
            if( f )return;
            $(this).data('focused', 1);
            $(this).select();
        }).on('blur', function(){
            $(this).data('focused', 0);
            // changeTotal(this);
        })
        .off('keydown').on('keydown', function(e){
            //tab key
            var index = $('[data-ref]').index(this);
            if( e.keyCode == 13 ){
                if( $(this).closest('td').next().hasClass('ciu-cell') ){
                    $(this).closest('tr').next().find('input:first').focus();
                } else {
                    $(this).closest('td').next().find('input').focus();
                }
                e.preventDefault();
            }
        });
    }
    function resetPicker(){
        if( $('#nct-range-type').val() == 0 ){
            $('#date-list').multiDatesPicker('resetDates');
            $('.nct-date').each(function(){
                var value = $(this).text();
                $('#date-list').multiDatesPicker('addDates', [$.datepicker.parseDate('dd-mm-yy', value)]);
            });
        }
    }
    function removeCol(e){
        //check consume
        var td = $(e).parent();
        var id = td.prop('id').replace('col-', ''), t = id.split('-');
        if( typeof Task.options.consume[id] != 'undefined' && Task.options.consume[id] ){
            alert('<?php __('This resource/PC already has consumed/in used data') ?>');
            return;
        }
        if( confirm('<?php __('Are you sure?') ?>') ){
            $('#col-' + id + ', .cell-' + id + ', #foot-' + id).each(function(){
                $(this).remove();
            });
            $('#res-' + id).show();
            listDeletion.push('res-' + id);
        }
    }

    function dialog_confirm(e, message) {
        $('.title').html(message);
        var dialog = $('#modal_dialog_confirm').dialog();
        $('#btnYes').click(function() {
            dialog.dialog('close');
            removeRowCall(e);
        });
        $('#btnNo').click(function() {
            dialog.dialog('close');
        });
    }
    function removeRowCall(e){
        var cols = $(e).parent().parent().find('input');
        cols.each(function(){
            var me = $(this);
            var id = me.prop('class').replace('workload-', '');
            var original = parseFloat($('#foot-' + id).text());
            $('#foot-' + id).text((original - parseFloat(me.val())).toFixed(2));
        });
        $(e).parent().parent().remove();
        //add to deletion list
        listDeletion.push('date:' + $(e).parent().parent().find('.nct-date').text());
        refreshPicker();
        calculateTotal();
		setLimitedDate('#nct-start-date', '#nct-end-date');
    }
    function removeRow(e){
        //check if has in used / consumed
        if( $(e).parent().parent().find('.ciu-cell').text() != '0.00 (0.00)' ){
            alert('<?php __('Can not delete this because it has consumed/in used data') ?>');
            return;
        }
        removeRowCall(e);
    }
    function changeTotal(e){
        //check here
        var me = $(e);
        if( !me.length )return;
        var old = parseFloat(me.data('old'));
        var newVal = parseFloat(me.val());
        var type = parseInt($('#nct-range-type').val());
        if( (isNaN(newVal) || newVal < 0 ) || (type == 0 && newVal > 1) ){
            if( type == 0 )
                alert('<?php __('Enter value between 0 and 1') ?>');
            else alert('<?php __('Please enter value >= 0') ?>');
            me.val(old);
            me.focus();
            return;
        }
        var id = me.prop('class').replace('workload-', '');
        var total = 0;
        $('.' + me.prop('class')).each(function(){
            total += parseFloat($(this).val());
        });
        $('#foot-' + id).text(total.toFixed(2));
        me.data('old', newVal);
        calculateTotal();
    }

    function calculateTotal(){
        var total = 0;
        $('#assign-list tfoot .value-cell').each(function(){
            total += parseFloat($(this).text());
        });
        $('#nct-total-workload').val(total.toFixed(2));
    }

    function minMaxDate(){
        var min, max;
        var type = $('#nct-range-type').val();
        if( type != 0 ){
            return [$('#nct-start-date').datepicker('getDate'), $('#nct-end-date').datepicker('getDate')];
        }
        $('.nct-date').each(function(){
            var text = $(this).text();
            var value = $.datepicker.parseDate('dd-mm-yy', text);
            if( !min || min > value)
                min = value;
            if( !max || max < value)
                max = value;
        });
        return [min, max];
    }
    function isValidList(){
        var range = minMaxDate(),
            start = $('#nct-start-date').datepicker('getDate'),
            end = $('#nct-end-date').datepicker('getDate');
        if( range[0] < start || range[1] > end )return false;
        return true;
    }

    function selectCurrentRange(){
        //$('#nct-range-picker').find('.ui-datepicker-current-day').addClass('ui-state-highlight');
    }
    function unhightlightRange(){
        $('#nct-range-picker').find('.ui-state-highlight').removeClass('ui-state-highlight');
        //$('#nct-range-picker').find('.ui-datepicker-current-day').removeClass('ui-datepicker-current-day');
    }

    function resetRange(){
        var start = $('#nct-start-date').datepicker('getDate');
        if( start ){
            //reset range picker
            $('#nct-range-picker').datepicker('setDate', start);
        }
        unhightlightRange();
        startDate = null;
        endDate = null;
    }

    function selectRange(){
        var val = parseInt($('#nct-range-type').val());
        switch(val){
            case 0:
                $('.start-end').show();
                $('.period-input').hide();
				break;
            default:
                $('.start-end').hide();
                $('.period-input').hide();
				break;
        }
        if( val ==3){
            $('#create-range').hide();
        } else {
            $('#create-range').show();			
        }
        resetRange();
        if( !$('#assign-table tbody tr').length ){
            $('#assign-table tfoot .value-cell').text('0.00');
            $('#nct-total-workload').val('0.00');
        }
    }

    function dateDiff(date1, date2) {
        date1.setHours(0);
        date1.setMinutes(0, 0, 0);
        date2.setHours(0);
        date2.setMinutes(0, 0, 0);
        var datediff = Math.abs(date1.getTime() - date2.getTime()); // difference
        return parseInt(datediff / (24 * 60 * 60 * 1000), 10); //Convert values days and return value
    }

    function dateString(date, format){
        if( !format )format = 'dd-mm-yy';
        return $.datepicker.formatDate(format, date);
    }

    function toRowName(date){
        //parse date from task
        var part = date.split('_');
        switch(part[0]){
            case '1':
            case '3':
                var d = part[1].split('-');
                var start = new Date(d[2], d[1]-1, d[0]);
                d = part[2].split('-');
                var end = new Date(d[2], d[1]-1, d[0]);
                return dateString(start, 'dd/mm') + ' - ' + dateString(end, 'dd/mm/yy');
            case '2':
                var d = part[1].split('-');
                var start = new Date(d[2], d[1]-1, d[0]);
                return monthName[start.getMonth()] + ' ' + d[2];
            default:
                return dateString(new Date(part[1]));
        }
    }
	function loadHolidays(year){
		if( typeof Holidays[year] == 'undefined' ){
			$.ajax({
				url: '<?php echo $this->Html->url('/') ?>holidays/getYear/' + year,
				dataType: 'json',
				success: function(data){
					Holidays[year] = data;
					applyDisableDays();
				}
			});
		}
	}
	function applyDisableDays(){
		hlds = [];
		$.each(Holidays, function(year, list){
			hlds = hlds.concat(list);
		});
		if( hlds.length ){
			$('#date-list').multiDatesPicker('addDates', hlds, 'disabled');
		}
	}
	function refreshPicker(){
		var start = $('#nct-start-date').datepicker('getDate'),
			end = $('#nct-end-date').datepicker('getDate');
		if( start && end && start <= end ){
			$('#date-list').datepicker('setDate', start);
			if( $('#multiselect-popupnct-pm .wd-combobox-content').find('INPUT:checkbox:checked').length > 0 ){
				$('#add-range').removeClass('disabled');
			}
			resetRange();
			$('#nct-period-start-date, #nct-period-end-date').prop('disabled', false);
		}
		else {
			$('#add-range').addClass('disabled');
			$('#nct-period-start-date, #nct-period-end-date').prop('disabled', true);
		}
		if( !start ) year = new Date().getFullYear();
		else year = start.getFullYear();
		loadHolidays(year);
		resetPicker();
		$('#date-list').datepicker('refresh');
		$('#nct-range-picker').datepicker('refresh');
	}
	//if date in disabled fields, reject
	function isValidDate(d, start, end){
		var date = d.getDay();
		return d >= start && d <= end && Workdays[date] == 1;
	}
	function getValidDate(){
		var result = [];
		var dates = $('#date-list').multiDatesPicker('getDates', 'object');
		var start = $('#nct-start-date').datepicker('getDate'),
			end = $('#nct-end-date').datepicker('getDate');
		for(var i = 0; i < dates.length; i++){
			if( isValidDate(dates[i], start, end) ){
				var date = dates[i].getDate(),
					month = dates[i].getMonth() + 1;
				result.push( (date < 10 ? '0' + date : date) + '-' + (month < 10 ? '0' + month : month) + '-' + dates[i].getFullYear() );
			}
		}
		return result;
	}
	$(document).ready(function(){
		 $('#date-list').multiDatesPicker({
            dateFormat: 'dd-mm-yy',
            separator: ',',
            //numberOfMonths: [1,3],
            beforeShowDay: function(d){
                var start = $('#nct-start-date').datepicker('getDate'),
                    end = $('#nct-end-date').datepicker('getDate');
                if( !start || !end || start > end ){
                    return [false, '', ''];
                }
                //var date = d.getDay();
                return [ isValidDate(d, start, end) ,'', ''];
            },
            onChangeMonthYear: function(year){
                loadHolidays(year);
            },
			onSelect:  function( date, inst) {
				if( $('#multiselect-popupnct-pm .wd-combobox-content').find('INPUT:checkbox:checked').length == 0 ){
					return;
				}
				if( date ){
                    var invalid = false;
                    $('.nct-date').each(function(){
                        var value = $(this).text();
                        if( value == date )invalid = true;
                    });
                    if( !invalid ){
                        //add new row
                        var html = '<tr><td id="date-' + date + '" class="nct-date" >' + date + '</td>';
                        $('.header-cell').each(function(){
                            var col = $(this);
                            var id = col.prop('id').replace('col-', '');
                            var hide = '';
                            if( !col.is(':visible') )hide = 'style="display: none"';
                            html += '<td class="value-cell ntc-employee-col cell-' + id + '" ' + hide + '><input type="text" id="val-' + date + '-' + id + '" data-old="0" class="workload workload-' + id + '" data-id="' + id + '" value="0" style="" onchange="changeTotal(this)" data-ref></td>';
                        });
                        html += '<td style="" class="ciu-cell">0.00 (0.00)</td><td class="remove-cell"><a class="cancel" onclick="removeRow(this)" href="javascript:;"><img src="/img/new-icon/close.png" /><img src="/img/new-icon/close-blue.png" /></a></td></tr>';
                        $('#assign-table tbody').append(html);
                    }
                }
				bindNctKeys();
			}
        });

        $('#clean-filters').click(function(){
            try {
                var panel = Ext.getCmp('pmstreepanel');
                panel.cleanFilters();
            } catch(ex){
            }

            var which = $('.type-focus').data('type');
            if( $('.type-focus').hasClass('type-focus') ){
                $('.filter-type').removeClass('type-focus');
                which = '';
            } else {
                $('.filter-type').removeClass('type-focus');
                $('.type-focus').addClass('type-focus');
            }
            $('#clean-filters').addClass('hidden');
        });
        $('#special-task-info').dialog({
            position    :'center',
            autoOpen    : false,
            autoHeight  : true,
            modal       : true,
            width       : 'auto',
            height      : 'auto',
            close       : function(){
                if( Task )Task.destroy();
            }
        });
        //$('#assign-list').height(Math.round((30*$(window).height())/100));
        $('#nct-start-date').datepicker({
            dateFormat: 'dd-mm-yy',
            beforeShowDay: function(d){
                var date = d.getDay();
                return [Workdays[date] == 1, '', ''];
            },
            onSelect: function(){
                //showPicker();
                refreshPicker();
            }
        });
        $('#nct-end-date').datepicker({
            dateFormat: 'dd-mm-yy',
            beforeShowDay: function(d){
                var date = d.getDay();
                var start = $('#nct-start-date').datepicker('getDate'),
                    check = Workdays[date] == 1;
                if( start != null ){
                    check = check && start <= d;
                }
                return [ check, '', ''];
            },
            beforeShow: function(e, i){
                var start = $('#nct-start-date').datepicker('getDate'),
                    end = $(e).datepicker('getDate');
                if( !end && start ){
                    return {defaultDate: start};
                }
            },
            onSelect: function(){
                //showPicker();
                refreshPicker();
            }
        });
		
        $('#nct-reset-date').click(function(){
            refreshPicker();
        });

        $('#cancel-special').click(function(){
            if( $(this).hasClass('disabled') )return false;
            $('#special-task-info').dialog('close');
        });
        $('#save-special').click(function(){
            if( $(this).hasClass('disabled') )return false;
            Task.commit();
        });
		initMultiSelect('#nct-resources-list');

        $('#nct-range-picker').datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            dateFormat: 'dd-mm-yy',
            onSelect: function(dateText, inst) {
                var type = parseInt($('#nct-range-type').val());
                var date = $(this).datepicker('getDate');
                var curStart = $('#nct-start-date').datepicker('getDate'),
                    curEnd = $('#nct-end-date').datepicker('getDate');
                if( type == 1 ){
                    startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 1);  //select monday
                    endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 5);    //select friday
                } else if(type == 2 || type == 0){
                    startDate = new Date(date.getFullYear(), date.getMonth(), 1);  //select first day
                    endDate = new Date(date.getFullYear(), date.getMonth() + 1, 0);  //select last day
                }else {
                    startDate = curStart;
                    endDate = curEnd ;
                }
                //điều chỉnh lại start date / end date của 2 cái input
                if( curStart > startDate ){
                    //$('#nct-start-date').datepicker('setDate', startDate);
                }
                if( curEnd < endDate ){
                    //$('#nct-end-date').datepicker('setDate', endDate);
                }
                var start = dateString(startDate);
                var end = dateString(endDate);
                $('#date-list').multiDatesPicker('resetDates');
                selectCurrentRange();
            },
            beforeShowDay: function(date) {
                var cssClass = '';
                var canSelect = true;
                var start = $('#nct-start-date').datepicker('getDate'),
                    end = $('#nct-end-date').datepicker('getDate');
                if( !start || !end )canSelect = false;
                else canSelect = date >= start && date <= end;
                if(date >= startDate && date <= endDate)
                    cssClass = 'ui-state-highlight';
                return [canSelect, cssClass];
            },
            onChangeMonthYear: function(year, month, inst) {
                selectCurrentRange();
            }
        });
        $('#per-workload').keypress(function(e){
            var key = e.keyCode ? e.keyCode : e.which;
            if(!key || key == 8 || key == 13 || e.ctrlKey){return;}
        });
        $(document).ready(function(){
            $('#per-workload').bind("cut copy paste",function(e) {
                e.preventDefault();
            });
        });
        // $("#per-workload").bind("paste", function(e){
        //     // var pastedData = e.originalEvent.clipboardData.getData('text');
        //     var pastedText;
        //     if (window.clipboardData && window.clipboardData.getData) { // IE
        //         pastedText = window.clipboardData.getData('text');
        //     } else if (e.originalEvent.clipboardData && e.originalEvent.clipboardData.getData) {
        //         pastedText = e.originalEvent.clipboardData.getData('text');
        //     }
        //     if( !$.isNumeric(pastedText) || pastedText < 0 ){
        //         e.preventDefault();
        //         return false;
        //     }
        // });
        $('#nct-unit-price').keypress(function(e){
            var key = e.keyCode ? e.keyCode : e.which;
            if(!key || key == 8 || key == 13 || e.ctrlKey){return;}
        });
        //init datepick period
        $('#nct-period-start-date').datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            dateFormat: 'dd-mm-yy',
            beforeShowDay: function(d){
                var date = d.getDay();
                var start = $('#nct-start-date').datepicker('getDate'),
                    end = $('#nct-end-date').datepicker('getDate');
                var canSelect = d >= start && Workdays[date] == 1 && d <= end;
                return [canSelect, '', ''];
            },
            onSelect: function(dateText, inst) {
                var type = parseInt($('#nct-range-type').val());
                var date = $(this).datepicker('getDate');
                startDate = date;
            },
        });
        $('#nct-period-end-date').datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            dateFormat: 'dd-mm-yy',
            beforeShowDay: function(d){
                var date = d.getDay();
                var start = $('#nct-period-start-date').datepicker('getDate'),
                    end = $('#nct-end-date').datepicker('getDate');
                if( start && end){
                    var canSelect = d >= start && Workdays[date] == 1 && d <= end;
                    return [canSelect, '', ''];
                }
                return [false];
            },
            onSelect: function(dateText, inst) {
                var type = parseInt($('#nct-range-type').val());
                var date = $(this).datepicker('getDate');
                endDate = date;
            },
        });
        $('#nct-range-type').change(function(){
            //xoa het tat ca workload
            $('#assign-table tbody').html('');
            selectRange();
        });
        $('#reset-range').click(function(){
            resetRange();
        });
        $('#add-range').click(function(){
			// console
            if( startDate && endDate ){
                _addRange(startDate, endDate);
            }
            $('.period-input-calendar').datepicker('setDate', null);
			
        });
        $('#create-range').click(function(){
            if( $('#multiselect-popupnct-pm .wd-combobox-content').find('INPUT:checkbox:checked').length == 0 ){
                var dialog = $('#modal_dialog_alert').dialog();
                $('#btnNoAL').click(function() {
                    dialog.dialog('close');
                });
                return;
            }
            var dateType = parseInt($('#nct-range-type').val());
            //available for week and month and day.
            var start = $('#nct-start-date').datepicker('getDate'),
                end = $('#nct-end-date').datepicker('getDate');
            if(!start || !end){
                var dialog = $('#modal_dialog_alert2').dialog();
                $('#btnNoAL2').click(function() {
                    dialog.dialog('close');
                });
                return false;
            }
			setLimitedDate('#nct-start-date', '#nct-end-date');
            addRange(start, end);
        });
		function addRange(start, end, reset){
			if( reset ){
				//xoa het tat ca workload
				$('#assign-table tbody').html('');
				selectRange();
			}
			var type = parseInt($('#nct-range-type').val());
			var list;
			if( type == 0 ) {
				list = getListDay(start, end)
			} else if (type == 1) {
				list = getListWeek(start, end)
			} else {
				list = getListMonth(start, end)
			}
			// var list = type == 1 ? getListWeek(start, end) : getListMonth(start, end),
			var minDate, maxDate;
			$.each(list, function(key, date){
				if(type == 0 ){
					if( !minDate || minDate >= date.date ){
						minDate = new Date(date.date);
					}
					if( !maxDate || maxDate <= date.date ){
						maxDate = new Date(date.date);
					}
					var _curYear = date.date.format('yy'),
						_curDate = date.date.format('dd-mm-yy')
					if(Holidays[_curYear].length == 0){
						loadHolidays(_curYear);
					}
					if( ($.inArray(_curDate, Holidays[_curYear]) == -1) && isValidDateForNctTaskDate(date.date) ){
						_addRange(date.date, date.date);
					}
				} else {
					if( !minDate || minDate >= date.start ){
						minDate = new Date(date.start);
					}
					if( !maxDate || maxDate <= date.end ){
						maxDate = new Date(date.end);
					}
					_addRange(date.start, date.end);
				}
			});
			$('#nct-start-date').datepicker('setDate', minDate);
			$('#nct-end-date').datepicker('setDate', maxDate);
		}
        $('#fill-workload').click(function(){
            var val = parseFloat($('#per-workload').val());
            if( isNaN(val) )return;
            if( $('#nct-range-type').val() == '0' && val > 1 )return;
            $('#assign-table tbody .value-cell input').each(function(){
                $(this).val(val).data('old', val).trigger('change');
            });
            //calculateTotal();
        });
		/*
			@date: js date object
			@day:
				0 = sunday
				1 = monday..
				6 = saturday
			@return: new date object
		*/
		function getDayOfWeek(date, day) {
			var d = new Date(date),
				cday = d.getDay(),
				diff = d.getDate() - cday + day;
			return new Date(d.setDate(diff));
		}
		function getListDay(start, end){
			var list = {};
			var current = new Date(start);
			while( current <= end ){
				var sm = new Date(current);
				var currentDate = dateString(sm ,'yymmdd');
				if( typeof list[currentDate] == 'undefined' ){
					list[currentDate] = {
						// start: monday,
						date: new Date(sm.getFullYear(), sm.getMonth(), sm.getDate())
					};
				}
				current = new Date(sm.getFullYear(), sm.getMonth(), sm.getDate()+1);
			}
			return list;
		}
		function getListWeek(start, end){
			var list = {};
			var current = new Date(start);
			while( current <= end ){
				var monday = getDayOfWeek(current, 1),
					currentDate = dateString(monday , 'yymmdd');
				if( typeof list[currentDate] == 'undefined' ){
					list[currentDate] = {
						start: monday,
						end: getDayOfWeek(current, 5)
					};
				}
				current = new Date(monday.getFullYear(), monday.getMonth(), monday.getDate()+7);
			}
			return list;
		}
		function getListMonth(start, end){
			var list = {};
			var current = new Date(start);
			end = new Date(end.getFullYear(), end.getMonth() + 1, 0);
			while( current <= end ){
				current.setDate(1);
				var sm = new Date(current);
				var currentDate = dateString(sm ,'yymmdd');
				if( typeof list[currentDate] == 'undefined' ){
					list[currentDate] = {
						start: sm,
						end: new Date(sm.getFullYear(), sm.getMonth() + 1, 0)
					};
				}
				current.setMonth(current.getMonth() + 1);
			}
			return list;
		}
		
		function set_width_popupnct(){
			// return;
			$.each( $('.wd-row-inline'), function (i, _row){
				var _row = $(_row);
				var _width = 0;
				$.each( _row.children(), function( j, _col){
					_width += $(_col).width()+41;
				});
				_row.width(_width);
			});
		}
		function _addRange(start, end){
			var date = dateString(start) + '_' + dateString(end);
			var type = parseInt($('#nct-range-type').val());
			var rowName;
			if(type == 0){
				date = dateString(start);
				rowName = start.format('dd-mm-yy');
			} else if (type == 1) {
				rowName = start.format('dd/mm') + ' - ' + end.format('dd/mm/yy');
			}else{
				rowName = monthName[start.getMonth()] + ' ' + start.getFullYear();
			}
			// var rowName = type == 2 ? monthName[start.getMonth()] + ' ' + start.getFullYear() : start.format('dd/mm') + ' - ' + end.format('dd/mm/yy');
			var invalid = $('#date-' + date).length ? true : false;
			if( !invalid ){
				//add new row
				var html = '<tr><td id="date-' + date + '" class="nct-date">' + rowName + '</td>';
				$('.header-cell').each(function(){
					var col = $(this);
					var id = col.prop('id').replace('col-', '');
					var hide = '';
					if( !col.is(':visible') )hide = 'style="display: none"';
					html += '<td class="value-cell cell-' + id + '" ' + hide + '><input type="text" id="val-' + date + '-' + id + '" data-old="0" class="workload-' + id + '" value="0" style="text-align: right; width: 98%; border: 0; padding: 0" onchange="changeTotal(this)" data-ref></td>';
				});
				html += '<td style="background: #f0f0f0" class="ciu-cell">0.00 (0.00)</td><td class="remove-cell"><a class="cancel" onclick="removeRow(this)" href="javascript:;"><img src="/img/new-icon/close.png" /><img src="/img/new-icon/close-blue.png" /></a></td></tr>';
				$('#assign-table tbody').append(html);
				bindNctKeys();
			}
			
		}
    });
	</script>