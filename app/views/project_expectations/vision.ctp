<?php
echo $this->Html->css(array(
    'jquery.multiSelect',
    'slick_grid/slick.grid_v2',
    'slick_grid/slick.pager',
    'slick_grid/slick.common_v2',
    'slick_grid/slick.edit',
    '/js/qtip/jquery.qtip',
    'slick_grid/slick.grid'
));
echo $this->Html->script(array(
    'history_filter',
    'slick_grid/lib/jquery-ui-1.8.16.custom.min',
    'slick_grid/slick.core',
    'slick_grid/slick.dataview',
    'slick_grid/controls/slick.pager',
    'slick_grid/slick.formatters',
    'slick_grid/plugins/slick.cellrangedecorator',
    'slick_grid/plugins/slick.cellrangeselector',
    'slick_grid/plugins/slick.cellselectionmodel',
    'slick_grid/plugins/slick.dataexporter',
    'slick_grid/slick.editors',
    'slick_grid_custom',
    'qtip/jquery.qtip',
    'slick_grid/lib/jquery.event.drag-2.0.min',
    'slick_grid/slick.grid',
));
echo $this->element('dialog_projects');
App::import("vendor", "str_utility");
$str_utility = new str_utility();

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
?>
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
    margin-top: 5px;
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
.slick-headerrow-columns {height: 36px !important;}
.slick-header .slick-header-column{
    padding: 10px 5px !important;
}
.slick-pane-top {
    top: 69px !important;
}
.slick-pane-right .slick-cell,
.slick-pane-right .slick-headerrow-column {
    border-right-color: #aaa;
    border-left: 0;
}
.slick-header-columns-right .slick-header-column:nth-child(2n+1){
    background: #09c !important;
}
</style>
<div id="attachment-template" style="display: none;">
    <div style="overflow: hidden;" class="img-to-right">
        <a class="download-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '?' => array('type' => 'download'))); ?>"><?php echo __('Download', true); ?></a>
        &nbsp; <a class="delete-attachment" style="display: none" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '?' => array('type' => 'delete'))); ?>" rel="%2$s"><?php echo __('Delete', true); ?></a>
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
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Are you sure you want to delete this acceptance?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-title">
            </div>
            <div class="wd-list-project">
                <div class="wd-table project-list" id="project_container" style="width: 100%; float: left; height: 600px;">
                </div>
                <div id="pager" style="clear:both;width:100%;height:36px;"></div>
            </div>
        </div>
    </div>
