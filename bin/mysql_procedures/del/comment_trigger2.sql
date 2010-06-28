DELIMITER $$

DROP TRIGGER IF EXISTS `comment_trigger2` $$
CREATE TRIGGER comment_trigger2 BEFORE UPDATE ON comment
FOR EACH ROW BEGIN
	SET NEW.c_hash = SHA(NEW.c_data);
	IF NEW.c_hash <> OLD.c_hash THEN
		SET NEW.c_hash_date = NOW();
	END IF;
END; $$

DELIMITER ;

