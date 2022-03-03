<?php echo $html->css('jqwidgets/jqx.base'); ?>
<?php echo $html->script('jqwidgets/jqxcore'); ?>
<?php echo $html->script('jqwidgets/jqxsortable'); ?>
<?php echo $html->script('jqwidgets/jqxkanban'); ?>
<?php echo $html->script('jqwidgets/jqxdata'); ?>
<?php echo $html->script('jqwidgets/demos'); ?>

<?php 
echo $html->css('slick'); 
echo $html->css('slick-theme');  
echo $html->css('preview/layout');  
echo $html->css('preview/kanban-vision');  
echo $html->script('slick.min'); 
?>
<style>
	body{
		overflow: hidden;
	}
	#sub-nav{
		display: none;
	}
	.wd-list-project .wd-title{
		padding-left: 40px;
		padding-right: 40px;
	}
</style>
<?php
function task_status_select($task = null, $listStatus = array()){
	if( empty($task)) return;
	ob_start();
	$task_id = $task['id'];
	$currentStatus = $task['task_status_id'];
	$enddate = $task['task_end_date'];
	$today = strtotime(date('d-m-Y'));
	?>
	<div class="task-status">
		<div class="status_texts">
			<?php 
			$index = 0;
			foreach($listStatus as $id => $status){ 
				$color = 'status_blue';
				if( $status['status'] == 'CL') $color = 'status_green';
				elseif( $today > $enddate )  $color = 'status_red';
				?>
				<span class="status_item status_item_<?php echo $id; ?> status_text <?php if( $id == $currentStatus) echo 'active';?> <?php echo $color;?>" data-value="<?php echo $id; ?>" data-text="<?php echo $status['name']; ?>" data-index="<?php echo $index++;?>"><?php echo $status['name']; ?></span>
			<?php } ?>
		</div>
		<div class="status_dots">
			<?php 
			$index = 0;
			foreach($listStatus as $id => $status){ 
				$color = 'status_blue';
				if( $status['status'] == 'CL') $color = 'status_green';
				elseif( $today > $enddate )  $color = 'status_red';
				?>
				<a href="javascript:void(0);" class="status_item status_item_<?php echo $id; ?> status_dot <?php if( $id == $currentStatus) echo 'active';?> <?php echo $color;?>" data-value="<?php echo $id; ?>" title="<?php echo $status['name']; ?>" data-index="<?php echo $index++;?>" data-taskid="<?php echo $task_id; ?>"></a>
			<?php } ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
function  list_employee_assigned($task, $list_assigned_avatar, $listPCAssign){
	ob_start();
	?>
	<div class="task-list-assigned">
		<?php foreach ($task['assigned'] as $assigned){
			$e_id = $assigned['reference_id'];  // employee id OR PC id
			if( $assigned['is_profit_center'] == 0){
				echo $list_assigned_avatar[$e_id]['tag'];
			}else{ // is PC
				echo '<div class="circle-name" title="'. $listPCAssign[$e_id] .'"><span data-id="'. $assigned['reference_id'] .'"><i class="icon-people"></i></span></div>';
			}
		}
		?>
	</div>
	<?php
	return ob_get_clean();
}
function draw_line_progress_byday($task ){
	ob_start();
	$s_date = !empty( $task['task_start_date'] ) ? $task['task_start_date'] : 0;
	$e_date = !empty( $task['task_end_date'] ) ? $task['task_end_date'] : 0;
	$today = time();
	if( $e_date == $s_date )  $value = 100;
	else $value = intval( ($today - $s_date) / ($e_date - $s_date) * 100 );
    $_css_class = ($value < 100) ? 'green-line': 'red-line';
	$display_value = max($value, 0);
	$display_value = min($value, 100);
    ?>
		<div class="progress-slider <?php echo $_css_class;?>" data-value="<?php echo $value;?>">
			<div class="progress-holder">
				<div class="progress-line-holder"></div>
			</div>
			<div class="progress-value" style="width:<?php echo $display_value;?>%;">
				<div class="progress-line"></div>
				<div class="progress-number"> <div class="text" style="margin-left: -<?php echo round($display_value);?>%;"><?php echo round($display_value);?>%</div> </div>
			</div>
		</div>
	<?php
    return ob_get_clean();
}
function draw_line_progress_by_consumed($value=0){
	ob_start();
    $_css_class = ($value <= 100) ? 'green-line': 'red-line';
	$display_value = min($value, 100);
    ?>
		<div class="progress-slider <?php echo $_css_class;?>" data-value="<?php echo $value;?>">
			<div class="progress-holder">
				<div class="progress-line-holder"></div>
			</div>
			<div class="progress-value" style="width:<?php echo $display_value;?>%;" title="<?php echo $display_value;?>%">
				<div class="progress-line"></div>
				<div class="progress-number"> <div class="text" style="margin-left: -<?php echo round($display_value);?>%;" ><?php echo round($display_value);?>%</div> </div>
			</div>
		</div>
	<?php
    return ob_get_clean();
}

/** function display_task_footer
* by Dai Huynh
* @param $task: Task info
	* estimated: workload
	* [special] : is special consumed 
	* [special_consumed] : value consumed special 
	* [consumed] : consumed
	* [task_start_date] => 1486335600 : timestampe start day 
    * [task_end_date] => 1486422000
* @output
	*  Workload
	*  Consumed: if consumed > workload: red
		* if special consumed: purple
	* Progress: (curentime - startday) / (endday -  start day) * 100%
*/
function display_task_footer($task){
	ob_start();
	$estimated = !empty( $task['estimated']) ? $task['estimated'] : 0;
	$consumed = !empty($task['special']) ? ( isset( $task['special_consumed']) ? $task['special_consumed'] : 0 ) : ( isset($task['consumed']) ? $task['consumed'] : 0 ) ;
	$progress = 100;
	if( $estimated != 0) $progress = ($consumed / $estimated) * 100;
	elseif( empty( $consumed) ) $progress = 0;
	?>
	<div class="task-workload">
		<p class="label"> <?php __('Workload');?> </p>
		<span class="value"> <?php printf('%05.2f ' .__('M.D', true), $estimated );?></span>
	</div>
	<div class="task-consumed <?php if( !empty( $task['special'])) echo 'special';?> <?php if($consumed > $estimated) echo 'has-overload';?>">
		<p class="label"> <?php __('Consumed');?> </p>
		<span class="value"> <?php printf('%05.2f ' .__('M.D', true), $consumed );?></span>
	</div>
	<div class="task-progress" data-progress="<?php echo $progress;?>">
		<?php echo draw_line_progress_by_consumed($progress);?>
	</div>
	<?php
	
	return ob_get_clean();
}
?>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-tab">
					<div class="wd-title">
						<a href="<?php echo $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'tasks_vision_new', $this->params['pass'][0])); ?>" class="btn" title="<?php __('Task list')?>">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
							  <defs>
								<style>
								  .cls-1 {
									fill: #424242;
									fill-rule: evenodd;
								  }
								</style>
							  </defs>
							  <path id="Mode_List" data-name="Mode List" class="cls-1" d="M434,38V36h16v2H434Zm0-9h16v2H434V29Zm0-7h16v2H434V22Zm-4,14h2v2h-2V36Zm0-7h2v2h-2V29Zm0-7h2v2h-2V22Z" transform="translate(-430 -20)"/>
							</svg>
						</a>
					</div>
					<div class="wd-table project-task-widget" id="project_container">
						<div id="kanban" class="kanban-task"></div>
					</div>
                </div>
            </div>
        </div>
    </div>
</div>

 <?php
	$i18ns = array(
		'Workload' => __('Workload', true),
		'Consumed' => __('Consumed', true),
		'M.D' => __('M.D', true),
	);
 ?>
<script type="text/javascript">
	projectStatusEX = <?php echo json_encode($projectStatusEX); ?>;
	task_kanban = <?php echo json_encode($datas_value); ?>;
	list_assigned_avatar = <?php echo json_encode($employeeAssignedAvt); ?>;
	data_tasks = <?php echo json_encode($datas); ?>;
	listStatus = <?php echo json_encode($list_org_project_status); ?>;
	listPCAssign = <?php echo json_encode($listResourceAssignName); ?>;
	i18ns = <?php echo json_encode($i18ns); ?>;
	$(document).ready(function () {
		var fields = [
				 { name: "id", type: "number" },
				 { name: "status", map: "task_status_id", type: "number" },
				 { name: "text", map: "task_title", type: "string" },
				 { name: "task_id", type: "number" },
				 
		];
		var source =
		 {
			 localData: task_kanban,
			 dataType: "array",
			 dataFields: fields
		 };
		var dataAdapter = new $.jqx.dataAdapter(source);
        var resourcesAdapterFunc = function () {
            var resourcesSource =
            {
                localData: task_kanban,
                dataType: "array",
				 dataFields: [
					{ name: "id", type: "number" },
				]
            };

            var resourcesDataAdapter = new $.jqx.dataAdapter(resourcesSource);
            return resourcesDataAdapter;
        }
		var wdKanban = $('#kanban');     
		var heightKanban= $(window).height() - 150;
        $(window).resize(function(){
            var heightKanban = $(window).height() - 150;
            $('#kanban').css({ height: heightKanban});
        });
		$('#kanban').jqxKanban({
			template: "<div><div class='task-item'>"
					+ "<div class='task-head'></div>"
					+ "<div class='jqx-kanban-item-text task-title'></div>"
					+ "<div class='task-footer clearfix'></div>"
					+ "</div></div>",
			width: '100%',
			height: heightKanban,
			resources: resourcesAdapterFunc(),
			source: dataAdapter,
			itemRenderer: function(element, data, resource){
				var task_item = data_tasks[data.id];
				if(task_item){
					// Assign to
					var _html_head = '<div class="task-list-assigned">';
					var list_assign = task_item['assigned'];
					if(list_assign){
						$.each(list_assign, function(ind, _data_assign) {
							_data_assign = _data_assign.split('_');
							if(_data_assign[1] == 0){
								_html_head += list_assigned_avatar[_data_assign[0]]['tag'];
							}else{
								_html_head += '<div class="circle-name" title="'+ listPCAssign[_data_assign[0]] +'"><span data-id="'+ _data_assign[0] +'"><i class="icon-people"></i></span></div>';
							}
							
						});
						
					}
					_html_head += '</div>';
					
					// Task end date
					
					if(task_item['end_date_format']){
						_html_head += '<span class="task-time">'+ task_item['end_date_format'] +'</span>';
					}
					// Task status
					_html_head += '<div class="task-status">';
					_status_title = '<div class="status_texts">';
					_status_button = '<div class="status_dots">';
					$.each(listStatus, function(id, task_status) {
						color = 'status_blue';
						if(task_status.status == 'CL') color = 'status_green';
						else if(task_item['late'] == 1) color = 'status_red';
						active = (data.status == task_status.id) ? 'active' : '';
						
						_status_title += '<span class="status_item status_item_'+ task_status.id +' status_text '+ active +' '+ color +'" data-value="'+ task_status.id +'">'+ task_status.name +'</span>';
						_status_button += '<a href="javascript:void(0);" class="status_item status_item_'+ task_status.id +' status_dot '+ active +' '+ color +'" data-value="'+ task_status.id +'" title="'+ task_status.name +'" data-taskid="'+ data.id +'"></a>';
					});
					_status_title += '</div>';
					_status_button += '</div>';
					_html_head += _status_title + _status_button;
					_html_head += '</div>';
					
					$(element).find(".task-head").html(_html_head);
					
					// Task footer
					_html_footer = '';
					if(task_item['project_name']){
						_html_footer += '<p class="project-title">'+ task_item['project_name'] +'</p>';
					}
					workload = (task_item['workload']) ? task_item['workload'] : 0;
					consumed = (task_item['consumed']) ? task_item['consumed'] : 0;
					// consumed = Math.round(consumed, 2);
					progress = 100;
					if( workload != 0) progress = (consumed / workload) * 100;
					else if(consumed) progress = 0;
					
					_html_footer+= '<div class="task-workload"><p class="label">'+ i18ns['Workload'] +'</p><span class="value">'+ workload + i18ns['M.D']+'</span></div>';
					_html_footer+= '<div class="task-consumed"><p class="label">'+ i18ns['Consumed'] +'</p><span class="value">'+ consumed + i18ns['M.D']+'</span></div>';
					
					// Progress line
					
					_css_class = (progress <= 100) ? 'green-line': 'red-line';
					display_value = (progress < 100) ? Math.round(progress) : 100;
					
					_html_footer += '<div class="task-progress" data-progress="'+ progress +'">';
					
					_html_footer += '<div class="progress-slider '+ _css_class +'" data-value="'+ display_value +'"><div class="progress-holder"><div class="progress-line-holder"></div></div><div class="progress-value" style="width:'+ display_value +'%" title="'+ display_value +'%"><div class="progress-line"></div><div class="progress-number"> <div class="text" style="margin-left: -'+ display_value +'%;" >'+ display_value+'%</div></div></div></div>';					
					_html_footer += '</div>'
					$(element).find(".task-footer").html(_html_footer);
				}
			},
			columns: projectStatusEX,
		});
		$('#kanban').on('itemMoved', function (event) {
			console.log(event);
            var args = event.args;
            var itemId = args.itemData.id;
            var flag_id = $('#kanban_'+args.itemId).find('.flag-id').val();
            if(flag_id) itemId = flag_id;
            var newColumn = args.newColumn['dataField'];
            if(itemId && newColumn){
                $.ajax({
                    url: '/kanban/update_task_status',
                    type: 'POST',
                    data: {
                        id: itemId,
                        status: newColumn,
                    },
					dataType: 'json',
					success: function(respon){
						if( respon.result == true){
							_task_move = $('#kanban_'+itemId);
							_status_active = $('.status_item_'+ newColumn);
							_task_move.find('.status_item').removeClass('active');
							_task_move.find(_status_active).addClass('active');
							
							//move task in slider
							_task_move1 = $('.task-'+itemId);
							_status_active1 = $('.status_item_'+ newColumn);
							_task_move1.find('.status_item').removeClass('active');
							_task_move1.find(_status_active1).addClass('active');
							_item_slider = '<li class="task-'+itemId+'">'+$('#widget-task').find('.task-'+itemId).html() + '</li>';
                            $('#widget-task').find('.task-'+itemId).addClass('removed');
                            $('#widget-task').find('.task-'+itemId).removeClass('.task-'+itemId);
							$('#widget-task').find('.status-'+newColumn).append(_item_slider);
							
						}
					},
                });
            }
        });
	});
</script>