</div>
<div id="collapse" onclick="collapseScreen();" ><button class="btn btn-esc"></button></div>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<?php
$viewLong = false;
$columns = array(
    array(
        'id' => 'no.',
        'field' => 'no.',
        'name' => '#',
        'width' => 40,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
    ),
    array(
        'id' => 'project_id',
        'field' => 'project_id',
        'name' => __('Project name', true),
        'width' => 150,
        'class' => 'align-center-class',
        'sortable' => true,
        'resizable' => true,
    ),
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
           //'editor' => 'Slick.Editors.selectBachground',
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
                // 'editor' => 'Slick.Editors.selectBox'
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
            // 'editor' => 'Slick.Editors.datePicker',
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
            // 'editor' => 'Slick.Editors.textArea',
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
            // 'editor' => 'Slick.Editors.textBox'
        );
    } else if( strpos($dx['field'], 'ssigned_to_') == 1 ){
        $columns[] = array(
            'id' => $dx['field'],
            'field' => $dx['field'],
            'name' => $name,
            'width' => 200,
            'sortable' => true,
            'resizable' => true,
            // 'editor' => 'Slick.Editors.mselectBox'
        );
    } else if( $dx['field'] == 'milestone' ){
        $columns[] = array(
            'id' => $dx['field'],
            'field' => $dx['field'],
            'name' => $name,
            'width' => 160,
            'sortable' => true,
            'resizable' => true,
            // 'editor' => 'Slick.Editors.selectBox',
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
            // 'editor' => 'Slick.Editors.Attachement',
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
$i = 1;
$dataView = array();
$selectMaps = array(
    'project_id' => $listProject,
    'milestone' => $milestone,
    'assigned_to_1' => $listEmployeeAndProfit,
    'assigned_to_2' => $listEmployeeAndProfit,
    'assigned_to_3' => $listEmployeeAndProfit,
);
foreach ($datasets as $key => $value) {
    $selectMaps[$key] = $value;
}
$i18n = array();
foreach ($projectExpec as $id => $_projectExpec) {
    $_data = $_projectExpec['ProjectExpectation'];
    $_data['milestone_date'] = !empty($_projectExpec['ProjectExpectation']['milestone']) && !empty($milestone_date[$_projectExpec['ProjectExpectation']['milestone']]) ? str_utility::convertToVNDate($milestone_date[$_projectExpec['ProjectExpectation']['milestone']]) : '';
    foreach ($fieldsets as $value) {
        $dx = $value['Expectation'];
        $_fieldset = $dx['field'];
        if(strpos($_fieldset, 'ate_') == 1 ){
            $_data[$_fieldset] = $_projectExpec['ProjectExpectation'][$_fieldset] ? str_utility::convertToVNDate($_projectExpec['ProjectExpectation'][$_fieldset]) : '';
        } else if(strpos($_fieldset, 'lert_') == 1 ){
            if(!empty($_projectExpec['ProjectExpectation']['milestone']) && !empty($milestone_date[$_projectExpec['ProjectExpectation']['milestone']])){
                $date = str_utility::convertToVNDate($milestone_date[$_projectExpec['ProjectExpectation']['milestone']]);
                $num = $dateAlert[$_fieldset];
                $t = '-' . $num . ' day';
                $_data[$_fieldset] = date('d-m-Y', strtotime($t, strtotime($date)));
            } else {
                $_data[$_fieldset] = '';
            }
        } else if(strpos($_fieldset, 'ssigned_to_') == 1 ){
            $_data[$_fieldset] = array();
            if(!empty($_expectationEmployeeRefer[$_fieldset][$_projectExpec['ProjectExpectation']['id']])){
                $v = $_expectationEmployeeRefer[$_fieldset][$_projectExpec['ProjectExpectation']['id']];
                foreach ($v as $key => $value) {
                    $_data[$_fieldset][$key] = $value['is_profit_center'] . '-' . $value['reference_id'];
                }
            }
        }
    }
    $_data['no.'] = $id;
    $_data['DataSet'] = array();

    $dataView[] = $_data;
}
if($viewLong){
?>
<style>
.slick-cell{
    padding: 8px 2px 2px 0 !important;
}
</style>
<?php
}
?>

<script type="text/javascript">
    var DataValidator = {};
    var listColor = <?php echo json_encode($_listColor) ?>;
    var colorDefault = <?php echo json_encode($colorDefault) ?>;
    var viewLong = <?php echo json_encode($viewLong) ?>;
    (function($){

        $(function(){
            var $this = SlickGridCustom;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  true;
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
                },
                longText : function(row, cell, value, columnDef, dataContext){
                    var textArea = value.replace(/\n/g, "<br>");
                    return "<div style='line-height: normal'><span>" + textArea + '</span></div>';
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
                    $("#ok_attach").click(function(){
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
                                _html += '<input type="radio" onClick="setColor('+ind+')" name="color" checked style="float: left" rel="no-history"/><div style="margin-left: 20px; height: 22px; background-color: '+val+'" >&nbsp;</div>';
                            } else {
                                _html += '<input type="radio" onClick="setColor('+ind+')" name="color" style="float: left" rel="no-history"/><div style="margin-left: 20px; height: 22px; background-color: '+val+'" >&nbsp;</div>';
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
            $this.onBeforeEdit = function(args){
                if(args.column.id == 'Status' && roleLogin == 'pm'){
                    if(args.item.idPr && $.inArray(args.item.idPr, _listIdModifyByPm) != -1){
                        // do nothing
                    } else {
                        return false;
                    }
               }
               return true;
            }
            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {};
            $this.url =  '<?php echo $html->url(array('action' => '')); ?>';
            var options = {
                rowHeight:  viewLong ? 60 : 33,
                enableAddRow: false
            };
            var dataGrid = $this.init($('#project_container'),data,columns,options);
            var exporter = new Slick.DataExporter('/projects/export');
            dataGrid.registerPlugin(exporter);
            $('#export-table').click(function(){
                exporter.submit();
                return false;
            });
        });
    })(jQuery);
</script>
