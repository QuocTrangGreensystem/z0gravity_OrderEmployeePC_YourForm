<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class PagesController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'Pages';

    /**
     * Default helper
     *
     * @var array
     * @access public
     */
    var $helpers = array('Html');
    
    var $layout = 'default_z0g';

    /**
     * This controller does not use a model
     *
     * @var array
     * @access public
     */
    var $uses = array();

    function notice() {
        echo '<meta http-equiv="refresh" content="120" />';
        exit(0);
    }

    function cleanup() {
        foreach (array(
    CACHE . 'views' . DS,
    CACHE . 'models' . DS,
    CACHE . 'persistent' . DS,
    TMP . 'uploads' . DS,
    TMP . 'uploads' . DS . 'Employee' . DS) as $path) {

            $normalFiles = glob($path . '*');
            $hiddenFiles = glob($path . '\.?*');

            $normalFiles = $normalFiles ? $normalFiles : array();
            $hiddenFiles = $hiddenFiles ? $hiddenFiles : array();

            $files = array_merge($normalFiles, $hiddenFiles);
            if (is_array($files)) {
                foreach ($files as $file) {
                    if (preg_match('/(\.|\.\.)$/', $file)) {
                        continue;
                    }
                    if (is_file($file) === true) {
                        @unlink($file);
                    }
                }
            }
        }
        if( !empty($this->params['requested']) )return;
        $this->Session->setFlash(__('All Cache has been cleanup.', true), "success");
        return $this->redirect('/');
    }

    function display() {
        $path = func_get_args();

        $count = count($path);
        if (!$count) {
            $this->redirect('/');
        }
        $page = $subpage = $title_for_layout = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        if (!empty($path[$count - 1])) {
            $title_for_layout = Inflector::humanize($path[$count - 1]);
        }
        $this->set(compact('page', 'subpage', 'title_for_layout'));
        $this->render(implode('/', $path));
    }

    function index() {
        list($seeMenuAudit, $seeMenuBusiness) = $this->_checkPermissionMenuAudit();
        $this->Session->write('seeMenuAudit', $seeMenuAudit);
        $this->Session->write('seeMenuBusiness', $seeMenuBusiness);
        list($enablePMS, $enableRMS, $enableAudit, $enableReport, $enableBusines, $enableZogMsgs) = $this->_enableModule();
        $this->Session->write('enablePMS', $enablePMS);
        $this->Session->write('enableRMS', $enableRMS);
        $this->Session->write('enableAudit', $enableAudit);
        $this->Session->write('enableReport', $enableReport);
        $this->Session->write('enableBusines', $enableBusines);
        $this->Session->write('enableZogMsgs', $enableZogMsgs);
        $this->set(compact('seeMenuAudit', 'enablePMS', 'enableRMS', 'enableAudit', 'enableReport', 'enableBusines', 'seeMenuBusiness', 'enableZogMsgs'));

        if ($this->Session->check("Auth.Employee")) {
            $fullname = $this->Session->read("Auth.Employee.first_name") . " " . $this->Session->read("Auth.Employee.last_name");
            $this->set("fullname", $fullname);
        } else {
            $this->redirect("/login");
        }
    }

    function test() {
        $this->layout = "test_layout";
    }

    function error(){

    }
}
?>
