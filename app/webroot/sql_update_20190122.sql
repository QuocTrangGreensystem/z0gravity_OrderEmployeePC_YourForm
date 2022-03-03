/* 
	Created by VN
	Delete record request has activity_id not exists
*/

DELETE FROM `activity_requests` WHERE task_id is null and activity_id not in (select id from activities);