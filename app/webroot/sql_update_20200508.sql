/* Update by VietNguyen
 * 06/05/2020
 * #610
 * remove goi spl_update_20200508-1.spl vi da gop chung vao goi nay
 */
ALTER TABLE `colors`
ADD COLUMN `logo_client` varchar(255) NULL AFTER `attachment`;

CREATE TABLE `sas_settings` (
`id`  int(10) NOT NULL AUTO_INCREMENT ,
`name` varchar(255) NULL,
`value`  varchar(2048) NULL ,
`content` varchar(2048) NULL,
`weight`  int(10) NULL ,
`created`  int(10) NULL ,
PRIMARY KEY (`id`)
);