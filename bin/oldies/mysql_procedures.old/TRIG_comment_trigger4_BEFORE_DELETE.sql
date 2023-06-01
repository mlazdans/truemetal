-- Workarounds, ka MySQL neaktivizē trigerus pie CASCADING DELETE!!!
DELIMITER $$

DROP TRIGGER IF EXISTS `comment_trigger4` $$
CREATE TRIGGER comment_trigger4 BEFORE DELETE ON `comment`
FOR EACH ROW BEGIN
	DECLARE v_parent_res_id INT DEFAULT NULL;
	SELECT res_id INTO v_parent_res_id FROM res_comment WHERE c_id = OLD.c_id;
	DELETE FROM res_comment WHERE c_id = OLD.c_id;
END; $$

DELIMITER ;

