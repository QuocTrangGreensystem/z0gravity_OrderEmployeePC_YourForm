/* 
By QuanNV
Ticket 358 Item create screen set default width column.
*/

DROP TABLE `company_column_defaults`;

CREATE TABLE `company_column_defaults` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `column_name` text DEFAULT NULL,
  `field_name` text DEFAULT NULL,
  `width` int(10) DEFAULT NULL,
  `company_id` int(10) NOT NULL,
  `created` int(10) DEFAULT NULL,
  `updated` int(10) DEFAULT NULL,
  `update_by_employee` int(10) DEFAULT NULL,

  PRIMARY KEY (`id`),
  CONSTRAINT fk_company_id
  FOREIGN KEY (`company_id`)
  REFERENCES companies (`id`)
)DEFAULT CHARSET=utf8;