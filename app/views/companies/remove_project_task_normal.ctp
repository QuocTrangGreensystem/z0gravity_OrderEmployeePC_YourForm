<style>
	.wd-content select{
		width: 100%;
		height: 440px;
	}
	#btnSave{
		margin-top: 20px;
	}
	.alert-text{
		display: inline-block;
		margin-left: 20px;
	}
	.progress-line{
		width: 100%;
		height: 10px;
		border-radius: 5px;
		background-color: #888;
		position: relative;
		display: block;
		margin-top: 10px;
	}
	.progress-line .progress-done{
		position: absolute;
		width: 0%;
		height: 10px;
		border-radius: 5px;
		background-color: green;
		display: block;
		left: 0;
		top: 0;
		-webkit-transition: all 0.3s ease 0s;
		-moz-transition: all 0.3s ease 0s;
		-ms-transition: all 0.3s ease 0s;
		-o-transition: all 0.3s ease 0s;
		transition: all 0.3s ease 0s;
	}
	.list_companies{
		position: relative;
	}
	.list_companies.loading:after{
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
	.list_companies.loading:before{
		content: '';
		position: fixed;
		width: 100%;
		height: 100%;
		background-color: rgba(255, 255, 255, 0.6);
		left: 0;
		top: 0;
		z-index: 2;
	}
</style>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">

        <div class="wd-main-content">
            <div class="wd-list-project">
                <?php
                $options = Configure::read('App.modules');
                App::import("vendor", "str_utility");
                $str_utility = new str_utility();
                //debug()
                ?>
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
							<p> Have <?php echo count($listActivityTasksLinked);?> NCT tasks consumed=0, workload=0 in  <?php echo count($listNameProjects);?> projects</p>
							<?php echo $this->Form->create('Company', array(
							'type' => 'POST',
							'class' => 'list_companies',
							'url' => array("controller" => "recycle_bins", "action" => "deleteOldNormalTask"))); ?>
								  <select id = "company-option" name = "company_id[]" multiple>
									<?php 
										foreach($listNameProjects as $id => $name){
											?>
												<option value= "<?php echo $id; ?>">
													<?php echo $name; ?>
												</option>
											<?php 
										}
									?>
								  </select>
								<button type="submit" class="wd-button-f wd-save-project" id="btnSave">
									<span><?php __('Delete') ?></span>
								</button>
								<div class="alert-text" style="color: red; font-size: 16px;"></div>
								<div class="progress-line">
									<span class="progress-done"></span>
								</div>
							<?php echo $this->Form->end(); ?>
                            </div>
								
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
<?php
	$listIdProject = array();
	// $listId = array();
	$i = 0;
	$n = 0;
	foreach($listNameProjects as $id => $name){
		$listIdProject[$i][$id] = $name;
		$n++;
		if($n == 10) {
			$i++;
			$n = 0;
		}
		// $listId[] = $id;
	}
	// ob_clean();
	// debug($listIdProject);
	// exit;
?>
<script>
	var project = <?php echo json_encode($listIdProject) ?>;
   $('#CompanyRemoveProjectTaskNormalForm').on('submit', function(e){
		e.preventDefault();
		if (confirm('<?php echo __("Are you sure delete all normal task with Workload = 0 and Consumed = 0 ?", true); ?>') == false){
			return;
		};
		$(this).addClass('loading');
		_form = $(this);
		// var _data = $('#company-option').val();
		var _data = project;
		// _data = (_data) ? _data : project;
		
		// console.log(_data[0]);return;
		
		var _url = $(this).attr('action');
		var n = 0;
		if(_data){
			progress = 100 / _data.length;
			width = 0;
			// console.log(_url);return;
			if( _data.length){
				_delete_project_task( _data, _url, n);
			}
		}
		
		return;
	});
	function _delete_project_task( _data, _url, n){
		if( _data.length > n){
			$.ajax({
				type: 'POST',
				url: _url,
				data: _data[n],
				dataType: 'json',
				cache: false,
				success: function(){
					 // _form.find('.alert-text').empty().append(project[_data[n]] +' deleted');
					 // $("#company-option option[value="+ _data[n] +"]").remove();
					  n++;
					  width = (n)*progress; 
					 _form.find('.progress-done').css('width', width + '%');
					 
					 if( _data.length > n){
						_delete_project_task( _data, _url, n);
					 }else{	
						$('.list_companies').removeClass('loading');
						location.reload();
					 }
				}
			});
		}
	}
</script>