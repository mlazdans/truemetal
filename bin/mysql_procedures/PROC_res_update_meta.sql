DELIMITER $$

DROP PROCEDURE IF EXISTS `res_update_meta` $$
CREATE PROCEDURE `res_update_meta` (p_res_id INT)
BEGIN
	DECLARE v_vote_sum INT DEFAULT 0;
	DECLARE v_comment_count INT DEFAULT 0;
	DECLARE v_comment_lastdate DATETIME DEFAULT '0000-00-00 00:00:00';

	/* Comment meta */
	SELECT
		COUNT(c.c_id),
		MAX(c.c_entered)
	INTO v_comment_count, v_comment_lastdate
	FROM
		comment c
	JOIN res_comment rc ON rc.c_id = c.c_id
	WHERE
		rc.res_id = p_res_id AND
		c_visible = "Y"
	;

	/* Vote meta */
	SELECT SUM(rv_value) INTO v_vote_sum FROM res_vote WHERE res_id = p_res_id;

	UPDATE res SET
		res_comment_count = v_comment_count,
		res_comment_lastdate = v_comment_lastdate,
		res_votes = v_vote_sum
	WHERE
		res_id = p_res_id;
END $$

DELIMITER ;

