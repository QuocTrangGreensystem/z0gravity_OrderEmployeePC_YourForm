<?php
	App::import("vendor", "str_utility");
	$str_utility = new str_utility();
	$view_content = $this->Xml->unserialize($view_content);
	ob_clean();
	debug($view_content);
	exit;
	$head = array();
	$head[] = "No";
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
	foreach ($view_content as $key=>$value) {
		foreach ($value["ProjectDetail"] as $key1=>$value1) {
			foreach ($value1 as $field_name=>$alias) {
				$head[] = $alias;
			}
		}	
	}
	$title = __("Project List ",true);
	$excel->addTitle($title);
	$excel->addRowHead($head);
	
	$i=0;
	foreach ($projects as $i=>$project) {
		$data = array();
		$i++;
		$data[] = $i;
		foreach ($view_content as $key=>$value) {
			foreach ($value["ProjectDetail"] as $key1=>$value1) {
				foreach ($value1 as $field_name=>$alias) {
					switch ($field_name) {
						case "project_name":
							$data[] = $project['Project']['project_name'];
							break;
						case "company_id":
							$data[] = $project['Company']['company_name'];
							break;										
						case "project_manager_id":
							$data[] = $project["Employee"]["fullname"];
							break;
						case "project_priority_id":
							$data[] = $project['ProjectPriority']['priority'];
							break;	
						case "project_status_id":
							$data[] = $project['ProjectStatus']['name'];
							break;
						case "start_date": 
							$data[] = $str_utility->convertToVNDate($project['Project']['start_date']);
							break;
						case "end_date": 
							$data[] = $str_utility->convertToVNDate($project['Project']['end_date']);
							break;
						case "planed_end_date":
							$data[] = $str_utility->convertToVNDate($project['Project']['planed_end_date']);
							break;
						case "weather": 
							$weather = "";
							if (!empty($project['ProjectAmr'])) $weather =  $project['ProjectAmr'][0]['weather'];
							$data[] = $weather;
							break;
						case "budget":
							$data[] = $project['Project']['budget'];
							break;
						case "project_phase_id":
							$data[] = $project['ProjectPhase']['name'];
							break;
						case "project_objectives":
							$data[] = $project['Project']['project_objectives'];
							break;
						case "issues":
							$data[] = $project['Project']['issues'];
							break;
						case "constraint":
							$data[] = $project['Project']['constraint'];
							break;
						case "remark":
							$data[] = $project['Project']['remark'];
							break;
					}
				}
			}
		}							
		$excel->addRow($data);
	}
	$file = $title;
	$excel->render($file);
?>