<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectUtilitiesController extends AppController {
	var $uses = array(
		'Project', 
		'TmpStaffingSystem',
	);
	var $name = 'ProjectUtilities';
	
	public function index(){}
	public function delete_archived_project(){
		$company_id = $this->employee_info["Company"]["id"];
		$project_archived = $this->Project->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'Project.company_id' => $company_id,
				'Project.category' => '3',
			),
			'fields' => array('Project.id', 'Project.project_name', 'Project.start_date', 'Project.end_date'),
		));
		$project_archived = !empty( $project_archived) ? Set::combine( $project_archived, '{n}.Project.id', '{n}.Project') : array();
		$staffing = $this->TmpStaffingSystem->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'TmpStaffingSystem.project_id' => array_keys($project_archived),
				'TmpStaffingSystem.model' => 'employee',
			),
			'fields' => array('TmpStaffingSystem.project_id', 'sum(TmpStaffingSystem.estimated) as estimated', 'sum(TmpStaffingSystem.consumed) as consumed'),
			'group' => array('TmpStaffingSystem.project_id')
		));
		$staffing = !empty( $staffing) ? Set::combine( $staffing, '{n}.TmpStaffingSystem.project_id', '{n}.0') : array();
		foreach( $staffing as $id => $v){
			$project_archived[$id] = array_merge( $project_archived[$id], $v); // left joins
		}
		$this->set(compact('project_archived'));
	}
	
	/* Delete Archived Project
	 * @access: Admin
	 *
	 */
	public function delete_projects($project_id){
		$this->layout = false;
		$this->loadModels('Project', 'ProjectFunctionEmployeeRefer', 'ProjectTeam');
		$list_model_delete = array(
			'ProjectAcceptance',
			'ProjectAmr',
			'ProjectBudgetExternal',
			'ProjectBudgetInternalDetail',
			'ProjectBudgetInternal',
			'ProjectBudgetInvoice',
			'ProjectBudgetProvisional',
			'ProjectBudgetPurchaseInvoice',
			'ProjectBudgetPurchase',
			'ProjectBudgetSale',
			'ProjectBudgetSyn',
			'ProjectCreatedVal',
			'ProjectCreatedValsComment',
			'ProjectDecision',
			'ProjectDependency',
			'ProjectEmployeeManager',
			'ProjectEvolutionImpactRefer',
			'ProjectEvolution',
			'ProjectExpectation',
			'ProjectFinancePlus',
			'ProjectFinancePlusDate',
			'ProjectFinancePlusAttachment',
			'ProjectFinancePlusTxt',
			'ProjectFinanceTwoPlusDate',
			'ProjectFinanceTwoPlusDetail',
			'ProjectFinanceTwoPlus',
			'ProjectFinance',
			'ProjectIssue',
			'ProjectListMultiple',
			'ProjectLivrableActor',
			'ProjectLivrable',
			'ProjectMilestone',
			'ProjectPart',
			'ProjectPhaseCurrent',
			'ProjectPhasePlan',
			'ProjectPowerbiDashboard',
			'ProjectRisk',
			'ProjectTaskAttachment',
			'ProjectTeam',
			'ProjectFile',
			'ProjectGlobalView',
			'ProjectImage',
			'ProjectLocalView',
			'ProjectTaskFavourite',
			'ProjectTask',
			'TmpStaffingSystem',
		);
		$result = 'KO';
		$delete = $this->Project->delete($project_id);
		if($delete){
			
			$this->delete_files($project_id);
			$this->delete_comments($project_id);
			
			$projectTeamIds = $this->ProjectTeam->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'project_id' => $project_id, 
				),
				'fields' => array('id')
			));
			if(!empty($projectTeamIds)){
				$this->ProjectFunctionEmployeeRefer->deleteAll(array('project_team_id' => $projectTeamIds), false);
			}
			$activity_id = $this->Project->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $project_id, 
				),
				'fields' => array('activity_id')
			));
			
			if(!empty($activity_id) && !empty($activity_id['Project']['activity_id'])){
				$this->deleteDataActivity($activity_id['Project']['activity_id']);
			}
			foreach($list_model_delete as $key => $model_name){
				$this->delete_data_relative($model_name, $project_id);
			}
			$result = 'success';
		}
		die($result);
	
	}
	public function deleteDataActivity($activity_id){
		$this->loadModels('ActivityTask', 'ActivityTaskEmployeeRefer', 'ActivityRequest', 'ActivityProfitRefer', 'Activity');
		
		$activityTaskIds = $this->ActivityTask->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'activity_id' => $activity_id, 
			),
			'fields' => array('id')
		));
		
		if(!empty($activityTaskIds)){
			$this->ActivityTaskEmployeeRefer->deleteAll(array('ActivityTaskEmployeeRefer.activity_task_id' => $activityTaskIds), false);
			$this->ActivityRequest->deleteAll(array('ActivityRequest.task_id' => $activityTaskIds), false);
		}
		
		$this->ActivityProfitRefer->deleteAll(array('ActivityProfitRefer.activity_id' => $activity_id), false);
		$this->ActivityRequest->deleteAll(array('ActivityRequest.activity_id' => $activity_id), false);
		$this->ActivityTask->deleteAll(array('ActivityTask.activity_id' => $activity_id), false);
		$this->Activity->deleteAll(array('Activity.id' => $activity_id), false);
		
			
	}
	private function delete_data_relative($model_name, $project_id){
		$this->loadModel($model_name);
		$field = $model_name . '.project_id';
		$this->$model_name->deleteAll(array($field => $project_id), false);
	}
	
	
	/* Delete files after delete Archived Project
	 * @access: Private
	 *
	 */
	private function delete_comments($project_id){
		
		$this->loadModels('ProjectFinancePlusTxtView', 'ProjectFinancePlus', 'LogSystem', 'ProjectTask', 'ProjectTaskTxt', 'ProjectTaskTxtRefer', 'ProjectTaskAttachmentView', 'ProjectFinancePlusAttachmentView', 'NctWorkload', 'ProjectTaskEmployeeRefer');
		
		// Delete ProjectFinancePlusTxt
		$projectFinancePlusIds = $this->ProjectFinancePlus->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id, 
			),
			'fields' => array('id')
		));
		
		if(!empty($projectFinancePlusIds)){
			$this->ProjectFinancePlusTxtView->deleteAll(array('project_finance_plus_id' => $projectFinancePlusIds), false);
			$this->ProjectFinancePlusAttachmentView->deleteAll(array('project_finance_plus_id' => $projectFinancePlusIds), false);
		}
		// Project commments : LogSystem
		$this->LogSystem->deleteAll(array('model_id' => $project_id), false);
		
		// Delete comment of project tasks.
		$projectTaskIds = $this->ProjectTask->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id, 
			),
			'fields' => array('id')
		));
		
		if(!empty($projectTaskIds)){
			$this->ProjectTaskTxt->deleteAll(array('project_task_id' => $projectTaskIds), false);
			$this->ProjectTaskTxtRefer->deleteAll(array('task_id' => $projectTaskIds), false);
			$this->ProjectTaskAttachmentView->deleteAll(array('task_id' => $projectTaskIds), false);
			$this->NctWorkload->deleteAll(array('project_task_id' => $projectTaskIds), false);
			$this->ProjectTaskEmployeeRefer->deleteAll(array('project_task_id' => $projectTaskIds), false);
		}
		
	}
	
	/* Delete files after delete Archived Project
	 * @access: Private
	 *
	 */
	private function delete_files($project_id){
		$this->loadModels('ProjectLivrable', 'ProjectLivrableComment', 'ProjectFile', 'ProjectGlobalView', 'ProjectImage', 'ProjectTaskAttachment','ProjectFinancePlusAttachment', 'ProjectLocalView', 'ProjectBudgetInvoice', 'ProjectBudgetExternal', 'ProjectBudgetSale');

		$company_name = $this->getCompanyDir();
		
		// delete file attachment ProjectLocalView
		$projectLocalView = $this->ProjectLocalView->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id, 
			),
			'fields' => array('attachment')
		));
		if(!empty($projectLocalView)){
			$pathLocalViewFile = $this->_getPath($project_id, 'projects', 'localviews') . $projectLocalView['ProjectLocalView']['attachment'];
			@unlink(trim($pathLocalViewFile));
		}
		
		// delete ProjectFinancePlusAttachment
		$projectFinancePlusAttachment = $this->ProjectFinancePlusAttachment->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id, 
			),
			'fields' => array('attachment')
		));
	
		if(!empty($projectFinancePlusAttachment)){
			$pathFinanceFile = $this->_getPath($project_id, 'projects', 'financeplus') . DS . $project_id . DS;
			foreach($projectFinancePlusAttachment as $key => $financeFile){
				@unlink($pathFinanceFile . $financeFile['ProjectFinancePlusAttachment']['attachment']);
			}
			
		}
		
		// Delete ProjectTaskAttachment
		$projectTaskAttachment = $this->ProjectTaskAttachment->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id, 
			),
			'fields' => array('attachment')
		));
	
		if(!empty($projectTaskAttachment)){
			$pathTaskFile = FILES . 'projects' . DS . 'project_tasks' . DS . $this->employee_info['Company']['id'] . DS;
			foreach($projectTaskAttachment as $key => $task_file){
				@unlink($pathTaskFile . $task_file['ProjectTaskAttachment']['attachment']);
			}
			
		}
		
		// Delete ProjectImage
		$projectImage = $this->ProjectImage->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id, 
			),
			'fields' => array('file', 'thumbnail')
		));
		
		if(!empty($projectImage)){
			$pathProjectFile = FILES . 'projects' . DS . $this->employee_info['Company']['id'] . DS . $project_id . DS;
			foreach($projectImage as $key => $project_image){
				$attachment = $project_image['ProjectImage']['file'];
				$thumbnail = $project_image['ProjectImage']['thumbnail'];
				@unlink($pathProjectFile . $attachment);
				@unlink($pathProjectFile . 'r_' . $attachment);
				@unlink($pathProjectFile . 'l_' . $attachment);
				@unlink($pathProjectFile . $thumbnail);
			}
			
		}
		
		// Delete ProjectGlobalView
		$projectGlobalView = $this->ProjectGlobalView->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id, 
			),
			'fields' => array('attachment')
		));
		
		if(!empty($projectGlobalView)){
			$pathGlobalViewFile = $this->_getPath($project_id, 'projects', 'globalviews') . $projectGlobalView['ProjectGlobalView']['attachment'];
			@unlink(trim($pathGlobalViewFile));
		}
		
		// Delete project files.
		$project_files = $this->ProjectFile->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id, 
			),
			'fields' => array('key', 'file_attachment')
		));
		if(!empty($project_files)){
			$pathProjectFile = FILES . 'projects' . DS;
			foreach($project_files as $key => $project_file){
				// delete file
				$path = $pathProjectFile . $project_file['ProjectFile']['key'] . DS . $company_name . DS . $project_id . DS . $project_file['ProjectFile']['file_attachment'];
				@unlink(trim($path));
			}
		}
		
		// Delete ProjectLivrableComment and ProjectLivrable file upload
		$projectLivrable = $this->ProjectLivrable->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id, 
			),
			'fields' => array('id', 'livrable_file_attachment')
		));
		$project_livrable_id = array();
		if(!empty($projectLivrable)){
			$pathLivrable = $this->_getPath($project_id, 'projects', 'livrable');
			foreach($projectLivrable as $key => $livrs){
				$project_livrable_id[] = $livrs['ProjectLivrable']['id'];
				// delete file
				@unlink(trim($pathLivrable . $livrs['ProjectLivrable']['livrable_file_attachment']));
				
			}
		}
		if(!empty($project_livrable_id)) $this->ProjectLivrableComment->deleteAll(array('project_livrable_id' => $project_livrable_id), false);
		
		// Delete file ProjectBudgetInvoice.
		$projectBudgetInvoice = $this->ProjectBudgetInvoice->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id, 
			),
			'fields' => array('file_attachement')
		));
		if(!empty($projectBudgetInvoice)){
			$pathInvoice = $this->_getPath($project_id, 'project_budgets', 'sales'). $project_id . DS;
			foreach($projectBudgetInvoice as $key => $invoice){
				// delete file
				@unlink(trim($pathInvoice . $invoice['ProjectBudgetInvoice']['file_attachement']));
				
			}
		}
		// Delete file ProjectBudgetExternal.
		$projectBudgetExternal = $this->ProjectBudgetExternal->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id, 
			),
			'fields' => array('file_attachement')
		));
		if(!empty($projectBudgetExternal)){
			$pathExternal = $this->_getPath($project_id, 'project_budgets', 'externals'). $project_id . DS;
			foreach($projectBudgetExternal as $key => $external){
				// delete file
				@unlink(trim($pathExternal . $external['ProjectBudgetExternal']['file_attachement']));
				
			}
		}
		
		// Delete file ProjectBudgetSale.
		$projectBudgetSale = $this->ProjectBudgetSale->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id, 
			),
			'fields' => array('file_attachement')
		));
		if(!empty($projectBudgetSale)){
			$pathSale = $this->_getPath($project_id, 'project_budgets', 'sales'). $project_id . DS;
			foreach($projectBudgetSale as $key => $sale){
				// delete file
				@unlink(trim($pathSale . $sale['ProjectBudgetSale']['file_attachement']));
				
			}
		}

	}
	protected function _getPath($project_id, $dir_name1 = 'projects', $dir_name2, $useParentDir = true) {
		$this->loadModel('ProjectLivrable', 'Project', 'Company');
        $company = $this->ProjectLivrable->Project->find('first', array(
            'recursive' => 0,
            'fields' => array(
                'Company.parent_id',
                'Company.company_name',
                'Company.dir'
            ), 'conditions' => array('Project.id' => $project_id)));
        $pcompany = $this->ProjectLivrable->Project->Company->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company['Company']['parent_id'])));
        $path = FILES . $dir_name1 . DS . $dir_name2 . DS;
        if ($pcompany && $useParentDir) {
            $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['dir'])) . DS;
        }
        $path .= $company['Company']['dir'] . DS;
        return $path;
    }
	
	protected function getCompanyDir() {
		$company_name = '';
		$this->loadModel('Company');
        $company = $this->Company->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $this->employee_info['Company']['id'])));
		$company_name = !empty($company) ?  $company['Company']['dir'] : '';
        return $company_name;
    }
}