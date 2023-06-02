DELIMITER $$

DROP FUNCTION IF EXISTS urlize $$
CREATE FUNCTION urlize(path_segment VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci )
RETURNS VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
DETERMINISTIC
BEGIN
	SET path_segment = LOWER(path_segment);
	SET path_segment = REGEXP_REPLACE(path_segment, '[^[:alpha:][:digit:]!_-]', ' ');
	SET path_segment = TRIM(path_segment);
	SET path_segment = REGEXP_REPLACE(path_segment, '[[:space:]]+', '-');
	SET path_segment = REGEXP_REPLACE(path_segment, '[-]+', '-');
	RETURN path_segment;
END $$

DELIMITER ;
