CREATE TABLE `project_issue_colors` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `company_id` int(10) DEFAULT NULL,
  `color` varchar(1024) DEFAULT NULL,
  `default` int(1) DEFAULT NULL,
  `display` int(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE `project_expectation_employee_refers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `project_expectation_id` int(10) DEFAULT NULL,
  `key` varchar(255) DEFAULT NULL,
  `reference_id` int(10) DEFAULT NULL,
  `is_profit_center` int(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

ALTER TABLE `project_issues`
ADD COLUMN `project_issue_color_id`  int(10) NULL AFTER `delivery_date`;

ALTER TABLE `project_issue_statuses`
ADD COLUMN `status`  int(1) NULL AFTER `company_id`;

