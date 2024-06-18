DELIMITER $$

DROP TRIGGER IF EXISTS res_trigger_BI $$
CREATE TRIGGER res_trigger_BI BEFORE INSERT ON res
FOR EACH ROW BEGIN
hash_loop: LOOP
	SET @rhash = SUBSTRING(SHA2(RAND(CURRENT_TIMESTAMP), 512), 1, 16);

	IF NOT EXISTS (SELECT * FROM res WHERE res_hash = @rhash) THEN
		SET NEW.res_hash = @rhash;
		LEAVE hash_loop;
	END IF;
END LOOP hash_loop;
END; $$

DELIMITER ;
