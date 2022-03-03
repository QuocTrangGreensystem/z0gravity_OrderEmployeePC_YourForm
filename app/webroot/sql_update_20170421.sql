CREATE TABLE `expectation_colors` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `company_id` int(10) DEFAULT NULL,
  `color` varchar(1024) DEFAULT NULL,
  `key` varchar(255) DEFAULT NULL,
  `display` int(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

ALTER TABLE `project_expectations`
ADD COLUMN `assigned_to_1`  int(10) NULL AFTER `milestone`,
ADD COLUMN `assigned_to_2`  int(10) NULL AFTER `assigned_to_1`,
ADD COLUMN `assigned_to_3`  int(10) NULL AFTER `assigned_to_2`;

ALTER TABLE `project_expectations`
ADD COLUMN `list_color_1`  int(10) NULL AFTER `assigned_to_3`,
ADD COLUMN `list_color_2`  int(10) NULL AFTER `list_color_1`,
ADD COLUMN `list_color_3`  int(10) NULL AFTER `list_color_2`,
ADD COLUMN `list_color_4`  int(10) NULL AFTER `list_color_3`,
ADD COLUMN `list_color_5`  int(10) NULL AFTER `list_color_4`;

