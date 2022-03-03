<?php
$gapi = GMapAPISetting::getGAPI();
App::import("vendor", "str_utility");
$str_utility = new str_utility();
echo $html->script(array(
    'jquery.validation.min',
    'html2canvas',
    'jquery.html2canvas.yourform',
    'vis.min',
    'dashboard/jqx-all',
    'dashboard/jqxchart',
	'autosize.min',
));
echo $html->css(array(
    'gantt_v2_1',
    'business',
    'vis.min',
));
?>
<style>
	.log-content, .log-comment{
		white-space: pre-line;
	}
	.budget-progress .jqx-chart-legend-text{	
		display: block !important;
		opacity: 0;
		margin: 0 10px;
	}
    .progress-circle .progress-value p{
        width: 75px;
    }
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

    fieldset div.wd-area > input{
        float: none;
        display: block;
        width: 94.2%;
        border: none;
    }
    fieldset div.wd-input select:focus, fieldset div.wd-input input:focus, fieldset div.wd-input textarea:focus{
        border: none;
    }
    #carousel .flex-active-slide img{
        cursor: pointer;
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
    .export-pdf-icon-all{
        background: url("/img_z0g/export-pdf.png") no-repeat !important;
        display: inline-block;
        width: 32px;
        height: 32px;
        vertical-align: top;
        opacity: 1;
    }
    .button-setting{
        display: inline-block;
        width: 32px;
        height: 32px;
        vertical-align: top;
        opacity: 1;
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
        max-width: 100%;
    }
    .gantt-chart-wrapper{
        overflow: auto;
    }
    #chart-wrapper{
        margin: 0 auto;
    }
    /*end*/
    #diagram {
        width: calc( 100% - 2px);
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
		margin-bottom: 10px;
    }
	.kpi-log .log-body {
		margin: 5px 5px 5px 52px;
	}
    #add-activity{
        display: none;
    }
    .normal-scroll{
        height: 800px;
        overflow-y: auto;
        overflow-x: hidden;
    }
    .wd-tab .wd-panel{
        border: none;
    }
    .full-scroll{
        height: 850px;
        overflow-y: auto;
        overflow-x: hidden;
        border: 1px solid #ccc;
    }
    .group-content > h3{
        background: #67a7ca;
    }
    .wd-input p img{
        width: auto;
        height: auto;
        float: none;
        margin-right: 0;
        margin-top: 0;
    }
    .wd-input p{
        padding: 3px;
        margin-bottom: 10px;
        padding-left: 0;
    }
    .wd-weather-list-dd ul li{
        width: 100px;
    }
    #table-cost table{width:80%;}#table-cost table tr td{border:1px solid #d4d4d4;text-align:center;padding:5px;}#table-cost table tr td.cost-header{background-color:#64a3c7;color:#FFF;}#table-cost table tr td.cost-md{background-color:#75923C;color:#FFF;}#table-cost table tr td.cost-euro{background-color:#95B3D7;color:#FFF;}.cost-disabled{background-color:#F5F5F5;}.checkbox,.wd-weather-list ul li input,.wd-weather-list ul li img,.wd-weather-list-dd ul li input,.wd-weather-list-dd ul li img{float:left;} .highcharts-container{ border:1px solid #999 !important;}.budget_external_chart{ margin-bottom:30px;}
    fieldset div.wd-input.wd-weather-list-dd{
        width: 500px;
    }
    #table-control{
        margin: 0 0 0 -10px !important;
    }
	#absence{
		float: none;
	}
    .absence-fixed th,.absence-fixed td.st{
        border-right : 1px solid #fff;
        color: #fff;
        text-align: left;
    }
    .absence-fixed .st a{
        color: #fff;
    }
    .absence-fixed .st strong{
        font-size: 0.8em;
        color : #FE4040;
    }
    #absence-scroll {
        overflow-x: scroll;
    }
    .ch-absen-validation{
        background-color: #F0F0F0;
    }
    .monday_am{
        border-left: 2px solid red;
    }
    #absence-wrapper {
        margin: 0 !important;
    }
    #absence-table tr td.ch-absen-validation{background-color: #c3dd8c;}
    #absence-wrapper .absence-fixed{ width: 99% !important;}
    #thColID{ width:8%; } #thColEmployee{ width:70%;}
    #absence th.colThDay{min-width:172px;max-width:172px;width:172px;overflow:hidden;}
    .am, .pm{
        overflow:hidden;
        padding-left: 0;
        padding-right: 0;
    }
    .am span, .pm span{
        width:100%;
        word-break:break-all;
    }
    .absence-fixed tbody tr {
      border: 1px solid #ccc;
    }
    .absence-fixed th {
      background: url(../img/front/bg-head-table.png) repeat-x #64a3c7;
      border-right: 1px solid #fff;
    }
    .absence-fixed tr th {
        padding: 5px;
      text-align: center;
      vertical-align: middle;
      border: 1px solid #fff;
    }
    .absence-fixed tbody td {
        border-right: 1px solid #ccc;
        text-align: right;
        vertical-align: middle;
        padding: 5px;
    }
    .absence-fixed td.st {
      background: url(../img/front/bg-head-table.png) repeat-x #5588B6;
      border: 1px solid #CACACA;
      color: #fff;
      vertical-align: middle;
      padding-left: 6px;
    }
    .absence-fixed .no{
        text-align: center;
    }
    .rp-waiting span{
        background-color: #E47E0A;
    }
    .absence-fixed tbody td span {
      padding: 3px;
      display: block;
    }
    .absence-fixed tbody td.ct {
      text-align: center;
      background-color: #E8F0FA;
      font-weight: bold;
    }
    .rp-holiday span {
      background-color: #ffff00;
    }
    .absence-fixed td.ui-selected {
      background: none repeat scroll 0 0 #F39814;
      color: white;
    }
    .fixed {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 9999;
        background: #f0f0f0;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.6);
        margin: 0 !important;
    }
    #menu {
        margin-bottom: 20px !important;
    }
    #table-control td {
        padding-top: 5px;
        vertical-align: middle;
    }
    #auto-cell {
        padding: 2px;
        text-align: center;
    }
    #profit{
        padding: 6px !important;
        border: solid 1px #c0c0c0 !important;
    }
    #table-freezer {
        width: 520px;
        float: left;
        table-layout:fixed;
    }
    #table-freezer tr td{
        height: 20px;
    }
    #table-scroller {
        overflow-x: hidden;
        overflow-y: auto;
    }
    #table-scroller table {
        float: left;
    }
    .absence-fixed tbody td{
        min-width: 120px;
        max-width: 130px;
    }
    .absence-fixed tr th{
        min-width: 120px;
        max-width: 130px;
    }
    .table-content tbody td {
        min-width: 120px;
        max-width: 130px;
        height: 20px;
    }
    #wd-container-footer{
        display: none;
    }
   
    .task_blue_bg {
        background-image: url(/img/extjs/icon-square.png);
        background-repeat: no-repeat;
        min-height: 16px;
    }
    .task_red_bg {
        background-image: url(/img/extjs/icon-triangle.png);
        background-repeat: no-repeat;
        min-height: 16px;
    }
    .order p{
        min-height: 16px;
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
    #sales-purchases{
        font-weight: bold;
        /*font-size: 16px;
        line-height: 13px;*/
        background: #ccc;
    }
    #data-sales-purchases{
        background: #ccc;
        font-weight: bold;
    }
    .finance_table td{
        min-width: 110px;
    }
    body{
        overflow-y: hidden; 
    }
    .wd-weather-list li{display:none; width: 42px; margin-left: 0}
    .wd-weather-list li.checked{display: block}
    .wd-weather-list li input{display :none}
    .wd-weather-list ul li{
        width: 42px;
        padding-right: 0;
    }
    .wd-weather-list ul li img{
        width: initial;
        height: initial;
    }
    @media(min-width: 1370px){
        .wd-project-detail .wd-title{
            right: 46px
        }
        
    }
    .group-content .progress-circle{
        width: 270px;
    }
	fieldset div.wd-input{
		float: none;
		display: block;
	}
</style>
<?php
// pm template
if($yourFormFilter['your_form'] == 1){
    // pm template
    $pmTemplate = '';
        $pmTemplate = '<span>';
        foreach($listEmployeeManagers['PM'] as $idPm => $namePm):
            $pmTemplate .=  $_employees['pm'][$idPm].', ';
        endforeach;
        $pmTemplate .= '</span>';
    $urlPm = $this->UserFile->avatar($this->data['Project']['project_manager_id']);
    $pmTemplate .= '<img width =  30 src="'. $urlPm .'" />';
    //end pm template

    // chiefbusiness template
     // ob_clean();

    // debug($listEmployeeManagers['CB']);
    $chiefTemplate = '';
        $chiefTemplate = '<span>';
        foreach($listEmployeeManagers['CB'] as $idPm => $namePm):
            $chiefTemplate .=  $_employees['pm'][$idPm].', ';
        endforeach;
        $chiefTemplate .= '</span>';
    $urlPm = $this->UserFile->avatar($this->data['Project']['project_manager_id']);
    $chiefTemplate .= '<img width =  30 src="'. $urlPm .'" />';

    
    // technical field
   
    // debug($listEmployeeManagers['TM']);
    // debug($_employees['pm']);
    // exit;
    $technicalTemplate = '';
        $technicalTemplate = '<span>';
        foreach($listEmployeeManagers['TM'] as $idPm => $namePm):
            $technicalTemplate .=  $_employees['pm'][$idPm].', ';
        endforeach;
        $technicalTemplate .= '</span>';
    $urlPm = $this->UserFile->avatar($this->data['Project']['project_manager_id']);
    $technicalTemplate .= '<img width =  30 src="'. $urlPm .'" />';



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
            'html' => '<p>' . (!empty($this->data['Project']['project_name']) ? $this->data['Project']['project_name'] : "") . '</p>',
        ),
        'project_code_1' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Project Code 1', true),
            'html' => '<p>' . (!empty($this->data['Project']['project_code_1']) ? $this->data['Project']['project_code_1'] : "") . '</p>',
        ),
        'company_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Company', true),
            'html' => '<p style="padding-top: 6px; width: 92.9%; height: 15px">' . $name_company . '</p>'
        ),
        'long_project_name' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Project long name', true),
            'html' => '<p>' . (!empty($this->data['Project']['long_project_name']) ? $this->data['Project']['long_project_name'] : "") . '</p>',
        ),
        'project_code_2' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Project Code 2', true),
            'html' => '<p>' . (!empty($this->data['Project']['project_code_2']) ? $this->data['Project']['project_code_2'] : "") . '</p>',
        ),
        'project_manager_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Project Manager', true),
            'html' => $pmTemplate
        ),
        'chief_business_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Chief Business', true),
            'html' => $chiefTemplate,
        ),
        'technical_manager_id' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Technical Manager', true),
            'html' => $technicalTemplate,
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
            'html' => '<p style="white-space:pre-line; padding: 10px; width: 93.5%; padding-bottom: 10px; padding-left: 0">'.$this->data['Project']['issues'].'</p>'
        ),
        'primary_objectives' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Primary Objectives', true),
            'html' => '<p style="white-space:pre-line; padding: 10px; width: 93.5%; padding-bottom: 10px; padding-left: 0">'.$this->data['Project']['primary_objectives'].'</p>'
        ),
        'project_objectives' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Project Objectives', true),
            'html' => '<p style="white-space:pre-line; padding: 10px; width: 93.5%; padding-bottom: 10px; padding-left: 0">'.$this->data['Project']['project_objectives'].'</p>'
        ),
        'constraint' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Constraint', true),
            'html' => '<p style="white-space:pre-line; padding: 10px; width: 93.5%; padding-bottom: 10px; padding-left: 0">'.$this->data['Project']['constraint'].'</p>'
        ),
        'remark' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Remark', true),
            'html' => '<p style="white-space:pre-line; padding: 10px; width: 93.5%; padding-bottom: 10px; padding-left: 0">'.$this->data['Project']['remark'].'</p>'
        ),
        'free_1' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Free 1', true),
            'html' => '<p style="white-space:pre-line; padding: 10px; width: 93.5%; padding-bottom: 10px; padding-left: 0">'.$this->data['Project']['free_1'].'</p>'
        ),
        'free_2' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Free 2', true),
            'html' => '<p style="white-space:pre-line; padding: 10px; width: 93.5%; padding-bottom: 10px; padding-left: 0">'.$this->data['Project']['free_2'].'</p>'
        ),
        'free_3' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Free 3', true),
            'html' => '<p style="white-space:pre-line; padding: 10px; width: 93.5%; padding-bottom: 10px; padding-left: 0">'.$this->data['Project']['free_3'].'</p>'
        ),
        'free_4' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Free 4', true),
            'html' => '<p style="white-space:pre-line; padding: 10px; width: 93.5%; padding-bottom: 10px; padding-left: 0">'.$this->data['Project']['free_4'].'</p>'
        ),
        'free_5' => array(
            'label' => __d(sprintf($_domain, 'Details'), 'Free 5', true),
            'html' => '<p style="white-space:pre-line; padding: 10px; width: 93.5%; padding-bottom: 10px; padding-left: 0">'.$this->data['Project']['free_5'].'</p>'
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
                $htmlListMultiple = '';
                if(!empty($ProjectMultiLists['project_list_multi_' . $num])){
                    foreach ($ProjectMultiLists['project_list_multi_' . $num] as $val) {
                        $htmlListMultiple .= $datasets['list_muti_' . $num][$val] . ', ';
                    }
                }
                   
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
                    // 'html' => '<p>' . (!empty($datasets[$this->data['Project']['list_' . $num]]) ? $datasets[$this->data['Project']['list_' . $num]] : '') . '</p>',
                    'html' => $datasets['list_' . $num][$this->data['Project']['list_' . $num]],
                );
            }
            //date
            $maps['date_' . $num] = array(
                'label' => __d(sprintf($_domain, 'Details'), 'Date ' . $num, true),
                'html' => $this->Form->input('date_' . $num, array('type' => 'text', 'class' => 'wd-date', 'div' => false, 'label' => false, 'value' => $str_utility->convertToVNDate($this->data['Project']['date_' . $num])))
            );

        }
        // exit;
        if( $num <= 16 ){
            //price
            $_class = 'numeric-value';
            if( $num > 6 ) {
                $_class .= ' not-decimal';
            }
            $maps['price_' . $num] = array(
                'label' => __d(sprintf($_domain, 'Details'), 'Price ' . $num, true),
                'html' => '<span style="display: inline-block; margin: 0px">' . (!empty($this->data['Project']['price_' . $num]) ? $this->data['Project']['price_' . $num] : '0.00') . '</span><span style="margin: 0;margin-left: 5px; ">'.$budget_settings.'</span>'
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
            // 'html' => $this->Form->input('text_one_line_' . $num, array('div' => false, 'label' => false, 'type' => 'text')),
            'html' => '<p style="white-space:pre-line;">' . (!empty($this->data['Project']['text_one_line_' . $num]) ? $this->data['Project']['text_one_line_' . $num] : "") . '</p>',

        );
        //text two line
        $maps['text_two_line_' . $num] = array(
            'label' => __d(sprintf($_domain, 'Details'), 'Text two line ' . $num, true),
            // 'html' => $this->Form->input('text_two_line_' . $num, array('class' => 'textarea-limit', 'div' => false, 'label' => false, 'rows' => '2', 'style' => 'height:35px;')),
            'html' => '<p style="white-space:pre-line;">' . (!empty($this->data['Project']['text_two_line_' . $num]) ? $this->data['Project']['text_two_line_' . $num] : "") . '</p>',

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
$htmlWea .= '<div class="wd-input wd-weather-list">';
$htmlWea .= '<ul style="float: left; display: inline;">';
$checked1 = $ProjectArms['ProjectAmr']['weather'] == 'sun' ? "checked" : '';
$checked2 = $ProjectArms['ProjectAmr']['weather'] == 'cloud' ? "checked" : '';
$checked3 = $ProjectArms['ProjectAmr']['weather'] == 'rain' ? "checked" : '';
$htmlWea .= '<li class="'.$checked1.'"><input checked="true" style="width: 25px; margin-top: 8px;" '.$checked1.' name="data[ProjectAmr][weather][]" value="sun" type="radio" /> <img title="Sun"  src="' . $html->url('/img/sun.png') .'"/></li>';
$htmlWea .= '<li style="padding-right: 15px" class="'.$checked2.'"><input type="radio" value="cloud" '.$checked2.' name="data[ProjectAmr][weather][]" style="width: 25px;margin-top: 8px;" /> <img title="Cloud" src="'. $html->url('/img/cloud.png') .'"  /></li>';
$htmlWea .= '<li class="'.$checked3.'"><input type="radio" value="rain" '.$checked3.' name="data[ProjectAmr][weather][]" style="width: 25px;margin-top: 8px;"   /> <img title="Rain"  src="'. $html->url('/img/rain.png') .'"  /></li>';

$checked1 = $ProjectArms['ProjectAmr']['rank'] == 'up' ? "checked" : '';
$checked2 = $ProjectArms['ProjectAmr']['rank'] == 'down' ? "checked" : '';
$checked3 = $ProjectArms['ProjectAmr']['rank'] == 'mid' ? "checked" : '';
$htmlWea .= '<li class="'.$checked1.'" style=""><input checked="true" style="width: 25px; margin-top: 8px;" '.$checked1.' name="data[ProjectAmr][rank][]" value="up" type="radio" /> <img title="Up" src=" '. $html->url('/img/up.png') .'"  /></li>';
$htmlWea .= '<li class="'.$checked2.'"><input type="radio" value="down" '.$checked2.' name="data[ProjectAmr][rank][]" style="width: 25px;margin-top: 8px;" /> <img title="Down" src="'. $html->url('/img/down.png') .'"/></li>';
$htmlWea .= '<li class="'.$checked3.'"><input type="radio" value="mid" '.$checked3.' name="data[ProjectAmr][rank][]" style="width: 25px;margin-top: 8px;"   /> <img title="Mid"  src="'. $html->url('/img/mid.png').'" style=""/></li>';
$htmlWea .= '</ul>';
$htmlWea .= '</div>';
$maps['weather'] = array(
    'html' => $htmlWea
);
?>
<div id="wd-container-main" class="wd-project-detail">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>

             <div class="wd-tab"> <div class="wd-panel">
            <div class="wd-title" style="max-width: 1328px; margin: auto; position: relative; padding-left: 20px; padding-bottom: 10px; ">
                <div class="wd-head-left" style ="width: calc(100% - 110px); display: inline-block; position: relative;">
                    <h2 class="wd-t1" style="color: #ffb250; margin-top: 2px; vertical-align: top;"><?php echo sprintf(__("%s", true), $projectName['Project']['project_name']); ?></h2>
                    <?php if($yourFormFilter['weather']) : ?>
                    <div class="" style="width: 200px; text-align: right; display: inline-block;"><?php echo !empty($maps['weather']['html']) ? $maps['weather']['html'] : '';?></div>
                    <?php endif; ?>
                </div>
                <div class="wd-head-right" style ="width: 105px; display: inline-block; vertical-align: top;">
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
			<div>
				<h2 id="project-name" class="wd-t1" style="margin-top: 30px; color: #ffb250; font-size: 21px; display: none; margin-bottom: 30px;"><?php echo sprintf(__("%s", true), $projectName['Project']['project_name']); ?></h2>
				<?php if($yourFormFilter['weather']) : ?>
					<div class="weather-pdf" style="position: relative; top: 6px; left: 26px; width: 200px; text-align: right; display: none;"><?php echo !empty($maps['weather']['html']) ? $maps['weather']['html'] : '';?></div>
				<?php endif; ?>
			</div>
        </div>
		 <div id='div_your_form' style="display: none">
			 <div data-widget="your_form">
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
							
							<?php echo !empty($maps[$fieldName]['html']) ? $maps[$fieldName]['html'] : '';?>
						</div>
					<?php
					}

				}
					?>
			</div>
        </div>
