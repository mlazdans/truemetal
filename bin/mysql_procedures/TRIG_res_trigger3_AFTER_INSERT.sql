DELIMITER $$

DROP TRIGGER IF EXISTS `res_trigger3` $$
CREATE TRIGGER res_trigger3 AFTER INSERT ON `res`
FOR EACH ROW BEGIN
	CALL res_update_meta(NEW.res_id);
	CALL res_update_meta(NEW.res_resid);
	CALL logins_update_meta(NEW.login_id);
END; $$

DELIMITER ;
