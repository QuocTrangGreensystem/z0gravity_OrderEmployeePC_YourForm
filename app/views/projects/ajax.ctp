<?php
echo $html->script(array('draw-progress'));
$gapi = GMapAPISetting::getGAPI();
App::import("vendor", "str_utility");
$str_utility = new str_utility();
?>
<style>
    .error-message {
        color: #FF0000;
        margin-left: 35px;
    }
    fieldset .wd-input label {
        display: block;
        float: none;
        color: #a9a9a9;
    }
    .wd-main-content a{
        border: none;
    }
    div#contentDialog img{
        max-height: 600px;
    }
    .mce-txt{
        margin: 0 !important;
    }
    .wd-data-manager input{
        width:25px !important;
    }
    .context-menu-shadow{
        background-color: white !important;
    }
    .wd-input img{
        width: 30px;
        height: 30px;
        float: right;
        margin-right: 2.8%;
        margin-top: -31px;
    }
    fieldset div.wd-input{
        width:100%;
    }
    fieldset label{
        text-align: left;
        line-height: normal;
        width: 500px;
    }
    fieldset div.wd-input{
        margin:4px 0;
    }
    fieldset div.wd-area > input{
        float: none;
        display: block;
        width: 94.2%;
        border: none;
    }
    fieldset div.wd-input select:focus, fieldset div.wd-input input:focus, fieldset div.wd-input textarea:focus{
        border: none;
    }
    .export-pdf-icon-all{
        background: url("/img_z0g/export-pdf.png") no-repeat !important;
        display: inline-block;
        width: 32px;
        height: 32px;
        vertical-align: top;
        opacity: 1;
    }
    .btn.btn-fullscreen{
        padding: 0;
    }
    .button-setting{
        display: inline-block;
        width: 32px;
        height: 32px;
        vertical-align: top;
        opacity: 1;
        font-size: 20px;
    }
    #overlay-container{
        display: none;
    }
    .wd-input ul li img{
        margin: 0;
    }
    fieldset div.wd-input.wd-weather-list{
        width: 100%;
    }
    /*phan nay cua gantt*/
    #gantt-display{
        overflow: hidden;
        padding-top: 10px;
    }
    #gantt-display .input{
        float: left;
    }
    #gantt-display .input input{
        vertical-align: middle;
    }
    #gantt-display .input label{
        padding: 0 7px;
    }
    #gantt-display .title{
        float: left;
        font-weight: bold;
        padding-right: 10px;
    }
    .gantt-ms td {
        border-bottom: 1px solid #ccc;
    }
    .gantt-month span {
        margin-top: -16px;
        margin-left: 5px;
    }
    #AjaxGanttChartDIV{
        max-width: 95%;
        border: 1px solid #c3c3c3;
    }
    /*end*/
    #diagram {
        width: 94.7%;
        height: 400px;
        position: relative;
        border: 1px solid #c3c3c3;
    }
    .gantt-d31 div{
        width: 62px;
    }
    .gantt-d30 div{
        width: 60px;
    }
    .gantt-d29 div{
        width: 58px;
    }
    .gantt-d28 div{
        width: 56px;
    }
    .gantt-year .gantt-d31 div{
        width: 32px;
    }
    .gantt-year .gantt-d30 div{
        width: 30px;
    }
    .gantt-year .gantt-d29 div{
        width: 29px;
    }
    .gantt-year .gantt-d28 div{
        width: 28px;
    }
    .gantt-scroll-place{
        display: none;
    }
    .gantt{
        width: 100%;
    }
    .task_red{
        background-image: url(/img/extjs/icon-triangle.png);
        background-repeat: no-repeat;
        padding-left: 20px;
        cursor: pointer;
    }
    .slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
    .wd-tab .wd-aside-left{width: 300px !important;}
    .slick-cell-move-handler {
        cursor: move;
    }
    .slick-cell-move-handler:empty {
        cursor: default;
    }
    #dependency-info {
        position: absolute;
        left: 0;
        bottom: 0;
        min-width: 200px;
        max-width: 300px;
        min-height: 80px;
        background: #fff;
        z-index: 999999;
        border: 1px solid #ddd;
        box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        padding: 10px;
    }
    #dependency-info dl {
        overflow: hidden;
        clear: both;
        margin-bottom: 5px;
    }
    #dependency-info dt {
        display: inline-block;
        width: 30px;
        height: 8px;
        margin-right: 10px;
        vertical-align: middle;
    }
    #dependency-info dd {
        display: inline-block;
        vertical-align: middle;
    }
    .progress-pie__bg {
        fill: rgba(255, 255, 255, 0.5);
    }
    .progress-pie__text {
        fill: #00426b;
        font-family: "Iceland", sans-serif;
        letter-spacing: -2;
    }
    .progress-pie__inner-disc {
        fill: white;
    }
    .sg-section--progress-pie .progress-pie {
        width: 18em;
        height: 18em;
    }
    .budget-chard{
        width: 24%;
        float: left;
    }
    .percent-chard{
        width: 50%;
        margin-left: 24%;
    }
    .circle-chard{
        float: right;
        width: 25%;
    }
    .kpi-log .log-content{
        height: 29px;
    }
    #add-activity{
        display: none;
    }
    .normal-scroll{
        overflow-y: auto;
        overflow-x: hidden;
        border: 1px solid #ccc;
    }
    .full-scroll{
        height: 850px;
        overflow-y: auto;
        overflow-x: hidden;
        border: 1px solid #ccc;
    }
    .group-content > h3{
        background: #67a7ca;
        margin-top: 0;
    }
    .wd-input p img{
        width: auto;
        height: auto;
        float: none;
        margin-right: 0;
        margin-top: 0;
    }
    .wd-input p{
        height: 20px;
        padding: 3px;
    }
    .wd-weather-list-dd ul li{
        width: 100px;
    }
    #table-cost table{width:80%;}#table-cost table tr td{border:1px solid #d4d4d4;text-align:center;padding:5px;}#table-cost table tr td.cost-header{background-color:#64a3c7;color:#FFF;}#table-cost table tr td.cost-md{background-color:#75923C;color:#FFF;}#table-cost table tr td.cost-euro{background-color:#95B3D7;color:#FFF;}.cost-disabled{background-color:#F5F5F5;}.checkbox,.wd-weather-list ul li input,.wd-weather-list ul li img,.wd-weather-list-dd ul li input,.wd-weather-list-dd ul li img{float:left;} .highcharts-container{ border:1px solid #999 !important;}.budget_external_chart{ margin-bottom:30px;}
    fieldset div.wd-input.wd-weather-list-dd{
        width: 500px;
    }
    .half-padding{
        margin-top: 0;
    }
    #mcs1_container .customScrollBox .container{
        background-color: none;
        width: 100%;
    }
    .wd-title{
        margin-top: -19px;
        margin-left: 25px;
    }
    .gantt-chart-wrapper{
        overflow-x: auto;
        overflow-y: hidden;
    }
    .wd-title:hover{
        cursor: move;
    }
    #wd-container-main{
        width: 100% !important;
    }
    .wd-weather-list li{display:none; width: 42px; margin-left: 0}
    .wd-weather-list li.checked{display: block}
    .wd-weather-list li input{display :none}
    .wd-weather-list ul li{
        width: 50px;
    }
    .wd-weather-list ul li img{
        width: initial;
        height: auto;
        float: none;
        display: inline-block;
    }
    h2.wd-t1{
        margin-left: 10px;
    }
    @media(max-width: 1366px){
        h2.wd-t1{
            margin-left: 50px;
        }
    }
    @media(min-width: 1370px){
        #contentDialog .wd-title{
            right: 38px;
        }
    }
    @media(max-width: 1024px){
        h2.wd-t1{
            margin-left: 65px;
        }
        #contentDialog .wd-title{
            padding-left: 20px;
        }
    }
</style>
<?php
    if($yourFormFilter['your_form'] == 1){
    // pm template
    $pmTemplate = '';
    $pmTemplate .= '<span style="width: auto;float: left;margin: 0px;margin-top: 8px;">';
    // foreach ($projectBackup as $_val) {
    //     $pmTemplate .= $_employees['pm'][$_val] . '(B), ';
    // }
    $pmTemplate .= $_employees['pm'][$this->data['Project']['project_manager_id']];
    $pmTemplate .= '</span>';
    $urlPm = $this->UserFile->avatar($this->data['Project']['project_manager_id']);
    $pmTemplate .= '<img style="float: left;margin: 0;margin-left: 10px;" src="'. $urlPm .'" />';
    $urlCB= $this->UserFile->avatar($this->data['Project']['chief_business_id']);
    $cuPhaseTemplate = '<p>';
    foreach ($listCurPhase as $val) {
        $cuPhaseTemplate .= $ProjectPhases[$val] . ', ';
    }
    $cuPhaseTemplate .= '</p>';
    if(!empty($projectPhasePlans)){
        $_start_date = $projectPhasePlans[0][0]['MinStartDate'];
        $_end_date = $projectPhasePlans[0][0]['MaxEndDate'];
    }
    $_start_date = isset($_start_date) ? $_start_date : null;
    $startTemplate = $this->Form->input('start_date', array('div' => false,
        'label' => false,
        'disabled' => 'disabled',
        'value' => $str_utility->convertToVNDate($_start_date),
        'type' => 'text'
    ));
    $maps = array(
        'project_name' => array(
            'label' => __d(sprintf($_domain, 'Details'), "Project Name", true),
            'html' => $this->Form->input('project_name', array('div' => false, 'label' => false, 'style' => ''))
        ),
        'project_code_1' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Project Code 1', true),
            'html' => $this->Form->input('project_code_1', array('div' => false, 'label' => false, 'id' => 'onChangeCode')).'<span style="display: none; float:left; color: #000; width: 62%" id= "valueOnChange"></span>'
        ),
        'company_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Company', true),
            'html' => '<p style="padding-top: 6px; width: 92.9%; height: 15px">' . $name_company . '</p>'
        ),
        'long_project_name' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Project long name', true),
            'html' => $this->Form->input('long_project_name', array('div' => false, 'label' => false))
        ),
        'project_code_2' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Project Code 2', true),
            'html' => $this->Form->input('project_code_2', array('div' => false, 'label' => false))
        ),
        'project_manager_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Project Manager', true),
            'html' => $pmTemplate
        ),
        'project_type_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Project type', true),
            'html' => '<p>' . (!empty($ProjectTypes[$this->data['Project']['project_type_id']]) ? $ProjectTypes[$this->data['Project']['project_type_id']] : "") . '</p>'
        ),
        'project_sub_type_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Sub type', true),
            'html' => '<p>' . (!empty($ProjectSubTypes[$this->data['Project']['project_sub_type_id']]) ? $ProjectSubTypes[$this->data['Project']['project_sub_type_id']] : '') . '</p>'
        ),
        'project_amr_program_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Program', true),
            'html' => '<p>' . (!empty($ProjectArmPrograms[$this->data['Project']['project_amr_program_id']]) ? $ProjectArmPrograms[$this->data['Project']['project_amr_program_id']] : '') . '</p>'
        ),
        'project_amr_sub_program_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Sub program', true),
            'html' => '<p>' . (!empty($ProjectArmSubPrograms[$this->data['Project']['project_amr_sub_program_id']]) ? $ProjectArmSubPrograms[$this->data['Project']['project_amr_sub_program_id']] : '') . '</p>'
        ),
        'project_priority_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Priority', true),
            'html' => '<p>' . (!empty($Priorities[$this->data['Project']['project_priority_id']]) ? $Priorities[$this->data['Project']['project_priority_id']] : '') . '</p>'
        ),
        'complexity_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Implementation Complexity', true),
            'html' => '<p>' . (!empty($Complexities[$this->data['Project']['complexity_id']]) ? $Complexities[$this->data['Project']['complexity_id']] : '') . '</p>'
        ),
        'created_value' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Created value', true),
            'html' => $this->Form->input('created_value', array('div' => false, 'label' => false,
                "class" => "placeholder", "placeholder" => __("Created value", true), "readonly" => 'readonly',
            ))
        ),
        'project_status_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Status', true),
            'html' => '<p>' . (!empty($Statuses[$this->data['Project']['project_status_id']]) ? $Statuses[$this->data['Project']['project_status_id']] : '') . '</p>'
        ),
        'project_phase_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Current Phase', true),
            'html' => $cuPhaseTemplate
        ),
        'activity_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Link To RMS Activity', true),
            'html' => '<p>' . (!empty($ProjectActivities[$this->data['Project']['activity_id']]) ? $ProjectActivities[$this->data['Project']['activity_id']] : '') . '</p>'
        ),
        'issues' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Issues', true),
            'html' => '<p style="white-space:pre-line; padding: 10px; width: 93.5%; padding-bottom: 30px;">'.$this->data['Project']['issues'].'</p>'
        ),
        'primary_objectives' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Primary Objectives', true),
            'html' => '<p style="padding: 10px; width: 93.5%; padding-bottom: 30px;">'.$this->data['Project']['primary_objectives'].'</p>'
        ),
        'project_objectives' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Project Objectives', true),
            'html' => '<p style="white-space:pre-line; padding: 10px; width: 93.5%; padding-bottom: 30px;">'.$this->data['Project']['project_objectives'].'</p>'
        ),
        'constraint' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Constraint', true),
            'html' => '<p style="white-space:pre-line; padding: 10px; width: 93.5%; padding-bottom: 30px;">'.$this->data['Project']['constraint'].'</p>'
        ),
        'remark' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Remark', true),
            'html' => '<p style="white-space:pre-line; padding: 10px; width: 93.5%; padding-bottom: 30px;">'.$this->data['Project']['remark'].'</p>'
        ),
        'free_1' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Free 1', true),
            'html' => '<p style="white-space:pre-line; padding: 10px; width: 93.5%; padding-bottom: 30px;">'.$this->data['Project']['free_1'].'</p>'
        ),
        'free_2' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Free 2', true),
            'html' => '<p style="white-space:pre-line; padding: 10px; width: 93.5%; padding-bottom: 30px;">'.$this->data['Project']['free_2'].'</p>'
        ),
        'free_3' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Free 3', true),
            'html' => '<p style="white-space:pre-line; padding: 10px; width: 93.5%; padding-bottom: 30px;">'.$this->data['Project']['free_3'].'</p>'
        ),
        'free_4' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Free 4', true),
            'html' => '<p style="white-space:pre-line; padding: 10px; width: 93.5%; padding-bottom: 30px;">'.$this->data['Project']['free_4'].'</p>'
        ),
        'free_5' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Free 5', true),
            'html' => '<p style="white-space:pre-line; padding: 10px; width: 93.5%; padding-bottom: 30px;">'.$this->data['Project']['free_5'].'</p>'
        ),
        'start_date' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Start Date', true),
            'html' => $startTemplate
        ),
        'end_date' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'End Date', true),
            'html' => $this->Form->input('end_date', array('div' => false,
                'label' => false,
                'disabled' => 'disabled',
                'value' => $str_utility->convertToVNDate(isset($_end_date) ? $_end_date : null),
                'type' => 'text'
            ))
        ),
        'budget_customer_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Customer', true),
            'html' => '<p>' . (!empty($budgetCustomers[$this->data['Project']['budget_customer_id']]) ? $budgetCustomers[$this->data['Project']['budget_customer_id']] : '') . '</p>'
        )
    );
    if($this->data['Project']['category'] == 1):
        $disabled = 'disabled';
        $style = 'width:95% !important; background-color: rgb(218, 221, 226);';
        if(isset($employeeInfo['Role']['name']) && $employeeInfo['Role']['name'] === 'admin'){
            $disabled = '';
            $style = 'width:95% !important;';
        }
        $option = array(__('No', true), __('Yes', true));
        $maps['activated'] = array(
            'label' => __d(sprintf($_domain, 'Details'), 'Timesheet Filling Activated', true),
            'html' => '<p>' . (!empty($this->data['Project']['budget_customer_id']) ? __('Yes', true) : __('No', true)) . '</p>'
        );
    endif;
    $range = range(1, 20);
    foreach($range as $num){
        if( $num <= 4 ){
            //bool 0/1
            $maps['bool_' . $num] = array(
                'label' => __d(sprintf($_domain, 'Details'), '0/1 ' . $num, true),
                'html' => '<p>' . ($this->data['Project']['bool_1'] == 1 ? __('Yes', true) : __('No', true)) . '</p>'
            );
        }
        if( $num <= 5 ){
            //text editor
            $maps['editor_' . $num] = array(
                'label' => __d(sprintf($_domain, 'Details'), 'Editor ' . $num, true),
                'html' => ( !empty($this->data['Project']['editor_' . $num]) ? html_entity_decode($this->data['Project']['editor_' . $num]) : '<p></p>')
            );
            //date MM/YY
            $maps['date_mm_yy_' . $num] = array(
                'label' => __d(sprintf($_domain, 'Details'), 'Date(MM/YY) ' . $num, true),
                'html' => $this->Form->input('date_mm_yy_' . $num, array('type' => 'text', 'class' => 'wd-date-mm-yy', 'div' => false, 'label' => false, 'value' => $this->data['Project']['date_mm_yy_' . $num]))
            );
            //date YY
            $maps['date_yy_' . $num] = array(
                'label' => __d(sprintf($_domain, 'Details'), 'Date(YY) ' . $num, true),
                'html' => $this->Form->input('date_yy_' . $num, array('type' => 'text', 'class' => 'wd-date-yy', 'div' => false, 'label' => false, 'value' => $this->data['Project']['date_yy_' . $num]))
            );
        }
        if($num <= 9){
            //yes/no
            $maps['yn_' . $num] = array(
                'label' => __d(sprintf($_domain, 'Details'), 'Yes/No ' . $num, true),
                'html' => '<p>' . ($this->data['Project']['yn_1'] == 1 ? __('Yes', true) : __('No', true)) . '</p>'
            );
        }
        // list mutiple select.
        if($num <= 10){
            $num_class = 7 + $num;
            if(!empty($datasets['list_muti_' . $num])){
                $htmlListMultiple = '<p>';
                if(!empty($ProjectMultiLists['list_muti_' . $num])){
                    foreach ($ProjectMultiLists['list_muti_' . $num] as $val) {
                        $htmlListMultiple .= $datasets['list_muti_' . $num][$val] . ', ';
                    }
                }
                $htmlListMultiple .= '</p>';
                $maps['list_muti_' . $num] = array(
                    'label' => __d(sprintf($_domain, 'Details'), 'List(multiselect) ' . $num, true),
                    'html' => $htmlListMultiple
                );
            }
        }
        if( $num <= 14 ){
            //list
            if(!empty( $datasets['list_' . $num])){
                $maps['list_' . $num] = array(
                    'label' => __d(sprintf($_domain, 'Details'), 'List ' . $num, true),
                    'html' => '<p>' . (!empty($datasets[$this->data['Project']['list_' . $num]]) ? $datasets[$this->data['Project']['list_' . $num]] : '') . '</p>'
                );
            }
            //date
            $maps['date_' . $num] = array(
                'label' => __d(sprintf($_domain, 'Details'), 'Date ' . $num, true),
                'html' => $this->Form->input('date_' . $num, array('type' => 'text', 'class' => 'wd-date', 'div' => false, 'label' => false, 'value' => $str_utility->convertToVNDate($this->data['Project']['date_' . $num])))
            );
        }
        if( $num <= 16 ){
            //price
            $_class = 'numeric-value';
            if( $num > 6 ) {
                $_class .= ' not-decimal';
            }
            $maps['price_' . $num] = array(
                'label' => __d(sprintf($_domain, 'Details'), 'Price ' . $num, true),
                'html' => '<span style="float: left; margin: 0">' . (!empty($this->data['Project']['price_' . $num]) ? $this->data['Project']['price_' . $num] : '0.00') . '</span><span style="float: left; margin: 0;margin-left: 5px; ">'.$budget_settings.'</span>'
            );
        }
        if( $num <= 18 ){
            //number
            $_class = 'numeric-value';
            if( $num > 6 ) {
                $_class .= ' not-decimal';
            }
            $maps['number_' . $num] = array(
                'label' => __d(sprintf($_domain, 'Details'), 'Number ' . $num, true),
                'html' => $this->Form->input('number_' . $num, array('div' => false, 'class' => $_class, 'label' => false))
            );
        }
        //text one line
        $maps['text_one_line_' . $num] = array(
            'label' => __d(sprintf($_domain, 'Details'), 'Text one line ' . $num, true),
            'html' => $this->Form->input('text_one_line_' . $num, array('div' => false, 'label' => false, 'type' => 'text'))
        );
        //text two line
        $maps['text_two_line_' . $num] = array(
            'label' => __d(sprintf($_domain, 'Details'), 'Text two line ' . $num, true),
            'html' => $this->Form->input('text_two_line_' . $num, array('class' => 'textarea-limit', 'div' => false, 'label' => false, 'rows' => '2', 'style' => 'height:35px;'))
        );
    }
    //team
    $PCModel = ClassRegistry::init('ProfitCenter');
    $listTeam = $PCModel->generateTreeList(array('company_id' => $employee_info['Company']['id']),null,null,' -- ',-1);
    $maps['team'] = array(
        'label' => __d(sprintf($_domain, 'Details'), 'Team', true),
        'html' => '<p>' . (!empty($listTeam[$this->data['Project']['team']]) ? $listTeam[$this->data['Project']['team']] : '') . '</p>'
    );
}

