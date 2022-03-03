<?php
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
App::import("Vendor", "str_utility");
$str_utility = new str_utility();

$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . 'project.xls');
$objPHPExcel->setActiveSheetIndex(0);
$activeSheet = & $objPHPExcel->getActiveSheet();
$activeSheet->setTitle('Project Milestones List');

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
$f[] = array(
    'name' => __('Part', true),
    'path' => 'ProjectMilestone.part_id',
    'width' => 30,
);
$fieldset = array(
    array(
        'name' => __('Milestone', true),
        'path' => 'ProjectMilestone.project_milestone',
        'width' => 30,
    ),
    array(
        'name' => __('Milestone date', true),
        'path' => 'ProjectMilestone.milestone_date',
        'width' => 30,
    ),
    array(
        'name' => __('Effective date', true),
        'path' => 'ProjectMilestone.effective_date',
        'width' => 30,
    ),
);
$listkey = array();
if(!empty($listAlert)){
    foreach ($listAlert as $_id => $_name) {
        $num = $numberAlert[$_id];
        $listkey[$_id] = 'alert_' . $_id;
        $fieldset[] = array(
            'name' => $_name . __(' D-', true) . $num,
            'path' => 'ProjectMilestone.alert_' . $_id,
            'width' => 30,
        );
    }
}
$fieldset[] = array(
    'name' => __('Validated', true),
    'path' => 'ProjectMilestone.validated',
    'width' => 20,
);
if($displayParst){
    $fieldset = array_merge($f, $fieldset);
}
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
		'size' => 11,
        'bold' => true,
        'color' => array(
            'rgb' => 'FFFFFF'
		)
	),
	'alignment' => array(
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
	),

));

$rIndex = 2;
$no = 1;
$colMax = $colName;
foreach ($projectMilestones as $projectMilestone) {
    $PhpExcel->value('A' . $rIndex, $no++);
    $activeSheet->getRowDimension($rIndex)->setRowHeight(21);

    $cIndex = 1;
    foreach ($fieldset as $_fieldset) {
        $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
        $_output = Set::classicExtract($projectMilestone, $_fieldset['path']);
        switch ($_fieldset['path']) {
            case 'ProjectMilestone.milestone_date' :{
                $_output = (empty($_output) || $_output == '0000-00-00') ? '' : $str_utility->convertToVNDate($_output);
                break;
            }
            case 'ProjectMilestone.validated' :{
                $_output = $_output ? __('Yes',true) : __('No',true);
                break;
            }
            case 'ProjectMilestone.part_id' :{
                $_output = ( !empty($_output) ) ? $partName[$_output] : '';
                break;
            }
            case 'ProjectMilestone.effective_date' :{
                $_output = ( !empty($_output) ) ? date('d-m-Y', $_output) : '';
                break;
            }
            default: {
                foreach ($listkey as $key => $value) {
                    if($_fieldset['path'] == 'ProjectMilestone.' . $value){
                        $date = $str_utility->convertToVNDate($projectMilestone['ProjectMilestone']['milestone_date']);
                        $num = $numberAlert[$key];
                        $t = '-' . $num . ' day';
                        $_date = date('d-m-Y', strtotime($t, strtotime($date)));
                        $_output = $_date;
                    }
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
		'fill' => array(),
		'font' => array(
			'size' => 11,
			'bold' => false
		),
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
		)
	));
    ///$PhpExcel->border('A' . $rIndex . ":" . $colName . $rIndex);
    $rIndex++;
}
//exit();
// Redirect output to a client?s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="projects_milestones_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
