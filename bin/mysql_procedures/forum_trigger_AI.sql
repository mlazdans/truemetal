DELIMITER $$

DROP TRIGGER IF EXISTS forum_trigger_AI $$
CREATE TRIGGER forum_trigger_AI AFTER INSERT ON forum
FOR EACH ROW BEGIN
	CALL res_meta_update_route(NEW.res_id);
END; $$

DELIMITER ;
