/*
	Created by Viet
	Ticket #1101
*/

ALTER TABLE `employees`
ADD COLUMN `update_by` int(10) DEFAULT NULL,
ADD COLUMN `last_login` int(10) DEFAULT NULL;