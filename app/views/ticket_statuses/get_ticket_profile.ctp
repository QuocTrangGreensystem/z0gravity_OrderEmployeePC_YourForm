<?php
$this->Form->create('TicketProfile');
$index = 0;
?>
<table class="select-employees" style="width: 100%;">
    <?php if (!empty($ticketProfiles)) : ?>
        <?php foreach ($ticketProfiles as $id => $name) : ?>
            <tr>
                <td class="wd-employ-data">
                    <?php
                    echo $this->Form->input($index . '.id', array('type' => 'checkbox',
                        'rel' => 'no-history',
                        'class' => 'id-hander',
                        'hiddenField' => false,
                        'id' => 'ticket-profile-' . $id,
                        'profile' => $id,
                        'value' => $id,
                        'label' => $name
                    ));
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php elseif (empty($ticketProfiles)) : ?>
        <tr>
            <td class="no-data" colspan="2">
                <label><b><?php echo __('No exists data!') ?></b></label>
            </td>
        </tr>
    <?php endif; ?>
</table>
<?php $this->Form->end(); ?>