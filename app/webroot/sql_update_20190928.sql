/* Update by Huynh LE
* 28-09-2019
* #458 Add 2 field interger (X.XX)
*/

ALTER TABLE `project_phases`
ADD COLUMN `tjm`  float(8,2) NULL AFTER `color`;
ALTER TABLE `profit_centers`
ADD COLUMN `tjm`  float(8,2) NULL AFTER `analytical`;
