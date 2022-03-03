<?php
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
App::import("Vendor", "str_utility");
$str_utility = new str_utility();
$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . 'project.xls');
$objPHPExcel->setActiveSheetIndex(0);
$activeSheet = & $objPHPExcel->getActiveSheet();
$activeSheet->setTitle('Audit Recommendation List');
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
$fieldset = array(
    array(
        'name' => __('Title', true),
        'path' => 'mission_title',
        'width' => 20,
    ),
    array(
        'name' => __('Number', true),
        'path' => 'mission_number',
        'width' => 20,
    ),
    array(
        'name' => __('Status', true),
        'path' => 'audit_setting_mission_status',
        'width' => 20,
    ),
    array(
        'name' => __('Auditor Company', true),
        'path' => 'audit_setting_auditor_company',
        'width' => 20,
    ),
    array(
        'name' => __('Manager', true),
        'path' => 'mission_manager',
        'width' => 50,
    ),
    array(
        'name' => __('ID Recommendation', true),
        'path' => 'id_recommendation',
        'width' => 30,
    ),
    array(
        'name' => __('Statement', true),
        'path' => 'contact',
        'width' => 20,
    ),
    array(
        'name' => __('Recommendation', true),
        'path' => 'recommendation',
        'width' => 20,
    ),
    array(
        'name' => __('Priority Recommendation', true),
        'path' => 'audit_setting_recom_priority',
        'width' => 40,
    ),
    array(
        'name' => __('Theme Recommendation', true),
        'path' => 'recom_theme',
        'width' => 40,
    ),
    array(
        'name' => __('Mission Manager Comments', true),
        'path' => 'comment_manager',
        'width' => 40,
    ),
    array(
        'name' => __('Recommendation Status (Mission Manager)', true),
        'path' => 'audit_setting_recom_status_mission',
        'width' => 60,
    ),
    array(
        'name' => __('Date Change Status (Mission Manager)', true),
        'path' => 'date_change_status_mission',
        'width' => 50,
    ),
    array(
        'name' => __('Recommendation Manager', true),
        'path' => 'recom_manager',
        'width' => 60,
    ),
    array(
        'name' => __('Initial Response Recommendation Manager', true),
        'path' => 'response_recom_manager',
        'width' => 50,
    ),
    array(
        'name' => __('Initial Implementation Date', true),
        'path' => 'implement_date',
        'width' => 40,
    ),
    array(
        'name' => __('Date Of Implementation Revised', true),
        'path' => 'implement_revised',
        'width' => 40,
    ),
    array(
        'name' => __('Recommendation Status (Recommendation Manager)', true),
        'path' => 'audit_setting_recom_status_recom',
        'width' => 60,
    ),
    array(
        'name' => __('Date Change Status (Recommendation Manager)', true),
        'path' => 'date_change_status_recom',
        'width' => 60,
    ),
    array(
        'name' => __('Operator Modification', true),
        'path' => 'author_modify',
        'width' => 40,
    ),
    array(
        'name' => __('Recommendation Manager Comments', true),
        'path' => 'comment_recom',
        'width' => 40,
    ),
    array(
        'name' => __('Attachments', true),
        'path' => 'attachments',
        'width' => 40,
    )
);
/**
 * Set header
 */
$PhpExcel->value('A1', __('Mission', true));
$activeSheet->mergeCells('A1:F1');
$PhpExcel->align('A1:F1');
$PhpExcel->value('G1', __('Recommendation', true));
$activeSheet->mergeCells('G1:W1');
$PhpExcel->align('G1:W1');
$PhpExcel->border('A1:F1', array(
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array('rgb' => '5fa1c4')
    ),
    'font' => array(
        'bold' => true,
        'color' => array(
            'rgb' => 'FFFFFF'
            ),
        'size' => 11,
        )
    ));
$PhpExcel->border('G1:W2', array(
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array('rgb' => '75923C')
    ),
    'font' => array(
        'bold' => true,
        'color' => array(
            'rgb' => 'FFFFFF'
            ),
        'size' => 11,
        )
    ));
$activeSheet->getColumnDimension('A')->setWidth(10);
$PhpExcel->value('A2', __('No.', true));
$cIndex = 1;
foreach ($fieldset as $_fieldset) {
    $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
    $PhpExcel->value($colName . '2', __($_fieldset['name'], true));
    $activeSheet->getColumnDimension($colName)->setWidth($_fieldset['width']);
}


$PhpExcel->align('A2:' . $colName . '2');
$PhpExcel->border('A2:F2', array(
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array('rgb' => '5fa1c4')
    ),
    'font' => array(
        'bold' => true,
        'color' => array(
            'rgb' => 'FFFFFF'
            ),
        'size' => 11,
        )
    ));
$PhpExcel->border('G2:' . $colName . '2', array(
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array('rgb' => '75923C')
    ),
    'font' => array(
        'bold' => true,
        'color' => array(
            'rgb' => 'FFFFFF'
            ),
        'size' => 11,
        )
    ));
