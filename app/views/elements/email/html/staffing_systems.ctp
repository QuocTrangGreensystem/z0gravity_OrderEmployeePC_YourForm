<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<h3><?php __('Hello') ?>,</h3>
<p>
    <?php
	$link = $this->Html->url('/staffing_systems/');
	echo sprintf(__('Profit Center/Profile of Employee (%s) have been changed.', true), $Employee);
	echo '<br/>';
    echo sprintf(__('Select the following link to %s.', true)
            , '<strong><a href="' . $link . '">' . __('Check and Format Staffing', true) . '</a></strong>');
    echo '<br/>';
    ?>
</p>
<p><?php __('Thank you'); ?></p>