DELIMITER $$

DROP TRIGGER IF EXISTS res_trigger_AI $$
CREATE TRIGGER res_trigger_AI AFTER INSERT ON res
FOR EACH ROW BEGIN
	CALL res_meta_update(NEW.res_id);
	IF NEW.res_resid IS NOT NULL THEN
		CALL res_meta_update(NEW.res_resid);
	END IF;
END; $$

DELIMITER ;
