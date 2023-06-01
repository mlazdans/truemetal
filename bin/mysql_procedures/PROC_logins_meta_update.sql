DELIMITER $$

DROP PROCEDURE IF EXISTS logins_meta_update $$
CREATE PROCEDURE logins_meta_update (p_l_id INTEGER UNSIGNED)
BEGIN
	CALL logins_meta_update_votes(p_l_id);
	CALL logins_meta_update_comments(p_l_id);
END $$

DELIMITER ;
