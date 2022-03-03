CREATE TABLE `project_budget_purchases` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `project_id` int(10) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `budget_customer_id` int(10) DEFAULT NULL,
  `order_date` date DEFAULT NULL,
  `sold` decimal(10,2) DEFAULT NULL,
  `man_day` decimal(10,2) DEFAULT NULL,
  `reference` varchar(128) DEFAULT NULL,
  `reference2` varchar(128) DEFAULT NULL,
  `file_attachement` text,
  `format` int(10) DEFAULT NULL,
  `justification` text,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `activity_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `project_budget_purchase_invoices` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `project_id` int(10) DEFAULT NULL,
  `project_budget_purchase_id` int(10) DEFAULT NULL,
  `name_invoi` varchar(128) DEFAULT NULL,
  `billed` decimal(10,2) DEFAULT NULL,
  `paid` decimal(10,2) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `effective_date` date DEFAULT NULL,
  `reference` varchar(128) DEFAULT NULL,
  `reference2` varchar(128) DEFAULT NULL,
  `file_attachement` text,
  `format` int(10) DEFAULT NULL,
  `justification` varchar(128) DEFAULT NULL,
  `activity_id` int(10) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;