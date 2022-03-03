/*
	Created by Viet Nguyen
*/
ALTER table `tmp_module_activity_exports`
add column `profit_center_id` varchar(25) DEFAULT NULL,
add column `project_name` varchar(255) DEFAULT NULL,
add column `project_program` varchar(255) DEFAULT NULL,
add column `phase_name` varchar(255) DEFAULT NULL,
add column `task_name` varchar(255) DEFAULT NULL,
add column `project_code_1` varchar(255) DEFAULT NULL,
add column `tjm` varchar(15) DEFAULT NULL,
add column `message` varchar(500) DEFAULT NULL;