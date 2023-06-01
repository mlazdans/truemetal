DELIMITER $$

DROP TRIGGER IF EXISTS res_vote_trigger_AI $$
CREATE TRIGGER res_vote_trigger_AI AFTER INSERT ON res_vote
FOR EACH ROW BEGIN
	CALL res_meta_update_votes(NEW.res_id);
END; $$

DELIMITER ;
