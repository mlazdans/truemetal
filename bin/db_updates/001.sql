ALTER TABLE `res`
	ADD `res_hash` VARCHAR(16) CHARACTER SET ascii COLLATE ascii_bin NOT NULL AFTER `res_id`;

UPDATE `res` SET res_hash = SHA2(CONCAT_WS('-', res_id, COALESCE(res_data)), 512);

ALTER TABLE `res` ADD UNIQUE(`res_hash`);
-- ALTER TABLE res DROP CONSTRAINT CHK_res_hash_len;
ALTER TABLE res ADD CONSTRAINT CHK_res_hash_len CHECK (LENGTH(res_hash) = 16);
