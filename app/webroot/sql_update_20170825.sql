CREATE TABLE `profile_project_manager_details` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) DEFAULT NULL,
  `company_id` int(10) DEFAULT NULL,
  `name_eng` varchar(100) DEFAULT NULL,
  `name_fre` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `model_id` int(10) DEFAULT NULL,
  `controllers` varchar(255) DEFAULT NULL,
  `functions` varchar(255) DEFAULT NULL,
  `display` int(10) DEFAULT NULL,
  `weight` int(10) DEFAULT NULL,
  `default_screen` int(10) DEFAULT NULL,
  `widget_id` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE `profile_project_managers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `company_id` int(10) DEFAULT NULL,
  `profile_name` varchar(255) DEFAULT NULL,
  `create_resource` int(1) DEFAULT NULL,
  `can_create_project` int(1) DEFAULT NULL,
  `can_delete_project` int(1) DEFAULT NULL,
  `can_change_status_project` int(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `up` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;
