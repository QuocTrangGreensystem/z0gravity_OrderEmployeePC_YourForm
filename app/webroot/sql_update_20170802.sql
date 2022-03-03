CREATE TABLE `project_finance_two_pluses` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `project_id` int(10) DEFAULT NULL,
  `company_id` int(10) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `project_finance_two_plus_details` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `project_id` int(10) DEFAULT NULL,
  `project_finance_two_plus_id` int(10) DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `budget_initial` decimal(16,2) DEFAULT NULL,
  `budget_revised` decimal(16,2) DEFAULT NULL,
  `last_estimated` decimal(16,2) DEFAULT NULL,
  `engaged` decimal(16,2) DEFAULT NULL,
  `bill` decimal(16,2) DEFAULT NULL,
  `disbursed` decimal(16,2) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `project_finance_two_plus_dates` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `project_id` int(10) DEFAULT NULL,
  `start` int(10) DEFAULT NULL,
  `end` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;