<?php
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
App::import("Vendor", "str_utility");
$str_utility = new str_utility();
/**
 * Chuyen fil CSV sang file excelt de export
 */
$objReaderCSV = PHPExcel_IOFactory::createReader('CSV');
$objReaderCSV->setInputEncoding('UTF-8');
$objPHPExcelCSV = $objReaderCSV->load($savePart);
$objWriterCSV = PHPExcel_IOFactory::createWriter($objPHPExcelCSV, 'Excel5');
$fileNameContent = 'tmp_content_file_export_' . $employeeLoginName . '.xls';
$partContent = SHARED . $employeeLoginName . DS . $fileNameContent;
$objWriterCSV->save($partContent);
/**
 * Ghi tieu de
 */
$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load($partContent);
$objPHPExcel->setActiveSheetIndex(0);
$activeSheet = &$objPHPExcel->getActiveSheet();
$activeSheet->insertNewRowBefore(1, 1);
$activeSheet->setTitle('Activity Export');
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
$groupResources = array('First name', 'Last name', 'Profit Center', 'ID1', 'ID2', 'ID3', 'ID4', 'ID5', 'ID6');
$groupActivities = array('Family', 'Sub family', 'Code 1', 'Code 2', 'Code 3', 'Code 4', 'Code 5', 'Code 6');
$groupProjects = array('REF 1', 'REF 2', 'REF 3', 'REF 4');
$groupPhases = array('REF 1', 'REF 2');
$groupTasks = array('REF 3', 'REF 4');

$fieldset = array();
$countResources = $countAbsences = $countProjects = $countPhases = $countTasks = $countQuantity = $countDate = $countValid = $countExtrac = 0;
if(!empty($activityExports)){
    foreach($activityExports as $key => $name){
        $fieldset[] = array(
            'name' => $name,
            'path' => strtolower(trim(str_replace(array(' ', '/'), '_', $key))),
            'width' => ($key == 'Profit Center') ? 55 : 25,
        );
        if(in_array($key, $groupResources)){
            $countResources++;
        }
        if(in_array($key, $groupActivities)){
            $countAbsences++;
        }
        if(in_array($key, $groupProjects)){
            $countProjects++;
        }
        if(in_array($key, $groupPhases)){
            $countPhases++;
        }
        if(in_array($key, $groupTasks)){
            $countTasks++;
        }
        if($key === 'Quantity'){
            $countQuantity = 1;
        }
        if($key === 'Date activity/absence'){
            $countDate = 1;
        }
        if($key === 'Validation date'){
            $countValid = 1;
        }
        if($key === 'Extraction date'){
            $countExtrac = 1;
        }
    }
}
$countResources = ($countResources != 0) ? $countResources - 1 : 0;
if(empty($fileExportModuleActivity)){
    $cIndex = 0;
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
        ))));
}
/**
 * Ghi file export tam vao server: file chi co header
 */
$fileNameHeader = 'activity_export_' . date('H_i_s_d_m_Y') . '.xls';
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$partHeader = SHARED . $employeeLoginName . DS . $fileNameHeader;
$objWriter->save($partHeader);
echo json_encode('https://' . $_SERVER['SERVER_NAME'] . '/app/webroot/shared/' . $employeeLoginName . '/' . $fileNameHeader);
exit;