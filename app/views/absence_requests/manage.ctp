<?php echo $html->css(array('context/jquery.contextmenu')); ?>
<?php
echo $html->script('context/jquery.contextmenu');
$avg = intval( ($_start + $_end) / 2 );
 ?>
<style type="text/css">
    #absence-fixed thead tr th{height: 43px;text-align: center;vertical-align: middle;}
    #absence-fixed th,#absence-fixed td.st{
        border-right : 1px solid #5fa1c4;
        color: #fff !important;
        text-align: left;
    }
    #absence-fixed .st a{
        color: #fff;
    }
    #absence-fixed .st strong{
        font-size: 0.8em;
        color : #FE4040;
    }
    #absence-scroll {
        overflow-x: scroll;
    }
    .ch-absen-validation{
        background-color: #F0F0F0;
    }
    .monday_am{
        border-left: 2px solid red;
    }
    #absence-table tr td.ch-absen-validation{background-color: #c3dd8c;}
    #absence-wrapper #absence-fixed{ width: 25% !important;}
    #thColID{ width:8%; } #thColEmployee{ width:70%;}
    #absence th.colThDay{min-width:172px;max-width:172px;width:172px;overflow:hidden;}
    .am, .pm{
        overflow:hidden;
        padding-left: 0;
        padding-right: 0;
    }
    .am span, .pm span{
        width:100%;
        word-break:break-all;
    }
    .fixedHeight, .fixedHeight td{
        border-bottom: none !important;
    }
	.wd-tab .wd-panel{
		padding: 0;
		border: none;
	}
	.rp-validated span {
		background-color: unset !important;
	}
	.rp-holiday span {
		background-color: unset !important;
	}
	#absence-table tr td.rp-holiday {
		background-color: #c3dd8c;
	}
</style>
<?php 
$svg_icons = array(
	'message' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(-4 -4)"><rect class="a" width="16" height="16" transform="translate(4 4)"/><path class="b" d="M10.5,8h-5a.5.5,0,0,0,0,1h5a.5.5,0,1,0,0-1ZM8,0C3.581,0,0,3.134,0,7a6.7,6.7,0,0,0,3,5.459V16l4.1-2.048c.3.029.6.047.9.047,4.418,0,8-3.134,8-7S12.417,0,8,0ZM8,13H7L4,14.5V11.891A5.772,5.772,0,0,1,1,7C1,3.686,4.133,1,8,1s7,2.686,7,6S11.865,13,8,13Zm3.5-8h-7a.5.5,0,0,0,0,1h7a.5.5,0,1,0,0-1Z" transform="translate(4.001 4)"/></g></svg>',
		'expand' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(-216 -168)"><rect class="a" width="16" height="16" transform="translate(216 168)"/><path class="b" d="M902-2125h-4v-1h3v-3h1v4Zm-8,0h-4v-4h1v3h3v1Zm8-8h-1v-3h-3v-1h4v4Zm-11,0h-1v-4h4v1h-3v3Z" transform="translate(-672 2307)"/></g></svg>',
		'reload' => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16"><g transform="translate(-1323 -240)"><path class="b" d="M199.5,191.5a7.98,7.98,0,0,0-5.44,2.15v-1.51a.64.64,0,0,0-1.28,0v3.2a.622.622,0,0,0,.113.341l.006.009a.609.609,0,0,0,.156.161c.007.005.01.013.017.018s.021.009.031.015a.652.652,0,0,0,.115.055.662.662,0,0,0,.166.034c.012,0,.023.007.036.007h3.2a.64.64,0,1,0,0-1.28h-1.8a6.706,6.706,0,1,1-2.038,4.8.64.64,0,1,0-1.28,0,8,8,0,1,0,8-8Z" transform="translate(1131.5 48.5)"/><rect class="a" width="16" height="16" transform="translate(1323 240)"/></g></svg>',
		'duplicate' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(-1621.625 -334.663)"><rect class="a" width="16" height="16" transform="translate(1621.625 334.663)"/><g transform="translate(36.824 46.863)"><path class="b" d="M1586.915,301.177a1.116,1.116,0,0,1-1.115-1.115V288.915a1.116,1.116,0,0,1,1.115-1.115h8.525a1.116,1.116,0,0,1,1.115,1.115v11.147a1.115,1.115,0,0,1-1.115,1.115Zm0-12.459a.2.2,0,0,0-.2.2v11.147a.2.2,0,0,0,.2.2h8.525a.2.2,0,0,0,.2-.2V288.915a.2.2,0,0,0-.2-.2Z"/><path class="b" d="M1590.915,305.177a1.116,1.116,0,0,1-1.115-1.115v-.656a.459.459,0,1,1,.918,0v.656a.2.2,0,0,0,.2.2h8.525a.2.2,0,0,0,.2-.2V292.915a.2.2,0,0,0-.2-.2h-.656a.459.459,0,0,1,0-.918h.656a1.116,1.116,0,0,1,1.115,1.115v11.147a1.115,1.115,0,0,1-1.115,1.115Z" transform="translate(-0.754 -1.377)"/></g></g></svg>',
		'agenda' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(0)"><rect class="a" width="16" height="16" transform="translate(0)"/><path class="b" d="M15,16H1a1,1,0,0,1-1-1V2.5a1,1,0,0,1,1-1h4V.5a.5.5,0,0,1,1,0v1h4V.5a.5.5,0,1,1,1,0v1h4a1,1,0,0,1,1,1V15A1,1,0,0,1,15,16ZM15,2.5H11V3a.5.5,0,1,1-1,0V2.5H6V3a.5.5,0,0,1-1,0V2.5H1V15H15ZM3.5,6h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1A.5.5,0,0,1,3,7.5v-1A.5.5,0,0,1,3.5,6Zm0,4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,3.5,10Zm4-4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1A.5.5,0,0,1,7,7.5v-1A.5.5,0,0,1,7.5,6Zm0,4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,7.5,10Zm4-4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,11.5,6Zm0,4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,11.5,10Z" transform="translate(0.001 -0.001)"/></g></svg>',
		'add' => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16.002 16.002"><g transform="translate(-120 -231.999)"><rect class="a" width="16" height="16" transform="translate(120 231.999)"/><path class="b" d="M21284,8418v-6h-6a1,1,0,0,1,0-2h6v-6a1,1,0,1,1,2,0v6h6a1,1,0,0,1,0,2h-6v6a1,1,0,1,1-2,0Z" transform="translate(-21157 -8171)"/></g></svg>',
		'users' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(-192 -132)"><rect class="a" width="16" height="16" transform="translate(192 132)"/><g transform="translate(192 134)"><path class="b" d="M205.507,144.938a4.925,4.925,0,0,0-9.707,0h-1.1a6.093,6.093,0,0,1,3.557-4.665l.211-.093-.183-.14a3.941,3.941,0,1,1,4.75,0l-.183.14.21.093a6.1,6.1,0,0,1,3.552,4.664Zm-4.851-10.909a2.864,2.864,0,1,0,2.854,2.864A2.863,2.863,0,0,0,200.657,134.029Z" transform="translate(-194.697 -132.938)"/><path class="b" d="M214.564,143.9a2.876,2.876,0,0,0-2.271-2.665.572.572,0,0,1-.449-.555.623.623,0,0,1,.239-.507,2.869,2.869,0,0,0-1.344-5.114,4.885,4.885,0,0,0-.272-.553,5.52,5.52,0,0,0-.351-.556c.082-.005.164-.008.245-.008a3.946,3.946,0,0,1,3.929,3.955,3.844,3.844,0,0,1-.827,2.406l-.1.13.147.076a3.959,3.959,0,0,1,2.132,3.392Z" transform="translate(-199.639 -133.26)"/></g></g></svg>',
		'validated' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 40 40"><g transform="translate(-317 -66)"><rect class="a" width="32" height="32" transform="translate(317 66)"/><path class="b" d="M9.791,1.412h0L4.314,7.757h0a.648.648,0,0,1-1.01,0h0L.209,4.171h0A.9.9,0,0,1,0,3.585a.777.777,0,0,1,.714-.827A.668.668,0,0,1,1.219,3h0l2.59,3L8.781.242h0A.668.668,0,0,1,9.285,0,.778.778,0,0,1,10,.827.9.9,0,0,1,9.791,1.412Z" transform="translate(332 82.001)"/></g></svg>',
		'reject' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32 viewBox="0 0 40 40"><g transform="translate(-323 -71)"><rect class="a" width="32" height="32" transform="translate(-323 -71)"/><path class="b" d="M-287.4-1709.767a.62.62,0,0,1,0-.876l3.942-3.942-3.942-3.943a.619.619,0,0,1,0-.876.619.619,0,0,1,.876,0l3.943,3.943,3.943-3.943a.619.619,0,0,1,.876,0,.619.619,0,0,1,0,.876l-3.942,3.943,3.942,3.942a.62.62,0,0,1,0,.876.62.62,0,0,1-.876,0l-3.943-3.943-3.943,3.943a.618.618,0,0,1-.438.182A.618.618,0,0,1-287.4-1709.767Z" transform="translate(620.586 1800.587)"/></g></svg>'
);

