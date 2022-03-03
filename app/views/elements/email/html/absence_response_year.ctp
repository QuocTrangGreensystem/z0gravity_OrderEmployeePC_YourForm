<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<h3><?php __('Hello') ?>,</h3>
<p>
    <?php
	if(!empty($ab_request[$employee_request])){
		foreach($ab_request[$employee_request] as $date){
			$_start = $date['start'];
			$_end = $date['end'];
			if ($isValidated) {
				echo sprintf(__('Absence has been validated from <i>%1$s %2$s %3$s</i> to <i>%4$s %5$s %6$s</i>.', true)
						, date('d', $_start), date('M', $_start), date('Y', $_start), date('d', $_end), date('M', $_end), date('Y', $_end));
				echo '<br/>';
				echo sprintf(__('Absence validée du <i>%1$s %2$s %3$s</i> à <i>%4$s %5$s %6$s</i>.', true)
						, date('d', $_start), date('M', $_start), date('Y', $_start), date('d', $_end), date('M', $_end), date('Y', $_end));
				 echo '<br/>';
			} else {
				echo sprintf(__('Absence has been rejected from <i>%1$s %2$s %3$s</i> to <i>%4$s %5$s %6$s</i>.', true)
						, date('d', $_start), date('M', $_start), date('Y', $_start), date('d', $_end), date('M', $_end), date('Y', $_end));
				echo '<br/>';
				echo sprintf(__('Absence annulée du <i>%1$s %2$s %3$s</i> à <i>%4$s %5$s %6$s</i>.', true)
						, date('d', $_start), date('M', $_start), date('Y', $_start), date('d', $_end), date('M', $_end), date('Y', $_end));
				 echo '<br/>';
			}
		}
	}
    ?>
</p>
<p><?php __('Thank you'); ?></p>
