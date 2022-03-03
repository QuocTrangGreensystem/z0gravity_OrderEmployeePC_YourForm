<?php
echo $html->script(array(
    'jquery.mjs.nestedSortable',
    'jquery.ui.touch-punch.min',
    'jquery.form'
));
echo $html->css(array(
    'nested-sortable',
	'preview/tab-admin',
	'layout_admin_2019'
));
?>
<style>
li .item-toggle-enable_newdesign{
	cursor: pointer;
    position: absolute;
    width: 30px;
    height: 26px;
    top: 0;
    right: 96px;
    text-indent: 100%;
    white-space: nowrap;
    overflow: hidden;
    line-height: 20px;
}
li .item-toggle-enable_newdesign::before {
    content: "☐";
    display: block;
    position: absolute;
    left: 0px;
    top: 3px;
    width: 100%;
    text-align: center;
    text-indent: 0px;
	color: #7ac6dc;
    font-size: 20px;
    font-weight: normal;
}
li.no-new-design >.item-toggle-enable_newdesign{
	/*cursor: not-allowed; */
	cursor: auto;
}
li.no-new-design >.item-toggle-enable_newdesign::before {
	color: #fff;
}
li.item-enable_newdesign >.item-toggle-enable_newdesign::before{
	content: "☑";
	color: rgb(12, 176, 224);
}
.wd-list-project .wd-tab .wd-content label {
	width: 350px;
}
#widget-library {
	width: 27%;
	min-width: 310px;
}
.sortable li .item-content {
	font-weight: unset;
}
.sortable .item-handle, .sortable .item-expand {
	height: 28px;
}
#zone {
	width: 60%;
}
</style>

