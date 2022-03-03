<?php 
    echo $html->css(array(
        'preview/global-views'
    ));
 ?>

<style>
	#delete-frame{
		position:relative;
		top: inherit;
		right: inherit;
	}
	#delete-frame span{
		position: absolute;
		left: 30%;
		top: 10px;
		width: 16px;
		height: 16px;
		background: url(https://z0gravity.local/img/ajax-loader.gif) no-repeat;
		display: none;
	}
	#upload-place div textarea{
		border: 1px solid #E1E6E8;
		background-color: #FFFFFF;
		box-shadow: 0 0 10px 1px rgba(29,29,27,0.06);
		padding: 10px;
		margin-bottom: 15px;
	}

</style>
<div id="wd-container-main" class="wd-project-detail">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab">
                <div class="wd-panel">
					
                    <?php echo $this->Session->flash();
						$has_data = (!empty($powerbiDashboards)) ? 1 : 0;

					?>
                    <div class="wd-section <?php echo $powerbiDashboards ? 'show-menu' : ''; ?>" id="wd-fragment-1">
						
						<div class="wd-dashboard-content" <?php if(!$has_data) echo "style = 'display: none'"?>>
							<div class="wd-title">
								<a href="javascript:void(0);" id = 'delete-frame' class="btn btn-table-collapse" title="Delete" style="">
									<span></span>
								</a>
							</div>
							<?php echo $powerbiDashboards['ProjectPowerbiDashboard']['iframe']; ?>
						</div>
		
						<div id="upload-place" <?php if($has_data) echo "style = 'display: none'"?> class="wd-upload" style="<?php echo 'display:' . ($powerbiDashboards ? 'none' : 'block'); ?>">
							<?php if( $canModified ) { ?>
									<div class="tabs-content-container">
										<?php 
											echo $this->Form->create('ProjectPowerbiDashboard', array(
												'url' => array('action' => 'upload',
													$projectName['Project']['id'])));
										   
											echo $this->Form->input('iframe', array('div' => false, 'label' => false,'id'=>'iframe',
												'type' => 'textarea',
												'placeholder' => 'Enter embed code here'
											)); 
										
											?>

											<button type="submit" class="btn-submit wd-button-f" id="btnSave">
												<?php __('Submit') ?>
											</button>
											
										<?php echo $this->Form->end(); ?>
									</div>
							<?php } ?>
						</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
	$('#delete-frame').click(function(){
		me = $(this);
		me.find('span').show();
		$.ajax({
			type : 'POST',
			url : '<?php echo $html->url('delete/' . @$powerbiDashboards['ProjectPowerbiDashboard']['id']) ?>',
			success : function(result){
				if(result == 1){
					console.log(result);
					me.find('span').hide();
					$('.wd-dashboard-content').hide();
					$('#upload-place').show();
				}
				
			}
		});
	});
</script>
    