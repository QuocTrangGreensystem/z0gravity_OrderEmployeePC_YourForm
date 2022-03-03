<?php
$this->Form->create('Employee');
$index = 0;
?>
<table class="select-employees" style="width: 100%;">
    <?php if (!empty($projectManagers)) : ?>
        <?php foreach ($projectManagers as $id => $name) : ?>
            <tr>
                <td class="wd-employ-data">
                    <?php
                    echo $this->Form->input($index . '.id', array('type' => 'checkbox',
                        'rel' => 'no-history',
                        'class' => 'id-hander',
                        'hiddenField' => false,
                        'value' => $id,
                        'label' => $name));
                    ?>
                </td>
                <td style="width: 80px;">
                    <?php
                    echo $this->Form->input(($index++) . '.bk', array('value' => 0, 'type' => 'checkbox',
                        'rel' => 'no-history',
                        'class' => 'bk-hander',
                        'hiddenField' => false,
                        'label' => __('Backup', true)));
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>
<?php $this->Form->end(); ?>