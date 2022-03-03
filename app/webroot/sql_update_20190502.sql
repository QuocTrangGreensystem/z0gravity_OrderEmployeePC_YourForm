/* Demove duplicate for history_filters table*/
/* Update by Huynh 2018-05-02 */

/* PHẦN NÀY TRÙNG VỚI GÓI SQL_UPDATE_20181027.SQL NÊN REMOVE ĐỂ TRÁNH DUPLICATE */

DELETE t1 FROM history_filters t1
	INNER JOIN history_filters t2 
WHERE
    (t1.updated < t2.updated AND t1.path = t2.path and t1.employee_id = t2.employee_id)
	OR
	(t1.updated = t2.updated AND t1.path = t2.path and t1.employee_id = t2.employee_id and t1.id > t2.id);