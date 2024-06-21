DELIMITER $$

DROP TRIGGER IF EXISTS article_trigger_AI $$
CREATE TRIGGER article_trigger_AI AFTER INSERT ON article
FOR EACH ROW BEGIN
	CALL res_meta_update_route(NEW.res_id);
END; $$

DELIMITER ;
