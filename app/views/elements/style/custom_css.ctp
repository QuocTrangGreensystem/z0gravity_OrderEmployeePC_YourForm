<?php
$employee_info = $this->Session->read("Auth.employee_info");
  // ob_clean();
  // debug($employee_info); 
  // exit;
// Change color default here and in view/colors/index
$defaultColor = array(
	'page_color' => '#0B4578',
	'header_color' => '#2362B8',
	'line_color' => '#C1C8C6',
	'table_color' => '#2362B8',
	'popup_color' => '#2362B8',
	'kpi_color' => '#2362B8',
	'tab_color' => '#2362B8',
	'tab_selected' => '#B1B8B6',
	'tab_hover' => '#2362B8',
	'button_color' => '#2362B8'
);
$schemeColor = array(
	'#217FC2',
	'#FFD051',
	'#016FDB',
	'#6EAF79',
	'#041839',
	'#049BF7',
	// '#F1F6FA',
);
$schemeColorLength = count($schemeColor);
if( empty($employee_info['Color'])) $employee_info['Color'] = array();
foreach($defaultColor as $key => $value){
	if( empty($employee_info['Color'][$key]) ) $employee_info['Color'][$key] = $value;
}
 // debug($defaultColor); 
 // debug($employee_info['Color']); 
  // exit;
?>

<!-- Custom color for Company -->
<!--
<?php 
	foreach($employee_info['Color'] as $key => $value){
		echo $key.': '.$value . PHP_EOL;
	}
