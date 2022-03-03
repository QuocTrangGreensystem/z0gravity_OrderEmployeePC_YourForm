<?php
	App::import("vendor", "str_utility");
	$str_utility = new str_utility();
	$view_content = $this->Xml->unserialize($view_content);
		foreach ($view_content as $key=>$value) {					
		if (isset($value["ProjectDetail"])) {
			foreach ($value["ProjectDetail"] as $key1=>$value1) {
				if (!is_array($value1)) {
				unset($view_content["UserView"]["ProjectDetail"]);
				$view_content["UserView"]["ProjectDetail"]['0'] = $value["ProjectDetail"];
				}
			}
		}
	}
	$title = __("Project Detail of ".$project_name['Project']['project_name'],true);
	$excel->addTitle($title);
	$head = array('No','Title','Content');
	$excel->addRowHead($head);
	$i=0;
	foreach ($projects as $i=>$project) {
		//debug($project);exit;
		$data = array();
		
		$data[] = $i;
		foreach ($view_content as $key=>$value) {
			foreach ($value["ProjectDetail"] as $key1=>$value1) {
				$i++;
				foreach ($value1 as $field_name=>$alias) {
					switch ($field_name) {
						case "project_name":
							$excel->addRow(array($i,$alias,$project['Project']['project_name']));break;
						case "company_id":
							$excel->addRow(array($i,$alias,$project['Company']['company_name']));break;
						case "project_manager_id":
							$excel->addRow(array($i,$alias,$project["Employee"]["fullname"]));break;
						case "project_priority_id":
							$excel->addRow(array($i,$alias,$project['ProjectPriority']['priority']));break;
						case "project_status_id":
							$excel->addRow(array($i,$alias,$project['ProjectStatus']['name']));break;
						case "start_date": 
							$excel->addRow(array($i,$alias,$str_utility->convertToVNDate($project['Project']['start_date'])));break;
						case "end_date": 
							$excel->addRow(array($i,$alias,$str_utility->convertToVNDate($project['Project']['end_date'])));break;
						case "planed_end_date":
							$excel->addRow(array($i,$alias,$str_utility->convertToVNDate($project['Project']['planed_end_date'])));break;
						case "weather": 
							$weather = "";
							if (!empty($project['ProjectAmr'])) $weather =  $project['ProjectAmr'][0]['weather'];
							$excel->addRow(array($i,$alias,$weather));break;
						case "budget":
							$excel->addRow(array($i,$alias,$project['Project']['budget']));break;
						case "project_phase_id":
							$excel->addRow(array($i,$alias,$project['ProjectPhase']['name']));break;
						case "project_objectives":
							$excel->addRow(array($i,$alias,$project['Project']['project_objectives']));break;
						case "issues":
							$excel->addRow(array($i,$alias,$project['Project']['issues']));break;
						case "constraint":
							$excel->addRow(array($i,$alias,$project['Project']['constraint']));break;
						case "remark":
							$excel->addRow(array($i,$alias,$project['Project']['remark']));break;
					}
				}
			}
		}	
	}
	$file = $title;
	$excel->render($file);
?>