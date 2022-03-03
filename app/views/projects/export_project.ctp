<?php
ob_start();
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
App::import("Vendor", "str_utility");
$str_utility = new str_utility();
$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . 'project.xls');
$objPHPExcel->setActiveSheetIndex(0);
$activeSheet = & $objPHPExcel->getActiveSheet();
$activeSheet->setTitle('Project List');
/* PhpExcel Set function */
Class PhpExcelSet {

    protected $_sheet = null;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct(PHPExcel_Worksheet $activeSheet) {
        $this->_sheet = & $activeSheet;
    }

    /**
     * Set alignment.
     *
     * @return void
     */
    public function align($range) {
        $this->_sheet->getStyle($range)->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    }

    /**
     * Set cell range value and style.
     *
     * @return void
     */
    public function value($range, $value = null, $style = array()) {
        $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
        $this->_sheet->setCellValue($range, $value);
        if ($style) {
            if (is_string($style)) {
                $style = array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('rgb' => $style)
                );
                $this->_sheet->getStyle($range)->getFill()->applyFromArray($style);
            } else {
                $this->_sheet->getStyle($range)->applyFromArray($style);
            }
        }
    }

    /**
     * Set cell range value and style.
     *
     * @return void
     */
    public function border($range, $style = array()) {
        $default = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                )
            )
        );
        $this->_sheet->getStyle($range)->applyFromArray(array_merge($default, $style));
    }
}

// PhpExcelSet Object
$PhpExcel = new PhpExcelSet($activeSheet);
$activeSheet->getColumnDimension('A')->setWidth(10);
$PhpExcel->align('A');
$cIndex = 1;
$listDatas = array(
    'workload_y' => __('Workload', true) . ' ' . date('Y', time()),
    'workload_last_one_y' => __('Workload', true) . ' ' . (date('Y', time()) - 1),
    'workload_last_two_y' => __('Workload', true) . ' ' . (date('Y', time()) - 2),
    'workload_last_thr_y' => __('Workload', true) . ' ' . (date('Y', time()) - 3),
    'workload_next_one_y' => __('Workload', true) . ' ' . (date('Y', time()) + 1),
    'workload_next_two_y' => __('Workload', true) . ' ' . (date('Y', time()) + 2),
    'workload_next_thr_y' => __('Workload', true) . ' ' . (date('Y', time()) + 3),
    'consumed_y' => __('Consumed', true) . ' ' . date('Y', time()),
    'consumed_last_one_y' => __('Consumed', true) . ' ' . (date('Y', time()) - 1),
    'consumed_last_two_y' => __('Consumed', true) . ' ' . (date('Y', time()) - 2),
    'consumed_last_thr_y' => __('Consumed', true) . ' ' . (date('Y', time()) - 3),
    'consumed_next_one_y' => __('Consumed', true) . ' ' . (date('Y', time()) + 1),
    'consumed_next_two_y' => __('Consumed', true) . ' ' . (date('Y', time()) + 2),
    'consumed_next_thr_y' => __('Consumed', true) . ' ' . (date('Y', time()) + 3),
    'provisional_budget_md' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional M.D", true),
    'provisional_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . date('Y', time()),
    'provisional_last_one_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) - 1),
    'provisional_last_two_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) - 2),
    'provisional_last_thr_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) - 3),
    'provisional_next_one_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) + 1),
    'provisional_next_two_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) + 2),
    'provisional_next_thr_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) + 3)
);

