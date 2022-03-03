<?php echo $html->script('jquery.validation.min'); ?>
<?php echo $html->script('jquery.dataTables'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
<?php
$employee_info = $this->Session->read("Auth.employee_info");
?>
<style>
.wd-header th{ line-height:41px; }
.valWorkload{ text-align:right; }
.error{ background:#F00 !important; color:#FFF !important; }
input[type='radio']{ margin-top:3px;}
label{ font-weight:bold; margin-right:10px; margin-top:-3px; }
#table-control{ margin-left:-5px !important; padding-bottom:10px; }
select{ padding:3px 5px}
input[type='button']{ padding:3px 5px; cursor:pointer}
</style>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                </div>
                <?php
                App::import("vendor", "str_utility");
                $str_utility = new str_utility();
				$arrMonth = array(
					'01'=>'Jan',
					'02'=>'Feb',
					'03'=>'March',
					'04'=>'Apri',
					'05'=>'May',
					'06'=>'Jun',
					'07'=>'July',
					'08'=>'Aug',
					'09'=>'Sep',
					'10'=>'Oct',
					'11'=>'Nov',
					'12'=>'Dec',
				);
				$currentYear = date('Y',time());
				$startYear = $currentYear - 5 ;
				$endYear = $currentYear + 6 ;
                ?>
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <div class="wd-content">
                                <h2 style="margin:5px 0 5px !important;" class="wd-t3"><?php __('Staffing System') ?></h2>
                                <div id="table-control">
                                    <label><input type="radio" checked="checked" value="Activity" name="typeChecking" /><?php echo __('Activity',true); ?></label>
                                    <label><input type="radio" value="Project" name="typeChecking" /><?php echo __('Project',true); ?></label>
                                    <select name="checkingMonth" id="checkingMonth">
                                    <option value="-1"> -- All -- </option>
                                    <?php foreach($arrMonth as $key=>$value)
                                    {
                                        ?>
                                        <option value="<?php echo $key;?>"><?php echo $value;?></option>  
                                        <?php
                                    }
                                    ?>
                                    </select>
                                    <select name="checkingYear" id="checkingYear">
                                    <?php for( $i = $startYear; $i < $endYear; $i++ )
                                    {
                                        $selected = '';
                                        if($currentYear == $i)	$selected = "selected";
                                        ?>
                                        <option <?php echo $selected; ?> value="<?php echo $i;?>"><?php echo $i;?></option>  
                                        <?php
                                    }
                                    ?>
                                    </select>
                                    <input type="button" value="Checking" onclick="checkingStaffing();" />
                                </div>
                                <table cellspacing="0" cellpadding="0" class="display" id="table-list-admin">
                                <thead>
                                    <tr class="wd-header">
                                        <th class="wd-order" width="5%"><?php echo __('#', true); ?></th>
                                        <th width="20%"><?php echo  __($keyword, true); ?></th>
                                        <th width="10%"><?php echo __('Workload from task', true); ?></th>
                                        <th width="10%"><?php echo __('Workload in staffing (E)', true); ?></th>
                                        <th width="10%"><?php echo __('Workload in staffing (P)', true); ?></th>
                                        <th width="10%"><?php echo __('Workload in staffing (S)', true); ?></th>
                                        <th width="5%"><?php echo __("Great", true); ?></th>
                                        <th width="25%"><?php echo __("Actions", true); ?></th>
                                    </tr>
                                </thead>
                                <tbody id="staffing_content">
                                	<?php foreach($results as $index=>$data)
									{
										?>
                                        <tr>
                                        	<td><?php echo $index;?></td>
                                            <td><?php echo $data['name'];?></td>
                                            <td class="valWorkload"><?php echo $data['workload'];?></td>
                                            <td class="valWorkload <?php echo $data['clsE'];?>"><?php echo $data['staffingE'];?></td>
                                            <td class="valWorkload <?php echo $data['clsP'];?>"><?php echo $data['staffingP'];?></td>
                                            <td class="valWorkload <?php echo $data['clsS'];?>"><?php echo $data['staffingS'];?></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <?php
									}
									?>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function checkingStaffing(){
	var type = $('input:radio[name = "typeChecking"]:checked').val();
	var month = $('#checkingMonth').val();
	var year = $('#checkingYear').val();
	var data = '';
	$.ajax({
		url  : "/staffing_systems/index/ajax/",
		type : "POST",
		data : {
			data:{'type' : type,'month' : month, 'year' : year}
		},
		success : function(html){
			$('#staffing_content').html(html);
		}
	});
}
</script>