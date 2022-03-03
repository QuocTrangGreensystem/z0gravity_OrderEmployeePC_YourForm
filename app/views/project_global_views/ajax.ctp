<style type="text/css">
    .error-message {
        color: #FF0000;
        margin-left: 35px;
    }
    #download-place img{
        margin-right: 5px;
        vertical-align: middle;
    }
    #replace-attachment{
        margin-left: 20px;
    }
    #wd-container-main .wd-layout{
        padding-bottom: 10px;
    }
	.wd-tab .wd-panel{ border:none !important;}
</style>
<input type="hidden" id="attachment-type" value="<?php echo $type ?>" />
            <div class="wd-tab">
                <div class="wd-panel">
                    <?php echo $this->Session->flash(); ?>
                    <div class="wd-section" id="wd-fragment-1">
                        <h2 class="wd-t2" style="float: left;border: 0;margin-left: 25px"><?php echo $projectName['Project']['project_name'] ?></h2>
                        <?php
                        echo $this->Form->create('ProjectGlobalView', array(
                            'type' => 'file',
                            'url' => array('controller' => 'project_global_views', 'action' => 'upload',
                                $projectName['Project']['id'])));
                        ?>

                        <?php echo $this->Form->end(); ?>
                        <br style="clear: both;" />
                    </div>
                    <div class="wd-section" id="wd-fragment-2">
                        <?php
                        $check = false;
                        $link = $this->Html->url(array('action' => 'attachment', $projectName['Project']['id'], '?' => array('sid' => $api_key)), true);
                        if ($projectGlobalView && $type == 'file') {
                            if (!preg_match('/\.(jpg|jpeg|bmp|gif|png|swf)$/i', $projectGlobalView['ProjectGlobalView']['attachment'])) {
                                $link = 'https://docs.google.com/gview?url=' . urlencode($link) . '&embedded=true';
                                $check = true;
                            }
                        }
                        if(!$check){
                        ?>
                        <img src="<?php echo $link; ?>" style="border: 1px solid #D8D8D8;"></img>
                        <?php } else { ?>
                        <iframe src="<?php echo $link; ?>" style="width: 100%;height: 900px; border: 1px solid #D8D8D8;"></iframe>
                        <?php } ?>
                    </div>
                </div>
            </div>

<script type="text/javascript">

    (function($){
        $(function(){
            $('#replace-attachment').click(function(){
                $('#download-place').remove();
                $('#upload-place').show();
            });
        });
    })(jQuery);

</script>
