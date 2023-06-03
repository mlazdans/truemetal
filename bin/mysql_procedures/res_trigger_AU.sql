DELIMITER $$

DROP TRIGGER IF EXISTS res_trigger_AU $$
CREATE TRIGGER res_trigger_AU AFTER UPDATE ON res
FOR EACH ROW BEGIN
-- 1) NEW.res_resid = null && OLD.res_resid = null => nekas nav jādara
-- 2) NEW.res_resid =    1 && OLD.res_resid = 1    => čeko visible, res_kind
-- 3) NEW.res_resid = null && OLD.res_resid = 1    => update OLD.res_resid
-- 4) NEW.res_resid =    1 && OLD.res_resid = null => update NEW.res_resid
-- 5) NEW.res_resid =    1 && OLD.res_resid = 2    => update OLD.res_resid && update NEW.res_resid

	-- 1
	IF COALESCE(NEW.res_resid, OLD.res_resid) IS NOT NULL THEN -- at least one is not null, not both
		-- 2
		IF NEW.res_resid <=> OLD.res_resid THEN -- NULL-safe equal to operator
			IF NOT (NEW.res_visible <=> OLD.res_visible AND NEW.res_kind <=> OLD.res_kind) THEN
				CALL res_meta_update_childs(NEW.res_resid);
			END IF;
		-- 3
		ELSEIF NEW.res_resid IS NULL THEN
			CALL res_meta_update_childs(OLD.res_resid);
		-- 4
		ELSEIF OLD.res_resid IS NULL THEN
			CALL res_meta_update_childs(NEW.res_resid);
		-- 5
		ELSE
			CALL res_meta_update_childs(NEW.res_resid);
			CALL res_meta_update_childs(OLD.res_resid);
		END IF;
	END IF;
END; $$

DELIMITER ;