?>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"><div class="wd-panel">
            <div class="wd-list-project">
                <div id="message-place">
                    <?php
                    echo $this->Session->flash();
                    $am = __('AM', true);
                    $pm = __('PM', true);

                    $dayMaps = array(
                        'monday' => $_start,
                        'tuesday' => $_start + DAY,
                        'wednesday' => $_start + (DAY * 2),
                        'thursday' => $_start + (DAY * 3),
                        'friday' => $_start + (DAY * 4),
                        'saturday' => $_start + (DAY * 5),
                        'sunday' => $_start + (DAY * 6)
                    );
                    ?>
                </div>
                <div class="wd-table" id="project_container" style="width:100%;">
                    <div id="absence-container" style="min-height:400px;">
                        <div id="table-control" class="wd-activity-actions" style="overflow: visible; min-width:1024px;">
                            <?php
                            echo $this->Form->create('Control', array(
                                'type' => 'get',
                                'url' => '/' . Router::normalize($this->here)));
                            ?>
                            <fieldset style="margin-left: 22px;">
                                <select style="padding:6px;" name="typeRequest" id="typeRequest">
                                    <option value="week" <?php echo $typeSelect=='week'?'selected':'';?>><?php echo __('Week',true);?></option>
                                    <option value="month" <?php echo $typeSelect=='month'?'selected':'';?>><?php echo __('Month',true);?></option>
                                    <!-- <option value="year" <?php //echo $typeSelect=='year'?'selected':'';?>><?php echo __('Year',true);?></option> -->
                                </select>
                                <?php echo $this->element('week_activity') ?>
                                
                                <?php echo $this->Form->select('profit', $paths, $profit['id'], array('empty' => false, 'escape' => false, 'style' => 'padding: 6px')) ?>
                                <?php
                                $params = array_combine(array_keys($constraint), Set::extract('{s}.name', $constraint));
                                unset($params['forecast'], $params['holiday']);
                                echo $this->Form->select('st', $params, $status, array(
                                    'empty' => __('--- Status ---', true), 'escape' => false, 'style' => 'padding: 6px'));
                                ?>
                                <button class="btn btn-go"></button>
                                <?php echo $this->element('btn_expand_pc') ?>
                                <a href="javascript:;" onclick="expandScreen();" id="expand-btn" class="btn btn-fullscreen"><?php echo $svg_icons['expand']; ?></a>
                                <a href="javascript:void(0)" id="submit-request-ok-top" class="validate-for-validate validate-for-validate-top validate-month" title="<?php __('Validate Requested')?>"><?php echo $svg_icons['validated']; ?></a>
                                <a href="javascript:void(0)" id="submit-request-no-top" class="validate-for-reject validate-for-reject-top reject-month" title="<?php __('Reject Requested')?>"><?php echo $svg_icons['reject']; ?></a>
                                <div style="clear:both;"></div>
                            </fieldset>
                            <?php
                            echo $this->Form->end();
                            ?>
                        </div>
                        <?php
                        $urlRequests = array('controller' => 'absence_requests', 'action' => 'manage', '?' => array(
                                        'st' => $status, 'profit' => $profit['id'], 'week' => date('W', $avg), 'year' => date('Y', $avg), 'get_path' => $getDataByPath));
                        if($typeSelect === 'month'){
                            $urlRequests = array('controller' => 'absence_requests', 'action' => 'manage','month', '?' => array('month' => date('m', $_start), 'year' => date('Y', $_start),
                                        'st' => $status, 'profit' => $profit['id'], 'get_path' => $getDataByPath));
                        } elseif($typeSelect === 'year') {
                            $urlRequests = array('controller' => 'absence_requests', 'action' => 'manage','year', '?' => array('month' => 1, 'year' => date('Y', $_start),
                                        'st' => $status, 'profit' => $profit['id'], 'get_path' => $getDataByPath));
                        }
                        if($getDataByPath)
                        $urlRequests['get_path'] = $getDataByPath;
                        echo $this->Form->create('Request', array(
                            'escape' => false, 'id' => 'request-form', 'type' => 'post',
                            'url' => $urlRequests));
                        ?>
                        <div id="absence-wrapper">
                            <div id="scrollTopAbsence" class="useLeftScroll"><div id="scrollTopAbsenceContent"></div></div>
                            <br clear="all"  />
                            <div id="scrollLeftAbsence">
                                <div id="scrollLeftAbsenceContent"></div>
                            </div>
                            <table id="absence-fixed">
                            <tr class="elmTemp">
                            <td class="elmTemp">
                            <table>
                                <thead>
                                    <tr class="header-height-fixed">
                                        <th id="thColID"><?php __('#'); ?></th>
                                        <th id="thColEmployee"><?php __('Employee'); ?></th>
                                        <th id="thColCapacity"><?php __('Capacity'); ?></th>
                                    </tr>
                                </thead>
                             </table>
                             </td>
                             </tr>
                             <tr class="elmTemp">
                                <td class="elmTemp">
                                    <div class="tbl-tbody" >
                                    <table>
                                        <tbody id="absence-table-fixed"></tbody>
                                     </table>
                                    </div>
                                </td>
                             </tr>
                            </table>
                            <div id="absence-scroll">
                                <table id="absence">
                                <tr class="elmTemp">
                                <td class="elmTemp">
                                <table>
                                    <thead>
                                        <tr class="header-height">
                                        <?php
                                            $trTop = $trBot = '';
                                            if(!empty($listWorkingDays)){
                                                $j=0;
                                                foreach($listWorkingDays as $key => $val){
                                                    $j++;
                                                    $_top = __(date('l', $val), true) . __(date(' d ', $val), true) . __(date('M', $val), true);
                                                    $trTop .= '<th class="colThDay" colspan="2">' . $_top . '</th>';
                                                    $trBot .= '<th id="colThAm'.$j.'" class="colThAm">' . $am . '</th><th id="colThPm'.$j.'" class="colThPm">' . $pm . '</th>';
                                                }
                                            }
                                        ?>
                                            <?php echo $trTop;?>
                                        </tr>
                                        <tr>
                                            <?php echo $trBot;?>
                                        </tr>
                                    </thead>
                                    </table>
                                    </td></tr>
                                    <tr class="elmTemp"><td class="elmTemp">
                                    <div class="tbl-tbody" >
                                    <table >
                                    <tbody id="absence-table">
                                        <tr><td colspan="15">&nbsp;</td></tr>
                                    </tbody>
                                    </table>
                                    </div>
                                    </td></tr>
                                </table>
                            </div>
                        </div>
                        <div class="wd-title">
                            <a href="javascript:void(0)" id="submit-request-ok" class="validate-for-validate validate-for-validate-bottom" title="<?php __('Validate Requested')?>"><span><?php __('Validate Requested'); ?></span></a>
                            <a style="margin-left: 53px; margin-top: -32px;" href="javascript:void(0)" id="submit-request-no" class="validate-for-reject validate-for-reject-bottom" title="<?php __('Reject Requested')?>"><span><?php __('Reject Requested'); ?></span></a>
                        </div>
                        <?php
                        echo $this->Form->input('ls', array('name' => 'data[ls]', 'type' => 'hidden', 'value' => time() - 1E9));
                        echo $this->Form->hidden('validated', array('name' => 'data[validated]', 'value' => 0, 'id' => 'ac-validated'));
                        echo $this->Form->end();
                        ?>
                    </div>
                </div>
            </div></div></div>
        </div>
    </div>
