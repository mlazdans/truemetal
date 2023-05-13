DELIMITER $$

DROP TRIGGER IF EXISTS `res_vote_trigger1` $$
CREATE TRIGGER res_vote_trigger1 AFTER INSERT ON res_vote
FOR EACH ROW BEGIN
	CALL res_update_meta(NEW.res_id);
END; $$

DELIMITER ;
