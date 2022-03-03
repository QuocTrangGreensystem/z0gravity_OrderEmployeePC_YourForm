
CREATE TABLE `project_issue_employee_refers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `project_issue_id` int(10) DEFAULT NULL,
  `reference_id` int(10) DEFAULT NULL,
  `is_profit_center` int(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;
