<?php
echo $this->Html->script(array(
	'bootstrap-datepicker',
));
echo $this->Html->css(array(
	'bootstrap-datepicker',
	'new-design'
));
?>

<div id="wd-container-main" class="wd-project-admin">
	<div class="widget">
		<h1>Date picker</h1>
		<div class="widget-content">
			<input type="text" class="dp">
		</div>
	</div>
</div>


<script>
(function($){
	$('.dp').datepicker();
})(jQuery);
</script>