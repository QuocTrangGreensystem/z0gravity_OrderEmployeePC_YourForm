<?php
echo $this->Html->css(array(
    'slick_grid/slick.grid.activity',
    'jquery.multiSelect',
    'projects',
    'jquery.fancybox',
    'dropzone.min',
    'slick_grid/slick.grid_v2',
    'slick_grid/slick.pager',
    'slick_grid/slick.common_v2',
    'slick_grid/slick.edit',
    '/js/qtip/jquery.qtip',
    'dashboard/jqx.base',
    'dashboard/jqx.web',
    'preview/project_finance_index_plus'
));
echo $this->Html->script(array(
    'jquery.multiSelect',
    'slick_grid/lib/jquery-ui-1.8.16.custom.min',
    'slick_grid/lib/jquery.event.drop-2.0.min',
    'slick_grid/lib/jquery.event.drag-2.2',
    'slick_grid/slick.core',
    'slick_grid/slick.dataview',
    'slick_grid/controls/slick.pager',
    'slick_grid/slick.formatters',
	'progresspie/jquery-progresspiesvg-min',
    'history_filter',
    'slick_grid/plugins/slick.cellrangedecorator',
    'slick_grid/plugins/slick.cellrangeselector',
    'slick_grid/plugins/slick.cellselectionmodel',
    'slick_grid/plugins/slick.rowselectionmodel',
    'slick_grid/plugins/slick.rowmovemanager',
    'slick_grid/slick.editors',
    'slick_grid/slick.grid',
    'slick_grid_custom',
    'slick_grid/slick.grid.activity',
    'jquery.ui.touch-punch.min',
    'qtip/jquery.qtip',
    'draw-progress',
    'slick_grid/plugins/slick.dataexporter',
    'dropzone.min',
    'jquery.fancybox.pack',
));
?>

<style>
.wd-project-admin .wd-layout > .wd-main-content > .wd-tab > .wd-panel{
	padding: 0;
	background-color: transparent;
	max-width: 1200px;
	padding: 0 40px;
	margin-left: auto;
	margin-right: auto;
}
</style>
<?php
echo $this->element('dialog_projects');
$viewEuro = $bg_currency;
$current_widget = array();
$user_canModified =  (!empty($canModified) || ($_isProfile && $_canWrite));
$msg_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
			<defs>
				<style>
				  .cls-1 {
					fill: #666;
					fill-rule: evenodd;
				  }
				</style>
			  </defs>
			  <path id="Z0gMSG" class="cls-1" d="M683.124,30h-6.249a0.625,0.625,0,1,0,0,1.25h6.249A0.625,0.625,0,1,0,683.124,30ZM680,20c-5.523,0-10,3.918-10,8.75a8.375,8.375,0,0,0,3.75,6.824V40l5.12-2.56c0.371,0.036.747,0.059,1.13,0.059,5.523,0,10-3.917,10-8.749S685.523,20,680,20Zm0,16.25c-1.435,0-1.25,0-1.25,0L675,38.125V34.864a7.213,7.213,0,0,1-3.751-6.114c0-4.142,3.918-7.5,8.751-7.5s8.749,3.358,8.749,7.5S684.832,36.25,680,36.25Zm4.374-10h-8.749a0.625,0.625,0,1,0,0,1.25h8.749A0.625,0.625,0,1,0,684.374,26.25Z" transform="translate(-670 -20)"></path>
		</svg>';
$define_colors = array(
	'grey' => '#666',
	'blue' => '#217FC2', // Blue
	'red' => '#E94754', // Red
	'green' => '#6EAF79', // Green
);

function template_finance_budget($_this, $totals = null, $bg_currency = '$', $user_canModified = false, $type = 'inv', $_domain){
	if( empty($totals)) return;
		ob_start();
		switch($type){
			case 'fon':
				$title = __d(sprintf($_domain, 'Budget_Operation'), "Budget Operation", null);break;
			case 'finaninv':
				$title =  __d(sprintf($_domain, 'Finance_Investment'), "Finance Investment", null);break;
			case 'finanfon':
				$title = __d(sprintf($_domain, 'Finance_Operation'), "Finance Operation", null);break;
			default: $title = __d(sprintf($_domain, 'Budget_Investment'), "Budget Investment", null);break;
		}
		
		?>
		<div id="<?php echo $type ?>-chard" class="chart">
			<div class="chart-inner">
				<a href="javascript:void(0);" id="" class="on-image-expand-btn btn btn-frame on-image-expand-btn btn-<?php echo $type ?>"></a>
				<a href="javascript:void(0);" class='collapse btn-<?php echo $type ?> wd-hide' title="<?php __('Collapse',true); ?>"></a>
				<div class="chard-content clearfix">
						<?php
							if($type == 'inv'){
								$current_widget = 'Budget_Investment';
							}elseif($type == 'fon'){
								$current_widget = 'Budget_Operation';
							}elseif($type == 'finaninv'){
								$current_widget = 'Finance_Investment';
							}else{
								$current_widget = 'Finance_Operation';
							}
							if(empty($totals['budget'])){
								$totals['budget'] = 0;
							}
							if(empty($totals['avancement'])){
								$totals['avancement'] = 0;
							}
							if($totals['budget'] == 0) {
								$per = 100;
							} else {
								$per = round($totals['avancement']/$totals['budget'] * 100,2);
							}
							$color_min = '#13FF02';
							$color_max = '#15830D';
							if( $totals['budget'] == 0 && $totals['avancement'] == 0 ){
								$width_bud = '0%';
								$width_avan = '0';
								$bg_color = 'green';
								$per = 0;
							} else if( $totals['budget'] == 0 ){
								$width_bud = '0%';
								$width_avan = '80';
								$bg_color = 'green';
							} else if( (($totals['avancement'] > $totals['budget']) && $totals['avancement'] > 0) || (($totals['avancement'] > 0) && ($totals['budget'] <= 0)) ){
								$color_min = '#F98E8E';
								$color_max = '#FF0606';
								$bg_color = 'red';
								$width_bud = '80%';
								$width_avan = (abs($totals['avancement'])/abs($totals['budget'])*80);
							} else {
								$width_bud = '80%';
								$width_avan = (abs($totals['avancement'])/abs($totals['budget'])*80);
								$bg_color = 'green';
							}
							$width_avan = $width_avan <= 100 ? $width_avan : 100;
							$width_avan = $width_avan . '%';
						?>
					 
					 <aside class="budget-progress-circle" style="overflow:visible;">
						<div class="progress-circle progress-circle-yellow">
							<div class="progress-circle-inner">
								<?php $color = ($per > 100) ? '#DB414F' : '#75AF7E';?>
								<div data-val = "<?php echo $per; ?>" id="myCanvas-<?php echo $type ?>" data-color="<?php echo $color;?>" style="width: 140px;" class="canvas-circle"></div>
							</div>
						</div>
					</aside>
					<div class="progress-values">
						<h3 class="wd-t1"><?php echo $title ?></h3>
						<div class ="progress-value progress-validated"><p><?php echo __d(sprintf($_domain, $current_widget), 'Budget', true);?></p><span><?php echo number_format((!empty($totals['budget']) ? $totals['budget'] : 0), 2, '.', ' '); ?> <?php echo ' '.$bg_currency; ?> </span></div>
						<div class ="progress-value progress-engaged"><p><?php echo __d(sprintf($_domain, $current_widget),'Engaged', true); ?></p><span><?php echo number_format((!empty($totals['avancement']) ? $totals['avancement'] : 0), 2, '.', ' '); ?> <?php echo ' '.$bg_currency; ?> </span></div>
					</div>
					<div id="table-control" style="display: none;">
						<?php
						echo $_this->Form->create($type, array(
							'type' => 'get',
							'url' => '/' . Router::normalize($_this->here)));
						echo $_this->Form->hidden($type.'_full');
						?>
						<fieldset>
							<label for="<?php echo $type ?>Start"><?php __('From') ?></label>
							<div class="input" >
								<?php
									/* #1098 user readonly khong the select date */
									echo $_this->Form->input($type .'_start', array(
										'label' => false, 
										'div' => false, 
										'rel' => 'no-history',  
										"class" => "placeholder", 
										"placeholder" => __("(dd-mm-yyyy)", true), 
										'onchange' => "validated('". $type ."');", 
										'value' => isset($totals['start']) ? date('d-m-Y', $totals['start']) : '',
										'disabled' => !$user_canModified
									));
								?>
							</div>
							<label for="<?php echo $type ?>End"> <?php __('To') ?> </label>
							<div class="input" id="wd-end-date-<?php echo $type ?>">
								<?php
									echo $_this->Form->input( $type .'_end', array('label' => false, 'div' => false, 'rel' => 'no-history', "class" => "placeholder", "placeholder" => __("(dd-mm-yyyy)", true), 'onchange' => "validated('". $type ."');", 'value' => isset($totals['end']) ? date('d-m-Y', $totals['end']) : '', 'disabled' => !$user_canModified));
								?>
							</div>
							<div class="btn chart-form-submit" id="wd-submit-<?php echo $type ?>">
								<?php if($user_canModified){?> <input type="submit" value="OK" id="sut-<?php echo $type ?>"> <?php } ?> 
								<svg fill="#6EAF79" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22px" height="36px"><path d="M 19.980469 5.9902344 A 1.0001 1.0001 0 0 0 19.292969 6.2929688 L 9 16.585938 L 5.7070312 13.292969 A 1.0001 1.0001 0 1 0 4.2929688 14.707031 L 8.2929688 18.707031 A 1.0001 1.0001 0 0 0 9.7070312 18.707031 L 20.707031 7.7070312 A 1.0001 1.0001 0 0 0 19.980469 5.9902344 z"/></svg>
							</div>
							<p style="display: none; clear: both; padding-top: 2px; color: red;" class="wd-error wd-error-<?php echo $type ?>"><?php echo __('The end date must be greater than start date', true);?></p>
							
							<div style="clear:both;"></div>
						</fieldset>
						<?php
						echo $_this->Form->end();
						?>
						<a href="javascript:void(0);" class="budget-export-excel btn btn-text export-excel-icon-all" data-type="<?php echo $type;?>" id="export-<?php echo $type ?>-submit" title="<?php __('Export Excel')?>"></a>
						
					</div>
				</div>
				<div class="wd-list-project" style="width: 100%; overflow: auto; display: none;">
					<?php if($user_canModified){ ?>
						<a href="javascript:void(0);" class="btn btn-plus-green" onclick="addNewRow('<?php echo $type ?>');" title="<?php __('Add an order') ?>">
							<span class='ver_line'></span>
							<span class='hoz_line'></span>
						</a>
					<?php } ?>
					
					<div class="wd-table" id="project_container_<?php echo $type ?>" style="width:100%; min-height:300px;">
					</div>
				</div>
			</div>
		</div>
	<?php
	return ob_get_clean();
}
?>
<style>
	<?php foreach( $define_colors as $color => $hex){?>
		.action-icon.<?php echo $color;?>_icon svg .cls-1{
			fill: <?php echo $hex;?>;
		}
	<?php } ?> 
