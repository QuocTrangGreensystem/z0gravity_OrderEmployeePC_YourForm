/*
	Created by Dai Huynh
	Change type of `value` field from xx.xxxxxxxxxx to xxxxxxxxx.xxx
*/
ALTER TABLE activity_requests MODIFY `value` decimal(12,9) DEFAULT '0';