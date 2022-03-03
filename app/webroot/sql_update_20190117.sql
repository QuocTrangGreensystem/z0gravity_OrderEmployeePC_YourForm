/*
	Created by Dai Huynh
	Add Flag is_timesheet_msg to save week message
	Email Z0G 15/1/2019: Feedback issue message activity
*/
ALTER table `activity_forecast_comments`
add column `is_timesheet_msg` tinyint(1) DEFAULT 0;

/*
	Add column save week_mesage
*/
ALTER table `tmp_module_activity_exports`
add column `week_message` text DEFAULT NULL;