</style>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"> <div class="wd-panel">
            <div id="message-place">
                <?php
                App::import("vendor", "str_utility");
                $str_utility = new str_utility();
                echo $this->Session->flash();
                ?>
            </div>
            <h2 class="wd-t1" style="margin-bottom: 10px; color: orange"><?php echo $projectName ?></h2>
            <div class="budget-row clearfix">
            <div id = "budget-chard-container" class="budget-chard-container clearfix">
                <div class="budget-chard-container-column">
					<div class="budget-chard-container-column-inner">
						<?php echo template_finance_budget($this, $totals['inv'], $bg_currency, $user_canModified, 'inv', $_domain); 
						echo template_finance_budget($this, $totals['finaninv'], $bg_currency, $user_canModified, 'finaninv', $_domain); 
						?>
					</div>
                </div>
                <div class="budget-chard-container-column">
					<div class="budget-chard-container-column-inner">
						<?php echo template_finance_budget($this, $totals['fon'], $bg_currency, $user_canModified, 'fon', $_domain); 
						echo template_finance_budget($this, $totals['finanfon'], $bg_currency, $user_canModified, 'finanfon', $_domain);
						?>
					</div>
				</div>
				
            </div>
            
             </div></div>
         </div>
        </div>
        <div style="clear:both;"></div>
    </div>

</div>
<div id="template_finance_upload" class="template_finance_upload" style="display:none;">
	<div class="template_finance_upload-inner">
		<div class="loading-mark wd-popup-container">
			<div class="heading">
				<h4><?php echo __('File upload(s)', true)?></h4>
				<span class="close-popup"><img title="close"  src="<?php echo $html->url('/img/new-icon/close.png'); ?>"/></span>
			</div>
			<div class="attachments-container">
				<div class="list_attachments"></div>
			</div> 
			<div class="wd-popup">
				<?php 
				echo $this->Form->create('Upload', array(
					'type' => 'POST',
					'class' => 'upload-form',
					'url' => array('controller' => 'project_finances_preview','action' => 'upload_document')));
					?>
					<div class="trigger-upload">
						<div id="upload-popup" method="post" action="<?php echo $this->Html->url(array('controller' => 'project_finances_preview','action' => 'upload_document'));?>" class="dropzone" value="" > </div>
					</div>
					<?php 
					echo $this->Form->input('url', array(
						'class' => 'not_save_history',
						'label' => array(
							'class' => 'label-has-sub',
							'text' =>__('URL Link',true),
							'data-text' => __('(optionnel)', true),
							),
						'type' => 'text',
						'id' => 'newDocURL',  
						'required' => true,
						'placeholder' => __('https://', true)
					));
					echo $this->Form->input('f_id', array(
						'type' => 'hidden',
						'rel' => 'no-history',
						'id' => 'UploadId',
						'value' => '',
					));
					echo $this->Form->input('project_id', array(
						'type' => 'hidden',
						'rel' => 'no-history',
						'id' => 'UploadProjectId',
						'value' => $projects['Project']['id'],
					));
					echo $this->Form->input('isInV', array(
						'type' => 'hidden',
						'rel' => 'no-history',
						'id' => 'UploadIsInv',
						'value' => 0,
					));
					?>
					<div class="wd-submit">
						<button type="submit" class="btn-form-action btn-ok btn-right" id="btnSave">
							<span><?php __('Upload') ?></span>
						</button>
						<a class="btn-form-action cancel" id="reset_button" href="javascript:void(0);">
							<?php echo __("Cancel", true); ?></span>
						</a>
					</div>
				<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>
<div id="template_logs" style="height: 420px; width: 320px;display: none;">
	<div class="comment-container-inner loading-mark">
		<div class="add-comment"></div>
		<div class="content_comment" style="min-height: 50px">
			<div class="append-comment"></div>
		</div>
    </div>
    
</div>
<?php
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
$types = array('inv', 'fon', 'finaninv', 'finanfon');
$columns_inv = array();
$columns_fon = array();
$trans_widget_expand = array();
foreach($types as $type){
	if($type == 'inv'){
		$trans_widget_expand = 'Budget_Investment';
	}elseif($type == 'fon'){
		$trans_widget_expand = 'Budget_Operation';
	}elseif($type == 'finaninv'){
		$trans_widget_expand = 'Finance_Investment';
	}elseif($type == 'finanfon'){
		$trans_widget_expand = 'Finance_Operation';
	}
	$columns_type = array(
		array(
			'id' => $type.'_no.',
			'field' => $type.'_no.',
			'name' => '',
			'width' => 40,
			'sortable' => false,
			'resizable' => false,
			'noFilter' => 1
		),
		array(
			'id' => $type.'_name',
			'field' => $type.'_name',
			'name' => "",
			'width' => 120,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'editor' => 'Slick.Editors.textBox'
		),
		array(
			'id' => $type.'_date',
			'field' => $type.'_date',
			'name' => __d(sprintf($_domain, $trans_widget_expand), 'Date', true),
			'width' => 100,
			'sortable' => false,
			'resizable' => true,
			'editor' => 'Slick.Editors.datePicker',
			'datatype' => 'datetime',
			'formatter' => 'Slick.Formatters.DateTime',
		),
		array(
			'id' => $type.'_budget',
			'field' => $type.'_budget',
			'name' => __d(sprintf($_domain, $trans_widget_expand), 'Budget', true),
			'width' => 120,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'formatter' => 'Slick.Formatters.manDayValue'
		),
		array(
			'id' => $type.'_avancement',
			'field' => $type.'_avancement',
			'name' => __d(sprintf($_domain, $trans_widget_expand), 'Avancement', true),
			'width' => 120,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'formatter' => 'Slick.Formatters.manDayValue'
		),
		array(
			'id' => $type.'_percent',
			'field' => $type.'_percent',
			'name' => __('%', true),
			'width' => 80,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'formatter' => 'Slick.Formatters.percentValue'
		)
	);
	$array_name = 'columns_'.$type;
	$$array_name =  $columns_type;
	
}


$columnInvYears = array();
$columnFonYears = array();

$columns_inv_export = array();
$columns_fon_export = array();

foreach($types as $type){
	if($type == 'inv'){
		$history_col = 'columnWidth1';
		$trans_widget_expand = 'Budget_Investment';
	}else if($type == 'fon'){
		$history_col = 'columnWidth2';
		$trans_widget_expand = 'Budget_Operation';
	}else if($type == 'finaninv'){
		$history_col = 'columnWidth3';
		$trans_widget_expand = 'Finance_Investment';
	}elseif($type == 'finanfon'){
		$history_col = 'columnWidth4';
		$trans_widget_expand = 'Finance_Operation';
	}
	$columnsTypeYears = array(
		array(
			'id' => $type.'_no.',
			'field' => $type.'_no.',
			'name' => '',
			'width' => 40,
			'sortable' => false,
			'resizable' => false,
			'noFilter' => 1
		),
		array(
			'id' => $type.'_name',
			'field' => $type.'_name',
			'name' => "",
			'width' => isset($history[$history_col][$type.'_name']) ? (int) $history[$history_col][$type.'_name'] : 120,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'editor' => 'Slick.Editors.textBox'
		),
		array(
			'id' => $type.'_date',
			'field' => $type.'_date',
			'name' => __d(sprintf($_domain, $trans_widget_expand), 'Date', true),
			'width' => 100,
			'sortable' => false,
			'resizable' => true,
			'editor' => 'Slick.Editors.datePicker',
			'datatype' => 'datetime',
			'formatter' => 'Slick.Formatters.DateTime',
		),
		array(
			'id' => $type.'_budget',
			'field' => $type.'_budget',
			'name' => __d(sprintf($_domain, $trans_widget_expand), 'Budget', true),
			'width' => isset($history[$history_col][$type.'_budget']) ? (int) $history[$history_col][$type.'_budget'] : 120,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'formatter' => 'Slick.Formatters.manDayValue'
		),
		array(
			'id' => $type.'_avancement',
			'field' => $type.'_avancement',
			'name' => __d(sprintf($_domain, $trans_widget_expand), 'Avancement', true),
			'width' => isset($history[$history_col][$type.'_avancement']) ? (int) $history[$history_col][$type.'_avancement'] : 120,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'formatter' => 'Slick.Formatters.manDayValue'
		),
		array(
			'id' => $type.'_percent',
			'field' => $type.'_percent',
			'name' => __('%', true),
			'width' => isset($history[$history_col][$type.'_percent']) ? (int) $history[$history_col][$type.'_percent'] : 80,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'formatter' => 'Slick.Formatters.percentValue'
		)
	);
	//ticket #736. Update by QuanNV 07/09/2020
	if($type == 'inv'){
		if(isset($displayDateFields['date_budget_inv']) && ($displayDateFields['date_budget_inv']=='0')){
			unset( $columnsTypeYears[2]);
		}
	}else if($type == 'fon'){
		if(isset($displayDateFields['date_budget_ope']) && ($displayDateFields['date_budget_ope']=='0')){
			unset( $columnsTypeYears[2]);
		}
	}else if($type == 'finaninv'){
		if(isset($displayDateFields['date_finan_inv']) && ($displayDateFields['date_finan_inv']=='0')){
			unset( $columnsTypeYears[2]);
		}
	}elseif($type == 'finanfon'){
		if(isset($displayDateFields['date_finan_ope']) && ($displayDateFields['date_finan_ope']=='0')){
			unset( $columnsTypeYears[2]);
		}
	}
	//end ticket #736
	$column_export = $columnsTypeYears;
	$start = date('Y',$totals[$type]['start']);
	$end = date('Y',$totals[$type]['end']);
	while($start <= $end){
		 $columnsTypeYears[] = array(
			'id' => $type.'_budget_' . $start,
			'field' => $type.'_budget_' . $start,
			'name' => __d(sprintf($_domain, $trans_widget_expand), 'Budget', true),
			'width' => isset($history[$history_col][$type.'_budget_' . $start]) ? (int) $history[$history_col][$type.'_budget_' . $start] : 120,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'editor' => 'Slick.Editors.numericValue',
			'validator' => 'DataValidation.numericValue',
			'formatter' => 'Slick.Formatters.manDayValue'
		);
		$column_export [] = array(
			'id' => $type.'_budget_' . $start,
			'field' => $type.'_budget_' . $start,
			'name' => __d(sprintf($_domain, $trans_widget_expand), 'Budget', true) .' '. $start,
		);
		$columnsTypeYears[] = array(
			'id' => $type.'_avancement_' . $start,
			'field' => $type.'_avancement_' . $start,
			'name' => __d(sprintf($_domain, $trans_widget_expand), 'Avancement', true),
			'width' => isset($history[$history_col][$type.'_avancement_' . $start]) ? (int) $history[$history_col][$type.'_avancement_' . $start] : 120,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'editor' => 'Slick.Editors.numericValue',
			'validator' => 'DataValidation.numericValue',
			'formatter' => 'Slick.Formatters.manDayValue'
		);
		$column_export [] = array(
			'id' => $type.'_avancement_' . $start,
			'field' => $type.'_avancement_' . $start,
			'name' => __d(sprintf($_domain, $trans_widget_expand), 'Avancement', true) . ' '. $start,
		);
		$columnsTypeYears[] = array(
			'id' => $type.'_percent_' . $start,
			'field' => $type.'_percent_' . $start,
			'name' => __('%', true),
			'width' => isset($history[$history_col][$type.'_percent_' . $start]) ? (int) $history[$history_col][$type.'_percent_' . $start] : 80,
			'sortable' => false,
			'resizable' => true,
			'noFilter' => 1,
			'formatter' => 'Slick.Formatters.percentValue'
		);
		$column_export [] = array(
			'id' => $type.'_percent_' . $start,
			'field' => $type.'_percent_' . $start,
			'name' => __('%', true). ' '. $start,
		);
		$start++;
	}
	/*define array columns named: 
		columns_inv_export
		columns_fon_export
		columns_finaninv_export
		columns_finanfon_export
	*/
	$columns_export = 'columns_'.$type.'_export';
		
	/*define array columns named: 
		columns_inv_years
		columns_fon_years
		columns_finaninv_years
		columns_finanfon_years
	*/
	$columns_years = 'columns_'.$type.'_years';

	$$columns_export = $column_export;
	$$columns_years = array_merge($columnsTypeYears);
	
}

