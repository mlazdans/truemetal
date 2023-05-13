DELIMITER $$

DROP PROCEDURE IF EXISTS `logins_update_meta` $$
CREATE PROCEDURE `logins_update_meta` (p_l_id INT)
BEGIN
	-- DECLARE v_votes_plus INT DEFAULT 0;
	-- DECLARE v_votes_minus INT DEFAULT 0;
	-- DECLARE v_comment_count INT DEFAULT 0;

	-- UPDATE logins set `votes_plus`=0,votes_minus=0,comment_count=0

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
	-- SELECT
	-- 	SUM(CASE WHEN rv.rv_value = +1 THEN 1 ELSE 0 END),
	-- 	SUM(CASE WHEN rv.rv_value = -1 THEN 1 ELSE 0 END)
	-- INTO v_votes_plus, v_votes_minus
	-- FROM logins l
	-- JOIN res r ON r.login_id=l.l_id
	-- JOIN comment c ON c.res_id = r.res_id
	-- JOIN res_vote rv ON rv.res_id = c.res_id
	-- WHERE (CASE WHEN p_l_id IS NOT NULL THEN l.l_id = p_l_id ELSE 1=1 END) AND r.res_visible = 1;

	-- SELECT COUNT(*) INTO v_votes_plus
	-- FROM comment c
	-- JOIN res_vote rv ON rv.res_id = c.res_id
	-- JOIN res r ON r.res_id = rv.res_id
	-- WHERE
	-- 	r.res_visible = 1 AND
	-- 	r.login_id = p_l_id AND
	-- 	rv.rv_value = 1
	-- ;

	-- SELECT COUNT(*) INTO v_votes_minus
	-- FROM comment c
	-- JOIN res_vote rv ON rv.res_id = c.res_id
	-- JOIN res r ON r.res_id = rv.res_id
	-- WHERE
	-- 	r.res_visible = 1 AND
	-- 	r.login_id = p_l_id AND
	-- 	rv.rv_value = -1
	-- ;

	-- SELECT COUNT(*) INTO v_comment_count
	-- FROM comment c
	-- JOIN res r ON r.res_id = c.res_id
	-- WHERE r.res_visible = 1 AND r.login_id = p_l_id;

	-- UPDATE logins SET
	-- 	votes_plus = v_votes_plus,
	-- 	votes_minus = v_votes_minus,
	-- 	comment_count = v_comment_count
	-- WHERE
	-- 	l_id = p_l_id
	-- ;

END $$

DELIMITER ;
