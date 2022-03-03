/* Update by Huynh LE #1093
* Add column update_by */

ALTER TABLE `activity_forecast_comments` ADD COLUMN `update_by`  int(10) NULL AFTER `updated`;
UPDATE `activity_forecast_comments` set activity_forecast_comments.update_by = activity_forecast_comments.employee_id WHERE update_by is null;