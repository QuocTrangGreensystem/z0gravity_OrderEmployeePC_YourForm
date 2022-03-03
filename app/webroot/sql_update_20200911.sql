Drop TABLE IF EXISTS `project_articles`;
Drop TABLE IF EXISTS `project_article_urls`;
delete from `menus` where menus.controllers = 'project_articles';
CREATE TABLE IF NOT EXISTS `project_communications` (
`id`  int(11) NOT NULL AUTO_INCREMENT,
`project_id`  int(11) NOT NULL ,
`communication_title`  varchar(255) NULL ,
`sub_title`  varchar(255) NULL ,
`image`  varchar(255) NULL ,
`start_date`  date NULL ,
`end_date`  date NULL ,
`status`  tinyint(1) NOT NULL DEFAULT 0 ,
`status_text`  varchar(255) NULL ,
`content`  longtext NULL ,
`custom_color`  varchar(25) NULL,
`public_date` date NULL ,
`publisher`  varchar(255) NOT NULL ,
`published`  tinyint(1) NOT NULL DEFAULT 0,
`updated`  int(10) NOT NULL ,
`employee_updated`  int(11) NOT NULL ,
PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS `project_communication_urls` (
`id`  int(11) NOT NULL AUTO_INCREMENT,
`communication_id`  int(11) NOT NULL ,
`url` varchar(255) NULL ,
`descriptions`  varchar(255) NULL ,
`updated`  int(10) NOT NULL ,
PRIMARY KEY (`id`)
);