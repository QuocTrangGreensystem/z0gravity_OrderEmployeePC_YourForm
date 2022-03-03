<?php echo $html->css(array('projects','context/jquery.contextmenu')); ?>
<?php echo $html->script('context/jquery.contextmenu'); ?>
<!-- export excel  -->
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'absence_requests', 'action' => 'export')));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<!-- /export excel  -->
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                    <h2 class="wd-t1"><?php echo $projectTaskName['ProjectTask']['task_title'] ?></h2>
                    <?php /* <a href="javascript:void(0);" class="wd-add-project" id="export-submit" style="margin-right:5px; "><span><?php __('Export Excel') ?></span></a> */ ?>
                </div>
                <div id="table-control">
                    <?php
                    echo $this->Form->create('Control', array(
                        'type' => 'get',
                        'url' => '/' . Router::normalize($this->here)));
                    ?>
                    <fieldset>
                        <label><?php __('From') ?></label>
                        <div class="input">
                            <?php
                                echo $this->Form->input('start', array('label' => false, 'style' => 'padding: 7px', 'div' => false, 'value' => isset($_start) ? date('m/d/Y', $_start) : ''));
                            ?>
                        </div>
                        <label> <?php __('To') ?> </label>
                        <div class="input" style="margin-right: 5px; overflow: hidden; float: left">
                            <?php
                                echo $this->Form->input('end', array('label' => false, 'style' => 'padding: 7px', 'div' => false, 'value' => isset($_end) ? date('m/d/Y', $_end) : ''));
                            ?>
                        </div>
                        <button type="submit" class="btn btn-go"></button>
                        <div style="clear:both;"></div>
                    </fieldset>
                    <?php
                    echo $this->Form->end();
                    ?>
                </div>
                <div id="message-place">
                    <?php
                    echo $this->Session->flash();
                    $dayMaps = array(
                        '1' => __('January', true),
                        '2' => __('February', true),
                        '3' => __('March', true),
                        '4' => __('April', true),
                        '5' => __('May', true),
                        '6' => __('June', true),
                        '7' => __('July', true),
                        '8' => __('August', true),
                        '9' => __('September', true),
                        '10' => __('October', true),
                        '11' => __('November', true),
                        '12' => __('December', true)
                    );
                    ?>
                </div>
                <div class="wd-table" id="project_container" style="width:100%;overflow-y: hidden;overflow-x: auto;">
                    <div id="absence-container" style="min-height:400px;">
                        <div id="absence-wrapper" style="margin: 0">
                            <table id="absence" style="width: auto;min-width: 100%;">
                                <!-- this is the head of the table -->
                                <thead>
                                    <tr>
                                        <th rowspan="3"><?php __('#'); ?></th>
                                        <th rowspan="3"><?php __('Employee'); ?></th>
                                        <th rowspan="2" colspan="2"><?php __('Total'); ?></th>
                                        <?php
                                        $columns = array();
                                        $m = $_minMonth;
                                        $y = $_minYear;
                                        $_output = '';
                                        $months = 0;
                                        while ($y < $_maxYear || ($y == $_maxYear && $m <= $_maxMonth)) {
                                            $columns[$y] = ((isset($columns[$y]) ? $columns[$y] : 0) + 1);
                                            $_output .= '<th colspan="2">' . $dayMaps[$m] . '</th>';
                                            $m++;
                                            if ($m == 13) {
                                                $m = 1;
                                                $y++;
                                            }
                                            $months++;
                                        } ?>

                                        <?php foreach ($columns as $year => $count): ?>
                                            <th colspan="<?php echo $count * 2 ?>"> <?php echo $year ?></th>
                                        <?php endforeach ?>
                                    </tr>
                                    <tr><?php echo $_output ?></tr>
                                    <tr>
                                        <th><?php __('Validated') ?></th>
                                        <th><?php __('Not Validated') ?></th>
                                        <?php for($i = 0; $i < $months; $i++): ?>
                                        <th><?php __('Validated') ?></th>
                                        <th><?php __('Not Validated') ?></th>
                                        <?php endfor ?>
                                    </tr>
                                </thead>

                                <!-- this is the body of the table -->
                                <tbody id="absence-table">

                                    <!-- summary row -->
                                    <tr>
                                        <td colspan="2" style="font-weight: bold;">SUMMARY</td>
                                        <td style="background-color: rgb(152, 187, 231) !important; vertical-align: middle;" class="month total-all-validated"></td>
                                        <td style="background-color: rgb(152, 187, 231) !important; vertical-align: middle;" class="month total-all-not-validated"></td>
                                        <?php
                                            $m = $_minMonth;
                                            $y = $_minYear;
                                            // loop throw months and years
                                            while ($y < $_maxYear || ($y == $_maxYear && $m <= $_maxMonth)) {
                                                ?>
                                                <td style="background-color: rgb(152, 187, 231) !important; vertical-align: middle;" class="month summary-all" data-type="validated" data-id="<?php echo $y .'-' . $m ?>"></td>
                                                <td style="background-color: rgb(152, 187, 231) !important; vertical-align: middle;" class="month summary-all" data-type="not-validated" data-id="<?php echo $y .'-' . $m ?>"></td>
                                                <?php
                                                $m++;
                                                if ($m == 13) {
                                                    $m = 1;
                                                    $y++;
                                                }
                                            }
                                        ?>

                                    </tr>

                                    <?php foreach($profitCenters as $key => $profitCenter):?>
                                        <!-- render the profit center summary row -->
                                        <tbody class="profit">
                                        <tr class="profit-center">
                                            <td colspan="2"><em class="icon-profit"></em><label class="name-profit"><?php echo $profitCenter;?></label></td>
                                            <td style="background-color: #E8F0FA !important; vertical-align: middle; text-align:center;" data-type="validated" data-pc="<?php echo $key ?>" class="summary total-validated total-validated-<?php echo $key ?>"></td>
                                            <td style="background-color: #E8F0FA !important; vertical-align: middle; text-align:center;" data-type="not-validated" data-pc="<?php echo $key ?>" class="summary total-not-validated total-not-validated-<?php echo $key?>"></td>
                                            <?php
                                            $m = $_minMonth;
                                            $y = $_minYear;

                                            // loop throw months and years
                                            while ($y < $_maxYear || ($y == $_maxYear && $m <= $_maxMonth)) {
                                            ?>
                                            <td style="background-color: #E8F0FA !important; vertical-align: middle;" data-type="validated" data-pc="<?php echo $key ?>" data-id="<?php echo $y .'-' . $m ?>" class="summary summary-validated-<?php echo $key ?> summary-validated-<?php echo $y .'-' . $m ?> month"></td>
                                            <td style="background-color: #E8F0FA !important; vertical-align: middle;" data-type="not-validated" data-pc="<?php echo $key ?>" data-id="<?php echo $y .'-' . $m ?>"  class="summary summary-not-validated-<?php echo $key ?> summary-not-validated-<?php echo $y .'-' . $m ?> month"></td>
                                            <?php
                                                $m++;
                                                if ($m == 13) {
                                                    $m = 1;
                                                    $y++;
                                                }
                                            }
                                            ?>
                                        </tr>

                                        <!-- render children rows -->
                                        <?php
                                        $stt = 1;
                                        foreach ($employees as $employee) : ?>
                                            <?php if($key == $employee['ProjectEmployeeProfitFunctionRefer'][0]['profit_center_id']):?>
                                            <tr>
                                                <td class="no"><?php echo $stt++; ?></td>
                                                <td class="name"><?php echo $employee['Employee']['first_name'] . ' ' . $employee['Employee']['last_name']; ?></td>
                                                <?php
                                                $m = $_minMonth;
                                                $y = $_minYear;
                                                $cell_outputs = array();
                                                $id = $employee['Employee']['id'];
                                                ?>
                                                <td style="vertical-align: middle;" data-employee="<?php echo $id ?>" class="month total-data-validated-<?php echo $id ?> total-data-validated"></td>
                                                <td style="vertical-align: middle;" data-employee="<?php echo $id ?>" class="month total-data-not-validated-<?php echo $id ?> total-data-not-validated"></td>
                                                <?php
                                                while ($y < $_maxYear || ($y == $_maxYear && $m <= $_maxMonth)) {
?>
                                                <td style="vertical-align: middle;"  class="month pc-<?php echo $key ?> pc-validated-<?php echo $id ?> data-validated-<?php echo $y .'-' . $m ?>">
                                                    <?php echo isset($activities[$id][$y . '-' . $m]['validated']) ? $activities[$id][$y . '-' . $m]['validated'] : '' ?>
                                                </td>
                                                <td style="vertical-align: middle;" class="month pc-<?php echo $key ?> pc-not-validated-<?php echo $id ?> data-not-validated-<?php echo $y .'-' . $m ?>">
                                                    <?php echo isset($activities[$id][$y . '-' . $m]['notValidated']) ? $activities[$id][$y . '-' . $m]['notValidated'] : '' ?>
                                                </td>
<?php
                                                    $m++;
                                                    if ($m == 13) {
                                                        $m = 1;
                                                        $y++;
                                                    }
                                                }
                                                ?>
                                            </tr>
                                            <?php endif;?>
                                        <?php endforeach;?>
                                    </tbody>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
