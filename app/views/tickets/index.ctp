<?php
echo $this->Html->css(array(
    'jquery.multiSelect',
    'projects',
    'slick_grid/slick.grid_v2',
    'slick_grid/slick.pager',
    'slick_grid/slick.common_v2',
    'slick_grid/slick.edit',
    'gantt_v2_1',
    'slick_grid/slick.grid',
    'preview/grid-project',
    'preview/projects',
    'preview/slickgrid.css?ver=1.3',
    'multiple-select',
    'preview/project_widgets.css?ver=1.3'
));
echo $this->Html->script(array(
    'jquery.multiSelect',
    'jquery.scrollTo',
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
    'slick_grid/lib/jquery.event.drag-2.0.min',
    'slick_grid/slick.grid.origin',
    'multiple-select'
));

App::import("vendor", "str_utility");
$str_utility = new str_utility();

function jsonParseOptions($options, $safeKeys = array())
{
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
    .slick-header .slick-header-column {
        padding: 10px 5px !important;
        border-right: 1px solid #fff !important;
    }

    .slick-pane-top {
        top: 69px !important;
    }

    .slick-pane-right .slick-cell,
    .slick-pane-right .slick-headerrow-column {
        border-right-color: #aaa;
        border-left: 0;
    }

    .slick-header-columns-right .slick-header-column:nth-child(2n+1) {
        background: #09c !important;
    }

    .slick-headerrow-columns {
        height: 36px !important;
    }

    body {
        overflow: hidden;
    }

    #wd-container-main.wd-project-admin .wd-layout {
        padding: 0px;
    }

    body #layout {
        background: #f2f5f7;
        /* padding: 40px; */
    }
</style>
<style>
    #layout #wd-container-main .wd-layout {
        /* padding: 0; */
        background: #f2f5f7;
        overflow: visible;
    }

    .wd-main-content .wd-title select {
        height: 40px;
        width: 150px;
        border: 1px solid #E0E6E8;
        padding: 0 20px 0 10px;
        color: #666666;
        font-family: "Open Sans";
        font-size: 14px;
        line-height: 38px;
        -webkit-appearance: none;
        -moz-appearance: none;
        -ms-appearance: none;
        -o-appearance: none;
        appearance: none;
        background: url(/img/new-icon/down.png) no-repeat right 10px center #fff !important;
    }

    .wd-title select::-ms-expand {
        display: none;
    }
</style>
<!-- #2711 Apply Project List design in to Ticket List -->
<style>
    .wd-layout .wd-main-content {
        margin: auto;
    }

    .wd-layout>.wd-main-content>.wd-tab {
        margin: 0;
        padding: 15px;
    }

    .wd-layout .wd-main-content .wd-tab .wd-panel {
        margin: 16px auto;
        box-sizing: border-box;
        border-color: #fff;
    }

    .wd-layout .wd-main-content .wd-tab .wd-panel:first-child {
        margin-top: 0;
    }

    .projects-dashboard-container {
        margin-top: 15px !important;
        margin: auto;
    }

    .projects-dashboard-container,
    .wd-title {
        max-width: 100%;
        margin: 0;
    }

    .wd-table .slick-header.ui-state-default {
        width: 100%;
    }

    .wd-main-content .wd-project-filter {
        position: relative;
        top: 0;
    }

    @media (min-width: 992px) {
        .wd-main-content .wd-project-filter {
            left: 240px;
        }
    }

    #wd-container-main.wd-project-admin.active {
        padding: 0;
    }

    #sub-nav {
        display: none;
    }

    .search-filter {
        width: calc(100vw - 50px);
        margin-left: 25px;
    }

    .wd-table .slick-viewport .slick-cell .circle-name {
        width: 30px;
        height: 30px;
        line-height: 30px;
        font-size: 14px;
        vertical-align: middle;
    }

    .slick-cell .circle-name {
        position: relative;
        background-color: #E4AF63;
    }

    .slick-row:nth-child(2n) .slick-cell .circle-name {
        background-color: #67BD65;
    }

    .slick-row:nth-child(3n) .slick-cell .circle-name {
        background-color: #6DAAD3;
    }

    .slick-row:nth-child(4n) .slick-cell .circle-name {
        background-color: #2858B1;
    }

    .circle-name,
    #add-employee {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        background-color: #72ADD2;
        color: #fff;
        text-transform: uppercase;
        font-size: 16px;
        text-align: center;
        line-height: 40px;
        font-weight: bold;
        display: inline-block;
        vertical-align: top;
    }

    .circle-name a,
    #add-employee a,
    .circle-name span {
        color: #fff;
        font-weight: 600;
    }

    .circle-name a:hover,
    #add-employee a:hover {
        text-decoration: none;
    }

    .wd-table .slick-pane-header.slick-pane-right {
        background: transparent;
    }

    .wd-table .slick-cell .gantt-ms i {
        position: relative;
        overflow: inherit;
    }

    .wd-table .slick-cell .gantt-ms i:after {
        content: '';
        position: absolute;
        width: calc(100% + 4px);
        height: calc(100% + 4px);
        border: 1px solid #BCBCBC;
        display: block;
        left: -3px;
        top: -3px;
        border-radius: 50%;
    }