<!-- finance ++ -->
<div id ="div_finance_two_plus" style="display: none">
    <?php 
     if ($showMenu['finance_two_plus'] && $yourFormFilter['finance_two_plus'] == 1) : ?>
    <div data-widget="finance_two_plus" id = "budget-chard" style="padding-top: 60px; clear:both; width: 100%;">
        <div id="inve-chard" style="float: left; width: 50%">
            <div class="budget-chard">
                <p><?php echo __d(sprintf($_domain, 'Finance_2'), 'Budget revised', true) .': '. number_format((!empty($totalTwoFinan['budget_revised']) ? $totalTwoFinan['budget_revised'] : 0), 2, '.', ' ') . ' '.$bg_currency ?></p>
                <p style="margin-top: 17px;"><?php echo __d(sprintf($_domain, 'Finance_2'), 'Latest estimate', true) .': '. number_format((!empty($totalTwoFinan['last_estimated']) ? $totalTwoFinan['last_estimated'] : 0), 2, '.', ' ')  . ' '.$bg_currency ?></p>
                <?php
                    if(empty($totalTwoFinan['budget_revised'])){
                        $totalTwoFinan['budget_revised'] = 0;
                    }
                    if(empty($totalTwoFinan['last_estimated'])){
                        $totalTwoFinan['last_estimated'] = 0;
                    }
                    $per = 0;
                    if($totalTwoFinan['budget_revised'] != 0) {
                        $per = round($totalTwoFinan['last_estimated']/$totalTwoFinan['budget_revised'] * 100,2);
                    }
                    $color_min = '#13FF02';
                    $color_max = '#15830D';
                    if( $totalTwoFinan['budget_revised'] == 0 && $totalTwoFinan['last_estimated'] == 0 ){
                        $width_bud = '0%';
                        $width_avan = '0';
                        $bg_color = 'green';
                        $per = 0;
                    } else if( $totalTwoFinan['budget_revised'] == 0 ){
                        $width_bud = '0%';
                        $width_avan = '80';
                        $bg_color = 'green';
                    } else if( (($totalTwoFinan['last_estimated'] > $totalTwoFinan['budget_revised']) && $totalTwoFinan['last_estimated'] > 0) || (($totalTwoFinan['last_estimated'] > 0) && ($totalTwoFinan['budget_revised'] <= 0)) ){
                        $color_min = '#F98E8E';
                        $color_max = '#FF0606';
                        $bg_color = 'red';
                        $width_bud = '80%';
                        $width_avan = (abs($totalTwoFinan['last_estimated'])/abs($totalTwoFinan['budget_revised'])*80);
                    } else {
                        $width_bud = '80%';
                        $width_avan = (abs($totalTwoFinan['last_estimated'])/abs($totalTwoFinan['budget_revised'])*80);
                        $bg_color = 'green';
                    }
                    $width_avan = $width_avan <= 100 ? $width_avan : 100;
                    $width_avan = $width_avan . '%';
                ?>
            </div>
            <div class="percent-chard">
                <div style="width: 50%">
                    <?php if($totalTwoFinan['budget_revised'] < 0){ ?>
                    <div style="height: 10px; width: <?php echo $width_bud ?>; background-color: #ccc; float: right"></div>
                    <?php } else { ?>
                    <div style="height: 10px; width: 0px; background-color: #ccc; float: right"></div>
                    <?php }
                    if($totalTwoFinan['last_estimated'] < 0){
                    ?>
                    <div style="margin-top: 10px; height: 10px; float: right; width: <?php echo $width_avan ?>; background-color: <?php echo $bg_color ?>"></div>
                    <?php } ?>
                </div>
                <div style="width: 50%; margin-left: 50%;">
                    <?php if($totalTwoFinan['budget_revised'] >= 0){ ?>
                    <div style="height: 10px; width: <?php echo $width_bud ?>; background-color: #ccc"></div>
                    <?php } else { ?>
                    <div style="height: 10px; width: 0px; background-color: #ccc"></div>
                    <?php }
                    if($totalTwoFinan['last_estimated'] >= 0){
                    ?>
                    <div style="margin-top: 10px; height: 10px; width: <?php echo $width_avan ?>; background-color: <?php echo $bg_color ?>"></div>
                    <?php } ?>
                </div>
            </div>
            <div class="circle-chard">
                <aside>
                    <svg id="svg_finance_two_plus" class="progress-pie" width="100%" height="100%" role="image" style="">
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
                </aside>
                <canvas id="canvas_finance_two_plus" style="display: none;"></canvas>
                <div id="png_finance_two_plus"></div>
            </div>
        </div>
        <div id="fon-chard" style="float: right; width: 50%">
            <div style="clear: both;">
                <p style="width: 20%; float: left"><?php echo __d(sprintf($_domain, 'Finance_2'), 'Budget revised', true)?></p>
                <p style="margin-left: 20%; width: 20%;"><?php echo number_format((!empty($totalTwoFinan['budget_revised']) ? $totalTwoFinan['budget_revised'] : 0), 2, '.', ' ') . ' '.$bg_currency ?></p>
                <p style="width: 9%; margin-left: 40%; margin-top: -25px"></p>
                <p style="width: 300px; height:20px; background-color: #ccc; margin-left: 50%; margin-top: -15px">&nbsp</p>
            </div>
            <div style="clear: both;">
                <p style="width: 20%; float: left"><?php echo __d(sprintf($_domain, 'Finance_2'), 'Latest estimate', true) ?></p>
                <p style="margin-left: 20%; width: 20%;"><?php echo number_format((!empty($totalTwoFinan['last_estimated']) ? $totalTwoFinan['last_estimated'] : 0), 2, '.', ' ') . ' '.$bg_currency ?></p>
                <?php
                    $totalTwoFinan['last_estimated'] = !empty($totalTwoFinan['last_estimated']) ? $totalTwoFinan['last_estimated'] : 0;
                    $_per = !empty($totalTwoFinan['budget_revised']) ? $totalTwoFinan['last_estimated']/$totalTwoFinan['budget_revised']*100 : 0;
                ?>
                <p style="width: 9%; margin-left: 40%; margin-top: -18px; text-align: right; <?php echo $_per > 100 ? 'color: red' : '' ?>"><?php
                    echo number_format($_per, 2, '.', ' ') . ' %';
                ?></p>
                <p style="width: <?php echo $_per*300/100 ?>px; height:20px; background-color: #f70707; margin-left: 50%; margin-top: -19px">&nbsp</p>
            </div>
            <div style="clear: both;">
                <p style="width: 20%; float: left"><?php echo __d(sprintf($_domain, 'Finance_2'), 'Engaged', true)?></p>
                <p style="margin-left: 20%; width: 20%;"><?php echo number_format((!empty($totalTwoFinan['engaged']) ? $totalTwoFinan['engaged'] : 0), 2, '.', ' ') . ' '.$bg_currency ?></p>
                <?php
                    $totalTwoFinan['engaged'] = !empty($totalTwoFinan['engaged']) ? $totalTwoFinan['engaged'] : 0;
                    $_per = !empty($totalTwoFinan['budget_revised']) ? $totalTwoFinan['engaged']/$totalTwoFinan['budget_revised']*100 : 0;
                ?>
                <p style="width: 9%; margin-left: 40%; margin-top: -18px; text-align: right; <?php echo $_per > 100 ? 'color: red' : '' ?>"><?php
                    echo number_format($_per, 2, '.', ' ') . ' %';
                ?></p>
                <p style="width: <?php echo $_per*300/100 ?>px; height:20px; background-color: #fdea02; margin-left: 50%; margin-top: -15px">&nbsp</p>
            </div>
            <div style="clear: both;">
                <p style="width: 20%; float: left"><?php echo __d(sprintf($_domain, 'Finance_2'), 'Bill', true) ?></p>
                <p style="margin-left: 20%; width: 20%;"><?php echo number_format((!empty($totalTwoFinan['bill']) ? $totalTwoFinan['bill'] : 0), 2, '.', ' ') . ' '.$bg_currency ?></p>
                <?php
                    $totalTwoFinan['bill'] = !empty($totalTwoFinan['bill']) ? $totalTwoFinan['bill'] : 0;
                    $_per = !empty($totalTwoFinan['budget_revised']) ? $totalTwoFinan['bill']/$totalTwoFinan['budget_revised']*100 : 0;
                ?>
                <p style="width: 9%; margin-left: 40%; margin-top: -18px; text-align: right; <?php echo $_per > 100 ? 'color: red' : '' ?>"><?php
                    echo number_format($_per, 2, '.', ' ') . ' %';
                ?></p>
                <p style="width: <?php echo $_per*300/100 ?>px; height:20px; background-color: rgba(255, 130, 1, 0.81); margin-left: 50%; margin-top: -13px">&nbsp</p>
            </div>
            <div style="clear: both;">
                <p style="width: 20%; float: left"><?php echo __d(sprintf($_domain, 'Finance_2'), 'Disbursed', true)?></p>
                <p style="margin-left: 20%; width: 20%;"><?php echo number_format((!empty($totalTwoFinan['disbursed']) ? $totalTwoFinan['disbursed'] : 0), 2, '.', ' ') . ' '.$bg_currency ?></p>
                <?php
                    $totalTwoFinan['disbursed'] = !empty($totalTwoFinan['disbursed']) ? $totalTwoFinan['disbursed'] : 0;
                    $_per = !empty($totalTwoFinan['budget_revised']) ? $totalTwoFinan['disbursed']/$totalTwoFinan['budget_revised']*100 : 0;
                ?>
                <p style="width: 9%; margin-left: 40%; margin-top: -18px; text-align: right; <?php echo $_per > 100 ? 'color: red' : '' ?>"><?php
                    echo number_format($_per, 2, '.', ' ') . ' %';
                ?></p>
                <p style="width: <?php echo $_per*300/100 ?>px; height:20px; background-color: #ca5959; margin-left: 50%; margin-top: -13px">&nbsp</p>
            </div>
        </div>
    </div>
    <?php
    
    $check = !empty($statusFilters['finance_two_plus']) ? $statusFilters['finance_two_plus'][0] : 0;
    $displayNone = !$showMenu['finance_two_plus'] || ($check == 1) ? 'display: none' : '';    ?>
    <div class="wd-input wd-area wd-none" style = "margin-top: 15px; <?php echo 'display: none' ?>">
        <div style="width: 100%; overflow-x: auto;">
            <table id = "absence" class="finance_table" style="width: auto;">
                <thead>
                    <tr>
                        <th></th>
                        <th colspan="8"><?php echo __('Total', true) ?></th>
                        <?php foreach ($financeTwoYear as $v) { ?>
                            <th colspan="8"><?php echo $v ?></th>
                        <?php } ?>
                    </tr>
                    <tr>
                        <th style="width: 200px;"><?php echo __('Name', true) ?></th>
                        <th style="width: 80px;"><?php echo __d(sprintf($_domain, 'Finance_2'), 'Budget initial', true) ?></th>
                        <th style="width: 80px;"><?php echo __d(sprintf($_domain, 'Finance_2'), 'Budget revised', true) ?></th>
                        <th style="width: 60px;"><?php echo __d(sprintf($_domain, 'Finance_2'), 'Latest estimate', true) ?></th>
                        <th style="width: 60px;"><?php echo __('%', true) ?></th>
                        <th style="width: 60px;"><?php echo __d(sprintf($_domain, 'Finance_2'), 'DR - DE', true) ?></th>
                        <th style="width: 60px;"><?php echo __d(sprintf($_domain, 'Finance_2'), 'Engaged', true) ?></th>
                        <th style="width: 60px;"><?php echo __d(sprintf($_domain, 'Finance_2'), 'Bill', true) ?></th>
                        <th style="width: 60px;"><?php echo __d(sprintf($_domain, 'Finance_2'), 'Disbursed', true) ?></th>
                        <?php foreach ($financeTwoYear as $v) { ?>
                            <th style="width: 80px;"><?php echo __d(sprintf($_domain, 'Finance_2'), 'Budget initial', true) ?></th>
                            <th style="width: 80px;"><?php echo __d(sprintf($_domain, 'Finance_2'), 'Budget revised', true) ?></th>
                            <th style="width: 60px;"><?php echo __d(sprintf($_domain, 'Finance_2'), 'Latest estimate', true) ?></th>
                            <th style="width: 60px;"><?php echo __('%', true) ?></th>
                            <th style="width: 60px;"><?php echo __d(sprintf($_domain, 'Finance_2'), 'DR - DE', true) ?></th>
                            <th style="width: 60px;"><?php echo __d(sprintf($_domain, 'Finance_2'), 'Engaged', true) ?></th>
                            <th style="width: 60px;"><?php echo __d(sprintf($_domain, 'Finance_2'), 'Bill', true) ?></th>
                            <th style="width: 60px;"><?php echo __d(sprintf($_domain, 'Finance_2'), 'Disbursed', true) ?></th>
                        <?php
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $httr = '';
                    foreach ($twoFinances as $key => $name) {
                        $httr2 = $httr1 = '';
                        foreach ($financeTwoYear as $_y) {
                            $httr2 .= '<td style="text-align: right;">'. number_format(((!empty($twoFinanceDetails[$key]) && !empty($twoFinanceDetails[$key][$_y]) && !empty($twoFinanceDetails[$key][$_y]["budget_initial"])) ? $twoFinanceDetails[$key][$_y]["budget_initial"] : 0),2,","," ") . ' €</td>';
                            $httr2 .= '<td style="text-align: right;">'. number_format((!empty($twoFinanceDetails[$key]) && !empty($twoFinanceDetails[$key][$_y]) && !empty($twoFinanceDetails[$key][$_y]["budget_revised"]) ? $twoFinanceDetails[$key][$_y]["budget_revised"] : 0),2,","," ") . ' €</td>';
                            $httr2 .= '<td style="text-align: right;">'. number_format((!empty($twoFinanceDetails[$key]) && !empty($twoFinanceDetails[$key][$_y]) && !empty($twoFinanceDetails[$key][$_y]["last_estimated"]) ? $twoFinanceDetails[$key][$_y]["last_estimated"] : 0),2,","," ") . ' €</td>';

                            $twoFinanceDetails[$key][$_y]['last_estimated'] = !empty($twoFinanceDetails[$key]) && !empty($twoFinanceDetails[$key][$_y]) && !empty($twoFinanceDetails[$key][$_y]['last_estimated']) ? $twoFinanceDetails[$key][$_y]['last_estimated'] : 0;
                            $twoFinanceDetails[$key][$_y]['budget_revised'] = !empty($twoFinanceDetails[$key]) && !empty($twoFinanceDetails[$key][$_y]) && !empty($twoFinanceDetails[$key][$_y]['budget_revised']) ? $twoFinanceDetails[$key][$_y]['budget_revised'] : 0;
                            $_p = $twoFinanceDetails[$key][$_y]['budget_revised'] != 0 ? ($twoFinanceDetails[$key][$_y]['last_estimated']/$twoFinanceDetails[$key][$_y]['budget_revised'])*100 : 0;

                            $httr2 .= '<td style="text-align: right;">' . number_format($_p, 2, ",", " "). ' %' . '</td>';
                            if($twoFinanceDetails[$key][$_y]['last_estimated'] - $twoFinanceDetails[$key][$_y]['budget_revised'] > 0){
                                $httr2 .= '<td style="text-align: right; color: red">' . number_format(($twoFinanceDetails[$key][$_y]['last_estimated'] - $twoFinanceDetails[$key][$_y]['budget_revised']), 2, ",", " "). ' '.$bg_currency . '</td>';
                            } else {
                                $httr2 .= '<td style="text-align: right; color: green">' . number_format(($twoFinanceDetails[$key][$_y]['last_estimated'] - $twoFinanceDetails[$key][$_y]['budget_revised']), 2, ",", " "). ' '.$bg_currency . '</td>';
                            }
                            $httr2 .= '<td style="text-align: right;">'. number_format((!empty($twoFinanceDetails[$key]) && !empty($twoFinanceDetails[$key][$_y]) && !empty($twoFinanceDetails[$key][$_y]["engaged"]) ? $twoFinanceDetails[$key][$_y]["engaged"] : 0),2,","," ") . ' €</td>';
                            $httr2 .= '<td style="text-align: right;">'. number_format((!empty($twoFinanceDetails[$key]) && !empty($twoFinanceDetails[$key][$_y]) && !empty($twoFinanceDetails[$key][$_y]["bill"]) ? $twoFinanceDetails[$key][$_y]["bill"] : 0),2,","," ") . ' €</td>';
                            $httr2 .= '<td style="text-align: right;">'. number_format((!empty($twoFinanceDetails[$key]) && !empty($twoFinanceDetails[$key][$_y]) && !empty($twoFinanceDetails[$key][$_y]["disbursed"]) ? $twoFinanceDetails[$key][$_y]["disbursed"] : 0),2,","," ") . ' €</td>';
                        }
                        $httr1 .= '<td>' . $name . '</td>';
                        $httr1 .= '<td style="text-align: right;">'. number_format((!empty($totalTwoFinan[$key]) && !empty($totalTwoFinan[$key]["budget_initial"]) ? $totalTwoFinan[$key]["budget_initial"] : 0),2,","," ") . ' €</td>';
                        $httr1 .= '<td style="text-align: right;">'. number_format((!empty($totalTwoFinan[$key]) && !empty($totalTwoFinan[$key]["budget_revised"]) ? $totalTwoFinan[$key]["budget_revised"] : 0),2,","," ") . ' €</td>';
                        $httr1 .= '<td style="text-align: right;">'. number_format((!empty($totalTwoFinan[$key]) && !empty($totalTwoFinan[$key]["last_estimated"]) ? $totalTwoFinan[$key]["last_estimated"] : 0),2,","," ") . ' €</td>';

                        $totalTwoFinan[$key]['last_estimated'] = !empty($totalTwoFinan[$key]) && !empty($totalTwoFinan[$key]['last_estimated']) ? $totalTwoFinan[$key]['last_estimated'] : 0;
                        $totalTwoFinan[$key]['budget_revised'] = !empty($totalTwoFinan[$key]) && !empty($totalTwoFinan[$key]['budget_revised']) ? $totalTwoFinan[$key]['budget_revised'] : 0;
                        $_p = $totalTwoFinan[$key]['budget_revised'] != 0 ? ($totalTwoFinan[$key]['last_estimated']/$totalTwoFinan[$key]['budget_revised'])*100 : 0;
                        $httr1 .= '<td style="text-align: right;">' . number_format($_p, 2, ",", " ") . ' %' . '</td>';
                        if($totalTwoFinan[$key]['last_estimated'] - $totalTwoFinan[$key]['budget_revised'] > 0){
                            $httr1 .= '<td style="text-align: right; color: red">' . number_format(($totalTwoFinan[$key]['last_estimated'] - $totalTwoFinan[$key]['budget_revised']), 2, ",", " ") . ' '.$bg_currency . '</td>';
                        } else {
                            $httr1 .= '<td style="text-align: right; color: green">' . number_format(($totalTwoFinan[$key]['last_estimated'] - $totalTwoFinan[$key]['budget_revised']), 2, ",", " ") . ' '.$bg_currency . '</td>';
                        }
                        $httr1 .= '<td style="text-align: right;">'. number_format((!empty($totalTwoFinan[$key]) && !empty($totalTwoFinan[$key]["engaged"]) ? $totalTwoFinan[$key]["engaged"] : 0),2,","," ") . ' €</td>';
                        $httr1 .= '<td style="text-align: right;">'. number_format((!empty($totalTwoFinan[$key]) && !empty($totalTwoFinan[$key]["bill"]) ? $totalTwoFinan[$key]["bill"] : 0),2,","," ") . ' €</td>';
                        $httr1 .= '<td style="text-align: right;">'. number_format((!empty($totalTwoFinan[$key]) && !empty($totalTwoFinan[$key]["disbursed"]) ? $totalTwoFinan[$key]["disbursed"] : 0),2,","," ") . ' €</td>';
                        $httr .= '<tr>' . $httr1 . $httr2 . '</tr>';
                    }
                    ?>
                    <tr style="background-color: #E8F0FA; font-weight: bold;">
                        <td></td>
                        <td style="text-align: right;"><?php  echo number_format((!empty($totalTwoFinan['budget_initial']) ? $totalTwoFinan['budget_initial'] : 0),2,',',' ') . ' '.$bg_currency ?></td>
                        <td style="text-align: right;"><?php  echo number_format((!empty($totalTwoFinan['budget_revised']) ? $totalTwoFinan['budget_revised'] : 0),2,',',' ') . ' '.$bg_currency ?></td>
                        <td style="text-align: right;"><?php  echo number_format((!empty($totalTwoFinan['last_estimated']) ? $totalTwoFinan['last_estimated'] : 0),2,',',' ') . ' '.$bg_currency ?></td>
                        <td style="text-align: right;"><?php
                            $totalTwoFinan['last_estimated'] = !empty($totalTwoFinan['last_estimated']) ? $totalTwoFinan['last_estimated'] : 0;
                            $totalTwoFinan['budget_revised'] = !empty($totalTwoFinan['budget_revised']) ? $totalTwoFinan['budget_revised'] : 0;
                            echo $totalTwoFinan['budget_revised'] != 0 ? number_format($totalTwoFinan['last_estimated']/$totalTwoFinan['budget_revised']*100,2,',', ' ') . ' %' : '0,00 %';
                        ?></td>
                        <?php if($totalTwoFinan['last_estimated'] - $totalTwoFinan['budget_revised'] > 0){ ?>
                            <td style="text-align: right; color: red"><?php  echo number_format(($totalTwoFinan['last_estimated'] - $totalTwoFinan['budget_revised']),2,',',' ') . ' '.$bg_currency ?></td>
                        <?php } else { ?>
                            <td style="text-align: right; color: green"><?php  echo number_format(($totalTwoFinan['last_estimated'] - $totalTwoFinan['budget_revised']),2,',',' ') . ' '.$bg_currency ?></td>
                        <?php } ?>
                        <td style="text-align: right;"><?php  echo number_format((!empty($totalTwoFinan['engaged']) ? $totalTwoFinan['engaged'] : 0),2,',',' ') . ' '.$bg_currency ?></td>
                        <td style="text-align: right;"><?php  echo number_format((!empty($totalTwoFinan['bill']) ? $totalTwoFinan['bill'] : 0),2,',',' ') . ' '.$bg_currency ?></td>
                        <td style="text-align: right;"><?php  echo number_format((!empty($totalTwoFinan['disbursed']) ? $totalTwoFinan['disbursed'] : 0),2,',',' ') . ' '.$bg_currency ?></td>
                        <?php foreach ($financeTwoYear as $value) { ?>
                            <td style="text-align: right;"><?php  echo number_format((!empty($totalTwoFinan[$value]) && !empty($totalTwoFinan[$value]['budget_initial']) ? $totalTwoFinan[$value]['budget_initial'] : 0),2,',',' ') . ' '.$bg_currency ?></td>
                            <td style="text-align: right;"><?php  echo number_format((!empty($totalTwoFinan[$value]) && !empty($totalTwoFinan[$value]['budget_revised']) ? $totalTwoFinan[$value]['budget_revised'] : 0),2,',',' ') . ' '.$bg_currency ?></td>
                            <td style="text-align: right;"><?php  echo number_format((!empty($totalTwoFinan[$value]) && !empty($totalTwoFinan[$value]['last_estimated']) ? $totalTwoFinan[$value]['last_estimated'] : 0),2,',',' ') . ' '.$bg_currency ?></td>
                            <td style="text-align: right;"><?php
                                $totalTwoFinan[$value]['last_estimated'] = !empty($totalTwoFinan[$value]) && !empty($totalTwoFinan[$value]['last_estimated']) ? $totalTwoFinan[$value]['last_estimated'] : 0;
                                $totalTwoFinan[$value]['budget_revised'] = !empty($totalTwoFinan[$value]) && !empty($totalTwoFinan[$value]['budget_revised']) ? $totalTwoFinan[$value]['budget_revised'] : 0;
                                echo $totalTwoFinan[$value]['budget_revised'] != 0 ? number_format($totalTwoFinan[$value]['last_estimated']/$totalTwoFinan[$value]['budget_revised']*100,2,',', ' ') . ' %' : '0,00 %';
                                ?></td>
                            <?php if($totalTwoFinan[$value]['last_estimated'] - $totalTwoFinan[$value]['budget_revised'] > 0){ ?>
                                <td style="text-align: right; color: red"><?php  echo number_format(($totalTwoFinan[$value]['last_estimated'] - $totalTwoFinan[$value]['budget_revised']),2,',',' ') . ' '.$bg_currency ?></td>
                            <?php } else { ?>
                                <td style="text-align: right; color: green"><?php  echo number_format(($totalTwoFinan[$value]['last_estimated'] - $totalTwoFinan[$value]['budget_revised']),2,',',' ') . ' '.$bg_currency ?></td>
                            <?php } ?>
                            <td style="text-align: right;"><?php  echo number_format((!empty($totalTwoFinan[$value]) && !empty($totalTwoFinan[$value]['engaged']) ? $totalTwoFinan[$value]['engaged'] : 0),2,',',' ') . ' '.$bg_currency ?></td>
                            <td style="text-align: right;"><?php  echo number_format((!empty($totalTwoFinan[$value]) && !empty($totalTwoFinan[$value]['bill']) ? $totalTwoFinan[$value]['bill'] : 0),2,',',' ') . ' '.$bg_currency ?></td>
                            <td style="text-align: right;"><?php  echo number_format((!empty($totalTwoFinan[$value]) && !empty($totalTwoFinan[$value]['disbursed']) ? $totalTwoFinan[$value]['disbursed'] : 0),2,',',' ') . ' '.$bg_currency ?></td>
                        <?php } ?>
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
<!-- end -->
<!-- project budget filcas -->
<?php
$totalXxxx = 0;
$md = !empty($employee_info['Company']['unit']) ? $employee_info['Company']['unit'] : 'M.D';
$viewChar = ($display == 'man-day') ? __($md, true) : __($budget_settings, true);
?>
<div id="div_fy_budget" style="display: none">
<?php if( !empty($showMenu['fy_budget']) && $showMenu['fy_budget'] && $yourFormFilter['fy_budget'] == 1) : ?>
<div data-widget="fy_budget" class="wd-input wd-area wd-none" style = "margin-top: 30px; width: 100%; <?php echo !$showMenu['fy_budget'] ? 'display: none' : '' ?>">
    <div class="group-content">
        <h3 class="half-padding"><span><?php echo __d(sprintf($_domain, 'KPI'), 'Budgets', true);?> </span></h3>
    </div>
    <?php
    $totalSales = $totalPurchase = $totalSaleTobill = $totalPurchaseTobill = $totalSaleBill = $totalPurchaseBill = 0;
    if(!empty($saleValues) && !empty($saleValues['order'])){
        foreach ($saleValues['order'] as $value) {
            $totalSales += $value;
        }
    }
    if(!empty($purchaseValues) && !empty($purchaseValues['order'])){
        foreach ($purchaseValues['order'] as $value) {
            $totalPurchase += $value;
        }
    }
    if(!empty($saleValues) && !empty($saleValues['toBill'])){
        foreach ($saleValues['toBill'] as $value) {
            $totalSaleTobill += $value;
        }
    }
    if(!empty($purchaseValues) && !empty($purchaseValues['toBill'])){
        foreach ($purchaseValues['toBill'] as $value) {
            $totalPurchaseTobill += $value;
        }
    }
    if(!empty($saleValues) && !empty($saleValues['billed'])){
        foreach ($saleValues['billed'] as $value) {
            $totalSaleBill += $value;
        }
    }
    if(!empty($purchaseValues) && !empty($purchaseValues['billed'])){
        foreach ($purchaseValues['billed'] as $value) {
            $totalPurchaseBill += $value;
        }
    }
    $nextOneYear = $year +1;
    ?>
        <table class="absence-fixed" id="table-freezer">
            <thead>
            <tr>
                <td></td>
                <td colspan="3">
                    <?php
                    $per = 0;
                    $color_min = '#13FF02';
                    $color_max = '#15830D';
                    if($totalSales != 0){
                        $per = round($totalPurchase/$totalSales * 100, 2);
                    }
                    if($per > 100){
                        $color_min = '#F98E8E';
                        $color_max = '#FF0606';
                    }
                    ?>
                    <div id="total-circle">
                        <aside style="border-left: 1px solid #ccc;">
                            <svg class="progress-pie" id="svg_fy_1" width="35%" height="70%" role="image" style="margin-bottom: -30px">
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
                                    <linearGradient id="pprgtotalfy" class="progress-pie__gradient">
                                        <stop offset="0%" stop-color="<?php echo $color_min ?>" />
                                        <stop offset="100%" stop-color="<?php echo $color_max ?>" />
                                    </linearGradient>
                                </defs>
                                <circle class="progress-pie__bg" filter="url(#drop-shadow)" r="50" cx="50" cy="50" fill="grey" opacity="0.3" />
                                <circle stroke="grey" stroke-width="9" fill="none" r="39.5" cx="50" cy="50" opacity="0.3" filter="url(#inset-shadow)" />
                                <circle class="progress-pie__ring" stroke="url(#pprgtotalfy)" stroke-width="9" stroke-dasharray="<?php echo abs($per/100*248.1858154) ?> 248.1858154" fill="none" r="39.5" cx="50" cy="50" transform="rotate(90, 50, 50)" />
                                <circle class="progress-pie__inner-disc" fill="grey" opacity="0.2" r="35" cx="50" cy="50" filter="url(#drop-shadow-flat)" />
                                <text class="progress-pie__text" x="50" y="57" text-anchor="middle" font-size="18"><?php echo $per ?><tspan font-size="15" dy="-5">%</tspan></text>
                            </svg>
                        </aside>
                        <canvas id="canvas_fy_1" style="display: none;"></canvas>
                        <div id="png_fy_1"></div>
                        <label id="label_fy" style="margin-left: 10px;font-size: 14px;font-weight: bold; color: #8c8c8c;"><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Purchase', true) . '/' . __d(sprintf($_domain, 'FY_Budget'), 'Sale', true) ?></label>

                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 130px !important"></td>
                <th style="width: 390px !important" colspan="3"><?php echo __('Total', true);?></th>
            </tr>
            <tr>
                <td></td>
                <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Order', true);?></th>
                <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'To bill', true);?></th>
                <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Paid', true);?></th>
            </tr>
            </thead>
            <tbody>
            <tr id="sales" style="width: 480px !important">
                <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Sale', true);?></th>
                <td class="saleOrder"><?php echo number_format($totalSales, 2, ',', ' ');?>&nbsp;<?php echo $viewChar;?></td>
                <td class="saleToBill"><?php echo number_format($totalSaleTobill, 2, ',', ' ');?>&nbsp;<?php echo $viewChar;?></td>
                <td class="saleBilled"><?php echo number_format($totalSaleBill, 2, ',', ' ');?>&nbsp;<?php echo $viewChar;?></td>
            </tr>
            <tr id="purchases" style="width: 480px !important">
                <th style="border: none"><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Purchase', true);?></th>
                <td class="saleOrder"><p class="<?php echo $totalPurchase > $totalSales ? 'task_red_bg' : 'task_blue_bg'  ?>"><?php echo number_format($totalPurchase, 2, ',', ' ');?>&nbsp;<?php echo $viewChar;?></p></td>
                <td class="saleToBill"><?php echo number_format($totalPurchaseTobill, 2, ',', ' ');?>&nbsp;<?php echo $viewChar;?></td>
                <td class="saleBilled"><?php echo number_format($totalPurchaseBill, 2, ',', ' ');?>&nbsp;<?php echo $viewChar;?></td>
            </tr>
            <tr id="sales-purchases" style="width: 480px !important">
                <th style="background: #ccc;font-weight: bold;border: 1px solid #ccc;"><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Sale-Purchase', true);?></th>
                <td class="saleOrder"><?php echo number_format($totalSales - $totalPurchase, 2, ',', ' ');?>&nbsp;<?php echo $viewChar;?></td>
                <td class="saleToBill"><?php echo number_format($totalSaleTobill - $totalPurchaseTobill, 2, ',', ' ');?>&nbsp;<?php echo $viewChar;?></td>
                <td class="saleBilled"><?php echo number_format($totalSaleBill - $totalPurchaseBill, 2, ',', ' ');?>&nbsp;<?php echo $viewChar;?></td>
            </tr>
            <tr id="xxxx" style="width: 480px !important">
                <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Xxxx', true);?></th>
                <td class="saleOrder"></td>
                <td class="saleToBill"></td>
                <td class="saleBilled"></td>
            </tr>
            </tbody>
        </table>
        <div id="table-scroller">
            <table class="table-content absence-fixed">
                <thead>
                <tr>
                    <td colspan="3">
                        <?php
                        $a = !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$year]) ?  $saleValues['order'][$year] : 0;
                        $b = !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$year]) ?  $purchaseValues['order'][$year] : 0;
                        $per = 0;
                        $color_min = '#13FF02';
                        $color_max = '#15830D';
                        if($a != 0){
                            $per = round($b/$a * 100, 2);
                        }
                        if($per > 100){
                            $color_min = '#F98E8E';
                            $color_max = '#FF0606';
                        }
                        ?>
                        <div class="">
                            <aside style="border-left: 1px solid #ccc;">
                                <svg class="progress-pie" id="svg_fy_2" width="35%" height="70%" role="image" style="margin-bottom: -14px">
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
                                        <linearGradient id="pprgyearfy" class="progress-pie__gradient">
                                            <stop offset="0%" stop-color="<?php echo $color_min ?>" />
                                            <stop offset="100%" stop-color="<?php echo $color_max ?>" />
                                        </linearGradient>
                                    </defs>
                                    <circle class="progress-pie__bg" filter="url(#drop-shadow)" r="50" cx="50" cy="50" fill="grey" opacity="0.3" />
                                    <circle stroke="grey" stroke-width="9" fill="none" r="39.5" cx="50" cy="50" opacity="0.3" filter="url(#inset-shadow)" />
                                    <circle class="progress-pie__ring" stroke="url(#pprgyearfy)" stroke-width="9" stroke-dasharray="<?php echo abs($per/100*248.1858154) ?> 248.1858154" fill="none" r="39.5" cx="50" cy="50" transform="rotate(90, 50, 50)" />
                                    <circle class="progress-pie__inner-disc" fill="grey" opacity="0.2" r="35" cx="50" cy="50" filter="url(#drop-shadow-flat)" />
                                    <text class="progress-pie__text" x="50" y="57" text-anchor="middle" font-size="18"><?php echo $per ?><tspan font-size="15" dy="-5">%</tspan></text>
                                </svg>
                            </aside>
                            <canvas id="canvas_fy_2" style="display: none;"></canvas>
                            <div id="png_fy_2"></div>
                        </div>
                    </td>
                    <td colspan="3">
                        <?php
                        $a = !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$nextOneYear]) ?  $saleValues['order'][$nextOneYear] : 0;
                        $b = !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$nextOneYear]) ?  $purchaseValues['order'][$nextOneYear] : 0;
                        $per = 0;
                        $color_min = '#13FF02';
                        $color_max = '#15830D';
                        if($a != 0){
                            $per = round($b/$a * 100, 2);
                        }
                        if($per > 100){
                            $color_min = '#F98E8E';
                            $color_max = '#FF0606';
                        }
                        ?>
                        <div class="">
                            <aside style="border-left: 1px solid #ccc;">
                                <svg class="progress-pie" id="svg_fy_3" width="35%" height="70%" role="image" style="margin-bottom: -14px">
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
                                        <linearGradient id="pprgnextoneyearfy" class="progress-pie__gradient">
                                            <stop offset="0%" stop-color="<?php echo $color_min ?>" />
                                            <stop offset="100%" stop-color="<?php echo $color_max ?>" />
                                        </linearGradient>
                                    </defs>
                                    <circle class="progress-pie__bg" filter="url(#drop-shadow)" r="50" cx="50" cy="50" fill="grey" opacity="0.3" />
                                    <circle stroke="grey" stroke-width="9" fill="none" r="39.5" cx="50" cy="50" opacity="0.3" filter="url(#inset-shadow)" />
                                    <circle class="progress-pie__ring" stroke="url(#pprgnextoneyearfy)" stroke-width="9" stroke-dasharray="<?php echo abs($per/100*248.1858154) ?> 248.1858154" fill="none" r="39.5" cx="50" cy="50" transform="rotate(90, 50, 50)" />
                                    <circle class="progress-pie__inner-disc" fill="grey" opacity="0.2" r="35" cx="50" cy="50" filter="url(#drop-shadow-flat)" />
                                    <text class="progress-pie__text" x="50" y="57" text-anchor="middle" font-size="18"><?php echo $per ?><tspan font-size="15" dy="-5">%</tspan></text>
                                </svg>
                            </aside>
                            <canvas id="canvas_fy_3" style="display: none;"></canvas>
                            <div id="png_fy_3"></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th colspan="3"><?php echo $year;?></th>
                    <th colspan="3"><?php echo $year+1;?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Order', true);?></th>
                    <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'To bill', true);?></th>
                    <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Paid', true);?></th>
                    <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Order', true);?></th>
                    <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'To bill', true);?></th>
                    <th><?php echo __d(sprintf($_domain, 'FY_Budget'), 'Paid', true);?></th>
                </tr>
                <tr id="data-sales">
                    <td class="order"><?php echo !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$year]) ? number_format($saleValues['order'][$year], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                    <td class="toBill"><?php echo !empty($saleValues) && isset($saleValues['toBill']) && !empty($saleValues['toBill']) && !empty($saleValues['toBill'][$year]) ? number_format($saleValues['toBill'][$year], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                    <td class="billed"><?php echo !empty($saleValues) && isset($saleValues['billed']) && !empty($saleValues['billed']) && !empty($saleValues['billed'][$year]) ? number_format($saleValues['billed'][$year], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>

                    <td class="order"><?php echo !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$nextOneYear]) ? number_format($saleValues['order'][$nextOneYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                    <td class="toBill"><?php echo !empty($saleValues) && isset($saleValues['toBill']) && !empty($saleValues['toBill']) && !empty($saleValues['toBill'][$nextOneYear]) ? number_format($saleValues['toBill'][$nextOneYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                    <td class="billed"><?php echo !empty($saleValues) && isset($saleValues['billed']) && !empty($saleValues['billed']) && !empty($saleValues['billed'][$nextOneYear]) ? number_format($saleValues['billed'][$nextOneYear], 2, ',', ' ') : '0,00';?>&nbsp;<?php echo $viewChar;?></td>
                    </tr>
                <tr id="data-purchases">
                    <td class="order">
                        <?php
                            $class="";
                            $a = !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$year]) ?  $saleValues['order'][$year] : 0;
                            $b = !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$year]) ?  $purchaseValues['order'][$year] : 0;
                            if($a >= $b){
                                $class = "task_blue_bg";
                            } else {
                                $class = "task_red_bg";
                            }
                            if( !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$year]) ){

                            } else {
                                $class="";
                            }
                        ?>
                        <p class="<?php echo $class; ?>">
                        <?php echo !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$year]) ? number_format($purchaseValues['order'][$year], 2, ',', ' ') . ' ' . $viewChar : '';?>
                        </p>
                    </td>
                    <td class="toBill"><?php echo !empty($purchaseValues) && isset($purchaseValues['toBill']) && !empty($purchaseValues['toBill']) && !empty($purchaseValues['toBill'][$year]) ? number_format($purchaseValues['toBill'][$year], 2, ',', ' ') . ' ' . $viewChar : '';?></td>
                    <td class="billed"><?php echo !empty($purchaseValues) && isset($purchaseValues['billed']) && !empty($purchaseValues['billed']) && !empty($purchaseValues['billed'][$year]) ? number_format($purchaseValues['billed'][$year], 2, ',', ' ') . ' ' . $viewChar : '';?></td>

                    <td class="order">
                        <?php
                            $class="";
                            $a = !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$nextOneYear]) ?  $saleValues['order'][$nextOneYear] : 0;
                            $b = !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$nextOneYear]) ?  $purchaseValues['order'][$nextOneYear] : 0;
                            if($a >= $b){
                                $class = "task_blue_bg";
                            } else {
                                $class = "task_red_bg";
                            }
                            if( !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$nextOneYear]) ){

                            } else {
                                $class="";
                            }
                        ?>
                        <p class="<?php echo $class; ?>">
                        <?php echo !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$nextOneYear]) ? number_format($purchaseValues['order'][$nextOneYear], 2, ',', ' ') . ' ' . $viewChar : '';?>
                        </p>
                    </td>
                    <td class="toBill"><?php echo !empty($purchaseValues) && isset($purchaseValues['toBill']) && !empty($purchaseValues['toBill']) && !empty($purchaseValues['toBill'][$nextOneYear]) ? number_format($purchaseValues['toBill'][$nextOneYear], 2, ',', ' ') . ' ' . $viewChar : '';?></td>
                    <td class="billed"><?php echo !empty($purchaseValues) && isset($purchaseValues['billed']) && !empty($purchaseValues['billed']) && !empty($purchaseValues['billed'][$nextOneYear]) ? number_format($purchaseValues['billed'][$nextOneYear], 2, ',', ' ') . ' ' . $viewChar : '';?></td>
                </tr>
                <tr id="data-sales-purchases">
                    <td class="order">
                        <?php
                        $a = !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$year]) ?  $saleValues['order'][$year] : 0;
                        $b = !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$year]) ?  $purchaseValues['order'][$year] : 0;
                        $budgetYear = $a - $b;
                        echo number_format(($a - $b), 2, ',', ' ') . ' ' . $viewChar;
                        ?>
                    </td>
                    <td class="toBill">
                        <?php
                        $a = !empty($saleValues) && isset($saleValues['toBill']) && !empty($saleValues['toBill']) && !empty($saleValues['toBill'][$year]) ?  $saleValues['toBill'][$year] : 0;
                        $b = !empty($purchaseValues) && isset($purchaseValues['toBill']) && !empty($purchaseValues['toBill']) && !empty($purchaseValues['toBill'][$year]) ?  $purchaseValues['toBill'][$year] : 0;
                        echo number_format(($a - $b), 2, ',', ' ') . ' ' . $viewChar;
                        ?>
                    </td>
                    <td class="billed">
                        <?php
                        $a = !empty($saleValues) && isset($saleValues['billed']) && !empty($saleValues['billed']) && !empty($saleValues['billed'][$year]) ?  $saleValues['billed'][$year] : 0;
                        $b = !empty($purchaseValues) && isset($purchaseValues['billed']) && !empty($purchaseValues['billed']) && !empty($purchaseValues['billed'][$year]) ?  $purchaseValues['billed'][$year] : 0;
                        echo number_format(($a - $b), 2, ',', ' ') . ' ' . $viewChar;
                        ?>
                    </td>
                    <td class="order">
                        <?php
                        $a = !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$nextOneYear]) ?  $saleValues['order'][$nextOneYear] : 0;
                        $b = !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$nextOneYear]) ?  $purchaseValues['order'][$nextOneYear] : 0;
                        $budgetNextOneYear = $a - $b;
                        echo number_format(($a - $b), 2, ',', ' ') . ' ' . $viewChar;
                        ?>
                    </td>
                    <td class="toBill">
                        <?php
                        $a = !empty($saleValues) && isset($saleValues['toBill']) && !empty($saleValues['toBill']) && !empty($saleValues['toBill'][$nextOneYear]) ?  $saleValues['toBill'][$nextOneYear] : 0;
                        $b = !empty($purchaseValues) && isset($purchaseValues['toBill']) && !empty($purchaseValues['toBill']) && !empty($purchaseValues['toBill'][$nextOneYear]) ?  $purchaseValues['toBill'][$nextOneYear] : 0;
                        echo number_format(($a - $b), 2, ',', ' ') . ' ' . $viewChar;
                        ?>
                    </td>
                    <td class="billed">
                        <?php
                        $a = !empty($saleValues) && isset($saleValues['billed']) && !empty($saleValues['billed']) && !empty($saleValues['billed'][$nextOneYear]) ?  $saleValues['billed'][$nextOneYear] : 0;
                        $b = !empty($purchaseValues) && isset($purchaseValues['billed']) && !empty($purchaseValues['billed']) && !empty($purchaseValues['billed'][$nextOneYear]) ?  $purchaseValues['billed'][$nextOneYear] : 0;
                        echo number_format(($a - $b), 2, ',', ' ') . ' ' . $viewChar;
                        ?>
                    </td>
                </tr>
                <tr id="data-xxxx">
                    <?php
                        $totalXxxx += $budgetYear;
                        if($totalXxxx >= 0){
                            $class = "task_blue_bg";
                        } else {
                            $class = "task_red_bg";
                        }
                    ?>
                    <td class="order"><p class="<?php echo $class; ?>"><?php echo number_format($totalXxxx, 2, ',', ' '); ?>&nbsp;<?php echo $viewChar;?></p></td>
                    <td class="toBill"></td>
                    <td class="billed"></td>

                    <?php
                        $totalXxxx += $budgetNextOneYear;
                        if($totalXxxx >= 0){
                            $class = "task_blue_bg";
                        } else {
                            $class = "task_red_bg";
                        }
                    ?>
                    <td class="order"><p class="<?php echo $class; ?>"><?php echo number_format($totalXxxx, 2, ',', ' '); ?>&nbsp;<?php echo $viewChar;?></p></td>
                    <td class="toBill"></td>
                    <td class="billed"></td>
                </tr>
                </tbody>
            </table>
            <?php
            $a = !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$nextOneYear+1]) ?  $saleValues['order'][$nextOneYear+1] : 0;
            $b = !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$nextOneYear+1]) ?  $purchaseValues['order'][$nextOneYear+1] : 0;
            $totalXxxx += $a - $b;
            $a = !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$year-1]) ?  $saleValues['order'][$year-1] : 0;
            $b = !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$year-1]) ?  $purchaseValues['order'][$year-1] : 0;
            $totalXxxx += $a - $b;
            $a = !empty($saleValues) && isset($saleValues['order']) && !empty($saleValues['order']) && !empty($saleValues['order'][$year-2]) ?  $saleValues['order'][$year-2] : 0;
            $b = !empty($purchaseValues) && isset($purchaseValues['order']) && !empty($purchaseValues['order']) && !empty($purchaseValues['order'][$year-2]) ?  $purchaseValues['order'][$year-2] : 0;
            $totalXxxx += $a - $b;
            ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<!-- end -->
