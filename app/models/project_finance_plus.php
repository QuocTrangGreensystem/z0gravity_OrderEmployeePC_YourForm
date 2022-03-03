<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectFinancePlus extends AppModel {
    var $name = 'ProjectFinancePlus';
	//var $belongsTo = array(
//        'Project' => array(
//            'className' => 'Project',
//            'foreignKey' => 'project_id',
//            'conditions' => '',
//            'fields' => '',
//            'order' => ''
//        )
//	);
    var $hasMany = array(
		'ProjectFinancePlusDetail' => array(
            'className' => 'ProjectFinancePlusDetail',
            'foreignKey' => 'project_finance_plus_id',
            'dependent' => true,
        ),
		'ProjectFinancePlusAttachmentView' => array(
            'className' => 'ProjectFinancePlusAttachmentView',
            'foreignKey' => 'project_finance_plus_id',
            'dependent' => true,
        ),
		'ProjectFinancePlusAttachment' => array(
            'className' => 'ProjectFinancePlusAttachment',
            'foreignKey' => 'project_finance_plus_id',
            'dependent' => true,
        ),
		'ProjectFinancePlusTxt' => array(
            'className' => 'ProjectFinancePlusTxt',
            'foreignKey' => 'project_finance_plus_id',
            'dependent' => true,
        ),
		'ProjectFinancePlusTxtView' => array(
            'className' => 'ProjectFinancePlusTxtView',
            'foreignKey' => 'project_finance_plus_id',
            'dependent' => true,
        ),
    );
}
?>