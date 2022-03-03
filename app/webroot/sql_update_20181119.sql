CREATE TABLE `project_task_txt_refers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `task_id` int(10) NOT NULL,
  `employee_id` int(10) NOT NULL,
  `read_status` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
)DEFAULT CHARSET=utf8;