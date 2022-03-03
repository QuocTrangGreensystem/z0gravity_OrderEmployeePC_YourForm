<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class AbsenceRequestReport extends AppModel {
    /**
     * Name of the model.
     *
     * @var string
     */
    public $name = 'AbsenceRequestReport';
    
     /**
     * I18n validate
     *
     * @param string $field The name of the field to invalidate
     * @param mixed $value Name of validation rule that was not failed, or validation message to
     *    be returned. If no validation key is provided, defaults to true.
     * 
     * @return void
     */
    public function invalidate($field, $value = true) {
        $value = __($value, true);
        parent::invalidate($field, $value);
    }
    /**
     * Save data absence report
     * @param array $datas = array('date' => ?, 'employee_id' => ?, 'absence_id' => ?, 'response' => ?, 'moment' => ?)
     * @return void
     */
    public function saveAbsenceReport($datas = array()){
        //$Model = ClassRegistry::init('AbsenceRequestReport');
        if(!empty($datas)){
            foreach($datas as $data){
                //$last = $this->find()
            }
        }
    }
}
?>