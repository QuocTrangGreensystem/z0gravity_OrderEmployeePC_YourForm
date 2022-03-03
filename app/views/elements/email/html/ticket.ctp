<?php
$params = array(
	'{url}' => $this->Html->link($ticket['name'], '/tickets/view/' . $ticket['id']),
	'{name}' => $ticket['name'],
	'{id}' => '#' . $ticket['id'],
	'{status}' => $status['name'],
	'{time}' => $this->Time->format('H:i, d-m-Y', $ticket['updated']),
	'{updater}' => $employee_info['Employee']['fullname']
);
if(!empty($email_content)){
    echo nl2br(str_replace(array_keys($params), array_values($params), $email_content));
}
?>