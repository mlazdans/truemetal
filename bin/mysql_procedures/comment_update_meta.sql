DELIMITER $$

DROP PROCEDURE IF EXISTS `comment_update_meta` $$
CREATE PROCEDURE `comment_update_meta` (IN p_cc_table VARBINARY(15), p_cc_table_id INT)
BEGIN
	DECLARE v_comment_count INT DEFAULT 0;
	DECLARE v_comment_lastdate DATETIME DEFAULT '0000-00-00 00:00:00';

	SELECT
		COUNT(c_id),
		MAX(c_entered)
	INTO v_comment_count, v_comment_lastdate
	FROM
		comment
	JOIN comment_connect ON cc_c_id = c_id
	WHERE
		cc_table = p_cc_table AND
		cc_table_id = p_cc_table_id AND
		c_visible = "Y";

	INSERT INTO comment_meta (
		cm_table, cm_table_id, cm_comment_count, cm_comment_lastdate
	) VALUES (
		p_cc_table, p_cc_table_id, v_comment_count, v_comment_lastdate
	) ON DUPLICATE KEY UPDATE
		cm_comment_count = v_comment_count,
		cm_comment_lastdate = v_comment_lastdate;
END $$

DELIMITER ;

