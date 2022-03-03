<?php
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
App::import("Vendor", "str_utility");
$str_utility = new str_utility();


$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . 'project.xls');

$objPHPExcel->setActiveSheetIndex(0);
$activeSheet = & $objPHPExcel->getActiveSheet();
$activeSheet->setTitle('Employees List');



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
        'name' => __('ID', true),
        'path' => 'Employee.id',
        'width' => 10,
    ),
    array(
        'name' => __('Fullname', true),
        'path' => 'Employee.fullname',
        'width' => 40,
    ),
    array(
        'name' => __('Company', true),
        'path' => 'Employee.company_id',
        'width' => 25,
    ),
    array(
        'name' => __('Type of contract', true),
        'path' => 'Employee.contract_type_id',
        'width' => 25,
    ),
    array(
        'name' => __('Role', true),
        'path' => 'Employee.role_id',
        'width' => 30,
    ),
    array(
        'name' => __('Email', true),
        'path' => 'Employee.email',
        'width' => 40,
    ),
    array(
        'name' => __('Work Phone', true),
        'path' => 'Employee.work_phone',
        'width' => 30,
    ),
    array(
        'name' => __('Mobile Phone', true),
        'path' => 'Employee.mobile_phone',
        'width' => 25,
    ),
    array(
        'name' => __('City', true),
        'path' => 'Employee.city_id',
        'width' => 25,
    ),
    array(
        'name' => __('Country', true),
        'path' => 'Employee.country_id',
        'width' => 25,
    )
);

$activeSheet->getColumnDimension('A')->setWidth(10);
$PhpExcel->align('A');
$PhpExcel->value('A2', __('No.', true));

$cIndex = 1;
foreach ($fieldset as $_fieldset) {
    $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
    $PhpExcel->value($colName . '2', __($_fieldset['name'], true));
    $activeSheet->getColumnDimension($colName)->setWidth($_fieldset['width']);
}
$PhpExcel->border('A2:' . $colName . '2', array(
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array('rgb' => '185790')
    ),
    'font' => array(
        'bold' => true,
        'color' => array(
            'rgb' => 'FFFFFF'
    ))));


$rIndex = 3;
$no = 1;
$colMax = $colName;
foreach ($employees as $employee) {
    $PhpExcel->value('A' . $rIndex, $no++);
    $activeSheet->getRowDimension($rIndex)->setRowHeight(21);

    $cIndex = 1;
    foreach ($fieldset as $_fieldset) {
        $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
        $_output = Set::classicExtract($employee, $_fieldset['path']);
        switch ($_fieldset['path']) {
            case 'Employee.contract_type_id' : {
                    $_output = (string) @$contractTypes[$_output];
                    break;
                }
            case 'Employee.country_id' : {
                    $_output = (string) @$countries[$_output];
                    break;
                }
            case 'Employee.city_id' : {
                    $_output = (string) @$cities[$_output];
                    break;
                }
            case 'Employee.company_id' : {
                    $_output = (string) @$companies[$employeeReferences[$employee['Employee']['id']]['company_id']];
                    break;
                }
            case 'Employee.role_id' : {
                    $_output = (string) @$roles[$employeeReferences[$employee['Employee']['id']]['role_id']];
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
    $PhpExcel->border('A' . $rIndex . ":" . $colName . $rIndex);
    $rIndex++;
}
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="employees_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');
$objWriter->save('php://output');
?>
