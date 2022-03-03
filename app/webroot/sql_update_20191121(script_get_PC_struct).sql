DELIMITER $$
CREATE PROCEDURE pr_PC_Organization(IN company_id int(9), IN number_level VARCHAR(2))
BEGIN
  SET @company_id = company_id;
  SET @number_level = number_level;
  SET @sql_string = '';
  SET @field = 'SELECT t0.name as LV0 ';
  SET @joins = '';
  
  SET @i = 1;
	 WHILE @i <= @number_level DO
      SET @field = CONCAT(@field, ',t', @i ,'.name as LV', @i);
	    SET @joins = CONCAT(@joins,' LEFT JOIN `profit_centers` AS t',@i ,' ON t',@i, '.parent_id = t', @i - 1,'.id ');
      SET @i = @i + 1;
  END WHILE;
  SET @sql_string = CONCAT(@field, ' FROM `profit_centers` AS t0 ',@joins, ' WHERE t0.company_id =', @company_id);
  PREPARE stmt FROM @sql_string;
  EXECUTE stmt;
  DEALLOCATE PREPARE stmt;
END$$
DELIMITER ;