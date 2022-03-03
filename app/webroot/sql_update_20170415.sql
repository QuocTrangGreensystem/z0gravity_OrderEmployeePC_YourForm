ALTER TABLE `project_issue_severities`
ADD COLUMN `color`  varchar(1024) NULL;
ALTER TABLE `project_issues`
ADD COLUMN `delivery_date`  date NULL;
