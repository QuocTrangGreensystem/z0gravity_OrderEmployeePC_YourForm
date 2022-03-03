<?php
if ($limitForCustomer['support']['isLimit']) {
    $content = sprintf(__('%s ticket(s) utilisé(s)  sur %s  - Fin de période %s', true), $limitForCustomer['support']['num'], $limitForCustomer['support']['limit'], $limitForCustomer['support']['period']);
    echo $this->element('ticket_alert_box', array(
        "current" => $limitForCustomer['support']['num'],
        "limit" => $limitForCustomer['support']['limit'],
        "content_title" => __('alert_ticket_title_support', true),
        "content_description" => $content
    ));
}
if ($limitForCustomer['formation']['isLimit']) {
    $content = sprintf(__('%s ticket(s) utilisé(s)  sur %s  - Fin de période %s', true), $limitForCustomer['formation']['num'], $limitForCustomer['formation']['limit'], $limitForCustomer['formation']['period']);
    echo $this->element('ticket_alert_box', array(
        "current" => $limitForCustomer['formation']['num'],
        "limit" => $limitForCustomer['formation']['limit'],
        "content_title" => __('alert_ticket_title_formation', true),
        "content_description" => $content
    ));
}
if ($limitForCustomer['coaching']['isLimit']) {
    $content = sprintf(__('%s ticket(s) utilisé(s)  sur %s  - Fin de période %s', true), $limitForCustomer['coaching']['num'], $limitForCustomer['coaching']['limit'], $limitForCustomer['coaching']['period']);
    echo $this->element('ticket_alert_box', array(
        "current" => $limitForCustomer['coaching']['num'],
        "limit" => $limitForCustomer['coaching']['limit'],
        "content_title" => __('alert_ticket_title_coaching', true),
        "content_description" => $content
    ));
}
?>