ALTER TABLE `project_livrable_categories`
ADD COLUMN `livrable_icon`  varchar(124) NULL AFTER `livrable_cat`;

ALTER TABLE `project_livrables`
ADD COLUMN `employee_id_upload`  int(10) NULL AFTER `weight`,
ADD COLUMN `version`  varchar(2048) NULL AFTER `employee_id_upload`;