<?php if(isset($url)){ ?>
   <iframe width="100%" height="900" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="<?php echo $is_https?'https':'http'?>://<?php echo $url;?>">
   </iframe>
<?php }else{ ?>
   <h3 style="text-align: center; margin-top: 200px;">
    <?php
        echo __('Not available');
    ?>
   </h3>
<?php }?>