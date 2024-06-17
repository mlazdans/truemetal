ALTER TABLE `res`
	ADD `res_hash` VARCHAR(10) CHARACTER SET ascii COLLATE ascii_bin NOT NULL AFTER `res_id`;

SELECT CONCAT_WS('-', cast(res_id as varchar(30)), COALESCE(res_email, '')) FROM `res`;

UPDATE `res` SET res_hash = SHA2(CONCAT_WS('-', res_id, COALESCE(res_data)), 224);
ALTER TABLE `res` ADD UNIQUE(`res_hash`);
