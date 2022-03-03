/*	Created by TrungVV
	Notify Token
*/

ALTER TABLE `ticket_metas`
ADD COLUMN `enable_for_customer` tinyint(1) DEFAULT '0' AFTER `company_id`,
ADD COLUMN `category` int(1) NULL DEFAULT 0 AFTER `enable_for_customer`;

ALTER TABLE `externals`
ADD COLUMN `limit_period` date DEFAULT NULL AFTER `company_id`,
ADD COLUMN `limit_support` int(9) DEFAULT '0' AFTER `limit_period`,
ADD COLUMN `limit_formation` int(9) DEFAULT '0' AFTER `limit_support`,
ADD COLUMN `limit_coaching` int(9) DEFAULT '0' AFTER `limit_formation`;