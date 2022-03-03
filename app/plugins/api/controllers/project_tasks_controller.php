<?php
class ProjectTasksController extends ApiAppController {
    public $uses = array('ProjectTask');
	var $components = array('MultiFileUpload');

    public function beforeFilter(){
        parent::beforeFilter();
    }

	// Ref: getCommentTxt() in project_tasks_controller.php
	public function get_task_comments(){

        $results = array();
        if(!empty($_POST)){
            $pTaskId = $_POST['task_id'];
            $this->loadModels('ProjectTaskTxt');
            $result = $this->ProjectTaskTxt->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_task_id' => $pTaskId),
                'fields' => array('ProjectTaskTxt.id', 'ProjectTaskTxt.project_task_id as task_id','ProjectTaskTxt.employee_id', 'ProjectTaskTxt.comment', 'ProjectTaskTxt.created','Employee.first_name', 'Employee.last_name'),
				'joins' => array(
					array(
						'table' => 'employees',
						'alias' => 'Employee',
						'type' => 'inner',
						'conditions' => array(
							'Employee.id = ProjectTaskTxt.employee_id'
						)
					),
				),
				'order' => array('id' => 'DESC'),
            ));
			foreach ($result as $key => $item) {
				$item = $this->z_merge_all_key($item);
				$result[$key] = $item;
			}
			// if( !empty($results['result']) ) $this->update_read_status($pTaskId);
			$this->ZAuth->respond('success', $result);
        }
		$this->ZAuth->respond('fail', null, 'data_incorrect', '0');	
    }
// Ref: update_text() from project_tasks_controller.php
	public function add_task_comment(){
        $result = array();
        if( !empty($this->data['task_id']) ){
           
            $this->loadModel('ProjectTaskTxt');
            $this->ProjectTaskTxt->create();
            $result = $this->ProjectTaskTxt->save(array(
                'project_task_id' => $this->data['task_id'],
                'employee_id' => $this->employee_info['Employee']['id'],
                'comment' => $this->data['comment'],
                'created' => date('Y-m-d H:i:s')
            ));
			$result['ProjectTaskTxt']['id'] = $this->ProjectTaskTxt->id;

			// $this->update_read_status($this->data['id']);
			$this->ZAuth->respond('success', $result);
        }
		$this->ZAuth->respond('fail', null, 'data_incorrect', '0');	
    }
