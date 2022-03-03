<?php
echo $html->script('jshashtable-2.1');
echo $html->script('jquery.numberformatter-1.2.3');
echo $html->script('jquery.formatCurrency-1.4.0');
echo $html->script('jquery.validation.min');
echo $html->script('validateDate');
echo $html->script(array('jquery.fancybox.pack')); 
echo $html->css(array('jquery.fancybox')); 
echo $html->css('dd');
echo $html->css('add_popup'); 
echo $html->script('jquery.dd');
echo $html->script('jquery-ui.multidatespicker');
echo $this->Html->css(array(
    'gridster/jquery.gridster.min',
));

echo $this->Html->css(array(
    'jquery.multiSelect',
    'slick_grid/slick.grid_v2',
    'slick_grid/slick.pager',
    'slick_grid/slick.common_v2',
    'slick_grid/slick.edit',
    '/js/qtip/jquery.qtip',
    'slick_grid/slick.grid',
	'preview/component',
	'dropzone.min',
	// 'slick',
	'preview/slickgrid',
	'preview/layout',
	'slick-theme',
	'layout_2019',
	'preview/datepicker-new',
	'jqwidgets/jqx.base',
	'preview/kanban-vision',
	'preview/kaban-task'
));
echo $this->Html->script(array(
    'history_filter',
    'slick_grid/lib/jquery-ui-1.8.16.custom.min',
    'slick_grid/slick.core',
    'slick_grid/slick.dataview',
    'slick_grid/controls/slick.pager',
    'slick_grid/slick.formatters',
    'slick_grid/plugins/slick.cellrangedecorator',
    'slick_grid/plugins/slick.cellrangeselector',
    'slick_grid/plugins/slick.cellselectionmodel',
    'slick_grid/plugins/slick.dataexporter',
    'slick_grid/slick.editors',
    'slick_grid_custom',
    'qtip/jquery.qtip',
    'slick_grid/lib/jquery.event.drag-2.0.min',
    'slick_grid/slick.grid',
	'dropzone.min',
	'slick.min',
    'slick_grid/slick.grid.activity',
	'preview/define_limit_date',
	'jqwidgets/jqxcore',
	'jqwidgets/jqxkanban',

)); ?>
<?php echo $html->css('jqwidgets/jqx.base'); ?>
<?php echo $html->script('jqwidgets/jqxcore'); ?>
<?php echo $html->script('jqwidgets/jqxsortable'); ?>
<?php echo $html->script('jqwidgets/jqxkanban'); ?>
<?php echo $html->script('jqwidgets/jqxdata'); ?>
<?php echo $html->script('jqwidgets/demos');

$define_task_colors = array(
	'blue' => '#217FC2', // Blue
	// 'blue' => '#00B241', // Blue
	'red' => '#E94754', // Red
	'green' => '#00B241', // Green
);

echo $this->element('dialog_projects');
$employee_info = $this->Session->read("Auth.employee_info");
App::import("vendor", "str_utility");
$str_utility = new str_utility();
$check_consumed = (!empty($adminTaskSetting) && (($adminTaskSetting['Consumed'] == 1)||($adminTaskSetting['Manual Consumed'] == 1))) ? 1 : 0;
$show_workload = !(empty($adminTaskSetting['Workload'])) ? $adminTaskSetting['Workload'] : 0;
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
?>
<style>
	.slick-cell .circle-name.comment-by{
		top: 4px;
		position: relative;
	}
	.task-status-select .status_dots.loading,
	.task-status-select .status_texts.loading{
		padding: 0;
		background: transparent;
	}
	
    .slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
    .wd-tab .wd-aside-left{width: 300px !important;}
    .slick-cell-move-handler {
        cursor: move;
    }
    .slick-cell-move-handler:empty {
        cursor: default;
    }
	#project_container{
		max-width: 100%;
	}
    p {
        margin-bottom: 10px;
    }
    .task_blue{
        background: url(/img/extjs/icon-square.png) left center no-repeat;
        padding-left: 20px;
        cursor: pointer;
    }
    .task_red{
        background: url(/img/extjs/icon-triangle.png) left center no-repeat;;
        padding-left: 20px;
        cursor: pointer;
    }
    .milestone-mi{
        background-image: url("/img/mi.png");
        background-repeat: no-repeat;
        display: block;
        padding-left: 20px;
        cursor: pointer;
		background-position: left center;
    }
    .milestone-green{
        background-image: url("/img/mi-green.png");
        background-repeat: no-repeat;
        display: block;
        padding-left: 20px;
        cursor: pointer;
		background-position: left center;
    }
    .milestone-blue{
        background-image: url("/img/mi-blue.png");
        background-repeat: no-repeat;
        display: block;
        padding-left: 20px;
        cursor: pointer;
		background-position: left center;
    }
    .milestone-orange{
        background-image: url("/img/mi-orange.png");
        background-repeat: no-repeat;
        display: block;
        padding-left: 20px;
        cursor: pointer;
		background-position: left center;
    }
    .slick-header .slick-header-column{
        padding: 10px 5px !important;
        border-right: 1px solid #5fa1c4 !important;
    }
    .slick-pane-top {
        top: 69px !important;
    }
    .slick-pane-right .slick-cell,
    .slick-pane-right .slick-headerrow-column {
        border-right-color: #aaa;
        border-left: 0;
    }
    .slick-header-columns-right .slick-header-column:nth-child(2n+1){
        background: #09c !important;
    }
    body {
        overflow-y: hidden;
    }
    #wd-container-footer{
        display: none;
    }
	a.action-acttachment{
		display: block;
		text-align: center;
		line-height: 37px;
	}
	a.action-acttachment img{
		width: 25px;
		height: auto;
		cursor: pointer;
		position: relative;
		top: 7px;
	}
	#dialogDetailValue.popup-upload{
		transform: translate(-50%, -50%);
		z-index: 9999;
		max-width: 800px;
		min-width: inherit;
		min-height: auto;
	}
	.task-status-select >*{
		float: left;	
	}
	.task-status-select .status_text{
		width: 105px;
		display: inline-block;
		z-index: 2;
		text-overflow: ellipsis;
		white-space: nowrap;
		overflow: hidden;
	}
	.task-status-select .status_dots{
		position: relative;
		height: 40px;
	}
	.task-status-select .status_dots:before{
		content: '';
		background-color: #F2F5F7;
		height: 4px;
		width: 100%;
		top: 50%;
		margin-top: -2px;
		left: 0;
		position: absolute;
	}
	.task-status-select .status_dot{
		width: 20px;
		height: 20px;
		border: 2px solid #F2F5F7;
		background: #F2F5F7;
		display: inline-block;
		margin: 8px 10px 0 0;
		border-radius: 50%;
		position: relative;
		-webkit-animation-duration: 1s;
		-moz-animation-duration: 1s;
		-o-animation-duration: 1s;
		animation-duration: 1s;
		-webkit-animation-fill-mode: both;
		-moz-animation-fill-mode: both;
		-o-animation-fill-mode: both;
		animation-fill-mode: both;
		box-sizing: border-box;
	}
	.task-status-select .status_dots:not(.disable) .status_dot:hover,
	.task-status-select .status_dot.active{
		border-color: #217FC2;
	}
	.task-status-select .status_dots.disable .status_dot:hover{
		cursor: not-allowed;
	}
	.task-status-select .status_dot.loading{
		border-color: #F2F5F7;
		border-top-color: #217FC2;
	}
	.task-status-select .status_dot.loading{
		border-top-color: #217FC2;
		-webkit-animation: wd-rotate 2s infinite;
		animation: wd-rotate 2s infinite;
	}
	.task-status-select .status_dot:last-child{
		margin-right: 0;
	}
	.task-status-select .status_text:not(.active){
		display: none;
	}
	.task-status-select .task-status >*{
		display: inline-block;
		margin-right: 10px;
		vertical-align: middle;
	}
	.task-status-select .task-status :last-child{
		margin-right: 0;
	}
	.icon-pcname {
	    width: 30px;
	    line-height: 30px;
	    height: 30px;
	    font-size: 14px;
	    top: 0;
	    margin-right: 0;
		background-color: #71BD60;
		border-radius: 50%;
		text-align: center;
		position: relative;
		vertical-align: middle;
		margin-right: 4px;
		display: inline-block;
	}
	.icon-pcname span {
		color: #fff;
		font-weight: 600;
	}
	.slick-cell .circle-name{
		width: 30px;
		height: 30px;
		line-height: 30px;
		font-size: 14px;
		display: inline-block;
		vertical-align: middle;
		margin-right: 6px;
	}
	<?php foreach ( $define_task_colors as $name => $color){?>
		.task-status-select .status_text.status_<?php echo $name;?>{
			color: <?php echo $color;?>;
		}
		.task-status-select .status_dot.active.status_<?php echo $name;?>{
			border-color: <?php echo $color;?>;
		}
		.task-status-select .status_dots:not(.disable) .status_dot.status_<?php echo $name;?>:hover{
			border-color: <?php echo $color;?>;
		}
		.task-status-select .status_dot.loading.status_<?php echo $name;?>{
			border-color: #F2F5F7;
			border-top-color: <?php echo $color;?>;
			padding: 0;
		}
		.wd-title .filter-status.status_<?php echo $name;?> span{
			background-color: <?php echo $color;?>;
		}
		.wd-title .filter-status.status_<?php echo $name;?>.focus{
			border-color: <?php echo $color;?>;
		}
	<?php } ?>
	
	.wd-title .filter-status{
		display: inline-block;
		margin-right: 6px;
		border: 1px solid #E1E6E8;
		line-height: 35px;
		padding: 0 10px;
		cursor: pointer;
		width: 40px;
		height: 40px;
		box-sizing: border-box;
		text-align: center;
	}
	.wd-title .filter-status:last-child{
		margin-right: 0;
	}
	.filter-status .status_dot{
		width: 14px;
		height: 14px;
		border-radius: 50%;
		display: inline-block;
		vertical-align: middle;
	}
	#refresh_menu:before{
		content: "\e098";
		font-family: 'simple-line-icons';
	}
	.wd-title .filter-status .status_square{
		width: 14px;
		height: 14px;
		border-radius: 3px;
		display: inline-block;
		vertical-align: middle;
	}
	
	.wd-title .filter-status .status_triangle{
		width: 0px;
		height: 0px;
		border-left: 7px solid #fff; 
		border-right: 7px solid #fff;
		border-bottom: 14px solid transparent;		
		display: inline-block;
		vertical-align: middle;
	}
	body .slick-pane.slick-pane-header{
		background: transparent;
	}
	.kanban-box, .wd-project-admin .project-task-widget{
		padding: 0;
	}
	.btn.btn-table-collapse{
		top: 0;
		right: 0;
	}
	.wd-project-admin .project-task-widget .jqx-kanban-column{
		border: none;
		padding: 0;
		padding-right: 10px;
	}
	.jqx-kanban-column-container > div .task-item .task-title{
		margin-bottom: 0;
	}
	.jqx-kanban-column-container > div .task-item .project-title{
		font-size: 14px;
	}
	.project-task-widget .status_dot{
		width: 16px;
		height: 16px;
	}
	.jqx-kanban-column-container > div .task-item .project-title{
		font-size: 14px;
	}
	.wd-multiselect.multiselect .circle-name {
		height: 27px;
		width: 27px;
	}
	.form-style-2019 .wd-multiselect.multiselect a.wd-combobox {
		padding-left: 10px;
	}
	.kanban-box .wd-kanban-container{
		width:<?php echo 320*count($projectStatusEX);?>px !important;
		min-width: 100%;
	}
	.wd-multiselect.multiselect .circle-name{
		vertical-align: bottom;
	}
	.table-normal-workload{
		margin-bottom: 30px;
	}
	.task-item .task-item-phase{
		margin-top: 5px;
	}
	#template_logs .content-logs .content .item-content{
		width: calc(100% - 73px);
		display: inline-block;
	}
	#template_logs .content-logs .content .cm-delete{
		display: inline-block;
		width: 20px;
		vertical-align: middle;
		font-size: 16px;
		color: #888;
		top: 10px;
		position: relative;
	}
	#template_logs .content-logs .content .cm-delete:hover{
		color: #F05352;
	}
	#template_logs .content-logs .content a.cm-delete.loading:after{
		content: '';
		width: 15px;
		height: 15px;
		border: 2px solid #E1E6E8;
		border-top-color: #F05352;
		border-radius: 50%;
		animation: wd-rotate 2s infinite;
		position: absolute;
		left: -1px;
		top: -1px;
	}
	#template_edit_task .wd-row-inline >.wd-col{
		min-height:  450px;
	}
