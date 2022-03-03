<?php
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
App::import("Vendor", "str_utility");
$str_utility = new str_utility();


$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . 'user_view.xls');
$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
$cacheSettings = array(
            'memoryCacheSize' => '256MB',
            'cacheTime' => 600,
            'max_execution_time' => 600,
            'max_input_time' => 600,
            'memory_limit' => '512M'
            );
PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
$objPHPExcel->setActiveSheetIndex(0);
$activeSheet = & $objPHPExcel->getActiveSheet();
$activeSheet->setTitle('Activity Review');



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
        'name' => $activityColumn['name']['name'],
        'path' => 'Activity.name',
        'width' => 30,
    ),
    array(
        'name' => $activityColumn['long_name']['name'],
        'path' => 'Activity.long_name',
        'width' => 30,
    ),
    array(
        'name' => $activityColumn['short_name']['name'],
        'path' => 'Activity.short_name',
        'width' => 30,
    ),
    array(
        'name' => $activityColumn['family_id']['name'],
        'path' => 'Activity.family_id',
        'width' => 30,
    ),
    array(
        'name' => $activityColumn['subfamily_id']['name'],
        'path' => 'Activity.subfamily_id',
        'width' => 30,
    ),
    array(
        'name' => $activityColumn['accessible_profit']['name'],
        'path' => 'AccessibleProfit',
        'width' => 90,
    ),
    array(
        'name' => $activityColumn['pms']['name'],
        'path' => 'Activity.pms',
        'width' => 30,
    ),
    array(
        'name' => $activityColumn['linked_profit']['name'],
        'path' => 'LinkedProfit',
        'width' => 60,
    ),
    array(
        'name' => $activityColumn['code1']['name'],
        'path' => 'Activity.code1',
        'width' => 30,
    ),
    array(
        'name' => $activityColumn['code2']['name'],
        'path' => 'Activity.code2',
        'width' => 30,
    ),
    array(
        'name' => $activityColumn['code3']['name'],
        'path' => 'Activity.code3',
        'width' => 30,
    ),
    array(
        'name' => $activityColumn['start_date']['name'],
        'path' => 'Activity.start_date',
        'width' => 30,
    ),
    array(
        'name' => $activityColumn['end_date']['name'],
        'path' => 'Activity.end_date',
        'width' => 30,
    ),
    array(
        'name' => $activityColumn['consumed']['name'],
        'path' => 'Activity.consumed',
        'width' => 30,
    ),
    array(
        'name' => $activityColumn['workload']['name'],
        'path' => 'Activity.workload',
        'width' => 30,
    ),
    array(
        'name' => $activityColumn['overload']['name'],
        'path' => 'Activity.overload',
        'width' => 30,
    ),
    array(
        'name' => $activityColumn['completed']['name'],
        'path' => 'Activity.completed',
        'width' => 30,
    ),
    array(
        'name' => $activityColumn['remain']['name'],
        'path' => 'Activity.remain',
        'width' => 30,
    ),
    array(
        'name' => __('Activated', true),
        'path' => 'Activity.activated',
        'width' => 30,
    ),
    array(
        'name' => $activityColumn['code4']['name'],
        'path' => 'Activity.code4',
        'width' => 30,
    ),
    array(
        'name' => $activityColumn['code5']['name'],
        'path' => 'Activity.code5',
        'width' => 30,
    ),
    array(
        'name' => $activityColumn['code6']['name'],
        'path' => 'Activity.code6',
        'width' => 30,
    ),
    array(
        'name' => $activityColumn['code7']['name'],
        'path' => 'Activity.code7',
        'width' => 30,
    ),
    array(
        'name' => $activityColumn['code8']['name'],
        'path' => 'Activity.code8',
        'width' => 30,
    ),
    array(
        'name' => $activityColumn['code9']['name'],
        'path' => 'Activity.code9',
        'width' => 30,
    ),
    array(
        'name' => $activityColumn['code10']['name'],
        'path' => 'Activity.code10',
        'width' => 30,
    )
);

$selectMaps = array(
    'actif' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'pms' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'family_id' => $families,
    'subfamily_id' => $subfamilies
);

App::import("vendor", "str_utility");
$str_utility = new str_utility();

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
        'startcolor' => array('rgb' => '5fa1c4')
    ),
    'font' => array(
        'bold' => true,
        'color' => array(
            'rgb' => 'FFFFFF'
            ),
        'size' => 11,
        )));

