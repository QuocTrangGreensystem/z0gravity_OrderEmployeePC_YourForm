<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectPhasesController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectPhases';
    public $uses = array('ProjectPhase', 'CompanyConfig');
    //var $layout = 'administrators';

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation');

    /**
     * index
     *
     * @return void
     * @access public
     */
    function index($view = '') {
        $this->loadModels('Profile', 'CompanyConfig');
        $this->ProjectPhase->recursive = 0;
        $companies1 = $this->ProjectPhase->Company->find('list');
        $parent_companies = $this->ProjectPhase->Company->find('list', array('fields' => array('id', 'parent_id')));
        if ($this->employee_info["Employee"]["is_sas"] != 1){
            $companyId = $this->employee_info["Company"]["id"];
        } else{
            $companyId = "";
        }
        $profiles = array();
        $activateProfile = false;
        if ($companyId != "") {
            $this->paginate = array(
                //phan trang
                'conditions' => array('OR' => array(
                        'ProjectPhase.company_id' => $companyId,
                        'parent_id' => $companyId)),
                'fields' => array('name', 'company_id', 'phase_order', 'color', 'tjm', 'add_when_create_project','activated', 'profile_id'),
                'order' => array('company_id ASC', 'phase_order IS NULL ASC', 'phase_order ASC'),
                'limit' => 1000,
            );
            $this->set('companies', $this->ProjectPhase->Company->getTreeList($companyId));
            $profiles = $this->Profile->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $companyId),
                'fields' => array('id', 'name')
            ));
            $activateProfile = isset($this->companyConfigs['activate_profile']) && !empty($this->companyConfigs['activate_profile']) ?  true : false;
        } else {
            $this->paginate = array(
                //phan trang
                'fields' => array('name', 'company_id', 'phase_order', 'color', 'tjm', 'add_when_create_project','activated', 'profile_id'),
                'order' => array('company_id ASC', 'phase_order IS NULL ASC', 'phase_order ASC'),
                'limit' => 1000,
            );
            $this->set('companies', $this->ProjectPhase->Company->generateTreeList(null, null, null, '--'));
        }
        $projectPhases = $this->paginate();
        if(!empty($projectPhases)){
            $i = 1;
            foreach($projectPhases as $key => $projectPhase){
                $this->ProjectPhase->id = $projectPhase['ProjectPhase']['id'];
                $this->ProjectPhase->save(array('phase_order' => $i));
                $projectPhases[$key]['ProjectPhase']['phase_order'] = $i;
                $i++;
            }
        }
		// debug($projectPhases);
		// exit;
        $this->set(compact('companyId', 'projectPhases', 'companies1', 'parent_companies', 'profiles', 'activateProfile', 'view'));
    }

    /**
     * get_order_up
     * Get order up
     *
     * @return void
     * @access public
     */
    function get_order_up() {
        $this->layout = 'ajax';
        $this->autoRender = false;

		foreach ($this->data as $id => $value_order) {
            if (!empty($id) && !empty($value_order) && $value_order!=0) {
                $this->ProjectPhase->id = $id;
                $this->ProjectPhase->save(array(
                    'phase_order' => intval($value_order)), array('validate' => false, 'callbacks' => false));
            }
        }
        die;
    }

    /**
     * get_order_down
     * Get order down
     *
     * @return void
     * @access public
     */
    function get_order_down() {
        $this->layout = 'ajax';
        $this->autoRender = false;
        $id_up = $_POST['phase_id_up'];
        $comapny_id_up = $_POST['company_id_up'];
        $phase_order_up = $_POST['order_phase_up'];

        $id_down = $_POST['phase_id_down'];
        $comapny_id_down = $_POST['company_id_down'];
        $phase_order_down = $_POST['order_phase_down'];

        if ($phase_order_down == null || $phase_order_down == '')
            $phase_order_down = 2;
        if ($phase_order_up == null || $phase_order_up == '')
            $phase_order_up = 1;

        if ($phase_order_down == $phase_order_up) {
            $phase_order_down++;
            $phase_order_up--;
        }

        $this->ProjectPhase->id = $id_up;
        $this->ProjectPhase->saveField('phase_order', $phase_order_down);
        echo $phase_order_down . "|";

        $this->ProjectPhase->id = $id_down;
        $this->ProjectPhase->saveField('phase_order', $phase_order_up);
        echo $phase_order_up;
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project phase', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('projectPhase', $this->ProjectPhase->read(null, $id));
    }

    /**
     * edit
     *
     * @return void
     * @access public
     */
    function update($id = null) {
		$result = false;
		$this->layout = false;
		// ob_clean();
		// debug($this->data);
		// exit;
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid project phase', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if (empty($this->data["id"])) {
                $this->ProjectPhase->recursive = -1;
                $last_order = $this->ProjectPhase->find("first", array(
                    "fields" => array("(Max(ProjectPhase.phase_order)+1) phase_last_order"),
                    "conditions" => array("ProjectPhase.company_id" => $this->data["company_id"])));
                $this->data["phase_order"] = $last_order[0]["phase_last_order"];
            }
            if($this->_checkDuplicate($this->data, 'ProjectPhase', 'name')){
                if ($this->ProjectPhase->save($this->data)) {
					$this->data = $this->ProjectPhase->read(null, $id);
					$this->data = $this->data['ProjectPhase'];
					$result = true;
                    $this->Session->setFlash(__('Saved', true),'success');
                } else {
                    $this->Session->setFlash(__('NOT SAVED', true), 'error');
                }
            } else {
                $this->Session->setFlash(__('Already exist', true), 'error');
            }
            // $this->redirect(array('action' => 'index'));
        }
        if (empty($this->data)) {
            $this->data = $this->ProjectPhase->read(null, $id);
        }
		$this->set(compact('result'));
    }

    /**
     *  Kiem tra phase da co su dung
     *  @return boolean
     *  @access private
     */

    private function _phaseIsUsing($id = null){
        $this->loadModel('ProjectPhasePlan');
        $this->loadModel('Project');
        $this->loadModel('ProjectAmr');
        // Project Phase Plan
        $checkPhasePlans = $this->ProjectPhasePlan->find('count', array(
                'recursive' => -1,
                'conditions' => array('ProjectPhasePlan.project_planed_phase_id' => $id)
            ));
        // Project
        $checkPhaseProjects =  $this->Project->find('count', array(
                'recursive' => -1,
                'conditions' => array('Project.project_phase_id' => $id)
            ));
        // Project KPI
        $checkPhaseProjectAmrs =  $this->ProjectAmr->find('count', array(
                'recursive' => -1,
                'conditions' => array('ProjectAmr.project_phases_id' => $id)
            ));
        $allowDeleteProjectPhase = 'true';
        if($checkPhasePlans != 0 || $checkPhaseProjects != 0 || $checkPhaseProjectAmrs != 0){
            $allowDeleteProjectPhase = 'false';
        }

        return $allowDeleteProjectPhase;
    }

    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for project phase', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
		$check = ($this->is_sas || $this->_isBelongToCompany($id, 'ProjectPhase'));
        $allowDeleteProjectPhase = $this->_phaseIsUsing($id);
        // neu allow delete project phase la true thi cho xoa
        if($check && ($allowDeleteProjectPhase == 'true')){
            if ($this->ProjectPhase->delete($id)){
                $this->Session->setFlash(__('Deleted', true), 'success');
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $this->Session->setFlash(__('Not deleted', true), 'error');
            $this->redirect(array('action' => 'index'));
        }

        $this->Session->setFlash(__('Not deleted', true), 'error');
        $this->redirect(array('action' => 'index'));
    }

    /**
     * Kiem tra cac project lien quan de cac phase da bi xoa khoi he thong
     *
     *
     */

    public function checkProjectHavePhaseDelete(){
        //$PhaseId = range(110, 127);
        $PhaseId = array(
            '110' => 'Build LOT1',
            '111' => 'Build LOT2',
            '112' => 'Build LOT3',
            '113' => 'MEP LOT1',
            '114' => 'MEP LOT2',
            '115' => 'MEP LOT3',
            '116' => 'Intégration LOT1',
            '117' => 'Intégration LOT2',
            '118' => 'Intégration LOT3',
            '119' => 'Recette LOT1',
            '120' => 'Recette LOT2',
            '121' => 'Recette LOT3',
            '122' => 'MEP LOT1',
            '123' => 'MEP LOT2',
            '124' => 'MEP LOT3',
            '125' => 'Déploiement LOT1',
            '126' => 'Déploiement LOT2',
            '127' => 'Déploiement LOT3'
        );
        $this->loadModel('ProjectPhasePlan');
        $this->loadModel('Project');
        $this->loadModel('ProjectAmr');
        $checkPhasePlans = $checkPhaseProjects = $checkPhaseProjectAmrs = array();
        // Project Phase Plan
        $checkPhasePlans = $this->ProjectPhasePlan->find('all', array(
                'recursive' => -1,
                'conditions' => array('ProjectPhasePlan.project_planed_phase_id' => array_keys($PhaseId)),
                'fields' => array('id', 'project_id', 'project_planed_phase_id'),
                'order' => array('project_id' => 'asc')
            ));
        // Project
        $checkPhaseProjects =  $this->Project->find('all', array(
                'recursive' => -1,
                'conditions' => array('Project.project_phase_id' => array_keys($PhaseId)),
                'fields' => array('id', 'project_phase_id')
            ));
        // Project KPI
        $checkPhaseProjectAmrs =  $this->ProjectAmr->find('all', array(
                'recursive' => -1,
                'conditions' => array('ProjectAmr.project_phases_id' => array_keys($PhaseId)),
                'fields' => array('id', 'project_id', 'project_phases_id')
            ));
        $pIdPhasePlans = !empty($checkPhasePlans) ? Set::classicExtract($checkPhasePlans, '{n}.ProjectPhasePlan.project_id') : array();
        $pIdProject = !empty($checkPhaseProjects) ? Set::classicExtract($checkPhaseProjects, '{n}.Project.id') : array();
        $pIdProjectAmrs = !empty($checkPhaseProjectAmrs) ? Set::classicExtract($checkPhaseProjectAmrs, '{n}.ProjectAmr.project_id') : array();

        $groupProjectIds = array_merge($pIdPhasePlans, $pIdProject, $pIdProjectAmrs);
        $groupProjectIds = !empty($groupProjectIds) ? array_unique($groupProjectIds) : array();
        echo 'Total: <span style="font-weight: bold;">' . count($groupProjectIds) . '</span> project id<br />';
        echo '<b>List Project Id:</b><br />';
        echo 'No ---------------> Project Id<br />';
        if(!empty($groupProjectIds)){
            $i = 0;
            foreach($groupProjectIds as $key => $groupProjectId){
                $i++;
                echo $i. '. &nbsp;&nbsp;&nbsp;--------------> &nbsp;<span style="color: red">'.$groupProjectId . '</span><br />';
            }
        }
        echo '<b>Project Detail:</b><br />';
        if(!empty($checkPhaseProjects)){
            foreach($checkPhaseProjects as $checkPhaseProject){
                echo 'Project Id: <span style="color: red">'. $checkPhaseProject['Project']['id'] . '</span> & Current Phase Id: <span style="color: red">' . $checkPhaseProject['Project']['project_phase_id'] . '</span> & Current Phase Name: <span style="color: red">' . $PhaseId[$checkPhaseProject['Project']['project_phase_id']] . '</span><br />';
            }
        }
        echo '<b>Project KPI:</b><br />';
        if(!empty($checkPhaseProjectAmrs)){
            foreach($checkPhaseProjectAmrs as $checkPhaseProjectAmr){
                echo 'Project Id: <span style="color: red">'. $checkPhaseProjectAmr['ProjectAmr']['project_id'] . '</span> & Current Phase Id: <span style="color: red">' . $checkPhaseProjectAmr['ProjectAmr']['project_phases_id'] . '</span> & Current Phase Name: <span style="color: red">' . $PhaseId[$checkPhaseProjectAmr['ProjectAmr']['project_phases_id']] . '</span><br />';
            }
        }
        echo '<b>Project Phase Plans:</b><br />';
        if(!empty($checkPhasePlans)){
            foreach($checkPhasePlans as $checkPhasePlan){
                echo 'Project Id: <span style="color: red">'. $checkPhasePlan['ProjectPhasePlan']['project_id'] . '</span> &nbsp;&nbsp;&nbsp;&nbsp; & &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Phase Plan Id: <span style="color: red">' . $checkPhasePlan['ProjectPhasePlan']['project_planed_phase_id'] . '</span> &nbsp;&nbsp;&nbsp;&nbsp; & &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Phase Plan Name: <span style="color: red">' . $PhaseId[$checkPhasePlan['ProjectPhasePlan']['project_planed_phase_id']] . '</span><br />';
            }
        }
        //debug($groupProjectIds);
        exit;
    }
      /**
     * Check duplocate
     *
     * @return void
     * @access public
     */
    function check_duplicate($name = null,$company_id = null) {
        $check = $this->ProjectPhase->find('count', array('conditions' => array(
                "company_id" => $company_id,
                "name" => strtolower($name)
                )));
        return !$check;
    }
    public function saveFields(){
        if( !empty($this->data) ){
            $this->store($this->data);
        }
        die(1);
    }
    /*

    */
    public function getFields(){
        $default = array(
            'progress|1',
            'kpi|1',
            //'project_part_id|1',
            'planed_duration|1',
            'predecessor|1',
            'profile_id|1',
            'phase_real_start_date|1',
            'phase_real_end_date|1',
            'project_phase_status_id|1',
            'color|1',
            'ref1|1',
            'ref2|1',
            'ref3|1',
            'ref4|1'
        );
        if( isset($this->companyConfigs['phase_fields']) ){
            $raw = json_decode($this->companyConfigs['phase_fields']);
            return $raw + $default;
        } else {
            //insert
            $this->CompanyConfig->save(array(
                'cf_name' => 'phase_fields',
                'company' => $this->employee_info['Company']['id'],
                'cf_value' => json_encode($default)
            ));
        }
        return $default;
    }
    public function store($data){
        $this->companyConfigs['phase_fields'] = json_encode($data);
        //save
        $str = $this->CompanyConfig->getDatasource()->value(json_encode($data), 'string');
        $this->CompanyConfig->updateAll(array(
            'cf_value' => $str
        ), array(
            'cf_name' => 'phase_fields',
            'company' => $this->employee_info['Company']['id']
        ));
    }
    function fields(){
        $this->set('data', $this->getFields());
        $this->set('company', $this->employee_info['Company']['id']);
        $this->set('names', array(
            'progress' => '% Achieved',
            'kpi' => 'KPI',
            'project_part_id' => 'Part',
            'planed_duration' => 'Duration',
            'predecessor' => 'Predecessor',
            'profile_id' => 'Profile',
            'phase_real_end_date' => 'Real end date',
            'phase_real_start_date' => 'Real start date',
            'project_phase_status_id' => 'Status',
            'ref1' => 'Ref 1',
            'ref2' => 'Ref 2',
            'ref3' => 'Ref 3',
            'ref4' => 'Ref 4'
        ));
    }
	/**
     * Order
     *
     * @return void
     * @access public
     */
    public function order($company_id = null) {
        $this->layout = false;
        if (!empty($this->data) && $this->_getCompany($company_id)) {
            foreach ($this->data as $id => $weight) {
                $last = $this->ProjectPhase->find('first', array(
                    'recursive' => -1, 'fields' => array('id'),
                    'conditions' => array('ProjectPhase.id' => $id, 'company_id' => $company_id)));
                if ($last && !empty($weight)) {
                    $this->ProjectPhase->id = $last['ProjectPhase']['id'];
                    $this->ProjectPhase->save(array(
                        'phase_order' => intval($weight)), array('validate' => false, 'callbacks' => false));
                }
            }
        }
        exit(0);
    }
}
?>