</style>
<?php 
$svg_icons = array(
	'grid' =>'<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
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
	'message' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><defs><style>  .cls-1 {fill: #666;fill-rule: evenodd;  }</style>  </defs>  <path id="Z0gMSG" class="cls-1" d="M683.124,30h-6.249a0.625,0.625,0,1,0,0,1.25h6.249A0.625,0.625,0,1,0,683.124,30ZM680,20c-5.523,0-10,3.918-10,8.75a8.375,8.375,0,0,0,3.75,6.824V40l5.12-2.56c0.371,0.036.747,0.059,1.13,0.059,5.523,0,10-3.917,10-8.749S685.523,20,680,20Zm0,16.25c-1.435,0-1.25,0-1.25,0L675,38.125V34.864a7.213,7.213,0,0,1-3.751-6.114c0-4.142,3.918-7.5,8.751-7.5s8.749,3.358,8.749,7.5S684.832,36.25,680,36.25Zm4.374-10h-8.749a0.625,0.625,0,1,0,0,1.25h8.749A0.625,0.625,0,1,0,684.374,26.25Z" transform="translate(-670 -20)"></path></svg>',
	'document' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><defs><style>  .cls-1 {fill: #666;fill-rule: evenodd;  }</style></defs><path id="document" class="cls-1" d="M2023.69,590.749h-7.38a0.625,0.625,0,0,0,0,1.25h7.38A0.625,0.625,0,0,0,2023.69,590.749Zm0-3.75h-7.38a0.626,0.626,0,0,0,0,1.251h7.38A0.626,0.626,0,0,0,2023.69,587Zm4.31,10h0V582.624a0.623,0.623,0,0,0-.62-0.624h-14.76a0.623,0.623,0,0,0-.62.624v18.75a0.624,0.624,0,0,0,.62.624h10.46v0l4.92-5v0Zm-4.92,3.459V597h3.4Zm3.69-4.71h-4.92v5h-8a0.623,0.623,0,0,1-.62-0.624v-16.25a0.625,0.625,0,0,1,.62-0.625h12.3a0.625,0.625,0,0,1,.62.625v11.874Z" transform="translate(-2010 -582)"></path></svg>',
	'edit' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><defs><style>  .cls-1 {fill: #666;fill-rule: evenodd;  }</style></defs><path id="EDIT" class="cls-1" d="M6593.86,260.788c-0.75.767-11.93,11.826-12.4,12.3a0.7,0.7,0,0,1-.27.157c-0.75.224-5.33,1.7-5.37,1.719a0.693,0.693,0,0,1-.2.03,0.625,0.625,0,0,1-.44-0.184,0.636,0.636,0,0,1-.16-0.642c0.01-.044,1.37-4.478,1.64-5.477a0.627,0.627,0,0,1,.16-0.285s11.99-12,12.34-12.343a3.636,3.636,0,0,1,2.42-1.056,3.186,3.186,0,0,1,2.23.981,3.347,3.347,0,0,1,1.18,2.356A3.455,3.455,0,0,1,6593.86,260.788Zm-17.36,12.665c1.21-.39,3.11-1.045,3.97-1.322a3.9,3.9,0,0,0-.94-1.565,4.037,4.037,0,0,0-1.78-1.087C6577.46,270.444,6576.85,272.274,6576.5,273.453Zm3.92-3.789a4.95,4.95,0,0,1,1.07,1.6c2.23-2.2,7.88-7.791,10.33-10.231a3.894,3.894,0,0,0-1.02-1.875,3.944,3.944,0,0,0-1.84-1.1c-2.41,2.409-8.16,8.167-10.37,10.372A5.418,5.418,0,0,1,6580.42,269.664Zm12.51-12.755a1.953,1.953,0,0,0-1.35-.63,2.415,2.415,0,0,0-1.53.69c-0.01.011-.06,0.055-0.09,0.09a5.419,5.419,0,0,1,1.73,1.194,5.035,5.035,0,0,1,1.14,1.763,1.343,1.343,0,0,0,.12-0.119,2.311,2.311,0,0,0,.78-1.534A2.168,2.168,0,0,0,6592.93,256.909Z" transform="translate(-6575 -255)"></path></svg>'
	
);
if(isset($check_ppm_writeTask) && $check_ppm_writeTask == 0){
	$svg_icons['edit'] = '';
}
foreach($fieldset as $key => $_fieldset){
	switch( $key){
		case 'Task':
        $columns[] = array(
            'id' => $key,
            'field' => $key,
            'name' => $_fieldset,
            'width' => 150,
            'cssClass' => 'wd-grey-background',
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.linkFormatter'
        );
	break;
    case 'Status':
		$columns[] = array(
			'id' => $key,
			'field' => $key,
			'name' => $_fieldset,
			'width' => 240,
			'sortable' => true,
			'resizable' => true,
			'formatter' => 'Slick.Formatters.wdSelectTaskStatus'
		);
	break;
    case 'List 1':
	case 'List 2':
	case 'List 3':
	case 'List 4':
	case 'List 5':
	case 'List 6':
	case 'List 7':
	case 'List 8':
	case 'List 9':
	case 'List 10':
	case 'List 11':
	case 'List 12':
	case 'List 13':
	case 'List 14':
	case 'Project type':
	case 'Sub type':
	case 'Sub sub type':
	case 'Implementation Complexity':
	case 'Customer':
		$columns[] = array(
			'id' => $key,
			'field' => $key,
			'name' => $_fieldset,
			'width' => 150,
			'sortable' => true,
			'resizable' => true,
			'formatter' => 'Slick.Formatters.selectBox'
		);
	break;
	case 'List(multiselect) 1':
	case 'List(multiselect) 2':
	case 'List(multiselect) 3':
	case 'List(multiselect) 4':
	case 'List(multiselect) 5':
	case 'List(multiselect) 6':
	case 'List(multiselect) 7':
	case 'List(multiselect) 8':
	case 'List(multiselect) 9':
	case 'List(multiselect) 10':
	case 'Current Phase':
		$columns[] = array(
			'id' => $key,
			'field' => $key,
			'name' => $_fieldset,
			'width' => 200,
			'sortable' => true,
			'resizable' => true,
			'formatter' => 'Slick.Formatters.selectBox'
		);
	break;
    case 'Milestone':
        $columns[] = array(
            'id' => $key,
            'field' => $key,
            'name' => $_fieldset,
            'width' => 100,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.milestoneFormatter'
        );
	break;
    case 'Start':
	case 'End':
        $columns[] = array(
            'id' => $key,
            'field' => $key,
            'name' => $_fieldset,
            'width' => 100,
            'sortable' => true,
            'resizable' => true,
            'datatype' => 'datetime',
            'formatter' => 'Slick.Formatters.taskImage'
        );
	break;
    case 'Initial start':
	case 'Initial end':
        $columns[] = array(
            'id' => $key,
            'field' => $key,
            'name' => $_fieldset,
            'width' => 100,
            'sortable' => true,
            'resizable' => true,
            'datatype' => 'datetime',
            'formatter' => 'Slick.Formatters.taskImage'
        );
	break;
    case 'Workload':
        $columns[] = array(
            'id' => $key,
            'field' => $key,
            'name' => $_fieldset,
            'width' => 100,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.taskImage'
        );
	break;
    case 'Text':
        $columns[] = array(
            'id' => $key,
            'field' => $key,
            'name' => $_fieldset,
            'width' => 200,
            'sortable' => false,
            'resizable' => true,
            'datatype' => 'number',
            'formatter' => 'Slick.Formatters.taskComment'
        );
	break;
    case 'Assigned':
    case 'Project Manager':
	case 'Technical manager':
	case 'Read Access':
	case 'UAT manager':
	case 'Chief Business':
	case 'Functional leader':
        $columns[] = array(
            'id' => $key,
            'field' => $key,
            'name' => $_fieldset,
            'width' => 150,
            'sortable' => false,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.assign'
        );
	break;
	case 'Team':
        $columns[] = array(
            'id' => $key,
            'field' => $key,
            'name' => $_fieldset,
            'width' => 80,
            'sortable' => false,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.assign'
        );
	break;
    case 'Consume':
	case 'Initial workload':
	case 'Duration':
	case 'Overload':
	case 'In Used':
	case 'Completed':
	case 'Remain':
	case 'Amount':
	case 'Progress orderr':
        $columns[] = array(
            'id' => $key,
            'field' => $key,
            'name' => $_fieldset,
            'width' => 100,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.valueFormat'
        );
	break;
    case 'Attachment':
		 $columns[] = array(
            'id' => $key,
            'field' => $key,
            'name' => $_fieldset,
            'width' => 100,
            'sortable' => true,
            'resizable' => true,
			'formatter' => 'Slick.Formatters.contextAttachment'
		);
	break;
	case 'Project name':
		 $columns[] = array(
            'id' => $key,
            'field' => $key,
            'name' => $_fieldset,
            'width' => 250,
            'sortable' => true,
            'resizable' => true,
		);
	break;
	default:
        $columns[] = array(
            'id' => $key,
            'field' => $key,
            'name' => $_fieldset,
            'width' => 100,
            'sortable' => false,
            'resizable' => true
        );
    }
}
foreach($columns as $key => $column){
	if(!empty($loadFilter) && !empty($loadFilter[$column['field']. '.Resize'])){
		$columns[$key]['width'] = intval($loadFilter[$column['field']. '.Resize']);
	}
}
$i = 1;
$dataView = array();
$statusOrderWeight = array();
foreach($listStatus as $id => $name){
	$statusOrderWeight[] = array(
		'id' => $id,
		'name' => $name,
	);
}
$listMilestoneName = array();
if(!empty($listMilestone)){
	foreach($listMilestone as $id => $name){
		$key = str_replace(' ', '_', strtolower($name));
		$listMilestoneName[$key] = $name;
	}
}
$listParts = array();
if(!empty($listPartNames)){
	foreach($listPartNames as $id => $name){
		$key = str_replace(' ', '_', strtolower($name));
		$listParts[$key] = $name;
	}
}
$selectMaps = array(
    'Status' => $statusOrderWeight,
    // 'Status' => $listStatus,
    'Priority' => $listPriority,
    'Sub program' => $projectSubProgram,
    'Program' => $projectProgram,
    'Lot' => $listParts,
    'Phase' => $listPhaseNames,
    'Milestone' => $listMilestoneName,
    'Assigned' => $listAssignTasksFilter,
    'List 1' => !empty($projectDatasets['list_1']) ? $projectDatasets['list_1'] : array(),
    'List 2' => !empty($projectDatasets['list_2']) ? $projectDatasets['list_2'] : array(),
    'List 3' => !empty($projectDatasets['list_3']) ? $projectDatasets['list_3'] : array(),
    'List 4' => !empty($projectDatasets['list_4']) ? $projectDatasets['list_4'] : array(),
    'List 5' => !empty($projectDatasets['list_5']) ? $projectDatasets['list_5'] : array(),
    'List 6' => !empty($projectDatasets['list_6']) ? $projectDatasets['list_6'] : array(),
    'List 7' => !empty($projectDatasets['list_7']) ? $projectDatasets['list_7'] : array(),
    'List 8' => !empty($projectDatasets['list_8']) ? $projectDatasets['list_8'] : array(),
    'List 9' => !empty($projectDatasets['list_9']) ? $projectDatasets['list_9'] : array(),
    'List 10' => !empty($projectDatasets['list_10']) ? $projectDatasets['list_10'] : array(),
    'List 11' => !empty($projectDatasets['list_11']) ? $projectDatasets['list_11'] : array(),
    'List 12' => !empty($projectDatasets['list_12']) ? $projectDatasets['list_12'] : array(),
    'List 13' => !empty($projectDatasets['list_13']) ? $projectDatasets['list_13'] : array(),
    'List 14' => !empty($projectDatasets['list_14']) ? $projectDatasets['list_14'] : array(),
    'Project type' => $projectTypes,
    'Sub type' => $projectSubTypes,
    'Sub sub type' => $projectSubTypes,
    'Implementation Complexity' => $projectComplexities,
    'Customer' => $budgetCustomers,
    'Current Phase' => $projectPhases,
    'Team' => $profitCenters,
    'List(multiselect) 1' => !empty($projectDatasets['list_muti_1']) ? $projectDatasets['list_muti_1'] : array(),
    'List(multiselect) 2' => !empty($projectDatasets['list_muti_2']) ? $projectDatasets['list_muti_2'] : array(),
    'List(multiselect) 3' => !empty($projectDatasets['list_muti_3']) ? $projectDatasets['list_muti_3'] : array(),
    'List(multiselect) 4' => !empty($projectDatasets['list_muti_4']) ? $projectDatasets['list_muti_4'] : array(),
    'List(multiselect) 5' => !empty($projectDatasets['list_muti_5']) ? $projectDatasets['list_muti_5'] : array(),
    'List(multiselect) 6' => !empty($projectDatasets['list_muti_6']) ? $projectDatasets['list_muti_6'] : array(),
    'List(multiselect) 7' => !empty($projectDatasets['list_muti_7']) ? $projectDatasets['list_muti_7'] : array(),
    'List(multiselect) 8' => !empty($projectDatasets['list_muti_8']) ? $projectDatasets['list_muti_8'] : array(),
    'List(multiselect) 9' => !empty($projectDatasets['list_muti_9']) ? $projectDatasets['list_muti_9'] : array(),
    'List(multiselect) 10' => !empty($projectDatasets['list_muti_10']) ? $projectDatasets['list_muti_10'] : array(),
    'List(multiselect) 10' => !empty($projectDatasets['list_muti_10']) ? $projectDatasets['list_muti_10'] : array(),
    'Project Manager' => !empty($projectDatasets['list_muti_10']) ? $projectDatasets['list_muti_10'] : array(),
);
$i18n = array(
	'-- Any --' => __('-- Any --', true),
	'M.D' => __('M.D', true),
	'minute' => __('cmMinute', true),
	'hour' => __('cmHour', true),
	'day' => __('cmDay', true),
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
	'Add new task' => __('Add new task', true),
	'Workload' => __('Workload', true),
	'Consumed' => __('Consumed', true),
	'M.D' => __('M.D', true),
	'Permission denied' => __('Permission denied', true),
	
);
for ($m=1; $m<=12; $m++) {
	$month = date('F', mktime(0,0,0,$m, 1, 2019));
	$i18n[$month] = __($month,true);
}
$can_move = 0;
$list_project_has_nct = array();
$listAllAssigned = array();
$canDiaryModify = (!empty($companyConfigs['diary_modify']) && $companyConfigs['diary_modify'] == 1) ? 1 : 0;
$canDiaryOtherField = (!empty($companyConfigs['diary_others_fields']) && $companyConfigs['diary_others_fields'] == 1) ? 1 : 0;
$canAddComment = (!empty($companyConfigs['diary_modify']) && !empty($companyConfigs['diary_others_fields']) && ($companyConfigs['diary_modify'] == 1) && ($companyConfigs['diary_others_fields'] == 1)) ? 1 : 0;
foreach ($datas as $id => $data) {
	$_data['DataSet'] = array(
		'canModified' => ($employee_info['Role']['name'] == 'admin' || ($companyConfigs['diary_modify'] == 1 && $companyConfigs['diary_status'] == 1) || (isset($listEmployeeManagerOfT[$id]) && $employee_info['Role']['name'] == 'pm')) ? 1 : 0,
	);
	$datas[$id]['canModified'] = ($employee_info['Role']['name'] == 'admin' || ($companyConfigs['diary_modify'] == 1 && $companyConfigs['diary_status'] == 1) || (isset($listEmployeeManagerOfT[$id]) && $employee_info['Role']['name'] == 'pm')) ? 1 : 0;
	if($datas[$id]['canModified'] == 1){
		$can_move = 1;
	}
    $_data['Program'] = (string) !empty($data['amr_program']) ? $data['amr_program'] : '';
    $_data['Sub program'] = (string) !empty($data['sub_amr_program']) ? $data['sub_amr_program'] : '';
    $_data['Project name'] = (string) !empty($data['project_name']) ? $data['project_name'] : '';
    $_data['Lot'] = (string) !empty($data['part_name']) ? str_replace(' ', '_', strtolower($listPartNames[$data['part_name']])): '';
    $_data['Phase'] = (string) !empty($data['phase_name']) ? $data['phase_name'] : '';
    $_data['Task'] = (string) !empty($data['task_title']) ? $data['task_title'] : '';
    $_data['Status'] = (string) !empty($data['status']) ? $data['status'] : '';
    $_data['Milestone'] = (string) !empty($data['milestone']) ? str_replace(' ', '_', strtolower($listMilestone[$data['milestone']])): '';
    $_data['milestone_id'] = (string) !empty($data['milestone']) ? $data['milestone'] : '';
    $_data['Priority'] = (string) !empty($data['priority']) ? $data['priority'] : '';
    $_data['Assigned'] = !empty($data['assigned']) ? $data['assigned'] : '';
	if( !empty($data['assigned'])) $listAllAssigned = array_merge( $listAllAssigned, $data['assigned']);
    $_data['Start'] = !empty($data['start_date']) ? $data['start_date'] : '';
    $_data['End'] = !empty($data['end_date']) ? $data['end_date'] : '';
    $_data['Workload'] = !empty($data['workload']) ? $data['workload'] : 0;
    $_data['Consume'] = !empty($data['consume']) ? $data['consume'] : 0;
    $_data['Code project'] = (string) !empty($data['code_project_1']) ? $data['code_project_1'] : '';
    $_data['Code project 1'] = (string) !empty($data['code_project_2']) ? $data['code_project_2'] : '';
    $_data['Text'] = (string) !empty($data['text']) ? $data['text'] : '';
    $_data['text_time'] = (string) !empty($data['text_time']) ? $data['text_time'] : 0;
    $_data['text_updater'] = (string) !empty($data['text_updater']) ? $data['text_updater'] : '';
    $_data['text_empl'] = (string) !empty($data['text_empl']) ? $data['text_empl'] : '';
    $_data['Initial workload'] = !empty($data['initial_estimated']) ? $data['initial_estimated'] : 0;
    $_data['Initial start'] = !empty($data['initial_task_start_date']) ? $data['initial_task_start_date'] : '';
    $_data['Initial end'] = !empty($data['initial_task_end_date']) ? $data['initial_task_end_date'] : '';
    $_data['Duration'] = !empty($data['duration']) ? $data['duration'] : 0;
    $_data['Overload'] = !empty($data['overload']) ? $data['overload'] : 0;
    $_data['In Used'] = !empty($data['in_used']) ? $data['in_used'] : 0;
    $_data['Completed'] = !empty($data['completed']) ? $data['completed'] . ' %' : 0 . ' %';
    $_data['Remain'] = !empty($data['remain']) ? $data['remain'] : 0;
    $_data['Amount'] = !empty($data['amount']) ? $data['amount'] . ' '.$bg_currency : 0 . ' '.$bg_currency;
    $_data['Progress order'] = !empty($data['progress_order']) ? $data['progress_order'] . ' %' : 0 . ' %';
    $_data['id'] = $id;
    $_data['idPr'] = $data['project_id'];
    $_data['no.'] = $id;
    $_data['enddate'] = strtotime( !empty($data['end_date']) ? $data['end_date'] : '' );
    $_data['today'] = strtotime(date('d-m-Y'));
    $_data['ed_format'] = !empty($data['end_date']) ? date('d-m-Y-W', strtotime($data['end_date'])) : '';
    $_data['cu_format'] = date('d-m-Y-W');
    $_data['current'] = time();
    $_data['Attachment'] = $data['attachment_count'];
    $_data['attach_read_status'] = $data['attach_read_status'];
    $_data['task_late_status'] = $data['task_late_status'];
	if($data['is_nct'] == 1 && !in_array( $data['project_id'], $list_project_has_nct)) $list_project_has_nct[] = $data['project_id'];
	
	$p_id = $data['project_id'];
	$keys = array(
		'Project Manager',
		'Technical manager',
		'Read Access',
		'UAT manager',
		'Chief Business',
		'Functional leader',
	);
	foreach($keys as $k){
		$_data[$k] = isset($listProject[$p_id][$k]) ? $listProject[$p_id][$k] : array();
		foreach( $_data[$k] as $e_id){
			$selectMaps[$k][$e_id] = $companyEmployees[$e_id];
		}
	}
	$keys = array(
		'Current Phase',
	);
	foreach($keys as $k){
		$_data[$k] = isset($listProject[$p_id][$k]) ? $listProject[$p_id][$k] : array();
	}
	$keys = array(
		'List 1',
		'List 2',
		'List 3',
		'List 4',
		'List 5',
		'List 6',
		'List 7',
		'List 8',
		'List 9',
		'List 10',
		'List 11',
		'List 12',
		'List 13',
		'List 14',
	);
	foreach($keys as $k){
		$refrence_key = str_replace(' ', '_', strtolower($k));
		$_data[$k] = !empty($listProject[$p_id][$refrence_key]) ? $listProject[$p_id][$refrence_key] : '';
	}
	$keys = array(
		'List(multiselect) 1' => 'project_list_multi_1',
		'List(multiselect) 2' => 'project_list_multi_2',
		'List(multiselect) 3' => 'project_list_multi_3',
		'List(multiselect) 4' => 'project_list_multi_4',
		'List(multiselect) 5' => 'project_list_multi_5',
		'List(multiselect) 6' => 'project_list_multi_6',
		'List(multiselect) 7' => 'project_list_multi_7',
		'List(multiselect) 8' => 'project_list_multi_8',
		'List(multiselect) 9' => 'project_list_multi_9',
		'List(multiselect) 10' => 'project_list_multi_10',
	);
	foreach($keys as $k => $refrence_key){
		$_data[$k] = isset($listProject[$p_id][$refrence_key]) ? $listProject[$p_id][$refrence_key]: array();
	}
	$keys = array(
		'Project type' => 'project_type_id',
		'Sub type' => 'project_sub_type_id',
		'Sub sub type' => 'project_sub_sub_type_id',
		'Implementation Complexity' => 'complexity_id',
		'Customer' => 'budget_customer_id',
		// 'Status' => 'project_status_id', // trùng task Status
		// 'Priority' => 'project_priority_id',// trùng task Priority
	);
	foreach($keys as $k => $refrence_key){
		$_data[$k] = !empty($listProject[$p_id][$refrence_key]) ? $listProject[$p_id][$refrence_key] : '';
	}
	$keys = array(
		'Team' => 'team',
		// 'Status' => 'project_status_id', // trùng task Status
		// 'Priority' => 'project_priority_id',// trùng task Priority
	);
	foreach($keys as $k => $refrence_key){
		$_data[$k] = !empty($listProject[$p_id][$refrence_key]) ? array($listProject[$p_id][$refrence_key]) : array();
	}
	$dataView[] = $_data;
}
// ob_clean();
// debug($dataView);
// exit;
$listAllAssigned = array_unique( $listAllAssigned);
$listAvatar = array(
	'listEmployeeAssigned' => array(),
	'listPCAssigned' => array()
);
foreach( $listAllAssigned as $empl){
	$empl = explode( '_', $empl);
	if( empty($empl[1])) {
		$listAvatar['listEmployeeAssigned'][$empl[0]] = $listEmployeeName[$empl[0]]['fullname'];
	}else{
		$listAvatar['listPCAssigned'][$empl[0]] = $listEmployeeName[$empl[0].'-'.$empl[1]]['name'];
	}
}
// ob_clean(); debug( $listAvatar); exit;
$myAvatar = '';
$avatarEmploys = array();
foreach ($listIdAva as $key => $value) {
    $avatarEmploys[$key] = $this->UserFile->avatar($value);
    if($value == $employee_id){
        $myAvatar = $avatarEmploys[$key];
    }
}

$file_upload = array();
if(!empty($attachedFile)){
	foreach ($attachedFile as $key => $value) {
		if ( preg_match('/\.(jpg|jpeg|bmp|gif|png|swf)$/i', $value)) {
			$file_upload[$key] = $this->Html->url(array('controller' => 'kanban', 'action' => 'attachment', $key, '?' => array('sid' => $api_key)));
		}else{
			$file_upload[$key] = $this->Html->url(array('controller' => 'kanban', 'action' => 'attachment', $key, '?' => array('download' => true, 'sid' => $api_key)),true);
		}
	}
}
?>

 
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
	<div class="wd-main-content">
	<div class="wd-tab"> 
		<div class="wd-panel">
            <div class="wd-list-project">
			<div class="wd-flex-title">
				<div class="wd-task-title wd-title clearfix">
					<a href="javascript:void(0)" class="btn" onclick="resetFilter();" id="refresh_menu" title="<?php __('Refresh')?>"><span class="icon-refresh"></span></a>
					<a href="<?php echo $this->Html->url(array('action' => 'export'));?>" id="export-table" class="btn btn-excel" title="<?php __('Export Excel')?>"></a>
					<?php if(($on_newdesign_assistant == 1) && ($roleLogin != 'conslt')){ ?>
						<a href="javascript:void(0);" id="add-task-button" class="btn btn-add-new" title="<?php __('Add new task')?>" onclick="wd_show_add_task()"></a>
					<?php }?>
					<a href="javascript:void(0);" onclick="expandScreen();" class="btn hide-on-mobile" id="expand-btn" title="<?php __('Expand');?>">
						<img src="<?php echo $html->url('/img/new-icon/expand.png') ?>"  />
					</a>
					<div class="filter-by-type">
						<a class="filter-status status_green <?php if( $filter_color == 'green') echo 'focus'; ?>" data-type="green" title="<?php __('Filter the task on time')?>"><span class="status_square"></span></a>
						<a class="filter-status status_red <?php if( $filter_color == 'red') echo 'focus'; ?>" data-type="red" title="<?php __('Filter the task in delay')?>"><span class="status_triangle"></span></a>
					</div>
					<a href="javascript:void(0);" style="display: none;"class="btn btn-table-collapse" id="collapse-btn" onclick="collapseScreen();" title="<?php __('Collapse table');?>" style=""></a>
					<a href="javascript:void(0);" class="btn btn-kanban"><?php echo $svg_icons['grid'] ?></a>
					<form method="get" action="" id="form_view_display">
						<div class="filter-select">
							<select id="filter-view" name="view">
								<option value="all"><?php echo __("All", true); ?></option>
								<option value="today"><?php echo __("Today", true); ?></option>
								<option value="week"><?php echo __("Week", true); ?></option>
								<option value="month"><?php echo __("Month", true); ?></option>
								<option value="year"><?php echo __("Year", true); ?></option>
							</select>
						</div>
					</form>
					<?php
					echo $this->Form->input('filter_task_title', array(
						'type'=> 'text',
						'id' => 'filter_task_title',
						// 'label' => __('Filter by title', true),
						'label' => false,
						'div' => array(
							'style' => 'display: none',
						),
						'required' => false,
						'autocomplete' => 'off',
						'onchange'=> 'filterTask(this);',
						'rel' => 'no-history',
						'placeholder' => __('Filter by title', true),
					));
					?>
					<div class="btn filter_submit" onclick="filterTask(this);" title="<?php __('Search');?>">
						<img src="<?php echo $this->Html->url('/img/new-icon/search.png');?>" all="search">
					</div>
					<a href="javascript:void(0);" class="btn btn-reset-filter hidden" id="clean-filters" style="display: none;" onclick="resetFilter();" title="<?php __('Reset filter') ?>"></a>
				</div>
				<div class="wd-list-assign">
					<div class="wd-list-assign-inner">
						<div class="box-ellipsis list-all-assigned" id="list-all-assigned" style="display: none;">
							<?php if(!empty($listAvatar)){ ?>
							<ul>
								<?php foreach ($listAvatar as $key => $value) {
									foreach( $value as $_id => $_name){
										$avt = '<i class="icon-people"></i>';
										$is_pc = 1;
										if($key == 'listEmployeeAssigned'){
											$avt = '<img src="' . $html->url('/img/avatar/'.$_id.'.png') .'" alt="avatar">';
											$is_pc = 0;
										}
										$emp = $_id . '-' . $is_pc;
										?>
										<li data-emp="<?php echo $emp;?>" class="<?php echo ( $is_pc ? 'assign-team' : '');?>">
											<div class="wd-input-checkbox-custom"  title="<?php echo $_name?>" >
												<input type="checkbox" onchange="filterTask(this);" name="data[Filter][employee][<?php echo $emp;?>]" class="filter-employee wd-hide" id="filter-employee-<?php echo $emp;?>" data-id="<?php echo $emp;?>" rel="no-history">
												<label for="filter-employee-<?php echo $emp;?>">
													<span class="circle-name"><?php echo $avt;?></span>
												</label>
											
											</div>
										</li>
										
									<?php
									}									
								   
								} ?>
							</ul>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
			</div>
				<?php echo $this->Session->flash(); ?>
			<div class="kanban-box">
				<div class="wd-kanban-container">
					<div id="kanban" class="project-task-widget kanban-task"></div>
				</div>
			</div>
			<div class="wd-table project-list wd-table-2018" id="project_container" style="width: 100%; height: 600px;"></div>
			<div id="pager" style="clear:both;width:100%;height:0;"></div>
			
		</div>
    </div>
    </div>
    </div>
    </div>
</div>
<div id="template_add_task_index" class="wd-full-popup" style="display: none;">  <!-- --> 
	<div class="wd-popup-inner">
		<div class="new-task-popup loading-mark wd-popup-container">
			<div class="wd-popup-head clearfix"> 
				<h4 class="active">
					<?php __('Add new task');?>
				</h4> 
				<a href="javascript:void(0);" class="close_template_add_task wd-close-popup" onclick="cancel_popup(this)"><img title="<?php __('Close');?>" src="<?php echo $this->Html->url('/img/new-icon/close.png');?>"></a>
			</div>
			<div class="new-task-popup-content wd-popup-content">
				<?php 
				echo $this->Form->create('ProjectTask', array(
					'type' => 'POST',
					'url' => array('controller' => 'project_tasks_preview', 'action' => 'add_new_task_popup'),
					'class' => 'form-style-2019'
					)
				);

				?>
				<p style="color: red" class="alert-message"></p>
				<?php if( $show_workload) { ?>
					<div class="wd-row">
						<div class="wd-col wd-col-sm-8">
				<?php } 
				echo $this->Form->input('return', array(
					'type'=> 'hidden',
					'value' => $this->Html->url(),
					
				));
				echo $this->Form->input('project_id', array(
					'type'=> 'select',
					'id' => 'toProjectAdd',
					'label' => __('Project name', true),
					'empty' => __('Project name', true),
					'options' => $listProjectbyPM,
					'required' => true,
					'rel' => 'no-history',
					'onchange'=> 'popupAddTask_projectOnChange(this);',
					'div' => array(
						'class' => 'wd-input'
					)
					
				));
				?>
				<div class="input select required wd-input">
					<label for="toPhaseAdd"> <?php __('Phase');?></label>
					<select name="data[ProjectTask][project_planed_phase_id]" id="toPhaseAdd" required="1" rel ="no-history" disabled onchange="popupAddTask_phaseOnChange(this);">
						<option value=""><?php __('Phase');?></option>
						
					</select>
				</div>
				<?php
				echo $this->Form->input('task_title', array(
					'type'=> 'text',
					'id' => 'newTaskName',
					'label' => __('Task name', true),
					'required' => true,
					'rel' => 'no-history',
					'div' => array(
						'class' => 'wd-input label-inline'
					)
				));
				?>
				<div class="wd-row">
					<div class="wd-col wd-col-sm-6">
						<div class="wd-input wd-area wd-none ">
							<label><?php  __d(sprintf($_domain, 'Project_Task'), 'Assigned To');?></label>
							<div class="wd-multiselect multiselect multiselect-pm" id="multiselect-pm-add">
								<a href="javasctipt:void(0);" class="wd-combobox wd-project-manager disable">
									<p style="position: absolute; color: rgb(198, 204, 207); display: block;">
										<?php __d(sprintf($_domain, 'Project_Task'), 'Assigned To');?>
									</p>
								</a>
								<div class="wd-combobox-content task_assign_to_id" style="">
									<div class="context-menu-filter">
										<span>
											<input type="text" class="wd-input-search" placeholder="<?php __('Search...');?>" rel="no-history">
										</span>
									</div>
									<div class="option-content"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="wd-col wd-col-sm-6">
					<?php 
						echo $this->Form->input('task_status_id', array(
							'type'=> 'select',
							'id' => 'toProjectAdd',
							'label' => __d(sprintf($_domain, 'Project_Task'), 'Status', true),
							'empty' => __d(sprintf($_domain, 'Project_Task'), 'Status', true),
							'options' => $listStatus,
							'required' => true,
							'rel' => 'no-history',
							'div' => array(
								'class' => 'wd-input'
							)
							
						));
						?>
					</div>
				</div>
				<div class="wd-row">
					<div class="wd-col wd-col-sm-6">
						<div class="wd-input wd-area wd-none">
						<?php 				
							echo $this->Form->input('task_start_date', array(
								'type'=> 'text',
								'id' => 'newTaskStartDay',
								'label' => __d(sprintf($_domain, 'Project_Task'), 'Start date', true),
								'required' => true,
								'class' => 'wd-date',
								'onchange'=> 'validated(this);',
								'autocomplete' => 'off',
								'rel' => 'no-history',
								'div' => array(
									'class' => 'wd-input label-inline'
								)
								
							));
							?>
						</div>
					</div>
					<div class="wd-col wd-col-sm-6">
					<?php 				
						echo $this->Form->input('task_end_date', array(
							'type'=> 'text',
							'id' => 'newTaskEndDay',
							'label' => __d(sprintf($_domain, 'Project_Task'), 'End date', true),
							'required' => true,
							'class' => 'wd-date',
							'autocomplete' => 'off',
							'onchange'=> 'validated(this);',
							'rel' => 'no-history',
							'div' => array(
								'class' => 'wd-input label-inline'
							)
							
						));
						?>
					</div>
				</div>
				<div id="popup_template_attach" >
					<div class="heading">
						
					</div> 
					<div class="trigger-upload">
						<div id="wd-upload-popup-index" method="post" action="<?php echo $this->Html->url(array('controller' => 'project_tasks_preview', 'action' => 'add_new_task_popup')); ?>" class="dropzone" value="" >
						</div>
					</div>
				</div>
				<?php if( $show_workload) { ?>
					</div>
					<div class="wd-col wd-col-sm-4">
						<div class="task-workload">
							<table id="new_task_assign_table" class="nct-assign-table">
								<thead>
									<tr>
										<td class="bold base-cell null-cell"><?php __('Resources');?> </td>
										<td class="bold base-cell null-cell"><?php  __d(sprintf($_domain, 'Project_Task'), 'Workload')?></td>
									</tr>
								</thead>
								<tbody>
									
								</tbody>
								<tfoot>
									<tr>
										<td class="base-cell"><?php __('Total') ?></td>
										<td class="base-cell total-consumed" id="total-consumed">0</td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
				<?php } ?>
				<div class="wd-row wd-submit-row">
					<div class="wd-col-xs-12">
						<div class="wd-submit">
							<button type="submit" class="btn-form-action btn-ok btn-right" id="btnSave">
								<span><?php __('Submit') ?></span>
							</button>
							<a class="btn-form-action btn-cancel" id="reset_button" href="javascript:void(0);" onclick="cancel_popup(this);">
								<?php echo __("Cancel", true); ?></span>
							</a>
						</div>
					</div>
				</div>
				<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>
<div id="template_edit_task" class="wd-full-popup" style="display: none;">
	<div class="wd-popup-inner">
		<div class="edit_task-popup loading-mark wd-popup-container" style="width: 580px;">
			<div class="wd-popup-head clearfix"> 
				<h4 class="active">
					<?php __('Edit task');?>
				</h4> 
				<a href="javascript:void(0);" class="close_template_add_task wd-close-popup" onclick="cancel_popup(this)"><img title="<?php __('Close');?>" src="<?php echo $this->Html->url(array(
					'controller' => 'img',
					'action' => 'new-icon',
					'close.png'
				));?>"></a>
			</div>
			<div class="edit_task-popup-content wd-popup-content">
			<!-- form -->
			<?php 
				echo $this->Form->create('ProjectTask', array(
					'type' => 'POST',
					'url' => array('controller' => 'project_tasks', 'action' => 'update_task'),
					'class' => 'form-style-2019',
					'id' => 'ProjectTask',
					)							
				);
				?>
				<p style="color: red" class="alert-message"></p>
				<?php 
				if( $show_workload) { ?>
					<div class="wd-row wd-row-inline">
						<div class="wd-col wd-col-sm-8">
				<?php } 
				echo $this->Form->input('id', array(
					'type'=> 'hidden',
					'value' => '',
					'id' =>'editTaskID'
				));
				echo $this->Form->input('task_title', array(
					'type'=> 'text',
					'id' => 'task_title',
					'label' => __('Task name', true),
					'required' => true,
					'rel' => 'no-history',
					'div' => array(
						'class' => 'wd-input label-inline'
					)
				));
				?>
				<div class="wd-input label-inline wd-input placeholder has-val">
					<label for="toPhaseEdit"> <?php __('Phase');?></label>
					<select name="data[ProjectTask][project_planed_phase_id]" id="toPhaseEdit" required="1" rel ="no-history" disabled onchange="popupEditTask_phaseOnChange(this);">
					</select>
					
				</div>
				<div class="wd-row">
					<div class="wd-col wd-col-sm-6">
						<div class="wd-input wd-area wd-none ">
							<!-- <label><?php __('Assign to');?></label> -->
							<div class="wd-multiselect multiselect multiselect-pm" id="multiselect-pm-edit">
								<a href="javasctipt:void(0);" class="wd-combobox wd-project-manager disable">
									<p style="position: absolute; color: rgb(198, 204, 207); display: block;">
										<?php  __d(sprintf($_domain, 'Project_Task'), 'Assigned To');?>
									</p>
								</a>
								<div class="wd-combobox-content task_assign_to_id" style="">
									<div class="context-menu-filter">
										<span>
											<input type="text" class="wd-input-search" placeholder="<?php __('Search...');?>" rel="no-history">
										</span>
									</div>
									<div class="option-content"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="wd-col wd-col-sm-6">
					<?php 
						echo $this->Form->input('task_status_id', array(
							'type'=> 'select',
							'id' => 'toStatus',
							'label' => __d(sprintf($_domain, 'Project_Task'), 'Status', true),
							'options' => $listStatus,
							'required' => true,
							'rel' => 'no-history',
							'div' => array(
								'class' => 'wd-input label-inline has-val'
							)
							
						));
						?>
					</div>
				</div>
				<div class="wd-row">
					<div class="wd-col wd-col-sm-6">
						<div class="wd-input wd-area wd-none">
						<?php 				
							echo $this->Form->input('task_start_date', array(
								'type'=> 'text',
								'id' => 'editTaskStartDay',
								'label' => __d(sprintf($_domain, 'Project_Task'), 'Start date', true),
								'required' => true,
								'class' => 'wd-date',
								'onchange'=> 'editTaskValidated(this);',
								'autocomplete' => 'off',
								'rel' => 'no-history',
								'div' => array(
									'class' => 'wd-input label-inline'
								)
								
							));
							?>
						</div>
					</div>
					<div class="wd-col wd-col-sm-6">
					<?php 				
						echo $this->Form->input('task_end_date', array(
							'type'=> 'text',
							'id' => 'editTaskEndDay',
							'label' => __d(sprintf($_domain, 'Project_Task'), 'End date', true),
							'required' => true,
							'class' => 'wd-date',
							'autocomplete' => 'off',
							'onchange'=> 'editTaskValidated(this);',
							'rel' => 'no-history',
							'div' => array(
								'class' => 'wd-input label-inline'
							)
							
						));
						?>
					</div>
				</div>
				<?php if( $show_workload) { ?>
					</div>
					<div class="wd-col wd-col-sm-4">
						<div class="task-workload table-normal-workload">
							<table id="edit_task_assign_table" class="nct-assign-table">
								<thead>
									<tr>
										<td class="bold base-cell null-cell"><?php __('Resources');?> </td>
										<td class="bold base-cell null-cell"><?php  __d(sprintf($_domain, 'Project_Task'), 'Workload')?></td>
									</tr>
								</thead>
								<tbody>
									
								</tbody>
								<tfoot>
									<tr>
										<td class="base-cell"><?php __('Total') ?></td>
										<td class="base-cell total-consumed" id="total-consumed">0</td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
				<?php } ?>
				<div class="wd-row wd-submit-row">
					<div class="wd-col-xs-12">
						<div class="wd-submit">
							<button type="submit" class="btn-form-action btn-ok btn-right" id="btnSave">
								<span><?php __('Save') ?></span>
							</button>
							<a class="btn-form-action btn-cancel" id="reset_button" href="javascript:void(0);" onclick="cancel_popup(this);">
								<?php echo __("Cancel", true); ?></span>
							</a>
						</div>
					</div>
				</div>
				<?php echo $this->Form->end(); ?>
			<!-- END  form -->
			</div>				
		</div>				
	</div>
</div>
<div id="template_upload" class="template_upload" style="height: auto; width: 320px;">
	<div class="heading">
        <h4><?php echo __('File upload(s)', true)?></h4>
        <span class="close close-popup"><img title="close"  src="<?php echo $html->url('/img/new-icon/close.png'); ?>"/></span>
    </div>
    <div id="content_comment">
        <div class="append-comment"></div>
    </div>
		<div class="wd-popup">
			<?php 
			echo $this->Form->create('Upload', array(
				'type' => 'POST',
				'url' => array('controller' => 'kanban','action' => 'update_document')));
				?>
				
					<div class="trigger-upload">
						<div id="upload-popup" method="post" action="/kanban/update_document/" class="dropzone" value="" >
						</div>
					</div>
				
				<?php echo $this->Form->input('url', array(
					'class' => 'not_save_history',
					'label' => array(
						'class' => 'label-has-sub',
						'text' =>__('URL Link',true),
						'data-text' => __('(optionnel)', true),
						),
					'type' => 'text',
					'id' => 'newDocURL',  
					'placeholder' => __('https://', true)));    
				?>                    
				<input type="hidden" name="data[Upload][id]" rel="no-history" value="" id="UploadId">
				<input type="hidden" name="data[Upload][project_id]" rel="no-history" value="" id="UploadProjectId">
			<?php echo $this->Form->end(); ?>
		</div>
		<ul class="actions" style="">
			<li><a href="javascript:void(0)" class="cancel"><?php __("Upload Cancel") ?></a></li>
			<li><a href="javascript:void(0)" class="new" id="ok_attach"><?php __('Upload Validate') ?></a></li>
		</ul>
</div>
<div id="template_logs" style="height: 420px; width: 320px;display: none;">
    <div class="add-comment"></div>
    <div class="content_comment" style="min-height: 50px">
    <div class="append-comment"></div>
    </div>
    
</div>

<?php echo $this->element('dialog_detail_value') ?>
<?php echo $this->element('dialog_projects') ?>
<?php
	// ob_clean();
	// debug($myRole);
	// exit;
?>
<div id="addProjectTemplate" class="loading-mark"></div>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
	var dataGrid;
	var api_key = <?php echo json_encode($api_key) ?>;
    var DataValidator = {};
    var listIdModifyByPm = <?php echo json_encode($listIdModifyByPm) ?>;
    var clStatus = <?php echo json_encode($clStatus) ?>;
    var filter_color = <?php echo json_encode($filter_color) ?>;
    var listMilestoneName = <?php echo json_encode($listMilestoneName) ?>;
    var _milestoneColor = <?php echo json_encode($_milestoneColor) ?>;
    var is_new_design = <?php echo json_encode( !empty( $employee_info['Color']['is_new_design']) ? 1 : 0 ) ?>;
    var list_org_project_status = <?php echo json_encode(array_values($list_org_project_status)) ?>;
    var _listIdModifyByPm = $.map(listIdModifyByPm, function(value, index) {
       return value;
    });
    var listEditStatus = <?php echo json_encode($listEditStatus) ?>;
    var listEditStatus = $.map(listEditStatus, function(value, index) {
       return value;
    });
    var myId = <?php echo json_encode($employee_info['Employee']['id'])?>;
    var roleLogin = <?php echo json_encode($roleLogin);?>;
    var canAddComment = <?php echo json_encode($canAddComment);?>;
    var canDiaryModify = <?php echo json_encode($canDiaryModify);?>;
    var canDiaryOtherField = <?php echo json_encode($canDiaryOtherField);?>;
    var listEmployeeManagerOfT = <?php echo json_encode($listEmployeeManagerOfT) ?>;
    var fullName = <?php echo json_encode($fullName) ?>;
    var avatarEmploys = <?php echo json_encode($avatarEmploys) ?>;
    var employeeAssignedAvt  = <?php echo json_encode($employeeAssignedAvt ) ?>;
    var myAvatar = <?php echo json_encode($myAvatar) ?>;
    var wdTable = $('.wd-table');
    var heightTable = $(window).height() - wdTable.offset().top - 80;
	var i18n = <?php echo json_encode($i18n); ?>;
	var text_by = <?php echo json_encode(  __('by', true) ) ?>;
	var text_modified = <?php echo json_encode( __('Modified', true) ) ?>;
	var isTablet = <?php echo json_encode($isTablet) ?>;
    var isMobile = <?php echo json_encode($isMobile) ?>;
    var dataView = <?php echo json_encode($dataView) ?>;
    var curent_time = <?php echo json_encode(time()) ?>;
    var list_project_has_nct = <?php echo json_encode($list_project_has_nct) ?>;
	var linkFile = <?php echo json_encode($file_upload) ?>;
	var check_consumed = <?php echo json_encode($check_consumed); ?>;
	var listProjectbyPM = <?php echo json_encode($listProjectbyPM); ?>;
	var can_move = <?php echo json_encode($can_move); ?>;
    var myRole = <?php echo json_encode($myRole); ?>;
    var emp_id_login = <?php echo json_encode($emp_id_login); ?>;
    //heightTable = (heightTable < 500) ? 500 : heightTable;
	var vv_Data = [];
	Dropzone.autoDiscover = false;
    wdTable.css({
        height: heightTable,
    });
	wdTable.find('.slick-viewport').css({
		height:  $('.wd-table').height() - $('.slick-pane-header:first').height()
	});
    $(window).resize(function(){
        heightTable = $(window).height() - wdTable.offset().top - 80;
        wdTable.css({
            height: heightTable,
        });
		wdTable.find('.slick-viewport').css({
			height:  $('.wd-table').height() - $('.slick-pane-header:first').height()
		});
    });
	 $('.close, .cancel').on( 'click', function (e) {
        $("#template_upload").removeClass('show');
    });
	$('.fancy.image').fancybox({
       type: 'image'
    });
    function expandScreen(){
        $('.wd-panel').addClass('treeExpand');
		$('#layout').addClass('wd-ontop');
        $('#collapse-btn').show();
        $('#expand-btn').hide();
        $('#filter_task_title').parent().show();
        // $('#list-all-assigned').show();
        $(window).resize();
    }
	
    function collapseScreen(){
        $('#collapse-btn').hide();
        $('#expand-btn').show();
		$('.wd-panel').removeClass('treeExpand');
		$('#layout').removeClass('wd-ontop');
		$('.kanban-box').hide();
		$('#project_container').show();
		$('.btn-kanban').show();
        $('#filter_task_title').parent().hide();
        $('#list-all-assigned').hide();
        $(window).resize();
    }
	var editTask_date_validated = 1;
    function editTaskValidated(_this){
        var st_date = $('#editTaskStartDay').val();
        var en_date = $('#editTaskEndDay').val();
        st_date = st_date.split('-');
        en_date = en_date.split('-');
        st_date = new Date(st_date[2],st_date[1],st_date[0]);
        en_date = new Date(en_date[2],en_date[1],en_date[0]);
        if( st_date > en_date){
            $(_this).css('border-color','red');
            editTask_date_validated = 0;
        }else{
            $('#editTaskStartDay').css('border-color','');
            $('#editTaskEndDay').css('border-color','');
            editTask_date_validated = 1;
        }
    };
	function set_assigned($elm, datas){
		$elm.find('.wd-combobox >.circle-name').remove();
		$elm.find('.wd-combobox >p').show();
		$elm.find('.wd-data input[type="checkbox"]').prop('checked', false).trigger('change');		
		$elm.find('.wd-input-search').val('').trigger('change');
		$.each( datas, function( ind, data){
			if( data.is_profit_center == 1) {
				$elm.find('input[value="'+data.reference_id+'-1"]').closest('.wd-data').click();
			}else{
				$elm.find('input[value="'+data.reference_id+'-0"]').closest('.wd-data').click();
			}
		});
	}
	$('.kanban-box').hide();
	$('.btn-kanban').on( 'click', function (e) {
		// $('.wd-panel').addClass('loading');
        expandScreen();
		$(this).hide();
		$('.kanban-box').show();
		$('#list-all-assigned').show();
		$('#project_container').hide();
		init_kanban(vv_Data);
		// $('.wd-panel').removeClass('loading');
    });
	
	resetFilter = function(){
		$('#filter-view').val('all').trigger('change');
		$('.filter-by-type').find('.focus').removeClass('focus');
		which = '';
		$.ajax({
			url: '/projects/saveFilterSortVisionTask',
			dataType: 'json',
			type: 'POST',
			data: {
				which: which
			},
			success: function(data){
			}
		});
		$.ajax({
			url : '/employees/history_filter',
			type: 'POST',
			data: {
				data: {
					path: HistoryFilter.here,						
				}
			},
			success : function(respons){
				var _data =  $.parseJSON(respons);
				$.each(_data, function( _index, _val){
					if( _index.indexOf('Resize') == -1){
						 _data[_index]='';
					}	
				});
				HistoryFilter.stask = _data;
				HistoryFilter.send();
				setTimeout(function(){
					location.reload();
				}, 500);
			},
			complete: function(){
				$(window).resize();
			}
		});
		
	}
	function openFileAttach(){
		_id = $(this).data("id");
		$.ajax({
			url : "/kanban/ajax/"+ _id,
			type: "GET",
			cache: false,
			success: function (html) {
				var dump = $('<div />').append(html);
				if( dump.children('.error').length == 1 ){
					//do nothing
				} else if ( dump.children('#attachment-type').val() ) {
					$('#contentDialog').html(html);
					$('#dialogDetailValue').addClass('popup-upload');
					showMe();
				}
			}
		});

	}
	function setLimitedDate(ele_start, ele_end){
		var limit_sdate = $('#popupnct-assign-table tbody tr:first').find('.popupnct-date').attr('id');
		if(limit_sdate){
			 limit_start_date = (limit_sdate.split("_")[1]).split("-");
			 limit_s_date = new Date(limit_start_date[1] +'-'+limit_start_date[0] +'-'+limit_start_date[2]);
			 $(ele_start).datepicker('option','maxDate',limit_s_date);
		}
		var limit_edate = $('#popupnct-assign-table tbody tr:last').find('.popupnct-date').attr('id');
		if(limit_edate){
			 limit_end_date = (limit_edate.split("_")[0]).split("-");
			 limit_e_date = new Date(limit_end_date[2] +'-'+limit_end_date[1] +'-'+limit_end_date[3]);
			  $(ele_end).datepicker('option','minDate',limit_e_date);
		}
	}
	$("#ok_attach").on('click',function(){
		id = $('input[name="data[Upload][id]"]').data('id');
		url = $.trim($('input[name="data[Upload][url]"]').val());
		var form = $("#UploadTasksVisionNewForm");
		if(url){
			form.submit();
		}
	});
	function deleteAttachment(){
		var me = $(this), taskId = me.prop('alt');
		var itemPic = $(this).closest('li');
		var itemList = $('#content_comment .append-comment ul');
		var attachment_cont = itemPic.closest('ul');
		if( confirm('<?php __('Delete?') ?>') ){
			//call ajax
			$.ajax({
				url: '<?php echo $this->Html->url('/project_tasks/delete_attachment/') ?>' + taskId,
				complete: function(){
					itemPic.remove();
					if( attachment_cont.is(':empty')){
						var _new_data = {
							'Attachment' : 0,
							'attach_read_status': 1
						};
						SlickGridCustom.update_after_edit(taskId, _new_data);
						
						// Update count file upload
						var _tag = $('.wd-task-actions a.task-attachment[data-task-id="' + taskId + '"]');
						_tag.addClass('read');
						_tag.find('span').html( parseInt(_tag.find('span').html())-1 );						
					}
				}
			})
		}
	}
	function deleteAttachmentFile(){
		var AttachmentId = $(this).closest('a').data('id');
		var taskId = $(this).closest('a').data('taskid');
		var itemPic = $(this).closest('li');
		var attachment_cont = itemPic.closest('ul');
		$.ajax({
			url: '<?php echo $this->Html->url('/kanban/delete_attachment/') ?>',
			type: "POST",
			data: {
				attachId: AttachmentId,
				taskId: taskId
			},
			dataType: 'json',
			cache: false,
			success: function (data) {
				itemPic.remove();
				
				// Update count file upload
				var _tag = $('.wd-task-actions a.task-attachment[data-task-id="' + taskId + '"]');
				_tag.addClass('read');
				_tag.find('span').html( parseInt(_tag.find('span').html())-1 );
					
				if( attachment_cont.is(':empty')){
					var _new_data = {
						'Attachment' : 0,
						'attach_read_status': 1
					};
					SlickGridCustom.update_after_edit(taskId, _new_data);
				}
				if(data_tasks && data_tasks[taskId]){
					data_tasks[taskId]['attachment'] -= 1; 
				}
			}
		})
	}
	function openAttachment(){
		var me = $(this), taskId = me.attr('alt');
		window.open('<?php echo $this->Html->url('/project_tasks/view_attachment/') ?>' + taskId, '_blank');
	}
	function openAttachmentDialog(){
		var me = $(this),
		id = me.data('task-id'),
		project_id = me.data('project-id');
		var _html = task_title = '';
		var latest_update = '';
		$('#UploadId').val(id);
		$('#UploadProjectId').val(project_id);
		var popup = $('#template_attach');
		var _has_file = 0;
		$.ajax({
			url: '/kanban/getTaskAttachment/'+ id,
			type: 'POST',
			data: {
				id: id,
			},
			dataType: 'json',
			success: function(data) {
				$("#template_upload").addClass('show');
				$('.light-popup').addClass('show');
				if((listEmployeeManagerOfT[id] === undefined) && (canDiaryModify == 0 || ((canDiaryModify == 1) && (canDiaryOtherField == 0))) && roleLogin != 'admin'){
					$('.wd-popup').addClass('hidden');
					$('.actions').addClass('hidden');
				}
				$("#template_upload").find('.project-name').empty().append(data['task_title']);
				_html += '<ul>';
				if (data['attachments']) {
					$.each(data['attachments'], function(ind, _data) {
						if( _data) _has_file = 1;
						if( _data['ProjectTaskAttachment']['is_old_attachment'] == 1){
							var att = _data['ProjectTaskAttachment']['attachment'].split(':');
							_html += '<li class="old-attachment">';
							if( att[0] == 'file' ) {
								if(linkFile[_data['ProjectTaskAttachment']['id']]) _link = linkFile[_data['ProjectTaskAttachment']['id']];
                                else _link = '/kanban/attachment/'+ _data['ProjectTaskAttachment']['id'] +'/?sid='+ api_key;
								
								_html += '<i class="icon-paper-clip"></i><span href = "'+ _link +'" class="file-name fancy image"  href="javascript:void(0);" alt="' + id + '">'+ _data['ProjectTaskAttachment']['attachment'].replace('file:','') +'</span>';
							} else {
								_html += '<i class="icon-link"></i><a class="file-name"  href="' + _data['ProjectTaskAttachment']['attachment'].replace('url:','') + '" target="_blank" alt="' + id + '">'+ _data['ProjectTaskAttachment']['attachment'].replace('url:','') +'</a>';
							}
							_html += '<a href="javascript:void(0);" data-id = "'+ id +'" alt="' + id + '"><img src="/img/new-icon/delete-attachment.png" alt="'+ id +'" onclick="deleteAttachmentFile.call(this)"></a>';
							_html += '</li>';
						}else{
							var _delete_icon = '<img src="/img/new-icon/delete-attachment.png" alt="'+ _data['ProjectTaskAttachment']['id'] +'" onclick="deleteAttachmentFile.call(this)">';
							if(_data['ProjectTaskAttachment']['is_file'] == 1){								
								if((/\.(gif|jpg|jpeg|tiff|png)$/i).test(_data['ProjectTaskAttachment']['attachment'])){ 
									if(linkFile[_data['ProjectTaskAttachment']['id']]) _link = linkFile[_data['ProjectTaskAttachment']['id']];
									else _link = '/kanban/attachment/'+ _data['ProjectTaskAttachment']['id'] +'/?sid='+ api_key;
									_html += '<li><i class="icon-paper-clip"></i><span href = "'+ _link +'" class="file-name fancy image" data-id = "'+ _data['ProjectTaskAttachment']['id'] +'">'+ _data['ProjectTaskAttachment']['attachment'] +'</span><a  data-id = "'+ _data['ProjectTaskAttachment']['id'] +'" data-taskid="' + id + '">' + ( roleLogin != 'conslt' ? _delete_icon : '' ) +'</a></li>';
								}else{
									_link = '/kanban/attachment/'+ _data['ProjectTaskAttachment']['id'] +'/?download=1&?sid='+ api_key;
									_html += '<li><i class="icon-paper-clip"></i><a class="file-name"  href="'+ _link +'">'+ _data['ProjectTaskAttachment']['attachment'] +'</a><a  data-id = "'+ _data['ProjectTaskAttachment']['id'] +'" data-taskid="' + id + '">' + ( roleLogin != 'conslt' ? _delete_icon : '' ) +'</a></li>';
								}
							}else{
								_html += '<li><i class="icon-link"></i><a class="file-name" target="_blank" href="'+ _data['ProjectTaskAttachment']['attachment'] +'">'+ _data['ProjectTaskAttachment']['attachment'] +'</a><a  data-id = "'+ _data['ProjectTaskAttachment']['id'] +'" data-taskid="' + id + '">' + ( roleLogin != 'conslt' ? _delete_icon : '' ) +'</a></li>';
							}
						}
					});
				}
				_html += '</ul>';
				$('#content_comment .append-comment').html(_html);
				// update read status
				var _new_data = {
					'Attachment' : _has_file,
					'attach_read_status': 1
				};
				SlickGridCustom.update_after_edit(id, _new_data);				
			}
		});
	}
    $(function() {
		var myDropzone = new Dropzone("#upload-popup", {
			// acceptedFiles: ".jpg,.jpeg,.bmp,.gif,.png,.txt,.doc,.xls,.pdf,.docx,.xlsx,.ppt,.pps,.pptx,.csv,.xlsm,.msg",
			acceptedFiles: "",
		});
        myDropzone.on("queuecomplete", function(file) {
            id = $('#UploadId').val();
			var _has_file = 0;
            $.ajax({
                url: '/kanban/getTaskAttachment/'+ id,
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    _html = '<ul>';
					if((listEmployeeManagerOfT[id] === undefined) && (canDiaryModify == 0 || ((canDiaryModify == 1) && (canDiaryOtherField == 0))) && roleLogin != 'admin'){
						$('.wd-popup').addClass('hidden');
						$('.actions').addClass('hidden');
					}
                    if (data['attachments']){ 
                        $.each(data['attachments'], function(ind, _data) {
							if( _data) _has_file = 1;
							if( _data['ProjectTaskAttachment']['is_old_attachment'] == 1){
								var att = _data['ProjectTaskAttachment']['attachment'].split(':');
								_html += '<li class="old-attachment">';
								if( att[0] == 'file' ) {
									_html += '<i class="icon-paper-clip"></i><a class="file-name"  href="javascript:void(0);" onclick="openAttachment.call(this)" alt="' + id + '">'+ _data['ProjectTaskAttachment']['attachment'].replace('file:','') +'</a>';
								} else {
									_html += '<i class="icon-link"></i><a class="file-name"  href="' + _data['ProjectTaskAttachment']['attachment'].replace('url:','') + '" target="_blank" alt="' + id + '">'+ _data['ProjectTaskAttachment']['attachment'].replace('url:','') +'</a>';
								}
								_html += '<a href="javascript:void(0);" data-id = "'+ id +'" alt="' + id + '"><img src="/img/new-icon/delete-attachment.png" alt="'+ id +'" onclick="deleteAttachmentFile.call(this)"></a>';
								_html += '</li>';
							}else{
								if(_data['ProjectTaskAttachment']['is_file'] == 1){
									if((/\.(gif|jpg|jpeg|tiff|png)$/i).test(_data['ProjectTaskAttachment']['attachment'])){
										if(linkFile[_data['ProjectTaskAttachment']['id']]) _link = linkFile[_data['ProjectTaskAttachment']['id']];
										else _link = '/kanban/attachment/'+ _data['ProjectTaskAttachment']['id'] +'/?sid='+ api_key;
										_html += '<li><i class="icon-paper-clip"></i><span href = "'+ _link +'" class="file-name fancy image" data-id = "'+ _data['ProjectTaskAttachment']['id'] +'">'+ _data['ProjectTaskAttachment']['attachment'] +'</span><a  data-id = "'+ _data['ProjectTaskAttachment']['id'] +'" data-taskid="' + id + '"><img src="/img/new-icon/delete-attachment.png" alt="'+ _data['ProjectTaskAttachment']['id'] +'" onclick="deleteAttachmentFile.call(this)"></a></li>';
									}else{
										_link = '/kanban/attachment/'+ _data['ProjectTaskAttachment']['id'] +'/?download=1&sid='+ api_key;
										_html += '<li><i class="icon-paper-clip"></i><a class="file-name"  href="'+ _link +'">'+ _data['ProjectTaskAttachment']['attachment'] +'</a><a  data-id = "'+ _data['ProjectTaskAttachment']['id'] +'" data-taskid="' + id + '"><img src="/img/new-icon/delete-attachment.png" alt="'+ _data['ProjectTaskAttachment']['id'] +'" onclick="deleteAttachmentFile.call(this)"></a></li>';
									}
								}else{
									_html += '<li><i class="icon-link"></i><a class="file-name" target="_blank" href="'+ _data['ProjectTaskAttachment']['attachment'] +'">'+ _data['ProjectTaskAttachment']['attachment'] +'</a><a  data-id = "'+ _data['ProjectTaskAttachment']['id'] +'" data-taskid="' + id + '"><img src="/img/new-icon/delete-attachment.png" alt="'+ _data['ProjectTaskAttachment']['id'] +'" onclick="deleteAttachmentFile.call(this)"></a></li>';
								}
							}
                        });
                    }
                    _html += '</ul>';
                    $('#content_comment .append-comment').find('ul').empty();  
                    $('#content_comment .append-comment').html(_html);
					
					// update count file upload
					var _tag = $('.wd-task-actions a.task-attachment[data-task-id="' + id + '"]');
					_tag.addClass('read');
					_tag.find('span').html( data.attachment_count );
					
					// Update data kanban
					if(data_tasks && data_tasks[id]){
						data_tasks[id]['attachment_count'] = data.attachment_count; 
					}
					var _new_data = {
						'Attachment' : _has_file,
						'attach_read_status': 1
					};
					SlickGridCustom.update_after_edit(id, _new_data);
					
                }
            });
        });
        myDropzone.on("success", function(file) {
            myDropzone.removeFile(file);
        });
		$('#UploadTasksVisionNewForm').on('submit', function(e){
            $('#UploadTasksVisionNewForm').parent('.wd-popup').addClass('loading');
            // return;
            if(myDropzone.files.length){
                e.preventDefault();
                myDropzone.processQueue();
            }
        });
        myDropzone.on('sending', function(file, xhr, formData) {
            // Append all form inputs to the formData Dropzone will POST
            var data = $('#UploadTasksVisionNewForm').serializeArray();
            $.each(data, function(key, el) {
                formData.append(el.name, el.value);
            });
        });
    });
	function add_new_task_item(data) {
		var _task = [] ;
		_task['status'] = parseInt( data['message']['ProjectTask']['task_status_id'] );
		_task['id'] = parseInt(data['message']['ProjectTask']['id']);
		_task['text'] = data['message']['ProjectTask']['task_title'];
		if( _task['id'] in data_tasks){
			$('#kanban').jqxKanban('removeItem', data['message']['ProjectTask']['id']);
			$('#widget-task').find('li.task-' + _task['id']).remove();
		}
		data_tasks[_task['id']] = data['message']['ProjectTask'];
		$('#kanban').jqxKanban('addItem', _task);
		$('#kanban_' + _task['id'] ).addClass('jqx-kanban-item').data('kanbanItemId', _task['id'] );
	}
	function newTaskDropzone_success_calback(file, dropzone_elm) {
        cancel_popup(dropzone_elm);
        var _xhr = '';
        var respon = '';
        if ('xhr' in file)
            _xhr = file.xhr;
        if ('responseText' in _xhr)
            respon = _xhr.responseText;
        data = JSON.parse(respon);
        add_new_task_item(data);
    }
	function new_task_dropzone(){
		// return;
		var newTaskDropzone = '';
		var dropzone_elm = "#wd-upload-popup-index";
		
		$(function() {
			var _form = $(dropzone_elm).closest('form');
			newTaskDropzone = new Dropzone(dropzone_elm, {
				// acceptedFiles: ".jpg,.jpeg,.bmp,.gif,.png,.txt,.doc,.xls,.pdf,.docx,.xlsx,.ppt,.pps,.pptx,.csv,.xlsm,.msg",
				acceptedFiles: "",
				imageSrc: "/img/new-icon/drop-icon.png",
				dictDefaultMessage: "<?php __('Drag & Drop your document or browse your folders');?>",
				autoProcessQueue: false,
				addRemoveLinks: true,  
				maxFiles: 1,
			});
			newTaskDropzone.on("queuecomplete", function(file) {
				$(dropzone_elm).closest('.loading-mark').removeClass('loading');
			});
			
			newTaskDropzone.on("success", function(file) {
				newTaskDropzone.removeFile(file);
                if (typeof newTaskDropzone_success_calback == 'function')
                    newTaskDropzone_success_calback(file, dropzone_elm);
                cancel_popup(dropzone_elm);
			});
			
			_form.on('submit', function(e){
				$(dropzone_elm).closest('.loading-mark').addClass('loading');
				if(newTaskDropzone.files.length){
					e.preventDefault();
					newTaskDropzone.processQueue();
				}else {
                    e.preventDefault();
                    $.ajax({
                        type: "POST",
                        url: _form.prop('action'),
                        data: _form.serialize(),
                        dataType: 'json',
                        success: function (data) {
                            if (data.success) {
                                add_new_task_item(data);
                                cancel_popup(dropzone_elm);
                            } else {
                                show_form_alert('#' + _form.prop('id'), data.message);
                                $(dropzone_elm).closest('.loading-mark').removeClass('loading');
                            }

                        }
                    });
                }
			});
			newTaskDropzone.on('sending', function(file, xhr, formData) {
				// Append all form inputs to the formData Dropzone will POST
				var data = $('#ProjectTaskTasksVisionNewForm').serializeArray();
				$.each(data, function(key, el) {
					formData.append(el.name, el.value);
				});
			});
		});
	}
	new_task_dropzone();
    (function($){
        $(function(){
            var $this = SlickGridCustom;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  true;
            $this.isExporting =  false;			
            var actionTemplate =  $('#action-template').html();
            var actionTemplateEdit =  $('#action-template-edit').html();
            $.extend(Slick.Formatters,{
                linkFormatter : function(row, cell, value, columnDef, dataContext){
                    var idTask = dataContext.id ? dataContext.id : 0,
                        idPr = dataContext.idPr ? dataContext.idPr : 0;
                        task = dataContext.Task ? dataContext.Task : '';
                    return "<a href='/project_tasks/index/" + idPr + "/?id=" + idTask +"' target=_blank>" + task + "</a>";
                },
                text : function(row, cell, value, columnDef, dataContext){
                    // $('.slick-cell').css({'height':'42px', 'overflow-y': 'hidden', 'z-index' : 1});
                    if(avatarEmploys[dataContext.id] !== undefined){
                        return '<img style="width: 30px; height: 30px; float: left; line-height: 30px; margin-right: 5px; border-radius: 50%; margin-top: 3px;" src="'+avatarEmploys[dataContext.id]+'"><p>' + value + '</p>';
                    } else {
                        return '<p>' + value + '</p>';
                    }
                },
				assign : function(row, cell, value, columnDef, dataContext){
					var _html = '';
					var field = columnDef.field;
					// console.log( field, value);
					if( $this.isExporting){
						$.each( value, function( ind, employee){
							_employee = employee.split('_');
							if( ind > 0) _html +=  ', ';
							if( $this.selectMaps[field][employee]) _html +=  $this.selectMaps[field][employee];
						});
						return _html;
					}
					$.each( value, function( ind, employee){
						if( $this.selectMaps[field][employee]){
							_employee = employee.split('_');
							if( _employee[1] == 0){ // is_employee
								var empID = _employee[0];
								_html += '<a class="circle-name" title="'+ $this.selectMaps[field][employee] +'"><span data-id="'+ empID +'"><img alt="'+ $this.selectMaps[field][employee] +'" src="' + employeeAvatar_link.replace('%ID%', empID) + '"/></span></a>';
							}else{ // is Profit Center
								// _html += empID;
								_html +=  '<div class="icon-pcname" title="' + $this.selectMaps[field][employee] + '">' + '<span><i class="icon-people"></i></span></div>';
							}
						}
					});
					return _html;
                },
				wdSelectTaskStatus: function(row, cell, value, columnDef, dataContext){
					if( $this.isExporting){
						var statusName = '';
						$.each(list_org_project_status, function(ind, status){
							if( status.id == value) statusName = status.name;
						});
						return statusName;
					}
					var _html ='';
					var	color = 'status_blue';
					var today = dataContext['today'];
					var task_id = dataContext['id'];
					var enddate = dataContext['enddate'];
					var canUpdateStatus = dataContext.DataSet.canModified;
					var titleDisable = i18n['Permission denied'];
					var disableClass = '';
					if(canUpdateStatus == 0){
						task_id = 0;
						disableClass =  'disable';
					}
					_html += '<div class="task-status-select clearfix">';
					_html += '<div class="status_texts">';
					$.each(list_org_project_status, function(ind, status){
						color = 'status_blue';
						if( status['status'] == 'CL') color = 'status_green';
						else { if( today > enddate ) color = 'status_red';} 
						_html += '<span class="status_text ' + (value == status.id ? 'active' : '' ) + ' ' + color + '" data-value="' + status.id + '" data-text="' + status.name + '">' + status.name + '</span>';
					});
					_html += ' </div>';
					_html += ' <div class="status_dots '+ disableClass +'" title="'+ titleDisable +'">';
					$.each(list_org_project_status, function(ind, status){
						color = 'status_blue';
						if( status['status'] == 'CL') color = 'status_green';
						else if( today > enddate ) color = 'status_red';
						var status_title = task_id == 0 ? titleDisable : status.name;
						_html += '<a href="javascript:void(0);" class="status_dot ' + (value == status.id ? 'active' : '') + ' ' + color + '" title="' + status_title + '" data-value="' + status.id + '" data-taskid="' + task_id + '"></a>';
					});
					_html += '</div>';
					_html += '</div>';
					return _html;
				},
				contextAttachment: function(row, cell, value, columnDef, dataContext){
					var has_att = value;
					var has_read = dataContext.attach_read_status;
					var src = '/img/new-icon/drop-icon.png';
					if( has_att) src = '/img/new-icon/drop-icon-red.png';
					if( has_att && has_read) src = '/img/new-icon/drop-icon-blue.png';
					context = '<a class="action-acttachment" data-task-id = "'+ dataContext.id +'" data-project-id = "'+ dataContext.idPr +'" onclick="openAttachmentDialog.call(this)"><img src="'  + src + '"></a>';
					
                    return context;
                },
                taskImage : function(row, cell, value, columnDef, dataContext){
                    if(columnDef.field != 'Workload'){
                        if(columnDef.field == 'Initial start' || columnDef.field == 'Initial end') {
                            return '<div style="text-align: center;"><span>' + value + '</span></div>';
                        }
                        var today = new Date();
                        var dd = today.getDate();
                        var mm = today.getMonth()+1; //January is 0!
                        var yyyy = today.getFullYear();
                        dd = dd < 10 ? '0' + dd : dd;
                        mm = mm < 10 ? '0' + mm : mm;

                        curDate = new Date(yyyy + '-' + mm + '-' + dd).getTime();
                        var sDate = value.split('-');
                        sDate = new Date(sDate[2] + '-' + sDate[1] + '-' + sDate[0]).getTime();
                        if(columnDef.field == 'Start'){
                            if(value){
                                var _classDate = 'task_blue';
                                var _title = '';
                                if(dataContext.Consume == 0 && sDate < curDate && dataContext.Workload > 0 && dataContext.Status != clStatus){
                                    _classDate = 'task_red';
                                    _title = '<?php echo __("No consumed and the current date > start date") ?>';
                                }
                                return '<div style="text-align: center;"><span class="' + _classDate + '" title="' + _title + '">' + value + '</span></div>';
                            } else {
                                return '<div style="text-align: center;"><span>' + value + '</span></div>';
                            }
                        } else if(columnDef.field == 'End'){
                            if(value){
                                var _classDate = 'task_blue';
                                var _title = '';
                                if(dataContext.Status  && dataContext.Status != clStatus && sDate < curDate){
                                    _classDate = 'task_red';
                                    _title = '<?php echo __("Task not closed and the current date > end date") ?>';
                                }
                                return '<div style="text-align: center;"><span class="' + _classDate + '" title="' + _title + '">' + value + '</span></div>';
                            } else {
                                return '<div style="text-align: center;"><span>' + value + '</span></div>';
                            }
                        }
                    } else {
                        var _classDate = 'task_blue';
                        var _title = '';
                        if(parseFloat(value) < parseFloat(dataContext.Consume)){
                            _classDate = 'task_red';
                            _title = '<?php echo __("Consumed > Workload") ?>';
                        }
                        return '<div style="margin-left: 30px;"><span class="' + _classDate + '" title="' + _title + '">' + parseFloat(value).toFixed(2) + '</span></div>';
                    }
                },
                valueFormat: function(row, cell, value, columnDef, dataContext){
                    return '<div style="text-align: right;"><span>' + parseFloat(value).toFixed(2) + '</span></div>';
                },
                milestoneFormatter: function(row, cell, value, columnDef, dataContext){
                    if( value && listMilestoneName[value]){
						var milestone_id = dataContext.milestone_id;
                        return '<div><i class="' + _milestoneColor[milestone_id] + '" style="width: 25px; float: left;">&nbsp</i><span style="margin-left: -25px">' + listMilestoneName[value] + '</span></div>';
                    } else {
                        return '';
                    }
                }
            });
            $.extend(Slick.Editors,{
                text: function(args){
                    var self = this;
                    $.extend(this, new BaseSlickEditor(args));
                    var check = false;
                    if(listEmployeeManagerOfT[args.item.id] !== undefined) check = true;
                    var _assign = args.item.Assigned.split(', ');
                    if(_assign.length > 0){
                        for (var i = 0; i < _assign.length; i++) {
                            if(_assign[i] == fullName) check = true;
                        }
                    }
                    if(check){
                        $(args.container).css({'height': '70px', 'z-index': 99999, 'overflow-y' : 'auto'});
                        if(avatarEmploys[args.item.id] !== undefined){
                            this.input = $("<input style='height:13px' type=text class='editor-text' /><img style='width: 20px; height: 20px; float: left; margin-right: 5px; border-radius: 50%;' src='"+avatarEmploys[args.item.id]+"'><p>" + args.item.Text +"</p>")
                            .appendTo(args.container);
                        } else {
                            this.input = $("<input style='height:13px' type=text class='editor-text' /><p>" + args.item.Text +"</p>")
                            .appendTo(args.container);
                        }
                        this.focus();
                        var $input = this.input[0];
                        var defaultValue = args.item.Text;
                        this.setValue = function (val) {
                            $($input).val('');
                        };
                        this.isValueChanged = function () {
                            if( $($input).val() != "" && $($input).val() != defaultValue ){
                                avatarEmploys[args.item.id] = myAvatar;
                            }
                            return $($input).val() != "" && $($input).val() != defaultValue;
                        };
                    } else {
                        if(avatarEmploys[args.item.id] !== undefined){
                            this.input = $("<img style='width: 25px; height: 25px; float: left; margin-right: 5px; border-radius: 50%;' src='"+avatarEmploys[args.item.id]+"'><p>" + args.item.Text +"</p>")
                            .appendTo(args.container);
                        } else {
                            this.input = $("<p>" + args.item.Text +"</p>")
                            .appendTo(args.container);
                        }
                    }
                }
            });
            $this.onBeforeEdit = function(args){
                if(args.column.id == 'Status'){
                    if(roleLogin == 'pm'){
                        if((args.item.idPr && $.inArray(args.item.idPr, _listIdModifyByPm) != -1) || $.inArray( myId, listEditStatus) != -1 ){
                            // do nothing
                        } else {
                            return false;
                        }
                    }
                    else if($.inArray( myId, listEditStatus) != -1 ){
                        // do nothing
                    }
               }
               return true;
            }
            
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                Status : {defaulValue : ''},
                Text : {defaulValue : ''},
            };
			var columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
			var options = {
				headerRowHeight: 40,
				rowHeight: 40,
				enableAddRow: false,
				enableCellNavigation: true,
			};
            $this.url =  '<?php echo $html->url(array('action' => 'update_vision_task')); ?>';
			var dataView_init = dataView;
			if( filter_color){
				dataView_init = [];
				$.each(dataView, function(key, val){
					if(val['task_late_status'] == filter_color) dataView_init.push(val);
				});

			}
            dataGrid = $this.init($('#project_container'),dataView_init,columns, options);
			resizeHandler();
			dataGrid.onColumnsResized.subscribe(function(e, args){
				resizeHandler();
			});
            $('.filter-by-type').on('click', '.filter-status', function(e){
				e.preventDefault();
				var which = $(this).data('type');
                if( $(this).hasClass('focus') ){
                    $('.filter-status').removeClass('focus');
                    which = '';
                } else {
                    $('.filter-status').removeClass('focus');
                    $(this).addClass('focus');
                }
                $.ajax({
                    url: '/projects/saveFilterSortVisionTask',
                    dataType: 'json',
                    type: 'POST',
                    data: {
                        which: which
                    },
                    success: function(data){
                    }
                });
				wd_title_filter();
				init_kanban(vv_Data);
			});
            $('#filter-view').on('change',  function(e){
				e.preventDefault();
				wd_title_filter();
				init_kanban(vv_Data);
			});
            
            var exporter = new Slick.DataExporter('/projects/export');
            dataGrid.registerPlugin(exporter);
            $('#export-table').click(function(){
				$this.isExporting = true;
                exporter.submit();
				$this.isExporting = false;
                return false;
            });
			$('body').on('click', '.task-status-select .status_dot', function(){
				var _this = $(this);
				_task_id = _this.data('taskid');
				if(_task_id == 0) return;
				if( _this.hasClass('active') || _this.hasClass('loading') || _this.closest('.status_dots').hasClass('loading') ) return;
				_this.closest('.status_dots').addClass('loading');
				_this.addClass('loading').siblings().removeClass('loading');
				_status_id = _this.data('value');
				$.ajax({
					type: "POST",
					url: '/kanban/update_task_status',
					data: {
							id: _task_id,
							status: _status_id
					},
					dataType: 'json',
					success: function(respon){
						_this.closest('.status_dots').removeClass('loading');
						var _texts = _this.closest('.task-status-select');
						_texts.find('.status_text').removeClass('active');
						_texts.find('.status_text[data-value="' + _status_id + '"]').addClass('active');
						_this.removeClass('loading');
						if( respon.result == true){
							_this.addClass('active').siblings().removeClass('active');
							_this.addClass('puffIn');
							var wait = window.setTimeout( function(){
								_this.removeClass('puffIn');
							}, 1500 );
							
							 // update to grid
							var row = dataGrid.getData().getRowById(_task_id);
							var item = dataGrid.getData().getItem(row);
							var dataView = dataGrid.getDataView();
							dataView.beginUpdate();
							item['Status'] = _status_id.toString();
							dataView.updateItem(_task_id, item);
							dataView.endUpdate();
							dataGrid.resizeCanvas();
							// update filter
							getDataFilterKanban(dataGrid.getData().getItems());
							
						}else{
							_this.removeClass('active').siblings().removeClass('active');
						}
					},
					error: function(){
						_this.closest('.status_dots').removeClass('loading');
						_this.removeClass('loading');
						_this.removeClass('active').siblings().removeClass('active');
					}
				});
			});
        });
    })(jQuery);
	function filterTask(){
		wd_title_filter();
		init_kanban(vv_Data);
	}
	function wd_title_filter(){
		var _data_filter = getDataFilterHeader();
		getDataFilterKanban(_data_filter);
		drawSlickGrid(_data_filter);
	}
	function getDataFilterHeader(){
		time_filter = $('#filter-view').val();
		var dataFilter = dataView;	
		var kanbanFilter = task_kanban;
		if( time_filter && time_filter != 'all'){
			dataFilter = [];				
			$.each(dataView, function(key, val){
				if(val['ed_format']){
					endate = val['ed_format'].split('-');
					current = val['cu_format'].split('-');
					if(time_filter == 'today'&& endate[0] == current[0] && endate[1] == current[1] && endate[2] == current[2]){
						dataFilter.push(val);
					}else if(time_filter == 'week' && endate[3] == current[3] && endate[2] == current[2]){
						dataFilter.push(val);
					}else if(time_filter == 'month' && endate[1] == current[1] && endate[2] == current[2]){
						dataFilter.push(val);
					}else if(time_filter == 'year' && endate[2] == current[2]){
						dataFilter.push(val);
					}
				}
			});
			
		}
		var late_status_filter = $('.filter-status.focus:first').data('type');
		if( late_status_filter && dataFilter.length){
			var new_data = [];
			$.each(dataFilter, function(key, val){
				if( val['task_late_status'] == late_status_filter ){
					new_data.push(val);
				}
			});
			dataFilter = new_data;
		}
		return dataFilter;
	}
	function checkTitle(task, title_filter){
		if( !title_filter) return 1;
		title_filter = title_filter.toLowerCase();
		regE = title_filter.toLowerCase().replace('?', '.').replace('*', '.+');
		var regE = new RegExp(regE);
		var tasktitle = task.Task.toLowerCase();
		// console.log( tasktitle, regE, tasktitle.match(regE));
		if( tasktitle.match(regE)) return 1;
		return 0;
		
	}
	function checkAssign(task, employee_filter){
		if( employee_filter == '') return 1;
		var result = 0;
		console.log( task, employee_filter);
		if( task.Assigned){
			$.each( task.Assigned, function( i, em){
				var emp = em.replace('_', '-');
				if( ($.inArray( emp, employee_filter) != -1) ){
					result = 1;
					return false;
				}
			});
		}
		return result;
	}
	function getDataFilterKanban(dataFilter){
		kanbanFilter = [];	
		var title_filter = $('#filter_task_title').val();
		var employee_filter = [];
		var emp_checkbox =  $('#list-all-assigned').find(':checked');
		if( emp_checkbox.length) $.each(emp_checkbox, function(i, _checkbox){
			employee_filter.push( $(_checkbox).data('id'));
		});
		
		if(dataFilter){
			$.each(dataFilter, function(key, val){
				if( val){
					if( !checkTitle(val, title_filter)){
						return true;
					}
					if( !checkAssign(val, employee_filter)){
						return true;
					}
					tmp_kanbanFilter  = {};
					tmp_kanbanFilter['id'] = val['id'].toString();
					tmp_kanbanFilter['task_status_id'] = val['Status'];
					tmp_kanbanFilter['task_title'] = val['Task'];
					kanbanFilter.push(tmp_kanbanFilter);
				}				
			});
		}
		vv_Data = kanbanFilter;
	}
	function drawSlickGrid(data){
		var ControlGrid = SlickGridCustom.getInstance();
		var newView = ControlGrid.getDataView();
		newView.beginUpdate();
		newView.setItems(data);
		newView.endUpdate();
		ControlGrid.invalidate();
		ControlGrid.render();
	}
	function resizeHandler(){
		var dataGrid = SlickGridCustom.getInstance();
		if( typeof dataGrid != 'undefined'){
			var grid_width = 0;
			$.each(dataGrid.getColumns(), function ( ind, column){
				grid_width += column.width;
			});
			if( grid_width) {
				$('#project_container').width(grid_width + 20);
				var _panel = $('#project_container').closest('.wd-panel');
				if( _panel.length) _panel.width(grid_width + 100);
			}
		}
	}
	HistoryFilter.afterLoad = function(){
		resizeHandler();
		run_filter = 1;
		filterTask();
	}
	$(window).on('resize', resizeHandler);
	function showPopupTaskComment() {
        var taskid = $(this).data("taskid");
        var _html = '';
        var latest_update = '';
		var index = 0;
		var has_comment  = 0;
        var popup = $('#template_logs');
        $.ajax({
            url: '/project_tasks/getCommentTxt',
            type: 'POST',
            data: {
                pTaskId: taskid
            },
            dataType: 'json',
            success: function(data) {
                draw_comment(popup, data);
            }
        });
       
    };
	$('body').on("focusout", "#update-comment", function () {
		var _this = $(this);
        var taskid = _this.data("taskid");
		var text_1 = _this.val();
		if ( text_1 == '') return;
        var popup = $('#template_logs');
		popup.find('.content_comment').addClass('loading');
		var comment_cont = popup.find('.content-logs-inner');
		var _html = '';
        $.ajax({
            url: '/project_tasks/update_text',
            type: 'POST',
            data: {
				data: {
					id: taskid,
					text_1: text_1
				}
            },
            dataType: 'json',
            success: function(data) {
				if( data){
					
					var idEm =  data['_idEm'],
						avatarSrc = employeeAvatar_link.replace('%ID%',idEm );
						nameEmloyee = data['text_updater'],
						comment = data['comment'],
						created = data['text_time'];
					_html = '<div class="content"><div class= "avatar"><span class="circle-name"><img class="avatar-image-img" src="'+ avatarSrc +'"></span></div><div class="item-content"><p>'+ nameEmloyee + ' ' + created +'</p><div class="comment">'+ comment +'</div></div>';
					_html += '<a class="cm-delete" href="javascript:void(0);" onclick="deleteCommentTask(this, '+ data['id']+');"><img src="/img/new-icon/delete-attachment.png"></a>';
					_html += '</div>';
					comment_cont.append(_html);
					_this.val('');
					popup.find('.content_comment').removeClass('loading');
					comment_cont.parent().animate({ scrollTop: comment_cont.height() }, 200);
					/*Update number count mess */
					var _tag = $('.wd-task-actions a.task-comment[data-taskid="' + taskid + '"]');
					_tag.addClass('read');
					_tag.find('span').html( parseInt(_tag.find('span').html())+1);
					
					/* Update to grid */
					var ControlGrid = SlickGridCustom.getInstance();
					var dataView = ControlGrid.getData();
					var actCell = ( ControlGrid.getActiveCell() ) ? ControlGrid.getActiveCell().cell : 0;
					dataView.beginUpdate();
					var _new_data = dataView.getItems();
					$.each( _new_data, function( ind, item){
						if( item.id == taskid){
							item.Text = comment;
							item.text_updater = nameEmloyee;
							item.text_time = data.current;
							item.text_empl = idEm;
							item.current = data.current;
							_new_data[ind] = item;
						}
					});
					dataView.setItems(_new_data);
					dataView.endUpdate();
					ControlGrid.invalidate();
					ControlGrid.render();
					var actRow = ControlGrid.getData().getRowById(taskid);
					
					ControlGrid.gotoCell(actRow, actCell, false);
					if(data_tasks && data_tasks[taskid]){
						data_tasks[taskid]['comment_count'] += 1; 
					}
				/* End Update to grid */
				}
			}
        });
       
    });
	function draw_comment(popup, data){
		taskid = data.id;
		var _html = '';
        var latest_update = '';
		var index = 0;
		var has_comment  = 0;
		var check = false;
		if(listEmployeeManagerOfT[taskid] !== undefined) check = true;
		if( (roleLogin == 'admin') || (canAddComment == 1)) check = true;
		if (data) {
			latest_update = '';
			if( check) _html += '<div class="comment">' + ( ((canAddComment == 1)||(roleLogin != 'conslt')) ? '<textarea data-taskid = '+ taskid +'  cols="30" rows="6" id="update-comment"></textarea>' : '') + '</div>';
			_html += '<div class="content-logs"><div class="content-logs-inner">';
			if( data.old_comment){
				var _cm = data['old_comment']['ProjectTask'];
				// if(_cm.text_time)
				latest_update = _cm.text_time ? _cm.text_time.slice(0, 10) : '';
				latest_update = text_modified + ' ' + latest_update + ' ' + text_by + ' ' + _cm['text_updater'];
				_html += '<div class="content content-' + index++ + '">';
				_html += '<div class = "avatar"><span class="circle-name"><span>' + _cm['avt'] + '</span></span></div>';
				_html += '<div class="item-content"><p>' +_cm['text_updater'] + ' '+ _cm['text_time'] + '</p><div class="comment">'+ _cm['text_1']+'</div></div>'
				_html += '</div>';
				if(_cm && !has_comment) has_comment = 1;
			}
			if( data.result){
				$.each(data.result, function(ind, _data) {
					_cm = _data.ProjectTaskTxt;
					if(_cm && ('id' in Object(_cm)) ){
						var name = ava_src = time = '';
						latest_update = _cm.created.slice(0, 10);
						comment = _cm['comment'] ? _cm['comment'].replace(/\n/g, "<br>") : '';
						name = _data.Employee.first_name + ' ' + _data.Employee.last_name;
						date = _cm.created;
						latest_update = text_modified + ' ' + latest_update + ' ' + text_by + ' ' + name;
						ava_src += '<img width = 35 height = 35 src="'+  employeeAvatar_link.replace('%ID%',_cm['employee_id'] ) +'" title = "'+ name +'" />';
						
						
						 _html += '<div class="content content-'+ index++ +'"><div class="avatar"><span class="circle-name">'+ ava_src +'</span></div><div class="item-content"><p>'+ name + ' ' + date +'</p><div class="comment">'+ comment +'</div></div>';
						if(myRole == 'admin' || _cm['employee_id'] == emp_id_login)  _html += '<a class="cm-delete" href="javascript:void(0);" onclick="deleteCommentTask(this, '+ _cm['id']+');"><img src="/img/new-icon/delete-attachment.png"></a>';
						_html += '</div>';
					}                       
				});
			}
			if( latest_update){
				// draw progress here
			}
			_html += '</div></div>';
		}
		
		popup.find('.content_comment:first').html(_html);
		
		var createDialog2 = function(){
			popup.dialog({
				position    :'center',
				autoOpen    : false,
				height      : 420,
				modal       : true,
				width       : (isTablet || isMobile) ?  320 : 520,
				minHeight   : 50,
				open : function(e){
					var $dialog = $(e.target);
					$dialog.dialog({open: $.noop});	
				}
			});
			createDialog2 = $.noop;
		}
		createDialog2();
		popup.dialog('option',{title: data.task_title }).dialog('open');
	};
	var alert_timeout = '';
	function show_form_alert(form, msg) {
        $(form).find('.alert-message').empty().append(msg);
        clearTimeout(alert_timeout);
        alert_timeout = setTimeout(function () {
            $(form).find('.alert-message').empty();
        }, 3000);
    }
	$('#ProjectTask').on('submit',function(e){
		e.preventDefault();
		var form_tag = '#ProjectTask';
		var _form = $(form_tag);
		// e.preventDefault();
        var start_date = $('#editTaskStartDay').val(),
            end_date = $('#editTaskEndDay').val();
		var st_date = start_date.split('-');
        var en_date = end_date.split('-');
        st_date = new Date(st_date[2],st_date[1],st_date[0]);
        en_date = new Date(en_date[2],en_date[1],en_date[0]);
        if(start_date && end_date && (st_date > en_date)){
            show_form_alert('#ProjectTask', "<?php __('Please enter the end date must be after the start date');?>");
            return;
        }
		_form.closest('.loading-mark').addClass('loading');
		$.ajax({
			type: "POST",
			url: _form.prop('action'),
			data: _form.serialize(),
			dataType: 'json',
			success: function(data){
				if( data.result == 'success'){
					var _task = [] ;
					_task['status'] = parseInt( data['message']['task_status_id'] );
					_task['id'] = parseInt(data['message']['id']);
					_task['text'] = data['message']['task_title'];
					data_tasks[_task['id']] = data['message'];
					$('#kanban').jqxKanban('removeItem', data['message']['id']);
					$('#widget-task').find('li.task-' + _task['id']).remove();
					$('#kanban').jqxKanban('addItem', _task);
					$('#kanban_' + _task['id'] ).addClass('jqx-kanban-item').data('kanbanItemId', _task['id'] );
					// add task into slider
					cancel_popup(form_tag);
				}else{
					show_form_alert('#' + _form.prop('id'), data.message);
					
				}
				_form.closest('.loading-mark').removeClass('loading');
			},
			error: function(){
				_form.closest('.loading-mark').removeClass('loading');
			}
		});
		
	});
	
	function openEditTask(){
		var _this = $(this);
		var taskid = _this.data('taskid');
		var project_id = _this.data('project-id');
		// ajaxGetResourcesIndex(project_id);
		var popup = $('#template_edit_task');
		popup.find('.loading-mark:first').addClass('loading');
		var popup_width = show_workload ? 1080 : 580;
		show_full_popup( '#template_edit_task', {width: popup_width});
		$('.multiselect-pm').addClass('loading');
		$('.multiselect-pm >.wd-combobox a.circle-name').remove();
		$('.multiselect-pm >.wd-combobox p').show();
		var _cont = $('.multiselect-pm .option-content');
		_cont.empty();
		$.ajax({
			url: '/projects/getTeamEmployees/' + project_id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
				if( data.success == true){
					var _cont = $('.multiselect-pm .option-content');
					var _html = '';
					$.each(data.data, function(ind, emp){
						emp = emp.Employee;
						_html += '<div class="projectManager wd-data-manager wd-group-' + emp.id + '">';
						_html += '<p class="projectManager wd-data">';
						_html += '<input type="checkbox" name="data[task_assign_to_id][]" value="' + emp.id + '-' + emp.is_profit_center + '" id="newtaskTaskAssignToId-'+ emp.id +'">';
						_html += '<span class="option-name" style="padding-left: 5px;">' + emp.name + '</span>';
						_html += '</p> </div>';
					});
					_cont.html(_html);
					init_multiselect('#multiselect-pm-add, #multiselect-pm-edit');
					$('.multiselect-pm').removeClass('loading');
				}
				$.ajax({
					url: '/project_tasks/get_task_info/',
					type: 'POST',
					data: {
						data: {
							id: taskid,
						}
					},
					dataType: 'json',
					success: function(data) {
						if(data){
							if( data.result == 'success'){
								c_task_data = data.data;
								popupEditTask_loadPhase(project_id);
								ajaxGetResourcesIndex(project_id);
								$('#editTaskID').val(data.data.id).trigger('change');
								$('#task_title').val(data.data.task_title).trigger('change');
								$('#toPhaseEdit').val(data.data.project_planed_phase_id).trigger('change');
								$('#toStatus').val(data.data.task_status_id).trigger('change');
								var start_date = data.data.task_start_date,
									end_date = data.data.task_end_date;
								if( start_date){
									var st_date = start_date.split('-');
									st_date = st_date[2] + '-' + st_date[1] + '-' + st_date[0];
									$('#editTaskStartDay').val(st_date).trigger('change');
								}
								if( end_date){
									var en_date = end_date.split('-');
									en_date = en_date[2] + '-' + en_date[1] + '-' + en_date[0];
									$('#editTaskEndDay').val(en_date).trigger('change');
								}
								$('#editTaskStartDay').val(st_date).trigger('change');
								$('#editTaskEndDay').val(en_date).trigger('change');
								setTimeout(function(){ 
									set_assigned(popup.find('#multiselect-pm-edit'), data.data.assigned);
									multiselect_pm_editonChange('multiselect-pm-edit');
									resetOptionAssigned(data.employees_actif, '#multiselect-pm-edit');
								}, 2000);
							}else{
								show_form_alert('#ProjectTask', "data.message");
							}
						}else{
							show_form_alert('#ProjectTask', "<?php __('Get task failed');?>");
						}
						popup.find('.loading-mark:first').removeClass('loading');
					},
					error: function(){
						show_form_alert('#ProjectTask', "<?php __('Get task failed');?>");
						popup.find('.loading-mark:first').removeClass('loading');
					}
				});
			}
		});
	};
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
	$(document).on('ready', function(){
		$('form').find('select, input').trigger('change');
	});
	
	
	$("#newTaskEndDay, #newTaskStartDay, #editTaskStartDay, #editTaskEndDay").datepicker({
        dateFormat      : 'dd-mm-yy'
    });
	$('.wd-date').define_limit_date('#newTaskStartDay', '#newTaskEndDay', '#editTaskStartDay', '#editTaskEndDay');
	
	var newtaskIndexForm_date_validated = 1;
    function validated(_this){
        var st_date = $('#newTaskStartDay').val();
        var en_date = $('#newTaskEndDay').val();
		if( st_date == '' || en_date == '' ) return;
        st_date = st_date.split('-');
        en_date = en_date.split('-');

        st_date = new Date(st_date[2],st_date[1],st_date[0]);
        en_date = new Date(en_date[2],en_date[1],en_date[0]);
        if( st_date > en_date){
            $(_this).css('border-color','red');
            newtaskIndexForm_date_validated = 0;
        }else{
            $('#newTaskStartDay').css('border-color','');
            $('#newTaskEndDay').css('border-color','');
            newtaskIndexForm_date_validated = 1;
        }
    };
	function disable_all_option(){
		$('#toPhase').val('').prop( "disabled", true );
	}
	function popupAddTask_phaseOnChange(){
		var _project_id = $('#toProjectAdd').val();
		var _phase_id = $('#toPhaseAdd').val();
		if( _phase_id == '') {
			$('#newTaskStartDay').val('');
			$('#newTaskEndDay').val('');
			return;
		}
		var listPhases = <?php echo json_encode( $listPhases);?>;
	
		if(_project_id && _phase_id){
			var phase = listPhases[_project_id][_phase_id];
			$('#newTaskStartDay').val(phase.phase_real_start_date).trigger('change');
			$('#newTaskEndDay').val(phase.phase_real_end_date).trigger('change');
		}
	}
	function popupEditTask_phaseOnChange(){
		var _project_id = $('#toPhaseEdit').find(':selected').data('projectid')
		var _phase_id = $('#toPhaseEdit').val();
		if( _phase_id == '') {
			$('#newTaskStartDay').val('');
			$('#newTaskEndDay').val('');
			return;
		}
		var listPhases = <?php echo json_encode( $listPhases);?>;
	
		if(_project_id && _phase_id){
			var phase = listPhases[_project_id][_phase_id];
			$('#newTaskStartDay').val(phase.phase_real_start_date).trigger('change');
			$('#newTaskEndDay').val(phase.phase_real_end_date).trigger('change');
		}
	}
	function popupAddTask_projectOnChange(){
		var _project_id = $('#toProjectAdd').val();
		
		if( _project_id == '') {
			disable_all_option();
			return;
		}
		var listPhases = <?php echo json_encode( $listPhases);?>;
		var _options = '';
		_options += '<option value=""><?php __('Phase');?></option>';
		$('#toPhaseAdd').addClass('loading').prop( "disabled", false ).val('');
		$.each( listPhases[_project_id], function (pid, phase){
			_options += '<option value="' + pid + '" data-projectid="' + _project_id + '">' + phase.name + '</option>';
		});
		$('#toPhaseAdd').html(_options );
		$('#toPhaseAdd').removeClass('loading');
		ajaxGetResourcesIndex(_project_id);
		
	}
	function popupEditTask_loadPhase(project_id){
		var listPhases = <?php echo json_encode( $listPhases);?>;
		var _options = '';
		// _options += '<option value=""><?php __('Phase');?></option>';
		$('#toPhaseEdit').addClass('loading').prop( "disabled", false ).val('');
		if(listPhases[project_id]){
			$.each( listPhases[project_id], function (pid, phase){
				_options += '<option value="' + pid + '" data-projectid="' + project_id + '">' + phase.name + '</option>';
			});
		}
		$('#toPhaseEdit').html(_options );
		$('#toPhaseEdit').removeClass('loading');
	}
	function ajaxGetResourcesIndex(_project_id){
		$('.multiselect-pm').addClass('loading');
		$('.multiselect-pm >.wd-combobox a.circle-name').remove();
		$('.multiselect-pm >.wd-combobox p').show();
		var _cont = $('.multiselect-pm .option-content');
		_cont.empty();
		$.ajax({
			url: '/projects/getTeamEmployees/'+_project_id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
				if( data.success == true){
					var _cont = $('.multiselect-pm .option-content');
					var _html = '';
					$.each(data.data, function(ind, emp){
						emp = emp.Employee;
						// console.log(emp);
						_html += '<div class="projectManager wd-data-manager wd-group-' + emp.id + ' actif-'+ emp.actif +'">';
						_html += '<p class="projectManager wd-data">';
						_html += '<input type="checkbox" name="data[task_assign_to_id][]" value="' + emp.id + '-' + emp.is_profit_center + '" id="newtaskTaskAssignToId-'+ emp.id +'">';
						_html += '<span class="option-name" style="padding-left: 5px;">' + emp.name + '</span>';
						_html += '</p> </div>';
					});
					_cont.html(_html);
					init_multiselect('#multiselect-pm-add, #multiselect-pm-edit');
					$('.multiselect-pm').removeClass('loading');
				}
			}
		});
	}
	jQuery.removeFromArray = function(value, arr) {
        return jQuery.grep(arr, function(elem, index) {
            return elem !== value;
        });
    };
	init_multiselect('#multiselect-pm-add, #multiselect-pm-edit');

    // JS function for kanban
	projectStatusEX = <?php echo json_encode($projectStatusEX); ?>;
	task_kanban = <?php echo json_encode($datas_value); ?>;
	list_assigned_avatar = <?php echo json_encode($employeeAssignedAvt); ?>;
	data_tasks = <?php echo json_encode($datas); ?>;
	listPCAssign = <?php echo json_encode($listResourceAssignName); ?>;
	listStatus = <?php echo json_encode($list_org_project_status); ?>;
	i18ns = <?php echo json_encode($i18n); ?>;
	svg_icons  = <?php echo json_encode($svg_icons); ?>;
	
	api_key = <?php echo json_encode($employee_info['Employee']['api_key']); ?>;
	var itemRenderer = function (element, data, resource, task_item){
		if(task_item){
			// Assign to
			var _html_head = '<div class="task-list-assigned">';
			var list_assign = task_item['assigned'];
			if(list_assign){
				$.each(list_assign, function(ind, _data_assign) {
					if($.isPlainObject(_data_assign)){
						is_profit_center = _data_assign['is_profit_center'];
						reference_id = _data_assign['reference_id'];
					}else{
						_data_assign = _data_assign.split('_');
						is_profit_center = _data_assign[1];
						reference_id = _data_assign[0];
					}
					if(is_profit_center == 0){
						_html_head += '<div class="circle-name"><img alt="" src="' + employeeAvatar_link.replace('%ID%', reference_id) + '"/></div>';
					}else{
						_html_head += '<div class="circle-name" title="'+ listPCAssign[reference_id] +'"><span data-id="'+ reference_id +'"><i class="icon-people"></i></span></div>';
					}
					
				});
				
			}
			_html_head += '</div>';
			
			// Task end date
			if(task_item['end_date_format']){
				translate_end_date = task_item['end_date_format'].split(' ');
				var late = task_item['task_late'];
				_html_head += '<span class="task-time' + (late ? ' task_late' : '') + '">'+ translate_end_date[0] + ' ' + i18n[translate_end_date[1]] + ' ' + translate_end_date[2]+'</span>';
			}
			
			// Task status
			// #444 remove status
			/*
			_html_head += '<div class="task-status">';
			_status_title = '<div class="status_texts">';
			_status_button = '<div class="status_dots">';
			$.each(listStatus, function(id, task_status) {
				color = 'status_blue';
				if(task_status.status == 'CL') color = 'status_green';
				else if(task_item['late'] == 1) color = 'status_red';
				active = (data.status == task_status.id) ? 'active' : '';
				
				_status_title += '<span class="status_item status_item_'+ task_status.id +' status_text '+ active +' '+ color +'" data-value="'+ task_status.id +'">'+ task_status.name +'</span>';
				_status_button += '<a href="javascript:void(0);" class="status_item status_item_'+ task_status.id +' status_dot '+ active +' '+ color +'" data-value="'+ task_status.id +'" title="'+ task_status.name +'" data-taskid="'+ data.id +'"></a>';
			});
			_status_title += '</div>';
			_status_button += '</div>';
			_html_head += _status_title + _status_button;
			_html_head += '</div>';
			*/
			// END #444 remove status
			
			$(element).find(".task-head").html(_html_head);
			var phase_color = task_item['project_planed_color'] ? task_item['project_planed_color'] : '';
			if(task_item['project_planed_name']) $(element).find(".task-item-phase").html('<span style="background-color: '+ phase_color +';"></span>'+ task_item['project_planed_name']);
			// Task footer
			_html_footer = '';
			
			_html_footer = '<div class="footer-top clear-fix">';
			if(task_item['project_name']){
				_html_footer += '<p class="project-title">'+ task_item['project_name'] +'</p>';
			}
			estimated = (task_item['workload']) ? task_item['workload'] : 0;
			consumed = task_item['consume'];
			// consumed = Math.round(consumed, 2);
			progress = 100;
			if( estimated != 0) progress = (consumed / estimated) * 100;
			else if(consumed) progress = 0;
			if( show_workload == 1) {
				_html_footer += '<div class="task-workload"><p class="label">'+ i18ns['Workload'] +'</p><span class="value">'+ estimated + i18ns['M.D']+'</span></div>';
			}
			if((check_consumed) && (check_consumed == 1)){
				_html_footer+= '<div class="task-consumed"><p class="label">'+ i18ns['Consumed'] +'</p><span class="value">'+ consumed + i18ns['M.D']+'</span></div>';
				// Progress line
				_css_class = (progress <= 100) ? 'green-line': 'red-line';
				display_value = (progress < 100) ? Math.round(progress) : 100;
				//Them dieu kien hien thi progress. neu hien thi consumed or manual consumed thi moi hien thi progress. QuanNV 04/07/2019
				_html_footer += '<div class="task-progress" data-progress="'+ progress +'">';
				
				_html_footer += '<div class="progress-slider '+ _css_class +'" data-value="'+ display_value +'"><div class="progress-holder"><div class="progress-line-holder"></div></div><div class="progress-value" style="width:'+ display_value +'%" title="'+ display_value +'%"><div class="progress-line"></div><div class="progress-number"> <div class="text" style="margin-left: -'+ display_value +'%;" >'+ display_value+'%</div></div></div></div></div>';
			}
			_html_footer += '</div>';
						
			// footer bottom
			_html_footer += '<div class="footer-bottom">';
			_html_footer += '<div class="wd-task-actions">';
			_html_footer += '<a href="javascript:void(0)" onclick="showPopupTaskComment.call(this);" class="wd-task-action task-comment no-comment '+ (task_item['comment_count'] ? 'has-value' : '')  +' '+ (task_item['read_status'] ? 'read' : '')  +'" data-taskid="'+ task_item['id'] +'" data-project-id="'+ task_item['project_id'] +'">';
			_html_footer += svg_icons['message'];
			
			_html_footer += '<span>' + task_item['comment_count'] + '</span></a>';
			_html_footer += '<a href="javascript:void(0)" onclick="openAttachmentDialog.call(this);" class="wd-task-action task-attachment '+ (task_item['attachment_count'] ? 'has-value' : '')  +' '+ (task_item['attach_read_status'] ? 'read' : '')  + '" data-task-id="'+ task_item['id'] +'" data-project-id="'+ task_item['project_id'] +'" tabindex="0">';
			_html_footer += svg_icons['document'];
			_html_footer += '<span>'+ task_item['attachment_count'] +'</span></a>';
			var can_edit = ( task_item['project_id'] in listProjectbyPM);
			if( can_edit){
				var is_nct = '', edit_action = 'openEditTask.call(this);';
				if(task_item['is_nct'] == 1){
					is_nct = 'task-nct';
					edit_action = 'openEditTaskNCT(this);';
				}
				_html_footer += '<a href="javascript:void(0)" onclick="'+ edit_action +'" class="'+ is_nct +' wd-task-action task-edit "  data-taskid="'+ task_item['id'] +'" data-project-id="'+ task_item['project_id'] +'" tabindex="0">';
				_html_footer += svg_icons['edit'];
				_html_footer += '<span></span></a>';
			}
			_html_footer += '</div>';
			_html_footer += '</div>';
			// footer bottom
			$(element).find(".task-footer").html(_html_footer);
		}
	};
	function filter_kanban(kanban_filter){
		init_kanban(kanban_filter);
	}
	function init_kanban(task_kanban){
		if( $('#kanban').length ) {
			$('#kanban').jqxKanban('destroy'); 
			$('.kanban-box').html('<div class="wd-kanban-container"><div id="kanban" class="project-task-widget kanban-task"></div></div>');
		}
		var fields = [
			 { name: "id", type: "number" },
			 { name: "status", map: "task_status_id", type: "number" },
			 { name: "text", map: "task_title", type: "string" },
			 { name: "task_id", type: "number" },
				 
		];
		var source =
		 {
			 localData: task_kanban,
			 dataType: "array",
			 dataFields: fields
		 };
		var dataAdapter = new $.jqx.dataAdapter(source);
        var resourcesAdapterFunc = function () {
            var resourcesSource =
            {
                localData: task_kanban,
                dataType: "array",
				 dataFields: [
					{ name: "id", type: "number" },
				]
            };

            var resourcesDataAdapter = new $.jqx.dataAdapter(resourcesSource);
            return resourcesDataAdapter;
        }
		
		var wdKanban = $('#kanban');     
		var heightKanban= $(window).height() - 140;	/* wd-panel padding top, bottom 40, Wd-title 60 */
        $(window).resize(function(){
            var heightKanban = $(window).height() - 140;
            $('#kanban').css({ height: heightKanban});
        });
		$('#kanban').jqxKanban({
			template: "<div><div class='task-item'>"
					+ "<div class='task-head'></div>"
					+ "<div class='jqx-kanban-item-text task-title'></div>"
					+ "<div class='task-item-phase'></div>"
					+ "<div class='task-footer clearfix'></div>"
					+ "</div></div>",
			width: '100%',
			height: heightKanban,
			resources: resourcesAdapterFunc(),
			source: dataAdapter,
			itemRenderer: function(element, data, resource){
				task_item = data_tasks[data.id];
				itemRenderer(element, data, resource, task_item);
			},
			columns: projectStatusEX,
			columnRenderer: function (element, collapsedElement, column) {
                var columnItems = $("#kanban").jqxKanban('getColumnItems', column.dataField).length;
				if( listStatus[column.dataField]['status'] == 'IP') element.closest('.jqx-kanban-column').addClass('in_progress_column');				
                element.find(".task_count").remove();
                element.find(".jqx-kanban-column-header-title").append($('<span data-id="'+ column['dataField'] +'" class="task_count">(' + columnItems + ')</span>'));
            },
			ready: function(){
				if(can_move == 0){
					$("#kanban").find('*').unbind('mousedown');
				}
			}
		});
		$('#kanban').on('itemMoved', function (event) {
            var args = event.args;
			console.log(args);
            var itemId = args.itemData.id;
            var canModified = data_tasks[itemId]['canModified'];
			if(canModified == 0) return;
            var flag_id = $('#kanban_'+args.itemId).find('.flag-id').val();
            if(flag_id) itemId = flag_id;
            var newColumn = args.newColumn['dataField'];
            if(itemId && newColumn){
                $.ajax({
                    url: '/kanban/update_task_status',
                    type: 'POST',
                    data: {
                        id: itemId,
                        status: newColumn,
                    },
					dataType: 'json',
					success: function(respon){
						if( respon.result == true){
							_task_move = $('#kanban_'+itemId);
							_status_active = $('.status_item_'+ newColumn);
							_task_move.find('.status_item').removeClass('active');
							_task_move.find(_status_active).addClass('active');
							
							 // update to grid
							SlickGridCustom.update_after_edit(itemId, {'Status': newColumn});
							wd_title_filter();
						}
					},
                });
            }
        });
	}
	function renderPhaseOfProject(_project_id){
		if( _project_id == '') {
			disable_all_option();
			return;
		}
		var listPhases = <?php echo json_encode( $listPhases);?>;
		var _options = '';
		_options += '<option value=""><?php __('Phase');?></option>';
		$('#popupnct-phase').addClass('loading').prop( "disabled", false ).val('');
		$.each( listPhases[_project_id], function (pid, phase){
			_options += '<option value="' + pid + '" data-projectid="' + _project_id + '">' + phase.name + '</option>';
		});
		$('#popupnct-phase').html(_options );
		$('#popupnct-phase').removeClass('loading');
		ajaxGetResourcesIndex(_project_id);
		
	}
	function openEditTaskNCT(elm) {
		var id = $(elm).data('taskid');
		var project_id = $(elm).data('project-id');
		renderPhaseOfProject(project_id);
		$('#template_add_nct_task').find('.add_nct_task-popup').addClass('loading');
		$.ajax({
			url : "/project_tasks/getNcTask/",
			type: "POST",
			cache: false,
			data: {data: {id: id}},
			dataType: 'json',
			success: function (data) {
				global_task_data = data;
				var type = 1;
				if( 'data' in global_task_data){
					$.each(global_task_data.data, function (key, val){
						console.log( val );
						if( typeof val[0]['type'] !== 'undefined') type = val[0]['type'];
						
						return false;
					});
				}
				$('#popupnct-range-type').val(type);
				show_full_popup('#template_add_nct_task', {width: 'inherit'});
				$('.popup-back').empty().html('<?php __('Edit NCT task');?>');
				$('#popupnct-id').val(id).trigger('change');
				$('#popupnct-name').val(data['task']['task_title']).trigger('change');
				$('#popupnct-phase').val(data['task']['project_planed_phase_id']).trigger('change');
				var assigns = [];
				if(data['columns']){
					$.each(data['columns'], function(key, column){
						id = (column.id).split('-');
						assigns.push({reference_id: id[0], is_profit_center: id[1]});
					});
				}
				render_list_date(data);
				setTimeout(function(){
					set_assigned($('#template_add_nct_task').find('.popupnct_nct_list_assigned'), assigns);
					multiselect_popupnct_pmonChange('multiselect-popupnct-pm');
					resetOptionAssigned(data.employees_actif, '#multiselect-popupnct-pm');
				}, 1000);
				
				$('#popupnct-status').val(data['task']['task_status_id']).trigger('change');
				$('#popupnct-start-date').val(data['task']['task_start_date']).trigger('change');
				$('#popupnct-end-date').val(data['task']['task_end_date']).trigger('change');
				$('#popupnct-profile').val(data['task']['profile_id']).trigger('change');
				$('#popupnct-priority').val(data['task']['task_priority_id']).trigger('change');
				$('#popupnct_per-workload').val(data['task']['estimated']).trigger('change');
				$('#template_add_nct_task #btnSave').empty().html('<?php __('Save');?>');
				setLimitedDate('#popupnct-start-date', '#popupnct-end-date');
				set_width_popupnct();
			},
			complete: function(){
				$('#template_add_nct_task').find('.add_nct_task-popup').removeClass('loading');
			},
		});
	
	}
	
	function render_list_date(data){
		var request = data.request;
		var data = data.data;
		var consumed = 0, in_used = 0;
		$.each(data, function(row, val){
			var date = row.substr(2);
			var date_name = toRowName(row);
			var html = '<tr><td id="date-' + date + '" class="popupnct-date" style="text-align: left">' + toRowName(row) + '<span class="cancel" onclick="removeRow(this)" href="javascript:;"></span></td>';
			
			var c = parseFloat(request[row][0]), iu = parseFloat(request[row][1]);
            if( isNaN(c) )c = 0;
            if( isNaN(iu) )iu = 0;
			//last col
            html += '<td style="background: #f0f0f0" class="ciu-cell">' + c.toFixed(2) + ' (' + iu.toFixed(2) + ')</td>';
			html += '<td class="row-action">';
			if(!( c > 0 || iu > 0 )){
                html += '<a class="cancel" onclick="popupnct_removeRow(this)" href="javascript:;"></a>';
            }
			html += '</td></tr>';
            $('#popupnct-assign-table tbody').append(html);
			consumed += c;
			in_used += iu;
		});
		$('#popupnct_total-consumed').empty().html(consumed.toFixed(2) +' ('+ in_used.toFixed(2) +')');
	}
	if(list_project_has_nct.length > 0){
		$(window).ready(function(){
			setTimeout(function(){
				if( !$('#addProjectTemplate').hasClass('loaded') && !$('#addProjectTemplate').hasClass('loading')){
					$('#add_nct_task-popup').addClass('loading');
					$.ajax({
						url : "/project_tasks_preview/add_task_popup/" + list_project_has_nct[0],
						type: "GET",
						cache: false,
						success: function (html) {
							$('#addProjectTemplate').empty().append($(html));
							$('.tabPopup .liPopup a.active').empty().html('<?php __('Edit task');?>');
							$('#template_add_task .btn-ok span').empty().html('<?php __('Save');?>');
							$('.right-link').hide();
							$('#add-form').trigger('reset');
							$(window).trigger('resize');
							$('#addProjectTemplate').addClass('loaded');
							$('#addProjectTemplate').removeClass('loading');
							$('input[data-return="form-return"]').val('<?php echo $this->here;?>');
							if( $('#addProjectTemplate').hasClass('open') ){
								var popup_width = show_workload ? 1080 : 580;
								show_full_popup( '#template_add_task', {width: popup_width});
								$('#template_add_task').find('input, select').trigger('change');
							}
						},
						complete: function(){
							$('#addProjectTemplate').removeClass('loading');
						},
						error: function(){
							alert('aaa');
						}
					});
				}
			}, 2000);
		});
	}
	function wd_show_add_task(){
		var popup_width = show_workload ? 1080 : 580;
		show_full_popup( '#template_add_task_index', {width: popup_width});
	}
	function deleteCommentTask(_this, cm_id){
		// if(!canModify) return;
		$(_this).addClass('loading');
		if(cm_id){
			 $.ajax({
				url: '/project_tasks/deleteCommentTxt',
				data: {
					cm_id: cm_id
				},
				type:'POST',
				dataType: 'json',
				success: function(res){
					if(res == 'success'){
						$(_this).closest('.content').remove();
						$(_this).removeClass('loading');
					}
				}			
			});
		}
	}
	
	/*
	* Workload table
	* init: list_assigned, c_task_data, show_workload
	*/
	var c_task_data = {}; // Continous task data 
	var list_assigned = {}; // Continous task data 
	var show_workload = <?php echo json_encode($show_workload); ?>; 
	function template_add_task_index_showed(){
		init_multiselect('#multiselect-pm-add');
	}
	function c_calcTotal(){
		$.each( $('.task-workload .nct-assign-table'), function( ind, elm){
			var _total = 0;
			$.each( $(elm).find('.c_workload'), function(ind, inp){
				_total += $(inp).val() ? parseFloat($(inp).val()) : 0;
			});
			$(elm).find('.total-consumed').text( _total.toFixed(2) );
		});
	}
	function draw_c_workload_table(elm){
		if( !elm) return;
		if(list_assigned.length == 0){
			elm.find('.nct-assign-table tbody').empty();
			return;
		}
		var _data_assigned = {};
		if(c_task_data && c_task_data.assigned){
			$.each(c_task_data.assigned, function(ind, emp){
				var key = emp.reference_id + '-' + emp.is_profit_center;
				_data_assigned[key] = emp;
			});
		}
		$.each( elm.find('.nct-assign-table .c_workload'), function(ind, cell){
			var id = $(cell).data('id');
			if( !(id in _data_assigned) ){
				$(cell).closest('tr').remove();
			}
		});
		$.each(list_assigned, function(ind, emp){
			var id = emp.id,
				name = emp.name;
			var employee = (emp.id).split('-');
			var e_id = employee[0];
			var is_profit_center = employee[1]==1 ? 1 : 0;
			
			var tag = '.c_workload-' + id;
			if( $(tag).length == 0){
				var _avt = '<span class="circle-name" title="' + name + '" data-id="' + id + '">';
				if( is_profit_center == 1 ){
					_avt += '<i class="icon-people"></i>';
				}else{
					_avt += '<img width = 35 height = 35 src="'+  employeeAvatar_link.replace('%ID%', e_id ) +'" title = "'+ name +'" alt="avatar"/>';	
				}
				_avt += '</span>';
				
				var res_col = '<td class="col_employee" >' + _avt + '</td>';
				
				var e_workload = (id in _data_assigned) ? _data_assigned[id]['estimated'] : 0;
				e_workload = parseFloat(e_workload).toFixed(2);
				var ip_name = 'data[workloads][' + id + ']';
				var val_col = '<td class="col_workload"><input type="text" id="val-' + id + '" class="c_workload c_workload-' + id + '"  id="c_workload-' + id + '" data-id="' + id + '" value="'+ e_workload +'" name="' + ip_name + '[estimated]" onkeyup="c_calcTotal(this)"/></td>';
				var _html = '<tr class="workload-row workload-row-' + id + ' ">' + res_col + val_col + '</tr>';
				elm.find('.nct-assign-table tbody').append( _html);
			}
		});
		c_calcTotal();
	}
	function get_list_assign(_this_id){
		var multiSelect = $('#' + _this_id);
		var _list_selected = multiSelect.find(':checkbox:checked');
		var employee_selected = []; 
		if( _list_selected.length){
			$.each( _list_selected, function(ind, emp){
				var key = $(emp).val();
				var val = $(emp).next('.option-name').text();
				employee_selected.push({id: key, name: val});
			});
		}
		return employee_selected;
	}
	function multiselect_pm_addonChange(_this_id){
		list_assigned = get_list_assign(_this_id);
		var _form = $('#' + _this_id).closest('form');
		draw_c_workload_table(_form);
	}
	function multiselect_pm_editonChange(_this_id){
		list_assigned = get_list_assign(_this_id);
		var _form = $('#' + _this_id).closest('form');
		draw_c_workload_table(_form);
	}
	function cancel_popup_template_add_task_index(){
		c_task_data = {};
		list_assigned = {};
		$('#new_task_assign_table').find('tbody').empty();
		c_calcTotal();
	}
	function cancel_popup_template_edit_task(){
		c_task_data = {};
		list_assigned = {};
		$('#edit_task_assign_table').find('tbody').empty();
		c_calcTotal();
	}
	/*
	* END Workload table
	*/
</script>