<!-- weather -->
<?php $showKpiBudget = false; ?>
<div id="div_weather" style="display: none">
<?php if($yourFormFilter['weather']) : ?>
<div data-widget="weather" class="wd-input wd-area wd-none">
    <label><?php echo !empty($maps['weather']['label']) ? $maps['weather']['label'] : ''; ?></label>
    <?php //echo !empty($maps['weather']['html']) ? $maps['weather']['html'] : '';
$commentKpiTemp = '<div class="group-content" style="width: 100%">';
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
$commentKpiTemp .= '<textarea class="log-content" rowspan="2" onfocus="autosize(this)" onblur="autosize.destroy(this)">'. 	$commentKpi['description'].'</textarea>';
$commentKpiTemp .= '</div>';
$commentKpiTemp .= '</li>';
                }
$commentKpiTemp .= '</ul>';
$commentKpiTemp .= '</div>';
$commentKpiTemp .= '</div>';

$todoKpiTemp = '<div class="group-content" style="width: 100%">';
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
$todoKpiTemp .= '<textarea class="log-content" rowspan="2" onfocus="autosize(this)" onblur="autosize.destroy(this)">'. $todoKpi['description'].'</textarea>';
$todoKpiTemp .= '</div>';
$todoKpiTemp .= '</li>';
                }
