<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <?php if($read == true):
                    if(!empty($getLeadStatus)){
                        $open = ($getLeadStatus == 1) ? 'selected="selected"' : '';
                        $won = ($getLeadStatus == 2) ? 'selected="selected"' : '';
                        $lost = ($getLeadStatus == 3) ? 'selected="selected"' : '';
                        $all = ($getLeadStatus == 4) ? 'selected="selected"' : '';
                    }
                ?>
                <div class="wd-title">
                    <a href="<?php echo $this->Html->url(array('action' => 'update', $company_id));?>" id="add-activity" class="btn btn-plus"><span></span></a>
                    <select style="margin-right:11px; width:18.8%% !important; padding: 6px; float: left;"  id="CategoryStatus">
                        <option value="0"><?php echo  __("--Select--", true);?></option>
                    </select>
                    <select style="margin-right:11px; width:8.8%% !important; padding: 6px; float: left;" id="CategoryCategory">
                        <option value="1" <?php echo isset($open) ? $open : '';?>><?php echo  __("Open", true)?></option>
                        <option value="2" <?php echo isset($won) ? $won : '';?>><?php echo  __("Closed Won", true)?></option>
                        <option value="3" <?php echo isset($lost) ? $lost : '';?>><?php echo  __("Closed Lost", true)?></option>
                        <option value="4" <?php echo isset($all) ? $all : '';?>><?php echo  __("All", true)?></option>
                    </select>
                </div>
                <?php endif;?>
                <div id="message-place">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    ?>
                </div>
                <div class="wd-table" id="project_container" style="width:100%;">

                </div>
                <div id="pager" style="width:100%;height:36px; overflow: hidden; margin-top: 30px;">

                </div>
                <?php //echo $this->element('grid_status'); ?>
            </div>
        </div>
    </div>
