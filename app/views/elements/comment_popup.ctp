<?php 
$icons_comment = array(
	'edit' => '<svg id="icon-modify" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
		  <rect id="box" width="24" height="24" fill="none"/>
		  <path id="icon" d="M67.066,288.393a.674.674,0,0,1-.648-.86l1.192-4.172a.669.669,0,0,1,.172-.291l8.047-8.047a2.153,2.153,0,0,1,3.04,0l.894.895a2.15,2.15,0,0,1,0,3.04L71.715,287a.671.671,0,0,1-.291.171l-4.173,1.192A.689.689,0,0,1,67.066,288.393Zm.982-1.656,2.837-.811,6.254-6.254-2.027-2.026L68.859,283.9Zm10.046-8.019.715-.715a.8.8,0,0,0,0-1.132l-.895-.9a.8.8,0,0,0-1.133,0l-.715.716Z" transform="translate(-61.392 -269.395)" fill="#7b7b7b"/>
		</svg>',
	'delete' => '<svg id="icon-close" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
	  <rect id="blank" width="24" height="24" fill="none"/>
	  <path id="icon" d="M6,13V8H1A1,1,0,0,1,1,6H6V1A1,1,0,0,1,8,1V6h5a1,1,0,0,1,0,2H8v5a1,1,0,0,1-2,0Z" transform="translate(12.001 2.1) rotate(45)" fill="#7b7b7b"/>
	</svg>'
);
$options = array(
    // 'ProjectAmr'  => __('Synthesis Comment', true),
    'Done'        => __d(sprintf($_domain, 'KPI'), 'Done', true),
    'ProjectRisk' => __d(sprintf($_domain, 'KPI'), 'Risk', true),
    'ProjectIssue' => __d(sprintf($_domain, 'KPI'), 'Issue', true),
    'ProjectAmr'  => __d(sprintf($_domain, 'KPI'), 'Comment', true),
);
?>
<style>
#template_logs.wd-synthesis-comment-dialog{
	display: block;
    position: fixed;
    top: 0px;
    /* right: -20px;*/
	right: 0;
	margin-right: 0;
    width: 670px;
    height: 800px;
    background-color: #fff;
    z-index: 11;
    padding: 30px;
    box-shadow: 0px 0px 64px rgba(0, 0, 0, 0.06);
}

#template_logs.wd-synthesis-comment-dialog .content{
	margin-top: 15px;
}
#template_logs.wd-synthesis-comment-dialog .content .item-content{
	background-color: #FCFCFC;
    border: 1px solid #E8E8E8;
    border-radius: 8px;
}
#template_logs.wd-synthesis-comment-dialog .comment{
	width: 100%;
}
#template_logs.wd-synthesis-comment-dialog .content .item-content .comment{
	background-color: #FCFCFC;
	color: #242424;
}
#template_logs.wd-synthesis-comment-dialog textarea{
	background: #FCFCFC;
	border: 1px solid #D3D3D3;
	padding: 15px;
	border-radius: 8px;
	resize: vertical;
	width: 100% !important;
	box-sizing: border-box;
}
#template_logs.wd-synthesis-comment-dialog .wd-comment-heading{
	position: relative;
	margin-bottom: 12px;
}
#template_logs.wd-synthesis-comment-dialog .wd-project-name{
	color: #242424;
	font-size: 20px;
	font-weight: 600;
}
#template_logs.wd-synthesis-comment-dialog .wd-comment-title{
	color: #1C557D;
	text-transform: uppercase;
	font-size: 12px;
	font-weight: 600;
	margin-top: 5px;
}
#template_logs.wd-synthesis-comment-dialog .wd-close{
	position: absolute;
	top: 0;
	right: -5px;
}
#template_logs.wd-synthesis-comment-dialog .cmt_edit:hover svg path,
#template_logs.wd-synthesis-comment-dialog .cmt_edit.loading svg path{
	fill: #247FC3;
}
.synthesis-logs .log-action{
	position: relative;
	display: inline-block;
}
#template_logs.wd-synthesis-comment-dialog .cmt_delete:hover svg path,
#template_logs.wd-synthesis-comment-dialog .cmt_delete.loading svg path,
#template_logs.wd-synthesis-comment-dialog .wd-close:hover svg path,
.synthesis-logs .log-field-delete:hover svg path,
.synthesis-logs .log-field-delete.loading svg path{
	fill: #F05352;
}
#template_logs.wd-synthesis-comment-dialog.loading:before{
	content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.9);
    left: 0;
    top: 0;
    z-index: 2;
}
#template_logs.wd-synthesis-comment-dialog.loading:after{
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
#template_logs.wd-synthesis-comment-dialog .content a.cmt_edit,
#template_logs.wd-synthesis-comment-dialog .content a.cmt_delete{
	position: relative;
	display: inline-block;
	width: 24px;
	height: 24px;
}
#template_logs.wd-synthesis-comment-dialog .content a.cmt_edit.loading:before {
    width: 18px;
    height: 18px;
    border: 2px solid #E1E6E8;
    border-top-color: #247FC3;
    animation: wd-rotate 2s infinite;
    content: '';
    display: inline-block;
    position: absolute;
    border-radius: 50%;
}
#template_logs.wd-synthesis-comment-dialog .content a.cmt_delete.loading:before,
.synthesis-logs .log-field-delete.loading:before{
    width: 18px;
    height: 18px;
    border: 2px solid #f1a7a7;
    border-top-color: #F05352;
    animation: wd-rotate 2s infinite;
    content: '';
    display: inline-block;
    position: absolute;
    border-radius: 50%;
	top: 1px;
	left: 1px;
}
.project-synthesis-widget .log-content ul li:not(:last-child){
	margin-bottom: 8px;
}