$todoKpiTemp .= '</ul>';
$todoKpiTemp .= '</div>';
$todoKpiTemp .= '</div>';

$doneKpiTemp  = '<div class="group-content" style="width: 100%">';
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
$doneKpiTemp .= '<textarea class="log-content" rowspan="2" onfocus="autosize(this)" onblur="autosize.destroy(this)">'. $doneKpi['description'].'</textarea>';
$doneKpiTemp .= '</div>';
$doneKpiTemp .= '</li>';
                }
$doneKpiTemp .= '</ul>';
$doneKpiTemp .= '</div>';
$doneKpiTemp .= '</div>';

$acceptanceKpiTemp = '<div style="width: 100%; overflow-x: auto;">';
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
$acceptanceKpiTemp .= '<li  style="padding-right: 15px" ><input '. (@$acc["ProjectAcceptance"]["weather"] == "cloud" ? "checked" : "") .' style="max-width: 25px; width: 25px;margin-top: 10px;" type="radio" class="weather" name="acceptance-'.$accId.'"> <img src="'. $html->url('/img/cloud.png') .'"  /></li>';
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

$budgetKpiTemp = '<div style="width: 100%; overflow-x: auto;">';
$budgetKpiTemp .= '<div class="group-content" style="width: 105%">';
$budgetKpiTemp .= '<h3><span>'. __d(sprintf($_domain, 'KPI'), 'Budget', true) .'</span></h3>';
$budgetKpiTemp .= '<div class="wd-input separator">';
        $md = !empty($employee_info['Company']['unit']) ? $employee_info['Company']['unit'] : 'M.D';
