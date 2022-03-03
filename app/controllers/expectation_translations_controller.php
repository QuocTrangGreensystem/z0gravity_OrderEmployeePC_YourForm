<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ExpectationTranslationsController extends AppController {
    public function beforeFilter(){
        parent::beforeFilter();
    }
    function index(){
        $this->autoInsert();
        $company_id = $this->employee_info['Company']['id'];
        $fields = $this->ExpectationTranslation->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id
            ),
        ));
        $this->set(compact('fields', 'company_id'));
    }

    public function autoInsert($company_id = null){
        if($company_id == null){
            $company_id = $this->employee_info['Company']['id'];
        }
        $origins = array(
            array('List 1', '', 'list_1', 0),
            array('List 2', '', 'list_2', 0),
            array('List 3', '', 'list_3', 0),
            array('List 4', '', 'list_4', 0),
            array('List 5', '', 'list_5', 0),
            array('List 6', '', 'list_6', 0),
            array('List 7', '', 'list_7', 0),
            array('List 8', '', 'list_8', 0),
            array('List 9', '', 'list_9', 0),
            array('List 10', '', 'list_10', 0),
            array('List 11', '', 'list_11', 0),
            array('List 12', '', 'list_12', 0),
            array('List 13', '', 'list_13', 0),
            array('List 14', '', 'list_14', 0),
            array('List 15', '', 'list_15', 0),
            array('List 16', '', 'list_16', 0),
            array('List 17', '', 'list_17', 0),
            array('List 18', '', 'list_18', 0),
            array('List 19', '', 'list_19', 0),
            array('List 20', '', 'list_20', 0),
            array('List 21', '', 'list_21', 0),
            array('List 22', '', 'list_22', 0),
            array('List 23', '', 'list_23', 0),
            array('List 24', '', 'list_24', 0),
            array('List 25', '', 'list_25', 0),
            array('List 26', '', 'list_26', 0),
            array('List 27', '', 'list_27', 0),
            array('List 28', '', 'list_28', 0),
            array('List 29', '', 'list_29', 0),
            array('List 30', '', 'list_30', 0),
            array('Date 1', '', 'date_1', 0),
            array('Date 2', '', 'date_2', 0),
            array('Date 3', '', 'date_3', 0),
            array('Date 4', '', 'date_4', 0),
            array('Date 5', '', 'date_5', 0),
            array('Date 6', '', 'date_6', 0),
            array('Date 7', '', 'date_7', 0),
            array('Date 8', '', 'date_8', 0),
            array('Date 9', '', 'date_9', 0),
            array('Date 10', '', 'date_10', 0),
            array('Text long 1', '', 'text_long_1', 0),
            array('Text long 2', '', 'text_long_2', 0),
            array('Text long 3', '', 'text_long_3', 0),
            array('Text long 4', '', 'text_long_4', 0),
            array('Text long 5', '', 'text_long_5', 0),
            array('Text short 1', '', 'text_short_1', 0),
            array('Text short 2', '', 'text_short_2', 0),
            array('Text short 3', '', 'text_short_3', 0),
            array('Text short 4', '', 'text_short_4', 0),
            array('Text short 5', '', 'text_short_5', 0),
            array('Text short 6', '', 'text_short_6', 0),
            array('Text short 7', '', 'text_short_7', 0),
            array('Text short 8', '', 'text_short_8', 0),
            array('Text short 9', '', 'text_short_9', 0),
            array('Text short 10', '', 'text_short_10', 0),
            array('Attached document', '', 'attached_documents', 0),
            array('Text', '', 'text', 0),
            array('Milestone', '', 'milestone', 0),
            array('Assigned to 1', '', 'assigned_to_1', 0),
            array('Assigned to 2', '', 'assigned_to_2', 0),
            array('Assigned to 3', '', 'assigned_to_3', 0),
            array('List color 1', '', 'list_color_1', 0),
            array('List color 2', '', 'list_color_2', 0),
            array('List color 3', '', 'list_color_3', 0),
            array('List color 4', '', 'list_color_4', 0),
            array('List color 5', '', 'list_color_5', 0),
        );
        foreach ($origins as $text) {
            $check = $this->ExpectationTranslation->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $this->employee_info['Company']['id'],
                    'original_text' => $text[0],
                    'field' => $text[2]
                )
            ));
            if( !$check ){
                //insert
                $this->ExpectationTranslation->create();
                $this->ExpectationTranslation->save(array(
                    'company_id' => $this->employee_info['Company']['id'],
                    'original_text' => $text[0],
                    'field' => $text[2]
                ));
            }
        }
        return;
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
                $this->ExpectationTranslation->id = $this->data['id'];
            } else {
                $this->ExpectationTranslation->create();
            }
            unset($this->data['id']);
            $saved = array(
                'eng' => $this->data['english'],
                'fre' => $this->data['france'],
            );
            if ($this->ExpectationTranslation->save($saved)) {
                $result = true;
                // $this->Session->setFlash(__('OK.', true), 'success');
            } else {
                $this->Session->setFlash(__('KO.', true), 'error');
            }
            $this->data['id'] = $this->ExpectationTranslation->id;
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }
}
