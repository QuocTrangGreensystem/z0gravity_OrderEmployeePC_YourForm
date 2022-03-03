<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ActivitiesController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'Activities';

    /**
     * Components
     *
     * @var array
     * @access public
     */
    var $components = array('MultiFileUpload', 'SlickExporter');

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->autoRedirect = false;
    }

    /**
     * Index
     *
     * @return void
     * @access public
     */
    function index() {
        ini_set('memory_limit', '512M');
        //ADD CODE BY VINGUYEN 07/05/2014
        if(isset($_POST['multiSort']))
        {
            $fieldSort=array();
            if($_POST['actSort']=='add')
            {
                if($_POST['flag']!=0)
                {
                    $fieldSort=$this->Session->read('sFieldSort');
                }
                else
                {
                    $fieldSort=array();
                    $this->Session->write('sFieldSort','');
                }
                $fieldSort[]=array('columnId'=>$_POST['value'],'sortAsc'=>1);
                $this->Session->write('sFieldSort',$fieldSort);
                echo json_encode($fieldSort);
                exit();
            }
            if($_POST['actSort']=='remove')
            {
                $fieldSort=$this->Session->read('sFieldSort');
                $fieldSort1=array();
                foreach($fieldSort as $array)
                {
                    if($array['columnId']!=$_POST['value'])
                    $fieldSort1[]=array('columnId'=>$array['columnId'],'sortAsc'=>$array['sortAsc']);
                }
                $this->Session->write('sFieldSort',$fieldSort1);
                echo json_encode($fieldSort1);
                exit();
            }
            if($_POST['actSort']=='update')
            {
                $fieldSort=$this->Session->read('sFieldSort');
                $count=count($fieldSort);
                for($i=0;$i<$count;$i++)
                {
                    if($fieldSort[$i]['columnId']==$_POST['value'])
                        $fieldSort[$i]['sortAsc']=round($_POST['type']);
                }
                $this->Session->write('sFieldSort',$fieldSort);
                echo json_encode($fieldSort);
                exit();
            }
        }
        //END ADD

        if (!($employeeName = $this->_getEmpoyee())) {
            $this->Session->setFlash(__('Your account do not have a company to management activity.', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }

        $this->Activity->cacheQueries = true;
        $this->Activity->Behaviors->attach('Containable');

        // filter follow category
        extract(array_merge(array(
                'activities_activated' => null),
                $this->params['url']));
        if(!empty($activities_activated)){ // not empty cate in the url
            $this->Session->write("App.activities_activated", $activities_activated);
        }else{ // empty cate in url
            $activities_activated = $this->Session->read("App.activities_activated");
            if($activities_activated){
                // do nothing
                $this->Session->write("App.activities_activated", $activities_activated);
            }else{
                $activities_activated = 1;
                $this->Session->write("App.activities_activated", 1);
            }

        }
        // set cate
        $this->set('activities_activated', $this->Session->read("App.activities_activated"));
        $activated = ($activities_activated == 1) ? 1 : (($activities_activated == 2) ? 0 : array(0, 1));
        if($activities_activated == 4){
            $activated = 2;
        }
        $activitiesAll = $this->Activity->find("all", array(
            //@Huupc Change filter fields using display
            'fields' => array(
                'id',
                'name',
                'long_name',
                'short_name',
                'family_id',
                'subfamily_id',
                'budget_customer_id',
                'project_manager_id',
                'pms',
                'project',
                'activated',
                'code1',
                'code2',
                'code3',
                'c44',
                'start_date',
                'end_date',
                'actif',
                'import_code',
                'code4',
                'code5',
                'code6',
                'code7',
                'code8',
                'code9',
                'code10'
            ),
            "conditions" => array('Activity.company_id' => $employeeName['company_id'], 'activated' => $activated),
            'recursive' => -1,
            ));
        /**
         * Count activiy lay ra, neu lon hon 2k thi kiem tra:
         * Neu admin thi nhay den trang archive.
         * Neu khac admin nhay lai trang va mac dinh che do activated.
         */
        if(count($activitiesAll) > 4000){
            if($this->employee_info['Role']['name'] == 'admin'){
                $this->Session->setFlash(__('More than 4000 activites, please archive activities', true), 'success');
                $this->redirect(array('controller' => 'staffing_systems', 'action' => 'archived'));
            } else {
                $this->Session->setFlash(__('Data to large, Please contact administrator.', true), 'success');
                $this->redirect(array('controller' => 'activities', 'action' => 'index', $employeeName['company_id'], '?' => array('activities_activated' => 1)));
            }
        }
        //edit by Thach : tang toc do load
        $activityProfitRefer = $this->Activity->AccessibleProfit->find('all', array(
            'fields' => array(
                    'id',
                    'profit_center_id',
                    'activity_id'
            ),
            'conditions' => array('type' => 0)
        ));
        $activityProfitRefer = Set::combine($activityProfitRefer,'{n}.AccessibleProfit.id','{n}.AccessibleProfit','{n}.AccessibleProfit.activity_id');
        $linkedProfitRefer = $this->Activity->LinkedProfit->find('all', array(
            'fields' => array(
                    'id',
                    'profit_center_id',
                    'activity_id'
            ),
            'conditions' => array('type' => 1)
        ));
        $linkedProfitRefer = Set::combine($linkedProfitRefer,'{n}.LinkedProfit.activity_id','{n}.LinkedProfit');
        $this->loadModel('ProjectEmployeeManager');
        $listActivityIds = !empty($activitiesAll) ? Set::classicExtract($activitiesAll, '{n}.Activity.id') : array();
        $listManger = $this->ProjectEmployeeManager->find('all', array(
            'recursive' => -1,
            'fields' => array(
                    'id',
                    'project_manager_id',
                    'is_backup',
                    'activity_id'
            ),
            'conditions' => array('activity_id' => $listActivityIds, 'is_profit_center' => 0)
        ));
        $listManger = !empty($listManger) ? Set::combine($listManger,'{n}.ProjectEmployeeManager.id','{n}.ProjectEmployeeManager','{n}.ProjectEmployeeManager.activity_id') : array();
        $activities = array();
        foreach($activitiesAll as $key_activities => $value_activities){
            //add AccessibleProfit neu co
            $AccessibleProfit = array();
            if(!empty($activityProfitRefer[$value_activities['Activity']['id']])){
                $_activityProfitRefer = array();
                foreach($activityProfitRefer[$value_activities['Activity']['id']] as $_key => $_value){
                    $_activityProfitRefer[] = $_value;
                }
                $value_activities['AccessibleProfit'] =$_activityProfitRefer;
            }else{
                $value_activities['AccessibleProfit'] = array();
            }
            //add LinkedProfit neu co
            if(!empty($linkedProfitRefer[$value_activities['Activity']['id']])){
                $_linkedProfitRefer = array();
                $value_activities['LinkedProfit'] = $linkedProfitRefer[$value_activities['Activity']['id']];
            }else{
                $value_activities['LinkedProfit'] = array();
            }
            $primaryManager = !empty($value_activities['Activity']['project_manager_id']) ? $value_activities['Activity']['project_manager_id'] : '';
            unset($value_activities['Activity']['project_manager_id']);
            //add manager neu co
            if(!empty($listManger[$value_activities['Activity']['id']])){
                $_manager = array();
                foreach($listManger[$value_activities['Activity']['id']] as $_key => $_value){
                    $_manager[] = $_value;
                }
                $value_activities['project_manager_id'] =$_manager;
            }else{
                $value_activities['project_manager_id'] = array();
            }
            if(!empty($primaryManager)){
                $value_activities['project_manager_id'][] = array(
                    'project_manager_id' => $primaryManager,
                    'is_backup' => 0
                );
            }
            $activities[] = $value_activities;
        }
        $activityColumn = ClassRegistry::init('ActivityColumn')->getOptions($employeeName['company_id']);

        $profitCenters = ClassRegistry::init('ProfitCenter')->generateTreeList(array(
            'company_id' => $employeeName['company_id']), null, null, '--');
        $projects = ClassRegistry::init('Project')->find('list', array('fields' => array('project_name')));

        $mapFamilies = Set::combine($this->Activity->Family->find('all', array(
                            'recursive' => -1,
                            'conditions' => array('company_id' => $employeeName['company_id']))), '{n}.Family.id', '{n}.Family');

        $families = $subfamilies = array();
        foreach ($mapFamilies as $mapFamilie) {
            if ($mapFamilie['parent_id']) {
                $subfamilies[$mapFamilie['id']] = $mapFamilie['name'];
            } else {
                $families[$mapFamilie['id']] = $mapFamilie['name'];
            }
        }
        /**
         * Get list budget customer
         */
        $this->loadModel('BudgetCustomer');
        $budgetCustomers = $this->BudgetCustomer->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $employeeName['company_id']),
            'fields' => array('id', 'name')
        ));
        /**
         * Get list project manager
         */
        $this->loadModel('Employee');
        $companyEmployees = $this->Employee->CompanyEmployeeReference->find('list', array(
            'recursive' => -1,
            'fields' => array('employee_id', 'role_id'),
            'conditions' => array('CompanyEmployeeReference.company_id' => $employeeName['company_id'])));

        $projectEmployees = $this->Employee->find('all', array(
            'order' => 'last_name  ASC',
            'recursive' => -1,
            'conditions' => array(
                'id' => array_keys($companyEmployees)
            ),
            'fields' => array('first_name', 'last_name', 'id')));

        $projectManagers = array();
        foreach ($projectEmployees as $projectEmployee) {
            $projectManagers['project'][$projectEmployee['Employee']['id']] = $projectEmployee['Employee']['first_name'] . ' ' . $projectEmployee['Employee']['last_name'];
            foreach (array('pm' => array(3,2), 'tech' =>array(3,5)) as $key => $role) {
                if (in_array($companyEmployees[$projectEmployee['Employee']['id']], $role)) {
                    $projectManagers[$key][$projectEmployee['Employee']['id']] = $projectManagers['project'][$projectEmployee['Employee']['id']];
                }
            }
        }
        $projectManagers = !empty($projectManagers['pm']) ? $projectManagers['pm'] : array();

        $this->set('projectManagers', $projectManagers);
        /*
        * Default project screen
         */
        $screenDefaults = ClassRegistry::init('Menu')->find('first', array(
            'recursive' => -1,
            'conditions' => array('model' => 'project', 'company_id' => $employeeName['company_id'], 'default_screen' => 1)
        ));
        $defaultProjectScreen = array(
            'controller' => 'projects',
            'action' => 'edit'
        );
        if( !empty($screenDefaults) ){
            $defaultProjectScreen['controller'] = $screenDefaults['Menu']['controllers'];
            $defaultProjectScreen['action'] = $screenDefaults['Menu']['functions'];
        }
        $this->set('defaultProjectScreen', $defaultProjectScreen);
        //$this->_parse();
        $this->_parseParams();
        $this->set(compact('activities', 'employeeName', 'activityColumn', 'profitCenters', 'families', 'subfamilies', 'mapFamilies', 'projects', 'budgetCustomers'));
    }

    public function manage() {
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        $this->loadModels('ActivityColumn');
        /**
         * Luu 1 cai session de kiem tra dang o fuction review or manager
         */
        $this->Session->write('ActedActiFunc', 'manage');
        if(isset($_POST['multiSort'])){
            $fieldSort=array();
            if($_POST['actSort']=='add'){
                if($_POST['flag']!=0){
                    $fieldSort=$this->Session->read('sFieldSort');
                } else {
                    $fieldSort=array();
                    $this->Session->write('sFieldSort','');
                }
                $fieldSort[]=array('columnId'=>$_POST['value'],'sortAsc'=>1);
                $this->Session->write('sFieldSort',$fieldSort);
                echo json_encode($fieldSort);
                exit();
            }
            if($_POST['actSort']=='remove'){
                $fieldSort=$this->Session->read('sFieldSort');
                $fieldSort1=array();
                foreach($fieldSort as $array){
                    if($array['columnId']!=$_POST['value'])
                    $fieldSort1[]=array('columnId'=>$array['columnId'],'sortAsc'=>$array['sortAsc']);
                }
                $this->Session->write('sFieldSort',$fieldSort1);
                echo json_encode($fieldSort1);
                exit();
            }
            if($_POST['actSort']=='update'){
                $fieldSort=$this->Session->read('sFieldSort');
                $count=count($fieldSort);
                for($i=0;$i<$count;$i++){
                    if($fieldSort[$i]['columnId']==$_POST['value'])
                        $fieldSort[$i]['sortAsc']=round($_POST['type']);
                }
                $this->Session->write('sFieldSort',$fieldSort);
                echo json_encode($fieldSort);
                exit();
            }
        }
        if (!($employeeName = $this->_getEmpoyee())) {
            $this->Session->setFlash(__('Your account do not have a company to management activity.', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }
        /**
         * Xu ly view va screen.
         */
        extract(array_merge(array(
                'actiManage' => null, 'view' => null),
                $this->params['url']));
        if(!empty($actiManage)){
            $this->Session->write("App.actiManage", $actiManage);
        } else {
            $actiManage = $this->Session->read("App.actiManage");
            if($actiManage){
                $this->Session->write("App.actiManage", $actiManage);
            }else{
                $actiManage = 1;
                $this->Session->write("App.actiManage", 1);
            }

        }
        if(!empty($view)){
            $this->Session->write("App.view", $view);
        } else {
            $view = $this->Session->read("App.view");
            if($view){
                $this->Session->write("App.view", $view);
            } else {
                $view = -1;
                $this->Session->write("App.view", -1);
            }
        }
        $activated = ($actiManage == 1) ? 1 : (($actiManage == 2) ? 0 : array(0, 1));
        if($actiManage == 4){
            $activated = 2;
        }
        $conditions = array('company_id' => $employeeName['company_id'], 'activated' => $activated);
        /**
         * Lay combobox cua personalized view activity
         */
        $personalizeList = $this->getPersonalizedViews($actiManage, true, $view);
        /**
         * Lay personalized view cua activity
         */
        if($view == -1 || $view == 0){
            $defaultView = ClassRegistry::init('UserDefaultView')->find('first', array(
                'conditions' => array(
                    'employee_id' => $employeeName['id'],
                    'model'=>'activity'
                ),
                'recursive' => -1,
                'fields' => array('user_view_id'))
            );
            if ($defaultView && $defaultView['UserDefaultView']['user_view_id'] != 0) {
                $checkStatus = -2;
                $viewId = $defaultView = $defaultView['UserDefaultView']['user_view_id'];
            }
            else{
                $defaultView = 0;
            }
        } else if($view==-2) {
            $defaultView = 0;
        } else{
            $defaultView = $view;
        }
        /**
         * Lay column default cua activity
         */
        if ($defaultView && $defaultView != 0) {
            $checkStatus = -2;
            $activityColumn = ClassRegistry::init('UserView')->find('first', array(
                'fields' => array('UserView.content'),
                'conditions' => array('UserView.id' => $defaultView)));
            if (!empty($activityColumn)) {
                $activityColumn = unserialize($activityColumn['UserView']['content']);
            }
            $this->Session->write("App.PersonalizedDefault", true);
        } else {
            $checkStatus = -1;
            $activityColumn = array(
                'name' => 'name',
                'short_name' => 'short_name',
                'family_id' => 'family_id'
            );
        }
        $activityColumn = $this->ActivityColumn->parseViewField($employeeName['company_id'], $activityColumn);
        $cacheColumn = !empty($activityColumn) ? Set::combine($activityColumn, '{s}.code', '{s}') : array();
        $activityColumn = $this->_sortColumnActivity($activityColumn, 1, $cacheColumn, array());
        /**
         * Lay all column trong activity column
         */
        $allActivityColumn = $this->ActivityColumn->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $employeeName['company_id']),
            'fields' => array('id', 'key')
        ));
        $allActivityColumn = $this->ActivityColumn->parseViewField($employeeName['company_id'], $allActivityColumn);
        $cacheColumn = !empty($allActivityColumn) ? Set::combine($allActivityColumn, '{s}.code', '{s}') : array();
        $allActivityColumn = $this->_sortColumnActivity($allActivityColumn, 1, $cacheColumn, array());
        /**
         * Lay tat ca cac activity theo dieu kien
         */
        $activities = $this->Activity->find("all", array(
            'recursive' => -1,
            "conditions" => $conditions
        ));
        $this->loadModels('BudgetCustomer', 'ProfitCenter', 'Project', 'Employee', 'ProjectEmployeeManager');
        /**
         * Lay ten cac project linked voi activity
         */
        $actiIds = !empty($activities) ? Set::classicExtract($activities, '{n}.Activity.id') : '';
        $projects = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array('activity_id' => $actiIds),
            'fields' => array('id', 'project_name')
        ));
        /**
         * Lay list profit center cua cong ty
         */
        $profitCenters = $this->ProfitCenter->generateTreeList(array(
            'ProfitCenter.company_id' => $employeeName['company_id']), null, null, '--');
        /**
         * Lay list family va sub-family cua cong ty
         */
        $mapFamilies = Set::combine($this->Activity->Family->find('all', array(
                            'recursive' => -1,
                            'conditions' => array('company_id' => $employeeName['company_id']))), '{n}.Family.id', '{n}.Family');
        $families = $subfamilies = array();
        foreach ($mapFamilies as $mapFamilie) {
            if ($mapFamilie['parent_id']) {
                $subfamilies[$mapFamilie['id']] = $mapFamilie['name'];
            } else {
                $families[$mapFamilie['id']] = $mapFamilie['name'];
            }
        }
        /**
         * Lay list budget customer cua cong ty
         */
        $budgetCustomers = $this->BudgetCustomer->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $employeeName['company_id']),
            'fields' => array('id', 'name')
        ));
        /**
         * Lay list project manager cua cong ty
         */
        $companyEmployees = $this->Employee->CompanyEmployeeReference->find('list', array(
            'recursive' => -1,
            'fields' => array('employee_id', 'role_id'),
            'conditions' => array('CompanyEmployeeReference.company_id' => $employeeName['company_id'])));

        $projectEmployees = $this->Employee->find('all', array(
            'order' => 'last_name  ASC',
            'recursive' => -1,
            'conditions' => array(
                'id' => array_keys($companyEmployees)
            ),
            'fields' => array('first_name', 'last_name', 'id')));

        $projectManagers = array();
        foreach ($projectEmployees as $projectEmployee) {
            $projectManagers['project'][$projectEmployee['Employee']['id']] = $projectEmployee['Employee']['first_name'] . ' ' . $projectEmployee['Employee']['last_name'];
            foreach (array('pm' => array(3,2), 'tech' =>array(3,5)) as $key => $role) {
                if (in_array($companyEmployees[$projectEmployee['Employee']['id']], $role)) {
                    $projectManagers[$key][$projectEmployee['Employee']['id']] = $projectManagers['project'][$projectEmployee['Employee']['id']];
                }
            }
        }
        $projectManagers = !empty($projectManagers['pm']) ? $projectManagers['pm'] : array();
        /**
         * Accessible Par && Linked Profit
         */
        $activityProfitRefer = $this->Activity->AccessibleProfit->find('all', array(
            'fields' => array(
                    'id',
                    'profit_center_id',
                    'activity_id'
            ),
            'conditions' => array('type' => 0, 'activity_id' => $actiIds)
        ));
        $activityProfitRefer = Set::combine($activityProfitRefer,'{n}.AccessibleProfit.id','{n}.AccessibleProfit.profit_center_id','{n}.AccessibleProfit.activity_id');
        $linkedProfitRefer = $this->Activity->LinkedProfit->find('all', array(
            'fields' => array(
                    'id',
                    'profit_center_id',
                    'activity_id'
            ),
            'conditions' => array('type' => 1, 'activity_id' => $actiIds)
        ));
        $linkedProfitRefer = Set::combine($linkedProfitRefer,'{n}.LinkedProfit.activity_id','{n}.LinkedProfit.profit_center_id');
        /**
         * Project manager
         */
        $listManger = $this->ProjectEmployeeManager->find('all', array(
            'recursive' => -1,
            'fields' => array(
                    'id',
                    'project_manager_id',
                    'is_backup',
                    'activity_id'
            ),
            'conditions' => array('activity_id' => $actiIds, 'is_profit_center' => 0)
        ));
        $listManger = !empty($listManger) ? Set::combine($listManger,'{n}.ProjectEmployeeManager.project_manager_id','{n}.ProjectEmployeeManager.is_backup','{n}.ProjectEmployeeManager.activity_id') : array();

        /*
        * List manual consumed
        */
        $this->loadModel('ActivityTask');
        $this->ActivityTask->virtualFields['manual'] = 'SUM(manual_consumed)';
        $tasks = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'activity_id' => $actiIds
            ),
            'fields' => array(
                'activity_id',
                'manual'
            ),
            'group' => array('activity_id')
        ));
        $this->set('manualData', $tasks);
        /*
        * Default project screen
         */
        $screenDefaults = ClassRegistry::init('Menu')->find('first', array(
            'recursive' => -1,
            'conditions' => array('model' => 'project', 'company_id' => $employeeName['company_id'], 'default_screen' => 1)
        ));
        $defaultProjectScreen = array(
            'controller' => 'projects',
            'action' => 'edit'
        );
        if( !empty($screenDefaults) ){
            $defaultProjectScreen['controller'] = $screenDefaults['Menu']['controllers'];
            $defaultProjectScreen['action'] = $screenDefaults['Menu']['functions'];
        }
        $this->set('defaultProjectScreen', $defaultProjectScreen);
        $this->_parseParams();
        // get history
        $this->loadModels('HistoryFilter');
        $history = $this->HistoryFilter->find('first', array(
            'conditions' => array(
                'path' => $this->params['url']['url'],
                'employee_id' => $employeeName['id']
            ),
            'fields' => array('params')
        ));
        if( !empty($history) ){
            $history = json_decode($history['HistoryFilter']['params'], true);
        } else {
            $history = array();
        }
        $this->set(compact('history', 'listManger', 'activityProfitRefer', 'linkedProfitRefer', 'actiManage','employeeName', 'activityColumn', 'profitCenters', 'families', 'subfamilies', 'mapFamilies', 'projects', 'budgetCustomers', 'projectManagers', 'personalizeList', 'activities', 'allActivityColumn'));
    }

    public function handleDataOfActivity(){
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        $this->loadModels('ActivityTask', 'ProjectTask', 'ActivityRequest', 'ProjectBudgetSyn', 'TmpStaffingSystem', 'Activity', 'ActivityColumn');
        $this->layout = false;
        $datas = array();
        if($_POST){
            $activityIds = !empty($_POST['activityIds']) ? json_decode($_POST['activityIds']) : '';
            $employeeName = !empty($_POST['employeeName']) ? json_decode($_POST['employeeName']) : '';
            $activityColumn = !empty($_POST['activityColumn']) ? json_decode($_POST['activityColumn'], true) : '';
            $map = !empty($_POST['map']) ? json_decode($_POST['map'], true) : '';
            $activityTasks = $this->ActivityTask->find('all', array(
                'recursive' => -1,
                'conditions' => array('activity_id' => $activityIds),
                'fields' => array('id', 'parent_id', 'activity_id', 'estimated', 'overload', 'project_task_id', 'special', 'special_consumed')
            ));
            $pTaskIds = !empty($activityTasks) ? Set::classicExtract($activityTasks, '{n}.ActivityTask.project_task_id') : array();
            $aTaskIds = !empty($activityTasks) ? Set::classicExtract($activityTasks, '{n}.ActivityTask.id') : array();
            $activityOfTasks = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.id', '{n}.ActivityTask.activity_id') : array();
            $parentIds = !empty($activityTasks) ? array_unique(Set::classicExtract($activityTasks, '{n}.parent_id')) : array();
            $activityTasks = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.id', '{n}.ActivityTask') : array();
            /**
             * Lay consumed cua cac activity
             */
            $consumedOfActivities = $this->ActivityRequest->find(
                'all',
                array(
                    'recursive' => -1,
                    'fields' => array(
                        'activity_id',
                        'task_id',
                        'SUM(value) as value'
                    ),
                'group' => array('activity_id', 'task_id'),
                'conditions' => array(
                    'status' => 2,
                    'company_id' => $employeeName->company_id,
                    'NOT' => array('value' => 0),
                    'OR' => array(
                        'task_id' => $aTaskIds,
                        'activity_id' => $activityIds
                    )

                )
            ));
            $consumedOfActivities = !empty($consumedOfActivities) ? Set::combine($consumedOfActivities, '{n}.ActivityRequest.task_id', '{n}.0.value', '{n}.ActivityRequest.activity_id') : array();
            $sumPrevious = $sumActivities = $consumedOfTasks = array();
            if(!empty($consumedOfActivities)){
                $consumedOfTasks = !empty($consumedOfActivities[0]) ? $consumedOfActivities[0] : array();
                unset($consumedOfActivities[0]);
                foreach($consumedOfActivities as $activityID => $value){
                    $val = array_shift($value);
                    $sumActivities[$activityID] = $val;
                    $sumPrevious[$activityID] = $val;
                }
                if(!empty($consumedOfTasks)){
                    foreach($consumedOfTasks as $taskId => $value){
                        $activityID = !empty($activityOfTasks[$taskId]) ? $activityOfTasks[$taskId] : 0;
                        if(!isset($sumActivities[$activityID])){
                            $sumActivities[$activityID] = 0;
                        }
                        $sumActivities[$activityID] += $value;
                    }
                }
            }
            /**
             * Tinh consumed cua employee theo activity
             */
            $consumedActivityOfEmployees = $this->ActivityRequest->find(
                'all',
                array(
                    'recursive' => -1,
                    'fields' => array(
                        'id',
                        'employee_id',
                        'activity_id',
                        'task_id',
                        'SUM(value) as value'
                    ),
                'group' => array('employee_id', 'activity_id', 'task_id'),
                'conditions' => array(
                    'status' => 2,
                    'OR' => array(
                        'task_id' => $aTaskIds,
                        'activity_id' => $activityIds
                    ),
                    'company_id' => $employeeName->company_id,
                    'NOT' => array('value' => 0))
                )
            );
            foreach ($consumedActivityOfEmployees as $consumedActivityOfEmployee) {
                $dx = $consumedActivityOfEmployee['ActivityRequest'];
                $value = $consumedActivityOfEmployee['0']['value'];
                $activityID = $dx['activity_id'];
                if(!empty($dx['task_id'])){
                    $activityID = !empty($activityOfTasks[$dx['task_id']]) ? $activityOfTasks[$dx['task_id']] : 0;
                }
                if (!isset($sumEmployees[$activityID][$dx['employee_id']])) {
                    $sumEmployees[$activityID][$dx['employee_id']] = 0;
                }
                $sumEmployees[$activityID][$dx['employee_id']] += $value;
                $employees[$dx['employee_id']] = $dx['employee_id'];
            }
            $employees = Set::combine($this->ActivityRequest->Employee->find('all', array(
                                'fields' => array('id', 'tjm', 'actif'), 'recursive' => -1,
                                )), '{n}.Employee.id', '{n}.Employee');
            /**
             * Lay cac project task thuoc activity task
             */
            $projectTasks = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array('ProjectTask.id' => $pTaskIds),
                'fields' => array('id', 'parent_id', 'project_id', 'estimated', 'overload', 'special', 'special_consumed')
            ));
            $parentProTaskIds = !empty($projectTasks) ? array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.parent_id')) : array();
            $projectTasks = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask') : array();
            /**
             * Tinh remain, remain special,.... theo tung task cua project
             */
            $sumWorload = $sumOverload = $sumRemains = $sumRemainSpecials = array();
            foreach($activityTasks as $taskId => $activityTask){
                if(in_array($taskId, $parentIds)){
                    unset($activityTasks[$taskId]);
                } else {
                    $activityId = $activityTask['activity_id'];
                    $dataEstimated = $activityTask['estimated'];
                    $dataOverload = $activityTask['overload'];
                    /**
                     * Project task
                     */
                    $hasSpecialPTask = false;
                    $consumedForTasks = 0;
                    if(!empty($activityTask['project_task_id'])){
                        $PTaskId = $activityTask['project_task_id'];
                        if(in_array($PTaskId, $parentProTaskIds)){
                            // do nothing
                        } else {
                            $dataEstimated = !empty($projectTasks[$PTaskId]['estimated']) ? $projectTasks[$PTaskId]['estimated'] : 0;
                            $dataOverload = !empty($projectTasks[$PTaskId]['overload']) ? $projectTasks[$PTaskId]['overload'] : 0;
                            $hasSpecialPTask = true;
                            if(!empty($projectTasks[$PTaskId]['special']) && $projectTasks[$PTaskId]['special'] == 1){
                                $consumedForTasks = !empty($projectTasks[$PTaskId]['special_consumed']) ? $projectTasks[$PTaskId]['special_consumed'] : 0;
                                if (!isset($sumRemainSpecials[$activityId])) {
                                    $sumRemainSpecials[$activityId] = 0;
                                }
                                $sumRemainSpecials[$activityId] += $dataEstimated - $consumedForTasks;
                            } else {
                                $consumedForTasks = !empty($consumedOfTasks[$taskId]) ? $consumedOfTasks[$taskId] : 0;
                            }
                        }
                    }
                    /**
                     * Consumed of activity task
                     */
                    if($hasSpecialPTask == false){
                        if(!empty($activityTask['special']) && $activityTask['special'] == 1){
                            $consumedForTasks = !empty($activityTask['special_consumed']) ? $activityTask['special_consumed'] : 0;
                            if (!isset($sumRemainSpecials[$activityId])) {
                                $sumRemainSpecials[$activityId] = 0;
                            }
                            $sumRemainSpecials[$activityId] += $dataEstimated - $consumedForTasks;
                        } else {
                            $consumedForTasks = !empty($consumedOfTasks[$taskId]) ? $consumedOfTasks[$taskId] : 0;
                        }
                    }
                    /**
                     * Remain cua task thuoc activity task
                     * Remain cua cac task thuoc external budget
                     */
                    if (!isset($sumRemains[$activityId])) {
                        $sumRemains[$activityId] = 0;
                    }
                    $sumRemains[$activityId] += number_format(($dataEstimated + $dataOverload) - $consumedForTasks, 2);
                    /**
                     * Sum Workload
                     */
                    if (!isset($sumWorload[$activityId])) {
                        $sumWorload[$activityId] = 0;
                    }
                    $sumWorload[$activityId] += $dataEstimated;
                    /**
                     * Sum overload
                     */
                    if (!isset($sumOverload[$activityId])) {
                        $sumOverload[$activityId] = 0;
                    }
                    $sumOverload[$activityId] += $dataOverload;
                }
            }
            /**
             * Lay du lieu budget
             */
            $budgets = $this->ProjectBudgetSyn->find('all', array(
                'recursive' => -1,
                'conditions' => array('activity_id' => $activityIds)
            ));
            $budgets = !empty($budgets) ? Set::combine($budgets, '{n}.ProjectBudgetSyn.activity_id', '{n}.ProjectBudgetSyn') : array();
            /**
             * Lay du lieu cho phan assign to
             */
            $dataSystems = array();
            if((isset($activityColumn['assign_to_employee']) && !empty($activityColumn['assign_to_employee'])) || (isset($activityColumn['assign_to_profit_center']) && !empty($activityColumn['assign_to_profit_center']))){
                $dataSystems = $this->TmpStaffingSystem->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'model' => array('employee', 'profit_center'),
                        'NOT' => array(
                            'activity_id' => 0,
                            'model_id' => 999999999
                        ),
                        'company_id' => $employeeName->company_id
                    ),
                    'fields' => array('activity_id', 'model', 'SUM(estimated) as value'),
                    'group' => array('activity_id', 'model')
                ));
                $dataSystems = !empty($dataSystems) ? Set::combine($dataSystems, '{n}.TmpStaffingSystem.activity_id', '{n}.0.value', '{n}.TmpStaffingSystem.model') : array();
            }
            /**
             * Tinh consumed cua nam hien tai, va consumed cua thang hien tai
             */
            $dataOfYears = array();
            $currentYears = date('Y', time());
            $lastY = strtotime('01-01-'. ($currentYears-3));
            $nextY = strtotime('31-12-'. ($currentYears+3));
            /*
            get consume from activity_requests
             */
            $monthx = range(1, 12);
            $fieldx = array('activity_id', 'SUM(`value`) as consumed');
            //each forward/backward year
            $rangeY = range(date('Y', $lastY), date('Y', $nextY));
            foreach ($rangeY as $cyear) {
                $fieldx[] = 'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . $cyear . '" THEN `value` ELSE 0 END) AS consumed_' . $cyear;
                if( $cyear == $currentYears ){
                    //each month current year
                    foreach ($monthx as $month) {
                        $x = strtotime('01-' . $month . '-' . $currentYears);
                        $x1 = date('M-Y', $x);
                        $x2 = 'consumed_' . strtolower($month == 6 || $month == 7 ? date('F', $x) : date('M', $x));
                        if( $month == 9 ){
                            $x1 = 'Sept';
                            $x2 = 'consumed_sept';
                        }
                        $fieldx[] = 'SUM(CASE WHEN FROM_UNIXTIME(date, "%b-%Y") = "' . $x1 . '" THEN `value` ELSE 0 END) AS ' . $x2;
                    }
                }
            }

            $taskIds = array();
            $list = array();
            foreach ($activityTasks as $dx) {
                if( !isset($list[$dx['activity_id']]) ){
                    $list[$dx['activity_id']] = array();
                }
                $list[$dx['activity_id']][] = $dx['id'];
                $taskIds[] = $dx['id'];
            }
            $field = 'CASE WHEN task_id = 0 THEN activity_id ';
            foreach($list as $id => $task){
                if( empty($task) )continue;
                $field .= sprintf('WHEN task_id IN (%s) THEN %s ', implode($task, ','), $id);
            }
            $field .= ' ELSE NULL END';
            $this->ActivityRequest->virtualFields['activity'] = $field;
            $fieldx[] = 'activity';
			
            $dataConsume = $this->ActivityRequest->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'OR' => array(
                        'activity_id' => $activityIds,
                        'task_id' => $taskIds
                    ),
					'status' => 2,
                    'date BETWEEN ? AND ?' => array($lastY, $nextY),
                ),
                'fields' => $fieldx,
                'group' => array('activity')
            ));
            $dataConsume = Set::combine($dataConsume, '{n}.ActivityRequest.activity', '{n}.0');
			//debug($dataConsume); exit;
			/**
			* Update by QUANNGUYEN 19/02/2019
			* tinh gia tri consumed cac Quater va semester
			*/
			foreach($dataConsume as $act_id => $value){
				$dataConsume[$act_id]['consumed_first'] = $dataConsume[$act_id]['consumed_jan'] + $dataConsume[$act_id]['consumed_feb'] + $dataConsume[$act_id]['consumed_mar'];
				
				$dataConsume[$act_id]['consumed_second'] = $dataConsume[$act_id]['consumed_apr'] + $dataConsume[$act_id]['consumed_may'] + $dataConsume[$act_id]['consumed_june'];
				
				$dataConsume[$act_id]['consumed_third'] = $dataConsume[$act_id]['consumed_july'] + $dataConsume[$act_id]['consumed_aug'] + $dataConsume[$act_id]['consumed_sept'];
				
				$dataConsume[$act_id]['consumed_fourth'] = $dataConsume[$act_id]['consumed_oct'] + $dataConsume[$act_id]['consumed_nov'] + $dataConsume[$act_id]['consumed_dec'];
				
				$dataConsume[$act_id]['consumed_firsts'] = $dataConsume[$act_id]['consumed_jan'] + $dataConsume[$act_id]['consumed_feb'] + $dataConsume[$act_id]['consumed_mar'] + $dataConsume[$act_id]['consumed_apr'] + $dataConsume[$act_id]['consumed_may'] + $dataConsume[$act_id]['consumed_june'];
				
				$dataConsume[$act_id]['consumed_seconds'] = $dataConsume[$act_id]['consumed_july'] + $dataConsume[$act_id]['consumed_aug'] + $dataConsume[$act_id]['consumed_sept'] + $dataConsume[$act_id]['consumed_oct'] + $dataConsume[$act_id]['consumed_nov'] + $dataConsume[$act_id]['consumed_dec'];				
			}
			/**
			* End update by QUANNGUYEN 19/02/2019
			*/
            $dataOfYears = $this->TmpStaffingSystem->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'model' => array('employee'),
                    'activity_id' => $activityIds,
                    'date BETWEEN ? AND ?' => array($lastY, $nextY),
                    'company_id' => $employeeName->company_id
                ),
                'fields' => array(
                    'activity_id',
                    // 'SUM(CASE WHEN FROM_UNIXTIME(date, "%b-%Y") = "Jan-' . $currentYears . '" THEN `consumed` ELSE 0 END) AS consumed_jan',
                    // 'SUM(CASE WHEN FROM_UNIXTIME(date, "%b-%Y") = "Feb-' . $currentYears . '" THEN `consumed` ELSE 0 END) AS consumed_feb',
                    // 'SUM(CASE WHEN FROM_UNIXTIME(date, "%b-%Y") = "Mar-' . $currentYears . '" THEN `consumed` ELSE 0 END) AS consumed_mar',
                    // 'SUM(CASE WHEN FROM_UNIXTIME(date, "%b-%Y") = "Apr-' . $currentYears . '" THEN `consumed` ELSE 0 END) AS consumed_apr',
                    // 'SUM(CASE WHEN FROM_UNIXTIME(date, "%b-%Y") = "May-' . $currentYears . '" THEN `consumed` ELSE 0 END) AS consumed_may',
                    // 'SUM(CASE WHEN FROM_UNIXTIME(date, "%b-%Y") = "Jun-' . $currentYears . '" THEN `consumed` ELSE 0 END) AS consumed_june',
                    // 'SUM(CASE WHEN FROM_UNIXTIME(date, "%b-%Y") = "Jul-' . $currentYears . '" THEN `consumed` ELSE 0 END) AS consumed_july',
                    // 'SUM(CASE WHEN FROM_UNIXTIME(date, "%b-%Y") = "Aug-' . $currentYears . '" THEN `consumed` ELSE 0 END) AS consumed_aug',
                    // 'SUM(CASE WHEN FROM_UNIXTIME(date, "%b-%Y") = "Sept-' . $currentYears . '" THEN `consumed` ELSE 0 END) AS consumed_sept',
                    // 'SUM(CASE WHEN FROM_UNIXTIME(date, "%b-%Y") = "Oct-' . $currentYears . '" THEN `consumed` ELSE 0 END) AS consumed_oct',
                    // 'SUM(CASE WHEN FROM_UNIXTIME(date, "%b-%Y") = "Nov-' . $currentYears . '" THEN `consumed` ELSE 0 END) AS consumed_nov',
                    // 'SUM(CASE WHEN FROM_UNIXTIME(date, "%b-%Y") = "Dec-' . $currentYears . '" THEN `consumed` ELSE 0 END) AS consumed_dec',
                    'SUM(CASE WHEN FROM_UNIXTIME(date, "%b-%Y") = "Jan-' . $currentYears . '" THEN `estimated` ELSE 0 END) AS workload_jan',
                    'SUM(CASE WHEN FROM_UNIXTIME(date, "%b-%Y") = "Feb-' . $currentYears . '" THEN `estimated` ELSE 0 END) AS workload_feb',
                    'SUM(CASE WHEN FROM_UNIXTIME(date, "%b-%Y") = "Mar-' . $currentYears . '" THEN `estimated` ELSE 0 END) AS workload_mar',
                    'SUM(CASE WHEN FROM_UNIXTIME(date, "%b-%Y") = "Apr-' . $currentYears . '" THEN `estimated` ELSE 0 END) AS workload_apr',
                    'SUM(CASE WHEN FROM_UNIXTIME(date, "%b-%Y") = "May-' . $currentYears . '" THEN `estimated` ELSE 0 END) AS workload_may',
                    'SUM(CASE WHEN FROM_UNIXTIME(date, "%b-%Y") = "Jun-' . $currentYears . '" THEN `estimated` ELSE 0 END) AS workload_june',
                    'SUM(CASE WHEN FROM_UNIXTIME(date, "%b-%Y") = "Jul-' . $currentYears . '" THEN `estimated` ELSE 0 END) AS workload_july',
                    'SUM(CASE WHEN FROM_UNIXTIME(date, "%b-%Y") = "Aug-' . $currentYears . '" THEN `estimated` ELSE 0 END) AS workload_aug',
                    'SUM(CASE WHEN FROM_UNIXTIME(date, "%b-%Y") = "Sep-' . $currentYears . '" THEN `estimated` ELSE 0 END) AS workload_sept',
                    'SUM(CASE WHEN FROM_UNIXTIME(date, "%b-%Y") = "Oct-' . $currentYears . '" THEN `estimated` ELSE 0 END) AS workload_oct',
                    'SUM(CASE WHEN FROM_UNIXTIME(date, "%b-%Y") = "Nov-' . $currentYears . '" THEN `estimated` ELSE 0 END) AS workload_nov',
                    'SUM(CASE WHEN FROM_UNIXTIME(date, "%b-%Y") = "Dec-' . $currentYears . '" THEN `estimated` ELSE 0 END) AS workload_dec',
					
					// 'SUM(workload_jan, workload_feb, workload_mar ) AS workload_first',
					
           //          'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . $currentYears . '" THEN `consumed` ELSE 0 END) AS consumed_' . $currentYears,
           //          'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears-1) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears-1),
           //          'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears-2) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears-2),
           //          'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears-3) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears-3),
           //          'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears+1) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears+1),
           //          'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears+2) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears+2),
           //          'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears+3) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears+3),
                    'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . $currentYears . '" THEN `estimated` ELSE 0 END) AS workload_' . $currentYears,
                    'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears-1) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears-1),
                    'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears-2) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears-2),
                    'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears-3) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears-3),
                    'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears+1) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears+1),
                    'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears+2) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears+2),
                    'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears+3) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears+3),
                    // 'SUM(consumed) as consumed',
                    'SUM(estimated) as workload'
                ),
                'group' => array('activity_id')
            )
);		
					
            $dataOfYears = !empty($dataOfYears) ? Set::combine($dataOfYears, '{n}.TmpStaffingSystem.activity_id', '{n}.0') : array();
			/**
			* Update by QUANNGUYEN 19/02/2019
			* tinh gia tri workload cac quater va semester
			*/
			foreach($dataOfYears as $act_id => $value){
				$dataOfYears[$act_id]['workload_first'] = $dataOfYears[$act_id]['workload_jan'] + $dataOfYears[$act_id]['workload_feb'] + $dataOfYears[$act_id]['workload_mar'];
				
				$dataOfYears[$act_id]['workload_second'] = $dataOfYears[$act_id]['workload_apr'] + $dataOfYears[$act_id]['workload_may'] + $dataOfYears[$act_id]['workload_june'];
				
				$dataOfYears[$act_id]['workload_third'] = $dataOfYears[$act_id]['workload_july'] + $dataOfYears[$act_id]['workload_aug'] + $dataOfYears[$act_id]['workload_sept'];
				
				$dataOfYears[$act_id]['workload_fourth'] = $dataOfYears[$act_id]['workload_oct'] + $dataOfYears[$act_id]['workload_nov'] + $dataOfYears[$act_id]['workload_dec'];
				
				$dataOfYears[$act_id]['workload_firsts'] = $dataOfYears[$act_id]['workload_jan'] + $dataOfYears[$act_id]['workload_feb'] + $dataOfYears[$act_id]['workload_mar'] + $dataOfYears[$act_id]['workload_apr'] + $dataOfYears[$act_id]['workload_may'] + $dataOfYears[$act_id]['workload_june'];
				
				$dataOfYears[$act_id]['workload_seconds'] = $dataOfYears[$act_id]['workload_july'] + $dataOfYears[$act_id]['workload_aug'] + $dataOfYears[$act_id]['workload_sept'] + $dataOfYears[$act_id]['workload_oct'] + $dataOfYears[$act_id]['workload_nov'] + $dataOfYears[$act_id]['workload_dec'];
			}
			/**
			* End update by QUANNGUYEN 19/02/2019
			*/
			//debug($dataOfYears); exit;
            /**
             * Build data for screen
             */
            if(!empty($activityIds)){
                /**
                 * Lay mot so column cua activity de tinh toan
                 */
                $activities = $this->Activity->find('all', array(
                    'recursive' => -1,
                    'conditions' => array('Activity.id' => $activityIds),
                    'fields' => array(
                        'code1', 'code2', 'code3', 'price','md', 'raf', 'c32', 'c33', 'c34',
                        'c35', 'c36', 'c37', 'c38', 'c39', 'c40', 'c41','c42', 'c43', 'c44', 'c45',
                        'code4', 'code5', 'code6', 'code7', 'code8', 'code9', 'code10', 'id'
                    )
                ));
                $activities = !empty($activities) ? Set::combine($activities, '{n}.Activity.id', '{n}.Activity') : array();
                foreach($activityIds as $aId){
                    $workload = !empty($sumWorload[$aId]) ? $sumWorload[$aId] : 0;
                    $previous = !empty($sumPrevious[$aId]) ? $sumPrevious[$aId] : 0;
                    $overload = !empty($sumOverload[$aId]) ? $sumOverload[$aId]: 0;
                    $consumed = !empty($sumActivities[$aId]) ? $sumActivities[$aId] : 0;
                    $remainSPCs = isset($sumRemainSpecials[$aId]) ? $sumRemainSpecials[$aId] : 0;
                    $remains = isset($sumRemains[$aId]) ? $sumRemains[$aId] : 0;
                    $remains = $remains - $remainSPCs;
                    $workload = $workload + $previous;
                    $progress = 0;
                    if(($workload + $overload) == 0){
                        $progress = 0;
                    } else {
                        $com = round(($consumed*100)/($workload + $overload), 2);
                        if($com > 100){
                            $progress = 100;
                        } else {
                            $progress = $com;
                        }
                    }
                    $datas[$aId]['real_price'] = 0;
                    if (isset($sumEmployees[$aId])) {
                        foreach($sumEmployees[$aId] as $id => $val) {
                            $reals = !empty($employees[$id]['tjm']) ? (float)str_replace(',', '.', $employees[$id]['tjm']) : 1;
                            if(isset($datas[$aId]['real_price']))
                            $datas[$aId]['real_price'] += round($val * $reals, 2);
                            else
                            $datas[$aId]['real_price'] = round($val * $reals, 2);
                        }
                    }
                    $datas[$aId]['workload'] = $workload;
                    $datas[$aId]['overload'] = $overload;
                    $datas[$aId]['consumed'] = round($consumed, 2);
                    $datas[$aId]['remain'] = $remains;
                    $datas[$aId]['completed'] = $progress;
                    //sales
                    $datas[$aId]['sales_sold'] = !empty($budgets[$aId]['sales_sold']) ? $budgets[$aId]['sales_sold'] : 0;
                    $datas[$aId]['sales_to_bill'] = !empty($budgets[$aId]['sales_to_bill']) ? $budgets[$aId]['sales_to_bill'] : 0;
                    $datas[$aId]['sales_billed'] = !empty($budgets[$aId]['sales_billed']) ? $budgets[$aId]['sales_billed'] : 0;
                    $datas[$aId]['sales_paid'] = !empty($budgets[$aId]['sales_paid']) ? $budgets[$aId]['sales_paid'] : 0;
                    $datas[$aId]['sales_man_day'] = !empty($budgets[$aId]['sales_man_day']) ? $budgets[$aId]['sales_man_day'] : 0;
                    //internal costs
                    $datas[$aId]['internal_costs_budget'] = !empty($budgets[$aId]['internal_costs_budget']) ? $budgets[$aId]['internal_costs_budget'] : 0;
                    $datas[$aId]['internal_costs_budget_man_day'] = !empty($budgets[$aId]['internal_costs_budget_man_day']) ? $budgets[$aId]['internal_costs_budget_man_day'] : 0;
                    $datas[$aId]['internal_costs_average'] = !empty($budgets[$aId]['internal_costs_average']) ? $budgets[$aId]['internal_costs_average'] : 0;
                    $datas[$aId]['internal_costs_engaged'] = $datas[$aId]['real_price'];
                    $datas[$aId]['internal_costs_forecasted_man_day'] = $datas[$aId]['remain'] + $datas[$aId]['consumed'];
                    $_average = !empty($budgets[$aId]['internal_costs_average']) ? $budgets[$aId]['internal_costs_average'] : 0;
                    $datas[$aId]['internal_costs_remain'] = round($datas[$aId]['remain']*$_average, 2);
                    $datas[$aId]['internal_costs_forecast'] = round($datas[$aId]['internal_costs_engaged'] + $datas[$aId]['internal_costs_remain'], 2);
                    $datas[$aId]['internal_costs_var'] = ($datas[$aId]['internal_costs_budget'] == 0) ? '-100%' : round((($datas[$aId]['internal_costs_forecast']/$datas[$aId]['internal_costs_budget']) - 1)*100, 2).'%';
                    //external costs
                    $datas[$aId]['external_costs_budget'] = !empty($budgets[$aId]['external_costs_budget']) ? $budgets[$aId]['external_costs_budget'] : 0;
                    $datas[$aId]['external_costs_forecast'] = !empty($budgets[$aId]['external_costs_forecast']) ? $budgets[$aId]['external_costs_forecast'] : 0;
                    $datas[$aId]['external_costs_var'] = !empty($budgets[$aId]['external_costs_var']) ? $budgets[$aId]['external_costs_var']. ' %' : '0 %';
                    $datas[$aId]['external_costs_ordered'] = !empty($budgets[$aId]['external_costs_ordered']) ? $budgets[$aId]['external_costs_ordered'] : 0;
                    $datas[$aId]['external_costs_remain'] = !empty($budgets[$aId]['external_costs_remain']) ? $budgets[$aId]['external_costs_remain'] : 0;
                    $datas[$aId]['external_costs_man_day'] = !empty($budgets[$aId]['external_costs_man_day']) ? $budgets[$aId]['external_costs_man_day'] : 0;
                    $datas[$aId]['external_costs_progress'] = !empty($budgets[$aId]['external_costs_progress']) ? $budgets[$aId]['external_costs_progress'] : 0;
                    $datas[$aId]['external_costs_progress_euro'] = !empty($budgets[$aId]['external_costs_progress_euro']) ? $budgets[$aId]['external_costs_progress_euro'] : 0;
                    //total costs
                    $datas[$aId]['total_costs_budget'] = $datas[$aId]['internal_costs_budget'] + $datas[$aId]['external_costs_budget'];
                    $datas[$aId]['total_costs_forecast'] = $datas[$aId]['internal_costs_forecast'] + $datas[$aId]['external_costs_forecast'];
                    $datas[$aId]['total_costs_engaged'] = $datas[$aId]['internal_costs_engaged'] + $datas[$aId]['external_costs_ordered'];
                    $datas[$aId]['total_costs_remain'] = $datas[$aId]['internal_costs_remain'] + $datas[$aId]['external_costs_remain'];
                    $datas[$aId]['total_costs_man_day'] = $datas[$aId]['internal_costs_forecasted_man_day'] + $datas[$aId]['external_costs_man_day'];
                    $datas[$aId]['total_costs_var'] = ($datas[$aId]['total_costs_budget'] == 0) ? '-100%' : round((($datas[$aId]['total_costs_forecast']/$datas[$aId]['total_costs_budget'])-1)*100, 2). '%';
                    //assign to
                    $tWorkload = $datas[$aId]['workload'] + $datas[$aId]['overload'];
                    $assgnEm = !empty($dataSystems) && !empty($dataSystems['employee'][$aId]) ? $dataSystems['employee'][$aId] : 0;
                    $assgnPc = !empty($dataSystems) && !empty($dataSystems['profit_center'][$aId]) ? $dataSystems['profit_center'][$aId] : 0;
                    $datas[$aId]['assign_to_employee'] = '0%';
                    $datas[$aId]['assign_to_profit_center'] = '0%';
                    if($tWorkload == 0){
                        $datas[$aId]['assign_to_employee'] = '0%';
                        $datas[$aId]['assign_to_profit_center'] = '0%';
                    } else {
                        $datas[$aId]['assign_to_employee'] = (round(($assgnEm/$tWorkload)*100, 2) > 100) ? '100%' : round(($assgnEm/$tWorkload)*100, 2).'%';
                        $datas[$aId]['assign_to_profit_center'] = (round(($assgnPc/$tWorkload)*100, 2) > 100) ? '100%' : round(($assgnPc/$tWorkload)*100, 2).'%';
                    }
                    // data of year
                    $datas[$aId]['consumed_current_year'] = !empty($dataConsume[$aId]['consumed_'.$currentYears]) ? round($dataConsume[$aId]['consumed_'.$currentYears], 2): '';

                    $datas[$aId]['workload_y'] = !empty($dataOfYears[$aId]['workload_'.$currentYears]) ? $dataOfYears[$aId]['workload_'.$currentYears]: 0;
                    $datas[$aId]['workload_last_one_y'] = !empty($dataOfYears[$aId]['workload_'.($currentYears-1)]) ? $dataOfYears[$aId]['workload_'.($currentYears-1)]: 0;
                    $datas[$aId]['workload_last_two_y'] = !empty($dataOfYears[$aId]['workload_'.($currentYears-2)]) ? $dataOfYears[$aId]['workload_'.($currentYears-2)]: 0;
                    $datas[$aId]['workload_last_thr_y'] = !empty($dataOfYears[$aId]['workload_'.($currentYears-3)]) ? $dataOfYears[$aId]['workload_'.($currentYears-3)]: 0;
                    $datas[$aId]['workload_next_one_y'] = !empty($dataOfYears[$aId]['workload_'.($currentYears+1)]) ? $dataOfYears[$aId]['workload_'.($currentYears+1)]: 0;
                    $datas[$aId]['workload_next_two_y'] = !empty($dataOfYears[$aId]['workload_'.($currentYears+2)]) ? $dataOfYears[$aId]['workload_'.($currentYears+2)]: 0;
                    $datas[$aId]['workload_next_thr_y'] = !empty($dataOfYears[$aId]['workload_'.($currentYears+3)]) ? $dataOfYears[$aId]['workload_'.($currentYears+3)]: 0;

                    $datas[$aId]['consumed_y'] = !empty($dataConsume[$aId]['consumed_'.$currentYears]) ? round($dataConsume[$aId]['consumed_'.$currentYears], 2): 0;
                    $datas[$aId]['consumed_last_one_y'] = !empty($dataConsume[$aId]['consumed_'.($currentYears-1)]) ? round($dataConsume[$aId]['consumed_'.($currentYears-1)], 2): 0;
                    $datas[$aId]['consumed_last_two_y'] = !empty($dataConsume[$aId]['consumed_'.($currentYears-2)]) ? round($dataConsume[$aId]['consumed_'.($currentYears-2)], 2): 0;
                    $datas[$aId]['consumed_last_thr_y'] = !empty($dataConsume[$aId]['consumed_'.($currentYears-3)]) ? round($dataConsume[$aId]['consumed_'.($currentYears-3)], 2): 0;
                    $datas[$aId]['consumed_next_one_y'] = !empty($dataConsume[$aId]['consumed_'.($currentYears+1)]) ? round($dataConsume[$aId]['consumed_'.($currentYears+1)], 2): 0;
                    $datas[$aId]['consumed_next_two_y'] = !empty($dataConsume[$aId]['consumed_'.($currentYears+2)]) ? round($dataConsume[$aId]['consumed_'.($currentYears+2)], 2): 0;
                    $datas[$aId]['consumed_next_thr_y'] = !empty($dataConsume[$aId]['consumed_'.($currentYears+3)]) ? round($dataConsume[$aId]['consumed_'.($currentYears+3)], 2): 0;

                    $datas[$aId]['workload_jan'] = !empty($dataOfYears) && !empty($dataOfYears[$aId]['workload_jan']) ? $dataOfYears[$aId]['workload_jan'] : '';
                    $datas[$aId]['workload_feb'] = !empty($dataOfYears) && !empty($dataOfYears[$aId]['workload_feb']) ? $dataOfYears[$aId]['workload_feb'] : '';
                    $datas[$aId]['workload_mar'] = !empty($dataOfYears) && !empty($dataOfYears[$aId]['workload_mar']) ? $dataOfYears[$aId]['workload_mar'] : '';
                    $datas[$aId]['workload_apr'] = !empty($dataOfYears) && !empty($dataOfYears[$aId]['workload_apr']) ? $dataOfYears[$aId]['workload_apr'] : '';
                    $datas[$aId]['workload_may'] = !empty($dataOfYears) && !empty($dataOfYears[$aId]['workload_may']) ? $dataOfYears[$aId]['workload_may'] : '';
                    $datas[$aId]['workload_june'] = !empty($dataOfYears) && !empty($dataOfYears[$aId]['workload_june']) ? $dataOfYears[$aId]['workload_june'] : '';
                    $datas[$aId]['workload_july'] = !empty($dataOfYears) && !empty($dataOfYears[$aId]['workload_july']) ? $dataOfYears[$aId]['workload_july'] : '';
                    $datas[$aId]['workload_aug'] = !empty($dataOfYears) && !empty($dataOfYears[$aId]['workload_aug']) ? $dataOfYears[$aId]['workload_aug'] : '';
                    $datas[$aId]['workload_sept'] = !empty($dataOfYears) && !empty($dataOfYears[$aId]['workload_sept']) ? $dataOfYears[$aId]['workload_sept'] : '';
                    $datas[$aId]['workload_oct'] = !empty($dataOfYears) && !empty($dataOfYears[$aId]['workload_oct']) ? $dataOfYears[$aId]['workload_oct'] : '';
                    $datas[$aId]['workload_nov'] = !empty($dataOfYears) && !empty($dataOfYears[$aId]['workload_nov']) ? $dataOfYears[$aId]['workload_nov'] : '';
                    $datas[$aId]['workload_dec'] = !empty($dataOfYears) && !empty($dataOfYears[$aId]['workload_dec']) ? $dataOfYears[$aId]['workload_dec'] : '';
					/**
					* Update by QUANNGUYEN 19/02/2019
					*/
					$datas[$aId]['workload_first'] = !empty($dataOfYears) && !empty($dataOfYears[$aId]['workload_first']) ? $dataOfYears[$aId]['workload_first'] : '';
					
					$datas[$aId]['workload_second'] = !empty($dataOfYears) && !empty($dataOfYears[$aId]['workload_second']) ? $dataOfYears[$aId]['workload_second'] : '';
					
					$datas[$aId]['workload_third'] = !empty($dataOfYears) && !empty($dataOfYears[$aId]['workload_third']) ? $dataOfYears[$aId]['workload_third'] : '';
					
					$datas[$aId]['workload_fourth'] = !empty($dataOfYears) && !empty($dataOfYears[$aId]['workload_fourth']) ? $dataOfYears[$aId]['workload_fourth'] : '';
					
					$datas[$aId]['workload_firsts'] = !empty($dataOfYears) && !empty($dataOfYears[$aId]['workload_firsts']) ? $dataOfYears[$aId]['workload_firsts'] : '';
					
					$datas[$aId]['workload_seconds'] = !empty($dataOfYears) && !empty($dataOfYears[$aId]['workload_seconds']) ? $dataOfYears[$aId]['workload_seconds'] : '';					
					/**
					* End update by QUANNGUYEN 19/02/2019
					*/
                    $datas[$aId]['consumed_jan'] = !empty($dataConsume) && !empty($dataConsume[$aId]['consumed_jan']) ? round($dataConsume[$aId]['consumed_jan'], 2) : '';
                    $datas[$aId]['consumed_feb'] = !empty($dataConsume) && !empty($dataConsume[$aId]['consumed_feb']) ? round($dataConsume[$aId]['consumed_feb'], 2) : '';
                    $datas[$aId]['consumed_mar'] = !empty($dataConsume) && !empty($dataConsume[$aId]['consumed_mar']) ? round($dataConsume[$aId]['consumed_mar'], 2) : '';
                    $datas[$aId]['consumed_apr'] = !empty($dataConsume) && !empty($dataConsume[$aId]['consumed_apr']) ? round($dataConsume[$aId]['consumed_apr'], 2) : '';
                    $datas[$aId]['consumed_may'] = !empty($dataConsume) && !empty($dataConsume[$aId]['consumed_may']) ? round($dataConsume[$aId]['consumed_may'], 2) : '';
                    $datas[$aId]['consumed_june'] = !empty($dataConsume) && !empty($dataConsume[$aId]['consumed_june']) ? round($dataConsume[$aId]['consumed_june'], 2) : '';
                    $datas[$aId]['consumed_july'] = !empty($dataConsume) && !empty($dataConsume[$aId]['consumed_july']) ? round($dataConsume[$aId]['consumed_july'], 2) : '';
                    $datas[$aId]['consumed_aug'] = !empty($dataConsume) && !empty($dataConsume[$aId]['consumed_aug']) ? round($dataConsume[$aId]['consumed_aug'], 2) : '';
                    $datas[$aId]['consumed_sept'] = !empty($dataConsume) && !empty($dataConsume[$aId]['consumed_sept']) ? round($dataConsume[$aId]['consumed_sept'], 2) : '';
                    $datas[$aId]['consumed_oct'] = !empty($dataConsume) && !empty($dataConsume[$aId]['consumed_oct']) ? round($dataConsume[$aId]['consumed_oct'], 2) : '';
                    $datas[$aId]['consumed_nov'] = !empty($dataConsume) && !empty($dataConsume[$aId]['consumed_nov']) ? round($dataConsume[$aId]['consumed_nov'], 2) : '';
                    $datas[$aId]['consumed_dec'] = !empty($dataConsume) && !empty($dataConsume[$aId]['consumed_dec']) ? round($dataConsume[$aId]['consumed_dec'], 2) : '';
					/**
					* Update by QUANNGUYEN 19/02/2019
					*/
					$datas[$aId]['consumed_first'] = !empty($dataConsume) && !empty($dataConsume[$aId]['consumed_first']) ? round($dataConsume[$aId]['consumed_first'], 2) : '';
					
					$datas[$aId]['consumed_second'] = !empty($dataConsume) && !empty($dataConsume[$aId]['consumed_second']) ? round($dataConsume[$aId]['consumed_second'], 2) : '';
					
					$datas[$aId]['consumed_third'] = !empty($dataConsume) && !empty($dataConsume[$aId]['consumed_third']) ? round($dataConsume[$aId]['consumed_third'], 2) : '';
					
					$datas[$aId]['consumed_fourth'] = !empty($dataConsume) && !empty($dataConsume[$aId]['consumed_fourth']) ? round($dataConsume[$aId]['consumed_fourth'], 2) : '';
					
					$datas[$aId]['consumed_firsts'] = !empty($dataConsume) && !empty($dataConsume[$aId]['consumed_firsts']) ? round($dataConsume[$aId]['consumed_firsts'], 2) : '';
					
					$datas[$aId]['consumed_seconds'] = !empty($dataConsume) && !empty($dataConsume[$aId]['consumed_seconds']) ? round($dataConsume[$aId]['consumed_seconds'], 2) : '';
					/**
					* End update by QUANNGUYEN 19/02/2019
					*/
                    //data from input activitydataConsume
                    $datas[$aId]['code1'] = (string) !empty($activities) && !empty($activities[$aId]['code1']) ? $activities[$aId]['code1'] : '';
                    $datas[$aId]['code2'] = (string) !empty($activities) && !empty($activities[$aId]['code2']) ? $activities[$aId]['code2'] : '';
                    $datas[$aId]['code3'] = (string) !empty($activities) && !empty($activities[$aId]['code3']) ? $activities[$aId]['code3'] : '';
                    $datas[$aId]['c32'] = (string) !empty($activities) && !empty($activities[$aId]['c32']) ? $activities[$aId]['c32'] : '';
                    $datas[$aId]['c33'] = (string) !empty($activities) && !empty($activities[$aId]['c33']) ? $activities[$aId]['c33'] : '';
                    $datas[$aId]['c34'] = (string) !empty($activities) && !empty($activities[$aId]['c34']) ? $activities[$aId]['c34'] : '';
                    $datas[$aId]['c35'] = (string) !empty($activities) && !empty($activities[$aId]['c35']) ? $activities[$aId]['c35'] : '';
                    $datas[$aId]['c36'] = (string) !empty($activities) && !empty($activities[$aId]['c36']) ? $activities[$aId]['c36'] : '';
                    $datas[$aId]['c37'] = (string) !empty($activities) && !empty($activities[$aId]['c37']) ? $activities[$aId]['c37'] : '';
                    $datas[$aId]['c38'] = (string) !empty($activities) && !empty($activities[$aId]['c38']) ? $activities[$aId]['c38'] : '';
                    $datas[$aId]['c39'] = (string) !empty($activities) && !empty($activities[$aId]['c39']) ? $activities[$aId]['c39'] : '';
                    $datas[$aId]['c40'] = (string) !empty($activities) && !empty($activities[$aId]['c40']) ? $activities[$aId]['c40'] : '';
                    $datas[$aId]['c41'] = (string) !empty($activities) && !empty($activities[$aId]['c41']) ? $activities[$aId]['c41'] : '';
                    $datas[$aId]['c42'] = (string) !empty($activities) && !empty($activities[$aId]['c42']) ? $activities[$aId]['c42'] : '';
                    $datas[$aId]['c43'] = (string) !empty($activities) && !empty($activities[$aId]['c43']) ? $activities[$aId]['c43'] : '';
                    $datas[$aId]['c44'] = (string) !empty($activities) && !empty($activities[$aId]['c44']) ? $activities[$aId]['c44'] : '';
                    $datas[$aId]['c45'] = (string) !empty($activities) && !empty($activities[$aId]['c45']) ? $activities[$aId]['c45'] : '';
                    $datas[$aId]['code4'] = (string) !empty($activities) && !empty($activities[$aId]['code4']) ? $activities[$aId]['code4'] : '';
                    $datas[$aId]['code5'] = (string) !empty($activities) && !empty($activities[$aId]['code5']) ? $activities[$aId]['code5'] : '';
                    $datas[$aId]['code6'] = (string) !empty($activities) && !empty($activities[$aId]['code6']) ? $activities[$aId]['code6'] : '';
                    $datas[$aId]['code7'] = (string) !empty($activities) && !empty($activities[$aId]['code7']) ? $activities[$aId]['code7'] : '';
                    $datas[$aId]['code8'] = (string) !empty($activities) && !empty($activities[$aId]['code8']) ? $activities[$aId]['code8'] : '';
                    $datas[$aId]['code9'] = (string) !empty($activities) && !empty($activities[$aId]['code9']) ? $activities[$aId]['code9'] : '';
                    $datas[$aId]['code10'] = (string) !empty($activities) && !empty($activities[$aId]['code10']) ? $activities[$aId]['code10'] : '';

                    foreach ($activityColumn as $key => $column) {
                        if (empty($column['calculate'])) {
                            continue;
                        }
                        if (!isset($column['_match'])) {
                            preg_match_all('/C\d+/i', $column['calculate'], $column['match']);
                            $column['match'] = array_unique($column['match'][0]);
                        }
                        $cal = $column['calculate'];
                        if (!empty($column['match'])) {
                            foreach ($column['match'] as $k) {
                                $cal = str_replace($k, isset($datas[$aId][$map[$k]]) ? floatval($datas[$aId][$map[$k]]) : 0, $cal);
                            }
                        }
						$res = @eval("return ($cal);");
						if( is_nan($res)){
							$res = '';
						}
                        $datas[$aId][$key] = $res;
                        if (!is_numeric($datas[$aId][$key])) {
                            $datas[$aId][$key] = 0;
                        } elseif (is_float($datas[$aId][$key])) {
                            $datas[$aId][$key] = round($datas[$aId][$key], 2);
                        }

                    }
                }
            }
        }
        die(json_encode($datas));
    }

    function getAllActivityTask($id){
        $this->autoRender = false;
        $allActivityTasks = $this->Activity->ActivityTask->find('count', array(
            'conditions' => array('activity_id' => $id)
        ));
        echo $allActivityTasks;
        exit;
    }

    /**
     * import csv file.
     * Check email exists in file and database.
     * Check company in file have exists in database.
     * Check role in file have exists in database.
     * Check profit center in file have exists in database.
     * Check functions center in file have exists in database.
     * If case on exists then notify.
     *
     * @param int $project_id
     * @return void
     * @access public
     */
    protected $default = array(
        'No.' => '',
        'Name' => '',
        'Long name' => '',
        'Short name' => '',
        'Family' => '',
        'Subfamily' => '',
        'Customer' => '',
        'Project Manager' => '',
        'Profit Accessible' => '',
        'PMS' => '',
        'Project' => '',
        'Profit Linked' => '',
        'N&deg; DT' => '',
        'N&deg; UAG' => '',
        'Code Analytique' => '',
        'Start date' => '',
        'End date' => '',
        'Import Code' => '',
        'Activated' => '',
        'Code 4' => '',
        'Code 5' => '',
        'Code 6' => '',
        'Code 7' => '',
        'Code 8' => '',
        'Code 9' => '',
        'Code 10' => ''
    );
    function import() {
        set_time_limit(0);
        if (!($employeeName = $this->_getEmpoyee())) {
            $this->Session->setFlash(__('Your account do not have a company to management activity.', true), 'error');
            $this->redirect(array('controller' => 'activities', 'action' => 'index'));
        }
        App::import('Vendor', 'ParsecsvLib', array('file' => 'parsecsv.lib.php'));
        App::import('Core', 'Validation', false);
        $csv = new parseCSV();
        if (!empty($_FILES['FileField']['name']['csv_file_attachment'])) {
            $this->MultiFileUpload->encode_filename = false;
            $this->MultiFileUpload->uploadpath = TMP . 'uploads' . DS . 'Activities' . DS;
            $this->MultiFileUpload->_properties['AttachTypeAllowed'] = "csv";
            $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
            $reVal = $this->MultiFileUpload->upload();
            if (!empty($reVal)) {
                $filename = TMP . 'uploads' . DS . 'Activities' . DS . $reVal['csv_file_attachment']['csv_file_attachment'];
                $csv->auto($filename);
                if (!empty($csv->data)) {
                    $records = array(
                        'Create' => array(),
                        'Update' => array(),
                        'Error' => array()
                    );
                    $default = $this->default;
                    $titleColumns = array_keys($default);

                    $this->loadModel('ProfitCenter');
                    $this->ProfitCenter->cacheQueries = $this->Activity->Family->cacheQueries = true;
                    $this->Activity->cacheQueries = false;

                    $listName = array();
                    $this->loadModel('Employee');
                    $this->loadModel('BudgetCustomer');
                    $this->loadModel('Project');

                    $validate = array('Name', 'Short name', 'Family');
                    foreach ($csv->data as $r) {
                        $error = false;
                        //pr($row);
                        $row = $this->renameKeys($r, $titleColumns);
                        $row = array_merge($default, $row, array('data' => array(), 'error' => array(),'columnHighLight' => array()));
                        // pr($row);
                        // die;
                        foreach ($validate as $key) {
                            $row[$key] = trim($row[$key]);
                            if (empty($row[$key])) {
                                $row['columnHighLight'][$key] =  '';
                                $row['error'][] = sprintf(__('The %s is not blank', true), $key);
                                $error = true;
                            }
                        }
                        if (!$error) {

                            // Profit Accessible

                            if (!empty($row['Profit Accessible'])) {
                                $tmp = array_filter(explode(';', trim($row['Profit Accessible'])));
                                if (!empty($tmp)) {
                                    foreach ($tmp as $value) {
                                        $_value = $this->ProfitCenter->find('first', array(
                                            'recursive' => -1, 'fields' => array('id'),
                                            'conditions' => array('name' => trim($value), 'company_id' => $employeeName['company_id'])));

                                        if (empty($_value)) {
                                            $row['columnHighLight']['Profit Accessible'] =  '';
                                            $row['error'][] = sprintf(__('The Profit Center ID \'%s\' does not exist', true), $value);
                                            $error = true;
                                            break;
                                        }
                                        $row['data']['accessible_profit'][] = $_value['ProfitCenter']['id'];
                                    }
                                }
                            }

                            // Profit Linked
                            if (!empty($row['Profit Linked'])) {
                                $tmp = $this->ProfitCenter->find('first', array(
                                    'recursive' => -1, 'fields' => array('id'),
                                    'conditions' => array('name' => trim($row['Profit Linked']), 'company_id' => $employeeName['company_id'])));
                                if (empty($tmp)) {
                                    $row['columnHighLight']['Profit Linked'] =  '';
                                    $row['error'][] = sprintf(__('The Profit Center ID \'%s\' does not exist', true), $row['Profit Linked']);
                                    $error = true;
                                    //break;
                                } else {
                                    $row['data']['linked_profit'] = $tmp['ProfitCenter']['id'];
                                }
                            }

                            // Family
                            if (!empty($row['Family'])) {
                                $tmp = $this->Activity->Family->find('first', array(
                                    'recursive' => -1, 'fields' => array('id'),
                                    'conditions' => array('name' => trim($row['Family']), 'parent_id' => null, 'company_id' => $employeeName['company_id'])));
                                if ($tmp) {
                                    $row['data']['family_id'] = $tmp['Family']['id'];
                                } else {
                                    $row['columnHighLight']['Family'] =  '';
                                    $row['error'][] = sprintf(__('The Family ID \'%s\' does not exist', true), $row['Family']);
                                    $error = true;
                                }
                            }

                            // Subfamily
                            if (!empty($row['Subfamily']) && !empty($row['data']['family_id'])) {
                                $tmp = $this->Activity->Family->find('first', array(
                                    'recursive' => -1, 'fields' => array('id'),
                                    'conditions' => array('name' => trim($row['Subfamily']), 'parent_id' => $row['data']['family_id'])));
                                if ($tmp) {
                                    $row['data']['subfamily_id'] = $tmp['Family']['id'];
                                } else {
                                    $row['columnHighLight']['Subfamily'] =  '';
                                    $row['error'][] = sprintf(__('The Subfamily ID \'%s\' does not exist', true), $row['Subfamily']);
                                    $error = true;
                                }
                            } elseif (!empty($row['Subfamily'])) {
                                $row['columnHighLight']['Subfamily'] =  '';
                                $row['error'][] = __('The Family was not found', true);
                                $error = true;
                            }
                            // if(empty($row['Subfamily'])){
                            //     $row['columnHighLight']['Subfamily'] =  '';
                            // }

                            // Start date
                            if (!empty($row['Start date'])) {
                                $row['data']['start_date'] = str_replace('/', '-', $row['Start date']);
                                if (!$row['data']['start_date']) {
                                    $row['columnHighLight']['Start date'] =  '';
                                    $row['error'][] = __('The start date is invalid.', true);
                                    $error = true;
                                }
                            } else {
                                $row['data']['start_date'] = '';
                            }
                            // End date
                            if (!empty($row['End date'])) {
                                $row['data']['end_date'] = str_replace('/', '-', $row['End date']);
                                if (!$row['data']['end_date']) {
                                    $row['columnHighLight']['End date'] =  '';
                                    $row['error'][] = __('The end date is invalid.', true);
                                    $error = true;
                                } elseif (!empty($row['data']['start_date']) && strtotime($this->ProfitCenter->convertTime($row['data']['start_date'])) > strtotime($this->ProfitCenter->convertTime($row['data']['end_date']))) {
                                    $row['columnHighLight']['End date'] =  '';
                                    $row['error'][] = __('The start date conflict with end date.', true);
                                    $error = true;
                                }
                            } else {
                                $row['data']['end_date'] = '';
                            }
                            // Name
                            $row['Name'] = trim($row['Name']);
                            if (isset($listName[$row['Name']])) {
                                $row['columnHighLight']['Name'] =  '';
                                $row['error'][] = __('The name has already been exist in this file.', true);
                                $error = true;
                            } else {
                               // debug($listName);
                                //  exit;
                                $listName[$row['Name']] ='';
                                $tmp = $this->Activity->find('first', array(
                                'recursive' => -1, 'fields' => array('id','project', 'pms'),
                                'conditions' => array('name' => $row['Name'], 'company_id' => $employeeName['company_id'])));
                                if ($tmp) {
                                    $row['data']['id'] = $tmp['Activity']['id'];
                                    $row['tmp']['project_id'] = $tmp['Activity']['project'];
                                    $row['tmp']['pms'] = $tmp['Activity']['pms'];
                                }
                                $row['data']['name'] = $row['Name'];
                            }
                            //$listName[$row['Name']] = $row['Name'];


                            // Long name
                            // if (empty($row['Long name'])) {
                            //     $row['columnHighLight']['Long name'] =  '';
                            // }
                            $row['data']['long_name'] = $row['Long name'];

                            // Short name
                            if (!empty($row['Short name'])) {
                                $row['data']['short_name'] = $row['Short name'];
                            }else{
                                $row['columnHighLight']['Short name'] =  '';
                            }

                            // PMS
                            /*if (!empty($row['PMS'])) {
                                $row['data']['pms'] = ($row['PMS'] == 'yes');
                            }else{
                                $row['columnHighLight']['PMS'] =  '';
                            }*/

                            // Code 1
                            // if (empty($row['N&deg; DT'])){
                            //     $row['columnHighLight']['N&deg; DT'] =  '';
                            // }
                            $row['data']['code1'] = $row['N&deg; DT'];

                            // Code 2
                            // if (empty($row['N&deg; UAG'])) {
                            //     $row['columnHighLight']['N&deg; UAG'] =  '';
                            // }
                            $row['data']['code2'] = $row['N&deg; UAG'];

                            // Code 3
                            // if (empty($row['Code Analytique'])){
                            //     $row['columnHighLight']['Code Analytique'] =  '';
                            // }
                            $row['data']['code3'] = $row['Code Analytique'];
                            //code 4 -> 10
                            // if (empty($row['Code 4'])){
                            //     $row['columnHighLight']['Code 4'] =  '';
                            // }
                            $row['data']['code4'] = $row['Code 4'];

                            // if (empty($row['Code 5'])){
                            //     $row['columnHighLight']['Code 5'] =  '';
                            // }
                            $row['data']['code5'] = $row['Code 5'];

                            // if (empty($row['Code 6'])){
                            //     $row['columnHighLight']['Code 6'] =  '';
                            // }
                            $row['data']['code6'] = $row['Code 6'];

                            // if (empty($row['Code 7'])){
                            //     $row['columnHighLight']['Code 7'] =  '';
                            // }
                            $row['data']['code7'] = $row['Code 7'];

                            // if (empty($row['Code 8'])){
                            //     $row['columnHighLight']['Code 8'] =  '';
                            // }
                            $row['data']['code8'] = $row['Code 8'];

                            // if (empty($row['Code 9'])){
                            //     $row['columnHighLight']['Code 9'] =  '';
                            // }
                            $row['data']['code9'] = $row['Code 9'];

                            // if (empty($row['Code 10'])){
                            //     $row['columnHighLight']['Code 10'] =  '';
                            // }
                            $row['data']['code10'] = $row['Code 10'];


                            //get customer id
                            if (!empty($row['Customer'])) {
                                $test = $this->BudgetCustomer->find('first', array(
                                    'fields' => 'id',
                                    'recursive' => -1,
                                    'conditions' => array(
                                        'name' => trim($row['Customer']),
                                        'company_id' => $employeeName['company_id']
                                    )
                                ));
                                if( !empty($test) )
                                    $row['data']['budget_customer_id'] = $test['BudgetCustomer']['id'];
                                else {
                                    $error = true;
                                    $row['error'][] = __('Customer not found!', true);
                                    $row['columnHighLight']['Customer'] =  '';
                                }
                            } else {
                                $row['data']['budget_customer_id'] = '';
                            }
                            //on update && have project ID
                            if( isset($row['tmp']['project_id'])){
                                $id = $row['data']['id'];
                                $pId = $row['tmp']['project_id'];
                                //get managers
                                $raw = explode(',', $row['Project Manager']);
                                $managers = array();
                                foreach($raw as $name){
                                    $trueName = trim(str_replace('(B)', '', trim($name)));
                                    $data = $this->Employee->find('first', array(
                                        'recursive' => -1,
                                        'fields' => 'Employee.id',
                                        'conditions' => array(
                                            'CONCAT_WS(\' \', first_name, last_name)' => $trueName,
                                            'Refer.company_id' => $employeeName['company_id']
                                        ),
                                        'joins' => array(
                                            array(
                                                'table' => 'company_employee_references',
                                                'alias' => 'Refer',
                                                'type' => 'left',
                                                'conditions' => array(
                                                    'Refer.employee_id = Employee.id'
                                                )
                                            )
                                        )
                                    ));
                                    if( !empty($data) ){
                                        if( strpos($name, '(B)') !== false )
                                            $managers[] = $data['Employee']['id'];
                                        //main manager has *
                                        else $managers[] = '*' . $data['Employee']['id'];
                                    }
                                }
                                //add field managers
                                $row['data']['managers'] = implode(';', $managers);
                                //add field project id
                                $row['data']['project_id'] = $pId;
                            }



                            //activated & pms
                            $row['data']['actif'] = $row['Activated'] == 'NO' || empty($row['Activated']) ? 0 : 1;
                            $row['data']['pms'] = $row['PMS'] == 'NO' || empty($row['PMS']) ? 0 : 1;

                            // if (empty($row['Import Code'])){
                            //     $row['columnHighLight']['Import Code'] =  '';
                            // }
                            $row['data']['import_code'] = $row['Import Code'];

                        }

                        if ($error) {
                            unset($row['data']);
                            $records['Error'][] = $row;
                        } else {
                            if (!empty($row['data']['id'])) {
                                //check if activity has task(s)
                                $activityId = $row['data']['id'];
                                $count = $this->Activity->ActivityTask->find('count', array(
                                    'conditions' => array(
                                        'activity_id' => $activityId
                                    ),
                                    'recursive' => -1
                                ));
                                if( $count && ( isset($row['tmp']['pms']) && $row['tmp']['pms'] != $row['data']['pms'] ) ){
                                    $row['error'][] = __('Can not change PMS status because activity already had task(s)!', true);
                                    $row['columnHighLight']['PMS'] =  '';
                                    //show error
                                    unset($row['data']);
                                    $records['Error'][] = $row;
                                } else {
                                    $records['Update'][] = $row;
                                }
                            } else {
                                $records['Create'][] = $row;
                            }
                        }

                    }
                }
                unlink($filename);
            }
            //pr($records);die;
            //$titleColumns = $this->_utf8_encode_mix($titleColumns);
            array_shift($titleColumns);
            $this->set(compact('records', 'employeeName','default','titleColumns'));
        } else {
            $this->redirect(array('action' => 'index'));
        }
    }

    function renameKeys($row, $newKeys){
        $oldKeys = array_keys($row);
        $result = array();
        $i = 0;
        $max = count($newKeys);
        foreach($row as $col){
            $result[$newKeys[$i]] = $row[$oldKeys[$i]];
            $i++;
            if( $max == $i )break;
        }
        return $result;
    }

    function save_import() {
        set_time_limit(0);
        if (!($employeeName = $this->_getEmpoyee())) {
            $this->Session->setFlash(__('Your account do not have a company to management activity.', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }

        if (!empty($this->data)) {
            extract($this->data['Import']);
            if ($task === 'do') {
                $import = array();
                //debug($this->data[$type][$task]);
//                exit;
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
                $this->loadModel('ProjectEmployeeManager');
                $this->loadModel('Project');
                foreach ($import as $data) {
                    $accessibleProfit = array();
                    if (!empty($data['accessible_profit'])) {
                        $accessibleProfit = explode(';', $data['accessible_profit']);
                        unset($data['accessible_profit']);
                    }
                    $linkedProfit = '';
                    if (!empty($data['linked_profit'])) {
                        $linkedProfit = $data['linked_profit'];
                        unset($data['linked_profit']);
                    }
                    if (!empty($data['id'])) {
                        $this->Activity->id = $data['id'];
                        unset($data['id']);
                    } else {
                        $this->Activity->create();
                    }
                    foreach (array('start_date', 'end_date') as $d) {
                        if (isset($data[$d])) {
                            $data[$d] = strtotime($this->Activity->convertTime($data[$d]));
                        }
                    }
                    $data['allow_profit'] = !($accessibleProfit && is_array($accessibleProfit));
                    $data['company_id'] = $employeeName['company_id'];
                    if ($this->Activity->save($data)) {
                        $this->Activity->id = $this->Activity->id ? $this->Activity->id : $this->Activity->getInsertID();

                        //save managers
                        $projectId = isset($data['project_id']) ? $data['project_id'] : '';
                        $list_backup = isset($data['managers']) ? explode(';', $data['managers']) : array();
                        if( $projectId && !empty($list_backup) ){
                            $mainManagerId = null;
                            $trueList = array();
                            //save backup managers
                            foreach($list_backup as $mid){
                                $trueId = str_replace('*', '', $mid);
                                if( strpos($mid, '*') !== false ){
                                    $mainManagerId = $trueId;
                                    continue;
                                }
                                $trueList[] = $mid;
                                $df = $this->ProjectEmployeeManager->find('first', array(
                                    'recursive' => -1,
                                    'conditions' => array(
                                        'project_id' => $projectId,
                                        'project_manager_id' => $trueId,
                                        'activity_id' => $this->Activity->id,
                                        'is_profit_center' => 0
                                    )
                                ));
                                if( empty($df) ){
                                    $this->ProjectEmployeeManager->create();
                                    $this->ProjectEmployeeManager->save(array(
                                        'project_id' => $projectId,
                                        'project_manager_id' => $trueId,
                                        'is_backup' => 1,
                                        'activity_id' => $this->Activity->id
                                    ));
                                }
                            }

                            //save main manager
                            //to activity
                            $this->Activity->saveField('project_manager_id', $mainManagerId);
                            //to project
                            $this->Project->save(array(
                                'id' => $projectId,
                                'project_manager_id' => $mainManagerId,
                                //also save customer
                                'budget_customer_id' => $data['budget_customer_id']
                            ));

                            //delete all other records not in trueList
                            $this->ProjectEmployeeManager->deleteAll(array(
                                'ProjectEmployeeManager.activity_id' => $this->Activity->id,
                                'ProjectEmployeeManager.project_id' => $projectId,
                                'NOT' => array('ProjectEmployeeManager.project_manager_id' => $trueList),
                            ), false);
                        }


                        $ProfitRefer = ClassRegistry::init('ActivityProfitRefer');
                        $saved = Set::combine($ProfitRefer->find('all', array(
                                            'conditions' => array('activity_id' => $this->Activity->id, 'type' => 0), 'recursive' => -1))
                                        , '{n}.ActivityProfitRefer.profit_center_id', '{n}.ActivityProfitRefer');

                        foreach ($accessibleProfit as $id) {
                            $data = array(
                                'activity_id' => $this->Activity->id,
                                'profit_center_id' => $id,
                                'type' => 0);
                            if (!empty($saved[$id])) {
                                unset($saved[$id]);
                                continue;
                            }
                            $ProfitRefer->create();
                            $ProfitRefer->save($data);
                        }

                        $last = $ProfitRefer->find('first', array(
                            'conditions' => array(
                                'activity_id' => $this->Activity->id,
                                'type' => 1
                            ),
                            'recursive' => -1));
                        if ($linkedProfit && !$last) {
                            $ProfitRefer->create();
                            $ProfitRefer->save(array(
                                'activity_id' => $this->Activity->id,
                                'profit_center_id' => $linkedProfit,
                                'type' => 1
                            ));
                        } elseif ($last) {
                            $saved[] = array('id' => $last['ActivityProfitRefer']['id']);
                        }
                        foreach ($saved as $_save) {
                            $ProfitRefer->delete($_save['id']);
                        }
                        $complete++;
                    }
                }
                $this->Session->setFlash(sprintf(__('The activities has been imported %s/%s.', true), $complete, count($import)));
                $this->redirect(array('action' => 'index'));
            } else {

                App::import('Vendor', 'ParsecsvLib', array('file' => 'parsecsv.lib.php'));
                $csv = new parseCSV();
                // export

                $header = array();
                $type = '';
                if ($this->data['Import']['type'] == 'Error' && !empty($this->data['Error']['export']))
                    $type = 'Error';
                if ($this->data['Import']['type'] == 'Create' && !empty($this->data['Create']['export']))
                    $type = 'Create';
                if ($this->data['Import']['type'] == 'Update' && !empty($this->data['Update']['export']))
                    $type = 'Update';
                if (!empty($type)) {
                    $header = array_keys($this->default);

                    $_listEmployee = array();
                    foreach($this->data[$type]['export'] as $key => $value){
                        $_listEmployee[$key] = $this->_utf8_encode_mix($value);
                    }
                    $csv->output($type . ".csv", $_listEmployee, $this->_translate_header($header), ",");
                } else {
                    $this->redirect(array('action' => 'index'));
                }
            }
        } else {
            $this->redirect(array('action' => 'index'));
        }

        exit;
    }
     /**
     * translate
     * author : Thach
     * @return array
     * @access private
     */
    private function _translate_header($input){
        $result = array();
        foreach($input as   $value){
            $result[] =  mb_convert_encoding(__($value, true),'UTF-16LE', 'UTF-8');
        }
        return $result;
    }
    private function _translate_array($input){
        $result = array();
        foreach($input as $key => $value){
            $result[mb_convert_encoding(__($key, true),'UTF-16LE', 'UTF-8')] =  mb_convert_encoding($value,'UTF-16LE', 'UTF-8');
        }
        return $result;
    }
    /**
     * Index
     *
     * @return void
     * @access public
     */
    function review_bak() {
        /**
         * Luu 1 cai session de kiem tra dang o fuction review or manager
         */
        $this->Session->write('ActedActiFunc', 'review');
        //ADD CODE BY VINGUYEN 07/05/2014
        if(isset($_POST['multiSort']))
        {
            $fieldSort=array();
            if($_POST['actSort']=='add')
            {
                if($_POST['flag']!=0)
                {
                    $fieldSort=$this->Session->read('sFieldSort');
                }
                else
                {
                    $fieldSort=array();
                    $this->Session->write('sFieldSort','');
                }
                $fieldSort[]=array('columnId'=>$_POST['value'],'sortAsc'=>1);
                $this->Session->write('sFieldSort',$fieldSort);
                echo json_encode($fieldSort);
                exit();
            }
            if($_POST['actSort']=='remove')
            {
                $fieldSort=$this->Session->read('sFieldSort');
                $fieldSort1=array();
                foreach($fieldSort as $array)
                {
                    if($array['columnId']!=$_POST['value'])
                    $fieldSort1[]=array('columnId'=>$array['columnId'],'sortAsc'=>$array['sortAsc']);
                }
                $this->Session->write('sFieldSort',$fieldSort1);
                echo json_encode($fieldSort1);
                exit();
            }
            if($_POST['actSort']=='update')
            {
                $fieldSort=$this->Session->read('sFieldSort');
                $count=count($fieldSort);
                for($i=0;$i<$count;$i++)
                {
                    if($fieldSort[$i]['columnId']==$_POST['value'])
                        $fieldSort[$i]['sortAsc']=round($_POST['type']);
                }
                $this->Session->write('sFieldSort',$fieldSort);
                echo json_encode($fieldSort);
                exit();
            }
        }
        //END ADD
        $employId = $this->Session->read('Auth.Employee.id');
        $employId = isset($employId) ? $employId : null;
        $path = $this->params['url']['url'];
        $this->loadModel('Employee');
        $last = $this->Employee->HistoryFilter->find('first', array(
            'recursive' => -1,
            'fields' => array(
                'id', 'params'
            ),
            'conditions' => array(
                'path' => $path,
                'employee_id' => $employId
            )
        ));
        $getTypes = $setTypes = array();
        if(!empty($last)){
            $getTypes = unserialize($last['HistoryFilter']['params']);
            $setTypes = !empty($getTypes['type']) ? $getTypes['type'][0] : 0;
        }
        $this->set(compact('setTypes'));
        /**
         * Get list budget customer
         */
        $this->loadModel('BudgetCustomer');
        $employeeName = $this->_getEmpoyee();
        $budgetCustomers = $this->BudgetCustomer->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $employeeName['company_id']),
            'fields' => array('id', 'name')
        ));
        $this->set(compact('budgetCustomers'));
        /**
         * Get list project manager
         */
        $this->loadModel('Employee');
        $companyEmployees = $this->Employee->CompanyEmployeeReference->find('list', array(
            'recursive' => -1,
            'fields' => array('employee_id', 'role_id'),
            'conditions' => array('CompanyEmployeeReference.company_id' => $employeeName['company_id'])));

        $projectEmployees = $this->Employee->find('all', array(
            'order' => 'last_name  ASC',
            'recursive' => -1,
            'conditions' => array(
                'id' => array_keys($companyEmployees)
            ),
            'fields' => array('first_name', 'last_name', 'id')));

        $projectManagers = array();
        foreach ($projectEmployees as $projectEmployee) {
            $projectManagers['project'][$projectEmployee['Employee']['id']] = $projectEmployee['Employee']['first_name'] . ' ' . $projectEmployee['Employee']['last_name'];
            foreach (array('pm' => array(3,2), 'tech' =>array(3,5)) as $key => $role) {
                if (in_array($companyEmployees[$projectEmployee['Employee']['id']], $role)) {
                    $projectManagers[$key][$projectEmployee['Employee']['id']] = $projectManagers['project'][$projectEmployee['Employee']['id']];
                }
            }
        }
        $projectManagers = !empty($projectManagers['pm']) ? $projectManagers['pm'] : array();
        $this->set('projectManagers', $projectManagers);
        $this->_parseNew();
        $this->_parseParams();
        Configure::write('debug', 2);
        $this->set('canModified', $this->_checkActivityRole());
    }

    function review() {
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        $this->loadModels('ActivityColumn');
        /**
         * Luu 1 cai session de kiem tra dang o fuction review or manager
         */
        $this->Session->write('ActedActiFunc', 'review');
        if(isset($_POST['multiSort'])){
            $fieldSort=array();
            if($_POST['actSort']=='add'){
                if($_POST['flag']!=0){
                    $fieldSort=$this->Session->read('sFieldSort');
                } else {
                    $fieldSort=array();
                    $this->Session->write('sFieldSort','');
                }
                $fieldSort[]=array('columnId'=>$_POST['value'],'sortAsc'=>1);
                $this->Session->write('sFieldSort',$fieldSort);
                echo json_encode($fieldSort);
                exit();
            }
            if($_POST['actSort']=='remove'){
                $fieldSort=$this->Session->read('sFieldSort');
                $fieldSort1=array();
                foreach($fieldSort as $array){
                    if($array['columnId']!=$_POST['value'])
                    $fieldSort1[]=array('columnId'=>$array['columnId'],'sortAsc'=>$array['sortAsc']);
                }
                $this->Session->write('sFieldSort',$fieldSort1);
                echo json_encode($fieldSort1);
                exit();
            }
            if($_POST['actSort']=='update'){
                $fieldSort=$this->Session->read('sFieldSort');
                $count=count($fieldSort);
                for($i=0;$i<$count;$i++){
                    if($fieldSort[$i]['columnId']==$_POST['value'])
                        $fieldSort[$i]['sortAsc']=round($_POST['type']);
                }
                $this->Session->write('sFieldSort',$fieldSort);
                echo json_encode($fieldSort);
                exit();
            }
        }
        if (!($employeeName = $this->_getEmpoyee())) {
            $this->Session->setFlash(__('Your account do not have a company to management activity.', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }
        /**
         * Xu ly view va screen.
         */
        extract(array_merge(array(
                'actiReview' => null),
                $this->params['url']));
        if(!empty($actiReview)){
            $this->Session->write("App.actiReview", $actiReview);
        }else{
            $actiReview = $this->Session->read("App.actiReview");
            if($actiReview){
                $this->Session->write("App.actiReview", $actiReview);
            }else{
                $actiReview = 1;
                $this->Session->write("App.actiReview", 1);
            }

        }
		$bg_currency = $this->getCurrencyOfBudget();
        $activated = ($actiReview == 1) ? 1 : (($actiReview == 2) ? 0 : array(0, 1));
        if($actiReview == 4){
            $activated = 2;
        }
        $conditions = array('company_id' => $employeeName['company_id'], 'activated' => $activated);
        /**
         * Lay activity column
         */
        $activityColumn = $this->ActivityColumn->getOptions($employeeName['company_id']);
        $cacheColumn = !empty($activityColumn) ? Set::combine($activityColumn, '{s}.code', '{s}') : array();
        $activityColumn = $this->_sortColumnActivity($activityColumn, 1, $cacheColumn, array());
        /**
         * Lay all column trong activity column
         */
        $allActivityColumn = $this->ActivityColumn->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $employeeName['company_id']),
            'fields' => array('id', 'key')
        ));
        $allActivityColumn = $this->ActivityColumn->parseViewField($employeeName['company_id'], $allActivityColumn);
        $cacheColumn = !empty($allActivityColumn) ? Set::combine($allActivityColumn, '{s}.code', '{s}') : array();
        $allActivityColumn = $this->_sortColumnActivity($allActivityColumn, 1, $cacheColumn, array());
        /**
         * Lay tat ca cac activity theo dieu kien
         */
        $activities = $this->Activity->find("all", array(
            'recursive' => -1,
            "conditions" => $conditions
        ));
        $this->loadModels('BudgetCustomer', 'ProfitCenter', 'Project', 'Employee', 'ProjectEmployeeManager');
        /**
         * Lay ten cac project linked voi activity
         */
        $actiIds = !empty($activities) ? Set::classicExtract($activities, '{n}.Activity.id') : '';
        /**
         * Lay list profit center cua cong ty
         */
        $profitCenters = $this->ProfitCenter->generateTreeList(array(
            'company_id' => $employeeName['company_id']), null, null, '--');
        /**
         * Lay list family va sub-family cua cong ty
         */
        $mapFamilies = Set::combine($this->Activity->Family->find('all', array(
                            'recursive' => -1,
                            'conditions' => array('company_id' => $employeeName['company_id']))), '{n}.Family.id', '{n}.Family');
        $families = $subfamilies = array();
        foreach ($mapFamilies as $mapFamilie) {
            if ($mapFamilie['parent_id']) {
                $subfamilies[$mapFamilie['id']] = $mapFamilie['name'];
            } else {
                $families[$mapFamilie['id']] = $mapFamilie['name'];
            }
        }
        /**
         * Lay list budget customer cua cong ty
         */
        $budgetCustomers = $this->BudgetCustomer->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $employeeName['company_id']),
            'fields' => array('id', 'name')
        ));
        /**
         * Lay list project manager cua cong ty
         */
        $companyEmployees = $this->Employee->CompanyEmployeeReference->find('list', array(
            'recursive' => -1,
            'fields' => array('employee_id', 'role_id'),
            'conditions' => array('CompanyEmployeeReference.company_id' => $employeeName['company_id'])));

        $projectEmployees = $this->Employee->find('all', array(
            'order' => 'last_name  ASC',
            'recursive' => -1,
            'conditions' => array(
                'id' => array_keys($companyEmployees)
            ),
            'fields' => array('first_name', 'last_name', 'id')));

        $projectManagers = array();
        foreach ($projectEmployees as $projectEmployee) {
            $projectManagers['project'][$projectEmployee['Employee']['id']] = $projectEmployee['Employee']['first_name'] . ' ' . $projectEmployee['Employee']['last_name'];
            foreach (array('pm' => array(3,2), 'tech' =>array(3,5)) as $key => $role) {
                if (in_array($companyEmployees[$projectEmployee['Employee']['id']], $role)) {
                    $projectManagers[$key][$projectEmployee['Employee']['id']] = $projectManagers['project'][$projectEmployee['Employee']['id']];
                }
            }
        }
        $projectManagers = !empty($projectManagers['pm']) ? $projectManagers['pm'] : array();
        /**
         * Project manager
         */
        $listManger = $this->ProjectEmployeeManager->find('all', array(
            'recursive' => -1,
            'fields' => array(
                    'id',
                    'project_manager_id',
                    'is_backup',
                    'activity_id'
            ),
            'conditions' => array('activity_id' => $actiIds, 'is_profit_center' => 0)
        ));
        $listManger = !empty($listManger) ? Set::combine($listManger,'{n}.ProjectEmployeeManager.project_manager_id','{n}.ProjectEmployeeManager.is_backup','{n}.ProjectEmployeeManager.activity_id') : array();
        /**
         * canModified
         */
        $canModified = $this->_checkActivityRole();
        /*
        * Default project screen
         */
        $screenDefaults = ClassRegistry::init('Menu')->find('first', array(
            'recursive' => -1,
            'conditions' => array('model' => 'project', 'company_id' => $employeeName['company_id'], 'default_screen' => 1)
        ));
        $defaultProjectScreen = array(
            'controller' => 'projects',
            'action' => 'edit'
        );
        if( !empty($screenDefaults) ){
            $defaultProjectScreen['controller'] = $screenDefaults['Menu']['controllers'];
            $defaultProjectScreen['action'] = $screenDefaults['Menu']['functions'];
        }
        $this->set('defaultProjectScreen', $defaultProjectScreen);
        $this->_parseParams();
        // get history
        $this->loadModels('HistoryFilter');
        $history = $this->HistoryFilter->find('first', array(
            'conditions' => array(
                'path' => $this->params['url']['url'],
                'employee_id' => $employeeName['id']
            ),
            'fields' => array('params')
        ));
        if( !empty($history) ){
            $history = json_decode($history['HistoryFilter']['params'], true);
        } else {
            $history = array();
        }
        $this->set(compact('history', 'canModified', 'listManger', 'actiReview', 'employeeName', 'activityColumn', 'profitCenters', 'families', 'subfamilies', 'mapFamilies', 'budgetCustomers', 'projectManagers', 'activities', 'allActivityColumn', 'bg_currency'));
    }

    private function _utf8_encode_mix($input){
        $result = array();
        foreach($input as $key => $value){
            $result[mb_convert_encoding($key,'UTF-16LE', 'UTF-8')] =  mb_convert_encoding($value,'UTF-16LE', 'UTF-8');
        }
        return $result;
    }
    /**
     * Index
     *
     * @return void
     * @access public
     */
    function detail($id = null) {
        if (!($employeeName = $this->_getEmpoyee())) {
            $this->Session->setFlash(__('Your account do not have a company to management activity.', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }

        $this->loadModel('ActivityRequest');
        $activityName = $this->Activity->find('first', array('recursive' => -1, 'conditions' => array('id' => $id)));

        $this->loadModel('ActivityTask');
        $activityTasks = $this->ActivityTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('ActivityTask.activity_id' => $id),
            'fields' => array('id')
        ));

        $_taskId = array();
        if(!empty($activityTasks)){
            $activityTasks = Set::combine($activityTasks, '{n}.ActivityTask.id', '{n}.ActivityTask');
            foreach($activityTasks as $activityTask){
                $_taskId[] = $activityTask['id'];
            }
        }
        if ($activityName) {
            if(!empty($this->params['url']['start']) && !empty($this->params['url']['end'])){
                $_start = strtotime(@$this->params['url']['start']);
                $_end = strtotime(@$this->params['url']['end']);
                $_taskId = !empty($_taskId) ? $_taskId : '';
                $datas = $this->ActivityRequest->find('all', array('recursive' => -1, 'fields' => array('id', 'employee_id', 'date', 'value', 'status'),
                'conditions' => array(
                    // 'status' => array(0, 1, 2),
                    'OR' => array(
                        'activity_id' => $id,
                        'task_id' => $_taskId
                    ),
                    'date BETWEEN ? AND ?' => array($_start, $_end),
                    'NOT' => array('value' => 0))));
                $this->set(compact('_start', '_end'));
            } else {
                $_taskId = !empty($_taskId) ? $_taskId : '';
                $datas = $this->ActivityRequest->find('all', array(
                    'recursive' => -1,
                    'fields' => array('id', 'employee_id', 'date', 'value', 'status', 'task_id'),
                    'conditions' => array(
                        // 'status' => array(0, 1, 2),
                        'OR' => array(
                            'activity_id' => $id,
                            'task_id' => $_taskId
                        ),
                        'NOT' => array('value' => 0)
                    )
                ));
            }
        }
        //if (empty($activityName) || empty($datas)) {
        //    $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
        //    $this->redirect(array('action' => 'review'));
        //}
        if (!empty($datas)) {
            $activities = array();
            $activityNotValidate = array();
            $months = array();
            foreach ($datas as $data) {
                $data = array_shift($data);
                list($y, $m) = explode('-', date('Y-n', $data['date']));
                if (!isset($activities[$data['employee_id']][$y . '-' . $m])) {
                    $activities[$data['employee_id']][$y . '-' . $m] = 0;
                }
                if( !isset($activityNotValidate[$data['employee_id']][$y . '-' . $m]) ){
                    $activityNotValidate[$data['employee_id']][$y . '-' . $m] = 0;
                }
                if($data['status'] == 2){
                    $activities[$data['employee_id']][$y . '-' . $m] += $data['value'];
                } else {
                    $activityNotValidate[$data['employee_id']][$y . '-' . $m] += $data['value'];
                }
                $months[$y][$m] = $m;
            }
            $_minYear = min(array_keys($months));
            $_minMonth = min($months[$_minYear]);
            $_maxYear = max(array_keys($months));
            $_maxMonth = max($months[$_maxYear]);

            $months = array_unique(array($_minYear, $_maxYear));

            $employees = $this->ActivityRequest->Employee->find('all', array('fields' => array(
                    'id', 'first_name', 'last_name'),
                    'conditions' => array('Employee.id' => array_keys($activities))
                    //'group' => array('Employee.first_name')
                    ));

            foreach($employees as $employee){
                $idProfitCenters[] = $employee['ProjectEmployeeProfitFunctionRefer'][0]['profit_center_id'];
            }
            $profitCenters = ClassRegistry::init('ProfitCenter')->find('list', array(
                    'conditions' => array('ProfitCenter.company_id' => $employeeName['company_id'], 'ProfitCenter.id' => $idProfitCenters),
                    'group' => array('ProfitCenter.name')));
        }
        $this->set(compact(array('_minYear', '_minMonth', '_maxYear', '_maxMonth', 'activities', 'employees', 'activityName', 'profitCenters', 'activityNotValidate')));
    }

    /**
     * Using display previous task in project task
     *
     * @return void
     * @access public
     */
    function details($id = null) {
        if (!($employeeName = $this->_getEmpoyee())) {
            $this->Session->setFlash(__('Your account do not have a company to management activity.', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }

        $this->loadModel('ActivityRequest');
        $activityName = $this->Activity->find('first', array('recursive' => -1, 'conditions' => array('id' => $id)));

        $this->loadModel('ActivityTask');
        $activityTasks = $this->ActivityTask->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'ActivityTask.activity_id' => $id,
                'ActivityTask.project_task_id' => null
            ),
            'fields' => array('id')
        ));
        $_taskId = array();
        if(!empty($activityTasks)){
            $activityTasks = Set::combine($activityTasks, '{n}.ActivityTask.id', '{n}.ActivityTask');
            foreach($activityTasks as $activityTask){
                $_taskId[] = $activityTask['id'];
            }
        }
        if ($activityName) {
            if(!empty($this->params['url']['start']) && !empty($this->params['url']['end'])){
                $_start = strtotime(@$this->params['url']['start']);
                $_end = strtotime(@$this->params['url']['end']);
                $_taskId = !empty($_taskId) ? $_taskId : '';
                $datas = $this->ActivityRequest->find('all', array('recursive' => -1, 'fields' => array('id', 'employee_id', 'date', 'value', 'status'),
                'conditions' => array(
                    'status' => array(0, 1, 2),
                    'OR' => array(
                            'activity_id' => $id,
                            'task_id' => $_taskId
                    ),
                    'date BETWEEN ? AND ?' => array($_start, $_end),
                    'NOT' => array('value' => 0))));
                $this->set(compact('_start', '_end'));
            } else {
                $_taskId = !empty($_taskId) ? $_taskId : '';
                $datas = $this->ActivityRequest->find('all', array(
                    'recursive' => -1,
                    'fields' => array('id', 'employee_id', 'date', 'value', 'status', 'task_id'),
                    'conditions' => array(
                        'status' => array(0, 1, 2),
                        'OR' => array(
                            'activity_id' => $id,
                            'task_id' => $_taskId
                        ),
                        'NOT' => array('value' => 0)
                    )
                ));
            }
        }
        if (!empty($datas)) {
            $activities = array();
            $activityNotValidate = array();
            $months = array();
            foreach ($datas as $data) {
                $data = array_shift($data);
                list($y, $m) = explode('-', date('Y-n', $data['date']));
                if (!isset($activities[$data['employee_id']][$y . '-' . $m])) {
                    $activities[$data['employee_id']][$y . '-' . $m] = 0;
                    $activityNotValidate[$data['employee_id']][$y . '-' . $m] = 0;
                }
                if($data['status'] == 2){
                    $activities[$data['employee_id']][$y . '-' . $m] += floatval($data['value']);
                }
                if($data['status'] == 0 || $data['status'] == 1){
                    $activityNotValidate[$data['employee_id']][$y . '-' . $m] += floatval($data['value']);
                }
                $months[$y][$m] = $m;
            }
            $_minYear = min(array_keys($months));
            $_minMonth = min($months[$_minYear]);
            $_maxYear = max(array_keys($months));
            $_maxMonth = max($months[$_maxYear]);

            $months = array_unique(array($_minYear, $_maxYear));

            $employees = $this->ActivityRequest->Employee->find('all', array('fields' => array(
                    'id', 'first_name', 'last_name'),
                    'conditions' => array('Employee.id' => array_keys($activities))
                    //'group' => array('Employee.first_name')
                    ));

            foreach($employees as $employee){
                $idProfitCenters[] = $employee['ProjectEmployeeProfitFunctionRefer'][0]['profit_center_id'];
            }
            $profitCenters = ClassRegistry::init('ProfitCenter')->find('list', array(
                    'conditions' => array('ProfitCenter.company_id' => $employeeName['company_id'], 'ProfitCenter.id' => $idProfitCenters),
                    'group' => array('ProfitCenter.name')));
        }
        $this->set(compact(array('_minYear', '_minMonth', '_maxYear', '_maxMonth', 'activities', 'employees', 'activityName', 'profitCenters', 'activityNotValidate')));
    }

     /**
     * Using display previous task in project task
     *
     * @return void
     * @access public
     */
    function detail_activity($id = null) {
        if (!($employeeName = $this->_getEmpoyee())) {
            $this->Session->setFlash(__('Your account do not have a company to management activity.', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }

        $this->loadModel('ActivityRequest');
        $activityName = $this->Activity->find('first', array('recursive' => -1, 'conditions' => array('id' => $id)));

        $this->loadModel('ActivityTask');
        $activityTasks = $this->ActivityTask->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'ActivityTask.activity_id' => $id,
                'ActivityTask.project_task_id' => null
            ),
            'fields' => array('id')
        ));
        $_taskId = array();
        if(!empty($activityTasks)){
            $activityTasks = Set::combine($activityTasks, '{n}.ActivityTask.id', '{n}.ActivityTask');
            foreach($activityTasks as $activityTask){
                $_taskId[] = $activityTask['id'];
            }
        }
        if ($activityName) {
            if(!empty($this->params['url']['start']) && !empty($this->params['url']['end'])){
                $_start = strtotime(@$this->params['url']['start']);
                $_end = strtotime(@$this->params['url']['end']);
                $_taskId = !empty($_taskId) ? $_taskId : '';
                $datas = $this->ActivityRequest->find('all', array('recursive' => -1, 'fields' => array('id', 'employee_id', 'date', 'value', 'status'),
                'conditions' => array(
                    'status' => array(0, 1, 2),
                    'OR' => array(
                            'activity_id' => $id
                    ),
                    'date BETWEEN ? AND ?' => array($_start, $_end),
                    'NOT' => array('value' => 0))));
                $this->set(compact('_start', '_end'));
            } else {
                $_taskId = !empty($_taskId) ? $_taskId : '';
                $datas = $this->ActivityRequest->find('all', array(
                    'recursive' => -1,
                    'fields' => array('id', 'employee_id', 'date', 'value', 'status'),
                    'conditions' => array(
                        'status' => array(0, 1, 2),
                        'OR' => array(
                            'activity_id' => $id
                        ),
                        'NOT' => array('value' => 0)
                    )
                ));
            }
        }
        if (!empty($datas)) {
            $activities = array();
            $activityNotValidate = array();
            $months = array();
            foreach ($datas as $data) {
                $data = array_shift($data);
                list($y, $m) = explode('-', date('Y-n', $data['date']));
                if (!isset($activities[$data['employee_id']][$y . '-' . $m])) {
                    $activities[$data['employee_id']][$y . '-' . $m] = 0;
                    $activityNotValidate[$data['employee_id']][$y . '-' . $m] = 0;
                }
                if($data['status'] == 2){
                    $activities[$data['employee_id']][$y . '-' . $m] += floatval($data['value']);
                }
                if($data['status'] == 0 || $data['status'] == 1){
                    $activityNotValidate[$data['employee_id']][$y . '-' . $m] += floatval($data['value']);
                }
                $months[$y][$m] = $m;
            }
            $_minYear = min(array_keys($months));
            $_minMonth = min($months[$_minYear]);
            $_maxYear = max(array_keys($months));
            $_maxMonth = max($months[$_maxYear]);

            $months = array_unique(array($_minYear, $_maxYear));

            $employees = $this->ActivityRequest->Employee->find('all', array('fields' => array(
                    'id', 'first_name', 'last_name'),
                    'conditions' => array('Employee.id' => array_keys($activities))
                    //'group' => array('Employee.first_name')
                    ));

            foreach($employees as $employee){
                $idProfitCenters[] = $employee['ProjectEmployeeProfitFunctionRefer'][0]['profit_center_id'];
            }
            $profitCenters = ClassRegistry::init('ProfitCenter')->find('list', array(
                    'conditions' => array('ProfitCenter.company_id' => $employeeName['company_id'], 'ProfitCenter.id' => $idProfitCenters),
                    'group' => array('ProfitCenter.name')));
        }
        $this->set(compact(array('_minYear', '_minMonth', '_maxYear', '_maxMonth', 'activities', 'employees', 'activityName', 'profitCenters', 'activityNotValidate')));
    }

    /**
     * Index
     *
     * @return void
     * @access public
     */
    function export() {
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        if (empty($this->data['Export']['list'])) {
            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            $this->redirect(array('action' => 'review'));
        }

        $this->_parseNew();
        /**
         * Get list budget customer
         */
        $this->loadModel('BudgetCustomer');
        $employeeName = $this->_getEmpoyee();
        $budgetCustomers = $this->BudgetCustomer->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $employeeName['company_id']),
            'fields' => array('id', 'name')
        ));
        $this->set(compact('budgetCustomers'));
        /**
         * Get list project manager
         */
        $this->loadModel('Employee');
        $companyEmployees = $this->Employee->CompanyEmployeeReference->find('list', array(
            'recursive' => -1,
            'fields' => array('employee_id', 'role_id'),
            'conditions' => array('CompanyEmployeeReference.company_id' => $employeeName['company_id'])));

        $projectEmployees = $this->Employee->find('all', array(
            'order' => 'last_name  ASC',
            'recursive' => -1,
            'conditions' => array(
                'id' => array_keys($companyEmployees)
            ),
            'fields' => array('first_name', 'last_name', 'id')));

        $projectManagers = array();
        foreach ($projectEmployees as $projectEmployee) {
            $projectManagers['project'][$projectEmployee['Employee']['id']] = $projectEmployee['Employee']['first_name'] . ' ' . $projectEmployee['Employee']['last_name'];
            foreach (array('pm' => array(3,2), 'tech' =>array(3,5)) as $key => $role) {
                if (in_array($companyEmployees[$projectEmployee['Employee']['id']], $role)) {
                    $projectManagers[$key][$projectEmployee['Employee']['id']] = $projectManagers['project'][$projectEmployee['Employee']['id']];
                }
            }
        }
        $projectManagers = !empty($projectManagers['pm']) ? $projectManagers['pm'] : array();
        $this->set('projectManagers', $projectManagers);


        $this->viewVars['activities'] = Set::combine($this->viewVars['activities'], '{n}.Activity.id', '{n}');
        if (!empty($this->viewVars['list'])) {
            $data = array_flip($this->viewVars['list']);
            foreach ($data as $id => $k) {
                if (!isset($this->viewVars['activities'][$id])) {
                    unset($data[$id]);
                    unset($this->viewVars['activities'][$id]);
                    continue;
                }
                $data[$id] = $this->viewVars['activities'][$id];
            }
            $this->viewVars['activities'] = $data;
            unset($data);
        }
        $this->layout = '';
    }
    public function getPersonalizedViews($status = null,$notAjax=false,$idView=0){

        $userViews = array();
        if(!empty($status)){
            $conditions = array();
            switch($status){
                case 1: {
                    $conditions = array(
                        'UserStatusViewActivity.activated' => 1,
                        //'UserStatusViewActivity.employee_id' => $this->employee_info['Employee']['id']
                    ); break;
                }
                case 2: {
                    $conditions = array(
                        'UserStatusViewActivity.not_activated' => 1,
                        //'UserStatusViewActivity.employee_id' => $this->employee_info['Employee']['id']
                    ); break;
                }
                default: {
                    $conditions = array(
                        'UserStatusViewActivity.activated_and_not_activated' => 1,
                        //'UserStatusViewActivity.employee_id' => $this->employee_info['Employee']['id']
                    ); break;
                }
            }
            if ($this->is_sas)
                $userViews = ClassRegistry::init('UserView')->find('list', array(
                    'recursive' => -1,
                    'fields' => array('UserView.id', 'UserView.name'),
                    'order' => 'UserView.name ASC',
                    'conditions'=>array('model'=>'activity')));
            else {
                if(!empty($conditions)){
                    $userViews = ClassRegistry::init('UserView')->find('list', array(
                        'recursive' => 0,
                        'fields' => array('UserView.id', 'UserView.name'),
                        'order' => 'UserView.name ASC',
                        'group' => 'UserView.id',
                        'conditions' => array(
                            'UserView.model'=>'activity',
                            'OR' => array(
                                'UserView.employee_id' => $this->employee_info['Employee']['id'],
                                array(
                                    'UserView.company_id' => $this->employee_info["Company"]["id"],
                                    'UserView.public' => true
                                )
                            ),
                            $conditions
                        )
                    ));
                }
            }
        }
        $str=$notAjax?'':'<option value=0>--Select--</option>';
        if(!empty($userViews))
        {
        foreach($userViews as $id=>$val)
        {
            $selected='';
            if($idView==$id)
            {
                $selected="selected='selected'";
            }
            $str.="<option value=".$id." ".$selected.">".$val."</option>";
        }
        }
        else
        {
            $str.="<option value=-2>--Predefined--</option>";
        }
        if($notAjax)
        {
            return $str;
        }
        else
        {
            $this->layout = false;
            echo json_encode($str);
            exit;
        }
    }
    /**
     * Index
     *
     * @return void
     * @access public
     */
    protected function _parse() {
        if (!($employeeName = $this->_getEmpoyee())) {
            $this->Session->setFlash(__('Your account do not have a company to management activity.', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }
        $company_id = !empty($employeeName['company_id']) ? $employeeName['company_id'] : 0;
        // filter follow category
        extract(array_merge(array(
                'activities_activated' => null),
                $this->params['url']));
        if(!empty($activities_activated)){ // not empty cate in the url
            $this->Session->write("App.activities_activated", $activities_activated);
        }else{ // empty cate in url
            $activities_activated = $this->Session->read("App.activities_activated");
            if($activities_activated){
                // do nothing
                $this->Session->write("App.activities_activated", $activities_activated);
            }else{
                $activities_activated = 1;
                $this->Session->write("App.activities_activated", 1);
            }

        }
        // set cate
        $this->set('activities_activated', $this->Session->read("App.activities_activated"));
        /**
         * Lay data budget
         */
        $this->loadModel('TmpStaffingSystem');
        $dataSystems = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => array('employee'),
                'NOT' => array(
                    'activity_id' => 0,
                    'model_id' => 999999999
                ),
                'company_id' => $company_id
            ),
            'fields' => array('activity_id', 'model_id', 'estimated')
        ));
        $assignEmployees = array();
        if(!empty($dataSystems)){
            foreach($dataSystems as $dataSystem){
                $dx = $dataSystem['TmpStaffingSystem'];
                if(!isset($assignEmployees[$dx['activity_id']])){
                    $assignEmployees[$dx['activity_id']] = 0;
                }
                $assignEmployees[$dx['activity_id']] += $dx['estimated'];
            }
        }
        $_dataSystems = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => array('profit_center'),
                'NOT' => array(
                    'activity_id' => 0,
                    'model_id' => 999999999
                ),
                'company_id' => $company_id
            ),
            'fields' => array('activity_id', 'model_id', 'estimated')
        ));
        $assignProfitCenters = array();
        if(!empty($_dataSystems)){
            foreach($_dataSystems as $_dataSystem){
                $dx = $_dataSystem['TmpStaffingSystem'];
                if(!isset($assignProfitCenters[$dx['activity_id']])){
                    $assignProfitCenters[$dx['activity_id']] = 0;
                }
                $assignProfitCenters[$dx['activity_id']] += $dx['estimated'];
            }
        }
        $this->set(compact('assignEmployees', 'assignProfitCenters'));
        $this->Activity->cacheQueries = true;
        $this->Activity->Behaviors->attach('Containable');

        $conditions = array('Activity.id' => null);
        if (!empty($this->data['Export']['list'])) {
            $list = array_filter(explode(',', $this->data['Export']['list']));
            if ($list) {
                $conditions = array('Activity.id' => $list);
            }
        } else {
            $conditions = array('company_id' => $employeeName['company_id']);
        }
        $activated = ($activities_activated == 1) ? 1 : (($activities_activated == 2) ? 0 : array(0, 1));
        $conditions = array_merge($conditions, array('activated' => $activated));
        $activities = $this->Activity->find("all", array(
            'contain' => array(),
            "conditions" => $conditions));
        $activityColumn = ClassRegistry::init('ActivityColumn')->getOptions($employeeName['company_id']);

        $this->loadModel('ActivityRequest');
        $this->loadModel('ActivityRequestConfirm');
        $employees = $sumEmployees = $sumActivities = array();
        $datas = $this->ActivityRequest->find(
            'all',
            array(
                'recursive' => -1,
                'fields' => array(
                    'id',
                    'employee_id',
                    'activity_id',
                    'SUM(value) as value'
                ),
            'group' => array('employee_id', 'activity_id'),
            'conditions' => array(
                'status' => 2,
                'company_id' => $employeeName['company_id'],
                'NOT' => array('value' => 0))
            )
        );

        $_datas = $this->ActivityRequest->find(
            'all',
            array(
                'recursive' => -1,
                'fields' => array(
                    'id',
                    'employee_id',
                    'task_id',
                    'SUM(value) as value'
                ),
            'group' => array('employee_id', 'task_id'),
            'conditions' => array(
                'status' => 2,
                'activity_id' => 0,
                'company_id' => $employeeName['company_id'],
                'NOT' => array('value' => 0))
            )
        );
        $this->loadModel('ActivityTask');
        $activityTasks = $this->ActivityTask->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'activity_id')
        ));
        $_sumActivitys = $_sumEmployees = array();
        foreach($_datas as $_data){
            foreach($activityTasks as $activityTask){
                if($_data['ActivityRequest']['task_id'] == $activityTask['ActivityTask']['id']){
                    $_sumActivitys[$activityTask['ActivityTask']['activity_id']][] = $_data[0]['value'];
                }
            }
            if (!isset($_sumEmployees[$_data['ActivityRequest']['task_id']][$_data['ActivityRequest']['employee_id']])) {
                $_sumEmployees[$_data['ActivityRequest']['task_id']][$_data['ActivityRequest']['employee_id']] = 0;
            }
            $_sumEmployees[$_data['ActivityRequest']['task_id']][$_data['ActivityRequest']['employee_id']] += $_data[0]['value'];
        }
        $dataFromEmployees = array();
        foreach($activityTasks as $activityTask){
            foreach($_sumEmployees as $id => $_sumEmployee){
                if($activityTask['ActivityTask']['id'] == $id){
                    $dataFromEmployees[$activityTask['ActivityTask']['activity_id']][] = $_sumEmployee;
                }
            }
        }
        $rDatas = array();
        if(!empty($dataFromEmployees)){
            foreach($dataFromEmployees as $id => $dataFromEmployee){
                foreach($dataFromEmployee as $values){
                    foreach($values as $employ => $value){
                        if(!isset($rDatas[$id][$employ])){
                            $rDatas[$id][$employ] = 0;
                        }
                        $rDatas[$id][$employ] += $value;
                    }
                }
            }
        }
        foreach($_sumActivitys as $k => $_sumActivity){
            $_sumActivitys[$k] = array_sum($_sumActivitys[$k]);
        }
        foreach ($datas as $data) {
            $dx = $data['ActivityRequest'];
            $data = $data['0']['value'];
            if (!isset($sumActivities[$dx['activity_id']])) {
                $sumActivities[$dx['activity_id']] = 0;
            }
            $sumActivities[$dx['activity_id']] += $data;
            if (!isset($sumEmployees[$dx['activity_id']][$dx['employee_id']])) {
                $sumEmployees[$dx['activity_id']][$dx['employee_id']] = 0;
            }
            $sumEmployees[$dx['activity_id']][$dx['employee_id']] += $data;
            $employees[$dx['employee_id']] = $dx['employee_id'];
        }
        $_dataFromEmployees = array();
        if(!empty($rDatas)){
            foreach($rDatas as $id => $rData){
                if(in_array($id, array_keys($sumEmployees))){

                } else {
                    $sumEmployees[$id] = $rData;
                    unset($rDatas[$id]);
                }
            }
        }
        $sumEmployGroups = array();
        if(!empty($sumEmployees)){
            unset($sumEmployees[0]);
            $sumEmployGroups[0] = $sumEmployees;
        }
        if(!empty($rDatas)){
            $sumEmployGroups[1] = $rDatas;
        }
        $sumEmployees = array();
        if(!empty($sumEmployGroups)){
            foreach($sumEmployGroups as $key => $sumEmployGroup){
                foreach($sumEmployGroup as $acId => $values){
                    foreach($values as $employs => $value){
                        if(!isset($sumEmployees[$acId][$employs])){
                            $sumEmployees[$acId][$employs] = 0;
                        }
                        $sumEmployees[$acId][$employs] += $value;
                    }
                }
            }
        }
        $previousTask = !empty($sumActivities) ? $sumActivities : array();
        if(!empty($sumActivities)){
            foreach($sumActivities as $id => $sumActivitie){
                $groupId[] = $id;
                if(!empty($_sumActivitys)){
                    foreach($_sumActivitys as $key => $_sumActivity){
                        if($id == $key){
                            $sumActivities[$id] = $sumActivitie + $_sumActivity;
                        }
                    }
                }
            }
        }
        if(!empty($_sumActivitys)){
            foreach($_sumActivitys as $key => $_sumActivity){
                if(in_array($key, $groupId)){
                    //do nothing
                } else {
                    $sumActivities[$key] = $_sumActivity;
                }
            }
        }
        //@Huupc: caculate workload, overload of project task and activity task.
        //@Huupc: Prject Task Linked Activity : PMS = YES
        $activityLinkeds = $this->Activity->find('list', array(
                'recursive' => -1,
                'conditions' => array('NOT' => array('project' => null)),
                'fields' => array('id', 'project')
            ));
        $this->loadModel('ProjectTask');
        $projectTasks = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $activityLinkeds),
                'fields' => array('id', 'parent_id', 'project_id', 'estimated', 'overload')
            ));
        $projectTasks = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask') : array();
        $parentIds = !empty($projectTasks) ? array_unique(Set::classicExtract($projectTasks, '{n}.parent_id')) : array();
        foreach($projectTasks as $key => $projectTask){
            if(in_array($key, $parentIds)){
                unset($projectTasks[$key]);
            }
        }

        $sumWorload = $sumOverload = array();
        foreach($projectTasks as $projectTask){
            $dx = $projectTask['project_id'];
            $dataEstimated = $projectTask['estimated'];
            $dataOverload = $projectTask['overload'];
            if (!isset($sumWorload[$dx])) {
                $sumWorload[$dx] = 0;
            }
            $sumWorload[$dx] += $dataEstimated;

            if (!isset($sumOverload[$dx])) {
                $sumOverload[$dx] = 0;
            }
            $sumOverload[$dx] += $dataOverload;
        }
        $dataFromProjectTasks = array();
        foreach($activityLinkeds as $id => $acti){
            $workload = isset($sumWorload[$acti]) ? $sumWorload[$acti] : 0;
            $previous = isset($previousTask[$id]) ? $previousTask[$id] : 0;
            $overload = isset($sumOverload[$acti]) ? $sumOverload[$acti]: 0;
            $consumed = isset($sumActivities[$id]) ? $sumActivities[$id] : 0;
            $dataFromProjectTasks[$id]['workload'] = $workload + $previous;
            $dataFromProjectTasks[$id]['overload'] = $overload;
            $dataFromProjectTasks[$id]['consumed'] = $consumed;
            if(($workload + $previous + $overload) == 0){
                $dataFromProjectTasks[$id]['completed'] = 0;
            } else {
                $com = round(($consumed*100)/($workload + $previous + $overload), 2);
                if($com > 100){
                    $dataFromProjectTasks[$id]['completed'] = 100;
                } else {
                    $dataFromProjectTasks[$id]['completed'] = $com;
                }
            }
            $rem = ($workload + $previous + $overload) - $consumed;
            if($rem < 0) {
                $dataFromProjectTasks[$id]['remain'] = 0;
            } else {
                $dataFromProjectTasks[$id]['remain'] = $rem;
            }
        }
        //@Huupc: End Prject Task Linked Activity : PMS = YES
        //@Huupc: Activity Task Not Linked Project : PMS = NO
        $taskNotLinkeds = $this->ActivityTask->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_task_id' => null),
                'fields' => array('id', 'parent_id', 'activity_id', 'estimated', 'overload')
            ));
        $taskNotLinkeds = !empty($taskNotLinkeds) ? Set::combine($taskNotLinkeds, '{n}.ActivityTask.id', '{n}.ActivityTask') : array();
        $_parentIds = !empty($taskNotLinkeds) ? array_unique(Set::classicExtract($taskNotLinkeds, '{n}.parent_id')) : array();
        foreach($taskNotLinkeds as $key => $taskNotLinked){
            if(in_array($key, $_parentIds)){
                unset($taskNotLinkeds[$key]);
            }
        }
        $_sumWorload = $_sumOverload = array();
        foreach($taskNotLinkeds as $taskNotLinked){
            $_dx = $taskNotLinked['activity_id'];
            $_dataEstimated = $taskNotLinked['estimated'];
            $_dataOverload = $taskNotLinked['overload'];
            if (!isset($_sumWorload[$_dx])) {
                $_sumWorload[$_dx] = 0;
            }
            $_sumWorload[$_dx] += $_dataEstimated;

            if (!isset($_sumOverload[$_dx])) {
                $_sumOverload[$_dx] = 0;
            }
            $_sumOverload[$_dx] += $_dataOverload;
        }
        $taskNotLinkeds = !empty($taskNotLinkeds) ? array_unique(Set::classicExtract($taskNotLinkeds, '{n}.activity_id')) : array();
        $dataFromActivityTasks = array();
        foreach($taskNotLinkeds as $id){
            $_workload = isset($_sumWorload[$id]) ? $_sumWorload[$id] : 0;
            $_previous = isset($previousTask[$id]) ? $previousTask[$id] : 0;
            $_overload = isset($_sumOverload[$id]) ? $_sumOverload[$id]: 0;
            $_consumed = isset($sumActivities[$id]) ? $sumActivities[$id] : 0;
            $dataFromActivityTasks[$id]['workload'] = $_workload + $_previous;
            $dataFromActivityTasks[$id]['overload'] = $_overload;
            $dataFromActivityTasks[$id]['consumed'] = $_consumed;
            if(($_workload + $_previous + $_overload) == 0){
                $dataFromActivityTasks[$id]['completed'] = 0;
            } else {
                $_com = round(($_consumed*100)/($_workload + $_previous + $_overload), 2);
                if($_com > 100){
                    $dataFromActivityTasks[$id]['completed'] = 100;
                } else {
                    $dataFromActivityTasks[$id]['completed'] = $_com;
                }
            }
            $rems = ($_workload + $_previous + $_overload) - $_consumed;
            if($rems < 0){
                $dataFromActivityTasks[$id]['remain'] = 0;
            } else {
                $dataFromActivityTasks[$id]['remain'] = $rems;
            }
        }
        //@Huupc: End Activity Task Not Linked Project : PMS = NO
        $mapFamilies = Set::combine($this->Activity->Family->find('all', array(
                            'recursive' => -1,
                            'conditions' => array('company_id' => $employeeName['company_id']))), '{n}.Family.id', '{n}.Family');
        $families = $subfamilies = array();

        foreach ($mapFamilies as $mapFamilie) {
            if ($mapFamilie['parent_id']) {
                $subfamilies[$mapFamilie['id']] = $mapFamilie['name'];
            } else {
                $families[$mapFamilie['id']] = $mapFamilie['name'];
            }
        }
        $employees = Set::combine($this->ActivityRequest->Employee->find('all', array(
                            'fields' => array('id', 'tjm', 'actif'), 'recursive' => -1,
                            'conditions' => array('id' => $employees))), '{n}.Employee.id', '{n}.Employee');

        $list_activities = $this->Activity->find('list', array(
                'recursive' => -1,
                'conditions' => array('pms' => 0),
                'fields' => array('id', 'name')
            ));
        $list_short_names = $this->Activity->find('list', array(
                'recursive' => -1,
                'conditions' => array('pms' => 0),
                'fields' => array('id', 'short_name')
            ));
        $listProfitCenters = ClassRegistry::init('ProfitCenter')->find('list', array(
            'conditions' => array('company_id' => $employeeName['company_id']),
            'fields' => array('id', 'name')
        ));

        $list_employees = $this->Employee->CompanyEmployeeReference->find('all', array(
            'conditions' => array(
                'CompanyEmployeeReference.company_id' => $employeeName['company_id'],
                'NOT' => array('Employee.is_sas' => 1)
            ),
            'fields' => array('Employee.id', 'Employee.first_name', 'Employee.last_name')
        ));
        $list_employees = !empty($list_employees) ? Set::combine($list_employees, '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name')) : array();
        /**
         * Lay du lieu budget
         */
        $this->loadModel('ProjectBudgetSyn');
        $budgets = $this->ProjectBudgetSyn->find('all', array(
            'recursive' => -1
        ));
        $budgets = !empty($budgets) ? Set::combine($budgets, '{n}.ProjectBudgetSyn.activity_id', '{n}.ProjectBudgetSyn') : array();
        $this->set(compact('list_activities', 'list_short_names', 'listProfitCenters', 'dataFromProjectTasks', 'dataFromActivityTasks', 'list_employees', 'budgets'));
        $this->set(compact('list', 'families', 'subfamilies', 'activities', 'company_id', 'employeeName', 'employees', 'activityColumn', 'sumEmployees', 'sumActivities'));
    }

    /**
     * update Activity from Review
     *
     *
     **/
    public function update_review(){

        $result = false;
        $this->layout = false;

        if (!empty($this->data) && ($employeeName = $this->_getEmpoyee())) {
            if (!empty($this->data['id'])) {
                // fix bug save in review then change in management
                $data = array(
                    //'pms'       => (isset($this->data['pms']) && $this->data['pms'] == 'yes'),
                    'activated' => (isset($this->data['activated']) && $this->data['activated'] == 'yes'),
                    'actif'     => (isset($this->data['actif']) && $this->data['actif'] == 'yes'),
                );
                /** Kiem tra project manager
                 * List danh sach nhap vao: chi co 1 project manager primary con lai la backup
                 */
                $valid = true;
                $managerBackups = array();
                $projectLinked = $this->Activity->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('Activity.id' => $this->data['id']),
                    'fields' => array('project')
                ));
                $projectLinked = !empty($projectLinked['Activity']['project']) ? $projectLinked['Activity']['project'] : 0;
                $PrimaryManager = 0;
                if(!empty($this->data['project_manager_id']) && !empty($this->data['backupPM'])){
                    if(in_array(0, $this->data['backupPM'])){ // co 1 or nhieu manager primary
                        $checkPrimary = array_count_values($this->data['backupPM']);
                        if($checkPrimary[0] == 1 || (!empty($this->employee_info) &&  !empty($this->employee_info['Role']) && !empty($this->employee_info['Role']['name']) && $this->employee_info['Role']['name'] == 'admin')){
                            unset($this->data['project_manager_id']);
                            foreach($this->data['backupPM'] as $employee => $backup){
                                if($backup == 0){ // manager primary
                                    $this->data['project_manager_id'] = $employee;
                                    $PrimaryManager = $employee;
                                } else { // list manager backup
                                    $managerBackups[] = array(
                                        'project_id' => !empty($this->data['project']) ? $this->data['project'] : 0,
                                        'project_manager_id' => $employee,
                                        'is_backup' => 1
                                    );
                                }
                            }
                            // co 1 primary manager
                            $valid = true;
                        } else { // co 2 primary manager tro len
                            $this->Session->setFlash(__('Just need a main project management.', true), 'error');
                            $valid = false;
                        }
                    } else { // khong co manager primary nao
                        $this->Session->setFlash(__('There should be a main project management.', true), 'error');
                        $valid = false;
                    }
                    unset($this->data['project']);
                } else {
                    unset($this->data['project_manager_id']);
                    unset($this->data['project']);
                }
                $this->Activity->id = $this->data['id'];
                if($valid == true){
                    if ($this->Activity->save(array_diff_key(array_merge($this->data, $data), array('backupPM' => '')))){
                        unset($this->data['project_manager_id']);
                        $result = true;
                        if(!empty($projectLinked) && $projectLinked != 0){
                            $this->loadModel('Project');
                            $this->Project->id = $projectLinked;
                            if($this->Project->save(array('project_manager_id' => $PrimaryManager, 'budget_customer_id' => $this->data['budget_customer_id'], 'activated' => $data['activated']))){
                                $this->data['project_manager_id'][] = $PrimaryManager;
                            }
                        } else {
                            if(!empty($PrimaryManager) && $PrimaryManager != 0){
                                $this->data['project_manager_id'][] = $PrimaryManager;
                            }
                        }
                        $this->loadModel('ProjectEmployeeManager');
                        $managers = Set::combine($this->ProjectEmployeeManager->find('all', array(
                            'recursive' => -1,
                            'conditions' => array('activity_id' => $this->Activity->id, 'is_profit_center' => 0)
                        )), '{n}.ProjectEmployeeManager.project_manager_id', '{n}.ProjectEmployeeManager');
                        if (!empty($managerBackups)) {
                            foreach($managerBackups as $managerBackup){
                                $this->data['project_manager_id'][] = $managerBackup['project_manager_id'];
                                $data = array(
                                    'activity_id' => $this->Activity->id,
                                    'project_manager_id' => $managerBackup['project_manager_id'],
                                    'project_id' => $projectLinked,
                                    'is_backup' => $managerBackup['is_backup']
                                );
                                if (!empty($managers[$managerBackup['project_manager_id']])) {
                                    unset($managers[$managerBackup['project_manager_id']]);
                                    continue;
                                }
                                $this->ProjectEmployeeManager->create();
                                $this->ProjectEmployeeManager->save($data);
                            }
                        }
                        foreach ($managers as $manager) {
                            $this->ProjectEmployeeManager->delete($manager['id']);
                        }
                        // $this->Session->setFlash(__('The Activity has been saved.', true), 'success');
                    } else {
                        $this->Session->setFlash(__('The Activity could not be saved. Please, try again.', true), 'error');
                    }
                }
            }else{
                $this->Session->setFlash(__('The Activity Id is not legal.', true), 'error');
            }
        }else {
            $this->Session->setFlash(__('The Activity could not be saved. Please, try again.', true), 'error');
        }

        $this->set(compact('result'));
    }
    /**
     * update Activity from Activity Management
     *
     * @return void
     * @access public
     */
     public function update_all_activity_has_profit(){
        //$ProfitRefer = ClassRegistry::init('ActivityProfitRefer');
        $this->loadModel('ActivityProfitRefer');
        $ProfitRefer = $this->ActivityProfitRefer->find('all',array(
            'fields' => 'activity_id',
            'group' => 'activity_id',
            'recursive' => -1
        ));
        $ProfitRefer = Set::combine($ProfitRefer, '{n}.ActivityProfitRefer.activity_id','{n}.ActivityProfitRefer.activity_id');
        if($this->Activity->updateAll(array('Activity.allow_profit' => 1),array('Activity.id' => $ProfitRefer ))){
            echo 'finish';
            exit;
        }
     }
    /**
     * update Activity from Activity Management
     *
     * @return void
     * @access public
     */
    public function update() {
        $result = false;
        $this->layout = false;
        if (!empty($this->data) && ($employeeName = $this->_getEmpoyee())) {
			unset( $this->data['name']); // Email: Z0G 15/2/2019: Enhancements activity/view
            if (!empty($this->data['id'])) {
                $this->Activity->id = $this->data['id'];
            }else{
                $this->Activity->create();
            }
            // fix bug save in review then change in management
            $data = array(
                'pms'       => (isset($this->data['pms']) && $this->data['pms'] == 'yes'),
                'activated' => (isset($this->data['activated']) && $this->data['activated'] == 'arch') ? 2 : ((isset($this->data['activated']) && $this->data['activated'] == 'yes') ? 1 : 0),
                'actif'     => (isset($this->data['actif']) && $this->data['actif'] == 'yes'),
            );
            // $data = array();

            foreach (array('start_date', 'end_date') as $key) {
                if (!empty($this->data[$key])) {
                    $data[$key] = strtotime($this->Activity->convertTime($this->data[$key]));
                }
            }
            unset($this->data['id']);
            $this->data['company_id'] = $employeeName['company_id'];
            $ProfitRefer = ClassRegistry::init('ActivityProfitRefer');
            //$data['allow_profit'] = !(!empty($this->data['accessible_profit']) && is_array($this->data['accessible_profit']));
            $data['allow_profit'] = !empty($this->data['accessible_profit']);
            /**
             * Kiem tra project manager
             * List danh sach nhap vao: chi co 1 project manager primary con lai la backup
             */
            $valid = true;
            $managerBackups = array();
            $projectLinked = !empty($this->data['project']) ? $this->data['project'] : 0;
            $PrimaryManager = 0;
            if(!empty($this->data['project_manager_id']) && !empty($this->data['backupPM'])){
                if(in_array(0, $this->data['backupPM'])){ // co 1 or nhieu manager primary
                    $checkPrimary = array_count_values($this->data['backupPM']);
                    if( !empty($checkPrimary[0] ) || (!empty($this->employee_info) &&  !empty($this->employee_info['Role']) && !empty($this->employee_info['Role']['name']) && $this->employee_info['Role']['name'] == 'admin')){
                        unset($this->data['project_manager_id']);
                        foreach($this->data['backupPM'] as $employee => $backup){
                            if($backup == 0){ // manager primary
                                $this->data['project_manager_id'] = $employee;
                                $PrimaryManager = $employee;
                            } else { // list manager backup
                                $managerBackups[] = array(
                                    'project_id' => !empty($this->data['project']) ? $this->data['project'] : 0,
                                    'project_manager_id' => $employee,
                                    'is_backup' => 1
                                );
                            }
                        }
                        // co 1 primary manager
                        $valid = true;
                    } else { // co 2 primary manager tro len
                        $this->Session->setFlash(__('Just need a main project management.', true), 'error');
                        $valid = false;
                    }
                } else { // khong co manager primary nao
                    $this->Session->setFlash(__('There should be a main project management.', true), 'error');
                    $valid = false;
                }
                unset($this->data['project']);
            } else {
                unset($this->data['project_manager_id']);
                unset($this->data['project']);
            }
            if($valid == true){
                if ($this->Activity->save(array_diff_key(array_merge($this->data, $data), array('accessible_profit' => '', 'linked_profit' => '', 'backupPM' => '')))) {
                    unset($this->data['project_manager_id']);
                    $result = true;
                    if(!empty($projectLinked) && $projectLinked != 0){
                        $this->loadModel('Project');
                        $this->Project->id = $projectLinked;
                        // if($this->Project->save(array('project_manager_id' => $PrimaryManager, 'budget_customer_id' => $this->data['budget_customer_id'], 'activated' => $data['activated']))){
                        //     $this->data['project_manager_id'][] = $PrimaryManager;
                        // }
                    } else {
                        if(!empty($PrimaryManager) && $PrimaryManager != 0){
                            $this->data['project_manager_id'][] = $PrimaryManager;
                        }
                    }
                    $this->loadModel('ProjectEmployeeManager');
                    $managers = Set::combine($this->ProjectEmployeeManager->find('all', array(
                        'recursive' => -1,
                        'conditions' => array('activity_id' => $this->Activity->id)
                    )), '{n}.ProjectEmployeeManager.project_manager_id', '{n}.ProjectEmployeeManager');
                    if (!empty($managerBackups)) {
                        foreach($managerBackups as $managerBackup){
                            $this->data['project_manager_id'][] = $managerBackup['project_manager_id'];
                            $data = array(
                                'activity_id' => $this->Activity->id,
                                'project_manager_id' => $managerBackup['project_manager_id'],
                                'project_id' => $managerBackup['project_id'],
                                'is_backup' => $managerBackup['is_backup']
                            );
                            if (!empty($managers[$managerBackup['project_manager_id']])) {
                                unset($managers[$managerBackup['project_manager_id']]);
                                continue;
                            }
                            $this->ProjectEmployeeManager->create();
                            $this->ProjectEmployeeManager->save($data);
                        }
                    }
                    foreach ($managers as $manager) {
                        $this->ProjectEmployeeManager->delete($manager['id']);
                    }
                    $saved = Set::combine($ProfitRefer->find('all', array(
                                        'conditions' => array('activity_id' => $this->Activity->id, 'type' => 0), 'recursive' => -1))
                                    , '{n}.ActivityProfitRefer.profit_center_id', '{n}.ActivityProfitRefer');
                    if (!empty($this->data['accessible_profit'])) {
                        foreach ($this->data['accessible_profit'] as $id) {
                            $data = array(
                                'activity_id' => $this->Activity->id,
                                'profit_center_id' => $id,
                                'type' => 0);
                            if (!empty($saved[$id])) {
                                unset($saved[$id]);
                                continue;
                            }
                            $ProfitRefer->create();
                            $ProfitRefer->save($data);
                        }
                    }
                    $last = $ProfitRefer->find('first', array(
                        'conditions' => array(
                            'activity_id' => $this->Activity->id,
                            'type' => 1
                        ),
                        'recursive' => -1));
                    if (!empty($this->data['linked_profit'])) {
                        if(!empty($last) && $last['ActivityProfitRefer']['id']){
                            $ProfitRefer->id = $last['ActivityProfitRefer']['id'];
                            $ProfitRefer->save(array('profit_center_id' => $this->data['linked_profit']));
                        } else {
                            $ProfitRefer->create();
                            $ProfitRefer->save(array(
                                'activity_id' => $this->Activity->id,
                                'profit_center_id' => $this->data['linked_profit'],
                                'type' => 1
                            ));
                        }
                    } else {
                        if(!empty($last) && $last['ActivityProfitRefer']['id']){
                            $ProfitRefer->delete($last['ActivityProfitRefer']['id']);
                        }
                    }
                    foreach ($saved as $_save) {
                        $ProfitRefer->delete($_save['id']);
                    }
                    $this->_deleteCacheContextMenu();
                    //Log by QN on 2015/02/07
                    $activity = $this->Activity->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'id' => $this->Activity->id
                        )
                    ));
                    $this->writeLog($this->data, $this->employee_info, sprintf('Update activity `%s`', $activity['Activity']['name']));
                    // $this->Session->setFlash(__('The Activity has been saved.', true), 'success');
                } else {
                    $this->Session->setFlash(__('The Activity could not be saved. Please, try again.', true), 'error');
                }
            }
            $this->data['id'] = $this->Activity->id;
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }

    function export_list() {
        set_time_limit(0);
        if (empty($this->data['Export']['list'])) {
            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            $this->redirect(array('action' => 'review'));
        }
        if (!($employeeName = $this->_getEmpoyee())) {
            $this->Session->setFlash(__('Your account do not have a company to management activity.', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }
        $this->_parse();
        $datas = array_filter(explode(',', $this->data['Export']['list']));

        $this->Activity->cacheQueries = true;
        $this->Activity->Behaviors->attach('Containable');
        $activities = $this->Activity->find("all", array(
            'contain' => array('AccessibleProfit' => array('id', 'profit_center_id'), 'LinkedProfit' => array('id', 'profit_center_id')),
            "conditions" => array('Activity.id' => $datas)));

        $activityColumn = ClassRegistry::init('ActivityColumn')->getOptions($employeeName['company_id']);
        $profitCenters = ClassRegistry::init('ProfitCenter')->find('list', array(
            'conditions' => array(
                'company_id' => $employeeName['company_id']
                )));
        $mapFamilies = Set::combine($this->Activity->Family->find('all', array(
                            'recursive' => -1,
                            'conditions' => array('company_id' => $employeeName['company_id']))), '{n}.Family.id', '{n}.Family');
        $families = $subfamilies = array();
        foreach ($mapFamilies as $mapFamilie) {
            if ($mapFamilie['parent_id']) {
                $subfamilies[$mapFamilie['id']] = $mapFamilie['name'];
            } else {
                $families[$mapFamilie['id']] = $mapFamilie['name'];
            }
        }
        $activities = Set::combine($activities, '{n}.Activity.id', '{n}');
        $data = array_flip($datas);
        foreach ($data as $id => $k) {
            if (!isset($activities[$id])) {
                unset($data[$id]);
                unset($activities[$id]);
                continue;
            }
            $data[$id] = $activities[$id];
        }
        $activities = $data;
        unset($data);

        $this->set(compact('activities', 'employeeName', 'activityColumn', 'profitCenters', 'families', 'subfamilies', 'mapFamilies'));
        $this->layout = '';
    }
    //edit by Thach 2013/11/05
    function export_listplus() {
        set_time_limit(0);
        if (empty($this->data['Exportplus']['list'])) {
            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!($employeeName = $this->_getEmpoyee())) {
            $this->Session->setFlash(__('Your account do not have a company to management activity.', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }
        //$this->_parse();
        $datas = array_filter(explode(',', $this->data['Exportplus']['list']));
        $this->Activity->cacheQueries = true;
        $this->Activity->Behaviors->attach('Containable');
       // $activities = $this->Activity->find("all", array(
//            'contain' => array(
//                'AccessibleProfit' => array('id', 'profit_center_id'),
//                'LinkedProfit' => array('id', 'profit_center_id')),
//            "conditions" => array('Activity.id' => $datas)));
//
        $activitiesAll = $this->Activity->find("all", array(
            //@Huupc Change filter fields using display
            'fields' => array(
                'id',
                'name',
                'long_name',
                'short_name',
                'family_id',
                'subfamily_id',
                'budget_customer_id',
                'project_manager_id',
                'pms',
                'project',
                'activated',
                'code1',
                'code2',
                'code3',
                'c44',
                'start_date',
                'end_date',
                'import_code',
                'actif',
                'code4',
                'code5',
                'code6',
                'code7',
                'code8',
                'code9',
                'code10'
            ),
            "conditions" => array('Activity.id' => $datas),
            'recursive' => -1,
            ));
        //edit by Thach : tang toc do load
        $activityProfitRefer = $this->Activity->AccessibleProfit->find('all', array(
            'fields' => array(
                    'id',
                    'profit_center_id',
                    'activity_id'
            ),
            "conditions" => array(
                'activity_id' => $datas,
                'type' => 0
            ),
        ));
        $activityProfitRefer = Set::combine($activityProfitRefer,'{n}.AccessibleProfit.id','{n}.AccessibleProfit','{n}.AccessibleProfit.activity_id');

        $linkedProfitRefer = $this->Activity->LinkedProfit->find('all', array(
            'fields' => array(
                    'id',
                    'profit_center_id',
                    'activity_id'
            ),
            'conditions' => array(
                'activity_id' => $datas,
                'type' => 1
            )
        ));
        $linkedProfitRefer = Set::combine($linkedProfitRefer,'{n}.LinkedProfit.activity_id','{n}.LinkedProfit');
        //pr($linkedProfitRefer);die;
        $this->loadModel('ProjectEmployeeManager');
        $listActivityIds = !empty($activitiesAll) ? Set::classicExtract($activitiesAll, '{n}.Activity.id') : array();
        $listManger = $this->ProjectEmployeeManager->find('all', array(
            'recursive' => -1,
            'fields' => array(
                    'id',
                    'project_manager_id',
                    'is_backup',
                    'activity_id'
            ),
            'conditions' => array('activity_id' => $listActivityIds, 'is_profit_center' => 0)
        ));
        $listManger = !empty($listManger) ? Set::combine($listManger,'{n}.ProjectEmployeeManager.id','{n}.ProjectEmployeeManager','{n}.ProjectEmployeeManager.activity_id') : array();
        $activities = array();
        foreach($activitiesAll as $key_activities => $value_activities){
            //add AccessibleProfit neu co
            $AccessibleProfit = array();
            if(!empty($activityProfitRefer[$value_activities['Activity']['id']])){
                $_activityProfitRefer = array();
                foreach($activityProfitRefer[$value_activities['Activity']['id']] as $_key => $_value){
                    $_activityProfitRefer[] = $_value;
                }
                $value_activities['AccessibleProfit'] =$_activityProfitRefer;
            }else{
                $value_activities['AccessibleProfit'] = array();
            }
            //add LinkedProfit neu co
            if(!empty($linkedProfitRefer[$value_activities['Activity']['id']])){
                $_linkedProfitRefer = array();
                $value_activities['LinkedProfit'] = $linkedProfitRefer[$value_activities['Activity']['id']];
            }else{
                $value_activities['LinkedProfit'] = array();
            }
            $primaryManager = !empty($value_activities['Activity']['project_manager_id']) ? $value_activities['Activity']['project_manager_id'] : '';
            unset($value_activities['Activity']['project_manager_id']);
            //add manager neu co
            if(!empty($listManger[$value_activities['Activity']['id']])){
                $_manager = array();
                foreach($listManger[$value_activities['Activity']['id']] as $_key => $_value){
                    $_manager[] = $_value;
                }
                $value_activities['project_manager_id'] =$_manager;
            }else{
                $value_activities['project_manager_id'] = array();
            }
            if(!empty($primaryManager)){
                $value_activities['project_manager_id'][] = array(
                    'project_manager_id' => $primaryManager,
                    'is_backup' => 0
                );
            }
            $activities[] = $value_activities;
        }
        $activityColumn = ClassRegistry::init('ActivityColumn')->getOptions($employeeName['company_id']);
        $profitCenters = ClassRegistry::init('ProfitCenter')->find('list', array(
            'conditions' => array(
                'company_id' => $employeeName['company_id']
                )));
        $mapFamilies = Set::combine($this->Activity->Family->find('all', array(
                            'recursive' => -1,
                            'conditions' => array('company_id' => $employeeName['company_id']))), '{n}.Family.id', '{n}.Family');
        $families = $subfamilies = array();
        foreach ($mapFamilies as $mapFamilie) {
            if ($mapFamilie['parent_id']) {
                $subfamilies[$mapFamilie['id']] = $mapFamilie['name'];
            } else {
                $families[$mapFamilie['id']] = $mapFamilie['name'];
            }
        }
        /**
         * Get list budget customer
         */
        $this->loadModel('BudgetCustomer');
        $budgetCustomers = $this->BudgetCustomer->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $employeeName['company_id']),
            'fields' => array('id', 'name')
        ));
        /**
         * Get list project manager
         */
        /**
         * Get list project manager
         */
        $this->loadModel('Employee');
        $companyEmployees = $this->Employee->CompanyEmployeeReference->find('list', array(
            'recursive' => -1,
            'fields' => array('employee_id', 'role_id'),
            'conditions' => array('CompanyEmployeeReference.company_id' => $employeeName['company_id'])));

        $projectEmployees = $this->Employee->find('all', array(
            'order' => 'last_name  ASC',
            'recursive' => -1,
            'conditions' => array(
                'id' => array_keys($companyEmployees)
            ),
            'fields' => array('first_name', 'last_name', 'id')));

        $projectManagers = array();
        foreach ($projectEmployees as $projectEmployee) {
            $projectManagers['project'][$projectEmployee['Employee']['id']] = $projectEmployee['Employee']['first_name'] . ' ' . $projectEmployee['Employee']['last_name'];
            foreach (array('pm' => array(3,2), 'tech' =>array(3,5)) as $key => $role) {
                if (in_array($companyEmployees[$projectEmployee['Employee']['id']], $role)) {
                    $projectManagers[$key][$projectEmployee['Employee']['id']] = $projectManagers['project'][$projectEmployee['Employee']['id']];
                }
            }
        }
        $projectManagers = !empty($projectManagers['pm']) ? $projectManagers['pm'] : array();
        $projects = ClassRegistry::init('Project')->find('list', array('fields' => array('project_name')));
        //debug($activities); exit;
        $this->set('projectManagers', $projectManagers);
        $this->set(compact('activities', 'employeeName', 'activityColumn', 'profitCenters', 'families', 'subfamilies', 'mapFamilies', 'budgetCustomers', 'projects'));
        $this->layout = '';

    }
    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for activity', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $allowDeleteActivity = $this->_activityIsUsing($id);
        if($allowDeleteActivity == 'true'){
            $activity = $this->Activity->find('first', array(
                'recursive' => -1,
                'conditions' => array('id' => $id, 'company_id' => $this->employee_info['Company']['id'])
            ));
            if( empty($activity) ){
                $this->Session->setFlash(__('Activity not found', true), 'error');
                $this->redirect(array('action' => 'index'));
            }
            $this->loadModel('Project');
            $projectId = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('activity_id' => $id),
                'fields' => array('id')
            ));
            if ($this->Activity->delete($id)) {
                /**
                 * Xoa lien ket voi project
                 * Neu co project thi update activity_id = 0 cua cac model sau:
                 * - ProjectBudgetInternal
                 * - ProjectBudgetInternalDetail
                 * - ProjectBudgetExternal
                 * - ProjectBudgetSale
                 * - ProjectBudgetInvoice
                 * - ProjectBudgetSyn
                 * - ProjectEmployeeManager
                 */
                $this->loadModel('ProjectBudgetInternal');
                $this->loadModel('ProjectBudgetInternalDetail');
                $this->loadModel('ProjectBudgetExternal');
                $this->loadModel('ProjectBudgetSale');
                $this->loadModel('ProjectBudgetInvoice');
                $this->loadModel('ProjectBudgetSyn');
                $this->loadModel('ProjectEmployeeManager');
                $this->loadModel('ProjectFinancePlus');
                $this->loadModel('ProjectFinancePlusDetail');
                if(!empty($projectId) && $projectId['Project']['id']){
                    $this->Project->id = $projectId['Project']['id'];
                    $this->Project->saveField('activity_id', '');
                    $this->ProjectBudgetInternal->updateAll(
                        array('ProjectBudgetInternal.activity_id' => 0),
                        array('ProjectBudgetInternal.project_id ' => $projectId['Project']['id'])
                    );
                    $this->ProjectBudgetInternalDetail->updateAll(
                        array('ProjectBudgetInternalDetail.activity_id' => 0),
                        array('ProjectBudgetInternalDetail.project_id ' => $projectId['Project']['id'])
                    );
                    $this->ProjectBudgetExternal->updateAll(
                        array('ProjectBudgetExternal.activity_id' => 0),
                        array('ProjectBudgetExternal.project_id ' => $projectId['Project']['id'])
                    );
                    $this->ProjectBudgetSale->updateAll(
                        array('ProjectBudgetSale.activity_id' => 0),
                        array('ProjectBudgetSale.project_id ' => $projectId['Project']['id'])
                    );
                    $this->ProjectBudgetInvoice->updateAll(
                        array('ProjectBudgetInvoice.activity_id' => 0),
                        array('ProjectBudgetInvoice.project_id ' => $projectId['Project']['id'])
                    );
                    $this->ProjectBudgetSyn->updateAll(
                        array('ProjectBudgetSyn.activity_id' => 0),
                        array('ProjectBudgetSyn.project_id ' => $projectId['Project']['id'])
                    );
                    $this->ProjectEmployeeManager->updateAll(
                        array('ProjectEmployeeManager.activity_id' => 0),
                        array('ProjectEmployeeManager.project_id ' => $projectId['Project']['id'])
                    );
                    $this->ProjectFinancePlus->updateAll(
                        array('ProjectFinancePlus.activity_id' => 0),
                        array('ProjectFinancePlus.project_id' => $projectId['Project']['id'])
                    );
                    $this->ProjectFinancePlusDetail->updateAll(
                        array('ProjectFinancePlusDetail.activity_id' => 0),
                        array('ProjectFinancePlusDetail.project_id' => $projectId['Project']['id'])
                    );
                } else {
                    $this->ProjectBudgetInternal->deleteAll(array('ProjectBudgetInternal.activity_id' => $id), false);
                    $this->ProjectBudgetInternalDetail->deleteAll(array('ProjectBudgetInternalDetail.activity_id' => $id), false);
                    $this->ProjectBudgetExternal->deleteAll(array('ProjectBudgetExternal.activity_id' => $id), false);
                    $this->ProjectBudgetSale->deleteAll(array('ProjectBudgetSale.activity_id' => $id), false);
                    $this->ProjectBudgetInvoice->deleteAll(array('ProjectBudgetInvoice.activity_id' => $id), false);
                    $this->ProjectBudgetSyn->deleteAll(array('ProjectBudgetSyn.activity_id' => $id), false);
                    $this->ProjectEmployeeManager->deleteAll(array('ProjectEmployeeManager.activity_id' => $id), false);
                }
                /**
                 * Xoa Profit Refer
                 */
                $this->loadModel('ActivityProfitRefer');
                $this->ActivityProfitRefer->deleteAll(array('ActivityProfitRefer.activity_id' => $id), false);
                /**
                 * Xoa cac tmp
                 */
                $this->loadModel('TmpStaffingSystem');
                $this->TmpStaffingSystem->deleteAll(array('TmpStaffingSystem.activity_id' => $id), false);
                /**
                 * Delete task and assign task
                 */
                $this->loadModel('ActivityTask');
                $activityTasks = $this->ActivityTask->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('activity_id' => $id),
                    'fields' => array('id', 'id')
                ));
                $this->ActivityTask->deleteAll(array('activity_id' => $id), false);
                if(!empty($activityTasks)){
                    $this->loadModel('ActivityTaskEmployeeRefer');
                    $this->ActivityTaskEmployeeRefer->deleteAll(array('activity_task_id' => $activityTasks), false);
                }
                //Log by QN on 2015/02/07
                $this->writeLog($activity, $this->employee_info, sprintf('Delete activity `%s`', $activity['Activity']['name']));
                $this->Session->setFlash(__('Activity has been deleted', true), 'success');
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $this->Session->setFlash(__('Activity is being in used. You can not delete.', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        /*
        if (ClassRegistry::init('ActivityRequest')->find('count', array('recursive' => -1, 'conditions' => array('activity_id' => $id)))
                || ClassRegistry::init('ActivityForecast')->find('count', array('recursive' => -1, 'conditions' => array(
                        'or' => array(array('activity_am' => $id, 'am_model' => 'Activity'), array('activity_pm' => $id, 'pm_model' => 'Activity')))))) {
            $this->Session->setFlash(__('You can not delete an activity used in an timesheet or in the forecast but you can desactivate it', true), 'error');
        } elseif (($employeeName = $this->_getEmpoyee()) && $this->Activity->delete($id)) {
            ClassRegistry::init('ActivityProfitRefer')->deleteAll(array(
                'activity_id' => $id
            ));
            $this->Session->setFlash(__('Activity has been deleted', true), 'success');
        } else {
            $this->Session->setFlash(__('Activity was not deleted', true), 'error');
        }
        */
        $this->redirect(array('action' => 'index'));
    }

    /**
     * Get _checkActivityRole
     *
     * @return void
     * @access protected
     */
    function _checkActivityRole() {
        if (empty($this->employee_info['Role']['name'])) {
            return false;
        }
        if (!($this->employee_info['Role']['name'] == 'hr'
                || $this->employee_info['Role']['name'] == 'admin' || $this->employee_info['Role']['name'] == 'pm')) {
            return false;
        }
        return true;
    }

    /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _getEmpoyee() {
        if (empty($this->employee_info['Company']['id'])) {
            return false;
        }
        $this->employee_info['Employee']['company_id'] = $this->employee_info['Company']['id'];
        $this->set('company_id', $this->employee_info['Company']['id']);
        $this->set('employee_id', $this->employee_info['Employee']['id']);
        return $this->employee_info['Employee'];
    }

    /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _parseParams() {
        $params = array('year' => 0, 'month' => 0, 'week' => 0);
        foreach (array_keys($params) as $type) {
            if (!empty($this->params['url'][$type])) {
                $params[$type] = intval($this->params['url'][$type]);
            }
        }
        if ($params['week'] && $params['year']) {
            $week = intval(date('W', mktime(0, 0, 0, 12, 31, $params['year'])));
            if (($week == 1 && $params['week'] <= 52) || ($week != 1 && $params['week'] <= $week)) {
                $date = new DateTime();
                $date->setISODate($params['year'], $params['week']);
                $date = strtotime($date->format('Y-m-d'));
            }
        } elseif ($params['month'] && $params['year']) {
            $date = mktime(0, 0, 0, $params['month'], 1, $params['year']);
        }

        if (empty($date)) {
            if (!empty($this->params['url']['month']) || !empty($this->params['url']['week']) || !empty($this->params['url']['year'])) {
                $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
                $this->redirect(array('action' => 'index'));
            }
            $date = mktime(0, 0, 0, date('m'), 1, date('Y'));
        }
        $_start = (date('w', $date) == 1) ? $date : strtotime('next monday', $date);
        $_end = strtotime('next sunday', $_start);
        $this->set(compact('_start', '_end'));
        return array($_start, $_end);
    }

    /**
     * get_sub_program
     * Get sub program
     * @param int $id
     * @return void
     * @access public
     */
    function get_activity_filter($currenr_id = null) {
        Configure::write('debug', 2);
        $this->autoRender = false;
        if (!empty($this->params['url']['data'])) {
            $this->Session->write('Filter.PMS_CHECK', $this->params['url']['data']);
            $companyId = $this->employee_info['Company']['id'];
            $list = $this->Activity->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'activated' => $this->params['url']['data'],
                    'company_id' => $companyId
                ),
                'fields' => array('id', 'name')
            ));
            if (!empty($list)) {
                foreach ($list as $k => $v) {
                    echo "<option value='" . $k . "' >" . $v . "</option>";
                }
            }
        } else {
            $this->Session->delete('Filter.PMS_CHECK');
        }
    }

    function get_sub_family($currenr_id = null){
        Configure::write('debug', 2);
        $this->autoRender = false;
        if (!empty($this->params['url']['data'])) {
            $list = ClassRegistry::init('ActivityFamily')->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'parent_id' => $this->params['url']['data']
                ),
                'fields' => array('id', 'name')
            ));
            if (!empty($list)) {
                foreach ($list as $k => $v) {
                    echo "<option value='" . $k . "' >" . $v . "</option>";
                }
            }
        }
    }

    function get_employee_for_profit_center($currenr_id = null){
        Configure::write('debug', 2);
        $this->autoRender = false;
        $this->loadModel('Employee');
        $this->loadModel('ProfitCenter');
        $this->loadModel('ProjectEmployeeProfitFunctionRefer');
        $pcList=array();
		$company = isset($this->employee_info['Company']['id']) ? $this->employee_info['Company']['id'] : 0;
        if(!empty($this->params['url']['data']))
        {
            $pcList=array_merge($pcList,$this->params['url']['data']);
            foreach($this->params['url']['data'] as $_index=>$_pc)
            {
                if($_pc == '') continue;
                $pathOfPC = $this->ProfitCenter->children($_pc);
                $pathOfPC = Set::classicExtract($pathOfPC,'{n}.ProfitCenter.id');
                $pcList=array_merge($pcList,$pathOfPC);
            }
            $pcList=array_unique($pcList);
        }
        $html="";
		$list = array();
        if (!empty($pcList)) {
            $refers = $this->ProjectEmployeeProfitFunctionRefer->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('profit_center_id' => $pcList),
                    'fields' => array('id', 'employee_id')
                ));
            $employees = $this->Employee->CompanyEmployeeReference->find('all', array(
                'conditions' => array(
                    //'CompanyEmployeeReference.company_id' => $employeeName['company_id'],
                    'NOT' => array('Employee.is_sas' => 1),
                    'Employee.id' => $refers
                ),
                'fields' => array('Employee.id', 'Employee.first_name', 'Employee.last_name'),
				'order' => array( 'Employee.first_name' => 'ASC')
            ));
        } else {
            
            $employees = $this->Employee->find('all', array(
				'recursive' => -1,
                'conditions' => array(
                    //'CompanyEmployeeReference.company_id' => $employeeName['company_id'],
                    'NOT' => array('Employee.is_sas' => 1),
                    'Employee.company_id' => $company
                ),
                'fields' => array('Employee.id', 'Employee.first_name', 'Employee.last_name'),
				'order' => array( 'Employee.first_name' => 'ASC')
            ));
		}
		$list = !empty($employees) ? Set::combine($employees, '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name')) : array();
		if (!empty($list)) {
			foreach ($list as $k => $v) {
				$html.= "<option value='" . $k . "' >" . $v . "</option>";
			}
		}		
        $results['list'] = $list;
        $results['html'] = $html;
        $results['pc'] = $pcList;
        die(json_encode($results));
    }
    function get_employee_by_pc(){
        Configure::write('debug', 2);
        $this->autoRender = false;
        $this->loadModel('Employee');
        $this->loadModel('ProfitCenter');
        $this->loadModel('ProjectEmployeeProfitFunctionRefer');
        $pcList=array();
        if(!empty($this->params['url']['data']))
        {
            $pcList=array_merge($pcList,$this->params['url']['data']);
            foreach($this->params['url']['data'] as $_index=>$_pc)
            {
                if($_pc == '') continue;
                $pathOfPC = $this->ProfitCenter->children($_pc);
                $pathOfPC = Set::classicExtract($pathOfPC,'{n}.ProfitCenter.id');
                $pcList=array_merge($pcList,$pathOfPC);
            }
            $pcList=array_unique($pcList);
        }
		$list = array();
        if (!empty($pcList)) {
            $refers = $this->ProjectEmployeeProfitFunctionRefer->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('profit_center_id' => $pcList),
                    'fields' => array('id', 'employee_id')
                ));
            $employees = $this->Employee->CompanyEmployeeReference->find('all', array(
                'conditions' => array(
                    //'CompanyEmployeeReference.company_id' => $employeeName['company_id'],
                    'NOT' => array('Employee.is_sas' => 1),
                    'Employee.id' => $refers
                ),
                'fields' => array('Employee.id', 'Employee.first_name', 'Employee.last_name'),
				'order' => array( 'Employee.first_name' => 'ASC')
            ));
        } else {
            $company = isset($this->employee_info['Company']['id']) ? $this->employee_info['Company']['id'] : 0;
            $employees = $this->Employee->find('all', array(
				'recursive' => -1,
                'conditions' => array(
                    //'CompanyEmployeeReference.company_id' => $employeeName['company_id'],
                    'NOT' => array('Employee.is_sas' => 1),
                    'Employee.company_id' => $company
                ),
                'fields' => array('Employee.id', 'Employee.first_name', 'Employee.last_name'),
				'order' => array( 'Employee.first_name' => 'ASC')
            ));
		}
		$list = !empty($employees) ? Set::combine($employees, '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name')) : array();
		// asort( $list );
        $results['list'] = $list;
        $results['pc'] = $pcList;
        die(json_encode($results));
    }

    /**
     * get_sub_program
     * Get sub program
     * @param int $id
     * @return void
     * @access public
     */
    function get_activity($currenr_id = null) {
        Configure::write('debug', 2);
        $this->autoRender = false;
        echo sprintf(__("%s--Select sub program--%s", true), '<option value="">', '</option>');
        if (!empty($this->params['url']['data'])) {
            $this->Session->write('Filter.Family', $this->params['url']['data']);
            $pms = $this->Session->read('Filter.PMS_CHECK');
            if(empty($pms)){
                $list = $this->Activity->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'family_id' => $this->params['url']['data']
                    ),
                    'fields' => array('id', 'name')
                ));
            } else {
                $list = $this->Activity->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'pms' => $pms,
                        'family_id' => $this->params['url']['data']
                    ),
                    'fields' => array('id', 'name')
                ));
            }

            if (!empty($list)) {
                foreach ($list as $k => $v) {
                    echo "<option value='" . $k . "' >" . $v . "</option>";
                }
            }
        } else {
            $this->Session->delete('Filter.Family');
        }
    }

    /**
     * get_sub_program
     * Get sub program
     * @param int $id
     * @return void
     * @access public
     */
    function get_sub_activity($currenr_id = null) {
        Configure::write('debug', 2);
        $this->autoRender = false;
        echo sprintf(__("%s--Select sub program--%s", true), '<option value="">', '</option>');
        $families = $this->Session->read('Filter.Family');
        $pms = $this->Session->read('Filter.PMS_CHECK');
        if (!empty($this->params['url']['data'])) {
            if(empty($families)){
                $list = $this->Activity->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'pms' => $pms,
                        'subfamily_id' => $this->params['url']['data']
                    ),
                    'fields' => array('id', 'name')
                ));
            } else {
                $list = $this->Activity->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'pms' => $pms,
                        'family_id' => $families,
                        'subfamily_id' => $this->params['url']['data']
                    ),
                    'fields' => array('id', 'name')
                ));
            }
            if (!empty($list)) {
                foreach ($list as $k => $v) {
                    echo "<option value='" . $k . "' >" . $v . "</option>";
                }
            }
        } else {
            $list = $this->Activity->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'pms' => $pms,
                    'subfamily_id' => $this->params['url']['data']
                ),
                'fields' => array('id', 'name')
            ));
            if (!empty($list)) {
                foreach ($list as $k => $v) {
                    echo "<option value='" . $k . "' >" . $v . "</option>";
                }
            }
        }
    }

    public function profitCenterOfActivity(){

    }

    // function nham nhi ko nen chay vao :P
    public function setActivited(){
        if (!($employeeName = $this->_getEmpoyee())) {
            $this->Session->setFlash(__('Your account do not have a company to management activity.', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }

        $this->Activity->cacheQueries = true;
        $this->Activity->Behaviors->attach('Containable');

        $conditions = array('Activity.id' => null);
        if (!empty($this->data['Export']['list'])) {
            $list = array_filter(explode(',', $this->data['Export']['list']));
            if ($list) {
                $conditions = array('Activity.id' => $list);
            }
        } else {
            $conditions = array('company_id' => $employeeName['company_id']);
        }
        $activities = $this->Activity->find("all", array(
            'contain' => array(),
            "conditions" => $conditions));

        $activityColumn = ClassRegistry::init('ActivityColumn')->getOptions($employeeName['company_id']);

        $this->loadModel('ActivityRequest');
        $this->loadModel('ActivityRequestConfirm');

        $employees = $sumEmployees = $sumActivities = array();

        $datas = $this->ActivityRequest->find(
            'all',
            array(
                'recursive' => -1,
                'fields' => array(
                    'id',
                    'employee_id',
                    'activity_id',
                    'SUM(value) as value'
                ),
            'group' => array('employee_id', 'activity_id'),
            'conditions' => array(
                'status' => 2,
                'company_id' => $employeeName['company_id'],
                'NOT' => array('value' => 0))
            )
        );

        $_datas = $this->ActivityRequest->find(
            'all',
            array(
                'recursive' => -1,
                'fields' => array(
                    'id',
                    'employee_id',
                    'task_id',
                    'SUM(value) as value'
                ),
            'group' => array('employee_id', 'task_id'),
            'conditions' => array(
                'status' => 2,
                'activity_id' => 0,
                'company_id' => $employeeName['company_id'],
                'NOT' => array('value' => 0))
            )
        );

        $this->loadModel('ActivityTask');
        $activityTasks = $this->ActivityTask->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'activity_id')
        ));
        $_sumActivitys = array();
        foreach($_datas as $_data){
            foreach($activityTasks as $activityTask){
                if($_data['ActivityRequest']['task_id'] == $activityTask['ActivityTask']['id']){
                    $_sumActivitys[$activityTask['ActivityTask']['activity_id']][] = $_data[0]['value'];
                }
            }
        }
        foreach($_sumActivitys as $k => $_sumActivity){
            $_sumActivitys[$k] = array_sum($_sumActivitys[$k]);
        }
        foreach ($datas as $data) {
            $dx = $data['ActivityRequest'];
            $data = $data['0']['value'];
            if (!isset($sumActivities[$dx['activity_id']])) {
                $sumActivities[$dx['activity_id']] = 0;
            }
            $sumActivities[$dx['activity_id']] += $data;
            if (!isset($sumEmployees[$dx['activity_id']][$dx['employee_id']])) {
                $sumEmployees[$dx['activity_id']][$dx['employee_id']] = 0;
            }
            $sumEmployees[$dx['activity_id']][$dx['employee_id']] += $data;
            $employees[$dx['employee_id']] = $dx['employee_id'];
        }

        if(!empty($sumActivities)){
            foreach($sumActivities as $id => $sumActivitie){
                $groupId[] = $id;
                if(!empty($_sumActivitys)){
                    foreach($_sumActivitys as $key => $_sumActivity){
                        if($id == $key){
                            $sumActivities[$id] = $sumActivitie + $_sumActivity;
                        }
                    }
                }
            }
        }
        if(!empty($_sumActivitys)){
            foreach($_sumActivitys as $key => $_sumActivity){
                if(in_array($key, $groupId)){
                    //do nothing
                } else {
                    $sumActivities[$key] = $_sumActivity;
                }
            }
        }

        $mapFamilies = Set::combine($this->Activity->Family->find('all', array(
                            'recursive' => -1,
                            'conditions' => array('company_id' => $employeeName['company_id']))), '{n}.Family.id', '{n}.Family');

        $families = $subfamilies = array();

        foreach ($mapFamilies as $mapFamilie) {
            if ($mapFamilie['parent_id']) {
                $subfamilies[$mapFamilie['id']] = $mapFamilie['name'];
            } else {
                $families[$mapFamilie['id']] = $mapFamilie['name'];
            }
        }
        $employees = Set::combine($this->ActivityRequest->Employee->find('all', array(
                            'fields' => array('id', 'tjm', 'actif'), 'recursive' => -1,
                            'conditions' => array('id' => $employees))), '{n}.Employee.id', '{n}.Employee');

        $list_activities = $this->Activity->find('list', array(
                'recursive' => -1,
                'conditions' => array('pms' => 0),
                'fields' => array('id', 'name')
            ));
        $list_short_names = $this->Activity->find('list', array(
                'recursive' => -1,
                'conditions' => array('pms' => 0),
                'fields' => array('id', 'short_name')
            ));

        //caculate
        $map = array();
        foreach ($activityColumn as $key => $column) {
            $map['C' . $column['code']] = $key;
            if (empty($column['display'])) {
                continue;
            }
            $columns[] = array_merge(array(
                'id' => $key,
                'field' => $key,
                'name' => $column['name'],
                'code' => 'C' . $column['code'],
                'calculate' => $column['calculate']));
        }
        $i = 1;
        $dataView = array();
        foreach ($activities as $activity) {
            $data = array(
                'id' => $activity['Activity']['id'],
                'no.' => $i++,
                'MetaData' => array()
            );

            foreach ($activityColumn as $key => $column) {
                $data[$key] = '';
                if ($column['calculate'] === false && isset($activity['Activity'][$key])) {
                    $data[$key] = (string) $activity['Activity'][$key];
                    if ($key === 'actif' || $key === 'pms') {
                        $data[$key] = $data[$key] ? 'yes' : 'no';
                    }
                }
            }

            //$data['start_date'] = $data['start_date'] ? $str_utility->convertToVNDate(date('Y-m-d', $data['start_date'])) : '';
            //$data['end_date'] = $data['end_date'] ? $str_utility->convertToVNDate(date('Y-m-d', $data['end_date'])) : '';
            $data['consumed'] = isset($sumActivities[$data['id']]) ? $sumActivities[$data['id']] : 0;
            $data['activated'] = $activity['Activity']['activated'] ? 'yes' : 'no';
            $data['actif'] = $activity['Activity']['actif'] ? 'yes' : 'no';
            if (isset($sumEmployees[$data['id']])) {
                foreach ($sumEmployees[$data['id']] as $id => $val) {
                    if(!empty($employees[$id]['tjm'])){
                        $employees[$id]['tjm'] = (float)str_replace(',', '.', $employees[$id]['tjm']);
                        $data['real_price'] += $val * $employees[$id]['tjm'];
                    }
                }
            }

            // Read the calculate formular and get the object
            foreach ($activityColumn as $key => &$column) {
                if (empty($column['calculate'])) {
                    continue;
                }
                if (!isset($column['_match'])) {
                    preg_match_all('/C\d+/i', $column['calculate'], $column['match']);
                    $column['match'] = array_unique($column['match'][0]);
                }
                $cal = $column['calculate'];
                if (!empty($column['match'])) {
                    foreach ($column['match'] as $k) {
                        $cal = str_replace($k, isset($data[$map[$k]]) ? floatval($data[$map[$k]]) : 0, $cal);
                    }
                }
                $data[$key] = @eval("return ($cal);");
                if (!is_numeric($data[$key])) {
                    $data[$key] = 0;
                } elseif (is_float($data[$key])) {
                    $data[$key] = round($data[$key], 2);
                }
            }

            $data['action.'] = '';

            $dataView[] = $data;
        }
        $gIdSetValues = array();
        foreach($dataView as $value){
            if($value['family_id'] == 20 && $value['c29'] <= 0){
                $gIdSetValues[] = $value['id'];
            }
            if($value['family_id'] == 6 && $value['c29'] <= 0){
                $gIdSetValues[] = $value['id'];
            }
        }
        //debug($gIdSetValues);
        if(!empty($gIdSetValues)){
            foreach($gIdSetValues as $ids){
                $this->Activity->id = $ids;
                $saved['activated'] = 0;
                $this->Activity->save($saved);
            }
        }
        pr('finish !');
        exit;
    }

    /**
     * Set pms = yes cho cac activity co linked
     */
    public function setPmsForActivityLinked(){
        $datas = $this->Activity->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'NOT' => array('project' => null)
            ),
            'fields' => array('id', 'id')
        ));
        foreach($datas as $data){
            $this->Activity->id = $data;
            $saved['pms'] = 1;
            $this->Activity->save($saved);
        }
        echo 'finish!';
        exit;
    }

    /**
     * Index
     *
     * @return void
     * @access public
     */

    protected function _parseNew($isManages=false) {
        if (!($employeeName = $this->_getEmpoyee())) {
            $this->Session->setFlash(__('Your account do not have a company to management activity.', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }
        $this->loadModel('ActivityRequest');
        $this->loadModel('ActivityTask');
        $this->loadModel('ProjectTask');
        $this->Activity->cacheQueries = true;
        $this->Activity->Behaviors->attach('Containable');
        $company_id = !empty($employeeName['company_id']) ? $employeeName['company_id'] : 0;
        // filter follow category

        extract(array_merge(array(
                'actiReview' => null),
                $this->params['url']));
        if(!empty($actiReview)){
            $this->Session->write("App.actiReview", $actiReview);
        }else{
            $actiReview = $this->Session->read("App.actiReview");
            if($actiReview){
                $this->Session->write("App.actiReview", $actiReview);
            }else{
                $actiReview = 1;
                $this->Session->write("App.actiReview", 1);
            }

        }
        //
        $conditions = array('Activity.id' => null);
        if (!empty($this->data['Export']['list'])) {
            $list = array_filter(explode(',', $this->data['Export']['list']));
            if ($list) {
                $conditions = array('Activity.id' => $list);
            }
        } else {
            $conditions = array('company_id' => $employeeName['company_id']);
        }
        $activated = ($actiReview == 1) ? 1 : (($actiReview == 2) ? 0 : array(0, 1));
        if($actiReview == 4){
            $activated = 2;
        }
        $conditions = array_merge($conditions, array('activated' => $activated));
        /**
         * Lay tat ca cac activity theo dieu kien
         */
        $activities = $this->Activity->find("all", array(
            'contain' => array(),
            "conditions" => $conditions));
        /**
         * Lay tat ca cac acvitity column duoc chon
         */
        $activityColumn = ClassRegistry::init('ActivityColumn')->getOptions($employeeName['company_id']);
        /**
         * Lay cac manager backup cua activity
         */
        $this->loadModel('ProjectEmployeeManager');
        $listActivityIds = !empty($activities) ? Set::classicExtract($activities, '{n}.Activity.id') : array();
        $listManger = $this->ProjectEmployeeManager->find('all', array(
            'recursive' => -1,
            'fields' => array(
                    'id',
                    'project_manager_id',
                    'is_backup',
                    'activity_id'
            ),
            'conditions' => array('activity_id' => $listActivityIds)
        ));
        $listManger = !empty($listManger) ? Set::combine($listManger,'{n}.ProjectEmployeeManager.id','{n}.ProjectEmployeeManager','{n}.ProjectEmployeeManager.activity_id') : array();
        $idActivities = $activityLinkeds = $activityNotLinkeds = array();
        if(!empty($activities)){
            foreach($activities as $key => $activity){
                $dx = $activity['Activity'];
                $primaryManager = $dx['project_manager_id'];
                unset($activities[$key]['Activity']['project_manager_id']);
                $idActivities[] = $dx['id'];
                if($dx['project'] != null){
                    $activityLinkeds[$dx['project']] = $dx['id'];
                } else {
                    $activityNotLinkeds[] = $dx['id'];
                }
                //add manager neu co
                if(!empty($listManger[$dx['id']])){
                    $_manager = array();
                    foreach($listManger[$dx['id']] as $_key => $_value){
                        $_manager[] = $_value;
                    }
                    $activities[$key]['project_manager_id'] =$_manager;
                }else{
                    $activities[$key]['project_manager_id'] = array();
                }
                if(!empty($primaryManager)){
                    $activities[$key]['project_manager_id'][] = array(
                        'project_manager_id' => $primaryManager,
                        'is_backup' => 0
                    );
                }
            }
        }
        $activityTasks = $this->ActivityTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('activity_id' => $idActivities),
            'fields' => array('id', 'parent_id', 'activity_id', 'estimated', 'overload', 'project_task_id')
        ));
        $idActivityTasks = !empty($activityTasks) ? Set::classicExtract($activityTasks, '{n}.ActivityTask.id') : array();
        $taskOfActivities = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.id', '{n}.ActivityTask.activity_id') : array();
        $listATaskLinkPTasks = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.project_task_id', '{n}.ActivityTask.id') : array();
        $employees = $sumEmployees = $sumActivities = array();
        $datas = $this->ActivityRequest->find(
            'all',
            array(
                'recursive' => -1,
                'fields' => array(
                    'id',
                    'employee_id',
                    'activity_id',
                    'SUM(value) as value'
                ),
            'group' => array('employee_id', 'activity_id'),
            'conditions' => array(
                'status' => 2,
                'activity_id' => $idActivities,
                'company_id' => $employeeName['company_id'],
                'NOT' => array('value' => 0))
            )
        );
        foreach ($datas as $data) {
            $dx = $data['ActivityRequest'];
            $data = $data['0']['value'];
            if (!isset($sumActivities[$dx['activity_id']])) {
                $sumActivities[$dx['activity_id']] = 0;
            }
            $sumActivities[$dx['activity_id']] += $data;
            if (!isset($sumEmployees[$dx['activity_id']][$dx['employee_id']])) {
                $sumEmployees[$dx['activity_id']][$dx['employee_id']] = 0;
            }
            $sumEmployees[$dx['activity_id']][$dx['employee_id']] += $data;
            $employees[$dx['employee_id']] = $dx['employee_id'];
        }
        $previousTask = !empty($sumActivities) ? $sumActivities : array();
        $_datas = $this->ActivityRequest->find(
            'all',
            array(
                'recursive' => -1,
                'fields' => array(
                    'id',
                    'employee_id',
                    'task_id',
                    'SUM(value) as value'
                ),
            'group' => array('employee_id', 'task_id'),
            'conditions' => array(
                'status' => 2,
                'task_id' => $idActivityTasks,
                'activity_id' => 0,
                'company_id' => $employeeName['company_id'],
                'NOT' => array('value' => 0))
            )
        );
        $sumTasks = array();
        foreach($_datas as $_data){
            $dx = $_data['ActivityRequest'];
            $val = $_data[0];
            $acti = !empty($taskOfActivities[$dx['task_id']]) ? $taskOfActivities[$dx['task_id']] : 0;
            if(!isset($sumActivities[$acti])){
                $sumActivities[$acti] = 0;
            }
            $sumActivities[$acti] += $val['value'];
            if (!isset($sumEmployees[$acti][$dx['employee_id']])) {
                $sumEmployees[$acti][$dx['employee_id']] = 0;
            }
            $sumEmployees[$acti][$dx['employee_id']] += $val['value'];
            $employees[$dx['employee_id']] = $dx['employee_id'];

            if(!isset($sumTasks[$dx['task_id']])){
                $sumTasks[$dx['task_id']] = 0;
            }
            $sumTasks[$dx['task_id']] += $val['value'];
        }
        //@Huupc: caculate workload, overload of project task and activity task.
        //@Huupc: Prject Task Linked Activity : PMS = YES
        $dataFromProjectTasks = $projectTasks = array();
        if(!empty($activityLinkeds)){
            $projectTasks = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => array_keys($activityLinkeds)),
                'fields' => array('id', 'parent_id', 'project_id', 'estimated', 'overload', 'special', 'special_consumed')
            ));
        }
        if(!empty($projectTasks)){
            $projectTasks = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask') : array();
            $parentIds = !empty($projectTasks) ? array_unique(Set::classicExtract($projectTasks, '{n}.parent_id')) : array();
            $sumWorload = $sumOverload = $sumRemainSpecials = array();
            foreach($projectTasks as $key => $projectTask){
                if(in_array($key, $parentIds)){
                    unset($projectTasks[$key]);
                }
                else{
                    $dx = $projectTask['project_id'];
                    $dataEstimated = $projectTask['estimated'];
                    $dataOverload = $projectTask['overload'];
                    if (!isset($sumWorload[$dx])) {
                        $sumWorload[$dx] = 0;
                    }
                    $sumWorload[$dx] += $dataEstimated;
                    if (!isset($sumOverload[$dx])) {
                        $sumOverload[$dx] = 0;
                    }
                    $sumOverload[$dx] += $dataOverload;
                    $consumedForTasks = 0;
                    if(!empty($projectTask['special']) && $projectTask['special'] == 1){
                        $consumedForTasks = !empty($projectTask['special_consumed']) ? $projectTask['special_consumed'] : 0;
                        if (!isset($sumRemainSpecials[$dx])) {
                            $sumRemainSpecials[$dx] = 0;
                        }
                        $sumRemainSpecials[$dx] += $dataEstimated - $consumedForTasks;
                    } else {
                        $ATaskId = !empty($listATaskLinkPTasks[$projectTask['id']]) ? $listATaskLinkPTasks[$projectTask['id']] : 0;
                        $consumedForTasks = !empty($sumTasks[$ATaskId]) ? $sumTasks[$ATaskId] : 0;
                    }
                    if (!isset($sumRemains[$dx])) {
                        $sumRemains[$dx] = 0;
                    }
                    $sumRemains[$dx] += ($dataEstimated + $dataOverload) - $consumedForTasks;
                }
            }
            foreach($activityLinkeds as $projectId => $activityId){
                $workload = isset($sumWorload[$projectId]) ? $sumWorload[$projectId] : 0;
                $overload = isset($sumOverload[$projectId]) ? $sumOverload[$projectId]: 0;
                $consumed = isset($sumActivities[$activityId]) ? $sumActivities[$activityId] : 0;
                $previous = isset($previousTask[$activityId]) ? $previousTask[$activityId] : 0;
                $dataFromProjectTasks[$activityId]['workload'] = $workload + $previous;
                $dataFromProjectTasks[$activityId]['overload'] = $overload;
                $dataFromProjectTasks[$activityId]['consumed'] = $consumed;
                $remainSPCs = isset($sumRemainSpecials[$projectId]) ? $sumRemainSpecials[$projectId] : 0;
                $remains = isset($sumRemains[$projectId]) ? $sumRemains[$projectId] : 0;
                $remains = $remains - $remainSPCs;
                if(($workload + $previous + $overload) == 0){
                    $dataFromProjectTasks[$activityId]['completed'] = 0;
                } else {
                    $com = round(($consumed*100)/($workload + $previous + $overload), 2);
                    if($com > 100){
                        $dataFromProjectTasks[$activityId]['completed'] = 100;
                    } else {
                        $dataFromProjectTasks[$activityId]['completed'] = $com;
                    }
                }
                $dataFromProjectTasks[$activityId]['remain'] = $remains;
            }
        }
        //@Huupc: End Prject Task Linked Activity : PMS = YES
        //@Huupc: Activity Task Not Linked Project : PMS = NO
        $dataFromActivityTasks = $taskNotLinkeds = array();
        if(!empty($activityTasks)){
            foreach($activityTasks as $activityTask){
                $dx = $activityTask['ActivityTask'];
                if(in_array($dx['activity_id'], $activityNotLinkeds)){
                    $taskNotLinkeds[] = $dx;
                }
            }
        }
        if(!empty($taskNotLinkeds)){
            //debug($taskNotLinkeds);
            $_parentIds = array_unique(Set::classicExtract($taskNotLinkeds, '{n}.parent_id'));
            $_sumWorload = $_sumOverload = array();
            foreach($taskNotLinkeds as $taskNotLinked){
                if(in_array($taskNotLinked['id'], $_parentIds)){
                    unset($taskNotLinkeds[$key]);
                }
                else{
                    $_dx = $taskNotLinked['activity_id'];
                    $_dataEstimated = $taskNotLinked['estimated'];
                    $_dataOverload = $taskNotLinked['overload'];
                    if (!isset($_sumWorload[$_dx])) {
                        $_sumWorload[$_dx] = 0;
                    }
                    $_sumWorload[$_dx] += $_dataEstimated;

                    if (!isset($_sumOverload[$_dx])) {
                        $_sumOverload[$_dx] = 0;
                    }
                    $_sumOverload[$_dx] += $_dataOverload;
                }
            }
            foreach($activityNotLinkeds as $id){
                $_workload = isset($_sumWorload[$id]) ? $_sumWorload[$id] : 0;
                $_previous = isset($previousTask[$id]) ? $previousTask[$id] : 0;
                $_overload = isset($_sumOverload[$id]) ? $_sumOverload[$id]: 0;
                $_consumed = isset($sumActivities[$id]) ? $sumActivities[$id] : 0;
                $dataFromActivityTasks[$id]['workload'] = $_workload + $_previous;
                $dataFromActivityTasks[$id]['overload'] = $_overload;
                $dataFromActivityTasks[$id]['consumed'] = $_consumed;
                if(($_workload + $_previous + $_overload) == 0){
                    $dataFromActivityTasks[$id]['completed'] = 0;
                } else {
                    $_com = round(($_consumed*100)/($_workload + $_previous + $_overload), 2);
                    if($_com > 100){
                        $dataFromActivityTasks[$id]['completed'] = 100;
                    } else {
                        $dataFromActivityTasks[$id]['completed'] = $_com;
                    }
                }
                $rems = ($_workload + $_previous + $_overload) - $_consumed;
                if($rems < 0){
                    $dataFromActivityTasks[$id]['remain'] = 0;
                } else {
                    $dataFromActivityTasks[$id]['remain'] = $rems;
                }
            }
        }
        //@Huupc: End Activity Task Not Linked Project : PMS = NO
        $mapFamilies = Set::combine($this->Activity->Family->find('all', array(
                            'recursive' => -1,
                            'conditions' => array('company_id' => $employeeName['company_id']))), '{n}.Family.id', '{n}.Family');
        $families = $subfamilies = array();

        foreach ($mapFamilies as $mapFamilie) {
            if ($mapFamilie['parent_id']) {
                $subfamilies[$mapFamilie['id']] = $mapFamilie['name'];
            } else {
                $families[$mapFamilie['id']] = $mapFamilie['name'];
            }
        }
        $employees = Set::combine($this->ActivityRequest->Employee->find('all', array(
                            'fields' => array('id', 'tjm', 'actif'), 'recursive' => -1,
                            'conditions' => array('id' => $employees))), '{n}.Employee.id', '{n}.Employee');
        /**
         * Lay du lieu budget
         */
        $this->loadModel('ProjectBudgetSyn');
        $budgets = $this->ProjectBudgetSyn->find('all', array(
            'recursive' => -1,
            'conditions' => array('activity_id' => $idActivities)
        ));
        $budgets = !empty($budgets) ? Set::combine($budgets, '{n}.ProjectBudgetSyn.activity_id', '{n}.ProjectBudgetSyn') : array();
        /**
         * Lay du lieu cho phan assign to
         */
        $this->loadModel('TmpStaffingSystem');
        $assignEmployees = $assignProfitCenters = array();
        if(isset($activityColumn['assign_to_employee']))
        {
        if($activityColumn['assign_to_employee'] && $activityColumn['assign_to_employee']['display'] == 1){
            $dataSystems = $this->TmpStaffingSystem->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'model' => array('employee'),
                    'NOT' => array(
                        'activity_id' => 0,
                        'model_id' => 999999999
                    ),
                    'company_id' => $employeeName['company_id']
                ),
                'fields' => array('activity_id', 'SUM(estimated) as value'),
                'group' => array('activity_id')
            ));
            $assignEmployees = !empty($dataSystems) ? Set::combine($dataSystems, '{n}.TmpStaffingSystem.activity_id', '{n}.0.value') : array();
        }
        }
        if(isset($activityColumn['assign_to_profit_center']))
        {
        if($activityColumn['assign_to_profit_center'] && $activityColumn['assign_to_profit_center']['display'] == 1){
            $_dataSystems = $this->TmpStaffingSystem->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'model' => array('profit_center'),
                    'NOT' => array(
                        'activity_id' => 0,
                        'model_id' => 999999999
                    ),
                    'company_id' => $employeeName['company_id']
                ),
                'fields' => array('activity_id', 'SUM(estimated) as value'),
                'group' => array('activity_id')
            ));
            $assignProfitCenters = !empty($_dataSystems) ? Set::combine($_dataSystems, '{n}.TmpStaffingSystem.activity_id', '{n}.0.value') : array();
        }
        }
        /**
         * Tinh consumed cua nam hien tai, va consumed cua thang hien tai
         */
        $currentYears = date('Y', time());
        $lastY = strtotime('01-01-'. ($currentYears-3));
        $nextY = strtotime('31-12-'. ($currentYears+3));
        // $consumedAndWorkloadForActivities = $this->TmpStaffingSystem->find('all', array(
        //     'recursive' => -1,
        //     'conditions' => array(
        //         'model' => array('employee'),
        //         'activity_id' => $idActivities,
        //         'date BETWEEN ? AND ?' => array($lastY, $nextY),
        //         'company_id' => $employeeName['company_id']
        //     ),
        //     'fields' => array(
        //         'activity_id',
        //         'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . $currentYears . '" THEN `consumed` ELSE 0 END) AS consumed_' . $currentYears,
        //         'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears-1) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears-1),
        //         'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears-2) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears-2),
        //         'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears-3) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears-3),
        //         'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears+1) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears+1),
        //         'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears+2) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears+2),
        //         'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears+3) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears+3),
        //         'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . $currentYears . '" THEN `estimated` ELSE 0 END) AS workload_' . $currentYears,
        //         'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears-1) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears-1),
        //         'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears-2) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears-2),
        //         'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears-3) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears-3),
        //         'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears+1) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears+1),
        //         'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears+2) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears+2),
        //         'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears+3) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears+3)
        //     ),
        //     'group' => array('activity_id')
        // ));
        // $consumedAndWorkloadForActivities = !empty($consumedAndWorkloadForActivities) ? Set::combine($consumedAndWorkloadForActivities, '{n}.TmpStaffingSystem.activity_id', '{n}.0') : array();

         /*
        get consume from activity_requests
         */
        $fieldx = array('activity_id', 'SUM(`value`) as consumed');
        //each forward/backward year
        $rangeY = range(date('Y', $lastY), date('Y', $nextY));
        foreach ($rangeY as $cyear) {
            $fieldx[] = 'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . $cyear . '" THEN `value` ELSE 0 END) AS consumed_' . $cyear;
        }

        $taskIds = array();
        $xlist = array();
        foreach ($activityTasks as $d) {
            $dx = $d['ActivityTask'];
            if( !isset($xlist[$dx['activity_id']]) ){
                $xlist[$dx['activity_id']] = array();
            }
            $xlist[$dx['activity_id']][] = $dx['id'];
            $taskIds[] = $dx['id'];
        }
        $field = 'CASE WHEN task_id = 0 THEN activity_id ';
        foreach($xlist as $id => $task){
            if( empty($task) )continue;
            $field .= sprintf('WHEN task_id IN (%s) THEN %s ', implode($task, ','), $id);
        }
        $field .= ' ELSE NULL END';
        $this->ActivityRequest->virtualFields['activity'] = $field;
        $fieldx[] = 'activity';

        $dataConsume = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'OR' => array(
                    'activity_id' => $idActivities,
                    'task_id' => $taskIds
                ),
                'date BETWEEN ? AND ?' => array($lastY, $nextY),
            ),
            'fields' => $fieldx,
            'group' => array('activity')
        ));
        $consumedAndWorkloadForActivities = !empty($dataConsume) ? Set::combine($dataConsume, '{n}.ActivityRequest.activity', '{n}.0') : array();

        $consumedOfMonth = array();
        if(isset($activityColumn['consumed_current_month']))
        {
            if($activityColumn['consumed_current_month'] && $activityColumn['consumed_current_month']['display'] == 1){
                $startMonthCurrent = strtotime('00:00:00 01-'.date('m', time()).'-'.date('Y', time()));
                $endMonthCurrent = strtotime('00:00:00 31-'.date('m', time()).'-'.date('Y', time()));
                $consumedOfMonth = $this->ActivityRequest->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'OR' => array(
                            'activity_id' => $idActivities,
                            'task_id' => $taskIds
                        ),
                        'date BETWEEN ? AND ?' => array($startMonthCurrent, $endMonthCurrent)
                    ),
                    'fields' => array('activity', 'SUM(`value`) as value'),
                    'group' => array('activity')
                ));
                $consumedOfMonth = !empty($consumedOfMonth) ? Set::combine($consumedOfMonth, '{n}.TmpStaffingSystem.activity_id', '{n}.0.value') : array();
            }
        }
        $cacheColumn = !empty($activityColumn) ? Set::combine($activityColumn, '{s}.code', '{s}') : array();
        $activityColumn = $this->_sortColumnActivity($activityColumn, 1, $cacheColumn, array());

        $this->set('activities_activated', $this->Session->read("App.activities_activated"));
        $this->set(compact('assignEmployees', 'assignProfitCenters', 'consumedOfYear', 'consumedOfMonth', 'consumedAndWorkloadForActivities', 'currentYears'));
        $this->set(compact('list_activities', 'list_short_names', 'listProfitCenters', 'dataFromProjectTasks', 'dataFromActivityTasks', 'list_employees', 'budgets'));
        $this->set(compact('list', 'families', 'subfamilies', 'activities', 'company_id', 'employeeName', 'employees', 'activityColumn', 'sumEmployees', 'sumActivities'));
    }

    /**
     * Sap xep thu tu cua cac cot tinh toan trong activity column
     */
    private function _sortColumnActivity($activityColumn, $runCount, $cacheColumn, $columnCaculates = array()){
        $tmpColumns = array();
        foreach ($activityColumn as $key => $column) {
            $column['codeTmp'] = !empty($column['code']) ? 'C' . $column['code'] : '';
            if(!empty($column['calculate'])){
                preg_match_all('/C\d+/i', $column['calculate'], $column['match']);
                $column['match'] = array_unique($column['match'][0]);
                if(!empty($column['match'])){ // neu key co tinh toan
                    if($runCount == 1){
                        $haveCalculate = false;
                        foreach($column['match'] as $val){ // duyet tung phan tu xem co phan tu nao can tinh ko?
                            if(!empty($activityColumn[strtolower($val)])){ // neu ma co column do mang column
                                if(!empty($activityColumn[strtolower($val)]['calculate'])){ // neu bat ky phan tu nao tinh toan thi check = true
                                    $haveCalculate = true;
                                }
                            }
                        }
                        if($haveCalculate == false){
                            $columnCaculates[strtolower($key)] = $column;//$column['calculate'];
                            unset($activityColumn[$key]);
                        }
                    } else {
                        $haveCalculate = array();
                        foreach($column['match'] as $val){  //duyet tung phan tu xem co phan tu nao can tinh ko?
                            $val = strtolower($val);
                            $_val = str_replace('c', '', $val);
                            if(in_array($val, array_keys($columnCaculates))){
                                $haveCalculate[] = true;
                            } elseif(!empty($cacheColumn[$_val]) && empty($cacheColumn[$_val]['calculate'])) {
                                $haveCalculate[] = true;
                            } else {
                                $haveCalculate[] = false;
                            }
                        }
                        if(!in_array(false, $haveCalculate)){
                            $columnCaculates[strtolower($key)] = $column;//$column['calculate'];
                            unset($activityColumn[$key]);
                        }
                    }
                }
            } else {
                $columnCaculates[strtolower($key)] = $column;
                unset($activityColumn[$key]);
            }
        }
        if(!empty($activityColumn)){
            $columnCaculates = $this->_sortColumnActivity($activityColumn, 2, $cacheColumn, $columnCaculates);
        }
        return $columnCaculates;
    }

    /**
     * Get status of company
     */
    private function _getIdStatusClosedOfCompany(){
        $this->loadModel('ProjectStatus');
        $infors = $this->Session->read('Auth.employee_info');
        $status = $this->ProjectStatus->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                //'name' => array('Clos', 'closed ', 'A lancer', 'A launch'),
                'company_id' => $infors['Company']['id']
            )
        ));
        $close = $aLancer = '';
        if(!empty($status)){
            foreach($status as $id => $val){
                $val = trim(strtolower($val));
                if($val == 'clos' || $val == 'closed'){
                    $close = $id;
                } elseif($val == 'a lancer' || $val == 'a launch'){
                    $aLancer = $id;
                }
            }
        }
        return $close;
    }
    /**
     * Lay project manager cua 1 company
     */
    public function get_project_manager(){
        $employeeName = $this->_getEmpoyee();
        /**
         * Get list project manager
         */
        $this->loadModel('Employee');
        $companyEmployees = $this->Employee->CompanyEmployeeReference->find('list', array(
            'recursive' => -1,
            'fields' => array('employee_id', 'role_id'),
            'conditions' => array('CompanyEmployeeReference.company_id' => $employeeName['company_id'])));

        $projectEmployees = $this->Employee->find('all', array(
            'order' => 'last_name  ASC',
            'recursive' => -1,
            'conditions' => array(
                'id' => array_keys($companyEmployees)
            ),
            'fields' => array('first_name', 'last_name', 'id')));

        $projectManagers = array();
        foreach ($projectEmployees as $projectEmployee) {
            $projectManagers['project'][$projectEmployee['Employee']['id']] = $projectEmployee['Employee']['first_name'] . ' ' . $projectEmployee['Employee']['last_name'];
            foreach (array('pm' => array(3,2), 'tech' =>array(3,5)) as $key => $role) {
                if (in_array($companyEmployees[$projectEmployee['Employee']['id']], $role)) {
                    $projectManagers[$key][$projectEmployee['Employee']['id']] = $projectManagers['project'][$projectEmployee['Employee']['id']];
                }
            }
        }
        $projectManagers = !empty($projectManagers['pm']) ? $projectManagers['pm'] : array();
        $this->set('projectManagers', $projectManagers);
        $this->layout = false;
    }
        /**
     *  Kiem tra activity da co su dung
     *  @return boolean
     *  @access private
     */

    private function _activityIsUsing($id = null){
        if( !isset($this->employee_info['Company']['id']) )return 'false';
        $this->loadModel('ActivityTask');
        $this->loadModel('ActivityRequest');
        $conditions = array(
            'activity_id' => $id
        );
        $activityTasks = array();
        $activityTasks = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'conditions' => array('activity_id' => $id),
            'fields' => array('id', 'id')
        ));
        if(!empty($activityTasks)){
            $conditions = array(
                'OR' => array(
                    'activity_id' => $id,
                    'task_id' => $activityTasks
                )
            );
        }
        $requests = $this->ActivityRequest->find('count', array(
            'recursive' => -1,
            'conditions' => $conditions
        ));
        $allowDeleteActivity= 'true';
        if($requests != 0){
            $allowDeleteActivity = 'false';
        }
        return $allowDeleteActivity;
    }
    // export Excel Review
    public function export_excel_review(){
        if( !empty($this->data) ){
            $this->SlickExporter->init();
            $data = json_decode($this->data['data'], true);
            $this->SlickExporter
                ->setT('Activity Review')   //auto translate
                ->save($data, 'activity_review_{date}.xls');
        }
        die;
    }
    // export Excel Index Screen
    public function export_excel_index(){
        if( !empty($this->data) ){
            $this->SlickExporter->init();
            $data = json_decode($this->data['data'], true);
            $this->SlickExporter
                ->setT('Activity Index')    //auto translate
                ->save($data, 'activity_index_{date}.xls');
        }
        die;
    }
    // export Excel View
    public function export_excel_manage(){
        if( !empty($this->data) ){
            $this->SlickExporter->init();
            $data = json_decode($this->data['data'], true);
            $this->SlickExporter
                ->setT('Activity View') //auto translate
                ->save($data, 'activity_view_{date}.xls');
        }
        die;
    }
}
?>