</div>
<?php
$columnAlignRight = array('goal', 'sales_price');
$columnAlignRightAndEuro = array('goal', 'sales_price');
$columnAlignRightAndManDay = array();
$columnNotCalculationConsumed = array(
    'id', 'no.', 'backup', 'salesman', 'MetaData', 'company_id', 'sale_customer_id',
    'status', 'name', 'code', 'order_number', 'customer_id', 'sale_customer_contact_id', 'deal_start_date',
    'deal_end_date', 'sale_setting_lead_maturite', 'sale_setting_lead_phase', 'description',
    'created', 'updated', 'update_by_employee', 'action.', 'leadLog'
);
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
$columns = array();
if(!empty($fieldset)){
    $columns[] = array(
        'id' => 'no.',
        'field' => 'no.',
        'name' => '#',
        'width' => 40,
        'sortable' => true,
        'resizable' => false,
        'noFilter' => 1,
    );
    foreach($fieldset as $field){
        $_column = array(
            'id' => $field['key'],
            'field' => $field['key'],
            'name' => __($field['name'], true),
            'width' => 150,
            'sortable' => true,
            'resizable' => true
        );
        if($field['key'] === 'salesman'){
            $_column['formatter'] = 'Slick.Formatters.Employee';
        }
        if($field['key'] === 'name'){
            $_column['formatter'] = 'Slick.Formatters.goToEditLead';
        }
        if($field['key'] === 'sales_price' || $field['key'] === 'goal'){
            $_column['formatter'] = 'Slick.Formatters.valSalesPrice';
        }
        $columns[] = $_column;
    }
    $columns[] = array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __('Action', true),
        'width' => 80,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
    );
}
$i = 1;
$dataView = array();
$totalHeaders = array();
$leadStatus = array('open' => 'Open', 'won' => 'Closed Won', 'lost' => 'Closed lost');
$selectMaps = array(
    'salesman' => !empty($employees) ? $employees : '',
    'company_id' => !empty($companies) ? $companies : '',
    'sale_customer_id' => !empty($saleCustomers) ? $saleCustomers : '',
    'status' => !empty($leadStatus) ? $leadStatus : '',
    'sale_setting_lead_phase' => !empty($saleSettings[6]) ? $saleSettings[6] : '',
    'sale_setting_lead_maturite' => !empty($maturites) ? $maturites : ''
);
App::import("vendor", "str_utility");
$str_utility = new str_utility();
if(!empty($saleLeads)){
    foreach($saleLeads as $idLead => $saleLead){
        $data = array(
            'id' => $saleLead['id'],
            'no.' => $i++,
            'backup' => array(),
            'salesman' => array(),
            'MetaData' => array()
        );
        if($saleLead['status'] == 0 || empty($saleLead['status'])){
            $saleLead['status'] = 'open';
        } elseif($saleLead['status'] == 1){
            $saleLead['status'] = 'won';
        } else {
            $saleLead['status'] = 'lost';
        }
        $data['company_id'] = (string) !empty($saleLead['company_id']) ? $saleLead['company_id'] : '';
        $data['sale_customer_id'] = (string) !empty($saleLead['sale_customer_id']) ? $saleLead['sale_customer_id'] : '';
        $data['status'] = (string) $saleLead['status'];
        $data['name'] = (string) $saleLead['name'];
        $data['code'] = (string) $saleLead['code'];
        $data['order_number'] = (string) $saleLead['order_number'];
        $data['customer_id'] = (string) $saleLead['customer_id'];
        $data['goal'] = (string) $saleLead['goal'];
        $data['sales_price'] = (string) $saleLead['sales_price'];
        $data['sale_customer_contact_id'] = (string) !empty($saleCustomerContacts[$saleLead['sale_customer_contact_id']]) ? $saleCustomerContacts[$saleLead['sale_customer_contact_id']] : '';
        $data['deal_start_date'] = (string) !empty($saleLead['deal_start_date']) ? date('d/m/Y', $saleLead['deal_start_date']) : '';
        $data['deal_end_date'] = (string) !empty($saleLead['deal_end_date']) ? date('d/m/Y', $saleLead['deal_end_date']) : '';
        $data['sale_setting_lead_maturite'] = (string) $saleLead['sale_setting_lead_maturite'];
        $data['sale_setting_lead_phase'] = (string) $saleLead['sale_setting_lead_phase'];
        $data['description'] = (string) $saleLead['description'];
        $data['created'] = (string) !empty($saleLead['created']) ? date('d/m/Y', $saleLead['created']) : '';
        $data['updated'] = (string) !empty($saleLead['updated']) ? date('d/m/Y', $saleLead['updated']) : '';
        $data['update_by_employee'] = (string) $saleLead['update_by_employee'];
        if(!empty($saleLeadEmployeeRefers[$saleLead['id']])){
            $_salesman = array();
            foreach($saleLeadEmployeeRefers[$saleLead['id']] as $employId => $backup){
                $data['salesman'][] = (string) $employId;
                $data['backup'][$employId] = !empty($backup) ? "1" : "0";
            }
        } else {
            $data['salesman'] = array();
        }
        $data['leadLog'] = !empty($saleLeadLogs[$saleLead['id']]) ? $saleLeadLogs[$saleLead['id']] : '';
        foreach($columns as $column){
            if(in_array($column['id'], $columnNotCalculationConsumed)){
                //do nothing
            } else {
                $val = $data[$column['id']] ? $data[$column['id']] : 0;
                $val = str_replace(' ', '', $val);
                $val = str_replace(',', '.', $val);
                if(!isset($totalHeaders[$column['id']])){
                    $totalHeaders[$column['id']] = 0;
                }
                $totalHeaders[$column['id']] += (float) $val;
            }
        }
        $data['action.'] = '';
        $dataView[] = $data;
    }
}
$viewManDay = __('M.D', true);
$i18n = array(
    'The Activity has already been exist.' => __('The Activity has already been exist.', true),
    'The date must be smaller than or equal to %s' => __('The date must be smaller than or equal to %s', true),
    'The date must be greater than or equal to %s' => __('The date must be greater than or equal to %s', true),
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true)
);
?>
<div id="action-template" style="display: none;">
    <a class="wd-edit" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'update', '%1$s', '%2$s')); ?>">Edit</a>
    <div class="wd-bt-big">
        <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
    </div>
