DELIMITER $$

DROP TRIGGER IF EXISTS `comment_connect_trigger1` $$
CREATE TRIGGER comment_connect_trigger1 AFTER INSERT ON `comment_connect`
FOR EACH ROW BEGIN
	CALL comment_update_meta(NEW.cc_table, NEW.cc_table_id);
END; $$

DELIMITER ;

