DELIMITER $$

DROP TRIGGER IF EXISTS res_vote_trigger_AU $$
CREATE TRIGGER res_vote_trigger_AU AFTER UPDATE ON res_vote
FOR EACH ROW BEGIN
	CALL res_meta_update_votes(NEW.res_id);
	IF (NEW.res_id <=> OLD.res_id) THEN
		CALL res_meta_update_votes(OLD.res_id);
	END IF;
END; $$

DELIMITER ;
