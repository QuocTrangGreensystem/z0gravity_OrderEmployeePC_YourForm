<?php
Class ExcelSet {

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
                ->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
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

    public function header($range, $value = '', $center = false) {
        //background: 004381 |
        //border: 185790 | 24,87,144
        $this->_sheet->setCellValue($range, $value);
        $this->_sheet->getStyle($range)->applyFromArray(array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '185790')
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '004381')
            ),
            'alignment' => array(
                'wrap' => true,
                'horizontal' => $center ? PHPExcel_Style_Alignment::HORIZONTAL_CENTER : PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => 'FFFFFF')
            )
        ));
    }

}

class SlickExporterComponent extends Object {

    public $controller;
    public $reader, $PhpExcel, $activeSheet;

    private $formatters, $actions;

    public function initialize(&$controller) {
        $this->controller = & $controller;
        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
        App::import("Vendor", "str_utility");
    }

    public function init() {

        $this->reader = PHPExcel_IOFactory::createReader('Excel5');
        $this->PhpExcel = $this->reader->load(TEMPLATES . 'project.xls');

        $this->PhpExcel->setActiveSheetIndex(0);
        $this->activeSheet = & $this->PhpExcel->getActiveSheet();
        $this->setTitle('Untitled');

        $this->formatters = $this->actions = array();

        //add pre-defined formatters
        $this->addFormatter('decimal', array($this, 'decimal'));
        $this->addFormatter('alignRight', array($this, 'alignRight'));
        $this->addFormatter('percentage', array($this, 'percentage'));
    }

    public function save($data, $filename) {
        $phpExcelSet = new ExcelSet($this->activeSheet);
        // writing header
        $row = 2;
        $col = 0;
        foreach ($data['header'] as $text) {
            $colName = PHPExcel_Cell::stringFromColumnIndex($col++);
            $phpExcelSet->header($colName . $row, $text);
            $this->activeSheet->getColumnDimension($colName)->setWidth(30);
        }
        // styling header
        $this->activeSheet->getRowDimension($row)->setRowHeight(25);

        // writing body
        $row++;
        foreach ($data['body'] as $columns) {
            $this->activeSheet->getRowDimension($row)->setRowHeight(20);
            $col = 0;
            foreach ($columns as $column) {
                // check va chuyen ve number.
                $_column = str_replace(',', '.', $column);
                if(is_numeric($_column)){
                    $column = $_column;
                }
                $output = '';
                $colName = PHPExcel_Cell::stringFromColumnIndex($col++);
                $_col = explode(':', $column);
                if ( isset($_col[0]) && $_col[0] == 'image' ) {
                    if( file_exists('./weathers/' . $_col[1] . '.png') ){
                        $objDrawing = new PHPExcel_Worksheet_Drawing();
                        $objDrawing->setName('Global Weather');
                        $objDrawing->setDescription('Global Weather');
                        $objDrawing->setPath('./weathers/' . $_col[1] . '.png');
                        $objDrawing->setCoordinates($colName . $row);
                        $objDrawing->setOffsetX(55);
                        $objDrawing->setWorksheet($this->activeSheet);
                    }
                } else {
                    // detecting format and do formatting here (using markup)
                    if( is_array($column) ){
                        $output = $this->format($column, $phpExcelSet, $colName, $row);
                        // finaly writing value
                        $phpExcelSet->value($colName . $row, $output);
                    } else {
                        // $this->activeSheet->setCellValueExplicit($colName . $row, $column, PHPExcel_Cell_DataType::TYPE_STRING);
                        $this->activeSheet->setCellValue($colName . $row, $column);
                    }
                }
            }
            $phpExcelSet->align('A' . $row . ":" . $colName . $row);
            $this->do_after_row_loop($phpExcelSet, $row);
            $row++;
        }

        // format file name
        $filename = str_replace(array(
            '{now}',
            '{date}',
            '{time}'
        ), array(
            date('H_i_s__d_m_Y'),
            date('d_m_Y'),
            date('H_i_s')
        ), $filename);

        // output file
        @ob_clean();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->PhpExcel, 'Excel5');
        $objWriter->save('php://output');
        die;
    }

    public function setTitle($title) {
        $this->activeSheet->setTitle($title);
        return $this;
    }

    public function setT($title) {
        $this->activeSheet->setTitle(__($title, true));
        return $this;
    }

    // actions
    private function do_after_row_loop(&$phpExcelSet, $row) {
        if( empty($this->actions['after_row_loop']) )return;

        foreach ($this->actions['after_row_loop'] as $function) {
            call_user_func($function, $this, $phpExcelSet, $row);
        }
    }

    // add action
    public function add_action($name, $function) {
        $this->actions[$name][] = $function;
    }

    //implements later
    private function format($column, &$phpExcelSet, $colName, $row) {
        $result = '';
        $type = $column['type'];

        if( isset($this->formatters[$type]) ){
            $result = call_user_func($this->formatters[$type], $this, $phpExcelSet, $colName, $row, $column);
        } else {
            switch($column['type']){
                default:
                    $result = $column['value'];
                break;
            }
        }
        return $result;
    }

    public function addFormatter($type, $function) {
        $this->formatters[$type] = $function;
        return $this;
    }

    // pre-defined formatters

    public function decimal($self, $set, $colName, $row, $column) {
        $this->activeSheet->getStyle($colName . $row)->getNumberFormat()->setFormatCode('###0.00');
        return $column['value'];
    }

    public function percentage($self, $set, $colName, $row, $column) {
        $activeSheet->getStyle($colName . $row)->getNumberFormat()->applyFromArray(
            array(
                'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
            )
        );
        return floatval($column['value']) / 100;
    }

    public function alignRight($self, $set, $colName, $row, $column) {
        $this->activeSheet->getStyle($colName . $row)->applyFromArray(array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ));
        return $column['value'];
    }
}
