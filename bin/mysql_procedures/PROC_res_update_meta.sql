DELIMITER $$

DROP PROCEDURE IF EXISTS res_update_meta $$
CREATE PROCEDURE res_update_meta (p_res_id INT)
BEGIN
	-- Votes skaita arī no banotiem loginiem
	INSERT INTO res_meta (
		res_id, res_votes, res_votes_plus_count, res_votes_minus_count
	) SELECT
		r.res_id,
		SUM(rv.rv_value) AS votes,
		SUM(CASE WHEN rv.rv_value = +1 THEN 1 ELSE 0 END) AS votes_plus,
		SUM(CASE WHEN rv.rv_value = -1 THEN 1 ELSE 0 END) AS votes_minus
	FROM res r
	LEFT JOIN res_vote rv ON rv.res_id = r.res_id
	WHERE r.res_id = COALESCE(p_res_id, r.res_id)
	GROUP BY r.res_id
	ON DUPLICATE KEY UPDATE
		res_votes=VALUES(res_votes),
		res_votes_plus_count=VALUES(res_votes_plus_count),
		res_votes_minus_count=VALUES(res_votes_minus_count)
	;

	-- Saskaita cik childi, tikai redzamie
	INSERT INTO res_meta (
		res_id, res_child_count, res_comment_count, res_child_last_date, res_comment_last_date
	) SELECT
		r.res_id,
		COUNT(parent.res_id) AS child_count,
		COUNT(CASE WHEN parent.table_id = 3 THEN parent.res_id ELSE NULL END) AS comment_count,
		MAX(parent.res_entered) AS child_last_date,
		MAX(CASE WHEN parent.table_id = 3 THEN parent.res_entered ELSE NULL END) AS comment_last_date
	FROM res r
	LEFT JOIN res parent ON parent.res_resid = r.res_id AND parent.res_visible = 1
	WHERE r.res_id = COALESCE(p_res_id, r.res_id)
	GROUP BY r.res_id
	ON DUPLICATE KEY UPDATE
		res_child_count=VALUES(res_child_count),
		res_comment_count=VALUES(res_comment_count),
		res_child_last_date=VALUES(res_child_last_date),
		res_comment_last_date=VALUES(res_child_last_date)
	;
END $$

DELIMITER ;
