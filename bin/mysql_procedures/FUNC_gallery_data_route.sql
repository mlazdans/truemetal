DELIMITER $$

DROP FUNCTION IF EXISTS gallery_data_route $$
CREATE FUNCTION gallery_data_route(
	gd_id INTEGER UNSIGNED
) RETURNS VARCHAR(255)
DETERMINISTIC
BEGIN
	IF gd_id IS NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'NULL is not allowed';
	END IF;

	-- return "/gallery/view/$res->gd_id";
	RETURN CONCAT("/gallery/view/", gd_id);
END $$

DELIMITER ;
