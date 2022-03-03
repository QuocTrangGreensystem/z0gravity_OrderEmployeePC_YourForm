
CREATE TABLE `your_form_filters` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `widget` varchar(100) DEFAULT NULL,
  `display` int(1) DEFAULT NULL,
  `weight` int(10) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
)DEFAULT CHARSET=utf8;
