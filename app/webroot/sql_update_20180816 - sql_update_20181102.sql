ALTER TABLE `project_risk_occurrences`
ADD `value_risk_occurrence` INT NULL;

UPDATE `project_risk_occurrences`
SET `value_risk_occurrence` = 0
WHERE `risk_occurrence` LIKE '%fai%' or `risk_occurrence` LIKE '%low%' or `risk_occurrence` LIKE '%small%';


UPDATE `project_risk_occurrences`
SET `value_risk_occurrence` = 1
WHERE `risk_occurrence` LIKE '%moy%' or `risk_occurrence` LIKE '%ave%' or `risk_occurrence` LIKE '%med%' or `risk_occurrence` LIKE '%mid%';


UPDATE `project_risk_occurrences`
SET `value_risk_occurrence` = 2
WHERE `risk_occurrence` LIKE '%for%' or `risk_occurrence` LIKE '%strong%' or `risk_occurrence` LIKE '%high%';

ALTER TABLE `project_risk_severities`
ADD `value_risk_severitie` INT NULL;

UPDATE `project_risk_severities`
SET `value_risk_severitie` = 0
WHERE `risk_severity` LIKE '%fai%' or `risk_severity` LIKE '%low%' or `risk_severity` LIKE '%small%';


UPDATE `project_risk_severities`
SET `value_risk_severitie` = 1
WHERE `risk_severity` LIKE '%moy%' or `risk_severity` LIKE '%ave%' or `risk_severity` LIKE '%med%' or `risk_severity` LIKE '%mid%';


UPDATE `project_risk_severities`
SET `value_risk_severitie` = 2
WHERE `risk_severity` LIKE '%for%' or `risk_severity` LIKE '%strong%' or `risk_severity` LIKE '%high%';

/* Update by huynh 2018-10-17 */
ALTER TABLE `employees`
ADD `avatar_color` varchar(10) NULL; 

/* Update by huynh 2018-10-22 */
ALTER TABLE `project_images`
ADD `thumbnail` varchar(255) NULL; 

/* Update by Huynh 2018-10-27 */
DELETE t1 FROM history_filters t1
	INNER JOIN history_filters t2 
WHERE
    (t1.updated < t2.updated AND t1.path = t2.path and t1.employee_id = t2.employee_id)
	OR
	(t1.updated = t2.updated AND t1.path = t2.path and t1.employee_id = t2.employee_id and t1.id > t2.id);

/* Remove chuỗi https://nextversion.z0gravity.com/ for history_filters table*/
/* Update by Huynh 2018-11-02 */
UPDATE history_filters set params =  REPLACE(history_filters.params, 'https://nextversion.z0gravity.com','') where path='rollback_url_employee_when_login';