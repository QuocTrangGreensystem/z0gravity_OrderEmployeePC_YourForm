<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ExpectationsController extends AppController {
    public $pages = array(
        'expectation_1',
        'expectation_2',
        'expectation_3',
        'expectation_4',
        'expectation_5',
        'expectation_6',
    );
    public function beforeFilter(){
        parent::beforeFilter();
    }
    function index($currentPage = null){
        if( !$currentPage || !in_array($currentPage, $this->pages) )$currentPage = Inflector::slug($this->pages[0]);
        $nameAlert = $this->autoInsert($currentPage);
        $company_id = $this->employee_info['Company']['id'];
        $fields = $this->Expectation->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'page' => $currentPage
            ),
            'order' => array('weight ASC')
        ));
        $this->loadModel('ExpectationTranslation');
        $langCode = Configure::read('Config.langCode');
        $f = ($langCode == 'fr') ? array('original_text', 'fre') : array('original_text', 'eng');
        $translations = $this->ExpectationTranslation->find('list', array(
            'recursive' => -1,
            'conditions' => array( 'company_id' => $company_id ),
            'fields' => $f
        ));
        $this->set(compact('fields', 'company_id', 'translations', 'nameAlert'));
    }

    public function autoInsert($currentPage, $company_id = null){
        if($company_id == null){
            $company_id = $this->employee_info['Company']['id'];
        }
        $origins = array(
            array('List 1', '', 'list_1', 0, 1),
            array('List 2', '', 'list_2', 0, 2),
            array('List 3', '', 'list_3', 0, 3),
            array('List 4', '', 'list_4', 0, 4),
            array('List 5', '', 'list_5', 0, 5),
            array('List 6', '', 'list_6', 0, 6),
            array('List 7', '', 'list_7', 0, 7),
            array('List 8', '', 'list_8', 0, 8),
            array('List 9', '', 'list_9', 0, 9),
            array('List 10', '', 'list_10', 0, 10),
            array('List 11', '', 'list_11', 0, 11),
            array('List 12', '', 'list_12', 0, 12),
            array('List 13', '', 'list_13', 0, 13),
            array('List 14', '', 'list_14', 0, 14),
            array('List 15', '', 'list_15', 0, 15),
            array('List 16', '', 'list_16', 0, 16),
            array('List 17', '', 'list_17', 0, 17),
            array('List 18', '', 'list_18', 0, 18),
            array('List 19', '', 'list_19', 0, 19),
            array('List 20', '', 'list_20', 0, 20),
            array('List 21', '', 'list_21', 0, 21),
            array('List 22', '', 'list_22', 0, 22),
            array('List 23', '', 'list_23', 0, 23),
            array('List 24', '', 'list_24', 0, 24),
            array('List 25', '', 'list_25', 0, 25),
            array('List 26', '', 'list_26', 0, 26),
            array('List 27', '', 'list_27', 0, 27),
            array('List 28', '', 'list_28', 0, 28),
            array('List 29', '', 'list_29', 0, 29),
            array('List 30', '', 'list_30', 0, 30),
            array('Date 1', '', 'date_1', 0, 31),
            array('Date 2', '', 'date_2', 0, 32),
            array('Date 3', '', 'date_3', 0, 33),
            array('Date 4', '', 'date_4', 0, 34),
            array('Date 5', '', 'date_5', 0, 35),
            array('Date 6', '', 'date_6', 0, 36),
            array('Date 7', '', 'date_7', 0, 37),
            array('Date 8', '', 'date_8', 0, 38),
            array('Date 9', '', 'date_9', 0, 39),
            array('Date 10', '', 'date_10', 0, 40),
            array('Text long 1', '', 'text_long_1', 0, 41),
            array('Text long 2', '', 'text_long_2', 0, 42),
            array('Text long 3', '', 'text_long_3', 0, 43),
            array('Text long 4', '', 'text_long_4', 0, 44),
            array('Text long 5', '', 'text_long_5', 0, 45),
            array('Text short 1', '', 'text_short_1', 0, 46),
            array('Text short 2', '', 'text_short_2', 0, 47),
            array('Text short 3', '', 'text_short_3', 0, 48),
            array('Text short 4', '', 'text_short_4', 0, 49),
            array('Text short 5', '', 'text_short_5', 0, 50),
            array('Text short 6', '', 'text_short_6', 0, 51),
            array('Text short 7', '', 'text_short_7', 0, 52),
            array('Text short 8', '', 'text_short_8', 0, 53),
            array('Text short 9', '', 'text_short_9', 0, 54),
            array('Text short 10', '', 'text_short_10', 0, 55),
            array('Attached document', '', 'attached_documents', 0, 56),
            array('Text', '', 'text', 0, 57),
            array('Milestone', '', 'milestone', 0, 58),
            array('Date', '', 'milestone_date', 0, 59),
            array('Assigned to 1', '', 'assigned_to_1', 0, 60),
            array('Assigned to 2', '', 'assigned_to_2', 0, 61),
            array('Assigned to 3', '', 'assigned_to_3', 0, 62),
            array('List color 1', '', 'list_color_1', 0, 63),
            array('List color 2', '', 'list_color_2', 0, 64),
            array('List color 3', '', 'list_color_3', 0, 65),
            array('List color 4', '', 'list_color_4', 0, 66),
            array('List color 5', '', 'list_color_5', 0, 67),
        );
        $i = 1;
        foreach ($origins as $text) {
            $check = $this->Expectation->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $this->employee_info['Company']['id'],
                    'original_text' => $text[0],
                    'page' => $currentPage,
                    'field' => $text[2],
                )
            ));
            if( !$check ){
                //insert
                $this->Expectation->create();
                $this->Expectation->save(array(
                    'company_id' => $this->employee_info['Company']['id'],
                    'original_text' => $text[0],
                    'page' => $currentPage,
                    'field' => $text[2],
                    'display' => $text[3],
                    'weight' => $text[4]
                ));
            }
            $i++;
        }
        // doan nay kiem tra va them alert.
        $this->loadModels('ProjectAlert');
        $alert = $this->ProjectAlert->find('all', array(
            'recusive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
                'display' => 1
            ),
            'fields' => array('id', 'alert_name', 'number_of_day'),
            'order' => array('alert_name ASC')
        ));
        $nameAlert = array();
        foreach ($alert as $key => $value) {
            $dx = $value['ProjectAlert'];
            $check = $this->Expectation->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $this->employee_info['Company']['id'],
                    'original_text' => 'alert_' . $dx['id'],
                    'page' => $currentPage,
                    'field' => 'alert_' . $dx['id'],
                )
            ));
            if( !$check ){
                //insert
                $this->Expectation->create();
                $this->Expectation->save(array(
                    'company_id' => $this->employee_info['Company']['id'],
                    'original_text' => 'alert_' . $dx['id'],
                    'page' => $currentPage,
                    'field' => 'alert_' . $dx['id'],
                    'display' => 0,
                    'weight' => $i
                ));
            }
            $i++;
            $nameAlert['alert_' . $dx['id']] = $dx['alert_name'] . __(' D-', true) . $dx['number_of_day'];
        }
        return $nameAlert;
    }
    /**
     * update
     *
     * @return void
     * @access public
     */
    public function update() {
        $result = false;
        $this->layout = false;
        $company_id = $this->employee_info['Company']['id'];
        if (!empty($this->data)) {
            if (!empty($this->data['id'])) {
                $this->Expectation->id = $this->data['id'];
            } else {
                $this->Expectation->create();
            }
            unset($this->data['id']);
            $saved = array(
                'display' => $this->data['display'] == 'yes' ? 1 : 0,
            );
            if ($this->Expectation->save($saved)) {
                $result = true;
                // $this->Session->setFlash(__('OK.', true), 'success');
            } else {
                $this->Session->setFlash(__('KO.', true), 'error');
            }
            $this->data['id'] = $this->Expectation->id;
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }
    /**
     * Order
     *
     * @return void
     * @access public
     */
    public function order($company_id = null) {
        if (!empty($this->data)) {
            foreach ($this->data as $id => $weight) {
                if (!empty($id) && !empty($weight) && $weight!=0) {
                    $this->Expectation->id = $id;
                    $this->Expectation->saveField('weight', $weight);
                }
            }
        }
        die;
    }
}
