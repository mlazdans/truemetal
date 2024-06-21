ALTER TABLE `res`
	ADD `res_hash` VARCHAR(16) CHARACTER SET ascii COLLATE ascii_bin NOT NULL AFTER `res_id`;

UPDATE `res` SET res_hash = SHA2(CONCAT_WS('-', res_id, COALESCE(res_data)), 512);

ALTER TABLE `res` ADD UNIQUE(`res_hash`);
ALTER TABLE `res` DROP `res_route`;
ALTER TABLE `res_meta` ADD `res_route` VARCHAR(255) NULL AFTER `res_comment_last_date`;
ALTER TABLE `res_meta` ADD UNIQUE(`res_route`);
