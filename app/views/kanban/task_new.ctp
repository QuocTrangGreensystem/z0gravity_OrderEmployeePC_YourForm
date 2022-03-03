<?php
App::import("vendor", "str_utility");
$str_utility = new str_utility();
?>
<?php
	// pm template
    $template_status = '<select name= "data[task_status_id]">';
    foreach ($projectStatus as $statu_id => $statu_name) {
    	$class = ($task_status_id == $statu_id) ? 'selected' : '';
    	$template_status .= '<option '. $class .' value='.$statu_id.'>' . $statu_name .'</option>';
    }
	$template_status .= '</select>';

	// phase template
    $template_phase = '<select name= "data[project_planed_phase_id]">';
    foreach ($projectPhases as $phase_id => $phase_name) {
    	$class = (!empty($this->data['project_planed_phase_id']) && $this->data['project_planed_phase_id'] == $phase_id) ? 'selected' : '';
    	$template_phase .= '<option '. $class .' value='.$phase_id.'>' . $phase_name .'</option>';
    }
	$template_phase .= '</select>';
	// projectPhases
    $maps = array(
        'project_name' => array(
            'label' => __d(sprintf($_domain, 'Details'), "Project Name", true),
            'html' => $this->Form->input('project_name', array('div' => false, 'label' => false, 'style' => '', 'class' => 'project-name', 'disabled' => 'disabled'))
        ),
        'project_planed_phase_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Project phase', true),
            'html' => $template_phase
        ),
        'task_title' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Task title', true),
            'html' => $this->Form->input('task_title', array('type' => 'textarea','div' => false, 'label' => false, 'style' => ''))
        ),
        'task_start_date' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Task start date', true),
            'html' => $this->Form->input('task_start_date', array('div' => false,
                'label' => false,
                'type' => 'text', 'class' => 'wd-date'
            )),
        ),
        'task_end_date' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Task end date', true),
            'html' => $this->Form->input('task_end_date', array('div' => false,
                'label' => false,
                'type' => 'text', 'class' => 'wd-date'
            )),
        ),
    );
	$maps2 = array(
        'Priority' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Priority', true),
            'html' => $this->Form->input('task_priority_id', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Status' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Status', true),
            // 'html' => $this->Form->input('task_status_id', array('div' => false, 'label' => false, 'style' => ''))
            'html' => $template_status
        ),
        'Milestone' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Milestone', true),
            'html' => $this->Form->input('milestone_id', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Profile' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Profile', true),
            'html' => $this->Form->input('profile_id', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Duration' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Duration', true),
            'html' => $this->Form->input('duration', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Predecessor' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Predecessor', true),
            'html' => $this->Form->input('predecessor', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Workload' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Workload', true),
            'html' => $this->Form->input('estimated', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Overload' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Overload', true),
            'html' => $this->Form->input('overload', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'ManualOverload' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'ManualOverload', true),
            'html' => $this->Form->input('manual_overload', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Consumed' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Consumed', true),
            'html' => $this->Form->input('consumed', array('div' => false, 'label' => false, 'style' => '')),
            'unit' => '<span class="unit">J.H</span>',
        ),
        'ManualConsumed' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'ManualConsumed', true),
            'html' => $this->Form->input('manual_consumed', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'InUsed' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'InUsed', true),
            'html' => $this->Form->input('wait', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Completed' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Completed', true),
            'html' => $this->Form->input('completed', array('div' => false, 'label' => false, 'style' => '')),
            'unit' => '<span class="unit">%</span>',
        ),
        'Remain' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Remain', true),
            'html' => $this->Form->input('remain', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Initialworkload' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Initialworkload', true),
            'html' => $this->Form->input('initial_estimated', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Initialstartdate' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Initialstartdate', true),
            'html' => $this->Form->input('initial_task_start_date', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Amount€' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Amount€', true),
            'html' => $this->Form->input('amount', array('div' => false, 'label' => false, 'style' => ''))
        ),
        '%progressorder' => array(
            'label' => __d(sprintf($_domain, 'Details'), '%progressorder', true),
            'html' => $this->Form->input('progress_order', array('div' => false, 'label' => false, 'style' => ''))
        ),
        '%progressorder€' => array(
            'label' => __d(sprintf($_domain, 'Details'), '%progressorder€', true),
            'html' => $this->Form->input('progress_order_amount', array('div' => false, 'label' => false, 'style' => ''))
        ),
        '+/-' => array(
            'label' => __d(sprintf($_domain, 'Details'), '+/-', true),
            'html' => $this->Form->input('slider', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'UnitPrice' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'UnitPrice', true),
            'html' => $this->Form->input('unit_price', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Consumed€' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Consumed€', true),
            'html' => $this->Form->input('consumed_euro', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Remain€' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Remain€', true),
            'html' => $this->Form->input('remain_euro', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Workload€' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Workload€', true),
            'html' => $this->Form->input('workload_euro', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Estimated€' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Estimated€', true),
            'html' => $this->Form->input('estimated_euro', array('div' => false, 'label' => false, 'style' => ''))
        )
    );

