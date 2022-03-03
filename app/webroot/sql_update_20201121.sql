CREATE TABLE if not exists `sso_logins` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`company_id`  int(11) NOT NULL ,
`issuer_url`  varchar(255) NULL ,
`saml_end_point`  varchar(255) NULL ,
`slo_end_point`  varchar(255) NULL ,
`services_id`  varchar(255) NULL ,
`services_name`  varchar(255) NULL ,
`certificate`  text NOT NULL ,
`created`  int(10) NULL ,
`updated`  int(10) NULL ,
PRIMARY KEY (`id`)
);