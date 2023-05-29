DELIMITER $$

DROP PROCEDURE IF EXISTS `logins_update_meta_all` $$
CREATE PROCEDURE `logins_update_meta_all` ()
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE v_l_id INT DEFAULT 0;
	DECLARE cur1 CURSOR FOR SELECT DISTINCT l_id FROM `logins`;
	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cur1;
	REPEAT
		FETCH cur1 INTO v_l_id;
		IF NOT done THEN
			CALL logins_update_meta(v_l_id);
		END IF;
	UNTIL done END REPEAT;
END $$

DELIMITER ;

