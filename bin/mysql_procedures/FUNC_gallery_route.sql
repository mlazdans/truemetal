DELIMITER $$

DROP FUNCTION IF EXISTS gallery_route $$
CREATE FUNCTION gallery_route(
	gal_id INTEGER UNSIGNED
) RETURNS VARCHAR(255)
DETERMINISTIC
BEGIN
	IF gal_id IS NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'NULL is not allowed';
	END IF;

	-- return "/gallery/$res->gal_id";
	RETURN CONCAT("/gallery/", gal_id);
END $$

DELIMITER ;