$budgetKpiTemp .= '<div style="float: left; line-height: -40px; width:30%">';
$budgetKpiTemp .= '<div class="wd-input wd-weather-list-dd">';
$budgetKpiTemp .= '<ul style="float: left; display: inline; width: 500px;">';
$budgetKpiTemp .= '<li><input '. ($budgetKpi["ProjectAmr"]["weather"] == "sun" ? "checked" : "") .' style="max-width: 25px; width: 25px;margin-top: 10px;" type="radio" class="weather" name="budget"> <img src="'. $html->url('/img/sun.png') .'"  /></li>';
$budgetKpiTemp .= '<li  style="padding-right: 15px" ><input '. ($budgetKpi["ProjectAmr"]["weather"] == "cloud" ? "checked" : "") .' style="max-width: 25px; width: 25px;margin-top: 10px;" type="radio" class="weather" name="budget"> <img src="'. $html->url('/img/cloud.png') .'"  /></li>';
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

$progressKpiTemp = '<div style="width: 100%">';
$progressKpiTemp .= '<div id="svg_kpi" class="group-content" style="clear:both;">';
$progressKpiTemp .= '<h3><span>'. __d(sprintf($_domain, 'KPI'), 'Progress', true) .'</span></h3>';

$progressKpiTemp .= '<div id = "svg_kpi_1" class="budget-progress" style="float: left">
         <div class="progress-label">
            <div class=""><span class="progress-label-color" style="background-color: #538FFA"></span><span>'. __('Consumed', true) .'</span></div>
            <div class=""><span class="progress-label-color" style="background-color: #E44353"></span><span>'. __('Planed', true) .'</span></div>
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
                <div class ="progress-value progress-validated"><p>'. __('Consumed', true) .'</p><span>'. round($validated, 2) .'</span></div>
                <div class ="progress-value progress-engaged"><p>'. __('Planed', true) .'</p><span>'. round($engaged, 2) .'</span></div>
            </div>
        </div>
    </aside>';

$progressKpiTemp .= '<div class="wd-table svg_budget" id="budget_db" data-type="budget" style="width:65%; float: left; margin-top: 2px; margin-left: 100px">';

$progressKpiTemp .= '</div>';
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
            <div class=""><span class="progress-label-color" style="background-color: #538FFA"></span><span>'.  __('Consumed', true) .'</span></div>
            <div class=""><span class="progress-label-color" style="background-color: #E44353"></span><span><span>'. __('Planed', true) .'</span></div>
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
if(!empty($showMenu['gantt']) && $showMenu['gantt']){
$ganttTemp .= '<div data-widget="gantt" class="wd-input wd-area wd-none" style = "margin-top: 15px;">';
$ganttTemp .= '<div id="AjaxGanttChartDIV">';
$ganttTemp .= '</div>';
$ganttTemp .= '</div>';
$ganttTemp .= '';
}
?>
<!-- gantt planing -->
<?php
$gantts = $stones = array();
$ganttStart = $ganttEnd = 0;

if(!empty($showMenu['milestone']) && $showMenu['milestone'] && $yourFormFilter['milestone'] == 1){
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
        'start' => ($_phase['start'] == 0) ? '00-00-0000' : date('d-m-Y', $_phase['start']),
        'end' => ($_phase['end'] == 0) ? '00-00-0000' : date('d-m-Y', $_phase['end']),
        'rstart' => ($_phase['rstart'] == 0) ? '00-00-0000' : date('d-m-Y', $_phase['rstart']),
        'rend' => ($_phase['rend'] == 0) ? '00-00-0000' : date('d-m-Y', $_phase['rend']),
        'completed' => $progresMilestone
    );
}
?>
<div id="div_milestone" style="display: none">
<?php if($showMenu['milestone'] && $yourFormFilter['milestone'] == 1) :  
?>
<div data-widget="milestone" class="wd-input wd-area wd-none" style = "margin-top: 15px;">
    <div class="group-content" style="width: 100%">
        <!-- <h3 class="half-padding"><span><?php echo __d(sprintf($_domain, 'KPI'), 'Milestone', true);?> </span></h3> -->
        <h3 class="half-padding"><span><?php echo !empty( $page_title['project_milestones']['index'] ) ? $page_title['project_milestones']['index']  : __d(sprintf($_domain, 'KPI'), 'Milestone', true) ;?> </span></h3>
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
<div data-widget="risk" class="wd-input wd-area wd-none" style = "margin-top: 15px;">
    <div class="">
        <div class="group-content" style="width: 100%; margin-bottom: 0;">
            <h3 class="half-padding"><span><?php echo !empty($page_title['project_risks']['index']) ? $page_title['project_risks']['index'] : __d(sprintf($_domain, 'KPI'), 'Risk', true);?> </span></h3>
        </div>
        <div id="project_risks" style="width: 100%">

        </div>
        <table id = "absence" style="width: 100%">
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
<div data-widget="issue" class="wd-input wd-area wd-none" style = "margin-top: 15px;">
    <div id="project_issue" style="width: 100%">
    </div>
    <div>
        <table id = "absence" style="width: 100%">
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
if(!empty($showMenu['local_view']) && $showMenu['local_view'] && $yourFormFilter['location'] == 1) :
$key_statics = 'AIzaSyA4rAT0fdTZLNkJ5o0uaAwZ89vVPQpr_Kc';
$_path_img = FILES . 'staticmap.png';
$latlong = $projectName['Project']['latlng'];
$latlong = json_decode($latlong, true);
$_url = 'https://maps.googleapis.com/maps/api/staticmap?center='.$latlong['lat'].','.$latlong['lng'].'&zoom=11&size=1330x500&markers=color:red%7Clabel:C%7C&key=' . $key_statics;
$content = file_get_contents($_url);
file_put_contents($_path_img, $content);
$linkImg = $this->Html->url(array('action' => 'attachment_static', '?' => array('sid' => $api_key)), true);

