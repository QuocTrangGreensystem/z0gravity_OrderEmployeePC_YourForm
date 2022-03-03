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

    public function header($range, $value = '', $center = false)
    {
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
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => 'FFFFFF')
            )
        ));
    }

    public function borderT($range){
        $this->_sheet->getStyle($range)->applyFromArray(array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')
                ),
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array('rgb' => '000000')
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array('rgb' => '000000')
                )
            )
        ));
    }
}

class CommonExporterComponent extends Object {

	public $controller;
	public $reader, $PhpExcel, $activeSheet;

	private $formatters, $actions;

	public function initialize(&$controller) {
        $this->controller = & $controller;
        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
        App::import("Vendor", "str_utility");
    }

    public function init(){

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

    public function save($data, $filename){
    	$phpExcelSet = new ExcelSet($this->activeSheet);
    	// writing header
    	$row = 2;
    	$col = 0;
    	foreach ($data['header'] as $def) {
    		$colName = PHPExcel_Cell::stringFromColumnIndex($col++);
            if( is_array($def) ){
                $text = $def['value'];
                if( isset($def['width']) ){
                    $this->activeSheet->getColumnDimension($colName)->setWidth($def['width']);
                }
            } else {
                $text = $def;
            }
			$phpExcelSet->header($colName . $row, $text);
    	}
    	// styling header
		$this->activeSheet->getRowDimension($row)->setRowHeight(21);

		// writing body
		$row++;
		foreach ($data['body'] as $block) {
			$col = 0;
            // print block name
            $rowspan = $row + count($block['rows']) - 1;
            $colName = PHPExcel_Cell::stringFromColumnIndex($col);
            $titleCell = $colName . $row;
            // center
            $this->activeSheet->getStyle($titleCell)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            //merge the title
            $this->activeSheet->mergeCells($titleCell . ':' . $colName . $rowspan);
            $phpExcelSet->borderT($titleCell . ':' . $colName . $rowspan);
            //apply bold
            $this->activeSheet->getStyle($titleCell . ':' . $colName . $rowspan)->applyFromArray(
                array(
                    'font'  => array(
                        'bold'  => true,
                    )
                )
            );
            $phpExcelSet->value($titleCell, $block['name']);
            // print rows and columns
			foreach ($block['rows'] as $columns) {
                $col = 1;
				foreach ($columns as $column){
                    $colName = PHPExcel_Cell::stringFromColumnIndex($col++);
                    if( is_array($column) ){
                        $output = $this->format($column, $phpExcelSet, $colName, $row);
                        // finaly writing value
                        $phpExcelSet->value($colName . $row, $output);
                    } else {
                        $phpExcelSet->value($colName . $row, $column);
                    }
                    $phpExcelSet->border($colName . $row);
                }
                $this->do_after_row_loop($phpExcelSet, $row);
                $row++;
			}
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

    public function setTitle($title){
        $this->activeSheet->setTitle($title);
        return $this;
    }

    public function setT($title){
        $this->activeSheet->setTitle(__($title, true));
        return $this;
    }

    // actions
    private function do_after_row_loop(&$phpExcelSet, $row){
        if( empty($this->actions['after_row_loop']) )return;

        foreach ($this->actions['after_row_loop'] as $function) {
            call_user_func($function, $this, $phpExcelSet, $row);
        }
    }

    // add action
    public function add_action($name, $function){
        $this->actions[$name][] = $function;
    }

    //implements later
    private function format($column, &$phpExcelSet, $colName, $row){
    	$result = '';
    	$type = @$column['type'];

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

    public function addFormatter($type, $function){
    	$this->formatters[$type] = $function;
        return $this;
    }

    // pre-defined formatters
    public function apply($type, $column, &$phpExcelSet, $colName, $row){
        $result = '';
        if( isset($this->formatters[$type]) ){
            $result = call_user_func($this->formatters[$type], $this, $phpExcelSet, $colName, $row, $column);
        } else {
            $result = $column['value'];
        }
        return $result;
    }

    public function decimal($self, $set, $colName, $row, $column){
    	$this->activeSheet->getStyle($colName . $row)->getNumberFormat()->setFormatCode('###0.00');
    	return $column['value'];
    }

    public function percentage($self, $set, $colName, $row, $column){
        $activeSheet->getStyle($colName . $row)->getNumberFormat()->applyFromArray( 
            array( 
                'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
            )
        );
        return floatval($column['value']) / 100;
    }

    public function alignRight($self, $set, $colName, $row, $column){
    	$this->activeSheet->getStyle($colName . $row)->applyFromArray(array(
    		'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                // 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ));
    	return $column['value'];
    }
}
