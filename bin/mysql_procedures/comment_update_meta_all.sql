DELIMITER $$

DROP PROCEDURE IF EXISTS `comment_update_meta_all` $$
CREATE PROCEDURE `comment_update_meta_all` ()
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE v_table VARBINARY(15) DEFAULT '';
	DECLARE v_table_id INT DEFAULT 0;
	DECLARE cur1 CURSOR FOR SELECT DISTINCT cc_table, cc_table_id FROM `comment_connect`;
	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cur1;
	REPEAT
		FETCH cur1 INTO v_table, v_table_id;
		IF NOT done THEN
			CALL comment_update_meta(v_table, v_table_id);
		END IF;
	UNTIL done END REPEAT;
END $$

DELIMITER ;

