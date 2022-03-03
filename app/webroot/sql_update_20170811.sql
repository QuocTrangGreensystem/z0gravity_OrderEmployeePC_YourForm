
CREATE TABLE `project_livrable_comments` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `project_livrable_id` int(10) DEFAULT NULL,
  `employee_id` int(10) DEFAULT NULL,
  `comment` varchar(2048) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
