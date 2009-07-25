DELIMITER $$

DROP TRIGGER IF EXISTS `comment_connect_trigger3` $$
CREATE TRIGGER comment_connect_trigger3 AFTER DELETE ON `comment_connect`
FOR EACH ROW BEGIN
	CALL comment_update_meta(OLD.cc_table, OLD.cc_table_id);
END; $$

DELIMITER ;

