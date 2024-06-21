DELIMITER $$
DROP PROCEDURE IF EXISTS res_meta_update_route $$
CREATE PROCEDURE res_meta_update_route (p_res_id INTEGER UNSIGNED)
BEGIN
	DECLARE done INT DEFAULT FALSE;
	DECLARE child_res_id INTEGER UNSIGNED;
	DECLARE childs CURSOR FOR SELECT res_id FROM res WHERE res_resid = p_res_id;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

	SET max_sp_recursion_depth=10;

	INSERT INTO res_meta (
		res_id, res_route
	) VALUES (
		p_res_id, res_route(p_res_id)
	) ON DUPLICATE KEY UPDATE res_route=VALUES(res_route);

	-- update childs
	OPEN childs;
		read_loop: LOOP
			FETCH childs INTO child_res_id;
			IF done THEN
				LEAVE read_loop;
			END IF;
			CALL res_meta_update_route(child_res_id);
		END LOOP;
	CLOSE childs;
END $$

DELIMITER ;
