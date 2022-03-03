<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<h3><?php __('Hello') ?>,</h3>
<p>
    <?php
    echo sprintf(__('The Absence request of <b>%1$s</b> from <i>%2$s %3$s</i>  to <i>%4$s %5$s</i> has to be validated', true), $employeeName['first_name'] . ' ' . $employeeName['last_name']
            , date('d', $_start), date('M', $_start), date('d', $_end), date('M', $_end));
    echo '<br/>';
    echo sprintf(__('Vous avez recu une demande d’absence de <b>%1$s</b> du <i>%2$s %3$s</i> à <i>%4$s %5$s</i>.', true), $employeeName['first_name'] . ' ' . $employeeName['last_name']
            , date('d', $_start), date('M', $_start), date('d', $_end), date('M', $_end));
    ?>
</p>
<p> 
    <?php
    echo sprintf(__('Select the following link to %s.', true)
            , '<strong><a href="' . $this->Html->url(sprintf('/absence_requests/manage?profit=%s&week=%s&year=%s', $profit, date('W', $_end), date('Y', $_end)), true)
            . '">' . __('validate that absences', true) . '</a></strong>');
    echo '<br/>';
    echo sprintf(__('Sélectionner le lien pour %s.', true)
            , '<strong><a href="' . $this->Html->url(sprintf('/absence_requests/manage?profit=%s&week=%s&year=%s', $profit, date('W', $_end), date('Y', $_end)), true)
            . '">' . __('valider absences demande', true) . '</a></strong>');
    
    ?>
</p>
<p><?php __('Thank you'); ?></p>