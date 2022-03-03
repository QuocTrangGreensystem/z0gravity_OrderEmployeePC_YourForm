CREATE TABLE if not exists `two_factor_authens` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`company_id`  int(11) NOT NULL ,
`employee_id`  int(11) NOT NULL ,
`secret_code`  varchar(25) NOT NULL ,
`duration_expired`  varchar(25) default 'day',
`created`  int(10) NULL ,
`updated`  int(10) NULL ,
PRIMARY KEY (`id`)
);

ALTER TABLE `two_factor_authens`
ADD COLUMN `current_cookie`  varchar(25) NULL AFTER `secret_code`;

ALTER TABLE `employees`
ADD COLUMN `two_factor_auth` int(10) DEFAULT 0;

CREATE TABLE `employee_last_logins` (
`id`  int(11) NOT NULL AUTO_INCREMENT,
`employee_id`  int(11) NOT NULL ,
`last_login`  int(10) NULL ,
`current_key`  varchar(50) NULL ,
`updated`  int(10) NULL ,
PRIMARY KEY (`id`)
);