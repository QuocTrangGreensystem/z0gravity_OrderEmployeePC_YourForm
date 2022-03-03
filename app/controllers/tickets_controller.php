<?php

class TicketsController extends AppController
{

	public $helpers = array('Validation', 'Time');
	public $uses = array('Ticket', 'TicketStatus', 'TicketProfileStatusReference', 'TicketNote', 'TicketMeta', 'TicketComment', 'TicketAttachment', 'TicketProfile', 'TicketSubscription', 'External', 'Company', 'HistoryFilter');
	public $components = array('MultiFileUpload', 'SlickExporter', 'ZEmail');
	private $allowedFiles = '*';

	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->set('allowedFiles', $this->allowedFiles);
		App::import('vendor', 'str_utility');
	}

	function index($viewId = null)
	{
		if ($viewId == -1) {
			return $this->redirect('/tickets/');
		}
		$this->loadModels('TicketMeta', 'TicketStatus', 'Employee', 'UserView', 'UserDefaultView');
		$employee = $this->Session->read("Auth.employee_info");
		$company_id = !empty($employee["Company"]["id"]) ? $employee["Company"]["id"] : 0;
		$checkEmployeeExternal = !empty($employee["Employee"]["external"]) ? $employee["Employee"]["external"] : 0;
		$externalCompany = !empty($employee["Employee"]["external_id"]) ? $employee["Employee"]["external_id"] : 0;
		/**
		 * Default fields
		 */
		$selectTicket = 0;
		if ($viewId != null && $viewId != -1) {
			$fieldsets = $this->UserView->find('first', array(
				'fields' => array('name', 'content'),
				'conditions' => array('UserView.id' => $viewId)
			));
			if (!empty($fieldsets)) {
				$fieldsets = unserialize($fieldsets['UserView']['content']);
			}
			$selectTicket = $viewId;
		} else {
			$defaultView = $this->UserDefaultView->find('first', array(
				'conditions' => array(
					'employee_id' => $employee['Employee']['id'],
					'model' => 'ticket'
				),
				'recursive' => -1,
				'fields' => array('user_view_id')
			));
			if ($defaultView && $defaultView['UserDefaultView']['user_view_id'] != 0) {
				$viewId = $defaultView['UserDefaultView']['user_view_id'];
				$fieldsets = $this->UserView->find('first', array(
					'fields' => array('name', 'content'),
					'conditions' => array('UserView.id' => $viewId)
				));

				if (!empty($fieldsets)) {
					$fieldsets = unserialize($fieldsets['UserView']['content']);
				}
				$selectTicket = $viewId;
			} else {
				$fieldsets = array(
					'name',
					'ticket_status_id',
					'type_id',
					'company_id',
					'delivery_date',
					'priority_id',
					'created',
					'employee_id'
				);
			}
		}
		$fieldsets = $this->Ticket->parseViewField($fieldsets);

		/**
		 * Danh sach cac
		 */
		$userViews = $this->UserView->find('list', array(
			'recursive' => 0,
			'fields' => array('UserView.id', 'UserView.name'),
			'order' => 'UserView.public ASC',
			'group' => 'UserView.id',
			'conditions' => array(
				'UserView.model' => 'ticket',
				'OR' => array(
					'UserView.employee_id' => $this->employee_info['Employee']['id'],
					array(
						'UserView.company_id' => $this->employee_info["Company"]["id"],
						'UserView.public' => true
					)
				)
			)
		));
		$external_companies = $this->External->find('list', array(
			'conditions' => array(
				'company_id' => $company_id
			),
			'fields' => array('id', 'name')
		));
		/**
		 * Lay status
		 */
		$statuses = $this->TicketStatus->find('all', array(
			'recursive' => -1,
			'conditions' => array('company_id' => $company_id),
			'fields' => array('id', 'name', 'acffected_cus', 'acffected_dep')
		));
		$ticketStatuses = $affections = array();
		foreach ($statuses as $s) {
			$ss = $s['TicketStatus'];
			$ticketStatuses[$ss['id']] = $ss['name'];
			$v = array();
			if ($ss['acffected_cus']) $v[] = __('Customer', true);
			if ($ss['acffected_dep']) $v[] = __('Developer', true);
			$affections[$ss['id']] = implode(' / ', $v);
		}
		/**
		 * Lay ticket
		 */
		$conditions = array('company_id' => $company_id, 'company_model' => 'Company');
		if (!empty($checkEmployeeExternal) && $checkEmployeeExternal == 1) {
			$conditions = array('company_id' => $externalCompany, 'company_model' => 'External');
		}
		// permission
		$profile_id = $this->employee_info['Employee']['ticket_profile_id'];
		$pr = $this->TicketProfile->find('first', array(
			'conditions' => array(
				'id' => $profile_id
			)
		));
		$tickets = array();
		$can_create = false;
		$profile = array();
		$visible_statuses = array();
		$metaCondition = array('company_id' => $company_id); // Dieu kien de lay TicketMeta
		if (!empty($pr)) {
			$profile = $pr['TicketProfile'];
			if ($profile['role'] == 'customer') { // Bo sung Dieu kien de lay TicketMeta cho customer.
				$metaCondition['OR'] = array(
					'enable_for_customer' => 1,
					'NOT' => array('meta_name' => 'type')
				);
			}
			if ($profile['role'] == 'developer') {
				$conditions['company_id'] = array($company_id);
				$conditions['company_id'] = array_merge($conditions['company_id'], array_keys($external_companies));
				$conditions['company_model'] = array('Company', 'External');
			}
			// view only my tickets
			else if ($profile['can_view'] == 1) {
				$conditions['employee_id'] = $this->employee_info['Employee']['id'];
			} else if ($profile['can_view'] == 0) {
				$conditions[] = '2=1';
			}
			$tickets = $this->Ticket->find('all', array(
				'recursive' => -1,
				'conditions' => $conditions
			));
			$can_create = $profile['can_create'] || $profile['role'] == 'developer';
		}
		$visible_statuses = $this->TicketStatus->getVisible($company_id, $profile_id);
		$resources = $this->Employee->find('list', array(
			'conditions' => array(
				'company_Id' => $company_id
			),
			'fields' => array('id', 'fullname')
		));
		/**
		 * Lay meta
		 */

		$ticketMetas = $this->TicketMeta->find('list', array(
			'recursive' => -1,
			'conditions' => $metaCondition,
			'fields' => array('id', 'meta_value', 'meta_name'),
			'group' => array('meta_name', 'id')
		));
		/*
		*	Get Limitations of company for Customer
		*/
		$isCustomer = false;
		$haveLimitTicket = false;
		$limitForCustomer = array();
		if (!empty($pr) && $pr['TicketProfile']['role'] == 'customer' && !!$checkEmployeeExternal) {
			$isCustomer = true;
			$companyLimits = $this->External->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					// 'company_id' => $company_id
					'id' => $employee["Employee"]['external_id']
				)
			));

			// Check period
			if (!empty($companyLimits) && !empty($companyLimits['External']['limit_period'])) {

				$haveLimitTicket = true;
				$limitForCustomer = $this->formatCompanyTicketLimits($companyLimits, $employee["Company"]["id"], $employee["Employee"]['external_id']);
				// debug($limitForCustomer);
			}
			// Count tickets in period
		} 
		// else if (!empty($pr) && $pr['TicketProfile']['role'] == 'developer') {
		// 	$companyLimits = $this->External->find('all', array(
		// 		'recursive' => -1,
		// 		'conditions' => array(
		// 			'id' => array_keys($external_companies)
		// 		),
		// 		'fields' => array('id', 'limit_support', 'limit_formation', 'limit_coaching', 'limit_period')
		// 	));

		// 	foreach ($companyLimits as $key => $value) {
		// 		$limitForCustomer[$value['External']['id']] = $this->formatCompanyTicketLimits($value, $company_id, $value['External']['id']);
		// 	}
		// 	// debug($limitForCustomer);
		// }

		$this->set(compact('visible_statuses', 'profile', 'fieldsets', 'tickets', 'ticketMetas', 'ticketStatuses', 'affections', 'userViews', 'selectTicket', 'can_create', 'resources', 'external_companies', 'company_id'));
		$this->set(compact('isCustomer', 'companyLimits', 'haveLimitTicket', 'limitForCustomer'));
	}

	public function view($ticket_id = null, $screen = null)
	{
		//init
		$is_external = $this->employee_info['Employee']['external'];
		$company_id = $this->employee_info['Company']['id'];
		$external_id = $this->employee_info['Employee']['external_id'];
		$cid = $is_external ? $external_id : $company_id;
		$company_model = $is_external ? 'External' : 'Company';
		$profile_id = $this->employee_info['Employee']['ticket_profile_id'];
		$me = $this->employee_info['Employee']['id'];

		// check if resource can access this ticket
		list($can_access, $ticket, $profile) = $this->user('can_view', $ticket_id);
		// get related data
		if ($can_access) {
			// get metas
			//Ticket #2711
			$getMetasCondition = array(
				'company_id' => $company_id
			);
			if ($profile['role'] == 'customer') {
				$getMetasCondition['OR'] = array(
					'enable_for_customer' => 1,
					'NOT' => array('meta_name' => 'type')
				);
			}
			$raw_metas = $this->TicketMeta->find('all', array(
				'conditions' => $getMetasCondition
			));
			$metas = array();
			foreach ($raw_metas as $m) {
				$meta = $m['TicketMeta'];
				$metas[$meta['meta_name']][$meta['id']] = $meta['meta_value'];
			}
			// debug($metas);

			// get statuses

			$statuses = $this->TicketStatus->getAll($company_id);
			$visible_statuses = $this->TicketStatus->getVisible($company_id, $profile_id);

			// get profile references

			// $references = $this->TicketProfileStatusReference->find('list', array(
			// 	'conditions' => array(
			// 		'ticket_status_id' => $ticket['ticket_status_id']
			// 	),
			// 	'fields' => array('ticket_profile_id', 'ticket_profile_id')
			// ));

			$affections = $this->getAffection($ticket['ticket_status_id']);

			// get all profiles

			$profiles = $this->TicketProfile->find('list', array(
				'conditions' => array(
					'company_id' => $company_id
				),
				'fields' => array('id', 'name')
			));

			// get attachments

			$attachments = $this->TicketAttachment->find('all', array(
				'conditions' => array(
					'ticket_id' => $ticket_id
				),
				'order' => array('created' => 'DESC')
			));
			if (!empty($attachments)) {
				$attachments = Set::combine($attachments, '{n}.TicketAttachment.id', '{n}.TicketAttachment', '{n}.TicketAttachment.type');
			}

			// get comments
			$this->recursive = 0;
			$this->paginate = array(

				'conditions' => array(
					'TicketComment.ticket_id' => $ticket_id
				),
				'limit' => 6,
				'order' => array('TicketComment.created' => 'DESC')
			);
			$comments = $this->paginate('TicketComment');

			$resources = $this->Employee->find('list', array(
				'conditions' => array(
					'company_Id' => $company_id
				),
				'fields' => array('id', 'fullname')
			));

			// get notes
			if ($screen == 'note') {
				$notes = $this->TicketNote->find('all', array(
					'conditions' => array(
						'ticket_id' => $ticket_id
					),
					'order' => array('created' => 'ASC')
				));
			}

			// get my subscription

			$is_subscribed = $this->TicketSubscription->isSubscribed($me, $ticket_id);

			// get company name

			$external_companies = $this->External->find('list', array(
				'conditions' => array(
					'company_id' => $company_id
				),
				'fields' => array('id', 'name')
			));

			$company_name = $this->employee_info['Company']['company_name'];

			// permissions

			list($can_update,,) = $this->user('can_update', $ticket_id, $ticket, $profile);
			// $can_create = $this->user('can_create', $id, $ticket, $profile);

			$this->set(compact('metas', 'statuses', 'affections', 'attachments', 'comments', 'resources', 'can_update', 'notes', 'is_subscribed', 'external_companies', 'company_name', 'visible_statuses', 'profiles'));
		} else {
			$this->Session->setFlash(__('No permission', true), 'error');
			$this->redirect(array('action' => 'index'));
			die;
		}

		$this->set(compact('me', 'is_external', 'company_id', 'external_id', 'company_model', 'profile_id', 'can_access', 'profile', 'ticket', 'cid', 'ticket_id', 'screen'));
		if ($profile['role'] == 'customer') {
			return $this->render('view');
		}
		if ($screen == 'note') {
			$this->render('note');
		} else if ($screen == 'doc') {
			$this->render('doc');
		} else if ($screen == 'spec' && @$profile['role'] == 'developer') {
			$this->render('spec');
		}
	}

	private function user($permission, $id, $ticket = array(), $profile = array())
	{

		//init
		$is_external = $this->employee_info['Employee']['external'];
		$company_id = $this->employee_info['Company']['id'];
		$external_id = $this->employee_info['Employee']['external_id'];
		$cid = $is_external ? $external_id : $company_id;
		$company_model = $is_external ? 'External' : 'Company';
		$profile_id = $this->employee_info['Employee']['ticket_profile_id'];
		$me = $this->employee_info['Employee']['id'];

		// $ticket = $profile = array();

		$can = false;
		if ($profile_id) {
			// get my profile preference
			if (empty($profile)) {
				$pr = $this->TicketProfile->find('first', array(
					'conditions' => array(
						'id' => $profile_id
					)
				));
				$profile = $pr['TicketProfile'];
			}
			if (empty($ticket)) {
				$t = $this->Ticket->find('first', array(
					'conditions' => array(
						'id' => $id
					)
				));
				if (!empty($t)) {
					$ticket = $t['Ticket'];
				}
			}
			if (!empty($ticket) && !empty($profile)) {
				// check company
				if (($ticket['company_model'] == $company_model && $cid == $ticket['company_id'])) {
					// check permission
					if ($profile[$permission] == 2 || ($profile[$permission] == 1 && $ticket['employee_id'] == $me)) {
						$can = true;
					}
				}
				// dev = full controls
				if ($profile['role'] == 'developer') {
					$can = true;
				}
			}
		}

		return array($can, $ticket, $profile);
	}

	public function add()
	{
		//init
		$is_external = $this->employee_info['Employee']['external'];
		$company_id = $this->employee_info['Company']['id'];
		$external_id = $this->employee_info['Employee']['external_id'];
		$cid = $is_external ? $external_id : $company_id;
		$company_model = $is_external ? 'External' : 'Company';
		$profile_id = $this->employee_info['Employee']['ticket_profile_id'];
		$me = $this->employee_info['Employee']['id'];

		// check if resource can access this ticket
		$p = $this->TicketProfile->read(null, $profile_id);
		$can_create = false;
		if (!empty($p)) {
			$profile = $p['TicketProfile'];
			$can_create = $profile['can_create'];
		}
		if ($can_create) {
			// get metas
			$getMetasCondition = array(
				'company_id' => $company_id
			);
			if ($p['TicketProfile']['role'] == 'customer') {
				$getMetasCondition['OR'] = array(
					'enable_for_customer' => 1,
					'NOT' => array('meta_name' => 'type')
				);
			}
			$raw_metas = $this->TicketMeta->find('all', array(
				'conditions' => $getMetasCondition
			));
			$metas = array();
			foreach ($raw_metas as $m) {
				$meta = $m['TicketMeta'];
				$metas[$meta['meta_name']][$meta['id']] = $meta['meta_value'];
			}
			// get all profiles

			$profiles = $this->TicketProfile->find('list', array(
				'conditions' => array(
					'company_id' => $company_id
				),
				'fields' => array('id', 'name')
			));

			// get statuses

			$statuses = $this->TicketStatus->getAll($company_id);
			$visible_statuses = $this->TicketStatus->getVisible($company_id, $profile_id);
			$company_name = $this->employee_info['Company']['company_name'];
			$external_companies = $this->External->find('list', array(
				'conditions' => array(
					'company_id' => $company_id
				),
				'fields' => array('id', 'name')
			));
			$this->set(compact('metas', 'statuses', 'references', 'attachments', 'comments', 'resources', 'can_update', 'notes', 'is_subscribed', 'external_companies', 'company_name', 'visible_statuses', 'profiles'));
		} else {
		}
		$this->set(compact('me', 'is_external', 'company_id', 'external_id', 'company_model', 'profile_id', 'can_create', 'profile', 'ticket', 'cid', 'ticket_id'));
	}

	private function getAffection($status_id, $return_text = false)
	{

		$s = $this->TicketStatus->read(null, $status_id);
		$result = array();
		if (!empty($s['TicketStatus']['acffected_cus'])) {
			if ($return_text) $result[] = __('Customer', true);
			else $result[] = 'customer';
		}
		if (!empty($s['TicketStatus']['acffected_dep'])) {
			if ($return_text) $result = __('Developer', true);
			else $result[] = 'developer';
		}
		if ($return_text) return implode(' / ', $text);
		return $result;
	}

	public function get_mime_type($file)
	{

		// our list of mime types
		$mime_types = array(
			"pdf" => "application/pdf", "exe" => "application/octet-stream", "zip" => "application/zip", "docx" => "application/msword", "doc" => "application/msword", "xls" => "application/vnd.ms-excel", "ppt" => "application/vnd.ms-powerpoint", "gif" => "image/gif", "png" => "image/png", "jpeg" => "image/jpg", "jpg" => "image/jpg", "mp3" => "audio/mpeg", "wav" => "audio/x-wav", "mpeg" => "video/mpeg", "mpg" => "video/mpeg", "mpe" => "video/mpeg", "mov" => "video/quicktime", "avi" => "video/x-msvideo", "3gp" => "video/3gpp", "css" => "text/css", "jsc" => "application/javascript", "js" => "application/javascript", "php" => "text/html", "htm" => "text/html", "html" => "text/html",
			'mp4' => 'video/mp4'
		);

		$extension = strtolower(end(explode('.', $file)));

		return @$mime_types[$extension];
	}

	public function upload($id = null)
	{
		$result = array();
		ini_set('memory_limit', '512M');
		ini_set('max_execution_time', 0);
		// neu co url
		if (!empty($this->data['Upload']['url']) && empty($_FILES)) {
			$data = array(
				'ticket_id' => $id,
				'file' => $this->data['Upload']['url'],
				'employee_id' => $this->employee_info['Employee']['id'],
				'size' => 0,
				'type' => 'link',
			);
			$this->TicketAttachment->create();
			if ($this->TicketAttachment->save($data)) {
				$this->Ticket->saveUpdated($id, $this->employee_info['Employee']['id'], time());
			}
			$result = $this->TicketAttachment->read();
			$result = $result['TicketAttachment'];
		} else {
			$_FILES['FileField'] = array();
			if (!empty($_FILES['file'])) {
				$_FILES['FileField']['name']['attachment'] = $_FILES['file']['name'];
				$_FILES['FileField']['type']['attachment'] = $_FILES['file']['type'];
				$_FILES['FileField']['tmp_name']['attachment'] = $_FILES['file']['tmp_name'];
				$_FILES['FileField']['error']['attachment'] = $_FILES['file']['error'];
				$_FILES['FileField']['size']['attachment'] = $_FILES['file']['size'];
				unset($_FILES['file']);
			}
			if (!empty($_FILES)) {
				$path = $this->getPath($id);
				App::import('Core', 'Folder');
				new Folder($path, true, 0777);

				$this->MultiFileUpload->encode_filename = false;
				$this->MultiFileUpload->uploadpath = $path;
				$this->MultiFileUpload->_properties['AttachTypeAllowed'] = $this->allowedFiles;
				$this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;

				$attachment = $this->MultiFileUpload->upload();

				if (!empty($attachment)) {

					$attachment = $attachment['attachment']['attachment'];

					$type = explode('/', $this->get_mime_type($attachment));

					$data = array(
						'ticket_id' => $id,
						'file' => $attachment,
						'employee_id' => $this->employee_info['Employee']['id'],
						'size' => filesize($path . $attachment)
					);
					if ($type[0] == 'image') {
						App::import("vendor", "resize");

						//resize image for thumbnail slideshow
						$resize = new ResizeImage($path . $attachment);
						$resize->resizeTo(100, 100, 'exact');
						$resize->saveImage($path . 'thumb_' . $attachment);

						$data['thumbnail'] = 'thumb_' . $attachment;
						$data['type'] = 'image';
					} else {
						$data['type'] = 'document';
					}

					$this->TicketAttachment->create();
					if ($this->TicketAttachment->save($data)) {
						$this->Ticket->saveUpdated($id, $this->employee_info['Employee']['id'], time());
					}
					$result = $this->TicketAttachment->read();
					$result = $result['TicketAttachment'];
				}
			}
		}
		die(json_encode($result));
	}

	protected function getPath($id)
	{
		$path = FILES . $this->employee_info['Company']['id'] . DS . 'tickets' . DS . $id . DS;
		return $path;
	}

	public function update_content()
	{
		if (!empty($this->data)) {
			$data['content'] = $this->data['content'];
			$this->Ticket->id = $this->data['id'];
			if ($this->Ticket->save($data)) {
				$this->Ticket->saveUpdated($this->data['id'], $this->employee_info['Employee']['id'], time());
			}
		}
		die(1);
	}

	public function update_info()
	{
		if (!empty($this->data)) {
			if ($this->data['Ticket']['id']) {
				$this->Ticket->id = $this->data['Ticket']['id'];
			} else {
				$this->Ticket->create();
				$this->data['Ticket']['employee_id'] = $this->employee_info['Employee']['id'];
			}
			// fix date
			if (!empty($this->data['Ticket']['delivery_date'])) {
				$this->data['Ticket']['delivery_date'] = str_utility::convertToSQLDate($this->data['Ticket']['delivery_date']);
			}
			// fix company
			$com = explode('-', $this->data['Ticket']['company_id']);
			$this->data['Ticket']['company_id'] = $com[1];
			$this->data['Ticket']['company_model'] = $com[0];
			//$this->data['Ticket']['employee_id'] = $this->employee_info['Employee']['id'];
			// add employee... khong cho update employee theo y khach hang.
			// $this->data['Ticket']['employee_id'] = $this->employee_info['Employee']['id'];
			// save ticket
			if ($this->Ticket->save($this->data['Ticket'])) {
				$ticket_id = $this->Ticket->id;
				$this->Ticket->saveUpdated($ticket_id, $this->employee_info['Employee']['id'], time());
			}
			// save subscribe
			if ($this->data['Ticket']['subscribe']) {
				$this->TicketSubscription->subscribe($this->employee_info['Employee']['id'], $this->Ticket->id);
			} else {
				$this->TicketSubscription->unsubscribe($this->employee_info['Employee']['id'], $this->Ticket->id);
			}
			// set flash
			$this->Session->setFlash(__('Saved', true), 'success');
			if (!isset($this->data['Ticket']['ticket_status_id'])) {
				$this->data['Ticket']['ticket_status_id'] = $this->data['Ticket']['old_status_id'];
			}
			// gathering information for sending email
			if (!isset($this->data['Ticket']['old_status_id']) || $this->data['Ticket']['old_status_id'] != $this->data['Ticket']['ticket_status_id']) {

				if (isset($this->data['Ticket']['old_status_id'])) {
					$this->addComment($this->Ticket->id, $this->data['Ticket']['old_status_id'], $this->data['Ticket']['ticket_status_id']);
				}

				$this->sendSMS($this->Ticket->id, $this->data['Ticket']['ticket_status_id']);
				$this->sendMailTicket($this->Ticket->id, $this->data['Ticket']['ticket_status_id']);
			}
			$this->redirect(str_replace('{id}', $this->Ticket->id, $this->data['Ticket']['redirect']));
		}
		$this->redirect('/tickets/');
		die;
	}

	private function sendSMS($ticket, $status_id)
	{
		$result = array();

		$this->loadModels('TicketPhoneNumber');
		$roles = $this->getAffection($status_id);
		$cond = array(
			'company_id' => $this->employee_info['Company']['id'],
		);
		foreach ($roles as $role) {
			switch ($role) {
				case 'customer':
					$cond['OR']['acffected_cus'] = 1;
					break;
				case 'developer':
					$cond['OR']['acffected_dep'] = 1;
					break;
			}
		}
		$ticket_info = $this->Ticket->find('first', array(
			'recursive' => 1,
			'conditions' => array(
				'Ticket.id' => $ticket
			),
			'fields' => array(
				'Ticket.id',
				'Ticket.name',
				'Ticket.type_id',
				'Ticket.priority_id',
				'Ticket.employee_id',
				'Ticket.ticket_status_id',
				'Employee.first_name',
				'Employee.last_name',
				'TicketStatus.name',
				'TicketStatus.send_sms',
				'TicketType.meta_value',
				'TicketPriority.meta_value',
			),
			'joins' => array(
				array(
					'table' => 'ticket_statuses',
					'alias' => 'TicketStatus',
					'conditions' => array(
						'TicketStatus.id = Ticket.ticket_status_id',
					)
				),
				array(
					'table' => 'employees',
					'alias' => 'Employee',
					'conditions' => array(
						'Employee.id = Ticket.employee_id',
					)
				),
				array(
					'table' => 'ticket_metas',
					'alias' => 'TicketType',
					'conditions' => array(
						'TicketType.id = Ticket.type_id',
					)
				),
				array(
					'table' => 'ticket_metas',
					'alias' => 'TicketPriority',
					'conditions' => array(
						'TicketPriority.id = Ticket.priority_id',
					)
				)
			)
		));
		if (empty($ticket_info['TicketStatus']['send_sms'])) {
			return;
		}
		App::import('Component', 'ZSms');
		if (file_exists(CONFIGS . 'sms_setting.php')) {
			include_once CONFIGS . 'sms_setting.php';
			$settings = get_class_vars('SmsSetting');
			$SMS = new ZSmsComponent();
			$SMS->initialize($this, $settings);
		} else {
			return false;
		}
		// debug( $ticket_info);
		$t_desc = $ticket_info['Ticket']['name'];
		$t_desc = (strlen($t_desc) > 35) ? substr($t_desc, 0, 34) . '...' : $t_desc;
		$t_url = Router::url(array(
			'plugin' => false,
			'controller' => 'tickets',
			'action' => 'view',
			$ticket
		), true);
		$t_name = $ticket_info['Employee']['first_name'] . ' ' .  $ticket_info['Employee']['first_name'];
		$t_priority = $ticket_info['TicketPriority']['meta_value'];
		$t_type = $ticket_info['TicketType']['meta_value'];
		$t_stt = $ticket_info['TicketStatus']['name'];
		$content = array(
			'Client : Z0 Gravity',
			__('Opened by', true) . ': ' . $t_name,
			__('Description', true) . ': ' . $t_desc,
			__('Link', true) . ': ' . $t_url,
			__('Type', true) . ': ' . $t_type,
			__('Priority', true) . ': ' . $t_priority,
			__('Status', true) . ': ' . $t_stt
		);
		$content = implode(PHP_EOL, $content);
		// $content = implode( ' ', $content);
		$inflector = new Inflector();
		$_transliteration = $inflector->_transliteration;
		$content = preg_replace(array_keys($_transliteration), array_values($_transliteration), $content);
		$phones = $this->TicketPhoneNumber->find('list', array(
			'recursive' => -1,
			'conditions' => $cond,
			'fields' => array('id', 'phone_number')
		));
		foreach ($phones as $number) {
			$SMS->choose_service($number);
			$data = $SMS->set_data(array(
				'to' => $number,
				'text' => $content
			));
			$result[] = $SMS->send();
		}
		$logs = array(
			'phones' => $phones,
			'text' => $content,
			'result' => $result,
		);
		$this->writeLog($logs, $this->employee_info, sprintf('Send SMS for ticket `%s`, Status `%s`', $ticket, $t_stt), $this->employee_info['Company']['id']);
		return $result;
	}
	private function sendMailTicket($ticket, $status_id)
	{
		$status = $this->TicketStatus->read(null, $status_id);
		if ($status['TicketStatus']['message']) {
			$roles = $this->getAffection($status_id);
			$profiles = $this->TicketProfile->find('list', array(
				'conditions' => array(
					'role' => $roles,
					'company_id' => $this->employee_info['Company']['id'],
				),
				'fields' => array('id', 'id')
			));
			$employee_ticket = $this->Ticket->find('first', array(
				'conditions' => array(
					'id' => $ticket
				),
				'fields' => array('employee_id')
			));

			$subscriptions = $this->TicketSubscription->find('list', array(
				'conditions' => array(
					'ticket_id' => $ticket
				),
				'fields' => array('employee_id', 'employee_id')
			));

			$creater_ticket = $this->Ticket->find('first', array(
				'conditions' => array(
					'id' => $ticket,
				),
				'fields' => array('employee_id'),
			));

			$sender = array(
				'ticket_profile_id' => $profiles,
				'external' => 0,
			);
			$emails = $this->Employee->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $this->employee_info['Company']['id'],
					'OR' => array(
						$sender,
						'id' => $creater_ticket['Ticket']['employee_id']
					),
					'ticket_profile_id IS NOT NULL'
				),
				'fields' => array('id', 'email')
			));
			// debug($emails); exit;
			if (!empty($emails)) {
				// ticket info
				$ticket_info = $this->Ticket->read(null, $ticket);
				$this->set('email_content', $status['TicketStatus']['message']);
				$this->set('ticket', $ticket_info['Ticket']);
				$this->set('status', $status['TicketStatus']);
				// setting up the title
				// type
				$type = $this->TicketMeta->find('first', array(
					'conditions' => array(
						'id' => $ticket_info['Ticket']['type_id']
					)
				));
				if (!empty($type)) {
					$type = $type['TicketMeta']['meta_value'];
				} else {
					$type = '';
				}
				// affections
				$affections_text = array();
				$to = array();
				foreach ($roles as $t) {
					$affections_text[] = __(Inflector::humanize($t), true);
				}
				if (file_exists(CONFIGS . 'mail_setting.php')) {
					include_once CONFIGS . 'mail_setting.php';
					$mailsetting = new MailSetting();
					$mailsupport = !empty($mailsetting->supportEmail) ? $mailsetting->supportEmail : 'support@azuree-app.com';
					$to[] = $mailsupport;
				}
				$affections_text = implode(' / ', $affections_text);
				// company name
				$external_companies = $this->External->find('list', array(
					'conditions' => array(
						'company_id' => $this->employee_info['Company']['id']
					),
					'fields' => array('id', 'name')
				));

				$company_name = $this->employee_info['Company']['company_name'];
				if ($ticket_info['Ticket']['company_model'] == 'External') {
					$company_name = $external_companies[$ticket_info['Ticket']['company_id']];
				}
				// debug( 'Change status to ' . $status['TicketStatus']['name']);
				$title = sprintf(__('Ticket #%s «%s» «%s» «%s» «%s» «%s»', true), $ticket, $type, $ticket_info['Ticket']['name'], $status['TicketStatus']['name'], $affections_text, $company_name);
				$emails = array_values($emails);
				/* Function _z0GSendEmail($to, $cc, $bcc, $subject, $element, $fileAttach, $debug)
				*/
				$this->_z0GSendEmail($to, null, $emails, $title, 'ticket');
				// debug( $to);debug( $emails); exit;
			}
		}
	}

	private function addComment($ticket, $old, $new)
	{
		if ($old != $new) {
			$o = $this->TicketStatus->read(null, $old);
			$n = $this->TicketStatus->read(null, $new);
			$this->TicketComment->create();
			$this->TicketComment->save(array(
				'employee_id' => $this->employee_info['Employee']['id'],
				'ticket_id' => $ticket,
				'type' => 1,
				'content' => json_encode(array(
					'old' => $o['TicketStatus']['name'],
					'new' => $n['TicketStatus']['name']
				))
			));
			$this->Ticket->saveUpdated($ticket, $this->employee_info['Employee']['id'], time());
		}
	}

	public function update_status()
	{
		$result = false;
		if (!empty($this->data)) {
			$ticket = $this->Ticket->read(null, $this->data['id']);
			if ($this->Ticket->save($this->data)) {

				$this->addComment($ticket['Ticket']['id'], $ticket['Ticket']['ticket_status_id'], $this->data['ticket_status_id']);

				$result = true;
				$this->sendSMS($this->data['id'], $this->data['ticket_status_id']);
				$this->sendMailTicket($this->data['id'], $this->data['ticket_status_id']);
			}
		}
		if (!$result) {
			$this->Session->setFlash(__('Not saved', true), 'error');
		} else {
			$this->Session->setFlash(__('Saved', true), 'success');
		}
		$this->set(compact('result'));
	}

	public function export()
	{
		if (!empty($this->data)) {
			$this->SlickExporter->init();
			$data = json_decode($this->data['data'], true);

			// an example of formatter
			// $this->SlickExporter->addFormatter('Name', function($exporter, $phpExcelSet, $colName, $row, $column){
			// 	$exporter->activeSheet->getStyle($colName . $row)->applyFromArray(array(
			// 		'font' => array(
			// 			'color' => array(
			// 			    'rgb' => '013D74'
			// 			)
			// 		)
			// 	));
			// 	return $column['value'];
			// });

			// save
			$this->SlickExporter
				->setT('Tickets')	//auto translate
				->save($data, 'tickets_{date}.xls');
		}
		die;
	}

	public function delete($id = null)
	{
		if (!$id) {
			$this->Session->setFlash(__('Invalid ID', true), 'error');
			$this->redirect(array('action' => 'index'));
		}
		$profile_id = $this->employee_info['Employee']['ticket_profile_id'];
		$pr = $this->TicketProfile->find('first', array(
			'conditions' => array(
				'id' => $profile_id
			)
		));
		if ($pr && $pr['TicketProfile']['role'] == 'developer') {
			if ($this->Ticket->delete($id)) {
				/**
				 * Xoa comment
				 */
				$this->TicketComment->deleteAll(array('TicketComment.ticket_id' => $id), false);
				/**
				 * Xoa subscription
				 */
				$this->TicketSubscription->deleteAll(array('TicketSubscription.ticket_id' => $id), false);
				/**
				 * Xoa Note
				 */
				$this->TicketNote->deleteAll(array('TicketNote.ticket_id' => $id), false);
				/**
				 * Xoa comment
				 */
				$this->TicketAttachment->deleteAll(array('TicketAttachment.ticket_id' => $id), false);
				$path = $this->getPath($id);
				if (is_dir($path)) {
					$files = scandir($path);
					foreach ($files as $file) {
						if ($file == '.' || $file == '..') {
							continue;
						}
						unlink($path . $file);
					}
					rmdir($path);
				}
				$this->Session->setFlash(__('Deleted', true), 'success');
			} else {
				$this->Session->setFlash(__('Not Deleted', true), 'error');
			}
		} else {
			$this->Session->setFlash(__('Not Deleted', true), 'error');
		}
		$this->redirect(array('action' => 'index'));
	}
	public function keepLogin()
	{
		die(json_encode(true));
	}
	public function ticket_phone_number()
	{
		$this->loadModels('TicketPhoneNumber', 'Company');
		if ($this->employee_info["Employee"]["is_sas"] != 1)
			$company_id = $this->employee_info["Company"]["id"];
		else
			$company_id = "";
		$this->set('company_id', $company_id);
		$companies = $this->Company->find('list');
		$parent_companies = $this->Company->find('list', array('fields' => array('Company.id', 'Company.parent_id')));
		$this->set(compact('companies', 'parent_companies'));
		$phone_data = array();
		if ($company_id != "") {
			$phone_data = $this->TicketPhoneNumber->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company_id
				)
			));
			$this->set('company_names', $this->Company->getTreeList($company_id));
		} else {
			$phone_data = $this->TicketPhoneNumber->find('all', array(
				'recursive' => -1,
			));
			$this->set('company_names', $this->Company->generateTreeList(null, null, null, '--'));
		}

		$this->set(compact('phone_data'));
	}
	public function update_phone_number()
	{
		$this->loadModels('TicketPhoneNumber');
		$data = array();
		$result = false;
		if (empty($this->data)) {
			$this->Session->setFlash(__('Invalid phone number', true), 'error');
		} else {
			$data['acffected_cus'] = 0;
			$data['acffected_dep'] = 0;
			if (!empty($this->data['acffected_to'])) {
				if ($this->data['acffected_to'] == 'developer') {
					$data['acffected_dep'] = 1;
				}
				if ($this->data['acffected_to'] == 'customer') {
					$data['acffected_cus'] = 1;
				}

				unset($this->data['acffected_to']);
			}
			if (empty($this->data['id'])) {
				unset($this->data['id']);
				$this->TicketPhoneNumber->create();
				$id = $this->TicketPhoneNumber->id;
			} else {
				$id = $this->data['id'];
				$data['updated'] = time();
			}
			$continue = $this->checkDuplicatePhone($this->data);
			if ($continue) {
				if ($this->TicketPhoneNumber->save(array_merge($this->data, $data))) {
					$this->Session->setFlash(__('Saved', true), 'success');
					$result = true;
					$this->data = $this->TicketPhoneNumber->read(null, $id);
					$this->data = $this->data['TicketPhoneNumber'];
				} else {
					$this->Session->setFlash(__('NOT SAVED', true), 'error');
				}
			} else {
				$this->Session->setFlash(__('Phone number already exists', true), 'error');
			}
		}
		$this->set(compact('result'));
	}
	/**
	 * delete
	 *
	 * @return void
	 * @access public
	 */
	function delete_phone_number($id = null)
	{
		$this->loadModels('TicketPhoneNumber');
		if (!$id) {
			$this->Session->setFlash(__('Invalid ID', true), 'error');
			$this->redirect(array('action' => 'ticket_phone_number'));
		}
		if ($this->TicketPhoneNumber->delete($id)) {
			$this->Session->setFlash(__('OK.', true), 'success');
		} else {
			$this->Session->setFlash(__('KO.', true), 'error');
		}
		$this->redirect(array('action' => 'ticket_phone_number'));
	}
	function checkDuplicatePhone($data)
	{
		$this->loadModels('TicketPhoneNumber');
		$result = false;
		$phone_data = $this->TicketPhoneNumber->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'phone_number' => $data['phone_number'],
				'company_id' => $data['company_id'],
			)
		));
		if (!empty($phone_data)) {
			if (!empty($data['id']) && ($data['id'] == $phone_data['TicketPhoneNumber']['id'])) {
				$result = true;
			}
		} else {
			$result = true;
		}
		return $result;
	}
	protected function countTickets($category, $limitedDate, $companyId, $externalCompanyId)
	{
		$this->loadModel('TicketMeta');
		$types = $this->TicketMeta->find('list', array(
			'conditions' => array(
				'meta_name' => 'type',
				'company_id' => $companyId,
				'category' => $category
			)
		));
		$count = $this->Ticket->find('count', array(
			'conditions' => array(
				'company_id' => $externalCompanyId,
				'company_model' => 'External',
				'type_id' => $types,
				'created >=' => $limitedDate
			)
		));
		return $count;
	}
	protected function formatCompanyTicketLimits($companyLimits, $companyId, $externalCompany){
		$limitForCustomer = array(
			"support" => array(
				"period" => null,
				"isLimit" => false,
				"limit" => 0,
				"num" => 0
			),
			"formation" => array(
				"period" => null,
				"isLimit" => false,
				"limit" => 0,
				"num" => 0
			),
			"coaching" => array(
				"period" => null,
				"isLimit" => false,
				"limit" => 0,
				"num" => 0
			)
		);
		if(empty($companyLimits)||empty($companyLimits['External']['limit_period'])){
			return $limitForCustomer;
		}
		list($year, $month, $day) = explode('-', $companyLimits['External']['limit_period']);
		$previous = date("Y") - 1;
		$currentYear = date("Y");
		if (date('Y-m-d', strtotime($currentYear . "-" . $month . "-" . $day)) < date("Y-m-d")) {
			$currentYear++;
		}
		
		if ($companyLimits['External']['limit_support'] > 0) {
			$limitForCustomer['support']['period'] = $day . "/" . $month . "/" . $currentYear;
			$limitForCustomer['support']['isLimit'] = true;
			$limitForCustomer['support']['limit'] = $companyLimits['External']['limit_support'];
			$limitForCustomer['support']['num'] = $this->countTickets(1, date('Y-M-D', strtotime($previous . "-" . $month . "-" . $day)), $companyId,  $externalCompany);
			// Debug("numSupportTickets".$numSupportTickets);
		}
		if ($companyLimits['External']['limit_formation'] > 0) {
			$limitForCustomer['formation']['period'] = $day . "/" . $month . "/" . $currentYear;
			$limitForCustomer['formation']['isLimit'] = true;
			$limitForCustomer['formation']['limit'] = $companyLimits['External']['limit_formation'];
			$limitForCustomer['formation']['num'] = $this->countTickets(2, date('Y-M-D', strtotime($previous . "-" . $month . "-" . $day)), $companyId,  $externalCompany);
			// Debug("numFormationTickets".$numFormationTickets);
		}
		if ($companyLimits['External']['limit_coaching'] > 0) {
			$limitForCustomer['coaching']['period'] = $day . "/" . $month . "/" . $currentYear;
			$limitForCustomer['coaching']['isLimit'] = true;
			$limitForCustomer['coaching']['limit'] = $companyLimits['External']['limit_coaching'];
			$limitForCustomer['coaching']['num'] = $this->countTickets(3, date('Y-M-D', strtotime($previous . "-" . $month . "-" . $day)), $companyId,  $externalCompany);
			// Debug("numFormationTickets".$numCoachingTickets);
		}
		return $limitForCustomer;
	}
	public	function getCompanyTicketLimits($externalCompanyId){
		$companyId = $this->employee_info['Company']['id'];
		$this->loadModel('External');
		$companyLimits = $this->External->find('first', array(
			'conditions' => array(
				'External.id' => $externalCompanyId
			),
			'fields' => array(
				'External.limit_support',
				'External.limit_formation',
				'External.limit_coaching',
				'External.limit_period'
			)
		));
		$limitForCustomer = $this->formatCompanyTicketLimits($companyLimits, $companyId, $externalCompanyId);
		$this->layout= 'ajax';
		$this->set(compact('limitForCustomer'));

	}
}