</div>
<?php
$dataView = array();

foreach ($employees as $id => $employee) {
    foreach ($listWorkingDays as $day => $time) {
        $default = array(
            'date' => $time,
            'absence_am' => 0,
            'absence_pm' => 0,
            'response_am' => 0,
            'response_pm' => 0,
            'employee_id' => $id
        );
        if (isset($requests[$id][$time])) {
            unset($requests[$id][$time]['date'], $requests[$id][$time]['employee_id']);
            $default = array_merge($default, array_filter($requests[$id][$time]));
            if (!empty($default['history'])) {
                $default['history'] = unserialize($default['history']);
            }
        }
        $dataView[$id][$day] = $default;
    }
}
$css = '';
$ctClass = array();
foreach ($constraint as $key => $data) {
    $ctClass[] = "rp-$key";
    $css .= ".rp-$key span {background-color : {$data['color']};}";
}
$ctClass = implode(' ', $ctClass);
$i18ns = array(
    'Add a comment' => __('Add a comment', true),
    'Summary' => __('Summary', true),
    'Holiday' => __('Holiday', true),
    'Date requesting' => __('Date requesting', true),
    'Date validate' => __('Date validate', true),
    'Date reject' => __('Date reject', true),
);
echo '<style type="text/css">' . $css . '</style>';
?>
<div style="display: none;" id="message-template">
    <div class="message error"><?php echo __('Cannot connect to server ...', true); ?><a href="#" class="close">x</a></div>
</div>
<!-- dialog_vision_portfolio -->
<div id="add-comment-dialog" class="buttons" style="display: none;" title="<?php echo __('Add new comments', true) ?>">
    <fieldset>
        <textarea rel="no-history" name="comment"></textarea>
    </fieldset>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"></a></li>
        <li><a href="javascript:void(0)" class="ok"></a></li>
    </ul>
</div>
<!-- dialog_vision_portfolio.end -->
<div id="dialog-request-all" class="buttons" style="display: none;" title="<?php echo __('Send request for validation', true) ?>">
    <div class="dialog-request-message">

    </div>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="ok"><?php __('OK') ?></a></li>
    </ul>
