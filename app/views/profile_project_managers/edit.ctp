<?php
echo $html->script(array(
    'jquery.mjs.nestedSortable',
    'jquery.ui.touch-punch.min',
    'jquery.form'
));
echo $html->css(array(
    'nested-sortable'
));
function _recursive($item){
    $html = '';
    $current = $item['ProfileProjectManagerDetail'];
    $widget_id = $current['widget_id'] . '-' . $current['id'];
    $class = 'list-item';
    $class .= $current['display'] ? '': ' item-hidden';
    $class .= $current['default_screen'] ? ' item-default' : '';
    $html .= '<li id="' . $widget_id . '" data-widget="' . $current['widget_id'] . '" data-id="' . $current['id'] . '" class="' . $class . '"><div class="item-handle">Drag</div><div class="item-expand"></div><div class="item-toggle-default" title="' . __('Default screen', true) . '"></div>';
    // read and write
    $html .= '<div class="item-toggle-read-write" title="' . __('Read and write', true) . '"><select name="data[ProfileProjectManagerDetail][' . $current['id'] . '][read_write]"><option value="0">'.__('Read', true).'</option><option '.(!empty($current['read_write']) && $current['read_write'] == 1 ? 'selected' : '').' value="1">'.__('Write', true).'</option></select></div>';
    //display
    $html .= '<div class="item-toggle-display" title="' . __('Display', true) . '"></div>';
    //meta data, inputs
    $html .= '<div class="item-content"><span class="disclose"><span></span></span><span class="item-title-eng">' . $current['name_eng'] . '</span> - <span class="item-title-fre">' . $current['name_fre'] . '</span></div>';
    $html .= '<div class="item-meta">';
    $html .= '<a href="javascript:;" onclick="remove_widget(this)" class="remove-widget">' . __('Delete', true) . '</a>';
    $html .= '<label>' . __('English', true) . '</label>';
    $html .= '<input type="text" class="item-text" data-lang="eng" name="data[ProfileProjectManagerDetail][' . $current['id'] . '][name_eng]" value="' . h($current['name_eng']) . '">';
    $html .= '<label>' . __('French', true) . '</label>';
    $html .= '<input type="text" class="item-text" data-lang="fre" name="data[ProfileProjectManagerDetail][' . $current['id'] . '][name_fre]" value="' . h($current['name_fre']) . '">';
    //hidden fields
    $html .= '<input type="hidden" class="item-parent" name="data[ProfileProjectManagerDetail][' . $current['id'] . '][parent_id]" value="' . $current['parent_id'] . '">';
    $html .= '<input type="hidden" class="item-default_screen" name="data[ProfileProjectManagerDetail][' . $current['id'] . '][default_screen]" value="' . $current['default_screen'] . '">';
    $html .= '<input type="hidden" class="item-weight" name="data[ProfileProjectManagerDetail][' . $current['id'] . '][weight]" value="' . $current['weight'] . '">';
    $html .= '<input type="hidden" name="data[ProfileProjectManagerDetail][' . $current['id'] . '][controllers]" value="' . $current['controllers'] . '">';
    $html .= '<input type="hidden" name="data[ProfileProjectManagerDetail][' . $current['id'] . '][functions]" value="' . $current['functions'] . '">';
    $html .= '<input type="hidden" name="data[ProfileProjectManagerDetail][' . $current['id'] . '][widget_id]" value="' . $current['widget_id'] . '">';
    $html .= '<input type="hidden" name="data[ProfileProjectManagerDetail][' . $current['id'] . '][model_id]" value="' . $current['model_id'] . '">';
    $html .= '<input type="hidden" name="data[ProfileProjectManagerDetail][' . $current['id'] . '][company_id]" value="' . $current['company_id'] . '">';
    $html .= '<input type="hidden" class="item-display" name="data[ProfileProjectManagerDetail][' . $current['id'] . '][display]" value="' . $current['display'] . '">';
    $html .= '<input type="hidden" class="item-delete" name="data[ProfileProjectManagerDetail][' . $current['id'] . '][delete]" value="0">';
    $html .= '</div>';
    //children recursive
    if( !empty($item['children']) ){
        $html .= '<ul>';
        foreach($item['children'] as $child){
            $html .= _recursive($child);
        }
        $html .= '</ul>';
    }
    $html .= '</li>';
    return $html;
}
function recursiveMenu($menu, $ul = true, $echo = true){
    $html = '';
    if( $ul )$html .= '<ul class="sortable">';
    foreach($menu as $item){
        $html .= _recursive($item);
    }
    if( $ul )$html .= '</ul>';
    if( $echo )echo $html;
    else return $html;
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
                                <div class="wd-title">
                                    <div style="width: 27%; float: left">
                                        <a href="<?php echo $this->Html->url('/profile_project_managers/') ?>" style="margin-bottom: 5px" class="btn btn-back"></a>
                                    </div>
                                    <div style="width: 60%; margin-left: 29%; margin-top: 10px">
                                        <h1 style="color: orange; float: left"> <?php echo $profileName ?></h1>
                                        <input <?php echo !empty($profile['ProfileProjectManager']['create_resource']) && $profile['ProfileProjectManager']['create_resource'] == 1 ? 'checked' : '' ?> class="input_set_permision" style="margin-left: 20px" type="checkbox" name="create_resource"><?php echo __('Create resources', true) ?>
                                        <input <?php echo !empty($profile['ProfileProjectManager']['can_create_project']) && $profile['ProfileProjectManager']['can_create_project'] == 1 ? 'checked' : '' ?> class="input_set_permision" type="checkbox" name="can_create_project"><?php echo __('Can create a project', true) ?>
                                        <input <?php echo !empty($profile['ProfileProjectManager']['can_delete_project']) && $profile['ProfileProjectManager']['can_delete_project'] == 1 ? 'checked' : '' ?> class="input_set_permision" type="checkbox" name="can_delete_project"><?php echo __('Can delete a project', true) ?>
                                        <input <?php echo !empty($profile['ProfileProjectManager']['can_change_status_project']) && $profile['ProfileProjectManager']['can_change_status_project'] == 1 ? 'checked' : '' ?> class="input_set_permision" type="checkbox" name="can_change_status_project"><?php echo __('May change the status of the opportunity/in progress', true) ?>
                                    </div>
                                </div>
                                <div id="message-place">
                                    <?php
                                    echo $this->Session->flash();
                                    ?>
                                </div>
                                <div id="widget-library" style="margin-top: 5px;">
                                    <h3>
                                        <button id="save-menu"><?php __('Save') ?></button>
                                        <span><?php __('List') ?></span>
                                        <button id="add-widget"><?php __('Add') ?> ➕</button>
                                    </h3>
                                    <ul id="widget-list">
                                    <?php foreach($widgets as $widget_id => $widget): ?>
                                        <li data-widget-item="<?php echo $widget_id ?>">
                                            <label for="<?php echo $widget_id ?>">
                                                <input type="checkbox" name="<?php echo $widget_id ?>" id="<?php echo $widget_id ?>" value="<?php echo $widget_id ?>">
                                                <span id="<?php echo $widget_id ?>-eng"><?php echo $widget['name_eng'] ?></span> - <span id="<?php echo $widget_id ?>-fre"><?php echo $widget['name_fre'] ?></span>
                                            </label>
                                        </li>
                                    <?php endforeach; ?>
                                    </ul>
                                </div>
                                <form id="zone" action="<?php echo $this->Html->url(array('action' => 'saveMenu', $model_id)) ?>" method="post">
                                    <?php recursiveMenu($menus) ?>
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
var model_id = <?php echo json_encode($model_id) ?>;
var template = <?php echo json_encode(recursiveMenu(array(array(
    'ProfileProjectManagerDetail' => array(
        'widget_id' => '{widget_id}',
        'name_eng' => '{name_eng}',
        'name_fre' => '{name_fre}',
        'display' => 1,
        'default_screen' => 0,
        'id' => '{id}',
        'model_id' => $model_id,
        'company_id' => $company_id,
        'functions' => '{functions}',
        'controllers' => '{controllers}',
        'weight' => '{weight}',
        'parent_id' => '{parent_id}'
    ),
    'children' => array()
    )), false, false)) ?>,
    widgets = <?php echo json_encode($widgets) ?>;

function remove_widget(el){
    var li = $(el).closest('li');
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
    var result = template;
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
    .on('click', '.item-toggle-display', function(){
        var inp = $(this).closest('li').find('.item-display:first');
        if( inp.val() == '1' ){
            $(this).closest('li').addClass('item-hidden');
            inp.val(0);
        } else {
            $(this).closest('li').removeClass('item-hidden');
            inp.val(1);
        }
    })
    .on('click', '.disclose', function(){
        $(this).closest('li').toggleClass('mjs-nestedSortable-collapsed').toggleClass('mjs-nestedSortable-expanded');
    })
    .on('click', '.item-toggle-default', function(){
        $('.sortable li').removeClass('item-default');
        $('.item-default_screen').val(0);
        $(this).closest('li').addClass('item-default').find('.item-default_screen').val(1);
    });
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
            widget.id = 'new_' + currentId++;
            widget.widget_id = widget_id;
            widget.weight = ++weight;
            widget.parent_id = '';
            $('.sortable').append(toData(widget));
        });
        //reset widgets list
        $('#widget-list input:checkbox').prop('checked', false);
        //bind
        $('html,body').stop().animate({
            scrollTop: $('#wd-container-footer').offset().top
        }, 700);
        init();
    });
    //save
    $('#save-menu').click(function(){
        //ajax form
        $('#zone').submit();
    });
    $(".input_set_permision").click(function(){
        var column = $(this).attr('name');
        var checked = $(this).attr('checked');
        checked = (checked !== undefined && checked == "checked") ? 1 : 0;
        $.ajax({
            url: '/profile_project_managers/savePermision/' + model_id,
            type: "POST",
            data: {
                column : column,
                checked : checked
            }
        });
    });
});
</script>
