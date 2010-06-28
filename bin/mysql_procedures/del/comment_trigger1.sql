DELIMITER $$

DROP TRIGGER IF EXISTS `comment_trigger1` $$
CREATE TRIGGER comment_trigger1 BEFORE INSERT ON comment
FOR EACH ROW BEGIN
	SET NEW.c_hash = SHA(NEW.c_data);
	SET NEW.c_hash_date = NEW.c_entered;
END; $$

DELIMITER ;

