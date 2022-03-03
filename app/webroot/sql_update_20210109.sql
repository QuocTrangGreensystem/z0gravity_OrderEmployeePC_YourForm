/*
	Created by VietNguyen
	Ticket #970
*/

ALTER TABLE `employees`
ADD COLUMN `can_see_forecast`  tinyint(4) NULL DEFAULT 0 AFTER `can_communication`;