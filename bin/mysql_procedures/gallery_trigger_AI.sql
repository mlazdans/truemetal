DELIMITER $$

DROP TRIGGER IF EXISTS gallery_trigger_AI $$
CREATE TRIGGER gallery_trigger_AI AFTER INSERT ON gallery
FOR EACH ROW BEGIN
	CALL res_meta_update_route(NEW.res_id);
END; $$

DELIMITER ;
