DELIMITER $$

DROP TRIGGER IF EXISTS logins_trigger1 $$
CREATE TRIGGER logins_trigger1 BEFORE INSERT ON logins
FOR EACH ROW BEGIN
	SET NEW.l_nick = TRIM(NEW.l_nick);
	SET NEW.l_email = LOWER(TRIM(NEW.l_email));
END; $$

DELIMITER ;
