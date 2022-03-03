/*	Created by VietNguyen
	Ticket #1200
*/

CREATE TABLE if not EXISTS `ticket_phone_numbers` (
`id`  int(10) NOT NULL AUTO_INCREMENT,
`phone_number`  VARCHAR(20) NOT NULL,
`phone_name`  VARCHAR(255) NULL ,
`company_id`  int(10) NOT NULL ,
`acffected_cus`  int(10) NOT NULL ,
`acffected_dep`  int(10) NOT NULL ,
`created`  int(11) NULL ,
`updated`  int(11) NULL ,
PRIMARY KEY (`id`)
)AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


ALTER TABLE `ticket_statuses`
ADD COLUMN `send_sms` int(1) DEFAULT 0;