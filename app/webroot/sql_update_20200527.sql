/*
	Ticket #641
	Update by Viet Nguyen.
	For all PRICE* field in project, authorize 999 999 999 999.99
*/

ALTER TABLE `projects`
MODIFY `price_7` decimal(15, 2),
MODIFY `price_8` decimal(15, 2),
MODIFY `price_9` decimal(15, 2),
MODIFY `price_10` decimal(15, 2),
MODIFY `price_11` decimal(15, 2),
MODIFY `price_12` decimal(15, 2),
MODIFY `price_13` decimal(15, 2),
MODIFY `price_14` decimal(15, 2),
MODIFY `price_15` decimal(15, 2),
MODIFY `price_16` decimal(15, 2);