<?php
class NotifyTokensController extends ApiAppController {
    var $name = 'NotifyTokens';
    public $components = array('ZNotifyExpo');
    public function beforeFilter() {
        parent::beforeFilter();
    }
    // Ref: getComments in z0g_msgs_controller.php
    public function get_notification_config() {
        if($this->RequestHandler->isPost()) {
            if (!empty($_POST['notify_token'])) {
                $token = $_POST['notify_token'];
                $idEmployee = $this->employee_info['Employee']['id'];
               
                $config = $this->NotifyToken->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'token' => $token,
                        'employee_id' => $idEmployee
                    )
                ));
                
                $this->ZAuth->respond(
                    'success', 
                    $config, 
                    'Your notification config', 
                    '0');
                
            }
        }
        $this->ZAuth->respond('fail', null, 'data_incorrect', '0');
        
    }
    public function upsert_config() {
        if( !$this->RequestHandler->isPost() ){
            $this->ZAuth->respond('data_incorrect', null, 'data_incorrect', '0');
            return;
        }
        $data = $_POST;
        if(empty($this->data['notify_token'])) {
            $this->ZAuth->respond('data_incorrect', null, 'data_incorrect', '0');
            return;
        }
		$notify_token = $this->data['notify_token'];
        $idEmployee = $this->employee_info['Employee']['id'];


        $check = $this->NotifyToken->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'token' => $notify_token,
                'employee_id' => $idEmployee
            ),
        ));

        $upsertData = array();
        $upsertData['notification_message'] = isset($data['notification_message'])? (int)$data['notification_message'] : 0;
        $upsertData['notification_message_project'] = isset($data['notification_message_project'])? (int)$data['notification_message_project'] : 0;
        $upsertData['notification_task_new'] = isset($data['notification_task_new'])? (int)$data['notification_task_new']: 0;
        $upsertData['notification_task_update'] = isset($data['notification_task_update'])? (int)$data['notification_task_update'] : 0;
        if(isset($data['device_name'])) $upsertData['device_name'] = $data['device_name'];
        $upsertData['updated'] = time();
        $upsertData['employee_id'] = $idEmployee;
        $upsertData['token'] = $notify_token;
        if(!empty($check)) {
            // cap nhat
            $upsertData['id'] = $check['NotifyToken']['id'];
            $upsertData['language'] = isset($data['language'])? $data['language'] : $check['NotifyToken']['language'];
            
            $upsertData['created'] = $check['NotifyToken']['created'];
        } else {
            $upsertData['language'] = isset($data['language'])? $data['language'] : 'fr';
            $upsertData['created'] = time();
            // tao moi
        }

        $result = $this->NotifyToken->save($upsertData);
        $result['id'] = $this->NotifyToken->id;
        $this->ZAuth->respond('success', $result, 'Success', '0');

    }
    public function set_notify_language() {
        if( !$this->RequestHandler->isPost() ){
            $this->ZAuth->respond('data_incorrect', null, 'data_incorrect', '0');
            return;
        }
        $data = $_POST;
        if(empty($this->data['notify_token']) || empty($this->data['language'])) {
            $this->ZAuth->respond('data_incorrect', null, 'data_incorrect', '0');
            return;
        }
		$notify_token = $this->data['notify_token'];
        $idEmployee = $this->employee_info['Employee']['id'];


        $check = $this->NotifyToken->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'token' => $notify_token,
                'employee_id' => $idEmployee
            ),
        ));
        if(empty($check)) {
            $this->ZAuth->respond('fail', null, 'empty', '0');
            return;
            
        } else {
            // cap nhat
            $check['NotifyToken']['language'] = $data['language'];
            

            $result = $this->NotifyToken->save($check['NotifyToken']);
            $result['id'] = $this->NotifyToken->id;
            $this->ZAuth->respond('success', $result, 'Success', '0');
        }
        

    }
    public function test_notify() {
        if( !$this->RequestHandler->isPost() ){
            $this->ZAuth->respond('data_incorrect', null, 'data_incorrect', '0');
            return;
        }
        $data = $_POST;
        if(empty($this->data['notify_token'])) {
            $this->ZAuth->respond('data_incorrect', null, 'data_incorrect', '0');
            return;
        }
		$notify_token = $this->data['notify_token'];
        $title = $this->data['title'];
        $content = $this->data['content'];
        $arr_data = $this->data['arr_data'];
        $project_id = $this->data['project_id'];

        // $service_name = ucfirst(array_shift((explode('.', $_SERVER['HTTP_HOST']))));
        // $service_name = !empty($service_name) ? $service_name : 'Preprod1';
        // $enable = MAppNotifyExpo::isEnableCrawl();
        // $s_name = MAppNotifyExpo::getServiceName();
        // Debug($enable);
        // Debug($s_name);
        // Debug($service_name);
        // exit;

        // $result = $this->notifyForMessage($project_id);
        // $result = $this->notifyForComment($project_id);
        // $result = $this->notifyForNewTask($project_id, 23511);
        // $result = $this->notifyForUpdateTask($project_id, 23511);
        // $result = $this->notifyForDeleteTask($project_id, 23511);
        // Debug($this->employee_info['Company']['id']);
        // Debug($data);
        // Debug(array_unique($people));
        // Debug($listAdmin);
        // exit;

        $result = $this->ZNotifyExpo->send_notify_expo($notify_token, $title, $content, $arr_data);
        // $this->writeLog($result, $this->employee_info, sprintf('Send Notify'), $this->employee_info['Company']['id']);
        $this->ZAuth->respond('success', $result, 'Success', '0');
    }
}