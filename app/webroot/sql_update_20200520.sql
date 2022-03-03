/* Update by Huynh 

Add item LINK*/

ALTER TABLE `sql_managers`
MODIFY COLUMN `type`  enum('sql','iframe', 'link') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'sql' AFTER `is_template`;

