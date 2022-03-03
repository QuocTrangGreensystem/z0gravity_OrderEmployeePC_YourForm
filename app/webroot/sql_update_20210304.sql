/*
	Created by Viet
	Ticket #1033
*/
ALTER TABLE `companies`
ADD COLUMN `day_alert_billing` int(10) DEFAULT NULL,
ADD COLUMN `day_licence` int(10) DEFAULT NULL,
ADD COLUMN `actif_max` int(6) DEFAULT NULL,
ADD COLUMN `no_add_more_max` int(1) DEFAULT 0,
ADD COLUMN customer_email TEXT DEFAULT NULL;