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
