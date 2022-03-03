/*
	Created by Dai Huynh
	Email: Z0G 28/1/2019 Last actions before the TET
	New widget status
	Add fields for project amrs
*/
ALTER table `project_amrs`
add column `scope_weather` varchar(50) DEFAULT NULL,
add column `schedule_weather` varchar(50) DEFAULT NULL,
add column `budget_weather` varchar(50) DEFAULT NULL,
add column `resources_weather` varchar(50) DEFAULT NULL,
add column `technical_weather` varchar(50) DEFAULT NULL;