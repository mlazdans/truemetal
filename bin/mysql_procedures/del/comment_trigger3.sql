DELIMITER $$

DROP TRIGGER IF EXISTS `comment_trigger3` $$
CREATE TRIGGER comment_trigger3 AFTER DELETE ON `comment`
FOR EACH ROW BEGIN
	DELETE FROM `comment_connect` WHERE cc_c_id = OLD.c_id;
END; $$

DELIMITER ;

