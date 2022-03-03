/*
	Ticket #615
	Update by Huynh Le
	Add column for method 4: save progress manual
*/
ALTER TABLE `projects`
ADD COLUMN `manual_progress` float not NULL DEFAULT 0 AFTER `company_id`;

