<?php
class LogSystemsController extends ApiAppController {
    public $uses = array('LogSystem');
    public function beforFilter(){
        parent::beforFilter();
    }

    public function get_comments_by_project() {
        if($this->RequestHandler->isPost()){
            $data = $_POST;
            $user = $this->ZAuth->user();
            $this->get_employee_info();
            
            $companyId = $this->employee_info['Company']['id'];
            $comment_model = $data['comment_model'];
            $project_id = $data['project_id'];
            if(in_array($this->employee_info['Role']['name'], array('admin','pm'))) {
                
                $query = array(
                    'conditions' => array(
                        'LogSystem.company_id' => $companyId,
                        'LogSystem.model_id' => $project_id,
                        'LogSystem.model' => $comment_model,
                    ),
                    'fields' => array('*'),
                    'order' => array('LogSystem.updated' => 'desc')
                );
                $this->LogSystem->recursive = -1;
                // Get total of LogSystem.
                $total = $this->LogSystem->find('count', array(
                    'conditions' => array(
                        'LogSystem.company_id' => $companyId,
                        'LogSystem.model_id' => $project_id,
                        'LogSystem.model' => $comment_model,
                    )));

                //Limit the number
                if (isset($data['limit']) && $data['limit']>0 && $data['limit']<=20) {
                    $query['limit']=$data['limit'];
                } else {
                    $query['limit']=20;
                }
                if (isset($data['page'])) {
                    $query['page']=$data['page'];
                }
                if (isset($data['offset'])) {
                    $query['offset']=$data['offset'];
                }
                // Debug($query);
                $listComments = $this->LogSystem->find('all', $query);
                $this->ZAuth->respond('success', array(
                    'Total' => $total,
                    'Comments' => $listComments,
                ));
            }
        }
        $this->ZAuth->respond('error', array() ,__('Permission deny'),301);
    }
    //REF function update_text_comment() in file projects_controller.php
    // Update & insert a comment
    public function upsert_comment() {
        if( !$this->RequestHandler->isPost() ){
            $this->ZAuth->respond('data_incorrect', null, 'data_incorrect', '0');
            return;
        }
        if(empty($this->data['id']) || empty($this->data['model']) || empty($this->data['text'])) {
            $this->ZAuth->respond('data_incorrect', null, 'data_incorrect', '0');
            return;
        }
        $result = false;
        $model = '';
		$project_id = !empty($this->data['id']) ? $this->data['id'] : '';
        
        if (!empty($this->data) && in_array($this->employee_info['Role']['name'], array('admin','pm'))) {

            $company_id = $this->employee_info['Company']['id'];
            $name = $this->employee_info['Employee']['fullname'] . ' ' . date('H:i d/m/Y', time());
            unset($this->data['id']);
            $check = $this->LogSystem->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'model_id' => $project_id
                ),
                'fields' => array('model', 'description'),
                'order' => array('id' => 'ASC')
            ));
            $data = array();
            
            if (!empty($this->data['model']) && ( in_array($this->data['model'], array('ProjectAmr', 'ToDo','Done','ProjectIssue','ProjectRisk','Scope', 'Schedule', 'Budget', 'Resources', 'Technical')) )) {
                $model = $this->data['model'];
                $data = array(
                    'company_id' => $company_id,
                    'model' => $model,
                    'model_id' => $project_id,
                    'name' => $name,
                    'description' => $this->data['text'],
                    'employee_id' => $this->employee_info['Employee']['id'],
                    'update_by_employee' => $this->employee_info['Employee']['fullname']
                );
            }
            
            if (!empty($this->data['logid'])) {
                // edit log comment
                $this->LogSystem->id = $this->data['logid'];
                $save_field = array(
                    'description' => $this->data['text'],
                    'updated' => time(),
                );
                if (!empty($data) && $this->LogSystem->save($save_field)) {
                    $result = true;
                }
            } else {
                $this->LogSystem->create();
                if (!empty($data) && $this->LogSystem->save($data)) {
                    $result = true;
                }
            }
            $this->data['id'] = $project_id;
            $this->notifyForComment($project_id, $this->data['model'], $this->data['text']); // Send notify to users.
            $this->ZAuth->respond('success', $this->LogSystem->id, 'Success', '0');
        }
        $this->ZAuth->respond('fail', null, 'Permission Deny', '0');
    }
}