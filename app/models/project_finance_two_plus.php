<?php
/**
 * z0 Gravity�
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectFinanceTwoPlus extends AppModel {
    var $name = 'ProjectFinanceTwoPlus';
    var $hasMany = array(
        'ProjectFinanceTwoPlusDetail' => array(
            'className' => 'ProjectFinanceTwoPlusDetail',
            'foreignKey' => 'project_finance_two_plus_id',
            'dependent' => true,
        )
    );
    public function defaultField($company_id = null){
        $results = array(
            'ProjectFinanceTwoPlus.budget_initial' => 'Budget initial',
            'ProjectFinanceTwoPlus.budget_revised' => 'Budget revised',
            'ProjectFinanceTwoPlus.last_estimated' => 'Latest estimate',
            'ProjectFinanceTwoPlus.percent' => '%',
            'ProjectFinanceTwoPlus.dr_de' => 'DR - DE',
            'ProjectFinanceTwoPlus.engaged' => 'Engaged',
            'ProjectFinanceTwoPlus.bill' => 'Bill',
            'ProjectFinanceTwoPlus.disbursed' => 'Disbursed',
        );
        $ProjectFinanceTwoPlusDetail = ClassRegistry::init('ProjectFinanceTwoPlusDetail');
        $ProjectFinanceTwoPlusDate = ClassRegistry::init('ProjectFinanceTwoPlusDate');
        $Project = ClassRegistry::init('Project');
        $listProject = $Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id
            ),
            'fields' => array('id', 'id')
        ));
        $financeTwoPlus = $ProjectFinanceTwoPlusDetail->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $listProject),
            'fields' => array(
                'MIN(year) AS startDate',
                'MAX(year) AS endDate'
            )
        ));
        $financeDates = $ProjectFinanceTwoPlusDate->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $listProject),
            'fields' => array(
                'MIN(start) AS fonStart',
                'MAX(end) AS fonEnd'
            )
        ));
        $startDate = !empty($financeTwoPlus[0][0]['startDate']) ? $financeTwoPlus[0][0]['startDate'] : date('Y', time());
        $endDate = !empty($financeTwoPlus[0][0]['endDate']) ? $financeTwoPlus[0][0]['endDate'] : date('Y', time());
        if(!empty($financeDates) && !empty($financeDates[0][0])){
            $financeDates = $financeDates[0][0];
            $startDate = (date('Y', $financeDates['fonStart']) < $startDate) ? date('Y', $financeDates['fonStart']) : $startDate;
            $endDate = (date('Y', $financeDates['fonEnd']) > $endDate) ? date('Y', $financeDates['fonEnd']) : $endDate;
        }
        while($startDate <= $endDate){
            $results['ProjectFinanceTwoPlus.budget_initial_' . $startDate] = 'Budget initial (' . $startDate . ')';
            $results['ProjectFinanceTwoPlus.budget_revised_' . $startDate] = 'Budget revised (' . $startDate . ')';
            $results['ProjectFinanceTwoPlus.last_estimated_' . $startDate] = 'Latest estimate (' . $startDate . ')';
            $results['ProjectFinanceTwoPlus.percent_' . $startDate] = '% (' . $startDate . ')';
            $results['ProjectFinanceTwoPlus.dr_de_' . $startDate] = 'DR - DE (' . $startDate . ')';
            $results['ProjectFinanceTwoPlus.engaged_' . $startDate] = 'Engaged (' . $startDate . ')';
            $results['ProjectFinanceTwoPlus.bill_' . $startDate] = 'Bill (' . $startDate . ')';
            $results['ProjectFinanceTwoPlus.disbursed_' . $startDate] = 'Disbursed (' . $startDate . ')';
            $startDate++;
        }
        return $results;
    }
}
?>
