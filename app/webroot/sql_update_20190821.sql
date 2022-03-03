ALTER TABLE `sql_managers`
ADD COLUMN `type`  enum('sql','iframe') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'sql' AFTER `is_template`;