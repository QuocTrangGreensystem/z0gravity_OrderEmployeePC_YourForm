<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if( $typeSelect == 'week' ){
    $link = $this->Html->url(sprintf('/activity_forecasts/request/week?week=%s&year=%s', $_week, $_year), true);
} else {
    $link = $this->Html->url(sprintf('/activity_forecasts/request/month?month=%s&year=%s', $_month, $_year), true);
}
?>
<h3><?php __('Hello') ?>,</h3>
<p>
    <?php
    if ($isValidated) {
        if( $typeSelect == 'week' )
            echo sprintf(__('Timesheet for <b>%1$s</b> validated.', true), $_week);
        else
            echo sprintf(__('Timesheet for <b>%1$s</b> validated.', true), $_month . '/' . $_year);
    } else {
        if( $typeSelect == 'week' )
            echo sprintf(__('Timesheet for <b>%1$s</b> rejected.', true), $_week);
        else
            echo sprintf(__('Timesheet for <b>%1$s</b> rejected.', true), $_month . '/' . $_year);
    }
    ?>
</p>
<p>
    <?php
    echo sprintf(__('Select the following link %s.', true)
            , '<strong><a href="' . $link . '">' . __('review your timesheet', true) . '</a></strong>');
    echo '<br/>';
    
    ?>
</p>
<p><?php __('Thank you'); ?></p>