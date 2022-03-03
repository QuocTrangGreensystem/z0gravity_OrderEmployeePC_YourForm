
/*
	Created by Vinh Huynh
	Email: Z0G 08/03/2019 Last actions before the TET
	Add fields for project zog_msgs
*/
 
CREATE TABLE `zog_msg_likes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) DEFAULT NULL,
  `zog_msg_id` int(10) DEFAULT NULL,
  `created` int(10) DEFAULT NULL,
  `updated` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE `zog_msg_refers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) DEFAULT NULL,
  `zog_msg_id` int(10) DEFAULT NULL,
  `project_id` int(10) DEFAULT NULL,
  `status` int(10) DEFAULT NULL,
  `created` int(10) DEFAULT NULL,
  `updated` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;



ALTER table `zog_msgs`
add column `parent_id` int(11) DEFAULT NULL,
add column `count_like` int(11) DEFAULT NULL;
