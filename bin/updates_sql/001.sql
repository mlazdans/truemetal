ALTER TABLE `logins`
	CHANGE `l_lastaccess` `l_lastaccess` DATETIME NULL,
	ADD `l_hash` VARCHAR(8) CHARACTER SET ascii COLLATE ascii_bin NOT NULL AFTER `l_id`;

UPDATE `logins` SET l_hash = MD5(l_email);
ALTER TABLE `logins` ADD UNIQUE(`l_hash`);
