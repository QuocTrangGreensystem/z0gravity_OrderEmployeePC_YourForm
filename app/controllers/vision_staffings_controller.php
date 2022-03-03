<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class VisionStaffingsController extends AppController {
    var $uses = array('Activity', 'ActivityFamily', 'TmpStaffingSystem', 'ActivityBudget');
    //public $tree = array();         //store tree result
    public $families = array();     //store all families
    public $familyTree = array();

    public function beforeFilter(){
        parent::beforeFilter();
    }
    /**
     * Index
     */
    public function index(){
        $getUrlDatas = !empty($this->params['url']) ? $this->params['url'] : array();
        $employeeName = $this->_getEmpoyee();
        if(!empty($getUrlDatas)){
            /**
             * Staffing Month
             */
            if(isset($getUrlDatas['vsShowBy']) && $getUrlDatas['vsShowBy'] == 'month'){
                if(isset($getUrlDatas['vsType']) && $getUrlDatas['vsType'] == 'ac'){ // show by activity
                    if(!empty($getUrlDatas['vsEmploy'])){ // co chon employee
                        $this->_showByMonthInActivityWithEmploy($employeeName, $getUrlDatas);
                    } else {
                        if(!empty($getUrlDatas['vsProfit'])){  // Co chon Profit Center
                            
                        } else { // khong chon employee va profit center
                            
                        }
                    }
                } elseif(isset($getUrlDatas['vsType']) && $getUrlDatas['vsType'] == 'pc'){ // show by profit center
                    
                } else { // show by profile
                    
                }
            } else {
                
            }
        }
        exit;
    }
    
    /**
     * Show by month in activity with employee
     */
    private function _showByMonthInActivityWithEmploy($employeeName, $getUrlDatas = array()){
        debug($getUrlDatas);
        $conditions['company_id'] = $employeeName['company_id']; // dk = company id
        if(!empty($getUrlDatas['vsAcName'])){ // neu co chon activity thi chi lay du lieu thuoc cac activity do
            $conditions['id'] = $getUrlDatas['vsAcName'];
        }
        /**
         * Lay cac activity theo danh sach dc chon
         */
        $activities = $this->Activity->find('all', array(
            'recursive' => -1,
			'order' => array('family_id', 'subfamily_id', 'id'),
            'conditions' => array($conditions),
            'fields' => array('id', 'name', 'family_id','subfamily_id', 'pms')
        ));
        /**
         * Format star date va end date sang kieu so
         */
        $start = strtotime('01-' . $getUrlDatas['vsFromMonth'] . '-' . $getUrlDatas['vsFromYear']);
		$end = strtotime('01-' . $getUrlDatas['vsToMonth'] . '-' . $getUrlDatas['vsToYear']);
        
        $staffings = $this->_activityWithEmploy($employeeName, $activities, $getUrlDatas['vsEmploy'], $start, $end, false, 'month');
        debug($activities); exit;
    }
    /**
     * Lay du lieu staffing
     */
    private function _activityWithEmploy($employeeName, $activities = null, $conditions = null, $start = null, $end = null, $onlyEmployee = false, $dateType = null){
        $fields = array(
			'TmpStaffingSystem.model_id', 'TmpStaffingSystem.model',
			'project_id', 'activity_id', 'SUM(TmpStaffingSystem.consumed) as consumed',
			'TmpStaffingSystem.company_id', 'TmpStaffingSystem.date', 'SUM(TmpStaffingSystem.estimated) as estimated',
			'Activity.id', 'Activity.name', 'Activity.family_id', 'Activity.subfamily_id'
		);
    	$joins = array(
			array(
				'table' => 'activities',
				'alias' => 'Activity',
				'type' => 'LEFT',
				'foreignKey' => 'activity_id',
				'conditions'=> array(
					'TmpStaffingSystem.activity_id = Activity.id', 
				)
			)
		);
        $group=array('TmpStaffingSystem.model_id', 'TmpStaffingSystem.activity_id', 'TmpStaffingSystem.date');
        $listIdActivitys = Set::classicExtract($activities, '{n}.Activity.id');
		$getDatas = $this->TmpStaffingSystem->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'TmpStaffingSystem.activity_id' => $listIdActivitys,
				'date BETWEEN ? AND ?' => array($start, $end),
				'TmpStaffingSystem.model_id' => $conditions,
				'model' => 'employee',
				'TmpStaffingSystem.company_id' => $employeeName['company_id']
			),
			'fields' => $fields,
			'joins' => $joins,
			'order' => array('TmpStaffingSystem.model_id','Activity.family_id','Activity.subfamily_id','TmpStaffingSystem.activity_id'),
			'group' => $group
		));
        debug($getDatas); exit;
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
		$this->employee_info['Employee']['day_established'] = $this->employee_info['Company']['day_established'];
        $this->set('company_id', $this->employee_info['Company']['id']);
        $this->set('employee_id', $this->employee_info['Employee']['id']);
        return $this->employee_info['Employee'];
    }
    /**
    *
    * Run once: chi can goi 1 lan de lay tat ca family
    *
    */

    function getFamilies(){
        $employeeName = $this->_getEmpoyee();
        $conditions = array('company_id' => $employeeName['company_id']);
        // if( $families ){
        //     $conditions['id'] = $families;
        // }
        $result = array();
        $data = $this->ActivityFamily->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'name', 'parent_id'),
            'conditions' => $conditions,
            'order' => array('parent_id' => 'ASC')
        ));
        //now build tree
        foreach($data as $family){
            $fa = $family['ActivityFamily'];
            $key = 'family-' . $fa['id'];
            $this->familyRefer[ $fa['id'] ] = $fa['parent_id'];
            if( !$fa['parent_id'] ){
                $result[ $key ] = $fa;
                $result[ $key ]['children'] = array();
            } else {
                $result[ 'family-' . $fa['parent_id'] ]['children'][ $key ] = $fa;
            }
        }
        $this->familyTree = $result;
        $this->families = Set::combine($data, '{n}.ActivityFamily.id', '{n}.ActivityFamily');
    }
    /**
    *
    * Families: array(id1, id2, ...), ko dc vua co id parent vua co id children
    * parent: array
    * return: array(parent['children'] => [families selected])
    */
    function buildFamilyTree($families){
        $result = array();
        foreach ($families as $id) {
            $key = 'family-' . $id;
            $family = $this->families[$id];
            if( !$family['parent_id'] ){
                if( !isset($result[$key]) ){
                    $result[$key] = $family;
                    $result[$key]['children'] = array();
                }
            } else {
                $parent = 'family-' . $family['parent_id'];
                if( !isset($result[$parent]) ){
                    $result[$parent] = $this->families[ $family['parent_id'] ];
                    $result[$parent]['children'] = array();
                }
                //append con vao
                if( !isset($result[$parent]['children'][$key]) ){
                    $result[$parent]['children'][$key] = $family;
                }
            }
        }
        return $result;
    }
    function test(){
        $this->getFamilies();
        $families = array(337);
        $tree = $this->buildFamilyTree($families);
        //$tree = $this->familyTree;
        $from = strtotime('2015-09-01 00:00:00');
        $to = strtotime('2015-10-31 00:00:00');
        $activities = $this->Activity->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'name', 'subfamily_id', 'family_id'),
            //condition de tim cac family o day...
            'conditions' => array('family_id' => $families)
        ));
        $ids = Set::classicExtract($activities, '{n}.Activity.id');
        $staffing = $this->TmpStaffingSystem->find('all', array(
            'conditions' => array(
                'activity_id' => $ids,
                'model' => 'employee',
                'date BETWEEN ? AND ?' => array($from, $to)
            ),
            'fields' => array('activity_id', 'date', 'SUM(estimated) as workload', 'SUM(consumed) as consumption'),
            'group' => array('activity_id', 'date'),
            'order' => array('date' => 'ASC')
        ));
        pr($staffing);
        die;
    }
}
?>