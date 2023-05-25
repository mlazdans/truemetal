DELIMITER $$

DROP TRIGGER IF EXISTS res_trigger_AU $$
CREATE TRIGGER res_trigger_AU AFTER UPDATE ON res
FOR EACH ROW BEGIN
	CALL res_update_meta(OLD.res_id);
	IF OLD.res_resid IS NOT NULL THEN
		CALL res_update_meta(OLD.res_resid);
	END IF;
	IF OLD.res_id != NEW.res_id THEN
		CALL res_update_meta(NEW.res_id);
	END IF;
	IF OLD.res_resid != NEW.res_resid AND NEW.res_resid IS NOT NULL THEN
		CALL res_update_meta(NEW.res_resid);
	END IF;
	IF OLD.login_id != NEW.login_id THEN
		CALL logins_update_meta(OLD.login_id);
	END IF;
	CALL logins_update_meta(NEW.login_id);
END; $$

DELIMITER ;
