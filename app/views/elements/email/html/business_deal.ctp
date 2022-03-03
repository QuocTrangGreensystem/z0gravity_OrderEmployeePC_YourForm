<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<h3><?php __('Hello') ?>,</h3>
<p>
    <?php
    echo sprintf(__('The renewal date of the deal <b>%1$s</b> is expired soon : <b>%2$s</b>', true), $lastUpdated['SaleLead']['name'], date('d/m/Y', $lastUpdated['SaleLead']['deal_renewal_date']));
    echo '<br/>';
    echo sprintf(__('Select the following link %s.', true)
            , '<strong><a href="' . $this->Html->url(sprintf('/sale_leads/deal_update/%s/%s', $company_id, $id), true)
            . '">' . __('review your business deal', true) . '</a></strong>');
    echo '<br/>';
    echo sprintf(__('La date de renouvellement du deal <b>%1$s</b> arrive à échéance le : <b>%2$s</b>', true), $lastUpdated['SaleLead']['name'], date('d/m/Y', $lastUpdated['SaleLead']['deal_renewal_date']));
    echo '<br/>';
    echo sprintf(__('Sélectionner le lien pour %s.', true)
            , '<strong><a href="' . $this->Html->url(sprintf('/sale_leads/deal_update/%s/%s', $company_id, $id), true)
            . '">' . __('revoir votre affaire d\'affaires', true) . '</a></strong>');
    ?>
</p>
<p><?php __('Thank you'); ?></p>