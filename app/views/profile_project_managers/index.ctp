<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->script('history_filter'); ?>
<?php echo $html->css('slick_grid/slick.grid'); ?>
<?php echo $html->css('slick_grid/slick.pager'); ?>
<?php echo $html->css('slick_grid/slick.common'); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
    .slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
    .wd-input-select{
        margin-bottom: 3px;
    }
    .wd-input-select label{
        font-size: 13px;
        font-weight: bold;
        padding-right: 20px;
        margin-top: 12px;
        display: block;
        float: left;
    }
    .wd-input-select select{
        padding: 5px;
        float: left;
        border: 1px solid rgb(179, 179, 179);
    }
    .gr-settings{
        width: 500px;
    }
    .gr-settings .wd-input-select select{
        float: right;
    }
    a.wd-edit{
        margin-top: 0px;
        margin-left: -20px;
    }
</style>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
                                <a href="javascript:void(0);" class="btn btn-plus-green" id="add-new-sales" style="margin-right:5px; margin-bottom: 10px" onclick="addNewDeliverablesButton();" title="<?php __('Add an item') ?>"></a>
                                <div id="message-place">
                                    <?php
                                    App::import("vendor", "str_utility");
                                    $str_utility = new str_utility();
                                    echo $this->Session->flash();
                                    ?>
                                </div>
                                <div class="wd-table" id="project_container" style="width:100%;height:400px;clear: both;">
                                </div>
                                <!--<div id="pager" style="width:100%;height:36px; overflow: hidden;">
                                </div>-->
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
        'id' => 'profile_name',
        'field' => 'profile_name',
        'name' => __('Name', true),
        'width' => 250,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox',
        'validator' => 'DataValidator.isUnique'
    ),
    array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __('Action', true),
        'width' => 100,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
        ));
$i = 1;
$dataView = array();
$selectMaps = array();
foreach ($listProfiles as $listProfile) {
    $data = array(
        'id' => $listProfile['ProfileProjectManager']['id'],
        'company_id' => $company_id,
        'no.' => $i++,
        'MetaData' => array()
    );
    $data['profile_name'] = (string) $listProfile['ProfileProjectManager']['profile_name'];
    $data['action.'] = '';
    $dataView[] = $data;
}
$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'The code is avaiable, please enter another!' => __('The code is avaiable, please enter another!', true),
    'Clear' => __('Clear', true)
);
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a class="wd-edit" title="Edit" href="<?php echo $this->Html->url(array('action' => 'edit', '%1$s')); ?>"><?php __('Edit') ?></a>
            <a style="margin-left: 5px;" onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<script type="text/javascript">

	var wdTable = $('.wd-table');
	$(document).ready(set_slick_table_height);
	$(window).resize(set_slick_table_height);
	function set_slick_table_height(){
		if( wdTable.length){
			var heightTable = $(window).height() - wdTable.offset().top - 45;
			console.log(heightTable);
			wdTable.css({
				height: heightTable,
			});
			heightViewPort = heightTable - 72;
			wdTable.find('.slick-viewport').height(heightViewPort);
			console.log( heightViewPort, "   ");
			clearInterval(wdTable);
		}
	}
	
    var DataValidator = {};
    var dataGrid;
    var loading = "<span id='loadingElm'><img src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt='Loading' /></span>";
    (function($){
        $(function(){
            var $this = SlickGridCustom;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  true;
            // For validate date
            var actionTemplate =  $('#action-template').html();
            DataValidator.isUnique = function(value,args){
                var result = true;
                $.each(args.grid.getData().getItems() , function(undefined,dx){
                    if(args.item.id && args.item.id == dx.id){
                        return true;
                    }
                    return true;
                    // return (result = (dx.name.toLowerCase() != value.toLowerCase()));
                });
                return {
                    valid : result,
                    message : $this.t('The Profile has already been exist.')
                };
            }
            $.extend(Slick.Formatters,{
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,
                    dataContext.company_id,dataContext.name), columnDef, dataContext);
                }
            });
            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                company_id : {defaulValue : '<?php echo $company_id; ?>', allowEmpty : false},
                profile_name : {defaulValue : '' , allowEmpty : false, maxLength: 100},
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            dataGrid = $this.init($('#project_container'),data,columns);
            addNewDeliverablesButton = function(){
                dataGrid.gotoCell(data.length, 1, true);
            }
        });
    })(jQuery);
</script>
