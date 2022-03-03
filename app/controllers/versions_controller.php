<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class VersionsController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'Versions';

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation');

    //var $layout = 'administrators';
    /**
     * add_version
     *
     * @return void
     * @access public
     */
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('version');
		$this->Auth->autoRedirect = false;
	}
    function updateVersion(){
       $this->autoRender = false; 
        if (($this->employee_info['Employee']['is_sas'] == 1)||($this->employee_info['Role']['name'] == 'admin')){
            if (file_exists('version/version.txt')) {
                $fp = fopen('version/version.txt', 'r');
                $contents = fread($fp, filesize('version/version.txt'));
                fclose($fp); 
                $alls = explode('---------------------',$contents);
                $ver = $update = '';
                //$i=0;
                $final = '0';
                $finalId = 0;
                $check = array();
                foreach($alls as $all):
                    $all = trim($all);
                    if( !$all )continue;
                    $top = explode("@Version:", $all);
                    $ver = explode("@Updatedate:", $top[1]);
                    $update = explode("@News:", $ver[1]);
                    $name = strval(trim($ver[0]));
                    $update[1] = str_replace("“", '"', $update[1]);
                    $update[1] = str_replace("”", '"', $update[1]);
                    $update[1] = str_replace("`", "'", $update[1]);
                    $check = $this->Version->find('first',array('recursive'=>-1,'conditions'=>array(
                        'name'=>$name),
                        'fields'=>array('id','is_current_version')
                    ));
                    $ver[0] = trim($ver[0]);
                    // kiem tra phien ban ton tai chua
                    if(empty($check)){
                        $this->Version->create();
                        $_dateAdd['name'] = $ver[0];
                        $_dateAdd['content'] = $update[1];
                        $_dateAdd['updated'] = strtotime($update[0]);
                        $_dateAdd['is_current_version'] = 0;
                        // if($i==0){
                        //     $_dateAdd['is_current_version'] = 1;
                        // }
                        $this->Version->save($_dateAdd);
                    }else{
                        $this->Version->id = $check['Version']['id'];
                        $_dateAdd['name'] = $ver[0];
                        $_dateAdd['content'] = $update[1];
                        $_dateAdd['updated'] = strtotime($update[0]);
                        $_dateAdd['is_current_version'] = 0;
                        $this->Version->save($_dateAdd);
                    }
                    if( version_compare($final, $ver[0], 'lt') ){
                        $finalId = $this->Version->id;
                        $final = $ver[0];
                    }
                    //$i++;
                endforeach;
                //$this->Version->updateAll(array('is_current_version'=>0));
                $this->Version->id = $finalId;
                $this->Version->save(array(
                    'is_current_version' => 1,
                    'updated' => false
                ));
                $this->Session->setFlash(__('Saved', true), 'success');
                $this->redirect(array('controller'=>'versions','action' => 'index'));
            }else{
                $this->Session->setFlash(__('The file version.txt does not exist', true), 'error');
                $this->redirect(array('controller'=>'versions','action' => 'index'));
            }
        }else{
             $this->Session->setFlash(__('NOT PERMISSION', true), 'error');
             $this->redirect(array('controller'=>'employees','action' => 'index'));
        }    
        exit();
    }
    /**
     * add_version
     *
     * @return void
     * @access public
     */
    /*
    function updateVersionE(){
        $this->autoRender = false;
        if (($this->employee_info['Employee']['is_sas'] == 1)||($this->employee_info['Role']['name'] == 'admin')){
            $lastest = $this->Version->find('first',array('conditions'=>array('is_current_version'=>1)));
            if (file_exists('version/version.txt')) {
                if(filemtime('version/version.txt') == $lastest['Version']['created']){
                    $this->Session->setFlash(__('Version was updated', true), 'success');
                    $this->redirect(array('controller'=>'versions','action' => 'index'));
                }
            }else{
                $this->Session->setFlash(__('The file version.txt does not exist', true), 'error');
                $this->redirect(array('controller'=>'versions','action' => 'index'));
            }
            $title = date('Y')-2011;
            $title.='.'.intval(date('m'));
            $lastestVersion = explode('.',$lastest['Version']['name']);
            // fix when version new month/new year (11.09.2014)
            if(intval($lastestVersion[1])!=intval(date('m'))){
                $title.='.1';    
            }else{
                $title.='.'.(intval($lastestVersion[2])+1);
            }
            //end fix
            $checkExist = $this->Version->find('first',array('conditions'=>array('name'=>$title)));
            if(empty($checkExist)&&$lastest['Version']['updated']<strtotime(date('m/d/Y'))){
                $this->Version->create();
                $fp = fopen('version/version.txt', 'r');
                $contents = fread($fp, filesize('version/version.txt'));
                fclose($fp);
                $contents = str_replace("“", '"', $contents);
                $contents = str_replace("”", '"', $contents);
                $contents = str_replace("`", "'", $contents);
                $this->Version->updateAll(array('is_current_version'=>0));
                $data['Version']['name'] = $title;
                $data['Version']['created'] = filemtime('version/version.txt');
                $data['Version']['content'] = $contents;
                $data['Version']['is_current_version'] = 1;
                $this->Version->save($data);
                $this->Session->setFlash(__('Saved', true), 'success');
                $this->redirect(array('controller'=>'versions','action' => 'index'));
            }else{
                $fp = fopen('version/version.txt', 'r');
                $contents = fread($fp, filesize('version/version.txt'));
                fclose($fp);
                $contents = str_replace("“", '"', $contents);
                $contents = str_replace("”", '"', $contents);
                $contents = str_replace("`", "'", $contents);
                $a = $this->Version->read(null,$lastest['Version']['id']);
                $this->Version->set(array(
                                'content' => $contents,
                                'created' => filemtime('version/version.txt')
                            ));
                $this->Version->save();
                $this->Session->setFlash(__('UPDATED', true), 'success');
                $this->redirect(array('controller'=>'versions','action' => 'index'));
            }
        }else{
             $this->Session->setFlash(__('NOT PERMISSION', true), 'error');
             $this->redirect(array('controller'=>'employees','action' => 'index'));
        }    
        exit();
    }
    */
    /**
     * index
     *
     * @return void
     * @access public
     */
    function index($id = null) {
        if (($this->employee_info['Employee']['is_sas'] == 1)||($this->employee_info['Role']['name'] == 'admin')){
			$conditions = array();
			if(!empty($id)){
				$conditions['id'] = $id;
			}
			
			$versions = $this->Version->find("first", array(
				'recursive' => -1,
				'conditions' => $conditions,
				'fields' => array('id','name','content','updated','created','is_current_version'),
				'order' => array('updated' => 'desc', 'updated' => 'desc'))
			);
			
			$version_number = $this->Version->find("list", array(
				'recursive' => -1,
				'fields' => array('id','name'),
				'order' => array('updated' => 'desc', 'updated' => 'desc'))
			);
			$this->set(compact('versions', 'version_number'));
			
			if( $this->params['isAjax']) die(json_encode(array(
				'success' => !empty( $versions) ? 'success' : 'failed',
				'data' => !empty( $versions) ? $versions : '',
			)));
		
        }else{
             $this->Session->setFlash(__('NOT PERMISSION', true), 'error');
             $this->redirect(array('controller'=>'employees','action' => 'index'));
        }
    }
	
    function version($id = null) {
		$this->layout = false;
		$conditions = array();
		if(!empty($id)){
			$conditions['id'] = $id;
		}
		
		$versions = $this->Version->find("first", array(
			'recursive' => -1,
			'conditions' => $conditions,
			'fields' => array('id','name','content','updated','created','is_current_version'),
			'order' => array('updated' => 'desc', 'updated' => 'desc'))
		);
		
		$version_number = $this->Version->find("list", array(
			'recursive' => -1,
			'fields' => array('id','name'),
			'order' => array('updated' => 'desc', 'updated' => 'desc'))
		);
		
		$this->set(compact('versions', 'version_number'));
		
		if( $this->params['isAjax']) die(json_encode(array(
			'success' => !empty( $versions) ? 'success' : 'failed',
			'data' => !empty( $versions) ? $versions : '',
		)));
    }
    /**
     * view
     *
     * @return void
     * @access public
     */
    function view($id = null) {
        if (($this->employee_info['Employee']['is_sas'] == 1)||($this->employee_info['Role']['name'] == 'admin')){
            if (!$id) {
                $this->Session->setFlash(__('Invalid version', true), 'error');
                $this->redirect(array('action' => 'index'));
            }
            $this->set('version', $this->Version->read(null, $id));
        }else{
             $this->Session->setFlash(__('NOT PERMISSION', true), 'error');
             $this->redirect(array('controller'=>'employees','action' => 'index'));
        }    
    }
    
    /**
     * edit
     *
     * @return void
     * @access public
     */
    function edit($id = null) {
        if ($this->employee_info['Employee']['is_sas'] == 1){
            if (!$id && empty($this->data)) {
                $this->Session->setFlash(__('Invalid version', true), 'error');
                $this->redirect(array('action' => 'index'));
            }
            if (!empty($this->data)) {
                $this->Version->updateAll(array('is_current_version'=>0));
                $this->data['Version']['is_current_version'] = 1;
                if ($this->Version->save($this->data)) {
                    $this->Session->setFlash(__('Saved', true), 'success');
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(__('NOT SAVED', true), 'error');
                    $this->redirect(array('action' => 'index'));
                }    
            }
            if (empty($this->data)) {
                $this->data = $this->Version->read(null, $id);
            }
         }else{
             $this->Session->setFlash(__('NOT PERMISSION', true), 'error');
             $this->redirect(array('controller'=>'employees','action' => 'index'));
        }    
    }

    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null) {
        if ($this->employee_info['Employee']['is_sas'] == 1){
            if (!$id) {
                $this->Session->setFlash(__('Invalid id for version', true), 'error');
                $this->redirect(array('action' => 'index'));
            }
            $checkDefault = $this->Version->find('first',array('conditions'=>array('Version.id'=>$id,'Version.is_current_version'=>1)));
            if(empty($checkDefault)){
                if ($this->Version->delete($id)) {
                    $this->Session->setFlash(__('Deleted', true), 'success');
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(__('Not deleted', true), 'error');
                    $this->redirect(array('action' => 'index'));
                }
            }else{
                $this->Session->setFlash(__('Not deleted', true), 'error');
                $this->redirect(array('action' => 'index'));
            }
         }else{
             $this->Session->setFlash(__('NOT PERMISSION', true), 'error');
             $this->redirect(array('controller'=>'employees','action' => 'index'));
        }    
    }
    /*
    * Update current version
    */
    function update_version($id = null){
        if ($this->employee_info['Employee']['is_sas'] == 1){
          $this->layout = '';
          $this->Version->updateAll(array('is_current_version'=>0));
          $this->Version->id = $id;
          $this->Version->saveField('is_current_version',1);
          exit();
        }else{
             $this->Session->setFlash(__('NOT PERMISSION', true), 'error');
             $this->redirect(array('controller'=>'employees','action' => 'index'));
        } 
    }
}
?>