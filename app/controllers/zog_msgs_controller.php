<?php

/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ZogMsgsController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ZogMsgs';

    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModels('Project', 'Employee', 'ProjectEmployeeManager', 'Subscribe', 'HistoryFilter', 'ZogMsgLike', 'ZogMsgRefer');
    }

    /**
     * index
     *
     * @return void
     * @access public
     */
    function index() {
        $thisMe = $this->action;
        $company_id = $this->employee_info['Company']['id'];
        $employee_id = $this->employee_info['Employee']['id'];
        $role = $this->employee_info['Role']['name'];
        if ($role == 'admin') {
            $conditions = array(
                'company_id' => $company_id,
                'category' => 1
            );
        } else {
            $listIds = $this->ProjectEmployeeManager->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_manager_id' => $employee_id
                ),
                'fields' => array('id', 'project_id')
            ));
            $listIds = array_unique($listIds);
            $conditions = array(
                'OR' => array(
                    'Project.id' => $listIds,
                    'technical_manager_id' => $employee_id,
                    'project_manager_id' => $employee_id,
                    'chief_business_id' => $employee_id
                ),
                'category' => 1
            );
        }
        $listProject = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => $conditions,
            'fields' => array('id', 'project_name'),
            'order' => array('project_name' => 'ASC')
        ));
        $this->set(compact('listProject'));
        // debug($listProject); exit;
        $employee_id = $this->employee_info['Employee']['id'];

        $this->loadModels('ProjectGlobalView');
        $listProjects = array();
        $globals = array();
        $listProjectIds = !empty($listProject) ? Set::combine($listProject, '{n}.Project.id', '{n}.Project.id') : array();

        foreach ($listProject as $project_id => $listProject) {
            // tinh global.
            $projectGlobalView = $this->ProjectGlobalView->find("first", array(
                'recursive' => -1, 'fields' => array('id', 'attachment', 'is_file', 'is_https'),
                "conditions" => array('project_id' => $project_id)));

            if ($projectGlobalView) {
                $noFileExists = false;
                $link = trim($this->_getPathGlobal($project_id, 'global')
                        . $projectGlobalView['ProjectGlobalView']['attachment']);
                if (!file_exists($link) || !is_file($link)) {
                    $noFileExists = true;
                }
                $globals[$project_id] = array(
                    'global' => $projectGlobalView,
                    'file' => $noFileExists
                );
            }
        }

        $this->set(compact('thisMe', 'employee_id', 'globals'));
    }

    function detail($id) {
        $thisMe = $this->action;
        $company_id = $this->employee_info['Company']['id'];
        $employee_id = $this->employee_info['Employee']['id'];
        $role = $this->employee_info['Role']['name'];
        if ($role == 'admin') {
            $conditions = array(
                'company_id' => $company_id
            );
        } else {
            $listIds = $this->ProjectEmployeeManager->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_manager_id' => $employee_id
                ),
                'fields' => array('id', 'project_id')
            ));
            $listIds = array_unique($listIds);
            $conditions = array(
                'OR' => array(
                    'Project.id' => $listIds,
                    'technical_manager_id' => $employee_id,
                    'project_manager_id' => $employee_id,
                    'chief_business_id' => $employee_id
                )
            );
        }
        $listProject = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => $conditions,
            'fields' => array('id', 'project_name'),
            'order' => array('project_name' => 'ASC')
        ));
        $employee_id = $this->employee_info['Employee']['id'];
        // security
        if (!empty($this->employee_info['Employee']['profile_account'])) {
            $this->loadModel('ProfileProjectManagerDetail');
            $profileName = $this->ProfileProjectManagerDetail->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'model_id' => $this->employee_info['Employee']['profile_account'],
                    'widget_id' => 'zog_msgs'
                )
            ));
            $profileName = !empty($profileName) ? $profileName['ProfileProjectManagerDetail']['display'] : 0;
        }
        if (empty($this->employee_info['Employee']['profile_account'])) {
            if (empty($listProject[$id])) {
                $this->Session->setFlash(sprintf(__('The project "#%s" was not found, please try again', true), $id), 'error');
                $this->redirect(array('controller' => 'projects', 'action' => 'index'));
            }
        } else {
            if (!$profileName) {
                $this->Session->setFlash(sprintf(__('The project "#%s" was not found, please try again', true), $id), 'error');
                $this->redirect(array('controller' => 'projects', 'action' => 'index'));
            }
        }
        $project_id = $id;
        $this->set(compact('thisMe', 'id', 'listProject', 'employee_id', 'project_id'));
        $this->action = 'index';
    }

    public function getComment() {
        if (!empty($_POST['id'])) {
            $id = $_POST['id'];
            // debug($id); exit;
            $idEmployee = $this->employee_info['Employee']['id'];
            $this->loadModels('ProjectGlobalView', 'Project');
            $listProjects = array();
            $globals = array();

            $projectGlobalView = $this->ProjectGlobalView->find("first", array(
                'recursive' => -1, 'fields' => array('id', 'attachment', 'is_file', 'is_https'),
                "conditions" => array('project_id' => $id)));
            $link = '';
            if ($projectGlobalView) {
                $noFileExists = false;
                $link = trim($this->_getPathGlobal($id, 'global')
                        . $projectGlobalView['ProjectGlobalView']['attachment']);
                if (!file_exists($link) || !is_file($link)) {
                    $noFileExists = true;
                }
            }
            $filter = $this->HistoryFilter->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'path' => 'zog_msgs',
                    'employee_id' => $idEmployee
                ),
                'fields' => array('id', 'params')
            ));
            $filter = !empty($filter) ? $filter['HistoryFilter']['params'] : 'DESC';
            $comments = $this->ZogMsg->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $id,
                    'parent_id' => NULL
                ),
                'fields' => array('*'),
                'order' => array('created' => $filter)
            ));
            $listComments = !empty($comments) ? Set::combine($comments, '{n}.ZogMsg.id', '{n}.ZogMsg') : array();
            $listIdEm = !empty($comments) ? array_unique(Set::classicExtract($comments, '{n}.ZogMsg.employee_id')) : array();
            $employees = $this->Employee->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'Employee.id' => $listIdEm
                ),
                'fields' => array('id', 'avatar', 'first_name', 'last_name')
            ));
            $employees = !empty($employees) ? Set::combine($employees, '{n}.Employee.id', '{n}.Employee') : array();
            $data = $_employee = array();
            foreach ($listComments as $_comment) {
               
                $_id = $_comment['employee_id'];
                $_comment['employee_id'] = $employees[$_id];
                $_comment['created'] = $this->time($_comment['created']);
                $_comment['sub'] = $this->subComment($_comment['id']);
                $data['comment'][] = $_comment;
            }
            $subscribe = $this->Subscribe->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $id,
                    'employee_id' => $idEmployee
                ),
                'fields' => array('id', 'subscribe')
            ));
            $subscribe = !empty($subscribe) ? $subscribe['Subscribe']['subscribe'] : 0;

            // lay max Id.
            $company_id = $this->employee_info['Company']['id'];
            $listProject = $this->Project->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                    'category' => 1
                ),
                'fields' => array('id', 'project_name'),
                'order' => array('project_name' => 'ASC')
            ));
            $_maxId = $this->ZogMsg->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => array_keys($listProject)
                ),
                'fields' => array('id', 'MAX(ZogMsg.id) AS id')
            ));
            $_maxId = !empty($_maxId) && !empty($_maxId[0][0]['id']) ? $_maxId[0][0]['id'] : 0;
            $data['subscribe'] = $subscribe;
            $data['order'] = $filter;
            $data['maxId'] = $_maxId;
            $data['link'] = $link;
            // debug($data);
            die(json_encode($data));
        }
        exit;
    }

    public function subComment($parent_id) {
        $comments = $this->ZogMsg->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'parent_id' => $parent_id
            ),
            'fields' => array('*'),
        ));
        $listComments = !empty($comments) ? Set::combine($comments, '{n}.ZogMsg.id', '{n}.ZogMsg') : array();
        $listIdEm = !empty($comments) ? array_unique(Set::classicExtract($comments, '{n}.ZogMsg.employee_id')) : array();
        $employees = $this->Employee->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'Employee.id' => $listIdEm
            ),
            'fields' => array('id', 'avatar', 'first_name', 'last_name')
        ));
        $employees = !empty($employees) ? Set::combine($employees, '{n}.Employee.id', '{n}.Employee') : array();

        $data = $_employee = array();
        if (!empty($listComments)) {
            foreach ($listComments as $_comment) {
                $_id = $_comment['employee_id'];
                $_comment['created'] = $this->time($_comment['created']);
                $_comment['employee_id'] = $employees[$_id];
                $data['comment'][] = $_comment;
            }
        }
        return $data;
    }

    public function saveComment() {
        $success = false;
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
            $success = (boolean) $this->ZogMsg->save($saved);
            $this->sendEmail($idPj, $content);
            $this->notifyForMessage($idPj, $content);
            $comment = $this->ZogMsg->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $this->ZogMsg->getLastInsertId()
                ),
            ));
            $employee_info = $this->Session->read('Auth.employee_info');
            $data['id'] = $this->ZogMsg->getLastInsertId();
            $data['content'] = $comment['ZogMsg']['content'];
            $data['count_like'] = $comment['ZogMsg']['count_like'];
            $data['employee_id'] = $comment['ZogMsg']['employee_id'];
            $data['first_name'] = $employee_info['Employee']['first_name'];
            $data['last_name'] = $employee_info['Employee']['last_name'];
            $data['created'] = $this->time($comment['ZogMsg']['created']);
            $data['parent_id'] = $comment['ZogMsg']['parent_id'];
            $data['project_id'] = $comment['ZogMsg']['project_id'];
            die(json_encode($data));
        }
        die(json_encode($success));
    }

    public function sendEmail($idPj, $messages) {
        $idEm = $this->employee_info['Employee']['id'];
        $listId = $this->Subscribe->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $idPj
            ),
            'fields' => array('id', 'employee_id')
        ));

        if (!empty($listId)) {
            $key = array_search($idEm, $listId);
            if (!empty($key)) {
                unset($listId[$key]);
            }

            $listEmail = $this->Employee->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'Employee.id' => $listId
                ),
                'fields' => array('id', 'email')
            ));
            $to = $listEmail;

            $PjName = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'Project.id' => $idPj
                ),
                'fields' => array('id', 'project_name')
            ));
            $fullName = $this->employee_info['Employee']['first_name'] . ' ' . $this->employee_info['Employee']['last_name'];
            $PjName = !empty($PjName) ? $PjName['Project']['project_name'] : '';
            $title = 'Project name:' . ' "' . $PjName . '" ' . $fullName . ' ' . date('Y-m-d h:i:sa');
            $messages = $title . ' ' . $messages;
            if (!empty($to)) {
                $saveContent = $messages;
                $this->set(compact('saveContent'));
                $success = (boolean) $this->_sendEmail($to, $title, 'subscribe', false, $listEmail);
            }
        }
        if (!empty($success)) {
            die('done');
        }
