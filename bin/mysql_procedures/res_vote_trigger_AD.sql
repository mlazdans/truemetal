DELIMITER $$

DROP TRIGGER IF EXISTS res_vote_trigger_AD $$
CREATE TRIGGER res_vote_trigger_AD AFTER DELETE ON res_vote
FOR EACH ROW BEGIN
	CALL res_update_meta(OLD.res_id);
END; $$

DELIMITER ;
