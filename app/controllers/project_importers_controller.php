<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectImportersController extends AppController {
	var $uses = array(
		'Project', 
		'ProjectFunction', 
		'Company', 
		'ProjectTeam', 
		'ProjectPhasePlan', 
		'ProjectPhase', 
		'ProjectCreatedVal', 
		'ProfitCenter',
		'ProjectLivrableActors', 
		'ProjectEvolutionImpact', 
		'ProjectLivrableActor',
		'ProjectEvolutionImpactRefer', 
		'CompanyEmployeeReference', 
		'ProjectAmr', 
		'ProjectMilestone', 
		'ProjectTask',
		'ProjectRisk',
		'ProjectIssue', 
		'ProjectDecision', 
		'ProjectLivrable', 
		'ProjectEvolution', 
		'ProjectFunctionEmployeeRefer',
		'ProjectStatus', 
		'ProjectPriority', 
		'ProjectComplexity', 
		'ProjectAmrProgram', 
		'ProjectType', 
		'ProjectSubType', 
		'ProjectAmrSubProgram',
		'Currency',
		'Employee',
		'Activity',
		'ActivityTask',
		'ActivityRequest',
		'ProjectBudgetInternal',
		'ProjectBudgetInternalDetail',
		'ProjectBudgetExternal',
		'ProjectBudgetSale',
		'ProjectBudgetInvoice',
		'ProjectBudgetSyn'
	);
	var $components = array('MultiFileUpload');
	
	public function index(){
		if( !isset($this->employee_info['Company']['id']) ){
			$this->Session->setFlash(__('You do not belong to any companies to do this', true), 'error');
			$this->redirect(array('action' => 'index'));
		}
		App::import('Vendor', 'ParsecsvLib', array('file' => 'parsecsv.lib.php'));
		$csv = new parseCSV();
		if (!empty($_FILES['FileField']['name']['csv_file_attachment'])) {
			$this->MultiFileUpload->encode_filename = false;
			$this->MultiFileUpload->uploadpath = TMP . 'uploads' . DS . 'importers' . DS;
			$this->MultiFileUpload->_properties['AttachTypeAllowed'] = "csv";
			$this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
			$reVal = $this->MultiFileUpload->upload();
			if (!empty($reVal)) {
				$this->loadModel('Employee');
				$filename = TMP . 'uploads' . DS . 'importers' . DS . $reVal['csv_file_attachment']['csv_file_attachment'];
				$csv->auto($filename);
				if (!empty($csv->data)) {
					$records = array(
						'Create' => array(),
						'Update' => array(),
						'Error' => array()
					);
					$default = array(
						'STATUS' => '',	//category
						'Project Name' => '',
						'Long Project Name' => '',
						'Project Code 1' => '',
						'Project Code 2' => '',
						'Project Manager' => '',
						'Project Type' => '',
						'Project Sub Type' => '',
						'Chief Business' => '',
						'Program' => '',
						'Sub Program' => '',
						'Technical Manager' => '',
						'Priority' => '',
						'Implementation Complexity' => '',
						'Customer' => '',
						'Status' => '',
						'Phase' => '',
						'Issues' => '',
						'Primary Objectives' => '',
						'Project Objectives' => '',
						'Constraint' => '',
						'Remark' => '',
						//activity
						'NAME' => '',
						'NAME DETAIL' => '',
						'SHORT NAME' => '',
						'FAMILY' => '',
						'SUB FAMILY' => '',
						'ACTIVATED' => '',
						'IMPORT CODE' => ''
					);
					$validate = array('STATUS', 'Project Name', 'Project Code 1', 'Project Manager', 'Phase');
					//prepair data
					$types = array(
						'OPPORTUNITY' => 2,
						'ARCHIVED' => 3,
						'IN PROGRESS' => 1,
						'MODEL' => 4
					);
					$company_id = $this->employee_info['Company']['id'];
					//complex
					$complexityList = $this->ProjectComplexity->find('list', array(
						'conditions' => array(
							'company_id' => $company_id
						),
						'fields' => array('id', 'name')
					));
					//project type
					$typeList = $this->ProjectType->find('list', array(
						'conditions' => array(
							'company_id' => $company_id,
							'display' => 1
						),
						'fields' => array('id', 'project_type')
					));
					//sub type
					$subTypeList = $this->ProjectSubType->find('list', array(
						'conditions' => array(
							'project_type_id' => array_keys($typeList),
							'display' => 1
						),
						'fields' => array('id', 'project_sub_type')
					));
					//resources
					$this->Employee->virtualFields = array('full_name' => "CONCAT(first_name, ' ', last_name)");
					$resourceList = $this->Employee->find('list', array(
						'recursive' => -1,
						'conditions' => array('company_id' => $company_id),
						'fields' => array('id', 'full_name')
					));
					//amr programs
					$programList = $this->ProjectAmrProgram->find('list', array(
						'conditions' => array(
							'company_id' => $company_id
						),
						'fields' => array('id', 'amr_program')
					));
					//sub program
					$subProgramList = $this->ProjectAmrSubProgram->find('list', array(
						'conditions' => array(
							'project_amr_program_id' => array_keys($programList)
						),
						'fields' => array('id', 'amr_sub_program')
					));
					//status
					$statusList = $this->ProjectStatus->find('list', array(
						'conditions' => array(
							'company_id' => $company_id
						),
						'fields' => array('id', 'name')
					));
					//budget customer (customer)
					$this->loadModel('BudgetCustomer');
					$customerList = $this->BudgetCustomer->find('list', array(
						'conditions' => array(
							'company_id' => $company_id
						),
						'fields' => array('id', 'name')
					));
					//phase list
					$phaseList = $this->ProjectPhase->find('list', array(
						'conditions' => array(
							'company_id' => $company_id
						),
						'fields' => array('id', 'name')
					));
					//priority: must format
					$priorityList = $this->ProjectPriority->find('list', array(
						'conditions' => array(
							'company_id' => $company_id
						),
						'fields' => array('id', 'priority')
					));
					//activity
					//families
					$this->loadModel('ActivityFamily');
					$familyList = $this->ActivityFamily->find('list', array(
						'conditions' => array(
							'company_id' => $company_id,
							'OR' => array(
								'parent_id IS NULL',
								'parent_id' => 0
							)
						),
						'fields' => array('id', 'name')
					));
					$subFamilyList =  $this->ActivityFamily->find('list', array(
						'conditions' => array(
							'parent_id' => array_keys($familyList)
						),
						'fields' => array('id', 'name')
					));
					$validate2 = array('NAME', 'SHORT NAME', 'NAME DETAIL', 'FAMILY', 'ACTIVATED');
					foreach ($csv->data as $row) {
						$row = $this->changeKeys($row, $default);
						//remove space
						array_walk($row, array($this, 'sstrim'));
						$error = false;                            
						$row = array_merge($default, $row, array('data' => array(), 'columnHighLight' => array(), 'error' => array(), 'description' => array()));
						foreach ($validate as $key) {
							$row[$key] = trim($row[$key]);
							if (empty($row[$key])) {
								$row['error'][] = sprintf(__('The %s is not blank', true), $key);
								$error = true;
							}
						}
						if( $row['STATUS'] == 'IN PROGRESS' ){
							foreach ($validate2 as $key) {
								$row[$key] = trim($row[$key]);
								if (empty($row[$key])) {
									$row['error'][] = sprintf(__('The %s is not blank', true), $key);
									$error = true;
								}
							}
						}
						if (!$error) {
							$row['data']['company_id'] = $company_id;
							if( !isset($types[$row['STATUS']]) ){
								$error = true;
								$row['error'][] = __('Invalid STATUS', true);
								$row['columnHighLight']['STATUS'] =  '';
							} else {
								$row['data']['category'] = $types[$row['STATUS']];
							}

							//check if project existed
							$project = $this->Project->find('first', array(
								'conditions' => array(
									'project_name' => $row['Project Name'],
									'company_id' => $company_id
								),
								'recursive' => -1
							));
							if( !empty($project) ){
								//if exists then check status
								if( $project['Project']['category'] == 1 && $row['STATUS'] == 'OPPORTUNITY'){
									//check consumed or have timesheet
									if( $this->hasConsumed($project) ){
										$error = true;
										$row['error'][] = __('Project already has consumed data', true);
										$row['columnHighLight']['Project Name'] =  '';
									} else {
										$row['data']['delete_activity'] = $project['Project']['activity_id'];
									}
								}
								$row['data']['id'] = $project['Project']['id'];
							} else {
								$row['data']['project_name'] = $row['Project Name'];
							}

							//long name
							$row['data']['long_project_name'] = $row['Long Project Name'];

							//project code 1
							$row['data']['project_code_1'] = $row['Project Code 1'];
							$row['data']['project_code_2'] = $row['Project Code 2'];

							//manager, tech manager, chief business
							//the first is pm, other is backup
							$names = explode(',', $row['Project Manager']);
							$pm = false;
							foreach($names as $name){
								if( ($id = array_search($name, $resourceList)) !== false ){
									if( !$pm )$row['data']['project_manager_id'] = $pm = $id;
									else $row['data']['backup_pm'][] = $id;
								} else {
									$error = true;
									$row['error'][] = sprintf(__('Project Manager \'%s\' does not exist', true), $name);
									$row['columnHighLight']['Project Manager'] =  '';
								}
							}

							if( !empty($row['Technical Manager']) ){
								$names = explode(',', $row['Technical Manager']);
								$pm = false;
								foreach($names as $name){
									if( ($id = array_search($name, $resourceList)) !== false ){
										if( !$pm )$row['data']['technical_manager_id'] = $pm = $id;
										else $row['data']['backup_tm'][] = $id;
									} else {
										$error = true;
										$row['error'][] = sprintf(__('Technical Manager \'%s\' does not exist', true), $name);
										$row['columnHighLight']['Technical Manager'] =  '';
									}
								}
							}

							if( !empty($row['Chief Business']) ){
								$names = explode(',', $row['Chief Business']);
								$pm = false;
								foreach($names as $name){
									if( ($id = array_search($name, $resourceList)) !== false ){
										if( !$pm )$row['data']['chief_business_id'] = $pm = $id;
										else $row['data']['backup_cb'][] = $id;
									} else {
										$error = true;
										$row['error'][] = sprintf(__('Chief Business \'%s\' does not exist', true), $name);
										$row['columnHighLight']['Chief Business'] =  '';
									}
								}
							}

							//project type
							if( !empty($row['Project Type'])){
								if( ($typeId = array_search($row['Project Type'], $typeList)) !== false ){
									$row['data']['project_type_id'] = $typeId;
								} else {
									$error = true;
									$row['error'][] = sprintf(__('Project type \'%s\' does not exist', true), $row['Project Type']);
									$row['columnHighLight']['Project Type'] =  '';
								}
							}
							//sub type
							if( !empty($row['Project Sub Type'])){
								if( ($typeId = array_search($row['Project Sub Type'], $subTypeList)) !== false ){
									$row['data']['project_sub_type_id'] = $typeId;
								} else {
									$error = true;
									$row['error'][] = sprintf(__('Project sub type \'%s\' does not exist', true), $row['Project Sub Type']);
									$row['columnHighLight']['Project Sub Type'] =  '';
								}
							}

							//program
							if( !empty($row['Program'])){
								if( ($id = array_search($row['Program'], $programList)) !== false ){
									$row['data']['project_amr_program_id'] = $id;
								} else {
									$error = true;
									$row['error'][] = sprintf(__('Program \'%s\' does not exist', true), $row['Program']);
									$row['columnHighLight']['Program'] =  '';
								}
							}
							if( !empty($row['Sub Program'])){
								if( ($id = array_search($row['Sub Program'], $subProgramList)) !== false ){
									$row['data']['project_amr_sub_program_id'] = $id;
								} else {
									$error = true;
									$row['error'][] = sprintf(__('Sub Program \'%s\' does not exist', true), $row['Sub Program']);
									$row['columnHighLight']['Sub Program'] =  '';
								}
							}
							//priority
							if( !empty($row['Priority'])){
								if( ($id = array_search($row['Priority'], $priorityList)) !== false ){
									$row['data']['project_priority_id'] = $id;
								} else {
									$error = true;
									$row['error'][] = sprintf(__('Project priority \'%s\' does not exist', true), $row['Priority']);
									$row['columnHighLight']['Priority'] =  '';
								}
							}
							//complexity
							if( !empty($row['Implementation Complexity'])){
								if( ($id = array_search($row['Implementation Complexity'], $complexityList)) !== false ){
									$row['data']['complexity_id'] = $id;
								} else {
									$error = true;
									$row['error'][] = sprintf(__('Implementation Complexity \'%s\' does not exist', true), $row['Implementation Complexity']);
									$row['columnHighLight']['Implementation Complexity'] =  '';
								}
							}
							//customer
							if( !empty($row['Customer'])){
								if( ($id = array_search($row['Customer'], $customerList)) !== false ){
									$row['data']['budget_customer_id'] = $id;
								} else {
									$error = true;
									$row['error'][] = sprintf(__('Customer \'%s\' does not exist', true), $row['Customer']);
									$row['columnHighLight']['Customer'] =  '';
								}
							}

							//status
							if( !empty($row['Status'])){
								if( ($id = array_search($row['Status'], $statusList)) !== false ){
									$row['data']['project_status_id'] = $id;
								} else {
									$error = true;
									$row['error'][] = sprintf(__('Status \'%s\' does not exist', true), $row['Status']);
									$row['columnHighLight']['Status'] =  '';
								}
							}

							//Phase
							if( !empty($row['Phase'])){
								if( ($id = array_search($row['Phase'], $phaseList)) !== false ){
									$row['data']['project_phase_id'] = $id;
								} else {
									$error = true;
									$row['error'][] = sprintf(__('Phase \'%s\' does not exist', true), $row['Phase']);
									$row['columnHighLight']['Phase'] =  '';
								}
							}
							//issues, objectives, constraint, remark
							$row['data']['issues'] = $row['Issues'];
							$row['data']['primary_objectives'] = $row['Primary Objectives'];
							$row['data']['project_objectives'] = $row['Project Objectives'];
							$row['data']['constraint'] = $row['Constraint'];
							$row['data']['remark'] = $row['Remark'];

							//activity section
							if( !empty($row['NAME']) ){
								//init activity
								$row['data']['activity'] = array();
								$row['data']['activity']['company_id'] = $company_id;
								//pm
								if( isset($row['data']['project_manager_id']) )$row['data']['activity']['project_manager_id'] = $row['data']['project_manager_id'];
								//customer
								if( isset($row['data']['budget_customer_id']) )$row['data']['activity']['budget_customer_id'] = $row['data']['budget_customer_id'];

								//check activity exist
								$check = $this->Activity->find('first', array(
									'conditions' => array(
										'name' => $row['NAME'],
										'company_id' => $company_id
									),
									'recursive' => -1
								));
								if( !empty($check) ){

									//kiem tra 2 ong nay co link toi nhau ko
									//TH1: project: chua link, activity da link toi pj khac
									if( (empty($project) || ($project && !$project['Project']['activity_id'])) && $check['Activity']['project_id'] ){
										$error = true;
										$row['error'][] = __('Activity is already linked to another project', true);
										$row['columnHighLight']['NAME'] =  '';
									} else if( $project && $project['Project']['activity_id'] && $check['Activity']['project'] && ($project['Project']['activity_id'] != $check['Activity']['id'] || $project['Project']['id'] != $check['Activity']['project'] ) ){
										$error = true;
										$row['error'][] = __('Activity and Project are already linked to another', true);
										$row['columnHighLight']['NAME'] =  '';
									} else {
										$row['data']['activity']['id'] = $check['Activity']['id'];
									}
								} else {
									if( $project && $project['Project']['activity_id'] ){
										$error = true;
										$row['error'][] = __('Project is already linked to another activity', true);
										$row['columnHighLight']['Project Name'] =  '';
									} else {
										$row['data']['activity']['name'] = $row['NAME'];
									}
								}
								//other names
								$row['data']['activity']['long_name'] = $row['NAME DETAIL'];
								$row['data']['activity']['short_name'] = $row['SHORT NAME'];
								//family
								if( ($id = array_search($row['FAMILY'], $familyList)) !== false ){
									$row['data']['activity']['family_id'] = $id;
								} else {
									$error = true;
									$row['error'][] = sprintf(__('Family \'%s\' does not exist', true), $row['FAMILY']);
									$row['columnHighLight']['FAMILY'] =  '';
								}
								//sub family
								if( !empty($row['SUB FAMILY'])){
									if( ($id = array_search($row['SUB FAMILY'], $subFamilyList)) !== false ){
										$row['data']['activity']['subfamily_id'] = $id;
									} else {
										$error = true;
										$row['error'][] = sprintf(__('Sub family \'%s\' does not exist', true), $row['SUB FAMILY']);
										$row['columnHighLight']['SUB FAMILY'] =  '';
									}
								}
								//activated
								if( $project['Project']['category'] == 1 && $row['STATUS'] == 'ARCHIVED' ){
									$row['data']['activity']['activated'] = 0;
								} else {
									$row['data']['activity']['activated'] = $row['ACTIVATED'] == 'YES' ? 1 : 0;
								}
								//pms
								$row['data']['activity']['pms'] = 1;
								//import code
								$row['data']['activity']['import_code'] = $row['IMPORT CODE'];
							}
						}
						if( $error ){
							unset($row['data']);
							$records['Error'][] = $row;
						} else if( isset($row['data']['id']) ) {
							$records['Update'][] = $row;
						} else {
							$records['Create'][] = $row;
						}
					} //endforeach
					$this->set('records', $records);
					$this->set('default', $default);
				}//end csv check
				unlink($filename);
				//return $this->render('import');
			}//end file check
		}//end method check
	}
	
	public function getTextOriginalYourForm(){
		$this->loadModel('Translation');
		$original_text = $this->Translation->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'page' => 'details',
				'field IS NOT NULL'
			),
			'fields' => array('field', 'original_text'),
			'order' => 'original_text ASC',
		));
		if(!empty($original_text)){
			foreach($original_text as $key => $value){
				if(preg_match("/upload_/", $key)){
					unset($original_text[$key]);
				}
				if(preg_match("/next_milestone_in_/", $key)){
					unset($original_text[$key]);
				}
			}
			if(!empty($original_text['last_modified'])){
				unset($original_text['last_modified']);
			}
			if(!empty($original_text['update_by_employee'])){
				unset($original_text['update_by_employee']);
			}
			if(!empty($original_text['activity_id'])){
				unset($original_text['activity_id']);
			}
			if(!empty($original_text['created_value'])){
				unset($original_text['created_value']);
			}
		}
		
		return $original_text;
	}

	public function import_model_project(){
		$this->loadModels('HistoryFilter');
		$data = array();
		$original_text = array();
		if(!empty($_FILES)){
			App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
			App::import("vendor", "str_utility");
			$str_utility = new str_utility();
			$data_format = array();
			$data_columns = array();
			if (!empty($_FILES['FileField']['name']['csv_file_attachment'])) {
				$this->MultiFileUpload->encode_filename = false;
				$this->MultiFileUpload->uploadpath = TMP . 'uploads' . DS . 'importers' . DS;
				$this->MultiFileUpload->_properties['AttachTypeAllowed'] = "*";
				$this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
				$reVal = $this->MultiFileUpload->upload();
				if (!empty($reVal)) {
					$filename = TMP . 'uploads' . DS . 'importers' . DS . $reVal['csv_file_attachment']['csv_file_attachment'];
					$inputFileType = PHPExcel_IOFactory::identify($filename);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$objPHPExcel = $objReader->load($filename);
					$objReader->setReadDataOnly(true);
					$objWorksheet = $objPHPExcel->getActiveSheet();
					$highestRow = $objWorksheet->getHighestRow(); 
					$highestColumn = $objWorksheet->getHighestColumn(); 
					$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); 
					$rows = array();
					$cols = array();
					for ($row = 1; $row <= $highestRow; ++$row) {
					  for ($col = 0; $col <= $highestColumnIndex; ++$col) {
						$cell = $objWorksheet->getCellByColumnAndRow($col, $row);
						if ($cell->getValue() instanceof PHPExcel_RichText) {
							$rows[$row][$col] = $cell->getValue()->getPlainText();
						} else {
							$rows[$row][$col] = $cell->getValue();
							if(!empty($rows[$row][$col]) && is_numeric($rows[$row][$col]) && PHPExcel_Shared_Date::isDateTime($cell) && strlen($rows[$row][$col])) {
								$rows[$row][$col] = date('d-m-Y', PHPExcel_Shared_Date::ExcelToPHP($cell->getValue()));
							}
						}
					  }
					}
					$data = $rows;
				}
				unlink($filename);
			}
			$original_text = array();
			if(!empty($data)){
				$flag = 0;
				foreach($data as $index => $values){
					if($flag == 0) $data_columns =  $values;
					else {
						$data_format[$index] = $values;
					}
					$flag = $index;
				}
				
				$this->loadModel('Translation');
				$mandatory_fields = array('Project Name', 'Program', 'Start Date', 'Project Manager');
				$original_text = $this->Translation->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'page' => 'details',
						'field IS NOT NULL',
						'original_text' => $mandatory_fields,
					),
					'fields' => array('field', 'original_text'),
					'order' => 'original_text ASC',
				));
				$original_text['model_named'] = __('Model Named', true);
			
			}
		}
	
		$setting_matching = $this->HistoryFilter->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'path' => 'model_project_importers_setting_matching_fields',
				'employee_id' => $this->employee_info['Employee']['id']
			),
			'fields' => array('params'),
		));
			
		$setting_matching = !empty($setting_matching) ? @unserialize($setting_matching['HistoryFilter']['params']) : array(); 

		$this->set(compact('data_format', 'data_columns', 'original_text', 'setting_matching'));
		
	}

	public function import_excel(){
		$this->loadModels('HistoryFilter');
		$data = array();
		$original_text = array();
		if(!empty($_FILES)){
			App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
			App::import("vendor", "str_utility");
			$str_utility = new str_utility();
			$data_format = array();
			$data_columns = array();
			$path = FILES . 'uploads' . DS;
            App::import('Core', 'Folder');
            new Folder($path, true, 0777);
			if (!empty($_FILES['FileField']['name']['csv_file_attachment'])) {
				$this->MultiFileUpload->encode_filename = false;
				$this->MultiFileUpload->uploadpath = $path;
				$this->MultiFileUpload->_properties['AttachTypeAllowed'] = "*";
				$this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
				$reVal = $this->MultiFileUpload->upload();
				if (!empty($reVal)) {
					$filename = $path . $reVal['csv_file_attachment']['csv_file_attachment'];
					$inputFileType = PHPExcel_IOFactory::identify($filename);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$objPHPExcel = $objReader->load($filename);
					$objReader->setReadDataOnly(true);
					$objWorksheet = $objPHPExcel->getActiveSheet();

					$highestRow = $objWorksheet->getHighestRow(); 
					$highestColumn = $objWorksheet->getHighestColumn(); 
					$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); 
					$rows = array();
					$cols = array();
					for ($row = 1; $row <= $highestRow; ++$row) {
					  for ($col = 0; $col <= $highestColumnIndex; ++$col) {
						$cell = $objWorksheet->getCellByColumnAndRow($col, $row);
						if ($cell->getValue() instanceof PHPExcel_RichText) {
							$rows[$row][$col] = $cell->getValue()->getPlainText();
						} else {
							$rows[$row][$col] = $cell->getValue();
							if(!empty($rows[$row][$col]) && is_numeric($rows[$row][$col]) && PHPExcel_Shared_Date::isDateTime($cell) && strlen($rows[$row][$col])) {
								$rows[$row][$col] = date('d-m-Y', PHPExcel_Shared_Date::ExcelToPHP($cell->getValue()));
							}
						}
					  }
					}
					$data = $rows;
				}
				unlink($filename);
			}
			if(!empty($data)){
				$flag = 0;
				foreach($data as $index => $values){
					if($flag == 0) $data_columns =  $values;
					else {
						$data_format[$index] = $values;
					}
					$flag = $index;
				}
				$original_text = $this->getTextOriginalYourForm();

				//QuanNV. Update ticket 787. Date 26/10/2020
				$original_text['address'] = 'Address'; //ticket 787 them field address
				$original_text['syn_comment'] = 'Comment'; //ticket 787 them field Synthesis Comment
				$original_text['syn_done'] = 'Done'; //ticket 787 them field Synthesis Done
				$original_text['syn_issues'] = 'Issue'; //ticket 787 them field Synthesis Issues
				$original_text['syn_risks'] = 'Risk'; //ticket 787 them field Synthesis Risks
				$original_text['category'] = 'Category'; //ticket 787 them field category
				asort($original_text); //ticket 787 sap xep lai.
				unset($original_text['']); //ticket 787 xoa field Project Details co key = 0
			}
		}
	
		$setting_matching = $this->HistoryFilter->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'path' => 'project_importers_setting_matching_fields',
				'employee_id' => $this->employee_info['Employee']['id']
			),
			'fields' => array('params'),
		));
			
		$setting_matching = !empty($setting_matching) ? @unserialize($setting_matching['HistoryFilter']['params']) : array(); 

		$this->set(compact('data_format', 'data_columns', 'original_text', 'setting_matching'));
		
	}

	public function getProjectName(){
		$this->loadModel('Project');
		
		$list_projects = $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $this->employee_info['Company']['id'],
			),
			'fields' => array('id', 'project_name')
		));
		
		return $list_projects;
	}
	
	public function save_import_model_projects(){
		$success = array();
		$datas = array();
		if(!empty($this->data)){
			$this->loadModels('Project', 'ProjectEmployeeManager');
			$datas = json_decode($this->data);
			$model_name = array();
			foreach($datas as $key => $value){
				$value = (array) $value;
				$project_name = substr(trim($value['model_named']), 0, 255);
				if(!empty($project_name)) $model_name[] = $project_name;
			}
			$project_model = $this->getProjectModel($model_name);
			$listModels = array();
			if(!empty($project_model)){
				foreach($project_model as $key => $pModel){
					$key_name = str_replace(' ', '_', strtolower($pModel['Project']['project_name']));
					$listModels[$key_name] = $pModel;
				}
			}
			foreach($datas as $key => $value){
				$value = (array) $value;
				// $project_model = array();
				$success[$value['id']] = false;
				if(!empty($value['project_name']) && !empty($value['model_named'])){
					$key_name = str_replace(' ', '_', strtolower(substr(trim($value['model_named']), 0, 255)));
					if(!empty($listModels[$key_name])){
						$saved = array();
						$data_model = $listModels[$key_name];
						$exist_project_id = $this->validatedProjectName(trim($value['project_name']));
						// check project is used in timesheet.
						$is_used  = 0;
						if(!empty($exist_project_id)){
							$is_used = $this->check_project_used($exist_project_id);
						}
						if($is_used == 0){
							// project_manager_id
							$list_employee_managers = array();
							if(!empty($value['project_manager_id'])){
								$project_manager = explode(",", trim($value['project_manager_id']));
								if(count($project_manager) > 0){
									$list_employee_managers['PM'] = $this->validatedModelProjectManager($project_manager);
								}
							}
							if(!empty($list_employee_managers)){
								$saved['project_name'] =  substr(trim($value['project_name']), 0, 255);
								
								// project_amr_program_id
								if(!empty($value['project_amr_program_id'])){
									$saved['project_amr_program_id'] = $this->validatedProgram($value['project_amr_program_id']);
								}
							
								// Default: the category is opportunity
								$project_id = 0;
								// Set empty the field default
								$old_start_date = time();
								if(!empty($data_model) && !empty($data_model['Project'])){
									$old_start_date = $data_model['Project']['start_date'];
									$data_model['Project'] = $saved + $data_model['Project'];
								}
								
								if(!empty($data_model)){
									if(!empty($exist_project_id)){
										$delete_project = $this->deleteProject($exist_project_id);
										if($delete_project){
											$project_id = $this->saveProjectFromModel($data_model);
										}
									}else{
										$project_id = $this->saveProjectFromModel($data_model);
									}
									if(!empty($project_id)){
										$success[$value['id']] = true;
										foreach($list_employee_managers as $type => $values){
											if(!empty($values)){
												foreach($values as $index => $employee_manager_id){
													$this->ProjectEmployeeManager->create();
													$this->ProjectEmployeeManager->save(array(
														'project_id' => $project_id,
														'company_id' => $this->employee_info['Company']['id'],
														'project_manager_id' => $employee_manager_id,
														'is_backup' => 0,
														'activity_id' => 0,
														'type' => $type,
														'is_profit_center' => 0,
													));
												}
											}
										}
										// skip date for project.
										if(!empty($value['start_date'])){
											$new_start_date = date('Y-m-d', strtotime($value['start_date']));
											$_duration = 0;
											if(strtotime($new_start_date) > strtotime($old_start_date)){
												$_duration = $this->skipDuration($old_start_date, $new_start_date);
											}else{
												$_duration = $this->skipDuration($new_start_date, $old_start_date);
												$_duration = 0 - $_duration;
											}
											
											$this->skipValue($project_id, $_duration);
										}
									}
								} 
							}
						}
					}
				}
			}
		}
		die(json_encode(array(
			'success' => $success,
			'data' => $datas,
			'message' => ''
		)));
		exit;
	}
	public function skipValue($modelId = null, $valueDay = 0){
		 $valueWeek = 0; $valueMonth = 0;
        $result = 'false';
        $this->loadModels('ProjectPhasePlan', 'NctWorkload');
            $projectTasks = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $modelId, 'predecessor' => null),
                'fields' => array('id', 'project_planed_phase_id', 'task_start_date', 'task_end_date', 'duration', 'parent_id', 'is_nct')
            ));
            $idOfNctTasks = $nctTasks = array();
            if(!empty($projectTasks)){
                $projectTasks = Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask');
                $listPhases = $saveYears = array();
                foreach($projectTasks as $taskId => $projectTask){
                    if($projectTask['task_start_date'] != '0000-00-00'){
                        $Y = date('Y', strtotime($projectTask['task_start_date']));
                        $saveYears[$Y] = $Y;
                    }
                    if($projectTask['is_nct']){
                        $idOfNctTasks[$taskId] = $taskId;
                        $isNCT = $this->NctWorkload->find('all', array(
                            'recursive' => -1,
                            'conditions' => array('project_task_id' => $taskId),
                        ));
                        if(!empty($isNCT)){
                            $nctTasks[$taskId] = $projectTask;
                        }
                        
                    } else {
                        if($projectTask['task_start_date'] != '0000-00-00'){
                            $_start = strtolower(date("l", strtotime($projectTask['task_start_date'])));
                            $startDate = strtotime($projectTask['task_start_date']);
                            if($_start == 'sunday'){
                                $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+1, date("Y", $startDate));
                            } elseif($_start == 'saturday') {
                                $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+2, date("Y", $startDate));
                            }
                            $projectTask['task_start_date'] = date('Y-m-d', $startDate);
                            if($valueDay >= 0){
                                $newStartDate = $this->_durationEndDate($projectTask['task_start_date'], '0000-00-00', $valueDay + 1);
                            } else {
                                $newStartDate = $this->_durationDiffEndDate($projectTask['task_start_date'], '0000-00-00', $valueDay);
                            }
							$task_duration = 1;
							if(!empty($projectTask['duration'])){
								$task_duration = $projectTask['duration'];
							}else if(!empty($projectTask['task_start_date']) && !empty($projectTask['task_end_date'])){
								$task_duration = $this->getWorkingDays($projectTask['task_start_date'], $projectTask['task_end_date'], null);
							}
                            $newEndDate = $this->_durationEndDate($newStartDate, '0000-00-00', $task_duration);
                            $saved = array(
                                'task_start_date' => $newStartDate,
                                'task_end_date' => $newEndDate,
                            );
                            $Y = date('Y', strtotime($newStartDate));
                            $saveYears[$Y] = $Y;
                            $Y = date('Y', strtotime($newEndDate));
                            $saveYears[$Y] = $Y;
                            $this->ProjectTask->id = $taskId;
                            $this->ProjectTask->save($saved);
                            if(!isset($listPhases[$projectTask['project_planed_phase_id']]['start'])){
                                $listPhases[$projectTask['project_planed_phase_id']]['start'] = array();
                            }
                            $listPhases[$projectTask['project_planed_phase_id']]['start'][] = strtotime($newStartDate);
                            if(!isset($listPhases[$projectTask['project_planed_phase_id']]['end'])){
                                $listPhases[$projectTask['project_planed_phase_id']]['end'] = array();
                            }
                            $listPhases[$projectTask['project_planed_phase_id']]['end'][] = strtotime($newEndDate);
                        }
                    }
                }
                 /**
                  * Lay du lieu cua table nct tasks
                  */
                $nctWorkloads = $this->NctWorkload->find('all', array(
                    'recursive' => -1,
                    'conditions' => array('project_task_id' => $idOfNctTasks),
                    'fields' => array('id', 'task_date', 'end_date', 'group_date', 'project_task_id')
                ));
                $taskDates = array();
                if(!empty($nctWorkloads)){
					$startHoliday = strtotime('01-01-' . (min($saveYears) - 3));
					$endHoliday = strtotime('31-12-' . (max($saveYears) + 3));
					$holidays   = array_keys(ClassRegistry::init('Holiday')->getOptionHolidays($startHoliday, $endHoliday, $company_id));
					$company_id = $this->employee_info['Company']['id'];
                    foreach($nctWorkloads as $nctWorkload){
                        $dx = $nctWorkload['NctWorkload'];
                        if(empty($dx['group_date'])){
                            if($valueDay >= 0){
                                $newStartDate = $this->_durationEndDate($dx['task_date'], '0000-00-00', $valueDay + 1);
                                $newStartDate = strtotime($newStartDate);
                                while(in_array($newStartDate, $holidays) || strtolower(date("l", $newStartDate)) == 'saturday' || strtolower(date("l", $newStartDate)) == 'sunday'){
                                    $newStartDate = mktime(0, 0, 0, date("m", $newStartDate), date("d", $newStartDate)+1, date("Y", $newStartDate));
                                }
                                $saved = array(
                                    'task_date' => date('Y-m-d', $newStartDate),
                                    'end_date' => date('Y-m-d', $newStartDate)
                                );
                            }else{
                                $newStartDate = $this->_durationDiffEndDate($dx['task_date'], '0000-00-00', $valueDay);
                                $newStartDate = strtotime($newStartDate);
                                while(in_array($newStartDate, $holidays) || strtolower(date("l", $newStartDate)) == 'saturday' || strtolower(date("l", $newStartDate)) == 'sunday'){
                                    $newStartDate = mktime(0, 0, 0, date("m", $newStartDate), date("d", $newStartDate)-1, date("Y", $newStartDate));
                                }
                                $saved = array(
                                    'task_date' => date('Y-m-d', $newStartDate),
                                    'end_date' => date('Y-m-d', $newStartDate)
                                );
                            }
                        } else {
                            $dateType = substr($dx['group_date'], 0, 1);
                            if($dateType == 1){
                                $startDate = strtotime($dx['task_date']);
                                $newStartDate = strtotime('+' . $valueWeek . ' week', $startDate);
                                $newEndDate = strtotime('friday this week', $newStartDate);
                            } else if($dateType == 2) {
                                $startDate = strtotime($dx['task_date']);
                                $newStartDate = strtotime('+' . $valueMonth . ' month', $startDate);
                                $newEndDate = strtotime('last day of this month', $newStartDate);
                            }
                            $saved = array(
                                'task_date' => date('Y-m-d', $newStartDate),
                                'end_date' => date('Y-m-d', $newEndDate),
                                'group_date' => $dateType . '_' . date('d-m-Y', $newStartDate) . '_' . date('d-m-Y', $newEndDate)
                            );
                        }
                        if(!isset($taskDates[$dx['project_task_id']])){
                            $taskDates[$dx['project_task_id']] = $newStartDate;
                        }
                        $taskDates[$dx['project_task_id']] = min($taskDates[$dx['project_task_id']], $newStartDate);
                        $this->NctWorkload->id = $dx['id'];
                        $this->NctWorkload->save($saved);
                    }
                }else if($valueDay == 0){
                    // for nct task haven't assign to
                    foreach($projectTasks as $taskId => $projectTask){
                        if($projectTask['task_start_date'] != '0000-00-00'){
                            $_start = strtolower(date("l", strtotime($projectTask['task_start_date'])));
                            $startDate = strtotime($projectTask['task_start_date']);

                            if($_start == 'sunday'){
                                $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+1, date("Y", $startDate));
                            } elseif($_start == 'saturday') {
                                $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+2, date("Y", $startDate));
                            }
                            $projectTask['task_start_date'] = date('Y-m-d', $startDate);
                            $newStartDate = strtotime('+' . $valueMonth . ' month', $startDate);

                            $newStartDate = date('Y-m-d', $newStartDate);
                            
                            $task_duration = 1;
							if(!empty($projectTask['duration'])){
								$task_duration = $projectTask['duration'];
							}else if(!empty($projectTask['task_start_date']) && !empty($projectTask['task_end_date'])){
								$task_duration = $this->getWorkingDays($projectTask['task_start_date'], $projectTask['task_end_date'], null);
							}
						
                            $newEndDate = $this->_durationEndDate($newStartDate, '0000-00-00', $task_duration);
                           
                            $saved = array(
                                'task_start_date' => $newStartDate,
                                'task_end_date' => $newEndDate,
                            );
                            $Y = date('Y', strtotime($newStartDate));
                            $saveYears[$Y] = $Y;
                            $Y = date('Y', strtotime($newEndDate));
                            $saveYears[$Y] = $Y;
                            $this->ProjectTask->id = $taskId;
                            $this->ProjectTask->save($saved);
                            if(!isset($listPhases[$projectTask['project_planed_phase_id']]['start'])){
                                $listPhases[$projectTask['project_planed_phase_id']]['start'] = array();
                            }
                            $listPhases[$projectTask['project_planed_phase_id']]['start'][] = strtotime($newStartDate);
                            if(!isset($listPhases[$projectTask['project_planed_phase_id']]['end'])){
                                $listPhases[$projectTask['project_planed_phase_id']]['end'] = array();
                            }
                            $listPhases[$projectTask['project_planed_phase_id']]['end'][] = strtotime($newEndDate);
                        }
                    }
                }
            }
            if(!empty($taskDates) && !empty($nctTasks)){
                foreach($nctTasks as $pTaskId => $nctTask){
                    $newStartDate = date('Y-m-d', $taskDates[$pTaskId]);
                    $task_duration = 1;
					if(!empty($projectTask['duration'])){
						$task_duration = $projectTask['duration'];
					}else if(!empty($projectTask['task_start_date']) && !empty($projectTask['task_end_date'])){
						$task_duration = $this->getWorkingDays($projectTask['task_start_date'], $projectTask['task_end_date'], null);
					}
				
					$newEndDate = $this->_durationEndDate($newStartDate, '0000-00-00', $task_duration);
                    $saved = array(
                        'task_start_date' => $newStartDate,
                        'task_end_date' => $newEndDate,
                    );
					
                    $this->ProjectTask->id = $pTaskId;
                    $this->ProjectTask->save($saved);
                    if(!isset($listPhases[$nctTask['project_planed_phase_id']]['start'])){
                        $listPhases[$nctTask['project_planed_phase_id']]['start'] = array();
                    }
                    $listPhases[$nctTask['project_planed_phase_id']]['start'][] = strtotime($newStartDate);
                    if(!isset($listPhases[$nctTask['project_planed_phase_id']]['end'])){
                        $listPhases[$nctTask['project_planed_phase_id']]['end'] = array();
                    }
                    $listPhases[$nctTask['project_planed_phase_id']]['end'][] = strtotime($newEndDate);
                }
            }
            /**
             * thao tac thuc hien luu cac predecessor
             */
            $projectTasks = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $modelId),
                'fields' => array('id', 'project_planed_phase_id', 'task_start_date', 'task_end_date', 'duration', 'parent_id', 'predecessor')
            ));
            $listTaskPres = array();
            if(!empty($projectTasks)){
                foreach($projectTasks as $projectTask){
                    $dx = $projectTask['ProjectTask'];
                    if(!empty($dx['predecessor'])){
                        $listTaskPres[$dx['id']] = $dx;
                    }
                }
                $projectTasks = Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask');
                if(!empty($listTaskPres)){
                    foreach($listTaskPres as $id => $listTaskPre){
                        $_start = !empty($projectTasks[$listTaskPre['predecessor']]['task_end_date']) ? $projectTasks[$listTaskPre['predecessor']]['task_end_date'] : '0000-00-00';
                        if($_start != '0000-00-00'){
                            $startDate = strtotime($_start);
                            $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+1, date("Y", $startDate));
                            $checkDay = strtolower(date("l", $startDate));
                            if($checkDay == 'sunday'){
                                $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+1, date("Y", $startDate));
                            } elseif($checkDay == 'saturday') {
                                $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+2, date("Y", $startDate));
                            }

                            $task_duration = 1;
							if(!empty($projectTask['duration'])){
								$task_duration = $projectTask['duration'];
							}else if(!empty($projectTask['task_start_date']) && !empty($projectTask['task_end_date'])){
								$task_duration = $this->getWorkingDays($projectTask['task_start_date'], $projectTask['task_end_date'], null);
							}
						
                            $newEndDate = $this->_durationEndDate($newStartDate, '0000-00-00', $task_duration);
                            $saved = array(
                                'task_start_date' => $newStartDate,
                                'task_end_date' => $newEndDate,
                            );
                            $this->ProjectTask->id = $id;
                            $this->ProjectTask->save($saved);
                            if(!isset($listPhases[$projectTask['ProjectTask']['project_planed_phase_id']]['start'])){
                                $listPhases[$projectTask['ProjectTask']['project_planed_phase_id']]['start'] = array();
                            }
                            $listPhases[$projectTask['ProjectTask']['project_planed_phase_id']]['start'][] = strtotime($newStartDate);
                            if(!isset($listPhases[$projectTask['ProjectTask']['project_planed_phase_id']]['end'])){
                                $listPhases[$projectTask['ProjectTask']['project_planed_phase_id']]['end'] = array();
                            }
                            $listPhases[$projectTask['ProjectTask']['project_planed_phase_id']]['end'][] = strtotime($newEndDate);
                        }
                    }
                }
            }
			$allProjectPlans = $this->ProjectPhasePlan->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'project_id' => $modelId,
					'NOT' => array('phase_real_start_date' => '0000-00-00', 'phase_real_end_date' => '0000-00-00')
				),
				'fields' => array('id', 'phase_real_start_date', 'phase_real_end_date', 'phase_planed_start_date', 'phase_planed_end_date')
			));
			$allProjectPlans = !empty($allProjectPlans) ? Set::combine($allProjectPlans, '{n}.ProjectPhasePlan.id', '{n}.ProjectPhasePlan') : array();
            if(!empty($listPhases)){
                foreach($listPhases as $planId => $list){
					if(!empty($allProjectPlans[$planId])){
						unset($allProjectPlans[$planId]);
					}
                    $realStart = min($list['start']);
                    $realEnd = max($list['end']);
                    $saved = array(
                        'phase_real_start_date' => date('Y-m-d', $realStart),
                        'phase_planed_start_date' => date('Y-m-d', $realStart),
                        'phase_real_end_date' => date('Y-m-d', $realEnd),
                        'phase_planed_end_date' => date('Y-m-d', $realEnd)
                    );
                    $this->ProjectPhasePlan->id = $planId;
                    $this->ProjectPhasePlan->save($saved);
                }
            }
			if(!empty($allProjectPlans)){
				// Skip date of phase no task 
				foreach($allProjectPlans as $id => $phasePlan){
					if($valueDay >= 0){
						$newPlanedStartDate = $this->_durationEndDate($phasePlan['phase_planed_start_date'], '0000-00-00', $valueDay + 1);
						$newRealStartDate = $this->_durationEndDate($phasePlan['phase_real_start_date'], '0000-00-00', $valueDay + 1);
					} else {
						$newPlanedStartDate = $this->_durationDiffEndDate($phasePlan['phase_planed_start_date'], '0000-00-00', $valueDay);
						$newRealStartDate = $this->_durationDiffEndDate($phasePlan['phase_real_start_date'], '0000-00-00', $valueDay);
					}
					
					$planed_duration = $this->getWorkingDays($phasePlan['phase_planed_start_date'], $phasePlan['phase_planed_end_date'], null);
					$real_duration = $this->getWorkingDays($phasePlan['phase_real_start_date'], $phasePlan['phase_real_start_date'], null);
					
					$newPlanedEndDate = $this->_durationEndDate($newPlanedStartDate, '0000-00-00', $planed_duration);
					$newRealEndDate = $this->_durationEndDate($newRealStartDate, '0000-00-00', $real_duration);
					
					$saved = array(
                        'phase_real_start_date' => $newRealStartDate,
                        'phase_planed_start_date' => $newPlanedStartDate,
                        'phase_real_end_date' => $newRealEndDate,
                        'phase_planed_end_date' => $newPlanedEndDate
                    );
                    $this->ProjectPhasePlan->id = $id;
                    $this->ProjectPhasePlan->save($saved);
				}
			}
			// debug($allProjectPlans);
			// exit;
            $this->_saveStartEndDateAllTask($modelId);
            $result = 'true';
		
        return $result;
        // $this->set(compact('result'));
    }
	 private function _durationDiffEndDate($startDate, $endDate, $duration){
        $_durationEndDate = '';
        if((strtotime($endDate) == '' || $endDate == '0000-00-00') && !empty($duration)){
            $dates_ranges[]= $startDate;
            $_startDate = strtotime($startDate);
            $s = $_startDate = mktime(0, 0, 0, date("m", $_startDate), date("d", $_startDate)+$duration, date("Y", $_startDate));
            $startDateCheck = strtotime($startDate);
            $_diffDates = 0;
            while($_startDate <= $startDateCheck){
                $dates_range = $_startDate;
                $_dateFitter = strtolower(date("l", $dates_range));
                if($_dateFitter == 'saturday' || $_dateFitter == 'sunday'){
                    $_diffDates++;
                }
                $_startDate = mktime(0, 0, 0, date("m", $_startDate), date("d", $_startDate)+1, date("Y", $_startDate));
            }
            for($i = 0; $i < $_diffDates; $i++){
                $s = mktime(0, 0, 0, date("m", $s), date("d", $s)-1, date("Y", $s));
                $_Fitter = strtolower(date("l", $s));
                if($_Fitter == 'saturday' || $_Fitter == 'sunday'){
                    $_diffDates++;
                }
            }
            $_end = date("Y-m-d", $s);
            $_durationEndDate = $_end;
        } else {
            $_durationEndDate = $endDate;
        }
        return $_durationEndDate;
    }
	private function _getHoliday($startDate, $endDate){
	     $this->loadModel('Holiday');
        $company_id = $this->employee_info['Company']['id'];
        $holidays = $this->Holiday->getOptionHolidays(strtotime($startDate), strtotime($endDate), $company_id);
        $_holiday = array();
        if ($startDate < $endDate) {
            $startDate = strtotime($startDate);
            $endDate = strtotime($endDate);

            while ($startDate <= $endDate){
                $_start = strtolower(date("l", $startDate));
                $_end = strtolower(date("l", $endDate));
                if($_start == 'saturday' || $_start == 'sunday' || in_array($startDate, array_keys($holidays))){
                    $_holiday[] = date("m-d-Y", $startDate);
                }
                $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+1, date("Y", $startDate));
            }
        }
        return $_holiday;
    }
	public function skipDuration($startDate, $endDate){
		$working = ClassRegistry::init('Workday')->getOptions($this->employee_info['Company']['id']);
		$duration = 0;
		$_startDate = strtotime($startDate);
		$_endDate = strtotime($endDate);
		while($_startDate < $_endDate){
			$day_of_week = strtolower(date("l", $_startDate));
			if((int)$working[$day_of_week] == 1) $duration++;
			$_startDate = mktime(0, 0, 0, date("m", $_startDate), date("d", $_startDate)+1, date("Y", $_startDate));
		}
		return $duration;
	}
	public function getWorkingDays($startDate, $endDate, $duration){
        $_durationDate = '';
        if($startDate != '0000-00-00' && $endDate != '0000-00-00'){
            if ($startDate <= $endDate) {
				$_holiday = $this->_getHoliday($startDate, $endDate);
				$_holiday = count($_holiday);
                $dates_range[]= $startDate;
                $startDate = strtotime($startDate);
                $endDate = strtotime($endDate);
                $_date = 0;
                while ($startDate <= $endDate){
                    $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+1, date("Y", $startDate));
                        $dates_range[]=date('Y-m-d', $startDate);
                        $_date++;
                }
                if($_holiday != 0){
                    $_date = $_date - $_holiday;
                }
            } else {
                $_date = 0;
            }
            $_durationDate = $_date;
        } else {
            $_durationDate = $duration;
        }
        return $_durationDate;
    }
	private function _getStartEndDateAllTask($project_id) {
        $this->_checkRole(false, $project_id);
        $this->loadModel('ProjectPhasePlan');
        $data = array();
        $projectPlans = $this->ProjectPhasePlan->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
                'NOT' => array('phase_real_start_date' => '0000-00-00', 'phase_real_end_date' => '0000-00-00')
            ),
            'fields' => array(
                'MIN(phase_real_start_date) AS startDate',
                'MAX(phase_real_end_date) AS endDate'
            )
        ));
        $data['task_start_date'] = isset($projectPlans[0][0]['startDate']) ? $projectPlans[0][0]['startDate'] : 0;
        $data['task_end_date'] = isset($projectPlans[0][0]['endDate']) ? $projectPlans[0][0]['endDate'] : 0;
        $data['initial_task_start_date'] = isset($projectPlans[0][0]['startDate']) ? $projectPlans[0][0]['startDate'] : 0;
        $data['initial_task_end_date'] = isset($projectPlans[0][0]['endDate']) ? $projectPlans[0][0]['endDate'] : 0;
        return $data;
    }
	 private function _saveStartEndDateAllTask($project_id) {
        $this->_checkRole(false, $project_id);
        $this->loadModel('Project');
		$_duration = 0;
        $data = $this->_getStartEndDateAllTask($project_id);
        if(!empty($data)){
            $_data['start_date'] = $data['task_start_date'];
            $_data['end_date'] = $data['task_end_date'];
            $this->Project->id = $project_id;
            if($this->Project->save($_data)){
				$_duration = $this->getWorkingDays($_data['start_date'], $_data['end_date'], null);
			}
        }
		return $_duration;
    }
	 private function _durationEndDate($startDate, $endDate, $duration){
        $_durationEndDate = '';
        if((strtotime($endDate) == '' || $endDate == '0000-00-00') && !empty($duration)){
            $dates_ranges[]= $startDate;
            $_startDate = strtotime($startDate);
            $_startDate = mktime(0, 0, 0, date("m", $_startDate), date("d", $_startDate)+$duration, date("Y", $_startDate));
            $startDateCheck = strtotime($startDate);
            $_addDates = 0;
            while($startDateCheck <= $_startDate){
                $dates_range = $startDateCheck;
                $_dateFitter = strtolower(date("l", $dates_range));
                if($_dateFitter == 'saturday' || $_dateFitter == 'sunday'){
                    $_addDates++;
                }
                $startDateCheck = mktime(0, 0, 0, date("m", $startDateCheck), date("d", $startDateCheck)+1, date("Y", $startDateCheck));
            }
            if($_addDates <= 1){
                $_startDate = mktime(0, 0, 0, date("m", $_startDate), date("d", $_startDate)-1, date("Y", $_startDate));
            } else {
                for($i = 1; $i < $_addDates; $i++){
                    $_startDate = mktime(0, 0, 0, date("m", $_startDate), date("d", $_startDate)+1, date("Y", $_startDate));
                    $_Fitter = strtolower(date("l", $_startDate));
                    if($_Fitter == 'saturday' || $_Fitter == 'sunday'){
                        $_addDates++;
                    }
                }
            }
            $_end = date("Y-m-d", $_startDate);
            $_durationEndDate = $_end;
        } else {
            $_durationEndDate = $endDate;
        }
        return $_durationEndDate;
    }
	/* Duplicate when import
	 * Khi update code function nay, nho update cac function lien quan
	 * projects/duplicate
	 * projects/duplicateProject
	 * project_importers/saveProjectFromModel
	 */
	public function saveProjectFromModel($new_record){
		$project_id = 0;
		$this->loadModels('Project');
		$oldId = $new_record['Project']['id'];
		$this->_checkRole(false, $oldId);
		$this->Project->id = $oldId;
		$this->Project->saveField('copy_number', $new_record['Project']['copy_number'] + 1);
		
		$new_record['Project']['project_copy'] = 1;
		$new_record['Project']['project_copy_id'] = $new_record['Project']['id'];
		$new_record['Project']['category'] = 2;  //when copy project, default project status = 2 (Opportunity).
		unset($new_record['Project']['id']);
		$this->Project->create();
		$new_record['Project']['copy_number'] = 0;
		$new_record['Project']['created'] = time();
		$new_record['Project']['updated'] = time();
		$new_record['Project']['last_modified'] = time();
		$new_record['Project']['project_code_1'] = null;
		unset($new_record['Project']['activity_id']);
		$pSave = $this->Project->save($new_record);
		if($pSave){
			$id_duplicate = $this->Project->getLastInsertID();
			$project_id = $id_duplicate; 
			//Copy record of ProjectTeam
			if (!empty($new_record['ProjectTeam'])) {
				foreach ($new_record['ProjectTeam'] as $pteam) {
					$pteam['project_id'] = $id_duplicate;
					$team_id = $pteam['id'];
					unset($pteam['id']);
					$this->ProjectTeam->create();
					$this->ProjectTeam->save($pteam);
					$pteam_id = $this->ProjectTeam->getLastInsertID();

					$pteam_cur_id = $team_id;
					$this->ProjectFunctionEmployeeRefer->recursive = -1;
					$PFERs = $this->ProjectFunctionEmployeeRefer->find('all', array(
						'conditions' => array('project_team_id' => $pteam_cur_id)));
					if (!empty($PFERs)) {
						foreach ($PFERs as $PFER) {
							unset($PFER['ProjectFunctionEmployeeRefer']['id']);
							$PFER['ProjectFunctionEmployeeRefer']['project_team_id'] = $pteam_id;
							$this->ProjectFunctionEmployeeRefer->create();
							$this->ProjectFunctionEmployeeRefer->save($PFER);
						}
					}
				}
			}
			//Copy record of ProjectPart
			$id_part_new = 0;
			$this->loadModel('ProjectPart');
			$newPart = array();
			if (!empty($new_record['ProjectPart'])) {
				foreach ($new_record['ProjectPart'] as $pparts) {
					$id_part_old = $pparts['id'];
					$pparts['project_id'] = $id_duplicate;
					unset($pparts['id']);
					$this->ProjectPart->create();
					$this->ProjectPart->save($pparts);
					$id_part_new = $this->ProjectPart->getLastInsertID();
					if (!empty($new_record['ProjectPhasePlan'])) {
						foreach ($new_record['ProjectPhasePlan'] as $pphase) {
							if($pphase['project_part_id'] == $id_part_old){
								$newPart[$pphase['id']] = $id_part_new;
							}
						}
					}
				}
			}
			//Copy record of ProjectMilestone
			$milestone_refer = array();
			if (!empty($new_record['ProjectMilestone'])) {
				foreach ($new_record['ProjectMilestone'] as $pmiles) {
					$pmiles['project_id'] = $id_duplicate;
					$oldMilesId = $pmiles['id'];
					unset($pmiles['id']);
					$this->ProjectMilestone->create();
					if($this->ProjectMilestone->save($pmiles)){
						$newMilesId = $this->ProjectMilestone->getLastInsertID();
						$milestone_refer[$oldMilesId] = $newMilesId;
					}
					
				}
			}
			//Copy record of ProjectPhasePlan
			if (!empty($new_record['ProjectPhasePlan'])) {
				$project_planed_phase_id_old = $listPhasePredes = $listPhaseIds = array();
				foreach ($new_record['ProjectPhasePlan'] as $pphase) {
					$pphase['project_id'] = $id_duplicate;
					$ProjectPhasePlan_id_old = $pphase['id'];
					$oldPhaseId = $pphase['id'];
					if (!empty($pphase['predecessor'])) {
						$listPhasePredes[$oldPhaseId] = $pphase['predecessor'];
					}
					
					if(!empty($newPart)){
						foreach($newPart as $key => $value){
							if($pphase['id'] == $key){
								$pphase['project_part_id'] = $value;
							}
						}
					}
					
					unset($pphase['id']);
					
					
					$this->ProjectPhasePlan->create();
					$this->ProjectPhasePlan->save($pphase);
					$newPhaseId = $this->ProjectPhasePlan->getLastInsertID();
					$listPhaseIds[$oldPhaseId] = $newPhaseId;
					$project_planed_phase_id_old[$ProjectPhasePlan_id_old] = $this->ProjectPhasePlan->getLastInsertID();
				}
				
				if (!empty($listPhasePredes)) {
					// debug($listPhasePredes);
					foreach ($listPhasePredes as $old => $pre) {
						$idUpdate = !empty($listPhaseIds[$old]) ? $listPhaseIds[$old] : 0;
						$this->ProjectPhasePlan->id = $idUpdate;
						$saved['predecessor'] = !empty($listPhaseIds[$pre]) ? $listPhaseIds[$pre] : '';
						$this->ProjectPhasePlan->save($saved);
					}
				}
			}
			//Copy record of ProjectTask
			//co project_id cu, get tat ca  cac task co project_planed_phase_id giu lai
			$company_id = $this->employee_info['Company']['id'];
			$this->loadModels('ProjectTaskEmployeeRefer', 'Employee', 'ProfitCenter', 'NctWorkload', 'ProjectTaskTxt', 'ProjectTaskAttachment');
			$listEmployeeActive = $this->Employee->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company_id,
					'actif' => 1,
					'OR' => array(
						'start_date IS NULL',
						'start_date' => '0000-00-00',
						'start_date <=' => date('Y-m-d', time())
					),
					'AND' => array(
						'OR' => array(
							'end_date IS NULL',
							'end_date' => '0000-00-00',
							'end_date >=' => date('Y-m-d', time())
						)
					)
				),
				'fields' => array('id', 'id')
			));
			$listProfitCenterActive = $this->ProfitCenter->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company_id,
				),
				'fields' => array('id', 'id')
			));
			if (!empty($new_record['ProjectTask'])) {
				$listTaskIds = $listTaskPredes = array();
				$old_comment = array();
				$all_tasks = array();
				/* Parent task duoc copy truoc. Sau do la cac task con lai */
				$tasks = $new_record['ProjectTask'];
				$tasks = Set::combine( $new_record['ProjectTask'], '{n}.id', '{n}');
				$parent_tasks = Set::classicExtract( $tasks, '{n}.parent_id');
				$parent_tasks = array_unique( $parent_tasks);
				foreach( $parent_tasks as $t_id){
					if( !empty($t_id))$all_tasks[] = $tasks[$t_id];
					unset($tasks[$t_id]);
				}
				$all_tasks = array_merge($all_tasks, $tasks);
				foreach ($all_tasks as $ptask) {
					$ptask['project_id'] = $id_duplicate;
					$ptask['project_planed_phase_id'] = $project_planed_phase_id_old[$ptask['project_planed_phase_id']];
					$oldTaskId = $ptask['id'];
					if (!empty($ptask['parent_id'])) {
						$ptask['parent_id'] = !empty($listTaskIds[$ptask['parent_id']]) ? $listTaskIds[$ptask['parent_id']] : 0;
					}
					if (!empty($ptask['predecessor'])) {
						$listTaskPredes[$oldTaskId] = $ptask['predecessor'];
					}
					$projectTaskEmpRefers = $this->ProjectTaskEmployeeRefer->find('all', array(
						'recursive' => -1,
						'conditions' => array(
							'project_task_id' => $oldTaskId
						),
						'fields' => array('reference_id', 'project_task_id', 'is_profit_center', 'estimated')
					));
					$projectTaskTxts = $this->ProjectTaskTxt->find('all', array(
						'recursive' => -1,
						'conditions' => array(
							'project_task_id' => $oldTaskId
						)
					));
					$projectTaskAttachments = $this->ProjectTaskAttachment->find('all', array(
						'recursive' => -1,
						'conditions' => array(
							'task_id' => $oldTaskId
						)
					));

					$listNCT = $this->NctWorkload->find('all', array(
						'recursive' => -1,
						'conditions' => array(
							'project_task_id' => $oldTaskId
						),
						'fields' => array('task_date', 'estimated', 'reference_id', 'is_profit_center', 'project_task_id', 'activity_task_id', 'group_date', 'end_date'),
					));
					unset($ptask['task_completed']);
					unset($ptask['task_assign_to']);
					unset($ptask['task_real_end_date']);
					unset($ptask['initial_estimated']);
					unset($ptask['overload']);
					unset($ptask['id']);
					unset($ptask['initial_task_start_date']);
					unset($ptask['initial_task_end_date']);
					unset($ptask['manual_consumed']);
					unset($ptask['manual_overload']);
					unset($ptask['created']);
					unset($ptask['updated']);
					$old_comment = $ptask['text_1'];
					unset($ptask['text_1']); // Remove comment
					$old_attachment = $ptask['attachment'];
					unset($ptask['attachment']); // Remove attachment
					unset($ptask['special_consumed']);

					// Edit comment info
					$ptask['text_updater'] = null;
					$ptask['text_time'] = null;
					
					// Milestone reference
					if(!empty($milestone_refer) && !empty($ptask['milestone_id']) && !empty($milestone_refer[$ptask['milestone_id']])){
						$ptask['milestone_id'] = $milestone_refer[$ptask['milestone_id']];
					}
					$this->ProjectTask->create();
					$_new_task = $this->ProjectTask->save($ptask);
					
					// Copy comment
					$_task_comment = $this->ProjectTaskTxt->find('all', array(
						'recursive' => -1,
						'conditions' => array(
							'project_task_id' => $oldTaskId
						),
					));
					$newTaskId = $this->ProjectTask->getLastInsertID();
					$listTaskIds[$oldTaskId] = $newTaskId;
					$estimatedOfNewTask = 0;
					foreach ($projectTaskEmpRefers as $key => $projectTaskEmpRefer) {
						$dx = $projectTaskEmpRefer['ProjectTaskEmployeeRefer'];
						if (($dx['is_profit_center'] == 1 && in_array($dx['reference_id'], $listProfitCenterActive)) || ($dx['is_profit_center'] == 0 && in_array($dx['reference_id'], $listEmployeeActive))) {
							$this->ProjectTaskEmployeeRefer->create();
							$this->ProjectTaskEmployeeRefer->save(array(
								'reference_id' => $dx['reference_id'],
								'project_task_id' => $newTaskId,
								'is_profit_center' => $dx['is_profit_center'],
								'estimated' => $dx['estimated']
							));
							$estimatedOfNewTask += !empty($dx['estimated']) ? $dx['estimated'] : 0;
						}
					}
					$this->ProjectTask->id = $newTaskId;
					$this->ProjectTask->save(array('estimated' => $estimatedOfNewTask));

					// Copy comment
					if (!empty($old_comment)) {
						$this->ProjectTaskTxt->create();
						$this->ProjectTaskTxt->save(array(
							'employee_id' => $this->employee_info['Employee']['id'],
							'project_task_id' => $newTaskId,
							'comment' => $old_comment,
							'created' => date('Y-m-d H:i:s')
						));
					}
					foreach ($projectTaskTxts as $key => $projectTaskTxt) {
						$task_txt = $projectTaskTxt['ProjectTaskTxt'];
						$this->ProjectTaskTxt->create();
						$this->ProjectTaskTxt->save(array(
							'employee_id' => $this->employee_info['Employee']['id'],
							'project_task_id' => $newTaskId,
							'comment' => $task_txt['comment'],
							'created' => date('Y-m-d H:i:s')
						));
					}
					// copy Attachments
					if (!empty($old_attachment)) {
						$_is_file = 0;
						$file_attachment = $url_attachment = array();
						$file_attachment = explode('file:', $old_attachment, 2);
						$url_attachment = explode('url:', $old_attachment, 2);
						$old_attachment = '';
						if ((!$url_attachment[0]) && isset($url_attachment[1])) {
							$old_attachment = $url_attachment[1];
						}
						if ((!$file_attachment[0]) && isset($file_attachment[1])) {
							$old_attachment = $file_attachment[1];
							$_is_file = 1;
						}
						$new_attachment = '';
						if ($_is_file && $old_attachment) {
							// Copy file sang new task theo định dạng filename(<num>).ext
							$new_attachment = $this->copy_task_attachment($old_attachment);
						}
						$new_attachment = !empty($new_attachment) ? $new_attachment : (!$_is_file ? $old_attachment : '');
						if (!empty($new_attachment)) {
							$this->ProjectTaskAttachment->create();
							$this->ProjectTaskAttachment->save(array(
								'project_id' => $id_duplicate,
								'task_id' => $newTaskId,
								'employee_id' => $this->employee_info['Employee']['id'],
								'attachment' => $new_attachment,
								'created' => time(),
								'updated' => time(),
								'is_file' => $_is_file,
								'is_https' => 0,
							));
						}
					}
					foreach ($projectTaskAttachments as $key => $projectTaskAttachment) {
						$task_att = $projectTaskAttachment['ProjectTaskAttachment'];
						$new_attachment = '';
						if ($task_att['is_file'] && !empty($task_att['attachment'])) {
							// Copy file sang new task theo định dạng filename(<num>).ext
							$new_attachment = $this->copy_task_attachment($task_att['attachment']);
						}
						if (!$task_att['is_file']) {
							$new_attachment = $task_att['attachment'];
						}
						if ($new_attachment) {
							$this->ProjectTaskAttachment->create();
							$this->ProjectTaskAttachment->save(array(
								'project_id' => $id_duplicate,
								'task_id' => $newTaskId,
								'employee_id' => $this->employee_info['Employee']['id'],
								'attachment' => $new_attachment,
								'created' => time(),
								'updated' => time(),
								'is_file' => $task_att['is_file'],
								'is_https' => $task_att['is_https'],
							));
						}
					}
					// End copy Attachments

					if (!empty($listNCT)) {
						foreach ($listNCT as $key => $value) {
							if ($ptask['is_nct']) {
								$nct_fields = array(
									'task_date' => $value['NctWorkload']['task_date'],
									'estimated' => $value['NctWorkload']['estimated'],
									'reference_id' => $value['NctWorkload']['reference_id'],
									'is_profit_center' => $value['NctWorkload']['is_profit_center'],
									'project_task_id' => $newTaskId,
									'activity_task_id' => 0,
									'group_date' => $value['NctWorkload']['group_date'],
									'end_date' => $value['NctWorkload']['end_date'],
								);
								$this->NctWorkload->create();
								$this->NctWorkload->save($nct_fields);
							}
						}
					}
				}
				if (!empty($listTaskPredes)) {
					foreach ($listTaskPredes as $old => $pre) {
						$idUpdate = !empty($listTaskIds[$old]) ? $listTaskIds[$old] : 0;
						$this->ProjectTask->id = $idUpdate;
						$saved['predecessor'] = !empty($listTaskIds[$pre]) ? $listTaskIds[$pre] : '';
						$this->ProjectTask->save($saved);
					}
				}
			}
			//Copy record of ProjectRisk
			if (!empty($new_record['ProjectRisk'])) {
				foreach ($new_record['ProjectRisk'] as $prisk) {
					$prisk['project_id'] = $id_duplicate;
					unset($prisk['id']);
					$this->ProjectRisk->create();
					$this->ProjectRisk->save($prisk);
				}
			}

			//Copy record of ProjectIssue
			if (!empty($new_record['ProjectIssue'])) {
				foreach ($new_record['ProjectIssue'] as $pissue) {
					$pissue['project_id'] = $id_duplicate;
					unset($pissue['id']);
					$this->ProjectIssue->create();
					$this->ProjectIssue->save($pissue);
				}
			}

			//Copy record of ProjectDecision
			if (!empty($new_record['ProjectDecision'])) {
				foreach ($new_record['ProjectDecision'] as $pdecision) {
					$pdecision['project_id'] = $id_duplicate;
					unset($pdecision['id']);
					$this->ProjectDecision->create();
					$this->ProjectDecision->save($pdecision);
				}
			}

			//Copy record of ProjectEvolution
			if (!empty($new_record['ProjectLivrable'])) {
				foreach ($new_record['ProjectLivrable'] as $plivra) {
					$plivra['project_id'] = $id_duplicate;
					$plivra_id = $plivra['id'];
					unset($plivra['id']);
					$this->ProjectLivrable->create();
					$this->ProjectLivrable->save($plivra);

					$plivra_new_id = $this->ProjectLivrable->getLastInsertID();
					$plivra_cur_id = $plivra_id;
					$this->ProjectLivrableActor->recursive = -1;
					$PFERs = $this->ProjectLivrableActor->find('all', array(
						'conditions' => array('ProjectLivrableActor.project_livrable_id' => $plivra_cur_id)));
					if (!empty($PFERs)) {
						foreach ($PFERs as $PFER) {
							unset($PFER['ProjectLivrableActor']['id']);
							$PFER['ProjectLivrableActor']['project_livrable_id'] = $plivra_new_id;
							$PFER['ProjectLivrableActor']['project_id'] = $id_duplicate;
							$this->ProjectLivrableActor->create();
							$this->ProjectLivrableActor->save($PFER);
						}
					}
				}
			}

			//Copy record of ProjectEvolution
			if (!empty($new_record['ProjectEvolution'])) {
				foreach ($new_record['ProjectEvolution'] as $pevolu) {
					$pevolu['project_id'] = $id_duplicate;
					$pevolu_id = $pevolu['id'];
					unset($pevolu['id']);
					$this->ProjectEvolution->create();
					$this->ProjectEvolution->save($pevolu);
					$pevolu_new_id = $this->ProjectEvolution->getLastInsertID();

					$pevolu_cur_id = $pevolu_id;
					$this->ProjectEvolutionImpactRefer->recursive = -1;
					$PFERs = $this->ProjectEvolutionImpactRefer->find('all', array(
						'conditions' => array('project_evolution_id' => $pevolu_cur_id)));
					if (!empty($PFERs)) {
						foreach ($PFERs as $PFER) {
							unset($PFER['ProjectEvolutionImpactRefer']['id']);
							$PFER['ProjectEvolutionImpactRefer']['project_evolution_id'] = $pevolu_new_id;
							$PFER['ProjectEvolutionImpactRefer']['project_id'] = $id_duplicate;
							$this->ProjectEvolutionImpactRefer->create();
							$this->ProjectEvolutionImpactRefer->save($PFER);
						}
					}
				}
			}

			//Copy record of ProjectAmr
			if (!empty($new_record['ProjectAmr'])) {
				foreach ($new_record['ProjectAmr'] as $pamr) {
					$pamr['project_id'] = $id_duplicate;
					unset($pamr['id']);
					$this->ProjectAmr->create();
					$this->ProjectAmr->save($pamr);
				}
			}

			//Copy record of Created value
			if (!empty($new_record['ProjectCreatedVal'])) {
				foreach ($new_record['ProjectCreatedVal'] as $pcreated) {
					$pcreated['project_id'] = $id_duplicate;
					unset($pcreated['id']);
					$this->ProjectCreatedVal->create();
					$this->ProjectCreatedVal->save($pcreated);
				}
			}
			//Copy Budget Internal Cost.
			if (!empty($new_record['ProjectBudgetInternal'])) {
				foreach ($new_record['ProjectBudgetInternal'] as $internal) {
					$internal['project_id'] = $id_duplicate;
					unset($internal['id']);
					$internal['activity_id'] = 0;
					$this->ProjectBudgetInternal->create();
					$this->ProjectBudgetInternal->save($internal);
				}
			}
			//Copy Budget Internal Cost Detail.
			if (!empty($new_record['ProjectBudgetInternalDetail'])) {
				foreach ($new_record['ProjectBudgetInternalDetail'] as $internalDetail) {
					$internalDetail['project_id'] = $id_duplicate;
					unset($internalDetail['id']);
					$internalDetail['activity_id'] = 0;
					$this->ProjectBudgetInternalDetail->create();
					$this->ProjectBudgetInternalDetail->save($internalDetail);
				}
			}

			//Copy Budget External Cost.
			if (!empty($new_record['ProjectBudgetExternal'])) {
				foreach ($new_record['ProjectBudgetExternal'] as $external) {
					$external['project_id'] = $id_duplicate;
					unset($external['id']);
					$external['activity_id'] = 0;
					$this->ProjectBudgetExternal->create();
					$this->ProjectBudgetExternal->save($external);
				}
			}

			//Copy Budget External Cost.
			if (!empty($new_record['ProjectBudgetSale'])) {
				foreach ($new_record['ProjectBudgetSale'] as $sale) {
					$sale['project_id'] = $id_duplicate;
					$oldSaleId = $sale['id'];
					unset($sale['id']);
					$sale['activity_id'] = 0;
					$this->ProjectBudgetSale->create();
					$this->ProjectBudgetSale->save($sale);
					$newSaleId = $this->ProjectBudgetSale->getLastInsertID();

					$this->ProjectBudgetInvoice->recursive = -1;
					$PFERs = $this->ProjectBudgetInvoice->find('all', array(
						'conditions' => array('project_budget_sale_id' => $oldSaleId)));
					if (!empty($PFERs)) {
						foreach ($PFERs as $PFER) {
							unset($PFER['ProjectBudgetInvoice']['id']);
							$PFER['ProjectBudgetInvoice']['activity_id'] = 0;
							$PFER['ProjectBudgetInvoice']['project_budget_sale_id'] = $newSaleId;
							$PFER['ProjectBudgetInvoice']['project_id'] = $id_duplicate;
							$this->ProjectBudgetInvoice->create();
							$this->ProjectBudgetInvoice->save($PFER);
						}
					}
				}
			}
			$this->ProjectTask->staffingSystem($id_duplicate);
		}
		return $project_id;
	}
	private function copy_task_attachment($filename){
		$result = false;
		if( empty($filename) ) return false;
		$company = $this->employee_info['Company']['id'];
        $path = FILES . 'projects' . DS . 'project_tasks' . DS . $company . DS;
		$old_file = new File($path.$filename);
		if( !$old_file->exists() ) return false;
		$file_info = $old_file->info();
		$continue = 1;
		$new_name = '';
		while($continue){
			$new_name = $file_info['filename'].'('.$continue.').'.$file_info['extension'];
			if( file_exists($path.$new_name) ){
				$continue++;
			}else{
				$continue = 0;
			}
		}
		if( $old_file->copy($path.$new_name)) $result = $new_name;
		return $result;
	}
	private function deleteProject($id){
		$this->loadModel('ProjectTask');
        $projectTasks = $this->ProjectTask->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id),
            'fields' => array('id', 'id')
        ));
		$this->Project->recursive = -1;
		$p = $this->Project->read(null, $id);
		if ($this->Project->delete($id)) {
			$this->writeLog($p, $this->employee_info, sprintf('Delete project `%s`', $p['Project']['project_name']), $p['Project']['company_id']);
			/**
			 * Delete staffing
			 */
			$this->loadModel('TmpStaffingSystem');
			$this->TmpStaffingSystem->deleteAll(array('TmpStaffingSystem.project_id' => $id), false);
			/**
			 * Delete task and assign task
			 */
			if (!empty($projectTasks)) {
				$this->ProjectTask->deleteAll(array('ProjectTask.id' => $projectTasks), false);
				$this->loadModel('ProjectTaskEmployeeRefer');
				$this->ProjectTaskEmployeeRefer->deleteAll(array('project_task_id' => $projectTasks), false);
			}
			/**
			 * Delete linked activity
			 */
			if (!empty($activityId) && $activityId['Activity']['id']) {
				// When you delete a project linked to an activity delete the project and the activity linked ( excepted if there are consumed )
				$this->Activity->delete($activityId['Activity']['id'], false);
				/**
				 * Xoa cac task linked
				 */
				$act_task_id = $this->ActivityTask->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'project_task_id' => $projectTasks,
					),
					'fields' => array('id', 'id'),
				));
				if (!empty($act_task_id)) {
					$this->ActivityRequest->deleteAll(array('task_id' => $act_task_id), false);
				}
				$this->ActivityRequest->deleteAll(array('ActivityRequest.activity_id' => $activityId['Activity']['id']), false);
				$this->ActivityTask->deleteAll(array('project_task_id' => $projectTasks), false);

				// clean cache menu acitivity / task 
				$company = $this->employee_info['Company']['id'];
				$cacheName = $company . '_' . $this->employee_info['Employee']['id'] . '_context_menu';
				Cache::delete($cacheName);
				$cacheNameMenu = $company . '_' . $this->employee_info['Employee']['id'] . '_context_menu_cache';
				Cache::delete($cacheNameMenu);
			}
			return true;
		}
	}
	private function check_project_used($id){
		
        $this->loadModel('Activity');
        $activityId = $this->Activity->find('first', array(
            'recursive' => -1,
            'conditions' => array('project' => $id),
            'fields' => array('id')
        ));
        $this->loadModel('ProjectTask');
        $this->loadModel('ActivityTask');
        $this->loadModel('ActivityRequest');
        $projectTasks = $this->ProjectTask->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id),
            'fields' => array('id', 'id')
        ));
        $request = 0;
        if (!empty($projectTasks)) {
            $activityTasks = $this->ActivityTask->find('list', array(
                'recursive' => -1,
                'conditions' => array('project_task_id' => $projectTasks),
                'fields' => array('id', 'id')
            ));
            if (!empty($activityTasks)) {
                $request = $this->ActivityRequest->find('count', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'task_id' => $activityTasks,
                    )
                ));
            }
        }
        if (!empty($activityId) && $activityId['Activity']['id']) {
            $request_activity = $this->ActivityRequest->find('count', array(
                'recursive' => -1,
                'conditions' => array(
                    'activity_id' => $activityId['Activity']['id'],
                )
            ));
            $request += $request_activity;
        }
        return $request;
	}
	public function save_projects(){
		$success = array();
		$datas = array();
		if(!empty($this->data)){
			$this->loadModels('Project', 'ProjectEmployeeManager', 'ProjectListMultiple', 'ProjectType', 'ProjectPhaseCurrent', 'ProjectTeam', 'ProjectFunctionEmployeeRefer');
			$datas = json_decode($this->data);
			$yn_match_string = array('yes', 'no', 'non', 'oui');
			$category_match = array(
				'inprogress' => 1,
				'opportunity' => 2,
				'archived' => 3,
				'model' => 4,
			);
			foreach($datas as $key => $value){
				$value = (array) $value;
				$success[$value['id']] = false;
				if(!(empty($value['project_name']) && empty($value['project_code_1']))){
					$saved = array();
					$saved['update_by_employee'] = $this->employee_info['Employee']['fullname'];
					if(!empty($value['project_code_1'])){
						$exist_project_id = $this->validatedProjectCode1($value['project_code_1']);
						$saved['project_code_1'] = $value['project_code_1'];
					}
					if(!empty($value['project_name'])){
						$p_code_1 = !empty($value['project_code_1']) ? $value['project_code_1'] : 0;
						$exist_project_id = $this->validatedProjectNameWithCode1(trim($value['project_name']), $p_code_1);
						$saved['project_name'] = substr(trim($value['project_name']), 0, 255);
					}
					if(!($exist_project_id === 'error' || (empty($exist_project_id) && empty($saved['project_name'])))){
						// project_manager_id
						$list_employee_managers = array();
						if(!empty($value['project_manager_id'])){
							$project_manager = explode(",", trim($value['project_manager_id']));
							if(count($project_manager) > 0){
								$list_employee_managers['PM'] = $this->validatedProjectManager($project_manager, $exist_project_id, 'PM');
								$list_employee_managers['PM'] = array_unique($list_employee_managers['PM']);
							}
						}
						if(!empty($list_employee_managers) || !empty($exist_project_id)){
							$name = $this->employee_info['Employee']['fullname'] . ' ' . date('H:i d/m/Y', time());
							// project_code_1
							if(!empty($value['project_name'])){
								$saved['project_name'] = $value['project_name'];
							}
							if(!empty($value['project_name'])){
								$saved['project_name'] = $value['project_name'];
							}
							//category
							if(empty($exist_project_id)) $saved['category'] = 2; // truong hop tao moi.
							if(!empty($value['category'])){
								$key_cate = strtolower(str_replace(' ','',trim($value['category'])));
								$saved['category'] = !empty($category_match[$key_cate]) ? $category_match[$key_cate] : 2;
							}
							
							if(!empty($value['project_code_2'])){
								$saved['project_code_2'] = $value['project_code_2'];
							}
							//address
							if(!empty($value['address'])){
								$saved['address'] = $value['address'];
							}
							// technical_manager_id
							if(!empty($value['technical_manager_id'])){
								$technical_manager = explode(",", trim($value['technical_manager_id']));
								if(count($technical_manager) > 0){
									$list_employee_managers['TM'] = $this->validatedProjectManager($technical_manager, $exist_project_id, 'TM');
									$list_employee_managers['TM'] = array_unique($list_employee_managers['TM']);
								}
							}
							// read_access
							if(!empty($value['read_access'])){
								$read_access = explode(",", trim($value['read_access']));
								if(count($read_access) > 0){
									$list_employee_managers['RA'] = $this->validatedProjectManager($read_access, $exist_project_id, 'RA');
									$list_employee_managers['RA'] = array_unique($list_employee_managers['RA']);
								}
							}
							// functional_leader_id
							if(!empty($value['functional_leader_id'])){
								$functional_leader = explode(",", trim($value['functional_leader_id']));
								if(count($functional_leader) > 0){
									$list_employee_managers['FL'] = $this->validatedProjectManager($functional_leader, $exist_project_id, 'FL');
									$list_employee_managers['FL'] = array_unique($list_employee_managers['FL']);
								}
							}
							// uat_manager_id
							if(!empty($value['uat_manager_id'])){							
								$uat_manager = explode(",", trim($value['uat_manager_id']));
								if(count($uat_manager) > 0){
									$list_employee_managers['UM'] = $this->validatedProjectManager($uat_manager, $exist_project_id, 'UM');
									$list_employee_managers['UM'] = array_unique($list_employee_managers['UM']);
								}
							}
							// chief_business_id
							if(!empty($value['chief_business_id'])){
								$chief_business = explode(",", trim($value['chief_business_id']));
								if(count($chief_business) > 0){
									$list_employee_managers['CB'] = $this->validatedProjectManager($chief_business, $exist_project_id, 'CB');
									$list_employee_managers['CB'] = array_unique($list_employee_managers['CB']);
								}
							}
							// project_type_id
							if(!empty($value['project_type_id'])){
								$saved['project_type_id'] = $this->validatedProjectType(trim($value['project_type_id']));
							}
							// project_sub_type and project sub sub type
							if(!empty($value['project_type_id']) && !empty($value['project_sub_type_id'])){
								$this->loadModel('ProjectType','ProjectSubType');
								$subType = trim($value['project_sub_type_id']);
								$checkExistSubType = $this->ProjectSubType->find('first', array(
									'recursive' => -1,
									'conditions' => array(
										'project_type_id' => $saved['project_type_id'],
										'project_sub_type' => $subType,
									),
									'fields' => array('id'),
								));
								if(!empty($checkExistSubType)){
									$idSubTypeNew = $checkExistSubType['ProjectSubType']['id'];
								}else{
									$this->ProjectSubType->create();
									$this->ProjectSubType->save(array(
										'project_type_id' => $saved['project_type_id'],
										'project_sub_type' => $subType,
										'created' => time(),
										'updated' => time(),
										'display' => 1,
									));
									$idSubTypeNew = $this->ProjectSubType->id;
								}
								if(!empty($idSubTypeNew)){
									$saved['project_sub_type_id'] = $idSubTypeNew;
								}
								if(!empty($value['project_sub_sub_type_id']) && !empty($idSubTypeNew)){
									$subSubType = trim($value['project_sub_sub_type_id']);
									$checkExistSubSubType = $this->ProjectSubType->find('first', array(
										'recursive' => -1,
										'conditions' => array(
											'parent_id' => $idSubTypeNew,
											'project_sub_type' => $subSubType,
										),
										'fields' => array('id'),
									));
									if(!empty($checkExistSubSubType)){
										$idSubSubTypeNew = $checkExistSubSubType['ProjectSubType']['id'];
									}else{
										$this->ProjectSubType->create();
										$this->ProjectSubType->save(array(
											'parent_id' => $idSubTypeNew,
											'project_sub_type' => $subSubType,
											'created' => time(),
											'updated' => time(),
											'display' => 1,
										));
										$idSubSubTypeNew = $this->ProjectSubType->id;
									}
									if(!empty($idSubSubTypeNew)){
										$saved['project_sub_sub_type_id'] = $idSubSubTypeNew;
									}
								}
							}
							// budget_customer_id
							if(!empty($value['budget_customer_id'])){
								$saved['budget_customer_id'] = $this->validatedBudgetCustomer(trim($value['budget_customer_id']));
							}
							// complexity_id
							if(!empty($value['complexity_id'])){
								$saved['complexity_id'] = $this->validatedProjectComplexity(trim($value['complexity_id']));
							}
							// project_status_id
							if(!empty($value['project_status_id'])){
								$saved['project_status_id'] = $this->validatedProjectStatus(trim($value['project_status_id']));
							}
							
							//project_phase_id
							$project_phase = array();
							if(!empty($value['project_phase_id'])){
								$project_phase = $this->validatedProjectCurrentPhase(trim($value['project_phase_id']), $exist_project_id);
								$project_phase = array_unique($project_phase);
							}
							
							// Team
							if(!empty($value['team'])){
								$saved['team'] = $this->validatedProjectTeams(trim($value['team']));
							}
							// start_date , end_date
							if(!empty($value['start_date'])){
								$saved['start_date'] = date('Y-m-d', strtotime($value['start_date']));
							}
							if(!empty($value['end_date'])){
								$saved['end_date'] = date('Y-m-d', strtotime($value['end_date']));
							}
							if(!empty($value['start_date']) && !empty($value['end_date'])){
								if(strtotime($value['start_date']) > strtotime($value['end_date'])){
									$saved['start_date'] = date('Y-m-d', strtotime($value['end_date']));
									$saved['end_date'] = date('Y-m-d', strtotime($value['start_date']));
								}
							}
							$list_multiselect = array();
							for($i = 1; $i <= 20; $i++){
								// date
								if($i <= 14){
									if(!empty($value['date_'. $i])) $saved['date_'.$i] = date('Y-m-d', strtotime($value['date_'.$i]));
								}
								
								if($i <= 5){
									if(!empty($value['date_mm_yy_'. $i])) $saved['date_mm_yy_'.$i] = date('Y-m', strtotime($value['date_mm_yy_'.$i]));
								}
								
								if($i <= 5){
									if(!empty($value['date_yy_'. $i])) $saved['date_yy_'.$i] = date('Y', strtotime($value['date_yy_'.$i]));
								}
								
								// price
								if($i <= 16){
									if(!empty($value['price_'. $i])) $saved['price_'.$i] = $value['price_'.$i];
								}
								
								// text_one_line
								if(!empty($value['text_one_line_'. $i])) $saved['text_one_line_'.$i] = $value['text_one_line_'.$i];
								
								// text_two_line
								if(!empty($value['text_two_line_'. $i])) $saved['text_two_line_'.$i] = $value['text_two_line_'.$i];
								
								// Free
								if(!empty($value['free_'. $i])) $saved['free_'.$i] = $value['free_'.$i];
								
								// Number
								if($i <= 18){
									if(!empty($value['number_'. $i])) $saved['number_'.$i] = $value['number_'.$i];
								}
								
								// Yes/No
								if($i <= 9){
									if(!empty($value['yn_'. $i]) && in_array(trim(strtolower($value['yn_'. $i])), $yn_match_string)){
										$yn_toLower = trim(strtolower($value['yn_'.$i]));
										if($yn_toLower == 'yes' || $yn_toLower == 'oui') $saved['yn_'. $i] = 1;
										if($yn_toLower == 'no' || $yn_toLower == 'non') $saved['yn_'. $i] = 0;
									}
								}
								
								// List
								if($i <= 14){
									if(!empty($value['list_'.$i])) $saved['list_'.$i] = $this->validatedList($value['list_'.$i], 'list_'.$i, 0);
								}
								
								// List multiselect.
								if($i <= 10){
									if(!empty($value['list_muti_'.$i])){
										$list_muti_name = explode(",", $value['list_muti_'.$i]);
										if(count($list_muti_name) > 0){
											$list_multiselect['project_list_multi_'.$i] = $this->validatedListMuti($list_muti_name, 'list_muti_'.$i, $exist_project_id);
											$list_multiselect['project_list_multi_'.$i] = array_unique($list_multiselect['project_list_multi_'.$i]);
											
										}
									}
								}
								// Editor
								if($i <= 5){
									if(!empty($value['editor_'.$i])) $saved['editor_'.$i] = $value['editor_'.$i];
								}
								
								// 0/1
								if($i <= 4){
									if(!empty($value['bool_'.$i])) $saved['bool_'.$i] = $value['bool_'.$i];
								}
							}
							$saved['activated'] = !empty($value['activated']) ? $value['activated'] : null;
							$saved['primary_objectives'] = !empty($value['primary_objectives']) ? $value['primary_objectives'] : null;
							$saved['project_objectives'] = !empty($value['project_objectives']) ? $value['project_objectives'] : null;
							$saved['issues'] = !empty($value['issues']) ? $value['issues'] : null;
							$saved['constraint'] = !empty($value['constraint']) ? $value['constraint'] : null;
							$saved['remark'] = !empty($value['remark']) ? $value['remark'] : null;
							$saved['long_project_name'] = !empty($value['long_project_name']) ? $value['long_project_name'] : null;
							// project_priority_id
							if(!empty($value['project_priority_id'])){
								$saved['project_priority_id'] = $this->validatedPriority($value['project_priority_id']);
							}
							
							// project_amr_program_id
							if(!empty($value['project_amr_program_id'])){
								$saved['project_amr_program_id'] = $this->validatedProgram($value['project_amr_program_id']);
							}
							
							// project_amr_sub_program_id
							if(!empty($value['project_amr_sub_program_id']) && !empty($saved['project_amr_program_id'])){
								$saved['project_amr_sub_program_id'] = $this->validatedSubProgram($value['project_amr_sub_program_id'], $saved['project_amr_program_id']);
							}
							
							$saved['company_id'] = $this->employee_info['Company']['id'];
							
							if(!empty($saved)){
								if(!empty($exist_project_id)){
									$this->Project->id = $exist_project_id;
								}else{
									$this->Project->create();
								}
								$result = $this->Project->save($saved);
								if($result){
									$success[$value['id']] = true;
									$project_id = $this->Project->id;
									$project_added = $this->Project->find('first', array(
										'recursive' => -1,
										'conditions' => array('id' => $project_id),
										'fields' => array('activity_id'),
									));
									$activity_id = !empty($project_added) ? $project_added['Project']['activity_id'] : '';
									foreach($list_employee_managers as $type => $values){
										if(!empty($values)){
											foreach($values as $index => $employee_manager_id){
												$this->ProjectEmployeeManager->create();
												$this->ProjectEmployeeManager->save(array(
													'project_id' => $project_id,
													'company_id' => $this->employee_info['Company']['id'],
													'project_manager_id' => $employee_manager_id,
													'is_backup' => 0,
													'activity_id' => 0,
													'type' => $type,
													'is_profit_center' => 0,
												));
											}
										}
									}
									
									//Ticket #787. Tạo Phase khi import. Updated  by QuanNV 15/12/2020
									$this->loadModels('ProjectPhase', 'ProjectPhasePlan');
									$listPhaseDefault = $this->ProjectPhase->find('list',array(
										'recursive' => -1,
										'conditions' => array(
											'company_id' =>$this->employee_info['Company']['id'],
											'add_when_create_project' => 1,
											'activated' => 1
										),
										'order' => 'ProjectPhase.phase_order ASC',
										'fields' => array('id')
									));
									if(!empty($listPhaseDefault)){
										$i = 1;
										foreach ($listPhaseDefault as $keyPhase => $idPhaseDefault){
											$newRecord['project_id'] = $project_id;
											$newRecord['project_planed_phase_id'] = $idPhaseDefault;
											$newRecord['weight'] = $i;
											if($i == 1){
												if(!empty($saved['start_date'])){
													$newRecord['phase_planed_start_date'] = $saved['start_date'];
													$newRecord['phase_real_start_date'] = $saved['start_date'];
												}else{
													$newRecord['phase_planed_start_date'] = '0000-00-00';
													$newRecord['phase_real_start_date'] = '0000-00-00';
												}
												if(!empty($saved['end_date'])){
													$newRecord['phase_planed_end_date'] = $saved['end_date'];
													$newRecord['phase_real_end_date'] = $saved['end_date'];
												}else{
													$newRecord['phase_planed_end_date'] = '0000-00-00';
													$newRecord['phase_real_end_date'] = '0000-00-00';
												}
											}else{
												$newRecord['phase_planed_start_date'] = '0000-00-00';
												$newRecord['phase_real_start_date'] = '0000-00-00';
												$newRecord['phase_planed_end_date'] = '0000-00-00';
												$newRecord['phase_real_end_date'] = '0000-00-00';
											}
											$this->ProjectPhasePlan->create();
											$this->ProjectPhasePlan->save($newRecord);
											$i++;
										}
									}
									// End ticket #787
									
									// Save list_multiselect
									if(!empty($list_multiselect)){
										foreach($list_multiselect as $key => $multiLists){
											if(!empty($multiLists)){
												foreach($multiLists as $index => $project_dataset_id){
													$this->ProjectListMultiple->create();
													$this->ProjectListMultiple->save(array(
														'project_id' => $project_id,
														'project_dataset_id' => $project_dataset_id,
														'key' => $key,
													));
												}
											}
										}
									
									}
									
									// save current phase.
									if(!empty($project_phase)){
										foreach($project_phase as $index => $phase_id){
											$this->ProjectPhaseCurrent->create();
											$this->ProjectPhaseCurrent->save(array(
												'project_id' => $project_id,
												'project_phase_id' => $phase_id,
											));
										}
									}

									//save synthesis comment.
									$this->loadModel('LogSystem');
									if(!empty($value['syn_comment'])){
										$this->LogSystem->create();
										$this->LogSystem->save(array(
											'company_id' => $this->employee_info['Company']['id'],
											'model' => 'ProjectAmr',
											'model_id' => $project_id,
											'name' => $name,
											'description' => $value['syn_comment'],
											'employee_id' => $this->employee_info['Employee']['id'],
											'update_by_employee' => $this->employee_info['Employee']['fullname'],
										));
									}
									if(!empty($value['syn_done'])){
										$this->LogSystem->create();
										$this->LogSystem->save(array(
											'company_id' => $this->employee_info['Company']['id'],
											'model' => 'Done',
											'model_id' => $project_id,
											'name' => $name,
											'description' => $value['syn_done'],
											'employee_id' => $this->employee_info['Employee']['id'],
											'update_by_employee' => $this->employee_info['Employee']['fullname'],
										));
									}
									if(!empty($value['syn_issues'])){
										$this->LogSystem->create();
										$this->LogSystem->save(array(
											'company_id' => $this->employee_info['Company']['id'],
											'model' => 'ProjectIssue',
											'model_id' => $project_id,
											'name' => $name,
											'description' => $value['syn_issues'],
											'employee_id' => $this->employee_info['Employee']['id'],
											'update_by_employee' => $this->employee_info['Employee']['fullname'],
										));
									}
									if(!empty($value['syn_risks'])){
										$this->LogSystem->create();
										$this->LogSystem->save(array(
											'company_id' => $this->employee_info['Company']['id'],
											'model' => 'ProjectRisk',
											'model_id' => $project_id,
											'name' => $name,
											'description' => $value['syn_risks'],
											'employee_id' => $this->employee_info['Employee']['id'],
											'update_by_employee' => $this->employee_info['Employee']['fullname'],
										));
									}
									if($saved['category'] == 1){
										$this->requestAction('/projects_preview/saveActivityLinked/'. $project_id .'/'. $this->employee_info['Company']['id'] . '/'. true);
									}
									if($saved['category'] == 2 && !empty($activity_id) && !empty($exist_project_id)){
										$this->requestAction('/projects_preview/deleteActivityLinked/'. $project_id .'/'. true);
									}
								}
							}
							
						}
					}
				}
				
			}
			
		}
		die(json_encode(array(
			'success' => $success,
			'data' => $datas,
			'message' => ''
		)));
		exit;
	}
	function validatedModelProjectManager($project_managers){
		$this->loadModels('Employee');
		$result = array();
		$default_user_profile = $this->Employee->default_user_profile($this->employee_info['Company']['id']);
		foreach($project_managers as $key => $project_manager_name){
			$project_manager_name = trim($project_manager_name);
			if(!empty($project_manager_name)){
				$full_name = (explode(' ', $project_manager_name));
				if(count($full_name)){
					$first_name = trim($full_name[0]);
					$last_name = trim(preg_replace('/^'.$first_name.'/', '', $project_manager_name ));
					if(!empty($first_name) && !empty($last_name)){
						$check_employee = $this->Employee->find('first', array(
							'recursive' => -1,
							'conditions' => array(
								'LOWER(first_name)' => strtolower($first_name),
								'LOWER(last_name)' => strtolower($last_name),
								'company_id' => $this->employee_info['Company']['id'],
							),
							'fields' => array('id')
						));
						
						if(!empty($check_employee)){
							$result[$key] = $check_employee['Employee']['id'];
						}else{
							$result[$key] = $this->addEmployee($first_name, $last_name);
						}
					}
				}
			}
		}
		return $result;
	}
	function validatedProjectManager($project_managers, $project_id, $type){
		$this->loadModels('Employee', 'ProjectEmployeeManager');
		$result = array();
		$default_user_profile = $this->Employee->default_user_profile($this->employee_info['Company']['id']);
		foreach($project_managers as $key => $project_manager_name){
			$project_manager_name = trim($project_manager_name);
			if(!empty($project_manager_name)){
				$full_name = (explode(' ', $project_manager_name));
				if(count($full_name)){
					$first_name = trim($full_name[0]);
					$last_name = trim(preg_replace('/^'.$first_name.'/', '', $project_manager_name ));
					if(!empty($first_name) && !empty($last_name)){
						$check_employee = $this->Employee->find('first', array(
							'recursive' => -1,
							'conditions' => array(
								'LOWER(first_name)' => strtolower($first_name),
								'LOWER(last_name)' => strtolower($last_name),
								'company_id' => $this->employee_info['Company']['id'],
							),
							'fields' => array('id')
						));
						
						if(!empty($check_employee)){
							if(!empty($project_id)){
								$count = $this->ProjectEmployeeManager->find('count', array(
									'recursive' => -1,
									'conditions' => array(
										'project_id' => $project_id,
										'type' => $type,
										'project_manager_id' => $check_employee['Employee']['id'],
									)
								));
								if($count == 0){
									$result[$key] = $check_employee['Employee']['id'];
								}
							}else{
								$result[$key] = $check_employee['Employee']['id'];
							}
						}else{
							$result[$key] = $this->addEmployee($first_name, $last_name);
						}
					}
				}
			}
		}
		return $result;
	}
	function validatedProjectTeams($teams){
		$this->loadModels('ProjectTeam', 'ProfitCenter', 'ProjectFunctionEmployeeRefer');
		$result = array();
		if(!empty($teams)){
			$teams = explode(",", $teams);
			foreach($teams as $key => $team){
				$team = trim($team);
				if(!empty($team)){
					$check_team = $this->ProfitCenter->find('first', array(
						'recursive' => -1,
						'conditions' => array(
							'company_id' => $this->employee_info['Company']['id'],
							'name' => trim(substr($team, 0, 255)),
						),
						'fields' => array('id')
					));
					if(!empty($check_team)){
						$result = $check_team['ProfitCenter']['id'];
					}else{
						$this->ProfitCenter->create();
						$this->ProfitCenter->save(array(
							'name' => trim(substr($team, 0, 255)),
							'company_id' => $this->employee_info['Company']['id'],
						));
						$result = $this->ProfitCenter->id;
					}
				}
			}
		}
		return $result;
	}
	function validatedProjectCurrentPhase($project_phases, $project_id){
		$this->loadModels('ProjectPhase', 'ProjectPhaseCurrent');
		$result = array();
		if(!empty($project_phases)){
			$project_phases = explode(",", trim($project_phases));
			foreach($project_phases as $key => $phase_name){
				$phase_name = trim($phase_name);
				if(!empty($phase_name)){
					$check_phase = $this->ProjectPhase->find('first', array(
						'recursive' => -1,
						'conditions' => array(
							'company_id' => $this->employee_info['Company']['id'],
							'name' => $phase_name,
						),
						'fields' => array('id')
					));
					if(!empty($check_phase)){
						if(!empty($project_id)){
							$count = $this->ProjectPhaseCurrent->find('count', array(
								'recursive' => -1,
								'conditions' => array(
									'project_id' => $project_id,
									'project_phase_id' => $check_phase['ProjectPhase']['id'],
								),
							));
							if($count == 0){
								$result[$key] = $check_phase['ProjectPhase']['id'];
							}
						}else{
							$result[$key] = $check_phase['ProjectPhase']['id'];
						}
					}else{
						$this->ProjectPhase->recursive = -1;
						$last_order = $this->ProjectPhase->find("first", array(
							"conditions" => array(
								"ProjectPhase.company_id" => $this->employee_info['Company']['id']
							),
							"fields" => array("(Max(ProjectPhase.phase_order)+1) phase_last_order")));
							
						$phase_order = $last_order[0]["phase_last_order"];
						$saved = array();
						$saved['name'] = $phase_name;
						$saved['color'] = '#004380';
						$saved['company_id'] = $this->employee_info['Company']['id'];
						$saved['activated'] = 1;
						$saved['phase_order'] = $phase_order;
						
						$this->ProjectPhase->create();
						$this->ProjectPhase->save($saved);
						$result[$key] = $this->ProjectPhase->id;
					}
				}
			}
		}
		return $result;
	}
	function validatedProjectStatus($status_name){
		$this->loadModels('ProjectStatus');
		$projectStatus = $this->ProjectStatus->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'name' => trim($status_name),
				'company_id' => $this->employee_info['Company']['id'],
			),
			'fields' => array('id'),
		));
		if(!empty($projectStatus)) return $projectStatus['ProjectStatus']['id'];
		else return '';
	}
	function validatedProjectNameWithCode1($project_name, $p_code_1){
		$this->loadModels('Project');
		$cut_project_name = substr(trim($project_name), 0, 255);
		$project_id = 0;
		$conditions = array(
			'OR' => array(
				'project_name' => trim($cut_project_name),
			),
			'company_id' => $this->employee_info['Company']['id'],
		);
		if(!empty($p_code_1)) $conditions['OR']['project_code_1'] = $p_code_1;
		$_project = $this->Project->find('all', array(
			'recursive' => -1,
			'conditions' => $conditions,
			'fields' => array('id'),
			
		));
		$count_project = count($_project);
		if($count_project == 1 ) $project_id = $_project[0]['Project']['id'];
		if($count_project > 1 ) $project_id = 'error';
		
		return $project_id;
	}
	function validatedProjectName($project_name){
		$this->loadModels('Project');
		$cut_project_name = substr(trim($project_name), 0, 255);
		$project_id = 0;
		$_project = $this->Project->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'project_name' => trim($cut_project_name),
				'company_id' => $this->employee_info['Company']['id'],
			),
			'fields' => array('id'),
			
		));
		if(!empty($_project)) $project_id = $_project['Project']['id'];
		return $project_id;
	}
	function getProjectModel($project_name){
		$this->loadModels('Project');
		// $cut_project_name = substr(trim($project_name), 0, 255);
		$_project = $this->Project->find('all', array(
			'conditions' => array(
				'Project.project_name' => $project_name,
				'Project.company_id' => $this->employee_info['Company']['id'],
				'Project.category' => 4 // model project 
			),
		));
		
		return $_project;
	}
	function addEmployee($first_name, $last_name){
		$this->loadModels('Employee','CompanyEmployeeReference');
		$project_manager_id = '';
		$saved = array();
		$default_user_profile = $this->Employee->default_user_profile($this->employee_info['Company']['id']);
		$saved = $default_user_profile;
		$saved['first_name'] = $first_name;
		$saved['last_name'] = $last_name;
		$saved['email'] = str_replace(' ', '', $first_name). $default_user_profile['sperator'] . str_replace(' ', '', $last_name) . $default_user_profile['domain_name'];
		$saved['role_id'] = 3;
		$saved['start_date'] = date('d-m-Y', time());
		$saved['language'] = 'fr';
		$saved['actif'] = 1;
		$saved['password'] = md5($default_user_profile["password"]);
		$saved['company_id'] = $this->employee_info['Company']['id'];
		$this->Employee->create();
		$data = $this->Employee->save($saved);
		if ($data ) {
			$see_all_projects = !empty($this->companyConfigs['see_all_projects']) ? 1 : 0; 
			$see_budget = 0;
			$employee_id = $this->Employee->getLastInsertID();
			$project_manager_id = $employee_id;
			$this->CompanyEmployeeReference->create();
			$this->CompanyEmployeeReference->save(array(
				'company_id' => $this->employee_info['Company']['id'],
				'employee_id' => $employee_id,
				'role_id' => 3,
				'control_resource' => $default_user_profile['control_resource'],
				'see_all_projects' => $see_all_projects,
				'see_budget' => $see_budget,
			));
		}
		return $project_manager_id;
	}
	function validatedProjectCode1($project_code_1){
		$result = null;
		$project_code = $this->Project->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $this->employee_info['Company']['id'],
				'project_code_1' => $project_code_1
			),
			'fields'=> array('id')
		));
		if(!empty($project_code)){
			$result = $project_code['Project']['id'];
		}
		return $result;
	}
	function validatedPriority($priority_name){
		$this->loadModel('ProjectPriority');
		$result = null;
		if(!empty($priority_name)){
			$priority_id = $this->ProjectPriority->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $this->employee_info['Company']['id'],
					'priority' => trim($priority_name),
				),
				'fields' => array('id'),
			));
			if(!empty($priority_id)){
				$result = $priority_id['ProjectPriority']['id'];
			}else{
				$this->ProjectPriority->create();
				$this->ProjectPriority->save(array(
					'company_id' => $this->employee_info['Company']['id'],
					'priority' => trim($priority_name),
					'created' => time(),
					'updated' => time(),
				));
				$result = $this->ProjectPriority->id;
			}
		}
		return $result;
	}
	function validatedProgram($program_name){
		$this->loadModel('ProjectAmrProgram');
		$result = null;
		if(!empty($program_name)){
			$program_id = $this->ProjectAmrProgram->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $this->employee_info['Company']['id'],
					'amr_program' => trim($program_name),
				),
				'fields' => array('id'),
			));
			if(!empty($program_id)){
				$result = $program_id['ProjectAmrProgram']['id'];
			}else{
				$this->ProjectAmrProgram->create();
				$family_id = $this->_createNewFamily($this->employee_info['Company']['id'], trim($program_name));
				$this->ProjectAmrProgram->save(array(
					'amr_program' => trim($program_name),
					'company_id' => $this->employee_info['Company']['id'],
					'family_id' => $family_id,
					'sub_family_id' => '',
					'created' => time(),
					'updated' => time(),
					'color' => '#004380'
				));
				// Kiem tra logic program voi family
				$result = $this->ProjectAmrProgram->id;
			}
		}
		return $result;
	}
	function validatedSubProgram($sub_program_name, $program_id){
		$this->loadModel('ProjectAmrSubProgram');
		$result = null;
		if(!empty($sub_program_name)){
			$sub_program_id = $this->ProjectAmrSubProgram->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'amr_sub_program' => trim($sub_program_name),
				),
				'fields' => array('id', 'project_amr_program_id'),
			));
			if(!empty($sub_program_id)){
				$result = $sub_program_id['ProjectAmrSubProgram']['id'];
				$project_amr_program_id = $sub_program_id['ProjectAmrSubProgram']['project_amr_program_id'];
				// Update program linked
				if(!empty($project_amr_program_id) && $project_amr_program_id != $program_id){
					// Do not save if the linked is exists
					$result = null;
				}
				
			}else{
				$this->ProjectAmrSubProgram->create();
				$this->ProjectAmrSubProgram->save(array(
					'amr_sub_program' => trim($sub_program_name),
					'project_amr_program_id' => $program_id,
					'sub_family_id' => null,
					'created' => time(),
					'updated' => time(),
				));
				$result = $this->ProjectAmrSubProgram->id;
			}
		}
		return $result;
	}
	private function _createNewFamily($company_id = null, $programName = null){
        /**
         * Check xem family nay co chua
         */
        $this->loadModels('ActivityFamily');
        $results = 0;
        $fams = $this->ActivityFamily->find('first', array(
            'recursive' => - 1,
            'conditions' => array('company_id' => $company_id, 'name' => $programName, 'parent_id IS NULL'),
            'fields' => array('id')
        ));
        if(!empty($fams) && !empty($fams['ActivityFamily']['id'])){
            $results = $fams['ActivityFamily']['id'];
        } else {
            $saved = array(
                'name' => $programName,
                'company_id' => $company_id
            );
            $this->ActivityFamily->create();
            if($this->ActivityFamily->save($saved)){
                $results = $this->ActivityFamily->id;
            }
        }
        return $results;
    }

	function validatedListMuti($values, $list_name, $project_id){
		$result = array();
		foreach($values as $key => $name){
			$list_val = trim($name);
			if(!empty($list_val)){
				$resValidated = $this->validatedList($list_val, $list_name, $project_id);
				if(!empty($resValidated)){
					$result[] = $this->validatedList($list_val, $list_name, $project_id);
				}
			}
		}
		return $result;
	}
	function validatedProjectType($project_type){
		$this->loadModel('ProjectType');
		$result = null;
		if(!empty($project_type)){
			$projectType = $this->ProjectType->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $this->employee_info['Company']['id'],
					'project_type' => $project_type,
				),
				'fields' => array('id'),
			));
			if(!empty($projectType)){
				$result = $projectType['ProjectType']['id'];
			}else{
				$this->ProjectType->create();
				$this->ProjectType->save(array(
					'company_id' => $this->employee_info['Company']['id'],
					'project_type' => $project_type,
					'created' => time(),
					'updated' => time(),
					'display' => 1,
				));
				$result = $this->ProjectType->id;
			}
		}
		return $result;
	}
	function validatedBudgetCustomer($budget_customer){
		$this->loadModel('BudgetCustomer');
		$result = null;
		if(!empty($budget_customer)){
			$budgetCustomer = $this->BudgetCustomer->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $this->employee_info['Company']['id'],
					'name' => trim(substr($budget_customer, 0, 255)),
				),
				'fields' => array('id'),
			));
			if(!empty($budgetCustomer)){
				$result = $budgetCustomer['BudgetCustomer']['id'];
			}else{
				$this->BudgetCustomer->create();
				$this->BudgetCustomer->save(array(
					'company_id' => $this->employee_info['Company']['id'],
					'name' => trim(substr($budget_customer, 0, 255)),
				));
				$result = $this->BudgetCustomer->id;
			}
		}
		return $result;
	}
	function validatedProjectComplexity($project_complexity){
		$this->loadModel('ProjectComplexity');
		$result = null;
		if(!empty($project_complexity)){
			$projectComplexity = $this->ProjectComplexity->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $this->employee_info['Company']['id'],
					'name' => $project_complexity,
				),
				'fields' => array('id'),
			));
			if(!empty($projectComplexity)){
				$result = $ProjectComplexity['ProjectComplexity']['id'];
			}else{
				$this->ProjectComplexity->create();
				$this->ProjectComplexity->save(array(
					'company_id' => $this->employee_info['Company']['id'],
					'name' => $project_complexity,
					'created' => time(),
					'updated' => time(),
					'display' => 1,
				));
				$result = $this->ProjectComplexity->id;
			}
		}
		return $result;
	}
	function validatedList($list_val, $list_name, $project_id){
		$this->loadModel('ProjectDataset', 'ProjectListMultiple');
		$result = null;
		if(!empty($list_val)){
			$dataSetList = $this->ProjectDataset->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $this->employee_info['Company']['id'],
					'dataset_name' => $list_name,
					'name' => trim(substr(trim($list_val), 0, 255)),
				),
				'fields' => array('id'),
			));
			if(!empty($dataSetList)){
				if(!empty($project_id) && strpos($list_name, 'list_muti_') !== false){
					$count = $this->ProjectListMultiple->find('count', array(
						'recursive' => -1,
						'conditions' => array(
							'project_id' => $project_id,
							'project_dataset_id' => $dataSetList['ProjectDataset']['id'],
						)
					));
					if($count == 0){
						$result = $dataSetList['ProjectDataset']['id'];
					}
				}else{
					$result = $dataSetList['ProjectDataset']['id'];
				}
			}else{
				$this->ProjectDataset->create();
				$this->ProjectDataset->save(array(
					'company_id' => $this->employee_info['Company']['id'],
					'dataset_name' => $list_name,
					'name' => trim($list_val),
					'display' => 1,
				));
				$result = $this->ProjectDataset->id;
			}
		}
		return $result;
	}
	public function save_import(){
		if (!empty($this->data)) {
			extract($this->data['Import']);
			
			if ($task === 'do') {//import
				$import = array();
				foreach (explode(',', $type) as $type) {
					if (empty($this->data[$type][$task])) {
						continue;
					}
					$import = array_merge($import, $this->data[$type][$task]);
				}
				if (empty($import)) {
					$this->Session->setFlash(__('The data to export was not found. Please try again.', true));
					$this->redirect(array('action' => 'index'));
				}

				$complete = 0;
				$company_id = $this->employee_info['Company']['id'];
				$this->loadModel('ProjectEmployeeManager');
				$this->loadModel('ActivityTaskEmployeeRefer');
				$this->loadModel('TmpStaffingSystem');
				//pr($import);die;
				foreach ($import as $data) {
					//do save here
					if( !isset($data['id']) )$this->Project->create();
					if( $this->Project->save($data) ){
						$pid = $this->Project->id;
						$aid = 0;
						//delete activity
						if( isset($data['delete_activity']) ){
							//delete tasks
							$aid = $data['delete_activity'];
							$tasks = $this->ActivityTask->find('list', array(
								'conditions' => array(
									'activity_id' => $aid
								),
								'fields' => array('id')
							));
							$this->ActivityTask->deleteAll(array(
								'ActivityTask.id' => $tasks
							));
							//delete task employee ref
							$this->ActivityTaskEmployeeRefer->deleteAll(array(
								'ActivityTaskEmployeeRefer.activity_task_id' => $tasks
							));
							//delete staffings
							$this->TmpStaffingSystem->deleteAll(array(
								'TmpStaffingSystem.activity_id' => $aid
							));
							$this->Activity->delete($aid);
							//remove
							$this->Project->saveField('activity_id', null);
						}
						//save activity
						else if( isset($data['activity']) ){
							if( !isset($data['activity']['id']) )$this->Activity->create();
							if( $this->Activity->save($data['activity']) ){
								$aid = $this->Activity->id;
								//link together
								$this->Activity->saveField('project', $pid);
								$this->Project->saveField('activity_id', $aid);
								//archived project
								if( $data['category'] == 3 ){
									$this->Activity->saveField('activated', 0);
								}
							}
						}
						//save backup project managers
						if( isset($data['backup_pm']) ){
							$this->ProjectEmployeeManager->deleteAll(array(
								'ProjectEmployeeManager.project_id' => $pid,
								'ProjectEmployeeManager.type' => 'PM'
							), false);
							foreach($data['backup_pm'] as $resourceId){
								$this->ProjectEmployeeManager->create();
								$this->ProjectEmployeeManager->save(array(
									'project_id' => $pid,
									'activity_id' => $aid,
									'project_manager_id' => $resourceId,
									'type' => 'PM',
									'is_backup' => 1
								));
							}
						}
						//save technical managers
						if( isset($data['backup_tm']) ){
							$this->ProjectEmployeeManager->deleteAll(array(
								'ProjectEmployeeManager.project_id' => $pid,
								'ProjectEmployeeManager.type' => 'TM'
							), false);
							foreach($data['backup_tm'] as $resourceId){
								$this->ProjectEmployeeManager->create();
								$this->ProjectEmployeeManager->save(array(
									'project_id' => $pid,
									'activity_id' => $aid,
									'project_manager_id' => $resourceId,
									'type' => 'TM',
									'is_backup' => 1
								));
							}
						}
						//save chief business
						if( isset($data['backup_cb']) ){
							$this->ProjectEmployeeManager->deleteAll(array(
								'ProjectEmployeeManager.project_id' => $pid,
								'ProjectEmployeeManager.type' => 'CB'
							), false);
							foreach($data['backup_cb'] as $resourceId){
								$this->ProjectEmployeeManager->create();
								$this->ProjectEmployeeManager->save(array(
									'project_id' => $pid,
									'activity_id' => $aid,
									'project_manager_id' => $resourceId,
									'type' => 'CB',
									'is_backup' => 1
								));
							}
						}
						$complete++;
					}
				}
				$this->Session->setFlash(sprintf(__('%s/%s project(s) have been imported.', true), $complete, count($import)));
				$this->redirect(array('action' => 'index'));
			} else { // export csv
				App::import('Vendor', 'ParsecsvLib', array('file' => 'parsecsv.lib.php'));
				$csv = new parseCSV();
				header("Content-Type: text/html; charset=ISO-8859");
				// export
				$header = array();
				$type = '';
				if ($this->data['Import']['type'] == 'Error' && !empty($this->data['Error']['export']))
					$type = 'Error';
				if ($this->data['Import']['type'] == 'Update' && !empty($this->data['Update']['export']))
					$type = 'Update';
				if (!empty($type)) {
					$_listEmployee = array();
					foreach ($this->data[$type]['export'][1] as $key => $value) {
						$header[] = __($key , true);
					}
					foreach($this->data[$type]['export'] as $key => $value){
						$_listEmployee[$key] = $this->_utf8_encode_mix($value);
					}
					$csv->output($type . ".csv",  $_listEmployee ,$this->_mix_coloumn($header), ",");
				} else {
					$this->redirect(array('action' => 'index'));
				}
			}
		} else {
			$this->redirect(array('action' => 'index'));
		}
		exit;
	}
	private function hasConsumed($project){
		if(!empty($project['Project']['activity_id']) || $project['Project']['activity_id'] != 0){
            $tasks = $this->ActivityTask->find('list', array(
                'recursive' => -1,
                'conditions' => array('activity_id' => $project['Project']['activity_id']),
                'fields' => array('id', 'id')
            ));
            if(!empty($tasks)){
                $request = $this->ActivityRequest->find('count', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'OR' => array(
                            'activity_id' => $project['Project']['activity_id'],
                            'task_id' => $tasks
                        )
                    )
                ));
            } else {
                $request = $this->ActivityRequest->find('count', array(
                    'recursive' => -1,
                    'conditions' => array('activity_id' => $project['Project']['activity_id'])
                ));
            }
            if($request != 0){
                return true;
            }
        }
        return false;
	}
	private function changeKeys($o, $d){
		$newKeys = array_keys($d);
		$oldKeys = array_keys($o);
		$result = array();
		for($i = 0; $i < count($oldKeys); $i++){
			$result[ $newKeys[$i] ] = $o[ $oldKeys[$i] ];
		}
		return $result;
	}
	public function sstrim(&$v){
		if( is_string($v) )$v = trim($v);
		return;
	}
	private function _utf8_encode_mix($input)
	{
		$result = array();
		foreach($input as $key => $value){
			$result[mb_convert_encoding($key,'UTF-16LE', 'UTF-8')] =  mb_convert_encoding($value,'UTF-16LE', 'UTF-8');
		}
		return $result;
	}
	private function _mix_coloumn($input){
		$result = array();
		foreach($input as $value){
			$result[] = mb_convert_encoding($value,'UTF-16LE', 'UTF-8');
		}
		return $result;
	}
	function save_setting_matching_fields() {
		$this->loadModels('HistoryFilter', 'Employee');
        if (!empty($_POST)) {
            extract($_POST);
            $path = rtrim($_POST['path'], '/');
            $params = $_POST['params'];
            $employId = $this->Session->read('Auth.Employee.id');
            $employId = isset($employId) ? $employId : null;
            $last = $this->Employee->HistoryFilter->find('first', array(
                'recursive' => -1,
                'fields' => array(
                    'id', 'params'
                ),
                'conditions' => array(
                    'path' => $path,
                    'employee_id' => $this->employee_info['Employee']['id']
            )));

            $this->Employee->HistoryFilter->create();
            if (!empty($last)) {
                $this->Employee->HistoryFilter->id = $last['HistoryFilter']['id'];
                unset($last);
            }
            if (empty($params)) {
                Configure::write('debug', 0);
                echo json_encode($_params);
                exit();
            }
            $this->Employee->HistoryFilter->save(array(
                'path' => $path,
                'params' => serialize($params),
                'employee_id' => $this->employee_info['Employee']['id']), array('validate' => false, 'callbacks' => false)
            );
            echo json_encode($params);
        }
        exit();
    }

}