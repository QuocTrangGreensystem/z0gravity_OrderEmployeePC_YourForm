<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<h3><?php __('Hello') ?>,</h3>
<p>
    <?php
    echo sprintf(__('Please select the following link to change your password. %1$s. This link is active %2$s hours.', true)
            , '<strong><a href="' . $this->Html->url('/password-change?token=' . $data['token'], true) . '">' . __('Change your password', true) . '</a></strong>', $lifeTime / 60 / 60);
    ?>
</p>
<p>
	<?php __('Regards'); ?>
	<br/>
	<?php __('z0Gravity team') ?>
</p>