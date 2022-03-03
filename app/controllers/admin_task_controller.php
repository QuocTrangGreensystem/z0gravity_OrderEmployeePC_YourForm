<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class AdminTaskController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'AdminTask';
    public $pages = array(
        'Project_Task'
    );
    public $company_id;
    var $uses = array('ProjectPhase','Company','ProjectTask');
    public $langs = array(
        'eng' => 'English',
        'fre' => 'French',
    );
    public $dragDropPages = array('Project_Task');
    public $headerFields = array('project_tasks');
    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation');

    /**
     * index
     *
     * @return void
     * @access public
     */
    public function beforeFilter(){
        parent::beforeFilter();
        $this->set(array(
            'pages' => $this->pages,
            'langs' => $this->langs,
        ));
        if (empty($this->params['requested']) && (@$this->employee_info["Role"]["id"] != 2 || $this->employee_info["Employee"]["is_sas"] == 1) )$this->redirect('/administrators');
        $this->company_id = @$this->employee_info["Company"]["id"];
        $this->set('company_id', $this->company_id);
        $this->set('dragDropPages', $this->dragDropPages);
        $this->set('headerFields', $this->headerFields);
        $this->loadModel('Translation');
        $this->loadModel('TranslationSetting');
    }
    function index($currentPage = null) {
        if(!$currentPage || !in_array($currentPage, $this->pages) )$currentPage = Inflector::slug($this->pages[0]);
        $this->autoInsert($currentPage);
        if( in_array($currentPage, $this->dragDropPages) ){
            $data = $this->Translation->find('all', array(
                'conditions' => array(
                    'page' => $currentPage,
					'TranslationSetting.company_id' => $this->company_id
                ),
                'fields' => array('*', 'CASE
                        WHEN TranslationSetting.setting_order IS NOT NULL THEN TranslationSetting.setting_order
                        WHEN TranslationSetting.setting_order IS NULL THEN 999
                    END as custom_order'
                ),
                'joins' => array(
                    array(
                        'table' => 'translation_settings',
                        'alias' => 'TranslationSetting',
                        'conditions' => array(
                            'Translation.id = TranslationSetting.translation_id',
                        ),
                        'type' => 'left'
                    )
                ),
                'order' => array(
                    'custom_order' => 'ASC'
                )
            ));
        }
        $this->set(compact('data', 'currentPage'));
    }
    public function setting() {
        $this->loadModel('ProjectSetting');
        if( !isset($this->employee_info['Company']['id']) ){
            $this->Session->setFlash(__('You do not have permission to access this page', true), 'error');
            return $this->redirect('/project_phases');
        }
        $setRemain = $this->get();
        $this->set(compact('setRemain'));
        if(!empty($this->data)){
            $result = false;
            $this->ProjectSetting->id = $setRemain['ProjectSetting']['id'];
            if($this->ProjectSetting->save($this->data['Project'])){
                $result = true;
            }
            if($result == true){
                $this->Session->setFlash(__('Saved', true), 'success');
            } else {
                $this->Session->setFlash(__('Not saved', true), 'error');
            }
        }
    }
    public function consumed() {
        $this->loadModel('ProjectSetting');
        if( !isset($this->employee_info['Company']['id']) ){
            $this->Session->setFlash(__('You do not have permission to access this page', true), 'error');
            return $this->redirect('/project_phases');
        }
        $setRemain = $this->get();
        $this->set(compact('setRemain'));
        if(!empty($this->data)){
            $result = false;
            $this->ProjectSetting->id = $setRemain['ProjectSetting']['id'];
            if($this->ProjectSetting->save($this->data['Project'])){
                $result = true;
            }
            if($result == true){
                $this->Session->setFlash(__('Saved', true), 'success');
            } else {
                $this->Session->setFlash(__('Not saved', true), 'error');
            }
        }
    }
    public function get(){
        $cid = $this->employee_info['Company']['id'];
        $data = $this->ProjectSetting->find('first', array(
            'conditions' => array('company_id' => $cid)
        ));
        if( empty($data) ){
            //insert data
            $data = array(
                'ProjectSetting' => array(
                    'company_id' => $cid,
                    'show_freeze' => 0
                )
            );
            $this->ProjectSetting->create();
            $this->ProjectSetting->save($data);
            $data['ProjectSetting']['id'] = $this->ProjectSetting->id;
        }
        return $data;
    }

    public function getTaskSettings($all = false){
        $this->loadModel('Translation');
        $default = array(
            'ID|1',
            'Task|1',
            'Order|0',
            'Priority|0',
            'Status|1',
            'Profile|0',
            'AssignedTo|1',
            '+/-|0',
            'Startdate|1',
            'Enddate|1',
            'Duration|1',
            'Predecessor|0',
            'Workload|0',
            'Overload|0',
            'Consumed|0',
            'ManualConsumed|0',
            'InUsed|0',
            'Completed|0',
            'Remain|0',
            'Amount€|0',
            '%progressorder|0',
            '%progressorder€|0',
            'Text|1',
            'Attachment|1',
            'Initialworkload|0',
            'Initialstartdate|0',
            'Initialenddate|0',
            'UnitPrice|0',
            'Consumed€|0',
            'Remain€|0',
            'Workload€|0',
            'Estimated€|0',
            'Milestone|0',
            'EAC|0',
        );
        if( $all ) return $default;
        $conds = array(
            'page' => 'Project_Task'
        );
        $raw = $this->Translation->find('list', array(
            'conditions' => $conds,
            'recursive' => -1,
            'fields' => array('original_text', 'TranslationSetting.show'),
            'joins' => array(
                array(
                    'table' => 'translation_settings',
                    'alias' => 'TranslationSetting',
                    'conditions' => array(
                        'Translation.id = TranslationSetting.translation_id',
                        'TranslationSetting.company_id' => $this->company_id
                    ),
                    'type' => 'left'
                )
            ),
            'order' => array('TranslationSetting.setting_order' => 'ASC')
        ));
		
		$setting_field = array();
		if(!empty($raw)){
			foreach($raw as $original_text => $show){
				$setting_field[] = str_replace(" ", "", $original_text) .'|'. $show;
			}
		}
		
        $data = array_unique($setting_field + $default);

        $new = array();
        foreach ($data as $key => $value) {
            $new[] = $value;
        }
        return $new;
    }

    private function autoInsert($page){
        $origins = array(
            'Project_Task' => array(
                array('ID', 'ID', '', 1),
                array('Task', 'Tâche', '', 1),
                array('Order', 'Order', '', 0),
                array('Priority', 'Priorité', '', 0),
                array('Status', 'Statut', '', 0),
                array('Profile', 'Profil', '', 0),
                array('Assigned To', 'Assigné à', '', 1),
                array('+/-', '+/-', '', 0, '', 0),
                array('Start date', 'Début', '', 1),
                array('End date', 'Fin', '', 1),
                array('Duration', 'Durée', '', 1),
                array('Predecessor', 'Prédécesseur', '', 0),
                array('Workload', 'Charge', '', 0),
                array('Overload', 'Surcharge', '', 0),
                array('Consumed', 'Consommé', '', 0),
                array('Manual Consumed', 'Consommé Manuel', '', 0),
                array('In Used', 'In Used', '', 0),
                array('Completed', 'Avancement', '', 0),
                array('Remain', 'Reste à faire', '', 0),
                array('Amount €', 'Montant €', 0, 0),
                array('% progress order', '% avancement commande', 0, 0),
                array('% progress order €', '% avancement commande €', 0, 0),
                array('Text', 'Texte', 0, 1),
                array('Attachment', 'Attachement', 0, 1),
                array('Initial workload', 'Initial workload', '', 0),
                array('Initial start date', 'Initial start date', '', 0),
                array('Initial end date', 'Initial end date', '', 0),
                array('Unit Price', 'Unit Price', '', 0),
                array('Consumed €', 'Consumed €', '', 0),
                array('Remain €', 'Remain €', '', 0),
                array('Workload €', 'Workload €', '', 0),
                array('Estimated €', 'Estimated €', '', 0),
                array('Milestone', 'Milestone', '', 0),
                array('EAC', 'Estimated at completion', '', 0),
            )
        );
        $default = $origins[$page];
        //check in translations table
        $i = 0;
        foreach ($default as $texts) {

            $data = $this->Translation->has($page, $texts[0]);
            //debug($data); 
            if( !$data ){
                //insert
                $this->Translation->create();
                $this->Translation->save(array(
                    'page' => $page,
                    'original_text' => $texts[0],
                    'field' => isset($texts[2]) ? $texts[2] : null
                ));
                $data = $this->Translation->read();
            }
            //add field
            $this->Translation->id = $data['Translation']['id'];
            if( isset($texts[2]) && $texts[2] != $data['Translation']['field'] ){
                $this->Translation->saveField('field', $texts[2]);
            }
            //add settings if not exists
            if( !$this->TranslationSetting->find('count', array('conditions' => array('translation_id' => $this->Translation->id, 'company_id' => $this->company_id))) ){
                $this->TranslationSetting->create();
                $this->TranslationSetting->save(array(
                    'translation_id' => $this->Translation->id,
                    'company_id' => $this->company_id,
                    'setting_order' => $i,
                    'show' => isset($texts[3]) ? $texts[3] : 1
                ));
            }
            //add entries if not exists
            if( $texts[1] && !$this->Translation->TranslationEntry->find('count', array('conditions' => array('translation_id' => $this->Translation->id, 'code' => 'fre', 'company_id' => $this->company_id))) ){
                $this->Translation->TranslationEntry->create();
                $this->Translation->TranslationEntry->save(array(
                    'translation_id' => $this->Translation->id,
                    'company_id' => $this->company_id,
                    'text' => $texts[1],
                    'code' => 'fre'
                ));
            }
            $i++;
        }
       // exit;
        return;
    }
	function update($id = null) {
		$result = false;
		$this->layout = false;
        if (!empty($this->data)) {
			$id = $this->data['id'];
			$display = ($this->data['display'] == 'yes') ? 1 : 0;
			$text_en = strval($this->data['english']);
			$text_fr = strval($this->data['french']);
			$this->TranslationSetting->updateAll(array(
				'TranslationSetting.show' => $display,
			),array(
				'TranslationSetting.translation_id' => $id,
                'TranslationSetting.company_id' => $this->data['company_id'],
			));
			$this->Translation->TranslationEntry->saveText($id, $this->data['company_id'], 'eng', $text_en);
			$this->Translation->TranslationEntry->saveText($id, $this->data['company_id'], 'fre', $text_fr);
            // $sync = array();
            foreach( $this->langs as $code => $name ){
				$currentPage = 'Project_Task';
				$dataLang = ($code == 'eng') ? $text_en : $text_fr;
				$dataLang = trim($dataLang);
				$this->Translation->TranslationEntry->saveText($id, $this->company_id, $code, $dataLang);
                $file = new File(APP . 'locale/'.$code.'/LC_MESSAGES/' . $currentPage . '_' . $this->data['company_id'] . '.po', true);
                $data = $this->Translation->find('all', array(
                    'fields' => 'Translation.original_text, TranslationEntry.text',
                    'recursive' => -1,
                    'conditions' => array(
                        'page' => $currentPage,
                        'TranslationEntry.company_id' => $this->data['company_id'],
                        'TranslationEntry.code' => $code,

                    ),
                    'joins' => array(
                        array(
                            'table' => 'translation_entries',
                            'alias' => 'TranslationEntry',
                            'conditions' => array(
                                'TranslationEntry.translation_id = Translation.id'
                            )
                        )
                    )
                ));
                $output = '';
                foreach( $data as $field ){
                    $output .= 'msgid "'.$field['Translation']['original_text'].'"' . "\n";
                    $output .= 'msgstr "'.$field['TranslationEntry']['text'].'"' . "\n\n";
                }
                $file->write($output);
                // $sync[] = array(
                    // 'path' => $file->Folder->pwd() . DS,
                    // 'file' => $file->name
                // );
                $file->close();
            }
            //clean up cache
            $this->requestAction('/pages/cleanup');
			$result = true;
        }else{
			return $this->redirect('/admin_task/index/Project_Task');
		}
		$this->set(compact('result'));
    }
	public function saveOrder(){
        if( !empty($this->data) ){
			$orders = $this->data;
            foreach($orders as $translation_id => $weight){
                $id = $this->TranslationSetting->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $this->company_id,
                        'translation_id' => $translation_id,
                    )
                ));
                $data = array(
                    'id' => $id['TranslationSetting']['id'],
                    'company_id' => $this->company_id,
                    'translation_id' => $translation_id,
                    'setting_order' => $weight
                );
                if( !$id ){
                    unset($data['id']);
                    $this->TranslationSetting->create();
                }
                $this->TranslationSetting->save($data);
            }
            die('ok');
        }
        die('not allowed!');
    }
}
?>