?>
-->
<style>
	<?php for( $i = 0; $i < $schemeColorLength; $i++){ ?>
		.kanban-task .jqx-kanban-column:nth-child(<?php echo $schemeColorLength;?>n+<?php echo $i+1;?>) .jqx-kanban-column-header{
			background: <?php echo $schemeColor[$i];?> !important;
		}
	<?php } ?> 
    <?php if(!empty($employee_info['Color']['page_color'])) {?>
       html.page-login{
            background: inherit;
       }
    <?php } ?>

    <?php if(!empty($employee_info['Color']['button_color'])) { ?>
       .btn--fancy{
              background: linear-gradient(to right, <?php echo $employee_info['Color']['button_color']; ?> 9.6%, <?php echo $employee_info['Color']['button_color']; ?> 86.33%);
       }
       .wd-button-f{
         border-color: <?php echo $employee_info['Color']['button_color']; ?>;
         background-color: <?php echo $employee_info['Color']['button_color']; ?>;
       }
       .home-btn:hover{
          background: linear-gradient(to right, <?php echo $employee_info['Color']['button_color']; ?> 15%, <?php echo $employee_info['Color']['button_color']; ?> 100%);
          border-color: <?php echo $employee_info['Color']['button_color']; ?>
        }
        body {
          scrollbar-face-color: <?php echo $employee_info['Color']['button_color']; ?>;
        }
        ::-webkit-scrollbar-thumb {
          background: <?php echo $employee_info['Color']['button_color']; ?>
        }
    <?php } ?>
    <?php if(!empty($employee_info['Color']['header_color'])) {?>

       #wd-container-header, body .budget-title-total, body .budget-title{
            background: inherit;
            background-color:<?php echo $employee_info['Color']['header_color']; ?>
       }       
       #wd-container-footer{
          border-color: <?php echo $employee_info['Color']['header_color']; ?>;
          background: inherit;
          background-color:<?php echo $employee_info['Color']['header_color']; ?>;
       }
    <?php } ?>
   <?php if(!empty($employee_info['Color']['table_color'])) {?>
       body .slick-header .slick-header-column, body .slick-pane.slick-pane-header, body table.display thead tr.wd-header, body #absence-fixed th,
       body #absence th,body #absence td.st,body #absence-table-fixed td.st,body #absence-task th, body #pager, body .slick-pager, .gantt-chart-wrapper .gantt .gantt-num, .pdc-header-table tr th, body .gs-custom-cell-euro-header, body .headerHighLight, body .absence-fixed th, body #table-cost table tr td.cost-header,
	   .ui-datepicker .ui-datepicker-header, body #ticket-fields th, body .ticket-header{
            background: inherit;
            background-color:<?php echo $employee_info['Color']['table_color']; ?>
       }
	   .company_background_color,
	   body .wd-forecasts-table th,
	   .table-container table thead th,
	   .wd-table .budget-title-second{
			background-color: <?php echo $employee_info['Color']['table_color']; ?> ;
			color: #fff;
		}
		.wd-widget .widget-title,
		.wd-layout .wd-new-list .wd-tt,
        body .x-column-header-inner, body .x-column-header-trigger, body .x-column-header.x-column-header-focus .x-column-header-inner {
            background-color:<?php echo $employee_info['Color']['table_color']; ?>
       }
       .popup-header, .popup-header-2,
       .new-design .x-grid-header-ct,
        body .gantt-head td,
		.absence-calendar .calendar-day-head,
        body #assign-table thead td,
        body .nct-assign-table thead td,
        #area-append-ntc .title-ntc,
        body .ui-dialog.dialog-special-task .ui-dialog-titlebar {
            background-color:<?php echo $employee_info['Color']['table_color']; ?> !important;
       }
       body .new-design .x-column-header,
       body .new-design .x-column-header-inner,
       body #assign-table thead td {
          border-color: <?php echo $employee_info['Color']['table_color']; ?> !important;
       }
       
      #gs-popup-content .popup-header td,
      #gs-popup-content .popup-header-2 td,
      .profit_plus .gantt-primary .gantt-node.gantt-node-head .gantt-num,
      .ganttCustom-month #export-header,
      .ganttCustom-month #export-header .gantt-num,
      .ganttCustom-month table .gantt-head,
      .ganttCustom-month table .gantt-head td,
      .ganttCustom-month #export-header tr, /*  : table as header */
      .ganttCustom-month #export-header tr td,
      body .gantt-chart-wrapper, body .pdc-table tbody{
          border-color: <?php echo $employee_info['Color']['table_color']; ?>;
       }
	   .table-container.pdc-container tr th:first-child{
          border-left-color: <?php echo $employee_info['Color']['table_color']; ?>;
       }
      

      #GanttChartDIV.profit_plus .gantt-content-wrapper .gantt-list .gantt-staff:first-child .gantt-node:first-child{
        border-top-color: <?php echo $employee_info['Color']['table_color']; ?>;
      }
      .ui-datepicker .ui-datepicker-header .ui-datepicker-title,
       .ganttCustom-month .trFamily .gantt-title td,
       .ui-datepicker .ui-datepicker-buttonpane button:hover{
          background-color: <?php echo $employee_info['Color']['table_color']; ?> !important;
       }
   <?php } ?>
   <?php if(!empty($employee_info['Color']['popup_color'])) {?>
       body .ui-dialog .ui-dialog-titlebar, body .comment_form .x-window-header-default-top{
            background: inherit;
            background-color:<?php echo $employee_info['Color']['popup_color']; ?>
       }
   <?php } ?>
   <?php if(!empty($employee_info['Color']['kpi_color'])) {?>
		.wd-table .budget-title >th,
       body .group-content > h3{
            background: inherit;
            background-color:<?php echo $employee_info['Color']['kpi_color']; ?>
       }
       body .group-content{
          border-color: <?php echo $employee_info['Color']['kpi_color']; ?>;
       }
   <?php } ?>
   <?php if(!empty($employee_info['Color']['tab_color'])) {?>
      #wd-top-nav a span.tab-center{
          background: <?php echo $employee_info['Color']['tab_color']; ?>;
      }
      .wd-tab .wd-item li{
          background-color: <?php echo $employee_info['Color']['tab_color']; ?>;
      }
      #wd-top-nav a span.tab-center:after{
          border-left-color: <?php echo $employee_info['Color']['tab_color']; ?>;
      }
      #wd-top-nav a span.tab-center:before{
          border-right-color: <?php echo $employee_info['Color']['tab_color']; ?>;
      }
      .wd-item-v li{
        background: transparent;
        background-color: <?php echo $employee_info['Color']['tab_color']; ?>;
      }
   <?php } ?>
   <?php if(!empty($employee_info['Color']['tab_selected'])) {?>
      #wd-top-nav a:hover span.tab-center,
      #wd-top-nav a span.wd-current-center{
          background: <?php echo $employee_info['Color']['tab_selected']; ?>;
      }
      #wd-top-nav a:hover span.tab-center:after,
      #wd-top-nav a span.wd-current-center:after{
          border-left-color: <?php echo $employee_info['Color']['tab_selected']; ?>;
      }
      #wd-top-nav a:hover span.tab-center:before,
      #wd-top-nav a span.wd-current-center:before{
          border-right-color: <?php echo $employee_info['Color']['tab_selected']; ?>;
      }
      .wd-tab .wd-item li.wd-current, .wd-tab .wd-item li:hover{
        background-color: <?php echo $employee_info['Color']['tab_selected']; ?>;
      }
      .wd-item-v li.wd-current, .wd-item-v li:hover{
        background-color: <?php echo $employee_info['Color']['tab_selected']; ?>;
      }
   <?php } ?>
</style>
<!-- END Custom color for Company -->