<?php echo $html->script(array(
    'autosize.min',
    'html2canvas',
    'preview/jquery.html2canvas.flash_info'
)); 
?>
<?php echo $html->css(array('preview/flash_info')); ?>
<?php
if( !function_exists('create_field_logs')){
    function create_field_logs($_this, $field_name, $loop, $is_avatar){
        $loop = min($loop, count($field_name));
        if(!empty($field_name)){
            //for($i = 0 ; $i < $loop; $i++){
            for($i = count($field_name)-1 ; $i >= count($field_name) - $loop; $i--){
                $avatar = '';
                if(!empty($field_name[$i]['LogSystem']['employee_id']) && $is_avatar[$field_name[$i]['LogSystem']['employee_id']]){
                    $src = $_this->UserFile->avatar($field_name[$i]['LogSystem']['employee_id']);
                    $avatar = '<img src ="'. $src .'" />';
                }else{
                    //$employee_name = explode(' ', $field_name[$i]['LogSystem']['name']);
                    // $avatar = substr(trim($employee_name[0]),  0, 1) .''.substr(trim($employee_name[1]),  0, 1);
                    $employee_name = !empty($field_name[$i]['LogSystem']['name']) ? explode(' ', $field_name[$i]['LogSystem']['name']) : array();
                    $avatar = isset($employee_name[0])&&isset($employee_name[1]) ? substr(trim($employee_name[0]),  0, 1) .''.substr(trim($employee_name[1]),  0, 1) : 'AV';
                } ?>
                <div class = "content-item log-item">
                    <div class="log-info">
                        <span class="circle-name"><?php echo $avatar; ?></span>
                        <a href="javascript:void(0)" class="flash-field-edit"><img src="/img/new-icon/edit-task.png"></a>
                        <span class="log-time"><?php echo date('d M Y', $field_name[$i]['LogSystem']['created']); ?></span>
                    </div>
                    <!-- <textarea class="content log-content" rows="3" onfocus="autosize(this)" onblur="autosize.destroy(this)"  onchange="updateLog.call(this, '<?php echo $field_name[$i]['LogSystem']['model'] ?>')" data-id = "<?php echo $field_name[$i]['LogSystem']['id']; ?>"><?php echo $field_name[$i]['LogSystem']['description']; ?></textarea> -->
                    <textarea class="content log-content" rows="3"  onchange="updateLog.call(this, '<?php echo $field_name[$i]['LogSystem']['model'] ?>')" data-id = "<?php echo $field_name[$i]['LogSystem']['id']; ?>"><?php echo $field_name[$i]['LogSystem']['description']; ?></textarea>
                </div>
                <?php
            }
        }else{ ?>
            <div class = "content-item log-item log-item-empty">
                <span><?php echo __("Empty", true) ?></span>
            </div>
        <?php }
    }
} 
if( !function_exists('wd_comment_form')){
    function wd_comment_form($model){
        if( !$model ) return;
        ob_start();
        ?>
        <div class="template_logs" style="display: none;">
            <div class="add-comment">
                <!-- <p class="flash-field-title add-comment-title"><?php __('Add new comment'); ?></p> -->
                <textarea class="add-comment-text" id="<?php echo $model; ?>-comment-text" name="<?php echo $model; ?>-comment-text" rows="5" onfocus="autosize(this)" onblur="autosize.destroy(this)"  onchange="autosize(this)" placeholder="<?php __('Your message'); ?>"></textarea>
                <input type="hidden" id="<?php echo $model; ?>-comment-model" name="<?php echo $model; ?>-comment-model" class="comment-model" value="<?php echo $model; ?>"></input>
                <p class="submit-row">
                    <a class="button add-comment-submit" href="javascript:void(0);" onclick="sent_comment(this,'<?php echo $model; ?>');"><?php __('Enregistrer'); ?></a>
                </p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

?>
<?php 
    echo $html->css('slick'); 
    echo $html->css('slick-theme');  
    echo $html->script('slick.min'); 
    $url_next = $_project_nav['next'] ? $html->url(array($_project_nav['next'])) : 'javascript:void(0)';
    $url_prev = $_project_nav['prev'] ? $html->url(array($_project_nav['prev'])) : 'javascript:void(0)';
?>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout wd-layout-flash">
    
        <div class="wd-main-content">
            <div class="wd-flash-action-top">
                <div class="wd-flash-nav">
                    <a href="<?php echo $url_prev;?>" class="btn btn-prevous">
                        <img src="/img/new-icon/arrow-left-gray.png">
                        <img class="img-hover" src="/img/new-icon/arrow-left-light.png">
                    </a>
                    <a href="<?php echo $url_next; ?>" class="btn btn-next">
                        <img src="/img/new-icon/arrow-right-gray.png">
                        <img class="img-hover" src="/img/new-icon/arrow-right-light.png">
                    </a>
                </div>
                <div class="wd-flash-list-action">
                    <a href="<?php echo $html->url("/project_phase_plans/phase_vision/" . $flash_data['project_id']) ?>" class="btn btn-gantt" title="<?php __('Gantt+') ?>"></a>
                    <a href="<?php echo $html->url("/projects_preview/exportExcelFlashInfo/" . $flash_data['project_id']) ?>" class="btn export-excel-icon-all hide-on-mobile" id="export-submit" title="<?php __('Export Excel')?>"><span><?php __('Export Excel') ?></span></a>
                    <a href="javascript:void(0)" onclick="pdfExport(); return false;" class="btn export-pdf-icon hide-on-mobile" id="export-pdf" title="<?php __('Export PDF file')?>">
                        <img src="/img/new-icon/file_icon_black.png">
                        <img class="img-hover" src="/img/new-icon/file_icon_light.png">
                        <span><?php __('PDF') ?></span></a>
                    <a href="javascript:;" onclick="expandScreen();" class="btn btn-expand hide-on-mobile"></a>
                </div>
            </div>
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>

            <div class="wd-tab">
                <div class="wd-flash-wrapper">
                    <div class="wd-flash-top">
                        <div class="project-title">
                            <div class="project-weather">
                                <?php if(!empty($flash_data['weather'])){ ?>
                                    <img src="<?php  echo $html->url('/img/new-icon/'. $flash_data['weather'] .'.png')?>" >
                                <?php } ?>
                                <?php if(!empty($flash_data['rank'])){ ?>
                                    <img src="<?php  echo $html->url('/img/new-icon/project_rank/'. $flash_data['rank'] .'.png')?>" >
                                <?php } ?>
                            </div>
                            <h4><?php echo $flash_data['project_name']?></h4>
                        </div>
                        <ul class="project-list">
                            <li>
                                <span><?php echo __("flash Project program", true) ?></span>
                                <p><?php echo !empty($flash_data['project_program']) ? $flash_data['project_program'] : ''; ?></p>
                            </li>
                            <li>
                                <span><?php echo __("flash Project Manager", true) ?></span>
                                <p><?php echo !empty($flash_data['project_manager']) ? $flash_data['project_manager'] : ''; ?></p>

                            </li>
                            <li>
                                <span><?php echo __('flash Project Code 1', true) ?></span>
                                <p><?php echo !empty($flash_data['project_code']) ? $flash_data['project_code'] : ''; ?></p>
                            </li>
                            <li>
                                <span><?php echo __('flash Planed', true);?></span>
                                <p><?php echo !empty($flash_data['validated']) ? $flash_data['validated'] : ''; ?> <?php echo __('M.D', true); ?></p>
                            </li>
                            <li>
                                <span><?php echo __('flash Consumed', true);?></span>
                                <p><?php echo !empty($flash_data['engaged']) ? $flash_data['engaged'] : ''; ?> <?php echo __('M.D', true); ?></p>
                            </li>
                            <li> 
                                <span><?php echo __('flash Overload', true);?></span>
                                <p><?php echo !empty($flash_data['overload']) ? $flash_data['overload'] : ''; ?></p>
                            </li>
                            <li>
                                <span><?php echo __('flash Start date', true);?></span>
                                <p><?php echo !empty($flash_data['start_date']) ? date('d/m/Y', strtotime($flash_data['start_date'])) : ''; ?></p>
                            </li>
                            <li>
                                <span><?php echo __('flash End date', true);?></span>
                                <p><?php echo !empty($flash_data['end_date']) ? date('d/m/Y', strtotime($flash_data['end_date'])) : ''; ?></p>
                            </li>
                        </ul>
                    </div> 
                    <div class="wd-flash-content clearfix"> <div class="wd-row"> 
                        <div class="wd-flash-left wd-col-lg-6 wd-col"><div class="wd-col-inner">
                            <div class="flash-field flash-milestone"><div class="flash-field-inner">
                                <div class="flash-field-title"><span><?php echo __('flash Milestone', true); ?></span></div>
                                <div class="flash-field-content">
                                    <div class="content-item">
										<div id="milestone-slider" class="wd-slick-slider">
											<div class="slides">
												<?php 
												
													$i = 0; 
													$next_ms = '';
													$min='99999999999';
													$active_ms ='';
													$compare_year = '';
													$currentDate = strtotime(date('d-m-Y', time()));
													foreach ($projectMilestones as $p) { 
														$milestone_date = strtotime($p['milestone_date']);
														$milestone_year = new DateTime();$milestone_year->setTimestamp($milestone_date);
														$milestone_year = $milestone_year->format('Y');
														// debug($milestone_year);
														if( $compare_year && $milestone_year != $compare_year){
															$projectMilestones[$i-1]['year'] = $compare_year;
														}
														$compare_year = $milestone_year;
														$flag = abs($currentDate - $milestone_date);
														 
														if($min > $flag && $milestone_date <= $currentDate){
															$min = $flag;
															$active_ms = $p['id'];
														}
														$i++;
													}
													$current_item = 0;
													$i= 0;
													foreach ($projectMilestones as $p) { 
														$milestone_date = strtotime($p['milestone_date']);
														$nearDate = $currentDate - $milestone_date;
														$item_class = '';
														if( !empty( $p['year']) ){
															$item_class .= ' has-year';
														}
														if( $current_item ){
															 $item_class .= ' next-item';
															 $current_item = 0;
														}
														if ($active_ms == $p['id']) {
															$item_class .= ' active-item';
															$current_item = 1;
														}else{
															if($milestone_date > $currentDate){
																$item_class .= ' last-item flag-item';
															}
														}
														if($p['validated']){
															$item_class .= ' milestone-validated';
														}else{
															if ($milestone_date < $currentDate) {
																$item_class .= ' milestone-mi milestone-red';
															} else if($milestone_date > $currentDate) {
																$item_class .= ' milestone-blue';
															} else {
																$item_class .= ' milestone-orange';
															}
														}
														if($milestone_date < $currentDate) { $item_class .= ' out_of_date'; }
														?>
															<div data-num = <?php echo $i; ?> class="wd-slider-item">
																<div class="milestones-item <?php echo $item_class; ?>" data-id="<?php echo $p['id']; ?>">
																	<?php if( !empty( $p['year']) ){ ?>
																		<div class="milestone-year">
																			<?php echo $p['year'];?> 
																		</div>
																	<?php } ?> 
																	<div class="date-milestones">
																		<span><b><?php echo date("d", strtotime($p['milestone_date'])); ?></b></span>
																		<span><?php echo date("M", strtotime($p['milestone_date'])); ?></span>
																	</div>
																	<p><?php echo $p['project_milestone']; ?></p>
																</div>
															</div>
														<?php 
														$i++;
													}
												?>
											</div>
										</div>
                                    </div>
                                </div>
                            </div></div>
                            <div class="flash-field flash-primary-object log-item"><div class="flash-field-inner">
                                <div class="flash-field-title">
                                    <span><?php echo __('flash Primary Objective', true) ?></span>
                                    <a href="javascript:void(0)" class="flash-field-edit"><img src="<?php  echo $html->url('/img/new-icon/edit-task.png')?>" ></a>
                                </div>
                                <div class="flash-field-content">
                                    <div class="content-item">
                                        <textarea class="content log-content" onfocus="autosize(this)" onblur="autosize.destroy(this)" rows = "20"  onchange="updateLog.call(this, 'primary_objectives')" data-id = "primary_objectives"><?php echo !empty($flash_data['primary_objectives']) ? $flash_data['primary_objectives'] : ''; ?></textarea>
                                    </div>
                                </div>
                            </div></div>
                        </div></div>
                        <div class="wd-flash-right wd-col-lg-6 wd-col"><div class="wd-col-inner">
                            <div class="wd-flash-log"><div class="wd-row">
                                <div class="wd-col-md-6 wd-col-lg-6 wd-col"><div class="flash-field flash-risk-comment">
                                    <div class="flash-field-title"><span><?php echo __('flash Risk/Opportunity', true);?></span></div>
                                    <div class="flash-field-content">
                                        <?php create_field_logs($this, $risk_comment, 1, $is_avatar); ?>
                                    </div>
                                    <a href="javascript:void(0);" onclick="show_comment_popup(this,'ProjectRisk')" class="flash-field-addnew"></a>
                                    <?php echo wd_comment_form('ProjectRisk');?>
                                </div></div>
                                <div class="wd-col-md-6 wd-col-lg-6 wd-col"><div class="flash-field flash-risk-risk ">
                                    <div class="flash-field-title"><span><?php echo __('flash Risk', true); ?></span></div>
                                    <div class="flash-field-content">
                                        <?php
                                        if(!empty($projectRisks['ProjectRisk'])){ ?>
                                        <div class = "content-item log-item">
                                           <?php 
                                             $avatar = '';
                                                if(!empty($projectRisks['employee_id']) && $is_avatar[$projectRisks['employee_id']]){
                                                    $src = $this->UserFile->avatar($projectRisks['employee_id']);
                                                    $avatar = '<img src ="'. $src .'" />';
                                                }else{
                                                    $employee_name = !empty($projectRisks['employee_name']) ? explode(' ', $projectRisks['employee_name']) : array();
                                                    $avatar = isset($employee_name[0])&&isset($employee_name[1]) ? substr(trim($employee_name[0]),  0, 1) .''.substr(trim($employee_name[1]),  0, 1) : 'AV';
                                                } ?>
                                            <div class="log-info" style="height: 30px">
                                                <span class="circle-name"><?php echo $avatar ?></span>
                                                <a href="javascript:void(0)" class="flash-field-edit"><img src="/img/new-icon/edit-task.png"></a>
                                                <span class="log-time"><?php echo date('d M Y', $projectRisks['ProjectRisk']['updated']); ?></span>
                                            </div>
                                            <textarea class="content log-content" rows="3"  onchange="updateProjectRisk.call(this, '<?php echo $projectRisks['ProjectRisk']['id'] ?>')"><?php echo $projectRisks['ProjectRisk']['project_risk']; ?></textarea>
                                        </div>
                                        <?php } else{ ?>
                                            <div class = "content-item log-item log-item-empty">
                                                <span><?php echo __("Empty", true) ?></span>
                                            </div>
                                        <?php } ?>
                                       
                                    </div>
                                    <a href="javascript:void(0);"  onclick="show_comment_popup(this, 'ProjectRisk');" onclick="" class="flash-field-addnew"></a>
                                    <div class="template_logs" style="display: none;">
                                        <div class="add-comment">
                                            <textarea class="add-comment-text" id="project-risk-text" name="project-risk-text" rows="5" onfocus="autosize(this)" onblur="autosize.destroy(this)"  onchange="autosize(this)" placeholder="<?php __('Your message'); ?>"></textarea>
                                            <p class="submit-row">
                                                <a class="button add-comment-submit" href="javascript:void(0);" onclick="addProjectRisk(this);"><?php __('Enregistrer'); ?></a>
                                            </p>
                                        </div>
                                    </div>
                                </div></div>
                                <div class="wd-col-md-6 wd-col-lg-6 wd-col"><div class="flash-field flash-kpi-comment ">
                                    <div class="flash-field-title"><span><?php echo __('flash KPI / Comment', true);?></span></div>
                                    <div class="flash-field-content">
                                        <?php create_field_logs($this, $kpi_comment, 2, $is_avatar); ?>
                                    </div>
                                    <a href="javascript:void(0);" onclick="show_comment_popup(this,'ProjectAmr')" class="flash-field-addnew"></a>
                                    <?php echo wd_comment_form('ProjectAmr');?>

                                        

                                    </a>
                                </div></div>
                                <div class="wd-col-md-6 wd-col-lg-6 wd-col"><div class="flash-field flash-kpi-done ">
                                    <div class="flash-field-title"><span><?php echo __( 'flash KPI / Done', true); ?></span></div>
                                    <div class="flash-field-content">
                                        <?php create_field_logs($this, $done, 2, $is_avatar); ?>
                                    </div>
                                    <a href="javascript:void(0);" onclick="show_comment_popup(this,'Done')" class="flash-field-addnew"></a>
                                    <?php echo wd_comment_form('Done');?>


                                </div></div>
                                <div class="wd-col-md-6 wd-col"><div class="flash-field flash-kpi-todo ">
                                    <div class="flash-field-title"><?php echo __( 'flash KPI / ToDo', true); ?></span></div>
                                    <div class="flash-field-content">
                                        <?php create_field_logs($this, $todo, 2, $is_avatar); ?>
                                    </div>
                                    <a href="javascript:void(0);" onclick="show_comment_popup(this,'ToDo')" class="flash-field-addnew"></a>
                                    <?php echo wd_comment_form('ToDo');?>


                                </div></div>
                            </div>
                        </div></div>
                    </div> </div> <!-- Close wd-flash-content -->
                </div>
            </div>
        </div>
    </div>
</div>
<?php
echo $this->Form->create('Export', array('url' => array('controller' => 'projects', 'action' => 'export_pdf'), 'type' => 'file'));
echo $this->Form->hidden('canvas', array('id' => 'canvasData'));
echo $this->Form->hidden('height', array('id' => 'canvasHeight'));
echo $this->Form->hidden('width', array('id' => 'canvasWidth'));
echo $this->Form->hidden('project_name', array('value' => $flash_data['project_name']));
echo $this->Form->hidden('project_id', array('value' => $flash_data['project_id']));
echo $this->Form->end();
?>

<script type="text/javascript">
    /**
     * Add log system of sale lead
     */
    var project_id = <?php echo json_encode($flash_data['project_id']); ?>;
    var is_avatar = <?php echo json_encode($is_avatar) ?>;
    function sent_comment(element, model){
        if (!model) return;
        var _this = $(element);
        var _model = model;
        var _ele_append = _this.closest('.flash-field').find('.flash-field-content');
        var _comment_box = _this.closest('.template_logs').find('.add-comment-text');
        var loop = 2;
        if (model == 'ProjectRisk') loop = 1;
        var _comment = $.trim(_comment_box.val());
        if(_model && _comment){
            _comment_box.prop('disabled', true);
            _this.addClass('loading');
            _ele_append.addClass('loading');
            $.ajax({
                url: '<?php echo $html->url(array('action' => 'update_data_log')) ?>',
                type : 'POST',
                dataType : 'json',
                data: {
                    data: {
                        id: '',
                        model: _model,
                        description: _comment,
                        model_id: project_id
                    }
                },
                success: function(response) {
                    var _html = '';
                    
                    $.each(response, function(ind, data) {
                        if(ind < loop){
                            var src = '',
                            employee_name = '';
                            if(is_avatar[data['LogSystem']['employee_id']]){
                                var src = <?php echo $this->UserFile->avatarjs() ?>.replace('{id}', data['LogSystem']['employee_id']);
                                avatar = '<img src ="'+ src + '" />';
                            }else{
                                employee_name = data['LogSystem']['name'].split(" ");
                                first_name  =  employee_name[0].substr(0,1);
                                last_name  =  employee_name[1].substr(0,1);
                                avatar = first_name +''+ last_name;
                            }
                            _html +='<div class = "content-item log-item"><div class="log-info"><span class="circle-name">'+ avatar +'</span><a href="javascript:void(0)" class="flash-field-edit"><img src="/img/new-icon/edit-task.png"></a><span class="log-time">'+ data["LogSystem"]["created"] +'</span></div><textarea class="content log-content" rows="3" onchange="updateLog.call(this, \''+_model +'\')" data-id = "'+ data['LogSystem']['id'] +'">'+ data['LogSystem']['description'] +'</textarea></div>';
                        }
                        if(_html) _ele_append.empty().append(_html);
                        _ele_append.removeClass('loading');
                       
                    });

                },
                complete: function(){
                    _comment_box.prop('disabled', false);
                }
            });
            
        }

    }
    function addProjectRisk(element){
        var _this = $(element);
        var _ele_append = _this.closest('.flash-field').find('.flash-field-content');
        var _comment_box = _this.closest('.template_logs').find('.add-comment-text');
        var _comment = $.trim(_comment_box.val());
        if( _comment){
            _comment_box.prop('disabled', true);
            _this.addClass('loading');
            _ele_append.addClass('loading');
            $.ajax({
                url: '<?php echo $html->url(array('action' => 'add_project_risk')) ?>',
                type : 'POST',
                dataType : 'json',
                data: {
                    data: {
                        id: '',
                        project_risk: _comment,
                        project_id: project_id
                    }
                },
                success: function(response) {
                    console.log(response);
                    var _html = '';
                    var src = '',
                    employee_name = '';
                    if(is_avatar[response['ProjectRisk']['employee_id']]){
                        var src = <?php echo $this->UserFile->avatarjs() ?>.replace('{id}', response['ProjectRisk']['employee_id']);
                        avatar = '<img src ="'+ src + '" />';
                    }else{
                        employee_name = data['LogSystem']['name'].split(" ");
                        first_name  =  employee_name[0].substr(0,1);
                        last_name  =  employee_name[1].substr(0,1);
                        avatar = first_name +''+ last_name;
                    }
                    _html +='<div class = "content-item log-item"><div class="log-info"><span class="circle-name">'+ avatar +'</span><a href="javascript:void(0)" class="flash-field-edit"><img src="/img/new-icon/edit-task.png"></a><span class="log-time">'+ response["ProjectRisk"]["updated"] +'</span></div><textarea class="content log-content" rows="3" onchange="updateProjectRisk.call(this, \'' + response['ProjectRisk']['id'] +'\')">'+ response['ProjectRisk']['project_risk'] +'</textarea></div>';
                
                    if(_html) _ele_append.empty().append(_html);
                    _ele_append.removeClass('loading');

                },
                complete: function(){
                    _comment_box.prop('disabled', false);
                }
            });
            
        }

    }
    function show_comment_popup( element, model){
        $(element).siblings('.template_logs').fadeToggle(300).toggleClass('active');
    }
    $('body').on('click', function(e){
        if(!( $(e.target).hasClass('flash-field') || $('.flash-field').find(e.target).length)){
            $('.template_logs').hide('300').removeClass('active');
        }
    });
    function updateLog(model){
        var inp = $(this),
            value = $.trim(inp.val()),
            log_id = inp.data('id');
        if( value ){
            inp.prop('disabled', true);
            // save
            $.ajax({
                url: '<?php echo $html->url(array('action' => 'update_data_log')) ?>',
                type : 'POST',
                dataType : 'json',
                data: {
                    data: {
                        id: log_id,
                        model: model,
                        description: value,
                        model_id: project_id
                    }
                },
                success: function(response) {
                },
                complete: function(){
                    inp.prop('disabled', false).css('color', '#3BBD43');
                }
            });
        }
    }
    function updateProjectRisk(id){
        var inp = $(this),
            value = $.trim(inp.val());
        if( value ){
            inp.prop('disabled', true);
            // save
            $.ajax({
                url: '<?php echo $html->url(array('action' => 'update_project_risk')) ?>',
                type : 'POST',
                dataType : 'json',
                data: {
                    data: {
                        id: id,
                        project_risk: value,
                        project_id: project_id
                    }
                },
                success: function(response) {
                },
                complete: function(){
                    inp.prop('disabled', false).css('color', '#3BBD43');
                }
            });
        }
    }
    $('.flash-field-edit').click(function(){
        var text_area = $(this).closest('.log-item').find('textarea');
        val = text_area.val();
  
        // focus textarea, clear value, re-apply
        text_area.focus().val("").val(val);
    });
    function milestones_slider(){
        //<div id="milestone-slider" class="wd-slick-slider">
        var _slider = $('#milestone-slider .slides');
        var active_index = 0, index=0;

        if( _slider.length == 0) return;
        var item = _slider.children('.wd-slider-item').length;
        if( item){
            _slider.children('.wd-slider-item').each(function(){
                if( $(this).find('.milestones-item').hasClass('active-item')) active_index = index;
                index++;
            });
        }
        var slider_show = Math.min(item,3);
        if(item == slider_show){
            active_index = 0;
        }
        var slick_slider = _slider.slick({
            infinite: false,
            slidesToShow: slider_show,
            //slidesToScroll: slider_show,
            speed: 600,
            arrows: true,
            dots: false,
            //centerMode: true,
            focusOnSelect: true,
            initialSlide: active_index,
            centerPadding: '0',
            prevArrow: '<button type="button" class="slick-prev"><span><img src="/img/new-icon/arrow-left-gray.png"><img class="img-hover" src="/img/new-icon/arrow-left-brown.png"><span></button>',
            nextArrow: '<button type="button" class="slick-next"><span><img src="/img/new-icon/arrow-right-gray.png"><img class="img-hover" src="/img/new-icon/arrow-right-brown.png"></span></button>',
            responsive:[
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: Math.max(slider_show-1, 1),
                    }
                },
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: Math.max(slider_show-2 , 3),
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: Math.max(slider_show-2 , 2),
                    }
                },
            ]
        });
    }
    milestones_slider();
	$('#milestone-slider').on('click', '.milestones-item', function(e){
        //e.preventDefault();
        $('#milestone-slider, .wd-table-container').addClass('loading');
        var _this = $(this);
        var color = '';
        $('#milestone-slider').find('.milestones-item').removeClass('active-item');
        $('#milestone-slider').find('.flag-item').addClass('last-item');
        var out_date = _this.hasClass('out_of_date');
        if( !_this.hasClass('milestone-validated')){
            color = 'green';
            _this.removeClass('milestone-blue milestone-mi milestone-red milestone-orange').addClass('milestone-validated');
        }else{
            if( out_date){
                color = 'mi';
                _this.removeClass('milestone-blue milestone-green milestone-validated milestone-orange').addClass('milestone-mi milestone-red');
            }else{
                color = 'blue';
                _this.removeClass('milestone-green milestone-validated milestone-mi milestone-red milestone-orange').addClass('milestone-blue');
            }
        }
        $(this).removeClass('last-item');
        $(this).addClass('active-item');
        var _item_id = $(this).data('id');
        $.ajax({
           url : "<?php echo $html->url('/project_milestones_preview/change_milestone_status/'.$flash_data['project_id']); ?>" + '/' + _item_id,
            type : 'GET',
            data : '',
            success : function(respons){
                respons = $.parseJSON(respons);
                var success = respons['result'];
                if( success){
                    var item = respons.data.ProjectMilestone;
                    var item_id = item.id;
                    console.log(item.validated );
                    var data = [], selectedRows = 0;
                    dataGrid.resetActiveCell();
                    var dataView = dataGrid.getDataView();
                    var tab_length = dataView.getLength();
                    dataView.beginUpdate();

                    var i = 0;
                    for( i =0; i< dataView.getLength(); i++){
                        // console.log(i);
                        // console.log(dataView.getItem(i));
                        data[i] = dataView.getItem(i)
                        //console.log(data[i]['id'] == item_id);
                        if( data[i]['id'] == item_id){
                            data[i].validated = (item.validated == 1) ? 'yes' : 'no';
                            selectedRows = i;
                        }
                    }
                    dataView.setItems(data);
                    dataView.endUpdate();
                    dataGrid.setSelectedRows(selectedRows);
                    dataGrid.render();
                    var _icon = $('.milestone-icon[data-itemid="' + _item_id + '"]');
                    _icon.removeClass('milestone-blue milestone-mi milestone-red milestone-orange milestone-green').addClass('milestone-'+color).attr('data-color','milestone-row-'+color);
                    var _row = _icon.closest('.slick-row');
                    _row.removeClass('milestone-row-mi milestone-row-blue milestone-row-green').addClass('milestone-row-'+color);



                    // Viet add code last-update here
                    $('#milestone-slider, .wd-table-container').removeClass('loading');
                }
                else{
                    location.reload();
                }
            },
            error: function(){
                location.reload();
            }
        });
    });
    function collapseScreen() {
        $('#collapse').hide();
        $('.wd-flash-wrapper').removeClass('treeExpand');
        isFull = false;
        $(window).trigger('resize');
    }
    function expandScreen() {
        $('.wd-flash-wrapper').addClass('treeExpand');
        $('#collapse').show();
        isFull = true;
        $(window).trigger('resize');
    }
    function pdfExport(){
        $('.wd-content-left, .flash-field-edit, .flash-field-addnew').css('display', 'none');
        $('.wd-flash-wrapper').css({"margin-top": "50px", "max-width": "1440px"});
        $('.wd-flash-wrapper').html2canvas();
    }
</script>
<div id="collapse" style="padding:4px; cursor:pointer; background-color:#FFF; display:none; position: fixed; top:0; right:0; z-index:9999999999" onclick="collapseScreen();" >
    <button class="btn btn-esc"></button>
</div>
<div id="overlay-container">
    <div id="overlay-wrapper"></div>
    <div id="overlay-box">
        Please wait, Preparing export ...
    </div>
</div>