</div>
<script type="text/javascript">
    var DataValidator = {};
    (function($){
        $(function(){
            var $this = SlickGridCustom,
            company_id = <?php echo json_encode($company_id);?>,
            saleCurrency = <?php echo json_encode($saleCurrency);?>,
            urlUpdateCustomer = <?php echo json_encode(urldecode($this->Html->link('%3$s', array('action' => 'update', '%1$s', '%2$s')))); ?>;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  true;
            var  viewManDay = <?php echo json_encode($viewManDay); ?>;
            // For validate date
            var actionTemplate =  $('#action-template').html();
            var backupText = {
                regex :  /<strong>\(B\)<\/strong>/,
                text : '<strong>(B)</strong>'
            };
            $.extend(Slick.Formatters,{
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate, company_id,
                    dataContext.id, dataContext.name), columnDef, dataContext);
                },
                goToEditLead : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(urlUpdateCustomer, company_id, dataContext.id, value), columnDef, dataContext);
                },
                Employee : function(row, cell, value, columnDef, dataContext){
                    var _value = [];
                    $.each(value, function(i,val){
                        _value.push($this.selectMaps['salesman'][val] + (dataContext.backup[val] == '1' ? backupText.text : '')); 
                    });
                    return Slick.Formatters.HTMLData(row, cell, _value.join(', '), columnDef, dataContext);
                },
                valSalesPrice : function(row, cell, value, columnDef, dataContext){
                    return '<span class="row-number">' + number_format(value, 2, '.', ' ') + ' ' + saleCurrency +'</span>';
                }
            });;
            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.columnAlignRightAndEuro = <?php echo json_encode($columnAlignRightAndEuro); ?>;
            $this.columnAlignRightAndManDay = <?php echo json_encode($columnAlignRightAndManDay); ?>;
            $this.columnNotCalculationConsumed = <?php echo json_encode($columnNotCalculationConsumed); ?>;
            var totalHeaders = <?php echo json_encode($totalHeaders);?>;
            $this.moduleAction = 'sale_lead_';
            $this.fields = {
                id : {defaulValue : 0}
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            ControlGrid = $this.init($('#project_container'),data,columns, {
                enableAddRow : false
            });
            $('.row-number').parent().addClass('row-number-custom');
            /**
             * Function Lay Personalized View Cua Status Lead
             */
            var personDefault = <?php echo json_encode($personDefault);?>;
            var checkStatus = <?php echo !empty($checkStatus) ? json_encode($checkStatus) : 1;?>;
            var getLeadStatus = <?php echo !empty($getLeadStatus) ? json_encode($getLeadStatus) : 1;?>;
            listPersonalizedViewOfLeadStautus = function(leadStatusValue, view_id){
                if(leadStatusValue != ''){
                    setTimeout(function(){
                        $.ajax({
                            url: '/sale_leads/getPersonalizedViews/' + leadStatusValue,
                            async: false,  
                            beforeSend: function(){
                                $('#CategoryStatus').html('Please waiting...');
                            },
                            success:function(datas) {
                                var datas = JSON.parse(datas);
                                var selected = selectDefined = selectDefault = '';
                                if(view_id != null){
                                    if(view_id == 0){
                                        selected = 'selected="selected"';
                                    } else if(view_id == -1){
                                        selectDefined = 'selected="selected"';
                                    } else if(view_id == -2){
                                        selectDefault = 'selected="selected"';
                                    }
                                }
                                var content = '<option value="0" ' + selected + '><?php echo  __("------- Select -------", true);?></option>';
                                if(personDefault == false){
                                    content += '<option value="-1" ' + selectDefined + '><?php echo  __("------- Predefined", true);?></option>';
                                } else {
                                    content += '<option value="-2" ' + selectDefault + '><?php echo  __("------- Default", true);?></option>';
                                }
                                $.each(datas, function(ind, val){
                                    var selected = '';
                                    if(view_id == ind && view_id != null && view_id != -2 && view_id != -1 && view_id != 0){
                                        selected = 'selected="selected"';
                                    }
                                    content += '<option value="' +ind+ '" ' + selected + '>' + val + '</option>';
                                });
                                $('#CategoryStatus').html(content);
                            }
                        });
                    } , 200);
                }
            }
            listPersonalizedViewOfLeadStautus(getLeadStatus, checkStatus);
            var lead_status = getLeadStatus ? getLeadStatus : 1;
            $('#CategoryCategory').change(function(){
                var _leadStatusValue = $(this).val();
                listPersonalizedViewOfLeadStautus(_leadStatusValue, 0);
                lead_status = _leadStatusValue;
            });
            $('#CategoryStatus').change(function(){
                var viewId = $(this).val();
                window.location = ('/sale_leads/index/' + company_id + '/' + viewId + '?lead_status=' +lead_status);
            });
            HistoryFilter.setVal = function(name, value){
                var $data = $("[name='"+name+"']").each(function(){
                    var $element = $(this);
                    if($element.is(':checkbox') || $element.is(':radio')){
                        if(!$.isArray(value)){
                            value = [value];
                        }
                        $element.prop('checked', $.inArray($element.val(), value) != -1);
                    }else{
                        $element.val(value);
                        $element.keypress();
                    }
                    $element.data('__auto_trigger' , true);
                    $element.change();
                });
                var _cols = ControlGrid.getColumns();
                var _numCols = cols.length;
                var _gridW = 0;
                for (var i=0; i<_numCols; i++) {
                    _gridW += _cols[i].width;
                }
                $('#wd-header-custom').css('width', _gridW);
                $('.row-number').parent().addClass('row-number-custom');
                return $data.length > 0;
            }
            /**
             * Calculation width of grid.
             */
            var cols = ControlGrid.getColumns();
            var numCols = cols.length;
            var gridW = 0;
            for (var i=0; i<numCols; i++) {
                gridW += cols[i].width;
            }
            ControlGrid.onScroll.subscribe(function(args, e, scope){
                $('.row-number').parent().addClass('row-number-custom');
            });
            ControlGrid.onSort.subscribe(function(args, e, scope){
                $('.row-number').parent().addClass('row-number-custom');
            });
            ControlGrid.onColumnsResized.subscribe(function (e, args) {
				var _cols = ControlGrid.getColumns();
                var _numCols = cols.length;
                var _gridW = 0;
                for (var i=0; i<_numCols; i++) {
                    _gridW += _cols[i].width;
                }
                $('#wd-header-custom').css('width', _gridW);
                $('.row-number').parent().addClass('row-number-custom');
			});
            if(columns){
                var headerConsumed = '<div id="wd-header-custom" class="slick-headerrow-columns" style="width: '+gridW+'px">';
                $.each(columns, function(_index, value){
                    var idOfHeader = 'sale_lead_' + value.id;
                    var valOfHeader = '';
                    if(totalHeaders && (totalHeaders[value.id] || totalHeaders[value.id] == 0)){
                        valOfHeader = totalHeaders[value.id];
                    }
                    if($.inArray(value.id, $this.columnAlignRightAndManDay) != -1){
                        valOfHeader = number_format(valOfHeader, 2, ',', ' ') + ' ' +viewManDay;
                    } else if($.inArray(value.id, $this.columnAlignRightAndEuro) != -1){
                        valOfHeader = number_format(valOfHeader, 2, ',', ' ') + ' ' + saleCurrency;
                    } else {
                        if(valOfHeader){
                            valOfHeader = number_format(valOfHeader, 2, ',', ' ');
                        }
                    }
                    idOfHeader = idOfHeader.replace('.', '_');
                    var left = 'l'+_index;
                    var right = 'r'+_index;
                    headerConsumed += '<div class="ui-state-default slick-headerrow-column wd-row-custom '+left+' '+right+'" id="'+idOfHeader+'"><p>'+valOfHeader+'</p></div>';
                });
                headerConsumed += '</div>';
                $('.slick-header-columns').after(headerConsumed);
            }
        });
    })(jQuery);
