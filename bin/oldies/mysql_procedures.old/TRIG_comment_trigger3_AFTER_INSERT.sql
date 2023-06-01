DELIMITER $$

DROP TRIGGER IF EXISTS `comment_trigger3` $$
CREATE TRIGGER comment_trigger3 AFTER INSERT ON `comment`
FOR EACH ROW BEGIN
	DECLARE v_parent_res_id INT DEFAULT NULL;
	SELECT res_id INTO v_parent_res_id FROM res_comment WHERE c_id = NEW.c_id;
	CALL res_update_meta(v_parent_res_id);
END; $$

DELIMITER ;

