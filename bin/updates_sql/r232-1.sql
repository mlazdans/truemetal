-- CREATE TABLE `tables` (
--  `table_id` int unsigned NOT NULL,
--  `table_name` VARCHAR(16),
--  CONSTRAINT `table_pk` PRIMARY KEY (`table_id`),
--  CONSTRAINT `table_u1` UNIQUE KEY(`table_name`)
-- );
-- INSERT INTO `tables` VALUES (1, 'comment');
-- INSERT INTO `tables` VALUES (2, 'article');
-- INSERT INTO `tables` VALUES (3, 'gallery');

ALTER TABLE `res`
	CHANGE `table_id` `table_id` INT UNSIGNED NOT NULL,
	ADD `res_nickname` VARCHAR(16),
	ADD `res_email` VARCHAR(64),
	ADD `res_ip` VARCHAR(32),
	ADD `res_visible` TINYINT UNSIGNED NOT NULL DEFAULT 1,
	CHANGE `res_entered` `res_entered` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
	DROP `res_votes`,
	DROP `res_votes_plus_count`,
	DROP `res_votes_minus_count`,
	ADD `res_resid` INT UNSIGNED NULL AFTER `res_id`,
	ADD `res_name` TINYTEXT,
	ADD `res_intro` TEXT,
	ADD `res_data` MEDIUMTEXT,
	ADD `res_data_compiled` MEDIUMTEXT,
	-- ADD res_allow_childs TINYINT UNSIGNED NOT NULL DEFAULT 0,
	DROP `res_comment_count`,
	DROP `res_comment_lastdate`;
ALTER TABLE `res`
	ADD CONSTRAINT `res_ibfk_2` FOREIGN KEY (`res_resid`) REFERENCES `res`(`res_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
	ADD KEY `res_visible` (`res_visible`,`login_id`,`res_id`)
;

CREATE TABLE `res_meta` (
 `res_id` int(10) unsigned NOT NULL,
 `res_votes` int(11) DEFAULT NULL,
 `res_votes_plus_count` int(11) DEFAULT NULL,
 `res_votes_minus_count` int(11) DEFAULT NULL,
 `res_child_count` int(10) unsigned DEFAULT NULL,
 `res_child_last_date` datetime DEFAULT NULL,
 `res_comment_count` int(10) unsigned DEFAULT NULL,
 `res_comment_last_date` datetime DEFAULT NULL,
 CONSTRAINT `res_meta_u1` UNIQUE KEY(`res_id`),
 CONSTRAINT `res_meta_fk1` FOREIGN KEY (`res_id`) REFERENCES `res` (`res_id`) ON DELETE CASCADE ON UPDATE CASCADE
);

-- delete/deactivate triggers (optional)
DROP TRIGGER IF EXISTS `comment_trigger1`;
DROP TRIGGER IF EXISTS `comment_trigger2`;
DROP TRIGGER IF EXISTS `comment_trigger3`;
DROP TRIGGER IF EXISTS `comment_trigger4`;
DROP TRIGGER IF EXISTS `res_comment_trigger1`;
DROP TRIGGER IF EXISTS `res_comment_trigger2`;
DROP TRIGGER IF EXISTS `res_comment_trigger3`;
DROP TRIGGER IF EXISTS `res_comment_trigger4`;
DROP TRIGGER IF EXISTS `res_vote_trigger1`;
DROP TRIGGER IF EXISTS `res_vote_trigger2`;
DROP TRIGGER IF EXISTS `res_vote_trigger3`;
-- add extlibs
-- config izmaiņas
-- palaist bin/res_update.php
-- palaist bin/mysql_procedures/import_procedures.bat
-- r232-2.sql
-- r232-3.sql
-- CALL res_update_meta(NULL);
-- CALL logins_update_meta(NULL);
-- palaist bin/sess_update.php
-- izdzēst esošās sessijas, lai ielādētu jauno session_data
