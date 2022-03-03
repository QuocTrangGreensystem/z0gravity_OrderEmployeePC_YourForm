<?php echo $html->css('jqwidgets/jqx.base'); ?>
<?php echo $html->css('dropzone.min'); ?>
<?php echo $html->script('jqwidgets/jqxcore'); ?>
<?php echo $html->script('jqwidgets/jqxsortable'); ?>
<?php echo $html->script('jqwidgets/jqxkanban'); ?>
<?php echo $html->script('jqwidgets/jqxdata'); ?>
<?php echo $html->script('jqwidgets/demos'); ?>
<?php echo $html->script('dropzone.min'); ?>
<?php 
echo $this->element('dialog_detail_value');
echo $html->css('slick'); 
echo $html->css('slick-theme');  
echo $html->script('slick.min'); 
?>

<?php

if( empty( $listEmployeeNames)) $listEmployeeNames = array();
$widget_title = !empty( $widget_title) ? $widget_title : __('Status', true);
$canModified = isset( $canModified ) ? $canModified : '0';
function weather_select($projectWeatherStatus, $field){
	ob_start();
	?>
<div class="wd-dropdown" data-container="row">
    <span class="selected">
        <span class="wd-weather <?php echo !empty($projectWeatherStatus[0]['ProjectAmr'][$field]) ? $projectWeatherStatus[0]['ProjectAmr'][$field] : 'sunny';?>"></span>
    </span>
    <ul class="popup-dropdown">
        <li>
            <a data-value="sun" class="wd-weather sunny <?php if( $projectWeatherStatus[0]['ProjectAmr'][$field] == 'sunny' || $projectWeatherStatus[0]['ProjectAmr'][$field] == 'sun' || empty($projectWeatherStatus[0]['ProjectAmr'][$field])) echo 'active';?>" data-class="wd-weather sunny"></a>
        </li>
        <li>
            <a data-value="fair" class="wd-weather fair <?php if( $projectWeatherStatus[0]['ProjectAmr'][$field] == 'fair') echo 'active';?>" data-class="wd-weather fair"></a>
        </li>
        <li>
            <a data-value="cloud" class="wd-weather cloudy <?php if( $projectWeatherStatus[0]['ProjectAmr'][$field] == 'cloudy' || $projectWeatherStatus[0]['ProjectAmr'][$field] == 'cloud') echo 'active';?>" data-class="wd-weather cloudy"></a>
        </li>
        <li>
            <a data-value="furry" class="wd-weather furry <?php if( $projectWeatherStatus[0]['ProjectAmr'][$field] == 'furry') echo 'active';?>" data-class="wd-weather furry"></a>
        </li>
        <li>
            <a data-value="rain" class="wd-weather rain <?php if( $projectWeatherStatus[0]['ProjectAmr'][$field] == 'rain') echo 'active';?>" data-class="wd-weather rain"></a>
        </li>

    </ul>
</div>
	<?php echo ob_get_clean();
}
function calc_time($updated){
	$_diff = time() - $updated ;
	$icon = '<i class="icon-clock"></i>';
	$time = '';
	if( $_diff < 0 ) { 
		$time =  '0 '. __('cmMinutes', true);
	}elseif($_diff < 3600*24*31){ // dưới 1 tháng
		if( $_diff < 3600){
			$time = ($_diff <= 120) ? ( '1 ' . __('cmMinute', true) ) : (( (int)( $_diff /60) ) . ' ' . __('cmMinutes', true));
		}elseif($_diff < 3600*24 ){
			$time = ($_diff <= 7200) ? ( '1 ' . __('cmHour', true) ):( ( (int)( $_diff /3600 )) . ' ' . __('cmHours', true));
		}else{
			$time = ($_diff <= 2*3600*24) ? ('1 ' . __('cmDay', true)) : (( (int)( $_diff /(3600*24)) ) . ' ' . __('cmDays', true));
		}
	}else{ // trên 1 tháng
		$t = 3600*24;
		$curr_date = new DateTime('now');
		$updated_date = new DateTime('@'.$updated);
		if( $_diff < 365 * $t){
			$jdiff = $curr_date->format('n') - $updated_date->format('n');
			if( $jdiff  <=0 ) $jdiff +=12;
			$time = ($jdiff  < 2) ? ('1 ' . __('cmMonth', true)) : ((int) $jdiff . ' ' .  __('cmMonths', true));
		}else{
			$jdiff = $curr_date->format('Y') - $updated_date->format('Y');
			$time = ($jdiff  < 2) ? ('1 ' . __('cmYear', true)) : ((int) $jdiff . ' ' .  __('cmYears', true));	
		}
	}
	echo $time.$icon;
}
function display_comment($UserFile, $comments){
	if( empty( $comments)) return;
	$comments = Set::sort( $comments, '{n}.updated', 'desc');
	$comment = $comments[0];
	$employee_id = $comment['employee_id'];
	
	ob_start(); ?>
	<div class="employee">
	<?php echo $UserFile->avatar_html($employee_id);?>
	</div>
	<div class="comment-text"><p><?php
			// echo trim( strip_tags($comment['description'], '<br>'));
			echo $comment['description'];
			?></p>
	</div>
	<div class="comment-time">
			<?php
			echo calc_time($comment['updated']);
			?>
	</div>
		<?php
		echo ob_get_clean();
	}
