<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ControllerListComponent extends Object {
   /* function get() {
        $paths = array();
        $controllerClasses = Configure::listObjects('controller', &$paths);

        $includePaths = explode(':', ini_get('include_path'));
        ini_set('include_path', implode(':', array_merge(array_diff($paths, $includePaths), $includePaths)));

        foreach($controllerClasses as $controller) {
            if ($controller != 'App') {
                $fileName = Inflector::underscore($controller).'_controller.php';pr($fileName);
                //require_once("../".$fileName);
                App::import('Controller', $fileName);
                $className = $controller . 'Controller';
                $actions = get_class_methods($className);//pr($actions);
                foreach($actions as $k => $v) {
                    if ($v{0} == '_') {
                        unset($actions[$k]);
                    }
                }
                $parentActions = get_class_methods('AppController');
                $controllers[$controller] = array_diff($actions, $parentActions);
            }
        }

        return $controllers;
    }*/
	function get() {
        App::import('File');
        $Folder = $this->folder = new Folder;
        $Folder->path = APP.'controllers'.DS;
        $controllers = $this->folder->read();
        foreach ($controllers['1'] AS $c) {
            $cName = Inflector::camelize(str_replace('_controller.php', '', $c));
            $controllerPaths[$cName] = APP.'controllers'.DS.$c;
        }

        $controllers = array();
	    foreach($controllerPaths as $path) {
	        $Folder->cd($path);
	        $controllerFiles = $Folder->find('.+_controller\.php$');
	        $controllers = am($controllers, array_map(array(&$this, '__controllerize'), $controllerFiles));
	    }
		$controllers = array_unique($controllers);
        
 		foreach($controllers as $key => $controller) {
             if ($controller != 'App' && $controller != 'Pages') {
                $fileName = Inflector::underscore($controller).'_controller.php';
                require_once(CONTROLLERS.$fileName);
                $className = $controller . 'Controller';
                $actions = get_class_methods($className);
                foreach($actions as $k => $v) {
                    if ($v{0} == '_') {
                        unset($actions[$k]);
                    }
                }
                $parentActions = get_class_methods('AppController');
                $controllers[$controller] = array_diff($actions, $parentActions);
            }
            unset($controllers[$key]);
 		}
       return $controllers;
	}

	function __controllerize($file) {
	    return Inflector::camelize(r('_controller.php', '', $file));
	}

}
?>