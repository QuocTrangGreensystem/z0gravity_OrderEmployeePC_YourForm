<div class="projectLivrableCategories form">
<?php echo $this->Form->create('ProjectLivrableCategory');?>
	<fieldset>
		<legend><?php __('Edit Project Livrable Category'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('livrable_cat');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('ProjectLivrableCategory.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('ProjectLivrableCategory.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Project Livrable Categories', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Project Livrables', true), array('controller' => 'project_livrables', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Livrable', true), array('controller' => 'project_livrables', 'action' => 'add')); ?> </li>
	</ul>
</div>