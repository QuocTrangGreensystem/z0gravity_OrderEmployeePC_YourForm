CREATE TABLE `project_expectations` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `project_id` int(10) DEFAULT NULL,
  `list_1` int(10) DEFAULT NULL,
  `list_2` int(10) DEFAULT NULL,
  `list_3` int(10) DEFAULT NULL,
  `list_4` int(10) DEFAULT NULL,
  `list_5` int(10) DEFAULT NULL,
  `list_6` int(10) DEFAULT NULL,
  `list_7` int(10) DEFAULT NULL,
  `list_8` int(10) DEFAULT NULL,
  `list_9` int(10) DEFAULT NULL,
  `list_10` int(10) DEFAULT NULL,
  `list_11` int(10) DEFAULT NULL,
  `list_12` int(10) DEFAULT NULL,
  `list_13` int(10) DEFAULT NULL,
  `list_14` int(10) DEFAULT NULL,
  `list_15` int(10) DEFAULT NULL,
  `list_16` int(10) DEFAULT NULL,
  `list_17` int(10) DEFAULT NULL,
  `list_18` int(10) DEFAULT NULL,
  `list_19` int(10) DEFAULT NULL,
  `list_20` int(10) DEFAULT NULL,
  `list_21` int(10) DEFAULT NULL,
  `list_22` int(10) DEFAULT NULL,
  `list_23` int(10) DEFAULT NULL,
  `list_24` int(10) DEFAULT NULL,
  `list_25` int(10) DEFAULT NULL,
  `list_26` int(10) DEFAULT NULL,
  `list_27` int(10) DEFAULT NULL,
  `list_28` int(10) DEFAULT NULL,
  `list_29` int(10) DEFAULT NULL,
  `list_30` int(10) DEFAULT NULL,
  `date_1` date DEFAULT NULL,
  `date_2` date DEFAULT NULL,
  `date_3` date DEFAULT NULL,
  `date_4` date DEFAULT NULL,
  `date_5` date DEFAULT NULL,
  `date_6` date DEFAULT NULL,
  `date_7` date DEFAULT NULL,
  `date_8` date DEFAULT NULL,
  `date_9` date DEFAULT NULL,
  `date_10` date DEFAULT NULL,
  `text_long_1` varchar(2048) DEFAULT NULL,
  `text_long_2` varchar(2048) DEFAULT NULL,
  `text_long_3` varchar(2048) DEFAULT NULL,
  `text_long_4` varchar(2048) DEFAULT NULL,
  `text_long_5` varchar(2048) DEFAULT NULL,
  `text_short_1` varchar(100) DEFAULT NULL,
  `text_short_2` varchar(100) DEFAULT NULL,
  `text_short_3` varchar(100) DEFAULT NULL,
  `text_short_4` varchar(100) DEFAULT NULL,
  `text_short_5` varchar(100) DEFAULT NULL,
  `text_short_6` varchar(100) DEFAULT NULL,
  `text_short_7` varchar(100) DEFAULT NULL,
  `text_short_8` varchar(100) NOT NULL,
  `text_short_9` varchar(100) DEFAULT NULL,
  `text_short_10` varchar(100) DEFAULT NULL,
  `attached_documents` varchar(255) DEFAULT NULL,
  `text` varchar(255) DEFAULT NULL,
  `milestone` int(10) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

ALTER TABLE `expectation_datasets`
ADD COLUMN `eng`  varchar(255) NULL AFTER `name`,
ADD COLUMN `fre`  varchar(255) NULL AFTER `eng`;