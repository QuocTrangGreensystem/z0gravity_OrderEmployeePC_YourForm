DELETE FROM `project_employee_managers` WHERE project_manager_id NOT IN(SELECT id FROM employees);