//        die('error');
    }

    public function saveSub() {
        if (!empty($_POST['id']) && !empty($this->employee_info['Employee']['id'])) {
            $id = $_POST['id'];
            $subscribe = $_POST['subscribe'];
            $id_employee = $this->employee_info['Employee']['id'];
            $conditions = array(
                'project_id' => $id,
                'employee_id' => $id_employee
            );
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
            $this->Subscribe->save($conditions);
            die('done');
        }
        die;
    }

    public function saveFilter() {
        if (!empty($_POST)) {
            $employee_id = $_POST['employee_id'];
            $conditions = array(
                'path' => 'zog_msgs',
                'employee_id' => $employee_id
            );
            $filter = $this->HistoryFilter->find('first', array(
                'recursive' => -1,
                'conditions' => $conditions,
                'fields' => array('id', 'params')
            ));
            if (empty($filter)) {
                $conditions['params'] = 'ASC';
                $this->HistoryFilter->create();
                $this->HistoryFilter->save($conditions);
            } else {
                // $filter = $filter['HistoryFilter']['params'];
                $conditions['params'] = ( $filter['HistoryFilter']['params'] == 'ASC') ? 'DESC' : 'ASC';
                $this->HistoryFilter->id = $filter['HistoryFilter']['id'];
                $this->HistoryFilter->save($conditions);
            }
        }
        die(json_encode($conditions['params']));
    }

    protected function _getPathGlobal($project_id, $global_view = false) {
        $company = $this->Project->find('first', array(
            'recursive' => 0,
            'fields' => array(
                'Company.parent_id',
                'Company.company_name',
                'Company.dir'
            ), 'conditions' => array('Project.id' => $project_id)));
        $pcompany = $this->Project->Company->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company['Company']['parent_id'])));
        $path = FILES . 'projects' . DS . 'localviews' . DS;
        if ($global_view == 'global')
            $path = FILES . 'projects' . DS . 'globalviews' . DS;
        if ($pcompany) {
            $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['dir'])) . DS;
        }
        $path .= $company['Company']['dir'] . DS;
        return $path;
    }

    public function getNewComment() {
        if (!empty($_POST)) {
            $projectId = $_POST['_id'];
            $maxId = $_POST['maxId'];
            $id_employee = $this->employee_info['Employee']['id'];
            $comments = $this->ZogMsg->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $projectId,
                    'id > ' => $maxId
                ),
                'fields' => array('*'),
                'order' => array('created' => 'DESC')
            ));
            $listComments = !empty($comments) ? Set::combine($comments, '{n}.ZogMsg.id', '{n}.ZogMsg') : array();
            $listIdEm = !empty($comments) ? array_unique(Set::classicExtract($comments, '{n}.ZogMsg.employee_id')) : array();
            $employees = $this->Employee->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'Employee.id' => $listIdEm
                ),
                'fields' => array('id', 'avatar', 'first_name', 'last_name')
            ));
            $employees = !empty($employees) ? Set::combine($employees, '{n}.Employee.id', '{n}.Employee') : array();