$htmlWea = '';
$htmlWea .= '<div class="wd-input wd-weather-list" style="">';
$htmlWea .= '<ul style="float: left; display: inline;">';
$checked1 = $ProjectArms['ProjectAmr']['weather'] == 'sun' ? "checked" : '';
$checked2 = $ProjectArms['ProjectAmr']['weather'] == 'cloud' ? "checked" : '';
$checked3 = $ProjectArms['ProjectAmr']['weather'] == 'rain' ? "checked" : '';
$htmlWea .= '<li class="'.$checked1.'" ><input checked="true" style="width: 25px; margin-top: 8px;" '.$checked1.' name="data[ProjectAmr][weather][]" value="sun" type="radio" /> <img title="Sun"  src="' . $html->url('/img/sun.png') .'"/></li>';
$htmlWea .= '<li class="'.$checked2.'" ><input type="radio" value="cloud" '.$checked2.' name="data[ProjectAmr][weather][]" style="width: 25px;margin-top: 8px;" /> <img title="Cloud" src="'. $html->url('/img/cloud.png') .'"  /></li>';
$htmlWea .= '<li class="'.$checked3.'"  ><input type="radio" value="rain" '.$checked3.' name="data[ProjectAmr][weather][]" style="width: 25px;margin-top: 8px;"   /> <img title="Rain"  src="'. $html->url('/img/rain.png') .'"  /></li>';

$checked1 = $ProjectArms['ProjectAmr']['rank'] == 'up' ? "checked" : '';
$checked2 = $ProjectArms['ProjectAmr']['rank'] == 'down' ? "checked" : '';
$checked3 = $ProjectArms['ProjectAmr']['rank'] == 'mid' ? "checked" : '';
$htmlWea .= '<li class="'.$checked1.'"><input checked="true" style="width: 25px; margin-top: 8px;" '.$checked1.' name="data[ProjectAmr][rank][]" value="up" type="radio" /> <img title="Up" src=" '. $html->url('/img/up.png') .'"  /></li>';
$htmlWea .= '<li class="'.$checked2.'" ><input type="radio" value="down" '.$checked2.' name="data[ProjectAmr][rank][]" style="width: 25px;margin-top: 8px;" /> <img title="Down" src="'. $html->url('/img/down.png') .'"/></li>';
$htmlWea .= '<li class="'.$checked3.'"  ><input type="radio" value="mid" '.$checked3.' name="data[ProjectAmr][rank][]" style="width: 25px;margin-top: 8px;"   /> <img title="Mid"  src="'. $html->url('/img/mid.png').'"/></li>';
$htmlWea .= '</ul>';
$htmlWea .= '</div>';
$maps['weather'] = array(
    'html' => $htmlWea
);
?>

<div id="wd-container-main" class="wd-project-detail">
    <div class="wd-layout">
        <div class="wd-main-content">
             <div class="wd-title" style="max-width: 1328px; margin: auto; position: relative;">
                <div class="wd-head-left" style ="width: calc(100% - 110px); display: inline-block; position: relative; top: 10px">
                    <h2 class="wd-t1" style="color: #ffb250; margin-top: 5px;"><?php echo sprintf(__("%s", true), $projectName['Project']['project_name']); ?></h2>
                    <?php if($yourFormFilter['weather']) : ?>
                    <div class="" style="width: 200px; text-align: right; display: inline-block;"><?php echo !empty($maps['weather']['html']) ? $maps['weather']['html'] : '';?></div>
                    <?php endif; ?>
                </div>
                <div class="wd-head-right" style ="width: 105px; display: inline-block; position: relative; bottom: 10px">
                    <div id="collapse" onclick="collapseScreen();" ><button class="btn btn-esc"></button></div>
                    <a href="javascript:;" onclick="expandScreen();" id="expand-btn" class="btn btn-fullscreen"></a>
                    <a href="#" onclick="SubmitDataExport();return false;" class="export-pdf-icon-all" title="<?php __('Export PDF')?>"><span></span></a>
                    <a href="<?php echo $html->url("/projects/your_form_filter/" . $project_name['Project']['id']) ?>" id="button-setting" class="button-setting" title="<?php __('Setting')?>"><span></span></a>
                </div>
            </div>
            <div id="wd-tab-content" class="wd-tab normal-scroll">
                <div class="wd-panel">
                    <div class="wd-section" id="wd-fragment-1">
                        <?php echo $this->Session->flash(); ?>
                        <?php
                        echo $this->Form->create('Project', array('enctype' => 'multipart/form-data', 'id' => 'ProjectEditForm'));
                        ?>

<fieldset>
    <div id='chart-wrapper' class="wd-scroll-form" style="height:auto; width: 1400px; overflow: hidden">
        <div id="wd-fragment-temp">
            <h2 id="project-name" class="wd-t1" style="color: #ffb250; font-size: 21px; display: none"><?php echo sprintf(__("%s", true), $projectName['Project']['project_name']); ?></h2>
              <?php // if($yourFormFilter['weather']) : ?>
            <div class="weather-pdf" style="position: relative; top: 17px; left: 26px; width: 200px; text-align: right; display: none;"><?php echo !empty($maps['weather']['html']) ? $maps['weather']['html'] : '';?></div>
            <?php //endif; ?>
        </div>
        <div id='div_your_form' style="display: none">
            <?php
            if($yourFormFilter['your_form'] == 1){
                $first = true;
                foreach($translation_data as $data){
                    //ignore project details
                    if( $data['Translation']['field'] == 'project_details')continue;
                    $fieldName = $data['Translation']['field'];
                    $class = isset($maps[$fieldName]['class']) ? $maps[$fieldName]['class'] : '';
                    $data_wid = '';
                    if($data['TranslationSetting']['show'] == 1 && $first){
                        $first = false;
                        $data_wid = 'data-widget="your_form"';
                    }
                ?>
                    <div <?php echo $data_wid ?> class="wd-input wd-area wd-none <?php echo $class ?>" <?php echo $data['TranslationSetting']['show'] == 0 ? 'style="display: none"' : '' ?>>
                        <label><?php echo !empty($maps[$fieldName]['label']) ? $maps[$fieldName]['label'] : ''; ?></label>
                        <?php echo !empty($maps[$fieldName]['html']) ? $maps[$fieldName]['html'] : ''; ?>
                    </div>
                <?php
                }
            }
                ?>
        </div>
<!-- weather -->
<?php $showKpiBudget = false; ?>
<div id="div_weather" style="display: none">
<?php if($showMenu['kpi']) : ?>
<div data-widget="weather"  class="wd-input wd-area wd-none" style = "<?php echo !$showMenu['kpi'] ? 'display: none' : '' ?>">
    <label><?php echo !empty($maps['weather']['label']) ? $maps['weather']['label'] : ''; ?></label>
    <?php //echo !empty($maps['weather']['html']) ? $maps['weather']['html'] : '';
$commentKpiTemp = '<div class="group-content" style="width: 95%">';
$commentKpiTemp .= '<h3 class="half-padding">';
$commentKpiTemp .= '<span>' . __d(sprintf($_domain, 'KPI'), 'Comment', true) .'</span>';
$commentKpiTemp .= '</h3>';
$commentKpiTemp .= '<div id="sale-lead-log" class="kpi-log">';
$commentKpiTemp .= '<ul>';
                if(!empty($commentKpi)){
                    $commentKpi = $commentKpi['LogSystem'];
                    $linkAvatar = $this->UserFile->avatar($commentKpi['employee_id']);
                    $name = htmlentities(preg_replace('# [0-9]{1,2}\:[0-9]{1,2} [0-9]{2}\/[0-9]{2}\/[0-9]{4}$#', '', $commentKpi['name']));
$commentKpiTemp .= '<li id="sale-log-'. $commentKpi['id'].'" data-log-id="'. $commentKpi['id'] .'">';
$commentKpiTemp .= '<img class="log-avatar" src="'.$linkAvatar .'">';
$commentKpiTemp .= '<div class="log-body">';
$commentKpiTemp .= '<h4 class="log-author">'.$name .'</h4>';
$commentKpiTemp .= '<em class="log-time">'.date('H:i d-m-Y', $commentKpi['created']).'</em>';
$commentKpiTemp .= '<textarea class="log-content" rowspan="2">'. $commentKpi['description'].'</textarea>';
$commentKpiTemp .= '</div>';
$commentKpiTemp .= '</li>';
                }
$commentKpiTemp .= '</ul>';
$commentKpiTemp .= '</div>';
$commentKpiTemp .= '</div>';

$todoKpiTemp = '<div class="group-content" style="width: 95%">';
$todoKpiTemp .= '<h3 class="half-padding">';
$todoKpiTemp .= '<span>'. __d(sprintf($_domain, 'KPI'), 'To Do', true) .'</span>';
$todoKpiTemp .= '</h3>';
$todoKpiTemp .= '<div id="todo-log" class="kpi-log">';
$todoKpiTemp .= '<ul>';
                if(!empty($todoKpi)){
                    $todoKpi = $todoKpi['LogSystem'];
                    $linkAvatar = $this->UserFile->avatar($todoKpi['employee_id']);
                    $name = htmlentities(preg_replace('# [0-9]{1,2}\:[0-9]{1,2} [0-9]{2}\/[0-9]{2}\/[0-9]{4}$#', '', $todoKpi['name']));
$todoKpiTemp .= '<li id="todo-'.$todoKpi['id'].'" data-log-id="'.$todoKpi['id'].'">';
$todoKpiTemp .= '<img class="log-avatar" src="'. $linkAvatar.'">';
$todoKpiTemp .= '<div class="log-body">';
$todoKpiTemp .= '<h4 class="log-author">'. $name.'</h4>';
$todoKpiTemp .= '<em class="log-time">'. date('H:i d-m-Y', $todoKpi['created']) .'</em>';
$todoKpiTemp .= '<textarea class="log-content" rowspan="2">'. $todoKpi['description'].'</textarea>';
$todoKpiTemp .= '</div>';
$todoKpiTemp .= '</li>';
                }
$todoKpiTemp .= '</ul>';
$todoKpiTemp .= '</div>';
$todoKpiTemp .= '</div>';

$doneKpiTemp  = '<div class="group-content" style="width: 95%">';
$doneKpiTemp .= '<h3 class="half-padding">';
$doneKpiTemp .= '<span>'. __d(sprintf($_domain, 'KPI'), 'Done', true) .'</span>';
$doneKpiTemp .= '</h3>';
$doneKpiTemp .= '<div id="done-log" class="kpi-log">';
$doneKpiTemp .= '<ul>';
                if(!empty($doneKpi)){
                    $doneKpi = $doneKpi['LogSystem'];
                    $linkAvatar = $this->UserFile->avatar($doneKpi['employee_id']);
                    $name = htmlentities(preg_replace('# [0-9]{1,2}\:[0-9]{1,2} [0-9]{2}\/[0-9]{2}\/[0-9]{4}$#', '', $doneKpi['name']));
$doneKpiTemp .= '<li id="done-'.$doneKpi['id'] .'" data-log-id="'. $doneKpi['id'] .'">';
$doneKpiTemp .= '<img class="log-avatar" src="'. $linkAvatar .'">';
$doneKpiTemp .= '<div class="log-body">';
$doneKpiTemp .= '<h4 class="log-author">'. $name .'</h4>';
$doneKpiTemp .= '<em class="log-time">'. date('H:i d-m-Y', $doneKpi['created']).'</em>';
$doneKpiTemp .= '<textarea class="log-content" rowspan="2">'. $doneKpi['description'].'</textarea>';
$doneKpiTemp .= '</div>';
$doneKpiTemp .= '</li>';
                }
$doneKpiTemp .= '</ul>';
$doneKpiTemp .= '</div>';
$doneKpiTemp .= '</div>';

$acceptanceKpiTemp = '<div style="width: 95%">';
$acceptanceKpiTemp .='<div class="group-content" style="width: 105%">';
$acceptanceKpiTemp .= '<h3><span>' . __d(sprintf($_domain, 'KPI'), 'Acceptance', true) . '</span></h3>';
$acceptanceKpiTemp .= '<table id="acceptance">';
$acceptanceKpiTemp .= '<tbody>';
        foreach($acceptancesKpi as $acc){
            if( !$acc['ProjectAcceptance']['weather'] )$acc['ProjectAcceptance']['weather'] = 'sun';
            $accId = $acc['ProjectAcceptance']['id'];
$acceptanceKpiTemp .= '<tr class="acceptance">';
$acceptanceKpiTemp .= '<td width="300">'. @$typeAcceptance[ $acc['ProjectAcceptance']['project_acceptance_type_id'] ] .'</td>';
$acceptanceKpiTemp .= '<td width="400" class="wd-weather-list-dd">';
$acceptanceKpiTemp .= '<ul style="float: left; display: inline;">';
$acceptanceKpiTemp .= '<li><input '. (@$acc["ProjectAcceptance"]["weather"] == "sun" ? "checked" : "") .' style="max-width: 25px; width: 25px;margin-top: 10px;" type="radio" class="weather" name="acceptance-'.$accId.'"> <img src="'. $html->url('/img/sun.png') .'"  /></li>';
$acceptanceKpiTemp .= '<li><input '. (@$acc["ProjectAcceptance"]["weather"] == "cloud" ? "checked" : "") .' style="max-width: 25px; width: 25px;margin-top: 10px;" type="radio" class="weather" name="acceptance-'.$accId.'"> <img src="'. $html->url('/img/cloud.png') .'"  /></li>';
$acceptanceKpiTemp .= '<li><input '. (@$acc["ProjectAcceptance"]["weather"] == "rain" ? "checked" : "") .' style="max-width: 25px; width: 25px;margin-top: 10px;" type="radio" class="weather" name="acceptance-'.$accId.'"> <img src="'. $html->url('/img/rain.png') .'"  /></li>';
$acceptanceKpiTemp .= '</ul>';
$acceptanceKpiTemp .= '</td>';
$acceptanceKpiTemp .= '<td>'. ($acc['ProjectAcceptance']['progress'] ? $acc['ProjectAcceptance']['progress'] : 0.00) .' %</td>';
$acceptanceKpiTemp .= '</tr>';
        }
$acceptanceKpiTemp .= '</tbody>';
$acceptanceKpiTemp .= '</table>';
$acceptanceKpiTemp .= '</div>';
$acceptanceKpiTemp .= '</div>';

$budgetKpiTemp = '<div style="width: 95%">';
$budgetKpiTemp .= '<div class="group-content" style="width: 105%">';
$budgetKpiTemp .= '<h3><span>'. __d(sprintf($_domain, 'KPI'), 'Budget', true) .'</span></h3>';
$budgetKpiTemp .= '<div class="wd-input separator">';
        $md = !empty($employee_info['Company']['unit']) ? $employee_info['Company']['unit'] : 'M.D';
$budgetKpiTemp .= '<div style="float: left; line-height: -40px; width:30%">';
$budgetKpiTemp .= '<div class="wd-input wd-weather-list-dd">';
$budgetKpiTemp .= '<ul style="float: left; display: inline; width: 500px;">';
$budgetKpiTemp .= '<li><input '. ($budgetKpi["ProjectAmr"]["weather"] == "sun" ? "checked" : "") .' style="max-width: 25px; width: 25px;margin-top: 10px;" type="radio" class="weather" name="budget"> <img src="'. $html->url('/img/sun.png') .'"  /></li>';
$budgetKpiTemp .= '<li><input '. ($budgetKpi["ProjectAmr"]["weather"] == "cloud" ? "checked" : "") .' style="max-width: 25px; width: 25px;margin-top: 10px;" type="radio" class="weather" name="budget"> <img src="'. $html->url('/img/cloud.png') .'"  /></li>';
$budgetKpiTemp .= '<li><input '. ($budgetKpi["ProjectAmr"]["weather"] == "rain" ? "checked" : "") .' style="max-width: 25px; width: 25px;margin-top: 10px;" type="radio" class="weather" name="budget"> <img src="'. $html->url('/img/rain.png') .'"  /></li>';
$budgetKpiTemp .= '</ul>';
$budgetKpiTemp .= '</div>';
$budgetKpiTemp .= '</div>';

$budgetKpiTemp .= '</div>';
$budgetKpiTemp .= '<div class="wd-input">';
$budgetKpiTemp .= '<div id="table-cost">';
$budgetKpiTemp .= '<table>';
                if(!empty($settingMenusKpi) && (!empty($settingMenusKpi['project_budget_internals']) || !empty($settingMenusKpi['project_budget_externals']))):
$budgetKpiTemp .= '<tr>';
$budgetKpiTemp .= '<td style="width: 120px;" rowspan="2"></td>';
$budgetKpiTemp .= '<td colspan="2" class="cost-header">'.__('Budget', true) .'</td>';
$budgetKpiTemp .= '<td colspan="2" class="cost-header">'. __('Forecast', true) .'</td>';
$budgetKpiTemp .= '<td class="cost-header">'. __('Var', true) .'</td>';
$budgetKpiTemp .= '<td colspan="2" class="cost-header">'. __('Consumed', true).'</td>';
$budgetKpiTemp .= '<td colspan="2" class="cost-header">'. __('Remain', true) .'</td>';
$budgetKpiTemp .= '</tr>';
$budgetKpiTemp .= '<tr>';
$budgetKpiTemp .= '<td style="width: 10%;" class="cost-md">'. __($md, true).'</td>';
$budgetKpiTemp .= '<td style="width: 10%;" class="cost-euro">'. __($budget_settings, true) .'</td>';
$budgetKpiTemp .= '<td style="width: 10%;" class="cost-md">'. __($md, true) .'</td>';
$budgetKpiTemp .= '<td style="width: 10%;" class="cost-euro">'. __($budget_settings, true) .'</td>';
$budgetKpiTemp .= '<td style="width: 10%;" class="cost-euro">'. __('%', true) .'</td>';
$budgetKpiTemp .= '<td style="width: 10%;" class="cost-md">'. __($md, true) .'</td>';
$budgetKpiTemp .= '<td style="width: 10%;" class="cost-euro">'. __($budget_settings, true) .'</td>';
$budgetKpiTemp .= '<td style="width: 10%;" class="cost-md">'. __($md, true) .'</td>';
$budgetKpiTemp .= '<td style="width: 10%;" class="cost-euro">'. __($budget_settings, true) .'</td>';
$budgetKpiTemp .= '</tr>';
                endif;
                if(!empty($settingMenusKpi) && !empty($settingMenusKpi['project_budget_internals'])):
$budgetKpiTemp .= '<tr>';
$budgetKpiTemp .= '<td class="cost-header">'. __("Internal", true) .'</td>';
$budgetKpiTemp .= '<td>'. (!empty($internals["budgetManDay"]) ? number_format($internals["budgetManDay"], 2, ",", " ") : 0) .'</td>';
$budgetKpiTemp .= '<td>'. (!empty($internals['budgetEuro']) ? number_format($internals['budgetEuro'], 2, ',', ' ') : 0) .'</td>';
$budgetKpiTemp .= '<td>'. (!empty($workloadInter) ? number_format($workloadInter, 2, ',', ' ') : 0) .'</td>';
$budgetKpiTemp .= '<td>'. (!empty($internals['forecastEuro']) ? number_format($internals['forecastEuro'], 2, ',', ' ') : 0) .'</td>';
$budgetKpiTemp .= '<td>'. (!empty($internals['varEuro']) ? number_format($internals['varEuro'], 2, ',', ' ') : 0) .'</td>';
$budgetKpiTemp .= '<td>'. (!empty($internals['consumedManday']) ? number_format($internals['consumedManday'], 2, ',', ' ') : 0) .'</td>';
$budgetKpiTemp .= '<td>'. (!empty($internals['consumedEuro']) ? number_format($internals['consumedEuro'], 2, ',', ' ') : 0) .'</td>';
$budgetKpiTemp .= '<td>'. (!empty($remainInter) ? number_format($remainInter, 2, ',', ' ') : 0) .'</td>';
$budgetKpiTemp .= '<td>'. (!empty($internals['remainEuro']) ? number_format($internals['remainEuro'], 2, ',', ' ') : 0) .'</td>';
$budgetKpiTemp .= '</tr>';
                endif;
                if(!empty($settingMenusKpi) && !empty($settingMenusKpi['project_budget_externals'])):
