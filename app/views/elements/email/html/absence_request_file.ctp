<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<h3><?php __('Bonjour') ?>,</h3>
<p>
    <?php
    echo sprintf(__('The Absence request of <b>%1$s</b> (%6$s) from <i>%2$s %3$s</i> to <i>%4$s %5$s</i>', true), $employeeName['first_name'] . ' ' . $employeeName['last_name']
            , date('d', $_start), date('M', $_start), date('d', $_end), date('M', $_end), $absenceInfo['Absence']['print']);
    echo '<br/>';
    echo sprintf(__('Vous avez recu une demande d’absence de <b>%1$s</b> (%6$s) du <i>%2$s %3$s</i> à <i>%4$s %5$s</i>.', true), $employeeName['first_name'] . ' ' . $employeeName['last_name']
            , date('d', $_start), date('M', $_start), date('d', $_end), date('M', $_end), $absenceInfo['Absence']['print']);
    ?>
</p>
<p> 
    <?php
    $msg = !empty($absenceAttachments['message']) ? $absenceAttachments['message'] : '';
    echo sprintf(__('Attached file for document %s.', true)
            , '<strong>' . $msg . '</strong>');
    echo '<br/>';
    echo sprintf(__('Fichier attaché our document %s.', true)
            , '<strong>' . $msg . '</strong>');
    ?>
</p>
<p><?php __('Merci'); ?></p>