/* number format */
$floatNumbers = array(
    'ProjectAmr.manual_consumed','ProjectAmr.md_validated','ProjectAmr.md_engaged','ProjectAmr.md_forecasted','ProjectAmr.md_variance','ProjectAmr.engaged','ProjectAmr.variance','ProjectAmr.forecasted','ProjectBudgetSyn.sales_sold','ProjectBudgetSyn.sales_to_bill','ProjectBudgetSyn.sales_billed','ProjectBudgetSyn.sales_paid','ProjectBudgetSyn.sales_man_day','ProjectBudgetSyn.total_costs_budget','ProjectBudgetSyn.total_costs_forecast','ProjectBudgetSyn.total_costs_engaged','ProjectBudgetSyn.total_costs_remain','ProjectBudgetSyn.total_costs_man_day','ProjectBudgetSyn.internal_costs_budget','ProjectBudgetSyn.internal_costs_forecast','ProjectBudgetSyn.internal_costs_engaged','ProjectBudgetSyn.internal_costs_remain','ProjectBudgetSyn.internal_costs_forecasted_man_day','ProjectBudgetSyn.external_costs_budget','ProjectBudgetSyn.external_costs_forecast','ProjectBudgetSyn.external_costs_ordered','ProjectBudgetSyn.external_costs_remain','ProjectBudgetSyn.external_costs_man_day','ProjectBudgetSyn.internal_costs_budget_man_day','ProjectBudgetSyn.internal_costs_average', 'ProjectBudgetSyn.internal_costs_engaged_md' ,'ProjectAmr.delay','ProjectFinance.bp_investment_city','ProjectFinance.bp_operation_city','ProjectFinance.available_investment','ProjectFinance.available_operation','ProjectFinance.finance_total_budget','ProjectBudgetSyn.workload_y','ProjectBudgetSyn.workload_last_one_y','ProjectBudgetSyn.workload_last_two_y','ProjectBudgetSyn.workload_last_thr_y','ProjectBudgetSyn.workload_next_one_y','ProjectBudgetSyn.workload_next_two_y','ProjectBudgetSyn.workload_next_thr_y','ProjectBudgetSyn.consumed_y','ProjectBudgetSyn.consumed_last_one_y','ProjectBudgetSyn.consumed_last_two_y','ProjectBudgetSyn.consumed_last_thr_y','ProjectBudgetSyn.consumed_next_one_y','ProjectBudgetSyn.consumed_next_two_y','ProjectBudgetSyn.consumed_next_thr_y','ProjectBudgetSyn.workload','ProjectBudgetSyn.overload','ProjectBudgetSyn.provisional_budget_md','ProjectBudgetSyn.provisional_y','ProjectBudgetSyn.provisional_last_one_y','ProjectBudgetSyn.provisional_last_two_y','ProjectBudgetSyn.provisional_last_thr_y','ProjectBudgetSyn.provisional_next_one_y','ProjectBudgetSyn.provisional_next_two_y','ProjectBudgetSyn.provisional_next_thr_y','Project.price_1','Project.price_2','Project.price_3','Project.price_4','Project.price_5','Project.price_6','ProjectBudgetSyn.external_costs_progress_euro','ProjectBudgetSyn.external_costs_var','ProjectBudgetSyn.external_costs_progress','ProjectAmr.budget','Project.budget'
);
$cates = array(
    1 => __("In progress", true),
    2 => __("Opportunity", true),
    3 => __("Archived", true),
    4 => __("Model", true)
);
$words = $this->requestAction('/translations/getByPage', array('pass' => array('KPI')));
foreach ($fieldset as $_fieldset) {
    if($addInternalMD == true && $_fieldset['key'] === 'ProjectBudgetSyn.internal_costs_budget_man_day'){
        continue;
    }
    if($addExternalMD == true && $_fieldset['key'] === 'ProjectBudgetSyn.external_costs_man_day'){
        continue;
    }
    $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
    $width = 30;
    switch ($_fieldset['key']) {
        case 'Project.project_name': {
                $width = 40;
                break;
            }
        case 'ProjectAmr.weather':
        case 'ProjectAmr.cost_control_weather':
        case 'ProjectAmr.planning_weather':
        case 'ProjectAmr.risk_control_weather':
        case 'ProjectAmr.organization_weather':
        case 'ProjectAmr.perimeter_weather':
        case 'ProjectAmr.issue_control_weather': {
                $PhpExcel->align($colName);
                $width = 20;
                break;
            }
    }
    $financeFieldsKey = array_keys($financeFields);
    if(in_array($_fieldset['key'],$financeFieldsKey)){
        $fieldName = __($financeFields[$_fieldset['key']], true);
        $ff = explode('.', $_fieldset['key']);
        if($ff[0] == 'ProjectFinancePlus'){
            $saveFieldName = explode(' ', $fieldName);
            if(is_numeric($saveFieldName[2])){
                $fieldName = $saveFieldName[0] . ' ' . $saveFieldName[1] . ' (Y)';
                $fieldName = __d(sprintf($_domain, 'Finance'), $fieldName, true);
                $fieldName = str_replace('(Y)', $saveFieldName[2], $fieldName);
            } else {
                $fieldName = $fieldName;
                $fieldName = __d(sprintf($_domain, 'Finance'), $fieldName, true);
            }
        }
    } else if( strpos($_fieldset['key'], 'Project.') !== false ){
        if( $_fieldset['key'] == 'Project.category' ) {
            $fieldName = __($_fieldset['name'], true);
        } else {
            $fieldName = substr($_fieldset['name'], 0, 1) == '*' ? __(substr($_fieldset['name'], 1), true) : __d(sprintf($_domain, 'Details'), $_fieldset['name'], true);
        }
    } else if( strpos($_fieldset['key'], 'ProjectAmr.') !== false && in_array($_fieldset['name'], $words) ){
        $fieldName = __d(sprintf($_domain, 'KPI'), $_fieldset['name'], true);
    }
    else {
        $fieldName = __($_fieldset['name'], true);
        $ff = explode('.', $_fieldset['key']);
        if( substr($ff[1], 0, 5) == 'sales' ){
            $fieldName = __d(sprintf($_domain, 'Sales'), $_fieldset['name'], true);
        } else if ( substr($ff[1], 0, 8) == 'internal' ) {
            if($_fieldset['key'] == 'ProjectBudgetSyn.internal_costs_engaged_md'){
                $_fieldset['name'] = 'Engaged M.D';
            }
            $fieldName = __d(sprintf($_domain, 'Internal_Cost'), $_fieldset['name'], true);
        } else if ( substr($ff[1], 0, 8) == 'external' ) {
            $fieldName = __d(sprintf($_domain, 'External_Cost'), $_fieldset['name'], true);
        }
    }
    $fields = trim(str_replace('ProjectBudgetSyn.', '', $_fieldset['key']));
    $fieldName = !empty($listDatas) && !empty($listDatas[$fields]) ? $listDatas[$fields] : $fieldName;
    $PhpExcel->value($colName . '1', $fieldName);
    $activeSheet->getColumnDimension($colName)->setWidth($width);
}
$PhpExcel->border('A1:' . $colName . '1', array(
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array('rgb' => '5fa1c4')
    ),
    'font' => array(
        'size' => 11,
        'bold' => true,
        'color' => array(
            'rgb' => 'FFFFFF'
    ))));
