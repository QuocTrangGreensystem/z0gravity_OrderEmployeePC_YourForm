<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->script('history_filter'); ?>
<?php echo $html->css('slick_grid/slick.grid'); ?>
<?php echo $html->css('slick_grid/slick.pager'); ?>
<?php echo $html->css('slick_grid/slick.common'); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
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

<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
.slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
.slick-headerrow-columns {height: 36px !important;}
.guidelines h3 {
    font-size: 14px;
    padding: 10px 0;
}
.guidelines li {
    padding: 5px;
    border-bottom: 1px solid #eee;
}
.guidelines li b {
    display: inline-block;
    min-width: 100px;
}
.guidelines li i {
    display: inline-block;
    padding: 2px 5px;
    background: #f0f0f0;
}
</style>
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
                                <h3 class="wd-t3">&nbsp;</h3>
                                <div id="message-place">
                                    <?php
                                    App::import("vendor", "str_utility");
                                    $str_utility = new str_utility();
                                    echo $this->Session->flash();
                                    ?>
                                </div>
                                <div class="wd-table" id="project_container" style="width:100%;height:400px;">

                                </div>
                                <div id="pager" style="width:100%;height:36px; overflow: hidden;">

                                </div>
                                <div class="guidelines">
                                    <h3><?php __('Message tags') ?></h3>
                                    <ul>
                                        <li><b>{id}</b><span><?php __('Ticket ID, e.g <i>#12345</i>') ?></span></li>
                                        <li><b>{name}</b><span><?php __('Ticket name') ?></span></li>
                                        <li><b>{url}</b><span><?php __('Ticket name with url, e.g') ?> <a href="#">RIGHT OF THE MODITY A VIEW</a></span></li>
                                        <li><b>{status}</b><span><?php __('Status') ?></span></li>
                                        <li><b>{time}</b><span><?php __('Time when the ticket is updated, e.g') ?> <i><?php echo date('H:i, d-m-Y') ?></i></span></li>
                                        <li><b>{updater}</b><span><?php __('Resource who updated the ticket') ?></span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
        'id' => 'no.',
        'field' => 'no.',
        'name' => '#',
        'width' => 40,
        'sortable' => true,
        'resizable' => false,
        'noFilter' => 1,
    ),
    array(
        'id' => 'name',
        'field' => 'name',
        'name' => __('Status', true),
        'width' => 250,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox',
        'validator' => 'DataValidator.isUnique'
    ),
    array(
        'id' => 'ticket_profile_id',
        'field' => 'ticket_profile_id',
        'name' => __('Who can select the status', true),
        'width' => 220,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.SelectProfile',
        'formatter' => 'Slick.Formatters.SelectProfile',
        //'validator' => 'DateValidate.SelectProfile'
    ),
    array(
        'id' => 'acffected_to',
        'field' => 'acffected_to',
        'name' => __('Affected to', true),
        'width' => 220,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.mselectBox',
        'formatter' => 'Slick.Formatters.SelectAffected'
    ),
    array(
        'id' => 'message',
        'field' => 'message',
        'name' => __('Message', true),
        'width' => 300,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textArea'
    ),
    array(
        'id' => 'is_default',
        'field' => 'is_default',
        'name' => __('Default', true),
        'width' => 110,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'display',
        'field' => 'display',
        'name' => __('Display', true),
        'width' => 90,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'send_sms',
        'field' => 'send_sms',
        'name' => __('Send SMS', true),
        'width' => 90,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'weight',
        'field' => 'weight',
        'name' => __('Order', true),
        'width' => 60,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Order'
    ),
    array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __('Action', true),
        'width' => 70,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
        ));
$i = 1;
$dataView = array();
$selectMaps = array(
    'ticketProfiles' => $ticketProfiles,
    'display' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'send_sms' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'is_default' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    //'acffected_to' => array(__('Developer', true), __('Customer', true))
    'acffected_to' => array('customer' => __('Customer', true), 'developer' => __('Developer', true)),
);

App::import("vendor", "str_utility");
$str_utility = new str_utility();

