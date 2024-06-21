DELIMITER $$

DROP FUNCTION IF EXISTS forum_route $$
CREATE FUNCTION forum_route(
	forum_id INTEGER UNSIGNED,
	res_name TINYTEXT
) RETURNS VARCHAR(255)
DETERMINISTIC
BEGIN
	IF CONCAT(forum_id, res_name) IS NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'NULL is not allowed';
	END IF;

	-- return "/forum/$res->forum_id-".urlize($res->res_name);
	RETURN CONCAT("/forum/", forum_id, "-", urlize(res_name));
END $$

DELIMITER ;
