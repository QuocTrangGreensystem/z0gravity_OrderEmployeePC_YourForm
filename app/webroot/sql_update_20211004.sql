/*	Created by TrungVV
	Notify Token
*/

CREATE TABLE if not EXISTS `notify_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(64) NOT NULL,
  `device_name` varchar(64) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `active` tinyint(4) DEFAULT '1',
  `notification_message` tinyint(1) DEFAULT '0',
  `notification_message_project` tinyint(1) DEFAULT '0',
  `notification_task_new` tinyint(1) DEFAULT '0',
  `notification_task_update` tinyint(1) DEFAULT '0',
  `created` int(10) DEFAULT NULL,
  `updated` int(10) DEFAULT NULL,
  `language` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
