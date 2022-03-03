<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class TranslationsController extends AppController {
    public $helpers = array('Cache');
    public $components = array('MultiFileUpload');
    public $pages = array(
        'KPI+',
        'Details',
        'Details_1',
        'Details_2',
        'Details_3',
        'Details_4',
		'Total_Cost',
        'Internal_Cost',
        'External_Cost',
        'Sales',
        'Purchase',
        'FY_Budget',
        'Resource',
        'Provisional',
        'Finance',
        'Finance_2',
        'Created_Value',
		'Budget_Investment',
		'Budget_Operation',
		'Finance_Investment',
		'Finance_Operation'
    );
	public $new_design_pages = array(
		'KPI+',
		'Details',
		'Details_1',
		'Details_2',
		'Details_3',
		'Details_4',
		'Details_5',
		'Details_6',
		'Details_7',
		'Details_8',
		'Details_9',
		'Total_Cost',
		'Internal_Cost',
		'External_Cost',
		'Sales',
		'Purchase',
		'FY_Budget',
		'Resource',
		'Provisional',
		'Finance',
		'Finance_2',
		'Created_Value',
		'Budget_Investment',
		'Budget_Operation',
		'Finance_Investment',
		'Finance_Operation',
		'Project_Task'
	);
	public $origins;
    public $langs = array(
        'eng' => 'English',
        'fre' => 'French',
    );
    public $shorts = array(
        'en' => 'eng',
        'fr' => 'fre'
    );
    public $dragDropPages = array('Details', 'Details_1', 'Details_2', 'Details_3', 'Details_4', 'Details_5', 'Details_6', 'Details_7', 'Details_8', 'Details_9', 'Total_Cost', 'Internal_Cost', 'External_Cost', 'Sales', 'Provisional', 'Purchase', 'Budget_Investment', 'Budget_Operation', 'Finance_Investment', 'Finance_Operation');
    public $headerFields = array('project_details', 'external_cost', 'internal_cost', 'sales', 'provisional', 'purchase');
    public $company_id;
    public function getList(){
        if (!empty($this->params['requested']))
            return $this->langs;
        die;
    }
    public function getPages(){
        if (!empty($this->params['requested']))
            return $this->pages;
        die;
    }
    public $cacheAction = array(
        'getByPage/' => '1 month'
    );
	public $non_edit_row = array('Project Name', 'Project Manager', 'Program');
    public function beforeFilter(){
        parent::beforeFilter();
        $employeeInfo = $this->Session->read("Auth.employee_info");
        $pages = $this->pages;
        // if(!empty($employeeInfo['Color']['is_new_design']) && $employeeInfo['Color']['is_new_design'] == 1){
            $this->pages = $this->new_design_pages;
        // }
		$origins = $this->get_default_translate();
        // details 1, Details 2, Details 3, Details 4.
        $origins['Details_1'] = $origins['Details_2'] = $origins['Details_3'] = $origins['Details_4'] = $origins['Details'];
        $origins['Details_5'] = $origins['Details_6'] = $origins['Details_7']= $origins['Details_8']= $origins['Details_9'] = $origins['Details_new'];
		
		$this->origins = $origins;
        $this->set(array(
            'pages' => $this->pages,
            'langs' => $this->langs,
        ));
		
		$allow_sas_action = array('exportPO');		
        if ( ( !in_array($this->params['action'], $allow_sas_action) && empty($this->params['requested']) ) && $this->employee_info["Employee"]["is_sas"] == 1 ) $this->redirect('/administrators');
        $this->company_id = @$this->employee_info["Company"]["id"];
        $this->set('company_id', $this->company_id);
        $this->set('dragDropPages', $this->dragDropPages);
        $this->set('headerFields', $this->headerFields);
        $this->set('non_edit_row', $this->non_edit_row);
        $this->loadModel('TranslationSetting');
    }
	public function index($currentPage = null){
		$a = time();
		$this->loadModels('TranslationEntry', 'TranslationSetting', 'ProjectManagerUpdateField');
        $employeeInfo = $this->Session->read("Auth.employee_info");
        $pages = $this->pages;
        if( !$currentPage || !in_array($currentPage, $pages) )$currentPage = Inflector::slug($this->pages[0]);
        $this->autoInsert($currentPage, $employeeInfo['Company']['id']);
		$list_keys = !empty( $this->origins[$currentPage] ) ? Set::classicExtract($this->origins[$currentPage], '{n}.0') : array();
		
		$data = array();
        if( in_array($currentPage, $this->dragDropPages) ){
			$translate = $this->Translation->find('all', array(
				'recursive' => -1,
                'conditions' => array(
                    'page' => $currentPage,
					'original_text' => $list_keys,
				),
				'fields' => array('id','original_text','page', 'field' )
			));
			$translate = Set::combine($translate,'{n}.Translation.id','{n}');
			
			$translate_id = Set::combine($translate,'{n}.Translation.id','{n}.Translation.id');
			
			$this->TranslationSetting->virtualFields['custom_order'] = 'CASE
				WHEN TranslationSetting.setting_order IS NOT NULL THEN TranslationSetting.setting_order
				WHEN TranslationSetting.setting_order IS NULL THEN 999
			END';
			$translation_settings = $this->TranslationSetting->find('all', array(
				'recursive' => -1,
                'conditions' => array(
                    'translation_id' => $translate_id,
					'company_id' => $this->company_id
				),
				'order' => array(
                    'custom_order' => 'ASC'
                ),
				'fields' => array('translation_id', 'show', 'block_name', 'next_block', 'next_line', 'company_id', 'custom_order' )
			));
			
			if(!empty($translation_settings)){
				foreach ($translation_settings as $key => $tranSetting) {
					$data[$key] = $translate[$tranSetting['TranslationSetting']['translation_id']];
					$data[$key]['TranslationSetting'] = $tranSetting['TranslationSetting'];
				}
			}else{
				$data = $translate;
			}
			
        } else {
            $data = $this->Translation->find('all', array(
                'conditions' => array(
                    'page' => $currentPage
                )
            ));
        }
		$pm_employee_active = array();
		$your_form = array('Details', 'Details_1', 'Details_2', 'Details_3', 'Details_4', 'Details_5', 'Details_6', 'Details_7', 'Details_8', 'Details_9');
		if(in_array($currentPage, $your_form)){
			$pm_employee_active = $this->Employee->find('all', array(
				'recursive' => -1,
				'joins' => array(
					array(
						'table' => 'company_employee_references',
						'alias' => 'CompanyEmployeeReference',
						'type' => 'inner',
						'conditions' => array('CompanyEmployeeReference.employee_id = Employee.id'),
					)
				),
				'conditions' => array(
					'Employee.company_id' => $this->company_id,
					'CompanyEmployeeReference.role_id' => 3,
					'actif' => 1,
					'OR' => array(
						'start_date IS NULL',
						'start_date' => '0000-00-00',
						'start_date <=' => date('Y-m-d', time())
					),
					'AND' => array(
						'OR' => array(
							'end_date IS NULL',
							'end_date' => '0000-00-00',
							'end_date >=' => date('Y-m-d', time())
						)
					)
				),
				'fields' => array('id', 'CONCAT(Employee.first_name, " ", Employee.last_name) AS full_name')
			)); 
			$pm_employee_active = !empty($pm_employee_active) ? Set::combine($pm_employee_active,'{n}.Employee.id','{n}.0.full_name') : array();
			
			// Get pm assigned.
			$pm_employee_assigned = $this->ProjectManagerUpdateField->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $this->company_id,
				),
				'fields' => array('field', 'employee_id')
			));
			$pm_update_field = array();
			if(!empty($pm_employee_assigned)){
				foreach($pm_employee_assigned as $index => $value){
					$field = $value['ProjectManagerUpdateField']['field'];
					$employee_id = $value['ProjectManagerUpdateField']['employee_id'];
					$pm_update_field[$field][] = $employee_id;
				}
			}
		}
		$entries = array();
		$list = array('Details', 'Details_1', 'Details_2', 'Details_3', 'Details_4', 'Details_5', 'Details_6', 'Details_7', 'Details_8', 'Details_9','External_Cost', 'Internal_Cost','Budget_Investment', 'Budget_Operation', 'Finance_Investment', 'Finance_Operation', 'Total_Cost', 'Sales', 'Purchase', 'FY_Budget', 'Resource', 'Provisional', 'Finance_2');
        if(in_array($currentPage, $list)){
			
			$details_original_text = $this->Translation->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'original_text' => $list_keys,
					'page' => $currentPage,
				),
				'fields' => array('id', 'original_text')
			));
			$original_text_id = array_keys($details_original_text);
			
			$translationEntry = $this->TranslationEntry->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $this->company_id,
					'translation_id' => $original_text_id,
				),
				'fields' => array('translation_id', 'text', 'code')
			));
			
			$translationEntry = Set::combine($translationEntry,'{n}.TranslationEntry.code','{n}.TranslationEntry.text','{n}.TranslationEntry.translation_id');
			
			foreach ($details_original_text as $id => $original_text) {
                if(empty($entries[$original_text])) $entries[$original_text] = !empty($translationEntry[$id]) ? $translationEntry[$id] : array();
            }
        }
        $this->set(compact('data', 'currentPage', 'entries', 'pm_employee_active', 'pm_update_field'));
    }
    
	// delete after run
    public function updateOrderSetting(){
		set_time_limit(0);
		ignore_user_abort(true);
		$st =  time();
		$this->loadModels('Translation', 'TranslationSetting', 'Company');
		$companies = $this->Company->find('list', array(
			'recurisve' => -1,
			'fields' => array('id', 'id')
		));
		$this->Translation->virtualFields['custom_order'] = 'CASE
			WHEN TranslationSetting.setting_order IS NOT NULL THEN TranslationSetting.setting_order
			WHEN TranslationSetting.setting_order IS NULL THEN 999
			END';
		$data = $this->Translation->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'Translation.page' => 'Details',
				'TranslationSetting.company_id' => $companies,
				'TranslationSetting.show' => 1,
			),
			'fields' => array('Translation.id','Translation.original_text','Translation.page', 'TranslationSetting.id', 'TranslationSetting.show', 'TranslationSetting.block_name', 'TranslationSetting.next_block', 'TranslationSetting.next_line', 'TranslationSetting.company_id', 'Translation.custom_order'),
			'joins' => array(
				array(
					'table' => 'translation_settings',
					'alias' => 'TranslationSetting',
					'conditions' => array(
						'Translation.id = TranslationSetting.translation_id',
					),
				),
			),
			'order' => array(
				'custom_order' => 'ASC'
			)
		));
		$translate_data = array();
		foreach( $data as $v){
			$translate_data[$v['TranslationSetting']['company_id']][] = $v;
		}
		$i = 0;
		$new_data = $data;
		foreach( $translate_data as $company => $data){
			foreach( $this->non_edit_row as $r){
				foreach( $data as $k => $item){
					if( $item['Translation']['original_text']== $r){
						if( empty( $item['TranslationSetting']['id'])) continue;
						$item = $item['TranslationSetting'];
						$item['setting_order'] = $i++;
						$this->TranslationSetting->id = $item['id'];
						$this->TranslationSetting->save($item);
						unset( $new_data[$k]);
					}
				}
			}
			foreach( $new_data as $k => $item){
				if( empty( $item['TranslationSetting']['id'])) continue;
				$item = $item['TranslationSetting'];
				$item['setting_order'] = $i++;
				$this->TranslationSetting->id = $item['id'];
				$this->TranslationSetting->save($item);
			}
		}
		echo 'Done. ' . (time() - $st) . ' seconds';
		exit;
	}
    public function getSetting($translate, $page){
        $data = $this->Translation->find('first', array(
            'conditions' => array(
                'page' => $page,
                'original_text' => $translate
            ),
            'fields' => array('TranslationSetting.*'),
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
            )
        ));
        return $data['TranslationSetting'];
    }

    public function getSettings($page, $exceptions = array()){
        $conds = array(
            'page' => $page,
            'field IS NOT NULL',
            'field !=' => '',
            'show' => 1
        );
        if( !empty($exceptions) )$conds['NOT'] = array('field' => $exceptions);
        $data = $this->Translation->find('list', array(
            'conditions' => $conds,
            'fields' => array('field', 'TranslationSetting.setting_order'),
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
        return $data;
    }

    public function save(){
		$result = false;
		$this->layout = false;
        if( !empty($this->data['id']) ){
			
			$result = true;
            $currentPage = $this->data['page'];
            $id = $this->data['id'];
            // custom for save your form.
            $list = array('Details_1', 'Details_2', 'Details_3', 'Details_4','Details_5', 'Details_6', 'Details_7', 'Details_8', 'Details_9');
            if(in_array($currentPage, $list)){
                $_id = $this->Translation->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('Translation.id' => $id),
                    'fields' => array('id', 'original_text')
                ));
                if(!empty($_id)){
                    $_id = $this->Translation->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'original_text' => $_id['Translation']['original_text'],
                            'page' => 'Details'
                        ),
                        'fields' => array('id', 'id')
                    ));
                    if(!empty($_id)){
                        $id = $_id['Translation']['id'];
                    }
                }
				$currentPage = 'Details';
				
				
            }
			if($currentPage == 'Details'){
				// ticket 1082.
				if(!empty($this->data['field'])){
					$this->loadModels('ProjectManagerUpdateField');
					$old_data = $this->ProjectManagerUpdateField->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'field' => $this->data['field'],
							'company_id' => $this->company_id,
                        ),
                        'fields' => array('employee_id', 'employee_id')
                    ));
					if(!empty($this->data['can_update'])){
						foreach($this->data['can_update'] as $employee_id){
							if(!in_array($employee_id, $old_data)){
								$saved = array(
									'field' => $this->data['field'],
									'employee_id' => $employee_id,
									'company_id' => $this->company_id,
								);
								$this->ProjectManagerUpdateField->create();
								$this->ProjectManagerUpdateField->save($saved);
							}else{
								unset($old_data[$employee_id]);
							}
						}
					}
					if(!empty($old_data)){
						foreach($old_data as $id => $employee_id){
							$this->ProjectManagerUpdateField->deleteAll(array(
								'ProjectManagerUpdateField.employee_id' => $employee_id,
								'ProjectManagerUpdateField.field' => $this->data['field'],
								'ProjectManagerUpdateField.company_id' => $this->company_id,
							), false);
						}
					}
				}
			}
			$this->data['block_name'] = isset($this->data['block_name']) ? trim($this->data['block_name']) : '';
			$this->Translation->id = $id;
			
			// save data to TranslationSetting
			if($currentPage == 'Project_Task'){
				$this->TranslationSetting->updateAll(array(
					'TranslationSetting.block_name' => '"' . addslashes( $this->data['block_name']) . '"',
					'TranslationSetting.next_line' => $this->data['next_line'],
					'TranslationSetting.next_block' => $this->data['next_block'],
				), array(
					'TranslationSetting.translation_id' => $id,
					'TranslationSetting.company_id' => $this->company_id,
				));
			}else{
				$this->TranslationSetting->updateAll(array(
					'TranslationSetting.block_name' => '"' . addslashes( $this->data['block_name']) . '"',
					'TranslationSetting.show' => $this->data['show'],
					'TranslationSetting.next_line' => $this->data['next_line'],
					'TranslationSetting.next_block' => $this->data['next_block'],
				), array(
					'TranslationSetting.translation_id' => $id,
					'TranslationSetting.company_id' => $this->company_id,
				));
			}
            $sync = array();
            foreach( $this->langs as $code => $name ){
				$this->data[$code] = trim($this->data[$code]);
				$this->Translation->TranslationEntry->saveText($id, $this->company_id, $code, $this->data[$code]);
                $file = new File(APP . 'locale/'.$code.'/LC_MESSAGES/' . $currentPage . '_' . $this->company_id . '.po', true);
                $data = $this->Translation->find('all', array(
                    'fields' => 'Translation.original_text, TranslationEntry.text',
                    'recursive' => -1,
                    'conditions' => array(
                        'page' => $currentPage,
                        'TranslationEntry.company_id' => $this->company_id,
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
                $sync[] = array(
                    'path' => $file->Folder->pwd() . DS,
                    'file' => $file->name
                );
                $file->close();
            }
            //clean up cache
            $this->requestAction('/pages/cleanup');
            $this->Session->setFlash(__('Saved', true), 'success');
            if ($currentPage == 'Project_Task') {
                $link = array('controller'=>'admin_task','action' => 'index', $currentPage);
            } else {
                $link =  '/translations/index/' . $currentPage;
            }
			
            if( $this->MultiFileUpload->otherServer ){
                return $this->MultiFileUpload->uploadTranslate($sync, $link);
            } else {
			
                if( $this->RequestHandler->isAjax() ){
                    $this->set(compact('result'));
                }else{
					return $this->redirect($link);
				}
            }
        }
        if( $this->RequestHandler->isAjax() ){
            return $this->render();
			
        }		
        // $this->redirect(array('action' => 'index'));
		$this->set(compact('result'));
    }
	
	public function exportPO($company_id = null){
		if( empty($this->employee_info['Employee']['is_sas']) ){
			$this->cakeError('error404');
		}
		if( empty($company_id) ) return false;
		$list_all_page = $this->new_design_pages;
		$origins = $this->origins;
		foreach ( $list_all_page as $currentPage){
			$list = array('Details_1', 'Details_2', 'Details_3', 'Details_4','Details_5', 'Details_6', 'Details_7', 'Details_8', 'Details_9');
            if(in_array($currentPage, $list)){
                $currentPage = 'Details';
            }
			$currentPage = Inflector::slug($currentPage);
			foreach( $this->langs as $code => $name ){
                $file = new File(APP . 'locale/'.$code.'/LC_MESSAGES/' . $currentPage . '_' . $company_id . '.po', true);
                $data = $this->Translation->find('all', array(
                    'fields' => 'Translation.original_text, TranslationEntry.text',
                    'recursive' => -1,
                    'conditions' => array(
                        'page' => $currentPage,
                        'TranslationEntry.company_id' => $company_id,
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
                $sync[] = array(
                    'path' => $file->Folder->pwd() . DS,
                    'file' => $file->name
                );
                $file->close();
            }
		}
		// if( $this->params['isAjax']) return json_encode($sync);
		if( $sync) return 1;
		return false;
	}
	

    public function saveSetting(){
        if( !empty($this->data) ){
            $id = $this->data['id'];
            $setting = $this->data['show'];
            $data = array(
                'show' => $setting ? $setting : 0,
                'company_id' => $this->company_id,
                'translation_id' => $this->data['translate']
            );
            if( !$id ){
                $this->TranslationSetting->create();
                $data['setting_order'] = 999;
            } else {
                $this->TranslationSetting->id = $id;
            }
            $this->TranslationSetting->save($data);
            die('ok');
        }
        die('not allowed!');
    }


    public function saveSettingNextLine(){
        if( !empty($this->data) ){
            $id = $this->data['id'];
            $setting = $this->data['nextline'];
            $data = array(
                'next_line' => $setting ? $setting : 0,
                'company_id' => $this->company_id,
                'translation_id' => $this->data['translate']
            );
            if( !$id ){
                $this->TranslationSetting->create();
                $data['setting_order'] = 999;
            } else {
                $this->TranslationSetting->id = $id;
            }
            $this->TranslationSetting->save($data);
            die('ok');
        }
        die('not allowed!');
    }

    public function saveSettingNextBlock(){
        if( !empty($this->data) ){
            $id = $this->data['id'];
            $setting = $this->data['nextblock'];
            $data = array(
                'next_block' => $setting ? $setting : 0,
                'company_id' => $this->company_id,
                'translation_id' => $this->data['translate']
            );
            if( !$id ){
                $this->TranslationSetting->create();
                $data['setting_order'] = 999;
            } else {
                $this->TranslationSetting->id = $id;
            }
            $this->TranslationSetting->save($data);
            die('ok');
        }
        die('not allowed!');
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

    private function entry($id, $cid, $text, $code){
        if( !$this->Translation->TranslationEntry->find('count', array(
            'conditions' => array(
                'translation_id' => $id,
                'company_id' => $cid,
                'code' => $code
            )
        ))){
            $this->Translation->TranslationEntry->create();
            $this->Translation->TranslationEntry->save(array(
                'translation_id' => $id,
                'company_id' => $cid,
                'code' => $code,
                'text' => $text
            ));
        }
    }
    private function get($text, $page){
        $result = $this->Translation->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'original_text' => $text,
                'page' => $page
            )
        ));
        if( empty($result) ){
            $result['Translation'] = array(
                'original_text' => $text,
                'page' => $page
            );
            $this->Translation->create();
            $this->Translation->save($result);
            $result['Translation']['id'] = $this->Translation->id;
        }
        return $result;
    }
    public function getByPage($page){
        return $this->Translation->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'page' => $page
            ),
            'fields' => array('original_text')
        ));
    }
    //only for project tasks
    public function getByLang($page){
        $langCode = $this->shorts[Configure::read('Config.langCode')];

        $this->Translation->virtualFields['translated'] = 'CASE WHEN Entry.text > "" THEN Entry.text ELSE Translation.original_text END';
        if($langCode == 'fre'){
            $this->Translation->virtualFields['translated'] = 'Entry.text';
        }
        $this->Translation->virtualFields['original_key'] = 'REPLACE(original_text, " ", "")';
        $data = $this->Translation->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'page' => $page
            ),
            'joins' => array(
                array(
                    'table' => 'translation_entries',
                    'alias' => 'Entry',
                    'type' => 'left',
                    'conditions' => array(
                        'Entry.translation_id = Translation.id',
                        'Entry.code' => $langCode,
                        'Entry.company_id' => $this->company_id
                    )
                )
            ),
            'fields' => array('original_key', 'translated')
        ));
        return $data;
    }

    public function autoInsert($page, $company_id = null){
		if( $page ) $page = Inflector::slug($page);
       if($company_id == null){
           return false;
       }
        $origins = $this->origins;
        // details 1, Details 2, Details 3, Details 4.
        $origins['Details_1'] = $origins['Details_2'] = $origins['Details_3'] = $origins['Details_4'] = $origins['Details'];
        $origins['Details_5'] = $origins['Details_6'] = $origins['Details_7']= $origins['Details_8']= $origins['Details_9'] = $origins['Details_new'];
        $default = !empty($origins[$page]) ? $origins[$page] : '';
        //check in translations table
        $i = 0;
        foreach ($default as $texts) {
            $data = $this->Translation->has($page, $texts[0]);
            if( !$data ){
                //insert
                $this->Translation->create();
                $this->Translation->save(array(
                    'page' => $page,
                    'original_text' => $texts[0],
                    'field' => isset($texts[2]) ? $texts[2] : null,
                ));
                $data = $this->Translation->read();
            }
            // update Project_Task field id 23/03/2018

            $projectTask = $this->Translation->find('all', array(
                'recursive' => -1,
                'conditions' => array('page' => 'Project_Task'),
                'fields' => array('id', 'field')
            ));
            // debug($projectTask); exit;
            // $dataFieldTask = $this->Translation->has($page, $texts[2]);
            // if( !$data && $page = 'Project_Task'){
            //     //insert
            //     $this->Translation->id = $last['Menu']['id'];
            //     $this->Translation->save(array(
            //         'page' => $page,
            //         'field' => isset($texts[2]) ? $texts[2] : null
            //     ));
            //     $data = $this->Translation->read();
            // }
            //add field
            $this->Translation->id = $data['Translation']['id'];
            if( isset($texts[2]) && $texts[2] != $data['Translation']['field'] ){
                $this->Translation->saveField('field', $texts[2]);
            }
            //add settings if not exists
            $list = array('Details_1', 'Details_2', 'Details_3', 'Details_4', 'Details_6', 'Details_7', 'Details_8', 'Details_9');
            if( !$this->TranslationSetting->find('count', array('conditions' => array('translation_id' => $this->Translation->id, 'company_id' => $company_id))) ){
                $this->TranslationSetting->create();
                $this->TranslationSetting->save(array(
                    'translation_id' => $this->Translation->id,
                    'company_id' => $company_id,
                    'setting_order' => $i,
                    'show' => in_array($page, $list) ? 0 : (isset($texts[3]) ? $texts[3] : 1)
                ));
            }
            //add entries if not exists
            if( $texts[1] && !$this->Translation->TranslationEntry->find('count', array('conditions' => array('translation_id' => $this->Translation->id, 'code' => 'fre', 'company_id' => $this->company_id))) ){
                $this->Translation->TranslationEntry->create();
                $this->Translation->TranslationEntry->save(array(
                    'translation_id' => $this->Translation->id,
                    'company_id' => $company_id,
                    'text' => $texts[1],
                    'code' => 'fre'
                ));
            }
            $i++;
        }
        return;
    }
	
	public function get_default_translate(){
		$origins = array(
            'KPI' => array(
                array('Comment', '', 'comment'),
                array('Planning', ''),
                array('Progress', ''),
                array('Budget', '', 'budget'),
                array('Staffing', ''),
                array('Risk', '', 'project_amr_risk_information'),
                array('Issue', '', 'project_amr_problem_information'),
                array('Log Comment', ''),
                array('Customer Point Of View', '', 'customer_point_of_view'),
                array('Done', '', 'done'),
                array('To Do', '', 'todo'),
                array('Acceptance', 'Recette'),
                array('Progress %', '', 'project_amr_progression'),
                array('Information', '', 'project_amr_solution'),
                array('Action', '', 'project_amr_solution_description'),
                array('Weather', '', 'weather'),
                array('Budget status', '', 'cost_control_weather'),
                array('Planning status', '', 'planning_weather'),
                array('Risk status', '', 'risk_control_weather'),
                array('Staffing status', '', 'organization_weather'),
                array('Issue status', '', 'issue_control_weather'),
                array('Trend', '', 'rank'),
                array('Delay', '', 'delay'),
                array('Created', '', 'created'),
                array('Updated', '', 'updated'),
                array('Budget comment', '', 'project_amr_budget_comment'),
                array('Scope', '', 'project_amr_scope'),
                array('Schedule', '', 'project_amr_schedule'),
                array('Resource', '', 'project_amr_resource'),
                array('Technical', '', 'project_amr_technical'),
				array('Budget weather', '', 'budget_weather'),
				array('Scope weather', '', 'scope_weather'),
                array('Schedule weather', '', 'schedule_weather'),
                array('Resource weather', '', 'resources_weather'),
                array('Technical weather', '', 'technical_weather'),
                array('Resources', ''),
            ),
            'Details' => array(
                array('Project Name', '', 'project_name'),
                array('Project Manager', '', 'project_manager_id'),
                array('Program', '', 'project_amr_program_id'),
                array('Project details', '', ''),
				array('Project ID', '', 'id'),
                array('Project long name', '', 'long_project_name'),
				array('Project Code 1', '', 'project_code_1'),
                array('Project Code 2', '', 'project_code_2'),
                array('Company', '', 'company_id'),
                array('Project type', '', 'project_type_id'),
                array('Sub type', '', 'project_sub_type_id'),
                array('Sub sub type', '', 'project_sub_sub_type_id'),
                array('Sub program', '', 'project_amr_sub_program_id'),
                array('Priority', '', 'project_priority_id'),
                array('Implementation Complexity', '', 'complexity_id'),
                array('Created value', '', 'created_value'),
                array('Status', '', 'project_status_id'),
                array('Current Phase', '', 'project_phase_id'),
				// Comment theo yeu cau ticket #614 30/7/2020
                // array('Link To RMS Activity', '', 'activity_id'),
                array('Issues', '', 'issues'),
                array('Primary Objectives', '', 'primary_objectives'),
                array('Project Objectives', '', 'project_objectives'),
                array('Constraint', '', 'constraint'),
                array('Remark', '', 'remark'),
                array('Chief Business', '', 'chief_business_id'),
                array('Technical manager', '', 'technical_manager_id'),
                array('Start Date', '', 'start_date'),
                array('End Date', '', 'end_date'),
                array('Customer', '', 'budget_customer_id'),
                array('Updated by resource', '', 'update_by_employee'),
                array('Last Update: {time} {date} by {resource}', '', ''),
                array('Last modified', '', 'last_modified'),
                array('Functional leader', '', 'functional_leader_id', 0),
                array('UAT manager', '', 'uat_manager_id', 0),
                array('Read Access', '', 'read_access', 0),
                array('Timesheet Filling Activated', '', 'activated'),
                array('Free 1', '', 'free_1', 0),
                array('Free 2', '', 'free_2', 0),
                array('Free 3', '', 'free_3', 0),
                array('Free 4', '', 'free_4', 0),
                array('Free 5', '', 'free_5', 0),

                array('Date 1', '', 'date_1', 0),
                array('Date 2', '', 'date_2', 0),
                array('Date 3', '', 'date_3', 0),
                array('Date 4', '', 'date_4', 0),
                array('List 1', '', 'list_1', 0),
                array('List 2', '', 'list_2', 0),
                array('List 3', '', 'list_3', 0),
                array('List 4', '', 'list_4', 0),
                array('Yes/No 1', '', 'yn_1', 0),
                array('Yes/No 2', '', 'yn_2', 0),
                array('Yes/No 3', '', 'yn_3', 0),
                array('Yes/No 4', '', 'yn_4', 0),
                array('0/1 1', '', 'bool_1', 0),
                array('0/1 2', '', 'bool_2', 0),
                array('0/1 3', '', 'bool_3', 0),
                array('0/1 4', '', 'bool_4', 0),
                array('Price 1', '', 'price_1', 0),
                array('Price 2', '', 'price_2', 0),
                array('Price 3', '', 'price_3', 0),
                array('Price 4', '', 'price_4', 0),
                array('Price 5', '', 'price_5', 0),
                array('Price 6', '', 'price_6', 0),
                array('Number 1', '', 'number_1', 0),
                array('Number 2', '', 'number_2', 0),
                array('Number 3', '', 'number_3', 0),
                array('Number 4', '', 'number_4', 0),
                array('Number 5', '', 'number_5', 0),
                array('Number 6', '', 'number_6', 0),
                array('Number 7', '', 'number_7', 0),
                array('Number 8', '', 'number_8', 0),
				// Comment theo yeu cau ticket #614 30/7/2020
                // array('Editor 1', '', 'editor_1', 0),
                // array('Editor 2', '', 'editor_2', 0),
                // array('Editor 3', '', 'editor_3', 0),
                // array('Editor 4', '', 'editor_4', 0),
                // array('Editor 5', '', 'editor_5', 0),
                // add new 28/09/2016
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

                array('Date 5', '', 'date_5', 0),
                array('Date 6', '', 'date_6', 0),
                array('Date 7', '', 'date_7', 0),
                array('Date 8', '', 'date_8', 0),
                array('Date 9', '', 'date_9', 0),
                array('Date 10', '', 'date_10', 0),
                array('Date 11', '', 'date_11', 0),
                array('Date 12', '', 'date_12', 0),
                array('Date 13', '', 'date_13', 0),
                array('Date 14', '', 'date_14', 0),

                array('Date(MM/YY) 1', '', 'date_mm_yy_1', 0),
                array('Date(MM/YY) 2', '', 'date_mm_yy_2', 0),
                array('Date(MM/YY) 3', '', 'date_mm_yy_3', 0),
                array('Date(MM/YY) 4', '', 'date_mm_yy_4', 0),
                array('Date(MM/YY) 5', '', 'date_mm_yy_5', 0),

                array('Date(YY) 1', '', 'date_yy_1', 0),
                array('Date(YY) 2', '', 'date_yy_2', 0),
                array('Date(YY) 3', '', 'date_yy_3', 0),
                array('Date(YY) 4', '', 'date_yy_4', 0),
                array('Date(YY) 5', '', 'date_yy_5', 0),
                array('Number 9', '', 'number_9', 0),
                array('Number 10', '', 'number_10', 0),
                array('Number 11', '', 'number_11', 0),
                array('Number 12', '', 'number_12', 0),
                array('Number 13', '', 'number_13', 0),
                array('Number 14', '', 'number_14', 0),
                array('Number 15', '', 'number_15', 0),
                array('Number 16', '', 'number_16', 0),
                array('Number 17', '', 'number_17', 0),
                array('Number 18', '', 'number_18', 0),

                array('Yes/No 5', '', 'yn_5', 0),
                array('Yes/No 6', '', 'yn_6', 0),
                array('Yes/No 7', '', 'yn_7', 0),
                array('Yes/No 8', '', 'yn_8', 0),
                array('Yes/No 9', '', 'yn_9', 0),
                array('Price 7', '', 'price_7', 0),
                array('Price 8', '', 'price_8', 0),
                array('Price 9', '', 'price_9', 0),
                array('Price 10', '', 'price_10', 0),
                array('Price 11', '', 'price_11', 0),
                array('Price 12', '', 'price_12', 0),
                array('Price 13', '', 'price_13', 0),
                array('Price 14', '', 'price_14', 0),
                array('Price 15', '', 'price_15', 0),
                array('Price 16', '', 'price_16', 0),

                array('Text one line 1', '', 'text_one_line_1', 0),
                array('Text one line 2', '', 'text_one_line_2', 0),
                array('Text one line 3', '', 'text_one_line_3', 0),
                array('Text one line 4', '', 'text_one_line_4', 0),
                array('Text one line 5', '', 'text_one_line_5', 0),
                array('Text one line 6', '', 'text_one_line_6', 0),
                array('Text one line 7', '', 'text_one_line_7', 0),
                array('Text one line 8', '', 'text_one_line_8', 0),
                array('Text one line 9', '', 'text_one_line_9', 0),
                array('Text one line 10', '', 'text_one_line_10', 0),
                array('Text one line 11', '', 'text_one_line_11', 0),
                array('Text one line 12', '', 'text_one_line_12', 0),
                array('Text one line 13', '', 'text_one_line_13', 0),
                array('Text one line 14', '', 'text_one_line_14', 0),
                array('Text one line 15', '', 'text_one_line_15', 0),
                array('Text one line 16', '', 'text_one_line_16', 0),
                array('Text one line 17', '', 'text_one_line_17', 0),
                array('Text one line 18', '', 'text_one_line_18', 0),
                array('Text one line 19', '', 'text_one_line_19', 0),
                array('Text one line 20', '', 'text_one_line_20', 0),

                array('Text two line 1', '', 'text_two_line_1', 0),
                array('Text two line 2', '', 'text_two_line_2', 0),
                array('Text two line 3', '', 'text_two_line_3', 0),
                array('Text two line 4', '', 'text_two_line_4', 0),
                array('Text two line 5', '', 'text_two_line_5', 0),
                array('Text two line 6', '', 'text_two_line_6', 0),
                array('Text two line 7', '', 'text_two_line_7', 0),
                array('Text two line 8', '', 'text_two_line_8', 0),
                array('Text two line 9', '', 'text_two_line_9', 0),
                array('Text two line 10', '', 'text_two_line_10', 0),
                array('Text two line 11', '', 'text_two_line_11', 0),
                array('Text two line 12', '', 'text_two_line_12', 0),
                array('Text two line 13', '', 'text_two_line_13', 0),
                array('Text two line 14', '', 'text_two_line_14', 0),
                array('Text two line 15', '', 'text_two_line_15', 0),
                array('Text two line 16', '', 'text_two_line_16', 0),
                array('Text two line 17', '', 'text_two_line_17', 0),
                array('Text two line 18', '', 'text_two_line_18', 0),
                array('Text two line 19', '', 'text_two_line_19', 0),
                array('Text two line 20', '', 'text_two_line_20', 0),

                array('Team', '', 'team', 0),

                // add new 01/12/2016
                array('List(multiselect) 1', '', 'list_muti_1', 0),
                array('List(multiselect) 2', '', 'list_muti_2', 0),
                array('List(multiselect) 3', '', 'list_muti_3', 0),
                array('List(multiselect) 4', '', 'list_muti_4', 0),
                array('List(multiselect) 5', '', 'list_muti_5', 0),
                array('List(multiselect) 6', '', 'list_muti_6', 0),
                array('List(multiselect) 7', '', 'list_muti_7', 0),
                array('List(multiselect) 8', '', 'list_muti_8', 0),
                array('List(multiselect) 9', '', 'list_muti_9', 0),
                array('List(multiselect) 10', '', 'list_muti_10', 0),
                array('Pictures', '', 'pictures', 0),
				// Comment theo yeu cau ticket #614 30/7/2020
                // array('Upload documents 1', '', 'upload_documents_1', 0),
                // array('Upload documents 2', '', 'upload_documents_2', 0),
                // array('Upload documents 3', '', 'upload_documents_3', 0),
                // array('Upload documents 4', '', 'upload_documents_4', 0),
                // array('Upload documents 5', '', 'upload_documents_5', 0),
                // array('Next milestone in week', '', 'next_milestone_in_week', 0),
                // array('Next milestone in day', '', 'next_milestone_in_day', 0),
				array('Lists', '', 'list', 0),
            ),
            'Details_new' => array(
                array('Project Name', '', 'project_name'),
                array('Project Manager', '', 'project_manager_id'),
                array('Program', '', 'project_amr_program_id'),
                // array('Status', '', 'project_status_id'),
                // array('Read Access', '', 'read_access'),
            ),
            'Total_Cost' => array(
                array('Total Costs', '', 'total_cost'),
                array('Budget €', '', 'tt_cost_budget'),
                array('Forecast €', '', 'tt_cost_forecast'),
                array('Var %', '', 'tt_cost_var'),
                array('Engaged €', '', 'tt_cost_engaged'),
                array('Remain €', '', 'tt_cost_remain'),
                //array('M.D', 'J.H', 'man_day'),
            ),
            'External_Cost' => array(
                array('External Cost', 'Coût Externe', 'external_cost'),
                array('Name', 'Nom', 'name'),
                array('Order date', 'Date de commande', 'order_date'),
                array('Provider', 'Fournisseur', 'budget_provider_id'),
                array('Type', '', 'budget_type_id', 0),
                array('CAPEX/OPEX', 'Investissement/Charge', 'capex_id', 0),
                array('Budget €', 'Budget €', 'budget_erro'),
                array('Forecast €', 'Forecast €', 'forecast_erro'),
                array('Var', 'Var', 'var_erro'),
                array('Ordered €', 'Fournisseur €', 'ordered_erro'),
                array('Remain €', 'Reste à faire', 'remain_erro'),
                array('M.D', 'J.H', 'man_day'),
                array('Consumed', 'Consommé', 'special_consumed'),
                array('Progress %', 'Avancement %', 'progress_md'),
                array('Progress €', 'Avancement €', 'progress_erro'),
                array('OPEX €', 'Charge €', 'opex_calculated', 0),
                array('CAPEX €', 'Investissement €', 'capex_calculated', 0),
                array('Attachement or URL', 'Fichier attaché ou URL', 'file_attachement'),
                array('Reference', 'Référence', 'reference', 0),
                array('Reference 2', 'Référence 2', 'reference2', 0),
                array('Reference 3', 'Référence 3', 'reference3', 0),
                array('Reference 4', 'Référence 4', 'reference4', 0),
                array('Expected date', 'Expected date', 'expected_date', 0),
                array('Due date', 'Due date', 'due_date', 0),
                array('Profit Center', 'Equipe', 'profit_center_id'),
                array('Funder', 'Financeur', 'funder_id')
            ),
            'Internal_Cost' => array(
                array('Internal Cost', 'Coût I​nterne', 'internal_cost'),
                array('Name', 'Nom', 'name'),
                array('Validation date', 'Validation date', 'validation_date'),
                array('Budget €', 'Budget €', 'budget_erro'),
                array('Forecast €', 'Forecast €', 'forecast_erro'),
                array('Var % (€)', 'Var % (€)', 'var_percent'),
                array('Engaged €', 'Validation date', 'engaged_erro'),
                array('Remain €', 'Reste à faire', 'remain_erro'),
                array('Budget M.D', 'Budget J.H', 'budget_md'),
                array('Forecast M.D', 'Forecast J.H', 'forecast_md'),
                array('Var % (M.D)', 'Var % (J.H)', 'var_percent_md'),
                array('Engaged M.D', 'Consommé J.H', 'engaged_md'),
                array('Remain M.D', 'Reste à faire J.H', 'remain_md'),
                array('Average daily rate €', 'Cout journalier moyen €', 'average'),
                array('Profit Center', 'Equipe', 'profit_center_id'),
                array('Funder', 'Financeur', 'funder_id')
                //Var %
            ),
            'Sales' => array(
                array('Sales', 'Vente', 'sales'),
                array('Name', 'Nom', 'name'),
                array('Customer', 'Client', 'budget_customer_id'),
                array('Order date', 'Date de commande', 'order_date'),
                array('Sold €', 'Vendu €', 'sold'),
                array('To Bill €', 'A Facturer €', 'billed'),
                array('Billed €', 'Facturé €', 'billed_check'),
                array('Paid €', 'Payé €', 'paid'),
                array('M.D', 'J.H', 'man_day'),
                array('Due Date', 'Date échéance', 'due_date', 0),
                array('Effective Date', 'Date effective', 'effective_date', 0),
                array('Reference', 'Référence', 'reference'),
                array('Reference 2', 'Référence 2', 'reference2'),
                array('Attachement or URL', 'Fichier attaché ou URL', 'file_attachement'),
                array('Justification', 'Justificatif', 'justification'),
                //array('Invoice', 'Facture')
            ),
            'Purchase' => array(
                array('Name', 'Nom', 'name'),
                array('Customer', 'Client', 'budget_customer_id'),
                array('Order date', 'Date de commande', 'order_date'),
                array('Sold €', 'Vendu €', 'sold'),
                array('To Bill €', 'A Facturer €', 'billed'),
                array('Billed €', 'Facturé €', 'billed_check'),
                array('Paid €', 'Payé €', 'paid'),
                array('M.D', 'J.H', 'man_day'),
                array('Due Date', 'Date échéance', 'due_date', 0),
                array('Effective Date', 'Date effective', 'effective_date', 0),
                array('Reference', 'Référence', 'reference'),
                array('Reference 2', 'Référence 2', 'reference2'),
                array('Attachement or URL', 'Fichier attaché ou URL', 'file_attachement'),
                array('Justification', 'Justificatif', 'justification'),
                //array('Invoice', 'Facture')
            ),
            'FY_Budget' => array(
                array('Order', 'Commande', 'order'),
                array('To bill', 'A Facturer', 'to_bill'),
                array('Paid', 'Payé', 'paid'),
                array('Sale', 'Vente', 'sale'),
                array('Purchase', 'Achat', 'purchase'),
                array('Sale-Purchase', 'Vente-Achat', 'vente_purchase'),
                array('Xxxx', 'Xxxx', 'xxxx'),
            ),
            'Resource' => array(
                array('ID2', '', ''),
                array('ID3', '', ''),
                array('ID4', '', ''),
                array('ID5', '', ''),
                array('ID6', '', '')
            ),
            'Provisional' => array(
                array('Provisional', '', 'provisional'),
                array('Budget Provisional M.D', '', 'provisional_budget_md'),
                array('Budget Provisional', '', 'provisional_budget')
            ),
            'Finance' => array(
                array('Name', '', 'name'),
                array('Total', '', 'total'),
                array('Budget', '', 'budget'),
                array('Engaged', 'Engagé', 'engaged'),
                array('Avancement', '', 'avancement'),
                array('Budget Investment', '', 'budget_investment'),
                array('Budget Operation', '', 'budget_operation'),
                array('Finance Investment', '', 'finan_investment'),
                array('Finance Operation', '', 'finan_operation'),
                array('Investment Total Budget', '', 'inv_budget'),
                array('Investment Total Avancement', '', 'inv_avancement'),
                array('Investment Total Percent', '', 'inv_percent'),
                array('Operation Total Budget', '', 'fon_budget'),
                array('Operation Total Avancement', '', 'fon_avancement'),
                array('Operation Total Percent', '', 'fon_percent'),
                array('Investment Budget (Y)', '', 'inv_budget_y'),
                array('Investment Avancement (Y)', '', 'inv_avancement_y'),
                array('Investment Percent (Y)', '', 'inv_percent_y'),
                array('Operation Budget (Y)', '', 'fon_budget_y'),
                array('Operation Avancement (Y)', '', 'fon_avancement_y'),
                array('Operation Percent (Y)', '', 'fon_percent_y'),
                array('BP Investment Ville', '', 'BP Investment Ville'),
                array('BP Operation Ville', '', 'BP Operation Ville'),
                array('Available Investment', '', 'Investissements disponibles'),
                array('Available Operation', '', 'Fonctionnement disponible'),
                array('Budget Total', '', 'Total du budget'),
            ),
            'Finance_2' => array(
                array('Name', '', 'name'),
                array('Total', '', 'total'),
                array('Budget revised', '', 'Budget révisé'),
                array('Budget initial', '', 'Budget initial'),
                array('Latest estimate', '', 'Dernière estimation'),
                array('DR - DE', '', 'DR - DE'),
                array('Engaged', '', 'Engagé'),
                array('Bill', '', 'Facturé'),
                array('Disbursed', '', 'Décaissé'),
            ),
            'Project_Task' => array(
                array('Task', '', 'task_title'),
                array('Order', '', 'progress_order'),
                array('Priority', '', 'task_priority_id'),
                array('Status', '', 'task_status_id'),
                array('Profile', '', 'profile_id'),
                array('Assigned To', '', 'task_assign_to'),
                array('Start date', '', 'task_start_date'),
                array('End date', '', 'task_end_date'),
                array('Duration', '', 'duration'),
                array('Predecessor', '', 'predecessor'),
                array('Workload', '', ''),
                array('Overload', '', 'overload'),
                array('Consumed', '', 'special_consumed'),
                array('Manual Consumed', '', 'manual_consumed'),
                array('In Used', '', ''),
                array('Completed', '', ''),
                array('Remain', '', ''),
                array('Initial workload', '', ''),
                array('Initial start date', '', 'initial_task_start_date'),
                array('Initial end date', '', 'initial_task_end_date'),
                array('Amount €', '', 'amount'),
                array('% progress order', '', 'progress_order'),
                array('% progress order €', '', ''),
                array('Attachment', '', 'attachment'),
                array('+/-', '', ''),
                array('Unit Price', '', 'unit_price'),
                array('Consumed €', '', ''),
                array('Remain €', '', ''),
                array('Workload €', '', ''),
                array('Estimated €', '', 'Budget révisé'),
                array('Milestone', '', 'milestone_id'),
            ),
            'Created_Value' => array(
                array('Financial', '', 'financial'),
                array('How should the project appear to our stakeholders?', "Qu'apporte le projet pour les investisseurs ?", 'financial_question'),
                array('Business Process', '', 'business'),
                array('What business processes must the project excel at?', "Quel processus métier le projet améliore t'il", 'business_question'),
                array('Customer', '', 'customer'),
                array('How should the project appear to our customers?', "Quel est la perception du projet pour nos clients ?", 'customer_question'),
                array('Learning & Growth', '', 'learning'),
                array('How can we sustain our ability to change and Improve?', "Comment pouvons-nous maintenir notre capacité à changer?", 'learning_question'),
            ),
            'Budget_Investment' => array(
                array('Total', '', 'total_budget_inv'),
                array('Budget', '', 'budget_budget_inv'),
                array('Engaged', 'Engagé', 'engaged_budget_inv'),
                array('Avancement', '', 'avancement_budget_inv'),
                array('Budget Investment', '', 'budget_inv'),
                array('Date', '', 'date_budget_inv'),
                array('Budget Investment (Y)', '', 'inv_budget_y'),
                array('Budget Investment Avancement (Y)', '', 'inv_avan_budget_y'),
                array('Budget Investment Percent (Y)', '', 'inv_percent_budget_y'),
            ),
            'Budget_Operation' => array(
                array('Total', '', 'total_budget_ope'),
                array('Budget', '', 'budget_budget_ope'),
                array('Engaged', 'Engagé', 'engaged_budget_ope'),
                array('Avancement', '', 'avancement_budget_ope'),
                array('Budget Operation', '', 'budget_ope'),
				array('Date', '', 'date_budget_ope'),
                array('Budget Operation (Y)', '', 'ope_budget_y'),
                array('Budget Operation Avancement (Y)', '', 'ope_avan_ope_y'),
                array('Budget Operation Percent (Y)', '', 'ope_percent_budget_y'),
            ),
            'Finance_Investment' => array(
                array('Total', '', 'total_finan_inv'),
                array('Budget', '', 'budget_finan_inv'),
                array('Engaged', 'Engagé', 'engaged_finan_inv'),
                array('Avancement', '', 'avancement_finan_inv'),
				array('Date', '', 'date_finan_inv'),
                array('Finance Investment', '', 'finan_inv'),
				array('Finance Investment Total Budget', '', 'finan_inv_budget'),
                array('Finance Investment Total Avancement', '', 'finan_inv_avancement'),
                array('Finance Investment Total Percent', '', 'finan_inv_percent'),
                array('Finance Investment Budget (Y)', '', 'finan_inv_y'),
                array('Finance Investment Avancement (Y)', '', 'finan_inv_avan_y'),
                array('Finance Investment Percent (Y)', '', 'finan_inv_percent_y'),
            ),
            'Finance_Operation' => array(
                array('Total', '', 'total_finan_ope'),
                array('Budget', '', 'budget_finan_ope'),
                array('Engaged', 'Engagé', 'engaged_finan_ope'),
                array('Avancement', '', 'avancement_finan_ope'),
                array('Finance Operation', '', 'finan_ope'),
				array('Date', '', 'date_finan_ope'),
				array('Finance Operation Total Budget', '', 'finan_ope_budget'),
                array('Finance Operation Total Avancement', '', 'finan_ope_avancement'),
                array('Finance Operation Total Percent', '', 'finan_ope_percent'),
                array('Finance Operation Budget (Y)', '', 'finan_ope_y'),
                array('Finance Operation Avancement (Y)', '', 'finan_ope_avan_y'),
                array('Finance Operation Percent (Y)', '', 'finan_ope_percent_y'),
            )
        );
		return $origins;
	}
	public function add_field_display(){
		$result = false;
		$data = array();
		$message = '';
		$this->layout = false;
		if (!empty($this->data)) {
			$this->data['company_id'] = $this->employee_info["Company"]["id"];
			if($this->data['show'] == '1'){
				$page = $this->Translation->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'Translation.id' => $this->data['field_id'],
					),
					'fields' => 'Translation.page'
				));
				$page = !empty($page) ? $page['Translation']['page'] : '';
				$max_order = $page = $this->Translation->find('list', array(
					'recursive' => -1,
					'joins' => array(
						array(
							'table' => 'translation_settings',
							'alias' => 'TranslationSetting',
							'conditions' => array(
								'Translation.id = TranslationSetting.translation_id',
							),
							'type' => 'inner'
						),
					),
					'conditions' => array(
						'Translation.page' => $page,
					),
					'fields' => array( 'TranslationSetting.translation_id',  'TranslationSetting.setting_order'),
					'order' => array(
						'setting_order' => 'Desc'
					),
					'limit' => 1
				));
				$max_order = !empty($max_order) ? array_shift($max_order) : 0;
				$max_order++;
				// debug( $max_order); exit;
				$result = $this->TranslationSetting->updateAll(array(
					'TranslationSetting.show' => $this->data['show'],
					'TranslationSetting.setting_order' => $max_order,
				), array(
					'TranslationSetting.translation_id' => $this->data['field_id'],
					'TranslationSetting.company_id' => $this->employee_info['Company']['id']
				));
			}else{
				$result = $this->TranslationSetting->updateAll(array(
					'TranslationSetting.show' => $this->data['show'],
				), array(
					'TranslationSetting.translation_id' => $this->data['field_id'],
					'TranslationSetting.company_id' => $this->employee_info['Company']['id']
				));
			}
			if( $result ){
				$data = $this->TranslationSetting->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'TranslationSetting.translation_id' => $this->data['field_id'],
						'TranslationSetting.company_id' => $this->data['company_id']
					),
					'fields' => '*'
				));
			}else{
				$this->Session->setFlash(__('Submit failed, please correct data before submit.', true), 'error');
			}
        }else{
			$this->Session->setFlash(__('Submit failed, please correct data before submit.', true), 'error');
		}
		die(json_encode(array(
			'result' => $result ? 'success' : 'failed',
			'data' => $data,
			'company_id' => $this->company_id,
			'message' => $message,
		)));
    }
}