$rIndex = 2;
$no = 1;
$colMax = $colName;
foreach ($activities as $activity) {
    $PhpExcel->value('A' . $rIndex, $no++);
    $activeSheet->getRowDimension($rIndex)->setRowHeight(21);

    $outputs = '';
    $outputLinked = '';
    $cIndex = 1;

    foreach ($activityColumn as $key => $column) {
        $data[$key] = '';
        if ($column['calculate'] === false && isset($activity['Activity'][$key])) {
            $data[$key] = (string) $activity['Activity'][$key];
            if ($key === 'actif' || $key === 'pms') {
                $data[$key] = $data[$key] ? 'yes' : 'no';
            }
        }
    }

    $cIndex = 1;
    foreach ($fieldset as $_fieldset) {
        $colName = PHPExcel_Cell::stringFromColumnIndex($cIndex++);
        $_output = Set::classicExtract($activity, $_fieldset['path']);
        switch ($_fieldset['path']) {
            case 'Activity.family_id' : {
                    $_output = isset($families[$_output]) ? $families[$_output] : '';
                    break;
                }
            case 'Activity.subfamily_id' : {
                    $_output = isset($subfamilies[$_output]) ? $subfamilies[$_output] : '';
                    break;
                }
            case 'Activity.pms' : {
                    $_output = $activity['Activity']['pms'] ? 'YES' : 'NO';
                    break;
                }
            case 'Activity.activated' : {
                    $_output = $activity['Activity']['activated'] ? 'YES' : 'NO';
                    break;
                }
            case 'Activity.activated' : {
                    $_output = $activity['Activity']['activated'] ? 'YES' : 'NO';
                    break;
                }
            case 'AccessibleProfit' : {
                    $outs = "";
                    if ($_output) {
                        foreach ($_output as $k => $val) {
                            $outs .= $profitCenters[$val['profit_center_id']] . "; ";
                        }
                    }
                    $_output = $outs;
                    break;
                }
            case 'LinkedProfit' : {
                    $outs = "";
                    if ($_output) {
                        foreach ($_output as $k => $val) {
                            $outs .= $profitCenters[$val['profit_center_id']] . "; ";
                        }
                    }
                    $_output = $outs;
                    break;
                }
            case 'Activity.md' : {
                    $_output = floatval($_output);
                    break;
                }
            case 'Activity.start_date' :
            case 'Activity.end_date' : {
                    $_output = (empty($_output) || $_output == '0000-00-00') ? '' : $str_utility->convertToVNDate(date('Y-m-d',$_output));
                    break;
                }
            case 'Activity.consumed' : {
                $_output = isset($sumActivities[$data['id']]) ? $sumActivities[$data['id']] : 0;
                break;
            }
            case 'Activity.workload' : {
                if($activity['Activity']['pms'] == 0){
                    $_output = isset($dataFromActivityTasks[$data['id']]['workload']) ? $dataFromActivityTasks[$data['id']]['workload'] : 0;
                } else {
                    $_output = isset($dataFromProjectTasks[$data['id']]['workload']) ? $dataFromProjectTasks[$data['id']]['workload'] : 0;
                }
                break;
            }
            case 'Activity.overload' : {
                if($activity['Activity']['pms'] == 0){
                    $_output = isset($dataFromActivityTasks[$data['id']]['overload']) ? $dataFromActivityTasks[$data['id']]['overload'] : 0;
                } else {
                    $_output = isset($dataFromProjectTasks[$data['id']]['overload']) ? $dataFromProjectTasks[$data['id']]['overload'] : 0;
                }
                break;
            }
            case 'Activity.completed' : {
                if($activity['Activity']['pms'] == 0){
                    $_output = isset($dataFromActivityTasks[$data['id']]['completed']) ? $dataFromActivityTasks[$data['id']]['completed'].'%' : '0%';
                } else {
                    $_output = isset($dataFromProjectTasks[$data['id']]['completed']) ? $dataFromProjectTasks[$data['id']]['completed'].'%' : '0%';
                }
                break;
            }
            case 'Activity.remain' : {
                if($activity['Activity']['pms'] == 0){
                    $_output = isset($dataFromActivityTasks[$data['id']]['remain']) ? $dataFromActivityTasks[$data['id']]['remain'] : 0;
                } else {
                    $_output = isset($dataFromProjectTasks[$data['id']]['remain']) ? $dataFromProjectTasks[$data['id']]['remain'] : 0;
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
    $rIndex++;
}
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="activity_management_' . date('H_i_s_d_m_Y') . '.xls"');
header('Cache-Control: max-age=0');
$objWriter->save('php://output');
?>
