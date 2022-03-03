CREATE TABLE `colors` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) DEFAULT NULL,
  `company_id` int(10) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `header_color` varchar(255) DEFAULT NULL,
  `line_color` varchar(255) DEFAULT NULL,
  `table_color` varchar(255) DEFAULT NULL,
  `popup_color` varchar(255) DEFAULT NULL,
  `kpi_color` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
)DEFAULT CHARSET=utf8;


ALTER TABLE `colors`
ADD COLUMN `tab_color`  varchar(255) NULL,
ADD COLUMN `tab_selected` varchar(255) NULL;

ALTER TABLE `colors`
ADD COLUMN `page_color`  varchar(255) NULL,
ADD COLUMN `button_color` varchar(255) NULL;

ALTER TABLE `colors`
ADD COLUMN `tab_hover`  varchar(255) NULL;


ALTER TABLE `colors`
ADD COLUMN `attachment`  varchar(255) NOT NULL,
ADD COLUMN `is_file`  tinyint(1) DEFAULT '1',
ADD COLUMN `is_https`  tinyint(1) DEFAULT '1';

ALTER TABLE `colors`
ADD COLUMN `attachment_background`  varchar(255) NOT NULL;

ALTER TABLE `colors`
ADD COLUMN `header_image`  varchar(255) NOT NULL;

ALTER TABLE `colors`
ADD COLUMN `is_new_design`  tinyint(1) DEFAULT '0';

ALTER TABLE `translation_settings`
ADD COLUMN `next_line`  tinyint(4) DEFAULT '0';

ALTER TABLE `employees`
ADD COLUMN `is_enable_new_design`  tinyint(1) DEFAULT '0';

CREATE TABLE `project_task_attachments` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `project_id` int(10) DEFAULT NULL,
  `task_id` int(10) DEFAULT NULL,
  `employee_id` int(10) DEFAULT NULL,
  `attachment` varchar(255) NOT NULL,
  `created` int(10) DEFAULT NULL,
  `updated` int(10) DEFAULT NULL,
  `is_file` tinyint(1) DEFAULT '1',
  `is_https` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
)DEFAULT CHARSET=utf8;

CREATE table project_created_vals_comments(
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `company_id` int(10) DEFAULT NULL,
  `project_id` int(10) DEFAULT NULL,
  `type_value` varchar(100) DEFAULT NULL,
  `employee_id` int(10) DEFAULT NULL,
  `comment` varchar(300) DEFAULT NULL,
  `created` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
)DEFAULT CHARSET=utf8;

CREATE table user_last_updateds(
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `company_id` int(10) NOT NULL,
  `employee_id` int(10) NOT NULL,
  `path` varchar(300) DEFAULT NULL,
  `action` varchar(300) DEFAULT NULL,
  `created` int(10) DEFAULT NULL,
  `updated` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
)DEFAULT CHARSET=utf8;

CREATE table project_indicator_settings(
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `company_id` int(10) NOT NULL,
  `employee_id` int(10) NOT NULL,
  `widget_setting` varchar(2000) DEFAULT NULL,
  `created` int(10) DEFAULT NULL,
  `user_last_updated` int(10) NULL,
  `updated` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
)DEFAULT CHARSET=utf8;