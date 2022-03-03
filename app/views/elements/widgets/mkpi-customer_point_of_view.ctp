<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo __d(sprintf($_domain, 'KPI'), 'Customer Point Of View', true);?></h3>
	</div>
	<div class="box-body">
		<ul class="list-inline"> 
			<li><input checked="true" style="width: 25px; margin-top: 8px;" <?php echo @$this->data["ProjectAmr"]["customer_point_of_view"] == 'sun' ? 'checked' : 'checked'; ?> value="sun" name="data[ProjectAmr][customer_point_of_view]" type="radio" /> <img title="Sun"  src="<?php echo $html->url('/img/sun.png') ?>"  /></li>
			<li><input type="radio" <?php echo @$this->data["ProjectAmr"]["customer_point_of_view"] == 'cloud' ? 'checked' : ''; ?> value="cloud" name="data[ProjectAmr][customer_point_of_view]" style="width: 25px;margin-top: 8px;" /> <img title="Cloud" src="<?php echo $html->url('/img/cloud.png') ?>"  /></li>
			<li><input type="radio" <?php echo @$this->data["ProjectAmr"]["customer_point_of_view"] == 'rain' ? 'checked' : ''; ?> value="rain" name="data[ProjectAmr][customer_point_of_view]" style="width: 25px;margin-top: 8px;"   /> <img title="Rain"  src="<?php echo $html->url('/img/rain.png') ?>"  /></li>
		</ul>
		<?php //echo $this->Form->radio('weather', array('div'=>false, 'label'=>false)); ?>
	</div>
</div>