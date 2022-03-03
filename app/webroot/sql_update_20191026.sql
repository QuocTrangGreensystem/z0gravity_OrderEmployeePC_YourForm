/* Update by Huynh LE
* 26-10-2019
* Delete comment of employee who has been deleted in "employees" table
*/

delete from `log_systems` WHERE employee_id not in(select id from employees);
