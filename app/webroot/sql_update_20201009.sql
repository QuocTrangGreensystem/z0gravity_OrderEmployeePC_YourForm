ALTER TABLE `project_phases`
ADD COLUMN `add_when_create_project` INT(10) DEFAULT '0' AFTER `updated`;