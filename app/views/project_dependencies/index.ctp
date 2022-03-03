<?php
echo $html->css('jquery.multiSelect');
echo $html->css('projects');
echo $html->css('slick_grid/slick.grid');
echo $html->css('slick_grid/slick.pager');
echo $html->css('slick_grid/slick.common');
echo $html->css('slick_grid/slick.edit');
echo $html->script('jquery.multiSelect');
echo $html->script('slick_grid/lib/jquery-ui-1.8.16.custom.min');
echo $html->script('slick_grid/lib/jquery.event.drag-2.0.min');
echo $html->script('slick_grid/slick.core');
echo $html->script('slick_grid/slick.dataview');
echo $html->script('slick_grid/controls/slick.pager');
echo $html->script('slick_grid/slick.formatters');
echo $html->script('slick_grid/plugins/slick.cellrangedecorator');
echo $html->script('slick_grid/plugins/slick.cellrangeselector');
echo $html->script('slick_grid/plugins/slick.cellselectionmodel');
echo $html->script('slick_grid/slick.editors');
echo $html->script('slick_grid/slick.grid');
echo $html->script(array('slick_grid_custom'));
echo $this->element('dialog_projects');
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
    .color {
        display: inline-block;
        width: 15px;
        height: 15px;
        border: 1px solid #ddd;
        vertical-align: middle;
    }
    a#open-diagram {
        text-align: center;
        line-height: 32px;
        display: inline-block;
        width: 32px;
        height: 32px;
        text-decoration: none;
        font-size: 20px;
        color: #424242;
        font-weight: normal;
    }
    #open-diagram:before{
        content: "\e616";
        font-family: 'simple-line-icons';

    }
    .wd-weather-list-dd ul li img{
        width: 24px;
        height: 24px;
    }
    .wd-weather-list-dd ul li{
        width: 48px;
    }
    #wd-container-footer{
        display: none;
    }
    body{
        overflow: hidden;
    }
</style>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"><div class="wd-panel">
            <div class="wd-list-project">
                <div class="wd-title">
                    <h2 class="wd-t1"><?php echo $projectName['Project']['project_name'] ?></h2>
                    <a href="<?php echo $this->Html->url('/project_dependencies/view/' . $projectName['Project']['id']) ?>" id="open-diagram">&nbsp;</a>
                    <?php
                    $employee_info = $this->Session->read("Auth.employee_info");
                    if($employee_info['Role']['name'] == 'admin'){
                    ?>
                    <a style="border-radius: 5px;" target="_blank" href="<?php echo $this->Html->url('/dependencies/index/ajax')?>" class="button-setting" <img style="margin-right: 0" src="<?php echo $this->Html->url('/img/ui/blank-plus.png') ?>" alt="" /></a>
                    <?php } ?>
                </div>
                <div class="wd-table" id="project_container" style="width:100%;height:400px;"></div>
                <div id="pager" style="width:100%;height:0px; overflow: hidden;"></div>
            </div>
            </div></div>
        </div>
    </div>
