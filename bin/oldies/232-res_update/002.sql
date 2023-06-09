ALTER TABLE `sessions` MODIFY `sess_id` VARCHAR(32) CHARACTER SET ascii COLLATE ascii_bin;
ALTER TABLE `logins`
	ADD `l_sess_id` VARCHAR(32) CHARACTER SET ascii COLLATE ascii_bin AFTER l_emailvisible,
	ADD `l_sess_ip` VARCHAR(32) CHARACTER SET ascii COLLATE ascii_bin AFTER l_sess_id
;
ALTER TABLE `logins` ADD UNIQUE(`l_sess_id`);
