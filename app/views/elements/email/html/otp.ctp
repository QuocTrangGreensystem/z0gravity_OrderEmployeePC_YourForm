<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<h3><?php __('Hello') ?>,</h3>
<p>
    <?php
    echo sprintf(__('Your OTP: <b>%1$s</b>', true), $otp_send);
    ?>
</p>
<p>
	<?php __('Regards'); ?>
	<br/>
	<?php __('z0Gravity team') ?>
</p>