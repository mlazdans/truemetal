DELIMITER $$

DROP PROCEDURE IF EXISTS `res_update_meta` $$
CREATE PROCEDURE `res_update_meta` (p_res_id INT)
BEGIN
	-- DECLARE v_vote_sum INT DEFAULT 0;
	-- DECLARE v_vote_plus_count INT DEFAULT 0;
	-- DECLARE v_vote_minus_count INT DEFAULT 0;
	-- DECLARE v_login_id INT DEFAULT NULL;
	-- DECLARE v_child_count INT DEFAULT 0;
	-- DECLARE v_child_last_date DATETIME;
	-- DECLARE v_comment_count INT DEFAULT 0;
	-- DECLARE v_comment_last_date DATETIME;

	-- IF p_res_id IS NULL THEN
	-- 	LEAVE b;
	-- END IF;
	INSERT INTO `res_meta` (res_id, res_votes, res_votes_plus_count, res_votes_minus_count, res_child_count, res_comment_count, res_child_last_date, res_comment_last_date)
	SELECT
		r.res_id,
		SUM(rv.rv_value) AS votes,
		SUM(CASE WHEN rv.rv_value = +1 THEN 1 ELSE 0 END) AS votes_plus,
		SUM(CASE WHEN rv.rv_value = -1 THEN 1 ELSE 0 END) AS votes_minus,
		COUNT(DISTINCT r2.res_id) AS child_count,
		COUNT(DISTINCT CASE WHEN r2.table_id = 3 THEN r2.res_id ELSE NULL END) AS comment_count,
		MAX(r2.res_entered) AS child_last_date,
		MAX(CASE WHEN r2.table_id = 3 THEN r2.res_entered ELSE NULL END) AS comment_last_date
	FROM res r
	LEFT JOIN res r2 ON r2.res_resid = r.res_id AND r2.res_visible = 1
	LEFT JOIN res_vote rv ON rv.res_id = r.res_id
	WHERE r.res_visible = 1 AND (CASE WHEN p_res_id IS NOT NULL THEN r.res_id = p_res_id ELSE 1=1 END)
	GROUP BY r.res_id
	ON DUPLICATE KEY UPDATE
		res_votes=VALUES(res_votes),
		res_votes_plus_count=VALUES(res_votes_plus_count),
		res_votes_minus_count=VALUES(res_votes_minus_count),
		res_child_count=VALUES(res_child_count),
		res_comment_count=VALUES(res_comment_count),
		res_child_last_date=VALUES(res_child_last_date),
		res_comment_last_date=VALUES(res_child_last_date)
	;

	-- /* Votes */
	-- SELECT SUM(rv_value) INTO v_vote_sum FROM res_vote WHERE res_id = p_res_id;
	-- SELECT COUNT(rv_value) INTO v_vote_plus_count FROM res_vote WHERE res_id = p_res_id AND rv_value = 1;
	-- SELECT COUNT(rv_value) INTO v_vote_minus_count FROM res_vote WHERE res_id = p_res_id AND rv_value = -1;

	-- /* Childs */
	-- SELECT COUNT(*), MAX(res_entered) INTO v_child_count, v_child_last_date FROM res WHERE res_resid = p_res_id AND res_visible = 1;

	-- /* Comments */
	-- SELECT COUNT(*), MAX(res_entered) INTO v_comment_count, v_comment_last_date FROM res WHERE res_resid = p_res_id AND res_visible = 1 AND table_id = 3;

	-- INSERT INTO `res_meta` (
	-- 	res_id, res_votes, res_votes_plus_count, res_votes_minus_count, res_child_count, res_child_last_date, res_comment_count, res_comment_last_date
	-- ) VALUES (
	-- 	p_res_id, v_vote_sum, v_vote_plus_count, v_vote_minus_count, v_child_count, v_child_last_date, v_comment_count, v_comment_last_date
	-- ) ON DUPLICATE KEY UPDATE
	-- 	res_votes = v_vote_sum,
	-- 	res_votes_plus_count = v_vote_plus_count,
	-- 	res_votes_minus_count = v_vote_minus_count,
	-- 	res_child_count = v_child_count,
	-- 	res_child_last_date = v_child_last_date,
	-- 	res_comment_count = v_comment_count,
	-- 	res_comment_last_date = v_comment_last_date
	-- ;

	--
	-- Ja sauc CALL res_update_meta_all(), tad labāk šo aizkomentēt un
	-- pēc tam izsaukt CALL logins_update_meta_all();
	--
	-- IF p_update_login_meta != 0 THEN
	-- 	SELECT login_id INTO v_login_id FROM res WHERE res_id = p_res_id;
	-- 	CALL logins_update_meta(v_login_id);
	-- END IF;

END $$

DELIMITER ;
