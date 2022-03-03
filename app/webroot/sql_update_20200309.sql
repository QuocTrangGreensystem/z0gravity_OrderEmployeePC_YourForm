-- 2020/03/09
-- Created by Viet Nguyen
-- Ticket #533 Finance+ Screen 

ALTER TABLE `project_finance_plus_dates`
ADD COLUMN `finaninv_start` int(10) DEFAULT NULL,
ADD COLUMN `finaninv_end` int(10) DEFAULT NULL,
ADD COLUMN `finanfon_start` int(10) DEFAULT NULL,
ADD COLUMN `finanfon_end` int(10) DEFAULT NULL;