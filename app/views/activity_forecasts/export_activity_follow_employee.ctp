<?php
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
App::import("Vendor", "str_utility");
$str_utility = new str_utility();
$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = !empty($fileExportModuleActivity) ? $objReader->load(SHARED . $employeeLoginName . DS . $fileExportModuleActivity) : $objReader->load(TEMPLATES . 'project.xls');
$objPHPExcel->setActiveSheetIndex(0);
$activeSheet = &$objPHPExcel->getActiveSheet();
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
if(empty($fileExportModuleActivity)){
    $colNameResources = PHPExcel_Cell::stringFromColumnIndex($countResources);
    $colNameAbsenceStarts = PHPExcel_Cell::stringFromColumnIndex($countResources+1);
    $colNameAbsenceEnds = PHPExcel_Cell::stringFromColumnIndex($countResources + $countAbsences);
    $colNameProjectStarts = PHPExcel_Cell::stringFromColumnIndex($countResources + $countAbsences+1);
    $colNameProjectEnds = PHPExcel_Cell::stringFromColumnIndex($countResources + $countAbsences + $countProjects);
    $colNamePhaseStarts = PHPExcel_Cell::stringFromColumnIndex($countResources + $countAbsences+1);
    $colNamePhaseEnds = PHPExcel_Cell::stringFromColumnIndex($countResources + $countAbsences + $countPhases);
    $colNameTaskStarts = PHPExcel_Cell::stringFromColumnIndex($countResources + $countAbsences + $countPhases + 1);
    $colNameTaskEnds = PHPExcel_Cell::stringFromColumnIndex($countResources + $countAbsences + $countPhases + $countTasks);
    $colNameQuantity = PHPExcel_Cell::stringFromColumnIndex($countResources + $countAbsences + $countProjects + $countQuantity);
    $colNameDate = PHPExcel_Cell::stringFromColumnIndex($countResources + $countAbsences + $countProjects + $countQuantity + $countDate);
    $colNameValid = PHPExcel_Cell::stringFromColumnIndex($countResources + $countAbsences + $countProjects + $countQuantity + $countDate + $countValid);
    $colNameExtrac = PHPExcel_Cell::stringFromColumnIndex($countResources + $countAbsences + $countProjects + $countQuantity + $countDate + $countValid + $countExtrac);
    
    $activeSheet->getColumnDimension('A')->setWidth(10);
    $PhpExcel->align('A');
    $activeSheet->mergeCells('A' . (1) . ':'.$colNameResources . (1));
    $activeSheet->mergeCells('A' . (1) . ':A2');
    $PhpExcel->value('A1', __('Resources', true));
    $PhpExcel->border('A' . (1) . ':'.$colNameResources . (1), array(
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array('rgb' => '60497A')
        ),
        'font' => array(
            'bold' => true,
            'color' => array(
                'rgb' => 'FFFFFF'
        ))));
    if($countAbsences != 0){
        $PhpExcel->align($colNameAbsenceStarts . (1));
        $activeSheet->mergeCells($colNameAbsenceStarts . (1) . ':'.$colNameAbsenceEnds . (1));
        $PhpExcel->value($colNameAbsenceStarts . (1), __('Activity/Absence', true));
        $PhpExcel->border($colNameAbsenceStarts . (1) . ':'.$colNameAbsenceEnds . (1), array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => '76933C')
            ),
            'font' => array(
                'bold' => true,
                'color' => array(
                    'rgb' => 'FFFFFF'
            ))));
        $PhpExcel->align($colNameAbsenceStarts . (2));
        $activeSheet->mergeCells($colNameAbsenceStarts . (2) . ':'.$colNameAbsenceEnds . (2));
        $PhpExcel->value($colNameAbsenceStarts . (2), __('Activity/Absence', true));
        $PhpExcel->border($colNameAbsenceStarts . (2) . ':'.$colNameAbsenceEnds . (2), array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => '16365C')
            ),
            'font' => array(
                'bold' => true,
                'color' => array(
                    'rgb' => 'FFFFFF'
            ))));
    }
    if($countProjects != 0){
        if($countPhases != 0){
            $PhpExcel->align($colNamePhaseStarts . (1));
            $activeSheet->mergeCells($colNamePhaseStarts . (1) . ':'.$colNamePhaseEnds . (1));
            $PhpExcel->value($colNamePhaseStarts . (1), __('Phase', true));
            $PhpExcel->border($colNamePhaseStarts . (1) . ':'.$colNamePhaseEnds . (1), array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('rgb' => '538DD5')
                ),
                'font' => array(
                    'bold' => true,
                    'color' => array(
                        'rgb' => 'FFFFFF'
                ))));
        }
        if($countTasks != 0){
            $PhpExcel->align($colNameTaskStarts . (1));
            $activeSheet->mergeCells($colNameTaskStarts . (1) . ':'.$colNameTaskEnds . (1));
            $PhpExcel->value($colNameTaskStarts . (1), __('Task', true));
            $PhpExcel->border($colNameTaskStarts . (1) . ':'.$colNameTaskEnds . (1), array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('rgb' => '538DD5')
                ),
                'font' => array(
                    'bold' => true,
                    'color' => array(
                        'rgb' => 'FFFFFF'
                ))));
        }
        $PhpExcel->align($colNameProjectStarts . (2));
        $activeSheet->mergeCells($colNameProjectStarts . (2) . ':'.$colNameProjectEnds . (2));
        $PhpExcel->value($colNameProjectStarts . (2), __('Project', true));
        $PhpExcel->border($colNameProjectStarts . (2) . ':'.$colNameProjectEnds . (2), array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => '16365C')
            ),
            'font' => array(
                'bold' => true,
                'color' => array(
                    'rgb' => 'FFFFFF'
            ))));
    }
    if($countQuantity != 0){
        $PhpExcel->align($colNameQuantity . (1));
        $activeSheet->mergeCells($colNameQuantity . (1) . ':'.$colNameQuantity . (2));
        $PhpExcel->value($colNameQuantity . (1), __('Quantity', true));
        $PhpExcel->border($colNameQuantity . (1) . ':'.$colNameQuantity . (2), array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'E26B0A')
            ),
            'font' => array(
                'bold' => true,
                'color' => array(
                    'rgb' => 'FFFFFF'
            ))));
    }
    if($countDate != 0){
        $PhpExcel->align($colNameDate . (1));
        $activeSheet->mergeCells($colNameDate . (1) . ':'.$colNameDate . (2));
        $PhpExcel->value($colNameDate . (1), __('Date activity/absence', true));
        $PhpExcel->border($colNameDate . (1) . ':'.$colNameDate . (2), array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'E26B0A')
            ),
            'font' => array(
                'bold' => true,
                'color' => array(
                    'rgb' => 'FFFFFF'
            ))));
    }
    if($countValid != 0){
        $PhpExcel->align($colNameValid . (1));
        $activeSheet->mergeCells($colNameValid . (1) . ':'.$colNameValid . (2));
        $PhpExcel->value($colNameValid . (1), __('Validation date', true));
        $PhpExcel->border($colNameValid . (1) . ':'.$colNameValid . (2), array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'E26B0A')
            ),
            'font' => array(
                'bold' => true,
                'color' => array(
                    'rgb' => 'FFFFFF'
            ))));
    }
    if($countExtrac != 0){
        $PhpExcel->align($colNameExtrac . (1));
        $activeSheet->mergeCells($colNameExtrac . (1) . ':'.$colNameExtrac . (2));
        $PhpExcel->value($colNameExtrac . (1), __('Extraction date', true));
        $PhpExcel->border($colNameExtrac . (1) . ':'.$colNameExtrac . (2), array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'E26B0A')
            ),
            'font' => array(
                'bold' => true,
                'color' => array(
                    'rgb' => 'FFFFFF'
            ))));
    }
    $PhpExcel->value('A3', __('No.', true));
    $cIndex = 1;
    foreach ($fieldset as $_fieldset) {
        $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
        $PhpExcel->value($colName . '3', __($_fieldset['name'], true));
        $activeSheet->getColumnDimension($colName)->setWidth($_fieldset['width']);
    }
    $PhpExcel->align('A3:' . $colName . '3');
    $PhpExcel->border('A3:' . $colName . '3', array(
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
$rIndex = !empty($fileExportModuleActivity) ? $totalRecordHasExport : 4;
$no = !empty($fileExportModuleActivity) ? $totalRecordHasExport - 3 : 1;
//$colMax = $colName;
//debug($fieldset); exit;
foreach ($employees as $employee) {
    /**
     * Neu co holiday thi export holiday cho employee nay
     */
    if(!empty($holidays)){
        foreach($holidays as $date => $value){
            $PhpExcel->value('A' . $rIndex, $no++);
            $activeSheet->getRowDimension($rIndex)->setRowHeight(21);
            $cIndex = 1;
            foreach ($fieldset as $_fieldset) {
                $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
                switch ($_fieldset['path']) {
                    case 'first_name' : {
                            $_output = !empty($employee['Employee']['first_name']) ? $employee['Employee']['first_name'] : '';
                            break;
                        }
                    case 'last_name' : {
                            $_output = !empty($employee['Employee']['last_name']) ? $employee['Employee']['last_name'] : '';
                            break;
                        }
                    case 'profit_center' : {
                            $pcId = !empty($employee['ProjectEmployeeProfitFunctionRefer'][0]['profit_center_id']) ? $employee['ProjectEmployeeProfitFunctionRefer'][0]['profit_center_id'] : '';
                            $_output = !empty($profitCenters[$pcId]) ? $profitCenters[$pcId] : '';
                            break;
                        }
                    case 'id1' : {
                            $_output = !empty($employee['Employee']['code_id']) ? $employee['Employee']['code_id'] : '';
                            break;
                        }
                    case 'id2' : {
                            $_output = !empty($employee['Employee']['identifiant']) ? $employee['Employee']['identifiant'] : '';
                            break;
                        }
                    case 'id3' : {
                            $_output = !empty($employee['Employee']['id3']) ? $employee['Employee']['id3'] : '';
                            break;
                        }
                    case 'id4' : {
                            $_output = !empty($employee['Employee']['id4']) ? $employee['Employee']['id4'] : '';
                            break;
                        }
                    case 'id5' : {
                            $_output = !empty($employee['Employee']['id5']) ? $employee['Employee']['id5'] : '';
                            break;
                        }
                    case 'id6' : {
                            $_output = !empty($employee['Employee']['id6']) ? $employee['Employee']['id6'] : '';
                            break;
                        }
                    case 'code_1' : {
                            $_output = '';
                            break;
                        }
                    case 'code_2' : {
                            $_output = '';
                            break;
                        }
                    case 'code_3' : {
                            $_output = '';
                            break;
                        }
                    case 'code_4' : {
                            $_output = '';
                            break;
                        }
                    case 'code_5' : {
                            $_output = '';
                            break;
                        }
                    case 'code_6' : {
                            $_output = '';
                            break;
                        }
                    case 'ref_1' : {
                            $_output = '';
                            break;
                        }
                    case 'ref_2' : {
                            $_output = '';
                            break;
                        }
                    case 'ref_3' : {
                            $_output = '';
                            break;
                        }
                    case 'ref_4' : {
                            $_output = '';
                            break;
                        }
                    case 'quantity' : {
                            $_output = !empty($value) ? $value : '';
                            break;
                        }
                    case 'date_activity_absence' : {
                            $_output = !empty($date) ? date('d-m-Y', $date) : '';
                            break;
                        }
                    case 'validation_date' : {
                            $_output = '';
                            break;
                        }
                    case 'extraction_date' : {
                            $_output = date('d-m-Y', time());
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
    }
    /**
     * Neu co absence thi export ra
     */
    if(!empty($absencesForEmployees[$employee['Employee']['id']])){
        foreach($absencesForEmployees[$employee['Employee']['id']] as $date => $value){
            $PhpExcel->value('A' . $rIndex, $no++);
            $activeSheet->getRowDimension($rIndex)->setRowHeight(21);
            $cIndex = 1;
            foreach ($fieldset as $_fieldset) {
                $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
                $absenceIdOfEmploy = !empty($listAbsenceCodes[$employee['Employee']['id']][$date]) ? $listAbsenceCodes[$employee['Employee']['id']][$date] : '';
                switch ($_fieldset['path']) {
                    case 'first_name' : {
                            $_output = !empty($employee['Employee']['first_name']) ? $employee['Employee']['first_name'] : '';
                            break;
                        }
                    case 'last_name' : {
                            $_output = !empty($employee['Employee']['last_name']) ? $employee['Employee']['last_name'] : '';
                            break;
                        }
                    case 'profit_center' : {
                            $pcId = !empty($employee['ProjectEmployeeProfitFunctionRefer'][0]['profit_center_id']) ? $employee['ProjectEmployeeProfitFunctionRefer'][0]['profit_center_id'] : '';
                            $_output = !empty($profitCenters[$pcId]) ? $profitCenters[$pcId] : '';
                            break;
                        }
                    case 'id1' : {
                            $_output = !empty($employee['Employee']['code_id']) ? $employee['Employee']['code_id'] : '';
                            break;
                        }
                    case 'id2' : {
                            $_output = !empty($employee['Employee']['identifiant']) ? $employee['Employee']['identifiant'] : '';
                            break;
                        }
                    case 'id3' : {
                            $_output = !empty($employee['Employee']['id3']) ? $employee['Employee']['id3'] : '';
                            break;
                        }
                    case 'id4' : {
                            $_output = !empty($employee['Employee']['id4']) ? $employee['Employee']['id4'] : '';
                            break;
                        }
                    case 'id5' : {
                            $_output = !empty($employee['Employee']['id5']) ? $employee['Employee']['id5'] : '';
                            break;
                        }
                    case 'id6' : {
                            $_output = !empty($employee['Employee']['id6']) ? $employee['Employee']['id6'] : '';
                            break;
                        }
                    case 'code_1' : {
                            $_output = !empty($absences[$absenceIdOfEmploy]['code1']) ? $absences[$absenceIdOfEmploy]['code1'] : '';
                            break;
                        }
                    case 'code_2' : {
                            $_output = !empty($absences[$absenceIdOfEmploy]['code2']) ? $absences[$absenceIdOfEmploy]['code2'] : '';
                            break;
                        }
                    case 'code_3' : {
                            $_output = !empty($absences[$absenceIdOfEmploy]['code3']) ? $absences[$absenceIdOfEmploy]['code3'] : '';
                            break;
                        }
                    case 'code_4' : {
                            $_output = '';
                            break;
                        }
                    case 'code_5' : {
                            $_output = '';
                            break;
                        }
                    case 'code_6' : {
                            $_output = '';
                            break;
                        }
                    case 'ref_1' : {
                            $_output = !empty($absences[$absenceIdOfEmploy]['code1']) ? $absences[$absenceIdOfEmploy]['code1'] : '';
                            break;
                        }
                    case 'ref_2' : {
                            $_output = !empty($absences[$absenceIdOfEmploy]['code2']) ? $absences[$absenceIdOfEmploy]['code2'] : '';
                            break;
                        }
                    case 'ref_3' : {
                            $_output = '';
                            break;
                        }
                    case 'ref_4' : {
                            $_output = '';
                            break;
                        }
                    case 'quantity' : {
                            $_output = !empty($value) ? array_shift($value) : '';
                            break;
                        }
                    case 'date_activity_absence' : {
                            $_output = !empty($date) ? date('d-m-Y', $date) : '';
                            break;
                        }
                    case 'validation_date' : {
                            $_output = !empty($absencesDates[$employee['Employee']['id']][$date]) ? date('d-m-Y', $absencesDates[$employee['Employee']['id']][$date]) : '';
                            break;
                        }
                    case 'extraction_date' : {
                            $_output = date('d-m-Y', time());
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
    }
    /**
     * Neu co task thi export ra
     */
    if(!empty($activityRequests[$employee['Employee']['id']])){
        foreach($activityRequests[$employee['Employee']['id']] as $key => $value){
            $PhpExcel->value('A' . $rIndex, $no++);
            $activeSheet->getRowDimension($rIndex)->setRowHeight(21);
            $cIndex = 1;
            foreach ($fieldset as $_fieldset) {
                $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
                /**
                 * Lay phase id cua task
                 */
                $PTaskId = !empty($ATaskLinkPTasks[$value['task_id']]) ? $ATaskLinkPTasks[$value['task_id']] : '';
                $PhasePlanId = !empty($projectTasks[$PTaskId]) ? $projectTasks[$PTaskId] : '';
                /**
                 * Lay activity id cua task
                 */
                $activityId = 0;
                if(!empty($value['task_id'])){
                    $activityId = !empty($ATaskOfActivity[$value['task_id']]) ? $ATaskOfActivity[$value['task_id']] : 0;
                } else {
                    $activityId = $value['activity_id'];
                }
                switch ($_fieldset['path']) {
                    case 'first_name' : {
                            $_output = !empty($employee['Employee']['first_name']) ? $employee['Employee']['first_name'] : '';
                            break;
                        }
                    case 'last_name' : {
                            $_output = !empty($employee['Employee']['last_name']) ? $employee['Employee']['last_name'] : '';
                            break;
                        }
                    case 'profit_center' : {
                            $pcId = !empty($employee['ProjectEmployeeProfitFunctionRefer'][0]['profit_center_id']) ? $employee['ProjectEmployeeProfitFunctionRefer'][0]['profit_center_id'] : '';
                            $_output = !empty($profitCenters[$pcId]) ? $profitCenters[$pcId] : '';
                            break;
                        }
                    case 'id1' : {
                            $_output = !empty($employee['Employee']['code_id']) ? $employee['Employee']['code_id'] : '';
                            break;
                        }
                    case 'id2' : {
                            $_output = !empty($employee['Employee']['identifiant']) ? $employee['Employee']['identifiant'] : '';
                            break;
                        }
                    case 'id3' : {
                            $_output = !empty($employee['Employee']['id3']) ? $employee['Employee']['id3'] : '';
                            break;
                        }
                    case 'id4' : {
                            $_output = !empty($employee['Employee']['id4']) ? $employee['Employee']['id4'] : '';
                            break;
                        }
                    case 'id5' : {
                            $_output = !empty($employee['Employee']['id5']) ? $employee['Employee']['id5'] : '';
                            break;
                        }
                    case 'id6' : {
                            $_output = !empty($employee['Employee']['id6']) ? $employee['Employee']['id6'] : '';
                            break;
                        }
                    case 'family' : {
                            $familyId = !empty($activities[$activityId]['family_id']) ? $activities[$activityId]['family_id'] : '';
                            $_output = !empty($families[$familyId]) ? $families[$familyId] : '';
                            break;
                        }
                    case 'sub_family' : {
                            $familyId = !empty($activities[$activityId]['subfamily_id']) ? $activities[$activityId]['subfamily_id'] : '';
                            $_output = !empty($families[$familyId]) ? $families[$familyId] : '';
                            break;
                        }
                    case 'code_1' : {
                            $_output = !empty($activities[$activityId]['code1']) ? $activities[$activityId]['code1'] : '';
                            break;
                        }
                    case 'code_2' : {
                            $_output = !empty($activities[$activityId]['code2']) ? $activities[$activityId]['code2'] : '';
                            break;
                        }
                    case 'code_3' : {
                            $_output = !empty($activities[$activityId]['code3']) ? $activities[$activityId]['code3'] : '';
                            break;
                        }
                    case 'code_4' : {
                            $_output = !empty($activities[$activityId]['code4']) ? $activities[$activityId]['code4'] : '';
                            break;
                        }
                    case 'code_5' : {
                            $_output = !empty($activities[$activityId]['code5']) ? $activities[$activityId]['code5'] : '';
                            break;
                        }
                    case 'code_6' : {
                            $_output = !empty($activities[$activityId]['code6']) ? $activities[$activityId]['code6'] : '';
                            break;
                        }
                    case 'ref_1' : {
                            $_output = !empty($phasePlans[$PhasePlanId]['ref1']) ? $phasePlans[$PhasePlanId]['ref1'] : '';
                            break;
                        }
                    case 'ref_2' : {
                            $_output = !empty($phasePlans[$PhasePlanId]['ref2']) ? $phasePlans[$PhasePlanId]['ref2'] : '';
                            break;
                        }
                    case 'ref_3' : {
                            $_output = !empty($phasePlans[$PhasePlanId]['ref3']) ? $phasePlans[$PhasePlanId]['ref3'] : '';
                            break;
                        }
                    case 'ref_4' : {
                            $_output = !empty($phasePlans[$PhasePlanId]['ref4']) ? $phasePlans[$PhasePlanId]['ref4'] : '';
                            break;
                        }
                    case 'quantity' : {
                            $_output = !empty($value['value']) ? $value['value'] : '';
                            break;
                        }
                    case 'date_activity_absence' : {
                            $_output = !empty($value['date']) ? date('d-m-Y', $value['date']) : '';
                            break;
                        }
                    case 'validation_date' : {
                            $valDate = '';
                            if(!empty($value['status']) && $value['status'] == 2){
                                $confirmForEmploy = !empty($listRequestConfirms[$employee['Employee']['id']]) ? $listRequestConfirms[$employee['Employee']['id']] : array();
                                if(!empty($confirmForEmploy) && !empty($confirmForEmploy[$value['date']])){
                                    $valDate = date('d-m-Y', $confirmForEmploy[$value['date']]);
                                }
                            }
                            $_output = $valDate;
                            break;
                        }
                    case 'extraction_date' : {
                            $_output = date('d-m-Y', time());
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
    }
}
$fileName = !empty($fileExportModuleActivity) ? $fileExportModuleActivity : 'activity_export_' . date('H_i_s_d_m_Y') . '.xls';
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save(SHARED . $employeeLoginName . DS . $fileName);
$_SESSION['totalRecordHasExport'] = $rIndex;
$_SESSION['fileExportModuleActivity'] = $fileName;
//$this->Session->write('totalRecordHasExport', $rIndex);
//$this->Session->write('fileExportModuleActivity', $fileName);
$results = array(
    'totalRecord' => $no - 1,
    'urlDownload' => 'https://' . $_SERVER['SERVER_NAME'] . '/app/webroot/shared/' . $employeeLoginName . '/' . $fileName
);
echo json_encode($results);
exit;