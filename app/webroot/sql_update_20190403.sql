/* 
By QuanNV
Ticket 358 Item create screen set default width column.
*/
CREATE TABLE `company_column_defaults` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `column_name` text DEFAULT NULL,
  `default_column` text DEFAULT NULL,
  `default_width` int(10) DEFAULT NULL,
  `company_id` int(10) NOT NULL,
  `created` int(10) DEFAULT NULL,

  PRIMARY KEY (`id`),
  CONSTRAINT fk_company_id
  FOREIGN KEY (`company_id`)
  REFERENCES companies (`id`)
)DEFAULT CHARSET=utf8;