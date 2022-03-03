drop table if exists `project_detail_employee_settings`;
CREATE TABLE if not EXISTS `project_detail_employee_settings` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`translation_setting_id` int(11) NOT NULL,
`employee_id` int(11) NOT NULL,
`field_display` tinyint NULL DEFAULT 1,
`block_display` tinyint NULL DEFAULT 1,
`updated` int(10) NULL,
PRIMARY KEY (`id`)
) AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;