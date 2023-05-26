DELIMITER $$

DROP PROCEDURE IF EXISTS res_meta_update $$
CREATE PROCEDURE res_meta_update (p_res_id INTEGER UNSIGNED)
BEGIN
	CALL res_meta_update_votes(p_res_id);
	CALL res_meta_update_childs(p_res_id);
END $$

DELIMITER ;
