<?php
class ZogMsgsController extends ApiAppController {
    var $name = 'ZogMsgs';

    public function beforeFilter() {
        parent::beforeFilter();
    }
    // Ref: getComments in z0g_msgs_controller.php
    public function get_messages() {
        if($this->RequestHandler->isPost()) {
            if (!empty($_POST['project_id'])) {
                $id = $_POST['project_id'];
                $idEmployee = $this->employee_info['Employee']['id'];
               
                $comments = $this->ZogMsg->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'ZogMsg.project_id' => $id,
                        'ZogMsg.parent_id' => NULL
                    ),
                    'fields' => array('ZogMsg.id', 'ZogMsg.employee_id', 'ZogMsg.content', 'ZogMsg.created', 'ZogMsg.project_id', 'ZogMsg.parent_id', 'ZogMsg.count_like', 'Employee.first_name', 'Employee.last_name'),
                    'order' => array('created' => 'DESC'),
                    'joins' => array(
                        array(
                            'table' => 'employees',
                            'alias' => 'Employee',
                            'conditions' => array('Employee.id = ZogMsg.employee_id'),
                            'type' => 'inner',
                        ),
                    ),
                ));
                foreach ($comments as $key => $item) {
                    $item = $this->z_merge_all_key($item);
                    $comments[$key] = $item;
                }
                $listIDs = !empty($comments) ? Set::classicExtract($comments, '{n}.id') : array();
                $comments = !empty($comments) ? Set::combine($comments, '{n}.id', '{n}') : array();
                
                if( !empty($listIDs)){
                    $child_comments = $this->ZogMsg->find('all', array(
                        'recursive' => -1,
                        'conditions' => array(
                            // 'ZogMsg.project_id' => $id,
                            'ZogMsg.parent_id' => $listIDs
                        ),
                        'fields' => array('ZogMsg.id', 'ZogMsg.employee_id', 'ZogMsg.content', 'ZogMsg.created', 'ZogMsg.parent_id', 'ZogMsg.count_like','Employee.first_name', 'Employee.last_name'),
                        'order' => array('created' => 'DESC'),
                        'joins' => array(
                            array(
                                'table' => 'employees',
                                'alias' => 'Employee',
                                'conditions' => array('Employee.id = ZogMsg.employee_id'),
                                'type' => 'inner',
                            ),
                        ),
                    ));
                    // $childs = array();
                    foreach ($child_comments as $key => $item) {
                        $item = $this->z_merge_all_key($item);
                        $p_id = $item['parent_id'];
                        if(empty($comments[$p_id]['children'])) $comments[$p_id]['children'] = array();
                        $comments[$p_id]['children'][] = $item;
                    }
                }
                $comments = array_values($comments);
                
