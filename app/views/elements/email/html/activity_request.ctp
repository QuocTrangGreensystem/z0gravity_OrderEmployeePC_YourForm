<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if( $typeSelect == 'week' ){
    $link = $this->Html->url(sprintf('/activity_forecasts/request/week?id=%s&profit=%s&week=%s&year=%s', $employeeName['id'], $profit, $_week, $_year), true);
    $text_en = sprintf(__('The timesheet of <b>%s</b> <i>week</i> <b>%s</b> has to be validated.', true), $employeeName['first_name'] . ' ' . $employeeName['last_name'], $_week);
    $text_fr = sprintf(__('Vous avez reçu la feuille de temps de la semaine <b>%s</b> de la <i>semaine</i> <b>%s</b> à valider.', true), $employeeName['first_name'] . ' ' . $employeeName['last_name'], $_week);
} else {
    $link = $this->Html->url(sprintf('/activity_forecasts/request/month?id=%s&profit=%s&month=%s&year=%s', $employeeName['id'], $profit, $_month, $_year), true);
    $text_en = sprintf(__('The timesheet of <b>%s</b> for <b>%s</b> has to be validated.', true), $employeeName['first_name'] . ' ' . $employeeName['last_name'], $_month . '/' . $_year);
    $text_fr = sprintf(__('Vous avez reçu la feuille de temps de la semaine <b>%s</b> de <b>%s</b> à valider.', true), $employeeName['first_name'] . ' ' . $employeeName['last_name'], $_month . '/' . $_year);
}
?>
<h3><?php __('Hello') ?>,</h3>
<p>
    <?php
    echo $text_en;
    echo '<br/>';
    echo $text_fr;
    ?>
</p>
<p>
    <?php
    echo sprintf(__('Select the following link to %s.', true)
            , '<strong><a href="' . $link . '">' . __('validate the timesheet', true) . '</a></strong>');
    echo '<br/>';
    echo sprintf(__('Sélectionner le lien pour %s.', true)
            , '<strong><a href="' . $link . '">' . __('valider la feuille de temps', true) . '</a></strong>');
    ?>
</p>
<p><?php __('Thank you'); ?></p>