</div>
<script type="text/javascript">
var gw = 15;
</script>
<![if IE]>
<script type="text/javascript">
var gw = 8;
</script>
<![endif]>
<script type="text/javascript">
	var showAllPicture = parseInt(<?php echo json_encode( isset( $companyConfigs['display_picture_all_resource']) ? $companyConfigs['display_picture_all_resource'] : 0); ?>);
    (function($){
        $(function(){
            var openDialog = function(title,callback){
                var $dialog = $('#dialog-request-all').attr('title' , title);
                $dialog.dialog({
                    zIndex : 10000,
                    modal : true,
                    minHeight : 50,
                    close : function(){
                        $dialog.dialog('destroy');
                    }
                });
                $dialog.find('a.ok').unbind().click(function(){
                    if(!$.isFunction(callback)){
                        $dialog.dialog('close');
                    }else{
                        callback.call(this);
                    }
                    return false;
                });
                $dialog.find('a.cancel').unbind().click(function(){
                    $dialog.dialog('close');
                    return false;
                }).toggle($.isFunction(callback));
            };
            $('#submit-request-no, #submit-request-no-top').click(function(){
                var $form = $('#request-form'),$input = $form.find('.checkbox :checked');
                if(!$input.length){
                    openDialog('<?php echo h(__('Reject Absence request?', true)); ?>');
                    $('#dialog-request-all .dialog-request-message').html('<?php echo h(__('Please select the employees.', true)); ?>');
                }else{
                    openDialog('<?php echo h(__('Reject Absence request?', true)); ?>',function(){
                        $('#ac-validated').val(0);
                        $form.submit();
                    });
                    $('#dialog-request-all .dialog-request-message').html('<?php echo h(__('Are you sure to reject request of selected employees for this week?', true)); ?>');
                }
            });
            $('#submit-request-ok, #submit-request-ok-top').click(function(){
                var $form = $('#request-form'),$input = $form.find('.checkbox :checked');
                if(!$input.length){
                    openDialog('<?php echo h(__('Validate Absence request?', true)); ?>');
                    $('#dialog-request-all .dialog-request-message').html('<?php echo h(__('Please select the employees.', true)); ?>');
                }else{
                    openDialog('<?php echo h(__('Validate Absence request?', true)); ?>',function(){
                        $('#ac-validated').val(1);
                        $form.submit();
                    });
                    $('#dialog-request-all .dialog-request-message').html('<?php echo h(__('Are you sure to validate request of selected employees for this week?', true)); ?>');
                }
            });
            var updateUrl = <?php echo json_encode($this->Html->url(array('action' => 'manage_update', $profit['id'], $_start, $_end, $typeSelect))); ?>,
            updateUrl2 = <?php echo json_encode($this->Html->url(array('action' => 'comment_update'))); ?>,
            deleteUrl2 = <?php echo json_encode($this->Html->url(array('action' => 'comment_delete'))); ?>,
            dataSets = <?php echo json_encode($dataView); ?>,
            comments = <?php echo json_encode(@$comments); ?> || {},
            holidays = <?php echo json_encode(@$holidays); ?> || {},
            absences = <?php echo json_encode($absences); ?>,
            constraint = <?php echo json_encode($constraint); ?>,
            workdays = <?php echo json_encode($workdays); ?>,
            employees = <?php echo json_encode($employees); ?>,
            ctClass = <?php echo json_encode($ctClass); ?>,
            employeeName = <?php echo json_encode($employeeName); ?>,
            dayHasValidations = <?php echo json_encode($dayHasValidations); ?>,
            statusConfirms = <?php echo json_encode($statusConfirms); ?>,
            totalWeek = <?php echo json_encode($totalWeek); ?>,
            typeSelect = <?php echo json_encode($typeSelect);?>,
            daysInWeek = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
            $container = $('#absence-table').html(''),
            $containerFixed = $('#absence-table-fixed').html('');
            var requestURL = '';
            if(typeSelect === 'week'){
                requestURL = <?php echo json_encode(urldecode($this->Html->link('%1$s', array('action' => 'index',$typeSelect, '?' => array('id' => '%2$s', 'profit' => $profit['id'], 'week' => date('W', $avg), 'year' => date('Y', $avg), 'get_path' => $getDataByPath)), array('escape' => false)))); ?>;
            } else if(typeSelect === 'month'){
                requestURL = <?php echo json_encode(urldecode($this->Html->link('%1$s', array('action' => 'index',$typeSelect, '?' => array('id' => '%2$s', 'profit' => $profit['id'], 'month' => date('m', $_start), 'year' => date('Y', $_start), 'get_path' => $getDataByPath)), array('escape' => false)))); ?>;
            } else {
                requestURL = <?php echo json_encode(urldecode($this->Html->link('%1$s', array('action' => 'index',$typeSelect, '?' => array('id' => '%2$s', 'profit' => $profit['id'], 'month' => date('m', $_start), 'year' => date('Y', $_start), 'get_path' => $getDataByPath)), array('escape' => false)))); ?>;
            }
            /* ------------Sort dataSets ------------------*/
            function sortObj(arr, dataSets){
                var temp = new Array();
                $.each(arr, function(key, value) {
                    temp.push({v:value, k: key});
                });
                temp.sort(function(a,b){
                   if(a.v > b.v){ return 1}
                    if(a.v < b.v){ return -1}
                      return 0;
                });
                var tmpObj = {};
                $.each(temp, function(key, val){
                    tmpObj['e_'+val.k] = val.v;
                });
                var tmpResults = {};
                $.each(tmpObj, function(key, value){
                    var id = key.replace('e_', '');
                    tmpResults[key] = dataSets[id];
                });
                return tmpResults;
            }
            var dataSets = sortObj(employees, dataSets);
            /* ------------Sort dataSets End --------------*/
            /**
             * Translate strings to the page language or a given language.
             */
            var i18ns = <?php echo json_encode($i18ns); ?>;
            var format = function(str,args) {
                var regex = /%(\d+\$)?(s)/g,
                i = 0;
                return str.replace(regex, function (substring, valueIndex, type) {
                    var value = valueIndex ? args[valueIndex.slice(0, -1)-1] : args[i++];
                    switch (type) {
                        case 's':
                            console.log( 1,  String(value));
                            return String(value);
                        default:
						console.log( 2, substring);
                            return substring;
                    }
                });
            };
            var t = function (str,args) {

                if (i18ns[str]) {
                    str = i18ns[str];
                }
                if(args === undefined){
                    return str;
                }
                if (!$.isArray(args)) {
                    args = $.makeArray(arguments);
                    args.shift();
                }
                return format(str, args);
            };
            var parseHandler = function(callback , $list ,  data){
                $('#message-place').html(data.message);
                setTimeout(function(){
                    $('#message-place .message').fadeOut('slow');
                } , 5000);
                callback($list , data);
            };
            var syncHandler = function(args , dsubmit , callback , check){
                var submit = {}, $list = $(this).find('td.ui-selected');
                $list.each(function(){
                    var $el = $(this),_ds = dataSets['e_'+$el.attr('dx')][$el.attr('dy')];
                    if(!_ds || $el.hasClass('loading') || ($.isFunction(check) && check($el) === false)){
                        return;
                    }
                    if(!submit[_ds.employee_id]){
                        submit[_ds.employee_id] = {};
                    }
                    if(!submit[_ds.employee_id][_ds.date]){
                        submit[_ds.employee_id][_ds.date] = {
                            date: _ds.date,
                            employee_id : _ds.employee_id
                        };
                    }
                    submit[_ds.employee_id][_ds.date][$el.hasClass('am') ? 'am' : 'pm'] = args.value;
                    $el.addClass('loading');
                });
                args.url = args.url+'?get_path=<?php echo $getDataByPath; ?>';
                if(!$.isEmptyObject(submit)){
                    $.ajax({
                        url : args.url,
                        cache : false,
                        type : 'POST',
                        dataType : 'json',
                        data : {
                            data : $.extend(dsubmit,submit)
                        },
                        success : function(data){
                            parseHandler(callback, $list, data);
                        },
                        error : function(){
                            parseHandler(callback , $list, {
                                error : true,
                                message : $('#message-template').html()
                            });
                        }
                    });
                }
            };
            var checkHistory = function(type,dx){
                return !!(dx && (dx['rq_' + type] || dx['rp_' + type]));
            };
            /* --------Custom--------- */
            var absenceHandler = function(data){
                syncHandler.call( this ,{ value : true, url :  updateUrl} , {
                    response : data
                } , function($list , data){
                    var j = 0;
                    $list.each(function(){
                        var j
                        var $ct,type,$el = $(this),_ds = dataSets['e_'+$el.attr('dx')][$el.attr('dy')],
                        type = $el.hasClass('am') ? 'am' : 'pm';
                        if(data[_ds.employee_id]){
                            var res = data[_ds.employee_id][_ds.date];
                            if(res && res.result){
                                _ds = $.extend(_ds , res || {});
                                type = _ds['response_' + type];
                                $el.removeClass(ctClass);
                                //$ct = $el.parent().find('.ct span');
                                $ct = '.' + $el.parent().attr('class').split(' ')[1];
                                $ct = $('#absence-table-fixed').find($ct + ' .ct span');
                                if(!type || type =='rejetion'){
                                    $ct.html(parseFloat($ct.html()) + 0.5);
                                    $el.removeClass('ui-selected response').addClass('workday').find('span').html('0.5');
                                }
                                $el.addClass('rp-' +  type);
                            }
                            if(res && checkHistory(type,res.history)){
                                $el.addClass('has-comment');
                            }
                        }
                        $el.removeClass('loading');
                    });
                }, function($el){
                    return $el.hasClass('response') && !$el.hasClass('rp-forecast');
                });
            };
            /* --------Check day has activity request--------- */
            var checkHasAcRequest = function(day, listDays){
                var result = false;
                if(listDays){
                    $.each(listDays, function(ind, val){
                        if(parseInt(day) === parseInt(val)){
                            result = true;
                            return result;
                        }
                    });
                }
                return result;
            };
            /* --------Dem so ngay co duoc cua 1 tuan/thang/nam--------- */
            var countHasAcRequest = function(listDays){
                var result = 0;
                if(listDays){
                    $.each(listDays, function(ind, val){
                        result++
                    });
                }
                return result;
            };
            /* --------Draw table--------- */
            var dataSum = {total : 0}, sumText = sumTextFixed = '';
            var status = {
                0 : t('Requested'),
                2 : t('Validated')
            };
            var objectLength =function (obj) {

                var result = 0;
                for(var prop in obj) {
                    if (obj.hasOwnProperty(prop)) {
                    // or Object.prototype.hasOwnProperty.call(obj, prop)
                      result++;
                    }
                }
                return result;
            };
            var objectToArray =function (obj) {

                var result = [];
                for(var prop in obj) {
                    if (obj.hasOwnProperty(prop)) {
                    // or Object.prototype.hasOwnProperty.call(obj, prop)
                      result.push(prop);
                    }
                }
                return result;
            };

            $.each(dataSets, function(id){

                var id = id.replace('e_', '');
                var output = '', total = 0;
                var statusDisplay = new Array();
                var select = true;
                var _dayHasValidations = dayHasValidations[id] ? dayHasValidations[id] : [];
                var j =0 ;
                $.each(this ,function(day , data){
                    j++;
                    var _day = day * 1000;
                    _day = new Date(_day);
                    _day = daysInWeek[_day.getDay()];
                    var val = parseFloat(workdays[_day]),dt = holidays[data.date] || {},
                    opt = {am : {className : ['am',day] , value : 0}, pm : {className : ['pm',day] , value : 0}} ;
                    var _statusConfirms = statusConfirms[id] ? statusConfirms[id] : [];
                    if(_statusConfirms.length != 0){
                        if(_statusConfirms.length > 1){
                            statusDisplay[id] = 0;
                        } else {
                            var $keyStatus=objectToArray(_statusConfirms);
                            statusDisplay[id] = _statusConfirms[$keyStatus[0]]; //lay phan tu dau tien cua obj
                        }
                        select = false;
                    } else {
                        statusDisplay[id] = '';
                    }
                    switch(val){
                        case 1:
                            if(!dt['am']){
                                //select && opt['am'].className.push('selectable');
                                if(checkHasAcRequest(day, _dayHasValidations)){
                                    opt['am'].className.push('ch-absen-validation');
                                } else {
                                    opt['am'].className.push('selectable');
                                }
                            }else{
                                opt['am'].className.push('rp-holiday');
                                opt['am'].value = t('Holiday');
                            }
                            if(!dt['pm']){
                                //select && opt['pm'].className.push('selectable');
                                if(checkHasAcRequest(day, _dayHasValidations)){
                                    opt['pm'].className.push('ch-absen-validation');
                                } else {
                                    opt['pm'].className.push('selectable');
                                }
                            }else{
                                opt['pm'].className.push('rp-holiday');
                                opt['pm'].value = t('Holiday');
                            }
                            break;
                        case 0.5:
                            if(!dt['am']){
                                //select && opt['am'].className.push('selectable');
                                if(checkHasAcRequest(day, _dayHasValidations)){
                                    opt['am'].className.push('ch-absen-validation');
                                } else {
                                    opt['am'].className.push('selectable');
                                }
                            }else{
                                opt['am'].className.push('rp-holiday');
                                opt['am'].value = t('Holiday');
                            }
                    }

                    $.each(['am','pm'] , function(){
                        try {
                            if(checkHistory(this,data.history) || comments[data.employee_id][data.date][this]){
                                opt[this].className.push('has-comment');
                            }
                        }catch(ex){};
                        if(data['absence_' +  this]){
                            opt[this].value = (absences[data['absence_' +  this]] || {}).print || t('Hidden');
                            opt[this].className.push(data['absence_' +  this]);
                            if(data['response_' +  this] != 'validated' || !data['response_' +  this]){
                                opt[this].className.push('workday');
                            }
                            if(data['response_' +  this]){
                                opt[this].className.push('response rp-' + data['response_' +  this]);
                            }
                        }else{
                            switch(true){
                                case val == 0.5 && this == 'am' && !dt['am']:
                                    total += 0.5;
                                    opt['am'].className.push('workday');
                                    opt['am'].value = 0.5;
                                    break;
                                case val == 1 && !dt[this]:
                                    total += 0.5;
                                    opt[this].className.push('workday');
                                    opt[this].value = 0.5;
                                    break;
                            }
                            if(data['response_' +  this]){
                                opt[this].className.push('rp-' + data['response_' +  this]);
                            }
                        }
                        if(!dataSum[day+this]){
                            dataSum[day+this] = 0;
                        }
                        if($.isNumeric(opt[this].value)){
                            dataSum[day+this] += opt[this].value;
                        }
                        opt[this].className.push(_day + '_' + this);
                    });
                    $.each(opt, function(){
                        output+= '<td dx="' + id + '" dy="' + day + '" class="' + this.className[0]+j+' '+ this.className.join(' ') +'"><span>' + this.value + '</span></td>';
                    });
                });
                var lenghtTemp=0;
                //var length = 0;
                //for(var prop in _dayHasValidations){
                    //if(data.hasOwnProperty(prop))
                        //lenghtTemp++;
                //}

                lenghtTemp=objectLength(_dayHasValidations);

                if(lenghtTemp != totalWeek){
                    statusDisplay[id] = '';
                    select = true;
                }
                dataSum.total += total;
				console.log( 'xx', requestURL,employees[id]);
				console.log( 'yy', t(requestURL,employees[id] , id));
                $containerFixed.append('<tr class="fixedHeight height_' +id+ '"><td class="no checkbox"> ' + (select ? '<input type="checkbox" name="data[id][]" value="' + id + '" /> ' : '') + '</td><td class="st12"><span>' + t(requestURL,employees[id] , id)
                    + (select ? '' : ('<strong> (' + status[statusDisplay[id]] + ')</strong>'))
                    + '</span><td class="ct"><span>' + total + '</span></td></tr>');
                $container.append('<tr class="fixedHeight height_' +id+ '">' + output + '</tr>');
            });
            sumText += '<tr class="space"><td colspan="1000"><span>&nbsp;</span></td></tr>';
            sumTextFixed += '<tr style="height:27px;"><td colspan="3"></td></tr><tr class="summary"><td class="ct" colspan="2"><span>' + t('Summary') + '</span><td class="ct"><span>' + dataSum.total + '</span></td></tr>';
            delete dataSum.total;
            $.each(dataSum , function(){
                sumText += '<td class=""><span>' + this + '</span></td>';
            });
            $container.append(sumText + '</tr>');
            $containerFixed.append(sumTextFixed);

            $('#absence-table .st a').mousedown(function(){
                window.location = $(this).attr('href');
            });

            var contextMenu = {hide : $.noop};
            $container.selectable({
                filter : 'td.selectable',
                unselected : function(){
                    contextMenu.hide();
                },
                selected : function(undefined, u){
                    removeTooltip(u.selected);
                }
            });
            var absenceHistory = function($el,type,data){
                var $list = $(this).find('ul.list-comment');
                var $info = $list.find('.info-comment').html('');
                if(!$info.length){
                    $info = $('<div class="comment info-comment"></div>');
                    var $del = $('<a class="close" title="'+ t('Close') +'">x</a>').click(function(){
                        $(this).closest('li').hide();
                        if($list.children().length == 1){
                            $.each(data , function(i){
                                delete data[i];
                            });
                            $el.removeClass('has-comment');
                            $el.tooltip('close');
                            $el.tooltip('disable');
                        }
                        return false;
                    });
                    $list.prepend($(t('<li class="info"><h4 class="title">%s</h4></li>' , t('Absence information'))).append($del).append($info));
                }
                if(data['rq_' + type]){
                    $info.append('<span><strong>' + t('Date requesting') + '</strong>: ' + String(data['rq_' + type]) +  '</span>');
                }
                if(data['rv_' + type]){
                    $info.append('<span><strong>' + t('Date validate') + '</strong>: ' + String(data['rv_' + type]) +  '</span>');
                }
                if(data['rj_' + type]){
                    $info.append('<span><strong>' + t('Date reject') + '</strong>: ' + String(data['rj_' + type]) +  '</span>');
                }
            };
            /* --------Comment--------- */
            var removeComment = function($el,id){
                $.ajax({
                    url : deleteUrl2,
                    cache : false,
                    type : 'GET',
                    data : {
                        id : id
                    }
                });
                if(this.siblings().length == 0){
                    $el.removeClass('has-comment');
                    $el.tooltip('close');
                    $el.tooltip('disable');
                }
                this.remove();
            };
            var initComment = function(){
                var $el = $(this) , $widget = $el.tooltip('widget');
                if($widget.is($el)){
                    $el.tooltip({
                        width : 300,
                        maxHeight : 150,
                        hold : 1000,
                        openEvent : 'mouseenter',
                        closeEvent : 'xmouseleave',
                        content : '<ul class="list-comment" />',
                        open: function(){
                            $el.addClass('comment-open');
                            removeTooltip($el.get(0));
                        }
                    });
                    $widget = $el.tooltip('widget').click(function(e){
                        e.preventDefault();
                        e.stopImmediatePropagation();
                    });
                }
                var ds = dataSets['e_'+$el.attr('dx')][$el.attr('dy')],$list = $widget.find('ul');
                var type = $el.hasClass('am') ? 'am' : 'pm';
                try {
                    $.each(comments[ds.employee_id][ds.date][type],function(i,v){
                        if(v.user_id == employeeName['id']){
                            var del = $('<a href="javascript:void(0);" class="close" title="'+ t('Delete this comment, you can\'t undo it.') +'">x</a>').click(function(){
                                removeComment.call($(this).parent() ,$el ,i);
                            });
                            $list.append($(t('<li><h4 class="title">%s <span class="date">(%s)</span> : </h4><div class="comment">%s</div></li>' , t('You'),v.created ,v.text)).append(del));
                        }else{
                            $list.append($(t('<li><h4 class="title">%s <span class="date">(%s)</span>: </h4><div class="comment">%s</div></li>' , employees[v.user_id],v.created,v.text)).append(del));
                        }
                        delete comments[ds.employee_id][ds.date][type][i];
                    });
                }catch(ex){};
                $el.tooltip('enable');
                checkHistory(type,ds.history) && absenceHistory.call($widget,$el,type,ds.history);
                return $widget;
            };
            var commentHandler = function(data){
                syncHandler.call(this ,{ value : data, url :  updateUrl2} , {} , function($list , data){
                    $list.each(function(){
                        var $el = $(this),_ds = dataSets['e_'+$el.attr('dx')][$el.attr('dy')],
                        type = $el.hasClass('am') ? 'am' : 'pm';
                        if(data[_ds.employee_id]){
                            var res = data[_ds.employee_id][_ds.date];
                            if(res.result){
                                if(!comments[_ds.employee_id]){
                                    comments[_ds.employee_id] = {};
                                }
                                if(!comments[_ds.employee_id][_ds.date]){
                                    comments[_ds.employee_id][_ds.date] = {};
                                }
                                if(!comments[_ds.employee_id][_ds.date][type]){
                                    comments[_ds.employee_id][_ds.date][type] = {};
                                }
                                comments[_ds.employee_id][_ds.date][type][res['id_'+type]] = {
                                    text : res[type],
                                    employee_id : _ds.employee_id,
                                    user_id : employeeName['id'],
                                    created : res.created
                                };
                                $el.addClass('has-comment');
                                initComment.call($el.get(0));
                            }
                        }
                        $el.removeClass('loading');
                    });
                });
            };
            var removeTooltip = function(self){
                $('#absence-table .comment-open').not(self).each(function(){
                    $(this).removeClass('comment-open').tooltip('close');
                });
            };
            $(document).on("mouseenter", "#absence-table .has-comment", function(e){
                var $widget = initComment.call(this);
                if($widget.is(':hidden')){
                    $(this).trigger('mouseenter', e);
                }
            });
            $(document).on("mouseleave", "#absence-table .has-comment", function(e){
                $(this).tooltip('clear');
            });
            $(document).click(function(e){
                removeTooltip($(e.target).closest('td.selectable').get(0));
            });
            /* -------------------------------------- */

            (function(){
                var menu = [{}];
                menu[0][t('Add a comment')] = {
                    onclick : function(imenu, cmenu , e){
                        var $dialog = $('#add-comment-dialog'),self = this;
                        $dialog.dialog({
                            zIndex : 10000,
                            modal : true,
                            close : function(){
                                $dialog.dialog('destroy');
                            }
                        });
                        $dialog.find('textarea').val('');
                        $dialog.find('a.ok').unbind().click(function(){
                            var val = $dialog.find('textarea').val();
                            if(val){
                                commentHandler.call(self, val);
                                $dialog.dialog('close');
                            }else{
                                $dialog.find('textarea').focus();
                            }
                            return false;
                        });
                        $dialog.find('a.cancel').unbind().click(function(){
                            $dialog.dialog('close');
                            return false;
                        });
                    },
                    className: 'add-comment', disabled: false
                };
                $.each(constraint , function(key, data){
                    if(key == 'holiday' || key == 'forecast'){
                        return;
                    }
                    var opt = {};
                    opt[data.name] = {
                        onclick : function(imenu, cmenu , e){
                            absenceHandler.call(this, key, imenu , cmenu , e);
                        },
                        className: 'rp-'+key, disabled: false, title: data.name
                    };
                    menu.push(opt);
                });
                $container.contextMenu(menu, {theme : 'vista' , beforeShow : function(){
                        contextMenu = this;
                        if(!$container.find('td.ui-selected').length){
                            return false;
                        }
                        this.menu.width('200');
                    }});
            })();
            function fixedHeightScreen()
            {
                $.each(employees, function(id, name){
                    var i = $('#absence-scroll').find('.height_' + id).height();
                    var j = $('#absence-fixed').find('.height_' + id).height();
                    if( i > j ){
                        if($.browser.mozilla == true){
                            $('#absence-fixed').find('.height_' + id).css("height", i);
                        } else {
                            $('#absence-fixed').find('.height_' + id).css("height", i+1);
                        }
                    } else {
                        if($.browser.mozilla == true){
                            $('#absence-scroll').find('.height_' + id).css("height", j);
                        }else{
                            $('#absence-scroll').find('.height_' + id).css("height", j+1);
                        }
                    }
                });
            }
            $(window).resize(function(e) {
                fixedHeightScreen();
                configSizeScroll();
            });
            fixedHeightScreen();
            configSizeScroll();
        });
        // tooltip validated
        var temp=setInterval(function(){
            $('.ch-absen-validation').focus(function(){
                $(this).tooltip('option' , 'content' , '<?php echo __('Timesheet validated',true);?>');
                $(this).tooltip('enable');
            }).mouseup(function(){
                $(this).tooltip('close');
            }).blur(function(){
                $(this).tooltip('option' , 'content' , '<?php echo __('Timesheet validated',true);?>');
                $(this).tooltip('enable');
            }).tooltip({maxWidth : 1000, maxHeight : 300,content: function(target){
                    return '<?php echo __('Timesheet validated',true);?>';
                }});
            clearInterval(temp);
        },1000);
    })(jQuery);
    <?php
    $month = date('m', $avg);
    $week = date('W', $avg);
    $year = date('Y', $avg);
    $profit = !empty($this->params['url']['profit']) ? $this->params['url']['profit'] : '';
    ?>

    var $month = <?php echo json_encode($month);?>,
        $week = <?php echo json_encode($week);?>,
        $year = <?php echo json_encode($year);?>,
        $profit = <?php echo json_encode($profit);?>;
    $('#typeRequest').change(function () {
        var linkRequest = '<?php echo $this->Html->url('/') ?>absence_requests/manage/';
        if($(this).val() == 'week'){ // change month to week
            linkRequest += 'week';
        } else if($(this).val() == 'month'){ // change week to month
            linkRequest += 'month';
        } else { // change to year
            $month = 1;
            linkRequest += 'year';
        }
        var refreshLink = '';
        refreshLink = linkRequest + '?year=' + $year + '&month=' + $month + '&profit=' + $profit;
        window.location.href = refreshLink;
    });
    var original = $('.header-height').height();
    function configSizeScroll(hd){
        $("#scrollTopAbsenceContent").width($("#absence").width());
        $("#scrollTopAbsence").width($("#absence-scroll").width());
        $("#scrollLeftAbsenceContent").height($("#absence-table").height());
        var hHead=original*2+4;
        $("#scrollLeftAbsence").css({'marginTop':(hHead)+'px'});
        $(".no.checkbox").width($("#thColID").width()+7);
        $(".st").width($("#thColEmployee").width()+7);
        $(".ct").width($("#thColCapacity").width()+7);
        var j = 0 ;

        $(".colThAm").each(function(index, element) {
            j++;
            $(".am"+j).width($("#colThAm"+j).width()+gw+2);
            $(".pm"+j).width($("#colThPm"+j).width()+gw);
        });
        if(hd!=800)
        {
            hd=hd-hHead-25;
        }
        $('.tbl-tbody').height(hd);
        $("#scrollLeftAbsence").height(hd);
    }
    setTimeout(function(){
        $(window).resize();
        configSizeScroll(800);
    }, 100);
    var allowScrollWindow = true;
    var abc = 0;
    //$(function () {
        $(document).on('onmousewheel wheel onmousewheel mousewheel DOMMouseScroll', function(event, delta) {
            if(event.originalEvent.wheelDelta)
            {
                delta = event.originalEvent.wheelDelta;
            }
            else
            {
                delta = event.originalEvent.deltaY * -1;
            }
            if(allowScrollWindow == false)
            {
                if(delta < 0)
                {
                    abc = abc == $("#absence-table").height() ? $("#absence-table").height() : abc + 120;
                }
                else
                {
                    abc = abc == 0 ? abc : abc - 120;
                }
                //$("#scrollLeftAbsence").scrollTop(abc);
                $('#scrollLeftAbsence').animate({scrollTop:abc},'fast');
                //$(".tbl-tbody").scrollTop(abc);
                //$("#absence-table-fixed").scrollTop(abc);
                return false;
            }
        });
        $("#scrollTopAbsence").scroll(function () {
            //$('.separator-week').parent().addClass('separator-week-div');
            //$('.disable-edit-day').parent().addClass('disable-edit-day-div');
            //$(".slick-viewport-right").scrollLeft($("#absence-scroll").scrollLeft());
            $("#absence-scroll").scrollLeft($("#scrollTopAbsence").scrollLeft());
        });
        $("#absence-scroll").scroll(function () {
            //$('.separator-week').parent().addClass('separator-week-div');
            //$('.disable-edit-day').parent().addClass('disable-edit-day-div');
            //$(".slick-viewport-right").scrollLeft($("#absence-scroll").scrollLeft());
            $("#scrollTopAbsence").scrollLeft($("#absence-scroll").scrollLeft());
        });
        $("#scrollLeftAbsence").scroll(function (e) {
            //abc = $('#scrollLeftAbsence').scrollTop();
            $(".tbl-tbody").scrollTop($('#scrollLeftAbsence').scrollTop());
            if(allowScrollWindow == true)
            abc = $('#scrollLeftAbsence').scrollTop();
            $("#absence-table-fixed").scrollTop($('#scrollLeftAbsence').scrollTop());
        });
    //});
    $("#absence-scroll").mouseover(function(e) {
        allowScrollWindow = false;
       // $('html').css({"overflow":"hidden"});
    });
    $("#absence-scroll").mouseout(function(e) {
        allowScrollWindow = true;
        //$('html').css({"overflow":"auto"});
    });
    $("#absence-fixed").mouseover(function(e) {
        allowScrollWindow = false;
       // $('html').css({"overflow":"hidden"});
    });
    $("#absence-fixed").mouseout(function(e) {
        allowScrollWindow = true;
        //$('html').css({"overflow":"auto"});
    });
    //EXPAND TREE
    $(document).keyup(function(e) {
        if (window.event)
        {
            var value = window.event.keyCode;
        }
        else
            var value=e.which;
        if (value == 27) { collapseScreen(); }
    });
    function collapseScreen()
    {
        configSizeScroll(800);
        $('#table-control').show();
        $('.wd-title').show();
        $('#collapse').hide();
        $('#project_container').removeClass('fullScreen');
        $(window).resize();
    }
    function expandScreen()
    {
        var wh=$(window).height();
        configSizeScroll(wh);
        $('#table-control').hide();
        $('.wd-title').hide();
        $('#project_container').addClass('fullScreen');
        $('#collapse').show();
        $(window).resize();
    }
    var step = 25;
var scrolling = false;

</script>
<div id="collapse" onclick="collapseScreen();" ><?php echo $this->Html->image("ui/icon-esc.png"); ?></div>
