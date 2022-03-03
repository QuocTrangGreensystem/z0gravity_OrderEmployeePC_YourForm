/* 
By VNGUYEN
Ticket 364
*/
CREATE TABLE `company_default_settings` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `company_id` int(10) NOT NULL,
  `df_key` text  NOT NULL,
  `df_value` text  NOT NULL,
  `employee_updated` int(10) NOT NULL,
  `created` int(10) DEFAULT NULL,
  `updated` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
)DEFAULT CHARSET=utf8;