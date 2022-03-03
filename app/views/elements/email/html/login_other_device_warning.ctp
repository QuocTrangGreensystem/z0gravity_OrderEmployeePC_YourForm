<h3><?php echo sprintf( __('Hello %s', true), $fullname); ?>,</h3>
<p>
    <?php __('The use of a new browser has been detected.'); ?> (<?php echo !empty( $browser['name']) ? ( $browser['name'] . ', ') : '';?><?php echo $realIP;?>)<br/>
    <?php __('If you are the originator of this connection, no worries.'); ?><br/>
    <?php __('Otherwise as a precaution, please change your password.'); ?>
</p>
<p>
	<?php __('Regards'); ?>
	<br/>
	<?php __('z0Gravity team') ?>
</p>