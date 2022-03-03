<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php
    if(!empty($saveContent)){
        echo $saveContent;
    } else {
?>
<h3><?php __('Hello') ?>,</h3>
<?php
if(!empty($times)){
    foreach($times as $day){
        $_week = date('W', $day);
        $_year = date('Y', $day);
        $link = $this->Html->url(sprintf('/activity_forecasts/request/week?week=%s&year=%s&profit=%s', $_week, $_year, $profits), true);
        
        echo '<p>' . sprintf(__('Timesheet week <b>%1$s</b> not completed.', true), $_week) . '</p>';
        echo '<p>' . sprintf(__('Feuille de temps semaine <b>%1$s</b> incomplet', true), $_week) . '</p>';
        echo '<p>' . sprintf(__('Select the following link %s.', true), '<strong><a href="' . $link . '">' . __('review your timesheet', true) . '</a></strong>') . '</p>';
        echo '<p>' . sprintf(__('Sélectionner le lien pour %s.', true), '<strong><a href="' . $link . '">' . __('revoir la feuille de temps.', true) . '</a></strong>') . '</p>';
        echo '<br/>';
    }
}
?>
<p><?php __('Thank you'); ?></p>
<?php
    }
?>