#template_logs.wd-synthesis-comment-dialog .content-logs .btn-form-action{
	background-color: #D3D3D3;
    line-height: 48px;
    display: inline-block;
    border-radius: 8px;
    width: 125px;
    text-align: center;
    font-size: 14px;
    text-transform: uppercase;
    color: #fff;
    font-weight: 600;
	background-image: linear-gradient(to right,#C6CCCF 0%,#C6CCCF 39%,#217FC2 64%,#217FC2 100%);
	background-size: 250%;
	transition: all 0.3s ease;
}
#template_logs.wd-synthesis-comment-dialog .content-logs .btn-form-action.btn-save{
	float: right;
	background: #217FC2;
}
#template_logs.wd-synthesis-comment-dialog .content-logs .btn-form-action.btn-cancel:hover{
	background-color: #217FC2;
    background-position: right center;
}

body #template_logs.wd-synthesis-comment-dialog .comment-lists:hover::-webkit-scrollbar,
#template_logs.wd-comment-dialog .comment textarea:hover::-webkit-scrollbar{
	width: 8px;
    height: 8px;
}
#template_logs.wd-synthesis-comment-dialog .content .item-content .comment a{
	color: #217FC2;
    font-weight: 600;
}
#template_logs .content .comment p{
	font-size: 14px;
    color: #424242;
}

<!-- Update by QuanNV  -->
#template_logs .content_comment .content-logs {
	height: 225px;
	overflow-y: auto;
}

#template_logs textarea {
	width: 100%;
}
<!-- END -->
#template_logs #content_comment .content{
	min-height: auto;
}
#template_logs #content_comment .content p{
	font-size: 14px;
	margin-bottom: 0;
}
#template_logs .log-progress{
	bottom: 0;
	height: auto;
	color: #C6CCCF;
	padding-top: 5px;
}
#template_logs .log-progress p{
	margin-bottom: 0;
}
#template_logs .comment-lists .content p.cmt_time{
	height: 20px;
	line-height: 20px;
	color: #242424;
	font-size: 12px;
	font-weight: 600;
	position: relative;
	top: 0px;
	width: calc(100% - 103px);
	display: inline-block;
	vertical-align: top;
}
#template_logs .content-logs .logs-info{
	color: #242424;
}
#template_logs .comment-lists .content .item-content {
	margin-left: 40px;
	padding: 10px;
	box-sizing: border-box;
	border-radius: 10px;
	margin-top: -9px;
}
#layout #wd-container-main .wd-layout{
	position: relative;
}
#template_logs .content-logs.loading:after {
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
#template_logs .comment{
	margin-bottom: 0;
}

