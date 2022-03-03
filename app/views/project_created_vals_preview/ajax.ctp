<?php echo $html->css('uniform.default'); ?>
<?php echo $html->css('preview/create-vals'); ?>
<?php echo $html->script('jQuery.easing1.3'); ?>
<?php echo $html->script('jquery.mousewheel'); ?>
<?php echo $html->script('jquery.mCustomScrollbar'); ?>
<?php echo $html->script('jquery.uniform.min'); ?>
<?php echo $html->script('common-value');?>
<?php
$_isProfile = !empty($employee_info['Employee']['profile_account']) ? $employee_info['Employee']['profile_account'] : 0;

/* Function wd_created_value_get_avatar
* Creat by Dai Huynh 31/05/2018
* @param $_this, $user_info
* Return string avatar image
*/
if( !function_exists('wd_created_value_get_avatar')){

    function wd_created_value_get_avatar($_this, $user_info){
        if( empty($user_info)) return;
        $avatar_img = '';
        $name = $user_info['first_name'] . ' ' . $user_info['last_name'];
        if( isset($user_info['avatar_resize'])){
            $url = $_this->UserFile->avatar($user_info['id']);
            $avatar_img = '<img src = "' .$url .'">';

        }else{
            $employee_name = explode(' ', $name);
            $url = substr( trim($employee_name[0]),  0, 1) .''.substr( trim($employee_name[1]),  0, 1);
            $avatar_img = '<p class="circle-name">' . $url . '</p>';

        }
        return $avatar_img;

    }
}
/* Function wd_show_createdValue_comment
* Creat by Dai Huynh 29/05/2018
* @param $_this, $type_value
* Display Icon with number
* Show/hide comment list on click
*/ 
if( !function_exists('wd_show_createdValue_comment')){
    function wd_show_createdValue_comment($_this, $type_value){
        
        $dataProjectCreatedValsComment = $_this->viewVars['dataProjectCreatedValsComment'];
		$project_id = $_this->viewVars['project_id'];
        $_domain = $_this->viewVars['_domain'];
        $employees_comment_info = $_this->viewVars['employees_comment_info'];
        $employee_info = $_this->viewVars['employee_info'];
		$Z0gMSG = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
		  <defs>
			<style>
			</style>
		  </defs>
		  <path id="Z0gMSG" class="cls-1" d="M683.124,30h-6.249a0.625,0.625,0,1,0,0,1.25h6.249A0.625,0.625,0,1,0,683.124,30ZM680,20c-5.523,0-10,3.918-10,8.75a8.375,8.375,0,0,0,3.75,6.824V40l5.12-2.56c0.371,0.036.747,0.059,1.13,0.059,5.523,0,10-3.917,10-8.749S685.523,20,680,20Zm0,16.25c-1.435,0-1.25,0-1.25,0L675,38.125V34.864a7.213,7.213,0,0,1-3.751-6.114c0-4.142,3.918-7.5,8.751-7.5s8.749,3.358,8.749,7.5S684.832,36.25,680,36.25Zm4.374-10h-8.749a0.625,0.625,0,1,0,0,1.25h8.749A0.625,0.625,0,1,0,684.374,26.25Z" transform="translate(-670 -20)"/>
		</svg>';

        ?>
         <div class="wd-value-created-block-comment">
             <?php 
			$comment_count = 0;
			foreach($dataProjectCreatedValsComment as $comment){
				if($comment['ProjectCreatedValsComment']['type_value'] == $type_value){
					$comment_count++;
				}
			} ?>
            <div class="wd-value-created-block-comment-header <?php if($comment_count) echo "has-comment"; ?>" data-project = '<?php echo $project_id ?>' data-type = '<?php echo $type_value; ?>' onclick="addCreatedComment.call(this);">
                <span class="icon"><?php echo $Z0gMSG; ?></span>
                <span class="number number-<?php echo $type_value; ?> <?php if(!$comment_count) echo 'hidden';?>"><?php echo $comment_count; ?></span>
            </div>
            <div class="comment_placeholder hidden" style="display:none">
                    <li class="comment">
                        <div class="comment-header">
                            <div class="comment-avatar">
                                <?php echo wd_created_value_get_avatar($_this, $employee_info['Employee']); ?>
                            </div>
                            <div class="comment-info">
                                <h3 class="name"><?php echo $employee_info['Employee']['fullname']; ?> </h3>
                                <p class="time">
                                    %TIME%
                                </p>
                            </div>
                            <div class="comment-body">
                                %TEXT%
                            </div>
                        </div>

                    </li>
            </div>
        </div>
        <?php 

    }
}
?>
<div class="wd-tab">
	<div class="wd-panel dialog_no_drag">
		<div class="wd-section">
			<?php echo $this->Session->flash(); ?>
			<div class="wd-container-created-value">
                    <div class="wd-container-block-list">
                        <form id="createdValue" action="#">
                            <div class="wd-block-created-value">
                                <p><span id="value_sum">0</span>/<span id="value_sum_total"><?php echo $totalValue['ProjectCreatedValue']['total'];?></span><canvas id="createdvalueCanvas" width="90px" height="90px"></canvas></p>
                            </div>
                            <input type="hidden" value="<?php echo $id ?>" name="data[id]" />
                            <input type="hidden" value="<?php echo $project_id ?>" name="data[project_id]" />
                            <div class="wd-block-content">
                            <div class="wd-block-list wd-block-list-01">
                                <div class="wd-block-head">
									<div class="wd-cre-content">
										<h2 class="wd-title-value"><?php echo __d(sprintf($_domain, 'Created_Value'), 'Financial', true); ?></h2>
										<p class="wd-question-value"><?php echo __d(sprintf($_domain, 'Created_Value'), 'How should the project appear to our stakeholders?', true) ?></p>
									</div>
									<div class="wd-cre-action">
										<div class="wd-block-score">
											<span class="value"><?php echo !empty($sumSelectedOfTypeVals['financial']) ? $sumSelectedOfTypeVals['financial'] : 0; ?></span>
											/
											<span class="value_sum"><?php echo !empty($sumOfTypeVal['financial']) ? $sumOfTypeVal['financial'] : 0; ?></span>
										</div>
										<?php wd_show_createdValue_comment($this,  'financial'); ?>
									</div>
                                </div>
                                <div class="wd-block-list-content">
									<div id="wd-container-scroll-01" class="wd-container-scroll">
										
											<div class="container">
												<div class="content">
													<fieldset class="wd-check-box-value">
														<?php
														$data_block = array();
														$n = 0;
														foreach ($created_values as $created_value):
															if ($created_value['ProjectCreatedValue']['type_value'] == 'financial'):
																$next_block = !empty($created_value['ProjectCreatedValue']['next_block']) ? $created_value['ProjectCreatedValue']['next_block'] : 0;
																$block_name = !empty($created_value['ProjectCreatedValue']['block_name']) ? $created_value['ProjectCreatedValue']['block_name'] : '';
																if($next_block){
																	$n++;
																}
																$data_block[$n][] = $created_value;
															endif;
														endforeach;	
														foreach ($data_block as $key => $datas):
															$block_title = '';
															$is_next = false;
															if(!empty($datas[0]['ProjectCreatedValue']['next_block']) && $datas[0]['ProjectCreatedValue']['next_block'] == 1){
																$block_title = '<span class="block-title">' . $datas[0]['ProjectCreatedValue']['block_name'] . '</span>';
																$is_next = true;
															}
															if($is_next) echo '<div class="wd-wraper">';
															echo $block_title;
															foreach ($datas as $created_value):
																if ($created_value['ProjectCreatedValue']['type_value'] == 'financial'):
																	?>
																	<div class="wd-check-box">
																		<label><input type="checkbox" rel="<?php echo $created_value['ProjectCreatedValue']['value'] ?>" name="data[created_value][]" class="checkbox" value="<?php echo $created_value['ProjectCreatedValue']['id'] ?>" /><?php echo $created_value['ProjectCreatedValue']['description'] ?> <span class="value">(<?php echo $created_value['ProjectCreatedValue']['value'] ?>)</span></label>
																		<div class="clear-fix"></div>
																	</div>
																	<?php
																endif;
															endforeach;
															if($is_next) echo '</div>';
														endforeach;
														?>
													</fieldset>
												</div>
											</div>
											<div class="dragger_container">
												<div class="dragger"></div>
											</div>
								
									</div>
                                </div>
                            </div>
                            <div class="wd-block-list wd-block-list-02">
                                <div class="wd-block-head">
									<div class="wd-cre-content">
										<h2 class="wd-title-value"><?php echo __d(sprintf($_domain, 'Created_Value'), 'Customer', true); ?></h2>
										<p class="wd-question-value"><?php echo __d(sprintf($_domain, 'Created_Value'), 'How should the project appear to our customers?', true) ?></p>
									</div>
									<div class="wd-cre-action">
										<div class="wd-block-score">
											<span class="value"><?php echo !empty($sumSelectedOfTypeVals['customer']) ? $sumSelectedOfTypeVals['customer'] : 0; ?></span>/<span class="value_sum"><?php echo !empty($sumOfTypeVal['customer']) ? $sumOfTypeVal['customer'] : 0; ?></span>
										</div>
										<?php wd_show_createdValue_comment($this, 'customer'); ?>
									</div>
								</div>
                                <div class="wd-block-list-content">
									<div id="wd-container-scroll-02" class="wd-container-scroll">
										
											<div class="container">
												<div class="content">
													<fieldset class="wd-check-box-value">
														<?php
														$data_block = array();
														
														$n = 0;
														foreach ($created_values as $created_value):
															if ($created_value['ProjectCreatedValue']['type_value'] == 'customer'):
																$next_block = !empty($created_value['ProjectCreatedValue']['next_block']) ? $created_value['ProjectCreatedValue']['next_block'] : 0;
																$block_name = !empty($created_value['ProjectCreatedValue']['block_name']) ? $created_value['ProjectCreatedValue']['block_name'] : '';
																if($next_block){
																	$n++;
																}
																$data_block[$n][] = $created_value;
															endif;
														endforeach;	
														foreach ($data_block as $key => $datas):
															$block_title = '';
															$is_next = false;
															if(!empty($datas[0]['ProjectCreatedValue']['next_block']) && $datas[0]['ProjectCreatedValue']['next_block'] == 1){
																$block_title = '<span class="block-title">' . $datas[0]['ProjectCreatedValue']['block_name'] . '</span>';
																$is_next = true;
															}
															if($is_next) echo '<div class="wd-wraper">';
															echo $block_title;
															foreach ($datas as $created_value):
															if ($created_value['ProjectCreatedValue']['type_value'] == 'customer'):
																?>
																<div class="wd-check-box">
																	<label><input type="checkbox" rel="<?php echo $created_value['ProjectCreatedValue']['value'] ?>" name="data[created_value][]" class="checkbox" value="<?php echo $created_value['ProjectCreatedValue']['id'] ?>" /><?php echo $created_value['ProjectCreatedValue']['description'] ?> <span class="value">(<?php echo $created_value['ProjectCreatedValue']['value'] ?>)</span></label>
																	<div class="clear-fix"></div>
																</div>
																<?php
															endif;
														endforeach;
														if($is_next) echo '</div>';
														endforeach;
														?>
													</fieldset>
												</div>
											</div>
											<div class="dragger_container">
												<div class="dragger"></div>
											</div>
										
									</div>
                                </div>
                            </div>
                            <div class="wd-block-list wd-block-list-03">
								<div class="wd-block-head">
									<div class="wd-cre-content">
										<h2 class="wd-title-value"><?php echo __d(sprintf($_domain, 'Created_Value'), 'Learning & Growth', true); ?></h2>
										<p class="wd-question-value"><?php echo __d(sprintf($_domain, 'Created_Value'), 'How can we sustain our ability to change and Improve?', true) ?></p>
									</div>
									<div class="wd-cre-action">
										<div class="wd-block-score">
											<span class="value"><?php echo !empty($sumSelectedOfTypeVals['learning']) ? $sumSelectedOfTypeVals['learning'] : 0; ?></span>/<span class="value_sum"><?php echo !empty($sumOfTypeVal['learning']) ? $sumOfTypeVal['learning'] : 0; ?></span>
										</div>
										<?php wd_show_createdValue_comment($this, 'learning'); ?>
									</div>
                                </div>
                                <div class="wd-block-list-content">
									<div id="wd-container-scroll-03" class="wd-container-scroll">
										
											<div class="container">
												<div class="content">
													<fieldset class="wd-check-box-value">
														<?php
														$data_block = array();
														$n = 0;
														foreach ($created_values as $created_value):
															if ($created_value['ProjectCreatedValue']['type_value'] == 'learning'):
																$next_block = !empty($created_value['ProjectCreatedValue']['next_block']) ? $created_value['ProjectCreatedValue']['next_block'] : 0;
																$block_name = !empty($created_value['ProjectCreatedValue']['block_name']) ? $created_value['ProjectCreatedValue']['block_name'] : '';
																if($next_block){
																	$n++;
																}
																$data_block[$n][] = $created_value;
															endif;
														endforeach;	
														foreach ($data_block as $key => $datas):
															$block_title = '';
															$is_next = false;
															if(!empty($datas[0]['ProjectCreatedValue']['next_block']) && $datas[0]['ProjectCreatedValue']['next_block'] == 1){
																$block_title = '<span class="block-title">' . $datas[0]['ProjectCreatedValue']['block_name'] . '</span>';
																$is_next = true;
															}
															if($is_next) echo '<div class="wd-wraper">';
															echo $block_title;
															foreach ($datas as $created_value):
															if ($created_value['ProjectCreatedValue']['type_value'] == 'learning'):
																?>
																<div class="wd-check-box">
																	<label><input type="checkbox" rel="<?php echo $created_value['ProjectCreatedValue']['value'] ?>" name="data[created_value][]" class="checkbox" value="<?php echo $created_value['ProjectCreatedValue']['id'] ?>" /><?php echo $created_value['ProjectCreatedValue']['description'] ?> <span class="value">(<?php echo $created_value['ProjectCreatedValue']['value'] ?>)</span></label>
																	<div class="clear-fix"></div>
																</div>
																<?php
															endif;
														endforeach;
														if($is_next) echo '</div>';
														endforeach;
														?>
													</fieldset>
												</div>
											</div>
											<div class="dragger_container">
												<div class="dragger"></div>
											</div>
										
									</div>
                                </div>
								
                            </div>
                            <div class="wd-block-list wd-block-list-04">
                                <div class="wd-block-head">
									<div class="wd-cre-content">
										<h2 class="wd-title-value"><?php echo __d(sprintf($_domain, 'Created_Value'), 'Business Process', true); ?></h2>
										<p class="wd-question-value"><?php echo __d(sprintf($_domain, 'Created_Value'), 'What business processes must the project excel at?', true) ?></p>
									</div>
									<div class="wd-cre-action">		
										<div class="wd-block-score">
											<span class="value"><?php echo !empty($sumSelectedOfTypeVals['business']) ? $sumSelectedOfTypeVals['business'] : 0; ?></span>/<span class="value_sum"><?php echo !empty($sumOfTypeVal['business']) ? $sumOfTypeVal['business'] : 0; ?></span>
										</div>
										<?php wd_show_createdValue_comment($this, 'business'); ?>
	
									</div>
                                </div>
                                <div class="wd-block-list-content">
									<div id="wd-container-scroll-04" class="wd-container-scroll">
										
											<div class="container">
												<div class="content">
													<fieldset class="wd-check-box-value">
														<?php
														$data_block = array();
														$n = 0;
														foreach ($created_values as $created_value):
															if ($created_value['ProjectCreatedValue']['type_value'] == 'business'):
																$next_block = !empty($created_value['ProjectCreatedValue']['next_block']) ? $created_value['ProjectCreatedValue']['next_block'] : 0;
																$block_name = !empty($created_value['ProjectCreatedValue']['block_name']) ? $created_value['ProjectCreatedValue']['block_name'] : '';
																if($next_block){
																	$n++;
																}
																$data_block[$n][] = $created_value;
															endif;
														endforeach;	
														foreach ($data_block as $key => $datas):
															$is_next = false;
															$block_title = '';
															if(!empty($datas[0]['ProjectCreatedValue']['next_block']) && $datas[0]['ProjectCreatedValue']['next_block'] == 1){
																$block_title = '<span class="block-title">' . $datas[0]['ProjectCreatedValue']['block_name'] . '</span>';
																$is_next = true;
															}
															if($is_next) echo '<div class="wd-wraper">';
															echo $block_title;
															foreach ($datas as $created_value):
															if ($created_value['ProjectCreatedValue']['type_value'] == 'business'):
																?>
																<div class="wd-check-box">
																	<label><input type="checkbox" rel="<?php echo $created_value['ProjectCreatedValue']['value'] ?>" name="data[created_value][]" class="checkbox" value="<?php echo $created_value['ProjectCreatedValue']['id'] ?>" /><?php echo $created_value['ProjectCreatedValue']['description'] ?> <span class="value">(<?php echo $created_value['ProjectCreatedValue']['value'] ?>)</span></label>
																	<div class="clear-fix"></div>
																</div>
																<?php
															endif;
														endforeach;
														if($is_next) echo '</div>';
														endforeach;
														?>
													</fieldset>
												</div>
											</div>
											<div class="dragger_container">
												<div class="dragger"></div>
											</div>
										
									</div>
                                </div>
								
                            </div>
                            </div>
                            <input type="hidden" id="sum_hidden" value="0" name="data[value]" />
                        </form>
                    </div>
                </div>
		</div>
	</div>
