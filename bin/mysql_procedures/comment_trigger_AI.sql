DELIMITER $$

DROP TRIGGER IF EXISTS comment_trigger_AI $$
CREATE TRIGGER comment_trigger_AI AFTER INSERT ON comment
FOR EACH ROW BEGIN
	CALL res_meta_update_route(NEW.res_id);
END; $$

DELIMITER ;