</div>
<?php
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
        'id' => 'target_id',
        'field' => 'target_id',
        'name' => __('Project', true),
        'width' => 200,
        'noFilter' => 1,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox',
        'validator' => 'DateValidate.isUnique'
    ),
    array(
        'id' => 'dependency_ids',
        'field' => 'dependency_ids',
        'name' => __('Dependency', true),
        'width' => 500,
        'noFilter' => 1,
        'sortable' => false,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.dependency',
        'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'select_id',
        'field' => 'select_id',
        'name' => '',
        'width' => 120,
        'noFilter' => 1,
        'sortable' => false,
        'resizable' => false,
        'formatter' => 'Slick.Formatters.imageData',
        'editor' => 'Slick.Editors.imageData'
    ),
    array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __('Action', true),
        'width' => 70,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
    )
);
$dataView = array();
App::import('Vendor', 'str_utility');
foreach ($data as $dat) {
    $array = array();
    $array['id'] = $dat['ProjectDependency']['id'];
    $array['target_id'] = $dat['ProjectDependency']['target_id'];
    $array['dependency_ids'] = json_decode($dat['ProjectDependency']['dependency_ids']);
    $array['select_id'] = $dat['ProjectDependency']['value'];
    $array['project_id'] = $projectName['Project']['id'];
    $dataView[] = $array;
}
$selectMaps = array(
    'target_id' => $projects,
    'dependency_ids' => $dependencies
);
$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true),
    'Date closing must between %1$s and %2$s' => __('Date closing must between %1$s and %2$s', true)
);
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(__('Delete?', true)); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<script type="text/javascript">
    var DateValidate = {},ControlGrid,IuploadComplete = function(json){
        var data = ControlGrid.eval('currentEditor');
        data.onComplete(json);
    };
    var projectName = <?php echo json_encode($projectName['Project']); ?>;
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
    $(document).ready(function($){
        var $this = SlickGridCustom;
        DateValidate.isUnique = function(value,args){
            var result = true,_value = $.trim(value).toLowerCase();
            var items = args.grid.getData().getItems();
            for(var i in items){
                if( items[i].target_id == _value ){
                    result = false;
                    break;
                }
            }
            return {
                valid : result,
                message : $this.t('This item already existed!')
            };
        };
        $this.i18n = <?php echo json_encode($i18n); ?>;
        $this.canModified =  <?php echo json_encode((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite)); ?>;
        $this.selectMaps = <?php echo json_encode($selectMaps) ?>;
        var colors = <?php echo json_encode($colors) ?>;

        var actionTemplate =  $('#action-template').html();

        $.extend(Slick.Formatters,{
            dependency : function(row, cell, value, columnDef, dataContext){
                var _value = [];
                $.each(value, function(i,val){
                    if($this.selectMaps['dependency_ids'][val]){
                        _value.push('<span class="color" style="background-color: ' + colors[val] + '"></span> ' + $this.selectMaps['dependency_ids'][val]);
                    }
                });
                return Slick.Formatters.HTMLData(row, cell, _value.join(', '), columnDef, dataContext);
            },
            imageData: function(row, cell, value, columnDef, dataContext){
                var _html = '<div class="display-image" '+ (!$this.canModified ? '' : 'onclick="updateImage.call(this)"' ) + '>';
                // value = (value !== 'undefined') ? value : 0;
                if(value == 3){
                    _html += '<center><img src="<?php echo $this->Html->url('/img/arrow-left.png')?>"><img src="<?php echo $this->Html->url('/img/arrow-right.png')?>"></center>';
                } else if(value == 2) {
                    _html += '<center><img src="<?php echo $this->Html->url('/img/arrow-right.png')?>"></center>';
                } else if(value == 1){
                    _html += '<center ><img src="<?php echo $this->Html->url('/img/arrow-left.png')?>"></center>';
                } else {
                    _html += '<span>&nbsp</span>';
                }
                _html +='</div>';
                if(!$this.canModified){
                    return Slick.Formatters.HTMLData(row, cell, _html, columnDef, dataContext);
                }
                _html += '<div class="update-image" data-id="'+ dataContext.id + '" data-value="'+value+'" style="overflow: hidden;"><div class="wd-input wd-weather-list-dd"><ul style="float: left; display: inline; overflow: hidden">';
                if(value == 3){
                    _html += '<li><input id="image-left" value="left" <?php echo !(($canModified && !$_isProfile )|| ($_isProfile && $_canWrite)) ? 'disabled' : '' ?>  type="checkbox" checked><img style="float: none" src="<?php echo $this->Html->url('/img/arrow-left.png')?>"></li>';
                    _html += '<li><input id="image-right" value="right" type="checkbox" checked><img style="float: none" src="<?php echo $this->Html->url('/img/arrow-right.png')?>"></li>';
                } else if(value == 2){
                    _html += '<li><input id="image-left" value="left" type="checkbox"><img style="float: none" src="<?php echo $this->Html->url('/img/arrow-left.png')?>"></li>';
                    _html += '<li><input id="image-right" value="right" type="checkbox" checked><img style="float: none" src="<?php echo $this->Html->url('/img/arrow-right.png')?>"></li>';
                } else if(value == 1){
                    _html += '<li><input id="image-left" value="left" type="checkbox" checked><img style="float: none" src="<?php echo $this->Html->url('/img/arrow-left.png')?>"></li>';
                    _html += '<li><input id="image-right" value="right" type="checkbox"><img style="float: none" src="<?php echo $this->Html->url('/img/arrow-right.png')?>"></li>';
                } else {
                    _html += '<li><input id="image-left" value="left" type="checkbox"><img style="float: none" src="<?php echo $this->Html->url('/img/arrow-left.png')?>"></li>';
                    _html += '<li><input id="image-right" value="right" type="checkbox"><img style="float: none" src="<?php echo $this->Html->url('/img/arrow-right.png')?>"></li>';
                }
                _html += '</ul></div></div>';
                return Slick.Formatters.HTMLData(row, cell, _html, columnDef, dataContext);
            },
            Action : function(row, cell, value, columnDef, dataContext){
                return Slick.Formatters.HTMLData(row, cell, $this.t(actionTemplate, dataContext.id,
                projectName.id), columnDef, dataContext);
            }
        });
        $.extend(Slick.Editors,{
            forecastValue : function(args){
                $.extend(this, new Slick.Editors.textBox(args));
                this.input.attr('maxlength' , 11).keypress(function(e){
                    var key = e.keyCode ? e.keyCode : e.which;
                    if(!key || key == 8 || key == 13){
                        return;
                    }
                    var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
                    if(!/^([0-9]{0,8})(\.[0-9]{0,2})?$/.test(val)){
                        e.preventDefault();
                        return false;
                    }
                });
            }
       });

        var  data = <?php echo json_encode($dataView); ?>;
        var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
        $this.fields = {
            id : {defaulValue : 0},
            target_id : {defaulValue : 0, allowEmpty : false},
            dependency_ids : {defaulValue : '', allowEmpty : false},
            project_id: {defaulValue : projectName.id}
        };
        $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
        ControlGrid = $this.init($('#project_container'),data,columns);
        $this.onBeforeEdit = function(args){
            if( !args.item && args.column.field != 'target_id' ){
                return false;
            }
            // if( args.column.field == 'target_id' && args.item ){
            // 	return false;
            // }
            return true;
        }
        $this.onCellChange = function(args){
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
        $('.row-center').parent().addClass('row-center-custom');
    });
    function updateImage(){
        var element = $(this).next();
        var t = $(this);
        var id = element.attr('data-id');
        var value = element.attr('data-value');
        $(this).hide();
        element.mouseleave(function(e){
            var t1 = element.find('#image-left').attr('checked');
            var t2 = element.find('#image-right').attr('checked');
            var _val = 0;
            if( t1 !== undefined && t2 !== undefined){
                _val = 3;
            } else if( t1 === undefined && t2 !== undefined ){
                _val = 2;
            } else if( t1 !== undefined && t2 === undefined ){
                _val = 1;
            }
            if(value != _val){
                $.ajax({
                    url: '/project_dependencies/updateImage/',
                    type: 'POST',
                    data: {
                        id: id,
                        value: _val
                    },
                    success: function(){
                        var _html ='';
                        if(_val == 3){
                            _html += '<center><img src="<?php echo $this->Html->url('/img/arrow-left.png')?>"><img src="<?php echo $this->Html->url('/img/arrow-right.png')?>"></center>';
                        } else if(_val == 2) {
                            _html += '<center><img src="<?php echo $this->Html->url('/img/arrow-right.png')?>"></center>';
                        } else if(_val == 1){
                            _html += '<center><img src="<?php echo $this->Html->url('/img/arrow-left.png')?>"></center>';
                        } else {
                            _html += '<span>&nbsp</span>';
                        }
                        t.html(_html);
                    }
                });
            }
            t.show();
        });
    }
</script>