$rIndex = 2;
$no = 1;
$colMax = $colName;
foreach ($projects as $project) {
    $PhpExcel->value('A' . $rIndex, $no++);
    $activeSheet->getRowDimension($rIndex)->setRowHeight(21);

    $cIndex = 1;
    foreach ($fieldset as $_fieldset) {
        if($addInternalMD == true && $_fieldset['key'] === 'ProjectBudgetSyn.internal_costs_budget_man_day'){
            continue;
        }
        if($addExternalMD == true && $_fieldset['key'] === 'ProjectBudgetSyn.external_costs_man_day'){
            continue;
        }
        $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
        if (is_array($_fieldset['path'])) {
            $_output = (string) current(Set::format(array($project), $_fieldset['path'][0], $_fieldset['path'][1]));
        } else {
            $_output = (string) Set::classicExtract($project, $_fieldset['path']);
        }
        $fieldName = explode('.', $_fieldset['key']);
        $fieldName = $fieldName[1];
        switch ($_fieldset['key']) {
            case 'Project.start_date':{
                    $_output = $str_utility->convertToVNDate($project["Project"]["start_date"]);
                    break;
                }
            case 'Project.end_date': {
                    $_output = $str_utility->convertToVNDate($project["Project"]["end_date"]);
                    break;
                }
            // case 'ProjectBudgetSyn.total_costs_man_day':
            // case 'ProjectBudgetSyn.workload':
            // case 'ProjectBudgetSyn.provisional_budget_md':
            // case 'ProjectBudgetSyn.workload':
            // case 'ProjectBudgetSyn.provisional_budget_md':
            // case 'ProjectBudgetSyn.external_costs_forecast':
            // case 'ProjectBudgetSyn.internal_costs_budget_man_day':
            // case 'ProjectFinance.finance_total_budget':
            // case '':
            // case '':
            // case '':
            // case '':
            // case '':
            // case '':
            //     $_output = number_format($_output, 2);
            //     break;
            case 'Project.date_1':
            case 'Project.date_2':
            case 'Project.date_3':
            case 'Project.date_4':
                $_output = $str_utility->convertToVNDate($_output);
            break;
            case 'Project.last_modified':
                $_output = $_output ? date('Y-m-d H:i:s', $_output) : '';
                break;
            case 'Project.category':
                $_output = $cates[$_output];
                break;
            case 'Project.bool_1':
            case 'Project.bool_2':
            case 'Project.bool_3':
            case 'Project.bool_4':
                $_output = $_output ? 1 : 0;
            break;
            case 'Project.yn_1':
            case 'Project.yn_2':
            case 'Project.yn_3':
            case 'Project.yn_4':
                $_output = $_output ? __('Yes', true) : __('No', true);
            break;
            case 'Project.list_1':
            case 'Project.list_2':
            case 'Project.list_3':
            case 'Project.list_4':
                $lid = $_output;
                $_output = isset($datasets[$fieldName][$lid]) ? $datasets[$fieldName][$lid] : '';
            break;
            case 'ProjectAmr.weather':
            case 'ProjectAmr.rank':
            case 'ProjectAmr.cost_control_weather':
            case 'ProjectAmr.planning_weather':
            case 'ProjectAmr.risk_control_weather':
            case 'ProjectAmr.organization_weather':
            case 'ProjectAmr.perimeter_weather':
            case 'ProjectAmr.issue_control_weather': {
                if( file_exists('./weathers/' . $_output . '.png') ){
                    $objDrawing = new PHPExcel_Worksheet_Drawing();
                    $objDrawing->setName('Global Weather');
                    $objDrawing->setDescription('Global Weather');
                    $objDrawing->setPath('./weathers/' . $_output . '.png');
                    $objDrawing->setCoordinates($colName . $rIndex);
                    $objDrawing->setOffsetX(55);
                    $objDrawing->setWorksheet($activeSheet);
                    $_output = '';
                    break;
                }
            }
            case 'ProjectAmr.updated':
                $_output = $_output ? date('Y-m-d H:i:s', $_output) : '';
                break;
            case 'ProjectAmr.created':
                $_output = $_output ? date('Y-m-d', $_output) : '';
                break;
            case 'Project.project_phase_id' : {
                if(!empty($project['ProjectPhaseCurrent'])){
                    $outPhase = array();
                    foreach($project['ProjectPhaseCurrent'] as $phasePlans){
                        $phaseID = $phasePlans['project_phase_id'];
                        $outPhase[$phaseID] = !empty($projectPhases[$phaseID]) ? $projectPhases[$phaseID] : '';
                    }
                    $_output = implode(', ', $outPhase);
                } else {
                    $_output = '';
                }
                break;
            }
            case 'Project.list_muti_1' : {
                $_output = '';
                if(!empty($project['ProjectListMultiple'])){
                    foreach ($project['ProjectListMultiple'] as $key => $value) {
                        if($value['key'] == 'project_list_multi_1'){
                            if(empty($_output)){
                                $_output = $datasets['list_muti_1'][$value['project_dataset_id']];
                            } else {
                                $_output .= ', ' . $datasets['list_muti_1'][$value['project_dataset_id']];
                            }
                        }
                    }
                }
                break;
            }
            case 'Project.list_muti_2' : {
                $_output = '';
                if(!empty($project['ProjectListMultiple'])){
                    foreach ($project['ProjectListMultiple'] as $key => $value) {
                        if($value['key'] == 'project_list_multi_2'){
                            if(empty($_output)){
                                $_output = $datasets['list_muti_2'][$value['project_dataset_id']];
                            } else {
                                $_output .= ', ' . $datasets['list_muti_2'][$value['project_dataset_id']];
                            }
                        }
                    }
                }
                break;
            }
            case 'Project.list_muti_3' : {
                $_output = '';
                if(!empty($project['ProjectListMultiple'])){
                    foreach ($project['ProjectListMultiple'] as $key => $value) {
                        if($value['key'] == 'project_list_multi_3'){
                            if(empty($_output)){
                                $_output = $datasets['list_muti_3'][$value['project_dataset_id']];
                            } else {
                                $_output .= ', ' . $datasets['list_muti_3'][$value['project_dataset_id']];
                            }
                        }
                    }
                }
                break;
            }
            case 'Project.list_muti_4' : {
                $_output = '';
                if(!empty($project['ProjectListMultiple'])){
                    foreach ($project['ProjectListMultiple'] as $key => $value) {
                        if($value['key'] == 'project_list_multi_4'){
                            if(empty($_output)){
                                $_output = $datasets['list_muti_4'][$value['project_dataset_id']];
                            } else {
                                $_output .= ', ' . $datasets['list_muti_4'][$value['project_dataset_id']];
                            }
                        }
                    }
                }
                break;
            }
            case 'Project.list_muti_5' : {
                $_output = '';
                if(!empty($project['ProjectListMultiple'])){
                    foreach ($project['ProjectListMultiple'] as $key => $value) {
                        if($value['key'] == 'project_list_multi_5'){
                            if(empty($_output)){
                                $_output = $datasets['list_muti_5'][$value['project_dataset_id']];
                            } else {
                                $_output .= ', ' . $datasets['list_muti_5'][$value['project_dataset_id']];
                            }
                        }
                    }
                }
                break;
            }
            case 'Project.list_muti_6' : {
                $_output = '';
                if(!empty($project['ProjectListMultiple'])){
                    foreach ($project['ProjectListMultiple'] as $key => $value) {
                        if($value['key'] == 'project_list_multi_6'){
                            if(empty($_output)){
                                $_output = $datasets['list_muti_6'][$value['project_dataset_id']];
                            } else {
                                $_output .= ', ' . $datasets['list_muti_6'][$value['project_dataset_id']];
                            }
                        }
                    }
                }
                break;
            }
            case 'Project.list_muti_7' : {
                $_output = '';
                if(!empty($project['ProjectListMultiple'])){
                    foreach ($project['ProjectListMultiple'] as $key => $value) {
                        if($value['key'] == 'project_list_multi_7'){
                            if(empty($_output)){
                                $_output = $datasets['list_muti_7'][$value['project_dataset_id']];
                            } else {
                                $_output .= ', ' . $datasets['list_muti_7'][$value['project_dataset_id']];
                            }
                        }
                    }
                }
                break;
            }
            case 'Project.list_muti_8' : {
                $_output = '';
                if(!empty($project['ProjectListMultiple'])){
                    foreach ($project['ProjectListMultiple'] as $key => $value) {
                        if($value['key'] == 'project_list_multi_8'){
                            if(empty($_output)){
                                $_output = $datasets['list_muti_8'][$value['project_dataset_id']];
                            } else {
                                $_output .= ', ' . $datasets['list_muti_8'][$value['project_dataset_id']];
                            }
                        }
                    }
                }
                break;
            }
            case 'Project.list_muti_9' : {
                $_output = '';
                if(!empty($project['ProjectListMultiple'])){
                    foreach ($project['ProjectListMultiple'] as $key => $value) {
                        if($value['key'] == 'project_list_multi_9'){
                            if(empty($_output)){
                                $_output = $datasets['list_muti_9'][$value['project_dataset_id']];
                            } else {
                                $_output .= ', ' . $datasets['list_muti_9'][$value['project_dataset_id']];
                            }
                        }
                    }
                }
                break;
            }
            case 'Project.list_muti_10' : {
                $_output = '';
                if(!empty($project['ProjectListMultiple'])){
                    foreach ($project['ProjectListMultiple'] as $key => $value) {
                        if($value['key'] == 'project_list_multi_10'){
                            if(empty($_output)){
                                $_output = $datasets['list_muti_10'][$value['project_dataset_id']];
                            } else {
                                $_output .= ', ' . $datasets['list_muti_10'][$value['project_dataset_id']];
                            }
                        }
                    }
                }
                break;
            }
            default:
                if( in_array($_fieldset['key'], $floatNumbers) ){
                    if( !is_numeric($_output) ){
                        $_output = '0.00';
                    }
                    $activeSheet->getStyle($colName . $rIndex)->getNumberFormat()->setFormatCode('# ##0.00');
                }
            break;
        }
        $PhpExcel->value($colName . $rIndex, $_output);
    }
    $styleArray = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            )
        )
    );
    //$PhpExcel->border('A' . $rIndex . ":" . $colName . $rIndex);
    $PhpExcel->align('A' . $rIndex . ":" . $colName . $rIndex);
    $PhpExcel->border('A' . $rIndex . ":" . $colName . $rIndex, array(
    'fill' => array(),
    'font' => array(
        'size' => 11,
        'bold' => false,
        )
    ));
    $rIndex++;
}
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="projects_vision_' . date('H_i_s_d_m_Y') . '.xls"');
header('Content-Transfer-Encoding: binary');
header('Cache-Control: max-age=0');
ob_end_clean();
$objWriter->save('php://output');
