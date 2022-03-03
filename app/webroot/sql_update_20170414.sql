
CREATE TABLE `project_alerts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `alert_name` varchar(255) DEFAULT NULL,
  `company_id` int(10) DEFAULT NULL,
  `display` int(2) DEFAULT NULL,
  `number_of_day` int(10) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
)DEFAULT CHARSET=utf8;
