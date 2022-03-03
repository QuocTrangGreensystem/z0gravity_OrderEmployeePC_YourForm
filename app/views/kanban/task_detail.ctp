<?php

App::import("vendor", "str_utility");
$str_utility = new str_utility();
?>
<?php echo $html->css('dropzone.min'); ?>
<?php $read_only = !(($canModified && !$_isProfile) || $_canWrite) ? 1 : 0; ?>
<?php
    function draw_line_progress($value){
        $_html = '';
        $_color_gray = '#E2E6E8';
        $_color_green = array('#6EAF79', '#89BB92', '#AACCB0', '#C1D6C5', '#D7E2D9', '#D7E2D9', '#D7E2D9', '#D7E2D9', '#D7E2D9', '#D7E2D9' );
        $_color_blue =  array('#6FB0CF', '#87BFDA', '#A3CCE0', '#BBDAE9', '#D6E8F0');
        $_use_color = $value > 50 ? $_color_green : $_color_blue;
        $_index = 1; $_current_color = '';
        for( $_index = 1; $_index <= 10; $_index++){
            $_current_color = $_index*10 <= $value ? $_use_color[(intval($value/10) - $_index)] : $_color_gray;
            $_html .= '<span class="progress-node" style="background: ' . $_current_color . '"></span>';
        }
        return $_html;
    }
    // pm template
    $template_status = '<select name= "data[task_status_id]">';
    foreach ($projectStatus as $statu_id => $statu_name) {
        $class = ($this->data['task_status_id'] == $statu_id) ? 'selected' : '';
        $template_status .= '<option '. $class .' value='.$statu_id.'>' . $statu_name .'</option>';
    }
    $template_status .= '</select>';

    // phase template
    $template_phase = '<select name= "data[project_planed_phase_id]">';
    $template_phase .= '<option>-- Select --</option>';
    foreach ($projectPhases as $phase_id => $phase_name) {
        $class = ($this->data['project_planed_phase_id'] == $phase_id) ? 'selected' : '';
        $template_phase .= '<option '. $class .' value='.$phase_id.'>' . $phase_name .'</option>';
    }
    $template_phase .= '</select>';
    // projectPhases
    $maps = array(
        'project_name' => array(
            // 'label' => __d(sprintf($_domain, 'Project_Task'), "Project Name", true),
            'html' => $this->Form->input('project_name', array('div' => false, 'label' => false, 'style' => '', 'class' => 'project-name', 'disabled' => 'disabled'))
        ),
        'project_planed_phase_id' => array(
            // 'label' => __d(sprintf($_domain, 'Project_Task'), 'Project phase', true),
            'html' => $template_phase
        ),
        'task_title' => array(
            // 'label' => __d(sprintf($_domain, 'Project_Task'), 'Task title', true),
            'html' => $this->Form->input('task_title', array('type' => 'textarea','div' => false, 'label' => false, 'style' => ''))
        ),
        'task_start_date' => array(
            'label' => __d(sprintf($_domain, 'Project_Task'), 'Start date', true),
            'html' => $this->Form->input('task_start_date', array('div' => false,
                'label' => false,
                'type' => 'text', 'class' => 'wd-date'
            )),
        ),
        'task_end_date' => array(
            'label' => __d(sprintf($_domain, 'Project_Task'), 'End date', true),
            'html' => $this->Form->input('task_end_date', array('div' => false,
                'label' => false,
                'type' => 'text', 'class' => 'wd-date'
            )),
        ),
    );
    $maps2 = array(
        'Priority' => array(
            'label' => __d(sprintf($_domain, 'Project_Task'), 'Priority', true),
            'html' => $this->Form->input('task_priority_id', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Status' => array(
            // 'label' => __d(sprintf($_domain, 'Project_Task'), 'Status', true),
            // 'html' => $this->Form->input('task_status_id', array('div' => false, 'label' => false, 'style' => ''))
            'html' => $template_status
        ),
        'Milestone' => array(
            'label' => __d(sprintf($_domain, 'Project_Task'), 'Milestone', true),
            'html' => $this->Form->input('milestone_id', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Profile' => array(
            'label' => __d(sprintf($_domain, 'Project_Task'), 'Profile', true),
            'html' => $this->Form->input('profile_id', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Duration' => array(
            'label' => __d(sprintf($_domain, 'Project_Task'), 'Duration', true),
            'html' => $this->Form->input('duration', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Predecessor' => array(
            'label' => __d(sprintf($_domain, 'Project_Task'), 'Predecessor', true),
            'html' => $this->Form->input('predecessor', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Workload' => array(
            'label' => __d(sprintf($_domain, 'Project_Task'), 'Workload', true),
            'html' => $this->Form->input('estimated', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Overload' => array(
            'label' => __d(sprintf($_domain, 'Project_Task'), 'Overload', true),
            'html' => $this->Form->input('overload', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'ManualOverload' => array(
            'label' => __d(sprintf($_domain, 'Project_Task'), 'ManualOverload', true),
            'html' => $this->Form->input('manual_overload', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Consumed' => array(
            'label' => __d(sprintf($_domain, 'Project_Task'), 'Consumed', true),
            'html' => $this->Form->input('consumed', array('div' => false, 'label' => false, 'style' => '','disabled' => 'disabled',)),
            'unit' => '<span class="unit">J.H</span>',
        ),
        'ManualConsumed' => array(
            'label' => __d(sprintf($_domain, 'Project_Task'), 'ManualConsumed', true),
            'html' => $this->Form->input('manual_consumed', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'InUsed' => array(
            'label' => __d(sprintf($_domain, 'Project_Task'), 'InUsed', true),
            'html' => $this->Form->input('wait', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Completed' => array(
            'label' => __d(sprintf($_domain, 'Project_Task'), 'Completed', true),
            'html' => $this->Form->input('completed', array('div' => false, 'label' => false, 'style' => '','disabled' => 'disabled',)),
            'unit' => '<span class="unit">%</span>',
        ),
        'Remain' => array(
            'label' => __d(sprintf($_domain, 'Project_Task'), 'Remain', true),
            'html' => $this->Form->input('remain', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Initialworkload' => array(
            'label' => __d(sprintf($_domain, 'Project_Task'), 'Initialworkload', true),
            'html' => $this->Form->input('initial_estimated', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Initialstartdate' => array(
            'label' => __d(sprintf($_domain, 'Project_Task'), 'Initialstartdate', true),
            'html' => $this->Form->input('initial_task_start_date', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Amount€' => array(
            'label' => __d(sprintf($_domain, 'Project_Task'), 'Amount€', true),
            'html' => $this->Form->input('amount', array('div' => false, 'label' => false, 'style' => ''))
        ),
        '%progressorder' => array(
            'label' => __d(sprintf($_domain, 'Project_Task'), '%progressorder', true),
            'html' => $this->Form->input('progress_order', array('div' => false, 'label' => false, 'style' => ''))
        ),
        '%progressorder€' => array(
            'label' => __d(sprintf($_domain, 'Project_Task'), '%progressorder€', true),
            'html' => $this->Form->input('progress_order_amount', array('div' => false, 'label' => false, 'style' => ''))
        ),
        '+/-' => array(
            'label' => __d(sprintf($_domain, 'Project_Task'), '+/-', true),
            'html' => $this->Form->input('slider', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'UnitPrice' => array(
            'label' => __d(sprintf($_domain, 'Project_Task'), 'UnitPrice', true),
            'html' => $this->Form->input('unit_price', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Consumed€' => array(
            'label' => __d(sprintf($_domain, 'Project_Task'), 'Consumed€', true),
            'html' => $this->Form->input('consumed_euro', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Remain€' => array(
            'label' => __d(sprintf($_domain, 'Project_Task'), 'Remain€', true),
            'html' => $this->Form->input('remain_euro', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Workload€' => array(
            'label' => __d(sprintf($_domain, 'Project_Task'), 'Workload€', true),
            'html' => $this->Form->input('workload_euro', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'Estimated€' => array(
            'label' => __d(sprintf($_domain, 'Project_Task'), 'Estimated€', true),
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
                        <ul class="list-assign list-assign-<?php echo $task_id; ?>">
                            <?php 
                                $disabled = $read_only ? 'display: none' : '';
                                foreach ($listAssiged as $id => $assigedValue) {
                                    if(!empty($assigedValue['ProjectTaskEmployeeRefer']['project_task_id']) && $assigedValue['ProjectTaskEmployeeRefer']['project_task_id'] == $task_id && !empty($assigedValue['ProjectTaskEmployeeRefer']['reference_id'])){
                                        $employee_id = $assigedValue['ProjectTaskEmployeeRefer']['reference_id'];
                                        if(!empty($assigedValue['ProjectTaskEmployeeRefer']['reference_id']) && $assigedValue['ProjectTaskEmployeeRefer']['is_profit_center'] == 0 ){
                                            $name = $employeeName[$assigedValue['ProjectTaskEmployeeRefer']['reference_id']]['first_name'].' '.$employeeName[$assigedValue['ProjectTaskEmployeeRefer']['reference_id']]['last_name'];
                                            $urlAvatar = $this->UserFile->avatar($assigedValue['ProjectTaskEmployeeRefer']['reference_id']);
                                            if(!empty($urlAvatar) && !empty($checkAvatar[$assigedValue['ProjectTaskEmployeeRefer']['reference_id']])){
                                                $action_delete = '<span class="delete-employee icon-close" onclick="removeAssign.call(this)" data-emp="'. $employee_id .'"></span>';
                                                $action_delete = $read_only ? '' : $action_delete;
                                                echo '<li title= "'. $name .'">'. $action_delete .'<img src='. $urlAvatar .' alt="login"></li>';
                                            }else{
                                                $employee_name = explode(' ', $name);
                                                $url = substr( trim($employee_name[0]),  0, 1) .''.substr( trim($employee_name[1]),  0, 1);
                                                $action_delete = '<span class="delete-employee icon-close" onclick="removeAssign.call(this)" data-emp="'. $employee_id .'"></span>';
                                                $action_delete = $read_only ? '' : $action_delete;
                                                echo '<li title ="'. $name .'">'. $action_delete .'<span class="circle-name">'. $url .'</span></li>';
                                            }
                                            
                                        }else if($assigedValue['ProjectTaskEmployeeRefer']['is_profit_center'] == 1){
                                            $action_delete = '<span class="delete-employee icon-close" onclick="removeAssign.call(this)" data-emp="'. $employee_id .'"></span>';
                                            $action_delete = $read_only ? '' : $action_delete;
                                            echo '<li title ="'. $name .'" class="assign-team">'. $action_delete .'<i class="icon-people"></i></li>';
                                        }
                                    }
                                }
                            ?>
                        </ul>
                         <?php if(($canModified && !$_isProfile) || $_canWrite){ ?>
                        <div id="popup-assign">
                            <a data-id = "<?php echo $task_id; ?>" onclick="getAssignEmployee.call(this);" data-start = '<?php echo $this->data['task_start_date'] ?>' data-end = '<?php echo $this->data['task_end_date']?>' title ="Assign to"><i class='icon-plus'></i></a>
                            <div class="popup-content" data-id = "<?php echo $task_id; ?>"></div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div id='content_comment'>
                <div class="task-field-upload">
                    <div class="content-attachment"><h4>Document(s)</h4>
                        <div class="attachments-file">
                            <?php if(!empty($this->data['attachments'])){ 
                                echo '<ul>';
                                foreach ($this->data['attachments'] as $key => $value) {
                                    if ( preg_match('/\.(jpg|jpeg|bmp|gif|png|swf)$/i', $value)) {?>
                                    <li><i class="icon-paper-clip"></i><span href="<?php echo  $this->Html->url(array('action' => 'attachment', $key, '?' => array('sid' => $api_key))); ?>" class="fancy image" rel="one_pic_expand" data-fancybox="image" data-type="image"><?php echo $value ?></span>
                                        <?php if(!$read_only){ ?>
                                            <a data-id = "<?php echo $key; ?>"><img src="/img/new-icon/delete-attachment.png" alt="<?php echo $key; ?>" onclick="deleteAttachment.call(this)"></a>
                                        <?php } ?>
                                    </li>

                                    <?php 
                                    } else{
                                        $link_download = $read_only ? '#' : $this->Html->url(array('action' => 'attachment', $key, '?' => array('download' => true, 'sid' => $api_key)),true);
                                        ?>
                                        <li><i class="icon-paper-clip"></i><a class="file-name" href="<?php echo  $link_download; ?>"><?php echo $value ?></span>
                                            <?php if(!$read_only){ ?>
                                                <a data-id = "<?php echo $key; ?>"><img src="/img/new-icon/delete-attachment.png" alt="<?php echo $key; ?>" onclick="deleteAttachment.call(this)"></a>
                                            <?php } ?>
                                        </li>
                                   <?php }
                                }
                                echo '</ul>';
                            } ?>
                        </div>
                    </div>
                    <?php if(!$read_only){ ?>
                   <div class="trigger-upload"><form id="upload-file" method="post" action="/kanban/update_document/<?php echo $this->data['project_id']; ?>" class="dropzone" value="" >
                        <input type="hidden" name="data[Upload][id]" rel="no-history" value="<?php echo $task_id; ?>" id="UploadId">
                    </form></div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="task-column task-comment">
            <div id="template_logs">
				<div class="add-comment"><div class="input-add"><textarea data-id="<?php echo $this->data['id']; ?>" class="text-textarea" id="update-comment" placeholder="Votre message..."></textarea></div></div>
                <div id="content_comment" class="content_comment">
                    <div id="content-comment-detail">
                        <?php if(!empty($projectTaskTxt)){
                            foreach ($projectTaskTxt as $key => $value) {
                                if(!empty($value['ProjectTaskTxt']['employee_id'])){
                                $employee_info = $projectTaskTxt['employee_info'][$value['ProjectTaskTxt']['employee_id']]; 
                             ?>
                            <div class="content-tast-text">
                                <div class="content">
									<div class="avatar">
										<img class="circle-name" src="<?php echo $this->Html->url('/employees/avatar/'. $value['ProjectTaskTxt']['employee_id'] .'/avatar_resize'); ?>">
									</div>
                                    <div class="content-employee">
                                        <div class="employee-info"><p><?php echo $employee_info['first_name'] .' '. $employee_info['last_name']; ?></p><p><?php echo $value['ProjectTaskTxt']['created']; ?></p></div>
										<div class="comment"><?php echo $value['ProjectTaskTxt']['comment']; ?></div>
                                    </div>
                                    
                                </div>
                            </div>
                        <?php } } } ?>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="task-column task-field">
            <?php echo $this->Form->create('Project', array('type' => 'POST', 'id' => 'form_edit_task', 'url' => array('controller' => 'kanban', 'action' => 'update_task'))); ?>
            <input type="hidden" name="data[id]" rel="no-history" value="<?php echo $task_id; ?>">
            <input type="hidden" name="data[project_id]" rel="no-history" value="<?php echo $this->data['project_id']; ?>">
            <div class="task-field-default">
                <?php
                $fieldNameDefault = array('project_name', 'project_planed_phase_id', 'task_title', 'task_start_date','task_end_date');
                foreach($fieldNameDefault as $data){
                    $fieldName = $data;
                    if($fieldName == 'task_start_date' || $fieldName == 'task_end_date' ){
                        echo '<div class="wd-input '. $fieldName .'">';
                        if(!empty($maps[$fieldName]['label'])){
                            echo '<label>'. $maps[$fieldName]['label'] . '</label>';
                        } ?>
                        <?php echo !empty($maps[$fieldName]['html']) ? $maps[$fieldName]['html'] : '';
                        echo '</div>';
                    }else{
                        if(!empty($maps[$fieldName]['label'])){
                            echo '<label>'. $maps[$fieldName]['label'] . '</label>';
                        } ?>
                        <?php echo !empty($maps[$fieldName]['html']) ? $maps[$fieldName]['html'] : ''; ?>
                    <?php }
                    }?>
            </div>
            <div class="task-field-option">
                <?php
                 foreach($columns_order as $field){
                    $field = explode('|', $field);
                    if($field[1] == 1){
                        if(!empty($maps2[$field[0]]['label'])){
                            echo '<label>'. $maps2[$field[0]]['label'] . '</label>';
                        }
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
            $taskCurrentDate = strtotime(date('Y-m-d', time())) - strtotime($this->data['task_start_date']);
            $taskDate = abs(strtotime($this->data['task_end_date']) - strtotime($this->data['task_start_date']));
            $initDate = 100;

            if($taskCurrentDate <= $taskDate && $taskDate) $initDate = floor(($taskCurrentDate / $taskDate) * 100);
            if( $taskCurrentDate <= 0 || !$taskDate) $initDate = 0;
            $late_day = strtotime(date('Y-m-d', time())) - strtotime($this->data['task_end_date']);
            $class = ($late_day > 0) ? 'late-day' : '';

        ?>
        <div class ="task-progress">
            <div class="project-progress <?php echo ($initDate > 50) ? 'late-progress' : ''; ?>">
                <p class="progress-full"> <?php echo draw_line_progress($initDate);?> </p><p><?php echo $initDate; ?>%</p>
            </div>
        </div>
        <?php } ?>
        <?php if(($canModified && !$_isProfile) || $_canWrite){ ?>
        <div class="buton-action">
            <a class="btn-submit" href="#"><?php echo __('enregistrer', true); ?></a>

        </div>
        <?php } ?>
    </div>
</div>

<script type="text/javascript">
var start_date = <?php echo json_encode($this->data['task_start_date']);?>;
var end_date = <?php echo json_encode($this->data['task_end_date']);?>;
var read_only = <?php echo json_encode($read_only)?>;
    if(read_only){
        $(".task-column").find("input, select, textarea, button").prop('disabled', true);
        // $("form").find("textarea").prop('disabled', true);
        // $("form").find("textarea").prop('disabled', true);
    }
    $(function(){
        $("#task_end_date").datepicker({
            minDate : start_date,
            buttonImageOnly : true,
            dateFormat      : 'yy-mm-dd',
            
        });
        $("#task_start_date").datepicker({
            maxDate: end_date,
            buttonImageOnly : true,
            dateFormat      : 'yy-mm-dd',
            
        });

    });

    if(start_date && end_date) $('.wd-date').define_limit_date('#task_start_date', '#task_end_date');

    $('.btn-submit').click(function(){
        $("#form_edit_task").submit();
    });
    Dropzone.autoDiscover = false;
    $(function() {
      var myDropzone = new Dropzone("#upload-file");
      myDropzone.on("queuecomplete", function(file) {
            id = $('#UploadId').val();
            $.ajax({
                url: '/kanban/getTaskAttachment/'+ id,
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    _html = '<ul>';
                    if (data['attachments']) {
                        $.each(data['attachments'], function(ind, _data) {
                           if((/\.(gif|jpg|jpeg|tiff|png)$/i).test(_data)){ 
                                _link = '/kanban/attachment/'+ ind +'/?sid='+ api_key;
                                _html += '<li><i class="icon-paper-clip"></i><span href="'+ _link +'" class="fancy image" rel="one_pic_expand" data-fancybox="image" data-type="image">'+ _data +'</span><a data-id = "'+ id +'"><img src="/img/new-icon/delete-attachment.png" alt="'+ ind +'" onclick="deleteAttachment.call(this)"></a></li>';
                            }else{
                                _link = '/kanban/attachment/'+ ind +'/?download=1&sid='+ api_key;
                                _html += '<li><i class="icon-paper-clip"></i><a class="file-name" href = "'+ _link +'" >'+ _data +'</a><a  data-id = "'+ id +'"><img src="/img/new-icon/delete-attachment.png" alt="'+ ind +'" onclick="deleteAttachment.call(this)"></a></li>';
                            }
                        });
                    }
                    _html += '</ul>';
                    $('.content-attachment .attachments-file').find('ul').empty();
                    $('.content-attachment .attachments-file').append(_html);

                }
            });
        });
        myDropzone.on("success", function(file) {
            console.log(1);
            myDropzone.removeFile(file);
        });
    })

</script>
