/*
	FUNCTION GET FULLNAME EMPLOYEE
*/

DELIMITER $$
CREATE FUNCTION F_FullName(employee_id int(9)) 
RETURNS VARCHAR(50)
BEGIN
	SET @employee_id = employee_id;
	return (SELECT CONCAT(first_name, ' ', last_name) FROM `employees` WHERE id = @employee_id);
END$$
DELIMITER ;

/* 
	GET DATA PROJECT 
*/

DELIMITER $$
CREATE PROCEDURE pr_GetFieldProject(IN pr_project_id int(9), IN pr_field_name VARCHAR(50))
BEGIN
  SET @pr_project_id = pr_project_id;
  SET @pr_field_name = pr_field_name;
  SET @sql_string = concat('SELECT (',@pr_field_name,') INTO @result FROM `projects` WHERE id = ',@pr_project_id);
  PREPARE stmt FROM @sql_string;
  EXECUTE stmt;
  DEALLOCATE PREPARE stmt;
END$$
DELIMITER ;

/*
	STORE PROCDURE GET FULL NAME OF EMPLOYEE
*/

DELIMITER $$
CREATE PROCEDURE pr_EmployeeManager(IN project_id INT(9), IN employee_type VARCHAR(50))
BEGIN
  SET @project_id = project_id;
  SET @employee_type = employee_type;
  CALL pr_GetFieldProject(@project_id, @employee_type);
  SET @sql_string = CONCAT('SELECT F_FullName(', @result, ')');
  PREPARE stmt FROM @sql_string;
  EXECUTE stmt;	
  DEALLOCATE PREPARE stmt;
END$$
DELIMITER ;

/*
	PROCDURE GET DATA LIST
	JOIN PROJECTS WITH PROJECT DATASETS
*/

DELIMITER $$
CREATE PROCEDURE pr_GetFieldListProject(IN pr_project_id int(9), IN pr_field_list VARCHAR(50))
BEGIN
  SET @pr_project_id = pr_project_id;
  SET @pr_field_list = pr_field_list;
  CALL pr_GetFieldProject(@pr_project_id, @pr_field_list);
  SET @sql_string = concat('SELECT name FROM `project_datasets` WHERE `project_datasets`.id = ',@result);
  PREPARE stmt FROM @sql_string;
  EXECUTE stmt;
  DEALLOCATE PREPARE stmt;
END$$
DELIMITER ;


/* FUNCTION FORMAT PROFIT CENTER NAME */
DELIMITER $$
CREATE function f_PCName(pc_name varchar(50))
RETURNS varchar(50)
BEGIN 
	SET @pc_name = pc_name;
	return CONCAT('PC/', @pc_name);
END$$
DELIMITER ;

/* FUNCTION GET DATA EMPLOYEE ASSIGN TO IN A TASK*/

DELIMITER $$
CREATE function f_ResourceAssign(task_id INT(20))
RETURNS varchar(250)
BEGIN 
	DECLARE result varchar(250);
	SET @task_id = task_id;
	SET @result = result;
	/* GET DATA EMPLOYEE ASSIGN TO */
    SELECT group_concat(F_FullName(reference_id) separator ', ')  INTO @employees FROM `project_task_employee_refers` WHERE is_profit_center = 0 AND project_task_id = @task_id;
	/* GET DATA PROFIT CENTER ASSIGN TO */
    SELECT group_concat(f_PCName(`profit_centers`.name) separator ', ') INTO @profit_centers  FROM `project_task_employee_refers`
	JOIN `profit_centers` ON `profit_centers`.id = `project_task_employee_refers`.reference_id
	WHERE is_profit_center = 1 AND project_task_id = @task_id;
	RETURN IFNULL(CONCAT(@employees, ', ', @profit_centers), IFNULL(@employees, @profit_centers));
END$$
DELIMITER ;

/*
	INFORMATION PROJECT DETAIL - GET DATA FROM TABLE projects
*/

/*
	1 - PROJECT MANAGER - USE PROCDURE pr_EmployeeManager(project_id, project_manager_id)
*/

CALL pr_EmployeeManager(1565, 'project_manager_id');