</style>
<style>
    .add-project {
        margin-left: 30px;
    }

    .add-project a {
        height: 50px;
        width: 50px;
        background-color: #5487FF;
        box-shadow: 0 0 10px 1px rgba(29, 29, 27, 0.2);
        border-radius: 50%;
        display: block;
        text-align: center;
        line-height: 50px;
        position: absolute;
        z-index: 21;
        -webkit-transition: all 0.2s;
        -moz-transition: all 0.2s;
        -o-transition: all 0.2s;
        transition: all 0.2s;
        top: -25px;
        right: 30px;
    }

    .add-project a:hover {
        -ms-transform: scale(1.05);
        /* IE 9 */
        -webkit-transform: scale(1.05);
        /* Safari 3-8 */
        transform: scale(1.05);
    }

    .add-project a.active {
        -ms-transform: rotate(45deg) scale(1.05);
        /* IE 9 */
        -webkit-transform: rotate(45deg) scale(1.05);
        /* Safari 3-8 */
        transform: rotate(45deg) scale(1.05);

    }

    .add-project a:before {
        content: '';
        width: 0px;
        height: 100%;
        display: inline-block;
        vertical-align: middle;
    }

    .add-project a img {
        display: inline-block;
        vertical-align: middle;
        margin-top: -1px;
    }

    #addProjectTemplate.open.loading {
        position: fixed;
        width: 100vw;
        height: 100vh;
        z-index: 99;
    }

    ::placeholder {
        /* Chrome, Firefox, Opera, Safari 10.1+ */
        color: #C6CCCF;
        opacity: 1;
        /* Firefox */
        font-size: 14px;
    }

    :-ms-input-placeholder {
        /* Internet Explorer 10-11 */
        color: #C6CCCF;
        font-size: 14px;
    }

    ::-ms-input-placeholder {
        /* Microsoft Edge */
        color: #C6CCCF;
        font-size: 14px;
    }

    .wd-add-project .btn-submit {
        height: 48px;
        text-align: center;
        line-height: 48px;
        background-color: #5487FF;
        border-radius: 5px;
        text-transform: uppercase;
        display: block;
        font-size: 14px;
        width: calc(100% - 25px);
        color: #fff;
    }

    .wd-add-project .btn-submit:hover {
        text-decoration: none;
    }

    #addProjectTemplate .ui-datepicker-trigger {
        vertical-align: top;
        padding: 0;
    }

    #addProjectTemplate .ui-datepicker-trigger {
        position: relative;
        top: -32px;
        left: 90%;
        vertical-align: top;
        cursor: pointer;
    }

    /* IE 10+ */
    @media screen and (-ms-high-contrast: active),
    (-ms-high-contrast: none) {
        #addProjectTemplate .ui-datepicker-trigger {
            left: 85%;
        }
    }

    #addProjectTemplate #end_date,
    #addProjectTemplate #start_date {
        margin-bottom: 0;
    }

    #addProjectTemplate #price_6,
    #addProjectTemplate #price_7 {
        background: url(../../img/new-icon/question-dark.jpg) no-repeat 96% center #fff;
    }

    select::-ms-expand {
        display: none;
    }

    @media(max-width: 1199px) {

        .open-filter-form {
            margin-top: 10px;
        }

        body .wd-project-admin.active {
            padding-top: 0;
        }

        .search-filter {
            padding-bottom: 10px;
        }
    }

    @media(max-width: 480px) {
        #addProjectTemplate.open {
            padding-left: 20px;
            padding-right: 20px;
        }
    }

    .project-field .multiselect {
        position: relative;
        border-right: none;
    }

    .project-field .multiselect>a {
        min-height: 40px;
        line-height: 40px;
        border: 1px solid #E1E6E8;
        background-color: #FFFFFF;
        background: linear-gradient(270deg, #FFFFFF 0%, #F9F9F9 100%);
        box-shadow: 0 0 10px 1px rgba(29, 29, 27, 0.06);
        padding-left: 20px;
        margin-bottom: 20px;
        font-size: 13px;
        border-radius: 2px;
        width: calc(100% - 20px);
        display: block;
        background: url(../../img/new-icon/down.png) no-repeat 96% center #fff;
        height: inherit;
        color: #C6CCCF;
    }


    .project-field .menu-filter {
        width: 96% !important;
        top: 100%;
        padding: 0;
        display: none;
        z-index: 3 !important;
        margin-left: 5px;
        margin-top: 5px;
    }

    .project-field .menu-filter span {
        display: block;
        background: url(/css/images/search_label.gif) no-repeat 2px center;
        padding-left: 17px;
        background-color: #fff;
        border: 1px solid #ddd;
    }

    .project-field .menu-filter span input {
        border: none;
    }

    .project-field label {
        display: none;
    }

    #addProjectTemplate #add-form {
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 20px;
        margin-bottom: 20px;
    }

    #add-form .project-field {
        margin-right: 3px;
    }

    #add-form .project-field .wd-combobox {
        padding-top: 0;
        padding-bottom: 0;
    }

    .list_multiselect {
        position: absolute;
        top: 100%;
        width: 100%;
        background-color: #fff;
        border: 1px solid #ddd;
        z-index: 2;
    }

    .progress-circle-text>i {
        width: 0px;
        height: 16px;
        background: url(../../img/ajax-loader.gif) no-repeat 2px center;
        display: none;
        transition: all 0.4s ease;
        position: relative;
        top: 0;
        right: 0;
        margin-left: 6px;
    }

    .progress-circle-text.loading i {
        display: inline-block;
        width: 16px;

    }

    #addProjectTemplate ::-webkit-scrollbar {
        width: 4px;
        height: 4px;
        cursor: pointer;
    }

    /* Track */
    #addProjectTemplate ::-webkit-scrollbar-track {
        box-shadow: inset 0 0 5px #F2F5F7;
        border-radius: 4px;
        background: #F2F5F7;
        cursor: pointer;
    }

    /* Handle */
    #addProjectTemplate ::-webkit-scrollbar-thumb {
        background: #5487FF;
        border-radius: 4px;
        cursor: pointer;
    }

    ::-ms-clear {
        display: none;
    }

    /*  MULTISELECT */

    .wd-multiselect.multiselect .circle-name {
        height: 30px;
        width: 30px;
        line-height: 30px;
        vertical-align: middle;
    }

    .wd-multiselect.multiselect .circle-name img {
        border-radius: 50%;
    }

    .wd-multiselect.multiselect .circle-name span {
        font-size: 14px;
        font-weight: 600;
        color: #fff;
        line-height: 30px;
        display: block;
    }

    .wd-multiselect.multiselect {
        font-size: 14px;
        line-height: 40px;
    }

    .wd-multiselect.multiselect a.wd-combobox {
        padding-left: 10px;
        width: calc(100% - 10px);
        line-height: 37px;
        font-weight: 400;
    }

    .wd-multiselect.multiselect .wd-combobox .circle-name:not(:last-child) {
        margin-right: 5px;
    }

    .wd-multiselect.multiselect .wd-combobox-content {
        position: absolute;
        top: 100%;
        width: calc(100% - 20px);
        z-index: 2;
        overflow: auto;
        box-shadow: 0 0 10px 1px rgba(29, 29, 27, 0.06);
        border: 1px solid #E1E6E8;
        border-top: none;
        background-color: #fff;
        -webkit-transition: all 0.4s;
        -moz-transition: all 0.4s;
        -o-transition: all 0.4s;
        transition: all 0.4s;
        padding: 10px;
    }

    .wd-multiselect.multiselect .wd-combobox-content .wd-data {
        height: 40px;
    }

    .wd-multiselect.multiselect .wd-combobox-content .option-name {
        color: #424242;
        font-size: 14px;
    }

    .wd-multiselect.multiselect .wd-combobox-content .context-menu-filter {
        margin: 0;
        padding: 0;
        border: none;
        width: calc(100% - 20px);
        border: 1px solid #E1E6E8;
        background-color: #FFFFFF;
        box-shadow: 0 0 10px 1px rgba(29, 29, 27, 0.06);
        position: absolute;
        z-index: 2;
        border-radius: 3px;
    }

    .wd-multiselect.multiselect .wd-combobox-content .context-menu-filter span {
        background: url(/img/new-icon/search.png) no-repeat 96% center;
        border: none;
    }

    .wd-multiselect.multiselect .wd-combobox-content .context-menu-filter input {
        height: 40px;
        width: 100%;
    }

    .wd-multiselect .wd-combobox-content .option-content {
        height: 160px;
        overflow: auto;
        margin-top: 45px;
    }

    .wd-multiselect .wd-combobox-content .option-content .wd-data-manager {
        cursor: pointer;
    }

    .wd-multiselect .wd-combobox-content .option-content .wd-data-manager input[type='checkbox'] {
        display: none;
    }

    @media(max-width: 767px) {
        .header-bottom .wd-layout-heading ul {
            display: block;
            width: 100%;
        }

        .header-bottom .wd-layout-heading .project-progress {
            display: block;
            width: 100%;
        }

        .wd-layout-heading .project-progress>span {
            text-align: left;
        }
    }

    .log-progress .project-progress {
        display: block;
        position: relative;
        width: 250px;
        text-align: left;
    }

    .project-progress .progress-full {
        display: inline-flex;
        justify-content: space-between;
        background-color: transparent;
    }

    .project-progress .progress-full {
        width: 180px;
        height: 6px;
    }

    .wd-layout-heading .progress-full {
        position: relative;
    }

    .project-progress .progress-node {
        width: calc(10% - 2px);
        margin: 0 1px;
        height: 100%;
        border-radius: 3px;
    }

    .wd-layout-heading .progress-full {
        float: left;
    }

    .wd-sumrow.btn {
        width: 32px;
    }

    .wd-title .btn i.html_entity {
        vertical-align: top;
        font-size: 19px;
        line-height: 29px;
        font-weight: 300;
    }

    .btn.active i {
        color: #5487ff;
    }

    .slick-row .row-number {
        float: right;
    }

    .wd-title .btn-text span {
        display: none;
    }

    #layout {
        background: #f2f5f7;
    }

    #filter_alert:hover {
        background-color: #FFF9F9;
    }

    #filter_alert.active {
        border-color: #E94754;
    }

    #filter_alert:before {
        content: '';
        width: 16px;
        height: 16px;
        background: #E94754;
        position: relative;
        border-radius: 3px;
        display: block;
        top: calc(50% - 8px);
        left: calc(50% - 8px);
    }

    .wd-row-custom p {
        text-align: left;
        padding-left: 10px;
        padding-right: 0px;
    }

    .slick-headerrow-columns .slick-headerrow-column {
        border: none;
        border-right: 1px solid #E9E9E9;
    }

    .slick-viewport .slick-cell a.project-favorite-action {
        text-align: center;
        width: 30px;
        height: 30px;
        padding: 6px;
        vertical-align: middle;
        display: inline-block;
        border: 1px solid #ddd;
        border-radius: 50%;
        position: relative;
        box-sizing: border-box;
        position: relative;
        background-color: #fff;
        background-position: 6px center;
    }

    .slick-viewport .slick-cell a.project-favorite-action svg {
        vertical-align: top;
    }

    .slick-viewport .slick-cell a.project-favorite-action.loading svg {
        opacity: 0.5;
    }

    .content-right-inner,
    .wd-layout>.wd-main-content>.wd-tab .wd-panel {
        padding: 15px;
        background: #fff;
        width: 100%;
        max-width: 1920px;
    }

    .wd-title .wd-right {
        width: auto;
    }

    .ui-datepicker select.ui-datepicker-month,
    .ui-datepicker select.ui-datepicker-year {
        width: 70px;
    }

    .ui-datepicker .ui-datepicker-title {
        line-height: 2.8em;
    }

    .ui-datepicker .ui-datepicker-header .ui-datepicker-prev,
    .ui-datepicker .ui-datepicker-header .ui-datepicker-next {
        top: 2px;
    }

    .ui-widget-content .ui-state-hover {
        background: inherit;
    }

    .ui-datepicker table td.ui-datepicker-today a {
        background-color: #91BFE1;
        color: #fff;
    }