?>
<div class ='wd-edit-popup'>
	<div class="wd-popup-inner">
		<div class="task-column task-attachment">
			<div class="task-field-assign">
				<div class="wd-task-item">
					<div class="wd-task-assign">
						<h4>Membre(s)</h4>
	                    <ul class="list-assign">
	                      
	                    </ul>
	                    <div id="popup-assign">
	                        <a onclick="getAssignEmployee.call(this);" title ="Assign to"><i class='icon-plus'></i></a>
	                        <div class="popup-content"></div>
	                    </div>
	                </div>
                </div>
	        </div>
	        <div id='content_comment'>
				<div class="task-field-upload">
					<div class="content-attachment"><h4>Document(s)</h4>
	                	<div class="trigger-upload"><form id="upload-file" method="post" action="/kanban/update_document" class="dropzone" value="" >
                            <input type="hidden" name="data[Upload][id]" rel="no-history" value="" id="UploadId">
                        </form></div>
	                </div>
				</div>
			</div>
		</div>
		<div class="task-column task-comment">
			<div id="template_logs">
    			<div id="content_comment">
    				<div id="content-comment-detail">
    					<?php if(!empty($projectTaskTxt)){
    						foreach ($projectTaskTxt as $key => $value) {
    							if(!empty($value['ProjectTaskTxt']['employee_id'])){
    							$employee_info = $projectTaskTxt['employee_info'][$value['ProjectTaskTxt']['employee_id']]; 
    						 ?>
	    					<div class="content-tast-text">
	    						<div class="content">
	    							<div class="content-employee">
	    								<img class="circle-name" src="<?php echo $this->UserFile->avatar($value['ProjectTaskTxt']['employee_id']); ?>">
	    								<div class="employee-info"><p><?php echo $employee_info['first_name'] .' '. $employee_info['last_name']; ?></p><p><?php echo $value['ProjectTaskTxt']['created']; ?></p></div>
	    							</div>
	    							<div class="comment"><?php echo $value['ProjectTaskTxt']['comment']; ?></div>
	    						</div>
	    					</div>
    					<?php } } } ?>
    				</div>
    			</div>
    		</div>
    		<div class="add-comment"><div class="input-add"><textarea class="text-textarea" id="update-comment"></textarea><button onclick="updateTaskText.call(this)" class="submit-btn-msg" type="button"><img src="/img/new-icon/icon-add.png" alt=""></button></div></div>
		</div>
		<div class="task-column task-field">
            <?php echo $this->Form->create('Project', array('type' => 'POST', 'id' => 'form_add_task', 'class' =>'fr-submit', 'url' => array('controller' => 'kanban', 'action' => 'createTaskJson/'. $this->data['project_id']))); ?>
			<div class="task-field-default">
				<?php
				$fieldNameDefault = array('project_name', 'project_planed_phase_id', 'task_title', 'task_start_date','task_end_date');
				foreach($fieldNameDefault as $data){
				    $fieldName = $data;
				    ?>
		                <?php echo !empty($maps[$fieldName]['html']) ? $maps[$fieldName]['html'] : ''; ?>
				<?php } ?>
	        </div>
			<div class="task-field-option">
				<?php
				 foreach($columns_order as $field){
				 	$field = explode('|', $field);
				 	if($field[1] == 1){
				 		echo !empty($maps2[$field[0]]['html']) ? '<div class ="option-field">' : '';
				 		echo !empty($maps2[$field[0]]['html']) ? $maps2[$field[0]]['html'] : '';
				 		echo !empty($maps2[$field[0]]['unit']) ? $maps2[$field[0]]['unit'] : '';
				 		echo !empty($maps2[$field[0]]['html']) ? '</div>' : '';
				 	}
				} ?>


			</div>
            <?php echo $this->Form->end(); ?>
		</div>
	</div>
	<div class="wd-popup-action">
		<?php if(!empty($this->data['task_end_date'])) {
            $taskCurrentDate = abs(strtotime(date('Y-m-d', time())) - strtotime($this->data['task_start_date']));
            $taskDate = abs(strtotime($this->data['task_end_date']) - strtotime($this->data['task_start_date']));
            $initDate = 100;
            if($taskCurrentDate <= $taskDate) $initDate = floor(($taskCurrentDate / $taskDate) * 100);
            $late_day = strtotime(date('Y-m-d', time())) - strtotime($this->data['task_end_date']);
            $class = ($late_day > 0) ? 'late-day' : '';

		?>
		<div class ="task-progress">
			<div class="project-progress <?php echo ($initDate > 50) ? 'late-progress' : ''; ?>">
	            <p class="progress-full"><span class="progress-content" style="width: <?php echo $initDate; ?>%"></span></p><p><?php echo $initDate; ?>%</p>
	        </div>
        </div>
        <?php } ?>
        <div class="buton-action">
            <a class="btn-submit" href="#">enregistrer</a>
        </div>
	</div>
