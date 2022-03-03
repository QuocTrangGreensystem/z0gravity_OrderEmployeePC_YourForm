<?php
echo $this->Html->css(array(
    'slick_grid/slick.grid.activity',
    'jquery.multiSelect',
    'projects',
    'slick_grid/slick.grid_v2',
    'slick_grid/slick.pager',
    'slick_grid/slick.common_v2',
    'slick_grid/slick.edit',
	'preview/project_decisions'
));
echo $this->Html->script(array(
    'jquery.multiSelect',
    'responsive_table',
    'slick_grid/lib/jquery-ui-1.8.16.custom.min',
    'slick_grid/lib/jquery.event.drop-2.0.min',
    'slick_grid/lib/jquery.event.drag-2.2',
    'slick_grid/slick.core',
    'slick_grid/slick.dataview',
    'slick_grid/controls/slick.pager',
    'slick_grid/slick.formatters',
    'slick_grid/plugins/slick.cellrangedecorator',
    'slick_grid/plugins/slick.cellrangeselector',
    'slick_grid/plugins/slick.cellselectionmodel',
    'slick_grid/plugins/slick.rowselectionmodel',
    'slick_grid/plugins/slick.rowmovemanager',
    'slick_grid/slick.editors',
    'slick_grid/slick.grid',
    'slick_grid_custom',
    'slick_grid/slick.grid.activity',
    'jquery.ui.touch-punch.min',
    'history_filter'
));
echo $this->element('dialog_projects');
?>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
    .row-number{
        float: right;
    }
    .row-center-custom{
        text-align: center;
    }
    .row-date{
        text-align: center;
    }
    .milestone-mi{
        background-image: url("/img/mi.png");
        background-repeat: no-repeat;
        display: block;
        cursor: pointer;
        width: 25px;
        float: left;
        margin-top: 5px;
    }
    .milestone-green{
        background-image: url("/img/mi-green.png");
        background-repeat: no-repeat;
        display: block;
        cursor: pointer;
        width: 25px;
        float: left;
        margin-top: 10px;
    }
    .milestone-blue{
        background-image: url("/img/mi-blue.png");
        background-repeat: no-repeat;
        display: block;
        cursor: pointer;
        width: 25px;
        float: left;
        margin-top: 5px;
    }
    .milestone-orange{
        background-image: url("/img/mi-orange.png");
        background-repeat: no-repeat;
        display: block;
        cursor: pointer;
        width: 25px;
        float: left;
        margin-top: 5px;
    }
    .muti_text{
        background-image: url("/img/extjs/icon-text.png");
        background-repeat: no-repeat;
        display: block;
        cursor: pointer;
        width: 25px;
        margin-top: 5px;
        margin-left: 15px;
    }
    .download-attachment{
        background-image: url("/img/extjs/icon-task-folder.png");
    }
    #comment-ct{
        max-height: 500px;
        overflow-x: hidden;
        overflow-y: auto;
    }
    .comment-ct{
        min-height: 50px;
    }
    .comment-btn{
        float: left;
        margin-left: 45px;
    }
    .comment-btn textarea{
        width: 300px;
        margin-left: -4px;
        margin-top: 20px;
        padding: 8px;
        line-height:1.5;
        font:13px Tahoma, cursive;
        max-height: 400px;
        overflow-x: hidden;
        overflow-y: auto;
    }
    .submit-btn-msg{
        width: 70px;
        height: 50px;
        border: none;
        vertical-align: top;
        color: #fff;
        text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.3);
        position: relative;
        margin-top: 20px;
        font-size: 20px;
        background-color: #56aaff !important;
    }
    .submit-btn-msg img{
        height: 40px;
    }
    .submit-btn-msg:hover{
        cursor: pointer;
    }
    .comment{
        min-height: 70px;
        max-width: 320px;
        background-color: #549ac1;
        border-bottom: 5px solid #fff;
        width: auto;
        float: left;
        padding-right: 30px;
        border-radius: 10px;
        margin-right: 40px;
    }
    .left-comment{
        width: 50px;
        float: left;
        clear: both;
        overflow: hidden;
    }
    .right-comment{
        padding-top: 5px;
        max-width: 684px;
        margin-left: 10px;
        padding-bottom: 5px;
    }
    .right-comment b{
        color: #578cca;
    }
    .right-comment span {
        /*margin-left: 10px;*/
    }
    .my-comment{
        background-color: #7ab3d1;
        color: #fff;
        min-height: 50px;
        border-bottom: 5px solid #fff;
        margin-right: 50px;
        width: auto;
        clear: both;
        float: right;
        padding-left: 30px;
        border-radius: 10px;
        margin-left: 40px;
        padding: 5px;
    }
    .my-date{
        float: right;
        margin-right: 30px;
        display: block;
        color: #000;
    }
    .my-content{
        float: right;
        clear: both;
        margin-right: 30px;
    }
    .right-avatar{
        float: right;
        margin-top: -70px;
        clear: both;
    }
    .right-avatar img{
        height: 40px;
        width: 40px;
        border-radius: 30px;
        margin-right: 10px;
        margin-top: 5px;
    }
    .avatar{
        border-radius: 50%;
        width: 40px;
        height: 40px;
    }
    .show-hide img{
        height: 30px;
    }
    .show-hide img:hover{
        cursor: pointer;
    }
    .hidden-div{
        display: none;
    }
    .message-new-ct{
        float: right;
        background-color: red;
        color: #fff;
        border-radius: 50%;
        width: 20px;
        text-align: center;
        display: block;
        height: 20px;
        margin-top: 5px;
        margin-right: 5px;
        line-height: 20px;
    }
    .delete-attachment, .download-attachment{
        margin: 0;
    }
    .muti-color{
        width: 100px !important;
        height: auto !important;
    }
    body{
        overflow: hidden;
    }
    #wd-container-footer{
        display: none;
    }
    .slick-viewport-left{
        overflow-x: hidden !important;
        overflow-y: auto;
    }
    .slick-viewport-right{
        overflow: hidden !important;
    }