$columns_inv_big = array();
$columns_fon_big = array();

foreach($types as $type){
	$typeAttachmentComment = array();
	$typeAttachmentComment[] = array(
		'id' => $type.'_attachment',
		'field' => $type.'_attachment',
		'name' => '',
		'width' => 40,
		'sortable' => true,
		'resizable' => false,
		'formatter' => 'Slick.Formatters.contextAttachment'
	);
	$typeAttachmentComment[] = array(
		'id' => $type.'_comment',
		'field' => $type.'_comment',
		'name' => '',
		'width' => 80,
		'sortable' => true,
		'resizable' => false,
		'formatter' => 'Slick.Formatters.contextComment'
	);
	$columnTypeAction = array();
	$columnTypeAction[] = array(
		'id' => $type.'_action.',
		'field' => $type.'_action.',
		'name' => __('Action', true),
		'width' => 0,
		'sortable' => false,
		'resizable' => false,
		'noFilter' => 1,
		'cssClass' => 'row_action_del',
		'formatter' => 'Slick.Formatters.Action'
	);
	
	$column_type_big_name = 'columns_'.$type.'_big';
	$column_type_name = 'columns_'.$type;
	$columns_years = 'columns_'.$type.'_years';
	
	$$column_type_big_name = array_merge($$columns_years, $typeAttachmentComment, $columnTypeAction);
	$$column_type_name = array_merge($$column_type_name , $columnTypeAction);
	
}

$i = 1;
foreach($types as $type){
	$dataView = $dataView_big = $totalHeader = $calPercent = array();
	if(!empty($finances[$type])){
		foreach($finances[$type] as $id => $finance){
			$data = array(
				'id' => $id,
				$type.'_no.' => $i++,
				'MetaData' => array()
			);
			$data['project_id'] = $projects['Project']['id'];
			$data['activity_id'] = $projects['Project']['activity_id'];
			$data['company_id'] = $projects['Project']['company_id'];
			$data[$type.'_name'] = (string) $finance['name'];
			$data[$type.'_date'] = (string) $finance['finance_date'];
			$totalBudget = $totalAvancement = $totalPercent = 0;
			$percentYears = array();
			if(!empty($financeDetails[$id])){
				foreach($financeDetails[$id] as $model => $finanDetail){
					if(!isset($totalHeader[$type.'_' . $model])){
						$totalHeader[$type.'_' . $model] = 0;
					}
					$totalHeader[$type.'_' . $model] += $finanDetail['value'];
					$data[$type.'_' . $model] = $finanDetail['value'];
					if(!isset($percentYears[$finanDetail['year']][$finanDetail['model']])){
						$percentYears[$finanDetail['year']][$finanDetail['model']] = 0;
					}
					$percentYears[$finanDetail['year']][$finanDetail['model']] += $finanDetail['value'];
					if(!isset($calPercent[$finanDetail['year']][$finanDetail['model']])){
						$calPercent[$finanDetail['year']][$finanDetail['model']] = 0;
					}
					$calPercent[$finanDetail['year']][$finanDetail['model']] += $finanDetail['value'];
					if($finanDetail['model'] == 'budget'){
						$totalBudget += $finanDetail['value'];
					} else {
						$totalAvancement += $finanDetail['value'];
					}
				}
			}
			if(!empty($percentYears)){
				foreach($percentYears as $year => $percentYear){
					$bud = !empty($percentYear['budget']) ? $percentYear['budget'] : 0;
					$ava = !empty($percentYear['avancement']) ? $percentYear['avancement'] : 0;
					$per = ($bud ==0 ) ? 0 : round($ava/$bud*100, 2);
					$data[$type.'_percent_' . $year] = $per;
				}
			}
			$totalPercent = ($totalBudget == 0) ? 0 : $totalAvancement/$totalBudget*100;
			if(!isset($totalHeader[$type.'_budget'])){
				$totalHeader[$type.'_budget'] = 0;
			}
			$totalHeader[$type.'_budget'] += $totalBudget;

			if(!isset($totalHeader[$type.'_avancement'])){
				$totalHeader[$type.'_avancement'] = 0;
			}
			$totalHeader[$type.'_avancement'] += $totalAvancement;
			if(!isset($calPercent['total']['budget'])){
				$calPercent['total']['budget'] = 0;
			}
			$calPercent['total']['budget'] += $totalBudget;
			if(!isset($calPercent['total']['avancement'])){
				$calPercent['total']['avancement'] = 0;
			}
			$calPercent['total']['avancement'] += $totalAvancement;

			$data[$type.'_percent'] = round($totalPercent, 2);
			// $data[$type.'_budget'] = round($totalBudget, 2);
			// $data[$type.'_avancement'] = round($totalAvancement, 2);
			$data[$type.'_comment'] = (in_array($id, $list_comments)) ? 1 : 0; // Chỗ này chỉ xác định có comment hay không chứ khong đếm số comment;
			$data['comment_read_status'] = !empty($read_comments[$id]) ? 1 : 0;
			$data[$type.'_attachment'] = (in_array($id, $list_attachments)) ? 1 : 0;
			$data['attach_read_status'] = !empty($read_attachments[$id]) ? 1 : 0;
			$data[$type.'_action.'] = '';

			$dataView[] = $data;

		}
		
		if(!empty($calPercent)){
			foreach($calPercent as $key => $val){
				$bud = isset($val['budget']) ? $val['budget'] : 0 ;
				$ava = isset($val['avancement']) ? $val['avancement'] : 0;
				$per = ($bud ==0 ) ? 0 : round($ava/$bud*100, 2);
				if($key == 'total'){
					$totalHeader[$type.'_percent'] = $per;
				} else {
					$totalHeader[$type.'_percent_' . $key] = $per;
				}
			}
		}
		
	}
	$data_view_name = 'data_view_'.$type;
	$total_header_name = 'total_header_'.$type;
	$cal_percent_name = 'cal_percent_'.$type;
	
	$$data_view_name = $dataView;
	$$total_header_name = $totalHeader;
	$$cal_percent_name = $calPercent;
	
}

foreach($types as $type){
	$header_consumed_right = '<div class="slick-header-columns">';
	$high_light = '.l2, .l3, .l4';
	$field_new_name = 'field_new_'.$type;
	$$field_new_name = array(
		'project_id' => $projects['Project']['id'],
		'activity_id' => $projects['Project']['activity_id'],
		'company_id' => $projects['Project']['company_id'],
		$type.'_name' => '',
		$type.'_date' => '',
		$type.'_percent' => '',
		$type.'_budget' => '',
		$type.'_avancement' => '',
	);
	
	$fields_name = 'fields_'.$type;
	$$fields_name = array(
		'id' => array('defaulValue' => 0),
		'project_id' => array('defaulValue' => $projects['Project']['id'], 'allowEmpty' => false),
		'activity_id' => array('defaulValue' => $projects['Project']['activity_id']),
		'company_id' => array('defaulValue' => $projects['Project']['company_id'], 'allowEmpty' => false),
		$type.'_name' => array('defaulValue' => '', 'allowEmpty' => false),
		$type.'_date' => array('defaulValue' => '', 'allowEmpty' => true)
	);

	$leftType = 6; $rightType = 6; $countType = 1;
	$typeStart = date('Y', $totals[$type]['start']);
	$typeEnd = date('Y', $totals[$type]['end']);
	$ex_field_new = array();
	$ex_fields_name = array();
	
	while($typeStart <= $typeEnd){
		$ex_fields_name[$type.'_budget_' . $typeStart] = array('defaulValue' => '');
		$ex_fields_name[$type.'_avancement_' . $typeStart] =  array('defaulValue' => '');
		$ex_field_new[$type.'_budget_' . $typeStart] = '';
		$ex_field_new[$type.'_avancement_' . $typeStart] = '';
		$ex_field_new[$type.'_percent_' . $typeStart] = '';

		$header_consumed_right .= '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'. $leftType++ .' r'. $rightType++ .'"><span>'.$typeStart.'</span></div>';
		$header_consumed_right .= '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'. $leftType++ .' r'. $rightType++ .'"></div>';
		$header_consumed_right .= '<div class="slick-headerrow-column gs-custom-cell-euro-header border-euro-custom l'. $leftType++ .' r'. $rightType++ .'"></div>';
		if($countType%2 == 0){
			$_l = $leftType;
			$high_light .= ', .l' . ($_l-3);
			$high_light .= ', .l' . ($_l-2);
			$high_light .= ', .l' . ($_l-1);
		}
		$countType++;
		$typeStart++;
	}
	$header_consumed_right_type = 'header_consumed_right_'.$type;
	$$header_consumed_right_type = $header_consumed_right;
	
	$high_light_type = 'high_light_'.$type;
	$$high_light_type = $high_light;
	
	$$field_new_name = array_merge($$field_new_name, $ex_field_new);
	$$fields_name = array_merge($$fields_name, $ex_fields_name);
	

}