//            $_newCommnet = array();
//            foreach ($newComment as $key => $value) {
//                $dx = $value['ZogMsg']['employee_id'];
//                $_newCommnet['employee_id'] = $employees[$dx]['first_name'] . ' ' . $employees[$dx]['last_name'];
//                $newComment[$key]['ZogMsg']['name'] = $_newCommnet['employee_id'];
//            }
//            $results['comment'] = $newComment;
            $_maxId = $this->ZogMsg->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $projectId
                ),
                'fields' => array('id', 'MAX(ZogMsg.id) AS id')
            ));
            $data = $_employee = array();
            if (!empty($listComments)) {
                foreach ($listComments as $_comment) {
                    $_id = $_comment['employee_id'];
                    $_comment['created'] = $this->time($_comment['created']);
                    $_comment['employee_id'] = $employees[$_id];
                    $_comment['sub'] = $this->subComment($_comment['id']);
                    $data['comment'][] = $_comment;
                }
            }
            $data['maxId'] = !empty($_maxId) && !empty($_maxId[0][0]['id']) ? $_maxId[0][0]['id'] : 0;
            ;
            die(json_encode($data));
        }
        exit;
    }

    public function listProjects() {
        $thisMe = $this->action;
        $company_id = $this->employee_info['Company']['id'];
        $employee_id = $this->employee_info['Employee']['id'];
        $role = $this->employee_info['Role']['name'];
        if ($role == 'admin') {
            $conditions = array(
                'company_id' => $company_id,
                'category' => 1
            );
        } else {
            $listIds = $this->ProjectEmployeeManager->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_manager_id' => $employee_id
                ),
                'fields' => array('id', 'project_id')
            ));
            $listIds = array_unique($listIds);
            $conditions = array(
                'OR' => array(
                    'Project.id' => $listIds,
                    'technical_manager_id' => $employee_id,
                    'project_manager_id' => $employee_id,
                    'chief_business_id' => $employee_id
                ),
                'category' => 1
            );
        }
        $listProject = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => $conditions,
            'fields' => array('id', 'project_name'),
            'order' => array('project_name' => 'ASC')
        ));
        $data = array();
        $firstId = 0;
        if (!empty($listProject)) {
            foreach ($listProject as $id => $name) {
                $count = $this->ZogMsg->find('count', array('recursive' => -1, 'conditions' => array(
                        'project_id' => $id
                )));
                $data[] = array('id' => $id, 'name' => $name, 'count' => $count, 'employee_id' => $employee_id);
            }
        }
        die(json_encode($data));
    }

    public function like() {
        $success = false;
        if (!empty($_POST['zog_msg_id'])) {
            $zogMsg = $this->ZogMsg->find('first', array('conditions' => array('id' => $_POST['zog_msg_id']), 'fields' => array('id', 'count_like')));
            $saved = array(
                'employee_id' => $this->employee_info['Employee']['id'],
                'zog_msg_id' => $_POST['zog_msg_id']
            );
            $checkLike = $this->ZogMsgLike->find('first', array('conditions' => array('zog_msg_id' => $_POST['zog_msg_id'], 'employee_id' => $this->employee_info['Employee']['id'])));
            if (!$checkLike) {
                $this->ZogMsgLike->create();
                $success = (boolean) $this->ZogMsgLike->save($saved);
                $this->ZogMsg->id = $_POST['zog_msg_id'];
                $this->ZogMsg->saveField("count_like", (int) $zogMsg['ZogMsg']['count_like'] + 1);
                $array['id'] = $_POST['zog_msg_id'];
                $array['count_like'] = (int) $zogMsg['ZogMsg']['count_like'] + 1;
                die(json_encode($array));
            }
        }
        die(json_encode($success));
    }

    public function time($time) {
        $ago = strtotime(date('Y-m-d H:i:s', time())) - strtotime($time);
        // duoi 1 phut
        if ($ago < 60) {
            return '1 ' . sprintf(__('cmMinute', true));
        }
        // tu 1 phut den 59 phut
        if ($ago >= 60 and $ago < 3600) {
            return floor($ago / 60) . ' ' . sprintf(__('cmMinutes', true));
        }
        // tu 1 gio den 2 gio
        if ($ago >= 3600 and $ago < 7200) {
            return '1 ' . sprintf(__('cmHour', true));
        }
        // tu 2 gio den 23 gio 
        if ($ago >= 7200 and $ago < 24 * 3600) {
            return floor($ago / 3600) . ' ' . sprintf(__('cmHours', true));
        }
        // tu 1 ngay den 2 ngay
        if ($ago >= 24 * 3600 and $ago < 24 * 2 * 3600) {
            return '1 ' . sprintf(__('cmDay', true));
        }
        // tu 2 ngay den 31 ngay
        if ($ago >= 2 * 24 * 3600 and $ago < 31 * 24 * 3600) {
            return floor($ago / (24 * 3600)) . ' ' . sprintf(__('cmDays', true));
        }
        // tu 1 thang den 2 thang
        if ($ago >= 31 * 24 * 3600 and $ago < 31 * 24 * 3600 * 2) {
            return '1 ' . sprintf(__('cmMonth', true));
        }
        // tu 2 thang den 12 thang
        if ($ago >= 31 * 24 * 3600 and $ago < 365 * 24 * 3600) {
            return floor($ago / (31 * 24 * 3600)) . ' ' . sprintf(__('cmMonths', true));
        }
        // tu 1 nam den 2 nam
        if ($ago >= 365 * 24 * 3600 and $ago < 365 * 24 * 3600 * 2) {
            return '1 ' . sprintf(__('cmYear', true));
        }
        // tu 2 nam tro len
        if ($ago > 365 * 24 * 3600) {
            return floor($ago / (365 * 24 * 3600)) . ' ' . sprintf(__('cmYears', true));
        }
    }

    public function getTest() {
        $comment = $this->ZogMsg->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'id' => 13
            ),
        ));
        die(json_encode($data));
    }

}