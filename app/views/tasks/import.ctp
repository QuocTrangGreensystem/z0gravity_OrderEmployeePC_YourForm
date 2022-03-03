<?php echo $html->script('jquery.dataTables'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->script('jquery.validation.min'); ?>
<?php echo $html->script('jquery.treeTable'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<!--[if gte IE 9]>
  <style type="text/css">
	.gradient {
	   filter: none;
	}
  </style>
<![endif]-->
<style>
	.wd-project-admin fieldset div.wd-input label { width: 29% !important;}
	fieldset div.wd-input div.error-message {padding-left: 31% !important;}
	#employee-place .ui-combobox, #employee-place-2 .ui-combobox{
		width: 63%;
	}
	#employee-place .ui-combobox input, #employee-place-2 .ui-combobox input{
		color: #000;
	}
fieldset div.wd-submit input.wd-save{background: url('<?php echo $this->Html->url('/img/front/bg-submit-save-new.png'); ?>') no-repeat left top !important;}
fieldset div.wd-submit input:hover{background-position:left -33px !important;}
a.wd-reset{background: url('<?php echo $this->Html->url('/img/front/bg-reload-new.png'); ?>') no-repeat left top !important;}
a.wd-reset:hover{background-position:left -33px !important;color:#000;text-decoration:none;}
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
.gradient:hover,
.gradient:focus {
	border-color: #bbb;
}
.error-message {
	margin: 10px 0 !important;
}
.error-info li {
	margin-left: 20px;
	list-style: decimal;
}

.buttons ul.type_buttons {
	padding-right: 2px!important;
}

table tbody tr:hover td {
	background: none!important;
}

.ui-dialog {
	font-size: 11px;
}

#dialog_import_CSV label {
	color: #000;
}

.type_buttons .error-message {
	background-color: #FFF;
	clear: both;
	color: #D52424;
	display: block;
	width: 212px;
	padding: 5px 0 0;
}

.form-error {
	border: 1px solid #D52424;
	color: #D52424;
}

.import-fieldset {
	border: 1px solid #D4D0C8;
	margin-bottom: 20px;
	padding: 5px;
}

.import-legend {
	font-size: 14px;
	font-weight: 700;
	padding: 5px;
}

.table-info {
	min-width: 200px;
}

.table-col {
	min-width: 200px;
	white-space: nowrap;
	overflow: hidden;
}

.display {
	min-width: 1165px;
}

.dataImport {
	overflow: auto;
	max-height: 300px;
}

