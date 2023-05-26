DELIMITER $$

DROP PROCEDURE IF EXISTS res_meta_update_childs $$
CREATE PROCEDURE res_meta_update_childs (p_res_id INTEGER UNSIGNED)
BEGIN
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
	WHERE p_res_id IS NULL OR r.res_id = p_res_id
	GROUP BY r.res_id
	ON DUPLICATE KEY UPDATE
		res_child_count=VALUES(res_child_count),
		res_comment_count=VALUES(res_comment_count),
		res_child_last_date=VALUES(res_child_last_date),
		res_comment_last_date=VALUES(res_child_last_date)
	;
END $$

DELIMITER ;
