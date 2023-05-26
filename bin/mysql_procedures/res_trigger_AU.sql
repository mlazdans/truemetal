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
END; $$

DELIMITER ;
