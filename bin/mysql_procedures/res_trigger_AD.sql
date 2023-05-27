DELIMITER $$

DROP TRIGGER IF EXISTS res_trigger_AD $$
CREATE TRIGGER res_trigger_AD AFTER DELETE ON res
FOR EACH ROW BEGIN
	IF OLD.res_resid IS NOT NULL THEN
		CALL res_meta_update_childs(OLD.res_resid);
	END IF;
END; $$

DELIMITER ;
