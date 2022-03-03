<?php
class UserFileHelper extends AppHelper {

	public $helpers = array('Html');
	
	/* __construct */
	public function __construct($options = null) {
        parent::__construct($options);
		if( !empty( $options['variables'])){
			foreach( $options['variables'] as $k => $v){
				$this->setval($k,$v);
			}
		}
		if( empty( $this->listEmployeeName )){
			$company_id = @$_SESSION['Auth']['employee_info']['Company']['id'];
			$allEmployee = ClassRegistry::init('Employee')->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'OR' => array(
						'company_id' => $company_id,
						'company_id is NULL',
					),
				),
				'fields' => array('id', 'first_name', 'last_name', 'company_id', 'updated'),
				'order' => array('first_name')
			));
			foreach( $allEmployee as $emp ){
				$emp['Employee']['fullname'] = $emp['Employee']['first_name'] . ' ' . $emp['Employee']['last_name'];
				$listEmployeeName[$emp['Employee']['id']] = $emp['Employee'];
			}
			$PCModel = ClassRegistry::init('ProfitCenter');
			$listPC = $PCModel->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company_id,
				),
				'fields' => array('id', 'name'),
			));
			foreach($listPC as $pc_id => $pc_name){
				$listEmployeeName[$pc_id.'-1']['name'] = 'PC / ' . $pc_name;
				$listEmployeeName[$pc_id.'-1']['is_pc'] = 1;
			}
			$this->listEmployeeName = $listEmployeeName;
		}
		// else {debug( $this->Employee); exit;}
    }

	public function avatar_bk($id, $size = 'small'){
		$default = $this->Html->url('/img/business/avatar.gif');
		return $this->Html->url(sprintf('/user_files/avatar/%s/%s?fallback=%s&t=%d', $id, $size, $default, time()));
	}

	public function setval($var_name, $value){
		$this->$var_name = $value;
	}
	public function employee_fullname($id=null){
		if(!empty(@$this->listEmployeeName[$id]['is_pc'])){
			return @$this->listEmployeeName[$id]['name'];
		}else return @$this->listEmployeeName[$id]['fullname'];
	}
	public function avatar_html($id=null, $size = 'small'){
		if( !empty( $this->listEmployeeName[$id]['fullname']))
			return '<a class="circle-name" title="'.$this->listEmployeeName[$id]['fullname'].'"><span data-id="'.$id.'"><img width = 35 height = 35 src="'.$this->avatar($id, $size).'"/></span></a>';
		return '';
	}
	public function avatar($id=null, $size = 'small'){
		$id = empty($id) ? '%ID%' : $id;
		$size =  ($size == 'small') ? '' : '_avatar';
		$listEmployeeName = !empty($this->listEmployeeName) ? $this->listEmployeeName : array();
		if( !empty( $listEmployeeName[$id]['updated']))return  $this->Html->url('/img/avatar/'.$id.$size.'.png?ver='. $listEmployeeName[$id]['updated']);
		return  $this->Html->url('/img/avatar/'.$id.$size.'.png');
	}
	public function avatarjs_bk($size = 'small'){
		$default = $this->Html->url('/img/business/avatar.gif');
		return json_encode($this->Html->url(sprintf('/user_files/avatar/{id}/%s?fallback=%s&t=%d', $size, $default, time())));
	}
	public function avatarjs($size = 'small'){
		// $size =  ($size == 'small') ? 'avatar_resize' : 'avatar';
		// return  json_encode($this->Html->url(array('controller' => 'employees','action' => 'attachment', '{id}', $size, '?' => array('sid' => time())), true));
		$size =  ($size == 'small') ? '' : '_avatar';
		return  json_encode($this->Html->url('/img/avatar/{id}'.$size.'.png'));
	}

	public function image($path){
		return $this->Html->url(sprintf('/user_files/image/?t=%d&path=%s', time(), $path));
	}

	public function imagejs(){
		echo json_encode($this->Html->url(sprintf('/user_files/image/?t=%d&path={path}', time())));
	}

	public function jimage($var){
		echo json_encode($this->Html->url(sprintf('/user_files/image/?t=%d&path=', time()))) . ' + ' . $var;
	}

	public function ticketImage($company_var, $ticket_var, $file){
		echo json_encode($this->Html->url(sprintf('/user_files/image/?t=%d&path=', time()))) . sprintf(' + %s + "/tickets/" + %s + "/" + %s', $company_var, $ticket_var, $file);
	}

}