<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class AppModel extends Model {

    public function beforeSave($options = array()) {
        if (!empty($this->data[$this->alias])) {
            foreach ($this->data[$this->alias] as &$val) {
                if( is_string($val) || is_numeric($val) )
                    $val = trim($val);
            }
			// This code only for mysql 5.5
			// $cols = $this->getColumnTypes();
			// foreach( $cols  as $f => $type){
				// if( $type == 'date' && (isset( $this->data[$this->alias][$f]) && $this->data[$this->alias][$f] == '0000-00-00')){
					// $this->data['Project'][$f] = null;
				// }
			// }
        }
        return parent::beforeSave($options);
    }

    public function convertTime($date) {
        if( empty($date)){ return '0000-00-00';} 
        if (!preg_match('/\d{1,2}-\d{1,2}-\d{4}/', $date)) {
            return null;
        }
        list($day, $month, $year) = explode('-', $date);
        return date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
    }

    public function unbindModelAll($list = array(), $reset = false) {
        $assocs = array('hasOne', 'hasMany', 'belongsTo', 'hasAndBelongsToMany');
        $params = array();
        foreach ($assocs as $assoc) {
            if ($list && !isset($list[$assoc])) {
                continue;
            }
            $params[$assoc] = array_keys($this->{$assoc});
        }
        if ($params) {
            $this->unbindModel($params, $reset);
        }
        return false;
    }
	public function loadModels(){
        $models = func_get_args();
        foreach($models as $model){
            $this->loadModel($model);
        }
    }
	public function loadModel($Model=null){
        if( $Model) $this->$Model = ClassRegistry::init($Model);
    }

}
