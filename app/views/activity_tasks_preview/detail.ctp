<?php echo $html->css(array('context/jquery.contextmenu')); ?>
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
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                    <h2 class="wd-t1"><?php echo sprintf(__("Activity Task Detail of %s", true), $activityTaskName['ActivityTask']['name']); ?></h2>
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
                        <div class="input" >
                            <?php
                                echo $this->Form->input('start', array('label' => false, 'div' => false,  "class" => "placeholder", "placeholder" => __("(dd-mm-yyyy)", true), 'onchange' => "validated(this);", 'value' => isset($_start) ? date('d-m-Y', $_start) : ''));
                            ?>
                        </div>
                        <label> <?php __('To') ?> </label>
                        <div id="wd-group">
                            <div class="input" id="wd-end-date">
                                <?php
                                    echo $this->Form->input('end', array('label' => false, 'div' => false,  "class" => "placeholder", "placeholder" => __("(dd-mm-yyyy)", true), 'onchange' => "validated(this);", 'value' => isset($_end) ? date('d-m-Y', $_end) : ''));
                                ?>
                            </div>
                            <p style="display: none; clear: both; padding-left: 10px; padding-top: 7px; color: red;" class="wd-error">The end date must be greater than start date</p>
                        </div>
                        <div class="button" id="wd-submit">
                            <input type="submit" value="OK" />
                        </div>
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
                        <div id="absence-wrapper" class="absence-wrapper-scroll">
                            <table id="absence" style="width: auto;min-width: 100%;">
                                <!-- this is the head of the table -->
                                <thead>
                                    <tr>
                                        <th rowspan="3"><?php __('#'); ?></th>
                                        <th rowspan="3"><?php __('Employee'); ?></th>
                                        <?php
                                        $columns = array();
                                        $m = isset($_minMonth) ? $_minMonth : (isset($_start) ? date('n', $_start) : 0);
                                        $y = isset($_minYear) ? $_minYear : (isset($_start) ? date('Y', $_start) : 0);
                                        $_output = '';
                                        $_tem = '';
                                        $_maxYear = isset($_maxYear) ? $_maxYear : (isset($_end) ? date('Y', $_end) : 0);
                                        $_maxMonth = isset($_maxMonth) ? $_maxMonth : (isset($_end) ? date('n', $_end) : 0);
                                        while ($y < $_maxYear || ($y == $_maxYear && $m <= $_maxMonth)) {
                                            $columns[$y] = ((isset($columns[$y]) ? $columns[$y] : 0) + 1);
                                            if($m != 0){
                                                $_output .= '<th colspan="2">' . $dayMaps[$m] . '</th>';
                                            } else {
                                                $_output .= '<th colspan="2">' . '0' . '</th>';
                                            }
                                            $_tem .= '<th>Validated</th><th>Not Validated</th>';
                                            //$_tem_2 .= '<th>Not Validate</th>'; 
                                            $m++;
                                            if ($m == 13) {
                                                $m = 1;
                                                $y++;
                                            }
                                        } ?>
                                        
                                        <?php foreach ($columns as $year => $count): ?>
                                            <th colspan="<?php echo $count*2;?>"> <?php echo $year ?></th>
                                        <?php endforeach ?>
                                        <tr><?php echo $_output; ?></tr>
                                        <tr>
                                            <?php echo $_tem; ?>
                                        </tr>
                                    </tr>
                                </thead>

                                <!-- this is the body of the table -->
                                <tbody id="absence-table">
                                    <?php $index = 1; ?>
                                    <?php 
                                        
                                        // init a variable that hold profit center summary data
                                        $profitCentersData = array();
                                        $profitCentersDataNotValidate = array();
                                        $profitCentersData['summary'] = array();
                                        $profitCentersDataNotValidate['summary'] = array();

                                        // loop throw each profit center to calculate the summary
                                        if(!empty($profitCenters)){
                                            foreach ($profitCenters as $key => $profitCenter) {
                                                $profitCentersData[$key] = array();
                                                $profitCentersDataNotValidate[$key] = array();
                                               // $profitCentersData[$key]['name'] = $profitCenter;
    
                                                // loop throw each employee
                                                foreach($employees as $employee){
    
                                                    // if employee is in this current profit center then increase the summary
                                                    if($key == $employee['ProjectEmployeeProfitFunctionRefer'][0]['profit_center_id']){
                                                        $m = $_minMonth;
                                                        $y = $_minYear;
    
                                                        // loop throw months and years
                                                        while ($y < $_maxYear || ($y == $_maxYear && $m <= $_maxMonth)) {
                                                            
                                                            if(isset($profitCentersData[$key][$y . '-' . $m]) || isset($profitCentersDataNotValidate[$key][$y . '-' . $m])){
                                                                $total = $profitCentersData[$key][$y . '-' . $m];
                                                                $totalNotValidate = $profitCentersDataNotValidate[$key][$y . '-' . $m];
                                                            }else{
                                                                $total = 0;
                                                                $totalNotValidate = 0;
                                                            }
    
                                                            if(isset($profitCentersData['summary'][$y . '-' . $m]) || isset($profitCentersDataNotValidate['summary'][$y . '-' . $m])){
                                                                $summary_total = $profitCentersData['summary'][$y . '-' . $m];
                                                                $summary_total_notValidate = $profitCentersDataNotValidate['summary'][$y . '-' . $m];
                                                            }else{
                                                                $summary_total = 0;
                                                                $summary_total_notValidate = 0;
                                                            }
    
                                                            if (isset($activities[$employee['Employee']['id']][$y . '-' . $m]) || isset($activityNotValidate[$employee['Employee']['id']][$y . '-' . $m])) {
                                                                // total validate
                                                                $_output = $activities[$employee['Employee']['id']][$y . '-' . $m];
                                                                $total += $_output;
                                                                $profitCentersData[$key][$y . '-' . $m] = $total;
                                                                // sum validate
                                                                $summary_total += $_output;
                                                                $profitCentersData['summary'][$y . '-' . $m] = $summary_total;
                                                                //total not validate
                                                                $_outputNotValidate = $activityNotValidate[$employee['Employee']['id']][$y . '-' . $m];
                                                                $totalNotValidate += $_outputNotValidate;
                                                                $profitCentersDataNotValidate[$key][$y . '-' . $m] = $totalNotValidate;
                                                                // sum not validate
                                                                $summary_total_notValidate += $_outputNotValidate;
                                                                $profitCentersDataNotValidate['summary'][$y . '-' . $m] = $summary_total_notValidate;
                                                            }
    
                                                            $m++;
                                                            if ($m == 13) {
                                                                $m = 1;
                                                                $y++;
                                                            }
                                                        }
    
                                                    }else{ // skip employees that not in current looping profit center
    
    
                                                    }
                                                }
                                            }
                                        }

                                    ?>

                                    <!-- summary row -->
                                    <tr>
                                        <td colspan="2" style="font-weight: bold;">SUMMARY</td>
                                        <?php 
                                            $m = isset($_minMonth) ? $_minMonth : (isset($_start) ? date('m', $_start) : 0);
                                            $y = isset($_minYear) ? $_minYear : (isset($_start) ? date('Y', $_start) : 0);

                                            $cell_outputs = array();

                                            // loop throw months and years
                                            while ($y < $_maxYear || ($y == $_maxYear && $m <= $_maxMonth)) {
                                                
                                                if(isset($profitCentersData['summary'][$y . '-' . $m])){
                                                    //$total = $profitCentersData[$key][$y . '-' . $m];
                                                    //$cell_outputs[] = $profitCentersData['summary'][$y . '-' . $m];
                                                    $cell_outputs[] = 
                                                    '<td style="background-color: rgb(152, 187, 231) !important;" class="month">' .$profitCentersData['summary'][$y . '-' . $m]. '</td>'.
                                                    '<td style="background-color: rgb(152, 187, 231) !important;" class="month">' .$profitCentersDataNotValidate['summary'][$y . '-' . $m]. '</td>';
                                                }else{
                                                    $cell_outputs[] = 
                                                    '<td style="background-color: rgb(152, 187, 231) !important;" class="month">' . 0 . '</td>'.
                                                    '<td style="background-color: rgb(152, 187, 231) !important;" class="month">' . 0 . '</td>';
                                                }
                                                $m++;
                                                if ($m == 13) {
                                                    $m = 1;
                                                    $y++;
                                                }
                                            }
                                        ?>

                                        <!-- echo the summary td -->
                                        <?php foreach ($cell_outputs as $index => $cell_output): ?>
                                            <?php echo $cell_output;?>
                                        <?php endforeach;?>

                                    </tr>

                                    <?php 
                                        if(!empty($profitCenters)):
                                            foreach($profitCenters as $key => $profitCenter):
                                    ?>
                                        <!-- render the profit center summary row -->
                                        <tbody id="profit">
                                        <tr class="profit-center">
                                            <td colspan="2"><em class="icon-profit"></em><label class="name-profit"><?php echo $profitCenter;?></label></td>
                                            <?php 
                                                $m = $_minMonth;
                                                $y = $_minYear;

                                                $cell_outputs = array();

                                                // loop throw months and years
                                                while ($y < $_maxYear || ($y == $_maxYear && $m <= $_maxMonth)) {
                                                    
                                                    if(isset($profitCentersData[$key][$y . '-' . $m]) || isset($profitCentersDataNotValidate[$key][$y . '-' . $m])){
                                                        //$total = $profitCentersData[$key][$y . '-' . $m];
                                                        //$cell_outputs[] = $profitCentersData[$key][$y . '-' . $m];
                                                        $cell_outputs[] = 
                                                        '<td style="background-color: #E8F0FA !important;" class="month">' .$profitCentersData[$key][$y . '-' . $m]. '</td>'.
                                                        '<td style="background-color: #E8F0FA !important;" class="month">' . $profitCentersDataNotValidate[$key][$y . '-' . $m] . '</td>'
                                                        ;
                                                    }else{
                                                        $cell_outputs[] = 
                                                        '<td style="background-color: #E8F0FA !important;" class="month">' . 0 . '</td>'.
                                                        '<td style="background-color: #E8F0FA !important;" class="month">' . 0 . '</td>'
                                                        ;
                                                    }
                                                    $m++;
                                                    if ($m == 13) {
                                                        $m = 1;
                                                        $y++;
                                                    }
                                                }
                                            ?>

                                            <!-- echo the summary td -->
                                            <?php foreach ($cell_outputs as $index => $cell_output): ?>
                                                <?php echo $cell_output;?>
                                            <?php endforeach; ?>
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
                                                $cell_outputNotValidates = array();
                                                while ($y < $_maxYear || ($y == $_maxYear && $m <= $_maxMonth)) {

                                                    $_output = '';
                                                    $_outputNotValidate = '';
                                                    if (isset($activities[$employee['Employee']['id']][$y . '-' . $m])) {
                                                        $_output = $activities[$employee['Employee']['id']][$y . '-' . $m];
                                                        $_outputNotValidate = $activityNotValidate[$employee['Employee']['id']][$y . '-' . $m];
                                                    }
                                                    $cell_outputs[] = '<td class="month">' . $_output . '</td>' . '<td class="month">' . $_outputNotValidate . '</td>';
                                                    $m++;
                                                    if ($m == 13) {
                                                        $m = 1;
                                                        $y++;
                                                    }
                                                }
                                                ?>
                                                <!-- render consumed cell - horizontally -->
                                                <?php foreach ($cell_outputs as $cell_output): ?>  
                                                    <?php echo $cell_output; ?>
                                                <?php endforeach; ?>
                                            </tr>
                                            <?php endif;?>
                                        <?php endforeach;?>
                                    </tbody>
                                    <?php 
                                            endforeach;
                                        endif;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .error{
        border: 1px solid red !important;
    }
    #wd-group{
    }
    .wd-end-st{
        overflow: hidden; 
        width: 253px; 
        float: left;
    }