.error,.error td,.error1 {
	background: #FF6F6F;
}
.buttons ul.type_buttons {
	height: 33px;
}
.buttons ul.type_buttons li a {
	text-indent: -9999px;
	height: 33px;
}
.display td,
.display th {
	vertical-align: middle;
}
.buttons ul.type_buttons {
	height: 33px;
}
.buttons ul.type_buttons li{
	float: right;
	margin-left: 0px;
	margin-top: 3px;
}
.buttons ul.type_buttons li a {
	text-indent: -9999px;
	height: 33px;
}
.buttons ul.type_buttons li a.export-excel-icon-all{
	margin-top: -5px;
}
.wd-list-project .wd-tab .wd-content label {
	width: 50px;
    margin-top: 5px;
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
				?>
				<div class="wd-tab">
					<?php echo $this->element("admin_sub_top_menu");?>
					<div class="wd-panel">
						<div class="wd-section" id="wd-fragment-1">
							<?php echo $this->element('administrator_left_menu') ?>
							<div class="wd-content">
								<h2 class="wd-t3"><?php __('Import project tasks') ?></h2>
								<?php
								echo $this->Session->flash();
								echo $this->Form->create('Import', array('id' => 'uploadForm', 'type' => 'file',
									'url' => array('controller' => 'tasks', 'action' => 'index')));
								?>
								<div class="wd-input">
									<label><?php echo __('File:') ?></label>
									<input type="file" name="FileField[csv_file_attachment]" />
									<button type="submit" id="import-submit" class="gradient" onclick="return false;" href="#"><?php echo __('Submit') ?></button>
                                    <button type="button" id="download-sample" class="gradient" href="#"><?php echo __('Example of CSV file') ?></button>
									<div style="clear:both; margin: 15px 0;color: #008000; font-style:italic;"><?php __('Allowed file type') ?>: *.csv</div>
									<div id="error"></div>
								</div>
								<?php echo $this->Form->end(); ?>

								<?php
if( isset($records) ):
									echo $this->Form->create('Import', array('id' => 'import-form', 'url' => array('controller' => 'tasks', 'action' => 'save_import')));
									?>
									<?php 

									foreach ($records as $type => $record) :
										if( $type == 'Merge')continue;
										?>
										<?php $no = 1; ?>
										<div class="import-fieldset">
											<div class="import-legend">
												<?php
												switch ($type) {
													case 'Update':
														echo __('Tasks', true);
														break;
													default:
													case 'Error':
														echo __('Error tasks', true);
														break;
												}
												?>
											</div>
											<div class="dataImport">
											<table border="0" cellspacing="1" cellpadding="3" class="display">
												<thead>

													<tr class="wd-header">
													<th class="table-no"><?php __('No.'); ?></th>
													<?php foreach($default as $key => $titleColumn){ ?>
													<th class="table-col"><?php __($key); ?></th>
													<?php } ?>
													<th class="table-info"><?php __('Info'); ?></th>
													</tr>

												</thead>

												<tbody>
													<?php foreach ($record as $data) :  ?> 
																	 
														<tr>
														<td class="table-no"><?php echo $no; ?></td>
														 <?php foreach($default as $key => $titleColumn){ 
																$hightlight= false;
																if(!empty($data['columnHighLight'])){
																	if(isset($data['columnHighLight'][$key])){
																		$hightlight = true;
																	}
																}                                    
														 ?>
														<td class="table-col"  <?php if($hightlight) echo 'style="border-color: red;"'; ?>  ><?php echo $data[$key]; ?></td>
														<?php } ?>
														<td class="table-info">
															<?php
															if(!empty($data['error'])){
																if(!empty($data['description'])){
																	echo join(', ',$data['description']).' is(are) blank';
																}
															}
															echo $this->Html->nestedList($data['error'], array('class' => 'error-info'));
															$import = array();
															
															if (!empty($data['data'])) {
																$import['do'] = $data['data'];
															}
															unset($data['error'], $data['data']);
															
														   
															$import['export'] = $data;                           
															foreach ($import as $action => $_data) {
																foreach ($_data as $_key => $value) {
																	if($_key!='description'&&$_key!='columnHighLight'){
																		if ($_key === 'function_id') {
																			foreach ($value as $_key => $_value) {
																				echo $this->Form->hidden($type . '.' . $action . '.' . $no . '.function_id.' . $_key . '.', array('value' => $_value));
																			}
																			continue;
																		}
																		
																		echo $this->Form->hidden($type . '.' . $action . '.' . $no . '.' . $_key, array('value' => $value));
																	}
																}
															}
															?>
														</td>
														</tr>
														<?php $no++; ?>
													<?php endforeach; ?>
												</tbody>
											</table>
											</div>
											<div class="ui-buttons">
													<?php if ($type === 'Update') : ?>
														<a id="submit-create-do" class="btn btn-ok" href="javascript:void(0)"></a>
														<a id="submit-create-export" class="btn btn-excel" href="javascript:void(0)"></a>
													<?php endif; ?>

													<?php if ($type === 'Error') : ?>
														<a id="submit-error-export" class="btn btn-excel" href="javascript:void(0)"></a>
													<?php endif; ?>
											</div>
										</div>
									<?php endforeach; ?>
									<?php
									echo $this->Form->hidden('task', array('value' => '', 'id' => 'import-task'));
									echo $this->Form->hidden('type', array('value' => '', 'id' => 'import-type'));
									echo $this->Form->hidden('projects', array('value' => implode(',', $projects)));
									echo $this->Form->hidden('merges', array('value' => json_encode($merges) ));
									echo $this->Form->hidden('mergedTasks', array('value' => $mergedTasks ));
									echo $this->Form->end();
									?>
									<div class="wd-title">
										<a class="btn-text" id="submit-export-all" href="javascript:void(0)" style="margin-right:5px; float: right;">
											<img src="<?php echo $this->Html->url('/img/ui/blank-ok.png') ?>" alt="" />
											<span><?php __('Do all') ?></span>
										</a>
									</div>
									<!-- End table -->
<?php endif ?>
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
	(function($){
		$('#download-sample').click(function(){
	        location.href = '<?php echo $this->Html->url('/shared/sample-tasks.csv') ?>'
	    });
		function submitForm(type,task){
			$('#import-task').val(task);
			$('#import-type').val(type);
			$('#import-form').submit();
		}
        
        $('#submit-create-export').click(function(){
            submitForm('Update','export');
        });
        
        $('#submit-create-do').click(function(){
            submitForm('Create','do');
        });
		
		$('#submit-error-export').click(function(){
			submitForm('Error','export');
		});
		$('#submit-export-all').click(function(){
			submitForm('Update','do');
		});
		$("#import-submit").click(function(){
            $(".error-message").remove();
    		$("input[name='FileField[csv_file_attachment]']").removeClass("form-error");
    		if($("input[name='FileField[csv_file_attachment]']").val()){
    			var filename = $("input[name='FileField[csv_file_attachment]']").val();
    			var valid_extensions = /(\.csv)$/i;   
    			if(valid_extensions.test(filename)){ 
    				$('#uploadForm').submit();
    				return true;
    			}
    			else{
    				$("input[name='FileField[csv_file_attachment]']").addClass("form-error");
    				jQuery('<div>', {
    					'class': 'error-message',
    					html: '<?php __('Incorrect type file') ?>'
                    }).appendTo('#error');
                }
            }else{
                jQuery('<div>', {
                    'class': 'error-message',
                    html: '<?php __('Please choose a file!') ?>'
                }).appendTo('#error');
    		}
    		return false;
    	});
		
	})(jQuery);
</script>