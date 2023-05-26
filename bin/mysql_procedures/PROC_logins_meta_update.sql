DELIMITER $$

DROP PROCEDURE IF EXISTS logins_meta_update $$
CREATE PROCEDURE logins_meta_update (p_l_id INTEGER UNSIGNED)
BEGIN
	UPDATE logins l
	LEFT JOIN (
		SELECT
			r.login_id AS login_id,
			SUM(rm.res_votes_plus_count) AS votes_plus,
			SUM(rm.res_votes_minus_count) AS votes_minus
		FROM res r
		JOIN res_meta rm ON rm.res_id = r.res_id
		WHERE
			r.login_id IS NOT NULL AND
			(p_l_id IS NULL OR r.login_id = p_l_id)
		GROUP BY r.login_id
	) AS t2 ON t2.login_id = l.l_id
	SET
		l.votes_plus = COALESCE(t2.votes_plus, 0),
		l.votes_minus = COALESCE(t2.votes_minus, 0)
	WHERE p_l_id IS NULL OR l.l_id = p_l_id
	;

	UPDATE logins l
	LEFT JOIN (
		SELECT
			r.login_id AS login_id,
			COUNT(*) AS comment_count
		FROM res r
		JOIN res_meta rm ON rm.res_id = r.res_id
		WHERE
			r.table_id = 3 AND
			r.res_visible = 1 AND
			r.login_id IS NOT NULL AND
			(p_l_id IS NULL OR r.login_id = p_l_id)
		GROUP BY r.login_id
	) AS t2 ON t2.login_id = l.l_id
	SET
		l.comment_count = COALESCE(t2.comment_count, 0)
	WHERE p_l_id IS NULL OR l.l_id = p_l_id
	;

END $$

DELIMITER ;