// Reff: getTaskAttachment() from file kanban_controller.php
public function get_task_attachments(){
	$result = array();
	if( !empty($this->data['task_id']) ){
		$task_id = $this->data['task_id'];
		$this->loadModels('projectTasks', 'ProjectTaskAttachment', 'ProjectEmployeeManager');
		// $taskName = $this->projectTasks->find('first', array(
		// 	'recursive' => -1,
		// 	'conditions' => array('id' => $task_id),
		// 	'fields' => array('task_title', 'attachment')
		// ));
		$attachments = $this->ProjectTaskAttachment->find('all', array(
			'recursive' => -1,
			'conditions' => array('task_id' => $task_id),
			'fields' => array(
				'ProjectTaskAttachment.id', 'ProjectTaskAttachment.project_id', 'ProjectTaskAttachment.task_id', 'ProjectTaskAttachment.attachment', 'ProjectTaskAttachment.is_file', 'ProjectTaskAttachment.is_https', 'ProjectTaskAttachment.updated',  
				'ProjectTaskAttachment.employee_id', 'Employee.first_name', 'Employee.last_name'
			),
			'joins' => array(
				array(
					'table' => 'employees',
					'alias' => 'Employee',
					'type' => 'inner',
					'conditions' => array(
						'Employee.id = ProjectTaskAttachment.employee_id'
					)
				),
			),
			'order' => array('id' => 'DESC'),
		));
		foreach ($attachments as $key => $item) {
			$item = $this->z_merge_all_key($item);
			if($item['is_file']==1) {
				$item['link'] = Router::url(array(
					// 'plugin' => false, 
					'controller' => $this->params['controller'], 
					// 'controller' => 'kanban', 
					'action' => 'attachment', 
					$item['id']
				), true);
				
			} else {
				$item['link'] = $item['attachment'];
			}
			$attachments[$key] = $item;
		}
		// if( !empty($taskName['projectTasks']['attachment'])){
		// 	$attachments[] = array(
		// 		'ProjectTaskAttachment' => array(
		// 			'attachment' => $taskName['projectTasks']['attachment'],
		// 			'is_old_attachment' => 1,
		// 			'task_id' => $task_id
		// 		)
		// 	);
		// }
		
		// if( !empty($attachments)){
		// 	$this->update_task_attachment_read_status($task_id);
		// }
		$this->ZAuth->respond('success', $attachments);	
	}
	$this->ZAuth->respond('fail', null, 'data_incorrect', '0');	
}
// Download attachment file by id
public function attachment($id = null) {
	$this->loadModel('ProjectTaskAttachment');
	// $this->layout = false;
	$link = '';
	$attachment_file = $this->ProjectTaskAttachment->find("first", array(
		'recursive' => -1, 'fields' => array('attachment','is_file','is_https', 'task_id'),
		"conditions" => array('id' => $id)));
		// Debug($attachment_file);
	if($attachment_file['ProjectTaskAttachment']['is_file']==0&&$attachment_file['ProjectTaskAttachment']['is_https']==1){
		$this->ZAuth->respond('fail', null, 'You get a link!', '0');
		return;
	}
	if ($attachment_file) {
		$link = trim($this->getPath('project_tasks',$this->employee_info['Company'])
				. $attachment_file['ProjectTaskAttachment']['attachment']);
		// Debug($link);
		
		if (file_exists($link) && is_file($link)){

			// if (file_exists($link) && is_file($link)) {
			// 	$link = '';
			// }
			$info = pathinfo($link);
			// Debug($info);
			$this->view = 'Media';
			$params = array(
				'id' => !empty($info['basename']) ? $info['basename'] : '',
				'path' => !empty($info['dirname']) ? $info['dirname'] . '/' : '',
				'name' => !empty($info['filename']) ? $info['filename'] : '',
				'mimeType' => array(
					'bmp' => 'image/bmp',
					'ppt' => 'application/vnd.ms-powerpoint',
					'pps' => 'application/vnd.ms-powerpoint',
					'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
					'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
					'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation'
				),
				'extension' => !empty($info['extension']) ? strtolower($info['extension']) : '',
				'download' => true,
			);
			if (!empty($info['extension']) && !in_array($info['extension'], array_keys($params['mimeType']))  ) {
			   $params['mimeType'][$info['extension']] = 'application/octet-stream';
			}
			// if (!empty($this->params['url']['download'])) {
			// 	$params['download'] = true;
			// }
			$this->set($params);
			// Debug($params['mimeType'][$info['extension']]);
			// return;
		} else {
			$this->ZAuth->respond('fail', null, 'File not exist!', '0');
		return;
		}
	} else {

		$this->ZAuth->respond('fail', null, 'data_incorrect', '0');
	}

}
// Ref: update_document($project_id) in kanban_controller.php
public function add_attachment(){
	if (empty($this->data['project_id']) || empty($this->data['task_id']) || (empty($this->data['url'])&& empty($_FILES)) ) {
		$this->ZAuth->respond('fail', null, 'data_incorrect', '0');	
		return;
	}
	$this->loadModel('ProjectTaskAttachment');
	$project_id = $this->data['project_id'];
	
	
	$idProject = $this->ProjectTask->find("first", array(
		'recursive' => -1,
		'fields' => array('project_id'),
		'conditions' => array('id' => $this->data['task_id'])
	));
	if ($project_id != $idProject['ProjectTask']['project_id']) {
		$this->ZAuth->respond('fail', null, 'data_incorrect', '0');	
		return;
	}
	// $data = array(
	// 	'status' => false,
	// 	'attachment' => ''
	// );
	// neu co url
	if(!empty($this->data['url']) && empty($_FILES)){
		$data = array(
			'project_id'=> $project_id,
			'task_id' => $this->data['task_id'],
			'attachment' => $this->data['url'],
			'employee_id' => $this->employee_info['Employee']['id'],
			'is_file' => 0,
			'is_https' => 1,

		);
		$this->ProjectTaskAttachment->create();
		$this->ProjectTaskAttachment->save($data);
	}else{
		$_FILES['FileField'] = array();
		if(!empty($_FILES['file']['name'])){
			$_FILES['FileField']['name']['attachment'] = $_FILES['file']['name'];
			$_FILES['FileField']['type']['attachment'] = $_FILES['file']['type'];
			$_FILES['FileField']['tmp_name']['attachment'] = $_FILES['file']['tmp_name'];
			$_FILES['FileField']['error']['attachment'] = $_FILES['file']['error'];
			$_FILES['FileField']['size']['attachment'] = $_FILES['file']['size'];
		}
		if(!empty($_FILES['FileField'])){
			unset($_FILES['file']);
			if (!empty($_FILES['FileField']['name']['attachment'])) {
				App::import('Core', 'Folder');
				$path = $this->getPath('project_tasks', $this->employee_info['Company']);
				// Debug($path);
				// Debug($_FILES['FileField']);
				// exit;
				new Folder($path, true, 0777);
				if (file_exists($path)) {

					$this->MultiFileUpload->encode_filename = false;
					$this->MultiFileUpload->uploadpath = $path;
					$this->MultiFileUpload->_properties['AttachTypeAllowed'] = '*';
					$this->MultiFileUpload->_properties['MaxSize'] = 100000 * 1024 * 1024;
					$file = $this->MultiFileUpload->upload();
					// Debug($file);
				} else {
					$file = "";
					$this->ZAuth->respond('fail', null, 'System save fail', '404');
				}
				// exit;
				if( !empty($file) ){
					// Debug($file['attachment']['attachment']);
					//begin save field
					$data = array(
						'project_id'=> $project_id,
						'task_id' => $this->data['task_id'],
						'attachment' =>  $file['attachment']['attachment'],
						'employee_id' => $this->employee_info['Employee']['id'],
						'size' => $file['attachment']['size'],
						'is_https' => 0,
						'is_file' => 1,
					);
					$this->ProjectTaskAttachment->create();
					$this->ProjectTaskAttachment->save($data);
				   
				}
			}
		}
		// $this->ZAuth->respond('fail', null, 'System save fail', '404');
	}
	$this->ZAuth->respond('success', $this->ProjectTaskAttachment->id, 'Add Document success');	
	// if (!empty( $data['task_id'])){
	// 	// set unread for other employee
	// 	$this->task_attachment_unread($data['task_id']);
	// 	// set read for curent employee
	// 	$this->update_task_attachment_read_status($data['task_id']);
	// }
	
	
}
// Delete a attachment by attach_id
public function delete_attachment(){
	if(empty($_POST['attach_id'])) {
		$this->ZAuth->respond('fail', null, 'data_incorrect', '0');	
		return;
	}
	$attachId = $_POST['attach_id'];

	if( $attachId < 0){
		$this->ZAuth->respond('fail', null, 'data_incorrect', '0');	
		return;
	}
	
	$this->loadModels('ProjectTaskAttachment');
	// $p = $this->ProjectTaskAttachment->read('attachment', $attachId);
	$p = $this->ProjectTaskAttachment->read(array('project_id','attachment'), $attachId);
	if( !$this->UserUtility->user_can('write_project', $p['ProjectTaskAttachment']['project_id'])) $this->data_incorrect('Permission Deny');
	if( !empty($p['ProjectTaskAttachment']['attachment']) ){
		$data = $p['ProjectTaskAttachment']['attachment'];
		$path = $this->getPath('project_tasks', $this->employee_info['Company']);
		if( $data){
			$fileInfo = pathinfo($data);
			if( file_exists( $path . $fileInfo['basename'])){
				unlink($path . $fileInfo['basename']);
			}
			// if($this->MultiFileUpload->otherServer){
			// 	$this->MultiFileUpload->deleteFileToServerOther($path, $fileInfo['basename']);
			// }
		}
	}

	$result = $this->ProjectTaskAttachment->delete($attachId);
	(!!$result) ?
	$this->ZAuth->respond('success', $result, 'Delete Attachment Successful'): 	
	$this->ZAuth->respond('fail', $result, 'Delete Attachment fail');
		
}
protected  function _deleteFile($task_id, $projectTaskAttachments){
	
	if(!empty($projectTaskAttachments)){
		foreach($projectTaskAttachments as $key => $task_attachment){
			$path = $this->getPath('project_tasks', $this->employee_info['Company']);
			if( $task_attachment['ProjectTaskAttachment']['is_file'] == 1){
				$fileInfo = pathinfo($task_attachment['ProjectTaskAttachment']['attachment']);
				@unlink($path . $fileInfo['basename']);
				// if($this->MultiFileUpload->otherServer){
				// 	$this->MultiFileUpload->deleteFileToServerOther($path, $fileInfo['basename']);
				// }
			}
		}
	}
	
	return;
}
// Delete all attachment of one or multi tasks
private function delete_attachment_by_task($task_id, $callback = true){
	$this->loadModels('ProjectTaskAttachment', 'ProjectTaskAttachmentView');
	// delete data task upload in table ProjectTaskAttachment
	$success = false;
	$projectTaskAttachments = $this->ProjectTaskAttachment->find('all', array(
		'recursive' => -1,
		'conditions' => array(
			'task_id' => $task_id,
			'attachment !=' => null
		),
		'fields' => array('task_id', 'attachment', 'is_file')
	));
	if($projectTaskAttachments){
		if($this->ProjectTaskAttachment->deleteAll(array('ProjectTaskAttachment.task_id' => $task_id), false)){
			$this->_deleteFile($task_id, $projectTaskAttachments);
			// delete data status of attchment file task  in table ProjectTaskAttachment
			$this->ProjectTaskAttachmentView->deleteAll(array('ProjectTaskAttachmentView.task_id' => $task_id), false);
			
			$success = true;
		}
	}
	// if($callback) die($success);
}
// Delete all comments by one or multi tasks
// public function delete_all_comments_by_task(){
// 	$taks_id = $_POST['task_id'];
// 	$result= $this->delete_comments($taks_id);
// 	$this->ZAuth->respond('result', $result, 'delete_all_comments_by_task');
// }
private function delete_comments($task_id){
	$this->loadModels('ProjectTaskTxt', 'ProjectTaskTxtRefer');
	$result=$this->ProjectTaskTxt->deleteAll(array('ProjectTaskTxt.project_task_id' => $task_id), false);
	// delete data task upload in table ProjectTaskAttachment
	if($result){
		// delete data status of attchment file task  in table ProjectTaskAttachment
		$this->ProjectTaskTxtRefer->deleteAll(array('ProjectTaskTxtRefer.task_id' => $task_id), false);
	}			
	return $result;
}
    public function list_status() {
		$user = $this->get_user();
		$projectStatus = $this->ProjectTask->ProjectStatus->find('all', array(
            'recursive' => -1,
			'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
            ),
			'order' => 'weight',
            'fields' => array('id', 'name', 'status', 'display', 'weight')
        ));
        foreach($projectStatus as $k => $v){
			$projectStatus[$k] = $v['ProjectStatus'];
		}
		$this->ZAuth->respond('success', $projectStatus);
	}

	public function tasks_by_employee($category = 2) {
		// Debug($this->companyConfigs); exit;
		if( !empty( $this->data['category'] )) {
			$category = $this->data['category'];
			if ($category == 5) $category = array(1,2);
		}
		/**
		* Xu ly project manager. Xoa cac dong = 0
		*/
		// Debug($this->data);
		if (!empty($this->data['task_status_id'])) {
			$this->data['task_status_id'] = array_unique($this->data['task_status_id']);
			if (($key = array_search(0, $this->data['task_status_id'])) !== false) {
				unset($this->data['task_status_id'][$key]);
			}
		}

		$user = $this->get_user();
		// Debug($user);
		$this->loadModel( 'Project', 'ProjectTaskEmployeeRefer');
		
		$myProjects = $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'Project.company_id' => $user['company_id'],
				'Project.category' => $category,
			),
			'fields' => array('id'),
		));
		$myProjects = !empty($myProjects) ? Set::extract($myProjects, '{n}.id') : array();

		$this->loadModel( 'ProjectTaskEmployeeRefer');
		$queryTasks = array(
			'conditions' => array(
				'ProjectTaskEmployeeRefer.reference_id' => $user['id'],
				'ProjectTask.project_id' => $myProjects,
			),
			'fields' => array('ProjectTaskEmployeeRefer.id','ProjectTaskEmployeeRefer.project_task_id'),
		);

		if(!empty($this->data['task_status_id'])) {
			$queryTasks['conditions']['ProjectTask.task_status_id']=$this->data['task_status_id'];
		}
		// Debug($this->data['task_status_id']);
		// Debug($queryTasks);
		// exit;
		$myTasks=$this->ProjectTaskEmployeeRefer->find('all', $queryTasks);
		$myTaskIds = !empty($myTasks) ? Set::extract($myTasks, '{n}.ProjectTaskEmployeeRefer.project_task_id') : array();
		// Debug($user['company_id']); 
		// Debug($user['id']); 
		// Debug($myProjects); 
		// Debug($myTaskIds); exit;
		$myTasks = $this->task_by_ids($myTaskIds);
		$this->ZAuth->respond('success', $myTasks);
	}
	private  function task_by_ids($ids = null) {
		$user = $this->get_user();
		// if( !empty( $this->data['project_id'] )) $project_id = $this->data['project_id'];
		// if( !$this->UserUtility->user_can('read_project', $project_id)) $this->ZAuth->respond('data_incorrect', null, 'data_incorrect', '0');
		$this->loadModels('ProjectTask', 'ProjectTaskEmployeeRefer', 'Employee', 'ProfitCenter', 'Project', 'ProjectAmr');
		$this->ProjectTask->Behaviors->attach('Containable');
		$query = array(
            'fields' => array(
                'ProjectTask.id',
                'ProjectTask.task_title',
                'ProjectTask.parent_id',
                'ProjectTask.project_id',
                'ProjectTask.project_planed_phase_id',
                'ProjectTask.task_priority_id',
                'ProjectTask.task_status_id',
                'ProjectTask.task_start_date',
                'ProjectTask.task_end_date',
                'ProjectTask.estimated',
                'ProjectTask.predecessor',
                'ProjectTask.duration',
                'ProjectTask.weight',
                'ProjectTask.is_nct',
                'ProjectTask.profile_id',
                'ProjectTask.text_1',
                'ProjectTask.attachment',
                'ProjectTask.text_updater',
                'ProjectTask.text_time',
                'ProjectTask.milestone_id',
                'ProjectTask.updated',
                'ProjectPhase.name as phase_name',
                'ProjectStatus.name as status_name',
                'Project.project_name as project_name',
				'ProjectAmr.weather as weather',
            ),
            'recursive' => -1,
            // "conditions" => array(
			// 	'ProjectTask.project_id' => $project_id,
			// ),
			'joins' => array(
				array(
					'table' => 'projects',
					'alias' => 'Project',
					'conditions' => array(
						'ProjectTask.project_id = Project.id',
					),
					'type' => 'left',
				),
				array(
					'table' => 'project_amrs',
					'alias' => 'ProjectAmr',
					'conditions' => array(
						'ProjectTask.project_id = ProjectAmr.project_id',
					),
					'type' => 'left',
				),
				array(
					'table' => 'project_phase_plans',
					'alias' => 'ProjectPhasePlan',
					'conditions' => array(
						'ProjectTask.project_planed_phase_id = ProjectPhasePlan.id',
					),
					'type' => 'left',
				),
				array(
					'table' => 'project_phases',
					'alias' => 'ProjectPhase',
					'conditions' => array(
						'ProjectPhasePlan.project_planed_phase_id = ProjectPhase.id',
					),
					'type' => 'left',
				),
				array(
					'table' => 'project_statuses',
					'alias' => 'ProjectStatus',
					'conditions' => array(
						'ProjectTask.task_status_id = ProjectStatus.id',
					),
					'type' => 'left',
				),
			),
            // 'order' => array('ProjectPhase.name ASC', 'weight ASC')
        );
		$query['conditions'] = array(
			'ProjectTask.id' => $ids
		);
		$query['order'] = array('ProjectTask.task_start_date DESC', 'ProjectTask.task_end_date DESC'); //Sort by task start date desc.
		$projectTasks = $this->ProjectTask->find('all', $query);
		$task_id = !empty($projectTasks) ? Set::extract($projectTasks, '{n}.ProjectTask.id') : array();
		$task_assign_to = array();
		if(!empty($task_id)){
			$resource_assign_to = $this->ProjectTaskEmployeeRefer->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'project_task_id' => $task_id
				),
				'fields' => array('project_task_id', 'reference_id', 'reference_id as id', 'is_profit_center')
			));
			if(!empty($resource_assign_to)){
				$employe_assign = array();
				$pc_assign = array();
				foreach($resource_assign_to as $index => $value){
					$dx = $value['ProjectTaskEmployeeRefer'];
					if($dx['is_profit_center'] == 1){
						$pc_assign[] = $dx['reference_id'];
					}else{
						$employe_assign[] = $dx['reference_id'];
					}
				}
				$employe_assign_name = array();
				$pc_assign_name = array();
				if(!empty($employe_assign)){
					$employe_assign_name = $this->Employee->find('list', array(
						'recursive' => -1,
						'conditions' => array(
							'id' => $employe_assign
						),
						'fields' => array('id', 'fullname')
					));
				}
				if(!empty($pc_assign)){
					$pc_assign_name = $this->ProfitCenter->find('list', array(
						'recursive' => -1,
						'conditions' => array(
							'id' => $pc_assign
						),
						'fields' => array('id', 'name')
					));
				}
				foreach($resource_assign_to as $index => $value){
					$dx = $value['ProjectTaskEmployeeRefer'];
					if(empty($task_assign_to[$dx['project_task_id']])) $task_assign_to[$dx['project_task_id']] = array();
					if($dx['is_profit_center'] == 1){
						$dx['name'] = !empty($pc_assign_name[$dx['reference_id']]) ? 'PC / ' . $pc_assign_name[$dx['reference_id']] : 'PC / NA';
					}else{
						$dx['name'] = !empty($employe_assign_name[$dx['reference_id']]) ? $employe_assign_name[$dx['reference_id']] : 'NA';
					}
					$task_assign_to[$dx['project_task_id']][] = $dx;
				}
			}
		}
		//Add permission info to Task.
		$is_admin = $this->employee_info['Role']['name'] ==  'admin' ? 1 : 0;
		$is_diaryModify = !empty($this->companyConfigs['diary_modify']) ? $this->companyConfigs['diary_modify'] : 0;
		$is_diaryStatus = !empty($this->companyConfigs['diary_status']) ? $this->companyConfigs['diary_modify'] : 0;
		$is_diaryOtherField = !empty($this->companyConfigs['diary_others_fields']) ? $this->companyConfigs['diary_others_fields'] : 0;
		$is_change = (($is_admin == 1) || (($is_diaryModify == 1) && ($is_diaryStatus == 1))) ? 1 : 0;
		$is_changeCommentAndFile = (($is_admin == 1) || (($is_diaryModify == 1) && ($is_diaryOtherField == 1))) ? 1 : 0;

		foreach ($projectTasks as $key => $item) {
			$item = $this->z_merge_all_key($item);
			foreach( array('task_start_date','task_end_date') as $k){
				if( !empty($item[$k]) ) 
					$item[$k] = date(API_DATE_FORMAT, strtotime($item[$k]));
			}
			$task_id = $item['id'];
			//Add permission values to Task
			$item['is_admin'] = $is_admin;
			$item['is_diaryModify'] = $is_diaryModify;
			$item['is_diaryStatus'] = $is_diaryStatus;
			$item['is_diaryOtherField'] = $is_diaryOtherField;
			$item['is_change'] = $is_change;
			$item['is_changeCommentAndFile'] = $is_changeCommentAndFile;


			$projectTasks[$key] = $item;
			$projectTasks[$key]['task_assign_to'] = array();
			if(!empty($task_assign_to[$task_id ])){
				$projectTasks[$key]['task_assign_to'] = $task_assign_to[$task_id ];
			}
		}
		// $this->ZAuth->respond('success', $projectTasks);
		return $projectTasks;
	}
    public function tasks_by_project($project_id = null) {
		$user = $this->get_user();
		if( !empty( $this->data['project_id'] )) $project_id = $this->data['project_id'];
		if( !$this->UserUtility->user_can('read_project', $project_id)) $this->ZAuth->respond('data_incorrect', null, 'data_incorrect', '0');
		$this->loadModels('ProjectTask', 'ProjectTaskEmployeeRefer', 'Employee', 'ProfitCenter');
		$this->ProjectTask->Behaviors->attach('Containable');
		$projectTasks = $this->ProjectTask->find('all', array(
            'fields' => array(
                'ProjectTask.id',
                'ProjectTask.task_title',
                'ProjectTask.parent_id',
                'ProjectTask.project_planed_phase_id',
                'ProjectTask.task_priority_id',
                'ProjectTask.task_status_id',
                'ProjectTask.task_start_date',
                'ProjectTask.task_end_date',
                'ProjectTask.estimated',
                'ProjectTask.predecessor',
                'ProjectTask.duration',
                'ProjectTask.weight',
                'ProjectTask.is_nct',
                'ProjectTask.profile_id',
                'ProjectTask.text_1',
                'ProjectTask.attachment',
                'ProjectTask.text_updater',
                'ProjectTask.text_time',
                'ProjectTask.milestone_id',
                'ProjectPhase.name as phase_name',
                'ProjectStatus.name as status_name',
            ),
            'recursive' => -1,
            "conditions" => array(
				'ProjectTask.project_id' => $project_id,
			),
			'joins' => array(
				array(
					'table' => 'project_phase_plans',
					'alias' => 'ProjectPhasePlan',
					'conditions' => array(
						'ProjectTask.project_planed_phase_id = ProjectPhasePlan.id',
					),
					'type' => 'left',
				),
				array(
					'table' => 'project_phases',
					'alias' => 'ProjectPhase',
					'conditions' => array(
						'ProjectPhasePlan.project_planed_phase_id = ProjectPhase.id',
					),
					'type' => 'left',
				),
				array(
					'table' => 'project_statuses',
					'alias' => 'ProjectStatus',
					'conditions' => array(
						'ProjectTask.task_status_id = ProjectStatus.id',
					),
					'type' => 'left',
				),
			),
            'order' => array('ProjectPhase.name ASC', 'weight ASC')
        ));
		$task_id = !empty($projectTasks) ? Set::extract($projectTasks, '{n}.ProjectTask.id') : array();
		$task_assign_to = array();
		if(!empty($task_id)){
			$resource_assign_to = $this->ProjectTaskEmployeeRefer->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'project_task_id' => $task_id
				),
				'fields' => array('project_task_id', 'reference_id', 'reference_id as id', 'is_profit_center')
			));
			if(!empty($resource_assign_to)){
				$employe_assign = array();
				$pc_assign = array();
				foreach($resource_assign_to as $index => $value){
					$dx = $value['ProjectTaskEmployeeRefer'];
					if($dx['is_profit_center'] == 1){
						$pc_assign[] = $dx['reference_id'];
					}else{
						$employe_assign[] = $dx['reference_id'];
					}
				}
				$employe_assign_name = array();
				$pc_assign_name = array();
				if(!empty($employe_assign)){
					$employe_assign_name = $this->Employee->find('list', array(
						'recursive' => -1,
						'conditions' => array(
							'id' => $employe_assign
						),
						'fields' => array('id', 'fullname')
					));
				}
				if(!empty($pc_assign)){
					$pc_assign_name = $this->ProfitCenter->find('list', array(
						'recursive' => -1,
						'conditions' => array(
							'id' => $pc_assign
						),
						'fields' => array('id', 'name')
					));
				}
				foreach($resource_assign_to as $index => $value){
					$dx = $value['ProjectTaskEmployeeRefer'];
					if(empty($task_assign_to[$dx['project_task_id']])) $task_assign_to[$dx['project_task_id']] = array();
					if($dx['is_profit_center'] == 1){
						$dx['name'] = !empty($pc_assign_name[$dx['reference_id']]) ? 'PC / ' . $pc_assign_name[$dx['reference_id']] : 'PC / NA';
					}else{
						$dx['name'] = !empty($employe_assign_name[$dx['reference_id']]) ? $employe_assign_name[$dx['reference_id']] : 'NA';
					}
					$task_assign_to[$dx['project_task_id']][] = $dx;
				}
			}
		}
		foreach ($projectTasks as $key => $item) {
			$item = $this->z_merge_all_key($item);
			foreach( array('task_start_date','task_end_date') as $k){
				if( !empty($item[$k]) ) 
					$item[$k] = date(API_DATE_FORMAT, strtotime($item[$k]));
			}
			$task_id = $item['id'];
			$projectTasks[$key] = $item;
			$projectTasks[$key]['task_assign_to'] = array();
			if(!empty($task_assign_to[$task_id ])){
				$projectTasks[$key]['task_assign_to'] = $task_assign_to[$task_id ];
			}
		}
		$this->ZAuth->respond('success', $projectTasks);
	}
	
	public function delete_task() {
		if($this->RequestHandler->isPost()) {
            $data = $_POST;

            if(!isset($data['id']) || !isset($data['project_id'])) {
                $this->ZAuth->respond('fail', null, 'data not empty', '0');
                return;
            }
			$project_id = $data['project_id'];
			$id = $data['id'];
			if( !$this->UserUtility->user_can('write_project', $project_id)) {

				$this->ZAuth->respond('fail', null, 'Permission Deny', '0');
				return;
			}
			// Will replace code hear.
			// $result = $this->ProjectTask->delete($id);

			$result = $this->destroyTaskJson($id, $project_id);
			if (!empty($result)) {
				$this->ZAuth->respond('success', $id, 'Delete Task successful.', '0');
			}
			$this->ZAuth->respond('fail', null, 'Project task was not deleted', '0');

		}
		$this->ZAuth->respond('fail', null, 'data_incorrect', '0');
        
    }
	/**
     * Xoa task:: Ref: function destroyTaskJson($id = null) of project_tasks_controller.php
     */
    private function destroyTaskJson($id = null, $project_id=null) {
		// $id = $data['id'];
        $this->loadModel('ActivityTask');
        $this->loadModel('ActivityRequest');
        $success = true;
        if (!empty($id) && is_numeric($id) && !empty($project_id)) {
            /**
             * List task dc chon va task con
             */
            $allTasks = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'OR' => array(
                        'ProjectTask.id' => $id,
                        'ProjectTask.parent_id' => $id
					),
					'ProjectTask.project_id' => $project_id
                ),
                'fields' => array('id', 'task_title', 'project_id', 'project_planed_phase_id')
            ));

			$listTasks = Set::classicExtract($allTasks, '{n}.ProjectTask.id');
            /**
             * Lay cac activity linked
             */
            $activityTasks = $this->ActivityTask->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_task_id' => $listTasks),
                'fields' => array('id', 'name')
            ));
            $listActivityTasks = Set::classicExtract($activityTasks, '{n}.ActivityTask.id');
            $hasUsed = 0;
            if (!empty($activityTasks)) {
                $hasUsed = $this->ActivityRequest->find('count', array(
                    'recursive' => -1,
                    'conditions' => array('task_id' => $listActivityTasks, 'value != 0')
                ));
            }
            if ($hasUsed != 0) {
                $success = false;
                $result = sprintf(__('This task "%s" or its sub-tasks is in used/ has consumed', true), $this->data['task_title']);
                $data = null;
            } else {
                // xoa cac activity request luu dang 0.0
                $activity_tasks = $this->ActivityTask->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_task_id' => $id
                    ),
                    'fields' => array('id', 'project_task_id')
                ));
                $activity_request = $this->ActivityRequest->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'task_id' => array_keys($activity_tasks),
                        'status !=' => 2
                    ),
                    'fields' => array('id', 'id')
                ));
                if(!empty($activity_request)){
                    foreach ($activity_request as $_id) {
                        $this->ActivityRequest->delete($_id);
                    }
                }
                /**
                 * Xoa cac task duoc chon va cac task con
                 */

                // foreach($listTasks as $taskId) {
                    // $this->_deleteFile($taskId);
                // }
                if ($this->ProjectTask->deleteAll(array('ProjectTask.id' => $listTasks), false)) {

                    /**
                     * Xoa cac assign.
                     */
                    $this->loadModel('ProjectTaskEmployeeRefer');
                    $this->loadModel('NctWorkload');
                    $this->NctWorkload->deleteAll(array('NctWorkload.project_task_id' => $listTasks), false);
                    $this->ProjectTaskEmployeeRefer->deleteAll(array('ProjectTaskEmployeeRefer.project_task_id' => $listTasks), false);
					
					// delete file attachment
					$this->delete_attachment_by_task($listTasks, false);
					// delete comments
					$this->delete_comments($listTasks);
                    /**
                     * Xoa cac activity task linked voi project task
                     */
                    $this->ActivityTask->deleteAll(array('ActivityTask.project_task_id' => $listTasks), false);
                    //Tracking action
                    $list = array();
                    $project_id = 0;
                    $project_planed_phase_id = 0;
                    foreach($allTasks as $task) {
                        $list[] = $task['ProjectTask']['task_title'];
                        $project_id = $task['ProjectTask']['project_id'];
                        $project_planed_phase_id = $task['ProjectTask']['project_planed_phase_id'];
                    }
					if(!empty($project_id) && !empty($project_planed_phase_id)) $this->_syncPhasePlanTimeAfterDeleteTask($project_id, $project_planed_phase_id);
                    $list2 = array();
                    foreach($activityTasks as $task) {
                        $list2[] = $task['ActivityTask']['name'];
                    }
                    $projectName = $this->ProjectTask->Project->find("first", array(
                    'recursive' => -1,
                    "fields" => array("Project.project_name"),
                    'conditions' => array('Project.id' => $project_id)));
                    //$this->writeLog(null, $this->employee_info, sprintf('Delete task `%s` under `%s` and activity task `%s` as well', implode('`, `', $list), @$projectName['Project']['project_name'], implode('`, `', $list2)));
                    $employ = $this->employee_info['Employee']['fullname'];
                    $message = $employ . ' ' . date('d/m/Y H:i') . ' ' .__('Project Task', true) . ' ' .  sprintf('Delete task `%s` under `%s` and activity task `%s` as well', implode('`, `', $list), @$projectName['Project']['project_name'], implode('`, `', $list2));
                    $this->writeLog($listTasks, $this->employee_info, $message);
                }
                // $this->_deleteCacheContextMenu();
                $success = $result = true;

            }
        }
        return $success;
        // $result = array("success" => $success, "message" => $result);
        // $this->set(compact('result'));
    }
	/**
     * @uses _syncPhasePlanTimeAfterDeleteTask Sync Phase's date and time after delete a task in a phase.
     * @param type $project_id, $project_planed_phase_id
     */
    private function _syncPhasePlanTimeAfterDeleteTask($project_id, $project_planed_phase_id) {
        $project_phase_plan = $this->_getProjectPhasePlan($project_id, $project_planed_phase_id);
		if (isset($project_phase_plan[0]['ProjectPhasePlan'])) {
			$project_phase_plan = $project_phase_plan[0]['ProjectPhasePlan'];
            $phase_scope = $this->_getPhaseScope($project_id, $project_planed_phase_id);
            $min_date = $phase_scope['MIN(task_start_date)'];
            $max_date = $phase_scope['MAX(task_end_date)'];
            $_min_date = strtotime($min_date);
            $_max_date = strtotime($max_date);
            if(empty($_min_date) || $_min_date == "" || $_min_date == 0){
                $project_phase_plan['phase_real_start_date'] = $project_phase_plan['phase_planed_start_date'];
            } else {
                $project_phase_plan['phase_real_start_date'] = $min_date;
            }
            if(empty($_max_date) || $_max_date == "" || $_max_date == 0){
                $project_phase_plan['phase_real_end_date'] = $project_phase_plan['phase_planed_end_date'];
            } else {
                $project_phase_plan['phase_real_end_date'] = $max_date;
            }
			
            $this->ProjectTask->ProjectPhasePlan->save($project_phase_plan);
			
        } else {
            // Do nothing.
        }
    }
	private function _validate_input_for_new_task($data = null) {
		$user = $this->get_user();
		// debug( $this->employee_info); exit;
		if(!empty($data['z_debug'])){
			debug($data);
			exit;
		}
		unset($data['access_token']);
		unset($data['auth_code']);
		$not_empty = array('project_id', 'task_status_id', 'project_planed_phase_id');
		foreach( $not_empty as $field){
			if( empty($data[$field])) $this->data_incorrect('Missing '. $field);
		}
		$project_id = $data['project_id'];
		$company_id = $this->employee_info['Company']['id'];
		if( !$this->UserUtility->user_can('write_project', $project_id)) $this->data_incorrect('');
		
		$not_allows = array('task_completed', 'task_assign_to', 'duration', 'created', 'updated', 'special', 'special_consumed', 'initial_estimated', 'is_nct', 'profile_id', 'date_type', 'progress_order', 'slider', 'unit_price', 'text_updater', 'text_time');
		foreach($not_allows as $k){
			if( isset($data[$k])) unset($data[$k]);
		}
		foreach( $data as $key => $value){
			$value = is_string($value) ? trim(strip_tags($value)) : $value;
			// if( empty( $value)) continue;
			switch( $key){
				case 'task_start_date' :
				case 'task_end_date' :
				case 'task_real_end_date' :
				case 'initial_task_start_date' :
				case 'initial_task_end_date' :
					if( empty( $value)) {$value='';break;}
					$date_pattern = '/^(?:(?:31(-)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(-)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(-)(?:0?2)\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(-)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/';
					if( !preg_match($date_pattern, $value, $matches)) $this->ZAuth->respond('data_incorrect', $value, sprintf(__('Bad %1$s format, DD-MM-YYYY is required', true), $key), 'ERR_DATE_FORMAT');		
					$value = $this->ProjectTask->convertTime($value);
					break;
				case 'task_priority_id':
					if( $key == 'task_priority_id') {
						$model = 'ProjectPriority';
						$cond = array($model. '.company_id' => $company_id);
					}
				case 'task_status_id':
					if( $key == 'task_status_id'){
						$model = 'ProjectStatus';
						$cond = array($model. '.company_id' => $company_id);
					}
				case 'parent_id':
				case 'predecessor':
					if( $key == 'predecessor'){
						$model = 'ProjectTask';
						$cond = array(
							$model. '.project_id' => $project_id
						);
					}
					if( $key == 'parent_id'){
						$cond = array($model. '.parent_id' => 0);
					}
				case 'milestone_id':
					if( $key == 'milestone_id'){
						$model = 'ProjectMilestone';
						$cond = array($model. '.project_id' => $project_id);
					}
				case 'project_planed_phase_id':
					if( $key == 'project_planed_phase_id') {
						$model = 'ProjectPhasePlan';
						$cond = array($model. '.project_id' => $project_id);
					}
					if( empty( $value)) { $value=""; break;}
					$cond['id'] = $value;
					$this->loadModels($model);
					$ref = 'ref'.$key;
					$$ref = $this->$model->find('first', array(
						'recursive' => -1,
						'conditions' => $cond,
						// 'fields' => array('id') 
					));
					if( empty( $$ref) ) $this->data_incorrect($key . ' incorrect');
					break;
				case 'weight': 
					if( !is_numeric($value))
						 $this->data_incorrect('weight has to be a number');
					$value = intval($value);
					break;
			}
			$data[$key] = $value;
		}
		// Allow truong hop ca 2 empty
		if( !empty($data['task_start_date']) || !empty( $data['task_end_date'])){
			if( empty($data['task_start_date'])) $this->data_incorrect('task_start_date is empty');
			if( empty($data['task_end_date'])) $this->data_incorrect('task_end_date is empty');
			if( strtotime($data['task_start_date']) > strtotime($data['task_end_date'])) $this->data_incorrect('task_start_date > task_end_date');
		}
		$res = array('ProjectTask' => $data);
		// Update comment
		if( !empty($data['text_1'])){
			$res['ProjectTaskTxt'] = array(
				'employee_id' => $user['id'],
				'comment' => $data['text_1'],
				'created' => date('Y-m-d h:i:s')
			);
			unset($res['ProjectTask']['text_1']);
		}
		if( !empty($data['text'])){
			$res['ProjectTaskTxt'] = array(
				'employee_id' => $user['id'],
				'comment' => $data['text'],
				'created' => date('Y-m-d h:i:s')
			);
			unset($res['ProjectTask']['text']);
		}
		// predecessor
		if( !empty( $data['predecessor']) && !empty( $refpredecessor['task_end_date'] ) && !empty( $data['task_start_date'] )){
			$predecessor_ed = strtotime($refpredecessor['task_end_date']);
			$task_start_date = strtotime($data['task_start_date']);
			if( $task_start_date <= $predecessor_ed) $this->data_incorrect('task_start_date must be greater than predecessor task_end_date');
		}
		
		// Assigned to 
		if(!empty( $data['assign_to'])){
			$this->loadModels('Employee', 'ProfitCenter');
			$company_employees = $this->Employee->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company_id,
					'actif' => 1,
					'OR' => array(
						'end_date IS NULL',
						'end_date' => '0000-00-00',
						'end_date >=' => date('Y-m-d', time())
					)
				),
				'fields' => array('id', 'id')
			));
			$company_profits = $this->ProfitCenter->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company_id
				),
				'fields' => array('id', 'id')
			));
			
			$res['ProjectTaskEmployeeRefer'] = array();
			$estimated = 0;
			foreach( $data['assign_to'] as $k => $v){
				$check = (empty($v['is_pc']) ? in_array($v['id'], $company_employees) : in_array($v['id'], $company_profits) );
				if( !$check ) $this->data_incorrect('Resource not found');
				$res['ProjectTaskEmployeeRefer'][] = array(
					'reference_id' => $v['id'],
					'estimated' => !empty($v['estimated']) ? $v['estimated'] : 0,
					'is_profit_center' => !empty($v['is_pc']) ? 1 : 0,
				);
				$estimated += !empty($v['estimated']) ? $v['estimated'] : 0;
			}
			$res['ProjectTask']['estimated'] = $estimated;
		}
		// Debug($res);
		// exit;
		return $res;
		
	}
	private function after_update_task($id = null) {
		$user = $this->get_user();
		$company_id = $this->employee_info['Company']['id'];
		$this->loadModels('ProjectTaskEmployeeRefer');
		if(!empty($this->data['ProjectTaskEmployeeRefer'])){
			foreach( $this->data['ProjectTaskEmployeeRefer'] as $ref){
				$ref['project_task_id'] = $id;
				if(isset($ref['id'])) {
					$this->ProjectTaskEmployeeRefer->id = $ref['id'];
					if(empty($ref['will_del'])) {
						$this->ProjectTaskEmployeeRefer->save($ref);
					}else{
						$this->ProjectTaskEmployeeRefer->delete();
					}
				}else{
					$this->ProjectTaskEmployeeRefer->create();
					$this->ProjectTaskEmployeeRefer->save($ref);
				}
			}
		}
		$this->ProjectTask->Behaviors->recursive=-1;
		$this->ProjectTask->Behaviors->attach('Containable');
		$project_task = $this->ProjectTask->find('first', array(
            'conditions' => array('ProjectTask.id' => $id),
			'contain' => array(
				'ProjectTaskEmployeeRefer'=> array('reference_id', 'estimated', 'is_profit_center'),
			),				
		));
		if( !empty($project_task['ProjectTaskEmployeeRefer'])){
			$project_task['ProjectTask']['assign_to'] = $project_task['ProjectTaskEmployeeRefer'];
		}
		$project_id = $project_task['ProjectTask']['project_id'];
		
		$this->_syncPhasePlanTime($project_task);
		$this->_saveStartEndDateAllTask($project_id);
		$this->_syncActivityTask($project_id, $project_task, $id);
		$this->Session->write('Auth.employee_info', $this->employee_info);
		$this->ProjectTask->staffingSystem($project_id,false);
		$log = 'Create new Project Tasks item `%s` by %s use API Web Services';
		$this->writeLog($this->data, array( 'Employee' => $user), sprintf($log, $id, $user["first_name"].' '.$user["last_name"]), $company_id);
		return $project_task['ProjectTask'];
	}
	
	public function add_task(){ // Normal(continue) task
		$user = $this->get_user();
		$this->data = $this->_validate_input_for_new_task($this->data);
		if(!empty($this->data['id'])){
			$this->ProjectTask->id = $this->data['id'];
		}else{
			$this->ProjectTask->create();
		}
		// debug($this->data['ProjectTask']); exit;
		$result = $this->ProjectTask->save($this->data['ProjectTask']);
		// debug( $result); exit;
		if( $result){
			// $this->ProjectTask->recursive = -1;
			// $item = $this->ProjectTask->read();
			$new_task_id = $this->ProjectTask->id;
			$item = $this->after_update_task($new_task_id);

			$this->notifyForNewTask($item['project_id'], $new_task_id, $result['ProjectTask']['task_title']); // Notify to users.

			$this->ZAuth->respond('success', $item);
		}
		$this->ZAuth->respond('failed', null, 'save_failed', 'NOT_SAVED');
	}
	private function _validate_input_for_update_task($dataUpdate = null) {
		$user = $this->get_user();
		$company_id = $this->employee_info['Company']['id'];
		// debug( $this->employee_info); exit;
		if(!empty($dataUpdate['z_debug'])){
			debug($dataUpdate);
			exit;
		}
		// unset($dataUpdate['access_token']);
		// unset($dataUpdate['auth_code']);
		$allow_fields = array('id','task_title', 'task_start_date', 'task_end_date', 'project_planed_phase_id', 'assign_to', 'task_status_id');
		$data = array();
		foreach($allow_fields as $field) {
			if(isset($dataUpdate[$field])) {
				$data[$field] = $dataUpdate[$field];
			} else {
				$this->data_incorrect('Missing '. $field);
			}
		}

		$this->loadModels('Employee', 'ProfitCenter', 'ProjectTaskEmployeeRefer', 'ProjectTask');
		$task = $this->ProjectTask->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $data['id'],
			),
			'fields' => array('id', 'project_id')
		));
		// debug( $task); exit;
		if(empty($task)) $this->data_incorrect('Data incorrect');
		$project_id = $task['ProjectTask']['project_id'];
		// $data[$key] = $value;
		//Tam thoi dong de consultant co the edit dc task.
		if(!$this->UserUtility->user_can('write_project', $task['ProjectTask']['project_id'])) $this->data_incorrect('Data incorrect');
		
		foreach( $data as $key => $value){
			$value = is_string($value) ? trim(strip_tags($value)) : $value;
			// if( empty( $value)) continue;
			switch( $key){
				case 'task_start_date' :
				case 'task_end_date' :
					if( empty( $value)) {$value='';break;}
					$date_pattern = '/^(?:(?:31(-)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(-)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(-)(?:0?2)\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(-)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/';
					if( !preg_match($date_pattern, $value, $matches)) $this->ZAuth->respond('data_incorrect', $value, sprintf(__('Bad %1$s format, DD-MM-YYYY is required', true), $key), 'ERR_DATE_FORMAT');		
					$value = $this->ProjectTask->convertTime($value);
					break;
				
				case 'task_status_id':
					if( $key == 'task_status_id'){
						$model = 'ProjectStatus';
						$cond = array($model. '.company_id' => $company_id);
					}
			
				case 'project_planed_phase_id':
					if( $key == 'project_planed_phase_id') {
						$model = 'ProjectPhasePlan';
						$cond = array($model. '.project_id' => $project_id);
					}
					if( empty( $value)) { $value=""; break;}
					$cond['id'] = $value;
					$this->loadModels($model);
					$ref = 'ref'.$key;
					$$ref = $this->$model->find('first', array(
						'recursive' => -1,
						'conditions' => $cond,
						// 'fields' => array('id') 
					));
					if( empty( $$ref) ) $this->data_incorrect($key . ' incorrect');
					break;
				// case 'weight': 
				// 	if( !is_numeric($value))
				// 		 $this->data_incorrect('weight has to be a number');
				// 	$value = intval($value);
				// 	break;
			}
			$data[$key] = $value;
		}
		// Allow truong hop ca 2 empty
		if( !empty($data['task_start_date']) || !empty( $data['task_end_date'])){
			if( empty($data['task_start_date'])) $this->data_incorrect('task_start_date is empty');
			if( empty($data['task_end_date'])) $this->data_incorrect('task_end_date is empty');
			if( strtotime($data['task_start_date']) > strtotime($data['task_end_date'])) $this->data_incorrect('task_start_date > task_end_date');
		}
		$res = array('ProjectTask' => $data);
		
		
		// Assigned to 
		if(!empty( $data['assign_to'])){
			
			$company_employees = $this->Employee->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company_id,
					'actif' => 1,
					'OR' => array(
						'end_date IS NULL',
						'end_date' => '0000-00-00',
						'end_date >=' => date('Y-m-d', time())
					)
				),
				'fields' => array('id', 'id')
			));
			$company_profits = $this->ProfitCenter->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company_id
				),
				'fields' => array('id', 'id')
			));
			
			$assignedList = $this->ProjectTaskEmployeeRefer->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'project_task_id' => $data['id'],
				),
				
				'fields' => array('id', 'reference_id', 'is_profit_center', 'estimated'),
			));
			$assigned = array();
			foreach($assignedList as $ref){
				$ref = $ref['ProjectTaskEmployeeRefer'];
				$ref['will_del'] = 1;
				$assigned[$ref['reference_id'] . '-' . $ref['is_profit_center']] = $ref;
			}
			// $res['ProjectTaskEmployeeRefer'] = $assigned;
			$estimated = 0;
			foreach( $data['assign_to'] as $k => $v){
				$v['is_pc'] = isset($v['is_pc']) ? $v['is_pc'] : 0;
				$k = $v['id'].'-'.$v['is_pc'];
				if(isset($assigned[$k])) { //update
					$assigned[$k]['estimated'] = isset($v['estimated']) ? floatval($v['estimated']) : ($assigned[$k]['estimated'] ? $assigned[$k]['estimated'] : 0);
					$assigned[$k]['will_del'] = 0;
					$estimated += $assigned[$k]['estimated'];
				} else { // add new
					$check = (empty($v['is_pc']) ? in_array($v['id'], $company_employees) : in_array($v['id'], $company_profits) );
					if( !$check ) $this->data_incorrect('Resource not found');
					$assigned[$k] = array(
						'reference_id' => $v['id'],
						'estimated' => !empty($v['estimated']) ? $v['estimated'] : 0,
						'is_profit_center' => !empty($v['is_pc']) ? 1 : 0,
					);
					$estimated += !empty($v['estimated']) ? $v['estimated'] : (0);
				}
			}
			$res['ProjectTask']['estimated'] = $estimated;
			$res['ProjectTaskEmployeeRefer'] = $assigned;
			// debug($res); exit;
		}
		return $res;
		
	}
	public function update_task(){ // Normal(continue) task
		$user = $this->get_user();
		$this->data = $this->_validate_input_for_update_task($this->data);
		
		$this->ProjectTask->id = $this->data['ProjectTask']['id'];
		
		// debug($this->data['ProjectTask']); exit;
		$result = $this->ProjectTask->save($this->data['ProjectTask']);
		// debug( $result); exit;
		if( $result){
			// $this->ProjectTask->recursive = -1;
			// $item = $this->ProjectTask->read();
			$new_task_id = $this->ProjectTask->id;
			$item = $this->after_update_task($new_task_id);

			$this->notifyForUpdateTask($item['project_id'], $new_task_id, $result['ProjectTask']['task_title']); // Notify to users.
			$this->ZAuth->respond('success', $item);


		}
		$this->ZAuth->respond('failed', null, 'save_failed', 'NOT_SAVED');
	}
	private function _validate_input_for_update_task_consultant($dataUpdate = null) {
		$user = $this->get_user();
		$company_id = $this->employee_info['Company']['id'];
		// debug( $this->employee_info); exit;
		if(!empty($dataUpdate['z_debug'])){
			debug($dataUpdate);
			exit;
		}
		// unset($dataUpdate['access_token']);
		// unset($dataUpdate['auth_code']);
		$allow_fields = array('id','task_title', 'project_planed_phase_id', 'task_status_id');
		$data = array();
		foreach($allow_fields as $field) {
			if(isset($dataUpdate[$field])) {
				$data[$field] = $dataUpdate[$field];
			} else {
				$this->data_incorrect('Missing '. $field);
			}
		}

		$this->loadModels('Employee', 'ProfitCenter', 'ProjectTaskEmployeeRefer', 'ProjectTask');
		$task = $this->ProjectTask->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $data['id'],
			),
			'fields' => array('id', 'project_id')
		));
		// debug( $task); exit;
		if(empty($task)) $this->data_incorrect('Data incorrect');
		$project_id = $task['ProjectTask']['project_id'];
		// $data[$key] = $value;
		//Tam thoi dong de consultant co the edit dc task.
		// if(!$this->UserUtility->user_can('write_task', $task['ProjectTask']['project_id'])) $this->data_incorrect('Data incorrect');
		
		foreach( $data as $key => $value){
			$value = is_string($value) ? trim(strip_tags($value)) : $value;
			// if( empty( $value)) continue;
			switch( $key){
				
				case 'task_status_id':
					if( $key == 'task_status_id'){
						$model = 'ProjectStatus';
						$cond = array($model. '.company_id' => $company_id);
					}
			
				case 'project_planed_phase_id':
					if( $key == 'project_planed_phase_id') {
						$model = 'ProjectPhasePlan';
						$cond = array($model. '.project_id' => $project_id);
					}
					if( empty( $value)) { $value=""; break;}
					$cond['id'] = $value;
					$this->loadModels($model);
					$ref = 'ref'.$key;
					$$ref = $this->$model->find('first', array(
						'recursive' => -1,
						'conditions' => $cond,
						// 'fields' => array('id') 
					));
					if( empty( $$ref) ) $this->data_incorrect($key . ' incorrect');
					break;
				
			}
			$data[$key] = $value;
		}
		// Allow truong hop ca 2 empty
		// if( !empty($data['task_start_date']) || !empty( $data['task_end_date'])){
		// 	if( empty($data['task_start_date'])) $this->data_incorrect('task_start_date is empty');
		// 	if( empty($data['task_end_date'])) $this->data_incorrect('task_end_date is empty');
		// 	if( strtotime($data['task_start_date']) > strtotime($data['task_end_date'])) $this->data_incorrect('task_start_date > task_end_date');
		// }
		$res = array('ProjectTask' => $data);
		
		
		
		return $res;
		
	}
	public function update_task_by_consultant(){ // Normal(continue) task
		$user = $this->get_user();
		$this->data = $this->_validate_input_for_update_task_consultant($this->data);
		$this->ProjectTask->id = $this->data['ProjectTask']['id'];
		
		// debug($this->data['ProjectTask']); exit;
		$result = $this->ProjectTask->save($this->data['ProjectTask']);
		// debug( $result); exit;
		if( $result){
			$this->ProjectTask->recursive = -1;
			$item = $this->ProjectTask->read();
			$new_task_id = $this->ProjectTask->id;
			$item = $this->after_update_task($new_task_id);
			$this->notifyForNewTask($item['project_id'], $new_task_id, $result['ProjectTask']['task_title']); // Notify to users.
			$this->ZAuth->respond('success', $item);
		}
		$this->ZAuth->respond('failed', null, 'save_failed', 'NOT_SAVED');
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
	private function _getHoliday($startDate, $endDate){
        $this->loadModel('Holiday');
		$user = $this->ZAuth->user();
		$company_id = $user['company_id'];	
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
	private function _syncPhasePlanTime($project_task) {
		$this->loadModel('ProjectTask');
        $project_id = $project_task["ProjectTask"]["project_id"];
        $project_planed_phase_id = $project_task["ProjectTask"]["project_planed_phase_id"];
        $task_end_date = strtotime($project_task["ProjectTask"]["task_end_date"]);
        $task_start_date = strtotime($project_task["ProjectTask"]["task_start_date"]);
        $project_phase_plan = $this->_getProjectPhasePlan($project_id, $project_planed_phase_id);
        if (isset($project_phase_plan[0]['ProjectPhasePlan'])) {
            $project_phase_plan = $project_phase_plan[0]['ProjectPhasePlan'];
            $phase_scope = $this->_getPhaseScope($project_id, $project_planed_phase_id);
            $min_date = $phase_scope['MIN(task_start_date)'];
            $max_date = $phase_scope['MAX(task_end_date)'];
            $_min_date = strtotime($min_date);
            $_max_date = strtotime($max_date);
            if(empty($_min_date) || $_min_date == "" || $_min_date == 0){
                if( $task_start_date )$project_phase_plan['phase_real_start_date'] = date('Y-m-d', $task_start_date);
            } else {
                if( $task_start_date && $task_start_date < $_min_date ){
                    $min_date = date('Y-m-d', $task_start_date);
                }
                $project_phase_plan['phase_real_start_date'] = $min_date;
            }
            if(empty($_max_date) || $_max_date == "" || $_max_date == 0){
                if( $task_end_date )$project_phase_plan['phase_real_end_date'] = date('Y-m-d', $task_end_date);
            } else {
                if( $task_end_date && $task_end_date > $_max_date ){
                    $max_date = date('Y-m-d', $task_end_date);
                }
                $project_phase_plan['phase_real_end_date'] = $max_date;
            }
            //$project_phase_plan[phase_planed_start_date]
            if( !$project_phase_plan['phase_planed_start_date'] || $project_phase_plan['phase_planed_start_date'] == '0000-00-00' ){
                $project_phase_plan['phase_planed_start_date'] = $project_phase_plan['phase_real_start_date'];
            }
            if( !$project_phase_plan['phase_planed_end_date'] || $project_phase_plan['phase_planed_end_date'] == '0000-00-00' ){
                $project_phase_plan['phase_planed_end_date'] = $project_phase_plan['phase_real_end_date'];
            }
            $project_phase_plan['planed_duration'] = $this->getWorkingDays($project_phase_plan['phase_planed_start_date'], $project_phase_plan['phase_planed_end_date'], 0);
            $this->ProjectTask->ProjectPhasePlan->save($project_phase_plan);
        } else {
            // Do nothing.
        }
    }
	private function _getProjectPhasePlan($project_id, $project_planed_phase_id) {
		$this->loadModel('ProjectTask');
        $this->ProjectTask->ProjectPhasePlan->Behaviors->attach('Containable');
        $projectPhasePlan = $this->ProjectTask->ProjectPhasePlan->find(
                'all', array(
                    'conditions' => array(
                        // "ProjectPhasePlan.project_planed_phase_id" => $project_planed_phase_id,
                        "ProjectPhasePlan.id" => $project_planed_phase_id,
                        "ProjectPhasePlan.project_id" => $project_id
                    )
                )
        );
        return $projectPhasePlan;
    }
	  private function _getPhaseScope($project_id, $project_planed_phase_id) {
        $this->ProjectTask->Behaviors->attach('Containable');
        $this->ProjectTask->cacheQueries = true;

        $conditions['OR'] = array(
            array('parent_id' => array(0)),
            array('parent_id' => null)
        );
        //'fields' => array('MAX(Yacht.price) as max_price', 'MIN(Yacht.price) as min_price', ...)
        $projectTasks = $this->ProjectTask->find("all", array(
            'fields' => array(
                'id',
                'task_title',
                'parent_id',
                'project_planed_phase_id',
                'task_priority_id',
                'task_status_id',
                'milestone_id',
                'task_assign_to',
                'task_completed',
                'task_start_date',
                'task_end_date',
                'task_real_end_date',
                'estimated',
                'MIN(task_start_date)',
                'MAX(task_end_date)'
                ),
            'recursive' => -1,
            "conditions" => array(
                'project_id' => $project_id,
                'project_planed_phase_id' => $project_planed_phase_id,
                'OR' => array(
                    'parent_id' => null,
                    'parent_id' => 0,
                ),
                'NOT' => array(
                    'task_start_date' => '0000-00-00',
                    'task_end_date' => '0000-00-00'
                )
            )
        ));
        return $projectTasks[0][0];
    }
	private function _saveStartEndDateAllTask($project_id) {
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
	 private function _getStartEndDateAllTask($project_id) {
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
	 private function _syncActivityTask($project_id, $project_task, $project_task_id){
		$this->loadModel('Activity');
		$activity = $this->Activity->find('all',array(
			'recursive' => -1,
			'conditions' => array('Activity.project' => $project_id),
		));
        if (!empty($activity)) {
            if (isset($activity[0])) {
                if(isset($activity[0]['Activity'])) {
                    if(isset($activity[0]['Activity']['id'])){
                        $activity_id = $activity[0]['Activity']['id'];
                        $this->_createActivityTask($project_task, $activity_id, $project_task_id, $project_id);
                    }
                }
            }
        }

    }
	private function _createActivityTask($project_task, $activity_id, $project_task_id, $project_id){
        $dataActivityTask = array();
        $phase_name = !empty($project_task['ProjectTask']['project_planed_phase_id']) ? $this->_getPhaseNameByPhasePlanId($project_id,$project_task['ProjectTask']['project_planed_phase_id']) : '';
        $activity_task_name = $phase_name . "/" . $project_task['ProjectTask']['task_title'];
        $this->loadModel('ActivityTask');
        $checkTask = $this->ActivityTask->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'activity_id' => $activity_id,
                'project_task_id' => $project_task_id
            ),
            'fields' => array('id')
        ));
        if(!empty($checkTask)){
            $this->ActivityTask->id = $checkTask['ActivityTask']['id'];
            $dataActivityTask['ActivityTask']['name'] = $activity_task_name;
        } else {
            $this->ActivityTask->create();
            $dataActivityTask['ActivityTask']['name'] = $activity_task_name;
            $dataActivityTask['ActivityTask']['activity_id'] = $activity_id;
            $dataActivityTask['ActivityTask']['project_task_id'] = $project_task_id;
        }
        $dataActivityTask['ActivityTask']['task_status_id'] = $project_task['ProjectTask']['task_status_id'];
        $dataActivityTask['ActivityTask']['milestone_id'] = $project_task['ProjectTask']['milestone_id'];
        $dataActivityTask['ActivityTask']['is_nct'] = isset($project_task['ProjectTask']['is_nct']) ? $project_task['ProjectTask']['is_nct'] : 0;
        $dataActivityTask['ActivityTask']['manual_consumed'] = isset($project_task['ProjectTask']['manual_consumed']) ? $project_task['ProjectTask']['manual_consumed'] : 0;
        $dataActivityTask['ActivityTask']['special'] = isset($project_task['ProjectTask']['special']) ? $project_task['ProjectTask']['special'] : 0;
        $dataActivityTask['ActivityTask']['amount'] = isset($project_task['ProjectTask']['amount']) ? $project_task['ProjectTask']['amount'] : 0;
        $dataActivityTask['ActivityTask']['progress_order'] = isset($project_task['ProjectTask']['progress_order']) ? $project_task['ProjectTask']['progress_order'] : 0;
        $result = $this->ActivityTask->save($dataActivityTask['ActivityTask']);
        //update nctworkload activity task id
        $this->loadModel('NctWorkload');
        $this->NctWorkload->updateAll(array(
            'NctWorkload.activity_task_id' => $this->ActivityTask->id
        ), array(
            'NctWorkload.project_task_id' => $project_task_id
        ));
        return $result;
    }
	 private function _getPhaseNameByPhasePlanId($project_id, $project_planed_phase_id){
        $projectPhases = $this->_getPhaseses($project_id);
        $projectPhases = Set::combine($projectPhases, '{n}.ProjectPhasePlan.id', '{n}.ProjectPhase.name');
        return $projectPhases[$project_planed_phase_id];
    }
	private function _getPhaseses($project_id) {
        if (!isset($this->_phases)) {
			$user = $this->ZAuth->user();
			$company_id = $user['company_id'];
            $this->ProjectTask->ProjectPhasePlan->Behaviors->attach('Containable');
            $projectPhases = $this->ProjectTask->ProjectPhasePlan->find('all', array(
                'fields' => array('id', 'phase_planed_start_date', 'phase_planed_end_date', 'project_part_id'),
                'contain' => array('ProjectPhase' => array('id', 'name'), 'ProjectPart' => array('id', 'title')),
                'conditions' => array(
                    "ProjectPhasePlan.project_id" => $project_id,
                    'company_id' => $company_id
                )
            ));
            $this->_phases = $projectPhases;
        }
        return $this->_phases;
    }
}