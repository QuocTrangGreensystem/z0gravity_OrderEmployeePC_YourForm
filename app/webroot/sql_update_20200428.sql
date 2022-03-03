/* Update by QuanNV
 * 28/04/2020
 * #607
 */
ALTER TABLE `project_milestones`
ADD COLUMN `initial_date` int(10) DEFAULT NULL after `project_milestone`;