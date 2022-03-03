
<?php 

$svg_icons = array(
		'add' => '<svg xmlns="http://www.w3.org/2000/svg" width="16.002" height="16.002" viewBox="0 0 16.002 16.002"><g transform="translate(-120 -231.999)"><rect class="a" width="16" height="16" transform="translate(120 231.999)"/><path class="b" d="M21284,8418v-6h-6a1,1,0,0,1,0-2h6v-6a1,1,0,1,1,2,0v6h6a1,1,0,0,1,0,2h-6v6a1,1,0,1,1-2,0Z" transform="translate(-21157 -8171)"/></g></svg>',
		'star' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(-160 -264)"><path class="b" d="M8,1.032l2.137,4.552L15,6.293,11.5,9.932l.806,5.037L8,12.674l-4.3,2.3.806-5.038-3.5-3.638,4.859-.71L8,1.032M8,0A1.156,1.156,0,0,0,6.958.67L5.149,4.547.985,5.187A1.163,1.163,0,0,0,.333,7.146l3.051,3.145-.707,4.361A1.166,1.166,0,0,0,3.15,15.79a1.152,1.152,0,0,0,1.224.068L8,13.9l3.63,1.953a1.156,1.156,0,0,0,1.7-1.205l-.708-4.361,3.052-3.145a1.163,1.163,0,0,0-.653-1.959l-4.163-.639L9.05.67A1.154,1.154,0,0,0,8,0Z" transform="translate(159.995 263.998)"/></g></svg>',
		'message' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(-4 -4)"><rect class="a" width="16" height="16" transform="translate(4 4)"/><path class="b" d="M10.5,8h-5a.5.5,0,0,0,0,1h5a.5.5,0,1,0,0-1ZM8,0C3.581,0,0,3.134,0,7a6.7,6.7,0,0,0,3,5.459V16l4.1-2.048c.3.029.6.047.9.047,4.418,0,8-3.134,8-7S12.417,0,8,0ZM8,13H7L4,14.5V11.891A5.772,5.772,0,0,1,1,7C1,3.686,4.133,1,8,1s7,2.686,7,6S11.865,13,8,13Zm3.5-8h-7a.5.5,0,0,0,0,1h7a.5.5,0,1,0,0-1Z" transform="translate(4.001 4)"/></g></svg>',
		'search' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs></defs><g transform="translate(-272 -128)"><rect class="a" width="24" height="24" transform="translate(272 128)"/><path class="b" d="M.154,15.843a.536.536,0,0,0,.758,0L5.3,11.456A6.5,6.5,0,1,0,4.54,10.7L.154,15.084A.536.536,0,0,0,.154,15.843ZM9.5,1A5.5,5.5,0,1,1,4,6.5,5.5,5.5,0,0,1,9.5,1Z" transform="translate(276.003 132)"/></g></svg>'
	);
 
if( !function_exists('template_item_forecast')){
	function template_item_forecast($value, $projectPhases, $projectParts, $svg_icons){
		ob_start();
		?>
		<li class="task_item" data-task_id = <?php echo $value['task_id']; ?>>
			<a href="javascript:void(0)" onclick="updateTaskFavourite(<?php echo $value['task_id']; ?>, 'toggle', true)" class="toggle-favourite task-favour"><?php echo $svg_icons['star']; ?></a>
			<div class="task-content">
			<a href="javascript:void(0)" onclick="menuAddTaskToTable(this, <?php echo $value['task_id']; ?>)" title="<?php echo $value['task_title']; ?>"><?php echo $value['task_title']; ?></a>
			<?php $part = !empty($projectParts[$value['part_id']]) ? $projectParts[$value['part_id']] : '';
			if(!empty($part)){?>
				<span class="task-part"><?php echo $part; ?></span>
			<?php }
				$phase = !empty($projectPhases[$value['phase_id']]) ? $projectPhases[$value['phase_id']] : '';
				if(!empty($phase)){?>
				<span class="task-phase"><?php echo $phase; ?></span>
			<?php } ?>
			</div>
			<a href="javascript:void(0)"  onclick="getCommentTask('task', <?php echo $value['task_id']; ?>)" id="comment-task" class="no-comment task-comment">
			<?php echo $svg_icons['message']; ?></a>
		</li>
		<?php
		return ob_get_clean();
	}
}
?>

<div class="wd-copy-forecast">
<?php
	$empTask = '';
	$pcTask = '';
	$projectPhases = $data['projectPhases'];
	$projectParts = $data['projectParts'];
	foreach($data['results'] as $key => $value){
		if($value['is_profit_center']){
			$pcTask .= template_item_forecast($value, $projectPhases, $projectParts, $svg_icons);
		}else{
			$empTask .= template_item_forecast($value, $projectPhases, $projectParts, $svg_icons);
		}
	}
	
 ?>
	<div class="wd-employee-task">
		<div class="copy-search-task">
			<span class="btn-search"><?php echo $svg_icons['search']?></span>
			<input type="text" rel="no-history" placeholder="Rechercher">
		</div>
		<div class="title"><a class="filter-favour"><?php echo $svg_icons['star']; ?></a><span><?php echo __('My tasks', true); ?></span></div>
		<ul class="task-copy"><?php echo $empTask; ?></ul>
	</div>
	<div class="wd-pc-task">
		<div class="copy-search-task">
			<span class="btn-search"><?php echo $svg_icons['search']?></span>
			<input type="text" rel="no-history" placeholder="Rechercher">
		</div>
		<div class="title"><a class="filter-favour"><?php echo $svg_icons['star']; ?></a><span><?php echo __('Assigned to my team', true); ?></span></div>
		<ul class="task-copy"><?php echo $pcTask; ?></ul>
	</div>
 </div>


?>