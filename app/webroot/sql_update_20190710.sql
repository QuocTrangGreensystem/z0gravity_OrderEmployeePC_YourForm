ALTER TABLE `translation_settings`
ADD COLUMN `next_block`  tinyint(4) DEFAULT '0';

ALTER TABLE `translations`
ADD COLUMN `block_name` varchar(255) DEFAULT NULL;