CREATE TABLE if not EXISTS `project_dashboards` (
`id`  int(10) NOT NULL AUTO_INCREMENT,
`employee_id`  int(10) NOT NULL ,
`company_id`  int(10) NOT NULL ,
`dashboard_data`  text NULL ,
`share_type`  varchar(255) NOT NULL DEFAULT 'nobody',
`created`  int(11) NULL ,
`updated`  int(11) NULL ,
PRIMARY KEY (`id`)
);
CREATE TABLE if not EXISTS `project_dashboard_shares` (
`id`  int(10) NOT NULL AUTO_INCREMENT,
`employee_id`  int(10) NULL,
`dashboard_id`  int(10) NOT NULL ,
`updated`  int(11) NULL ,
PRIMARY KEY (`id`)
);
CREATE TABLE if not EXISTS `project_dashboard_actives` (
`id`  int(10) NOT NULL AUTO_INCREMENT,
`employee_id`  int(10) NULL,
`dashboard_id`  int(10) NOT NULL ,
`updated`  int(11) NULL ,
PRIMARY KEY (`id`)
);