<?php
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
App::import("Vendor", "str_utility");
$str_utility = new str_utility();
$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . 'project.xls');
$objPHPExcel->setActiveSheetIndex(0);
$activeSheet = & $objPHPExcel->getActiveSheet();
$activeSheet->setTitle('Activity Budget List');
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
     * Set alignment.
     *
     * @return void
     */
    public function alignRight($range) {
        $this->_sheet->getStyle($range)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
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
        'name' => __('Family', true),
        'path' => 'family_id',
        'width' => 30,
    ),
    array(
        'name' => __('Sub Family', true),
        'path' => 'subfamily_id',
        'width' => 20,
    ),
    array(
        'name' => __('Type', true),
        'path' => 'type_id',
        'width' => 15,
    ),
    array(
        'name' => __('Year', true),
        'path' => 'year',
        'width' => 20,
    )
);
$firstYear = strtotime('01-01-'.$year);
$lastYear = strtotime('31-12-'.$year);
$countMonth = 1;
while($firstYear <= $lastYear){
    $fieldset[] = array(
        'name' => __(date('M', $firstYear), true) . '-' . __(date('y', $firstYear), true) . ' (P' . $countMonth . ')',
        'path' => $firstYear,
        'width' => 15,
    );
    $firstYear = mktime(0, 0, 0, date("m", $firstYear)+1, date("d", $firstYear), date("Y", $firstYear));
    $countMonth++;
}

$activeSheet->getColumnDimension('A')->setWidth(10);
//$PhpExcel->align('A');
$PhpExcel->value('A1', __('No.', true));
$cIndex = 1;
foreach ($fieldset as $_fieldset) {
    $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
    if(is_numeric($_fieldset['path']) || $_fieldset['path'] === 'year'){
        $PhpExcel->alignRight($colName);
    }
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
$groupTypes = array('euro', 'md', 'fte', 'ec_euro');
$formats = array('euro' => '&euro;', 'md' => __('M.D', true), 'fte' => __('FTE', true), 'ec_euro' => __('External Cost', true) . ' &euro;');

if(!empty($datas)){
    foreach ($datas as $key) {
        list($family, $sub, $type) = explode('-', $key);

        $PhpExcel->value('A' . $rIndex, $no++);
        $activeSheet->getRowDimension($rIndex)->setRowHeight(21);
        $cIndex = 1;
        foreach($fieldset as $field){
            $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
            $_output = '';
            switch ($field['path']) {
                case 'family_id':{
                    $_output = !empty($families[$family]) ? $families[$family] : '';
                    break;
                }
                case 'subfamily_id':{
                    $_output = !empty($families[$sub]) ? $families[$sub] : '';
                    break;
                }
                case 'type_id':{
                    $_output = !empty($formats[$type]) ? $formats[$type] : '';
                    $tmpChar = html_entity_decode($_output, ENT_QUOTES, 'utf-8');
                    $_output = str_replace('&euro;', '€', $tmpChar);
                    break;
                }
                case 'year':{
                    if( isset($budgets[$key][$year]) ){
                        $activeSheet->getStyle($colName . $rIndex)->getNumberFormat()->setFormatCode('# ##0.00');
                        $_output = $budgets[$key][$year];
                    }
                    break;
                }
                default:
                    if( isset($budgets[$key][$field['path']]) ){
                        $activeSheet->getStyle($colName . $rIndex)->getNumberFormat()->setFormatCode('# ##0.00');
                        $_output = $budgets[$key][$field['path']];
                    }
                break;
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
        'fill' => array(),
        'font' => array(
            'size' => 11,
            'bold' => false,
            )
        ));
        $rIndex++;
    }
    
}
$PhpExcel->alignRight('E2:' . $colName . '2');
$endRow = $rIndex-1;
$objPHPExcel->getActiveSheet()->getStyle('E2:' . 'E'.$endRow)->applyFromArray(
    array(
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'ECEC00')
        )
    )
);
//exit;
// Redirect output to a client?s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="activity_budget_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');