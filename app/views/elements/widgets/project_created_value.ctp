<?php
/* Input
 * @param: created_values
 totalValue
 sumOfTypeVal
 sumSelectedOfTypeVals
 dataProjectCreatedValsComment
 
	
*/
// ob_clean(); debug($projectMilestones); exit;
$widget_title = !empty( $widget_title) ? $widget_title : __('Created Value', true);
if( empty($created_values) ) return;
$canComment = isset( $canComment) ? $canComment : 0;
$type_names = array(
    'financial' =>  array(
		'title' => __d(sprintf($_domain, 'Created_Value'), 'Financial', true),
		'desc' => __d(sprintf($_domain, 'Created_Value'), 'How should the project appear to our stakeholders?', true)
	),
	'customer' => array(
		'title' => __d(sprintf($_domain, 'Created_Value'), 'Customer', true),
		'desc' => __d(sprintf($_domain, 'Created_Value'), 'How should the project appear to our customers?', true)
	),
    'learning' =>  array(
		'title' => __d(sprintf($_domain, 'Created_Value'), 'Learning & Growth', true),
		'desc' => __d(sprintf($_domain, 'Created_Value'), 'How can we sustain our ability to change and Improve?', true)
	),
    'business' => array(
		'title' => __d(sprintf($_domain, 'Created_Value'), 'Business Process', true),
		'desc' => __d(sprintf($_domain, 'Created_Value'), 'What business processes must the project excel at?', true)
	),
);
$sum_created_value = 0;
foreach( $sumSelectedOfTypeVals as $val){
	$sum_created_value += $val;
}
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
        ?>
        <div class="wd-value-created-block-comment">
            <div class="wd-value-created-block-comment-content" style="display: none;">
                <div class="wd-value-created-block-comment-box">
                    <ul class="comments-list">
                        <?php 
                        $comment_count = 0;
                        foreach($dataProjectCreatedValsComment as $comment){
                            if($comment['ProjectCreatedValsComment']['type_value'] == $type_value){
                                $comment_count++;
                                $avatar_img = '';
                                $employees_comment_id = $comment['ProjectCreatedValsComment']['employee_id'];
                                $name = $employees_comment_info[$employees_comment_id]['first_name'] . ' ' . $employees_comment_info[$employees_comment_id]['last_name'];
                                $avatar_img = wd_created_value_get_avatar($_this, $employees_comment_info[$employees_comment_id]);
                                $date = date('d-m-Y', $comment['ProjectCreatedValsComment']['created']);
                                $time = date('h:m:s', $comment['ProjectCreatedValsComment']['created']);
                                $comment_text = $comment['ProjectCreatedValsComment']['comment'];
                                ?>
                                    <li class="comment">
                                        <div class="comment-header">
                                            <div class="comment-avatar">
                                                <?php echo $avatar_img; ?>
                                            </div>
                                            <div class="comment-info">
                                                <h3 class="name"><?php echo $name; ?> </h3>
                                                <p class="time">
                                                    <?php echo $date;
                                                    echo ' '.__d(sprintf($_domain, 'Created_Value'), 'at', true).' ';
                                                    echo $time;
                                                    ?> 
                                                </p>
                                            </div>
                                            <div class="comment-body">
                                                <?php echo $comment_text; ?>
                                            </div>
                                        </div>

                                    </li>
                                <?php
                            }
                        }
                        if(!$comment_count){
                            ?>
                            <li class="no-comment"> <p>
                            <?php echo __d(sprintf($_domain, 'Created_Value'), 'No Comment here', true); ?>
                            </p></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <?php // display  comment icon; ?>
            <div class="wd-value-created-block-comment-header <?php if($comment_count) echo "has-comment"; ?>" data-project = '<?php echo $project_id ?>' data-type = '<?php echo $type_value; ?>' onclick="updateText.call(this);">
                <span class="icon"> <img src="/img/new-icon/mess.png" alt="Comment"></span>
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
<div class="wd-widget wd-created-value-widget">
	<div class="wd-widget-inner">
		<div class="widget-title">
			<h3 class="title"> <?php echo $widget_title; ?> </h3>
			<div class="widget-action">
				<div class="sum-created-value">
					<span class="per-number">
						<span id="value_sum"> <?php echo $sum_created_value; ?></span>/<span><?php echo $totalValue['ProjectCreatedValue']['total']; ?></span>
					</span>
					<div class="value-line-progress">
						<span class="value-line value-line-dark">
							<?php for( $index = 0; $index <4; $index++){?>
								<span class="per-item" data-value="<?php echo 0.25*($index+1); ?>"></span>
							<?php } ?> 
						</span>
						<div class="value-line-light" style="width: <?php echo round($sum_created_value/$totalValue['ProjectCreatedValue']['total']*100,2) . '%';?>">
							<span class="value-line ">
								<?php for( $index = 0; $index <4; $index++){?>
									<span class="per-item" data-value="<?php echo 0.25*($index+1); ?>"></span>
								<?php } ?> 
							</span>
						</div>
					</div>
				</div>
				<a href="javascript:void(0);" onclick="wd_widget_expand(this)" class="wd-widget-expand"><img src="/img/new-icon/expand_white.png"></a>
				<a href="javascript:void(0);" onclick="wd_widget_collapse(this)" class="wd-widget-collapse" style="display: none;"><img src="/img/new-icon/close-light.png"></a>
			</div>
		</div>
		<div class="widget_content">
			<div class="wd-container-created-value">
				<div class="wd-container-block-list">
					<form id="createdValue" action="<?php echo $html->url(array("controller" => "project_created_vals", "action" => "saveCreated")) ?>">
						<input type="hidden" value="<?php echo $project_id ?>" name="data[project_id]" />
						<input type="hidden" id="sum_hidden" value="<?php echo $sum_created_value; ?>" name="data[value]" />
						<div class="wd-block-content">
						
<?php 
$index = 1;
foreach ($type_names as $type_value => $type_name){ ?>
	<div class="wd-block-list wd-block-list-0<?php echo $index; ?>">
		<div class="block-header">
			<div class="wd-block-score">
				<span class="value"><?php echo !empty($sumSelectedOfTypeVals[$type_value]) ? $sumSelectedOfTypeVals[$type_value] : 0; ?></span>
				/
				<span class="value_sum"><?php echo !empty($sumOfTypeVal[$type_value]) ? $sumOfTypeVal[$type_value] : 0; ?></span>
			</div>
			<h2 class="wd-title-value"><?php echo $type_name['title']; ?></h2>
			
			<?php wd_show_createdValue_comment($this, $type_value); ?>
			<div class="clearfix">
				<p class="wd-question-value"><?php echo $type_name['desc']; ?></p>
				<div class="expand-content <?php //if( $index == 1 ) echo 'expand'; ?> ">
					<a href="javascript:;" onclick="expandContent(this);">
						<img title="<?php __('Expand Content');?>"  class="wg-cv-expand" src="<?php echo $html->url('/img/new-icon/inactive-dark.png'); ?>"/>
						<img title="<?php __('Collapse Content');?>"  class="wg-cv-collapse" src="<?php echo $html->url('/img/new-icon/active-dark.png'); ?>"/>
					</a>
				</div>
			</div>
		</div>
		<div id="wd-container-scroll-0<?php echo $index;?> " class="wd-container-scroll" style="display: none;">
			<div class="customScrollBox">
				<div class="container">
					<div class="content">
						<div class="wd-check-box-value">
							<?php
							$count = count($created_values);
							$opened = 0;
							$n = 0;
							foreach ($created_values as $created_value):
								$checked = false;
								if ($created_value['ProjectCreatedValue']['type_value'] == $type_value):
									if($created_value['ProjectCreatedValue']['next_block'] == 1){
										if($opened) echo '</div>';
										echo '<div class="wd-wraper"><span class="block-title">'. $created_value['ProjectCreatedValue']['block_name'] .'</span>';
										$opened = 1;
									}
									$checked = !empty( $projectC['ProjectCreatedVal']) ? in_array($created_value['ProjectCreatedValue']['id'],$projectC['ProjectCreatedVal'], true ) : $checked;
									?>
									<div class="wd-check-box wd-input wd-custom-checkbox">
										<label><input type="checkbox" rel="<?php echo $created_value['ProjectCreatedValue']['value'] ?>" name="data[created_value][]" class="checkbox" value="<?php echo $created_value['ProjectCreatedValue']['id'] ?>" <?php if($checked ) echo 'checked="checked"';?> /> <span class="wd-checkbox"></span> <?php echo $created_value['ProjectCreatedValue']['description'] ?> <span class="value">(<?php echo $created_value['ProjectCreatedValue']['value'] ?>)</span></label>
									</div>
									
									
									<?php
								endif;
								$n++;
							endforeach;
							if($n == $count && $opened == 1){
								echo '</div>';
							}
							?>
						</div>
						
					</div>
				</div>				
			</div>
		
		</div>
			
			
	</div>
	<?php 
	$index++;
} ?>

			
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>	
<div id="template_created" class="template-comment-vals" style="height: 420px; width: 320px;display: none;">
    <div class="add-comment"></div>
    <div id="content_comment" style="min-height: 50px">
        <div class="append-comment"></div>
    </div>

</div>
<script>
	var canComment = <?php echo json_encode($canComment); ?>;
	function updateText() {
        project_id = $(this).data("project");
        type = $(this).data("type");
        var _html = '';
        var latest_update = '';
        var popup = $('#template_created');
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
                    _html += '<div class="comment-input"><textarea data-project_id = ' + project_id + ' data-type_value = ' + type + ' cols="30" rows="6" id="update-comment"></textarea></div>';
                    _html += '<div class="content-logs">';
					var i = 0;
					$.each(data, function (ind, _datas) {
						_data = _datas['ProjectCreatedValsComment'];
						name = ava_src = '';
						comment = _data['comment'] ? _data['comment'].replace(/\n/g, "<br>") : '';
						date = _data['created'];
						date = new Date(_data['created'] * 1e3).toISOString().slice(0, 10);
						var _src = js_avatar(_data['employee_id']);
							ava_src += '<img width = 35 height = 35 src="' + _src + '" title = "" />';
						_html += '<div class="content content-' + i++ + '"><div class="avatar">' + ava_src + '</div><div class="item-content"><p>' + date + '</p><div class="comment-desc">' + comment + '</div></div></div>';
						i++;
					});
                    _html += '</div>';
                }
                $(popup).find('#content_comment').html(_html);

                var createDialog_created = function () {
                    $('#template_created').dialog({
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
                    createDialog_created = $.noop;
                }
                createDialog_created();
                $("#template_created").dialog('option', {title: ''}).dialog('open');

            }
        });

    }
	function wd_widget_expand(_element){
		var _this = $(_element);
		var _wg_container = _this.closest('.wd-widget');
		_wg_container.addClass('fullScreen');
		_wg_container.closest('li').addClass('wd_on_top');
		_this.hide();
		_wg_container.find('.wd-widget-collapse').show();
		_this.closest('.wd-col').css('width', '100%').siblings().hide();
		_this.closest('.wd-row').siblings().hide();
		$('#wd-container-header-main, .wd-indidator-header').hide();
		$('#layout').addClass('widget-expand');
		
	}
	function wd_widget_collapse(_element){
		var _this = $(_element);
		var _wg_container = _this.closest('.wd-widget');
		_wg_container.removeClass('fullScreen');
		_wg_container.closest('li').removeClass('wd_on_top');
		_this.hide();
		_wg_container.find('.wd-widget-expand').show();
		_this.closest('.wd-col').css('width', '').siblings().show();
		_this.closest('.wd-row').siblings().show();
		$('#wd-container-header-main, .wd-indidator-header').show();
		$('#layout').removeClass('widget-expand');
		$('#expand').show();
		$('#table-collapse').hide();
	}
	function update_wg_progress(sum=0){
		var _total_value = <?php echo $totalValue['ProjectCreatedValue']['total']; ?>;
		var _line_item = $('.per-line .per-item');
		var _curent_percent = Math.round(sum/_total_value*10000) / 100;
		var _progress_line = $('.value-line-light');
		_progress_line.css('width', _curent_percent + '%');
		// _line_item.each(function(){
			
		// });
	}
	function updateChecked(element){
		var _this = $(element);
		if(_this.is(':checked')){
			box = _this.closest('.wd-wraper');
			if(box.length > 0){
				_this.closest('.wd-check-box').siblings('.wd-check-box').find('span.checked').removeClass('checked');
				_this.closest('.wd-check-box').siblings('.wd-check-box').find(':checkbox').prop('checked', false).trigger('change');
			}
		}
	}
	$(function(){
        var $form = $('#createdValue'), canModified = '<?php echo ($canModified && !$_isProfile ) || ($_isProfile && $_canWrite); ?>';
		
        if(!canModified){
            $(".wd-check-box-value .wd-check-box input").attr('disabled' , true);
        }
        $form.find(':checkbox').on('change', function(){

            var sum = 0;
            var result = [];
            var box_sum_tag = $(this).closest('.wd-block-list').find('.wd-block-score .value');
			updateChecked(this);
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
                   update_wg_progress(sum);
                }
            });
            $form = $("#createdValue");
            $.ajax({
                type:'POST',
                url: "<?php echo $html->url(array("controller" => "project_created_vals", "action" => "saveCreated")) ?>" ,
                data:$form.serializeArray(),
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
        // tong = 0;
        $("#createdValue input[name='data[created_value][]']").each(function(){
        <?php if (!empty($projectC['ProjectCreatedVal'])): ?>
            <?php foreach ($projectC['ProjectCreatedVal'] as $key => $value): ?>
				<?php if (is_numeric($value)): ?>
					if($(this).val() == '<?php echo $value; ?>') {
						$(this).attr('checked','checked');
						$(this).parent().addClass('checked');
					}
				 <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
        });
        // $("#value_sum").html(tong);
        update_wg_progress(<?php echo $sum_created_value; ?>);
        // $("#sum_hidden").val(tong);

  
		/* Create by Van Viet 14-08-2018 
            Submit Comment
        */
        
		 $('body').on("focusout", "#update-comment", function () {
			if(canComment == 0) return;
			var _this = $(this);
			var text = $(this).val(),
					type_value = $(this).data("type_value"),
					project_id = $(this).data("project_id");
			if (text != '') {
				var _html = '';
				$('#update-comment').closest('#content_comment').addClass('loading');

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
								var _src = js_avatar( _data['employee_id']);
									ava_src += '<img width = 35 height = 35 src="' + _src + '" title = "" />';
								_html += '<div class="content content-' + i++ + '"><div class="avatar">' + ava_src + '</div><div class="item-content"><p>' + date + '</p><div class="comment-desc">' + comment + '</div></div></div>';
								
								
							});
							count_ele = '.number-' + type_value;
							$(count_ele).empty().html(i-1);
							$(count_ele).removeClass('hidden');
						} else {
							_html += '';
						}
						$('#template_created .content-logs').empty().append(_html);
						$('#update-comment').val('');
						$('#update-comment').closest('#content_comment').removeClass('loading');
					}

				});
			}
		});

    });
	function expandContent(_element){
		var _this = $(_element);
		_this.closest('.expand-content').toggleClass('expand');
		if( _this.closest('.expand-content').hasClass('expand') ){
			_this.closest('.wd-block-list').find('.wd-container-scroll').slideDown();
		}else{
			_this.closest('.wd-block-list').find('.wd-container-scroll').slideUp();
		}
		$(window).trigger('resize');
	}
