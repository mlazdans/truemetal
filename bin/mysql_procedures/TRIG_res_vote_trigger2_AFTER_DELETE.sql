DELIMITER $$

DROP TRIGGER IF EXISTS `res_vote_trigger2` $$
CREATE TRIGGER res_vote_trigger2 AFTER DELETE ON res_vote
FOR EACH ROW BEGIN
	CALL res_update_meta(OLD.res_id);
END; $$

DELIMITER ;
