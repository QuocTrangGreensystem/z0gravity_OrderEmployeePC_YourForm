<?php
/* Input
 * @param: $project => Array
        (
            [Project] => Array
                (
                    [project_name] => ''
                    [updated] => ''
                    [last_modified] => ''
                    [update_by_employee] => ''
                    [id] => ''
                    [company_id] => ''
                    [address] => ''
					[primary_objectives] => ''
					[project_objectives] => ''
                )

        )
*/
// ob_clean(); debug($project);  exit;

if ( empty($project) ) return;
$canModified = isset( $canModified ) ? $canModified : false;
$widget_title = !empty( $widget_title) ? $widget_title : __('Primary Objectives', true);
?>


<div class="wd-widget project-primary-object-widget">
	<div class="wd-widget-inner">
		<div class="widget-title">
			<h3 class="title"> <?php echo $widget_title; ?> </h3>
			<div class="widget-action">
				<?php if( $canModified){ ?>
				<a href="javascript:void(0)" class="primary-object-edit"><img src="<?php  echo $html->url('/img/new-icon/edit-task_white.png'); ?>" /></a>
				<?php } ?> 
			</div>
		</div>
		<div class="widget_content">
			<textarea class="project-primary-object-content" rows = "20" onchange="primary_object_update(this)" data-id = "primary_objectives" <?php if(!$canModified) echo 'disabled'; ?> ><?php echo !empty($project['Project']['primary_objectives']) ? $project['Project']['primary_objectives'] : ''; ?></textarea>
		</div>
	</div>
</div>	

<script>
	$('.primary-object-edit').on('click', function(){
        var text_area = $(this).closest('.wd-widget').find('.project-primary-object-content:first');
        _val = text_area.val();
        text_area.focus().val('').val(_val);
		_scroll = text_area.prop('scrollHeight') - text_area.height();
		text_area.animate({scrollTop:_scroll}, '300');
		
	});
	function primary_object_update(_element){
		var inp = $(_element),
			value = $.trim(inp.val()),
			log_id = inp.data('id');
		var project_id = <?php echo $project['Project']['id']; ?>;
		var model = 'primary_objectives';
		if( value ){
			inp.prop('disabled', true);
			// save
			$.ajax({
				url: '<?php echo $html->url(array('controller' => 'projects_preview', 'action' => 'update_data_log')) ?>',
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
					setTimeout(function(){
						inp.css('color','');
					}, 3000);
				}
			});
		}
	}
	
	
</script>

<style>
	
	.project-primary-object-widget .widget_content .project-primary-object-content{
		width: 100%;
		height: 288px;
		padding: 20px;
		border: none;
		resize: none;
		font-size: 14px;
		line-height: 20px;
		color: #242424;
		animation: all ease 0.3s;
		background: #fff;
	}
</style>