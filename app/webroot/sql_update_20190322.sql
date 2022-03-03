-- GET FULL NAME OF RESOURCES

DELIMITER $$
DROP FUNCTION IF EXISTS F_FullName;
CREATE FUNCTION F_FullName(employee_id int(9)) 
RETURNS VARCHAR(50)
READS SQL DATA
DETERMINISTIC
BEGIN
	SET @employee_id = employee_id;
	return (SELECT CONCAT(first_name, ' ', last_name) FROM `employees` WHERE id = @employee_id);
END$$
DELIMITER ;

-- GET PROJECT MANAGE

DELIMITER $$
DROP FUNCTION IF EXISTS f_ProjectManager;
CREATE FUNCTION f_ProjectManager(project_id INT(20))
RETURNS varchar(250)
READS SQL DATA
DETERMINISTIC
BEGIN 
	SET @project_id = project_id;
	SELECT project_manager INTO @project_managers FROM v_project_manager_refer
	WHERE project_id = @project_id;
	RETURN @project_managers;
	
END$$
DELIMITER ;

-- GET PROJECT PROGRAM

DELIMITER $$
DROP FUNCTION IF EXISTS f_ProjectProgram;
CREATE FUNCTION f_ProjectProgram(program_id INT(20))
RETURNS varchar(250)
READS SQL DATA
DETERMINISTIC
BEGIN 
	SET @program_id = program_id;
    SELECT amr_program  INTO @project_program FROM `project_amr_programs` WHERE id = @program_id;
	RETURN @project_program;
	
END$$
DELIMITER ;

-- GET PROJECT SUB PROGRAM

DELIMITER $$
DROP FUNCTION IF EXISTS f_ProjectSubProgram;
CREATE FUNCTION f_ProjectSubProgram(sub_program_id INT(20))
RETURNS varchar(250)
READS SQL DATA
DETERMINISTIC
BEGIN 
	SET @sub_program_id = sub_program_id;
    SELECT amr_sub_program  INTO @project_sub_program FROM `project_amr_sub_programs` WHERE id = @sub_program_id;
	RETURN @project_sub_program;
	
END$$
DELIMITER ;

-- GET PROJECT CATEGORY
DELIMITER $$
DROP FUNCTION IF EXISTS f_ProjectCategory;
CREATE FUNCTION f_ProjectCategory(cate_id INT(5)) 
RETURNS VARCHAR(20)
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE cate_name varchar(20);
 
    IF cate_id = 1 THEN
		SET cate_name = 'In progress';
    ELSEIF cate_id = 2 THEN
        SET cate_name = 'Opportunity';
    ELSEIF cate_id = 3 THEN
        SET cate_name = 'Archived';
	ELSEIF cate_id = 4 THEN
        SET cate_name = 'Model';
    END IF;
 
 RETURN (cate_name);
END$$
DELIMITER ;

-- GET PROJECT PHASE PLAN

DELIMITER $$
DROP FUNCTION IF EXISTS f_ProjectPhasePlan;
CREATE FUNCTION f_ProjectPhasePlan(phase_plan_id INT(20))
RETURNS varchar(250)
READS SQL DATA
DETERMINISTIC
BEGIN 
	SET @phase_plan_id = phase_plan_id;
    SELECT name  INTO @project_phase_name FROM `project_phases` WHERE id = @phase_plan_id;
	RETURN @project_phase_name;
	
END$$
DELIMITER ;

-- CREATE VIEW PROJECT MANAGER

DELIMITER $$
DROP PROCEDURE IF EXISTS pr_ViewProjectManagerRefer;
CREATE PROCEDURE pr_ViewProjectManagerRefer()
READS SQL DATA
DETERMINISTIC
BEGIN
	SET @sql_string = 'CREATE VIEW `v_project_manager_refer` AS SELECT project_id, CONCAT(GROUP_CONCAT(DISTINCT(F_FullName(`project_employee_managers`.project_manager_id))), \',\', F_FullName(`projects`.project_manager_id)) as project_manager
	FROM `project_employee_managers`
	INNER JOIN `projects` ON `projects`.id = `project_employee_managers`.project_id
	WHERE type = \'PM\' AND `project_employee_managers`.project_manager_id <> `projects`.project_manager_id
	GROUP BY project_id';
	PREPARE stmt FROM @sql_string;
	EXECUTE stmt;	
	DEALLOCATE PREPARE stmt;

END$$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS pr_GetDataProject;
CREATE PROCEDURE pr_GetDataProject()
READS SQL DATA
DETERMINISTIC
BEGIN
	DROP VIEW IF EXISTS v_project_manager_refer;
	CALL pr_ViewProjectManagerRefer();
	SET @sql_string = 'select f_ProjectCategory(category) as Status, f_ProjectProgram(project_amr_program_id) as Program, f_ProjectSubProgram(project_amr_sub_program_id) as \'Sub Program\', project_name as \'Project name\',
	v_project_manager_refer.project_manager as \'Project Manager\', project_code_1 as \'Project code 1\',
	f_ProjectPhasePlan(`project_phase_plans`.project_planed_phase_id) as \'Phase name\' ,`project_phase_plans`.ref1 as \'REF1 of phase name\',
	`project_phase_plans`.ref2 as \'REF2 of phase name\',
	`project_phase_plans`.ref3 as \'REF3 of phase name\' ,`project_phase_plans`.ref4 as \'REF4 of phase name\' ,
	`project_phase_plans`.phase_planed_start_date as \'Plan start date\', `project_phase_plans`.phase_planed_end_date as \'Plan end date\',
	`project_phase_plans`.phase_real_start_date as \'Real start date\',  `project_phase_plans`.phase_real_end_date  as \'Real end date\'
	FROM projects
	INNER JOIN v_project_manager_refer on projects.id = v_project_manager_refer.project_id
	INNER JOIN `project_phase_plans` on `project_phase_plans`.project_id = `projects`.id
	WHEre company_id = %companyID%';
	PREPARE stmt FROM @sql_string;
	EXECUTE stmt;	
	DEALLOCATE PREPARE stmt;

END$$
DELIMITER ;

DELIMITER $$
DROP FUNCTION IF EXISTS f_CompanyName;
CREATE FUNCTION f_CompanyName(company_id INT(20))
RETURNS varchar(50)
READS SQL DATA
DETERMINISTIC
BEGIN 
	SET @company_id = company_id;
    SELECT company_name  INTO @company_name FROM `companies` WHERE id = @company_id;
	RETURN @company_name;
	
END$$
DELIMITER ;

DELIMITER $$
DROP FUNCTION IF EXISTS f_ProfitCenter;
CREATE FUNCTION f_ProfitCenter(employee_id INT(20))
RETURNS varchar(100)
READS SQL DATA
DETERMINISTIC
BEGIN 
	SET @employee_id = employee_id;
    SELECT GROUP_CONCAT(name) INTO @profit_name FROM `profit_centers` WHERE manager_id = @employee_id GROUP BY manager_id;
	RETURN @profit_name;
	
END$$
DELIMITER ;

-- GET TASK IS EMPTY WITH TIMESHEET

SELECT id, FROM_UNIXTIME(date, '%d/%m/%Y') as date,
value, model, F_FullName(employee_id) as 'Employee', f_ProfitCenter(employee_id) as 'Team', company_id, f_CompanyName(company_id) as 'Company Name',
 `status`, task_id, FROM_UNIXTIME(created, '%d/%m/%Y') as created, 
FROM_UNIXTIME(updated, '%d/%m/%Y') as updated, value_hour, manager_by
FROM `activity_requests` WHERE task_id not in (select id from activity_tasks);

