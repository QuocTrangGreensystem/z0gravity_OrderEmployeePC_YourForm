<style>
.error-message { color :red; margin: 0;}
li.error-message {padding: 4px 18px 0 0; font-size: 12px;}
#error span { 
    color :red; 
    margin: 0;
    font-family: Sans-serif;
    font-size: 12px; 
}
#table-list-admin {
    border-collapse: collapse;
}
#table-list-admin th {
    background: #eee;
}
#table-list-admin td,
#table-list-admin th {
    padding: 5px;
    border: 1px solid #ddd;
}
</style>
<?php App::import("vendor", "str_utility");
                    $str_utility = new str_utility(); ?>
<div style="overflow-y: auto;max-height:  379px;color: #000;font-size: 12px; padding: 0 10px" id="produp">
        <?php echo $form->create("Project",array('action'=>'duplicate','enctype' => 'multipart/form-data'));?>
        <table cellspacing="0" cellpadding="0" id="table-list-admin" class="display"  >
        	<thead>
    			<tr class="wd-header">
                    <th width="5%" class="wd-left">#</th>
                    <th class="wd-left"><?php __('Project name') ?></th>
                    <th class="wd-left">&nbsp;</th>
    			</tr>
    		</thead>
    		<tbody>
                <?php 
            	   $i = 0; 
                    foreach ($projects as $project): //debug($project);
                		$class = null;            	
                        $i++
            	?>
    			 <tr>
            		<td><?php echo $i ?></td>
                    <td>
                        <?php echo $project['Project']['project_name'];?>
                    </td>
                    <td><center><input type="radio" value="<?php echo $project['Project']['id'] ?>|<?php echo $project['Project']['project_name']?>" name="data[Project][duplicate]" /></center></td>
            	</tr>
                <?php endforeach; ?>
            </tbody>
             
        
    	</table>
        <?php echo $form->end()?>
        </div>
        <div class="clear:both"></div>
         <ul class="type_buttons" style="padding: 10px 10px 0 0 ;position: relative">
            <li><a href="javascript:void(0)" class="cancel1"><?php __("Cancel") ?></a></li>
            <li><a href="javascript:void(0)" class="new1"><?php __('Ok') ?></a></li>
            <li id="error"><span></span></li>
        </ul>     
