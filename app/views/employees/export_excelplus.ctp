<?php
ob_end_clean();
ob_start();
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
        'name' => __('First name', true),
        'path' => 'Employee.first_name',
        'width' => 20,
    ),
    array(
        'name' => __('Last name', true),
        'path' => 'Employee.last_name',
        'width' => 20,
    ),
    array(
        'name' => __('Email', true),
        'path' => 'Employee.email',
        'width' => 40,
    ),
    array(
        'name' => __('Company', true),
        'path' => 'Employee.company_id',
        'width' => 25,
    ),

    array(
        'name' => __('Role', true),
        'path' => 'Employee.role_id',
        'width' => 30,
    ),
    array(
        'name' => __('Profit center ', true),
        'path' => 'listNameProfitCenters.employee_id',
        'width' => 30,
    ),
    array(
        'name' => __('Skill', true),
        'path' => 'listNameFunction.employee_id',
        'width' => 30,
    ),
    array(
        'name' => __('Start date', true),
        'path' => 'Employee.start_date',
        'width' => 15,
    ),
    array(
        'name' => __('End date', true),
        'path' => 'Employee.end_date',
        'width' => 15,
    ),
    array(
        'name' => __('Average Daily Rate', true),
        'path' => 'Employee.tjm',
        'width' => 30,
    ),
    array(
        'name' => __('ID', true),
        'path' => 'Employee.code_id',
        'width' => 10,
    ),
    array(
        'name' => __d(sprintf($_domain, 'Resource'), 'ID2', true),
        'path' => 'Employee.identifiant',
        'width' => 10,
    ),
    array(
        'name' => __d(sprintf($_domain, 'Resource'), 'ID3', true),
        'path' => 'Employee.id3',
        'width' => 10,
    ),
    array(
        'name' => __d(sprintf($_domain, 'Resource'), 'ID4', true),
        'path' => 'Employee.id4',
        'width' => 10,
    ),
    array(
        'name' => __d(sprintf($_domain, 'Resource'), 'ID5', true),
        'path' => 'Employee.id5',
        'width' => 10,
    ),
    array(
        'name' => __d(sprintf($_domain, 'Resource'), 'ID6', true),
        'path' => 'Employee.id6',
        'width' => 10,
    ),
    array(
        'name' => __('Actif', true),
        'path' => 'Employee.actif',
        'width' => 10,
    ),
    array(
        'name' => __('External', true),
        'path' => 'Employee.external',
        'width' => 10,
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
        'name' => __('Home Phone', true),
        'path' => 'Employee.home_phone',
        'width' => 25,
    ),
    array(
        'name' => __('Fax number', true),
        'path' => 'Employee.fax',
        'width' => 25,
    ),
    array(
        'name' => __('City', true),
        'path' => 'Employee.city_id',
        'width' => 25,
    ),
    array(
        'name' => __('Post code', true),
        'path' => 'Employee.post_code',
        'width' => 25,
    ),
     array(
        'name' => __('Address', true),
        'path' => 'Employee.address',
        'width' => 25,
    ),
    array(
        'name' => __('Country', true),
        'path' => 'Employee.country_id',
        'width' => 25,
    ),
    array(
        'name' => __('Type of contract', true),
        'path' => 'Employee.contract_type_id',
        'width' => 25,
    ),
    array(
        'name' => __('Capacity/Year', true),
        'path' => 'Employee.capacity_by_year',
        'width' => 25,
    ),
);

$activeSheet->getColumnDimension('A')->setWidth(10);
$PhpExcel->align('A');
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
        'startcolor' => array('rgb' => '185790')
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
                    $_output = __((string) @$roles[$employeeReferences[$employee['Employee']['id']]['role_id']], true);
                    break;
                }
            case 'listNameProfitCenters.employee_id':{
                $_output = (string)@$listNameProfitCenters[$employee['Employee']['id']];
                break;
            }
            case 'listNameFunction.employee_id' :{
                if(!empty($listNameFunction[$employee['Employee']['id']])){
                    $names_fucntions = array();
                    foreach($listNameFunction[$employee['Employee']['id']] as $name_function){
                        if( !$name_function )continue;
                        $names_fucntions[] = $name_function;
                    }
                    $names_fucntions = !empty($names_fucntions) ? join(',',$names_fucntions) : '';
                    $_output = (string)@$names_fucntions;
                }
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
    'fill' => array(

    ),
    'font' => array(
        'size' => 11,
        'bold' => false,
        )
    ));
    //$PhpExcel->border('A' . $rIndex . ":" . $colName . $rIndex);
    $rIndex++;
}


//exit();
// Redirect output to a client?s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="employees_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
?>
