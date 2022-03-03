/*
	Created by Viet
	Ticket #1082
*/

CREATE TABLE `project_manager_update_fields` (
  `id` int(10) AUTO_INCREMENT NOT NULL ,
  `field` varchar(255) NOT NULL,
  `company_id` int(10) DEFAULT NULL,
  `employee_id` int(10) NOT NULL,
  `created` int(10) DEFAULT NULL,
  `updated` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
)DEFAULT CHARSET=utf8;