                $this->loadModel('Subscribe');
                $subscribe = $this->Subscribe->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_id' => $id,
                        'employee_id' => $idEmployee
                    ),
                    'fields' => array('id', 'subscribe')
                ));
                $subscribe = !empty($subscribe) ? $subscribe['Subscribe']['subscribe'] : 0;
                $this->ZAuth->respond(
                    'success', 
                    array('subscribe' => $subscribe, 'messages'=> $comments), 
                    'list messages', 
                    '0');
                
            }
        }
        $this->ZAuth->respond('fail', null, 'data_incorrect', '0');
        
    }

    public function new_message() {

        if (!empty($_POST['project_id']) && (!empty($_POST['content'])) && !empty($this->employee_info['Employee']['id'])) {
            $idPj = $_POST['project_id'];
            $content = $_POST['content'];
            $idEm = $this->employee_info['Employee']['id'];
            $saved = array(
                'parent_id' => !empty($_POST['parent_id']) ? $_POST['parent_id'] : NULL,
                'employee_id' => $idEm,
                'project_id' => $idPj,
                'content' => $content,
            );
            $this->ZogMsg->create();
            $result = $this->ZogMsg->save($saved);
            // $this->sendEmail($idPj, $content);
            $this->notifyForMessage($idPj, $content);
            // Debug($result);
            $this->ZAuth->respond('success', !empty( $result) ? $this->ZogMsg->id : '', 'Inserted message', '0');
        }
        $this->ZAuth->respond('fail', null, 'data_incorrect', '0');
    }
    public function update_message() {

        if (!empty($_POST['id']) && !empty($_POST['project_id']) && (!empty($_POST['content'])) && !empty($this->employee_info['Employee']['id'])) {
            $idMsg = $_POST['id'];
            $idPj = $_POST['project_id'];
            $content = $_POST['content'];
            $idEm = $this->employee_info['Employee']['id'];
            $list = $this->ZogMsg->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $idMsg,
                    'project_id' => $idPj,
                    'employee_id' => $idEm,
                ),
                //'limit' => 3,
                'fields' => array('*'),
                'order' => array('created' => 'DESC')
            ));
            // Debug(!empty($list));
            // Debug($list[0]['ZogMsg']);
            // exit;
            if(!empty($list)) {
                $saved = $list[0]['ZogMsg'];
                $saved['content'] = $content;
                  
                // $this->ZogMsg->create();
                $result = $this->ZogMsg->save($saved);
                // $this->sendEmail($idPj, $content);
                // Debug($result);
                $this->ZAuth->respond('success', !empty( $result) ? $this->ZogMsg->id : '', 'Updated message', '0');
            } else {
                $this->ZAuth->respond('fail', null, 'data_incorrect', '0');
            }
            
        }
        $this->ZAuth->respond('fail', null, 'data_incorrect', '0');
    }
    public function toggle_like() {

        if (!empty($_POST['zog_msg_id'])) {
            $zogMsg = $this->ZogMsg->find('first', array('conditions' => array('id' => $_POST['zog_msg_id']), 'fields' => array('id', 'count_like')));
            $saved = array(
                'employee_id' => $this->employee_info['Employee']['id'],
                'zog_msg_id' => $_POST['zog_msg_id']
            );
            $this->loadModel('ZogMsgLike');
            $checkLike = $this->ZogMsgLike->find('first', array('conditions' => array('zog_msg_id' => $_POST['zog_msg_id'], 'employee_id' => $this->employee_info['Employee']['id'])));
            // Debug($zogMsg);
            if (!$checkLike) {
                $this->ZogMsgLike->create();
                $this->ZogMsgLike->save($saved);
                $this->ZogMsg->id = $_POST['zog_msg_id'];
                
                $this->ZogMsg->saveField("count_like", (int) $zogMsg['ZogMsg']['count_like'] > 0 ?  (int) $zogMsg['ZogMsg']['count_like'] + 1 : 1);
                // $array['id'] = $_POST['zog_msg_id'];
                // $array['count_like'] = (int) $zogMsg['ZogMsg']['count_like'] + 1;
                $this->ZAuth->respond('success', $this->ZogMsg->id, 'like', '0');
            }
            else {
                $this->ZogMsgLike->id = $checkLike['ZogMsgLike']['id'];
                $result = $this->ZogMsgLike->delete();
                $this->ZogMsg->id = $_POST['zog_msg_id'];
                // Debug($result);
                if ($result) 
                {
                    $this->ZogMsg->saveField("count_like", (int) $zogMsg['ZogMsg']['count_like'] > 0 ? (int) $zogMsg['ZogMsg']['count_like'] - 1 : 0);
                }
                $this->ZAuth->respond('success', $this->ZogMsg->id, 'dis-like', '0');    
            }
        }
        $this->ZAuth->respond('fail', null, 'data_incorrect', '0');
    }
    public function save_subcribe() {
        if (!empty($_POST['project_id']) && !empty($this->employee_info['Employee']['id'])) {
            $id = $_POST['project_id'];
            $subscribe = $_POST['subscribe'];
            $id_employee = $this->employee_info['Employee']['id'];
            $conditions = array(
                'project_id' => $id,
                'employee_id' => $id_employee
            );
            $this->loadModel('Subscribe');
            $record = $this->Subscribe->find('first', array(
                'recursive' => -1,
                'conditions' => $conditions,
                'fields' => array('id', 'subscribe')
            ));
            $conditions['subscribe'] = $subscribe;
            if (!empty($record)) {
                $this->Subscribe->id = $record['Subscribe']['id'];
            } else {
                $this->Subscribe->create();
            }
            $result = $this->Subscribe->save($conditions);
            $this->ZAuth->respond('success', $result, 'saved', '0');
        }
        $this->ZAuth->respond('fail', null, 'data_incorrect', '0');
    }
}