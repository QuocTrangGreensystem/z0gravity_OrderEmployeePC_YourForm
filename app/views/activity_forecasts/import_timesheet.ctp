<?php 
    echo $html->script(array(
        'jquery.multiSelect', 
        'slick_grid/lib/jquery-ui-1.8.16.custom.min',
        'slick_grid/lib/jquery.event.drag-2.0.min',
        'slick_grid/slick.core',
        'slick_grid/slick.dataview',
        'slick_grid/controls/slick.pager',
        'slick_grid/slick.formatters',
        'slick_grid/plugins/slick.cellrangedecorator',
        'slick_grid/plugins/slick.cellrangeselector',
        'slick_grid/plugins/slick.cellselectionmodel',
        'slick_grid/slick.editors',
        'slick_grid/slick.grid',
        'slick_grid_custom'
    )); 
    echo $html->css(array(
        'jquery.multiSelect',
        'slick_grid/slick.grid',
        'slick_grid/slick.pager',
        'slick_grid/slick.common',
        'slick_grid/slick.edit',
        'jquery.ui.custom',
		'preview/tab-admin',
		'layout_admin_2019'
    ));
    echo $this->element('dialog_projects');
?>
<style>
    .wd-input{
        float: left;
        margin: 6px 0;
        overflow: hidden;
        clear: both;
        width: 100%;
    }
    .wd-input label{
        float: left;
        width: 135px;
        padding-right: 10px;
        line-height: 29px;
        text-transform: capitalize;
        text-align: left;
        font-size: 13px;
        font-weight: bold;
	}
	.wd-input input{
        float: left;
        width: 300px;
        padding: 6px 5px;
        border: 1px solid #d4d4d4;
        background-color: #fff;
	}
	.gradient {
		background: #ffffff;
		background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2ZmZmZmZiIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiNmMGYwZjAiIHN0b3Atb3BhY2l0eT0iMSIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+);
		background: -moz-linear-gradient(top, #ffffff 0%, #f0f0f0 100%);
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(100%,#f0f0f0));
		background: -webkit-linear-gradient(top, #ffffff 0%,#f0f0f0 100%);
		background: -o-linear-gradient(top, #ffffff 0%,#f0f0f0 100%);
		background: -ms-linear-gradient(top, #ffffff 0%,#f0f0f0 100%);
		background: linear-gradient(to bottom, #ffffff 0%,#f0f0f0 100%);
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#f0f0f0',GradientType=0 );
		padding: 5px 15px;
		border: 1px solid #d0d0d0;
		cursor: pointer;
		border-radius: 4px;
	}
	.gradient:hover, .gradient:focus {
		border-color: #bbb;
	}
	.buttons ul.type_buttons {
		height: auto;
    }
	.wd-list-project .wd-tab .wd-content label {
		width: 150px;
	}
	#export-start-date-inno, #export-end-date-inno {
		width: 150px;
	}
	.type_buttons {
		padding-right: 10px;
		overflow: hidden;
	}
	.type_buttons li {
		display: inline-block;
	}
	#download-sample, #import-submit {
		vertical-align: middle;
	}
</style>
<div id="wd-container-main" class="wd-project-admin">
	<?php echo $this->element("project_top_menu") ?>
	<div class="wd-layout">
		<div class="wd-main-content">
			<div class="wd-list-project">
				<?php
				App::import("vendor", "str_utility");
				$str_utility = new str_utility();
                echo $this->Session->flash();
				?>
				<div class="wd-tab">
					<?php echo $this->element("admin_sub_top_menu");?>
					<div class="wd-panel">
						<div class="wd-section" id="wd-fragment-1">
							<?php echo $this->element('administrator_left_menu') ?>
							<div class="wd-content">
                                <?php
                                    if($valid == 0){
                                ?>
								<h2 class="wd-t3"></h2>
                                <div id="dialog_import_CSV" title="Import CSV file">
                                    <?php
                                    echo $this->Form->create('Import', array('id' => 'uploadForm', 'type' => 'file', //'target' => '_blank',
                                        'url' => array('controller' => 'activity_forecasts', 'action' => 'import_csv')));
                                    ?>
                                    <div class="wd-input">
                                        <label><?php echo __('File:') ?></label>
                                        <input type="file" name="FileField[csv_file_attachment]" />
                                        <div style="clear:both; margin-left:145px; width: 220px; color: #008000; font-style:italic;">(<?php __('Allowed file type') ?>: *.csv)</div>
                                    </div>
                                    <div class="wd-input">
                        				<label for=""><?php __('Start Date') ?></label>
                        				<?php echo $this->Form->input('start_date', array(
                        					'div' => false,
                        					'id' => 'export-start-date-inno',
                        					//'class' => 'export-datepicker',
                        					'label' => false,
                                            'readonly' => true
                        				    ));
                                        ?>
                        			</div>
                        			<div class="wd-input">
                        				<label for=""><?php __('End Date') ?></label>
                        				<?php echo $this->Form->input('end_date', array(
                        					'div' => false,
                        					'id' => 'export-end-date-inno',
                        					//'class' => 'export-datepicker',
                        					'label' => false,
                                            'readonly' => true
                        				    )); 
                                        ?>
                        			</div>
                                    <div class="wd-input wd-input-80">
										<label for="budget" style=""><?php __("Automatic Validation")?></label>
										<?php echo $this->Form->input('auto_valid', array(
                                            'div' => false, 
                                            'label' => false, 
                                            'type' => 'checkbox',
                                            'style' => 'width: auto !important;vertical-align:middle;float:none;margin-top:8px;'
                                            ));
                                        ?>    
									</div>
                                    <ul class="type_buttons">
                                        
                                        <li>
                                            <button type="button" id="download-sample" class="gradient" href="#"><?php echo __('Example of import file') ?></button>
                                        </li>
                                        <li><a id="import-submit" class="new" onclick="return false;" href="#"><?php echo __('Submit') ?></a></li>
                                        <li id="error"></li>
                                    </ul>
                                    <?php echo $this->Form->end(); ?>
                                </div>
                                <?php
                                    } elseif($valid == 1){
                                ?>
                                <h2 class="wd-t3"><?php echo __("Validated Timesheet", true); ?></h2>
                                <?php
                                    echo $this->Form->create('Request', array('id' => 'validatedTimeSheet', 'url' => array('controller' => 'activity_forecasts', 'action' => 'response', 'month', 'true')));
                                    echo $this->Form->hidden('selected', array('value' => $requests));
                                    echo $this->Form->hidden('id', array('name' => 'data[id]', 'value' => serialize($listEmployees)));
                                    echo $this->Form->hidden('validated', array('name' => 'data[validated]', 'value' => 1));
                                    echo $this->Form->end();
                                ?>
                                <fieldset style="margin-top: -12px;margin-bottom: 6px;">
                                    <div class="wd-submit" style="margin: inherit !important;">
                                        <input type="submit" id="btnSave" value="" class="wd-save"/>
                                    </div>
                                </fieldset>
                                <div class="wd-table" id="project_container" style="width:100%;height:300px;">

                                </div>
                                <?php
                                    }
                                ?>
							</div>
						</div>
					</div>
				</div>
			</div>
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

$columns = array(
    array(
        'id' => 'no.',
        'field' => 'no.',
        'name' => '#',
        'width' => 40,
        'sortable' => true,
        'resizable' => false,
        'noFilter' => 1,
    ),
    array(
        'id' => 'resources',
        'field' => 'resources',
        'name' => __('Resources', true),
        'width' => 500,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1
    ),
    array(
        'id' => 'start_date',
        'field' => 'start_date',
        'name' => __('Start Date', true),
        'width' => 300,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1
    ),
    array(
        'id' => 'end_date',
        'field' => 'end_date',
        'name' => __('End Date', true),
        'width' => 300,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1
    )
);
$i = 0;
$dataView = array();
$selectMaps = array();

App::import("vendor", "str_utility");
$str_utility = new str_utility();

if(!empty($buildDatas)){
    foreach ($buildDatas as $buildData) {
        $i++;
        $data = array(
            'id' => $i,
            'no.' => $i
        );
        $data['resources'] = !empty($buildData['employee_id']) && !empty($buildEmploys) && !empty($buildEmploys[$buildData['employee_id']]) ? (string) $buildEmploys[$buildData['employee_id']] : '';
        $data['start_date'] = !empty($buildData['start']) ? (string) date('d-m-Y', $buildData['start']) : '';
        $data['end_date'] = !empty($buildData['end']) ? (string) date('d-m-Y', $buildData['end']) : '';
        $data['action.'] = '';
        $dataView[] = $data;
    }
}
$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'The code is avaiable, please enter another!' => __('The code is avaiable, please enter another!', true),
    'Clear' => __('Clear', true)
);
?>
<style>
    .ui-state-disabled .ui-state-default{
        color: silver;
    }
</style>
<script type="text/javascript">
    var DataValidator = {};
    var valid = <?php echo $valid;?>;
    if(valid == 1){
        (function($){
            $(function(){
                var $this = SlickGridCustom;
                $this.i18n = <?php echo json_encode($i18n); ?>;
                $this.canModified =  true;
                var  data = <?php echo json_encode($dataView); ?>;
                var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
                $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
                $this.init($('#project_container'),data,columns,{showHeaderRow: false});
            });
            
        })(jQuery);
    }
	$("#import-submit").click(function(){
		$(".error-message").remove();
		$("input[name='FileField[csv_file_attachment]']").removeClass("form-error");
		if($("input[name='FileField[csv_file_attachment]']").val()){
			var filename = $("input[name='FileField[csv_file_attachment]']").val();
			var valid_extensions = /(\.csv)$/i; 
            var date1 = $('#export-start-date-inno').datepicker('getDate'),
    			date2 = $('#export-end-date-inno').datepicker('getDate');
    		if( date2 < date1){
    			alert('<?php __('End date must be greater than start date') ?>');
                return false;
    		} 
			if(valid_extensions.test(filename)){ 
				$('#uploadForm').submit();
				return true;
			}
			else{
				$("input[name='FileField[csv_file_attachment]']").addClass("form-error");
				jQuery('<div>', {
					'class': 'error-message',
					text: 'Incorrect type file'
				}).appendTo('#error');
			}
		}else{
			jQuery('<div>', {
				'class': 'error-message',
				text: 'Please choose a file!'
			}).appendTo('#error');
		}
		return false;
	});
    $('#btnSave').click(function(){
        var date1 = $('#export-start-date-inno').datepicker('getDate'),
			date2 = $('#export-end-date-inno').datepicker('getDate');
		if( date2 < date1){
			alert('<?php __('End date must be greater than start date') ?>');
		} else {
		  $('#validatedTimeSheet').submit();
		}
    });
    $('#export-start-date-inno').datepicker({
		dateFormat : 'dd-mm-yy',
		showOn : 'focus',
        beforeShowDay: function(date){ 
            return [date.getDay() == 1, '']; 
        }
	});
    $('#export-end-date-inno').datepicker({
		dateFormat : 'dd-mm-yy',
		showOn : 'focus',
        beforeShowDay: function(date){ 
            return [date.getDay() == 5, '']; 
        }
	});
    $('#download-sample').click(function(){
        location.href = '<?php echo $this->Html->url('/shared/sample-timesheet.csv') ?>'
    });
</script>