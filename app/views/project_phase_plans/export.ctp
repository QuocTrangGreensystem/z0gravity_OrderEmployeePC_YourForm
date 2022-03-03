<?php

App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
App::import("Vendor", "str_utility");
$str_utility = new str_utility();


$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . 'project.xls');

$objPHPExcel->setActiveSheetIndex(0);
$activeSheet = & $objPHPExcel->getActiveSheet();
$activeSheet->setTitle('Project Phase Plans List');



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

    /**
     * Set cell range value and style.
     *
     * @return void
     */
    public function style($range, $style = array()) {
        $this->_sheet->getStyle($range)->applyFromArray($style);
    }

}

// PhpExcelSet Object
$PhpExcel = new PhpExcelSet($activeSheet);
if($displayParst){
    $_fieldset[] = array(
        'name' => __('Part', true),
        'path' => 'ProjectPart.title',
        'width' => 30,
    );
}
$fieldset = array(
    array(
        'name' => __('Name', true),
        'path' => 'ProjectPhase.name',
        'width' => 30,
    ),
    array(
        'name' => __('Plan start date', true),
        'path' => 'ProjectPhasePlan.phase_planed_start_date',
        'width' => 30,
    ),
    array(
        'name' => __('Plan end date', true),
        'path' => 'ProjectPhasePlan.phase_planed_end_date',
        'width' => 20,
    ),
);
if(!empty($_fieldset)) $fieldset = array_merge($_fieldset, $fieldset);
$optionFieldset = array(
    'kpi' => array(
            'name' => __('KPI', true),
            'path' => 'ProjectPhasePlan.kpi',
            'width' => 10,
        ),
    'planed_duration' => array(
            'name' => __('Duration', true),
            'path' => 'ProjectPhasePlan.planed_duration',
            'width' => 10,
        ),
    'predecessor' => array(
            'name' => __('Predecessor', true),
            'path' => 'ProjectPhasePlan.predecessor',
            'width' => 30,
        ),
    'phase_real_start_date' => array(
            'name' => __('Real start date', true),
            'path' => 'ProjectPhasePlan.phase_real_start_date',
            'width' => 20,
        ),
    'phase_real_end_date' => array(
            'name' => __('Real end date', true),
            'path' => 'ProjectPhasePlan.phase_real_end_date',
            'width' => 20,
        ),
    'phase_status' => array(
            'name' => __('Status', true),
            'path' => 'ProjectPhaseStatus.phase_status',
            'width' => 20,
        ),
    'color' => array(
            'name' => __('Color', true),
            'path' => 'ProjectPhasePlan.color',
            'width' => 10,
        ),
    'ref1' => array(
            'name' => __('Ref 1', true),
            'path' => 'ProjectPhasePlan.ref1',
            'width' => 20,
        ),
    'ref2' => array(
            'name' => __('Ref 2', true),
            'path' => 'ProjectPhasePlan.ref2',
            'width' => 20,
        ),
    'ref3' => array(
            'name' => __('Ref 3', true),
            'path' => 'ProjectPhasePlan.ref3',
            'width' => 20,
        ),
    'ref4' => array(
            'name' => __('Ref 4', true),
            'path' => 'ProjectPhasePlan.ref4',
            'width' => 20,
        ),
    'profile_id' => array(
            'name' => __('Profile', true),
            'path' => 'ProjectPhasePlan.profile_id',
            'width' => 20,
        ),
    'progress' => array(
            'name' => __('% Achieved', true),
            'path' => 'ProjectPhasePlan.progress',
            'width' => 10,
        ),
);
$settings = $this->requestAction('/project_phases/getFields');
foreach($settings as $setting){
    list($key, $show) = explode('|', $setting);
    if( $show == 0 )continue;
    if( $key == 'progress' && !$manuallyAchievement )continue;
    if( $key == 'profile_id' && !$activateProfile )continue;
    if( isset($optionFieldset[$key]) )$fieldset[] = $optionFieldset[$key];
}

$activeSheet->getColumnDimension('A')->setWidth(10);
$PhpExcel->align('A');
$PhpExcel->value('A1', __('#', true));
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
        'size' => '11',
        'color' => array(
            'rgb' => 'FFFFFF'
    ))));

$rIndex = 2;
$no = 1;
$colMax = $colName;
foreach ($data as $projectPhasePlan) {
    $PhpExcel->value('A' . $rIndex, $no++);
    $PhpExcel->style('A'.$rIndex, array(
        'font' => array(
            'bold' => false
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        )
    ));
    $activeSheet->getRowDimension($rIndex)->setRowHeight(21);

    $cIndex = 1;
    foreach ($fieldset as $_fieldset) {
        $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
        $_output = Set::classicExtract($projectPhasePlan, $_fieldset['path']);
        switch ($_fieldset['path']) {
            case 'ProjectPhasePlan.phase_planed_start_date' :
            case 'ProjectPhasePlan.phase_planed_end_date' :
            case 'ProjectPhasePlan.phase_real_start_date' :
            case 'ProjectPhasePlan.phase_real_end_date' : {
                    $_output = (empty($_output) || $_output == '0000-00-00') ? '' : $str_utility->convertToVNDate($_output);
                    break;
                }
            case 'ProjectPhasePlan.predecessor':
                if( isset($predecessors[$_output]) ){
                    $_output = $predecessors[$_output];
                } else $_output = '';
            break;
            case 'ref1':
            case 'ref2':
                if(!$_output)$_output = '';
            break;
            case 'ProjectPhasePlan.kpi' :
                $PhpExcel->border($colName . $rIndex, array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'startcolor' => array('rgb' => $_output)
                    )
                ));
                $_output = '';
            break;
            case 'ProjectPhasePlan.color' :
                // debug($_output); exit;
                $_output = str_replace('#', '', $_output);
                $PhpExcel->border($colName . $rIndex, array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'startcolor' => array('rgb' => $_output)
                    )
                ));
                $_output = '';
            break;
            case 'ProjectPhasePlan.progress' :
                $_output = $_output . ' %';
            break;
        }
        $PhpExcel->value($colName . $rIndex, (string) $_output);
        $PhpExcel->style($colName . $rIndex, array(
            'font' => array(
                'bold' => false
            )
        ));
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
//exit();
// Redirect output to a client�s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="projects_phase_plans_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
