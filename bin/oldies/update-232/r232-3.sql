-- ALTER TABLE `forum`
	-- ADD forum_themecount INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'meta',
	-- ADD forum_lastthemedate DATETIME  COMMENT 'meta';

-- ALTER TABLE `logins`
-- 	DROP INDEX `l_login`,
-- 	CHANGE `l_lastaccess` `l_lastaccess` DATETIME NULL,
-- 	ADD `l_hash` VARCHAR(8) NOT NULL AFTER `l_id`;
-- UPDATE `logins` SET l_hash = MD5(l_id);
-- ALTER TABLE `logins` ADD UNIQUE(`l_hash`);

TRUNCATE TABLE `login_accept`;
TRUNCATE TABLE `login_forgot`;

ALTER TABLE `login_accept`
	CHANGE `la_login` `la_email` VARCHAR(196) NOT NULL,
	CHANGE `la_accepted` `la_accepted` DATETIME NULL DEFAULT NULL;

ALTER TABLE `login_forgot`
	CHANGE `f_login` `f_email` VARCHAR(196) NOT NULL;
