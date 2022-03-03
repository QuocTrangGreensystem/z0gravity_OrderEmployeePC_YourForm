CREATE TABLE `expectation_datasets` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `company_id` int(10) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `dataset_name` varchar(255) DEFAULT NULL,
  `display` int(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE `expectation_translations` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `company_id` int(10) DEFAULT NULL,
  `original_text` varchar(255) DEFAULT NULL,
  `eng` varchar(255) DEFAULT NULL,
  `fre` varchar(255) DEFAULT NULL,
  `field` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE `expectations` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `company_id` int(10) DEFAULT NULL,
  `page` varchar(100) DEFAULT NULL,
  `original_text` varchar(255) DEFAULT NULL,
  `eng` varchar(255) DEFAULT NULL,
  `fre` varchar(255) DEFAULT NULL,
  `field` varchar(255) DEFAULT NULL,
  `weight` int(10) DEFAULT NULL,
  `display` int(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;