$budgetKpiTemp .= '<tr>';
$budgetKpiTemp .= '<td class="cost-header">'. __('External', true) .'</td>';
$budgetKpiTemp .= '<td>'. (!empty($externals['BudgetManDay']) ? number_format($externals['BudgetManDay'], 2, ',', ' ') : 0) .'</td>';
$budgetKpiTemp .= '<td>'. (!empty($externals['BudgetEuro']) ? number_format($externals['BudgetEuro'], 2, ',', ' ') : 0) .'</td>';
$budgetKpiTemp .= '<td>'. (!empty($workloadExter) ? number_format($workloadExter, 2, ',', ' ') : 0) .'</td>';
$budgetKpiTemp .= '<td>'. (!empty($externals['ForecastEuro']) ? number_format($externals['ForecastEuro'], 2, ',', ' ') : 0) .'</td>';
$budgetKpiTemp .= '<td>'. (!empty($externals['VarEuro']) ? number_format($externals['VarEuro'], 2, ',', ' ') : 0) .'</td>';
$budgetKpiTemp .= '<td>'. (!empty($externalConsumeds) ? number_format($externalConsumeds, 2, ',', ' ') : 0) .'</td>';
$budgetKpiTemp .= '<td>'. (!empty($externals['ConsumedEuro']) ? number_format($externals['ConsumedEuro'], 2, ',', ' ') : 0) .'</td>';
$budgetKpiTemp .= '<td>'. (!empty($remainExter) ? number_format($remainExter, 2, ',', ' ') : 0) .'</td>';
$budgetKpiTemp .= '<td>'. (!empty($externals['RemainEuro']) ? number_format($externals['RemainEuro'], 2, ',', ' ') : 0) .'</td>';
$budgetKpiTemp .= '</tr>';
                endif;
                if(!empty($settingMenusKpi) && !empty($settingMenusKpi['project_budget_sales'])):
$budgetKpiTemp .= '<tr style="height: 25px;">';
$budgetKpiTemp .= '</tr>';
$budgetKpiTemp .= '<tr>';
$budgetKpiTemp .= '<td style="width: 120px;" rowspan="2"></td>';
$budgetKpiTemp .= '<td colspan="2" class="cost-header">'. __('Sold', true) .'</td>';
$budgetKpiTemp .= '<td class="cost-header">'. __('Billed', true) .'</td>';
$budgetKpiTemp .= '<td class="cost-header">'. __('Paid', true) .'</td>';
$budgetKpiTemp .= '</tr>';
$budgetKpiTemp .= '<tr>';
$budgetKpiTemp .= '<td style="width: 10%;" class="cost-md">'. __($md, true) .'</td>';
$budgetKpiTemp .= '<td style="width: 10%;" class="cost-euro">'. __($budget_settings, true) .'</td>';
$budgetKpiTemp .= '<td style="width: 10%;" class="cost-euro">'. __($budget_settings, true).'</td>';
$budgetKpiTemp .= '<td style="width: 10%;" class="cost-euro">'. __($budget_settings, true) .'</td>';
$budgetKpiTemp .= '</tr>';
                    $print = '';
                    if(!empty($settingMenusKpi) && (empty($settingMenusKpi['project_budget_internals']) && empty($settingMenusKpi['project_budget_externals']))){
                        $print = 'style="width: 10%;"';
                    }
$budgetKpiTemp .= '<tr>';
$budgetKpiTemp .= '<td class="cost-header"'. $print .'>'. __('Sale', true) .'</td>';
$budgetKpiTemp .= '<td>'. (!empty($sales['manDay']) ? number_format($sales['manDay'], 2, ',', ' ') : 0) .'</td>';
$budgetKpiTemp .= '<td>'. (!empty($sales['sold']) ? number_format($sales['sold'], 2, ',', ' ') : 0) .'</td>';
$budgetKpiTemp .= '<td>'. (!empty($sales['billed']) ? number_format($sales['billed'], 2, ',', ' ') : 0) .'</td>';
$budgetKpiTemp .= '<td>'. (!empty($sales['paid']) ? number_format($sales['paid'], 2, ',', ' ') : 0) .'</td>';
$budgetKpiTemp .= '</tr>';
                endif;
$budgetKpiTemp .= '</table>';
$budgetKpiTemp .= '</div>';
$budgetKpiTemp .= '<div id="table-sales">';
$budgetKpiTemp .= '</div>';
$budgetKpiTemp .= '</div>';
$budgetKpiTemp .= '</div>';
$budgetKpiTemp .= '</div>';

$progressKpiTemp = '<div style="width: 95%">';
$progressKpiTemp .= '<div id="svg_kpi" class="group-content" style="clear:both;">';
$progressKpiTemp .= '<h3><span>'. __d(sprintf($_domain, 'KPI'), 'Progress', true) .'</span></h3>';

$progressKpiTemp .= '<div id="svg_kpi_1" class="budget-progress" style="float: left">
         <div class="progress-label">
            <div class=""><span class="progress-label-color" style="background-color: #538FFA"></span><span>'.  __('Consumed', true) .'</span></div>
            <div class=""><span class="progress-label-color" style="background-color: #E44353"></span><span>'.  __('Planed', true) .'</span></div>
        </div>
        <div id = "budget-inner" class="budget-inner">';
$countdataSets = !empty($dataSets) ? count($dataSets) : 0;
$progressKpiTemp .= '<div class="wd-table svg_budget" id="budget_db" data-type="budget" style="width:'. $countdataSets * 50 .'px;height:280px; float: left; margin-top: 2px;">';

$progressKpiTemp .= '</div>';
$progressKpiTemp .= '</div>
        <span id ="left" class="scroll-progress scroll-left"></span>
        <span id ="right" class="scroll-progress scroll-right"></span>
    </div>';
$progressKpiTemp .= '<canvas id="canvas_kpi" style="display: none;"></canvas>';
$progressKpiTemp .= '<div id="png-container_kpi"></div>';
$progressKpiTemp .= '<aside class="budget-progress-circle" style="overflow:visible; margin-top: 32px; float: left"><div class="progress-circle progress-circle-yellow">
            <div class="progress-circle-inner">
                <i class="icon-question" aria-hidden="true"></i>
                <canvas data-value = '. $progression .' id="myCanvas" width="165" height="160" style="" class="canvas-circle"></canvas>
                <div class ="progress-value progress-validated"><p>'.  __('Consumed', true) .'</p><span>'. round($engaged, 2) .'</span></div>
                <div class ="progress-value progress-engaged"><p>'.  __('Planed', true) .'</p><span>'. round($validated, 2) .'</span></div>
            </div>
        </div>
    </aside>';
$progressKpiTemp .= '<canvas id="canvas_budget" style="display: none;"></canvas>';
$progressKpiTemp .= '<div id="png-container_budget"></div>';
$progressKpiTemp .= '<br clear="all"/>';
foreach($dataExternals as $_external=> $_dataExternal){
    $count = !empty($_dataExternal['dataSetsExternal']) ? count($_dataExternal['dataSetsExternal']) : 0;

$progressKpiTemp .= '<div class="demo-gauge">';
$progressKpiTemp .= '<div id="gauge_'. $_external.'">';
$progressKpiTemp .= '</div>';
$progressKpiTemp .= '<br clear="all"  />';
                    $pros = !empty($_dataExternal['progressExternal']) ? $_dataExternal['progressExternal'] : 0;
$progressKpiTemp .= '<div class="num-progress" style="margin-left: 261px;">'.  __($pros . '% Progression', true) .'</div>';
$progressKpiTemp .= '</div>';
$progressKpiTemp .= '<div class="budget-progress budget-external">
        <div class="progress-label">
            <div class=""><span class="progress-label-color" style="background-color: #538FFA"></span><span>'. __('Consumed', true) .'</span></div>
            <div class=""><span class="progress-label-color" style="background-color: #E44353"></span><span><span>'.__('Planed', true) .'</span></div>
        </div>
        <div id = "budget-inner" class="budget-inner">';

$progressKpiTemp .= '<div class="wd-table svg_budget" id="budget_external_'. $_external .'" data-type="external_'. $_external .'" style="width: '. $count * 50 .'px;height:280px; float: left; margin-top: 27px;">';

$progressKpiTemp .= '</div>';
$progressKpiTemp .= '</div>
        <span id ="left" class="scroll-progress scroll-left"></span>
        <span id ="right" class="scroll-progress scroll-right"></span>
    </div>';
$progressKpiTemp .= '<canvas id="canvas_external_'. $_external .'" style="display: none;"></canvas>';
$progressKpiTemp .= '<div id="png-container_external_'. $_external .'"></div>';
$progressKpiTemp .= '<br clear="all"  />';
    }
$progressKpiTemp .= '</div>';

$ordersKpi = $this->requestAction('/kpi_settings/get');
foreach ($ordersKpi as $value) {
    $orderKpi = explode('|', $value);
    if($orderKpi[0] == 'comment' && $orderKpi[1] == 1){
        echo $commentKpiTemp;
    } else if( $orderKpi[0] == 'done' && $orderKpi[1] == 1 ){
        echo $doneKpiTemp;
    } else if($orderKpi[0] == 'to_do' && $orderKpi[1] == 1){
        echo $todoKpiTemp;
    } else if($orderKpi[0] == 'acceptance' && $orderKpi[1] == 1){
        echo $acceptanceKpiTemp;
    } else if($orderKpi[0] == 'budget' && $orderKpi[1] == 1){
        echo $budgetKpiTemp;
    } else if($orderKpi[0] == 'progress' && $orderKpi[1] == 1){
        $showKpiBudget = true;
        echo $progressKpiTemp;
    }
}
    ?>
</div>
<?php endif; ?>
</div>
<!-- gantt -->
<?php
$ganttTemp = '';
$ganttTemp .= '<div data-widget="gantt" class="wd-input wd-area wd-none" style = "margin-top: 15px;'. ((!empty($showMenu['gantt']) && $showMenu['gantt']) ? '' : 'display: none') .'">';
$ganttTemp .= '<div id="AjaxGanttChartDIV">';
$ganttTemp .= '</div>';
$ganttTemp .= '</div>';
$ganttTemp .= '';
?>
<!-- gantt planing -->
<?php
$gantts = $stones = array();
$ganttStart = $ganttEnd = 0;
if($showMenu['milestone'] && $yourFormFilter['milestone'] == 1){
    if(!empty($mileStone['ProjectMilestone'])){
        foreach ($mileStone['ProjectMilestone'] as $p) {
            $_start = strtotime($p['milestone_date']);
            if (!$ganttStart || $_start < $ganttStart) {
                $ganttStart = $_start;
            } elseif (!$ganttEnd || $_start > $ganttEnd) {
                $ganttEnd = $_start;
            }
            $stones[] = array($_start, $p['project_milestone'], $p['validated']);
        }
    }
    if (!empty($_projectPhasePlans)) {
        $_phase['start'] = $_phase['end'] = $_phase['rstart'] = $_phase['rend'] = 0;
        foreach ($_projectPhasePlans as $p) {
            $phace = $p['ProjectPhasePlan'];
            /**
             * Set start, end, real start, real end.
             */
            if(isset($_phase['start']) && !empty($_phase['start']) && $_phase['start'] != 0){
                $date = $this->Gantt->toTime($phace['phase_planed_start_date']);
                if(($date <= $_phase['start']) && $date != 0){
                    $_phase['start'] = $date;
                }
            } else {
                $_phase['start'] = $this->Gantt->toTime($phace['phase_planed_start_date']);
            }
            if(isset($_phase['end']) && !empty($_phase['end']) && $_phase['end'] != 0){
                $date = $this->Gantt->toTime($phace['phase_planed_end_date']);
                if($date >= $_phase['end']){
                    $_phase['end'] = $date;
                }
            } else {
                $_phase['end'] = $this->Gantt->toTime($phace['phase_planed_end_date']);
            }
            if(isset($_phase['rstart']) && !empty($_phase['rstart']) && $_phase['rstart'] != 0){
                $date = $this->Gantt->toTime($phace['phase_real_start_date']);
                if(($date <= $_phase['rstart']) && $date != 0){
                    $_phase['rstart'] = $date;
                }
            } else {
                $_phase['rstart'] = $this->Gantt->toTime($phace['phase_real_start_date']);
            }
            if(isset($_phase['rend']) && !empty($_phase['rend']) && $_phase['rend'] != 0){
                $date = $this->Gantt->toTime($phace['phase_real_end_date']);
                if($date >= $_phase['rend']){
                    $_phase['rend'] = $date;
                }
            } else {
                $_phase['rend'] = $this->Gantt->toTime($phace['phase_real_end_date']);
            }
            $_phase['id'] = $phace['id'];
            $_phase['name'] = !empty($phace['ProjectPhase']['name']) ? $phace['ProjectPhase']['name'] : '';
            $_phase['color'] = !empty($phace['ProjectPhase']['color']) ? $phace['ProjectPhase']['color'] : '#004380';
            if ($_phase['rstart'] > 0) {
                $_start = min($_phase['start'], $_phase['rstart']);
            } else {
                $_start = $_phase['start'];
            }
            if (!$ganttStart || ($_start > 0 && $_start < $ganttStart)) {
                $ganttStart = $_start;
            }
            $_end = max($_phase['end'], $_phase['rend']);
            if (!$ganttEnd || $_end > $ganttEnd) {
                $ganttEnd = $_end;
            }
            $_gantt['phase'][0] = $_phase;
        }
        $completed = 0;
        if(!empty($phases) && !empty($phases[$id])){
            $ds = $phases[$id];
            $workload = !empty($ds['workload']) ? $ds['workload'] : 0;
            $consumed = !empty($ds['consumed']) ? $ds['consumed'] : 0;
            if($workload == 0){
                $completed = 0;
            } else{
                $completed = round((($consumed*100)/$workload), 2);
            }
        }
    }
    $gantts = array(
        'id' => $id,
        'project_part_id' => '',
        'name' => $projectName['Project']['project_name'],
        'predecessor' => '',
        'color' => '#f05656',
        'assign' => '',
        'start' => (empty($_phase['start']) || $_phase['start'] == 0) ? '00-00-0000' : date('d-m-Y', $_phase['start']),
        'end' => (empty($_phase['end']) || $_phase['end'] == 0) ? '00-00-0000' : date('d-m-Y', $_phase['end']),
        'rstart' => (empty($_phase['rstart']) || $_phase['rstart'] == 0) ? '00-00-0000' : date('d-m-Y', $_phase['rstart']),
        'rend' => (empty($_phase['rend']) || $_phase['rend'] == 0) ? '00-00-0000' : date('d-m-Y', $_phase['rend']),
        'completed' => !empty($completed) ? $completed : 0
    );
}
?>
<div id="div_milestone" style="display: none">
<?php if($showMenu['milestone'] && $yourFormFilter['milestone'] == 1) :  ?>
<div data-widget="milestone"  class="wd-input wd-area wd-none" style = "margin-top: 15px; <?php echo !$showMenu['milestone'] ? 'display: none' : '' ?>">
    <div style="width: 95%">
        <?php
        $this->GanttV2->create('year', $ganttStart, $ganttEnd, isset($stones) ? $stones : array(), false);
        $this->GanttV2->draw($gantts['id'], $gantts['name']
                , $gantts['predecessor'], strtotime($gantts['start'])
                , strtotime($gantts['end']), strtotime($gantts['rstart']), strtotime($gantts['rend']), $gantts['color']
                ,'parent'
                , $gantts['completed']
                , $gantts['assign']
                );
        $this->GanttV2->end();
        ?>
    </div>