$selectMaps = array();
$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true),
    'Date closing must between %1$s and %2$s' => __('Date closing must between %1$s and %2$s', true)
);
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete_finance', '%1$s', '%2$s', '?' => array('fon_start' => date('d-m-Y', $fonStart), 'fon_end' => date('d-m-Y', $fonEnd), 'inv_start' => date('d-m-Y', $invStart), 'inv_end' => date('d-m-Y', $invEnd)))); ?>&open=%4$s">Delete</a>
        </div>
    </div>
</div>
<script type="text/javascript">
	var type_full = <?php echo json_encode($type_full); ?>;
    function settable_height(_current_table){
		if(_current_table.length ===  0) return;
        var wdTable = _current_table.find('.wd-list-project');
        var heightTable = $(window).height() - wdTable.offset().top - 60;
        if(heightTable > 300){
            wdTable.css({
                height: heightTable,
            });
        }else{
            wdTable.css('height','');
        }
        $(window).resize(function(){
            heightTable = $(window).height() - wdTable.offset().top - 60;
            if(heightTable > 300){
                wdTable.css({
                    height: heightTable,
                });
            }else{
                wdTable.css('height','');
            }
        });
    }
    var DateValidate = {},dataGrid, ControlGridOne, ControlGridTwo, ControlGridThree, ControlGridFour, IuploadComplete = function(json){
        var data = dataGrid.eval('currentEditor');
        data.onComplete(json);
    };
	var msg_icon = <?php echo json_encode($msg_icon);?>;
	var project_id = <?php echo json_encode($projects['Project']['id']);?>;
	var canModified =  <?php echo json_encode($user_canModified); ?>;
	var isTablet  =  <?php echo json_encode($isTablet ); ?>;
	var isMobile  =  <?php echo json_encode($isMobile ); ?>;
	var api_key = <?php echo json_encode($employee_info['Employee']['api_key']); ?>;
	var att_link = <?php echo json_encode($this->Html->url(array(
		'controller' => 'project_finances_preview',
		'action' => 'attachment',
		'%ID%',
		'?' => array(
			'sid' => $employee_info['Employee']['api_key'],
		)
	))); ?>;
	var exporter_inv, exporter_fon;
	if( !canModified) $('.upload-form').hide();
	Dropzone.autoDiscover = false;
    $(function() {
		var dropzone_tag = "#upload-popup";
		var form = $(dropzone_tag).closest('form');
		var popup = $('#template_finance_upload');
		var myDropzone = new Dropzone(dropzone_tag, {
			acceptedFiles: ".jpg,.jpeg,.bmp,.gif,.png,.txt,.doc,.xls,.pdf,.docx,.xlsx,.ppt,.pps,.pptx,.csv,.xlsm,.msg",
			imageSrc: "/img/new-icon/draganddrop.png",
			dictDefaultMessage: "<?php __('Drag & Drop your document or browse your folders');?>",
			addRemoveLinks: true,  
			maxFiles: 1,
			dictRemoveFile: '<?php __('Remove file');?>',
		});
        // myDropzone.on("queuecomplete", function(file) {
			
        // });
        myDropzone.on("success", function(file) {
            myDropzone.removeFile(file);
			data = JSON.parse(file.xhr.responseText);
			var is_inv = $('#UploadIsInv').val();
			if (data['results'] == 'success') {
				data = data.data;
				draw_list_att(popup, data, is_inv);
			}
			form.closest('.loading-mark').removeClass('loading');
        });
        form.on('submit', function(e){
            form.closest('.loading-mark').addClass('loading');
            // return;
            if(myDropzone.files.length){
                e.preventDefault();
                myDropzone.processQueue();
            }
			else{
				e.preventDefault();
				var is_inv = $('#UploadIsInv').val();
				$.ajax({
					url: form.prop('action'),
					type: 'POST',
					dataType: 'json',
					data: form.serialize(),
					success: function(data) {
						data = data.data;
						draw_list_att(popup, data, is_inv);
					},
					complete: function(){
						form.closest('.loading-mark').removeClass('loading');
					}
					
				});
			}
        });
        myDropzone.on('sending', function(file, xhr, formData) {
			form.closest('.loading-mark').addClass('loading');
            // Append all form inputs to the formData Dropzone will POST
            var data = form.serializeArray();
            $.each(data, function(key, el) {
                formData.append(el.name, el.value);
            });
        });
    });
	function draw_comment_update_grid(popup, data, is_inv){
		var id = data.id;
		var _html = '';
		var index = 0;
		var has_comment  = 0;
		var title = ('title' in data) ? data['title'] : '';
		if( title) popup.dialog('option',{title: title })
		if (data) {
			if( canModified) _html += '<div class="comment"><textarea data-id = '+ id +'  cols="30" rows="6" data-is_inv="' + is_inv + '" id="update-comment"></textarea></div>';
			_html += '<div class="content-logs' + ( !canModified ? ' no-comment' : '') + '"><div class="content-logs-inner">';
			if( data.result){
				$.each(data.result, function(ind, _data) {
					_cm = _data.ProjectFinancePlusTxt;
					if(_cm && ('id' in Object(_cm)) ){
						var name = ava_src = time = '';
						if( _cm.is_ws_comment != 0){
							name = 'Web Services';
							ava_src = 'WS';
						}else{
							name = _data.Employee.first_name + ' ' + _data.Employee.last_name;
							ava_src += '<img width = 35 height = 35 src="'+  employeeAvatar_link.replace('%ID%',_cm['employee_id'] ) +'" title = "'+ name +'" />';
						}
						comment = _cm['comment'] ? _cm['comment'].replace(/\n/g, "<br>") : '';
						var date = _cm.text_time;
						_html += '<div class="content content-'+ index++ +'"><div class="avatar"><span class="circle-name">'+ ava_src +'</span></div><div class="item-content"><p>'+ name + ' ' + date +'</p><div class="comment">'+ comment +'</div></div></div>';
						has_comment++;
					}                       
				});
			}
			_html += '</div></div>';
		}
		popup.find('.content_comment:first').html(_html);

		/* Update to grid */
		
		args = {
			comment_read_status: 1
		};
		if( is_inv){
			args.inv_comment = has_comment;
		}else{
			args.fon_comment = has_comment;
		}
		var ControlGrid = is_inv ? ControlGridOne : ControlGridTwo;
		var dataView = ControlGrid.getData();
		var actCell = ( ControlGrid.getActiveCell() ) ?( ControlGrid.getActiveCell().cell ) : 0;
		dataView.beginUpdate();
		var _new_data = dataView.getItems();
		$.each( _new_data, function( ind, item){
			if( item.id == id){
				$.each( args, function( key, val){
					item[key] = val;
				});
			}
			_new_data[ind] = item;
		});
		dataView.setItems(_new_data);
		dataView.endUpdate();
		ControlGrid.invalidate();
		ControlGrid.render();
		var actRow = ControlGrid.getData().getRowById(id);
		ControlGrid.gotoCell(actRow, actCell, false);
		/* End Update to grid */
	};
	function draw_comment_update_grid_no_ws_avt(popup, data, is_inv){
		var id = data.id;
		var _html = '';
		var index = 0;
		var has_comment  = 0;
		var title = ('title' in data) ? data['title'] : '';
		if( title) popup.dialog('option',{title: title })
		if (data) {
			if( canModified) _html += '<div class="comment"><textarea data-id = '+ id +'  cols="30" rows="6" data-is_inv="' + is_inv + '" id="update-comment"></textarea></div>';
			_html += '<div class="content-logs' + ( !canModified ? ' no-comment' : '') + '"><div class="content-logs-inner">';
			if( data.result){
				$.each(data.result, function(ind, _data) {
					_cm = _data.ProjectFinancePlusTxt;
					if(_cm && ('id' in Object(_cm)) ){
						var name = ava_src = time = '';
						comment = _cm['comment'] ? _cm['comment'].replace(/\n/g, "<br>") : '';
						var date = _cm.text_time;
						if( _cm.is_ws_comment != 0){
							_html += '<div class="content content-'+ index++ +'"><div class="avatar"><span class="no-circle-name"></span></div><div class="item-content"><p>' + date +'</p><div class="comment">'+ comment +'</div></div></div>';
						}else{
							name = _data.Employee.first_name + ' ' + _data.Employee.last_name;
							ava_src += '<img width = 35 height = 35 src="'+  employeeAvatar_link.replace('%ID%',_cm['employee_id'] ) +'" title = "'+ name +'" />';
							_html += '<div class="content content-'+ index++ +'"><div class="avatar"><span class="circle-name">'+ ava_src +'</span></div><div class="item-content"><p>'+ name + ' ' + date +'</p><div class="comment">'+ comment +'</div></div></div>';
						}
						has_comment++;
					}                       
				});
			}
			_html += '</div></div>';
		}
		popup.find('.content_comment:first').html(_html);

		/* Update to grid */
		
		args = {
			comment_read_status: 1
		};
		if( is_inv){
			args.inv_comment = has_comment;
		}else{
			args.fon_comment = has_comment;
		}
		var ControlGrid = is_inv ? ControlGridOne : ControlGridTwo;
		var dataView = ControlGrid.getData();
		var actCell = ( ControlGrid.getActiveCell() ) ?( ControlGrid.getActiveCell().cell ) : 0;
		dataView.beginUpdate();
		var _new_data = dataView.getItems();
		$.each( _new_data, function( ind, item){
			if( item.id == id){
				$.each( args, function( key, val){
					item[key] = val;
				});
			}
			_new_data[ind] = item;
		});
		dataView.setItems(_new_data);
		dataView.endUpdate();
		ControlGrid.invalidate();
		ControlGrid.render();
		var actRow = ControlGrid.getData().getRowById(id);
		ControlGrid.gotoCell(actRow, actCell, false);
		/* End Update to grid */
	};
	function showPopupTaskComment(elm){
		var _this = $(elm);
        var project = project_id;
        var id = _this.data('id');
		var is_inv = _this.data('is_inv');
        var _html = '';
        var latest_update = '';
		var index = 0;
		var has_comment  = 0;
        var popup = $('#template_logs');
		
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
		popup.find( '.content_comment').empty();
		popup.dialog('option',{title: '' }).dialog('open');
		popup.find('.loading-mark').addClass('loading');
        $.ajax({
            url: '/project_finances_preview/getCommentTxt/' + id ,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                draw_comment_update_grid(popup, data.data, is_inv);
            },
			complete: function(){
				popup.find('.loading-mark').removeClass('loading');
			}
        });
	}
	$('body').on("focusout", "#update-comment", function () {
		var _this = $(this);
        var id = _this.data("id");
		var is_inv = _this.data('is_inv');
		var text = _this.val();
		if ( text == '') return;
        var popup = $('#template_logs');
		popup.find('.loading-mark').addClass('loading');
		var comment_cont = popup.find('.content-logs-inner');
		var _html = '';
        $.ajax({
            url: '/project_finances_preview/update_text/' + id ,
            type: 'POST',
            data: {
				data: {
					id: id,
					text: text
				}
            },
            dataType: 'json',
            success: function(data) {
				if( data){
					draw_comment_update_grid(popup, data.data, is_inv);
				}
			},
			complete: function(){
				popup.find('.loading-mark').removeClass('loading');
			}
        });
    });
	
	function deleteAttachmentFile(){
		var AttachmentId = $(this).closest('a').data('id');
		var fid = $(this).closest('a').data('fid');
		var itemPic = $(this).closest('li');
		var attachment_cont = itemPic.closest('ul');
		$.ajax({
			url: '<?php echo $this->Html->url('/project_finances_preview/delete_attachment/') ?>' + AttachmentId,
			success: function (data) {
				itemPic.slideUp(300, function(){
					itemPic.remove();
				});
			}
		})
	}
	function draw_list_att(popup, data, is_inv){
		var _html = '<ul>';
		var _has_file = 0;
		var f_id = data['id'];
		$.each(data['attachments'], function(ind, _data) {
			if( _data) _has_file++;
			var att_id = _data['ProjectFinancePlusAttachment']['id'];
			var att_name = _data['ProjectFinancePlusAttachment']['attachment'];
			var is_file = _data['ProjectFinancePlusAttachment']['is_file'];
			if( is_file == 1){
				if((/\.(gif|jpg|jpeg|tiff|png)$/i).test(att_name)){ 
					_html += '<li><i class="icon-paper-clip"></i><a href="' + att_link.replace('%ID%', att_id) + '" class="file-name image fancy" rel="finance_gallery_' + att_id + '" data-id = "'+ att_id +'">'+ att_name +'</span><a href="javascript:void(0);" class="delete" data-id = "'+ att_id +'" data-fid="' + f_id + '"><img src="/img/new-icon/delete-attachment.png" alt="'+ att_id +'" onclick="deleteAttachmentFile.call(this)"></a></li>';
				}else{
					_link = '/project_finances_preview/attachment/'+ att_id +'/?download=1&sid='+ api_key;
					_html += '<li><i class="icon-paper-clip"></i><a class="file-name"  href="'+ _link +'">'+ att_name +'</a><a href="javascript:void(0);" class="delete" data-id = "'+ att_id +'" data-fid="' + f_id + '"><img src="/img/new-icon/delete-attachment.png" alt="'+ att_id +'" onclick="deleteAttachmentFile.call(this)"></a></li>';
				}
			}else{
				_html += '<li><i class="icon-link"></i><a class="file-name" target="_blank" href="'+ att_name +'">'+ att_name +'</a><a href="javascript:void(0);" class="delete" data-id = "'+ att_id +'" data-fid="' + f_id + '"><img src="/img/new-icon/delete-attachment.png" alt="'+ att_id +'" onclick="deleteAttachmentFile.call(this)"></a></li>';
			}
		});
		_html += '</ul>';
		popup.find('.list_attachments').html(_html);
		
		/* Update to grid */
		args = {
			attach_read_status: 1
		};
		if( is_inv){
			args.inv_attachment = _has_file;
		}else{
			args.fon_attachment = _has_file;
		}
		var ControlGrid = is_inv ? ControlGridOne : ControlGridTwo;
		var dataView = ControlGrid.getData();
		var actCell = ( ControlGrid.getActiveCell() ) ?( ControlGrid.getActiveCell().cell ) : 0;
		dataView.beginUpdate();
		var _new_data = dataView.getItems();
		$.each( _new_data, function( ind, item){
			if( item.id == f_id){
				$.each( args, function( key, val){
					item[key] = val;
				});
			}
			_new_data[ind] = item;
		});
		dataView.setItems(_new_data);
		dataView.endUpdate();
		ControlGrid.invalidate();
		ControlGrid.render();
		var actRow = ControlGrid.getData().getRowById(f_id);
		ControlGrid.gotoCell(actRow, actCell, false);
		/* END Update to grid */
	}
	$('.fancy.image').fancybox({
		type: 'image'
	});
	function openAttachmentDialog(elm){
		var _this = $(elm);
		var id = _this.data("id");
		var is_inv = _this.data('is_inv');
		$('#UploadIsInv').val(is_inv);
		$('#UploadId').val(id);
		// $('#UploadProjectId').val(project_id);
		var popup = $('#template_finance_upload');
		var _has_file = 0;
		popup.find('.loading-mark').addClass('loading');
		popup.fadeIn(300);
		$.ajax({
			url: '/project_finances_preview/getAttachments/'+ id,
			type: 'GET',
			dataType: 'json',
			success: function(data) { 
				if (data['results'] == 'success') {
					data = data.data;
					draw_list_att(popup, data, is_inv);
				}
			},
			complete: function(){
				popup.find('.loading-mark').removeClass('loading');
			}
		});
	}
	
	$("#template_finance_upload").on( 'click', '.close-popup, .cancel', function (e) {
        $("#template_finance_upload").fadeOut(300);
    });
    (function($){
        $(function(){
            var $this = SlickGridCustom,
            headerConsumedRightInv = '<div class="slick-header-columns">',
            headerConsumedRightFon = '<div class="slick-header-columns">',
            highLightInv = '.l2, .l3, .l4',
            highLightFon = '.l2, .l3, .l4',
            invStart = <?php echo json_encode(date('Y', $invStart));?>,
            invEnd = <?php echo json_encode(date('Y', $invEnd));?>,
            fonStart = <?php echo json_encode(date('Y', $fonStart));?>,
            fonEnd = <?php echo json_encode(date('Y', $fonEnd));?>,
            projects = <?php echo !empty($projects['Project']) ? json_encode($projects['Project']) : json_encode(array());?>,
            viewEuro = <?php echo json_encode($viewEuro);?>,
            total_header_inv = <?php echo json_encode($total_header_inv);?>,
            total_header_fon = <?php echo json_encode($total_header_fon);?>,
            total_header_finaninv = <?php echo json_encode($total_header_finaninv);?>,
            total_header_finanfon = <?php echo json_encode($total_header_finanfon);?>,
            totalName = <?php echo json_encode(__d(sprintf($_domain, 'Finance'), 'Total', true));?>;

            projects['activity_id'] = projects['activity_id'] ? projects['activity_id'] : 0;
            $this.canModified =  <?php echo json_encode($user_canModified); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;

            var _history = <?php echo empty($history) ? '{}' : json_encode($history) ?>;
            var historyData = new $.z0.data(_history);
            var historyPath = <?php echo json_encode($this->params['url']['url']) ?>;
            var fields_inv = <?php echo json_encode($fields_inv) ?>;
            var fields_fon = <?php echo json_encode($fields_fon) ?>;
            var fields_finaninv = <?php echo json_encode($fields_finaninv) ?>;
            var fields_finanfon = <?php echo json_encode($fields_finanfon) ?>;
			$this.isExpand = false;
			$this.isResizing = 0;
			
            var actionTemplate =  $('#action-template').html();
            function resizeHandler(type){
				var _controlGrid = ControlGridOne;
				flag = 1;
				if(type == 'inv'){
					_controlGrid = ControlGridOne;
				}else if(type == 'fon'){
					_controlGrid = ControlGridTwo;
					flag = 2;
				}else if(type == 'finaninv'){
					_controlGrid = ControlGridThree;
					flag = 3;
				}else{
					_controlGrid = ControlGridFour;
					flag = 4;
				}
                var _cols = _controlGrid.getColumns();
                var _numCols = _cols.length;
                var _gridW = 0;
                var columnWidth = {};
                for (var i=0; i<_numCols; i++) {
                    _gridW += _cols[i].width;
                    columnWidth[_cols[i].id] = _cols[i].width;
                }
                $('.slick-header-columns').css('width', _gridW);
                historyData.set('columnWidth'+flag, columnWidth);
                // call save here
                // **
				if( $this.isExpand){
					saveFilter();
				}
            }
           
            var saveTimer;
            function saveFilter(){
                clearTimeout(saveTimer);
                saveTimer = setTimeout(function(){
                    $.z0.History.save(historyPath, historyData);
                }, 750);
            }
			var DataValidation = {};
            $.extend(Slick.Formatters,{
                Action : function(row, cell, value, columnDef, dataContext){
					var type = columnDef['id'].substring(0, 3);
					var exp = $this.isExpand ? (type + '-expand') : '';
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,
                    dataContext.project_id, dataContext.name, exp), columnDef, dataContext);
                },
                manDayValue : function(row, cell, value, columnDef, dataContext){
                    if(value && value != 0 && typeof value !== 'undefined'){
                        value = number_format(value, 2, ',', ' ');
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + ' ' + viewEuro + '</span> ', columnDef, dataContext);
                    } else {
						console.log(value);
                        value = (value && value != 0) ? value : '';
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + '</span> ', columnDef, dataContext);
                    }
                },
                percentValue : function(row, cell, value, columnDef, dataContext){
                    if(value && value != 0){
                        var old_value = (value <= 100 ? value : 100);
                        value = number_format(value, 2, ',', ' ');
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + ' %' + '</span> <span class="row-percent" data-value="'+old_value +'" style="width: '+ old_value +'%"></span>', columnDef, dataContext);
                    } else {
                        value = (value && value != 0) ? value : '';
                        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + '</span> ', columnDef, dataContext);
                    }
                },
				contextAttachment: function(row, cell, value, columnDef, dataContext){
					var has_att = value;
					var has_read = dataContext.attach_read_status;
					var src = '/img/new-icon/drop-icon.png';
					var is_inv = ('inv_name' in dataContext) ? 1 : 0;
					if( has_att) src = '/img/new-icon/drop-icon-red.png';
					if( has_att && has_read) src = '/img/new-icon/drop-icon-blue.png';
					context = '<a class="action-acttachment action-icon " data-id = "'+ dataContext.id +'" data-project-id = "'+ dataContext.project_id +'" data-is_inv="' + is_inv + '"  onclick="openAttachmentDialog(this)"><img src="'  + src + '"></a>';
                    return context;
				},
				contextComment: function(row, cell, value, columnDef, dataContext){
					var has_comm = value;
					var has_read = dataContext.comment_read_status;
					var _class = '';
					var is_inv = ('inv_name' in dataContext) ? 1 : 0;
					
					if( has_comm) _class = 'red_icon';
					if( has_comm && has_read) _class = 'blue_icon';
					context = '<a class="action-comment action-icon ' + _class + '" data-id = "'+ dataContext.id +'" data-project-id = "'+ dataContext.project_id +'" data-is_inv="' + is_inv + '"  onclick="showPopupTaskComment(this)">' + msg_icon + '</a>';
					
                    return context;
				},
            });

            $.extend(Slick.Editors,{
                numericValue : function(args){
                    $.extend(this, new Slick.Editors.textBox(args));
					this.input.attr('max' , 1e16);
                    this.input.attr('maxlength' , 18).keypress(function(e){
                        var key = e.keyCode ? e.keyCode : e.which;
                        var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
                        if(val == '0' || !/^[\-]?([0-9]{0,16})(\.[0-9]{0,2})?$/.test(val)){
                            e.preventDefault();
                            return false;
                        }
						
                    });
                }
            });
			DataValidation.numericValue = function(value, args){
				var limit = new BigNumber(value).toFixed(2);
				var _valid = true, 
				_msg = '';
				if(limit > 1e16){
					_msg = '<?php echo __("The value entered must be less than 1E16", true)?>';
					_valid = false;
				}
				return {
					valid : _valid,
					message : _msg
				};
			};
            var  types = <?php echo json_encode($types); ?>;
            var  data_1 = <?php echo json_encode($data_view_inv); ?>;
            var  data_2 = <?php echo json_encode($data_view_fon); ?>;
            var  data_3 = <?php echo json_encode($data_view_finaninv); ?>;
            var  data_4 = <?php echo json_encode($data_view_finanfon); ?>;
            var  columns_1 = <?php echo jsonParseOptions($columns_inv, array('editor', 'formatter', 'validator')); ?>; 
            var  columns_1_big = <?php echo jsonParseOptions($columns_inv_big, array('editor', 'formatter', 'validator')); ?>; 
            var  columns_1_export  = <?php echo jsonParseOptions($columns_inv_export , array('editor', 'formatter', 'validator')); ?>; 
            var  columns_2 = <?php echo jsonParseOptions($columns_fon, array('editor', 'formatter', 'validator')); ?>;
            var  columns_2_big = <?php echo jsonParseOptions($columns_fon_big, array('editor', 'formatter', 'validator')); ?>;
            var  columns_2_export  = <?php echo jsonParseOptions($columns_fon_export , array('editor', 'formatter', 'validator')); ?>;
            var  columns_3 = <?php echo jsonParseOptions($columns_finaninv, array('editor', 'formatter', 'validator')); ?>; 
            var  columns_3_big = <?php echo jsonParseOptions($columns_finaninv_big, array('editor', 'formatter', 'validator')); ?>;
			var  columns_3_export  = <?php echo jsonParseOptions($columns_finaninv_export , array('editor', 'formatter', 'validator')); ?>;
			var  columns_4 = <?php echo jsonParseOptions($columns_finanfon, array('editor', 'formatter', 'validator')); ?>; 
            var  columns_4_big = <?php echo jsonParseOptions($columns_finanfon_big, array('editor', 'formatter', 'validator')); ?>;
			var  columns_4_export  = <?php echo jsonParseOptions($columns_finanfon_export , array('editor', 'formatter', 'validator')); ?>;
			// console.log(columns_1_export);
            $this.onBeforeEdit = function(args){
                var columnId = args.column.id;
                if( !$this.canModified) return false;
                if(columnId){
                    columnId = columnId.split('_');
                    if(columnId[0] == 'inv'){
                        $this.url =  '<?php echo $html->url(array('controller' => 'project_finances_preview', 'action' => 'update_finance', 'inv')); ?>';
                        $this.fields = fields_inv;
                    }else if(columnId[0] == 'fon') {
                        $this.url =  '<?php echo $html->url(array('controller' => 'project_finances_preview', 'action' => 'update_finance', 'fon')); ?>';
                        $this.fields = fields_fon;
                    }else if(columnId[0] == 'finaninv'){
						$this.url =  '<?php echo $html->url(array('controller' => 'project_finances_preview', 'action' => 'update_finance', 'finaninv')); ?>';
                        $this.fields = fields_finaninv;
					}else{
						$this.url =  '<?php echo $html->url(array('controller' => 'project_finances_preview', 'action' => 'update_finance', 'finanfon')); ?>';
                        $this.fields = fields_finanfon;
					}
                } else {
                    return false;
                }
                return true;
            }
			$this.onColumnsResized = function(args){
				if( $this.isResizing) return true;
				$this.isResizing = 1;
				var grid = args.grid;
				var options = grid.getOptions();
				var column_change = {};
				// only check when expand
				if( options.forceFitColumns == false ){
					// get column with changed
					var _columns = grid.getColumns();
					$.each( _columns, function(i, column){
						// moi lan resize co the co nhieu column bi anh huong, o day chi check cho 1 column
						if (column.width != column.previousWidth) { column_change = column;}
					});
				}
				var list_column_willchange = {};
				if( column_change.width ){
					var width = column_change.width;
					var _field = column_change.field.split('_');
					if( _field[1]){
						// change column width for the columns with same name
						var price_fields = ['avancement', 'budget'];
						$.each(_columns, function(i, column){
							var field = column.field.split('_');
							if( field[1] == _field[1] || ( ($.inArray(field[1], price_fields) != -1) && ($.inArray(_field[1], price_fields) != -1) ) ){
								_columns[i].previousWidth = _columns[i].width;
								_columns[i].width = width;
							}
						});
						grid.wdSetColumns(_columns); // uppdate for Slick grid custom
						grid.setColumns(_columns);
						type = $(grid.getContainerNode()).attr('id');
						if(type){
							type = type.split('_');
							headerCalc(type[2]);
						}
					
					}
				}
				grid.eval('applyColumnHeaderWidths();updateCanvasWidth(true);');
				$this.isResizing = 0;
				return true;
			}
			function updateCanvas(type, percent){
				var canvas = 'myCanvas-'+type;
				color = (percent > 100) ? '#DB414F' : '#75AF7E';
				$('#' + canvas).attr('data-color', color);
				$('#' + canvas).data('val', percent);
				draw_pie_progress($('#' + canvas));
				setColorFromCanvas($('#' + canvas));
			}
            $this.onCellChange = function(args){
				 if(args && args.column.id && args.item){
                    var columnId = args.column.id;
                    columnId = columnId.split('_');
					columnId = columnId[0];
                    var totalBudget = new BigNumber(0), 
						totalAvan = new BigNumber(0),
						totalPercent = 0;
                    var budgetYears = {}, avanYears = {};
                    $.each(args.item, function(ind, val){
                        val = val ? val : 0;
                        ind = ind.split('_');
						if(ind.length == 3){
                            if(ind[1] == 'budget'){
                                budgetYears[ind[2]] = parseFloat(val);
                                totalBudget = totalBudget.plus(val);
                            } else if(ind[1] == 'avancement') {
                                avanYears[ind[2]] = parseFloat(val);
								totalAvan = totalAvan.plus(val);
                            }
                        }
                    });
                    totalPercent = (totalBudget == 0) ? 0 : (totalAvan.div(totalBudget).times(100)).toFixed(2);
                    if(budgetYears){
                        $.each(budgetYears, function(y, budVal){
                            var avanVal = avanYears[y] ? avanYears[y] : 0;
                            var perVal = (budVal == 0) ? 0 : avanVal/budVal*100;
                            args.item[columnId + '_percent_' + y] = parseFloat(perVal);
                        });
                    }
                    args.item[columnId + '_percent'] = totalPercent;
                    args.item[columnId + '_budget'] = totalBudget.toFixed(2);
                    args.item[columnId + '_avancement'] = totalAvan.toFixed(2);
                    /**
                     * Tinh header
                     */
					if(columnId == 'inv'){
						ControlGrid = ControlGridOne;
						_datas = $.extend(true, {}, data_1);
					}else if(columnId == 'fon'){
						ControlGrid = ControlGridTwo;
						_datas = $.extend(true, {}, data_2);
					}else if(columnId == 'finaninv'){
						ControlGrid = ControlGridThree;
						_datas = $.extend(true, {}, data_3);
					}else{
						ControlGrid = ControlGridFour;
						_datas = $.extend(true, {}, data_4);
					}
                    var _totalHeader = {}, _budgetHeader = {}, _avanHeader = {};
					$.each(_datas, function(key, _data){
                        $.each(_data, function(ind, val){
                            val = val ? val : 0;
                            var _ind = ind.split('_');
							// console.log(_ind);
							if(_ind[1] && (_ind[1] == 'budget' || _ind[1] == 'avancement')){
                                if(!_totalHeader[ind]){
                                    _totalHeader[ind] = new BigNumber(0);
                                }
                                // _totalHeader[ind] += parseFloat(val);
                                _totalHeader[ind] = _totalHeader[ind].plus(val);
                                var _key = _ind[2] ? _ind[2] : 'total';
                                if(_ind[1] == 'budget'){
                                    if(!_budgetHeader[_key]){
                                        _budgetHeader[_key] = new BigNumber(0);
										// console.log( _budgetHeader);
                                    }
                                    // _budgetHeader[_key].plus(parseFloat(val));
									// if( 'inv_budget_2022' === ind) console.log( _budgetHeader[_key].toFixed(), val, parseFloat(val));
                                    _budgetHeader[_key] = _budgetHeader[_key].plus(val);
                                } else if(_ind[1] == 'avancement') {
                                    if(!_avanHeader[_key]){
                                        _avanHeader[_key] = new BigNumber(0);
                                    }
                                    // _avanHeader[_key] += parseFloat(val);
                                    _avanHeader[_key]= _avanHeader[_key].plus(val);
                                }
								
                            }
                        });
                    });
					ele_grid = $(ControlGrid.getContainerNode());
					ele_grid.closest('.chart-inner').find('.progress-validated span').empty().html(_budgetHeader['total'].toFormat(2)+' '+viewEuro);
					ele_grid.closest('.chart-inner').find('.progress-engaged span').empty().html(_avanHeader['total'].toFormat(2)+' '+viewEuro);
					// per_total = _budgetHeader['total'] ? ((_avanHeader['total'] / _budgetHeader['total'])*100).toFixed(2) : 0;
					per_total = (_budgetHeader['total'].toFixed() !=0) ? (_avanHeader['total'].div(_budgetHeader['total']).times(100)).toFixed(2) : 0;
					
                    if(_budgetHeader){
                        $.each(_budgetHeader, function(key, budVal){
                            var avanVal = _avanHeader[key] ? _avanHeader[key] : 0;
                            var perVal = (budVal == 0) ? 0 : avanVal/budVal*100;
                            if(key == 'total'){
                                _totalHeader[columnId + '_percent'] = perVal;
                            } else {
                                _totalHeader[columnId + '_percent_' + key] = perVal;
                            }
                        });
                    }
					
                    if(_totalHeader){
                        $.each(_totalHeader , function(id){
                            var _views = id.split('_');
                            var _symbol = (_views[1] && _views[1] == 'percent') ? '%' : viewEuro;
							// console.log( this);
							var val = '';
							if( BigNumber.isBigNumber(this)){
								val = this.toFormat(2) + ' ' + _symbol;
							}else{
								val = Number(this) ? number_format(Number(this), 2, ',', ' ') + ' ' + _symbol : '';
							}
                            if($(ControlGrid.getHeaderRowColumn(id)).hasClass('row-number')){
                                $(ControlGrid.getHeaderRowColumn(id)).find('.row-number b').html(val);
                            } else {
                                $(ControlGrid.getHeaderRowColumn(id)).html('<span class="row-number"><b>' + val + '</b></span>');
                            }
                        });
                    }
                    var columns = args.grid.getColumns(),
                        col, cell = args.cell;
                    do {
                        cell++;
                        if( columns.length == cell )break;
                        col = columns[cell];
                    } while (typeof col.editor == 'undefined');

                    if( cell < columns.length ){
                        args.grid.gotoCell(args.row, cell, true);
                    }
					
					updateCanvas(columnId, per_total);
                }
                $('.row-number').parent().addClass('row-number-custom');
            }
			$.each(types, function(n, type){
				ele_container = '#project_container_'+type;
				if(type == 'inv'){
					data = data_1; columns = columns_1;
				}else if(type == 'fon'){
					data = data_2; columns = columns_2;
				}else if(type == 'finaninv'){
					data = data_3; columns = columns_3;
				}else{
					data = data_4; columns = columns_4;
				}
				
				$.each(data, function(key, value){
					data[key][type + '_percent'] = 0;
					data[key][type + '_budget'] = 0;
					data[key][type + '_avancement'] = 0;
					var totalBudget = new BigNumber(0), 
						totalAvan = new BigNumber(0),
						totalPercent = 0;
                    $.each(value, function(ind, val){
                        val = val ? val : 0;
                        ind = ind.split('_');
						if(ind.length == 3){
                            if(ind[1] == 'budget'){
                                totalBudget = totalBudget.plus(val);
                            } else if(ind[1] == 'avancement') {
								totalAvan = totalAvan.plus(val);
                            }
                        }
                    });
                    totalPercent = (totalBudget == 0) ? 0 : (totalAvan.div(totalBudget).times(100)).toFixed(2);
                  
                    data[key][type + '_percent'] = totalPercent;
                    data[key][type + '_budget'] = totalBudget.toFixed(2);
					data[key][type + '_avancement'] = totalAvan.toFixed(2);
					
				});
				ControlGrid = $this.init($(ele_container),data,columns,{
					showHeaderRow: true,
					enableAddRow : false,
					rowHeight: 40,
					enableCellNavigation: true,
					enableAddRow: true,
					forceFitColumns: true
				});
				ControlGrid.onColumnsResized.subscribe(function (e, args) {
					resizeHandler(type);
				});
				
				if(type == 'inv'){
					ControlGridOne = ControlGrid;
				}else if(type == 'fon'){
					ControlGridTwo = ControlGrid;
				}else if(type == 'finaninv'){
					ControlGridThree = ControlGrid;
				}else{
					ControlGridFour = ControlGrid;
				}
			
			});
           
            
            var _ids = 999999999999;
            addNewRow = function(type){
                if(!$this.canModified) return;
                var ControlGrid = ControlGridOne;
				var newRow = $.extend(true, {}, <?php echo json_encode($field_new_inv); ?>);
				if(type == 'fon'){
					ControlGrid = ControlGridTwo;
					newRow = $.extend(true, {}, <?php echo json_encode($field_new_fon); ?>);
				}
				if(type == 'finaninv'){
					ControlGrid = ControlGridThree;
					newRow = $.extend(true, {}, <?php echo json_encode($field_new_finaninv); ?>);
				}
				if(type == 'finanfon'){
					ControlGrid = ControlGridFour;
					newRow = $.extend(true, {}, <?php echo json_encode($field_new_finanfon); ?>);
				}
                var rowData = ControlGrid.getData().getItems();
                var _length = rowData.length;
                newRow['id'] = _ids++;
                ControlGrid.invalidateRow(_length);
                rowData.splice(_length, 0, newRow);
                ControlGrid.getData().setItems(rowData);
                ControlGrid.render();
                ControlGrid.scrollRowIntoView(_length-1, false);
                $('.row-number').parent().addClass('row-number-custom');
                ControlGrid.gotoCell(_length, 1, true);
            }
            /**
             * Add header phia duoi
             */
			 $.each(types, function(n, type){
				if(type == 'inv'){
					total_header = total_header_inv;
					_ControlGrid = ControlGridOne;
				}else if(type == 'fon'){
					total_header = total_header_fon;
					_ControlGrid = ControlGridTwo;
				}else if(type == 'finaninv'){
					total_header = total_header_finaninv;
					_ControlGrid = ControlGridThree;
				}else{
					total_header = total_header_finanfon;
					_ControlGrid = ControlGridFour;
				}
				if(total_header){
					$.each(total_header , function(id){
						var _views = id.split('_');
						var _symbol = (_views[1] && _views[1] == 'percent') ? '%' : viewEuro;
						var val = Number(this) ? number_format(Number(this), 2, ',', ' ') + ' ' + _symbol : '';
						// console.log(val);
						$(_ControlGrid.getHeaderRowColumn(id)).html('<span class="row-number"><b>' + val + '</b></span>');
					});
				}
			 });
            /**
             * add class lt
             */
            $('.row-number').parent().addClass('row-number-custom');
            $('.slick-headerrow-columns div').addClass('gs-custom-cell-euro');
            /**
             * Add header phia tren
             */
            var headerConsumedLeft =
                '<div class="slick-header-columns">'
                    + '<div class="slick-headerrow-column l0 r1 gs-custom-cell-euro-header fist-element border-euro-custom"></div>'
                    + '<div class="slick-headerrow-column l2 r5 gs-custom-cell-euro-header border-euro-custom"><span>' +totalName+ '</span></div>'
              + '</div>';
			$.each(types, function(n, type){
				_ele = '#project_container_'+ type;
				_ele_high_light =  <?php echo json_encode($high_light_inv);?>;
				_html = <?php echo json_encode($header_consumed_right_inv);?>;
				if(type == 'fon'){
					_html = <?php echo json_encode($header_consumed_right_fon);?>;
					_ele_high_light = <?php echo json_encode($high_light_fon);?>;
				}
				if(type == 'finaninv'){
					_html = <?php echo json_encode($header_consumed_right_finaninv);?>;
					_ele_high_light = <?php echo json_encode($high_light_finaninv);?>;
				}
				if(type == 'finanfon'){
					_html = <?php echo json_encode($header_consumed_right_finanfon);?>;
					_ele_high_light = <?php echo json_encode($high_light_finanfon);?>;
				}
				$(_ele).find('.slick-header-columns-right').before(_html);
				$(_ele).find('.slick-header-columns-left').before(headerConsumedLeft);
				
				$(_ele + ' .slick-header-columns').find(_ele_high_light).addClass('headerHighLight');
			});
           
            // 1111
            function hexToRgb(hex) {
                var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
                return result ? {
                    r: parseInt(result[1], 16),
                    g: parseInt(result[2], 16),
                    b: parseInt(result[3], 16)
                } : null;
            }
				/**
				 * Tinh header
				 */
			function headerCalc(columnId){
				if(columnId == 'inv'){
					ControlGrid = ControlGridOne;
					_datas = $.extend(true, {}, data_1);
				}else if(columnId == 'fon'){
					ControlGrid = ControlGridTwo;
					_datas = $.extend(true, {}, data_2);
				}else if(columnId == 'finaninv'){
					ControlGrid = ControlGridThree;
					_datas = $.extend(true, {}, data_3);
				}else{
					ControlGrid = ControlGridFour;
					console.log(23);
					_datas = $.extend(true, {}, data_4);
				}
				var _totalHeader = {}, _budgetHeader = {}, _avanHeader = {};
				$.each(_datas, function(key, _data){
                        $.each(_data, function(ind, val){
                            val = val ? val : 0;
                            var _ind = ind.split('_');
							// console.log(_ind);
							if(_ind[1] && (_ind[1] == 'budget' || _ind[1] == 'avancement')){
                                if(!_totalHeader[ind]){
                                    _totalHeader[ind] = new BigNumber(0);
                                }
                                // _totalHeader[ind] += parseFloat(val);
								// console.log(val, typeof val)
                                _totalHeader[ind] = _totalHeader[ind].plus(val);
                                var _key = _ind[2] ? _ind[2] : 'total';
                                if(_ind[1] == 'budget'){
                                    if(!_budgetHeader[_key]){
                                        _budgetHeader[_key] = new BigNumber(0);
										// console.log( _budgetHeader);
                                    }
                                    // _budgetHeader[_key].plus(parseFloat(val));
									// if( 'inv_budget_2022' === ind) console.log( _budgetHeader[_key].toFixed(), val, parseFloat(val));
                                    _budgetHeader[_key] = _budgetHeader[_key].plus(val);
                                } else if(_ind[1] == 'avancement') {
                                    if(!_avanHeader[_key]){
                                        _avanHeader[_key] = new BigNumber(0);
                                    }
                                    // _avanHeader[_key] += parseFloat(val);
                                    _avanHeader[_key]= _avanHeader[_key].plus(val);
                                }
								
                            }
                        });
                    });
				ele_grid = $(ControlGrid.getContainerNode());
				ele_grid.closest('.chart-inner').find('.progress-validated span').empty().html(_budgetHeader['total'].toFormat(2)+' '+viewEuro);
				ele_grid.closest('.chart-inner').find('.progress-engaged span').empty().html(_avanHeader['total'].toFormat(2)+' '+viewEuro);
				per_total = (_budgetHeader['total'].toFixed() !=0) ? (_avanHeader['total'].div(_budgetHeader['total']).times(100)).toFixed(2) : 0;
				if(_budgetHeader){
					// console.log(_budgetHeader);
					$.each(_budgetHeader, function(key, budVal){
						var avanVal = _avanHeader[key] ? _avanHeader[key] : 0;
						var perVal = (budVal == 0) ? 0 : avanVal/budVal*100;
						if(key == 'total'){
							_totalHeader[columnId + '_percent'] = perVal;
						} else {
							_totalHeader[columnId + '_percent_' + key] = perVal;
						}
					});
				}
				if(_totalHeader){
					// console.log(_totalHeader);
					$.each(_totalHeader , function(id){
						var _views = id.split('_');
						var _symbol = (_views[1] && _views[1] == 'percent') ? '%' : viewEuro;
						// console.log(this);
						var val = '';
						if( BigNumber.isBigNumber(this)){
							val = this.toFormat(2) + ' ' + _symbol;
						}else{
							val = Number(this) ? number_format(Number(this), 2, ',', ' ') + ' ' + _symbol : '';
						}
						if($(ControlGrid.getHeaderRowColumn(id)).hasClass('row-number')){
							$(ControlGrid.getHeaderRowColumn(id)).find('.row-number b').html(val);
						} else {
							$(ControlGrid.getHeaderRowColumn(id)).html('<span class="row-number"><b>' + val + '</b></span>');
						}
					});
				}
				$('.row-number').parent().addClass('row-number-custom');
			}
			function setColorFromCanvas(_this){
				var color = _this.attr('data-color');
				var _chart = _this.closest('.chart');
				_chart.find('.slick-pane.slick-pane-header').css('background-color',color);
				_chart.find('.btn-plus-green').css('color',color);
				_chart.find('.btn-plus-green span').css('background-color',color);
				var rgb = hexToRgb(color);
				var header_color = 'rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ', 0.3 )';
				var percent_color = 'rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ', 0.1 )';
				_chart.find('.slick-headerrow').css('background-color',header_color);
				_chart.find('.wd-table').data('chart-color',percent_color);
				_chart.find('.row-percent').css('background-color',percent_color);
				$('.row-number').parent().addClass('row-number-custom');
			}
			$('[id*="myCanvas"]').each(function(){
				var _this = $(this);
				setColorFromCanvas(_this);
			});
            $('.on-image-expand-btn').on('click', function(){
                var _this = $(this);
				var container = _this.closest('.chart');
				_this.closest('.chart').find('.wd-list-project').show();
				_this.closest('.chart').find('#table-control').show();
				var type = 'fon';
				var ControlGrid, column_big;
				if( _this.hasClass('btn-inv')){
					type = 'inv';
					ControlGrid = ControlGridOne;
					column_big = columns_1_big;
					$('#inv-chard').find('input[name="inv_full"]').val(1);
					window.history.replaceState( {} , 'updateState', ' ?inv_full=1' );
				}else if(_this.hasClass('btn-fon')){
					type = 'fon';
					ControlGrid = ControlGridTwo;
					column_big = columns_2_big;
					$('#fon-chard').find('input[name="fon_full"]').val(1);
					window.history.replaceState( {} , 'updateState', ' ?fon_full=1' );
				}else if(_this.hasClass('btn-finaninv')){
					type = 'finaninv';
					ControlGrid = ControlGridThree;
					column_big = columns_3_big;
					$('#finaninv-chard').find('input[name="finaninv_full"]').val(1);
					window.history.replaceState( {} , 'updateState', ' ?finaninv_full=1' );
				}else{
					type = 'finanfon';
					ControlGrid = ControlGridFour;
					column_big = columns_4_big;
					$('#finanfon-chard').find('input[name="finanfon_full"]').val(1);
					window.history.replaceState( {} , 'updateState', ' ?finanfon_full=1' );
				}
                var _chart = _this.closest('.chart');
                _chart.addClass('fullScreen');
				settable_height(container);
				$('.wd-tab').height(0);
                _this.hide();
                _this.next('.collapse').show();
                _chart.find('.slick-pane-right').show();
                var _pane_right_width = _chart.find('.wd-list-project').width() - _chart.find('.slick-pane.slick-pane-header.slick-pane-left').first().width();
                // settable_height(); 
				$this.isExpand = true;
				var options = ControlGrid.getOptions();
				options.frozenColumn = 5;
				options.forceFitColumns = false;
				ControlGrid.setOptions(options); // set truoc
                ControlGrid.wdSetColumns(column_big);
                ControlGrid.setColumns(column_big);				
				headerCalc( type );

            });
           
            $('.collapse').on('click', function(){
                var _this = $(this);
				_this.closest('.chart').find('.wd-list-project').hide();
				_this.closest('.chart').find('#table-control').hide();
				var type = 'fon';
				var ControlGrid, column_big;
				if( _this.hasClass('btn-inv')){
					$('#inv-chard').find('input[name="inv_full"]').val(0);
					window.history.replaceState( {} , 'updateState', ' ?inv_full=0' );
				}else{
					$('#fon-chard').find('input[name="fon_full"]').val(0);
					window.history.replaceState( {} , 'updateState', '?fon_full=0' );
				}
                var _chart = _this.closest('.chart');
                _chart.removeClass('fullScreen');
				$('.wd-tab').css('height','');
                _this.hide();
                _this.prev('.on-image-expand-btn').show();
                _chart.find('.slick-pane-right').hide();
                // settable_height();
				$this.isExpand = false;
            });
		
			$('.budget-export-excel').on('click', function(){
				var type = $(this).data('type');
				exporter = new Slick.DataExporter('/project_finances_preview/finance_export');
				console.log(exporter);
				var columns_export;
				if(type == 'inv'){
                    exportControlGrid = ControlGridOne;
					columns_export = columns_1_export;
				}else if(type == 'fon') {
				    exportControlGrid = ControlGridTwo;
					columns_export = columns_2_export;
				}else if(type == 'finaninv'){
					exportControlGrid = ControlGridThree;
					columns_export = columns_3_export;
				}else{
					exportControlGrid = ControlGridFour;
					columns_export = columns_4_export;
				}
			
				exportControlGrid.registerPlugin(exporter);
				var _columns = exportControlGrid.getColumns();
				exportControlGrid.isExporting = true;
				exportControlGrid.wdSetColumns(columns_export );
				exportControlGrid.setColumns(columns_export );
				exporter.submit();
				exportControlGrid.wdSetColumns(_columns);
				exportControlGrid.setColumns(_columns);
				exportControlGrid.isExporting = false;
				headerCalc(type);
			});
			$(window).resize(function(){
				ControlGridOne.resizeCanvas();
				ControlGridTwo.resizeCanvas();
				ControlGridThree.resizeCanvas();
				ControlGridFour.resizeCanvas();
			});
        });

    })(jQuery);
	$("#invInvStart, #invInvEnd, #fonFonStart, #fonFonEnd, #finaninvFinaninvStart, #finaninvFinaninvEnd, #finanfonFinanfonStart, #finanfonFinanfonEnd").datepicker({
		dateFormat: 'dd-mm-yy'
	});
	
    function validated(type){
        var _start = $('input[name="'+type +'_start"]').val().toString();
        _start = _start.split('-');
        var myStartDate = new Date(_start[2],_start[1],_start[0]);
        _start = Number(myStartDate);

        var _end = $('input[name="'+type +'_end"]').val().toString();
        _end = _end.split('-');
        var myEndDate = new Date(_end[2],_end[1],_end[0]);
        _end = Number(myEndDate);
    }

    (function($){
       
    })(jQuery);
    $( document ).ready(function() {
        view = <?php echo json_encode(isset($_GET["open"]) ? $_GET["open"] : '') ?>;
        if(view == 'inv-expand'){
            $('.on-image-expand-btn.btn-inv').click();
        }else if(view == 'fon-expand'){
            $('.on-image-expand-btn.btn-fon').click();
        }
		if(type_full){
			$(type_full).find('.on-image-expand-btn').trigger('click');
		}
    });
	function draw_pie_progress(pie_progress){
		var progress_width = pie_progress.width();
		pie_progress.addClass('wd-progress-pie').setupProgressPie({
			size: 124,
			strokeWidth: 8,
			ringWidth: 8,
			ringEndsRounded: true,
			strokeColor: "#f3f3f3",
			color: function(value){
				var red = 'rgba(233, 71, 84, 1)';
				var green = 'rgba(110, 175, 121, 1)';
				return pie_progress.data('val') > 100 ? red : green;
			},
			valueData: "val",
			contentPlugin: "progressDisplay",
			contentPluginOptions: {
				fontFamily: 'Open Sans',
				multiline: [
					{
						cssClass: "progresspie-progressText",
						fontSize: 11,
						textContent: ('<?php __('Progress');?>').toUpperCase(),
						color: '#ddd',
					},
					{
						cssClass: "progresspie-progressValue",
						fontSize: 28,
						textContent: '%s%' ,
						color: function(value){
							var red = 'rgba(233, 71, 84, 1)';
							var green = 'rgba(110, 175, 121, 1)';
							return pie_progress.data('val') > 100 ? red : green;
						}
					},
				],
				fontSize: 28
			},
			animate: {
				dur: "1.5s"
			}
		}).progressPie();
	}
	draw_pie_progress($('#myCanvas-inv'));
	draw_pie_progress($('#myCanvas-fon'));
	draw_pie_progress($('#myCanvas-finaninv'));
	draw_pie_progress($('#myCanvas-finanfon'));

</script>