</div>
<script type="text/javascript">

    $(function(){
        $("#task_start_date, #task_end_date").datepicker({
            showOn          : 'button',
            buttonImage     : '<?php echo $html->url("../../img/new-icon/date.png") ?>',
            buttonImageOnly : true,
            dateFormat      : 'yy-mm-dd'
        });
    });
    $('.btn-submit').click(function(){
        $("#form_add_task").submit();
    });
    Dropzone.autoDiscover = false;
    $(function() {
      var myDropzone = new Dropzone("#upload-file");
      myDropzone.on("addedfile", function(file) {
        /* Maybe display some more file information on your page */
      });
    })
	// var wd_task_status = $('#wd-task-status').find('.task_status'),
	// taskStatusDatas = <?php echo !empty($projectStatus) ? json_encode($projectStatus) : json_encode(array());?>;
	// /**
 //     * Phan chon cac phan tu trong combobox cua projectEmployeeManager
 //     */
 //    var $ids = [];
 //    wd_task_status.each(function(){
 //        var data = $(this).find('.wd-data');
 //        /**
 //         * When load data
 //         */

 //        var valList = $(data).find('#task_status_id').val();
 //        // console.log(taskStatusDatas);
 //        //     	console.log(valList);
 //        if(taskStatusDatas){
 //            $.each(taskStatusDatas, function(employId){

 //                if(valList == employId){
 //                    $(data).find('#task_status_id').attr('checked', 'checked');
 //                    $('a.wd-combobox').append('<span class="wd-dt-'+valList+'">' + $('.wd-group-' + valList).find('span').html() + '</span><span class="wd-em-'+valList+'">,</span>');
 //                }
 //                $ids.push(employId);
 //            });
 //        }
 //        /**
 //         * When click in checkbox
 //         */
 //        $(data).find('#task_status_id').click(function(){
 //            var _datas = $(this).val();
 //            if($(this).is(':checked')){
 //                $ids.push(_datas);
 //                $('a.wd-combobox').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '</span><span class="wd-em-'+_datas+'">, </span>');
 //            } else {
 //                $ids = jQuery.removeFromArray(_datas, $ids);
 //                $('a.wd-combobox').find('.wd-dt-' +_datas).remove();
 //                $('a.wd-combobox').find('.wd-em-' +_datas).remove();
 //            }
 //        });
 //    });
</script>