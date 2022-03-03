<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectFinancesPreviewController extends AppController {

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $uses = array('ProjectFinance', 'ProjectFinancePlus', 'ProjectFinancePlusDetail', 'Project', 'ProjectFinancePlusDate');
    /**
     * index
     *
     * @return void
     * @access public
     */
	var $components = array('MultiFileUpload', 'SlickExporter');
    public function index($project_id = null){
        if (!$this->_checkRole(false, $project_id)) {
            $this->data = null;
        }
        $this->_checkWriteProfile('finance');
		$bg_currency = $this->getCurrencyOfBudget();
        $valDefault = '0.00' ;
        $this->loadModels('ProjectFinancePartner', 'Project');
        $projectName = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('id' => $project_id),
            'fields' => array('id', 'project_name')
        ));
        $data = $this->ProjectFinance->find('all',array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id)
        ));
        if(empty($data)) {
            $data['bp_investment_city'] =  $valDefault;
            $data['bp_operation_city'] =  $valDefault;
            $data['available_investment'] =  $valDefault;
            $data['available_operation'] =  $valDefault;
            $data['finance_total_budget'] =  $valDefault;
            $data['finance_plan'] =  $valDefault;
            $data['comment'] = '';
            $saves = array('project_id' => $project_id);
            $this->ProjectFinance->create();
            $this->ProjectFinance->save($saves);
            $finance_id = $this->ProjectFinance->getLastInsertID();
        } else {
            $data = Set::classicExtract($data,'{n}.ProjectFinance');
            $data = $data[0];
            $data['finance_plan'] =  $data['finance_total_budget'] - $data['bp_investment_city'] - $data['bp_operation_city'];
            $finance_id = $data['id'];
        }

        //DATA PARTNER
        $dataPartner = $this->ProjectFinancePartner->find('all',array(
            'recursive' => -1,
            'conditions' => array('finance_id' => $finance_id,'finance_partner <> ' => ''),
            'order' => array('id')
        ));
        $this->ProjectFinancePartner->deleteAll(
            array('ProjectFinancePartner.finance_id' => $finance_id,'ProjectFinancePartner.finance_partner' => '')
        );
        $this->set(compact('project_id','data','finance_id','dataPartner', 'projectName', 'bg_currency'));
    }
    public function update($project_id = null){
        if(isset($_POST['updateMe'])&&$_POST['updateMe'] == 1) {
            $value = $_POST['value'];
            if( $_POST['field'] == 'comment' ){
                $db = $this->ProjectFinance->getDataSource();
                $value = $db->value($value, 'string');
            }
            $this->ProjectFinance->updateAll(
                array('ProjectFinance.'.$_POST['field'] => $value),
                array('ProjectFinance.project_id ' => $project_id)
            );
            echo 1;
        } else echo -1;
        exit;
    }
    public function add_partner($finance_id = null){
        $this->loadModel('ProjectFinancePartner');
        if( isset($_POST['add']) && $_POST['add'] == 1 && $finance_id != null ){
            $saves = array('finance_id' => $finance_id);
            $this->ProjectFinancePartner->create();
            $this->ProjectFinancePartner->save($saves);
            $partner_id = $this->ProjectFinancePartner->getLastInsertID();
            echo $partner_id;
        }
        exit;
    }
    public function delete_partner($partner_id = null){
        $this->loadModel('ProjectFinancePartner');
        if( isset($_POST['del']) && $_POST['del'] == 1 && $partner_id != null ){
            $check = $this->ProjectFinancePartner->find('count',array(
                'recursive' => -1,
                'conditions' => array('id' => $partner_id)
            ));
            if($check) {
                $check = $this->ProjectFinancePartner->delete($partner_id);
            }
            echo "Deleted!";
        }
        exit;
    }
    public function update_partner($id = null){
        $this->loadModel('ProjectFinancePartner');
        if(isset($_POST['updateMe']) && $_POST['updateMe'] == 1) {
            $value = $_POST['value'];
            if($_POST['field'] == 'finance_percent') {
                $this->ProjectFinancePartner->updateAll(
                    array('ProjectFinancePartner.'.$_POST['field'] => $value),
                    array('ProjectFinancePartner.id ' => $id)
                );
                echo 1;
            } else {
                $this->ProjectFinancePartner->updateAll(
                    array('ProjectFinancePartner.'.$_POST['field'] => "'$value'"),
                    array('ProjectFinancePartner.id ' => $id)
                );
                echo 'Updated!';
            }
        }
        exit;
    }

    public function index_plus($project_id){
        /**
         * Lay start date va end date
         */
        if (!$this->_checkRole(false, $project_id)) {
            $this->data = null;
        }
        $this->_checkWriteProfile('finance_plus');
		$bg_currency = $this->getCurrencyOfBudget();
        $employee_info = $this->employee_info;
		$types = array('inv', 'fon', 'finaninv', 'finanfon');
        $invStart = $invEnd = $fonStart = $fonEnd = '';
        $getSaveHistory = $this->ProjectFinancePlusDate->find('first', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id)
        ));
		$invStart = $fonStart = $finanFonStart = $finanInvStart = '';
		$invEnd = $fonEnd = $finanFonEnd = $finanInvEnd = '';
		$type_full = '';
		foreach($types as $type){
			$typeStart = $typeEnd = '';
			if(!empty($getSaveHistory)) {
				$typeStart = !empty($getSaveHistory['ProjectFinancePlusDate'][$type.'_start']) ? $getSaveHistory['ProjectFinancePlusDate'][$type.'_start'] : '';
				$typeEnd = !empty($getSaveHistory['ProjectFinancePlusDate'][$type.'_end']) ? $getSaveHistory['ProjectFinancePlusDate'][$type.'_end'] : '';
			}				
			$typeStart = !empty($this->params['url'][$type.'_start']) ? strtotime(@$this->params['url'][$type.'_start']) : (!empty($typeStart) ? $typeStart : time());
			$typeEnd = !empty($this->params['url'][$type.'_end']) ? strtotime(@$this->params['url'][$type.'_end']) : (!empty($typeEnd) ? $typeEnd : time());
			
			if(!empty($this->params['url'][$type.'_full'])) $type_full = '#'.$type.'-chard';
			
			if($type == 'inv'){
				$invStart = $typeStart;
				$invEnd = $typeEnd;
			}
			if($type == 'fon'){
				$fonStart = $typeStart;
				$fonEnd = $typeEnd;
			}
			if($type == 'finaninv'){
				$finanInvStart = $typeStart;
				$finanInvEnd = $typeEnd;
			}
			if($type == 'finanfon'){
				$finanFonStart = $typeStart;
				$finanFonEnd = $typeEnd;
			}
		}
        /**
         * Lay project
         */		
        $projects = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('Project.id' => $project_id),
            'fields' => array('id', 'activity_id', 'project_name', 'company_id')
        ));
		
        $company_id = !empty($projects['Project']['company_id']) ? $projects['Project']['company_id'] : 0;
        $activity_id = !empty($projects['Project']['activity_id']) ? $projects['Project']['activity_id'] : 0;
        $projectName = !empty($projects['Project']['project_name']) ? $projects['Project']['project_name'] : '';
        /**
         * Lay cac finance+
         */
        $finances = $this->ProjectFinancePlus->find('all', array(
            'recursive' => -1,
            'conditions' => array(
				'project_id' => $project_id,
				// 'activity_id' => $activity_id,
			),
            'fields' => array('id', 'name', 'type', 'finance_date'),
            // 'group' => array('type', 'id')
        ));
		// debug( $finances); exit;
		$list_keys = !empty($finances) ? Set::classicExtract($finances, '{n}.ProjectFinancePlus.id') : array();
		
		$new = array();
		if( !empty($finances)){
			foreach( $finances as $key => $finance){
				$finance = $finance['ProjectFinancePlus'];
				$finance['finance_date'] = (!empty( $finance['finance_date']) && $finance['finance_date'] != '0000-00-00') ? date("d-m-Y", strtotime($finance['finance_date'])) : '';
				$type = strtolower($finance['type']);
				$new[$type][$finance['id']] = $finance;
			}
		}
		$finances = $new;
		unset( $new);
		// Lay Comment
		$this->loadModels('ProjectFinancePlusTxt', 'ProjectFinancePlusTxtView');
		$list_comments = $this->ProjectFinancePlusTxt->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'project_finance_plus_id' => $list_keys,
				'comment not' => null,
				'comment !=' => '',
			),
			'fields' => array('id','project_finance_plus_id'),
		));
		$read_comments = $this->ProjectFinancePlusTxtView->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'project_finance_plus_id' => $list_keys,
				'employee_id' => $this->employee_info['Employee']['id'],
			),
			'fields' => array('project_finance_plus_id','read_status'),
		));
		// Lay Attachment
		$this->loadModels('ProjectFinancePlusAttachment', 'ProjectFinancePlusAttachmentView');
		$list_attachments = $this->ProjectFinancePlusAttachment->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'project_finance_plus_id' => $list_keys,
			),
			'fields' => array('id','project_finance_plus_id'),
		));
		$read_attachments = $this->ProjectFinancePlusAttachmentView->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'project_finance_plus_id' => $list_keys,
				'employee_id' => $this->employee_info['Employee']['id'],
			),
			'fields' => array('project_finance_plus_id','read_status'),
		));
        /**
         * Finance detail
         */
        $_financeDetails = $this->ProjectFinancePlusDetail->find('all', array(
            'recursive' => -1,
            'conditions' => array(
				'project_id' => $project_id,
				// 'activity_id' => $activity_id
			),
            'fields' => array('project_finance_plus_id', 'model', 'year', 'value', 'type')
        ));
        $financeDetails = array();
        $yearOfFinances = array();
        $totals = array();
        if( !empty($_financeDetails) ){
            foreach($_financeDetails as $financeDetail){
                $dx = $financeDetail['ProjectFinancePlusDetail'];
                $financeDetails[$dx['project_finance_plus_id']][$dx['model'] . '_' . $dx['year']] = $dx;
                if( !empty( $dx['year'])) $yearOfFinances[$dx['type']][$dx['year']] = $dx['year'];
                if(empty($totals[$dx['type']][$dx['model']])){
                    $totals[$dx['type']][$dx['model']] = 0;
                }
                $totals[$dx['type']][$dx['model']] += $dx['value'];
            }
        }
		// debug( $_financeDetails);
		// debug( $yearOfFinances);
		foreach($types as $type ){
			if( !empty($yearOfFinances[$type]) ){
				$typeStartData = strtotime(date('d-m') . '-' . min($yearOfFinances[$type]));
				$typeEndData = strtotime(date('d-m') . '-' . max($yearOfFinances[$type]));
				if(empty($this->params['url'][$type.'_start'])){
					if($type == 'inv' && $typeStartData < $invStart){
						$invStart = $typeStartData;
					}
					if($type == 'fon' && $typeStartData < $fonStart){
						$fonStart = $typeStartData;
					}
					if($type == 'finaninv' && $typeStartData < $finanInvStart){
						$finanInvStart = $typeStartData;
					}
					if($type == 'finanfon' && $typeStartData < $finanFonStart){
						$finanFonStart = $typeStartData;
					}
				}
				if(empty($this->params['url'][$type.'_end'])){
					if($type == 'inv' && $typeEndData > $invEnd){
						$invEnd = $typeEndData;
					}
					if($type == 'fon' && $typeEndData > $fonEnd){
						$fonEnd = $typeEndData;
					}
					if($type == 'finaninv' && $typeEndData > $finanInvEnd){
						$finanInvEnd = $typeEndData;
					}
					if($type == 'finanfon' && $typeEndData > $finanFonEnd){
						$finanFonEnd = $typeEndData;
					}
				}
				// debug( $type );
				// debug( date('y', $invStart));
			}
			if($type == 'inv'){
				$totals[$type]['start'] = $invStart;
				$totals[$type]['end'] = $invEnd;
			}
			if($type == 'fon'){
				$totals[$type]['start'] = $fonStart;
				$totals[$type]['end'] = $fonEnd;
			}
			if($type == 'finaninv'){
				$totals[$type]['start'] = $finanInvStart;
				$totals[$type]['end'] = $finanInvEnd;
			}
			if($type == 'finanfon'){
				$totals[$type]['start'] = $finanFonStart;
				$totals[$type]['end'] = $finanFonEnd;
			}
			
		}
        $saveHistory = array(
            'inv_start' => $invStart,
            'inv_end' => $invEnd,
            'fon_start' => $fonStart,
            'fon_end' => $fonEnd,
            'finaninv_start' => $finanInvStart,
            'finaninv_end' => $finanInvEnd,
            'finanfon_start' => $finanFonStart,
            'finanfon_end' => $finanFonEnd,
            'company_id' => $company_id,
            'project_id' => $project_id
        );
		// debug( $saveHistory);
		// debug( date('y', $invStart));
		// debug( date('y', $invEnd));
		// debug( date('y', $fonStart));
		// debug( date('y', $fonEnd));
        if( !empty($getSaveHistory) && !empty($getSaveHistory['ProjectFinancePlusDate']['id']) ){
            $this->ProjectFinancePlusDate->id = $getSaveHistory['ProjectFinancePlusDate']['id'];
        } else {
            $this->ProjectFinancePlusDate->create();
        }
        $this->ProjectFinancePlusDate->save($saveHistory);
        $this->loadModels('HistoryFilter');
        $history = $this->HistoryFilter->find('first', array(
            'conditions' => array(
                'path' => $this->params['url']['url'],
                'employee_id' => $employee_info['Employee']['id']
            ),
            'fields' => array('params')
        ));
        if( !empty($history) ){
            $history = json_decode($history['HistoryFilter']['params'], true);
        } else {
            $history = array();
        }
		$this->loadModels('Translation', 'TranslationSetting');
		$displayDateFields = $this->Translation->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'Translation.field' => array(
					'date_budget_inv',
					'date_budget_ope',
					'date_finan_inv',
					'date_finan_ope',
				),
				'TranslationSetting.company_id' => $company_id,
				'TranslationSetting.show' => 1
			),
			'fields' => array('Translation.field', 'TranslationSetting.show'),
			'joins' => array(
				array(
					'table' => 'translation_settings',
					'alias' => 'TranslationSetting',
					'conditions' => array(
						'Translation.id = TranslationSetting.translation_id',
					),
					'type' => 'inner'
				)
			)
		));
        $this->set(compact('invStart', 'invEnd', 'fonStart', 'fonEnd', 'finanInvStart', 'finanInvEnd', 'finanFonStart', 'finanFonEnd', 'project_id', 'projects', 'finances', 'financeDetails', 'projectName', 'history', 'totals', 'list_comments', 'read_comments','list_attachments', 'read_attachments', 'bg_currency', 'type_full','displayDateFields'));
		// exit;
    }
	public function update_text($id= null){
		$data = array();
		$message='';
		$result = array();
		if( !empty($this->data['id'])) $id = $this->data['id'];
		$finances = $this->ProjectFinancePlus->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $id 
			),
			'fields' => array('*'),
		));
		$project_id = $finances['ProjectFinancePlus']['project_id'];
		if( $this->_checkRole(true, $project_id)){
			if( $id ){
				$employee = $this->employee_info['Employee']['id'];
				$data['employee_id'] = $this->employee_info['Employee']['id'];
				$data['project_id'] = $project_id;
				$data['project_finance_plus_id'] = $this->data['id'];
				$data['comment'] = $this->data['text'];
				$data['created'] = time();
				$data['updated'] = time();
				$this->loadModel('ProjectFinancePlusTxt');
				$this->ProjectFinancePlusTxt->create();
				$result = $this->ProjectFinancePlusTxt->save($data);
				// read status đã được update trong getCommentTxt
			}
		}else{
			$message = __('You have not permission to access this function', true);
		}
		if( $result){
			$data = $this->getCommentTxt($id, true);
			$data['new_comment'] = $result;
		} 
        if( $this->params['isAjax']) 
			die(json_encode(array(
				'results' => $data ? 'success' : 'failed',
				'data' => $data ,
				'message' => $message
			)));
		else{
			return $data;
		}
		
	}
	public function getCommentTxt($id = null, $return = false){
		$data = array();
		$message='';
		if( $id ){
			$data['id'] = $id;
			$finances = $this->ProjectFinancePlus->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $id 
				),
				'fields' => array('*'),
			));
			// $project_id = $finances['ProjectFinancePlus']['project_id'];
			$data['title'] = $finances['ProjectFinancePlus']['name'];
			$this->loadModel('ProjectFinancePlusTxt');
			$result = $this->ProjectFinancePlusTxt->find('all', array(
				'recursive' => -1,
				'conditions' => array('project_finance_plus_id' => $id),
				'fields' => array('ProjectFinancePlusTxt.id', 'ProjectFinancePlusTxt.employee_id', 'ProjectFinancePlusTxt.is_ws_comment', 'ProjectFinancePlusTxt.comment', 'ProjectFinancePlusTxt.created','Employee.first_name', 'Employee.last_name'),
				'joins' => array(
					array(
						'table' => 'employees',
						'alias' => 'Employee',
						'type' => 'inner',
						'conditions' => array(
							'Employee.id = ProjectFinancePlusTxt.employee_id'
						)
					),
				),
				'order' => array('created' => "DESC" ),
			));
			foreach( $result as $key => $comment){
				$result[$key]['ProjectFinancePlusTxt']['text_time'] = date('Y-m-d H:i:s', $comment['ProjectFinancePlusTxt']['created'] );
				// Ticket 980 - When you write a msg do not display  the avatar , display avatar API 
				if( $comment['ProjectFinancePlusTxt']['is_ws_comment']){
					unset($result[$key]['ProjectFinancePlusTxt']['employee_id']);
					unset($result[$key]['Employee']);
				}
			}
			$data['result'] = $result;
			if( !empty($data['result']) ) $this->update_comment_read_status($id);
		}
		if( $return){
			return $data;
		}else{
			die(json_encode(array(
				'results' => $data ? 'success' : 'failed',
				'data' => $data ,
				'message' => $message
			)));
		}
		
    }
	private function update_comment_read_status($id = null){
		$finances = $this->ProjectFinancePlus->find('first', array(
            'recursive' => -1,
            'conditions' => array('id' => $id),
            'fields' => array('id', 'project_id'),
        ));
		$project_id = $finances['ProjectFinancePlus']['project_id'];
		// $this->_checkRole(false, $project_id);
		$result = false;
		$data = '';

		if( $finances ){
			$this->loadModel('ProjectFinancePlusTxtView');
			$data = $this->ProjectFinancePlusTxtView->find('first', array(
				'conditions' => array(
				'project_finance_plus_id' => $id,
				'employee_id' => $this->employee_info['Employee']['id'],
				),
			));
			if( empty( $data)){
				$data = $this->ProjectFinancePlusTxtView->create();
				$data['ProjectFinancePlusTxtView']['project_finance_plus_id'] = $id;
				$data['ProjectFinancePlusTxtView']['employee_id'] = $this->employee_info['Employee']['id'];
			}
			$data['ProjectFinancePlusTxtView']['read_status'] = 1;
			$data = $this->ProjectFinancePlusTxtView->save($data);
			if( $data) $result = true;
		}
		return $result;	
	}

	protected function _getPath($project_id) {
	// public function getPath($project_id) {
		$this->loadModel('Project', 'Company');
        $company = $this->Project->find('first', array(
            'recursive' => 0,
            'fields' => array(
                'Company.parent_id',
                'Company.company_name',
                'Company.dir'
            ), 
			'conditions' => array('Project.id' => $project_id)
		));
		// debug($company); exit;
		$pcompany = '';
		if( !empty( $company['Company']['parent_id'])){
			$pcompany = $this->Company->find('first', array(
				'recursive' => -1, 
				'conditions' => array(
					'Company.id' => $company['Company']['parent_id']
				)
			));
		}
        $path = FILES . 'projects' . DS . 'financeplus' . DS;
        if ($pcompany) {
            $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['dir'])) . DS;
        }
        $path .= $company['Company']['dir'] . DS . $project_id . DS;
		// debug( $path); exit;
        return $path;
    }
	public function upload_document(){
		$data = array();
		$message='';
		$result = array();
		$this->loadModel('ProjectFinancePlusAttachment');
		$finance_att = $this->ProjectFinancePlus->find('first', array(
            'recursive' => -1,
            'conditions' => array(
				'id' => $this->data['Upload']['f_id'],
			),
            'fields' => array('*'),
        ));
		$project_id = !empty($finance_att) ? $finance_att['ProjectFinancePlus']['project_id'] : '';
		if( $this->_checkRole(true, $project_id)){
			// debug(1); exit;
			if( !empty($this->data['Upload']['f_id']) ){
				$f_id = $this->data['Upload']['f_id'];
				if( !empty($_FILES) ){
					$_FILES['FileField'] = array();
					if(!empty($_FILES)){
						$_FILES['FileField']['name']['attachment'] = $_FILES['file']['name'];
						$_FILES['FileField']['type']['attachment'] = $_FILES['file']['type'];
						$_FILES['FileField']['tmp_name']['attachment'] = $_FILES['file']['tmp_name'];
						$_FILES['FileField']['error']['attachment'] = $_FILES['file']['error'];
						$_FILES['FileField']['size']['attachment'] = $_FILES['file']['size'];
					}
					unset($_FILES['file']);
					$filePath = realpath($_FILES["FileField"]["tmp_name"]['attachment']);
					$path = $this->_getPath($project_id);
					App::import('Core', 'Folder');
					new Folder($path, true, 0777);
					if (file_exists($path)) {
						$this->MultiFileUpload->encode_filename = false;
						$this->MultiFileUpload->uploadpath = $path;
						$this->MultiFileUpload->_properties['AttachTypeAllowed'] = "*";
						$this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
						$attachment = $this->MultiFileUpload->upload();
					} else {
						$attachment = "";
						$this->Session->setFlash(sprintf(__('File system save failed.', 'Could not create requested directory: %s.', true), $path), 'error');
					}
					if (!empty($attachment)) {
						$data = array(
                            'project_id'=> $project_id,
                            'project_finance_plus_id' => $f_id,
                            'employee_id' => $this->employee_info['Employee']['id'],
                            'attachment' =>  $attachment['attachment']['attachment'],
                            'is_file' => 1,
                            'is_https' => 0,
                            'created' => time(),
                            'updated' => time(),
                        );
                        $this->ProjectFinancePlusAttachment->create();
                        $result = $this->ProjectFinancePlusAttachment->save($data);
					} else {
						$this->Session->setFlash(implode(', ', array_values($this->MultiFileUpload->errors)), 'error');
					}
				}
				if(!empty($this->data['Upload']['url']) && empty($_FILES)){
					$data = array(
						'project_id'=> $project_id,
						'project_finance_plus_id' => $f_id,
						'attachment' => $this->data['Upload']['url'],
						'employee_id' => $this->employee_info['Employee']['id'],
						'is_file' => 0,
						'is_https' => 0, // Cái này để cho add iframe.
						'created' => time(),
						'updated' => time(),

					);
					$this->ProjectFinancePlusAttachment->create();
					$result = $this->ProjectFinancePlusAttachment->save($data);
				}
				if (!empty( $data)){
					// set unread for other employee
					$this->attachment_unread($f_id);
					// set read for curent employee
					$this->update_attachment_read_status($f_id);
				}
			}
		}else{
			$message = __('You have not permission to access this function', true);
		}
		if( $result){
			$data = $this->getAttachments($f_id, true);
			$data['new_attachment'] = $result;
		} 
        if( $this->params['isAjax']) 
			die(json_encode(array(
				'results' => $data ? 'success' : 'failed',
				'data' => $data ,
				'message' => $message
			)));
		else{
			return $data;
		}
	}
	public function attachment($att_id=null){
		if( empty($att_id) ) exit;
		$this->layout = false;
		$link = '';
		$project_id = '';
		$key = isset($_GET['sid']) ? $_GET['sid'] : '';
		$this->loadModels('Project', 'ProjectFinancePlusAttachment');
		$financesAtt = $this->ProjectFinancePlusAttachment->find("first", array(
			"conditions" => array(
				'id' => $att_id,
			),
			'fields' => array('*')
		));
		if( $key && !empty($financesAtt) ){
			$info = $this->ApiKey->retrieve($key);
			if( empty($info) ){
				die('Permission denied');
			}
			$project_id = $financesAtt['ProjectFinancePlusAttachment']['project_id'];
			$project = $this->Project->find('count', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $info['ApiKey']['company_id'],
					'id' => $financesAtt['ProjectFinancePlusAttachment']['project_id'],
				)
			));
			if( !$project ){
				die('Permission denied');
			}
		} else {
			die('Permission denied');
		}
		if ( !empty($financesAtt['ProjectFinancePlusAttachment']['is_file']) ) {
			$link = trim($this->_getPath($project_id) . $financesAtt['ProjectFinancePlusAttachment']['attachment']);
			// debug( $this->_getPath($project_id));
			// debug( $link); exit;
			if (empty($link)) {
				$link = '';
				
			} else {
				if (!file_exists($link) || !is_file($link)) {
					$link = '';
				}
				$info = pathinfo($link);
				$this->view = 'Media';
				$params = array(
					'id' => !empty($info['basename']) ? $info['basename'] : '',
					'path' => !empty($info['dirname']) ? $info['dirname'] . '/' : '',
					'name' => !empty($info['filename']) ? $info['filename'] : '',
					'mimeType' => array(
						'bmp' => 'image/bmp',
						'ppt' => 'application/vnd.ms-powerpoint',
						'pps' => 'application/vnd.ms-powerpoint',
						'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
						'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
						'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation'
					),
					'extension' => !empty($info['extension']) ? strtolower($info['extension']) : '',
				);
				if (!empty($this->params['url']['download'])) {
					$params['download'] = true;
				}
				$this->set($params);
			}
		}
		if (!$link && !empty($this->params['url']['download'])) {
			$this->Session->setFlash(__('File not found.', true), 'error');
			$this->redirect(array('action' => 'index_plus', $project_id));
		}
		if(isset( $financesAtt['ProjectFinancePlusAttachment']['is_file']) && ( $financesAtt['ProjectFinancePlusAttachment']['is_file'] == 0)){
		   $this->set('url',$financesAtt['ProjectFinancePlusAttachment']['attachment']);
		   $this->set('is_https',$financesAtt['ProjectFinancePlusAttachment']['is_https']);
		}
	}
    public function getAttachments($id = null, $return = null){
		$data = array();
		$message='';
		$finances = $this->ProjectFinancePlus->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $id 
			),
			'fields' => array('*'),
		));
		if( !empty( $id)){
			$data['id'] = $id;		
			$data['title'] =  $finances['ProjectFinancePlus']['name'];
			$this->loadModel('ProjectFinancePlusAttachment');
			$result = $this->ProjectFinancePlusAttachment->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'project_finance_plus_id' => $id
				),
				'fields' => array(
					'ProjectFinancePlusAttachment.*', 
					'Employee.first_name',
					'Employee.last_name'
				),
				'joins' => array(
					array(
						'table' => 'employees',
						'alias' => 'Employee',
						'type' => 'inner',
						'conditions' => array(
							'Employee.id = ProjectFinancePlusAttachment.employee_id'
						)
					),
				),
				'order' => array('updated' => "DESC" ),
			));
			foreach( $result as $key => $attachment){
				$attachment[$key]['ProjectFinancePlusAttachment']['updated'] = date('Y-m-d H:i:s', $attachment['ProjectFinancePlusAttachment']['updated'] );
			}
			$data['attachments'] = $result;
			if( !empty($result) ) $this->update_attachment_read_status($id);
		}else{
			$message = __('You have not permission to access this function', true);
		}
		if( $return){
			return $data;
		}else{
			die(json_encode(array(
				'results' => $data ? 'success' : 'failed',
				'data' => $data ,
				'message' => $message
			)));
		}
	}
	public function delete_attachment($att_id=null){
		// $this->_checkRole(false, $project_id);
		$this->loadModels('ProjectFinancePlusAttachment');
        // $this->loadModels('projectTasks', 'ProjectTaskAttachment');
        $p = $this->ProjectFinancePlusAttachment->find('first', array(
			'conditions' => array(
				'id' => $att_id
			),
		));
        // debug($p); exit;
        if( !empty($p['ProjectFinancePlusAttachment']['attachment']) ){
            $data = $p['ProjectFinancePlusAttachment']['attachment'];
			$project_id = $p['ProjectFinancePlusAttachment']['project_id'];
            $path = $this->_getPath($project_id);
            if( $data){
                $fileInfo = pathinfo($data);
                @unlink($path . $fileInfo['basename']);
                if($this->MultiFileUpload->otherServer){
                    $this->MultiFileUpload->deleteFileToServerOther($path, $fileInfo['basename']);
                }
            }
        }
        $this->ProjectFinancePlusAttachment->delete($att_id);
        die( json_encode($att_id));
    }
	private function attachment_unread($f_id = null){
		$this->loadModel('ProjectFinancePlusAttachmentView');
		$list_stt = $this->ProjectFinancePlusAttachmentView->find('all', array(
			'conditions' => array(
			'project_finance_plus_id' => $f_id,
			'employee_id !=' => $this->employee_info['Employee']['id'] 
			),
		));
		foreach ($list_stt as $val) {
			$_this_data = $val;
			$_this_data['ProjectFinancePlusAttachmentView']['read_status'] = 0;
			$this->ProjectFinancePlusAttachmentView->id = $val['ProjectFinancePlusAttachmentView']['id'];
			$result = $this->ProjectFinancePlusAttachmentView->save($_this_data);
			// debug($result);
		}
		return;
	}
	private function update_attachment_read_status($id = null){
		$finances = $this->ProjectFinancePlus->find('first', array(
            'recursive' => -1,
            'conditions' => array('id' => $id),
            'fields' => array('id', 'project_id'),
        ));
		// $project_id = $finances['ProjectFinancePlus']['project_id'];
		// $this->_checkRole(false, $project_id);
		$result = false;
		$data = '';
		if( $finances ){
			$this->loadModel('ProjectFinancePlusAttachmentView');
			$data = $this->ProjectFinancePlusAttachmentView->find('first', array(
				'conditions' => array(
				'project_finance_plus_id' => $id,
				'employee_id' => $this->employee_info['Employee']['id'],
				),
			));
			if( empty( $data)){
				$data = $this->ProjectFinancePlusAttachmentView->create();
				$data['ProjectFinancePlusAttachmentView']['project_finance_plus_id'] = $id;
				$data['ProjectFinancePlusAttachmentView']['employee_id'] = $this->employee_info['Employee']['id'];
			}
			$data['ProjectFinancePlusAttachmentView']['read_status'] = 1;
			$data = $this->ProjectFinancePlusAttachmentView->save($data);
			if( $data) $result = true;
		}
		return $result;	
	}
    public function update_finance($type){
        $result = false;
        $this->layout = false;
        /**
         * Save Data
         */
        if( !empty($this->data) ){
            $datas = $this->data;
			$this->_checkRole(false, @$datas['project_id']);
            $this->ProjectFinancePlus->create();
            if( !empty($datas['id']) ){
                $this->ProjectFinancePlus->id = $datas['id'];
            }
            $saveFins = array(
                'project_id' => $datas['project_id'],
                'activity_id' => $datas['activity_id'],
                'company_id' => $datas['company_id'],
                'project_id' => $datas['project_id'],
                'name' => $this->data[$type.'_name'],
                'type' => $type,
                'finance_date' => (!empty( $this->data[$type.'_date']) ? date('Y-m-d', strtotime($this->data[$type.'_date'])) : NULL ),
            );
			// debug($saveFins);
			// exit;
            unset($datas['id']);
            unset($datas['project_id']);
            unset($datas['activity_id']);
            unset($datas['company_id']);
            unset($datas[$type.'_name']);
            unset($datas[$type.'_date']);
            /**
             * Save name and type finance
             */
            if( $this->ProjectFinancePlus->save($saveFins) ){
                $lastId = $this->ProjectFinancePlus->id;
                if( !empty($datas) ){
                    foreach($datas as $key => $data){
                        $key = explode('_', $key);
                        $saved = array(
                            'project_id' => $this->data['project_id'],
                            'activity_id' => $this->data['activity_id'],
                            'company_id' => $this->data['company_id'],
                            'project_finance_plus_id' => $lastId,
                            'model' => $key[1],
                            'year' => $key[2],
                            'type' => $type
                        );
                        $last = $this->ProjectFinancePlusDetail->find('first', array(
                            'recursive' => -1,
                            'conditions' => $saved,
                            'fields' => array('id')
                        ));
                        $this->ProjectFinancePlusDetail->create();
                        if( !empty($last) && !empty($last['ProjectFinancePlusDetail']['id']) ){
                            $this->ProjectFinancePlusDetail->id = $last['ProjectFinancePlusDetail']['id'];
                        }
                        $saved['value'] = $data;
                        $this->ProjectFinancePlusDetail->save($saved);
                    }
                }
                $result = true;
                $this->data['id'] = $lastId;
            } else {
                $this->Session->setFlash(__('Not Saved.', true), 'error');
            }
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }
    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete_finance($id = null, $project_id = null) {
       
        $view = !empty($this->params['url']['view']) ? $this->params['url']['view'] : '';
        
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Finance', true), 'error');
            $this->redirect(array('action' => 'index_plus', $project_id, '?' => array('view' =>$view)));
        }
        if ($this->_checkRole(true, $project_id)) {
            if ($this->ProjectFinancePlus->delete($id)) {
                $this->ProjectFinancePlusDetail->deleteAll(array('ProjectFinancePlusDetail.project_finance_plus_id' => $id), false);
                $this->redirect(array('action' => 'index_plus', $project_id, '?' => array('view' =>$view)));
            }
            $this->Session->setFlash(__('Not deleted', true), 'error');
        }
        $this->redirect(array('action' => 'index_plus', $project_id, '?' => array('view' =>$view)));
    }
    function plus($project_id){
        $this->loadModels('ProjectFinanceTwoPlus', 'ProjectFinanceTwoPlusDetail', 'ProjectFinanceTwoPlusDate');
        /**
         * Lay start date va end date
         */
        if (!$this->_checkRole(false, $project_id)) {
            $this->data = null;
        }
        $this->_checkWriteProfile('finance_two_plus');
		$bg_currency = $this->getCurrencyOfBudget();
        $employee_info = $this->employee_info;
        $start = $end = '';
        $getSaveHistory = $this->ProjectFinanceTwoPlusDate->find('first', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id)
        ));
        if(!empty($getSaveHistory)) {
            $start = !empty($getSaveHistory['ProjectFinanceTwoPlusDate']['start']) ? $getSaveHistory['ProjectFinanceTwoPlusDate']['start'] : '';
            $end = !empty($getSaveHistory['ProjectFinanceTwoPlusDate']['end']) ? $getSaveHistory['ProjectFinanceTwoPlusDate']['end'] : '';
        }
        $start = !empty($this->params['url']['inv_start']) ? strtotime(@$this->params['url']['inv_start']) : (!empty($start) ? $start : time());
        $end = !empty($this->params['url']['inv_end']) ? strtotime(@$this->params['url']['inv_end']) : (!empty($end) ? $end : time());
        /**
         * Lay project
         */
        $projects = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('Project.id' => $project_id),
            'fields' => array('id', 'project_name', 'company_id')
        ));
        $company_id = !empty($projects['Project']['company_id']) ? $projects['Project']['company_id'] : 0;
        $projectName = !empty($projects['Project']['project_name']) ? $projects['Project']['project_name'] : '';
        /**
         * Lay cac finance+
         */
        $finances = $this->ProjectFinanceTwoPlus->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('id', 'name'),
        ));
        /**
         * Finance detail
         */
        $_financeDetails = $this->ProjectFinanceTwoPlusDetail->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
        ));
        $financeDetails = array();
        $yearOfFinances = array();
        $total = array();
        if( !empty($_financeDetails) ){
            foreach($_financeDetails as $financeDetail){
                $dx = $financeDetail['ProjectFinanceTwoPlusDetail'];
                $_dx = $dx;
                unset($_dx['id']);
                unset($_dx['project_id']);
                unset($_dx['project_finance_two_plus_id']);
                unset($_dx['year']);
                unset($_dx['created']);
                unset($_dx['updated']);
                $financeDetails[$dx['project_finance_two_plus_id']][$dx['year']] = $_dx;
                $yearOfFinances[$dx['year']] = $dx['year'];
                if(!isset($total['budget_revised'])){
                    $total['budget_revised'] = 0;
                }
                $total['budget_revised'] += !empty($dx['budget_revised']) ? $dx['budget_revised'] : 0;
                if(!isset($total['last_estimated'])){
                    $total['last_estimated'] = 0;
                }
                $total['last_estimated'] += !empty($dx['last_estimated']) ? $dx['last_estimated'] : 0;
                if(!isset($total['engaged'])){
                    $total['engaged'] = 0;
                }
                $total['engaged'] += !empty($dx['engaged']) ? $dx['engaged'] : 0;
                if(!isset($total['bill'])){
                    $total['bill'] = 0;
                }
                $total['bill'] += $dx['bill'];
                if(!isset($total['disbursed'])){
                    $total['disbursed'] = 0;
                }
                $total['disbursed'] += !empty($dx['disbursed']) ? $dx['disbursed'] : 0;
            }
        }
        if( !empty($yearOfFinances) ){
            $StartData = strtotime(date('d-m') . '-' . min($yearOfFinances));
            $EndData = strtotime(date('d-m') . '-' . max($yearOfFinances));
            if(empty($this->params['url']['start']) && $StartData < $start){
                $invStart = $StartData;
            }
            if(empty($this->params['url']['end']) && $EndData > $end){
                $end = $EndData;
            }
        }
        $saveHistory = array(
            'start' => $start,
            'end' => $end,
            'project_id' => $project_id
        );
        if(!empty($this->params['url']['inv_start']) && !empty($this->params['url']['inv_end'])){
            if( !empty($getSaveHistory) && !empty($getSaveHistory['ProjectFinanceTwoPlusDate']['id']) ){
                $this->ProjectFinanceTwoPlusDate->id = $getSaveHistory['ProjectFinanceTwoPlusDate']['id'];
            } else {
                $this->ProjectFinanceTwoPlusDate->create();
            }
            $this->ProjectFinanceTwoPlusDate->save($saveHistory);
        }
        $this->set(compact('project_id', 'financeDetails', 'start', 'end', 'projects', 'projectName', 'finances', 'total', 'bg_currency'));
    }
    public function update_finance_two_plus(){
        $result = false;
        $this->layout = false;
        $this->loadModels('ProjectFinanceTwoPlus', 'ProjectFinanceTwoPlusDetail');
        if( !empty($this->data) ){
            $datas = $this->data;
            $this->ProjectFinanceTwoPlus->create();
            if( !empty($datas['id']) ){
                $this->ProjectFinanceTwoPlus->id = $datas['id'];
            }
            $saveFins = array(
                'company_id' => $datas['company_id'],
                'project_id' => $datas['project_id'],
                'name' => $this->data['name'],
            );
            unset($datas['id']);
            unset($datas['project_id']);
            unset($datas['company_id']);
            unset($datas['name']);
            /**
             * Save name and type finance
             */
            if( $this->ProjectFinanceTwoPlus->save($saveFins) ){
                $lastId = $this->ProjectFinanceTwoPlus->id;
                if( !empty($datas) ){
                    foreach($datas as $key => $data){
                        $key = explode('-', $key);
                        $saved = array(
                            'project_id' => $this->data['project_id'],
                            'project_finance_two_plus_id' => $lastId,
                            'year' => $key[1],
                        );
                        $last = $this->ProjectFinanceTwoPlusDetail->find('first', array(
                            'recursive' => -1,
                            'conditions' => $saved,
                            'fields' => array('id')
                        ));
                        $_saved = array(
                            $key[0] => $data
                        );
                        $saved = array_merge($saved, $_saved);
                        $this->ProjectFinanceTwoPlusDetail->create();
                        if( !empty($last) && !empty($last['ProjectFinanceTwoPlusDetail']['id']) ){
                            $this->ProjectFinanceTwoPlusDetail->id = $last['ProjectFinanceTwoPlusDetail']['id'];
                        }
                        $saved['value'] = $data;
                        $this->ProjectFinanceTwoPlusDetail->save($saved);
                    }
                }
                $result = true;
                $this->data['id'] = $lastId;
            } else {
                $this->Session->setFlash(__('Not Saved.', true), 'error');
            }
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }
    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete_finance_two_plus($id = null, $project_id = null) {
        $fonStart = !empty($this->params['url']['fon_start']) ? $this->params['url']['fon_start'] : date('d-m-Y', time());
        $fonEnd = !empty($this->params['url']['fon_end']) ? $this->params['url']['fon_end'] : date('d-m-Y', time());
        $invStart = !empty($this->params['url']['inv_start']) ? $this->params['url']['inv_start'] : date('d-m-Y', time());
        $invEnd = !empty($this->params['url']['inv_end']) ? $this->params['url']['inv_end'] : date('d-m-Y', time());
        $this->loadModels('ProjectFinanceTwoPlus', 'ProjectFinanceTwoPlusDetail');
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Finance', true), 'error');
            $this->redirect(array('action' => 'plus', $project_id, '?' => array('fon_start' => $fonStart, 'fon_end' => $fonEnd, 'inv_start' => $invStart, 'inv_end' => $invEnd)));
        }
        if ($this->_checkRole(true, $project_id)) {
            if ($this->ProjectFinanceTwoPlus->delete($id)) {
                $this->ProjectFinanceTwoPlusDetail->deleteAll(array('ProjectFinanceTwoPlusDetail.project_finance_two_plus_id' => $id), false);
                // $this->Session->setFlash(__('Deleted.', true), 'success');
                $this->redirect(array('action' => 'plus', $project_id, '?' => array('fon_start' => $fonStart, 'fon_end' => $fonEnd, 'inv_start' => $invStart, 'inv_end' => $invEnd)));
            }
            $this->Session->setFlash(__('Not deleted', true), 'error');
        }
        $this->redirect(array('action' => 'plus', $project_id, '?' => array('fon_start' => $fonStart, 'fon_end' => $fonEnd, 'inv_start' => $invStart, 'inv_end' => $invEnd)));
    }
    function ajax($project_id = null){
		$bg_currency = $this->getCurrencyOfBudget();
        $cat = !empty($this->params['url']['cat']) ? $this->params['url']['cat'] : '';
        /**
         * Lay start date va end date
         */
        if (!$this->_checkRole(false, $project_id)) {
            $this->data = null;
        }
        $this->_checkWriteProfile('finance_plus');
        $employee_info = $this->employee_info;
        $invStart = $invEnd = $fonStart = $fonEnd = $finanInvStart = $finanInvEnd = $finanFonStart = $finanFonEnd = '';
        $getSaveHistory = $this->ProjectFinancePlusDate->find('first', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id)
        ));
        if(!empty($getSaveHistory)) {
            $invStart = !empty($getSaveHistory['ProjectFinancePlusDate']['inv_start']) ? $getSaveHistory['ProjectFinancePlusDate']['inv_start'] : '';
            $invEnd = !empty($getSaveHistory['ProjectFinancePlusDate']['inv_end']) ? $getSaveHistory['ProjectFinancePlusDate']['inv_end'] : '';
            $fonStart = !empty($getSaveHistory['ProjectFinancePlusDate']['fon_start']) ? $getSaveHistory['ProjectFinancePlusDate']['fon_start'] : '';
            $fonEnd = !empty($getSaveHistory['ProjectFinancePlusDate']['fon_end']) ? $getSaveHistory['ProjectFinancePlusDate']['fon_end'] : '';
            $finanInvStart = !empty($getSaveHistory['ProjectFinancePlusDate']['finaninv_start']) ? $getSaveHistory['ProjectFinancePlusDate']['finaninv_start'] : '';
            $finanInvEnd = !empty($getSaveHistory['ProjectFinancePlusDate']['finaninv_end']) ? $getSaveHistory['ProjectFinancePlusDate']['finaninv_end'] : '';
            $finanFonStart = !empty($getSaveHistory['ProjectFinancePlusDate']['finanfon_start']) ? $getSaveHistory['ProjectFinancePlusDate']['finanfon_start'] : '';
            $finanFonEnd = !empty($getSaveHistory['ProjectFinancePlusDate']['finanfon_end']) ? $getSaveHistory['ProjectFinancePlusDate']['finanfon_end'] : '';
        }
        $invStart = !empty($this->params['url']['inv_start']) ? strtotime(@$this->params['url']['inv_start']) : (!empty($invStart) ? $invStart : time());
        $invEnd = !empty($this->params['url']['inv_end']) ? strtotime(@$this->params['url']['inv_end']) : (!empty($invEnd) ? $invEnd : time());
        $fonStart = !empty($this->params['url']['fon_start']) ? strtotime(@$this->params['url']['fon_start']) : (!empty($fonStart) ? $fonStart : time());
        $fonEnd = !empty($this->params['url']['fon_end']) ? strtotime(@$this->params['url']['fon_end']) : (!empty($fonEnd) ? $fonEnd : time());
		
        $finanInvStart = !empty($this->params['url']['finaninv_start']) ? strtotime(@$this->params['url']['finaninv_start']) : (!empty($finanInvStart) ? $finanInvStart : time());
        $finanInvEnd = !empty($this->params['url']['finaninv_end']) ? strtotime(@$this->params['url']['finaninv_end']) : (!empty($finanInvEnd) ? $finanInvEnd : time());
        $finanFonStart = !empty($this->params['url']['finanfon_start']) ? strtotime(@$this->params['url']['finanfon_start']) : (!empty($finanFonStart) ? $finanFonStart : time());
        $finanFonEnd = !empty($this->params['url']['finanfon_end']) ? strtotime(@$this->params['url']['finanfon_end']) : (!empty($finanFonEnd) ? $finanFonEnd : time());
        /**
         * Lay project
         */
        $projects = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('Project.id' => $project_id),
            'fields' => array('id', 'activity_id', 'project_name', 'company_id')
        ));
        $company_id = !empty($projects['Project']['company_id']) ? $projects['Project']['company_id'] : 0;
        $activity_id = !empty($projects['Project']['activity_id']) ? $projects['Project']['activity_id'] : 0;
        $projectName = !empty($projects['Project']['project_name']) ? $projects['Project']['project_name'] : '';
        /**
         * Lay cac finance+
         */
        $finances = $this->ProjectFinancePlus->find('all', array(
            'recursive' => -1,
            'conditions' => array(
				'project_id' => $project_id,
				'activity_id' => $activity_id,
			),
            'fields' => array('id', 'name', 'type', 'finance_date'),
        ));
		$new = array();
		if( !empty($finances)){
			foreach( $finances as $key => $finance){
				$finance = $finance['ProjectFinancePlus'];
				$finance['finance_date'] = (!empty( $finance['finance_date']) && $finance['finance_date'] != '0000-00-00') ? date("d-m-Y", strtotime($finance['finance_date'])) : '';
				$new[$finance['type']][$finance['id']] = $finance;
			}
		}
		$finances = $new;
		unset( $new);
        /**
         * Finance detail
         */
        $_financeDetails = $this->ProjectFinancePlusDetail->find('all', array(
            'recursive' => -1,
            'conditions' => array(
				'project_id' => $project_id,
				'activity_id' => $activity_id,
			),
            'fields' => array('project_finance_plus_id', 'model', 'year', 'value', 'type')
        ));
        $financeDetails = array();
        $yearOfFinances = array();
        $totals = array();
        if( !empty($_financeDetails) ){
            foreach($_financeDetails as $financeDetail){
                $dx = $financeDetail['ProjectFinancePlusDetail'];
                $financeDetails[$dx['project_finance_plus_id']][$dx['model'] . '_' . $dx['year']] = $dx;
                $yearOfFinances[$dx['type']][$dx['year']] = $dx['year'];
                if(empty($totals[$dx['type']][$dx['model']])){
                    $totals[$dx['type']][$dx['model']] = 0;
                }
                $totals[$dx['type']][$dx['model']] += $dx['value'];
            }
        }
        if( !empty($yearOfFinances['inv']) ){
            $invStartData = strtotime(date('d-m') . '-' . min($yearOfFinances['inv']));
            $invEndData = strtotime(date('d-m') . '-' . max($yearOfFinances['inv']));
            if(empty($this->params['url']['inv_start']) && $invStartData < $invStart){
                $invStart = $invStartData;
            }
            if(empty($this->params['url']['inv_end']) && $invEndData > $invEnd){
                $invEnd = $invEndData;
            }
        }
        if( !empty($yearOfFinances['fon']) ){
            $fonStartData = strtotime(date('d-m') . '-' . min($yearOfFinances['fon']));
            $fonEndData = strtotime(date('d-m') . '-' . max($yearOfFinances['fon']));
            if(empty($this->params['url']['fon_start']) && $fonStartData < $fonStart){
                $fonStart = $fonStartData;
            }
            if(empty($this->params['url']['fon_end']) && $fonEndData > $fonEnd){
                $fonEnd = $fonEndData;
            }
        }
        if( !empty($yearOfFinances['finaninv']) ){
            $finanInvStartData = strtotime(date('d-m') . '-' . min($yearOfFinances['finaninv']));
            $finanInvEndData = strtotime(date('d-m') . '-' . max($yearOfFinances['finaninv']));
            if(empty($this->params['url']['finaninv_start']) && $finanInvStartData < $finanInvStart){
                $finanInvStart = $finanInvStartData;
            }
            if(empty($this->params['url']['finaninv_end']) && $finanInvEndData > $finanInvEnd){
                $finanInvEnd = $finanInvEndData;
            }
        }
        if( !empty($yearOfFinances['finanfon']) ){
            $finanFonStartData = strtotime(date('d-m') . '-' . min($yearOfFinances['finanfon']));
            $finanFonEndData = strtotime(date('d-m') . '-' . max($yearOfFinances['finanfon']));
            if(empty($this->params['url']['finanfon_start']) && $finanFonStartData < $finanFonStart){
                $finanFonStart = $finanFonStartData;
            }
            if(empty($this->params['url']['finanfon_end']) && $finanFonEndData > $finanFonEnd){
                $finanFonEnd = $finanFonEndData;
            }
        }
        $saveHistory = array(
            'inv_start' => $invStart,
            'inv_end' => $invEnd,
            'fon_start' => $fonStart,
            'fon_end' => $fonEnd,
            'finaninv_start' => $finanInvStart,
            'finaninv_end' => $finanInvEnd,
            'finanfon_start' => $finanFonStart,
            'finanfon_end' => $finanFonEnd,
            'company_id' => $company_id,
            'project_id' => $project_id
        );
        if( !empty($getSaveHistory) && !empty($getSaveHistory['ProjectFinancePlusDate']['id']) ){
            $this->ProjectFinancePlusDate->id = $getSaveHistory['ProjectFinancePlusDate']['id'];
        } else {
            $this->ProjectFinancePlusDate->create();
        }
        $this->ProjectFinancePlusDate->save($saveHistory);
        $this->loadModels('HistoryFilter');
        $history = $this->HistoryFilter->find('first', array(
            'conditions' => array(
                'path' => $this->params['url']['url'],
                'employee_id' => $employee_info['Employee']['id']
            ),
            'fields' => array('params')
        ));
        if( !empty($history) ){
            $history = json_decode($history['HistoryFilter']['params'], true);
        } else {
            $history = array();
        }
        $this->set(compact('invStart', 'invEnd', 'fonStart', 'fonEnd', 'finanInvStart', 'finanInvEnd', 'finanFonStart', 'finanFonEnd', 'project_id', 'projects', 'finances', 'financeDetails', 'projectName', 'history', 'totals', 'bg_currency'));
    
    }
	// export Excel Index Screen
    public function finance_export() {
        if (!empty($this->data)) {
            $this->SlickExporter->init();
            $data = json_decode($this->data['data'], true);
			// if( end($data['header']) == __('Action', true)) {
				// array_pop($data['header']);
				// foreach ($data['body'] as $key => $val){
					// array_pop($data['body'][$key]);
				// }
			// }
            $this->SlickExporter
                    ->setT('Project Finance')  //auto translate
                    ->save($data, 'project_finances_plus_{date}.xls');
        }
        die;
    }
}
?>
