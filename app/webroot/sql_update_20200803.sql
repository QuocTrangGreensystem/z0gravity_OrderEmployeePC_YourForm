ALTER TABLE `translation_settings`
ADD COLUMN `block_name` varchar(255) DEFAULT NULL;

DELIMITER $$
Drop PROCEDURE if exists pr_update_blockname;
CREATE PROCEDURE pr_update_blockname()
BEGIN
	set @id = 0;
	SET @block_name = '';
	SELECT max(translations.id) INTO @n FROM translations;
	WHILE @id<=@n DO 
		SELECT translations.block_name FROM translations where translations.id = @id INTO @block_name;
		IF(@block_name IS NOT NULL and @block_name != '') then 
			update translation_settings set translation_settings.block_name=@block_name where translation_settings.translation_id = @id;
		end IF;
		set @id = @id + 1;
	END WHILE;
END;

call pr_update_blockname();
Drop PROCEDURE if exists pr_update_blockname;
$$
DELIMITER ;

ALTER TABLE `translations`
Drop COLUMN `block_name`;