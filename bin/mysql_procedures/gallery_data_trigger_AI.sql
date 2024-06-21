DELIMITER $$

DROP TRIGGER IF EXISTS gallery_data_trigger_AI $$
CREATE TRIGGER gallery_data_trigger_AI AFTER INSERT ON gallery_data
FOR EACH ROW BEGIN
	CALL res_meta_update_route(NEW.res_id);
END; $$

DELIMITER ;
