<p class="field-title"><?php echo __('Project Manager', true); ?></p>
<a class="wd-combobox-filter wd-combobox-pm">
	<?php if(!empty($listAssigned)){ ?>
		<?php 
		foreach($listAssigned as $id => $value){
			if($value['is_profit_center']) { ?>
				<span title="<?php echo $value['name']; ?>" data-id="<?php echo $value['id']; ?>" class="circle-name wd-dt-<?php echo $value['id']; ?>"><i class="icon-people"></i></span>
			<?php }else{ ?>
				<span data-id="<?php echo $value['id']; ?>" class="circle-name wd-dt-<?php echo $value['id']; ?>"><img title="<?php echo $value['name']; ?>" width="30" height="30" src="<?php echo $this->UserFile->avatar($value['id']); ?>" alt="avatar" title=""></span>
			<?php } 
		}
	}else{
		echo '<p>empty</p>';
	}?>
</a>