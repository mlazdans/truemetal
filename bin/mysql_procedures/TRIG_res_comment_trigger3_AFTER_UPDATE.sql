DELIMITER $$

DROP TRIGGER IF EXISTS `res_comment_trigger3` $$
CREATE TRIGGER res_comment_trigger3 AFTER UPDATE ON res_comment
FOR EACH ROW BEGIN
	CALL res_update_meta(NEW.res_id);
	CALL res_update_meta(OLD.res_id);
END; $$

DELIMITER ;