</style>
<div id="attachment-template" style="display: none;">
    <div style="overflow: hidden;" class="img-to-right">
        <a class="download-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '?' => array('type' => 'download'))); ?>"><?php echo __('Download', true); ?></a>
        &nbsp; <a class="delete-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '?' => array('type' => 'delete'))); ?>" rel="%2$s"><?php echo __('Delete', true); ?></a>
    </div>
</div>
<div id="not-attachment-template" style="display: none;">
    <div style="overflow: hidden;" class="img-to-right">
        <i style="margin-left: -2px" class="download-attachment" href=""><?php echo __('Download', true); ?></i>
    </div>
</div>
<div id="weather-template" style="display: none;">
    <div style="overflow: hidden;">
        <div class="wd-input wd-weather-list-dd">
            <ul style="float: left; display: inline; overflow: hidden">
                <li><input style="margin-top: 8px" value="sun" name="weather-%1$s" data-id="%1$s" %2$s class="weather weather-sun" type="radio"> <img style="float: none" title="Sun" src="<?php echo $this->Html->url('/') ?>img/sun.png"></li>
                <li><input type="radio" value="cloud" name="weather-%1$s" class="weather weather-cloud" %3$s style="margin-top: 8px;" data-id="%1$s"> <img style="float: none" title="Cloud" src="<?php echo $this->Html->url('/') ?>img/cloud.png"></li>
                <li><input type="radio" value="rain" name="weather-%1$s" class="weather weather-rain" %4$s style="margin-top: 8px;" data-id="%1$s"> <img style="float: none" title="Rain" src="<?php echo $this->Html->url('/') ?>img/rain.png"></li>
            </ul>
        </div>
    </div>
</div>
<div id="commentsPopup" class="buttons" style="display: none;">
    <div class="modal-body">
    </div>
    <div class="comment-btn">
        <button onclick="saveCommentPj()" class="submit-btn-msg" type="button">
            <img src="<?php echo $this->Html->url('/img/ui/blank-plus.png');?>" alt="">
        </button>
        <textarea class="textarea-ct" name="name" rows="2" cols="40"></textarea>
    </div>
</div>
<!-- dialog_attachement_or_url -->
<div id="dialog_attachement_or_url" class="buttons" style="display: none;">
    <fieldset>
        <?php
        echo $this->Form->create('Upload', array(
                'type' => 'file', 'id' => 'form_dialog_attachement_or_url',
                'url' => array('controller' => 'project_expectations', 'action' => 'upload')
            )); ?>
        <div style="height:auto;" class="wd-scroll-form">
            <div class="wd-input">
                <label for="attachement"><?php __("Attachement") ?></label>
                <p id="gs-attach"></p>
                <?php
                echo $this->Form->hidden('id', array('id' => false, 'rel' => 'no-history', 'value' => ''));
                echo $this->Form->hidden('project_id', array('value' => $project_id, 'rel' => 'no-history'));
                ?>
                <?php
                echo $this->Form->input('attachment', array('type' => 'file', 'value' => '',
                    'name' => 'FileField[attachment]',
                    'label' => false,
                    'class' => 'update_attach_class',
                    'rel' => 'no-history'));
                ?>
            </div>
        </div>
        <?php
        echo $this->Form->end();
        ?>
    </fieldset>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_attach"><?php __('OK') ?></a></li>
    </ul>
</div>
<!-- dialog_attachement_or_url.end -->

<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>

            <div class="wd-tab"> <div class="wd-panel">
            <div class="wd-list-project" style="height: 750px">
                <div class="wd-title">
                    <a href="javascript:void(0);" class="btn btn-plus-green" id="add-new-sales" style="margin-right:5px;" onclick="addNewAcceptanceButton();" title="<?php __('Add an item') ?>"></a>
                    <a href="javascript:void(0);" class="btn btn-reset-filter hidden" id="reset-filter" onclick="resetFilter();" style="margin-right:5px;" title="<?php __('Reset filter') ?>"></a>
                </div>
                <div id="scrollTopAbsence"><div id="scrollTopAbsenceContent"></div></div>
                <br clear="all"  />
                <div class="wd-table" id="project_container" style="width:100%;height:700px;"></div>
                <div id="pager" style="width:100%;height:0px; overflow: hidden;"></div>
            </div>
            </div></div>
        </div>
    </div>
