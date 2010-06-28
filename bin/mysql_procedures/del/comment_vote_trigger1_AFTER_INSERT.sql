DELIMITER $$

DROP TRIGGER IF EXISTS `comment_vote_trigger1` $$
CREATE TRIGGER comment_vote_trigger1 AFTER INSERT ON comment_votes
FOR EACH ROW BEGIN
	CALL comment_update_votes(NEW.cv_c_id);
END; $$

DELIMITER ;

