/* Update by Viet 
 * 09/01/2020
 * #526
 */
ALTER TABLE `project_budget_internal_details`
ADD COLUMN `budget_erro` decimal(10,2) DEFAULT NULL AFTER `budget_md`;
