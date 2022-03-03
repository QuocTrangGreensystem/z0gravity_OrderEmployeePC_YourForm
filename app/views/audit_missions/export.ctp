<?php
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
App::import("Vendor", "str_utility");
$str_utility = new str_utility();
$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . 'project.xls');
$objPHPExcel->setActiveSheetIndex(0);
$activeSheet = & $objPHPExcel->getActiveSheet();
$activeSheet->setTitle('Audit Mission List');
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
        'name' => __('Mission Title', true),
        'path' => 'mission_title',
        'width' => 30,
    ),
    array(
        'name' => __('Mission Number', true),
        'path' => 'mission_number',
        'width' => 20,
    ),
    array(
        'name' => __('Mission Status', true),
        'path' => 'audit_setting_mission_status',
        'width' => 20,
    ),
    array(
        'name' => __('Auditor Company', true),
        'path' => 'audit_setting_auditor_company',
        'width' => 20,
    ),
    array(
        'name' => __('Auditor', true),
        'path' => 'auditor',
        'width' => 20,
    ),
    array(
        'name' => __('Audited Company', true),
        'path' => 'audited_company',
        'width' => 20,
    ),
    array(
        'name' => __('Mission Manager', true),
        'path' => 'mission_manager',
        'width' => 60,
    ),
    array(
        'name' => __('Mission Type', true),
        'path' => 'audit_setting_mission_type',
        'width' => 20,
    ),
    array(
        'name' => __('Mission Validation Date', true),
        'path' => 'mission_validation_date',
        'width' => 40,
    ),
    array(
        'name' => __('Comments', true),
        'path' => 'comment',
        'width' => 20,
    ),
    array(
        'name' => __('Attachments', true),
        'path' => 'attachments',
        'width' => 60,
    ),
    array(
        'name' => __('Mission Closing Date', true),
        'path' => 'mission_closing_date',
        'width' => 40,
    )
);
$activeSheet->getColumnDimension('A')->setWidth(10);
//$PhpExcel->align('A');
$PhpExcel->value('A1', __('No.', true));
$cIndex = 1;
foreach ($fieldset as $_fieldset) {
    $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
    $PhpExcel->value($colName . '1', __($_fieldset['name'], true));
    $activeSheet->getColumnDimension($colName)->setWidth($_fieldset['width']);
}
$PhpExcel->align('A1:' . $colName . '1');
$PhpExcel->border('A1:' . $colName . '1', array(
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
$rIndex = 2;
$no = 1;
$colMax = $colName;
if(!empty($auditMissions)){
    foreach($auditMissions as $auditMission){
        $PhpExcel->value('A' . $rIndex, $no++);
        $activeSheet->getRowDimension($rIndex)->setRowHeight(21);
        $cIndex = 1;
        foreach ($fieldset as $_fieldset) {
            $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
            $_output = Set::classicExtract($auditMission, $_fieldset['path']);
            switch ($_fieldset['path']) {
                case 'audit_setting_mission_status':{
                    $_output = !empty($auditSettings[1]) && !empty($auditSettings[1][$_output]) ? $auditSettings[1][$_output] : '';
                    break;
                }
                case 'audit_setting_auditor_company':{
                    $_output = !empty($auditSettings[0]) && !empty($auditSettings[0][$_output]) ? $auditSettings[0][$_output] : '';
                    break;
                }
                case 'mission_number':{
                    $_output = $_output;//!empty($_output) ? str_replace('.', ',', $_output) : '';
                    break;
                }
                case 'mission_manager':{
                    $list = array();
                    if(!empty($auditMissionEmployees[$auditMission['id']])){
                        foreach($auditMissionEmployees[$auditMission['id']] as $employ => $backup){
                            $manager = !empty($employees[$employ]) ? $employees[$employ] : '';
                            $list[] = sprintf('%s%s', $manager, $backup ? '(B)' : '');
                        }
                    }
                    $_output = implode(', ', $list);
                    break;
                }
                case 'audit_setting_mission_type':{
                    $_output = !empty($auditSettings[2]) && !empty($auditSettings[2][$_output]) ? $auditSettings[2][$_output] : '';
                    break;
                }
                case 'mission_validation_date':{
                    $_output = !empty($_output) ? date('d/m/Y', $_output) : '';
                    break;
                }
                case 'mission_closing_date':{
                    $_output = !empty($_output) ? date('d/m/Y', $_output) : '';
                    break;
                }
                case 'attachments':{
                    $list = array();
                    if(!empty($auditMissionFiles[$auditMission['id']])){
                        $list = $auditMissionFiles[$auditMission['id']];
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
//exit();
// Redirect output to a client?s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="audit_mission_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');