jQuery(document).ready(function($) {

    $('.profit-center').toggle(function(){
        $(this).parent().find('tr').not('tr.profit-center').slideUp();
        $(this).find('em').removeClass('icon-profit');
        $(this).find('em').addClass('icon-profit-expand');
    }, function(){
        $(this).parent().find('tr').not('tr.profit-center').slideDown();
        $(this).find('em').removeClass('icon-profit-expand');
        $(this).find('em').addClass('icon-profit');
    });
    $('#ControlStart').datepicker();
    $('#ControlEnd').datepicker();
    $('.summary').each(function(){
        var t = $(this),
            type = t.data('type'),
            id = t.data('id'),
            pc = t.data('pc'),
            total = 0;
        $('.pc-' + pc + '.data-' + type + '-' + id).each(function(){
            var t = parseFloat($(this).text());
            if( !isNaN(t) )total += t;
        });
        t.text(total.toFixed(2).replace(/\.?0+$/, ''));
    });
    var total_validate = total_not_validate = 0;
    $('.summary-all').each(function(){
        var t = $(this),
            type = t.data('type'),
            id = t.data('id'),
            total = 0;
        $('.summary-' + type + '-' + id).each(function(){
            var t = parseFloat($(this).text());
            if( !isNaN(t) )total += t;
        });
        t.text(total.toFixed(2).replace(/\.?0+$/, ''));
        if(type == "validated"){
            total_validate += parseFloat(total);
        }else{
            total_not_validate += parseFloat(total);
        }
    });
    $('.total-all-validated').text(total_validate.toFixed(2).replace(/\.?0+$/, ''));
    $('.total-all-not-validated').text(total_not_validate.toFixed(2).replace(/\.?0+$/, ''));
    // total of team.
    $('.total-validated').each(function(){
        var pc = $(this).data('pc');
        var total_validate = 0;
        $('.summary-validated-'+pc).each(function(){
            total_validate += parseFloat($(this).text());
        });
        $('.total-validated-'+pc).text(total_validate.toFixed(2).replace(/\.?0+$/, ''));
    });
    $('.total-not-validated').each(function(){
        var pc = $(this).data('pc');
        var total_not_validate = 0;
        $('.summary-not-validated-'+pc).each(function(){
            total_not_validate += parseFloat($(this).text());
        });
        $('.total-not-validated-'+pc).text(total_not_validate);
    });
    // total of resource.
    $('.total-data-validated').each(function(){
        var id = $(this).data('employee');
        var total_validate = 0;
        $('.pc-validated-'+id).each(function(){
            total_validate += (!isNaN(parseFloat($(this).text())) ? parseFloat($(this).text()) : 0 );
        });
        $('.total-data-validated-'+id).text(total_validate.toFixed(2).replace(/\.?0+$/, ''));
    });
    $('.total-data-not-validated').each(function(){
        var id = $(this).data('employee');
        var total_not_validate = 0;
        $('.pc-not-validated-'+id).each(function(){
            total_not_validate += (!isNaN(parseFloat($(this).text())) ? parseFloat($(this).text()) : 0 );
        });
        $('.total-data-not-validated-'+id).text(total_not_validate);
    });
});
</script>
