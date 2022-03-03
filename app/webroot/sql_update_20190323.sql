/* 
By dai Huynh
Ticket 357
*/
CREATE TABLE `employee_default_profiles` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `company_id` int(10) NOT NULL,
  `df_value` text  NOT NULL,
  `employee_id` int(10) NOT NULL,
  `created` int(10) DEFAULT NULL,
  `updated` int(10) DEFAULT NULL,

  PRIMARY KEY (`id`)
)DEFAULT CHARSET=utf8;