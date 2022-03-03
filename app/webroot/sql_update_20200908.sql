CREATE TABLE `project_targets` (
`id`  int(10) NOT NULL AUTO_INCREMENT ,
`type`  varchar(20) NOT NULL ,
`value`  decimal(16,2) NULL ,
`company_id`  int(10) NOT NULL ,
`created`  int(10) NULL ,
`updated`  int(10) NULL ,
PRIMARY KEY (`id`),
FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`)
);