</script>

<style>
	/* More customize */
	.wd-block-score{
		width: 60px;
		line-height: 30px;
		text-align: center;
		border-radius: 15px;
		background: #5584FF;
		display: inline-block;
		margin-bottom: 11px;
		margin-right: 6px;
		font-size: 16px;
		font-weight: 600;
		color: #fff;
	}
	.wd-container-created-value .wd-container-block-list .wd-block-list h2.wd-title-value{
		font-family: "Open Sans";   font-size: 20px;    font-weight: 600;   line-height: 30px; margin-bottom: 11px;
		display: inline-block;
		max-width: calc( 100% - 71px);
		text-overflow: ellipsis;
	}

	.content-right-inner, .wd-layout > .wd-main-content > .wd-tab > .wd-panel, .wd-section{
		background-color: transparent;
	}
	.wd-container-block-list .wd-block-list{
		/* width: 480px; */
		background-color: #FFFFFF;
		box-shadow: 0 0 20px 1px rgba(29,29,27,0.06);
		/* display: inline-block; */
		padding: 20px;
		/* vertical-align: top; */
		/* margin: 40px; */
		margin-bottom: 20px;
	}
	.wd-container-block-list .wd-block-list:last-child{
		margin-bottom: 0;
	}
	.wd-tab .wd-panel, .wd-section{
		padding: 0;
	}

	.wd-container-created-value .wd-container-block-list .wd-block-list p.wd-question-value{
		font-size: 14px;    line-height: 18px; color: #666;
	}
	.wd-check-box-value .wd-check-box span.value{
		float: right;
	}

	.wd-container-block-list #createdValue{
		position: relative;
		font-size: 14px;
	}
	.wd-container-block-list #createdValue .wd-block-content{
		/* display: flex; */
		/* flex-wrap: wrap; */
		/* justify-content: center; */
		/* margin: -40px; */
		/* z-index: 1; */
		/* position: relative; */
	}
	.wd-comment-form .comment{
		display: block;
		height: 46px;
		line-height: 46px;
		padding-left: 15px;
		padding-right: 65px;
		border-radius: 3px;
		border: 1px solid #E1E6E8;
		margin-bottom: 0;
		width: 100%;
	}
	.wd-comment-form{
		position: relative;
	}
	.wd-comment-form .submit{
		position: absolute;
		width: 50px;
		height: 100%;
		top: 0;
		right: 0;
		border: none;
		background: transparent;
	}
	#createdvalueCanvas{
		position: absolute;
		top:-5px;
		left: -5px;
	}
	.wd-value-created-block-comment{
		float: right;
		position: relative;
	}
	.wd-value-created-block-comment .number{
		position: absolute;
		width: 16px;
		height: 16px;
		color: #fff;
		background: #E7524E;
		border-radius: 50%;
		text-align: center;
		line-height: 16px;
		font-size: 12px;
		top: -4px;
		right: -8px;
	}
	/* Change color */
	.wd-block-list-01 .wd-block-score{
		background-color: #C1ABD4 !important;
	}
	.wd-block-list-01 div.checker span.checked:before{
		background-color: #C1ABD4 !important;
	}
	.wd-block-list-01 .wd-title-value{
		color: #C1ABD4 !important;
	}
	.wd-block-list-02 .wd-block-score{
		background-color: #92D487 !important;
	}
	.wd-block-list-02 div.checker span.checked:before{
		background-color: #92D487 !important;
	}
	.wd-block-list-02 .wd-title-value{
		color: #92D487 !important;
	}
	.wd-block-list-03 .wd-block-score{
		background-color: #E6721C !important;
	}
	.wd-block-list-03 div.checker span.checked:before{
		background-color: #E6721C !important;
	}
	.wd-block-list-03 .wd-title-value{
		color: #E6721C !important;
	}
	.wd-block-list-04 .wd-block-score{
		background-color: #943B9A !important;
	}
	.wd-block-list-04 div.checker span.checked:before{
		background-color: #943B9A !important;
	}
	.wd-block-list-04 .wd-title-value{
		color: #943B9A !important;
	}
	.wd-value-created-block-comment-box{
		max-height: 380px;
		overflow-y: auto;
		width: 520px;
	}
	.wd-value-created-block-comment-box .comments-list{
		padding-right: 20px;
	}
	.wd-value-created-block-comment-box .comment{
		border: 1px solid #F2F5F7;
		border-radius: 3px;
		margin-bottom: 10px;
		padding: 20px 18px;
	}
	.comment .comment-avatar{
		display: inline-block;
		width: 42px;
		height: 42px;
		border-radius: 50%;
		overflow: hidden;
		vertical-align: middle;
		margin-right: 6px;
		margin-bottom: 8px;
	}
	.comment .comment-avatar img{
		width: 100%;
		height: auto;
	}
	.wd-created-value-widget .comment .comment-info{
		display: inline-block;
		vertical-align: middle;
		width: calc( 100% - 52px);
	}
	.wd-created-value-widget .comment{
		width: 100%;
		height: inherit;
	}
	.comment .comment-info .name{
		color: #424242; 
		font-size: 16px;
		font-weight: 600;
		line-height: 18px;
	}
	.comment .comment-info .time{
		font-size: 14px;
		line-height: 18px;
		color: #c7cdd0;
	}
	.comment .comment-body{
		color: #424242;
		font-size: 14px;
		font-weight: 300;
		line-height: 18px;
		margin-bottom: 15px;
		margin-left: 53px;
	}
	.value-line-progress, .per-number{
		display: inline-block;
		line-height: inherit;
		vertical-align: top;
		margin-right: 10px;
	}
	.value-line-progress{
		height: 40px;
		position: relative;
	}
	.value-line{
		width: 100px;;
		float: left;
		display: flex;		
		padding: 17px 0;
	}
	.value-line .per-item{
		height: 6px;
		border-radius: 3px;
		background-color: #666666;
		margin: 0 1px;
		flex-grow: 1;
	}
	.value-line-light .value-line .per-item{
		background-color: #6eb07a;
	}
	.value-line-light{
		position: absolute;
		height: 100%;
		overflow: hidden;
		top:0;
		left: 0;
		width: 0;
		transition: all 0.3s ease;
	}
	.wd-question-value{
		max-width: calc( 100% - 40px);
		float: left;
		margin-bottom: 12px;
	}
	.expand-content{
		float: right;
	}
	.expand-content a{
		text-decoration: none;
	}
	.expand-content .wg-cv-expand{
		display: none;
	}
	.expand-content.expand .wg-cv-expand{
		display: block;
	}
	.expand-content.expand .wg-cv-collapse{
		display: none;
	}
	#createdValue .wd-custom-checkbox label{
		width: calc( 100% - 25px);
	}
	.wd-container-scroll .customScrollBox .wd-check-box:not(:last-child){
		margin-bottom: 10px;
	}
	#createdValue .customScrollBox{
		border: none;
	}
	.wd-value-created-block-comment-content{
		position: fixed;
		width: 100vw;
		height: 100vh;
		background: rgba( 255,255,255,0.40);
		z-index: 10;
		top: 0px;
		left: 0;
		text-align: center;
	}
	.wd-value-created-block-comment-content.open:before {
		content: '';
		width: 0;
		height:  100%;
		display:  inline-block;
		vertical-align: middle;
	}
	.wd-value-created-block-comment-content.open .wd-value-created-block-comment-box {
		max-height: 380px;
		overflow-y: auto;
		max-width: 520px;
		display: inline-block;
		vertical-align: middle;
		background: #fff;
		padding: 20px;
		padding-right: 0;
		box-shadow: 0 0 10px rgba(29,29,27,0.3);
	}
	.wd-value-created-block-comment-content .circle-name{
		width: 40px;
		height: 40px;
		font-size: 12px;
		line-height: 40px;
	}
	.wd-value-created-block-comment-content .comments-list{
		text-align: left;
	}
	.wd-block-list .wd-check-box-value .wd-wraper {
		padding: 10px;
		border: 1px solid #eee;
		display: block;
		position: relative;
		padding-bottom: 9px;
		padding-top: 15px;
	}
	.wd-block-list .wd-check-box-value .wd-wraper .block-title {
		position: absolute;
		top: -10px;
		z-index: 2;
		display: block;
		background-color: #fff;
		padding-right: 5px;
		color: #222;
		text-transform: capitalize;
	}
	.wd-block-list .wd-check-box-value .wd-wraper:first-child {
		margin-top: 5px;
	}
	.wd-block-list .wd-check-box-value .wd-wraper:not(:first-child) {
		margin-top: 20px;
	}
	#template_created #content_comment #update-comment{
		box-sizing: border-box;
	}
	#template_created.template-comment-vals .comment, #template_created.template-comment-vals #content_comment .content{
		margin-bottom: 0;
	}
	#template_created.template-comment-vals #content_comment .content-0 p{
		display: block;
	}
	#template_created.template-comment-vals #content_comment .content{
		min-height: inherit;
		margin-top: 10px;
	}
	#template_created.template-comment-vals #content_comment .content-logs{
		max-height: 200px;
		overflow: auto;
	}
	#template_created.template-comment-vals #content_comment.loading:after {
		content: '';
		position: absolute;
		width: 100%;
		height: 100%;
		top: 0;
		left: 0;
		background: rgba(255,255,255,0.75) url(/img/business/wait-1.gif) center no-repeat;
		background-size: 40px;
		z-index: 0;
		opacity: 0;
		transition: all 0.2s ease;
		z-index: 20;
		opacity: 1;
		visibility: visible;
	}
	#template_created.template-comment-vals .comment{
		margin: 0;
	}
	#template_created .content-logs .content .avatar img {
		border-radius: 50%;
	}
	#template_created #content_comment .content p{
		color: #C6CCCF;
		margin-bottom: 3px;
	}
	#template_created #content_comment .content{
		margin-bottom: 15px;
	}

	#template_created #content_comment #update-comment{
		border: 1px solid #E1E6E8;	background-color: #FFFFFF;	box-shadow: 0 0 10px 1px rgba(29,29,27,0.06); padding: 10px;
		width: 100%;
		box-sizing: border-box;
	}
	#template_created .content-logs .content .avatar,
	#template_created .content-logs .content .item-content{
		display: inline-block;
		vertical-align: top;
	}
	#template_created .content-logs .content .item-content{
		width: calc(100% - 50px);
	}
	#template_created .content-logs .content .avatar{
		margin-right: 10px;
	}
	#template_created .content-logs .content .avatar img{
		border-radius: 50%;
	}
</style>