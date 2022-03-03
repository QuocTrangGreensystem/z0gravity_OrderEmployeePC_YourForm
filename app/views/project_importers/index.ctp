<style type="text/css">
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

center {
	color: red;
	font-weight: 700;
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
	margin: 10px 0 0 0 !important;
}
#download-sample {
	margin-left: 10px;
}
#wd-container-main.wd-project-index .wd-layout{
	background-color: transparent;
}
</style>
<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<style>
.wd-list-project .wd-tab .wd-content label {
	width: 50px;
    margin-top: 5px;
}
</style>
<div id="wd-container-main" class="wd-project-index">
	<?php echo $this->element("project_top_menu") ?>
	<div class="wd-layout">
		<div class="wd-main-content">
			<div class="wd-list-project">
				<div class="wd-tab">
					<?php echo $this->element("admin_sub_top_menu");?>
					<div class="wd-panel">
						<div class="wd-section" id="wd-fragment-1">
							<?php echo $this->element('administrator_left_menu') ?>
							<div class="wd-content">
								<h2 class="wd-t3"><?php __('Import projects', true) ?></h2>
									<?php
									echo $this->Session->flash();
									echo $this->Form->create('Import', array('id' => 'uploadForm', 'type' => 'file',
										'url' => array('controller' => 'project_importers', 'action' => 'index')));
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
									echo $this->Form->create('Import', array('id' => 'import-form', 'url' => array('controller' => 'project_importers', 'action' => 'save_import')));
									foreach ($records as $type => $record) : ?>
										<?php $no = 1; ?>
										<div class="import-fieldset">
											<div class="import-legend">
												<?php
												switch ($type) {
													case 'Create':
														echo __('New Projects', true);
														break;
													case 'Update':
														echo __('Update projects', true);
														break;
													default:
													case 'Error':
														echo __('Error projects', true);
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
																$heighLigh= false;
																if(!empty($data['columnHighLight'])){
																	if(isset($data['columnHighLight'][$key])){
																		$heighLigh = true;
																	}
																}                                    
														 ?>
														<td class="table-col"  <?php if($heighLigh) echo 'style="border-color: red;"'; ?>  ><?php echo $data[$key]; ?></td>
														<?php } ?>
														<td class="table-info">
															<?php
															// if(!empty($data['error'])){
															//     if(!empty($data['description'])){
															//         echo  join(', ',$data['description']).' is(are) blank';
															//     }
															// }
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
																		if ( is_array($value) ) {
																			foreach($value as $key => $v){
																				echo $this->Form->hidden($type . '.' . $action . '.' . $no . '.' . $_key . '.' . $key, array('value' => $v));
																			}
																		}
																		else {
																			echo $this->Form->hidden($type . '.' . $action . '.' . $no . '.' . $_key, array('value' => $value));
																		}
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
											<div class="ui-buttons" style="margin-bottom: 4px;">
												<ul class="type_buttons">
													<?php if ($type === 'Update') : ?>
														<li><a id="submit-update-export" class="export-excel-icon-all" href="javascript:void(0)"><?php echo __('Export') ?></a></li>
														<li><a id="submit-update-do" class="new" href="javascript:void(0)"><?php echo __('Update') ?></a></li>
													<?php endif; ?>

													<?php if ($type === 'Create') : ?>
														<li><a id="submit-create-export" class="export-excel-icon-all" href="javascript:void(0)"><?php echo __('Export') ?></a></li>
														<li><a id="submit-create-do" class="new" href="javascript:void(0)"><?php echo __('Create') ?></a></li>
													<?php endif; ?>

													<?php if ($type === 'Error') : ?>
														<li><a id="submit-error-export" class="export-excel-icon-all" href="javascript:void(0)"><?php echo __('Export') ?></a></li>
													<?php endif; ?>
												</ul>
											</div>
										</div>
									<?php endforeach; ?>
									<?php
									echo $this->Form->hidden('task', array('value' => '', 'id' => 'import-task'));
									echo $this->Form->hidden('type', array('value' => '', 'id' => 'import-type'));
									echo $this->Form->end();
									?>
									<div class="wd-title">
										<a class="btn-text" id="submit-export-all" href="javascript:void(0)" style="margin-right:5px;">
											<img src="<?php echo $this->Html->url('/img/ui/blank-ok.png') ?>" alt="" />
											<span><?php __('Do all action') ?></span>
										</a>
									</div>
								<?php endif ?>
								</div>
							</div>
						</div>
					</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	(function($){
		$('#download-sample').click(function(){
			location.href = '<?php echo $this->Html->url('/shared/sample-projects.csv') ?>'
		});
		function submitForm(type,task){
			$('#import-task').val(task);
			$('#import-type').val(type);
			$('#import-form').submit();
		}
		
		$('#submit-create-export').click(function(){
			submitForm('Create','export');
		});
		$('#submit-create-do').click(function(){
			submitForm('Create','do');
		});
		
		$('#submit-update-export').click(function(){
			submitForm('Update','export');
		});
		$('#submit-update-do').click(function(){
			submitForm('Update','do');
		});
		
		$('#submit-error-export').click(function(){
			submitForm('Error','export');
		});
		$('#submit-export-all').click(function(){
			submitForm('Create,Update','do');
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
						html: '<?php __('Incorrect file type') ?>'
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