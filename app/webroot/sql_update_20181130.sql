/* Remove column employee_id */
ALTER TABLE colors
drop column employee_id;

/* Remove column is_new_design */
ALTER TABLE colors
drop column is_new_design;

/* Delete duplicate company_id */
DELETE t1 FROM colors t1
	INNER JOIN colors t2 
WHERE
  (t1.company_id = t2.company_id AND t1.id > t2.id and t2.color is not null)
	OR
	(t1.company_id = t2.company_id AND t1.id < t2.id and t1.color is null);
	
/* Delete empty field */
DELETE FROM `colors`
WHERE
  company_id is NULL
AND
 attachment='';