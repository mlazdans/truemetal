DELIMITER $$

DROP TRIGGER IF EXISTS `res_trigger2` $$
CREATE TRIGGER res_trigger2 AFTER DELETE ON `res`
FOR EACH ROW BEGIN
	IF OLD.res_resid IS NOT NULL THEN
		CALL res_update_meta(OLD.res_resid);
	END IF;
	CALL logins_update_meta(OLD.login_id);
END; $$

DELIMITER ;
