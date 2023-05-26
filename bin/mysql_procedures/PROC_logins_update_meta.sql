DELIMITER $$

DROP PROCEDURE IF EXISTS logins_update_meta $$
CREATE PROCEDURE logins_update_meta (p_l_id INT)
BEGIN
	UPDATE logins l
	LEFT JOIN (
		SELECT
			r.login_id AS login_id,
			SUM(rm.res_votes_plus_count) AS votes_plus,
			SUM(rm.res_votes_minus_count) AS votes_minus
		FROM res r
		JOIN res_meta rm ON rm.res_id = r.res_id
		WHERE r.login_id IS NOT NULL AND r.login_id = COALESCE(p_l_id, r.login_id)
		GROUP BY r.login_id
	) AS t2 ON t2.login_id = l.l_id
	SET
		l.votes_plus = COALESCE(t2.votes_plus, 0),
		l.votes_minus = COALESCE(t2.votes_minus, 0)
	WHERE l.l_id = COALESCE(p_l_id, l.l_id)
	;

	UPDATE logins l
	LEFT JOIN (
		SELECT
			r.login_id AS login_id,
			COUNT(*) AS comment_count
		FROM res r
		JOIN res_meta rm ON rm.res_id = r.res_id
		WHERE r.table_id = 3 AND r.res_visible = 1 AND r.login_id IS NOT NULL AND r.login_id = COALESCE(p_l_id, r.login_id)
		GROUP BY r.login_id
	) AS t2 ON t2.login_id = l.l_id
	SET
		l.comment_count = COALESCE(t2.comment_count, 0)
	WHERE l.l_id = COALESCE(p_l_id, l.l_id)
	;

END $$

DELIMITER ;