foreach ($ticketStatuses as $ticketStatus) {
    $data = array(
        'id' => $ticketStatus['TicketStatus']['id'],
        'company_id' => $company_id,
        'no.' => $i++,
        'MetaData' => array(),
        'ticket_profile_id' => array(),
        'acffected_to' => array()
    );
    $data['name'] = (string) $ticketStatus['TicketStatus']['name'];
    $accfecteds = array();
    if(!empty($profileOfStatuses[$ticketStatus['TicketStatus']['id']])){
        foreach($profileOfStatuses[$ticketStatus['TicketStatus']['id']] as $profile_id){
            $data['ticket_profile_id'][] = $profile_id;
            //$rolePro = !empty($roleOfProfiles[$profile_id]) ? $roleOfProfiles[$profile_id] : '';
            //$accfecteds[$rolePro] = $rolePro;
        }
    } else {
        $data['ticket_profile_id'] = array();
    }
    if(!empty($ticketStatus['TicketStatus']['acffected_cus'])){
        $accfecteds[] = 'customer';
    }
    if(!empty($ticketStatus['TicketStatus']['acffected_dep'])){
        $accfecteds[] = 'developer';
    }
    $data['acffected_to'] = $accfecteds;
    $data['message'] = (string) $ticketStatus['TicketStatus']['message'];
    $data['weight'] = (int) $ticketStatus['TicketStatus']['weight'];
    $data['display'] = $ticketStatus['TicketStatus']['display'] ? 'yes' : 'no';
    $data['send_sms'] = !empty($ticketStatus['TicketStatus']['send_sms']) ? 'yes' : 'no';
    $data['is_default'] = $ticketStatus['TicketStatus']['is_default'] ? 'yes' : 'no';
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
            <a onclick="return confirm('<?php echo h(__('Delete?', true)); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<div id="order-template" style="display: none;">
    <div style="display: block;padding-top: 6px;" class="phase-order-handler overlay" rel="%s">
        <span class="wd-up" style="cursor: pointer;"></span>
        <span class="wd-down" style="cursor: pointer;"></span>
    </div>
</div>
<script type="text/javascript">
    var DataValidator = {};
    var wdTable = $('.wd-table');
    var heightTable = $(window).height() - wdTable.offset().top - 40;
    wdTable.css({
        height: heightTable,
    });
    $(window).resize(function(){
        location.reload();
        heightTable = $(window).height() - wdTable.offset().top - 240;
        wdTable.find('.slick-viewport').css({
            height: heightTable,
        });
    });

    (function($){
        
        $(function(){
            var $this = SlickGridCustom;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  true;
            var actionTemplate =  $('#action-template').html();
            var orderTemplate = $('#order-template').html();
            var roleOfProfiles = <?php echo !empty($roleOfProfiles) ? json_encode($roleOfProfiles) : json_encode(array());?>;
             DataValidator.isUnique = function(value,args){
                var result = true;
                $.each(args.grid.getData().getItems() , function(undefined,dx){
                    if(args.item.id && args.item.id == dx.id){
                        return true;
                    }
                    return (result = (dx.name.toLowerCase() != value.toLowerCase()));
                });
                return {
                    valid : result,
                    message : $this.t('The Status has already been exist.')
                };
            }
            var initMenuFilter = function($menu){
                var $filter = $('<div class="context-menu-filter"><span><input type="text" rel="no-history"></span></div>');
                $menu.before($filter);
        
                var timeoutID = null, searchHandler = function(){
                    var val = $(this).val();
                    var te = $($menu).find('tbody tr td.wd-employ-data div label').html();
                    
                    $($menu).find('tbody tr td.wd-employ-data div label').each(function(){
                        var $label = $(this).html();
                        $label = $label.toLowerCase();
                        val = val.toLowerCase();
                        if(!val.length || $label.indexOf(val) != -1 || !val){
                            //$(this).parent().css('display', 'block');
                            //$(this).parent().next().css('display', 'block');
                            $(this).parent().parent().parent().removeClass('wd-displays');
                        } else{
                            //$(this).parent().parent().parent().css('display', 'none');
                            //$(this).parent().next().css('display', 'none');
                            $(this).parent().parent().parent().addClass('wd-displays');
                        }
                    });
                };
        
                $filter.find('input').click(function(e){
                    e.stopImmediatePropagation();
                }).keyup(function(){
                    var self = this;
                    clearTimeout(timeoutID);
                    timeoutID = setTimeout(function(){
                        searchHandler.call(self);
                    } , 200);
                });
        
            };
            $.extend(Slick.Formatters,{
                SelectProfile : function(row, cell, value, columnDef, dataContext){
                    var _value = [];
                    if(value){
                        $.each(value, function(i,val){
                            if( $this.selectMaps['ticketProfiles'][val]){
                                _value.push( $this.selectMaps['ticketProfiles'][val]); 
                            }
                        });
                    }
                    return Slick.Formatters.HTMLData(row, cell, _value.join(', '), columnDef, dataContext);
                },
                SelectAffected : function(row, cell, value, columnDef, dataContext){
                    var _value = [];
                    if(value){
                        $.each(value, function(i,val){
                            if( $this.selectMaps['acffected_to'][val]){
                                _value.push( $this.selectMaps['acffected_to'][val]); 
                            }
                        });
                    }
                    return Slick.Formatters.HTMLData(row, cell, _value.join(', '), columnDef, dataContext);
                },                
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,
                    dataContext.company_id,dataContext.name), columnDef, dataContext);
                },
                Order : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(orderTemplate , row));
                }
            });
            $.extend(Slick.Editors,{
                SelectProfile : function(args){
                    var $options,hasChange = false,isCreated = false;
                    var defaultValue = [];
                    var scope = this;
                    var preload = function(){
                        // Get Ajax Employee
                        var getTicketProfile = function(){
                            if(scope.getValue(true) === true){
                                return;
                            }
                            scope.setValue(true);
                            $.ajax({
                                url : '<?php echo $html->url(array('action' => 'get_ticket_profile', $company_id)); ?>',
                                cache :  false,
                                type : 'GET',
                                success : function(data){
                                    $options.html(data);
                                    scope.input.html('');
                                    scope.setValue(defaultValue);
                                    scope.input.select();
                                    initMenuFilter($('.select-employees'));
                                    $options.find('.id-hander').each(function(){
                                        var $el =  $(this);
                                        $.each(defaultValue, function(undefined,v){
                                            if($el.val() == v){
                                                $el.prop('checked' , true);
                                                return false;
                                            }
                                        });
                                    }).click(function(){
                                        var list = [],c = args.column.id ,
                                        $hander = $(this).closest('tr').find('.id-hander');
                                        args.item.ticket_profile_id = {};
                                        //args.item.acffected_to = {};
                                        if(this.checked && !$hander.is(':checked')){
                                            $hander.prop('checked' , true);
                                        }
                                        $options.find('.id-hander:checked').each(function(){
                                            var $el =  $(this),id= $el.val(),pf = $el.attr('profile');
                                            list.push(id);
                                            args.item.ticket_profile_id[id] = pf;
                                            //var _rolePro = roleOfProfiles[id] ? roleOfProfiles[id] : '';
                                            //args.item.acffected_to[_rolePro] = _rolePro;
                                        });
                                        args.item[c] = list;
                                        hasChange = true;
                                        scope.setValue(list);
                                    });
                                }
                            });
                        };
                        getTicketProfile();
                    };
                    $.extend(this, new BaseSlickEditor(args));
                    
                    $options = $('<div class="multiSelectOptions" style="position: absolute; z-index: 99999; visibility: hidden;max-height:150px;"></div>').appendTo('body');
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
                            if(scope.input.width() < 320){
                                $options.width(320);
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
                            val = Slick.Formatters.SelectProfile(null,null,val, args.column ,args.item);
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

                    this.validate = function () {
                        var option = $this.fields[args.column.id] || {};
                        var result = {
            				valid: true,
            				message: typeof option.message != 'undefined' ? option.message : $this.t('This information is not blank!')
            			},val = this.getValue();
            			if(option.allowEmpty === false && !val.length && !this.isCreate){
            				result.valid = false;
            			}
            			if(result.valid && val.length){
            				if(option.maxLength && val.length > option.maxLength){
            					result = {
            						valid: false,
            						message: $this.t('Please enter must be no larger than %s characters long.' , option.maxLength)
            					};
            				}else if($.isFunction(args.column.validator)){
            					result = args.column.validator.call(this, val, args);
            				}
            			}
            			if(!result.valid && result.message){
            				this.tooltip(result.message , result.callback);
            			}
            			return result;
                    };

                    this.focus();
                }
            });
            $this.onBeforeEdit = function(args){
                //if(args.column.id == 'acffected_to'){
//                    return false;
//                }
                return true;
            }
            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                company_id : {defaulValue : '<?php echo $company_id; ?>', allowEmpty : false},
                name : {defaulValue : '' , allowEmpty : false, maxLength: 100},
                ticket_profile_id : {defaulValue : ''},
                message : {defaulValue : '', maxLength : 3000},
                weight : {defaulValue : 0 },
                display : {defaulValue : ''},
                send_sms : {defaulValue : 'no'},
                is_default : {defaulValue : ''},
                acffected_to : {defaulValue : ''}
            };
            $this.onCellChange = function(args){
                args.item.weight = args.item.weight ? args.item.weight : data.length;
                if(args.grid.getData().getItems()){
                    $.each(args.grid.getData().getItems(), function(index, val){
                        if(val.is_default === 'yes' && val.id != args.item.id){
                            var _rowParent = args.grid.getData().getRowById(val.id);
                            args.grid.getData().getItems()[_rowParent].is_default = 'no';
                            args.grid.updateRow(_rowParent);
                        }
                    });
                }
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            var dataGrid = $this.init($('#project_container'),data,columns);
            dataGrid.setSortColumn('weight' , true);
            $('[name="project_container.SortOrder"],[name="project_container.SortColumn"]').remove();
            var $colSort = $(dataGrid.getHeaderRow()).closest('.ui-widget').find('.slick-header-column-sorted .slick-column-name');
            var sortData = function(){
                $.each(dataGrid.getSortColumns() , function(){
                    this.sortAsc = !this.sortAsc;
                });
                $colSort.data('autoTrigger', true)
                $colSort.click();
            };
            $colSort.click(function(e){
                if(!$colSort.data('autoTrigger')){
                    e.stopImmediatePropagation();
                    return false;
                }
                $colSort.data('autoTrigger',false);
            });
            //
            (function(){
                
                if(!$this.canModified){
                    return;
                }
                var saveData = {},timeoutID;
                
                var updateSort = function(){
                    $.ajax({
                        url : '<?php echo $html->url(array('action' => 'order', $company_id)); ?>',
                        cache : false,
                        type : 'POST',
                        data : {
                            data : $.extend({},saveData)
                        }
                    });
                    saveData = {};
                };
                
                var getRow = function(row){
                    var $el = $(dataGrid.getCellNode(row, 1));
                    if($el.length){
                        return $el.parent();
                    }
                    return false;
                };
                
                var  toggleElement = function(s , d){
                    var sdata = dataGrid.getDataItem(s);
                    var ddata = dataGrid.getDataItem(d);

                    var w = ddata.weight;
                    ddata.weight = sdata.weight;
                    sdata.weight = w;

                    saveData[sdata.id] = sdata.weight;
                    saveData[ddata.id] = ddata.weight;
                    
                    clearTimeout(timeoutID);
                    timeoutID = setTimeout(updateSort , 1500);
                    sortData();
                    var $s = getRow(d);
                    $s.stop().animate({backgroundColor:'#CCFFCC'}, 'slow', '', function(){
                        $s.animate({backgroundColor:'#FFF'}, 'slow' , function(){
                            $s.css('backgroundColor' , '');
                        });
                    });
                };
                
                $('.phase-order-handler span.wd-up').live('click' , function(){
                    var row = Number($(this).parent().attr('rel'));
                    if (getRow(row - 1)) {
                        toggleElement(row,row - 1);
                    }
                });
                
                $('.phase-order-handler span.wd-down').live('click' , function(){
                    var row = Number($(this).parent().attr('rel'));
                    if (getRow(row + 1)) {
                        toggleElement(row,row + 1);
                    }
                });
                
            })();
        
        });
        
    })(jQuery);
</script>