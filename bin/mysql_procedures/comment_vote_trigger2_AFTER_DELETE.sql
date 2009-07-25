DELIMITER $$

DROP TRIGGER IF EXISTS `comment_vote_trigger2` $$
CREATE TRIGGER comment_vote_trigger2 AFTER DELETE ON comment_votes
FOR EACH ROW BEGIN
	CALL comment_update_votes(OLD.cv_c_id);
END; $$

DELIMITER ;