</style>
<!-- End -->
<script type="text/javascript">
    HistoryFilter.here = '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url = '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<?php
$columns = array(
    array(
        'id' => 'no.',
        'field' => 'id',
        'name' => __('ID', true),
        'width' => 60,
        'datatype' => 'number',
        'sortable' => true,
        'resizable' => false,
        'noFilter' => 1
    )
);
foreach ($fieldsets as $key => $fieldset) {
    if (($profile['role'] == 'customer') && ($fieldset['key'] == 'delivery_date')) continue;
    if ($fieldset['key'] == 'name') {
        $columns[] = array(
            'id' => $fieldset['key'],
            'field' => $fieldset['key'],
            'name' => __($fieldset['name'], true),
            'width' => 150,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.linkFormatter'
        );
    } else if ($fieldset['key'] == 'ticket_status_id') {
        $columns[] = array(
            'id' => $fieldset['key'],
            'field' => $fieldset['key'],
            'name' => __($fieldset['name'], true),
            'width' => 200,
            'sortable' => true,
            'resizable' => true,
            'editor' => 'Slick.Editors.singleSelect'
        );
    } else if ($fieldset['key'] == 'delivery_date' || $fieldset['key'] == 'created' || $fieldset['key'] == 'updated') {
        $columns[] = array(
            'id' => $fieldset['key'],
            'field' => $fieldset['key'],
            'name' => __($fieldset['name'], true),
            'width' => 150,
            'sortable' => true,
            'formatter' => 'Slick.Formatters.vndate',
            'resizable' => true
        );
    } else {
        $columns[] = array(
            'id' => $fieldset['key'],
            'field' => $fieldset['key'],
            'name' => __($fieldset['name'], true),
            'width' => 150,
            'sortable' => true,
            'resizable' => true
        );
    }
}
if (!empty($profile) && $profile['role'] == 'developer') {
    $columns[] = array(
        'id' => 'edit_spec',
        'field' => 'edit_spec',
        'name' => __('Spec', true),
        'width' => 40,
        'sortable' => false,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.EditAction',
        'ignoreExport' => true,
        'noFilter' => 1
    );
    $columns[] = array(
        'id' => 'edit_doc',
        'field' => 'edit_doc',
        'name' => __('Doc', true),
        'width' => 40,
        'sortable' => false,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.EditAction',
        'ignoreExport' => true,
        'noFilter' => 1
    );
    $columns[] = array(
        'id' => 'edit_note',
        'field' => 'edit_note',
        'name' => __('Note', true),
        'width' => 40,
        'sortable' => false,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.EditAction',
        'ignoreExport' => true,
        'noFilter' => 1
    );
    $columns[] = array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __('Action', true),
        'width' => 70,
        'sortable' => false,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.Action',
        'ignoreExport' => true,
        'noFilter' => 1
    );
}
$i = 1;
$dataView = array();
$companies = array('0' . $company_id => $employee_info['Company']['company_name']);
foreach ($external_companies as $id => $name) {
    $companies['1' . $id] = $name; // . ' (' . __('External', true) . ')';
}
$selectMaps = array(
    'type_id' => !empty($ticketMetas['type']) ? $ticketMetas['type'] : array(),
    'priority_id' => !empty($ticketMetas['priority']) ? $ticketMetas['priority'] : array(),
    'function_id' => !empty($ticketMetas['function']) ? $ticketMetas['function'] : array(),
    'version_id' => !empty($ticketMetas['version']) ? $ticketMetas['version'] : array(),
    'ticket_status_id' => !empty($ticketStatuses) ? $ticketStatuses : array(),
    'employee_id' => !empty($resources) ? $resources : array(),
    'employee_updated_id' => !empty($resources) ? $resources : array(),
    'company_id' => $companies
);
$i18n = array();
foreach ($tickets as $ticket) {
    $data = array(
        'id' => $ticket['Ticket']['id'],
        'no.' => $ticket['Ticket']['id'],
        'DataSet' => array()
    );
    $data['name'] = (string) $ticket['Ticket']['name'];
    $data['company_id'] = ($ticket['Ticket']['company_model'] == 'Company' ? '0' : '1') . $ticket['Ticket']['company_id'];
    $data['type_id'] = (int) $ticket['Ticket']['type_id'];
    $data['priority_id'] = (int) $ticket['Ticket']['priority_id'];
    $data['function_id'] = (int) $ticket['Ticket']['function_id'];
    $data['version_id'] = (int) $ticket['Ticket']['version_id'];
    $data['employee_id'] = $ticket['Ticket']['employee_id'];
    $data['employee_updated_id'] = $ticket['Ticket']['employee_updated_id'];

    $data['delivery_date'] = $ticket['Ticket']['delivery_date'] && $ticket['Ticket']['delivery_date'] != '0000-00-00' ? $this->Time->format('Y-m-d', $ticket['Ticket']['delivery_date']) : '';
    //$data['open_date'] = $ticket['Ticket']['open_date'] && $ticket['Ticket']['open_date'] != '0000-00-00' ? $this->Time->format('d-m-Y', $ticket['Ticket']['open_date']) : '';
    //$data['content'] = (string) $ticket['Ticket']['content'];
    $data['created'] = $this->Time->format('Y-m-d', $ticket['Ticket']['created']);
    $data['updated'] = $this->Time->format('Y-m-d', $ticket['Ticket']['updated']);
    $data['affections'] = !empty($affections[$ticket['Ticket']['ticket_status_id']]) ? $affections[$ticket['Ticket']['ticket_status_id']] : '';
    //$data['updated'] = (string) $ticket['Ticket']['updated'];
    $data['ticket_status_id'] = (int) $ticket['Ticket']['ticket_status_id'];

    $data['can_update'] = $profile['role'] == 'developer' || $profile['can_update'] == 2 || ($profile['can_update'] == 1 && $data['employee_id'] == $employee_info['Employee']['id']);

    $dataView[] = $data;
}
?>
<?php
$container_width = 0;
if (!empty($filter_render)) {
    foreach ($columns as $key => $vals) {
        $field_resize = $vals['field'] . '.Resize';
        if (!empty($filter_render[$field_resize])) {
            $columns[$key]['width'] = intval($filter_render[$field_resize]);
        }
    }
}
foreach ($columns as $key => $vals) {
    $container_width += $vals['width'];
}
?>
<div id="action-template" style="display: none;">
    <a class="wd-edit" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'view', '%1$s')); ?>"></a>
    <div class="wd-bt-big">
        <a onclick="return confirm('<?php echo h(__('Delete?', true)); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s')); ?>"><?php __('Delete'); ?></a>
    </div>
