<?php echo $html->script('jquery.validation.min'); 

echo $html->script(array(
        'slick_grid/lib/jquery-ui-1.8.16.custom.min',
        'slick_grid/lib/jquery.event.drag-2.0.min',
        'slick_grid/slick.core',
        'slick_grid/slick.dataview',
        'slick_grid/controls/slick.pager',
        'slick_grid/slick.formatters',
        'slick_grid/plugins/slick.cellrangedecorator',
        'slick_grid/plugins/slick.cellrangeselector',
        'slick_grid/plugins/slick.cellselectionmodel',
        'slick_grid/slick.editors',
        'slick_grid/slick.grid',
        'slick_grid_custom',
		'progress/nanobar'
    ));

    echo $html->css(array(
        'jquery.multiSelect',
        'slick_grid/slick.grid',
        'slick_grid/slick.pager',
        'slick_grid/slick.common',
        'preview/slickgrid',
        
    ));
?>
<?php echo $html->css('jquery.ui.custom'); 
echo $html->css('slick_grid/slick.edit'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<?php
$employee_info = $this->Session->read("Auth.employee_info");
?>
<style>
#project_container{
	margin-top: 20px;
}
</style>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                </div>
                <?php
                App::import("vendor", "str_utility");
                $str_utility = new str_utility();
                ?>
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
							<?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
								<div id="message-place">
									<?php
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
<div id="action-template" style="display: none;">
    <div class="action-menu">
        <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true))); ?>');" class="action-menu-item" href="<?php echo $this->Html->url(array('action' => 'delete_testimonial', '%1$s')); ?>">
		<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 20.03 20">
		  <defs>
			<style>
			  .cls-1 {
				fill: #666;
				fill-rule: evenodd;
			  }
			</style>
		  </defs>
		  <path id="suppr" class="cls-1" d="M6644.04,275a0.933,0.933,0,0,1-.67-0.279l-8.38-8.374-8.38,8.374a0.954,0.954,0,1,1-1.35-1.347l8.38-8.374-8.38-8.374a0.954,0.954,0,0,1,1.35-1.347l8.38,8.374,8.38-8.374a0.933,0.933,0,0,1,.67-0.279,0.953,0.953,0,0,1,.67,1.626L6636.33,265l8.38,8.374A0.953,0.953,0,0,1,6644.04,275Z" transform="translate(-6624.97 -255)"/>
		</svg>
		</a>
        
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
		'id' => 'no.',
		'field' => 'no.',
		'name' => '#',
		'width' => 40,
		'sortable' => true,
		'resizable' => false
	),
    array(
        'id' => 'value',
        'field' => 'value',
        'name' => __('Title', true),
        'width' => 250,
        'sortable' => true,
        'resizable' => true,
		'editor' => 'Slick.Editors.textBox',
    ),
    array(
        'id' => 'content',
        'field' => 'content',
        'name' => __('Content', true),
        'width' => 270,
        'sortable' => true,
        'resizable' => true,
		'editor' => 'Slick.Editors.textBox',
    ),
	'action.' => array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => '&nbsp;',
        'width' => 50,
        'minWidth' => 50,
        'maxWidth' => 80,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
    )
);
$i = 1;
$dataView = array();
foreach ($testimonials as $key => $testimonial) {
    $data = array(
        'id' => $testimonial['SasSetting']['id'],
        'MetaData' => array()
    );
	$data['value'] = $testimonial['SasSetting']['value'];
	$data['content'] = $testimonial['SasSetting']['content'];
	$data['weight'] = $testimonial['SasSetting']['weight'];
	$data['action.'] = '';
	$dataView[] = $data;
}
$i18n = array();

?>
<script type="text/javascript">
var timeoutID;
function get_grid_option(){
	var _option ={
		frozenColumn: '',
		enableAddRow: true,            
		showHeaderRow: false,
		rowHeight: 40,
		forceFitColumns: true,
		topPanelHeight: 40,
		headerRowHeight: 40
	};

	if( $(window).width() > 992 ){
		return _option;
	}
	else{
		_option.frozenColumn = '';
		_option.forceFitColumns = false;
		return _option;
	}
}
(function($){
	$(function(){
		var $this = SlickGridCustom;
        $this.i18n = <?php echo json_encode($i18n); ?>;
        $this.canModified =  true;
		$this.url = <?php echo json_encode($html->url(array('action' => 'update_testimonial'))); ?>;
		var actionTemplate =  $('#action-template').html();
		$.extend(Slick.Formatters,{
			Action : function(row, cell, value, columnDef, dataContext){
				return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id));
			},
			
		});
        var data = <?php echo json_encode($dataView); ?>;
        var columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
        $this.fields = {
            id : {defaulValue : 0},
            value : {defaulValue : '', allowEmpty : false},
            content : {defaulValue : '', allowEmpty : false},
            weight : {defaulValue : 0},
        };
        ControlGrid = $this.init($('#project_container'),data,columns, get_grid_option());
	});
})(jQuery);

</script>