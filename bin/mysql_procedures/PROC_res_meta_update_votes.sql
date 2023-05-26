DELIMITER $$

DROP PROCEDURE IF EXISTS res_meta_update_votes $$
CREATE PROCEDURE res_meta_update_votes (p_res_id INTEGER UNSIGNED)
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
	WHERE p_res_id IS NULL OR r.res_id = p_res_id
	GROUP BY r.res_id
	ON DUPLICATE KEY UPDATE
		res_votes=VALUES(res_votes),
		res_votes_plus_count=VALUES(res_votes_plus_count),
		res_votes_minus_count=VALUES(res_votes_minus_count)
	;
END $$

DELIMITER ;
