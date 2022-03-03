DELETE FROM `project_livrable_comments` WHERE employee_id NOT IN(SELECT id FROM employees);
DELETE FROM `project_livrable_actors` WHERE employee_id NOT IN(SELECT id FROM employees);