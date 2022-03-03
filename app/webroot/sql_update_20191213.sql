/* Update by Huynh LE
* Ticket #517 2019-12-13
* #517 add fields in activity_requests table
* email Ticket #517 «EVOLUTION» «add fields in activity_requests table» «EN COURS DE DEV» «Developer» «z0 Gravity»
*/
ALTER TABLE `activity_requests`
ADD COLUMN `price_resource`  float(8,2) NOT NULL DEFAULT 0 AFTER `manager_by`,
ADD COLUMN `price_team`  float(8,2) NOT NULL DEFAULT 0 AFTER `price_resource`,
ADD COLUMN `price_profil`  float(8,2) NOT NULL DEFAULT 0 AFTER `price_team`,
ADD COLUMN `cost_price_resource`  float(8,2) NOT NULL DEFAULT 0 AFTER `price_profil`,
ADD COLUMN `cost_price_team`  float(8,2) NOT NULL DEFAULT 0 AFTER `cost_price_resource`,
ADD COLUMN `cost_price_profil`  float(8,2) NOT NULL DEFAULT 0 AFTER `cost_price_team`;