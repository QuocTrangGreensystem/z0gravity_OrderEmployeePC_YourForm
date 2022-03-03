<?php if( file_exists(ELEMENTS . DS . 'widgets'. DS .'project_budget.ctp') ){
	echo $this->element('widgets'. DS .'project_budget', array(
		'type' => $type
	));
} ?>
<?php if( file_exists(ELEMENTS . DS . 'widgets'. DS .'project_progress_line.ctp') ){
	echo $this->element('widgets'. DS .'project_progress_line', array(
		'type' => $type
	));
} ?>