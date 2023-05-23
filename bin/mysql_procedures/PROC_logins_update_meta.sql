DELIMITER $$

DROP PROCEDURE IF EXISTS `logins_update_meta` $$
CREATE PROCEDURE `logins_update_meta` (p_l_id INT)
BEGIN
	UPDATE logins l
	LEFT JOIN (SELECT
		r.login_id AS login_id,
		SUM(CASE WHEN rv.rv_value = +1 THEN 1 ELSE 0 END) AS votes_plus,
		SUM(CASE WHEN rv.rv_value = -1 THEN 1 ELSE 0 END) AS votes_minus,
		COUNT(DISTINCT c.c_id) AS comment_count
	FROM res r
	JOIN comment c ON c.res_id = r.res_id
	LEFT JOIN res_vote rv ON rv.res_id = c.res_id
	WHERE r.res_visible = 1 AND r.login_id IS NOT NULL AND (CASE WHEN p_l_id IS NOT NULL THEN r.login_id = p_l_id ELSE 1=1 END)
	GROUP BY r.login_id
	) AS t2 ON t2.login_id = l.l_id
	SET
		l.votes_plus = t2.votes_plus,
		l.votes_minus = t2.votes_minus,
		l.comment_count = t2.comment_count
	 WHERE (CASE WHEN p_l_id IS NOT NULL THEN l.l_id = p_l_id ELSE 1=1 END)
	;
END $$

DELIMITER ;
