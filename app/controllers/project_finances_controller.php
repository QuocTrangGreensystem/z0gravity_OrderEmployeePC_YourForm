<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectFinancesController extends AppController {

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $uses = array('ProjectFinance', 'ProjectFinancePlus', 'ProjectFinancePlusDetail', 'Project', 'ProjectFinancePlusDate');
    /**
     * index
     *
     * @return void
     * @access public
     */
    public function index($project_id = null){
        if (!$this->_checkRole(false, $project_id)) {
            $this->data = null;
        }
        $this->_checkWriteProfile('finance');
		$bg_currency = $this->getCurrencyOfBudget();
        $valDefault = '0.00' ;
        $this->loadModels('ProjectFinancePartner', 'Project');
        $projectName = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('id' => $project_id),
            'fields' => array('id', 'project_name')
        ));
        $data = $this->ProjectFinance->find('all',array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id)
        ));
        if(empty($data)) {
            $data['bp_investment_city'] =  $valDefault;
            $data['bp_operation_city'] =  $valDefault;
            $data['available_investment'] =  $valDefault;
            $data['available_operation'] =  $valDefault;
            $data['finance_total_budget'] =  $valDefault;
            $data['finance_plan'] =  $valDefault;
            $data['comment'] = '';
            $saves = array('project_id' => $project_id);
            $this->ProjectFinance->create();
            $this->ProjectFinance->save($saves);
            $finance_id = $this->ProjectFinance->getLastInsertID();
        } else {
            $data = Set::classicExtract($data,'{n}.ProjectFinance');
            $data = $data[0];
            $data['finance_plan'] =  $data['finance_total_budget'] - $data['bp_investment_city'] - $data['bp_operation_city'];
            $finance_id = $data['id'];
        }

        //DATA PARTNER
        $dataPartner = $this->ProjectFinancePartner->find('all',array(
            'recursive' => -1,
            'conditions' => array('finance_id' => $finance_id,'finance_partner <> ' => ''),
            'order' => array('id')
        ));
        $this->ProjectFinancePartner->deleteAll(
            array('ProjectFinancePartner.finance_id' => $finance_id,'ProjectFinancePartner.finance_partner' => '')
        );
        $this->set(compact('project_id','data','finance_id','dataPartner', 'projectName', 'bg_currency'));
    }
    public function update($project_id = null){
        if(isset($_POST['updateMe'])&&$_POST['updateMe'] == 1) {
            $value = $_POST['value'];
            if( $_POST['field'] == 'comment' ){
                $db = $this->ProjectFinance->getDataSource();
                $value = $db->value($value, 'string');
            }
            $this->ProjectFinance->updateAll(
                array('ProjectFinance.'.$_POST['field'] => $value),
                array('ProjectFinance.project_id ' => $project_id)
            );
            echo 1;
        } else echo -1;
        exit;
    }
    public function add_partner($finance_id = null){
        $this->loadModel('ProjectFinancePartner');
        if( isset($_POST['add']) && $_POST['add'] == 1 && $finance_id != null ){
            $saves = array('finance_id' => $finance_id);
            $this->ProjectFinancePartner->create();
            $this->ProjectFinancePartner->save($saves);
            $partner_id = $this->ProjectFinancePartner->getLastInsertID();
            echo $partner_id;
        }
        exit;
    }
    public function delete_partner($partner_id = null){
        $this->loadModel('ProjectFinancePartner');
        if( isset($_POST['del']) && $_POST['del'] == 1 && $partner_id != null ){
            $check = $this->ProjectFinancePartner->find('count',array(
                'recursive' => -1,
                'conditions' => array('id' => $partner_id)
            ));
            if($check) {
                $check = $this->ProjectFinancePartner->delete($partner_id);
            }
            echo "Deleted!";
        }
        exit;
    }
    public function update_partner($id = null){
        $this->loadModel('ProjectFinancePartner');
        if(isset($_POST['updateMe']) && $_POST['updateMe'] == 1) {
            $value = $_POST['value'];
            if($_POST['field'] == 'finance_percent') {
                $this->ProjectFinancePartner->updateAll(
                    array('ProjectFinancePartner.'.$_POST['field'] => $value),
                    array('ProjectFinancePartner.id ' => $id)
                );
                echo 1;
            } else {
                $this->ProjectFinancePartner->id = $id;
                $this->ProjectFinancePartner->save(array(
                    $_POST['field'] => $value,
                ));
                echo 'Updated!';
            }
        }
        exit;
    }

    public function index_plus($project_id){
        if(isset($this->employee_info['Color']['is_new_design']) && $this->employee_info['Color']['is_new_design']){
            $this->redirect(array(
                'controller' => 'project_finances_preview',
                'action' => 'index_plus/'.$project_id
            ));
        }
        /**
         * Lay start date va end date
         */
        if (!$this->_checkRole(false, $project_id)) {
            $this->data = null;
        }
		
        $this->_checkWriteProfile('finance_plus');
		$bg_currency = $this->getCurrencyOfBudget();
        $employee_info = $this->employee_info;
        $invStart = $invEnd = $fonStart = $fonEnd = '';
        $getSaveHistory = $this->ProjectFinancePlusDate->find('first', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id)
        ));
        if(!empty($getSaveHistory)) {
            $invStart = !empty($getSaveHistory['ProjectFinancePlusDate']['inv_start']) ? $getSaveHistory['ProjectFinancePlusDate']['inv_start'] : '';
            $invEnd = !empty($getSaveHistory['ProjectFinancePlusDate']['inv_end']) ? $getSaveHistory['ProjectFinancePlusDate']['inv_end'] : '';
            $fonStart = !empty($getSaveHistory['ProjectFinancePlusDate']['fon_start']) ? $getSaveHistory['ProjectFinancePlusDate']['fon_start'] : '';
            $fonEnd = !empty($getSaveHistory['ProjectFinancePlusDate']['fon_end']) ? $getSaveHistory['ProjectFinancePlusDate']['fon_end'] : '';
        }
        $invStart = !empty($this->params['url']['inv_start']) ? strtotime(@$this->params['url']['inv_start']) : (!empty($invStart) ? $invStart : time());
        $invEnd = !empty($this->params['url']['inv_end']) ? strtotime(@$this->params['url']['inv_end']) : (!empty($invEnd) ? $invEnd : time());
        $fonStart = !empty($this->params['url']['fon_start']) ? strtotime(@$this->params['url']['fon_start']) : (!empty($fonStart) ? $fonStart : time());
        $fonEnd = !empty($this->params['url']['fon_end']) ? strtotime(@$this->params['url']['fon_end']) : (!empty($fonEnd) ? $fonEnd : time());
        /**
         * Lay project
         */
        $projects = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('Project.id' => $project_id),
            'fields' => array('id', 'activity_id', 'project_name', 'company_id')
        ));
        $company_id = !empty($projects['Project']['company_id']) ? $projects['Project']['company_id'] : 0;
        $activity_id = !empty($projects['Project']['activity_id']) ? $projects['Project']['activity_id'] : 0;
        $projectName = !empty($projects['Project']['project_name']) ? $projects['Project']['project_name'] : '';
        /**
         * Lay cac finance+
         */
        $finances = $this->ProjectFinancePlus->find('list', array(
            'recursive' => -1,
            'conditions' => array(
				'project_id' => $project_id,
				// 'activity_id' => $activity_id,
			),
            'fields' => array('id', 'name', 'type'),
            'group' => array('type', 'id')
        ));
        /**
         * Finance detail
         */
        $_financeDetails = $this->ProjectFinancePlusDetail->find('all', array(
            'recursive' => -1,
            'conditions' => array(
				'project_id' => $project_id,
				// 'activity_id' => $activity_id,
			),
            'fields' => array('project_finance_plus_id', 'model', 'year', 'value', 'type')
        ));
        // debug($project_id); exit;
        $financeDetails = array();
        $yearOfFinances = array();
        $totals = array();
        if( !empty($_financeDetails) ){
            foreach($_financeDetails as $financeDetail){
                $dx = $financeDetail['ProjectFinancePlusDetail'];
                $financeDetails[$dx['project_finance_plus_id']][$dx['model'] . '_' . $dx['year']] = $dx;
                $yearOfFinances[$dx['type']][$dx['year']] = $dx['year'];
                if(empty($totals[$dx['type']][$dx['model']])){
                    $totals[$dx['type']][$dx['model']] = 0;
                }
                $totals[$dx['type']][$dx['model']] += $dx['value'];
            }
        }
        if( !empty($yearOfFinances['inv']) ){
            $invStartData = strtotime(date('d-m') . '-' . min($yearOfFinances['inv']));
            $invEndData = strtotime(date('d-m') . '-' . max($yearOfFinances['inv']));
            if(empty($this->params['url']['inv_start']) && $invStartData < $invStart){
                $invStart = $invStartData;
            }
            if(empty($this->params['url']['inv_end']) && $invEndData > $invEnd){
                $invEnd = $invEndData;
            }
        }
        if( !empty($yearOfFinances['fon']) ){
            $fonStartData = strtotime(date('d-m') . '-' . min($yearOfFinances['fon']));
            $fonEndData = strtotime(date('d-m') . '-' . max($yearOfFinances['fon']));
            if(empty($this->params['url']['fon_start']) && $fonStartData < $fonStart){
                $fonStart = $fonStartData;
            }
            if(empty($this->params['url']['fon_end']) && $fonEndData > $fonEnd){
                $fonEnd = $fonEndData;
            }
        }
        $saveHistory = array(
            'inv_start' => $invStart,
            'inv_end' => $invEnd,
            'fon_start' => $fonStart,
            'fon_end' => $fonEnd,
            'company_id' => $company_id,
            'project_id' => $project_id
        );
        if( !empty($getSaveHistory) && !empty($getSaveHistory['ProjectFinancePlusDate']['id']) ){
            $this->ProjectFinancePlusDate->id = $getSaveHistory['ProjectFinancePlusDate']['id'];
        } else {
            $this->ProjectFinancePlusDate->create();
        }
        $this->ProjectFinancePlusDate->save($saveHistory);
        $this->loadModels('HistoryFilter');
        $history = $this->HistoryFilter->find('first', array(
            'conditions' => array(
                'path' => $this->params['url']['url'],
                'employee_id' => $employee_info['Employee']['id']
            ),
            'fields' => array('params')
        ));
        if( !empty($history) ){
            $history = json_decode($history['HistoryFilter']['params'], true);
        } else {
            $history = array();
        }
        $this->set(compact('invStart', 'invEnd', 'fonStart', 'fonEnd', 'project_id', 'projects', 'finances', 'financeDetails', 'projectName', 'history', 'totals', 'bg_currency'));
    }

    public function update_finance($type){
        $result = false;
        $this->layout = false;
        /**
         * Save Data
         */
        if( !empty($this->data) ){
            $datas = $this->data;
            $this->ProjectFinancePlus->create();
            if( !empty($datas['id']) ){
                $this->ProjectFinancePlus->id = $datas['id'];
            }
            $saveFins = array(
                'project_id' => $datas['project_id'],
                'activity_id' => $datas['activity_id'],
                'company_id' => $datas['company_id'],
                'project_id' => $datas['project_id'],
                'name' => ($type == 'inv') ? $this->data['inv_name'] : $this->data['fon_name'],
                'type' => $type
            );
            unset($datas['id']);
            unset($datas['project_id']);
            unset($datas['activity_id']);
            unset($datas['company_id']);
            unset($datas['inv_name']);
            unset($datas['fon_name']);
            /**
             * Save name and type finance
             */
            if( $this->ProjectFinancePlus->save($saveFins) ){
                $lastId = $this->ProjectFinancePlus->id;
                if( !empty($datas) ){
                    foreach($datas as $key => $data){
                        $key = explode('_', $key);
                        $saved = array(
                            'project_id' => $this->data['project_id'],
                            'activity_id' => $this->data['activity_id'],
                            'company_id' => $this->data['company_id'],
                            'project_finance_plus_id' => $lastId,
                            'model' => $key[1],
                            'year' => $key[2],
                            'type' => $type
                        );
                        $last = $this->ProjectFinancePlusDetail->find('first', array(
                            'recursive' => -1,
                            'conditions' => $saved,
                            'fields' => array('id')
                        ));
                        $this->ProjectFinancePlusDetail->create();
                        if( !empty($last) && !empty($last['ProjectFinancePlusDetail']['id']) ){
                            $this->ProjectFinancePlusDetail->id = $last['ProjectFinancePlusDetail']['id'];
                        }
                        $saved['value'] = $data;
                        $this->ProjectFinancePlusDetail->save($saved);
                    }
                }
                $result = true;
                $this->data['id'] = $lastId;
            } else {
                $this->Session->setFlash(__('Not Saved.', true), 'error');
            }
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }
    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete_finance($id = null, $project_id = null) {
        $fonStart = !empty($this->params['url']['fon_start']) ? $this->params['url']['fon_start'] : date('d-m-Y', time());
        $fonEnd = !empty($this->params['url']['fon_end']) ? $this->params['url']['fon_end'] : date('d-m-Y', time());
        $invStart = !empty($this->params['url']['inv_start']) ? $this->params['url']['inv_start'] : date('d-m-Y', time());
        $invEnd = !empty($this->params['url']['inv_end']) ? $this->params['url']['inv_end'] : date('d-m-Y', time());
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Finance', true), 'error');
            $this->redirect(array('action' => 'index_plus', $project_id, '?' => array('fon_start' => $fonStart, 'fon_end' => $fonEnd, 'inv_start' => $invStart, 'inv_end' => $invEnd)));
        }
        if ($this->_checkRole(true, $project_id)) {
            if ($this->ProjectFinancePlus->delete($id)) {
                $this->ProjectFinancePlusDetail->deleteAll(array('ProjectFinancePlusDetail.project_finance_plus_id' => $id), false);
                // $this->Session->setFlash(__('Deleted.', true), 'success');
                $this->redirect(array('action' => 'index_plus', $project_id, '?' => array('fon_start' => $fonStart, 'fon_end' => $fonEnd, 'inv_start' => $invStart, 'inv_end' => $invEnd)));
            }
            $this->Session->setFlash(__('Not deleted', true), 'error');
        }
        $this->redirect(array('action' => 'index_plus', $project_id, '?' => array('fon_start' => $fonStart, 'fon_end' => $fonEnd, 'inv_start' => $invStart, 'inv_end' => $invEnd)));
    }
    function plus($project_id){
        $this->loadModels('ProjectFinanceTwoPlus', 'ProjectFinanceTwoPlusDetail', 'ProjectFinanceTwoPlusDate');
        /**
         * Lay start date va end date
         */
        if (!$this->_checkRole(false, $project_id)) {
            $this->data = null;
        }
        $this->_checkWriteProfile('finance_two_plus');
		$bg_currency = $this->getCurrencyOfBudget();
        $employee_info = $this->employee_info;
        $start = $end = '';
        $getSaveHistory = $this->ProjectFinanceTwoPlusDate->find('first', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id)
        ));
        if(!empty($getSaveHistory)) {
            $start = !empty($getSaveHistory['ProjectFinanceTwoPlusDate']['start']) ? $getSaveHistory['ProjectFinanceTwoPlusDate']['start'] : '';
            $end = !empty($getSaveHistory['ProjectFinanceTwoPlusDate']['end']) ? $getSaveHistory['ProjectFinanceTwoPlusDate']['end'] : '';
        }
        $start = !empty($this->params['url']['inv_start']) ? strtotime(@$this->params['url']['inv_start']) : (!empty($start) ? $start : time());
        $end = !empty($this->params['url']['inv_end']) ? strtotime(@$this->params['url']['inv_end']) : (!empty($end) ? $end : time());
        /**
         * Lay project
         */
        $projects = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('Project.id' => $project_id),
            'fields' => array('id', 'project_name', 'company_id')
        ));
        $company_id = !empty($projects['Project']['company_id']) ? $projects['Project']['company_id'] : 0;
        $projectName = !empty($projects['Project']['project_name']) ? $projects['Project']['project_name'] : '';
        /**
         * Lay cac finance+
         */
        $finances = $this->ProjectFinanceTwoPlus->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('id', 'name'),
        ));
        /**
         * Finance detail
         */
        $_financeDetails = $this->ProjectFinanceTwoPlusDetail->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
        ));
        $financeDetails = array();
        $yearOfFinances = array();
        $total = array();
        if( !empty($_financeDetails) ){
            foreach($_financeDetails as $financeDetail){
                $dx = $financeDetail['ProjectFinanceTwoPlusDetail'];
                $_dx = $dx;
                unset($_dx['id']);
                unset($_dx['project_id']);
                unset($_dx['project_finance_two_plus_id']);
                unset($_dx['year']);
                unset($_dx['created']);
                unset($_dx['updated']);
                $financeDetails[$dx['project_finance_two_plus_id']][$dx['year']] = $_dx;
                $yearOfFinances[$dx['year']] = $dx['year'];
                if(!isset($total['budget_revised'])){
                    $total['budget_revised'] = 0;
                }
                $total['budget_revised'] += !empty($dx['budget_revised']) ? $dx['budget_revised'] : 0;
                if(!isset($total['last_estimated'])){
                    $total['last_estimated'] = 0;
                }
                $total['last_estimated'] += !empty($dx['last_estimated']) ? $dx['last_estimated'] : 0;
                if(!isset($total['engaged'])){
                    $total['engaged'] = 0;
                }
                $total['engaged'] += !empty($dx['engaged']) ? $dx['engaged'] : 0;
                if(!isset($total['bill'])){
                    $total['bill'] = 0;
                }
                $total['bill'] += $dx['bill'];
                if(!isset($total['disbursed'])){
                    $total['disbursed'] = 0;
                }
                $total['disbursed'] += !empty($dx['disbursed']) ? $dx['disbursed'] : 0;
            }
        }
        if( !empty($yearOfFinances) ){
            $StartData = strtotime(date('d-m') . '-' . min($yearOfFinances));
            $EndData = strtotime(date('d-m') . '-' . max($yearOfFinances));
            if(empty($this->params['url']['start']) && $StartData < $start){
                $invStart = $StartData;
            }
            if(empty($this->params['url']['end']) && $EndData > $end){
                $end = $EndData;
            }
        }
        $saveHistory = array(
            'start' => $start,
            'end' => $end,
            'project_id' => $project_id
        );
        if(!empty($this->params['url']['inv_start']) && !empty($this->params['url']['inv_end'])){
            if( !empty($getSaveHistory) && !empty($getSaveHistory['ProjectFinanceTwoPlusDate']['id']) ){
                $this->ProjectFinanceTwoPlusDate->id = $getSaveHistory['ProjectFinanceTwoPlusDate']['id'];
            } else {
                $this->ProjectFinanceTwoPlusDate->create();
            }
            $this->ProjectFinanceTwoPlusDate->save($saveHistory);
        }
        $this->set(compact('project_id', 'financeDetails', 'start', 'end', 'projects', 'projectName', 'finances', 'total', 'bg_currency'));
    }
    public function update_finance_two_plus(){
        $result = false;
        $this->layout = false;
        $this->loadModels('ProjectFinanceTwoPlus', 'ProjectFinanceTwoPlusDetail');
        if( !empty($this->data) ){
            $datas = $this->data;
            $this->ProjectFinanceTwoPlus->create();
            if( !empty($datas['id']) ){
                $this->ProjectFinanceTwoPlus->id = $datas['id'];
            }
            $saveFins = array(
                'company_id' => $datas['company_id'],
                'project_id' => $datas['project_id'],
                'name' => $this->data['name'],
            );
            unset($datas['id']);
            unset($datas['project_id']);
            unset($datas['company_id']);
            unset($datas['name']);
            /**
             * Save name and type finance
             */
            if( $this->ProjectFinanceTwoPlus->save($saveFins) ){
                $lastId = $this->ProjectFinanceTwoPlus->id;
                if( !empty($datas) ){
                    foreach($datas as $key => $data){
                        $key = explode('-', $key);
                        $saved = array(
                            'project_id' => $this->data['project_id'],
                            'project_finance_two_plus_id' => $lastId,
                            'year' => $key[1],
                        );
                        $last = $this->ProjectFinanceTwoPlusDetail->find('first', array(
                            'recursive' => -1,
                            'conditions' => $saved,
                            'fields' => array('id')
                        ));
                        $_saved = array(
                            $key[0] => $data
                        );
                        $saved = array_merge($saved, $_saved);
                        $this->ProjectFinanceTwoPlusDetail->create();
                        if( !empty($last) && !empty($last['ProjectFinanceTwoPlusDetail']['id']) ){
                            $this->ProjectFinanceTwoPlusDetail->id = $last['ProjectFinanceTwoPlusDetail']['id'];
                        }
                        $saved['value'] = $data;
                        $this->ProjectFinanceTwoPlusDetail->save($saved);
                    }
                }
                $result = true;
                $this->data['id'] = $lastId;
            } else {
                $this->Session->setFlash(__('Not Saved.', true), 'error');
            }
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }
    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete_finance_two_plus($id = null, $project_id = null) {
        $fonStart = !empty($this->params['url']['fon_start']) ? $this->params['url']['fon_start'] : date('d-m-Y', time());
        $fonEnd = !empty($this->params['url']['fon_end']) ? $this->params['url']['fon_end'] : date('d-m-Y', time());
        $invStart = !empty($this->params['url']['inv_start']) ? $this->params['url']['inv_start'] : date('d-m-Y', time());
        $invEnd = !empty($this->params['url']['inv_end']) ? $this->params['url']['inv_end'] : date('d-m-Y', time());
        $this->loadModels('ProjectFinanceTwoPlus', 'ProjectFinanceTwoPlusDetail');
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Finance', true), 'error');
            $this->redirect(array('action' => 'plus', $project_id, '?' => array('fon_start' => $fonStart, 'fon_end' => $fonEnd, 'inv_start' => $invStart, 'inv_end' => $invEnd)));
        }
        if ($this->_checkRole(true, $project_id)) {
            if ($this->ProjectFinanceTwoPlus->delete($id)) {
                $this->ProjectFinanceTwoPlusDetail->deleteAll(array('ProjectFinanceTwoPlusDetail.project_finance_two_plus_id' => $id), false);
                // $this->Session->setFlash(__('Deleted.', true), 'success');
                $this->redirect(array('action' => 'plus', $project_id, '?' => array('fon_start' => $fonStart, 'fon_end' => $fonEnd, 'inv_start' => $invStart, 'inv_end' => $invEnd)));
            }
            $this->Session->setFlash(__('Not deleted', true), 'error');
        }
        $this->redirect(array('action' => 'plus', $project_id, '?' => array('fon_start' => $fonStart, 'fon_end' => $fonEnd, 'inv_start' => $invStart, 'inv_end' => $invEnd)));
    }
}
?>