</style>
<script>
    $('.profit-center').toggle(function(){
        $(this).parent().find('tr').not('tr.profit-center').slideUp();
        $(this).find('em').removeClass('icon-profit');
        $(this).find('em').addClass('icon-profit-expand');
    }, function(){
        $(this).parent().find('tr').not('tr.profit-center').slideDown();
        $(this).find('em').removeClass('icon-profit-expand');
        $(this).find('em').addClass('icon-profit');
    });
    
    $("#ControlStart, #ControlEnd").datepicker({
        //showOn          : 'button',
        //buttonImage     : '<?php //echo $html->url("/img/front/calendar.gif") ?>',
        //buttonImageOnly : true,
        dateFormat      : 'dd-mm-yy'
    }); 
    
    function validated(value){
        var _start = $("#ControlStart").val();
        _start = _start.split('-');
        var myStartDate = new Date(_start[2],_start[1],_start[0]);
        _start = Number(myStartDate);
        
        var _end = $("#ControlEnd").val().toString();
        _end = _end.split('-');   
        var myEndDate = new Date(_end[2],_end[1],_end[0]);
        _end = Number(myEndDate);
        if(_start <= _end){
            $('#wd-end-date').removeClass('error');
            $('#wd-group').removeClass('wd-end-st');
            $('.wd-error').css('display', 'none');
            $('#wd-submit').show();
        } else {
            $('#wd-end-date').addClass('error');
            $('#wd-group').addClass('wd-end-st');
            $('.wd-error').css('display', 'block');
            $('#wd-submit').hide();
        }
    }
</script>