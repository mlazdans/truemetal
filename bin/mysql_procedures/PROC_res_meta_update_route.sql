DELIMITER $$

DROP PROCEDURE IF EXISTS res_meta_update_route $$
CREATE PROCEDURE res_meta_update_route (p_res_id INTEGER UNSIGNED)
BEGIN
	INSERT INTO res_meta (
		res_id, res_route
	) VALUES (
		p_res_id, res_route(p_res_id)
	) ON DUPLICATE KEY UPDATE res_route=VALUES(res_route);

	-- update childs
	INSERT INTO res_meta (
		res_id, res_route
	) SELECT res_id, res_route(res_id) FROM res WHERE res_resid = p_res_id
	ON DUPLICATE KEY UPDATE res_route=VALUES(res_route);
END $$

DELIMITER ;
