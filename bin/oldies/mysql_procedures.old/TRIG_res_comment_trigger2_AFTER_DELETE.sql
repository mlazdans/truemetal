DELIMITER $$

DROP TRIGGER IF EXISTS `res_comment_trigger2` $$
CREATE TRIGGER res_comment_trigger2 AFTER DELETE ON res_comment
FOR EACH ROW BEGIN
	CALL res_update_meta(OLD.res_id);
END; $$

DELIMITER ;

