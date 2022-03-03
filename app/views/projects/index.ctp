<?php
echo $this->Html->css(array(
    'jquery.multiSelect',
    'projects',
    'slick_grid/slick.grid_v2',
    'slick_grid/slick.pager',
    'slick_grid/slick.common_v2',
    'slick_grid/slick.edit',
    'gantt_v2_1',
    '/js/qtip/jquery.qtip',
    'preview/grid-project',
    'preview/projects',
    'preview/slickgrid.css?ver=1.3',
    'preview/layout',
    'preview/customer-logo-popup',
    'add_popup',
    'dropzone.min',
	'jquery.fancybox.css',
	'preview/datepicker-new',
	// 'jquery.ui.core',
	'jquery.ui.custom',
));
echo $this->Html->script(array(
    'jquery.form',
    // 'jquery.multiSelect',
    'jquery.scrollTo',
    'history_filter',
    'slick_grid/lib/jquery-ui-1.8.16.custom.min',
    'slick_grid/slick.core',
    'slick_grid/slick.dataview',
    'slick_grid/controls/slick.pager',
    'slick_grid/slick.formatters',
    'slick_grid/plugins/slick.cellrangedecorator',
    'slick_grid/plugins/slick.cellrangeselector',
    'slick_grid/plugins/slick.cellselectionmodel',
    'slick_grid/plugins/slick.rowselectionmodel',
    'slick_grid/slick.editors',
    'slick_grid_custom',
    'qtip/jquery.qtip',
    'slick_grid/plugins/slick.dataexporter',
    'draw-progress',
	'jquery.fancybox.pack',
	'slick_grid/lib/jquery.event.drag-2.2',
	'slick_grid/slick.grid.origin',
	'progresspie/jquery-progresspiesvg-min.js',
	'dashboard/jqx-all',
	'dashboard/jqxchart_preview',
	'dashboard/jqxcore',
    'dashboard/jqxdata',
    'dashboard/jqxcheckbox',
    'dashboard/jqxradiobutton',
    'dashboard/gettheme',
    'dashboard/jqxgauge',
    'dashboard/jqxbuttons',
    'dashboard/jqxslider',
));
$has_upload = 0;
$first_commment_column = '';
$bg_currency = $budget_settings;
?>
<style>

	.wd-layout .wd-main-content{
		margin: auto;
	}
	.wd-layout > .wd-main-content > .wd-tab{
		margin: 0;
		padding: 15px;
	}
	.wd-layout .wd-main-content .wd-tab .wd-panel{
		margin: auto !important;
		box-sizing: border-box;
	}
	.projects-dashboard-container {
		margin-top: 15px !important;
		margin:auto;
	}
	.projects-dashboard-container,
	.wd-title {
		max-width: 100%;
	}
    .wd-table .slick-header.ui-state-default{
        width: 100%;
    }

    .wd-main-content .wd-project-filter{
        position: relative;
        top: 0;
    }
    .wd-list-project{
        margin-top: 0;
    }
    @media (min-width: 992px){
        .wd-main-content .wd-project-filter{
            left:240px;
        }
    }
    #wd-container-main.wd-project-admin.active{
        padding: 0;
    }
    #sub-nav{
        display: none;
    }
    .search-filter {
        width: calc( 100vw - 50px);
        margin-left: 25px;
    }
    .wd-table .slick-viewport .slick-cell .circle-name{
        width: 30px;
        height: 30px;
        line-height: 30px;
        font-size: 14px;
		vertical-align: middle;
    }
    .slick-cell .circle-name{
        position: relative;
        background-color: #E4AF63;
    }
    .slick-row:nth-child(2n) .slick-cell .circle-name{
        background-color: #67BD65;
    }
    .slick-row:nth-child(3n) .slick-cell .circle-name{
        background-color: #6DAAD3;
    }
    .slick-row:nth-child(4n) .slick-cell .circle-name{
        background-color: #2858B1;
    }
    .circle-name, #add-employee{
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        background-color: #72ADD2;
        color: #fff;
        text-transform: uppercase;
        font-size: 16px;
        text-align: center;
        line-height: 40px;
        font-weight: bold;
        display: inline-block;
        vertical-align: top;
    }
    .circle-name a, #add-employee a, .circle-name span{
        color: #fff;
        font-weight: 600;
    }
    .circle-name a:hover, #add-employee a:hover{
        text-decoration: none;
    }
    .wd-table .slick-pane-header.slick-pane-right{
        background: transparent;
    }
	.wd-table .slick-cell .gantt-ms i{
		position: relative;
		overflow: inherit;
	}
	.wd-table .slick-cell .gantt-ms i:after{
		content: '';
		position: absolute;
		width: calc(100% + 4px);
		height: calc(100% + 4px);
		border: 1px solid #BCBCBC;
		display: block;
		left: -3px;
		top: -3px;
		border-radius: 50%;
	}
</style>
<style>
    .add-project{
        margin-left: 30px;
    }
    .add-project a{
        height: 50px;
        width: 50px;
        background-color: #5487FF;
        box-shadow: 0 0 10px 1px rgba(29,29,27,0.2);
        border-radius: 50%;
        display: block;
        text-align: center;
        line-height: 50px;
        position: absolute;
        z-index: 21;
        -webkit-transition: all 0.2s;
        -moz-transition: all 0.2s;
        -o-transition: all 0.2s;
        transition: all 0.2s;
        top: -25px;
        right: 30px;
    }
    .add-project a:hover{
        -ms-transform: scale(1.05); /* IE 9 */
        -webkit-transform: scale(1.05); /* Safari 3-8 */
        transform: scale(1.05);
    }
    .add-project a.active{
        -ms-transform: rotate(45deg) scale(1.05); /* IE 9 */
        -webkit-transform: rotate(45deg) scale(1.05); /* Safari 3-8 */
        transform: rotate(45deg) scale(1.05);

    }
    .add-project a:before{
        content: '';
        width: 0px;
        height: 100%;
        display: inline-block;
        vertical-align: middle;
    }
    .add-project a img{
        display: inline-block;
        vertical-align: middle;
        margin-top: -1px;
    }

    /* Popup add new project * /
    #addProjectTemplate.open {
        display: block !important;
        position: absolute;
        top: 0;
        right: 0;
        z-index: 20;
        background: #fff;
        color: #C6CCCF;
        background-color: #FFFFFF;
        box-shadow: 0 0 10px 1px rgba(29,29,27,0.06);
        padding-left: 40px;
        padding-right: 30px;
        padding-top: 20px;
        padding-bottom: 30px;
        min-height: 400px;
    }

    #addProjectTemplate.loading:before{
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.9);
        left: 0;
        top: 0;
        z-index: 2;
    }
    #addProjectTemplate.loading:after{
        content: '';
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        background: url(/img/business/wait-1.gif) no-repeat center center;
        z-index: 3;
        display: block;
        background-size: cover;
        width: 50px;
        height: 50px;
    }
    #addProjectTemplate h3{
        color: #424242;
        font-size: 18px;
        margin-bottom: 25px;
        font-weight: 600;
    }
    #addProjectTemplate .wd-input > input, #addProjectTemplate .wd-input > textarea{
        display: block;
    }
    #addProjectTemplate .wd-input textarea{
        resize: vertical;
    }
    #addProjectTemplate .wd-input > input,
    #addProjectTemplate .wd-input > select,
    #addProjectTemplate .wd-input textarea{
        height: 40px;
        border: 1px solid #E1E6E8;
        width: 100%;
        background-color: #FFFFFF;
        box-shadow: 0 0 10px 1px rgba(29,29,27,0.06);
        padding-left: 20px;
        margin-bottom: 20px;
        font-size: 13px;
        border-radius: 2px;
        box-sizing: border-box;
    }
    #addProjectTemplate .wd-input > textarea{
        height: inherit;
        padding-top: 10px;
    }
    #addProjectTemplate .wd-input > input,
    #addProjectTemplate .wd-input textarea{
        width: 100%
    }
    #addProjectTemplate .wd-input > select{
        background: url(../../img/new-icon/down.png) no-repeat 96% center #fff;
        -webkit-appearance: none;
        -moz-appearance: none;
        -ms-appearance: none;
        -o-appearance: none;
        appearance: none;
        color: #C6CCCF;
        font-size: 14px;
    }
	*/
	#addProjectTemplate.open.loading{
		position: fixed;
		width: 100vw;
		height: 100vh;
		z-index:99;
	}
    ::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
        color: #C6CCCF;
        opacity: 1; /* Firefox */
        font-size: 14px;
    }

    :-ms-input-placeholder { /* Internet Explorer 10-11 */
        color: #C6CCCF;
        font-size: 14px;
    }

    ::-ms-input-placeholder { /* Microsoft Edge */
        color: #C6CCCF;
        font-size: 14px;
    }
    .wd-add-project .btn-submit{
        height: 48px;
        text-align: center;
        line-height: 48px;
        background-color: #5487FF;
        border-radius: 5px;
        text-transform: uppercase;
        display: block;
        font-size: 14px;
        width: calc(100% - 25px);
        color: #fff;
    }
    .wd-add-project .btn-submit:hover{
        text-decoration: none;
    }

    #addProjectTemplate .ui-datepicker-trigger {
        vertical-align: top;
        padding: 0;
    }

    #addProjectTemplate .ui-datepicker-trigger {
        position: relative;
        top: -32px;
        left: 90%;
        vertical-align: top;
        cursor: pointer;
    }
    /* IE 10+ */
    @media screen and (-ms-high-contrast: active), (-ms-high-contrast: none) {
        #addProjectTemplate .ui-datepicker-trigger {
            left: 85%;
        }
    }
    #addProjectTemplate #end_date,
    #addProjectTemplate #start_date{
        margin-bottom: 0;
    }
    #addProjectTemplate #price_6, #addProjectTemplate #price_7{
        background: url(../../img/new-icon/question-dark.jpg) no-repeat 96% center #fff;
    }
    select::-ms-expand{
        display: none;
    }

    @media(max-width: 1199px){
        .wd-main-content .wd-list-project{
            margin-top: 0;
        }
        .open-filter-form{
            margin-top: 10px;
        }
        body .wd-project-admin.active{
            padding-top: 0;
        }
        .search-filter{
            padding-bottom: 10px;
        }
    }

    @media(max-width: 480px){
        #addProjectTemplate.open{
            padding-left: 20px;
            padding-right: 20px;
        }
    }
    .project-field .multiselect{
        position: relative;
        border-right: none;
    }
    .project-field .multiselect > a{
        min-height: 40px;
        line-height: 40px;
        border: 1px solid #E1E6E8;
        background-color: #FFFFFF;
        background: linear-gradient(270deg, #FFFFFF 0%, #F9F9F9 100%);
        box-shadow: 0 0 10px 1px rgba(29,29,27,0.06);
        padding-left: 20px;
        margin-bottom: 20px;
        font-size: 13px;
        border-radius: 2px;
        width: calc( 100% - 20px);
        display: block;
        background: url(../../img/new-icon/down.png) no-repeat 96% center #fff;
        height: inherit;
        color: #C6CCCF;
    }


    .project-field .menu-filter{
        width: 96% !important;
        top: 100% ;
        padding: 0;
        display: none;
        z-index: 3 !important;
        margin-left: 5px;
        margin-top: 5px;
    }
    .project-field .menu-filter span{
        display: block;
        background: url(/css/images/search_label.gif) no-repeat 2px center;
        padding-left: 17px;
        background-color: #fff;
        border: 1px solid #ddd;
    }
    .project-field .menu-filter span input{
        border: none;
    }
    .project-field label{
        display: none;
    }

    #addProjectTemplate #add-form {
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 20px;
        margin-bottom: 20px;
    }
    #add-form .project-field{
        margin-right: 3px;
    }
    #add-form .project-field .wd-combobox{
        padding-top: 0;
        padding-bottom: 0;
    }
    .list_multiselect{
        position: absolute;
        top: 100%;
        width: 100%;
        background-color: #fff;
        border: 1px solid #ddd;
        z-index: 2;
    }
    .progress-circle-text >i{
        width: 0px;
        height: 16px;
        background: url(../../img/ajax-loader.gif) no-repeat 2px center;
        display: none;
        transition: all 0.4s ease;
        position: relative;
        top: 0;
        right: 0;
        margin-left: 6px;
    }
    .progress-circle-text.loading i{
        display: inline-block;
        width: 16px;

    }
    #addProjectTemplate ::-webkit-scrollbar{
        width: 4px;
        height: 4px;
        cursor: pointer;
    }

    /* Track */
    #addProjectTemplate ::-webkit-scrollbar-track{
        box-shadow: inset 0 0 5px #F2F5F7; 
        border-radius: 4px;
        background: #F2F5F7;
        cursor: pointer;
    }

    /* Handle */
    #addProjectTemplate ::-webkit-scrollbar-thumb{
        background: #5487FF; 
        border-radius: 4px;
        cursor: pointer;
    }
    ::-ms-clear {
        display: none;
    }

    /*  MULTISELECT */

    .wd-multiselect.multiselect .circle-name{
        height: 30px;	
        width: 30px;
        line-height: 30px;
        vertical-align: middle;
    }
    .wd-multiselect.multiselect .circle-name img{
        border-radius: 50%;
    }
    .wd-multiselect.multiselect .circle-name span{
        font-size: 14px;
        font-weight: 600;
        color: #fff;
        line-height: 30px;
        display: block;
    }
    .wd-multiselect.multiselect{
        font-size: 14px;	
        line-height: 40px;
    }
    .wd-multiselect.multiselect a.wd-combobox{
        padding-left: 10px;
        width: calc( 100% - 10px);
        line-height: 37px;
        font-weight: 400;
    }
    .wd-multiselect.multiselect .wd-combobox .circle-name:not(:last-child){
        margin-right: 5px;
    }
    .wd-multiselect.multiselect .wd-combobox-content{
        position: absolute;
        top: 100%;
        width: calc(100% - 20px);
        z-index: 2;
        overflow: auto;
        box-shadow: 0 0 10px 1px rgba(29,29,27,0.06);
        border: 1px solid #E1E6E8;
        border-top: none;
        background-color: #fff;
        -webkit-transition: all 0.4s;
        -moz-transition: all 0.4s;
        -o-transition: all 0.4s;
        transition: all 0.4s;
        padding: 10px;
    }
    .wd-multiselect.multiselect .wd-combobox-content .wd-data{
        height: 40px;
    }
    .wd-multiselect.multiselect .wd-combobox-content .option-name{
        color: #424242;	font-size: 14px;
    }
    .wd-multiselect.multiselect .wd-combobox-content .context-menu-filter{
        margin: 0;
        padding: 0;
        border: none;
        width: calc(100% - 20px);
        border: 1px solid #E1E6E8;
        background-color: #FFFFFF;
        box-shadow: 0 0 10px 1px rgba(29,29,27,0.06);
        position: absolute;
        z-index: 2;
        border-radius: 3px;
    }
    .wd-multiselect.multiselect .wd-combobox-content .context-menu-filter span {
        background: url(/img/new-icon/search.png) no-repeat 96% center;
        border: none;
    }
    .wd-multiselect.multiselect .wd-combobox-content .context-menu-filter input {
        height: 40px;
        width: 100%;
    }
    .wd-multiselect .wd-combobox-content .option-content{
        height: 160px;
        overflow: auto;
        margin-top: 45px;
    }
    .wd-multiselect .wd-combobox-content .option-content .wd-data-manager{
        cursor: pointer;
    }
    .wd-multiselect .wd-combobox-content .option-content .wd-data-manager input[type = 'checkbox']{
        display: none;
    }
    @media(max-width: 767px){
        .header-bottom .wd-layout-heading ul{
            display: block;
            width: 100%;
        }
        .header-bottom .wd-layout-heading .project-progress{
            display: block;
            width: 100%;
        }
        .wd-layout-heading .project-progress >span{
            text-align: left;
        }
    }
    .log-progress .project-progress {
        display: block;
        position: relative;
        width: 250px;
        text-align: left;
    }
    .project-progress .progress-full {
        display: inline-flex;
        justify-content: space-between;
        background-color: transparent;
    }
    .project-progress .progress-full {
        width: 180px;
        height: 6px;
    }
    .wd-layout-heading .progress-full {
        position: relative;
    }
    .project-progress .progress-node {
        width: calc(10% - 2px);
        margin: 0 1px;
        height: 100%;
        border-radius: 3px;
    }
    .wd-layout-heading .progress-full{
        float: left;
    }
    .wd-sumrow.btn{
        width: 32px;
    }
    .wd-title .btn i.html_entity{
        vertical-align: top;
        font-size: 19px;
        line-height: 29px;
        font-weight: 300;
    }
    .btn.active i{
        color: #5487ff;
    }
    .wd-list-project #scrollTopAbsence{
        float: none;
        position: relative;
		height: 6px;
		margin-bottom: 15px;
		margin-top: 0 !important;
    }
    .slick-row .row-number{
        float: right;
    }
    .wd-title .btn-text span{
        display: none;
    }
	#layout{
		    background: #f2f5f7;
	}
	#filter_alert:hover{
		background-color: #FFF9F9;
	}
	#filter_alert.active{
		border-color: #E94754;
	}
	#filter_alert:before{
		content: '';
		width: 16px;
		height: 16px;
		background: #E94754;
		position: relative;
		border-radius: 3px;
		display: block;
		top: calc( 50% - 8px);
		left: calc( 50% - 8px);
	}
	.wd-row-custom p{
		text-align: left;
		padding-left: 10px;
		padding-right: 0px;
	}
	.slick-headerrow-columns .slick-headerrow-column{
		border: none;
		border-right: 1px solid #E9E9E9;
	}
	.slick-viewport .slick-cell a.project-favorite-action{
		text-align: center;
		width: 30px;
		height: 30px;
		padding: 6px;
		vertical-align: middle;
		display: inline-block;
		border: 1px solid #ddd;
		border-radius: 50%;
		position: relative;
		box-sizing: border-box;
		position: relative;
		background-color: #fff;
		background-position: 6px center;
	}
	.slick-viewport .slick-cell a.project-favorite-action svg{
		vertical-align: top;
	}
	.slick-viewport .slick-cell a.project-favorite-action.loading svg{
		opacity: 0.5;
	}
	.content-right-inner, .wd-layout > .wd-main-content > .wd-tab .wd-panel{
		padding: 15px;
		background: #fff;
		max-width: 100%;
	}
	.wd-title .wd-right {
		width: auto;
	}
	.ui-datepicker select.ui-datepicker-month, 
	.ui-datepicker select.ui-datepicker-year{
		width: 70px;
	}
	.ui-datepicker .ui-datepicker-title{
		    line-height: 2.8em;
	}
	.ui-datepicker .ui-datepicker-header .ui-datepicker-prev, 
	.ui-datepicker .ui-datepicker-header .ui-datepicker-next{
		top: 2px;
	}
	.ui-widget-content .ui-state-hover{
		    background: inherit;
	}
	.ui-datepicker table td.ui-datepicker-today a{
		background-color: #91BFE1;
		color: #fff;
	}
	.wd-list-project .wd-title{
		max-width: 1920px;
		width: auto !important
	}
</style>
<style>.a{fill:none;}.b{fill:#bcbcbc;fill-rule:evenodd;}</style>
<?php
if(!empty($confirmGantt) && !empty($confirmGantt['stones'])){ ?>
	<style>
		.project-list .slick-viewport.slick-viewport-left .grid-canvas-left .slick-row .slick-cell,
		.project-list .slick-viewport.slick-viewport-right .grid-canvas-right .slick-row .slick-cell{
			height: 45px;
			line-height:45px;
		}
		.project-list .slick-viewport.slick-viewport-left .grid-canvas-left .slick-row .slick-cell .grid-action .wd-actions a{
			height: 45px;
			line-height:45px;
		}
	</style>
<?php }
$icons_title = array(
	'project' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><rect class="svg-a" width="24" height="24"/><path class="svg-b" d="M15.611,8.778A4.389,4.389,0,1,1,20,4.389,4.394,4.394,0,0,1,15.611,8.778Zm0-7.064a2.675,2.675,0,1,0,2.675,2.675A2.678,2.678,0,0,0,15.611,1.714ZM10.587,9.961h-3a.857.857,0,0,1,0-1.714h3a.857.857,0,1,1,0,1.714Zm-3,3.619a.857.857,0,0,1,0-1.714h3a.857.857,0,1,1,0,1.714Zm1.5-10.041H4.746A3.035,3.035,0,0,0,1.714,6.57v8.684a3.035,3.035,0,0,0,3.031,3.031H13.43a3.028,3.028,0,0,0,3.031-3.031V10.913a.857.857,0,1,1,1.714,0v4.342A4.743,4.743,0,0,1,13.43,20H4.746A4.751,4.751,0,0,1,0,15.254V6.57A4.751,4.751,0,0,1,4.746,1.825H9.087a.857.857,0,0,1,0,1.714Z" transform="translate(2 2)"/></svg>',
	'weather' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs></defs><rect class="svg-a" width="24" height="24"/><path class="svg-b" d="M10,8.125V6.25h6V8.125ZM10,2.5h6V4.375H10ZM5.312,20A5.309,5.309,0,0,1,2.5,10.186V2.812a2.813,2.813,0,0,1,5.625,0v7.374A5.309,5.309,0,0,1,5.312,20Zm1.472-8.414a.94.94,0,0,1-.534-.847V2.812a.938.938,0,0,0-1.875,0v7.927a.94.94,0,0,1-.534.847,3.438,3.438,0,1,0,2.943,0Z" transform="translate(4 2)"/></svg>',
	'progress' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><rect class="svg-a" width="24" height="24"/><path class="svg-b" d="M19.063,5.625a.938.938,0,0,1-.938-.937V3.2L13.464,7.862h0L11.6,9.725h0a.936.936,0,0,1-1.325,0h0L5.938,5.388,1.6,9.725h0A.937.937,0,1,1,.275,8.4h0l5-5h0A.936.936,0,0,1,6.6,3.4h0l4.338,4.337,1.2-1.2h0l.093-.093.543-.543h0L16.8,1.875H15.313a.938.938,0,1,1,0-1.875h3.75A.938.938,0,0,1,20,.938v3.75A.938.938,0,0,1,19.063,5.625Z" transform="translate(2 7)"/></svg>',
	'milestone' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><rect class="miles-a" width="24" height="24"/><path class="miles-b" d="M2.717,20A2.848,2.848,0,0,1,0,17.043V4.3A2.848,2.848,0,0,1,2.717,1.345H4.189V2.953H2.717A1.265,1.265,0,0,0,1.562,4.3V5.694H18.439V4.3a1.264,1.264,0,0,0-1.156-1.349H15.811V1.345h1.472A2.848,2.848,0,0,1,20,4.3V17.043A2.848,2.848,0,0,1,17.283,20ZM1.562,17.043a1.265,1.265,0,0,0,1.155,1.35H17.283a1.265,1.265,0,0,0,1.156-1.35V7.3H1.562Zm12.313-13.6V.8a.781.781,0,1,1,1.562,0V3.445a.781.781,0,1,1-1.562,0Zm-9.311,0V.8A.781.781,0,1,1,6.125.8V3.445a.781.781,0,1,1-1.561,0ZM6.5,2.953V1.345h7V2.953Z" transform="translate(1.998 2.002)"/><g class="miles-c" transform="translate(5 11)"><rect class="miles-d" width="7" height="8" rx="1"/><rect class="miles-a" x="0.7" y="0.7" width="5.6" height="6.6" rx="0.3"/></g></svg>',
	'program' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><rect class="svg-a" width="24" height="24"/><path class="svg-b" d="M-251.746,762.143V751.309a.865.865,0,0,1,.873-.857.866.866,0,0,1,.873.857v10.834a.866.866,0,0,1-.873.857A.865.865,0,0,1-251.746,762.143Zm-16.259.857A1.952,1.952,0,0,1-270,761.1V744.9a1.952,1.952,0,0,1,1.995-1.9h10.976a1.952,1.952,0,0,1,1.995,1.9v16.2a1.952,1.952,0,0,1-1.995,1.9Zm-.2-18.1v16.2a.2.2,0,0,0,.2.188h10.976a.193.193,0,0,0,.2-.188V744.9a.192.192,0,0,0-.2-.188h-10.976A.194.194,0,0,0-268.2,744.9Zm13.963,17.025V748.977a.958.958,0,0,1,.872-1.025.959.959,0,0,1,.874,1.025v12.951a.959.959,0,0,1-.874,1.025A.958.958,0,0,1-254.24,761.927Zm-11.024-4.726a.879.879,0,0,1-.9-.857.878.878,0,0,1,.9-.857h3.683a.878.878,0,0,1,.9.857.879.879,0,0,1-.9.857Zm-.091-3.344a.878.878,0,0,1-.9-.857.878.878,0,0,1,.9-.856h5.5a.879.879,0,0,1,.9.856.879.879,0,0,1-.9.857Zm0-3.342a.878.878,0,0,1-.9-.857.878.878,0,0,1,.9-.857h5.5a.879.879,0,0,1,.9.857.879.879,0,0,1-.9.857Z" transform="translate(272 -741)"/></svg>',
	'project_manager' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><rect class="svg-a" width="24" height="24"/><g transform="translate(2 2)"><path class="svg-b" d="M1.623,44.536c.388-3.464,1.526-5.171,3.806-5.711a.834.834,0,0,0,.052-1.6,3.69,3.69,0,0,1,1.052-7.2,3.69,3.69,0,0,1,1.052,7.2.834.834,0,0,0,.052,1.6c2.279.539,3.419,2.248,3.806,5.711a.818.818,0,0,0,.9.73.826.826,0,0,0,.717-.918c-.243-2.169-.862-5-3.289-6.439A5.366,5.366,0,0,0,6.533,28.36a5.366,5.366,0,0,0-3.239,9.548C.867,39.347.248,42.18,0,44.347a.826.826,0,0,0,.717.918.888.888,0,0,0,.093.005A.819.819,0,0,0,1.623,44.536Z" transform="translate(0 -25.271)"/><path class="b" d="M94.982,9.549A5.348,5.348,0,0,0,96.988,5.34,5.3,5.3,0,0,0,91.743,0a5.215,5.215,0,0,0-3.877,1.744.841.841,0,0,0,.053,1.172.8.8,0,0,0,1.151-.054,3.593,3.593,0,0,1,2.673-1.2A3.653,3.653,0,0,1,95.36,5.34,3.651,3.651,0,0,1,92.8,8.863a.834.834,0,0,0,.052,1.6c2.28.539,3.418,2.248,3.806,5.711a.82.82,0,0,0,.809.735.888.888,0,0,0,.093-.005.826.826,0,0,0,.717-.918C98.029,13.82,97.409,10.987,94.982,9.549Z" transform="translate(-78.278)"/></g></svg>',
	'sun' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.sun-a{fill:none;}.sun-b{fill:#ee8845;fill-rule:evenodd;}</style></defs><rect class="sun-a" width="24" height="24"/><path class="sun-b" d="M19.143,10.857H17.429a.857.857,0,0,1,0-1.714h1.714a.857.857,0,0,1,0,1.714ZM16.32,4.891a.857.857,0,0,1-1.212-1.212l1.143-1.143a.857.857,0,1,1,1.212,1.212ZM10,16a6,6,0,1,1,6-6A6.007,6.007,0,0,1,10,16ZM10,5.714A4.286,4.286,0,1,0,14.286,10,4.29,4.29,0,0,0,10,5.714Zm0-2.286a.857.857,0,0,1-.857-.857V.857a.857.857,0,1,1,1.714,0V2.571A.857.857,0,0,1,10,3.429ZM4.286,5.143a.855.855,0,0,1-.606-.251L2.537,3.749A.857.857,0,0,1,3.749,2.537L4.891,3.679a.857.857,0,0,1-.606,1.463ZM3.429,10a.857.857,0,0,1-.857.857H.857a.857.857,0,1,1,0-1.714H2.571A.857.857,0,0,1,3.429,10Zm.251,5.108A.857.857,0,1,1,4.891,16.32L3.749,17.463a.857.857,0,0,1-1.212-1.212ZM10,16.571a.857.857,0,0,1,.857.857v1.714a.857.857,0,0,1-1.714,0V17.429A.857.857,0,0,1,10,16.571Zm5.714-1.714a.854.854,0,0,1,.606.251l1.143,1.143a.857.857,0,1,1-1.212,1.212L15.108,16.32a.857.857,0,0,1,.606-1.463Z" transform="translate(2 2)"/></svg>',
	'cloud' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.cloud-a{fill:none;}.cloud-b{fill:#79b2da;fill-rule:evenodd;}</style></defs><rect class="cloud-a" width="24" height="24"/><path class="cloud-b" d="M19.268,9.257H17.8a.773.773,0,0,1,0-1.543h1.463a.773.773,0,0,1,0,1.543Zm-2.9-4.341a.709.709,0,0,1-.517.226.752.752,0,0,1-.732-.771.793.793,0,0,1,.214-.546L16.312,2.8a.713.713,0,0,1,.518-.226.752.752,0,0,1,.732.771.788.788,0,0,1-.215.545Zm-1.576,6.74a3.353,3.353,0,0,1,1.791,3A3.26,3.26,0,0,1,13.415,18H3.659A3.761,3.761,0,0,1,0,14.143a3.884,3.884,0,0,1,1.967-3.418A5.242,5.242,0,0,1,7.073,5.657,4.852,4.852,0,0,1,8.6,5.912a4.073,4.073,0,0,1,3.347-1.8A4.264,4.264,0,0,1,16.1,8.486,4.46,4.46,0,0,1,14.795,11.657ZM7.073,7.2a3.749,3.749,0,0,0-3.646,3.619l-.05.87-.732.4a2.326,2.326,0,0,0-1.181,2.05,2.26,2.26,0,0,0,2.2,2.314h9.756a1.757,1.757,0,0,0,1.707-1.8A1.8,1.8,0,0,0,14.071,13l-.9-.4V11.571a2.249,2.249,0,0,0-2.122-2.309l-.68-.023-.421-.564A3.575,3.575,0,0,0,7.073,7.2Zm4.878-1.543a2.61,2.61,0,0,0-1.993.949A5.291,5.291,0,0,1,11.1,7.721,3.653,3.653,0,0,1,14.267,9.9a2.9,2.9,0,0,0,.367-1.41A2.762,2.762,0,0,0,11.951,5.657Zm0-2.571a.752.752,0,0,1-.732-.771V.771a.733.733,0,1,1,1.463,0V2.314A.752.752,0,0,1,11.951,3.086Zm-3.9,2.057a.711.711,0,0,1-.518-.226L6.556,3.888a.79.79,0,0,1-.214-.545.753.753,0,0,1,.732-.771A.711.711,0,0,1,7.59,2.8l.976,1.029a.791.791,0,0,1,.215.546A.753.753,0,0,1,8.049,5.143Z" transform="translate(2 3)"/></svg>',
	'rain' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.rain-a{fill:none;}.rain-b{fill:#f05352;fill-rule:evenodd;}</style></defs><rect class="rain-a" width="24" height="24"/><path class="rain-b" d="M16.176,14.4a.9.9,0,0,1,0-1.8,2.107,2.107,0,0,0,.792-4.039L15.882,8.1V6.9a2.661,2.661,0,0,0-2.559-2.693L12.5,4.18l-.508-.658A4.359,4.359,0,0,0,8.529,1.8a4.452,4.452,0,0,0-4.4,4.222L4.072,7.036l-.883.472A2.708,2.708,0,0,0,4.412,12.6a.9.9,0,0,1,0,1.8A4.456,4.456,0,0,1,0,9.9,4.508,4.508,0,0,1,2.372,5.912,6.225,6.225,0,0,1,8.529,0,6.117,6.117,0,0,1,13.38,2.408,4.456,4.456,0,0,1,17.647,6.9a3.913,3.913,0,0,1-1.471,7.5ZM7.353,10.2a.891.891,0,0,1,.882.9v1.2a.883.883,0,1,1-1.765,0V11.1A.891.891,0,0,1,7.353,10.2Zm0,3.6a.891.891,0,0,1,.882.9v1.2a.883.883,0,1,1-1.765,0V14.7A.891.891,0,0,1,7.353,13.8Zm2.941-2.4a.891.891,0,0,1,.882.9v1.2a.883.883,0,1,1-1.765,0V12.3A.891.891,0,0,1,10.294,11.4Zm0,3.6a.891.891,0,0,1,.882.9v1.2a.883.883,0,1,1-1.765,0V15.9A.891.891,0,0,1,10.294,15Zm2.941-4.8a.891.891,0,0,1,.882.9v1.2a.883.883,0,1,1-1.765,0V11.1A.891.891,0,0,1,13.235,10.2Zm0,3.6a.891.891,0,0,1,.882.9v1.2a.883.883,0,1,1-1.765,0V14.7A.891.891,0,0,1,13.235,13.8Z" transform="translate(2 3)"/></svg>',
	'setting' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><rect class="svg-a" width="24" height="24"/><path class="svg-b" d="M19.874,11.507a.857.857,0,0,1-.56.68l-1.576.56c-.092.264-.2.521-.315.769l.716,1.509a.855.855,0,0,1-.084.874,9.959,9.959,0,0,1-2.146,2.146.853.853,0,0,1-.875.084l-1.508-.717c-.249.117-.506.222-.77.316L12.195,19.3a.854.854,0,0,1-.676.56A9.848,9.848,0,0,1,10,19.99a9.848,9.848,0,0,1-1.519-.127A.854.854,0,0,1,7.8,19.3l-.561-1.575c-.264-.094-.521-.2-.77-.316l-1.508.717a.853.853,0,0,1-.875-.084A9.959,9.959,0,0,1,1.945,15.9a.855.855,0,0,1-.084-.874l.716-1.509a8.2,8.2,0,0,1-.315-.769l-1.576-.56a.857.857,0,0,1-.56-.68A9.876,9.876,0,0,1,0,9.991,9.879,9.879,0,0,1,.126,8.473a.857.857,0,0,1,.56-.678l1.576-.56a8.061,8.061,0,0,1,.315-.769L1.861,4.957a.857.857,0,0,1,.084-.876A9.981,9.981,0,0,1,4.091,1.936a.853.853,0,0,1,.875-.084l1.509.717a7.869,7.869,0,0,1,.769-.316L7.8.677a.858.858,0,0,1,.676-.56,9.115,9.115,0,0,1,3.038,0,.858.858,0,0,1,.676.56l.561,1.576a8.021,8.021,0,0,1,.77.316l1.508-.717a.854.854,0,0,1,.875.084,9.981,9.981,0,0,1,2.146,2.145.857.857,0,0,1,.084.876l-.716,1.509a8.2,8.2,0,0,1,.315.769l1.576.56a.855.855,0,0,1,.56.678A9.879,9.879,0,0,1,20,9.991,9.876,9.876,0,0,1,19.874,11.507ZM18.245,9.234l-1.479-.526a.853.853,0,0,1-.535-.565,6.426,6.426,0,0,0-.517-1.257.86.86,0,0,1-.021-.777l.671-1.41a8.357,8.357,0,0,0-1.073-1.072l-1.41.67a.85.85,0,0,1-.777-.02,6.463,6.463,0,0,0-1.257-.517.857.857,0,0,1-.564-.535l-.526-1.479a6.957,6.957,0,0,0-1.512,0L8.717,3.225a.857.857,0,0,1-.564.535A6.463,6.463,0,0,0,6.9,4.277a.854.854,0,0,1-.778.02l-1.41-.67A8.356,8.356,0,0,0,3.636,4.7l.671,1.41a.86.86,0,0,1-.021.777,6.5,6.5,0,0,0-.517,1.258.854.854,0,0,1-.535.563l-1.479.527a6.955,6.955,0,0,0,0,1.513l1.479.525a.858.858,0,0,1,.535.565,6.421,6.421,0,0,0,.517,1.257.862.862,0,0,1,.021.778l-.671,1.409a8.366,8.366,0,0,0,1.072,1.073l1.41-.671A.854.854,0,0,1,6.9,15.7a6.463,6.463,0,0,0,1.257.517.854.854,0,0,1,.564.535l.526,1.478a6.958,6.958,0,0,0,1.512,0l.526-1.478a.854.854,0,0,1,.564-.535A6.463,6.463,0,0,0,13.1,15.7a.858.858,0,0,1,.777-.021l1.41.67a8.277,8.277,0,0,0,1.073-1.072l-.671-1.409a.862.862,0,0,1,.021-.778,6.453,6.453,0,0,0,.517-1.257.858.858,0,0,1,.535-.565l1.479-.525a6.966,6.966,0,0,0,0-1.514ZM10,13.757A3.968,3.968,0,1,1,13.969,9.79,3.973,3.973,0,0,1,10,13.757Zm0-6.222A2.254,2.254,0,1,0,12.254,9.79,2.257,2.257,0,0,0,10,7.535Z" transform="translate(2 2.01)"/></svg>',
	'moving' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><rect class="svg-a" width="24" height="24"/><path class="svg-b" d="M10,12V10h2v2ZM5,12V10H7v2ZM0,12V10H2v2ZM10,7V5h2V7ZM5,7V5H7V7ZM0,7V5H2V7ZM10,2V0h2V2ZM5,2V0H7V2ZM0,2V0H2V2Z" transform="translate(6 6)"/></svg>',
	'ok' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g transform="translate(-325 -75)"><rect class="svg-a" width="40" height="40" transform="translate(317 66)"></rect><path class="svg-b" d="M9.791,1.412h0L4.314,7.757h0a.648.648,0,0,1-1.01,0h0L.209,4.171h0A.9.9,0,0,1,0,3.585a.777.777,0,0,1,.714-.827A.668.668,0,0,1,1.219,3h0l2.59,3L8.781.242h0A.668.668,0,0,1,9.285,0,.778.778,0,0,1,10,.827.9.9,0,0,1,9.791,1.412Z" transform="translate(332 82.001)"></path></g></svg>',
	'phase' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><rect class="svg-a" width="24" height="24"/><path class="svg-b" d="M-1280.284,1084a2.848,2.848,0,0,1-2.717-2.957V1068.3a2.848,2.848,0,0,1,2.717-2.957h1.472v1.608h-1.472a1.265,1.265,0,0,0-1.155,1.349v1.392h16.877V1068.3a1.265,1.265,0,0,0-1.156-1.349h-1.472v-1.608h1.472A2.848,2.848,0,0,1-1263,1068.3v12.741a2.848,2.848,0,0,1-2.717,2.957Zm-1.155-2.957a1.266,1.266,0,0,0,1.155,1.349h14.566a1.265,1.265,0,0,0,1.156-1.349V1071.3h-16.877Zm12.313-13.6V1064.8a.794.794,0,0,1,.781-.8.794.794,0,0,1,.781.8v2.642a.793.793,0,0,1-.781.8A.793.793,0,0,1-1269.125,1067.445Zm-9.311,0V1064.8a.793.793,0,0,1,.781-.8.794.794,0,0,1,.781.8v2.642a.793.793,0,0,1-.781.8A.793.793,0,0,1-1278.436,1067.445Zm1.935-.492v-1.608h7v1.608Z" transform="translate(1285 -1062)"/></svg>',
	'calculate' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.calcu-a,.calcu-b{fill:none;}.calcu-b{stroke:#bcbcbc;stroke-linecap:round;stroke-linejoin:round;stroke-width:1.4px;}.calcu-c{fill:#bcbcbc;}.calcu-d{stroke:none;}</style></defs><rect class="calcu-a" width="24" height="24"/><g class="calcu-b" transform="translate(4 2)"><rect class="calcu-d" width="16" height="20" rx="2"/><rect class="calcu-a" x="0.7" y="0.7" width="14.6" height="18.6" rx="1.3"/></g><g class="calcu-b" transform="translate(7 5)"><rect class="calcu-d" width="10" height="5" rx="1"/><rect class="calcu-a" x="0.7" y="0.7" width="8.6" height="3.6" rx="0.3"/></g><path class="calcu-c" d="M16845.7,21992.4a.7.7,0,0,1,0-1.4h2.6a.7.7,0,0,1,0,1.4Zm-6,0a.7.7,0,0,1,0-1.4h2.605a.7.7,0,0,1,0,1.4Z" transform="translate(-16832.004 -21979.398)"/><path class="calcu-c" d="M16845.7,21992.4a.7.7,0,0,1,0-1.4h2.6a.7.7,0,0,1,0,1.4Zm-6,0a.7.7,0,0,1,0-1.4h2.605a.7.7,0,0,1,0,1.4Z" transform="translate(-16832.004 -21976.398)"/><path class="calcu-c" d="M16845.7,21992.4a.7.7,0,0,1,0-1.4h2.6a.7.7,0,0,1,0,1.4Zm-6,0a.7.7,0,0,1,0-1.4h2.605a.7.7,0,0,1,0,1.4Z" transform="translate(-16832.004 -21973.398)"/></svg>',
	'tasks' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.a{fill:none;}.b{fill:#7b7b7b;fill-rule:evenodd;}</style></defs><rect class="a" width="24" height="24"/><path class="b" d="M13.581,19.87a.893.893,0,0,1-.857.031L7,17.03,1.277,19.9A.9.9,0,0,1,.42,19.87.854.854,0,0,1,0,19.138V5.342A2.628,2.628,0,0,1,2.655,2.748h8.691A2.628,2.628,0,0,1,14,5.342v13.8A.853.853,0,0,1,13.581,19.87ZM12.247,5.342a.892.892,0,0,0-.9-.881H2.655a.892.892,0,0,0-.9.881V17.735L6.6,15.3a.9.9,0,0,1,.8,0l4.847,2.431ZM10.284,1.71H3.716A.867.867,0,0,1,2.84.853.867.867,0,0,1,3.716,0h6.568a.867.867,0,0,1,.877.857A.867.867,0,0,1,10.284,1.71Z" transform="translate(5 2.004)"/></svg>',
	'delete' => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14"><g transform="translate(-317 -66)"><rect class="svg-a" width="14" height="14" transform="translate(317 66)"></rect><path transform="translate(606.5 1788)" d="M-287.4-1709.767a.62.62,0,0,1,0-.876l3.942-3.942-3.942-3.943a.619.619,0,0,1,0-.876.619.619,0,0,1,.876,0l3.943,3.943,3.943-3.943a.619.619,0,0,1,.876,0,.619.619,0,0,1,0,.876l-3.942,3.943,3.942,3.942a.62.62,0,0,1,0,.876.62.62,0,0,1-.876,0l-3.943-3.943-3.943,3.943a.618.618,0,0,1-.438.182A.618.618,0,0,1-287.4-1709.767Z" class="svg-b"></path></g></svg>',
	'edit' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><rect class="svg-a" width="24" height="24"/><path class="svg-b" d="M67.355,294.392a.962.962,0,0,1-.926-1.228l1.7-5.96a.955.955,0,0,1,.245-.416l11.5-11.5a3.075,3.075,0,0,1,4.342,0l1.278,1.278a3.071,3.071,0,0,1,0,4.342L74,292.408a.958.958,0,0,1-.415.245l-5.961,1.7A.982.982,0,0,1,67.355,294.392Zm1.4-2.366,4.053-1.158,8.935-8.935-2.9-2.895-8.934,8.934Zm14.351-11.455,1.022-1.021a1.145,1.145,0,0,0,0-1.617l-1.278-1.279a1.148,1.148,0,0,0-1.618,0l-1.022,1.023Z" transform="translate(-64.392 -272.395)"/></svg>',
	'add' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.a{fill:none;}.b{fill:#7b7b7b;}</style></defs><rect class="a" width="24" height="24"/><path class="b" d="M17925,19762v-8h-8a1,1,0,1,1,0-2h8v-8a1,1,0,1,1,2,0v8h8a1,1,0,1,1,0,2h-8v8a1,1,0,1,1-2,0Z" transform="translate(-17914 -19741)"/></svg>',
	'ok' => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14"><g transform="translate(-317 -66)"><rect class="svg-a" width="14" height="14" transform="translate(317 66)" ></rect><path class="svg-fill-green" d="M9.791,1.412h0L4.314,7.757h0a.648.648,0,0,1-1.01,0h0L.209,4.171h0A.9.9,0,0,1,0,3.585a.777.777,0,0,1,.714-.827A.668.668,0,0,1,1.219,3h0l2.59,3L8.781.242h0A.668.668,0,0,1,9.285,0,.778.778,0,0,1,10,.827.9.9,0,0,1,9.791,1.412Z" transform="translate(319 69)" ></path></g></svg>',
	'pen' => '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 20 20">
		<path id="EDIT" class="svg-b" d="M6593.86,260.788c-0.75.767-11.93,11.826-12.4,12.3a0.7,0.7,0,0,1-.27.157c-0.75.224-5.33,1.7-5.37,1.719a0.693,0.693,0,0,1-.2.03,0.625,0.625,0,0,1-.44-0.184,0.636,0.636,0,0,1-.16-0.642c0.01-.044,1.37-4.478,1.64-5.477a0.627,0.627,0,0,1,.16-0.285s11.99-12,12.34-12.343a3.636,3.636,0,0,1,2.42-1.056,3.186,3.186,0,0,1,2.23.981,3.347,3.347,0,0,1,1.18,2.356A3.455,3.455,0,0,1,6593.86,260.788Zm-17.36,12.665c1.21-.39,3.11-1.045,3.97-1.322a3.9,3.9,0,0,0-.94-1.565,4.037,4.037,0,0,0-1.78-1.087C6577.46,270.444,6576.85,272.274,6576.5,273.453Zm3.92-3.789a4.95,4.95,0,0,1,1.07,1.6c2.23-2.2,7.88-7.791,10.33-10.231a3.894,3.894,0,0,0-1.02-1.875,3.944,3.944,0,0,0-1.84-1.1c-2.41,2.409-8.16,8.167-10.37,10.372A5.418,5.418,0,0,1,6580.42,269.664Zm12.51-12.755a1.953,1.953,0,0,0-1.35-.63,2.415,2.415,0,0,0-1.53.69c-0.01.011-.06,0.055-0.09,0.09a5.419,5.419,0,0,1,1.73,1.194,5.035,5.035,0,0,1,1.14,1.763,1.343,1.343,0,0,0,.12-0.119,2.311,2.311,0,0,0,.78-1.534A2.168,2.168,0,0,0,6592.93,256.909Z" transform="translate(-6575 -255)"></path>
	</svg>', 
	'estimated' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><rect class="svg-a" width="24" height="24"/><path class="svg-b" d="M9,0C4.037,0,0,1.963,0,4.375v11.25C0,18.037,4.037,20,9,20s9-1.963,9-4.375V4.375C18,1.963,13.963,0,9,0ZM9,1.875c4.666,0,7.072,1.752,7.072,2.5S13.666,6.875,9,6.875s-7.071-1.752-7.071-2.5S4.335,1.875,9,1.875Zm7.072,13.75c0,.748-2.406,2.5-7.072,2.5s-7.071-1.752-7.071-2.5V12.7A13.938,13.938,0,0,0,9,14.375,13.945,13.945,0,0,0,16.072,12.7ZM9,12.5c-4.665,0-7.071-1.752-7.071-2.5V7.073A13.938,13.938,0,0,0,9,8.75a13.945,13.945,0,0,0,7.072-1.677V10C16.072,10.748,13.666,12.5,9,12.5Z" transform="translate(3 2)"/></svg>',
	'printer' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.ap,.cp{fill:none;}.bp{fill:#7b7b7b;}.cp{stroke:#7b7b7b;stroke-width:2px;}.d{stroke:none;}</style></defs><rect class="ap" width="24" height="24"></rect><path class="bp" d="M17920,19756h0l-2,0a2,2,0,0,1-2-2v-9a2,2,0,0,1,2-2h16.008a2,2,0,0,1,2,2v9a2,2,0,0,1-2,2h-2v-2h1a1,1,0,0,0,1-1v-7a1,1,0,0,0-1-1h-14a1,1,0,0,0-1,1v7a1,1,0,0,0,1,1h1v2Z" transform="translate(-17914.002 -19736.998)"></path><g class="cp" transform="translate(6 2)"><rect class="d" width="12" height="6"></rect><rect class="ap" x="1" y="1" width="10" height="4"></rect></g><g class="cp" transform="translate(6 15)"><rect class="d" width="12" height="7"></rect><rect class="ap" x="1" y="1" width="10" height="5"></rect></g><circle class="bp" cx="1.5" cy="1.5" r="1.5" transform="translate(15 10)"></circle></svg>'
);
echo $this->element('dialog_detail_value');
function draw_line_progress($value=0){
	ob_start();
    $_css_class = ($value <= 100) ? 'green-line': 'red-line';
	$display_value = min($value, 100);
	// if(!empty($adminTaskSetting['Consumed']) || !empty($adminTaskSetting['Manual Consumed'])){
		?>
			<div class="wd-progress-slider <?php echo $_css_class;?>" data-value="<?php echo $value;?>">
				<div class="wd-progress-holder">
					<div class="wd-progress-line-holder"></div>
				</div>
				<div class="wd-progress-value-line" style="width:<?php echo $display_value;?>%;"></div>
				<div class="wd-progress-value-text">
					<div class="wd-progress-value-inner">
						<div class="wd-progress-number" style="left:<?php echo $display_value;?>%;">
							<div class="text"><?php echo round($display_value);?>%</div> 
							<input class="input-progress wd-hide" value="<?php echo $value;?>"  onchange="saveManualProgress(this);" onfocusout="progressHideInput(this)" />
						</div>
					</div>
				</div>
			</div>
		<?php
	// }
    return ob_get_clean();
}
$useManualConsumed = $useManualConsumed = isset($companyConfigs['manual_consumed']) ? intval($companyConfigs['manual_consumed']) : 0;
$showProgress = 1;
if( !empty($companyConfigs['project_progress_method']) && ($companyConfigs['project_progress_method'] == 'no_progress') ) $showProgress = 0; 
if( 
	(empty($companyConfigs['project_progress_method']) || ( $companyConfigs['project_progress_method'] == 'consumed')) 
	&& empty($adminTaskSetting['Consumed']) 
	&& empty($adminTaskSetting['Manual Consumed'] )
){
	$showProgress = 0; 
}

$canModified = $employee_info['Role']['name']== 'admin' ||  $employee_info['Employee']['update_your_form'];
if($viewGantt){
    echo $this->Html->css(array(
        'slick_grid/slick.grid.activity'
    ));
    echo $this->Html->script(array(
        'slick_grid/lib/jquery.event.drag-2.2',
        'slick_grid/slick.grid.activity'
    ));
} else {
    echo $this->Html->css(array(
        'slick_grid/slick.grid',
    ));
    // echo $this->Html->script(array(
        // 'slick_grid/lib/jquery.event.drag-2.0.min',
        // 'slick_grid/slick.grid'
    // ));
}

//Sort Program, Sub-Program. Ticket #455
if(!empty($projectProgram)){
	asort($projectProgram);
}

if(!empty($projectSubProgram)){
	asort($projectSubProgram);
}
if(!empty($budgetCustomers)){
	asort($budgetCustomers);
}
$cates = array(
    1 => __("In progress", true),
    2 => __("Opportunity", true),
    3 => __("Archived", true),
    4 => __("Model", true)
);

App::import("vendor", "str_utility");
$str_utility = new str_utility();
$unit = !empty($employee['Company']['unit']) ? $employee['Company']['unit'] : 'M.D';
$i18n = array(
	'-- Any --' => __('-- Any --', true),
    'M.D' => __($unit, true),
	'minute' => __('cmMinute', true),
	'hour' => __('cmHour', true),
	'day' => __('cmDay', true),
	'd' => __('d', true),
	'month' => __('cmMonth', true),
	'year' => __('cmYear', true),
	'minutes' => __('cmMinutes', true),
	'hours' => __('cmHours', true),
	'days' => __('cmDays', true),
	'months' => __('cmMonths', true),
	'years' => __('cmYears', true),
	'startday' => __('Start date', true),
	'enddate' => __('End date', true),
	'progress' => __('Progress', true),
	'resource' => __('Resource', true),
	'date' => __('Date', true),
	'sun' => __('Sun', true),
	'cloud' => __('Cloud', true),
	'rain' => __('Rain', true),
	'fair' => __('Fair', true),
	'furry' => __('Furry', true),
	'mid' => __('Mid', true),
	'up' => __('Up', true),
	'down' => __('Down', true),
	'project' => __('project', true),
	'projects' => __('projects', true),
	'real' =>  __('Real', true),
	'plan' =>  __('Planed', true),
	'real_end_date' =>  __('Real end date', true),
	'plan_end_date' =>  __('Plan end date', true),
	'real_start_date' =>  __('Real start date', true),
	'plan_start_date' =>  __('Plan start date', true),
	'Late milestone' =>  __('Late milestone', true),
	'late_project' =>  __('Late</br>projects', true),
	'Next milestone' =>  __('Next milestone', true),
	'invBudget' =>  __d(sprintf($_domain, 'Budget_Investment'), 'Budget', true),
	'invEngaged' =>  __d(sprintf($_domain, 'Budget_Investment'), 'Engaged', true),
	'fonBudget' =>  __d(sprintf($_domain, 'Budget_Operation'), 'Budget', true),
	'fonEngaged' =>  __d(sprintf($_domain, 'Budget_Operation'), 'Engaged', true),
	'finaninvBudget' =>  __d(sprintf($_domain, 'Finance_Investment'), 'Budget', true),
	'finaninvEngaged' =>  __d(sprintf($_domain, 'Finance_Investment'), 'Engaged', true),
	'finanfonBudget' =>  __d(sprintf($_domain, 'Finance_Operation'), 'Budget', true),
	'finanfonEngaged' =>  __d(sprintf($_domain, 'Finance_Operation'), 'Engaged', true),
	'BudgetEuro' =>  __d(sprintf($_domain, 'Total_Cost'), 'Budget €', true),
	'ForecastEuro' =>  __d(sprintf($_domain, 'Total_Cost'), 'Forecast €', true),
	'Internal Budget M.D' => __d(sprintf($_domain, 'Internal_Cost'), 'Budget M.D', true),
	'Internal Forecast M.D' => __d(sprintf($_domain, 'Internal_Cost'), 'Forecast M.D', true),
	'Internal Engaged M.D' => __d(sprintf($_domain, 'Internal_Cost'), 'Engaged M.D', true),
	'Internal Budget €' => __d(sprintf($_domain, 'Internal_Cost'), 'Budget €', true),
	'Internal Forecast €' => __d(sprintf($_domain, 'Internal_Cost'), 'Forecast €', true),
	'Internal Engaged €' => __d(sprintf($_domain, 'Internal_Cost'), 'Engaged €', true),
	'Synthesis Engaged €' => __d(sprintf($_domain, 'Total_Cost'), 'Engaged €', true),
	'External Budget Euro' => __d(sprintf($_domain, 'External_Cost'), 'Budget €', true),
	'External Forecast Euro' => __d(sprintf($_domain, 'External_Cost'), 'Forecast €', true),
	'External Var Euro' => __d(sprintf($_domain, 'External_Cost'), 'Var', true),
	'External Ordered Euro' => __d(sprintf($_domain, 'External_Cost'), 'Ordered €', true),
	'Off budget projects' => __("Off budget projects", true),
	'Budget' => __("Budget", true),
	'Engaged' => __("Engaged", true),
	'In progress' => __("In progress", true),
	'Opportunity' => __("Opportunity", true),
	'Archived' => __("Archived", true),
    'Model' => __("Model", true),
	'In time' => __("In time", true),
	'Late' => __("Late", true),
	'Total' => __("Total", true),
	'Target' => __("Target", true),
	'Updated by' => __("Updated by", true),
	'Planed capacity' => __("Planed</br>capacity", true),
	'Select team' => __("Select team", true),
	'confirmDelete' => __('Please confirm you want to delete project<br><strong>%s</strong>', true),
	'No' => __("No", true),
	'Yes' => __("Yes", true),
);
$month_i18n = array();
for($i=1; $i<=12;  $i++){
	$date = '01-'.$i.'-2020';
	$month = date('M', strtotime($date));
	$month_i18n[$month] = __($month, true);
}

if( empty( $synt_i18ns ) ) $synt_i18ns = array();
for ($m=1; $m<=12; $m++) {
	$month = date('M', mktime(0,0,0,$m, 1, 2000));
	$synt_i18ns[$m] = __($month,true);
	$synt_i18ns[$month] = __($month,true);
}

$labelSummaryHeader = array(
	'ProjectWidget.FinancePlus_inv_budget' =>  __d(sprintf($_domain, 'Budget_Investment'), 'Budget', true),
	'ProjectWidget.FinancePlus_inv_engaged' =>  __d(sprintf($_domain, 'Budget_Investment'), 'Engaged', true),
	'ProjectWidget.FinancePlus_fon_budget' =>  __d(sprintf($_domain, 'Budget_Operation'), 'Budget', true),
	'ProjectWidget.FinancePlus_fon_engaged' =>  __d(sprintf($_domain, 'Budget_Operation'), 'Engaged', true),
	'ProjectWidget.FinancePlus_finaninv_budget' =>  __d(sprintf($_domain, 'Finance_Investment'), 'Budget', true),
	'ProjectWidget.FinancePlus_finaninv_engaged' =>  __d(sprintf($_domain, 'Finance_Investment'), 'Engaged', true),
	'ProjectWidget.FinancePlus_finanfon_budget' =>  __d(sprintf($_domain, 'Finance_Operation'), 'Budget', true),
	'ProjectWidget.FinancePlus_finanfon_engaged' =>  __d(sprintf($_domain, 'Finance_Operation'), 'Engaged', true),
	'ProjectWidget.Internal_budget_md' => __d(sprintf($_domain, 'Internal_Cost'), 'Budget M.D', true),
	'ProjectWidget.Internal_forecast_md' => __d(sprintf($_domain, 'Internal_Cost'), 'Forecast M.D', true),
	'ProjectWidget.Internal_consumed_md' => __d(sprintf($_domain, 'Internal_Cost'), 'Engaged M.D', true),
	'ProjectWidget.Internal_budget_euro' => __d(sprintf($_domain, 'Internal_Cost'), 'Budget €', true),
	'ProjectWidget.Internal_forecast_euro' => __d(sprintf($_domain, 'Internal_Cost'), 'Forecast €', true),
	'ProjectWidget.Internal_engaged_euro' => __d(sprintf($_domain, 'Internal_Cost'), 'Engaged €', true),
	'ProjectWidget.External_budget_erro' => __d(sprintf($_domain, 'External_Cost'), 'Budget €', true),
	'ProjectWidget.External_forecast_erro' => __d(sprintf($_domain, 'External_Cost'), 'Forecast €', true),
	'ProjectWidget.External_ordered_erro' => __d(sprintf($_domain, 'External_Cost'), 'Ordered €', true),
	'ProjectWidget.Synthesis_budget' => __d(sprintf($_domain, 'Total_Cost'), 'Budget €', true),
	'ProjectWidget.Synthesis_forecast' => __d(sprintf($_domain, 'Total_Cost'), 'Forecast €', true),
);
$viewManDay = __($unit, true);
$default_widths = array(
	'Project.project_amr_program_id' =>400,
	'Project.project_amr_sub_program_id' =>400,
	'Project.project_name' =>800,
	'Project.project_manager_id' =>290,
	'ProjectAmr.weather' =>172,
	'ProjectAmr.todo' =>700,
	'ProjectAmr.project_amr_progression' =>700,
	'ProjectAmr.done' =>700,
	'ProjectBudgetSyn.Amount€' =>250,
	'default' => 200,
);
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
$cates = array(1 => __("In progress", true), 2 => __("Opportunity", true), 3 => __("Archived", true), 4 => __("Model", true));
$columnAlignRight = array(
    'ProjectBudgetSyn.total_costs_var', 'ProjectBudgetSyn.internal_costs_var', 'ProjectBudgetSyn.external_costs_var',
    'ProjectBudgetSyn.external_costs_progress', 'ProjectBudgetSyn.assign_to_profit_center', 'ProjectBudgetSyn.assign_to_employee',
    'ProjectAmr.budget', 'ProjectAmr.project_amr_cost_control_id',
    'ProjectAmr.project_amr_organization_id', 'ProjectAmr.project_amr_plan_id', 'ProjectAmr.project_amr_perimeter_id',
    'ProjectAmr.project_amr_risk_control_id', 'ProjectAmr.project_amr_problem_control_id',
    'Project.created_value', 'Project.budget', 'ProjectBudgetSyn.ManualConsumed', 'ProjectBudgetSyn.InUsed',
    'Project.number_1', 'Project.number_2', 'Project.number_3', 'Project.number_4', 'Project.number_5', 'Project.number_6', 'Project.number_7', 'Project.number_8', 'Project.number_9',
    'Project.number_10', 'Project.number_11', 'Project.number_12', 'Project.number_13', 'Project.number_14', 'Project.number_15', 'Project.number_16', 'Project.number_17', 'Project.number_18'
);
$columnAlignRight = array_merge($columnAlignRight, $financeFields);
$columnAlignRightAndEuro = array(
    'ProjectBudgetSyn.sales_sold', 'ProjectBudgetSyn.sales_to_bill', 'ProjectBudgetSyn.sales_billed', 'ProjectBudgetSyn.sales_paid',
    'ProjectBudgetSyn.purchases_sold', 'ProjectBudgetSyn.purchases_to_bill', 'ProjectBudgetSyn.purchases_billed', 'ProjectBudgetSyn.purchases_paid',
    'ProjectBudgetSyn.total_costs_budget', 'ProjectBudgetSyn.total_costs_forecast', 'ProjectBudgetSyn.total_costs_engaged', 'ProjectBudgetSyn.total_costs_remain',
    'ProjectBudgetSyn.internal_costs_budget', 'ProjectBudgetSyn.internal_costs_forecast', 'ProjectBudgetSyn.internal_costs_engaged',
    'ProjectBudgetSyn.internal_costs_remain', 'ProjectBudgetSyn.external_costs_budget', 'ProjectBudgetSyn.external_costs_forecast', 'ProjectBudgetSyn.external_costs_ordered',
    'ProjectBudgetSyn.external_costs_remain', 'ProjectBudgetSyn.external_costs_progress_euro', 'ProjectBudgetSyn.internal_costs_average',
    'ProjectFinance.bp_investment_city', 'ProjectFinance.bp_operation_city', 'ProjectFinance.available_investment',
    'ProjectFinance.available_operation', 'ProjectFinance.finance_total_budget',
    'Project.price_1', 'Project.price_2', 'Project.price_3', 'Project.price_4', 'Project.price_5', 'Project.price_6', 'Project.price_7', 'Project.price_8', 'Project.price_9', 'Project.price_10',
    'Project.price_11', 'Project.price_12', 'Project.price_13', 'Project.price_14', 'Project.price_15', 'Project.price_16', 'ProjectBudgetSyn.Workload€', 'ProjectBudgetSyn.Consumed€', 'ProjectBudgetSyn.Remain€', 'ProjectBudgetSyn.Amount€', 'ProjectBudgetSyn.Estimated€',
    'ProjectBudgetSyn.UnitPrice', 'ProjectBudgetSyn.%progressorder€',
);
$columnAlignRightAndEuro = array_merge($columnAlignRightAndEuro, $finEuros, $finTwoPlusEuros);
$columnAlignRightAndManDay = array(
    'ProjectAmr.manual_consumed', 'ProjectAmr.md_validated', 'ProjectAmr.md_engaged', 'ProjectBudgetSyn.internal_costs_engaged_md', 'ProjectAmr.md_forecasted','ProjectAmr.md_variance',
    'ProjectBudgetSyn.internal_costs_budget_man_day', 'ProjectBudgetSyn.sales_man_day', 'ProjectBudgetSyn.total_costs_man_day', 'ProjectBudgetSyn.internal_costs_forecasted_man_day', 'ProjectBudgetSyn.external_costs_man_day', 'ProjectAmr.delay',
    'ProjectBudgetSyn.workload_y', 'ProjectBudgetSyn.workload_last_one_y', 'ProjectBudgetSyn.workload', 'ProjectBudgetSyn.overload', 'ProjectBudgetSyn.ManualConsumed', 'ProjectBudgetSyn.InUsed',
    'ProjectBudgetSyn.workload_last_two_y', 'ProjectBudgetSyn.workload_last_thr_y', 'ProjectBudgetSyn.workload_next_one_y', 'ProjectBudgetSyn.workload_next_two_y', 'ProjectBudgetSyn.consumed',
    'ProjectBudgetSyn.workload_next_thr_y', 'ProjectBudgetSyn.consumed_y', 'ProjectBudgetSyn.consumed_last_one_y', 'ProjectBudgetSyn.consumed_last_two_y', 'ProjectBudgetSyn.consumed_last_thr_y',
    'ProjectBudgetSyn.consumed_next_one_y', 'ProjectBudgetSyn.consumed_next_two_y', 'ProjectBudgetSyn.consumed_next_thr_y', 'ProjectBudgetSyn.Initialworkload', 'ProjectBudgetSyn.Remain', 'ProjectBudgetSyn.Consumed', 'ProjectBudgetSyn.Workload', 'ProjectBudgetSyn.Overload',
    'ProjectBudgetSyn.provisional_budget_md', 'ProjectBudgetSyn.provisional_y', 'ProjectBudgetSyn.provisional_last_one_y', 'ProjectBudgetSyn.provisional_last_two_y',
    'ProjectBudgetSyn.provisional_last_thr_y', 'ProjectBudgetSyn.provisional_next_one_y', 'ProjectBudgetSyn.provisional_next_two_y', 'ProjectBudgetSyn.provisional_next_thr_y'
);
$columnAlignRightAndPercent = array('ProjectBudgetSyn.roi', 'ProjectBudgetSyn.Completed', 'ProjectBudgetSyn.%progressorder');
$columnAlignRightAndPercent = array_merge($columnAlignRightAndPercent, $finPercents, $finTwoPlusPercent);
$widgetCaculateTotalEuro = array(
	'ProjectWidget.FinancePlus_inv_budget',
	'ProjectWidget.FinancePlus_inv_engaged',
	'ProjectWidget.FinancePlus_fon_budget',
	'ProjectWidget.FinancePlus_fon_engaged',
	'ProjectWidget.FinancePlus_finaninv_budget',
	'ProjectWidget.FinancePlus_finaninv_engaged',
	'ProjectWidget.FinancePlus_finanfon_budget',
	'ProjectWidget.FinancePlus_finanfon_engaged',
	'ProjectWidget.Synthesis_budget',
	'ProjectWidget.Synthesis_forecast',
	'ProjectWidget.External_budget_erro',
	'ProjectWidget.External_forecast_erro',
	'ProjectWidget.External_ordered_erro',
	'ProjectWidget.Internal_budget_euro',
	'ProjectWidget.Internal_forecast_euro',
	'ProjectWidget.Internal_engaged_euro',
);

$widgetCaculateTotalMD = array(
	'ProjectWidget.Internal_budget_md',
	'ProjectWidget.Internal_forecast_md',
	'ProjectWidget.Internal_consumed_md',
);
$widgetCaculateTotalPercent = array(
	'ProjectWidget.FinancePlus_inv_percent',
	'ProjectWidget.FinancePlus_fon_percent',
	'ProjectWidget.FinancePlus_finaninv_percent',
	'ProjectWidget.FinancePlus_finanfon_percent',
	'ProjectWidget.Synthesis_percent',
	'ProjectWidget.Internal_percent_forecast_md',
	'ProjectWidget.Internal_percent_consumed_md',
	'ProjectWidget.External_percent_forecast_erro',
	'ProjectWidget.External_percent_ordered_erro',
	'ProjectWidget.External_var_erro',
	'ProjectWidget.Internal_percent_forecast_euro',
	'ProjectWidget.Internal_percent_consumed_euro',
);
$columnCalculationConsumeds = array(
    'ProjectBudgetSyn.purchases_sold', 'ProjectBudgetSyn.purchases_to_bill', 'ProjectBudgetSyn.purchases_billed', 'ProjectBudgetSyn.purchases_paid',
    'ProjectAmr.manual_consumed', 'ProjectAmr.md_validated', 'ProjectAmr.md_engaged', 'ProjectBudgetSyn.internal_costs_engaged_md', 'ProjectAmr.md_forecasted','ProjectAmr.md_variance',
    'ProjectAmr.engaged','ProjectAmr.variance','ProjectAmr.forecasted','ProjectBudgetSyn.sales_sold', 'ProjectBudgetSyn.Workload€', 'ProjectBudgetSyn.Consumed€', 'ProjectBudgetSyn.Remain€', 'ProjectBudgetSyn.Amount€', 'ProjectBudgetSyn.Estimated€',
    'ProjectBudgetSyn.sales_to_bill', 'ProjectBudgetSyn.sales_billed', 'ProjectBudgetSyn.sales_paid', 'ProjectBudgetSyn.Consumed', 'ProjectBudgetSyn.Remain', 'ProjectBudgetSyn.Workload', 'ProjectBudgetSyn.Initialworkload', 'ProjectBudgetSyn.UnitPrice', 'ProjectBudgetSyn.Overload',
    'ProjectBudgetSyn.sales_man_day', 'ProjectBudgetSyn.total_costs_budget', 'ProjectBudgetSyn.total_costs_forecast', 'ProjectBudgetSyn.ManualConsumed', 'ProjectBudgetSyn.InUsed', 'ProjectBudgetSyn.%progressorder€',
    'ProjectBudgetSyn.total_costs_engaged', 'ProjectBudgetSyn.total_costs_remain', 'ProjectBudgetSyn.total_costs_man_day',
    'ProjectBudgetSyn.internal_costs_budget', 'ProjectBudgetSyn.internal_costs_forecast', 'ProjectBudgetSyn.internal_costs_engaged',
    'ProjectBudgetSyn.internal_costs_remain', 'ProjectBudgetSyn.internal_costs_forecasted_man_day', 'ProjectBudgetSyn.external_costs_budget',
    'ProjectBudgetSyn.external_costs_forecast', 'ProjectBudgetSyn.external_costs_ordered', 'ProjectBudgetSyn.external_costs_remain',
    'ProjectBudgetSyn.external_costs_man_day', 'ProjectBudgetSyn.internal_costs_budget_man_day', 'ProjectBudgetSyn.internal_costs_average',
    'ProjectAmr.delay', 'ProjectFinance.bp_investment_city', 'ProjectFinance.bp_operation_city', 'ProjectFinance.available_investment',
    'ProjectFinance.available_operation', 'ProjectFinance.finance_total_budget', 'ProjectBudgetSyn.workload_y', 'ProjectBudgetSyn.workload_last_one_y',
    'ProjectBudgetSyn.workload_last_two_y', 'ProjectBudgetSyn.workload_last_thr_y', 'ProjectBudgetSyn.workload_next_one_y', 'ProjectBudgetSyn.workload_next_two_y',
    'ProjectBudgetSyn.workload_next_thr_y', 'ProjectBudgetSyn.consumed_y', 'ProjectBudgetSyn.consumed_last_one_y', 'ProjectBudgetSyn.consumed_last_two_y', 'ProjectBudgetSyn.consumed_last_thr_y', 'ProjectBudgetSyn.consumed',
    'ProjectBudgetSyn.consumed_next_one_y', 'ProjectBudgetSyn.consumed_next_two_y', 'ProjectBudgetSyn.consumed_next_thr_y', 'ProjectBudgetSyn.workload', 'ProjectBudgetSyn.overload',
    'ProjectBudgetSyn.provisional_budget_md', 'ProjectBudgetSyn.provisional_y', 'ProjectBudgetSyn.provisional_last_one_y', 'ProjectBudgetSyn.provisional_last_two_y',
    'ProjectBudgetSyn.provisional_last_thr_y', 'ProjectBudgetSyn.provisional_next_one_y', 'ProjectBudgetSyn.provisional_next_two_y', 'ProjectBudgetSyn.provisional_next_thr_y',
    'Project.price_1', 'Project.price_2', 'Project.price_3', 'Project.price_4', 'Project.price_5', 'Project.price_6', 'Project.price_7', 'Project.price_8', 'Project.price_9', 'Project.price_10',
    'Project.price_11', 'Project.price_12', 'Project.price_13', 'Project.price_14', 'Project.price_15', 'Project.price_16',
    'Project.number_1', 'Project.number_2', 'Project.number_3', 'Project.number_4', 'Project.number_5', 'Project.number_6', 'Project.number_7', 'Project.number_8', 'Project.number_9', 'Project.number_10',
    'Project.number_11', 'Project.number_12', 'Project.number_13', 'Project.number_14', 'Project.number_15', 'Project.number_16', 'Project.number_17', 'Project.number_18',
);
$columnCalculationConsumeds = array_merge($columnCalculationConsumeds, $finEuros, $finTwoPlusEuros, $widgetCaculateTotalEuro, $widgetCaculateTotalMD);
$listDatas = array(
    'workload_y' => __('Workload', true) . ' ' . date('Y', time()),
    'workload_last_one_y' => __('Workload', true) . ' ' . (date('Y', time()) - 1),
    'workload_last_two_y' => __('Workload', true) . ' ' . (date('Y', time()) - 2),
    'workload_last_thr_y' => __('Workload', true) . ' ' . (date('Y', time()) - 3),
    'workload_next_one_y' => __('Workload', true) . ' ' . (date('Y', time()) + 1),
    'workload_next_two_y' => __('Workload', true) . ' ' . (date('Y', time()) + 2),
    'workload_next_thr_y' => __('Workload', true) . ' ' . (date('Y', time()) + 3),
    'consumed_y' => __('Consumed', true) . ' ' . date('Y', time()),
    'consumed_last_one_y' => __('Consumed', true) . ' ' . (date('Y', time()) - 1),
    'consumed_last_two_y' => __('Consumed', true) . ' ' . (date('Y', time()) - 2),
    'consumed_last_thr_y' => __('Consumed', true) . ' ' . (date('Y', time()) - 3),
    'consumed_next_one_y' => __('Consumed', true) . ' ' . (date('Y', time()) + 1),
    'consumed_next_two_y' => __('Consumed', true) . ' ' . (date('Y', time()) + 2),
    'consumed_next_thr_y' => __('Consumed', true) . ' ' . (date('Y', time()) + 3),
    'provisional_budget_md' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . __($unit, true),
    'provisional_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . date('Y', time()),
    'provisional_last_one_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) - 1),
    'provisional_last_two_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) - 2),
    'provisional_last_thr_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) - 3),
    'provisional_next_one_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) + 1),
    'provisional_next_two_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) + 2),
    'provisional_next_thr_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) + 3)
);
$columnNoFilters = array(
    'no.','action.','ProjectAmr.customer_point_of_view'
);
$columnDefaultTranslate = array(
    'Project.category', 'ProjectWidget.Progress', 'Project.created', 'Project.updated_opp_ip', 'Project.updated_ip_arch'
);
$columnSelected = array(
    'Project.project_manager_id', 'Project.project_phase_id', 'Project.team', 'Project.project_priority_id', 'Project.project_status_id', 'Project.project_type_id',
    'Project.project_sub_type_id','Project.project_sub_sub_type_id', 'Project.project_amr_program_id', 'Project.project_amr_sub_program_id', 'Project.list_1', 'Project.list_2',
    'Project.list_3', 'Project.list_4', 'Project.list_5', 'Project.list_6', 'Project.list_7', 'Project.list_8', 'Project.list_9', 'Project.list_10',
    'Project.list_11', 'Project.list_12', 'Project.list_13', 'Project.list_14', 'Project.bool_1', 'Project.bool_2', 'Project.bool_3', 'Project.bool_4', 'Project.list_muti_1', 'Project.list_muti_2',
    'Project.list_muti_3', 'Project.list_muti_4', 'Project.list_muti_5', 'Project.list_muti_6', 'Project.list_muti_7', 'Project.list_muti_8', 'Project.list_muti_9', 'Project.list_muti_10', 'Project.chief_business_id',  'Project.technical_manager_id',  'Project.read_access', 'Project.functional_leader_id', 'Project.uat_manager_id','ProjectAmr.weather', 'ProjectAmr.rank', 'ProjectAmr.cost_control_weather', 'ProjectAmr.planning_weather', 'ProjectAmr.risk_control_weather',
    'ProjectAmr.organization_weather', 'ProjectAmr.issue_control_weather', 'ProjectAmr.technical_weather', 'ProjectAmr.resources_weather', 'ProjectAmr.schedule_weather', 'ProjectAmr.scope_weather', 'ProjectAmr.budget_weather', 'Project.budget_customer_id', 'Project.category'
);
$columnNotCalculationConsumed = array(
    'no.', 'Project.project_name', 'Project.long_project_name', 'Project.project_code_1', 'Project.project_code_2', 'Project.project_manager_id',
    'Project.technical_manager_id', 'Project.read_access', 'Project.project_phase_id', 'Project.project_priority_id', 'Project.project_status_id', 'Project.start_date',
    'Project.end_date', 'Project.primary_objectives', 'Project.project_objectives', 'Project.issues', 'Project.constraint', 'Project.remark', 'Project.company_id',
    'Project.project_type_id', 'Project.project_sub_type_id', 'Project.chief_business_id', 'Project.project_amr_program_id', 'Project.project_amr_sub_program_id',
    'Project.copy_number', 'Project.complexity_id', 'Project.created_value', 'Project.activity_id', 'Project.created', 'Project.updated_opp_ip', 'Project.updated_ip_arch', 'Project.category', 'Project.update_by_employee',
    'Project.project_copy', 'Project.project_copy_id', 'Project.budget_customer_id', 'Project.is_freeze', 'Project.freeze_by', 'Project.freeze_time', 'Project.off_freeze',
    'Project.last_modified', 'Project.free_1', 'Project.free_2', 'Project.free_3', 'Project.free_4', 'Project.free_5', 'Project.functional_leader_id', 'Project.uat_manager_id', 'Project.address',
    'Project.date_1', 'Project.date_2', 'Project.date_3', 'Project.date_4', 'Project.date_5', 'Project.date_6', 'Project.date_7', 'Project.date_8', 'Project.date_9', 'Project.date_10', 'Project.date_11', 'Project.date_12', 'Project.date_13', 'Project.date_14',
    'Project.list_1', 'Project.list_2', 'Project.list_3', 'Project.list_4', 'Project.list_5', 'Project.list_6', 'Project.list_7', 'Project.list_8', 'Project.list_9', 'Project.list_10', 'Project.list_11', 'Project.list_12', 'Project.list_13', 'Project.list_14',
    'Project.yn_1', 'Project.yn_2', 'Project.yn_3', 'Project.yn_4', 'Project.yn_5', 'Project.yn_6', 'Project.yn_7', 'Project.yn_8', 'Project.yn_9',
    'Project.date_mm_yy_1', 'Project.date_mm_yy_2', 'Project.date_mm_yy_3', 'Project.date_mm_yy_4', 'Project.date_mm_yy_5', 'Project.date_yy_1', 'Project.date_yy_2', 'Project.date_yy_3', 'Project.date_yy_4', 'Project.date_yy_5',
    'Project.bool_1', 'Project.bool_2', 'Project.bool_3', 'Project.bool_4', 'Project.activated', 'Project.team',
    'ProjectAmr.budget', 'ProjectAmr.project_amr_progression', 'ProjectAmr.project_amr_risk_information', 'ProjectAmr.project_amr_problem_information',
    'ProjectAmr.project_amr_solution', 'ProjectAmr.project_amr_solution_description', 'ProjectAmr.created', 'ProjectAmr.updated', 'ProjectAmr.rank',
    'ProjectAmr.weather', 'ProjectAmr.cost_control_weather', 'ProjectAmr.planning_weather', 'ProjectAmr.risk_control_weather',
    'ProjectAmr.organization_weather', 'ProjectAmr.issue_control_weather', 'ProjectAmr.customer_point_of_view', 'ProjectAmr.done', 'ProjectAmr.todo', 'ProjectAmr.comment',
    'ProjectBudgetSyn.internal_costs_var', 'ProjectBudgetSyn.external_costs_var', 'ProjectBudgetSyn.external_costs_progress', 'ProjectBudgetSyn.total_costs_var',
    'ProjectBudgetSyn.assign_to_profit_center', 'ProjectBudgetSyn.assign_to_employee', 'ProjectBudgetSyn.roi', 'ProjectBudgetSyn.Completed', 'ProjectBudgetSyn.%progressorder', 'action.', 'Project.list_muti_1', 'Project.list_muti_2','ProjectAmr.scope_weather', 'ProjectAmr.budget_weather','ProjectAmr.risk_control_weather',
    'ProjectAmr.organization_weather', 'ProjectAmr.issue_control_weather', 'ProjectAmr.technical_weather', 'ProjectAmr.resources_weather', 'ProjectAmr.schedule_weather',
    'Project.list_muti_3', 'Project.list_muti_4', 'Project.list_muti_5', 'Project.list_muti_6', 'Project.list_muti_7', 'Project.list_muti_8', 'Project.list_muti_9', 'Project.list_muti_10', 'Project.next_milestone_in_day', 'Project.next_milestone_in_week', 'ProjectAmr.project_amr_scope', 'ProjectWidget.Milestone_late','ProjectWidget.Milestone_next', 'ProjectWidget.Phase_diff', 'ProjectWidget.Phase_plan','ProjectWidget.Project_progress', 'ProjectWidget.Phase_progress','ProjectWidget.Phase_real','MFavorite.modelId'
);
$columnNotCalculationConsumed = array_merge($columnNotCalculationConsumed, $finPercents, $finTwoPlusPercent);
?>

<style>
    .slick-header .slick-header-column{
        padding: 10px 5px !important;
        border-right: 1px solid #fff !important;
    }
    // .slick-pane-top {
        // top: 69px !important;
    // }
    .slick-pane-right .slick-cell,
    .slick-pane-right .slick-headerrow-column {
        border-right-color: #aaa;
        border-left: 0;
    }
    .slick-header-columns-right .slick-header-column.wd-highlight-column:after{
        position: absolute;
        width: 100%;
        height: 100%;
        content:'';
        top: 0;
        left: 0;
        background: rgba(255,255,255,0.1);
    }
    #show_count_task{
        text-align: center;
        font-size: 18px;
    }
    .ok-new-project, .cancel-new-project{
        min-width: 115px;
        border: none;
        height: 44px;
        border-radius: 3px;
		margin-left: -10px;
		margin-top: 20px;
    }
	
    .ok-new-project:hover, .cancel-new-project:hover{
        cursor: pointer;
    }
	.cancel-new-project{
		margin-left: 20px;
	}
    #btnNoAL{
        margin-left: 190px;
    }
    #modal_dialog_alert{
        min-height: 60px !important;
    }
    #modal_dialog_confirm{
        min-height: 40px !important;
		padding: 20px;
		text-align: center;
    }
    #dialogDetailValue{
        min-width: 200px !important;
        top: 20%;
        left: 20%;
    }
    #contentDialog img{
        max-width: 100%;
        max-height: 600px;
    }
    .wd-tab .wd-panel{
        padding: 0;
    }
    .btn-grid{
        width: 32px;
        height: 32px;
        line-height: 37px;
        text-align: center;
        font-size: 20px;
        color: #424242;
    }
    .btn-grid:hover{
        text-decoration: none;
    }
    body {
        overflow: hidden;
    }
    #layout{
        min-height: 740px;
    }
    .wd-title{
		margin-left: auto;
		margin-right: auto;
        margin-top: 10px;
		box-sizing: border-box;
    }
    #layout #wd-container-main .wd-layout{
        padding: 0;
        background: #f2f5f7;
		overflow: visible;
    }
    #wd-container-footer{
        display: none;
    }
    #dialogDetailValue:hover{
        cursor: move;
    }
    #dialogDetailValue .dialog_no_drag{
        cursor: default;
    }
    #wd-fragment-2:hover{
        cursor:auto;
    }
    .wd-table .slick-headerrow-column{
        background:  transparent;
    }
    #template_logs #content_comment .log-progress{
        height: auto;
		padding-top: 10px;
    }
    #template_logs #content_comment #update-comment{
        border: 1px solid #E1E6E8;  
        background-color: #FFFFFF;  
        box-shadow: 0 0 10px 1px rgba(29,29,27,0.06); 
        width: calc( 100% - 20px ); 
        padding: 10px;
        resize: none;
        max-height: 150px;
        height: 98px;
    }
    #template_logs #content_comment .comment{
        margin-bottom: 15px;
    }
    #template_logs #content_comment{
        height: 100%;
        margin-top: -14px;
        position: relative;
        padding-bottom: 14px;
    }
    #template_logs .content-logs{
        height: calc( 100% - 200px);
        overflow-y: auto;
    }
    #content_comment.loading:before{
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.9);
        left: 0;
        top: 0;
        z-index: 2;
    }
    #content_comment.loading:after{
        content: '';
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        background: url(/img/business/wait-1.gif) no-repeat center center;
        z-index: 3;
        display: block;
        background-size: cover;
        width: 50px;
        height: 50px;
    }
    .wd-list-project .wd-title .btn:before,
    .wd-list-project .wd-title .btn i{
        line-height: 38px;
        color: inherit;
    }
    .wd-list-project .wd-title .btn{
        height: 40px;
        width: 40px;
        line-height: 38px;
        display: none;
        border: 1px solid #E1E6E8;
        background-color: #FFFFFF;
        border-radius: 3px;
        padding: 0;
        box-sizing: border-box;
        display: inline-block;
        text-align: center;
        transition: all 0.3s ease;
        color: #666;
    }
	.wd-list-project .wd-title .btn.wd-sumrow.active{
		background: #5487ff;
		color: #fff;
	}
	.wd-list-project .wd-title .btn.btn-project-favorite{
		padding: 10px;
	}
	.wd-list-project .wd-title .btn.btn-project-favorite:hover{
		background-color: transparent;
	}
	.wd-list-project .wd-title .btn.btn-project-favorite.active svg .svg-stroke-color{
		stroke: #79b2da;
		fill: #79b2da;
	}
	.wd-list-project .wd-title .btn.btn-project-favorite svg{
		vertical-align: top;
	}
    .wd-list-project .wd-title .btn:hover{
        background-color: #247FC3;
        color: #fff;
    }
    .wd-list-project .wd-title .btn:hover svg .cls-1{
        fill: #fff;
    }
    .wd-list-project .wd-title .btn:hover .line{
        background: #fff;
    }
    .wd-list-project .wd-title .btn:hover:before,
    .wd-list-project .wd-title .btn:hover i{
        color: inherit;
    }
    .wd-main-content .wd-list-project .wd-title select {
        height: 40px;
        width: 150px;
        border: 1px solid #E0E6E8;
        padding: 0 20px 0 10px;
        color: #666666;
        font-family: "Open Sans";
        font-size: 14px;
        line-height: 38px;
        -webkit-appearance: none;
        -moz-appearance: none;
        -ms-appearance: none;
        -o-appearance: none;
        appearance: none;
        background: url(/img/new-icon/down.png) no-repeat right 10px center #fff !important;
    }
    .wd-title select::-ms-expand {
        display: none;
    }
	.slick-viewport .slick-row.odd {
		background: #FFF;
	}
	.wd-progress-pie {
		padding:0;
	}
	.slick-cell.wd-open-popup .circle-name {
		width: 30px; 
		height: 30px;
		line-height: 30px;
		display: inline-block;
		vertical-align: middle;
	}
	.slick-cell .wd-open-popup.comment-text{
		display: inline-block;
		vertical-align: middle;
		line-height: initial;
	}
	.slick-cell .wd-open-popup.comment-text .time{
		line-height: 13px;
		display: block;
		color: #666;
		font-size: 10px;
	}
	.slick-cell .wd-open-popup.comment-text .time i {
		margin-right: 4px;
	}
	.slick-cell.wd-open-popup .comment-text .comment {
		display: block;
		overflow: hidden;
		text-overflow: inherit;
		white-space: pre-wrap;
		line-height: initial;
	}
	.wd-table .slick-cell.grid-action .wd-actions a{
		vertical-align: middle;
		border-right: 0px solid transparent;
	}
	.slick-cell .gantt-ms {
		top: calc( 50% + 3px);
	}
	.slick-cell .gantt-line-s {
		top: calc( 50% - 6px);
	}
	.slick-cell .gantt-line-n {
		top: calc( 50% - 16px);
	}
	.slick-cell.wg_even .wd-widget-in-cell > div {
		vertical-align: middle;
		display: inline-block;
		width: 100%;
	}
</style>

<script type="text/javascript">
    HistoryFilter.auto = false;
    HistoryFilter.here = '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url = '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
	// HistoryFilter.auto = 0;
</script>
<?php
$isFilterFavorite = !empty($filter_render) && !empty($filter_render['filter_project_favorite']) && $filter_render['filter_project_favorite'] == 'yes' ? true :  false;
$isFilterAlert = !empty($filter_render) && !empty($filter_render['filter_alert']) && $filter_render['filter_alert'] == 'yes' ? true :  false;
$enableDashboard = !empty($companyConfigs['display_project_dashboard']) && $companyConfigs['display_project_dashboard'] != 0;
$isDisplayDashboard = !empty($filter_render) && !empty($filter_render['switch-dashboard']) && $filter_render['switch-dashboard'][0] == 1 ? true :  false;
unset($filter_render['switch-dashboard']);
$words = $this->requestAction('/translations/getByPage', array('pass' => array('KPI')));
$finan_title = array(
	'inv' => __d(sprintf($_domain, 'Budget_Investment'), "Budget Investment", null),
	'fon' => __d(sprintf($_domain, 'Budget_Operation'), "Budget Operation", null),
	'finaninv' =>  __d(sprintf($_domain, 'Finance_Investment'), "Finance Investment", null),
	'finanfon' => __d(sprintf($_domain, 'Finance_Operation'), "Finance Operation", null),
);
$wd_index = 0;

if( $has_upload ){
	echo $this->Html->script('dropzone.min');
}

$i = 1;
$dataView = array();
$selects = array(
    'Project.yn_1' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'Project.yn_2' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'Project.yn_3' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'Project.yn_4' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'Project.yn_5' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'Project.yn_6' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'Project.yn_7' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'Project.yn_8' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'Project.yn_9' => array('yes' => __('Yes', true), 'no' => __('No', true))
);
$projectManagers = array();
$eindex = 0;
$totalHeaders = array();
$notAdminPm =  $employee['Role']['name'] == 'conslt';

$exception = array(
	'Project.complexity_id',
	'Project.chief_business_id',
	'Project.technical_manager_id',
	'Project.read_access',
	'Project.functional_leader_id',
	'Project.uat_manager_id',
);
$gantts = $stones = array();
$ganttStart = $ganttEnd = 0;
foreach ($projects as $project) {
    $ProjectEmployeeManager = array();
    if(!empty($project['ProjectEmployeeManager'])) $ProjectEmployeeManager = $project['ProjectEmployeeManager'];
    $pEM = array();

    if(!empty($ProjectEmployeeManager)){
        foreach ($ProjectEmployeeManager as $key => $value) {
            $pEM[] = $value['project_manager_id'];
        }
    }
    $data = array(
        'id' => (int) $project['Project']['id'],
        'no.' => $i++,
        'DataSet' => array(
            'tm' => $project['Project']['technical_manager_id'],
            'cb' => $project['Project']['chief_business_id'],
            'pm' =>  $project['Project']['project_manager_id'],
            'pEM' => $pEM,
			'category' => $project['Project']['category'],
			'internal_progress' => array(),
			'external_progress' => array(),
			'finance_progress' => array(),
			'summary_task' => array(),
			'pc_forecast' => array(),
        )
    );
    $projectID = $project['Project']['id'];
	$data['DataSet']['internal_progress'] = !empty($dataset_internals[$projectID]) ? $dataset_internals[$projectID] : array();
	$data['DataSet']['external_progress'] = !empty($dataset_externals[$projectID]) ? $dataset_externals[$projectID] : array();
	$total_key = array_unique(array_merge( array_keys($data['DataSet']['internal_progress']), array_keys($data['DataSet']['external_progress'])));
	sort( $total_key);
	$data['DataSet']['syns_progress_key'] = $total_key;
	$data['DataSet']['finance_progress'] = !empty($finance_progress_column[$projectID]) ? $finance_progress_column[$projectID] : array();
	$data['DataSet']['summary_task'] = !empty($summary_tasks[$projectID]) ? $summary_tasks[$projectID] : array();
	$data['DataSet']['pc_forecast'] = !empty($pc_forecast[$projectID]) ? $pc_forecast[$projectID] : array();
    $activityID = $project['Project']['activity_id'];
    $category = $project['Project']['category'];
    $workload = !empty($sumWorload[$projectID]) ? $sumWorload[$projectID] : 0;
    $previous = !empty($sumPrevious[$projectID]) ? $sumPrevious[$projectID] : 0;
    $overload = !empty($sumOverload[$projectID]) ? $sumOverload[$projectID]: 0;
    $consumed = !empty($activityID) && !empty($sumActivities[$activityID]) ? $sumActivities[$activityID] : 0;
    $remainSPCs = isset($sumRemainSpecials[$projectID]) ? $sumRemainSpecials[$projectID] : 0;
    $remains = isset($sumRemains[$projectID]) ? $sumRemains[$projectID] : 0;
    $remains = $remains - $remainSPCs;
    $workload = $workload + $previous;
    $progress = 0;
    if(($workload + $overload) == 0){
        $progress = 0;
    } else {
        $com = round(($consumed*100)/($workload + $overload), 2);
        if($com > 100){
            $progress = 100;
        } else {
            $progress = $com;
        }
    }
    $mdForecasted = !empty($projectEvolutions[$projectID]) ? $projectEvolutions[$projectID] : 0;
    if(isset($project['ProjectAmr'])){
        $project['ProjectAmr'][0]['project_amr_progression'] = $progress;
        $project['ProjectAmr'][0]['md_validated'] = $consumed;
        $project['ProjectAmr'][0]['md_engaged'] = $remains;
        $project['ProjectAmr'][0]['md_forecasted'] = $mdForecasted;
        $project['ProjectAmr'][0]['md_variance'] = round($remains + $consumed - $mdForecasted, 2);
        $project['ProjectAmr'][0]['validated_currency_id'] = !empty($project['ProjectAmr'][0]['validated_currency_id']) ? $currency_name[$project['ProjectAmr'][0]['validated_currency_id']] : '';
        $project['ProjectAmr'][0]['engaged_currency_id'] = !empty($project['ProjectAmr'][0]['engaged_currency_id']) ? $currency_name[$project['ProjectAmr'][0]['engaged_currency_id']] : '';
        $project['ProjectAmr'][0]['forecasted_currency_id'] = !empty($project['ProjectAmr'][0]['forecasted_currency_id']) ? $currency_name[$project['ProjectAmr'][0]['forecasted_currency_id']] : '';
        $project['ProjectAmr'][0]['variance_currency_id'] = !empty($project['ProjectAmr'][0]['variance_currency_id']) ? $currency_name[$project['ProjectAmr'][0]['variance_currency_id']] : '';
        $phaseDelays = !empty($projectPhasePlans[$projectID]) ? $projectPhasePlans[$projectID] : array();
        $phasePlans = !empty($phaseDelays['MaxEndDatePlan']) && $phaseDelays['MaxEndDatePlan'] != '0000-00-00' ? strtotime($phaseDelays['MaxEndDatePlan']) : 0;
        $phaseReals = !empty($phaseDelays['MaxEndDateReal']) && $phaseDelays['MaxEndDateReal'] != '0000-00-00' ? strtotime($phaseDelays['MaxEndDateReal']) : 0;
        $project['ProjectAmr'][0]['delay'] = intval(($phaseReals - $phasePlans)/86400);
        $project['ProjectAmr'][0]['manual_consumed'] = isset($manualData[$projectID]) ? $manualData[$projectID] : 0;
    }
    $engagedErro = 0;
	if(!empty($activityID) && !empty($sumEmployees[$activityID])) {
        foreach ($sumEmployees[$activityID] as $id => $val) {
            $reals = !empty($employees[$id]['tjm']) ? (float)str_replace(',', '.', $employees[$id]['tjm']) : 1;
            $engagedErro += $val * $reals;
        }
    }
    $average = !empty($budgetSyns[$projectID]['internal_costs_average']) ? $budgetSyns[$projectID]['internal_costs_average'] : 0;
    $internalBudget = !empty($budgetSyns[$projectID]['internal_costs_budget']) ? $budgetSyns[$projectID]['internal_costs_budget'] : 0;
    $internalForecastManday = $consumed + $remains;
    $internalRemain = round($remains*$average, 2);
	if(!empty($allProjectBudgetSyns) && !empty($allProjectBudgetSyns[$projectID])){
		$internalForecast = $allProjectBudgetSyns[$projectID]['internal_costs_forecast'];
		$internalCostsVar = ($internalBudget != 0) ? $allProjectBudgetSyns[$projectID]['internal_costs_var'] . '%' : '-100%';
	}else{
		$internalForecast = round($engagedErro + ($remains*$average), 2);
		$internalCostsVar = ($internalBudget == 0) ? '-100%' : round(((($engagedErro + ($remains * $average))/$internalBudget)-1)*100, 2) . '%';
	}
    $externalBudget = !empty($budgetSyns[$projectID]['external_costs_budget']) ? $budgetSyns[$projectID]['external_costs_budget'] : 0;
    $externalForecast = !empty($budgetSyns[$projectID]['external_costs_forecast']) ? $budgetSyns[$projectID]['external_costs_forecast'] : 0;
    $externalOrdered = !empty($budgetSyns[$projectID]['external_costs_ordered']) ? $budgetSyns[$projectID]['external_costs_ordered'] : 0;
    $externalRemain = !empty($budgetSyns[$projectID]['external_costs_remain']) ? $budgetSyns[$projectID]['external_costs_remain'] : 0;
    $externalManday = !empty($budgetSyns[$projectID]['external_costs_man_day']) ? $budgetSyns[$projectID]['external_costs_man_day'] : 0;
    if($category == 2){
        $project['ProjectBudgetSyn'][0]['sales_to_bill'] = 0;
        $project['ProjectBudgetSyn'][0]['sales_billed'] = 0;
        $project['ProjectBudgetSyn'][0]['sales_paid'] = 0;
        $project['ProjectBudgetSyn'][0]['external_costs_forecast'] = 0;
        $project['ProjectBudgetSyn'][0]['external_costs_ordered'] = 0;
        $project['ProjectBudgetSyn'][0]['external_costs_remain'] = 0;
        $project['ProjectBudgetSyn'][0]['external_costs_var'] = '0%';
        $internalForecastManday = 0;
        $internalRemain = 0;
        $internalForecast = 0;
        $engagedErro = 0;
        $consumed = 0;
        $remain = 0;
        $externalForecast = 0;
        $externalOrdered = 0;
        $externalRemain = 0;
    }
    $project['ProjectBudgetSyn'][0]['workload'] = $workload;
    $project['ProjectBudgetSyn'][0]['overload'] = $overload;
    $project['ProjectBudgetSyn'][0]['internal_costs_engaged'] = $engagedErro;
    $project['ProjectBudgetSyn'][0]['internal_costs_engaged_md'] = $consumed;
    $project['ProjectBudgetSyn'][0]['internal_costs_forecasted_man_day'] = $consumed + $remains;
    // $project['ProjectBudgetSyn'][0]['internal_costs_remain'] = round($remains*$average, 2);
	// $project['ProjectBudgetSyn'][0]['internal_costs_forecast'] = round($engagedErro + ($remains*$average), 2);
	if(!empty($allProjectBudgetSyns) && !empty($allProjectBudgetSyns[$projectID])){
		$project['ProjectBudgetSyn'][0]['internal_costs_engaged'] = $allProjectBudgetSyns[$projectID]['internal_costs_engaged'];
		$project['ProjectBudgetSyn'][0]['internal_costs_forecasted_man_day'] = $allProjectBudgetSyns[$projectID]['internal_costs_forecasted_man_day'];
		$project['ProjectBudgetSyn'][0]['internal_costs_remain'] = $allProjectBudgetSyns[$projectID]['internal_costs_remain'];
		$project['ProjectBudgetSyn'][0]['internal_costs_forecast'] = $allProjectBudgetSyns[$projectID]['internal_costs_forecast'];
	}else{
		$project['ProjectBudgetSyn'][0]['internal_costs_engaged'] = $engagedErro;
		$project['ProjectBudgetSyn'][0]['internal_costs_forecasted_man_day'] = $consumed + $remains;
		$project['ProjectBudgetSyn'][0]['internal_costs_remain'] = round($remains*$average, 2);
		$project['ProjectBudgetSyn'][0]['internal_costs_forecast'] = round($engagedErro + ($remains*$average), 2);
	}
    $project['ProjectBudgetSyn'][0]['internal_costs_var'] = ($category == 2) ? '0%' : $internalCostsVar;
    $project['ProjectBudgetSyn'][0]['total_costs_budget'] = $internalBudget + $externalBudget;
    $project['ProjectBudgetSyn'][0]['total_costs_forecast'] = $internalForecast + $externalForecast;
    $project['ProjectBudgetSyn'][0]['total_costs_engaged'] = $engagedErro + $externalOrdered;
    $project['ProjectBudgetSyn'][0]['total_costs_remain'] = $internalRemain + $externalRemain;
    $project['ProjectBudgetSyn'][0]['total_costs_man_day'] = $internalForecastManday + $externalManday;
    $totalForecast = !empty($project['ProjectBudgetSyn'][0]['total_costs_forecast']) ? $project['ProjectBudgetSyn'][0]['total_costs_forecast'] : 0;
    $totalBudget = !empty($project['ProjectBudgetSyn'][0]['total_costs_budget']) ? $project['ProjectBudgetSyn'][0]['total_costs_budget'] : 0;
    $totalVar = ($totalBudget == 0) ? '-100%' : round((($totalForecast/$totalBudget) - 1)*100, 2).'%';
    $project['ProjectBudgetSyn'][0]['total_costs_var'] = ($category == 2) ? '0%' : $totalVar;
    $tWorkload = $workload + $overload;
    $assgnPc = !empty($staffingSystems['profit_center']) && !empty($staffingSystems['profit_center'][$projectID]) ? $staffingSystems['profit_center'][$projectID] : 0;
    $assgnEmploy = !empty($staffingSystems['employee']) && !empty($staffingSystems['employee'][$projectID]) ? $staffingSystems['employee'][$projectID] : 0;
    $project['ProjectBudgetSyn'][0]['assign_to_profit_center'] = ($tWorkload == 0) ? '0%' : ((round(($assgnPc/$tWorkload)*100, 2) > 100)? '100%' : round(($assgnPc/$tWorkload)*100, 2).'%');
    $project['ProjectBudgetSyn'][0]['assign_to_employee'] = ($tWorkload == 0) ? '0%' : ((round(($assgnEmploy/$tWorkload)*100, 2) > 100)? '100%' : round(($assgnEmploy/$tWorkload)*100, 2).'%');
    $totalExter = !empty($budgetSyns[$projectID]['external_costs_budget']) ? $budgetSyns[$projectID]['external_costs_budget'] : 0;
    $totalInter = !empty($internalBudgets[$projectID]) ? $internalBudgets[$projectID] : 0;
    $project['ProjectBudgetSyn'][0]['internal_costs_budget_man_day'] = $totalInter;
    $sold = !empty($budgetSyns[$projectID]['sales_sold']) ? $budgetSyns[$projectID]['sales_sold'] : 0;
    $totalIE = $totalExter + $totalInter;
    $totalB = $externalBudget+$internalBudget;
    $project['ProjectBudgetSyn'][0]['roi'] = ($totalB != 0) ? round((($sold-$totalB)/$totalB)*100, 2) : 0;
    $project['Project']['project_copy_id'] = isset($project['Project']['project_copy_id']) && isset($projectCopies[$project['Project']['project_copy_id']]) ? $projectCopies[ $project['Project']['project_copy_id'] ] : '';
    $project['Project']['freeze_by'] = isset($project['Project']['freeze_by']) && isset($freezers[$project['Project']['freeze_by']]) ? $freezers[ $project['Project']['freeze_by'] ] : '';
    if(isset($project['Project']['updated']))$project['Project']['updated'] = $project['Project']['last_modified'] ? $project['Project']['last_modified'] : $project['Project']['updated'];

    $project['ProjectBudgetSyn'][0]['workload_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_'.$currentYears]) ? $consumedAndWorkloadForActivities[$projectID]['workload_'.$currentYears]: 0;
    $project['ProjectBudgetSyn'][0]['workload_last_one_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_'.($currentYears-1)]) ? $consumedAndWorkloadForActivities[$projectID]['workload_'.($currentYears-1)]: 0;
    $project['ProjectBudgetSyn'][0]['workload_last_two_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_'.($currentYears-2)]) ? $consumedAndWorkloadForActivities[$projectID]['workload_'.($currentYears-2)]: 0;
    $project['ProjectBudgetSyn'][0]['workload_last_thr_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_'.($currentYears-3)]) ? $consumedAndWorkloadForActivities[$projectID]['workload_'.($currentYears-3)]: 0;
    $project['ProjectBudgetSyn'][0]['workload_next_one_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_'.($currentYears+1)]) ? $consumedAndWorkloadForActivities[$projectID]['workload_'.($currentYears+1)]: 0;
    $project['ProjectBudgetSyn'][0]['workload_next_two_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_'.($currentYears+2)]) ? $consumedAndWorkloadForActivities[$projectID]['workload_'.($currentYears+2)]: 0;
    $project['ProjectBudgetSyn'][0]['workload_next_thr_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_'.($currentYears+3)]) ? $consumedAndWorkloadForActivities[$projectID]['workload_'.($currentYears+3)]: 0;
    $project['ProjectBudgetSyn'][0]['consumed_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_'.$currentYears]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_'.$currentYears]: 0;
    $project['ProjectBudgetSyn'][0]['consumed_last_one_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_'.($currentYears-1)]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_'.($currentYears-1)]: 0;
    $project['ProjectBudgetSyn'][0]['consumed_last_two_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_'.($currentYears-2)]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_'.($currentYears-2)]: 0;
    $project['ProjectBudgetSyn'][0]['consumed_last_thr_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_'.($currentYears-3)]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_'.($currentYears-3)]: 0;
    $project['ProjectBudgetSyn'][0]['consumed_next_one_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_'.($currentYears+1)]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_'.($currentYears+1)]: 0;
    $project['ProjectBudgetSyn'][0]['consumed_next_two_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_'.($currentYears+2)]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_'.($currentYears+2)]: 0;
    $project['ProjectBudgetSyn'][0]['consumed_next_thr_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_'.($currentYears+3)]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_'.($currentYears+3)]: 0;
    $project['ProjectBudgetSyn'][0]['provisional_y'] = !empty($provisionals[$projectID]['provisional_'.$currentYears]) ? $provisionals[$projectID]['provisional_'.$currentYears]: 0;
    $project['ProjectBudgetSyn'][0]['provisional_last_one_y'] = !empty($provisionals[$projectID]['provisional_'.($currentYears-1)]) ? $provisionals[$projectID]['provisional_'.($currentYears-1)]: 0;
    $project['ProjectBudgetSyn'][0]['provisional_last_two_y'] = !empty($provisionals[$projectID]['provisional_'.($currentYears-2)]) ? $provisionals[$projectID]['provisional_'.($currentYears-2)]: 0;
    $project['ProjectBudgetSyn'][0]['provisional_last_thr_y'] = !empty($provisionals[$projectID]['provisional_'.($currentYears-3)]) ? $provisionals[$projectID]['provisional_'.($currentYears-3)]: 0;
    $project['ProjectBudgetSyn'][0]['provisional_next_one_y'] = !empty($provisionals[$projectID]['provisional_'.($currentYears+1)]) ? $provisionals[$projectID]['provisional_'.($currentYears+1)]: 0;
    $project['ProjectBudgetSyn'][0]['provisional_next_two_y'] = !empty($provisionals[$projectID]['provisional_'.($currentYears+2)]) ? $provisionals[$projectID]['provisional_'.($currentYears+2)]: 0;
    $project['ProjectBudgetSyn'][0]['provisional_next_thr_y'] = !empty($provisionals[$projectID]['provisional_'.($currentYears+3)]) ? $provisionals[$projectID]['provisional_'.($currentYears+3)]: 0;
    $project['ProjectBudgetSyn'][0]['provisional_budget_md'] = $totalInter + $externalManday;
    if(!empty($finances[$projectID])){
        $totalBudgetInv = $totalAvanInv = $totalBudgetFon = $totalAvanFon = 0;
        $totalBudgetFinanInv = $totalAvanFinanInv = $totalBudgetFinanFon = $totalAvanFinanFon = 0;
        $percentYearInvs = $percentYearFons = array();
        $percentYearFinanInvs = $percentYearFinanFons = array();
        foreach($finances[$projectID] as $key => $fins){
            $project['ProjectFinancePlus'][0][$key] = $fins;
            if(!empty($key)){
                $key = explode('_', $key);
                if(!empty($key[0]) && $key[0] == 'inv'){
                    if(!isset($percentYearInvs[$key[2]][$key[1]])){
                        $percentYearInvs[$key[2]][$key[1]] = 0;
                    }
                    $percentYearInvs[$key[2]][$key[1]] += $fins;
                    if(!empty($key[1]) && $key[1] == 'budget'){
                        $totalBudgetInv += $fins;
                    } else {
                        $totalAvanInv += $fins;
                    }
                } else if(!empty($key[0]) && $key[0] == 'fon'){
                    if(!isset($percentYearFons[$key[2]][$key[1]])){
                        $percentYearFons[$key[2]][$key[1]] = 0;
                    }
                    $percentYearFons[$key[2]][$key[1]] += $fins;
                    if(!empty($key[1]) && $key[1] == 'budget'){
                        $totalBudgetFon += $fins;
                    } else {
                        $totalAvanFon += $fins;
                    }
                } else if(!empty($key[0]) && $key[0] == 'finaninv'){
                    if(!isset($percentYearFinanInvs[$key[2]][$key[1]])){
                        $percentYearFinanInvs[$key[2]][$key[1]] = 0;
                    }
                    $percentYearFinanInvs[$key[2]][$key[1]] += $fins;
                    if(!empty($key[1]) && $key[1] == 'budget'){
                        $totalBudgetFinanInv += $fins;
                    } else {
                        $totalAvanFinanInv += $fins;
                    }
                } else if(!empty($key[0]) && $key[0] == 'finanfon'){
                    if(!isset($percentYearFinanFons[$key[2]][$key[1]])){
                        $percentYearFinanFons[$key[2]][$key[1]] = 0;
                    }
                    $percentYearFinanFons[$key[2]][$key[1]] += $fins;
                    if(!empty($key[1]) && $key[1] == 'budget'){
                        $totalBudgetFinanFon += $fins;
                    } else {
                        $totalAvanFinanFon += $fins;
                    }
                }
            }
        }
        if(!empty($percentYearInvs)){
            foreach($percentYearInvs as $year => $percentYearInv){
                $bud = !empty($percentYearInv['budget']) ? $percentYearInv['budget'] : 0;
                $ava = !empty($percentYearInv['avancement']) ? $percentYearInv['avancement'] : 0;
                $per = ($bud ==0 ) ? 0 : round($ava/$bud*100, 2);
                if($per > 100){
                    $per = 100;
                } elseif($per < 0) {
                    $per = 0;
                }
                $project['ProjectFinancePlus'][0]['inv_percent_'. $year] = $per;
            }
        }
        if(!empty($percentYearFons)){
            foreach($percentYearFons as $year => $percentYearFon){
                $bud = !empty($percentYearFon['budget']) ? $percentYearFon['budget'] : 0;
                $ava = !empty($percentYearFon['avancement']) ? $percentYearFon['avancement'] : 0;
                $per = ($bud ==0 ) ? 0 : round($ava/$bud*100, 2);
                if($per > 100){
                    $per = 100;
                } elseif($per < 0) {
                    $per = 0;
                }
                $project['ProjectFinancePlus'][0]['fon_percent_'. $year] = $per;
            }
        }
        if(!empty($percentYearFinanInvs)){
            foreach($percentYearFinanInvs as $year => $percentYearFinanInv){
                $bud = !empty($percentYearFinanInv['budget']) ? $percentYearFinanInv['budget'] : 0;
                $ava = !empty($percentYearFinanInv['avancement']) ? $percentYearFinanInv['avancement'] : 0;
                $per = ($bud ==0 ) ? 0 : round($ava/$bud*100, 2);
                if($per > 100){
                    $per = 100;
                } elseif($per < 0) {
                    $per = 0;
                }
                $project['ProjectFinancePlus'][0]['finaninv_percent_'. $year] = $per;
            }
        }
        if(!empty($percentYearFinanFons)){
            foreach($percentYearFinanFons as $year => $percentYearFinanFon){
                $bud = !empty($percentYearFinanFon['budget']) ? $percentYearFinanFon['budget'] : 0;
                $ava = !empty($percentYearFinanFon['avancement']) ? $percentYearFinanFon['avancement'] : 0;
                $per = ($bud ==0 ) ? 0 : round($ava/$bud*100, 2);
                if($per > 100){
                    $per = 100;
                } elseif($per < 0) {
                    $per = 0;
                }
                $project['ProjectFinancePlus'][0]['finanfon_percent_'. $year] = $per;
            }
        }
        $totalPercentInv = ($totalBudgetInv == 0) ? 0 : $totalAvanInv/$totalBudgetInv*100;
        if($totalPercentInv > 100){
            $totalPercentInv = 100;
        } elseif($totalPercentInv < 0) {
            $totalPercentInv = 0;
        }
        $totalPercentFon = ($totalBudgetFon == 0) ? 0 : $totalAvanFon/$totalBudgetFon*100;
        if($totalPercentFon > 100){
            $totalPercentFon = 100;
        } elseif($totalPercentFon < 0) {
            $totalPercentFon = 0;
        }
        $totalPercentFinanInv = ($totalBudgetFinanInv == 0) ? 0 : $totalAvanFinanInv/$totalBudgetFinanInv*100;
        if($totalPercentFinanInv > 100){
            $totalPercentFinanInv = 100;
        } elseif($totalPercentFinanInv < 0) {
            $totalPercentFinanInv = 0;
        }
        $totalPercentFinanFon = ($totalBudgetFinanFon == 0) ? 0 : $totalAvanFinanFon/$totalBudgetFinanFon*100;
        if($totalPercentFinanFon > 100){
            $totalPercentFinanFon = 100;
        } elseif($totalPercentFinanFon < 0) {
            $totalPercentFinanFon = 0;
        }
        $project['ProjectFinancePlus'][0]['inv_budget'] = !empty($totalBudgetInv) ? $totalBudgetInv : '';
        $project['ProjectFinancePlus'][0]['inv_avancement'] = !empty($totalAvanInv) ? $totalAvanInv : '';
        $project['ProjectFinancePlus'][0]['inv_percent'] = !empty($totalPercentInv) ? $totalPercentInv : '';
        $project['ProjectFinancePlus'][0]['finaninv_budget'] = !empty($totalBudgetFinanInv) ? $totalBudgetFinanInv : '';
        $project['ProjectFinancePlus'][0]['finaninv_avancement'] = !empty($totalAvanFinanInv) ? $totalAvanFinanInv : '';
        $project['ProjectFinancePlus'][0]['finaninv_percent'] = !empty($totalPercentFinanInv) ? $totalPercentFinanInv : '';
        $project['ProjectFinancePlus'][0]['fon_budget'] = !empty($totalBudgetFon) ? $totalBudgetFon : '';
        $project['ProjectFinancePlus'][0]['fon_avancement'] = !empty($totalAvanFon) ? $totalAvanFon : '';
        $project['ProjectFinancePlus'][0]['fon_percent'] = !empty($totalPercentFon) ? $totalPercentFon : '';
        $project['ProjectFinancePlus'][0]['finanfon_budget'] = !empty($totalBudgetFinanFon) ? $totalBudgetFinanFon : '';
        $project['ProjectFinancePlus'][0]['finanfon_avancement'] = !empty($totalAvanFinanFon) ? $totalAvanFinanFon : '';
        $project['ProjectFinancePlus'][0]['finanfon_percent'] = !empty($totalPercentFinanFon) ? $totalPercentFinanFon : '';
    }
    if(!empty($financesTwoPlus[$projectID])){
        foreach ($financesTwoPlus[$projectID] as $key => $value) {
            $project['ProjectFinanceTwoPlus'][0][$key] = $value;
        }
        $project['ProjectFinanceTwoPlus'][0]['last_estimated'] = !empty($project['ProjectFinanceTwoPlus'][0]['last_estimated']) ? $project['ProjectFinanceTwoPlus'][0]['last_estimated'] : 0;
        $project['ProjectFinanceTwoPlus'][0]['budget_revised'] = !empty($project['ProjectFinanceTwoPlus'][0]['budget_revised']) ? $project['ProjectFinanceTwoPlus'][0]['budget_revised'] : 0;
        // dr-de.
        $project['ProjectFinanceTwoPlus'][0]['dr_de'] = $project['ProjectFinanceTwoPlus'][0]['last_estimated'] - $project['ProjectFinanceTwoPlus'][0]['budget_revised'];
        $project['ProjectFinanceTwoPlus'][0]['percent'] = $project['ProjectFinanceTwoPlus'][0]['budget_revised'] != 0 ? $project['ProjectFinanceTwoPlus'][0]['last_estimated']/$project['ProjectFinanceTwoPlus'][0]['budget_revised']*100 : 0;
        $_startFinanceTwoPlus = $startFinanceTwoPlus;
        while ($_startFinanceTwoPlus <= $endFinanceTwoPlus) {
            $project['ProjectFinanceTwoPlus'][0]['last_estimated_' . $_startFinanceTwoPlus] = !empty($project['ProjectFinanceTwoPlus'][0]['last_estimated_' . $_startFinanceTwoPlus]) ? $project['ProjectFinanceTwoPlus'][0]['last_estimated_' . $_startFinanceTwoPlus] : 0;
            $project['ProjectFinanceTwoPlus'][0]['budget_revised_' . $_startFinanceTwoPlus] = !empty($project['ProjectFinanceTwoPlus'][0]['budget_revised_' . $_startFinanceTwoPlus]) ? $project['ProjectFinanceTwoPlus'][0]['budget_revised_' . $_startFinanceTwoPlus] : 0;
            // dr-de & dr/de.
            $project['ProjectFinanceTwoPlus'][0]['dr_de_' . $_startFinanceTwoPlus] = $project['ProjectFinanceTwoPlus'][0]['last_estimated_' . $_startFinanceTwoPlus] - $project['ProjectFinanceTwoPlus'][0]['budget_revised_' . $_startFinanceTwoPlus];
            $project['ProjectFinanceTwoPlus'][0]['percent_' . $_startFinanceTwoPlus] = $project['ProjectFinanceTwoPlus'][0]['budget_revised_' . $_startFinanceTwoPlus] != 0 ? $project['ProjectFinanceTwoPlus'][0]['last_estimated_' . $_startFinanceTwoPlus]/$project['ProjectFinanceTwoPlus'][0]['budget_revised_' . $_startFinanceTwoPlus]*100 : 0;
            $_startFinanceTwoPlus++;
        }
    }
	// debug( $fieldset); exit;
    foreach ($fieldset as $_fieldset) {
        if (is_array($_fieldset['path'])) {
            $_outputName = (string) current(Set::format(array($project), $_fieldset['path'][0], $_fieldset['path'][1]));
            if(in_array($_fieldset['key'], $columnSelected)){
                $_key = explode('.', $_fieldset['key']);
                $_output = !empty($project['Project'][$_key[1]]) ? $project['Project'][$_key[1]] : '';
            } else {
                $_output = (string) current(Set::format(array($project), $_fieldset['path'][0], $_fieldset['path'][1]));
            }
			 
        } else {
            $_outputName = $_output = (string) Set::classicExtract($project, $_fieldset['path']);
            if(in_array($_fieldset['key'], $columnSelected)){
                $_key = explode('.', $_fieldset['key']);
                $_path = explode('.', $_fieldset['path']);
                $_output = !empty($project[$_path[0]]['id']) ? $project[$_path[0]]['id'] : '';
            }
        }
        switch ($_fieldset['key']) {
            case 'Project.project_name': {
                    $_output = $project["Project"]["project_name"];
                    break;
                }
            // case 'Project.read_access': {
            //         $_output = $project["ProjectEmployeeManager"]["project_manager_id"];
            //         break;
            //     }
            case 'Project.freeze_time':
            case 'ProjectAmr.created':
            case 'Project.created':
            case 'Project.updated_opp_ip':
            case 'Project.updated_ip_arch':
                if( !$_output )$_output = '';
                    else $_output = date('d-m-Y', $_output);
                    break;
            case 'Project.last_modified':
				if( !$_output )$_output = '';
						else $_output = date('d-m-Y', $_output);
						break;
            case 'ProjectAmr.updated':
                    if( !$_output )$_output = '';
                    else $_output = date('Y-m-d H:i:s', $_output);
                    break;
            case 'Project.start_date':
            case 'Project.planed_end_date':
            case 'Project.end_date': {
                    $_output = $str_utility->convertToVNDate($_output);
                    break;
                }
            case 'ProjectAmr.weather':
            case 'ProjectAmr.rank':
            case 'ProjectAmr.cost_control_weather':
            case 'ProjectAmr.planning_weather':
            case 'ProjectAmr.risk_control_weather':
            case 'ProjectAmr.organization_weather':
            case 'ProjectAmr.perimeter_weather':
            case 'ProjectAmr.issue_control_weather':
            case 'ProjectAmr.customer_point_of_view':
			case 'ProjectAmr.budget_weather':
			case 'ProjectAmr.scope_weather':
			case 'ProjectAmr.schedule_weather':
			case 'ProjectAmr.resources_weather':
			case 'ProjectAmr.technical_weather': {
                    if( $_outputName ){
                        $data['DataSet'][$_fieldset['key']] = $_outputName;
						$selects[$_fieldset['key']][$_outputName] = $i18n[$_outputName];
                        $_output = $_outputName;
                    }
                    break;
                }
            case 'Project.project_amr_sub_program_id':
                    $data['DataSet'][$_fieldset['key']] = $project['ProjectAmrSubProgram']['id'];
                    $selects[$_fieldset['key']][$project['ProjectAmrSubProgram']['id']] = $_outputName;
                    break;
            case 'Project.budget_customer_id' : {
                    $data['DataSet'][$_fieldset['key']] = $project['BudgetCustomer']['id'];
                    $selects[$_fieldset['key']][$project['BudgetCustomer']['id']] = $_outputName;
                    break;
                }
            case 'Project.project_amr_program_id' : {
                    $data['DataSet'][$_fieldset['key']] = $project['ProjectAmrProgram']['id'];
                    $selects[$_fieldset['key']][$project['ProjectAmrProgram']['id']] = $_outputName;
                    break;
                }
            case 'Project.project_manager_id': {
                    $data['DataSet'][$_fieldset['key']] = $project['Project']['project_manager_id'];
					foreach($project['Project']['project_manager_id'] as $key => $id){
						$selects[$_fieldset['key']][$id] = $this->UserFile->employee_fullname($id);
					}
                    break;
                }
			case 'Project.chief_business_id':
			case 'Project.technical_manager_id':
			case 'Project.read_access':
            case 'Project.functional_leader_id':
            case 'Project.uat_manager_id': {
				
					// add value in js find "avaProjectManager: function"
					// Add selects for filter below
					$_list_resources = array(
						'Project.technical_manager_id' => $_technical_manager_list,
						'Project.chief_business_id' => $_chief_business_list,
						'Project.read_access' => $_read_access_manager_list,
						'Project.functional_leader_id' => $_functional_leader_list, 
						'Project.uat_manager_id' => $_uat_manager_list,
					);
					$data['DataSet'][$_fieldset['key']] = !empty ($_list_resources[$_fieldset['key']][$project['Project']['id']] ) ? $_list_resources[$_fieldset['key']][$project['Project']['id']] : array();
					
					$_output = !empty ($_list_resources[$_fieldset['key']][$project['Project']['id']] ) ? $_list_resources[$_fieldset['key']][$project['Project']['id']] : array();
                    break;
				}	
            case 'Project.project_type_id' : {
                    $data['DataSet'][$_fieldset['key']] = $project['ProjectType']['id'];
                    $selects[$_fieldset['key']][$project['ProjectType']['id']] = $_outputName;
                    break;
                }
            case 'Project.project_sub_type_id' : {
                    $data['DataSet'][$_fieldset['key']] = $project['ProjectSubType']['id'];
                    $selects[$_fieldset['key']][$project['ProjectSubType']['id']] = $_outputName;
                    break;
                }
            case 'Project.project_sub_sub_type_id' : {
                    $data['DataSet'][$_fieldset['key']] = $project['ProjectSubSubType']['id'];
                    $selects[$_fieldset['key']][$project['ProjectSubSubType']['id']] = $_outputName;
                    break;
                }
            case 'Project.project_phase_id' : {
                    $data[$_fieldset['key']] = !empty($project['ProjectPhaseCurrent']) ? (array) Set::classicExtract($project['ProjectPhaseCurrent'], '{n}.project_phase_id') : array();
                    break;
                }
            case 'Project.team' : {
                    $data[$_fieldset['key']] = !empty($project['Project']['team']) ? $project['Project']['team'] : '';
					$_output = !empty($listPc[$_outputName]) ? $_outputName : '';
                    break;
                }
            case 'Project.list_muti_1' :{
                    if(!empty($project['ProjectListMultiple'])){
                        foreach ($project['ProjectListMultiple'] as $key => $value) {
                            if($value['key'] == 'project_list_multi_1'){
                                $data[$_fieldset['key']][$key] = $value['project_dataset_id'];
                            }
                        }
                    }
                    $data[$_fieldset['key']] = !empty($data[$_fieldset['key']]) ? array_values($data[$_fieldset['key']]) : array();
                    break;
                }
            case 'Project.list_muti_2' :{
                    if(!empty($project['ProjectListMultiple'])){
                        foreach ($project['ProjectListMultiple'] as $key => $value) {
                            if($value['key'] == 'project_list_multi_2'){
                                $data[$_fieldset['key']][$key] = $value['project_dataset_id'];
                            }
                        }
                    }
                    $data[$_fieldset['key']] = !empty($data[$_fieldset['key']]) ? array_values($data[$_fieldset['key']]) : array();
                    break;
                }
            case 'Project.list_muti_3' :{
                    if(!empty($project['ProjectListMultiple'])){
                        foreach ($project['ProjectListMultiple'] as $key => $value) {
                            if($value['key'] == 'project_list_multi_3'){
                                $data[$_fieldset['key']][$key] = $value['project_dataset_id'];
                            }
                        }
                    }
                    $data[$_fieldset['key']] = !empty($data[$_fieldset['key']]) ? array_values($data[$_fieldset['key']]) : array();
                    break;
                }
            case 'Project.list_muti_4' :{
                    if(!empty($project['ProjectListMultiple'])){
                        foreach ($project['ProjectListMultiple'] as $key => $value) {
                            if($value['key'] == 'project_list_multi_4'){
                                $data[$_fieldset['key']][$key] = $value['project_dataset_id'];
                            }
                        }
                    }
                    $data[$_fieldset['key']] = !empty($data[$_fieldset['key']]) ? array_values($data[$_fieldset['key']]) : array();
                    break;
                }
            case 'Project.list_muti_5' :{
                    if(!empty($project['ProjectListMultiple'])){
                        foreach ($project['ProjectListMultiple'] as $key => $value) {
                            if($value['key'] == 'project_list_multi_5'){
                                $data[$_fieldset['key']][$key] = $value['project_dataset_id'];
                            }
                        }
                    }
                    $data[$_fieldset['key']] = !empty($data[$_fieldset['key']]) ? array_values($data[$_fieldset['key']]) : array();
                    break;
                }
            case 'Project.list_muti_6' :{
                    if(!empty($project['ProjectListMultiple'])){
                        foreach ($project['ProjectListMultiple'] as $key => $value) {
                            if($value['key'] == 'project_list_multi_6'){
                                $data[$_fieldset['key']][$key] = $value['project_dataset_id'];
                            }
                        }
                    }
                    $data[$_fieldset['key']] = !empty($data[$_fieldset['key']]) ? array_values($data[$_fieldset['key']]) : array();
                    break;
                }
            case 'Project.list_muti_7' :{
                    if(!empty($project['ProjectListMultiple'])){
                        foreach ($project['ProjectListMultiple'] as $key => $value) {
                            if($value['key'] == 'project_list_multi_7'){
                                $data[$_fieldset['key']][$key] = $value['project_dataset_id'];
                            }
                        }
                    }
                    $data[$_fieldset['key']] = !empty($data[$_fieldset['key']]) ? array_values($data[$_fieldset['key']]) : array();
                    break;
                }
            case 'Project.list_muti_8' :{
                    if(!empty($project['ProjectListMultiple'])){
                        foreach ($project['ProjectListMultiple'] as $key => $value) {
                            if($value['key'] == 'project_list_multi_8'){
                                $data[$_fieldset['key']][$key] = $value['project_dataset_id'];
                            }
                        }
                    }
                    $data[$_fieldset['key']] = !empty($data[$_fieldset['key']]) ? array_values($data[$_fieldset['key']]) : array();
                    break;
                }
            case 'Project.list_muti_9' :{
                    if(!empty($project['ProjectListMultiple'])){
                        foreach ($project['ProjectListMultiple'] as $key => $value) {
                            if($value['key'] == 'project_list_multi_9'){
                                $data[$_fieldset['key']][$key] = $value['project_dataset_id'];
                            }
                        }
                    }
                    $data[$_fieldset['key']] = !empty($data[$_fieldset['key']]) ? array_values($data[$_fieldset['key']]) : array();
                    break;
                }
            case 'Project.list_muti_10' : {
                    if(!empty($project['ProjectListMultiple'])){
                        foreach ($project['ProjectListMultiple'] as $key => $value) {
                            if($value['key'] == 'project_list_multi_10'){
                                $data[$_fieldset['key']][$key] = $value['project_dataset_id'];
                            }
                        }
                    }
                    $data[$_fieldset['key']] = !empty($data[$_fieldset['key']]) ? array_values($data[$_fieldset['key']]) : array();
                    break;
                }
            case 'Project.project_priority_id' : {
                    $data['DataSet'][$_fieldset['key']] = $project['ProjectPriority']['id'];
                    break;
                }
            case 'Project.project_status_id' : {
                    $data['DataSet'][$_fieldset['key']] = $project['ProjectStatus']['id'];
                    $selects[$_fieldset['key']][$project['ProjectStatus']['id']] = $_outputName;
                    break;
                }
            case 'ProjectAmr.project_amr_risk_information' : {
                    if(empty($_output)){
                        if(!empty($logGroups[$project['Project']['id']]) && !empty($logGroups[$project['Project']['id']]['ProjectRisk'])){
                            $_output = !empty($logGroups[$project['Project']['id']]['ProjectRisk']['description']) ? $logGroups[$project['Project']['id']]['ProjectRisk'] : '';
							$_output['current'] = time();
                        }
                    }
					if( empty( $first_commment_column) ) $first_commment_column = $_fieldset['key'];
                    break;
                }
            case 'ProjectAmr.project_amr_problem_information' : {
                    if(empty($_output)){
                        if(!empty($logGroups[$project['Project']['id']]) && !empty($logGroups[$project['Project']['id']]['ProjectIssue'])){
                            // $_output = !empty($logGroups[$project['Project']['id']]['ProjectIssue']['description']) ? $logGroups[$project['Project']['id']]['ProjectIssue']['description'] : '';
                            $_output = !empty($logGroups[$project['Project']['id']]['ProjectIssue']['description']) ? $logGroups[$project['Project']['id']]['ProjectIssue'] : '';
							$_output['current'] = time();
                        }
                    }
					if( empty( $first_commment_column) ) $first_commment_column = $_fieldset['key'];
                    break;
                }
            case 'ProjectAmr.project_amr_solution' : {
                    if(empty($_output)){
                        if(!empty($logGroups[$project['Project']['id']]) && !empty($logGroups[$project['Project']['id']]['ProjectAmr'])){
                            // $_output = !empty($logGroups[$project['Project']['id']]['ProjectAmr']['description']) ? $logGroups[$project['Project']['id']]['ProjectAmr']['description'] : '';
                            $_output = !empty($logGroups[$project['Project']['id']]['ProjectAmr']['description']) ? $logGroups[$project['Project']['id']]['ProjectAmr'] : '';
							$_output['current'] = time();
                        }
                    }
					if( empty( $first_commment_column) ) $first_commment_column = $_fieldset['key'];
                    break;
                }
            case 'ProjectAmr.done':
                if( isset($logs[ $project['Project']['id'] ]['Done']) ){
                    // $_output = $logs[ $project['Project']['id'] ]['Done']['description'];
                    $_output = $logs[ $project['Project']['id'] ]['Done'];$_output['current'] = time();
                }
				if( empty( $first_commment_column) ) $first_commment_column = $_fieldset['key'];
                break;
            case 'ProjectAmr.todo':
                if( isset($logs[ $project['Project']['id'] ]['ToDo']) ){
                    $_output = $logs[ $project['Project']['id'] ]['ToDo'];
					$_output['current'] = time();
                }
				if( empty( $first_commment_column) ) $first_commment_column = $_fieldset['key'];
                break;
            case 'ProjectAmr.comment':
                if( isset($logs[ $project['Project']['id'] ]['ProjectAmr']) ){
                    $_output = $logs[ $project['Project']['id'] ]['ProjectAmr'];
					$_output['current'] = time();
                }
				if( empty( $first_commment_column) ) $first_commment_column = $_fieldset['key'];
                break;
            case 'ProjectAmr.project_amr_budget_comment':
                if( isset($logs[ $project['Project']['id'] ]['Budget']) ){
                    $_output = $logs[ $project['Project']['id'] ]['Budget'];
					$_output['current'] = time();
                }
				if( empty( $first_commment_column) ) $first_commment_column = $_fieldset['key'];
                break;
            case 'ProjectAmr.project_amr_scope':
                if( isset($logs[ $project['Project']['id'] ]['Scope']) ){
                    $_output = $logs[ $project['Project']['id'] ]['Scope'];
					$_output['current'] = time();
                }
				if( empty( $first_commment_column) ) $first_commment_column = $_fieldset['key'];
                break;
            case 'ProjectAmr.project_amr_resource':
                if( isset($logs[ $project['Project']['id'] ]['Resources']) ){
                    $_output = $logs[ $project['Project']['id'] ]['Resources'];
					$_output['current'] = time();
                }
				if( empty( $first_commment_column) ) $first_commment_column = $_fieldset['key'];
                break;
            case 'ProjectAmr.project_amr_schedule':
                if( isset($logs[ $project['Project']['id'] ]['Schedule']) ){
                    $_output = $logs[ $project['Project']['id'] ]['Schedule'];
					$_output['current'] = time();
                }
				if( empty( $first_commment_column) ) $first_commment_column = $_fieldset['key'];
                break;
            case 'ProjectAmr.project_amr_technical':
                if( isset($logs[ $project['Project']['id'] ]['Technical']) ){
                    $_output = $logs[ $project['Project']['id'] ]['Technical'];
					$_output['current'] = time();
                }
				if( empty( $first_commment_column) ) $first_commment_column = $_fieldset['key'];
                break;
            case 'Project.list_1':
            case 'Project.list_2':
            case 'Project.list_3':
            case 'Project.list_4':
            case 'Project.list_5':
            case 'Project.list_6':
            case 'Project.list_7':
            case 'Project.list_8':
            case 'Project.list_9':
            case 'Project.list_10':
            case 'Project.list_11':
            case 'Project.list_12':
            case 'Project.list_13':
            case 'Project.list_14':
                $_key = explode('.', $_fieldset['key']);
                $_key = $_key[1];
                $_name = '';
                if( isset($datasets[$_key][$_outputName]) ){
                    $_name = $datasets[$_key][$_outputName];
                    $selects[$_fieldset['key']][$_outputName] = $_name;
                }
                $_output = ($_outputName != 0) ? $_outputName : '';
                break;
            case 'Project.bool_1':
            case 'Project.bool_2':
            case 'Project.bool_3':
            case 'Project.bool_4':
                $_output = !empty($_outputName) ? $_outputName : 'zero';
            break;
            case 'Project.date_1':
            case 'Project.date_2':
            case 'Project.date_3':
            case 'Project.date_4':
            case 'Project.date_5':
            case 'Project.date_6':
            case 'Project.date_7':
            case 'Project.date_8':
            case 'Project.date_9':
            case 'Project.date_10':
            case 'Project.date_11':
            case 'Project.date_12':
            case 'Project.date_13':
            case 'Project.date_14':
                $_output = $str_utility->convertToVNDate($_outputName);
                break;
            case 'Project.category':
                $_output = $_outputName;
				$selects[$_fieldset['key']][$_outputName] = $cates[$_outputName];
                break;
            case 'Project.yn_1':
            case 'Project.yn_2':
            case 'Project.yn_3':
            case 'Project.yn_4':
            case 'Project.yn_5':
            case 'Project.yn_6':
            case 'Project.yn_7':
            case 'Project.yn_8':
            case 'Project.yn_9':
			
                $_output = $_outputName ? 'yes' : 'no';
                break;
            // case 'Project.team':
                // $_output = !empty($listPc[$_outputName]) ? $_outputName : '';
                // break;
            case 'ProjectBudgetSyn.Workload€':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['Workload€'] : 0;
                break;
            case 'ProjectBudgetSyn.Consumed€':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['Consumed€'] : 0;
                break;
            case 'ProjectBudgetSyn.Remain€':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['Remain€'] : 0;
                break;
            case 'ProjectBudgetSyn.Amount€':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['Amount€'] : 0;
                break;
            case 'ProjectBudgetSyn.Estimated€':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['Estimated€'] : 0;
                break;
            case 'ProjectBudgetSyn.Consumed':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['Consumed'] : 0;
                break;
            case 'ProjectBudgetSyn.Remain':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['Remain'] : 0;
                break;
            case 'ProjectBudgetSyn.Workload':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['Workload'] : 0;
                break;
            case 'ProjectBudgetSyn.Initialworkload':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['Initialworkload'] : 0;
                break;
            case 'ProjectBudgetSyn.UnitPrice':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['UnitPrice'] : 0;
                break;
            case 'ProjectBudgetSyn.Overload':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['Overload'] : 0;
                break;
            case 'ProjectBudgetSyn.ManualConsumed':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['ManualConsumed'] : 0;
                break;
            case 'ProjectBudgetSyn.InUsed':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['InUsed'] : 0;
                break;
            case 'ProjectBudgetSyn.Completed':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['Completed'] : 0;
                break;
            case 'ProjectBudgetSyn.%progressorder':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['%progressorder'] : 0;
                break;
            case 'ProjectBudgetSyn.%progressorder€':
                $_output = !empty($TaskEuros[$projectID]) ? $TaskEuros[$projectID]['%progressorder€'] : 0;
                break;
            case 'Project.next_milestone_in_day':
                $_output = $listNextMilestoneByDay[$projectID];
                break;
            case 'Project.next_milestone_in_week':
                $_output = $listNextMilestoneByWeek[$projectID];
                break;
            case 'ProjectBudgetSyn.purchases_sold':
                $_output = !empty($Purchase[$projectID]) && !empty($Purchase[$projectID]['purchases_sold']) ? $Purchase[$projectID]['purchases_sold'] : 0;
                break;
            case 'ProjectBudgetSyn.purchases_to_bill':
                $_output = !empty($Purchase[$projectID]) && !empty($Purchase[$projectID]['purchases_to_bill']) ? $Purchase[$projectID]['purchases_to_bill'] : 0;
                break;
            case 'ProjectBudgetSyn.purchases_billed':
                $_output = !empty($Purchase[$projectID]) && !empty($Purchase[$projectID]['purchases_billed']) ? $Purchase[$projectID]['purchases_billed'] : 0;
                break;
            case 'ProjectBudgetSyn.purchases_paid':
                $_output = !empty($Purchase[$projectID]) && !empty($Purchase[$projectID]['purchases_paid']) ? $Purchase[$projectID]['purchases_paid'] : 0;
                break;
            case 'Project.upload_documents_1':
			case 'Project.upload_documents_2':
			case 'Project.upload_documents_3':
			case 'Project.upload_documents_4':
			case 'Project.upload_documents_5':
                $_output = !empty($project['ProjectFile'][str_replace('Project.','',$_fieldset['key'])]) ? array_values( $project['ProjectFile'][str_replace('Project.','',$_fieldset['key'])] ) : array();
                break;
			case 'ProjectWidget.Project_progress':
				$_output = !empty($projectProgress[$projectID]['Completed']) ? $projectProgress[$projectID]['Completed'] : 0;
				$_output .='%';
                break;
			
				//$projectID
			case 'ProjectWidget.Phase_real':
				$data['DataSet']['Phase_real']['date'] = 0;
				$_output = '';
				if( !empty($projectPhasePlans[$projectID]['max_end_date_real'])){
					$_output = $projectPhasePlans[$projectID]['max_end_date_real'];
					$data['DataSet']['Phase_real']['date'] = strtotime($projectPhasePlans[$projectID]['MaxEndDateReal']);
				}
                break;
			case 'ProjectWidget.Phase_plan':
				$data['DataSet']['Phase_plan'] = array(
					'date' => 0,
					'diff' => 0
				);	
				$_output = '';
				if( !empty($projectPhasePlans[$projectID]['max_end_date_plan'])){
					$_output = $projectPhasePlans[$projectID]['max_end_date_plan'];
					$data['DataSet']['Phase_plan']['date'] = strtotime($projectPhasePlans[$projectID]['MaxEndDatePlan']);
					$diff = '';
					if( isset($projectPhasePlans[$projectID]['diff'])){
						$diff = intval($projectPhasePlans[$projectID]['diff']);
					}
					$data['DataSet']['Phase_plan']['diff'] = $diff;
				}
				break;
			case 'ProjectWidget.Phase_progress':
				$_output = !empty($projectProgress[$projectID]['Completed']) ? $projectProgress[$projectID]['Completed'] : 0;
				$_output .='%';
				// $data['DataSet']['Progress'] = !empty($projectProgress) ? $projectProgress[$projectID]['Completed'] : 0;
                break;
			case 'ProjectWidget.Phase_diff':
				$_output = isset($projectPhasePlans[$projectID]['diff']) ? $projectPhasePlans[$projectID]['diff'].__('d', true) : '';
				// $_output .= __('d', true);
                break;
			case 'ProjectWidget.Milestone_late':
				$data['DataSet']['Milestone_late'] = array(
					'value' => array(),
					'date' => '',
				);
				$_output = '';
				if( !empty($milestoneWidgetData[$projectID]['milestone_late'])){
					$late = $milestoneWidgetData[$projectID]['milestone_late'];
					$_output = $late['date'] .'-'. $late['project_milestone'];
					$data['DataSet']['Milestone_late']= array(
						'value' => array( 
							'text' =>  $late['project_milestone'],
							'date' => $late['date'], 
						),
						'date' => strtotime($late['date'])
					);
				}
				break;
			case 'ProjectWidget.Milestone_next':
				$data['DataSet']['Milestone_next'] =  array(
					'value' => '',
					'date' => '',
				);
				$_output = '';
				if( !empty($milestoneWidgetData[$projectID]['next_milestone'])){
					$next = $milestoneWidgetData[$projectID]['next_milestone'];
					$_output = $next['date'] .'-'. $next['project_milestone'];
					$data['DataSet']['Milestone_next']= array(
						'value' => array( 
							'text' =>  $next['project_milestone'],
							'date' => $next['date'], 
						),
						'date' => strtotime($next['date'])
					);
				}
				break;
			case 'ProjectWidget.FinancePlus_inv_budget':
			case 'ProjectWidget.FinancePlus_inv_engaged':
			case 'ProjectWidget.FinancePlus_fon_budget':
			case 'ProjectWidget.FinancePlus_fon_engaged':
			case 'ProjectWidget.FinancePlus_finaninv_budget':
			case 'ProjectWidget.FinancePlus_finaninv_engaged':
			case 'ProjectWidget.FinancePlus_finanfon_budget':
			case 'ProjectWidget.FinancePlus_finanfon_engaged':
				$_output = 0;
				$finan_type = array('inv', 'fon','finaninv', 'finanfon');
				foreach($finan_type as $key => $type){
					if($_fieldset['key'] == 'ProjectWidget.FinancePlus_'. $type .'_engaged'){
						if( !empty($financePlusWidgetData[$projectID][$type . '_avancement'])){
							$_output = $financePlusWidgetData[$projectID][$type . '_avancement'];
						}
					}
					if($_fieldset['key'] == 'ProjectWidget.FinancePlus_'. $type .'_budget'){
						if( !empty($financePlusWidgetData[$projectID][$type . '_budget'])){
							$_output = $financePlusWidgetData[$projectID][$type . '_budget'];
						}
					}
				}
				break;
			case 'ProjectWidget.FinancePlus_inv_percent':
			case 'ProjectWidget.FinancePlus_fon_percent':
			case 'ProjectWidget.FinancePlus_finaninv_percent':
			case 'ProjectWidget.FinancePlus_finanfon_percent':
				$_output = '0%';
				$finan_type = array('inv', 'fon','finaninv', 'finanfon');
				foreach($finan_type as $key => $type){
					if($_fieldset['key'] == 'ProjectWidget.FinancePlus_'. $type .'_percent'){
						if( !empty($financePlusWidgetData[$projectID][$type .'_progress'])){
							$_output = $financePlusWidgetData[$projectID][$type .'_progress']. '%';
						}
					}
				}
				break;
			case 'ProjectWidget.Synthesis_budget':
				$_output = 0;
				if( !empty( $allProjectBudgetSyns[$projectID])){
					$budget_syns = $allProjectBudgetSyns[$projectID];
					$internal_costs_budget = (float)$budget_syns['internal_costs_budget'];
					$external_costs_budget = (float)$budget_syns['external_costs_budget'];
					$_output =  ((float)$internal_costs_budget + (float)$external_costs_budget);
				}
				break;
			case 'ProjectWidget.Synthesis_forecast':
				$_output = 0;
				if( !empty( $allProjectBudgetSyns[$projectID])){
					$budget_syns = $allProjectBudgetSyns[$projectID];
					$internal_costs_forecast = (float)$budget_syns['internal_costs_forecast'];
					$external_costs_forecast = (float)$budget_syns['external_costs_forecast'];
					$_output = ((float)$internal_costs_forecast + (float)$external_costs_forecast);
				}
				break;
			case 'ProjectWidget.Synthesis_percent':
				$_output = '100%';
				if( !empty( $allProjectBudgetSyns[$projectID])){
					$budget_syns = $allProjectBudgetSyns[$projectID];
					$internal_costs_budget = (float)$budget_syns['internal_costs_budget'];
					$internal_costs_forecast = (float)$budget_syns['internal_costs_forecast'];
					$external_costs_budget = (float)$budget_syns['external_costs_budget'];
					$external_costs_forecast = (float)$budget_syns['external_costs_forecast'];
					$totalBudgetProgress = (float)$internal_costs_budget + (float)$external_costs_budget;
					$totalForecastProgress = (float)$internal_costs_forecast + (float)$external_costs_forecast;
					if($totalBudgetProgress == 0) {
						$per_budget_syns = 100;
					} else {
						$per_budget_syns = round($totalForecastProgress/$totalBudgetProgress * 100);
					} 
					$_output = $per_budget_syns . '%';
				}
                break;
			case 'ProjectWidget.Internal_budget_md':
				$_output = 0;
				if( !empty( $allProjectBudgetSyns[$projectID])){
					$budget_syns = $allProjectBudgetSyns[$projectID];
					$_output = (float)$budget_syns['internal_costs_budget_man_day'];
				}
				break;
			case 'ProjectWidget.Internal_forecast_md':
				$_output = 0;
				if( !empty( $allProjectBudgetSyns[$projectID])){
					$budget_syns = $allProjectBudgetSyns[$projectID];
					$_output = (float)$budget_syns['internal_costs_forecasted_man_day'];
				}
				break;
			case 'ProjectWidget.Internal_percent_forecast_md':
				$_output = 0;
				if( !empty( $allProjectBudgetSyns[$projectID])){
					$budget_syns = $allProjectBudgetSyns[$projectID];
					$internal_costs_budget_man_day = (float)$budget_syns['internal_costs_budget_man_day'];
					$internal_costs_forecasted_man_day = (float)$budget_syns['internal_costs_forecasted_man_day'];
					if($internal_costs_budget_man_day == 0) {
						$_output = $internal_costs_forecasted_man_day > 0 ? 100 : 0;
					} else {
						$_output = round($internal_costs_forecasted_man_day/$internal_costs_budget_man_day * 100);
					}
				}
				$_output = $_output . '%';
				break;
				
			case 'ProjectWidget.Internal_consumed_md':
				$pid = $project['Project']['id'];
				if( $useManualConsumed ){
					$_output = !empty($dataActivityTaskManual[$pid]['consumed']) ? $dataActivityTaskManual[$pid]['consumed'] : 0;
				}else{
					$_output = $consumed;
				}
				break;
			case 'ProjectWidget.Internal_percent_consumed_md':
				$_output = 0;
				if( !empty( $allProjectBudgetSyns[$projectID])){
					$budget_syns = $allProjectBudgetSyns[$projectID];
					$internal_costs_forecasted_man_day = (float)$budget_syns['internal_costs_forecasted_man_day'];
					if($internal_costs_forecasted_man_day == 0) {
						$_output = $consumed > 0 ? 100 : 0;
					} else {
						$_output = round($consumed/$internal_costs_forecasted_man_day * 100);
					}
				}else{
					if($consumed > 0) $_output = 100;
				}
				$_output = $_output . '%';
				break;
			case 'ProjectWidget.Internal_budget_euro':
				$_output = 0;
				if( !empty( $allProjectBudgetSyns[$projectID])){
					$budget_syns = $allProjectBudgetSyns[$projectID];
					$_output = (float)$budget_syns['internal_costs_budget'];
				}
				break;
			case 'ProjectWidget.Internal_forecast_euro':
				$_output = 0;
				if( !empty( $allProjectBudgetSyns[$projectID])){
					$budget_syns = $allProjectBudgetSyns[$projectID];
					$_output = (float)$budget_syns['internal_costs_forecast'];
				}
				break;
			case 'ProjectWidget.Internal_percent_forecast_euro':
				$_output = 0;
				if( !empty( $allProjectBudgetSyns[$projectID])){
					$budget_syns = $allProjectBudgetSyns[$projectID];
					$internal_costs_budget = (float)$budget_syns['internal_costs_budget'];
					$internal_costs_forecast = (float)$budget_syns['internal_costs_forecast'];
					if($internal_costs_budget == 0) {
						if($internal_costs_forecast > 0) $_output = 100;
					} else {
						$_output = round($internal_costs_forecast/$internal_costs_budget * 100);
					}
				}
				$_output = $_output . '%';
				break;
				
			case 'ProjectWidget.Internal_engaged_euro':
				$_output = 0;
				if( !empty( $allProjectBudgetSyns[$projectID])){
					$budget_syns = $allProjectBudgetSyns[$projectID];
					$_output = (float)$budget_syns['internal_costs_engaged'];
				}
				break;
			case 'ProjectWidget.Internal_percent_consumed_euro':
				$_output = 0;
				if( !empty( $allProjectBudgetSyns[$projectID])){
					$budget_syns = $allProjectBudgetSyns[$projectID];
					$internal_costs_engaged = (float)$budget_syns['internal_costs_engaged'];
					$internal_costs_forecast = (float)$budget_syns['internal_costs_forecast'];
					if($internal_costs_forecast == 0) {
						if($internal_costs_engaged > 0) $_output = 100;
					} else {
						$_output = round($internal_costs_engaged/$internal_costs_forecast * 100);
					}
				}else{
					if($consumed > 0) $_output = 100;
				}
				$_output = $_output . '%';
				break;
			case 'ProjectWidget.External_budget_erro':
				$_output = 0;
				if( !empty( $allProjectBudgetSyns[$projectID])){
					$budget_syns = $allProjectBudgetSyns[$projectID];
					$_output = (float)$budget_syns['external_costs_budget'];
				}
				break;
			case 'ProjectWidget.External_forecast_erro':
				$_output = 0;
				if( !empty( $allProjectBudgetSyns[$projectID])){
					$budget_syns = $allProjectBudgetSyns[$projectID];
					$_output = (float)$budget_syns['external_costs_forecast'];
				}
				break;
			case 'ProjectWidget.External_percent_forecast_erro':
				$_output = 0;
				if( !empty( $allProjectBudgetSyns[$projectID])){
					$budget_syns = $allProjectBudgetSyns[$projectID];
					$external_costs_forecast = (float)$budget_syns['external_costs_forecast'];
					$external_costs_budget = (float)$budget_syns['external_costs_budget'];
					if($external_costs_budget == 0) {
						if($external_costs_forecast > 0) $_output = 100;
					} else {
						$_output = round($external_costs_forecast/$external_costs_budget * 100);
					}
				}
				$_output = $_output . '%';
				break;
				
			case 'ProjectWidget.External_ordered_erro':
				$_output = 0;
				if( !empty( $allProjectBudgetSyns[$projectID])){
					$budget_syns = $allProjectBudgetSyns[$projectID];
					$_output = (float)$budget_syns['external_costs_ordered'];
				}
				break;
			case 'ProjectWidget.External_percent_ordered_erro':
				$_output = 0;
				if( !empty( $allProjectBudgetSyns[$projectID])){
					$budget_syns = $allProjectBudgetSyns[$projectID];
					$external_costs_ordered = (float)$budget_syns['external_costs_ordered'];
					$external_costs_forecast = (float)$budget_syns['external_costs_forecast'];
					if($external_costs_forecast == 0) {
						$_output = 100;
					} else {
						$_output = round($external_costs_ordered / $external_costs_forecast * 100);
					}
				}
				$_output = $_output . '%';
				break;
				
			
        }
        if( !in_array($_fieldset['key'], $exception) ){
            if (is_numeric($_output)) {
                if (strpos($_output, '.')) {
                    $_output = floatval($_output);
                } else {
                    $_output = (int)$_output;
                }
            }elseif (!is_array($_output) && preg_match("/^(-){0,5}( ){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/i", (string)$_output) == 1) {
                $_output = str_replace(' ', '', $_output);
                $_output = str_replace(',', '', $_output);
                $_output = floatval($_output);
            }
        }
        if(in_array($_fieldset['key'], $columnCalculationConsumeds)){
            $val = $_output ? $_output : 0;
            if(!isset($totalHeaders[$_fieldset['key']])){
                $totalHeaders[$_fieldset['key']] = 0;
            }
            $totalHeaders[$_fieldset['key']] += $val;
        }
        if($_fieldset['key'] == 'ProjectWidget.External_var_erro'){
            $val = $_output ? $_output : 0;
            if(!isset($totalHeaders[$_fieldset['key']])){
                $totalHeaders[$_fieldset['key']] = 0;
            }
            $totalHeaders[$_fieldset['key']] += $val;
        }
		// if( $_fieldset['key'] == 'Project.read_access') debug( $data);
        $notValueFromProjectTable = array('Project.project_phase_id', 'Project.list_muti_1', 'Project.list_muti_2', 'Project.list_muti_3', 'Project.list_muti_4', 'Project.list_muti_5', 'Project.list_muti_6', 'Project.list_muti_7', 'Project.list_muti_8', 'Project.list_muti_9', 'Project.list_muti_10');
        if(!in_array($_fieldset['key'], $notValueFromProjectTable)){
            $data[$_fieldset['key']] = $_output;
        }
		// if( $_fieldset['key'] == 'Project.read_access'){  debug($data); exit;}
    }
	
	$finan_type = array('inv', 'fon','finaninv', 'finanfon');
	foreach($finan_type as $key => $type){
		if( empty( $totalHeaders['ProjectWidget.FinancePlus_'.$type.'_budget'] )){
			$totalHeaders['ProjectWidget.FinancePlus_'.$type.'_percent'] = !empty($totalHeaders['ProjectWidget.FinancePlus_'.$type.'_engaged']) ? 100 : 0;
		}else {
			$totalHeaders['ProjectWidget.FinancePlus_'.$type.'_percent']  = 100 * $totalHeaders['ProjectWidget.FinancePlus_'.$type.'_engaged']  / $totalHeaders['ProjectWidget.FinancePlus_'.$type.'_budget'];
		}
	}
	if( empty( $totalHeaders['ProjectWidget.Synthesis_budget'] )) $totalHeaders['ProjectWidget.Synthesis_percent'] = 100;
	else $totalHeaders['ProjectWidget.Synthesis_percent']  = 100 * $totalHeaders['ProjectWidget.Synthesis_forecast']  / $totalHeaders['ProjectWidget.Synthesis_budget'];
	
	// Internal MD
	if( empty( $totalHeaders['ProjectWidget.Internal_budget_md'] )){
		$totalHeaders['ProjectWidget.Internal_percent_forecast_md'] = !empty($totalHeaders['ProjectWidget.Internal_forecast_md']) ? 100 : 0;
	}else{
		$totalHeaders['ProjectWidget.Internal_percent_forecast_md']  = 100 * $totalHeaders['ProjectWidget.Internal_forecast_md']  / $totalHeaders['ProjectWidget.Internal_budget_md'];
	}
	
	if( empty( $totalHeaders['ProjectWidget.Internal_forecast_md'] )){
		$totalHeaders['ProjectWidget.Internal_percent_consumed_md'] = !empty($totalHeaders['ProjectWidget.Internal_consumed_md'])? 100 : 0;
	}else{
		$totalHeaders['ProjectWidget.Internal_percent_consumed_md']  = 100 * $totalHeaders['ProjectWidget.Internal_consumed_md']  / $totalHeaders['ProjectWidget.Internal_forecast_md'];
	}
	
	// Internal costs 
	if( empty( $totalHeaders['ProjectWidget.Internal_budget_euro'] )){
		$totalHeaders['ProjectWidget.Internal_percent_forecast_euro'] = !empty($totalHeaders['ProjectWidget.Internal_forecast_euro'])? 100 : 0;
	}else{
		$totalHeaders['ProjectWidget.Internal_percent_forecast_euro']  = 100 * $totalHeaders['ProjectWidget.Internal_forecast_euro']  / $totalHeaders['ProjectWidget.Internal_budget_euro'];
	}

	if( empty( $totalHeaders['ProjectWidget.Internal_forecast_euro'] )){
		$totalHeaders['ProjectWidget.Internal_percent_consumed_euro'] = !empty($totalHeaders['ProjectWidget.Internal_engaged_euro'])? 100 : 0;
	}else{
		$totalHeaders['ProjectWidget.Internal_percent_consumed_euro']  = 100 * $totalHeaders['ProjectWidget.Internal_engaged_euro']  / $totalHeaders['ProjectWidget.Internal_forecast_euro'];
	}
	// External
	if( empty( $totalHeaders['ProjectWidget.External_budget_erro'] )){
		$totalHeaders['ProjectWidget.External_percent_forecast_erro'] = !empty($totalHeaders['ProjectWidget.External_forecast_erro']) ? 100 : 0;
	}else{
		$totalHeaders['ProjectWidget.External_percent_forecast_erro']  = 100 * $totalHeaders['ProjectWidget.External_forecast_erro']  / $totalHeaders['ProjectWidget.External_budget_erro'];
	}

	if( empty( $totalHeaders['ProjectWidget.External_forecast_erro'] )){
		$totalHeaders['ProjectWidget.External_percent_ordered_erro'] = !empty($totalHeaders['ProjectWidget.External_ordered_erro']) ? 100 : 0;
	}else{
		$totalHeaders['ProjectWidget.External_percent_ordered_erro']  = 100 * $totalHeaders['ProjectWidget.External_ordered_erro']  / $totalHeaders['ProjectWidget.External_forecast_erro'];
	}
	$data['used'] = !empty($project_used[$project['Project']['id']]) ? 1 : 0;
	$data['action.'] = '';
    if($viewGantt){
        /**
         * Add milestones
         */
        if(!empty($project['ProjectMilestone'])){
            foreach ($project['ProjectMilestone'] as $p) {
                $_start = strtotime($p['milestone_date']);
                if (!$ganttStart || $_start < $ganttStart) {
                    $ganttStart = $_start;
                } elseif (!$ganttEnd || $_start > $ganttEnd) {
                    $ganttEnd = $_start;
                }
                $stones[$project['Project']['id']][date('Y', $_start)][] = array(date('d-m-Y', $_start), $p['project_milestone'], $p['validated']);
            }
        }
        if (!empty($project['ProjectPhasePlan'])) {
            $_phase['start'] = $_phase['end'] = $_phase['rstart'] = $_phase['rend'] = 0;
            foreach ($project['ProjectPhasePlan'] as $phace) {
                /**
                 * Set start, end, real start, real end.
                 */
                if(isset($_phase['start']) && !empty($_phase['start']) && $_phase['start'] != 0){
                    $date = $this->Gantt->toTime($phace['phase_planed_start_date']);
                    if(($date <= $_phase['start']) && $date != 0){
                        $_phase['start'] = $date;
                    }
                } else {
                    $_phase['start'] = $this->Gantt->toTime($phace['phase_planed_start_date']);
                }
                if(isset($_phase['end']) && !empty($_phase['end']) && $_phase['end'] != 0){
                    $date = $this->Gantt->toTime($phace['phase_planed_end_date']);
                    if($date >= $_phase['end']){
                        $_phase['end'] = $date;
                    }
                } else {
                    $_phase['end'] = $this->Gantt->toTime($phace['phase_planed_end_date']);
                }
                if(isset($_phase['rstart']) && !empty($_phase['rstart']) && $_phase['rstart'] != 0){
                    $date = $this->Gantt->toTime($phace['phase_real_start_date']);
                    if(($date <= $_phase['rstart']) && $date != 0){
                        $_phase['rstart'] = $date;
                    }
                } else {
                    $_phase['rstart'] = $this->Gantt->toTime($phace['phase_real_start_date']);
                }
                if(isset($_phase['rend']) && !empty($_phase['rend']) && $_phase['rend'] != 0){
                    $date = $this->Gantt->toTime($phace['phase_real_end_date']);
                    if($date >= $_phase['rend']){
                        $_phase['rend'] = $date;
                    }
                } else {
                    $_phase['rend'] = $this->Gantt->toTime($phace['phase_real_end_date']);
                }
                $_phase['id'] = $phace['id'];
                $_phase['name'] = !empty($phace['ProjectPhase']['name']) ? $phace['ProjectPhase']['name'] : '';
                $_phase['color'] = !empty($phace['ProjectPhase']['color']) ? $phace['ProjectPhase']['color'] : '#004380';
                if ($_phase['rstart'] > 0) {
                    $_start = min($_phase['start'], $_phase['rstart']);
                } else {
                    $_start = $_phase['start'];
                }
                if (!$ganttStart || ($_start > 0 && $_start < $ganttStart)) {
                    $ganttStart = $_start;
                }
                $_end = max($_phase['end'], $_phase['rend']);
                if (!$ganttEnd || $_end > $ganttEnd) {
                    $ganttEnd = $_end;
                }
                $_gantt['phase'][0] = $_phase;
            }
            // $completed = 0;
            $comPlan = '';
			$completed = !empty($projectProgress[$projectID]['Completed']) ? $projectProgress[$projectID]['Completed'] : 0;
            if(!empty($phases) && !empty($phases[$projectID])){ //&& !empty($phases[$_pId][$node['id']])){
                $ds = $phases[$projectID];
                $workload = !empty($ds['workload']) ? $ds['workload'] : 0;
                $consumed = !empty($ds['consumed']) ? $ds['consumed'] : 0;
                // if($workload == 0){
                    // $completed = 0;
                // } else{
                    // $completed = round((($consumed*100)/$workload), 2);
                // }
                if($_phase['start'] != 0 && $_phase['end'] != 0){
                    $datediff = $_phase['end'] - $_phase['start'];
                    $datediff = floor($datediff/(60*60*24));
                    $comPlan = round(($datediff*$completed)/100, 0);
                    $comPlan = strtotime("+$comPlan days", $_phase['start']);
                }
                if($_phase['rstart'] != 0 && $_phase['rend'] != 0){
                    $datediff = $_phase['rend'] - $_phase['rstart'];
                    $datediff = floor($datediff/(60*60*24));
                    $comReal = round(($datediff*$completed)/100, 0);
                    $comReal = strtotime("+$comReal days", $_phase['rstart']);
                }
            }
			
            $gantts[$projectID] = array(
                'start' => ($_phase['start'] == 0) ? '00-00-0000' : date('d-m-Y', $_phase['start']),
                'end' => ($_phase['end'] == 0) ? '00-00-0000' : date('d-m-Y', $_phase['end']),
                'rstart' => ($_phase['rstart'] == 0) ? '00-00-0000' : date('d-m-Y', $_phase['rstart']),
                'rend' => ($_phase['rend'] == 0) ? '00-00-0000' : date('d-m-Y', $_phase['rend']),
                'comPlan' => !empty($comPlan) ? date('d-m-Y', $comPlan) : '',
                'comReal' => !empty($comReal) ? date('d-m-Y', $comReal) : '',
                'completed' => $completed
            );
        }
    }
    $dataView[] = $data;
}
foreach ($fieldset as $_fieldset) {
    if (!empty($noProjectManager) && $_fieldset['key'] == 'Project.project_manager_id') {
        continue;
    }
	// remove field widget if that fileds not displayed on the menu.
	if (!empty($column_hiddens) && in_array($_fieldset['key'], $column_hiddens)) {
        continue;
    }
    $financeFieldsKey = array_keys($financeFields);
    if(in_array($_fieldset['key'],$financeFieldsKey)){
        $fieldName = __($financeFields[$_fieldset['key']], true);
        $ff = explode('.', $_fieldset['key']);
        if($ff[0] == 'ProjectFinancePlus'){
			$finan_domain_name = 'Finance';
			$finan_tmp_field = $_fieldset['key'];
			$finan_domain_key = explode('_', $finan_tmp_field);
			switch($finan_domain_key[0]){
				// case 'ProjectFinancePlus.inv':
					// $domain_name = 'Budget_Investment';
					// break;
				// case 'ProjectFinancePlus.fon':
					// $domain_name = 'Budget_Operation';
					// break;
				case 'ProjectFinancePlus.finaninv':
					$finan_domain_name = 'Finance_Investment';
					break;
				case 'ProjectFinancePlus.finanfon':
					$finan_domain_name = 'Finance_Operation';
					break;
				default: 
					$finan_domain_name = 'Finance';
					break;
				
			}
			$saveFieldName = explode('_', $_fieldset['key']);
			if(!empty($saveFieldName[2]) && is_numeric($saveFieldName[2])){
				$pf_year = $saveFieldName[2];
				$fieldName = str_replace($pf_year, '(Y)', $fieldName);
				$fieldName = __d(sprintf($_domain, $finan_domain_name), $fieldName, true);
				$fieldName = str_replace('(Y)', $pf_year, $fieldName);
			}else {
                $fieldName = $fieldName;
                $fieldName = __d(sprintf($_domain, $finan_domain_name), $fieldName, true);
            }
        } else if($ff[0] == 'ProjectFinance'){
            $fieldName = __d(sprintf($_domain, 'Finance'), $fieldName, true);
        } else if($ff[0] == 'ProjectFinanceTwoPlus'){
            $saveFieldName = explode(' (', $fieldName);
            if(!empty($saveFieldName[1])) $saveFieldName[1] = str_replace(')', '', $saveFieldName[1]);
            if( !empty($saveFieldName[1]) && is_numeric($saveFieldName[1]) ){
                $fieldName = __d(sprintf($_domain, 'Finance_2'), $saveFieldName[0], true) . ' ' . $saveFieldName[1];
            } else {
                $fieldName = __d(sprintf($_domain, 'Finance_2'), $fieldName, true);
            }
        }
    } else if( strpos($_fieldset['key'], 'Project.') !== false ){
        // debug($_fieldset['key']);
        if($_fieldset['key'] == 'Project.category'){
            $fieldName = __($_fieldset['name'], true);
        } else if($_fieldset['key'] == 'Project.next_milestone_in_day'){
            $fieldName = __d(sprintf($_domain, 'Details'), 'Next milestone in day', true);
        } else if($_fieldset['key'] == 'Project.next_milestone_in_week'){
            $fieldName = __d(sprintf($_domain, 'Details'), 'Next milestone in week', true);
        } else {
            $fieldName = substr($_fieldset['name'], 0, 1) == '*' ? __(substr($_fieldset['name'], 1), true) : __d(sprintf($_domain, 'Details'), $_fieldset['name'], true);
            if( substr($_fieldset['key'], 0, 18)  == 'Project.list_muti_'){
                $k = str_replace('List Muti ', '', $_fieldset['name']);
                $fieldName = __d(sprintf($_domain, 'Details'), 'List(multiselect) ' . $k, true);
            }
        }
    } else if( strpos($_fieldset['key'], 'ProjectAmr.') !== false && in_array($_fieldset['name'], $words) ){
		
        $fieldName = __d(sprintf($_domain, 'KPI'), $_fieldset['name'], true);
        if($_fieldset['key'] == 'ProjectAmr.project_amr_solution'){
            $fieldName = __d(sprintf($_domain, 'KPI'), 'Comment', true);
        }
    } else if(in_array($_fieldset['key'], array('ProjectBudgetSyn.purchases_sold', 'ProjectBudgetSyn.purchases_to_bill', 'ProjectBudgetSyn.purchases_billed', 'ProjectBudgetSyn.purchases_paid'))){
        if($_fieldset['key'] == 'ProjectBudgetSyn.purchases_sold'){
            $fieldName = __d(sprintf($_domain, 'Purchase'), 'Sold €', true);
        } else if($_fieldset['key'] == 'ProjectBudgetSyn.purchases_to_bill'){
            $fieldName = __d(sprintf($_domain, 'Purchase'), 'To Bill €', true);
        } else if($_fieldset['key'] == 'ProjectBudgetSyn.purchases_billed'){
            $fieldName = __d(sprintf($_domain, 'Purchase'), 'Billed €', true);
        } else if($_fieldset['key'] == 'ProjectBudgetSyn.purchases_paid'){
            $fieldName = __d(sprintf($_domain, 'Purchase'), 'Paid €', true);
        }
    }
    // get fieldName for Project Task.
    else if( $_fieldset['key'] == 'ProjectAmr.manual_consumed' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), $_fieldset['name'], true);
    } else if( $_fieldset['key'] == 'ProjectBudgetSyn.Workload€' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), 'Workload €', true);
    } else if( $_fieldset['key'] == 'ProjectBudgetSyn.Consumed€' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), 'Consumed €', true);
    } else if( $_fieldset['key'] == 'ProjectBudgetSyn.Remain€' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), 'Remain €', true);
    } else if( $_fieldset['key'] == 'ProjectBudgetSyn.Amount€' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), 'Amount €', true);
    } else if( $_fieldset['key'] == 'ProjectBudgetSyn.Estimated€' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), 'Estimated €', true);
    } else if( $_fieldset['key'] == 'ProjectBudgetSyn.Initialworkload' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), 'Initial workload', true);
    } else if( $_fieldset['key'] == 'ProjectBudgetSyn.UnitPrice' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), 'Unit Price', true);
    } else if( $_fieldset['key'] == 'ProjectBudgetSyn.Overload' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), 'Overload', true);
    } else if( $_fieldset['key'] == 'ProjectBudgetSyn.ManualConsumed' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), 'Manual Consumed', true);
    } else if( $_fieldset['key'] == 'ProjectBudgetSyn.Completed' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), 'Completed', true);
    } else if( $_fieldset['key'] == 'ProjectBudgetSyn.InUsed' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), 'In Used', true);
    } else if( $_fieldset['key'] == 'ProjectBudgetSyn.%progressorder' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), '% progress order', true);
    } else if( $_fieldset['key'] == 'ProjectBudgetSyn.%progressorder€' ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'), '% progress order €', true);
    } else if( in_array($_fieldset['key'], array('ProjectBudgetSyn.Consumed', 'ProjectBudgetSyn.Remain', 'ProjectBudgetSyn.Workload')) ){
        $fieldName = __d(sprintf($_domain, 'Project_Task'),  $_fieldset['name'], true);
    } else {
        $fieldName = __($_fieldset['name'], true);
        $ff = explode('.', $_fieldset['key']);
        if( substr($ff[1], 0, 5) == 'sales' ){
            $fieldName = __d(sprintf($_domain, 'Sales'), $_fieldset['name'], true);
        } else if ( substr($ff[1], 0, 8) == 'internal' ) {
            if($_fieldset['key'] == 'ProjectBudgetSyn.internal_costs_engaged_md'){
                $_fieldset['name'] = 'Engaged M.D';
            }
            $fieldName = __d(sprintf($_domain, 'Internal_Cost'), $_fieldset['name'], true);
        } else if ( substr($ff[1], 0, 8) == 'external' ) {
            $fieldName = __d(sprintf($_domain, 'External_Cost'), $_fieldset['name'], true);
        }
    }

    $_fieldListKey = trim(str_replace('ProjectBudgetSyn.', '', $_fieldset['key']));
	
	$wd_default = !empty($checkWidth[$_fieldset['key']]) ? $checkWidth[$_fieldset['key']] : ( !empty($default_widths[$_fieldset['key']]) ? $default_widths[$_fieldset['key']] : $default_widths['default'] );
    $_column = array(
        'id' => $_fieldset['key'],
        'field' => $_fieldset['key'],
        'name' => !empty($listDatas) && !empty($listDatas[$_fieldListKey]) ? $listDatas[$_fieldListKey] : $fieldName,
		'nameExport' => !empty($_fieldset['nameExport']) ? $_fieldset['nameExport'] : '',
        'sortable' => true,
        'resizable' => true,
		'width' =>  intval( $_column['width'] = (!empty($loadFilter) && !empty($loadFilter[$_fieldset['key']. '.Resize'])) ? $loadFilter[$_fieldset['key']. '.Resize'] :  $wd_default),
    );
    switch ($_fieldset['key']) {
        case 'Project.project_name':
            $_column['formatter'] = 'Slick.Formatters.linkFormatter';
            $_column['cssClass'] = 'bg_gray slick-cell-merged';
            $_column['headerCssClass'] = 'slick-header-merged';
            break;
        case 'MFavorite.modelId':
            $_column['formatter'] = 'Slick.Formatters.projectFavorite';
            $_column['cssClass'] = 'bg_gray';
			$_column['width'] = 50;
			$_column['resizable'] = false;
			$wd_index++;
            break;
        case 'Project.project_manager_id':
            $_column['formatter'] = 'Slick.Formatters.avaResource';
            $_column['cssClass'] = 'text-hover-show';
            break;
        case 'Project.functional_leader_id':
        case 'Project.chief_business_id':
        case 'Project.technical_manager_id':
        case 'Project.read_access':
        case 'Project.uat_manager_id':
            $_column['formatter'] = 'Slick.Formatters.avaProjectManager';
            break;
        case 'Project.freeze_time':
        case 'Project.start_date':
        case 'Project.planed_end_date':
        case 'Project.end_date':
        case 'ProjectAmr.created':
        case 'Project.created':
        case 'Project.updated_opp_ip':
        case 'Project.updated_ip_arch':
        case 'Project.date_1':
        case 'Project.date_2':
        case 'Project.date_3':
        case 'Project.date_4':
        case 'Project.date_5':
        case 'Project.date_6':
        case 'Project.date_7':
        case 'Project.date_8':
        case 'Project.date_9':
        case 'Project.date_10':
        case 'Project.date_11':
        case 'Project.date_12':
        case 'Project.date_13':
        case 'Project.date_14':
        case 'Project.date_mm_yy_1': 
        case 'Project.date_mm_yy_2': 
        case 'Project.date_mm_yy_3': 
        case 'Project.date_mm_yy_4': 
        case 'Project.date_mm_yy_5': 
            $_column['datatype'] = 'datetime';
            break;
								  
        case 'ProjectAmr.rank':
        case 'ProjectAmr.cost_control_weather':
        case 'ProjectAmr.planning_weather':
        case 'ProjectAmr.risk_control_weather':
        case 'ProjectAmr.organization_weather':
        case 'ProjectAmr.perimeter_weather':
        case 'ProjectAmr.issue_control_weather':
        case 'ProjectAmr.customer_point_of_view':
        case 'ProjectAmr.weather':
            // $_column['isImage'] = true;
            $_column['formatter'] = 'Slick.Formatters.ImageData';
            $_column['sorter'] = 'weatherSorter';
            break;
        case 'ProjectAmr.budget_weather':
        case 'ProjectAmr.scope_weather':
        case 'ProjectAmr.schedule_weather':
        case 'ProjectAmr.resources_weather':
        case 'ProjectAmr.technical_weather':
            // $_column['isImage'] = true;
            $_column['formatter'] = 'Slick.Formatters.ImageDataNew';
            $_column['sorter'] = 'weatherSorter';
            break;
        case 'ProjectAmr.comment':
        case 'ProjectAmr.project_amr_risk_information':
        case 'ProjectAmr.project_amr_problem_information':
        case 'ProjectAmr.done':
        case 'ProjectAmr.todo':
        case 'ProjectAmr.project_amr_budget_comment':
        case 'ProjectAmr.project_amr_scope':
        case 'ProjectAmr.project_amr_schedule':
        case 'ProjectAmr.project_amr_resource':
        case 'ProjectAmr.project_amr_technical':
        case 'ProjectAmr.project_amr_solution':
            $_column['formatter'] = 'Slick.Formatters.comment';
			$_column['cssClass'] = 'wd-open-popup';
			$_column['datatype'] = 'array';
			$_column['sortKey'] = 'updated';
			$_column['sortType'] = 'number';
			$_column['asyncPostRender'] = 'asyncResizableFirstRow';
            break;
        case 'Project.project_amr_progression':
        case 'Project.md_forecasted':
        case 'Project.md_validated':
        case 'Project.md_engaged':
        case 'Project.md_variance':
        case 'ProjectAmr.project_amr_progression':
        case 'ProjectAmr.md_validated':
        case 'ProjectAmr.md_engaged':
        case 'ProjectAmr.internal_costs_engaged_md':
        case 'ProjectAmr.md_variance':
        case 'ProjectAmr.md_forecasted':
        case 'ProjectAmr.validated':
        case 'ProjectAmr.engaged':
        case 'ProjectAmr.forecasted':
        case 'ProjectAmr.variance':
        case 'ProjectAmr.manual_consumed':
        case 'ProjectBudgetSyn.Workload€':
        case 'ProjectBudgetSyn.Consumed€':
        case 'ProjectBudgetSyn.Remain€':
        case 'ProjectBudgetSyn.Amount€':
        case 'ProjectBudgetSyn.Estimated€':
        case 'ProjectBudgetSyn.Remain':
        case 'ProjectBudgetSyn.Workload':
        case 'ProjectBudgetSyn.Initialworkload':
        case 'ProjectBudgetSyn.UnitPrice':
        case 'ProjectBudgetSyn.%progressorder€':
        case 'ProjectBudgetSyn.Consumed':
            $_column['formatter'] = 'Slick.Formatters.floatFormatter';
            break;
        case 'Project.is_freeze':
        case 'Project.is_staffing':
        case 'Project.off_freeze':
        case 'Project.project_copy':
            $_column['formatter'] = 'Slick.Formatters.yesNoFormatter';
            break;
        case 'Project.project_priority_id':
            $_column['isSelected'] = true;
            $_column['formatter'] = 'Slick.Formatters.selectFormatter';
            $_column['width'] = 120;
            break;
        case 'Project.project_type_id':
        case 'Project.project_sub_type_id':
        case 'Project.project_sub_sub_type_id':
            break;
        case 'Project.project_amr_program_id':
        case 'Project.project_amr_sub_program_id':
            // $_column['isSelected'] = true;
            $_column['formatter'] = 'Slick.Formatters.selectBoxFormatter';
            break;
        case 'Project.next_milestone_in_day':
        case 'Project.next_milestone_in_week':
            $_column['formatter'] = 'Slick.Formatters.nextMilestone';
            break;
		case 'Project.upload_documents_1':
		case 'Project.upload_documents_2':
		case 'Project.upload_documents_3':
		case 'Project.upload_documents_4':
		case 'Project.upload_documents_5':
			$has_upload = 1;
			$_column['formatter'] = 'Slick.Formatters.uploadDocument';
            break;
		case 'ProjectWidget.Project_progress':
			$cssClass = array(
				'no-padding',
				'ProjectWidget',
				'ProjectProgress',
				(($wd_index%2) ? 'wg_odd' : 'wg_even' ), //even : chan , odd: le
			);
			$wd_index++;
			$headerCssClass = 'slick_filter_percent';
			$_column = array_merge($_column, array(
				'width' => 150,
				'cssClass' => implode(' ', $cssClass),
				'headerCssClass' => $headerCssClass,
				'formatter' => 'Slick.Formatters.widgetProject_progress',
				'datatype' => 'number',
				'asyncPostRender' => 'asyncDragablePhaseProgress',
				'customFilterDisplay' => 'CustomFilter.display.Project_progress',
				'customFilterFunction' => 'CustomFilter.filter.Project_progress'
			));
			break;
		case 'ProjectWidget.Phase_plan':
		case 'ProjectWidget.Phase_real':
		case 'ProjectWidget.Phase_progress':
		case 'ProjectWidget.Phase_diff':
			$widget_name = str_replace('ProjectWidget.', '', $_fieldset['key']);
			$formatter = 'Slick.Formatters.widget'.$widget_name;
			$widget_name = explode('_', $widget_name);
			$widget_name = $widget_name[0];			
			$headerCssClass = '';
			$cssClass = array(
				'no-padding',
				'ProjectWidget',
				$widget_name,
				(($wd_index%2) ? 'wg_odd' : 'wg_even' ), //even : chan , odd: le
			);
			$headerCssClass .= (($wd_index%2) ? ' wg_odd' : ' wg_even');
			if( $_fieldset['key'] !== 'ProjectWidget.Phase_diff'){
				$headerCssClass = 'slick-header-merged' . (($wd_index%2) ? ' wg_odd' : ' wg_even');
				$cssClass[] = 'slick-cell-merged';
			}else{
				$headerCssClass .= (($wd_index%2) ? ' wg_odd' : ' wg_even');
				$wd_index++;
			}
			$_column = array_merge($_column, array(
				'width' => 150,
				'cssClass' => implode(' ', $cssClass),
				'headerCssClass' => $headerCssClass,
				'formatter' => $formatter,
				'datatype' => 'datetime'
			));
			if( $_fieldset['key'] == 'ProjectWidget.Phase_progress'){
				$_column['datatype'] = 'number';
				$_column['asyncPostRender'] = 'asyncDragablePhaseProgress';
			}
			if($_fieldset['key'] == 'ProjectWidget.Phase_diff'){
				$_column['datatype'] = 'number';
				$_column['width'] = 70;
			}
			break;
		case 'ProjectWidget.Milestone_late':
		case 'ProjectWidget.Milestone_next':
			$widget_name = str_replace('ProjectWidget.', '', $_fieldset['key']);
			$formatter = 'Slick.Formatters.widget'.$widget_name;
			$datatype_sort = 'DataSet.'. $widget_name .'.date';
			$widget_name = explode('_', $widget_name);
			$widget_name = $widget_name[0];			
			$headerCssClass = '';
			$cssClass = array(
				'no-padding',
				'ProjectWidget',
				$widget_name,
				(($wd_index%2) ? 'wg_odd' : 'wg_even' ), //even : chan , odd: le
			);
			if( $_fieldset['key'] !== 'ProjectWidget.Milestone_next'){
				$headerCssClass = 'slick-header-merged';
				$cssClass[] = 'slick-cell-merged';
			}
			$headerCssClass .= (($wd_index%2) ? ' wg_odd' : ' wg_even');
			if( $_fieldset['key'] == 'ProjectWidget.Milestone_next'){
				$wd_index++;
			}
			$_column = array_merge($_column, array(
				'width' => 200,
				'cssClass' => implode(' ', $cssClass),
				'headerCssClass' => $headerCssClass,
				'formatter' => $formatter,
				'datatype' => 'datetime',
				// 'datatype' => 'mixed',
				// 'datatype_sort' => $datatype_sort,
			));
			break;
		case 'ProjectWidget.FinancePlus_inv_budget':
		case 'ProjectWidget.FinancePlus_inv_engaged':
		case 'ProjectWidget.FinancePlus_inv_percent':
		case 'ProjectWidget.FinancePlus_fon_budget':
		case 'ProjectWidget.FinancePlus_fon_engaged':
		case 'ProjectWidget.FinancePlus_fon_percent':
		case 'ProjectWidget.FinancePlus_finaninv_budget':
		case 'ProjectWidget.FinancePlus_finaninv_engaged':
		case 'ProjectWidget.FinancePlus_finaninv_percent':
		case 'ProjectWidget.FinancePlus_finanfon_budget':
		case 'ProjectWidget.FinancePlus_finanfon_engaged':
		case 'ProjectWidget.FinancePlus_finanfon_percent':
			$widget_name = str_replace('ProjectWidget.', '', $_fieldset['key']);
			$formatter = 'Slick.Formatters.widget'.$widget_name;
			$widget_name = explode('_', $widget_name);
			$widget_name = $widget_name[0];			
			$headerCssClass = '';
			$cssClass = array(
				'no-padding',
				'ProjectWidget',
				$widget_name,
				(($wd_index%2) ? 'wg_odd' : 'wg_even' ), //even : chan , odd: le
			);
			if( $_fieldset['key'] !== 'ProjectWidget.FinancePlus_finanfon_percent'){
				$headerCssClass = 'slick-header-merged';
				$cssClass[] = 'slick-cell-merged';
			}
			$headerCssClass .= (($wd_index%2) ? ' wg_odd' : ' wg_even');
			$header_title = " ";
			if(!empty($_column['name'])){
				$header_title = !empty($finan_title[$_column['name']]) ? $finan_title[$_column['name']] : ' ';
			}
			$nameExport = '';
			if(!empty($_column['nameExport'])){
				$name_group = !empty($finan_title[$_column['nameExport']]) ? $finan_title[$_column['nameExport']] : '';
				if($_column['nameExport'] == '%'){
					$nameExport = '%';
				}else{
					$nameExport = (!empty($name_group) && !empty($labelSummaryHeader[$_fieldset['key']])) ? $name_group .' - '. $labelSummaryHeader[$_fieldset['key']] : '';
				}
			}
			$_column = array_merge($_column, array(
				'width' => !empty($header_title) ? 180 :  150,
				'name' => $header_title,
				'nameExport' => $nameExport,
				'cssClass' => implode(' ', $cssClass),
				'headerCssClass' => $headerCssClass,
				'formatter' => $formatter,
				'datatype' => 'number'
			));
			$finan_type = array('inv', 'fon','finaninv', 'finanfon');
			foreach($finan_type as $key => $type){
				if( $_fieldset['key'] == 'ProjectWidget.FinancePlus_'. $type .'_percent'){
					$_column['width'] = 80;
					$wd_index++;
				}
			}
			
			break;
		case 'ProjectWidget.Synthesis_budget':
		case 'ProjectWidget.Synthesis_forecast':
		case 'ProjectWidget.Synthesis_percent':
			$widget_name = str_replace('ProjectWidget.', '', $_fieldset['key']);
			$formatter = 'Slick.Formatters.widget'.$widget_name;
			$widget_name = explode('_', $widget_name);
			$widget_name = $widget_name[0];			
			$cssClass = array(
				'no-padding',
				'ProjectWidget',
				$widget_name,
				(($wd_index%2) ? 'wg_odd' : 'wg_even' ), //even : chan , odd: le
			);
			$headerCssClass = '';
			if( $_fieldset['key'] !== 'ProjectWidget.Synthesis_percent'){
				$headerCssClass = 'slick-header-merged';
				$cssClass[] = 'slick-cell-merged';
			}
			$headerCssClass .= (($wd_index%2) ? ' wg_odd' : ' wg_even');
			$header_name = '';
			$_column = array_merge($_column, array(
				'width' => 150,
				'cssClass' => implode(' ', $cssClass),
				'headerCssClass' => $headerCssClass,
				'formatter' => $formatter,
				'datatype' => 'number'
				
			));
			if( $_fieldset['key'] == 'ProjectWidget.Synthesis_percent'){
				$_column['width'] = 80;
				$wd_index++;
			}
			break;
		case 'ProjectWidget.Internal_budget_md':
		case 'ProjectWidget.Internal_forecast_md':
		case 'ProjectWidget.Internal_percent_forecast_md':
		case 'ProjectWidget.Internal_consumed_md':
		case 'ProjectWidget.Internal_percent_consumed_md':
			$widget_name = str_replace('ProjectWidget.', '', $_fieldset['key']);
			$formatter = 'Slick.Formatters.widget'.$widget_name;
			$widget_name = explode('_', $widget_name);
			$widget_name = $widget_name[0];			
			$cssClass = array(
				'no-padding',
				'ProjectWidget',
				$widget_name,
				(($wd_index%2) ? 'wg_odd' : 'wg_even' ), //even : chan , odd: le
			);
			$headerCssClass = '';
			if( $_fieldset['key'] !== 'ProjectWidget.Internal_percent_consumed_md'){
				$headerCssClass = 'slick-header-merged';
				$cssClass[] = 'slick-cell-merged';
			}
			$headerCssClass .= (($wd_index%2) ? ' wg_odd' : ' wg_even');
			$group_column_name = $enableWidgets['internal_cost'] . ' '. __('M.D', true);
			$nameExport = '';
			if(!empty($_column['nameExport'])){
				if($_column['nameExport'] == '%'){
					$nameExport = '%';
				}else{
					$nameExport = $group_column_name .' - '. $i18n[$_column['nameExport']];
				}
			}
			
			$_column = array_merge($_column, array(
				'width' => 150,
				'nameExport' => $nameExport,
				'cssClass' => implode(' ', $cssClass),
				'headerCssClass' => $headerCssClass,
				'formatter' => $formatter,
				'datatype' => 'number'
				
			));
			if( $_fieldset['key'] == 'ProjectWidget.Internal_percent_consumed_md' || $_fieldset['key'] == 'ProjectWidget.Internal_percent_forecast_md'){
				$_column['width'] = 80;
			}
			if( $_fieldset['key'] == 'ProjectWidget.Internal_percent_consumed_md'){
				// $_column['width'] = 80;
				$wd_index++;
			}
			break;
		case 'ProjectWidget.Internal_budget_euro':
		case 'ProjectWidget.Internal_forecast_euro':
		case 'ProjectWidget.Internal_percent_forecast_euro':
		case 'ProjectWidget.Internal_engaged_euro':
		case 'ProjectWidget.Internal_percent_consumed_euro':
			$widget_name = str_replace('ProjectWidget.', '', $_fieldset['key']);
			$formatter = 'Slick.Formatters.widget'.$widget_name;
			$widget_name = explode('_', $widget_name);
			$widget_name = $widget_name[0];			
			$cssClass = array(
				'no-padding',
				'ProjectWidget',
				$widget_name,
				(($wd_index%2) ? 'wg_odd' : 'wg_even' ), //even : chan , odd: le
			);
			$headerCssClass = '';
			if( $_fieldset['key'] !== 'ProjectWidget.Internal_percent_consumed_euro'){
				$headerCssClass = 'slick-header-merged';
				$cssClass[] = 'slick-cell-merged';
			}
			$headerCssClass .= (($wd_index%2) ? ' wg_odd' : ' wg_even');
			$group_column_name = !empty($enableWidgets['internal_cost']) ? $enableWidgets['internal_cost'] . ' '. $budget_settings : '';
			$nameExport = '';
			if(!empty($_column['nameExport'])){
				if($_column['nameExport'] == '%'){
					$nameExport = '%';
				}else{
					$nameExport = $group_column_name .' - '. $i18n[$_column['nameExport']];
				}
			}
			$_column = array_merge($_column, array(
				'width' => 150,
				'nameExport' => $nameExport,
				'cssClass' => implode(' ', $cssClass),
				'headerCssClass' => $headerCssClass,
				'formatter' => $formatter,
				'datatype' => 'number'
				
			));
			if( $_fieldset['key'] == 'ProjectWidget.Internal_percent_consumed_euro' || $_fieldset['key'] == 'ProjectWidget.Internal_percent_forecast_euro'){
				$_column['width'] = 80;
			}
			if( $_fieldset['key'] == 'ProjectWidget.Internal_percent_consumed_euro'){
				// $_column['width'] = 80;
				$wd_index++;
			}
			break;
		case 'ProjectWidget.External_budget_erro':
		case 'ProjectWidget.External_forecast_erro':
		case 'ProjectWidget.External_percent_forecast_erro':
		case 'ProjectWidget.External_ordered_erro':
		case 'ProjectWidget.External_percent_ordered_erro':
			$widget_name = str_replace('ProjectWidget.', '', $_fieldset['key']);
			$formatter = 'Slick.Formatters.widget'.$widget_name;
			$widget_name = explode('_', $widget_name);
			$widget_name = $widget_name[0];			
			$cssClass = array(
				'no-padding',
				'ProjectWidget',
				$widget_name,
				(($wd_index%2) ? 'wg_odd' : 'wg_even' ), //even : chan , odd: le
			);
			
			$headerCssClass = '';
			if( $_fieldset['key'] !== 'ProjectWidget.External_percent_ordered_erro'){
				$headerCssClass = 'slick-header-merged';
				$cssClass[] = 'slick-cell-merged';
			}
			$headerCssClass .= (($wd_index%2) ? ' wg_odd' : ' wg_even');
			$group_column_name = !empty($enableWidgets['external_cost']) ? $enableWidgets['external_cost'] : '';
			$nameExport = '';
			if(!empty($_column['nameExport'])){
				if($_column['nameExport'] == '%'){
					$nameExport = '%';
				}else{
					$nameExport = $group_column_name .' - '. $i18n[$_column['nameExport']];
				}
			}
			$_column = array_merge($_column, array(
				'width' => 150,
				'nameExport' => $nameExport,
				'cssClass' => implode(' ', $cssClass),
				'headerCssClass' => $headerCssClass,
				'formatter' => $formatter,
				'datatype' => 'number'
				
			));
			if( $_fieldset['key'] == 'ProjectWidget.External_percent_ordered_erro' || $_fieldset['key'] == 'ProjectWidget.External_percent_forecast_erro'){
				$_column['width'] = 80;
			}
			if( $_fieldset['key'] == 'ProjectWidget.External_percent_ordered_erro'){
				$wd_index++;
			}
			break;
		// case 'ProjectWidget.Progress':
			// $cssClass = ($wd_index%2) ? 'wd_odd' : 'wd_even'; //even : chan , odd: le
			// $_column['width'] = 250;
			// $_column['cssClass'] = 'no-padding ' . str_replace('.', ' ', $_fieldset['key']) . ' ' . $cssClass;
			// $_column['formatter'] = 'Slick.Formatters.widgetProgress';
			// $wd_index++;
			// break;
    }
    if(in_array($_fieldset['key'], $columnAlignRight)){
        $_column['formatter'] = 'Slick.Formatters.numberVal';
        $_column['datatype'] = 'number';
    }
    if(in_array($_fieldset['key'], $columnAlignRightAndEuro)){
        $_column['formatter'] = 'Slick.Formatters.numberValEuro';
        $_column['datatype'] = 'number';
    }
    if(in_array($_fieldset['key'], $columnAlignRightAndManDay)){
        $_column['formatter'] = 'Slick.Formatters.numberValManDay';
        $_column['datatype'] = 'number';
    }
    if(in_array($_fieldset['key'], $columnAlignRightAndPercent)){
        $_column['formatter'] = 'Slick.Formatters.numberValPercent';
        $_column['datatype'] = 'number';
    }
    if(in_array($_fieldset['key'], $columnNoFilters)){
        $_column['noFilter'] = 1;
    }
    if(in_array($_fieldset['key'], $columnDefaultTranslate)){
        $_column['name'] = __($_column['name'], true);
    }
    $columns[] = $_column;
}
// Add selects for filter
// add by Huynh
$_list_resources = array(
	'Project.technical_manager_id' => $_technical_manager_list,
	'Project.chief_business_id' => $_chief_business_list,
	'Project.functional_leader_id' => $_functional_leader_list,
	'Project.uat_manager_id' => $_uat_manager_list,
	'Project.read_access' => $project_read_access_manager_list,
);

foreach( $_list_resources as $key => $val){
	$_list = array();
	$_list_fliter = array();
	foreach ($val as $_us_list){
		$_list = array_merge($_list, $_us_list);
	}
    if($key != 'Project.read_access') $_list = array_values(array_unique($_list));
	foreach($_list as $_resource){
        if($key == 'Project.read_access'){
            if(!empty($_resource['is_profit_center'])){
                $_list_fliter[$_resource['id']] = $this->UserFile->employee_fullname($_resource['id'] . '-1');
            }else $_list_fliter[$_resource['id']] = $this->UserFile->employee_fullname($_resource['id']);
        }else $_list_fliter[$_resource] = $this->UserFile->employee_fullname($_resource);
	}
	$selects[$key] = array_filter($_list_fliter);
}
// exit;
/**
 * Gantt
 */
$leftColumns = count($columns);
$columnsOfGantt = array();
if($viewGantt){
    $yStart = !empty($confirmGantt['from']) ? $confirmGantt['from'] : date('Y', $ganttStart);
    $yEnd = !empty($confirmGantt['to']) ? $confirmGantt['to'] : date('Y', $ganttEnd);
    while($yStart <= $yEnd){
        $columnsOfGantt[] = 'Gantt' . $yStart;
        $columns[] = array(
            'id' => 'Gantt' . $yStart,
            'field' => 'Gantt' . $yStart,
            'name' => __($yStart, true),
            'width' => 365,
            'sortable' => false,
            'resizable' => false,
            'noFilter' => 1,
			'headerCssClass' => ( $yStart % 2) ? 'wd-highlight-column' : '',
            'formatter' => 'Slick.Formatters.GanttCustom'
        );
        $yStart++;
    }
    $columnsOfGantt[] = 'action.';
}
if( !($isMobileOnly || $isTablet) || (($isMobileOnly || $isTablet) && !$viewGantt) ){
    $columns[] = array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __('Action', true),
        'width' => 125,
        'sortable' => false,
        'resizable' => true,
        'ignoreExport' => true,
        'cssClass' => 'grid-action',
        'formatter' => 'Slick.Formatters.Action',
        'noFilter' => 1
    );
}
$selects['Project.project_priority_id'] = $priorities;
$selects['Project.bool_1'] = array('1' => '1', 'zero' => '0');
$selects['Project.bool_2'] = array('1' => '1', 'zero' => '0');
$selects['Project.bool_3'] = array('1' => '1', 'zero' => '0');
$selects['Project.bool_4'] = array('1' => '1', 'zero' => '0');
$selects['Project.project_phase_id'] = $ProjectPhases;
$selects['Project.team'] = $listPc;
$selects['Project.list_muti_1'] = !empty($datasets['list_muti_1']) ? $datasets['list_muti_1'] : array();
$selects['Project.list_muti_2'] = !empty($datasets['list_muti_2']) ? $datasets['list_muti_2'] : array();
$selects['Project.list_muti_3'] = !empty($datasets['list_muti_3']) ? $datasets['list_muti_3'] : array();
$selects['Project.list_muti_4'] = !empty($datasets['list_muti_4']) ? $datasets['list_muti_4'] : array();
$selects['Project.list_muti_5'] = !empty($datasets['list_muti_5']) ? $datasets['list_muti_5'] : array();
$selects['Project.list_muti_6'] = !empty($datasets['list_muti_6']) ? $datasets['list_muti_6'] : array();
$selects['Project.list_muti_7'] = !empty($datasets['list_muti_7']) ? $datasets['list_muti_7'] : array();
$selects['Project.list_muti_8'] = !empty($datasets['list_muti_8']) ? $datasets['list_muti_8'] : array();
$selects['Project.list_muti_9'] = !empty($datasets['list_muti_9']) ? $datasets['list_muti_9'] : array();
$selects['Project.list_muti_10'] = !empty($datasets['list_muti_10']) ? $datasets['list_muti_10'] : array();
ksort($projectManagers);
$projectManagers = Set::combine(array_values($projectManagers), '{n}.0', '{n}.1');
$selectMaps = $selects;
$list_cate = array(
	1 => __("In progress", true),
	6 => __("Opportunity", true),
	5 => __("In progress + Opportunity", true),
	3 => __("Archived", true),
	4 => __("Model", true),
	
)
?>

<?php 

	$svg_icons = array(
		'grid' =>'<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
		style="
			top:4px;
			position:relative;
		">
		<defs>
		<style>
			.cls-1 {
				fill: #666;
				fill-rule: evenodd;
			}
		</style>
		</defs>
		<path id="grid" data-name="Mode Thumbs" class="cls-1" d="M361,40V31h9v9h-9Zm8-8h-7v7h7V32Zm-8-12h9v9h-9V20ZM350,31h9v9h-9V31Zm0-11h9v9h-9V20Z" transform="translate(-350 -20)"/>
		</svg>',
		'star' => '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 47.94 47.94" style="enable-background:new 0 0 47.94 47.94;" xml:space="preserve">
		<path class="svg-stroke-color" d="M26.285,2.486l5.407,10.956c0.376,0.762,1.103,1.29,1.944,1.412l12.091,1.757  c2.118,0.308,2.963,2.91,1.431,4.403l-8.749,8.528c-0.608,0.593-0.886,1.448-0.742,2.285l2.065,12.042  c0.362,2.109-1.852,3.717-3.746,2.722l-10.814-5.685c-0.752-0.395-1.651-0.395-2.403,0l-10.814,5.685  c-1.894,0.996-4.108-0.613-3.746-2.722l2.065-12.042c0.144-0.837-0.134-1.692-0.742-2.285l-8.749-8.528  c-1.532-1.494-0.687-4.096,1.431-4.403l12.091-1.757c0.841-0.122,1.568-0.65,1.944-1.412l5.407-10.956  C22.602,0.567,25.338,0.567,26.285,2.486z"/></svg>',
	);
	$container_width = 0;
	if(!empty($filter_render)){
		foreach($columns as $key => $vals){
			$field_resize = $vals['field'] . '.Resize';
			if(!empty($filter_render[$field_resize])){
				$columns[$key]['width'] = intval($filter_render[$field_resize]);
			}
		}
	}
	foreach($columns as $key => $vals){
		$container_width += $vals['width'];
	}
?>
<div id="addProjectTemplate" class="loading-mark"></div>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-tab"> 
				 <div class="wd-list-project">
				<!-- Add old filter -->
						<?php 
						echo $this->Session->flash();
						?>
                        <div class="wd-title">
							<?php 
							if(!empty($listLogo) ){?>
								<div class="wd-customer-logo">
									<?php 
									$logo_display = $this->Html->url(array('controller' => 'customer_logos', 'action' => 'attachment', $listLogo[0]['id'], '?' => array('sid' => $api_key)), true);
									if(count($listLogo) > 1){ ?>
										<div class="wd-popup-customer-logo">
											<a href="javascript:void(0)" class="wd-logo-close pull-right" onclick="cancel_popup_logo(this);"><img title="close" src="<?php echo $this->Html->url('/img/new-icon/close.png');?>"></a>
											<ul>
											<?php
											
											foreach($listLogo as $index => $cLogo){ 
												$logo_id = $cLogo['id'];
												$url = $this->Html->url(array('controller' => 'customer_logos', 'action' => 'attachment', $logo_id, '?' => array('sid' => $api_key)), true);
												$checked = '';
												if(!empty($employee_logo) && !empty($employee_logo[$logo_id])){
													$logo_display = $url;
													$checked = 'checked';
												}
											?>
												<li class="wd-popup-logo-item">
													<label><span class="wd-logo-checkbox <?php echo $checked; ?>"></span><input <?php echo $checked; ?> type="checkbox" rel="no-history" name="logo-check-<?php echo $logo_id ?>" class="checkbox-logo" value="<?php echo $logo_id; ?>" /><img src="<?php echo $url; ?>" /></label>
												</li>
											<?php }	?>
											</ul>
										</div>
									<?php }	?>
									<span  class="img-customer-logo"><img src="<?php echo $logo_display; ?>" /></span>
									
								</div>
							<?php } ?>
                            <div class="check-box-stones" style="display:none">
						<?php
							echo $this->Form->input('milestones.check', array(
								'id' => 'MilestonesCheck',
								'style' => 'float:left;',
								'type' => 'checkbox',
								'div' => false,
								'label' => false,
								'rel' => 'no-history'
							));
						?>
                                <p style="float:left;font-weight:bold"><?php echo __("Display all name of milestones", true); ?></p>
                            </div>

					<?php
						echo $this->Form->create('Category', array('style' => 'display: inline-block'));
						$href = '';
						$href = $this->params['url'];
						if(!empty($appstatus)){
							$op = ($appstatus == 1) ? 'selected="selected"' : ''; //InPro
							$ar = ($appstatus == 3) ? 'selected="selected"' : ''; //Archived
							$md = ($appstatus == 4) ? 'selected="selected"' : ''; //Model
							$io = ($appstatus == 5) ? 'selected="selected"' : ''; //Oppor and InPro
							$io2 = ($appstatus == 6) ? 'selected="selected"' : ''; //Oppor
						}
						$_sum_project = array( '','','','','','','','',);
						$_count_project = count($projects);
						$_sum_project[$appstatus] = ' ('. $_count_project. '/'. $_count_project.')';
					?>
                            <select style="margin-right:5px; width: auto !important; float: none" class="wd-customs" id="CategoryStatus" rel="no-history">
                                <option value="0"><?php echo  __("--Select--", true);?></option>
                            </select>
					<?php if($cate != 2):?>
                            <select style="margin-right:5px; width: auto !important; float: none" class="wd-customs" id="CategoryCategory" rel="no-history">

									<?php foreach( $list_cate as $key => $val){ ?>
                                <option value = "<?php echo $key;?>" <?php if($appstatus == $key) echo 'selected="selected"' ; ?> > <?php echo $val . $_sum_project[$key] ;?> </option>
									<?php } ?>
                            </select>
					<?php endif;?>
					<?php
						echo $this->Form->end();
					?>
                    <a href="javascript:void(0);" id="export-table" class="btn btn-excel" title="<?php __('Export Excel file');?>"></a>
					<?php if(($employee['Employee']['create_a_project'] == 1 && empty($profileName)) || (!empty($profileName) && ($profileName['ProfileProjectManager']['can_create_project'] == 1)) || ($employee['Role']['id'] == 2)){ ?>
                            <a href="javascript:void(0);" id="add_new_popup" class="btn btn-text btn-blue" title="<?php __('wdtext_create_a_project');?>">
                                <i class="icon-plus"></i>
									<?php if(!isset($companyConfigs['add_proroject_full_icon']) || $companyConfigs['add_proroject_full_icon'] == 1) { ?>
                                <span><?php __('Add Project') ?></span>
									<?php } ?>
                            </a>
					<?php } ?>
                            <a href="<?php echo $this->Html->url('/user_views/') ?>" target="_blank" class="btn button-setting" title="<?php __('Manage the personalized views');?>"></a>
					<?php if(!isset($companyConfigs['display_project_global']) || $companyConfigs['display_project_global'] == 1) { ?>
                            <a href="<?php echo $this->Html->url('/projects_preview/map/') ?>" id="map-icon" class="btn btn-globe" title="<?php __('Map');?>"></a>
					<?php } ?>
					<?php if(!isset($companyConfigs['display_project_grid']) || $companyConfigs['display_project_grid'] == 1) { ?>
                            <a href="<?php echo $this->Html->url('/projects/index_plus') ?>" id="grid-icon" class="btn btn-grid" title="<?php __('Grid');?>"><?php echo $svg_icons['grid'] ?></a>
					<?php } ?>

                            <a href="javascript:void(0);" class="btn btn-text reset-filter hidden" id="reset-filter" onclick="resetFilter();" style="margin-right:5px;" title="<?php __('Delete the filter') ?>">
                                <i class="icon-refresh"></i>
                            </a>
                            <a href="javascript:void(0);" class="wd-sumrow btn btn-text" onclick="openSumrow.call(this);">
                                <i class="sumrow html_entity" style="top: -3px; position: relative;" title="<?php __('Enable Sum row');?>" >&sum;</i>
                            </a> 
					<?php if(($employee_info['Role']['name'] == 'pm' && count($sql_request) > 0)){ 
						$report_url = $html->url('/reports/');
						$report_tab = '';
						$count_report = count($sql_request);
						if($count_report == 1){
							$sql_manager_id = array_values($sql_request);
							$report_url = $html->url(array('controller' => 'reports', 'action' => 'viewReport', $sql_manager_id[0]));
							$report_tab = 'target="_blank"';
						}
							
					?>
                            <a href="<?php echo $report_url; ?>" class="btn btn-sql btn-text" title= "<?php echo __('Project SQL');?>" <?php echo $report_tab;?>>
                                <i class="icon-equalizer"></i>
                            </a> 
					<?php } ?>
					<?php if($filter_alert){ ?>
                            <a href="javascript:void(0);" id="filter_alert" class="btn btn-filter-alert " title="<?php __('Display the project with an alert') ?>">
                            </a>
							<input type="hidden" name="filter_alert" id="filter_alert_input" class="filter_alert_input" />
					<?php } ?>
						<a href="javascript:void(0);" id="filter_project_favorite" class="btn btn-project-favorite" title="<?php __('Display favorite projects') ?>">
							<?php echo $svg_icons['star'];?>
                            <input type="hidden" name="filter_project_favorite" id="filter_favorite_input" class="filter_favorite_input" />
						</a>
							
					<?php if( $isTablet ): ?>

					<?php if($displayExpectation){?>
                            <a href="javascript:void(0);" id="expectation_screen" class="btn btn-text btn-blue btn-add" title="<?php __('Vision Expectation') ?>">
                                <p class="line"></p>
                                <p class="line"></p>
                                <span class="wd-hide"><?php __('Vision Expectation') ?></span>
                            </a>
					<?php } ?>

					<?php if($showTaskVision){ ?>
                            <a href="javascript:void(0);" id="vision_task" class="btn btn-text btn-blue btn-add" title="<?php __('Vision task') ?>">
                                <p class="line"></p>
                                <p class="line"></p>
                                <span class="wd-hide"><?php __('Vision task') ?></span>
                            </a>
					<?php } ?>
					<?php elseif( !$isMobileOnly ): ?>

						<?php if(!isset($companyConfigs['dispaly_vision_staffing_new']) || $companyConfigs['dispaly_vision_staffing_new'] == 1) { ?>
                            <a href="javascript:void(0);" id="add_vision_staffing_news" class="btn-text" title="<?php __('Vision staffing+') ?>">
                                <i class="icon-eye"></i>
                                <!-- <img src="<?php echo $this->Html->url('/img/ui/blank-vision.png') ?>" alt="" /> -->
                                <span><?php __('Vision staffing+') ?></span>
                            </a>
						<?php } ?>

						<?php if(!isset($companyConfigs['display_vision_portfolio']) || $companyConfigs['display_vision_portfolio'] == 1) { ?>
                            <a href="javascript:void(0);" id="add_vision_portfolio" class="btn-text" title="<?php __('Vision portfolio') ?>">
                                <i class="icon-eye"></i>
								<?php echo $this->Html->url('/img/ui/blank-vision.png') ?>
                                <span><?php __('Vision portfolio') ?></span>
                            </a>
						<?php } ?>

						<?php if(!isset($companyConfigs['display_portfolio']) || $companyConfigs['display_portfolio'] == 1) { ?>
                            <a href="javascript:void(0);" id="add_portfolio" class="btn-text" title="<?php __('Portfolio') ?>">
                                <i class="icon-eye"></i>
								<?php echo $this->Html->url('/img/ui/blank-vision.png') ?>
                                <span><?php __('Portfolio') ?></span>
                            </a>
						<?php } ?>

						<?php if($displayExpectation){ ?>
                            <a href="javascript:void(0);" id="expectation_screen" class="btn btn-text btn-blue btn-add" title="<?php __('Vision Expectation') ?>">
                                <p class="line"></p>
                                <p class="line"></p>
                                <span class="wd-hide"><?php __('Vision Expectation') ?></span>
                            </a>
						<?php } ?>

						<?php if($showTaskVision){ ?>
                            <a href="javascript:void(0);" id="vision_task" class="btn btn-text btn-blue btn-add" title="<?php __('Vision task') ?>">
                                <p class="line"></p>
                                <p class="line"></p>
                                <span class="wd-hide"><?php __('Vision task') ?></span>
                            </a>
						<?php } ?>

						<?php echo $this->element('multiSortHtml'); ?>
                        <!-- End Add old filter -->						

					<?php endif ?>
					<?php if( !empty( $first_commment_column)){ ?>
						<div class="wd-right">
							<?php 
							$viewStone = ( !empty($confirmGantt) && isset($confirmGantt['stones']) ) ? $confirmGantt['stones'] : false;
							$history_height = !empty( $filter_render['comment_column_height']) ? $filter_render['comment_column_height'] : ( $viewStone ? 45 : 40);
							echo $this->Form->input('comment-column-height', array(
								'type' => 'hidden',
								'class' => 'hidden',
								'name' => 'comment_column_height',
								'value' => !empty( $filter_render['comment_column_height']) ? $filter_render['comment_column_height'] : ( $viewStone ? 45 : 40),	
							));
							?>
						</div>
					<?php } ?> 
					<?php if( $enableDashboard){ ?>
						<div class="wd-right">
							<?php echo $this->Form->input('switch-dashboard', array(
								'type' => 'checkbox',
								'class' => 'hidden',
								'label' => '<span class="wd-btn-switch"><span></span></span>',
								'checked'=> !empty($isDisplayDashboard) ? true : false,
								'name' => 'switch-dashboard',
								'div' => array(
									'class' => 'wd-input wd-checkbox-switch wd-switch-dashboard',
									'title' => __('Display widgets',true),
								),
								'onChange' => 'display_dashboard()',
								'type' => 'checkbox', 
							));
							?>
						</div>
					<?php } ?> 
					</div>
					<?php if( $enableDashboard){
						echo $this->element('projects_dashboard', array(
							// 'icons_title' => $icons_title,
							'container_width' => $container_width,
							'projectIds' => $projectIds,
							'sumActivities' => $sumActivities,
							'dash_widgets' => $dash_widgets,
							'dashboard_histories' => $dashboard_histories,
							'multi_dashboard_container' => '.wd-title',
						)); 
					} ?>
					<br clear="all"  />
				</div>
                <div class="wd-panel" style="width: <?php echo $container_width + 50;?>px;" data-w="<?php echo $container_width + 50;?>">
                    <div class="wd-list-project">
						<div class="container-project-list">
							<div id="scrollTopAbsence"><div id="scrollTopAbsenceContent"></div></div>
							<div class="wd-table project-list" id="project_container" rels="<?php echo ($viewGantt && !empty($confirmGantt) && $confirmGantt['stones']) ? 'yes' : 'no';?>">

							</div>
							<div id="pager" style="clear:both;width:100%;height:0px;"></div>
						</div>
                    </div>
                </div>
            </div>
        </div>
		<?php if( $has_upload) { ?>
		<!--  Upload document template -->
		<div id="wd-template-upload" class="wd-full-popup" style="display: none;">
			<div class="wd-popup-inner">
				<div class="template-popup loading-mark wd-popup-container">
					<div class="heading project-name clearfix">
						<h4></h4>
						<a href="javascript:void(0)" class="wd-popup-close pull-right" onclick="cancel_popup(this);"><img title="close" src="<?php echo $this->Html->url('/img/new-icon/close.png');?>"></a>
					</div>
					<div class="template-popup-content wd-popup-content">
						<div class="content-container" style="min-height: 50px">
							<div class="list-file list-document">
								<ul> </ul>
							</div>
							<div class="upload-file upload-document">
								<div class="trigger-upload">
									<form id="upload_documents" method="post" action="<?php echo $this->Html->url(array('controller' => 'projects', 'action' => 'uploads')); ?>" class="wd-dropzone" value="" >
										<input type="hidden" rel="no-history" id="document-project-id" name="data[project_id]" value="">
										<input type="hidden" rel="no-history" id="document-key" name="data[key]" value="">
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!--  END Upload document template -->
		<?php } ?> 
		<?php echo $this->element("comment_popup"); ?>
    </div>
	
</div>
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'projects', 'action' => 'export_project', $viewId)));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<div id="dialog_vision_portfolio" class="buttons" style="display: none;">
    <fieldset>
        <?php echo $this->Form->create('Project', array('type' => 'GET', 'id' => 'form_vision_portfolio', 'url' => array('action' => 'projects_vision', $appstatus))); ?>
        <div style="height:auto;" class="wd-scroll-form">
            <div class="wd-input">
                <label for="program"><?php __d(sprintf($_domain, 'Details'),"Program") ?></label>
                <?php
                echo $this->Form->input('project_amr_program_id', array(
                    'div' => false,
                    'label' => false,
                    "empty" => __("--", true),
                    'name' => 'vision_program',
                    'id' => 'ProjectProjectAmrProgramId_port',
                    'multiple' => true,
                    'hiddenField' => false,
                    "options" => $project_arm_programs
                ));
                ?>
            </div>
            <div class="wd-input">
                <label for="program"><?php __("Sub Program") ?></label>
                <?php
                echo $this->Form->input('project_amr_sub_program_id', array(
                    'div' => false,
                    'label' => false,
                    'style' => 'width:69% !important',
                    'name' => 'vision_sub_program',
                    'id' => 'ProjectProjectAmrSubProgramId_port',
                    'hiddenField' => false,
                    'multiple' => false,
                    'empty' => __('--', true),
                    "class" => "wd-disable",
                    "options" => array()
                ));
                ?>
            </div>
            <div class="wd-input">
                <label for="project-manager"><?php __d(sprintf($_domain, 'Details'),"Project Manager") ?></label>
                <?php
                echo $this->Form->input('project_manager_id', array(
                    'type' => 'select',
                    'name' => 'vision_pm',
                    'id' => 'ProjectProjectManagerId_port',
                    'div' => false,
                    'label' => false,
                    'multiple' => false,
                    'hiddenField' => false,
                    "empty" => __("--", true),
                    'style' => 'width:69% !important',
                    "options" => $projectManagersOption
                ));
                ?>
            </div>
            <div class="wd-input">
                <label for="status"><?php __d(sprintf($_domain, 'Details'), "Status") ?></label>
                <select name="project_status_id" style="margin-right:11px; width:8.8%% !important; padding: 6px;" class="wd-customs" id="ProjectProjectStatusId_port">
                    <option value="1" <?php echo isset($op) ? $op : '';?>><?php echo  __("In progress", true)?></option>
                    <!--option value="2" <?php echo isset($in) ? $in : '';?>><?php echo  __("Opportunity", true)?></option-->
                    <option value="3" <?php echo isset($ar) ? $ar : '';?>><?php echo  __("Archived", true)?></option>
                    <option value="4" <?php echo isset($md) ? $md : '';?>><?php echo  __("Model", true)?></option>
                </select>
            </div>
        </div>
        <?php
        echo $this->Form->end();
        ?>
    </fieldset>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_port"><?php __('OK') ?></a></li>
    </ul>
</div>
<div id="dialog_vision_task" class="buttons" style="display: none;">
    <fieldset>
        <?php echo $this->Form->create('Project', array('type' => 'GET', 'id' => 'form_vision_task')); ?>
        <div class="wd-input" id="" style="overflow: visible">
            <label for="">&nbsp;</label>
            <?php
            echo $this->Form->input('category', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'nameCategory',
                'id' => 'category-id',
                'multiple' => false,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                'type' => 'select',
                'multiple' => true,
                "options" => $cates
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __("Status Of Project") ?></label>
            <?php
            echo $this->Form->input('status_project', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'nameStatusProject',
                'id' => 'statusProject',
                'multiple' => false,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                'type' => 'select',
                'multiple' => true,
                "options" => !empty($projectStatus) ? $projectStatus : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __d(sprintf($_domain, 'Details'), "Program") ?></label>
            <?php
            echo $this->Form->input('program_project', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'nameProgramProject',
                'id' => 'programProject',
                'multiple' => false,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                'type' => 'select',
                'multiple' => true,
                "options" => !empty($projectProgram) ? $projectProgram : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __d(sprintf($_domain, 'Details'), "Sub program") ?></label>
            <?php
            echo $this->Form->input('sub_program_project', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'nameSubProgramProject',
                'id' => 'subProgramProject',
                'multiple' => false,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                'type' => 'select',
                'multiple' => true,
                "options" => !empty($projectSubProgram) ? $projectSubProgram : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __("Status of task") ?></label>
            <?php
            echo $this->Form->input('status_task', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'nameStatusTask',
                'id' => 'statusTask',
                'multiple' => false,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                'type' => 'select',
                'multiple' => true,
                "options" => !empty($projectStatus) ? $projectStatus : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __("Priority of task") ?></label>
            <?php
            echo $this->Form->input('priority_task', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'nameProrityTask',
                'id' => 'prorityTask',
                'multiple' => false,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                'type' => 'select',
                'multiple' => true,
                "options" => !empty($priorities) ? $priorities : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __("Task") ?></label>
            <?php
            echo $this->Form->input('task_project', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'nameTaskProject',
                'id' => 'taskProject',
                'multiple' => false,
                'type' =>'text',
                'hiddenField' => false,
                'style' => 'width: 289px !important; border: 1px solid #aaa;'
                ));
            ?>
        </div>
        <div class="wd-input" id="export-assign-team-div" style="overflow: visible">
            <label for=""><?php __("Assigned Team") ?></label>
            <?php
            echo $this->Form->input('assigned_team', array('div' => false, 'label' => false,
                'type' => 'select',
                'name' => 'assignedTeam',
                'id' => 'export-assign-team',
                'div' => false,
                'multiple' => true,
                'hiddenField' => false,
                'label' => false,
                "empty" => __("-- Any --", true),
                'style' => 'width: 300px !important',
                "options" => !empty($listPc) ? $listPc : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="export-assign-employee-div" style="overflow: visible">
            <label for=""><?php __("Assigned Resources") ?></label>
            <?php
            echo $this->Form->input('assigned_resources', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'assignedResources',
                'id' => 'export-assign-employee',
                'multiple' => true,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                "empty" => __("-- Any --", true),
                "options" => array(),
                "options" => !empty($listEmployee) ? $listEmployee : array(),
                //'selected' => isset($arrGetUrl['team']) ? $arrGetUrl['team'] : array()
                ));
            ?>
        </div>

        <div class="wd-input wd-calendar" id="" style="overflow: visible">
            <label for=""><?php __('Start Date'); ?></label>
            <?php
            echo $this->Form->input('start_date_vision', array('div' => false, 'label' => false,
                'empty' => false,
                'rel' => 'no-history',
                'id' => 'startDateVision',
                'name' => 'nameStartDateVision',
                'style' => 'width: 288px; border: 1px solid #aaa;'
            ));
            ?>
            <span style="display: none; float:left; color: #000; width: 62%" id= "valueStartDate"></span>
        </div>
        <div class="wd-input wd-calendar" id="" style="overflow: visible">
            <label for=""><?php __('End Date'); ?></label>
            <?php
            echo $this->Form->input('end_date_vision', array('div' => false, 'label' => false,
                'empty' => false,
                'rel' => 'no-history',
                'id' => 'endDateVision',
                'name' => 'nameEndDateVision',
                'style' => 'width: 288px; border: 1px solid #aaa;'
            ));
            ?>
            <span style="display: none; float:left; color: #000; width: 62%" id= "valueEndDate"></span>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __d(sprintf($_domain, 'Details'), "Project Code 1") ?></label>
            <?php
            echo $this->Form->input('code_project', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'nameCodeProject',
                'id' => 'codeProject',
                'multiple' => true,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                "options" => !empty($_listProjectCode) ? $_listProjectCode : array(),
                //'selected' => isset($arrGetUrl['team']) ? $arrGetUrl['team'] : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __d(sprintf($_domain, 'Details'), "Project Code 2") ?></label>
            <?php
            echo $this->Form->input('code_project_1', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'nameCodeProject1',
                'id' => 'codeProject1',
                'multiple' => true,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                "options" => !empty($_listProjectCode1) ? $_listProjectCode1 : array(),
                //'selected' => isset($arrGetUrl['team']) ? $arrGetUrl['team'] : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label><?php echo __("Milestone", true) ?></label>
            <?php
            echo $this->Form->input('milestone', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'nameMilestone',
                'id' => 'milestone',
                'multiple' => true,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                "options" => !empty($_milestone) ? $_milestone : array(),
                ));
            ?>
        </div>
        <?php echo $this->Form->end(); ?>
    </fieldset>
    <div style="clear: both;"></div>
    <ul class=" type_buttons" style="padding-right: 10px !important">
        <p id="show_count_task"></p>
        <li><a href="javascript:void(0)" class="cancel"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="full_screen" id="full_screen_vision" title="<?php __('Full Screen')?>"><span><?php __('Full Screen') ?></span></a></li>
        <li><a href="javascript:void(0)" class="export" id="ok_sum_vision" title="<?php __('Export Excel')?>"><span><?php __('Export Excel') ?></span></a></li>
        <li><a href="javascript:void(0)" class="cancel reset" id="reset_sum_team"><?php __('RESET') ?></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_export_file_team" style="display: none;"><?php __('OK') ?></a></li>
    </ul>
</div>
<div id="dialog_vision_expectation" class="buttons" style="display: none;">
    <fieldset>
        <?php echo $this->Form->create('Project', array('type' => 'GET', 'id' => 'form_vision_expectation')); ?>
        <div class="wd-input" id="" style="overflow: visible">
            <label for="">&nbsp;</label>
            <?php
            echo $this->Form->input('category', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'cateExpec',
                'id' => 'CatePExpec',
                'multiple' => false,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                'type' => 'select',
                'multiple' => true,
                "options" => $cates
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __("Status Of Project") ?></label>
            <?php
            echo $this->Form->input('status_project', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'statusExpec',
                'id' => 'StatusPExpec',
                'multiple' => false,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                'type' => 'select',
                'multiple' => true,
                "options" => !empty($projectStatus) ? $projectStatus : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __d(sprintf($_domain, 'Details'), "Program") ?></label>
            <?php
            echo $this->Form->input('program_project', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'programExpec',
                'id' => 'ProgramPExpec',
                'multiple' => false,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                'type' => 'select',
                'multiple' => true,
                "options" => !empty($projectProgram) ? $projectProgram : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __d(sprintf($_domain, 'Details'), "Sub program") ?></label>
            <?php
            echo $this->Form->input('sub_program_project', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'subproExpec',
                'id' => 'SubProgramPExpec',
                'multiple' => false,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                'type' => 'select',
                'multiple' => true,
                "options" => !empty($projectSubProgram) ? $projectSubProgram : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __("Expectations") ?></label>
            <?php
            echo $this->Form->input('expectations', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'nameExpec',
                'id' => 'nameExpec',
                'multiple' => false,
                'type' =>'text',
                'hiddenField' => false,
                'style' => 'width: 289px !important; border: 1px solid #aaa;'
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __("Assigned Team") ?></label>
            <?php
            echo $this->Form->input('assigned_team', array('div' => false, 'label' => false,
                'type' => 'select',
                'name' => 'assignedTeam',
                'id' => 'AssignTeamExpec',
                'div' => false,
                'multiple' => true,
                'hiddenField' => false,
                'label' => false,
                "empty" => __("-- Any --", true),
                'style' => 'width: 300px !important',
                "options" => !empty($listPc) ? $listPc : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __("Assigned Resources") ?></label>
            <?php
            echo $this->Form->input('assigned_resources', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'assignedResources',
                'id' => 'AssignRessourceExpec',
                'multiple' => true,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                "empty" => __("-- Any --", true),
                "options" => array(),
                "options" => !empty($listEmployee) ? $listEmployee : array(),
                //'selected' => isset($arrGetUrl['team']) ? $arrGetUrl['team'] : array()
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __("Screen") ?></label>
            <?php
            echo $this->Form->input('screen', array('div' => false, 'label' => false,
                "empty" => false,
                'name' => 'screen',
                'id' => 'ScreenExpec',
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                'type' => 'select',
                // 'multiple' => true,
                "options" => !empty($screenExpec) ? $screenExpec : array()
                ));
            ?>
        </div>
        <div class="wd-input wd-calendar" id="" style="overflow: visible">
            <label for=""><?php __('Start Date'); ?></label>
            <?php
            echo $this->Form->input('start_date_expec', array('div' => false, 'label' => false,
                'empty' => false,
                'rel' => 'no-history',
                'id' => 'startDateExpec',
                'name' => 'start',
                'style' => 'width: 288px; border: 1px solid #aaa;'
            ));
            ?>
            <span style="display: none; float:left; color: #000; width: 62%" id= "valueStartDate"></span>
        </div>
        <div class="wd-input wd-calendar" id="" style="overflow: visible">
            <label for=""><?php __('End Date'); ?></label>
            <?php
            echo $this->Form->input('end_date_expec', array('div' => false, 'label' => false,
                'empty' => false,
                'rel' => 'no-history',
                'id' => 'endDateExpec',
                'name' => 'end',
                'style' => 'width: 288px; border: 1px solid #aaa;'
            ));
            ?>
            <span style="display: none; float:left; color: #000; width: 62%" id= "valueEndDate"></span>
        </div>
        <?php echo $this->Form->end(); ?>
    </fieldset>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="full_screen" id="full_screen_expectation" title="<?php __('Full Screen')?>"><span><?php __('Full Screen') ?></span></a></li>
        <!-- <li><a href="javascript:void(0)" class="export" id="ok_sum_vision" title="<?php __('Export Excel')?>"><span><?php __('Export Excel') ?></span></a></li> -->
        <li><a href="javascript:void(0)" class="cancel reset" id="reset_vision_expec"><?php __('RESET') ?></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_export_file_team" style="display: none;"><?php __('OK') ?></a></li>
    </ul>
</div>
<div id="contextMenu-project" style="display: none;"></div>
<div id='modal_dialog_confirm' style="display: none">
    <div class='title'></div>
    <input class="ok-new-project" type='button' value='<?php echo __('OK', true) ?>' id='btnYes' />
    <input class="cancel-new-project" type='button' value='<?php echo __('Cancel', true) ?>' id='btnNo' />
</div>
<div id="template_logs" style="height: 420px; width: 320px;display: none;">
    <div class="add-comment"></div>
    <div id="content_comment" style="min-height: 50px">
        <div class="append-comment"></div>
    </div>

</div>
<?php
$text_modified = __('Modified', true);
$i18ns = array(
	'add_favorite' => __('Add favorite', true),
	'remove_favorite' => __('Remove favorite', true),
	'project_used' => __('Cannot be deleted, Project with consumed or used in timesheet', true),
);

$text_by = __('by', true);

$_linkDashboard = '';
if( !empty($screenDashboard)){ 
	foreach($screenDashboard as $screen => $screen_val){
		$_linkDashboard = array();
		$_lang = $employee_info['Employee']['language'];
		$_title = ($_lang == 'fr') ? $screen_val['name_fre'] : (($_lang == 'en') ? $screen_val['name_eng'] :  __('Dashboard', true));
		$_linkDashboard['link'] =  $html->url(array('controller' => $screen_val['controllers'], 'action' => $screen_val['functions']));
		$_linkDashboard['title'] =  $_title;
		if($screen == 'indicator') break;
	}
}
$text_widget = array(
	'Phase' => array(
		'left' => __('Real end date', true),
		'right' => __('Plan end date', true),
	),
	'Milestone' => array(
		'left' => __('Late milestone', true),
		'right' => __('Next milestone', true),
	),
	'FinancePlus' => array(
		'left' => __d(sprintf($_domain, 'Finance'), 'Budget', true),
		'right' => __d(sprintf($_domain, 'Finance'), 'Engaged', true),
	),
	'Synthesis' => array(
		'left' => __d(sprintf($_domain, 'Total_Cost'), 'Budget €', true),
		'right' => __d(sprintf($_domain, 'Total_Cost'), 'Forecast €', true),
	)
);

?>
<div class="widget-template" style="display: none;">
	<div class="wd-widget-in-cell loading-mark">
		<div class="wd-widget-compare">
			<div class="wd-value-inner clearfix">
				<div class="wd-value-content left hidden">
					<p class="wg-label">%LeftText%</p>
					<span class="wg-value">%LeftVal%</span> 	
				</div>
				<div class="wd-value-content right hidden">
					<p class="wg-label">%RightText%</p> 
					<span class="wg-value">%RightVal%</span><span class="wd-value-total hidden">%totalVal%</span> 	
				</div>
			</div>
		</div>
		<div class="wd-widget-progress">
			<div class="wd-progress-slider">
				<div class="wd-progress-holder">
					<div class="wd-progress-line-holder"></div>
				</div>
				<div class="wd-progress-value-line" style="width:100%;"></div>
				<div class="wd-progress-value-text">
					<div class="wd-progress-value-inner">
						<div class="wd-progress-number" style="left:100%;">
							<div class="text">100%</div> 
							<input class="input-progress wd-hide" value="100">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
 </div>
 <div id="jqx-format-tooltip-in" style="display: none; height: 50px">
	<ul class="content-tooltip">
		<li class="budget"><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Budget €', true); ?>: <span>{budget} <?php echo $budget_settings;?></span></li>
		<li class="forecast"><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Forecast €', true); ?>: <span>{forecast} <?php echo $budget_settings;?></span></li>
		<li class="consumed"><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Engaged €', true); ?>: <span>{consumed} <?php echo $budget_settings;?></span></li>
	</ul>
	<span class="arrow"></span>
</div>

 <div id="jqx-format-tooltip-in-md" style="display: none; height: 50px">
	<ul class="content-tooltip">
		<li class="budget"><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Budget MD', true); ?>: <span>{budget} <?php echo $i18n['M.D'];?></span></li>
		<li class="forecast"><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Forecast MD', true); ?>: <span>{forecast} <?php echo $i18n['M.D'];?></span></li>
		<li class="consumed"><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Engaged MD', true); ?>: <span>{consumed} <?php echo $i18n['M.D'];?></span></li>
	</ul>
	<span class="arrow"></span>
</div>

<div id="jqx-format-tooltip-ex" style="display: none; height: 50px">
	<ul class="content-tooltip">
		<li class="budget"><?php echo __d(sprintf($_domain, 'External_Cost'), 'Budget €', true); ?>: <span>{budget} <?php echo $budget_settings;?></span></li>
		<li class="forecast"><?php echo __d(sprintf($_domain, 'External_Cost'), 'Forecast €', true); ?>: <span>{forecast} <?php echo $budget_settings;?></span></li>
		<li class="ordered consumed"><?php echo __d(sprintf($_domain, 'External_Cost'), 'Ordered €', true); ?>: <span>{consumed} <?php echo $budget_settings;?></span></li>
	</ul>
	<span class="arrow"></span>
</div>

<div id="jqx-format-tooltip-synth" style="display: none; height: 50px">
	<ul class="content-tooltip">
		<li class="budget"><?php echo __d(sprintf($_domain, 'Total_Cost'), 'Budget €', true); ?>: <span>{budget} <?php echo $budget_settings;?></span></li>
		<li class="forecast"><?php echo __d(sprintf($_domain, 'Total_Cost'), 'Forecast €', true); ?>: <span>{forecast} <?php echo $budget_settings;?></span></li>
		<li class="ordered consumed"><?php echo __d(sprintf($_domain, 'Total_Cost'), 'Engaged €', true); ?>: <span>{consumed} <?php echo $budget_settings;?></span></li>
	</ul>
	<span class="arrow"></span>
</div>
<style id="css_custom_height">
<?php if( !empty($first_commment_column)){
	?>
	#project_container .slick-viewport .grid-canvas .slick-row .slick-cell{
		height: <?php echo $history_height;?>px;
		line-height:<?php echo $history_height;?>px;
	}
	#project_container .slick-cell.wd-open-popup .comment-text .comment{
		max-height: <?php echo $history_height-18;?>px;
	}
<?php } ?> 
</style>
<script type="text/javascript">
    // var first = 0;
	$this = SlickGridCustom;   
	dashboard_timeout = 2000;	
    var mx = 'J F M A M J J A S O N D'.split(' ');
    var DataValidator = {};
    var ControlGrid;
    var svg_icons = <?php echo json_encode($svg_icons);?>;
    var projectIds = <?php echo json_encode($projectIds);?>;
    // var projectProgress = < ?php echo json_encode($projectProgress);?>;
    var projectStatus = <?php echo json_encode($projectStatus);?>;
    var icons_title = <?php echo json_encode($icons_title);?>;
    var i18ns = <?php echo json_encode($i18ns);?>;
    var month_i18n = <?php echo json_encode($month_i18n);?>;
	var synt_i18ns = <?php echo json_encode($synt_i18ns); ?>;
    var columnsOfGantt = <?php echo json_encode($columnsOfGantt);?>;
    var today = new Date('<?php echo date('Y-m-d') ?>');
    var viewGantt = <?php echo json_encode($viewGantt);?>;
    var listPriorities = <?php echo json_encode($priorities) ?>;
    var listProjectType = <?php echo json_encode($listprojectTypes) ?>;
    var listProjectSubType = <?php echo json_encode($listprojectSubTypes) ?>;
    var listProjectSubSubType = <?php echo json_encode($listprojectSubSubTypes) ?>;
    var projectProgram = <?php echo json_encode($projectProgram) ?>;
    var projectSubProgram = <?php echo json_encode($projectSubProgram) ?>;
	var budget_settings = <?php echo json_encode($budget_settings); ?>;
    var utility = {};
    var _linkEdit = <?php echo json_encode($html->url('/' . $ACLController . '/' . $ACLAction));?>;
    var _linkDashboard = <?php echo json_encode($_linkDashboard);?>;
    var _linkKanban = ''; //<?php echo json_encode($html->url('/kanban/index'));?>;
    var _linkAdmin = <?php echo json_encode($html->url('/project_phases/'));?>;
    var _linkDelete = <?php echo json_encode($html->url('/projects/delete'));?>;
    var controller = 'projects';
    var update_your_form = <?php echo json_encode($employee['Employee']['update_your_form']) ?>;
    var savePosition = <?php echo $savePosition ?>;
    var listEmployeeManager = <?php echo json_encode($listEmployeeManager) ?>;
    var employee_id = <?php echo json_encode($employee_id) ?>;
    var $checkDisplayProfileScreen = <?php echo json_encode($checkDisplayProfileScreen) ?>;
    var text_by = <?php echo json_encode($text_by) ?>;
    var text_modified = <?php echo json_encode($text_modified) ?>;
    var isTablet = <?php echo json_encode($isTablet) ?>;
    var isMobile = <?php echo json_encode($isMobile) ?>;
    var list_avatar = <?php echo json_encode( $list_avatar); ?>;
    var _uat_manager_list = <?php echo json_encode( $_uat_manager_list); ?>;
    var _technical_manager_list = <?php echo json_encode( $_technical_manager_list); ?>;
    var _functional_leader_list = <?php echo json_encode( $_functional_leader_list); ?>;
    var _chief_business_list = <?php echo json_encode( $_chief_business_list); ?>;
    var project_read_access_manager_list = <?php echo json_encode( $project_read_access_manager_list); ?>;
    var i18n = <?php echo json_encode($i18n); ?>;
    var list_cate = <?php echo json_encode($list_cate); ?>;
    var appstatus = <?php echo json_encode($appstatus); ?>;
    appstatus = (appstatus in list_cate) ? appstatus : 1;
    var default_category = appstatus;
    var listProjectOfPM = <?php echo json_encode($listProjectOfPM); ?>;
    var _count_project = <?php echo json_encode($_count_project); ?>;
    var has_upload = <?php echo json_encode($has_upload); ?>;
    var text_widget = <?php echo json_encode($text_widget); ?>;
    var filter_render = <?php echo json_encode($filter_render); ?>;
    var first_commment_column = <?php echo json_encode($first_commment_column); ?>;
    var first_resize_column_run = 0;
    var labelSummaryHeader = <?php echo json_encode($labelSummaryHeader); ?>;
    var isFilterFavorite = <?php echo json_encode($isFilterFavorite); ?>;
    var isFilterAlert = <?php echo json_encode($isFilterAlert); ?>;
    var enableDashboard = <?php echo !empty($enableDashboard) ? 1 : 0 ?>;
    var isDisplayDashboard = <?php echo !empty($isDisplayDashboard) ? 1 : 0 ?>;
    var cate = <?php echo json_encode($cate); ?>;
    // var projectPhasePlans = < ?php echo json_encode($projectPhasePlans); ?>;
    var enableWidgets = <?php echo json_encode($enableWidgets); ?>;
    var typeTaskStatus = <?php echo json_encode($typeTaskStatus);?>;
	var month_scroll = <?php echo json_encode(date('M-Y', time()));?>;
	var layout_for_print = false;
	
	var canModified = <?php echo json_encode($canModified); ?>;
	var companyConfigs = <?php echo json_encode($companyConfigs); ?>;
	var showProgress = <?php echo json_encode($showProgress); ?>;
	var favorites = <?php echo json_encode($favorites); ?>;
	var curentYear = <?php echo json_encode(date('Y', time())); ?>;
	var CustomFilter = {
		display : {},
		filter : {}		
	};
	$('body').find('.fancy.image').fancybox({
	   type: 'image'
	});
	function sortObjectByNumber(data){
		if(typeof data == "object"){
			var sortable = [];
			var i = 0;
		    var result = {};
			for (var key in data) {
				if(key != 'sum'){
					sortable[i++] = data[key];
				}
			}
			sortable.sort(function(a, b) {
				if(a['count'] && b['count']){
					var x = a['count'];		
					var y = b['count'];		
					return x < y ? 1 : x > y ? -1 : 0;
				}
				if( a ) return 1;
				if( b ) return -1;
				return 0;
			});
			return sortable;
		}
		return data;
	}
	// function get_data_budget_finance_plus(){
		// var dataLength = ControlGrid.getDataLength();
		// var dataView = ControlGrid.getDataView();
		// var res = [];
		// var result = [];
		// for( var i=0; i< dataLength; i++){
			// var project_item = dataView.getItem(i);
			// project_line = project_item['DataSet']['finance_progress'];
			
			// $.each( project_line , function( key, data){
				// for (var year in data) {
					// if(typeof res[year] === 'undefined') res[year] = {};
					// if(typeof res[year][key] === 'undefined') res[year][key] = 0;
					// res[year]['year'] = year;
					// res[year][key] += parseFloat(data[year]);
				// }
			// });
			
		// }
		// return res;
		
	// }
	if( enableDashboard ){
		Dashboard.project_ids = [];
		$.each(projectIds, function(k, id){
			Dashboard.project_ids.push(id);
		});
		Dashboard.get_displayed_projects = function(){
			if( !ControlGrid) return Dashboard.project_ids;
			Dashboard.project_ids = [];
			var dataLength = ControlGrid.getDataLength();
			var dataView = ControlGrid.getDataView();
			var ids = [];
			for( var i=0; i< dataLength; i++){
				var item = dataView.getItem(i);
				ids.push(item.id);
			}
			Dashboard.set('project_ids',ids);
			return ids;
		}
	}
	/*
	
	Dashboard.project_ids = [];
	$.each(projectIds, function(k, id){
		Dashboard.project_ids.push(id);
	});
	Dashboard.get_list_projects = function() {
		if( !ControlGrid) return {};
		var dataLength = ControlGrid.getDataLength();
		var dataView = ControlGrid.getDataView();
		var ids = [];
		for( var i=0; i< dataLength; i++){
			var item = dataView.getItem(i);
			ids.push(item.id);
		}
		return ids;
	}
	Dashboard.get_data_dash_late_milestones = function() {
		if( !ControlGrid) return {};
		var dataLength = ControlGrid.getDataLength();
		var dataView = ControlGrid.getDataView();
		var res = [];
		for( var i=0; i< dataLength; i++){
			var item = dataView.getItem(i);
			if( item.DataSet.Milestone_late && item.DataSet.Milestone_late.date){
				var data = item.DataSet.Milestone_late.value;
				data['project_id'] = item.id;
				data['project_name'] = item['Project.project_name'];
				data['timestamp_date'] = item.DataSet.Milestone_late.date;
				res.push(data);
			}
		}
		var data = $.orderObjectBySpecialKey( res, 'timestamp_date', 'number', true);
		return data;
	}
	Dashboard.get_data_dash_phase = function() {
		if( !ControlGrid) return {};
		var dataLength = ControlGrid.getDataLength();
		var dataView = ControlGrid.getDataView();
		var res = {
			content: {
				title: enableWidgets['phase'],
				plan:  SlickGridCustom.t('plan'),
				real:  SlickGridCustom.t('real'),
				plan_end_date:  SlickGridCustom.t('plan_end_date'),
				real_end_date:  SlickGridCustom.t('real_end_date'),
				plan_start_date:  SlickGridCustom.t('plan_start_date'),
				real_start_date:  SlickGridCustom.t('real_start_date'),
				late_project:  SlickGridCustom.t('late_project'),
				min_start_date_plan: '',
				min_start_date_real: '',
				max_end_date_plan: '',
				max_end_date_real: '',
				phase_diff: '',
				late: 0
			},
			data: []
		};
		var min_start_date_plan = min_start_date_real = max_end_date_plan = max_end_date_real = "0";
		var min_plan_id = min_real_id = max_plan_id = max_real_id = 0;
		var data = [];
		for( var i=0; i< dataLength; i++){
			var project_item = dataView.getItem(i);
			var item = [];
			var p_id = project_item['id'];
			item['project_id'] = p_id;
			item['project_name'] = project_item['Project.project_name'];
			item['end_date_plan'] = project_item['ProjectWidget.Phase_plan'];
			item['end_date_real'] = project_item['ProjectWidget.Phase_real'];
			
			item['phase_end_diff'] = project_item['ProjectWidget.Phase_diff'];
			item['project_progress'] = project_item['ProjectWidget.Phase_progress'];
			item['start_date_plan'] = '';
			item['start_date_real'] = '';
			if( p_id in projectPhasePlans){
				p = projectPhasePlans[p_id];
				item['start_date_plan'] = p['min_start_date_plan'];
				item['start_date_real'] = p['min_start_date_real'];

				if( (min_start_date_plan == "0") || (min_start_date_plan > p['MinStartDatePlan'] )){
					min_start_date_plan = p['MinStartDatePlan'];
					min_plan_id = p_id;
				}
				if(( min_start_date_real == "0") || (min_start_date_real > p['MinStartDateReal'] )){
					min_start_date_real = p['MinStartDateReal'];
					min_real_id = p_id;
				}
				if( max_end_date_plan < p['MaxEndDatePlan'] ){
					max_end_date_plan = p['MaxEndDatePlan'];
					max_plan_id = p_id;
				}
				if( max_end_date_real < p['MaxEndDateReal'] ){
					max_end_date_real = p['MaxEndDateReal'];
					max_real_id = p_id;
				}
			}
			data.push(item);
		}
		res.data = $.orderObjectBySpecialKey(data, 'phase_end_diff', 'number', false);
		if( min_plan_id ) res.content.min_start_date_plan = projectPhasePlans[min_plan_id]['min_start_date_plan'];
		if( min_real_id ) res.content.min_start_date_real = projectPhasePlans[min_real_id]['min_start_date_real'];
		if( max_plan_id ) res.content.max_end_date_plan = projectPhasePlans[max_plan_id]['max_end_date_plan'];
		if( max_real_id ) {
			res.content.max_end_date_real = projectPhasePlans[max_real_id]['max_end_date_real'];
			max_end_date_real = new Date(max_end_date_real);
			max_end_date_plan = new Date(max_end_date_plan);
			var diff_date = max_end_date_real - max_end_date_plan;
			diff_date /= ( 1000 * 3600 * 24);
			res.content.phase_diff = diff_date + SlickGridCustom.t('d');
			if(i = 1){
				$.each( res.data, function( i, pr_data){
					res.content.phase_diff = pr_data.phase_end_diff;
				});
			}
			if(max_end_date_real > max_end_date_plan) res.content.late = 1;
		}
		return res;
	}
	Dashboard.get_data_dash_financeplusinv = function() {
		if( !ControlGrid) return {};
		var result = [];
		var res = get_data_budget_finance_plus();
		var n = 0;
		progress_inv_max = 0;
		for (var year in res) {
			result[n++] = res[year];
			if(typeof res[year]['inv_avancement'] === 'undefined') res[year]['inv_avancement'] = 0;
			if(typeof res[year]['inv_budget'] === 'undefined') res[year]['inv_budget'] = 0;
			progress_inv_max = Math.max(res[year]['inv_avancement'],res[year]['inv_budget'], progress_inv_max);
		}
		return result;
	}
	Dashboard.get_data_dash_financeplusfinaninv = function() {
		if( !ControlGrid) return {};
		var result = [];
		var res = get_data_budget_finance_plus();
		var n = 0;
		progress_finaninv_max = 0;
		for (var year in res) {
			result[n++] = res[year];
			if(typeof res[year]['finaninv_budget'] === 'undefined') res[year]['finaninv_budget'] = 0;
			if(typeof res[year]['finaninv_avancement'] === 'undefined') res[year]['finaninv_avancement'] = 0;
			progress_finaninv_max = Math.max(res[year]['finaninv_budget'], res[year]['finaninv_avancement'], progress_finaninv_max);
		}
		return result;
	}
	Dashboard.get_data_dash_financeplusfon  = function() {
		if( !ControlGrid) return {};
		var result = [];
		var res = get_data_budget_finance_plus();
		var n = 0;
		progress_fon_max = 0;
		for (var year in res) {
			result[n++] = res[year];
			if(typeof res[year]['fon_avancement'] === 'undefined') res[year]['fon_avancement'] = 0;
			if(typeof res[year]['fon_budget'] === 'undefined') res[year]['fon_budget'] = 0;
			progress_fon_max = Math.max(res[year]['fon_avancement'],res[year]['fon_budget'], progress_fon_max);
		}
		return result;
	}
	Dashboard.get_data_dash_financeplusfinanfon  = function() {
		if( !ControlGrid) return {};
		var result = [];
		var res = get_data_budget_finance_plus();
		var n = 0;
		progress_finanfon_max = 0;
		for (var year in res) {
			result[n++] = res[year];
			if(typeof res[year]['finanfon_avancement'] === 'undefined') res[year]['finanfon_avancement'] = 0;
			if(typeof res[year]['finanfon_budget'] === 'undefined') res[year]['finanfon_budget'] = 0;
			progress_finanfon_max = Math.max(res[year]['finanfon_avancement'],res[year]['finanfon_budget'], progress_finanfon_max);
		}
		return result;
	}
	Dashboard.get_data_dash_progress_all_tasks = function() {
		if( !ControlGrid) return {};
		var dataLength = ControlGrid.getDataLength();
		var dataView = ControlGrid.getDataView();
		var res = {
			count_status: 0,
			task_status: {},
		};
		for( var i=0; i< dataLength; i++){
			var item = dataView.getItem(i);
			if( item.DataSet.summary_task) {
				var data = item.DataSet.summary_task;
				if( data.count_by_stt){
					$.each( data.count_by_stt, function( stt, count){
						res['count_status'] += count;
						if( !( stt in res['task_status'])) res['task_status'][stt] = 0;
						res['task_status'][stt] += count;
					});
				}
			}
		}
		return res;
	}
	Dashboard.get_data_dash_internalbudgeteuro = function() {
		if( !ControlGrid) return {};
		var res = [];
		res['budget_euro'] = 0;
		res['forecast_euro'] = 0;
		res['consumed_euro'] = 0;
		res['count_project'] = 0;
		res['count_project_exceed_budget'] = 0;
		res['progress_line'] = [];
		var dataLength = ControlGrid.getDataLength();
		var dataView = ControlGrid.getDataView();
		var sum_line = [];
		for( var i=0; i< dataLength; i++){
			var project_item = dataView.getItem(i);
			var item = [];
			res['count_project'] += 1;
			res['budget_euro'] += project_item['ProjectWidget.Internal_budget_euro'];
			res['forecast_euro'] += project_item['ProjectWidget.Internal_forecast_euro'];
			res['consumed_euro'] += project_item['ProjectWidget.Internal_engaged_euro'];
			project_line = project_item['DataSet']['internal_progress'];
			if(project_item['ProjectWidget.Internal_forecast_euro'] > project_item['ProjectWidget.Internal_budget_euro']){
				res['count_project_exceed_budget'] += 1;
			}else{
				res['count_project_exceed_budget'] += 0;
			}
			$.each( project_line , function( index, data){
				_date = data['date'];
				if(typeof _date !== 'undefined') {
					// _date = 'd_' + _date;
					if(typeof sum_line[_date] === 'undefined')sum_line[_date] = {};
					sum_line[_date]['date'] = _date;
					sum_line[_date]['date_format'] = data['date_format'];
					budget_price = data['budget_price'] ? data['budget_price'] : 0;
					forecast_price = data['validated_price'] ? data['validated_price'] : 0;
					consumed_price = data['consumed_price'] ? data['consumed_price'] : 0;
					if(typeof sum_line[_date]['budget_price'] === 'undefined') sum_line[_date]['budget_price'] = 0;
					if(typeof sum_line[_date]['validated_price'] === 'undefined') sum_line[_date]['validated_price'] = 0;
					if(typeof sum_line[_date]['consumed_price'] === 'undefined') sum_line[_date]['consumed_price'] = 0;
					sum_line[_date]['budget_price'] += parseFloat(budget_price);
					sum_line[_date]['validated_price'] += parseFloat(forecast_price);
					sum_line[_date]['consumed_price'] += parseFloat(consumed_price);
				}
			});
		}
		var i = 0;
		progress_euro_max = 0;
		internal_chart_fields = ['budget_price', 'validated_price', 'consumed_price'];
		for (var key in sum_line) {
			if(i == 0) res['progress_line'][i] = sum_line[key];
			else{
				var j = i - 1;
				if(typeof res['progress_line'][i] === 'undefined') res['progress_line'][i] = {};
				res['progress_line'][i]['date'] = sum_line[key]['date'];
				res['progress_line'][i]['date_format'] = sum_line[key]['date_format'];
				res['progress_line'][i]['budget_price'] = sum_line[key]['budget_price'];
				res['progress_line'][i]['validated_price'] = sum_line[key]['validated_price'];
				res['progress_line'][i]['consumed_price'] =  sum_line[key]['consumed_price'];
				progress_euro_max = Math.max(sum_line[key]['budget_price'],sum_line[key]['validated_price'], sum_line[key]['consumed_price'], progress_euro_max);
			}
			i++;
			
		};
		progress_euro_max =  Math.round( progress_euro_max * 1.1, 2);
		res['percent_consumed_euro'] = calculatePercent(res['consumed_euro'], res['forecast_euro']);
		res['percent_forecast_euro'] = calculatePercent(res['forecast_euro'], res['budget_euro']);
		res['budget_euro'] = number_format(res['budget_euro'], 2, ',', ' ') + ' ' + budget_settings;
		res['forecast_euro'] = number_format(res['forecast_euro'], 2, ',', ' ') + ' ' + budget_settings;
		res['consumed_euro'] = number_format(res['consumed_euro'], 2, ',', ' ') + ' ' + budget_settings;
		return res;
	}
	Dashboard.get_data_dash_internalbudgetmd = function() {
		if( !ControlGrid) return {};
		var res = [];
		res['budget_md'] = 0;
		res['forecast_md'] = 0;
		res['consumed_md'] = 0;
		res['count_project'] = 0;
		res['count_project_exceed_budget'] = 0;
		res['progress_line'] = [];
		var dataLength = ControlGrid.getDataLength();
		var dataView = ControlGrid.getDataView();
		var sum_line = [];
		for( var i=0; i< dataLength; i++){
			var project_item = dataView.getItem(i);
			var item = [];
			res['count_project'] += 1;
			res['budget_md'] += project_item['ProjectWidget.Internal_budget_md'];
			res['forecast_md'] += project_item['ProjectWidget.Internal_forecast_md'];
			res['consumed_md'] += project_item['ProjectWidget.Internal_consumed_md'];
			project_line = project_item['DataSet']['internal_progress'];
			if(project_item['ProjectWidget.Internal_forecast_md'] > project_item['ProjectWidget.Internal_budget_md']){
				res['count_project_exceed_budget'] += 1;
			}else{
				res['count_project_exceed_budget'] += 0;
			}
			$.each( project_line , function( index, data){
				_date = data['date'];
				if(typeof _date !== 'undefined') {
					// _date = 'd_' + _date;
					if(typeof sum_line[_date] === 'undefined')sum_line[_date] = {};
					sum_line[_date]['date'] = _date;
					sum_line[_date]['date_format'] = data['date_format'];
					budget_price = data['budget_md'] ? data['budget_md'] : 0;
					forecast_price = data['validated'] ? data['validated'] : 0;
					consumed_price = data['consumed'] ? data['consumed'] : 0;
					if(typeof sum_line[_date]['budget_md'] === 'undefined') sum_line[_date]['budget_md'] = 0;
					if(typeof sum_line[_date]['validated'] === 'undefined') sum_line[_date]['validated'] = 0;
					if(typeof sum_line[_date]['consumed'] === 'undefined') sum_line[_date]['consumed'] = 0;
					sum_line[_date]['budget_md'] += parseFloat(budget_price);
					sum_line[_date]['validated'] += parseFloat(forecast_price);
					sum_line[_date]['consumed'] += parseFloat(consumed_price);
				}
			});
		}
		var i = 0;
		progress_md_max = 0;
		internalMD_chart_fields = ['budget_md', 'validated', 'consumed'];
		for (var key in sum_line) {
			if(i == 0) res['progress_line'][i] = sum_line[key];
			else{
				var j = i - 1;
				if(typeof res['progress_line'][i] === 'undefined') res['progress_line'][i] = {};
				res['progress_line'][i]['date'] = sum_line[key]['date'];
				res['progress_line'][i]['date_format'] = sum_line[key]['date_format'];
				res['progress_line'][i]['budget_md'] = sum_line[key]['budget_md'];
				res['progress_line'][i]['validated'] = sum_line[key]['validated'];
				res['progress_line'][i]['consumed'] =  sum_line[key]['consumed'];
				progress_md_max = Math.max(sum_line[key]['budget_md'],sum_line[key]['validated'], sum_line[key]['consumed'], progress_md_max);
			}
			i++;
			
		};
		progress_euro_max =  Math.round( progress_euro_max * 1.2, 2);
		res['percent_consumed_md'] = calculatePercent(res['consumed_md'], res['forecast_md']);
		res['percent_forecast_md'] = calculatePercent(res['forecast_md'], res['budget_md']);
		res['budget_md'] = number_format(res['budget_md'], 2, ',', ' ') + ' ' + $this.t('M.D');
		res['forecast_md'] = number_format(res['forecast_md'], 2, ',', ' ') + ' ' + $this.t('M.D');
		res['consumed_md'] = number_format(res['consumed_md'], 2, ',', ' ') + ' ' + $this.t('M.D');
		return res;
	}
	Dashboard.get_data_dash_externalbudget = function() {
		if( !ControlGrid) return {};
		var res = [];
		res['budget_euro'] = 0;
		res['forecast_euro'] = 0;
		res['ordered_euro'] = 0;
		res['count_project'] = 0;
		res['count_project_exceed_budget'] = 0;
		res['progress_line'] = [];
		var dataLength = ControlGrid.getDataLength();
		var dataView = ControlGrid.getDataView();
		var sum_line = [];
		for( var i=0; i< dataLength; i++){
			var project_item = dataView.getItem(i);
			var item = [];
			res['count_project'] += 1;
			res['budget_euro'] += project_item['ProjectWidget.External_budget_erro'];
			res['forecast_euro'] += project_item['ProjectWidget.External_forecast_erro'];
			res['ordered_euro'] += project_item['ProjectWidget.External_ordered_erro'];
			project_line = project_item['DataSet']['external_progress'];
			if(project_item['ProjectWidget.External_forecast_erro'] > project_item['ProjectWidget.External_budget_erro']){
				res['count_project_exceed_budget'] += 1;
			}else{
				res['count_project_exceed_budget'] += 0;
			}
			for (var key in project_line) {
				data = project_line[key];
				_date = data['date'];
				if(typeof _date !== 'undefined') {
					// _date = 'd_' + _date;
					if(typeof sum_line[_date] === 'undefined')sum_line[_date] = {};
					sum_line[_date]['date'] = _date;
					sum_line[_date]['date_format'] = data['date_format'];
					budget_price = data['budget'] ? data['budget'] : 0;
					forecast_price = data['forecast'] ? data['forecast'] : 0;
					ordered_price = data['ordered'] ? data['ordered'] : 0;
					if(typeof sum_line[_date]['budget_price'] === 'undefined') sum_line[_date]['budget_price'] = 0;
					if(typeof sum_line[_date]['forecast_price'] === 'undefined') sum_line[_date]['forecast_price'] = 0;
					if(typeof sum_line[_date]['ordered_price'] === 'undefined') sum_line[_date]['ordered_price'] = 0;
					sum_line[_date]['budget_price'] += parseFloat(budget_price);
					sum_line[_date]['forecast_price'] += parseFloat(forecast_price);
					sum_line[_date]['ordered_price'] += parseFloat(ordered_price);
				}
			}
		}
		external_chart_fields = ['budget_price', 'forecast_price', 'ordered_price'];
		var i = 0;
		progress_external_euro_max = 0;
		for (var key in sum_line) {
			if(i == 0) res['progress_line'][i] = sum_line[key];
			else{
				if(typeof res['progress_line'][i] === 'undefined') res['progress_line'][i] = {};
				res['progress_line'][i]['date'] = sum_line[key]['date'];
				res['progress_line'][i]['date_format'] = sum_line[key]['date_format'];
				res['progress_line'][i]['budget_price'] = sum_line[key]['budget_price'];
				res['progress_line'][i]['forecast_price'] = sum_line[key]['forecast_price'];
				res['progress_line'][i]['ordered_price'] =  sum_line[key]['ordered_price'];
				progress_external_euro_max = Math.max(sum_line[key]['budget_price'],sum_line[key]['forecast_price'], sum_line[key]['ordered_price'], progress_external_euro_max);
			}
			i++;
		};
		progress_external_euro_max =  Math.round( progress_external_euro_max * 1.1, 2);
		res['percent_ordered_euro'] = calculatePercent(res['ordered_euro'], res['forecast_euro']);
		res['percent_forecast_euro'] = calculatePercent(res['forecast_euro'], res['budget_euro']);
		res['budget_euro'] = number_format(res['budget_euro'], 2, ',', ' ') + ' ' + budget_settings;
		res['forecast_euro'] = number_format(res['forecast_euro'], 2, ',', ' ') + ' ' + budget_settings;
		res['ordered_euro'] = number_format(res['ordered_euro'], 2, ',', ' ') + ' ' + budget_settings;
		return res;
	}
	Dashboard.get_data_dash_synthesis = function() {
		if( !ControlGrid) return {};
		var res = [];
		res['budget_euro'] = 0;
		res['forecast_euro'] = 0;
		res['consumed_euro'] = 0;
		res['count_project'] = 0;
		res['count_project_exceed_budget'] = 0;
		res['progress_line'] = [];
		var dataLength = ControlGrid.getDataLength();
		var dataView = ControlGrid.getDataView();
		var sum_line = [];
		for( var i=0; i< dataLength; i++){
			var project_item = dataView.getItem(i);
			var item = [];
			res['count_project'] += 1;
			res['budget_euro'] += project_item['ProjectWidget.Synthesis_budget'];
			res['forecast_euro'] += project_item['ProjectWidget.Synthesis_forecast'];
			res['consumed_euro'] += (project_item['ProjectWidget.External_ordered_erro'] + project_item['ProjectWidget.Internal_engaged_euro']);
			if(project_item['ProjectWidget.Synthesis_forecast'] > project_item['ProjectWidget.Synthesis_budget']){
				res['count_project_exceed_budget'] += 1;
			}else{
				res['count_project_exceed_budget'] += 0;
			}
			var data_external = project_item['DataSet']['external_progress'];
			var data_internal = project_item['DataSet']['internal_progress'];
			var data_total = project_item['DataSet']['syns_progress_key'];
			
			var lastInter = {
				'budget' : 0,
				'forecast' : 0,
				'engared' : 0,
			};
			var lastEx = {
				'budget' : 0,
				'forecast' : 0,
				'engared' : 0,
			};
			for (var key in data_total) {
				_date = data_total[key];
				if(typeof _date !== 'undefined') {
					if(typeof sum_line[_date] === 'undefined')sum_line[_date] = {};
					sum_line[_date]['date'] = _date;
					sum_line[_date]['date_format'] = data_internal[_date] ? data_internal[_date]['date_format'] : data_external[_date]['date_format'];
					
					if( data_internal[_date] &&  data_internal[_date]['budget_price']) lastInter['budget'] = data_internal[_date]['budget_price'];
					if( data_external[_date] &&  data_external[_date]['budget']) lastEx['budget'] = data_external[_date]['budget'];
					budget_price = lastEx['budget'] + lastInter['budget'];
					
					if( data_internal[_date] &&  data_internal[_date]['validated_price']) lastInter['forecast'] = data_internal[_date]['validated_price'];
					if( data_external[_date] &&  data_external[_date]['forecast']) lastEx['forecast'] = data_external[_date]['forecast'];
					forecast_price = lastEx['forecast'] + lastInter['forecast'];
					
					if( data_internal[_date] &&  data_internal[_date]['consumed_price']) lastInter['engared'] = data_internal[_date]['consumed_price'];
					if( data_external[_date] &&  data_external[_date]['ordered']) lastEx['engared'] = data_external[_date]['ordered'];
					consumed_price = lastEx['engared'] + lastInter['engared'];
					
					if(typeof sum_line[_date]['budget_price'] === 'undefined') sum_line[_date]['budget_price'] = 0;
					if(typeof sum_line[_date]['forecast_price'] === 'undefined') sum_line[_date]['forecast_price'] = 0;
					if(typeof sum_line[_date]['consumed_price'] === 'undefined') sum_line[_date]['consumed_price'] = 0;
					sum_line[_date]['budget_price'] += parseFloat(budget_price);
					sum_line[_date]['forecast_price'] += parseFloat(forecast_price);
					sum_line[_date]['consumed_price'] += parseFloat(consumed_price);
				}
			}
		}
		var i = 0;
		progress_synthesis_euro_max = 0;
		synthesis_chart_fields = ['budget_price', 'forecast_price', 'consumed_price'];
		for (var key in sum_line) {
			if(i == 0) res['progress_line'][i] = sum_line[key];
			else{
				var j = i - 1;
				if(typeof res['progress_line'][i] === 'undefined') res['progress_line'][i] = {};
				res['progress_line'][i]['date'] = sum_line[key]['date'];
				res['progress_line'][i]['date_format'] = sum_line[key]['date_format'];
				res['progress_line'][i]['budget_price'] = sum_line[key]['budget_price'];
				res['progress_line'][i]['forecast_price'] = sum_line[key]['forecast_price'];
				res['progress_line'][i]['consumed_price'] =  sum_line[key]['consumed_price'];
				progress_synthesis_euro_max = Math.max(sum_line[key]['budget_price'],sum_line[key]['forecast_price'], sum_line[key]['consumed_price'], progress_synthesis_euro_max);
			}
			i++;
			
		};
		progress_synthesis_euro_max =  Math.round( progress_synthesis_euro_max * 1.1, 2);
		res['percent_consumed_euro'] = calculatePercent(res['consumed_euro'], res['forecast_euro']);
		res['percent_forecast_euro'] = calculatePercent(res['forecast_euro'], res['budget_euro']);
		res['budget_euro'] = number_format(res['budget_euro'], 2, ',', ' ') + ' ' + budget_settings;
		res['forecast_euro'] = number_format(res['forecast_euro'], 2, ',', ' ') + ' ' + budget_settings;
		res['consumed_euro'] = number_format(res['consumed_euro'], 2, ',', ' ') + ' ' + budget_settings;
		return res;
	}
	Dashboard.get_data_dash_next_milestones = function() {
		if( !ControlGrid) return {};
		var dataLength = ControlGrid.getDataLength();
		var dataView = ControlGrid.getDataView();
		var res = [];
		for( var i=0; i< dataLength; i++){
			var item = dataView.getItem(i);
			if( item.DataSet.Milestone_next && item.DataSet.Milestone_next.date) {
				var data = item.DataSet.Milestone_next.value;
				data['project_id'] = item.id;
				data['project_name'] = item['Project.project_name'];
				data['timestamp_date'] = item.DataSet.Milestone_next.date;
				res.push(data);
			}
		}
		var data = $.orderObjectBySpecialKey( res, 'timestamp_date', 'number', true);
		return data;
	}
	Dashboard.get_data_dash_count_projects = function(data){
		if( !ControlGrid) return {};
		var count_in_list = [];
		var dataLength = ControlGrid.getDataLength();
		var dataView = ControlGrid.getDataView();
		for( var i=0; i< dataLength; i++){
			var item = dataView.getItem(i);
			if(typeof count_in_list[item['DataSet']['category']] === 'undefined') count_in_list[item['DataSet']['category']] = 0;
			count_in_list[item['DataSet']['category']] += 1;
		}
		return count_in_list;
	}
	Dashboard.get_data_dash_count_tasks = function(){
		if( !ControlGrid) return {};
		var dataLength = ControlGrid.getDataLength();
		var dataView = ControlGrid.getDataView();
		var res = {
			count_tasks: 0,
			late_tasks: 0,
			intime_task: 0,
		};
		for( var i=0; i< dataLength; i++){
			var item = dataView.getItem(i);
			if( item.DataSet.summary_task) {
				var data = item.DataSet.summary_task;
				res['count_tasks'] += (data.count_task || 0);
				res['late_tasks'] += (data.count_task_late|| 0);
				res['intime_task'] += (data.count_task_intime|| 0);
			}
		}
		return res;
	}
	Dashboard.get_data_dash_progress_tasks = function() {
		if( !ControlGrid) return {};
		var dataLength = ControlGrid.getDataLength();
		var dataView = ControlGrid.getDataView();
		var res = {
			count_tasks: 0,
			task_status: {
				CL: 0,
				IP: 0
			},
		};
		for( var i=0; i< dataLength; i++){
			var item = dataView.getItem(i);
			if( item.DataSet.summary_task) {
				var data = item.DataSet.summary_task;
				res['count_tasks'] += (data.count_task || 0);
				res['task_status']['CL'] += (data.count_task_late|| 0);
				res['task_status']['IP'] += (data.count_task_intime|| 0);
			}
		}
		return res;
	}
	Dashboard.get_data_dash_list_weather = function(){
		var weather = [];
		var keys = ['sun', 'cloud', 'rain', 'sum'];
		$.each( keys , function( index, key){
			weather[key] = 0;
		});
		if(ControlGrid){
			var dataLength = ControlGrid.getDataLength();
			var dataView = ControlGrid.getDataView();
			for( var i=0; i< dataLength; i++){
				var item = dataView.getItem(i);
				weather['sum']++;
				key = item['ProjectAmr.weather'];
				if(key) weather[key]++;
			};
		}  
		return weather;
	}
	Dashboard.get_data_dash_sum_progress = function(){
		var projects = Dashboard.project_ids;
		var project_progress_method = companyConfigs.project_progress_method||'consumed';
		Dashboard.project_progress_method = project_progress_method;
		var progress = 0;
		switch( project_progress_method) {
			case 'count_close_task':
				var total_task = 0, cl_task = 0;
				$.each( projects, function( i,p){
					if( projectProgress[p]){
						total_task += parseFloat(projectProgress[p]['TotalTask']);
						cl_task += parseFloat(projectProgress[p]['ClosedTask']);
					}
				});
				if( total_task == 0 ) progress = 100;
				else progress = cl_task * 100 / total_task;	
				break;
			case 'workload_of_close_task':
				var total_workload = 0, cl_workload = 0;
				$.each( projects, function( i,p){
					if( projectProgress[p]){
						total_workload += parseFloat(projectProgress[p]['TotalWorkload']);
						cl_workload += parseFloat(projectProgress[p]['ClosedWorkload']);
					}
				});
				if( total_workload == 0 ) progress = 100;
				else progress = cl_workload * 100 / total_workload;	
				break;
			case 'manual':
				if( projects.length){
					$.each( projects, function( i,p){
						progress += parseFloat(projectProgress[p]['Completed']);
					});
					progress /= projects.length;
				}
				break;
			case 'no_progress':
				
				break;
			case 'consumed':
			default:
				var s_consumed = 0, s_workload = 0;
				$.each( projects, function( i,p){
					if( projectProgress[p]){
						s_consumed += parseFloat(projectProgress[p]['Consumed']);
						s_workload += parseFloat(projectProgress[p]['Workload']);
					}
				});
				if( s_workload == 0 ) progress = s_consumed ? 100 : 0;
				else progress = s_consumed * 100 / s_workload;			
		}
		Dashboard.progress = progress;
		return progress;
	}
	Dashboard.get_data_dash_progress_chart = function(){
		var projects = Dashboard.project_ids;
		var project_progress_method = companyConfigs.project_progress_method||'consumed';
		Dashboard.project_progress_method = project_progress_method;
		var progress = 0;
		var step = 10;
		var key = 0;
		var max = 100;
		var data = {};
		while( key <= max){
			data[key] = 0;
			$.each( projects, function( i,p){
				var _progress = projectProgress[p]['Completed'];
				_progress = Math.min( 100, Math.max( _progress, 0));
				if( (_progress >= key) && (_progress < (key+step))){
					data[key] = data[key] + 1;
				}
			});
			key += step;
		}
		return data;
	}
	Dashboard.get_data_project_amr_program_id = function(){
		var programs = {};
		programs['sum'] = 0;
		if(ControlGrid){
			var dataLength = ControlGrid.getDataLength();
			var dataView = ControlGrid.getDataView();
			for( var i=0; i< dataLength; i++){
				var item = dataView.getItem(i);
				if(item['Project.project_amr_program_id']){
					programs['sum']++;
					var program_id = item['Project.project_amr_program_id'];
					if( ! programs[program_id]){
						programs[program_id] = [];
						programs[program_id]['count'] = 0;
					}
					programs[program_id]['count']++;
					programs[program_id]['name'] = projectProgram[program_id];
				}
			};
		}  
		return programs;
	}
	Dashboard.get_data_dash_project_managers = function(){
		var project_managers = [];
		project_managers['sum'] = 0;
		if(ControlGrid){
			var dataLength = ControlGrid.getDataLength();
			var dataView = ControlGrid.getDataView();
			for( var i=0; i< dataLength; i++){
				var item = dataView.getItem(i);
				manager_ids = item['Project.project_manager_id'];
				if(manager_ids.length > 0){
					$.each(manager_ids, function(key, manager_id){
						if(! project_managers[manager_id]){
							project_managers[manager_id	] = [];
							project_managers[manager_id]['count'] = 0;
							project_managers['sum']++;
						}
						project_managers[manager_id]['count']++;
						project_managers[manager_id]['percent'] =  number_format((project_managers[manager_id]['count'] * 100) / dataLength, 1);
						project_managers[manager_id]['name'] = list_avatar[manager_id]['full_name'];
						project_managers[manager_id]['manager_id'] = manager_id;
					});
					
				}
			};
		}  
		return project_managers;
	}
	Dashboard.get_data_dash_profit_center_estimated = function(pc_selected){
		var pc_estimated = [];
		if(ControlGrid){
			var dataLength = ControlGrid.getDataLength();
			var dataView = ControlGrid.getDataView();
			for( var i=0; i< dataLength; i++){
				var item = dataView.getItem(i);
				var project_estimated = item['DataSet']['pc_forecast'];
				for (var pc_id in project_estimated) {
					if(typeof pc_estimated[pc_id] === 'undefined') pc_estimated[pc_id] = {};
					for (var date in project_estimated[pc_id]) {
						if(typeof pc_estimated[pc_id][date] === 'undefined') pc_estimated[pc_id][date] = [];
						if(typeof pc_estimated[pc_id][date]['value'] === 'undefined') pc_estimated[pc_id][date]['value'] = 0;
						var text_month = project_estimated[pc_id][date]['month_text'];
						pc_estimated[pc_id][date]['text_month'] = project_estimated[pc_id][date]['month_text'];
						pc_estimated[pc_id][date]['value'] = project_estimated[pc_id][date]['value'] ? parseFloat(project_estimated[pc_id][date]['value']) : 0;
						pc_estimated[pc_id][date]['capcity'] = parseFloat(project_estimated[pc_id][date]['capacity']);
					}
				}
			}
		}
		data_pc_forecast = pc_estimated;
		var data = {};
		data['pc_forecasts'] = pc_selected ? pc_estimated[pc_selected] : {};
		data['pc_manager'] = typeof list_avatar[list_pc_manager[pc_selected]] !== 'undefined' ? list_avatar[list_pc_manager[pc_selected]] : '';
		return data;
	}
	Dashboard.get_data_dash_pc_forecast = function(pc_forecast){
		var pc_estimated = [];
		var pc_selected = [];
		if(ControlGrid){
			var dataLength = ControlGrid.getDataLength();
			var dataView = ControlGrid.getDataView();
			for( var i=0; i< dataLength; i++){
				var item = dataView.getItem(i);
				var project_id = item.id;
				var project_estimated = pc_forecast[project_id];
				for (var pc_id in project_estimated) {
					pc_selected = pc_id;
					if(typeof pc_estimated[pc_id] === 'undefined') pc_estimated[pc_id] = {};
					for (var date in project_estimated[pc_id]) {
						if(typeof pc_estimated[pc_id][date] === 'undefined') pc_estimated[pc_id][date] = [];
						if(typeof pc_estimated[pc_id][date]['value'] === 'undefined') pc_estimated[pc_id][date]['value'] = 0;
						var text_month = project_estimated[pc_id][date]['month_text'];
						pc_estimated[pc_id][date]['text_month'] = project_estimated[pc_id][date]['month_text'];
						pc_estimated[pc_id][date]['value'] += project_estimated[pc_id][date]['value'] ? parseFloat(project_estimated[pc_id][date]['value']) : 0;
						pc_estimated[pc_id][date]['capcity'] = project_estimated[pc_id][date]['capacity'] ? parseFloat(project_estimated[pc_id][date]['capacity']) : 0;
					}
				}
			}
		}
		// data_pc_forecast = pc_estimated;
		var data = {};
		data['pc_forecasts'] = pc_selected ? pc_estimated[pc_selected] : {};
		data['pc_manager'] = typeof list_avatar[list_pc_manager[pc_selected]] !== 'undefined' ? list_avatar[list_pc_manager[pc_selected]] : '';
		return data;
	}
	Dashboard.get_data_select = function($key){
		var data = {};
		// data['sum'] = 0;
		if(ControlGrid){
			var dataLength = ControlGrid.getDataLength();
			var dataView = ControlGrid.getDataView();
			var col = 'Project.' + $key;
			if( col in SlickGridCustom.selectMaps){
				var col_maps = SlickGridCustom.selectMaps[col];
				for( var i=0; i< dataLength; i++){
					var item = dataView.getItem(i);
					if(item[col]){
						var key = item[col];
						var list_keys = [];
						if(  $.isArray(key)){
							$.each( key, function( i, k){
								list_keys.push(k);
							});
						}else{
							list_keys.push(key);
						}
						$.each( list_keys, function( i, key_id){
							if( ! data[key_id]){
								data[key_id] = [];
								data[key_id]['count'] = 0;
								data[key_id]['key_id'] = key_id;
							}
							data[key_id]['count']++;
							data[key_id]['name'] = col_maps[key_id];
						});
					}
				};
			}
		}
		sorted = $.orderObjectBySpecialKey(data, 'count', 'number', false);
		return sorted;
	}
	Dashboard.get_data_number = function($key){
		if( typeof projectTarget == 'undefined') return;
		var data = {};
		data['sum'] = 0;
		data['target'] = projectTarget && projectTarget['target_'+$key] ? projectTarget['target_'+$key] : [];
		data.field = $key
		if(ControlGrid){
			var dataLength = ControlGrid.getDataLength();
			var dataView = ControlGrid.getDataView();
			var col = 'Project.' + $key;
			var sum = 0;
			for( var i=0; i< dataLength; i++){
				var item = dataView.getItem(i);
				if(item[col]){
					data['sum'] += parseFloat(item[col]);
				}
			};
		}
		var target_value = data['target']['value'] ? data['target']['value'] : 0;
		data.target_percent = calculatePercent(data.sum, target_value);
		
		return data;
	}
	// var list_select_yourform = Dashboard.list_select_yourform;
	// $.each ( list_select_yourform, function( i, n){
		// func = 'get_data_dash_' + n;
		// Dashboard[func] = function(){
			// return Dashboard.get_data_select(n);
		// }
	// });
	// var list_number_yourform = Dashboard.list_number_yourform;
	// $.each ( list_number_yourform, function( i, n){
		// func = 'get_data_dash_' + n;
		// Dashboard[func] = function(){
			// return Dashboard.get_data_number(n);
		// }
	// });

	*/
	function display_dashboard(){
		if( typeof Dashboard != "object") return;
		var _cols = ControlGrid.getColumns();
        var _numCols = _cols.length;
        var _gridW = 0;
        for (var i = 0; i < _numCols; i++) {
                _gridW += _cols[i].width;
        }
		
		var checkbox = $('#switch-dashboard');
		Dashboard.display = checkbox.length ?  checkbox.is(':checked') : false;
		if(Dashboard.display) $('body').css('overflow', 'inherit');
		if( Dashboard.display) {
			$('.dash_multi_dashboard_select').show();
			$('.projects-dashboard-setting').show();
			$('.projects-dashboard-container').addClass('active');
			var minWidth = $('.z0g-header-inner').width();
			_gridW += 40;
			_gridW = Math.max(minWidth, _gridW);
			$('.projects-dashboard-container, .wd-title').css('width', _gridW);
		}else{
			$('.dash_multi_dashboard_select').hide();
			$('.projects-dashboard-setting').hide();
			$('.projects-dashboard-container').removeClass('active');
		}
		if( !Dashboard.ready){
			Dashboard.el.css('overflow', 'hidden');
			Dashboard.el.addClass('loading-mark loading');
		}
		Dashboard.refresh();
		$(window).trigger('resize');
	}
	function escapeRegExp(str) {
		return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
	}
	function replace(obj, str){
		$.each(obj, function(key, value){
			str = replaceAll('{' + key + '}', value, str);
		});
		return str;
	}
	function replaceAll(find, replace, str) {
	  return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
	}
	
    var widgetBudget_html = $('.widget-template').html();
    function resizeHandler() {
        var _cols = ControlGrid.getColumns();
        var _numCols = _cols.length;
        var _gridW = 0;
        for (var i = 0; i < _numCols; i++) {
            // if ($.inArray(_cols[i].id, columnsOfGantt) != -1 && viewGantt) {
                // do nothing
                _gridW += _cols[i].width;
            // } else {
                // _gridW += _cols[i].width;
            // }
        }
        $('#wd-header-custom').css('width', _gridW);
        $('#project_container').css('width', _gridW + 20);
        $('.wd-panel').css('width', _gridW + 40);
        var minWidth = $('.z0g-header-inner').width();
		_gridW += 40;
		_gridW = Math.max(minWidth, _gridW);
		$('.projects-dashboard-container, .wd-title').css('width', _gridW);
        ControlGrid.resizeCanvas();
        $(window).trigger('resize');
    }
    function daysInMonth(month, year) {
        return parseInt(new Date(year, month, 0).getDate());
    }
	(function ($) {
        var urlUpdateCustomer = <?php echo json_encode(urldecode($this->Html->link('%3$s', array('action' => 'update', '%1$s', '%2$s')))); ?>,
                gantts = <?php echo json_encode($gantts);?>,
                stones = <?php echo json_encode($stones);?>,
                isPM = <?php echo json_encode($isPM) ?>,
                editable = <?php echo json_encode($editable) ?>,
                employeeId = <?php echo json_encode($employee_info['Employee']['id']) ?>,
                dataView,
                sortcol,
                triggger = false,
                grid,
                $sortColumn,
                $sortOrder,
                data = <?php echo json_encode($dataView); ?>,
                selects = <?php echo json_encode($selects); ?>,
                totalHeaders = <?php echo json_encode($totalHeaders);?>,
                typeGantt = type = 'year',
                viewStone = <?php echo !empty($confirmGantt) && isset($confirmGantt['stones']) ? json_encode($confirmGantt['stones']) : json_encode(false);?>,
                viewInitial = <?php echo !empty($confirmGantt) && isset($confirmGantt['initial']) ? json_encode($confirmGantt['initial']) : json_encode(false);?>,
                viewReal = <?php echo !empty($confirmGantt) && isset($confirmGantt['real']) ? json_encode($confirmGantt['real']) : json_encode(false);?>,
                columnFilters = {},
                $parent = $('#project_container'),
                timeOutId = null,
                dataViewGG = {},
                listTopRow = {},
                heightNewRow = {},
                gridGG = {},
                viewManDay = <?php echo json_encode($viewManDay); ?>,
                actionTemplate = $('#action-template').html(),
                backupText = {
                    regex: /<strong>\(B\)<\/strong>/,
                    text: '<strong>(B)</strong>'
                },
                imagePriorities = {sun: 0, cloud: 1, rain: 2, up: 3, down: 4, mid: 5},
                counter,
                enable_popup = <?php echo !empty($employee_info['Employee']['is_enable_popup']) ? 1 : 0 ?>,
                see_budget = <?php echo !empty($employee_info['CompanyEmployeeReference']['see_budget']) ? 1 : 0 ?>,
                update_budget = <?php echo !empty($employee_info['Employee']['update_budget']) ? 1 : 0 ?>,
                isPm = <?php echo !empty($employee_info['Role']['name']) && $employee_info['Role']['name'] == 'pm' ? 1 : 0 ?>,
                isAdmin = <?php echo !empty($employee_info['Role']['name']) && $employee_info['Role']['name'] == 'admin' ? 1 : 0 ?>,
                count = 0,
                personDefault = <?php echo json_encode($personDefault);?>,
                appstatus = <?php echo json_encode($appstatus);?>,
                checkStatus = <?php echo json_encode($checkStatus);?>,
                cate_id = appstatus ? appstatus : 1,
                cate = <?php echo json_encode($cate);?>,
                viewId = '',
                checkShowFullMistones = false,
                listColorNextMil = <?php echo json_encode($listColorNextMil); ?>,
                $display_all_name_of_milestones = <?php echo isset($confirmGantt['display_all_name_of_milestones']) && $confirmGantt['display_all_name_of_milestones'] ? 1 : 0 ; ?>;

        function weatherSorter(a, b) {
            return imagePriorities[a.DataSet[sortcol]] > imagePriorities[b.DataSet[sortcol]] ? 1 : -1;
        }
		function asyncDragablePhaseProgress(cellNode, row, dataContext, colDef){
			var p_id = dataContext.id;
			var caneditproject = isAdmin || ( isPm && editable[p_id]);
			var canUpdateprogress = (companyConfigs['project_progress_method'] == "manual") && caneditproject;
			if( canUpdateprogress){
				initEditableProgress( cellNode, p_id);
			}
		}
		function asyncResizableFirstRow(cellNode, row, dataContext, colDef){
			var col = ControlGrid.getColumnIndex(first_commment_column);
			var row = 0;
			var node = ControlGrid.getCellNode( row,col);
			if( node){
				var _con = $(node).children('.wd-open-popup-container');
				if( !_con.hasClass('ui-resizable')){
					$(node).css({
						'z-index': 2,
						'overflow' : 'visible'
					});
					_con.resizable({
						minHeight: viewStone ? 45 : 40,
						grid: [10,22],
						handles: 's',
						stop: function( event, ui ) {
							ControlGrid.setRowHeightCallback(parseInt(ui.size.height), true);
						}
					});
				}
			}			
		}
		function saveManualProgress(elm, project_id){
			var _this = $(elm);
			if(!_this.hasClass('input-progress')) {
				_this = _this.find('.input-progress');
			}
			var val = _this.val();
			var cont = _this.closest('.wd-widget-in-cell');
			cont.addClass('loading');
			$.ajax({
				url: '/projects_preview/saveFieldYourForm/' + project_id,
				type: 'post',
				dataType: 'json',
				data: {
					field: 'manual_progress',
					value: val
				},
				beforeSend: function(){
					cont.addClass('loading');
				},
				success: function(res){
					if( res.result == 'success'){
						displayProgrressVal(cont, parseFloat( res.data.Project.manual_progress ), true);
						_this.blur(); 
						$this.update_after_edit( project_id, {
							'ProjectWidget.Phase_progress' : res.data.Project.manual_progress + '%',
							'ProjectWidget.Project_progress' : res.data.Project.manual_progress + '%',
							});
					}else{;
						location.reload();
					}
				},
				error: function(){
					location.reload();
				},
				complete: function(){
					cont.removeClass('loading');
				}
			});
		}
		function displayProgrressVal(container, val, is_animation){
			var v = parseInt( Math.max(0, Math.min(val, 100)));
			var css_class= '';
			if( val > 100) css_class='red-line';
			if( is_animation){
				container.find('.wd-progress-value-line, .wd-progress-number').addClass('ease');
			}
			container.removeClass('red-line').addClass(css_class);
			container.data('value', val);
			container.find('.wd-progress-value-line').width(v + '%');
			container.find('.wd-progress-number').css('left', v + '%');
			container.find('.wd-progress-number .text').text(v + '%');
			container.find('.input-progress').val(val);
			setTimeout( function(){
				container.find('.wd-progress-value-line, .wd-progress-number').removeClass('ease');
			}, 400);
		}
        function initEditableProgress(elm, p_id) {
			var _this = $(elm);
			_this.find('.wd-progress-number').draggable({
				axis: 'x',
				opacity: 0.6,
				cursor: 'pointer',
				drag: function( event, ui ) {
					var container = ui.helper.closest('.wd-progress-slider');
					var _w = ui.helper.width() + parseInt(ui.helper.css('border-left-width')) + parseInt(ui.helper.css('border-right-width'));
					var max_w = container.width() - _w;
					ui.position.left = Math.max( 0, Math.min( container.width() - _w, ui.position.left ));
					var val = parseInt(ui.position.left / max_w * 100);
					displayProgrressVal(container, val);
				},
				stop: function( event, ui ) {
					saveManualProgress(ui.helper[0], p_id);
				}
			});
			_this.on('click', '.wd-progress-number .text', function(){
				var _this = $(this);
				var _input = _this.siblings('.input-progress');
				_this.parent().addClass('focus');
				_input.on('change', function(){
					saveManualProgress(this, p_id);
				});
				_input.on('keydown', function(e){
					if( e.keyCode == 13){
						_input.blur();
						var _this = $(this).parent();
						_this.find('.text').show();
						_this.find('.input-progress').addClass('wd-hide');
						_this.removeClass('focus');
					}
				});
				_input.on('blur', function(){
					var _this = $(this).parent();
					_this.find('.text').show();
					_this.find('.input-progress').addClass('wd-hide');
					_this.removeClass('focus');
				});
				setTimeout( function(){
					_this.hide();
					_input.removeClass('wd-hide').show().focus();
					_this.parent().addClass('focus');
				}, 10);
			});
		}
        function listProjectStautus(id, view_id) {
            if (id != '') {
                $.ajax({
                    url: '/projects/getPersonalizedViews/' + id,
                    async: false,
                    beforeSend: function () {
                        $('#CategoryStatus').html('<option>Please waiting...</option>');
                    },
                    success: function (data) {
                        var data = JSON.parse(data);
                        var selected = selectDefined = selectDefault = '';
                        if (view_id != null) {
                            if (view_id == 0) {
                                selected = 'selected="selected"';
                            } else if (view_id == -1) {
                                selectDefined = 'selected="selected"';
                            } else if (view_id == -2) {
                                selectDefault = 'selected="selected"';
                            }
                        }
                        var content = '<option value="0" ' + selected + '><?php echo  __("------- Select -------", true);?></option>';
                        if (personDefault == false) {
                            content += '<option value="-1" ' + selectDefined + '><?php echo  __("-- Default", true);?></option>';
                        } else {
                            content += '<option value="-2" ' + selectDefault + '><?php echo  __("-- Default", true);?></option>';
                        }
						if( data){
							data = $.sortObjectByValue(data);
							$.each(data, function (ind, val) {
								var selected = '';
								if (view_id == val[0] && view_id != null && view_id != -2 && view_id != -1 && view_id != 0) {
									selected = 'selected="selected"';
								}
								content += '<option value="' + val[0] + '" ' + selected + '>' + val[1] + '</option>';
							});
						}
                        $('#CategoryStatus').html(content);


                        var list_icon = [
                            '/img/new-icon/pilotage.png',
                            '/img/new-icon/planing.png',
                            '/img/new-icon/budget.png',
                            '/img/new-icon/risques.png',
                        ];
                        var _list_cnav = _list_drop = '';
                        var index = 0;
                        $.each(data, function (ind, val) {
                            var activated = '';
                            if (view_id == val[0] && view_id != null && view_id != -2 && view_id != -1 && view_id != 0) {
                                activated = 'activated';
                            }
                            if (activated)
                                $('#cn-button').text(val[1]);

                            if (index < 4) {
                                _list_cnav += '<li data-value="' + val[0] + '" class="item ' + activated + '"><a href="/projects/index/' + val[0] + '?cate=' + cate + '"><img src="' + list_icon[index] + '"><span>' + val[1] + '</span></a></li>';
                            } else {
                                _list_drop += '<li data-value="' + val[0] + '" class="item ' + activated + '"><a href="/projects/index/' + val[0] + '?cate=' + cate + '"><span>' + val[1] + '</span></a></li>';
                            }
                            index++;
                        });
                        $('#cn-wrapper .circular-nav').append(_list_cnav);
                        $('#cn-wrapper .dropdown').prepend(_list_drop);

                        $('#CategoryStatus').html(content);
                    }
                });
            }
        }
        listProjectStautus(appstatus, checkStatus);
        function initPersionalize() {
            var button = $('#cn-button'),
                    wrapper = $('#persionalize .component');
            // var menu_ext_button = $('.cn-wrapper .open-dropdown');
            var menu_ext_button = wrapper.find('.open-dropdown');
            // var menu_ext = menu_ext_button.closest('cn-wrapper').find('.dropdown:first');

            button.on('click', function (e) {
                e.preventDefault();
                wrapper.toggleClass('persionalize-active').removeClass('menu-open');
            });
            menu_ext_button.on('click', function () {
                wrapper.toggleClass('menu-open');
            });

            $('body').on('click', function (e) {
                if (!$(e.target).hasClass('persionalize-container') && !$('.persionalize-container').find(e.target).length) {
                    wrapper.removeClass('persionalize-active').removeClass('menu-open');
                }
            });


        }
        function initCategoryView() {
            var button = $('#cc-button'),
                    wrapper = $('#CategoryView .component');
            var menu_ext_button = wrapper.find('.open-dropdown');
            // var menu_ext = menu_ext_button.closest('cn-wrapper').find('.dropdown:first');

            button.on('click', function (e) {
                e.preventDefault();
                wrapper.toggleClass('persionalize-active').removeClass('menu-open');

            });
            menu_ext_button.on('click', function () {
                wrapper.toggleClass('menu-open');
            });
            $('body').on('click', function (e) {
                if (!$(e.target).hasClass('circular-menu-container') && !$('.circular-menu-container').find(e.target).length) {
                    wrapper.removeClass('persionalize-active').removeClass('menu-open');
                }
            });

        }
        initPersionalize();
        initCategoryView();

        function showProjectCreatedVals(e, id) {
            jQuery.ajax({
                url: "/project_created_vals_preview/ajax/" + id,
                type: "GET",
                data: data,
                cache: false,
                success: function (html) {
                    jQuery('#dialogDetailValue').css({'padding-top': 0, 'padding-bottom': 0, 'left': "50%", 'top': "50%",'transform': "translate(-50%, -50%)", 'margin-left': "unset", 'margin-right': "unset"});
                    var wh = jQuery(window).height();
                    if (wh < 768) {
                        // jQuery('#dialogDetailValue').css({'min-width':'200px !important'});
                        jQuery('#contentDialog').css({'max-height': 600, 'width': 'auto'});
                    } else {
                        jQuery('#contentDialog').css({'max-height': 'none', 'width': 1200 });
                    }
                    jQuery('#contentDialog').html(html);
                    jQuery(e.target).removeClass('hoverCell');
                    jQuery(e.target).removeClass('loading');
                    showMe();
                    clearInterval(counter);
					setTimeout(function(){
						setHeightBoxCreated();
					}, 100);
                }
            });
        }
		function setHeightBoxCreated(){
			var pop_created = $('#createdValue');
			if(pop_created.length > 0){
				var box_created = pop_created.find('.wd-block-list');
				var max_height = 0;
				$.each(box_created, function(index, e){
					var box_height = $(e).height();
					console.log(box_height);
					if(box_height > max_height) max_height = box_height;
				});
				$.each(box_created, function(index, e){
					$(e).height(max_height);
				});
				console.log(max_height);
			}
		}
		function showProjectBudgetSyn(e, id) {
            jQuery.ajax({
                url: "/project_budget_synthesis/ajax/" + id,
                type: "GET",
                cache: false,
                success: function (html) {
                    jQuery('#dialogDetailValue').css({'padding-top': 0, 'padding-bottom': 20});
                    jQuery('#contentDialog').css({'max-height': 600, 'max-width': 1700,});
                    jQuery('#contentDialog').html(html);
                    jQuery(e.target).removeClass('hoverCell');
                    jQuery(e.target).removeClass('loading');
                    showMe();
                    clearInterval(counter);
                }
            });
        }
        function showProjectFinanceGlobalViews(e, id, _cat) {
            var _this = $(e.target);
            $.ajax({
                url: "/project_finances_preview/ajax/" + id,
                type: "GET",
                cache: false,
                data: {
                    cat: _cat
                },
                success: function (html) {
                    _this.removeClass('loading').removeClass('hoverCell');
                    $('#dialogDetailValue').css({
                        'padding-top': 0,
                        'padding-bottom': 0,
                    });
                    var wh = $(window).height();
                    if (wh < 768) {
                        $('#contentDialog').css({'max-height': 600, 'width': 'auto'});
                    } else {
                        $('#contentDialog').css({'max-height': 'none', 'width': 'auto'});
                    }
                    showMe();
                    $('#contentDialog').html(html);
                    clearInterval(counter);
                }
            });
        }
        function totalDate(start, end) {
            /**
             * Tinh tong so ngay
             */
            start = new Date(start).getTime();
            end = new Date(end).getTime();
            var totalDays = Math.abs(parseInt(start) - parseInt(end));
            totalDays = Math.ceil(totalDays / (1000 * 3600 * 24));
            return totalDays;
        }
        function DrawGantt(projectId, start, end, comPlan, currentColumn, color, type, projectName, completed) {
            var _width = 0,
                    _left = 0,
                    _border_style = 'dashed',
                    _completedWidth = 0,
                    result = '';
            start = start.split('-');
            end = end.split('-');
            var saveSt = start.slice(), saveEn = end.slice();
            /**
             * Draw
             */
            if (currentColumn < start[2] || currentColumn > end[2]) {
                // don't draw anything
            } else {
                // Tong so ngay cua 1 nam
                var _totalDateOfYear = totalDate(currentColumn + '-01-01', currentColumn + '-12-31');
                // Tong so ngay tu ngay bat dau den cuoi nam
                var _totalStartToYear = totalDate(start[2] + '-' + start[1] + '-' + start[0], currentColumn + '-12-31');
                // Tong so ngay de ve diem bat dau
                var _startLine = Math.abs(parseInt(_totalStartToYear) - parseInt(_totalDateOfYear));
                _startLine = (_startLine * 100) / _totalDateOfYear;
                // Tong so ngay tu ngay bat dau cua nam den ngay cuoi cung cua gantt
                var _totalStartToEndGantt = totalDate(currentColumn + '-01-01', end[2] + '-' + end[1] + '-' + end[0]);
                var _endLine = (_totalStartToEndGantt * 100) / _totalDateOfYear;
                // Tinh end line cho cac truong hop start va end trong cung 1 nam.
                var _totalStartToEndGanttNotStartAtJanuary = totalDate(start[2] + '-' + start[1] + '-' + start[0], end[2] + '-' + end[1] + '-' + end[0]);
                var _endLineNotStartAtJanuary = (_totalStartToEndGanttNotStartAtJanuary * 100) / _totalDateOfYear;
                if (start[2] == end[2]) {
                    _width += _endLineNotStartAtJanuary;
                    _left += _startLine;
                } else {
                    if (start[2] == currentColumn) {
                        _width += _endLine;
                        _left += _startLine;
                    } else if (currentColumn < end[2]) {
                        _width += 100;
                    } else {
                        _width += _endLine;
                    }
                }
                if (type == 's') {
					_border_style = 'solid';
                }
                if (comPlan) {
                    comPlan = comPlan.split('-');
                    if (currentColumn == comPlan[2]) {
                        /**
                         * Tinh tong so ngay
                         */
                        var _start = start[2] + '-' + start[1] + '-' + start[0],
                                _end = end[2] + '-' + end[1] + '-' + end[0];
                        if (start[2] < comPlan[2]) {
                            _start = comPlan[2] + '-01-01';
                        }
                        if (end[2] > comPlan[2]) {
                            _end = comPlan[2] + '-12-31';
                        }
                        _start = new Date(_start).getTime();
                        _end = new Date(_end).getTime();
                        var totalDays = Math.abs(parseInt(_start) - parseInt(_end));
                        totalDays = Math.ceil(totalDays / (1000 * 3600 * 24));
                        /*
                         * Tinh so ngay completed
                         */
                        var _com = new Date(comPlan[2] + '-' + comPlan[1] + '-' + comPlan[0]).getTime();
                        _com = Math.abs(parseInt(_start) - parseInt(_com));
                        _com = Math.ceil(_com / (1000 * 3600 * 24));
                        _completedWidth = (_com * 100) / totalDays;
                    } else if (currentColumn < comPlan[2]) {
                        _completedWidth = 100;
                    } else {
                        _completedWidth = 0;
                    }
                    if (completed <= 0) {
                        _completedWidth = 0;
                    }
                }
                completed = (completed > 100) ? 100 : completed;
                completed = (completed < 0) ? 0 : completed;
                var dataTooltip = '<div class="hover-data" style="display: none">' +
                        '<p class="hover-data-name">' + projectName + '</p>' +
                        '<p class="hover-data-start">' + saveSt[0] + '/' + saveSt[1] + '/' + saveSt[2] + '</p>' +
                        '<p class="hover-data-end">' + saveEn[0] + '/' + saveEn[1] + '/' + saveEn[2] + '</p>' +
                        '<p class="hover-data-comp">' + completed + '%</p>' +
                        '<p class="hover-data-assign"></p>' +
                        '</div>';
                result += '<div id="line-' + type + '-' + projectId + '" onclick="showPhaseDetail(event, ' + projectId + ')"class="gantt-line-' + type + ' hover-tooltip-cus" style="position: absolute; left: ' + _left + '%; border: 2px '+ _border_style +' ' + color + '; width:' + _width + '%;">' +
                        dataTooltip + '<em style="position: relative; top: -2px; background-color: ' + color + '; height: 7px; display: block; width:' + _completedWidth + '%;"></em>' +
                        '</div>';
            }
            return result;
        }
        var _role = <?php echo json_encode($employee['Role']['name']);?>;
        var _employee_id = <?php echo json_encode($employee['Employee']['id']);?>;
        var _allowDeleteProject = <?php echo json_encode($employee['Employee']['delete_a_project']);?>;
        var _allowDeleteProfjectByProfile = <?php echo !empty($profileName) ? json_encode($profileName['ProfileProjectManager']['can_delete_project']) : 0;?>;
		CustomFilter.display = {
			Project_progress: function(column, header){
				var unit = '%';
				var val = (typeof filter_render !== "undefined" && filter_render[column.id] ) ? filter_render[column.id] : '0,100';
				var _input = '<input type="hidden" name="' + column.id + '" id="' + column.id + '" class="input-filter" data-column="' + column.id + '" value="' + val + '"/>';
				val = val.split(',');
				min = val[0];
				max = val[1];
				var id=column.id.replace('.', '_') + '_slider';
				var _slider = $('<div class="slick-header-progress-cont"><div class="slick-header-progress-slider"><div class="slick-header-progress-item" id="' + id +'"></div></div></div>');
				
				_slider.append(_input);	
				header.append(_slider);					
				header.find('#' + id).slider({
					range: true,
					min: 0,
					max: 100,
					values: [ min, max ],
					create: function( e, ui){
						var _this = $(this);
						_this.find( '.ui-slider-handle').addClass('max').html(max + unit).data('input', column.id).first().addClass('min').html(min + unit).removeClass('max');
					},
					slide: function(e, ui){
						$(ui.handle).html(ui.value + unit);
					},
					change: function(e, ui){
						var _this = $(this);
						var vals = ui.values;
						_this.find( '.min').html(vals[0] + unit);
						_this.find( '.max').html(vals[1] + unit);
						_this.data('ismanual', true);
						var _input = $('#project_container').find( 'input[name="' + $(ui.handle).data('input') + '"]');
						_input.val(vals.join(',')).trigger('change');
					}
				});
				header.find('.input-filter').on('change', function(){
					var _slider = header.find('#' + id);
					if( !_slider.data('ismanual')){
						console.log('Set value');
						var _this = $(this);
						val = _this.val();
						val = val ? val.split(',') : [0, 100];
						_slider.slider( "values", val );
					}
					_slider.data('ismanual', false);
				});
				return header.find('.input-filter');
			}
		};
		CustomFilter.filter = {
			Project_progress: function(item, column, filter_val){
				filter_val = filter_val[0].split(',');
				var value = parseFloat(item[column.field]);
				var min = filter_val[0] ? filter_val[0] : 0;
				var max = filter_val[1] ? filter_val[1] : 100;
				value = Math.min(Math.max( value, 0), 100);
				return ((value >= min) && (value <= max));
			}
		};
        $.extend(Slick.Formatters, {
            Action: function (row, cell, value, columnDef, dataContext) {
                var linkProjectName = <?php echo json_encode($html->url('/' . $ACLController . '/' . $ACLAction));?>;
                //value = '<div class="wd-actions"><a href="'+ _linkDashboard +'/'+ dataContext['id'] +'?view=new" class="wd-dashboard"></a><a href="'+ _linkKanban +'/'+ dataContext['id'] +'?view=new" class="wd-kanban"></a><a href="'+ linkProjectName +'/'+ dataContext['id'] +'" class="wd-edit"></a>';
                // remove kanban
                value = '<div class="wd-actions">';
                if (_linkDashboard) {
                    value += '<a target="_blank" href="' + _linkDashboard['link'] + '/' + dataContext['id'] + '?view=new" class="wd-dashboard" title="' + _linkDashboard['title'] + '"></a>';
                }
                if (_linkKanban) {
                    value += '<a href="' + _linkKanban + '/' + dataContext['id'] + '?view=new" class="wd-kanban" title="<?php __('Kanban');?>"></a>';
                }
                if (linkProjectName) {
                    value += '<a href="' + linkProjectName + '/' + dataContext['id'] + '" class="wd-edit"></a>';
                }
				if(dataContext['used'] == 0){
					var _pname = dataContext['Project.project_name'];
					if (_role == 'admin' || (_role == 'pm' && _allowDeleteProject == 1 && $.inArray(dataContext['id'].toString(), listProjectOfPM) != -1)) {
						value += '<a href="javascript:void(0);" onclick="dialog_confirm(' + dataContext['id'] + ',\'' + $this.t('confirmDelete', htmlEntities(_pname)) + '\')" class="wd-hover-advance-tooltip"></a>';
					}
				}else{
					value += '<a href="javascript:void(0);" title="'+ i18ns['project_used'] +'"class="wd-hover-advance-tooltip disable"></a>';
				}


                value += '</div>';
                return Slick.Formatters.HTMLData(row, cell, value, columnDef, dataContext);
            },
            comment: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value.description ? '<span class="comment">' + value.description + '</span>' : '';
				}
                var _pid = dataContext.id;
                var _colum = columnDef.id;
                var _h = (viewStone) ? 40 : 42;
                var name = '';
                var circle_name = '';
                var s_name = '';
                var _updated = value.updated;
                var _current = value.current ? value.current : 0;
                var tag_cur = '';
                if (_current) {
                    _diff = _current - _updated;
                    if (_diff < 3600 * 24 * 31) { // dưới 1 tháng
                        if (_diff < 3600) {
                            tag_cur = (_diff <= 60) ? '1 ' + i18n.minute : parseInt(_diff / 60).toString() + ' ' + i18n.minutes;
                        } else if (_diff < 3600 * 24) {
                            tag_cur = (_diff <= 3600) ? '1 ' + i18n.hour : parseInt(_diff / 3600).toString() + ' ' + i18n.hours;
                        } else {
                            tag_cur = (_diff <= 3600 * 24) ? '1 ' + i18n.day : parseInt(_diff / (3600 * 24)).toString() + ' ' + i18n.days;
                        }
                    } else { // trên 1 tháng
                        var t = 3600 * 24;
                        var curr_date = new Date(_current * 1000);
                        var _updated_date = new Date(_updated * 1000);
                        if (_diff < 365 * t) {
                            var _jdiff = curr_date.getMonth() - _updated_date.getMonth();
                            if (_jdiff <= 0)
                                _jdiff += 12;
                            tag_cur = (_jdiff == 1) ? '1 ' + i18n.month : parseInt(_jdiff).toString() + ' ' + i18n.months;
                        } else {
                            var _jdiff = curr_date.getFullYear() - _updated_date.getFullYear();
                            tag_cur = (_jdiff == 1) ? '1 ' + i18n.year : parseInt(_jdiff).toString() + ' ' + i18n.years;
                        }
                    }
                }
                if ('update_by_employee' in Object(value)) {
                    name = [];
                    var s_name = value.update_by_employee.split(" ");
                    name[0] = s_name[0][0] + s_name[1][0];
                    name[1] = value.update_by_employee;
                    
                        circle_name = '<span class="project-manager-name circle-name" title = "' + name[1] + '"><img width = 35 height = 35 src="' + js_avatar(value.employee_id) + '" title = "' + value.update_by_employee + '" /></span>';
                    
                }
                var model = 'ProjectAmr';
                var field = 'comment';
                if (_colum == 'ProjectAmr.comment') {

                } else if (_colum == 'ProjectAmr.project_amr_solution') {
                    field = 'project_amr_solution';
                } else if (_colum == 'ProjectAmr.done') {
                    model = 'Done';
                    field = 'done';
                } else if (_colum == 'ProjectAmr.project_amr_scope') {
                    model = 'Scope';
                    field = 'project_amr_scope';
                } else if (_colum == 'ProjectAmr.project_amr_budget_comment') {
                    model = 'Budget';
                    field = 'project_amr_budget_comment';
                } else if (_colum == 'ProjectAmr.project_amr_schedule') {
                    model = 'Schedule';
                    field = 'project_amr_schedule';
                } else if (_colum == 'ProjectAmr.project_amr_resource') {
                    model = 'Resources';
                    field = 'project_amr_resource';
                } else if (_colum == 'ProjectAmr.project_amr_technical') {
                    model = 'Technical';
                    field = 'project_amr_technical';
                } else if (_colum == 'ProjectAmr.project_amr_problem_information') {
                    model = 'ProjectIssue';
                    field = 'project_amr_problem_information';
                } else if (_colum == 'ProjectAmr.project_amr_risk_information') {
                    model = 'ProjectRisk';
                    field = 'project_amr_risk_information';
                } else {
                    model = 'ToDo';
                    field = 'todo';
                }
                if (tag_cur) {
                    tag_cur = '<i class="icon-clock"></i>' + tag_cur;
                }
				var cm_log_id = (_employee_id == value.employee_id ) ? value.id : 0;
                if (circle_name) {
                    return '<div class="wd-open-popup-container">' + circle_name + '<div class="hover-popup">' + urlify(value.description) + '</div><div class="wd-open-popup comment-text" data-project_id = ' + _pid + ' onclick="show_comment_popup(this, \''+ model +'\');"><div class="comment">' + value.description + '</div><span class="time">' + tag_cur + '</span></div> </div>';
                } else {
					return '<div class="wd-open-popup-container"><p class="wd-open-popup comment-text" data-project_id = ' + _pid + ' onclick="show_comment_popup(this, \''+ model +'\');">&nbsp;</p> </div>';
                }
                return '<p>' + value + '</p>';
            },
            GanttCustom: function (row, cell, value, columnDef, dataContext) {
                var _color = (row % 2 == 0) ? '#0099cc' : '#f05656';
                var projectId = dataContext.id ? dataContext.id : 0,
                        projectName = dataContext['Project.project_name'] ? dataContext['Project.project_name'] : '';
                var currentColumn = columnDef.id.substring(5);
                value = '';
				value_export =  '';
                //draw curent month
                //draw all months
                total = totalDate(currentColumn + '-01-01', currentColumn + '-12-31') + 1;
                for (var i = 0; i < 12; i++) {
                    m = i + 1;
                    m = m < 10 ? '0' + m : m;
                    days = daysInMonth(m, currentColumn);
                    layerWidth = days / total * 100;
                    offsetLeft = totalDate(currentColumn + '-01-01', currentColumn + '-' + m + '-01') / total * 100;
                    if (currentColumn == today.getFullYear() && today.getMonth() == i) {
                        value += '<div class="gantt-current-month" style="width: ' + layerWidth + '%; left: ' + offsetLeft + '%;"></div>';
                    } else {
                        value += '<div class="gantt-month" style="width: ' + layerWidth + '%; left: ' + offsetLeft + '%;"></div>';
                    }
                }
                if (gantts[projectId]) {
                    var start = gantts[projectId].start ? gantts[projectId].start : '',
                            end = gantts[projectId].end ? gantts[projectId].end : '',
                            rstart = gantts[projectId].rstart ? gantts[projectId].rstart : '',
                            rend = gantts[projectId].rend ? gantts[projectId].rend : '',
                            comPlan = gantts[projectId].comPlan ? gantts[projectId].comPlan : 0,
                            comReal = gantts[projectId].comReal ? gantts[projectId].comReal : 0,
                            completed = gantts[projectId].completed ? gantts[projectId].completed : 0;
                    if (start != '00-00-0000' && end != '00-00-0000' && viewInitial) {
                        value += DrawGantt(projectId, start, end, comPlan, currentColumn, _color, 'n', projectName, completed);
						value_export += i18n['plan']+ ': ' + start + '    ' + end + '   '+ completed +'% \n';
						// n is plan
                    }
                    if (rstart != '00-00-0000' && rend != '00-00-0000' && viewReal) {
                        value += DrawGantt(projectId, rstart, rend, comReal, currentColumn, _color, 's', projectName, completed); 
						value_export += i18n['real']+ ': ' + rstart + '    ' + rend + '    '+ completed +'% \n';
						// s is real
                    }
                }
                if (viewStone && stones[projectId] && stones[projectId][currentColumn]) {
                    /**
                     * Tinh tong so ngay
                     */
                    var _start = new Date(currentColumn + '-01-01').getTime();
                    var _end = new Date(currentColumn + '-12-31').getTime();
                    var totalDays = Math.abs(parseInt(_start) - parseInt(_end));
                    totalDays = Math.ceil(totalDays / (1000 * 3600 * 24));
                    /**
                     * tinh vi tri cua milestone
                     */
                    var stoneHtml = '';
                    $.each(stones[projectId][currentColumn], function (index, value) {
                        var _date = value[0] ? value[0].split('-') : '';
                        var _leftStone = new Date(_date[2] + '-' + _date[1] + '-' + _date[0]).getTime();
                        _leftStone = Math.abs(parseInt(_start) - parseInt(_leftStone));
                        _leftStone = Math.ceil(_leftStone / (1000 * 3600 * 24));
                        _leftStone = (_leftStone * 100) / totalDays;
                        if (value[2] == 1) {
                            stoneHtml += '<div class="gantt-msi gantt-msi-green gantt-ms" style="left:' + _leftStone + '%;">';
                        } else {
                            var dStone = new Date(_date[2] + '-' + _date[1] + '-' + _date[0]).getTime();
                            if (today.getTime() > dStone) {
                                stoneHtml += '<div class="gantt-msi gantt-ms" style="left:' + _leftStone + '%;">';
                            } else if (today.getTime() < dStone) {
                                stoneHtml += '<div class="gantt-msi gantt-msi-blue gantt-ms" style="left:' + _leftStone + '%;">';
                            } else {
                                stoneHtml += '<div class="gantt-msi gantt-msi-orange gantt-ms" style="left:' + _leftStone + '%;">';
                            }
                        }
						value_export += value[1] +': ' + _date[0] + '/' + _date[1] + '/' + _date[2] + '\n';
                        stoneHtml += '<div class="hover-stone" style="display:none;">' +
                                '<p class="hover-stone-name">' + value[1] + '</p>' +
                                '<p class="hover-stone-date">' + _date[0] + '/' + _date[1] + '/' + _date[2] + '</p>' +
                                '</div>' +
                                '<i></i><span></span></div><br />';
                    });
                    value += '<div class="st-index-' + projectId + '">' + stoneHtml + '</div>';
                }
				if( $this.isExporting){
					return value_export;
				}
                return Slick.Formatters.HTMLData(row, cell, value, columnDef, dataContext);
            },
            selectFormatter: function (row, cell, value, columnDef, dataContext) {
                var html;
                var xyz = dataContext.DataSet['pm'] == employeeId || dataContext.DataSet['tm'] == employeeId || dataContext.DataSet['cb'] == employeeId;
                //old logic. !isPM || editable[dataContext.id] || xyz
                if (!isPM || (isPM && update_your_form)) {
                    html = '<select style="padding: 2px 15px 2px 5px" rel="no-history" onchange="updatePriority.call(this, \'' + dataContext.id + '\', ' + cell + ')"><option value="0">--</option>';
                    for (var i in listPriorities) {
                        html += '<option value="' + i + '" ' + (i == dataContext.DataSet['Project.project_priority_id'] ? 'selected' : '') + '>' + listPriorities[i] + '</option>';
                    }
                    html += '</select>';
                } else {
                    html = listPriorities[dataContext['Project.project_priority_id']] ? listPriorities[dataContext['Project.project_priority_id']] : '';
                }
                return Slick.Formatters.HTMLData(row, cell, html, columnDef, dataContext);
            },
            selectBoxFormatter: function (row, cell, value, columnDef, dataContext) {
                var html;
                var type, keys;
                /* Update by VN
                 Program and Sub program
                 Remove the possibility for Admin and PM
                 */
                canUpdate = (!isPM || (isPM && update_your_form));
                if (columnDef.field == 'Project.project_type_id') {
                    type = listProjectType;
                    keys = 'Project.project_type_id';
                } else if (columnDef.field == 'Project.project_sub_type_id') {
                    type = listProjectSubType;
                    keys = 'Project.project_sub_type_id';
					
                }else if (columnDef.field == 'Project.project_sub_sub_type_id') {
                    type = listProjectSubSubType;
                    keys = 'Project.project_sub_sub_type_id';
					
                } else if (columnDef.field == 'Project.project_amr_program_id') {
                    type = projectProgram;
                    keys = 'Project.project_amr_program_id';
					
                    canUpdate = false;
                } else if (columnDef.field == 'Project.project_amr_sub_program_id') {
                    type = projectSubProgram;
                    keys = 'Project.project_amr_sub_program_id';
                    canUpdate = false;
                } else {
                    return;
                }
                var xyz = dataContext.DataSet['pm'] == employeeId || dataContext.DataSet['tm'] == employeeId || dataContext.DataSet['cb'] == employeeId;
                if (canUpdate) {
                    html = '<select style="padding: 2px 15px 2px 5px" rel="no-history" data-key="' + keys + '" onchange="updateTypeAndProgram.call(this, \'' + dataContext.id + '\', ' + cell + ')"><option value="0">--</option>';
                    for (var i in type) {
                        html += '<option value="' + i + '" ' + (i == dataContext.DataSet[keys] ? 'selected' : '') + '>' + type[i] + '</option>';
                    }
                    html += '</select>';
                } else {
                    var program_name = type[dataContext[keys]] ? type[dataContext[keys]] : '';
                    html = '<span class="program-name">' + program_name + '</span>';

                }
                return Slick.Formatters.HTMLData(row, cell, html, columnDef, dataContext);
            },
            ImageData: function (row, cell, value, columnDef, dataContext) {
                var dataSet = dataContext.DataSet;
				
				if( $this.isExporting){
					return( $this.i18n[dataSet[columnDef.field]]);
				}
                var xyz = dataContext.DataSet['pm'] == employeeId || dataContext.DataSet['tm'] == employeeId || dataContext.DataSet['cb'] == employeeId;
                var _html = '';
                if (value) {
					 folder = 'new-icon/';
					 if(columnDef.field == 'ProjectAmr.rank'){
						 folder += 'project_rank/';
						_html += '<div class="image-center"><img alt="'+ $this.i18n[dataSet[columnDef.field]] +'"  title="'+ $this.i18n[dataSet[columnDef.field]] +'" src="/img/'+ folder + value + '.png"></div>';
					 }else{
						 _html += '<center>'+ icons_title[value] +'</center>';
					 }
                } else {
                    _html += '<span>&nbsp</span>';
                }
                if (!isPM || editable[dataContext.id] || xyz) {
                    return Slick.Formatters.HTMLData(row, cell, '<div class="change-image" data-id="' + dataContext.id + '" data-image="' + dataSet[columnDef.field] + '" data-key="' + columnDef.field + '" onclick="updateWeathers.call(this)">' + _html + '</div>', columnDef, dataContext);
                } else {
                    return Slick.Formatters.HTMLData(row, cell, '<div class="change-image">' + _html + '</div>', columnDef, dataContext);
                }
            },
            ImageDataNew: function (row, cell, value, columnDef, dataContext) {
                var dataSet = dataContext.DataSet;
				if( $this.isExporting){
					return( $this.i18n[dataSet[columnDef.field]]);
				}
                var xyz = dataContext.DataSet['pm'] == employeeId || dataContext.DataSet['tm'] == employeeId || dataContext.DataSet['cb'] == employeeId;
                var _html = '';
                if (value) {
					if($this.i18n[dataSet[columnDef.field]] == 'Rain'){
						_html += '<center>'+ icons_title['rain'] +'</center>';
					}else if($this.i18n[dataSet[columnDef.field]] == 'Sun'){
						_html += '<center>'+ icons_title['sun'] +'</center>';
					}else if($this.i18n[dataSet[columnDef.field]] == 'Cloud'){
						_html += '<center>'+ icons_title['cloud'] +'</center>';
					}else{
						_html += '<div class="image-center"><img alt="'+ $this.i18n[dataSet[columnDef.field]] +'"  title="'+ $this.i18n[dataSet[columnDef.field]] +'" src="/img/new-icon/'+ value +'.png"></div>';
					}
                } else {
                    _html += '<span>&nbsp</span>';
                }
                if (!isPM || editable[dataContext.id] || xyz) {
                    return Slick.Formatters.HTMLData(row, cell, '<div class="change-image" data-new = "yes" data-id="' + dataContext.id + '" data-image="' + dataSet[columnDef.field] + '" data-key="' + columnDef.field + '" onclick="updateWeathers.call(this)">' + _html + '</div>', columnDef, dataContext);
                } else {
                    return Slick.Formatters.HTMLData(row, cell, '<div class="change-image">' + _html + '</div>', columnDef, dataContext);
                }
            },
            yesNoFormatter: function (row, cell, value, columnDef, dataContext) {
                return Slick.Formatters.HTMLData(row, cell, value == '1' ? '<?php __('Yes') ?>' : '<?php __('No') ?>', columnDef, dataContext);
            },
            floatFormatter: function (row, cell, value, columnDef, dataContext) {
                value = value ? value : 0;
				if( $this.isExporting){
					return value;
				}
                return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ') + '</span>', columnDef, dataContext);
            },
            numberVal: function (row, cell, value, columnDef, dataContext) {
                value = value ? value : 0;
				if( $this.isExporting){
					return value;
				}
                var icon = '';
                if (columnDef.id == 'ProjectBudgetSyn.assign_to_employee' || columnDef.id == 'ProjectBudgetSyn.assign_to_profit_center'
                        || columnDef.id == 'ProjectBudgetSyn.total_costs_var' || columnDef.id == 'ProjectBudgetSyn.internal_costs_var'
                        || columnDef.id == 'ProjectBudgetSyn.external_costs_var' || columnDef.id == 'ProjectBudgetSyn.external_costs_progress'
                        ) {
                    icon = '%';
                }
                return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ') + '' + icon + '</span>', columnDef, dataContext);
            },
            numberValEuro: function (row, cell, value, columnDef, dataContext) {
                value = value ? value : 0;
				if( $this.isExporting){
					return value;
				}
                return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ') + ' ' + budget_settings + ' </span>', columnDef, dataContext);
            },
            numberValManDay: function (row, cell, value, columnDef, dataContext) {
                value = value ? value : 0;
				if( $this.isExporting){
					return value;
				}
                return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ') + ' ' + viewManDay + '</span> ', columnDef, dataContext);
            },
            numberValPercent: function (row, cell, value, columnDef, dataContext) {
                value = value ? value : 0;
                return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ') + ' ' + '%' + '</span> ', columnDef, dataContext);
            },
            linkFormatter: function (row, cell, value, columnDef, dataContext) {
                var idPr = dataContext.id ? dataContext.id : 0;
                if ($checkDisplayProfileScreen == 2) {
                    return '<a href="#" class="show_message_profile project-is-' + idPr + '">' + value + '</a>';
                }
                var linkProjectName = <?php echo json_encode($html->url('/' . $ACLController . '/' . $ACLAction));?>;
                return '<a href=' + linkProjectName + '/' + dataContext['id'] + ' class="project-is-' + idPr + '">' + value + '</a>';
            },
            projectFavorite: function (row, cell, value, columnDef, dataContext) {
                var idPr = dataContext.id ? dataContext.id : 0;
				if( $this.isExporting){
					return favorites[idPr] == 1 ? '*' : '';
				}
				var has_favorite = favorites[idPr] == 1 ? i18ns.remove_favorite : i18ns.add_favorite;
				var favorite_class = favorites[idPr] == 1 ? 'favorite' : '';
                return '<a id="favorite-'+ idPr +'" href="javascript: void(0);" title="'+ has_favorite +'" class="project-favorite-action '+ favorite_class +'" onclick="toggleFavoriteProject('+ idPr +');">'+ svg_icons["star"]+'</a>';
            },
            avaResource: function (row, cell, value, columnDef, dataContext) {
                avatar = '';
                $.each(value, function (key, val) {
                    if (!$this.isExporting) {
                        avatar += avatar_html(val);
                    }
					if( $this.isExporting ){
						if( typeof list_avatar[val] != 'undefined'){
							avatar += ( avatar ?  ', ' : '');
							avatar += employee_name(val);
						}
					}
                });
                return avatar;
            },
            avaProjectManager: function (row, cell, value, columnDef, dataContext) {
                listResource = [];
                field = columnDef.field;
                if (field == 'Project.chief_business_id') {
                    listResource = _chief_business_list;
                } else if (field == 'Project.technical_manager_id') {
                    listResource = _technical_manager_list;
                } else if (field == 'Project.read_access') {
                    listResource = project_read_access_manager_list;
                    // console.log(listResource);  
                } else if (field == 'Project.functional_leader_id') {
                    listResource = _functional_leader_list;
                } else {
                    listResource = _uat_manager_list;
                }
                
                avatar = '';
                if (listResource[dataContext.id]) {
                    $.each(listResource[dataContext.id], function (key, val) {
                        if (!$this.isExporting) {
                            if (field == 'Project.read_access'){
                                if( val.is_profit_center == '1'){   
                                    avatar += avatar_html(val.id + '-1');
                                }else avatar += avatar_html(val.id);
                              
                            }else{
                                avatar += avatar_html(val);
                            }
							
						}
						if( $this.isExporting ){
                            avatar += ( avatar ?  ', ' : '');
                            if(field == 'Project.read_access'){
                                if (val.is_profit_center == '1'){
                                    avatar += employee_name(val.id + '-1');
                                } else avatar += employee_name(val.id);
                            }else {
                                avatar += employee_name(val);
                            }
						}
                    });
                }

                return avatar;
            },
            nextMilestone: function (row, cell, value, columnDef, dataContext) {
                var projectId = dataContext.id ? dataContext.id : 0;
                if (projectId != 0 && listColorNextMil[projectId]) {
                    return '<div style="text-align: center; background-color: ' + listColorNextMil[projectId] + '">' + value + '</div>';
                }
                return '<div style="text-align: center;">' + value + '</div>'
            },
			uploadDocument: function (row, cell, value, columnDef, dataContext) {
				var _html = '';
				if( $this.isExporting){
					// if( !value.length) return '';
					$.each(value, function( ind, file){
						if( !_html) _html += '<br/>';
						_html += file.file_attachment;
					});
					return _html;
				}
				var count = 0;
				var field = columnDef['field'].replace('Project.', '');
				// if( !value.length) return '';
				$.each(value, function( ind, file){
					count++;
				});
				var src = Azuree.root + 'img/new-icon/' + (count ? 'ic-upload-read' : 'ic-upload') + '.png';
				return '<p class="wd-open-popup center_middle ' + (count ? 'has-file' : 'no-file') + '" data-key="' + field + '" data-id="' + dataContext.id + '" onclick="open_multi_upload.call(this)" ><img src="' + src + '" alt="upload"/></p>';
			},
			// widgetBudget: function (row, cell, value, columnDef, dataContext) {
				// if( $this.isExporting){
					// return value;
				// }
				// var widget_name = columnDef['field'].replace('ProjectWidget.', '');
				// var html = $(widgetBudget_html);
				// html.find('.wd-widget-compare').siblings().remove();
				// var text = {};
				// html.addClass(widget_name);
				// if ( widget_name in text_widget) {
					// text = text_widget[widget_name];
					// var arr_data = dataContext.DataSet[widget_name];
					// if( 'left' in arr_data){
						// var l_val = $.isArray(arr_data['left']) ? ( '<span class="text">' + arr_data['left'].join('</span><span>') + '</span>') : arr_data['left'];
						// var left = html.find('.left').removeClass('hidden');
						// left.find('.wg-label').html(text['left']);
						// left.find('.wg-value').html(l_val);
					// }else{
						// html.find('.left').remove();
					// }
					// if( 'right' in arr_data){
						// var r_val = $.isArray(arr_data['right']) ? ( '<span class="text">' + arr_data['right'].join('</span><span>') + '</span>') : arr_data['right'];
						// var right = html.find('.right').removeClass('hidden');
						// right.find('.wg-label').html(text['right']).attr('title', text['right']);
						// right.find('.wg-value').html(r_val);
						// if( 'total' in arr_data){
							// switch(widget_name){
								// case 'Synthesis':
								// case 'FinancePlus':
									// class_number = (parseFloat( arr_data['total']) > 100 ) ? 'over-threshold red-color' : 'green-color';
									// break;
								// default:
									// class_number = (parseFloat( arr_data['total']) > 0 ) ? 'positive-number red-color' : 'negative-number green-color';
							// }
							// right.find('.wd-value-total').removeClass('hidden').addClass(class_number).html(arr_data['total']);
						// }else{
							// html.find('.wd-value-total').remove();
						// }
					// }else{
						// html.find('.right').remove();
					// }
					// html.find('.wd-value-inner').addClass( (html.find('.wd-value-content').length == 2) ? 'two-item' : 'one-item');
					// return html[0].outerHTML;
				// }
				// return value;
			// },
			// widgetProgress: function (row, cell, value, columnDef, dataContext) {
				// if( $this.isExporting){
					// return value;
				// }
				// value = parseFloat(value);
				// var display_val =  parseInt(Math.min(Math.max( value, 0), 100));
				// var widget_name = columnDef['field'].replace('ProjectWidget.', '');
				// var html = $(widgetBudget_html);
				// html.find('.wd-widget-progress').siblings().remove();
				// html.find('.wd-progress-slider').attr('data-value', value).addClass( (value > 100) ? 'red-line' : 'green-line');
				// html.find('.wd-progress-value-line').css('width', display_val + '%');
				// html.find('.wd-progress-number').css('left', display_val + '%').find('.text').html(display_val + '%');
				// html.find('.wd-progress-number').find('.input-progress').attr('value', value).val(value);
				// return html[0].outerHTML;
			// },
			widgetProject_progress: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				value = parseFloat(dataContext['ProjectWidget.Project_progress']);
				var display_val =  parseInt(Math.min(Math.max( value, 0), 100));
				var html = '<div class="wd-widget-progress"><div class="wd-progress-slider"><div class="wd-progress-holder"><div class="wd-progress-line-holder"></div></div><div class="wd-progress-value-line" style="width:' + display_val + '%;"></div><div class="wd-progress-value-text"><div class="wd-progress-value-inner"><div class="wd-progress-number" style="left:' + display_val + '%;"><div class="text">' + value + '%</div><input class="input-progress wd-hide" value="' + value + '"></div></div></div></div></div>';
				return html;
			},
			widgetPhase_progress: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				value = parseFloat(value);
				var display_val =  parseInt(Math.min(Math.max( value, 0), 100));
				var html = $(widgetBudget_html);
				html.find('.wd-widget-progress').siblings().remove();
				html.find('.wd-progress-slider').attr('data-value', value).addClass( (value > 100) ? 'red-line' : 'green-line');
				html.find('.wd-progress-value-line').css('width', display_val + '%');
				html.find('.wd-progress-number').css('left', display_val + '%').find('.text').html(display_val + '%');
				html.find('.wd-progress-number').find('.input-progress').attr('value', value).val(value);
				if( dataContext['DataSet']['Phase_plan']['diff'] != ''){
					var diff = parseFloat( dataContext['DataSet']['Phase_plan']['diff']);
					if( diff > 0 ){
						html.addClass('red-color');
					}
				}
				return html[0].outerHTML;
			},
			widgetPhase_real: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				// return dataContext['DataSet']['Phase_real']['date'];
				var classes = {};
				if( dataContext['DataSet']['Phase_plan']['diff'] != ''){
					var diff = parseFloat( dataContext['DataSet']['Phase_plan']['diff']);
					if( diff > 0 ){
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				return drawCellWidget('Phase', $this.t('real_end_date'), value, '', false, classes);
			},
			widgetPhase_plan: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				var classes = {};
				var diff = false;
				if( dataContext['DataSet']['Phase_plan']['diff'] !== ''){
					diff = parseFloat( dataContext['DataSet']['Phase_plan']['diff']);
					if( diff > 0 ){
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				return drawCellWidget('Phase', $this.t('plan_end_date'), value, '', false, classes);
			},
			widgetPhase_diff: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				var classes = {};
				if( parseFloat(value) > 0 ){
					classes['wd-widget'] = "red-color";
					classes['wd-value-total'] = "red-color";
				}
				return drawCellWidget('Phase','&nbsp;', '&nbsp;', '', value, classes);
			},
			widgetMilestone_late: function (row, cell, value, columnDef, dataContext) {
				var milestone_val = dataContext.DataSet.Milestone_late.value;
				if( $this.isExporting){
					return (milestone_val && typeof milestone_val.text !== 'undefined') ? (milestone_val.text + ' ' + milestone_val.date) : '';
				}
				var classes = {
					'wd-widget': 'red-color'
				};
				var title = '';
				if( milestone_val){
					title = milestone_val.text + ' ' + milestone_val.date;
				}
				return drawCellWidget('Milestone', $this.t('Late milestone'), milestone_val, title, false, classes);
			},
			widgetMilestone_next: function (row, cell, value, columnDef, dataContext) {
				var milestone_val = dataContext.DataSet.Milestone_next.value;
				if( $this.isExporting){
					return (milestone_val && typeof milestone_val.text !== 'undefined') ? (milestone_val.text + ' ' + milestone_val.date) : '';
				}
				var title = '';
				if( milestone_val){
					title = milestone_val.text + ' ' + milestone_val.date;
				}
				return drawCellWidget('Milestone', $this.t('Next milestone'), milestone_val, title, false, false);
			},
			widgetFinancePlus_inv_budget: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				value = number_format(value, 2, '.', ' ') + ' ' + budget_settings;
				var classes = {};
				if( dataContext['ProjectWidget.FinancePlus_percent']){
					var p = parseFloat( dataContext['ProjectWidget.FinancePlus_inv_percent']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				return drawCellWidget('Finance', $this.t('invBudget'), value, '', false, classes);
			},
			widgetFinancePlus_inv_engaged: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				value = number_format(value, 2, '.', ' ') + ' ' + budget_settings;
				var classes = {};
				if( dataContext['ProjectWidget.FinancePlus_inv_percent']){
					var p = parseFloat( dataContext['ProjectWidget.FinancePlus_inv_percent']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				return drawCellWidget('Finance', $this.t('invEngaged'), value, '', false, classes);
			},
			widgetFinancePlus_inv_percent: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				var classes = {};
				var p = parseFloat( value );
				if( p > 100) {
					classes['wd-widget'] = "red-color";
					classes['wd-value-total'] = "red-color";
				}
				return drawCellWidget('Finance', '&nbsp;', '&nbsp;', '', value, classes);
			},
			widgetFinancePlus_fon_budget: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				value = number_format(value, 2, '.', ' ') + ' ' + budget_settings;
				var classes = {};
				if( dataContext['ProjectWidget.FinancePlus_fon_percent']){
					var p = parseFloat( dataContext['ProjectWidget.FinancePlus_fon_percent']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				return drawCellWidget('Finance', $this.t('fonBudget'), value, '', false, classes);
			},
			widgetFinancePlus_fon_engaged: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				value = number_format(value, 2, '.', ' ') + ' ' + budget_settings;
				var classes = {};
				if( dataContext['ProjectWidget.FinancePlus_fon_percent']){
					var p = parseFloat( dataContext['ProjectWidget.FinancePlus_fon_percent']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				return drawCellWidget('Finance', $this.t('fonEngaged'), value, '', false, classes);
			},
			widgetFinancePlus_fon_percent: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				var classes = {};
				var p = parseFloat( value );
				if( p > 100) {
					classes['wd-widget'] = "red-color";
					classes['wd-value-total'] = "red-color";
				}
				return drawCellWidget('Finance', '&nbsp;', '&nbsp;', '', value, classes);
			},
			widgetFinancePlus_finaninv_budget: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				value = number_format(value, 2, '.', ' ') + ' ' + budget_settings;
				var classes = {};
				if( dataContext['ProjectWidget.FinancePlus_finaninv_percent']){
					var p = parseFloat( dataContext['ProjectWidget.FinancePlus_finaninv_percent']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				return drawCellWidget('Finance', $this.t('finaninvBudget'), value, '', false, classes);
			},
			widgetFinancePlus_finaninv_engaged: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				value = number_format(value, 2, '.', ' ') + ' ' + budget_settings;
				var classes = {};
				if( dataContext['ProjectWidget.FinancePlus_finaninv_percent']){
					var p = parseFloat( dataContext['ProjectWidget.FinancePlus_finaninv_percent']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				return drawCellWidget('Finance', $this.t('finaninvEngaged'), value, '', false, classes);
			},
			widgetFinancePlus_finaninv_percent: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				var classes = {};
				var p = parseFloat( value );
				if( p > 100) {
					classes['wd-widget'] = "red-color";
					classes['wd-value-total'] = "red-color";
				}
				return drawCellWidget('Finance', '&nbsp;', '&nbsp;', '', value, classes);
			},
			widgetFinancePlus_finanfon_budget: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				value = number_format(value, 2, '.', ' ') + ' ' + budget_settings;
				var classes = {};
				if( dataContext['ProjectWidget.FinancePlus_finanfon_percent']){
					var p = parseFloat( dataContext['ProjectWidget.FinancePlus_finanfon_percent']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				return drawCellWidget('Finance', $this.t('finanfonBudget'), value, '', false, classes);
			},
			widgetFinancePlus_finanfon_engaged: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				value = number_format(value, 2, '.', ' ') + ' ' + budget_settings;
				var classes = {};
				if( dataContext['ProjectWidget.FinancePlus_finanifon_percent']){
					var p = parseFloat( dataContext['ProjectWidget.FinancePlus_finanfon_percent']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				return drawCellWidget('Finance', $this.t('finanfonEngaged'), value, '', false, classes);
			},
			widgetFinancePlus_finanfon_percent: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				var classes = {};
				var p = parseFloat( value );
				if( p > 100) {
					classes['wd-widget'] = "red-color";
					classes['wd-value-total'] = "red-color";
				}
				return drawCellWidget('Finance', '&nbsp;', '&nbsp;', '', value, classes);
			},
			widgetSynthesis_budget: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				value = number_format(value, 2, '.', ' ') + ' ' + budget_settings;
				var classes = {};
				if( dataContext['ProjectWidget.Synthesis_percent']){
					var p = parseFloat( dataContext['ProjectWidget.Synthesis_percent']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				return drawCellWidget('Synthesis', $this.t('BudgetEuro'), value, '', false, classes);
			},
			widgetSynthesis_forecast: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				value = number_format(value, 2, '.', ' ') + ' ' + budget_settings;
				var classes = {};
				if( dataContext['ProjectWidget.Synthesis_percent']){
					var p = parseFloat( dataContext['ProjectWidget.Synthesis_percent']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				return drawCellWidget('Synthesis', $this.t('ForecastEuro'), value, '', false, classes);
			},
			widgetSynthesis_percent: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				var classes = {};
				var p = parseFloat( value );
				if( p > 100) {
					classes['wd-widget'] = "red-color";
					classes['wd-value-total'] = "red-color";
				}
				return drawCellWidget('Synthesis', '&nbsp;', '&nbsp;', '', value, classes);
			},
			widgetInternal_budget_md: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				value = number_format(value, 2, '.', ' ') + ' ' + viewManDay;
				var classes = {};
				if( dataContext['ProjectWidget.Internal_percent_forecast_md']){
					var p = parseFloat( dataContext['ProjectWidget.Internal_percent_forecast_md']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				if( dataContext['ProjectWidget.Internal_percent_consumed_md']){
					var p = parseFloat( dataContext['ProjectWidget.Internal_percent_consumed_md']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				return drawCellWidget('InternalBudget', $this.t('Internal Budget M.D'), value, '', false, classes);
			},
			widgetInternal_forecast_md: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				value = number_format(value, 2, '.', ' ') + ' ' + viewManDay;
				var classes = {};
				if( dataContext['ProjectWidget.Internal_percent_forecast_md']){
					var p = parseFloat( dataContext['ProjectWidget.Internal_percent_forecast_md']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				if( dataContext['ProjectWidget.Internal_percent_consumed_md']){
					var p = parseFloat( dataContext['ProjectWidget.Internal_percent_consumed_md']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				return drawCellWidget('InternalBudget', $this.t('Internal Forecast M.D'), value, '', false, classes);
			},
			widgetInternal_percent_forecast_md: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				var classes = {};
				var p = parseFloat( value );
				if( p > 100) {
					classes['wd-widget'] = "red-color";
					classes['wd-value-total'] = "red-color";
				}
				if( dataContext['ProjectWidget.Internal_percent_consumed_md']){
					var p = parseFloat( dataContext['ProjectWidget.Internal_percent_consumed_md']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				return drawCellWidget('InternalBudget', '&nbsp;', '&nbsp;', '', value, classes);
			},
			widgetInternal_consumed_md: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				value = number_format(value, 2, '.', ' ') + ' ' + viewManDay;
				var classes = {};
				if( dataContext['ProjectWidget.Internal_percent_forecast_md']){
					var p = parseFloat( dataContext['ProjectWidget.Internal_percent_forecast_md']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				if( dataContext['ProjectWidget.Internal_percent_consumed_md']){
					var p = parseFloat( dataContext['ProjectWidget.Internal_percent_consumed_md']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				return drawCellWidget('InternalBudget', $this.t('Internal Engaged M.D'), value, '', false, classes);
			},
			widgetInternal_percent_consumed_md: function (row, cell, value, columnDef, dataContext) {
				
				if( $this.isExporting){
					return value;
				}
				var classes = {};
				var p = parseFloat( value );
				if( p > 100) {
					classes['wd-widget'] = "red-color";
					classes['wd-value-total'] = "red-color";
				}
				if( dataContext['ProjectWidget.Internal_percent_forecast_md']){
					var p = parseFloat( dataContext['ProjectWidget.Internal_percent_forecast_md']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				return drawCellWidget('InternalBudget', '&nbsp;', '&nbsp;', '', value, classes);
			},
			widgetInternal_budget_euro: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				value = number_format(value, 2, '.', ' ') + ' ' + budget_settings;
				var classes = {};
				if( dataContext['ProjectWidget.Internal_percent_forecast_euro']){
					var p = parseFloat( dataContext['ProjectWidget.Internal_percent_forecast_euro']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				if( dataContext['ProjectWidget.Internal_percent_consumed_euro']){
					var p = parseFloat( dataContext['ProjectWidget.Internal_percent_consumed_euro']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				return drawCellWidget('InternalBudget', $this.t('Internal Budget €'), value, '', false, classes);
			},
			widgetInternal_forecast_euro: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				value = number_format(value, 2, '.', ' ') + ' ' + budget_settings;
				var classes = {};
				if( dataContext['ProjectWidget.Internal_percent_forecast_euro']){
					var p = parseFloat( dataContext['ProjectWidget.Internal_percent_forecast_euro']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				if( dataContext['ProjectWidget.Internal_percent_consumed_euro']){
					var p = parseFloat( dataContext['ProjectWidget.Internal_percent_consumed_euro']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				return drawCellWidget('InternalBudget', $this.t('Internal Forecast €'), value, '', false, classes);
			},
			widgetInternal_percent_forecast_euro: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				var classes = {};
				var p = parseFloat( value );
				if( p > 100) {
					classes['wd-widget'] = "red-color";
					classes['wd-value-total'] = "red-color";
				}
				if( dataContext['ProjectWidget.Internal_percent_consumed_euro']){
					var p = parseFloat( dataContext['ProjectWidget.Internal_percent_consumed_euro']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				return drawCellWidget('InternalBudget', '&nbsp;', '&nbsp;', '', value, classes);
			},
			widgetInternal_engaged_euro: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				value = number_format(value, 2, '.', ' ') + ' ' + budget_settings;
				var classes = {};
				if( dataContext['ProjectWidget.Internal_percent_forecast_euro']){
					var p = parseFloat( dataContext['ProjectWidget.Internal_percent_forecast_euro']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				if( dataContext['ProjectWidget.Internal_percent_consumed_euro']){
					var p = parseFloat( dataContext['ProjectWidget.Internal_percent_consumed_euro']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				return drawCellWidget('InternalBudget', $this.t('Internal Engaged €'), value, '', false, classes);
			},
			widgetInternal_percent_consumed_euro: function (row, cell, value, columnDef, dataContext) {
				
				if( $this.isExporting){
					return value;
				}
				var classes = {};
				var p = parseFloat( value );
				if( p > 100) {
					classes['wd-widget'] = "red-color";
					classes['wd-value-total'] = "red-color";
				}
				if( dataContext['ProjectWidget.Internal_percent_forecast_euro']){
					var p = parseFloat( dataContext['ProjectWidget.Internal_percent_forecast_euro']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				return drawCellWidget('InternalBudget', '&nbsp;', '&nbsp;', '', value, classes);
			},
			widgetExternal_budget_erro: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				var classes = {};
				if( value > 0 ){
					var forecast_erro = parseFloat( dataContext['ProjectWidget.External_forecast_erro']);
					if( value < forecast_erro) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				
				value = number_format(value, 2, '.', ' ') + ' ' + budget_settings;	
				return drawCellWidget('ExternalBudget', $this.t('External Budget Euro'), value, '', false, classes);
			},
			widgetExternal_forecast_erro: function (row, cell, value, columnDef, dataContext) {
				
				if( $this.isExporting){
					return value;
				}
				value = number_format(value, 2, '.', ' ') + ' ' + budget_settings;
				var classes = {};
				var budget_erro = parseFloat( dataContext['ProjectWidget.External_budget_erro']);
				if( budget_erro > 0 ){
					var forecast_erro = parseFloat( dataContext['ProjectWidget.External_forecast_erro']);
					if( budget_erro < forecast_erro) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				
				return drawCellWidget('ExternalBudget', $this.t('External Forecast Euro'), value, '', false, classes);
			},
			widgetExternal_percent_forecast_erro: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				var classes = {};
				var p = parseFloat( value );
				if( p > 100) {
					classes['wd-widget'] = "red-color";
					classes['wd-value-total'] = "red-color";
				}
				if( dataContext['ProjectWidget.External_percent_ordered_erro']){
					var p = parseFloat( dataContext['ProjectWidget.External_percent_ordered_erro']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				return drawCellWidget('ExternalBudget', '&nbsp;', '&nbsp;', '', value, classes);
			},
			widgetExternal_ordered_erro: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				value = number_format(value, 2, '.', ' ') + ' ' + budget_settings;
				var classes = {};
				var budget_erro = parseFloat( dataContext['ProjectWidget.External_budget_erro']);
				if( budget_erro > 0 ){
					var forecast_erro = parseFloat( dataContext['ProjectWidget.External_forecast_erro']);
					if( budget_erro < forecast_erro) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				
				return drawCellWidget('ExternalBudget', $this.t('External Ordered Euro'), value, '', false, classes);
			},
			widgetExternal_percent_ordered_erro: function (row, cell, value, columnDef, dataContext) {
				if( $this.isExporting){
					return value;
				}
				var classes = {};
				var p = parseFloat( value );
				if( p > 100) {
					classes['wd-widget'] = "red-color";
					classes['wd-value-total'] = "red-color";
				}
				if( dataContext['ProjectWidget.External_percent_forecast_erro']){
					var p = parseFloat( dataContext['ProjectWidget.External_percent_forecast_erro']);
					if( p > 100) {
						classes['wd-widget'] = "red-color";
						classes['wd-value-total'] = "red-color";
					}
				}
				return drawCellWidget('ExternalBudget', '&nbsp;', '&nbsp;', '', value, classes);
			},
        });
		function drawCellWidget(name, label, value, title, total_value, classes){
			var _v = value;
			if( typeof value == 'object'){
				_v = '';
				$.each( value, function( i,v){
					_v += '<span class="' + i + '">' + v + '</span>';
				});
			}
			if( value == ''){
				return '<div class="wd-widget wd-widget-in-cell ' + name +'"></div>';
			}
			var html = '<div><div class="wd-widget wd-widget-in-cell ' + name +'"><div class="wd-widget-compare"><div class="wd-value-inner"><div class="wd-value-content" title="'+title+'"><p class="wg-label">' + label + '</p><span class="wg-value">' + _v  + '</span>';
			if( total_value !== false){
				html += '<span class="wd-value-total">' + total_value + '</span>';
			}
			html +=  '</div></div></div></div></div>';
			if( classes){
				var _html = $(html);
				$.each( classes, function(k,c){
					var e = _html.find('.' + k);
					if( e.length) e.addClass(c);
				});
				html = _html.html();
			}
			return html;
		}
        var data = <?php echo json_encode($dataView); ?>;
        var columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator', 'asyncPostRender', 'customFilterDisplay', 'customFilterFunction')); ?>;
        var leftColumns = <?php echo json_encode($leftColumns);?>;
        $this.canModified = true;
        $this.fields = {
            id: {defaulValue: 0},
            'ProjectAmr.comment': {defaulValue: ''},
            'ProjectAmr.project_amr_solution': {defaulValue: ''},
            'ProjectAmr.project_amr_risk_information': {defaulValue: ''},
            'ProjectAmr.project_amr_problem_information': {defaulValue: ''},
            'ProjectAmr.done': {defaulValue: ''},
            'ProjectAmr.todo': {defaulValue: ''},
            'ProjectAmr.project_amr_scope': {defaulValue: ''},
        };
        $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
        $this.url = '<?php echo $html->url(array('action' => 'update')); ?>';
        $this.columnCalculationConsumeds = <?php echo json_encode($columnCalculationConsumeds);?>;
        $this.columnAlignRightAndEuro = <?php echo json_encode($columnAlignRightAndEuro);?>;
        $this.widgetCaculateTotalEuro = <?php echo json_encode($widgetCaculateTotalEuro);?>;
        $this.widgetCaculateTotalPercent = <?php echo json_encode($widgetCaculateTotalPercent);?>;
        $this.widgetCaculateTotalMD = <?php echo json_encode($widgetCaculateTotalMD);?>;
        $this.columnAlignRightAndManDay = <?php echo json_encode($columnAlignRightAndManDay);?>;
        $this.columnAlignRightAndPercent = <?php echo json_encode($columnAlignRightAndPercent);?>;
        $this.columnNotCalculationConsumed = <?php echo json_encode($columnNotCalculationConsumed);?>;
        $this.i18n = <?php echo json_encode($i18n); ?>;
		var rowHeight = (viewStone) ? 45 : 40;
		if( filter_render['comment_column_height'] && first_commment_column){
			var rowHeightHistory = filter_render['comment_column_height'];
			rowHeight = Math.max( rowHeight,rowHeightHistory);
		}
        var options = {
            headerRowHeight: 40,
            enableAddRow: false,
            rowHeight: rowHeight,
            gantt: true,
            // frozenColumn: leftColumns - 1
            frozenColumn: '',
			enableAsyncPostRender: true,
			asyncPostRenderDelay: 100
        };
		var mql = window.matchMedia('printer');
		if( mql.matches) { $options.autoHeight = true;}
        ControlGrid = $this.init($('#project_container'), data, columns, options);
		update_table_height();
		// add_print_button_for_firefox();
		var dataView = ControlGrid.getDataView();
		if( typeof Dashboard != 'undefined'){
			if( !$.isEmptyObject(Dashboard)){
				Dashboard.set('display', isDisplayDashboard);
				display_dashboard();
			}
		}
        var exporter = new Slick.DataExporter('/projects/export_excel_index');
        ControlGrid.registerPlugin(exporter);

        $('#export-table').click(function () {
			$this.isExporting = 1;
            exporter.submit();
			$this.isExporting = 0;
            return false;
        });
		$("body").on("click", ".scroll-progress", function() {
			var dir = $(this).hasClass('scroll-progress-right') ? '+=' : '-=' ;
			$(this).closest(".dash-progress-line").find('.progress-line-inner').stop().animate({scrollLeft: dir+'500'}, 1000);
		});
        var _gridView;
		ControlGrid.setRowHeightCallback = function(height, isRender){
			if( !height ) height = (viewStone) ? 45 : 40;
			ControlGrid.setOptions({rowHeight:height});
			$('#css_custom_height').text(
				'#project_container .slick-viewport .grid-canvas .slick-row .slick-cell{height: ' + height + 'px; line-height: ' + height + 'px;} #project_container .slick-cell.wd-open-popup .comment-text .comment{ max-height: ' + (height-18) + 'px;}'
			);
			if( isRender||0){
				first_resize_column_run = 0;
				ControlGrid.invalidate();
				ControlGrid.render();
			}
			if( $('#comment-column-height').length) $('#comment-column-height').val(height).trigger('change');
		};
        $this.onContextMenu = function (gridView) {
            _gridView = gridView;
            var cell = gridView.grid.getCellFromEvent(gridView.record);
            var currentRows = gridView.grid.getData().getItems()[cell.row];
            if (!currentRows) {
                return;
            } else {
                if (currentRows.project_budget_sale_id) {
                    return;
                }
            }
            $('#contextMenu-project')
                    .data("row", cell.row)
                    .css("top", gridView.record.pageY)
                    .css("left", gridView.record.pageX)
                    .show();
            $("body").one("click", function () {
                $('#contextMenu-project').hide();
            });
            // $('#contextMenu-project').on( "mousedown", t);
        }
        ControlGrid.onMouseLeave.subscribe(function (e, args) {
            jQuery(e.target).removeClass('hoverCell');
            clearInterval(counter);
        });
        ControlGrid.onMouseEnter.subscribe(function (e, args) {
            if (!enable_popup)
                return;
            var cell = args.grid.getCellFromEvent(e);
            var me = args.grid.getData().getItem(cell.row);
            var column_subj = columns[cell.cell].field.split('.')[0];


            // alert(columns[cell.cell].field);
            if (columns[cell.cell].field == 'Project.created_value') {
                clearInterval(counter);
                jQuery(e.target).addClass('hoverCell');
                counter = setInterval(function () {
                    jQuery(e.target).addClass('loading');
                    showProjectCreatedVals(e, me.id)
                }, 2000);
            } else if (column_subj == 'ProjectFinancePlus') {
                clearInterval(counter);
                jQuery(e.target).addClass('hoverCell');
                var _cat = '';
                if (columns[cell.cell].field.indexOf("inv") >= 0)
                    _cat = 'investissement';
                if (columns[cell.cell].field.indexOf("fon") >= 0)
                    _cat = 'fonctionnement';
                if (columns[cell.cell].field.indexOf("finaninv") >= 0)
                    _cat = 'finan_investissement';
                if (columns[cell.cell].field.indexOf("finanfon") >= 0)
                    _cat = 'finan_fonctionnement';
                counter = setInterval(function () {
                    jQuery(e.target).addClass('loading');
                    showProjectFinanceGlobalViews(e, me.id, _cat)
                }, 2000);
            } else {
                if (!see_budget && isPm) {
                    return;
                }
                var field = columns[cell.cell].field.substr(0, 16);
                if (field == 'ProjectBudgetSyn' && columns[cell.cell].field != 'ProjectBudgetSyn.assign_to_profit_center' && columns[cell.cell].field != 'ProjectBudgetSyn.assign_to_employee') {
                    clearInterval(counter);
                    jQuery(e.target).addClass('hoverCell');
                    counter = setInterval(function () {
                        jQuery(e.target).addClass('loading');
                        showProjectBudgetSyn(e, me.id)
                    }, 2000);
                }
            }
        });
        ControlGrid.onScroll.subscribe(function (args, e, scope) {
            $('.row-parent').parent().addClass('row-parent-custom');
            $('.row-disabled').parent().addClass('row-disabled-custom');
            $('.row-number').parent().addClass('row-number-custom');
        });
        ControlGrid.onColumnsResized.subscribe(function (e, args) {
            resizeHandler();
            setupScroll();
            $('.row-parent').parent().addClass('row-parent-custom');
            $('.row-disabled').parent().addClass('row-disabled-custom');
            $('.row-number').parent().addClass('row-number-custom');
        });
        var dataView = ControlGrid.getDataView();
        dataView.onRowCountChanged.subscribe(function (e, args) {
            var _leng = ControlGrid.getDataLength();
            var _data = '';
            var _i = 0;
            for (_i = 0; _i < _leng; _i++) {
                if (_data)
                    _data += '-' + ControlGrid.getDataItem(_i).id;
                else
                    _data += ControlGrid.getDataItem(_i).id;
            }
            $.ajax({
                type: 'POST',
                url: '/projects/save_project_list_filter',
                data: {
                    path: 'project_list_filter',
                    params: _data,
                },
                cache: false,
                success: function (respon) {
                }
            });
        });
        $(ControlGrid.getHeaderRow()).delegate(":input", "change keyup", function (e) {
            var text = $(this).val();
            if (text != '') {
                $(this).parent().css('border', 'solid 1px #E9E9E9');
            } else {
                $(this).parent().css('border', 'none');
            }
        });
        /**
         * Calculation width of grid.
         */
        var cols = ControlGrid.getColumns();
        var numCols = cols.length;
        var gridW = 0;
        for (var i = 0; i < numCols; i++) {
            // if ($.inArray(cols[i].id, columnsOfGantt) != -1 && viewGantt) {
                // do nothing
            // } else {
                gridW += cols[i].width;
            // }
        }
		
		// Luu y: Khi update summary dong thoi update chuc nang filter re-calculate summary o file (slick_grid_custom.js)
        if (columns) {
            var headerConsumed = '<div id="wd-header-custom" class="slick-headerrow-columns" style="width: ' + gridW + 'px">';
			var exClass = '';
			var key = 'tmp';
			var colorClass = [];
            $.each(columns, function (index, value) {
				var idOfHeader = value.id;
                var valOfHeader = (totalHeaders[idOfHeader] || totalHeaders[idOfHeader] == 0) ? totalHeaders[idOfHeader] : '';
				if ($.inArray(idOfHeader, columnsOfGantt) != -1 && viewGantt) {
                    //do nothing
                } else {
					if(((value.name).trim()).length > 0){
						key = value.id;
					}
					if ($.inArray(idOfHeader, $this.widgetCaculateTotalPercent) != -1) {
						colorClass[key] = !(colorClass[key]) ? '' : colorClass[key];
						if(valOfHeader > 100){
							colorClass[key] =  ' red-color';
						}
					}
				}
			});
			
            $.each(columns, function (index, value) {
                var idOfHeader = value.id;
				var statusColor = '';
				var fieldName = (value.field).split('.');
				var isFieldWidget = fieldName[0];
				if(isFieldWidget == 'ProjectWidget' && exClass.length == 0){
					exClass = 'wd-sum-even';
				}
                var valOfHeader = (totalHeaders[idOfHeader] || totalHeaders[idOfHeader] == 0) ? totalHeaders[idOfHeader] : '';
                if ($.inArray(idOfHeader, columnsOfGantt) != -1 && viewGantt) {
                    //do nothing
                } else {
                    if ($.inArray(idOfHeader, $this.columnAlignRightAndManDay) != -1) {
                        valOfHeader = number_format(valOfHeader, 2, ',', ' ') + ' ' + viewManDay;
                    } else if ($.inArray(idOfHeader, $this.columnAlignRightAndEuro) != -1) {
                        valOfHeader = number_format(valOfHeader, 2, ',', ' ') + ' ' + budget_settings;
                    } else if ($.inArray(idOfHeader, $this.widgetCaculateTotalEuro) != -1) {
                        valOfHeader = number_format(valOfHeader, 2, '.', ' ') + ' ' + budget_settings;
                    } else if ($.inArray(idOfHeader, $this.widgetCaculateTotalMD) != -1) {
                        valOfHeader = number_format(valOfHeader, 2, '.', ' ') + ' ' + viewManDay;
                    } else if ($.inArray(idOfHeader, $this.widgetCaculateTotalPercent) != -1) {
                        valOfHeader = '<span class="wd-sum-percent">' + number_format(valOfHeader, 2, '.', ' ') + '%' + '</span>';
                    } else {
                        if (valOfHeader) {
                            valOfHeader = number_format(valOfHeader, 2, ',', ' ');
                        }
                    }
					hasLabel = labelSummaryHeader[idOfHeader] ? 'hasLabel' : '';
					textLabel = labelSummaryHeader[idOfHeader] ? labelSummaryHeader[idOfHeader]  : '';
                    idOfHeader = idOfHeader.replace('.', '_');
                    var left = 'l' + index;
                    var right = 'r' + index;
					if(((value.name).trim()).length > 0 && isFieldWidget == 'ProjectWidget'){
						statusColor = colorClass[value.id] ? colorClass[value.id] : '';
						if(exClass == 'wd-sum-even'){
							exClass = 'wd-sum-odd';
						}else{
							exClass = 'wd-sum-even';
						}
					}
					labelHtml = '';
					if(hasLabel.length > 0){
						labelHtml = '<label>' + textLabel + '</label>';
					}
                    headerConsumed += '<div class="ui-state-default slick-headerrow-column wd-row-custom ' + hasLabel + ' ' + exClass + statusColor +' ' + left + ' ' + right + '" id="' + idOfHeader + '">'+ labelHtml +'<p>' + valOfHeader + '</p></div>';
                }
            });
            headerConsumed += '</div>';
            if (viewGantt) {
                var headerConsumedRight = '<div id="wd-header-custom-right" class="slick-headerrow-columns"></div>';
                $('.slick-header-columns-left').after(headerConsumed);
                // $('.slick-header-columns-right').after(headerConsumedRight); // remove by huynh
                /*
                 layerWidth = 100/12;
                 for(var i = 0; i < 12; i++){
                 offsetLeft = i * layerWidth;
                 headerConsumed += '<div class="gantt-month" style="width: ' + layerWidth + '%; left: ' + offsetLeft + '%;"></div>';
                 }
                 */
                $('.slick-pane.slick-pane-top.slick-pane-right .slick-headerrow-column:not(:last)').each(function (j) {
                    var me = $(this);
                    var year = $('.slick-pane-right .slick-header-column').eq(j).text();
                    total = totalDate(year + '-01-01', year + '-12-31') + 1;
                    for (var i = 0; i < 12; i++) {
                        m = i + 1;
                        m = m < 10 ? '0' + m : m;
                        days = daysInMonth(m, year);
                        layerWidth = days / total * 100;
                        offsetLeft = totalDate(year + '-01-01', year + '-' + m + '-01') / total * 100;
                        me.append('<div class="gantt-month" style="width: ' + layerWidth + '%; left: ' + offsetLeft + '%;"><span>' + mx[i] + '</span></div>');
                    }
                });
            } else {
                $('.slick-header-columns').after(headerConsumed);
            }
        }
        /* table .end */
        var createDialog = function () {
            $("#ProjectProjectAmrProgramId_port").multiSelect({
                noneSelected: '<?php __("--"); ?>',
                url: '<?php echo $html->url('/projects/get_sub_program/') ?>',
                update: "#ProjectProjectAmrSubProgramId_port",
                loadingClass: 'wd-disable',
                loadingText: 'Loading...',
                oneOrMoreSelected: '*',
                selectAll: false
            });

            $("#ProjectProjectAmrProgramId").multiSelect({
                noneSelected: '<?php __("--"); ?>',
                url: '<?php echo $html->url('/projects/get_sub_program/') ?>',
                update: "#ProjectProjectAmrSubProgramId",
                loadingClass: 'wd-disable',
                loadingText: 'Loading...',
                oneOrMoreSelected: '*',
                selectAll: false
            });

            $("#ProjectProjectAmrProgramId_sum").multiSelect({
                noneSelected: '<?php __("--"); ?>',
                url: '<?php echo $html->url('/projects/get_sub_program/') ?>',
                update: "#ProjectProjectAmrSubProgramId_sum",
                loadingClass: 'wd-disable',
                loadingText: 'Loading...',
                oneOrMoreSelected: '*',
                selectAll: false
            });

            $('#dialog_vision_portfolio, #dialog_vision_staffing_news').dialog({
                position: 'center',
                autoOpen: false,
                autoHeight: true,
                modal: true,
                width: 500,
                open: function (e) {
                    var $dialog = $(e.target);
                    $dialog.find('select').not('#ProjectProjectAmrProgramId,#ProjectProjectAmrProgramId_port,#ProjectProjectAmrProgramId_sum').multiSelect({
                        noneSelected: '<?php __("--"); ?>',
                        oneOrMoreSelected: '*', selectAll: false});
                    $dialog.dialog({open: $.noop});
                    //HistoryFilter.parse();
                }
            });

            $('#dialog_vision_task').dialog({
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

            $('#dialog_vision_expectation').dialog({
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
        }
        $("#add_vision_staffing_news").live('click', function () {
            createDialog();
            $("#dialog_vision_staffing_news").dialog('option', {title: 'Vision Staffing+ Filter'}).dialog('open');
        });
        $("#add_vision_portfolio").live('click', function () {
            createDialog();
            $("#dialog_vision_portfolio").dialog('option', {title: 'Vision Portfolio Filter'}).dialog('open');
        });
        $(".cancel").live('click', function () {
            $("#dialog_vision_portfolio, #dialog_vision_staffing_news").dialog('close');
        });
        $("#vision_task").live('click', function () {
            createDialog();
            $("#dialog_vision_task").dialog('option', {title: ''}).dialog('open');
        });
        $(".cancel").live('click', function () {
            $("#dialog_vision_task").dialog('close');
        });
        $("#expectation_screen").live('click', function () {
            createDialog();
            $("#dialog_vision_expectation").dialog('option', {title: ''}).dialog('open');
        });
        $(".cancel").live('click', function () {
            $("#dialog_vision_expectation").dialog('close');
        });
        $("#ok").click(function () {
            $("#form_vision_staffing").submit();
        });
        $("#ok_sum").click(function () {
            $("#form_vision_staffing_news").submit();
        });
        $("#ok_port").click(function () {
            $("#form_vision_portfolio").submit();
        });
        $('#ProjectProjectGanttId0,#ProjectProjectGanttId1').click(function () {
            if ($('#ProjectProjectGanttId1').prop('checked')) {
                $('#display-real-time').show().find('input').prop('disabled', false);
            } else {
                $('#display-real-time').hide().find('input').prop('disabled', true);
            }
        }).filter(':checked').click();
        $('#ProjectProjectGanttIdNews0, #ProjectProjectGanttIdNews1').click(function () {
            if ($('#ProjectProjectGanttIdNews1').prop('checked')) {
                $('#display-real-time-news').show().find('input').prop('disabled', false);
            } else {
                $('#display-real-time-news').hide().find('input').prop('disabled', true);
            }
        }).filter(':checked').click();
        $('#CategoryCategory').change(function () {
            var cate_id= $(this).val();
			var view_id = $('#CategoryStatus').val();
            actionSelectView(view_id, cate_id);
        });
        $('#CategoryStatus').change(function () {
			var view_id = $(this).val();
			var cate_id = $('#CategoryCategory').val();
            actionSelectView(view_id, cate_id);
        });
		function actionSelectView(view_id, cate_id){
			if (view_id != 0) {
				window.location = ('/projects/index/' + view_id + '?cate=' + cate_id);
			}
		}
        $('#ProjectProjectFile0').click(function () {
            $('#ok_sum').show();
            $('#ok_export_file').hide();
        });
        $('#ProjectProjectFile1').click(function () {
            $('#ok_sum').hide();
            $('#ok_export_file').show();
        });
        $('#ok_export_file').click(function () {
            $("#dialog_vision_staffing_news").dialog('close');
        });
        $('#ok_export_file').click(function () {
            var showGantt = $('#show_gantt').find('input:checked').val();
            var realTime = $('#display-real-time-news').find('input:checked').val();
            var showBy = $('#show_by').find('input:checked').val();
            var showSum = $('#show_summary').find('input:checked').val();
            var program = [];
            $('#filter_program div label').each(function () {
                if ($(this).find('input').is(':checked')) {
                    var _val = $(this).find('input:checked').val();
                    program.push(_val);
                }
            });
            var subProgram = [];
            $('#filter_sub_program div label').each(function () {
                if ($(this).find('input').is(':checked')) {
                    var _val = $(this).find('input:checked').val();
                    subProgram.push(_val);
                }
            });
            var manager = [];
            $('#filter_manager div label').each(function () {
                if ($(this).find('input').is(':checked')) {
                    var _val = $(this).find('input:checked').val();
                    manager.push(_val);
                }
            });
            var status = [];
            $('#filter_status div label').each(function () {
                if ($(this).find('input').is(':checked')) {
                    var _val = $(this).find('input:checked').val();
                    status.push(_val);
                }
            });
            var profitCenter = [];
            $('#filter_profitCenter div label').each(function () {
                if ($(this).find('input').is(':checked')) {
                    var _val = $(this).find('input:checked').val();
                    profitCenter.push(_val);
                }
            });
            var func = [];
            $('#filter_function div label').each(function () {
                if ($(this).find('input').is(':checked')) {
                    var _val = $(this).find('input:checked').val();
                    func.push(_val);
                }
            });
            $('#ExportVisionShowGantt').val(showGantt);
            $('#ExportVisionShowType').val(showBy);
            $('#ExportVisionSummary').val(showSum);
            $('#ExportVisionProgram').val(program);
            $('#ExportVisionSubProgram').val(subProgram);
            $('#ExportVisionManager').val(manager);
            $('#ExportVisionStatus').val(status);
            $('#ExportVisionProfitCenter').val(profitCenter);
            $('#ExportVisionFunction').val(func);
            $('#ExportVisionIndexForm').submit();
        });
        if ($display_all_name_of_milestones == 1) {
            setTimeout(function () {
                $('#MilestonesCheck').trigger('click');
                $('#MilestonesCheck').attr('checked', 'checked');
            }, 2000);
        }

        $('#export-submit').click(function () {
            var length = ControlGrid.getDataLength();
            var list = [];
            for (var i = 0; i < length; i++) {
                list.push(ControlGrid.getData().getItem(i).id);
            }
            $('#export-item-list').val(list.join(',')).closest('form').submit();
        });
        $('#add_portfolio').click(function () {
            var length = ControlGrid.getDataLength();
            var list = [];
            for (var i = 0; i < length; i++) {
                list.push(ControlGrid.getData().getItem(i).id);
            }
            var urlPortfolio = '<?php echo $this->Html->url(array('controller' => 'projects', 'action' => 'projects_vision', $appstatus)); ?>';
            urlPortfolio = urlPortfolio + '/' + list.join('-');
            window.location.href = urlPortfolio;
        });
        HistoryFilter.setVal = function (name, value) {
            //setupScroll();
            $('.row-parent').parent().addClass('row-parent-custom');
            $('.row-disabled').parent().addClass('row-disabled-custom');
            $('.row-number').parent().addClass('row-number-custom');
            var $data = $("[name='" + name + "']").each(function () {
                var $element = $(this);
                if ($element.is(':checkbox') || $element.is(':radio')) {
                    if (!$.isArray(value)) {
                        value = [value];
                    }
                    $element.prop('checked', $.inArray($element.val(), value) != -1);
                } else {
                    $element.val(value);
                    $element.keypress();
                }
                $element.data('__auto_trigger', true);
                $element.change();
            });
            return $data.length > 0;
        }
        $('.row-parent').parent().addClass('row-parent-custom');
        $('.row-disabled').parent().addClass('row-disabled-custom');
        $('.row-number').parent().addClass('row-number-custom');
        //scroll to curent year
        var container = $('.slick-viewport.slick-viewport-top.slick-viewport-right'),
                item = $('.gantt-current-month:eq(0)');
        if (container.length && item.length) {
            container.scrollTo(item, true, false);
        }
        history_reset = function () {
            var check = false;
            $('.multiselect-filter').each(function (val, ind) {
                var text = '';
                if ($(ind).find('input').length != 0) {
                    text = $(ind).find('input').val();
                } else {
                    text = $(ind).find('span').html();
                    if (text == "<?php __('-- Any --');?>" || text == '-- Any --') {
                        text = '';

                    }
                }
                if (text != '') {
                    check = true;
                }
            });
            if (!check) {
                $('#reset-filter').addClass('hidden');
            } else {
                $('#reset-filter').removeClass('hidden');
            }
        }
        resetFilter = function () {
            $('.input-filter').val('').trigger('change');
            $('.multiSelectOptions input[type="checkbox"]').prop('checked', false).trigger('change');
            ControlGrid.setSortColumn();
            $('input[name="project_container.SortOrder"]').val('').trigger('change');
            $('input[name="project_container.SortColumn"]').val('').trigger('change');
            $('#filter_alert_input').val('no').trigger('change');
            $('#filter_favorite_input').val('no').trigger('change');

        }
		
    })(jQuery);
	<?php if( !empty($ajax_get_progress_line)){?>
		// setTimeout( function(){
			// $.ajax({
				// type: 'POST',
				// url: '/projects/get_dash_widget_progress_line',
				// data: { data:{
					// projectIds: projectIds,
				// }},
				// dataType: "json",
				// success: function (datas) {
					// var Slick_data = ControlGrid.getData().getItems();
					// var refresh = 0;
					// $.each(Slick_data, function(i,v){
						// var p_id = v.id;
						// if( p_id in datas.dataset_internals){
							// v.DataSet.internal_progress = datas.dataset_internals[p_id];
							// refresh = 1;
						// }
						// if( p_id in datas.dataset_externals){
							// v.DataSet.external_progress = datas.dataset_externals[p_id];
							// refresh = 1;
						// }
						// if( p_id in datas.syns_progress_key){
							// v.DataSet.syns_progress_key = datas.syns_progress_key[p_id];
							// refresh = 1;
						// }
					// });
					// if( Dashboard.display && refresh){
						// Dashboard.refresh_widget_progress_line();
					// }
				// }
			// });
		// }, 1500);
	<?php } ?>
    function number_format(number, decimals, dec_point, thousands_sep) {
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function (n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + Math.round(n * k) / k;
                };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }

    function dialog_confirm(pjId, confirmTitle) {
        // event.preventDefault();
       $('#modal_dialog_confirm').find('.title').html(confirmTitle);
        var dialog = $('#modal_dialog_confirm').dialog({
			width: 350,
			height: auto,
		});
        $('#btnYes').click(function () {
            dialog.dialog('close');
            // event.stopPropagation();
            delete_project(pjId);
        });
        $('#btnNo').click(function () {
            dialog.dialog('close');
        });
    }
    function delete_project(pjId) {
        $.ajax({
            type: 'POST',
            url: _linkDelete + '/' + pjId,
            data: {
                ajax: true
            },
            dataType: "json",
            success: function (datas) {
                location.reload();
            },
            error: function () {
                location.reload();
            }
        });
    }
    function showPhaseDetail(e, project) {
        var e = window.event || e;
        if (e.stopPropagation) {
            e.stopPropagation();
        } else {
            e.returnValue = false;
        }
        var type = 'year';
        var data = 'type=' + type + '&ajax=1&call=1';
        $.ajax({
            url: '/project_phase_plans/phase_vision/' + project + '?' + data,
            data: data,
            async: false,
            type: 'POST',
            success: function (datas) {
                jQuery('.dragger_container').css({'z-index': 10});
                var wh = jQuery(window).height();
                var ww = jQuery(window).width();
                jQuery('#dialogDetailValue').css({'top': "20%", 'left': '5%'});
                jQuery('#dialogDetailValue').css({'padding-top': 20, 'padding-bottom': 5, 'width': 'auto', 'max-width': '90%'});
                jQuery('#contentDialog').html(datas);
                jQuery('#AjaxGanttChartDIV .gantt-child').show();
                setTimeout(function () {
                    showMe();
                    jQuery('.gantt-line .gantt-d30').show();
					$('.gantt-line-s').siblings('.gantt-line-desc.gantt-line-n').show();
					$('.gantt-line-n').siblings('.gantt-line-desc.gantt-line-s').hide();
                    var wGantt = jQuery('#AjaxGanttChartDIV table.gantt').width();
                    jQuery('#AjaxGanttChartDIV .customScrollBox').width(wGantt);
                    jQuery('#AjaxGanttChartDIV .container').width(wGantt);
                    jQuery('#AjaxGanttChartDIV .content').width(wGantt);
                    jQuery('#ajaxScroll').width(wGantt);
                    switch (type) {
                        case 'year':
                        case 'month':
                            var $tar = '#month_' + (today.getMonth() + 1) + '_' + today.getFullYear();
                            break;
                        case 'week':
                            var $tar = '#week_<?php echo date('W') ?>_' + (today.getMonth() + 1) + '_' + today.getFullYear();
                            break;
                        default:
                            var $tar = '#date_' + today.getDate() + '_' + (today.getMonth() + 1) + '_' + today.getFullYear();
                            break;
                    }
                    var target = jQuery('#dialogDetailValue').find($tar);
                    if (target.length) {
                        jQuery('#dialogDetailValue').scrollTo(target, true, null);
                    }
                    var gantt_ms = $('.gantt-line .gantt-msi');
                    var _conf = 0;
                    max_height = 0;
                    gantt_ms.each(function () {
                        var _this = $(this).find('span');
                        _this.css('margin-left', -(_this.width() / 2 - 13));
                        
                        max_height = Math.max(max_height, _this.parent().height());


                    });
                    $('.gantt-ms .gantt-line').height(max_height + 10);
					$('.gantt-chart .gantt-chart-wrapper').css('max-height', wh - 140);
					var gant_line = $('#mcs1_container').find('.gantt-line');
					$.each(gant_line, function(i, e){
						var line_s = $(e).find('.gantt-line-s').length;
						var line_n = $(e).find('.gantt-line-n').length;
						if((line_n == 0 && line_s > 0) || (line_n > 0 && line_s == 0) ){
							$(e).addClass('gantt-one-line');
						}else if(line_n == 0 && line_s == 0){
							$(e).addClass('gantt-no-line');
						}
					});
					$('#dialogDetailValue').css({"left": "40px", "top": "40px"});
                }, 100);
            }
        });
    }
    function updatePriority(id, cell) {
        var t = $(this).prop('disabled', true).css('background-color', '#eee');
        var v = $(this).val();
        //ajax
        $.ajax({
            url: '<?php echo $this->Html->url('/projects/updatePriority') ?>',
            type: 'POST',
            data: {
                data: {
                    id: id,
                    project_priority_id: v
                }
            },
            complete: function () {

                /* Update to grid */
                var dataView = ControlGrid.getData();
                var actCell = (ControlGrid.getActiveCell()) ? ControlGrid.getActiveCell().cell : 0;
                dataView.beginUpdate();
                var _new_data = dataView.getItems();
                $.each(_new_data, function (ind, item) {
                    if (item.id == id) {
                        item.DataSet['Project.project_priority_id'] = v;
                        item['Project.project_priority_id'] = listPriorities[v] ? listPriorities[v] : '';
                        _new_data[ind] = item;
                    }
                });
                dataView.setItems(_new_data);
                dataView.endUpdate();
                ControlGrid.invalidate();
                ControlGrid.render();
                var actRow = ControlGrid.getData().getRowById(id);
                ControlGrid.gotoCell(actRow, actCell, false);
                /* End Update to grid */

                t.prop('disabled', false).css('background-color', '#fff');
                var $input = $('.slick-headerrow-column.l' + cell + ' .multiSelect');
                var o = $input.data('config');
                $('#Project.project_name').trigger('change');
            }
        });
    }
    // update updateTypeAndProgram
    function updateTypeAndProgram(id, cell) {
        var t = $(this).prop('disabled', true).css('background-color', '#eee');
        var v = $(this).val();
        var keys = $(this).data("key");
        //ajax
        $.ajax({
            url: '<?php echo $this->Html->url('/projects/updateTypeAndProgram') ?>',
            type: 'POST',
            data: {
                data: {
                    project_id: id,
                    data_id: v,
                    keys: keys
                }
            },
            complete: function () {
                //update grid
                if (keys == 'Project.project_type_id') {
                    type = listProjectType;
                } else if (keys == 'Project.project_sub_type_id') {
                    type = listProjectSubType;
                } else if (keys == 'Project.project_amr_program_id') {
                    type = projectProgram;
                } else if (keys == 'Project.project_amr_sub_program_id') {
                    type = projectSubProgram;
                }
                /* Update to grid */
                var dataView = ControlGrid.getData();
                var actCell = (ControlGrid.getActiveCell()) ? ControlGrid.getActiveCell().cell : 0;
                dataView.beginUpdate();
                var _new_data = dataView.getItems();
                $.each(_new_data, function (ind, item) {
                    if (item.id == id) {
                        item.DataSet[keys] = v;
                        item[keys] = type[v] ? type[v] : '';
                        _new_data[ind] = item;
                    }
                });
                dataView.setItems(_new_data);
                dataView.endUpdate();
                ControlGrid.invalidate();
                ControlGrid.render();
                var actRow = ControlGrid.getData().getRowById(id);
                ControlGrid.gotoCell(actRow, actCell, false);
                /* End Update to grid */

                t.prop('disabled', false).css('background-color', '#fff');
                var $input = $('.slick-headerrow-column.l' + cell + ' .multiSelect');
                var o = $input.data('config');
                $('#Project.project_name').trigger('change');
            }
        });
    }
    // hover open popup text
    var _timeout;
    function closePopupText() {
        clearTimeout(_timeout);
        var elm = this;
        _timeout = setTimeout(function () {
            $(elm).closest('.slick-cell').removeClass('active more-style');
            $(elm).closest('.slick-cell').find('.hover-popup').removeClass('open');
        }, 300);

    }
    /* Edit by huynh 11/07/2018
     -*/
    function updateWeathers() {
        var t = $(this),
			checked = false,
			image = t.data('image'),
			key = t.data('key'),
			id = t.data('id'),
			isNew = t.data('new'),
			_html = '',
			_ex_class = '';
        if (isNew) {
            _ex_class = 'fit-content';
        }
        $(this).closest('.slick-cell').addClass('active  more-style');
        _html += '<div class="wd-popup-weather ' + _ex_class + '"><div class="wd-input wd-weather-list-dd"><ul>';
        if (key == 'ProjectAmr.rank') {
            _html += '<li id="weather-up" data-value="up" class="weather weather-up' + (image == 'up' ? ' selected' : '') + '"> <img style="float: none" title="'+ i18n['up'] +'" src="<?php echo $this->Html->url('/') ?>img/new-icon/project_rank/up.png" /></li>';
            _html += '<li id="weather-mid" data-value="mid" class="weather weather-mid' + (image== 'mid' ? ' selected' : '') + '"> <img style="float: none" title="'+ i18n['mid'] +'" src="<?php echo $this->Html->url('/') ?>img/new-icon/project_rank/mid.png" /></li>';
            _html += '<li id="weather-down" data-value="down" class="weather weather-down' + (image== 'down' ? ' selected' : '') + '"> <img style="float: none" title="'+ i18n['down'] +'" src="<?php echo $this->Html->url('/') ?>img/new-icon/project_rank/down.png" /></li>';
        } else {
            _html += '<li id="weather-sun" data-value="sun" class="weather weather-sun' + (image== 'sun' ? ' selected' : '') + '">'+ icons_title["sun"]+'</li>';
            if (isNew) {
                _html += '<li id="weather-fair" data-value="fair" class="weather weather-fair' + (image== 'fair' ? ' selected' : '') + '"> <img style="float: none" title="'+ i18n['fair'] +'" src="<?php echo $this->Html->url('/') ?>img/new-icon/fair.png" /></li>';
            }
            _html += '<li id="weather-cloud" data-value="cloud" class="weather weather-cloud' + (image== 'cloud' ? ' selected' : '') + '">'+ icons_title["cloud"]+'</li>';
            if (isNew) {
                _html += '<li id="weather-furry" data-value="furry" class="weather weather-furry' + (image== 'furry' ? ' selected' : '') + '"> <img style="float: none" title="'+ i18n['furry'] +'" src="<?php echo $this->Html->url('/') ?>img/new-icon/furry.png" /></li>';
            }
            _html += '<li id="weather-rain" data-value="rain" class="weather weather-rain' + (image== 'rain' ? ' selected' : '') + '">'+ icons_title["rain"]+'</li>';
        }
        _html += '</ul></div></div>';
        if (!$(this).find('.wd-popup-weather').length) {
            var _this = $(this);
            _this.append(_html);
            var _table_top = _this.closest('.slick-viewport').offset().top;
            var _this_top = _this.find('.wd-popup-weather ul').offset().top;
            if (_this_top < _table_top)
                _this.find('.wd-popup-weather ul').css('top', '38px');
			if( _this.closest('.slick-pane').length){
				var _view_right = _this.closest('.slick-pane').offset().left + _this.closest('.slick-pane').width();
				var _view_left = _this.closest('.slick-pane').offset().left;
			}else{
				var _view_right = _this.closest('.slick-viewport').offset().left + _this.closest('.slick-viewport').width();
				var _view_left = _this.closest('.slick-viewport').offset().left;
			}
            var _popup_right = _this.find('.wd-popup-weather ul').offset().left + _this.find('.wd-popup-weather ul').width();
            var _popup_left = _this.find('.wd-popup-weather ul').offset().left;
            if (_popup_left < _view_left) {
                _this.find('.wd-popup-weather ul').css({
                    'left': '0',
                    'transform': 'none'
                });
            } else if (_popup_right > _view_right) {
                _this.find('.wd-popup-weather ul').css({
                    'left': 'inherit',
                    'transform': 'none',
                    'right': '0'

                });
            } else {
                _this.find('.wd-popup-weather ul').css({
                    'left': '',
                    'right': '0',
                    'transform': ''
                });
            }
        }
        // $(this).find('.weather').unbind('click').on('click', function () {
        $(this).find('.weather').on('click', function () {
            if (!$(this).hasClass('selected')) {
                var val = $(this).data('value');
                t.addClass('wt-loading');
                // update weather
                if (val != image) {
                    $.ajax({
                        url: '/projects/updateWeather/',
                        type: 'POST',
                        data: {
                            data: {
                                project_id: id,
                                key: key,
                                val: val
                            }
                        },
                        success: function () {
							var args = {};
							args = SlickGridCustom.getInstance().getData().getItemById(id);
							args['DataSet'][key] = val;
							args[key] = val;
							SlickGridCustom.update_after_edit(id,args);
                           
                        },
						complete: function(){
							t.removeClass('wt-loading');
						}
                    });
                }
            }
        });
        // $(this).mouseleave(function () {
            // _image = t.data('image');
            // $(this).closest('.slick-cell').removeClass('active');
            // if (!checked) {
                // if (_image === 'undefined') {
                    // $(this).html('<span>&nbsp</span>');
                // } else {
                    // $(this).html('<center><img src="<?php echo $this->Html->url('/') ?>img/new-icon/' + _image + '.png"></center>');

                // }
            // }
        // });
    }
    //qtip

    $('#project_container').on('mouseenter', '.hover-tooltip-cus', function (ev) {
        var selector = $(this);
        var data = {
            title: selector.find('.hover-data-name').text(),
            start: selector.find('.hover-data-start').text(),
            end: selector.find('.hover-data-end').text(),
            progress: selector.find('.hover-data-comp').text(),
            resource: selector.find('.hover-data-assign').text()
        }
        selector.qtip({
            overwrite: false,
            show: {
                solo: true,
                event: ev.type, // Use the same event type as above
                ready: true // Show immediately - important!
            },
            hide: 'click mouseleave',
            content: {
                text: function (e, api) {
                    var content = $('<dl class="hover-content"></dl>');
                    content.append('<dt>' + i18n.startday + '</dt>');
                    content.append('<dd>' + data.start + '</dd>');
                    content.append('<dt>' + i18n.enddate + '</dt>');
                    content.append('<dd>' + data.end + '</dd>');
                    content.append('<dt>' + i18n.progress + '</dt>');
                    content.append('<dd>' + data.progress + '</dd>');
                    content.append('<dt>' + i18n.resource + '</dt>');
                    content.append('<dd>' + data.resource + '</dd>');
                    return content;
                },
                title: data.title
            },
            position: {
                my: 'bottom center',
                at: 'top center',
                target: 'mouse',
                adjust: {
                    mouse: false
                }
            },
            style: {
                classes: 'qtip-shadow qtip-xlight'
            }
        });
    });
    $('#project_container').on('mouseenter click', '.gantt-msi', function (ev) {
        var selector = $(this);
        var data = {
            title: selector.find('.hover-stone-name').text(),
            date: selector.find('.hover-stone-date').text()
        }
        selector.qtip({
            overwrite: false,
            show: {
                solo: true,
                event: ev.type, // Use the same event type as above
                ready: true // Show immediately - important!
            },
            hide: 'mouseleave',
            content: {
                text: function (e, api) {
                    var content = $('<dl class="hover-content"></dl>');
                    content.append('<dt>' + i18n.date + '</dt>');
                    content.append('<dd>' + data.date + '</dd>');
                    return content;
                },
                title: data.title
            },
            position: {
                my: 'bottom center',
                at: 'top center',
                target: 'mouse',
                adjust: {
                    mouse: false
                }
            },
            style: {
                classes: 'qtip-shadow qtip-xlight'
            }
        });
    });
	function init_draggable_progress(){}
	if( companyConfigs['project_progress_method'] && companyConfigs['project_progress_method']=='manual' && canModified){
		$('.project-item').on('click', '.wd-progress-number .text', function(){
			var _this = $(this);
			_this.hide();
			_this.siblings('.input-progress').removeClass('wd-hide').focus();
			_this.parent().addClass('focus');
		});
		function saveManualProgress(elm){
			var _this = $(elm);
			if(!_this.hasClass('input-progress')) {
				_this = _this.find('.input-progress');
			}
			var val = _this.val();
			var project_item = _this.closest('.project-item');
			var project_id = project_item.data('project_id');
			project_item.find('.loading-mark').addClass('loading');
			$.ajax({
				url: '/projects_preview/saveFieldYourForm/' + project_id,
				type: 'post',
				dataType: 'json',
				data: {
					field: 'manual_progress',
					value: val
				},
				beforeSend: function(){
					project_item.find('.loading-mark').addClass('loading');
				},
				success: function(res){
					if( res.result == 'success'){
						displayProgrressVal(_this.closest('.wd-progress-slider'), parseFloat( res.data.Project.manual_progress ), true);
						_this.blur(); 
						if( _this.closest('.project-item').hasClass('grid-comment-dialog')){
							var project_item = $('#project-item-' + project_id);
							displayProgrressVal(project_item.find('.wd-progress-slider:first'), parseFloat( res.data.Project.manual_progress ), true);
						}
					}else{;
						location.reload();
					}
				},
				error: function(){
					location.reload();
				},
				complete: function(){
					project_item.find('.loading-mark').removeClass('loading');
				}
			});
		}
		function progressHideInput(elm){
			var _this = $(elm).parent();
			_this.find('.text').show();
			_this.find('.input-progress').addClass('wd-hide');
			_this.removeClass('focus');
		}
		function init_draggable_progress(){
			$('body').find('.project-item.editable').find('.wd-progress-number').draggable({
				axis: 'x',
				opacity: 0.6,
				cursor: 'pointer',
				drag: function( event, ui ) {
					var container = ui.helper.closest('.wd-progress-slider');
					var _w = ui.helper.width() + parseInt(ui.helper.css('border-left-width')) + parseInt(ui.helper.css('border-right-width'));
					var max_w = container.width() - _w;
					ui.position.left = Math.max( 0, Math.min( container.width() - _w, ui.position.left ));
					var val = parseInt(ui.position.left / max_w * 100);
					displayProgrressVal(container, val);
				},
				stop: function( event, ui ) {
					saveManualProgress(ui.helper[0]);
				}
			});
		}
		init_draggable_progress();
		function displayProgrressVal(container, val, is_animation){
			var v = parseInt( Math.max(0, Math.min(val, 100)));
			var css_class= '';
			if( val > 100) css_class='red-line';
			if( is_animation){
				container.find('.wd-progress-value-line, .wd-progress-number').addClass('ease');
			}
			container.removeClass('red-line').addClass(css_class);
			container.data('value', val);
			container.find('.wd-progress-value-line').width(v + '%');
			container.find('.wd-progress-number').css('left', v + '%');
			container.find('.wd-progress-number .text').text(v + '%');
			container.find('.input-progress').val(val);
			setTimeout( function(){
				container.find('.wd-progress-value-line, .wd-progress-number').removeClass('ease');
			}, 400);
		}
	}
    function updateText() {
        id = $(this).data("id");
        model = $(this).data("model");
        field = $(this).data("field");
        var _html = '';
        var latest_update = '';

        var popup = $('#template_logs');
        $.ajax({
            url: '/projects/getComment',
            type: 'POST',
            data: {
                id: id,
                model: model,
            },
            dataType: 'json',
            success: function (data) {
                _html = '<p class="project-name">' + data['project_name'] + '</p>';
                html = '<div id="content-comment-id">';

                if (data) {
                    latest_update = '';
                    _html += '<div class="comment"><textarea data-id = ' + id + ' data-field = ' + field + ' data-model = ' + model + ' cols="30" rows="6" id="update-comment"></textarea></div>';
                    _html += '<div class="content-logs">';
                    if (data[0]) {
                        latest_update = new Date(data[0]['updated'] * 1e3).toISOString().slice(0, 10);
						latest_update = latest_update.split('-');
                        latest_update = text_modified + ' ' + latest_update[2] + '/' + latest_update[1] + '/' + latest_update[0] + ' ' + text_by + ' ' + data[0]['update_by_employee'];
                        var i = 0;

                        $.each(data, function (ind, _data) {

                            if (_data && ('id' in Object(_data))) {
                                name = ava_src = '';
                                comment = _data['description'] ? _data['description'].replace(/\n/g, "<br>") : '';
                                date = _data['updated'];
                                date = new Date(_data['updated'] * 1e3).toISOString().slice(0, 10);
								date = date.split('-');
								
                                ava_src += '<img width = 35 height = 35 src="' + js_avatar(_data['employee_id']) + '" title = "' + _data['name'] + '" />';
								
                                _html += '<div class="content content-' + i++ + '"><div class="avatar">' + ava_src + '</div><div class="item-content"><p>' + date[2] + '/' + date[1] + '/' + date[0] + '</p><div class="comment">' + comment + '</div></div></div>';
                            }
                            i++;
                        });
                    }
                    _html += '</div>';
                }

                // var class_pro = '';
                // if (data['initDate'] > 50)
                    // class_pro = 'late-progress';
                // _html_progress = "<div class='log-progress'><div class='project-progress " + class_pro + "'><p class='progress-full'>" + draw_line_progress(data['initDate']) + "</p><p>" + data['initDate'] + "%</p></div>";
                // _html += _html_progress;
                // _html += '<div class="logs-info"><p class="update-by-employee">' + latest_update + '</p></div></div>';
                // $('#content_comment').html(_html);
				
				_html += '</div>';
                _html_progress = "<div class='log-progress'>";
				if( showProgress) _html_progress += draw_progress_line(data['initDate']);
				_html_progress += "</div>";
                _html += _html_progress;
                _html += '<div class="logs-info"><p class="update-by-employee">' + latest_update + '</p></div>';
                $('#content_comment').html(_html);
                popup.dialog('option', {title: data['project_name']});
				init_draggable_progress();

                var createDialog2 = function () {
                    $('#template_logs').dialog({
                        position: 'center',
                        autoOpen: false,
                        height: 420,
                        modal: true,
                        width: (isTablet || isMobile) ? 320 : 520,
                        minHeight: 50,
                        open: function (e) {
                            var $dialog = $(e.target);
                            $dialog.dialog({open: $.noop});
                        }
                    });
                    createDialog2 = $.noop;
                }
                createDialog2();
                $("#template_logs").dialog('option', {title: ''}).dialog('open');

            }
        });

    }
	
	var upload_document_canedit = 0;
	$('#wd-template-upload').find('.upload-document').hide();
	$('#wd-template-upload').find('.document_delete').hide();
	
	$('#wd-template-upload').on('click', '.document_delete', function(e){
		var _this = $(this);
		var _url = _this.attr('href');
		if( _url){
			e.preventDefault();
			$.ajax({
				url: _url,
				cache: false,
				dataType: 'json',
				type: 'get',
				beforeSend: function () {
					_this.closest('.loading-mark').addClass('loading');
				},
				complete: function () {
					_this.closest('.loading-mark').removeClass('loading');
				},
				success: function (data) {
					if(data.result == "success"){
						var  _html = '';
						$.each(data.data.ProjectFile, function(ind, file){
							_html += draw_file_list(file);
						});
						// _html += '</ul>';
						var key = _this.data('key');
						var _p_id = data.data.Project.id;
						if( key){
							key = 'Project.' + key;
							var args = {};
							args[key] = data.data.ProjectFile;
							SlickGridCustom.update_after_edit(_p_id,args);
						}
						_this.closest('.loading-mark').removeClass('loading');
						_this.closest('.list-document ul').html(_html);
						
					}else{
						
					}
					
				}
			});
		}
		
	});
    function draw_file_list(file){
		var canedit = upload_document_canedit; 
		file = file.ProjectFile;
		var _link = '/projects/attachment/'+ file.key + '/' + file.project_id + '/' + file.id + '/';
		var _fancy = '';
		if((/\.(gif|jpg|jpeg|tiff|png)$/i).test(file.file_attachment)) _fancy = ' fancy image';
		var _li = '<li>';
		_li += '<i class="icon-paper-clip"></i><a href = "'+ _link + 'download" class="file-name' + _fancy + '" alt="attachment ' + file.id + '">'+ file.file_attachment  +'</a>';
		if( canedit){
			_li += '<a href="' + _link + 'delete" data-key = "'+ file.key  +'" class="document_delete"><img src="/img/new-icon/delete-attachment.png" alt="'+ file.id +'"></a>';
		}
		_li += '</li>';
		return _li;
	}
	if(  has_upload){
		function open_multi_upload(){
			var _this = $(this);
			var _key = _this.data('key');
			var _p_id = _this.data('id');
			var _html = '';
			var popup_tag = '#wd-template-upload';
			var popup = $(popup_tag);
			show_full_popup(popup_tag, {width: 580});
			$('#document-project-id').val(_p_id);
			$('#document-key').val(_key);
			$.ajax({
				url: '<?php echo $html->url('/projects/get_upload_document/') ?>'  + _p_id,
				cache: false,
				dataType: 'json',
				type: 'post',
				data: {
					data: {
						key: _key
					}
				},
				beforeSend: function () {
					popup.find('.loading-mark').addClass('loading');
				},
				complete: function () {
					popup.find('.loading-mark').removeClass('loading');
				},
				success: function (data) {
					if(data.result == "success"){
						popup.find( '.heading >h4').html(data.data.Project['project_name']);
						if( data.data.canEdit == true ){
							upload_document_canedit = 1;
							popup.find('.upload-document').show();
							popup.find('.document_delete').show();
						}else{
							upload_document_canedit = 0;
							popup.find('.upload-document').hide();
							popup.find('.document_delete').hide();
						}
						var  _html = '';
						$.each(data.data.ProjectFile, function(ind, file){
							_html += draw_file_list(file);
						});
						popup.find('.list-document ul').html(_html);
					}else{
						popup.find('.upload-document').hide();
					}
				}
			});
		}
		Dropzone.autoDiscover = false;
		$(window).ready(function(){
			_tag = '#upload_documents'; 
			var popup_tag = '#wd-template-upload';
			var popup = $(popup_tag);
			$(_tag).addClass('dropzone'); 
			$(_tag).wdDropzone({
				dz_success: function(file, _Dropzone){
					_Dropzone.removeFile(file);
					var _xhr = '';
					var respon = '';
					if ('xhr' in file)
						_xhr = file.xhr;
					if ('responseText' in _xhr)
						respon = _xhr.responseText;
					data = JSON.parse(respon);
					var canedit = upload_document_canedit;
					_doc = data.ProjectFile;
					var _link = '/projects/attachment/'+ _doc.key + '/' + _doc.project_id + '/' + _doc.id + '/';
					var _fancy = '';
					if((/\.(gif|jpg|jpeg|tiff|png)$/i).test(_doc.file_attachment)) _fancy = ' fancy image';
					var _li = '<li>';
					_li += '<i class="icon-paper-clip"></i><a href = "'+ _link + 'download" class="file-name' + _fancy + '" alt="attachment ' + _doc.id + '">'+ _doc.file_attachment  +'</a>';
					if( canedit){
						_li += '<a href="' + _link + 'delete" data-key = "'+ _doc.key  +'" class="document_delete"><img src="/img/new-icon/delete-attachment.png" alt="'+ _doc.id +'"></a>';
					}
					_li += '</li>';
					popup.find('.list-document ul').append(_li);
					
					// Update to grid
					var p_id = $('#document-project-id').val();
					var key = $('#document-key').val();
					if( key){
						key = 'Project.' + key;
						var grid_item = SlickGridCustom.getInstance().getData().getItemById(p_id);
						var args = grid_item[key];
						args.push(data);
						// args[key] = data.data.ProjectFile;
						SlickGridCustom.update_after_edit(p_id,args);
					}
				},
				dz_sending: function(file, xhr, formData){
					popup.find('.loading-mark').addClass('loading');
				},
				dz_queuecomplete: function(_Dropzone){
					popup.find('.loading-mark').removeClass('loading');
				},
			});
		});
	}

    // add dialog vision task
    $(function () {
        $("#startDateVision, #endDateVision").datepicker({
            showOn: 'button',
            buttonImage: '<?php echo $html->url("/img/front/calendar.gif") ?>',
            buttonImageOnly: true,
            dateFormat: 'dd-mm-yy'
        });
		
    });
    var multiStatusProject = $('#category-id').multipleSelect({
        minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>'
    });
    var multiStatusProject = $('#statusProject').multipleSelect({
        minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>'
    });
    var multiProgramProject = $('#programProject').multipleSelect({
        minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>'
    });
    var multilSubProgramProject = $('#subProgramProject').multipleSelect({
        minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>'
    });
    var multilStatusTask = $('#statusTask').multipleSelect({
        minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>'
    });
    var multilProrityTask = $('#prorityTask').multipleSelect({
        minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>'
    });
    $('#export-assign-employee').multipleSelect({
        // minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>',
        oneOrMoreSelected: '*',
        selectAll: false
    });
    var mutilCodeProject = $('#codeProject').multipleSelect({
        minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>'
    });
    var mutilCodeProject1 = $('#codeProject1').multipleSelect({
        minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>'
    });
    var milestone = $('#milestone').multipleSelect({
        minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>'
    });
    var timeout3;
    var multiAssign = $('#export-assign-team').multipleSelect({
        minimumCountSelected: 0,
        position: 'top',
        placeholder: '<?php __("-- Any --") ?>',
        onClick: function (view) {
            clearTimeout(timeout3);
            timeout3 = setTimeout(function () {
                updatePcAndResource(multiAssign, $('#export-assign-employee'), view.complete);
            }, 1000);
        }
    });
    var totalPC = <?php echo isset($listPc) ? json_encode(count($listPc)) : 0 ?>;
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
    var vv;
    $('body').on("focusout", "#update-comment", function () {
        var _this = $(this);
        var text = $(this).val(),
                field = $(this).data("field"),
                model = $(this).data("model"),
                id = $(this).data("id");
        if (text != '') {
            var _html = '';
            $('#update-comment').closest('#content_comment').addClass('loading');

            $.ajax({
                url: '/projects/update_text_comment',
                type: 'POST',
                data: {
                    data: {
                        id: id,
                        text: text,
                        field: field,
                        model: model,
                    }
                },
                dataType: 'json',
                success: function (data) {
                    _html = _log_progress = '';
                    var latest_update;
                    if (data) {
                        latest_update = new Date(data[0]['updated'] * 1e3).toISOString().slice(0, 10);
                        latest_update = text_modified + ' ' + latest_update + ' ' + text_by + ' ' + data[0]['update_by_employee'];
                        i = 1;
                        $.each(data, function (ind, _data) {
                            if (_data && ('id' in Object(_data))) {
                                name = ava_src = '';
                                comment = _data['description'] ? _data['description'].replace(/\n/g, "<br>") : '';
                                date = new Date(_data['updated'] * 1e3).toISOString().slice(0, 10);
                                
                                    ava_src += '<img width = 35 height = 35 src="' + js_avatar(_data['employee_id']) + '" title = "' + _data['name'] + '" />';
                                
                                _html += '<div class="content content-' + i++ + '"><div class="avatar">' + ava_src + '</div><div class="item-content"><p>' + date + '</p><div class="comment">' + comment + '</div></div></div>';
                            }
                        });
                    } else {
                        _html += '';
                    }
                    var class_pro = '';
                    if (latest_update)
                        _log_progress += '<p class="update-by-employee">' + latest_update + '</p>';
                    $('#template_logs .content-logs').empty().append(_html);
                    $('#update-comment').val('');
                    $('#template_logs .log-progress .logs-info').empty().append(_log_progress);
                    $('#update-comment').closest('#content_comment').removeClass('loading');

                    /* Update to grid */
                    if (data) {
                        var dataView = ControlGrid.getData();
                        var actCell = (ControlGrid.getActiveCell()) ? ControlGrid.getActiveCell().cell : 0;
                        dataView.beginUpdate();
                        var _new_data = dataView.getItems();
                        $.each(_new_data, function (ind, item) {
                            if (item.id == id) {
                                item_name = 'ProjectAmr.' + field;
                                item[item_name] = data[0];
                                item[item_name]['current'] = data['current'];
                                _new_data[ind] = item;
                            }
                        });
                        dataView.setItems(_new_data);
                        dataView.endUpdate();
                        ControlGrid.invalidate();
                        ControlGrid.render();
                        var actRow = ControlGrid.getData().getRowById(id);
                        // ControlGrid.gotoCell(0, 0, false);
                        ControlGrid.gotoCell(actRow, actCell, false);
                    }
                    /* End Update to grid */
                }

            });
        }
    });
    $("#reset_sum_team").click(function () {
        $('#category-id').multipleSelect('setSelects', []);
        $('#statusProject').multipleSelect('setSelects', []);
        $('#programProject').multipleSelect('setSelects', []);
        $('#subProgramProject').multipleSelect('setSelects', []);
        $('#statusTask').multipleSelect('setSelects', []);
        $('#prorityTask').multipleSelect('setSelects', []);
        $('#taskProject').val('');
        $('#export-assign-team').multipleSelect('setSelects', []);
        $('#export-assign-employee').multipleSelect('setSelects', []);
        $('#codeProject').multipleSelect('setSelects', []);
        $('#codeProject1').multipleSelect('setSelects', []);
        $('#milestone').multipleSelect('setSelects', []);
        $('#startDateVision').val('');
        $('#endDateVision').val('');
        vsFilter.set('nameCategory', []);
        vsFilter.set('nameStatusProject', []);
        vsFilter.set('nameProgramProject', []);
        vsFilter.set('nameSubProgramProject', []);
        vsFilter.set('nameStatusTask', []);
        vsFilter.set('nameProrityTask', []);
        vsFilter.set('assignedTeam', []);
        vsFilter.set('assignedResources', []);
        vsFilter.set('nameCodeProject', []);
        vsFilter.set('milestone', []);
        vsFilter.set('nameTaskProject', '');
        vsFilter.set('nameStartDateVision', '');
        vsFilter.set('nameEndDateVision', '');
        return false;
    });
    // submit vision task.
    var vsFilter = {};
	$(window).ready(function(){
		$.z0.History.load('vision_task', function (data) {
			vsFilter = data;
			var nameCategory = data.get('nameCategory', []);
			$('#category-id').multipleSelect('setSelects', nameCategory);
			var nameStatusProject = data.get('nameStatusProject', []);
			$('#statusProject').multipleSelect('setSelects', nameStatusProject);
			var nameProgramProject = data.get('nameProgramProject', []);
			$('#programProject').multipleSelect('setSelects', nameProgramProject);
			var nameSubProgramProject = data.get('nameSubProgramProject', []);
			$('#subProgramProject').multipleSelect('setSelects', nameSubProgramProject);
			var nameStatusTask = data.get('nameStatusTask', []);
			$('#statusTask').multipleSelect('setSelects', nameStatusTask);
			var nameProrityTask = data.get('nameProrityTask', []);
			$('#prorityTask').multipleSelect('setSelects', nameProrityTask);
			var nameTaskProject = data.get('nameTaskProject');
			$('#taskProject').val(nameTaskProject);
			var assignedTeam = data.get('assignedTeam', []);
			$('#export-assign-team').multipleSelect('setSelects', assignedTeam);
			var assignedResources = data.get('assignedResources', []);
			$('#export-assign-employee').multipleSelect('setSelects', assignedResources);
			var nameCodeProject = data.get('nameCodeProject', []);
			$('#codeProject').multipleSelect('setSelects', nameCodeProject);
			var nameCodeProject1 = data.get('nameCodeProject1', []);
			$('#codeProject1').multipleSelect('setSelects', nameCodeProject1);
			var nameMilestone = data.get('nameMilestone', []);
			$('#milestone').multipleSelect('setSelects', nameMilestone);
			var nameStartDateVision = data.get('nameStartDateVision');
			$('#startDateVision').val(nameStartDateVision);
			var nameEndDateVision = data.get('nameEndDateVision');
			$('#endDateVision').val(nameEndDateVision);
		});
	});
    var urlExport = <?php echo json_encode($this->Html->url(array('controller' => 'projects', 'action' => 'export_vision_task')))?>;
    var urlTaskVision = <?php echo json_encode($this->Html->url(array('controller' => 'projects', 'action' => 'tasks_vision')))?>;
    $("#ok_sum_vision").click(function () {
        vsFilter.set('nameCategory', $('#category-id').multipleSelect('getSelects'));
        vsFilter.set('nameStatusProject', $('#statusProject').multipleSelect('getSelects'));
        vsFilter.set('nameProgramProject', $('#programProject').multipleSelect('getSelects'));
        vsFilter.set('nameSubProgramProject', $('#subProgramProject').multipleSelect('getSelects'));
        vsFilter.set('nameStatusTask', $('#statusTask').multipleSelect('getSelects'));
        vsFilter.set('nameProrityTask', $('#prorityTask').multipleSelect('getSelects'));
        vsFilter.set('nameTaskProject', $('#taskProject').val());
        vsFilter.set('assignedTeam', $('#export-assign-team').multipleSelect('getSelects'));
        vsFilter.set('assignedResources', $('#export-assign-employee').multipleSelect('getSelects'));
        vsFilter.set('nameStartDateVision', $('#startDateVision').val());
        vsFilter.set('nameEndDateVision', $('#endDateVision').val());
        vsFilter.set('nameCodeProject', $('#codeProject').multipleSelect('getSelects'));
        vsFilter.set('nameCodeProject1', $('#codeProject1').multipleSelect('getSelects'));
        vsFilter.set('nameMilestone', $('#milestone').multipleSelect('getSelects'));
        //save filter
        $.z0.History.save('vision_task', vsFilter);
        //submit
        $('#form_vision_task').attr('action', urlExport);
        setTimeout(function () {
            $("#form_vision_task").submit();
        }, 750);
    });
    var task_not_found = <?php echo json_encode(__('Task not found', true)); ?>,
            task_found = <?php echo json_encode(__(' task found', true)); ?>,
            tasks_found = <?php echo json_encode(__(' tasks found', true)); ?>;
    $('#startDateVision , #endDateVision').change(function () {
        var _taskName = $('#taskProject').val(),
                _start = $('#startDateVision').val(),
                _end = $('#endDateVision').val();
        checkTaskForTaskVision(_taskName, _start, _end);
    });
    $('#taskProject').focusout(function () {
        var _taskName = $('#taskProject').val(),
                _start = $('#startDateVision').val(),
                _end = $('#endDateVision').val();
        checkTaskForTaskVision(_taskName, _start, _end);
    });
    function checkTaskForTaskVision(_taskName, _start, _end) {
        $.ajax({
            url: '<?php echo $html->url('/projects/check_task_for_task_vision/') ?>',
            data: {
                task: _taskName,
                start: _start,
                end: _end
            },
            type: 'POST',
            dataType: 'json',
            success: function (result) {
                if (result > 0) {
                    var t = (result > 1) ? tasks_found : task_found;
                    $('#show_count_task').text(result + t).css('color', 'blue');
                } else {
                    $('#show_count_task').text(task_not_found).css('color', 'red');
                }
            }
        });
    }
    function openSumrow() {
        $(this).toggleClass('active');
        var parent = $(this).closest('.wd-tab');
        parent.find('.slick-header #wd-header-custom').toggle();
		resizeHandler();
    }
    $('#full_screen_vision').click(function () {
        vsFilter.set('nameCategory', $('#category-id').multipleSelect('getSelects'));
        vsFilter.set('nameStatusProject', $('#statusProject').multipleSelect('getSelects'));
        vsFilter.set('nameProgramProject', $('#programProject').multipleSelect('getSelects'));
        vsFilter.set('nameSubProgramProject', $('#subProgramProject').multipleSelect('getSelects'));
        vsFilter.set('nameStatusTask', $('#statusTask').multipleSelect('getSelects'));
        vsFilter.set('nameProrityTask', $('#prorityTask').multipleSelect('getSelects'));
        vsFilter.set('nameTaskProject', $('#taskProject').val());
        vsFilter.set('assignedTeam', $('#export-assign-team').multipleSelect('getSelects'));
        vsFilter.set('assignedResources', $('#export-assign-employee').multipleSelect('getSelects'));
        vsFilter.set('nameStartDateVision', $('#startDateVision').val());
        vsFilter.set('nameEndDateVision', $('#endDateVision').val());
        vsFilter.set('nameCodeProject', $('#codeProject').multipleSelect('getSelects'));
        vsFilter.set('nameCodeProject1', $('#codeProject1').multipleSelect('getSelects'));
        vsFilter.set('nameMilestone', $('#milestone').multipleSelect('getSelects'));
        //save filter
        $.z0.History.save('vision_task', vsFilter);
        $('#form_vision_task').attr('action', urlTaskVision);
        setTimeout(function () {
            $("#form_vision_task").submit();
        }, 750);
    });
    // dialog expectation.
    $(function () {
        $("#startDateExpec, #endDateExpec").datepicker({
            showOn: 'button',
            buttonImage: '<?php echo $html->url("/img/front/calendar.gif") ?>',
            buttonImageOnly: true,
            dateFormat: 'dd-mm-yy'
        });
        
    });
    var multiStatusProject = $('#CatePExpec').multipleSelect({
        minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>'
    });
    var multiStatusProject = $('#StatusPExpec').multipleSelect({
        minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>'
    });
    var multiProgramProject = $('#ProgramPExpec').multipleSelect({
        minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>'
    });
    var multilSubProgramProject = $('#SubProgramPExpec').multipleSelect({
        minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>'
    });
    $('#AssignRessourceExpec').multipleSelect({
        // minimumCountSelected: 0,
        placeholder: '<?php __("-- Any --") ?>',
        oneOrMoreSelected: '*',
        selectAll: false
    });
    var timeout3;
    var multiAssign = $('#AssignTeamExpec').multipleSelect({
        minimumCountSelected: 0,
        position: 'top',
        placeholder: '<?php __("-- Any --") ?>',
        onClick: function (view) {
            clearTimeout(timeout3);
            timeout3 = setTimeout(function () {
                updatePcAndResource(multiAssign, $('#AssignRessourceExpec'), view.complete);
            }, 1000);
        }
    });
    var totalPC = <?php echo isset($listPc) ? json_encode(count($listPc)) : 0 ?>;
    $("#reset_vision_expec").click(function () {
        $('#CatePExpec').multipleSelect('setSelects', []);
        $('#StatusPExpec').multipleSelect('setSelects', []);
        $('#ProgramPExpec').multipleSelect('setSelects', []);
        $('#SubProgramPExpec').multipleSelect('setSelects', []);
        $('#nameExpec').val('');
        $('#AssignTeamExpec').multipleSelect('setSelects', []);
        $('#AssignRessourceExpec').multipleSelect('setSelects', []);
        $('#ScreenExpec').val('');
        $('#startDateExpec').val('');
        $('#endDateExpec').val('');
        vsFilter.set('cateExpec', []);
        vsFilter.set('StatusPExpec', []);
        vsFilter.set('ProgramPExpec', []);
        vsFilter.set('SubProgramPExpec', []);
        vsFilter.set('AssignTeamExpec', []);
        vsFilter.set('AssignRessourceExpec', []);
        vsFilter.set('screen', '');
        vsFilter.set('startDateExpec', '');
        vsFilter.set('endDateExpec', '');
        return false;
    });
    // submit vision task.
    var vsFilter = {};
	$(window).ready(function(){
		$.z0.History.load('vision_expectaion', function (data) {
			vsFilter = data;
			var nameCategory = data.get('cateExpec', []);
			$('#CatePExpec').multipleSelect('setSelects', nameCategory);
			var nameStatusProject = data.get('StatusPExpec', []);
			$('#StatusPExpec').multipleSelect('setSelects', nameStatusProject);
			var nameProgramProject = data.get('ProgramPExpec', []);
			$('#ProgramPExpec').multipleSelect('setSelects', nameProgramProject);
			var nameSubProgramProject = data.get('SubProgramPExpec', []);
			$('#SubProgramPExpec').multipleSelect('setSelects', nameSubProgramProject);
			var nameTaskProject = data.get('nameExpec');
			$('#nameExpec').val(nameTaskProject);
			var assignedTeam = data.get('AssignTeamExpec', []);
			$('#AssignTeamExpec').multipleSelect('setSelects', assignedTeam);
			var assignedResources = data.get('AssignRessourceExpec', []);
			$('#AssignRessourceExpec').multipleSelect('setSelects', assignedResources);
			var scr = data.get('screen', []);
			$('#ScreenExpec').val(scr);
			var nameStartDateVision = data.get('startDateExpec');
			$('#startDateExpec').val(nameStartDateVision);
			var nameEndDateVision = data.get('endDateExpec');
			$('#endDateExpec').val(nameEndDateVision);
		});
	});
    var urlExpectation = <?php echo json_encode($this->Html->url(array('controller' => 'project_expectations', 'action' => 'vision')))?>;
    $("#full_screen_expectation").click(function () {
        vsFilter.set('cateExpec', $('#CatePExpec').multipleSelect('getSelects'));
        vsFilter.set('StatusPExpec', $('#StatusPExpec').multipleSelect('getSelects'));
        vsFilter.set('ProgramPExpec', $('#ProgramPExpec').multipleSelect('getSelects'));
        vsFilter.set('SubProgramPExpec', $('#SubProgramPExpec').multipleSelect('getSelects'));
        vsFilter.set('nameExpec', $('#nameExpec').val());
        vsFilter.set('AssignTeamExpec', $('#AssignTeamExpec').multipleSelect('getSelects'));
        vsFilter.set('AssignRessourceExpec', $('#AssignRessourceExpec').multipleSelect('getSelects'));
        vsFilter.set('startDateExpec', $('#startDateExpec').val());
        vsFilter.set('endDateExpec', $('#endDateExpec').val());
        vsFilter.set('screen', $('#ScreenExpec').val());
        //save filter
        $.z0.History.save('vision_expectaion', vsFilter);
        //submit
        $('#form_vision_expectation').attr('action', urlExpectation);
        setTimeout(function () {
            $("#form_vision_expectation").submit();
        }, 750);
    });
    //scroll to curent year
    var container = $('.slick-viewport.slick-viewport-top.slick-viewport-right'),
            item = $('.gantt-current-month:eq(0)');

    if (container.length && item.length) {
        container.scrollTo(item, true, false);
    }
    var $companyConfigs = <?php echo json_encode($companyConfigs) ?>;
    if ($companyConfigs['display_muti_sort'] == 0) {
        $('#btnCont').hide();
    }
    function setupScroll() {
        // if ($('.slick-header-right').length != 0) {
			// console.log( 1);
			// console.log( $('.slick-header-right'));
            // var right = $('.slick-header-right').width();
			// var ct_w = $('.grid-canvas-right').width();
			// console.log( right, ct_w);
			// if( ct_w > right){
				// $("#scrollTopAbsenceContent").width(ct_w);
				// $("#scrollTopAbsence").show().width(right);
				// $("#scrollTopAbsence").css('left', $('.grid-canvas-right').offset().left - 40);
			// }else{
				// $("#scrollTopAbsence").hide();
			// }
        // } else {
            var ct_w = 0;
            $(".grid-canvas").each(function (val, ind) {
                ct_w += $(ind).width();
            });
			var w = $("#project_container").width();
			if( ct_w > w){
				$("#scrollTopAbsenceContent").width(ct_w);
				$("#scrollTopAbsence").show().width($("#project_container").width());
				$("#scrollTopAbsence").css('left', 0);
			}else{
				$("#scrollTopAbsence").hide();
			}
        // }
    }
    $("#scrollTopAbsence").scroll(function () {
        // if ($('.slick-header-right').length != 0) {
            // $('.slick-viewport-right').scrollLeft($("#scrollTopAbsence").scrollLeft());
        // } else {
            $(".slick-viewport").scrollLeft($("#scrollTopAbsence").scrollLeft());
        // }
    });
    $(".slick-viewport").scroll(function () {
        // if ($('.slick-header-right').length != 0) {
            // $("#scrollTopAbsence").scrollLeft($(".slick-viewport-right").scrollLeft());
        // } else {
            $("#scrollTopAbsence").scrollLeft($(".slick-viewport").scrollLeft());
        // }
    });
    // setupScroll();
    $('.multiselect-filter').find('a').each(function (val, index) {
        $(index).attr('href', '');
    });
    $("#dialogDetailValue").draggable({
        cancel: "#wd-fragment-2, #wd-tab-content, .dialog_no_drag",
        stop: function (event, ui) {
            var position = $("#dialogDetailValue").position();
            $.ajax({
                url: "/projects/savePopupPositions",
                type: "POST",
                data: {
                    top: position.top,
                    left: position.left
                }
            });
            savePosition = position;
        }
    });
    $('.show_message_profile').click(function () {
        alert('<?php echo __("With your profile, you cannot see project screen", true); ?>');
    });
    function openFilter() {
        var project_filter = $('.open-filter-form').closest('.wd-project-filter');
        project_filter.find('.search-filter').toggleClass('active');
        if ($(window).width() > 992) {
            $('.search-filter').css('left', 0 - ($('.wd-project-filter').position().left) - parseInt($('.wd-project-filter').css('padding-left')));
        } else {
            $('.search-filter').css('left', '');
        }
        $('body').find('.wd-project-admin').toggleClass('active');
    }
    $('.close-filter').click(function () {
        $(this).closest('.search-filter').toggleClass('active');
        $('body').find('.wd-project-admin').toggleClass('active');
    });
    update_table_height();
	
    function slickGridFilterCallBack() {
        var dataView = ControlGrid.getDataView();
        var count = dataView.getLength();
        $('#CategoryCategory option[selected="selected"]').text(list_cate[appstatus] + ' (' + count + '/' + _count_project + ')');

        var cat = $('#CategoryCategory').val();
        var length = ControlGrid.getDataLength();
        var list = [];
        for (var i = 0; i < length; i++) {
            list.push(ControlGrid.getData().getItem(i).id);
        }
        var grid_href = map_href = '';
        grid_href = '<?php echo $this->Html->url('/projects/index_plus/') ?>' + cat + '/' + list.join('-');
        map_href = '<?php echo $this->Html->url('/projects_preview/map/') ?>' + cat + '/' + list.join('-');
        ;
        if (grid_href)
            $('#grid-icon').prop('href', grid_href);
        if (map_href)
            $('#map-icon').prop('href', map_href);
		if( !$.isEmptyObject(Dashboard)){
			if( Dashboard.display ){
				Dashboard.refresh();
			}
		}
    }
	function jsUcfirst(string){
		return string.charAt(0).toUpperCase() + string.slice(1);
	}
	$(window).ready(function(){
		setTimeout(function(){
			if( !$('#addProjectTemplate').hasClass('loaded') && !$('#addProjectTemplate').hasClass('loading')){
				$('#addProjectTemplate').addClass('loading');
				$.ajax({
					url : "/projects/add_popup/",
					type: "GET",
					cache: false,
					success: function (html) {
						$('#addProjectTemplate').empty().html(html);
						$('#add-form').trigger('reset');
						$(window).trigger('resize');
						$('#addProjectTemplate').addClass('loaded');
						$('#addProjectTemplate').removeClass('loading');
						$('input[data-return="form-return"]').val('<?php echo $this->here;?>');
						if( $('#addProjectTemplate').hasClass('open') ){
							show_full_popup('#template_add_task_prj');
						}
					}
				});
			}
		}, 4000);
		
	});
	$('#add_new_popup').on('click', function(e){
		$('#addProjectTemplate').addClass('open');
		// $('.add-project a').toggleClass('active');
		$(window).trigger('resize');
		var popup_width = 580;
		if( $('#addProjectTemplate').hasClass('open') && !$('#addProjectTemplate').hasClass('loaded') && !$('#addProjectTemplate').hasClass('loading')){
			$('#addProjectTemplate').addClass('loading');
			$.ajax({
				url : "/projects/add_popup/",
				type: "GET",
				cache: false,
				success: function (html) {
					$('#addProjectTemplate').empty().html(html);
					$(window).trigger('resize');
					$('input[data-return="form-return"]').val('<?php echo $this->here;?>');
					$('#addProjectTemplate').addClass('loaded');
					$('#addProjectTemplate').removeClass('loading');
					if( $('#NewTask').hasClass('active') ) popup_width = show_workload ? 1080 : 580;
					show_full_popup( '#template_add_task_prj', {width: popup_width});
				}
			});
		}
		if( $('#addProjectTemplate').hasClass('loaded') ){
			
			if( $('#NewTask').hasClass('active') ) popup_width = show_workload ? 1080 : 580;
			show_full_popup( '#template_add_task_prj', {width: popup_width});
		}
	});
	$(window).resize(function () {
		update_table_height();
    });
	function update_table_height(){
		var ControlGrid = SlickGridCustom.getInstance();
		var wdTable = $('.wd-layout').find('.wd-table');
		var mql = window.matchMedia('printer');
		var z_printing = window.isZPrinting||false;
		if( mql.matches || z_printing){
			layout_for_print = true;
			if( ControlGrid) { ControlGrid.set_full_height();}
		}else{
			layout_for_print = false;
			var heightTable = $(window).height() - wdTable.offset().top - 40;
			if( $(window).height() < 800){
				var dashboard = $('#switch-dashboard');
				wd_display_dashboard = dashboard.length ?  dashboard.is(':checked') : false;
				if(wd_display_dashboard) heightTable = $(window).height() *  2 / 3;
			}
			wdTable.css({
				height: heightTable,
			});
			if( ControlGrid) ControlGrid.resizeCanvas(); 
			setupScroll();
		}
	}
	update_table_height();
	// function add_print_button_for_firefox(){
		// if(navigator.userAgent.indexOf("Firefox") != -1){
			// btn = '<a href="javascript:void(0);" class="btn btn-print hide-on-mobile" id="window-print" onclick="firefox_active_print(this)" title="<?php echo __('Print layout');?> ">' + icons_title['printer'] + '</a>';
			// $('.wd-title').first().append( $(btn));
			// $(window).unbind('afterprint');
		// }
	// }
	// function firefox_active_print(elm){
		// var _this= $(elm);
		// var _grid = SlickGridCustom.getInstance();
		// if( !_grid) return false;
		// if( _this.hasClass('active')){
			// window.isZPrinting= false;
			// _grid.set_max_height();
		// }else{
			// window.isZPrinting= true;
			// _grid.set_full_height();
			// window.print();
		// }
		// _this.toggleClass('active');
		
	// }
	// Vi history filter khong luu cac gia tri bang false nen thay doi dung yes/no
	$('#filter_alert').on('click', function(){
		var _input = $('#filter_alert_input');
		if( !_input.hasClass('disabled')){
			var v = ( _input.val() == 'yes');
			_input.val( v ? 'no' : 'yes' ).trigger('change');
		}
	});
	$('#filter_alert_input').on('change', function(){
		var _this = $(this);
		var val = _this.val();
		if( val == 'yes') {
			$('#filter_alert').addClass('active');
		}else{
			$('#filter_alert').removeClass('active');
		}
		var dataView = ControlGrid.getDataView();
		dataView.refresh();	
		reCaculateHeaderSummary();
		slickGridFilterCallBack.call();
	});
	
	$('#filter_project_favorite').on('click', function(){
		var _input = $('#filter_favorite_input');
		if( !_input.hasClass('disabled')){
			var v = ( _input.val() == 'yes');
			_input.val( v ? 'no' : 'yes' ).trigger('change');
		}
	});
	$('#filter_favorite_input').on('change', function(){
		var _this = $(this);
		var val = _this.val();
		if( val == 'yes') {
			$('#filter_project_favorite').addClass('active');
		}else{
			$('#filter_project_favorite').removeClass('active');
		}
		var dataView = ControlGrid.getDataView();
		dataView.refresh();		
		reCaculateHeaderSummary();
		slickGridFilterCallBack.call();
	});
	function filter_by_favorite(item){
		return ( favorites[item['id']] == 1);
	}
	if(isFilterFavorite){
		$('#filter_project_favorite').trigger('click');
	}
	if(isFilterAlert){
		$('#filter_alert').trigger('click');
	}
	function filter_by_alert(item){
		var grid = SlickGridCustom.getInstance();
		var _columns = grid.getColumns();
		var res = false;
		$.each( _columns, function( k, c){
			var _field = c.field;
			switch(_field){
				case "ProjectWidget.Phase_plan":
					if( parseInt( item['DataSet']['Phase_plan']['diff']) > 0) res = true;
					break;
				case "ProjectWidget.Milestone_late":
					if( item['DataSet']['Milestone_late']['date'] != '')  res = true;
					break;
				case "ProjectWidget.Project_progress":
					if( parseInt( item['ProjectWidget.Project_progress']) > 100) res = true;
					break;
				case "ProjectWidget.Phase_progress":
					if( parseInt( item['ProjectWidget.Phase_progress']) > 100) res = true;
					break;
				case "ProjectWidget.FinancePlus_inv_budget":
					if( parseFloat( item['ProjectWidget.FinancePlus_inv_percent']) > 100)  res = true;
					break;
				case "ProjectWidget.FinancePlus_fon_budget":
					if( parseFloat( item['ProjectWidget.FinancePlus_fon_percent']) > 100)  res = true;
					break;
				case "ProjectWidget.FinancePlus_finaninv_budget":
					if( parseFloat( item['ProjectWidget.FinancePlus_finaninv_percent']) > 100)  res = true;
					break;
				case "ProjectWidget.FinancePlus_finanfon_budget":
					if( parseFloat( item['ProjectWidget.FinancePlus_finanfon_percent']) > 100)  res = true;
					break;
				case "ProjectWidget.Synthesis_budget":
					if( parseFloat( item['ProjectWidget.Synthesis_percent']) > 100)  res = true;
					break;
				case "ProjectWidget.Internal_budget_md":
					if( parseFloat( item['ProjectWidget.Internal_percent_forecast_md']) > 100)  res = true;
					if( parseFloat( item['ProjectWidget.Internal_percent_consumed_md']) > 100)  res = true;
				case "ProjectWidget.Internal_budget_euro":
					if( parseFloat( item['ProjectWidget.Internal_percent_forecast_euro']) > 100)  res = true;
					if( parseFloat( item['ProjectWidget.Internal_percent_consumed_euro']) > 100)  res = true;
					break;
				case "ProjectWidget.External_budget_erro":
					if( parseFloat( item['ProjectWidget.External_percent_forecast_erro']) > 100)  res = true;
					if( parseFloat( item['ProjectWidget.External_percent_ordered_erro']) > 100)  res = true;
					break;
			}
		});
		return res;		
	}
	function custom_screen_filter(item){
		if( $('#filter_alert_input').val() == 'yes'){
			if( !filter_by_alert(item)){
				return false;
			}
		}
		if( $('#filter_favorite_input').val() == 'yes'){
			if( !filter_by_favorite(item)){
				return false;
			}
		}
		return true;
	}
	function reCaculateHeaderSummary(){
		var dataView = ControlGrid.getDataView();
		var columns = ControlGrid.getColumns();
		var length = dataView.getLength();
		var listSumTop = new Array();
		for(var i = 0; i < length ; i++){
			$.each(dataView.getItem(i), function(key, val){
				if($.inArray(key, $this.columnNotCalculationConsumed) != -1){
					// do nothing
				} else {
					if(!listSumTop[key]){
						listSumTop[key] = 0;
					}
					val = val ? $this.number_format(val, 2, '.', '') : 0;
					listSumTop[key] += parseFloat(val);
				}
			});
		}
		
		var colorClass = [];
		for (var i = 0; i < columns.length; i++){
			var column = columns[i];
			var idOfHeader = column.id;
			var valOfHeader = (listSumTop[column.id] || listSumTop[column.id] == 0) ? listSumTop[column.id] : '';
			if(((column.name).trim()).length > 0){
				key = column.id;
			}
			if ($.inArray(idOfHeader, $this.widgetCaculateTotalPercent) != -1) {
				colorClass[key] = !(colorClass[key]) ? '' : colorClass[key];
				switch(idOfHeader) {
				  case 'ProjectWidget.External_percent_forecast_erro':
						valOfHeader = calculatePercent(listSumTop['ProjectWidget.External_forecast_erro'], listSumTop['ProjectWidget.External_budget_erro']);
						break;
				  case 'ProjectWidget.External_percent_ordered_erro':
						valOfHeader = calculatePercent(listSumTop['ProjectWidget.External_ordered_erro'], listSumTop['ProjectWidget.External_forecast_erro']);
						break;
				  case 'ProjectWidget.FinancePlus_finanfon_percent':
						valOfHeader = calculatePercent(listSumTop['ProjectWidget.FinancePlus_finanfon_engaged'], listSumTop['ProjectWidget.FinancePlus_finanfon_budget']);
						break;
				  case 'ProjectWidget.FinancePlus_finaninv_percent':
						valOfHeader = calculatePercent(listSumTop['ProjectWidget.FinancePlus_finaninv_engaged'], listSumTop['ProjectWidget.FinancePlus_finaninv_budget']);
						break;
				  case 'ProjectWidget.FinancePlus_fon_percent':
						valOfHeader = calculatePercent(listSumTop['ProjectWidget.FinancePlus_fon_engaged'], listSumTop['ProjectWidget.FinancePlus_fon_budget']);
						break;
				  case 'ProjectWidget.FinancePlus_inv_percent':
						valOfHeader = calculatePercent(listSumTop['ProjectWidget.FinancePlus_inv_engaged'], listSumTop['ProjectWidget.FinancePlus_inv_budget']);
						break;
				  case 'ProjectWidget.Synthesis_percent':
						valOfHeader = calculatePercent(listSumTop['ProjectWidget.Synthesis_forecast'], listSumTop['ProjectWidget.Synthesis_budget']);
						break;
				  case 'ProjectWidget.Internal_percent_consumed_euro':
					valOfHeader = calculatePercent(listSumTop['ProjectWidget.Internal_engaged_euro'], listSumTop['ProjectWidget.Internal_forecast_euro']);
						break;
				  case 'ProjectWidget.Internal_percent_consumed_md':
						valOfHeader = calculatePercent(listSumTop['ProjectWidget.Internal_consumed_md'], listSumTop['ProjectWidget.Internal_forecast_md']);
						break;
				  case 'ProjectWidget.Internal_percent_forecast_euro':
						valOfHeader = calculatePercent(listSumTop['ProjectWidget.Internal_forecast_euro'], listSumTop['ProjectWidget.Internal_budget_euro']);
						break;
				  case 'ProjectWidget.Internal_percent_forecast_md':
						valOfHeader = calculatePercent(listSumTop['ProjectWidget.Internal_forecast_md'], listSumTop['ProjectWidget.Internal_budget_md']);
						break;
				  default:
					// code block
				}
				if(valOfHeader > 100){
					colorClass[key] =  ' red-color';
				}
			}
		}
		var statusColor = '';
		for (var i = 0; i < columns.length; i++){
			var column = columns[i];
			var idOfHeader = column.id;
			var valOfHeader = (listSumTop[column.id] || listSumTop[column.id] == 0) ? listSumTop[column.id] : '';
			if ($.inArray(idOfHeader, $this.columnAlignRightAndManDay) != -1) {
				valOfHeader = number_format(valOfHeader, 2, ',', ' ') + ' ' + $this.t('M.D');
			} else if ($.inArray(idOfHeader, $this.columnAlignRightAndEuro) != -1) {
				valOfHeader = number_format(valOfHeader, 2, ',', ' ') + ' ' + $this.currency;
			} else if ($.inArray(idOfHeader, $this.widgetCaculateTotalEuro) != -1) {
				valOfHeader = number_format(valOfHeader, 2, '.', ' ') + ' ' + $this.currency;
			} else if ($.inArray(idOfHeader, $this.widgetCaculateTotalMD) != -1) {
				valOfHeader = number_format(valOfHeader, 2, '.', ' ') + ' ' + $this.t('M.D');
			} else if ($.inArray(idOfHeader, $this.widgetCaculateTotalPercent) != -1) {
				// sum_percent = valOfHeader;
				switch(idOfHeader) {
				  case 'ProjectWidget.External_percent_forecast_erro':
						valOfHeader = calculatePercent(listSumTop['ProjectWidget.External_forecast_erro'], listSumTop['ProjectWidget.External_budget_erro']);
						break;
				  case 'ProjectWidget.External_percent_ordered_erro':
						valOfHeader = calculatePercent(listSumTop['ProjectWidget.External_ordered_erro'], listSumTop['ProjectWidget.External_forecast_erro']);
						break;
				  case 'ProjectWidget.FinancePlus_finanfon_percent':
						valOfHeader = calculatePercent(listSumTop['ProjectWidget.FinancePlus_finanfon_engaged'], listSumTop['ProjectWidget.FinancePlus_finanfon_budget']);
						break;
				  case 'ProjectWidget.FinancePlus_finaninv_percent':
						valOfHeader = calculatePercent(listSumTop['ProjectWidget.FinancePlus_finaninv_engaged'], listSumTop['ProjectWidget.FinancePlus_finaninv_budget']);
						break;
				  case 'ProjectWidget.FinancePlus_fon_percent':
						valOfHeader = calculatePercent(listSumTop['ProjectWidget.FinancePlus_fon_engaged'], listSumTop['ProjectWidget.FinancePlus_fon_budget']);
						break;
				  case 'ProjectWidget.FinancePlus_inv_percent':
						valOfHeader = calculatePercent(listSumTop['ProjectWidget.FinancePlus_inv_engaged'], listSumTop['ProjectWidget.FinancePlus_inv_budget']);
						break;
				  case 'ProjectWidget.Synthesis_percent':
						valOfHeader = calculatePercent(listSumTop['ProjectWidget.Synthesis_forecast'], listSumTop['ProjectWidget.Synthesis_budget']);
						break;
				  case 'ProjectWidget.Internal_percent_consumed_euro':
					valOfHeader = calculatePercent(listSumTop['ProjectWidget.Internal_engaged_euro'], listSumTop['ProjectWidget.Internal_forecast_euro']);
						break;
				  case 'ProjectWidget.Internal_percent_consumed_md':
						valOfHeader = calculatePercent(listSumTop['ProjectWidget.Internal_consumed_md'], listSumTop['ProjectWidget.Internal_forecast_md']);
						break;
				  case 'ProjectWidget.Internal_percent_forecast_euro':
						valOfHeader = calculatePercent(listSumTop['ProjectWidget.Internal_forecast_euro'], listSumTop['ProjectWidget.Internal_budget_euro']);
						break;
				  case 'ProjectWidget.Internal_percent_forecast_md':
						valOfHeader = calculatePercent(listSumTop['ProjectWidget.Internal_forecast_md'], listSumTop['ProjectWidget.Internal_budget_md']);
						break;
				  default:
					// code block
				}
				valOfHeader = '<span class="wd-sum-percent">' + number_format(valOfHeader, 2, '.', ' ') + '%' + '</span>';
			} else {
				if (valOfHeader) {
					valOfHeader = number_format(valOfHeader, 2, ',', ' ');
				}
			}
			if(((column.name).trim()).length > 0){
				statusColor = colorClass[column.id] ? colorClass[column.id] : '';
			}
			idOfHeader = idOfHeader.replace('.', '_');
			if($this.moduleAction){
				idOfHeader = $this.moduleAction + idOfHeader;
			}
			if($('#'+idOfHeader).length){
				if(statusColor.length){
					$('#'+idOfHeader).addClass('red-color');
				}else{
					$('#'+idOfHeader).removeClass('red-color');
				}
			}
			$('#'+idOfHeader+' p').html(valOfHeader);
		}
	}
	function calculatePercent (a, b) {
		if(typeof a === 'undefined') a = 0;
		if(typeof b === 'undefined') b = 0;
		if(b == 0){
			if(a > 0) return 100;
			else return 0;
		}else{
			return 100 * a / b;
		}
		
	}
	// HistoryFilter.afterLoad = function(){
		// var _input = $('#filter_alert_input');
		// _input.removeClass('disabled');
	// }
	
	function toggleFavoriteProject(project_id){
		var _this = $('#favorite-'+project_id);
		$.ajax({
			url: '/projects/toggleFavoriteProject/' + project_id,
			type: 'get',
			dataType: 'json',
			beforeSend: function(){
				_this.addClass('loading');
			},
			success: function(res){
				if( res.result == 'success'){
					value = '';
					if( res.favorite){
						value = project_id;
						favorites[project_id] = 1;
						_this.addClass('favorite').prop('title', i18ns.remove_favorite);
					}else{
						favorites[project_id] = 0;
						_this.removeClass('favorite').prop('title', i18ns.add_favorite);
					}
					var dataView = ControlGrid.getData();
					dataView.beginUpdate();
					var _new_data = dataView.getItems();
					$.each(_new_data, function (ind, item) {
						if (item.id == project_id) {
							item.DataSet['MFavorite.modelId'] = value;
							item['MFavorite.modelId'] = value;
							_new_data[ind] = item;
						}
					});
					dataView.setItems(_new_data);
					dataView.endUpdate();
					ControlGrid.invalidate();
					ControlGrid.render();
				}else{;
					location.reload();
				}
			},
			error: function(){
				location.reload();
			},
			complete: function(){
				_this.removeClass('loading');
			}
		});
	}
   
	function toggle_popup_logo(){
		var _logo_container = $('.wd-customer-logo');
		if(_logo_container.length > 0){
			_logo_container.toggleClass('active');
		}
	}
	$('.img-customer-logo').on('click', function(e){
		toggle_popup_logo();
	});
	function cancel_popup_logo(){
		toggle_popup_logo();
	}
	function htmlEntities(str) {
		return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
	}
	function urlify(text) {
		 var urlRegex = /(https?:\/\/[^\s]+)/g;
		 return text.replace(urlRegex, function(url) {
			return '<a target="_blank" href="' + url + '">' + url + '</a>';
		 })
	}
	$('.checkbox-logo').on('change', function(e){
		var _this = $(this);
		var id_logo = null;
		var _container_checkbox = _this.closest('.wd-popup-logo-item');
		if(_this.is(':checked')){
			_this.siblings('.wd-logo-checkbox').addClass('checked');
			var _item_logo = _this.closest('.wd-popup-logo-item').siblings('.wd-popup-logo-item');
			_item_logo.find('.wd-logo-checkbox').removeClass('checked');
			_item_logo.find('.checkbox-logo').prop('checked', false);
			id_logo = _this.val();
		}else{
			_this.siblings('.wd-logo-checkbox').removeClass('checked');
		}
		
		$.ajax({
			url: '/projects/updateCustomerLogo/'+ id_logo,
			type: 'get',
			dataType: 'json',
			beforeSend: function(){
				_container_checkbox.addClass('loading');
			},
			success: function(res){
				if( res == 1){
					var _src_logo = _this.siblings('img').attr('src');
					$('.img-customer-logo').find('img').attr("src", _src_logo);
				}
			},
			error: function(){
				location.reload();
			},
			complete: function(){
				_container_checkbox.removeClass('loading');
			}
		});
		
	});
</script>
<?php
    echo $this->Form->create('ExportVision', array('url' => array('controller' => 'project_staffings', 'action' => 'export_system'), 'type' => 'file'));
    echo $this->Form->hidden('showGantt');
    echo $this->Form->hidden('showType');
    echo $this->Form->hidden('summary');
    echo $this->Form->hidden('program');
    echo $this->Form->hidden('sub_program');
    echo $this->Form->hidden('manager');
    echo $this->Form->hidden('status');
    echo $this->Form->hidden('profit_center');
    echo $this->Form->hidden('function');
    echo $this->Form->end();
?>