CREATE TABLE project_powerbi_dashboards (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  project_id int(10) NOT NULL,
  iframe varchar(1000) NOT NULL,
  employee_id int(11) DEFAULT NULL,
  created int(10) DEFAULT NULL,
  updated int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
)