/*
	2 - SPONSOR - USE PROCDURE pr_GetFieldProject(project_id, text_one_line_1)
*/

CALL pr_GetFieldProject(1565, 'text_one_line_1'); SELECT @result;

/*
	3 - BUS OWNER - USE PROCDURE pr_GetFieldProject(project_id, text_one_line_2)
*/

CALL pr_GetFieldProject(1565, 'text_one_line_2'); SELECT @result;

/*
	5 - PROJECT WBS - USE PROCDURE pr_GetFieldProject(project_id, project_code_1)
*/

CALL pr_GetFieldProject(1565, 'project_code_1'); SELECT @result;

/*
	6 - PHASE - USE PROCDURE pr_GetFieldProject(project_id, list_1)
*/

CALL pr_GetFieldListProject(1565, 'list_1'); 

/*
	7 - PROJECT NAME - USE PROCDURE pr_GetFieldProject(project_id, project_name)

*/

CALL pr_GetFieldProject(1565, 'project_name'); SELECT @result;


/* 
	GET DATA MILESTONE FROM TABLE project_milestones
*/

/*
	9 - AGREED - FIELD ID: milestone_date
	
*/
SELECT DATE_FORMAT(milestone_date, '%d - %m - %Y') as 'Agreed' 
FROM `project_milestones` WHERE project_id = 2091 
ORDER BY milestone_date ASC LIMIT 1;

/*
	10 - DATA TABLE MILESTONE FROM TABLE project_milestones
	GET DATA FIELDS
	 - Key Milestones - FIELD ID: project_milestone
	 - DATE FLAN      - FIELD ID: milestone_date
	 - VALIDATED      - FIELD ID: validated
*/

SELECT project_milestone as 'Key Milestones', DATE_FORMAT(milestone_date, '%b %Y') as 'Plan', validated as 'Validated' 
FROM `project_milestones` WHERE project_id = 2091;

/*
	GET INFORMATION OF THE TASKS FROM TABLE project_tasks
	
*/
/*
	1 - TASK NAME - FIELD ID: task_title
	2 - END DATE - FIELD ID: task_end_date
	3 - EMPLOYEE ASSIGN TO - FUNCTION f_ResourceAssign(id) - FIELD ID: ID
*/

SELECT task_title, f_ResourceAssign(id) as Assign, task_end_date  FROM `project_tasks` WHERE project_id = 2090;


/*
	GET INFORMATION FROM RISK
*/

/*
	GET DATA PROJECT RISK FROM TABLE project_risks
	1 - RISK NAME - FIELD ID: project_risk
	2 - RISK ASSIGN TO - FIELD ID: risk_assign_to
	3 - RISK CLOSE DATE - FIELD ID: risk_close_date
	
*/
SELECT project_risk, F_FullName(risk_assign_to) as risk_assign_to , risk_close_date FROM `project_risks` WHERE project_id = 2091;

/*

	GET DATA PROJECT
	CONSUMED, REMAIND, PROGRESS, 
*/
DELIMITER $$
CREATE PROCEDURE pr_GetDataBudgetProject(
	IN pr_project_id INT,
	OUT pr_consumed INT,
	OUT pr_completed INT,
	OUT pr_estimated INT
)
BEGIN
	SET @pr_project_id = pr_project_id;
	SET @activity_id = f_activity_id(@pr_project_id);
	
	/*GET DATA ESTIMATED*/
	SELECT sum(estimated) INTO pr_estimated FROM `project_tasks` WHERE project_id = @pr_project_id;

	/* GET DATA CONSUMED */
	SELECT sum(`activity_requests`.`value`) INTO pr_consumed FROM `activity_tasks`
	JOIN `activity_requests` ON `activity_tasks`.id = `activity_requests`.task_id
	WHERE `activity_tasks`.activity_id = @activity_id AND `activity_requests`.`status` = 2;

	/* GET DATA REMAIN */
	SET pr_completed = pr_estimated  - pr_consumed;

	/* GET DATA CONSUMED OF ACTIVITY */
	SELECT sum(`value`) INTO @consumed_activity FROM `activity_requests`
	WHERE activity_id = @activity_id AND `status` = 2;
	IF @consumed_activity IS NOT NULL THEN
		SET pr_consumed = pr_consumed + @consumed_activity;
	End IF;

