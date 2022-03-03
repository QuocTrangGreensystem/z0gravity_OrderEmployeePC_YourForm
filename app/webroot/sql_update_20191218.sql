/* Update by Huynh 
 * 18/12/2019
 * #517
 */
ALTER TABLE `activity_requests`
ADD COLUMN `employee_name`  varchar(255) NULL AFTER `cost_price_profil`,
ADD COLUMN `profit_center_name`  varchar(255) NULL AFTER `employee_name`;
/* END #517 */