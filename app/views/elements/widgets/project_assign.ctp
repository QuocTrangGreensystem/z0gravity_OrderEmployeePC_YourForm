<?php $widget_title = !empty( $widget_title) ? $widget_title : __('Participant(s)', true); 
$employeeAvatarLink = $this->Html->url(array(
		'controller' => 'employees',
		'action' => 'avatar',
		'%ID%',
		'avatar_resize', 
	));
?>
<div class="wd-widget project-assign-widget">
	<div class="wd-widget-inner">
		<div class="widget-title">
			<h3 class="title"> <?php echo $widget_title; ?> </h3>
		</div>
		<div class="widget_content">
			<ul><?php
                $name = $full_name = '';
                if(!empty($listAssign)){
                    foreach($listAssign as $key => $listEmAssig){
                    	$full_name = !empty( $listEmAssig['fullname']) ?  $listEmAssig['fullname'] : '';
                    	if($listEmAssig['is_profit_center']){ ?>
                    		<li><a class="circle-name" href ="#" title ="<?php echo 'PC / '. $full_name; ?>"><i class="icon-user"></i></a></li>
                    	<?php }else{
                    		$class_pm = !empty($list_employee_manager) && in_array($listEmAssig['id'] , $list_employee_manager) ? ' is-pm' : '';
	                    	
	                    		$src = $this->UserFile->avatar($listEmAssig['id']);?>
	                    		<li><a class="circle-name<?php echo $class_pm; ?>" href ="javascript:void(0);" title ="<?php echo $full_name; ?>"><img src="<?php echo $src; ?>" alt="avatar"/></a></li>
	                    	<?php 
                        	}
                        }
                    }
                ?>
            </ul>
		</div>
	</div>
</div>	

<style>

.project-assign-widget ul li{
	display: inline-block;
	color: #fff;
	margin-bottom: 10px;
}
.project-assign-widget ul li:not(:last-child){
	margin-right: 7px;
}
.project-assign-widget ul li a{
	color: #fff;
}
.project-assign-widget ul li a:hover{
	text-decoration: none;
}
.project-assign-widget ul li img{
	width: 100%;
	height: auto;
}
.project-assign-widget .circle-name {
    height: 40px;
    width: 40px;
    line-height: 40px;
    font-size: 12px;
}
</style>