DELIMITER $$

DROP TRIGGER IF EXISTS `res_comment_trigger1` $$
CREATE TRIGGER res_comment_trigger1 AFTER INSERT ON res_comment
FOR EACH ROW BEGIN
	CALL res_update_meta(NEW.res_id);
END; $$

DELIMITER ;