</div>
<?php endif; ?>
</div>
<!-- risks -->
<div id="div_risk" style="display: none">
<?php if($showMenu['risk'] && $yourFormFilter['risk'] == 1) : ?>
<div data-widget="risk" class="wd-input wd-area wd-none" style = "margin-top: 15px; <?php echo !$showMenu['risk'] ? 'display: none' : '' ?>">
    <div>
        <div id="project_risks" style="width: 95%">
        </div>
        <table id = "absence" style="width: 95%">
            <thead>
                <tr>
                    <th><?php echo __('Risk/Opportunity', true) ?></th>
                    <th><?php echo __('Severity', true) ?></th>
                    <th><?php echo __('Occurrence', true) ?></th>
                    <th><?php echo __('Status', true) ?></th>
                    <th><?php echo __('Assign to', true) ?></th>
                    <th><?php echo __('Action Related', true) ?></th>
                    <th><?php echo __('Date closing', true) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($projectRisks as $projectRisk) {
                    $dx = $projectRisk['ProjectRisk'];
                    if( empty($statusFilters['risk']) || (!empty($statusFilters['risk']) && (empty($dx['project_issue_status_id']) || (in_array($dx['project_issue_status_id'], $statusFilters['risk'])))) ){
                ?>
                    <tr>
                        <td><?php echo $dx['project_risk'] ?></td>
                        <td><?php echo $riskSeverities[$dx['project_risk_severity_id']] ?></td>
                        <td><?php echo $riskOccurrences[$dx['project_risk_occurrence_id']] ?></td>
                        <td><?php echo !empty($issueStatus[$dx['project_issue_status_id']]) ? $issueStatus[$dx['project_issue_status_id']] : '' ?></td>
                        <td><?php echo !empty($_employees['project'][$dx['risk_assign_to']]) && !empty($dx['risk_assign_to']) ? $_employees['project'][$dx['risk_assign_to']] : '' ?></td>
                        <td><?php echo $dx['actions_manage_risk'] ?></td>
                        <td><?php echo $str_utility->convertToVNDate($dx['risk_close_date']) ?></td>
                    </tr>
                <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
</div>
<!-- problems -->
<div id ="div_issue" style="display: none">
<?php if($showMenu['issue'] && $yourFormFilter['issue'] == 1): ?>
<div data-widget="issue" class="wd-input wd-area wd-none" style = "margin-top: 15px; <?php echo !$showMenu['issue'] ? 'display: none' : '' ?>">
    <div id="project_issue" style="width: 95%">
    </div>
    <div>
        <table id = "absence" style="width: 95%">
            <thead>
                <tr>
                    <th><?php echo __('Blocking', true) ?></th>
                    <th><?php echo __('Issue', true) ?></th>
                    <th><?php echo __('Severity', true) ?></th>
                    <th><?php echo __('Status', true) ?></th>
                    <th><?php echo __('Assign to', true) ?></th>
                    <th><?php echo __('Actions related', true) ?></th>
                    <th><?php echo __('Delivery Date', true) ?></th>
                    <th><?php echo __('Created Date', true) ?></th>
                    <th><?php echo __('Date closing', true) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($projectIssues as $projectIssue) {
                    $dx = $projectIssue['ProjectIssue'];
                    if( empty($statusFilters['issue']) || (!empty($statusFilters['issue']) && (empty($dx['project_issue_status_id']) || (in_array($dx['project_issue_status_id'], $statusFilters['issue'])))) ){
                ?>
                    <tr>
                        <td><?php
                            if( !empty($dx['project_issue_color_id']) && $dx['project_issue_color_id'] != 0 ){
                                $td = '<div style="width: 20px; height: 20px; background-color: '.$listColor[$dx['project_issue_color_id']].'; padding-left: 0px; margin:0 auto;margin-top : 4px;"></div>';
                            } else {
                                $td = '<div style="width: 20px; height: 20px; background-color: '.$colorDefault.'; padding-left: 0px; margin:0 auto;margin-top : 4px;"></div>';
                            }
                            echo $td;
                        ?></td>
                        <td><?php echo $dx['project_issue_problem'] ?></td>
                        <td><?php
                            $output = !empty($dx['project_issue_severity_id']) && !empty($issueSeverities[$dx['project_issue_severity_id']]) ? $issueSeverities[$dx['project_issue_severity_id']] : '';
                            $color = $colorSeverities && $colorSeverities[$dx['project_issue_severity_id']] ? $colorSeverities[$dx['project_issue_severity_id']] : '#004380';
                            $td = '<div style="clear: both"><i class="icon-color" style="float: left; width: 15px; height: 15px; margin-top: 4px; margin-right: 5px; background-color:' . $color . '">&nbsp</i><span>' . $output . '</span></div>';
                            echo $td;
                        ?></td>
                        <td><?php echo !empty($dx['project_issue_status_id']) && !empty($issueStatus[$dx['project_issue_status_id']]) ? $issueStatus[$dx['project_issue_status_id']] : '' ?></td>
                        <!--hien thi project issue assign.-->
                        <td><?php echo !empty($listReference[$dx['id']]) ? $listReference[$dx['id']] : '' ?></td>
                        <td><?php echo $dx['issue_action_related'] ?></td>
                        <td><?php
                            $td = $str_utility->convertToVNDate($dx['delivery_date']);
                            $now = time();
                            if(!empty($dx['delivery_date'])){
                                $sDate = strtotime($dx['delivery_date']);
                                if(($now > $sDate) && !empty($issueStatus[$dx['project_issue_status_id']]) && ($issueStatus[$dx['project_issue_status_id']] != 'CLOS')){
                                    $td = '<div><span class="task_red" style="padding-left: 20px">' . $td . '</span></div>';
                                }
                            }
                            echo $td;
                        ?></td>
                        <td><?php echo $str_utility->convertToVNDate($dx['date_open']) ?></td>
                        <td><?php echo $str_utility->convertToVNDate($dx['date_issue_close']) ?></td>
                    </tr>
                <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
</div>
<!-- localization -->
<?php
$locationTemp = '';
if($showMenu['local_view'] && $yourFormFilter['location'] == 1) :
$key_statics = 'AIzaSyA4rAT0fdTZLNkJ5o0uaAwZ89vVPQpr_Kc';
$_path_img = FILES . 'staticmap.png';
$latlong = $projectName['Project']['latlng'];
$latlong = json_decode($latlong, true);
$_url = 'https://maps.googleapis.com/maps/api/staticmap?center='.$latlong['lat'].','.$latlong['lng'].'&zoom=11&size=1330x500&markers=color:red%7Clabel:C%7C&key=' . $key_statics;
$content = file_get_contents($_url);
file_put_contents($_path_img, $content);
$linkImg = $this->Html->url(array('action' => 'attachment_static', '?' => array('sid' => $api_key)), true);

$locationTemp .= '<div id="div_location" data-widget="location"  class="wd-input wd-area wd-none" style = "margin-top: 15px;' . (!$showMenu['local_view'] ? 'display: none' : '') .'">';
$locationTemp .= '<div class="wd-section" id="wd-fragment-2" style="margin-top: -13px">';
        if($projectLocalView){
            if(!$projectLocalView['ProjectLocalView']['is_file']){
                $is_http = $projectLocalView['ProjectLocalView']['is_https'] ? 'https://' : 'http://';
                $IFRAME = $is_http.$projectLocalView['ProjectLocalView']['attachment'] ;
            }
        }
        $link = $this->Html->url(array('action' => 'attachment', $id, '?' => array('sid' => $api_key)), true);
        if ($projectLocalView && empty($noFileExists)) {
            if (!preg_match('/\.(jpg|jpeg|bmp|gif|png|swf)$/i', $projectLocalView['ProjectLocalView']['attachment'])) {
                $link = 'https://docs.google.com/gview?url=' . ($link) . '&embedded=true';
            }
        }
        if(!isset($IFRAME)) {
            $IFRAME = $link;
        }
$locationTemp .= '<br />';
$locationTemp .= '<input type="text" id="coord-input" value="'.$projectName['Project']['address'].'" size="40" style="display: none">';
$locationTemp .= '<iframe src="' . $IFRAME . '" style="width: 94.7%;height: 400px; border: 1px solid #D8D8D8;" id="local-frame"></iframe>';
$locationTemp .= '<iframe src="about:blank" style="width: 94.7%;height: 500px; border: 1px solid #D8D8D8; display: none" id="map-frame" allowfullscreen></iframe>';
$locationTemp .= '</div>';
if(!empty($latlong)){
    $locationTemp .= '<img id="img_location" style="display: none; width: 1330px; height: 500px; float: left; margin-top: 0px" src="' . $linkImg . '">';
}
$locationTemp .= '</div>';
endif;
$locationTemp .= '';
?>
<!-- dependency -->
<?php
$diagramTemp = '';
$diagramTemp .= '<div id="div_diagram" data-widget="dependency" class="wd-input wd-area wd-none" style = "margin-top: 15px; '. (!$showMenu["dependency"] ? "display: none" : "") .'">';
$diagramTemp .= '<div id="diagram" style="95%">';
$diagramTemp .= '<div class="vis-network" tabindex="900" style="position: relative; overflow: hidden; touch-action: none; -webkit-user-select: none; -webkit-user-drag: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); width: 100%;">';
$diagramTemp .= '<canvas style="position: relative; touch-action: none; -webkit-user-select: none; -webkit-user-drag: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); width: 100%; height: 100%;"></canvas>';
$diagramTemp .= '</div>';
$diagramTemp .= '</div>';
$diagramTemp .= '</div>';
$diagramTemp .= '';

// global view
if(empty($statusFilters['global_view']) || $statusFilters['global_view'] == 0){
    $_style = 'width: auto; height: auto; margin-top: 0; margin-left: 0; float: left;';
} else {
    $_style = 'width: auto; height: 400px; margin-top: 0; float: left;';
}
$globalTemp = '';
$globalTemp .= '<div data-widget="global_view"  class="wd-input wd-area wd-none" style = "margin-top: 15px; ' . (!$showMenu["global_view"] ? "display: none" : "") .'?>">';
$globalTemp .= '<div id="global_view" style="width: 95%; position: relative">';
if($showMenu['global_view'] && $yourFormFilter['global_view'] == 1) :
        if ($projectGlobalView) {
                if(!$projectGlobalView['ProjectGlobalView']['is_file']){
                    $is_http = $projectGlobalView['ProjectGlobalView']['is_https'] ? 'https://' : 'http://';
                    $IFRAME = $is_http.$projectGlobalView['ProjectGlobalView']['attachment'] ;
                }
        }
        $isDoc = false;
        $link = $this->Html->url(array('controller' => 'project_global_views', 'action' => 'attachment', $projectName['Project']['id'], '?' => array('sid' => $api_key)), true);
        if ($projectGlobalView && empty($noFileExists)) {
            if (!preg_match('/\.(jpg|jpeg|bmp|gif|png|swf)$/i', $projectGlobalView['ProjectGlobalView']['attachment'])) {
                $link = 'https://docs.google.com/gview?url=' . ($link) . '&embedded=true';
                $isDoc = true;
            }
        } else {
            $link = '';
        }
        if(!isset($IFRAME)) {
            $IFRAME = $link;
        } else {
            $is_link = 1;
            $IFRAME = $IFRAME;
            $IFRAME_NAME = $IFRAME;
        }
        if(!$isDoc){
$globalTemp .= '<img src="' . $link . '" style="'.$_style.'"></img>';
        } else {
$globalTemp .= '<iframe src="'. $link .'" style="width: 45%;height: 402px;margin-top: 0; position: absolute;"></iframe>';
        }
$globalTemp .= '</div>';
$globalTemp .= '</div>';
endif;
$globalTemp .= '';
?>
<!-- budget internal -->
<div id ="div_internal_cost" style="display: none">
<?php if($showMenu['internal_cost'] && $yourFormFilter['buget_internal'] == 1) : ?>
<div data-widget="buget_internal" class="wd-input wd-area wd-none" style = "margin-top: 15px; <?php echo !$showMenu['internal_cost'] ? 'display: none' : '' ?>">
    <div>
        <table id = "absence" style="width: 95%">
            <thead>
                <tr>
                    <th><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Name', true) ?></th>
                    <th><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Validation date', true) ?></th>
                    <th><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Budget €', true) ?></th>
                    <th><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Forecast €', true) ?></th>
                    <th><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Var % (€)', true) ?></th>
                    <th><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Engaged €', true) ?></th>
                    <th><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Remain €', true) ?></th>
                    <th><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Budget M.D', true) ?></th>
                    <th><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Forecast M.D', true) ?></th>
                    <th><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Var % (' . ' M.D)', true) ?></th>
                    <th><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Engaged M.D', true) ?></th>
                    <th><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Remain M.D', true) ?></th>
                    <th><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Average daily rate €', true) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $httr = '';
                $total_budget_euro = $total_budget_md = $totalAve = $count = 0;
                foreach ($budgets as $budget) {
                    $dx = $budget['ProjectBudgetInternalDetail'];
                    $httr .= '<tr>';
                    $httr .= '<td> ' . $dx['name'].'</td>';

                    $budget_md = !empty($dx['budget_md']) ? $dx['budget_md'] : 0;
                    $averages = !empty($dx['average']) ? $dx['average'] : 0;
                    $httr .= '<td>' . $str_utility->convertToVNDate($dx['validation_date']) . '</td>';
                    $httr .= '<td style="float: right;">'. number_format($averages*$budget_md, 2, ',', ' ') . ' €' .'</td>';
                    $total_budget_euro += $averages*$budget_md;
                    $total_budget_md += $budget_md;
                    $count++;
                    $totalAve += $averages;
                    $httr .= '<td></td>';
                    $httr .= '<td></td>';
                    $httr .= '<td></td>';
                    $httr .= '<td></td>';
                    $httr .= '<td style="text-align: right;">' . number_format($dx['budget_md'], 2, ',', ' ') . ' ' . __('M.D', true) .'</td>';
                    $httr .= '<td></td>';
                    $httr .= '<td></td>';
                    $httr .= '<td></td>';
                    $httr .= '<td></td>';
                    $httr .= '<td style="text-align: right;">' . number_format($averages, 2, ',', ' ') . ' €' .'</td>';
                    $httr .= '</tr>';
                }
                $avgAve = $count > 0 ? round($totalAve/$count, 2) : 0;
                $remain_md = $getDataProjectTasks['remain'] ? ($getDataProjectTasks['remain'] - ($externalBudgets - $externalConsumeds)) : 0;
                $consumed_md = $getDataProjectTasks['consumed'] ? $getDataProjectTasks['consumed'] : 0;
                $forecast_md = $remain_md + $consumed_md;
                $remain_erro = $remain_md*$avgAve;
                $forecast_erro = $engagedErro + $remain_erro;
                $varmd = $total_budget_md > 0 ? ($forecast_md/$total_budget_md-1)*100 : 0;
                $vareurro = $total_budget_euro > 0 ? ($forecast_erro/$total_budget_euro-1)*100 : 0;
                ?>
                <tr style="background-color: #E8F0FA">
                    <td><?php echo __('Total', true) ?></td>
                    <td></td>
                    <td style="text-align: right;"><?php echo number_format($total_budget_euro, 2, ',', ' ') . ' €' ?></td>
                    <td style="text-align: right;"><?php echo number_format($forecast_erro, 2, ',', ' ') . ' €' ?></td>
                    <td style="text-align: right;"><?php echo number_format($vareurro, 2, ',', ' ') . ' %' ?></td>
                    <td style="text-align: right;"><?php echo number_format($engagedErro, 2, ',', ' ') . ' €' ?></td>
                    <td style="text-align: right;"><?php echo number_format($remain_erro, 2, ',', ' ') . ' €' ?></td>
                    <td style="text-align: right;"><?php echo number_format($total_budget_md, 2, ',', ' ') . ' ' . __('M.D', true) ?></td>
                    <td style="text-align: right;"><?php echo number_format($forecast_md, 2, ',', ' ') . ' ' . __('M.D', true) ?></td>
                    <td style="text-align: right;"><?php echo number_format($varmd, 2, ',', ' ') . ' %' ?></td>
                    <td style="text-align: right;"><?php echo number_format($consumed_md, 2, ',', ' ') . ' ' . __('M.D', true) ?></td>
                    <td style="text-align: right;"><?php echo number_format($remain_md, 2, ',', ' ') . ' ' . __('M.D', true) ?></td>
                    <td style="text-align: right;"><?php echo number_format($avgAve, 2, ',', ' ') . ' ' . __('M.D', true) ?></td>
                </tr>
                <?php
                echo $httr;
                ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
</div>
<!-- budget external -->
<div id="div_external_cost" style="display: none">
<?php if($showMenu['external_cost'] && $yourFormFilter['budget_externals'] == 1) : ?>
<div data-widget="budget_externals" class="wd-input wd-area wd-none" style = "margin-top: 15px; <?php echo !$showMenu['external_cost'] ? 'display: none' : '' ?>">
    <div>
        <table id = "absence" style="width: 95%">
            <thead>
                <tr>
                    <th><?php echo __d(sprintf($_domain, 'External_Cost'), 'Name', true) ?></th>
                    <th><?php echo __d(sprintf($_domain, 'External_Cost'), 'Order date', true) ?></th>
                    <!-- <th><?php echo __d(sprintf($_domain, 'External_Cost'), 'Provider', true) ?></th> -->
                    <th><?php echo __d(sprintf($_domain, 'External_Cost'), 'Type', true) ?></th>
                    <th><?php echo __d(sprintf($_domain, 'External_Cost'), 'CAPEX/OPEX', true) ?></th>
                    <th><?php echo __d(sprintf($_domain, 'External_Cost'), 'Budget €', true) ?></th>
                    <th><?php echo __d(sprintf($_domain, 'External_Cost'), 'Forecast €', true) ?></th>
                    <th><?php echo __d(sprintf($_domain, 'External_Cost'), 'Ordered €', true) ?></th>
                    <th><?php echo __d(sprintf($_domain, 'External_Cost'), 'Remain €', true) ?></th>
                    <th><?php echo __d(sprintf($_domain, 'External_Cost'), 'M.D', true) ?></th>
                    <th><?php echo __d(sprintf($_domain, 'External_Cost'), 'Consumed', true) ?></th>
                    <th><?php echo __d(sprintf($_domain, 'External_Cost'), 'Progress %', true) ?></th>
                    <th><?php echo __d(sprintf($_domain, 'External_Cost'), 'Progress €', true) ?></th>
                    <th><?php echo __d(sprintf($_domain, 'External_Cost'), 'Attachement or URL', true) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $httr = '';
                $total_budget_euro = $total_forecast_euro = $total_ordered_erro = $total_remain_erro = $total_man_day = $total_special_consume = $total_progress_euro = 0;
                foreach ($budgetExternals as $budgetExternal) {
                    $dx = $budgetExternal['ProjectBudgetExternal'];
                    $_id = $dx['id'];
                    $totalconsumed = !empty($taskExternals[$_id]['consumed']) ? $taskExternals[$_id]['consumed'] : '';
                    $ordered_erro = !empty($dx['ordered_erro']) ? $dx['ordered_erro'] : 0;
                    $remain_erro = !empty($dx['remain_erro']) ? $dx['remain_erro'] : 0;
                    $budget_erro = !empty($dx['budget_erro']) ? $dx['budget_erro'] : 0;
                    $total_budget_euro += $budget_erro;
                    $forecast_erro = $ordered_erro+$remain_erro;
                    $total_forecast_euro += $forecast_erro;
                    $total_ordered_erro += $ordered_erro;
                    $total_remain_erro += $remain_erro;
                    $total_man_day += !empty($dx['man_day']) ? $dx['man_day'] : 0;
                    $total_special_consume += $totalconsumed;
                    $total_progress_euro += !empty($dx['progress_erro']) ? $dx['progress_erro'] : 0;
                    if($budget_erro == 0){
                        $var_erro = (0-1)*100;
                    } else {
                        $var_erro = round((($forecast_erro/$budget_erro)-1)*100, 2);
                    }
                    $httr .= '<tr>';
                        $httr .= '<td>'.$dx['name'] .'</td>';
                        $httr .= '<td>'.$str_utility->convertToVNDate($dx['order_date']) .'</td>';
                        $httr .= '<td>'. (!empty($dx['budget_type_id']) && !empty($budgetTypes[$dx['budget_type_id']]) ? $budgetTypes[$dx['budget_type_id']] : '').'</td>';
                        $httr .= '<td style="text-align: right;">'. (!empty($dx['budget_erro']) ? number_format($dx['budget_erro'], 2, ',', ' ') . ' €' : '0.00 €').'</td>';
                        $httr .= '<td style="text-align: right;">'. (!empty($forecast_erro) ? number_format($forecast_erro, 2, ',', ' ') . ' €' : '0.00 €').'</td>';
                        $httr .= '<td style="text-align: right;">'. (!empty($var_erro) ? number_format($var_erro, 2, ',', ' ') . ' %' : '0.00 %') .'</td>';
                        $httr .= '<td style="text-align: right;">'.(!empty($dx['ordered_erro']) ? number_format($dx['ordered_erro'], 2, ',', ' ') . ' €' : '0.00 €') .'</td>';
                        $httr .= '<td style="text-align: right;">'. (!empty($dx['remain_erro']) ? number_format($dx['remain_erro'], 2, ',', ' ') . ' €' : '0.00 €') .'</td>';
                        $httr .= '<td style="text-align: right;">'. (!empty($dx['man_day']) ? number_format($dx['man_day'], 2, ',', ' ') . ' ' . __('M.D', true) : '0.00 ' . __('M.D', true)) .'</td>';
                        $httr .= '<td style="text-align: right;">'. (!empty($totalconsumed) ? number_format($totalconsumed,2,',',' ') . ' ' . __('M.D', true) : '0.00 ' . __('M.D', true)) .'</td>';
                        $httr .= '<td style="text-align: right;">'. (!empty($dx['progress_md']) ? number_format($dx['progress_md'], 2, ',', ' ') . ' %' : '0.00 %') .'</td>';
                        $httr .= '<td style="text-align: right;">'. (!empty($dx['progress_erro']) ? number_format($dx['progress_erro'], 2, ',', ' ') . ' €' : '0.00 €') .'</td>';
                        $httr .= '<td style="text-align: right;">'. $dx['file_attachement'] .'</td>';
                    $httr .= '</tr>';
                }
                $vareurro = $total_budget_euro > 0 ? round(($total_forecast_euro/$total_budget_euro -1)*100, 2) : 0;
                $varmd = $total_ordered_erro > 0 ?  round(($total_progress_euro/$total_ordered_erro)*100, 2) : 0;
                ?>
                <tr style="background-color: #E8F0FA">
                    <td><?php echo __('Total', true) ?></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: right;"><?php echo number_format($total_budget_euro, 2, ',', ' ') . ' €' ?></td>
                    <td style="text-align: right;"><?php echo number_format($total_forecast_euro, 2, ',', ' ') . ' €' ?></td>
                    <td style="text-align: right;"><?php echo number_format($vareurro, 2, ',', ' ') . ' %' ?></td>
                    <td style="text-align: right;"><?php echo number_format($total_ordered_erro, 2, ',', ' ') . ' €' ?></td>
                    <td style="text-align: right;"><?php echo number_format($total_remain_erro, 2, ',', ' ') . ' €' ?></td>
                    <td style="text-align: right;"><?php echo number_format($total_man_day, 2, ',', ' ') . ' ' . __('M.D', true)?></td>
                    <td style="text-align: right;"><?php echo number_format($total_special_consume, 2, ',', ' ') . ' ' . __('M.D', true) ?></td>
                    <td style="text-align: right;"><?php echo number_format($varmd, 2, ',', ' ') . ' %' ?></td>
                    <td style="text-align: right;"><?php echo number_format($total_progress_euro, 2, ',', ' ') . ' €' ?></td>
                    <td></td>
                </tr>
                <?php
                echo $httr;
                ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
<div>
<!-- finance+ -->
<div id ="div_finance_plus" style="display: none">
    <?php if(isset($showMenu['finance_plus']) && $showMenu['finance_plus'] && $yourFormFilter['finance_plus'] == 1) : ?>
    <div data-widget="finance_plus" id = "budget-chard" style="padding-top: 30px; clear:both; width: 95%; <?php echo !$showMenu['finance_plus'] ? 'display: none' : '' ?>">
        <div id="inve-chard" style="float: left; width: 50%">
            <h3 class="wd-t1" style="color: #ffb250; margin-bottom: 10px;"><?php echo __d(sprintf($_domain, 'Finance'), 'Budget Investment', true); ?></h3>
            <div class="chard-content">
                <div class="budget-chard" style="display: none">
                    <p><?php echo __('Budget', true) .': '. number_format((!empty($totals['inv']['budget']) ? $totals['inv']['budget'] : 0), 2, '.', ' ') . ' €' ?></p>
                    <p><?php echo __('Engaged', true) .': '. number_format((!empty($totals['inv']['avancement']) ? $totals['inv']['avancement'] : 0), 2, '.', ' ')  . ' €' ?></p>
                    <?php
                    if(empty($totals['inv']['budget'])){
                        $totals['inv']['budget'] = 0;
                    }
                    if(empty($totals['inv']['avancement'])){
                        $totals['inv']['avancement'] = 0;
                    }if($totals['inv']['budget'] == 0) {
                        $per = 100;
                    } else {
                        $per = round($totals['inv']['avancement']/$totals['inv']['budget'] * 100,2);
                    }
                    $color_min = '#13FF02';
                    $color_max = '#15830D';
                    if( $totals['inv']['budget'] == 0 && $totals['inv']['avancement'] == 0 ){
                        $width_bud = '0%';
                        $width_avan = '0';
                        $bg_color = 'green';
                        $per = 0;
                    } else if( $totals['inv']['budget'] == 0 ){
                        $width_bud = '0%';
                        $width_avan = '80';
                        $bg_color = 'green';
                    } else if( (($totals['inv']['avancement'] > $totals['inv']['budget']) && $totals['inv']['avancement'] > 0) || (($totals['inv']['avancement'] > 0) && ($totals['inv']['budget'] <= 0)) ){
                        $color_min = '#F98E8E';
                        $color_max = '#FF0606';
                        $bg_color = 'red';
                        $width_bud = '80%';
                        $width_avan = (abs($totals['inv']['avancement'])/abs($totals['inv']['budget'])*80);
                    } else {
                        $width_bud = '80%';
                        $width_avan = (abs($totals['inv']['avancement'])/abs($totals['inv']['budget'])*80);
                        $bg_color = 'green';
                    }
                    $width_avan = $width_avan <= 100 ? $width_avan : 100;
                    $width_avan = $width_avan . '%';
                    ?>
                </div>
                 <aside class="budget-progress-circle" style="overflow:visible; margin-top: 32px">

                    <div class="progress-circle progress-circle-yellow">
                        <div class="progress-circle-inner">
                            <i class="icon-question" aria-hidden="true"></i>
                            <canvas data-value = "<?php echo $per; ?>" id="myCanvas-2" width="165" height="160" style="" class="canvas-circle"></canvas>
                            <div class ="progress-value progress-validated"><p><?php echo  __('Budget', true);?></p><span><?php echo number_format((!empty($totals['inv']['budget']) ? $totals['inv']['budget'] : 0), 2, '.', ' '); ?> € </span></div>
                            <div class ="progress-value progress-engaged"><p><?php echo  __('Engaged', true);?></p><span><?php echo number_format((!empty($totals['inv']['avancement']) ? $totals['inv']['avancement'] : 0), 2, '.', ' '); ?> € </span></div>
                        </div>
                    </div>
                </aside>
                <div class="percent-chard" style="display: none">
                    <div style="width: 50%">
                        <?php if($totals['inv']['budget'] < 0){ ?>
                        <div style="height: 10px; width: <?php echo $width_bud ?>; background-color: #ccc; float: right"></div>
                        <?php } else { ?>
                        <div style="height: 10px; width: 0px; background-color: #ccc; float: right"></div>
                        <?php }
                        if($totals['inv']['avancement'] < 0){
                        ?>
                        <div style="margin-top: 25px; height: 10px; float: right; width: <?php echo $width_avan ?>; background-color: <?php echo $bg_color ?>"></div>
                        <?php } ?>
                    </div>
                    <div style="width: 50%; margin-left: 50%; position: relative; top: 3px">
                        <?php if($totals['inv']['budget'] >= 0){ ?>
                        <div style="height: 10px; width: <?php echo $width_bud ?>; background-color: #ccc"></div>
                        <?php } else { ?>
                        <div style="height: 10px; width: 0px; background-color: #ccc"></div>
                        <?php }
                        if($totals['inv']['avancement'] >= 0){
                        ?>
                        <div style="margin-top: 14px; height: 10px; width: <?php echo $width_avan ?>; background-color: <?php echo $bg_color ?>"></div>
                        <?php } ?>
                    </div>
                </div>
                <div class="circle-chard" style="display: none">
                    <svg id="svg_inve_chard" class="progress-pie" width="100%" height="100%" role="image" style="">
                        <defs>
                            <filter id="drop-shadow" x="-50%" y="-50%" width="200%" height="200%">
                                <feOffset result="offOut" in="SourceAlpha" dx="0" dy="0" />
                                <feGaussianBlur result="blurOut" in="offOut" stdDeviation="1" />
                                <feBlend in="SourceGraphic" in2="blurOut" mode="normal" />
                            </filter>
                            <!-- inner circle shadow filter -->
                            <filter id="drop-shadow-flat" x="-50%" y="-50%" width="200%" height="200%">
                                <feGaussianBlur in="SourceAlpha" stdDeviation="1" result="A" />
                                <feBlend in="SourceGraphic" in2="A" mode="normal" />
                            </filter>
                            <!-- shadow under progress ring -->
                            <filter id="inset-shadow" x="-50%" y="-50%" width="200%" height="200%">
                                <femorphology in="SourceAlpha" operator="erode" radius="0.5" />
                                <feComponentTransfer>
                                    <feFuncA type="table" tableValues="1 0" />
                                </feComponentTransfer>
                                <feGaussianBlur stdDeviation="1" />
                                <feOffset dx="0" dy="0" result="offsetblur" />
                                <feFlood flood-color="rgb(0, 0, 0)" result="color" />
                                <feComposite in2="offsetblur" operator="in" />
                                <feComposite in2="SourceAlpha" operator="in" />
                            </filter>
                            <linearGradient id="pprg1" class="progress-pie__gradient">
                                <stop offset="0%" stop-color="<?php echo $color_min ?>" />
                                <stop offset="100%" stop-color="<?php echo $color_max ?>" />
                            </linearGradient>
                        </defs>
                        <circle class="progress-pie__bg" filter="url(#drop-shadow)" r="50" cx="50" cy="50" fill="grey" opacity="0.3" />
                        <circle stroke="grey" stroke-width="9" fill="none" r="39.5" cx="50" cy="50" opacity="0.3" filter="url(#inset-shadow)" />
                        <circle class="progress-pie__ring" stroke="url(#pprg1)" stroke-width="9" stroke-dasharray="<?php echo abs($per/100*248.1858154) ?> 248.1858154" fill="none" r="39.5" cx="50" cy="50" transform="rotate(90, 50, 50)" />
                        <circle class="progress-pie__inner-disc" fill="grey" opacity="0.2" r="35" cx="50" cy="50" filter="url(#drop-shadow-flat)" />
                        <text class="progress-pie__text" x="50" y="57" text-anchor="middle" font-size="18"><?php echo $per ?><tspan font-size="15" dy="-5">%</tspan></text>
                    </svg>
                </div>
            </div>
            <canvas id="canvas" style="display: none; width: 200px; height: 200px"></canvas>
            <div id="png-container"></div>
        </div>
        <div id="fon-chard" style="float: right; width: 50%">
            <h3 class="wd-t1" style="color: #ffb250; margin-bottom: 10px;"><?php echo __d(sprintf($_domain, 'Finance'), 'Budget Operation', true); ?></h3>
            <div class="chard-content">
                <div class="budget-chard" style="display: none">
                    <p><?php echo __('Budget', true) .': '. number_format((!empty($totals['fon']['budget']) ? $totals['fon']['budget'] : 0), 2, '.', ' ') . ' €' ?></p>
                    <p><?php echo __('Engaged', true) .': '. number_format((!empty($totals['fon']['avancement']) ? $totals['fon']['avancement'] : 0), 2, '.', ' ') . ' €' ?></p>
                    <?php
                    if(empty($totals['fon']['budget'])){
                        $totals['fon']['budget'] = 0;
                    }
                    if(empty($totals['fon']['avancement'])){
                        $totals['fon']['avancement'] = 0;
                    }
                    if($totals['fon']['budget'] == 0) {
                        $per = 100;
                    } else {
                        $per = round($totals['fon']['avancement']/$totals['fon']['budget'] * 100,2);
                    }
                    $_color_min = '#13FF02';
                    $_color_max = '#15830D';
                    if( $totals['fon']['budget'] == 0 && $totals['fon']['avancement'] == 0  ){
                        $width_bud = '0%';
                        $width_avan = '0';
                        $bg_color = 'green';
                        $per = 0;
                    } else if( $totals['fon']['budget'] == 0 ){
                        $width_bud = '0%';
                        $width_avan = '80';
                        $bg_color = 'green';
                    } else if( (($totals['fon']['avancement'] > $totals['fon']['budget']) && $totals['fon']['avancement'] > 0) || (($totals['fon']['avancement'] > 0) && ($totals['fon']['budget'] <= 0)) ){
                        $_color_min = '#F98E8E';
                        $_color_max = '#FF0606';
                        $bg_color = 'red';
                        $width_bud = '80%';
                        $width_avan = (abs($totals['fon']['avancement'])/abs($totals['fon']['budget'])*80);
                    } else {
                        $width_bud = '80%';
                        $width_avan = (abs($totals['fon']['avancement'])/abs($totals['fon']['budget'])*80);
                        $bg_color = 'green';
                    }
                    $width_avan = $width_avan <= 100 ? $width_avan : 100;
                    $width_avan = $width_avan . '%';
                    ?>
                </div>
                <div class="percent-chard" style="display: none">
                    <div style="width: 50%">
                        <?php if($totals['fon']['budget'] < 0){ ?>
                        <div style="height: 10px; width: <?php echo $width_bud ?>; background-color: #ccc; float: right"></div>
                        <?php } else { ?>
                        <div style="height: 10px; width: 0px; background-color: #ccc; float: right"></div>
                        <?php }
                        if($totals['fon']['avancement'] < 0){
                        ?>
                        <div style="margin-top: 25px; height: 10px; float: right; width: <?php echo $width_avan ?>; background-color: <?php echo $bg_color ?>"></div>
                        <?php } ?>
                    </div>
                    <div style="width: 50%; margin-left: 185px; position: relative; top: 3px">
                        <?php if($totals['fon']['budget'] >= 0){ ?>
                        <div style="height: 10px; width: <?php echo $width_bud ?>; background-color: #ccc"></div>
                        <?php } else { ?>
                        <div style="height: 10px; width: 0px; background-color: #ccc"></div>
                        <?php }
                        if($totals['fon']['avancement'] >= 0){
                        ?>
                        <div style="margin-top: 14px; height: 10px; width: <?php echo $width_avan ?>; background-color: <?php echo $bg_color ?>"></div>
                        <?php } ?>
                    </div>
                </div>
                <div class="circle-chard" style="display: none">
                    <div id='gaugeProfit'>
                        <svg id="svg_circle_chard" class="progress-pie" width="100%" height="100%" role="image" style="">
                            <defs>
                                <filter id="drop-shadow" x="-50%" y="-50%" width="200%" height="200%">
                                    <feOffset result="offOut" in="SourceAlpha" dx="0" dy="0" />
                                    <feGaussianBlur result="blurOut" in="offOut" stdDeviation="1" />
                                    <feBlend in="SourceGraphic" in2="blurOut" mode="normal" />
                                </filter>
                                <!-- inner circle shadow filter -->
                                <filter id="drop-shadow-flat" x="-50%" y="-50%" width="200%" height="200%">
                                    <feGaussianBlur in="SourceAlpha" stdDeviation="1" result="A" />
                                    <feBlend in="SourceGraphic" in2="A" mode="normal" />
                                </filter>
                                <!-- shadow under progress ring -->
                                <filter id="inset-shadow" x="-50%" y="-50%" width="200%" height="200%">
                                    <femorphology in="SourceAlpha" operator="erode" radius="0.5" />
                                    <feComponentTransfer>
                                        <feFuncA type="table" tableValues="1 0" />
                                    </feComponentTransfer>
                                    <feGaussianBlur stdDeviation="1" />
                                    <feOffset dx="0" dy="0" result="offsetblur" />
                                    <feFlood flood-color="rgb(0, 0, 0)" result="color" />
                                    <feComposite in2="offsetblur" operator="in" />
                                    <feComposite in2="SourceAlpha" operator="in" />
                                </filter>
                                <linearGradient id="pprg2" class="progress-pie__gradient">
                                    <stop offset="0%" stop-color="<?php echo $_color_min ?>" />
                                        <stop offset="100%" stop-color="<?php echo $_color_max ?>" />
                                </linearGradient>
                            </defs>
                            <circle class="progress-pie__bg" filter="url(#drop-shadow)" r="50" cx="50" cy="50" fill="grey" opacity="0.3" />
                            <circle stroke="grey" stroke-width="9" fill="none" r="39.5" cx="50" cy="50" opacity="0.3" filter="url(#inset-shadow)" />
                            <circle class="progress-pie__ring" stroke="url(#pprg2)" stroke-width="9" stroke-dasharray="<?php echo abs($per/100*248.1858154) ?> 248.1858154" fill="none" r="39.5" cx="50" cy="50" transform="rotate(90, 50, 50)" />
                            <circle class="progress-pie__inner-disc" fill="grey" opacity="0.2" r="35" cx="50" cy="50" filter="url(#drop-shadow-flat)" />
                            <text class="progress-pie__text" x="50" y="57" text-anchor="middle" font-size="18"><?php echo $per ?><tspan font-size="15" dy="-5">%</tspan></text>
                        </svg>
                    </div>
                    <canvas id="canvas1" style="display: none; width: 200px; height: 200px"></canvas>
                    <div id="png-container1"></div>
                </div>
                <aside class="budget-progress-circle" style="overflow:visible; margin-top: 32px">
                    <div class="progress-circle progress-circle-yellow">
                        <div class="progress-circle-inner">
                            <i class="icon-question" aria-hidden="true"></i>
                            <canvas data-value = "<?php echo $per; ?>" id="myCanvas-3" width="165" height="160" style="" class="canvas-circle"></canvas>
                            <div class ="progress-value progress-validated"><p><?php echo  __('Budget', true);?></p><span><?php echo number_format((!empty($totals['fon']['budget']) ? $totals['fon']['budget'] : 0), 2, '.', ' '); ?> €</span></div>
                            <div class ="progress-value progress-engaged"><p><?php echo  __('Engaged', true);?></p><span><?php echo number_format((!empty($totals['fon']['avancement']) ? $totals['fon']['avancement'] : 0), 2, '.', ' '); ?> €</span></div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>

<?php
$check = !empty($statusFilters['finance_plus']) ? $statusFilters['finance_plus'][0] : 0;
$displayNone = !$showMenu['finance_plus'] || ($check == 1) ? 'display: none' : '';
?>
<div class="wd-input wd-area wd-none" <?php if(empty($dataFinan['inv_year'])){ echo 'style="display: none"';} ?>  style = "margin-top: 15px; <?php echo $displayNone ?>">
    <div style="width: 95%; overflow-x: auto;">
        <?php  if(!empty($dataFinan['inv_year'])){ ?>
        <table id = "absence" style="width: auto;">
            <thead>
                <tr>
                    <th></th>
                    <th colspan="3"><?php echo __('Total', true) ?></th>
                    <?php foreach ($dataFinan['inv_year'] as $v) { ?>
                        <th colspan="3"><?php echo $v ?></th>
                    <?php } ?>
                </tr>
                <tr>
                    <th style="width: 200px;"><?php echo __('Name', true) ?></th>
                    <th style="width: 80px;"><?php echo __d(sprintf($_domain, 'Finance'), 'Budget', true) ?></th>
                    <th style="width: 80px;"><?php echo __d(sprintf($_domain, 'Finance'), 'Avancement', true) ?></th>
                    <th style="width: 60px;"><?php echo __('%', true) ?></th>
                    <?php
                    $_budget = array();
                    foreach ($dataFinan['inv_year'] as $v) { ?>
                        <th style="width: 80px;"><?php echo __d(sprintf($_domain, 'Finance'), 'Budget', true) ?></th>
                        <th style="width: 80px;"><?php echo __d(sprintf($_domain, 'Finance'), 'Avancement', true) ?></th>
                        <th style="width: 60px;"><?php echo __('%', true) ?></th>
                    <?php
                    $_budget[$v]['budget'] = $_budget[$v]['avancement'] = 0;
                    } ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $httr = '';
                $_budet_total = $_avance_total = 0;
                foreach ($dataFinan['inv'] as $key => $value) {
                    $httr .= '<tr>';
                    $httr .= '<td>'.$finances['inv'][$key].'</td>';
                    // xu ly Total
                    $httr .= '<td style="text-align: right;">'.number_format($value['total']['budget'],2,',',' ') . ' €' . '</td>';
                    $_budet_total += $value['total']['budget'];
                    $httr .= '<td style="text-align: right;">'.number_format($value['total']['avancement'],2,',',' ') . ' €' . '</td>';
                    $_avance_total += $value['total']['avancement'];
                    $per = $value['total']['budget'] > 0 ? number_format(round(($value['total']['avancement']/$value['total']['budget'])*100),2,',', ' ') . ' %' : '';
                    $httr .= '<td style="text-align: right;">'.$per.'</td>';
                    //end
                    unset($value['total']);
                    ksort($value);
                    foreach ($value as $k => $val) {
                        $httr .= '<td style="text-align: right;">'. (!empty($val['budget']) ? number_format($val['budget'],2,',',' ') : 0.00) . ' €' . '</td>';
                        $_budget[$k]['budget'] += $val['budget'];
                        $httr .= '<td style="text-align: right;">'.(!empty($val['avancement']) ? number_format($val['avancement'],2,',',' ') : 0.00) . ' €' . '</td>';
                        $_budget[$k]['avancement'] += (!empty($val['avancement']) ? $val['avancement'] : 0);
                        $per = (!empty($val['avancement']) && !empty($val['budget']) && $val['budget'] > 0) ? number_format(round(($val['avancement']/$val['budget'])*100),2,',', ' ') . ' %' : '';
                        $httr .= '<td style="text-align: right;">'.$per.'</td>';
                    }
                    $httr .= '</tr>';
                }
                ksort($_budget);
                ?>
                <tr style="background-color: #E8F0FA">
                    <td></td>
                    <td style="text-align: right;"><?php  echo number_format($_budet_total,2,',',' ') . ' €' ?></td>
                    <td style="text-align: right;"><?php  echo number_format($_avance_total,2,',',' ') . ' €' ?></td>
                    <td style="text-align: right;"><?php  echo $_budet_total > 0 ? number_format(round(($_avance_total/$_budet_total)*100),2,',', ' ') . ' %' : '' ?></td>
                    <?php foreach ($_budget as $value) { ?>
                        <td style="text-align: right;"><?php  echo number_format($value['budget'],2,',',' ') . ' €' ?></td>
                        <td style="text-align: right;"><?php  echo number_format($value['avancement'],2,',',' ') . ' €' ?></td>
                        <td style="text-align: right;"><?php  echo $value['budget'] > 0 ? number_format(round(($value['avancement']/$value['budget'])*100),2,',', ' ') . ' %' : '' ?></td>
                    <?php } ?>
                </tr>
                <?php
                echo $httr;
                ?>
            </tbody>
        </table>
        <?php } ?>
    </div>
</div>

<div class="wd-input wd-area wd-none" <?php if(empty($dataFinan['inv_year'])){ echo 'style="display: none"';} ?> style = "margin-top: 15px; <?php echo $displayNone ?>">
    <div style="width: 95%; overflow-x: auto;">
        <?php  if(!empty($dataFinan['fon_year'])){ ?>
        <table id = "absence" style="width: auto;">
            <thead>
                <tr>
                    <th></th>
                    <th colspan="3"><?php echo __('Total', true) ?></th>
                    <?php foreach ($dataFinan['fon_year'] as $v) { ?>
                        <th colspan="3"><?php echo $v ?></th>
                    <?php } ?>
                </tr>
                <tr>
                    <th style="width: 200px;"><?php echo __('Name', true) ?></th>
                    <th style="width: 80px;"><?php echo __d(sprintf($_domain, 'Finance'), 'Budget', true) ?></th>
                    <th style="width: 80px;"><?php echo __d(sprintf($_domain, 'Finance'), 'Avancement', true) ?></th>
                    <th style="width: 60px;"><?php echo __('%', true) ?></th>
                    <?php
                    $budget = array();
                    foreach ($dataFinan['fon_year'] as $v) { ?>
                        <th style="width: 80px;"><?php echo __d(sprintf($_domain, 'Finance'), 'Budget', true) ?></th>
                        <th style="width: 80px;"><?php echo __d(sprintf($_domain, 'Finance'), 'Avancement', true) ?></th>
                        <th style="width: 60px;"><?php echo __('%', true) ?></th>
                    <?php
                    $_budget[$v]['budget'] = $_budget[$v]['avancement'] = 0;
                    } ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $httr = '';
                $_budet_total = $_avance_total = 0;
                foreach ($dataFinan['fon'] as $key => $value) {
                    $httr .= '<tr>';
                    $httr .= '<td>'.$finances['fon'][$key].'</td>';
                    // xu ly Total
                    $httr .= '<td style="text-align: right;">'.number_format($value['total']['budget'],2,',',' ') . ' €' . '</td>';
                    $_budet_total += $value['total']['budget'];
                    $httr .= '<td style="text-align: right;">'.number_format($value['total']['avancement'],2,',',' ') . ' €' . '</td>';
                    $_avance_total += $value['total']['avancement'];
                    $per = $value['total']['budget'] > 0 ? number_format(round(($value['total']['avancement']/$value['total']['budget'])*100),2,',', ' ') . ' %' : '';
                    $httr .= '<td style="text-align: right;">'.$per.'</td>';
                    //end
                    unset($value['total']);
                    ksort($value);
                    foreach ($value as $k => $val) {
                        $httr .= '<td style="text-align: right;">'. (!empty($val['budget']) ? number_format($val['budget'],2,',',' ') : 0.00) . ' €' . '</td>';
                        $_budget[$k]['budget'] += !empty($val['budget']) ? $val['budget'] : 0;
                        $httr .= '<td style="text-align: right;">'.(!empty($val['avancement']) ? number_format($val['avancement'],2,',',' ') : 0.00 ). ' €' . '</td>';
                        $_budget[$k]['avancement'] += !empty($val['avancement']) ? $val['avancement'] : 0;
                        $per = !empty($val['avancement']) && !empty($val['budget']) && $val['budget'] > 0 ? number_format(round(($val['avancement']/$val['budget'])*100),2,',', ' ') . ' %' : '';
                        $httr .= '<td style="text-align: right;">'.$per.'</td>';
                    }
                    $httr .= '</tr>';
                }
                ksort($_budget);
                ?>
                <tr style="background-color: #E8F0FA">
                    <td></td>
                    <td style="text-align: right;"><?php  echo number_format($_budet_total,2,',',' ') . ' €' ?></td>
                    <td style="text-align: right;"><?php  echo number_format($_avance_total,2,',',' ') . ' €' ?></td>
                    <td style="text-align: right;"><?php  echo $_budet_total > 0 ? number_format(round(($_avance_total/$_budet_total)*100),2,',', ' ') . ' %' : '' ?></td>
                    <?php foreach ($_budget as $value) { ?>
                        <td style="text-align: right;"><?php  echo number_format($value['budget'],2,',',' ') . ' €' ?></td>
                        <td style="text-align: right;"><?php  echo number_format($value['avancement'],2,',',' ') . ' €' ?></td>
                        <td style="text-align: right;"><?php  echo $value['budget'] > 0 ? number_format(round(($value['avancement']/$value['budget'])*100),2,',', ' ') . ' %' : '' ?></td>
                    <?php } ?>
                </tr>
                <?php
                echo $httr;
                ?>
            </tbody>
        </table>
        <?php } ?>
    </div>
</div>
<?php endif; ?>
</div>
<!-- project tasks -->
<div id="div_project_task" style="display: none">
<?php if($showMenu['task'] && $yourFormFilter['project_task'] == 1) :
    $isManual = isset($companyConfigs['manual_consumed']) ? $companyConfigs['manual_consumed'] : 0;
    $head = array();
    foreach($orders as $key){
        list($word, $show) = explode('|', $key);
        if( $word == 'Order' || (!$isManual && $word == 'ManualConsumed') || !intval($show) )continue;
        if( in_array($word, array('Initialstartdate', 'Initialworkload', 'Initialenddate')) && $projectName['Project']['off_freeze'] == 0 )continue;
        $head[] = $i18n[$word];
    }
?>
<div data-widget="project_task" class="wd-input wd-area wd-none" style = "margin-top: 15px; <?php echo !$showMenu['task'] ? 'display: none' : '' ?>">
    <div id="project_task" style="width: 95%; overflow: auto;">
        <table id = "absence" style="width: auto">
            <thead>
                <tr>
                    <?php
                    foreach($head as $hea) {
                        echo '<th style="white-space: nowrap;">' . $hea . '</th>';
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                //
                function toData($isManual, $projectName, $orders, $pid, $taskName, $prio, $status, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain, $amount, $progress_order, $progress_order_amount, $id_activity, $wait, $slider, $text,
                 $unit_price, $consumed_euro, $remain_euro, $workload_euro, $workloadInitial = null,$startInitial = null,$endInitial = null){
                    // echo '<td style="text-align: right; width: 30px;">'.$pid.'</td>';
                    foreach($orders as $key){
                        list($word, $show) = explode('|', $key);
                        if( $word == 'Order' || (!$isManual && $word == 'ManualConsumed') || !intval($show) )continue;
                        if( in_array($word, array('Initialstartdate', 'Initialworkload', 'Initialenddate')) && $projectName['Project']['off_freeze'] == 0 )continue;
                        switch($word){
                            case 'Task':
                                $taskName = !empty($taskName) ? $taskName : '';
                                echo '<td style="width: 150px;white-space: nowrap;">' . $taskName . '</td>';
                            break;
                            case 'Order':
                                continue;
                            break;
                            case 'AssignedTo':
                                $assign = !empty($assign) ? $assign : '';
                                echo '<td style="width: 150px;white-space: nowrap;">' . $assign . '</td>';
                            break;
                            case 'Priority':
                                $prio = !empty($prio) ? $prio : '';
                                echo '<td style="width: 50px;white-space: nowrap;">' . $prio . '</td>';
                            break;
                            case 'Status':
                                $status = !empty($status) ? $status : '';
                                echo '<td style="width: 50px;white-space: nowrap;">' . $status . '</td>';
                            break;
                            case 'Profile':
                                $profile = !empty($profile) ? $profile : '';
                                echo '<td style="width: 50px;white-space: nowrap;">' . $profile . '</td>';
                            break;
                            case 'Startdate':
                                $start = !empty($start) && ($start != 0) && ($start != '0000-00-00') ? $start : '';
                                echo '<td style="width: 50px;white-space: nowrap;">' . $start . '</td>';
                            break;
                            case 'Enddate':
                                $end = !empty($end) && ($end != 0) && ($end != '0000-00-00') ? $end : '';
                                echo '<td style="width: 50px;white-space: nowrap;">' . $end . '</td>';
                            break;
                            case '+/-':
                                $slider = !empty($slider) ? $slider : '';
                                echo '<td style="text-align: right; width: 20px;white-space: nowrap;">' . $slider . '</td>';
                                break;
                            case 'Duration':
                                $duration = !empty($duration) ? $duration : '';
                                echo '<td style="text-align: right; width: 20px;white-space: nowrap;">' . $duration . '</td>';
                            break;
                            case 'Predecessor':
                                $predecessor = ((!empty($predecessor) && $predecessor != NULL) ? number_format($predecessor,2,',',' ') : 0);
                                echo '<td style="text-align: right; width: 20px;white-space: nowrap;">' . $predecessor . '</td>';
                            break;
                            case 'Workload':
                                $workload = ((!empty($workload) && $workload != NULL) ? number_format($workload,2,',',' ') : 0);
                                echo '<td style="text-align: right; width: 30px;white-space: nowrap;">' . $workload . '</td>';
                            break;
                            case 'Overload':
                                $overload = ((!empty($overload) && $overload != NULL) ? number_format($overload,2,',',' ') : 0);
                                echo '<td style="text-align: right; width: 30px;white-space: nowrap;">' . $overload . '</td>';
                            break;
                            case 'Consumed':
                                $cons = ((!empty($cons) && $cons != NULL) ? number_format($cons,2,',',' ') : 0);
                                echo '<td style="text-align: right; width: 30px;white-space: nowrap;">' . $cons . '</td>';
                            break;
                            case 'ManualConsumed':
                                $data = isset($companyConfigs['manual_consumed']) && $companyConfigs['manual_consumed'] ? number_format($manual,2,',',' ') : '';
                                echo '<td style="text-align: right; width: 50px;white-space: nowrap;">' . $data . '</td>';
                            break;
                            case 'InUsed':
                                $wait = ((!empty($wait) && $wait != NULL) ? number_format($wait,2,',',' ') : 0);
                                echo '<td style="text-align: right; width: 30px;white-space: nowrap;">' . $wait . '</td>';
                            break;
                            case 'Completed':
                                $comp = !empty($comp) ? $comp : '';
                                echo '<td style="text-align: right; width: 30px;white-space: nowrap;">' . $comp . '</td>';
                            break;
                            case 'Remain':
                                $remain = ((!empty($remain) && $remain != NULL) ? number_format($remain,2,',',' ') : 0);
                                echo '<td style="text-align: right; width: 30px;white-space: nowrap;">' . $remain . '</td>';
                            break;
                            case 'Initialworkload':
                                $data = $projectName['Project']['off_freeze'] != 0 ? (!empty($workloadInitial) ? number_format($workloadInitial,2,',',' ') : '') : '';
                                echo '<td style="text-align: right; width: 30px;white-space: nowrap;">' . $data . '</td>';
                            break;
                            case '%progressorder':
                                $data = !empty($progress_order) ? number_format($progress_order,2,',',' ') . ' %' : '';
                                echo '<td style="text-align: right; width: 30px;white-space: nowrap;">' . $data . '</td>';
                            break;
                            case 'Text':
                                $text = !empty($text) ? $text : '';
                                echo '<td style="width: 30px;white-space: nowrap;">' . $text . '</td>';
                            break;
                            case 'Attachment':
                                    echo '<td style="width: 30px;white-space: nowrap;"></td>';
                            break;
                            case 'UnitPrice':
                                    $data = !empty($unit_price) ? number_format($unit_price,2,',',' ') . ' €' : '';
                                    echo '<td style="text-align: right; width: 30px;white-space: nowrap;">' . $data . '</td>';
                            break;
                            case 'Consumed€':
                                    $data = !empty($consumed_euro) ? number_format($consumed_euro,2,',',' ') . ' €' : '';
                                    echo '<td style="text-align: right; width: 30px;white-space: nowrap;">' . $data . '</td>';
                            break;
                            case 'Remain€':
                                    $data = !empty($remain_euro) ? number_format($remain_euro,2,',',' ') . ' €' : '';
                                    echo '<td style="text-align: right; width: 30px;white-space: nowrap;">' . $data . '</td>';
                            break;
                            case 'Workload€':
                                    $data = !empty($workload_euro) ? number_format($workload_euro,2,',',' ') . ' €' : '';
                                    echo '<td style="text-align: right; width: 30px;white-space: nowrap;">' . $data . '</td>';
                            break;
                            case 'Initialstartdate':
                                $data = !empty($startInitial) && $projectName['Project']['off_freeze'] != 0 ? $startInitial : '';
                                echo '<td style="text-align: right; width: 30px;white-space: nowrap;">' . $data . '</td>';
                            break;
                            case 'Initialenddate':
                                $data = !empty($endInitial) && $projectName['Project']['off_freeze'] != 0 ? $endInitial : '';
                                echo '<td style="text-align: right; width: 30px;white-space: nowrap;">' . $data . '</td>';
                            break;
                            default:
                                $word = substr($word, 0, 4);
                                if($word == 'Amou'){
                                    $data = !empty($amount) ? number_format($amount,2,',',' ') . ' €' : '';
                                } else if ($word == '%pro'){
                                    $data = !empty($progress_order_amount) ? number_format($progress_order_amount,2,',',' ') . ' €' : '';
                                }
                                echo '<td style="text-align: right; width: 30px;white-space: nowrap;">'. (!empty($data) ? $data : '') .'</td>';
                            break;
                        }
                    }
                }
                //build data
                $pid = '';
                $taskName = '';
                $prio = '';
                $status = '';
                $assign = '';
                $start = $str_utility->convertToVNDate($projectTaskForTasks['task_start_date']);
                $end = $str_utility->convertToVNDate($projectTaskForTasks['task_end_date']);
                $duration = $projectTaskForTasks['duration'];
                $predecessor = isset($projectTaskForTasks['predecessor']) ? $projectTaskForTasks['predecessor'] : '';
                $workload = $projectTaskForTasks['estimated'] ? $projectTaskForTasks['estimated'] : 0;
                $cons = $projectTaskForTasks['consumed'];
                $wait = !empty($projectTaskForTasks['wait']) ? $projectTaskForTasks['wait'] : 0;
                $comp = $projectTaskForTasks['completed'];
                $remain = $projectTaskForTasks['remain'];
                $over = isset($projectTaskForTasks['overload']) ? $projectTaskForTasks['overload'] : 0;
                $overload = $isManual ? $projectTaskForTasks['manual_overload'] : $over;
                $profile = isset($projectTaskForTasks['profile_text']) ? $projectTaskForTasks['profile_text'] : '';
                $manual = isset($projectTaskForTasks['manual_consumed']) ? $projectTaskForTasks['manual_consumed'] : 0;
                $amount = isset($projectTaskForTasks['amount']) ? $projectTaskForTasks['amount'] : 0;
                $progress_order = isset($projectTaskForTasks['progress_order']) ? $projectTaskForTasks['progress_order'] : 0;
                $progress_order_amount = isset($projectTaskForTasks['progress_order_amount']) ? $projectTaskForTasks['progress_order_amount'] : 0;
                $id_activity = isset($projectTaskForTasks['id_activity']) ? $projectTaskForTasks['id_activity'] : '';
                $wait = isset($projectTaskForTasks['wait']) ? $projectTaskForTasks['wait'] : 0;
                $slider = isset($projectTaskForTasks['slider']) ? $projectTaskForTasks['slider'] : 0;
                $text = isset($projectTaskForTasks['text_1']) ? $projectTaskForTasks['text_1'] : '';
                $unit_price = isset($projectTaskForTasks['unit_price']) ? $projectTaskForTasks['unit_price'] : 0;
                $consumed_euro = isset($projectTaskForTasks['consumed_euro']) ? $projectTaskForTasks['consumed_euro'] : 0;
                $remain_euro = isset($projectTaskForTasks['remain_euro']) ? $projectTaskForTasks['remain_euro'] : 0;
                $workload_euro = isset($projectTaskForTasks['workload_euro']) ? $projectTaskForTasks['workload_euro'] : 0;

                echo '<tr style="background-color: #E8F0FA">';
                if($settingP['ProjectSetting']['show_freeze'] == 1){
                    if($checkP['Project']['is_freeze']==1){
                        $workloadInitial = isset($projectTaskForTasks['initial_estimated']) ? $projectTaskForTasks['initial_estimated'] : 0;
                        $startInitial = isset($projectTaskForTasks['initial_task_start_date']) ? $str_utility->convertToVNDate($projectTaskForTasks['initial_task_start_date']) : '';
                        $endInitial = isset($projectTaskForTasks['initial_task_end_date']) ? $str_utility->convertToVNDate($projectTaskForTasks['initial_task_end_date']) : '';
                    }else{
                        $workloadInitial = 0;
                        $startInitial = '';
                        $endInitial = '';
                    }
                    toData($isManual, $projectName, $orders, $pid, $taskName, $prio, $status, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain, $amount, $progress_order, $progress_order_amount, $id_activity, $wait, $slider, $text, $unit_price, $consumed_euro, $remain_euro, $workload_euro, $workloadInitial,$startInitial,$endInitial);
                }else{
                    toData($isManual, $projectName, $orders, $pid, $taskName, $prio, $status, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain, $amount, $progress_order, $progress_order_amount, $id_activity, $wait, $slider, $text, $unit_price, $consumed_euro, $remain_euro, $workload_euro);
                }
                echo '</tr>';
                foreach($projectTaskForTasks['children'] as $parts) {
                    $pid = '';
                    $taskName = ' ---- ' . strip_tags($parts['task_title']);
                    $prio = !empty($parts['task_priority_text']) ? $parts['task_priority_text'] : '';
                    $status = '';
                    $assign = '';
                    $start = $str_utility->convertToVNDate($parts['task_start_date']);
                    $end = $str_utility->convertToVNDate($parts['task_end_date']);
                    $duration = $parts['duration'];
                    $predecessor = isset($parts['predecessor']) ? $parts['predecessor'] : '';
                    $workload = $parts['estimated'] ? $parts['estimated'] : 0;
                    $cons = $parts['consumed'];
                    $wait = !empty($parts['wait']) ? $parts['wait'] : 0;
                    $comp = $parts['completed'];
                    $remain = $parts['remain'];
                    $profile = isset($parts['profile_text']) ? $parts['profile_text'] : '';
                    $manual = isset($parts['manual_consumed']) ? $parts['manual_consumed'] : 0;
                    $over = isset($parts['overload']) ? $parts['overload'] : 0;
                    $overload = $isManual ? $parts['manual_overload'] : $over;
                    $amount = isset($parts['amount']) ? $parts['amount'] : 0;
                    $progress_order = isset($parts['progress_order']) ? $parts['progress_order'] : 0;
                    $progress_order_amount = isset($parts['progress_order_amount']) ? $parts['progress_order_amount'] : 0;
                    $id_activity = isset($parts['id_activity']) ? $parts['id_activity'] : '';
                    $wait = isset($parts['wait']) ? $parts['wait'] : 0;
                    $slider = isset($parts['slider']) ? $parts['slider'] : 0;
                    $text = isset($parts['text_1']) ? $parts['text_1'] : '';
                    $unit_price = isset($parts['unit_price']) ? $parts['unit_price'] : 0;
                    $consumed_euro = isset($parts['consumed_euro']) ? $parts['consumed_euro'] : 0;
                    $remain_euro = isset($parts['remain_euro']) ? $parts['remain_euro'] : 0;
                    $workload_euro = isset($parts['workload_euro']) ? $parts['workload_euro'] : 0;
                    echo '<tr>';
                    if($settingP['ProjectSetting']['show_freeze'] == 1) {
                        if($checkP['Project']['is_freeze'] == 1) {
                            $workloadInitial = isset($parts['initial_estimated']) ? $parts['initial_estimated'] : 0;
                            $startInitial = isset($parts['initial_task_start_date']) ? $str_utility->convertToVNDate($parts['initial_task_start_date']) : '';
                            $endInitial = isset($parts['initial_task_end_date']) ? $str_utility->convertToVNDate($parts['initial_task_end_date']) : '';
                        } else {
                            $workloadInitial = 0;
                            $startInitial = '';
                            $endInitial = '';
                        }
                        toData($isManual, $projectName, $orders, $pid, $taskName, $prio, $status, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain, $amount, $progress_order, $progress_order_amount, $id_activity, $wait, $slider, $text, $unit_price, $consumed_euro, $remain_euro, $workload_euro, $workloadInitial,$startInitial,$endInitial);
                    } else {
                        toData($isManual, $projectName, $orders, $pid, $taskName, $prio, $status, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain, $amount, $progress_order, $progress_order_amount, $id_activity, $wait, $slider, $text, $unit_price, $consumed_euro, $remain_euro, $workload_euro);
                    }
                    echo '</tr>';
                    if(!empty($parts['children'])){
                        foreach($parts['children'] as $_phases){
                            $pid = !empty($_phases['id']) && $_phases['id'] < 999999999 ? $_phases['id'] : '';
                            $taskName = ' ------ ' . strip_tags($_phases['task_title']);
                            $prio = !empty($_phases['task_priority_text']) ? $_phases['task_priority_text'] : '';
                            $status = !empty($_phases['task_status_text']) ? $_phases['task_status_text'] : '';
                            if(!empty($_phases['task_status_text']) && !empty($_phases['task_status_id']) && !empty($_phases['children'])){
                                $listStatusChildren = Set::combine($_phases['children'], '{n}.task_status_id', '{n}.task_status_id');
                                if(count($listStatusChildren) == 1){ // co 1 trang thai
                                    $statusId = array_shift($listStatusChildren);
                                    $status = !empty($projectStatus) && !empty($projectStatus[$statusId]) ? $projectStatus[$statusId] : '';
                                } else {
                                    $status = !empty($statusOfCompanies['ProjectStatus']['name']) ? $statusOfCompanies['ProjectStatus']['name'] : '';
                                }
                            }
                            $ass = !empty($_phases['task_assign_to_text']) ? implode(', ', $_phases['task_assign_to_text']) : '';
                            $assign = $ass;
                            $start = $str_utility->convertToVNDate($_phases['task_start_date']);
                            $end = $str_utility->convertToVNDate($_phases['task_end_date']);
                            $duration = $_phases['duration'];
                            $predecessor = isset($_phases['predecessor']) ? $_phases['predecessor'] : '';
                            $workload = $_phases['estimated'] ? $_phases['estimated'] : 0;
                            $cons = $_phases['consumed'];
                            $wait = !empty($_phases['wait']) ? $_phases['wait'] : 0;
                            $comp = $_phases['completed'];
                            $remain = $_phases['remain'];
                            $profile = isset($_phases['profile_text']) ? $_phases['profile_text'] : '';
                            $manual = isset($_phases['manual_consumed']) ? $_phases['manual_consumed'] : 0;
                            $over = isset($_phases['overload']) ? $_phases['overload'] : 0;
                            $overload = $isManual ? $_phases['manual_overload'] : $over;
                            $amount = isset($_phases['amount']) ? $_phases['amount'] : 0;
                            $progress_order = isset($_phases['progress_order']) ? $_phases['progress_order'] : 0;
                            $progress_order_amount = isset($_phases['progress_order_amount']) ? $_phases['progress_order_amount'] : 0;
                            $id_activity = isset($_phases['id_activity']) ? $_phases['id_activity'] : '';
                            $wait = isset($_phases['wait']) ? $_phases['wait'] : 0;
                            $slider = isset($_phases['slider']) ? $_phases['slider'] : 0;
                            $text = isset($_phases['text_1']) ? $_phases['text_1'] : '';
                            $unit_price = isset($_phases['unit_price']) ? $_phases['unit_price'] : 0;
                            $consumed_euro = isset($_phases['consumed_euro']) ? $_phases['consumed_euro'] : 0;
                            $remain_euro = isset($_phases['remain_euro']) ? $_phases['remain_euro'] : 0;
                            $workload_euro = isset($_phases['workload_euro']) ? $_phases['workload_euro'] : 0;
                            if( empty($statusFilters['project_task']) || (!empty($statusFilters['project_task']) && (empty($_phases['task_status_id']) || (in_array($_phases['task_status_id'], $statusFilters['project_task'])))) ){
                                echo '<tr>';
                                if($settingP['ProjectSetting']['show_freeze'] == 1){
                                    if($checkP['Project']['is_freeze'] == 1){
                                        $workloadInitial = isset($_phases['initial_estimated']) ? $_phases['initial_estimated'] : 0;
                                        $startInitial = isset($_phases['initial_task_start_date']) ? $str_utility->convertToVNDate($_phases['initial_task_start_date']) : '';
                                        $endInitial = isset($_phases['initial_task_end_date']) ? $str_utility->convertToVNDate($_phases['initial_task_end_date']) : '';
                                    } else {
                                        $workloadInitial = 0;
                                        $startInitial = '';
                                        $endInitial = '';
                                    }
                                    toData($isManual, $projectName, $orders, $pid, $taskName, $prio, $status, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain, $amount, $progress_order, $progress_order_amount, $id_activity, $wait, $slider, $text, $unit_price, $consumed_euro, $remain_euro, $workload_euro, $workloadInitial,$startInitial,$endInitial);
                                } else {
                                    toData($isManual, $projectName, $orders, $pid, $taskName, $prio, $status, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain, $amount, $progress_order, $progress_order_amount, $id_activity, $wait, $slider, $text, $unit_price, $consumed_euro, $remain_euro, $workload_euro);
                                }
                                echo '</tr>';
                            }
                            if(!empty($_phases['children'])){
                                foreach($_phases['children'] as $tasks){
                                    $pid = $tasks['id'];
                                    $taskName = ' -------- ' . strip_tags($tasks['task_title']);
                                    $prio = !empty($tasks['task_priority_text']) ? $tasks['task_priority_text'] : '';
                                    $status = !empty($tasks['task_status_text']) ? $tasks['task_status_text'] : '';
                                    if(!empty($tasks['task_status_text']) && !empty($tasks['task_status_id']) && !empty($tasks['children'])){
                                        $listStatusChildren = Set::combine($tasks['children'], '{n}.task_status_id', '{n}.task_status_id');
                                        if(count($listStatusChildren) == 1){ // co 1 trang thai
                                            $statusId = array_shift($listStatusChildren);
                                            $status = !empty($projectStatus) && !empty($projectStatus[$statusId]) ? $projectStatus[$statusId] : '';
                                        } else {
                                            $status = !empty($statusOfCompanies['ProjectStatus']['name']) ? $statusOfCompanies['ProjectStatus']['name'] : '';
                                        }
                                    }
                                    $ass = !empty($tasks['task_assign_to_text']) ? implode(', ', $tasks['task_assign_to_text']) : '';
                                    $assign = $ass;
                                    $start = $str_utility->convertToVNDate($tasks['task_start_date']);
                                    $end = $str_utility->convertToVNDate($tasks['task_end_date']);
                                    $duration = $tasks['duration'];
                                    $predecessor = isset($tasks['predecessor']) ? $tasks['predecessor'] : '';
                                    $workload = $tasks['estimated'] ? $tasks['estimated'] : 0;
                                    $cons = $tasks['consumed'];
                                    $wait = !empty($tasks['wait']) ? $tasks['wait'] : 0;
                                    $comp = $tasks['completed'];
                                    $remain = $tasks['remain'];
                                    $profile = isset($tasks['profile_text']) ? $tasks['profile_text'] : '';
                                    $manual = isset($tasks['manual_consumed']) ? $tasks['manual_consumed'] : 0;
                                    $over = isset($tasks['overload']) ? $tasks['overload'] : 0;
                                    $overload = $isManual ? $tasks['manual_overload'] : $over;
                                    $amount = isset($tasks['amount']) ? $tasks['amount'] : 0;
                                    $progress_order = isset($tasks['progress_order']) ? $tasks['progress_order'] : 0;
                                    $progress_order_amount = isset($tasks['progress_order_amount']) ? $tasks['progress_order_amount'] : 0;
                                    $id_activity = isset($tasks['id_activity']) ? $tasks['id_activity'] : '';
                                    $wait = isset($tasks['wait']) ? $tasks['wait'] : 0;
                                    $slider = isset($tasks['slider']) ? $tasks['slider'] : 0;
                                    $text = isset($tasks['text_1']) ? $tasks['text_1'] : '';
                                    $unit_price = isset($tasks['unit_price']) ? $tasks['unit_price'] : 0;
                                    $consumed_euro = isset($tasks['consumed_euro']) ? $tasks['consumed_euro'] : 0;
                                    $remain_euro = isset($tasks['remain_euro']) ? $tasks['remain_euro'] : 0;
                                    $workload_euro = isset($tasks['workload_euro']) ? $tasks['workload_euro'] : 0;
                                    if( empty($statusFilters['project_task']) || (!empty($statusFilters['project_task']) && (empty($tasks['task_status_id']) || (in_array($tasks['task_status_id'], $statusFilters['project_task'])))) ){
                                        echo '<tr>';
                                        if($settingP['ProjectSetting']['show_freeze'] == 1){
                                            if($checkP['Project']['is_freeze'] == 1){
                                                $workloadInitial = isset($tasks['initial_estimated']) ? $tasks['initial_estimated'] : 0;
                                                $startInitial = isset($tasks['initial_task_start_date']) ? $str_utility->convertToVNDate($tasks['initial_task_start_date']) : '';
                                                $endInitial = isset($tasks['initial_task_end_date']) ? $str_utility->convertToVNDate($tasks['initial_task_end_date']) : '';
                                            } else {
                                                $workloadInitial = 0;
                                                $startInitial = '';
                                                $endInitial = '';
                                            }
                                            toData($isManual, $projectName, $orders, $pid, $taskName, $prio, $status, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain, $amount, $progress_order, $progress_order_amount, $id_activity, $wait, $slider, $text, $unit_price, $consumed_euro, $remain_euro, $workload_euro, $workloadInitial,$startInitial,$endInitial);
                                        } else {
                                            toData($isManual, $projectName, $orders, $pid, $taskName, $prio, $status, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain, $amount, $progress_order, $progress_order_amount, $id_activity, $wait, $slider, $text, $unit_price, $consumed_euro, $remain_euro, $workload_euro);
                                        }
                                        echo '</tr>';
                                    }
                                    if(!empty($tasks['children'])){
                                        foreach($tasks['children'] as $_tasks){
                                            $pid = $_tasks['id'];
                                            $taskName = ' ---------- ' . strip_tags($_tasks['task_title']);
                                            $prio = !empty($_tasks['task_priority_text']) ? $_tasks['task_priority_text'] : '';
                                            $status = !empty($_tasks['task_status_text']) ? $_tasks['task_status_text'] : '';
                                            $ass = !empty($_tasks['task_assign_to_text']) ? implode(', ', $_tasks['task_assign_to_text']) : '';
                                            $assign = $ass;
                                            $start = $str_utility->convertToVNDate($_tasks['task_start_date']);
                                            $end = $str_utility->convertToVNDate($_tasks['task_end_date']);
                                            $duration = $_tasks['duration'];
                                            $predecessor = isset($_tasks['predecessor']) ? $_tasks['predecessor'] : '';
                                            $workload = $_tasks['estimated'] ? $_tasks['estimated'] : 0;
                                            $cons = $_tasks['consumed'];
                                            $wait = !empty($_tasks['wait']) ? $_tasks['wait'] : 0;
                                            $comp = $_tasks['completed'];
                                            $remain = $_tasks['remain'];
                                            $profile = isset($_tasks['profile_text']) ? $_tasks['profile_text'] : '';
                                            $manual = isset($_tasks['manual_consumed']) ? $_tasks['manual_consumed'] : 0;
                                            $over = isset($_tasks['overload']) ? $_tasks['overload'] : 0;
                                            $overload = $isManual ? $_tasks['manual_overload'] : $over;
                                            $amount = isset($_tasks['amount']) ? $_tasks['amount'] : 0;
                                            $progress_order = isset($_tasks['progress_order']) ? $_tasks['progress_order'] : 0;
                                            $progress_order_amount = isset($_tasks['progress_order_amount']) ? $_tasks['progress_order_amount'] : 0;
                                            $id_activity = isset($_tasks['id_activity']) ? $_tasks['id_activity'] : '';
                                            $wait = isset($_tasks['wait']) ? $_tasks['wait'] : 0;
                                            $slider = isset($_tasks['slider']) ? $_tasks['slider'] : 0;
                                            $text = isset($_tasks['text_1']) ? $_tasks['text_1'] : '';
                                            $unit_price = isset($_tasks['unit_price']) ? $_tasks['unit_price'] : 0;
                                            $consumed_euro = isset($_tasks['consumed_euro']) ? $_tasks['consumed_euro'] : 0;
                                            $remain_euro = isset($_tasks['remain_euro']) ? $_tasks['remain_euro'] : 0;
                                            $workload_euro = isset($_tasks['workload_euro']) ? $_tasks['workload_euro'] : 0;
                                            if( empty($statusFilters['project_task']) || (!empty($statusFilters['project_task']) && (empty($_tasks['task_status_id']) || (in_array($_tasks['task_status_id'], $statusFilters['project_task'])))) ){
                                                echo '<tr>';
                                                if($settingP['ProjectSetting']['show_freeze'] == 1){
                                                    if($checkP['Project']['is_freeze'] == 1){
                                                        $workloadInitial = isset($_tasks['initial_estimated']) ? $_tasks['initial_estimated'] : 0;
                                                        $startInitial = isset($_tasks['initial_task_start_date']) ? $str_utility->convertToVNDate($_tasks['initial_task_start_date']) : '';
                                                        $endInitial = isset($_tasks['initial_task_end_date']) ? $str_utility->convertToVNDate($_tasks['initial_task_end_date']) : '';
                                                    } else {
                                                        $workloadInitial = 0;
                                                        $startInitial = '';
                                                        $endInitial = '';
                                                    }
                                                    toData($isManual, $projectName, $orders, $pid, $taskName, $prio, $status, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain, $amount, $progress_order, $progress_order_amount, $id_activity, $wait, $slider, $text, $unit_price, $consumed_euro, $remain_euro, $workload_euro, $workloadInitial,$startInitial,$endInitial);
                                                } else {
                                                    toData($isManual, $projectName, $orders, $pid, $taskName, $prio, $status, $profile, $assign, $start, $end, $duration, $predecessor, $workload,$overload, $cons, $manual, $wait, $comp, $remain, $amount, $progress_order, $progress_order_amount, $id_activity, $wait, $slider, $text, $unit_price, $consumed_euro, $remain_euro, $workload_euro);
                                                }
                                                echo '</tr>';
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
</div>
<!-- project phase plans -->
<div id="div_phase" style="display: none">
<?php if($showMenu['phase'] && $yourFormFilter['phase'] == 1) : ?>
<div data-widget="phase" class="wd-input wd-area wd-none" style = "margin-top: 15px; <?php echo !$showMenu['phase'] ? 'display: none' : '' ?>">
    <?php
    $settings = $this->requestAction('/project_phases/getFields');
    ?>
    <div>
        <table id = "absence" style="width: 95%">
            <thead>
                <tr>
                    <?php if($displayParst){ ?>
                    <th><?php echo __('Part', true) ?></th>
                    <?php } ?>
                    <th><?php echo __('Phase', true) ?></th>
                    <th><?php echo __('Plan start date', true) ?></th>
                    <th><?php echo __('Plan end date', true) ?></th>
                    <?php
                    foreach ($settings as $setting) {
                        list($key, $show) = explode('|', $setting);
                        if( $show == 0 )continue;
                        if( $key == 'progress' && !$manuallyAchievement )continue;
                        if( $key == 'profile_id' && !$activateProfile )continue;
                        switch ($key) {
                            case 'kpi':
                                echo '<th>' . __('KPI', true) . '</th>';
                                break;
                            case 'progress':
                                echo '<th>' . __('% Achieved', true) . '</th>';
                                break;
                            case 'planed_duration':
                                echo '<th>' . __('Duration', true) . '</th>';
                                break;
                            case 'predecessor':
                                echo '<th>' . __('Predecessor', true) . '</th>';
                                break;
                            case 'profile_id':
                                echo '<th>' . __('Profile', true) . '</th>';
                                break;
                            case 'phase_real_start_date':
                                echo '<th>' . __('Real start date', true) . '</th>';
                                break;
                            case 'phase_real_end_date':
                                echo '<th>' . __('Real end date', true) . '</th>';
                                break;
                            case 'project_phase_status_id':
                                echo '<th>' . __('Status', true) . '</th>';
                                break;
                            case 'color':
                                echo '<th>' . __('Color', true) . '</th>';
                                break;
                            case 'ref1':
                                echo '<th>' . __('Ref 1', true) . '</th>';
                                break;
                            case 'ref2':
                                echo '<th>' . __('Ref 2', true) . '</th>';
                                break;
                            case 'ref3':
                                echo '<th>' . __('Ref 3', true) . '</th>';
                                break;
                            case 'ref4':
                                echo '<th>' . __('Ref 4', true) . '</th>';
                                break;
                        }
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($_projectPhasePlans as $_projectPhasePlan) {
                    $dx = $_projectPhasePlan['ProjectPhasePlan'];
                    if (isset($projectPhases1[$dx['project_planed_phase_id']])) {
                        $predecessors[$dx['id']] = $projectPhases1[$dx['project_planed_phase_id']] . (isset($projectParts[$dx['project_part_id']]) ? ' (' . $_projectParts[$dx['project_part_id']] . ')' : '');
                    }
                }
                foreach ($_projectPhasePlans as $_projectPhasePlan) {
                    $dx = $_projectPhasePlan['ProjectPhasePlan'];
                ?>
                <tr>
                    <?php if($displayParst){ ?>
                    <td><?php echo !empty($dx['project_part_id']) && !empty($_projectParts[$dx['project_part_id']]) ? $_projectParts[$dx['project_part_id']] : '' ?></td>
                    <?php } ?>
                    <td><?php echo !empty($dx['project_planed_phase_id']) && !empty($projectPhases1[$dx['project_planed_phase_id']]) ? $projectPhases1[$dx['project_planed_phase_id']] : '' ?></td>
                    <td style="text-align: right"><?php echo $str_utility->convertToVNDate($dx['phase_planed_start_date']) ?></td>
                    <td style="text-align: right"><?php echo $str_utility->convertToVNDate($dx['phase_planed_end_date']) ?></td>
                    <?php
                    foreach ($settings as $setting) {
                        list($key, $show) = explode('|', $setting);
                        if( $show == 0 )continue;
                        if( $key == 'progress' && !$manuallyAchievement )continue;
                        if( $key == 'profile_id' && !$activateProfile )continue;
                        switch ($key) {
                            case 'kpi':
                                $c = $dx['phase_planed_end_date'] < $dx['phase_real_end_date'] ? '#F00' : '#0F0';
                                echo '<td><div style="width: 20px; height: 20px; background-color: '.$c.'; padding-left: 0px; margin:0 auto;margin-top : 4px;"></div></td>';
                                break;
                            case 'progress':
                                echo '<td><div style="position: relative; text-align: center"><div style="position: absolute; width: '.$dx['progress'].'%; height: 145%; margin-top: -10px; background-color: rgb(77, 255, 130);"></div><span style="position: relative">'.$dx['progress'].' %</span></div></td>';
                                break;
                            case 'planed_duration':
                                echo '<td>' . $dx['planed_duration'] . '</td>';
                                break;
                            case 'predecessor':
                                echo '<td>' . (!empty($dx['predecessor']) && !empty($predecessors[$dx['predecessor']]) ? $predecessors[$dx['predecessor']] : '') . '</td>';
                                break;
                            case 'profile_id':
                                echo '<td>' . (!empty($dx['profile_id']) && !empty($profiles[$dx['profile_id']]) ? $profiles[$dx['profile_id']] : '') . '</td>';
                                break;
                            case 'phase_real_start_date':
                                echo '<td style="text-align: right">' . $str_utility->convertToVNDate($dx['phase_real_start_date']) . '</td>';
                                break;
                            case 'phase_real_end_date':
                                echo '<td style="text-align: right">' . $str_utility->convertToVNDate($dx['phase_real_end_date']) . '</td>';
                                break;
                            case 'project_phase_status_id':
                                echo '<td>' . (!empty($dx['project_phase_status_id']) && !empty($projectPhaseStatuses[$dx['project_phase_status_id']]) ? $projectPhaseStatuses[$dx['project_phase_status_id']] : '') . '</td>';
                                break;
                            case 'color':
                                echo '<td><div style="width: 20px; height: 20px; background-color: ' . $_projectPhasePlan['ProjectPhase']['color'] . '; padding-left: 0px; margin:0 auto;margin-top : 4px;"></div></td>';
                                break;
                            case 'ref1':
                                echo '<td>' . $dx['ref1'] . '</td>';
                                break;
                            case 'ref2':
                                echo '<td>' . $dx['ref2'] . '</td>';
                                break;
                            case 'ref3':
                                echo '<td>' . $dx['ref3'] . '</td>';
                                break;
                            case 'ref4':
                                echo '<td>' . $dx['ref4'] . '</td>';
                                break;
                        }
                    }
                    ?>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
</div>
<!-- end -->
                            </div>
                        </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end setting dialog -->
<?php
echo $this->element('dialog_detail_value');
echo $this->element('dialog_projects');
echo $this->Form->create('Export', array('url' => array('controller' => 'projects', 'action' => 'export_pdf'), 'type' => 'file'));
echo $this->Form->hidden('canvas', array('id' => 'canvasData'));
echo $this->Form->hidden('height', array('id' => 'canvasHeight'));
echo $this->Form->hidden('width', array('id' => 'canvasWidth'));
echo $this->Form->hidden('export_screen', array('value' => 'ajax'));
echo $this->Form->hidden('project_name', array('value' => $projectName['Project']['project_name']));
echo $this->Form->hidden('project_id', array('value' => $projectName['Project']['id']));
echo $this->Form->end();
?>

<script type="text/javascript">

    var yourFormFilter = <?php echo json_encode($yourFormFilter) ?>;
    var showMenu = <?php echo json_encode($showMenu) ?>;
    var project_id = <?php echo json_encode($project_name['Project']['id']) ?>;
    var $showKpiBudget = <?php echo json_encode($showKpiBudget) ?>;
    var $breakpage = <?php echo json_encode($breakpage) ?>;
    var wdTable = $('.normal-scroll');
    var heightTable = $(window).height() - 80;
    var widthTable = $(window).width() - 80;
    wdTable.css({
        overflow: 'auto',
        height: heightTable,
        width: widthTable,
    });

    $(window).resize(function(){
        var heightTable = $(window).height() - 80;
        var widthTable = $(window).width() - 80;
        wdTable.css({
            overflow: 'auto',
            height: heightTable,
            width: widthTable,
        });
    });
    function SubmitDataExport(){
        $(".wmd-view-topscroll").scrollLeft(0);
        $('.wmd-view-topscroll').removeAttr('style');
        expandScreen();
        $('.wmd-view-topscroll').css('width', '100%');
        $('#wd-fragment-2').css('display', 'none');
        $('#img_location').css('display', '');
        $('#svg_inve_chard').css('display', 'none');
        $('#svg_circle_chard').css('display', 'none');
        $('#wd-tab-content').css('height', 'auto');
        // collapseScreen();
        $('#wd-tab-content').removeClass('normal-scroll');
        $('#svg_kpi_1').css('display', 'none');
        $('.svg_budget').css('display', 'none');
        $('.progress-label').css('display', 'none');
        $('.scroll-progress').css('display', 'none');
        $('.img_budget_export').css('display', 'block');
        $('.weather-pdf').css('display', 'inline-block');
        // $('#png-container img').css('display', '');
        // $('#png-container1 img').css('display', '');
        var _total = 0;
        var i = 1;
        $($('#wd-fragment-temp').children()).each(function(val, index){
            if($(index).is(':visible')){
                $.each($breakpage, function(val, key){
                    if(key == $(index).data('widget')){
                        var calcu = _total%820;
                        if(calcu > 0){
                            var j = i > 9 ? (i > 5 ? 20 : 0) : 30;
                            var _hei = 820 - calcu + i*j;
                            if(_hei >= 820) _hei = _hei%820;
                            $(index).prepend('<div class="element-empty" style="height: '+_hei+'px">&nbsp</div>');
                            i++;
                        }
                    }
                });
                var t = $(index).outerHeight() + 10;
                _total += t;
            }
        });
        $('#mock').height(_total);
        $('#chart-wrapper').html2canvas();
    }
    // call big image.
    if(yourFormFilter['gantt'] == 1){
        var url = <?php echo json_encode($html->url('/project_phase_plans/phase_vision/'. $id .'?type=year')) ?>;
        $.get(url, null, function(text){
            var element = $(text).find('#AjaxGanttChartDIV').html();
            $('#AjaxGanttChartDIV').html(element);
            var el = $('#AjaxGanttChartDIV').find('.gantt-child-child');
            $(el).each(function(){
                var str = $(this).attr('class');
                if(str.search('wd-task-') != -1){
                    jQuery($(this)).css({'display':'none'});
                }
            });
            var vl = $('#AjaxGanttChartDIV').find('.gantt-child');
            $(vl).each(function(){
                var str = $(this).attr('class');
                if(str.search('wd-task-') != -1){
                    jQuery($(this)).css({'display':'none'});
                }
            });
            var element = $('#AjaxGanttChartDIV').find('.gantt-line').last();
            var e = $(element).find('div');
            var _top = 0;
            var oldleft = curleft = 0;
            if($(element).parent().closest('tr').hasClass('gantt-ms') === true){
                $(e).each(function(index, value){
                    var position = $(value).position();
                    var _left = position.left;
                    curleft = _left;
                    if( (curleft >= (oldleft - 150)) && (curleft <= (oldleft + 150)) ){
                        _top = _top + 16;
                        $(value).css('top', _top + 'px');
                        $(element).css('height',(16 + _top) + 'px');
                    }
                    oldleft = _left;
                });
            }
        });
    }
    if(showMenu['risk'] && yourFormFilter['risk'] == 1){
        var url = <?php echo json_encode($html->url('/project_risks/index/'. $id)) ?>;
        $.get(url, null, function(text){
            var element1 = $(text).find('.wd-list-project').html();
            $('#project_risks').html(element1);
            $('#project_risks').find('.wd-title').hide();
            $('#project_risks').find('#project_container').hide();
            $('#project_risks').find('#pager').hide();
            $('#project_risks').find('p').hide();
        });
    }
    if(showMenu['issue'] == 1 && yourFormFilter['issue'] == 1){
        var url = <?php echo json_encode($html->url('/project_issues/index/'. $id)) ?>;
        $.get(url, null, function(text){
            var element1 = $(text).find('.wd-list-project').html();
            $('#project_issue').html(element1);
            $('#project_issue').find('.wd-title').hide();
            $('#project_issue').find('#project_container').hide();
            $('#project_issue').find('#pager').hide();
            $('#project_issue').find('p').hide();
        });
    }
    //location view
    var state = 1;
    var coord = /^\s*(\-?[0-9]+\.[0-9]+)\s*,\s*(\-?[0-9]+\.[0-9]+)\s*$/;
    var gapi = <?php echo json_encode($gapi) ?>;
    // dependency
    var toggling = false,
        open = '<?php echo $this->Html->url('/img/icon-plus.png') ?>',
        close = '<?php echo $this->Html->url('/img/icon-minus.png') ?>',
        _history = <?php echo empty($history) ? '{}' : json_encode($history) ?>,
        historyData = new $.z0.data(_history),
        data = <?php echo json_encode($dataDependency) ?>,
        projects = <?php echo json_encode($listProjects) ?>,
        dependencies = <?php echo json_encode($dependencies) ?>,
        project = <?php echo json_encode($projectName['Project']) ?>,
        colors = <?php echo json_encode($colors) ?>,
        list = <?php echo json_encode(array_unique($list)) ?>,
        cp = '<?php echo $project_id ?>',
        count = <?php echo json_encode($count) ?>,
        nodes,
        links,
        linkTracks = {},
        pTracks = {},
        diagram;
        pTracks[cp] = 1;

    function trackProject(id){
        if( typeof pTracks[id] == 'undefined' )pTracks[id] = 1;
    }

    function hasLink(i, d){
        var id = i + '-' + d;
        return typeof linkTracks[id] != 'undefined';
    }

    function trackLink(i, d){
        var id = i + '-' + d;
        if( !hasLink(i, d) )linkTracks[id] = 1;
    }

    function breakByHalf(text){
        if( text !== undefined){
            var arrofwords = text.split(" ");
            var middle = arrofwords.length / 2;
            arrofwords.splice(middle, 0, "\n");
            return arrofwords.join(" ");
        }
        return '';
    }

    function attachButton(id){
        if( !diagram.findNode('btn-' + id)[0] ){
            nodes.add({
                id: 'btn-' + id,
                group: 'button',
                size: 10
            });
        }
        repositionButton(id);
    }

    function detachButton(id){
        if( diagram.findNode('btn-' + id)[0] ){
            nodes.update({id: 'btn-' + id, hidden: true});
        }
    }

    function repositionButton(id){
        //reposition
        try {
            var node = diagram.findNode(id)[0];
            var btnNode = diagram.findNode('btn-' + id)[0];
            if( btnNode ){
                var x = node.x + node.shape.width/2 + 10;
                diagram.moveNode('btn-' + id, x, node.y);
            }
        } catch(ex){}
    }

    function updateButtons(){
        nodes.forEach(function(node){
            if( node && node.id.indexOf('btn') == -1 ){
                var Node = diagram.findNode(node.id)[0];
                var currentLinks = Node.edges.length, totalLinks = count[node.id];
                if( currentLinks < totalLinks ){
                    attachButton(node.id);
                } else {
                    detachButton(node.id);
                }
            }
        });
    }

    function expand(pid){
        if( toggling )return;
        jQuery.getJSON('<?php echo $this->Html->url('/project_dependencies/expand/') ?>' + pid, function(response){
            //do add node/links
            process(response.data, function(){
                //update links count for additional projects
                for(var i in response.count){
                    count[i] = response.count[i];
                }
                diagram.setData({nodes: nodes, edges: links});
                updateButtons();
            });
            toggling = false;
        });
    }

    function process(rawData, afterRender){
        var pos = _history['dependency_' + cp];
        for(var i in rawData){
            var node = rawData[i].ProjectDependency;
            if( typeof pTracks[ node.target_id ] == 'undefined' ){
                var newNode = {
                    id: node.target_id,
                    label: breakByHalf(projects[node.target_id]),
                    group: 'project'
                };
                if( typeof pos != 'undefined' && typeof pos[node.target_id] != 'undefined' ){
                    newNode.x = pos[node.target_id].x;
                    newNode.y = pos[node.target_id].y;
                }
                nodes.add(newNode);
                trackProject(node.target_id);
            }
            //make links
            var dataLinks = $.parseJSON(node.dependency_ids);
            for(var j in dataLinks){
                var linkId = dataLinks[j];
                var arrow = {};
                if( node.value == 3){ // left and right
                    arrow = {
                        to: 0,
                        from: 0
                    };
                } else if (node.value == 2){ // right
                    arrow = {
                        from: 0
                    };
                }else if(node.value == 1){ // left
                    arrow = {
                        to: 0
                    };
                }
                if( !hasLink(node.grouper, linkId) ){
                    links.add({
                        id: node.grouper + linkId,
                        from: node.project_id,
                        to: node.target_id,
                        label: dependencies[linkId],
                        title: dependencies[linkId],
                        color: colors[linkId],
                        arrows: arrow,
                        font: {
                            color: colors[linkId],
                            align: 'horizontal',
                            size: '12'
                        },
                        selectionWidth: 0
                    });
                    trackLink(node.grouper, linkId);
                }
            }
        }
        if( typeof afterRender == 'function' ){
            afterRender();
        }
    }

    function init(){
        //add nodes and links
        nodes = new vis.DataSet();
        links = new vis.DataSet();
        nodes.add({
            id: cp,
            label: breakByHalf(project.project_name),
            group: 'main'
        });
        process(data);
        //finalizing...
        var options = {
            interaction: {
                selectConnectedEdges: false
            },
            groups: {
                main: {
                    shape: 'box',
                    physics: false,
                    color: '#a4ccdf',
                    shadow: {
                        enabled: true,
                        size: 2,
                        x: 1,
                        y: 1
                    }
                },
                project: {
                    shape: 'box',
                    color: '#f0f0f0',
                    value: 3,
                    font: {
                        size: '12',
                        face: 'arial',
                        color: '#333'
                    },
                    physics: false,
                    shadow: {
                        enabled: true,
                        size: 2,
                        x: 1,
                        y: 1
                    }
                },
                button: {
                    image: open,
                    borderWidth: 1,
                    shape: 'circularImage',
                    size: 16,
                    physics: false,
                    title: '<?php __('Expand') ?>',
                    color: {
                        border: '#006da9',
                        background: '#fff'
                    }
                }
            },
            layout: {
                randomSeed: 915113
                // improvedLayout: true
            }
        };
        var dataset = {
            nodes: nodes,
            edges: links
        };
        diagram = new vis.Network(document.getElementById('diagram'), dataset, options);
        updateButtons();
        //attach events
        diagram.on('dragEnd', function(params){
            var node = params.nodes[0];
            if( node && node.indexOf('btn-') != -1 ){
                //reposition the btn
                repositionButton(node.replace('btn-', ''));
            }
        });
        diagram.on('dragging', function(params){
            var node = params.nodes[0];
            if( node && node.indexOf('btn-') == -1 ){
                //reposition the btn
                repositionButton(node);
            }
        });
        diagram.on('click', function(params){
            var node = params.nodes[0];
            if( node && node.indexOf('btn-') != -1 ){
                expand(node.replace('btn-', ''));
            }
        });
        jQuery('#diagram').append('<div id="dependency-info"></div>');
        jQuery.each(dependencies, function(i, v){
            jQuery('#dependency-info').append('<dl><dt style="background-color: ' + colors[i] + '"></dt><dd>' + v + '</dd></dl>');
        });
    }
    // jQuery(document).ready(init);
    // order html.
    var digramTemp = <?php echo json_encode($diagramTemp) ?>;
    var locationTemp = <?php echo json_encode($locationTemp) ?>;
    var globalTemp = <?php echo json_encode($globalTemp) ?>;
    var ganttTemp = <?php echo json_encode($ganttTemp) ?>;
    var phaseTemp = $('#div_phase').html();
    var projectTaskTemp = $('#div_project_task').html();
    var financeTemp = $('#div_finance_plus').html();
    $('#div_finance_plus').html('');
    var externalTemp = $('#div_external_cost').html();
    var internalTemp = $('#div_internal_cost').html();
    var issueTemp = $('#div_issue').html();
    var riskTemp = $('#div_risk').html();
    var milestoneTemp = $('#div_milestone').html();
    $('#div_milestone').html('');
    var weatheTemp = $('#div_weather').html();
    $('#div_weather').html('');
    var yourFormTemp = $('#div_your_form').html();
    $.each(yourFormFilter, function(index, value){
        switch (index){
            case 'your_form':
                if(value != 0){
                    $('#wd-fragment-temp').append(yourFormTemp);
                }
            break;
            case 'weather':
                if(value != 0){
                    $('#wd-fragment-temp').append(weatheTemp);
                }
            break;
            case 'gantt':
                if(value != 0){
                    $('#wd-fragment-temp').append(ganttTemp);
                }
            break;
            case 'milestone':
                if(value != 0){
                    $('#wd-fragment-temp').append(milestoneTemp);
                }
            break;
            case 'risk':
                if(value != 0){
                    $('#wd-fragment-temp').append(riskTemp);
                }
            break;
            case 'issue':
                if(value != 0){
                    $('#wd-fragment-temp').append(issueTemp);
                }
            break;
            case 'location':
                $('#wd-fragment-temp').append(locationTemp);
                if(value == 0){
                    $('#div_location').css('display','none');
                }
            break;
            case 'dependency':
                $('#wd-fragment-temp').append(digramTemp);
                if(value == 0){
                    $('#div_diagram').css('display','none');
                }
            break;
            case 'global_view':
                if(value != 0){
                    $('#wd-fragment-temp').append(globalTemp);
                }
            break;
            case 'buget_internal':
                if(value != 0){
                    $('#wd-fragment-temp').append(internalTemp);
                }
            break;
            case 'budget_externals':
                if(value != 0){
                    $('#wd-fragment-temp').append(externalTemp);
                }
            break;
            case 'project_task':
                if(value != 0){
                    $('#wd-fragment-temp').append(projectTaskTemp);
                }
            break;
            case 'phase':
                if(value != 0){
                    $('#wd-fragment-temp').append(phaseTemp);
                }
            break;
            case 'finance_plus':
                if(value != 0){
                    $('#wd-fragment-temp').append(financeTemp);
                }
            break;
        }
    });
    var element = $('#mcs1_container').find('.gantt-line').last();
    var e = $(element).find('div');
    var _top = 0;
    var oldleft = curleft = 0;
    if($(element).parent().closest('tr').hasClass('gantt-ms') === true){
        $(e).each(function(index, value){
            var position = $(value).position();
            var _left = position.left;
            curleft = _left;
            if( (curleft >= (oldleft - 150)) && (curleft <= (oldleft + 150 )) ){
                _top = _top + 16;
                $(value).css('top', _top + 'px');
                $(element).css('height',(16 + _top) + 'px');
            }
            oldleft = _left;
        });
    }
    var settings = {};
    $(document).ready(function () {
        var years    = <?php echo json_encode($setYear); ?>,
        manDays    = <?php echo json_encode($manDays); ?>,
        dataSets    = <?php echo !empty($dataSets) ? json_encode($dataSets) : json_encode(array()); ?>;
        settings = {
                title: "<?php echo __($md, true) . ' ' . __('Planed Follow Up', true);?>",
                description: years,
                padding: { left: 5, top: 5, right: 5, bottom: 5 },
                titlePadding: { left: 90, top: 0, right: 0, bottom: 10 },
                source: dataSets,
                categoryAxis:
                    {
                        dataField: 'date',
                        description: '',
                        showGridLines: false,
                    },
                colorScheme: 'scheme02',
                seriesGroups:
                    [
                        {
                            type: 'spline',
                            showLabels: false,//default
                            valueAxis:
                            {
                                axisSize: 'auto',
                                minValue: 0,
                                maxValue: manDays,
                                unitInterval: manDays,
                                description: '',
                                displayValueAxis: false
                            },
                            series: [
                                    // { dataField: 'estimation', displayText: 'Estimation', labelOffset: {x: 0, y: 10}},
                                    { dataField: 'consumed', displayText: '<?php echo __('Consumed', true);?>', labelOffset: {x: 0, y: 0}, color: '#538FFA'},
                                    { dataField: 'validated', displayText: '<?php echo __('Planed', true);?>', labelOffset: {x: 0, y: 0}, color: '#E44353'}

                                ]
                        },
                    ]
            };
            // dash board budget external
            <?php
            foreach($dataExternals as $_external=> $_dataExternal){ ?>
                var years    = <?php echo json_encode($_dataExternal['setYearExternal']); ?>,
                manDays    = <?php echo json_encode($_dataExternal['manDayExternals']); ?>,
                dataSets    = <?php echo json_encode($_dataExternal['dataSetsExternal']); ?>;
                var settingsExternal = {
                        title: "<?php echo $_dataExternal['setProviderName']; echo " : "; echo $_dataExternal['setNameExternal']; echo " "; echo __($md, true) . ' ' . __('Planed Follow Up', true);?>",
                        description: years,
                        padding: { left: 5, top: 5, right: 5, bottom: 5 },
                        titlePadding: { left: 90, top: 0, right: 0, bottom: 10 },
                        source: dataSets,
                        categoryAxis:
                            {
                                dataField: 'date',
                                description: 'Time',
                                showGridLines: false
                            },
                        colorScheme: 'scheme02',
                        seriesGroups:
                            [
                                {
                                    type: 'spline',
                                    showLabels: false,//default
                                    valueAxis:
                                    {
                                        axisSize: 'auto',
                                        minValue: 0,
                                        maxValue: manDays,
                                        unitInterval: manDays/10,
                                        description: '',
                                        displayValueAxis: false
                                    },
                                    series: [
                                            //{ dataField: 'estimation', displayText: 'Estimation', labelOffset: {x: 0, y: 10}},
                                            { dataField: 'consumed', displayText: '<?php echo __('Consumed', true);?>', labelOffset: {x: 0, y: 10}, color: '#f05a24'},
                                            { dataField: 'planed', displayText: '<?php echo __('Planed', true);?>', labelOffset: {x: 0, y: -10}, color: '#0cb0e0'}

                                        ]
                                },
                                {
                                    type: 'spline',
                                    showLabels: true,
                                    showLegend: true,
                                    columnsGapPercent: 100,
                                    valueAxis: {
                                        minValue: 0,
                                        maxValue: manDays,
                                        unitInterval: manDays/10,
                                        description: '',
                                        displayValueAxis: false

                                    },
                                    series: [
                                                {
                                                    dataFieldFrom: 'planed',
                                                    dataFieldTo: 'consumed',
                                                    //displayText: 'Different between Consumed and Validated',
                                                    formatFunction: caculate,
                                                    color: '#FF0000',
                                                    labelOffset: {x: 20, y: 0}
                                                }
                                            ]

                                }
                            ]
                    };
                    $('#budget_external_<?php echo $_external; ?>').jqxChart(settingsExternal);
                <?php } ?>
            function caculate(value){
               value = value.from - value.to;
               return  Math.round(value * 100) / 100 ;
            }
            // $('#budget_db').jqxChart(settings);
            if( yourFormFilter['weather'] == 1 && $showKpiBudget ){
                var svgString1 = new XMLSerializer().serializeToString(document.querySelector('#svg_kpi_1'));
                // console.log(svgStr/ing1);
                $('#svg_kpi_1').css('margin-top', '12px');
                var canvas1 = document.getElementById("canvas_kpi");
                var ctx1 = canvas1.getContext("2d");
                var DOMURL1 = self.URL || self.webkitURL || self;
                var img1 = new Image();
                // img1.crossOrigin = "Anonymous";
                img1.crossOrigin = '';
                img1.crossOrigin='anonymous'
                var svg1 = new Blob([svgString1], {type: "image/svg+xml;charset=utf-8"});
                var url1 = DOMURL1.createObjectURL(svg1);
                img1.src = url1;
                img1.onload = function() {
                    try{
                        ctx1.drawImage(img1, 0, 0);
                        var png1 = canvas1.toDataURL("image/png");
                        document.querySelector('#png-container_kpi').innerHTML = '<img class="img_budget_export" style="display: none; width: 270px;float: left;height: 140px;margin:0; margin-top: 50px" src="'+png1+'"/>';
                        DOMURL1.revokeObjectURL(png1);
                    }catch(e){
                        var b641 = btoa(unescape(encodeURIComponent( svgString1 )));
                        var url1 = "data:image/svg+xml;base64," + b641;
                        // console.log(url1);
                        document.querySelector('#png-container_kpi').innerHTML = '<img class="img_budget_export" style="display: none;width: 90px;float: left;height: 90px;margin:0; margin-top: 50px" src="'+url1+'"/>';
                    }
                };
                setTimeout(function(){
                    $('.wd-table').find('#svgChart').each(function(val, index){
                        var type = $(index).closest('div').data('type');
                        var svgString = new XMLSerializer().serializeToString(index);
                        var canvas = document.getElementById("canvas_" + type);
                        canvas.width = 900;
                        canvas.height = 300;
                        var ctx = canvas.getContext("2d");
                        var DOMURL = self.URL || self.webkitURL || self;
                        var img = new Image();
                        img.width = 900;
                        img.height = 300;
                        // img.crossOrigin = "Anonymous";
                        img.crossOrigin = '';
                        img.crossOrigin='anonymous'
                        var svg = new Blob([svgString], {type: "image/svg+xml;charset=utf-8"});
                        var url = DOMURL.createObjectURL(svg);
                        img.src = url;
                        img.onload = function() {
                            try{
                                ctx.drawImage(img, 0, 0);
                                var png = canvas.toDataURL("image/png");
                                var style = 'display: none; width: 860px;float: left;height: 280px;margin:0; margin-left: 270px';
                                if(type == 'budget'){
                                    style = 'display: none; width: 860px;float: left;height: 280px;margin:0;';
                                }
                                document.querySelector('#png-container_' + type).innerHTML = '<img class="img_budget_export" style="'+style+'" src="'+png+'"/>';
                                DOMURL.revokeObjectURL(png);
                            }catch(e){
                                var b64 = btoa(unescape(encodeURIComponent( svgString )));
                                var url = "data:image/svg+xml;base64," + b64;
                                var style = 'display: none;width: 900px;float: left;height: 280px;margin:0; margin-left: 270px';
                                if(type == 'budget'){
                                    style = 'display: none;width: 900px;float: left;height: 280px;margin:0;margin-left: 180px;';
                                }
                                document.querySelector('#png-container_' + type).innerHTML = '<img class="img_budget_export" style="'+style+'" src="'+url+'"/>';
                            }
                        };
                    });
                }, 2000);
            }
    });
    function expandScreen(){
        $('#table-control').hide();
        $('#expand-btn').hide();
        $('#wd-container-main').addClass('fullScreen');
        $('#wd-tab-content').removeClass('normal-scroll');
        // $('#wd-tab-content').addClass('full-scroll');
        $('#collapse').show();
        $('#closePopup').hide();
        $(window).resize();
    }
    function collapseScreen(){
        $('#table-control').show();
        $('#expand-btn').show();
        $('#collapse').hide();
        $('#wd-tab-content').removeClass('full-scroll');
        $('#wd-tab-content').addClass('normal-scroll');
        $('#wd-container-main').removeClass('fullScreen');
        $('#wd-tab-content').css('height', '800px');
        $('#closePopup').show();
        $(window).resize();
    }
    var _width = $('#global_view').find('img').width();
    var _left = (1400 - _width)/2;
    $('#global_view').find('img').css('margin-left', _left+'px');
    
    if(document.getElementById('myCanvas')){
        var prog = draw_progress('myCanvas');
    } 
    if(document.getElementById('myCanvas-2')){
        var prog = draw_progress('myCanvas-2');
    } 
    if(document.getElementById('myCanvas-3')){
        var prog = draw_progress('myCanvas-3');
    }
    
    (function($){
        $("#right, #left").click(function() {
            var dir = this.id=="right" ? '+=' : '-=' ;
            $(this).closest(".budget-progress").find('#budget-inner').stop().animate({scrollLeft: dir+'500'}, 1000);
        });
        
    })(jQuery);
</script>
<div id="overlay-container">
    <div id="overlay-wrapper"></div>
    <div id="overlay-box">
        Please wait, Preparing export ...
    </div>
</div>