#template_logs.wd-synthesis-comment-dialog .comment .ui-resizable-s {
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
#template_logs.wd-synthesis-comment-dialog .comment .wd-input-comment{
	position: relative;
	display: block;
	margin-bottom: 20px;
}
#template_logs.wd-synthesis-comment-dialog .comment .wd-input-comment .ui-wrapper{
	overflow: inherit !important;
    padding: 0 !important;
    width: 100% !important;
}
#template_logs .wd-form-buttons{
	margin-top: 20px;
}
</style> 
<div id="template_logs" style="display: none;" class="wd-comment-dialog loading_mark wd-synthesis-comment-dialog">
	<div class="wd-comment-heading">
		<p class="wd-project-name"></p>
		<p class="wd-comment-title"></p>
		<a href="javascript:void(0)" class="wd-close"><?php echo $icons_comment['delete']?></a>
	</div>
	
    <div class="add-comment"></div>
	<div class="content-logs-wrapper"></div>
	
</div>
<?php 
$height_input = ClassRegistry::init('HistoryFilter')->find('first', array(
	'recursive' => -1,
	'conditions' => array(
		'path' => 'project_synthesis_height_input_add_comment',
		'employee_id' => $employee_info['Employee']['id'],
	),
	'fields' => array('params')
));
$height_input = !empty($height_input) ? $height_input['HistoryFilter']['params'] : 145;

