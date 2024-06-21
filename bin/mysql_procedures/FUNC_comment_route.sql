DELIMITER $$

DROP FUNCTION IF EXISTS comment_route $$
CREATE FUNCTION comment_route(
	parent_res_route VARCHAR(255),
	c_id INTEGER UNSIGNED
) RETURNS VARCHAR(255)
DETERMINISTIC
BEGIN
	IF CONCAT(parent_res_route, c_id) IS NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'NULL is not allowed';
	END IF;

	-- return $res->parent_res_route.'#comment'.$res->c_id;
	RETURN CONCAT(parent_res_route, "#comment", c_id);
END $$

DELIMITER ;
