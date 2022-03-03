<?php 
class ExcelHelper extends AppHelper 
{
    var $filename = 'arquive';
    var $columnTotal=0;
    var $checkCountColumn=true;
    var $rows = array();
    var $title = "No Title";
    
    var $header = "<?xml version=\"1.0\" encoding=\"UTF-8\"?\>
	<?mso-application progid=\"Excel.Sheet\"?\>
<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\"
 xmlns:o=\"urn:schemas-microsoft-com:office:office\"
 xmlns:x=\"urn:schemas-microsoft-com:office:excel\"
 xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\"
 xmlns:html=\"http://www.w3.org/TR/REC-html40\">";
 
	//Hieu viet
	var $style = "<Styles>
					<Style ss:ID=\"s61\" ss:Name=\"Normal\">
						<Alignment ss:Vertical=\"Bottom\"/>
						<Borders>
						<Border ss:Position=\"Bottom\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\"/>
						<Border ss:Position=\"Left\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\"/>
						<Border ss:Position=\"Right\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\"/>
						<Border ss:Position=\"Top\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\"/>
						</Borders>
						<Font ss:FontName=\"Calibri\" x:Family=\"Swiss\" ss:Size=\"11\" ss:Color=\"#000000\"/>
						<Interior/>
						<NumberFormat/>
						<Protection/>
					</Style>
					<Style ss:ID=\"s62\">
						<Borders>
						<Border ss:Position=\"Bottom\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\"/>
						<Border ss:Position=\"Left\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\"/>
						<Border ss:Position=\"Right\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\"/>
						<Border ss:Position=\"Top\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\"/>
						</Borders>
						<Alignment ss:Horizontal=\"Center\" ss:Vertical=\"Bottom\"/>
						<Font ss:FontName=\"Calibri\" x:Family=\"Swiss\" ss:Size=\"11\" ss:Color=\"#000000\"
						ss:Bold=\"1\"/>
						<Interior ss:Color=\"#D9D9D9\" ss:Pattern=\"Solid\"/>
					</Style>
					<Style ss:ID=\"s63\">
						<Borders>
						<Border ss:Position=\"Bottom\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\"/>
						<Border ss:Position=\"Left\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\"/>
						<Border ss:Position=\"Right\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\"/>
						<Border ss:Position=\"Top\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\"/>
					   </Borders>
					   <Alignment ss:Horizontal=\"Center\" ss:Vertical=\"Bottom\"/>
					   <Font ss:FontName=\"Calibri\" x:Family=\"Swiss\" ss:Size=\"16\" ss:Color=\"#000000\"
						ss:Bold=\"1\"/>
					</Style>
				</Styles>";
 
    var $footer = "</Workbook>";
    
    var $worksheet_title = "Table";
    
    function getHeaders () {
        header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
        header("Content-Disposition: inline; filename=\"" . $this->filename . ".xls\"");
    }
    
    function addRow ($data = array()) {
        
        foreach($data as $key => $value) {
            $data[$key] = "<Cell ss:StyleID=\"s61\"><Data ss:Type=\"String\"><![CDATA[" . $value . "]]></Data></Cell>\n";
        }
        $this->rows[] = $data;
    }
	function addRowHead ($data = array()) {
        
        foreach($data as $key => $value) {
            $data[$key] = "<Cell ss:StyleID=\"s62\"><Data ss:Type=\"String\"><![CDATA[" . $value . "]]></Data></Cell>\n";
			if($this->checkCountColumn) $this->columnTotal++;
        }
		$this->checkCountColumn = false;
        $this->rows[] = $data;
    }
	function addTitle ($data) {
        $this->title = $data;
    }
    
    function setTitle ($title) {
        $title = preg_replace ("/[\\\|:|\/|\?|\*|\[|\]]/", "", $title);
        $title = substr ($title, 0, 31);
        $this->worksheet_title = $title;
    }
	
    function render ($file = null) {
        $this->filename = ($file) ? $file : $this->filename;
        $this->getHeaders();
		
        $content = array();
		$content[] = "<Row ss:Height=\"18.75\"><Cell ss:MergeAcross='".($this->columnTotal-1)."' ss:StyleID=\"s63\"><Data ss:Type=\"String\">".$this->title."</Data></Cell></Row>";
        foreach($this->rows as $row) {
            $content[] = "<Row>" . implode('', $row) . "</Row>";
        }
        
        $data = implode("\n", $content);
        
        echo stripslashes ($this->header);
        echo stripslashes ($this->style);
        echo "\n<Worksheet ss:Name=\"" . $this->worksheet_title. "\">\n<Table>\n";
        echo "<Column ss:Index=\"1\" ss:AutoFitWidth=\"0\" ss:Width=\"30\"/>\n";
		//for($i=1;$i<$this->columnTotal;$i++){
			echo "<Column ss:AutoFitWidth=\"0\" ss:Width=\"120\" ss:Span='".($this->columnTotal-2)."'/>";
		//}
        echo $data;
		
		//echo "<Row>  <Cell>  <Data ss:Type=\"String\"><![CDATA[" . $this->columnTotal . "]]></Data>  </Cell>  </Row>\n";
		
        echo "</Table>\n</Worksheet>\n";
        echo $this->footer;
    }
}