$locationTemp .= '<div id="div_location" data-widget="location" class="wd-input wd-area wd-none" style = "margin-top: 15px;">';
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
$locationTemp .= '<input type="text" id="coord-input" size="40" style="display: none">';
$locationTemp .= '<iframe src="' . $IFRAME . '" style="width: calc( 100% - 2px);height: 400px; border: 1px solid #D8D8D8;" id="local-frame"></iframe>';
$locationTemp .= '<iframe src="about:blank" style="width: calc( 100% - 2px);height: 500px; border: 1px solid #D8D8D8; display: none" id="map-frame" allowfullscreen></iframe>';
$locationTemp .= '</div>';
if(!empty($latlong)){
    $locationTemp .= '<img id="img_location" style="display: none; width: 1330px; height: 500px; margin-top: 0px" src="' . $linkImg . '">';
}
$locationTemp .= '</div>';
endif;
$locationTemp .= '';
?>
<!-- dependency -->
<?php
$diagramTemp = '';
if(!empty($showMenu['dependency']) && $showMenu["dependency"]){
$diagramTemp .= '<div id="div_diagram" data-widget="dependency" class="wd-input wd-area wd-none" style = "margin-top: 15px;">';
$diagramTemp .= '<div id="diagram" style="95%">';
$diagramTemp .= '<div class="vis-network" tabindex="900" style="position: relative; overflow: hidden; touch-action: none; -webkit-user-select: none; -webkit-user-drag: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); width: 100%;">';
$diagramTemp .= '<canvas style="position: relative; touch-action: none; -webkit-user-select: none; -webkit-user-drag: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); width: 100%; height: 100%;"></canvas>';
$diagramTemp .= '</div>';
$diagramTemp .= '</div>';
$diagramTemp .= '</div>';
$diagramTemp .= '';
}
if(!empty($showMenu['global_view']) && $showMenu['global_view'] && $yourFormFilter['global_view'] == 1) :
$_style = 'width: auto; height: auto;';
$globalTemp = '<div data-widget="global_view" class="wd-input wd-area wd-none" style = "margin-top: 15px;">';
$globalTemp .= '<div id="global_view" style="width: 100%; position: relative">';

        if ($projectGlobalView) {
                if(!$projectGlobalView['ProjectGlobalView']['is_file']){
                    $is_http = $projectGlobalView['ProjectGlobalView']['is_https'] ? 'https://' : 'http://';
                    $IFRAME = $is_http.$projectGlobalView['ProjectGlobalView']['attachment'] ;
                }
				if(isset($statusFilters['global_view']) && $statusFilters['global_view'][0] == 1 && !empty($info_image)){
					$width = $info_image[0] / 2;
					$_style = 'width: '. $width .'px; height: auto;';
				}
        }
        $isDoc = false;
        $link = $this->Html->url(array('controller' => 'project_global_views', 'action' => 'attachment', $projectName['Project']['id'], '?' => array('sid' => $api_key)), true);
        // if ($projectGlobalView && empty($noFileExists)) {
         if ($projectGlobalView) {
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
        } else{
$globalTemp .= '<iframe src="'. $link .'" style="width: 45%;height: 402px;margin-top: 0; position: absolute;"></iframe>';
        }
$globalTemp .= '</div>';
$globalTemp .= '</div>';
endif;
?>
<!-- budget internal -->

<?php if($showMenu['internal_cost'] && $yourFormFilter['buget_internal'] == 1) : ?>
<div id ="div_internal_cost" style="display: none">
<div data-widget="buget_internal" class="wd-input wd-area wd-none" style = "margin-top: 15px;">
    <div>
        <div class="group-content" style="width: 100%; margin-bottom: 0;">
            <!-- <h3 class="half-padding"><span><?php echo __d(sprintf($_domain, 'KPI'), 'Internal Cost', true);?> </span></h3> -->
            <h3 class="half-padding"><span><?php echo !empty( $page_title['project_budget_internals']['index'] ) ? $page_title['project_budget_internals']['index']  : __d(sprintf($_domain, 'KPI'), 'Internal Cost', true) ;?> </span></h3>
        </div>
        <table id = "absence" style="width: 100%">
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
                    $httr .= '<td style="float: right;">'. number_format($averages*$budget_md, 2, ',', ' ') . ' '.$bg_currency .'</td>';
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
                    $httr .= '<td style="text-align: right;">' . number_format($averages, 2, ',', ' ') . ' '.$bg_currency .'</td>';
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
                <tr style="background-color: #E8F0FA; font-weight: bold;">
                    <td><?php echo __('Total', true) ?></td>
                    <td></td>
                    <td style="text-align: right;"><?php echo number_format($total_budget_euro, 2, ',', ' ') . ' '.$bg_currency ?></td>
                    <td style="text-align: right;"><?php echo number_format($forecast_erro, 2, ',', ' ') . ' '.$bg_currency ?></td>
                    <td style="text-align: right;"><?php echo number_format($vareurro, 2, ',', ' ') . ' %' ?></td>
                    <td style="text-align: right;"><?php echo number_format($engagedErro, 2, ',', ' ') . ' '.$bg_currency ?></td>
                    <td style="text-align: right;"><?php echo number_format($remain_erro, 2, ',', ' ') . ' '.$bg_currency ?></td>
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
</div>
<?php endif; ?>

<!-- budget external -->

<?php if($showMenu['external_cost'] && $yourFormFilter['budget_externals'] == 1) : ?>
<div id="div_external_cost" style="display: none">
<div data-widget="budget_externals" class="wd-input wd-area wd-none" style = "margin-top: 15px;">
    <div>
        <div class="group-content" style="width: 100%; margin-bottom: 0;">
            <!-- <h3 class="half-padding"><span><?php echo __d(sprintf($_domain, 'KPI'), 'External Cost', true);?> </span></h3> -->
			<h3 class="half-padding"><span><?php echo !empty( $page_title['project_budget_externals']['index'] ) ? $page_title['project_budget_externals']['index']  : __d(sprintf($_domain, 'KPI'), 'External Cost', true) ;?> </span></h3>
        </div>
        <table id = "absence" style="width: 100%">
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
                        $httr .= '<td style="text-align: right;">'. (!empty($dx['budget_erro']) ? number_format($dx['budget_erro'], 2, ',', ' ') . ' '.$bg_currency : '0.00 €').'</td>';
                        $httr .= '<td style="text-align: right;">'. (!empty($forecast_erro) ? number_format($forecast_erro, 2, ',', ' ') . ' '.$bg_currency : '0.00 €').'</td>';
                        $httr .= '<td style="text-align: right;">'. (!empty($var_erro) ? number_format($var_erro, 2, ',', ' ') . ' %' : '0.00 %') .'</td>';
                        $httr .= '<td style="text-align: right;">'.(!empty($dx['ordered_erro']) ? number_format($dx['ordered_erro'], 2, ',', ' ') . ' '.$bg_currency : '0.00 €') .'</td>';
                        $httr .= '<td style="text-align: right;">'. (!empty($dx['remain_erro']) ? number_format($dx['remain_erro'], 2, ',', ' ') . ' '.$bg_currency : '0.00 €') .'</td>';
                        $httr .= '<td style="text-align: right;">'. (!empty($dx['man_day']) ? number_format($dx['man_day'], 2, ',', ' ') . ' ' . __('M.D', true) : '0.00 ' . __('M.D', true)) .'</td>';
                        $httr .= '<td style="text-align: right;">'. (!empty($totalconsumed) ? number_format($totalconsumed,2,',',' ') . ' ' . __('M.D', true) : '0.00 ' . __('M.D', true)) .'</td>';
                        $httr .= '<td style="text-align: right;">'. (!empty($dx['progress_md']) ? number_format($dx['progress_md'], 2, ',', ' ') . ' %' : '0.00 %') .'</td>';
                        $httr .= '<td style="text-align: right;">'. (!empty($dx['progress_erro']) ? number_format($dx['progress_erro'], 2, ',', ' ') . ' '.$bg_currency : '0.00 €') .'</td>';
                        $httr .= '<td style="text-align: right;">'. $dx['file_attachement'] .'</td>';
                    $httr .= '</tr>';
                }
                $vareurro = $total_budget_euro > 0 ? round(($total_forecast_euro/$total_budget_euro -1)*100, 2) : 0;
                $varmd = $total_ordered_erro > 0 ?  round(($total_progress_euro/$total_ordered_erro)*100, 2) : 0;
                ?>
                <tr style="background-color: #E8F0FA; font-weight: bold;">
                    <td><?php echo __('Total', true) ?></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: right;"><?php echo number_format($total_budget_euro, 2, ',', ' ') . ' '.$bg_currency ?></td>
                    <td style="text-align: right;"><?php echo number_format($total_forecast_euro, 2, ',', ' ') . ' '.$bg_currency ?></td>
                    <td style="text-align: right;"><?php echo number_format($vareurro, 2, ',', ' ') . ' %' ?></td>
                    <td style="text-align: right;"><?php echo number_format($total_ordered_erro, 2, ',', ' ') . ' '.$bg_currency ?></td>
                    <td style="text-align: right;"><?php echo number_format($total_remain_erro, 2, ',', ' ') . ' '.$bg_currency ?></td>
                    <td style="text-align: right;"><?php echo number_format($total_man_day, 2, ',', ' ') . ' ' . __('M.D', true)?></td>
                    <td style="text-align: right;"><?php echo number_format($total_special_consume, 2, ',', ' ') . ' ' . __('M.D', true) ?></td>
                    <td style="text-align: right;"><?php echo number_format($varmd, 2, ',', ' ') . ' %' ?></td>
                    <td style="text-align: right;"><?php echo number_format($total_progress_euro, 2, ',', ' ') . ' '.$bg_currency ?></td>
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
<!-- finance+ -->