<?php
$is_old_design  = !(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design']) ;
if( $is_old_design){
	echo '<style>li[data-widget="flash_info"], li[data-widget-item="flash_info"]{display: none;}</style>';
	foreach( $menus as $k => $item){
		if( ($item['Menu']['widget_id'] == 'flash_info') && !empty($item['children'])){
			$menus = array_merge( $menus, $item['children']);
			$menus[$k]['children'] = array();
		}
	}
}
function _recursive($item, $has_new_designs = array()){
	// Not yet
	// Remove Flash++ and Indicator from old design 
    $html = '';
    $current = $item['Menu'];
	$has_new_design = ( !empty( $has_new_designs[$current['controllers']]) && in_array( $current['functions'], (array) $has_new_designs[$current['controllers']]) );
    $widget_id = $current['widget_id'] . '-' . $current['id'];
    $class = 'list-item';
    $class .= $current['display'] ? '': ' item-hidden';
    $class .= $current['default_screen'] ? ' item-default' : '';
    $class .= $current['enable_newdesign']==1 ? ' item-enable_newdesign' : ''; // == 0 || = {enable_newdesign} is false
	if( $has_new_design ){
		$class .=' has-new-design';
	}else{
		$class .=' no-new-design';
	}
    $html .= '<li id="' . $widget_id . '" data-widget="' . $current['widget_id'] . '" data-id="' . $current['id'] . '" class="' . $class . '"><div class="item-handle">Drag</div><div class="item-expand"></div><div class="item-toggle-default" title="' . __('Default screen', true) . '"></div>';
    //display
    $html .= '<div class="item-toggle-display" title="' . __('Display', true) . '"></div>';
    $html .= '<div class="item-toggle-enable_newdesign" title="' .( $has_new_design ? __('Enable new design', true) : '' ). '"></div>';
    //meta data, inputs
    $html .= '<div class="item-content"><span class="disclose"><span></span></span><span class="item-title-eng">' . $current['name_eng'] . '</span> - <span class="item-title-fre">' . $current['name_fre'] . '</span></div>';
    $html .= '<div class="item-meta">';

    $html .= '<a href="javascript:;" onclick="remove_widget(this)" class="remove-widget">' . __('Delete', true) . '</a>';

    $html .= '<label style="width:70px;">' . __('English', true) . '</label>';
    $html .= '<input style="float:left;" type="text" class="item-text" data-lang="eng" name="data[Menu][' . $current['id'] . '][name_eng]" value="' . h($current['name_eng']) . '">';
    $html .= '<label style="width:70px;">' . __('French', true) . '</label>';
    $html .= '<input type="text" class="item-text" data-lang="fre" name="data[Menu][' . $current['id'] . '][name_fre]" value="' . h($current['name_fre']) . '">';

    //hidden fields
    $html .= '<input type="hidden" class="item-parent" name="data[Menu][' . $current['id'] . '][parent_id]" value="' . $current['parent_id'] . '">';
    $html .= '<input type="hidden" class="item-default_screen" name="data[Menu][' . $current['id'] . '][default_screen]" value="' . $current['default_screen'] . '">';
    $html .= '<input type="hidden" class="item-weight" name="data[Menu][' . $current['id'] . '][weight]" value="' . $current['weight'] . '">';
    $html .= '<input type="hidden" name="data[Menu][' . $current['id'] . '][controllers]" value="' . $current['controllers'] . '">';
    $html .= '<input type="hidden" name="data[Menu][' . $current['id'] . '][functions]" value="' . $current['functions'] . '">';
    $html .= '<input type="hidden" name="data[Menu][' . $current['id'] . '][widget_id]" value="' . $current['widget_id'] . '">';
    $html .= '<input type="hidden" name="data[Menu][' . $current['id'] . '][model]" value="' . $current['model'] . '">';
    $html .= '<input type="hidden" name="data[Menu][' . $current['id'] . '][company_id]" value="' . $current['company_id'] . '">';
    $html .= '<input type="hidden" class="item-display" name="data[Menu][' . $current['id'] . '][display]" value="' . $current['display'] . '">';
    $html .= '<input type="hidden" class="item-enable_newdesign" name="data[Menu][' . $current['id'] . '][enable_newdesign]" value="' . $current['enable_newdesign'] . '">';
    $html .= '<input type="hidden" class="item-delete" name="data[Menu][' . $current['id'] . '][delete]" value="0">';
    $html .= '</div>';
    //children recursive
    if( !empty($item['children']) ){
        $html .= '<ul>';
        foreach($item['children'] as $child){
            $html .= _recursive($child, $has_new_designs);
        }
        $html .= '</ul>';
    }
    $html .= '</li>';
    return $html;
}
function recursiveMenu($menu, $ul = true, $echo = true, $has_new_designs = array()){
    $html = '';
    if( $ul )$html .= '<ul class="sortable">';
    foreach($menu as $item){
        $html .= _recursive($item, $has_new_designs);
    }
    if( $ul )$html .= '</ul>';
    if( $echo )echo $html;
    else return $html;
}  
$is_sas = $employee_info['Employee']['is_sas'] && empty($employee_info['Company']['id']);
if((!empty($is_sas) && $is_sas == 1) || ($is_sas == 0 && empty($employee_info['Employee']['company_id']))){
	$isAdminSas = 1;
}else{
	$isAdminSas = 0;
}
?>
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
                                <div id="message-place">
                                    <?php
                                    echo $this->Session->flash();
                                    ?>
                                </div>
								<?php if($isAdminSas == 1){?>
									<div id="widget-library">
										<h3>
											<button id="save-menu"><?php __('Save') ?></button>
											<span><?php __('List') ?></span>
											<button id="add-widget"><?php __('Add') ?> ➕</button>
										</h3>
										<ul id="widget-list">
										<?php 
										foreach($widgets as $widget_id => $widget): ?>
											<li data-widget-item="<?php echo $widget_id ?>">
												<label for="<?php echo $widget_id ?>">
													<input type="checkbox" name="<?php echo $widget_id ?>" id="<?php echo $widget_id ?>" value="<?php echo $widget_id ?>">
													<span id="<?php echo $widget_id ?>-eng"><?php echo $widget['name_eng'] ?></span> - <span id="<?php echo $widget_id ?>-fre"><?php echo $widget['name_fre'] ?></span>
												</label>
											</li>
										<?php endforeach; ?>
										</ul>
									</div>
								<?php }else{ ?>
									<h3>
										<button id="save-menu"><?php __('Save') ?></button>
									</h3>
								<?php } ?>
                                <form id="zone" action="<?php echo $this->Html->url('/menus/save') ?>" method="post">
                                    <?php recursiveMenu($menus, true, true, $has_new_designs) ?>
                                </form>
                                <div style="clear: both"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
// variables
var template_no_newdesign = <?php echo json_encode(recursiveMenu(array(array(
    'Menu' => array(
        'widget_id' => '{widget_id}',
        'name_eng' => '{name_eng}',
        'name_fre' => '{name_fre}',
        'display' => 1,
        'default_screen' => 0,
        'enable_newdesign' => '{enable_newdesign}',
        'id' => '{id}',
        'model' => $model,
        'company_id' => $company_id,
        'functions' => '{functions}',
        'controllers' => '{controllers}',
        'weight' => '{weight}',
        'parent_id' => '{parent_id}'
    ),
    'children' => array()

)), false, false)) ?>,
	template_has_newdesign = <?php echo json_encode(recursiveMenu(array(array(
		'Menu' => array(
			'widget_id' => '{widget_id}',
			'name_eng' => '{name_eng}',
			'name_fre' => '{name_fre}',
			'display' => 1,
			'default_screen' => 0,
			'enable_newdesign' => '{enable_newdesign}',
			'id' => '{id}',
			'model' => $model,
			'company_id' => $company_id,
			'functions' => '{functions}',
			'controllers' => '{controllers}',
			'weight' => '{weight}',
			'parent_id' => '{parent_id}'
		),
		'children' => array(
		)
	)), false, false, array('{controllers}' => '{functions}'))) ?>,
    widgets = <?php echo json_encode($widgets) ?>, 
	has_new_designs = <?php echo json_encode($has_new_designs) ?>;
//console.log(template);
function remove_widget(el){
    var li = $(el).closest('li');
    // if( !$.isNumeric(li.data('id')) ){
    //     li.slideUp(200, function(){
    //         li.remove();
    //     });
    //     return;
    // }
    
    // var widget_id = li.val(),
    //     widget = widgets[widget_id];
    //     //add fields for widget
    //     console.log(widget);
    //     widget.display = 0;
    //     toData(widget);  

    if( confirm(<?php echo json_encode(__('Delete?', true)) ?>) ){
        //remove current node
        li.slideUp(200, function() {
            //move all children to this node
            li.find('> ul > li').each(function(){
                x = $(this).clone();
                parent = li.parent().parent();
                if( parent[0].nodeName == 'LI' ){
                    x.find('.item-parent:first').val(parent.data('id'));
                } else {
                    x.find('.item-parent:first').val('');
                }
                //remove child
                $(this).remove();
                x.insertAfter(li);
            });
            //remove if not saved yet
            if( !$.isNumeric(li.data('id')) ){
                li.remove();
            } else {
                //add data to form
                li.addClass('item-deleted').find('.item-delete:first').val(1);
            }
            init();
        });
    }
}

function toData(data){
    var result = template_no_newdesign;
	if( data.controllers in has_new_designs){
		if(has_new_designs[data.controllers].indexOf(data.functions) != -1 ){
			result = template_has_newdesign;
		}
	}
    for(var i in data){
        var regex = new RegExp('\{' + i + '\}', 'g');
        result = result.replace(regex, data[i]);
    }
    return result;
}

function bindText(t){
    t.off('change keyup').on('change keyup', function(){
        var text = $(this).val();
        $(this).closest('li').find('.item-title-' + $(this).data('lang')).first().text(text);
    });
}

function init(){
    //blur existed left item
    $('#widget-list li').each(function(){
        widget = $(this).data('widget-item');
        if( $('[data-widget="' + widget + '"]:not(.item-deleted)').length ){
            $(this).addClass('existed');
            $(this).find('input').attr("disabled", true);
        } else {
            $(this).removeClass('existed');
            $(this).find('input').attr("disabled", false);
        }
    });
    bindText($('.item-text'));
    updateWeight();
}

function updateWeight(){
    $('.list-item').each(function(i){
        $(this).find('.item-weight').val((i+1));
    });
}

$(document).ready(function(){
    //delegates
    $('#zone').on('click', '.item-expand', function(){
        var meta = $(this).closest('li').find('> .item-meta');
        var expand = $(this).closest('li').find('> .item-expand');
        meta.slideToggle('fast', function(){
            if( meta.is(':visible') ){
                expand.removeClass('closed').addClass('expanded');
            } else {
                expand.removeClass('expanded').addClass('closed');
            }
        });
    })
	
	/* Modified by Dai Huynh
	* 10-10-2018
	* Check remove default if not display
	*/
    .on('click', '.item-toggle-display', function(){
        var inp = $(this).closest('li').find('.item-display:first');
		var _def = $(this).closest('li').find('.item-default_screen');
		if( inp.val() == '1' && _def.val() == '0'){
            $(this).closest('li').addClass('item-hidden');
            inp.val(0);
        } else{
            if( inp.val() == 0) {
                $(this).closest('li').removeClass('item-hidden');
                inp.val(1);
            }
        }
    })
	
	/* Add Enable New design for screen 
	* By Dai Huynh 22-11-2018
	*/
    .on('click', '.has-new-design >.item-toggle-enable_newdesign', function(){
        var inp = $(this).closest('li').find('.item-enable_newdesign:first');
		var _def = $(this).closest('li').find('.item-default_screen');
		if( inp.val() == '1'){
            $(this).closest('li').removeClass('item-enable_newdesign');
            inp.val(0);
        } else{
            if( inp.val() == 0) {
				$(this).closest('li').addClass('item-enable_newdesign');
				inp.val(1);
            }
        }
    })
	/* End Add Enable New design for screen */
	
    .on('click', '.disclose', function(){
        $(this).closest('li').toggleClass('mjs-nestedSortable-collapsed').toggleClass('mjs-nestedSortable-expanded');
    })
    .on('click', '.item-toggle-default', function(){
        var inp = $(this).closest('li').find('.item-display:first');
        if( inp.val() == '1'){
            $('.sortable li').removeClass('item-default');
            $('.item-default_screen').val(0);
            $(this).closest('li').addClass('item-default').find('.item-default_screen').val(1);
            $('.item-default ul').find('.item-default_screen').val(0);
        }
    })
    ;
	/* END Modified by Dai Huynh */
	
    // setup tree
    init();
    $('.sortable').nestedSortable({
        forcePlaceholderSize: true,
        handle: '.item-handle',
        helper: 'clone',
        items: 'li',
        opacity: .6,
        placeholder: 'placeholder',
        revert: 150,
        tolerance: 'pointer',
        toleranceElement: '> div',
        maxLevels: 3,
        listType: 'ul',
        isTree: true,
        expandOnHover: 700,
        startCollapsed: false,
        relocate: function(ev, info){
            var li = info.item;
            var parent = li.parent().parent();
            //update parent
            if( parent[0].nodeName == 'LI' ){
                li.find('.item-parent:first').val(parent.data('id'));
            } else {
                li.find('.item-parent:first').val('');
            }
            //update weight
            updateWeight();
            bindText(li.find('.item-text'));
        }
    });

    //add item
    var currentId = 1;
    $('#add-widget').click(function(){
        var weight = parseInt($('.sortable .item-weight:last').val());
        $('#widget-list input:checked').each(function(){
            var widget_id = $(this).val(),
                widget = widgets[widget_id];
            //add fields for widget
            console.log(widget);
            widget.id = 'new_' + currentId++;
            widget.widget_id = widget_id;
            widget.weight = ++weight;
            widget.parent_id = '';
            $('.sortable').append(toData(widget));
        });
        //reset widgets list
        $('#widget-list input:checkbox').prop('checked', false);
        //bind
		if($('#wd-container-footer').offset() != null){
			$('html,body').stop().animate({
				scrollTop: $('#wd-container-footer').offset().top
			}, 700);
		}
        init();
    });
    //save
    $('#save-menu').click(function(){
        //ajax form
        $('#zone').submit();
    });
    // $('#zone').ajaxForm({
    //     dataType: 'json',
    //     beforeSubmit: function(){
    //         $('#save-menu').addClass('saving').prop('disabled', true);
    //     },
    //     success: function(data){
    //         //replace
    //         $.each(data, function(id, realID){
    //             var li = $('li[data-id="' + id + '"]');
    //             li.data('id', realID);
    //         });
    //     },
    //     complete: function(){
    //         $('#save-menu').removeClass('saving').prop('disabled', false);
    //     }
    // });
});
</script>
