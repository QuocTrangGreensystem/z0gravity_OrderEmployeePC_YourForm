<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class VisionTaskExportsController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'VisionTaskExports';
    //var $layout = 'administrators';

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation');
	
	/**
     * #1821 - Display your form fields on task vision
     *
     * @var array
     * @access public
     */
    var $your_form_fields = array();
	
	function beforeFilter() {
        parent::beforeFilter();
        $this->your_form_fields = array(
			// select fields
			'list_1' => 'List 1',
			'list_2' => 'List 2',
			'list_3' => 'List 3',
			'list_4' => 'List 4',
			'list_5' => 'List 5',
			'list_6' => 'List 6',
			'list_7' => 'List 7',
			'list_8' => 'List 8',
			'list_9' => 'List 9',
			'list_10' => 'List 10',
			'list_11' => 'List 11',
			'list_12' => 'List 12',
			'list_13' => 'List 13',
			'list_14' => 'List 14',
			'project_type_id' => 'Project type',
			'project_sub_type_id' => 'Sub type',
			'project_sub_sub_type_id' => 'Sub sub type',
			'complexity_id' => 'Implementation Complexity',
			'budget_customer_id' => 'Customer',
			// 'project_priority_id' => 'Priority', // trùng với task, fix sau
			// 'project_status_id' => 'Status',
			'team' => 'Team',
			// multi-select fields
			'list_muti_1' => 'List(multiselect) 1',
			'list_muti_10' => 'List(multiselect) 10',
			'list_muti_2' => 'List(multiselect) 2',
			'list_muti_3' => 'List(multiselect) 3',
			'list_muti_4' => 'List(multiselect) 4',
			'list_muti_5' => 'List(multiselect) 5',
			'list_muti_6' => 'List(multiselect) 6',
			'list_muti_7' => 'List(multiselect) 7',
			'list_muti_8' => 'List(multiselect) 8',
			'list_muti_9' => 'List(multiselect) 9',
			'project_phase_id' => 'Current Phase',
			// PM fields
			'project_manager_id' => 'Project Manager',
			'read_access' => 'Read Access',
			'technical_manager_id' => 'Technical manager',
			'uat_manager_id' => 'UAT manager',
			'chief_business_id' => 'Chief Business',
			'functional_leader_id' => 'Functional leader',
		);
	}
	
     /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($company_id = null) {
        $this->loadModel('Company');
        $modifyScreen = 'NO';
        if ($this->is_sas && empty($company_id)) {
            $companies = $this->Company->find('list');
			$this->set(compact('companies'));
        } else {
            $companyName = $this->_getCompany($company_id);
            if (empty($companyName)) {
                $this->Session->setFlash(sprintf(__('The company "#%s" was not found, please try again', true), $company_id), 'error');
            }
            $company_id = $companyName['Company']['id'];
            // default columns
            $visionTaskExportDefaults = array(
                0 => array(
                    'name' => 'Program',
                    'english' => 'Program',
                    'france' => 'Programme',
                    'company_id' => $company_id,
                    'display' => 0,
                    'weight' => 1
                ),
                1 => array(
                    'name' => 'Sub program',
                    'english' => 'Sub program',
                    'france' => 'Sous programme',
                    'company_id' => $company_id,
                    'display' => 0,
                    'weight' => 2
                ),
                2 => array(
                    'name' => 'Project name',
                    'english' => 'Project name',
                    'france' => 'Nom du projet',
                    'company_id' => $company_id,
                    'display' => 1,
                    'weight' => 3
                ),
                3 => array(
                    'name' => 'Lot',
                    'english' => 'Lot',
                    'france' => 'Lot',
                    'company_id' => $company_id,
                    'display' => 0,
                    'weight' => 4
                ),
                4 => array(
                    'name' => 'Phase',
                    'english' => 'Phase',
                    'france' => 'Phase',
                    'company_id' => $company_id,
                    'display' => 0,
                    'weight' => 5
                ),
                5 => array(
                    'name' => 'Task',
                    'english' => 'Task',
                    'france' => 'Tache',
                    'company_id' => $company_id,
                    'display' => 1,
                    'weight' => 6
                ),
                6 => array(
                    'name' => 'Status',
                    'english' => 'Status',
                    'france' => 'Statut',
                    'company_id' => $company_id,
                    'display' => 1,
                    'weight' => 7
                ),
                7 => array(
                    'name' => 'Priority',
                    'english' => 'Priority',
                    'france' => 'Priorite',
                    'company_id' => $company_id,
                    'display' => 0,
                    'weight' => 8
                ),
                8 => array(
                    'name' => 'Assigned',
                    'english' => 'Assigned',
                    'france' => 'Attribué',
                    'company_id' => $company_id,
                    'display' => 1,
                    'weight' => 9
                ),
                9 => array(
                    'name' => 'Start',
                    'english' => 'Start',
                    'france' => 'début',
                    'company_id' => $company_id,
                    'display' => 1,
                    'weight' => 10
                ),
                10 => array(
                    'name' => 'End',
                    'english' => 'End',
                    'france' => 'Fin',
                    'company_id' => $company_id,
                    'display' => 1,
                    'weight' => 11
                ),
                11 => array(
                    'name' => 'Workload',
                    'english' => 'Workload',
                    'france' => 'Charge',
                    'company_id' => $company_id,
                    'display' => 0,
                    'weight' => 12
                ),
                12 => array(
                    'name' => 'Consume',
                    'english' => 'Consume',
                    'france' => 'Consommcode projecte',
                    'company_id' => $company_id,
                    'display' => 0,
                    'weight' => 13
                ),
                13 => array(
                    'name' => 'Code project',
                    'english' => 'Code project',
                    'france' => 'Code projet',
                    'company_id' => $company_id,
                    'display' => 0,
                    'weight' => 14
                ),
                14 => array(
                    'name' => 'Code project 1',
                    'english' => 'Code project 1',
                    'france' => 'Code projet 1',
                    'company_id' => $company_id,
                    'display' => 0,
                    'weight' => 15
                ),
                15 => array(
                    'name' => 'Text',
                    'english' => 'Text',
                    'france' => 'Texte',
                    'company_id' => $company_id,
                    'display' => 1,
                    'weight' => 16
                ),
                16 => array(
                    'name' => 'Initial workload',
                    'english' => 'Initial workload',
                    'france' => 'Charge initiale',
                    'company_id' => $company_id,
                    'display' => 0,
                    'weight' => 17
                ),
                17 => array(
                    'name' => 'Initial start',
                    'english' => 'Initial start',
                    'france' => 'Date de début',
                    'company_id' => $company_id,
                    'display' => 0,
                    'weight' => 18
                ),
                18 => array(
                    'name' => 'Initial end',
                    'english' => 'Initial end',
                    'france' => 'Date de fin',
                    'company_id' => $company_id,
                    'display' => 0,
                    'weight' => 19
                ),
                19 => array(
                    'name' => 'Duration',
                    'english' => 'Duration',
                    'france' => 'Durée en',
                    'company_id' => $company_id,
                    'display' => 0,
                    'weight' => 20
                ),
                20 => array(
                    'name' => 'Overload',
                    'english' => 'Overload',
                    'france' => 'Surcharge',
                    'company_id' => $company_id,
                    'display' => 0,
                    'weight' => 21
                ),
                21 => array(
                    'name' => 'In Used',
                    'english' => 'In Used',
                    'france' => 'En Cours',
                    'company_id' => $company_id,
                    'display' => 0,
                    'weight' => 22
                ),
                22 => array(
                    'name' => 'Completed',
                    'english' => 'Completed',
                    'france' => 'Avancement',
                    'company_id' => $company_id,
                    'display' => 0,
                    'weight' => 23
                ),
                23 => array(
                    'name' => 'Remain',
                    'english' => 'Remain',
                    'france' => 'Restant',
                    'company_id' => $company_id,
                    'display' => 0,
                    'weight' => 24
                ),
                24 => array(
                    'name' => 'Amount',
                    'english' => 'Amount €',
                    'france' => 'Montant €',
                    'company_id' => $company_id,
                    'display' => 0,
                    'weight' => 25
                ),
                25 => array(
                    'name' => 'Progress order',
                    'english' => '% progress order',
                    'france' => '% ordre de progrès',
                    'company_id' => $company_id,
                    'display' => 0,
                    'weight' => 26
                ),
                26 => array(
                    'name' => 'Milestone',
                    'english' => 'Milestone',
                    'france' => 'Jalons',
                    'company_id' => $company_id,
                    'display' => 0,
                    'weight' => 27
                ),
                27 => array(
                    'name' => 'Attachment',
                    'english' => 'Attachment',
                    'france' => 'Fichier',
                    'company_id' => $company_id,
                    'display' => 1,
                    'weight' => 28
                ),
            );
            $visionTaskExports = $this->VisionTaskExport->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id
                ),
                'order' => array('weight' => 'ASC')
            ));
			$translation_saved = !empty($visionTaskExports) ? Set::classicExtract($visionTaskExports, '{n}.VisionTaskExport.translation_id') : array();
			$translation_saved = array_filter( $translation_saved);
            $visionTaskExports = !empty($visionTaskExports) ? Set::combine($visionTaskExports, '{n}.VisionTaskExport.id', '{n}.VisionTaskExport') : array();
			
            if(!empty($visionTaskExports)){
                $i = 1;
                foreach($visionTaskExports as $key => $visionTaskExport){
                    $this->VisionTaskExport->id = $visionTaskExport['id'];
                    $this->VisionTaskExport->save(array('weight' => $i));
                    $visionTaskExports[$key]['weight'] = $i;
                    $i++;
                }
                // chay lan dau sau khi update them column.
                if(count($visionTaskExportDefaults) != count($visionTaskExports)){
                    foreach ($visionTaskExportDefaults as $key => $value) {
                        $check = $this->VisionTaskExport->find('first', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'company_id' => $company_id,
                                'name' => $value['name']
                            )
                        ));
                        if(empty($check)){
                            $this->VisionTaskExport->create();
                            $this->VisionTaskExport->save($value);
                        }
                    }
                }
            } else {
                $this->VisionTaskExport->create();
                $this->VisionTaskExport->saveAll($visionTaskExportDefaults);
            }
			
			/* Translation data */
			$this->loadModels('Translation', 'TranslationSetting');
			$this->TranslationSetting->virtualFields['custom_order'] = 'CASE
				WHEN TranslationSetting.setting_order IS NOT NULL THEN TranslationSetting.setting_order
				WHEN TranslationSetting.setting_order IS NULL THEN 999
			END';
			$this->Translation->hasMany = array(
				'TranslationEntry' => array(
					'className' => 'TranslationEntry',
					'foreignKey' => 'translation_id',
					'conditions' => array(
						'TranslationEntry.company_id' => $company_id
						
					),
					'fields' => array(
						'TranslationEntry.text', 
						'TranslationEntry.code',
						'TranslationEntry.language',
					)
				),
				'TranslationSetting' => array(
					'className' => 'TranslationSetting',
					'foreignKey' => 'translation_id',
					'conditions' => array(
						'TranslationSetting.company_id' => $company_id,
						// 'TranslationSetting.show' => 1
					),
					'fields' => array(
						'TranslationSetting.translation_id', 
						'TranslationSetting.show',
						'TranslationSetting.custom_order',
					),
				)
			);
			$translateData = $this->Translation->find('all', array(
				// 'recursive' => -1,
                'conditions' => array(
                    'Translation.page' => 'Details',
					'Translation.original_text' => $this->your_form_fields,
					'Translation.original_text' => $this->your_form_fields,
				),
				'contain' => array('TranslationEntry', 'TranslationSetting'),
				'fields' => array(
					'Translation.id','Translation.original_text',
				),
				'order' => array(
                    // 'TranslationSetting.custom_order' => 'ASC'
                ),
			));
			$translateData = !empty( $translateData) ? Set::sort($translateData, '{n}.TranslationSetting.0.custom_order', 'asc') : array();
			$translateData = !empty( $translateData) ? Set::combine($translateData, '{n}.Translation.id', '{n}') : array();
			$translate_ids = array();
			foreach( $translateData as $_t_id => $_translation){
				$_show = isset($_translation['TranslationSetting'][0]['show']) ? $_translation['TranslationSetting'][0]['show'] : 0;
				if( $_show) $translate_ids[] = $_t_id;
			}
			// debug( $translateData);
			// debug( $translate_ids); exit;
			/* END Translation data */
			
			/* Save Translation data to VisionTaskExport */
			// delete from VisionTaskExport: If item is disabled from youForm then delete it
			$this->VisionTaskExport->deleteAll(
				array(
					'VisionTaskExport.company_id' => $company_id,
					'NOT' => array(
						'VisionTaskExport.translation_id is NULL',
						'VisionTaskExport.translation_id' => $translate_ids,
					)
				), false
			);
			// Add new field from Your form 
			$_last = end( $visionTaskExports);
			$max_weight =  $_last['weight'];
			foreach ( $translateData as $k => $v){
				$_t_id = $v['Translation']['id'];
				$_show = isset($v['TranslationSetting'][0]['show']) ? $v['TranslationSetting'][0]['show'] : 0;
				if( !in_array($_t_id, $translation_saved) && $_show){
					$this->VisionTaskExport->create();
					$this->VisionTaskExport->save(array(
						'company_id' => $company_id,
						'translation_id' => $_t_id,
						'name' => $v['Translation']['original_text'],
						'display' => 0,
						'weight' => ++$max_weight,
					));
				}
			}
			/* END Translation data to VisionTaskExport */
			
			/* Get Data again */
			$visionTaskExports = $this->VisionTaskExport->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company_id
				),
				'order' => array('weight' => 'ASC')
			));
			$visionTaskExports = !empty($visionTaskExports) ? Set::combine($visionTaskExports, '{n}.VisionTaskExport.id', '{n}.VisionTaskExport') : array();
			/* END Get Data again */
			
			$this->set(compact('company_id', 'companyName', 'visionTaskExports', 'translateData'));
        }
    }

    /**
     * Order
     *
     * @return void
     * @access public
     */
    public function order($company_id = null) {
        if (!empty($this->data) && $this->_getCompany($id)) {
            foreach ($this->data as $id => $weight) {
                if (!empty($id) && !empty($weight) && $weight!=0) {
                    $this->VisionTaskExport->id = $id;
                    $this->VisionTaskExport->saveField('weight', $weight);
                }
            }
        }
        die;
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
        if (!empty($this->data) && $this->_getCompany()) {
            $companyName = $this->_getCompany();
            $company_id = $companyName['Company']['id'];
            $data = array(
                'display' => (isset($this->data['display']) && $this->data['display'] == 'yes')
            );
            $this->VisionTaskExport->create();
            if (!empty($this->data['id'])) {
                $this->VisionTaskExport->id = $this->data['id'];
            }
            unset($this->data['id']);
            if ($this->VisionTaskExport->save(array_merge($this->data, $data))) {
                $result = true;
                $this->Session->setFlash(__('OK.', true), 'success');
            } else {
                $this->Session->setFlash(__('KO.', true), 'error');
            }
            $this->data['id'] = $this->VisionTaskExport->id;
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }
    /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _getCompany($company_id = null) {
        if (!$this->is_sas) {
            $company_id = $this->employee_info['Company']['id'];
        } elseif (!$company_id && !empty($this->data['company_id'])) {
            $company_id = $this->data['company_id'];
        }
        $companyName = ClassRegistry::init('Company')->find('first', array('recursive' => -1
            , 'conditions' => array('id' => $company_id)));
        if (empty($companyName)) {
            return false;
        }
        return $companyName;
    }
}