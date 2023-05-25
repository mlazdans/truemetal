DELIMITER $$

DROP TRIGGER IF EXISTS logins_trigger_BI $$
CREATE TRIGGER logins_trigger_BI BEFORE INSERT ON logins
FOR EACH ROW BEGIN
	SET NEW.l_nick = TRIM(NEW.l_nick);
	SET NEW.l_email = LOWER(TRIM(NEW.l_email));
END; $$

DELIMITER ;
