<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectFinance extends AppModel {
    var $belongsTo = array(
        'Project' => array(
            'className' => 'Project',
            'foreignKey' => 'project_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    var $name = 'ProjectFinance';
    public function defaultFields($company_id = null){
        $results = array(
            'ProjectFinance.bp_investment_city' => 'BP Investment Ville',
            'ProjectFinance.bp_operation_city'	=> 'BP Operation Ville',
            'ProjectFinance.available_investment' => 'Available Investment',
            'ProjectFinance.available_operation' => 'Available Operation',
            'ProjectFinance.finance_total_budget' => 'Budget Total',
        );
        return $results;
    }
    public function defaultFieldPlus($company_id = null){
		 $results = array(
            'ProjectFinancePlus.inv_budget' => 'Investment Total Budget',
            'ProjectFinancePlus.inv_avancement' => 'Investment Total Avancement',
            'ProjectFinancePlus.inv_percent' => 'Investment Total Percent',
            'ProjectFinancePlus.finaninv_budget' => 'Finance Investment Total Budget',
            'ProjectFinancePlus.finaninv_avancement' => 'Finance Investment Total Avancement',
            'ProjectFinancePlus.finaninv_percent' => 'Finance Investment Total Percent',
            'ProjectFinancePlus.fon_budget' => 'Operation Total Budget',
            'ProjectFinancePlus.fon_avancement' => 'Operation Total Avancement',
            'ProjectFinancePlus.fon_percent' => 'Operation Total Percent',
            'ProjectFinancePlus.finanfon_budget' => 'Finance Operation Total Budget',
            'ProjectFinancePlus.finanfon_avancement' => 'Finance Operation Total Avancement',
            'ProjectFinancePlus.finanfon_percent' => 'Finance Operation Total Percent'
        );
        $ProjectFinancePlusDetail = ClassRegistry::init('ProjectFinancePlusDetail');
        $ProjectFinancePlusDate = ClassRegistry::init('ProjectFinancePlusDate');
        $financePlus = $ProjectFinancePlusDetail->find('all', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array(
                'MIN(year) AS startDate',
                'MAX(year) AS endDate'
            )
        ));
        $financeDates = $ProjectFinancePlusDate->find('all', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array(
                'MIN(inv_start) AS invStart',
                'MAX(inv_end) AS invEnd',
                'MIN(finaninv_start) AS finanInvStart',
                'MAX(finaninv_end) AS finanInvEnd',
                'MIN(fon_start) AS fonStart',
                'MAX(fon_end) AS fonEnd',
                'MIN(finanfon_start) AS finanFonStart',
                'MAX(finanfon_end) AS finanFonEnd'
            )
        ));
        $startDate = !empty($financePlus[0][0]['startDate']) ? $financePlus[0][0]['startDate'] : date('Y', time());
        $endDate = !empty($financePlus[0][0]['endDate']) ? $financePlus[0][0]['endDate'] : date('Y', time());
        if(!empty($financeDates) && !empty($financeDates[0][0])){
            $financeDates = $financeDates[0][0];
            $startDate = (date('Y', $financeDates['invStart']) < $startDate) ? date('Y', $financeDates['invStart']) : $startDate;
            $endDate = (date('Y', $financeDates['invEnd']) > $endDate) ? date('Y', $financeDates['invEnd']) : $endDate;
            $startDate = (date('Y', $financeDates['finanInvStart']) < $startDate) ? date('Y', $financeDates['finanInvStart']) : $startDate;
            $endDate = (date('Y', $financeDates['finanInvEnd']) > $endDate) ? date('Y', $financeDates['finanInvEnd']) : $endDate;
            $startDate = (date('Y', $financeDates['fonStart']) < $startDate) ? date('Y', $financeDates['fonStart']) : $startDate;
            $endDate = (date('Y', $financeDates['fonEnd']) > $endDate) ? date('Y', $financeDates['fonEnd']) : $endDate;
            $startDate = (date('Y', $financeDates['finanFonStart']) < $startDate) ? date('Y', $financeDates['finanFonStart']) : $startDate;
            $endDate = (date('Y', $financeDates['finanFonEnd']) > $endDate) ? date('Y', $financeDates['finanFonEnd']) : $endDate;
        }
        while($startDate <= $endDate){
            $results['ProjectFinancePlus.inv_budget_' . $startDate] = 'Investment Budget ' . $startDate;
            $results['ProjectFinancePlus.inv_avancement_' . $startDate] = 'Investment Avancement ' . $startDate;
            $results['ProjectFinancePlus.inv_percent_' . $startDate] = 'Investment Percent ' . $startDate;
            $results['ProjectFinancePlus.finaninv_budget_' . $startDate] = 'Finance Investment Budget ' . $startDate;
            $results['ProjectFinancePlus.finaninv_avancement_' . $startDate] = 'Finance Investment Avancement ' . $startDate;
            $results['ProjectFinancePlus.finaninv_percent_' . $startDate] = 'Finance Investment Percent ' . $startDate;
            $results['ProjectFinancePlus.fon_budget_' . $startDate] = 'Operation Budget ' . $startDate;
            $results['ProjectFinancePlus.fon_avancement_' . $startDate] = 'Operation Avancement ' . $startDate;
            $results['ProjectFinancePlus.fon_percent_' . $startDate] = 'Operation Percent ' . $startDate;
            $results['ProjectFinancePlus.finanfon_budget_' . $startDate] = 'Finance Operation Budget ' . $startDate;
            $results['ProjectFinancePlus.finanfon_avancement_' . $startDate] = 'Finance Operation Avancement ' . $startDate;
            $results['ProjectFinancePlus.finanfon_percent_' . $startDate] = 'Finance Operation Percent ' . $startDate;
            $startDate++;
        }
        return $results;
    }
}
?>
