<?php
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
App::import("Vendor", "str_utility");
$str_utility = new str_utility();
$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . 'project.xls');
$objPHPExcel->setActiveSheetIndex(0);
$activeSheet = & $objPHPExcel->getActiveSheet();
$activeSheet->setTitle('Vision Task');
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
        $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
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
$activeSheet->getColumnDimension('A')->setWidth(10);
// $PhpExcel->align('A');
$PhpExcel->value('A2', __('No.', true));

$cIndex = 1;
$activeSheet->getRowDimension(2)->setRowHeight(21);
foreach ($fieldset as $_key => $_fieldset) {
    $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
    switch ($_key) {
        case 'Program':
            $width = 20;
            break;
        case 'Sub program':
            $width = 30;
            break;
        case 'Project name':
            $width = 50;
            break;
        case 'Lot':
            $width = 20;
            break;
        case 'Phase':
            $width = 30;
            break;
        case 'Task':
            $width = 50;
            break;
        case 'Status':
            $width = 20;
            break;
        case 'Milestone':
            $width = 30;
            break;
        case 'Priority':
            $width = 30;
            break;
        case 'Assigned':
            $width = 40;
            break;
        case 'Start':
            $width = 20;
            break;
        case 'End':
            $width = 20;
            break;
        case 'Workload':
            $width = 10;
            break;
        case 'Consume':
            $width = 10;
            break;
        case 'Code project':
            $width = 20;
            break;
        case 'Code project 1':
            $width = 20;
            break;
        default:
            $width = 30;
            break;
        }
    $PhpExcel->value($colName . '2', __($_fieldset, true));
    $activeSheet->getColumnDimension($colName)->setWidth($width);
}
$PhpExcel->border('A2:' . $colName . '2', array(
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array('rgb' => '5fa1c4')
    ),
    'font' => array(
        'size' => 11,
        'bold' => true,
        'color' => array(
            'rgb' => 'FFFFFF'
    ))));

$rIndex = 3;
$no = 1;
$colMax = $colName;
foreach ($datas as $data) {
    $PhpExcel->value('A' . $rIndex, $no++);
    $activeSheet->getRowDimension($rIndex)->setRowHeight(21);

    $cIndex = 1;
    foreach ($fieldset as $_key => $_fieldset) {
        $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
        switch ($_key) {
            case 'Program':
                $_output = !empty($data['amr_program']) ? $data['amr_program'] : '';
                break;
            case 'Sub program':
                $_output = !empty($data['sub_amr_program']) ? $data['sub_amr_program'] : '';
                break;
            case 'Project name':
                $_output = !empty($data['project_name']) ? $data['project_name'] : '';
                break;
            case 'Lot':
                $_output = !empty($data['part_name']) ? $data['part_name'] : '';
                break;
            case 'Phase':
                $_output = !empty($data['phase_name']) ? $data['phase_name'] : '';
                break;
            case 'Task':
                $_output = !empty($data['task_title']) ? $data['task_title'] : '';
                $_output = strip_tags($_output);
                break;
            case 'Status':
                $_output = !empty($data['status']) ? $data['status'] : '';
                break;
            case 'Priority':
                $_output = !empty($data['priority']) ? $data['priority'] : '';
                break;
            case 'Milestone':
                $_output = !empty($data['milestone']) ? $data['milestone'] : '';
                break;
            case 'Assigned':
                $_output = !empty($data['assigned']) ? $data['assigned'] : '';
                break;
            case 'Start':
                $_output = !empty($data['start_date']) ? $data['start_date'] : '';
                break;
            case 'End':
                $_output = !empty($data['end_date']) ? $data['end_date'] : '';
                break;
            case 'Workload':
                $_output = !empty($data['workload']) ? $data['workload'] : 0;
                break;
            case 'Consume':
                $_output = !empty($data['consume']) ? $data['consume'] : 0;
                break;
            case 'Code project':
                $_output = !empty($data['code_project_1']) ? (string) $data['code_project_1'] : '';
                break;
            case 'Code project 1':
                $_output = !empty($data['code_project_2']) ? (string) $data['code_project_2'] : '';
                break;
            case 'Text':
                $_output = !empty($data['text']) ? $data['text'] : '';
                break;
            case 'Initial workload':
                $_output = !empty($data['initial_estimated']) ? $data['initial_estimated'] : '';
                break;
            case 'Initial start':
                $_output = !empty($data['initial_task_start_date']) ? $data['initial_task_start_date'] : '';
                break;
            case 'Initial end':
                $_output = !empty($data['initial_task_end_date']) ? $data['initial_task_end_date'] : '';
                break;
            case 'Duration':
                $_output = !empty($data['duration']) ? $data['duration'] : '';
                break;
            case 'Overload':
                $_output = !empty($data['overload']) ? $data['overload'] : '';
                break;
            case 'In Used':
                $_output = !empty($data['in_used']) ? $data['in_used'] : '';
                break;
            case 'Completed':
                $_output = !empty($data['completed']) ? $data['completed'] : '';
                break;
            case 'Remain':
                $_output = !empty($data['remain']) ? $data['remain'] : '';
                break;
            case 'Amount':
                $_output = !empty($data['amount']) ? $data['amount'] : '';
                break;
            case 'Progress order':
                $_output = !empty($data['progress_order']) ? $data['progress_order'] . ' %' : '';
                break;
        }
        $PhpExcel->value($colName . $rIndex, $_output);
    }
    $rIndex++;
}

// Redirect output to a client?s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="vision_task_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
