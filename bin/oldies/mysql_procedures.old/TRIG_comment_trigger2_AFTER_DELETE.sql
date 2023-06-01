DELIMITER $$

DROP TRIGGER IF EXISTS `comment_trigger2` $$
CREATE TRIGGER comment_trigger2 AFTER DELETE ON `comment`
FOR EACH ROW BEGIN
	DECLARE v_parent_res_id INT DEFAULT NULL;
	SELECT res_id INTO v_parent_res_id FROM res_comment WHERE c_id = OLD.c_id;
	CALL res_update_meta(v_parent_res_id);
END; $$

DELIMITER ;