END$$
DELIMITER ;
/*
	FOR BUDGET J.H GET DATA FROM TABLE project_budget_internals
	
	CREATED A PROCDURE: pr_GetDataBudgetInternal WITH PARAMS: project_id
	OUTPUT:
	  - Budget IS budget_md
	  - Actual IS consumed
	  - ETC IS REMAIN
	  - AEC IS FORCAST MD
*/


DELIMITER $$
CREATE PROCEDURE pr_GetDataBudgetInternal(IN pr_project_id int(9))
BEGIN
	SET @pr_project_id = pr_project_id;
	SET @activity_id = f_activity_id(@pr_project_id);
	
	CALL pr_GetDataBudgetProject(@pr_project_id, @consumed, @completed, @estimated);

	/* GET DATA BUDGET MD */
	SELECT sum(budget_md) INTO @budget_md FROM `project_budget_internal_details` WHERE project_id = @pr_project_id;

	SET @sql_string = 'SELECT @budget_md AS \'Budget\', @consumed as \'Actual\', @completed as \'ETC\', @estimated as \'AEC\'';
	PREPARE stmt FROM @sql_string;
	EXECUTE stmt;	
	DEALLOCATE PREPARE stmt;

END$$
DELIMITER ;

/*
	1 - BUDGET - FIELD ID: budget_md
	2 - ACTUAL - FIELD ID: engaged_md
	3 - ETC    - FIELD ID: remain_md
*/


Call pr_GetDataBudgetInternal(3050); 

/*
	FOR BUDGET IN €: GET DATA FROM TABLE project_budget_internals, project_budget_externals
	1 - BUDGET = sum(project_budget_internals.budget_md) + sum(project_budget_externals.budget_md)
	2 - ACTUAL = sum(project_budget_internals.engaged_md) + sum(project_budget_externals.engaged_md)
	3 - ETC    = sum(project_budget_internals.remain_md) + sum(project_budget_externals.remain_md)
	4 - EAC    = sum(project_budget_internals.forecast_md) + sum(project_budget_externals.forecast_md)
*/

/*
	GET DATA BUDGET EXTERNALS EURO
*/

DELIMITER $$
CREATE PROCEDURE get_data_buget_external_euro(
 IN pr_project_id INT,
 OUT ex_budget_euro INT,
 OUT ex_ordered_euro INT,
 OUT ex_remain_euro INT,
 OUT ex_forcast_euro INT)
BEGIN
			
	SET @pr_project_id = pr_project_id;

	-- ex_budget_euro
	SELECT sum(budget_erro) INTO ex_budget_euro
	FROM `project_budget_externals` WHERE project_id = @pr_project_id;
	
	-- ex_ordered_euro
	SELECT sum(ordered_erro) INTO ex_ordered_euro
	FROM `project_budget_externals` WHERE project_id = @pr_project_id;

	-- ex_remain_euro
	SELECT sum(remain_erro) INTO ex_remain_euro
	FROM `project_budget_externals` WHERE project_id = @pr_project_id;
	
	-- ex_forcast_euro
	SELECT sum(ordered_erro + remain_erro) INTO ex_forcast_euro
	FROM `project_budget_externals` WHERE project_id = @pr_project_id;
 
END$$
DELIMITER ;


/*
	FUNCTION GET ACTIVITY ID
*/
DELIMITER $$
CREATE function f_activity_id(pr_project_id INT(20))
RETURNS INT(20)
BEGIN 
	SET @pr_project_id = pr_project_id;
	RETURN (SELECT activity_id FROM `projects` WHERE id = @pr_project_id);
END$$
DELIMITER ;


/*
	FUNCTION GET TJM EMPLOYEE
*/

DELIMITER $$
CREATE function f_employee_tjm(pr_employee_id INT(5))
RETURNS INT(5)
BEGIN 
	SET @pr_employee_id = pr_employee_id;
	RETURN IFNULL((SELECT tjm FROM `employees` WHERE id = @pr_employee_id), 1);
END$$
DELIMITER ;

/*
	FUNCTION GET Total Internal Average
*/