</div>

<script>
	var employees_comment_info = <?php echo json_encode($employees_comment_info); ?>;
	function addCreatedComment() {
        project_id = $(this).data("project");
        type = $(this).data("type");
        var _html = '';
        var latest_update = '';
        var popup = $('#template_logs');
        $.ajax({
            url: '/project_created_vals_preview/getDataProjectCreatedValsComment/'+ project_id + '/' + 0,
            type: 'POST',
            data: {
                type: type,
            },
            dataType: 'json',
            success: function (data) {
				_html = '';
                html = '<div id="content-comment-id">';
                if (data) {
                    _html += '<div class="comment"><textarea data-project_id = ' + project_id + ' data-type_value = ' + type + ' cols="30" rows="6" id="cr-add-comment"></textarea></div>';
                    _html += '<div class="content-logs">';
					var i = 0;
					$.each(data, function (ind, _datas) {
						_data = _datas['ProjectCreatedValsComment'];
						name = ava_src = '';
						comment = _data['comment'] ? _data['comment'].replace(/\n/g, "<br>") : '';
						date = _data['created'];
						date = new Date(_data['created'] * 1e3).toISOString().slice(0, 10);
						var _src = <?php echo $this->UserFile->avatarjs() ?>.replace('{id}', _data['employee_id']);
							ava_src += '<img width = 35 height = 35 src="' + _src + '" title = "" />';
						_html += '<div class="content content-' + i++ + '"><div class="avatar">' + ava_src + '</div><div class="item-content"><p>' + date + '</p><div class="comment">' + comment + '</div></div></div>';
						i++;
					});
                    _html += '</div>';
                }
                $('#content_comment').html(_html);

                var createDialog2 = function () {
                    $('#template_logs').dialog({
                        position: 'center',
                        autoOpen: false,
                        height: 420,
                        modal: true,
                        width: 520,
                        minHeight: 50,
                        open: function (e) {
                            var $dialog = $(e.target);
                            $dialog.dialog({open: $.noop});
                        }
                    });
                    createDialog2 = $.noop;
                }
                createDialog2();
                $("#template_logs").dialog('option', {title: ''}).dialog('open');

            }
        });

    }
    function update_progress(){
            var activeBorder = document.querySelector("#createdvalueCanvas");
            var cw = activeBorder.offsetWidth/2;
            var ch = activeBorder.offsetHeight/2;
            var value_sum = document.querySelector("#value_sum").textContent;
            var value_sum_total = document.querySelector("#value_sum_total").textContent;
            var context = activeBorder.getContext('2d');
            context.clearRect(0,0,2*cw,2*cw);
            context.strokeStyle= '#6EAF79';
            context.lineWidth = 10;
            context.beginPath();
            context.arc(cw,ch,40,1.5*Math.PI,(1.5 + 2*value_sum/value_sum_total)*Math.PI,false);
            context.stroke();

    }
    $(function(){
        var $form = $('#createdValue'), canModified = '<?php echo ($canModified && !$_isProfile ) || ($_isProfile && $_canWrite); ?>';
        if(!canModified){
            $(".wd-check-box-value .wd-check-box input").attr('disabled' , true);
        }
        $form.find(':checkbox').click(function(){

            var sum = 0;
            var result = [];
            var box_sum_tag = $(this).closest('.wd-block-list').find('.wd-block-score .value');
            var box_sum = 0;
            $form.find("input[type=checkbox]:checked").each(function(){
                sum +=parseInt($(this).attr('rel'));
                result.push($(this).attr('rel'));
            });
			$(this).closest('.wd-block-list').find('.wd-container-scroll input[type=checkbox]:checked').each(function(){
				box_sum += parseInt($(this).attr('rel'));
			});
            box_sum_tag.prop('value', box_sum).prop('Counter', parseInt(box_sum_tag.text()) );
			box_sum_tag.stop();
			box_sum_tag.animate({
				Counter: box_sum
			}, {
				duration: 200,
				easing: 'linear',
				step: function (now) {
				   $(this).text(Math.ceil(now));
				}
			});
			
			var sum_tag = $("#value_sum");
			$("#sum_hidden").val(sum);
			// sum_tag.html(sum);
			sum_tag.prop('value', sum).prop('Counter', parseInt(sum_tag.text()) );
			sum_tag.stop();
			sum_tag.animate({
				Counter: sum
			}, {
				duration: 200,
				easing: 'linear',
				step: function (now) {
				   $(this).text(Math.ceil(now));
				   update_progress();
				}
			});

            $form = $("#createdValue");
            $.ajax({
                type:'POST',
                url: "<?php echo $html->url(array("controller" => "project_created_vals", "action" => "saveCreated")) ?>" ,
                data: $form.serializeArray(),
                cache: false,
                success:function(content){
                    
                },
                error:function(e){
                    if(e.statusText == "error"){
                        alert('Error when update values, please check your connection and reload your page!');
                        return;
                    }
                }
            });
        });
        tong = 0;
        $("#createdValue input[name='data[created_value][]']").each(function(){
        <?php if (!empty($projectC['ProjectCreatedVal'])): ?>
            <?php foreach ($projectC['ProjectCreatedVal'] as $key => $value): ?>
                if($(this).val() == '<?php echo $value; ?>') {
                    $(this).attr('checked','checked');
                    $(this).parent().addClass('checked');
                    tong += Number($(this).attr('rel'));
                }

            <?php endforeach; ?>
        <?php endif; ?>
        });
        var sum_tag = $("#value_sum");
		$("#sum_hidden").val(tong);
		setTimeout(function(){
			sum_tag.prop('value', tong).prop('Counter', parseInt(sum_tag.text()) );
			sum_tag.stop();
			sum_tag.animate({
				Counter: tong
			}, {
				duration: 500,
				step: function (now) {
				   $(this).text(Math.ceil(now));
				   update_progress();
				}
			});
		}, 500);

        /* Create by Dai Huynh 29-05-2018 
            Submit Comment
        */
        
		 $('body').on("focusout", "#cr-add-comment", function () {
			var _this = $(this);
			var text = $(this).val(),
					type_value = $(this).data("type_value"),
					project_id = $(this).data("project_id");
			if (text != '') {
				var _html = '';
				$('#cr-add-comment').closest('#content_comment').addClass('loading');

				$.ajax({
					type: 'POST',
					url: "<?php echo $html->url(array("controller" => "project_created_vals_preview", "action" => "saveProjectCreatedValsComment")) ?>" ,
                    data: {
                        project_id: project_id,
                        type_value: type_value,
                        comment: text
                    } ,
					dataType: 'json',
					success: function (data) {
						_html = _log_progress = '';
						count_mess = 0;
						if (data) {
							i = 1;
							$.each(data, function (ind, _datas) {
								_data = _datas['ProjectCreatedValsComment'];
								name = ava_src = '';
								comment = _data['comment'] ? _data['comment'].replace(/\n/g, "<br>") : '';
								date = _data['created'];
								date = new Date(_data['created'] * 1e3).toISOString().slice(0, 10);
								var _src = <?php echo $this->UserFile->avatarjs() ?>.replace('{id}', _data['employee_id']);
									ava_src += '<img width = 35 height = 35 src="' + _src + '" title = "" />';
								_html += '<div class="content content-' + i++ + '"><div class="avatar">' + ava_src + '</div><div class="item-content"><p>' + date + '</p><div class="comment">' + comment + '</div></div></div>';
								
								
							});
							count_ele = '.number-' + type_value;
							$(count_ele).empty().html(i-1);
							$(count_ele).removeClass('hidden');
						} else {
							_html += '';
						}
						$('#template_logs .content-logs').empty().append(_html);
						$('#cr-add-comment').val('');
						$('#cr-add-comment').closest('#content_comment').removeClass('loading');
					}

				});
			}
		});
        /* Create by Dai Huynh 30-5-2018 
        * Open/close Comment box
        */
        $('.wd-value-created-block-comment').on('click', function(e){
            e.preventDefault();
            var _this = $(this);
            var _comment_content = _this.find('.wd-value-created-block-comment-content');
            if( _comment_content.length != ''){
                _comment_content.toggleClass('open').fadeToggle(300);
            }
            $('body').on('click','.wd-value-created-block-comment', function(e){
                e.stopPropagation();
            }).on('click', function(){
                $('.wd-value-created-block-comment-content.open').removeClass('open').fadeOut(300);
            })
        });



    });
</script>
