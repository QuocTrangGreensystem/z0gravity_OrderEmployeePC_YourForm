ALTER TABLE `project_created_values`
ADD COLUMN `next_block`  tinyint(4) DEFAULT '0',
ADD COLUMN `block_name` varchar(255) DEFAULT NULL;