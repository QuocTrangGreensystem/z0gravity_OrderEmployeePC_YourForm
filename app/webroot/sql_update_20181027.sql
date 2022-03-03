/* Phần này được lưu chung trong file sql_update_20180816.sql nên không cần update nếu đã update trước đó */
/* Demove duplicate for history_filters table*/
/* Update by Huynh 2018-10-27 */
DELETE t1 FROM history_filters t1
	INNER JOIN history_filters t2 
WHERE
    (t1.updated < t2.updated AND t1.path = t2.path and t1.employee_id = t2.employee_id)
	OR
	(t1.updated = t2.updated AND t1.path = t2.path and t1.employee_id = t2.employee_id and t1.id > t2.id);