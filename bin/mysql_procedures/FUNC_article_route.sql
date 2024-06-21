DELIMITER $$

DROP FUNCTION IF EXISTS article_route $$
CREATE FUNCTION article_route(
	module_id VARCHAR(64),
	art_id INTEGER UNSIGNED,
	res_name TINYTEXT
) RETURNS VARCHAR(255)
DETERMINISTIC
BEGIN
	IF CONCAT(module_id, art_id, res_name) IS NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'NULL is not allowed';
	END IF;

	-- return "/$res->module_id/$res->art_id-".urlize($res->res_name);
	RETURN CONCAT("/", module_id, "/", art_id, "-", urlize(res_name));
END $$

DELIMITER ;