$rIndex = 3;
$no = 1;
$colMax = $colName;
if(!empty($auditRecoms)){
    foreach($auditRecoms as $auditMissionId => $auditRecom){
        foreach($auditRecom as $auditRecomId => $audit){
            $PhpExcel->value('A' . $rIndex, $no++);
            $activeSheet->getRowDimension($rIndex)->setRowHeight(21);
            $cIndex = 1;
            foreach ($fieldset as $_fieldset) {
                $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
                $_output = Set::classicExtract($audit, $_fieldset['path']);
                switch ($_fieldset['path']) {
                    case 'mission_title':{
                        $vals = !empty($auditMissions[$auditMissionId]['mission_title']) ? $auditMissions[$auditMissionId]['mission_title'] : '';
                        $_output = $vals;
                        break;
                    }
                    case 'mission_number':{
                        $vals = !empty($auditMissions[$auditMissionId]['mission_number']) ? $auditMissions[$auditMissionId]['mission_number'] : '';
                        //$_output = !empty($vals) ? str_replace('.', ',', $vals) : '';
                        $_output = $vals;
                        break;
                    }
                    case 'audit_setting_mission_status':{
                        $vals = !empty($auditMissions[$auditMissionId]['audit_setting_mission_status']) ? $auditMissions[$auditMissionId]['audit_setting_mission_status'] : '';
                        $_output = !empty($auditSettings[1]) && !empty($auditSettings[1][$vals]) ? $auditSettings[1][$vals] : '';
                        break;
                    }
                    case 'audit_setting_auditor_company':{
                        $vals = !empty($auditMissions[$auditMissionId]['audit_setting_auditor_company']) ? $auditMissions[$auditMissionId]['audit_setting_auditor_company'] : '';
                        $_output = !empty($auditSettings[1]) && !empty($auditSettings[0][$vals]) ? $auditSettings[0][$vals] : '';
                        break;
                    }
                    case 'mission_manager':{
                        $list = array();
                        if(!empty($auditMissionEmployees[$auditMissionId])){
                            foreach($auditMissionEmployees[$auditMissionId] as $employ => $backup){
                                $manager = !empty($employees[$employ]) ? $employees[$employ] : '';
                                $list[] = sprintf('%s%s', $manager, $backup ? '(B)' : '');
                            }
                        }
                        $_output = implode(', ', $list);
                        break;
                    }
                    case 'audit_setting_recom_priority':{
                        $_output = !empty($auditSettings[1]) && !empty($auditSettings[4][$_output]) ? $auditSettings[4][$_output] : '';
                        break;
                    }
                    case 'audit_setting_recom_status_mission':{
                        $_output = !empty($auditSettings[1]) && !empty($auditSettings[3][$_output]) ? $auditSettings[3][$_output] : '';
                        break;
                    }
                    case 'recom_manager':{
                        $list = array();
                        if(!empty($missionManagerRecoms[$audit['id']])){
                            foreach($missionManagerRecoms[$audit['id']] as $employ => $backup){
                                $manager = !empty($employees[$employ]) ? $employees[$employ] : '';
                                $list[] = sprintf('%s%s', $manager, $backup ? '(B)' : '');
                            }
                        }
                        $_output = implode(', ', $list);
                        break;
                    }
                    case 'implement_date':{
                        $_output = !empty($_output) ? date('d/m/Y', $_output) : '';
                        break;
                    }
                    case 'implement_revised':{
                    $_output = !empty($_output) ? date('d/m/Y', $_output) : '';
                    break;
                }
                case 'date_change_status_mission':{
                    $_output = !empty($_output) ? date('d/m/Y', $_output) : '';
                    break;
                }
                case 'date_change_status_recom':{
                    $_output = !empty($_output) ? date('d/m/Y', $_output) : '';
                    break;
                }
                case 'audit_setting_recom_status_recom':{
                    $_output = !empty($auditSettings[5]) && !empty($auditSettings[5][$_output]) ? $auditSettings[5][$_output] : '';
                    break;
                }
                //case 'author_modify':{
//                    $list = array();
//                    if(!empty($operatorModifications[$audit['id']])){
//                        foreach($operatorModifications[$audit['id']] as $employ => $backup){
//                            $manager = !empty($employees[$employ]) ? $employees[$employ] : '';
//                            $list[] = sprintf('%s%s', $manager, '');
//                        }
//                    }
//                    $_output = implode(', ', $list);
//                    break;
//                }
                case 'attachments':{
                    $list = array();
                    if(!empty($auditRecomFiles[$audit['id']])){
                        $list = $auditRecomFiles[$audit['id']];
                    }
                    $_output = implode(', ', $list);
                    break;
                }
                }
                $PhpExcel->value($colName . $rIndex, (string) $_output);
            }
            $styleArray = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                )
            );
            $PhpExcel->border('A' . $rIndex . ":" . $colName . $rIndex, array(
            'fill' => array(
                
            ),
            'font' => array(
                'size' => 11,
                'bold' => false,
                )
            ));
            $rIndex++; 
        } 
    }
}
// Redirect output to a client?s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="audit_recom_follow_employ' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');