<?php if( file_exists(ELEMENTS . DS . 'widgets'. DS .'project_assign.ctp') ){
	echo $this->element('widgets'. DS .'project_assign', array(
		'type' => $type
	));
} ?>
<?php if( file_exists(ELEMENTS . DS . 'widgets'. DS .'project_objectives.ctp') ){
	echo $this->element('widgets'. DS .'project_objectives', array(
		'type' => $type
	));
} ?>