</div>
<div id="action-template-edit" style="display: none;">
    <a class="wd-edit" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'view', '%1$s', '%2$s')); ?>"></a>
</div>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-tab">
                <div class="wd-panel">
                    <div class="wd-title">
                        <select style="margin-right:5px; float: none" class="wd-customs" id="CategoryStatus" rel="no-history">
                            <option value="-1" <?php echo ($selectTicket == -1) ? 'selected="selected"' : ''; ?>><?php echo  __("--Default--", true); ?></option>
                            <?php
                            if (!empty($userViews)) {
                                foreach ($userViews as $key => $name) {
                                    $sl = ($selectTicket == $key) ? 'selected="selected"' : '';
                            ?>
                                    <option value="<?php echo $key; ?>" <?php echo $sl; ?>><?php echo $name; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                        <?php if ($can_create) : ?>
                            <a href="<?php echo $this->Html->url(array('action' => 'add')); ?>" class="btn btn-plus"><span></span></a>
                            <a href="<?php echo $this->Html->url(array('action' => 'export')); ?>" id="export-table" class="btn btn-excel"><span></span></a>
                        <?php endif ?>
                        <a href="javascript:void(0);" class="btn btn-text reset-filter hidden" id="reset-filter" title="<?php __('Delete the filter') ?>">
                            <i class="icon-refresh"></i>
                        </a>
                    </div>
                </div>

                <div class='ticket__alerts loading-mark' id="ticket__alerts">
                    <?php
                    if ($isCustomer && $haveLimitTicket) {
                        if ($limitForCustomer['support']['isLimit']) {
                            $content = sprintf(__('%s ticket(s) utilisé(s)  sur %s  - Fin de période %s', true), $limitForCustomer['support']['num'], $limitForCustomer['support']['limit'], $limitForCustomer['support']['period']);
                            echo $this->element('ticket_alert_box', array(
                                "current" => $limitForCustomer['support']['num'],
                                "limit" => $limitForCustomer['support']['limit'],
                                "content_title" => __('alert_ticket_title_support', true),
                                "content_description" => $content
                            ));
                        }
                        if ($limitForCustomer['formation']['isLimit']) {
                            $content = sprintf(__('%s ticket(s) utilisé(s)  sur %s  - Fin de période %s', true), $limitForCustomer['formation']['num'], $limitForCustomer['formation']['limit'], $limitForCustomer['formation']['period']);
                            echo $this->element('ticket_alert_box', array(
                                "current" => $limitForCustomer['formation']['num'],
                                "limit" => $limitForCustomer['formation']['limit'],
                                "content_title" => __('alert_ticket_title_formation', true),
                                "content_description" => $content
                            ));
                        }
                        if ($limitForCustomer['coaching']['isLimit']) {
                            $content = sprintf(__('%s ticket(s) utilisé(s)  sur %s  - Fin de période %s', true), $limitForCustomer['coaching']['num'], $limitForCustomer['coaching']['limit'], $limitForCustomer['coaching']['period']);
                            echo $this->element('ticket_alert_box', array(
                                "current" => $limitForCustomer['coaching']['num'],
                                "limit" => $limitForCustomer['coaching']['limit'],
                                "content_title" => __('alert_ticket_title_coaching', true),
                                "content_description" => $content
                            ));
                        }
                    } ?>
                </div>

                <div class="wd-panel">
                    <div class="wd-table project-list" id="project_container" style="width: 100%; height: 600px;">
                    </div>
                    <!-- div id="pager" style="clear:both;width:100%;height:36px;"></div -->
                </div>
            </div>
        </div>
    </div>
    <style>
        .btn-text i {
            line-height: 38px;
        }

        .wd-bt-big,
        a.wd-edit {
            margin-top: 10px;
        }

        .loading {
            height: 70px;
        }

        .ticket__alerts {
            display: block;
            overflow: auto;
            white-space: nowrap;
            width: 100%;
            max-width: 1920px;
            margin: 16px auto;
        }

        .ticket--component {
            display: inline-block;
            padding: 16px;
            border: grey solid 1px;
            border-radius: 5px;
        }

        .ticket--component+.ticket--component {
            margin-left: 16px;
        }

        .alerts__box-ok {
            border-color: #BCD8ED;
            background-color: #fff;
        }

        .alerts__box-warning {
            border-color: #EE8845;
            background-color: #fff;
        }

        .alerts__box-error {
            border-color: #F05352;
            background-color: #fff;
        }

        .ticket--content__title {
            text-transform: uppercase;
            color: #7B7B7B;
        }

        .ticket--content__desc {
            color: #242424;
        }
    </style>
    <script type="text/javascript">
        var DataValidator = {},
            profile = <?php echo json_encode($profile) ?>,
            showLimitInfo = <?php echo (!$isCustomer || $haveLimitTicket) ? true : false ?>;
        var wdTable = $('.wd-table');
        $(window).resize(function() {
            update_table_height();
        });

        function update_table_height() {
            var heightTable = $(window).height() - wdTable.offset().top - 80;
            wdTable.height(heightTable);
            var heightViewPort = heightTable - wdTable.find('.slick-header:first').height() - wdTable.find('.slick-headerrow:first').height() - 1;
            // wdTable.find('.slick-viewport').height(heightViewPort);
            if (SlickGridCustom.getInstance()) SlickGridCustom.getInstance().resizeCanvas();
        }
        update_table_height();

        function history_reset() {
            var check = false;
            $('.multiselect-filter').each(function(val, ind) {
                var text = '';
                if ($(ind).find('input').length != 0) {
                    text = $(ind).find('input').val();
                } else {
                    text = $(ind).find('span').html();
                    if (text == "<?php __('-- Any --'); ?>" || text == '-- Any --') {
                        text = '';
                    }
                }
                if (text != '') {
                    check = true;
                }
            });
            if (!check) {
                $('#reset-filter').addClass('hidden');
            } else {
                $('#reset-filter').removeClass('hidden');
            }
        }
        (function($) {
            $(function() {
                var $this = SlickGridCustom;
                $this.url = '<?php echo $this->Html->url(array('action' => 'update_status')); ?>';
                $this.i18n = <?php echo json_encode($i18n); ?>;
                $this.canModified = true;

                $this.singleSelectable = <?php echo json_encode($visible_statuses) ?>;
                $this.fields = {
                    id: {
                        defaulValue: 0
                    },
                    ticket_status_id: {
                        defaultValue: 0
                    }
                };
                var actionTemplate = $('#action-template').html();
                var actionTemplateEdit = $('#action-template-edit').html();

                $.extend(Slick.Formatters, {
                    Action: function(row, cell, value, columnDef, dataContext) {
                        return Slick.Formatters.HTMLData(row, cell, $this.t(actionTemplate, dataContext.id), columnDef, dataContext);
                    },
                    EditAction: function(row, cell, value, columnDef, dataContext) {
                        var type = 'spec';
                        if (columnDef.id == 'edit_doc') {
                            type = 'doc';
                        } else if (columnDef.id == 'edit_note') {
                            type = 'note';
                        }
                        //check role here
                        if (type == 'spec' && profile.role != 'developer') {
                            return Slick.Formatters.HTMLData(row, cell, '', columnDef, dataContext);
                        }
                        return Slick.Formatters.HTMLData(row, cell, $this.t(actionTemplateEdit, dataContext.id, type), columnDef, dataContext);
                    },
                    vndate: function(row, cell, value, columnDef, dataContext) {
                        var newValue = '';
                        if (value) {
                            value = value.split('-');
                            newValue = value[2] + '-' + value[1] + '-' + value[0];
                        }
                        return Slick.Formatters.HTMLData(row, cell, newValue, columnDef, dataContext);
                    },
                    linkFormatter: function(row, cell, value, columnDef, dataContext) {
                        var idPr = dataContext.id ? dataContext.id : 0;
                        var linkProjectName = <?php echo json_encode($html->url('/tickets/view')); ?>;
                        return '<a href=' + linkProjectName + '/' + dataContext['id'] + ' class="project-is-' + idPr + '">' + value + '</a>';
                    }
                });
                $.extend(Slick.Editors, {
                    singleSelect: function(args) {
                        $.extend(this, new BaseSlickEditor(args));
                        var $input, $div, $reset;
                        var defaultValue;
                        var scope = this;

                        this.init = function() {
                            $div = $('<div />').appendTo(args.container);
                            $input = $("<select />").appendTo($div);
                            // add option
                            if (typeof $this.selectMaps[args.column.field] != 'undefined') {
                                $.each($this.selectMaps[args.column.field], function(id, name) {
                                    var opt = $('<option />').prop({
                                        value: id,
                                        disabled: !args.item.can_update || (args.item.can_update && !(typeof $this.singleSelectable != 'undefined' && typeof $this.singleSelectable[id] != 'undefined'))
                                    }).text(name);
                                    $input.append(opt);
                                });
                            }
                            $input.multipleSelect({
                                single: true,
                                filter: false,
                                onClick: function(view) {
                                    view.instance.setSelects([view.value]);
                                }
                            });
                            $reset = $('<img src="/img/rebuild.jpg" class="reset-status" style="vertical-align: middle; cursor: pointer" title="<?php __('Reset') ?>">')
                                .appendTo($div)
                                .click(function() {
                                    $input.multipleSelect('setSelects', [defaultValue]);
                                });
                            this.focus();
                        };

                        this.destroy = function() {
                            $div.remove();
                        };

                        this.focus = function() {
                            $input.multipleSelect('focus');
                        };

                        this.getValue = function() {
                            return $input.multipleSelect('getSelects')[0];
                        };

                        this.setValue = function(val) {
                            $input.multipleSelect('setSelects', [val]);
                        };

                        this.loadValue = function(item) {
                            defaultValue = item[args.column.field] || "";
                            this.setValue(defaultValue);
                            $input.data('default-value', defaultValue);
                            //$input.select();
                        };

                        this.serializeValue = function() {
                            return $input.multipleSelect('getSelects')[0];
                        };

                        this.applyValue = function(item, state) {
                            item[args.column.field] = state;
                        };

                        this.isValueChanged = function() {
                            var value = this.getValue();
                            return value != defaultValue;
                        };

                        this.validate = function() {
                            if (args.column.validator) {
                                var validationResults = args.column.validator($input.val());
                                if (!validationResults.valid) {
                                    return validationResults;
                                }
                            }

                            return {
                                valid: true,
                                msg: null
                            };
                        };

                        this.init();
                    },
                });
                var data = <?php echo json_encode($dataView); ?>;
                var columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
                $this.selectMaps = <?php echo json_encode($selectMaps); ?>;

                var dataGrid = $this.init($('#project_container'), data, columns, {
                    enableAddRow: false
                });
                update_table_height();

                var exporter = new Slick.DataExporter('/tickets/export');
                dataGrid.registerPlugin(exporter);

                $('#export-table').click(function() {
                    exporter.submit();
                    return false;
                });

                $('#CategoryStatus').change(function() {
                    $('#CategoryStatus option').each(function() {
                        if ($(this).is(':selected')) {
                            viewId = $('#CategoryStatus').val();
                            if (viewId != 0) {
                                window.location = ('/tickets/index/' + viewId);
                            } else {
                                window.location = ('/tickets/index/');
                            }
                        }
                    });
                });
                $('#reset-filter').on('click', function(e) {
                    $('.input-filter').val('').trigger('change');
                    $('.multiSelectOptions.slickgrid-select input[type="checkbox"]').prop('checked', false).trigger('change');
                    dataGrid.setSortColumn();
                    $('input[name="project_container.SortOrder"]').val('').trigger('change');
                    $('input[name="project_container.SortColumn"]').val('').trigger('change');
                });
            });

        })(jQuery);

        function show_company_info(_company) {
            var is_external = _company[0];
            var externalCompanyId = _company.replace(is_external, '', _company);
            // add loading animation
            $('#ticket__alerts').addClass('loading');
            $.ajax({
                url: '<?php echo $this->Html->url(array('action' => 'getCompanyTicketLimits')); ?>' + '/' + externalCompanyId,
                type: 'GET',
                success: function(res) {
                    $('#ticket__alerts').html(res);
                },
                complete: function() {
                    $('#ticket__alerts').removeClass('loading');
                }
            });
        }

        function slickGridFilterCallBack() {
            if (showLimitInfo) {
                var dataGrid = SlickGridCustom.getInstance();
                var _company_index = dataGrid.getColumnIndex('company_id');
                if (typeof _company_index == 'undefined') return;
                var _dataview = dataGrid.getDataView();
                var _length = _dataview.getLength();
                var __continue = _length;
                if (_length) {
                    var _company = _dataview.getItem(0)['company_id'];
                    for (i = 0; i < _length; i++) {
                        if (_dataview.getItem(i)['company_id'] != _company) {
                            __continue = 0;
                            break;
                        }
                    }
                }
                if (__continue) {
                    show_company_info(_company);
                } else {
                    $('#ticket__alerts').html('');
                }
            }
        }
    </script>