DELIMITER $$
CREATE function f_total_internal_average(pr_project_id INT(5))
RETURNS INT(5)
BEGIN 
	SET @pr_project_id = pr_project_id;
	RETURN IFNULL((SELECT sum(average)/count(id) FROM `project_budget_internal_details` WHERE project_id = @pr_project_id), 0);
END$$
DELIMITER ;

/*
	FUNCTION GET REMAIND EXTERNAL
	GET ESTIMATED AND CONSUMED OF TASK SPECIAL
*/

DELIMITER $$
CREATE function f_external_remaind_special(pr_project_id INT(5))
RETURNS INT(5)
BEGIN 
	SET @pr_project_id = pr_project_id;
	RETURN IFNULL((SELECT SUM(estimated) - SUM(special_consumed) FROM `project_tasks` WHERE special = 1 AND project_id = @pr_project_id), 0);
END$$
DELIMITER ;


/*
	GET DATA BUDGET EXTERNALS EURO
*/

DELIMITER $$
CREATE PROCEDURE get_data_buget_internal_euro(
 IN pr_project_id INT,
 OUT in_budget_euro INT,
 OUT in_engaged_euro INT,
 OUT in_remain_euro INT,
 OUT in_forcast_euro INT)
BEGIN
			
	SET @pr_project_id = pr_project_id;
	SET @activity_id = f_activity_id(@pr_project_id);
	-- in_budget_euro
	SELECT sum(budget_md * average) INTO in_budget_euro 
	FROM `project_budget_internal_details` WHERE project_id = @pr_project_id;
	
	-- in_engaged_euro
	SELECT sum(value * f_employee_tjm(employee_id)) INTO in_engaged_euro FROM `activity_requests`
	JOIN `activity_tasks` ON `activity_tasks`.id = `activity_requests`.task_id or `activity_tasks`.activity_id = `activity_requests`.activity_id
	WHERE `activity_tasks`.activity_id = @activity_id and `activity_requests`.`status` = 2;
	
	-- in_remain_euro
	SELECT ((@pr_estimated  - @pr_consumed) - f_external_remaind_special(@pr_project_id)) * f_total_internal_average(@pr_project_id) INTO in_remain_euro;
	
	-- in_forcast_euro
	SELECT in_engaged_euro + (f_total_internal_average(@pr_project_id) * (@pr_estimated  - @pr_consumed)) INTO in_forcast_euro;
 
END$$
DELIMITER ;

/*
	PROCEDURE GET DATE PROJECT BUDGET EURO
*/

DELIMITER $$
CREATE PROCEDURE get_data_buget_euro(IN pr_project_id INT)
BEGIN
			
	SET @pr_project_id = pr_project_id;
	CALL pr_GetDataBudgetProject(@pr_project_id, @pr_consumed, @pr_completed,@pr_estimated);
	CALL get_data_buget_internal_euro(@pr_project_id, @in_budget_euro, @in_engaged_euro, @in_remain_euro, @in_forcast_euro);
	CALL get_data_buget_external_euro(@pr_project_id, @ex_budget_euro, @ex_engaged_euro, @ex_remain_euro, @ex_forcast_euro);
	SET @sql_string = 'SELECT(@in_budget_euro + @ex_budget_euro) AS \'BUDGET\',
	  (@in_engaged_euro + @ex_engaged_euro) AS \'ACTUAL\',
	  (@in_remain_euro + @ex_remain_euro) AS \'ETC\',
	  (@in_forcast_euro + @ex_forcast_euro) AS \'EAC\';';
	PREPARE stmt FROM @sql_string;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
 
END$$
DELIMITER ;


/*
	Progress   = Consumed/workload
*/

SELECT ROUND((@consumed/@estimated) * 100, 2) AS 'PROGRESS';

