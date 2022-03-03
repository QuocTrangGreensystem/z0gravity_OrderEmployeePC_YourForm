<?php
echo $this->Form->input('SaleLead.sale_customer_id', array(
    'options' => $saleCustomers, 'label' => false, 'div' => false, 'empty' => ''
));
?>