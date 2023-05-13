DELIMITER $$

DROP TRIGGER IF EXISTS `res_vote_trigger3` $$
CREATE TRIGGER res_vote_trigger3 AFTER UPDATE ON res_vote
FOR EACH ROW BEGIN
	CALL res_update_meta(OLD.res_id);
	IF (OLD.res_id != NEW.res_id) THEN
		CALL res_update_meta(NEW.res_id);
	END IF;
END; $$

DELIMITER ;
