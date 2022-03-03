CREATE TABLE `project_task_favourites` (
`id`  int(10) NOT NULL AUTO_INCREMENT ,
`task_id`  int(10) NOT NULL COMMENT 'project task id',
`employee_id`  int(10) NOT NULL ,
`favourite`  tinyint NOT NULL DEFAULT 0 ,
`company_id`  int(10) NULL ,
`project_id`  int(10) NULL ,
`created`  int(10) NOT NULL ,
`updated`  int(10) NOT NULL ,
PRIMARY KEY (`id`)
);