</div>
<?php
$viewLong = false;
function jsonParseOptions($options, $safeKeys = array()) {
    $output = array();
    $safeKeys = array_flip($safeKeys);
    foreach ($options as $option) {
        $out = array();
        foreach ($option as $key => $value) {
            if (!is_int($value) && !isset($safeKeys[$key])) {
                $value = json_encode($value);
            }
            $out[] = $key . ':' . $value;
        }
        $output[] = implode(', ', $out);
    }
    return '[{' . implode('},{ ', $output) . '}]';
}
$columns = array(
    array(
        'id' => 'no.',
        'field' => 'no.',
        'name' => '#',
        'width' => 40,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
    )
);

foreach ($fieldsets as $key => $value) {
    $dx = $value['Expectation'];
    $name = !empty($value['Translation'][$fields]) ? $value['Translation'][$fields] : $dx['original_text'];
    if( strpos($dx['field'], 'ist_color_') == 1){
       $columns[] = array(
           'id' => $dx['field'],
           'field' => $dx['field'],
           'name' => $name,
           'width' => 70,
           'sortable' => false,
           'resizable' => false,
           'noFilter' => 1,
           'editor' => 'Slick.Editors.selectBachground',
           'formatter' => 'Slick.Formatters.selectBachground'
       );
    } else if( strpos($dx['field'], 'ist_') == 1){
        if( !empty($datasets[$dx['field']]) ){
            $columns[] = array(
                'id' => $dx['field'],
                'field' => $dx['field'],
                'name' => $name,
                'width' => 150,
                'sortable' => false,
                'resizable' => true,
                'editor' => 'Slick.Editors.selectBox'
            );
        }
    } else if( strpos($dx['field'], 'ate_') == 1 ){
        $columns[] = array(
            'id' => $dx['field'],
            'field' => $dx['field'],
            'name' => $name,
            'width' => 110,
            'sortable' => true,
            'resizable' => true,
            'editor' => 'Slick.Editors.datePicker',
            'datatype' => 'datetime'
        );
    } else if( strpos($dx['field'], 'ext_long_') == 1 ){
        $viewLong = true;
        $columns[] = array(
            'id' => $dx['field'],
            'field' => $dx['field'],
            'name' => $name,
            'width' => 250,
            'sortable' => true,
            'resizable' => true,
            'editor' => 'Slick.Editors.textArea',
            'formatter' => 'Slick.Formatters.longText'
        );
    } else if( strpos($dx['field'], 'ext_short_') == 1 ){
        $columns[] = array(
            'id' => $dx['field'],
            'field' => $dx['field'],
            'name' => $name,
            'width' => 200,
            'sortable' => true,
            'resizable' => true,
            'editor' => 'Slick.Editors.textBox'
        );
    } else if( strpos($dx['field'], 'ssigned_to_') == 1 ){
        $columns[] = array(
            'id' => $dx['field'],
            'field' => $dx['field'],
            'name' => $name,
            'width' => 200,
            'sortable' => true,
            'resizable' => true,
            'editor' => 'Slick.Editors.mselectBox'
        );
    } else if( $dx['field'] == 'milestone' ){
        $columns[] = array(
            'id' => $dx['field'],
            'field' => $dx['field'],
            'name' => $name,
            'width' => 160,
            'sortable' => true,
            'resizable' => true,
            'editor' => 'Slick.Editors.selectBox',
            'formatter' => 'Slick.Formatters.iconColor'
        );
    } else if( $dx['field'] == 'attached_documents' ){
        $columns[] = array(
            'id' => $dx['field'],
            'field' => $dx['field'],
            'name' => $name,
            'width' => 100,
            'sortable' => true,
            'resizable' => true,
            'noFilter' => 1,
            'editor' => 'Slick.Editors.Attachement',
            'formatter' => 'Slick.Formatters.Attachement'
        );
    } else if( $dx['field'] == 'text' ){
        $columns[] = array(
            'id' => $dx['field'],
            'field' => $dx['field'],
            'name' => $name,
            'width' => 60,
            'sortable' => true,
            'resizable' => true,
            'noFilter' => 1,
            // 'editor' => 'Slick.Editors.Attachement',
            'formatter' => 'Slick.Formatters.MutiText'
        );
    } else {
        if(strpos($dx['field'], 'lert_') == 1){
            $name = $nameAlert[$dx['field']];
        }
        $columns[] = array(
            'id' => $dx['field'],
            'field' => $dx['field'],
            'name' => $name,
            'width' => 160,
            'sortable' => true,
            'resizable' => true,
        );
    }
}
$columns[] = array(
    'id' => 'action.',
    'field' => 'action.',
    'name' => __(' ', true),
    'width' => 70,
    'sortable' => false,
    'resizable' => false,
    'noFilter' => 1,
    'formatter' => 'Slick.Formatters.Action'
);
App::import('Vendor', 'str_utility');
$selectMaps = array(
    'milestone' => $milestone,
    'assigned_to_1' => $listEmployeeAndProfit,
    'assigned_to_2' => $listEmployeeAndProfit,
    'assigned_to_3' => $listEmployeeAndProfit,
);
foreach ($datasets as $key => $value) {
    $selectMaps[$key] = $value;
}
$dataView = array();
$i = 1;
foreach ($project_expectations as $project_expectation) {
    $project_expectation = $project_expectation['ProjectExpectation'];
    $data = array(
        'id' => $project_expectation['id'],
        'company_id' => $company_id,
        'no.' => $i++,
        'MetaData' => array()
    );
    foreach ($fieldsets as $value) {
        $dx = $value['Expectation'];
        if(strpos($dx['field'], 'ate_') == 1 ){
            $data[$dx['field']] = $project_expectation[$dx['field']] ? str_utility::convertToVNDate($project_expectation[$dx['field']]) : '';
        } else if($dx['field'] == 'milestone_date') {
            $data[$dx['field']] = !empty($project_expectation['milestone']) && !empty($milestone_date[$project_expectation['milestone']]) ? str_utility::convertToVNDate($milestone_date[$project_expectation['milestone']]) : '';
        } else if(strpos($dx['field'], 'lert_') == 1 ){
            if(!empty($project_expectation['milestone']) && !empty($milestone_date[$project_expectation['milestone']])){
                $date = str_utility::convertToVNDate($milestone_date[$project_expectation['milestone']]);
                $num = $dateAlert[$dx['field']];
                $t = '-' . $num . ' day';
                $data[$dx['field']] = date('d-m-Y', strtotime($t, strtotime($date)));
            } else {
                $data[$dx['field']] = '';
            }
        } else if(strpos($dx['field'], 'ssigned_to_') == 1 ){
            $data[$dx['field']] = array();
            if(!empty($_expectationEmployeeRefer[$dx['field']][$project_expectation['id']])){
                $v = $_expectationEmployeeRefer[$dx['field']][$project_expectation['id']];
                foreach ($v as $key => $value) {
                    $data[$dx['field']][$key] = $value['is_profit_center'] . '-' . $value['reference_id'];
                }
            }
        } else {
            $data[$dx['field']] = $project_expectation[$dx['field']];
        }
    }
    $data['project_id'] = $project_id;
    $dataView[] = $data;
}
$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true),
    'Date closing must between %1$s and %2$s' => __('Date closing must between %1$s and %2$s', true),
    'Delete?' => __('Delete?', true)
);
$listAvartar = array();
$listIdEm = array_keys($listEmployee);
foreach ($listIdEm as $_id) {
    $_id = str_replace('0-', '', $_id);
    $link = $this->UserFile->avatar($_id, "small");
    $listAvartar[$_id] = $link;
}
if($viewLong){
?>
<?php
}
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Are you sure you want to delete this acceptance?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<script type="text/javascript">
var wdTable = $('.wd-table');
var heightTable = $(window).height() - wdTable.offset().top - 40;
// heightTable = (heightTable < 550) ? 550 : heightTable;
wdTable.css({
    height: heightTable,
});
$(window).resize(function(){
    heightTable = $(window).height() - wdTable.offset().top - 40;
    // heightTable = (heightTable < 550) ? 550 : heightTable;
    wdTable.css({
        height: heightTable,
    });
});
var DateValidate = {},ControlGrid,IuploadComplete = function(json){
    var data = ControlGrid.eval('currentEditor');
    data.onComplete(json);
};
var listAvartar = <?php echo json_encode($listAvartar) ?>;
var employee_id = <?php echo json_encode($employee_id); ?>;
var projectName = <?php echo json_encode($projectName['Project']); ?>;
var nameAlert = <?php echo json_encode($nameAlert); ?>;
var dateAlert = <?php echo json_encode($dateAlert); ?>;
var milestone_date = <?php echo json_encode($milestone_date); ?>;
var listColor = <?php echo json_encode($_listColor) ?>;
var colorDefault = <?php echo json_encode($colorDefault) ?>;
var viewLong = <?php echo json_encode($viewLong) ?>;
function number_format (number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
};