<?php if( !empty($showMenu['finance_plus']) && $yourFormFilter['finance_plus'] == 1) : ?>
	<div id ="div_finance_plus" style="display: none">
    <div data-widget="finance_plus" id = "budget-chard" style="margin-top: 15px; width: 100%;">
        <div id="inve-chard" style="display: inline-block; width: calc(50% - 2px)">
            <h3 class="wd-t1" style="color: #ffb250; margin-bottom: 10px;"><?php echo __d(sprintf($_domain, 'Finance'), 'Budget Investment', true); ?></h3>
            <div class="chard-content">

                <div class="budget-chard" style="display: none">
                    <p><?php echo __('Budget', true) .': '. number_format((!empty($totals['inv']['budget']) ? $totals['inv']['budget'] : 0), 2, '.', ' ') . ' '.$bg_currency ?></p>
                    <p><?php echo __('Engaged', true) .': '. number_format((!empty($totals['inv']['avancement']) ? $totals['inv']['avancement'] : 0), 2, '.', ' ')  . ' '.$bg_currency ?></p>
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
                <div class="percent-chard" style="display: none">
                    <div style="width: 50%">
                        <?php if($totals['inv']['budget'] < 0){ ?>
                        <div style="height: 10px; width: <?php echo $width_bud ?>; background-color: #ccc; float: right"></div>
                        <?php } else { ?>
                        <div style="height: 10px; width: 0px; background-color: #ccc; float: right"></div>
                        <?php }
                        if($totals['inv']['avancement'] < 0){
                        ?>
                        <div style="margin-top: 10px; height: 10px; float: right; width: <?php echo $width_avan ?>; background-color: <?php echo $bg_color ?>"></div>
                        <?php } ?>
                    </div>
                    <div style="width: 50%; margin-left: 50%;">
                        <?php if($totals['inv']['budget'] >= 0){ ?>
                        <div style="height: 10px; width: <?php echo $width_bud ?>; background-color: #ccc"></div>
                        <?php } else { ?>
                        <div style="height: 10px; width: 0px; background-color: #ccc"></div>
                        <?php }
                        if($totals['inv']['avancement'] >= 0){
                        ?>
                        <div style="margin-top: 10px; height: 10px; width: <?php echo $width_avan ?>; background-color: <?php echo $bg_color ?>"></div>
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
               <aside class="budget-progress-circle" style="overflow:visible; margin-top: 32px">
                    <div class="progress-circle progress-circle-yellow">
                        <div class="progress-circle-inner">
                            <i class="icon-question" aria-hidden="true"></i>
                            <canvas data-value = "<?php echo $per; ?>" id="myCanvas-2" width="165" height="160" style="" class="canvas-circle"></canvas>
                            <div class ="progress-value progress-validated"><p><?php echo  __('Budget', true);?></p><span><?php echo number_format((!empty($totals['inv']['budget']) ? $totals['inv']['budget'] : 0), 2, '.', ' '); ?> <?php echo ' '.$bg_currency; ?></span></div>
                            <div class ="progress-value progress-engaged"><p><?php echo  __('Engaged', true);?></p><span><?php echo number_format((!empty($totals['inv']['avancement']) ? $totals['inv']['avancement'] : 0), 2, '.', ' '); ?> <?php echo ' '.$bg_currency; ?></span></div>
                          <!--   <div class="progress-circle-text"><?php echo __d(sprintf($_domain, 'KPI'), 'synthèse détaillée', true);?></div> -->
                        </div>
                    </div>
                </aside>
            </div>
            <canvas id="canvas" style="display: none; width: 200px; height: 200px"></canvas>

        </div>
        <div id="fon-chard" style="display: inline-block; width: calc(50% - 2px)">
            <h3 class="wd-t1" style="color: #ffb250; margin-bottom: 10px;"><?php echo __d(sprintf($_domain, 'Finance'), 'Budget Operation', true); ?></h3>
            <div class="chard-content">
                <div class="budget-chard" style="display: none">
                    <p><?php echo __('Budget', true) .': '. number_format((!empty($totals['fon']['budget']) ? $totals['fon']['budget'] : 0), 2, '.', ' ') . ' '.$bg_currency ?></p>
                    <p><?php echo __('Engaged', true) .': '. number_format((!empty($totals['fon']['avancement']) ? $totals['fon']['avancement'] : 0), 2, '.', ' ') . ' '.$bg_currency ?></p>
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
                        <div style="margin-top: 10px; height: 10px; float: right; width: <?php echo $width_avan ?>; background-color: <?php echo $bg_color ?>"></div>
                        <?php } ?>
                    </div>
                    <div style="width: 50%; margin-left: 185px;">
                        <?php if($totals['fon']['budget'] >= 0){ ?>
                        <div style="height: 10px; width: <?php echo $width_bud ?>; background-color: #ccc"></div>
                        <?php } else { ?>
                        <div style="height: 10px; width: 0px; background-color: #ccc"></div>
                        <?php }
                        if($totals['fon']['avancement'] >= 0){
                        ?>
                        <div style="margin-top: 10px; height: 10px; width: <?php echo $width_avan ?>; background-color: <?php echo $bg_color ?>"></div>
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
                            <div class ="progress-value progress-validated"><p><?php echo  __('Budget', true);?></p><span><?php echo number_format((!empty($totals['fon']['budget']) ? $totals['fon']['budget'] : 0), 2, '.', ' '); ?> <?php echo ' '.$bg_currency; ?></span></div>
                            <div class ="progress-value progress-engaged"><p><?php echo  __('Engaged', true);?></p><span><?php echo number_format((!empty($totals['fon']['avancement']) ? $totals['fon']['avancement'] : 0), 2, '.', ' '); ?> <?php echo ' '.$bg_currency; ?></span></div>
                          <!--   <div class="progress-circle-text"><?php echo __d(sprintf($_domain, 'KPI'), 'synthèse détaillée', true);?></div> -->
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
	<?php
$check = !empty($statusFilters['finance_plus']) ? $statusFilters['finance_plus'][0] : 0;// tuong tu voi finance++.
$displayNone = !$showMenu['finance_plus'] || ($check == 1) ? 'display: none' : '';

?>
	<div class="wd-input wd-area wd-none" <?php if(empty($dataFinan['inv_year'])){ echo 'style="display: none"';} ?>  style = "margin-top: 15px; <?php echo $displayNone ?>">
		<div style="width: 100%; overflow-x: auto;">
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
						$httr .= '<td style="text-align: right;">'.number_format($value['total']['budget'],2,',',' ') . ' '.$bg_currency . '</td>';
						$_budet_total += $value['total']['budget'];
						$httr .= '<td style="text-align: right;">'.number_format($value['total']['avancement'],2,',',' ') . ' '.$bg_currency . '</td>';
						$_avance_total += $value['total']['avancement'];
						$per = $value['total']['budget'] > 0 ? number_format(round(($value['total']['avancement']/$value['total']['budget'])*100),2,',', ' ') . ' %' : '';
						$httr .= '<td style="text-align: right;">'.$per.'</td>';
						//end
						unset($value['total']);
						ksort($value);
						foreach ($value as $k => $val) {
							$httr .= '<td style="text-align: right;">'. (!empty($val['budget']) ? number_format($val['budget'],2,',',' ') : 0.00) . ' '.$bg_currency . '</td>';
							$_budget[$k]['budget'] += $val['budget'];
							$httr .= '<td style="text-align: right;">'.(!empty($val['avancement']) ? number_format($val['avancement'],2,',',' ') : 0.00) . ' '.$bg_currency . '</td>';
							$_budget[$k]['avancement'] += (!empty($val['avancement']) ? $val['avancement'] : 0);
							$per = (!empty($val['avancement']) && !empty($val['budget']) && $val['budget'] > 0) ? number_format(round(($val['avancement']/$val['budget'])*100),2,',', ' ') . ' %' : '';
							$httr .= '<td style="text-align: right;">'.$per.'</td>';
						}
						$httr .= '</tr>';
					}
					ksort($_budget);
					?>
					<tr style="background-color: #E8F0FA; font-weight: bold;">
						<td></td>
						<td style="text-align: right;"><?php  echo number_format($_budet_total,2,',',' ') . ' '.$bg_currency ?></td>
						<td style="text-align: right;"><?php  echo number_format($_avance_total,2,',',' ') . ' '.$bg_currency ?></td>
						<td style="text-align: right;"><?php  echo $_budet_total > 0 ? number_format(round(($_avance_total/$_budet_total)*100),2,',', ' ') . ' %' : '' ?></td>
						<?php foreach ($_budget as $value) { ?>
							<td style="text-align: right;"><?php  echo number_format($value['budget'],2,',',' ') . ' '.$bg_currency ?></td>
							<td style="text-align: right;"><?php  echo number_format($value['avancement'],2,',',' ') . ' '.$bg_currency ?></td>
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
		<div style="width: 100%; overflow-x: auto;">
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
						$httr .= '<td style="text-align: right;">'.number_format($value['total']['budget'],2,',',' ') . ' '.$bg_currency . '</td>';
						$_budet_total += $value['total']['budget'];
						$httr .= '<td style="text-align: right;">'.number_format($value['total']['avancement'],2,',',' ') . ' '.$bg_currency . '</td>';
						$_avance_total += $value['total']['avancement'];
						$per = $value['total']['budget'] > 0 ? number_format(round(($value['total']['avancement']/$value['total']['budget'])*100),2,',', ' ') . ' %' : '';
						$httr .= '<td style="text-align: right;">'.$per.'</td>';
						//end
						unset($value['total']);
						ksort($value);
						foreach ($value as $k => $val) {
							$httr .= '<td style="text-align: right;">'. (!empty($val['budget']) ? number_format($val['budget'],2,',',' ') : 0.00) . ' '.$bg_currency . '</td>';
							$_budget[$k]['budget'] += !empty($val['budget']) ? $val['budget'] : 0;
							$httr .= '<td style="text-align: right;">'.(!empty($val['avancement']) ? number_format($val['avancement'],2,',',' ') : 0.00 ). ' '.$bg_currency . '</td>';
							$_budget[$k]['avancement'] += !empty($val['avancement']) ? $val['avancement'] : 0;
							$per = !empty($val['avancement']) && !empty($val['budget']) && $val['budget'] > 0 ? number_format(round(($val['avancement']/$val['budget'])*100),2,',', ' ') . ' %' : '';
							$httr .= '<td style="text-align: right;">'.$per.'</td>';
						}
						$httr .= '</tr>';
					}
					ksort($_budget);
					?>
					<tr style="background-color: #E8F0FA; font-weight: bold;">
						<td></td>
						<td style="text-align: right;"><?php  echo number_format($_budet_total,2,',',' ') . ' '.$bg_currency ?></td>
						<td style="text-align: right;"><?php  echo number_format($_avance_total,2,',',' ') . ' '.$bg_currency ?></td>
						<td style="text-align: right;"><?php  echo $_budet_total > 0 ? number_format(round(($_avance_total/$_budet_total)*100),2,',', ' ') . ' %' : '' ?></td>
						<?php foreach ($_budget as $value) { ?>
							<td style="text-align: right;"><?php  echo number_format($value['budget'],2,',',' ') . ' '.$bg_currency ?></td>
							<td style="text-align: right;"><?php  echo number_format($value['avancement'],2,',',' ') . ' '.$bg_currency ?></td>
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
<?php
 if($showMenu['task'] && $yourFormFilter['project_task'] == 1) :
    $isManual = isset($companyConfigs['manual_consumed']) ? $companyConfigs['manual_consumed'] : 0;
    $head = array();
    foreach($orders as $key){
        list($word, $show) = explode('|', $key);
        if( $word == 'Order' || (!$isManual && $word == 'ManualConsumed') || !intval($show) )continue;
        if( in_array($word, array('Initialstartdate', 'Initialworkload', 'Initialenddate')) && $projectName['Project']['off_freeze'] == 0 )continue;
        $head[] = $i18n[$word];
    }
?>
<div data-widget="project_task" class="wd-input wd-area wd-none" style = "margin-top: 15px;">
    <div id="project_task" style="width: 100%; overflow: auto;">
        <table id = "absence" style="width: auto; min-width: 100%;">
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
                                    $data = !empty($unit_price) ? number_format($unit_price,2,',',' ') . ' '.$bg_currency : '';
                                    echo '<td style="text-align: right; width: 30px;white-space: nowrap;">' . $data . '</td>';
                            break;
                            case 'Consumed€':
                                    $data = !empty($consumed_euro) ? number_format($consumed_euro,2,',',' ') . ' '.$bg_currency : '';
                                    echo '<td style="text-align: right; width: 30px;white-space: nowrap;">' . $data . '</td>';
                            break;
                            case 'Remain€':
                                    $data = !empty($remain_euro) ? number_format($remain_euro,2,',',' ') . ' '.$bg_currency : '';
                                    echo '<td style="text-align: right; width: 30px;white-space: nowrap;">' . $data . '</td>';
                            break;
                            case 'Workload€':
                                    $data = !empty($workload_euro) ? number_format($workload_euro,2,',',' ') . ' '.$bg_currency : '';
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
                                    $data = !empty($amount) ? number_format($amount,2,',',' ') . ' '.$bg_currency : '';
                                } else if ($word == '%pro'){
                                    $data = !empty($progress_order_amount) ? number_format($progress_order_amount,2,',',' ') . ' '.$bg_currency : '';
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

                echo '<tr style="background-color: #E8F0FA; font-weight: bold;">';
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
<div data-widget="phase" class="wd-input wd-area wd-none" style = "margin-top: 15px;">
    <?php
    $settings = $this->requestAction('/project_phases/getFields');
    ?>
    <div>
        <table id = "absence" style="width: 100%">
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
                                echo '<td><div style="position: relative; text-align: center"><div style="position: absolute; width: '. (!empty($dx['progress']) ? $dx['progress'] : 0) .'%; height: 100%; top: 0;left: 0; background-color: rgb(77, 255, 130);"></div><span style="position: relative">'.(!empty($dx['progress']) ? $dx['progress'] : '0.00').' %</span></div></td>';
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
<!-- end -->
                            </div>
                        </fieldset>
                        </form>
                    </div>
                </div>
            </div>
            </div></div>
        </div>
    </div>
</div>
<!-- end setting dialog -->
<?php
echo $validation->bind("Project");
echo $this->Form->create('Export', array('url' => array('controller' => 'projects', 'action' => 'export_pdf'), 'type' => 'file'));
echo $this->Form->hidden('canvas', array('id' => 'canvasData'));
echo $this->Form->hidden('height', array('id' => 'canvasHeight'));
echo $this->Form->hidden('width', array('id' => 'canvasWidth'));
echo $this->Form->hidden('project_name', array('value' => $projectName['Project']['project_name']));
echo $this->Form->hidden('project_id', array('value' => $projectName['Project']['id']));
echo $this->Form->end();
?>
<script type="text/javascript">
    (function($){
        $("#right, #left").click(function() {
            var dir = this.id=="right" ? '+=' : '-=' ;
            $(this).closest(".budget-progress").find('#budget-inner').stop().animate({scrollLeft: dir+'500'}, 1000);
        });
        
    })(jQuery);
    var yourFormFilter = <?php echo json_encode($yourFormFilter) ?>;
    var showMenu = <?php echo json_encode($showMenu) ?>;
    var project_id = <?php echo json_encode($project_name['Project']['id']) ?>;
    var $showKpiBudget = <?php echo json_encode($showKpiBudget) ?>;
    var $breakpage = <?php echo json_encode($breakpage) ?>;
    var $breakpage_new = <?php echo json_encode(array_values($breakpage)) ?>;
    var $totalXxxx = <?php echo json_encode($totalXxxx) ?>;
    var viewChar = <?php echo json_encode($viewChar);?>;
    var wdTable = $('.normal-scroll');
    var heightTable = $(window).height() - 280;
    wdTable.css({
        overflow: 'auto',
        height: heightTable,
    });

    $(window).resize(function(){
        var heightTable = $(window).height() - 280;
        wdTable.css({
            overflow: 'auto',
            height: heightTable,
        });
    });
    if($totalXxxx >= 0){
        $('#xxxx').find('td.saleOrder').html('<p class="task_blue_bg">' + number_format(parseFloat($totalXxxx), 2, ',', ' ') + '&nbsp;' + viewChar + '</p>');
    } else {
        $('#xxxx').find('td.saleOrder').html('<p class="task_red_bg">' + number_format(parseFloat($totalXxxx), 2, ',', ' ') + '&nbsp;' + viewChar + '</p>');
    }
    function number_format(number, decimals, dec_point, thousands_sep) {
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
	var page_height = 840;
    function SubmitDataExport(){
		$('#project-name').css('display', 'inline-block');
        $(".wmd-view-topscroll").scrollLeft(0);
        $('.wmd-view-topscroll').removeAttr('style');
        $('.wmd-view-topscroll').css('width', '100%');
        $('#wd-fragment-2').css('display', 'none');
        $('#img_location').css('display', '');
        $('#svg_inve_chard').css('display', 'none');
        $('#svg_circle_chard').css('display', 'none');
        collapseScreen();
        $('#wd-tab-content').removeClass('normal-scroll');
        $('#svg_kpi_1').css('display', 'none');
        $('#svg_fy_1').css('display', 'none');
        $('#svg_fy_2').css('display', 'none');
        $('#svg_fy_3').css('display', 'none');
        $('.progress-label').css('display', '');
        $('.scroll-progress').css('display', 'none');
        $('#svg_finance_two_plus').css('display', 'none');
        $('#label_fy').css({'position':'absolute', 'margin-top': '92px'});
        $('.svg_budget').css('display', 'none');
        $('#chart-wrapper').css('margin-left', 0).css('width', '1280');
        $('#wd-tab-content').css('height', 'auto');
        $('.weather-pdf').css('display', 'inline-block');
        $('.weather-pdf .wd-weather-list').css('display', '');
        $('.img_budget_export').css('display', '');
        $('body').css('overflow', 'auto');
        var _total = 0;
		autosize($('textarea.log-content'));
		$('textarea.log-content').each(function(ind, elm){
			$(elm).parent().append('<p class="log-comment">' + $(elm).text() + '</p>');
			$(elm).remove();
		});
        // var i = 1;
        // $($('#wd-fragment-temp').children()).each(function(val, index){
            // if($(index).is(':visible')){
                // $.each($breakpage, function(val, key){
                    // if(key == $.trim($(index).data('widget'))){
                        // var calcu = _total%840;
                        // if(calcu > 0){
                            // var j = i > 3 ? (i > 5 ? 27 : 10) : 5;
                            // var _hei = 840 - calcu + i*j;
                            // if(key == 'finance_plus'){
                                // _hei = 840 - calcu + i*30;
                            // }
                            // if(_hei >= 840) _hei = _hei%840;
                            // $(index).prepend('<div class="element-empty" style="height: '+_hei+'px">&nbsp</div>');
                            // i++;
                        // }
                    // }
                // });
                // var t = $(index).outerHeight() + 10;
                // _total += t;
            // }
        // }); 
		var i=0;
		var _space = 0, _hei = 0;
		var _total_height = 0;
		$($('#wd-fragment-temp').children()).each(function(val, index){
            if($(index).is(':visible')){
				// console.log( $(index).data('widget'),  $breakpage);
				_total_height += $(index).height();
				console.log($(index).data('widget'));
                if( $.inArray( $(index).data('widget'), $breakpage_new) !== -1){
					_this_height = $(index).height();
					_total_height -= _this_height;
					console.log(_total_height);
					if(_total_height < page_height){
						_space = page_height - _total_height;
					}else{
						_space = page_height - _total_height%page_height;
					}
					console.log(_space);
					$(index).prepend('<div class="element-empty" style="height: '+ _space +'px">&nbsp</div>');
					_total_height = _this_height;
				}
            }
        });
        // console.log(_w_height);
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
                    if( (curleft >= (oldleft - 150)) && (curleft <= (oldleft + 150 )) ){
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
    function refreshMap(show){
        var query = $.trim($('#coord-input').val());
        if( query ){
            //initial google maps
            $('#map-frame').prop('src', 'https://www.google.com/maps/embed/v1/place?q=' + encodeURIComponent(query) + '&key=' + gapi);
            if( show ){
                $('#map-frame').show();
                $('#local-frame').hide();
                state = 0;
            }
        } else {
            $('#map-frame').prop('src', 'about:blank');
        }
    }
    $(document).ready(function(){
        var saving = false;
        $('#coord-input').val(<?php echo json_encode($projectName['Project']['address']) ?>);
        <?php if($projectName['Project']['address']): ?> refreshMap(true);  <?php endif ?>
    });
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
		if( $('#diagram').length == 0) return;
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
    jQuery(document).ready(init);
    // order html.
    var digramTemp = <?php echo json_encode($diagramTemp) ?>;
    var locationTemp = <?php echo json_encode($locationTemp) ?>;
    var globalTemp = <?php echo json_encode(isset($globalTemp) ? $globalTemp :  ''); ?>;
    var ganttTemp = <?php echo json_encode($ganttTemp) ?>;
    var phaseTemp = $('#div_phase').html();
    var projectTaskTemp = $('#div_project_task').html();
    var financeTemp = $('#div_finance_plus').html();
    $('#div_finance_plus').html('');
    var financeTwoTemp = $('#div_finance_two_plus').html();
    $('#div_finance_two_plus').html('');
    var externalTemp = $('#div_external_cost').html();
    var internalTemp = $('#div_internal_cost').html();
    var issueTemp = $('#div_issue').html();
    var riskTemp = $('#div_risk').html();
    var milestoneTemp = $('#div_milestone').html();
    $('#div_milestone').html('');
    var weatheTemp = $('#div_weather').html();
    $('#div_weather').html('');
    var yourFormTemp = $('#div_your_form').html();
    var fyBudgetTemp = $('#div_fy_budget').html();
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
            case 'finance_two_plus':
                if(value != 0){
                    $('#wd-fragment-temp').append(financeTwoTemp);
                }
            break;
            case 'fy_budget':
                if(value != 0){
                    $('#wd-fragment-temp').append(fyBudgetTemp);
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
    $(document).ready(function () {
        var years    = <?php echo json_encode($setYear); ?>,
        manDays    = <?php echo json_encode($manDays); ?>,
        dataSets    = <?php echo !empty($dataSets) ? json_encode($dataSets) : json_encode(array()); ?>;
        var settings = {
                title: "",
                description: years,
                padding: { left: 5, top: 5, right: 5, bottom: 5 },
                titlePadding: { left: 90, top: 0, right: 0, bottom: 10 },
                source: dataSets,
                categoryAxis:
                    {
                        dataField: 'date',
                        description: '',
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
                                unitInterval: manDays,
                                description: '',
                                displayValueAxis: false
                            },
                            series: [
                                    // { dataField: 'estimation', displayText: 'Estimation', labelOffset: {x: 0, y: 10}},
                                    { dataField: 'consumed', displayText: '<?php echo __('Consumed', true);?>', labelOffset: {x: 0, y: 0}, color: '#538FFA'},
                                    { dataField: 'validated', displayText: '<?php echo __('Planed', true);?>', labelOffset: {x: 0, y: 20}, color: '#E44353'}

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
            $('#budget_db').jqxChart(settings);
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
                console.log(img1);
                img1.onload = function() {
                    try{
                        ctx1.drawImage(img1, 0, 0);
                        var png1 = canvas1.toDataURL("image/png");
                        document.querySelector('#png-container_kpi').innerHTML = '<img class="img_budget_export" style="display: none;width: 0px;float: left;height: 140px;margin:0; margin-top: 50px" src="'+png1+'"/>';
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

                        console.log(canvas);
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
                                    style = 'display: none;width: 900px;float: left;height: 280px;margin:0;';
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
        $('#wd-tab-content').addClass('full-scroll');
        $('#collapse').show();
        $(window).resize();
    }
    function collapseScreen(){
        $('#table-control').show();
        $('#expand-btn').show();
        $('#collapse').hide();
        $('#wd-tab-content').removeClass('full-scroll');
        $('#wd-tab-content').addClass('normal-scroll');
        $('#wd-container-main').removeClass('fullScreen');
        $(window).resize();
    }
    if( showMenu['fy_budget'] == 1 && yourFormFilter['fy_budget'] == 1){
        var svgString12 = new XMLSerializer().serializeToString(document.querySelector('#svg_fy_1'));
        var canvas12 = document.getElementById("canvas_fy_1");
        var ctx12 = canvas12.getContext("2d");
        var DOMURL12 = self.URL || self.webkitURL || self;
        var img12 = new Image();
        img12.crossOrigin = '';
        img12.crossOrigin='anonymous'
        var svg12 = new Blob([svgString12], {type: "image/svg+xml;charset=utf-8"});
        var url12 = DOMURL12.createObjectURL(svg12);
        img12.src = url12;
        img12.onload = function() {
            try{
                ctx12.drawImage(img12, 0, 0);
                var png12 = canvas12.toDataURL("image/png");
                document.querySelector('#png_fy_1').innerHTML = '<img class="img_budget_export" style="display: none;width: 90px; height: 90px; margin: 0; float: left; margin-bottom: 20px" src="'+png12+'"/>';
                DOMURL12.revokeObjectURL(png12);
            }catch(e){
                var b6412 = btoa(unescape(encodeURIComponent( svgString12 )));
                var url12 = "data:image/svg+xml;base64," + b6412;
                document.querySelector('#png_fy_1').innerHTML = '<img class="img_budget_export" style="display: none;width: 90px; height: 90px;margin: 0; float: left; margin-bottom: 20px" src="'+url12+'"/>';
            }
        };

        var svgStringfy2 = new XMLSerializer().serializeToString(document.querySelector('#svg_fy_2'));
        var canvasfy2 = document.getElementById("canvas_fy_2");
        var ctxfy2 = canvasfy2.getContext("2d");
        var DOMURL1 = self.URL || self.webkitURL || self;
        var imgfy2 = new Image();
        imgfy2.crossOrigin = '';
        imgfy2.crossOrigin='anonymous'
        var svgfy2 = new Blob([svgStringfy2], {type: "image/svg+xml;charset=utf-8"});
        var urlfy2 = DOMURL1.createObjectURL(svgfy2);
        imgfy2.src = urlfy2;
        imgfy2.onload = function() {
            try{
                ctxfy2.drawImage(imgfy2, 0, 0);
                var pngfy2 = canvasfy2.toDataURL("image/png");
                document.querySelector('#png_fy_2').innerHTML = '<img class="img_budget_export" style="display: none;width: 90px; height: 90px; margin: 0; float: left; margin-bottom: 20px" src="'+pngfy2+'"/>';
                DOMURL1.revokeObjectURL(pngfy2);
            }catch(e){
                var b64 = btoa(unescape(encodeURIComponent( svgStringfy2 )));
                var url = "data:image/svg+xml;base64," + b64;
                document.querySelector('#png_fy_2').innerHTML = '<img class="img_budget_export" style="display: none;width: 90px; height: 90px;margin: 0; float: left; margin-bottom: 20px" src="'+url+'"/>';
            }
        };
        //
        var svgStringfy22 = new XMLSerializer().serializeToString(document.querySelector('#svg_fy_3'));
        var canvasfy22 = document.getElementById("canvas_fy_3");
        var ctxfy22 = canvasfy22.getContext("2d");
        var DOMURL2 = self.URL || self.webkitURL || self;
        var imgfy22 = new Image();
        imgfy22.crossOrigin = '';
        imgfy22.crossOrigin='anonymous'
        var svgfy22 = new Blob([svgStringfy22], {type: "image/svg+xml;charset=utf-8"});
        var urlfy22 = DOMURL2.createObjectURL(svgfy22);
        imgfy22.src = urlfy22;
        imgfy22.onload = function() {
            try{
                ctxfy22.drawImage(imgfy22, 0, 0);
                var pngfy22 = canvasfy22.toDataURL("image/png");
                document.querySelector('#png_fy_3').innerHTML = '<img class="img_budget_export" style="display: none;width: 90px; height: 90px; margin: 0; float: left; margin-bottom: 20px" src="'+pngfy22+'"/>';
                DOMURL2.revokeObjectURL(pngfy22);
            }catch(e){
                var b64 = btoa(unescape(encodeURIComponent( svgStringfy22 )));
                var url = "data:image/svg+xml;base64," + b64;
                document.querySelector('#png_fy_3').innerHTML = '<img class="img_budget_export" style="display: none;width: 90px; height: 90px;margin: 0; float: left; margin-bottom: 20px" src="'+url+'"/>';
            }
        };
    }
    if( yourFormFilter['finance_two_plus'] == 1){
        var svgStringfi = new XMLSerializer().serializeToString(document.querySelector('#svg_finance_two_plus'));
        $('#svg_finance_two_plus').css('margin-top', '-80px');
        var canvasfi = document.getElementById("canvas_finance_two_plus");
        var ctxfi = canvasfi.getContext("2d");
        var DOMURL = self.URL || self.webkitURL || self;
        var imgfi = new Image();
        imgfi.crossOrigin = '';
        imgfi.crossOrigin='anonymous'
        var svgfi = new Blob([svgStringfi], {type: "image/svg+xml;charset=utf-8"});
        var urlfi = DOMURL.createObjectURL(svgfi);
        imgfi.src = urlfi;
        imgfi.onload = function() {
            try{
                ctxfi.drawImage(imgfi, 0, 0);
                var pngfi = canvasfi.toDataURL("image/png");
                document.querySelector('#png_finance_two_plus').innerHTML = '<img class="img_budget_export" style="display: none;width: 250px; height: 130px;margin: 0; float: left; margin-top: -70px" src="'+pngfi+'"/>';
                DOMURL.revokeObjectURL(pngfi);
            }catch(e){
                var b64 = btoa(unescape(encodeURIComponent( svgStringfi )));
                var url = "data:image/svg+xml;base64," + b64;
                document.querySelector('#png_finance_two_plus').innerHTML = '<img class="img_budget_export" style="display: none;width: 250px; height: 130px;margin: 0; float: left; margin-top: -70px" src="'+url+'"/>';
            }
        };
    }
    function scrollPage(){
        var wdTable = $('#wd-tab-content');
        var heightTable = $(window).height() - wdTable.offset().top - 40;
        //heightTable = (heightTable < 500) ? 500 : heightTable;
        wdTable.css({
            height: heightTable,
        });
        $(window).resize(function(){
            heightTable = $(window).height() - wdTable.offset().top - 40;
            //heightTable = (heightTable < 500) ? 500 : heightTable;
            wdTable.css({
                height: heightTable,
            });
        });
    }
    scrollPage();
    setTimeout(function(){
        var img = $('#global_view').find('img');
        img.on('load', function(){
            var width = $(this).width();
            if(width > 0 && width < $('#global_view').width()){
                $(img).css('margin-left', ($('#global_view').width() - width)/2 + 'px');
            }
        });
    }, 3500);
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

<script>
    function draw_progress(myCanvas){
        //var id = id ? id : 'myCanvas';
        var id = myCanvas;
        var canvas = document.getElementById(id);
        var context = canvas.getContext('2d');
        /* Khởi tạo giá trị */
        var al=0; // giá trị khởi đầu
        var start= 21; // Vị trí khởi đầu
        var border = 10; // độ rộng của vòng cung
        var bgr_cl = '#d4d4d4'; // Background color
        var total_steps = 12; // chia vòng tròn làm 12 phần
        var num_steps = 10; // chạy 10 phần bỏ trống 2
        var not_fill = 0.1; // tô màu 0.9 (90%), bỏ trống 0.1 (10%)
        var max = 0; // giá trị dừng 
        if( canvas.getAttribute('data-value') ) max = canvas.getAttribute('data-value');
        var format = max;
        if(max > 100) max = 100;
        var font = '15pt Verdana';
        arr_cl2 = ['#DB414F','#E0636E','#EA848E','#EFA5AC','#EFA5AC','#EFA5AC','#EFA5AC','#EFA5AC','#EFA5AC','#EFA5AC']; // cái này cho nó trên 50%
        arr_cl1 = ['#75AF7E','#8FBB96','#AECDB3','#DBE4DC','#DBE4DC','#DBE4DC','#DBE4DC','#DBE4DC','#DBE4DC']; // cái này cho dưới 50%
        /* End Khởi tạo giá trị */
        var width = context.canvas.width;
        var height = context.canvas.height;
        var square = Math.min(width,height); // lấy kích thước canvas
        square /= 2; // bán kính = 1/2 canvas
        square -= border/2; // trừ phần border 
        var cw=context.canvas.width/2;
        var ch=context.canvas.height/2;
        var diff; // độ dài vòng cung
        var percent;

        /*
        arc( x,y,R, start_angle, e_angle,bool counterclockwise);
        */
        function progressBar(){
            angle = Math.PI*2/total_steps; // vong cung của 1 góc
            diff=(al/10)*Math.PI*2;
            context.clearRect(0,0,400,200);
            
            // Vẽ vòng cung màu nhạt bên dưới (placeholder)
            for(i = 0; i< num_steps; i++){
              context.beginPath(); // clear vị trí con trỏ graph
              context.lineWidth = border; // độ rộng của vòng cung
              context.arc(cw,ch,square,i*angle+start ,(i+1)*angle + start - angle*not_fill ,false);
              context.strokeStyle=bgr_cl;
              context.stroke();
            }
            // context.strokeStyle= al <50 ? '#F29D3A' : 'red';
            
            
            // Tô màu viền
            color = max > 90 ? arr_cl2 : arr_cl1;
            //number = parseInt(max/10);
            number = Math.floor(max/10); // làm tròn xuống
            surplus = parseInt(max%10);
            i=0;
            for(i=0; i < number;i++){
                context.beginPath(); // clear vị trí con trỏ graph
                context.lineWidth = border; // độ rộng của vòng cung
                context.arc(cw,ch,square,i*angle+start ,(i+1)*angle + start - angle*not_fill ,false);
                color_index = number - i - 1; 
                context.strokeStyle=color[color_index];
                context.stroke();
            }
            if(surplus){
                context.beginPath(); // clear vị trí con trỏ graph
                context.lineWidth = border; // độ rộng của vòng cung
                surplus_angle = surplus*angle/10;
                context.arc(cw,ch,square,i*angle+start ,i*angle + start + surplus_angle ,false);
                color_index = 0; 
                context.strokeStyle=color[color_index];
                context.stroke();
            }
        // End tô màu viền
        
        // vẽ hình tròn bên trong      
        context.beginPath();
        context.arc(cw,ch,square - 2*border,0,Math.PI*2,false);
        context.fillStyle = bgr_cl;
        context.fill();
        
        // vẽ chữ
        context.beginPath();
        context.textAlign='center';
        context.lineWidth = 5;
        context.font = font;
        context.fillStyle = '#fff';
        context.fill();
        context.beginPath();
        context.stroke();
        context.fillText(format+'%',cw+2,ch+6);
        
        // Phần này để đó cho chữ chạy và thanh progres chạy
        /*percent = parseInt(al/10) * 10;
        context.fillText(percent+'%',cw+2,ch+6);
        ///al = al == 99 ? 0 : al;  
        if(al>= max){
              clearTimeout(bar);
          }
          al++;*/
    }

    //var bar=setInterval(progressBar,50); // cái này cho chạy với delay 50
    var bar = progressBar();
    }
    // var prog = draw_progress('myCanvas');
    // var prog = draw_progress('myCanvas-2');

    if(document.getElementById('myCanvas')){
        var prog = draw_progress('myCanvas');
    } 
    if(document.getElementById('myCanvas-2')){
        var prog = draw_progress('myCanvas-2');
    } 
    if(document.getElementById('myCanvas-3')){
        var prog = draw_progress('myCanvas-3');
    }


</script>
