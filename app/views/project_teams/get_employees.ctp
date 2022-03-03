<?php
$this->Form->create('Employee');
$index = 0;
?>
<table class="select-employees" style="width: 100%;">
    <?php if (!empty($datas)) : ?>
        <?php foreach ($datas as $data) : ?>
            <tr>
                <td class="wd-employ-data">
                    <?php
                    echo $this->Form->input($index . '.id', array('type' => 'checkbox',
                        'rel' => 'no-history',
                        'class' => 'id-hander',
                        'hiddenField' => false,
                        'profit' => $data['ProjectEmployeeProfitFunctionRefer']['profit_center_id'],
                        'value' => $data['Employee']['id'],
                        'label' => sprintf('%s %s', $data['Employee']['first_name'], $data['Employee']['last_name'])));
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
    <?php if (!empty($profitName) && !empty($funcs)) : ?>
        <tr>
            <td class="no-employee" colspan="2">
                <?php
                echo $this->Form->input($index . '.id', array('type' => 'checkbox',
                    'rel' => 'no-history',
                    'value' => 'no',
                    'label' => sprintf(__('List all employees for %s', true), $profitName['ProfitCenter']['name'])));
                ?>
            </td>
        </tr>
    <?php elseif (empty($datas)) : ?>
        <tr>
            <td class="no-data" colspan="2">
                <label><b><?php echo __('No exists data!') ?></b></label>
            </td>
        </tr>
    <?php endif; ?>
</table>
<?php $this->Form->end(); ?>