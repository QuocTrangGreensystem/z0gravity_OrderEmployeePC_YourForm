<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->css('projects'); ?>
<?php echo $html->css('slick_grid/slick.grid'); ?>
<?php echo $html->css('slick_grid/slick.pager'); ?>
<?php echo $html->css('slick_grid/slick.common'); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<script type="text/javascript">
</script>
<style>
    .slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
    .wd-input-contries{
        width: auto;
        height: 32px;
        margin-bottom: 15px;
    }
</style>
<!-- export excel  -->
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'project_parts', 'action' => 'export', $projectName['Project']['id'])));
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
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
                                <h2 class="wd-t3"></h2>
                                <?php if($mutil_country): ?>
                                <select class="wd-input-contries" name="typeRequest" id="typeRequest">
                                    <?php foreach ($list_country as $id => $name) { ?>
                                        <option value="<?php echo $id ?>" <?php echo $typeSelect == $id ?'selected' : '';?>><?php echo $name?></option>
                                    <?php } ?>
                                </select>
                                <?php endif; ?>
                                <div id="message-place">
                                    <?php
                                    App::import("vendor", "str_utility");
                                    $str_utility = new str_utility();
                                    echo $this->Session->flash();
                                    ?>
                                </div>
                                <div class="wd-table" id="project_container" style="width:100%;height:400px;">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $this->element('dialog_projects') ?>
<?php echo $html->script('slick_grid/lib/jquery-ui-1.8.16.custom.min'); ?>
<?php echo $html->script('slick_grid/lib/jquery.event.drag-2.0.min'); ?>
<?php echo $html->script('slick_grid/slick.core'); ?>
<?php echo $html->script('slick_grid/slick.dataview'); ?>
<?php echo $html->script('slick_grid/controls/slick.pager'); ?>
<?php echo $html->script('slick_grid/slick.formatters'); ?>
<?php echo $html->script('slick_grid/plugins/slick.cellrangedecorator'); ?>
<?php echo $html->script('slick_grid/plugins/slick.cellrangeselector'); ?>
<?php echo $html->script('slick_grid/plugins/slick.cellselectionmodel'); ?>
<?php echo $html->script('slick_grid/slick.editors'); ?>
<?php echo $html->script('slick_grid/slick.grid'); ?>
<?php echo $html->script(array('slick_grid_custom')); ?>
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
        'id' => 'no.',
        'field' => 'no.',
        'name' => '#',
        'width' => 40,
        'sortable' => true,
        'resizable' => false,
        'noFilter' => 1,
    ),
    array(
        'id' => 'monday',
        'field' => 'monday',
        'name' => __('Monday', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    //'formatter' => 'Slick.Formatters.dselectBox'
    ),
    array(
        'id' => 'tuesday',
        'field' => 'tuesday',
        'name' => __('Tuesday', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'wednesday',
        'field' => 'wednesday',
        'name' => __('Wednesday', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'thursday',
        'field' => 'thursday',
        'name' => __('Thursday', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'friday',
        'field' => 'friday',
        'name' => __('Friday', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'saturday',
        'field' => 'saturday',
        'name' => __('Saturday', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'sunday',
        'field' => 'sunday',
        'name' => __('Sunday', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
        ));
$i = 1;
$dataView = array();
$options = array('0.0' => '0.0', '0.5' => '0.5', '1.0' => '1.0');
$selectMaps = array(
    'monday' => $options,
    'tuesday' => $options,
    'wednesday' => $options,
    'thursday' => $options,
    'friday' => $options,
    'saturday' => $options,
    'sunday' => $options
);
$workday = !empty($workdays[0]) ? $workdays[0] : array();
$data = array(
	'id' => isset( $workday['Workday']['id']) ? $workday['Workday']['id'] : '',
	'company_id' => $company_id,
	'no.' => $i++,
	'MetaData' => array()
);
$data['monday']		= isset($workday['Workday']['monday']) 		? $workday['Workday']['monday'] 	: '1.0';
$data['tuesday']	= isset($workday['Workday']['tuesday']) 	? $workday['Workday']['tuesday'] 	: '1.0';
$data['wednesday']	= isset($workday['Workday']['wednesday']) 	? $workday['Workday']['wednesday'] 	: '1.0';
$data['thursday']	= isset($workday['Workday']['thursday']) 	? $workday['Workday']['thursday'] 	: '1.0';
$data['friday']		= isset($workday['Workday']['friday']) 		? $workday['Workday']['friday'] 	: '1.0';
$data['saturday']	= isset($workday['Workday']['saturday']) 	? $workday['Workday']['saturday'] 	: '0.0';
$data['sunday']		= isset($workday['Workday']['sunday']) 		? $workday['Workday']['sunday'] 	: '0.0';

$dataView[] = $data;
$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true)
);
?>
<script type="text/javascript">
    var DateValidate = {};
    (function($){
        $(function(){
            var $this = SlickGridCustom;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  true;

            $.extend(Slick.Formatters,{
                dselectBox : function(row, cell, value, columnDef, dataContext){
                    var _value =  Slick.Formatters.selectBox(row, cell,value, columnDef, dataContext);
                    return /.0$/.test(_value) ? parseInt(_value, 10) : value;
                }
            });

            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                company_id : {defaulValue : '<?php echo $company_id; ?>', allowEmpty : false},
                country_id : {defaulValue : '<?php echo $typeSelect; ?>'},
                monday : {defaulValue : '' , allowEmpty : false},
                tuesday : {defaulValue : '', allowEmpty : false},
                wednesday : {defaulValue : '', allowEmpty : false},
                thursday : {defaulValue : '',allowEmpty : false},
                friday : {defaulValue : '',allowEmpty : false},
                saturday : {defaulValue : 0},
                sunday : {defaulValue : 0}
            };
            var _enableAddRow = true;
            if(data && data.length > 0){
                _enableAddRow = false;
            }
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            $this.init($('#project_container'),data,columns , {
                enableAddRow : _enableAddRow,
                showHeaderRow : false
            });
            $this.onAfterSave =  function(result,args){
                $this.init($('#project_container'),data,columns , {
                    enableAddRow : false,
                    showHeaderRow : false
                });
                return true;
            };
        });

        $('#typeRequest').change(function(){
            var linkRequest = '/workdays/index/',
                company_id = <?php echo json_encode($company_id) ?>,
                country_id = $(this).val();
            linkRequest = linkRequest + company_id + '/' + country_id;
            window.location.href = linkRequest;
        });
    })(jQuery);
</script>