?>
<div class="wd-widget project-status-widget">
    <div class="wd-widget-inner">
        <div class="widget-title">
            <h3 class="title"> <?php echo $widget_title; ?> </h3>
            <div class="widget-action">
                <a href="javascript:void(0);" onclick="wd_status_expand(this)" class="primary-object-expand"><img src="/img/new-icon/expand_white.png"></a>
                <a href="javascript:void(0);" onclick="wd_status_collapse(this)" class="primary-object-collapse" style="display: none;"><img src="/img/new-icon/close-light.png"></a>
            </div>
        </div>
        <div class="widget_content loading-mark">
            <div class="wd-project-status">
                <?php 
				if( empty( $stt_models)){
					echo '<p class="widget-empty">'. __('Enable at least one field on settings to view content') .'</p>';
					return;
				} ?>
                <div class="list-status-table">
					<?php foreach($stt_models as $index => $value){
						$model = $value['model'];
						$is_display = $value['model_display'];
						if( $is_display){
							$_log = !empty( $logGroups[$model]) ? $logGroups[$model] : array();

						?>
                    <div class="row <?php echo $model; ?>" data-model="<?php echo $model; ?>" data-field="<?php echo strtolower($model).'_weather'; ?>">
                        <div class="title"><?php __d(sprintf($_domain, 'KPI'), $model); ?></div>
                        <div class="weather"><?php echo weather_select($projectWeatherStatus, $model); ?></div>
                        <div class="comment" onclick="show_status_comment_popup(this, '<?php echo $model;?>')"><?php display_comment( $this->UserFile, $_log);?></div>
							<?php if( $canModified) { ?>
                        <div class="add-new add-new-comment"><a href="javascript:void(0);" onclick="show_status_comment_popup(this, '<?php echo $model;?>')" ></a></div>
							<?php } ?>
                    </div>
						<?php } ?>
					<?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="status_comment_logs" class="loading-mark" style="height: auto; width: 320px;display: none;">
    <div class="add-comment"></div>
    <div class="content_comment" style="min-height: 50px">
        <div class="append-comment"></div>
    </div>

</div>
<script type="text/javascript">
    var project_id = <?php echo $project_id;?>;
    var status_comment_popup = $('#status_comment_logs');
    $('.wd-project-status .wd-dropdown .selected').on('click', function () {
        var cont = $(this).closest('.row');
        cont.addClass('wd-on-top').siblings().removeClass('wd-on-top').find('.wd-dropdown').removeClass('open');
    });
	var listEmployeeNames = <?php echo json_encode($listEmployeeNames); ?>;
	function wd_status_expand(_element) {
        var _this = $(_element);
        var _wg_container = _this.closest('.wd-widget');
        _wg_container.addClass('fullScreen');
        _wg_container.closest('li').addClass('wd_on_top');
        _this.hide();
        _wg_container.find('.primary-object-collapse').show();
		_this.closest('.wd-col').css('width', '100%').siblings().hide();
		_this.closest('.wd-row').siblings().hide();
		$('#wd-container-header-main, .wd-indidator-header').hide();
		$('#layout').addClass('widget-expand');
    }
    function wd_status_collapse(_element) {
        var _this = $(_element);
        var _wg_container = _this.closest('.wd-widget');
        _wg_container.removeClass('fullScreen');
        _wg_container.closest('li').removeClass('wd_on_top');
        _this.hide();
        _wg_container.find('.primary-object-expand').show();
		_this.closest('.wd-col').css('width', '').siblings().show();
		_this.closest('.wd-row').siblings().show();
		$('#wd-container-header-main, .wd-indidator-header').show();
		$('#layout').removeClass('widget-expand');
		$('#expand').show();
		$('#table-collapse').hide();
    }
    $('.weather').on('click', 'a.wd-weather', function (e) {
        var _this = $(this);
        var _field = $(this).closest('.row').data('field');
        var _value = _this.data('value');
        $.ajax({
            url: '<?php echo $this->Html->url(array('controller' => 'project_amrs_preview', 'action' => 'updateWeather'));?>',
            type: 'POST',
            data: {
                data: {
                    project_id: project_id,
                    field: 'data[ProjectAmr][' + _field + '][]',
                    value: _value
                },
            },
            dataType: 'json',
            success: function (data) {
                if (data != 1) {
                    _this.closest('.wd-dropdown').find('.error').removeClass('error');
                    _this.addClass('error');
                    _this.closest('.wd-dropdown').find('.selected').addClass('error');
                } else {
                    _this.removeClass('error');
                    _this.closest('.wd-dropdown').find('.selected').removeClass('error');
                }
            }

        });
    });
    function show_status_comment_popup(element, model) {
        field = $(this).data("field");
        var latest_update = '';
        var log_id = $(element).data('log-id');
        // console.log($(element).closest('.list-status-table'));
        $(element).closest('.loading-mark').addClass('loading');
        var _title = $(element).siblings('.title').text();
        var text_modified = '<?php __('Modified');?>';
        var text_by = '<?php __('by');?>';
        var canModified = <?php echo json_encode($canModified);?>;
        $.ajax({
            url: '/projects_preview/getComment',
            type: 'POST',
            data: {
                id: project_id,
                model: model,
                log_id: log_id
            },
            dataType: 'json',
            success: function (data) {
                _html = '';//'<p class="project-name">' + data['project_name'] + '</p>';
				_html += '<div class="comment" style="margin-bottom:15px; height:auto;" ><textarea  data-id = ' + project_id + ' data-field = ' + model + ' cols="30" rows="6" id="update-status-comment"></textarea></div>';
                _html += '<div class="content-logs">';
                if (data['comment']) {
					_html += '<div class="comment-lists" style=" max-height: 200px; overflow-y: scroll;">';
                    $.each(data['comment'], function (ind, _data) {

						comment = _data['description'] ? _data['description'].replace(/\n/g, "<br>") : '';
						date = _data['updated'];
						var name = listEmployeeNames[_data['employee_id']];
						var src = js_avatar( _data['employee_id']);
						var avatar = '<span class="circle-name" title="' + name + '"><img src="' + src + '" title = "' + _data['name'] + '" /></span>';
						
						_html += '<div class="content"><div class="avatar">' + avatar + '</div><div class="item-content"><p>' + _data['name'] + '</p><div class="comment" >' + comment + '</div></div></div>';

                    });
                } else {
                    _html += '<div class="comment" style="margin-bottom:15px; height:auto;" ><textarea  data-id = ' + project_id + ' data-field = ' + model + ' cols="30" rows="6" id="update-status-comment"></textarea></div>';
                }
                _html += '</div>'
                
                status_comment_popup.find('.content_comment').html(_html);
                var createStatusDialog = function () {
                    $('#status_comment_logs').dialog({
                        position: 'center',
                        autoOpen: false,
                        modal: true,
                        width: 520,
                        minHeight: 50,
                        open: function (e) {
                            var $dialog = $(e.target);
                            $dialog.dialog({open: $.noop});
                        }
                    });
                    createStatusDialog = $.noop;
                }
                createStatusDialog();
                status_comment_popup.dialog('option', {title: (_title ? _title : '')}).dialog('open');
            },
			complete: function(){
                $(element).closest('.loading-mark').removeClass('loading');
			}
        });

    }
    
    $('body').on("change", "#update-status-comment", function () {
		var _this = $(this);
		// console.log( _this);
        var text = _this.val(),
                field = _this.data("field"),
                id = _this.data("id"),
				logid = _this.data("log-id");
                _html = _log_progress = _layout_html = '';
		var _dialog = _this.closest('.ui-dialog-content');
        if (text != '') {
			_this.closest('.loading-mark').addClass('loading');
            $.ajax({
                url: '/projects_preview/update',
                type: 'POST',
                data: {
                    data: {
                        id: id,
                        text: text,
                        field: field,
                        logid: logid,
                    }
                },
                dataType: 'json',
                success: function (result) {
                    var _data = result[0];
					name = ava_src = '';
					comment = _data['description'] ? _data['description'].replace(/\n/g, "<br>") : '';
					date = _data['updated'];
					var name = listEmployeeNames[_data['employee_id']];
					var src = js_avatar( _data['employee_id']);
					var avatar = '<span class="circle-name" title="' + name + '"><img src="' + src + '" title = "' + _data['name'] + '" /></span>';
					_html += '<div class="content"><div class="avatar">' + avatar + '</div><div class="item-content"><p>' + _data['name'] + '</p><div class="comment" >' + comment + '</div></div></div>';
					_dialog.find('.comment-lists').prepend(_html);
					_this.val('');
					
					// update to table
					var _comment_cont = $('.wd-project-status .list-status-table .row.' + field + ' .comment');
					var _employee = '<div class="employee">' + avatar + '</div>';
					var _comment = '<div class="comment-text"><p>' + comment + '</p></div>';
					var _time = '<div class="comment-time"> <?php echo '1 '.__('cmMinute', true);?> <i class="icon-clock"></i></div>';
					_comment_cont.html(_employee + _comment + _time);
                },
				complete: function(){
                    _this.closest('.loading-mark').removeClass('loading');
				}
            });
        }
    });
    var hoverTimer;
    $('.list-status-table .comment').on("mouseenter", function () {
        clearTimeout(hoverTimer);
        var _this = $(this);
        hoverTimer = setTimeout(function () {
            var model = $(_this).closest('.row').data('model');
            if (model)
                show_status_comment_popup(_this, model);
        }, 1000);
    }).on("mouseleave", function () {
        clearTimeout(hoverTimer);
    });

</script>
<style>
    /*
    .project-message-widget .ui-resizable-s {
            width: 100%;
            height: 8px;
            background-color: red;
            display: block;
            cursor: s-resize;
            position: absolute;
            bottom: -4px;
            z-index: 3;
            background: url(/img/new-icon/resizable-handle.png) center no-repeat;
    }
    .project-message-widget .zog-current{
        padding-left: 0;
        padding-right: 0;
        padding-top: 0;
    }.project-message-widget .zog-message{
        padding: 0;
        padding-top: 10px;
    }
    .zog-current .zog-current-title h3,
    .zog-message .zog-title h3{
        margin-left: 0;
        font-size: 12px;    
        font-weight: bold;  
        line-height: 18px;
        text-transform: uppercase;
            color: #C6CCCF;
    }
    .zog-comment{
        padding: 10px;
    }
    .project-message-widget .wd-zog-messages{
        padding-right: 0;
        height: 248px;
        overflow: auto;
    }
    .project-message-widget .add-message{
        border: 1px solid #E1E6E8;    
        background-color: #FFFFFF;  
        box-shadow: 0 5px 10px 1px rgba(29,29,27,0.06);
    }
    .project-message-widget .add-message textarea{
        padding: 0;
        margin: 0;
        height: 40px;
        line-height: 40px;
        width: calc(100% - 40px);
        border: none;
        resize: none;
        vertical-align: top;
        padding-left: 15px;
        padding-right: 15px;
    }
    .project-message-widget .add-message button{
        padding: 0;
        border: none;
        vertical-align: top;
        position: relative;
        top: 12px;
        cursor: pointer;
    }
    .project-message-widget .widget_content{
        padding-top: 10px;
    }
    .project-message-widget .zog-title{
        margin-bottom: -10px;
    }
    .project-message-widget button{
        background: transparent;
    }
    .project-message-widget button img{
        width: 20px;
        height: auto;
    }
    .project-message-widget button img + img{
        display: none;
        top: 10px;
    }
    .project-message-widget button.loading img{
        display: none;
    }
    .project-message-widget button.loading img + img{
        display: inline-block;
    }
    .project-message-widget .submit-btn-msg{
        width: inherit;
        height: inherit;
    }
    */
</style>