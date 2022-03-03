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
	.wd-tab .wd-panel{ border:none !important}
</style>

            <div class="wd-tab">
                <div class="wd-panel">
                    <?php echo $this->Session->flash(); ?>
                    <div class="wd-section" id="wd-fragment-1">
                        <h2 class="wd-t2" style="float: left;border: 0;"><?php echo $projectName['Project']['project_name'] ?></h2>
                        <?php
                        echo $this->Form->create('ProjectLocalView', array(
                            'type' => 'file',
                            'url' => array('controller' => 'project_local_views', 'action' => 'upload',
                                $projectName['Project']['id'])));
                        ?>
                     
                        <?php echo $this->Form->end(); ?>    
                        <br style="clear: both;" />
                    </div>
                    <div class="wd-section" id="wd-fragment-2">
                        <?php
                        $link = $this->Html->url(array('action' => 'attachment', $projectName['Project']['id']), true);
                        if ($projectLocalView && empty($noFileExists)) {
                            if (!preg_match('/\.(jpg|jpeg|bmp|gif|png|swf)$/i', $projectLocalView['ProjectLocalView']['attachment'])) {
                                $link = 'https://docs.google.com/gview?url=' . ($link) . '&embedded=true';
                            }
                        }
                        ?>
                        <iframe src="<?php echo $link; ?>" style="width: 100%;height: 900px; border: 1px solid #D8D8D8;"></iframe>
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