(function($){
    $(function(){
        var $this = SlickGridCustom;
        DateValidate.isUnique = function(value,args){
            var result = true,_value = $.trim(value).toLowerCase();
            var items = args.grid.getData().getItems();
            for(var i in items){
                if( items[i].project_acceptance_type_id == _value ){
                    result = false;
                    break;
                }
            }
            return {
                valid : result,
                message : $this.t('This item already existed!')
            };
        };

        $('a.delete-attachment').live('click' , function(){
            var row = $(this).attr('rel'),
            data = ControlGrid.getDataItem(row);
            if(data && confirm($this.t('Delete?')) ){
                data && $.ajax({
                    cache : false,
                    type : 'GET',
                    url : $(this).attr('href')
                });
                data['file'] = '';
                ControlGrid.updateRow(row);
            }
            return false;
        });

        $this.i18n = <?php echo json_encode($i18n); ?>;
        $this.canModified =  <?php echo json_encode((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite)); ?>;
        $this.selectMaps = <?php echo json_encode($selectMaps) ?>;
        // For validate date
        var getTime = function(value){
            value = value.split("-");
            return (new Date(parseInt(value[2] ,10), parseInt(value[1], 10), parseInt(value[0], 10))).getTime();
        }

        var actionTemplate =  $('#action-template').html(),
            attachmentTemplate =  $('#attachment-template').html(),
            notAttachmentTemplate =  $('#not-attachment-template').html(),
            weatherTemplate =  $('#weather-template').html();
        var milestone_date = <?php echo json_encode($milestone_date) ?>;
        var milestone = <?php echo json_encode($milestone) ?>;
        var validated = <?php echo json_encode($validated) ?>;

        $.extend(Slick.Formatters,{
            Attachement : function(row, cell, value, columnDef, dataContext){
                if(value){
                    value = $this.t(attachmentTemplate,dataContext.id,row);
                } else {
                    value = $this.t(notAttachmentTemplate,dataContext.id,row);
                }
                return Slick.Formatters.HTMLData(row, cell, value, columnDef, dataContext);
            },
            Action : function(row, cell, value, columnDef, dataContext){
                return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,
                dataContext.project_id,dataContext.project_issue_problem), columnDef, dataContext);
            },
            iconColor : function(row, cell, value, columnDef, dataContext){
                var rightnow = new Date();
                if(value !== undefined && value != ''){
                    var dateConvert = milestone_date[value].split("-");
                    var dateCheck = new Date(dateConvert[0]+'-'+dateConvert[1]+'-'+dateConvert[2]);
                    if(validated[value] == 1){
                        return '<i class="milestone-green">&nbsp</i><span>' + milestone[value] + '</span>';
                    } else {
                        if (dateCheck < rightnow) {
                            return '<i class="milestone-mi">&nbsp</i><span>' + milestone[value] + '</span>';
                        } else if(dateCheck > rightnow) {
                            return '<i class="milestone-blue">&nbsp</i><span>' + milestone[value] + '</span>';
                        } else {
                            return '<i class="milestone-orange">&nbsp</i><span>' + milestone[value] + '</span>';
                        }
                    }
                }
                return '';
            },
            longText : function(row, cell, value, columnDef, dataContext){
                var textArea = value.replace(/\n/g, "<br>");
                return "<div style='line-height: 40px'><span>" + textArea + '</span></div>';
            },
            MutiText : function(row, cell, value, columnDef, dataContext){
                return '<div style="text-align: center"><i class="muti_text" onclick="callMutitext(' + dataContext.id + ')">&nbsp</i></div>';
            },
            selectBachground : function(row, cell, value, columnDef, dataContext){
                if(value != 0 && value != null){
                    return '<div style="width: 20px; height: 20px; background-color: ' + listColor[columnDef.id][value] + '; padding-left: 0px; margin:0 auto;margin-top : 4px;"></div>';
                }
                return '<div style="width: 20px; height: 20px; background-color: ' + colorDefault[columnDef.id] + '; padding-left: 0px; margin:0 auto;margin-top : 4px;"></div>';
            }
        });

        $.extend(Slick.Editors,{
            Attachement : function(args){
                var self = this;
                $.extend(this, new BaseSlickEditor(args));
                var createDialog = function(){
                    $('#dialog_attachement_or_url').dialog({
                        position    :'center',
                        autoOpen    : false,
                        autoHeight  : true,
                        modal       : true,
                        width       : 500,
                        open : function(e){
                            var $dialog = $(e.target);
                            $dialog.dialog({open: $.noop});
                        }
                    });
                    createDialog = $.noop;
                }

                createDialog();
                var titlePopup = <?php echo json_encode(__('Attachement or URL', true))?>;
                $("#dialog_attachement_or_url").dialog('option',{title:titlePopup}).dialog('open');

                $(".cancel").live('click',function(){
                    $("#dialog_attachement_or_url").dialog('close');
                });
                this.input = $("<div style='overflow: hidden;' class='img-to-right'><i style='margin-left: -2px' class='download-attachment' href=''><?php echo __('Download', true); ?></i></div>")
                .appendTo(args.container);
                // .attr('rel','no-history').addClass('editor-text');
                $("#ok_attach").click(function(){
                    //self.input[0].remove();
                    $('#action-attach-url').css('display', 'none');
                    $('.browse').css('display', 'block');
                    $("#dialog_attachement_or_url").dialog('close');

                    var form = $("#form_dialog_attachement_or_url");
                    form.find('input[name="data[Upload][id]"]').val(args.item.id);
                    form.submit();
                });
                this.focus();
            },
            selectBachground : function(args){
                var $options,hasChange = false,isCreated = false;
                var defaultValue = [];
                var scope = this;
                var col_id = args.column.id;
                var oldColor_id = args.item[col_id];
                setColor = function(color_id){
                    var rowId = args.item.id;
                    var cell = args.container[0];
                    // $('.multiSelectOptions').hide();
                    $(cell).find('div').css('background-color', listColor[col_id][color_id]);
                    args.item[col_id] = color_id;
                    Slick.Formatters.selectBachground(null,null,color_id, args.column ,args.item);
                    if(oldColor_id != color_id){
                        hasChange = true;
                    } else {
                        hasChange = false;
                    }
                }
                var preload = function(){
                    var _html = '';
                    _html += '<form>';
                    $.each(listColor[col_id], function(ind, val){
                        if(ind == oldColor_id){
                            _html += '<input type="radio" onClick="setColor(' + ind + ')" name="color" checked style="float: left" rel="no-history"/><div style="margin-left: 20px; height: 22px; background-color: '+val+'" >&nbsp;</div>';
                        } else {
                        _html += '<input type="radio" onClick="setColor(' + ind + ')" name="color" style="float: left" rel="no-history"/><div style="margin-left: 20px; height: 22px; background-color: '+val+'" >&nbsp;</div>';
                        }
                    });
                    _html += '</form>'
                    $options.html(_html);
                    scope.input.html('');
                    scope.setValue(defaultValue);
                    scope.input.select();
                }
                $.extend(this, new BaseSlickEditor(args));

                $options = $('<div class="multiSelectOptions muti-color" style="position: absolute; z-index: 99999; visibility: hidden;max-height:150px; width:100px !important; height:auto !important"></div>').appendTo('body');
                var hideOption = function(){
                    scope.input.removeClass('active').removeClass('hover');
                    $options.css({
                        visibility:'hidden',
                        display : 'none'
                    });
                }
                this.input = $('<a href="javascript:void(0);" class="multiSelect"></a>')
                .appendTo(args.container)
                .hover( function() {
                    scope.input.addClass('hover');
                }, function() {
                    scope.input.removeClass('hover');
                })
                .click( function(e) {
                    // Show/hide on click
                    if(scope.input.hasClass('active')) {
                        hideOption();
                    } else {
                        var offset = scope.input.addClass('active').offset();
                        $options.css({
                            top:  offset.top + scope.input.outerHeight() + 'px',
                            left: offset.left + 'px',
                            visibility:'visible',
                            display : 'block'
                        });
                        $options.find('input[type=text]').focus().select();
                        if(scope.input.width() < 320){
                            $options.width(100);
                        }
                    }
                    e.stopPropagation();
                    return false;
                });

                $(document).click( function(event) {
                    if(!($(event.target).parents().andSelf().is('.multiSelectOptions'))){
                        hideOption();
                    }
                });

                var destroy = this.destroy;
                this.destroy = function () {
                    $options.remove();
                    destroy.call(this, $.makeArray(arguments));
                };

                this.getValue = function (val) {
                    if(this.input.html() == 'Loading ...'){
                        if(val ==true){
                            return true;
                        }
                        return '';
                    }
                    return this.input.html().split(',');
                };

                this.setValue = function (val) {
                    if(val === true){
                        val = 'Loading ...';
                    }else{
                        val = Slick.Formatters.selectBachground(null,null,val, args.column ,args.item);
                    }
                    this.input.html(val);
                };

                this.loadValue = function (item) {
                    defaultValue = item[args.column.field] || "";
                };

                this.serializeValue = function () {
                    if(!isCreated){
                        this.loadValue(args.item);
                        preload();
                    }
                    return scope.getValue();
                };

                var applyValue = this.applyValue;
                this.applyValue = function (item, state) {
                    if($.isEmptyObject(item)){
                        applyValue.call(this, item , state);
                    }
                    $.extend(item ,args.item , true);
                };

                this.isValueChanged = function () {
                    return (hasChange == true);
                };
                this.focus();
            }
       });

        var  data = <?php echo json_encode($dataView); ?>;
        var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
        $this.fields = {
            id : {defaulValue : 0},
            milestone: {defaulValue : '', allowEmpty : false},
            list_1 : {defaulValue : ''},
            list_2 : {defaulValue : ''},
            list_3 : {defaulValue : ''},
            list_4 : {defaulValue : ''},
            list_5 : {defaulValue : ''},
            list_6 : {defaulValue : ''},
            list_7 : {defaulValue : ''},
            list_8 : {defaulValue : ''},
            list_9 : {defaulValue : ''},
            list_10 : {defaulValue : ''},
            list_11 : {defaulValue : ''},
            list_12 : {defaulValue : ''},
            list_13 : {defaulValue : ''},
            list_14 : {defaulValue : ''},
            list_15 : {defaulValue : ''},
            list_16 : {defaulValue : ''},
            list_17 : {defaulValue : ''},
            list_18 : {defaulValue : ''},
            list_19 : {defaulValue : ''},
            list_20 : {defaulValue : ''},
            list_21 : {defaulValue : ''},
            list_22 : {defaulValue : ''},
            list_23 : {defaulValue : ''},
            list_24 : {defaulValue : ''},
            list_25 : {defaulValue : ''},
            list_26 : {defaulValue : ''},
            list_27 : {defaulValue : ''},
            list_28 : {defaulValue : ''},
            list_29 : {defaulValue : ''},
            list_30 : {defaulValue : ''},
            date_1 : {defaulValue : ''},
            date_2 : {defaulValue : ''},
            date_3 : {defaulValue : ''},
            date_4 : {defaulValue : ''},
            date_5 : {defaulValue : ''},
            date_6 : {defaulValue : ''},
            date_7 : {defaulValue : ''},
            date_8 : {defaulValue : ''},
            date_9 : {defaulValue : ''},
            date_10 : {defaulValue : ''},
            text_long_1 : {defaulValue : ''},
            text_long_2 : {defaulValue : ''},
            text_long_3 : {defaulValue : ''},
            text_long_4 : {defaulValue : ''},
            text_long_5 : {defaulValue : ''},
            text_short_1 : {defaulValue : ''},
            text_short_2 : {defaulValue : ''},
            text_short_3 : {defaulValue : ''},
            text_short_4 : {defaulValue : ''},
            text_short_5 : {defaulValue : ''},
            text_short_6 : {defaulValue : ''},
            text_short_7 : {defaulValue : ''},
            text_short_8 : {defaulValue : ''},
            text_short_9 : {defaulValue : ''},
            text_short_10 : {defaulValue : ''},
            assigned_to_1 : {defaulValue : []},
            assigned_to_2 : {defaulValue : []},
            assigned_to_3 : {defaulValue : []},
            list_color_1 : {defaulValue : ''},
            list_color_2 : {defaulValue : ''},
            list_color_3 : {defaulValue : ''},
            list_color_4 : {defaulValue : ''},
            list_color_5 : {defaulValue : ''},
            project_id: {defaulValue : <?php echo $project_id ?>},
        };
        $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
        var options = {
            rowHeight:  viewLong ? 60 : 33,
        };
        ControlGrid = $this.init($('#project_container'),data,columns, {
				// frozenColumn: 1,
				rowHeight: 40,
				headerRowHeight: 40, 
			});
        $this.onBeforeEdit = function(args){
            if(args.column.field == 'attached_documents' && (!args.item || args.item['attached_documents'] || !args.item['id'])){
                return false;
            }
            return true;
        }
        $this.onCellChange = function(args){
            if(args.item && args.item.milestone != ''){
                var columnId = args.column.id;
                columnId = columnId.substring(0, 3);
                var mileston_id = args.item.milestone;
                var d = milestone_date[mileston_id];
                var _d = d.split('-');
                var date = _d[2] + '-' + _d[1] + '-' + _d[0];
                args.item['milestone_date'] = date;
                $.each(dateAlert, function(_id, val){
                    var _date;
                    _date = diffDate(date, val);
                    args.item[_id] = _date;
                });
            }
            $('.row-center').parent().addClass('row-center-custom');
            var columns = args.grid.getColumns(),
                col, cell = args.cell;
            do {
                cell++;
                if( columns.length == cell )break;
                col = columns[cell];
            } while (typeof col.editor == 'undefined');

            if( cell < columns.length ){
                args.grid.gotoCell(args.row, cell, true);
            } else {
                //end of row
                try {
                    args.grid.gotoCell(args.row + 1, 0);
                } catch(ex) {}
            }
        }
        // add new colum grid
        addNewAcceptanceButton = function(){
            ControlGrid.gotoCell(data.length, 1, true);
        }
        $('.row-center').parent().addClass('row-center-custom');
        $(ControlGrid.getHeaderRow()).delegate(":input", "change keyup", function (e) {
            var text = $(this).val();
            if( text != '' ){
                $(this).parent().css('border', 'solid 2px orange');
            } else {
                $(this).parent().css('border', 'none');
            }
        });
    });

    /* table .end */
    var createDialog = function(){
        $('#dialog_attachement_or_url').dialog({
            position    :'center',
            autoOpen    : false,
            autoHeight  : true,
            modal       : true,
            width       : 500,
            open : function(e){
                var $dialog = $(e.target);
                $dialog.dialog({open: $.noop});
            }
        });
        createDialog = $.noop;
    }

    $("#action-attach-url").live('click',function(){
        createDialog();
        var titlePopup = <?php echo json_encode(__('Attachement or URL', true))?>;
        $("#dialog_attachement_or_url").dialog('option',{title:titlePopup}).dialog('open');
    });

    $(".cancel").live('click',function(){
        $("#dialog_attachement_or_url").dialog('close');
    });
    $("#gs-url").click(function(){
        $(this).addClass('gs-url-add');
        $('#gs-attach').addClass('gs-attach-remove');
        $('.update_url').removeAttr('disabled').css('border', '1px solid #3B57EE');
        $('.update_attach_class').attr('disabled', 'disabled').css('border', '1px solid #d4d4d4');
    });
    $("#gs-attach").click(function(){
        $(this).removeClass('gs-attach-remove');
        $('#gs-url').removeClass('gs-url-add');
        $('.update_attach_class').removeAttr('disabled').css('border', '1px solid #3B57EE');;
        $('.update_url').attr('disabled', 'disabled').css('border', '1px solid #d4d4d4');
    });
    $('.row-number').parent().addClass('row-number-custom');
    function setupScroll(){
        $("#scrollTopAbsenceContent").width($(".grid-canvas-left:first").width()+50);
        $("#scrollTopAbsence").width($(".slick-viewport-left:first").width());
    }
    setTimeout(function(){
        setupScroll();
    }, 2500);
    $("#scrollTopAbsence").scroll(function () {
        $(".slick-viewport-left:first").scrollLeft($("#scrollTopAbsence").scrollLeft());
    });
    history_reset = function(){
        var check = false;
        $('.multiselect-filter').each(function(val, ind){
            var text = '';
            if($(ind).find('input').length != 0){
                text = $(ind).find('input').val();
            } else {
                text = $(ind).find('span').html();
                if( text == "<?php __('-- Any --');?>" || text == '-- Any --'){
                    text = '';
                }
            }
            if( text != '' ){
                $(ind).css('border', 'solid 2px orange');
                check = true;
            } else {
                $(ind).css('border', 'none');
            }
        });
        if(!check){
            $('#reset-filter').addClass('hidden');
        } else {
            $('#reset-filter').removeClass('hidden');
        }
    }
    resetFilter = function(){
        // HistoryFilter.stask = '{}';
        // HistoryFilter.send();
        // $('.multiselect-filter').each(function(val, ind){
            // if($(ind).find('input').length != 0){
                // $(ind).find('input').val('');
            // } else {
                // $(ind).find('span').html("<?php __('-- Any --');?>");
            // }
            // $(ind).css('border', 'none');
            // $('#reset-filter').addClass('hidden');
        // });
        // setTimeout(function(){
            // location.reload();
        // }, 500);
		$('.multiselect-filter input').val('').trigger('change');
		$('.multiSelectOptions input[type="checkbox"]').prop('checked', false).trigger('change');
		dataGrid.setSortColumn();
		$('input[name="project_container.SortOrder"]').val('').trigger('change');
		$('input[name="project_container.SortColumn"]').val('').trigger('change');
    }
})(jQuery);
function callMutitext(id){
    $('#commentsPopup').dialog({
        position    :'center',
        autoOpen    : false,
        autoHeight  : true,
        modal       : true,
        width       : 450,
        open : function(e){
            var $dialog = $(e.target);
            $dialog.dialog({open: $.noop});
        }
    });
    createDialog = $.noop;
    $("#commentsPopup").dialog('option',{title:''}).dialog('open');
    $('.submit-btn-msg').attr('data-id', id);
    var popup = $("#commentsPopup");
    var body = popup.find(".modal-body");
    var _html = '';
    body.html(_html);
    $.ajax({
        url: '/project_expectations/getComment',
        type: 'POST',
        data: {
            id: id,
            model: 'project_expectations'
        },
        dataType: 'json',
        success: function(data) {
            if (data['comment']) {
                $.each(data['comment'], function(ind, _data) {
                    var idEm = _data['employee_id']['id'],
                        name = _data['employee_id']['first_name'] + ' ' + _data['employee_id']['last_name'],
                        content = _data['content'].replace(/\n/g, "<br>"),
                        date = _data['created'];
                    var link = listAvartar[idEm];
                    if (employee_id == idEm) {
                        _html += '<div class="my-comment"><div class="my-date"><span>' + date + '</span></div><div class="my-content"><span>' + content + '</span></div></div><div class="right-avatar"><img class="avatar" src="'+link+'" alt="photo"></div>';
                    } else {
                        _html += '<div class="left-comment"><img class="avatar" src="' + link + '" alt="photo"></div><div class="comment"><div class="right-comment"><div><span>' + name + '</span><span class="date"> &nbsp' + date + '</span></div><div><span>' + content + '</span></div></div></div>';
                    }
                });
            } else {
                _html += '';
            }
            body.html(_html);
        }
    });
}
var saveCommentPj = function(){
    var text = $('.textarea-ct').val(),
        _id = $('.submit-btn-msg').data('id');
    var d = new Date,
    dformat =   [ d.getFullYear(),
                ((d.getMonth()+1) < 10 ? '0'+(d.getMonth()+1) : (d.getMonth()+1)),
                (d.getDate()< 10 ? '0'+d.getDate() : d.getDate())].join('-')+' '+
                [(d.getHours() < 10 ? '0'+d.getHours() : d.getHours()),
                (d.getMinutes() < 10 ? '0'+d.getMinutes() : d.getMinutes()),
                (d.getSeconds() < 10 ? '0'+d.getSeconds() : d.getSeconds())].join(':');
    var link = listAvartar[employee_id];
    var content = text.replace(/\n/g, "<br>");
    if(text != ''){
        $.ajax({
            url: '/project_expectations/saveComment',
            type: 'POST',
            data: {
                id: _id,
                model: 'project_expectations',
                content: text
            },
            success: function(success){
                $('.modal-body').append('<div class="my-comment"><div class="my-date"><span>' + dformat + '</span></div><div class="my-content"><span>' + content + '</span></div></div><div class="right-avatar"><img class="avatar" src="'+link+'" alt="photo"></div>')
                $('.textarea-ct').val('');
            }
        });
    }
};
// minus date - val (val is number).
function diffDate(date, val){
    var d = date.split('-');
    var newdate = new Date(d[2], d[1]-1, d[0]);
    newdate.setDate(newdate.getDate() - val); // minus the date
    var nd = new Date(newdate);
    var dd = nd.getDate() < 10 ? '0' + nd.getDate() : nd.getDate();
    var mm = nd.getMonth() < 10 ? '0' + (nd.getMonth() + 1) : nd.getMonth() + 1;
    var yyyy = nd.getFullYear();
    var _date = dd + '-' + mm + '-' + yyyy;
    return _date;
}
</script>
