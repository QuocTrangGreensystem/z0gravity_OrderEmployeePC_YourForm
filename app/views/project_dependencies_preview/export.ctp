<?php
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
App::import("Vendor", "str_utility");
$str_utility = new str_utility();

$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . 'project.xls');
$objPHPExcel->setActiveSheetIndex(0);
$activeSheet = & $objPHPExcel->getActiveSheet();
$activeSheet->setTitle('Project Dependencies List');

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
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
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
	// array(
	//     'name' => __('No.', true),
	//     'path' => 'index',
	//     'width' => 30,
	// ),
    array(
        'name' => __('Project', true),
        'path' => 'target_name',
        'width' => 40,
    ),
    array(
        'name' => __('Dependency', true),
        'path' => 'dependency_ids',
        'width' => 60,
    ),
    array(
        'name' => __('Action', true),
        'path' => 'value',
        'width' => 10,
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
    	'size' => '14',
    )
));


$rIndex = 2;
$no = 1;
$colMax = $colName;
// ob_clean();
foreach ($projectDependenciesPreviews as $projectDependency) {
    $PhpExcel->value('A' . $rIndex, $no++);
    $activeSheet->getRowDimension($rIndex)->setRowHeight(21);

    $cIndex = 1;
    foreach ($fieldset as $_fieldset) {
        $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
        $_output = Set::classicExtract($projectDependency, $_fieldset['path']);
        switch ($_fieldset['path']) {
            case 'dependency_ids' :{
                $list_id = json_decode($projectDependency[$_fieldset['path']]);
                if($list_id){
                	$list_deps = array();
                	foreach($list_id as $val){
                		$list_deps[] = $dependencies[$val];
                	}
                	$_output = implode(', ', $list_deps );
                }
                break;
            }
            case 'value' :{
            	$value = $projectDependency[$_fieldset['path']];
				switch ($value) {
					case '1':
						$_output = '←';
						break;
					
					case '2':
						$_output = "→";
						break;
					
					case '3':
						$_output = '↔';
						break;
					
					default:
						$_output = '';
						break;
				}
				// $_output = "Value";
                break;
            }
            default: {
                $_output = $projectDependency[$_fieldset['path']];
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
    ///$PhpExcel->border('A' . $rIndex . ":" . $colName . $rIndex);
    $rIndex++;
}
// exit();
// ob_clean();
// debug($PhpExcel);
// exit;
// Redirect output to a client�s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="projects_dependencies_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;