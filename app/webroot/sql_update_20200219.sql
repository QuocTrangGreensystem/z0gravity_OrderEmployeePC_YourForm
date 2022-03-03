/* Update by Viet 
 * 19/02/2020
 * #539
 */
ALTER TABLE `projects`
ADD COLUMN `project_sub_sub_type_id` int(11) NULL AFTER `project_sub_type_id`;