DROP TABLE `user`;

UPDATE `modules` set `module_visible`=1 WHERE module_visible='Y';
UPDATE `modules` set `module_visible`=0 WHERE module_visible='N';
UPDATE `modules` set `module_active`=1 WHERE module_active='Y';
UPDATE `modules` set `module_active`=0 WHERE module_active='N';
ALTER TABLE `modules`
	ADD CONSTRAINT `modules_u1` UNIQUE KEY(`mod_modid`, `module_id`),
	CHANGE `module_entered` `module_entered` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	CHANGE `module_visible` `module_visible` TINYINT UNSIGNED NOT NULL DEFAULT '1',
	CHANGE `module_active` `module_active` TINYINT UNSIGNED NOT NULL DEFAULT '1';

UPDATE `forum` set `forum_closed`=1 WHERE forum_closed='Y';
UPDATE `forum` set `forum_closed`=0 WHERE forum_closed='N';
-- UPDATE `forum` set `forum_allow_childs`=1 WHERE forum_allow_childs='Y';
-- UPDATE `forum` set `forum_allow_childs`=0 WHERE forum_allow_childs='N';
-- ALTER TABLE `forum`
-- 	CHANGE `forum_allow_childs` `forum_allow_childs` TINYINT UNSIGNED NOT NULL DEFAULT 0,
-- 	CHANGE `forum_closed` `forum_closed` TINYINT UNSIGNED NOT NULL DEFAULT 0
-- ;

UPDATE `logins` set `l_active`=1 WHERE l_active='Y';
UPDATE `logins` set `l_active`=0 WHERE l_active='N';
UPDATE `logins` set `l_accepted`=1 WHERE l_accepted='Y';
UPDATE `logins` set `l_accepted`=0 WHERE l_accepted='N';
UPDATE `logins` set `l_logedin`=1 WHERE l_logedin='Y';
UPDATE `logins` set `l_logedin`=0 WHERE l_logedin='N';
UPDATE `logins` set `l_emailvisible`=1 WHERE l_emailvisible='Y';
UPDATE `logins` set `l_emailvisible`=0 WHERE l_emailvisible='N' OR l_emailvisible='';
ALTER TABLE `logins`
	DROP `l_disable_avatars`,
	CHANGE `l_active` `l_active` TINYINT UNSIGNED NOT NULL DEFAULT 1,
	CHANGE `l_accepted` `l_accepted` TINYINT UNSIGNED NOT NULL DEFAULT 0,
	CHANGE `l_logedin` `l_logedin` TINYINT UNSIGNED NOT NULL DEFAULT 0,
	CHANGE `l_emailvisible` `l_emailvisible` TINYINT UNSIGNED NOT NULL DEFAULT 0,
	CHANGE `l_entered` `l_entered` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
;
UPDATE `logins` SET l_lastaccess = NULL WHERE `l_lastaccess`='0000-00-00 00:00:00';

UPDATE `login_accept` set `la_sent`=1 WHERE la_sent='Y';
UPDATE `login_accept` set `la_sent`=0 WHERE la_sent='N';
ALTER TABLE `login_accept`
	CHANGE `la_entered` `la_entered` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	CHANGE `la_sent` `la_sent` TINYINT UNSIGNED NOT NULL DEFAULT 0
;

UPDATE `login_forgot` set `f_sent`=1 WHERE f_sent='Y';
UPDATE `login_forgot` set `f_sent`=0 WHERE f_sent='N';
ALTER TABLE `login_forgot`
	CHANGE `f_entered` `f_entered` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	CHANGE `f_sent` `f_sent` TINYINT UNSIGNED NOT NULL DEFAULT 0
;

ALTER TABLE `res_vote`
	CHANGE `rv_entered` `rv_entered` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
;

ALTER TABLE `search_log`
	CHANGE `sl_entered` `sl_entered` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
;

-- ALTER TABLE `res_vote`
-- 	ADD `rv_id` INT UNSIGNED FIRST,
-- 	ADD CONSTRAINT `res_vote_u1` UNIQUE KEY(`rv_id`);
-- SET @var_name = 0;
-- UPDATE `res_vote` SET `rv_id`=(@var_name:=@var_name+1);

-- res indexi
--  KEY `login_id` (`login_id`),
--  KEY `res_ibfk_2` (`res_resid`),
--  KEY `res_visible` (`res_visible`,`login_id`,`res_id`) USING BTREE,
