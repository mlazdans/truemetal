DELIMITER $$

DROP PROCEDURE IF EXISTS `comment_update_votes` $$
CREATE PROCEDURE `comment_update_votes`(IN p_c_id INT)
BEGIN
	DECLARE v_vote_sum INT DEFAULT 0;


	SELECT
		SUM(cv_value)
	INTO v_vote_sum
	FROM
		comment_votes
	WHERE
		cv_c_id = p_c_id;

	UPDATE comment SET c_votes = v_vote_sum WHERE c_id = p_c_id;
END $$

DELIMITER ;

