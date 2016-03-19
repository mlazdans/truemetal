DELIMITER $$

DROP PROCEDURE IF EXISTS `logins_update_meta` $$
CREATE PROCEDURE `logins_update_meta` (p_l_id INT)
BEGIN
	DECLARE v_votes_plus INT DEFAULT 0;
	DECLARE v_votes_minus INT DEFAULT 0;
	DECLARE v_comment_count INT DEFAULT 0;

	SELECT
		COUNT(*)
	INTO v_votes_plus
	FROM
		comment c
	JOIN res_vote rv ON rv.res_id = c.res_id
	WHERE
		c.login_id = p_l_id AND
		rv.rv_value = 1
	;

	SELECT
		COUNT(*)
	INTO v_votes_minus
	FROM
		comment c
	JOIN res_vote rv ON rv.res_id = c.res_id
	WHERE
		c.login_id = p_l_id AND
		rv.rv_value = -1
	;

	SELECT COUNT(*) INTO v_comment_count FROM comment WHERE login_id = p_l_id;

	UPDATE logins SET
		votes_plus = v_votes_plus,
		votes_minus = v_votes_minus,
		comment_count = v_comment_count
	WHERE
		l_id = p_l_id
	;

END $$

DELIMITER ;


