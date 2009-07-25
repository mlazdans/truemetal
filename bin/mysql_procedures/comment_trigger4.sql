/* Pēc apdeita arī izkalkulē metadatus */
DELIMITER $$

DROP TRIGGER IF EXISTS `comment_trigger4` $$
CREATE TRIGGER comment_trigger4 AFTER UPDATE ON `comment`
FOR EACH ROW BEGIN
	DECLARE v_table VARBINARY(15) DEFAULT '';
	DECLARE v_table_id INT DEFAULT 0;

	SELECT
		cc_table,
		cc_table_id
	INTO v_table, v_table_id
	FROM
		comment_connect
	WHERE
		cc_c_id = NEW.c_id;

	CALL comment_update_meta(v_table, v_table_id);
END; $$

DELIMITER ;