/**
 * Format Number
 */
function number_format(number, decimals, dec_point, thousands_sep) {
  // http://kevin.vanzonneveld.net
  // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +     bugfix by: Michael White (http://getsprink.com)
  // +     bugfix by: Benjamin Lupton
  // +     bugfix by: Allan Jensen (http://www.winternet.no)
  // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  // +     bugfix by: Howard Yeend
  // +    revised by: Luke Smith (http://lucassmith.name)
  // +     bugfix by: Diogo Resende
  // +     bugfix by: Rival
  // +      input by: Kheang Hok Chin (http://www.distantia.ca/)
  // +   improved by: davook
  // +   improved by: Brett Zamir (http://brett-zamir.me)
  // +      input by: Jay Klehr
  // +   improved by: Brett Zamir (http://brett-zamir.me)
  // +      input by: Amir Habibi (http://www.residence-mixte.com/)
  // +     bugfix by: Brett Zamir (http://brett-zamir.me)
  // +   improved by: Theriault
  // +      input by: Amirouche
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // *     example 1: number_format(1234.56);
  // *     returns 1: '1,235'
  // *     example 2: number_format(1234.56, 2, ',', ' ');
  // *     returns 2: '1 234,56'
  // *     example 3: number_format(1234.5678, 2, '.', '');
  // *     returns 3: '1234.57'
  // *     example 4: number_format(67, 2, ',', '.');
  // *     returns 4: '67,00'
  // *     example 5: number_format(1000);
  // *     returns 5: '1,000'
  // *     example 6: number_format(67.311, 2);
  // *     returns 6: '67.31'
  // *     example 7: number_format(1000.55, 1);
  // *     returns 7: '1,000.6'
  // *     example 8: number_format(67000, 5, ',', '.');
  // *     returns 8: '67.000,00000'
  // *     example 9: number_format(0.9, 0);
  // *     returns 9: '1'
  // *    example 10: number_format('1.20', 2);
  // *    returns 10: '1.20'
  // *    example 11: number_format('1.20', 4);
  // *    returns 11: '1.2000'
  // *    example 12: number_format('1.2000', 3);
  // *    returns 12: '1.200'
  // *    example 13: number_format('1 000,50', 2, '.', ' ');
  // *    returns 13: '100 050.00'
  // Strip all characters but numerical ones.
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
}
</script>
<?php 
    echo $html->css(array(
        'jquery.multiSelect',   
        'slick_grid/slick.grid_v2',
        'slick_grid/slick.pager',
        'slick_grid/slick.common_v2',
        'slick_grid/slick.edit',
        'audit'
    )); 
    echo $html->script(array(
        'history_filter',
        'jquery.multiSelect',
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
        'responsive_table.js'
    ));
    echo $this->element('dialog_projects');
?>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
.slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
</style>