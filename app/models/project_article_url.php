<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectArticleUrl extends AppModel {
	public $recursive = -1;
	var $belongsTo = array(
        'ProjectArticle' => array(
            'className' => 'ProjectArticle',
            'foreignKey' => 'article_id ',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
	);
}
?>