if( empty( $model_display )) $model_display = $options;
$count_model = count($model_display);
if( empty( $synth_i18ns ) ) $synth_i18ns = array();
for ($m=1; $m<=12; $m++) {
	$month = date('M', mktime(0,0,0,$m, 1, 2000));
	$synth_i18ns[$m] = __($month,true);
	$synth_i18ns[$month] = __($month,true);
	
}
$synth_i18ns['Your message'] = __('Your message',true);
$synth_i18ns['Reset'] = __('Reset',true);
$synth_i18ns['Send'] = __('Send',true);
$text_modified = __('Modified', true);
$text_by = __('by', true);
?>
<script type="text/javascript">
    var project_id = <?php echo json_encode(!empty($project_id) ? $project_id : 0) ?>;
    var synthesis_column = <?php echo json_encode(!empty($synthesis_column) ? $synthesis_column : 4) ?>;
    var count_model = <?php echo json_encode($count_model) ?>;
    var synth_i18ns = <?php echo json_encode($synth_i18ns); ?>;
    var height_input = <?php echo json_encode($height_input); ?>;
    var text_by = <?php echo json_encode($text_by) ?>;
    var text_modified = <?php echo json_encode($text_modified) ?>;
    var icons_comment = <?php echo json_encode($icons_comment); ?>;
    var options = <?php echo json_encode($options); ?>;
    var employee_id = <?php echo json_encode($employee_info['Employee']['id']); ?>;
	var _synthesis_resizable = $('.project-synthesis-widget .synthesis-logs');
	var wg_synthesis_height_backup = 0;
	function nl2br (str, is_xhtml) {   
		var breakTag = '<br>';    
		return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, breakTag);
	}
    function wd_synthesis_expand(_element) {
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
		
		wg_synthesis_height_backup = _synthesis_resizable.height();
		wd_synthesis_destroyresizable();
		// setTimeout( function(){
			var _height_layout = $(window).height() - _synthesis_resizable.offset().top - 21;
			_synthesis_resizable.height(_height_layout);
		// }
			// widget_after_expand();
		$('.project-synthesis-widget .log-content ul li').toggleClass('show', true).toggleClass('hidden', false);
    }
	$('.project-synthesis-widget').on('click', '.wd-synth-column', function(){
		var column = $(this).data('column');
		var _this = $(this);
		$.ajax({
			url : '/project_amrs_preview/filterColumnSynthesis',
			type: 'POST',
			data: {
				data: {
					path: 'project_synthesis_column',
					params: column,
				}
			},
			success: function(){
				synthesis_column = column;
			}
		});
		if(column == 2){
			$('.synthesis-logs .log-content').find('li:first').siblings().hide();
		}else{
			$('.synthesis-logs .log-content').find('li').show();
		}
		$('.wd-synth-column').removeClass('active');
		$('.widget_content').find('.synthesis-logs').removeClass('log-column-2 log-column-4 col-synth-2 col-synth-4');
		$('.widget_content').find('.synthesis-logs').addClass('log-column-'+ column+' col-synth-'+ column);
		_this.addClass('active');
		var width = (100 / column);
		if(count_model < 3) width = (100 / count_model);
		$('.project-synthesis-widget .widget_content').find('.log-content').css('width', width+'%');
		
	});

    function wd_synthesis_collapse(_element) {
		var _this = $(_element);
        var _wg_container = _this.closest('.wd-widget');
		_wg_container.closest('li').removeClass('wd_on_top');
		_wg_container.removeClass('fullScreen');
        _this.hide();
        _wg_container.find('.primary-object-expand').show();
		_this.closest('.wd-col').css('width', '').siblings().show();
		_this.closest('.wd-row').siblings().show();
		$('#wd-container-header-main, .wd-indidator-header').show();
		$('#layout').removeClass('widget-expand');
		wd_synthesis_initresizable();
		_synthesis_resizable.height(wg_synthesis_height_backup);
		$('#expand').show();
		$('#table-collapse').hide();
		$.each( $('.project-synthesis-widget .log-content ul'), function(i, _col){
			$.each( $(_col).find('>li'), function( i, _cm){
				var _this = $(_cm);
				var _show = i<3;
				_this.toggleClass('show', _show ).toggleClass('hidden', !_show );
			});
		});
		$('.project-synthesis-widget .synthesis-logs').scrollTop(0);
    }
    function synthesis_draw_comment(data, model, log_id, project_id){
        var popup = $('#template_logs');
        var _html = '';
		_html += '<div class="content-logs">';
		var _project_name = '';
		if( data['project_name']){
			_project_name = data['project_name'];
		}else{
			_project_name = popup.siblings('.ui-dialog-titlebar').find('.ui-dialog-title').text();
		}
		popup.find('.wd-project-name').empty().text(_project_name);
		popup.find('.wd-comment-title').empty().text(options[model]);
		var edit_comment = '';
		var comment_list = '';
		var latest_update = '';
		$.each(data['comment'], function (ind, _data) {
			if(data['comment'][0] && ind == 0){
				latest_update = new Date(_data['updated'] * 1e3).toISOString().slice(0, 10);
				
				latest_update = latest_update.split('-');
				latest_update = text_modified + ' ' + latest_update[2] + '/' + latest_update[1] + '/' + latest_update[0] + ' ' + text_by + ' ' + _data['update_by_employee'];
			}
			var name = '' , ava_src = '';
			var comment = _data['description'] ? _data['description'].replace(/\n/g, "<br>") : '';
			date = _data['updated'];
			var avatar = js_avatar( _data['employee_id']);
			ava_src += '<img width = 35 height = 35 src="' + avatar + '" title = "' + _data['name'] + '" />';
			
			comment_list += '<div class="content comment-content-' + _data['id'] + '"><div class="avatar">' + ava_src + '</div><p class="cmt_time">' + _data['name'] + '</p>';
			if(_data['employee_id'] == employee_id){
				comment_list += '<a href="javascript:void(0)" onclick="open_edit_popup(this, \'' + _data['model'] + '\')" data-project_id="'+ project_id +'" data-log-id = "' + _data['id'] + '" class="cmt_edit">'+ icons_comment['edit'] +'</a>';
				
				comment_list += '<a href="javascript:void(0)" onclick="delete_comment(this, \'' + _data['model'] + '\')" data-project_id="'+ project_id +'" data-log-id = "' + _data['id'] + '" class="cmt_delete">'+ icons_comment['delete'] +'</a>';
			}
			comment_list += '<div class="item-content"><div class="comment">' + urlify(comment) + '</div></div>';
			comment_list += '</div>';
			if( _data['id'] == log_id){
				edit_comment = _data['description'];
			}
		});
		_html += '<div class="comment"><div class="wd-input-comment"><textarea id="synthesis-input-comment" data-log-id = ' + log_id + ' data-id = ' + project_id + ' data-field = ' + model + ' rows="6" style="height: '+ height_input +'px" class="synthesis-update-comment" placeholder="'+ synth_i18ns['Your message'] +'">' + edit_comment + '</textarea>';
			_html += "</div>";
			_html += '<div class="wd-form-buttons">';
			_html += '<a class="btn-form-action btn-cancel" id="reset_button" href="javascript:void(0);">'+synth_i18ns['Reset']+'</a>';
			_html += '<a class="btn-form-action btn-save" id="save_button" href="javascript:void(0);">'+synth_i18ns['Send']+'</a>';
			_html += "</div>";
		_html += "</div>";
		_html += '<div class="comment-lists" style="overflow-y: scroll;">';
		_html += comment_list;
		_html += "</div>";
		var _html_progress = "<div class='log_popup_footer'><div class='log-progress'>";
		_html_progress += draw_progress_line(data['initDate']);
		_html_progress += "</div>";
		_html_progress += '<div class="logs-info"><p class="update-by-employee">' + latest_update + '</p></div></div>';
		_html += _html_progress;
		_html += "</div>";
		popup.find('.content-logs-wrapper').empty().html(_html);
		popup.removeClass('loading');
		var ele_cm_list = $('#template_logs').find('.comment-lists');
		if(ele_cm_list.length > 0) setHeightPopup();
	}
	$('.wd-synthesis-comment-dialog').on('click', '.wd-close',function(){
		$(this).closest('.wd-synthesis-comment-dialog').hide("slide", { direction: "right" }, 100);
	});
    function open_edit_popup(element, model) {
        var field = $(this).data("field");
        var log_id = $(element).data('log-id');
        project_id = $(element).data('project_id') ? $(element).data('project_id') : project_id;
		$(element).addClass('loading');
		var popup = $('#template_logs');
		var is_contain = $(element).closest('.wd-comment-dialog');
		if(is_contain.length == 0){
			popup.addClass('loading');
		}
		if(!popup.is(':visible')){
			popup.addClass('loading');
			popup.show("slide", { direction: "right" }, 500);
		}
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
                synthesis_draw_comment(data, model, log_id, project_id);
				input_synth_resizable();
				$(element).removeClass('loading');
            }
        });
    }
	function setHeightPopup(){
		var popup = $('#template_logs');
		var wd_height = $(window).height();
		popup.height(wd_height);
		var ele_cm_list = popup.find('.comment-lists');
		var _h = wd_height - popup.find('.wd-comment-heading').outerHeight(true) - parseInt(popup.css('padding-top')) - parseInt(popup.css('padding-bottom'));
		$.each( ele_cm_list.siblings(), function(i, _el){
			_h -= $(_el).height();
		});
		if(ele_cm_list.length > 0) ele_cm_list.height(_h);
	}
	$(window).resize(function () {
		setHeightPopup();
    });
    function delete_comment(element, model) {
		var _this = $(this);
        var cm_list = $(element).closest('.comment-lists');
        var log_id = $(element).data('log-id');
		var next_cmt = $('.synthesis-logs').find('.cmt-' + log_id).next();
		project_id = $(element).data('project_id') ? $(element).data('project_id') : project_id;
		$(element).addClass('loading');
        $.ajax({
            url: '/project_amrs_preview/delete_comment',
            type: 'POST',
            data: {
                id: project_id,
                model: model,
                log_id: log_id
            },
            dataType: 'json',
            success: function (data) {
                if(data){
					$(cm_list).find('.comment-content-'+log_id).remove();
					// remove on view dashboard
					$('ul.'+model).find('.cmt-'+log_id).remove();
					$(element).removeClass('loading');
					if(synthesis_column == 2 && next_cmt.length > 0) next_cmt.show();
					
					// Refresh on view after delete comment - Project list - slickgrid
					if(typeof ControlGrid !== 'undefined'){
						var dataView = ControlGrid.getData();
						var actCell = (ControlGrid.getActiveCell()) ? ControlGrid.getActiveCell().cell : 0;
						dataView.beginUpdate();
						var _new_data = dataView.getItems();
						$.each(_new_data, function (ind, item) {
							if (item.id == project_id) {
								var type = 'comment';
								if( model == 'Done') type = 'done';
								if( model == 'ProjectIssue') type = 'project_amr_problem_information';
								if( model == 'ProjectRisk') type = 'project_amr_risk_information';
									
								item_name = 'ProjectAmr.' + type;
								item[item_name] = data;
								item[item_name]['current'] = data['updated'];
								_new_data[ind] = item;
							}
						});
						dataView.setItems(_new_data);
						dataView.endUpdate();
						ControlGrid.invalidate();
						ControlGrid.render();
					}
					
					// Refresh on view project grid
					var project_item = $('#project-item-' + project_id);
					if(project_item.length > 0){
						project_item.find('.project-item-last-modified').find('span:first').empty().html('1 <?php __('cmMinute');?>');
						project_item.find('.project-description').empty().html(data.description).prop('title', data.description);
					}
				}
            }
        });
    }
    function show_comment_popup(element, model) {
        var _html = '';
        var latest_update = '';
        var popup = $('#template_logs');
		popup.addClass('loading');
		popup.show("slide", { direction: "right" }, 500);
		setHeightPopup();
		var _pid = $(element).data('project_id') ? $(element).data('project_id') : project_id;
        $.ajax({
            url: '/projects_preview/getComment',
            type: 'POST',
            data: {
                id: _pid,
                model: model
            },
            dataType: 'json',
            success: function (data) {
                synthesis_draw_comment(data, model, 0, _pid);
				input_synth_resizable();
				
			}
        });
    }
    $('.wd-synthesis-comment-dialog').on("click", "#reset_button", function () {
		var _this = $(this);
		var _inputComment = $('#synthesis-input-comment');
		_inputComment.val('');
		_inputComment.data('log-id', 0);
	});
    $('.wd-synthesis-comment-dialog').on("click", "#save_button", function () {
		var _this = $(this);
		var _input_add = $('#synthesis-input-comment');
        var text = _input_add.val(),
                field = _input_add.data("field"),
                project_id = _input_add.data("id"),
                logid = _input_add.data("log-id"),
                html = _layout_html = '';
        if (text != '') {
            _input_add.closest('.content-logs').addClass('loading');
            $.ajax({
                url: '/projects_preview/update',
                type: 'POST',
                data: {
                    data: {
                        id: project_id,
                        text: text,
                        field: field,
                        logid: logid,
                    }
                },
                dataType: 'json',
                success: function (result) {
					var _data = '';
					if( logid){
						$.each(result, function(ind, comm){
							if( comm['id'] == logid){
								_data = comm;
							}
						});
					}else{
						_data = result[0];
					}
					var latest_update;
                    if( _data){
						var _log_progress = '';
						latest_update = new Date(_data['updated'] * 1e3).toISOString().slice(0, 10);
						latest_update = latest_update.split('-');
						var update_by_employee = text_modified + ' ' + latest_update[2] + '/' + latest_update[1] + '/' + latest_update[0] + ' ' + text_by + ' ' + _data['update_by_employee'];
						$('.comment-content-' + _data['id']).remove();
						$('.synthesis-logs').find('ul.' + field + ' li[data-id="' + _data['id'] + '"]').remove();
						
						name = ava_src = '';
						comment = _data['description'] ? _data['description'].replace(/\n/g, "<br>") : '';
						date = _data['updated'];
						var avatar = js_avatar(_data['employee_id']);
						ava_src += '<img width = 35 height = 35 src="' + avatar + '" title = "' + _data['name'] + '" />';
						var updated = latest_update[2] + ' ' + synth_i18ns[ parseInt(latest_update[1]) ] + ' ' + latest_update[0];
			
						html += '<div class="content comment-content-' + _data['id'] + '"><div class="avatar">' + ava_src + '</div><p class="cmt_time">' + _data['name'] + '</p>';
						var action_button = '<a href="javascript:void(0)" onclick="open_edit_popup(this, \'' + _data['model'] + '\')" data-project_id="'+ project_id +'"  data-log-id = "' + _data['id'] + '" class="cmt_edit">'+ icons_comment['edit'] +'</a>';
			
						action_button += '<a href="javascript:void(0)" onclick="delete_comment(this, \'' + _data['model'] + '\')" data-project_id="'+ project_id +'"  data-log-id = "' + _data['id'] + '" class="cmt_delete">'+ icons_comment['delete'] +'</a>';
						html += action_button + '<div class="item-content"><div class="comment">' + urlify(comment) + '</div></div></div>';
						$('.comment-lists').prepend(html);
						if (update_by_employee) _log_progress += '<p class="update-by-employee">' + update_by_employee + '</p>';
						$('#template_logs .content-logs .logs-info').empty().append(_log_progress);
						_input_add.val('');
						_input_add.data( 'log-id', 0 );
						// Refresh on view after update comment - Dashboard widget
						_description = nl2br(_data['description']);
						var _layout_html = '<li class="cmt-' + _data['id'] + '" data-id="' + _data['id'] + '"><div class="log-item"><p class="circle-name">' + ava_src + '</p><div class="log-info"><span class="log-time">' + updated + '</span>'+ action_button +'</div><div class="cont_cmt" data-log-id="' + _data['id'] + '">' + urlify(_description) + '</div></div></li>';
						_ele_log = _data['model'];
						$('.synthesis-logs').find("." + _ele_log).prepend(_layout_html);
						if(synthesis_column == 2) $('.synthesis-logs').find('.cmt-' + _data['id']).siblings().hide();
						
						// Refresh on view after update comment - Project list - slickgrid
						if(typeof ControlGrid !== 'undefined'){
							var dataView = ControlGrid.getData();
							
							var actCell = (ControlGrid.getActiveCell()) ? ControlGrid.getActiveCell().cell : 0;
							dataView.beginUpdate();
							var _new_data = dataView.getItems();
							$.each(_new_data, function (ind, item) {
								if (item.id == project_id) {
									var type = 'comment';
									if( field == 'Done') type = 'done';
									if( field == 'ProjectIssue') type = 'project_amr_problem_information';
									if( field == 'ProjectRisk') type = 'project_amr_risk_information';
										
									item_name = 'ProjectAmr.' + type;
									item[item_name] = _data;
									item[item_name]['current'] = result['current'];
									// console.log(item_name);
									_new_data[ind] = item;
								}
							});
							dataView.setItems(_new_data);
							// console.log(_new_data);
							dataView.endUpdate();
							ControlGrid.invalidate();
							ControlGrid.render();
						}
						
						// Refresh on view project grid
						var project_item = $('#project-item-' + project_id);
						if(project_item.length > 0){
							project_item.find('.project-item-last-modified').find('span:first').empty().html('1 <?php __('cmMinute');?>');
							project_item.find('.project-description').empty().html(_data['description']).prop('title', _data['description']);
						}
                    
					}
                    
                },
				complete: function(){
					_this.closest('.content-logs').removeClass('loading');
				}
            });
        }
    });
    function sent_comment(element, model) {
        if (!model)
            return;
        var _this = $(element);
        var _model = model;
        var _ele_append = _this.closest('.log-content').find('ul');
        var _comment_box = _this.closest('.log-content').find('.add-comment-text');
        var loop = 3;
        var _comment = $.trim(_comment_box.val());
        if (_model && _comment) {
            _comment_box.prop('disabled', true);
            _this.addClass('loading');
            _ele_append.addClass('loading');
            _comment_box.val('');
            _this.closest('.template_logs').removeClass('active');
            $.ajax({
                url: '<?php echo $html->url(array('action' => 'update_data_log')) ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    id: '',
                    model: _model,
                    description: _comment,
                    model_id: project_id,
                },
                success: function (response) {
                    var _html = '', i = 0;
                    $.each(response, function (ind, data) {
                        if (i < 3) {
                            var src = '',
                                    employee_name = '';
                            
							var avatar = js_avatar(_data['employee_id']);
							ava_src += '<img width = 35 height = 35 src="' + avatar + '" title = "' + _data['name'] + '" />';
                    
							avatar = '<p class="circle-name">' + avatar + '</p>';
							var updated = data['updated'];
							updated = updated.split('/');
							updated = updated[0] + ' ' + synth_i18ns[ parseInt(updated[1]) ] + ' ' + updated[2];
							// var _description = _data['description'].replaceAll('\n', '</br>');
							var _description = nl2br(_data['description']);
                            _html += '<li data-id="' + data['id'] + '"><div class="log-item">' + avatar + '<span class="log-time">' + updated + '</span><a href="javascript:void(0)" onclick="delete_comment(this, \'' + data['model'] + '\')" data-log-id = "' + data['id'] + '" class="log-field-edit">'+icons_comment['delete']+'</a><div class="cont_cmt" data-log-id="' + data['id'] + '">' + _description + '</div></div></li>';
                            i++;
                        }
                    });
                    if (_html)
                        _this.closest('.log-content').find('ul').empty().append(_html);
                    _ele_append.removeClass('loading');
                },
                complete: function () {
                    _comment_box.prop('disabled', false);
                }
            });
        }
    }
    function wd_synthesis_initresizable() {
        var _max_height = 0;
        var _min_height = 280;
		var  _synthesis_resizing = 0;
		var  _synthesis_resize_timeout = 0;
        _synthesis_resizable.children().each(function () {
			var _this = $(this);
			var _height = _this.find('.log-title').outerHeight(true);
			$.each(_this.find('ul li'), function(i,t){
				_height += $(t).outerHeight(true);
			});
            _max_height = Math.max(_max_height, _height);
        });
        _max_height = _max_height + parseInt(_synthesis_resizable.css('padding-top')) + parseInt(_synthesis_resizable.css('padding-bottom'));
        _max_height = Math.max(_min_height, _max_height);
        _min_height = Math.min(_min_height, _max_height);
        _synthesis_resizable.resizable({
            handles: "s",
            maxHeight: _max_height + 60,
            minHeight: _min_height,
            resize: function (e, ui) {
                _max_height = 0;
                _min_height = 280;
                _synthesis_resizable.children().each(function () {
					var _this = $(this);
					var _height = _this.find('.log-title').outerHeight(true);
					$.each(_this.find('ul li'), function(i,t){
						_height += $(t).outerHeight(true);
					});
					_max_height = Math.max(_max_height, _height);
                });
                _min_height = Math.min(_min_height, _max_height);
                _synthesis_resizable.resizable("option", 'maxHeight', _max_height + 60);
                _synthesis_resizable.resizable("option", 'minHeight', _min_height);
				clearTimeout( _synthesis_resize_timeout);
				_synthesis_resize_timeout = setTimeout( function(){
					$('#indicator_synthesis_comment_height').val(_synthesis_resizable.height()).trigger('change');
				}, 2000);
            }
        });
        $(window).trigger('resize');
    }
	function input_synth_resizable(){
		var _max_height = 0;
        var _min_height = 280;
		var _synthesis_resize_timeout = 0;
		$('#synthesis-input-comment').resizable({
            handles: "s",
            resize: function (e, ui) {
			   $('#synthesis-input-comment').height(ui.size.height);
            },
			stop: function(e,ui) {
			  if(ui.size.height > 0){
				  height_input = ui.size.height;
				   $.ajax({
					url : '/project_amrs_preview/history_height_input_add_comment',
					type: 'POST',
					data: {
						data: {
							path: 'project_synthesis_height_input_add_comment',
							params: ui.size.height,
						}
					}
				});
			  }
		   }
        });
		$('#synthesis-input-comment').height(height_input);
	}
    function wd_synthesis_destroyresizable() {
        _synthesis_resizable.resizable("destroy");
        _synthesis_resizable.css({
            width: '',
            height: ''
        });
    }
    wd_synthesis_initresizable();
	function urlify(text) {
		 var urlRegex = /(https?:\/\/[^\s]+)/g;
		 return text.replace(urlRegex, function(url) {
			return '<a target="_blank" href="' + url + '">' + url + '</a>';
		 })
	}
	function draw_progress_line(value){
		var _css_class = (value <= 100) ? 'green-line': 'red-line';
		var display_value = Math.min(value, 100);
		var html = '<div class="wd-progress-slider ' + _css_class + '" data-value="' + value + '"> <div class="wd-progress-holder"> <div class="wd-progress-line-holder"></div></div>';
		html += '<div class="wd-progress-value-line" style="width:' + display_value +'%;"></div><div class="wd-progress-value-text"><div class="wd-progress-value-inner"><div class="wd-progress-number" style="left:' + display_value +'%;">';
		html += '<div class="text">' + Math.round(display_value) + '%</div><input class="input-progress wd-hide" value="' + value +'"  onchange="saveManualProgress(this);" onfocusout="progressHideInput(this)" />';
		html += ' </div> </div> </div> </div>';
		return html;
	}
    // To Viet
    // Nho goi function wd_synthesis_destroyresizable() khi Expand va goi function wd_synthesis_initresizable() sau khi collapse
</script>