/*
	Weather and message: GET DATA FROM TABLE project_amrs
	1  - PROJECT STATUS - FIELD ID:  weather
	2  - SCOPE - FIELD ID: scope_weather
	3  - SCHEDULE - FIELD ID: schedule_weather
	4  - BUDGET - FIELD ID: budget_weather
	5  - RESOURCE - FIELD ID: resources_weather
	6  - TECHNICAL - FIELD ID: technical_weather
	7,8,9,10,11  - GET DATA FROM TABLE LOGSYSTEM
	12 - PROGRESS OF THE WEEK/ HIGHTLIGHTS - FIELD ID:  GET DATA FROM TABLE LOGSYSTEM - MODEL: PROJECTAMR
	13 - EXCUTIVE SUMARY - FIELD ID:  GET DATA FROM TABLE LOGSYSTEM - MODEL: DONE
*/

	
/* 
	GET DATA PROJECT 
*/

DELIMITER $$
CREATE PROCEDURE pr_GetFieldProjectAmr(IN pr_project_id int(9), IN pr_field_name VARCHAR(50))
BEGIN
  SET @pr_project_id = pr_project_id;
  SET @pr_field_name = pr_field_name;
  SET @sql_string = concat('SELECT (',@pr_field_name,') INTO @result FROM `project_amrs` WHERE project_id = ',@pr_project_id);
  PREPARE stmt FROM @sql_string;
  EXECUTE stmt;
  DEALLOCATE PREPARE stmt;
END$$
DELIMITER ;

/*
	1  - PROJECT STATUS - FIELD ID:  weather
*/

CALL pr_GetFieldProjectAmr(3050, 'weather'); SELECT @result;
	
/*
	2  - SCOPE - FIELD ID: scope_weather
*/

CALL pr_GetFieldProjectAmr(3050, 'scope_weather'); SELECT @result;

/*
	3  - SCHEDULE - FIELD ID: schedule_weather
*/

CALL pr_GetFieldProjectAmr(3050, 'schedule_weather'); SELECT @result;

/*
	4  - BUDGET - FIELD ID: budget_weather
*/

CALL pr_GetFieldProjectAmr(3050, 'budget_weather'); SELECT @result;

/*
	5  - RESOURCE - FIELD ID: resources_weather
*/

CALL pr_GetFieldProjectAmr(3050, 'resources_weather'); SELECT @result;

/*
	6  - TECHNICAL - FIELD ID: technical_weather
*/

CALL pr_GetFieldProjectAmr(3050, 'technical_weather'); SELECT @result;

/*
	PROCDURE GET COMMENT
*/

DELIMITER $$
CREATE PROCEDURE pr_GetCommentLogs(IN pr_project_id int(9), IN pr_model_name VARCHAR(50))
BEGIN
  SET @pr_project_id = pr_project_id;
  SET @pr_model_name = pr_model_name;
  SET @sql_string = concat('SELECT group_concat(description separator \'\n\') INTO @result FROM `log_systems` WHERE model = \'', @pr_model_name, '\' AND model_id = ',@pr_project_id);
  PREPARE stmt FROM @sql_string;
  EXECUTE stmt;
  DEALLOCATE PREPARE stmt;
END$$
DELIMITER ;

/*
	7  - COMMENT SCOPE - pr_model_name: Scope
*/
	
	CALL pr_GetCommentLogs(1541, 'Scope'); SELECT @result;
	
/*
	8  - COMMENT SCHEDULE - pr_model_name: Schedule
	
*/
	CALL pr_GetCommentLogs(1541, 'Schedule'); SELECT @result;

/*
	9  - COMMENT BUDGET - pr_model_name: Budget
*/
	
	CALL pr_GetCommentLogs(1541, 'Budget'); SELECT @result;

/*
	10 - COMMENT RESOURCE - pr_model_name: Resource
*/
	CALL pr_GetCommentLogs(1541, 'Resource'); SELECT @result;

/*
	11 - COMMENT TECHNICAL - pr_model_name: Technical
*/

	CALL pr_GetCommentLogs(1541, 'Technical'); SELECT @result;
	
/*
	12 - PROGRESS OF THE WEEK/ HIGHTLIGHTS IS COMMENT - pr_model_name: ProjecAmr
	
*/

	CALL pr_GetCommentLogs(1541, 'ProjecAmr'); SELECT @result;

/*
	13 - EXCUTIVE SUMARY IS COMMENT DONE - pr_model_name: Done

*/

	CALL pr_GetCommentLogs(1541, 'Done'); SELECT @result;

