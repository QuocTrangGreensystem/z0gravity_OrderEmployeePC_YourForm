/*
	Created by VietNguyen
	Ticket #1031
*/

CREATE TABLE `customer_logos` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `logo_name` varchar(255) DEFAULT NULL,
  `company_id` int(10) DEFAULT NULL,
  `created` int(10) DEFAULT NULL,
  `updated` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
)DEFAULT CHARSET=utf8;


ALTER TABLE `employees`
ADD COLUMN `logo_id`  int(10) NULL DEFAULT null AFTER `can_see_forecast`;