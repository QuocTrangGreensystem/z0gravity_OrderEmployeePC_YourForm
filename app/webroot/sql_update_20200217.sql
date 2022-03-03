/* Update by Viet 
 * 17/02/2020
 * #539
 */
ALTER TABLE `project_sub_types`
ADD COLUMN `parent_id`  varchar(255) NULL AFTER `id`;