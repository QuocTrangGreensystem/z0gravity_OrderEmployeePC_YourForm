<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->css('jquery.ui.sortable'); ?>
<?php echo $html->script('jquery.ui.touch-punch.min'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<style>
	.item {
		cursor: move;
		border: 1px solid #ccc;
		margin-bottom: 5px;
		float: left;
		clear: both;
		line-height: 30px;
		/* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#f6f8f9+0,e5ebee+50,d7dee3+51,f5f7f9+100;White+Gloss */
		background: rgb(246,248,249); /* Old browsers */
		/* IE9 SVG, needs conditional override of 'filter' to 'none' */
		background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2Y2ZjhmOSIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjUwJSIgc3RvcC1jb2xvcj0iI2U1ZWJlZSIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjUxJSIgc3RvcC1jb2xvcj0iI2Q3ZGVlMyIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiNmNWY3ZjkiIHN0b3Atb3BhY2l0eT0iMSIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+);
		background: -moz-linear-gradient(top,  rgba(246,248,249,1) 0%, rgba(229,235,238,1) 50%, rgba(215,222,227,1) 51%, rgba(245,247,249,1) 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(246,248,249,1)), color-stop(50%,rgba(229,235,238,1)), color-stop(51%,rgba(215,222,227,1)), color-stop(100%,rgba(245,247,249,1))); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top,  rgba(246,248,249,1) 0%,rgba(229,235,238,1) 50%,rgba(215,222,227,1) 51%,rgba(245,247,249,1) 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top,  rgba(246,248,249,1) 0%,rgba(229,235,238,1) 50%,rgba(215,222,227,1) 51%,rgba(245,247,249,1) 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top,  rgba(246,248,249,1) 0%,rgba(229,235,238,1) 50%,rgba(215,222,227,1) 51%,rgba(245,247,249,1) 100%); /* IE10+ */
		background: linear-gradient(to bottom,  rgba(246,248,249,1) 0%,rgba(229,235,238,1) 50%,rgba(215,222,227,1) 51%,rgba(245,247,249,1) 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f6f8f9', endColorstr='#f5f7f9',GradientType=0 ); /* IE6-8 */
	}
	.item .name {
		padding: 0 10px;
		min-width: 200px;
		display: inline-block;
	}
	.item .display {
		display: inline-block;
		padding: 0 10px;
	}
	.item.display-0 {
		border-color: #eee;
	}
	.item.display-0 .name {
		color: #777;
	}
	.msg {
		position: fixed;
		top: 40%;
		left: 40%;
		width: 20%;
		background: #fff;
		padding: 10px;
		border: 10px solid #eee;
		border-radius: 6px;
		display: none;
		color: #000;
		text-align: center;
	}
	.ui-state-highlight {
		line-height: 30px;
		margin-bottom: 5px;
		float: left;
		clear: both;
	}
	.wd-t3 h2 {
		vertical-align: middle;
	}
	#save {
		float: none;
	}
</style>
<div id="wd-container-main" class="wd-project-admin">
	<?php echo $this->element("project_top_menu") ?>
	<div class="wd-layout">
		<div class="wd-main-content">
			<div class="wd-list-project">
				<div class="wd-title">
				</div>
				<div class="wd-tab">
					<!-- version, translation... -->
					<?php echo $this->element("admin_sub_top_menu");?>
					<!-- end menu -->
					<div class="wd-panel">
						<div class="wd-section" id="wd-fragment-1">
							<?php echo $this->element('administrator_left_menu') ?>
							<div class="wd-content">
								<h2 class="wd-t3">
									<button type="button" class="wd-button-f wd-save-project" id="save">
				                        <span><?php __('Save') ?></span>
				                    </button>
								</h2>
								<p style="margin-bottom: 10px"><?php __('Drag and drop to reorder') ?></p>
							<!-- start list -->
								<ul id="sortable">
								<?php
								foreach($data as $field):
									$name = explode('|', $field);
									$field = $name[0];
									if( $field == 'project_part_id' )continue;
									$display = intval($name[1]);
									$name = __(isset($names[$name[0]]) ? $names[$name[0]] : Inflector::humanize($name[0]), true);
								?>
									<li class="item display-<?php echo $display ?>" data-field="<?php echo $field ?>">
										<span class="name"><?php echo $name ?></span>
										<span class="display"><input type="checkbox" value="1" <?php echo $display ? 'checked' : '' ?> /></span>
									</li>
								<?php endforeach; ?>
								</ul>
							<!-- end list -->
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
function serialize(){
	var data = {data:[]};
	$('.item').each(function(){
		data.data.push($(this).data('field') + '|' + ($(this).find('input').prop('checked') ? 1 : 0));
	});
	return data;
}
jQuery(document).ready(function($){
	$('#sortable').sortable({
		placeholder: "ui-state-highlight",
		forcePlaceholderSize: true
	});
	$('.display input').click(function(){
		//save
		var value = $(this).prop('checked') ? 1 : 0;
		//update UI
		$(this).closest('li').removeClass('display-0 display-1').addClass('display-' + value);
	});
	$('#save').click(function(){
		var me = $(this);
		if( me.hasClass('grayscale') )return false;
		me.addClass('grayscale');
		//ajax
		$.ajax({
			url: '<?php echo $this->Html->url('/project_phases/saveFields') ?>',
			type: 'POST',
			data: serialize(),
			complete: function(){
				me.removeClass('grayscale');
			}
		})
	});
});
</script>