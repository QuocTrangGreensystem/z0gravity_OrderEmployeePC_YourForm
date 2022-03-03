<?php
/**
 * PHPExcel
 *
 * Copyright (C) 2006 - 2011 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.7.6, 2011-02-27
 */

/** Error reporting */
error_reporting(E_ALL);

date_default_timezone_set('Europe/Paris');

/** PHPExcel */
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Read from Excel5 (.xls) template
$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(TEMPLATES . "user_view.xls");


	App::import("vendor", "str_utility");
	$str_utility = new str_utility();

	$title = __("Project Deliverable of ".$projectName['Project']['project_name'],true);
	$head = array('No','Deliverable','Status','Progress','Delivery date','Planned d. date','Responsibler','Actors','File Attachment');
	$k=0;//$g =1;
    $name_column = $columns;
    foreach ($head as $hea) {
        $j = 2;
        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A1', $title)
                            ->setCellValue($name_column[$k].$j, $hea);
        //$objPHPExcel->getActiveSheet()->getColumnDimension($name_column[$g])->setAutoSize(true);                     
        $k++; //$g++;          
    }
    
    $objPHPExcel->getActiveSheet()->getStyle($name_column['0'].$j.":".$name_column[$k-1].$j)->getFill()
                                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('CCCCCCCC');
	$i=0;$j=3;
	foreach($projectLivrables as $i=>$livrable)
	{
		
		$i++;
		$livrable_id = $livrable['ProjectLivrable']["id"];
                                                                                    
		$livrable_category_id = $livrable['ProjectLivrable']["project_livrable_category_id"];
		$livrable_category_name = "";
		foreach($projectLivrableCategories as $id=>$name){
			if($livrable_category_id==$id){
				$livrable_category_name = $name;   
				$livrable_category_id = $id;
				break; 
			}
		}
		
		$livrable_status_id = $livrable['ProjectLivrable']["project_livrable_status_id"];
		$livrable_status_name = "";
		foreach($projectStatuses as $id=>$name){
			if($livrable_status_id==$id){
				$livrable_status_name = $name;   
				$livrable_status_id = $id;
				break; 
			}
		}
		
		
		$livrable_progression = $livrable['ProjectLivrable']["livrable_progression"];
		$livrable_date_delivery = $livrable['ProjectLivrable']["livrable_date_delivery"];
		
		
		$livrable_date_delivery_planed = $livrable['ProjectLivrable']["livrable_date_delivery_planed"];
		
		$livrable_responsible_id = $livrable['ProjectLivrable']["livrable_responsible"];
		$livrable_responsible_name = "";
		foreach($projectManagers as $id=>$name){
			if($livrable_responsible_id==$id){
				$livrable_responsible_name = $name;   
				$livrable_responsible_id = $id;
				break; 
			}
		}
		
		$livrable_actors_ids = "";
		$livrable_actors_list = "";
		foreach($projectLivrableActors as $actor){
			if ($actor['ProjectLivrableActor']['project_livrable_id'] == $livrable_id) {
				$livrable_actors_ids .= $actor['ProjectLivrableActor']['employee_id']."-";
				$livrable_actors_list .= $projectManagers[$actor['ProjectLivrableActor']['employee_id']].", ";
			}
		}
		
		$livrable_file_attachment = $livrable['ProjectLivrable']['livrable_file_attachment'];
		$data=array($i,$livrable_category_name,$livrable_status_name,$livrable_progression,$str_utility->convertToVNDate($livrable_date_delivery),$str_utility->convertToVNDate($livrable_date_delivery_planed),$livrable_responsible_name,$livrable_actors_list,$livrable_file_attachment);
										
		$k=0;
        foreach ($data as $dat) {
                $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($name_column[$k].$j, $dat);								
                $k++; 
        }
        $j++;
	}
    $f = $j + 2;
        $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setName('global');
            $objDrawing->setDescription('Global');
            $objDrawing->setPath('./img/front/global-logo.jpg');
            $objDrawing->setCoordinates($name_column['1'].$f);
            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
        $objPHPExcel->getActiveSheet()->getRowDimension($f)->setRowHeight(13);
        $objPHPExcel->getActiveSheet()->getStyle($name_column['1'].$f)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($name_column['1'].$f)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client�s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="project_deliverable_of_'.str_replace(" ",'_',$projectName['Project']['project_name']).'_'.date('H_i_s_d_m_Y').'.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;        
?>