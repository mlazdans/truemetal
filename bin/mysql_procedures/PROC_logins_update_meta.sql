DELIMITER $$

DROP PROCEDURE IF EXISTS `logins_update_meta` $$
CREATE PROCEDURE `logins_update_meta` (p_l_id INT)
BEGIN
	-- TODO: te skaita tikai komentu plus/minus, bet vajag visu res
	--       comment_count skaitīt tikai komentus
	UPDATE logins l
	LEFT JOIN (
		SELECT
			r.login_id AS login_id,
			SUM(CASE WHEN rv.rv_value = +1 THEN 1 ELSE 0 END) AS votes_plus,
			SUM(CASE WHEN rv.rv_value = -1 THEN 1 ELSE 0 END) AS votes_minus,
			COUNT(DISTINCT c.c_id) AS comment_count
		FROM res r
		JOIN comment c ON c.res_id = r.res_id
		LEFT JOIN res_vote rv ON rv.res_id = c.res_id
		WHERE r.res_visible = 1 AND r.login_id IS NOT NULL AND r.login_id = COALESCE(p_l_id, r.login_id)
		GROUP BY r.login_id
	) AS t2 ON t2.login_id = l.l_id
	SET
		l.votes_plus = t2.votes_plus,
		l.votes_minus = t2.votes_minus,
		l.comment_count = t2.comment_count
	WHERE l.l_id = COALESCE(p_l_id, l.l_id)
	;
END $$

DELIMITER ;
